<?
includelogic('accounting/accounting');
$accounting = new accounting();

$query_salary = "SELECT
                  s.AmountThisPeriod, s.JournalID, s.ValidFrom as FromDate,
                  s.ValidTo as ToDate, a.AccountPlanID, a.AccountName,
                  s.PayDate, s.DomesticBankAccount, s.TS, s.SalaryID,
                  s.JournalDate, s.Period
                 FROM
                  salary as s,
                  accountplan as a
                 WHERE s.AccountPlanID = a.AccountPlanID AND (s.ActualPayDate IS NULL OR s.ActualPayDate = '0000-00-00')
                 ORDER BY s.JournalID DESC";
$result_salary = $_lib['db']->db_query($query_salary);

print $_lib['sess']->doctype;
?>

<head>
  <title>Empatix - unpaid salary list</title>
  <? includeinc('head') ?>
</head> 
<body>
  <? includeinc('top') ?>
  <? includeinc('left') ?>
  <table class="lodo_data">
    <thead>
      <tr>
        <th colspan="12">Ubetalte l&oslash;nninger</th>
      </tr>
      <tr>
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
        <th class="sub"></th>
      </tr>
    </thead>
    <tbody>
      <?
        while($row = $_lib['db']->db_fetch_object($result_salary)) {
        $i++;
        $sec_color = (!($i % 2)) ? "BGColorLight" : "BGColorDark";
      ?>
      <tr class="<? print "$sec_color"; ?>">
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
        <td>
          <?
            if($_lib['sess']->get_person('AccessLevel') > 3) {
              if($accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel'))) {
                echo $_lib['form3']->button(array('url' => $_lib['sess']->dispatch . "t=salary.list&SalaryID=" . $row->SalaryID . "&action_salary_delete=1", 'name' => 'Slett', 'confirm' => 'Vil du virkelig slette linjen?'));
              }
              else {
                print "Stengt";
              }
            }
          ?>
        </td>
      </tr>
      <?
        }
      ?>
</tbody>
</table>
</body>
</html>
