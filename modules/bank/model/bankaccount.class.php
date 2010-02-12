<?
/***************************************************************************************************
* Empatix functionality
*
* @package empatix_core1_action
* @version  $Id: 
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.empatix.com/ Empatix AS, 1994-2006, post@empatix.com 
*/

class model_bank_bankaccount {

    #input: AccountID, Period
    function __construct($args) {
        global $_lib;

        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }

        #print_r($_lib['input']->request);
    }

    function bancaccountcard($AccountID) {
        global $_lib;

        if($AccountID) {
            $query_card             = "select * from bankaccountcard where Active=1 and AccountID=$AccountID order by Sort";
            $this->bankaccountcardA = $_lib['db']->get_arrayrow(array('query' => $query_card));
        }
    }

    function saldo($AccountID) {
        global $_lib;
        
        $ThisPeriod = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
        $Day 		= substr($_lib['sess']->get_session('LoginFormDate'), 9,10);
    
        $sql_period = "select AmountIn, AmountOut, Period from bankvotingperiod where AccountID=$AccountID and Period <= '$ThisPeriod' and (AmountIn is not null and  AmountOut is not null) order by period desc limit 1";
        #print "$sql_period<br>\n";
        $periodrow  = $_lib['storage']->get_row(array('query' => $sql_period));
        $Period 	= $periodrow->Period;
        if($Period != $ThisPeriod) $Day = 31; #If it is an earlier motnh, ignore days.
        
        $sql_days   = "select sum(AmountIn) as AmIn, sum(AmountOut) as AmOut from accountline where AccountID=$AccountID and Period = '$Period' and Day <= '$Day' and Active=1";
        #print "$sql_days<br>\n";
        $daysrow   	= $_lib['storage']->get_row(array('query' => $sql_days));
    
        return array(($periodrow->AmountIn - $periodrow->AmountOut) + ($daysrow->AmIn - $daysrow->AmOut), $Period);
    }

    function totalsaldo() {
        global $_lib;
    
        $query_accounts = "select AccountID from account where Active=1 and includeinsaldo=1 order by Sort";
        $result_account = $_lib['db']->db_query($query_accounts);

        while($row = $_lib['db']->db_fetch_object($result_account)) {
            list($Amount, $Period) = $this->saldo($row->AccountID);
            $TotalAmount += $Amount;
        }
        return $TotalAmount;
    }

    function cardadd($AccountID) {
        global $_lib;

        $dataH = array();

        $dataH['AccountID']             = $AccountID;
        $dataH['Active']                = 1;
        $dataH['InsertedByPersonID']    = $_lib['sess']->get_person('PersonID');
        $dataH['InsertedDateTime']      = $_lib['sess']->get_session('Datetime');
        $dataH['UpdatedByPersonID']     = $_lib['sess']->get_person('PersonID');

        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'bankaccountcard', 'debug' => true));
    }

    function cardremove($AccountID) {
        global $_lib;

        $dataH = array();

        $dataH['BankAccountCardID']     = $BankAccountCardID;
        $dataH['Active']                = 0;
        
        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'bankaccountcard', 'debug' => true));
    }

    function periodremove() {
        global $_lib;

        $query  = "select * from accountline where Period='$this->Period' and AccountID='$this->AccountID'";
        if($this->debug) print "$query_voucher<br>";
        $result = $_lib['db']->db_query($query);

        #delete all connections
        
        while($row = $_lib['db']->db_fetch_object($result)) {
            $query = "delete from voucheraccountline where AccountLineID='$row->AccountLineID'";
            if($this->debug) print "$query_voucher<br>";
            $_lib['db']->db_delete($query);
        }

        $query = "delete from accountline where Period='$this->Period' and AccountID='$this->AccountID'";
        if($this->debug) print "$query_voucher<br>";
        $_lib['db']->db_delete($query);

        $query = "delete from bankvotingline where VoucherPeriod='$this->Period' and AccountID='$this->AccountID'";
        if($this->debug) print "$query_voucher<br>";
        $_lib['db']->db_delete($query);
    }

    #Lock the period, no further updating is allowed
    function periodlock() {
        global $_lib;

        $query = "update bankvotingperiod set Locked=1 where Period='$this->Period' and AccountID='$this->AccountID'";
        print "query: $query<br>\n";
        $_lib['db']->db_update($query);
    }
}
?>