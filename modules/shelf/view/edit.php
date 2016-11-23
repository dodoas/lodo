<?php
includelogic('shelf/shelf');

$shelfs = new lodo_shelf();
$id = (int)$_GET['ShelfID'];

include('record.inc');

$shelf = $shelfs->get($id);

list($name, $active) = $shelf;

$update_url = $_lib['sess']->dispatch."t=shelf.list&ShelfID=" . $id;

?>
<? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Shelf edit</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>


<form action="<?= $update_url ?>" method="post" class="lodo_data">
<table>
  <tr>
    <td>Id</td>
    <td><?= $id ?></td>
  <tr>
  <tr>
    <td>Name</td>
    <td><input type="text" value="<?= $name ?>" name="<?= $update_name ?>" /></td>
  <tr>
  <tr>
    <td>Active</td>
    <td><input type="checkbox" <?= $active ? "checked" : "" ?> name="<?= $update_active ?>" /></td>
  <tr>
  <tr> 
    <td></td>
    <? if($_lib['sess']->get_person('AccessLevel') > 1) { ?>
    <td><input type="submit" value="Save" name="<?= $update_submit ?>" /></td>
    <? } ?>
  </tr>
</table>
</form>

</html>
