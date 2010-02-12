<?
# $Id: edit.php,v 1.18 2005/10/28 17:59:40 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includemodel('accounting/accounting');
includemodel('budget/budget_action');
$accounting     = new accounting();

$periodYear = $_REQUEST['Periods'];
$budgetType = $_REQUEST['Type'];

$db_table='budget';
$db_table2='budgetline';

require_once "record.inc";

    /*  Make a check for new lines, or delete old lines  */
    /*  Getting loop over all marked accounts  */
    if($budgetType == 'result')
        $query = "select AccountPlanID from accountplan where EnableBudgetResult=1 and Active=1 order by AccountPlanID asc";
    elseif($budgetType == 'liquidity')
        $query = "select AccountPlanID from accountplan where EnableBudgetLikviditet=1 and Active=1 order by AccountPlanID asc";
    $accountRows = $_lib['db']->get_hash(array('query' => $query, 'key'=>'AccountPlanID', 'value'=>'AccountPlanID'));

    /*  Getting loop over all registerd accounts  */
    $query= "select BL.AccountPlanID, B.BudgetID, BL.Active, BL.BudgetLinesID from budgetline BL, budget B where B.BudgetID=BL.BudgetID and B.Type='".$budgetType."' and B.PeriodYear='".$periodYear."' order by BL.AccountPlanID asc";
    $budgetRows = $_lib['db']->get_hashhash(array('query' => $query, 'key'=>'AccountPlanID'));

    foreach($accountRows as $value)
    {
        if((!$value == $budgetRows[$value]['AccountPlanID']) or $budgetRows[$value]['Active'] == 0)
        {
            $budgetAction = new budget_Action(array('_sess'=>$_sess, '_dsn'=>$_dsn));

            if($budgetRows[$value]['Active'] == 0 and isset($budgetRows[$value]['Active']))
                $budgetAction->action_budget_setActive(array('db_table'=>$db_table2, 'BudgetLinesID'=>$budgetRows[$value]['BudgetLinesID']));
            else
                $budgetAction->action_budget_addLine(array('periodYear'=>$periodYear, 'budgetType'=>$budgetType, 'AccountPlanID'=>$value, 'db_table'=>$db_table2));

            $_lib['sess']->debug('legg til.'.$value);
            $_lib['sess']->debug(isset($budgetRows[$value]['Active'])."...");
        }
    }
    foreach($budgetRows as $key => $value)
    {
        if(!$budgetRows[$key]['AccountPlanID'] == $accountRows[$key])
        {
            $budgetAction = new budget_Action(array('_sess'=>$_sess, '_dsn'=>$_dsn));

            $budgetAction->action_budget_removeLine(array('db_table'=>$db_table2, 'BudgetLinesID'=>$budgetRows[$key]['BudgetLinesID']));

            $_lib['sess']->debug('fjern.'.$key);
        }
    }

    $budget_query = "select BL.*, BL.SumIn, BL.SumOut from budgetline BL, budget B where B.BudgetID=BL.BudgetID and B.Type='".$budgetType."' and B.PeriodYear='".$periodYear."' and BL.Active='1' and B.Active='1' and BL.Active='1' order by BL.AccountPlanID asc";
    $budgetLines = $_lib['db']->db_query($budget_query);
//$_lib['sess']->debug($budget_query);
?>
    <? print $_lib['sess']->doctype ?>
<head>
        <title>Invoice edit</title>
        <meta name="cvs"                content="$Id: edit.php,v 1.18 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>


<form name="budget" action="<? print $MY_SELF."&Periods=".$periodYear."&Type=".$budgetType ?>" method="post">
    <table class="lodo_data">
        <thead>
            <tr>
                <th colspan="3">Resultat budsjett</th>
                <th colspan="25" />
            </tr>
        </thead>
        <tbody>
            <?
            $rowCounter = 0;
            $totalOut = 0;
            $totalIn = 0;
            $totalRows = array();
            while($budgetRow = $_lib['db']->db_fetch_assoc($budgetLines))
            {
                $rowCounter++;

                $totalOut = 0;
                $totalIn = 0;
                
                $account     = $accounting->get_accountplan_object($budgetRow['AccountPlanID']);
                $accountName = $account->AccountName;
                ?>
                <tr>
                    <td colspan="4"><nobr>Kontonummer</nobr></td>
                    <td>Utgift</td>
                    <td>Inntekt</td>
                </tr>
                <?
                print "<tr>";
                print "<td colspan=\"4\"><a href=\"".$_lib['sess']->dispatch."t=accountplan.hovedbok&accountplan.AccountPlanID=".$budgetRow['AccountPlanID']."\">".$budgetRow['AccountPlanID'] ." : $accountName</a></td>\n";
			    
                print "<td align=\"right\"><nobr>".$_lib['format']->Amount(array('value'=>$budgetRow['SumOut'], 'return'=>'value'))."</nobr></td>\n";
                print "<td align=\"right\"><nobr>".$_lib['format']->Amount(array('value'=>$budgetRow['SumIn'], 'return'=>'value'))."</nobr></td>\n";
                print "</tr>";

                print "<tr>";
                for($i=1; $i<=12; $i++)
                {
                    if($i % 4 == 1)
                    {
                        ?>
                        </tr>
                        <tr height="10">
                        </tr>
                        <tr>
                            <td><? print $_lib['format']->MonthToText(array('value'=>$i, 'return'=>'value')) ?></td>
                            <td></td>
                            <td><? print $_lib['format']->MonthToText(array('value'=>$i+1, 'return'=>'value')) ?></td>
                            <td></td>
                            <td><? print $_lib['format']->MonthToText(array('value'=>$i+2, 'return'=>'value')) ?></td>
                            <td></td>
                            <td><? print $_lib['format']->MonthToText(array('value'=>$i+3, 'return'=>'value')) ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Utgift</td>
                            <td>Inntekt</td>
                            <td>Utgift</td>
                            <td>Inntekt</td>
                            <td>Utgift</td>
                            <td>Inntekt</td>
                            <td>Utgift</td>
                            <td>Inntekt</td>
                        </tr>
                        <tr>
                        <?
                    }
                    print "<td>".$_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Period'.$i.'Out', 'pk'=>$budgetRow['BudgetLinesID'], 'value' =>$_lib['format']->Amount(array('value'=>$budgetRow['Period'.$i.'Out'], 'return'=>'value')), 'width'=> 10, 'class'=>'number')) ." </td>\n";
                    print "<td>".$_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Period'.$i.'In', 'pk'=>$budgetRow['BudgetLinesID'] , 'value' =>$_lib['format']->Amount(array('value'=>$budgetRow['Period'.$i.'In'], 'return'=>'value')),  'width'=> 10, 'class'=>'number')) . "</td>\n";

                    $totalRows['PeriodOut'.$i] += $budgetRow['Period'.$i.'Out'];
                    $totalRows['PeriodIn'.$i] += $budgetRow['Period'.$i.'In'];

                    $totalOut += $budgetRow['Period'.$i.'Out'];
                    $totalIn += $budgetRow['Period'.$i.'In'];

                    #if($i % 4 == 0)
                    #{
                    #    print "</tr><tr>";
                    #    for($j=$i; $j<($i+4); $j++)
                    #    {
                    #        print "<td align=\"right\"><nobr>".$_lib['format']->Amount(array('value'=>$totalRows['PeriodOut'.$j], 'return'=>'value'))."</td>";
                    #        print "<td align=\"right\"><nobr>".$_lib['format']->Amount(array('value'=>$totalRows['PeriodIn'.$j], 'return'=>'value'))."</td>";
                    #    }
                    #}
                }
                print "</tr><tr height=\"10\"></tr>";

                print $_lib['form3']->input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$budgetRow['BudgetLinesID']));

            }
            print $_lib['form3']->input(array('type'=>'hidden', 'name'=>'numberofrows', 'value'=>$rowCounter));

            $totalOut=0;
            $totalIn=0;

            for($i=1; $i<=(count($totalRows)/2); $i++)
            {
                $totalOut += $totalRows['PeriodOut'.$i];
                $totalIn += $totalRows['PeriodIn'.$i];
            }
            ?>
        <tfoot>
            <tr>
                <td>Total</td>
                <td align="right"><nobr><? print $_lib['format']->Amount(array('value'=>$totalOut, 'return'=>'value')) ?></nobr></td>
                <td align="right"><nobr><? print $_lib['format']->Amount(array('value'=>$totalIn, 'return'=>'value')) ?></nobr></td>
            <?
                for($i=1; $i<=(count($totalRows)/2); $i++)
                {


                }
            ?>
            </tr>
            <tr>
                <td><? if(($totalIn - $totalOut) >= 0) { print "<font color=\"blue\">Overskudd</font>"; } else { print "<font color=\"red\">Underskudd</font>"; } ?></td>
                <td align="right"><nobr><? if(($totalIn - $totalOut) >= 0) { print "<font color=\"blue\">"; } else { print "<font color=\"red\">"; } print $_lib['format']->Amount(array('value'=>$totalIn - $totalOut, 'return'=>'value')) ?></font></nobr></td>
                <td colspan="25"></td>
            </tr>
            <tr height="10">
                <td colspan="27">
            </tr>
            <tr>
                <td>
                <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
                <? print $_lib['form3']->submit(array('name'=>'action_budget_update', 'value'=>'Lagre (S)', 'accesskey'=>'S')) ?>
                <? } ?>
                </td>
                <td colspan="26"></td>
            </tr>
        </tfoot>
    </table>
</form>

</body>
</html>