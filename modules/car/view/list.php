<?
if(!$CompanyCarID) {
  $CompanyCarID = $_REQUEST['CompanyCarID'];
}

$db_table = "companycar";
require_once "record.inc";

$query_car  = "select * from $db_table order by CarName limit 200";
$result_car = $_lib['db']->db_query($query_car);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - car list</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Biler:</th>
    <th colspan="3"></th>
  </tr>
  <tr>
    <th></th>
    <th align="right" colspan="3">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="car_search" action="<? print $_lib['sess']->dispatch ?>t=car.edit" method="post">
        Nytt nr:
        <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'CompanyCarID', 'width'=>'10')) ?>
        <? print $_lib['form3']->submit(array('name'=>'action_car_new', 'value'=>'Ny bil')) ?>
      </form>
    <? } ?>
    </th>
  </tr>
  <tr>
    <td class="menu">Bil</td>
    <td class="menu">Bil navn</td>
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
      <td><a href="<? print $_lib['sess']->dispatch ?>t=car.edit&companycar.CompanyCarID=<? print "$row->CompanyCarID"; ?>"><? print $row->CompanyCarID; ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=car.edit&companycar.CompanyCarID=<? print "$row->CompanyCarID"; ?>"><? print $row->CarName; ?></a>
      <td><? print $_lib['form3']->checkbox(array('table'=>'project', 'value'=>$row->Active, 'disabled'=>'1')) ?>
<? } ?>
</tbody>
</table>
</body>
</html>


