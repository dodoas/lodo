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
    <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'periode', 'value' => $_periode, "access" => $_lib['sess']->get_person('AccessLevel'))); ?>
    <? print $_lib['form3']->submit(array('name'=>'action_show_salaries', 'value'=>'Vis l&oslash;nnslipper')); ?>
  </p>
</form>


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
$employee_errors = array();
$employee_names_list = array();
while($row = $_lib['db']->db_fetch_object($result_salary))
{
    $report_for_salary = new altinn_report($_periode, array($row->SalaryID), array(), false);
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
    $query_report_id  = "SELECT * FROM altinnReport1salary AS ars JOIN altinnReport1 AS ar ON ars.AltinnReport1ID = ar.AltinnReport1ID WHERE ars.SalaryId =  " . $row->SalaryID . " AND IFNULL(ReceivedStatus, '') != 'rejected' AND IFNULL(ReceivedStatus, '') != 'replaced' AND IFNULL(CancellationStatus, '') != 'cancelled' AND IFNULL(CancellationStatus, '') != 'is_cancellation' ORDER BY UpdatedAt desc LIMIT 1";
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

<? function print_work_relation_table($report, $employees, $active = true) { 
  global $_lib; 
  global $_periode; 
  global $employee_errors; 
  global $employee_names_list; ?>
<table class="lodo_data">
  <thead>
    <tr>
      <th colspan="6">Ansatte</th>
    </tr>
    <tr>
      <th class="sub">Velg</th>
      <th class="sub">ID</th>
      <th class="sub">Navn</th>
      <th class="sub">Arbeidsforhold</th>
      <th class="sub">Rapportert i denne perioden</th>
      <th class="sub"></th>
    </tr>
  </thead>
  <tbody>
<?
  $employee_data = array();
  $subcompany_names = array();
  foreach ($employees as $employee) {
    if($active) {
      $query_work_relations = "SELECT sc.Name, sc.OrgNumber, wr.* FROM workrelation wr JOIN subcompany sc ON sc.SubcompanyID = wr.SubcompanyID WHERE AccountPlanID = " . $employee->AccountPlanID ." AND (wr.WorkStart <= '". $_periode ."-01' OR wr.WorkStart LIKE '". $_periode ."%') AND (wr.WorkStop >= '". $_periode ."-01' OR wr.WorkStop = '0000-00-00')";
    } else {
      $query_work_relations = "SELECT sc.Name, sc.OrgNumber, wr.* FROM workrelation wr JOIN subcompany sc ON sc.SubcompanyID = wr.SubcompanyID WHERE AccountPlanID = " . $employee->AccountPlanID ." AND !((wr.WorkStart <= '". $_periode ."-01' OR wr.WorkStart LIKE '". $_periode ."%') AND (wr.WorkStop >= '". $_periode ."-01' OR wr.WorkStop = '0000-00-00'))";
    }
    $result_work_relations = $_lib['db']->db_query($query_work_relations);
    while($work_relation = $_lib['db']->db_fetch_object($result_work_relations)) {
      $report_for_employee = new altinn_report($report->period, array(), array($work_relation->WorkRelationID), true);
      $employee_names_list[$employee->AccountPlanID] = $report_for_employee->fullNameForErrorMessage($employee);
      $report_for_employee->populateReportArray();
      if (empty($report_for_employee->errors)) {
        $ready_for_altinn_status = false;
      } else {
        $employee_errors[$employee->AccountPlanID] = $report_for_employee->errors;
        $ready_for_altinn_status = 'Ikke klar';
      }

      // last report for this period that included this employee
      $query_altin_employee = "SELECT ar1wr.*
                              FROM altinnReport1WorkRelation ar1wr JOIN
                                    altinnReport1 ar1 ON ar1wr.AltinnReport1ID = ar1.AltinnReport1ID
                              WHERE ar1.Period = '" . $_periode . "' AND ar1wr.WorkRelationID = " . $work_relation->WorkRelationID . "
                              ORDER BY ar1.AltinnReport1ID";
      $result_altin_employee = $_lib['db']->db_query($query_altin_employee);
      $employee_reported = $_lib['db']->db_numrows($result_altin_employee) != 0;

      if ($employee_reported) {
        $list_of_reports = "Sendt i rapporter ";
        while($altinn_employee = $_lib['db']->db_fetch_object($result_altin_employee)) {
          $list_of_reports .= "<a href='" . $_lib['sess']->dispatch . "t=altinnsalary.show&AltinnReport1ID=" . $altinn_employee->AltinnReport1ID . "'>" . $altinn_employee->AltinnReport1ID . "</a>, ";
        }
        $list_of_reports = substr($list_of_reports, 0, -2);
      } else {
        $list_of_reports = "Ikke rapportert";
      }
      $subcompany_names[$work_relation->SubcompanyID] = $work_relation->Name;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["employee"] = $employee;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["work_relation"] = $work_relation;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["report_for_employee"] = $report_for_employee;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["ready_for_altinn_status"] = $ready_for_altinn_status;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["employee_reported"] = $employee_reported;
      $employee_data[$work_relation->SubcompanyID][$work_relation->WorkRelationID]["list_of_reports"] = $list_of_reports;
    }
  }
  
  foreach ($employee_data as $SubCompanyID => $work_relations) {
    ?>
    <tr>
      <th colspan="6"><? print $subcompany_names[$SubCompanyID]; ?></th>
    </tr>
    <?
    foreach ($work_relations as $big_row) {
      $employee = $big_row["employee"];
      $work_relation = $big_row["work_relation"];
      $report_for_employee = $big_row["report_for_employee"];
      $ready_for_altinn_status = $big_row["ready_for_altinn_status"];
      $employee_reported = $big_row["employee_reported"];
      $list_of_reports = $big_row["list_of_reports"];
?>
    <tr>
      <td><? print $_lib['form3']->checkbox(array('name' => "use_work_relation[" . $work_relation->WorkRelationID . "]")); ?></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $employee->AccountPlanID ?>"><? print $employee->AccountPlanID ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $employee->AccountPlanID ?>"><? print $employee->FirstName . " " . $employee->LastName; ?></a></td>
      <td><? print $work_relation->WorkRelationID . ' - ' . $work_relation->Name . ' (' . $work_relation->WorkStart . ' - ' . $work_relation->WorkStop . ') ' . $employee->FirstName . ' ' . $employee->LastName . '(' . $employee->AccountPlanID . ')'; ?></td>
      <td>
        <? print $list_of_reports; ?>
      </td>
      <td>
        <?
          if ($ready_for_altinn_status) {
            print $ready_for_altinn_status;
          } else {
        ?>
          <a class='button' href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list&altinnReport1_periode=<? print $_periode; ?>&only_register_employee=1&use_work_relation[<? print $work_relation->WorkRelationID; ?>]=1&action_soap1=1">Register arbeidsforhold i Altinn</a>
        <?
          }
        ?>
      </td>
    </tr>
<?
    }
  }
?>
  </tbody>
</table>
<br>
<? } ?>

<?
  $report = new altinn_report($_periode, array(), array(), false);
  // all employees employed in this period
  $query_employees = $report->queryStringForCurrentlyEmployedEmployees();
  $result_employees = $_lib['db']->db_query($query_employees);
  $active_employees = array();
  while($employee = $_lib['db']->db_fetch_object($result_employees)) {
    $active_employees[] = $employee;
  }

  print_work_relation_table($report, $active_employees, true);

  // all employees employed not in this period
  $address = $_lib["sess"]->dispatchs;
  foreach ($_GET as $key => $value) {
    if($key != "show_inactive") {
      $address .= "&$key=$value";
    }
  }
  $address .= "&periode=$_periode&action_show_salaries=1";

  if(isset($_GET['show_inactive'])) {
    print "<a href='". $address ."'>Skjul tidligere ansatte</a><br><br>";

    $query_other_employees = $report->queryStringForCurrentlyUnemployedEmployees();

    $result_employees = $_lib['db']->db_query($query_other_employees);
    $inactive_employees = array();
    while($employee = $_lib['db']->db_fetch_object($result_employees)) {
      $inactive_employees[] = $employee;
    }

    print_work_relation_table($report, $inactive_employees, false);
  } else {
    print "<a href='". $address ."&show_inactive=1'>Vis tidligere ansatte</a><br><br>";
  }
?>

<br/>
  <input type="hidden" name="altinnReport1_periode" value='<?print $_periode; ?>'>
  <span>OTP:</span><br/>
  <span>Sone: <? print $tax_zone . " ($tax_municipality_name)"; ?></span><br/>
  <span>Prosent: <? print $_lib['format']->Amount($tax_percent); ?>%</span><br/>
  <span>Bel&oslash;p: </span><input type="text" name="altinnReport1_pensionAmount" value='<? print $_lib['format']->Amount(0); ?>'><br/><br/>
<?
  print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Send rapport'));
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
?>

</body>
</html>
