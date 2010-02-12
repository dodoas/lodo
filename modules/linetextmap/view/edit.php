<?
# $Id: edit.php,v 1.5 2005/11/18 07:35:46 thomasek Exp $ product_edit.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$visKontonr = true;

if(!$_REQUEST["linetextmap_ReportID"])
{
  $RapportID = $_REQUEST['RapportID'];
}
else
{
  $RapportID = $_REQUEST['linetextmap_ReportID'];
}
$db_table  = "linetextmap";

require_once "record.inc";

if ($RapportID == 100)
{
	$myLine = "ReportShort";
	$myEnableLine = "EnableReportShort";
}
else
{
	$myLine = "Report" . $RapportID . "Line";
	$myEnableLine = "EnableReport" . $RapportID;
}
$query_list = "select distinct " . $myLine . " from accountplan where " . $myEnableLine . " = 1 order by " . $myLine . ";"; //  Active='1'
$result_list = $_lib['db']->db_query($query_list);
$db_total = $_lib['db']->db_numrows($result_list);
while ($row = $_lib['db']->db_fetch_object($result_list))
{
	// $myLine = "Report" . $RapportID . "Line";
	$query_map = "select LineTextMapID, Line, Text from $db_table where ReportID='$RapportID' and Line = '" . $row->$myLine . "'";
	$row_map = $_lib['storage']->get_row(array('query' => $query_map));
if ($row_map->LineTextMapID != "")
	$textArea .= $row_map->LineTextMapID . "::";
else
	$textArea .= "NULL::";
	if ($visKontonr)
	{
		$query = "select AccountPlanID, AccountName from accountplan where " . $myEnableLine . " = 1 and " . $myLine . " = '" . $row->$myLine . "';"; //  Active='1'
		$result = $_lib['db']->db_query($query);
		$isFirst = true;
		while ($row2 = $_lib['db']->db_fetch_object($result))
		{
			if ($isFirst)
			{
				$isFirst = false;
			}
			else
			{
				$textArea .= ", ";
			}

			$textArea .= $row2->AccountPlanID;
		}
		$textArea .= "::";
	}
	$textArea .= $row->$myLine . "::";
	$textArea .= $row_map->Text . "\n";
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - LineTextMap</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.5 2005/11/18 07:35:46 thomasek Exp $">
    <? includeinc('head') ?>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
?>
<form name="LineTextMap" action="<? print $_lib['sess']->dispatch ?>t=linetextmap.edit" method="post">
<input type="hidden" name="linetextmap_ReportID" value="<? print $RapportID ?>">
<? print $message ?>
<table cellspacing="0">
<thead>
    <tr>
        <th>LineTextMap register
        <th colspan="2">
    <tr>
        <th colspan="3">ID::<?php if ($visKontonr) { ?>Kontonr1, Kontonr2...Kontonr?::<?php } ?> Linjenr::Linjetekst
</thead>
<tbody>
    <tr>
        <td valign="top">Tekstlinjer</td>
        <td colspan="2"><textarea name="tekst" cols="100" rows="<?php print $db_total * 2; ?>"><?php print $textArea; ?></textarea></td>
</tbody>

<tfoot>
    <tr>
        <td align="right" colspan="3">
            <?
            if($_lib['sess']->get_person('AccessLevel') >= 3)
            {
                print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_linetextmap_update', 'accesskey'=>'S', 'tabindex'=>'6'));
            }
            ?>
        </td>
    </tr>
        <td align="right" colspan="3">
            <a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.list">Tilbake</a></td>
        </td>
    </tr>
  </form>
</tfoot>
</table>

</body>
</html>