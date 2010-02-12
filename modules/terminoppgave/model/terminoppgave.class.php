<?
# $Id: terminoppgave.php,v 1.22 2005/09/09 06:51:08 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

class logic_terminoppgave_terminoppgave {

    public $SoneH                = array();
    public $KommuneH             = array();
    public $SalaryH              = array();
    public $SalaryLineH          = array();
    public $ArbeidsgiveravgiftH  = array();

    #input: FromPeriod, 
    function __construct($args) {
        global $_lib;
        
        foreach($args as $key => $value){
            $this->{$key} = $value;
        }

        ################
        #hente ut alle linjenummer med arbavgift
        $query_arbavgift = "select distinct LineNumber, SalaryText from salaryline as sl, salary as s where (sl.EnableEmployeeTax=1 or sl.LineNumber=90 or sl.LineNumber=91 or sl.LineNumber=92) and s.Period >= '$this->FromPeriod' and s.Period <= '$this->ToPeriod' and sl.SalaryText != '' order by sl.LineNumber asc";
        #print "$query_arbavgift<br>";
        $result_arbavgift = $_lib['db']->db_query($query_arbavgift);
        while($row = $_lib['db']->db_fetch_object($result_arbavgift)) {   
            #print "linje: " . $form_linenumber1->value(array('field'=>'SalaryText')) . "<br>";
            $this->LineNumberMapH[$row->LineNumber] = $row->SalaryText;
        };
        #print_r($this->LineNumberMapH);
        
        ################
        $query_arbeidsgiveravgift = "select * from arbeidsgiveravgift order by Code asc";
        #print "$query_arbeidsgiveravgift<br>\n";
        $result_arbeidsgiveravgift = $_lib['db']->db_query($query_arbeidsgiveravgift);

        #sjekker om arbavgift finnes
        while($arbeidsgiveravgiftO = $_lib['db']->db_fetch_object($result_arbeidsgiveravgift)) {   
    
    
            if(!$arbeidsgiveravgiftO->Code) {
                print "Feil: Kode tom<br>";				
            }
    
            ################
            $query_kommune = "select * from kommune where Sone = '" . $arbeidsgiveravgiftO->Code . "' order by KommuneNumber asc";
            #print "$query2<br>";
            #print "$query_kommune<br>";
            $result_kommune = $_lib['db']->db_query($query_kommune);
    
            ################
            while($kommune = $_lib['db']->db_fetch_object($result_kommune)) {   

                $this->KommuneMapH[$kommune->KommuneID]->KommuneName    = $kommune->KommuneName;
                $this->KommuneMapH[$kommune->KommuneID]->KommuneNumber  = $kommune->KommuneNumber;
     
                ################
                #hente ut alle lønningshoder til denne kommunen
                $query_salary = "select S.SalaryID, S.AccountPlanID, S.JournalID, S.Period, A.AccountName, A.SocialSecurityNumber from salary S, accountplan A where S.Period >= '$this->FromPeriod' and S.Period <= '$this->ToPeriod' and S.AccountPlanID=A.AccountPlanID and A.KommuneID=".$kommune->KommuneID." order by S.AccountPlanID asc, S.JournalID asc";
                #print "$query_salary<br>";
                $result_salary = $_lib['db']->db_query($query_salary);
 
                while($salary = $_lib['db']->db_fetch_object($result_salary)) { 

                    $this->JournalMapH[$salary->JournalID]         =  $salary->Period;
                    $this->AccountPlanMapH[$salary->AccountPlanID] = $salary->AccountName;
     
                    ################
                    #looper over alle l¿nnslinjenummerne
                    #hente ut alle arbavgift lønningslinjer til dette lønningshodet
                    $query_salaryline = "select SL.* from salaryline SL where SL.SalaryID=" . $salary->SalaryID . " and (SL.EnableEmployeeTax=1 or SL.LineNumber=90 or SL.LineNumber=91 or SL.LineNumber=92) order by LineNumber asc";
                    #print "$query_salaryline<br>";
                    $result_salaryline          = $_lib['db']->db_query($query_salaryline);
                    while($salaryline = $_lib['db']->db_fetch_object($result_salaryline)) {

                        $LineID                                                 =  $salaryline->LineNumber;
                        $LineAmount                 		                    =  $salaryline->AmountThisPeriod;
                        if ($LineID >= 70)
                            $LineAmount = -$LineAmount;

                        if($LineID != 90 && $LineID != 91 && $LineID != 92) {

                            $this->ArbeidsgiveravgiftSoneH[$arbeidsgiveravgiftO->Code]->Amount                                                       += $LineAmount;
                            $this->ArbeidsgiveravgiftSoneH[$arbeidsgiveravgiftO->Code]->Percent                                                      = $arbeidsgiveravgiftO->Percent;
                            $this->ArbeidsgiveravgiftSoneH[$arbeidsgiveravgiftO->Code]->Avgift = $this->ArbeidsgiveravgiftSoneH[$arbeidsgiveravgiftO->Code]->Amount * ($this->ArbeidsgiveravgiftSoneH[$arbeidsgiveravgiftO->Code]->Percent/100);

                            $this->ArbeidsgiveravgiftKommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID]->Amount                               += $LineAmount;
                            $this->ArbeidsgiveravgiftKommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID]->Percent                              = $arbeidsgiveravgiftO->Percent;
                            $this->ArbeidsgiveravgiftKommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID]->Avgift = $this->ArbeidsgiveravgiftKommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID]->Amount * ($this->ArbeidsgiveravgiftKommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID]->Percent / 100);

                            $this->ArbeidsgiveravgiftAccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID]->Amount   += $LineAmount;
                            $this->ArbeidsgiveravgiftAccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID]->Percent  = $arbeidsgiveravgiftO->Percent;
                            $this->ArbeidsgiveravgiftAccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID]->Avgift   = $this->ArbeidsgiveravgiftAccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID]->Amount * ($this->ArbeidsgiveravgiftAccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID]->Percent/100);
                        }
     
                        $this->SumH[$LineID]                                                                += $LineAmount;
                        $this->SoneH[$arbeidsgiveravgiftO->Code][$LineID]                                   += $LineAmount;
                        $this->KommuneH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$LineID]           += $LineAmount;                        
                        $this->AccountPlanH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID][$LineID]                    += $LineAmount;
                        $this->SalaryLineH[$arbeidsgiveravgiftO->Code][$kommune->KommuneID][$salary->AccountPlanID][$salary->JournalID][$LineID] += $LineAmount;
                    };
                }
            }
        }
    
        #Sum arbeidsgvieravgift utenfor % nivŒ
        if(is_array($this->ArbeidsgiveravgiftSoneH)) {
            foreach($this->ArbeidsgiveravgiftSoneH as $SoneO) {
                $this->ArbeidsgiveravgiftSumO->Amount += $SoneO->Amount;
                $this->ArbeidsgiveravgiftSumO->Avgift += $SoneO->Avgift;
            }
        }
    }

    public function LineIDToName($LineID) {
        return $this->LineNumberMapH[$LineID];
    }
    
    public function AccountPlanIDToName($AccountPlanID) {
        return $this->AccountPlanMapH[$AccountPlanID];
    }
    
    public function JournalIDToName($SalaryID) {
        return $this->JournalMapH[$SalaryID];
    }

    public function KommuneIDToName($KommuneID) {
        return $this->KommuneMapH[$KommuneID]->KommuneName;
    }

    public function KommuneIDToNumber($KommuneID) {
        return $this->KommuneMapH[$KommuneID]->KommuneNumber;
    }


}
?>