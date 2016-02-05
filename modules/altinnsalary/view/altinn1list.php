<?
$db_table = "altinnReport1";
require_once "record.inc";

$query_altinn = "select * from $db_table order by AltinnReport1ID";
$result = $_lib['db']->db_query($query_altinn);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 1 List</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>



<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list">Soap 1 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn2list">Soap 2 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn3list">Soap 3 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn4list">Soap 4 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn5list">Soap 5 LIST</a>

<p>Altinn pin:</p>
<textarea rows='7' cols='20'><? print_r($_SESSION['altinn_pin']); ?></textarea><br />
<form name="delete_pin" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_delete_pin', 'value'=>'Delete saved pin')) ?>
</form>
<form name="expire_pin" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_expire_pin', 'value'=>'Expire saved pin')) ?>
</form>
<p>Altinn password:</p>
<textarea rows='7' cols='20'><? print_r($_SESSION['altinn_password']); ?></textarea><br />
<form name="delete_altinn_password" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_delete_password', 'value'=>'Delete saved altinn password')) ?>
</form>
<form name="expire_altinn_password" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_expire_password', 'value'=>'Expire saved altinn password')) ?>
</form>
<br /> <br />
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list&only_register_employee=1" method="post">
    Periode:
    <? print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'periode', 'value' => $_REQUEST['altinnReport1_periode'])); ?>
    <? print $_lib['form3']->submit(array('name'=>'action_soap1_show_salaries', 'value'=>'show salares')); ?>
  <? print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Test Soap1')) ?>
  <? print $_lib['form3']->submit(array('name'=>'action_generate_xml_report', 'value'=>'Generate XML')) ?>
<?
$query_salary   = "select S.AmountThisPeriod, S.JournalID, S.ValidFrom as FromDate, S.ValidTo as ToDate, A.AccountPlanID, A.AccountName, S.PayDate, S.DomesticBankAccount, S.TS, S.SalaryID, S.JournalDate, S.Period from salary as S, accountplan as A where S.AccountPlanID=A.AccountPlanID AND ActualPayDate LIKE  '" . $_REQUEST['altinnReport1_periode'] . "%' order by S.JournalID desc";
$result_salary  = $_lib['db']->db_query($query_salary);
?>
<br /><br />
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
        <td>
        <? print $_lib['form3']->checkbox(array('name' => "use_salary[" . $row->SalaryID . "]")); ?>L <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalID ?></a></td>
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
</form>

<br /><br /><br />

<table class="lodo_data">
  <thead>
    <tr>
      <th>Soap 1:</th>
      <th colspan="11"></th>
    </tr>
    <tr>
      <td class="menu">ID</td>
      <td class="menu">ReceiptId</td>
      <td class="menu">ParentReceiptId</td>
      <td class="menu">ReceiptText</td>
      <td class="menu">ReceiptHistory</td>
      <td class="menu">LastChanged</td>
      <td class="menu">ReceiptTypeName</td>
      <td class="menu">ReceiptStatusCode</td>
      <td class="menu">ExternalShipmentReference</td>
      <td class="menu">OwnerPartyReference</td>
      <td class="menu">PartyReference</td>
      <td class="menu">Salaries</td>
    </tr>
  </thead>


  <tbody>
  <?
  while($row = $_lib['db']->db_fetch_object($result)) {
  ?>
    <tr>
      <td><?print $row->AltinnReport1ID; ?></td>
      <td><?print $row->ReceiptId; ?></td>
      <td><?print $row->ParentReceiptId; ?></td>
      <td><?print $row->ReceiptText; ?></td>
      <td><?print $row->ReceiptHistory; ?></td>
      <td><?print $row->LastChanged; ?></td>
      <td><?print $row->ReceiptTypeName; ?></td>
      <td><?print $row->ReceiptStatusCode; ?></td>
      <td><?print $row->ExternalShipmentReference; ?></td>
      <td><?print $row->OwnerPartyReference; ?></td>
      <td><?print $row->PartyReference; ?></td>
      <td>
        <?
        $query_altin_salary = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$row->AltinnReport1ID." ORDER BY SalaryId ASC";
        $result_altin_salary  = $_lib['db']->db_query($query_altin_salary);

        while($_row = $_lib['db']->db_fetch_object($result_altin_salary)){
          $query_salary = "SELECT * FROM salary WHERE JournalID = ".$_row->JournalID;
          $result_salary  = $_lib['db']->db_query($query_salary);
          $_salary = $_lib['db']->db_fetch_object($result_salary);
        ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>">L <? ($_salary->UpdatedAt > $_row->UpdatedAt) ? print $_row->JournalID." (endrett)" : print $_row->JournalID ?></a>
        <? } ?>
      </td>
    </tr>


  <? } ?>
  </tbody>
</table>

<?
if (isset($xml_generated)) {
  $xml = $report->generateXML();
  echo "ERRORS:<br/>";
  if (!empty($report->errors)) foreach($report->errors as $error) echo '<p>' . $error . '</p>';
  else echo '<p>No errors.</p>';
  echo "XML DATA:<br/>";
?>
<textarea rows='100' cols='150'>
<?
echo "Salaries array:\n";
print_r($report->salaries);
echo "Employees array:\n";
print_r($report->employees);
echo "Message array:\n";
print_r($report->melding);
echo $xml;
?>
</textarea>
<?
}
?>
</body>
</html>
