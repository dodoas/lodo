<?
define("CertFile","returdata/file.pem");
includelogic('bank/bank');
includelogic('postmotpost/postmotpost');

class lodo_ocr_ocr {
    private $server = 'https://enett.bbs.no';

    private $formatH = array(
        'NY000010' => array(
            'name'              => 'startrecordforsendelse',
            'formatkode'        => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'      => array('start' => 2,  'stop' => 3, 'value' => '00', 'type' => 'text'),
            'forsendelsestype'  => array('start' => 4,  'stop' => 5, 'value' => '00', 'type' => 'text'),
            'recordtype'        => array('start' => 6,  'stop' => 7, 'value' => '10', 'type' => 'text'),
            'dataavsender'      => array('start' => 8,  'stop' => 15, 'value' => '', 'type' => 'text'),
            'forsendelsesnummer'=> array('start' => 16, 'stop' => 22, 'value' => '', 'type' => 'text'),
            'datamottaker'      => array('start' => 23, 'stop' => 30, 'value' => '', 'type' => 'text'),
        ),
        'NY090020' => array(
            'name'              => 'startrecordoppdrag',
            'formatkode'        => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'      => array('start' => 2,  'stop' => 3, 'value' => '09', 'type' => 'text'),
            'oppdragstype'      => array('start' => 4,  'stop' => 5, 'value' => '00', 'type' => 'text'),
            'recordtype'        => array('start' => 6,  'stop' => 7, 'value' => '20', 'type' => 'text'),
            'avtaleid'          => array('start' => 8,  'stop' => 16, 'value' => '', 'type' => 'text'),
            'oppdragsnummer'    => array('start' => 17, 'stop' => 23, 'value' => '', 'type' => 'text'),
            'oppdragskonto'     => array('start' => 24, 'stop' => 34, 'value' => '', 'type' => 'account'),
        ),
        'NY09**30' => array(
            'name'              => 'transaksjonsrecord1',
            'formatkode'         => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'       => array('start' => 2,  'stop' => 3, 'value' => '09', 'type' => 'text'),
            'transaksjonstype'   => array('start' => 4,  'stop' => 5, 'value' => ''  , 'type' => 'transactiontype'),
            'recordtype'         => array('start' => 6,  'stop' => 7, 'value' => '30', 'type' => 'text'),
            'transaksjonsnummer' => array('start' => 8,  'stop' => 14, 'value' => '', 'type' => 'int'),
            'oppgjorsdato'       => array('start' => 15, 'stop' => 20, 'value' => '', 'type' => 'date'),
            'sentralid'          => array('start' => 21, 'stop' => 22, 'value' => '', 'type' => 'text'),
            'dagkode'            => array('start' => 23, 'stop' => 24, 'value' => '', 'type' => 'day'),
            'delavregningsnummer'=> array('start' => 25, 'stop' => 25, 'value' => '', 'type' => 'int'),
            'lopenummer'         => array('start' => 26, 'stop' => 30, 'value' => '', 'type' => 'int'),
            'fortegn'            => array('start' => 31, 'stop' => 31, 'value' => '', 'type' => 'sign'),
            'belop'              => array('start' => 32, 'stop' => 48, 'value' => '', 'type' => 'amount'),
            'kid'                => array('start' => 49, 'stop' => 73, 'value' => '', 'type' => 'kid'),
            'kortutsteder'       => array('start' => 74, 'stop' => 75, 'value' => '', 'type' => 'text'),
        ),
        'NY09**31' => array(
            'name'              => 'transaksjonsrecord2',
            'formatkode'         => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'       => array('start' => 2,  'stop' => 3, 'value' => '09', 'type' => 'text'),
            'transaksjonstype'   => array('start' => 4,  'stop' => 5, 'value' => '',   'type' => 'transactiontype'),
            'recordtype'         => array('start' => 6,  'stop' => 7, 'value' => '31', 'type' => 'text'),
            'transaksjonsnummer' => array('start' => 8,  'stop' => 14, 'value' => '' , 'type' => 'int'),
            'blankettnummer'     => array('start' => 15, 'stop' => 24, 'value' => '' , 'type' => 'text'),
            'arkivreferanse'     => array('start' => 25, 'stop' => 33, 'value' => '' , 'type' => 'text'),
            'oppdragsdato'       => array('start' => 41, 'stop' => 46, 'value' => '' , 'type' => 'date'),
            'debetkonto'         => array('start' => 47, 'stop' => 57, 'value' => '' , 'type' => 'account'),
        ),
        'NY090088' => array(
            'name'              => 'stoprecordoppdrag',
            'formatkode'        => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'      => array('start' => 2,  'stop' => 3, 'value' => '09', 'type' => 'text'),
            'oppdragstype'      => array('start' => 4,  'stop' => 5, 'value' => '00', 'type' => 'text'),
            'recordtype'        => array('start' => 6,  'stop' => 7, 'value' => '88', 'type' => 'text'),
            'anttransaksjoner'  => array('start' => 8,  'stop' => 15, 'value' => '' , 'type' => 'int'),
            'antrecords'        => array('start' => 16, 'stop' => 23, 'value' => '' , 'type' => 'int'),
            'sumbelop'          => array('start' => 24, 'stop' => 40, 'value' => '' , 'type' => 'amount'),
            'oppgjorsdato'      => array('start' => 41, 'stop' => 46, 'value' => '' , 'type' => 'date'),
            'forsteoppgjorsdato'=> array('start' => 47, 'stop' => 52, 'value' => '' , 'type' => 'date'),
            'sisteoppgjorsdato' => array('start' => 53, 'stop' => 58, 'value' => '' , 'type' => 'date'),
        ),
        'NY000089' => array(
            'name'              => 'stoprecordforsendelse',
            'formatkode'        => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'      => array('start' => 2,  'stop' => 3, 'value' => '00', 'type' => 'text'),
            'forsendelsestype'  => array('start' => 4,  'stop' => 5, 'value' => '00', 'type' => 'text'),
            'recordtype'        => array('start' => 6,  'stop' => 7, 'value' => '89', 'type' => 'text'),
            'anttransaksjoner'  => array('start' => 8,  'stop' => 15, 'value' => '' , 'type' => 'int'),
            'antrecords'        => array('start' => 16, 'stop' => 23, 'value' => '' , 'type' => 'int'),
            'sumbelop'          => array('start' => 24, 'stop' => 40, 'value' => '' , 'type' => 'amount'),
            'oppgjorsdato'      => array('start' => 41, 'stop' => 46, 'value' => '' , 'type' => 'date'),
        )
    );

    private $transaksjonstypeH = array(
        10 => 'Transaksjon fra giro belastet konto',
        11 => 'Transaksjon fra faste oppdrag',
        12 => 'Transaksjon fra direkte remittering',
        13 => 'Transaksjon fra bedrifts terminal giro',
        14 => 'Transaksjon fra skrange giro',
        15 => 'Transaksjon fra avtale giro',
        16 => 'Transaksjon fra tele giro',
        17 => 'Transaksjon fra giro betalt kontant',
        18 => 'Reversering med KID',
        19 => 'Kj¿p med KID',
        20 => 'Reversering med fritekst',
        21 => 'Kj¿p med fritekst'
    );
    
    function __construct() {
        $this->postmotpost = new postmotpost(array());
    }
    
    ################################################################################################
	private function bbs_retrieve_init()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->server); 
		curl_setopt($ch, CURLOPT_SSLCERT, CertFile);
		curl_setopt($ch, CURLOPT_SSLCERTPASSWD, "getocrkid");
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/kaker");
		curl_exec($ch);
        return $ch;
	}

    ################################################################################################
    public function bbs_retrieve_ocr() {
        $ch = bbs_retrieve_init();
        curl_setopt($ch, CURLOPT_URL, $this->server . "/cehttp/portaler/a2.jsp");
        curl_exec($ch);
        curl_setopt($ch, CURLOPT_URL, $this->server . "/cehttp/overfor/hentocrgiro.jsp");
        $document = explode("\n",curl_exec($ch));
        foreach($document as $html)
        {
            if(strstr($html,"<A HREF"))
            {
                $d = explode("\"",$html);
                curl_setopt($ch, CURLOPT_URL, $this->server . '/' . $d[1]);
                $getdata = curl_exec($ch);
                $ocrdata = explode("\n",$getdata);
                foreach($ocrdata as $ocrline)
                {
                    echo($ocrline."\n");
                }
            }
        }
	}

    ################################################################################################
    public function parse_ocr() {

        $this->success              = true;
        $this->dataH                = array();
        $this->account              = '';
        $this->sumaccount           = 0;

        #everything
        $this->numforsendelserecords= 0;
        $this->sumforsendelserecords= 0;

        #pr account
        $this->numoppdragrecordH    = array();
        $this->sumoppdragrecordH    = array();
        
        includelogic('fixedwidthtoobject/fixedwidthtoobject');
        $fixedwitdthtoobject = new framework_logic_fixedwidthtoobject(array('formatH' => $this->formatH, 'IdentificationStart' => 0, 'IdentificationStop' => 8));
	# Suppress warning text for prettyness -eirhje 23.01.10
        $ocrdata = @explode("\r\n",file_get_contents("/kunder/konsulentvikaren/dev.lodo.no/ocr.txt"));
        foreach($ocrdata as $ocrline)
        {

            $identification = substr($ocrline, 0, 8);
            if($identification) {
                $lineO = $fixedwitdthtoobject->line($ocrline);
                
                ####################################################################################
                #Process logic on the individual lines
                if($lineO->name == 'startrecordoppdrag') {
                    $this->account = $lineO->oppdragskonto; #Sett kontoen for oppdraget
                }

                $this->numforsendelserecords++;
                $this->numoppdragrecordH[$this->account]++;

                #Prosessering og summering
                if($lineO->name == 'transaksjonsrecord1') {
                    $this->dataH[$this->account][$lineO->transaksjonsnummer]  = $lineO;
                    $this->sumforsendelserecords                += $lineO->belop;
                    $this->sumoppdragrecordH[$this->account]    += $lineO->belop;
                }
                if($lineO->name == 'transaksjonsrecord2') {
                    $this->dataH[$this->account][$lineO->transaksjonsnummer]->blankettnummer = $lineO->blankettnummer;
                    $this->dataH[$this->account][$lineO->transaksjonsnummer]->arkivreferanse = $lineO->arkivreferanse;
                    $this->dataH[$this->account][$lineO->transaksjonsnummer]->oppdragsdato   = $lineO->oppdragsdato;
                    $this->dataH[$this->account][$lineO->transaksjonsnummer]->debetkonto     = $lineO->debetkonto;
                }

                if($lineO->name == 'stoprecordoppdrag') {
                    #Sjekk at summer og records stemmer
                    if($lineO->antrecords != $this->numoppdragrecordH[$this->account] || $lineO->sumbelop != $this->sumoppdragrecordH[$this->account] ) {
                        $this->success = false;
                    }
                }

                if($lineO->name == 'stoprecordforsendelse') {
                    #Sjekk at summer og records stemmer
                    #print "sjekk: $lineO->antrecords!= $this->numforsendelserecords\n";
                    if($lineO->antrecords!= $this->numforsendelserecords || $lineO->sumbelop != $this->sumforsendelserecords) {
                        $this->success = false;
                    }
                }
            }
        }
        
        #print_r($this->dataH);
        
        if(!$this->success) {
            print "Ikke suksess#############################################################";
            return false;
        } else {
            #print_r($this->dataH);
            return $this->dataH;
        }
    }

    ################################################################################################
    #Preprocess - update Accountplans and statuses - ready for journaling flag.
    public function preprocess() {
        global $_lib;
        
        $AccountTransactionsO = $this->parse_ocr();
        
        foreach($AccountTransactionsO as $BankAccount => &$TransactionsO) {

            $query                  = "select * from account where AccountNumber='" . $BankAccount . "' and Active=1";
            #print "$query<br>\n";
            $bankaccount            = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));
            #print_r($bankaccount);
            $BankAccountPlanID      = $bankaccount->AccountPlanID;
            $BankAccountID          = $bankaccount->AccountID;

            foreach($TransactionsO as $tmp => $TransactionO) {

                $i++;
                if (!($i % 2)) {
                    $TransactionO->Class = "r1";
                } else {
                    $TransactionO->Class = "r0";
                }
    
                #New variables we add in this process
                $TransactionO->Status                   = '';
                $TransactionO->Journal                  = true;
                $TransactionO->JournalID                = 0;
                $TransactionO->CustomerAccountPlanID    = 0;
                $TransactionO->BankAccountPlanID        = $BankAccountPlanID;
                $TransactionO->BankAccountID            = $BankAccountID;
    
                if($TransactionO->debetkonto) {
                    #Should this be more restricted in time or period to eliminate false searches? Any other method to limit it to oly look in the correct records? No?
                    $query                  = "select * from accountplan where DomesticBankAccount='" . $TransactionO->debetkonto . "'";
                    #print "$query<br>\n";
                    $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));
                    if($account) {
                        $TransactionO->Status .= 'Match p&aring; bankkonto.';

                        $TransactionO->CustomerAccountPlanID   = $account->AccountPlanID;
                        $TransactionO->CustomerAccountPlanName = $account->AccountName;
                    }
                }
                
                ####################################################################################
                #Check if we get a kid match $TransactionO->kid
                #pne poster logikken?
                #What if KID match has another account than bancaccountmatch
                list($tmpstatus, $journalH) = $this->postmotpost->findOpenPostKid($TransactionO->kid, 0);
                if(count($journalH) == 0) {
                    $TransactionO->Status .= 'Ingen KID funnet p&aring; noen &aring;pne bilag. ';
                    $TransactionO->Journal = false;

                } elseif(count($journalH) == 1) {
                    #Ikke helt eksakt da det kan v¾re flere vouchers - men konotplan som er yyerste nivŒ er i alle fall bare en sŒ da tar vi den.
                    $TransactionO->Status .= 'Fant eksakt KID match p&aring; bilag. ';
                    $journalH                   = array_pop($journalH); #Remove accountplan hash level
                    $journalH                   = array_pop($journalH); #remove vouchernumber hash level (could be more than one)
                    $TransactionO->OpenPostH    = $journalH;

                    if($TransactionO->CustomerAccountPlanID) {
                        #We found customer account based on bancaccount earlier - see if this matches
                        if($TransactionO->CustomerAccountPlanID != $journalH['AccountPlanID']) {
                            $TransactionO->Status .= 'AccountPlanID fra bankkontonummer er forskjellig fra KID match. ';                
                            $TransactionO->Journal = false;
                        }
                    } elseif($journalH['AccountPlanID']) {
                        $TransactionO->CustomerAccountPlanID = $journalH['AccountPlanID'];
                    } else {
                        $TransactionO->Status .= 'Klarer ikke &aring; finne en AccountPlanID. ';
                        $TransactionO->Journal = false;
                    }
                    #print_r($journalH);

                    #Everything OK - start to journal
                } else {
                    
                    if($TransactionO->CustomerAccountPlanID) {

                        if(count($journalH[$TransactionO->CustomerAccountPlanID]) == 0) {
                            $TransactionO->Status .= 'Fant ingen KID p&aring; AccountPlanID. ';
                            $TransactionO->Journal = false;

                        } elseif(count($journalH[$TransactionO->CustomerAccountPlanID]) == 1) {
                            $journalH                   = array_pop($journalH[$TransactionO->CustomerAccountPlanID]); #Remove accountplan hash level
                            $TransactionO->OpenPostH    = $journalH;

                        } else {
                            $TransactionO->Status .= 'Fant flere matchende KID p&aring; AccountPlanID. ';
                            $TransactionO->Journal = false;
                        }

                    } else {
                        $TransactionO->Status .= 'Fant flere matchende KID p&aring; ulike bilag - bankkontonummer mŒ settes riktig pŒ kunde. ';
                        $TransactionO->Journal = false;
                    }
                }
    
                if($TransactionO->Journal) {
                    #Sjekk om den gŒr i null.
                    $sum = $_lib['convert']->Amount($TransactionO->OpenPostH['AmountIn'] - $TransactionO->OpenPostH['AmountOut']);
                    print "sum: $sum - " . $TransactionO->belop . "<br>\n";
                    if($TransactionO->belop != $sum) {
                        $TransactionO->Status .= "Innbetaling av annet bel&oslash;p enn fakturert: " . $sum . ". ";
                    } else {
                        $TransactionO->Status .= 'Lukkes automatisk. ';
                    }
                }

                #Check if account from KID match is the same as account from bankaccoutnmatch - if not error message
    
                #$journalH = array_pop($journalH);
                #print_r($journalH);
                    
                if(!$TransactionO->CustomerAccountPlanID) {
                    $TransactionO->Status .= 'Finner ikke kontoplan for bankkonto: ' . $TransactionO->debetkonto;
                    $TransactionO->Journal = false;
                    $TransactionO->Class   = 'red';
                }
                if(!$TransactionO->BankAccountPlanID) {
                    $TransactionO->Status .= 'Finner ikke oppsett i bankavstemming for konto: ' . $BankAccount;
                    $TransactionO->Journal = false;
                    $TransactionO->Class   = 'red';
                }
                
                ####################################################################################
                #Check that we have not journaled the same incoming invoice earlier
                #But this requires that we know AccountPlanID and on closed accounts - it will be BanAccount that is our only mapping.
                #Problem salget - ligger her med samme kontoplan og kid og gir treff - hvordan hindre det - sjekke dato ogsŒ? 
                $query          = "select * from voucher where AccountPlanID='" . $TransactionO->CustomerAccountPlanID . "' and KID='" . $TransactionO->kid . "' and VoucherDate='" . $TransactionO->oppgjorsdato . "' and Active=1";
                print "$query<br>\n";
                $voucherexists  = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));
                if($voucherexists) {
                    $TransactionO->Status      = "Innbetaling er bilagsf&oslash;rt tidligere";
                    $TransactionO->Journal     = false;
                    $TransactionO->JournalID   = $voucherexists->JournalID;
                    $TransactionO->Class       = 'green';
                    $TransactionO->VoucherType = 'B';
                }
                if($TransactionO->Journal) {
                    $TransactionO->Status   .= "Klar til bilagsf&oslash;ring";                
                }                
            }
        }
        return $AccountTransactionsO;
    }

    ################################################################################################
    public function journal() {
        global $_lib;

        print "Bilagsf&oslash;r<br>\n";
        includelogic('accounting/accounting');
        $accounting     = new accounting();

        $AccountTransactionsO = $this->preprocess();

        foreach($AccountTransactionsO as $BankAccount => $TransactionsO) {
            foreach($TransactionsO as $tmp => $TransactionO) {

                if($TransactionO->Journal) {
        
                    print_r($TransactionsO);
        
                    $VoucherType = 'B';
                    list($JournalID, $tmp) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $VoucherType, 'reuse' => false, 'from' => 'ocr'));
        
                    $VoucherH = array();
                    $TransactionO->JournalID                = $JournalID;
                    $VoucherH['voucher_JournalID']          = $JournalID;
                    $VoucherH['voucher_VoucherPeriod']      = substr($TransactionO->oppgjorsdato, 0, 7);
                    $VoucherH['voucher_VoucherDate']        = $TransactionO->oppgjorsdato;
                    $VoucherH['voucher_EnableAutoBalance']  = 0;
                    $VoucherH['voucher_AddedByAutoBalance'] = 0;
                    $VoucherH['voucher_VoucherType']        = $VoucherType;
                    $VoucherH['voucher_AutoKID']            = 1; #Information updated automatically from KID information
        
                    ##
                    if($TransactionO->belop < 0)
                        $VoucherH['voucher_AmountIn']           = abs($TransactionO->belop);
                    else
                        $VoucherH['voucher_AmountOut']          = abs($TransactionO->belop);
                    
                    ##
                    $VoucherH['voucher_Active']             = 1;
                    $VoucherH['voucher_Description']        = $TransactionO->arkivreferanse;
                    $VoucherH['voucher_AutomaticReason']    = "OCR: ";
                    $VoucherH['voucher_KID']                = $TransactionO->kid;

                    $VoucherH['voucher_AccountPlanID']      = $TransactionO->CustomerAccountPlanID;
                    
                    #print_r($VoucherH);
                    print "\n<hr>\n";
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $VoucherType, 'comment' => 'Fra OCR'));

                    $VoucherH['voucher_AccountPlanID']      = $TransactionO->BankAccountPlanID;

                    $VoucherH = $this->SwitchSideAmount($VoucherH);

                    #print_r($VoucherH);
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $VoucherType, 'comment' => 'Fra OCR'));

                    #Close open posts on this KID if it really is closed
                    $this->postmotpost->closePost($TransactionO->CustomerAccountPlanID, $TransactionO->kid);

                    #Insert line in bankavstemming
                    $this->insertBankavstemmingLine($TransactionO);

                    #Update bank account on accountplan if it does not exist.
                    $this->updateBankAccount($TransactionO->CustomerAccountPlanID, $TransactionO->debetkonto);
                }
            }
        }
    }

    ################################################################################################
    #Updates bank account on an accountplan. Should probably not overwrite bankaccount if it exists - but again bank account should be a structure now since it is used for identification
    function updateBankAccount($AccountPlanID, $BankAccount) {
        global $_lib;
        if($AccountPlanID && $BankAccount) {
            $dataH = array();
            $dataH['DomesticBankAccount']   = $BankAccount;
            $dataH['AccountPlanID']         = $AccountPlanID;
            $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'debug' => true));
        }
    }

    ################################################################################################
    #inserts a bankavstemming line as complete as possible
    function insertBankavstemmingLine($TransactionO) {
    
        $dataH = array();
        $dataH['AccountID']             = $TransactionO->BankAccountID;
        $dataH['ArchiveRef']            = $TransactionO->arkivreferanse;
        $dataH['ReskontroAccountPlanID']= $TransactionO->CustomerAccountPlanID;
        $dataH['JournalID']             = $TransactionO->JournalID;
 
        if($TransactionO->belop > 0)
            $dataH['AmountIn']      = abs($TransactionO->belop);
        else
            $dataH['AmountOut']     = abs($TransactionO->belop);

        $dataH['Day']               = substr($TransactionO->oppgjorsdato, 8, 2);
        $dataH['Period']            = substr($TransactionO->oppgjorsdato, 0, 7);
        $dataH['BookKeepingDate']   = $TransactionO->oppgjorsdato;
        $dataH['InterestDate']      = $TransactionO->oppgjorsdato;
        $dataH['Active']            = 1;
        $dataH['Approved']          = 1;

        $dataH['debug']  = true;
        $dataH['KID']    = $TransactionO->kid;
        $dataH['Comment']= 'Automatisk opprettet fra OCR';

        #KId, kommentar, bilagsnummer mŒ sendes med herfra.
        $bank = new framework_logic_bank($dataH);
        $bank->AddAccountLine($dataH);
    }
    
    ################################################################################################
    function SwitchSideAmount($VoucherH) {
        $AmountIn                       = $VoucherH['voucher_AmountIn'];
        $VoucherH['voucher_AmountIn']   = $VoucherH['voucher_AmountOut'];
        $VoucherH['voucher_AmountOut']  = $AmountIn;
        return $VoucherH;
    }

}
?>
