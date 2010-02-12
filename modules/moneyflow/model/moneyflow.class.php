<?
class moneyflow {
    public $sumBalance = 0;
    public $detailH    = array();
    public $dateH      = array();
    public $saldoH     = array();
    public $amountH    = array();
    public $StartDate  = '2007-02-01';
    public $StartAmountIn    = 0;
    public $StartAmountOut   = 0;
    public $StartAmountBalance  = 0;
    public $result_saldo     = 0;
    public $result_account   = 0;

    function __construct($args) {
        global $_lib;
        
        foreach($args as $key => $value) {
            $this->{$key} = $value;
            #print "KEYX: $key = $value<br>\n";
        }
    
        if(!$this->StartDate)
            $this->StartDate = $_lib['sess']->get_session('LoginFormDate');
        
        $this->year          = $_lib['date']->get_this_year($this->StartDate);
        
        $expected_query = "select voucher.*, accountplan.AccountName, accountplan.OrgNumber from voucher, accountplan where voucher.AccountPlanID=accountplan.AccountPlanID and accountplan.EnableMoneyFlow=1 and voucher.DueDate > '" . $this->StartDate . "' and accountplan.EnableReskontro != 1 and (VoucherType='U' or VoucherType='S' or VoucherType='L') and voucher.Active=1 order by voucher.DueDate asc ";
        #print "expected: $expected_query<br>\n";
        $result = $_lib['db']->db_query($expected_query);
        #VoucherType = U og S
        
        $saldo_query = "select voucher.AccountPlanID, accountplan.AccountName, sum(voucher.AmountIn) as sumin, sum(voucher.AmountOut) as sumout from voucher, accountplan where voucher.AccountPlanID=accountplan.AccountPlanID and accountplan.EnableSaldo=1 and voucher.VoucherDate <= '" . $this->StartDate . "' and voucher.Active=1  group by voucher.AccountPlanID"; #Where start saldo er satt.
        #print "saldo: $saldo_query<br>\n";
        $this->result_saldo = $_lib['db']->db_query($saldo_query);
        
        $expected_query_accounts = "select voucher.AccountPlanID, accountplan.AccountName from voucher, accountplan where voucher.AccountPlanID=accountplan.AccountPlanID and accountplan.EnableMoneyFlow=1 group by voucher.AccountPlanID";
        #print "accounts: $expected_query_accounts<br>\n";
        $this->result_account = $_lib['db']->db_query($expected_query_accounts);    

        ############################################################################################
        #Start saldo
        $saldo_query     = "select a.AccountPlanID, a.AccountName, sum(v.AmountIn) as AmountIn, sum(v.AmountOut) as AmountOut from voucher as v, accountplan as a where a.EnableSaldo=1 and v.AccountplanID=a.AccountplanID and v.VoucherDate >= '" . $this->year . "" . "-01-01' and v.VoucherDate <= '" . $this->StartDate . "' and v.Active=1 group by v.AccountPlanID";
        #print "<b>$saldo_query</b><br>\n";
        $result_saldo = $_lib['db']->db_query($saldo_query);
        while($saldo = $_lib['db']->db_fetch_object($result_saldo)) {
            $saldo->AmountBalance = $saldo->AmountIn - $saldo->AmountOut;
            $this->AmountStartBalance += $saldo->AmountBalance;
            $this->saldoH[] = $saldo;
        }
        #print_r($this->saldoH);
        
        ############################################################################################
        #$startsaldo     = $_lib['storage']->get_row(array('query' => $sql_result));
        if($this->AmountStartBalance > 0) 
            $this->StartAmountIn    = $this->AmountStartBalance;
        else 
            $this->StartAmountOut   = abs($this->AmountStartBalance);

        $startsaldo->AmountIn    =  $this->StartAmountIn;
        $startsaldo->AmountOut   =  $this->StartAmountOut;
        $startsaldo->AccountName =  'Start saldo';

        #kalkuler
        $this->StartAmountBalance = $this->StartAmountIn - $this->StartAmountOut;
        $startsaldo->sumBalance = $this->sumBalance = $this->StartAmountBalance;

        $this->dateH[$this->StartDate][] = $startsaldo;
        $this->detailH[$this->StartDate][] = $startsaldo;

        #Gruppering pŒ datoer og summering    
        while($expected = $_lib['db']->db_fetch_object($result)) {
            $expected->AmountBalance = $expected->AmountIn - $expected->AmountOut;
            $this->sumBalance       += $expected->AmountBalance;
            $expected->sumBalance    = $this->sumBalance;

            if($expected->sumBalance >= 0)
                $expected->color = "blue";
            else
                $expected->color = "red";

            $this->detailH[$expected->DueDate][] = $expected;
            $this->dateH[$expected->DueDate]->AmountIn    += $expected->AmountIn;
            $this->dateH[$expected->DueDate]->AmountOut   += $expected->AmountOut;
            $this->dateH[$expected->DueDate]->AmountSaldo += $this->dateH[$expected->DueDate]->AmountIn - $this->dateH[$expected->DueDate]->AmountOut;                

            $this->amountH[$expected->AmountBalance][] = $expected;
        }
    }
    
    #Return value changed to return one ore more matches. All recieving functions has to be changed accordingly.
    function findmatch($args) {
        $args['VoucherDate'] = substr($args['VoucherDate'], 0, 10);
    
        $balance = $args['AmountIn'] - $args['AmountOut'];

        #if($balance == 5) {
        #    ksort($this->amountH);
        #    print "find: $balance<br>\n";
        #    print_r($this->amountH);
        #}

        if(isset($this->amountH[$balance])) {
            return $this->amountH[$balance];
        } else {
            return 0;
        }
    }
}
?>