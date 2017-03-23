<?
#should split to factory pattern with incoming and outgoing invoices.
includelogic('invoice/invoice');
includelogic('fakturabank/fakturabankvoting');
includelogic('exchange/exchange');
# oauth
includelogic('oauth/oauth');
# needed for updating unit from fakturabank
includelogic('orgnumberlookup/orgnumberlookup');
includelogic("accountplan/scheme");

class lodo_fakturabank_fakturabank {
    private $host           = '';
    private $protocol       = '';
    private $timeout        = 30;
    private $OrgNumber      = '';
    public  $startexectime  = '';
    public  $stopexectime   = '';
    public  $diffexectime   = '';
    public  $error          = '';
    public  $success        = false;
    private $ArrayTag       = array(
                                 'Invoice'                     => true,
                                 'CreditNote'                  => true,
                                 'AdditionalDocumentReference' => true,
                                 'AllowanceCharge'             => true,
                                 'InvoiceLine'                 => true,
                                 'CreditNoteLine'              => true,
                                 'TaxSubtotal'                 => true
                                );
    private $attributesOfInterest = array(
									 'schemeID',
									 'currencyID'
								 );

    function __construct() {
        global $_lib;
        $this->startexectime  = microtime();

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        if(is_array($args)) {
            foreach($args as $key => $value) {
                $this->{$key} = $value;
            }
        }

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber= strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));

    }

    function __destruct() {
        $this->stopexectime   = microtime();
        $this->diffexectime   = $this->stopexectime - $this->startexectime;
    }

    ####################################################################################################
    #Get a list of all outgoing invoices from fakturabank
    public function outgoing() {
        global $_lib, $_SETUP;
        #https://fakturabank.no/invoices.xml?orgnr=981951271

        $page       = "rest/invoices.xml";

        $params     = "?rows=200&orgnr=$this->OrgNumber"; // add top limit rows=200, otherwise we only get one record
        $params     .= "&supplier_status=" . $_SETUP['FB_INVOICE_DOWNLOAD_STATUS'];
        $params     .= "&order=invoiceno&sord=asc&type=outgoing&lodo=true";

        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        if (isset($_SESSION['oauth_invoices_fetched'])) {
          $data = $_SESSION['oauth_resource']['result'];
        }
        else {
          $_SESSION['oauth_action'] = 'outgoing_invoices';
          $oauth_client = new lodo_oauth();
          $data = $oauth_client->get_resources($url);
        }
        $invoicesO = $this->retrieve($data);
        $validated_invoices = $this->validate_outgoing($invoicesO);
        $this->save_outgoing($validated_invoices);
        return $validated_invoices;
    }

    ####################################################################################################
    #Get a list of all incoming invoices from fakturabank
    public function incoming() {
        global $_lib, $_SETUP;
        #https://fakturabank.no/invoices?orgnr=981951271

        $page       = "rest/invoices.xml";
        $params     = "?rows=200&orgnr=" . $this->OrgNumber . '&order=issue_date&sord=asc&type=incoming&lodo=true'; // add top limit rows=1000, otherwise we only get one record
        $params    .= '&customer_status=' . $_SETUP['FB_INVOICE_DOWNLOAD_STATUS'];
        $url    = "$this->protocol://$this->host/$page$params";
        $_lib['message']->add($url);

        if (isset($_SESSION['oauth_invoices_fetched'])) {
          $data = $_SESSION['oauth_resource']['result'];
        }
        else {
          $_SESSION['oauth_action'] = 'incoming_invoices';
          $oauth_client = new lodo_oauth();
          $data = $oauth_client->get_resources($url);
        }
        $invoicesO = $this->retrieve($data);
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

    private function find_car_by_code($CarCode) {
        global $_lib;

        $query = "SELECT `CarID`, `CarCode` FROM car WHERE `CarCode` = '$CarCode'";
        $result = $_lib['storage']->db_query3(array('query' => $query));
        if (!$result) {
            return false;
        }
        if ($obj = $_lib['storage']->db_fetch_object($result)) {
            return $obj->CarID;
        } else {
            return false;
        }
    }

    private function find_department_by_id($DepartmentID) {
        global $_lib;

        if (!is_numeric($DepartmentID)) {
            return false;
        }

        $query = "SELECT `DepartmentID` FROM department WHERE `DepartmentID` = '$DepartmentID'";
		$result = $_lib['storage']->db_query3(array('query' => $query));
		if (!$result) {
			return false;
		}
		if ($obj = $_lib['storage']->db_fetch_object($result)) {
			return $DepartmentID;
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
            if($invoice->PaymentMeans->PaymentID) {
                $dataH['KID']  = $invoice->PaymentMeans->PaymentID; #KID
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
			if($invoice->PaymentMeans->PaymentID) {
				$dataH['KID']  = $invoice->PaymentMeans->PaymentID; #KID
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
    private function retrieve($xml_data) {
        global $_lib;

        $size = strlen($xml_data);
        $xml_std_header = '<?xml version="1.0"?>';
        $std_hd_len = strlen($xml_std_header);
        if (substr($xml_data, 0, $std_hd_len) == $xml_std_header) {
          $xml_data = trim(substr($xml_data, $std_hd_len));
        }
        if(substr($xml_data,0,9) != '<Invoices') {
            $_lib['message']->add($xml_data);
            if ($_SESSION['oauth_resource']['code'] == 403) $_lib['message']->add("Utilstrekkelige rettigheter i fakturabank!");
        } else {
            if($size) {
                includelogic('xmldomtoobject/xmldomtoobject');
                $domtoobject = new empatix_framework_logic_xmldomtoobject(array('arrayTags' => $this->ArrayTag, 'attributesOfInterest' => $this->attributesOfInterest));
                $invoiceO    = $domtoobject->convert($xml_data);
            } else {
                $_lib['message']->add("XML Dokument tomt - pr&oslash;v igjen: $url");
            }
        }

        return $invoiceO;
    }

    ################################################################################################
    # Sets the given status and comment on an invoice in Fakturabank with a given internal FakturabankID
    # input: id (FakturabankI internal ID, status[registered], comment (without & signs)
    # outoupt: changed status event in Fakturabank
    # Used to set an indicator in fakturaBank that an invoice has been bookkept in LODO
    private function setEvents($events) {
        global $_lib;
        $retstatus = true;

        if (empty($events)) return true;
        ############################################################################################
        #Make Event XML
        $dom            = new DOMDocument( "1.0", "UTF-8" );
        $dom_events     = $dom->createElement('events');
        foreach ($events as $event) {
          $dom_event      = $dom->createElement('event');
          $dom_id         = $dom->createElement('id', $event['id']);
          $dom_name       = $dom->createElement('name', $event['status']);
          $dom_comment    = $dom->createElement('comment', $event['comment']);

          $dom_event->appendChild($dom_id);
          $dom_event->appendChild($dom_name);
          $dom_event->appendChild($dom_comment);

          $dom_events->appendChild($dom_event);
        }
        $dom_company    = $dom->createElement('company');
        $dom_identifier = $dom->createElement('identifier', $this->OrgNumber);
        $dom_company->appendChild($dom_identifier);
        $dom_events->appendChild($dom_company);
        $dom->appendChild($dom_events);
        $xml = $dom->saveXML();

        ############################################################################################
        #Set event status on fakturabank server
        $page   = "rest/statuses.xml";
        $url    = "$this->protocol://$this->host/$page";

        $client = new lodo_oauth();
        $_SESSION['oauth_action'] = 'set_invoice_statuses';
        $client->post_resources($url, array('xml' => $xml));
        return true;
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
                    # Invoice has an error in department code (departmentcode __ must be empty or a number for outgoing invoices)
                    $InvoiceO->Status     .= "Faktura har feil i avdelingskode (avdelingskode " . $acc_cost_params['departmentcode'] . " m&aring; v&aelig;re tom eller et nummer for utg&aring;nde fakturaer)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($this->find_department_by_id($acc_cost_params['departmentcode']))) {
                    $InvoiceO->DepartmentID = $DepartmentID;
                } else {
                    # Internal department for code __ not found (departmentcode does not match any internal departments)
                    $InvoiceO->Status     .= "Fant ikke intern avdeling for kode " . $DepartmentID . " (avdelingskode er ikke lik noen intern avdeling)";
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
                    # Invoice has an error in project code (projectcode must be empty or a number for outgoing invoices)
                    $InvoiceO->Status     .= "Faktura har feil i prosjektkode (prosjektkode m&aring; v&aelig;re tom eller et nummer for utg&aring;nde fakturaer)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($ProjectID = $this->find_project_by_id($acc_cost_params['projectcode']))) {
                    $InvoiceO->ProjectID = $ProjectID;
                } else {
                    # Customer project for code __ not found (projectcode does not match any internal projects)
                    $InvoiceO->Status     .= "Fant ikke kundens prosjekt for kode $ProjectID (prosjektkode er ikke lik noen intern prosjekt)";
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

            # get fakturabankinvoiceout id
			if ($LodoID = $this->get_fakturabankoutgoinginvoice($InvoiceO->FakturabankID)) {
				$InvoiceO->LodoID = $LodoID;
			} else {
				$InvoiceO->LodoID = null;
			}

            if (empty($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID)) {
                # Invoice missing customer number
                $InvoiceO->Status     .= "Faktura mangler kundenummer";
                $InvoiceO->Journal     = false;
                $InvoiceO->Class       = 'red';

                continue;
            }

            $firma_id_value = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;
            $firma_id_type = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID_Attr_schemeID;
            $country = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode;
            $customernumber = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->CustomerNumber;

            if (!$this->extractOutgoingAccountingCost($InvoiceO)) {
                continue;
            }

            #Should this be more restricted in time or period to eliminate false searches? Any other method to limit it to only look in the correct records? No?
            $accounts = $this->find_customer_reskontros_by_firma_id($firma_id_value, $firma_id_type, $country);
            if (count($accounts) > 0) {
                if (count($accounts) == 1) {
                  $account = $accounts[0];
                  if ($account->AccountPlanID != $customernumber) {
                    // customer number different
                    $EditCustomerAccountLink = sprintf(
                      '<a href="%s&t=accountplan.reskontro&accountplan.AccountPlanID=%s&accountplan_type=customer" target="_blank">%s</a>',
                      $_lib['sess']->dispatch,
                      $account->AccountPlanID,
                      $account->AccountPlanID
                    );
                    $InvoiceO->Status .= "Kundenummer i fakturabank($customernumber) og lodo($EditCustomerAccountLink) er ulike; ";
                  }
                } elseif (count($accounts) > 1) {
                  $match_account_index = -1;
                  $accounts_links = array();
                  for($i = 0; $i < count($accounts); $i++) {
                    $account = $accounts[$i];
                    $EditCustomerAccountLink = sprintf(
                      '<a href="%s&t=accountplan.reskontro&accountplan.AccountPlanID=%s&accountplan_type=customer" target="_blank">%s</a>',
                      $_lib['sess']->dispatch,
                      $account->AccountPlanID,
                      $account->AccountPlanID
                    );
                    $accounts_links[$i] = $EditCustomerAccountLink;
                    if ($account->AccountPlanID == $customernumber) {
                      $match_account_index = $i;
                    }
                  }
                  if ($match_account_index >= 0) {
                    $account = $accounts[$match_account_index];
                    $account_link = $accounts_links[$match_account_index];
                    $InvoiceO->Status .= "Kundenummer i fakturabank($customernumber) og lodo($account_link) er like, fant flere match(" . implode(', ', $accounts_links) . "); ";
                  } else {
                    $InvoiceO->Status .= "Kundenummer i fakturabank($customernumber) og lodo er ulike, fant flere men ingen match(" . implode(', ', $accounts_links) . "); ";
                  }
                }
                $InvoiceO->AccountPlanID = $account->AccountPlanID;

                if(!$accounting->is_valid_accountperiod($InvoiceO->Period, $_lib['sess']->get_person('AccessLevel'))) {
                    #Find last and first open period could have been an own accountperiod object.

                    $PeriodOld         = $InvoiceO->Period;
                    $InvoiceO->Period  = $accounting->get_first_open_accountingperiod($PeriodOld);
                    # The period __ is locked, changing to __
                    $InvoiceO->Status .= 'Perioden ' . $PeriodOld . ' er lukket endrer til ' . $InvoiceO->Period . '. ';
                }

                // We are changing an incomig EHF CreditNote to a negative invoice so this amount needs to be
                // negated to be saved in LODO correctly as a negative invoice
                if ($InvoiceO->CreditNote) {
                    $InvoiceO->LegalMonetaryTotal->PayableAmount = -$InvoiceO->LegalMonetaryTotal->PayableAmount;
                    $InvoiceO->TaxTotal->TaxAmount = -$InvoiceO->TaxTotal->TaxAmount;
                    if(!empty($InvoiceO->AllowanceCharge)) {
                        foreach ($InvoiceO->AllowanceCharge as &$allowance_charge) {
                            $allowance_charge->Amount = -$allowance_charge->Amount;
                            unset($allowance_charge); // because of problem with references
                        }
                    }
                }

                #Check that we have not journaled the same invoices earlier.
                #JournalID = Invoice number on outgoing invoices.
                $query          = "select * from invoiceout where InvoiceID='" . $InvoiceO->ID . "'";
                #print "$query<br>\n";
                $voucherexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($voucherexists) {
                    # Invoice is downloaded
                    $InvoiceO->Status     .= "Faktura er lastet ned";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Journaled   = true;
                    $InvoiceO->Class       = 'green';
                } else {

                    foreach($InvoiceO->InvoiceLine as &$line) {

                        if($line->LineExtensionAmount != 0) {
                            // We are changing an incomig EHF CreditNote to a negative invoice so these amounts need to be
                            // negated to be saved in LODO correctly as a negative invoice
                            if ($InvoiceO->CreditNote) {
                              $line->InvoicedQuantity = -$line->InvoicedQuantity;
                              $line->LineExtensionAmount = -$line->LineExtensionAmount;
                              $line->TaxTotal->TaxAmount = -$line->TaxTotal->TaxAmount;
                              if(!empty($line->AllowanceCharge)) {
                                  foreach ($line->AllowanceCharge as &$allowance_charge) {
                                    $allowance_charge->Amount = -$allowance_charge->Amount;
                                    unset($allowance_charge); // because of problem with references
                                  }
                              }
                              if(!empty($line->Price->AllowanceCharge)) {
                                  foreach ($line->Price->AllowanceCharge as &$allowance_charge) {
                                    $allowance_charge->Amount = -$allowance_charge->Amount;
                                    unset($allowance_charge); // because of problem with references
                                  }
                              }
                            }
                            #It has to be an amount to be checked - all zero lines will not be imported later.
                            $query          = "select * from product where ProductNumber='" . $line->Item->SellersItemIdentification->ID . "' and Active=1";
                            #print "$query<br>\n";
                            $productexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                            if($productexists) {
                                if($productexists->AccountPlanID) {
                                    $line->Item->SellersItemIdentification->ProductID = $productexists->ProductID;
                                } else {
                                    # Account not set on product: __
                                    $InvoiceO->Status     .= "Konto ikke satt p&aring; produkt: " . $line->Item->SellersItemIdentification->ID;
                                    $InvoiceO->Journal     = false;
                                    $InvoiceO->Class       = 'red';
                                }
                            } else {
                                # Product number: __ does not exist.
                                $InvoiceO->Status     .= "Produktnr: " . $line->Item->SellersItemIdentification->ID . " eksisterer ikke. ";
                                $InvoiceO->Journal     = false;
                                $InvoiceO->Class       = 'red';
                            }
                        }
                        #We could have auto create of product as well...
                    }

                    if (!empty($InvoiceO->AllowanceCharge)) {
                      foreach ($InvoiceO->AllowanceCharge as $allowance_charge) {
                        $query = "select * from allowancecharge where ChargeIndicator = " . $allowance_charge->ChargeIndicator . " and lower(Reason) = lower('" . $allowance_charge->AllowanceChargeReason . "') and Active = 1";
                        $allowance_charge_exists = $_lib['db']->get_row(array('query' => $query, 'debug' => false));
                        if(!$allowance_charge_exists->AllowanceChargeID) {
                          if ($allowance_charge->ChargeIndicator == 'true') {
                            $InvoiceO->Status     .= "Kostnad: '" . $allowance_charge->AllowanceChargeReason . "' eksisterer ikke. ";
                          } else {
                            $InvoiceO->Status     .= "Rabatt: '" . $allowance_charge->AllowanceChargeReason . "' eksisterer ikke. ";
                          }
                          $InvoiceO->Journal     = false;
                          $InvoiceO->Class       = 'red';
                        }
                      }
                    }
                }

                if($InvoiceO->Journal) {
                    # Ready to bookkept based on firma id
                    $InvoiceO->Status   .= "Klar til bilagsf&oslash;ring basert p&aring: Firma ID";
                }
            } else {
                # Could not find customer based on firma id __
                $InvoiceO->Status     .= "Finner ikke kunde basert p&aring; Firma ID: " . $firma_id_type . " " . $firma_id_value . ". ";
                $account_by_customernumber = self::find_customer_reskontro_by_customernumber($customernumber);
                if (!$account_by_customernumber) {
                  # Create on customer number
                  $msg = "Opprett p&aring; kundenr($customernumber)";
                  $InvoiceO->Status .= sprintf('<a href="#" onclick="javascript:addsingleaccountplan(\'%s\'); return false;">%s</a>', $InvoiceO->ID, $msg);
                  $InvoiceO->Journal = false;
                  $InvoiceO->Class   = 'red';
                } else {
                  # Edit customer with that customer number
                  $msg = "Rediger kunde med kundenr($customernumber)";
                  $EditCustomerAccountLink = sprintf(
                    '<a href="%s&t=accountplan.reskontro&accountplan.AccountPlanID=%s&accountplan_type=customer" target="_blank">%s</a>',
                    $_lib['sess']->dispatch,
                    $customernumber,
                    $msg
                  );
                  $InvoiceO->Status .= $EditCustomerAccountLink;
                  $InvoiceO->Journal = false;
                  $InvoiceO->Class   = 'red';
                }
            }
        }
        return $invoicesO;
    }

    private function extractLineAccountingCost(&$line, &$InvoiceO) {
      global $_lib;
      includelogic("car/car");
      if ($line->AccountingCost) {
        $acc_cost_params = explode(";", $line->AccountingCost);
        foreach($acc_cost_params as $param) {
          list($key, $value) = explode("=", $param);
          if ($key == 'Bil') { // CarID info
            $query = "select * from car where CarCode='" . $value . "' and ". car::car_active_sql("car.CarID", $InvoiceO->IssueDate) ."=1";
            $carexists = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
            if($carexists) {
              $line->CarID   = $carexists->CarID;
              $line->CarCode = $carexists->CarCode;
            }
            else {
              $InvoiceO->Status .= "Bil: " . $value . " eksisterer ikke. ";
              $InvoiceO->Journal = false;
              $InvoiceO->Class   = 'red';
            }
          }
          if ($key == 'Avd') { // DepartmentID info
            $query = "select * from department where DepartmentID='" . $value . "'";
            $department_exists = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
            if($department_exists) {
              $line->DepartmentID = $department_exists->DepartmentID;
            }
            else {
              $InvoiceO->Status .= "Avdeling: " . $value . " eksisterer ikke. ";
              $InvoiceO->Journal = false;
              $InvoiceO->Class   = 'red';
            }
          }
          if ($key == 'Prosj') { // ProjectID info
            $query = "select * from project where ProjectID='" . $value . "'";
            $project_exists = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
            if($project_exists) {
              $line->ProjectID = $project_exists->ProjectID;
            }
            else {
              $InvoiceO->Status .= "Prosjekt: " . $value . " eksisterer ikke. ";
              $InvoiceO->Journal = false;
              $InvoiceO->Class   = 'red';
            }
          }
        }
      }
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
                    # Invoice has an error in department code (customer departmentcode must be empty or a number for incoming invoices)
                    $InvoiceO->Status     .= "Faktura har feil i avdelingskode (kunde avdelingskode m&aring; v&aelig;re tom eller et nummer for ing&aring;nde fakturaer)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($DepartmentID = $this->find_department_by_id($acc_cost_params['customerdepartmentcode']))) {
                    $InvoiceO->DepartmentID = $DepartmentID;
                } else {
                    # Internal department for code __ not found (customer departmentcode does not match any internal departments)
                    $InvoiceO->Status     .= "Fant ikke intern avdeling for kode $DepartmentID (avdelingskode er ikke lik noen intern avdeling)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
            }

            if (($CarCode = $acc_cost_params['carcode']) != '') {
                if ($CarID = $this->find_car_by_code($CarCode)) {
                    $InvoiceO->CarID = $CarID;
                } else {
                    $InvoiceO->Status     .= "Fant ikke intern bil for kode " . $CarCode . " (carcode does not match any internal cars)";
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
                    # Invoice has an error in project code (customer projectcode must be empty or a number for incoming invoices)
                    $InvoiceO->Status     .= "Faktura har feil i prosjektkode (kunde prosjektkode m&aring; v&aelig;re tom eller et nummer for ing&aring;nde fakturaer)";
                    $InvoiceO->Journal     = false;
                    $InvoiceO->Class       = 'red';
                    return false;
                }
                if (is_numeric($ProjectID = $this->find_project_by_id($acc_cost_params['customerprojectcode']))) {
                    $InvoiceO->ProjectID = $ProjectID;
                } else {
                    # Customer project for code __ not found (customer projectcode does not match any internal projects)
                    $InvoiceO->Status     .= "Fant ikke kundens prosjekt for kode $ProjectID (kunde prosjektkode er ikke lik noen intern prosjekt)";
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
        $starting_id = 100000001;
        $used_accounts_hash = $_lib['storage']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "select AccountPlanID from accountplan where AccountPlanType = 'supplier' and AccountPlanID >= $starting_id order by AccountPlanID"));
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

	    #Cleaning after prior developer -eirhje 23.01.10

            # get fakturabankinvoicein id
			if ($LodoID = $this->get_fakturabankincominginvoice($InvoiceO->FakturabankID)) {
				$InvoiceO->LodoID = $LodoID;
			} else {
				$InvoiceO->LodoID = null;
			}

            #print "URL: " . $InvoiceO->UBLExtensions->UBLExtension->ExtensionContent->URL . "<br>\n";
            #print "FB ID:   $InvoiceO->FakturabankID<br>\n";

            //#Should this be more restricted in time or period to eliminate false searches? Any other method to limit it to only look in the correct records? No?

            // We are changing an incomig EHF CreditNote to a negative invoice so this amount needs to be
            // negated to be saved in LODO correctly as a negative invoice
            if ($InvoiceO->CreditNote) {
                $InvoiceO->LegalMonetaryTotal->PayableAmount = -$InvoiceO->LegalMonetaryTotal->PayableAmount;
                $InvoiceO->TaxTotal->TaxAmount = -$InvoiceO->TaxTotal->TaxAmount;
                if(!empty($InvoiceO->AllowanceCharge)) {
                    foreach ($InvoiceO->AllowanceCharge as &$allowance_charge) {
                        $allowance_charge->Amount = -$allowance_charge->Amount;
                        unset($allowance_charge); // because of problem with references
                    }
                }
            }

            $_CompanyID = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
            $_SchemeID  = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
            list($account, $SchemeID)  = $this->find_reskontro($_CompanyID, 'supplier', $_SchemeID);
            if($account) {
                $InvoiceO->AccountPlanID   = $account->AccountPlanID;

                #Check if this invoice exists
                $query          = "select * from invoicein where SupplierAccountPlanID='" . $InvoiceO->AccountPlanID . "' and InvoiceNumber='" . $InvoiceO->ID . "'";
                //print "$query<br>\n";
                $invoiceexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($invoiceexists) {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    # Invoice is already downloaded
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
                    # Counterpart accountplan result/balance not set for accountplan _
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
                    # Find last and first open period could have been in own accountperiod object.

                    $PeriodOld         = $InvoiceO->Period;
                    $InvoiceO->Period  = $accounting->get_first_open_accountingperiod($PeriodOld);
                    # The period __ is locked, changing to __
                    $InvoiceO->Status .= 'Perioden ' . $PeriodOld . ' er lukket endrer til ' . $InvoiceO->Period . '. ';
                }
                if ($InvoiceO->DocumentCurrencyCode != exchange::getLocalCurrency() && !exchange::getConversionRate($InvoiceO->DocumentCurrencyCode)) {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    # Could not find currency value for __
                    $InvoiceO->Status .= 'Finner ikke valutaverdi for '. $InvoiceO->DocumentCurrencyCode;
                }

                if ($InvoiceO->IssueDate == '0000-00-00') {
                    $InvoiceO->Journal = false;
                    $InvoiceO->Class   = 'red';
                    # Date cannot be __
                    $InvoiceO->Status .= 'Dato kan ikke v&aelig;re '. $InvoiceO->IssueDate;
                }
                if ($InvoiceO->LegalMonetaryTotal->PayableAmount == 0) {
                    # Do you want invoice amount crowns __?
                    $InvoiceO->Status .= "Vil du ha fakturabel&oslash;p kr " . $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) . '? ';
                }

                #Check that we have not journaled the same invoices earlier.
                $query          = "select * from invoicein where SupplierAccountPlanID='" . $InvoiceO->AccountPlanID . "' and InvoiceNumber='" . $InvoiceO->ID . "' and Active=1";
                #print "$query<br>\n";
                $voucherexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));
                if($voucherexists) {
                    # Invoice is downloaded
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
                  // We are changing an incomig EHF CreditNote to a negative invoice so these amounts need to be
                  // negated to be saved in LODO correctly as a negative invoice
                  if ($InvoiceO->CreditNote) {
                    $line->InvoicedQuantity = -$line->InvoicedQuantity;
                    $line->LineExtensionAmount = -$line->LineExtensionAmount;
                    $line->TaxTotal->TaxAmount = -$line->TaxTotal->TaxAmount;
                    if(!empty($line->AllowanceCharge)) {
                        foreach ($line->AllowanceCharge as &$allowance_charge) {
                            $allowance_charge->Amount = -$allowance_charge->Amount;
                            unset($allowance_charge); // because of problem with references
                        }
                    }
                    if(!empty($line->Price->AllowanceCharge)) {
                        foreach ($line->Price->AllowanceCharge as &$allowance_charge) {
                            $allowance_charge->Amount = -$allowance_charge->Amount;
                            unset($allowance_charge); // because of problem with references
                        }
                    }
                  }

                  $this->extractLineAccountingCost($line, $InvoiceO);
                }
                if($InvoiceO->Journal) {
                    # Ready to bookkeep based on Firma ID __
                    $InvoiceO->Status   .= "Klar til bilagsf&oslash;ring basert p&aring: FirmaID: $SchemeID";
                }

                #$this->registerincoming($InvoiceO);
            } else {
                $scheme_value = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                $scheme_type  = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
                # Could not find supplier based on PartyIdentification __
                $InvoiceO->Status   .= "Finner ikke leverand&oslash;r basert p&aring; PartyIdentification: " .
                    $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;

                if ($scheme_type == 'NO:ORGNR') {
                    $TemporaryAccountPlanID = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
                } else {
                    // find the first available account plan id
                    $org = new lodo_orgnumberlookup_orgnumberlookup();
                    $org->getOrgNumberByScheme($scheme_value, $scheme_type);
                    $starting_id = 100000001;

                    if ($_lib['sess']->get_companydef('BaseAccountIDOnMotkonto')) $starting_id += $org->MotkontoResultat1 * 10000;

                    for ($i = $starting_id; $i <= 999999999; $i++) {
                      if (!isset($used_accounts_hash[$i])) break;
                    }

                    $TemporaryAccountPlanID = $i;
                    $used_accounts_hash[$TemporaryAccountPlanID] = $TemporaryAccountPlanID;
                }

                $InvoiceO->Status   .= sprintf(
                    '<input type="submit" value="Opprett" name="action_fakturabank_addmissingaccountplan" onclick="chooseOnlyOneAccountPlanID(event, %s); ">',
                    $TemporaryAccountPlanID
                );

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

    private function find_customer_reskontros_by_firma_id($firma_id_value, $firma_id_type, $country) {
      global $_lib;

      $accounts_query = "
        SELECT *
        FROM
          accountplan
        WHERE
          AccountPlanType = 'customer' AND
          AccountPlanID IN (
            SELECT aps.AccountPlanID
            FROM
              accountplanscheme aps
              JOIN
              fakturabankscheme fbs
              ON fbs.FakturabankSchemeID = aps.FakturabankSchemeID
            WHERE
              aps.SchemeValue = '$firma_id_value' AND
              fbs.SchemeType = '$firma_id_type' " .
              (empty($country) ? "" : "AND aps.CountryCode = '$country'") .
         ")";
      $accounts_result = $_lib['db']->db_query($accounts_query);
      $accounts = array();
      if ($_lib['db']->db_numrows($accounts_result) > 0) {
        while ($account = $_lib['db']->db_fetch_object($accounts_result)) {
          $accounts[] = $account;
        }
      }
      return $accounts;
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
    # Try to find the ledger(reskontro) in the following sequence: OrgNumber, E-Mail, Phone, AccountPlanID/Customer number
    # It will be possible to add a lot of mappings here - but it will be a lot of manual adminsitration to get it working
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
                    #print "Found it with $SchemeID: $PartyIdentification<br />\n";
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
        #Assuming we only set the account once

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
            continue;
        }
        $org->getOrgNumberByScheme($scheme_value, $scheme_type);

        if($org->success) {
          # Inforamtion is fetched automatically based on organisation number
          $_lib['message']->add("Opplysninger er hentet automatisk basert p&aring; organisasjonsnummeret.");

          // Only update if the fields contains a value
          if(!empty($org->OrgNumber))   $_POST['accountplan_OrgNumber']   = $dataH['OrgNumber'] = $org->OrgNumber;
          if(!empty($org->AccountName)) $_POST['accountplan_AccountName'] = $dataH['AccountName'] = $org->AccountName;
          if(!empty($org->Email))       $_POST['accountplan_Email']       = $dataH['Email'] = $org->Email;
          if(!empty($org->Mobile))      $_POST['accountplan_Mobile']      = $dataH['Mobile'] = $org->Mobile;
          if(!empty($org->Phone))       $_POST['accountplan_Phone']       = $dataH['Phone'] = $org->Phone;
          if(!empty($org->ParentCompanyName))    $_POST['accountplan_ParentName']   = $dataH['ParentName'] = $org->ParentCompanyName;
          if(!empty($org->ParentCompanyNumber))  $_POST['accountplan_ParentOrgNumber']   = $dataH['ParentOrgNumber'] = $org->ParentCompanyNumber;

          $_POST['accountplan_EnableInvoiceAddress'] = $dataH['EnableInvoiceAddress'] = 1;
          if(!empty($org->IAdress->Address1)) $_POST['accountplan_Address'] = $dataH['Address'] = $org->IAdress->Address1;
          if(!empty($org->IAdress->City))     $_POST['accountplan_City']    = $dataH['City'] = $org->IAdress->City;
          if(!empty($org->IAdress->ZipCode))  $_POST['accountplan_ZipCode'] = $dataH['ZipCode'] = $org->IAdress->ZipCode;

          if(!empty($org->IAdress->Country))  $_POST['accountplan_CountryCode'] = $dataH['CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);

          if(!empty($org->DomesticBankAccount)) $_POST['accountplan_DomesticBankAccount'] = $dataH['DomesticBankAccount'] = $org->DomesticBankAccount;

          if(!empty($org->CreditDays)) {
            $_POST['accountplan_EnableCredit'] = $dataH['EnableCredit'] = 1;
            $_POST['accountplan_CreditDays'] = $dataH['CreditDays'] = $org->CreditDays;
          }
          if(!empty($org->MotkontoResultat1))	{
            $_POST['accountplan_EnableMotkontoResultat'] = $dataH['EnableMotkontoResultat'] = 1;
            $_POST['accountplan_MotkontoResultat1'] = $dataH['MotkontoResultat1'] = $org->MotkontoResultat1;
          }
          if(!empty($org->MotkontoResultat2))	{
            $_POST['accountplan_EnableMotkontoResultat'] = $dataH['EnableMotkontoResultat'] = 1;
            $_POST['accountplan_MotkontoResultat2'] = $dataH['MotkontoResultat2'] = $org->MotkontoResultat2;
          }
          if(!empty($org->MotkontoBalanse1)) {
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

            if (empty($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->CustomerNumber)) {
                # Not possible to auto create because customer number is missing
                $_lib['message']->add("Ikke mulig &aring; auto-opprette fordi kundenr mangler.");
                continue;
            }

            $customernumber = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->CustomerNumber;

            list($SchemeID, $SchemeIDType) = $this->extractCustomerSchemeID($InvoiceO);

            #check if exists first
            if ($account = $this->find_customer_reskontro_by_customernumber($customernumber)) {
                continue; // exists already
            } else {
                $dataH = array();

                if($customernumber > 10000) {
                    #We should have known SchemeID - so we can decide if accountplan should be counted automatically or not > 10000 because of norwegian accountplan

                    #We must anyway check that the suggested accountplan does not exist from before.


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

                    #Should have done a lookup from brreg as the same time as this registration, but we get pretty much all the info from the invoice
                    #Can we set a default counterpart accountplan that will be ok?
                    #We must copy default data from the parent category to this - must centralize creation of accountplans in own object
                    #print_r($dataH);
                    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'action' => 'auto', 'debug' => false));
                    if (!empty($SchemeID) && !empty($SchemeIDType)) {
                      $Country = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode;
                      // Default to Norway if none supplied
                      if (empty($Country)) $Country = 'NO';
                      $FakturabankScheme = $_lib['storage']->get_row(array('query' => "SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = '" . $SchemeIDType . "'"));
                      $FirmaIDH = array(
                        'AccountPlanID' => $customernumber,
                        'FakturabankSchemeID' => $FakturabankScheme->FakturabankSchemeID,
                        'SchemeValue' => $SchemeID,
                        'CountryCode' => $Country
                      );
                      $_lib['storage']->store_record(array('data' => $FirmaIDH, 'table' => 'accountplanscheme', 'action' => 'auto', 'debug' => false));
                    }
                    #exit;
                    $count++;
                } else {
                    # Ledger(reskontro) with number lower than 10000 must be created manually: __
                    $_lib['message']->add("Reskontro med nummer lavere enn 10.000 mŒ opprettes manuelt: " . $customernumber);
                }
            }

            if (!empty($single_invoice_id) && $InvoiceO->ID == $single_invoice_id) {
                break; // finished since we have processed the only one we wanted
            }
        }
        # __ accountplans automatically created - counterpart accountplan must be set manually
        $_lib['message']->add("$count kontoplaner automatisk opprettet - motkonto m&aring; settes manuelt");
    }

    ################################################################################################
    public function registerincoming() {
        global $_lib, $accounting, $_SETUP;

        // since when we load the page we already have fetched invoices
        $_SESSION['oauth_invoices_fetched'] = true;
        $invoicesO        = $this->incoming();
        $conversion_rate  = 0;
        $is_foreign       = false;

        $fbvoting = new lodo_fakturabank_fakturabankvoting();

        if (!empty($invoicesO->Invoice)) {

        $events = array();
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
                $dataH['DepartmentID']            = $InvoiceO->DepartmentID;
                $dataH['ProjectID']               = $InvoiceO->ProjectID;
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
                $dataH['ICountryCode']           = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode;

                $dataH['DName']                  = $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                $dataH['DAddress']               = $InvoiceO->Delivery->DeliveryLocation->Address->StreetName;
                $dataH['DCity']                  = $InvoiceO->Delivery->DeliveryLocation->Address->CityName;
                $dataH['DZipCode']               = $InvoiceO->Delivery->DeliveryLocation->Address->PostalZone;
                $dataH['DCountryCode']           = $InvoiceO->Delivery->DeliveryLocation->Address->IdentificationCode;

                #Only real KID can be registered in the KID field
                if($InvoiceO->PaymentMeans->PaymentID) {
                    $dataH['KID']  = $InvoiceO->PaymentMeans->PaymentID; #KID
                }
                $ID = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoicein', 'debug' => false));

                # Allowance/Charge on invoice
                if (!empty($InvoiceO->AllowanceCharge)) {
                    foreach ($InvoiceO->AllowanceCharge as $AllowanceCharge) {
                        $query_allowance_charge = "select * from allowancecharge where ChargeIndicator = " . $AllowanceCharge->ChargeIndicator . " and lower(Reason) = lower('" . $AllowanceCharge->AllowanceChargeReason . "') and Active = 1";
                        $allowance_charge = $_lib['db']->get_row(array('query' => $query_allowance_charge, 'debug' => false));
                        $dataAC["AllowanceChargeID"] = $allowance_charge->AllowanceChargeID;
                        $dataAC["InvoiceType"] = 'in'; # Hardcoded since this is an incoming invoice
                        $dataAC["InvoiceID"] = $ID;
                        $dataAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                        $dataAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                        $dataAC["Amount"] = $AllowanceCharge->Amount;
                        // Select VatID based on the suppliers result bookkeeping account
                        $vat_id_query = "SELECT case when a.VatID < 40 THEN a.VatID + 30 ELSE a.VatID END as VatID FROM accountplan a WHERE a.AccountPlanID = (SELECT MotkontoResultat1 FROM accountplan WHERE AccountPlanID = " . $InvoiceO->AccountPlanID . ")";
                        $vat = $_lib['db']->get_row(array('query' => $vat_id_query));
                        $dataAC["VatID"] = $vat->VatID;
                        $dataAC["VatPercent"] = $AllowanceCharge->TaxCategory->Percent;
                        $_lib['storage']->store_record(array('data' => $dataAC, 'table' => 'invoiceallowancecharge', 'debug' => false));
                    }
                }

                foreach($InvoiceO->InvoiceLine as $line) {

                    #print_r($line);

                    #preprocess price/quantity - because inconsistent data can appear
                    $IsOnlyTax = false;
                    if($line->InvoicedQuantity != 0 && $line->Price->PriceAmount != 0) {
                        $Quantity   = $line->InvoicedQuantity;
                        $CustPrice  = $line->Price->PriceAmount;
                    } else {
                        $Quantity   = 1;
                        $CustPrice  = $line->LineExtensionAmount;

                        // If this is MVA only line, save this field to true.
                        // We later use it to bookkeep this line to tax account.
                        if($line->Price->PriceAmount == 0 && preg_match("/^MVA/", $line->Item->Name)) {
                            $IsOnlyTax = true;
                        }
                    }

                    if($CustPrice != 0) {
                        $LineNum += 10;
                        $datalineH                      = array();
                        $datalineH['ID']                = $ID;
                        $datalineH['AccountPlanID']     = $InvoiceO->MotkontoAccountPlanID;
                        $datalineH['LineNum']           = $LineNum;
                        $datalineH['ProductName']       = $line->Item->Name;
                        $datalineH['ProductNumber']     = $line->Item->SellersItemIdentification->ID;
                        $datalineH['CarID']             = $line->CarID;
                        $datalineH['DepartmentID']      = $line->DepartmentID;
                        $datalineH['ProjectID']         = $line->ProjectID;
                        $datalineH['Comment']           = $line->Item->Description;
                        $datalineH['QuantityOrdered']   = $Quantity;
                        $datalineH['QuantityDelivered'] = $Quantity;
                        $datalineH['IsOnlyTax']         = $IsOnlyTax;

                        #Foreign currency
                        if ($is_foreign) {
                            $datalineH['UnitCostPrice'] = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, $CustPrice);
                            $datalineH['ForeignCurrencyID'] = $InvoiceO->DocumentCurrencyCode;
                            $datalineH['ForeignAmount']     = (float)$line->LineExtensionAmount + $line->TaxTotal->TaxAmount;
                            $datalineH['ForeignConvRate']   = $conversion_rate;
                            $datalineH['TotalWithoutTax']   = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, (float)$line->LineExtensionAmount);
                            $datalineH['TaxAmount']         = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, (float)$line->TaxTotal->TaxAmount);
                        } else {
                            $datalineH['UnitCostPrice'] = $CustPrice;
                            $datalineH['TotalWithoutTax']   = (float)$line->LineExtensionAmount;
                            $datalineH['TaxAmount']         = (float)$line->TaxTotal->TaxAmount;
                        }

                        $datalineH['UnitCustPrice'] = $datalineH['UnitCostPrice'];

                        $datalineH['UnitCostPriceCurrencyID'] = exchange::getLocalCurrency();
                        $datalineH['UnitCustPriceCurrencyID'] = exchange::getLocalCurrency();

                        $datalineH['Vat']               = $line->Item->ClassifiedTaxCategory->Percent;
                        #$datalineH['VatID']             = $line->Price->PriceAmount; #This must probably be mapped

                        $datalineH['TotalWithTax'] = $datalineH['TotalWithoutTax'] + $datalineH['TaxAmount'];

                        $datalineH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                        $datalineH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
                        $datalineH['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');

                        $LineID = $_lib['storage']->store_record(array('data' => $datalineH, 'table' => 'invoiceinline', 'debug' => false));

                        # Allowance/Charge on invoice line
                        if (!empty($line->AllowanceCharge)) {
                            foreach ($line->AllowanceCharge as $AllowanceCharge) {
                                $datalineAC["InvoiceType"] = 'in'; # Hardcoded since this is an incoming invoice
                                $datalineAC["AllowanceChargeType"] = 'line'; # Hardcoded since this is a line allowance/charge
                                $datalineAC["InvoiceLineID"] = $LineID;
                                $datalineAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                                $datalineAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                                $datalineAC["Amount"] = $AllowanceCharge->Amount;
                                $_lib['storage']->store_record(array('data' => $datalineAC, 'table' => 'invoicelineallowancecharge', 'debug' => false));
                            }
                        }

                        # Allowance/Charge on invoice line price
                        if (!empty($line->Price->AllowanceCharge)) {
                            foreach ($line->Price->AllowanceCharge as $AllowanceCharge) {
                                $datalineAC["InvoiceType"] = 'in'; # Hardcoded since this is an incoming invoice
                                $datalineAC["AllowanceChargeType"] = 'price'; # Hardcoded since this is a price allowance/charge
                                $datalineAC["InvoiceLineID"] = $LineID;
                                $datalineAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                                $datalineAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                                $datalineAC["Amount"] = $AllowanceCharge->Amount;
                                $_lib['storage']->store_record(array('data' => $datalineAC, 'table' => 'invoicelineallowancecharge', 'debug' => false));
                            }
                        }
                    }
                }

                #Update fakturabank voting tables to enable lookup of lodo invoice
                #given bank transaction information, when importing transactions from bank
                $fbvoting->update_fakturabank_incoming_invoice($InvoiceO->FakturabankID, $ID, $InvoiceO->AccountPlanID);

                #Set status in fakturabank
                $comment = "Lodo PHP Invoicein ID: " . $ID . " registered " . strftime("%F %T");
                $events[] = array( 'id' => $InvoiceO->FakturabankID, 'status' => $_SETUP['FB_INVOICE_UPDATE_STATUS'], 'comment' => $comment);

            } else {
                #print "Invoice found: " . $InvoiceO->AccountPlanID . "', InvoiceID='" . $InvoiceO->ID . "<br>\n";
            }
          }
          $this->setEvents($events);
        }
    }

    public function registeroutgoing() {
        global $_lib, $_SETUP;

        // since when we load the page we already have fetched invoices
        $_SESSION['oauth_invoices_fetched'] = true;
        $invoicesO = $this->outgoing();

        $events = array();
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
                    $dataH['TotalVat']              = $InvoiceO->TaxTotal->TaxAmount;
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
                    $dataH['CurrencyID'] = $InvoiceO->DocumentCurrencyCode;

                    # Sender info
                    $query                  = "select * from company where CompanyID='" . $dataH['FromCompanyID'] . "'";
                    $company                = $_lib['storage']->get_row(array('query' => $query));

                    $dataH['SName']         = empty($InvoiceO->AccountingSupplierParty->Party->PartyName->Name)                            ? $company->VName        : $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
                    $dataH['SAddress']      = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName)                  ? $company->VAddress     : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
                    $dataH['SCity']         = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName)                    ? $company->VCity        : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName;
                    $dataH['SZipCode']      = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone)                  ? $company->VZipCode     : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;
                    $dataH['SCountryCode']  = empty($InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode) ? $company->VCountryCode : $InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode;
                    $dataH['SPhone']        = empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone)                         ? $company->Phone        : $InvoiceO->AccountingSupplierParty->Party->Contact->Telephone;
                    $dataH['SMobile']       = $company->Mobile;
                    $dataH['SFax']          = empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telefax)                           ? $company->Fax          : $InvoiceO->AccountingSupplierParty->Party->Contact->Telefax;
                    $dataH['SEmail']        = empty($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail)                    ? $company->Email        : $InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail;

                    # Save from imported data only if correct type is sent
                    $dataH['SBankAccount']        = (!empty($InvoiceO->PaymentMeans->PayeeFinancialAccount->ID)) ? $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID : $company->BankAccount;
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
                    $dataH['ICountryCode']           = $InvoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode;
                    $dataH['Phone']                  = $InvoiceO->AccountingCustomerParty->Party->Contact->Telephone;
                    $dataH['IEmail']                 = $InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail;

                    $dataH['DName']                  = $InvoiceO->AccountingCustomerParty->Party->PartyName->Name;
                    $dataH['DAddress']               = $InvoiceO->Delivery->DeliveryLocation->Address->StreetName;
                    $dataH['DCity']                  = $InvoiceO->Delivery->DeliveryLocation->Address->CityName;
                    $dataH['DZipCode']               = $InvoiceO->Delivery->DeliveryLocation->Address->PostalZone;
                    $dataH['DCountryCode']           = $InvoiceO->Delivery->DeliveryLocation->Address->Country->IdentificationCode;

                    if($InvoiceO->PaymentMeans->PaymentID) {
                        $dataH['KID']  = $InvoiceO->PaymentMeans->PaymentID; #KID
                    }
                    $ID = $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));

                    # Allowance/Charge on invoice
                    if (!empty($InvoiceO->AllowanceCharge)) {
                        foreach ($InvoiceO->AllowanceCharge as $AllowanceCharge) {
                            $query_allowance_charge = "select * from allowancecharge where ChargeIndicator = " . $AllowanceCharge->ChargeIndicator . " and lower(Reason) = lower('" . $AllowanceCharge->AllowanceChargeReason . "') and Active = 1";
                            $allowance_charge = $_lib['db']->get_row(array('query' => $query_allowance_charge, 'debug' => false));
                            $dataAC["AllowanceChargeID"] = $allowance_charge->AllowanceChargeID;
                            $dataAC["InvoiceType"] = 'out'; # Hardcoded since this is an outgoing invoice
                            $dataAC["InvoiceID"] = $ID;
                            $dataAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                            $dataAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                            $dataAC["Amount"] = $AllowanceCharge->Amount;
                            // Select VatID based on the Category and date
                            $vat_id_from_category = "select VatID from vat where Category = '" . $AllowanceCharge->TaxCategory->ID . "' and Type = 'sale' and ValidFrom <= '" . $InvoiceO->IssueDate . "' and ValidTo >= '" . $InvoiceO->IssueDate . "'";
                            $vat = $_lib['db']->get_row(array('query' => $vat_id_from_category));
                            $dataAC["VatID"] = $vat->VatID;
                            $dataAC["VatPercent"] = $AllowanceCharge->TaxCategory->Percent;
                            $_lib['storage']->store_record(array('data' => $dataAC, 'table' => 'invoiceallowancecharge', 'debug' => false));
                        }
                    }

                    #Must check that product number is correct and matching
                    foreach($InvoiceO->InvoiceLine as $line) {

                        #preprocess price/quantity - because inconsistent data can appear
                        if($line->InvoicedQuantity != 0 && $line->Price->PriceAmount != 0) {
                            $Quantity   = $line->InvoicedQuantity;
                            $CustPrice  = $line->Price->PriceAmount;
                        } else {
                            $Quantity   = 1;
                            $CustPrice  = $line->LineExtensionAmount;
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

                            $datalineH['TaxAmount']         = $line->TaxTotal->TaxAmount;
                            $datalineH['Vat']               = $line->Item->ClassifiedTaxCategory->Percent;
                            #$datalineH['VatID']             = $line->Price->VatID; #This must probably be mapped

                            $datalineH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
                            $datalineH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');

                            $LineID = $_lib['storage']->store_record(array('data' => $datalineH, 'table' => 'invoiceoutline', 'debug' => false));

                            # Allowance/Charge on invoice line
                            if (!empty($line->AllowanceCharge)) {
                                foreach ($line->AllowanceCharge as $AllowanceCharge) {
                                    $datalineAC["InvoiceType"] = 'out'; # Hardcoded since this is an outgoing invoice
                                    $datalineAC["AllowanceChargeType"] = 'line'; # Hardcoded since this is a line allowance/charge
                                    $datalineAC["InvoiceLineID"] = $LineID;
                                    $datalineAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                                    $datalineAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                                    $datalineAC["Amount"] = $AllowanceCharge->Amount;
                                    $_lib['storage']->store_record(array('data' => $datalineAC, 'table' => 'invoicelineallowancecharge', 'debug' => false));
                                }
                            }

                            # Allowance/Charge on invoice line price
                            if (!empty($line->Price->AllowanceCharge)) {
                              foreach ($line->Price->AllowanceCharge as $AllowanceCharge) {
                                $datalineAC["InvoiceType"] = 'out'; # Hardcoded since this is an outgoing invoice
                                $datalineAC["AllowanceChargeType"] = 'price'; # Hardcoded since this is a price allowance/charge
                                $datalineAC["InvoiceLineID"] = $LineID;
                                $datalineAC["ChargeIndicator"] = ($AllowanceCharge->ChargeIndicator == 'true') ? 1 : 0;
                                $datalineAC["AllowanceChargeReason"] = $AllowanceCharge->AllowanceChargeReason;
                                $datalineAC["Amount"] = $AllowanceCharge->Amount;
                                $_lib['storage']->store_record(array('data' => $datalineAC, 'table' => 'invoicelineallowancecharge', 'debug' => false));
                              }
                            }
                        }
                    }

                    #Update fakturabank voting tables to enable lookup of lodo invoice
                    #given bank transaction information, when importing transactions from bank
                    $fbvoting = new lodo_fakturabank_fakturabankvoting();
                    $fbvoting->update_fakturabank_outgoing_invoice($InvoiceO->FakturabankID, $ID, $InvoiceO->AccountPlanID);

                    #Set status in fakturabank
                    $comment = "Lodo PHP Invoiceout ID: " . $InvoiceO->ID . " registered " . strftime("%F %T");
                    $events[] = array('id' => $InvoiceO->FakturabankID, 'status' => $_SETUP['FB_INVOICE_UPDATE_STATUS'], 'comment' => $comment);

                } else {
                    #print "Invoice found: " . $InvoiceO->AccountPlanID . "', InvoiceID='" . $InvoiceO->ID . "<br>\n";
                }
                $invoice = new invoice(array('CustomerAccountPlanID' => $dataH['CustomerAccountPlanID'], 'VoucherType' => 'S', 'InvoiceID' => $dataH['InvoiceID']));
                $invoice->init(array());
                $invoice->journal();
            }
        }
        $this->setEvents($events);
    }

    /* Adds a node element to the xml document
     * Returns the created node or false on failure
     * $doc               -> xml document object, used to create the node element
     * $parent_element    -> element to which the new node is to be appended as a child
     * $node_name         -> name of the new node
     * $value             -> value the node is to have, defaults to 'null_value' if none given(in case of cac:... nodes)
     * $attributes_array  -> array of key => value attributes to be added to the node, defaults to an empty array if none given
     * $use_cdata         -> create a CDATA section in the node value, defaults to false
     */
    public function createElementIfNotEmpty($doc, $parent_element, $node_name, $value = 'null_value', $attributes_array = array(), $use_cdata = false) {
        if (!is_null($value) && $value !== '') { // check if not null/empty string
          if ($value !== 'null_value') { // if a value is given
            $value = utf8_encode($value);
            if ($use_cdata) {
              $cdata_value = $doc->createCDATASection($value);
              $cbc = $doc->createElement($node_name);
              $cbc->appendChild($cdata_value);
            } else {
              $cbc = $doc->createElement($node_name, $value);
            }
          } else {
            $cbc = $doc->createElement($node_name);
          }
          foreach($attributes_array as $attribute_name => $attribute_value) {
            $cbc->setAttribute($attribute_name, $attribute_value);
          }
          $parent_element->appendChild($cbc);
          return $cbc;
        }
        return false;
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

        self::createElementIfNotEmpty($doc, $invoice, 'cbc:UBLVersionID', '2.1');
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:CustomizationID', 'urn:www.cenbii.eu:transaction:biitrns010:ver2.0:extended:urn:www.peppol.eu:bis:peppol5a:ver2.0:extended:urn:www.difi.no:ehf:faktura:ver2.0');
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:ProfileID', 'urn:www.cenbii.eu:profile:bii05:ver2.0');
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:ID', $InvoiceO->ID);
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:IssueDate', $InvoiceO->IssueDate);
        $invoice_type_code_attributes = array('listID' => 'UNCL1001');
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:InvoiceTypeCode', $InvoiceO->InvoiceTypeCode, $invoice_type_code_attributes);

        // Not by EHF but needed by fakturaBank
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:OriginSystemSavedBy', $InvoiceO->LodoSavedBy);
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:DateOfIssue', $InvoiceO->DateOfIssue);

        // uses CDATA section for special text that may have some characters not allowed as regular XML node value
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:Note', $InvoiceO->Note, array(), true);
        $currency_code_attributes = array('listID' => 'ISO4217');
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:DocumentCurrencyCode', $InvoiceO->DocumentCurrencyCode, $currency_code_attributes);

        /* gather data to be sent in AccountCost element */
        $acc_cost = '';
        $acc_types = array('DepartmentID', 'DepartmentCode', 'ProjectID', 'ProjectCode', 'CustomerDepartment', 'CustomerProject');
        foreach ($acc_types as $acc_type) {
            if ($InvoiceO->$acc_type != "") {
                if (!empty($acc_cost)) {
                    $acc_cost .= ';';
                }
                $acc_cost .= strtolower($acc_type) . '=' . urlencode(utf8_encode($InvoiceO->$acc_type));
            }
        }
        self::createElementIfNotEmpty($doc, $invoice, 'cbc:AccountingCost', $acc_cost);

        if (!empty($InvoiceO->OrderReference)) {
            $order_reference = self::createElementIfNotEmpty($doc, $invoice, 'cac:OrderReference');
            self::createElementIfNotEmpty($doc, $order_reference, 'cbc:ID', $InvoiceO->OrderReference->ID);
        }

        ############################################################################################
        # AccountingSupplierParty
        $supplier = self::createElementIfNotEmpty($doc, $invoice, 'cac:AccountingSupplierParty');
            $cacparty = self::createElementIfNotEmpty($doc, $supplier, 'cac:Party');

                $name = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyName');
                    // handle ampersand in company names (we should probably send all text data with cdata function)
                    self::createElementIfNotEmpty($doc, $name, 'cbc:Name', $InvoiceO->AccountingSupplierParty->Party->PartyName->Name, array(), true);

                $address = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PostalAddress');
                    self::createElementIfNotEmpty($doc, $address, 'cbc:StreetName', $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:AdditionalStreetName', $InvoiceO->AccountingSupplierParty->Party->PostalAddress->BuildingNumber);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:CityName', $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:PostalZone', $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone);
                    $country = self::createElementIfNotEmpty($doc, $address, 'cac:Country');
                        $country_identification_attributes = array('listID' => 'ISO3166-1:Alpha2');
                        self::createElementIfNotEmpty($doc, $country, 'cbc:IdentificationCode', $InvoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode, $country_identification_attributes);

                if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme)) {
                    $partytaxscheme = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyTaxScheme');
                        $company_id_attributes = array();
                        if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID)) {
                            $company_id_attributes['schemeID'] = utf8_encode($InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID);
                        }
                        self::createElementIfNotEmpty($doc, $partytaxscheme, 'cbc:CompanyID', $InvoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID, $company_id_attributes);

                        $taxscheme = self::createElementIfNotEmpty($doc, $partytaxscheme, 'cac:TaxScheme');
                            $id_attributes = array('schemeID' => 'UN/ECE 5153', 'schemeAgencyID' => '6');
                            self::createElementIfNotEmpty($doc, $taxscheme, 'cbc:ID', 'VAT', $id_attributes);
                }

                if (!empty($InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID) && !empty($InvoiceO->AccountingSupplierParty->Party->PartyName->Name)){
                    $legalentity = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyLegalEntity');
                        // handle ampersand in company names (we should probably send all text data with cdata function)
                        self::createElementIfNotEmpty($doc, $legalentity, 'cbc:RegistrationName', $InvoiceO->AccountingSupplierParty->Party->PartyName->Name, array(), true);
                        $company_id_attributes = array('schemeID' => 'NO:ORGNR');
                        self::createElementIfNotEmpty($doc, $legalentity, 'cbc:CompanyID', $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID, $company_id_attributes);
                }

                if (!empty($InvoiceO->AccountingSupplierParty->Party->Contact->Telephone) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->Fax) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail) ||
                    !empty($InvoiceO->AccountingSupplierParty->Party->Contact->Mobile)) {
                    $contact = self::createElementIfNotEmpty($doc, $cacparty, 'cac:Contact');
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Name', $InvoiceO->AccountingSupplierParty->Party->Contact->Name);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Telephone', $InvoiceO->AccountingSupplierParty->Party->Contact->Telephone);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Telefax', $InvoiceO->AccountingSupplierParty->Party->Contact->Telefax);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:ElectronicMail', $InvoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail);
                        // This is not strictly by EHF but is by the customization we use on the EHF (www.cenbii.eu, Look at Invoice/cbc:CustomizationID)
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Note', 'Mobile: ' . $InvoiceO->AccountingSupplierParty->Party->Contact->Mobile);
                }

        ############################################################################################
        # AccountingCustomerParty
        $customer = self::createElementIfNotEmpty($doc, $invoice, 'cac:AccountingCustomerParty');
            $cacparty = self::createElementIfNotEmpty($doc, $customer, 'cac:Party');

                // Add customer number
                if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID)) {
                    $identification = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyIdentification');
                        // Not by EHF standard, but since we send to FB only, we send whatever schema we want
                        $AccountPlanID = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;
                        $schemeControl = new lodo_accountplan_scheme($AccountPlanID);
                        $firmaID = $schemeControl->getFirstFirmaID();
                        if($firmaID && $firmaID['type'] && $firmaID['value']) {
                            $schemaValue = $firmaID['value'];
                            $id_attributes = array('schemeID' => $firmaID['type']);
                            self::createElementIfNotEmpty($doc, $identification, 'cbc:ID', $schemaValue, $id_attributes);
                        } else {
                            $schemaValue = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;
                            $id_attributes = array('schemeID' => 'NO:SUP-ACCNT-RE');
                            self::createElementIfNotEmpty($doc, $identification, 'cbc:ID', $schemaValue, $id_attributes);
                        }

                        // Not by EHF standard, but we always want to send this customer number to FB
                        $customer_number = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID;
                        self::createElementIfNotEmpty($doc, $identification, 'cbc:CustomerNumber', $customer_number);
                }

                $name = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyName');
                    // handle ampersand in company names (we should probably send all text data with cdata function)
                    self::createElementIfNotEmpty($doc, $name, 'cbc:Name', $InvoiceO->AccountingCustomerParty->Party->PartyName->Name, array(), true);

                $address = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PostalAddress');
                    self::createElementIfNotEmpty($doc, $address, 'cbc:StreetName', $InvoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:AdditionalStreetName', $InvoiceO->AccountingCustomerParty->Party->PostalAddress->BuildingNumber);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:CityName', $InvoiceO->AccountingCustomerParty->Party->PostalAddress->CityName);
                    self::createElementIfNotEmpty($doc, $address, 'cbc:PostalZone', $InvoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone);
                    $country = self::createElementIfNotEmpty($doc, $address, 'cac:Country');
                        $country_identification_attributes = array('listID' => 'ISO3166-1:Alpha2');
                        self::createElementIfNotEmpty($doc, $country, 'cbc:IdentificationCode', $InvoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode, $country_identification_attributes);

                if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme)) {
                    $partytaxscheme = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyTaxScheme');
                        $company_id_attributes = array();
                        if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID)) {
                            $company_id_attributes['schemeID'] = utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID);
                        }
                        self::createElementIfNotEmpty($doc, $partytaxscheme, 'cbc:CompanyID', $InvoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID, $company_id_attributes);

                        $taxscheme = self::createElementIfNotEmpty($doc, $partytaxscheme, 'cac:TaxScheme');
                            $id_attributes = array('schemeID' => 'UN/ECE 5153', 'schemeAgencyID' => '6');
                            self::createElementIfNotEmpty($doc, $taxscheme, 'cbc:ID', 'VAT', $id_attributes);
                }

                if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID) && !empty($InvoiceO->AccountingCustomerParty->Party->PartyName->Name)){
                    $legalentity = self::createElementIfNotEmpty($doc, $cacparty, 'cac:PartyLegalEntity');
                        // handle ampersand in company names (we should probably send all text data with cdata function)
                        self::createElementIfNotEmpty($doc, $legalentity, 'cbc:RegistrationName', $InvoiceO->AccountingCustomerParty->Party->PartyName->Name, array(), true);
                        $company_id_attributes = array('schemeID' => utf8_encode($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyIDSchemeID));
                        self::createElementIfNotEmpty($doc, $legalentity, 'cbc:CompanyID', $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID, $company_id_attributes);
                } else {
                    # Customer not chosen, or FirmaID is invalid. The invoice was not sent.
                    $_lib['message']->add("Kunde ikke valgt, eller Firma ID er ugyldig. Fakturaen ble ikke sendt.");
                    return false; // stop from sending, crucial field missing
                }

                if (!empty($InvoiceO->AccountingCustomerParty->Party->Contact->ID) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->Telephone) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->Fax) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail) ||
                    !empty($InvoiceO->AccountingCustomerParty->Party->Contact->Mobile)) {
                    $contact = self::createElementIfNotEmpty($doc, $cacparty, 'cac:Contact');
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:ID', $InvoiceO->AccountingCustomerParty->Party->Contact->ID);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Name', $InvoiceO->AccountingCustomerParty->Party->Contact->Name);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Telephone', $InvoiceO->AccountingCustomerParty->Party->Contact->Telephone);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Telefax', $InvoiceO->AccountingCustomerParty->Party->Contact->Telefax);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:ElectronicMail', $InvoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail);
                        self::createElementIfNotEmpty($doc, $contact, 'cbc:Note', 'Mobile: ' . $InvoiceO->AccountingCustomerParty->Party->Contact->Mobile);
                }

        // Delivery (DeliveryAddress)
        if (!empty($InvoiceO->DeliveryAddress)) {
            $delivery = self::createElementIfNotEmpty($doc, $invoice, 'cac:Delivery');
                $cac_delivery_location = self::createElementIfNotEmpty($doc, $delivery, 'cac:DeliveryLocation');
                    $cac_address = self::createElementIfNotEmpty($doc, $cac_delivery_location, 'cac:Address');
                        self::createElementIfNotEmpty($doc, $cac_address, 'cbc:StreetName', $InvoiceO->DeliveryAddress->Address);
                        self::createElementIfNotEmpty($doc, $cac_address, 'cbc:CityName', $InvoiceO->DeliveryAddress->City);
                        self::createElementIfNotEmpty($doc, $cac_address, 'cbc:PostalZone', $InvoiceO->DeliveryAddress->ZipCode);
                        $cac_country = self::createElementIfNotEmpty($doc, $cac_address, 'cac:Country');
                            $country_identification_attributes = array('listID' => 'ISO3166-1:Alpha2');
                            self::createElementIfNotEmpty($doc, $cac_country, 'cbc:IdentificationCode', $InvoiceO->DeliveryAddress->CountryCode, $country_identification_attributes);
        }


        ############################################################################################
        # PaymentMeans
        $paymentmeans = self::createElementIfNotEmpty($doc, $invoice, 'cac:PaymentMeans');

            $means_code_attributes = array('listID' => 'UNCL4461');
            self::createElementIfNotEmpty($doc, $paymentmeans, 'cbc:PaymentMeansCode', $InvoiceO->PaymentMeans->PaymentMeansCode, $means_code_attributes);
            self::createElementIfNotEmpty($doc, $paymentmeans, 'cbc:PaymentDueDate', $InvoiceO->PaymentMeans->PaymentDueDate);

            // KID number
            self::createElementIfNotEmpty($doc, $paymentmeans, 'cbc:PaymentID', $InvoiceO->PaymentMeans->PaymentID);

            $financialaccount = self::createElementIfNotEmpty($doc, $paymentmeans, 'cac:PayeeFinancialAccount');
                $id_attributes = array('schemeID' => 'BBAN');
                self::createElementIfNotEmpty($doc, $financialaccount, 'cbc:ID', $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID, $id_attributes);

        ############################################################################################
        # PaymentTerms
        if (!empty($InvoiceO->PaymentTerms->Note)) {
            $paymentterms = self::createElementIfNotEmpty($doc, $invoice, 'cac:PaymentTerms');
                self::createElementIfNotEmpty($doc, $paymentterms, 'cbc:Note', $InvoiceO->PaymentTerms->Note, array(), true);
        }

        ############################################################################################
        # Allowances/Charges on invoice
        if(count($InvoiceO->AllowanceCharge)) {
              foreach($InvoiceO->AllowanceCharge as $allowance_charge) {
                  if ($allowance_charge->Amount != 0) {
                      $allowance_charge_cac = self::createElementIfNotEmpty($doc, $invoice, 'cac:AllowanceCharge');
                          self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:ChargeIndicator', (($allowance_charge->ChargeIndicator == '1')?'true':'false'));
                          self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:AllowanceChargeReason', $allowance_charge->AllowanceChargeReason);
                          $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                          self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:Amount', $allowance_charge->Amount, $amount_attributes);

                          $tax_category_cac = self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cac:TaxCategory');
                              $id_attributes = array('schemeID' => 'UNCL5305');
                              self::createElementIfNotEmpty($doc, $tax_category_cac, 'cbc:ID', $allowance_charge->TaxCategory->ID, $id_attributes);
                              self::createElementIfNotEmpty($doc, $tax_category_cac, 'cbc:Percent', $allowance_charge->TaxCategory->Percent);
                              $cac_tax = self::createElementIfNotEmpty($doc, $tax_category_cac, 'cac:TaxScheme');
                                  self::createElementIfNotEmpty($doc, $cac_tax, 'cbc:ID', $allowance_charge->TaxCategory->TaxScheme->ID);
                  }
              }
        }

        ############################################################################################
        # TaxTotal
        $tax = self::createElementIfNotEmpty($doc, $invoice, 'cac:TaxTotal');
            $tax_amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
            self::createElementIfNotEmpty($doc, $tax, 'cbc:TaxAmount', $InvoiceO->TaxTotal['TaxAmount'], $tax_amount_attributes);

            if(is_array($InvoiceO->TaxTotal)) {
                foreach($InvoiceO->TaxTotal as $VatPercent => $Vat) {
                    if(is_numeric($VatPercent)) {
                        $subtotal = self::createElementIfNotEmpty($doc, $tax, 'cac:TaxSubtotal');
                            $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                            self::createElementIfNotEmpty($doc, $subtotal, 'cbc:TaxableAmount', $Vat->TaxSubtotal->TaxableAmount, $amount_attributes);
                            self::createElementIfNotEmpty($doc, $subtotal, 'cbc:TaxAmount', $Vat->TaxSubtotal->TaxAmount, $amount_attributes);

                            $category = self::createElementIfNotEmpty($doc, $subtotal, 'cac:TaxCategory');
                                $id_attributes = array('schemeID' => 'UNCL5305');
                                self::createElementIfNotEmpty($doc, $category, 'cbc:ID', $Vat->TaxSubtotal->TaxCategory->ID, $id_attributes);
                                self::createElementIfNotEmpty($doc, $category, 'cbc:Percent', $Vat->TaxSubtotal->TaxCategory->Percent);
                                self::createElementIfNotEmpty($doc, $category, 'cbc:TaxExemptionReason', $Vat->TaxSubtotal->TaxCategory->TaxExemptionReason);
                                $scheme = self::createElementIfNotEmpty($doc, $category, 'cac:TaxScheme');
                                    self::createElementIfNotEmpty($doc, $scheme, 'cbc:ID', $Vat->TaxSubtotal->TaxCategory->TaxScheme->ID);
                    }
                }
            } else {
                $_lib['message']->add("Skatte info mangler.");
            }

        ############################################################################################
        # LegalMonetaryTotal
        $monetary = self::createElementIfNotEmpty($doc, $invoice, 'cac:LegalMonetaryTotal');

            $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:LineExtensionAmount', $InvoiceO->LegalMonetaryTotal->LineExtensionAmount, $amount_attributes);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:TaxExclusiveAmount', $InvoiceO->LegalMonetaryTotal->TaxExclusiveAmount, $amount_attributes);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:TaxInclusiveAmount', $InvoiceO->LegalMonetaryTotal->TaxInclusiveAmount, $amount_attributes);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:AllowanceTotalAmount', $InvoiceO->LegalMonetaryTotal->AllowanceTotalAmount, $amount_attributes);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:ChargeTotalAmount', $InvoiceO->LegalMonetaryTotal->ChargeTotalAmount, $amount_attributes);
            self::createElementIfNotEmpty($doc, $monetary, 'cbc:PayableAmount', $InvoiceO->LegalMonetaryTotal->PayableAmount, $amount_attributes);

        ############################################################################################
        # InvoiceLine (loop)
        if(count($InvoiceO->InvoiceLine)) {
            foreach($InvoiceO->InvoiceLine as $id => $line) {

                $invoiceline = self::createElementIfNotEmpty($doc, $invoice, 'cac:InvoiceLine');
                    self::createElementIfNotEmpty($doc, $invoiceline, 'cbc:ID', $line->ID);
                    $quantity_attributes = array('unitCodeListID' => 'UNECERec20', 'unitCode' => 'NAR');
                    self::createElementIfNotEmpty($doc, $invoiceline, 'cbc:InvoicedQuantity', $line->InvoicedQuantity, $quantity_attributes);

                    $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                    self::createElementIfNotEmpty($doc, $invoiceline, 'cbc:LineExtensionAmount', $line->LineExtensionAmount, $amount_attributes);

                    ############################################################################################
                    # Allowances/Charges on invoice line
                    if(count($line->AllowanceCharge)) {
                        foreach($line->AllowanceCharge as $allowance_charge) {
                            if ($allowance_charge->Amount != 0) {
                                $allowance_charge_cac = self::createElementIfNotEmpty($doc, $invoiceline, 'cac:AllowanceCharge');
                                    self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:ChargeIndicator', (($allowance_charge->ChargeIndicator == '1')?'true':'false'));
                                    self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:AllowanceChargeReason', $allowance_charge->AllowanceChargeReason);
                                    $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                                    self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:Amount', $allowance_charge->Amount, $amount_attributes);
                            }
                        }
                    }

                    $cac_tax_total = self::createElementIfNotEmpty($doc, $invoiceline, 'cac:TaxTotal');
                        $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                        self::createElementIfNotEmpty($doc, $cac_tax_total, 'cbc:TaxAmount', $line->TaxTotal->TaxAmount, $amount_attributes);

                    $item = self::createElementIfNotEmpty($doc, $invoiceline, 'cac:Item');
                        self::createElementIfNotEmpty($doc, $item, 'cbc:Description', $line->Item->Description);
                        self::createElementIfNotEmpty($doc, $item, 'cbc:Name', $line->Item->Name);

                        # Product number
                        if (!empty($line->Item->SellersItemIdentification->ID)) {
                            $cac_sellers_item_identification = self::createElementIfNotEmpty($doc, $item, 'cac:SellersItemIdentification');
                                self::createElementIfNotEmpty($doc, $cac_sellers_item_identification, 'cbc:ID', $line->Item->SellersItemIdentification->ID);
                        }

                        # Add UNSPSC
                        if($line->Item->CommodityClassification->UNSPSC->ItemClassificationCode) {
                            $cac_commodity_classification = self::createElementIfNotEmpty($doc, $item, 'cac:CommodityClassification');
                                $item_classification_code_attributes = array('listName' => 'UNSPSC', 'listVersionID' => '7.0401');
                                self::createElementIfNotEmpty($doc, $cac_commodity_classification, 'cbc:ItemClassificationCode', $line->Item->CommodityClassification->UNSPSC->ItemClassificationCode, $item_classification_code_attributes);
                        }

                        $cac_classified_tax_category = self::createElementIfNotEmpty($doc, $item, 'cac:ClassifiedTaxCategory');
                            self::createElementIfNotEmpty($doc, $cac_classified_tax_category, 'cbc:ID', $line->Item->ClassifiedTaxCategory->ID);
                            self::createElementIfNotEmpty($doc, $cac_classified_tax_category, 'cbc:Percent', $line->Item->ClassifiedTaxCategory->Percent);
                            $cac_tax_scheme = self::createElementIfNotEmpty($doc, $cac_classified_tax_category, 'cac:TaxScheme');
                                self::createElementIfNotEmpty($doc, $cac_tax_scheme, 'cbc:ID', 'VAT');

                    # Price
                    $price = self::createElementIfNotEmpty($doc, $invoiceline, 'cac:Price');
                        $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                        self::createElementIfNotEmpty($doc, $price, 'cbc:PriceAmount', $line->Price->PriceAmount, $amount_attributes);
                        $quantity_attributes = array('unitCode' => 'NAR', 'unitCodeListID' => 'UNECERec20');
                        self::createElementIfNotEmpty($doc, $price, 'cbc:BaseQuantity', $line->Price->BaseQuantity, $quantity_attributes);

                        ############################################################################################
                        # Allowances/Charges on invoiceline price
                        if(count($line->Price->AllowanceCharge)) {
                            foreach($line->Price->AllowanceCharge as $allowance_charge) {
                                if ($allowance_charge->Amount != 0) {
                                    $allowance_charge_cac = self::createElementIfNotEmpty($doc, $price, 'cac:AllowanceCharge');
                                        self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:ChargeIndicator', (($allowance_charge->ChargeIndicator == '1')?'true':'false'));
                                        self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:AllowanceChargeReason', $allowance_charge->AllowanceChargeReason);
                                        $amount_attributes = array('currencyID' => $InvoiceO->DocumentCurrencyCode);
                                        self::createElementIfNotEmpty($doc, $allowance_charge_cac, 'cbc:Amount', $allowance_charge->Amount, $amount_attributes);
                                }
                            }
                        }
            }
        } else {
            # No invoice lines found. The invoice was not sent.
            $_lib['message']->add('Ingen fakturalinjer funnet. Fakturaen ble ikke sendt.');
            return false;
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
        if ($xml == false) return;

        #$_lib['message']->add("FB->write1()");

        #print "<br>\n<br>\n$xml\n<br>\n<br>";

        $page = "/rest/invoices.xml";
        $url  = "$this->protocol://$this->host$page";

        if (isset($_SESSION['oauth_invoice_sent']) && !isset($_SESSION['sent_aga_to_fakturabank'])) {
          unset($_SESSION['oauth_invoice_sent']);
          $data = $_SESSION['oauth_resource'];
          unset($_SESSION['oauth_resource']);
        }
        else {
          $_SESSION['oauth_action'] = 'send_invoice';
          $_SESSION['oauth_invoice_object'] = $InvoiceO;
          $_SESSION['oauth_invoice_sent'] = true;
          $oauth_client = new lodo_oauth();
          $oauth_client->post_resources($url, array("xml" => $xml));
          $data = $_SESSION['oauth_resource'];
        }
        $this->save_invoice_export_data($data);
        return true;
    }

    public function save_invoice_export_data($data) {
      global $_lib;
      if ($data['code'] != 201) { // not created
        if (isset($_SESSION['altinn_invoice_sending']) && $_SESSION['altinn_invoice_sending']) {
          $result = simplexml_load_string($data['result']);
          $insert_query = "INSERT INTO altinnlog
            (Type, Class, Message, PersonID, AltinnReference)
            VALUES
            ('ERROR', 'Failed AGA or FTR export to fakturabank', '" . $result->message . "', " . $_lib['sess']->get_person('PersonID') . ", " . $_SESSION['altinn_invoice_reference'] . ")";
          $_lib['db']->db_query($insert_query);
          unset($_SESSION['altinn_invoice_sending']);
        }
        $invoice_type = $_SESSION['altinn_invoice_type'] ? $_SESSION['altinn_invoice_type'].": " : "";
        $_SESSION['oauth_invoice_error'][] = $invoice_type."Error: " . $data['result'];
        # Error: Insufficient rights in fakturaBank
        if ($data['code'] == 403) $_SESSION['oauth_invoice_error'][] = $invoice_type. "Error: Utilstrekkelige rettigheter i fakturabank!";
      }
      else {
        if (isset($_SESSION['altinn_invoice_sending']) && $_SESSION['altinn_invoice_sending']) {
          $AltinnReport4ID = $_SESSION['altinn_invoice_reference'];
          $invoice_type = $_SESSION['altinn_invoice_type'];
          if ($invoice_type == "AGA" || $invoice_type == "FTR") {
            $dataH = array();
            $dataH['AltinnReport4ID'] = $AltinnReport4ID;
            $dataH['SentToFakturabankBy'] = $_lib['sess']->get_person('PersonID');
            $dataH['SentToFakturabankAt'] = strftime("%F %T");
            $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'altinnReport4', 'debug' => false));
          }
          unset($_SESSION['altinn_invoice_sending']);
          $_SESSION['oauth_invoice_error'][] = $_SESSION['altinn_invoice_type'].": Success";
          unset($_SESSION['altinn_invoice_type']);
        } else {
          $dataH = array();
          $dataH['InvoiceID']             = $_SESSION['oauth_invoice_id'];
          $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
          $dataH['FakturabankDateTime']   = strftime("%F %T");
          $result_invoice = $_lib['db']->db_query("select * from invoiceout where InvoiceID=" . (int) $dataH['InvoiceID']);
          $invoice = $_lib['db']->db_fetch_object($result_invoice);
          if (!$invoice->Locked) {
            $dataH['Locked']   = 1;
            $dataH['LockedAt'] = strftime("%F %T");
            $dataH['LockedBy'] = $_lib['sess']->get_person('PersonID');
          }
          $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
          $invoice_type = $_SESSION['altinn_invoice_type'] ? ($_SESSION['altinn_invoice_type'].": ") : "";
          $_SESSION['oauth_invoice_error'][] = $invoice_type."Success";
        }
      }
      if (isset($_SESSION['sending_aga_ftr_to_fakturabank'])) {
        if(isset($_SESSION['sending_aga_to_fakturabank'])) {
          unset($_SESSION['sending_aga_to_fakturabank']);
          $_SESSION['sent_aga_to_fakturabank'] = true;
          $AltinnReport4ID = $_SESSION['altinn_invoice_reference'];
          header("Location: ". $_lib['sess']->dispatchs . "t=altinnsalary.show4&action_invoice_fakturabanksend_altinn_ftr=1&AltinnReport4ID=". $AltinnReport4ID);
          die();
        } else if(isset($_SESSION['sending_ftr_to_fakturabank'])) {
          unset($_SESSION['sent_aga_to_fakturabank']);
          unset($_SESSION['sending_ftr_to_fakturabank']);
        }
      }
      unset($_SESSION['altinn_invoice_reference']);
    }

    public function updateCarFromFakturabank($CarCode, $CarID) {
      global $_lib;

      $page = "rest/cars.xml?orgno=". $this->OrgNumber ."&code=". $CarCode;
      $url = $this->construct_fakturabank_url($page);

      $_SESSION['oauth_car_code'] = $CarCode;
      $_SESSION['oauth_car_id']   = $CarID;

      if (isset($_SESSION['oauth_car_info_fetched'])) {
        unset($_SESSION['oauth_car_info_fetched']);
        $data = $_SESSION['oauth_resource']['result'];
        unset($_SESSION['oauth_resource']);
      }
      else {
        $_SESSION['oauth_action'] = 'get_car_info';
        $oauth_client = new lodo_oauth();
        $_SESSION['oauth_car_info_fetched'] = true;
        $data = $oauth_client->get_resources($url);
      }

      $result = $data;
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

?>
