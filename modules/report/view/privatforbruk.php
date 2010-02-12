<?
# $Id: privatforbruk.php,v 1.15 2005/10/14 13:15:42 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$choosenYear = $_POST['accountperiod.Period'];
if(!$choosenYear)
{
    $choosenYear = substr($_lib['sess']->get_session('LoginFormDate'),0,4);
}
$choosenPeriod = $_POST['choosenPeriod'];
if(!$choosenPeriod)
{
    $choosenPeriod = 12;
}

$db_table = "setup";
require_once "record.inc";

$query_setup    = "select name, value from setup";
$setup = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

$taxPercent = $setup['expectedflowTax'];
$calculatedTax1 = $setup['expectedflowPayedTaxTerm1'];
$calculatedTax2 = $setup['expectedflowPayedTaxTerm2'];
$calculatedTax3 = $setup['expectedflowPayedTaxTerm3'];
$calculatedTax4 = $setup['expectedflowPayedTaxTerm4'];

$periode_query = "select A.Period from accountperiod A where substring(A.Period,1,4)='$choosenYear' and A.Period!='".$choosenYear."-13'";
$result = $_lib['db']->db_query($periode_query);

$_lib['sess']->Debug($periode_query);

$totalTax = 0;
$totalOut = 0;
$totalIn = 0;
$totalNet = 0;
$totalResult = 0;

$year = 12;

$startMnd = 1;
$currentMnd = $startMnd;
?>


    <? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Privat forbruk</title>
        <meta name="cvs"                content="$Id: privatforbruk.php,v 1.15 2005/10/14 13:15:42 thomasek Exp $" />
        <? includeinc('head'); ?>
    </head>
<body>
    <? includeinc('top'); ?>
    <? includeinc('left'); ?>
    <table border="0" cellspacing="0">
        <thead>
            <tr><th>
            <form name="velg" action="<? print $MY_SELF ?>" method="post">
                <table border="0" cellspaving="0">
                    <tr>
                        <td>År
                        <td>Til måned
                    <tr>
                        <td>
                            <? print $_lib['form3']->PeriodeYear_menu3(array('table'=>'accountperiod', 'field'=>'Period', 'value'=>substr($_lib['sess']->get_session('LoginFormDate'),0,4))) ?>
                        <td align="center">
                            <select name="choosenPeriod">
                                <?
                                for($i=1; $i<=12; $i++)
                                {
                                    if($choosenPeriod == $i)
                                        print "<option value=\"".$i."\" selected>$i";
                                    else
                                        print "<option value=\"".$i."\">$i";
                                }
                                ?>
                            </select>
                        <td>
                            <input type="submit" value="Velg periode" name="velg_periode">
                </table>
            </form>
    </table>
    <br><br>
    <form name="velg" action="<? print $MY_SELF ?>" method="post">
        <table border=0 cellspacing="0" width="600">
            <thead>
                <tr>
                    <th>Periode
                    <th>Privatuttak
                    <th>Overskudd
                    <th>Skatt <? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Value', 'pk'=>'expectedflowTax', 'value'=>$_lib['format']->Percent(array('value'=>$taxPercent, 'return'=>'value')), 'width'=>'2', 'class'=>'number')) ?>
                    <th>Netto
                    <th>Resultat
            <tbody>
            <?
                while($periode = $_lib['db']->db_fetch_object($result))
                {
                    $select_query = "select sum(voucher.AmountIn) as sumin, sum(voucher.AmountOut) as sumout from voucher, accountplan where voucher.VoucherPeriod='$periode->Period' and accountplan.EnablePersonalUsage=1 and voucher.AccountPlanID=accountplan.AccountPlanID and voucher.Active=1";
                    $row = $_lib['storage']->get_row(array('query' => $select_query));
                    $select_query = "select sum(voucher.AmountIn) as sumin, sum(voucher.AmountOut) as sumout from voucher, accountplan where voucher.VoucherPeriod='$periode->Period' and accountplan.EnablePersonalUsage=1 and voucher.Active=1 and voucher.AccountPlanID=".$_lib['sess']->get_companydef('VoucherResultAccount');
                    $row2 = $_lib['storage']->get_row(array('query' => $select_query));

                    $out = $row->sumout - $row->sumin;
                    $totalOut += $out;

                    $in = $row2->sumin - $row2->sumout;
                    $totalIn += $in;

                    $tax = $in * $taxPercent;
                    $totalTax += $tax;

                    $net = $in - $tax;
                    $totalNet += $net;

                    $resultBalance = $net - $out;
                    $totalResult += $resultBalance;

                    $choosenMnd = substr($periode->Period,5,2);

                    ?>
                    <tr>
                        <td><? print $_lib['format']->MonthToText(array('value'=>$choosenMnd, 'return'=>'value')) ?>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$out, 'return'=>'value')) ?>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$in, 'return'=>'value')) ?>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$tax, 'return'=>'value')) ?>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$net, 'return'=>'value')) ?>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$resultBalance, 'return'=>'value')) ?>
                    <?
                }
            ?>
            <tfoot>
                <tr height="10"><td><br>
                <tr>
                    <td>sum
                    <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalOut, 'return'=>'value')) ?>
                    <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalIn, 'return'=>'value')) ?>
                    <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalTax, 'return'=>'value')) ?>
                    <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalNet, 'return'=>'value')) ?>
                    <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalResult, 'return'=>'value')) ?>
        </table>
        <br><br><br>
        <table border="0" width="280">
            <tr>
                <td>Bet skatt:
                <td align="center">1.Term
                <td align="left"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Value', 'pk'=>'expectedflowPayedTaxTerm1', 'value'=>$calculatedTax1, 'class'=>'number', 'width'=>'31')) ?>
            <tr>
                <td>
                <td align="center">2.Term
                <td align="left"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Value', 'pk'=>'expectedflowPayedTaxTerm2', 'value'=>$calculatedTax2, 'class'=>'number', 'width'=>'31')) ?>
            <tr>
                <td>
                <td align="center">3.Term
                <td align="left"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Value', 'pk'=>'expectedflowPayedTaxTerm3', 'value'=>$calculatedTax3, 'class'=>'number', 'width'=>'31')) ?>
            <tr>
                <td>
                <td align="center">4.Term
                <td align="left"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'Value', 'pk'=>'expectedflowPayedTaxTerm4', 'value'=>$calculatedTax4, 'class'=>'number', 'width'=>'31')) ?>
            <?
                $totalCalulatedTax = $calculatedTax1 + $calculatedTax2 + $calculatedTax3 + $calculatedTax4;
            ?>
            <tr>
                <td>
                <td align="center">Sum
                <td align="right"><? print $totalCalulatedTax * $choosenPeriod / $year ?>
            <tr height="10">
                <td>
            <tr>
                <td colspan="3">
                    <b>
                    <?
                        $taxDifference = ($totalCalulatedTax * $choosenPeriod / $year) - $totalTax;
                        if($taxDifference == 0)
                            print "Ut i fra opplysningene som er oppgitt,<br>ser det ut til at du går i null på skatten";
                        if($taxDifference > 0)
                            print "Ut i fra opplysningene som er oppgitt,<br>ser det ut som du får tilbakebetaling på: ".$_lib['format']->Amount(array('value'=>$taxDifference, 'return'=>'value'));
                        if($taxDifference < 0)
                            print "Ut i fra opplysningene som er oppgitt,<br>ser det ut som du får restskatt på: ".$_lib['format']->Amount(array('value'=>$taxDifference, 'return'=>'value'));
                    ?>
        </table>
        <br><br><br>
        <table>
            <tr>
                <td>
                <td>
                <td>bokført mnd
                <td>året
                <td>
            <tr>
                <td>Stipulert privatforbruk
                <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalOut, 'return'=>'value')) ?>
                <td align="center"><? print $choosenPeriod ?>
                <td align="center"><? print $year ?>
                <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalOut * $choosenPeriod / $year, 'return'=>'value')) ?>
            <tr>
                <td>Stipulert overskudd
                <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalIn, 'return'=>'value')) ?>
                <td align="center"><? print $choosenPeriod ?>
                <td align="center"><? print $year ?>
                <td align="right"><? print $_lib['format']->Amount(array('value'=>$totalIn * $choosenPeriod / $year, 'return'=>'value')) ?>
            <tr>
                <td>Økning av bedriftens verdi
                <td>
                <td>
                <td>
                <td align="right"><? print $_lib['format']->Amount(array('value'=>($totalIn * $choosenPeriod / $year) - ($totalOut * $choosenPeriod / $year), 'return'=>'value')) ?>
        </table>
        <br>
        <?
            if($totalResult == 0)
                print "<h2><font color=\"\">Du ser ut til å gå i null, pass på så du ikke tar ut for mye</font></h2>";
            elseif($totalResult > 0)
                print "<h2><font color=\"blue\">Du ser ut til å gå positivt</font></h2>";
            elseif($totalResult < 0)
                print "<h2><font color=\"red\">Du tar ut for mye, du ser ut til å gå negativt</font></h2>";
        ?>
        <table border="0" cellspacing="0" width="600">
            <tr>
                <td width="100%" align="right"> <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_oppdater_privatforbruk', 'value'=>'Oppdater/beregn')) ?>
        </table>
    </form>
</body>
</html>