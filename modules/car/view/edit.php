<?
$db_table = "companycar";

if(!$CompanyCarID) {
    $CompanyCarID = (int) $_REQUEST['companycar_CompanyCarID'];
}

$_this_date = $_lib['sess']->get_session('LoginFormDate');
$_year      = $_lib['date']->get_this_year($_this_date);

require_once "record.inc";
$query      = "select * from $db_table where CompanyCarID = $CompanyCarID";
$companycar = $_lib['storage']->get_row(array('query' => $query));

# get car milage per year
$car_milage = array();
$car_milage_query = "SELECT * FROM companycarmilage WHERE CompanyCarID = $CompanyCarID";
$car_milage_result = $_lib['storage']->db_query($car_milage_query);
while($_car_milage = $_lib['storage']->db_fetch_object($car_milage_result)) {
  $car_milage[$_car_milage->MilageYear] = $_car_milage;
}

#Do car calculations
$car_calculations = array();
foreach($car_milage as $milage_year => $milage) {
  if($milage->km0101) {
    $query_car = "SELECT SUM(Quantity) as sum_quantity, SUM(AmountIn) as sumin, SUM(AmountOut) as sumout FROM voucher WHERE CarID = $CompanyCarID and AccountPlanID=7000 and VoucherPeriod >= '$milage_year-01' and VoucherDate < '". ($milage_year + 1) ."-01-01'";
    #quantity = antall liter
    $car = $_lib['storage']->get_row(array('query' => $query_car));
    $distance = $milage->km3112 - $milage->km0101;
    $money_spent_on_fuel = 0;
    if($car->sum_quantity > 0) {
      $money_spent_on_fuel = $car->sumin - $car->sumout;
    }
    $money_spent_per_mile = 0;
    if ($distance > 0) {
      $money_spent_per_mile = $money_spent_on_fuel / $distance;
    }
  }
  $car_calculations[$milage_year] = array("km0101" => $milage->km0101, "km3112" => $milage->km3112,
                                          "distance" => (int)$distance, "money_spent_on_fuel" => (float)$money_spent_on_fuel,
                                          "money_spent_per_mile" => (float)$money_spent_per_mile);
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - companycar</title>
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="companycar_CompanyCarID" value="<? print $companycar->CompanyCarID ?>">
<table class="lodo_data">
<tr class="result">
    <th colspan="4">Bil</th>
</tr>
<tr>
    <td class="menu">Bil</td>
    <td><? print $companycar->CompanyCarID  ?></td>
</tr>
<tr>
    <td class="menu">Bil navn</td>
    <td><input type="text" name="companycar.CarName" value="<? print $companycar->CarName ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Registreringsnr</td>
    <td><input type="text" name="companycar.CarCode" value="<? print $companycar->CarCode ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Aktiv</td>
    <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$companycar->Active)) ?></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;psdato</td>
    <td><input type="text" name="companycar.ValidFrom" value="<? if ((int)($companycar->ValidFrom) != 0) print strftime("%F", strtotime($companycar->ValidFrom)) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;pepris</td>
    <td><input type="text" name="companycar.PurchasePrice" value="<? print $_lib['format']->Amount($companycar->PurchasePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;psdato</td>
    <td><input type="text" name="companycar.ValidTo" value="<? if ((int)($companycar->ValidTo) != 0) print strftime("%F", strtotime($companycar->ValidTo)) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Salgspris</td>
    <td><input type="text" name="companycar.SalePrice" value="<? if ($companycar->SalePrice > 0) print $_lib['format']->Amount($companycar->SalePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Typen</td>
    <td><input type="text" name="companycar.VehicleType" value="<? print $companycar->VehicleType ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Aktiver MVA</td>
    <td colspan="3"><? $_lib['form2']->checkbox2($db_table, "EnableVAT", $companycar->EnableVAT,''); ?></td>
</tr>
<tr>
  <td class="menu">
    <table class="menu lodo_data">
      <tr><td class="menu">Calcualtions</td></tr>
      <tr><td class="menu">Start milage</td></tr>
      <tr><td class="menu">End milage</td></tr>
      <tr><td class="menu">Distance</td></tr>
      <tr><td class="menu">Money spent on fuel</td></tr>
      <tr><td class="menu">Money spent per mile</td></tr>
    </table>
  </td>
  <td colspan="3">
    <table class="lodo_data">
      <tr>
        <?
          $_years = array_keys($car_calculations);
          rsort($_years);
          foreach($_years as $year) { ?>
          <th><? print $year; ?></th>
        <? } ?>
      </tr>
      <tr>
        <? foreach($_years as $year) { ?>
          <td><input type="text" name="companycarmilage.km0101.<? print $year; ?>" value="<? print $car_calculations[$year]['km0101']; ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr>
        <? foreach($_years as $year) { ?>
          <td><input type="text" name="companycarmilage.km3112.<? print $year; ?>" value="<? print $car_calculations[$year]['km3112']; ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr>
        <? foreach($_years as $year) { ?>
          <td><? print $car_calculations[$year]['distance']; ?></td>
        <? } ?>
      </tr>
      <tr>
        <? foreach($_years as $year) { ?>
          <td><? print $car_calculations[$year]['money_spent_on_fuel']; ?></td>
        <? } ?>
      </tr>
      <tr>
        <? foreach($_years as $year) { ?>
          <td><? print $car_calculations[$year]['money_spent_per_mile']; ?></td>
        <? } ?>
      </tr>
    </table>
  </td>
</tr>
<tr>
    <td class="menu">Annen informasjon</td>
    <td colspan="3"><input type="text" name="companycar.Description" value="<? print "$companycar->Description";  ?>" size="60"></td>
</tr>

<tr>
    <td colspan="4" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_car_update" value="Lagre bil" />
    <? } ?></td>
</tr>
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=car.list" method="post">
<tr>
    <? print $_lib['form3']->hidden(array('name'=>'CompanyCarID', 'value'=>$CompanyCarID)) ?>
    <td colspan="4" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_car_delete" value="Slett bil" onclick='return confirm("Er du sikker?")' />
    <? } ?>
</tr>
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
