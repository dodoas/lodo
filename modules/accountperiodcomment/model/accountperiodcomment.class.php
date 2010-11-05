<?
#Listas all comments for all accounts in all periods in a matrix form
class accountperiodcomment {
    public $PeriodH     = array();
    public $AccountH    = array();
    public $DataH       = array();

    function __construct() {
        global $_lib;

        #$ToPeriod   = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));
        $Year       = $_lib['date']->get_this_year($_lib['sess']->get_session('Date'));
        $ToPeriod   = $Year . '-12';
        #$FromPeriod = $Year . '-01';
        $FromPeriod = '2007-01';

        #Data
        $query_comments = "select BankVotingPeriodID, AccountID, Period, Comment from bankvotingperiod where Period >= '$FromPeriod' and Period <= '$ToPeriod' order by Period, AccountID";
        $result_account = $_lib['db']->db_query($query_comments);
        
        while($row = $_lib['db']->db_fetch_object($result_account)) {
            $this->DataH[$row->AccountID][$row->Period] = $row;
        }
    
        #Y Axis
        $query_periods  = "select * from accountperiod where (Status=2 or Status=3) and Period >= '$FromPeriod' and Period <= '$ToPeriod' order by Period desc";
        $this->PeriodH  = $_lib['storage']->get_hash(array('key' => 'Period', 'value' => 'Period', 'query' => $query_periods));
        
        #X axis
        $query_accounts = "select AccountID, concat(AccountPlanID, ' - ' ,AccountNumber, ' - ' , BankName, ' - ', AccountDescription) as AccountNumber from account where Active=1 order by Sort";
        $this->AccountH = $_lib['storage']->get_hash(array('key' => 'AccountID', 'value' => 'AccountNumber', 'query' => $query_accounts));
    }
}

?>
