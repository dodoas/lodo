<?
/*******************************************************************************
 * Lodo recurring invoice
 *
 * @author Thomas Ekdahl, Empatix AS,
 *         Blix Solutions AS
 * @copyright http://www.empatix.com/ Empatix AS, 1994-2005, post@empatix.com
 *            http://www.lodo.no, 2010
 *
 */

includelogic('fakturabank/fakturabank');
includelogic('kid/kid');

/*
 * for aa kjoere i batch maa litt ekstra logikk til
 */
includelogic('accounting/accounting');
includelogic('invoice/invoice');

class recurring {
    public $RecurringID       = 0;
    public $CustomerAccountPlanID   = 0;
    public $JournalID       = 0;
    public $VoucherType     = 'S';
    public $table_head      = 'recurringout';
    public $table_line      = 'recurringoutline';
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
            $this->JournalID = $this->RecurringID;
        #print "ferdig<br />\n";
    }

    function init($args) {
        global $_lib, $accounting;
        #Read the invoice to memory if it exists
        #print "xx0<br>\n";
        $this->clear_line();

        #print "INVOICE_ID: $this->RecurringID<br><br>\n";

        if($this->RecurringID) {
            #print "xx1<br>\n";
            $result_head = $_lib['db']->db_query("select * from $this->table_head where RecurringID=" . (int) $this->RecurringID);
            #print "result_head: select * from $this->table_head where RecurringID=" . (int) $this->RecurringID . "<br>\n";
            $result_line = $_lib['db']->db_query("select * from $this->table_line where RecurringID=" . (int) $this->RecurringID . " and Active=1");
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
            $headH['InvoiceDate']            = $_lib['sess']->get_session('Date');
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
                $headH['DueDate'] = $_lib['date']->add_Days($headH['InvoiceDate'], 10);
            }
        }

        if(!$headH['ProjectID'] && $accountplan->ProjectID) 
            $headH['ProjectID'] = $accountplan->ProjectID;

        if(!$headH['DepartmentID'] && $accountplan->DepartmentID) 
            $headH['DepartmentID'] = $accountplan->DepartmentID;

        /*$args['invoiceout_ICountry_'.$this->RecurringID] = $accountplan->address;
        $args['invoiceout_DCountry_'.$this->RecurringID] = $accountplan->address;*/
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
        if(strlen($headH['Period']) != 7)
            $headH['Period']            = $_lib['date']->get_this_period($headH['InvoiceDate']);

        #print_r($headH);
        if(!$headH['CustomerAccountPlanID'])
            $_lib['message']->add(array('message' => "Du m&aring; velge kunden som skal motta fakturaen"));

        unset($headH['TotalCustPrice']);
        
        if($_lib['setup']->get_value('kid.accountplanid') || $_lib['setup']->get_value('kid.RecurringID')) {
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
        $_lib['db']->db_update_multi_table($args, array('recurringout' => 'RecurringID', 'recurringoutline' => 'LineID'));

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
            #if($this->RecurringID) { #Delete old invoice
            #    $this->delete_invoice();
            #}

            unset($this->headH['DeliveryDate']);
            unset($this->headH['OrderDate']);
            unset($this->headH['inline']);

            $headH = $this->headH;
            if($this->debug) print_r($headH);
            $this->RecurringID = $_lib['storage']->store_record(array('data' => $headH, 'table' => $this->table_head, 'debug' => false));
            #print_r($this->lineH);

            /* Generate invoice line */
            #print "Generer fakturalinjer\n\n";
            foreach($this->lineH as $lineH)
            {
                $lineH['RecurringID'] = $this->RecurringID;
                if($this->debug) print_r($lineH);
                $_lib['storage']->store_record(array('data' => $lineH, 'table' => $this->table_line, 'debug' => false));
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
        #print "'JournalID'=>$this->RecurringID, 'VoucherType'=>'S', 'AccountPlanID'=>$this->AccountPlanID";
        #$this->journal(array('JournalID'=>$this->RecurringID, 'VoucherType'=>'S', 'AccountPlanID'=>$this->AccountPlanID));

        return $this->RecurringID;
    }

    /*******************************************************************************
    * Find a new RecurringID
    * @return RecurringID
    */
    function getNextID()
    {
        global $_lib;
        $sql = "SELECT RecurringID from recurring ORDER BY RecurringID DESC";
        $id = $_lib['storage']->get_row(array('query' => $sql));
        return $id->RecurringID + 1;
    }


    /*******************************************************************************
    * Create a new invoice and a empty invoice line
    * @param array(Date, Status, Active, FromCompanyID, InvoiceDate, PaymentDate, DeliveryDate);
    * @return RecurringID
    */
    function create($args)
    {
        global $_lib;
	$args['RecurringID'] = $this->getNextID();

        $this->init($args);
        $this->set_head($args);
        $this->set_line(array('Active' => 1));
        
        $this->make_invoice();

        return $args['RecurringID'];
    }

    /*******************************************************************************
    * Copy invoice
    * @param
    * @return
    */
    function copy()
    {
        global $_lib, $accounting;
        $this->OldRecurringID = $this->RecurringID;

        $this->clear_line();

        $accountplan = $accounting->get_accountplan_object($this->CustomerAccountPlanID);

        list($this->RecurringID, $message)        = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $this->VoucherType));

        $sql_head = "select * from $this->table_head where RecurringID='$this->OldRecurringID' and Active != 0";
        $result_head = $_lib['db']->db_query($sql_head);
        $headH = $_lib['db']->db_fetch_assoc($result_head);

        $headH['RecurringID']           = $this->RecurringID;
        $headH['OrderDate']           = $_lib['sess']->get_session('LoginFormDate');
        $headH['Period']              = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));
        
        $headH['Status']              = "progress";
        $headH['Active']              = 1;
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

        $query_invoiceline = "select * from $this->table_line where RecurringID='$this->OldRecurringID' and Active!=0 order by LineID asc";
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
        return $this->RecurringID;
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
        
        $_lib['message']->add(array('message' => "Linje $LineID p&aring; faktura $this->RecurringID er slettet"));
        
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
            if($key != action_invoicerecurring_new)
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
        $sql_delete_invoiceline = "delete from recurringoutline where RecurringID=" . $this->RecurringID;
        $_lib['db']->db_delete($sql_delete_invoiceline);

        $sql_delete_invoice     = "delete from recurringout where RecurringID=" . $this->RecurringID;
        $_lib['db']->db_delete($sql_delete_invoice);

	$sql_delete_recurring =  "delete from recurring where RecurringID=" . $this->RecurringID;
	$_lib['db']->db_delete($sql_delete_recurring);

        #print "Sletter: $this->RecurringID, $this->VoucherType<br>\n";

        $_lib['message']->add(array('message' => "Faktura $this->RecurringID er slettet"));

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
        $invoicelineH['recurringoutline_Active']          = 1;
        $invoicelineH['recurringoutline_RecurringID']    = $this->RecurringID;

        return $_lib['db']->db_new_hash($invoicelineH, $this->table_line);
    }
    
    /**
     * lag en ny recurring invoice
     * @param update
     *     updater en allerede eksisterende
     */
    function create_recurring($args, $update = false)
    {
        global $_lib;
	
	$data = array(
	    'RecurringID' => $this->RecurringID,
	    'StartDate' => $args["recurring_StartDate_$this->RecurringID"],
	    'TimeInterval' => $args["recurring_TimeInterval_$this->RecurringID"],
	    'PrintInterval' => $args["recurring_PrintInterval_$this->RecurringID"],
	    'EndDate' => $args["recurring_EndDate_$this->RecurringID"]
	    );

	if($update === false)
	{
	    $get = sprintf("select RecurringID from recurring where RecurringID = '%d'", $this->RecurringID);
	    $row = $_lib['storage']->get_row(array('query'=>$get));
	    
	    if(!$row)
	    {
		$_lib['storage']->store_record(array('data' => $data, 'table' => 'recurring', 'debug' => false));
	    }
	}
	else
	{
	    $_lib['db']->db_update(
		sprintf("update recurring SET StartDate = '%s', TimeInterval = '%s', PrintInterval = '%d', EndDate = '%s' WHERE RecurringID = '%d'", 
			mysql_escape_string($data['StartDate']), mysql_escape_string($data['TimeInterval']),
			$data['PrintInterval'], $data['EndDate'], $this->RecurringID));    
	}
    }

    /**
     * Bytt ut enkelte tokener i en streng
     *
     * @param str
     *     streng hvor utbyttingen skal skje
     * @param date
     *     dato for fakturaen
     * 
     * @return 
     *     streng med følgende erstatninger: 
     *      - %M med månedsnavn, 
     *      - %W med ukestall
     *      - %K med kvartalstreng
     *      - %H med halvårstreng
     *      - %Y med årstall
     */
    function replace_tokens($str, $date)
    {
	$date_info = $this->get_date_info($date);
        
	$replace = array('%M',                 '%W',            '%K',                  '%H',                 '%Y',            '%LM',            '%NM',            '%LY',             '%NY');
	$with    = array($date_info['m_name'], $date_info['w'], $date_info['kvartal'], $date_info['halvar'], $date_info['y'], $date_info['lm'], $date_info['nm'], $date_info['y']-1, $date_info['y'] + 1);
        
	return str_replace($replace, $with, $str);
    }
    
    /**
     * Legger til en ny invoice i invoiceoutline-tabellen
     * @param
     *     recurring-linjen som skal brukes som mal
     * @return
     *     true om fakturaen ble sendt, ellers false
     */
    function send_invoice($row) 
    {
	global $accounting, $_lib;
        
        $sql = "SELECT RecurringID, CustomerAccountPlanID, CommentCustomer FROM recurringout WHERE RecurringID = " . $row["RecurringID"];
        $r = $_lib['db']->db_query($sql);
        $head = $_lib['db']->db_fetch_assoc($r);
        
	$id = $head['RecurringID'];
        if($head['CustomerAccountPlanID'] == 0)
            return false;

        /* lager ny account per gang for aa unngaa tull */
	$accounting = new accounting();
	$invoice = new invoice(array('VoucherType' => 'S', 'CustomerAccountPlanID' => $head['CustomerAccountPlanID']));
	$invoice->copy_from_recurring($id, $this->replace_tokens($head["CommentCustomer"], $row["LastDate"]));

        /* update fikser bla. forfallsdato */
        $sql = sprintf("SELECT * FROM invoiceout WHERE InvoiceID = %d", $invoice->InvoiceID);
        $r = $_lib['db']->db_query($sql);
        $head = $_lib['db']->db_fetch_assoc($r);
        $invoice->update($head);

        return true;
    }
    
    /**
     * Send en repeterende faktura
     * @param recurring
     *     Linje fra tabellen 'recurring'
     * @return
     *     true
     */
    function check_and_send($recurring)
    {
	global $_lib;

        $recurring_intervals = $this->get_intervals();

	/* slår opp SQL INTERVAL streng for gitt interval. Se interval.inc */
	$interval = $recurring_intervals[ $recurring["TimeInterval"] ][1];
	$printinterval = $recurring["PrintInterval"] . " DAY";

        if($recurring["StartDate"] == "0000-00-00")
            return;
        
	if($recurring["LastDate"] == "0000-00-00")
	{
            $sql = sprintf("UPDATE recurring 
					SET LastDate = StartDate - INTERVAL %s
					WHERE RecurringID = %d", $interval, $recurring["RecurringID"]);

            $_lib['db']->db_query($sql);

            $r = $_lib['db']->db_query(sprintf("SELECT * FROM recurring WHERE RecurringID = %d", $recurring["RecurringID"]));
            $recurring = $_lib['db']->db_fetch_assoc($r);
	}
        
	/* teller for hvor mange fakturaer som er generert */
	$n = 0;
        
	$_lib['message']->add(sprintf("%d (%s):\n", $recurring["RecurringID"], $interval));
        
	while(1)
	{
            /* 
               oppdater denne linjen med ny LastDate om det skal sendes ut en ny
               faktura nå

            */
            $sql = sprintf(
                        "
			UPDATE recurring 
				SET LastDate = DATE_ADD(LastDate,  INTERVAL %s)
			WHERE RecurringID = %d
				AND DATEDIFF(DATE_SUB(DATE_ADD(LastDate, INTERVAL %s), INTERVAL %s), CURDATE()) <= 0
				AND (EndDate = '0000-00-00' OR DATE_ADD(LastDate, INTERVAL %s) < EndDate)
			",
                           $interval,
                           $recurring["RecurringID"],
                           $interval, $printinterval, $interval
		);
            
            $r = $_lib['db']->db_query($sql);
            echo $sql;

            $sql = sprintf("SELECT * FROM recurring WHERE RecurringID = %d",
                           $recurring["RecurringID"]);

            $r = $_lib['db']->db_query($sql);
            $row = $_lib['db']->db_fetch_assoc($r);
            
            /* sjekker om LastDate ble oppdatert */
            if($row["LastDate"] == $recurring["LastDate"])
            {
                $_lib['message']->add(sprintf("\t%s fakturaer\n", $n));
                
                return true;
            }
            else
            {
                $this->send_invoice($row);
                $recurring = $row;
                $n++;
            }
	}
    }
    
    /**
     * Funksjon for å generere info om måneden til en faktura.
     * @param date
     *     dato fakturaen går
     * @return array
     *      m_name => månedsnavn
     *      m      => månedstall
     *      w      => ukestall
     *      y      => årstall
     *      kvartal=> streng med enten: første, andre, tredje, fjerde
     *      halvar => streng med enten: første, andre
     */
    function get_date_info($date)
    {
	$date = strtotime($date);
	$m = (int)date("n", $date);
	$w = (int)date("W", $date);
	$y = (int)date("Y", $date);
        
	$mnd = array("N/A",
                     "Januar", 
                     "Februar",
                     "Mars",
                     "April",
                     "Mai",
                     "Juni",
                     "Juli",
                     "August",
                     "September",
                     "Oktober",
                     "November",
                     "Desember");
        
	$num = array(
            "f&oslash;rste",
            "andre",
            "tredje",
            "fjerde");
        
	/* 
	   kvartal:
             1      2      3       4
           1,2,3  4,5,6  7,8,9  10,11,12
        */
	if($m <= 3)
            $k = 1;
	else if($m <= 6)
            $k = 2;
	else if($m <= 9)
            $k = 3;
	else
            $k = 4;
        
	/* halvår */
	if($m <= 6)
            $h = 1;
	else
            $h = 2;

        if($m == 1) {
            $lm = 12;
            $nm = 2;
        }
        else if($m == 12) {
            $lm = 11;
            $nm = 1;
        }
        else { 
            $lm = $m-1;
            $nm = $m+1;
        }
        

	return 
            array(
                'm_name' => $mnd[$m],       /* månedsnavn */
                'm' => $m,                  /* månedstall */
                'w' => $w,                  /* ukestall */
                'y' => $y,                  /* årstall */
                'kvartal' => $num[$k - 1],  /* kvartalstreng: først, andre, tredje, fjerde */
                'halvar'  => $num[$h - 1],  /* halvårstreng: første, andre */
                'nm' => $mnd[$nm],
                'lm' => $mnd[$lm]
		);
        
    }

    /**
     * Henter tabell me de forskjellige intervalene. 
     * etter dette er satt i produksjon kan man ikke fjerne linjer eller endre betydning
     * uten at det vil foraarsake mye rot 
     */
    function get_intervals() 
    {
        $recurring_intervals =
            array(  
                "year" => array("&Aring;rlig", "1 YEAR"),
                "y13"  => array("Hver 3. m&aring;ned", "3 MONTH"),
                "y14"  => array("Hver 4. m&aring;ned", "4 MONTH"),
                "mnd"  => array("Hver m&aring;ned", "1 MONTH"),
                "4wk"  => array("Hver 4. uke", "4 WEEK"),
                "2wk"  => array("Hver 2. uke", "2 WEEK"),
                "wk"   => array("Hver uke", "1 WEEK"),
                "day"  => array("Hver dag", "1 DAY")
                );

        return $recurring_intervals;
    }
}

require_once($_SETUP['HOME_DIR'] . "/code/lib/cache/cache.class.php");
require_once($_SETUP['HOME_DIR'] . "/code/lib/setup/setup.class.php");
require_once($_SETUP['HOME_DIR'] . "/code/lib/format/format.class.php");

function shutdown_handle()
{
  global $_lib;

//  echo $_lib['message']->get();
   
}

class model_invoicerecurring_recurring 
{
    function database_list() {
        global $_lib;

        $ret = array();
        $system_dbs = array('lodo', 'mysql', 'test', 'information_schema');
        
        $query_show = "show databases";
        $result     = $_lib['db']->db_query($query_show);
        $i = 0;
        while ($row = $_lib['db']->db_fetch_object($result)) 
        {
            if (in_array($row->Database, $system_dbs)) 
            {
                continue;
            }

            $ret[] = $row;
        }
            
        return $ret;
    }

    function iter_all_db() 
    {
        global $_lib, $_SETUP;

        register_shutdown_function('shutdown_handle');
        $dbs = $this->database_list();
        
        foreach($dbs as $db) 
        {
            $name = $db->Database;
            $_lib['message']->add("\n$name\n---------------\n");

            /* sett opp alle de forskjellige _lib-entriene som kreves for denne koden */
            $_lib['storage'] = $_lib['db'] = 
                new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'], 
                                   'database' => $name, 
                                   'username' => $_SETUP['DB_USER_DEFAULT'], 
                                   'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

            $_lib['cache'] = new Cache(array());
            $_lib['setup'] = new framework_lib_setup(array());
            $_lib['format'] = new format(array('_NF' => $_NF, '_DF' => $_DF, '_dbh' => $_dbh, '_dsn' => $_dsn));

            $query = "SHOW TABLES LIKE 'mvaavstemming';";
            $row = $_lib['db']->get_row(array('query' => $query));
            
            if (empty($row)) {
                continue;
            }

            /* companydef needed for journaling as default values. No impact on invoices created. */
            $sql = "SELECT * FROM company WHERE CompanyID=1";
            $row = $_lib['db']->get_row(array('query' => $sql));
            $_lib['sess']->companydef = $row;
            
            /* check and send */
            global $accounting;
            
            $accounting = 0;

            /* check_and_send does not check if startDate is in the future. 
               This is done here to save a query. */
            $r =  $_lib['db']->db_query("SELECT * FROM recurring WHERE DATE_SUB(StartDate, INTERVAL `PrintInterval` DAY) <= NOW()") 
                           or die("ERROR: " . mysql_error());
            $recurring_array = array();	
            while($row = $_lib['db']->db_fetch_assoc($r))
            {   
                $recurring_array[] = $row;
            }

            foreach($recurring_array as $line)
            {   
                $recurring = new recurring(array());
                $recurring->check_and_send($line);
            }


        }
    }

}

?>
