<?
# $Id: fieldlanguage.php,v 1.9 2005/10/28 17:59:41 thomasek Exp $ ConfDBFields_edit.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$ConfDBFieldID = $_REQUEST['ConfDBFieldID'];

$db_table  = "confdbfields";
require_once  "record.inc";
$TableName = $_REQUEST['TableName'];
$TableField = $_REQUEST['TableField'];

$query_lang = "select * from confdbfieldlanguage where TableName='$TableName' and TableField='$TableField'";
$result_lang = $_lib['db']->db_query($query_lang);
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - fieldlanguage</title>
    <meta name="cvs"                content="$Id: fieldlanguage.php,v 1.9 2005/10/28 17:59:41 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>
<body>

<form action="<? print $MY_SELF ?>" method="post">
<table>
<thead>
<tr>
  <th align="right" colspan="5">
    <input type="hidden" name="ConfDBFieldID" 	value="<? print $ConfDBFieldID ?>">
    <input type="hidden" name="TableName" 		value="<? print $TableName ?>">
    <input type="hidden" name="TableField" 		value="<? print $TableField ?>">
    <input type="submit" value="New field language (N)" name="action_confdbfield_fieldlanguagenew" tabindex="1" accesskey="N">
  </tr>
  <tr>
    <th>Tabell.Feltnavn</th>
    <th><a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.edit&TableName=<? print $TableName; ?>"><? print "$TableName"; ?>.<? print "$TableField"; ?></a></th>
  </tr>
  <tr>
    <th>ID</th>
    <th>Language</th>
    <th>Alias</th>
    <th>Beskrivelse</th>
<?
while($row = $_lib['db']->db_fetch_object($result_lang)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
$where     = "ConfDBFieldID=$row->ConfDBFieldID";
?>
    <tr>
    <td><? print $row->ConfDbFieldLanguageID; ?></td>
    <td><input type="text"  name="confdbfieldlanguage.Language.<? print $row->ConfDbFieldLanguageID ?>"   	value="<? print $row->Language ?>"    size="17"></td>
    <td><input type="text"  name="confdbfieldlanguage.Alias.<? print $row->ConfDbFieldLanguageID ?>"      	value="<? print $row->Alias ?>"       size="17"></td>
    <td><input type="text"  name="confdbfieldlanguage.Description.<? print $row->ConfDbFieldLanguageID ?>"  value="<? print $row->Description ?>"       size="17"></td>

<? } ?>
</tbody>
</table>
<input type="submit" value="Save (S)" 	name="action_confdbfield_fieldlanguageupdate" accesskey="S" tabindex="8">
</form>
</body>
</html>
