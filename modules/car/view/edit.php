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
  $query_car = "SELECT SUM(Quantity) as sum_quantity, SUM(AmountIn) as sumin, SUM(AmountOut) as sumout FROM voucher WHERE CarID = $CarID and AccountPlanID=7000 and VoucherPeriod >= '$milage_year-01' and VoucherDate < '". ($milage_year + 1) ."-01-01' and Active = 1 ";
  $_car = $_lib['storage']->get_row(array('query' => $query_car));
  $distance = $milage->EndMilage - $milage->StartMilage;
  $distance_in_miles = $distance / 10;
  $money_spent_per_mile = 0;
  $liter_per_mile = 0;
  $money_spent_on_fuel = $_car->sumin - $_car->sumout;
  $price_per_liter = $milage->PricePerLiter;
  $total_liters = 0;
  if($price_per_liter > 0) {
    $total_liters = $money_spent_on_fuel / $milage->PricePerLiter;  
  }  
  if ($distance_in_miles > 0) {
    $money_spent_per_mile = $money_spent_on_fuel / $distance_in_miles;
    $liter_per_mile = $total_liters / $distance_in_miles;
  }
  $car_calculations[$milage_year] = array("StartMilage" => $milage->StartMilage, "EndMilage" => $milage->EndMilage,
                                          "distance" => (int)$distance, "money_spent_on_fuel" => (float)$money_spent_on_fuel,
                                          "money_spent_per_mile" => (float)$money_spent_per_mile,
                                          "PricePerLiter" => (float)$milage->PricePerLiter, "total_liters" => (float)$total_liters,
                                          "liter_per_mile" => (float)$liter_per_mile);
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - car</title>
    <? includeinc('head') ?>
    <script type="text/javascript">
      function setCarEnableVatCheckbox(type) {
        var mva_active = "not_selected";
        switch(type) {
          case 'Personbil':
            mva_active = false;
          break
          case 'Varebil(klasse 2)':
            mva_active = true;
          break
          case 'Lastebil':
            mva_active = true;
          break
          case 'Slepvogn':
            mva_active = true;
          break
        }

        if(mva_active != "not_selected") {
          $(".carEnableVatCheckbox")[0].checked = mva_active;
        }
      }

      function setCarActiveCheckbox() {
        var buy_date_string = $("#buy_date").val();
        var sell_date_string = $("#sell_date").val();

        var current_date = Date.parse(new Date());
        var buy_date = validateDate(buy_date_string) ? Date.parse(buy_date_string) : current_date;
        var sell_date = validateDate(sell_date_string) ? Date.parse(sell_date_string) : current_date;

        var difference_needed = 30 * 24 * 60 * 60 * 1000;

        var checkbox = $(".carActiveCheckbox")[0];
        var active = (current_date >= buy_date - difference_needed) && (current_date <= sell_date + difference_needed);
        checkbox.checked = active;
      }

      // takes date as string ('YYYY-MM-DD'), returns true if valid, false if not.
      function validateDate(date) {
        // regex check
        date = date.trim();
        if (!date.match(/^\d{4}-\d{1,2}-\d{1,2}$/)) return false;

        // check with Date object
        var date_obj = new Date(date);
        if (!date_obj) return false;

        // compare with Date object string
        var dd = date_obj.getDate();
        var mm = date_obj.getMonth() + 1;
        var yyyy = date_obj.getFullYear();

        var date_ints = date.split("-");
        if (yyyy != parseInt(date_ints[0]) || mm != parseInt(date_ints[1]) || dd != parseInt(date_ints[2])) return false;

        return true;
      }

    </script>
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
    <td class="menu">ID</td>
    <td><? print $car->CarID  ?></td>
</tr>
<tr>
    <td class="menu">Aktiv</td>
    <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>car::is_active($car->CarID), 'disabled'=>true, 'class'=>'carActiveCheckbox')) ?></td>
</tr>
<tr>
    <td class="menu">Registreringsnummer</td>
    <td><input type="text" name="car.CarCode" value="<? print $car->CarCode ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Merke og modell</td>
    <td><input type="text" name="car.BrandAndModel" value="<? print $car->BrandAndModel ?>" size="60"></td>
</tr>
<tr> <!-- leave or remove? -->
    <td class="menu">Bilnavn</td>
    <td><input type="text" name="car.CarName" value="<? print $car->CarName ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Type</td>
    <?
      $VehicleTypes = array(
        'Personbil'         => 'Personbil',
        'Varebil(klasse 2)' => 'Varebil(klasse 2)',
        'Lastebil'          => 'Lastebil',
        'Slepvogn'          => 'Slepvogn'
      );
    ?>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $VehicleTypes, 'table'=> 'car', 'field'=>'VehicleType', 'value'=>$car->VehicleType, 'notChoosenText' => ' ', 'OnChange'=>'setCarEnableVatCheckbox(this.value);')); ?></td>
</tr>
<tr>
    <td class="menu">Aktiver MVA</td>
    <td><? print $_lib['form3']->checkbox(array('table'=>'car', 'field'=>'EnableVAT', 'value'=>$car->EnableVAT, 'class'=>'carEnableVatCheckbox')) ?></td>
</tr>
<tr>
    <td class="menu">Registrerings&aring;r</td>
    <td colspan="3"><input type="text" name="car.RegistrationYear" value="<? print "$car->RegistrationYear";  ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Antall seter</td>
    <td><input type="text" name="car.NumberOfSeats" value="<? print $car->NumberOfSeats ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;psdato</td>
    <td><input type="text" name="car.ValidFrom" value="<? if ((int)($car->ValidFrom) != 0) print strftime("%F", strtotime($car->ValidFrom)) ?>" size="60" id="buy_date" onchange="setCarActiveCheckbox();"></td>
</tr>
<tr>
    <td class="menu">Kj&oslash;pepris</td>
    <td><input type="text" name="car.PurchasePrice" value="<? print $_lib['format']->Amount($car->PurchasePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Salgsdato</td>
    <td><input type="text" name="car.ValidTo" value="<? if ((int)($car->ValidTo) != 0) print strftime("%F", strtotime($car->ValidTo)) ?>" size="60" id="sell_date" onchange="setCarActiveCheckbox();"></td>
</tr>
<tr>
    <td class="menu">Salgspris</td>
    <td><input type="text" name="car.SalePrice" value="<? if ($car->SalePrice > 0) print $_lib['format']->Amount($car->SalePrice) ?>" size="60"></td>
</tr>
<tr>
    <td class="menu">Drivstoff</td>
    <?
      $FuelTypes = array(
        'Diesel' => 'Diesel',
        'Bensin' => 'Bensin',
        'Elektrisk' => 'Elbil'
      );
    ?>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $FuelTypes, 'table'=> 'car', 'field'=>'Fuel', 'value'=>$car->Fuel, 'notChoosenText' => ' ')); ?></td>
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
      <tr class="height22"><td class="menu">Pris pr. Liter</td></tr>
      <tr class="height22"><td class="menu">Start km.stand</td></tr>
      <tr class="height22"><td class="menu">Slutt km.stand</td></tr>
      <tr class="height22"><td class="menu">Avstand</td></tr>
      <tr class="height22"><td class="menu">Kr brukt p&aring; drivstoff</td></tr>
      <tr class="height22"><td class="menu">Liter brukt</td></tr>
      <tr class="height22"><td class="menu">Kr pr mil</td></tr>
      <tr class="height22"><td class="menu">Liter pr mil</td></tr>
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
          <td><input class="align-right" type="text" name="carmilage.PricePerLiter.<? print $year; ?>" value="<? print $_lib['format']->Amount($car_calculations[$year]['PricePerLiter']); ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td><input class="align-right" type="text" name="carmilage.StartMilage.<? print $year; ?>" value="<? print $_lib['format']->Amount($car_calculations[$year]['StartMilage']); ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td><input class="align-right" type="text" name="carmilage.EndMilage.<? print $year; ?>" value="<? print $_lib['format']->Amount($car_calculations[$year]['EndMilage']); ?>" size="15"></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $_lib['format']->Amount($car_calculations[$year]['distance']); ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $_lib['format']->Amount($car_calculations[$year]['money_spent_on_fuel']); ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $_lib['format']->Amount($car_calculations[$year]['total_liters']); ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $_lib['format']->Amount($car_calculations[$year]['money_spent_per_mile']); ?></td>
        <? } ?>
      </tr>
      <tr class="height22">
        <?
          for($i = $skip_years; ($i < count($_years)) && ($i < $skip_years + $years_per_line); $i++) {
            $year = $_years[$i];
        ?>
          <td class="align-right"><? print $_lib['format']->Amount($car_calculations[$year]['liter_per_mile']); ?></td>
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
    <input type="submit" name="action_car_update_from_fakturabank" value="Oppdater fra Fakturabank" />
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
<? includeinc('bottom');
  unset($_SESSION['oauth_car_info_fetched']);
?>
</body>
</html>
