<?
includemodel('feriepenger/grid');

class salaryreport
{
    public $_year;
    private $_personID;
    private $_workedWholeYear = 0;
    private $_workStart = "";
    private $_workStop = "";
    private $_workedDays = 0;

    private $_query = '';
    private $_accountrow;
    private $_salaryLineHash = array();

    private $_salaryTopLineHash = array('111-A'=>'1', '112-A'=>'1', '116-A'=>'1', '000'=>'1', '313'=>'1', '316'=>'1', '950'=>'1',
                                    '211'=>'1', '311'=>'1', '312'=>'1', '314'=>'1', '401'=>'1', '711'=>'1');


    public $_reportHash = array();
    public $_comanyInfo;
##############################################################

    function salaryreport($args)
    {
//    	print_r($args);
        global $_lib, $_SETUP;

        $this->_year = $args['year'];
        $this->_personID = $args['employeeID'];

        includelogic('company/companyinfo');
        $this->_comanyInfo = new ComanyInfo(array());
        #print_r($this->_comanyInfo);

        $this->_query = "select a.AccountName, a.Address, a.ZipCode, a.City, a.SocietyNumber, k.KommuneNumber, k.KommuneName, a.WorkStart, a.WorkStop, a.WorkPercent from accountplan as a left join kommune as k on (a.KommuneID=k.KommuneID) where a.AccountPlanID='$this->_personID'";
        $this->_accountrow = $_lib['storage']->get_row(array('query' => $this->_query));

        #companyinfo
        $this->_reportHash['company']['VName']                   = $this->_comanyInfo->CustomerCompany->CompanyName;
        $this->_reportHash['company']['VAddress']                = $this->_comanyInfo->CustomerCompany->VAddress;
        $this->_reportHash['company']['VZipCode']                = $this->_comanyInfo->CustomerCompany->VZipCode;
        $this->_reportHash['company']['VCity']                   = $this->_comanyInfo->CustomerCompany->VCity;
        $this->_reportHash['company']['OrgNumber']               = $this->_comanyInfo->CustomerCompany->OrgNumber;
        $this->_reportHash['company']['CompanyMunicipality']     = $this->_comanyInfo->CustomerCompany->CompanyMunicipality;
        $this->_reportHash['company']['CompanyMunicipalityName'] = $this->_comanyInfo->CustomerCompany->CompanyMunicipalityName;

        #personinfo
        $this->_reportHash['account']['AccountName']   = $this->_accountrow->AccountName;
        $this->_reportHash['account']['Address']       = $this->_accountrow->Address;
        $this->_reportHash['account']['ZipCode']       = $this->_accountrow->ZipCode;
        $this->_reportHash['account']['City']          = $this->_accountrow->City;
        $this->_reportHash['account']['SocietyNumber'] = $this->_accountrow->SocietyNumber;
        $this->_reportHash['account']['KommuneNumber'] = $this->_accountrow->KommuneNumber;
        $this->_reportHash['account']['KommuneName']   = $this->_accountrow->KommuneName;
        $this->_reportHash['account']['City']          = $this->_accountrow->City;
        $this->CalculateWorkAmount();

        $this->_query = "select sum(sl.AmountThisPeriod) as sumLineCode, sl.SalaryCode, sl.SalaryText, sum(sl.NumberInPeriod) as NumberInPeriod from salary as s, salaryline as sl where s.AccountPlanID='$this->_personID' and s.SalaryID=sl.SalaryID and substring(s.Period, 1, 4)='$this->_year' and sl.SalaryCode is not null and sl.SalaryCode != '' group by sl.SalaryCode";
        #print "$this->_query<br>\n"; 
        $this->_salaryLineHash = $_lib['storage']->get_hashhash(array('query'=>$this->_query, 'key'=>'SalaryCode'));

        foreach($this->_salaryLineHash as $salaryCode => $lineHash)
        {
            if($this->_salaryTopLineHash[$salaryCode] == 1)
            {
                $this->_reportHash['head'][$salaryCode] = $lineHash;
            }
        }
        
        # Beregn feriepenger
        $grid = new feriepenger_grid();
        $grid->selectYear($this->_year);
        $result = $grid->gridPerson($this->_personID);
        
        $this->_reportHash['head']['000']['sumLineCode'] = $result["SkyldigFeriepengeGrunnlag"];
        
        $this->_query = "select sum(sl.AmountThisPeriod) as sumLineCode, sl.SalaryCode, sl.SalaryText, sum(sl.NumberInPeriod) as NumberInPeriod, sl.LineNumber from salary as s, salaryline as sl where s.AccountPlanID='$this->_personID' and s.SalaryID=sl.SalaryID and substring(s.Period, 1, 4)='$this->_year' and sl.SalaryCode is not null and sl.SalaryCode != '' group by sl.LineNumber";
        $this->_salaryLineHash = $_lib['storage']->get_hashhash(array('query'=>$this->_query, 'key'=>'LineNumber'));
        foreach($this->_salaryLineHash as $LineNumber => $lineHash)
        {
        	$salaryCode = $lineHash["SalaryCode"];
            if($this->_salaryTopLineHash[$salaryCode] != 1)
            {
                //$this->_salaryBottomLineHash[$salaryCode] = $lineHash;
                $this->_reportHash['body'][$LineNumber] = $lineHash;
            }
        }
    }

##############################################################

    function get_workDays()
    {
        if($this->_workedWholeYear == 1)
        {
            return 0;
        }
        else
        {
            return $this->_workedDays;
        }
    }

##############################################################

    function CalculateWorkAmount()
    {
        global $_lib;
        if(strlen($this->_accountrow->WorkStart) > 0 and $this->_accountrow->WorkStart != '0000-00-00')
        {
            if($this->_accountrow->WorkStart <= "$this->_year-01-01")
            {
                $this->_reportHash['account']['WorkedWholeYear'] = 1;
                $this->_reportHash['account']['WorkStart'] = "$this->_year-01-01";
            }
            else
            {
                $this->_reportHash['account']['WorkedWholeYear'] = 0;
                $this->_reportHash['account']['WorkStart'] = $this->_accountrow->WorkStart;
            }
        }
        else
        {
            $this->_reportHash['account']['WorkStart'] = "$this->_year-01-01";
            $this->_reportHash['account']['WorkedWholeYear'] = 1;
        }

        if(strlen($this->_accountrow->WorkStop) > 0 and $this->_accountrow->WorkStop != '0000-00-00' and strlen($this->_reportHash['account']['WorkStart']) > 0)
        {
            if($this->_accountrow->WorkStop < "$this->_year-12-31")
            {
                #vi kan sette den til 0 uansett hva den er satt til fra start
                $this->_reportHash['account']['WorkedWholeYear'] = 0;
                $this->_reportHash['account']['WorkStop'] = $this->_accountrow->WorkStop;
            }
            else
            {
                #hvis den er satt til 1 fra start, kan vi sette den til 1 her og
                if($this->_reportHash['account']['WorkedWholeYear'] == 1)
                    $this->_reportHash['account']['WorkedWholeYear'] = 1;
                else
                    $this->_reportHash['account']['WorkedWholeYear'] = 0;
                $this->_reportHash['account']['WorkStop'] = "$this->_year-12-31";
            }
            $workedStartPoint = $this->_reportHash['account']['WorkStart'];
            $workedStartPoint = $this->_reportHash['account']['WorkStop'];
            
            // $this->_reportHash['account']['WorkedDays'] = 
            $myDays = $_lib['date']->dateDiff($this->_reportHash['account']['WorkStop'], $this->_reportHash['account']['WorkStart']);
            if ($myDays > 0)
	            $this->_reportHash['account']['WorkedDays'] = $myDays + 1;
	        else
	            $this->_reportHash['account']['WorkedDays'] = 0;
        }
        else
        {
            $this->_reportHash['account']['WorkStop'] = "$this->_year-12-31";
//            $this->_reportHash['account']['WorkedWholeYear'] = 1;
            $this->_reportHash['account']['WorkedDays'] = $_lib['date']->dateDiff($this->_reportHash['account']['WorkStop'], $this->_reportHash['account']['WorkStart']);
        }
       	$this->_reportHash['account']['WorkPercent'] = $this->_accountrow->WorkPercent;
        if ($this->_accountrow->WorkPercent != 100 || $this->_accountrow->WorkPercent != "" )
        {
        	$this->_reportHash['account']['WorkedDays'] = $this->_accountrow->WorkPercent * $this->_reportHash['account']['WorkedDays'] / 100;
        }
    }
}
?>
