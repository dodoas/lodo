<?
#Examples of retrieveing data
################################################################################
# Company
# $ComanyInfo->CustomerCompany->VName
# $ComanyInfo->CustomerCompany->VAddress
# $ComanyInfo->CustomerCompany->VZipCode
# $ComanyInfo->CustomerCompany->VCity
# $ComanyInfo->CustomerCompany->OrgNumber
# $ComanyInfo->CustomerCompany->FoundedYear
# $ComanyInfo->CustomerCompany->Category
#
################################################################################
# Person (always returns daglig leder from the different companies)
# $ComanyInfo->RegnskapPerson->FirstName
# $ComanyInfo->RegnskapPerson->LastName

################################################################################
class ComanyInfo
{
    public $CustomerCompany   = array();
    public $CustomerPerson    = array();
    public $RevisorCompany    = array();
    public $RevisorPerson     = array();
    public $RegnskapCompany   = array();
    public $RegnskapPerson    = array();
    public $FoundedThisYear   = 0;
    public $Labour            = 0;

    ############################################################################
    function ComanyInfo()
    {
        $this->CustomerCompany   = $this->_GetCompany(1);
        $this->CustomerPerson    = $this->_GetPerson($this->CustomerCompany);
        $this->RevisorCompany    = $this->_GetCompany(3);
        $this->RevisorPerson     = $this->_GetPerson($this->RevisorCompany);
        $this->RegnskapCompany   = $this->_GetCompany(2);
        $this->RegnskapPerson    = $this->_GetPerson($this->RegnskapCompany);
        $this->FoundedThisYear   = $this->IsFoundedThisYear($this->CustomerCompany->FoundedDate);
        #$this->Labour            = $this->CalculateLabour(array());

        // This is a correction of OrgNumber because someone are using space and the letters MVA in the string. OrgNumber2 is therefor a
        $this->CustomerCompany->OrgNumber2 = str_replace("mva", "", str_replace(" ", "", str_replace("MVA", "", $this->CustomerCompany->OrgNumber)));
        $this->RevisorCompany->OrgNumber2 = str_replace("mva", "", str_replace(" ", "", str_replace("MVA", "", $this->RevisorCompany->OrgNumber)));
        $this->RegnskapCompany->OrgNumber2 = str_replace("mva", "", str_replace(" ", "", str_replace("MVA", "", $this->RegnskapCompany->OrgNumber)));
    }

    ############################################################################
    private function _GetCompany($ClassificationID)
    {
        global $_lib;
        $query      = "select * from company where ClassificationID='" . $ClassificationID . "'";
        $company    = $_lib['storage']->get_row(array('query'=>$query));
        if(!$company && $ClassificationID != 3) print "Missing company with ClassificationID: $ClassificationID<br>\n";
        return $company;
    }

    ############################################################################
    private function _GetPerson($Company)
    {
        global $_lib;
        #Henter bare ut en person, dvs Daglig leder = ClassificationID = 3
        $query = "select p.* from person as p, companypersonstruct as cps where cps.CompanyID='" . $Company->CompanyID . "' and cps.PersonID=p.PersonID and p.ClassificationID=3 order by p.PersonID asc";
        return $_lib['storage']->get_row(array('query'=>$query));
    }

    ############################################################################
    function IsFoundedThisYear($FoundedDate)
    {
        global $_lib;
        $foundedYear = $_lib['date']->get_this_year($FoundedDate);
        if($foundedYear < '2002')
        {
            return '0';
        }
        else
        {
            return '1';
        }
    }

    ############################################################################
    function CalculateLabour($fromPeriod, $toPeriod)
    {
        global $_sess, $_date, $_dbh, $_dsn, $_lib;

        $query = "select * from accountplan where AccountPlanType='employee'";
        $result = $_lib['db']->db_query($query);

        $sum = 0;
        $startDate = $fromPeriod.'-01';
        $stopDate = $toPeriod.'-01';
        while($row = $_lib['db']->db_fetch_object($result))
        {
            if($row->WorkStart < $startDate)
            {
                $minusMonths = 0;
            }
            elseif($row->WorkStart < $stopDate)
            {
                $minusMonths = $_date->get_months_between_periods($_date->get_this_period($row->WorkStart), $_date->get_this_period($startDate));
            }
            else
            {
                $minusMonths = 12;
            }

            if($row->WorkStop > $stopDate)
            {
                $minusMonths = 0;
            }
            elseif($row->WorkStop > $startDate)
            {
                $minusMonths = $_date->get_months_between_periods($_date->get_this_period($row->WorkStop), $_date->get_this_period($stopDate));
            }
            else
            {
                $minusMonths = 12;
            }

            $sum += ((12 - $minusMonths) / 12 * $row->WorkPercent);
        }

        return $sum;
    }
    ############################################################################
}
?>
