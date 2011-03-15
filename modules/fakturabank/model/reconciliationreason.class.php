<?
class lodo_fakturabank_reconciliationreason {
    private $username       = '';
    private $password       = '';

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

        $this->username         = $_lib['sess']->get_person('FakturabankUsername');
        $this->password         = $_lib['sess']->get_person('FakturabankPassword');


        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        if(!$this->username || !$this->username) {
            $_lib['message']->add("Fakturabank brukernavn og passord er ikke definert p&aring; brukeren din");
        } else {
            $this->login = true;
        }

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber'))); 

        $this->credentials = "$this->username:$this->password";		


        $this->credentials = "$this->username:$this->password";		


		$page       = "bank_transaction_accounts.json";
        $params     = "?identifier=" . $this->OrgNumber . '&identifier_type=NO:ORGNR';
//        if($this->retrievestatus) $params .= '&customer_status=' . $this->retrievestatus;
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
            $account = $reason['bank_transaction_account'];
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

            $query = "insert into fakturabankbankreconciliationreason (FakturabankBankReconciliationReasonID, " . ($has_accountno ? "AccountPlanID, " : "") . "FakturabankBankReconciliationReasonCode, FakturabankBankReconciliationReasonName) values ('" .
                $account['id'] . "', '" .
                ($has_accountno ? $account['default_account_plan_number'] . "', '" : "") .
                utf8_decode($account['code']) . "', '" . 
                utf8_decode($account['name']) . "')";

			$_lib['db']->db_insert2(array('query'=> $query));
        }

        $_lib['message']->add("Konto-koblinger fra Fakturabank ble lagt til");


        return true;
    }

    private function retrieve_reasons($page, $url) {
        global $_lib;

        if(!$this->login) return false;
        
        $headers = array(
            "GET ".$page." HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"run\"",
            "Authorization: Basic " . base64_encode($this->credentials)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        #curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); #Is this safe?
        #curl_setopt($ch, CURLOPT_CAINFO, "path:/ca-bundle.crt"); 

        $json_data           = curl_exec($ch);

        if (curl_errno($ch)) {
            $_lib['message']->add("Nettverkskobling til Fakturabank ikke OK");
            $_lib['message']->add("Error: " . curl_error($ch));
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
