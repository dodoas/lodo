<?
$debug = false;
$VoucherType = 'L';

if($debug) print_r($_REQUEST);

if($_lib['input']->getProperty('salary_save_info')) {
  $existing = $_POST['existingSalaryLines'];
  $checked  = $_POST['recieveSalary'];

  if($checked == null) {
    $checked = array();
  }

  foreach($existing as $item) {
    $sql = sprintf("DELETE FROM salaryinfo WHERE SalaryConfID = %d AND SalaryperiodconfID = %d LIMIT 1", $item, $SalaryperiodconfID);
    $_lib['db']->db_query($sql);

    $amount = $_POST['salaryinfo_amount_' . $item];
    $project = $_POST['salaryinfo_project_' . $item];
    if($project == '')
        $project = -1;

    if(in_array($item, $checked)) {
      $sql = sprintf("INSERT INTO salaryinfo (`SalaryConfID`, `amount`, `project`, `SalaryperiodconfID`) VALUES ('%d', '%s', '%s', '%d');",
                     $item, $amount, $project, $SalaryperiodconfID);
      $_lib['db']->db_query($sql);
    }
  }
}
elseif($_lib['input']->getProperty('action_salary_update'))
{
  #Possible to extend or alter parameters here
  $tables['salary']     = 'SalaryID';
  $tables['salaryline'] = 'SalaryLineID';

  $_lib['storage']->db_update_multi_table($_POST, $tables);
}
elseif($_lib['input']->getProperty('action_salarymainconfline_new'))
{
   $SalaryConfID = $_lib['input']->getProperty('SalaryConfID');

   $fields = array();
   $fields['salaryconfline_SalaryConfID'] = $SalaryConfID;

   $_lib['storage']->db_new_hash($fields, 'salaryconfline');
}
elseif($_lib['input']->getProperty('action_salaryconfline_new'))
{
   $SalaryConfLineID = $_lib['input']->getProperty('SalaryConfLineID');
   $SalaryConfID = $_lib['input']->getProperty('SalaryConfID');

   $query = "select * from $db_table2 where SalaryConfLineID=$SalaryConfLineID";
   $orgConfLine = $_lib['storage']->get_row(array('query' => $query));

   $fields = array();
   $fields['salaryconfline_SalaryConfID']      = $orgConfLine->SalaryConfID;
   $fields['salaryconfline_LineNumber']        = $orgConfLine->LineNumber;
   $fields['salaryconfline_SalaryText']        = $orgConfLine->SalaryText;
   $fields['salaryconfline_NumberInPeriod']    = $orgConfLine->NumberInPeriod;
   $fields['salaryconfline_Rate']              = $orgConfLine->Rate;
   $fields['salaryconfline_AmountThisPeriod']  = $orgConfLine->AmountThisPeriod;
   $fields['salaryconfline_AccountPlanID']     = $orgConfLine->AccountPlanID;
   $fields['salaryconfline_EnableEmployeeTax'] = $orgConfLine->EnableEmployeeTax;
   $fields['salaryconfline_ProjectID']         = $orgConfLine->ProjectID;
   $fields['salaryconfline_DepartmentID']      = $orgConfLine->DepartmentID;
   $fields['salaryconfline_SalaryCode']        = $orgConfLine->SalaryCode;

   $_lib['storage']->db_new_hash($fields, 'salaryconfline');
}
elseif($_lib['input']->getProperty('action_salaryline_new'))
{
    $SalaryLineID = $_lib['input']->getProperty('SalaryLineID');
    $SalaryID   = $_lib['input']->getProperty('SalaryID');

    $query = "select * from salaryline where SalaryLineID=$SalaryLineID";
    $orgLine = $_lib['storage']->get_row(array('query' => $query));

    $fields = array();
    $fields['salaryline_SalaryID']          = $orgLine->SalaryID;
    $fields['salaryline_LineNumber']        = $orgLine->LineNumber;
    $fields['salaryline_SalaryText']        = $orgLine->SalaryText;
    $fields['salaryline_NumberInPeriod']    = $orgLine->NumberInPeriod;
    $fields['salaryline_Rate']              = $orgLine->Rate;
    $fields['salaryline_AmountThisPeriod']  = $orgLine->AmountThisPeriod;
    $fields['salaryline_AmountThisYear']    = $orgLine->AmountThisYear;
    $fields['salaryline_AccountPlanID']     = $orgLine->AccountPlanID;
    $fields['salaryline_EnableEmployeeTax'] = $orgLine->EnableEmployeeTax;
    $fields['salaryline_EnableVacationPayment'] = $orgLine->EnableVacationPayment;
    $fields['salaryline_ProjectID']         = $orgLine->ProjectID;
    $fields['salaryline_DepartmentID']      = $orgLine->DepartmentID;
    $fields['salaryline_EmployeeTax']       = $orgLine->EmployeeTax;
    $fields['salaryline_SalaryCode']      	= $orgLine->SalaryCode;

    $_lib['storage']->db_new_hash($fields, 'salaryline');
}
elseif($_lib['input']->getProperty('action_defconf_save'))
{
    unset($_POST['action_defconf_save']);
    foreach ($_POST as $key => $value)
    {
        $elements = explode("_", $key);
        if(count($elements) == 3)
        {
            $query = "update setup set value='$value' where Name='$elements[2]'";
            //print "$query<br>\n";
            $_lib['db']->db_update($query);
        }
    }
}
elseif($_lib['input']->getProperty('action_salary_new'))
{
  list($JournalID, $tmp) = $accounting->get_next_available_journalid(array('available'=>true, 'update'=>true, 'type' =>'L'));

  $query="select SalaryID from salary where JournalID=$JournalID";
  if($debug) print "$query<br>\n";
  $row_salary = $_lib['storage']->get_row(array('query' => $query));
  if(isset($row_salary->SalaryID) and strlen($row_salary->SalaryID)>0)
  {
      $SalaryID=$row_salary->SalaryID;
  }
  else
  {
        $query_setup    = "select name, value from setup";
        $setup          = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));
        if($debug) print_r($setup);

        $query_salary_period_conf = "select * from salaryperiodconf where SalaryperiodconfID = '" . (int) $_lib['input']->getProperty('SalaryperiodconfID') . "'";

        $result_salary_period_conf = $_lib['db']->db_query($query_salary_period_conf);
        $salary_period_conf        = $_lib['db']->db_fetch_object($result_salary_period_conf);


        if(!empty($salary_period_conf->Voucherdate))
      {
          $journalDate = $salary_period_conf->Voucherdate;
      }
      else
      {
        $journalDate = $_lib['sess']->get_session('Date');
      }

        if(!empty($salary_period_conf->Fromdate))
      {
          $fromDate = $salary_period_conf->Fromdate;
      }
      else
      {
          $fromDate = $_lib['sess']->get_session('Date');
      }

        if(!empty($salary_period_conf->Todate))
      {
          $toDate = $salary_period_conf->Todate;
      }
      else
      {
          $toDate = $_lib['sess']->get_session('Date');
      }

      ################################################################
      #Get configuration spesific to this employee - overrides hovedmal on same line numbers
      $query_head_employee  = "select * from salaryconf where SalaryConfID = '" . (int) $_lib['input']->getProperty('SalaryConfID') . "'";
      $result_head_employee = $_lib['db']->db_query($query_head_employee);
      $head_employee        = $_lib['db']->db_fetch_object($result_head_employee);

      $accountplan = $accounting->get_accountplan_object($head_employee->AccountPlanID);

      #Possible to extend or alter parameters here
      $postmain['salary_JournalID']           = $JournalID;
      $postmain['salary_SalaryConfID']        = $SalaryConfID;
      $postmain['salary_AccountPlanID']       = $head_employee->AccountPlanID;
      $postmain['salary_ValidFrom']           = $fromDate;
      $postmain['salary_ValidTo']             = $toDate;
      $postmain['salary_JournalDate']         = $journalDate;
      $postmain['salary_Period']              = $salary_period_conf->Period;  //$_lib['date']->get_this_period($journalDate);

      if($accountplan->EnableCredit == 1)
        $postmain['salary_PayDate']           = $_lib['date']->add_Days($journalDate, $accountplan->CreditDays);
      else
        $postmain['salary_PayDate']           = $journalDate;

      $postmain['salary_DomesticBankAccount'] = $accountplan->DomesticBankAccount;
      $postmain['salary_CreatedByPersonID']   = $_lib['sess']->get_person('PersonID');

      #Insert salary head
      if($debug) print_r($postmain);

      $SalaryID = $_lib['storage']->db_new_hash($postmain, 'salary');

      ################################################################
      #Get configuration hovedmal - used as a basis for salary
      $query_head_default   = "select * from salaryconf where SalaryConfID = '1'";
      $result_head_default  = $_lib['db']->db_query($query_head_default);
      $head_default         = $_lib['db']->db_fetch_object($result_head_default);

      ################################################################
      #Get default / hovedmal configuration lines
      $query_salary_default     = "select * from salaryconfline where SalaryConfID = '1'";
      if($debug) print "$query_salary_default<br>\n";
      $result_salary_default    = $_lib['db']->db_query($query_salary_default);

      $lines = array();
      while($line = $_lib['db']->db_fetch_object($result_salary_default)) {
        $lines[$line->LineNumber]['AccountPlanID']      = $line->AccountPlanID;
        $lines[$line->LineNumber]['NumberInPeriod']     = $line->NumberInPeriod;
        $lines[$line->LineNumber]['Rate']               = $line->Rate;
        $lines[$line->LineNumber]['SalaryText']         = $line->SalaryText;
        $lines[$line->LineNumber]['AmountThisPeriod']   = $line->AmountThisPeriod;
        $lines[$line->LineNumber]['EnableEmployeeTax']  = $line->EnableEmployeeTax;
        $lines[$line->LineNumber]['SalaryID']           = $SalaryID;
        $lines[$line->LineNumber]['ProjectID']          = $line->ProjectID;
        $lines[$line->LineNumber]['DepartmentID']       = $line->DepartmentID;
        $lines[$line->LineNumber]['EnableVacationPayment']  = $line->EnableVacationPayment;
        $lines[$line->LineNumber]['SalaryCode']  		= $line->SalaryCode;
      }

      ################################################################
      #Get employee spesific lines
      #This overrides default / hovedmal lines with the same line number.
      $query_salary_employee    = "select * from salaryconfline where SalaryConfID = '$SalaryConfID'";
      if($debug) print "$query_salary_employee<br>\n";
      $result_salary_employee   = $_lib['db']->db_query($query_salary_employee);

      while($line = $_lib['db']->db_fetch_object($result_salary_employee)) {
        if(!$line->Active)
        {
          unset($lines[$line->LineNumber]);
          continue;
        }
        $lines[$line->LineNumber]['AccountPlanID']      = $line->AccountPlanID;
        $lines[$line->LineNumber]['NumberInPeriod']     = $line->NumberInPeriod;
        $lines[$line->LineNumber]['Rate']               = $line->Rate;
        $lines[$line->LineNumber]['SalaryText']         = $line->SalaryText;
        $lines[$line->LineNumber]['AmountThisPeriod']   = $line->AmountThisPeriod;
        $lines[$line->LineNumber]['EnableEmployeeTax']  = $line->EnableEmployeeTax;
        $lines[$line->LineNumber]['SalaryID']           = $SalaryID;
        $lines[$line->LineNumber]['ProjectID']          = $line->ProjectID;
        $lines[$line->LineNumber]['DepartmentID']       = $line->DepartmentID;
        $lines[$line->LineNumber]['EnableVacationPayment']  = $line->EnableVacationPayment;
        #$lines[$line->LineNumber]['SalaryCode']  			= $line->SalaryCode;
      }

      ################################################################
      #Insert salary lines in database

      $postsub['salaryline_SalaryID'] = $SalaryID;

      foreach ($lines as $linenum => $field)
      {
        if($lines[$linenum]['EnableEmployeeTax'] == 1)
        {
            $query = "select a.Percent from arbeidsgiveravgift as a, kommune as k, accountplan as ap where ap.AccountPlanID=$head_employee->AccountPlanID and ap.KommuneID=k.KommuneID and a.Code=k.Sone";
            if($debug) print "$query<br>\n";
            $arbavgift = $_lib['storage']->get_row(array('query' => $query));
            $lines[$linenum]['EmployeeTax'] = $arbavgift->Percent;
        }
        #print "linenum: $linenum : text : " . $lines[$linenum]['SalaryText'] . "<br>";
        $postsub['salaryline_LineNumber']        = $linenum;
        $postsub['salaryline_AccountPlanID']     = $lines[$linenum]['AccountPlanID'];
        $postsub['salaryline_NumberInPeriod']    = $lines[$linenum]['NumberInPeriod'];
        $postsub['salaryline_Rate']              = $lines[$linenum]['Rate'];
        $postsub['salaryline_SalaryText']        = $lines[$linenum]['SalaryText'];
        $postsub['salaryline_AmountThisPeriod']  = $lines[$linenum]['AmountThisPeriod'];
        $postsub['salaryline_EnableEmployeeTax'] = $lines[$linenum]['EnableEmployeeTax'];
        $postsub['salaryline_ProjectID']         = $lines[$linenum]['ProjectID'];
        $postsub['salaryline_DepartmentID']      = $lines[$linenum]['DepartmentID'];
        $postsub['salaryline_EnableVacationPayment'] = $lines[$linenum]['EnableVacationPayment'];
        $postsub['salaryline_EmployeeTax']     	= $lines[$linenum]['EmployeeTax'];
        $postsub['salaryline_SalaryCode']       = $lines[$linenum]['SalaryCode'];
        if($debug) print_r($postsub);

        $_lib['storage']->db_new_hash($postsub, 'salaryline');
      }
  }
}
elseif($_lib['input']->getProperty('action_salary_delete'))
{
  $query="select JournalID, Period, JournalDate from salary where SalaryID=".$_lib['input']->getProperty('SalaryID');
  $row_salary = $_lib['storage']->get_row(array('query' => $query));

  includelogic('postmotpost/postmotpost');
  $postmotpost = new postmotpost(array());
  $postmotpost->openPostJournal($row_salary->JournalID);

  $VoucherPeriod    = $row_salary->Period;
  $VoucherDate      = $row_salary->JournalDate;

  $accounting->delete_journal($row_salary->JournalID, $VoucherType);

    $post = array();
    $post['voucher_VoucherPeriod'] = $VoucherPeriod;
    $post['voucher_VoucherDate']   = $VoucherDate;
    if(strlen($VoucherPeriod)>0)
    {
        //print_r($post); print $VoucherType;
        $accounting->set_journal_motkonto(array('post'=>$post, 'VoucherType'=>$VoucherType));
    }

  $primarykey2['SalaryID'] = $_lib['input']->getProperty('SalaryID');
  #Possible to extend or alter parameters here
  $_lib['storage']->db_delete_hash('salary',     $primarykey2);
  $_lib['storage']->db_delete_hash('salaryline', $primarykey2);
}
elseif($_lib['input']->getProperty('action_salaryconf_new'))
{
  #Has to require_once department id?
  #Possible to extend or alter parameters here
  $_POST['salaryconf_CreatedByPersonID'] = $login_id;
  $SalaryConfID = $_lib['storage']->db_new_hash($_POST, 'salaryconf');

  ################################################################
  #Get default / hovedmal configuration lines
  $query_salary_default     = "select * from salaryconfline where SalaryConfID = '1'";
  $result_salary_default    = $_lib['db']->db_query($query_salary_default);

  $lines = array();
  while($line = $_lib['db']->db_fetch_object($result_salary_default))
  {
    $lines[$line->LineNumber]['AccountPlanID']      	= $line->AccountPlanID;
    $lines[$line->LineNumber]['NumberInPeriod']     	= $line->NumberInPeriod;
    $lines[$line->LineNumber]['Rate']               	= $line->Rate;
    $lines[$line->LineNumber]['SalaryText']         	= $line->SalaryText;
    $lines[$line->LineNumber]['AmountThisPeriod']   	= $line->AmountThisPeriod;
    $lines[$line->LineNumber]['EnableEmployeeTax']  	= $line->EnableEmployeeTax;
    $lines[$line->LineNumber]['SalaryID']           	= $SalaryID;
    $lines[$line->LineNumber]['ProjectID']          	= $line->ProjectID;
    $lines[$line->LineNumber]['DepartmentID']       	= $line->DepartmentID;
    $lines[$line->LineNumber]['EnableVacationPayment'] 	= $line->EnableVacationPayment;
    $lines[$line->LineNumber]['SalaryCode'] 			= $line->SalaryCode;
  }

  ################################################################
  #Insert salary lines in database

  $postsub['salaryconfline_SalaryConfID'] = $SalaryConfID;

  foreach ($lines as $linenum => $field)
  {
    #print "linenum: $linenum : text : " . $lines[$linenum]['SalaryText'] . "<br>";
    $postsub['salaryconfline_LineNumber']        	= $linenum;
    $postsub['salaryconfline_AccountPlanID']     	= $lines[$linenum]['AccountPlanID'];
    $postsub['salaryconfline_NumberInPeriod']    	= $lines[$linenum]['NumberInPeriod'];
    $postsub['salaryconfline_Rate']              	= $lines[$linenum]['Rate'];
    $postsub['salaryconfline_SalaryText']        	= $lines[$linenum]['SalaryText'];
    $postsub['salaryconfline_AmountThisPeriod']  	= $lines[$linenum]['AmountThisPeriod'];
    $postsub['salaryconfline_EnableEmployeeTax'] 	= $lines[$linenum]['EnableEmployeeTax'];
    $postsub['salaryconfline_ProjectID']         	= $lines[$linenum]['ProjectID'];
    $postsub['salaryconfline_DepartmentID']      	= $lines[$linenum]['DepartmentID'];
    $postsub['salaryconfline_EnableVacationPayment'] = $lines[$linenum]['EnableVacationPayment'];
 	$postsub['salaryconfline_SalaryCode']      		= $lines[$linenum]['SalaryCode'];

    $_lib['storage']->db_new_hash($postsub, 'salaryconfline');
  }

}
elseif($_lib['input']->getProperty('action_salaryconf_update'))
{
  $lagre = true;

  ############################
  # Hvis det er hovedmal
  if($_POST['SalaryConfID'] == 1)
  {
    $counter = $_POST['row_count'];
    for($i=0; $i<$counter; $i++)
    {
      if($_POST['salaryconfline_LineNumber_'.$_POST[$i]] < 10 or $_POST['salaryconfline_LineNumber_'.$_POST[$i]] > 100)
      {
          ######################
          # stopper lagring av mal og setter feilmelding
          $lagre = false;
          //unset($_POST['salaryconfline_LineNumber_'.$_POST[$i]]);
          $message .= "<font color=\"red\">Mal ble ikke lagret<br>Du kan ikke velge verdi mindre enn 10 eller større enn 100</font><br>";

          $query_delete = "delete from salaryconfline where SalaryConfLineID=".$_POST[$i];
          //print($query_delete);
          $_lib['db']->db_query($query_delete);
      }
      unset($_POST[$i]);
    }
    unset($_POST['row_count']);
  }

  ############################
  # Hvis det er en delmal
  if($_POST['SalaryConfID'] != 1)
  {
    $query = "select SalaryConfID from salaryconf where AccountPlanID='".$_REQUEST['salaryconf_AccountPlanID_'.$_REQUEST['SalaryConfID']]."' and SalaryConfID!=1";
    $salary = $_lib['storage']->get_row(array('query' => $query));
    if(isset($salary->SalaryConfID) and strlen($salary->SalaryConfID)>0 and $salary->SalaryConfID!=$_lib['input']->getProperty('SalaryConfID'))
    {
        $lagre = false;
        $message .= "<font color=\"red\">Mal ble ikke lagret<br>Du kan ikke opprette flere enn en mal pr ansatt</font><br>";
    }
    //print $query;
    //print_r($_POST);
  }

  if($lagre)
  {
    $tables['salaryconf']         = 'SalaryConfID';
    $tables['salaryconfline']     = 'SalaryConfLineID';

    $_lib['storage']->db_update_multi_table($_POST, $tables);
  }
}
elseif($_lib['input']->getProperty('action_salary_lock'))
{
  $query = sprintf("UPDATE salary SET LockedBy = '%s %s', LockedDate = NOW() WHERE SalaryID = %d LIMIT 1", 
			$_lib['sess']->get_person('FirstName'), $_lib['sess']->get_person('LastName'), $SalaryID);
  $_lib['db']->db_query($query);
}
elseif($_lib['input']->getProperty('action_salary_journal'))
{
  ################################################################
  #First save the content of the forms
  $counter = $_POST['fieldcount'];

  $tables['salary']     = 'SalaryID';
  $tables['salaryline'] = 'SalaryLineID';

  if( ($_POST['salary_DomesticBankAccount_'.$SalaryID] == 0) || ($_POST['salary_DomesticBankAccount_'.$SalaryID] == "") )
  {
    $query="select DomesticBankAccount from accountplan where AccountPlanID=".$_POST['AccountPlanID'];
    $row = $_lib['storage']->get_row(array('query' => $query));
    $_POST['salary_DomesticBankAccount_'.$SalaryID] = $row->DomesticBankAccount;

    $_lib['sess']->debug($query);
  }

  $accountplanid = $_POST['AccountPlanID'];
  unset($_POST['AccountPlanID']);

  $sumTotal = 0;
  for($i=1; $i<=$counter; $i++)
  {
      $lineID = $_POST[$i];
      if( ($_POST['salaryline_AmountThisPeriod_'.$lineID] == "") || ($_POST['salaryline_AmountThisPeriod_'.$lineID] == 0) )
      {
          $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_NumberInPeriod_'.$lineID]));
          $tmpvalue1 = $hash['value'];

          $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_Rate_'.$lineID]));
          $tmpvalue2 = $hash['value'];

          $_POST['salaryline_AmountThisPeriod_'.$lineID] = ($tmpvalue1 * $tmpvalue2);
      }
      elseif($_POST['salaryline_AmountThisPeriod_'.$lineID] != ($_POST['salaryline_NumberInPeriod_'.$lineID] * $_POST['salaryline_Rate_'.$lineID]) )
      {
          $message = "<font color=\"red\">OBS! Antall * Sats er ikke lik Totalen</font>";
      }

      $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_AmountThisPeriod_'.$lineID]));
      $_POST['salaryline_AmountThisPeriod_'.$lineID] = $hash['value'];

      if( ($_POST['salaryline_LineNumber_'.$lineID] >= $lineInFrom) and ($_POST['salaryline_LineNumber_'.$lineID] < $lineInTo) )
      {
        $sumTotal += $_POST['salaryline_AmountThisPeriod_'.$lineID];
      }
      elseif( ($_POST['salaryline_LineNumber_'.$lineID] >= $lineOutFrom) and ($_POST['salaryline_LineNumber_'.$lineID] < $lineOutTo) )
      {
        $sumTotal -= $_POST['salaryline_AmountThisPeriod_'.$lineID];
      }
      if($_POST['salaryline_LineNumber_'.$lineID] == 100)
      {
        $_POST['salaryline_AccountPlanID_'.$lineID] = $_POST['salary_AccountPlanID_'.$_POST['SalaryID']];
      }

	  if($_POST['EnableVacationPayment_'.$lineID] == 1) {
	  	#print "Feriepengerja<br>";
	  	$VacationPayment += $_POST['salaryline_AmountThisPeriod_'.$lineID];
	  }

      /*$query = "select sum(AmountThisPeriod) as sum from salaryline where SalaryID=".$_REQUEST['SalaryID']." and LineNumber=".$_POST['salaryline_LineNumber_'.$lineID];
      $sum = $_lib['storage']->get_row(array('query' => $query));
      $_POST['salaryline_AmountThisYear_'.$lineID] = $sum->sum;*/

      unset($_POST[$i]);
  }
  unset($_POST['fieldcount']);

  #$_POST['salary_VacationPayment_'.$SalaryID]           = $VacationPayment; 
  $_POST['salary_AmountThisPeriod_'.$_POST['SalaryID']] = $sumTotal;

  #print_r($_POST);
  $_lib['storage']->db_update_multi_table($_POST, $tables);

  ################################################################
  #Get had and lines
  $query_salary         = "select * from salary where SalaryID = '$SalaryID'";
  $result_salary        = $_lib['db']->db_query($query_salary);
  $salary               = $_lib['db']->db_fetch_object($result_salary);

  $query_salaryline     = "select * from salaryline where SalaryID = '$SalaryID'";
  $result_salaryline    = $_lib['db']->db_query($query_salaryline);

  ################################################################
  #Then calcultate the different lines hittil i Œr, and update the fields
  #Find first period this year
  if(!$salary->PayDate)
  {
    $_lib['sess']->error("Pay date is required");
    print "Pay date is required";
    exit;
  }
  /*$first_period = $_lib['date']->get_this_year($salary->PayDate) . "-01";
  while($salaryline = $_lib['db']->db_fetch_object($result_salaryline))
  {
    $query_account  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where AccountPlanID = '$salaryline->AccountPlanID' and Description = '$salary->AccountPlanID' and VoucherPeriod >= '$first_period' and VoucherPeriod <= '$salary->Period' group by AccountPlanID";
    #print "$query_account<br>";
    $result_account = $_lib['db']->db_query($query_account);
    $account        = $_lib['db']->db_fetch_object($result_account);

    $pksalary['SalaryLineID']                  = $salaryline->SalaryLineID;
    $fieldssalary['salaryline_AmountThisYear'] = abs($account->sumin - $account->sumout);
    //print_r($fieldssalary);
    //print_r($pksalary);
    $_lib['db']->db_update_hash($fieldssalary, 'salaryline', $pksalary);
  }*/

  /* start accounting */

    #print "Bilagsnummer: $salary->JournalID<br>\n";

    ################################################################
    #Get or update journal id
    $VoucherType = 'L';
    if($salary->JournalID)
    {
        includelogic('postmotpost/postmotpost');
        $postmotpost = new postmotpost(array());
        $postmotpost->openPostJournal($salary->JournalID, $VoucherType);
    
        #Delete old accounting and insert new
        $accounting->delete_journal($salary->JournalID, $VoucherType);
    }
    /*else
    {
      #Get a new available journal id
      list($JournalID, $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $VoucherType));
  
      #Update journal id on salary
      $pk['SalaryID']                     = $SalaryID;
      $salaryfield['salary_JournalID']    = $JournalID;
      $_lib['storage']->db_update_hash($salaryfield, 'salary', $pk);
    }*/
  
    ################################################################
    #Insert salary lines as voucher for this employee
    $result_salaryline    = $_lib['db']->db_query($query_salaryline);
  
  $fields = array();
  $fields['voucher_JournalID']       = $salary->JournalID;
  $fields['voucher_Reference']       = $salary->JournalID;
  $fields['voucher_VoucherPeriod']   = $salary->Period;
  $fields['voucher_VoucherDate']     = $salary->JournalDate;
  $fields['voucher_DueDate']         = $salary->PayDate;
  $fields['voucher_Active']          = 1;
  $fields['voucher_AutomaticReason'] = "L&oslash;nnsslipp: $SalaryID, Ansatt: $salary->AccountPlanID ";
  $fields['voucher_Description']     = 'Periode: ' . $salary->Period . ' ansatt: ' . $salary->AccountPlanID;
  $fields['voucher_InvoiceID']       = $VoucherType . $salary->JournalID;

  unset($fields['voucher_AmountIn']);
  unset($fields['voucher_AmountOut']);

  if($sumTotal >= 0)
    $fields['voucher_AmountIn'] = $sumTotal;
  else
    $fields['voucher_AmountOut'] = $sumTotal;

  //print_r($fields);

  $VoucherID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$salary->AccountPlanID, 'type'=>'reskontro', 'VoucherType'=>$VoucherType));

  #Update hovedbokskonto for this postering, it may have changed
  $tmp = $fields['voucher_AmountIn'];
  $fields['voucher_AmountIn'] 		= $fields['voucher_AmountOut'];
  $fields['voucher_AmountOut']		= $tmp;
  $fields['voucher_VoucherType'] 	= $VoucherType;
  #$accounting->voucher_to_hovedbok_auto($salary->AccountPlanID, $fields, $VoucherID);

  #print_r($salary);
  #print_r($fields);
  unset($fields['voucher_AmountIn']);
  unset($fields['voucher_AmountOut']);

  while($salaryline = $_lib['db']->db_fetch_object($result_salaryline))
  {
    unset($fields['voucher_AmountIn']);
    unset($fields['voucher_AmountOut']);
    # DO not isernt poseringer with 0 value
    if($salaryline->AmountThisPeriod != 0)
    {
      if( ($salaryline->LineNumber >= $lineInFrom) and ($salaryline->LineNumber < $lineInTo) )
      {
        $fields['voucher_AmountIn'] 	= $salaryline->AmountThisPeriod;
      }
      elseif( ($salaryline->LineNumber >= $lineOutFrom) and ($salaryline->LineNumber < $lineOutTo) )
      {
        $fields['voucher_AmountOut'] 	= $salaryline->AmountThisPeriod;
      }
      $fields['voucher_ProjectID']      = $salaryline->ProjectID;
      $fields['voucher_DepartmentID']   = $salaryline->DepartmentID;
      $account = $salaryline->AccountPlanID;
      #print "insert voucherline<br>\n";
      $tmpID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$account, 'VoucherType'=>$VoucherType));

      #Update hovedbokskonto for this postering, it may have changed
      #$fields['voucher_VoucherType'] = $VoucherType;
      #$accounting->voucher_to_hovedbok_auto($account, $fields, $tmpID);
    }
  }

  $fields['voucher_AccountPlanID'] = $account;

  #print "insert voucherline5<br>\n";
  $accounting->correct_journal_balance($fields, $salary->JournalID, $VoucherType);

  #print "insert voucherline10<br>\n";
  $accounting->set_journal_motkonto(array('post'=>$fields, 'VoucherType'=>$VoucherType));
}


elseif($_lib['input']->getProperty('action_salaryline_delete'))
{
    $pk['SalaryLineID']=$_lib['input']->getProperty('SalaryLineID');
    //print_r($pk);
    $_lib['storage']->db_delete_hash('salaryline', $pk);
}
elseif($_lib['input']->getProperty('action_salaryconfline_delete'))
{
    $pk['SalaryConfLineID']=$_lib['input']->getProperty('SalaryConfLineID');
    //print_r($_REQUEST);
    $_lib['storage']->db_delete_hash('salaryconfline', $pk);
}
elseif($_lib['input']->getProperty('action_salaryconf_delete'))
{
    $pk['SalaryConfID']=$_lib['input']->getProperty('SalaryConfID');
    $_lib['storage']->db_delete_hash('salaryconfline', $pk);
    $_lib['storage']->db_delete_hash('salaryconf', $pk);
}
elseif($_lib['input']->getProperty('action_salarysubconf_enter'))
{
    $query = "select * from salaryconfline where SalaryConfID=1";
    $confhash = $_lib['storage']->get_hashhash(array('query'=>$query, 'key'=>'LineNumber'));
    //print_r($confhash);

    $query = "select * from salaryconfline where SalaryConfID=$SalaryConfID";
    $salaryhash = $_lib['storage']->get_hashhash(array('query'=>$query, 'key'=>'LineNumber'));
    //print_r($salaryhash);

    foreach($confhash as $key => $conflinehash)
    {
        if(isset($salaryhash[$key]))
        {
            //print $key." eksisterer<br>";
        }
        else
        {
            $fields = array();
            $fields['salaryconfline_SalaryConfID']          = $SalaryConfID;
            $fields['salaryconfline_LineNumber']            = $conflinehash['LineNumber'];
            $fields['salaryconfline_SalaryText']            = $conflinehash['SalaryText'];
            $fields['salaryconfline_NumberInPeriod']        = $conflinehash['NumberInPeriod'];
            $fields['salaryconfline_Rate']                  = $conflinehash['Rate'];
            $fields['salaryconfline_AmountThisPeriod']      = $conflinehash['AmountThisPeriod'];
            $fields['salaryconfline_AccountPlanID']         = $conflinehash['AccountPlanID'];
            $fields['salaryconfline_EnableEmployeeTax']     = $conflinehash['EnableEmployeeTax'];
            $fields['salaryconfline_ProjectID']             = $conflinehash['Project ID'];
            $fields['salaryconfline_DepartmentID']          = $conflinehash['DepartmentID'];
            $fields['salaryconfline_SalaryCode']            = $conflinehash['SalaryCode'];
            $fields['salaryconfline_EnableVacationPayment'] = $conflinehash['EnableVacationPayment'];
            //print_r($fields);

            $_lib['storage']->db_new_hash($fields, 'salaryconfline');

            #print "Lagt til linje nr ".$SalaryConfID."<br>\n";
        }
    }
    foreach($salaryhash as $key => $salarylinehash)
    {
        if(isset($confhash[$key]))
        {
            //print $key." eksisterer<br>";
        }
        else
        {
            $pk = array();
            $pk['SalaryConfLineID']=$salarylinehash['SalaryConfLineID'];
            $_lib['storage']->db_delete_hash('salaryconfline', $pk);

            print "Slettet linje nr ".$salarylinehash['SalaryConfLineID']."<br>\n";
        }
    }
}
elseif($_lib['input']->getProperty('action_salary_updatetemplatecode')) {
  #Get code and holiday money from hovedmal
  $SalaryConfID = $_lib['input']->getProperty('SalaryConfID');
  
  $query 	= "select LineNumber, SalaryCode, EnableVacationPayment, EnableEmployeeTax, SalaryText from salaryconfline where SalaryConfID=1";
  $codehash = $_lib['storage']->get_hashhash(array('query'=>$query, 'key'=>'LineNumber'));
  #print_r($codehash);
  
  foreach($codehash as $line => $row) {
    $sql_update = "update salaryconfline set SalaryCode='" . $row['SalaryCode'] . "', EnableVacationPayment='" . $row['EnableVacationPayment'] . "', EnableEmployeeTax='" . $row['EnableEmployeeTax'] . "', SalaryText='" . $row['SalaryText'] . "' where SalaryConfID='$SalaryConfID' and LineNumber='" . $line . "'";
    #print "$sql_update<br>\n";
    $_lib['db']->db_update($sql_update);
  }
}
elseif($_lib['input']->getProperty('action_salary_updatesalarycode')) {
    #print_r($_REQUEST);
    $SalaryID 	    = $_lib['input']->getProperty('SalaryID');
    $SalaryConfID   = $_lib['input']->getProperty('SalaryConfID');
    
    $query 	= "select LineNumber, SalaryCode, EnableVacationPayment, EnableEmployeeTax, SalaryText, AccountPlanID from salaryconfline where SalaryConfID='$SalaryConfID'";
    #print $query;
    $codehash = $_lib['storage']->get_hashhash(array('query'=>$query, 'key'=>'LineNumber'));
    #print_r($codehash);
    
    foreach($codehash as $line => $row) {
        $query_sjekk 	= "select LineNumber from salaryline where SalaryID='$SalaryID' and LineNumber='" . $line . "';";
        $orgConfLine = $_lib['storage']->get_row(array('query' => $query_sjekk));
        if ($orgConfLine->LineNumber == "") {
            #print "Lager ny linje";
            $sql_insert = "insert into salaryline (SalaryLineID, SalaryID, LineNumber) values(NULL, '$SalaryID', '" . $line . "');";
            #print "$sql_insert<br>\n";
            $_lib['db']->db_update($sql_insert);          
        }
        $sql_update = "update salaryline set SalaryText='" . $row['SalaryText'] . "', SalaryCode='" . $row['SalaryCode'] . "', EnableVacationPayment='" . $row['EnableVacationPayment'] . "', EnableEmployeeTax='" . $row['EnableEmployeeTax'] . "', AccountPlanID='" . $row['AccountPlanID'] . "' where SalaryID='$SalaryID' and LineNumber='" . $line . "'";
        #print "$sql_update<br>\n";
        $_lib['db']->db_update($sql_update);
    }
} elseif($_lib['input']->getProperty('action_salary_fakturabanksend')) {
    if($_lib['sess']->get_person('FakturabankExportPaycheckAccess')) {
        includelogic('fakturabank/fakturabanksalary');
        $fb = new lodo_fakturabank_fakturabanksalary();

        $fb->sendsalary($SalaryID, $SalaryConfID);
        //        print "<h1>Feature is not available yet</h1>";
        
    } // else TODO later: handle illegal access attempt
}
?>
