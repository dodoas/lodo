<?
/*******************************************************************************
* Lodo functionality
*
* @package lodo_core0_invoice
* @version  $Id:
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.empatix.com/ Empatix AS, 1994-2005, post@empatix.com
*
* $invoiceH = array();
* $InvoiceID = $invoice = new invoice();
* $invoice->create();
* $invoiceH['invoice_InvoiceID_' . $InvoiceID] = $InvoiceID;
* $line1ID = $invoice->linenew();
* $invoiceH['invoice_InvoiceID_' . $Line1ID] = $InvoiceID;
* #Mere data
* $line2ID = $invoice->linenew();
* $invoiceH['invoice_InvoiceID_' . $Line1ID] = $InvoiceID;
* #Mere data
* $line3ID = $invoice->linenew();
* $invoiceH['invoice_InvoiceID_' . $Line1ID] = $InvoiceID;
* #Mere data
* $line4ID = $invoice->linenew();
* $invoiceH['invoice_InvoiceID_' . $Line1ID] = $InvoiceID;
* #Mere data
* $invoice->update($invoiceH);
*/

includelogic('exchange/exchange');
includelogic('fakturabank/fakturabank');
includelogic('altinnsalary/files');
includelogic('kid/kid');
includelogic("accountplan/scheme");
includelogic("kommune/kommune");

class invoice {
    public $InvoiceID       = 0;
    public $CustomerAccountPlanID   = 0;
    public $JournalID       = 0;
    public $VoucherType     = 'S';
    public $table_head      = 'invoiceout';
    public $table_line      = 'invoiceoutline';
    public $allowance_charge_table      = 'invoiceallowancecharge';
    public $line_allowance_charge_table = 'invoicelineallowancecharge';
    private $headH          = array();
    private $lineH          = array();
    private $debug          = false;

    /*******************************************************************************
    * constructor
    * @param
    * @return
    */
    function __construct($args) {
        #print "Init\n";
        #print_r($args);
        foreach($args as $key => $value) {
            $this->{$key} = $value;
            #print "$key = $value<br>\n";
        }

        if(!$this->JournalID)
            $this->JournalID = $this->InvoiceID;
        #print "ferdig<br />\n";
    }

    function init($args) {
        global $_lib, $accounting;
        #Read the invoice to memory if it exists
        #print "xx0<br>\n";
        $this->clear_line();

        #print "INVOICE_ID: $this->InvoiceID<br><br>\n";

        if($this->InvoiceID) {
            #print "xx1<br>\n";
            $result_head = $_lib['db']->db_query("select * from $this->table_head where InvoiceID=" . (int) $this->InvoiceID);
            #print "result_head: select * from $this->table_head where InvoiceID=" . (int) $this->InvoiceID . "<br>\n";
            $result_line = $_lib['db']->db_query("select * from $this->table_line where InvoiceID=" . (int) $this->InvoiceID . " and Active=1");
            #print "xx3<br>\n";

            $headH = $_lib['db']->db_fetch_assoc($result_head);
            //print_r($headH);

            /*
             * InvoiceDate is needed by set_line to select correct VAT
             */
            if(isset($headH['InvoiceDate'])) {
                $this->set_head(array('InvoiceDate' => $headH['InvoiceDate']));
            }

            while($lineH = $_lib['db']->db_fetch_assoc($result_line)) {
                $this->set_line($lineH);
            }
        }

        if(!$result_head){

            /* Set default values for invoice head*/
            $headH['InsertedByPersonID']      = $_lib['sess']->get_person('PersonID');
            $headH['UpdatedByPersonID']      = $_lib['sess']->get_person('PersonID');
            $headH['SalePersonID']           = $_lib['sess']->get_person('PersonID');

            #print "\n\nFantes ikke: " . $_lib['sess']->get_companydef('CompanyID');
            #print_r($headH);

            $headH['Active']                 = 1;
            if(isset($_COOKIE["invoice_voucher_date"])) {
                $headH['InvoiceDate']            = $_COOKIE["invoice_voucher_date"];
            }
            else {
                $headH['InvoiceDate']            = $_lib['sess']->get_session('Date');
            }

            $headH['InsertedDateTime']       = $_lib['sess']->get_session('Date');
            #$headH['DueDate']               = $_lib['sess']->get_session('Date');
            $headH['PaymentDate']            = $_lib['sess']->get_session('Date');
            $headH['Status']                 = 'progress';
            $headH['OrderDate']   = $_lib['sess']->get_session('Date');
            $headH['DeliveryDate']= $_lib['sess']->get_session('DateTo');
        }

        //#Possible to extend or alter parameters here
        //#Set default parameters
        $accountplan = $accounting->get_accountplan_object($this->CustomerAccountPlanID);

        $headH['IName']                 = $accountplan->AccountName;
        $headH['DName']                 = $accountplan->AccountName;

        $headH['DAddress']              = empty($args['invoiceout_DAddress_' . $this->InvoiceID]) ? $headH['DAddress'] : $args['invoiceout_DAddress_' . $this->InvoiceID];
        $headH['DZipCode']              = empty($args['invoiceout_DZipCode_' . $this->InvoiceID]) ? $headH['DZipCode'] : $args['invoiceout_DZipCode_' . $this->InvoiceID];
        $headH['DCity']                 = empty($args['invoiceout_DCity_' . $this->InvoiceID]) ? $headH['DCity'] : $args['invoiceout_DCity_' . $this->InvoiceID];
        $headH['DCountryCode']          = empty($args['invoiceout_DCountryCode_' . $this->InvoiceID]) ? $headH['DCountryCode'] : $args['invoiceout_DCountryCode_' . $this->InvoiceID];

        if($accountplan->EnableInvoicePoBox) {
          $headH['IPoBox']              = $accountplan->IPoBox;
          $headH['IPoBoxCity']          = $accountplan->IPoBoxCity;
          $headH['IPoBoxZipCode']       = $accountplan->IPoBoxZipCode;
          $headH['IPoBoxZipCodeCity']   = $accountplan->IPoBoxZipCodeCity;
          $headH['IAddress']            = '';

        } else {
          $headH['IAddress']            = $accountplan->Address;
          $headH['IPoBox']              = '';
          $headH['IPoBoxCity']          = '';
          $headH['IPoBoxZipCode']       = '';
          $headH['IPoBoxZipCodeCity']   = '';
        }

        $headH['IZipCode']              = $accountplan->ZipCode;
        $headH['ICity']                 = $accountplan->City;

        $headH['ICountryCode']              = $accountplan->CountryCode;
        $headH['DEmail']                = $accountplan->Email;
        $headH['IEmail']                = $accountplan->Email;

        if($this->CustomerAccountPlanID && ($headH['DueDate'] == '0000-00-00' || !$headH['DueDate']))
        {
            if(is_numeric($accountplan->CreditDays) and $accountplan->CreditDays > 0)
            {
                $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], $accountplan->CreditDays);
            } elseif($_lib['sess']->get_session('CreditDays') > 0) {
                #Get credit days from own company
                $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], $_lib['sess']->get_session('CreditDays'));
            } else {
                $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], 0);
            }
        }

        if(!$headH['ProjectID'] && $accountplan->ProjectID)
            $headH['ProjectID'] = $accountplan->ProjectID;

        if(!$headH['DepartmentID'] && $accountplan->DepartmentID)
            $headH['DepartmentID'] = $accountplan->DepartmentID;

        /*$args['invoiceout_ICountry_'.$this->InvoiceID] = $accountplan->address;
        $args['invoiceout_DCountry_'.$this->InvoiceID] = $accountplan->address;*/
        $headH['IEmail']              = $accountplan->Email;
        $headH['DEmail']              = $accountplan->Email;
        $headH['CompanyID']           = $this->CustomerAccountPlanID;
        $headH['CreatedByPersonID']   = $_lib['sess']->get_person('PersonID');
        $headH['CreatedDateTime']     = $_lib['sess']->get_session('Date');
        $headH['BankAccount']         = $accountplan->DomesticBankAccount;

        if($this->CustomerAccountPlanID) {
            if( ($accountplan->EnableCredit == 1) && (strlen($headH['DueDate']) == 0))
            {
                $row = $_lib['storage']->get_row(array('query' => "SELECT ADDDATE('".$headH['InvoiceDate']."', INTERVAL ".$accountplan->CreditDays." DAY) as duedate"));
                $headH['DueDate'] = $row->duedate;
            }
            elseif( ($accountplan->EnableCredit == 0) && (strlen($headH['DueDate']) == 0) )
            {
                $headH['DueDate'] = $headH['InvoiceDate'];
            }
        }

        $headH['FromCompanyID']         = $_lib['sess']->get_companydef('CompanyID');
        if(strlen($headH['Period']) != 7) {
            if(isset($_COOKIE['invoice_period'])) {
                $headH['Period']            = $_COOKIE['invoice_period'];
            }
            else {
                $headH['Period']            = $_lib['date']->get_this_period($headH['InvoiceDate']);
            }
        }
        #print_r($headH);
        if(!$headH['CustomerAccountPlanID'])
            $_lib['message']->add(array('message' => "Du m&aring; velge kunden som skal motta fakturaen"));

        unset($headH['TotalCustPrice']);
        unset($headH['TotalVat']);

        if($_lib['setup']->get_value('kid.accountplanid') || $_lib['setup']->get_value('kid.invoiceid')) {
            $kidO         = new lodo_logic_kid();
            $headH['KID'] = $kidO->generate($headH);
        }

        $this->set_head($headH);
    }

    /*******************************************************************************
    * Update invoice based on std
    * @param array
    * @return
    */

    function update($args) {
        global $_lib, $_SETUP, $accounting;

        #enrich args with address data
        self::enrichArgsWithAddressFields($args);
        #Update multi into db to support old format
        #print_r($args);
        self::addVATPercentToAllowanceCharge($args);
        self::invertAllAllowanceAmounts($args);
        $tables_to_update = array(
          'invoiceout'                 => 'InvoiceID',
          'invoiceoutline'             => 'LineID',
          'invoiceallowancecharge'     => 'InvoiceAllowanceChargeID',
          'invoicelineallowancecharge' => 'InvoiceLineAllowanceChargeID');
        $_lib['db']->db_update_multi_table($args, $tables_to_update);
        
        #Then read everything from disk and correct calculations
        $this->init($args);
        $this->make_invoice();

        // Since the line above only updates table columns, REPLACE INTO will create in case there is no record to update.
        $invoice_id = $args['InvoiceID'];
        if ($args["PrintInterval"]) $invoiceoutprint_date = date('Y-m-d', strtotime($this->headH["InvoiceDate"]. ' - '.$args["PrintInterval"].' days'));
        else $invoiceoutprint_date = $args["invoiceoutprint_InvoicePrintDate_". $invoice_id];
        $replace_invoiceoutprint = sprintf("REPLACE INTO invoiceoutprint (InvoiceID, InvoicePrintDate) VALUES ('%d', '%s');", $invoice_id, $invoiceoutprint_date);
        $_lib['db']->db_query($replace_invoiceoutprint);
    }

    // Since it is then simpler to load for XML, and we only show it inverted in view/edit page
    function invertAllAllowanceAmounts(&$args) {
        global $_lib;

        foreach ($args as $arg_key => $arg_val) {
          if ((strpos($arg_key, 'invoiceallowancecharge_Amount_') !== false) || (strpos($arg_key, 'invoicelineallowancecharge_Amount_') !== false)) {
            $charge_indicator = $args[preg_replace('/Amount/', 'ChargeIndicator', $arg_key)];
            $amount = $_lib['convert']->Amount($args[$arg_key]);
            if ($charge_indicator == 0) $args[$arg_key] = -$amount;
          }
        }
    }

    function addVATPercentToAllowanceCharge(&$args) {
        if (!is_numeric($args['InvoiceID'])) {
            return;
        }

        global $_lib;

        $InvoiceID = $args['InvoiceID'];
        $InvoiceDate = $args['invoiceout_InvoiceDate_'.$InvoiceID];
        foreach ($args as $arg_key => $arg_val) {
          if (strpos($arg_key, 'invoiceallowancecharge_VatID_') !== false) {
            $select_vat = "select * from vat where VatID = '" . $arg_val . "' and Type = 'sale' and ValidFrom <= '" . $InvoiceDate . "' and ValidTo >= '" . $InvoiceDate . "'";
            $vat = $_lib['db']->get_row(array('query' => $select_vat));
            $args[preg_replace('/VatID/', 'VatPercent', $arg_key)] = $vat->Percent;
          }
        }
    }

    function enrichArgsWithAddressFields(&$args) {
        if (!is_numeric($args['InvoiceID'])) {
            return;
        }

        global $_lib;

        $invoiceout_due_date_key = "invoiceout_DueDate_" . $args['InvoiceID'];
        $prefix = isset($args[$invoiceout_due_date_key]) ? "invoiceout" : "invoicein";

        $get_invoicefrom = "select IName as FromName, IAddress as FromAddress, Email, WWW, IZipCode as Zip, ICity as City, ICountryCode as CountryCode, Phone, Fax, BankAccount, Mobile, OrgNumber, VatNumber from company where CompanyID='" . $_lib['sess']->get_companydef('CompanyID') . "'";
        $row_from = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

        $args[$prefix . "_SName_" . $args['InvoiceID']] = $row_from->FromName;
        $args[$prefix . "_SAddress_" . $args['InvoiceID']] = $row_from->FromAddress;
        $args[$prefix . "_SZipCode_" . $args['InvoiceID']] = $row_from->Zip;
        $args[$prefix . "_SCity_" . $args['InvoiceID']] = $row_from->City;
        $args[$prefix . "_SCountryCode_" . $args['InvoiceID']] = $row_from->CountryCode;
        $args[$prefix . "_SPhone_" . $args['InvoiceID']] = $row_from->Phone;
        $args[$prefix . "_SFax_" . $args['InvoiceID']] = $row_from->Fax;
        $args[$prefix . "_SBankAccount_" . $args['InvoiceID']] = $row_from->BankAccount;
        $args[$prefix . "_SEmail_" . $args['InvoiceID']] = $row_from->Email;
        $args[$prefix . "_SMobile_" . $args['InvoiceID']] = $row_from->Mobile;
        $args[$prefix . "_SWeb_" . $args['InvoiceID']] = $row_from->WWW;
        $args[$prefix . "_SOrgNo_" . $args['InvoiceID']] = $row_from->OrgNumber;
        $args[$prefix . "_SVatNo_" . $args['InvoiceID']] = $row_from->VatNumber;

        $customer_accountplan_id = $args["invoiceout_CustomerAccountPlanID_" . $args["InvoiceID"]];
        if (!is_numeric($customer_accountplan_id)) {
            return;
        }
        $get_invoiceto = "select * from accountplan where AccountPlanID='" . $customer_accountplan_id . "'";
        $row_to = $_lib["storage"]->get_row(array("query" => $get_invoiceto));
        $args[$prefix . "_IOrgNo_" . $args['InvoiceID']] = $row_to->OrgNumber;
        $args[$prefix . "_IVatNo_" . $args['InvoiceID']] = $row_to->VatNumber;
        $args[$prefix . "_IMobile_" . $args['InvoiceID']] = $row_to->Mobile;
        $args[$prefix . "_IWeb_" . $args['InvoiceID']] = $row_to->Web;
        $args[$prefix . "_Phone_" . $args['InvoiceID']] = $row_to->Phone;
        $args[$prefix . "_IAddress_" . $args['InvoiceID']] = $row_to->Address;
        $args[$prefix . "_IZipCode_" . $args['InvoiceID']] = $row_to->ZipCode;
        $args[$prefix . "_IPoBox_" . $args['InvoiceID']] = $row_to->PoBox;
        $args[$prefix . "_IPoBoxCity_" . $args['InvoiceID']] = $row_to->PoBoxCity;
        $args[$prefix . "_ICity_" . $args['InvoiceID']] = $row_to->City;
        $args[$prefix . "_IPoBoxZipCode_" . $args['InvoiceID']] = $row_to->PoBoxZipCode;
        $args[$prefix . "_IPoBoxZipCodeCity_" . $args['InvoiceID']] = $row_to->PoBoxZipCodeCity;
        $args[$prefix . "_ICountryCode_" . $args['InvoiceID']] = $row_to->CountryCode;
        $args[$prefix . "_IEmail_" . $args['InvoiceID']] = $row_to->Email;
        $args[$prefix . "_BankAccount_" . $args['InvoiceID']] = $row_to->BankAccount;

        // Set delivery address fields. If account plan has changed we set delivery address to customer address which is default.
        $get_invoice = "select * from invoiceout where InvoiceID = " . $args['InvoiceID'];
        $invoice_row = $_lib["storage"]->get_row(array("query" => $get_invoice));
        $customer_accountplan_changed = $invoice_row->CustomerAccountPlanID != $customer_accountplan_id;
        $args[$prefix . "_DAddress_" . $args['InvoiceID']] = ($customer_accountplan_changed) ? $row_to->Address : $args['_DAddress'];
        $args[$prefix . "_DZipCode_" . $args['InvoiceID']] = ($customer_accountplan_changed) ? $row_to->ZipCode : $args['_DZipCode'];
        $args[$prefix . "_DCity_" . $args['InvoiceID']] = ($customer_accountplan_changed) ? $row_to->City : $args['_DCity'];
        $args[$prefix . "_DCountryCode_" . $args['InvoiceID']] = ($customer_accountplan_changed) ? $row_to->CountryCode : $args['_DCountryCode'];
    }

    # check if all the needed fields are set in oreder to create a valid xml for sending to fakturabank
    function fakturabank_send_precheck()
    {
      global $_lib;

      $ready_to_send = true;
      $error_messages = array();
      # required fields
      $head_required_fields = array('InvoiceDate', 'SName', 'SCity', 'SZipCode', 'IName', 'ICity', 'IZipCode', array('Phone', 'IMobile', 'IFax', 'IEmail'), 'DueDate', 'SBankAccount', 'DAddress', 'DZipCode', 'DCity', 'DCountryCode');
      $line_required_fields = array('ProductID', 'QuantityDelivered', 'ProductName', 'UnitCustPrice');

      # Translations for mandatory fields
      $translated_head_required_fields = array(
        'InvoiceDate'  => "Fakturadato",
        'SName' => "Leverandør navn",
        'SCity' => "Leverandør by",
        'SZipCode' => "Leverandør porstnr",
        'IName' => "Kunde navn",
        'ICity' => "Kunde by",
        'IZipCode' => "Kunde postnr",
        'CustomerAddressArray' => "Telefon, Mobil, Fax eller Email.",
        'DueDate' => "Forfallsdato",
        'SBankAccount' => "Bankkonto",
        'DAddress' => "Leveringsadresse(Adresse)",
        'DZipCode' => "Leveringsadresse(Postnummer)",
        'DCity' => "Leveringsadresse(Posted)",
        'DCountryCode' => "Leveringsadresse(Land)"
      );

      $translated_line_required_fields = array(
        'ProductID' => "Produkt",
        'QuantityDelivered' => "Antall Levert",
        'ProductName' => "Produkt navn",
        'UnitCustPrice' => "Enhets pris"
      );


      # main/head fields
      foreach($head_required_fields as $field_name) {
        $is_array = is_array($field_name);
        $is_set = false;
        if ($is_array) {
          foreach($field_name as $field) {
            $is_set = $is_set || !empty($this->headH[$field]);
          }
        }
        else $is_set = !empty($this->headH[$field_name]);
        if (in_array($field_name, array('InvoiceDate', 'DueDate'))) {
          $is_set = !($this->headH[$field_name] == '0000-00-00');
        }
        if (!$is_set) {
          $ready_to_send = false;
          if ($is_array) $error_messages[] = 'F&oslash;r du kan sende til Fakturabank er du n&oslash;dt til &aring; fylle ut f&oslash;lgene felt: ' . $translated_head_required_fields['CustomerAddressArray'];
          else $error_messages[] = 'F&oslash;r du kan sende til Fakturabank er du n&oslash;dt til &aring; fylle ut f&oslash;lgene felt: ' . $translated_head_required_fields[$field_name];
        }
      }
      # if no invoice lines
      if (!(count($this->lineH) > 0)) {
        $error_messages[] = 'F&oslash;r du kan sende til Fakturabank er du n&oslash;dt til &aring; fylle ut ett av f&oslash;lgene felt!';
        return array(false, $error_messages);
      }
      # if any invoice lines, check each for required fields
      $line_count = 1;
      foreach($this->lineH as $line) {
        foreach($line_required_fields as $field_name) {
          if (in_array($field_name, array('QuantityDelivered', 'UnitCustPrice'))) $is_set = $line[$field_name] != 0;
          else $is_set = !empty($line[$field_name]);
          if (!$is_set) {
            $ready_to_send = false;
            $error_messages[] = 'F&oslash;r du kan sende til Fakturabank er du n&oslash;dt til &aring; fylle: ' . $translated_line_required_fields[$field_name] . ' p&aring; faktura  linje ' . $line_count . '.';
          }
        }
        $line_count++;
      }
      return array($ready_to_send, $error_messages);
    }

    function make_invoice()
    {
        global $_lib, $accounting;

        #print_r($this->lineH);

        if(count($this->lineH) > 0) #det må minst være en linje i fakturaen
        {
            #if($this->InvoiceID) { #Delete old invoice
            #    $this->delete_invoice();
            #}

            unset($this->headH['DeliveryDate']);
            unset($this->headH['OrderDate']);
            unset($this->headH['inline']);

            $headH = $this->headH;

            if($this->debug) print_r($headH);
            $this->InvoiceID = $_lib['storage']->store_record(array('data' => $headH, 'table' => $this->table_head, 'debug' => false));

            #print_r($this->lineH);

            if (!empty($this->invoice_allowance_charge)) {
              foreach($this->invoice_allowance_charge as $allowance_chargeH) {
                  $allowance_chargeH['InvoiceID'] = $this->InvoiceID;
                  $_lib['storage']->store_record(array('data' => $allowance_chargeH, 'table' => $this->allowance_charge_table, 'debug' => false));
              }
            }

            $i = 0;
            /* Generate invoice line */
            #print "Generer fakturalinjer\n\n";
            foreach($this->lineH as $lineH)
            {
                $lineH['InvoiceID'] = $this->InvoiceID;
                if($this->debug) print_r($lineH);
                $InvoiceLineID = $_lib['storage']->store_record(array('data' => $lineH, 'table' => $this->table_line, 'debug' => false));
                if (!empty($this->invoice_line_allowance_charge[$i])) {
                  foreach($this->invoice_line_allowance_charge[$i] as $line_allowance_chargeH) {
                      $line_allowance_chargeH['InvoiceLineID'] = $InvoiceLineID;
                      $_lib['storage']->store_record(array('data' => $line_allowance_chargeH, 'table' => $this->line_allowance_charge_table, 'debug' => false));
                  }
                }
                $i++;
            }

            if((count($this->lineH) == 1 && $this->lineH[0]['ProductID'] > 0 && $this->lineH[0]['QuantityDelivered'] != 0) || count($this->lineH) > 1) {
                $this->journal();
            } else {
                $_lib['message']->add(array('message' => 'Det mangler for mange opplysninger til at fakturaen kan bli bilagsf&oslash;rt'));
            }

        }
        else
        {

            #print "Sletter bilag pga mangel pŒ linjer: $this->JournalID, $this->VoucherType<br />";
            #Slett billag hvis det ikke har noen linjer
            $accounting->delete_journal($this->JournalID, $this->VoucherType);

            // update Total sums for invoice to 0
            $this->InvoiceID = $_lib['storage']->store_record(array('data' => $this->headH, 'table' => $this->table_head, 'debug' => false));

            $_lib['message']->add(array('message' => 'Ingen fakturalinjer registrert, fakturabilag ikke opprettet'));
            $this->success = true;
        }
        #print "'JournalID'=>$this->InvoiceID, 'VoucherType'=>'S', 'AccountPlanID'=>$this->AccountPlanID";
        #$this->journal(array('JournalID'=>$this->InvoiceID, 'VoucherType'=>'S', 'AccountPlanID'=>$this->AccountPlanID));

        return $this->InvoiceID;
    }


    /*******************************************************************************
    * Create a new invoice and a empty invoice line
    * @param array(Date, Status, Active, FromCompanyID, InvoiceDate, PaymentDate, DeliveryDate);
    * @return InvoiceID
    */
    function create($args)
    {
        global $_lib, $accounting;
        list($args['InvoiceID'], $message)                = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $this->VoucherType));

        $this->init($args);
        // unset so we don't try to save this to invoice table in set_head function
        if (isset($args["voucher_period"])) unset($args["voucher_period"]);
        if (isset($args["voucher_date"])) unset($args["voucher_date"]);
        $args['CurrencyID'] = exchange::getLocalCurrency();
        $this->set_head($args);
        $this->set_line(array('Active' => 1));

        // Check if entry already exists in invoiceoutprint table (might be the case for an "unsaved") invoice.
        $query_invprint = "select * from invoiceoutprint where InvoiceID='" . $args['InvoiceID'] . "'";
        $result2 = $_lib['db']->db_query($query_invprint);
        if (!$result2 || !($row = $_lib['db']->db_fetch_assoc($result2))) {
            /* create the invoiceoutprint row */
            $s = sprintf("INSERT INTO invoiceoutprint (`InvoiceID`, `InvoicePrintDate`) VALUES ('%d', '0000-00-00');",
                         $args['InvoiceID']);
            $_lib['db']->db_query($s);

        }

        $this->make_invoice();
        return $args['InvoiceID'];
    }

    function copy_from_recurring($recurring_id, $customer_comment = false)
    {
        global $_lib, $accounting;
        $this->OldInvoiceID = $this->InvoiceID;

        /* henter recurring-linjen */
        $s = "SELECT * FROM recurring WHERE RecurringID = '$recurring_id'";
        $r = $_lib['db']->db_query($s);
        $recurring = $_lib['db']->db_fetch_assoc($r);

        $this->clear_line();

        $accountplan = $accounting->get_accountplan_object($this->CustomerAccountPlanID);

        list($this->InvoiceID, $message) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $this->VoucherType));

        $sql_head = "select * from recurringout where RecurringID='$recurring_id' and Active != 0";
        $result_head = $_lib['db']->db_query($sql_head);
        $headH = $_lib['db']->db_fetch_assoc($result_head);

        unset($headH['RecurringID']);

        if($customer_comment !== false) {
            $headH['CommentCustomer'] = $customer_comment;
        }

        $headH['InvoiceID']           = $this->InvoiceID;
        $headH['OrderDate']           = $_lib['sess']->get_session('LoginFormDate');
        $headH['Period']              = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));

        $headH['Status']              = "progress";
        $headH['Active']              = 1;
        $headH['CreatedDateTime']     = $_lib['sess']->get_session('Date');
        $headH['InvoiceDate']         = $_lib['sess']->get_session('LoginFormDate');

        /* legger pÃ¥ print-intervalet om det finnes for denne recurringinvoice'en */
        if($recurring['PrintInterval'])
        {
//            $headH['InvoiceDate'] = $_lib['date']->add_Days( $recurring['LastDate'], $recurring['PrintInterval'] );
            $headH['InvoiceDate'] = $recurring['LastDate'];
            $headH['Period']      = $_lib['date']->get_this_period($recurring['LastDate']);

            $s = sprintf("REPLACE INTO invoiceoutprint (`InvoiceID`, `InvoicePrintDate`)
				VALUES ('%d', DATE_SUB(DATE('%s'), INTERVAL %d DAY));",
                         $this->InvoiceID, $recurring['LastDate'], $recurring['PrintInterval']);
            $_lib['db']->db_query($s);
        }

        if( ($accountplan->EnableCredit == 1) )
        {
            if($accountplan->CreditDays <= 0) {
              $message .= "Kundenummer: $this->CustomerAccountPlanID har 0 dagers kreditt";
            }
            $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], $accountplan->CreditDays);
            // Rettet av Geir 06.02.2006. Den gamle (linjen nedenfor) beregnet feil.
            // $_lib['date']->add_Days($row_head->InvoiceDate, $accountplan->CreditDays);
        }
        elseif( ($accountplan->EnableCredit == 0) )
        {
            $headH['DueDate'] = $row_head->InvoiceDate;
        }

        $headH['PaymentDate']             = $_lib['sess']->get_session('DateTo');
        $headH['DeliveryDate']            = $_lib['sess']->get_session('DateTo');

        $this->set_head($headH);

        $query_invoiceline = "select * from recurringoutline where RecurringID='$recurring_id' and Active!=0 order by LineID asc";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        while($lineH = $_lib['db']->db_fetch_assoc($result2))
        {
            unset($lineH['LineID']); #This id is pk so we cannot copy it.
            unset($lineH['RecurringID']);
            #print "linje\n";
            #print_r($lineH);
            $this->set_line($lineH);
        }


        $this->make_invoice();
        return $this->InvoiceID;
    }

    /*******************************************************************************
    * Copy invoice
    * @param
    * @return
    */
    function copy()
    {
        global $_lib, $accounting;
        $this->OldInvoiceID = $this->InvoiceID;

        $this->clear_line();

        $accountplan = $accounting->get_accountplan_object($this->CustomerAccountPlanID);

        list($this->InvoiceID, $message)        = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $this->VoucherType));

        $sql_head = "select * from $this->table_head where InvoiceID='$this->OldInvoiceID' and Active != 0";
        $result_head = $_lib['db']->db_query($sql_head);
        $headH = $_lib['db']->db_fetch_assoc($result_head);

        $headH['InvoiceID']           = $this->InvoiceID;
        $headH['OrderDate']           = $_lib['sess']->get_session('LoginFormDate');
        $headH['Period']              = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));

        $headH['Status']              = "progress";
        $headH['Active']              = 1;
        $headH['Locked']              = 0; // hard code to unlocked since this is a new invoice
        $headH['FakturabankPersonID'] = 0; // hard code to nil since this is a new invoice
        $headH['FakturabankDateTime'] = null; // hard code to nil since this is a new invoice
        $headH['CreatedDateTime']     = $_lib['sess']->get_session('Date');
        $headH['InvoiceDate']         = $_lib['sess']->get_session('LoginFormDate');

        if( ($accountplan->EnableCredit == 1) )
        {
            if($accountplan->CreditDays <= 0) {
              $message .= "Kundenummer: $this->CustomerAccountPlanID har 0 dagers kreditt";
            }
            $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], $accountplan->CreditDays);
            // Rettet av Geir 06.02.2006. Den gamle (linjen nedenfor) beregnet feil.
            // $_lib['date']->add_Days($row_head->InvoiceDate, $accountplan->CreditDays);
        }
        elseif( ($accountplan->EnableCredit == 0) )
        {
            $headH['DueDate'] = $row_head->InvoiceDate;
        }

        $headH['PaymentDate']             = $_lib['sess']->get_session('DateTo');
        $headH['DeliveryDate']            = $_lib['sess']->get_session('DateTo');

        $this->set_head($headH);

        $this->invoice_allowance_charge = array();
        $query_invoice_allowance_charge = "select * from $this->allowance_charge_table where InvoiceID='$this->OldInvoiceID' and InvoiceType = 'out'";
        $result_allowance_charge = $_lib['db']->db_query($query_invoice_allowance_charge);
        while($allowance_chargeH = $_lib['db']->db_fetch_assoc($result_allowance_charge)) {
            unset($allowance_chargeH['InvoiceAllowanceChargeID']); #This id is pk so we cannot copy it.
            unset($allowance_chargeH['InvoiceID']);
            $this->invoice_allowance_charge[] = $allowance_chargeH;
        }

        $this->invoice_line_allowance_charge = array();
        $i = 0;

        $query_invoiceline = "select * from $this->table_line where InvoiceID='$this->OldInvoiceID' and Active!=0 order by LineID asc";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        while($lineH = $_lib['db']->db_fetch_assoc($result2))
        {
            $this->invoice_line_allowance_charge[$i] = array();
            $query_invoiceline_allowance_charge = "select * from $this->line_allowance_charge_table where InvoiceLineID='" . $lineH['LineID'] . "' and InvoiceType = 'out'";
            $result_line_allowance_charge = $_lib['db']->db_query($query_invoiceline_allowance_charge);
            while($line_allowance_chargeH = $_lib['db']->db_fetch_assoc($result_line_allowance_charge)) {
                unset($line_allowance_chargeH['InvoiceLineAllowanceChargeID']); #This id is pk so we cannot copy it.
                unset($line_allowance_chargeH['InvoiceLineID']);
                $this->invoice_line_allowance_charge[$i][] = $line_allowance_chargeH;
            }
            $i++;

            unset($lineH['LineID']); #This id is pk so we cannot copy it.
            unset($lineH['InvoiceID']);
            #print "linje\n";
            #print_r($lineH);
            $this->set_line($lineH, false);
        }

        $this->make_invoice();
        return $this->InvoiceID;
    }

    /*******************************************************************************
    * Delete invoice line's allowance/charge
    * @param
    * @return
    */
    function line_allowance_charge_delete($InvoiceLineAllowanceChargeID)
    {
        global $_lib;
        $query = "delete from $this->line_allowance_charge_table where InvoiceLineAllowanceChargeID = " . $InvoiceLineAllowanceChargeID;
        $ret = $_lib['db']->db_delete($query);

        $this->init(array());
        $this->make_invoice();
    }

    /*******************************************************************************
    * Delete invoice's allowance charge
    * @param
    * @return
    */
    function allowance_charge_delete($InvoiceAllowanceChargeID)
    {
        global $_lib;
        $query = "delete from $this->allowance_charge_table where InvoiceAllowanceChargeID = " . $InvoiceAllowanceChargeID;
        $ret = $_lib['db']->db_delete($query);

        $this->init(array());
        $this->make_invoice();
    }

    /*******************************************************************************
    * Delete invoiceline
    * @param
    * @return
    */
    function linedelete($LineID)
    {
        global $_lib;

        // Dependent destroy of all allowances/charges connected to this line
        $query="delete from $this->line_allowance_charge_table where InvoiceType = 'out' and InvoiceLineID = " . $LineID;
        $_lib['db']->db_delete($query);

        $query="update $this->table_line set Active=0 where LineID=" . $LineID;
        $ret = $_lib['db']->db_update($query);

        $_lib['message']->add(array('message' => "Linje $LineID p&aring; faktura $this->InvoiceID er slettet"));

        if($this->CustomerAccountPlanID == 0) {
            $query = sprintf(
                "SELECT I.CustomerAccountPlanID FROM
                      invoiceout I,
                      invoiceoutline IL
                    WHERE
                        IL.LineID = %d AND I.InvoiceID = IL.InvoiceID",
                $LineID
                );

            $row = $_lib['db']->get_row(array('query' => $query));

            $this->CustomerAccountPlanID = $row->CustomerAccountPlanID;
        }

        $this->init(array());
        $this->make_invoice();
    }

    /***************************************************************************
    * Create the invoice head
    * @param $head - object that contains all invoice head data
    */
    function set_head($head)
    {
        global $_lib;

        #print_r($head);
        #Computed default values

        # What about default values? More flexible with hashes but slower.
        foreach($head as $key => $value)
        {
            if($key != 'action_invoice_new')
                $this->headH[$key] = $value; #Could be a hash loop to preserve default values
            #print "$key = $value<br>\n";
        }
    }

    /***************************************************************************
    * Insert invoice line
    * @param $line - hash containing invoice line data
    */
    function clear_line()
    {
        $this->lineH            = array();
        $this->TotalCustPrice   = 0;
        $this->totalSum         = 0;
        $this->totalVat         = 0;
        $this->set_head(array('TotalCustPrice' => 0));
        $this->set_head(array('TotalVat' => 0));
        $this->set_head(array('TotalCostPrice' => 0));
    }

    /***************************************************************************
    * Insert invoice line
    * @param $line - hash containing invoice line data
    */
    function set_line($line, $update_totals = true)
    {
        global $_lib, $accounting;
        #What about setting line num automatically? Could trigger automatic preprosessing on a field basis

        $lineH = array();

        foreach($line as $key => $value) {
            $lineH[$key]  = $value;
        }

        if($line['ProductID'] > 0) {
        $query = "select * from product as P where P.ProductID='" . $line['ProductID'] . "'";
        #print "$query<br>";
        $product = $_lib['storage']->get_row(array('query' => $query));

        $accountplan = $accounting->get_accountplan_object($product->AccountPlanID);
        #print_r($accountplan);

        $VATID = (!is_null($accountplan->VatID) && $accountplan->VatID != 0) ? $accountplan->VatID : 10; // If no VAT category select VAT undefined = 0%
        $VAT = $accounting->get_vataccount_object(array('VatID' => $VATID, 'date' => $this->headH['InvoiceDate']));

        #print_r($product);

        if($line['QuantityDelivered'] == 0) #Rettet av Geir. Maa vare mulig aa lage kreditnota med minus i antall.
            $lineH['QuantityDelivered'] = 0;

        if($line['UnitCustPrice'] == 0)
            $lineH['UnitCustPrice'] = $product->UnitCustPrice;

        #if($line['Vat'] <= 0)
        $lineH['Vat']   = $VAT->Percent;
        $lineH['VatID'] = $VATID;

        if(!$line['ProductName'])
            $lineH['ProductName'] = $product->ProductName;
        } else {
            $_lib['message']->add(array('message' => 'InvoiceID ' . $this->InvoiceID . ':Du m&aring; velge produkter til alle fakturalinjene'));
        }

        if($lineH['QuantityDelivered'] == 0)
            $_lib['message']->add(array('message' => 'InvoiceID ' . $this->InvoiceID . ':Du m&aring; taste Antall produkter p&aring; fakturalinjen'));

        if($lineH['UnitCustPrice'] == 0)
            $_lib['message']->add(array('message' => 'InvoiceID ' . $this->InvoiceID . ':Du m&aring; sette en Enhetspris p&aring; fakturalinjen'));

        #exit;
        $tmpquant   = $lineH['QuantityDelivered'];
        $custprice  = $_lib['convert']->Amount($lineH['UnitCustPrice']);

        if (!empty($line['LineID']) && is_numeric($line['LineID'])) {
          $query = "select sum(if(ChargeIndicator = 1, Amount, -Amount)) as sum from invoicelineallowancecharge where InvoiceType = 'out' and AllowanceChargeType = 'line' and InvoiceLineID = " . $line['LineID'];
          $result = $_lib['storage']->get_row(array('query' => $query));
          $sum_line_allowance_charge = $result->sum;;
        } else {
          $sum_line_allowance_charge = 0.0;
        }

        if ($update_totals) {
          $this->totalSum += round($tmpquant * $custprice + $sum_line_allowance_charge, 2);
          $this->totalVat += round(($tmpquant * $custprice + $sum_line_allowance_charge) * ($lineH['Vat']/100), 2);
        }

        $this->lineH[] = $lineH;

        if (!empty($line['InvoiceID']) && is_numeric($line['InvoiceID'])) {
          $query = "select sum(if(ChargeIndicator = 1, Amount*(100+VatPercent)/100.0, -(Amount*(100+VatPercent)/100.0))) as sum, sum(if(ChargeIndicator = 1, Amount*VatPercent/100.0, -(Amount*VatPercent/100.0))) as vat_sum from invoiceallowancecharge where InvoiceType = 'out' and InvoiceID = " . $line['InvoiceID'];
          $result = $_lib['storage']->get_row(array('query' => $query));
          $sum_invoice_allowance_charge = $result->sum;
          $vat_sum_invoice_allowance_charge = $result->vat_sum;
        } else {
          $sum_invoice_allowance_charge = 0.0;
          $vat_sum_invoice_allowance_charge = 0.0;
        }

        if ($update_totals) {
          $this->TotalCustPrice = $this->totalSum + $this->totalVat + $sum_invoice_allowance_charge;
          #print "<b>TotalCustPrice: $this->TotalCustPrice</b><br>";
          #$this->headH[] = 199; #$this->TotalCustPrice;
          $this->set_head(array('TotalCustPrice' => $this->TotalCustPrice));
          $this->set_head(array('TotalVat' => $this->totalVat + $vat_sum_invoice_allowance_charge));
        }
    }

    /*******************************************************************************
    * Delete the entire invoice
    * @param
    * @return
    */
    function delete_invoice()
    {
        global $_lib, $accounting;

        # Dependent destroy of all things connected to invoice we are destroying
        $sql_delete_invoiceline_allowance_charge = "delete from $this->line_allowance_charge_table where InvoiceType = 'out' and InvoiceLineID in ( select LineID from invoiceoutline where InvoiceID = " . $this->InvoiceID .")";
        $_lib['db']->db_delete($sql_delete_invoiceline_allowance_charge);

        $sql_delete_invoice_allowance_charge = "delete from $this->allowance_charge_table where InvoiceType = 'out' and InvoiceID = " . $this->InvoiceID;
        $_lib['db']->db_delete($sql_delete_invoice_allowance_charge);

        $sql_delete_invoiceline = "delete from invoiceoutline where InvoiceID=" . $this->InvoiceID;
        $_lib['db']->db_delete($sql_delete_invoiceline);

        $sql_delete_invoice     = "delete from invoiceout where InvoiceID=" . $this->InvoiceID;
        $_lib['db']->db_delete($sql_delete_invoice);

        $sql_delete_print_date  = "delete from invoiceoutprint where InvoiceID=" . $this->InvoiceID;
        $_lib['db']->db_delete($sql_delete_print_date);

        #print "Sletter: $this->InvoiceID, $this->VoucherType<br>\n";
        $accounting->delete_journal($this->InvoiceID, $this->VoucherType);

        $_lib['message']->add(array('message' => "Faktura $this->InvoiceID er slettet"));

        return true;
    }

    /*******************************************************************************
    * Add a new invoice line allowance/charge
    * @param
    * @return
    */
    function line_allowance_charge_new($InvoiceLineID)
    {
        global $_lib;
        $allowance_chargeH['invoicelineallowancecharge_InvoiceType']         = 'out';
        $allowance_chargeH['invoicelineallowancecharge_InvoiceLineID']       = $InvoiceLineID;
        $allowance_chargeH['invoicelineallowancecharge_AllowanceChargeType'] = 'line';
        return $_lib['db']->db_new_hash($allowance_chargeH, $this->line_allowance_charge_table);
    }

    /*******************************************************************************
    * Add a new invoice allowance/charge
    * @param
    * @return
    */
    function allowance_charge_new()
    {
        global $_lib;
        $allowance_chargeH['invoiceallowancecharge_InvoiceType'] = 'out';
        $allowance_chargeH['invoiceallowancecharge_InvoiceID']   = $this->InvoiceID;
        return $_lib['db']->db_new_hash($allowance_chargeH, $this->allowance_charge_table);
    }

    /*******************************************************************************
    * Add a new invoiceline
    * @param
    * @return
    */
    function linenew()
    {
        global $_lib;
        $invoicelineH['invoiceoutline_Active']          = 1;
        $invoicelineH['invoiceoutline_InvoiceID']    = $this->InvoiceID;
        return $_lib['db']->db_new_hash($invoicelineH, $this->table_line);
    }

    /***********************************************************************
    *Start accounting
    ***********************************************************************/
    public function journal() {
        global $_lib, $accounting;

        /**********************************************************************/
        #Regnskapsf¿ringne begynner
        if(isset($this->headH['InvoiceID']))
        {
            $this->JournalID = $this->headH['InvoiceID'];

            #Delete old accounting
            $accounting->delete_journal($this->JournalID, $this->VoucherType);
        }

        #print "<h1>Bilagsf¿rer: JournalID: $this->JournalID</h1>\n";

        /**********************************************************************/
        /* Generate the accounting new */
        $fields = array();
        $fields['voucher_JournalID']      = $this->JournalID;
        $fields['voucher_ExternalID']     = $this->headH['ExternalID'];
        $fields['voucher_KID']            = $this->headH['KID'];
        $fields['voucher_InvoiceID']      = $this->InvoiceID;
        $fields['voucher_ProjectID']      = $this->headH['ProjectID'];
        $fields['voucher_DepartmentID']   = $this->headH['DepartmentID'];
        $fields['voucher_VoucherPeriod']  = $this->headH['Period'];
        $fields['voucher_VoucherDate']    = $this->headH['InvoiceDate'];
        $fields['voucher_VoucherType']    = $this->VoucherType;
        $fields['voucher_DueDate']        = $this->headH['DueDate'];
        $fields['voucher_Active']         = 1;
        $fields['voucher_AutomaticReason']          = "Faktura: $this->JournalID";
        $fields['voucher_Description']              = $this->headH['CommentCustomer']; # Take the description from the head to each line. $row->Comment;

        #print_r($this->headH);
        if($this->headH['TotalCustPrice'] >= 0)
        {
            $fields['voucher_AmountOut'] = abs($this->headH['TotalCustPrice']);
            unset($fields['voucher_AmountIn']);
        }
        elseif($this->headH['TotalCustPrice'] < 0)
        {
            $fields['voucher_AmountIn'] = abs($this->headH['TotalCustPrice']);
            unset($fields['voucher_AmountOut']);
        }

        $query_setup    = "select name, value from setup";
        $setup = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

        #print_r($this->headH);
        #print "hode: $this->CustomerAccountPlanID<br>\n";
        #print_r($fields);

        $VoucherID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=> $this->headH['CustomerAccountPlanID'], 'type'=>'reskontro', 'VoucherType'=> $this->VoucherType, 'invoice'=>'1', 'debug' => true));

        $query_invoiceline = "select * from $this->table_line where InvoiceID='$this->InvoiceID' and Active=1 order by LineID asc";
        #print "$query_invoiceline<br>";
        $result2 = $_lib['db']->db_query($query_invoiceline);

        // Generate vouchers for invoice lines
        while($row = $_lib['db']->db_fetch_object($result2))
        {
            $fieldsline = array();
            $fieldsline['voucher_AmountOut'] = 0;
            $fieldsline['voucher_AmountIn'] = 0;

            $query = "select ProductID, ProductName, AccountPlanID, CompanyDepartmentID, ProjectID from product where productID=".$row->ProductID;
            $productRow = $_lib['storage']->get_row(array('query' => $query));
            #print_r($productRow);

            $fieldsline['voucher_AutomaticReason']  = "Faktura: $this->JournalID";

            $sumprice = round($row->UnitCustPrice * $row->QuantityDelivered, 2);
            // if($row->Discount) {
            //     $sumprice = $sumprice * (1-$row->Discount/100);
            // }
            $query = "select sum(if(ChargeIndicator = 1, Amount, -Amount)) as sum from invoicelineallowancecharge where InvoiceType = 'out' and AllowanceChargeType = 'line' and InvoiceLineID = " . $row->LineID;
            $result = $_lib['storage']->get_row(array('query' => $query));
            $sum_line_allowance_charge = $result->sum;

            $sumprice += $sum_line_allowance_charge;

            $fieldsline['voucher_AmountOut']        = round($sumprice * (1 + ($row->Vat/100)), 2 );

            #print "verdi: " . $fieldsline['voucher_AmountOut'] . "<br>";
            if($fieldsline['voucher_AmountOut'] < 0)
            {
                $fieldsline['voucher_AmountIn'] = abs($fieldsline['voucher_AmountOut']);
                unset($fieldsline['voucher_AmountOut']);
            }

            #print "verdi ut: " . $fieldsline['voucher_AmountOut'] . "<br>";
            #print "verdi inn: " . $fieldsline['voucher_AmountIn'] . "<br>";

            $fieldsline['voucher_JournalID']        = $this->JournalID;
            #$fieldsline['voucher_KID']       = $this->JournalID; #Ikke kid Œ linjer
            $fieldsline['voucher_VatID']            = $row->VatID;
            $fieldsline['voucher_Vat']              = $row->Vat;

            if(isset($this->headH['DepartmentID']) && $this->headH['DepartmentID'] > 0)
                $fieldsline['voucher_DepartmentID']     = $this->headH['DepartmentID'];
            else
                $fieldsline['voucher_DepartmentID']     = $productRow->CompanyDepartmentID;

            if(isset($this->headH['ProjectID']) && $this->headH['ProjectID'] > 0)
                $fieldsline['voucher_ProjectID']        = $this->headH['ProjectID'];
            else
                $fieldsline['voucher_ProjectID']        = $productRow->ProjectID;

            $fieldsline['voucher_Description']      = $this->headH['CommentCustomer']; # Take the description from the head to each line. $row->Comment;
            $fieldsline['voucher_VoucherText']      = $row->ProductID;
            $fieldsline['voucher_VoucherPeriod']    = $this->headH['Period'];
            $fieldsline['voucher_VoucherDate']      = $this->headH['InvoiceDate'];
            $fieldsline['voucher_VoucherType']      = $this->VoucherType;
            $fieldsline['voucher_DueDate']          = $this->headH['DueDate'];
            $fieldsline['voucher_Active']           = 1;

            if(strlen($productRow->AccountPlanID)>0)
                $line_accountplanid = $productRow->AccountPlanID;
            else {
                $_lib['message']->add("For produkt '".$productRow->ProductID.' - '.$productRow->ProductName."' er ikke resultatkonto satt");
                $line_accountplanid = $setup['salecreditinntekt'];
            }

           $fieldsline['voucher_AccountPlanID']    = $line_accountplanid;

            #print_r($fieldsline);
            #print "linje: $line_accountplanid<br>\n";
            $accounting->insert_voucher_line(array('post'=>$fieldsline, 'accountplanid'=>$line_accountplanid, 'type'=>'result1', 'VoucherType'=>$this->VoucherType, 'invoice'=>'1', 'debug' => true));
        }

        $query_invoice_allowance_charge = "select * from invoiceallowancecharge where InvoiceType = 'out' and InvoiceID = '$this->InvoiceID'";

        $result_allowance_charge = $_lib['db']->db_query($query_invoice_allowance_charge);

        // Generate vouchers for invoice allowances/charges
        while($row = $_lib['db']->db_fetch_object($result_allowance_charge))
        {
            $fieldsline = array();
            $fieldsline['voucher_AmountOut'] = 0;
            $fieldsline['voucher_AmountIn'] = 0;

            $query = "select ac.OutAccountPlanID, ac.DepartmentID, ac.ProjectID from invoiceallowancecharge iac left join allowancecharge ac on iac.AllowanceChargeID = ac.AllowanceChargeID where iac.InvoiceAllowanceChargeID = " . $row->InvoiceAllowanceChargeID;
            $invoiceallowancecharge = $_lib['storage']->get_row(array('query' => $query));

            $fieldsline['voucher_AccountPlanID']    = $invoiceallowancecharge->OutAccountPlanID;
            $fieldsline['voucher_AutomaticReason']  = "Faktura rabatt/kostnad : $row->InvoiceAllowanceChargeID";
            $fieldsline['voucher_DepartmentID']     = $invoiceallowancecharge->DepartmentID;
            $fieldsline['voucher_ProjectID']        = $invoiceallowancecharge->ProjectID;

            $sumprice = round($row->Amount, 2);
            $sumprice *= ($row->ChargeIndicator == 1)? 1 : -1;

            $fieldsline['voucher_AmountOut'] = round($sumprice * (1 + ($row->VatPercent/100)), 2);

            if($fieldsline['voucher_AmountOut'] < 0)
            {
                $fieldsline['voucher_AmountIn'] = abs($fieldsline['voucher_AmountOut']);
                unset($fieldsline['voucher_AmountOut']);
            }

            $fieldsline['voucher_JournalID']        = $this->JournalID;
            $fieldsline['voucher_VatID']            = $row->VatID;
            $fieldsline['voucher_Vat']              = $row->VatPercent;

            $fieldsline['voucher_Description']      = $row->AllowanceChargeReason;
            $fieldsline['voucher_VoucherPeriod']    = $this->headH['Period'];
            $fieldsline['voucher_VoucherDate']      = $this->headH['InvoiceDate'];
            $fieldsline['voucher_VoucherType']      = $this->VoucherType;
            $fieldsline['voucher_DueDate']          = $this->headH['DueDate'];
            $fieldsline['voucher_Active']           = 1;

            $accounting->insert_voucher_line(array('post'=>$fieldsline, 'accountplanid'=>$fieldsline['voucher_AccountPlanID'], 'type'=>'result1', 'VoucherType'=>$this->VoucherType, 'invoice'=>'1', 'debug' => true));
        }

        // Generate vouchers for reconciliation reasons
        $VoucherH = array();
        // Only get those reasons for outgoing invoices(with InvoiceOut set as true)
        $fb_query = sprintf("SELECT * FROM fbdownloadedinvoicereasons WHERE LodoID = %d AND InvoiceOut = 1", $this->InvoiceID);

        $fb_rows = $_lib['db']->db_query($fb_query);
        $original_accountplanid = $this->headH['CustomerAccountPlanID'];

        while($fb_row = $_lib['db']->db_fetch_object($fb_rows)) {
            $reasonID = $fb_row->ClosingReasonId;
            $reconciliation_amount = $fb_row->Amount;

            $VoucherH['voucher_AmountIn']       = 0;
            $VoucherH['voucher_AmountOut']      = 0;
            $VoucherH['voucher_Vat']            = '';
            $VoucherH['voucher_Description']    = '';
            $VoucherH['voucher_AccountPlanID']  = 0;
            $VoucherH['voucher_InvoiceID']        = $this->InvoiceID;
            $VoucherH['voucher_JournalID']        = $this->JournalID;
            $VoucherH['voucher_VoucherPeriod']    = $this->headH['Period'];
            $VoucherH['voucher_VoucherDate']      = $this->headH['InvoiceDate'];
            $VoucherH['voucher_DueDate']          = $this->headH['DueDate'];

            if($reasonID) {
                $VoucherH['voucher_Description'] = sprintf(
                    'Reconciliation from reason %d',
                    $reasonID
                    );

                $reasonQuery = sprintf(
                    "SELECT r.*
                   FROM fakturabankinvoicereconciliationreason r,
                        accountplan a
                   WHERE r.FakturabankInvoiceReconciliationReasonID = %d
                     AND r.AccountPlanID = a.AccountPlanID",
                    $reasonID
                    );

                $reason_row = $_lib['storage']->get_row(array('query' => $reasonQuery, 'debug' => true));
                if(!$reason_row) {
                    $_lib['message']->add(sprintf("Noe galt med reconciliationreason %d", $reasonID));
                }
                else {
                    $VoucherH['voucher_AccountPlanID'] = $reason_row->AccountPlanID;

                    if($reconciliation_amount > 0) {
                        $VoucherH['voucher_AmountIn']   = abs($reconciliation_amount);
                        $VoucherH['voucher_AmountOut']  = 0;
                    }
                    else {
                        $VoucherH['voucher_AmountOut']  = abs($reconciliation_amount);
                        $VoucherH['voucher_AmountIn']   = 0;
                    }

                    $accounting->insert_voucher_line(
                        array(
                            'post' => $VoucherH,
                            'accountplanid' => $VoucherH['voucher_AccountPlanID'],
                            'VoucherType'=> $this->VoucherType,
                            'comment' => 'Fra fakturabank - Reconciliation'
                            )
                        );

                    /* motpost */
                    $VoucherH['voucher_AccountPlanID'] = $original_accountplanid;
                    $tmp = $VoucherH['voucher_AmountIn'];
                    $VoucherH['voucher_AmountIn'] = $VoucherH['voucher_AmountOut'];
                    $VoucherH['voucher_AmountOut'] = $tmp;

                    $accounting->insert_voucher_line(
                        array(
                            'post' => $VoucherH,
                            'accountplanid' => $VoucherH['voucher_AccountPlanID'],
                            'VoucherType'=> $this->VoucherType,
                            'comment' => 'Fra fakturabank - Reconciliation'
                            )
                        );
                }
            }
        }


        #print "PosteringsID: $VoucherID<br>\n";
        $AmountIn                       = $fields['voucher_AmountIn'];
        $fields['voucher_AmountIn']     = $fields['voucher_AmountOut'];
        $fields['voucher_AmountOut']    = $AmountIn;
        #print "<hr>";
        #print_r($fields);
        #print "Hovedbok auto: AccountPlanID, $this->AccountPlanID, VoucherID: $VoucherID, VoucherType: $this->VoucherType, JournalID: $this->JournalID<br>\n";
        #$accounting->voucher_to_hovedbok_auto($this->AccountPlanID, $fields, $VoucherID);
        $accounting->set_journal_motkonto(array('post'=>$fields, 'VoucherType'=>$this->VoucherType));
        $accounting->correct_journal_balance($fields, $this->JournalID, $this->VoucherType);
    }

    ################################################################################################
    #Legger fakturadata over i fakturabank dataformat og sender det over fakturabank.
    function fakturabank_send() {
        global $_lib;

        $this->invoiceO = new stdClass();
        $this->taxH     = array();

        $sql_invoice    = "select invoiceout.*, P.Email as SavedByInLodo, IOP.InvoicePrintDate from invoiceout left outer join person P ON invoiceout.UpdatedByPersonID = P.PersonID left outer join invoiceoutprint IOP on invoiceout.InvoiceID = IOP.InvoiceID where invoiceout.InvoiceID='$this->InvoiceID'";
        #print "$sql_invoice<br>\n";
        $invoice        = $_lib['storage']->get_row(array('query' => $sql_invoice));

        ############################################################################################
        $this->invoiceO->ID                   = $invoice->InvoiceID;
        $this->invoiceO->IssueDate            = $invoice->InvoiceDate;
        $this->invoiceO->DateOfIssue          = $invoice->InvoicePrintDate; // not by EHF, per Arnt's instructions
        $this->invoiceO->InvoiceTypeCode      = '380';
        $this->invoiceO->Note                 = $invoice->CommentCustomer;
        $this->invoiceO->LodoSavedBy          = $invoice->SavedByInLodo;
        $this->invoiceO->DocumentCurrencyCode = $invoice->CurrencyID;

        if ($invoice->DepartmentID != "" || $invoice->DepartmentID === 0) { // "0" is valid
            $this->invoiceO->DepartmentCode = $invoice->DepartmentID;

            $sql_department = "select * from companydepartment where CompanyDepartmentID=" . (int) $invoice->DepartmentID;
            $department = $_lib['storage']->get_row(array('query' => $sql_department));

            if (!empty($department->DepartmentName)) {
                $this->invoiceO->Department = $department->DepartmentName;
            }
        }

        if (!empty($invoice->DepartmentCustomer)) {
            $this->invoiceO->CustomerDepartment = $invoice->DepartmentCustomer;
        }

        if ($invoice->ProjectID != "" || $invoice->ProjectID === 0) { // "0" is valid
            $this->invoiceO->ProjectCode = $invoice->ProjectID;

            $sql_project = "select * from project where ProjectID=" . (int) $invoice->ProjectID;
            $project = $_lib['storage']->get_row(array('query' => $sql_project));

            if (!empty($project->Heading)) {
                $this->invoiceO->Project = $project->Heading;
            }
        }

        if (!empty($invoice->ProjectNameCustomer)) {
            $this->invoiceO->CustomerProject = $invoice->ProjectNameCustomer;
        }


        /* Do not transmit references as OrderReference, because in Lodo they are not reference ids, but instead CONTACT PERSONS
        if (!empty($invoice->RefInternal)) {
            $this->invoiceO->OrderReference->ID = $invoice->RefInternal; // this should be RefCustomer but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
        }

        if (!empty($invoice->RefCustomer)) {
            $this->invoiceO->OrderReference->SalesOrderID = $invoice->RefCustomer; // this should be RefInternal but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
        }
        */

        ############################################################################################

        $this->invoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID        = preg_replace('/[^0-9]/', '', $invoice->SOrgNo);

        if (!empty($invoice->SVatNo)) {
            $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID        = preg_replace('/ /', '', $invoice->SVatNo);
            if ($invoice->SCountryCode == 'SE') {
                $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'SE:VAT';
            } else if ($invoice->SCountryCode == 'NO') {
                $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
            } // else leave empty
        }
        $this->invoiceO->AccountingSupplierParty->Party->PartyName->Name                = $invoice->SName;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName      = $invoice->SAddress;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->BuildingNumber  = '';
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->CityName        = $invoice->SCity;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone      = $invoice->SZipCode;
        if (empty($invoice->SCountryCode)) {
            $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode= 'NO';
        } else {
            $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode= $invoice->SCountryCode;
        }

        if (!empty($invoice->SPhone)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Telephone = $invoice->SPhone;
        }
        if (!empty($invoice->SMobile)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Mobile = $invoice->SMobile;
        }
        if (!empty($invoice->SFax)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Telefax = $invoice->SFax;
        }
        if (!empty($invoice->SEmail)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail = $invoice->SEmail;
        }


        if (!empty($invoice->RefCustomer)) {
            // We should use RefSupplier but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefSupplier
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Name = $invoice->RefCustomer;
        }

        ############################################################################################

        if (!empty($invoice->IOrgNo)) {
          $firstFirmaID = array('value' => preg_replace('/[^0-9]+/', '', $invoice->IOrgNo), 'type' => 'NO:ORGNR');
        } else {
          $schemeControl = new lodo_accountplan_scheme($invoice->CustomerAccountPlanID);
          $firstFirmaID = $schemeControl->getFirstFirmaID();
        }
        $this->invoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID        = $firstFirmaID['value'];
        $this->invoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyIDSchemeID = $firstFirmaID['type'];

        if (!empty($invoice->IVatNo)) {
            $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID        = $invoice->IVatNo;
            if ($invoice->ICountryCode == 'SE') {
                $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'SE:VAT';
            } else if ($invoice->ICountryCode == 'NO') {
                $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
            } // else leave empty
        }
        $this->invoiceO->AccountingCustomerParty->Party->PartyIdentification->ID = $invoice->CustomerAccountPlanID;
        $this->invoiceO->AccountingCustomerParty->Party->PartyName->Name                = $invoice->IName;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName      = $invoice->IAddress;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->BuildingNumber  = '';
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->CityName        = $invoice->ICity;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone     = $invoice->IZipCode;
        if (empty($invoice->ICountryCode)) {
            $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode= 'NO';
        } else {
            $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode= $invoice->ICountryCode;
        }

        $this->invoiceO->AccountingCustomerParty->Party->Contact->ID = $_lib['sess']->get_person('PersonID');
        if (!empty($invoice->Phone)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Telephone = $invoice->Phone;
        }
        if (!empty($invoice->IMobile)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Mobile = $invoice->IMobile;
        }
        if (!empty($invoice->IFax)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Telefax = $invoice->IFax;
        }
        $email = $invoice->Email;
        if (!empty($invoice->IEmail)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail = $invoice->IEmail;
        }

        if (!empty($invoice->RefInternal)) {
            // We should use RefCustomer but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Name = $invoice->RefInternal;
        }


        // Delivery address
        $this->invoiceO->DeliveryAddress->Address     = $invoice->DAddress;
        $this->invoiceO->DeliveryAddress->City        = $invoice->DCity;
        $this->invoiceO->DeliveryAddress->ZipCode     = $invoice->DZipCode;
        $this->invoiceO->DeliveryAddress->CountryCode = $invoice->DCountryCode;

        ############################################################################################
        $this->invoiceO->PaymentMeans->PaymentMeansCode             = 42;
        $this->invoiceO->PaymentMeans->PaymentDueDate               = $invoice->DueDate;
        $this->invoiceO->PaymentMeans->PayeeFinancialAccount->ID    = preg_replace('/ /', '', $invoice->SBankAccount);
        $this->invoiceO->PaymentMeans->PayeeFinancialAccount->Name  = 'Bank';

        if (!empty($invoice->BankAccount)) {
            $this->invoiceO->PaymentMeans->PayerFinancialAccount->ID = $invoice->BankAccount;
        }

        if (!empty($invoice->KID)) {
            $this->invoiceO->PaymentMeans->PaymentID = $invoice->KID;
        }

        // Payment Terms
        $this->invoiceO->PaymentTerms->Note                          = $invoice->PaymentCondition;

        // Allowance/Charge on invoice
        $query_invoice_allowance_charge = "select * from $this->allowance_charge_table where InvoiceID = '" . (int) $this->InvoiceID . "' and InvoiceType = 'out'";
        $result_allwance_charge = $_lib['db']->db_query($query_invoice_allowance_charge);

        $invoice_allowance_total     = 0;
        $invoice_charge_total        = 0;
        $invoice_allowance_tax_total = 0;
        $invoice_charge_tax_total    = 0;
        while($acrow = $_lib['db']->db_fetch_object($result_allwance_charge)) {
          $allowance_charge = new StdClass();
          $allowance_charge->ChargeIndicator            = (int) $acrow->ChargeIndicator;
          $allowance_charge->AllowanceChargeReason      = $acrow->AllowanceChargeReason;
          $allowance_charge->Amount                     = (float) $acrow->Amount;
          // Select category based on VatID and date
          $category_from_vat_id = "select Category from vat where VatID = '" . $acrow->VatID . "' and Type = 'sale' and ValidFrom <= '" . $invoice->InvoiceDate . "' and ValidTo >= '" . $invoice->InvoiceDate . "'";
          $vat = $_lib['db']->get_row(array('query' => $category_from_vat_id));
          $allowance_charge->TaxCategory->ID            = $vat->Category;
          $allowance_charge->TaxCategory->Percent       = (float) $acrow->VatPercent;
          $allowance_charge->TaxCategory->TaxScheme->ID = 'VAT'; // Hardcoded to VAT
          $this->invoiceO->AllowanceCharge[] = $allowance_charge;

          # Calculate AllowanceTotalAmount and ChargeTotalAmount
          $invoice_allowance_total += ($acrow->ChargeIndicator) ? 0 : $acrow->Amount;
          $invoice_charge_total    += ($acrow->ChargeIndicator) ? $acrow->Amount : 0;

          $taxable_amount = $acrow->Amount;
          $tax_amount = $acrow->Amount * ($acrow->VatPercent/100.0);
          if ($allowance_charge->ChargeIndicator == 1) {
            $this->taxH[$acrow->VatPercent]->TaxableAmount += $taxable_amount;
            $invoice_charge_tax_total += $tax_amount;
            $this->taxH[$acrow->VatPercent]->TaxAmount += $tax_amount;
          }
          else {
            $this->taxH[$acrow->VatPercent]->TaxableAmount -= $taxable_amount;
            $invoice_allowance_tax_total += $tax_amount;
            $this->taxH[$acrow->VatPercent]->TaxAmount -= $tax_amount;
          }
          $this->taxH[$acrow->VatPercent]->Category = $vat->Category;
        }


        ############################################################################################
        $query_invoiceline      = "select il.*, p.UNSPSC, p.EAN, p.ProductNumber, v.Category from invoiceoutline as il left outer join product as p on il.ProductID=p.ProductID left outer join vat as v on il.VatID=v.VatID and (
          il.Vat = v.Percent or (il.Vat = 0 and v.Percent is null)) and v.ValidFrom <= '".$this->headH["InvoiceDate"]."' and v.ValidTo >= '".$this->headH["InvoiceDate"]."' where il.InvoiceID='" . (int) $this->InvoiceID . "' and il.Active <> 0 order by il.LineID asc";
        #print "$query_invoiceline\n";
        $result2                = $_lib['db']->db_query($query_invoiceline);

        while($line = $_lib['db']->db_fetch_object($result2)) {
            // Allowance/Charge on invoice line
            $allowances = 0;
            $charges    = 0;
            $query_invoiceline_allowance_charge = "select * from $this->line_allowance_charge_table where InvoiceLineID = '" . (int) $line->LineID . "' and InvoiceType = 'out' and AllowanceChargeType = 'line'";
            $result_allwance_charge = $_lib['db']->db_query($query_invoiceline_allowance_charge);

            while($acrow = $_lib['db']->db_fetch_object($result_allwance_charge)) {
              $allowance_charge = new StdClass();
              $allowance_charge->ChargeIndicator       = (int) $acrow->ChargeIndicator;
              $allowance_charge->AllowanceChargeReason = $acrow->AllowanceChargeReason;
              $allowance_charge->Amount                = (float) $acrow->Amount;
              $this->invoiceO->InvoiceLine[$line->LineID]->AllowanceCharge[] = $allowance_charge;
              $allowances += ($acrow->ChargeIndicator == 0) ? $acrow->Amount : 0;
              $charges    += ($acrow->ChargeIndicator == 1) ? $acrow->Amount : 0;
            }

            #print_r($line);
            $linetotal      = $line->UnitCustPrice * $line->QuantityDelivered + $charges - $allowances;
            $linetaxamount  = $linetotal * ($line->Vat / 100);
            $taxtotal      += $linetaxamount;
            $total         += $linetotal;
            $this->taxH[$line->Vat]->TaxableAmount    += $linetotal;
            $this->taxH[$line->Vat]->TaxAmount        += $linetaxamount;
            $this->taxH[$line->Vat]->Category          = $line->Category;

            $this->invoiceO->InvoiceLine[$line->LineID]->ID                                     = $line->LineID;
            $this->invoiceO->InvoiceLine[$line->LineID]->InvoicedQuantity                       = $line->QuantityDelivered;
            $this->invoiceO->InvoiceLine[$line->LineID]->LineExtensionAmount                    = $linetotal;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxAmount                    = $linetaxamount;

            $this->invoiceO->InvoiceLine[$line->LineID]->Item->Name                             = $line->ProductName;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->Description                      = $line->Comment;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->SellersItemIdentification->ID    = $line->ProductNumber;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->CommodityClassification->UNSPSC->ItemClassificationCode = $line->UNSPSC;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->ClassifiedTaxCategory->ID        = $line->Category;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->ClassifiedTaxCategory->Percent   = $line->Vat;

            $this->invoiceO->InvoiceLine[$line->LineID]->Price->PriceAmount                     = $line->UnitCustPrice;

            // Allowance/Charge on invoice line price
            $query_lineprice_allowance_charge = "select * from $this->line_allowance_charge_table where InvoiceLineID = '" . (int) $line->LineID . "' and InvoiceType = 'out' and AllowanceChargeType = 'price'";
            $result_allwance_charge = $_lib['db']->db_query($query_lineprice_allowance_charge);

            while($acrow = $_lib['db']->db_fetch_object($result_allwance_charge)) {
              $allowance_charge = new StdClass();
              $allowance_charge->ChargeIndicator       = (int) $acrow->ChargeIndicator;
              $allowance_charge->AllowanceChargeReason = $acrow->AllowanceChargeReason;
              $allowance_charge->Amount                = (float) $acrow->Amount;
              $this->invoiceO->InvoiceLine[$line->LineID]->Price->AllowanceCharge[] = $allowance_charge;
            }

        }

        ############################################################################################
        $this->invoiceO->TaxTotal['TaxAmount'] = $taxtotal + $invoice_charge_tax_total - $invoice_allowance_tax_total;

        #TODO: Subtotal should be repeated for each tax amount - forach - and function
        foreach($this->taxH as $VatPercent => $vat) {
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxableAmount                = $vat->TaxableAmount;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxAmount                    = $vat->TaxAmount;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->ID              = $vat->Category;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->Percent         = $VatPercent;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->TaxScheme->ID   = 'VAT';
        }

        $this->invoiceO->LegalMonetaryTotal->AllowanceTotalAmount = $invoice_allowance_total;
        $this->invoiceO->LegalMonetaryTotal->ChargeTotalAmount    = $invoice_charge_total;
        $this->invoiceO->LegalMonetaryTotal->LineExtensionAmount  = $total;
        $this->invoiceO->LegalMonetaryTotal->TaxExclusiveAmount   = $total + $invoice_charge_total - $invoice_allowance_total;
        $this->invoiceO->LegalMonetaryTotal->TaxInclusiveAmount   = $total + $taxtotal + $invoice_charge_tax_total - $invoice_allowance_tax_total + $invoice_charge_total - $invoice_allowance_total;
        $this->invoiceO->LegalMonetaryTotal->PayableAmount        = $total + $taxtotal + $invoice_charge_tax_total - $invoice_allowance_tax_total + $invoice_charge_total - $invoice_allowance_total;

        #print_r($this->invoiceO);

        $fb = new lodo_fakturabank_fakturabank();
        $fb->write($this->invoiceO);

        #print_r($this->invoiceO->InvoiceLine);
        return $this->invoiceO;
    }

    function fakturabankjournal() {
        throw new Exception("lodo_fakturabank_fakturabank::journal function not implemented");

        /* global $_lib; */

        /* $fb = new lodo_fakturabank_fakturabank(); */
        /* $fb->journal(); */
    }

    function lock() {
        global $_lib;

        $dataH = array();
        $dataH['InvoiceID']             = $this->InvoiceID;
        $dataH['Locked']                = 1;
        $dataH['LockedBy']              = $_lib['sess']->get_person('PersonID');
        $dataH['LockedAt']              = strftime("%F %T");

        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
    }

    function CheckIfAnythingChanged($InvoiceID, $InvoiceLineIDs, &$args) {
      global $_lib;
      $Changed = false;
      $Invoice = $_lib['storage']->get_row(array('query' => "SELECT * FROM invoiceout WHERE InvoiceID = " . $InvoiceID));
      // Check if anything changed
      $InvoiceFieldNames = array("CustomerAccountPlanID" => flase, "CurrencyID" => false, "InvoiceDate" => false, "DueDate" => false,
                                 "Period" => false,"Note" => false, "RefCustomer" => false, "RefInternal" => false, "DepartmentID" => false,
                                 "DepartmentCustomer" => false, "ProjectID" => false, "ProjectNameCustomer" => false, "DeliveryCondition" => false,
                                 "PaymentCondition" => false, "CommentCustomer" => false);
      foreach ($InvoiceFieldNames as $FieldName => $IsAmount) {
        $ArgName = "invoiceout_".$FieldName."_".$InvoiceID;
        if ($IsAmount) {
          $ValueHash = $_lib['convert']->Amount(array('value'=>$args[$ArgName]));
          $Value = $ValueHash['value'];
        }
        else $Value = $args[$ArgName];
        if ($Value != $Invoice->{$FieldName}) {
          $Changed = true;
          return $Changed;
        }
      }
      while ($InvoiceLineID = array_pop($InvoiceLineIDs)) {
        $InvoiceLine = $_lib['storage']->get_row(array('query' => "SELECT * FROM invoiceoutline WHERE LineID = " . $InvoiceLineID));
        // Check if anything changed
        $InvoiceLineFieldNames = array("ProductID" => false, "ProductName" => false, "QuantityDelivered" => false, "UnitCustPrice" => true, "Comment" => false);
        foreach($InvoiceLineFieldNames as $FieldName => $IsAmount) {
          $ArgName = "invoiceoutline_".$FieldName."_".$InvoiceLineID;
          if ($IsAmount) {
            $ValueHash = $_lib['convert']->Amount(array('value'=>$args[$ArgName]));
            $Value = $ValueHash['value'];
          }
          else $Value = $args[$ArgName];
          if ($Value != $InvoiceLine->{$FieldName}) {
            $Changed = true;
            return $Changed;
          }
        }
      }
      return $Changed;
    }

    function populateAltinnInvoiceObject($AltinnReport4ID, $invoice_type) {
      global $_lib;

      $query_altinn_report4 = "select * from altinnReport4 where AltinnReport4ID = " . $AltinnReport4ID;
      $result_altinn_report4 = $_lib['db']->db_query($query_altinn_report4);
      $altinn_report4_row = $_lib['db']->db_fetch_object($result_altinn_report4);

      $query_altinn_report1 = "select ar1.* from altinnReport4 ar4 inner join altinnReport2 as ar2 on ar4.res_ArchiveReference = ar2.res_ArchiveReference inner join altinnReport1 as ar1 on ar1.ReceiptId = ar2.res_ReceiptId where ar4.AltinnReport4ID = " . $AltinnReport4ID;
      $result_altinn_report1 = $_lib['db']->db_query($query_altinn_report1);
      $altinn_report1_row = $_lib['db']->db_fetch_object($result_altinn_report1);

      $altinn_file = new altinn_file($altinn_report4_row->Folder);
      $file_contents = $altinn_file->readFile("tilbakemelding" . $altinn_report4_row->AltinnReport4ID . ".xml");
      if (!$file_contents) {
        $_lib['message']->add('Tilbakemeldingen kan ikke leses');
        return false; // File can't be read
      } else {
        $xml = simplexml_load_string($file_contents);
      }

      $altinn_invoice = new stdClass();
      $taxH = array();

      $altinn_reference = $altinn_report4_row->res_ArchiveReference;
      $recieved_messages = $xml->Mottak->mottattLeveranse;
      $publishing_date = 0;
      foreach ($recieved_messages as $message) {
        $publishing_date = max($publishing_date, strtotime((string)$message->leveringstidspunkt));
      }
      $publishing_date = strftime("%F", $publishing_date);
      $issue_date = strtotime((string) $xml->Mottak->kalendermaaned . '-01');
      $issue_date = strftime("%F", strtotime('next month -1 hour', $issue_date));
      $bank_account_number = (string) $xml->Mottak->innbetalingsinformasjon->kontonummer;
      $customer_bank_account_number = preg_replace('/[^0-9]/', '', $_lib['sess']->get_companydef('BankAccount'));
      $kommune = new kommune();
      $kommune->load_by_field_value(array('BankAccountNumber' => $bank_account_number));
      if (is_null($kommune->KommuneNumber)) {
        $_lib['message']->add('Finner ikke bankkonto in kommunelisten, ta kontakt med administrator før du kan sende til fakturaBank');
        return false;
      }
      $kommune_orgno = preg_replace('/[^0-9]/', '', $kommune->OrgNumber);
      $kommune_name = $kommune->OrgName;
      $customer_orgno = (string) $xml->Mottak->innsender->norskIdentifikator;
      $customer_orgno = preg_replace('/[^0-9]+/', '', $customer_orgno);
      $customer_name = $_lib['sess']->get_companydef('CompanyName');
      $due_date = (string) $xml->Mottak->innbetalingsinformasjon->forfallsdato;
      if ($invoice_type == "AGA") {
        $kid = (string) $xml->Mottak->innbetalingsinformasjon->kidForArbeidsgiveravgift;
        $amount = (float) $xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumArbeidsgiveravgift;
      } else {
        $kid = (string) $xml->Mottak->innbetalingsinformasjon->kidForForskuddstrekk;
        $amount = (float) $xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumForskuddstrekk;
      }

      if($altinn_report1_row->ErstatterMeldingsId) {
        $query = "SELECT res_ArchiveReference AS AltinnRef FROM altinnReport2 WHERE res_ReceiptId = (SELECT ReceiptId FROM altinnReport1 WHERE MeldingsId = '". $altinn_report1_row->ErstatterMeldingsId ."')";
        $replaces_altinn_ref = $_lib["db"]->get_row(array('query' => $query))->AltinnRef;
        if($replaces_altinn_ref) {
          $note = "Erstatter ". $replaces_altinn_ref;
        }
      }

      $altinn_invoice->ID = $invoice_type . '-' . $altinn_reference;
      $altinn_invoice->IssueDate = $issue_date;
      $altinn_invoice->InvoiceTypeCode = '380';
      // Not by EHF, is used in fakturaBank
      $altinn_invoice->DateOfIssue = $publishing_date;
      $altinn_invoice->Note = $note;
      $altinn_invoice->DocumentCurrencyCode = exchange::getLocalCurrency();

      // Invoice supplier
      $altinn_invoice->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID = $kommune_orgno;
      $altinn_invoice->AccountingSupplierParty->Party->PartyLegalEntity->RegistrationName = $kommune_name;

      $altinn_invoice->AccountingSupplierParty->Party->PartyName->Name = $kommune_name;

      $altinn_invoice->AccountingSupplierParty->Party->PostalAddress->StreetName = trim($kommune->Address1 . "\n" . $kommune->Address2 . "\n" . $kommune->Address3);
      $altinn_invoice->AccountingSupplierParty->Party->PostalAddress->BuildingNumber = '';
      $altinn_invoice->AccountingSupplierParty->Party->PostalAddress->CityName = $kommune->City;
      $altinn_invoice->AccountingSupplierParty->Party->PostalAddress->PostalZone = $kommune->ZipCode;
      $altinn_invoice->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode = 'NO';

      $altinn_invoice->AccountingSupplierParty->Party->Contact->Telephone = $kommune->Telephone;
      $altinn_invoice->AccountingSupplierParty->Party->Contact->Note = "Mobile: ". $kommune->Mobile;
      $altinn_invoice->AccountingSupplierParty->Party->Contact->Telefax = $kommune->Telefax;
      $altinn_invoice->AccountingSupplierParty->Party->Contact->ElectronicMail = $kommune->Email;

      // Invoice customer
      $altinn_invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID = $customer_orgno;
      $altinn_invoice->AccountingCustomerParty->Party->PartyLegalEntity->RegistrationName = $customer_name;
      $altinn_invoice->AccountingCustomerParty->Party->PartyLegalEntity->CompanyIDSchemeID = 'NO:ORGNR';
      $altinn_invoice->AccountingCustomerParty->Party->PartyName->Name = $customer_name;

      // Using company's address and contact info.
      $altinn_invoice->AccountingCustomerParty->Party->PostalAddress->StreetName = $_lib['sess']->get_companydef('IAddress');
      $altinn_invoice->AccountingCustomerParty->Party->PostalAddress->CityName = $_lib['sess']->get_companydef('ICity');
      $altinn_invoice->AccountingCustomerParty->Party->PostalAddress->PostalZone = $_lib['sess']->get_companydef('IZipCode'); // ZipCode
      $altinn_invoice->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode = $_lib['sess']->get_companydef('ICountryCode');
      $altinn_invoice->AccountingCustomerParty->Party->Contact->ID = $_lib['sess']->get_person('PersonID');
      $altinn_invoice->AccountingCustomerParty->Party->Contact->Telephone = $_lib['sess']->get_companydef('Phone');
      $altinn_invoice->AccountingCustomerParty->Party->Contact->Note = "Mobile: ". $_lib['sess']->get_companydef('Mobile');
      $altinn_invoice->AccountingCustomerParty->Party->Contact->Telefax = $_lib['sess']->get_companydef('Fax');
      $altinn_invoice->AccountingCustomerParty->Party->Contact->ElectronicMail = $_lib['sess']->get_companydef('Email');

      // Invoice payment means
      $altinn_invoice->PaymentMeans->PaymentMeansCode = 42;
      $altinn_invoice->PaymentMeans->PaymentDueDate = $due_date;
      $altinn_invoice->PaymentMeans->PayeeFinancialAccount->ID = $bank_account_number;

      $altinn_invoice->PaymentMeans->PaymentID = $kid;

      // Invoice lines
      $altinn_invoice->InvoiceLine[0]->ID = 0;
      $altinn_invoice->InvoiceLine[0]->InvoicedQuantity = 1;
      $altinn_invoice->InvoiceLine[0]->LineExtensionAmount = $amount;
      $altinn_invoice->InvoiceLine[0]->TaxTotal->TaxAmount = 0;

      $altinn_invoice->InvoiceLine[0]->Item->Name = $invoice_type . ' ' . strftime('%Y-%m', strtotime($issue_date));
      $altinn_invoice->InvoiceLine[0]->Item->ClassifiedTaxCategory->ID = 'E';
      $altinn_invoice->InvoiceLine[0]->Item->ClassifiedTaxCategory->TaxExemptionReason = 'Skattefritt, 0%';
      $altinn_invoice->InvoiceLine[0]->Item->ClassifiedTaxCategory->Percent = 0;
      $altinn_invoice->InvoiceLine[0]->Item->ClassifiedTaxCategory->TaxScheme->ID = 'VAT';

      $altinn_invoice->InvoiceLine[0]->Price->PriceAmount = $amount;

      $altinn_invoice->TaxTotal['TaxAmount'] = 0;

      $altinn_invoice->TaxTotal['TaxAmount'] = $vat->TaxAmount;
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxableAmount = $amount;
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxAmount = 0;
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxCategory->ID = 'E'; // Tax exempt, 0%
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxCategory->TaxExemptionReason = 'Skattefritt, 0%';
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxCategory->Percent = 0;
      $altinn_invoice->TaxTotal[0]->TaxSubtotal->TaxCategory->TaxScheme->ID = 'VAT';

      $altinn_invoice->LegalMonetaryTotal->PayableAmount = $amount;
      $altinn_invoice->LegalMonetaryTotal->TaxExclusiveAmount = $amount;
      $altinn_invoice->LegalMonetaryTotal->TaxInclusiveAmount = $amount;
      $altinn_invoice->LegalMonetaryTotal->LineExtensionAmount = $amount;

      return $altinn_invoice;
    }

}
?>
