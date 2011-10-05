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
includelogic('kid/kid');

class invoice {
    public $InvoiceID       = 0;
    public $CustomerAccountPlanID   = 0;
    public $JournalID       = 0;
    public $VoucherType     = 'S';
    public $table_head      = 'invoiceout';
    public $table_line      = 'invoiceoutline';
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
            #print_r($headH);

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

        #Possible to extend or alter parameters here
        #Set default parameters
        $accountplan = $accounting->get_accountplan_object($this->CustomerAccountPlanID);

        $headH['IName']                 = $accountplan->AccountName;
        $headH['DName']                 = $accountplan->AccountName;
        $headH['DAddress']              = $accountplan->Address;
        $headH['DZipCode']              = $accountplan->ZipCode;

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
        $headH['DCity']                 = $accountplan->City;
        
        $headH['DCountryCode']              = $accountplan->CountryCode;
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

        #Update multi into db to support old format        
        #print_r($args);
        $_lib['db']->db_update_multi_table($args, array('invoiceout' => 'InvoiceID', 'invoiceoutline' => 'LineID', 'invoiceoutprint' => 'InvoiceID'));

        #Then read everything from disk and correct calculations
        $this->init($args);
        $this->make_invoice();
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

            /* Generate invoice line */
            #print "Generer fakturalinjer\n\n";
            foreach($this->lineH as $lineH)
            {
                $lineH['InvoiceID'] = $this->InvoiceID;
                if($this->debug) print_r($lineH);
                $_lib['storage']->store_record(array('data' => $lineH, 'table' => $this->table_line, 'debug' => false));
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

        $query_invoiceline = "select * from $this->table_line where InvoiceID='$this->OldInvoiceID' and Active!=0 order by LineID asc";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        while($lineH = $_lib['db']->db_fetch_assoc($result2))
        {
            unset($lineH['LineID']); #This id is pk so we cannot copy it.
            unset($lineH['InvoiceID']); 
            #print "linje\n";
            #print_r($lineH);
            $this->set_line($lineH);
        }

        $this->make_invoice();
        return $this->InvoiceID;
    }

    /*******************************************************************************
    * Delete invoiceline
    * @param
    * @return
    */
    function linedelete($LineID)
    {
        global $_lib;
        $query="update $this->table_line set Active=0 where LineID=" . $LineID;
        $ret = $_lib['db']->db_update($query);
        
        $_lib['message']->add(array('message' => "Linje $LineID p&aring; faktura $this->InvoiceID er slettet"));
        
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
            if($key != action_invoice_new)
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
        $this->totalMva         = 0;
        $this->set_head(array('TotalCustPrice' => 0));
        $this->set_head(array('TotalVat' => 0));
        $this->set_head(array('TotalCostPrice' => 0));
    }

    /***************************************************************************
    * Insert invoice line
    * @param $line - hash containing invoice line data
    */
    function set_line($line)
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
        
        $VAT = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $this->headH['InvoiceDate']));
        #print_r($VAT);

        #print_r($product);

        if($line['QuantityDelivered'] == 0) #Rettet av Geir. Maa vare mulig aa lage kreditnota med minus i antall.
            $lineH['QuantityDelivered'] = 0;

        if($line['UnitCustPrice'] <= 0)
            $lineH['UnitCustPrice'] = $product->UnitCustPrice;

        #if($line['Vat'] <= 0)
        $lineH['Vat']   = $VAT->Percent;
        $lineH['VatID'] = $VAT->VatID;

        if(!$line['ProductName'])
            $lineH['ProductName'] = $product->ProductName;
        } else {
            $_lib['message']->add(array('message' => 'Du m&aring; velge produkter til alle fakturalinjene'));
        }

        if($lineH['QuantityDelivered'] == 0)
            $_lib['message']->add(array('message' => 'Du m&aring; taste Antall produkter p&aring; fakturalinjen'));

        if($lineH['UnitCustPrice'] == 0)
            $_lib['message']->add(array('message' => 'Du m&aring; sette en Enhetspris p&aring; fakturalinjen'));

        #exit;
        $tmpquant   = $lineH['QuantityDelivered'];
        $custprice  = $_lib['convert']->Amount($lineH['UnitCustPrice']);

        $this->totalSum += round($tmpquant * $custprice, 2);
        $this->totalMva += round($tmpquant * $custprice * ($lineH['Vat']/100), 2);
        
        $this->lineH[] = $lineH;
        
        $this->TotalCustPrice = $this->totalSum + $this->totalMva;
        #print "<b>TotalCustPrice: $this->TotalCustPrice</b><br>";
        #$this->headH[] = 199; #$this->TotalCustPrice;
        $this->set_head(array('TotalCustPrice' => $this->TotalCustPrice));
    }

    /*******************************************************************************
    * Delete the entire invoice
    * @param
    * @return
    */
    function delete_invoice()
    {
        global $_lib, $accounting;
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
        $fields['voucher_CustomerAccountPlanID']    = $this->headH['CustomerAccountPlanID'];
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

        while($row = $_lib['db']->db_fetch_object($result2))
        {
            $fieldsline = array();
            $fieldsline['voucher_AmountOut'] = 0;
            $fieldsline['voucher_AmountIn'] = 0;

            $query = "select AccountPlanID, CompanyDepartmentID, ProjectID from product where productID=".$row->ProductID;
            $productRow = $_lib['storage']->get_row(array('query' => $query));
            #print_r($productRow);

            $fieldsline['voucher_AutomaticReason']  = "Faktura: $this->JournalID";

            $sumprice = round($row->UnitCustPrice * $row->QuantityDelivered, 2);
            if($row->Discount) {                
                $sumprice = $sumprice * (1-$row->Discount/100);
            }
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
            else
                $line_accountplanid = $setup['salecreditinntekt'];

           $fieldsline['voucher_AccountPlanID']    = $line_accountplanid;

            #print_r($fieldsline);
            #print "linje: $line_accountplanid<br>\n";
            $accounting->insert_voucher_line(array('post'=>$fieldsline, 'accountplanid'=>$line_accountplanid, 'type'=>'result1', 'VoucherType'=>$this->VoucherType, 'invoice'=>'1', 'debug' => true));
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

        $sql_invoice    = "select * from invoiceout where InvoiceID='$this->InvoiceID'";
        #print "$sql_invoice<br>\n";
        $invoice        = $_lib['storage']->get_row(array('query' => $sql_invoice));

        ############################################################################################
        $this->invoiceO->ID                   = $invoice->InvoiceID;
        $this->invoiceO->IssueDate            = $invoice->InvoiceDate;
        $this->invoiceO->Note            = $invoice->CommentCustomer;

        $sql_supplier = "select * from company where CompanyID=" . (int) $invoice->FromCompanyID;
        $supplier               = $_lib['storage']->get_row(array('query' => $sql_supplier));

        $this->invoiceO->DocumentCurrencyCode = exchange::getLocalCurrency();

        /* Do not transmit references as OrderReference as now, as they are in Lodo, not reference ids, but instead CONTACT PERSONS
        if (!empty($invoice->RefInternal)) {
            $this->invoiceO->OrderReference->ID = $invoice->RefInternal; // this should be RefCustomer but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
        }

        if (!empty($invoice->RefCustomer)) {
            $this->invoiceO->OrderReference->SalesOrderID = $invoice->RefCustomer; // this should be RefInternal but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
        }
        */

        ############################################################################################

        $this->invoiceO->AccountingSupplierParty->Party->WebsiteURI                     = $supplier->WWW;
        $this->invoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID        = preg_replace('/[^0-9]/', '', $supplier->OrgNumber);
        if (!empty($supplier->VatNumber)) {
            $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID        = $supplier->VatNumber;
            if ($supplier->ICountryCode == 'SE') {
                $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'SE:VAT';
            } else if ($supplier->ICountryCode == 'NO') {
                $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
            } // else leave empty
        } else if (strstr(strtolower($supplier->OrgNumber), 'mva')) {
            $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyID        = $supplier->OrgNumber;
            $this->invoiceO->AccountingSupplierParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
        }
        $this->invoiceO->AccountingSupplierParty->Party->PartyName->Name                = $supplier->CompanyName;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName      = $supplier->IAddress;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->BuildingNumber  = '';
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->CityName        = $supplier->ICity;
        $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone      = $supplier->IZipCode;
        if (empty($supplier->ICountryCode)) {
            $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode= 'NO';
        } else {
            $this->invoiceO->AccountingSupplierParty->Party->PostalAddress->Country->IdentificationCode= $supplier->ICountryCode;
        }

        if (!empty($supplier->Phone)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Telephone = $supplier->Phone;
        }
        if (!empty($supplier->Mobile)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Mobile = $supplier->Mobile;
        }
        if (!empty($supplier->Fax)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->Telefax = $supplier->Fax;
        }
        if (!empty($supplier->Email)) {
            $this->invoiceO->AccountingSupplierParty->Party->Contact->ElectronicMail = $supplier->Email;
        }


        if (!empty($invoice->RefCustomer)) { 
            // We should use RefSupplier but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefSupplier
            $ref_names = explode(" ", $invoice->RefCustomer, 2); // max two segments
            $this->invoiceO->AccountingSupplierParty->Party->Person->FirstName = $ref_names[0];
            if (count($ref_names) > 1) {
                $this->invoiceO->AccountingSupplierParty->Party->Person->FamilyName = $ref_names[1];
            } else {
                $this->invoiceO->AccountingSupplierParty->Party->Person->FamilyName = "";
            }
            $this->invoiceO->AccountingSupplierParty->Party->Person->MiddleName = "";
            $this->invoiceO->AccountingSupplierParty->Party->Person->JobTitle = "";
        }

        ############################################################################################
        $sql_customer = "select * from accountplan where AccountPlanID=" . (int) $invoice->CustomerAccountPlanID;
        $customer     = $_lib['storage']->get_row(array('query' => $sql_customer));

        $this->invoiceO->AccountingCustomerParty->Party->WebsiteURI                     = $customer->URL;
        $this->invoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID        = preg_replace('/[^0-9]+/', '', $customer->OrgNumber);

        if (!empty($customer->VatNumber)) {
            $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID        = $customer->VatNumber;
            if ($customer->CountryCode == 'SE') {
                $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'SE:VAT';
            } else if ($customer->CountryCode == 'NO') {
                $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
            } // else leave empty
        } else if (strstr(strtolower($customer->OrgNumber), 'mva')) {
            $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyID        = $customer->OrgNumber;
            $this->invoiceO->AccountingCustomerParty->Party->PartyTaxScheme->CompanyIDSchemeID = 'NO:ORGNR';
        }

        $this->invoiceO->AccountingCustomerParty->Party->PartyIdentification->ID = $customer->AccountPlanID;
        $this->invoiceO->AccountingCustomerParty->Party->PartyName->Name                = $customer->AccountName;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->StreetName      = $customer->Address;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->BuildingNumber  = '';
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->CityName        = $customer->City;
        $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->PostalZone     = $customer->ZipCode;
        if (empty($customer->CountryCode)) {
            $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode= 'NO';
        } else {
            $this->invoiceO->AccountingCustomerParty->Party->PostalAddress->Country->IdentificationCode= $customer->CountryCode;
        }

        if (!empty($customer->Phone)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Telephone = $customer->Phone;
        }
        if (!empty($customer->Mobile)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Mobile = $customer->Mobile;
        }
        if (!empty($customer->Fax)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->Telefax = $customer->Fax;
        }
        if (!empty($customer->Email)) {
            $this->invoiceO->AccountingCustomerParty->Party->Contact->ElectronicMail = $customer->Email;
        }

        if (!empty($invoice->RefInternal)) { 
            // We should use RefCustomer but has been hardcoded the wrong way other places in lodo and must be reverted (incl in existing database records) before we can use RefCustomer
            $ref_names = explode(" ", $invoice->RefInternal, 2); // max two segments
            $this->invoiceO->AccountingCustomerParty->Party->Person->FirstName = $ref_names[0];
            if (count($ref_names) > 1) {
                $this->invoiceO->AccountingCustomerParty->Party->Person->FamilyName = $ref_names[1];
            } else {
                $this->invoiceO->AccountingCustomerParty->Party->Person->FamilyName = "";
            }
            $this->invoiceO->AccountingCustomerParty->Party->Person->MiddleName = "";
            $this->invoiceO->AccountingCustomerParty->Party->Person->JobTitle = "";
        }



        ############################################################################################
        $this->invoiceO->PaymentMeans->PaymentMeansCode             = 42;
        $this->invoiceO->PaymentMeans->PaymentDueDate               = $invoice->DueDate;
        $this->invoiceO->PaymentMeans->PayeeFinancialAccount->ID    = $supplier->BankAccount;
        $this->invoiceO->PaymentMeans->PayeeFinancialAccount->Name  = 'Bank';

        if (!empty($invoice->BankAccount)) {
            $this->invoiceO->PaymentMeans->PayerFinancialAccount->ID = $invoice->BankAccount;
            $this->invoiceO->PaymentMeans->PayerFinancialAccount->Name  = 'Bank';
        }

        if (!empty($invoice->KID)) {
            $this->invoiceO->PaymentMeans->InstructionID = $invoice->KID;
            $this->invoiceO->PaymentMeans->InstructionNote = "KID";
        }
        ############################################################################################
        $query_invoiceline      = "select il.*, p.UNSPSC, p.EAN from invoiceoutline as il, product as p where il.InvoiceID='" . (int) $this->InvoiceID . "' and il.ProductID=p.ProductID and il.Active <> 0 order by il.LineID asc";
        #print "$query_invoiceline\n";
        $result2                = $_lib['db']->db_query($query_invoiceline);
        
        while($line = $_lib['db']->db_fetch_object($result2)) {
            #print_r($line);
            $linetotal      = $line->UnitCustPrice * $line->QuantityDelivered;
            $linetaxamount  = $linetotal * ($line->Vat / 100);
            $taxtotal      += $linetaxamount;
            $total         += $linetotal;
            $this->taxH[$line->Vat]->TaxableAmount    += $linetotal;
            $this->taxH[$line->Vat]->TaxAmount        += $linetaxamount;

            $this->invoiceO->InvoiceLine[$line->LineID]->ID                                     = $line->LineID;
            $this->invoiceO->InvoiceLine[$line->LineID]->LineExtensionAmount                    = $linetotal;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxAmount                    = $linetaxamount;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxSubtotal->TaxableAmount   = $linetotal;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxSubtotal->TaxAmount       = $linetaxamount;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxSubtotal->Percent         = $line->Vat;
            $this->invoiceO->InvoiceLine[$line->LineID]->TaxTotal->TaxSubtotal->TaxCategory->TaxScheme->ID = 'VAT';

            $this->invoiceO->InvoiceLine[$line->LineID]->Item->Name                             = $line->ProductName;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->Description                      = $line->Comment;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->SellersItemIdentification->ID    = $line->ProductID;
            $this->invoiceO->InvoiceLine[$line->LineID]->Item->CommodityClassification->UNSPSC->ItemClassificationCode = $line->UNSPSC;

            $this->invoiceO->InvoiceLine[$line->LineID]->Price->PriceAmount                     = $line->UnitCustPrice;
            $this->invoiceO->InvoiceLine[$line->LineID]->Price->BaseQuantity                    = $line->QuantityDelivered;
        }

        ############################################################################################
        $this->invoiceO->TaxTotal['TaxAmount'] = $taxtotal;
        
        #TODO: Subtotal should be repeated for each tax amount - forach - and function
        foreach($this->taxH as $VatPercent => $vat) {
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxableAmount                = $vat->TaxableAmount;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxAmount                    = $vat->TaxAmount;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->ID              = 'VAT';
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->Percent         = $VatPercent;
            $this->invoiceO->TaxTotal[$VatPercent]->TaxSubtotal->TaxCategory->TaxScheme->ID   = 'VAT';
        }

        $this->invoiceO->LegalMonetaryTotal->PayableAmount      = $total + $taxtotal;
        $this->invoiceO->LegalMonetaryTotal->TaxExclusiveAmount = $total;

        #print_r($this->invoiceO);
        
        $fb = new lodo_fakturabank_fakturabank();
        if($fb->write($this->invoiceO)) {
            
            $dataH = array();
            $dataH['InvoiceID']             = $this->InvoiceID;
            $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
            $dataH['FakturabankDateTime']   = $_lib['sess']->get_session('Datetime');
            $dataH['Locked']                = 1;
            
            $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
        }

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
            
        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
    }
}
?>
