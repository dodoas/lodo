<?
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
$last_periode = $accounting->get_last_accountperiod_this_year(strftime('%F', time()));
if (is_null($last_periode)) $last_periode = $accounting->get_last_accountperiod_this_year(strftime('%F', strtotime('last year')));
if (isset($_POST['periode'])) $_periode = $_POST['periode'];
else $_periode = $last_periode;
?>

<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.salarylist" method="post">
  <p>
    Periode:
    <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'periode', 'value' => $_periode)); ?>
    <? print $_lib['form3']->submit(array('name'=>'action_show_slaries', 'value'=>'show salares')); ?>
  </p>
</form>

<?
$query_salary   = "select S.AmountThisPeriod, S.JournalID, S.ValidFrom as FromDate, S.ValidTo as ToDate, A.AccountPlanID, A.AccountName, S.PayDate, S.DomesticBankAccount, S.TS, S.SalaryID, S.JournalDate, S.Period from salary as S, accountplan as A where S.AccountPlanID=A.AccountPlanID AND ActualPayDate LIKE  '" . $_periode . "%' order by S.JournalID desc";
$result_salary  = $_lib['db']->db_query($query_salary);
if ($_lib['db']->db_numrows($result_salary)) {
?>

<table class="lodo_data">
<thead>
   <tr>
     <th colspan="12">L&oslash;nnsutbetalinger
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
while($row = $_lib['db']->db_fetch_object($result_salary))
{
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
        <td>L <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalDate ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->Period ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountPlanID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->AccountName ?></a></td>
        <!-- <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Amount(array('value'=>$row->AmountThisPeriod, 'return'=>'value')) ?></a></td> -->
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->PayDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->FromDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->ToDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->DomesticBankAccount ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.print&SalaryID=<? print $row->SalaryID ?>" target="print">Vis</a></td>
    </tr>
    <?
  }
?>
</tbody>
</table>

<br/>
<form name="altinnsalary_send_report" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
  <input type="hidden" name="altinnReport1_periode" value='<?print $_periode; ?>'>
<?
  print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Send report'));
?>
</form>
<?
}
else {
?>
<h4>Ingen l&oslash;nnslipper funnet<h4>
<?
}
?>
</body>
</html>
