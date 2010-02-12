<?
# $Id: tablelanguage.php,v 1.7 2005/10/28 17:59:41 thomasek Exp $ ConfDBFields_edit.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "confdbfields";

$TableName = $_REQUEST['TableName'];

require_once  "record.inc";

$query_lang = "select CL.*, C.TableField from confdbfieldlanguage as CL, confdbfields as C where CL.TableName='$TableName' and CL.ConfDBFieldID=C.ConfDBFieldID order by C.TableField, CL.Language";
//print $query_lang;

$result_lang = $_lib['db']->db_query($query_lang);
//print $_lib['db']->db_numrows($result_lang);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - fieldlanguage</title>
    <meta name="cvs"                content="$Id: tablelanguage.php,v 1.7 2005/10/28 17:59:41 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>
<body>
<table>
    <form name="menu" action="<? print $_lib['sess']->dispatch ?>t=confdbfield.tablelanguage&amp;TableName=<? print $TableName ?>" method="post">
        <tr>
            <td><? print $_form3->text(array('table'=>'lang', 'field'=>'name')) ?></td>
            <td><? print $_form3->submit(array('name'=>'action_confdbfield_newlang', 'value'=>'New language')) ?></td>
        </tr>
    </form>
</table>
<form action="<? print $MY_SELF ?>" method="post">
    <table>
        <thead>
        <tr>
             <th colspan="2">
                 Tilbake til <a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.edit&TableName=<? print $TableName; ?>"><? print "$TableName"; ?></a>
             </th>
        </tr>
        <tr>
            <th>ID</th>
            <th>Field name</th>
            <th>Language</th>
            <th>Alias</th>
            <th>Beskrivelse</th>
        </tr>
        </thead>
        <tbody>
        <?
        while($row = $_lib['db']->db_fetch_object($result_lang))
        {
            $i++;
            if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
            $where = "ConfDBFieldID=$row->ConfDBFieldID";
            ?>
            <tr>
                <td><? print $row->ConfDbFieldLanguageID; ?></td>
                <td><? print $row->TableField ?></td>
                <td><input type="text"  name="confdbfieldlanguage.Language.<? print $row->ConfDbFieldLanguageID ?>"     value="<? print $row->Language ?>"    size="17"></td>
                <td><input type="text"  name="confdbfieldlanguage.Alias.<? print $row->ConfDbFieldLanguageID ?>"        value="<? print $row->Alias ?>"       size="17"></td>
                <td><input type="text"  name="confdbfieldlanguage.Description.<? print $row->ConfDbFieldLanguageID ?>"  value="<? print $row->Description ?>"       size="17"></td>
                <td><a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.tablelanguage&amp;action_confdbfieldlanguage_delete=1&amp;ConfDbFieldLanguageID=<? print $row->ConfDbFieldLanguageID ?>&amp;ConfDBFieldID=<? print $ConfDBFieldID ?>&amp;TableField=<? print $TableField ?>&amp;TableName=<? print $TableName ?>">Delete</a>
            </tr>
            <?
        }
        ?>
        </tbody>
    </table>
    <input type="submit" value="Save (S)" name="action_general_update" accesskey="S" tabindex="8">
</form>

</body>
</html>
