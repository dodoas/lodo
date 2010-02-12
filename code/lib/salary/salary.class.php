<?
class salary {
    var $ValidFrom = '';
    var $ValidTo   = '';
    var $PersonID  = 0;

    function salary($args) {
        $this->ValidFrom    = $args['ValidFrom'];
        $this->ValidTo      = $args['ValidTo'];
        $this->PersonID     = $args['PersonID'];
    }

    function project_salary() {
        global $_sess, $_dbh, $_dsn, $_date;

        $query_person   = "select * from person where PersonID='$this->PersonID'";
        $person         = $_lib['db']->get_row(array('query' => $query_person));
        // print_r($person);
        $accountplan = $accounting->get_accountplan_object($person->AccountPlanID);

        #Possible to extend or alter parameters here
        $postmain['salary_SalaryConfID']        = 0;
        $postmain['salary_AccountPlanID']       = $person->AccountPlanID;
        $postmain['salary_ValidFrom']           = $this->ValidFrom;
        $postmain['salary_ValidTo']             = $this->ValidTo;
        $postmain['salary_PayDate']             = $this->ValidTo;
        $postmain['salary_JournalDate']         = $this->ValidTo;
        #$postmain['salary_Period']                 = $_date->get_this_period(this->ValidTo);
        $postmain['salary_DomesticBankAccount'] = $accountplan->DomesticBankAccount;
        $postmain['salary_CreatedByPersonID']   = $_sess->get_person('PersonID');

        #Insert salary head
        $SalaryID = $_lib['db']->db_new_hash($postmain, 'salary');

        $postsub['salaryline_SalaryID'] = $SalaryID;

        #############################################################
        #Pay pr hour, only for activities with salary enabled
        $query = "select sum(t.Hours) as Hours from timeliste as t, projectactivity as a where  t.PersonID='$this->PersonID' and t.Date >= '$this->ValidFrom' and t.Date <= '$this->ValidTo' and t.SalaryID=0 and t.ProjectID=a.ProjectID and t.ProjectActivityID=a.ProjectActivityID and a.EnableSalary=1 group by t.PersonID";
        print "$query<br>";
        $result = $_lib['db']->db_query($query);
        $accountplan_id = 5000; #L¿nn
        $feriepenger    = 0.12;
        $offentlig      = 0.141; #Arb giver avgift

        $salary = $_lib['db']->db_fetch_object($result);
        $postsub['salaryline_LineNumber']               = 11;
        $postsub['salaryline_AccountPlanID']            = $accountplan_id;
        $postsub['salaryline_NumberInPeriod']           = $salary->Hours;
        $postsub['salaryline_Rate']                     = $person->CostPrice;
        $postsub['salaryline_SalaryText']               = "Timel&oslash;nn";
        $postsub['salaryline_AmountThisPeriod']         = ($salary->Hours * $person->CostPrice);
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_ProjectID']                = 0;
        $postsub['salaryline_DepartmentID']             = 0;
        $postsub['salaryline_SalaryCode']               = '111-A';
        $postsub['salaryline_EnableVacationPayment']    = 1;
        $postsub['salaryline_EnableEmployeeTax']        = 1;
        $postsub['salaryline_EmployeeTax']              = 1;
        $_lib['db']->db_new_hash($postsub, 'salaryline');

        $salary = $_lib['db']->db_fetch_object($result);
        $postsub['salaryline_LineNumber']               = 12;
        $postsub['salaryline_AccountPlanID']            = $accountplan_id;
        $postsub['salaryline_NumberInPeriod']           = 0;
        $postsub['salaryline_Rate']                     = 0;
        $postsub['salaryline_SalaryText']               = "Bonus";
        $postsub['salaryline_AmountThisPeriod']         = 0;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_ProjectID']                = 0;
        $postsub['salaryline_DepartmentID']             = 0;
        $postsub['salaryline_SalaryCode']               = '111-A';
        $postsub['salaryline_EnableVacationPayment']    = 1;
        $postsub['salaryline_EnableEmployeeTax']        = 1;
        $postsub['salaryline_EmployeeTax']              = 1;
        $_lib['db']->db_new_hash($postsub, 'salaryline');

        $salary = $_lib['db']->db_fetch_object($result);
        $postsub['salaryline_LineNumber']               = 30;
        $postsub['salaryline_AccountPlanID']            = $accountplan_id;
        $postsub['salaryline_NumberInPeriod']           = 0;
        $postsub['salaryline_Rate']                     = 0;
        $postsub['salaryline_SalaryText']               = "Km godtgj¿relse";
        $postsub['salaryline_AmountThisPeriod']         = 0;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_ProjectID']                = 0;
        $postsub['salaryline_DepartmentID']             = 0;
        $postsub['salaryline_SalaryCode']               = '711';
        $postsub['salaryline_EnableVacationPayment']    = 0;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_EmployeeTax']              = 0;
        $_lib['db']->db_new_hash($postsub, 'salaryline');

        $salary = $_lib['db']->db_fetch_object($result);

        $postsub['salaryline_LineNumber']               = 91;
        $postsub['salaryline_AccountPlanID']            = $accountplan_id;
        $postsub['salaryline_NumberInPeriod']           = 1;
        $postsub['salaryline_Rate']                     = $person->Tax;
        $postsub['salaryline_SalaryText']               = "Skatt";
        $postsub['salaryline_EnableVacationPayment']    = 0;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_EmployeeTax']              = 0;
        $postsub['salaryline_SalaryCode']        = '950';
        $postsub['salaryline_AmountThisPeriod']  = ($salary->Hours * $person->CostPrice) * $person->Tax;
        $_lib['db']->db_new_hash($postsub, 'salaryline');

        $query_update = "update timeliste set SalaryID='$SalaryID' where PersonID='$this->PersonID' and Date >= '$this->ValidFrom' and Date <= '$this->ValidTo' and SalaryID=0";
        $_lib['db']->db_update($query_update);

        #############################################################
        #Pay for expences
        $query   = "select sum($expence->Amount) as Amount from expenceline where ExpencePersonID='$this->PersonID' and ExpenceLineDate >= '$this->ValidFrom' and ExpenceLineDate <= '$this->ValidTo' order by ExpenceLineDate";
        $expence = $_lib['db']->get_row(array('query' => $query));
        $accountplan_id = 5990; #utgift

        $postsub['salaryline_LineNumber']               = 40;
        $postsub['salaryline_AccountPlanID']            = accountplan_id;
        $postsub['salaryline_NumberInPeriod']           = $expence->Amount;
        $postsub['salaryline_Rate']                     = 0;
        $postsub['salaryline_SalaryText']               = "Utgift etter regning";
        $postsub['salaryline_AmountThisPeriod']         = $expence->Amount;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_ProjectID']                = 0;
        $postsub['salaryline_DepartmentID']             = 0;
        $postsub['salaryline_EnableVacationPayment']    = 0;
        $postsub['salaryline_EnableEmployeeTax']        = 0;
        $postsub['salaryline_EmployeeTax']              = 0;
        $postsub['salaryline_SalaryCode']               = '';

        $_lib['db']->db_new_hash($postsub, 'salaryline');
        $query_update = "update expenceline set SalaryID='$SalaryID' where ExpencePersonID='$this->PersonID' and ExpenceLineDate >= '$this->ValidFrom' and ExpenceLineDate <= '$this->ValidTo' and SalaryID=0";
        $_lib['db']->db_update($query_update);

        #############################################################
        #Pay for travel
        $query = "select sum(DriveDistance) as KM from drivedistance where DrivePersonID='$this->PersonID' and DriveDate >= '$this->ValidFrom' and DriveDate <= '$this->ValidTo' group by DrivePersonID";
        $drive = $_lib['db']->get_row(array('query' => $query));
        $accountplan_id = 7130; #Reisekost oppgavepliktig
        $krprkm         = 3;    ##3kr pr km

        if($drive->KM > 0) {
            $postsub['salaryline_LineNumber']        = $linenum++;
            $postsub['salaryline_AccountPlanID']     = accountplan_id;
            $postsub['salaryline_NumberInPeriod']    = $drive->KM;
            $postsub['salaryline_Rate']              = krprkm;
            $postsub['salaryline_SalaryText']        = "Kj&oslash;ring med bil";
            $postsub['salaryline_AmountThisPeriod']  = krprkm * $drive->KM;
            $postsub['salaryline_EnableEmployeeTax'] = 0;
            $postsub['salaryline_ProjectID']         = 0;
            $postsub['salaryline_DepartmentID']      = 0;
            $_lib['db']->db_new_hash($postsub, 'salaryline');

            $query_update = "update drivedistance set SalaryID='$SalaryID' where DrivePersonID='$this->PersonID' and DriveDate >= '$this->ValidFrom' and DriveDate <= '$this->ValidTo' and SalaryID=0";
            $_lib['db']->db_update($query_update);
        }
    }
}
?>