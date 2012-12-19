<?
# $Id: list.php,v 1.14 2005/11/03 15:33:11 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('auditorreport/auditorreport');

require_once "record.inc";

$auditorreportQuery = "select PeriodYear from auditorreport";
$auditorreportList = $_lib['db']->db_query($auditorreportQuery);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Revisorrapport liste</title>
    <meta name="cvs"                content="$Id: list.php,v 1.14 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<?
includeinc('top');
includeinc('left');
?>

<table class="lodo_data">
    <thead>
        <tr>
            <th>Revisor rapporter</th>
        </tr>
    <tbody>
        <?
        while($auditorreportRow = $_lib['db']->db_fetch_object($auditorreportList))
        {
            print "<tr>";
            print "<td align=\"center\"><a href=\"".$_lib['sess']->dispatch."t=auditorreport.edit&PeriodYear=" . $auditorreportRow->PeriodYear . "\" target=\"_new10\">" . $auditorreportRow->PeriodYear . "</a></td>";
            print "</tr>";
        }
        ?>
</table>
<table border="0">
    <tbody>
        <form name="budget" action="<? print $_lib['sess']->dispatch ?>t=auditorreport.list" method="post">
        <tr>
            <td>
                <? print $_lib['form3']->text(array('name' => 'PeriodYear', 'value' => $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate')))) ?>
            </td>
        </tr>
        <tr>
            <td>
                <? print $_lib['form3']->input(array('type'=>'submit', 'name'=>'action_auditorreport_new', 'value'=>' Nytt år (N)', 'accesskey'=>'N')) ?>
            </td>
            <td>
            </td>
        </tr>
        </form>
</table>

</body>
</html>