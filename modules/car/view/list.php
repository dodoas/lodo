<?
if(!$CarID) {
  $CarID = $_REQUEST['CarID'];
}

$db_table = "car";
require_once "record.inc";

$query_car  = "select * from $db_table order by CarName limit 200";
$result_car = $_lib['db']->db_query($query_car);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Bil liste</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Biler:</th>
    <th colspan="6"></th>
  </tr>
  <tr>
    <th colspan="3"></th>
    <th align="right" colspan="3">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="car_search" action="<? print $_lib['sess']->dispatch ?>t=car.edit" method="post">
        Nytt nr:
        <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'CarID', 'width'=>'10')) ?>
        <? print $_lib['form3']->submit(array('name'=>'action_car_new', 'value'=>'Ny bil')) ?>
      </form>
    <? } ?>
    </th>
  </tr>
  <tr>
    <td class="menu">Bil</td>
    <td class="menu">Bilnavn</td>
    <td class="menu">Registreringsnummer</td>
    <td class="menu">Kj&oslash;psdato</td>
    <td class="menu">Salgsdato</td>
    <td class="menu">Aktiv</td>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_car)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=car.edit&car.CarID=<? print "$row->CarID"; ?>"><? print $row->CarID; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=car.edit&car.CarID=<? print "$row->CarID"; ?>"><? print $row->CarName; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=car.edit&car.CarID=<? print "$row->CarID"; ?>"><? print $row->CarCode; ?></a></td>
      <td><? print strftime("%F", strtotime($row->ValidFrom)); ?></td>
      <td><? if ((int)($car->ValidTo) != 0) print strftime("%F", strtotime($row->ValidTo));
             else print "0000-00-00"; ?></td>
      <td><? print $_lib['form3']->checkbox(array('table'=>'project', 'value'=>car::is_active($row->CarID), 'disabled'=>'1')) ?></td>
  </tr>
<? } ?>
</tbody>
</table>
</body>
</html>


