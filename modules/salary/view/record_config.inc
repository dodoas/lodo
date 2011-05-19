<?php


if($_lib['input']->getProperty('action_create_defaults'))
{
  // One day! this will be a global!
  $months = array( 'Januar', 'Februar', 'Mars', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Desember', 'Extra' );

  for($i = 0; $i < 13; $i++)
  {
    $month_prefix = (strlen($i + 1) < 2) ? "0".($i + 1) : ($i + 1);

    $fields = array();
    $fields['salaryperiodconf_Name'] = $months[$i];
    $fields['salaryperiodconf_Year'] = $confyear;
    $fields['salaryperiodconf_Voucherdate'] = sprintf("%d-%s-01", $confyear, $month_prefix);
    $fields['salaryperiodconf_Period'] = sprintf("%d-%s", $confyear, $month_prefix);
    $fields['salaryperiodconf_Fromdate'] = sprintf("%d-%s-01", $confyear, $month_prefix);
    $fields['salaryperiodconf_Todate'] = sprintf("%d-%s-%d", 
                                                 $confyear, $month_prefix, 
                                                 date('t', strtotime( sprintf("%d-%s-01", $confyear, $month_prefix))) );

    $_lib['storage']->db_new_hash($fields, 'salaryperiodconf');
  }

  header("Location: /" . $_SETUP['DISPATCHR'] ."t=salary.config&confyear=".$confyear);
  exit;
}
else if($_lib['input']->getProperty('action_update'))
{

  $_lib['db']->db_update_multi_table($_POST, array('salaryperiodconf' => 'SalaryperiodconfID') );

  $_lib['db']->db_query("DELETE FROM salaryperiodentries WHERE SalaryperiodconfID = $SalaryperiodconfID AND JournalID = 0");
  if(isset($_POST['employees']))
  {
    foreach($_POST['employees'] as $employee)
    {
      $_lib['db']->db_query(
        sprintf(
          'INSERT INTO salaryperiodentries (`SalaryperiodconfID`, `AccountPlanID`) VALUES(%d, %d);',
          $SalaryperiodconfID,
          $employee
        )
      );
    }
  }
}
