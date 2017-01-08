<?
# $Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no


include 'record.inc';
includelogic('accountperiodcomment/accountperiodcomment');
includemodel('bank/bank');

$apc = new accountperiodcomment();

?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1><a href="<? print $_lib['sess']->dispatch ?>t=bank.list">Kasse/bank-avstemming</a> <br />
<a href="<? print $_lib['sess']->dispatch ?>t=bank.accountperiodcomment"><b>Brukte bilagsnummer</b></a></h1>

<? print $_lib['form3']->start(array('name' => 'accountperiodcomment')); ?>

<?php

$data = array();
$save_button = $_lib['form3']->submit(array('name' => 'action_bank_commentupdate', 'value' => 'Lagre', 'accesskey' => 'S'));

foreach($apc->AccountH as $AccountID => $AccountName) {

    foreach($apc->PeriodH as $tmp => $Period)
    {
        $year = substr($Period, 0, 4);

        if($apc->DataH[$AccountID][$Period]->BankVotingPeriodID)
            $data[$year][$AccountName['AccountNumber']][$Period] =
                array(
                'a' => $AccountID,
                'period' => $Period,
                'pk' => $apc->DataH[$AccountID][$Period]->BankVotingPeriodID,
                'value' => $apc->DataH[$AccountID][$Period]->Comment
                );
        else
            $data[$year][$AccountName['AccountNumber']][$Period] = array(
                'a' => $AccountID,
                'error' => true
                );
    }
}

foreach($data as $year => $accounts) {
    echo "<h1>$year</h1>";

    echo "<table class='lodo_data'>";
    echo "<tr>";
    echo "<th>Konto</th>";

    foreach($accounts[key($accounts)] as $pname => $d)
        echo "<th>$pname</th>";

    echo "</tr>";

    foreach($accounts as $account => $period) {
        $acc_id = $period[key($period)]['a'];
        if (isset($apc->AccountExp[$acc_id]) && $year > date('Y', strtotime($apc->AccountExp[$acc_id]))) continue;
        echo "<tr>";
        echo "<td>$account<br><br><br></td>";

        foreach($period as $pname => $d) {
            if(!isset($d['error'])) {
              $query_max_min = "SELECT MAX(JournalID) AS max, MIN(JournalID) AS min, COUNT(JournalID) as count FROM accountline WHERE AccountID = " . $d['a'] . " AND Active = 1 AND Period = '$pname'";
              $max_min = $_lib['db']->get_row(array("query" => $query_max_min));
              $query_acc_voucher_type = "SELECT VoucherType FROM account WHERE AccountID = " . $d['a'];
              $voucher_type = $_lib['db']->get_row(array("query" => $query_acc_voucher_type))->VoucherType;
              $query_max_min_voucher = "SELECT MAX(VoucherID) AS max, MIN(VoucherID) AS min, COUNT(VoucherID) as count FROM voucher WHERE AccountPlanID = " . $apc->AccountH[$d['a']]['AccountPlanID'] . " AND VoucherType = '" . $voucher_type . "' AND Active = 1 AND VoucherPeriod = '$pname'";
              $max_min_voucher = $_lib['db']->get_row(array("query" => $query_max_min_voucher));
              $_done_journals = "<br>K $max_min->count " . $voucher_type . " " . $max_min->min . "-" . $max_min->max;
              $_done_journals .= "<br>R $max_min_voucher->count " . $voucher_type . " " . $max_min_voucher->min . "-" . $max_min_voucher->max;
              $comment_input = $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'Comment', 'pk' => $d['pk'], 'value' => $d['value']));
              if (isset($apc->AccountExp[$acc_id]) && $pname > date('Y-m', strtotime($apc->AccountExp[$acc_id]))) $comment_input = "Avsluttet";
              printf("<td>%s</td>", $comment_input . $_done_journals);
            }
            else {
                echo "<td></td>";
            }
        }

        echo "</tr>";
    }

    echo "<tr><td colspan='6'>$save_button</td><td>$save_button</td></tr>";

    echo "</table>";
}

?>

<? print $_lib['form3']->stop(array()); ?>
</body>
</html>
