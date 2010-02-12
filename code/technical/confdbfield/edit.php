<?
# $Id: edit.php,v 1.12 2005/10/28 17:59:41 thomasek Exp $ ConfDBFields_edit.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "confdbfields";

require_once  "record.inc";

$TableName = $_REQUEST['TableName'];

$query = "select * from $db_table where TableName='$TableName' order by TableField";
//print "$query<br>";
$result = $_lib['db']->db_query($query);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - order list</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.12 2005/10/28 17:59:41 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>

<body>
<table>
    <form name="menu" action="<? print $_lib['sess']->dispatch ?>t=confdbfield.tablelanguage&amp;TableName=<? print $TableName ?>" method="post">
        <tr>
            <td><? print $_form3->text(array('table'=>'lang', 'field'=>'name')) ?></td>
            <td><? print $_form3->submit(array('name'=>'action_confdbfield_newlang', 'value'=>'New language')) ?></td>
        </tr>
        <tr colspan="2">
            <td><a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.tablelanguage&amp;TableName=<? print $TableName ?>">Edit language</a>
        </tr>
    </form>
</table>

<table cellspacing="0">
<thead>
<tr>
<th colspan="3">
Table: <? print $TableName ?>

<th align="right" colspan="5">
    <form name="menu" action="<? print $MY_SELF ?>" method="post">
        <input type="hidden" name="TableName" value="<? print "$TableName"; ?>">
        <input type="submit" value="New table field (N)" name="action_confdbfield_dbupdate" tabindex="1" accesskey="N">
    </form>

  <tr>
    <th>ID</th>
    <th>Field name</th>
    <th>Def</th>
    <th>PK</th>
    <th>Required</th>
    <th>DefValue</th>
    <th>FieldType</th>
    <th>FormType</th>
    <th>FormTypeEdit</th>
    <th>Input validation</th>
    <th>Output validation</th>
    <th>Height</th>
    <th>Width</th>
    <th>FieldExtra</th>
    <th>FieldExtraEdit</th>
    <th>Act</th>
  </tr>
</thead>
<tbody>

<?
while($row = $_lib['db']->db_fetch_object($result)) {
$i++;
$form_name = "ConfDBFields_$i";
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
$where     = "ConfDBFieldID=$row->ConfDBFieldID";
?>
    <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
    <input type="hidden" name="ConfDBFieldID"   value="<? print $row->ConfDBFieldID; ?>">
    <tr bgcolor="#E6DEBE">
        <td><? print $row->ConfDBFieldID ?>
        <td><? print $row->TableField ?>
        <td><a href="<? print $_lib['sess']->dispatch ?> t=confdbfield.fieldlanguage&amp;ConfDBFieldID=<? print $row->ConfDBFieldID ?>&amp;TableField=<? print $row->TableField ?>&amp;TableName=<? print "$TableName"; ?>">Lang</a>
        <td><? $_form->checkbox("PrimaryKey", $row->PrimaryKey, $form_name, $db_table, $where); ?>
        <td><? $_form->checkbox("Required", $row->Required, $form_name, $db_table, $where) ?>
        <td><input type="text"  name="DefaultValue"             value="<? print $row->DefaultValue; ?>"         size="2"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','DefaultValue','<? print $where; ?>','float')">
        <td><? print $row->FieldType; ?>
        <td><input type="text"  name="FormType"                 value="<? print $row->FormType ?>"             size="8"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FormType','<? print $where; ?>','float')">
        <td><input type="text"  name="FormTypeEdit"             value="<? print $row->FormTypeEdit ?>"         size="8"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FormTypeEdit','<? print $where; ?>','float')">
        <td><input type="text"  name="InputValidation"          value="<? print $row->InputValidation ?>"      size="8"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','InputValidation','<? print $where; ?>','float')">
        <td><input type="text"  name="OutputValidation"         value="<? print $row->OutputValidation ?>"     size="8"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','OutputValidation','<? print $where; ?>','float')">
        <td><input type="text"  name="FormHeight"               value="<? print $row->FormHeight ?>"           size="3"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FormHeight','<? print $where; ?>','float')">
        <td><input type="text"  name="FormWidth"                value="<? print $row->FormWidth ?>"            size="3"    onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FormWidth','<? print $where; ?>','float')">
        <td><input type="text"  name="FieldExtra"               value="<? print $row->FieldExtra ?>"           size="10"   onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FieldExtra','<? print $where; ?>','float')">
        <td><input type="text"  name="FieldExtraEdit"           value="<? print $row->FieldExtraEdit ?>"       size="10"   onChange="Auto_Save('<? print $form_name ?>','<? print $db_table ?>','FieldExtraEdit','<? print $where; ?>','float')">

        <td><? $_form->checkbox("Active",$row->Active, $form_name, $db_table, $where); ?>
    </tr>
    </form>
<? } ?>
</tbody>
</table>
</body>
</html>
