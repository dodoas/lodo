<?
$db_table = "allowancecharge";
require_once "record.inc";

$query = "select ac.*, a.AccountName as OutAccountName, d.DepartmentName, p.Heading as ProjectName from $db_table as ac left join accountplan a on ac.OutAccountPlanID = a.AccountPlanID left join department d on d.DepartmentID = ac.DepartmentID left join project p on p.ProjectID = ac.ProjectID";
$result2 = $_lib['db']->db_query($query);
$db_total = $_lib['db']->db_numrows($result2);

while($row = $_lib['db']->db_fetch_object($result2)) {
  $date = $_lib['sess']->get_session('LoginFormDate');
  $vat_query = "select Percent from vat where Type = 'sale' and Active = 1 and VatID = ".(int)$row->OutVatID." and ValidFrom <= '$date' and ValidTo >= '$date'";
  $vat_out = $_lib['storage']->get_row(array('query' => $vat_query));
  if(!$vat_out) $_lib['message']->add(($row->ChargeIndicator != 1 ? "Rabatt ":"Kostnad ") . $row->AllowanceChargeID . ": Feil utg&aring;ende konto valg");
  else $row->OutPercent = $vat_out->Percent;
  $allowances_charges[] = $row;
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - rabatt/kostnad liste</title>
    <? includeinc('head') ?>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
    if ($messages = $_lib['message']->get()) {
?>
    <div class="warning"><? print $messages; ?></div>
    <br>
<?
    }
?>

    <table class="lodo_data" width="700px">
        <thead>
            <tr>
                <th>Rabatt/Kostnad listen:</th>
            <tr>
                <th style="text-align: right;">
                  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
                    <form name="edit" action="<? print $_lib['sess']->dispatch ?>t=allowancecharge.edit" method="post">
                        <? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'AllowanceChargeID')) ?>
                        <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_allowance_new', 'value'=>'Ny rabatt')) ?>
                        <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_charge_new', 'value'=>'Ny kostnad')) ?>
                    </form>
                  <? } ?>
               </th>
            </tr>
         </thead>
    </table>

    <table class="lodo_data" width="700px">
            <tr>
              <th class="sub">ID</th>
              <th class="sub">Aktiv</th>
              <th class="sub">Type</th>
              <th class="sub">&Aring;rsak</th>
              <th class="sub" align="right">Bel&oslash;p</th>
              <th class="sub" align="right">Resultat konto</th>
              <th class="sub" align="right">MVA</th>
              <th class="sub">Prosjekt</th>
              <th class="sub">Avdeling</th>
            </tr>
            <?
              if (!empty($allowances_charges)) {
                foreach($allowances_charges as $row) {
            ?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=allowancecharge.edit&AllowanceChargeID=<? print $row->AllowanceChargeID ?>"><? print $row->AllowanceChargeID ?></a></td>
                        <td> <? print $_lib['form3']->checkbox(array('table'=>'allowancecharge', 'value'=>$row->Active, 'disabled'=>'1')) ?> </td>
                        <td> <? if ($row->ChargeIndicator) print 'kostnad'; else print 'rabatt'; ?> </td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=allowancecharge.edit&AllowanceChargeID=<? print $row->AllowanceChargeID ?>"><? print $row->Reason ?></a></td>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$row->Amount, 'return'=>'value')) ?></td>
                        <td align="left"><? if($row->OutAccountName) print $row->OutAccountPlanID." ".$row->OutAccountName; ?></td>
                        <td align="right"><? if (!is_null($row->OutPercent)) print $_lib['format']->Percent(array('value'=>$row->OutPercent*1, 'return'=>'value')); ?></td>
                        <td><? print $row->ProjectName; ?></td>
                        <td><? print $row->DepartmentName; ?></td>
                    </tr>
                    <?
                }
              }
            ?>
    </table>
</body>
</html>


