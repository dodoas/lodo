<?
includelogic('accounting/accounting');
$accounting = new accounting();
$last_periode = $accounting->get_last_accountperiod_this_year(strftime('%F', time()));
if (is_null($last_periode)) $last_periode = $accounting->get_last_accountperiod_this_year(strftime('%F', strtotime('last year')));
if (isset($_REQUEST['periode'])) $_periode = $_REQUEST['periode'];
else $_periode = $last_periode;

require_once "record.inc";

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 1 List</title>
    <? includeinc('head') ?>
</head>

<body>

<?
includeinc('top');
includeinc('left');
print $_lib['message']->get();
?>

<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.salarylist" method="post">
  <p>
    Periode:
    <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'periode', 'value' => $_periode)); ?>
    <? print $_lib['form3']->submit(array('name'=>'action_show_salaries', 'value'=>'Vis l&oslash;nnslipper')); ?>
  </p>
</form>

<? if ($result_salary && $_lib['db']->db_numrows($result_salary)) { ?>
<form name="altinnsalary_send_report" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="13">L&oslash;nnsutbetalinger</th>
  </tr>
  <tr>
    <th class="sub">Velg</th>
    <th class="sub">Nr</th>
    <th class="sub">Dato</th>
    <th class="sub">Periode</th>
    <th class="sub">Ansatt</th>
    <th class="sub">Navn</th>
    <th class="sub">Utbetalt dato</th>
    <th class="sub">Fra perioden</th>
    <th class="sub">Til perioden</th>
    <th class="sub">Bankkonto</th>
    <th class="sub">Utskrift</th>
    <th class="sub">Sendt i raport</th>
    <th class="sub"></th>
  </tr>
</thead>

<tbody>
<?
$errors = array();
while($row = $_lib['db']->db_fetch_object($result_salary))
{
    $report_for_salary = new altinn_report($row->Period, array($row->SalaryID), null, false);
    $report_for_salary->populateReportArray();
    if (empty($report_for_salary->errors)) $is_ready_for_altinn = 'Klar';
    else {
      $errors[$row->JournalID] = $report_for_salary->errors;
      $is_ready_for_altinn = 'Ikke klar';
    }
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <?
    $query_report_id  = "SELECT * FROM altinnReport1salary AS ars JOIN altinnReport1 AS ar ON ars.AltinnReport1ID = ar.AltinnReport1ID WHERE ars.SalaryId =  " . $row->SalaryID . " ORDER BY UpdatedAt desc LIMIT 1";
    $result_report_id = $_lib['db']->db_query($query_report_id);
    $report_id = $_lib['db']->db_fetch_object($result_report_id);
    ?>
    <tr class="<? print "$sec_color"; ?>">
        <td><? print $_lib['form3']->checkbox(array('name' => "use_salary[" . $row->SalaryID . "]", 'disabled'=>$report_id->AltinnReport1ID ? 'disabled' : '')); ?></td>
        <td>L <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalDate ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->Period ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountPlanID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->AccountName ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->PayDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->FromDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->ToDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->DomesticBankAccount ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.print&SalaryID=<? print $row->SalaryID ?>" target="print">Vis</a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show&AltinnReport1ID=<? print $report_id->AltinnReport1ID ?>" target="print"><? print $report_id->AltinnReport1ID ? $report_id->AltinnReport1ID . " (" . $report_id->Period . ")" : ""?></a></td>
        <td><? print $is_ready_for_altinn; ?></td>
    </tr>
    <?
  }
?>
</tbody>
</table>

<br/>

<table class="lodo_data">
  <thead>
    <tr>
      <th colspan="5">Ansatte</th>
    </tr>
    <tr>
      <th class="sub">Velg</th>
      <th class="sub">ID</th>
      <th class="sub">Navn</th>
      <th class="sub">Rapportert i denne perioden</th>
      <th class="sub"></th>
    </tr>
  </thead>
  <tbody>
<?
  $report = new altinn_report($_periode);
  // all employees employed in this period
  $query_employees = $report->queryStringForCurrentlyEmployedEmployees();
  $result_employees = $_lib['db']->db_query($query_employees);
  while($employee = $_lib['db']->db_fetch_object($result_employees)) {
    $report_for_employee = new altinn_report($report->period, null, array($employee->AccountPlanID), true);
    $employee_names_list[$employee->AccountPlanID] = $report_for_employee->fullNameForErrorMessage($employee);
    $report_for_employee->populateReportArray();
    if (empty($report_for_employee->errors)) {
      $ready_for_altinn_status = false;
    } else {
      $employee_errors[$employee->AccountPlanID] = $report_for_employee->errors;
      $ready_for_altinn_status = 'Ikke klar';
    }
?>
    <tr>
      <td><? print $_lib['form3']->checkbox(array('name' => "use_employee[" . $employee->AccountPlanID . "]")); ?></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $employee->AccountPlanID ?>"><? print $employee->AccountPlanID ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $employee->AccountPlanID ?>"><? print $employee->FirstName . " " . $employee->LastName; ?></a></td>
<?
  // last report for this period that included this employee
  $query_altin_employee = "SELECT ar1e.*
                           FROM altinnReport1employee ar1e JOIN
                                altinnReport1 ar1 ON ar1e.AltinnReport1ID = ar1.AltinnReport1ID
                          WHERE ar1.Period = '" . $_periode . "' AND ar1e.AccountPlanID = " . $employee->AccountPlanID . "
                          ORDER BY ar1.AltinnReport1ID";
  $result_altin_employee = $_lib['db']->db_query($query_altin_employee);
  $employee_reported = $_lib['db']->db_numrows($result_altin_employee) != 0;
?>
      <td>
        <?
          if ($employee_reported) {
            $list_of_reports = "Sendt i rapporter ";
            while($altinn_employee = $_lib['db']->db_fetch_object($result_altin_employee)) {
              $list_of_reports .= "<a href='" . $_lib['sess']->dispatch . "t=altinnsalary.show&AltinnReport1ID=" . $altinn_employee->AltinnReport1ID . "'>" . $altinn_employee->AltinnReport1ID . "</a>, ";
            }
            $list_of_reports = substr($list_of_reports, 0, -2);
            print $list_of_reports;
          } else {
            print "Ikke rapportert";
          }
        ?>
      </td>
      <td>
        <?
          if ($ready_for_altinn_status) {
            print $ready_for_altinn_status;
          } else {
        ?>
          <a class='button' href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list&altinnReport1_periode=<? print $_periode; ?>&only_register_employee=1&use_employee[<? print $employee->AccountPlanID; ?>]=1&action_soap1=1">Register ansatte i Altinn</a>
        <?
          }
        ?>
      </td>
    </tr>
<?
  }
?>
  </tbody>
</table>

<br/>
  <input type="hidden" name="altinnReport1_periode" value='<?print $_periode; ?>'>
<?
  print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Send report'));
?>

<br/><br/>

</form>

<?
  if (!empty($errors) || !empty($employee_errors)) {
?>
  <table class="lodo_data">
      <tr>
        <th class="sub">Mangler</th>
      </tr>
<?
    if (!empty($errors)) {
      foreach($errors as $salary_journal_id => $salary_errors) {
?>
      <tr>
        <th>L <? print $salary_journal_id; ?></th>
      </tr>
<?
        foreach($salary_errors as $error) {
?>
      <tr>
        <td><? print $error; ?></td>
      </tr>
<?
        }
      }
    }
    if (!empty($employee_errors)) {
      foreach($employee_errors as $accountplan_id => $errors_for_employee) {
?>
      <tr>
        <th>Ansatt <? print $employee_names_list[$accountplan_id]; ?></th>
      </tr>
<?
        foreach($errors_for_employee as $error) {
?>
      <tr>
        <td><? print $error; ?></td>
      </tr>
<?
        }
      }
    }
?>
  </table>
<?
  }
}
else {
?>
<h4>Ingen l&oslash;nnslipper funnet</h4>
<?
}
?>

</body>
</html>
