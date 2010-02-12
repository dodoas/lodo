<?
# $Id: list.php,v 1.16 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$CreatedDate = $_REQUEST['CreatedDate'];

$db_table = "varelager";
$db_table2 = "varelagerline";

require_once "record.inc";

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Varelager</title>
    <meta name="cvs"                content="$Id: list.php,v 1.16 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
      <th align="left">Varetelling
      <th colspan="3">
  </tr>
  <tr>
      <th>Dato</th>
      <th>Beskrivelse</th>
      <th><input type="button" name="name" value=" Oppdater " onClick="document.location='<?php global $MY_SELF; print $MY_SELF; ?>';"/></th>
  </tr>
  <tr>
      <form name="varelager_ny" action="<? print $_lib['sess']->dispatch ?>t=varelager.edit" method="post" target="_new">
        <th>
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'CreatedDate', 'width'=>'10', 'tabindex'=>'1')) ?>
        </th>
        <th>
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Description', 'width'=>'30', 'tabindex'=>'2')) ?>
        </th>
        <th>
            <? print $_lib['form3']->submit(array('name'=>'action_varelager_new', 'value'=>'Ny varetelling')) ?>
        </th>
      </form>
  </tr>
  <tr>
    <th class="menu">Dato</th>
    <th class="menu">Beskrivelse</th>
    <th class="menu"></th>
  </tr>
</thead>

<tbody>
<?
$query = "select * from $db_table order by CreatedDate desc";
$result = $_lib['db']->db_query($query);
while($row = $_lib['db']->db_fetch_object($result))
{
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print $sec_color ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=varelager.edit&VareLagerID=<? print $row->VareLagerID ?>" target="_new"><? print $row->CreatedDate; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=varelager.edit&VareLagerID=<? print $row->VareLagerID ?>" target="_new"><? print $row->Description; ?></a></td>
      <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=varelager.list&action_varelager_delete=<? print $row->VareLagerID ?>" class="button">Slett</a>
      <? } ?>
      </td>
  </tr>
<? } ?>
</tbody>
</table>
</body>
</html>
