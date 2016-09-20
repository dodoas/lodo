<?
$db_table  = "allowancecharge";
require_once "record.inc";

$query = "select * from $db_table where AllowanceChargeID='$AllowanceChargeID'";
$row = $_lib['storage']->get_row(array('query' => $query));

$date = $_lib['sess']->get_session('LoginFormDate');
$vat_query = "select Percent from vat where Active = 1 and VatID = " . (int) $row->OutVatID . " and ValidFrom <= '$date' and ValidTo >= '$date'";
$vat_out = $_lib['storage']->get_row(array('query' => $vat_query));
$vat_query = "select Percent from vat where Active = 1 and VatID = " . (int) $row->InVatID . " and ValidFrom <= '$date' and ValidTo >= '$date'";
$vat_in = $_lib['storage']->get_row(array('query' => $vat_query));
$tabindex = 1;
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - rabatt/kostnad</title>
    <? includeinc('head') ?>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
?>

<form name="allowancecharge" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AllowanceChargeID" value="<? print $row->AllowanceChargeID ?>">
<? print $message ?>
<table cellspacing="0">
<thead>
    <tr>
        <th>Rabatt/Kostnad register</th>
        <th colspan="2"></th>
    </tr>
</thead>
<tbody>
    <tr>
        <td>ID</td>
        <td colspan="2"><? print $row->AllowanceChargeID ?></td>
    </tr>
    <tr>
        <td>Aktiv</td>
        <td colspan="2"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'pk'=>$row->AllowanceChargeID, 'tabindex' => $tabindex++, 'value'=>$row->Active)) ?></td>
    </tr>
    <tr>
        <td>Type</td>
        <td colspan="2"><? print $_lib['form3']->Generic_menu3(array('data'=>array('1'=>'Kostnad', '0'=>'Rabatt'), 'table'=>$db_table, 'field'=>'ChargeIndicator', 'pk'=>$row->AllowanceChargeID, 'tabindex' => $tabindex++, 'value'=>$row->ChargeIndicator)); ?></td>
    </tr>
    <tr>
        <td>&Aring;rsak</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Reason', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->Reason)) ?></td>
    </tr>
    <tr>
        <td>Bel&oslash;p</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Amount', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$_lib['format']->Amount($row->Amount))) ?>
    </tr>
    <tr>
        <td>Resultat konto inn</td>
        <td colspan="2"><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'InAccountPlanID', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->InAccountPlanID, 'type' => array(0 => 'result', 1 => 'balance'), 'required' => 1)) ?></td>
    </tr>
    <tr>
        <td>MVA inn</td>
        <td colspan="2"><? if($vat_in->Percent) { print "$vat_in->Percent%"; } ?></td>
    </tr>
    <tr>
        <td>Resultat konto ut</td>
        <td colspan="2"><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'OutAccountPlanID', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->OutAccountPlanID, 'type' => array(0 => 'result', 1 => 'balance'), 'required' => 1)) ?></td>
    </tr>
    <tr>
        <td>MVA ut</td>
        <td colspan="2"><? if($vat_out->Percent) { print "$vat_out->Percent%"; } ?></td>
    </tr>
</tbody>
<tfoot>
    <tr>
        <td align="right" colspan="3">
            <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
            <? print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_allowancecharge_update', 'accesskey'=>'S', 'tabindex'=>'6')) ?></td>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td width="70"></td>
        <td align="right" colspan="1">
            <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
            <a href="<? print $_lib['sess']->dispatch."t=allowancecharge.list&amp;AllowanceChargeID=$row->AllowanceChargeID&amp;action_allowancecharge_delete=1" ?>" accesskey="D" class="button" 
                 onclick='return confirm("Er du sikker?")'>Slett (D)</a>
            </td>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="3">
            <a href="<? print $_lib['sess']->dispatch ?>t=allowancecharge.list">Tilbake</a></td>
        </td>
    </tr>
  </form>
</tfoot>
</table>
</body>
</html>
