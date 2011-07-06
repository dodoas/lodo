<?
/***************************************************************************************************
* Empatix functionality
*
* @package empatix_core1_action
* @version  $Id: 
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.empatix.com/ Empatix AS, 1994-2006, post@empatix.com 
*/

includelogic('moneyflow/moneyflow');

class framework_logic_bank {

    public $closeableaccountline = array();

    public $AccountID           = 0;
    public $AccountNumber       = 0;
    public $AccountName         = '';
    public $AccountPlanID       = 0;
    public $PrevPeriod          = '2008-11';
    public $ThisPeriod          = '2008-12';
    public $NextPeriod          = '2009-01';
    public $VoucherType         = 'B';
    public $bankaccountcalc;
    public $prevbankaccountcalc;
    
    private $closeablevouchertilbakeline = array();
    private $closeableaccounttilleggline = array();
    private $closeablevoucheraccountline = array();

    public $voucher_this_hash = array();
    public $bank_this_hash    = array();
    public $voucher_prev_hash = array();
    public $bank_prev_hash    = array();

    public $url         = '';
    public $urlvoucher  = '';

    public $_dsn;
    public $_dbh;
    private $debugKID         = '9999999999999999999999999999999999';
    private $debug      = false;

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    public function __construct($args) {
        global $_lib;
        if($this->debug) print "__construct<br>\n";
        if(isset($args['AccountLineID'])) {
            #Find the correct account and period based on the AccountLineID that is unique.
            $query      = "select AccountID, Period from accountline as al where AccountLineID=" . (int) $args['AccountLineID'];
            if($this->debug) print "$query   ";
            $account    = $_lib['storage']->get_row(array('query' => $query, 'debug' => $this->debug));
            $this->AccountID  = $account->AccountID;
            $this->ThisPeriod     = $account->Period;
        }
        elseif(strlen($args['Period']) == 7) {
            #print "<h1>HER2</h1>";
            $this->ThisPeriod   = $args['Period'];
            $this->AccountID    = $args['AccountID'];

        } else {
            #print "<h1>HER3</h1>";
            $this->ThisPeriod = $_lib['date']->get_prev_period(array('value' => $_lib['sess']->get_session('LoginFormDate'), 'realPeriod' => 1));
            $this->AccountID    = $args['AccountID'];
        }
        
        if(!$this->AccountID || !$this->ThisPeriod) { 
            print "Mangler AccountID eller Period";
            exit;
        }
        
        $this->searchstring = $_lib['convert']->Amount($args['searchstring']);
        $this->side         = $args['side'];
        $this->type         = $args['type'];
        if(!$this->side)
            $this->side = 'AmountOut';
            
        if($this->side == 'AmountOut') 
            $this->oside = 'AmountIn';
        else
            $this->oside = 'AmountOut';
            
        $this->PrevPeriod   = $_lib['date']->get_prev_period(array('value'=>$this->ThisPeriod));
        $this->NextPeriod   = $_lib['date']->get_next_period(array('value'=>$this->ThisPeriod));
        $this->_dbh         = $_dbh;
        $this->_dsn         = $_dsn;
            
        ###############################################################
        #Find config
        $query_bank_head    = "select * from account where AccountID='$this->AccountID'";
        $bank_head          = $_lib['storage']->get_row(array('query' => $query_bank_head));
  
        $query_accountplan  = "select * from accountplan where AccountPlanID=" . (int) $bank_head->AccountPlanID;
        $accountplan        = $_lib['storage']->get_row(array('query' => $query_accountplan));
  
        $this->AccountNumber    = $bank_head->AccountNumber;
        $this->AccountName      = $bank_head->AccountDescription;
        $this->AccountPlanID    = $bank_head->AccountPlanID;
        $this->VoucherType      = $bank_head->VoucherType;
        $this->DebitColor       = $accountplan->DebitColor;
        $this->CreditColor      = $accountplan->CreditColor;
  
        $this->url        = $_lib['sess']->dispatch . 't=bank.edit&amp;AccountID=' . $this->AccountID . '&amp;report_Period=' . $this->ThisPeriod;
        $this->urlvoucher = $_lib['sess']->dispatch . 't=journal.edit&amp;voucher_AccountPlanID=' . $this->AccountPlanID . '&amp;new=1&amp;voucher_Period=' . $this->ThisPeriod . '&amp;';
    }
  
    /***********************************************************************************************
    * Reads all data from database to object
    * @param None
    * @return Loaded object 
    */
    public function init() {
        global $_lib;
        if($this->debug) print "init<br>\n";
        ############################################################################################
        #Zero out this for correct second init of same object after journaling
        #print "Running init<br>\n";
        $this->unvotedaccount               = array();
        $this->sumunvotedaccount            = new stdClass();
        
        $this->unvotedvoucher               = array();
        $this->sumunvotedvoucher            = new stdClass();
        
        $this->voucher_this_hash            = array();
        $this->bank_this_hash               = array();
        $this->voucher_prev_hash            = array();
        $this->bank_prev_hash               = array();
        
        $this->bankvoucher_this_hash        = array();
        $this->bankvote_tillegg             = array();
        $this->bankvote_tilbake             = array();

        $this->bankvotingperiod             = new stdClass();
        $this->sumbankvotingtilbake         = new stdClass();
        $this->sumbankvotingtillegg         = new stdClass();

        ############################################################################################
        $this->voucher->saldo       = $this->getAccountplanSaldo();
    
        $this->bankvoucher_this_hash= $this->_get_bank_voucher_hash($this->AccountPlanID, $this->ThisPeriod);
        $this->bankvote_tillegg     = $this->_get_bank_voting_tillegg($this->ThisPeriod);
        $this->bankvote_tilbake     = $this->_get_bank_voting_tilbake($this->ThisPeriod);

        #Must be before bankvotingperiod
        list($this->prevbankaccountcalc->AmountIn, $this->prevbankaccountcalc->AmountOut, $this->prevbankaccountcalc->AmountSaldo) = $this->getBankAccountSaldo($this->PrevPeriod);

        $this->bankvotingperiod = $this->bankvotingperiod($this->ThisPeriod); #Must be before bankaccount
        $this->bankaccount      = $this->_get_bankaccount($this->ThisPeriod);

        $this->unvoted();

        $tmptopAmountSaldo =   $this->bankvotingperiod->topAmountIn  - $this->bankvotingperiod->topAmountOut;
        $tmptilbakeSaldo   = - $this->sumbankvotingtilbake->AmountIn + $this->sumbankvotingtilbake->AmountOut;
        $tmptilleggSaldo   =   $this->sumbankvotingtillegg->AmountIn - $this->sumbankvotingtillegg->AmountOut;
        $this->bankvotingperiod->topAmountSaldo = $tmptopAmountSaldo + $tmptilbakeSaldo + $tmptilleggSaldo;        
    }

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    #Avstemming mot tilbakef¿ring i perioden
    private function _get_bank_voting_tilbake($Period) {
        global  $_lib;
        if($this->debug) print "_get_bank_voting_tilbake<br>\n";
        $voucher_hash = array();
        $query_voucher          = "select b.* from bankvotingline as b where b.AccountID='$this->AccountID' and b.VoucherPeriod ='$Period' and type='tilbake' order by b.VoucherDate desc, KID desc";
        if($this->debug) print "$query_voucher<br>";
        $result_voucher_hash    = $_lib['db']->db_query($query_voucher);
        while($row_voucher = $_lib['db']->db_fetch_object($result_voucher_hash)){
            $id = $row_voucher->VoucherDate . ':' . $row_voucher->BankVotingLineID;
            #print_r($row_voucher);
            
            $bankvoting_tilbake_hash[$id] = $row_voucher;

            #print "_get_bank_voting_tilbake for topp: ut: " . $this->bankvotingperiod->topAmountOut . ", inn" . $this->bankvotingperiod->topAmountIn . "<br>\n";

            $this->sumbankvotingtilbake->AmountIn    += $row_voucher->AmountIn;
            $this->sumbankvotingtilbake->AmountOut   += $row_voucher->AmountOut;
  
            #print "<b>_get_bank_voting_tilbake : inn: " . $row_voucher->AmountOut . ", ut" . $row_voucher->AmountIn . "</b><br>\n";
            #print "_get_bank_voting_tilbake etter topp: ut: " . $this->bankvotingperiod->topAmountOut . ", inn" . $this->bankvotingperiod->topAmountIn . "<br>\n";
    
            $bankvoting_tilbake_hash[$id]->{'class' . $this->side} = 'number';
            $bankvoting_tilbake_hash[$id]->{'class' . $this->oside} = 'number';

            if($this->type == 'voucher' && round($row_voucher->{$this->side},2) == round($this->searchstring,2) && round($this->searchstring, 2) > 0)
                $bankvoting_tilbake_hash[$id]->{'class' . $this->side} = 'number red';
  
            $this->set_closeable_vouchertilbake(0, $row_voucher->KID, $row_voucher->InvoiceNumber, - $row_voucher->AmountIn + $row_voucher->AmountOut, 'bank voting tilbake');
        }
        
        #print_r($this->closeableaccounttilleggline);
        if(is_array($bankvoting_tilbake_hash))
            ksort($bankvoting_tilbake_hash, SORT_REGULAR);
        #print_r($bankvoting_tilbake_hash['VoucherDate']);
        return $bankvoting_tilbake_hash;
    }
  
    /***********************************************************************************************
    * Avstemming mot tillegsf¿ring i perioden
    * @param Define input parameters
    * @return Define return og function 
    */
    private function _get_bank_voting_tillegg($Period) {
        global  $_lib;
        if($this->debug) print "_get_bank_voting_tillegg<br>\n";
        $bankvoting_tillegg_hash = array();
        $query_voucher          = "select b.* from bankvotingline as b where b.AccountID='$this->AccountID' and b.VoucherPeriod ='$Period' and type='tillegg' order by b.VoucherDate desc, KID desc";
        if($this->debug) print "$query_voucher<br>";
        $result_voucher_hash    = $_lib['db']->db_query($query_voucher);

        while($row_voucher = $_lib['db']->db_fetch_object($result_voucher_hash)){
            $id = $row_voucher->VoucherDate . ':' . $row_voucher->BankVotingLineID;
    
            $bankvoting_tillegg_hash[$id] = $row_voucher;

            #print "_get_bank_voting_tillegg for topp: ut: " . $this->bankvotingperiod->topAmountOut . ", inn" . $this->bankvotingperiod->topAmountIn . "<br>\n";

            $this->sumbankvotingtillegg->AmountIn  += $row_voucher->AmountIn;
            $this->sumbankvotingtillegg->AmountOut += $row_voucher->AmountOut;

            #print "<b>_get_bank_voting_tillegg : ut: " . $row_voucher->AmountOut . ", inn" . $row_voucher->AmountIn . "</b><br>\n";
            #print "_get_bank_voting_tillegg etter topp: ut: " . $this->bankvotingperiod->topAmountOut . ", inn" . $this->bankvotingperiod->topAmountIn . "<br>\n";

            $bankvoting_tillegg_hash[$id]->{'class' . $this->side} = 'number';
            $bankvoting_tillegg_hash[$id]->{'class' . $this->oside} = 'number';

            if($this->type == 'bank' && round($row_voucher->{$this->side},2) == round($this->searchstring,2) && round($this->searchstring, 2) > 0)
                $bankvoting_tillegg_hash[$id]->{'class' . $this->side} = 'number red';

            if($row_voucher->KID)
                $this->set_closeable_accounttillegg(0, $row_voucher->KID, $row_voucher->InvoiceNumber, $row_voucher->AmountIn - $row_voucher->AmountOut, 'bank voting tillegg');
        }
        #print_r($bankvoting_tillegg_hash);
        ksort($bankvoting_tillegg_hash, SORT_REGULAR);
        #print_r($bankvoting_tillegg_hash);
        return $bankvoting_tillegg_hash;
    }

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function getAccountplanSaldo() {
        global $_lib;
        if($this->debug) print "getAccountplanSaldo<br>\n";
        $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout, sum(Quantity) as quantity from voucher where AccountPlanID=" . $this->AccountPlanID . " and VoucherPeriod < '" . $this->ThisPeriod . "' and Active=1";
        if($this->debug) print "$query_voucher<br>\n";
        $row = $_lib['storage']->get_row(array('query' => $query_voucher));
        return $row->sumin - $row->sumout;
    }

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function getBankAccountSaldo($Period) {
        global $_lib;
        if($this->debug) print "getBankAccountSaldo<br>\n";
        $bankvotingperiod = $this->bankvotingperiod($Period); #Must be before bankaccount

        $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from accountline where AccountID=" . $this->AccountID . " and Period = '" . $Period . "' and Active=1";
        if($this->debug) print "$query_voucher<br>\n";
        $row = $_lib['storage']->get_row(array('query' => $query_voucher));
        
        $row->sumin  += $bankvotingperiod->AmountIn;
        $row->sumout += $bankvotingperiod->AmountOut;
        
        return array($row->sumin, $row->sumout, $row->sumin - $row->sumout);
    }

    /***********************************************************************************************
    * Kontoutskrift
    * @param Define input parameters
    * @return Define return og function 
    */
    private function _get_bankaccount($Period) {
        global  $_lib;
        if($this->debug) print "_get_bankaccount<br>\n";
        
        $this->bankaccountcalc->AmountOut += $this->bankvotingperiod->AmountOut;
        $this->bankaccountcalc->AmountIn  += $this->bankvotingperiod->AmountIn;
        
        $bankvoting_tillegg_hash = array();
        $query_bank     = "select a.* from accountline as a, voucheraccountline as vl where a.AccountID=$this->AccountID and a.Period='$Period' and a.AccountLineID=vl.AccountLineID and a.Active=1 order by a.Priority asc, a.Day";
        if($this->debug) print "$query_bank<br>\n";
        $result_bank    = $_lib['db']->db_query($query_bank);

        while($row = $_lib['db']->db_fetch_object($result_bank)) {
        
            $row->VoucherDate = $row->BookKeepingDate;

            $this->bankaccountcalc->AmountIn  += $row->AmountIn;
            $this->bankaccountcalc->AmountOut += $row->AmountOut;

            $row->{'class' . $this->side}  = 'number';
            $row->{'class' . $this->oside} = 'number';

            if($this->type == 'bank' && round($row->{$this->side},2) == round($this->searchstring,2) && round($this->searchstring, 2) > 0)
                $row->{'class' . $this->side} = 'number red';

            $this->set_closeable_accounttillegg($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber, -$row->AmountIn + $row->AmountOut, "fra konto linje nummer: $row->Priority");
            $this->set_closeable_voucheraccount($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber, -$row->AmountIn + $row->AmountOut, "fra konto linje nummer: $row->Priority");
            
            $bankaccount_hash[] = $row;
        }

        $this->bankaccountcalc->AmountSaldo = $this->bankaccountcalc->AmountIn - $this->bankaccountcalc->AmountOut;
        #print $this->bankaccountcalc->AmountSaldo . " = " . $this->bankaccountcalc->AmountIn . " - " . $this->bankaccountcalc->AmountOut . "<br>\n";

        return $bankaccount_hash;
    }
    
    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function bankvotingperiod($Period) {
        global $_lib;
        if($this->debug) print "bankvotingperiod<br>\n";
        $query_bankvotingperiod     = "select * from bankvotingperiod where AccountID='$this->AccountID' and Period ='$Period'";
        #print "$query_bankvotingperiod<br>";
        $bankvotingperiod_hash      = $_lib['db']->db_query($query_bankvotingperiod);

        $bankvotingperiod = $_lib['db']->db_fetch_object($bankvotingperiod_hash);

        if(!$bankvotingperiod) {
            $bankvotingperiod->AccountID            = $postmain['bankvotingperiod_AccountID']         = $this->AccountID;
            $bankvotingperiod->Period               = $postmain['bankvotingperiod_Period']            = $Period;
            $bankvotingperiod->InsertedDateTime     = $postmain['bankvotingperiod_InsertedDateTime']  = 'NOW()';
            $bankvotingperiod->InsertedByPersonID   = $postmain['bankvotingperiod_InsertedByPersonID']= $_lib['sess']->get_person('PersonID');

            #The previous periods end saldo is the next periods default start saldo.
            if($this->prevbankaccountcalc->AmountSaldo > 0) {
                $bankvotingperiod->AmountIn         = $postmain['bankvotingperiod_AmountIn']          = abs($this->prevbankaccountcalc->AmountSaldo);
            } else {
                $bankvotingperiod->AmountOut        = $postmain['bankvotingperiod_AmountOut']         = abs($this->prevbankaccountcalc->AmountSaldo);
            }

            $bankvotingperiod->BankVotingPeriodID   = $_lib['db']->db_new_hash($postmain, 'bankvotingperiod');            
        }
        
        #Denne er snudd
        $bankvotingperiod->AmountSaldo    = $bankvotingperiod->AmountIn - $bankvotingperiod->AmountOut;
        
        $bankvotingperiod->topAmountOut  += $bankvotingperiod->AmountOut;
        $bankvotingperiod->topAmountIn   += $bankvotingperiod->AmountIn;
        #print_r($bankvotingperiod);
        
        return $bankvotingperiod;
    }

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function is_closeable_voucheraccount($AccountPlanID, $KID, $InvoiceID){
        $success    = false;
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        #print_r($this->closeablevoucheraccountline);

        if($KID && !$success) {
            if(isset($this->closeablevoucheraccountline['KID'][$KID]) && round($this->closeablevoucheraccountline['KID'][$KID], 2) == 0) {
                $success = true;
            }
        }
        if($InvoiceID && !$success) {
            if(isset($this->closeablevoucheraccountline['InvoiceID'][$InvoiceID]) && round($this->closeablevoucheraccountline['InvoiceID'][$InvoiceID], 2) == 0) {
                $success = true;
            }
        }

        return $success;
    }

    private function set_closeable_voucheraccount($AccountPlanID, $KID, $InvoiceID, $amount, $comment){
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        #print "Setter voucheraccount: Konto: $AccountPlanID, KID:$KID, Fnr: $InvoiceID, Belop:$amount, Kommentar: $comment<br>\n";

        if($KID) {
            $this->closeablevoucheraccountline['KID'][$KID]             += round($amount,2);
        }
        if($InvoiceID) {
            $this->closeablevoucheraccountline['InvoiceID'][$InvoiceID] += round($amount,2);
        }

        if($KID == $this->debugKID) {
            print "set: voucheraccount #$AccountPlanID#$KID#$InvoiceID# += $amount - saldo #" . $this->closeablevoucheraccountline['KID'][$KID] . "# - $comment<br>\n";
            #print_r($this->closeablevoucheraccountline['KID'][$KID]);
        }
    }

    private function get_voucheraccount($AccountPlanID, $KID, $InvoiceID) {

        if($KID) {
            $value = $this->closeablevoucheraccountline['KID'][$KID];
        } elseif($InvoiceID) {
            $value = $this->closeablevoucheraccountline['InvoiceID'][$InvoiceID];
        } else {
            $value = 0;
        }

        return $value;
    }
    
    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function is_closeable_accounttillegg($AccountPlanID, $KID, $InvoiceID){
        $success    = false;
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        if($KID && !$success) {
            if(isset($this->closeableaccounttilleggline['KID'][$KID]) && round($this->closeableaccounttilleggline['KID'][$KID], 2) == 0) {
                $success = true;
            }        
        }
        if($InvoiceID && !$success) {
            if(isset($this->closeableaccounttilleggline['InvoiceID'][$InvoiceID]) && round($this->closeableaccounttilleggline['InvoiceID'][$InvoiceID], 2) == 0) {
                $success = true;
            }
        }

        return $success;
    }

    private function set_closeable_accounttillegg($AccountPlanID, $KID, $InvoiceID, $amount, $comment){
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        if($KID) {
            $this->closeableaccounttilleggline['KID'][$KID]             += round($amount, 2);
        }
        if($InvoiceID) {
            $this->closeableaccounttilleggline['InvoiceID'][$InvoiceID] += round($amount, 2);
        }

        if($KID == $this->debugKID)
            print "set: accounttillegg #$AccountPlanID#$KID#$InvoiceID# += $amount - saldo #" . $this->closeableaccounttilleggline['KID'][$KID] . "# - $comment<br>\n";
    }

    private function get_accounttillegg($AccountPlanID, $KID, $InvoiceID) {

        if($KID) {
            $value = $this->closeableaccounttilleggline['KID'][$KID];
        } elseif($InvoiceID) {
            $value = $this->closeableaccounttilleggline['InvoiceID'][$InvoiceID];
        } else {
            $value = 0;
        }

        return $value;
    }

    /***********************************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function 
    */
    private function is_closeable_vouchertilbake($AccountPlanID, $KID, $InvoiceID){
        $success    = false;
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        if($KID && !$success) {
            if(isset($this->closeablevouchertilbakeline['KID'][$KID]) && round($this->closeablevouchertilbakeline['KID'][$KID], 2) == 0) {
                $success = true;
            }
        }
        if($InvoiceID && !$success) {
            if(isset($this->closeablevouchertilbakeline['InvoiceID'][$InvoiceID]) && round($this->closeablevouchertilbakeline['InvoiceID'][$InvoiceID], 2) == 0) {
                $success = true;
            }
        }
        return $success;
    }

    private function set_closeable_vouchertilbake($AccountPlanID, $KID, $InvoiceID, $amount, $comment){
        $KID        = trim($KID);
        $InvoiceID  = trim($InvoiceID);

        if($KID) {
            $this->closeablevouchertilbakeline['KID'][$KID]             += round($amount, 2);
        }
        if($InvoiceID) {
            $this->closeablevouchertilbakeline['InvoiceID'][$InvoiceID] += round($amount, 2);
        }
        if($KID == $this->debugKID)
            print "set: vouchertilbake #$AccountPlanID#$KID#$InvoiceID# += $amount - saldo #" . $this->closeablevouchertilbakeline['KID'][$KID] . "# - $comment<br>\n";
    }

    private function get_vouchertilbake($AccountPlanID, $KID, $InvoiceID) {

        if($KID) {
            $value = $this->closeablevouchertilbakeline['KID'][$KID];
        } elseif($InvoiceID) {
            $value = $this->closeablevouchertilbakeline['InvoiceID'][$InvoiceID];
        } else {
            $value = 0;
        }

        return $value;
    }

    /***********************************************************************************************
    * Super funksjon som ser pŒ alle kombinasjoner av lukninger
    * @param 
    * @return
    */
    public function is_closeable($AccountPlanID, $KID, $InvoiceID) {
        $status = false;
        if($this->debug) print "is_closeable<br>\n";
        if($this->is_closeable_accounttillegg($AccountPlanID, $KID, $InvoiceID)) {
            $status = true;
            $comment = "closeable_accounttillegg";
        }
        if($this->is_closeable_voucheraccount($AccountPlanID, $KID, $InvoiceID)) {
            $status = true;
            $comment = "closeable_voucheraccount";
        }
        if($this->is_closeable_vouchertilbake($AccountPlanID, $KID, $InvoiceID)) {
            $status  = true;
            $comment = "closeable_vouchertilbake";
        }
            
        #print "$comment: $AccountPlanID, KID: $KID, InvoiceID: $InvoiceID";
        return $status;
    }

    public function getDiff($AccountPlanID, $KID, $InvoiceID) {
        $value = 0;

        $value = $this->get_accounttillegg($AccountPlanID, $KID, $InvoiceID);
        if(!$value) {
            $value = $this->get_voucheraccount($AccountPlanID, $KID, $InvoiceID);
        }
        if(!$value) {
            $value = $this->get_vouchertilbake($AccountPlanID, $KID, $InvoiceID);
        }        
        return $value;
    }


    /***********************************************************************************************
    * Alle banktransaksjoner som er f¿rt mot kto 1920 i perioden som er oppgitt
    * @param Define input parameters
    * @return Define return og function 
    */
    private function _get_bank_voucher_hash($AccountPlanID, $Period) {
        global $_lib;

        if($this->debug) print "_get_bank_voucher_hash<br>\n";

        if($this->voucher->saldo > 0)
            $this->voucher->sumAmountIn += $this->voucher->saldo;
        else 
            $this->voucher->sumAmountOut += abs($this->voucher->saldo);

        $voucher_hash = array();
        ###############################################################
        #Find all posts in regnskap not matched before (AccountLineID = 0) for all previous periods - for speed - not very elegant. Better methods?
        #FInd all records in v not existing in vl
        $query_voucher          = "select v.* from voucher as v where v.AccountPlanID='$AccountPlanID' and v.VoucherPeriod ='$Period' and v.Active=1 order by v.JournalID desc, v.VoucherDate desc";
        #print "$query_voucher<br>";
        $result_voucher_hash    = $_lib['db']->db_query($query_voucher);
        while($row_voucher = $_lib['db']->db_fetch_object($result_voucher_hash)){
          
            $row_hash = array();
            $row_hash = $row_voucher;
            $Amount = 0;
            if($row_voucher->AmountIn > 0) {
                $Amount = $row_voucher->AmountIn;
            } else {
                $Amount = -$row_voucher->AmountOut;
            }
            $row_hash->Amount = $Amount;
            $row_hash->Num   += 1;
            $row_hash->Date   = $_lib['format']->Date($row_voucher->VoucherDate);

            $row_hash->{'class' . $this->side}  = 'number';
            $row_hash->{'class' . $this->oside} = 'number';

            $this->voucher->sumAmountOut += $row_voucher->AmountOut;
            $this->voucher->sumAmountIn  += $row_voucher->AmountIn;

            #print "side:" . $row_voucher->{$this->side} . ' x ' . $this->searchstring . "<br>\n";
            if($this->type == 'voucher' && round($row_voucher->{$this->oside},2) == round($this->searchstring,2) && round($this->searchstring, 2) > 0)
                $row_hash->{'class' . $this->oside} = 'number red';
            
            $this->set_closeable_vouchertilbake($row_voucher->AccountPlanID, $row_voucher->KID, $row_voucher->InvoiceID, ($row_voucher->AmountIn - $row_voucher->AmountOut), "fra bilag med JournalID: $row_voucher->JournalID");
            $this->set_closeable_voucheraccount($row_voucher->AccountPlanID, $row_voucher->KID, $row_voucher->InvoiceID, ($row_voucher->AmountIn - $row_voucher->AmountOut), "fra bilag med JournalID: $row_voucher->JournalID");
            #print "JID: $row_voucher->JournalID, Amount: $Amount, ref: $row_voucher->KID, date: $row_voucher->VoucherDate<br>";
            $bankvoucher_hash[] = $row_hash;
        }
        #print_r($voucher_hash);
        
        $this->voucher->sumSaldo = $this->voucher->sumAmountIn - $this->voucher->sumAmountOut;
        
        return $bankvoucher_hash;
    }

    /***********************************************************************************************
    * Make a hash of unmatched accountlines and vouchers
    * @param Define input parameters
    * @return Define return og function 
    */
    private function unvoted() {
        global $_lib;
        if($this->debug) print "unvoted<br>\n";
        $unvoted = array();

        if($this->bankaccountcalc->AmountSaldo > 0)
            $this->unvotedcalc->AmountIn  += $this->bankaccountcalc->AmountSaldo;
        else 
            $this->unvotedcalc->AmountOut += abs($this->bankaccountcalc->AmountSaldo);

        /******************************************************************************************/
        #Ikke matchede bank konto og tillegsf¿ringslinjer
        if(is_array($this->bankvote_tilbake)) {
            foreach($this->bankvote_tilbake as $line) {
                
                #print "KID1: $line->KID, AmountIn: $line->AmountIn AmountOut: $line->AmountOut<br>\n";
                #print_r($line);
            
                if(!$this->is_closeable_voucheraccount(0, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_vouchertilbake(0, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_accounttillegg(0, $line->KID, $line->InvoiceNumber)) {
                    
                    if($line->AmountIn > 0 || $line->AmountOut > 0) {
                        $this->unvotedaccount[] = $line;
                        #print_r($line);

                        $this->sumunvotedaccount->AmountIn  += $line->AmountIn;
                        $this->sumunvotedaccount->AmountOut += $line->AmountOut;
                    }
                }
            }
        }
        
        if(is_array($this->bankaccount)) {
            foreach($this->bankaccount as $line) {

                #print "KID2: $line->KID, AmountIn: $line->AmountIn AmountOut: $line->AmountOut<br>\n";
                #print_r($line);

                if(!$this->is_closeable_voucheraccount($line->ReskontroAccountPlanID, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_vouchertilbake($line->ReskontroAccountPlanID, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_accounttillegg($line->ReskontroAccountPlanID, $line->KID, $line->InvoiceNumber)) {

                    #print "NO KID: $line->KID, InvoiceID: $line->InvoiceID, AmountIn: $line->AmountIn AmountOut: $line->AmountOut<br>\n";
                    #print_r($line);

                    if($line->AmountIn > 0 || $line->AmountOut > 0) {
                                        
                        $this->unvotedaccount[] = $line;
                        #print_r($line);

                        $this->sumunvotedaccount->AmountIn  += $line->AmountIn;
                        $this->sumunvotedaccount->AmountOut += $line->AmountOut;
                    }
                }
            }
        }

        /******************************************************************************************/      
        #Ikke matchede bilag og tilbakef¿ringslinjer
        foreach($this->bankvote_tillegg as $line) {

            if(!$this->is_closeable_voucheraccount(0, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_vouchertilbake(0, $line->KID, $line->InvoiceNumber) && !$this->is_closeable_accounttillegg(0, $line->KID, $line->InvoiceNumber)) {

                if($line->AmountIn > 0 || $line->AmountOut > 0) {

                    $this->unvotedvoucher[] = $line;

                    $this->sumunvotedvoucher->AmountIn  += $line->AmountIn;
                    $this->sumunvotedvoucher->AmountOut += $line->AmountOut;
                }
            }
        }

        if(is_array($this->bankvoucher_this_hash)) {
            foreach($this->bankvoucher_this_hash as $line) {
                #print_r($line);

                if(!$this->is_closeable_voucheraccount($line->AccountPlanID, $line->KID, $line->InvoiceID) && !$this->is_closeable_vouchertilbake($line->AccountPlanID, $line->KID, $line->InvoiceID) && !$this->is_closeable_accounttillegg($line->AccountPlanID, $line->KID, $line->InvoiceID)) {

                    if($this->debugKID == $line->KID) {
                        print "Open Konto: $line->AccountPlanID, KID: $line->KID<br>\n";
                        print "closeablevouchertilbakeline KID: " . $this->closeablevouchertilbakeline['KID'][$line->KID] . "<br>\n";
                        print "closeablevouchertilbakeline: " . $this->closeablevouchertilbakeline['InvoiceID'][$line->InvoiceID] . "<br>\n";

                        print "closeableaccounttilleggline KID: " . $this->closeableaccounttilleggline['KID'][$line->KID] . "<br>\n";
                        print "closeableaccounttilleggline: " . $this->closeableaccounttilleggline['InvoiceID'][$line->InvoiceID] . "<br>\n";

                        print "closeablevoucheraccountline: " . $this->closeablevoucheraccountline['KID'][$line->KID] . "<br>\n";
                        print "closeablevoucheraccountline: " . $this->closeablevoucheraccountline['InvoiceID'][$line->InvoiceID] . "<br>\n";

                    }

                    if($line->AmountIn > 0 || $line->AmountOut > 0) {

                        #$this->unvotedvoucher[$line->VoucherDate.$line->KID.$line->VoucherID] = $line;
                        $this->unvotedvoucher[] = $line;

                        $this->sumunvotedvoucher->AmountIn  += $line->AmountIn;
                        $this->sumunvotedvoucher->AmountOut += $line->AmountOut;
                    }
                }
            }
        }
        
        /******************************************************************************************/
        #Find match to moneyflow and suggest it as a voucher
        $prevprevperiod = $_lib['date']->get_prev_period(array('value' => $this->PrevPeriod . '-01', 'realPeriod' => 1));
        $moneyflow = new moneyflow(array('StartDate' =>  $prevprevperiod . '-01'));
        if(is_array($this->unvotedaccount)) {
            foreach($this->unvotedaccount as $id => $unvotedmatch) {
  
                if(!$unvotedmatch->KID) {
                    $match = $moneyflow->findmatch(array('AmountIn' => $unvotedmatch->AmountIn, 'AmountOut' => $unvotedmatch->AmountOut, 'VoucherDate' => $line->Period .'-'.$line->Day ));
                    if(count($match) == 1) {
                        $this->unvotedaccount[$id]->MatchAccountPlanID   = $match[0]->AccountPlanID;
                        $this->unvotedaccount[$id]->MatchAccountName     = $match[0]->AccountName;
                        $this->unvotedaccount[$id]->MatchKID             = $match[0]->KID;
                        $this->unvotedaccount[$id]->MatchInvoiceID       = $match[0]->InvoiceID;
                        $this->unvotedaccount[$id]->MatchVoucherDate     = $match[0]->VoucherDate;
                        $this->unvotedaccount[$id]->MatchVoucherID       = $match[0]->VoucherID;
                        $this->unvotedaccount[$id]->MatchVoucherType     = $match[0]->VoucherType;
                        $this->unvotedaccount[$id]->MatchJournalID       = $match[0]->JournalID;
                    } elseif(count($match) > 1) {

                        #print "Fant mer enn en match<br>";
                        
                        # Found more than one match, see if we can discriminate on InvoiceNumber
                        //$unvotedmatch->InvoiceNumber

                        $single_match = null;
                        if (!empty($unvotedmatch->InvoiceNumber)) {
                            $match_count = 0;

                            foreach($match as $tmp => $info) {
                                if ($info->InvoiceID == $unvotedmatch->InvoiceNumber) {
                                    $match_count++;
                                    if ($match_count > 1) {
                                        $single_match = null;
                                        break;
                                    } else {
                                        $single_match = $info;
                                    }
                                }
                            }
                        }

                        if ($single_match) {
                            $this->unvotedaccount[$id]->MatchAccountPlanID   = $single_match->AccountPlanID;
                            $this->unvotedaccount[$id]->MatchAccountName     = $single_match->AccountName;
                            $this->unvotedaccount[$id]->MatchInvoiceID       = $single_match->InvoiceID;
                            $this->unvotedaccount[$id]->MatchVoucherDate     = $single_match->VoucherDate;
                            $this->unvotedaccount[$id]->MatchVoucherID       = $single_match->VoucherID;
                            $this->unvotedaccount[$id]->MatchVoucherType     = $single_match->VoucherType;
                            $this->unvotedaccount[$id]->MatchJournalID       = $single_match->JournalID;
                        } else {
                            #print_r($match);
                            $dataH = array();

                            $dataH['Manuell match'] = 'Ingen match';
                            $only_match = null;                        

                            foreach($match as $tmp => $info) {
                                #We have to make a compound key - to return three values.
                                $dataH[$info->KID . '#' . $info->InvoiceID . '#' . $info->AccountPlanID] = $info->InvoiceID . ':' . $info->DueDate . ':' . $info->AccountName . ':' . $info->AccountPlanID;
                                #print "Mange: KID: $info->KID - $info->AccountPlanID:$info->AccountName: dato: $info->VoucherDate, belop: $info->AmountBalance<br>\n";
                            }

                            $this->unvotedaccount[$id]->MatchSelect = $dataH;
                        }
                    }
                }
            }
        } else {
            print "Ingen unvotedaccount<br>\n";
        }

        /******************************************************************************************/      

        $tmpaccountsaldo = -$this->sumunvotedaccount->AmountIn + $this->sumunvotedaccount->AmountOut;
        $tmpvouchersaldo = $this->sumunvotedvoucher->AmountIn - $this->sumunvotedvoucher->AmountOut;

        $this->unvotedcalc->AmountSaldo = $this->bankaccountcalc->AmountSaldo + $tmpaccountsaldo + $tmpvouchersaldo;
        #print "Ferdig bunn saldo: " . $this->bankaccountcalc->AmountSaldo . " + tilbakesaldo: " . $tmpaccountsaldo . " + tilleggsaldo: " . $tmpvouchersaldo . "<br>\n";

        #print "<br>unvotedcalc: <br>";
        #print_r($this->unvotedcalc);
        #print "endunvotedcalc: <br>";
        
        #Sort the hashes accordingly
        if(is_array($this->unvotedvoucher)) ksort($this->unvotedvoucher);
        if(is_array($this->unvotedaccount)) ksort($this->unvotedaccount);
        
        #print_r($this->unvotedvoucher);
        
        #print_r($this->closeablevoucheraccountline);
        
        return true;
    }

    /***********************************************************************************************
    * Finds all occurences of amount, returns the number found and the last occurence id
    * @param Define input parameters
    * @return Define return og function 
    */
    public function find_bank_amount($bank_hash, $amount) {
        if($this->debug) print "find_bank_amount<br>\n";
        $num            = 0;
        $accountline_id = 0;
        
        foreach ($bank_hash as $id => $tmp) {
            #Check if this amount eksists in bank_hash
            if($bank_hash[$id]['Amount'] == $amount) {
              $accountline_id = $id;
              $num++;
              $hits     .= "$id (" . $bank_hash[$id][Date] . "),";
              $journalid  = $bank_hash[$id]['JournalID'];
            }
        }
        return array('id' => $accountline_id, 'num' => $num, 'hits' => $hits, 'JournalID' => $journalid); #$id, $num:
    }

    /***********************************************************************************************
    *   Finds all occurences of amount, returns the number found and the last occurence id
    * @param Define input parameters
    * @return Define return og function 
    */
    public function find_voucher_amount($voucher_hash, $amount) {
        if($this->debug) print "find_voucher_amount<br>\n";
    
        $num        = 0;
        $journalid  = 0;
       
        foreach ($voucher_hash as $id => $tmp) {
            #Check if this amount eksists in bank_hash
            if($voucher_hash[$id]['Amount'] == $amount) {
              $journalid = $id;
              $num++;
              $hits     .= "$id (" . $voucher_hash[$id][Amount] . "),";
            }
        }
        return array('id' => $journalid, 'num' => $num, 'hits' => $hits); #$id, $num:
    }

    /***********************************************************************************************
    * Checks if it exists accountlines for the specified AccountID and Period
    * @param none
    * @return true/false 
    */
    private function CheckIfAccountlineExist() {
        global $_lib;

        if($this->debug) print "CheckIfAccountlineExist<br>\n";

        $query_line    = "select * from accountline where AccountID=$this->AccountID and Period = '$this->ThisPeriod' and Active=1";
        if($this->debug) print "$query_line<br>\n";
        $row          = $_lib['storage']->get_row(array('query' => $query_line));
        if($row) {
            return true;
        } else {
            return false;
        }
    }

    private function getMaxAccountlinePriority() {
        global $_lib;

        if($this->debug) print "getMaxAccountlinePriority<br>\n";

        $select_high_priority_sql  = "select max(Priority) as Priority from accountline where AccountID=" . (int) $this->AccountID . " and Period='" . $this->ThisPeriod . "' and Active=1";
        if($this->debug) print "$select_high_priority_sql<br>\n";
        $maxpriority = $_lib['storage']->get_row(array('query' => $select_high_priority_sql));

        if($maxpriority->Priority) 
            $Priority  = $maxpriority->Priority + 1; 
        else 
            $Priority  = 1;

        return $Priority;
    }
    
    private function getMaxAccountlineJournalID() {
        global $_lib;

        if($this->debug) print "getMaxAccountlineJournalID<br>\n";

        #We find highest JournalID used and increment on it.
        $select_high_journalid_sql  = "select max(JournalID) as JournalID from accountline where AccountID=" . (int) $this->AccountID . " and Period='" . $this->ThisPeriod . "' and Active=1";
        if($this->debug) print "$select_high_journalid_sql<br>\n";
        $maxjournal = $_lib['storage']->get_row(array('query' => $select_high_journalid_sql));

        if($maxjournal->JournalID)
            $JournalID  = $maxjournal->JournalID + 1; 
        else 
            $JournalID  = $_lib['sess']->get_companydef('VoucherBankNumber');

        return $JournalID;
    }

    /***********************************************************************************************
    * Checks if it exists accountlines for the specified AccountID and Period
    * @param none
    * @return true/false 
    */
    private function AddAccountLine($args) {
        global $_lib;

        if($this->debug) print "AddAccountLine<br>\n";
    
        #Default date is last day in month for better sorting when saving before everything is punched
        $LastDate = $_lib['date']->get_last_day_in_month($this->ThisPeriod);

        ############################################################################################
        # Set default values
        $DataH = array();
        $DataH['Period']            = $this->ThisPeriod;
        $DataH['AccountID']         = $this->AccountID;
        $DataH['Active']            = 1;
        $DataH['AccountID']         = $this->AccountID;
        $DataH['InterestDate']      = $LastDate;
        $DataH['BookKeepingDate']   = $LastDate;
        $DataH['Period']            = $this->ThisPeriod;
        $DataH['Day']               = substr($LastDate,8,2);
        $DataH['JournalID']         = $this->getMaxAccountlineJournalID();
        $DataH['Priority']          = $this->getMaxAccountlinePriority();

        $DataH['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
        $DataH['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
        $DataH['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');

        # Overwrite default values
        if(is_array($args)) {
           foreach($args as $key => $value) {
               $DataH[$key] = $value;
           }
        }

        ############################################################################################
        $postvl['AccountLineID'] = $_lib['storage']->store_record(array('table' => 'accountline', 'data' => $DataH, 'debug' => $this->debug));
        $AccountLineID = $_lib['db']->store_record(array('data' => $postvl, 'table' => 'voucheraccountline', 'debug' => $this->debug));
    }

    /***********************************************************************************************
    * Function that adds new accounting lines with the highest journal number increased
    * @param num (of new lines)
    * @return adds accountline records 
    */
    public function AddAccountLines($num) {
        for($i=1; $i<= $num; $i++) {
            $this->AddAccountLine($postmain);
        }
    }

    /***********************************************************************************************
    * Checks if it exists accountlines for the specified AccountID and Period
    * @param none
    * @return true/false 
    */    
    public function ZeroAccountLineJournalID() {
        global $_lib;
        
        $fields['accountline_JournalID']    = 0;
        $primarykey['AccountID']            = $this->AccountID;
        $primarykey['Period']               = $this->ThisPeriod;

        $_lib['storage']->db_update_hash($fields, 'accountline', $primarykey);
    }
    
    public function journal() {
        global $_lib, $accounting;
       
        foreach($this->unvotedaccount as $unvoted) {
        
            if($unvoted->Approved && !$accounting->IsJournalIDInUse($unvoted->JournalID, $this->VoucherType) && ($unvoted->ReskontroAccountPlanID || $unvoted->ResultAccountPlanID) && $unvoted->Day >= 1 && $unvoted->Day <= 31 && $unvoted->JournalID > 0) {

                #print_r($unvoted);

                $VoucherH = array();
                $VoucherH['voucher_JournalID']           = $unvoted->JournalID;
                $VoucherH['voucher_VoucherPeriod']       = $unvoted->Period;
                $VoucherH['voucher_VoucherDate']         = $unvoted->Period . '-' . $unvoted->Day;
                $VoucherH['voucher_EnableAutoBalance']   = 0;
                $VoucherH['voucher_AddedByAutoBalance']  = 0;
                $VoucherH['voucher_VoucherType']         = $this->VoucherType;
                $VoucherH['voucher_AutoKID']             = 1; #Information updated automatically from KID information
                $VoucherH['voucher_AmountIn']            = $unvoted->AmountIn;
                $VoucherH['voucher_AmountOut']           = $unvoted->AmountOut;
                $VoucherH['voucher_Active']              = 1;
                $VoucherH['voucher_Description']         = $unvoted->Description;
                $VoucherH['voucher_AutomaticReason']     = "Automatisk fra bankavstemming linje: " . $unvoted->AccountLineID;
                $VoucherH['voucher_DueDate']             = $VoucherH['voucher_VoucherDate']; #Same as voucher date since this is bank account direct transactions
                $VoucherH['voucher_InvoiceID']           = $unvoted->InvoiceNumber;
                $VoucherH['voucher_KID']                 = $unvoted->KID;
                
                if(!$unvoted->InvoiceNumber && !$unvoted->KID) {
                    #If InvoiceNumber and KID is empty - we put JournalID in InvoiceID field for bankavstemming
                    $VoucherH['voucher_InvoiceID']           = $unvoted->JournalID;
                }

                ####################################################################################
                if($unvoted->ReskontroAccountPlanID && $unvoted->ResultAccountPlanID) {

                    ############
                    #1.st line
                    $VoucherH['voucher_AccountPlanID']       = $this->AccountPlanID;                    
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                    ############
                    #2d line
                    $VoucherH['voucher_AccountPlanID']       = $unvoted->ReskontroAccountPlanID;
                    $VoucherH = $this->SwitchSideAmount($VoucherH);
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                    ############
                    #3d line
                    $VoucherH = $this->SwitchSideAmount($VoucherH);                    
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                    ############
                    #4th line
                    $VoucherH['voucher_AccountPlanID']      = $unvoted->ResultAccountPlanID;
                    $VoucherH['voucher_ProjectID']          = $unvoted->ProjectID;
                    $VoucherH['voucher_DepartmentID']       = $unvoted->DepartmentID;
                    $VoucherH['voucher_Quantity']           = $unvoted->ResultQuantity;
                    $VoucherH['voucher_VAT']                = $unvoted->VAT;

                    $VoucherH = $this->SwitchSideAmount($VoucherH);                    
                    $accounting->insert_voucher_line(array('post'=> $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                ####################################################################################
                } elseif($unvoted->ReskontroAccountPlanID) {

                    ############
                    #1.st line
                    $VoucherH['voucher_AccountPlanID']       = $this->AccountPlanID;
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                    ############
                    #2d line
                    $VoucherH['voucher_AccountPlanID']       = $unvoted->ReskontroAccountPlanID;
                    $VoucherH = $this->SwitchSideAmount($VoucherH);
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));
                
                ####################################################################################
                } elseif($unvoted->ResultAccountPlanID) {

                    ############
                    #1.st line
                    $VoucherH['voucher_AccountPlanID']       = $this->AccountPlanID;
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                    ############
                    #2d line
                    $VoucherH['voucher_AccountPlanID']      = $unvoted->ResultAccountPlanID;
                    $VoucherH['voucher_ProjectID']          = $unvoted->ProjectID;
                    $VoucherH['voucher_DepartmentID']       = $unvoted->DepartmentID;
                    $VoucherH['voucher_Quantity']           = $unvoted->ResultQuantity;
                    $VoucherH['voucher_VAT']                = $unvoted->VAT;

                    $VoucherH = $this->SwitchSideAmount($VoucherH);
                    $accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $this->VoucherType, 'comment' => 'Fra bankavstemming'));

                ####################################################################################                
                } else {
                    print "This should not happen";
                }

                $accounting->get_next_available_journalid(array('JournalID' => $VoucherH['voucher_JournalID'], 'verify' => true, 'update' => true, 'type' => $this->VoucherType, 'from' => 'bankavstemming'));

                #Update the kid back to the accountline
                #We dont really need to update the KID if its a real kid, only of it is journal id
                if(!$unvoted->KID && $VoucherH['voucher_KID']) {
                    $query_update = "update accountline set KID='" . $VoucherH['voucher_KID'] . "' where AccountLineID=" . (int) $unvoted->AccountLineID;
                    $_lib['db']->db_update($query_update);
                }
                if(!$unvoted->InvoiceNumber && $VoucherH['voucher_InvoiceID']) {
                    $query_update = "update accountline set InvoiceNumber='" . $VoucherH['voucher_InvoiceID'] . "' where AccountLineID=" . (int) $unvoted->AccountLineID;
                    $_lib['db']->db_update($query_update);
                }
            }
        }

        #Oppdater balanse og resultat    
        $post = array();
        $post['voucher_VoucherPeriod'] = $this->ThisPeriod;
        $post['voucher_VoucherDate']   = $this->ThisPeriod . '-01';

        $accounting->set_journal_motkonto(array('post' => $post, 'VoucherType' => $this->VoucherType));
    }

    private function SwitchSideAmount($VoucherH) {
        $AmountIn                       = $VoucherH['voucher_AmountIn'];
        $VoucherH['voucher_AmountIn']   = $VoucherH['voucher_AmountOut'];
        $VoucherH['voucher_AmountOut']  = $AmountIn;
        return $VoucherH;
    }
    
    public function __destruct() {
    
        #print "Avslutt<br>\n";
        #print_r($this->closeablevoucheraccountline);
        #print_r($this->closeableaccounttilleggline);
        #print_r($this->closeablevouchertilbakeline);

        #print_r(debug_backtrace());
    }
}
?>
