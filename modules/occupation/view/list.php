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
    <th colspan="3"></th>
    <th align="right" colspan="4">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="occupation_search" action="<? print $_lib['sess']->dispatch ?>t=occupation.edit" method="post">
        Nytt nr:
        <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'OccupationID', 'width'=>'10')) ?>
        <? print $_lib['form3']->submit(array('name'=>'action_occupation_new', 'value'=>'Nytt yrke')) ?>
      </form>
    <? } ?>
    </th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Aktiv</td>
    <td class="menu">Dems ID</td>
    <td class="menu">Kode</td>
    <td class="menu">Navn</td>
    <td class="menu">Dems oppdatert</td>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_occupation)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
    <td><a href="<? print $_lib['sess']->dispatch ?>t=occupation.edit&occupation.OccupationID=<? print "$row->OccupationID"; ?>"><? print $row->OccupationID; ?></a></td>
    <td><? print $_lib['form3']->checkbox(array('table'=>'project', 'value'=>$row->Active, 'disabled'=>'1')) ?></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=occupation.edit&occupation.OccupationID=<? print "$row->OccupationID"; ?>"><? print $row->RemoteID; ?></a></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=occupation.edit&occupation.OccupationID=<? print "$row->OccupationID"; ?>"><? print "$row->YNr $row->LNr"; ?></a></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=occupation.edit&occupation.OccupationID=<? print "$row->OccupationID"; ?>"><? print $row->Name; ?></a></td>
    <td><? print strftime("%F", strtotime($row->RemoteLastUpdatedAt)); ?></td>
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
    <? print $_lib['form3']->submit(array('name'=>'action_occupation_import', 'value'=>'Import', 'style'=>'margin-left:70px')) ?>
</form>
</body>
</html>
