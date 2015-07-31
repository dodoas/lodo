<?
includelogic('oauth/oauth');
class lodo_fakturabank_invoicereconciliationreason {

    public function invoice_reconciliation_reason_to_accountplan($fakturabank_bank_reconciliation_reason_id) {
        global $_lib;

        $query = "select AccountPlanID from fakturabankinvoicereconciliationreason where FakturabankInvoiceReconciliationReasonID = '" . $fakturabank_bank_reconciliation_reason_id . "' AND AccountPlanID != '0' and AccountPlanID is not null";

        $rows = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'FakturabankInvoiceReconciliationReasonID'));
        if (empty($rows)) {
            return false;
        }

        $row = reset($rows);

        $accountplan_id = $row['AccountPlanID'];

        $query = "SELECT * from accountplan where accountplan.AccountPlanID = '$accountplan_id'";
		$accountplans = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'AccountPlanID'));

        if (empty($accountplans)) {
            return false;
        }

        $accountplan = reset($accountplans);
        
        return $accountplan;
    }

    public function import_mappings($_mappings = false) {
        global $_lib;

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

		    $page   = "rest/closing_reasons.json";
        $params = "?type=invoice";
        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        if (!$_mappings) $this->retrieve_reasons($page, $url);
        else $reasons = $_mappings;

        $this->update_database($reasons);
        return true;
    }
    
    private function update_database($reasons) 
    {
        global $_lib;

        $db_table = "fakturabankinvoicereconciliationreason";

        if (empty($reasons)) {
            return false;
        }

        foreach ($reasons as $reason) {
            $account = $reason['closing_reason'];
            $query   = "select * from $db_table where FakturabankInvoiceReconciliationReasonID = '" . $account['id'] . "'";
            $fakturabankinvoicereconciliationreason = $_lib['storage']->get_row(array('query' => $query));
            if (!empty($fakturabankinvoicereconciliationreason)) {                
                if (!empty($fakturabankinvoicereconciliationreason->AccountPlanID)) { // don't overwrite if accountplanid is set
                    continue;
                }

                $query = "delete from fakturabankinvoicereconciliationreason where FakturabankInvoiceReconciliationReasonID = '". $account['id'] . "'"; // delete to reinsert below
                $_lib['db']->db_delete($query);
            }

            $has_accountno = !empty($account['default_account_plan_number']);

            $query = "insert into fakturabankinvoicereconciliationreason (FakturabankInvoiceReconciliationReasonID, " . ($has_accountno ? "AccountPlanID, " : "") . "FakturabankInvoiceReconciliationReasonCode, FakturabankInvoiceReconciliationReasonName, LedgerType) values ('" .
                $account['id'] . "', '" .
                ($has_accountno ? $account['default_account_plan_number'] . "', '" : "") .
                utf8_decode($account['code']) . "', '" . 
                utf8_decode($account['name']) . "', '" . 
                utf8_decode($account['default_account_ledger_type']) . "')";

			$_lib['db']->db_insert2(array('query'=> $query));
        }

        $_lib['message']->add("Konto-koblinger fra Fakturabank ble lagt til");


        return true;
    }

    private function retrieve_reasons($page, $url) {
        global $_lib;

        $client = new lodo_oauth();
        $_SESSION['oauth_action'] = 'get_invoice_closing_reasons';

        $json_data = $client->get_resources($url);
    }
}
?>
