<?
$db_table = "occupation";

if(!$OccupationID) {
    $OccupationID = (int) $_REQUEST['occupation_OccupationID'];
}

require_once "record.inc";
$query      = "select * from $db_table where OccupationID = $OccupationID";
$occupation = $_lib['storage']->get_row(array('query' => $query));
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Yrke</title>
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<a href="<? print $_lib['sess']->dispatch ?>t=occupation.list">Tilbake til listen</a>
<form action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="occupation_OccupationID" value="<? print $occupation->OccupationID ?>">
<table class="lodo_data">

  <tr class="result">
    <th colspan="4">Yrke</th>
  </tr>

  <tr>
    <td class="menu">ID</td>
    <td><? print $occupation->OccupationID  ?></td>
  </tr>

  <tr>
    <td class="menu">Aktiv</td>
    <td colspan="3"><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$occupation->Active)) ?></td>
  </tr>

  <tr>
    <td class="menu">Yrkekode</td>
    <td>
      YNr <input type="text" name="occupation.YNr" value="<? print $occupation->YNr ?>" size="5">
      LNr <input type="text" name="occupation.LNr" value="<? print $occupation->LNr ?>" size="5">
  </tr>

  <tr>
    <td class="menu">Navn</td>
    <td><input type="text" name="occupation.Name" value="<? print $occupation->Name ?>" size="60"></td>
  </tr>

  <?
    if (!empty($occupation->RemoteID)) {
  ?>
    <tr>
      <td class="menu">Dems ID</td>
      <td><? print $occupation->RemoteID ?></td>
    </tr>
  <?
    }
    if (!empty($occupation->RemoteLastUpdatedAt)) {
  ?>
    <tr>
      <td class="menu">Dems sisst oppdater</td>
      <td><? print $occupation->RemoteLastUpdatedAt ?></td>
    </tr>
  <?
    }
  ?>

  <tr>
    <td></td>
    <td align="left">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_occupation_update" value="Lagre yrke" />
    <? } ?></td>
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=occupation.list" method="post">
    <? print $_lib['form3']->hidden(array('name'=>'OccupationID', 'value'=>$OccupationID)) ?>
    <td align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_occupation_delete" value="Slett yrke" onclick='return confirm("Er du sikker?")' />
    <? } ?>
</tr>
</form>
</table>

<? includeinc('bottom') ?>
</body>
</html>
