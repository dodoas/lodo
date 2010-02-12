<?
class logic_remittance_remittance implements Iterator {

    private $iteratorH                   = array() ;
    public $RemittanceStatus             = 'recieved';
    
    private $formatH = array(
        'BETFOR00' => array(
            'name'                  => 'startrecord',
            'AH-ID'                 => array('start' => 1,   'stop' => 2,   'type' => 'text', 'value' => 'AH'),
            'AH-VERSJON'            => array('start' => 3,   'stop' => 3,   'type' => 'text', 'value' => '2'),
            'AH-RETURKODE'          => array('start' => 4,   'stop' => 5,   'type' => 'int' , 'value' => '00'),
            'AH-RUTINE-ID'          => array('start' => 6,   'stop' => 9,   'type' => 'text', 'value' => 'TBII'), #Innland
            'AH-TRANSDATO'          => array('start' => 10,  'stop' => 13,  'type' => 'monthdate',  'field' => 'Date'),
            'AH-TRANS-SEKVNR'       => array('start' => 14,  'stop' => 19,  'type' => 'int', 'field' => 'RemittanceDaySequence'),
            'AH-TRANSKODE'          => array('start' => 20,  'stop' => 27,  'type' => 'text', 'value' => ''),
            'AH-BRUKERID'           => array('start' => 28,  'stop' => 38,  'type' => 'text', 'value' => ''),
            'AH-ANTALL'             => array('start' => 39,  'stop' => 40,  'type' => 'text', 'value' => '04'),
            'TRANSAKSJONSKODE'      => array('start' => 41,   'stop' => 48,  'type' => 'text', 'value' => 'BETFOR00'),
            'FORETAKSNUMMER'        => array('start' => 49,   'stop' => 59,  'type' => 'int' , 'field' => 'CustomerOrgNumber'),
            'DIVISJON'              => array('start' => 60,   'stop' => 70,  'type' => 'text', 'value' => ''),
            'SEKVENSKONTROLLFELT'   => array('start' => 71,   'stop' => 74,  'type' => 'int',  'field' => 'RemittanceSequence'),
            'RESERVERT1'            => array('start' => 75,   'stop' => 80,  'type' => 'text', 'value' => ''),
            'PRODUKSJONSDATO'       => array('start' => 81,   'stop' => 84,  'type' => 'monthdate', 'field' => 'Date'),
            'PASSORD'               => array('start' => 85,   'stop' => 94,  'type' => 'text', 'value' => ''),
            'RUTINE VERSJON'        => array('start' => 95,   'stop' => 104, 'type' => 'text', 'value' => 'VERSJON002'),
            'NYTT PASSORD'          => array('start' => 105,  'stop' => 114, 'type' => 'text', 'value' => ''),
            'OPERAT¯RNUMMER'        => array('start' => 115,  'stop' => 125, 'type' => 'text', 'value' => ''),
            'SIGILL: SEGL-BRUK'     => array('start' => 126,  'stop' => 126, 'type' => 'text', 'value' => ''),
            'SIGILL: SEGL-DATO'     => array('start' => 127,  'stop' => 132, 'type' => 'text',  'value' => ''),
            'SIGILL: DEL-N¯KKEL'    => array('start' => 133,  'stop' => 152, 'type' => 'text',  'value' => ''),
            'SIGILL: SEGL HVORDAN'  => array('start' => 153,  'stop' => 153, 'type' => 'text', 'value' => ''),
            'RESERVERT2'            => array('start' => 154,  'stop' => 296, 'type' => 'text', 'value' => ''),
            'EGENREFERANSE'         => array('start' => 297,  'stop' => 311, 'type' => 'text', 'value' => ''),
            'RESERVERT3'            => array('start' => 312,  'stop' => 320, 'type' => 'text', 'value' => ''),
        ),
        'BETFOR21' => array(
            'name'                  => 'Overf¿rselsrecord',
            'AH-ID'                 => array('start' => 1,   'stop' => 2,   'type' => 'text', 'value' => 'AH'),
            'AH-VERSJON'            => array('start' => 3,   'stop' => 3,   'type' => 'text', 'value' => '2'),
            'AH-RETURKODE'          => array('start' => 4,   'stop' => 5,   'type' => 'int' , 'value' => '00'),
            'AH-RUTINE-ID'          => array('start' => 6,   'stop' => 9,   'type' => 'text', 'value' => 'TBII'), #Innland
            'AH-TRANSDATO'          => array('start' => 10,  'stop' => 13,  'type' => 'monthdate',  'field' => 'Date'),
            'AH-TRANS-SEKVNR'       => array('start' => 14,  'stop' => 19,  'type' => 'int', 'field' => 'RemittanceDaySequence'),
            'AH-TRANSKODE'          => array('start' => 20,  'stop' => 27,  'type' => 'text', 'value' => ''),
            'AH-BRUKERID'           => array('start' => 28,  'stop' => 38,  'type' => 'text', 'value' => ''),
            'AH-ANTALL'             => array('start' => 39,  'stop' => 40,  'type' => 'text', 'value' => '04'),
            'TRANSAKSJONSKODE'      => array('start' => 41,   'stop' => 48,  'value' => 'BETFOR21', 'type' => 'text'),
            'FORETAKSNUMMER'        => array('start' => 49,   'stop' => 59,  'field' => 'CustomerOrgNumber', 'type' => 'int'),
            'KONTONUMMER'           => array('start' => 60,   'stop' => 70,  'field' => 'CustomerBankAccount', 'type' => 'bankaccount'),
            'SEKVENSKONTROLLFELT'   => array('start' => 71,   'stop' => 74,  'field' => 'RemittanceSequence', 'type' => 'int'),
            'REFERANSENUMMER'       => array('start' => 75,   'stop' => 80,  'value' => '', 'type' => 'text'), #Brukes til sletting av tidligere betalingsoppdrag
            'BETALINGSDATO'         => array('start' => 81,   'stop' => 86,  'field' => 'DueDate', 'type' => 'date'),
            'EGENREF. OPPDRAG'      => array('start' => 87,   'stop' => 116, 'field' => 'ID', 'type' => 'text'), #Egenreferanse for avstemming i eget EDB system
            'RESERVERT1'            => array('start' => 117,  'stop' => 117, 'value' => '', 'type' => 'text'),
            'MOTTAKERS KONTONUMMER' => array('start' => 118,  'stop' => 128, 'field' => 'SupplierBankAccount', 'type' => 'bankaccount'),
            'MOTTAKERS NAVN'        => array('start' => 129,  'stop' => 158, 'field' => 'IName', 'type' => 'text'),
            'ADRESSE 1'             => array('start' => 159,  'stop' => 188, 'field' => 'IAddress', 'type' => 'text'),
            'ADRESSE 2'             => array('start' => 189,  'stop' => 218, 'value' => '', 'type' => 'text'),
            'POSTNR'                => array('start' => 219,  'stop' => 222, 'field' => 'IZipCode', 'type' => 'int'),
            'POSTSTED'              => array('start' => 223,  'stop' => 248, 'field' => 'ICity', 'type' => 'text'),
            'BEL¯P TIL EGEN KONTO'  => array('start' => 249,  'stop' => 263, 'value' => '0', 'type' => 'amount'), # Skal kun oppgis ved overf¿ring mellom egne konto
            'TEKSTKODE'             => array('start' => 264,  'stop' => 266, 'value' => '602', 'type' => 'int'),  # Kommer pŒ mottakers kontoudrag KID/Melding etc 602 = default - men kan spesifiseres noe bedre
            'TRANSAKSJONSTYPE'      => array('start' => 267,  'stop' => 267, 'value' => 'F', 'type' => 'text'),   #F = Fakturautbetaling, L=L¿nn, E=Egen konto, M=Masseutbetaling
            'SLETTEKODE'            => array('start' => 268,  'stop' => 268, 'value' => '', 'type' => 'text'),    #S - hvis tidligere records skal slettes
            'TOTALBEL¯P'            => array('start' => 269,  'stop' => 283, 'value' => '', 'type' => 'amount'),  #Benyttes kun pŒ avregningsreturen
            'KLIENTREFERANSE'       => array('start' => 284,  'stop' => 288, 'field' => 'Database', 'type' => 'text'), #Klientregnskap
            'VALUTERINGSDATO'       => array('start' => 289,  'stop' => 294, 'field' => 'DueDate', 'type' => 'date'),
            'VALUTERING MOTT. BANK' => array('start' => 295,  'stop' => 300, 'value' => '', 'type' => 'date'), #type text fordi den skal v¾re blank nŒr den er tom
            'SLETTERSAK'           => array('start' => 301,  'stop' => 301, 'value' => '', 'type' => 'text'),
            'RESERVERT2'            => array('start' => 302,  'stop' => 310, 'value' => '', 'type' => 'text'),
            'BLANKETTNUMMER'        => array('start' => 311,  'stop' => 320, 'value' => '', 'type' => 'int'),
        ),
        'BETFOR23' => array(
            'name'                  => 'Fakturarecord',
            'AH-ID'                 => array('start' => 1,   'stop' => 2,   'type' => 'text', 'value' => 'AH'),
            'AH-VERSJON'            => array('start' => 3,   'stop' => 3,   'type' => 'text', 'value' => '2'),
            'AH-RETURKODE'          => array('start' => 4,   'stop' => 5,   'type' => 'int' , 'value' => '00'),
            'AH-RUTINE-ID'          => array('start' => 6,   'stop' => 9,   'type' => 'text', 'value' => 'TBII'), #Innland
            'AH-TRANSDATO'          => array('start' => 10,  'stop' => 13,  'type' => 'monthdate',  'field' => 'Date'),
            'AH-TRANS-SEKVNR'       => array('start' => 14,  'stop' => 19,  'type' => 'int', 'field' => 'RemittanceDaySequence'),
            'AH-TRANSKODE'          => array('start' => 20,  'stop' => 27,  'type' => 'text', 'value' => ''),
            'AH-BRUKERID'           => array('start' => 28,  'stop' => 38,  'type' => 'text', 'value' => ''),
            'AH-ANTALL'             => array('start' => 39,  'stop' => 40,  'type' => 'text', 'value' => '04'),
            'TRANSAKSJONSKODE'      => array('start' => 41,   'stop' => 48,  'value' => 'BETFOR23', 'type' => 'text'),
            'FORETAKSNUMMER'        => array('start' => 49,   'stop' => 59,  'field' => 'CustomerOrgNumber',   'type' => 'int'),
            'KONTONUMMER'           => array('start' => 60,   'stop' => 70,  'field' => 'CustomerBankAccount', 'type' => 'bankaccount'),
            'SEKVENSKONTROLLFELT'   => array('start' => 71,   'stop' => 74,  'field' => 'RemittanceSequence', 'type' => 'int'),
            'REFERANSENUMMER'       => array('start' => 75,   'stop' => 80,  'value' => '', 'type' => 'text'),
            'MELDING TIL MOTTAKER1' => array('start' => 81,   'stop' => 120, 'field' => 'SupplierMessage1', 'type' => 'text'), #Fritekst melding til mottaker, beyttes hvis ikke strukturert med KID
            'MELDING TIL MOTTAKER2' => array('start' => 121,  'stop' => 160, 'field' => 'SupplierMessage2', 'type' => 'text'),
            'MELDING TIL MOTTAKER3' => array('start' => 161,  'stop' => 200, 'field' => 'SupplierMessage3', 'type' => 'text'),
            'KUNDEID_FELT'          => array('start' => 201,  'stop' => 227, 'field' => 'KID', 'type' => 'text'),
            'EGENREFERANSE FAKTURA' => array('start' => 228,  'stop' => 257, 'field' => 'ID', 'type' => 'text'),
            'FAKTURABEL¯P'          => array('start' => 258,  'stop' => 272, 'field' => 'RemittanceAmount', 'type' => 'amount'),
            'DEBET/KREDIT KODE'     => array('start' => 273,  'stop' => 273, 'value' => 'D', 'type' => 'text'), #debet C=Credit
            'FAKURANUMMER'          => array('start' => 274,  'stop' => 293, 'field' => 'InvoiceNumber', 'type' => 'text'),
            'L¯PENUMMER'            => array('start' => 294,  'stop' => 296, 'value' => '', 'type' => 'text'),
            'SLETTERSAK'           => array('start' => 297,  'stop' => 297, 'value' => '', 'type' => 'text'),
            'KUNDENUMMER'           => array('start' => 298,  'stop' => 312, 'field' => 'CustomerNumber', 'type' => 'text'), #Kundenummer hos mottaker
            'FAKTURADATO'           => array('start' => 313,  'stop' => 320, 'field' => 'InvoiceDate', 'type' => 'date'),
        ),
        'BETFOR99' => array(
            'name'                  => 'avslutningsrecord',
            'AH-ID'                 => array('start' => 1,   'stop' => 2,   'type' => 'text', 'value' => 'AH'),
            'AH-VERSJON'            => array('start' => 3,   'stop' => 3,   'type' => 'text', 'value' => '2'),
            'AH-RETURKODE'          => array('start' => 4,   'stop' => 5,   'type' => 'int' , 'value' => '00'),
            'AH-RUTINE-ID'          => array('start' => 6,   'stop' => 9,   'type' => 'text', 'value' => 'TBII'), #Innland
            'AH-TRANSDATO'          => array('start' => 10,  'stop' => 13,  'type' => 'monthdate',  'field' => 'Date'),
            'AH-TRANS-SEKVNR'       => array('start' => 14,  'stop' => 19,  'type' => 'int', 'field' => 'RemittanceDaySequence'),
            'AH-TRANSKODE'          => array('start' => 20,  'stop' => 27,  'type' => 'text', 'value' => ''),
            'AH-BRUKERID'           => array('start' => 28,  'stop' => 38,  'type' => 'text', 'value' => ''),
            'AH-ANTALL'             => array('start' => 39,  'stop' => 40,  'type' => 'text', 'value' => '04'),
            'TRANSAKSJONSKODE'      => array('start' => 41,   'stop' => 48,  'value' => 'BETFOR99', 'type' => 'text'),
            'FORETAKSNUMMER'        => array('start' => 49,   'stop' => 59,  'field' => 'CustomerOrgNumber', 'type' => 'int'),
            'RESERVERT1'            => array('start' => 60,   'stop' => 70,  'value' => '', 'type' => 'text'),
            'SEKVENSKONTROLLFELT'   => array('start' => 71,   'stop' => 74,  'field' => 'RemittanceSequence', 'type' => 'int'),
            'RESERVERT2'            => array('start' => 75,   'stop' => 80,  'value' => '', 'type' => 'text'),
            'PRODUKSJONSDATO'       => array('start' => 81,   'stop' => 84,  'field' => 'Date', 'type' => 'monthdate'),
            'ANTALL OPPDRAG'        => array('start' => 85,   'stop' => 88,  'field' => 'NumTransactions', 'type' => 'int'),
            'TOTALSUM FIL'          => array('start' => 89,   'stop' => 103, 'field' => 'TotalAmount', 'type' => 'amount'),
            'ANTALL RECORDS'        => array('start' => 104,  'stop' => 108, 'field' => 'NumRecords', 'type' => 'int'),
            'RESERVERT3'            => array('start' => 109,  'stop' => 271, 'value' => '', 'type' => 'text'),
            'SIGILL: SECURITY'      => array('start' => 272,  'stop' => 275, 'value' => '', 'type' => 'text'),
            'SIGILL: LANGUAGE'      => array('start' => 276,  'stop' => 276, 'value' => '', 'type' => 'text'),
            'SIGILL: VERSJON'       => array('start' => 277,  'stop' => 277, 'value' => '', 'type' => 'text'),
            'SIGILL: INTERFACE'     => array('start' => 278,  'stop' => 278, 'value' => '', 'type' => 'text'),
            'SIGILL: KONTROLLFELT'  => array('start' => 279,  'stop' => 296, 'value' => '', 'type' => 'text'),
            'VERSJON SOFTWARE'      => array('start' => 297,  'stop' => 312, 'field' => 'VersionSoftware', 'type' => 'text'),
            'VERSJON BANK'          => array('start' => 313,  'stop' => 320, 'value' => '', 'type' => 'text'),
        ),
    );

    /***************************************************************************
    * Construct
    * @param 
    * @return
    */
    public function __construct($args) {
        global $_lib;
    
        foreach($args as $key => $value) {
            $this->{$key} = $value;
            #print "$key => $value<br>\n";
        }
        
        if(!$this->FromDate) {
            $this->FromDate = $_lib['sess']->get_session('DateStartYear');
        }

        if(!$this->ToDate) {
            $this->ToDate   = $_lib['sess']->get_session('DateEndYear');
        }
    }

    /***************************************************************************
    * Generate query to list remittance invoices
    * @param status
    * @return populated object.
    */
    #List all incoming invoices according to status
    function fill($args) {
        global $_lib;

        $status = $args['status'];

        $query = "select * from invoicein as i";
        $query .= " where i.RemittanceAmount > 0 and ";
        #$query .= " RemittanceStatus ='sent' and"; #Remember this in production to avoid duplicate payments
        $query .= " PaymentMeans ='42' and"; #Only pay invoices that is not already marked as payed by cash or card

        #Should check that the status is such that this invoice should be payed (ref payment meny, cash and credit card payed invoices should not be listed here.

        if($this->RemittanceStatus) {
            $query .= " i.RemittanceStatus='$this->RemittanceStatus' and ";
        }
        if($this->InvoiceNumber) {
            $query .= " i.InvoiceNumber like '%$this->InvoiceNumber%' and ";
        }
        if($this->FromDate) {
            $query .= " i.InvoiceDate >= '$this->FromDate' and ";
        }
        if($this->ToDate) {
            $query .= " i.InvoiceDate <= '$this->ToDate' and ";
        }
        if($this->IName) {
            $query .= " i.IName like '%$this->IName%' and ";
        }

        $query = substr($query, 0, -4);
        $query .= " order by i.InvoiceDate asc";

        #print $query;
        $result     = $_lib['db']->db_query($query);
        while($row  = $_lib['db']->db_fetch_object($result)) {

            $row->run = true;
            if($row->RemittanceAmount < 0) {
                $row->Status .= 'Remitteringsbel¿p kan ikke v¾re negativt';
                $row->run   = false;
            }
            
            $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
            $new_pattern    = array("", "", "");
            $row->SupplierBankAccount = preg_replace($old_pattern, $new_pattern, $row->SupplierBankAccount); 
            $row->CustomerBankAccount = preg_replace($old_pattern, $new_pattern, $row->CustomerBankAccount);             
            
            if(strlen($row->SupplierBankAccount) != 11) {
                $row->Status .= 'Mottaker bankkonto inneholder ikke 11 siffer';
                $row->run   = false;
            } elseif(!$this->isValidBankAccount($row->SupplierBankAccount)) {
                $row->Status .= 'Mottaker bankkonto er tastet feil';
                $row->run   = false;                
            }
            
            if(strlen($row->CustomerBankAccount) != 11) {
                $row->Status .= 'Betaler bankkonto inneholder ikke 11 siffer';
                $row->run   = false;
            } elseif(!$this->isValidBankAccount($row->CustomerBankAccount)) {
                $row->Status .= 'Betaler bankkonto er tastet feil';
                $row->run   = false;                
            }
            if(!$row->IZipCode || !$row->ICity) {
                $row->run = false;
                $row->Status .= "Leverand&oslash;r postnummer eller poststed mangler";
            }
            if(!$row->InvoiceNumber) {
                $row->Status .= 'Fakturanummer mangler';
                $row->run   = false;
            }
            if(!$row->InvoiceDate) {
                $row->Status .= 'Fakturadato mangler';
                $row->run   = false;
            }
            if(!$row->DueDate) {
                $row->Status .= 'Forfallsdato mangler';
                $row->run   = false;
            }
            
            if(!$row->run) {
                $row->Class       = 'red';
            }
            
            $this->iteratorH[] = $row;
        }
    }

    /***************************************************************************
    * Generate global parameters for telepay
    * @param
    * @return
    */
    function head() {
        global $_lib;

        $query  = "select max(RemittanceDaySequence) from invoicein where RemittanceSendtDateTime='" . $_lib['sess']->get_session('Date') . "' and Active=1";
        #print "Finn h¿yeste dag sekvens: $query";
        $daysequence = $_lib['storage']->get_row(array('query' => $query));

        $query  = "select max(RemittanceSequence) from invoicein where Active=1";
        #print "Finn h¿yeste sekvens: $query";
        $sequence = $_lib['storage']->get_row(array('query' => $query));
        
        #print "<h2>Her kommer remitteringsfila p&aring; TelePay 2.0.1 formatet</h2>";
        $transaction                                = new stdClass();
        $this->transaction->RemittanceSequence            = $daysequence->RemittanceSequence + 1; #MŒ kalkuleres og settes
        $this->transaction->RemittanceDaySequence         = $daysequence->RemittanceDaySequence; #MŒ kalkuleres og settes

        #$this->transaction->BatchID              = 99; #MŒ kalkuleres og settes
        $this->transaction->Date                  = $_lib['sess']->get_session('Date');
        $this->transaction->Datetime              = $_lib['sess']->get_session('Datetime');
        $this->transaction->VersionSoftware       = '00001.00    LODO';
        
        $old_pattern                              = array("/[^0-9]/");
        $new_pattern                              = array("");
        $this->transaction->CustomerOrgNumber     = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber'))); 
        $this->transaction->CustomerName          = $_lib['sess']->get_companydef('CompanyName');
        $this->transaction->Database              = $_SETUP['DB_NAME']['0'];
        
        $this->transaction->NumTransactions       = 0;
        $this->transaction->TotalAmount           = 0;
        $this->transaction->NumRecords            = 0;    

       if(!$InvoiceO->CustomerBankAccount) { #Sjekke lengde og modulus pŒ kontonummer ogsŒ
           $_lib['message']->Add("Betalerkonto mangler");
           return;
       }
       if(!$this->transaction->CustomerOrgNumber) {
           $_lib['message']->Add("Orgnummer/personnummer mangler p&aring; betaler");
           return;
       }
    }

    /***************************************************************************
    * Generate telepay 2.01. format
    * @param 
    * @return string containting a telepay file.
    */
    function pay($args) {
        global $_lib, $_SETUP;
        #make the actual payment file and configuration

        $this->head();
        
        #print_r($this->iteratorH);
        $file .= $this->format('BETFOR00', new stdClass());
        foreach($this->iteratorH as $InvoiceO) {

            ########################################################################################
            #Error checking
            #MŒ sjekke at det er et positivt bel¿p
            if($InvoiceO->RemittanceAmount < 0) {
                $_lib['message']->Add("Bel&oslash;p m&aring; v&aelig;re st&oslash;rre enn null - Faktura: $InvoiceO->InvoiceNumber");
                continue;
            }

            if(!$InvoiceO->SupplierBankAccount) { #Sjekke lengde og modulus pŒ kontonummer ogsŒ
                $_lib['message']->Add("Mottakerkonto mangler p&aring; faktura: $InvoiceO->InvoiceNumber");
                continue;
            }

            ########################################################################################
            $this->transaction->NumTransactions++; #Only count BETFOR21
            $this->transaction->TotalAmount         += $InvoiceO->RemittanceAmount;
            $this->transaction->ID                  = $InvoiceO->ID;
            $this->transaction->RemittanceSequence++;
            $this->transaction->RemittanceSequence  = $this->transaction->RemittanceSequence;
            $InvoiceO->CustomerNumber = ''; #We do not have the customer number here, or??????? Just hardcode blank since not required.

            #MŒ sjekke at DueDate er i dag eller senere - ellers mŒ den resettes.
            if($InvoiceO->DueDate < $this->transaction->Date) $InvoiceO->DueDate = $this->transaction->Date; #Remittance pay date????
            
            if($InvoiceO->KID) { #KID - denne skal aldri slŒ til - til Œ begynne med.
                #print "**** KID: $InvoiceO->InvoiceNumber<br>\n";
                $InvoiceO->SupplierMessage1     = '';
                $InvoiceO->SupplierMessage2     = '';
                $InvoiceO->SupplierMessage3     = '';

                $InvoiceO->CustomerNumber       = '';
                $InvoiceO->InvoiceNumber        = '';
                $InvoiceO->InvoiceDate          = '';

            } elseif($InvoiceO->InvoiceNumber) { #Strukturert - Fakturanummer er minimum. fakturadato og kundenummer er ekstra
                #print "**** Strukturert: $InvoiceO->InvoiceNumber<br>\n";

                $InvoiceO->SupplierMessage1     = '';
                $InvoiceO->SupplierMessage2     = '';
                $InvoiceO->SupplierMessage3     = '';

                #KID
                $InvoiceO->KID                  = '';

            } else { #Ustrukturert
                #print "**** Ustrukturert: $InvoiceO->InvoiceNumber<br>\n";

                $InvoiceO->SupplierMessage1     = 'Faktura: ' . $InvoiceO->InvoiceNumber;
                $InvoiceO->SupplierMessage2     = 'Kunde: ' . $this->transaction->CustomerName;
                $InvoiceO->SupplierMessage3     = '';            

                #KID
                $InvoiceO->KID                  = '';
                
                #Strukturert
                $InvoiceO->CustomerNumber       = '';
                $InvoiceO->InvoiceNumber        = '';
                $InvoiceO->InvoiceDate          = '';
            }
            
            $file .= $this->format('BETFOR21', $InvoiceO); #Overf¿rsel - til samme leverand¿r

            $this->transaction->RemittanceSequence++;
            $this->transaction->RemittanceSequence  = $this->transaction->RemittanceSequence;
            
            $file .= $this->format('BETFOR23', $InvoiceO); #Faktura - fra leverand¿r
            #Set remitteringsdato, person, batch og sekvens nummer i invoicein id.
            $this->updatestatus();
        }

            $this->transaction->RemittanceSequence++;
            $this->transaction->RemittanceSequence  = $this->transaction->RemittanceSequence;

        $file .= $this->format('BETFOR99', new stdClass());

        return $file;
    }
    
    /***************************************************************************
    * Update the status in the incoming invoice
    * @param None
    * @return status
    */
    function updatestatus() {
        global $_lib;

        $dataH = array();
        $dataH['ID']                            = $this->transaction->ID;
        $dataH['RemittanceSequence']            = $this->transaction->RemittanceSequence;
        $dataH['RemittanceDaySequence']         = $this->transaction->RemittanceDaySequence;
        $dataH['RemittanceSendtDateTime']       = $_lib['sess']->get_session('Datetime');
        $dataH['RemittanceSendtPersonID']       = $_lib['sess']->get_person('PersonID');
        $dataH['RemittanceStatus']              = 'sent';
        
        #Disse mŒ fjernes nŒr vi har en godkjenningsprosess
        $dataH['RemittanceApprovedDateTime']    = $_lib['sess']->get_session('Datetime');
        $dataH['RemittanceApprovedPersonID']    = $_lib['sess']->get_person('PersonID');

        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoicein', 'debug' => false));
    }
    
    /***************************************************************************
    * Convert from database to file format pr line as defined in the top of this object
    * @param type, row
    * @return Line with correct format
    */
    function format($type, $row) {

        $row->RemittanceSequence   = $this->transaction->RemittanceSequence;
        $row->CustomerOrgNumber    = $this->transaction->CustomerOrgNumber;
        $row->NumTransactions      = $this->transaction->NumTransactions;
        $row->TotalAmount          = $this->transaction->TotalAmount;
        $row->Database             = $this->transaction->Database;
        $row->Date                 = $this->transaction->Date;
        $row->VersionSoftware      = $this->transaction->VersionSoftware;

        $this->transaction->RemittanceDaySequence++;    #Starts at zero each day
        $this->transaction->NumRecords++;               #Starts at zero each file
        
        $row->RemittanceDaySequence = $this->transaction->RemittanceDaySequence;
        $row->NumRecords            = $this->transaction->NumRecords;

        #$html = "$type";
        foreach($this->formatH[$type] as $field => $column) {
            if($field != 'name') {
                #print_r($row);
                #print "$field - field: " . $column['field'] . "<br>\n";

                $value  = '';
                if(isset($column['field']) && $row->{$column['field']}) {
                    $value  = $row->{$column['field']};

                } elseif(isset($column['value']) && strlen($column['value'])) {
                    $value  = $column['value'];

                } else {
                    $value  = ' ';
                }
                $length = $column['stop'] - $column['start'] + 1;
                if($column['type'] == 'int' || $column['type'] == 'amount') {
                    $pad    = '0';
                    $align  = STR_PAD_LEFT;
                } elseif($column['type'] == 'text' || $column['type'] == 'date' || $column['type'] == 'bankaccount' || $column['type'] == 'monthdate') {
                    $pad    = ' ';
                    $align  = STR_PAD_RIGHT;
                } else {
                    $pad    = '*'; #Not defined                
                    $align  = STR_PAD_LEFT;
                }
                $value = $this->{$column['type']}($value);
                $value = substr($value, 0, $length); #husk Œ klippe de totale datene til max lenge
                
                $html .= str_pad($value, $length, $pad, $align);
                #print "field: $field: length: $length, value: <b>" . str_pad($value, $length, $pad, $align) . "</b>, pad: $pad, field: " . $column['field'] . ", type: " . $column['type'] . "<br>\n";

            }
        }
        $html .= "\n";
        return $html;
    }

    /***************************************************************************
    * Date converting
    * @param date
    * @return date
    */
    function date($date) {
        #print "date: $date<br>\n";
        $date = trim($date);
        return substr($date, 2, 2) . substr($date, 5, 2) . substr($date, 8, 2);
    }
    
    function bankaccount($bankaccount) {
        $old_pattern = array("/[^0-9]/");
        $new_pattern = array("");

        #Should have been modules checksum on bank account here.
        
        return preg_replace($old_pattern, $new_pattern , trim($bankaccount));
    }

    function amount($amount) {
        #2 last places is desimals. punktum removed
        #print "Amount: $amount<br>\n";

        list($integer, $desimal) = explode('.', trim($amount));
        $desimal = str_pad($desimal, 2, 0, STR_PAD_RIGHT);

        #print "integer: $integer, desimal: $desimal<br>\n";
        
        #Should have been modules checksum on bank account here.        
        $amount = trim($integer.$desimal);
        #print "Amount adjusted: $amount<br>\n";
        return $amount;
    }

    function text($text) {
        $text = preg_replace('/[\r|\n]/m', ' ', $text);
        return trim(strtoupper($text));
    }

    function int($int) {
        return trim($int);
    }
    
    #input: yyyy-mm-dd
    #output: mmdd
    function monthdate($date) {
        #print "monthdate($date)<br>\n";
        return trim(substr($date, 5, 2) . substr($date, 8, 2));
    }

    ################################################################################################
    #CDV-11 modulus control of bankaccount
    function isValidBankAccount($BankAccount) {
	    global $_lib;

        $valid              = true;
		$bankaccountlength  = 11;
        $Product            = 0;
        $Divide             = 0;
        $Int                = 0; 
        $Rest               = 0;

        $weigthH = array(
            0  => 5,
            1  => 4,
            2  => 3,
            3  => 2,
            4  => 7,
            5  => 6,
            6  => 5,
            7  => 4,
            8  => 3,
            9  => 2,
            10 => 1
        );
	     
        ############################################################################################
	    #Remove everything not numbers
        $old_pattern    = array("/[^0-9]/");
        $new_pattern    = array("");
        $BankAccount = preg_replace($old_pattern, $new_pattern, $BankAccount); 

        ############################################################################################
        #Run modulus check
        
        for ($i = 0; $i <= $bankaccountlength; $i++) {
            $Product += ($BankAccount[$i] * $weigthH[$i]);
        }
        
        $Divide = $Product / 11;
        
        #Divide should not contain anything behind . i.e rest
        list($Int, $Rest) = explode('.', $Divide);

        #print "<br>\nBankAccount: $BankAccount: Produkt: $Product, Divide: $Divide, int: $Int, rest: $Rest<br>\n";

        if($Rest != 0) {
            $valid = false;
        }

        return $valid;
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param None
    * @return Current iteration
    */
    function current() {
        return current($this->iteratorH);
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function next() {
       $this->valid = (FALSE !== next($this->iteratorH));
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function key() {
        return key($this->iteratorH);
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function valid() {
        return $this->valid;
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function rewind() {
        $this->valid = (FALSE !== reset($this->iteratorH)); ;
    }
}
?>