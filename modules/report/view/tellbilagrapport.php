<?
# $Id: tellbilagrapport.php,v 1.10 2005/10/14 13:15:42 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "voucher";

#antall bilag pr type:
#select V.VoucherType, count(V.JournalID) as journals from voucher V where V.VoucherType!='' group by V.VoucherType
#antall posteringer pr bilag

$queryYear = "select substring(Period,1,4) as PeriodYear from accountperiod group by PeriodYear order by PeriodYear desc";
$resultYear = $_lib['db']->db_query($queryYear);

$dataH = array();

$type_query = "select JournalID, VoucherPeriod, VoucherID, VoucherType from voucher V where Active=1 order by V.VoucherPeriod";
$result = $_lib['db']->db_query($type_query);
while($row = $_lib['db']->db_fetch_object($result)) {
    $dataH[$row->VoucherPeriod][$row->VoucherType]['journal'][$row->JournalID] = 1;
    $dataH[$row->VoucherPeriod][$row->VoucherType]['voucher']++;
}

krsort($dataH);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Tell bilag</title>
    <meta name="cvs"                content="$Id: tellbilagrapport.php,v 1.10 2005/10/14 13:15:42 thomasek Exp $" />
    <? includeinc('head'); ?>
    </head>
<body>
    <? includeinc('top'); ?>
    <? includeinc('left'); ?>
    <h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>


    <?php
        ob_start();
    ?>
    <b>Bilag pr mnd (parentes er antall linjer)</b>
    <table width="50%">
        <thead>
            <tr>
                <th colspan="8">Tell bilag rapport</th>
            </tr>
            <tr>
                <th>Periode</th>
                <th>Kasse</th>
                <th>Bank</th>
                <th>Utgift</th>
                <th>Salg</th>
                <th>L&oslash;nn</th>
                <th>Ukeomsetning</th>
                <th>Auto</th>
            </tr>
            <? foreach($dataH as $period => $dataH) { 
                $year = substr($period,0,4);
                $yearH[$year]['K']->journal += count($dataH['K']['journal']);
                $yearH[$year]['B']->journal += count($dataH['B']['journal']);
                $yearH[$year]['U']->journal += count($dataH['U']['journal']);
                $yearH[$year]['S']->journal += count($dataH['S']['journal']);
                $yearH[$year]['L']->journal += count($dataH['L']['journal']);
                $yearH[$year]['O']->journal += count($dataH['O']['journal']);
                $yearH[$year]['A']->journal += count($dataH['A']['journal']);

                $yearH[$year]['K']->voucher += $dataH['K']['voucher'];
                $yearH[$year]['B']->voucher += $dataH['B']['voucher'];
                $yearH[$year]['U']->voucher += $dataH['U']['voucher'];
                $yearH[$year]['S']->voucher += $dataH['S']['voucher'];
                $yearH[$year]['L']->voucher += $dataH['L']['voucher'];
                $yearH[$year]['O']->voucher += $dataH['O']['voucher'];
                $yearH[$year]['A']->voucher += $dataH['A']['voucher'];
                ?>
                <tr>
                <td><? print $period ?></td>
                <td><? if(count($dataH['K']['journal'])) print count($dataH['K']['journal']) ?> <? if($dataH['K']['voucher']) { ?>(<? print $dataH['K']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['B']['journal'])) print count($dataH['B']['journal']) ?> <? if($dataH['B']['voucher']) { ?>(<? print $dataH['B']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['U']['journal'])) print count($dataH['U']['journal']) ?> <? if($dataH['U']['voucher']) { ?>(<? print $dataH['U']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['S']['journal'])) print count($dataH['S']['journal']) ?> <? if($dataH['S']['voucher']) { ?>(<? print $dataH['S']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['L']['journal'])) print count($dataH['L']['journal']) ?> <? if($dataH['L']['voucher']) { ?>(<? print $dataH['L']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['O']['journal'])) print count($dataH['O']['journal']) ?> <? if($dataH['O']['voucher']) { ?>(<? print $dataH['O']['voucher'] ?>)<? } ?></td>
                <td><? if(count($dataH['A']['journal'])) print count($dataH['A']['journal']) ?> <? if($dataH['A']['voucher']) { ?>(<? print $dataH['A']['voucher'] ?>)<? } ?></td>
                </tr>
            <? } ?>
    </table>

    <?php
         $first_table = ob_get_contents();
         ob_end_clean();
    ?>


    <b>Bilag pr &aring;r (parentes er antall linjer)</b>
    <table width="50%">
        <thead>
            <tr>
                <th colspan="9">Tell bilag rapport</th>
            </tr>
            <tr>
                <th>Periode</th>
                <th>Kasse</th>
                <th>Bank</th>
                <th>Utgift</th>
                <th>Salg</th>
                <th>L&oslash;nn</th>
                <th>Ukeomsetning</th>
                <th>Auto</th>
                <th>Totalt</th>
            </tr>
            <? foreach($yearH as $year => $dataH) { ?>
                <tr>
                <td><? print $year ?></td>
                <td><? if(count($dataH['K']->journal)) print $dataH['K']->journal ?> <? if($dataH['K']->voucher) { ?>(<? print $dataH['K']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['B']->journal)) print $dataH['B']->journal ?> <? if($dataH['B']->voucher) { ?>(<? print $dataH['B']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['U']->journal)) print $dataH['U']->journal ?> <? if($dataH['U']->voucher) { ?>(<? print $dataH['U']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['S']->journal)) print $dataH['S']->journal ?> <? if($dataH['S']->voucher) { ?>(<? print $dataH['S']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['L']->journal)) print $dataH['L']->journal ?> <? if($dataH['L']->voucher) { ?>(<? print $dataH['L']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['O']->journal)) print $dataH['O']->journal ?> <? if($dataH['O']->voucher) { ?>(<? print $dataH['O']->voucher ?>)<? } ?></td>
                <td><? if(count($dataH['A']->journal)) print $dataH['A']->journal ?> <? if($dataH['A']->voucher) { ?>(<? print $dataH['A']->voucher ?>)<? } ?></td>
                <td><? print ($dataH['K']->journal + $dataH['B']->journal + $dataH['U']->journal + $dataH['S']->journal + $dataH['L']->journal + $dataH['O']->journal + $dataH['A']->journal) ?> (<? print ($dataH['K']->voucher + $dataH['B']->voucher + $dataH['U']->voucher + $dataH['S']->voucher + $dataH['L']->voucher + $dataH['O']->voucher + $dataH['A']->voucher) ?>)</td>
                </tr>
            <? } ?>
    </table>

    <?php

        echo $first_table;

    ?>



</body>
</html>
