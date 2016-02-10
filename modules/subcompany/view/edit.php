<?
require_once "record.inc";

$query = "SELECT * FROM $db_table WHERE SubcompanyID = $SubcompanyID";
$subcompany = $_lib['storage']->get_row(array('query' => $query));
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Virksomhet</title>
    <meta name="cvs" content="$Id: edit.php,v 1.33 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<a href="<? print $_lib['sess']->dispatch ?>t=subcompany.list">Tilbake til virksomhetlisten</a>
<table class="lodo_data">
  <form name="subcompany_edit" action="" method="post">
    <input type="hidden" name="subcompany_SubcompanyID" value="<? print $subcompany->SubcompanyID; ?>">
    <tr class="result">
      <th colspan="4">Virksomhet</th>
    </tr>
    <tr>
      <td class="menu">virksomhet</td>
      <td><? print $subcompany->SubcompanyID  ?></td>
    <tr>
      <td class="menu">Navn</td>
      <td><input type="text" name="subcompany.Name" value="<? print $subcompany->Name ?>" size="60"></td>
    <tr>
      <td class="menu">OrgNumber</td>
      <td><input type="text" name="subcompany.OrgNumber" value="<? print $subcompany->OrgNumber ?>" size="60"></td>
    <tr>
    <tr>
      <td colspan="4" align="right">
      <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_subcompany_update" value="Lagre virksomhet" />
      <? } ?>
      </td>
    </tr>
  </form>
  <form name="delete" action="<? print $_lib['sess']->dispatch ?>t=subcompany.list" method="post">
    <tr>
      <? print $_lib['form3']->hidden(array('name'=>'subcompany.SubcompanyID', 'value'=>$SubcompanyID)) ?>
      <td colspan="4" align="right">
      <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_subcompany_delete" value="Slett virksomhet" onclick='return confirm("Er du sikker?")' />
      <? } ?>
  </form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
