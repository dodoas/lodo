<?
# $Id: edit.php,v 1.30 2005/10/28 17:59:40 thomasek Exp $ product_edit.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

if(!$ProductID)
{
  $ProductID = $_REQUEST['ProductID'];
}
$db_table  = "product";


includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$query = "select * from $db_table where ProductID='$ProductID'";
$row = $_lib['storage']->get_row(array('query' => $query));

$accountplan = $accounting->get_accountplan_object($row->AccountPlanID);

$VAT = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $_lib['sess']->get_session('LoginFormDate')));

#print_r($VAT);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - product</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.30 2005/10/28 17:59:40 thomasek Exp $">
    <? includeinc('head') ?>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
?>

<form name="product" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="ProductID" value="<? print $row->ProductID ?>">
<? print $message ?>
<table cellspacing="0">
<thead>
    <tr>
        <th>Produkt register
        <th colspan="2">
</thead>
<tbody>
    <tr>
        <td>ProduktID</td>
        <td colspan="2"><? print $row->ProductID ?></td>
    </tr>
    <tr>
        <td>Produktnummer</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'ProductNumber', 'pk'=>$row->ProductID, 'value'=>$row->ProductNumber, 'tabindex'=>'1')) ?></td>
    </tr>
    <tr>
        <td>Produktnavn</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'ProductName', 'pk'=>$row->ProductID, 'value'=>$row->ProductName, 'tabindex'=>'2')) ?></td>
    </tr>
    <tr>
        <td>Kostpris</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'UnitCostPrice', 'pk'=>$row->ProductID, 'value'=>$_lib['format']->Amount($row->UnitCostPrice))) ?>
    </tr>
    <tr>
        <td>Pris</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'UnitCustPrice', 'pk'=>$row->ProductID, 'value'=>$_lib['format']->Amount($row->UnitCustPrice))) ?>
	<td width="100">Inklusiv MVA:</td>
        <td width="200"><input type="text" id="priceincvat" /></td>
    </tr>
    <tr>
        <td>Resultat konto</td>
        <td colspan="2"><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'AccountPlanID', 'pk'=>$row->ProductID, 'value'=>$row->AccountPlanID, 'tabindex'=>'5', 'type' => array(0 => 'result', 1 => 'balance'), 'required' => 1)) ?></td>
    </tr>
    <tr>
        <td>MVA</td>
        <td colspan="2"><? if($VAT->Percent) { print "$VAT->Percent%"; } ?></td>
    </tr>
    <tr>
        <td>Avdeling</td>
        <td colspan="2"><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'CompanyDepartmentID', 'pk'=>$row->ProductID, 'value' => $row->CompanyDepartmentID, 'tabindex' => '5')) ?></td>
    </tr>
    <tr>
        <td>Prosjekt</td>
        <td colspan="2"><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$row->ProductID,  'value' =>  $row->ProjectID, 'tabindex' => '5')) ?></td>
    </tr>
    <tr>
        <td>UNSPSC</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'UNSPSC', 'pk'=>$row->ProductID, 'value'=> $row->UNSPSC)) ?>
    </tr>
    <tr>
        <td>EAN</td>
        <td colspan="2"><? print $_lib['form3']->input(array('type'=>'text', 'table'=>$db_table, 'field'=>'EAN', 'pk'=>$row->ProductID, 'value'=> $row->EAN)) ?>
    </tr>

</tbody>
<tfoot>
    <tr>
        <td align="right" colspan="3">
            <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
            <? print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_product_update', 'accesskey'=>'S', 'tabindex'=>'6')) ?></td>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td width="70"></td>
        <td align="right" colspan="1">
            <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
            <!-- <? print $_lib['form3']->submit(array('value'=>'Slett (D)', 'name'=>'action_product_delete', 'accesskey'=>'D', 'tabindex'=>'7')) ?> -->
            <a href="<? print $_lib['sess']->dispatch."t=product.list&amp;ProductID=$row->ProductID&amp;action_product_delete=1" ?>" accesskey="D" class="button" 
                 onclick='return confirm("Er du sikker?")'>Slett (D)</a>
            </td>
            <? } ?>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="3">
            <a href="<? print $_lib['sess']->dispatch ?>t=product.list">Tilbake</a></td>
        </td>
    </tr>
  </form>
</tfoot>
</table>

<script type="text/javascript">
$(document).ready(function(){
  $('#priceincvat').keyup(function(){
    var dest = $('#product\\.UnitCustPrice\\.<?= $row->ProductID ?>');
    var val = $('#priceincvat').val();
    var mva = <?= 1+(((float)$VAT->Percent)/100) ?>;
    
    var value = String(val/mva);
    var comma = value.indexOf('.');
    if(comma != -1)
      value = value.substring(0, value.indexOf('.') + 3);
    dest.val(value);
  });
});
</script>

</body>
</html>
