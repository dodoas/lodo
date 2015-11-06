<?
$db_table = "car";

if(!$CarID) {
    $CarID = (int) $_REQUEST['car_CarID'];
}

$_this_date = $_lib['sess']->get_session('LoginFormDate');
$_year      = $_lib['date']->get_this_year($_this_date);

require_once "record.inc";
$query      = "select * from $db_table where CarID = $CarID";
$car = $_lib['storage']->get_row(array('query' => $query));

# get car milage per year
$car_milage = array();
$car_milage_query = "SELECT * FROM carmilage WHERE CarID = $CarID";
$car_milage_result = $_lib['storage']->db_query($car_milage_query);
while($_car_milage = $_lib['storage']->db_fetch_object($car_milage_result)) {
  $car_milage[$_car_milage->MilageYear] = $_car_milage;
}

#Do car calculations
$car_calculations = array();
foreach($car_milage as $milage_year => $milage) {
  $query_car = "SELECT SUM(Quantity) as sum_quantity, SUM(AmountIn) as sumin, SUM(AmountOut) as sumout FROM voucher WHERE CarID = $CarID and AccountPlanID=7000 and VoucherPeriod >= '$milage_year-01' and VoucherDate < '". ($milage_year + 1) ."-01-01'";
  $_car = $_lib['storage']->get_row(array('query' => $query_car));
  $distance = $milage->EndMilage - $milage->StartMilage;
  $money_spent_on_fuel = 0;
  $money_spent_per_mile = 0;
  if($_car->sum_quantity > 0) {
    $money_spent_on_fuel = $_car->sumin - $_car->sumout;
  }
  if ($distance > 0) {
    $money_spent_per_mile = $money_spent_on_fuel / $distance;
  }
  $car_calculations[$milage_year] = array("StartMilage" => $milage->StartMilage, "EndMilage" => $milage->EndMilage,
                                          "distance" => (int)$distance, "money_spent_on_fuel" => (float)$money_spent_on_fuel,
                                          "money_spent_per_mile" => (float)$money_spent_per_mile);
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - car</title>
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="car_CarID" value="<? print $car->CarID ?>">
<table class="lodo_data">
<tr class="result">
    <th colspan="4">Bil</th>
</tr>
<tr>
    <td class="menu">Bil</td>
    <td><? print $car->CarID  ?></td>
</tr>
<tr>
    <td class="menu">Bil navn</td>
    <td><input type="text" name="car.CarName" value="<? print $car->CarName ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Registreringsnummer</td>
    <td><input type="text" name="car.CarCode" value="<? print $car->CarCode ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Aktiv</td>
    <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$car->Active)) ?></td>
</tr>
<tr>
    <td class="menu">Merke og modell</td>
    <td><input type="text" name="car.BrandAndModel" value="<? print $car->BrandAndModel ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Antall seter</td>
    <td><input type="text" name="car.NumberOfSeats" value="<? print $car->NumberOfSeats ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;psdato</td>
    <td><input type="text" name="car.ValidFrom" value="<? if ((int)($car->ValidFrom) != 0) print strftime("%F", strtotime($car->ValidFrom)) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;pepris</td>
    <td><input type="text" name="car.PurchasePrice" value="<? print $_lib['format']->Amount($car->PurchasePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Salgsdato</td>
    <td><input type="text" name="car.ValidTo" value="<? if ((int)($car->ValidTo) != 0) print strftime("%F", strtotime($car->ValidTo)) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Salgspris</td>
    <td><input type="text" name="car.SalePrice" value="<? if ($car->SalePrice > 0) print $_lib['format']->Amount($car->SalePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Typen</td>
    <?
      $VehicleTypes = array(
        'Varebil(klasse 2)' => 'Varebil(klasse 2)',
        'Personbil'         => 'Personbil',
        'Lastebil(tankbil)' => 'Lastebil(tankbil)',
        'Slepvogn'          => 'Slepvogn'
      );
    ?>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $VehicleTypes, 'table'=> 'car', 'field'=>'VehicleType', 'value'=>$car->VehicleType, 'notChoosenText' => ' ')); ?></td>
</tr>
<tr>
    <td class="menu">Aktiver MVA</td>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => array('nei', 'ja'), 'table'=> 'car', 'field'=>'EnableVAT', 'value'=>$car->EnableVAT, 'required' => true)); ?></td>
</tr>
<tr>
    <td class="menu">Registrerings&aring;r</td>
    <td colspan="3"><input type="text" name="car.RegistrationYear" value="<? print "$car->RegistrationYear";  ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Drivstoff</td>
    <td colspan="3"><input type="text" name="car.Fuel" value="<? print "$car->Fuel";  ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Annen informasjon</td>
    <td colspan="3"><input type="text" name="car.Description" value="<? print "$car->Description";  ?>" size="60"></td>
</tr>
<?
$_years = array_keys($car_calculations);
rsort($_years);
$years_per_line = 10;
for($skip_years = 0; $skip_years < count($_years); $skip_years+=$years_per_line) {
?>
<tr>
  <td class="menu">
    <table class="lodo_data">
      <tr class="height22"><td class="menu">Kalkulasjon</td></tr>
      <tr class="height22"><td class="menu">Start km.stand</td></tr>
      <tr class="height22"><td class="menu">Slutt milage</td></tr>
      <tr class="height22"><td class="menu">Avstand</td></tr>
      <tr class="height22"><td class="menu">Kr brukt p&aring; drivstoff</td></tr>
      <tr class="height22"><td class="menu">Kr per kilometer</td></tr>
    </table>
  </td>
  <td colspan="3">
    <table class="lodo_data">
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <th class="align-right"><? print $year; ?></th>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td><input class="align-right" type="text" name="carmilage.StartMilage.<? print $year; ?>" value="<? print $car_calculations[$year]['StartMilage']; ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td><input class="align-right" type="text" name="carmilage.EndMilage.<? print $year; ?>" value="<? print $car_calculations[$year]['EndMilage']; ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $car_calculations[$year]['distance']; ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $car_calculations[$year]['money_spent_on_fuel']; ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $car_calculations[$year]['money_spent_per_mile']; ?></td>
        <? } ?>
      </tr>
    </table>
  </td>
</tr>
<?
}
?>

<tr>
    <td></td>
    <td align="left">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_car_update" value="Lagre bil" />
    <? } ?></td>
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=car.list" method="post">
    <? print $_lib['form3']->hidden(array('name'=>'CarID', 'value'=>$CarID)) ?>
    <td align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_car_delete" value="Slett bil" onclick='return confirm("Er du sikker?")' />
    <? } ?>
</tr>
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
