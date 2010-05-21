<?
class postmotpost {

    public  $sum;                       # Total sum
    public  $sumaccountH    = array();  # Sum pr konto
    public  $voucherH       = array();  # All open vouchers
    public  $matchH         = array();  # All matches on KID and Invoice

    private $_reference     = "";
    private $_sumIn         = 0;
    private $_sumOut        = 0;
    private $_journalID     = array();
    private $_voucher;
    private $_parent;
    private $_missingStruct = array();

    private $_query = "";
    private $_result;
    private $_parents;
    private $_childs;
    private $_retval;
    private $_counter;

    #args input paramters
    public $AccountPlanID          = 0;
    public $ReskontroFromAccount   = 0;
    public $ReskontroToAccount     = 0;
    public $DepartmentID           = 0;
    public $ProjectID              = 0;
    
    function __construct($args)
    {
        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }
    }

    function __destruct()
    {
        #print_r($this->matchH);
    }

    /***************************************************************************
    * Funkjson som finner ÔøΩpne poster (linjer) med oppgitt KID
    * kid = KID to search for
    * JournalID = The JournalID you are searching from
    */
    function findOpenPostKid($kid, $NotVoucherID) {
        global $_lib;

		#if(!$VoucherID) {
		#	print "Posteringslinjeid mangler<br>\n";
		#}

        $open_voucherH = array();
        $open_journalH = array(); #For counting only
        $open_resultH  = array();
        $status        = 0;

        if($kid > 0) {
            $query_open_voucher  = "select v.JournalID, v.VoucherID, v.AmountIn, v.AmountOut, v.AccountPlanID, v.VoucherDate, v.KID, v.InvoiceID from accountplan as a, voucher as v left join voucherstruct as vs on (vs.ParentVoucherID=v.VoucherID or vs.ChildVoucherID=v.VoucherID) 
            where 
              v.KID='" . $kid . "' and 
              v.VoucherID != '" . $NotVoucherID . "' and 
              vs.ParentVoucherID is null and 
              v.AccountPlanID=a.AccountPlanID and 
              a.EnablePostPost=1 && 
              a.EnableReskontro=0 and 
              v.Active=1 
            order by VoucherStructID asc";
            #print "Åpne poster (derfor is null sjekk): $query_open_voucher<br>\n";
            $open_voucher_result = $_lib['db']->db_query($query_open_voucher);

            while($voucherH = $_lib['db']->db_fetch_assoc($open_voucher_result)) {
                $open_voucherH[$voucherH['JournalID']][$voucherH['VoucherID']] = $voucherH;
                $open_journalH[$voucherH['JournalID']] = true;
            }

            #print "Bilag funnet:<br>\n";
            #print_r($open_journalH);
            #print "Voucher struktur<br>\n";
            #print_r($open_voucherH);
            #print "Count bilag<br>\n";
            #print count($open_journalH);

            if(count($open_journalH) == 0) {
                $_lib['message']->add(array('message' => 'Ingen KID funnet p&aring; noen &aring;pne bilag'));
            } elseif(count($open_journalH) == 1) {
                $_lib['message']->add(array('message' => 'Fant eksakt KID matchKIDH p&aring; bilag'));
                $status = 1;
            } else {
                $_lib['message']->add(array('message' => 'Fant flere matchKIDHende KID p&aring; ulike bilag'));
            }

        } else {
            $_lib['message']->add(array('message' => 'Ingen KID oppgitt'));
        }
        return array($status, $open_voucherH);
    }

    /***************************************************************************
    * Funksjon som finner reskontroen i en open_voucherH, og returnerer Amount + Accountplan som skal fÔøΩres mot KID
    */
    function getKIDInfo($open_voucherH) {
        global $_lib, $accounting;

        $count          = 0;
        $status         = 0;
        $AccountPlanID  = 0;
        $AmountIn       = 0;
        $AmountOut      = 0;

        #print "getKIDInfo open_voucherH<br>\n";
        #print_r($open_voucherH);
        foreach($open_voucherH as $JournalID => $JournalH) {
            #print "getKIDInfo JournalH<br>\n";
            #print_r($JournalH);
            foreach($JournalH as $VoucherID => $VoucherH) {
                #print "getKIDInfo VoucherH<br>\n";
                #print_r($VoucherH);
                $accountplan = $accounting->get_accountplan_object($VoucherH['AccountPlanID']);
                #Finn kundereskontro i billaget
                if($accountplan->AccountPlanType == 'customer' || $accountplan->AccountPlanType == 'supplier') {
                    $count++;
                    $AccountPlanID  = $VoucherH['AccountPlanID'];
                    $AmountIn       = $VoucherH['AmountOut']; #Snu dem
                    $AmountOut      = $VoucherH['AmountIn'];
                }
            }
        }

        if($count == 0) {
            $_lib['message']->add(array('message' => 'Ingen kundereskontro funnet p&aring; den oppgitte KID referansen'));
        }
        elseif($count == 1) {
            $_lib['message']->add(array('message' => 'En kundereskontro funnet p&aring; den oppgitte KID referansen'));
            $status = 1;
        }
        else {
            $_lib['message']->add(array('message' => 'Flere kundereskontro funnet p&aring; den oppgitte KID referansen. Tilfeldig valg ble gjort'));
        }
        return array($AccountPlanID, $AmountIn, $AmountOut, $JournalID, $VoucherID, $status);
    }

    /***************************************************************************
    * Function that generate arrays with open post information
    * @Param AccountPlanID, ReskontroFrom, ReskontroTo
    */
    function getopenpost() {
        global $_lib, $accounting;

        /* clean old totals */
        $this->total['total']->Name        = 'Total';
        $this->total['total']->AmountIn   = 0;
        $this->total['total']->AmountOut  = 0;
        $this->total['total']->FAmountIn  = 0;
        $this->total['total']->FAmountOut = 0;
        $this->voucherH = array();
        $this->matchH = array();
        $this->sumaccountH = array();

        if($this->AccountPlanID){

            $accountplan  = $accounting->get_accountplan_object($this->AccountPlanID);

            $where = '';
            if($this->DepartmentID > 0) {
                $whereextra = ' V.DepartmentID=' . (int) $this->DepartmentID . ' and ';
            }
            if($this->ProjectID > 0) {
                $whereextra .= ' V.ProjectID=' . (int) $this->ProjectID . ' and ';
            }

            if($this->ReskontroFromAccount)
                $where .= " V.AccountPlanID >= '" . $this->ReskontroFromAccount . "' and ";

            if($this->ReskontroToAccount)
                $where .=  " V.AccountPlanID <= " . $this->ReskontroToAccount . " and "; 

            $where .= " A.AccountPlanType='" . $accountplan->ReskontroAccountPlanType . "' and ";

            $query_voucher      = "select V.* from accountplan A, voucher V left join voucherstruct as VS on VS.ParentVoucherID=V.VoucherID or VS.ChildVoucherID=V.VoucherID 
            where V.Active=1 and V.AccountPlanID=A.AccountPlanID and 
            A.EnablePostPost=1
            and $where $whereextra (VS.Closed is NULL or VS.Closed=0) 
            order by V.AccountPlanID asc, V.VoucherPeriod asc, V.VoucherDate asc, V.VoucherID asc";
            #$vouchers          = $_lib['db']->get_hashhash(array('query' => $query_voucher, 'key'=>'VoucherID'));
            #print "$query_voucher<br>\n";

            $result2            = $_lib['db']->db_query($query_voucher);
            #exit;
            $CountOpenVoucher   = $_lib['db']->db_numrows($result2);

            /*******************************************************************
            * Loop and calculate all data
            */
            while($voucher = $_lib['db']->db_fetch_object($result2))
            {
                $AccountPlanID = $voucher->AccountPlanID;

                if($voucher->KID       && !$this->matchH[$AccountPlanID]['KID'][$voucher->KID]) {
                    $this->matchH[$AccountPlanID]['KID'][$voucher->KID] = 0;
                }
                if($voucher->InvoiceID && !$this->matchH[$AccountPlanID]['InvoiceID'][$voucher->InvoiceID]) {
                    $this->matchH[$AccountPlanID]['InvoiceID'][$voucher->InvoiceID] = 0;
                }

                /*******************************************************************
                * Total sum for the loop
                */
                $this->total['total']->Name        = 'Total';
                $this->total['total']->AmountIn   += $voucher->AmountIn;
                $this->total['total']->AmountOut  += $voucher->AmountOut;
                $this->total['total']->FAmountIn  += $voucher->ForeignAmountIn;
                $this->total['total']->FAmountOut += $voucher->ForeignAmountOut;

                /*******************************************************************
                * New sum each time we change the account
                */
                if(strlen($sumaccountH[$AccountPlanID]->Name) == 0) {
                    $accpl=$accounting->get_accountplan_object($voucher->AccountPlanID);
                    $this->sumaccountH[$AccountPlanID]->Name       = $voucher->AccountPlanID." - ".$accpl->AccountName;
                }
                $this->sumaccountH[$AccountPlanID]->AmountIn   += $voucher->AmountIn;
                $this->sumaccountH[$AccountPlanID]->AmountOut  += $voucher->AmountOut;
                $this->sumaccountH[$AccountPlanID]->FAmountIn  += $voucher->ForeignAmountIn;
                $this->sumaccountH[$AccountPlanID]->FAmountOut += $voucher->ForeignAmountOut;

                /*******************************************************************
                * matchKIDH kid references on the same account (always)
                */
                #print "AccountPlanID: $AccountPlanID KID:$voucher->KID AmountIn:$voucher->AmountIn - AmountOut:$voucher->AmountOut<br>";
                #print "Fant " . $this->matchH[$AccountPlanID][KID] . "+ (inn - ut) for seg selv " . ($voucher->AmountIn - $voucher->AmountOut) . "<br>";
                #$calc = ($voucher->AmountIn - $voucher->AmountOut);

                if($voucher->KID) {
                    $this->matchH[$AccountPlanID]['KID'][$voucher->KID]             = round($this->matchH[$AccountPlanID]['KID'][$voucher->KID], 3) + ($voucher->AmountIn - $voucher->AmountOut);
                }
                if($voucher->InvoiceID) {
                    $this->matchH[$AccountPlanID]['InvoiceID'][$voucher->InvoiceID] = round($this->matchH[$AccountPlanID]['InvoiceID'][$voucher->InvoiceID], 3) + ($voucher->AmountIn - $voucher->AmountOut);
                }

                #print $this->matchH[$AccountPlanID]['KID']; print "<br>";

                /*******************************************************************
                * Data from each voucher line
                */
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->AccountPlanID     = $voucher->AccountPlanID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->JournalID         = $voucher->JournalID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->VoucherDate       = $voucher->VoucherDate;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->VoucherPeriod     = $voucher->VoucherPeriod;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->VoucherType       = $voucher->VoucherType;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->AmountIn          = $voucher->AmountIn;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->AmountOut         = $voucher->AmountOut;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ForeignAmountIn   = $voucher->ForeignAmountIn;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ForeignAmountOut  = $voucher->ForeignAmountOut;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->VAT               = $voucher->VAT;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->Quantity          = $voucher->Quantity;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->DepartmentID      = $voucher->DepartmentID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ProjectID         = $voucher->ProjectID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->DueDate           = $voucher->DueDate;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->DescriptionID     = $voucher->DescriptionID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->Description       = $voucher->Description;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->BalanceOk         = $voucher->BalanceOk;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->VoucherID         = $voucher->VoucherID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->KID               = $voucher->KID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->InvoiceID         = $voucher->InvoiceID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ForeignCurrencyID = $voucher->ForeignCurrencyID;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ForeignAmount		= $voucher->ForeignAmount;
                $this->voucherH[$AccountPlanID][$voucher->VoucherID]->ForeignConvRate   = $voucher->ForeignConvRate;
            }

            /***************************************************************
            * Account sum - Dette er det som faktisk er registrert pÔøΩ hovedbokskontoen
            */
            $query_saldo = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout, sum(V.ForeignAmountIn) as fsumin, sum(V.ForeignAmountOut) as fsumout from voucher as V where V.AccountPlanID = '" . $this->AccountPlanID . "' and $whereextra V.Active=1 ";
            #print "XX: $query_saldo<br>\n";
            $saldo = $_lib['storage']->get_row(array('query' => $query_saldo));

            $this->total['account']->Name = $accountplan->AccountPlanID." - ".$accountplan->AccountName;
            $sumSaldo  = $saldo->sumin  - $saldo->sumout;
            $sumFSaldo = $saldo->fsumin - $saldo->fsumout;

            if($sumSaldo > 0) {
                $this->total['account']->AmountIn   = $sumSaldo;
                $this->total['account']->AmountOut  = 0;
            } elseif($sumSaldo < 0) {
                $this->total['account']->AmountOut  = abs($sumSaldo);
                $this->total['account']->AmountIn   = 0;
            }
            if($sumFSaldo > 0) {
                $this->total['account']->FAmountIn  = $sumFSaldo;
            } elseif($sumFSaldo < 0) {
                $this->total['account']->FAmountOut = abs($sumFSaldo);
            }

            foreach($this->sumaccountH as $AccountPlanID => $account) {
                $this->sumaccountH[$AccountPlanID]->Diff    = $account->AmountIn  - $account->AmountOut;
                $this->sumaccountH[$AccountPlanID]->FDiff   = $account->FAmountIn - $account->FAmountOut;
            }

            $this->total['total']->Diff     = $this->total['total']->AmountIn   - $this->total['total']->AmountOut;
            $this->total['total']->FDiff    = $this->total['total']->FAmountIn  - $this->total['total']->FAmountOut;

            /***************************************************************
            * Calculate diff - dette er for ÔøΩ finne om det er en differenase mellom reskontro og hovedbok pÔøΩ sumn
            * Hvis det er en differanse, sÔøΩ er det feil.
            */
            $this->total['diff']->Name       = 'Differanse';
            $this->total['diff']->AmountIn   = $this->total['account']->AmountIn    - $this->total['total']->AmountIn;
            $this->total['diff']->AmountOut  = $this->total['account']->AmountOut   - $this->total['total']->AmountOut;
            $this->total['diff']->FAmountIn  = $this->total['account']->FAmountIn   - $this->total['total']->FAmountIn;
            $this->total['diff']->FAmountOut = $this->total['account']->FAmountOut  - $this->total['total']->FAmountOut;

            $this->total['diff']->Diff       = $this->total['diff']->AmountIn    - $this->total['diff']->AmountOut;
            $this->total['diff']->FDiff      = $this->total['diff']->FAmountIn   - $this->total['diff']->FAmountOut;

            $this->total['account']->Diff    = $this->total['account']->AmountIn    - $this->total['account']->AmountOut;
            $this->total['account']->FDiff   = $this->total['account']->FAmountIn   - $this->total['account']->FAmountOut;

        }
        else
        {
          print "Ingen kontoer er valgt<br>";
        }
        
        #print_r($this->matchH);
    }

    /***************************************************************************
    * Function that returns true if it is a closing match
    * @Param AccountPlanID, KID, InvoiceID
    */
    function isCloseAble($AccountPlanID, $KID, $InvoiceID) {
        $success    = false;
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        #print "KID: $KID, InvoiceID: $InvoiceID<br>\n";
        #print_r($this->matchH[$AccountPlanID]);

        if($KID && !$success) {
            if(isset($this->matchH[$AccountPlanID]['KID'][$KID]) && round($this->matchH[$AccountPlanID]['KID'][$KID], 2) == 0) {
                $success = true;
            }        
        }
        if($InvoiceID && !$success) {
            if(isset($this->matchH[$AccountPlanID]['InvoiceID'][$InvoiceID]) && round($this->matchH[$AccountPlanID]['InvoiceID'][$InvoiceID], 2) == 0) {
                $success = true;
            }
        }

        return $success;
    }

    /***************************************************************************
    * @Function that returns the KID or InvoiceID diff
    * @Param AccountPlanID*, [KID, InvoiceID]. One of KID or InvoiceID should 
    */
    function getDiff($AccountPlanID, $KID, $InvoiceID) {
        $success    = false;
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        if($KID) {
            $value = $this->matchH[$AccountPlanID]['KID'][$KID];
        } elseif($InvoiceID) {
            $value = $this->matchH[$AccountPlanID]['InvoiceID'][$InvoiceID];
        } else {
            $value = 0;
        }

        return $value;
    }

    /***************************************************************************
    * Function that opens closed post lines on spesific line ids
    * @Param VoucherID to be opened
    */
    function openPost($VoucherID) {
        global $_lib;
        
        if($VoucherID > 0) {
            $query_deleteclosed = "delete from voucherstruct where ParentVoucherID=" . $VoucherID . " or ChildVoucherID=" . $VoucherID;
            //print "$query_deleteclosed<br>\n";
            $_lib['db']->db_delete($query_deleteclosed);
        } else {
            #print "openPost linjenummer ikke angitt";
        }
    }

    /***************************************************************************
    * Function that opens closed post lines on a complete Journal
    * @Param JournalID to be opened
    */
    function openPostJournal($JournalID, $VoucherType) {
        global $_lib;

        #Open all closed posts related to invoice. Could be optimized to only close reskontros as pr def
        $query_voucher  = "select VoucherID from voucher where JournalID='" . $JournalID . "' and VoucherType='" . $VoucherType . "' and Active=1"; // mulig mangel av journalid her
        #print "$query_voucher<br>\n";
        $result_voucher = $_lib['db']->db_query($query_voucher);

        while($row = $_lib['db']->db_fetch_object($result_voucher)){
            $this->openPost($row->VoucherID);
        }
    }

    /***************************************************************************
    * Closing posts, only if the post goes to 0
    * Changed logic. Try to close on KID first, then try to close on Invoice ID. Not as now - KID first and then not try InvoiceID
    * @Param AccountPlanID, ReskontroFrom, ReskontroTo
    */
    function closePost($matchAccountPlanID, $KID, $InvoiceID) {
        global $_lib;
        $balance = 999999999999999999999; #Has to be different from zero - since zero is the good condition
        
        #print "closePost: AccountPlanID: $matchAccountPlanID, KID: $KID, InvoiceID: $InvoiceID<br>\n";
        
        #Skulle vi ha joinet slik at vi ikke kan finne en linje med samme kid, hvis kid er gjenbrukt pÔøΩ samme konto? TE 2005-11-11
        #Dvs ikke ta med tidligere lukkede KID/Fakturanummer.
        if($KID && $balance != 0) {
            $query_voucher = "select * from voucher where AccountPlanID='".$matchAccountPlanID."' and KID = '".$KID."' and Active=1 order by VoucherDate desc"; #Order by to always choose the newest if more than one
            list($balance, $AmountInH, $AmountOutH) = $this->closeDataStructure($query_voucher);
        }
        
        if($InvoiceID && $balance != 0) {
            #If balance is zero we do not need to try to close it based on Invoice if the balance is zero already - then we can close it on KID.
            $query_voucher = "select * from voucher where AccountPlanID='".$matchAccountPlanID."' and InvoiceID = '".$InvoiceID."' and Active=1 order by VoucherDate desc"; #Order by to always choose the newest if more than one
            list($balance, $AmountInH, $AmountOutH) = $this->closeDataStructure($query_voucher);
        }

        if(!$KID && !$InvoiceID) {
            $_lib['message']->add('Ingen KID eller fakturanummer');
            return false;
        }

        if($balance == 0 && (count($AmountInH)+count($AmountOutH)) > 1)
        {
            if(count($AmountInH) >= count($AmountOutH))
            {
                #print "AmountInH: " . count($AmountInH) . " >= AmountOutH: " . count($AmountOutH) . "<br />\n";
                #list($ParentVoucher, $value) = each($AmountOutH);
                foreach($AmountOutH as $ParentVoucherID => $value) {
                    foreach($AmountInH as $ChildVoucherID => $in)
                    {
                        $this->closePostSQL($ParentVoucherID, $ChildVoucherID);
                    }
                }
            }
            if(count($AmountInH) < count($AmountOutH))
            {
                #print "AmountInH: " . count($AmountInH) . " < AmountOutH: " . count($AmountOutH) . "<br />\n";
                #list($ParentVoucher, $value) = each($AmountInH);
                foreach($AmountInH as $ParentVoucherID => $value) {
                    foreach($AmountOutH as $ChildVoucherID => $in)
                    {
                        $this->closePostSQL($ParentVoucherID, $ChildVoucherID);
                    }
                }
            }
        }
    }

    private function closeDataStructure($query) {
        global $_lib;
        
        #print "closeDataStructure: $query<br>\n";
        
        $result = $_lib['db']->db_query($query);

        $AmountInH  = array();
        $AmountOutH = array();
        while($voucher = $_lib['db']->db_fetch_object($result)) {
            #print_r($voucher);
            #print "Loop<br>\n";

            if($voucher->AmountIn != 0)
            {
                $AmountInH[$voucher->VoucherID]  += $voucher->AmountIn;
            }
            if($voucher->AmountOut != 0)
            {
                $AmountOutH[$voucher->VoucherID] += $voucher->AmountOut;
            }

            $balance += ($voucher->AmountIn - $voucher->AmountOut);
        }

        #We have to round the amount because of imprecision in php calculations
        $balance = round($balance, 3);
        #print "balance: $balance<br>\n";
    
        return array($balance, $AmountInH, $AmountOutH);
    }

    private function closePostSQL($ParentVoucherID, $ChildVoucherID) {
        global $_lib;
    
        $query  = "select * from voucherstruct where ParentVoucherID='$ParentVoucherID' and ChildVoucherID='$ChildVoucherID'";
        $closed   = $_lib['storage']->get_row(array('query' => $query));

        if(!$closed) {
            $query_ins = "insert into voucherstruct set ParentVoucherID='$ParentVoucherID', ChildVoucherID='$ChildVoucherID', Closed=1";
            #print $query_ins."<br />\n";
            $_lib['db']->db_insert($query_ins);
        } else {
            #$_lib['message']->add("Duplikatlukking: $ParentVoucherID:$ChildVoucherID");
        }
    }

    /**
     * Open all posts on given accountplan.
     */
    public function openAllPostsAccount($AccountPlanID) {
        global $_lib;

        $voucher_query = "select VoucherID from voucher where AccountPlanID=$AccountPlanID";
        $r = $_lib['db']->db_query($voucher_query);

        while($voucher = $_lib['db']->db_fetch_assoc($r)) {
            $id = $voucher['VoucherID'];
            $delete_query = "DELETE FROM voucherstruct WHERE ParentVoucherID = $id OR ChildVoucherID = $id";
            $_lib['db']->db_delete($delete_query);
        }
    }

    /**
     * Close all posts on given accountplan
     */ 
    public function closeAllPostsAccount($AccountPlanID) {
        global $_lib;

        $this->getopenpost();
        $closeableH = array();

        $account = $this->voucherH[$AccountPlanID];
        
        foreach($account as $voucher) {
            if($this->isCloseAble($AccountPlanID, $voucher->KID, $voucher->InvoiceID)) {
                
                #print "Kan lukkes: AccountPlanID: $AccountPlanID<br>\n";
                #print_r($account);
                
                $close = new stdClass();
                $close->matchAccountPlanID  = $voucher->AccountPlanID;
                $close->matchKid            = $voucher->KID;
                $close->matchInvoiceID      = $voucher->InvoiceID;
                $close->AccountPlanID       = $this->AccountPlanID;
                $closeableH[]               = $close;
            }
        }

        $_lib['message']->add("Lukker " . count($closeableH) . " bilag p&aring; $AccountPlanID som g&aring;r i null");
        if(count($closeableH)) {
            foreach($closeableH as $close) {
                $this->closePost($close->matchAccountPlanID, $close->matchKid, $close->matchInvoiceID);
            }
        }
    }
    
    #Close all open posts that has sum on KID to 0
    public function closeAllPosts() {
        global $_lib;

        $this->getopenpost();
    
        $closeableH = array();
    
        if(count($this->voucherH) > 0){
            foreach($this->voucherH as $AccountPlanID => $account) {
                foreach($account as $voucher) {

                    if($this->isCloseAble($AccountPlanID, $voucher->KID, $voucher->InvoiceID)) {
                        
                        #print "Kan lukkes: AccountPlanID: $AccountPlanID<br>\n";
                        #print_r($account);

                        #Denne kan lukkes.
                        $close = new stdClass();
                        $close->matchAccountPlanID  = $voucher->AccountPlanID;
                        $close->matchKid            = $voucher->KID;
                        $close->matchInvoiceID      = $voucher->InvoiceID;
                        $close->AccountPlanID       = $this->AccountPlanID;
                        $closeableH[]               = $close;
				    }
                }
            }
        }

        #print("Lukker " . count($closeableH) . " bilag som g&aring;r i null");
        #print_r($closeableH);
        $_lib['message']->add("Lukker " . count($closeableH) . " bilag som g&aring;r i null");
        if(count($closeableH)) {
            foreach($closeableH as $close) {
                #$_lib['message']->add("Lukker: $close->matchAccountPlanID, KID: $close->matchKID, Fnr: $close->matchInvoiceID");
                #print("Lukker: $close->matchAccountPlanID, KID: $close->matchKid, Fnr: $close->matchInvoiceID<br>\n");
                $this->closePost($close->matchAccountPlanID, $close->matchKid, $close->matchInvoiceID);
            }
        }
        
        #$this->getopenpost(); #read new and fresh data from database. Did not help on the cache.
    }

    /***************************************************************************
    * Opens every post, in all periods all customers 
    * @Param None
    */
    function openAllPosts() {
        global $_lib;

        #Open all closed posts related to invoice. Could be optimized to only close reskontros as pr def
        $sql_openpost  = "delete from voucherstruct";
        #print "$sql_openpost<br>\n";
        return $_lib['db']->db_delete($sql_openpost);
    }

    function findMotKonto($AccountPlanID) {
        global $_lib;

        $q = 
            "SELECT MotkontoResultat1, MotkontoResultat2, MotkontoResultat3, MotkontoBalanse1, MotkontoBalanse2, MotkontoBalanse3 " .
            " FROM accountplan WHERE AccountPlanID = $AccountPlanID";

        $r = $_lib['db']->db_query($q);
        
        return $_lib['db']->db_fetch_assoc($r);
    }
}
?>