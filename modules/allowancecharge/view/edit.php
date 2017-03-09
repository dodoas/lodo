<?
$db_table  = "allowancecharge";
require_once "record.inc";

$query = "select * from $db_table where AllowanceChargeID='$AllowanceChargeID'";
$row = $_lib['storage']->get_row(array('query' => $query));

$date = $_lib['sess']->get_session('LoginFormDate');
$vat_query = "select Percent from vat where Type = 'sale' and Active = 1 and VatID = " . (int) $row->OutVatID . " and ValidFrom <= '$date' and ValidTo >= '$date'";
$vat_out = $_lib['storage']->get_row(array('query' => $vat_query));
$tabindex = 1;
if(!$vat_out) $_lib['message']->add("Feil utg&aring;ende konto valg");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - rabatt/kostnad</title>
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>

<body>
<script>

var first_time_amount = true;
function setAllowanceToNegativeAmountIfFirstTime() {
  var charge_indicator_element = document.getElementById("allowancecharge.ChargeIndicator.<? print $AllowanceChargeID; ?>");
  var charge_indicator = charge_indicator_element.value;
  var amount_element = document.getElementById("allowancecharge.Amount.<? print $AllowanceChargeID; ?>");
  var amount = toNumber(amount_element.value);
  if (charge_indicator == 0 && amount > 0 && first_time_amount) {
    first_time_amount = false;
    amount = -amount;
  }
  amount_element.value = toAmountString(amount);
}
var first_time_percent = true;
function setAllowanceToNegativePercentIfFirstTime() {
  var charge_indicator_element = document.getElementById("allowancecharge.ChargeIndicator.<? print $AllowanceChargeID; ?>");
  var charge_indicator = charge_indicator_element.value;
  var percent_element = document.getElementById("allowancecharge.PercentAmount.<? print $AllowanceChargeID; ?>");
  var percent = toNumber(percent_element.value);
  if (charge_indicator == 0 && percent > 0 && first_time_percent) {
    first_time_percent = false;
    percent = -percent;
  }
  percent_element.value = toAmountString(percent);
}

function onlyPercentOrAmount(percent_or_amount) {
    var amount_element = document.getElementById("allowancecharge.Amount.<? print $AllowanceChargeID; ?>");
    var percent_element = document.getElementById("allowancecharge.PercentAmount.<? print $AllowanceChargeID; ?>");
    if(percent_or_amount == 'percent') {
        amount_element.value = toAmountString(0);
    } else {
        percent_element.value = toAmountString(0);
    }
    
    var is_percentage_element = document.getElementById("allowancecharge.IsPercentage.<? print $AllowanceChargeID; ?>");
    is_percentage_element.value = (percent_or_amount == 'percent' ? '1' : '0');
}
</script>
<?
    includeinc('top');
    includeinc('left');
?>

<?
    if ($message = $_lib['message']->get()) {
      print "<div class='warning'>" . $message . "</div><br>";
    }
?>

<form name="allowancecharge" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AllowanceChargeID" value="<? print $row->AllowanceChargeID ?>">
<input id="allowancecharge.IsPercentage.<? print $AllowanceChargeID; ?>" type="hidden" name="allowancecharge.IsPercentage.<? print $AllowanceChargeID; ?>" value="<? print $row->IsPercentage ?>">
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
        <td colspan="2"><? print $_lib['form3']->Generic_menu3(array('data'=>array('1'=>'Kostnad', '0'=>'Rabatt'), 'table'=>$db_table, 'field'=>'ChargeIndicator', 'pk'=>$row->AllowanceChargeID, 'tabindex' => $tabindex++, 'value'=>$row->ChargeIndicator, 'OnChange' => 'setAllowanceToNegativeAmountIfFirstTime()')); ?></td>
    </tr>
    <tr>
        <td>&Aring;rsak</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Reason', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->Reason)) ?></td>
    </tr>
    <tr>
        <td>Percent</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'PercentAmount', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$_lib['format']->Amount($row->PercentAmount), 'OnChange' => 'onlyPercentOrAmount(\'percent\'); setAllowanceToNegativePercentIfFirstTime();', 'width'=>'5')) ?>%
    </tr>
    <tr>
        <td>Bel&oslash;p</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Amount', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$_lib['format']->Amount($row->Amount), 'OnChange' => 'onlyPercentOrAmount(\'amount\'); setAllowanceToNegativeAmountIfFirstTime()')) ?>
    </tr>
    <tr>
        <td>Resultat konto</td>
        <td colspan="2"><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'OutAccountPlanID', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->OutAccountPlanID, 'type' => array(0 => 'result', 1 => 'balance'), 'required' => 1)) ?></td>
    </tr>
    <tr>
        <td>MVA</td>
        <td colspan="2"><? if(!is_null($vat_out->Percent)) { print "$vat_out->Percent%"; } ?></td>
    </tr>
    <tr>
        <td>Prosjekt</td>
        <td colspan="2"><? print $_lib['form2']->project_menu2(array('table'=>$db_table, 'field'=>'ProjectID', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->ProjectID, 'unset' => true)) ?></td>
    </tr>
    <tr>
        <td>Avdeling</td>
        <td colspan="2"><? print $_lib['form2']->department_menu2(array('table'=>$db_table, 'field'=>'DepartmentID', 'pk'=>$row->AllowanceChargeID, 'tabindex'=>$tabindex++, 'value'=>$row->DepartmentID, 'unset' => true)) ?></td>
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
