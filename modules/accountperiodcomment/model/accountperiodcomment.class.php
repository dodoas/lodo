<?
#Listas all comments for all accounts in all periods in a matrix form
class accountperiodcomment {
    public $PeriodH     = array();
    public $AccountH    = array();
    public $DataH       = array();
    public $AccountExp  = array();
    public $FromPeriod  = "";
    public $ToPeriod  = "";

    function __construct() {
        global $_lib;
        #Get expiry date for account
        $query_accounts_exp = "select AccountID, ValidTo from account where Active=1 AND YEAR(ValidTo) <> 0 order by Sort";
        $this->AccountExp   = $_lib['storage']->get_hash(array('key' => 'AccountID', 'value' => 'ValidTo', 'query' => $query_accounts_exp));

        #X axis
        $query_accounts = "select AccountID, AccountPlanID, concat(AccountPlanID, ' - ' ,AccountNumber, ' - ' , BankName, ' - ', AccountDescription) as AccountNumber from account where Active=1 order by Sort";
        $result_account = $_lib['storage']->db_query( $query_accounts);
        while($row = $_lib['db']->db_fetch_object($result_account)) {
            $this->AccountH[$row->AccountID] = array('AccountPlanID' => $row->AccountPlanID, 'AccountNumber' => $row->AccountNumber );
        }

        #Y Axis
        $query_periods  = "select * from accountperiod where (Status=2 or Status=3) order by Period desc";
        $this->PeriodH  = $_lib['storage']->get_hash(array('key' => 'Period', 'value' => 'Period', 'query' => $query_periods));
        $ArrayAllOpenPeriods = array_keys($this->PeriodH);
        $LastOpenPeriod = end($ArrayAllOpenPeriods);

        $ToPeriodYear = substr($ArrayAllOpenPeriods[0],0,4);
        $this->FromPeriod = strftime('%Y', strtotime("-1 year", strtotime($LastOpenPeriod))) . "-01";
        $this->ToPeriod = $ToPeriodYear . "-12";

        $CurrentPeriod = $this->FromPeriod;
        $this->PeriodH = array();
        while(strtotime($CurrentPeriod) <= strtotime($this->ToPeriod)){
            array_push($this->PeriodH, $CurrentPeriod);
            $CurrentPeriodYear = substr($CurrentPeriod,0,4);
            $CurrentPeriodMonth = substr($CurrentPeriod,5,2);
            if($CurrentPeriodMonth == "12"){
                array_push($this->PeriodH, $CurrentPeriodYear . "-13");
            }
            $CurrentPeriod = date('Y-m', strtotime('next month', strtotime($CurrentPeriod)));
        }
        rsort($this->PeriodH);

        $AccounIDPeriod = array();
        foreach ($this->AccountH as $account_id => $_) {
            foreach ($this->PeriodH as $period) {
                array_push($AccounIDPeriod, array($account_id, $period));
            }
        }

        $select_query = implode(" UNION ", array_map(function($account) {
            return "SELECT $account[0] as AccountID, '$account[1]' as Period";
        }, $AccounIDPeriod));

        $query_create_missing = "INSERT INTO bankvotingperiod(AccountID, Period, Comment)

                                  SELECT AllPeriods.AccountID, AllPeriods.Period, '' as Comment
                                  FROM ($select_query) as AllPeriods
                                  WHERE (AllPeriods.AccountID, AllPeriods.Period) NOT IN
                                  (SELECT AccountID, Period FROM bankvotingperiod WHERE AccountID IS NOT NULL AND Period IS NOT NULL);";
        $_lib['db']->db_query($query_create_missing);

        #Data
        $query_comments = "select BankVotingPeriodID, AccountID, Period, Comment from bankvotingperiod where Period >= '$this->FromPeriod' and Period <= '$ToPeriodYear-13' order by Period, AccountID";
        $result_account = $_lib['db']->db_query($query_comments);

        while($row = $_lib['db']->db_fetch_object($result_account)) {
            $this->DataH[$row->AccountID][$row->Period] = $row;
        }
    }
}

?>
