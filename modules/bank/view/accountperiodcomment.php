<?
# $Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no


include 'record.inc';
includelogic('accountperiodcomment/accountperiodcomment');
includemodel('bank/bank');

$apc = new accountperiodcomment();

/* open the comment field for every post */
foreach($apc->AccountH as $AccountID => $AccountName) {
    foreach($apc->PeriodH as $Period => $tmp) {
        if(!$apc->DataH[$AccountID][$Period]->BankVotingPeriodID) {
            $bank = new framework_logic_bank(array('Period' => $Period, 'AccountID' => $AccountID));
            $bank->init();
        }
    }
}

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

    foreach($apc->PeriodH as $Period => $tmp)
    {
        $year = substr($Period, 0, 4);

        if($apc->DataH[$AccountID][$Period]->BankVotingPeriodID)
            $data[$year][$AccountName][$Period] = 
                array(
                'a' => $AccountID,
                'period' => $Period, 
                'pk' => $apc->DataH[$AccountID][$Period]->BankVotingPeriodID,
                'value' => $apc->DataH[$AccountID][$Period]->Comment
                );
        else
            $data[$year][$AccoutName][$Period] = array(
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
        echo "<tr>";
        echo "<td>$account</td>";

        foreach($period as $pname => $d) {
            if(!isset($d['error'])) {
                printf("<td>%s</td>", 
                       $_lib['form3']->text(array('table' => 'bankvotingperiod', 
                                                  'field' => 'Comment', 
                                                  'pk'    => $d['pk'],
                                                  'value' => $d['value']))
                    );
            }
            else {
                echo "<td></td>";
            }
        }

        echo "</tr>";
    }

    echo "<tr><td>$save_button</td></tr>";

    echo "</table>";
}

?>

<? print $_lib['form3']->stop(array()); ?>
</body>
</html>
