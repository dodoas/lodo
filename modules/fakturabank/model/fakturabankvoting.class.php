<?
#should split to factory pattern with incoming and outgoing invoices.
includelogic('invoice/invoice');

class lodo_fakturabank_fakturabankvoting {
    #private $host           = 'fakturabank.cavatina.no';
    private $host           = 'fakturabank.no';
    #private $protocol       = 'http';
    private $protocol       = 'https';
    private $username       = '';
    private $password       = '';
    private $login          = false;
    private $timeout        = 30; 
    private $retrievestatus = '';
    private $credentials    = '';
    private $OrgNumber      = '';
    public  $startexectime  = '';
    public  $stopexectime   = '';
    public  $diffexectime   = '';
    public  $error          = '';
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

        $this->username         = $_lib['sess']->get_person('FakturabankUsername');
        $this->password         = $_lib['sess']->get_person('FakturabankPassword');
        $this->retrievestatus   = $_lib['setup']->get_value('fakturabank.status');

        if(!$this->username || !$this->username) {
            $_lib['message']->add("Fakturabank brukernavn og passord er ikke definert p&aring; brukeren din");
        } else {
            $this->login = true;
        }

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber'))); 

        $this->credentials = "$this->username:$this->password";		
	}

	function get_balance_report() {
        global $_lib;

		$this->setup_connection_values();

		$page       = "balance_report.xml";
		// http://fakturabank.no/balance_report.xml?identifier=
        $params     = "?identifier=" . $this->OrgNumber . '&identifier_type=NO:ORGNR';
//        if($this->retrievestatus) $params .= '&customer_status=' . $this->retrievestatus;
        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        $voting = $this->retrieve_voting($page, $url);
		$validated_voting = $this->validate_voting($voting);
		return $validated_voting;
	}

    private function retrieve_voting($page, $url) {
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

        $xml_data           = curl_exec($ch);

        if (curl_errno($ch)) {
            $_lib['message']->add("Nettverkskobling til Fakturabank ikke OK");
            $_lib['message']->add("Error: " . curl_error($ch));
        } else {
            $_lib['message']->add("Nettverkskobling til Fakturabank OK");
        }
        curl_close($ch);
        
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

	public function import_transactions($account_id, $period) {
        if (!is_numeric($account_id)) {
            return false;
        }

        if (!preg_match('/[0-9]{4}-[0-9]{2}/', $period)) {
            return false;
        }

        global $_lib;

        # get transaction data from fakturabank
		$voting = $this->get_balance_report();
        if (!is_array($voting) || empty($voting)) {
            return false;
        }
		$this->save_transactions($voting);

        # import fakturabank transaction records accountline table
        $this->import_transactions_to_accounting($account_id, $period);

	}

    

    protected function import_transactions_to_accounting($account_id, $period) {
        global $_lib;

		$query = "SELECT AccountNumber FROM account WHERE AccountID='" . $account_id . "'";


		$row = $_lib['storage']->get_row(array('query' => $query));

		if (empty($row)) {
			return false;
		}

		$account_number = str_replace(" ", "", $row->AccountNumber);

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


        $transactionsimported = 0;
        $duplicatetransaction = 0;
        $Priority = 0;

        $linesA = array();

        foreach ($rows as $fb_transaction) {
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
            $lineH['KID'] 		= $_lib['db']->db_escape($fb_transaction['KID']);

            if($fb_transaction['Incoming']) {
                $lineH['AmountIn'] = $fb_transaction['Amount'];
				
            } else {
                $lineH['AmountOut'] = $fb_transaction['Amount'];
            }

            #Only add lines with a amount
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

            #Do we really need voucheraccountline - or could we throw it away????
            $_lib['db']->store_record(array('data' => $postvl, 'table' => 'voucheraccountline'));
		}

		$_lib['message']->add("Transactions importert: $transactionsimported, duplikattransaksjoner: $duplicatetransaction<br>");
    }

	public function save_incoming_w_voting($invoices) {
        # voting includes both bank transactions and relations to invoices
		$voting = $this->get_balance_report();
        # save bank transactions
		$this->save_transactions($voting);
        # save voting relations
		$this->save_voting_relation($voting, $invoices);
	}

	public function save_outgoing_w_voting($invoices) {
		return; 

        global $_lib;

		$voting = $this->get_balance_report();

		$this->save_transactions($voting);

		$this->save_voting_relation($voting, $invoices);
	}

	public function save_voting_relation(&$voting, $invoices) {
		global $_lib;


		foreach ($voting as &$transaction) {
			if (isset($transaction) && !empty($transaction) && 
				isset($transaction->relations) && !empty($transaction->relations)) {
				
				foreach ($transaction->relations->relation as &$relation) {
					$dataH = array();

					$relation->{'FakturabankID'} = $relation->{'id'};
					
					if ($LodoID = $this->get_fakturabanktransactionrelation($relation->{'FakturabankID'})) {
						$relation->{'LodoID'} = $LodoID;
						$action = "update";
						$dataH['ID'] = $relation->{'LodoID'};
					} else {
						$relation->{'LodoID'} = null;
						$action = "insert";
						unset($dataH['ID']);
					}

					# decipher type (e.g. InvoiceIn, InvoiceOut, TransactionCosts etc.)

					$dataH['FakturabankID'] = $relation->{'id'};
					$dataH['FakturabankTransactionID'] = $transaction->{"id"};
					$dataH['FakturabankInvoiceID'] = $relation->{"invoice-id"};
					$dataH['Incoming'] = $transaction->incoming;
					$dataH['Description'] = $transaction->description;
					$dataH['DoneReconciliatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"done-reconciliated-at"});
					$dataH['FromBankAccount'] = $this->strip_account_number($transaction->{"from-account"});
					$dataH['ToBankAccount'] = $this->strip_account_number($transaction->{"to-account"});
					$dataH['PostingDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $transaction->{"posting-date"});
					$dataH['Ref'] = $transaction->ref;
					$dataH['KID'] = $transaction->kid;
					$dataH['Type'] = $relation->{'type'};
					$dataH['Amount'] = $relation->{'amount'};
					$dataH['Currency'] = $transaction->{"currency"}; // should be relation currency
					$dataH['TransactionAmount'] = $transaction->amount;
					$dataH['TransactionCurrency'] = $transaction->{"currency"};
					$dataH['FakturabankBankTransactionAccountID'] = $relation->{'bank-transaction-account-id'};
					$dataH['CreatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'created-at'});
					$dataH['CreatedByID'] = $relation->{'created-by-id'};
					$dataH['UpdatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'updated-at'});
					$dataH['UpdatedByID'] = $relation->{'updated-by-id'};
					$dataH['AccountID'] = $relation->{'account'}->{"id"};
					$dataH['AccountName'] = $relation->{'account'}->{"name"};
					$dataH['AccountClose'] = $relation->{'account'}->{"close"};
					$dataH['AccountCreatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'account'}->{"created-at"});
					$dataH['AccountUpdatedAt'] = $_lib['date']->t_to_mysql_format($relation->{'account'}->{"updated-at"});

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

		$query = "UPDATE fakturabanktransactionrelation SET InvoiceID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankInvoiceID = '$FakturabankInvoiceID' AND Incoming = '1'";

		$_lib['storage']->db_query3(array('query' => $query));

		$query = "UPDATE fakturabankinvoicein SET LodoID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankID = '$FakturabankInvoiceID'";

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

		$query = "UPDATE fakturabanktransactionrelation SET InvoiceID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankInvoiceID = '$FakturabankInvoiceID' AND Incoming = '1'";

		$_lib['storage']->db_query3(array('query' => $query));

		$query = "UPDATE fakturabankinvoiceout SET LodoID = '$InvoiceID', AccountPlanID = '$AccountPlanID', AccountPlanOrgNumber = '$AccountPlanOrgNumber' WHERE FakturabankID = '$FakturabankInvoiceID'";

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

		$relations = $_lib['storage']->get_hashhash(array('query' => $query, 'key' => 'InvoiceID'));

		if (empty($relations)) {
			return false;
		}

		return $relations;
	}

	public function get_fakturabanktransactionrelation($FakturabankID) {
        global $_lib;

		if (!is_numeric($FakturabankID)) {
			return false;
		}

		$query = "SELECT `ID` FROM fakturabanktransactionrelation WHERE `FakturabankID` = '$FakturabankID'";
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

	private function save_transactions($voting) {
		global $_lib;

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

			$transaction->incoming = ($transaction->{"from-account"} == "") ? 1 : 0;

			$dataH['Incoming'] = $transaction->incoming;
			$dataH['Amount'] = $transaction->amount;
			$dataH['Currency'] = $transaction->currency;
			$dataH['Description'] = $transaction->description;
			$dataH['DoneReconciliatedAt'] = $_lib['date']->t_to_mysql_format($transaction->{"done-reconciliated-at"});
			$dataH['FromBankAccount'] = $this->strip_account_number($transaction->{"from-account"});
			$dataH['ToBankAccount'] = $this->strip_account_number($transaction->{"to-account"});
			$dataH['PostingDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $transaction->{"posting-date"});
			$dataH['Ref'] = $transaction->ref;
			$dataH['KID'] = $transaction->kid;
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

    public function findmatch($args) {
        global $_lib;

        $args['VoucherDate'] = substr($args['VoucherDate'], 0, 10);

		$query = "SELECT AccountNumber FROM account WHERE AccountID='" . $args['BankAccountID'] . "'";

		$row = $_lib['storage']->get_row(array('query' => $query));

		if (empty($row)) {
			return false;
		}

		$args['BankAccount'] = str_replace(" ", "", $row->AccountNumber);

		$relations = $this->lookup_invoice_relations($args);

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