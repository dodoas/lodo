<?

class lodo_fakturabank_bankreconciliationreason {

    public static function translate_ledger_type($ledger_type) {
        switch ($ledger_type) {
            case "main":
                return "hoved";
            case "customer":
                return "kunde";
            case "supplier":
                return "leverand&oslash;r";
            case "salary":
            return "l&oslash;nn";
            default:
                return null;
        }
    }
    
    public function bank_reconciliation_reason_to_accountplan($fakturabank_bank_reconciliation_reason_id) {
        global $_lib;

        $query = "select AccountPlanID from fakturabankbankreconciliationreason where FakturabankBankReconciliationReasonID = '" . $fakturabank_bank_reconciliation_reason_id . "' AND AccountPlanID != '0' and AccountPlanID is not null";

        $rows = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'FakturabankBankReconciliationReasonID'));
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

    public function import_mappings() {
        global $_lib;

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        $page       = "rest/closing_reasons.json";
        $params     = "?type=bank_transactions";
        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        $reasons = $this->retrieve_reasons($page, $url);

        $this->update_database($reasons);
        return true;
    }
    
    private function update_database($reasons) 
    {
        global $_lib;

        $db_table = "fakturabankbankreconciliationreason";

        if (empty($reasons)) {
            return false;
        }

        foreach ($reasons as $reason) {
            $account = $reason["closing_reason"];
            $query   = "select * from $db_table where FakturabankBankReconciliationReasonID = '" . $account['id'] . "'";
            $fakturabankbankreconciliationreason = $_lib['storage']->get_row(array('query' => $query));
            if (!empty($fakturabankbankreconciliationreason)) {                
                if (!empty($fakturabankbankreconciliationreason->AccountPlanID)) { // don't overwrite if accountplanid is set
                    continue;
                }

                $query = "delete from fakturabankbankreconciliationreason where FakturabankBankReconciliationReasonID = '". $account['id'] . "'"; // delete to reinsert below
                $_lib['db']->db_delete($query);
            }

            $has_accountno = !empty($account['default_account_plan_number']);

            $query = "insert into fakturabankbankreconciliationreason (FakturabankBankReconciliationReasonID, " . ($has_accountno ? "AccountPlanID, " : "") . "FakturabankBankReconciliationReasonCode, FakturabankBankReconciliationReasonName, LedgerType) values ('" .
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

        $headers = array(
            "GET ".$page." HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

        $json_data           = curl_exec($ch);

        if (curl_errno($ch)) {
            $_lib['message']->add("Nettverkskobling til Fakturabank ikke OK");
            $_lib['message']->add("Error: " . curl_error($ch));
        } else if ($json_data == "Unauthorized") {
            $_lib['message']->add("Nettverkskobling til Fakturabank OK");
            $_lib['message']->add("Error: " . $json_data);
        } else {
            $_lib['message']->add("Nettverkskobling til Fakturabank OK");
        }
        curl_close($ch);
        
        $size = strlen($json_data);

        if($size) {
            
            $reasons = json_decode($json_data, true); // this functions only works with utf8
        } else {
            $_lib['message']->add("JSON Dokument tomt - pr&oslash;v igjen: $url");
        }

        return $reasons;
    }
}
?>
