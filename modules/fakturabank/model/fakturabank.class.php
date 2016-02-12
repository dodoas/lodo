<?
#should split to factory pattern with incoming and outgoing invoices.
includelogic('invoice/invoice');
includelogic('fakturabank/fakturabankvoting');
includelogic('exchange/exchange');
# needed for updating unit from fakturabank
includelogic('orgnumberlookup/orgnumberlookup');
includelogic("accountplan/scheme");

class lodo_fakturabank_fakturabank {
    private $host           = '';
    private $protocol       = '';
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
                                 'Invoice'          => true,
                                 'cac:InvoiceLine'  => true,
                                 'InvoiceLine'      => true,
                                 'TaxSubtotal'      => true
                                );
    private $attributesOfInterest = array(
									 'schemeID',
									 'currencyID'
								 );

    function __construct() {
        global $_lib;
        $this->startexectime  = microtime();

        $this->username         = $_lib['sess']->get_person('FakturabankUsername');
        $this->password         = $_lib['sess']->get_person('FakturabankPassword');
        $this->retrievestatus   = $_lib['setup']->get_value('fakturabank.status');

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        if(is_array($args)) {
            foreach($args as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if(!$this->username || !$this->username) {
            $_lib['message']->add("Fakturabank brukernavn og passord er ikke definert p&aring; brukeren din");
        } else {
            $this->login = true;
        }

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber= strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));

        $this->credentials = "$this->username:$this->password";
    }

    function __destruct() {
        $this->stopexectime   = microtime();
        $this->diffexectime   = $this->stopexectime - $this->startexectime;
    }

    ####################################################################################################
    #Get a list of all outgoing invoices from fakturabank
    public function outgoing() {
        global $_lib;
        #https://fakturabank.no/invoices/outgoing.xml?orgnr=981951271

        $page       = "invoices/outgoing.xml";

        $params     = "?rows=200&orgnr=$this->OrgNumber"; // add top limit rows=1000, otherwise we only get one record
        $params     .= "&supplier_status=for_bookkeeping"; #Only retrieve with status 'for_bookkeeping'
        $params     .= "&order=invoiceno&sord=asc";

        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['sess']->debug($url);

        $invoicesO = $this->retrieve($page, $url);
		$validated_invoices = $this->validate_outgoing($invoicesO);
		$this->save_outgoing($validated_invoices);
        return $validated_invoices;
    }

    ####################################################################################################
    #Get a list of all incoming invoices from fakturabank
    public function incoming() {
        global $_lib;
        #https://fakturabank.no/invoices?orgnr=981951271

        $page       = "invoices";
        $params     = "?rows=200&orgnr=" . $this->OrgNumber . '&order=issue_date&sord=asc'; // add top limit rows=1000, otherwise we only get one record
        if($this->retrievestatus) $params .= '&customer_status=' . $this->retrievestatus;
        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        $invoicesO = $this->retrieve($page, $url);
        if (empty($invoicesO)) {
            return false;
        }

        $validated_invoices = $this->validate_incoming($invoicesO);
        if (empty($validated_invoices)) {
            return false;
        }

        $this->save_incoming($validated_invoices);

        return $validated_invoices;
    }

	public function get_fakturabankincominginvoice($FakturabankID) {
        global $_lib;

		if (!is_numeric($FakturabankID)) {
			return false;
		}

		$query = "SELECT `ID` FROM fakturabankinvoicein WHERE `FakturabankID` = '$FakturabankID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if (($obj = $_lib['storage']->db_fetch_object($result)) && is_numeric($obj->ID)) {
			return $obj->ID;
		}

		return false;
	}

	public function get_fakturabankoutgoinginvoice($FakturabankID) {
        global $_lib;

		if (!is_numeric($FakturabankID)) {
			return false;
		}

		$query = "SELECT `ID` FROM fakturabankinvoiceout WHERE `FakturabankID` = '$FakturabankID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if (($obj = $_lib['storage']->db_fetch_object($result)) && is_numeric($obj->ID)) {
			return $obj->ID;
		}

		return false;
	}

    private function find_project_by_id($ProjectID) {
        global $_lib;

        if (!is_numeric($ProjectID)) {
            return false;
        }

        $query = "SELECT `ProjectID` FROM project WHERE `ProjectID` = '$ProjectID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if ($obj = $_lib['storage']->db_fetch_object($result)) {
			return $ProjectID;
		} else {
            return false;
        }
    }

    private function find_department_by_id($CompanyDepartmentID) {
        global $_lib;

        if (!is_numeric($CompanyDepartmentID)) {
            return false;
        }

        $query = "SELECT `CompanyDepartmentID` FROM companydepartment WHERE `CompanyDepartmentID` = '$CompanyDepartmentID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if ($obj = $_lib['storage']->db_fetch_object($result)) {
			return $CompanyDepartmentID;
		} else {
            return false;
        }
    }

    private function save_incoming($invoices) {
        global $_lib;

        if (empty($invoices->Invoice)) {
            return false;
        }

        foreach ($invoices->Invoice as &$invoice) {
            $dataH = array();
            $dataRR = array(); // ReconciliationReasons for invoice

            if ($invoice->LodoID) {
                $action = "update";
                $dataH['ID'] = $invoice->LodoID;
            } else {
                $action = "insert";
            }

            if (empty($invoice->FakturabankID)) {
                continue;
            }

            $dataH['FakturabankID'] = $invoice->FakturabankID;
            $dataH['FakturabankNumber'] = $invoice->ID;
            $dataH['ProfileID'] = $invoice->ProfileID;

            //# find KID
            if($invoice->PaymentMeans->InstructionNote == 'KID' && $invoice->PaymentMeans->InstructionID) {
                $dataH['KID']  = $invoice->PaymentMeans->InstructionID; #KID
                                                                            }
            $dataH['IssueDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $invoice->IssueDate);
            $dataH['DocumentCurrency'] = $invoice->DocumentCurrencyCode;
            $dataH['SupplierPartyIndentification'] = $invoice->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
            $dataH['SupplierPartyIndentificationSchemeID'] = $invoice->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            $dataH['SupplierPartyName'] = $invoice->AccountingSupplierParty->Party->PartyName->Name;

            //# use companyid if present
            if (!empty($invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)) {
                $dataH['CustomerPartyIndentification'] = $invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID;
                $dataH['CustomerPartyIndentificationSchemeID'] = $invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            } else {
                $dataH['CustomerPartyIndentification'] = $invoice->AccountingCustomerParty->Party->PartyIdentification->ID;
                $dataH['CustomerPartyIndentificationSchemeID'] = $invoice->AccountingCustomerParty->Party->PartyIdentification->ID_Attr_schemeID;
            }

            /* Fields do not exist in fakturabankinvoicein table
            if ($invoice->Department != "") { // "0" is valid
                $dataH['Department'] = $invoice->Department;
            }
            if ($invoice->Project  != "") { // "0" is valid
                $dataH['Project'] = $invoice->Project;
            }
            if (!empty($invoice->ProjectNameInternal)) {
                $dataH['ProjectNameInternal'] = $invoice->ProjectNameInternal;
            }
            if (!empty($invoice->ProjectNameCustomer)) {
                $dataH['ProjectNameCustomer'] = $invoice->ProjectNameCustomer;
            }
            */

            $dataH['CustomerPartyName'] = $invoice->AccountingCustomerParty->Party->PartyName->Name;
            $dataH['PaymentMeansCode'] = $invoice->PaymentMeans->PaymentMeansCode;

            $dataH['PaymentMeansDueDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $invoice->PaymentMeans->PaymentDueDate);
            $dataH['TaxTotalAmount'] = $invoice->TaxTotal->TaxAmount;
            $dataH['TaxTotalAmountCurrency'] = $invoice->TaxTotal->TaxAmount_Attr_currencyID;
            $dataH['LegalMonetaryTotTaxExclusAmount'] = $invoice->LegalMonetaryTotal->TaxExclusiveAmount;
            $dataH['LegalMonetaryTotTaxExclusAmountCurrency'] = $invoice->LegalMonetaryTotal->TaxExclusiveAmount_Attr_currencyID;
            $dataH['LegalMonetaryTotPayableAmount'] = $invoice->LegalMonetaryTotal->PayableAmount;
            $dataH['LegalMonetaryTotPayableAmountCurrency'] = $invoice->LegalMonetaryTotal->PayableAmount_Attr_currencyID;
            $dataH['Class'] = $invoice->Class;
            $dataH['Status'] = $invoice->Status;
            $dataH['Journal'] = $invoice->Journal;
            $dataH['JournalID'] = $invoice->JournalID;
            $dataH['AccountPlanID'] = $invoice->AccountPlanID;
            $dataH['VoucherType'] = $invoice->VoucherType;


            $ret = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'fakturabankinvoicein', 'action' => $action, 'debug' => false));

            $reason_array = array();
            if (!empty($invoice->ReconciliationReasons)) {
                foreach ($invoice->ReconciliationReasons as &$reason) {
                     $dataRR['FakturabankInvoiceInId'] = $this->get_fakturabankincominginvoice($invoice->FakturabankID);
                     $dataRR['ClosingReasonId'] = $reason[0];
                     $dataRR['Amount'] = $reason[1];
                     $dataRR['IsCustomerClosingReason'] = (int)$reason[2];
                     // InvoiceOut is automatically set to false since it is its default value
                     $reason_array[] = $dataRR;
                }
            }

            // Cleaning all reasons records from database so we can skip checking if we have more or less reason in updated download
            if ($action = "update") {
                if ($invoice->LodoID) {
                    $query = 'DELETE FROM fbdownloadedinvoicereasons where FakturabankInvoiceInId = ' . $invoice->LodoID;
                    $_lib['storage']->db_query($query);
                }

            }
            foreach ($reason_array as $dataRR) {
                $ret = $_lib['storage']->store_record(array('data' => $dataRR, 'table' => 'fbdownloadedinvoicereasons', 'action' => "insert", 'debug' => false));
            }

            if ($action == "insert") {
                $invoice->LodoID = $ret;
            }
        }
    }

    private function save_outgoing($invoices) {
        if (empty($invoices)) {
            return false;
        }

        global $_lib;

		foreach ($invoices->Invoice as &$invoice) {
			$dataH = array();
            $dataRR = array(); // ReconciliationReasons for invoice

			if ($invoice->LodoID) {
				$action = "update";
				$dataH['ID'] = $invoice->LodoID;
			} else {
				$action = "insert";
			}

			$dataH['FakturabankID'] = $invoice->FakturabankID;
			$dataH['FakturabankNumber'] = $invoice->ID;
			$dataH['ProfileID'] = $invoice->ProfileID;

			# find KID
			if($invoice->PaymentMeans->InstructionNote == 'KID' && $invoice->PaymentMeans->InstructionID) {
				$dataH['KID']  = $invoice->PaymentMeans->InstructionID; #KID
			}
			$dataH['IssueDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $invoice->IssueDate);
			$dataH['DocumentCurrency'] = $invoice->DocumentCurrencyCode;
			$dataH['SupplierPartyIndentification'] = $invoice->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
			$dataH['SupplierPartyIndentificationSchemeID'] = $invoice->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
			$dataH['SupplierPartyName'] = $invoice->AccountingSupplierParty->Party->PartyName->Name;
            # use companyid if present
            if (!empty($invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)) {
                $dataH['CustomerPartyIndentification'] = $invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID;
                $dataH['CustomerPartyIndentificationSchemeID'] = $invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            } else {
                $dataH['CustomerPartyIndentification'] = $invoice->AccountingCustomerParty->Party->PartyIdentification->ID;
                $dataH['CustomerPartyIndentificationSchemeID'] = $invoice->AccountingCustomerParty->Party->PartyIdentification->ID_Attr_schemeID;
            }
            /* Fields do not exist in fakturabankinvoiceout table
            if (is_numeric($invoice->DepartmentID)) { // "0" is valid
                $dataH['DepartmentID'] = $invoice->DepartmentID;
            }
            if (!empty($invoice->DepartmentCustomer)) {
                $dataH['DepartmentCustomer'] = $invoice->DepartmentCustomer;
            }
            if (is_numeric($invoice->ProjectID)) { // "0" is valid
                $dataH['ProjectID'] = $invoice->ProjectID;
            }
            if (!empty($invoice->ProjectNameInternal)) {
                $dataH['ProjectNameInternal'] = $invoice->ProjectNameInternal;
            }
            if (!empty($invoice->ProjectNameCustomer)) {
                $dataH['ProjectNameCustomer'] = $invoice->ProjectNameCustomer;
            }
            */
			$dataH['CustomerPartyName'] = $invoice->AccountingCustomerParty->Party->PartyName->Name;
			$dataH['PaymentMeansCode'] = $invoice->PaymentMeans->PaymentMeansCode;

			$dataH['PaymentMeansDueDate'] = $_lib['date']->mysql_format("%Y-%m-%d", $invoice->PaymentMeans->PaymentDueDate);
			$dataH['TaxTotalAmount'] = $invoice->TaxTotal->TaxAmount;
			$dataH['TaxTotalAmountCurrency'] = $invoice->TaxTotal->TaxAmount_Attr_currencyID;
			$dataH['LegalMonetaryTotTaxExclusAmount'] = $invoice->LegalMonetaryTotal->TaxExclusiveAmount;
			$dataH['LegalMonetaryTotTaxExclusAmountCurrency'] = $invoice->LegalMonetaryTotal->TaxExclusiveAmount_Attr_currencyID;
			$dataH['LegalMonetaryTotPayableAmount'] = $invoice->LegalMonetaryTotal->PayableAmount;
			$dataH['LegalMonetaryTotPayableAmountCurrency'] = $invoice->LegalMonetaryTotal->PayableAmount_Attr_currencyID;
			$dataH['Class'] = $invoice->Class;
			$dataH['Status'] = $invoice->Status;
			$dataH['Journal'] = $invoice->Journal;
			$dataH['JournalID'] = $invoice->JournalID;
			$dataH['AccountPlanID'] = $invoice->AccountPlanID;
			$dataH['VoucherType'] = $invoice->VoucherType;

			$ret = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'fakturabankinvoiceout', 'action' => $action, 'debug' => false));

            $reason_array = array();
            if (!empty($invoice->ReconciliationReasons)) {
                foreach ($invoice->ReconciliationReasons as &$reason) {
                     $dataRR['FakturabankInvoiceInId'] = $this->get_fakturabankoutgoinginvoice($invoice->FakturabankID);
                     $dataRR['ClosingReasonId'] = $reason[0];
                     $dataRR['Amount'] = $reason[1];
                     $dataRR['IsCustomerClosingReason'] = (int)$reason[2];
                     // InvoiceOut is set to true to note that this reason is for an outgoing invoice
                     $dataRR['InvoiceOut'] = true;
                     $reason_array[] = $dataRR;
                }
            }

            // Cleaning all reasons records from database so we can skip checking if we have more or less reason in updated download
            if ($action = "update") {
                if ($invoice->LodoID) {
                    $query = 'DELETE FROM fbdownloadedinvoicereasons where FakturabankInvoiceInId = ' . $invoice->LodoID;
                    $_lib['storage']->db_query($query);
                }

            }

            foreach ($reason_array as $dataRR) {
                $_lib['storage']->store_record(array('data' => $dataRR, 'table' => 'fbdownloadedinvoicereasons', 'action' => "insert", 'debug' => false));
            }

			if ($action == "insert") {
				$invoice->LodoID = $ret;
			}
		}
	}

    ####################################################################################################
    #READ XML
    private function retrieve($page, $url) {
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

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

        $xml_data           = curl_exec($ch);

        if (curl_errno($ch)) {
            $_lib['message']->add("Nettverkskobling til Fakturabank ikke OK");
            $_lib['message']->add("Error: " . curl_error($ch));
        } else {
            $_lib['message']->add("Nettverkskobling til Fakturabank OK");
        }
        curl_close($ch);


        $size = strlen($xml_data);
	$xml_std_header = '<?xml version="1.0"?>';
	$std_hd_len = strlen($xml_std_header);
	if (substr($xml_data, 0, $std_hd_len) == $xml_std_header) {
		$xml_data = trim(substr($xml_data, $std_hd_len));
	}
        if(substr($xml_data,0,9) != '<Invoices') {

            $_lib['message']->add($xml_data);

        } else {

            if($size) {
                includelogic('xmldomtoobject/xmldomtoobject');
                $domtoobject = new empatix_framework_logic_xmldomtoobject(array('arrayTags' => $this->ArrayTag, 'attributesOfInterest' => $this->attributesOfInterest));
                #print "\n<hr>$xml_data\n<hr>";
                $invoiceO    = $domtoobject->convert($xml_data);
            } else {
                $_lib['message']->add("XML Dokument tomt - pr&oslash;v igjen: $url");
            }
        }

        return $invoiceO;
    }

    ################################################################################################
    # Sets the given status and comment on an invoice in Fakturabank with a given internal FakturabankID
    #input: id (FakturabankI internal ID, status[registered], comment (without & signs)
    #outoupt: changed status event in Fakturabank
    private function setEvent($id, $status, $comment) {
        global $_lib;
        $retstatus = true;

        ############################################################################################
        #Make Event XML
        $dom            = new DOMDocument( "1.0", "UTF-8" );
        $dom_event      = $dom->createElement('event');
        $dom_name       = $dom->createElement('name', $status);
        $dom_comment    = $dom->createElement('comment', $comment);
        $dom_company    = $dom->createElement('company');
        $dom_identifier = $dom->createElement('identifier', $this->OrgNumber);

        $dom_company->appendChild($dom_identifier);
        $dom_event->appendChild($dom_name);
        $dom_event->appendChild($dom_comment);
        $dom_event->appendChild($dom_company);
        $dom->appendChild($dom_event);
        $xml = $dom->saveXML();

        ############################################################################################
        #Set event status on fakturabank server
        $page   = "invoices/$id/events";
        $url    = "$this->protocol://$this->host/$page";

        #print("setEvent: $url: $xml");

        $headers = array(
            "GET " . $page . " HTTP/1.0",
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
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        #curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

        $returndata = curl_exec($ch);

        if (curl_errno($ch)) {
            $_lib['message']->add("Error: " . curl_error($ch));
            $retstatus = false;
        } else {
            #$_lib['message']->add("Satt event: $returndata");
        }
        curl_close($ch);
        return $retstatus;
    }

    private function extractOutgoingAccountingCost(&$InvoiceO) {
        // we support using ; instead of &
        parse_str(str_replace(";", "&", $InvoiceO->AccountingCost), $acc_cost_params);

        /* department information is not required for outgoing invoices, so no error if empty */
        if (!empty($acc_cost_params)) {
            if (!empty($acc_cost_params['customerdepartment'])) {
                // In Lodo, DepartmentCustomer is really used for department project name (in this case supplier department name)
                $InvoiceO->DepartmentCustomer = $acc_cost_params['customerdepartment'];
            }

            if ($acc_cost_params['departmentcode'] != '') {
                if (!is_numeric($DepartmentID = $acc_cost_params['departmentcode'])) {
                    $InvoiceO->Status     .= "Faktura har feil i avdelingskode (departmentcode " . $acc_cost_params['departmentcode'] . " must be empty or a number for incoming invoices)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($this->find_department_by_id($acc_cost_params['departmentcode']))) {
                    $InvoiceO->DepartmentID = $DepartmentID;
                } else {
                    $InvoiceO->Status     .= "Fant ikke intern avdeling for kode " . $DepartmentID . " (departmentcode does not match any internal departments)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
            }

            if (!empty($acc_cost_params['customerproject'])) {
                $InvoiceO->ProjectNameCustomer = $acc_cost_params['customerproject'];
            }

            if ($acc_cost_params['projectcode'] != "") {
                if (!is_numeric($acc_cost_params['projectcode'])) {
                    $InvoiceO->Status     .= "Faktura har feil i prosjektkode (projectcode must be empty or a number for incoming invoices)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($ProjectID = $this->find_project_by_id($acc_cost_params['projectcode']))) {
                    $InvoiceO->ProjectID = $ProjectID;
                } else {
                    $InvoiceO->Status     .= "Fant ikke kundens prosjekt for kode $ProjectID (projectcode does not match any internal projects)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }

                if (!empty($acc_cost_params['project'])) {
                    $InvoiceO->ProjectNameInternal = $acc_cost_params['project'];
                }
            }

            if (!empty($acc_cost_params['supplierreconciliationreasons'])) {
                foreach ($acc_cost_params['supplierreconciliationreasons'] as $key => $value) {
                    // we will get reconciliation_line_reason.id-closing_reason.id
                    $key_array = explode('-', $key);
                    $key = $key_array[count($key_array)-1];

                    if (is_numeric($key) && is_numeric($value)) {
                        // Passing true or false may not be needed anymore
                        $InvoiceO->ReconciliationReasons[] = array($key, $value, false);
                    }
                }
            }
        }

        return true;
    }

    ################################################################################################
    #validate - update Accountplans and statuses - ready for journaling flag.
    private function validate_outgoing($invoicesO) {
        if (!is_array($invoicesO->Invoice) || empty($invoicesO->Invoice)) {
            return false;
        }

        global $_lib, $accounting;

        foreach($invoicesO->Invoice as &$InvoiceO) {

            $i++;
            if (!($i % 2)) {
                $InvoiceO->Class = "r1";
            } else {
                $InvoiceO->Class = "r0";
            }

            #New variables we add in this process
            $InvoiceO->Status        = '';
            $InvoiceO->Journal       = true;
            $InvoiceO->Journaled     = false;
            $InvoiceO->JournalID     = $InvoiceO->ID;
            $InvoiceO->VoucherType   = 'S';
            $InvoiceO->AccountPlanID = 0;
            $InvoiceO->MotkontoAccountPlanID = 0;
            $InvoiceO->Period        = substr($InvoiceO->IssueDate, 0, 7);
            $InvoiceO->ReconciliationReasons = array();

            $urlH = explode('/', $InvoiceO->UBLExtensions->UBLExtension->ExtensionContent->URL);
            $InvoiceO->FakturabankID = $urlH[4]; #Last element is the internalID.

            # get fakturabankinvoiceout id
			if ($LodoID = $this->get_fakturabankoutgoinginvoice($InvoiceO->FakturabankID)) {
				$InvoiceO->LodoID = $LodoID;
			} else {
				$InvoiceO->LodoID = null;
			}

            if (empty($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID)) {
                $InvoiceO->Status     .= "Faktura mangler kundenummer";
                $InvoiceO->Journal     = false;
                $InvoiceO->Class       = 'red';

                continue;
            }

            $customernumber = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;

            if (!$this->extractOutgoingAccountingCost($InvoiceO)) {
                continue;
            }

            #Should this be more restricted in time or period to eliminate false searches? Any other method to limit it to only look in the correct records? No?
            $account = $this->find_customer_reskontro_by_customernumber($customernumber);
            if ($account) {
                $InvoiceO->AccountPlanID = $account->AccountPlanID;

                if(!$accounting->is_valid_accountperiod($InvoiceO->Period, $_lib['sess']->get_person('AccessLevel'))) {
                    #Finne siste og f¿rste Œpne periode kunne v¾rt i et eget accountperiod objekt.

                    $PeriodOld         = $InvoiceO->Period;
                    $InvoiceO->Period  = $accounting->get_first_open_accountingperiod();
                    $InvoiceO->Status .= 'Perioden ' . $PeriodOld . ' er lukket endrer til ' . $InvoiceO->Period . '. ';
                }

                #Check that we have not journaled the same invoices earlier.
                #JournalID = Invoice number on outgoing invoices.
                $query          = "select * from invoiceout where InvoiceID='" . $InvoiceO->ID . "'";
                #print "$query<br>\n";
                $voucherexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($voucherexists) {
                    $InvoiceO->Status     .= "Faktura er lastet ned";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Journaled   = true;
                    $InvoiceO->Class       = 'green';
                } else {

                    foreach($InvoiceO->InvoiceLine as &$line) {

                        if($line->TaxTotal->TaxSubtotal[0]->TaxableAmount != 0) {
                            #It has to be an amount to be checked - all zero lines will not be imported later.
                            $query          = "select * from product where ProductNumber='" . $line->Item->SellersItemIdentification->ID . "' and Active=1";
                            #print "$query<br>\n";
                            $productexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                            if($productexists) {
                                if($productexists->AccountPlanID) {
                                    $line->Item->SellersItemIdentification->ProductID = $productexists->ProductID;

                                } else {
                                    $InvoiceO->Status     .= "Konto ikke satt p&aring; produkt: " . $line->Item->SellersItemIdentification->ID;
                                    $InvoiceO->Journal     = false;
                                    $InvoiceO->Class       = 'red';
                                }
                            } else {
                                $InvoiceO->Status     .= "Produktnr: " . $line->Item->SellersItemIdentification->ID . " eksisterer ikke. ";
                                $InvoiceO->Journal     = false;
                                $InvoiceO->Class       = 'red';
                            }
                        }
                        #Vi kunne ha auto opprettet produkter ogsŒ.....
                    }
                }

                if($InvoiceO->Journal) {
                    $InvoiceO->Status   .= "Klar til bilagsf&oslash;ring basert p&aring: Kundenummer";
                }
            } else {
                $InvoiceO->Status     .= "Finner ikke kunde basert pŒ kundenummer: " . $customernumber . ". ";
                $msg = "Opprett p&aring; kundenr";
                $InvoiceO->Status .= sprintf('<a href="#" onclick="javascript:addsingleaccountplan(\'%s\'); return false;">%s</a>', $InvoiceO->ID, $msg);
                $InvoiceO->Journal = false;
                $InvoiceO->Class   = 'red';
            }
        }
        return $invoicesO;
    }

    private function extractIncomingAccountingCost(&$InvoiceO) {
        // we support using ; instead of &
        parse_str(str_replace(";", "&", $InvoiceO->AccountingCost), $acc_cost_params);

        /* department information is not required for incoming invoices, so no error if empty */
        if (!empty($acc_cost_params)) {
            /* Ignore department for now, since we currently don't store it in the db
               if (!empty($acc_cost_params['department'])) {
               // In Lodo, DepartmentCustomer is really used for department project name (in this case supplier department name)
               $InvoiceO->DepartmentCustomer = $acc_cost_params['department'];
               }
            */

            if ($acc_cost_params['customerdepartmentcode'] != "") {
                if (!is_numeric($acc_cost_params['customerdepartmentcode'])) {
                    $InvoiceO->Status     .= "Faktura har feil i prosjektkode (customerdepartmentcode must be empty or a number for incoming invoices)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($DepartmentID = $this->find_department_by_id($acc_cost_params['customerdepartmentcode']))) {
                    $InvoiceO->Department = $DepartmentID;
                } else {
                    $InvoiceO->Status     .= "Fant ikke intern avdeling for kode $DepartmentID (customerdepartmentcode does not match any internal departments)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
            }

            if (!empty($acc_cost_params['project'])) {
                // In Lodo, ProjectNameCustomer is really used for counterpart project name (in this case  supplier)
                $InvoiceO->ProjectNameCustomer = $acc_cost_params['project'];
            }

            if ($acc_cost_params['customerprojectcode'] != "") { // "0" is a valid value
                if (!is_numeric($acc_cost_params['customerprojectcode'])) {
                    $InvoiceO->Status     .= "Faktura har feil i prosjektkode (customerprojectcode must be empty or a number for incoming invoices)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($ProjectID = $this->find_project_by_id($acc_cost_params['customerprojectcode']))) {
                    $InvoiceO->Project = $ProjectID;
                } else {
                    $InvoiceO->Status     .= "Fant ikke kundens prosjekt for kode $ProjectID (customerprojectcode does not match any internal projects)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }

                if (!empty($acc_cost_params['customerproject'])) {
                    $InvoiceO->ProjectNameInternal = $acc_cost_params['customerproject'];
                }
            }

            if (!empty($acc_cost_params['reisegarantifondet'])) {
                $value = $acc_cost_params['reisegarantifondet'];
                $value = strtolower($value);
                if($value ==  1 || $value ==  'yes' || $value == 'ja') {
                    $value = 1;
                } else {
                    $value = 0;
                }
                $InvoiceO->Reisegarantifond = $value;
            }

            if (!empty($acc_cost_params['customerreconciliationreasons'])) {
                foreach ($acc_cost_params['customerreconciliationreasons'] as $key => $value) {
                    // we will get reconciliation_line_reason.id-closing_reason.id
                    $key_array = explode('-', $key);
                    $key = $key_array[count($key_array)-1];

                    if (is_numeric($value)) {
                        // Passing true or false may not be needed anymore
                        $InvoiceO->ReconciliationReasons[] = array($key, $value, true);
                    }
                }
            }

        }

        return true;
    }

    ################################################################################################
    #validate - update Accountplans and statuses - ready for journaling flag.
    private function validate_incoming($invoicesO) {
        global $_lib, $accounting;

        $VoucherType = 'U';

        #Estimate which journal ids will be used
        list($JournalID, $tmp) = $accounting->get_next_available_journalid(array('available' => true, 'update' => false, 'type' => $VoucherType, 'reuse' => false, 'from' => 'Fakturabank estimate'));

	/* Cleanup error message -eirhje 29.01.10 */
       if(isset($invoicesO->Invoice))
        foreach($invoicesO->Invoice as &$InvoiceO) {

            $i++;
            if (!($i % 2)) {
                $InvoiceO->Class = "r1";
            } else {
                $InvoiceO->Class = "r0";
            }

            #New variables we add in this process
            $InvoiceO->Status        = '';
            $InvoiceO->Journal       = true;
            $InvoiceO->JournalID     = 0;
            $InvoiceO->AccountPlanID = 0;
            $InvoiceO->MotkontoAccountPlanID = 0;
            $InvoiceO->Period        = substr($InvoiceO->IssueDate, 0, 7);
            $InvoiceO->VoucherType   = $VoucherType;
            $InvoiceO->ReconciliationReasons = array();

            if (!$this->extractIncomingAccountingCost($InvoiceO)) {
                continue;
            }

            $urlH = explode('/', $InvoiceO->UBLExtensions->UBLExtension->ExtensionContent->URL);
            $InvoiceO->FakturabankID = $urlH[4]; #Last element is the internalID.
	    #Cleaning after prior developer -eirhje 23.01.10

            # get fakturabankinvoicein id
			if ($LodoID = $this->get_fakturabankincominginvoice($InvoiceO->FakturabankID)) {
				$InvoiceO->LodoID = $LodoID;
			} else {
				$InvoiceO->LodoID = null;
			}

            #print "URL: " . $InvoiceO->UBLExtensions->UBLExtension->ExtensionContent->URL . "<br>\n";
            #print "FB ID:   $InvoiceO->FakturabankID<br>\n";

            //#Should this be more restricted in time or period to eliminate false searches? Any other method to limit it to oly look in the correct records? No?

            list($account, $SchemeID)  = $this->find_reskontro($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID, 'supplier', $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID);
            if($account) {
                $InvoiceO->AccountPlanID   = $account->AccountPlanID;

                #Check if this invoice exists
                $query          = "select * from invoicein where SupplierAccountPlanID='" . $InvoiceO->AccountPlanID . "' and InvoiceNumber='" . $InvoiceO->ID . "'";
                //print "$query<br>\n";
                $invoiceexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($invoiceexists) {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    $InvoiceO->Status .= 'Faktura er allerede lastet ned';
                }


                if($account->EnableMotkontoResultat && $account->MotkontoResultat1) {
                    $InvoiceO->MotkontoAccountPlanID   = $account->MotkontoResultat1;
                } elseif($account->EnableMotkontoBalanse && $account->MotkontoBalanse1) {
                    $InvoiceO->MotkontoAccountPlanID   = $account->MotkontoBalanse1;
                }

                // Bookkeeping account data
                $query = "select * from accountplan where AccountPlanID='" . $InvoiceO->MotkontoAccountPlanID . "' and Active=1";

                $acc = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));

                $InvoiceO->MotkontoAccountName = $acc->AccountName;

                if(!$InvoiceO->MotkontoAccountPlanID) {
                    $InvoiceO->Status   .= sprintf(
                        'Motkonto resultat/balanse ikke satt for konto <a href="%s&t=accountplan.reskontro&AccountPlanID=%s&inline=show" target="_blank">%s</a>',
                        $_lib['sess']->dispatch,
                        $InvoiceO->AccountPlanID,
                        $InvoiceO->AccountPlanID
                        );

                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                }

                if(!$accounting->is_valid_accountperiod($InvoiceO->Period, $_lib['sess']->get_person('AccessLevel'))) {
                    #Finne siste og f¿rste Œpne periode kunne v¾rt i et eget accountperiod objekt.

                    $PeriodOld         = $InvoiceO->Period;
                    $InvoiceO->Period  = $accounting->get_first_open_accountingperiod();
                    $InvoiceO->Status .= 'Perioden ' . $PeriodOld . ' er lukket endrer til ' . $InvoiceO->Period . '. ';
                }
                if ($InvoiceO->DocumentCurrencyCode != exchange::getLocalCurrency() && !exchange::getConversionRate($InvoiceO->DocumentCurrencyCode)) {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    $InvoiceO->Status .= 'Finner ikke valutaverdi for '. $InvoiceO->DocumentCurrencyCode;
                }

                if ($InvoiceO->IssueDate == '0000-00-00') {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    $InvoiceO->Status .= 'Dato kan ikke v&aelig;re '. $InvoiceO->IssueDate;
                }
                if ($InvoiceO->LegalMonetaryTotal->PayableAmount == 0) {
                    $InvoiceO->Status .= "Vil du ha fakturabel&oslash;p kr " . $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) . '? ';
                }

                #Check that we have not journaled the same invoices earlier. C
                $query          = "select * from invoicein where SupplierAccountPlanID='" . $InvoiceO->AccountPlanID . "' and InvoiceNumber='" . $InvoiceO->ID . "' and Active=1";
                #print "$query<br>\n";
                $voucherexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($voucherexists) {
                    $InvoiceO->Status     .= "Faktura er lastet ned";
                    $InvoiceO->Journal     = false;

                    if($voucherexists->JournalID) {
                        $InvoiceO->JournalID   = $voucherexists->JournalID;
                        $InvoiceO->Journaled   = true;

                    } else {
                        $InvoiceO->JournalID   = $JournalID;
                        $JournalID++;
                    }
                    $InvoiceO->Class       = 'green';
                } else {
                    #Just estimate which journal ID's we are going to use
                    $InvoiceO->JournalID   = $JournalID;
                    $JournalID++;
                }

                # validate invoice lines
                foreach($InvoiceO->InvoiceLine as &$line) {
                  if ($line->Item->AdditionalItemProperty->Name == 'Car') {
                    $query = "select * from car where CarCode='" . $line->Item->AdditionalItemProperty->Value . "' and Active=1";
                    $carexists = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                    if($carexists) {
                      $line->Item->CarID   = $carexists->CarID;
                      $line->Item->CarCode = $carexists->CarCode;
                    }
                    else {
                      $InvoiceO->Status .= "Bil: " . $line->Item->AdditionalItemProperty->Value . " eksisterer ikke. ";
                      $InvoiceO->Journal = false;
                      $InvoiceO->Class   = 'red';
                    }
                  }
                }
                if($InvoiceO->Journal) {
                    $InvoiceO->Status   .= "Klar til bilagsf&oslash;ring basert p&aring: SchemeID: $SchemeID";
                }

                #$this->registerincoming($InvoiceO);
            } else {
                $scheme_value = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                $scheme_type  = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
                $InvoiceO->Status   .= "Finner ikke leverand&oslash;r basert p&aring; PartyIdentification: " .
                    $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;

                // add if NO:ORGNR
                if ($scheme_type == 'NO:ORGNR') {
                  $InvoiceO->Status   .= sprintf(
                    '<a href="%s&t=fakturabank.createaccount&accountplanid=%s&orgnumber=%s&type=supplier" target="_blank">Opprett</a>',
                    $_lib['sess']->dispatch,
                    $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID,
                    $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID
                  );
                }
                else { // add based on scheme
                  $InvoiceO->Status   .= sprintf(
                    '<a href="%s&t=fakturabank.createaccount&amp;not_noorgno=1&scheme_type=%s&scheme_value=%s&type=supplier" target="_blank">Opprett</a>',
                    $_lib['sess']->dispatch,
                    $scheme_type,
                    $scheme_value
                  );
                }

                $InvoiceO->Journal = false;
                $InvoiceO->Class   = 'red';
                $InvoiceO->MissingAccountPlan = true;
            }
        }
        return $invoicesO;
    }

    private function extractCustomerSchemeID($InvoiceO) {
        $companyid = null;
        $schemeid = null;

        if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)) {
            if ($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID != 'NO:SUP-ACCNT-RE') {
                $companyid = $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID;
                $schemeid = $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            }
        }

        return array($companyid, $schemeid);
    }

    private function extractSupplierSchemeID($InvoiceO) {
        $companyid = null;
        $schemeid = null;

        if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID)) {
            if ($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID != 'NO:SUP-ACCNT-RE') {
                $companyid = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                $schemeid = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            }
        }

        return array($companyid, $schemeid);
    }


    private function find_customer_reskontro_by_customernumber($customernumber) {
        global $_lib;


        $query                  = "select * from accountplan where REPLACE(AccountPlanID, ' ', '') like '%" . $customernumber . "%' and AccountPlanID <> '' and AccountPlanID is not null and AccountPlanType='customer'";
        $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
        if($account) {
            return $account;
        } else {
            return false;
        }
    }

    ################################################################################################
    # Try to find the reskontro in the following sequenze: OrgNumber, E-Mail, Phone, AccountPlanID/Customer number
    # It will be possible to add a lot of mappings here - but it will be a lot of manuell adminsitration to get it working
    private function find_reskontro($PartyIdentification, $type, $SchemeType = '') {
        global $_lib;

        if($PartyIdentification) {

            if ($type == 'supplier') {
                $SequenceH = array(
                                   'OrgNumber'                 => 1,
                                   'IBAN'                      => 2,
                                   'DomesticBankAccount'       => 3,
                                   'Email'                     => 4,
                                   'Mobile'                    => 5,
                                   'Phone'                     => 6,
                                   'AccountPlanID'             => 8,
                                   'AccountName'               => 9,
                                   );

            } else {
                $SequenceH = array(
                                   'OrgNumber'                 => 1,
                                   'IBAN'                      => 2,
                                   'DomesticBankAccount'       => 3,
                                   'Email'                     => 4,
                                   'Mobile'                    => 5,
                                   'Phone'                     => 6,
                                   'CustomerNumber'            => 7,
                                   'AccountPlanID'             => 8,
                                   'AccountName'               => 9,
                                   );

            }
            foreach($SequenceH as $key => $value) {
                #We should look at SchemeID - but the parser does not give us the scheme id - so we look in the preferred sequence until we find an account.
                $query                  = "select * from accountplan where REPLACE($key, ' ', '') like '%" . $PartyIdentification . "%' and $key <> '' and $key is not null and AccountPlanType='$type'";
                #print "$query<br>\n";
                $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($account) {
                    $SchemeID  = $key;
                    #print "Fant den med $SchemeID: $PartyIdentification<br />\n";
                    break;
                }
            }
        }

        // try with GLN
        if(!$account) {
            $query = sprintf("select a.* from accountplan a, accountplangln g WHERE a.AccountPlanID = g.AccountPlanID AND g.GLN = '%s'",
                             $PartyIdentification);
            $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
            if($account) {
                $SchemeID = "GLN";
            }
        }

        // Scheme ID lookup
        if(!$account && $SchemeType) {
            $schemeLookup = lodo_accountplan_scheme::findAccountPlanType($SchemeType, $PartyIdentification, $type);
            if($schemeLookup !== null) {
                $query = sprintf("select * from accountplan where accountplanid = %d", $schemeLookup);
                $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                $SchemeID = $SchemeType;
            }

        }
        #Forutsatt antall bare 1.

        return array($account, $SchemeID);
    }

    #Update AccountPlan from Fakturabank unit
    public function update_accountplan_from_fakturabank($AccountPlanID) {
      global $_lib;

      $schemeControl = new lodo_accountplan_scheme($AccountPlanID);
      $schemes = $schemeControl->listSchemes();
      # added so if no schemes are present to try with AccountPlanId or OrgNo
      if (empty($schemes)) $schemes[] = array("SchemeValue" => "", "FakturabankSchemeID" => 1);
      # try to download info for each scheme. we only need one success
      foreach ($schemes as $scheme) {
        $scheme_value = $scheme['SchemeValue'];
        $scheme_type = $schemeControl->findScheme($scheme['FakturabankSchemeID']);
        $org = new lodo_orgnumberlookup_orgnumberlookup();
        if (empty($scheme_value)) {
          if(strlen($_POST['accountplan_OrgNumber']) >= 9) $scheme_value = $_POST['accountplan_OrgNumber'];
          elseif(strlen($AccountPlanID) >= 9) $scheme_value = $AccountPlanID;
        }
        $org->getOrgNumberByScheme($scheme_value, $scheme_type);

        if($org->success) {
          $_lib['message']->add("Opplysninger er hentet automatisk basert p&aring; organisasjonsnummeret.");

          // Only update if the fields contains a value
          if($org->OrgNumber)   $_POST['accountplan_OrgNumber']   = $dataH['OrgNumber'] = $org->OrgNumber;
          if($org->AccountName) $_POST['accountplan_AccountName'] = $dataH['AccountName'] = $org->AccountName;
          if($org->Email)       $_POST['accountplan_Email']       = $dataH['Email'] = $org->Email;
          if($org->Mobile)      $_POST['accountplan_Mobile']      = $dataH['Mobile'] = $org->Mobile;
          if($org->Phone)       $_POST['accountplan_Phone']       = $dataH['Phone'] = $org->Phone;
          if(!empty($org->ParentCompanyName))    $_POST['accountplan_ParentName']   = $dataH['ParentName'] = $org->ParentCompanyName;
          if(!empty($org->ParentCompanyNumber))  $_POST['accountplan_ParentOrgNumber']   = $dataH['ParentOrgNumber'] = $org->ParentCompanyNumber;

          $_POST['accountplan_EnableInvoiceAddress'] = $dataH['EnableInvoiceAddress'] = 1;
          if($org->IAdress->Address1) $_POST['accountplan_Address'] = $dataH['Address'] = $org->IAdress->Address1;
          if($org->IAdress->City)     $_POST['accountplan_City']    = $dataH['City'] = $org->IAdress->City;
          if($org->IAdress->ZipCode)  $_POST['accountplan_ZipCode'] = $dataH['ZipCode'] = $org->IAdress->ZipCode;

          if($org->IAdress->Country)  $_POST['accountplan_CountryCode'] = $dataH['CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);

          if($org->DomesticBankAccount) $_POST['accountplan_DomesticBankAccount'] = $dataH['DomesticBankAccount'] = $org->DomesticBankAccount;

          if($org->CreditDays) {
            $_POST['accountplan_EnableCredit'] = $dataH['EnableCredit'] = 1;
            $_POST['accountplan_CreditDays'] = $dataH['CreditDays'] = $org->CreditDays;
          }
          if($org->MotkontoResultat1)	{
            $_POST['accountplan_EnableMotkontoResultat'] = $dataH['EnableMotkontoResultat'] = 1;
            $_POST['accountplan_MotkontoResultat1'] = $dataH['MotkontoResultat1'] = $org->MotkontoResultat1;
          }
          if($org->MotkontoResultat2)	{
            $_POST['accountplan_EnableMotkontoResultat'] = $dataH['EnableMotkontoResultat'] = 1;
            $_POST['accountplan_MotkontoResultat2'] = $dataH['MotkontoResultat2'] = $org->MotkontoResultat2;
          }
          if($org->MotkontoBalanse1) {
            $_POST['accountplan_EnableMotkontoResultat'] = $dataH['EnableMotkontoResultat'] = 1;
            $_POST['accountplan_MotkontoBalanse1'] = $dataH['MotkontoBalanse1'] = $org->MotkontoBalanse1;
          }
          $dataH['AccountPlanID'] = $AccountPlanID;
          $dataH['Active'] = 1;
          # if one successful download is done, then we are done
          break;
        }
      }
      $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'debug' => false));
    }

    #Only for adding new customers at this point in time.
    public function addmissingaccountplan($single_invoice_id = false) {
        global $_lib;

        $VALID_ORGNO_SCHEMES = array('NO:ORGNR', 'SE:OrganisationNumber');

        $invoicesO  = $this->outgoing();
        $count      = 0;

        foreach($invoicesO->Invoice as $InvoiceO) {
            if (!empty($single_invoice_id) && $InvoiceO->ID != $single_invoice_id) {
                continue; // this is not the droid you are looking for
            }

            if (empty($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID)) {
                $_lib['message']->add("Ikke mulig &aring; auto-opprette fordi kundenr mangler.");
                continue;
            }

            $customernumber = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;

            list($SchemeID, $SchemeIDType) = $this->extractCustomerSchemeID($InvoiceO);

            #check if exists first
            if ($account = $this->find_customer_reskontro_by_customernumber($customernumber)) {
                continue; // exists already
            } else {
                $dataH = array();

                if($customernumber > 10000) {
                    #Vi burde visst SchemeID - slik at vi kan bestemme om kontoplan skal telles automatisk eller ikke > 10000 pga norsk kontoplan

                    #Vi mŒ uansett sjekke at den foreslŒtte kontoplanen ikke eksiterer fra f¿r.


                    $dataH['AccountPlanID']     = $customernumber;
                    if (!empty($SchemeID) && in_array($SchemeIDType, $VALID_ORGNO_SCHEMES)) {
                        $dataH['OrgNumber']         = $SchemeID;
                    }
                    $dataH['AccountName']       = $InvoiceO->AccountingCustomerParty->Party->PartyName->Name;
                    $dataH['AccountPlanType']   = 'customer';

                    $dataH['Address']           = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName;
                    $dataH['City']              = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName;
                    $dataH['ZipCode']           = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone;

                    #$dataH['IAddress']          = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName;
                    #$dataH['ICity']             = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName;
                    #$dataH['IZipCode']          = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone;

                    $dataH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                    $dataH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
                    $dataH['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
                    $dataH['Active']            = 1;

                    #creditdays
                    $dataH['EnableCredit']      = 1;
                    $dataH['CreditDays']        = $_lib['date']->dateDiff($InvoiceO->PaymentMeans->PaymentDueDate, $InvoiceO->IssueDate);

                    #Credit/debit color and text
                    $dataH['debittext']         = 'Salg';
                    $dataH['credittext']        = 'Betal';
                    $dataH['DebitColor']        = 'debitblue';
                    $dataH['CreditColor']       = 'creditred';

                    $dataH['EnablePostPost']    = 1;

                    #burde kj¿rt oppslag fra brreg samtidig med denne registreringen, men vi fŒr ganske mye info fra fakturaen
                    #Kan vi sette en default motkonto som vil v¾re "grei???"
                    #Vi mŒ kopiere defaultdata fra mor kategorien til denne - mŒ sentralisere opprettelse av kontoplaner i eget objekt
                    #print_r($dataH);
                    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'action' => 'auto', 'debug' => false));
                    #exit;
                    $count++;
                } else {
                    $_lib['message']->add("Reskontro med nummer lavere enn 10.000 mŒ opprettes manuelt: " . $customernumber);
                }
            }

            if (!empty($single_invoice_id) && $InvoiceO->ID == $single_invoice_id) {
                break; // finished since we have processed the only one we wanted
            }
        }
        $_lib['message']->add("$count kontoplaner automatisk opprettet - motkonto m&aring; settes manuelt");
    }

    # TODO(mladjo2505): Remove, since it is not used anymore
    #Only for adding new suppliers at this point in time.
    public function incomingaddmissingaccountplan($single_invoice_id = false) {
        global $_lib;

        $invoicesO  = $this->incoming();
        $count      = 0;

        foreach($invoicesO->Invoice as $InvoiceO) {
            if (!empty($single_invoice_id) && $InvoiceO->ID != $single_invoice_id) {
                continue; // this is not the droid you are looking for
            }

            if (empty($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID)) {
              $_lib['message']->add("Ikke mulig &aring; auto-opprette fordi leverand&oslash;rnr mangler.");
                continue;
            }

            list($SchemeID, $SchemeIDType) = $this->extractSupplierSchemeID($InvoiceO);
            list($account, $_SchemeID)  = $this->find_reskontro($SchemeID, 'supplier', $SchemeIDType);
            if($account) {
                continue; // exists already
            } else {
                if ($SchemeIDType == 'NO:ORGNR') $AccountPlanID = $SchemeID;
                else {
                  // the first available account plan id
                  $used_accounts_hash = array_keys($_lib['storage']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "select AccountPlanID from accountplan where AccountPlanType = 'supplier' order by AccountPlanID")));
                  for ($i = 100000000, $j = 0; $i <= 999999999; $i++) if ($i != $used_accounts_hash[$j++]) break;
                  $AccountPlanID = $i;
                }
                $dataH = array();
                $dataH['AccountPlanID']     = $AccountPlanID;
                $dataH['AccountName']       = $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                $dataH['AccountPlanType']   = 'supplier';

                $dataH['Address']           = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
                $dataH['City']              = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName;
                $dataH['ZipCode']           = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;

                $dataH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                $dataH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
                $dataH['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
                $dataH['Active']            = 1;

                #creditdays
                $dataH['EnableCredit']      = 1;
                $dataH['CreditDays']        = $_lib['date']->dateDiff($InvoiceO->PaymentMeans->PaymentDueDate, $InvoiceO->IssueDate);

                #Credit/debit color and text
                $dataH['debittext']         = 'Salg';
                $dataH['credittext']        = 'Betal';
                $dataH['DebitColor']        = 'debitblue';
                $dataH['CreditColor']       = 'creditred';

                $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'action' => 'auto', 'debug' => false));
                $FakturabankScheme = $_lib['storage']->get_row(array('query' => "select FakturabankSchemeID from fakturabankscheme where SchemeType = '$SchemeIDType'"));
                $FakturabankSchemeID = $FakturabankScheme->FakturabankSchemeID;
                $schemedataH = array();
                $schemedataH['FakturabankSchemeID'] = $FakturabankSchemeID;
                $schemedataH['SchemeValue'] = $SchemeID;
                $schemedataH['AccountPlanID'] = $AccountPlanID;
                $_lib['storage']->store_record(array('data' => $schemedataH, 'table' => 'accountplanscheme', 'action' => 'auto', 'debug' => false));
                # after creating the account plan update it from fakturabank
                $this->update_accountplan_from_fakturabank($AccountPlanID);
                #exit;
                $count++;
            }

            if (!empty($single_invoice_id) && $InvoiceO->ID == $single_invoice_id) {
                break; // finished since we have processed the only one we wanted
            }
        }
        $_lib['message']->add("$count kontoplaner automatisk opprettet - motkonto m&aring; settes manuelt");
    }


    ################################################################################################
    public function registerincoming() {
        global $_lib, $accounting;

        $invoicesO        = $this->incoming();
        $conversion_rate  = 0;
        $is_foreign       = false;

        $fbvoting = new lodo_fakturabank_fakturabankvoting();

        if (!empty($invoicesO->Invoice)) {

        foreach($invoicesO->Invoice as &$InvoiceO) {
            $is_foreign = false;
            #If all essential data quality is ok - download the invoice
            if($InvoiceO->Journal) {
                $dataH = array();

                #Foreign currency
                if ($InvoiceO->DocumentCurrencyCode != exchange::getLocalCurrency()) {
                    $is_foreign = true;
                    $conversion_rate = exchange::getConversionRate($InvoiceO->DocumentCurrencyCode);
                    $dataH['TotalCustPrice']    = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, $InvoiceO->LegalMonetaryTotal->PayableAmount);
                    $dataH['ForeignCurrencyID'] = $InvoiceO->DocumentCurrencyCode;
                    $dataH['ForeignAmount']     = $InvoiceO->LegalMonetaryTotal->PayableAmount;
                    $dataH['ForeignConvRate']   = $conversion_rate;
                } else {
                    $dataH['TotalCustPrice'] = $InvoiceO->LegalMonetaryTotal->PayableAmount; #If negative this is probably a credit note
                }

                $dataH['SupplierAccountPlanID'] = $InvoiceO->AccountPlanID;
                $dataH['InvoiceNumber']         = $InvoiceO->ID;
                $dataH['ExternalID']            = $InvoiceO->FakturabankID;
                $dataH['FakturabankID']         = $InvoiceO->FakturabankID;
                $dataH['Period']                = $InvoiceO->Period;
                $dataH['InvoiceDate']           = $InvoiceO->IssueDate;
                $dataH['Department']            = $InvoiceO->Department;
                $dataH['Project']               = $InvoiceO->Project;
                $dataH['ProjectNameInternal']               = $InvoiceO->ProjectNameInternal;
                $dataH['ProjectNameCustomer']               = $InvoiceO->ProjectNameCustomer;
                $dataH['isReisegarantifond']    = $InvoiceO->Reisegarantifond;
                $dataH['VoucherType']           = 'U';

                $dataH['DueDate']               = $InvoiceO->PaymentMeans->PaymentDueDate;

                $dataH['SupplierBankAccount']   = $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID;
                $dataH['CustomerBankAccount']   = $_lib['sess']->get_companydef('BankAccount');

                $old_pattern    = array("/[^0-9]/");
                $new_pattern    = array("");
                $dataH['SupplierBankAccount'] = strtolower(preg_replace($old_pattern, $new_pattern, $dataH['SupplierBankAccount']));
                $dataH['CustomerBankAccount'] = strtolower(preg_replace($old_pattern, $new_pattern, $dataH['CustomerBankAccount']));

                if(!$dataH['SupplierBankAccount']) {
                    #If BankAccount is not given in incoming invoice, copy it from Supplier Accountplan.
                    $supplier                       = $accounting->get_accountplan_object($InvoiceO->AccountPlanID);
                    $dataH['SupplierBankAccount']   = $supplier->DomesticBankAccount;
                } else {

                    if($dataH['SupplierBankAccount']) {
                        $supplierH = array();
                        $supplierH['AccountPlanID']         = $dataH['SupplierAccountPlanID'];
                        $supplierH['DomesticBankAccount']   = $dataH['SupplierBankAccount'];
                        $_lib['storage']->store_record(array('data' => $supplierH, 'table' => 'accountplan', 'debug' => false));
                    }
                }

                $old_pattern                        = array("/[^0-9]/");
                $new_pattern                        = array("");
                $dataH['CustomerAccountPlanID']     = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));

                $dataH['InsertedByPersonID']    = $_lib['sess']->get_person('PersonID');
                $dataH['InsertedDateTime']      = $_lib['sess']->get_session('Datetime');
                $dataH['UpdatedByPersonID']     = $_lib['sess']->get_person('PersonID');

                $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
                $dataH['FakturabankDateTime']   = $_lib['sess']->get_session('Datetime');

                $dataH['Active']                = 1;
                $dataH['RemittanceStatus']      = 'recieved';
                $dataH['RemittanceAmount']      = $dataH['TotalCustPrice']; #We suggest to pay the entire invoice by default

                $dataH['IName']                  = $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                $dataH['IAddress']               = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
                $dataH['ICity']                  = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName;
                $dataH['IZipCode']               = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;

                $dataH['DName']                  = $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                $dataH['DAddress']               = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
                $dataH['DCity']                  = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName;
                $dataH['DZipCode']               = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;

                #Only real KID can be registered in the KID field
                if($InvoiceO->PaymentMeans->InstructionNote == 'KID' && $InvoiceO->PaymentMeans->InstructionID) {
                    $dataH['KID']  = $InvoiceO->PaymentMeans->InstructionID; #KID
                }
                $ID = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoicein', 'debug' => false));

                foreach($InvoiceO->InvoiceLine as $line) {

                    #print_r($line);

                    #preprocess price/quantity - because inconsistent data can appear
                    if($line->Price->BaseQuantity && $line->Price->PriceAmount) {
                        if($line->TaxTotal->TaxSubtotal[0]->TaxableAmount) {
                            if($line->Price->BaseQuantity * $line->Price->PriceAmount == $line->TaxTotal->TaxSubtotal[0]->TaxableAmount) {
                                $Quantity   = $line->Price->BaseQuantity;
                                $CustPrice  = $line->Price->PriceAmount;
                            } else {
                                $Quantity   = 1;
                                $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxableAmount;
                            }
                        } else {
                            $Quantity   = 1;
                            $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxAmount;
                        }
                    } else {
                        $Quantity   = 1;
                        $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxableAmount;
                    }

                    if($CustPrice != 0) {
                        $LineNum += 10;
                        $datalineH                      = array();
                        $datalineH['ID']                = $ID;
                        $datalineH['AccountPlanID']     = $InvoiceO->MotkontoAccountPlanID;
                        $datalineH['LineNum']           = $LineNum;
                        $datalineH['ProductName']       = $line->Item->Name;
                        $datalineH['ProductNumber']     = $line->Item->SellersItemIdentification->ID;
                        $datalineH['CarID']             = $line->Item->CarID;
                        $datalineH['Comment']           = $line->Item->Description;
                        $datalineH['QuantityOrdered']   = $Quantity;
                        $datalineH['QuantityDelivered'] = $Quantity;

                        #Foreign currency
                        if ($is_foreign) {
                            $datalineH['UnitCostPrice'] = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, $CustPrice);
                            $datalineH['ForeignCurrencyID'] = $InvoiceO->DocumentCurrencyCode;
                            $datalineH['ForeignAmount']     = (float)$CustPrice;
                            $datalineH['ForeignConvRate']   = $conversion_rate;
                        } else {
                            $datalineH['UnitCostPrice'] = $CustPrice;
                        }

                        $datalineH['UnitCustPrice'] = $datalineH['UnitCostPrice'];

                        $datalineH['UnitCostPriceCurrencyID'] = exchange::getLocalCurrency();
                        $datalineH['UnitCustPriceCurrencyID'] = exchange::getLocalCurrency();

                        $datalineH['TaxAmount']         = $line->TaxTotal->TaxSubtotal[0]->TaxAmount;
                        $datalineH['Vat']               = $line->TaxTotal->TaxSubtotal[0]->Percent;
                        #$datalineH['VatID']             = $line->Price->PriceAmount; #Denne mŒ nok mappes

                        $datalineH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                        $datalineH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
                        $datalineH['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');

                        $_lib['storage']->store_record(array('data' => $datalineH, 'table' => 'invoiceinline', 'debug' => false));
                    }
                }

                #Update fakturabank voting tables to enable lookup of lodo invoice
                #given bank transaction information, when importing transactions from bank
                $fbvoting->update_fakturabank_incoming_invoice($InvoiceO->FakturabankID, $ID, $InvoiceO->AccountPlanID);

                #Set status in fakturabank
                $comment = "Lodo PHP Invoicein ID: " . $ID . " accounted " . $_lib['sess']->get_session('Datetime');
                $this->setEvent($InvoiceO->FakturabankID, 'accounted', $comment);

            } else {
                #print "Faktura finnes: " . $InvoiceO->AccountPlanID . "', InvoiceID='" . $InvoiceO->ID . "<br>\n";
            }
        }
        }
    }

    public function registeroutgoing() {
        global $_lib;

        $invoicesO = $this->outgoing();

        foreach($invoicesO->Invoice as &$InvoiceO) {
            if($InvoiceO->Journal) {

                #Check if this invoice exists
                $query          = "select * from invoiceout where InvoiceID='" . $InvoiceO->ID . "'";
                #print "$query<br>\n";
                $invoiceexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));

                #If it does not exist - insert it into incoming invoices table - ready for remittance
                if(!$invoiceexists) {
                    $dataH = array();
                    $dataH['CustomerAccountPlanID'] = $InvoiceO->AccountPlanID;
                    $dataH['InvoiceID']             = $InvoiceO->ID;
                    $dataH['JournalID']             = $InvoiceO->ID;
                    $dataH['ExternalID']            = $InvoiceO->FakturabankID;
                    $dataH['FakturabankID']         = $InvoiceO->FakturabankID;
                    $dataH['Period']                = $InvoiceO->Period;
                    $dataH['InvoiceDate']           = $InvoiceO->IssueDate;
                    $dataH['DueDate']               = $InvoiceO->PaymentMeans->PaymentDueDate;
                    $dataH['TotalCustPrice']        = $InvoiceO->LegalMonetaryTotal->PayableAmount; #If negative this is probably a credit note
                    $dataH['InsertedByPersonID']    = $_lib['sess']->get_person('PersonID');
                    $dataH['InsertedDateTime']      = $_lib['sess']->get_session('Datetime');
                    $dataH['Active']                = 1;
                    $dataH['FromCompanyID']         = 1;
                    $dataH['SupplierAccountPlanID'] = 1;
                    $dataH['DepartmentID'] = $InvoiceO->DepartmentID;
                    $dataH['ProjectID'] = $InvoiceO->ProjectID;
                    $dataH['DepartmentCustomer'] = $InvoiceO->DepartmentCustomer;
                    $dataH['ProjectNameInternal'] = $InvoiceO->ProjectNameInternal;
                    $dataH['ProjectNameCustomer'] = $InvoiceO->ProjectNameCustomer;

                    # Sender info
                    $query                  = "select * from company where CompanyID='" . $dataH['FromCompanyID'] . "'";
                    $company                = $_lib['storage']->get_row(array('query' => $query));

                    $dataH['SName']         = empty($InvoiceO->AccountingSupplierParty->Party->PartyName->Name)                            ? $company->VName        : $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                    $dataH['SAddress']      = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName)                  ? $company->VAddress     : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
                    $dataH['SZipCode']      = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone)                  ? $company->VZipCode     : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;
                    $dataH['SCountryCode']  = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode) ? $company->VCountryCode : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode;
                    $dataH['SPhone']        = empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone)                         ? $company->Phone        : $InvoiceO->AccountingSupplierParty->Party->Contact->Telephone;
                    $dataH['SMobile']       = empty($InvoiceO->AccountingSupplierParty->Party->Contact->Mobile)                            ? $company->Mobile       : $InvoiceO->AccountingSupplierParty->Party->Contact->Mobile; // ?? check on fb?
                    $dataH['SEmail']        = empty($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail)                    ? $company->Email        : $InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail;
                    $dataH['SWeb']          = empty($InvoiceO->AccountingSupplierParty->Party->WebsiteURI)                                 ? $company->WWW          : $InvoiceO->AccountingSupplierParty->Party->WebsiteURI;

                    # Save from imported data only if correct type is sent
                    $dataH['SBankAccount']        = (!empty($InvoiceO->PaymentMeans->PayeeFinancialAccount->ID) && ($InvoiceO->PaymentMeans->PayeeFinancialAccount->Name == "Bank")) ? $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID : $company->BankAccount;
                    if ($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID == "NO:ORGNR") {
                      $dataH['SOrgNo'] = empty($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID) ? $company->OrgNumber : $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                      $dataH['SVatNo'] = $company->VatNumber;
                    }
                    else if ($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID == "NO:VAT") {
                      $dataH['SOrgNo'] = $company->OrgNumber;
                      $dataH['SVatNo'] = empty($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID) ? $company->VatNumber : $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                    }

                    $dataH['IName']                  = $InvoiceO->AccountingCustomerParty->Party->PartyName->Name;
                    $dataH['IAddress']               = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName;
                    $dataH['ICity']                  = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName;
                    $dataH['IZipCode']               = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone;

                    $dataH['DName']                  = $InvoiceO->AccountingCustomerParty->Party->PartyName->Name;
                    $dataH['DAddress']               = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName;
                    $dataH['DCity']                  = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName;
                    $dataH['DZipCode']               = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone;

                    if($InvoiceO->PaymentMeans->InstructionNote == 'KID' && $InvoiceO->PaymentMeans->InstructionID) {
                        $dataH['KID']  = $InvoiceO->PaymentMeans->InstructionID; #KID
                    }
                    $ID = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
                    #MŒ sjekke at produktnummer stemmer og matcher
                    foreach($InvoiceO->InvoiceLine as $line) {

                        #preprocess price/quantity - because inconsistent data can appear
                        if($line->Price->BaseQuantity && $line->Price->PriceAmount) {
                            if($line->TaxTotal->TaxSubtotal[0]->TaxableAmount) {
                                if($line->Price->BaseQuantity * $line->Price->PriceAmount == $line->TaxTotal->TaxSubtotal[0]->TaxableAmount) {
                                    $Quantity   = $line->Price->BaseQuantity;
                                    $CustPrice  = $line->Price->PriceAmount;
                                } else {
                                    $Quantity   = 1;
                                    $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxableAmount;
                                }
                            } else {
                                $Quantity   = 1;
                                $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxAmount;
                            }
                        } else {
                            $Quantity   = 1;
                            $CustPrice  = $line->TaxTotal->TaxSubtotal[0]->TaxableAmount;
                        }

                        if($CustPrice != 0) {
                            $LineNum += 10;
                            $datalineH                      = array();
                            $datalineH['InvoiceID']         = $InvoiceO->ID;
                            $datalineH['LineNum']           = $LineNum;
                            $datalineH['ProductID']         = $line->Item->SellersItemIdentification->ProductID;
                            $datalineH['ProductName']       = $line->Item->Name;
                            $datalineH['ProductNumber']     = $line->Item->SellersItemIdentification->ID;
                            $datalineH['Comment']           = $line->Item->Description;
                            $datalineH['QuantityOrdered']   = $Quantity;
                            $datalineH['QuantityDelivered'] = $Quantity;
                            $datalineH['UnitCostPrice']     = $CustPrice;
                            $datalineH['UnitCustPrice']     = $CustPrice;
                            $datalineH['UnitCostPriceCurrencyID'] = exchange::getLocalCurrency();
                            $datalineH['UnitCustPriceCurrencyID'] = exchange::getLocalCurrency();

                            $datalineH['TaxAmount']         = $line->TaxTotal->TaxSubtotal[0]->TaxAmount;
                            $datalineH['Vat']               = $line->TaxTotal->TaxSubtotal[0]->Percent;
                            #$datalineH['VatID']             = $line->Price->VatID; #Denne mŒ nok mappes

                            $datalineH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                            $datalineH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');

                            $_lib['storage']->store_record(array('data' => $datalineH, 'table' => 'invoiceoutline', 'debug' => false));
                        }
                    }

                    #Update fakturabank voting tables to enable lookup of lodo invoice
                    #given bank transaction information, when importing transactions from bank
                    $fbvoting = new lodo_fakturabank_fakturabankvoting();
                    $fbvoting->update_fakturabank_outgoing_invoice($InvoiceO->FakturabankID, $ID, $InvoiceO->AccountPlanID);

                    #Set status in fakturabank
                    $comment = "Lodo PHP Invoiceout ID: " . $InvoiceO->ID . " accounted " . $_lib['sess']->get_session('Datetime');
                    $this->setEvent($InvoiceO->FakturabankID, 'accounted', $comment);

                } else {
                    #print "Faktura finnes: " . $InvoiceO->AccountPlanID . "', InvoiceID='" . $InvoiceO->ID . "<br>\n";
                }
                $invoice = new invoice(array('CustomerAccountPlanID' => $dataH['CustomerAccountPlanID'], 'VoucherType' => 'S', 'InvoiceID' => $dataH['InvoiceID']));
                $invoice->init(array());
                $invoice->journal();
            }
        }
    }

    ################################################################################################
    public function hash_to_xml($InvoiceO) {
        global $_lib;

        $xml = "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . "><Invoice xmlns:qdt=\"urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2\" xmlns:ccts=\"urn:oasis:names:specification:ubl:schema:xsd:CoreComponentParameters-2\" xmlns:stat=\"urn:oasis:names:specification:ubl:schema:xsd:DocumentStatusCode-1.0\" xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\" xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\" xmlns:udt=\"urn:un:unece:uncefact:data:draft:UnqualifiedDataTypesSchemaModule:2\" xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2\"></Invoice>";

        $doc = new DOMDocument();
        $doc->formatOutput  = true;
        $doc->loadXML($xml);

        $invoices = $doc->getElementsByTagNameNS('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', 'Invoice');

        // Not an empty foreach loop! Sets up $invoice var for later use.
        foreach($invoices as $invoice) {
            #print_r($invoice);
        }

        $cbc = $doc->createElement('cbc:UBLVersionID', '2.0');
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:CustomizationID', 'urn:fakturabank.no:ubl-2.0-customizations:Invoice');
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:ProfileID', 'Invoice');
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:ID', $InvoiceO->ID);
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:IssueDate', $InvoiceO->IssueDate);
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:Note');
        $cdata = $doc->createCDATASection(utf8_encode($InvoiceO->Note)); // handle ampersand in text
        $cbc->appendChild($cdata);
        $invoice->appendChild($cbc);

        $cbc = $doc->createElement('cbc:DocumentCurrencyCode', $InvoiceO->DocumentCurrencyCode);
        $invoice->appendChild($cbc);


        /* gather data to be sent in AccountCost element */
        $acc_cost = '';
        $acc_types = array('Department', 'DepartmentCode', 'Project', 'ProjectCode', 'CustomerDepartment', 'CustomerProject');

        foreach ($acc_types as $acc_type) {
            if ($InvoiceO->$acc_type != "") {
                if (!empty($acc_cost)) {
                    $acc_cost .= ';';
                }
                $acc_cost .= strtolower($acc_type) . '=' . urlencode(utf8_encode($InvoiceO->$acc_type));
            }
        }

        if (!empty($acc_cost)) {
            $cbc = $doc->createElement('cbc:AccountingCost', utf8_encode($acc_cost));
            $invoice->appendChild($cbc);
        }

        if (!empty($InvoiceO->OrderReference)) {
            $order_reference = $doc->createElement('cac:OrderReference');

            if (!empty($InvoiceO->OrderReference->ID)) {
                $cbc = $doc->createElement('cbc:ID', utf8_encode($InvoiceO->OrderReference->ID));
                $order_reference->appendChild($cbc);
            }
            if (!empty($InvoiceO->OrderReference->SalesOrderID)) {
                $cbc = $doc->createElement('cbc:SalesOrderID', utf8_encode($InvoiceO->OrderReference->SalesOrderID));
                $order_reference->appendChild($cbc);
            }

            $invoice->appendChild($order_reference);
        }

        ############################################################################################
        #AccountingSupplierParty
        $supplier = $doc->createElement('cac:AccountingSupplierParty');
            $cacparty = $doc->createElement('cac:Party');

            $cbc = $doc->createElement('cbc:WebsiteURI', utf8_encode($InvoiceO->AccountingSupplierParty->Party->WebsiteURI));
                $cacparty->appendChild($cbc);

                $name = $doc->createElement('cac:PartyName');
                $cbc = $doc->createElement('cbc:Name');
                $cdata = $doc->createCDATASection(utf8_encode($InvoiceO->AccountingSupplierParty->Party->PartyName->Name)); // handle ampersand in company names (we should probably send all text data with cdata function)
                $cbc->appendChild($cdata);
                $name->appendChild($cbc);
                $cacparty->appendChild($name);

                $address = $doc->createElement('cac:PostalAddress', utf8_encode(''));

                    $cbc = $doc->createElement('cbc:StreetName', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName));
                    $address->appendChild($cbc);

                    $cbc = $doc->createElement('cbc:BuildingNumber', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PostalAddress->BuildingNumber));
                    $address->appendChild($cbc);

                    $cbc = $doc->createElement('cbc:CityName', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName));
                    $address->appendChild($cbc);

                    $cbc = $doc->createElement('cbc:PostalZone', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone));
                    $address->appendChild($cbc);

                    $country = $doc->createElement('cac:Country');

                        $cbc = $doc->createElement('cbc:IdentificationCode', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode));
                        $country->appendChild($cbc);

                    $address->appendChild($country);

                $cacparty->appendChild($address);

                if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID)) {
                    $partytaxscheme = $doc->createElement('cac:PartyTaxScheme');
                    $cbc = $doc->createElement('cbc:CompanyID', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID));
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID)) {
                        $cbc->setAttribute('schemeID', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID));
                    }


                    $partytaxscheme->appendChild($cbc);

                    $taxscheme = $doc->createElement('cac:TaxScheme');

                    $cbc = $doc->createElement('cbc:ID', utf8_encode('VAT'));
                    $cbc->setAttribute('schemeID', 'UN/ECE 5153');
                    $cbc->setAttribute('schemeAgencyID', '6');
                    $taxscheme->appendChild($cbc);

                    $partytaxscheme->appendChild($taxscheme);

                    $cacparty->appendChild($partytaxscheme);
                }

                $legalentity = $doc->createElement('cac:PartyLegalEntity');
                $cbc = $doc->createElement('cbc:CompanyID', utf8_encode($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID));

                $cbc->setAttribute('schemeID', 'NO:ORGNR');
                $legalentity->appendChild($cbc);

                $cacparty->appendChild($legalentity);


                if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->Fax) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->Mobile)) {
                    $contact = $doc->createElement('cac:Contact');
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone)) {
                        $cbc = $doc->createElement('cbc:Telephone', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telefax)) {
                        $cbc = $doc->createElement('cbc:Telefax', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Contact->Telefax));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail)) {
                        $cbc = $doc->createElement('cbc:ElectronicMail', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->Mobile)) {
                        $cbc = $doc->createElement('cbc:Note', utf8_encode("Mobile: " . $InvoiceO->AccountingSupplierParty->Party->Contact->Mobile));
                        $contact->appendChild($cbc);
                    }
                    $cacparty->appendChild($contact);
                }


                if (!empty($InvoiceO->AccountingSupplierParty->Party->Person)) {
                    $person = $doc->createElement('cac:Person');
                    $cbc = $doc->createElement('cbc:FirstName', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Person->FirstName));
                    $person->appendChild($cbc);
                    $cbc = $doc->createElement('cbc:FamilyName', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Person->FamilyName));
                    $person->appendChild($cbc);
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Person->MiddleName)) {
                        $cbc = $doc->createElement('cbc:MiddleName', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Person->MiddleName));
                        $person->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingSupplierParty->Party->Person->JobTitle)) {
                        $cbc = $doc->createElement('cbc:JobTitle', utf8_encode($InvoiceO->AccountingSupplierParty->Party->Person->JobTitle));
                        $person->appendChild($cbc);
                    }

                    $cacparty->appendChild($person);
                }


            $supplier->appendChild($cacparty);

        $invoice->appendChild($supplier);


        ############################################################################################
        #AccountingCustomerParty
        $customer = $doc->createElement('cac:AccountingCustomerParty');
            $cacparty = $doc->createElement('cac:Party');

            if (!empty($InvoiceO->AccountingCustomerParty->Party->WebsiteURI)) {
                $cbc = $doc->createElement('cbc:WebsiteURI', utf8_encode($InvoiceO->AccountingCustomerParty->Party->WebsiteURI));
                $cacparty->appendChild($cbc);
            }

                // Add customer nr
                $identification = $doc->createElement('cac:PartyIdentification');
                $cbc = $doc->createElement('cbc:ID', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID));
                $cbc->setAttribute('schemeID', 'NO:SUP-ACCNT-RE');
                $identification->appendChild($cbc);
                $cacparty->appendChild($identification);

                $name = $doc->createElement('cac:PartyName');
                $cbc = $doc->createElement('cbc:Name');
                $cdata = $doc->createCDATASection(utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyName->Name)); // handle ampersand in company names (we should probably send all text data with cdata function)
                //$cdata = $doc->createCDATASection(utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyName->Name)); // handle ampersand in company names (we should probably send all text data with cdata function)
                $cbc->appendChild($cdata);
                $name->appendChild($cbc);
                $cacparty->appendChild($name);

                $address = $doc->createElement('cac:PostalAddress');

                $cbc = $doc->createElement('cbc:StreetName', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName));
                $address->appendChild($cbc);

                $cbc = $doc->createElement('cbc:BuildingNumber', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PostalAddress->BuildingNumber));
                $address->appendChild($cbc);

                $cbc = $doc->createElement('cbc:CityName', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName));
                $address->appendChild($cbc);

                $cbc = $doc->createElement('cbc:PostalZone', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone));
                $address->appendChild($cbc);

                $country = $doc->createElement('cac:Country');

                $cbc = $doc->createElement('cbc:IdentificationCode', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode));
                $country->appendChild($cbc);

                $address->appendChild($country);

                $cacparty->appendChild($address);


                if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID)) {
                    $partytaxscheme = $doc->createElement('cac:PartyTaxScheme');
                    $cbc = $doc->createElement('cbc:CompanyID', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID));
                    if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID)) {
                        $cbc->setAttribute('schemeID', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID));
                    }
                    $partytaxscheme->appendChild($cbc);

                    $taxscheme = $doc->createElement('cac:TaxScheme');

                    $cbc = $doc->createElement('cbc:ID', utf8_encode('VAT'));
                    $cbc->setAttribute('schemeID', 'UN/ECE 5153');
                    $cbc->setAttribute('schemeAgencyID', '6');
                    $taxscheme->appendChild($cbc);

                    $partytaxscheme->appendChild($taxscheme);

                    $cacparty->appendChild($partytaxscheme);
                }

                if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)){
                    if (strlen(preg_replace('/[^0-9]/', '', $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)) == 9) { // has valid org nr, add it,
                        $legal_entity = $doc->createElement('cac:PartyLegalEntity');
                        $cbc = $doc->createElement('cbc:CompanyID', utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID));
                        $cbc->setAttribute('schemeID', 'NO:ORGNR');
                        $legal_entity->appendChild($cbc);

                        $cacparty->appendChild($legal_entity);
                    } else {
                        $_lib['message']->add("hash_to_xml::invalid orgnr. Organisasjonsnummeret " . $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID . " til " . $InvoiceO->AccountingCustomerParty->Party->PartyName->Name . " er ugyldig. Fakturaen ble likevel sendt med kundenr som id.");
                    }
                }


                if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->Telephone) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->Fax) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->Mobile)) {
                    $contact = $doc->createElement('cac:Contact');
                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->Telephone)) {
                        $cbc = $doc->createElement('cbc:Telephone', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Contact->Telephone));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->Telefax)) {
                        $cbc = $doc->createElement('cbc:Telefax', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Contact->Telefax));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail)) {
                        $cbc = $doc->createElement('cbc:ElectronicMail', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail));
                        $contact->appendChild($cbc);
                    }
                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->Mobile)) {
                        $cbc = $doc->createElement('cbc:Note', utf8_encode('Mobile: ' . $InvoiceO->AccountingCustomerParty->Party->Contact->Mobile));
                        $contact->appendChild($cbc);
                    }

                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->BankAccount)) {
                        $cbc = $doc->createElement('cbc:BankAccount', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Contact->BankAccount));
                        $cbc->setAttribute('schemeID', 'BBAN'); // use IBAN if IBAN, BBAN or BANK is unclassified account
                        $contact->appendChild($cbc);
                    }

                    $cacparty->appendChild($contact);


                    if (!empty($InvoiceO->AccountingCustomerParty->Party->Person)) {
                        $person = $doc->createElement('cac:Person');
                        $cbc = $doc->createElement('cbc:FirstName', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Person->FirstName));
                        $person->appendChild($cbc);
                        $cbc = $doc->createElement('cbc:FamilyName', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Person->FamilyName));
                        $person->appendChild($cbc);
                        if (!empty($InvoiceO->AccountingCustomerParty->Party->Person->MiddleName)) {
                            $cbc = $doc->createElement('cbc:MiddleName', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Person->MiddleName));
                            $person->appendChild($cbc);
                        }
                        if (!empty($InvoiceO->AccountingCustomerParty->Party->Person->JobTitle)) {
                            $cbc = $doc->createElement('cbc:JobTitle', utf8_encode($InvoiceO->AccountingCustomerParty->Party->Person->JobTitle));
                            $person->appendChild($cbc);
                        }

                        $cacparty->appendChild($person);
                    }

                }


            $customer->appendChild($cacparty);

        $invoice->appendChild($customer);

        // Delivery (DeliveryAddress)
        $delivery = $doc->createElement('cac:Delivery');

        $cacaddress = $doc->createElement('cac:DeliveryAddress');

        $cbc = $doc->createElement('cbc:StreetName', utf8_encode($InvoiceO->DeliveryAddress->Address));
        $cacaddress->appendChild($cbc);

        $cbc = $doc->createElement('cbc:CityName', utf8_encode($InvoiceO->DeliveryAddress->City));
        $cacaddress->appendChild($cbc);

        $cbc = $doc->createElement('cbc:PostalZone', utf8_encode($InvoiceO->DeliveryAddress->ZipCode));
        $cacaddress->appendChild($cbc);

        $cbcaddressline = $doc->createElement('cac:AddressLine');
        $cbc = $doc->createElement('cbc:Line', utf8_encode($InvoiceO->DeliveryAddress->Address));
        $cbcaddressline->appendChild($cbc);
        $cacaddress->appendChild($cbcaddressline);

        $cbccountry = $doc->createElement('cac:Country');
        $cbc = $doc->createElement('cbc:IdentificationCode', utf8_encode($InvoiceO->DeliveryAddress->CountryCode));
        $cbc->setAttribute('listID', 'ISO3166-1:Alpha2');
        $cbccountry->appendChild($cbc);
        $cacaddress->appendChild($cbccountry);

        $delivery->appendChild($cacaddress);

        $invoice->appendChild($delivery);


        ############################################################################################
        $paymentmeans = $doc->createElement('cac:PaymentMeans');

        $cbc = $doc->createElement('cbc:PaymentMeansCode', utf8_encode($InvoiceO->PaymentMeans->PaymentMeansCode));
            $cbc->setAttribute('listSchemeURI', 'urn:www.nesubl.eu:codelist:gc:PaymentMeansCode:2007.1');
            $cbc->setAttribute('listID', 'Payment Means');
            $paymentmeans->appendChild($cbc);

            $cbc = $doc->createElement('cbc:PaymentDueDate', utf8_encode($InvoiceO->PaymentMeans->PaymentDueDate));
            $paymentmeans->appendChild($cbc);

            #KID number
            $cbc = $doc->createElement('cbc:InstructionID', utf8_encode($InvoiceO->PaymentMeans->InstructionID));
            $paymentmeans->appendChild($cbc);

            #KID (text)
            $cbc = $doc->createElement('cbc:InstructionNote', utf8_encode($InvoiceO->PaymentMeans->InstructionNote));
            $paymentmeans->appendChild($cbc);

            if (!empty($InvoiceO->PaymentMeans->PayerFinancialAccount)) {
                $payerfinancialaccount = $doc->createElement('cac:PayerFinancialAccount');

                $cbc = $doc->createElement('cbc:ID', utf8_encode($InvoiceO->PaymentMeans->PayerFinancialAccount->ID));
                $cbc->setAttribute('schemeID', 'BBAN');
                $payerfinancialaccount->appendChild($cbc);

                $cbc = $doc->createElement('cbc:Name', utf8_encode($InvoiceO->PaymentMeans->PayerFinancialAccount->Name));
                $payerfinancialaccount->appendChild($cbc);

                $paymentmeans->appendChild($payerfinancialaccount);
            }

            $financialaccount = $doc->createElement('cac:PayeeFinancialAccount');

            $cbc = $doc->createElement('cbc:ID', utf8_encode($InvoiceO->PaymentMeans->PayeeFinancialAccount->ID));
                $cbc->setAttribute('schemeID', 'BBAN');
                $financialaccount->appendChild($cbc);

                $cbc = $doc->createElement('cbc:Name', utf8_encode($InvoiceO->PaymentMeans->PayeeFinancialAccount->Name));
                $financialaccount->appendChild($cbc);

            $paymentmeans->appendChild($financialaccount);


        $invoice->appendChild($paymentmeans);

        ############################################################################################
        #TaxTotal
        $tax = $doc->createElement('cac:TaxTotal');

            $cbc = $doc->createElement('cbc:TaxAmount', utf8_encode($InvoiceO->TaxTotal['TaxAmount']));
            $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
            $tax->appendChild($cbc);

        #print_r($invoiceH);
        #print "<h1>TAX hash</h1>";
        #print_r($invoiceH['TaxTotal']);

        if(is_array($InvoiceO->TaxTotal)) {
            foreach($InvoiceO->TaxTotal as $VatPercent => $Vat) {
                if(is_numeric($VatPercent)) {
                    $subtotal = $doc->createElement('cac:TaxSubtotal');
                    $cbc = $doc->createElement('cbc:TaxableAmount', utf8_encode($Vat->TaxSubtotal->TaxableAmount));
                        $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                        $subtotal->appendChild($cbc);

                        $cbc = $doc->createElement('cbc:TaxAmount', utf8_encode($Vat->TaxSubtotal->TaxAmount));
                        $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                        $subtotal->appendChild($cbc);

                        $category = $doc->createElement('cac:TaxCategory');

                        $cbc = $doc->createElement('cbc:ID', utf8_encode($Vat->TaxSubtotal->TaxCategory->ID));
                            $category->appendChild($cbc);

                            $cbc = $doc->createElement('cbc:Percent', utf8_encode($Vat->TaxSubtotal->TaxCategory->Percent));
                            $category->appendChild($cbc);

                            $scheme = $doc->createElement('cac:TaxScheme');

                            $cbc = $doc->createElement('cbc:ID', utf8_encode($Vat->TaxSubtotal->TaxCategory->TaxScheme->ID));
                            $scheme->appendChild($cbc);

                            $category->appendChild($scheme);

                        $subtotal->appendChild($category);

                    $tax->appendChild($subtotal);
                }
            }
            $invoice->appendChild($tax);
        } else {
            print "TAX info mangler<br>\n";
        }

        ############################################################################################
        #LegalMonetaryTotal
        $monetary = $doc->createElement('cac:LegalMonetaryTotal');

        $cbc = $doc->createElement('cbc:TaxExclusiveAmount', utf8_encode($InvoiceO->LegalMonetaryTotal->TaxExclusiveAmount));
            $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
            $monetary->appendChild($cbc);

            $cbc = $doc->createElement('cbc:PayableAmount', utf8_encode($InvoiceO->LegalMonetaryTotal->PayableAmount));
            $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
            $monetary->appendChild($cbc);

        $invoice->appendChild($monetary);

        ############################################################################################
        #InvoiceLine (loop)
        if(count($InvoiceO->InvoiceLine)) {
            foreach($InvoiceO->InvoiceLine as $id => $line) {

                $invoiceline = $doc->createElement('cac:InvoiceLine');

                    $cbc = $doc->createElement('cbc:ID', utf8_encode($line->ID));
                    $invoiceline->appendChild($cbc);

                    $cbc = $doc->createElement('cbc:LineExtensionAmount', utf8_encode($line->LineExtensionAmount));
                    $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                    $invoiceline->appendChild($cbc);

                    $total = $doc->createElement('cac:TaxTotal');

                        $cbc = $doc->createElement('cbc:TaxAmount', utf8_encode($line->TaxTotal->TaxAmount));
                        $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);

                        $total->appendChild($cbc);

                        $subtotal = $doc->createElement('cac:TaxSubtotal');
                            $cbc  = $doc->createElement('cbc:TaxableAmount', utf8_encode($line->TaxTotal->TaxSubtotal->TaxableAmount));
                            $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                            $subtotal->appendChild($cbc);
                            $cbc  = $doc->createElement('cbc:TaxAmount', utf8_encode($line->TaxTotal->TaxSubtotal->TaxAmount));
                            $subtotal->appendChild($cbc);
                            $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                            $cbc  = $doc->createElement('cbc:Percent', utf8_encode($line->TaxTotal->TaxSubtotal->Percent));
                            $subtotal->appendChild($cbc);

                            $taxcategory = $doc->createElement('cac:TaxCategory');
                                $taxscheme = $doc->createElement('cac:TaxScheme');
                                    $cbc = $doc->createElement('cbc:ID', utf8_encode($line->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->ID));
                                    $taxscheme->appendChild($cbc);

                                $taxcategory->appendChild($taxscheme);

                            $subtotal->appendChild($taxcategory);

                        $total->appendChild($subtotal);

                    $invoiceline->appendChild($total);

                    $item = $doc->createElement('cac:Item');

                        if($line->Item->Description) {
                            $cbc = $doc->createElement('cbc:Description', utf8_encode($line->Item->Description));
                            $item->appendChild($cbc);
                        }

                        $cbc = $doc->createElement('cbc:Name', utf8_encode($line->Item->Name));
                        $item->appendChild($cbc);

                        #Productnumber
                        $SellersItemIdentification = $doc->createElement('cac:SellersItemIdentification');
                            $cbc = $doc->createElement('cbc:ID', utf8_encode($line->Item->SellersItemIdentification->ID));
                            $SellersItemIdentification->appendChild($cbc);
                        $item->appendChild($SellersItemIdentification);

                        #Add UNSPSC
                        if($line->Item->CommodityClassification->UNSPSC->ItemClassificationCode) {
                            $CommodityClassification = $doc->createElement('cac:CommodityClassification');

                                $ItemClassificationCode = $doc->createElement('cbc:ItemClassificationCode', utf8_encode($line->Item->CommodityClassification->UNSPSC->ItemClassificationCode));
                                $ItemClassificationCode->setAttribute('listName', 'UNSPSC');
                                $ItemClassificationCode->setAttribute('listVersionID', '7.0401');

                                $CommodityClassification->appendChild($ItemClassificationCode);
                            $item->appendChild($CommodityClassification);
                        }

                    $invoiceline->appendChild($item);

                    #Price
                    $price = $doc->createElement('cac:Price');

                        $cbc = $doc->createElement('cbc:PriceAmount', utf8_encode($line->Price->PriceAmount));
                        $cbc->setAttribute('currencyID', $InvoiceO->DocumentCurrencyCode);
                        $price->appendChild($cbc);

                        $cbc = $doc->createElement('cbc:BaseQuantity', utf8_encode($line->Price->BaseQuantity));
                        $cbc->setAttribute('unitCode', 'HUR');
                        $price->appendChild($cbc);

                    $invoiceline->appendChild($price);

                $invoice->appendChild($invoiceline);
            }
        } else {
            $_lib['message']->add('ERROR: Ingen fakturalinjer funnet');
        }

        ############################################################################################
        $xml = $doc->saveXML();
        return $xml;
    }

    ####################################################################################################
    #WRITE XML
    function write($InvoiceO) {
        global $_lib;

        #$_lib['message']->add("FB->write()");

        #print_r($InvoiceH);

        $xml = $this->hash_to_xml($InvoiceO);

        #$_lib['message']->add("FB->write1()");

        #print "<br>\n<br>\n$xml\n<br>\n<br>";

        $page = "/invoices";
        $url  = "$this->protocol://$this->host$page";

        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"run\"",
            "Content-length: ".strlen($xml),
            "Authorization: Basic " . base64_encode($this->credentials)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

        // Apply the XML to our curl call
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        $data = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($data, $header_size);
        #$_lib['message']->add("FB->write()->exec()");

        if (curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 201) {
            if (curl_errno($ch)) $_lib['message']->add("Error: opprette faktura: " . curl_error($ch));
            else $_lib['message']->add("Error: opprette faktura: " . $body);
        } else {
            // Show me the result
            $_lib['message']->add(microtime() . " Opprettet faktura: $i");
            $_lib['message']->add("<pre>$body</pre>");
            #print_r(curl_getinfo($ch));
            $this->success  = true;
        }

        curl_close($ch);
        return $this->success;
    }

    public function updateCarFromFakturabank($CarCode, $CarID) {
      global $_lib;

      if(!$this->login) return false;

      $page = "rest/cars.xml?orgno=". $this->OrgNumber ."&code=". $CarCode;
      $url = $this->construct_fakturabank_url($page);

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

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
      curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

      $result = curl_exec($ch);
      includelogic('xmldomtoobject/xmldomtoobject');
      $domtoobject = new empatix_framework_logic_xmldomtoobject(array());
      $car = $domtoobject->convert($result);

      $xml_key_to_db_fieled_name = array(
        "name" => "CarName",
        "modelyear" => "RegistrationYear",
        "purchased-date" => "ValidFrom",
        "sold-date" => "ValidTo"
      );
      if (!$car->error) {
        $car_update_query = "UPDATE car SET CarCode = '". $car->code ."'";
        foreach($xml_key_to_db_fieled_name as $xml_key => $db_filed_name) {
          if ($car->{$xml_key}) $car_update_query .= ", ". $db_filed_name ." = '". $car->{$xml_key} ."'";
        }
        $car_update_query .= " WHERE CarID = ". $CarID;
        $_lib['db']->db_query($car_update_query);
      }
      else $_lib['message']->add("ERROR: ". $car->error);
    }

    public function construct_fakturabank_url($page=''){
      return "$this->protocol://$this->host/$page";
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
