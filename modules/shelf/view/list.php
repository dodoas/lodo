<?php
includelogic('shelf/shelf');

$shelfs = new lodo_shelf();
include('record.inc');

$dispatch = $_lib['sess']->dispatch;

?>
<? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Shelf list</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
  <tr>
   <th style="width: 30px;">Id</th>
   <th style="width: 120px;">Name</th>
   <th>Status</th>
   <th></td>
  </tr>
<?
foreach($shelfs->listAll() as $id => $v) {
    list($name, $active) = $v;
    
    $edit_url = $dispatch."t=shelf.edit&ShelfID=".$id;
    
    ?>
  <tr>
    <td><?= $id ?></td>
    <td><?= $name ?></td>
    <td><?= $active ? "active" : "inactive"  ?></td>
    <td><a href="<?= $edit_url ?>">edit</a></td>
  </tr>
    <?
}
?>
</table>

<? $new_url = $dispatch."t=shelf.list"; ?>
<form action="<?= $new_url ?>" method="post">
  <table>
    <tr>
    <? if($_lib['sess']->get_person('AccessLevel') > 1) { ?>
      <td>New</td>
      <td><input type="text" name="<?= $new_name ?>" /></td>
      <td><input type="submit" name="<?= $new_submit ?>" value="Insert" /></td>
    <? } ?>
    </tr>   
  </table>
</form>
</html>