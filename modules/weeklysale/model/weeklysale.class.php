<?
/* $Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includelogic('accounting/accounting');

class weeklysale {
    public $head        = array();
    public $salehead;
    public $sale        = array();
    public $revenuehead = array();
    public $revenue     = array();

    function __construct($WeeklySaleID, $WeeklySaleConfID) {
    
        #print "kaller makegroups: WeeklySaleID: $WeeklySaleID, WeeklySaleConfID: $WeeklySaleConfID<br />\n";
        $this->makegroups($WeeklySaleID, $WeeklySaleConfID);
    }

    function makegroups($WeeklySaleID, $WeeklySaleConfID) {
        global $_lib;

        #print "makegroups: WeeklySaleID: $WeeklySaleID, WeeklySaleConfID: $WeeklySaleConfID<br />\n";
        #debug_print_backtrace();

        if($WeeklySaleID) {
            $query_week             = "select * from weeklysale where WeeklySaleID = '$WeeklySaleID'";
            $result_week            = $_lib['db']->db_query($query_week);
            $this->head             = $_lib['db']->db_fetch_object($result_week);
            $WeeklySaleConfID       = $this->head->WeeklySaleConfID;
        } elseif($WeeklySaleConfID) {
            $WeeklySaleConfID = $WeeklySaleConfID;
        } else {
            print "Missing WeeklySaleID or WeeklySaleConfID<br />\n";
            #debug_print_backtrace();            
        }
        
        $query_conf_head        = "select * from weeklysaleconf where WeeklySaleConfID = " . (int) $WeeklySaleConfID . " limit 1";
        #print "q1: $query_conf_head<br />\n";
        $this->sale_conf_head   = $_lib['storage']->get_row(array('query' => $query_conf_head));
        
        $query_sale_conf        = "select * from weeklysalegroupconf where WeeklySaleConfID = " . (int) $WeeklySaleConfID . " and Type=1 limit 1";
        #print "q2: $query_sale_conf<br />\n";
        $this->sale_conf        = $_lib['storage']->get_row(array('query' => $query_sale_conf));
        //$_lib['sess']->debug($query_sale_conf);
        
        $query_revenue_conf     = "select * from weeklysalegroupconf where WeeklySaleConfID = " . (int) $WeeklySaleConfID . " and Type=2 limit 1";
        #print "q3: $query_revenue_conf<br />\n";
        $this->revenue_conf     = $_lib['storage']->get_row(array('query' => $query_revenue_conf));

        if($this->sale_conf->Group1Name)  { $this->salehead['groups'][$this->sale_conf->Group1Name] = 1; }
        if($this->sale_conf->Group2Name)  { $this->salehead['groups'][$this->sale_conf->Group2Name] = 2; }
        if($this->sale_conf->Group3Name)  { $this->salehead['groups'][$this->sale_conf->Group3Name] = 3; }
        if($this->sale_conf->Group4Name)  { $this->salehead['groups'][$this->sale_conf->Group4Name] = 4; }
        if($this->sale_conf->Group5Name)  { $this->salehead['groups'][$this->sale_conf->Group5Name] = 5; }
        if($this->sale_conf->Group6Name)  { $this->salehead['groups'][$this->sale_conf->Group6Name] = 6; }
        if($this->sale_conf->Group7Name)  { $this->salehead['groups'][$this->sale_conf->Group7Name] = 7; }
        if($this->sale_conf->Group8Name)  { $this->salehead['groups'][$this->sale_conf->Group8Name] = 8; }
        if($this->sale_conf->Group9Name)  { $this->salehead['groups'][$this->sale_conf->Group9Name] = 9; }
        if($this->sale_conf->Group10Name) { $this->salehead['groups'][$this->sale_conf->Group10Name] = 10; }
        if($this->sale_conf->Group11Name) { $this->salehead['groups'][$this->sale_conf->Group11Name] = 11; }
        if($this->sale_conf->Group12Name) { $this->salehead['groups'][$this->sale_conf->Group12Name] = 12; }
        if($this->sale_conf->Group13Name) { $this->salehead['groups'][$this->sale_conf->Group13Name] = 13; }
        if($this->sale_conf->Group14Name) { $this->salehead['groups'][$this->sale_conf->Group14Name] = 14; }
        if($this->sale_conf->Group15Name) { $this->salehead['groups'][$this->sale_conf->Group15Name] = 15; }
        if($this->sale_conf->Group16Name) { $this->salehead['groups'][$this->sale_conf->Group16Name] = 16; }
        if($this->sale_conf->Group17Name) { $this->salehead['groups'][$this->sale_conf->Group17Name] = 17; }
        if($this->sale_conf->Group18Name) { $this->salehead['groups'][$this->sale_conf->Group18Name] = 18; }
        if($this->sale_conf->Group19Name) { $this->salehead['groups'][$this->sale_conf->Group19Name] = 19; }
        if($this->sale_conf->Group20Name) { $this->salehead['groups'][$this->sale_conf->Group20Name] = 20; }

        if($this->revenue_conf->Group1Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group1Name] = 1; }
        if($this->revenue_conf->Group2Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group2Name] = 2; }
        if($this->revenue_conf->Group3Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group3Name] = 3; }
        if($this->revenue_conf->Group4Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group4Name] = 4; }
        if($this->revenue_conf->Group5Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group5Name] = 5; }
        if($this->revenue_conf->Group6Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group6Name] = 6; }
        if($this->revenue_conf->Group7Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group7Name] = 7; }
        if($this->revenue_conf->Group8Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group8Name] = 8; }
        if($this->revenue_conf->Group9Name)  { $this->revenuehead['groups'][$this->revenue_conf->Group9Name] = 9; }
        if($this->revenue_conf->Group10Name) { $this->revenuehead['groups'][$this->revenue_conf->Group10Name] = 10; }
        if($this->revenue_conf->Group11Name) { $this->revenuehead['groups'][$this->revenue_conf->Group11Name] = 11; }
        if($this->revenue_conf->Group12Name) { $this->revenuehead['groups'][$this->revenue_conf->Group12Name] = 12; }
        if($this->revenue_conf->Group13Name) { $this->revenuehead['groups'][$this->revenue_conf->Group13Name] = 13; }
        if($this->revenue_conf->Group14Name) { $this->revenuehead['groups'][$this->revenue_conf->Group14Name] = 14; }
        if($this->revenue_conf->Group15Name) { $this->revenuehead['groups'][$this->revenue_conf->Group15Name] = 15; }
        if($this->revenue_conf->Group16Name) { $this->revenuehead['groups'][$this->revenue_conf->Group16Name] = 16; }
        if($this->revenue_conf->Group17Name) { $this->revenuehead['groups'][$this->revenue_conf->Group17Name] = 17; }
    
        #print_r($this->revenuehead['groups']);
        #print_r($this->salehead['groups']);
        #print "Ferdig med gruppene<br />\n";
    }

    function presentation() {
        global $_lib;

        #print "Starter presentation<br />\n";

        $accounting = new accounting();

        $readonly = "";
        if($this->head->Period and !$accounting->is_valid_accountperiod($this->head->Period, $_lib['sess']->get_person('AccessLevel'))) {
          $message .= "Perioden er lukket, du kan ikke endre data";
          $readonly = "readonly disable";
        }

        $query_sale         = "select * from weeklysaleday where WeeklySaleID = '" . $this->head->WeeklySaleID . "' and Type=1 order by DayID asc";
        #print "q4: $query_sale<br />\n";
        $result_sale        = $_lib['db']->db_query($query_sale);

        $query_revenue  = "select * from weeklysaleday where WeeklySaleID = '" . $this->head->WeeklySaleID . "' and Type=2 order by DayID asc";
        #print "q5: $query_revenue<br />\n";

        $result_revenue = $_lib['db']->db_query($query_revenue);

        #$this->head->Period = $_lib['form3']->AccountPeriod_menu3(array('table' => 'weeklysale', 'field' => 'Period', 'pk'=>$this->head->WeeklySaleID, 'value' => $this->head->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'pk' => $this->head->WeeklySaleID, 'tabindex'=>'3', 'required'=>'1'));
        $query="select DepartmentName from companydepartment where CompanyDepartmentID='" . $this->head->DepartmentID . "'";
        #print "q6: $query<br />\n";

        $row=$_lib['storage']->get_row(array('query' => $query));

        $this->head->DepartmentName        = $row->DepartmentName;
        $this->head->TemplateName          = $this->sale_conf_head->Name;
        $this->head->CompanyAddress        = $_lib['sess']->get_companydef('VName');
        $this->head->CompanyAddress        = $_lib['sess']->get_companydef('VAddress');
        $this->head->CompanyZipCode        = $_lib['sess']->get_companydef('VZipCode');
        $this->head->CompanyCity           = $_lib['sess']->get_companydef('VCity');
        $this->head->CompanyPhone          = $_lib['sess']->get_companydef('Phone');
        $this->head->CompanyMobile         = $_lib['sess']->get_companydef('Mobile');

        /******************************************************************************************/
        while($sale = $_lib['db']->db_fetch_object($result_sale))
        {
            if($sale->Locked == 1 and $readonly == "")
            {
                $readonly = "disabled";
            }

            foreach($this->salehead['groups'] as $name => $i) {
                $amountfield            = "Group{$i}Amount";
                $accountfield           = "Group{$i}Account";
                $quantityfield          = "Group{$i}Quantity";
                
                $this->salehead['sumday'][$sale->ParentWeeklySaleDayID]   += $sale->{$amountfield}; #MERK: MŒ endres fra dayid for Œ summere riktig
                $this->salehead['sumgroup'][$i]         += $sale->{$amountfield};
                $this->salehead['sumquantity'][$i]      += $sale->{$quantityfield};
            }
            if(!$this->salehead['sumday'][$sale->ParentWeeklySaleDayID]) { $this->salehead['sumday'][$sale->ParentWeeklySaleDayID] = 0; };
            $this->head->saletotal += $this->salehead['sumday'][$sale->ParentWeeklySaleDayID];

            #print_r($this->salehead['sumday']);
            #print "<h1>" . $this->head->saletotal . "</h1>";

            $this->head->saleznrtotal += $sale->ZnrTotalAmount;

            $this->sale[$sale->WeeklySaleDayID] = $sale;
            $this->sale[$sale->WeeklySaleDayID]->WeekDayName = $_lib['date']->get_WeekDayName($sale->DayID);

           if($sale->PersonID > 0) {
               $this->sale[$sale->WeeklySaleDayID]->Person = $_lib['format']->PersonIDToName(array('value' => $sale->PersonID, 'return' => 'value'));
           } else {
               $this->sale[$sale->WeeklySaleDayID]->Person = '';
           }
           $counter += 43;

        }

        foreach($this->salehead['groups'] as $name => $i) {
            $amountfield             = "Group{$i}Amount";
            $accountfield            = "Group{$i}Account";
            $projectfield            = "Group{$i}ProjectID";
            $departmentfield         = "Group{$i}DepartmentID";

            $account = $accounting->get_accountplan_object($this->sale_conf->{$accountfield});
            $this->salehead['account'][$i] = $account->AccountPlanID . "-" . substr($account->AccountName,0,5);

            if($this->sale_conf->{$projectfield} && $account->EnableProject)
                $this->salehead['project'][$i] = $this->projectname($this->sale_conf->{$projectfield}); 
            else
                $this->salehead['project'][$i] = '';
          
            if($this->sale_conf->{$departmentfield} && $account->EnableDepartment)
                $this->salehead['department'][$i]  = $this->departmentname($this->sale_conf->{$departmentfield});
            else 
                $this->salehead['department'][$i] = '';

            $this->salehead['enablequantity'][$i]   = $account->EnableQuantity;
        }
        
        #Hvorfor skal disse hardkodes? TE
        #$this->salehead['project'][14] = $this->projectname(14);
        #$this->salehead['project'][15] = $this->projectname(15);
        #$this->salehead['project'][16] = $this->projectname(16);
        #$this->salehead['project'][17] = $this->projectname(17);
        #$this->salehead['project'][18] = $this->projectname(18);
        #$this->salehead['project'][19] = $this->projectname(19);
        #$this->salehead['project'][20] = $this->projectname(20);

        
        /******************************************************************************************/
        /* REVENUE */
        /******************************************************************************************/
             
        $this->sumtot   = 0;
        $this->sum      = array();
        $counter        = 0;

        while($revenue = $_lib['db']->db_fetch_object($result_revenue))
        {
            if($revenue->Locked == 1 and $readonly == "")
            {
                $readonly = "disabled";
            }

	    if(is_array($this->revenuehead['groups'])) {
	        foreach($this->revenuehead['groups'] as $name => $i) {
                    $amount = "Group{$i}Amount";
                
                    $this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID]     += $revenue->{$amount}; #MERK: MŒ endres fra dayid for Œ summere riktig
                    $this->revenuehead['sumgroup'][$i]              += $revenue->{$amount};                
                }
	    }
            
            if(!isset($this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID])) { $this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID] = 0; };
            $this->head->sum18 = $this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID] - $this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID]; #Kontant

            #Sum vertical
            $this->revenuehead['sumgroup'][18] += $this->head->sum18;
            $this->head->sumcashin   += $revenue->Group19Amount; #sum19
            $this->head->sumcashout  += $revenue->Group20Amount; #sum20

            $this->head->cashDiff               = $revenue->ActuallyCashAmount - $this->head->sum18;
            $this->head->sumActuallyCashAmount += $revenue->ActuallyCashAmount;

            $this->revenue[$revenue->WeeklySaleDayID]               = $revenue;
            $this->revenue[$revenue->WeeklySaleDayID]->WeekDayName  = $_lib['date']->get_WeekDayName($revenue->DayID);
            if($revenue->PersonID > 0) {
                $this->revenue[$revenue->WeeklySaleDayID]->Person = $_lib['format']->PersonIDToName(array('value' => $revenue->PersonID, 'return' => 'value'));
            }
            $counter += 43;
            
            
            #extra
            $this->revenuehead['sumcash'][$revenue->ParentWeeklySaleDayID] = $this->salehead['sumday'][$revenue->ParentWeeklySaleDayID]  - $this->revenuehead['sumday'][$revenue->ParentWeeklySaleDayID];
            $this->head->sumcash                        += $this->revenuehead['sumcash'][$revenue->ParentWeeklySaleDayID];
            $this->revenuehead['sumdiff'][$revenue->ParentWeeklySaleDayID] = $this->revenuehead['sumcash'][$revenue->ParentWeeklySaleDayID] - $revenue->ActuallyCashAmount;
            $this->head->sumcashdiff                    += $this->revenuehead['sumdiff'][$revenue->ParentWeeklySaleDayID];
        }


        /******************************************************************************************/
        # Calculations
        /*
        foreach ($this->revenuehead['sumday'] as $znr => $amount)
        {
            #print "$amount+";
            $this->revenuehead['sumcash'][$znr] = $this->salehead['sumday'][$znr] - $amount;
            $this->revenuehead['sumdiff'][$znr] = $this->salehead['sumcash'][$znr] - minus opptelling;
            $this->revenuetotal += $amount;
        }
        */

        if(is_array($this->revenuehead['groups'])) {
            foreach($this->revenuehead['groups'] as $name => $i) {
                $accountfield       = "Group{$i}Account";
                $projectfield       = "Group{$i}ProjectID";
                $departmentfield    = "Group{$i}DepartmentID";
        
                $account = $accounting->get_accountplan_object($this->revenue_conf->{$accountfield});
    
                $this->revenuehead['account'][$i]     = $account->AccountPlanID . "-" . substr($account->AccountName,0,5);
                
                if($this->revenue_conf->{$projectfield} && $account->EnableProject)
                    $this->revenuehead['project'][$i]     = $this->projectname($this->revenue_conf->{$projectfield});
                else 
                    $this->revenuehead['project'][$i] = '';
                    
                if($this->revenue_conf->{$departmentfield} && $account->EnableDepartment)
                    $this->revenuehead['department'][$i]  = $this->departmentname($this->revenue_conf->{$departmentfield});
                else 
                    $this->revenuehead['department'][$i] = '';
            }
        }

        $account = $accounting->get_accountplan_object($this->revenue_conf->Group18Account);
        $this->revenuehead['account'][18]         = $account->AccountPlanID . "-" . substr($account->AccountName,0,5);
        
        if($this->revenue_conf->Group18ProjectID && $account->EnableProject)
            $this->revenuehead['project'][18]         = $this->projectname($this->revenue_conf->Group18ProjectID);
        else
            $this->revenuehead['project'][18] = '';

        if($this->revenue_conf->Group18DepartmentID && $account->EnableDepartment)
            $this->revenuehead['department'][18]      = $this->departmentname($this->revenue_conf->Group18DepartmentID);
        else
            $this->revenuehead['department'][18] = '';

        #print "saletotal: " . $this->head->sumcash . " + sumcashin: " . $this->head->sumcashin . " - sumcashout: " . $this->sumcashout . " + PrivateAmount: " . $this->head->PrivateAmount . "<br>\n";
        $this->head->TotalAmount = $this->head->sumcash + $this->head->sumcashin - $this->head->sumcashout + $this->head->PrivateAmount; #total_week

	foreach(range(1, 3) as $i)
	        $this->head->TotalAmount += $this->head->{"Bank" . $i ."Amount"};

        #print_r($this->revenue);

        return true;
    }

    function departmentname($CompanyDepartmentID) {
        global $_lib;
        
        if(isset($CompanyDepartmentID)) {
            $query="select * from companydepartment where CompanyDepartmentID=" . (int) $CompanyDepartmentID;
            $row = $_lib['storage']->get_row(array('query' => $query));
            $departmentname = $CompanyDepartmentID ."-" . substr($row->DepartmentName,0,7);
        }
        return $departmentname;
    }

    function projectname($ProjectID) {
        global $_lib;
        
        $query="select Heading from project where ProjectID=" . (int) $ProjectID;
        $row = $_lib['storage']->get_row(array('query' => $query));
        return $ProjectID ."-" . substr($row->Heading,0,7);
    }
    
    function isUniqueZnr($template, $znr) {
        global $_lib;
    
        $query_znr         = "select count(wsd.Znr) as count from weeklysale as ws, weeklysaleconf as wsc, weeklysaleday as wsd where wsc.Name = '" . $_lib['db']->db_escape($template) . "' and wsc.WeeklySaleConfID=ws.WeeklySaleConfID and ws.WeeklySaleID=wsd.WeeklySaleID and wsd.Znr=" . $znr . " and wsd.Type=1";
        #print "isUniqueZnr: $query_znr<br>";
        $result_znr        = $_lib['db']->db_query($query_znr);
        $row               = $_lib['db']->db_fetch_object($result_znr);

        return $row->count;
    }
}
