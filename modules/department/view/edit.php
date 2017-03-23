<?
/* $Id: edit.php,v 1.33 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

if(!$DepartmentID) {
    $DepartmentID = (int) $_REQUEST['department_DepartmentID'];
}

$db_table = "department";
require_once "record.inc";

$_this_date = $_lib['sess']->get_session('LoginFormDate');
$_year          = $_lib['date']->get_this_year($_this_date);

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where DepartmentID = $DepartmentID";
#print "$query<br>\n";
$department = $_lib['storage']->get_row(array('query' => $query));

#Do car calculations
if($department->km0101) {
    //$query_car          = "select sum(Quantity) as sum_quantity, sum(AmountIn) as sumin, sum(AmountOut) as sumout from journal where DepartmentID = $DepartmentID";
    $query_car          = "select sum(Quantity) as sum_quantity, sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where DepartmentID = $DepartmentID and AccountPlanID=7000 and VoucherPeriod >= '$_year-01' and VoucherDate <= '$_this_date'";
    #print $query_car;
    #quantity = antall liter
    $car            = $_lib['storage']->get_row(array('query' => $query_car));
    $distance       = $department->km3112 - $department->km0101;
    if($car->sum_quantity > 0) {
    $krprliter        = ($car->sumin - $car->sumout) / $car->sum_quantity;
    } else {
    $krprliter = 0;
    }

    $literprmil = $car->sum_quantity / $distance;
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - department</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.33 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="department_DepartmentID" value="<? print $department->DepartmentID ?>">
<table class="lodo_data">
<tr class="result">
    <th colspan="4">Avdeling</th>
<tr>
    <td class="menu">Avdeling</td>
    <td><? print $department->DepartmentID  ?></td>
<tr>
    <td class="menu">Avdelingsnavn</td>
    <td><input type="text" name="department.DepartmentName" value="<? print $department->DepartmentName ?>" size="60"></td>
<tr>
    <td class="menu">Aktiv</td>
    <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$department->Active)) ?></td>
<tr>
    <td class="menu">Adresse</td>
    <td><input type="text" name="department.Address" value="<? print $department->Address ?>" size="60"></td>
<tr>
    <td class="menu">Postnummer</td>
    <td><input type="text" name="department.ZipCode" value="<? print $department->ZipCode ?>" size="60"></td>

<? //lagt til 6/1-2005 ?>
<tr>
    <td class="menu">Poststed</td>
    <td><input type="text" name="department.City" value="<? print $department->City ?>" size="60"></td>


<tr>
    <td class="menu">Avsluttes pr 31/12</td>
    <td colspan="3"><? $_lib['form2']->checkbox2($db_table, "EnableZeroYearEnd", $department->EnableZeroYearEnd,''); ?></td>
<tr>
    <td class="menu">Annen informasjon</td>
    <td colspan="3"><input type="text" name="department.Description" value="<? print "$department->Description";  ?>" size="60"></td>
<tr>
    <th colspan="4">Gjelder ved bil definert som avdeling (oppgi km 1/1 for &aring; aktivisere bil beregninger)</td>
<tr>
    <td class="menu">Km 1/1</td>
    <td colspan="3"><input type="text" name="department.km0101" value="<? print $department->km0101  ?>" size="60"></td>

<? if($department->km0101) { ?>
<tr>
    <td class="menu">Km 31/12</td>
    <td colspan="3"><input type="text" name="department.km3112" value="<? print $department->km3112  ?>" size="60"></td>

<tr>
    <td class="menu">Distanse</td>
    <td colspan="3"><? print $distance ?></td>

<tr>
    <td class="menu">Kr pr liter</td>
    <td colspan="3"><? print $krprliter ?></td>

<tr>
    <td class="menu">Liter pr mil</td>
    <td colspan="3"><? print $literprmil ?></td>
<? } ?>

<tr>
    <td colspan="4" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_department_update" value="Lagre avdeling" />
    <? } ?></td>
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=department.list" method="post">
<tr>
    <? print $_lib['form3']->hidden(array('name'=>'DepartmentID', 'value'=>$DepartmentID)) ?>
    <td colspan="4" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_department_delete" value="Slett avdeling" onclick='return confirm("Er du sikker?")' />
    <? } ?>
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
