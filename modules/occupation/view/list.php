<?

if(!$OccupationID) {
  $OccupationID = $_REQUEST['OccupationID'];
}

$db_table = "occupation";
require_once "record.inc";

$query_car  = "select * from $db_table order by YNr, LNr";
$result_occupation = $_lib['db']->db_query($query_car);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Yrke liste</title>
    <? includeinc('head') ?>
    <? includeinc('combobox') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Yrker:</th>
    <th colspan="7"></th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Kode</td>
    <td class="menu">Navn</td>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_occupation)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
    <td><? print $row->OccupationID; ?></td>
    <td><? print "$row->YNr $row->LNr"; ?></td>
    <td><? print $row->Name; ?></td>
  </tr>
<? } ?>
</tbody>
</table>
<?
$csvFile = file($_SETUP['HOME_DIR'] . $_SETUP['YRKE_CSV']);
$data = array();
foreach ($csvFile as $line) {
    $data[] = str_replace(";", " ", $line);
}
?>
<form name="occupation_import" action="<? print $_lib['sess']->dispatch ?>t=occupation.list" method="post">
    <? print $_lib['form3']->Occupation_menu3(array('name'=>'occupation.Import', 'width'=>75, 'data'=>$data, 'same_key_value'=>true, 'tabindex'=>$tabindex++, 'class' => 'combobox', 'required' => false, 'notChoosenText' => 'Velg yrke', 'show_all'=>true)) ?>
    <? print $_lib['form3']->submit(array('name'=>'action_occupation_import', 'value'=>'Importer', 'style'=>'margin-left:70px')) ?>
</form>
</body>
</html>
