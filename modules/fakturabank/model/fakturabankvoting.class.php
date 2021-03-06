<?
#should split to factory pattern with incoming and outgoing invoices.
includelogic('invoice/invoice');
includelogic("accountplan/scheme");
includelogic("oauth/oauth");

class lodo_fakturabank_fakturabankvoting {
    private $host           = '';
    private $protocol       = '';
    private $timeout        = 30;
    private $retrievestatus = '';
    private $OrgNumber      = '';
    private $account_number = '';
    public  $startexectime  = '';
    public  $stopexectime   = '';
    public  $diffexectime   = '';
    public  $error          = '';
    public  $tempBankAccount = '';
    public  $success        = false;
    private $ArrayTag       = array(
                                 'bank-transaction' => true,
                                 'relation' => true
                                );
    private $attributesOfInterest = array();

    function __construct() {
        $this->startexectime  = microtime();
    }

    function __destruct() {
        $this->stopexectime   = microtime();
        $this->diffexectime   = $this->stopexectime - $this->startexectime;
    }

	private function strip_account_number($account_str) {
		if (!$account_str) {
			return $account_str;
		}

		#trim away leading and trailing characters, e.g. from "DnBNOR16443299436BedriftskontoPartnerBasis"

		if (is_numeric($account_str)) {
			return $account_str;
		}

		// strip anything but numbers
		return preg_replace("/[^0-9]*/",
							"",
							$account_str);
	}

	function setup_connection_values() {
        global $_lib;

        $this->retrievestatus   = $_lib['setup']->get_value('fakturabank.status');

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));
	}

	function get_balance_report($account_id = null, $period = null, $country_code = null) {
        global $_lib;
        $this->tempBankAccount = $this->get_account_number($account_id);

		$this->setup_connection_values();

        $page       = "rest/balance_report.xml";
        // http://fakturabank.no/rest/balance_report.xml?identifier=&identifier_type=&start_date=&end_date=&country_code=&account_number=
        # If no country_code is set, send Norway's country code
        $country_code = ($country_code == '')?'NO':$country_code;
        $params     = "?identifier=" . $this->OrgNumber . '&identifier_type=NO:ORGNR';
        if ($account_id != null && $period != null) {
            $params     .= '&';
            $params .= "start_date=" . $this->period_to_startdate($period) . "&";
            $params .= "end_date=" . $this->period_to_enddate($period) . "&";
            $params .= "country_code=" . $country_code . "&";
            $params .= "account_number=" . $this->get_account_number($account_id) . "&";
        }
        $url    = "$this->protocol://$this->host/$page$params";

        if (isset($_SESSION['oauth_balance_report_fetched'])) {
          unset($_SESSION['oauth_balance_report_fetched']);
          $data = $_SESSION['oauth_resource'];
        }
        else {
          $_SESSION['oauth_action'] = 'get_balance_report';
          $oauth_client = new lodo_oauth();
          $_SESSION['oauth_balance_report_messages'][] = $url;
          $_SESSION['oauth_balance_report_fetched'] = true;
          $oauth_client->get_resources($url);
          $data = $_SESSION['oauth_resource'];
        }
        unset($_SESSION['oauth_resource']);

        $report_xml = $data['result'];
        if ($data['code'] != 200) {
          $_SESSION['oauth_balance_report_messages'][] = "<span style='color: red' >" . $report_xml . "</span>";
          if ($data['code'] == 403) $_SESSION['oauth_balance_report_messages'][] = "<span style='color: red' >" . "Utilstrekkelige rettigheter i fakturabank!" . "</span>";
        }

        $voting = $this->retrieve_voting($data['result']);

        $bank_statement = $voting->{"bank-statement"};

		$validated_voting = $this->validate_voting($voting);

		return array($validated_voting, $bank_statement);
	}

    private function retrieve_voting($xml_data) {
        global $_lib;

        $size = strlen($xml_data);

        if(!strstr(substr($xml_data,0,100), '<bank-transactions')) {
            $_lib['message']->add($xml_data);
        } else {
            if($size) {
                includelogic('xmldomtoobject/xmldomtoobject');
                $domtoobject = new empatix_framework_logic_xmldomtoobject(array('arrayTags' => $this->ArrayTag, 'attributesOfInterest' => $this->attributesOfInterest));
                #print "\n<hr>$xml_data\n<hr>";
                $voting    = $domtoobject->convert($xml_data);

            } else {
                $_lib['message']->add("XML Dokument tomt - pr&oslash;v igjen: $url");
            }
        }

        return $voting;
    }

    public function import_transactions($account_id, $period, $country_code) {
        if (!is_numeric($account_id)) {
            return false;
        }

        if (empty($period)) {
            return false;
        }

        if (!preg_match('/([0-9]{4})-([0-9]){2}/', $period, $matches)) {
            return false;
        }

        global $_lib;

        //# get transaction data from fakturabank
        list($voting, $bank_statement) = $this->get_balance_report($account_id, $period, $country_code);
        if (!is_array($voting) || empty($voting)) {
            return false;
        }

        // Save bank statement if exists.
        if ($bank_statement)
            $this->save_bank_statement($bank_statement, $account_id, $period);

        $this->save_transactions($voting, $period);

        // I believe import_transactions_to_accounting need this data to work.
        $this->save_voting_relation($voting);

        //# import fakturabank transaction records accountline table
        $this->import_transactions_to_accounting($account_id, $period);
    }

    public function save_bank_statement($bank_statement, $account_id, $period) {
        global $_lib;
        $query = sprintf("SELECT * FROM accountextras WHERE AccountID = %d AND Period = '%s'",
                                        $account_id, $period);

        $account_extra_row = $_lib['db']->get_row(array("query" => $query));

        $start_amount = $_lib['convert']->Amount($bank_statement->{"start-amount"});
        $end_amount = $_lib['convert']->Amount($bank_statement->{"end-amount"});

        if ($start_amount <= 0) {
            $extra_entry_in = 0;
            $extra_entry_out = abs($start_amount);
        } else {
            $extra_entry_in = abs($start_amount);
            $extra_entry_out = 0;
        }

        if ($end_amount <= 0) {
            $extra_last_in = 0;
            $extra_last_out = abs($end_amount);
        } else {
            $extra_last_in = abs($end_amount);
            $extra_last_out = 0;
        }

        $q = sprintf(" UPDATE bankvotingperiod SET AmountIn = '%s', AmountOut = '%s' WHERE AccountID = '%d' AND Period = '%s'"
            ,$extra_entry_in
            ,$extra_entry_out
            ,$account_id
            ,$period);
        $_lib['db']->db_query($q);

        if (!$account_extra_row) {
            $q = sprintf("INSERT INTO accountextras
              (`AccountID`, `Period`, `BankEntryIn`, `BankEntryOut`, `BankLastIn`, `BankLastOut`, `JournalID`)
            VALUES
              ('%d', '%s', '%s', '%s', '%s', '%s', '%d');
            ",
                 $account_id, $period, $extra_entry_in, $extra_entry_out, $extra_last_in, $extra_last_out, 0);

            $_lib['db']->db_query($q);
        } elseif ($account_extra_row->BankEntryIn == 0 && $account_extra_row->BankEntryOut == 0 && $account_extra_row->BankLastIn == 0 && $account_extra_row->BankLastOut == 0) {
            $q = sprintf("REPLACE INTO accountextras
              (`AccountExtrasID`, `AccountID`, `Period`, `BankEntryIn`, `BankEntryOut`, `BankLastIn`, `BankLastOut`, `JournalID`)
            VALUES
              ('%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d');
            ",
                 $account_extra_row->AccountExtrasID, $account_id, $period, $extra_entry_in, $extra_entry_out, $extra_last_in, $extra_last_out, $account_extra_row->JournalID);

            $_lib['db']->db_query($q);
        }
    }

    protected function period_to_startdate($period) {
        return "$period-01";
    }

    protected function period_to_enddate($period) {
        if (!preg_match('/([0-9]{4})-([0-9]{2})/', $period, $matches)) {
            return false;
        }

        $month = (int) $matches[2];
        $year = (int) $matches[1];

        if ($month == 12) {
            $month = 1;
            $year++;
        } else {
            $month++;
        }

        $day = 1; // hardcode to 1 of next month to get slack

        $monthstr = $month < 10 ? "0$month" : "$month";
        $daystr = $day < 10 ? "0$day" : "$day";

        return "$year-$monthstr-$daystr";

    }

    protected function get_account_number($account_id) {
        if ($this->account_number != "") {
            return $this->account_number;
        }
        global $_lib;

		$query = "SELECT AccountNumber FROM account WHERE AccountID='" . $account_id . "'";


		$row = $_lib['storage']->get_row(array('query' => $query));

		if (empty($row)) {
			return false;
		}

		return str_replace(" ", "", $row->AccountNumber);
    }

    private function lookup_account_by_OrgNumber($OrgNumber) {
        global $_lib;

        $query = sprintf(
            "SELECT AccountPlanID FROM accountplan WHERE OrgNumber = '%s'",
            $rel['InvoiceSupplierIdentity']
            );

        $row = $_lib['storage']->get_row(array('query' => $query));

        return $row;
    }

    /*private function decide_transactiontype($transaction) {
        global $_lib;

        if($transaction['InvoiceCustomerIdentitySchemeID'] == 'NO:ORGNR'
           && $transaction['InvoiceCustomerIdentity'] == $this->OrgNumber)
            return 'ING';
        else if($transaction['InvoiceSupplierIdentitySchemeID'] == 'NO:ORGNR'
                && $transaction['InvoiceSupplierIdentity'] == $this->OrgNumber)
            return 'UTG';

            }*/

    protected function import_transactions_to_accounting($account_id, $period) {
        $account_number = $this->get_account_number($account_id);
        if (!$account_number) {
            return false;
        }
        global $_lib;

        $period_start = $period . "-01 00:00:00";
        $period_end = substr($period, 0, 5) . (((int) substr($period, 5, 2)) + 1) . "-01 00:00:00";

        $period_start = $_lib['db']->db_escape($period_start);
        $period_end = $_lib['db']->db_escape($period_end);

        $query = "SELECT * FROM fakturabanktransaction WHERE
                        PostingDate >= '$period_start' AND
                        PostingDate < '$period_end' AND
                        (FromBankAccount = '$account_number' OR ToBankAccount = '$account_number')
                        ORDER BY PostingDate ASC";

        $rows = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'ID'));

        if (!is_array($rows) || empty($rows)) {
            $_lib['message']->add("Ingen transaksjoner ble funnet i perioden.");
        }


        $q_extras = sprintf("SELECT * FROM accountextras WHERE AccountID = %d AND Period = '%s'",
                           $account_id, $period);
        $r_extras = $_lib['db']->db_query($q_extras);

        if($_lib['db']->db_numrows($r_extras)) {
            $extras = $_lib['db']->db_fetch_assoc($r_extras);
            $extras_JournalID = $extras['JournalID'];
        }
        else {
            $extras_JournalID = 0;
        }

        $transactionsimported = 0;
        $duplicatetransaction = 0;
        $Priority = 0;

        $linesA = array();

        foreach ($rows as $fb_transaction) {
            // Do not import split transactions (split by cremul data showing which transactions
            // the transaction is an accumulation of), in the future we might want to use them,
            // for which they will be available in fakturabanktransaction table.
            if ($fb_transaction['IsSplit']) {
                continue;
            }

            $lineH = array();
            # Fiks date formats to iso standard
            $lineH['AccountID'] 		= $account_id;
            $lineH['FakturabankTransactionLodoID'] = $fb_transaction['ID'];
            $lineH['Period'] 			= $period;
            $lineH['Active'] 			= 1;
            $lineH['InterestDate'] 		= $fb_transaction['PostingDate'];
            $lineH['Day'] 				= substr($fb_transaction['PostingDate'], 8, 2);

            #Kunne hatt delvis automatisk reskontro match basert paa beskrivelse.
            $lineH['Description'] 		= $_lib['db']->db_escape($fb_transaction['Description']);
            $lineH['ArchiveRef'] 		= $_lib['db']->db_escape($fb_transaction['Ref']);
            $lineH['KID'] 		        = $_lib['db']->db_escape($fb_transaction['KID']);


            if($extras_JournalID != 0) {
                $lineH['JournalID'] = $extras_JournalID++;
            }


            //
            // It is most likely an error that Invoiceno is used here. The field in fakturabanktransaction is called InvoiceNumber.
            // That field might also be wrong since I have never observed it having a value.
            // The wanted data should reside inside fakturabanktransactionrelation.
            // - maw
            //
            // $lineH['InvoiceNumber'] 		= $_lib['db']->db_escape($fb_transaction['Invoiceno']);
            //$lineH['InvoiceNumber'] = $_lib['db']->db_escape( $this->get_fakturabanktransactionrelation( $fb_transaction['ID'] ) );

            $lineH['InvoiceNumber'] = $_lib['db']->db_escape($fb_transaction['InvoiceNumber']);

            //echo $fb_transaction['FakturabankID']."<br />";
            $transaction_relations = $this->get_faturabanktransactionrelations( $fb_transaction['FakturabankID'] );
            if(count($transaction_relations) > 1 || $lineH['InvoiceNumber'] == '') {
                if(count($transaction_relations) == 1) {
                    $lineH['InvoiceNumber'] = $transaction_relations[0]['InvoiceNumber'];
                    $lineH['KID'] = $transaction_relations[0]['KID'];
                    //print_r($transaction_relations);

                    if($lineH['InvoiceNumber'] == '') {
                        $lineH['InvoiceNumber'] = "FB(" . $fb_transaction['FakturabankID'] . ")";
                        $lineH['KID'] = "";
                    }
                }
                else if(count($transaction_relations)) {
                    $lineH['InvoiceNumber'] = "FB(" . $fb_transaction['FakturabankID'] . ")";
                    $lineH['KID'] = "";
                }
            }

            /* remove trailing -F from invoices starting with L */
            if($lineH['InvoiceNumber'][0] == 'L') {
                $lineH['InvoiceNumber'] = preg_replace("/(L\d+)(-F)?-\d+/", "\\1\\2",$lineH['InvoiceNumber']);
            }

            $lineH['Comment'] = '';

            //
            // Do some quick and dirty Scheme ID lookup
            //
            foreach($transaction_relations as $rel) {
                $relation_error = "";
                $relation_found = false;

                if($rel['PaycheckNo'] != '') {
                    $lineH['Comment'] .= 'Lon ';

                    $accountplan_row = $this->find_account_plan_type(
                        $rel['InvoiceSupplierIdentity'], $rel['InvoiceSupplierIdentitySchemeID'], 'employee'
                        );

                    if(!$accountplan_row) {
                        $relation_error = sprintf("Missing supplier with %s = %s",
                                                  $rel['InvoiceSupplierIdentitySchemeID'],
                                                  $rel['InvoiceSupplierIdentity']);
                    }
                    else {
                        $relation_found = true;
                        $lineH['ReskontroAccountPlanID'] = $accountplan_row->AccountPlanID;
                    }
                }
                else if($rel['InvoiceType'] == 'incoming') {
                    $lineH['Comment'] .= 'Ing ';

                    $accountplan_row = $this->find_account_plan_type($rel['InvoiceSupplierIdentity'], $rel['InvoiceSupplierIdentitySchemeID'], 'supplier');

                    if(!$accountplan_row) {
                        $relation_error = sprintf("Missing supplier with %s = %s",
                                                  $rel['InvoiceSupplierIdentitySchemeID'],
                                                  $rel['InvoiceSupplierIdentity']);
                    }
                    else {
                        $lineH['ReskontroAccountPlanID'] = $accountplan_row->AccountPlanID;
                        $relation_found = true;
                    }
                }
                else if($rel['InvoiceType'] == 'outgoing') {
                    $lineH['Comment'] .= 'Utg ';

                    $accountplan_row = $this->find_account_plan_type($rel['InvoiceCustomerIdentity'], $rel['InvoiceCustomerIdentitySchemeID'], 'customer');

                    if(!$accountplan_row) {
                        $relation_error = sprintf("Missing customer with %s = %s",
                                                  $rel['InvoiceCustomerIdentitySchemeID'],
                                                  $rel['InvoiceCustomerIdentity']);
                    }
                    else {
                        $lineH['ReskontroAccountPlanID'] = $accountplan_row->AccountPlanID;
                        $relation_found = true;
                    }
                }
                else if($rel['AccountID'] != 0) {
                    $query = sprintf(
                        "SELECT r.AccountPlanID, r.LedgerType
                           FROM fakturabankbankreconciliationreason r,
                                accountplan a
                           WHERE r.FakturabankBankReconciliationReasonID = %d and r.AccountPlanID = a.AccountPlanID",
                        $rel['AccountID']);

                    $reconciliation = $_lib['storage']->get_row(array('query' => $query));

                    //echo "<br>Rec:<br>";
                    //print_r($reconciliation);
                    //echo "<br><br>";

                    if(!$reconciliation) {
                        $relation_error = sprintf("Missing reason id %d\n", $rel['AccountID']);
                    }
                    else {
                        if($reconciliation->LedgerType == "main") {
                            $lineH['ResultAccountPlanID'] = $reconciliation->AccountPlanID;
                            $lineH['Comment'] .= 'Hov(Result) ';
                        }
                        else {
                            $lineH['ReskontroAccountPlanID'] = $reconciliation->AccountPlanID;
                            $lineH['Comment'] .= 'Hov(Reskontro) ';
                        }

                        $relation_found = true;
                    }
                }

                if(!$relation_found) {
                    $msg = sprintf('<br />Error "%s": %s<br />', $fb_transaction['Description'], $relation_error);
                    $_lib['message']->add(array('message' => $msg));
                }
            }

            if($fb_transaction['Incoming']) {
                $lineH['AmountIn'] = $fb_transaction['Amount'];
            } else {
                $lineH['AmountOut'] = $fb_transaction['Amount'];
            }

            $lineH['AmountOut'] = $lineH['AmountOut'] * -1;
            $lineH['Currency'] = $fb_transaction['Currency'];
            #Only add lines with an amount
            if($lineH['AmountIn'] > 0 || $lineH['AmountOut'] > 0) {
                # Don't insert transaction if already in DB
                $sql_exists  = "select * from accountline where FakturabankTransactionLodoID='" . $fb_transaction['ID'] . "'";
                #print "$sql_days<br>\n";

                $exist   	     = $_lib['storage']->get_row(array('query' => $sql_exists));
                if($exist) {
                    $duplicatetransaction++;
                } else {
                    $linesA[] = $lineH;
                }
            }
            $transactionsimported++;
        }

        foreach($linesA as $lineH) {
            $Priority++;

            $lineH['Priority'] 		 = $Priority;
            #print_r($lineH);

            $postvl['AccountLineID'] = $_lib['storage']->store_record(array('table' => 'accountline', 'data' => $lineH));
            //#Do we really need voucheraccountline - or could we throw it away????
            $_lib['db']->store_record(array('data' => $postvl, 'table' => 'voucheraccountline'));
        }

        $_SESSION['oauth_balance_report_messages'][] = "Transaksjoner importert: $transactionsimported, duplikat-transaksjoner: $duplicatetransaction<br>";
    }

    public function lookup_invoice($FakturabankInvoiceID) {
		global $_lib;

        $query = "SELECT * FROM fakturabankinvoicein WHERE `FakturabankID` = '" . $FakturabankInvoiceID . "'";

        $ret = $_lib['storage']->get_row(array('query' => $query));
        if (!empty($ret)) {
            return $ret;
        }

        $query = "SELECT * FROM fakturabankinvoiceout WHERE `FakturabankID` = '" . $FakturabankInvoiceID . "'";
        $ret = $_lib['storage']->get_row(array('query' => $query));
		return $ret;
    }

    /*
     * Lookup accountplan of a given type
     *
     * @param identity - value. i.e. an actual organization number
     * @param scheme_id - value type. i.e. NO:ORGNR for norwegian organization number
     * @param type - account type such as 'supplier' or 'customer'
     */
    public function find_account_plan_type($identity, $scheme_id, $type) {
        global $_lib;

        $schemeLookup = lodo_accountplan_scheme::findAccountPlanType($scheme_id, $identity, $type);
        if($schemeLookup !== null) {
          $obj = new stdClass();
          $obj->AccountPlanID = $schemeLookup;
          return $obj;
        }

        /*
         * This is legacy code from when each field was static.
         */
        if ($scheme_id == 'NO:ORGNR') {
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE OrgNumber = '$identity' AND AccountPlanType = '$type'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }
        else if(preg_match("/:EMAIL$/", $scheme_id)) {
            $query = "SELECT a.AccountPlanID FROM fakturabankemail e, accountplan a WHERE e.Email = '$identity' AND a.AccountPlanID = e.AccountPlanID AND a.AccountPlanType = '$type'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;

            /* fall back to normal email */
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE Email = '$identity' AND AccountPlanType = '$type'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }
        else if($scheme_id == 'IBAN') {
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE IBAN = '$identity' AND AccountPlanType = '$type'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }
        else if($scheme_id == "GLN") {
            $query = "SELECT a.AccountPlanID, a.OrgNumber, g.GLN FROM accountplan a, accountplangln g WHERE g.GLN = '$identity' AND a.AccountPlanType = '$type' AND a.AccountPlanID = g.AccountPlanID";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }

        $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE AccountPlanID = '$identity'";
        $accountplan = $_lib['storage']->get_row(array('query' => $query));
        if (!empty($accountplan)) {
            return $accountplan;
        }

        return false;
    }

    /*
     * Lookup accountplan
     *
     * @param identity - value. i.e. an actual organization number
     * @param scheme_id - value type. i.e. NO:ORGNR for norwegian organization number
     */
    public function find_account_plan($identity, $scheme_id) {
        global $_lib;

        $schemeLookup = lodo_accountplan_scheme::findAccountPlan($scheme_id, $identity);
        if($schemeLookup !== null) {
            return array('AccountPlanID' => $schemeLookup);
        }

        $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE AccountPlanID = '$identity'";
        $accountplan = $_lib['storage']->get_row(array('query' => $query));
        if (!empty($accountplan)) {
            return $accountplan;
        }

        /* no account found on identity value, try lookup on orgnumber if it exists */

        if ($scheme_id == 'NO:ORGNR') {
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE OrgNumber = '$identity'";
            return $_lib['storage']->get_row(array('query' => $query));
        }
        else if(preg_match("/:EMAIL$/", $scheme_id)) {
            $query = "SELECT AccountPlanID FROM fakturabankemail WHERE Email = '$identity'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;

            /* fall back to normal email */
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE Email = '$identity'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }
        else if($scheme_id == 'IBAN') {
            $query = "SELECT AccountPlanID, OrgNumber FROM accountplan WHERE IBAN = '$identity'";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }
        else if($scheme_id == "GLN") {
            $query = "SELECT a.AccountPlanID, a.OrgNumber, g.GLN FROM accountplan a, accountplangln g WHERE g.GLN = '$identity' AND a.AccountPlanID = g.AccountPlanID";
            $r = $_lib['storage']->get_row(array('query' => $query));
            if($r)
                return $r;
        }

        return false;
    }

    public function save_voting_relation(&$voting) {
        global $_lib;

        if(isset($voting))
            foreach ($voting as &$transaction) {

                if (isset($transaction) && !empty($transaction) &&
                    isset($transaction->relations) && !empty($transaction->relations)) {

                    //printf("Transaction: %d: <br />", $transaction->{'id'});

                    foreach ($transaction->relations->relation as &$relation) {
                        $dataH = array();

                        //printf("Relation: %d<br />", $relation->{'id'});

                        $relation->{'FakturabankID'} = $relation->{'id'};

                        // FakturabankID is not enough anymore.
                        // Logic is changed in Fakturabank so FakturabankID is not unique.
                        if ($LodoID = $this->get_fakturabanktransactionrelation($relation->{'FakturabankID'}, $transaction->{"id"})) {
                            $relation->{'LodoID'} = $LodoID;
                            $action = "update";
                            $dataH['ID'] = $relation->{'LodoID'};
                        } else {
                            $relation->{'LodoID'} = null;
                            $action = "insert";
                            unset($dataH['ID']);
                        }

                        $dataH['FakturabankID'] = $relation->{'id'};
                        $dataH['FakturabankTransactionID'] = $transaction->{"id"};


                        /* find and set invoice related data */

                        $dataH['FakturabankInvoiceID'] = $relation->{"invoice-id"};
                        if (!empty($relation->{"invoice-id"})) {
                            $dataH['InvoiceNumber'] = $relation->{"invoiceno"};
                            if (!empty($relation->{"kid"})) {
                                $dataH['KID'] = $relation->{"kid"};
                            }
                            $has_counterpart_id = false;

                            // assume incoming unless supplier id is our id, as we should have all our
                            // outgoing invoices with a proper supplier orgnr
                            $dataH['InvoiceType'] = 'incoming';

                            if (!empty($relation->{"invoice-supplier-identity"})) {
                                $dataH['InvoiceSupplierIdentity'] = $relation->{"invoice-supplier-identity"};
                                $dataH['InvoiceSupplierIdentitySchemeID'] = $relation->{"invoice-supplier-identity-scheme-id"};
                                if ($dataH['InvoiceSupplierIdentitySchemeID'] == 'NO:ORGNR' &&
                                    $dataH['InvoiceSupplierIdentity'] == $this->OrgNumber) {
                                    $dataH['InvoiceType'] = 'outgoing';
                                } else {
                                    $has_counterpart_id = true;
                                }
                            }
                            if (!empty($relation->{"invoice-customer-identity"})) {
                                $dataH['InvoiceCustomerIdentity'] = $relation->{"invoice-customer-identity"};
                                $dataH['InvoiceCustomerIdentitySchemeID'] = $relation->{"invoice-customer-identity-scheme-id"};
                                if ($dataH['InvoiceCustomerIdentitySchemeID'] == 'NO:ORGNR' &&
                                    $dataH['InvoiceCustomerIdentity'] == $this->OrgNumber) {
                                    $dataH['InvoiceType'] = 'incoming';
                                } else {
                                    $has_counterpart_id = true;
                                }
                            }

                            //# update relation with invoice data, to enable easy lookup of relations in bank reconciliation
                            if ($invoice = $this->lookup_invoice($dataH['FakturabankInvoiceID'])) {
                                $dataH['InvoiceID'] = $invoice->ID;
                                $dataH['AccountPlanID'] = $invoice->AccountPlanID;
                                $dataH['AccountPlanOrgNumber'] = $invoice->AccountPlanOrgNumber;
                            } else if ($has_counterpart_id) {
                                //# manually lookup accountplanid and accountplanorgnumber,
                                //# this must work both for incoming and outgoing invoices
                                //# In some cases there will not yet be an accountplan in the system
                                //# for the counterpart of the invoice
                                if ($dataH['InvoiceType'] == 'outgoing') {
                                    $accountplan = $this->find_account_plan($dataH['InvoiceCustomerIdentity'], $dataH['InvoiceCustomerIdentitySchemeID']);
                                } else {
                                    $accountplan = $this->find_account_plan($dataH['InvoiceSupplierIdentity'], $dataH['InvoiceSupplierIdentitySchemeID']);
                                }

                                if ($accountplan) {
                                    $dataH['AccountPlanID'] = $accountplan->AccountPlanID;
                                    if (!empty($accountplan->OrgNumber)) {
                                        $dataH['AccountPlanOrgNumber'] = $accountplan->OrgNumber;
                                    }
                                }
                            }
                        }


                        /* look for paycheck data */

                        if (!empty($relation->{"paycheck-no"})) {
                            $dataH['PaycheckNo'] = $relation->{"paycheck-no"};

                            if (empty($transaction->invoiceno)) {
                                //# set InvoiceNumber = PaycheckNo if PaycheckNo present and InvoiceNumber empty
                                $transaction_query = "UPDATE accountline SET InvoiceNumber = '" . $relation->{"paycheck-no"} . "' WHERE FakturabankTransactionLodoID = (SELECT ID from fakturabanktransaction WHERE FakturabankID = '" . $transaction->{"id"} . "')";
                                $_lib['storage']->db_query3(array('query' => $transaction_query));
                            }
                        }

                        $dataH['Incoming'] = $transaction->incoming;
                        $dataH['Description'] = $transaction->description;
                        $dataH['FakturabankReconciliationID'] = $relation->{"reconciliation-id"};
                        $dataH['DoneReconciliatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"done-reconciliated-at"});
                        $dataH['FromBankAccount'] = $this->strip_account_number($transaction->{"from-account"});
                        $dataH['ToBankAccount'] = $this->strip_account_number($transaction->{"to-account"});
                        $dataH['PostingDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $transaction->{"posting-date"});
                        $dataH['Ref'] = $transaction->ref;
                        if (empty($dataH['KID']) && !empty($transaction->kid)) {
                            $dataH['KID'] = $transaction->kid;
                        }
                        $dataH['Type'] = $relation->{'type'};
                        $dataH['Amount'] = $relation->{'amount'};
                        if(!empty($relation->{"invoice-currency"})) {
                            $dataH['Currency'] = $relation->{"invoice-currency"};
                        } else {
                            $dataH['Currency'] = $transaction->{"currency"};
                        }
                        $dataH['TransactionAmount'] = $transaction->amount;
                        $dataH['TransactionCurrency'] = $transaction->{"currency"};
                        $dataH['FakturabankBankTransactionAccountID'] = $relation->{'bank-transaction-account-id'};
                        $dataH['CreatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'created-at'});
                        $dataH['CreatedByID'] = $relation->{'created-by-id'};
                        $dataH['UpdatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'updated-at'});
                        $dataH['UpdatedByID'] = $relation->{'updated-by-id'};

                        if (!empty($relation->{'account'})) {
                            $dataH['AccountID'] = $relation->{'account'}->{"id"};
                            $dataH['AccountName'] = $relation->{'account'}->{"name"};
                            $dataH['AccountClose'] = $relation->{'account'}->{"close"};
                            $dataH['AccountCreatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'account'}->{"created-at"});
                            $dataH['AccountUpdatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'account'}->{"updated-at"});
                        }

                        $ret = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'fakturabanktransactionrelation', 'action' => $action, 'debug' => false));
                        if ($action == "insert") {
                            $relation->{'LodoID'} = $ret;
                        }
                    }
                }
            }
	}

	public function update_fakturabank_incoming_invoice($FakturabankInvoiceID, $InvoiceID, $AccountPlanID) {
		global $_lib;

		if (!is_numeric($FakturabankInvoiceID)) {
			return false;
		}

		if (!is_numeric($InvoiceID)) {
			return false;
		}

		if (!is_numeric($AccountPlanID)) {
			return false;
		}

		# find orgnumber
                $query = "SELECT accountplan.OrgNumber from accountplan where accountplan.AccountPlanID = '$AccountPlanID'";
		$org_hash = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'OrgNumber', 'value' => 'OrgNumber'));
		$AccountPlanOrgNumber = reset($org_hash);
		$AccountPlanOrgNumber = $AccountPlanOrgNumber['OrgNumber'];

		# Update fakturabankinvoicein, fakturabanktransactionrelation tables
		# (to enable lookup of lodo invoice given bank transaction information)

		$query = "UPDATE fakturabanktransactionrelation SET InvoiceID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankInvoiceID = '$FakturabankInvoiceID'";

		$_lib['storage']->db_query3(array('query' => $query));

		$query = "UPDATE fakturabankinvoicein SET LodoID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankID = '$FakturabankInvoiceID'";

		$_lib['storage']->db_query3(array('query' => $query));

        $query = "UPDATE fbdownloadedinvoicereasons SET LodoID = '$InvoiceID' WHERE FakturabankInvoiceInID in (SELECT ID from fakturabankinvoicein WHERE FakturabankID = '$FakturabankInvoiceID')";

        $_lib['storage']->db_query3(array('query' => $query));
	}

	public function update_fakturabank_outgoing_invoice($FakturabankInvoiceID, $InvoiceID, $AccountPlanID) {
		global $_lib;

		if (!is_numeric($FakturabankInvoiceID)) {
			return false;
		}

		if (!is_numeric($InvoiceID)) {
			return false;
		}

		if (!is_numeric($AccountPlanID)) {
			return false;
		}

		# find orgnumber
        $query = "SELECT accountplan.OrgNumber from accountplan where accountplan.AccountPlanID = '$AccountPlanID'";
		$org_hash = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'OrgNumber', 'value' => 'OrgNumber'));
		$AccountPlanOrgNumber = reset($org_hash);
		$AccountPlanOrgNumber = $AccountPlanOrgNumber['OrgNumber'];

		# Update fakturabankinvoiceout, fakturabanktransactionrelation tables
		# (to enable lookup of lodo invoice given bank transaction information)

		$query = "UPDATE fakturabanktransactionrelation SET InvoiceID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankInvoiceID = '$FakturabankInvoiceID'";

		$_lib['storage']->db_query3(array('query' => $query));

		$query = "UPDATE fakturabankinvoiceout SET LodoID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankID = '$FakturabankInvoiceID'";

		$_lib['storage']->db_query3(array('query' => $query));

        $query = "UPDATE fbdownloadedinvoicereasons SET LodoID = '$InvoiceID' WHERE FakturabankInvoiceInID in (SELECT ID from fakturabankinvoiceout WHERE FakturabankID = '$FakturabankInvoiceID')";

        $_lib['storage']->db_query3(array('query' => $query));
	}

	public function lookup_invoice_relations($args) {
            global $_lib;

            $invoice_ids = array();

            if (empty($args['id']) || !is_numeric($args['id'])) {
                return false;
            }

            $query = "SELECT * FROM accountline WHERE `AccountLineID` = '" . $args['id'] . "'";

            $accountline = $_lib['storage']->get_row(array('query' => $query));
            if (empty($accountline)) {
                return false;
            }


            // if transaction was imported from fakturabank, match on fakturabankid
            if (!empty($accountline->FakturabankTransactionLodoID) &&
                is_numeric($accountline->FakturabankTransactionLodoID)) {

                $query = "SELECT FakturabankID FROM fakturabanktransaction WHERE `ID` = '" . $accountline->FakturabankTransactionLodoID . "'";
                $result = $_lib['storage']->db_query3(array('query' => $query));
                if (!$result) {
                    return false;
                }

                if (!($obj = $_lib['storage']->db_fetch_object($result)) || !is_numeric($obj->FakturabankID)) {
                    return false;
                }
                $FakturabankTransactionID = $obj->FakturabankID;

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
				tr.FakturabankTransactionID = '$FakturabankTransactionID'";
            } else { // ... else, transaction imported from csv or punched, match on date, amount, account
                $transaction_date = $_lib['storage']->db_escape($args['VoucherDate']);
                $Incoming = $_lib['storage']->db_escape($args['Incoming']);
                $bank_account = $_lib['storage']->db_escape($args['BankAccount']);
                $amount = $_lib['storage']->db_escape($args['Amount']);

                if ($Incoming) {
                    $bank_account_stmt = "tr.FromBankAccount = '$bank_account' AND";
                } else {
                    $bank_account_stmt = "tr.ToBankAccount = '$bank_account' AND";
                }

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
				tr.PostingDate = '$transaction_date' AND
				$bank_account_stmt
				tr.Incoming = '$Incoming' AND
				ROUND(tr.TransactionAmount, 2) = ROUND('$amount', 2)";
            }

            //# there might be several relations for a transaction and only those with AccountPlanID set serves a purpose since that is the only ones we currently can handle
            $relations = $_lib['storage']->get_hashhash(array('query' => $query . " AND AccountPlanID is not NULL AND AccountPlanID != '' AND FakturabankInvoiceID IS NOT NULL", 'key' => 'FakturabankInvoiceID'));

            if (empty($relations)) {
                return false;
            }

            return $relations;
	}

	public function lookup_paycheck_relations($args) {
            global $_lib;

            $invoice_ids = array();

            if (empty($args['id']) || !is_numeric($args['id'])) {
                return false;
            }

            $query = "SELECT * FROM accountline WHERE `AccountLineID` = '" . $args['id'] . "'";

            $accountline = $_lib['storage']->get_row(array('query' => $query));
            if (empty($accountline)) {
                return false;
            }


            // if transaction was imported from fakturabank, match on fakturabankid
            if (!empty($accountline->FakturabankTransactionLodoID) &&
                is_numeric($accountline->FakturabankTransactionLodoID)) {

                $query = "SELECT FakturabankID FROM fakturabanktransaction WHERE `ID` = '" . $accountline->FakturabankTransactionLodoID . "'";
                $result = $_lib['storage']->db_query3(array('query' => $query));
                if (!$result) {
                    return false;
                }

                if (!($obj = $_lib['storage']->db_fetch_object($result)) || !is_numeric($obj->FakturabankID)) {
                    return false;
                }
                $FakturabankTransactionID = $obj->FakturabankID;

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
				tr.FakturabankTransactionID = '$FakturabankTransactionID' and PaycheckNo is not null";
            } else { // ... else, transaction imported from css or punched, match on date, amount, account
                $transaction_date = $_lib['storage']->db_escape($args['VoucherDate']);
                $Incoming = $_lib['storage']->db_escape($args['Incoming']);
                $bank_account = $_lib['storage']->db_escape($args['BankAccount']);
                $amount = $_lib['storage']->db_escape($args['Amount']);

                if ($Incoming) {
                    $bank_account_stmt = "tr.FromBankAccount = '$bank_account' AND";
                } else {
                    $bank_account_stmt = "tr.ToBankAccount = '$bank_account' AND";
            }

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
 				PaycheckNo IS NOT NULL AND
				tr.InvoiceID IS NOT NULL AND
				tr.PostingDate = '$transaction_date' AND
				$bank_account_stmt
				tr.Incoming = '$Incoming' AND
				ROUND(tr.TransactionAmount, 2) = ROUND('$amount', 2)";
            }


            //# there might be several relations for a transaction and only those with AccountPlanID set serves a purpose since that is the only ones we currently can handle
            $relations = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'PaycheckNo'));

            if (empty($relations)) {
                return false;
            }

            return $relations;
	}

        function lookup_reconciliation_reason_relations($args) {
            global $_lib;

            $invoice_ids = array();

            if (empty($args['id']) || !is_numeric($args['id'])) {
                return false;
            }

            $query = "SELECT * FROM accountline WHERE `AccountLineID` = '" . $args['id'] . "'";

            $accountline = $_lib['storage']->get_row(array('query' => $query));
            if (empty($accountline)) {
                return false;
            }

            // if transaction was imported from fakturabank, match on fakturabankid
            if (!empty($accountline->FakturabankTransactionLodoID) &&
                is_numeric($accountline->FakturabankTransactionLodoID)) {

                $query = "SELECT FakturabankID FROM fakturabanktransaction WHERE `ID` = '" . $accountline->FakturabankTransactionLodoID . "'";
                $result = $_lib['storage']->db_query3(array('query' => $query));
                if (!$result) {
                    return false;
                }

                if (!($obj = $_lib['storage']->db_fetch_object($result)) || !is_numeric($obj->FakturabankID)) {
                    return false;
                }
                $FakturabankTransactionID = $obj->FakturabankID;

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
				tr.FakturabankTransactionID = '$FakturabankTransactionID'";
            } else { // ... else, transaction imported from css or punched, match on date, amount, account
                $transaction_date = $_lib['storage']->db_escape($args['VoucherDate']);
                $Incoming = $_lib['storage']->db_escape($args['Incoming']);
                $bank_account = $_lib['storage']->db_escape($args['BankAccount']);
                $amount = $_lib['storage']->db_escape($args['Amount']);

                if ($Incoming) {
                    $bank_account_stmt = "tr.FromBankAccount = '$bank_account' AND";
                } else {
                    $bank_account_stmt = "tr.ToBankAccount = '$bank_account' AND";
                }

                $query = "SELECT * FROM fakturabanktransactionrelation tr WHERE
				tr.InvoiceID IS NOT NULL AND
				tr.PostingDate = '$transaction_date' AND
				$bank_account_stmt
				tr.Incoming = '$Incoming' AND
				ROUND(tr.TransactionAmount, 2) = ROUND('$amount', 2)";
            }

            //# there might be several relations for a transaction and only those with FakturabankBankTransactionAccountID set serves a purpose since that is the only ones we currently can handle
            $relations = $_lib['storage']->get_hashhash(array('query' => $query . " AND (InvoiceID IS NULL) AND FakturabankBankTransactionAccountID IS NOT NULL AND FakturabankBankTransactionAccountID != '0'", 'key' => 'FakturabankID'));

            if (empty($relations)) {
                return false;
            }

            return $relations;
        }

        public function get_faturabanktransactionrelations($FakturabankTransactionID) {
            global $_lib;

            if(!is_numeric($FakturabankTransactionID)) {
                return false;
            }

            $query = sprintf("SELECT * FROM fakturabanktransactionrelation WHERE FakturabankTransactionID = '%d'",
                             $FakturabankTransactionID);

            //printf("%s<br />", $query);

            $arr = array();
            $r = $_lib['storage']->db_query3(array('query' => $query));

            while( ($row = $_lib['storage']->db_fetch_assoc($r)) ) {
                $arr[] = $row;
            }

            return $arr;
        }

	public function get_fakturabanktransactionrelation($FakturabankID, $FakturabankTransactionID) {
            global $_lib;

            if (!is_numeric($FakturabankID)) {
                printf("False1<br>");
                return false;
            }

            $query = "SELECT `ID` FROM fakturabanktransactionrelation WHERE `FakturabankID` = '$FakturabankID' AND `FakturabankTransactionID` = $FakturabankTransactionID";
            $result = $_lib['storage']->db_query3(array('query' => $query));
            if (!$result) {
                return false;
            }
            if (($obj = $_lib['storage']->db_fetch_object($result)) && is_numeric($obj->ID)) {
                return $obj->ID;
            }

            return false;
	}

	public function get_fakturabanktransaction($FakturabankID) {
        global $_lib;

		if (!is_numeric($FakturabankID)) {
			return false;
		}

		$query = "SELECT `ID` FROM fakturabanktransaction WHERE `FakturabankID` = '$FakturabankID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if (($obj = $_lib['storage']->db_fetch_object($result)) && is_numeric($obj->ID)) {
			return $obj->ID;
		}

		return false;
	}

        public function get_fakturabanktransactionobject($FakturabankID) {
        global $_lib;

		if (!is_numeric($FakturabankID)) {
			return false;
		}

		$query = "SELECT * FROM fakturabanktransaction WHERE `FakturabankID` = '$FakturabankID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if (($obj = $_lib['storage']->db_fetch_object($result)) && is_numeric($obj->ID)) {
			return $obj;
		}

		return false;
	}



	private function save_transactions($voting, $period) {
		global $_lib;

        if (empty($voting)) return false;

        /**
         * To avoid having old (and potentially deleted or modified) transactions around
         * after doing import of a period, we must first delete existing transactions and relations, in this
         * period.
         *
         * In save_voting_relations(), called after this function if $period exists,
         * we will update relations with invoice data like done in
         * update_fakturabank_outgoing/ingoing_invoice().
         * If we don't do this, then the data linking a relation to an invoice (and
         * thereby to a ledger (reskontro)) could be lost, since this data is added
         * when importing invoices.
         */

        $query = "select FakturabankID from fakturabanktransaction where PostingDate LIKE '$period%' AND (FromBankAccount=" . $this->tempBankAccount . " OR ToBankAccount=" . $this->tempBankAccount . ")";
        $existing_transactions = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'FakturabankID'));
        if (!empty($existing_transactions)) {
            foreach ($existing_transactions as $ExistingFakturabankID => $ExistingFakturabankTransaction) {
                $query = "delete from fakturabanktransactionrelation where FakturabankTransactionID = '$ExistingFakturabankID'";
                $_lib['db']->db_delete($query);
            }

            $query = "delete from fakturabanktransaction where PostingDate LIKE '$period%'";
                $_lib['db']->db_delete($query);
        }

		foreach ($voting as &$transaction) {
			$dataH = array();

			$transaction->FakturabankID = $transaction->id;

			if ($LodoID = $this->get_fakturabanktransaction($transaction->FakturabankID)) {
				$transaction->LodoID = $LodoID;
				$action = "update";
				$dataH['ID'] = $transaction->LodoID;
			} else {
				$transaction->LodoID = null;
				$action = "insert";
			}

			$dataH['FakturabankID'] = $transaction->FakturabankID;

            // store incoming value for further use in save_voting()
			$transaction->incoming = (
                                      $transaction->{"transaction-type"} == "C" || // credit
                                      $transaction->{"transaction-type"} == "RD" // reverse debit
                                      ) ? 1 : 0;

			$dataH['Incoming'] = $transaction->incoming;
			$dataH['TransactionType'] = $transaction->{"transaction-type"};
			$dataH['Amount'] = $transaction->amount;
			$dataH['Currency'] = $transaction->currency;
			$dataH['Description'] = $transaction->description;
			$dataH['DoneReconciliatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"done-reconciliated-at"});
			$dataH['FromBankAccount'] = $this->strip_account_number($transaction->{"from-account"});
			$dataH['ToBankAccount'] = $this->strip_account_number($transaction->{"to-account"});
			$dataH['PostingDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $transaction->{"posting-date"});
			$dataH['Ref'] = $transaction->ref;
			$dataH['KID'] = $transaction->kid;
			$dataH['InvoiceNumber'] = $transaction->invoiceno;
			$dataH['IsSplit'] = $transaction->{"is-split"};
			$dataH['ParentID'] = $transaction->{"parent-id"};
			$dataH['CounterpartName'] = $transaction->{"counterpart-name"};

			$dataH['TransactionBatchId'] = $transaction->{"transaction-batch-id"};
			$dataH['UnitId'] = $transaction->{"unit-id"};
			$dataH['CreatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"created-at"});
			$dataH['CreatedBy'] = $transaction->{"created-by"};
			$dataH['UpdatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"updated-at"});

			$ret = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'fakturabanktransaction', 'action' => $action, 'debug' => false));
			if ($action == "insert") {
				$transaction->LodoID = $ret;
			}
		}
	}

        public function findinvoicematch($args) {
            global $_lib;

            $args['VoucherDate'] = substr($args['VoucherDate'], 0, 10);

            $query = "SELECT AccountNumber FROM account WHERE AccountID='" . $args['BankAccountID'] . "'";

            // Commented this so we don't print out id's in view. Debug leftover?
            // printf("Got id: %d\n", $args['id']);

            $row = $_lib['storage']->get_row(array('query' => $query));

            if (empty($row)) {
                return false;
            }

            $args['BankAccount'] = str_replace(" ", "", $row->AccountNumber);

            $relations = $this->lookup_invoice_relations($args);

            if (!isset($relations) || empty($relations)) {
                return false;
            }

            //#get first relation
            $relation = reset($relations);

            return $relation;
        }


    public function findpaycheckmatch($args) {
        global $_lib;

        $args['VoucherDate'] = substr($args['VoucherDate'], 0, 10);

		$query = "SELECT AccountNumber FROM account WHERE AccountID='" . $args['BankAccountID'] . "'";

		$row = $_lib['storage']->get_row(array('query' => $query));

		if (empty($row)) {
			return false;
		}

		$args['BankAccount'] = str_replace(" ", "", $row->AccountNumber);

		$relations = $this->lookup_paycheck_relations($args);
		if (!isset($relations) || empty($relations)) {
			return false;
		}

		#get first relation
		$relation = reset($relations);

        $query = "select AccountPlanID from salary where JournalID = '" . substr($relation['PaycheckNo'], 1) . "'";
        $paychecks = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'AccountPlanID'));
        if (!empty($paychecks)) {
            $paycheck_match = reset($paychecks);
        } else {
            $paycheck_match = false;
        }

		return $paycheck_match;
    }

    public function findnoninvoicematch($args) {
        global $_lib;

        $args['VoucherDate'] = substr($args['VoucherDate'], 0, 10);

		$query = "SELECT AccountNumber FROM account WHERE AccountID='" . $args['BankAccountID'] . "'";

		$row = $_lib['storage']->get_row(array('query' => $query));

		if (empty($row)) {
			return false;
		}

		$args['BankAccount'] = str_replace(" ", "", $row->AccountNumber);

		$relations = $this->lookup_reconciliation_reason_relations($args);

		if (!isset($relations) || empty($relations)) {
			return false;
		}

		#get first relation
		$relation = reset($relations);

		return $relation;
    }

    ################################################################################################
    #validate -
    private function validate_voting($voting) {
		# since transactions will not be shown we currently need no validation
		$arrayname = "bank-transaction";
        return $voting->$arrayname;
    }
}

####################################################################################################
#STATISTICS

#print "\n\nVeldig bra<br>\nExectid: $diffexectime\n\n";
#print "starttime:     $starttime\n";
#print "startexectime: $startexectime\n";
#print "startexectime: $stopexectime\n";
#print "stoptime :     $stoptime\n";
?>
