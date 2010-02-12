<?
# $Id: list.php,v 1.19 2005/10/28 17:59:41 thomasek Exp $ ConfDBFields_list.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Empatix based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

require_once  "record.inc";
$db_table  = "confdbfields";

$query = "select ConfDBFIeldID, TableName from $db_table group by TableName";
$result = $_lib['db']->db_query($query);
?>
<? print $_lib['doctype'] ?>
<head>
    <title>Empatix - table metadata - <? print $_SETUP['DB_NAME'][0] ?></title>
    <meta name="cvs"                content="$Id: list.php,v 1.19 2005/10/28 17:59:41 thomasek Exp $" />
</head>

<body>
<table>
<form name="ConfDBFields" action="<? print $_MY_SELF ?>" method="post">
<thead>
  <tr>
    <th>Alle database navn</th>
    <th align="right">
        <input type="submit" value="Synkroniser valgte datamodeller (V)" name="action_confdbfield_updateselected" tabindex="2" accesskey="V">
    </th>
  </tr>
</thead>

<tbody>
<?

	print $_lib['message']->get();

    $query_show = "show databases";
    $result1 = $_lib['db']->db_query($query_show);
    $i = 0;
    while ($i <  $_lib['db']->db_numrows($result1))
    {
        $db_name =  $_lib['db']->db_fetch_object($result1);
        #print_r($db_name);
        ?>
        <tr>
            <td width="10">
                <? print $_form3->checkbox(array('name'=>'check_'.$i, 'value'=>$_REQUEST['check_'.$i])) ?>
            </td>
            <td>
                <? print $db_name->Database ?>
                <input type="hidden" name="<? print $i ?>" value="<? print $db_name->Database ?>">
            </td>
        </tr>
        <?
        $i++;
    }
?>
<input type="hidden" name="TotalCount" value="<? print $i-- ?>">
</tbody>
</form>
</table>

<table>
<thead>
  <tr>
    <th>Tabell navn innlogget database

    <th align="right">
    <form name="ConfDBFields" action="<? print $_MY_SELF ?>" method="post">
        <input type="submit" value="Synkroniser datamodell (U)" name="action_confdbfield_dbupdate" tabindex="1" accesskey="U">
    </form>
</thead>

<tbody>
<?
$i=0;
while($row = $_lib['db']->db_fetch_object($result)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
    <tr class="<? print "$sec_color"; ?>">
      <td> <a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.edit&TableName=<? print $row->TableName; ?>"><? print $row->TableName ?></a>
      <td>
<? } ?>
</tbody>
</table>
</body>
</html>