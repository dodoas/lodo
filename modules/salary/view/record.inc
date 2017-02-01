<?

function salary_journal($SalaryID) {
  global $_lib;
  global $accounting;
  global $lineInFrom;
  global $lineInTo;
  global $lineOutFrom;
  global $lineOutTo;

  ################################################################
  #Get head and lines
  $query_salary         = "select * from salary where SalaryID = '$SalaryID'";
  $result_salary        = $_lib['db']->db_query($query_salary);
  $salary               = $_lib['db']->db_fetch_object($result_salary);

  $query_salaryline     = "select * from salaryline where SalaryID = '$SalaryID'";
  $result_salaryline    = $_lib['db']->db_query($query_salaryline);

  $query_salaryline_sumtotal = "SELECT SUM( IF(LineNumber >= $lineInFrom AND LineNumber < $lineInTo, AmountThisPeriod, IF( LineNumber >= $lineOutFrom AND LineNumber <= $lineOutTo, -AmountThisPeriod, 0)) ) as sumTotal FROM salaryline WHERE SalaryID = '$SalaryID'";
  $result_salaryline_sumtotal = $_lib['db']->db_query($query_salaryline_sumtotal);

  ################################################################
  #Then calcultate the different lines hittil i Œr, and update the fields
  #Find first period this year
  if(!$salary->PayDate)
  {
    $_lib['sess']->error("Pay date is required");
    print "Pay date is required";
    exit;
  }

  /* start accounting */

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
  $fields['voucher_VoucherType']     = $VoucherType;

  unset($fields['voucher_AmountIn']);
  unset($fields['voucher_AmountOut']);

  $sumTotal = $_lib['db']->db_fetch_object($result_salaryline_sumtotal)->sumTotal;

  if($sumTotal >= 0)
    $fields['voucher_AmountIn'] = $sumTotal;
  else
    $fields['voucher_AmountOut'] = -$sumTotal;

  $VoucherID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$salary->AccountPlanID, 'type'=>'reskontro', 'VoucherType'=>$VoucherType));

  while($salaryline = $_lib['db']->db_fetch_object($result_salaryline))
  {
    unset($fields['voucher_AmountIn']);
    unset($fields['voucher_AmountOut']);
    # DO not isernt poseringer with 0 value
    if($salaryline->AmountThisPeriod != 0)
    {
      if( ($salaryline->LineNumber >= $lineInFrom) and ($salaryline->LineNumber < $lineInTo) )
      {
        $fields['voucher_AmountIn']   = $salaryline->AmountThisPeriod;
      }
      elseif( ($salaryline->LineNumber >= $lineOutFrom) and ($salaryline->LineNumber <= $lineOutTo) )
      {
        $fields['voucher_AmountOut']  = $salaryline->AmountThisPeriod;
      }
      $fields['voucher_ProjectID']      = $salaryline->ProjectID;
      $fields['voucher_DepartmentID']   = $salaryline->DepartmentID;
      $account = $salaryline->AccountPlanID;
      if ($salaryline->LineNumber == 100) $fields['voucher_InvoiceID'] .= "-F";

      $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$account, 'VoucherType'=>$VoucherType));
    }
  }

  $fields['voucher_AccountPlanID'] = $account;

  $accounting->correct_journal_balance($fields, $salary->JournalID, $VoucherType);
  $accounting->set_journal_motkonto(array('post'=>$fields, 'VoucherType'=>$VoucherType));
}

function salary_update($SalaryID, $SalaryConfID) {
  global $_lib;
  global $lineInFrom;
  global $lineInTo;
  global $lineOutFrom;
  global $lineOutTo;

  $SalaryLineIDs = array();
  $SalaryLinesCount = $_REQUEST["fieldcount"];
  for ($i = 1; $i <= $SalaryLinesCount; $i++) { $SalaryLineIDs[] = $_REQUEST[$i]; }

  if (!($_lib['input']->getProperty('action_salary_new')) && CheckIfAnythingChanged($SalaryID, $SalaryLineIDs)) {
    // set who updated and when if anything changed
    $_POST["salary_UpdatedBy_".$SalaryID] = $_lib['sess']->get_person('PersonID');
    $_POST["salary_UpdatedAt_".$SalaryID] = strftime("%F %T");

    // To show history of changed salaries on altinn salary page
    $update_query = "UPDATE
      altinnReport1salary AS ars
    JOIN
      altinnReport1 AS ar
      ON ars.AltinnReport1ID = ar.AltinnReport1ID
    SET
      Changed = '1'
    WHERE
      ReplacedByMeldindsID IS NULL AND
      SalaryId = '".$SalaryID."';";
    $_lib['db']->db_query($update_query);
  }
  if ($_lib['input']->getProperty('action_salary_new') || ($_lib['input']->getProperty('action_salary_journal') && CheckIfWorkRelationChanged($SalaryID, $_POST['salary_WorkRelationID_'. $SalaryID]))) {
    // set who updated altinn fields
    $_POST["salary_AltinnFieldsUpdatedBy_".$SalaryID] = $_lib['sess']->get_person('PersonID');
    $_POST["salary_AltinnFieldsUpdatedAt_".$SalaryID] = strftime("%F %T");
  }

  // if we are journaling a newly created salary we need to manualy make $_POST so same logic can be used
  if ($_lib['input']->getProperty('action_salary_new')) {
    $_POST['SalaryID'] = $SalaryID;

    $query="select * from salary where SalaryID=".$_POST['SalaryID'];
    $row = $_lib['storage']->get_row(array('query' => $query));
    $_POST['salary_JournalDate_'.$SalaryID] = $row->JournalDate;
    $_POST['salary_Period_'.$SalaryID] = $row->Period;
    $_POST['AccountPlanID'] = $_POST['salary_AccountPlanID_'.$SalaryID] = $row->AccountPlanID;
    $_POST['salary_ValidFrom_'.$SalaryID] = $row->ValidFrom;
    $_POST['salary_ValidTo_'.$SalaryID] = $row->ValidTo;
    $_POST['salary_PayDate_'.$SalaryID] = $row->PayDate;
    $_POST['salary_DomesticBankAccount_'.$SalaryID] = $row->DomesticBankAccount;
    $_POST['salary_Comment_'.$SalaryID] = $row->Comment;
    $_POST['salary_InternComment_'.$SalaryID] = $row->InternComment;
    $_POST['salary_VacationPayment_'.$SalaryID] = $row->VacationPayment;
    // set who updated salary
    $_POST["salary_UpdatedBy_".$SalaryID] = $_lib['sess']->get_person('PersonID');
    $_POST["salary_UpdatedAt_".$SalaryID] = strftime("%F %T");

    $query="select count(*) as fieldcount from salaryline where SalaryID=".$_POST['SalaryID'];
    $row = $_lib['storage']->get_row(array('query' => $query));
    $_POST['fieldcount'] = $row->fieldcount;

    $query="select * from salaryline where SalaryID=".$_POST['SalaryID'];
    $result=$_lib['db']->db_query($query);
    $icount = 0;
    while ($row = $_lib['db']->db_fetch_object($result)) {
      $i++;
      $_POST[$i] = $row->SalaryLineID;
      $_POST['salaryline_LineNumber_'.$row->SalaryLineID] = $row->LineNumber;
      $_POST['salaryline_NumberInPeriod_'.$row->SalaryLineID] = $row->NumberInPeriod;
      $_POST['salaryline_Rate_'.$row->SalaryLineID] = $row->Rate;
      $_POST['salaryline_AmountThisPeriod_'.$row->SalaryLineID] = $row->AmountThisPeriod;
      $_POST['EnableVacationPayment_'.$row->SalaryLineID] = $row->EnableVacationPayment;
      $_POST['EnableEmployeeTax_'.$row->SalaryLineID] = $row->EnableEmployeeTax;
      $_POST['MandatoryTaxSubtraction_'.$row->SalaryLineID] = $row->MandatoryTaxSubtraction;
      $_POST['SendToAltinn_'.$row->SalaryLineID] = $row->SendToAltinn;
      $_POST['Fordel_'.$row->SalaryLineID] = $row->Fordel;
    }
  }
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
    $AmountThisPeriod = $_POST['salaryline_AmountThisPeriod_'.$lineID];
    $ValueHash = $_lib['convert']->Amount(array('value'=>$AmountThisPeriod));
    $AmountThisPeriod = $ValueHash['value'];
    $NumberInPeriod = $_POST['salaryline_NumberInPeriod_'.$lineID];
    $ValueHash = $_lib['convert']->Amount(array('value'=>$NumberInPeriod));
    $NumberInPeriod = $ValueHash['value'];
    $Rate = $_POST['salaryline_Rate_'.$lineID];
    $ValueHash = $_lib['convert']->Amount(array('value'=>$Rate));
    $Rate = $ValueHash['value'];
    if( ($_POST['salaryline_AmountThisPeriod_'.$lineID] == "") || ($_POST['salaryline_AmountThisPeriod_'.$lineID] == 0) )
    {
        $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_NumberInPeriod_'.$lineID]));
        $tmpvalue1 = $hash['value'];

        $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_Rate_'.$lineID]));
        $tmpvalue2 = $hash['value'];

        $_POST['salaryline_AmountThisPeriod_'.$lineID] = ($tmpvalue1 * $tmpvalue2);
    }
    elseif(($AmountThisPeriod != ($NumberInPeriod * $Rate) && !isset($_POST['floor_'.$lineID])) || (isset($_POST['floor_'.$lineID]) && (floor($AmountThisPeriod) != floor($NumberInPeriod * $Rate))))
    {
        $message = "<font color=\"red\">OBS! Feil utregning.</font>";
    }

    $hash = $_lib['convert']->Amount(array('value'=>$_POST['salaryline_AmountThisPeriod_'.$lineID]));
    $_POST['salaryline_AmountThisPeriod_'.$lineID] = (isset($_POST['floor_'.$lineID])) ? floor($hash['value']) : $hash['value'];

    if( ($_POST['salaryline_LineNumber_'.$lineID] >= $lineInFrom) and ($_POST['salaryline_LineNumber_'.$lineID] < $lineInTo) )
    {
      $sumTotal += $_POST['salaryline_AmountThisPeriod_'.$lineID];
    }
    elseif( ($_POST['salaryline_LineNumber_'.$lineID] >= $lineOutFrom) and ($_POST['salaryline_LineNumber_'.$lineID] <= $lineOutTo) )
    {
      $sumTotal -= $_POST['salaryline_AmountThisPeriod_'.$lineID];
    }
    if($_POST['salaryline_LineNumber_'.$lineID] == 100)
    {
      $_POST['salaryline_AccountPlanID_'.$lineID] = $_POST['salary_AccountPlanID_'.$_POST['SalaryID']];
    }

    if($_POST['EnableVacationPayment_'.$lineID] == 1) {
      $VacationPayment += $_POST['salaryline_AmountThisPeriod_'.$lineID];
    }

    unset($_POST[$i]);
  }
  unset($_POST['fieldcount']);

  $_POST['salary_AmountThisPeriod_'.$_POST['SalaryID']] = $sumTotal;

  if(CheckIfAccountPlanChanged($SalaryID)){
    // Update SalaryConfLine if AccountPlanID is updated
    $q = "SELECT * FROM salaryconf WHERE AccountPlanID = ". $_POST["salary_AccountPlanID_". $SalaryID]." LIMIT 1";
    $salaryConf = $_lib['storage']->get_row(array('query' => $q));
    $_POST["salary_SalaryConfID_". $SalaryID] = $salaryConf->SalaryConfID;
  }

  if (!$_lib['input']->getProperty('action_salary_new') && CheckIfWorkRelationChanged($SalaryID, $_POST['salary_WorkRelationID_'.$SalaryID])) {
    $work_relation_query = "SELECT * FROM workrelation WHERE WorkRelationID = '".$_POST['salary_WorkRelationID_'.$SalaryID]."'";
    $work_relation = $_lib['storage']->get_row(array('query' => $work_relation_query));
    $_POST['salary_ShiftType_'.$SalaryID]              = $work_relation->ShiftType;
    $_POST['salary_WorkTimeScheme_'.$SalaryID]         = $work_relation->WorkTimeScheme;
    $_POST['salary_TypeOfEmployment_'.$SalaryID]       = $work_relation->TypeOfEmployment;
    $_POST['salary_OccupationID_'.$SalaryID]           = $work_relation->OccupationID;
    $_POST['salary_SubcompanyID_'.$SalaryID]           = $work_relation->SubcompanyID;
    $_POST['salary_WorkStart_'.$SalaryID]              = $work_relation->WorkStart;
    $_POST['salary_WorkStop_'.$SalaryID]               = $work_relation->WorkStop;
    $_POST['salary_InCurrentPositionSince_'.$SalaryID] = $work_relation->InCurrentPositionSince;
    $_POST['salary_WorkPercent_'.$SalaryID]            = $work_relation->WorkPercent;
    $_POST['salary_WorkPercentUpdatedAt_'.$SalaryID]   = $work_relation->WorkPercentUpdatedAt;
    $_POST['salary_WorkMeasurement_'.$SalaryID]        = $work_relation->WorkMeasurement;
    $_POST['salary_SalaryDateChangedAt_'.$SalaryID]    = $work_relation->SalaryDateChangedAt;
  }

  $_lib['storage']->db_update_multi_table($_POST, $tables);
}

function CheckIfWorkRelationChanged($SalaryID, $WorkRelationID) {
  global $_lib;
  $Changed = false;
  $Salary = $_lib['storage']->get_row(array('query' => "SELECT * FROM salary WHERE SalaryID = " . $SalaryID));
  $WorkRelation = $_lib['storage']->get_row(array('query' => "SELECT * FROM workrelation WHERE WorkRelationID = '" . $WorkRelationID . "'"));
  // Check if anything work relation related changed
  $SalaryFieldNames = array('WorkRelationID', 'OccupationID', 'SubcompanyID', 'WorkStart',
                            'WorkStop', 'InCurrentPositionSince', 'WorkTimeScheme', 'ShiftType',
                            'TypeOfEmployment', 'WorkPercent', 'WorkPercentUpdatedAt',
                            'WorkMeasurement', 'SalaryDateChangedAt');
  foreach ($SalaryFieldNames as $FieldName) {
    if ($WorkRelation->{$FieldName} != $Salary->{$FieldName}) {
      $Changed = true;
      return $Changed;
    }
  }
  return $Changed;
}
function CheckIfAnythingChanged($SalaryID, $SalaryLineIDs) {
  global $_lib;
  $Changed = false;
  $Salary = $_lib['storage']->get_row(array('query' => "SELECT * FROM salary WHERE SalaryID = " . $SalaryID));
  // Check if anything changed
  $SalaryFieldNames = array("JournalDate" => false, "Period" => false, "AccountPlanID" => false, "ValidFrom" => false,
                            "ValidTo" => false, "PayDate" => false, "DomesticBankAccount" => false, "Comment" => false,
                            "VacationPayment" => false, "ActualPayDate" => false, "ShiftType" => false,
                            "WorkTimeScheme" => false, "TypeOfEmployment" => false, "OccupationID" => false,
                            "WorkRelationID" => false);
  foreach ($SalaryFieldNames as $FieldName => $IsAmount) {
    $ArgName = "salary_".$FieldName."_".$SalaryID;
    if ($IsAmount) {
      $ValueHash = $_lib['convert']->Amount(array('value'=>$_REQUEST[$ArgName]));
      $Value = $ValueHash['value'];
    }
    else $Value = $_REQUEST[$ArgName];
    if ($Value == $Salary->{$FieldName}) unset($_REQUEST[$ArgName]);
    else {
      $Changed = true;
      return $Changed;
    }
  }
  while ($SalaryLineID = array_pop($SalaryLineIDs)) {
    $SalaryLine = $_lib['storage']->get_row(array('query' => "SELECT * FROM salaryline WHERE SalaryLineID = " . $SalaryLineID));
    // Check if anything changed
    $SalaryLineFieldNames = array("NumberInPeriod" => true, "Rate" => true, "AmountThisPeriod" => true);
    foreach($SalaryLineFieldNames as $FieldName => $IsAmount) {
      $ArgName = "salaryline_".$FieldName."_".$SalaryLineID;
      if ($IsAmount) {
        $ValueHash = $_lib['convert']->Amount(array('value'=>$_REQUEST[$ArgName]));
        $Value = $ValueHash['value'];
      }
      else $Value = $_REQUEST[$ArgName];
      if ($Value == $SalaryLine->{$FieldName}) unset($_REQUEST[$ArgName]);
      else {
        $Changed = true;
        return $Changed;
      }
    }
  }
  return $Changed;
}

function CheckIfAccountPlanChanged($SalaryID) {
  global $_lib;
  $Salary = $_lib['storage']->get_row(array('query' => "SELECT * FROM salary WHERE SalaryID = " . $SalaryID));
  // Check if AccountPlanID change,

  return $_POST["salary_AccountPlanID_". $SalaryID] != $Salary->AccountPlanID;
}

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

  /* updated taxcard */
  $_lib['storage']->db_update_multi_table($_POST, 
                                          array(
                                              'reportedtaxcard' => 'ReportedTaxcardID'
                                              ));
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
   $fields['salaryconfline_SalaryDescription'] = $orgConfLine->SalaryDescription;
   $fields['salaryconfline_SendToAltinn']      = $orgConfLine->SendToAltinn;
   $fields['salaryconfline_Fordel']            = $orgConfLine->Fordel;
   $fields['salaryconfline_NumberInPeriod']    = $orgConfLine->NumberInPeriod;
   $fields['salaryconfline_Rate']              = $orgConfLine->Rate;
   $fields['salaryconfline_AmountThisPeriod']  = $orgConfLine->AmountThisPeriod;
   $fields['salaryconfline_AccountPlanID']     = $orgConfLine->AccountPlanID;
   $fields['salaryconfline_EnableEmployeeTax'] = $orgConfLine->EnableEmployeeTax;
   $fields['salaryconfline_MandatoryTaxSubtraction'] = $orgConfLine->MandatoryTaxSubtraction;
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
    $fields['salaryline_SalaryDescription'] = $orgLine->SalaryDescription;
    $fields['salaryline_SendToAltinn']      = $orgLine->SendToAltinn;
    $fields['salaryline_Fordel']            = $orgLine->Fordel;
    $fields['salaryline_NumberInPeriod']    = $orgLine->NumberInPeriod;
    $fields['salaryline_Rate']              = $orgLine->Rate;
    $fields['salaryline_AmountThisPeriod']  = $orgLine->AmountThisPeriod;
    $fields['salaryline_AmountThisYear']    = $orgLine->AmountThisYear;
    $fields['salaryline_AccountPlanID']     = $orgLine->AccountPlanID;
    $fields['salaryline_EnableEmployeeTax'] = $orgLine->EnableEmployeeTax;
    $fields['salaryline_MandatoryTaxSubtraction'] = $orgLine->MandatoryTaxSubtraction;
    $fields['salaryline_EnableVacationPayment'] = $orgLine->EnableVacationPayment;
    $fields['salaryline_ProjectID']         = $orgLine->ProjectID;
    $fields['salaryline_DepartmentID']      = $orgLine->DepartmentID;
    $fields['salaryline_EmployeeTax']       = $orgLine->EmployeeTax;
    $fields['salaryline_SalaryCode']        = $orgLine->SalaryCode;

    $_lib['storage']->db_new_hash($fields, 'salaryline');
    // set who updated and when, since we added a line
    $sql_update = "UPDATE salary SET UpdatedAt = NOW(), UpdatedBy = '" . $_lib['sess']->get_person('PersonID') . "' WHERE SalaryID = '$SalaryID'";
    $_lib['db']->db_update($sql_update);
    salary_journal();
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
    $query="select MAX(JournalID) as MaxJournalID from salary";
    $row_salary = $_lib['storage']->get_row(array('query' => $query));
    $JournalID = $row_salary->MaxJournalID + 1;
        $query_setup    = "select name, value from setup";
        $setup          = $_lib['storage']->get_hash(
            array('query' => $query_setup, 'key' => 'name', 'value' => 'value')
            );
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

        //################################################################
        //#Get configuration spesific to this employee - overrides hovedmal on same line numbers
        $query_head_employee  = "select * from salaryconf where SalaryConfID = '" . (int) $_lib['input']->getProperty('SalaryConfID') . "'";
        $result_head_employee = $_lib['db']->db_query($query_head_employee);
        $head_employee        = $_lib['db']->db_fetch_object($result_head_employee);
        $accountplan = $accounting->get_accountplan_object($head_employee->AccountPlanID);
        $work_relation_query = "SELECT * FROM workrelation WHERE AccountPlanID = '".$head_employee->AccountPlanID."' ORDER BY WorkRelationID DESC LIMIT 1";
        $work_relation = $_lib['storage']->get_row(array('query' => $work_relation_query));

        //#Possible to extend or alter parameters here
        $postmain['salary_JournalID']              = $JournalID;
        $postmain['salary_SalaryConfID']           = $SalaryConfID;
        $postmain['salary_AccountPlanID']          = $head_employee->AccountPlanID;
        $postmain['salary_ValidFrom']              = $fromDate;
        $postmain['salary_ValidTo']                = $toDate;
        $postmain['salary_JournalDate']            = $journalDate;
        $postmain['salary_Period']                 = $salary_period_conf->Period;  //$_lib['date']->get_this_period($journalDate);
        $postmain['salary_KommuneID']              = $accountplan->KommuneID;
        $postmain['salary_WorkRelationID']         = $work_relation->WorkRelationID;
        $postmain['salary_ShiftType']              = $work_relation->ShiftType;
        $postmain['salary_OccupationID']           = $work_relation->OccupationID;
        $postmain['salary_WorkTimeScheme']         = $work_relation->WorkTimeScheme;
        $postmain['salary_TypeOfEmployment']       = $work_relation->TypeOfEmployment;
        $postmain['salary_SubcompanyID']           = $work_relation->SubcompanyID;
        $postmain['salary_WorkStart']              = $work_relation->WorkStart;
        $postmain['salary_WorkStop']               = $work_relation->WorkStop;
        $postmain['salary_InCurrentPositionSince'] = $work_relation->InCurrentPositionSince;
        $postmain['salary_WorkPercent']            = $work_relation->WorkPercent;
        $postmain['salary_WorkPercentUpdatedAt']   = $work_relation->WorkPercentUpdatedAt;
        $postmain['salary_WorkMeasurement']        = $work_relation->WorkMeasurement;
        $postmain['salary_SalaryDateChangedAt']    = $work_relation->SalaryDateChangedAt;

        if($accountplan->EnableCredit == 1)
            $postmain['salary_PayDate']           = $_lib['date']->add_Days($journalDate, $accountplan->CreditDays);
        else
            $postmain['salary_PayDate']           = $journalDate;

        $postmain['salary_DomesticBankAccount'] = $accountplan->DomesticBankAccount;
        $postmain['salary_CreatedByPersonID']   = $_lib['sess']->get_person('PersonID');

        //#Insert salary head
        if($debug) print_r($postmain);

        $SalaryID = $_lib['storage']->db_new_hash($postmain, 'salary');

        $query_arb = "select a.Percent from kommune as k, arbeidsgiveravgift as a where a.Code=k.Sone";
        $arb = $_lib['storage']->get_row(array('query' => $query_arb));

        /*
          $postextra['salaryextra_SalaryID'] = $SalaryID;
          $postextra['salaryextra_AccountName'] = $accountplan->AccountName;
          $postextra['salaryextra_Address'] = $accountplan->Address;
          $postextra['salaryextra_City'] = $accountplan->City;
          $postextra['salaryextra_ZipCode'] = $accountplan->ZipCode;
          $postextra['salaryextra_SocietyNumber'] = $accountplan->SocietyNumber;
          $postextra['salaryextra_TabellTrekk'] = $accountplan->TabellTrekk;
          $postextra['salaryextra_ProsentTrekk'] = $accountplan->ProsentTrekk;
          $postextra['salaryextra_MunicipalityPercent'] = $arb->Percent;
          $_lib['storage']->db_new_hash($postextra, 'salaryextra');
        */

        //################################################################
        //#Get configuration hovedmal - used as a basis for salary
        $query_head_default   = "select * from salaryconf where SalaryConfID = '1'";
        $result_head_default  = $_lib['db']->db_query($query_head_default);
        $head_default         = $_lib['db']->db_fetch_object($result_head_default);

        //################################################################
        //#Get default / hovedmal configuration lines
        $query_salary_default     = "select * from salaryconfline where SalaryConfID = '1' and Active=1";
        if($debug) print "$query_salary_default<br>\n";
        $result_salary_default    = $_lib['db']->db_query($query_salary_default);

        $lines = array();
        while($line = $_lib['db']->db_fetch_object($result_salary_default)) {
            $lines[$line->LineNumber]['AccountPlanID']      = $line->AccountPlanID;
            $lines[$line->LineNumber]['NumberInPeriod']     = $line->NumberInPeriod;
            $lines[$line->LineNumber]['Rate']               = $line->Rate;
            $lines[$line->LineNumber]['SalaryText']         = $line->SalaryText;
            $lines[$line->LineNumber]['SalaryDescription']  = $line->SalaryDescription;
            $lines[$line->LineNumber]['SendToAltinn']       = $line->SendToAltinn;
            $lines[$line->LineNumber]['Fordel']             = $line->Fordel;
            $lines[$line->LineNumber]['AmountThisPeriod']   = $line->AmountThisPeriod;
            $lines[$line->LineNumber]['EnableEmployeeTax']  = $line->EnableEmployeeTax;
            $lines[$line->LineNumber]['MandatoryTaxSubtraction'] = $line->MandatoryTaxSubtraction;
            $lines[$line->LineNumber]['SalaryID']           = $SalaryID;
            $lines[$line->LineNumber]['ProjectID']          = $line->ProjectID;
            $lines[$line->LineNumber]['DepartmentID']       = $line->DepartmentID;
            $lines[$line->LineNumber]['EnableVacationPayment']  = $line->EnableVacationPayment;
            $lines[$line->LineNumber]['SalaryCode']  		= $line->SalaryCode;
        }

        //################################################################
        //#Get employee spesific lines
        //#This overrides default / hovedmal lines with the same line number.
        $query_salary_employee    = "select * from salaryconfline where SalaryConfID = '$SalaryConfID' order by Active ASC";
        if($debug) print "$query_salary_employee<br>\n";
        $result_salary_employee   = $_lib['db']->db_query($query_salary_employee);

        while($line = $_lib['db']->db_fetch_object($result_salary_employee)) {
            if(!$line->Active && !isset($lines[$line->LineNumber]['MultipleLines']))
            {
                unset($lines[$line->LineNumber]);
                continue;
            }
            $lines[$line->LineNumber]['AccountPlanID']      = $line->AccountPlanID;
            $lines[$line->LineNumber]['NumberInPeriod']     = $line->NumberInPeriod;
            $lines[$line->LineNumber]['Rate']               = $line->Rate;
            $lines[$line->LineNumber]['SalaryText']         = $line->SalaryText;
            $lines[$line->LineNumber]['SalaryDescription']  = $line->SalaryDescription;
            $lines[$line->LineNumber]['SendToAltinn']       = $line->SendToAltinn;
            $lines[$line->LineNumber]['Fordel']             = $line->Fordel;
            $lines[$line->LineNumber]['AmountThisPeriod']   = $line->AmountThisPeriod;
            $lines[$line->LineNumber]['EnableEmployeeTax']  = $line->EnableEmployeeTax;
            $lines[$line->LineNumber]['MandatoryTaxSubtraction'] = $line->MandatoryTaxSubtraction;
            $lines[$line->LineNumber]['SalaryID']           = $SalaryID;
            $lines[$line->LineNumber]['ProjectID']          = $line->ProjectID;
            $lines[$line->LineNumber]['DepartmentID']       = $line->DepartmentID;
            $lines[$line->LineNumber]['EnableVacationPayment']  = $line->EnableVacationPayment;
            $lines[$line->LineNumber]['SalaryCode']  			= $line->SalaryCode;
            if (!isset($lines[$line->LineNumber]['MultipleLines'])) {
              $lines[$line->LineNumber]['MultipleLines'] = false;
              $lines[$line->LineNumber]['MultipleLinesNumber'] = 0;
            }
            else $lines[$line->LineNumber]['MultipleLines'] = true;
            $i = $lines[$line->LineNumber]['MultipleLinesNumber'];
            $lines[$line->LineNumber][$i]['AccountPlanID'] = $lines[$line->LineNumber]['AccountPlanID'];
            $lines[$line->LineNumber][$i]['NumberInPeriod'] = $lines[$line->LineNumber]['NumberInPeriod'];
            $lines[$line->LineNumber][$i]['Rate'] = $lines[$line->LineNumber]['Rate'];
            $lines[$line->LineNumber][$i]['SalaryText'] = $lines[$line->LineNumber]['SalaryText'];
            $lines[$line->LineNumber][$i]['SalaryDescription'] = $lines[$line->LineNumber]['SalaryDescription'];
            $lines[$line->LineNumber][$i]['SendToAltinn'] = $lines[$line->LineNumber]['SendToAltinn'];
            $lines[$line->LineNumber][$i]['Fordel'] = $lines[$line->LineNumber]['Fordel'];
            $lines[$line->LineNumber][$i]['AmountThisPeriod'] = $lines[$line->LineNumber]['AmountThisPeriod'];
            $lines[$line->LineNumber][$i]['EnableEmployeeTax'] = $lines[$line->LineNumber]['EnableEmployeeTax'];
            $lines[$line->LineNumber][$i]['MandatoryTaxSubtraction'] = $lines[$line->LineNumber]['MandatoryTaxSubtraction'];
            $lines[$line->LineNumber][$i]['SalaryID'] = $lines[$line->LineNumber]['SalaryID'];
            $lines[$line->LineNumber][$i]['ProjectID'] = $lines[$line->LineNumber]['ProjectID'];
            $lines[$line->LineNumber][$i]['DepartmentID'] = $lines[$line->LineNumber]['DepartmentID'];
            $lines[$line->LineNumber][$i]['EnableVacationPayment'] = $lines[$line->LineNumber]['EnableVacationPayment'];
            $lines[$line->LineNumber][$i]['SalaryCode'] = $lines[$line->LineNumber]['SalaryCode'];
            $lines[$line->LineNumber]['MultipleLinesNumber']++;
        }

        //################################################################
        //#Insert salary lines in database

        $postsub['salaryline_SalaryID'] = $SalaryID;

        foreach ($lines as $linenum => $field)
        {
            $multiple = $field['MultipleLines'];
            if ($multiple) {
              for ($i=0; $i < $field['MultipleLinesNumber']; $i++) {
                if($field[$i]['EnableEmployeeTax'] == 1)
                {
                  $query = "select a.Percent from arbeidsgiveravgift as a, kommune as k, accountplan as ap where ap.AccountPlanID=$head_employee->AccountPlanID and ap.KommuneID=k.KommuneID and a.Code=k.Sone";
                  if($debug) print "$query<br>\n";
                  $arbavgift = $_lib['storage']->get_row(array('query' => $query));
                  $field[$i]['EmployeeTax'] = $arbavgift->Percent;
                }
                #print "linenum: $linenum : text : " . $lines[$linenum]['SalaryText'] . "<br>";
                $postsub['salaryline_LineNumber']        = $linenum;
                $postsub['salaryline_AccountPlanID']     = $field[$i]['AccountPlanID'];
                $postsub['salaryline_NumberInPeriod']    = $field[$i]['NumberInPeriod'];
                $postsub['salaryline_Rate']              = $field[$i]['Rate'];
                $postsub['salaryline_SalaryText']        = $field[$i]['SalaryText'];
                $postsub['salaryline_SalaryDescription'] = $field[$i]['SalaryDescription'];
                $postsub['salaryline_SendToAltinn']      = $field[$i]['SendToAltinn'];
                $postsub['salaryline_Fordel']            = $field[$i]['Fordel'];
                $postsub['salaryline_AmountThisPeriod']  = $field[$i]['AmountThisPeriod'];
                $postsub['salaryline_EnableEmployeeTax'] = $field[$i]['EnableEmployeeTax'];
                $postsub['salaryline_MandatoryTaxSubtraction'] = $field[$i]['MandatoryTaxSubtraction'];
                $postsub['salaryline_ProjectID']         = $field[$i]['ProjectID'];
                $postsub['salaryline_DepartmentID']      = $field[$i]['DepartmentID'];
                $postsub['salaryline_EnableVacationPayment'] = $field[$i]['EnableVacationPayment'];
                $postsub['salaryline_EmployeeTax']     	= $field[$i]['EmployeeTax'];
                $postsub['salaryline_SalaryCode']       = $field[$i]['SalaryCode'];
                if($debug) print_r($postsub);

                $_lib['storage']->db_new_hash($postsub, 'salaryline');
              }
            }
            else {
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
            $postsub['salaryline_SalaryDescription'] = $lines[$linenum]['SalaryDescription'];
            $postsub['salaryline_SendToAltinn']      = $lines[$linenum]['SendToAltinn'];
            $postsub['salaryline_Fordel']            = $lines[$linenum]['Fordel'];
            $postsub['salaryline_AmountThisPeriod']  = $lines[$linenum]['AmountThisPeriod'];
            $postsub['salaryline_EnableEmployeeTax'] = $lines[$linenum]['EnableEmployeeTax'];
            $postsub['salaryline_MandatoryTaxSubtraction'] = $lines[$linenum]['MandatoryTaxSubtraction'];
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
    $lines[$line->LineNumber]['EnableEmployeeTax']    = $line->EnableEmployeeTax;
    $lines[$line->LineNumber]['MandatoryTaxSubtraction'] = $line->MandatoryTaxSubtraction;
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
    $postsub['salaryconfline_EnableEmployeeTax']  = $lines[$linenum]['EnableEmployeeTax'];
    $postsub['salaryconfline_MandatoryTaxSubtraction'] = $lines[$linenum]['MandatoryTaxSubtraction'];
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
elseif($_lib['input']->getProperty('action_salary_internal')) {
    $head = $_POST;    
    $head2 = $_POST;

    foreach($head2 as $k => $v) {
        if(strstr($k, "InternComment") === false) {
            unset($head[$k]);
        }
    }

    $_lib['storage']->db_update_multi_table($head, array('salary' => 'SalaryID'));
}

if($_lib['input']->getProperty('action_salary_journal') || $_lib['input']->getProperty('action_salary_new')){
  salary_update($SalaryID, $SalaryConfID);
  salary_journal($SalaryID);
}


elseif($_lib['input']->getProperty('action_salaryline_delete'))
{
    $pk['SalaryLineID']=$_lib['input']->getProperty('SalaryLineID');
    //print_r($pk);
    $_lib['storage']->db_delete_hash('salaryline', $pk);
    // set who updated and when, since we removed a line
    $sql_update = "UPDATE salary SET UpdatedAt = NOW(), UpdatedBy = '" . $_lib['sess']->get_person('PersonID') . "' WHERE SalaryID = '$SalaryID'";
    $_lib['db']->db_update($sql_update);
    salary_journal();
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
elseif($_lib['input']->getProperty('action_salarysubconf_enter') || $_lib['input']->getProperty('action_salary_updatetemplatecode'))
{
    $query = "select * from salaryconfline where SalaryConfID=1";
    $query_result = $_lib['storage']->db_query($query);
    //print_r($confhash);

    while($row = $_lib['storage']->db_fetch_assoc($query_result))
    {
        $query_exists="SELECT * FROM salaryconfline WHERE SalaryConfID = '". $SalaryConfID ."' AND LineNumber = '". $row['LineNumber'] ."' AND SalaryText = '". $row['SalaryText'] ."'";
        $query_exists_result = $_lib['storage']->db_query($query_exists);
        $exists = ($_lib['storage']->db_numrows($query_exists_result) > 0);
        if (!$exists) {
            $fields = array();
            $fields['salaryconfline_SalaryConfID']          = $SalaryConfID;
            $fields['salaryconfline_LineNumber']            = $row['LineNumber'];
            $fields['salaryconfline_Active']                = $row['Active'];
            $fields['salaryconfline_SalaryText']            = $row['SalaryText'];
            $fields['salaryconfline_SalaryDescription']     = $row['SalaryDescription'];
            $fields['salaryconfline_SendToAltinn']          = $row['SendToAltinn'];
            $fields['salaryconfline_Fordel']                = $row['Fordel'];
            $fields['salaryconfline_NumberInPeriod']        = $row['NumberInPeriod'];
            $fields['salaryconfline_Rate']                  = $row['Rate'];
            $fields['salaryconfline_AmountThisPeriod']      = $row['AmountThisPeriod'];
            $fields['salaryconfline_AccountPlanID']         = $row['AccountPlanID'];
            $fields['salaryconfline_EnableEmployeeTax']     = $row['EnableEmployeeTax'];
            $fields['salaryconfline_MandatoryTaxSubtraction'] = $row['MandatoryTaxSubtraction'];
            $fields['salaryconfline_ProjectID']             = $row['Project ID'];
            $fields['salaryconfline_DepartmentID']          = $row['DepartmentID'];
            $fields['salaryconfline_CarID']                 = $row['CarID'];
            $fields['salaryconfline_SalaryCode']            = $row['SalaryCode'];
            $fields['salaryconfline_EnableVacationPayment'] = $row['EnableVacationPayment'];
            //print_r($fields);

            $_lib['storage']->db_new_hash($fields, 'salaryconfline');

            #print "Lagt til linje nr ".$SalaryConfID."<br>\n";
        }
        else {
          $sql_update = "UPDATE salaryconfline SET ".
              "SalaryDescription ='" . $row['SalaryDescription'] .
           "', SendToAltinn ='" . $row['SendToAltinn'] .
           "', Fordel ='" . $row['Fordel'] .
           "', SalaryCode='" . $row['SalaryCode'] .
           "', EnableVacationPayment='" . $row['EnableVacationPayment'] .
           "', EnableEmployeeTax='" . $row['EnableEmployeeTax'] .
           "', MandatoryTaxSubtraction='" . $row['MandatoryTaxSubtraction'] .
           "' WHERE SalaryConfID='$SalaryConfID' AND LineNumber='" . $row['LineNumber'] . "' AND SalaryText = '". $row['SalaryText'] ."'";
          $_lib['db']->db_update($sql_update);
        }
    }
    $query = "select * from salaryconfline where SalaryConfID='". $SalaryConfID ."'";
    $query_result = $_lib['storage']->db_query($query);

    while($row = $_lib['storage']->db_fetch_assoc($query_result))
    {
        $query_exists="SELECT * FROM salaryconfline WHERE SalaryConfID = 1 AND LineNumber = '". $row['LineNumber'] ."' AND SalaryText = '". $row['SalaryText'] ."'";
        $query_exists_result = $_lib['storage']->db_query($query_exists);
        $exists = ($_lib['storage']->db_numrows($query_exists_result) > 0);
        if (!$exists) {
            $pk = array();
            $pk['SalaryConfLineID']=$row['SalaryConfLineID'];
            $_lib['storage']->db_delete_hash('salaryconfline', $pk);

            print "Slettet linje nr ".$salarylinehash['SalaryConfLineID']."<br>\n";
        }
    }
}
elseif($_lib['input']->getProperty('action_salary_updatesalarycode')) {
    #print_r($_REQUEST);
    $SalaryID 	    = $_lib['input']->getProperty('SalaryID');
    $SalaryConfID   = $_lib['input']->getProperty('SalaryConfID');

    $query 	= "select * from salaryconfline where SalaryConfID='$SalaryConfID' AND Active = 1";
    #print $query;
    $query_result = $_lib['storage']->db_query($query);
    #print_r($codehash);

    while($row = $_lib['storage']->db_fetch_assoc($query_result)) {
      $query_exists = "SELECT * FROM salaryline WHERE SalaryID='$SalaryID' AND LineNumber = '". $row['LineNumber'] . "' AND SalaryText='". $row['SalaryText'] ."'";
      $query_exists_result = $_lib['storage']->db_query($query_exists);
      $exists = ($_lib['storage']->db_numrows($query_exists_result) > 0);
      if (!$exists) {
        #print "Lager ny linje";
        $sql_insert = "insert into salaryline " .
        "(SalaryLineID, SalaryID, SalaryCode, EnableVacationPayment, EnableEmployeeTax, MandatoryTaxSubtraction, AccountPlanID, LineNumber, SalaryText, SalaryDescription,
          SendToAltinn, Fordel) values".
        "(NULL,'$SalaryID','".$row['SalaryCode']."','".$row['EnableVacationPayment']."','".$row['EnableEmployeeTax']."','".$row['MandatoryTaxSubtraction']."','".$row['AccountPlanID']."','" . $row['LineNumber'] . "','". $row['SalaryText'] . "','". $row['SalaryDescription'] ."','". $row['SendToAltinn'] . "','" . $row['Fordel'] ."');";
        # print "$sql_insert<br>\n";
        $_lib['db']->db_update($sql_insert);
      }
      $sql_update = "update salaryline set " .
           "SalaryCode='" . $row['SalaryCode'] .
        "', EnableVacationPayment='" . $row['EnableVacationPayment'] .
        "', EnableEmployeeTax='" . $row['EnableEmployeeTax'] .
        "', MandatoryTaxSubtraction='" . $row['MandatoryTaxSubtraction'] .
        "', AccountPlanID='" . $row['AccountPlanID'] .
        "', SalaryDescription='" . $row['SalaryDescription'] .
        "', SendToAltinn='" . $row['SendToAltinn'] .
        "', Fordel='" . $row['Fordel'] .
        "' where SalaryID='$SalaryID' and LineNumber='" . $row['LineNumber'] . "' and SalaryText='". $row['SalaryText']  ."'";
      # print "$sql_update<br>\n";
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
elseif($_lib['input']->getProperty('action_salary_update_extra')) {
    // update salary fields with employee information
    $query_employee_info = "SELECT a.*
                            FROM accountplan a JOIN salary s ON s.AccountPlanID = a.AccountPlanID
                            WHERE s.SalaryID='$SalaryID'";
    $employee_info = $_lib['storage']->get_row(array('query' => $query_employee_info));
    $query_update_salary_with_employee_info = sprintf("UPDATE salary SET
                                                       DomesticBankAccount = '%s', KommuneID = '%s', ShiftType = '%s',
                                                       WorkTimeScheme = '%s', TypeOfEmployment = '%s', OccupationID = '%s',
                                                       SubcompanyID = '%s'
                                                       WHERE SalaryID = %d;",
                                                       $employee_info->DomesticBankAccount,
                                                       $employee_info->KommuneID,
                                                       $employee_info->ShiftType,
                                                       $employee_info->WorkTimeScheme,
                                                       $employee_info->TypeOfEmployment,
                                                       $employee_info->OccupationID,
                                                       $employee_info->SubcompanyID,
                                                       $SalaryID);
    $_lib['db']->db_query($query_update_salary_with_employee_info);
}
elseif($_lib['input']->getProperty('action_altindato_update')) {
  if (isset($_POST['salary_ActualPayDate_'.$SalaryID])) {
    $query_update_altinn_date = "UPDATE salary SET ActualPayDate = '". $_POST['salary_ActualPayDate_'.$SalaryID] ."' where SalaryID = " . $SalaryID;
    $_lib['db']->db_query($query_update_altinn_date);
  }
}
elseif($_lib['input']->getProperty('action_salary_update_altinn_fields') && CheckIfWorkRelationChanged($SalaryID, $_POST['salary_WorkRelationID_'.$SalaryID])) {
  $tables['salary'] = 'SalaryID';

  $work_relation_query = "SELECT * FROM workrelation WHERE WorkRelationID = '".$_POST['salary_WorkRelationID_'.$SalaryID]."'";
  $work_relation = $_lib['storage']->get_row(array('query' => $work_relation_query));
  $args['salary_WorkRelationID_'.$SalaryID]         = $work_relation->WorkRelationID;
  $args['salary_ShiftType_'.$SalaryID]              = $work_relation->ShiftType;
  $args['salary_WorkTimeScheme_'.$SalaryID]         = $work_relation->WorkTimeScheme;
  $args['salary_TypeOfEmployment_'.$SalaryID]       = $work_relation->TypeOfEmployment;
  $args['salary_OccupationID_'.$SalaryID]           = $work_relation->OccupationID;
  $args['salary_SubcompanyID_'.$SalaryID]           = $work_relation->SubcompanyID;
  $args['salary_WorkStart_'.$SalaryID]              = $work_relation->WorkStart;
  $args['salary_WorkStop_'.$SalaryID]               = $work_relation->WorkStop;
  $args['salary_InCurrentPositionSince_'.$SalaryID] = $work_relation->InCurrentPositionSince;
  $args['salary_WorkPercent_'.$SalaryID]            = $work_relation->WorkPercent;
  $args['salary_WorkPercentUpdatedAt_'.$SalaryID]   = $work_relation->WorkPercentUpdatedAt;
  $args['salary_WorkMeasurement_'.$SalaryID]        = $work_relation->WorkMeasurement;
  $args['salary_SalaryDateChangedAt_'.$SalaryID]    = $work_relation->SalaryDateChangedAt;

  // set who updated altinn fields and when
  $args["salary_AltinnFieldsUpdatedBy_".$SalaryID] = $_lib['sess']->get_person('PersonID');
  $args["salary_AltinnFieldsUpdatedAt_".$SalaryID] = strftime("%F %T");

  $_lib['storage']->db_update_multi_table($args, $tables);
}

function getAltinnReportedDateTime($SalaryID) {
  global $_lib;
  $reported_date = $_lib['db']->get_row(array('query' => "SELECT ar1.LastChanged FROM altinnReport1 ar1 JOIN altinnReport1salary ar1s ON ar1.AltinnReport1ID = ar1s.AltinnReport1ID WHERE ar1s.SalaryId = " . $SalaryID . " ORDER BY ar1.LastChanged DESC LIMIT 1"));
  $reported_to_altinn_at = ($reported_date->LastChanged) ? strftime("%F %H:%M", strtotime($reported_date->LastChanged)) : "Ikke rapportert";
  return $reported_to_altinn_at;
}
?>
