<?
# $Id: list.php,v 1.14 2005/11/03 15:33:11 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('vat/mvaavstemming');
$avst = new mva_avstemming(array('_sess' => $_sess, '_dbh' => $_dbh, '_dsn' => $_dsn, '_date' => $_date, 'year' => $_REQUEST['Period']));

$db_table  = 'mvaavstemming';
$db_table2 = 'mvaavstemmingline';
$db_table3 = 'mvaavstemminglinefield';

require_once "record.inc";

$avstemmingQuery = "select substring(PeriodYear,1,4) as period from $db_table group by period";
$avstemmingList = $_lib['db']->db_query($avstemmingQuery);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - MVA avstemming liste</title>
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
            <th>MVA avstemminger</th>
        </tr>
    <tbody>
        <?
        while($avstemmingRow = $_lib['db']->db_fetch_object($avstemmingList))
        {
            print "<tr>";
            print "<td align=\"center\"><a href=\"".$_lib['sess']->dispatch."t=mvaavstemming.edit&Period=".$avstemmingRow->period."\" target=\"_new10\">".$avstemmingRow->period."</a></td>";
            print "</tr>";
        }
        ?>
</table>
<table border="0">
    <tbody>
        <form name="budget" action="<? print $_lib['sess']->dispatch ?>t=mvaavstemming.list" method="post">
        <tr>
            <td>
                <? print $_lib['form3']->text(array('name' => 'Year', 'value' => $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate')))) ?>
            </td>
        </tr>
        <tr>
            <td>
                <? print $_lib['form3']->input(array('type'=>'submit', 'name'=>'action_avstemming_new', 'value'=>' Nytt år (N)', 'accesskey'=>'N')) ?>
            </td>
            <td>
            </td>
        </tr>
        </form>
</table>

</body>
</html>