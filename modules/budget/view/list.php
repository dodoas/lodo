<?
# $Id: list.php,v 1.9 2005/10/14 13:15:40 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table='budget';
$db_table2='budgetline';

require_once "record.inc";

$budgetResult_query = "select substring(PeriodYear,1,4) as periods from budget where Type='result' group by periods";
$budgetResultList = $_lib['db']->db_query($budgetResult_query);

$budgetLiquidity_query = "select substring(PeriodYear,1,4) as periods from budget where Type='liquidity' group by periods";
$budgetLiquidityList = $_lib['db']->db_query($budgetLiquidity_query);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - budget list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.9 2005/10/14 13:15:40 thomasek Exp $" />
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
            <th>Resultatbudsjetter</th>
            <th>Likviditetbudsjetter</th>
        </tr>
    <tbody>
        <?
        while($ResultRow = $_lib['db']->db_fetch_object($budgetResultList) and $LiquidityRow = $_lib['db']->db_fetch_object($budgetLiquidityList))
        {
            print "<tr>";
            print "<td align=\"center\"><a href=\"".$_SETUP[DISPATCH]."t=budget.edit&Type=result&Periods=".$ResultRow->periods."\">".$ResultRow->periods."</a></td>";
            print "<td align=\"center\"><a href=\"".$_SETUP[DISPATCH]."t=budget.edit&Type=liquidity&Periods=".$LiquidityRow->periods."\">".$LiquidityRow->periods."</a></td>";
            print "</tr>";
        }
        ?>
</table>
<table border="0">
    <tbody>
        <form name="budget" action="<? print $_SETUP[DISPATCH]."t=budget.list" ?>" method="post">
        <tr>
            <td>

            </td>
            <td>
            	<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
            	<? print $_lib['form3']->text(array('name' => 'year', 'value' => '')) ?>
                <? print $_lib['form3']->submit(array('name'=>'action_budget_new', 'value'=>' Nytt år (N)', 'accesskey'=>'N')) ?>
                <? } ?>
            </td>
            <td>
            </td>
        </tr>
        </form>
</table>

</body>
</html>