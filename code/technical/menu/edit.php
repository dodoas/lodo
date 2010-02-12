<?
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$MenuName  = $_REQUEST['MenuName'];
$Language1 = $_REQUEST['Language1'];

$db_table  = "confmenues";
require_once  "record.inc";

$query = "select * from confmenues where MenuName='$MenuName' and Language='$Language1'";
$form = &new framework_lib_inline(array('query' => $query, 'table' => 'confmenues', '_dbh' => $_dbh, '_dsn' => $_dsn, '_maxwidth' => 40, 'action' => 'edit'));

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - order list</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.10 2005/01/30 12:35:04 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>

<body>
<table>
  <tr class="Heading">

    <td>
      <H6><b>Meny verdi</b></H6>
    </td>

    <td>
      <H6><b>Meny valg</b></H6>
    </td>

    <td align="right">
    <form name="menu" action="<? print $MY_SELF ?>" method="post">
        <input type="hidden" name="MenuName"    value="<? print $MenuName ?>">
        <input type="hidden" name="Language1"   value="<? print $Language1 ?>">
        <input type="text"   name="Language"    value="<? print $Language1 ?>">
    <td>
        <input type="submit" value="New menu choice (N)" name="record2_new" tabindex="1" accesskey="N">
    </form>
    </td>
    </tr>
<? print $form->start(array('action'=> $MY_SELF, 'method'=>'post'))?>
<? print $form->hidden(array('name' => 'MenuName'  , 'value' => $MenuName))  ?>
<? print $form->hidden(array('name' => 'Language1' , 'value' => $Language1)) ?>
<? print $form->hidden(array('name' => 'Language'  , 'value' => $Language1)) ?>
<tr>
<td colspan="4"></td>
</tr>
<?
do
{
    ?>
    <tr class="<? print $form->class ?>">
        <td><? print $form->show(array('field'=>'MenuValue')) ?>
        <td colspan="2"><? print $form->show(array('field'=>'MenuChoice')) ?>
        <td><? print $form->show(array('field'=>'Language')) ?>
    </tr>
    <?
} while($form->next_row());
?>
    </table>
</body>
<?
print $form->submit(array('name'=>'action_general_update', 'value'=>'Lagre', 'accesskey'=>'S'));
print $form->stop(array());
?>
</html>
