<?
class framework_logic_regnskapsrapport {
    public $lineH       = array();
    public $lineSumH    = array();
    public $info        = array(); #Manually inputted information
    public $db_table    = 'shortreport';

    function __construct($args) {
        global $_lib;

        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }
        
        if(!$this->Period) {
            $this->Period = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
        }
        
        $this->misc();
        $this->data($this->LineID, $this->Period);
    }
   

    function misc() {
        global $_lib;
        
        if(strlen($this->LineID) > 0) {
            $sql_update = "update shortreport set RememberLineChoice=" . (int) $this->LineID;
            $_lib['db']->db_query($sql_update);
        }
        
        $check_query = "select Period from accountperiod where Period='".$this->Period."'";
        $reportResult = $_lib['db']->db_query($check_query);
        if($_lib['db']->db_numrows($reportResult) == 0)
        {
            $query = "select Period from accountperiod order by Period desc";
            $row = $_lib['storage']->get_row(array('query' => $query));
            $this->Period = $row->Period;
        }

        $get_accountreport = "select * from $this->db_table where Period='$this->Period'";
        $reportResult = $_lib['db']->db_query($get_accountreport);

        if($_lib['db']->db_numrows($reportResult) == 0)
        {
            $query = "insert into $this->db_table (Period) values ('".$this->Period."')";
            $tmp = $_lib['db']->db_query($query);
        }
        
        $this->info = $_lib['storage']->get_row(array('query' => $get_accountreport));
        $this->LineID = $this->info->RememberLineChoice;
    }
    
    
    function data($LineID, $Period) {
        global $_lib;

        $this->thisStartPeriod  = substr($Period,0,4)."-01";
        $this->thisEndPeriod    = $Period;
        $this->thisYear         = $_lib['date']->get_this_year($this->thisStartPeriod);

        $this->prevStartPeriod  = $_lib['date']->get_this_period_last_year($this->thisStartPeriod);
        $this->prevEndPeriod    = $_lib['date']->get_this_period_last_year($this->thisEndPeriod);
        $this->prevYear         = $_lib['date']->get_this_year($this->prevStartPeriod);

        if(strlen($this->DepartmentID) > 0) {
            $where = ' V.DepartmentID=' . $this->DepartmentID . ' and ';
        }
        if(strlen($this->ProjectID) > 0) {
            $where .= ' V.ProjectID=' . (int) $this->ProjectID . ' and ';
        }

        if($LineID) {
            
            $queryThisYear = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$this->thisEndPeriod."' and V.VoucherPeriod>='".$this->thisStartPeriod."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='" . $LineID . "' group by A.ReportShort";
            #print "$queryThisYear";
            $compareThisYear        = $_lib['storage']->get_row(array('query' => $queryThisYear));
            $ThisYearSum            = $compareThisYear->sumin - $compareThisYear->sumout;

            $queryLastYear          = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$this->prevEndPeriod."' and V.VoucherPeriod>='".$this->prevStartPeriod."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='" . $LineID . "' group by A.ReportShort";
            $compareLastYear        = $_lib['storage']->get_row(array('query' => $queryLastYear));
            $LastYearSum            = $compareLastYear->sumin - $compareLastYear->sumout;

            $queryWholeLastYear     = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where substring(V.VoucherPeriod,1,4)='".(substr($this->thisStartPeriod,0,4)-1)."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='" . $LineID . "' group by A.ReportShort";
            $compareWholeLastYear   = $_lib['storage']->get_row(array('query' => $queryWholeLastYear));
            $WholeLastYearSum       = $compareWholeLastYear->sumin - $compareWholeLastYear->sumout;
        }

        $query = "select AccountPlanID, AccountName, ReportShort from accountplan where EnableReportShort=1 and Active=1 order by ReportShort";
        $result2 = $_lib['db']->db_query($query);
        
        while($row = $_lib['db']->db_fetch_object($result2)) {
            #print_r($row);

            $queryThisYear = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.AccountPlanID=$row->AccountPlanID and V.VoucherPeriod<='".$this->thisEndPeriod."' and V.Active=1 and $where V.VoucherPeriod>='".$this->thisStartPeriod."'";
            $rowThisYear = $_lib['storage']->get_row(array('query' => $queryThisYear));

            $queryLastYear = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.AccountPlanID=$row->AccountPlanID and V.VoucherPeriod<='".$this->prevEndPeriod."' and V.Active=1 and $where V.VoucherPeriod>='".$this->prevStartPeriod."'";
            #print "sql: $queryLastYear<br />";
            $rowLastYear = $_lib['storage']->get_row(array('query' => $queryLastYear));

            $queryWholeLastYear = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.AccountPlanID=$row->AccountPlanID and V.Active=1 and $where substring(V.VoucherPeriod,1,4)='".(substr($this->thisStartPeriod,0,4)-1)."'";
            $rowWholeLastYear = $_lib['storage']->get_row(array('query' => $queryWholeLastYear));

            # Line
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['AccountPlanID']    = $row->AccountPlanID;
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['AccountName']      = $row->AccountName;
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['ThisYearAmount']   = $rowThisYear->sumin - $rowThisYear->sumout;
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['ThisYearPercent']  = '';
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['LastYearAmount']   = $rowLastYear->sumin - $rowLastYear->sumout;
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['LastYearPercent']  = '';
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['Year']             = $rowWholeLastYear->sumin - $rowWholeLastYear->sumout;
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['Percent']          = '';
            $this->lineH[$row->ReportShort][$row->AccountPlanID]['LineID']           = $row->ReportShort;
            
            # Sum
            $this->lineSumH[$row->ReportShort]['ThisYearAmount']   += ($rowThisYear->sumin - $rowThisYear->sumout);
            $this->lineSumH[$row->ReportShort]['LastYearAmount']   += ($rowLastYear->sumin - $rowLastYear->sumout);
            $this->lineSumH[$row->ReportShort]['Year']             += ($rowWholeLastYear->sumin - $rowWholeLastYear->sumout);
            $this->lineSumH[$row->ReportShort]['LineID']           = $row->ReportShort;
        }


        foreach($this->lineH as $linenum => $tmp) {
            ksort($this->lineH[$linenum], SORT_REGULAR);
        }

        #Oversett linjenavn
        $linetext = new linetextmap(array('ReportID' => 100));
        
        foreach($this->lineSumH as $LineID => $tmp) {
            $this->lineSumH[$LineID]['LineText']        = $linetext->getTextFromLineNr(array('Line' =>$LineID, 'LanguageID' => 'no'));
            if($LastYearSum != 0) {
                $this->lineSumH[$LineID]['LastYearPercent'] = (100 / $LastYearSum) * $this->lineSumH[$LineID]['LastYearAmount'];
                 #print "$LineID = $LastYearSum * " . $this->lineSumH[$LineID]['LastYearAmount'] . " percemnt " . $this->lineSumH[$LineID]['LastYearPercent'] . "<br>\n";
            }

            if($WholeLastYearSum != 0)
                $this->lineSumH[$LineID]['Percent'] = (100 / $WholeLastYearSum) * $this->lineSumH[$LineID]['Year'];
            
            if($ThisYearSum != 0)
                $this->lineSumH[$LineID]['ThisYearPercent'] = (100 / $ThisYearSum) * $this->lineSumH[$LineID]['ThisYearAmount'];
                  
        }
    }
}
?>