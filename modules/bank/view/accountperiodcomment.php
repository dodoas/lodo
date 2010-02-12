<?
# $Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no


include 'record.inc';
includelogic('accountperiodcomment/accountperiodcomment');
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

<h1><a href="<? print $_lib['sess']->dispatch ?>t=bank.list">Kasse/bank-avstemming</a> / <a href="<? print $_lib['sess']->dispatch ?>t=bank.accountperiodcomment"><b>Konto periode kommentar</b></a></h1>

<? print $_lib['form3']->start(array('name' => 'accountperiodcomment')); ?>
<table class="lodo_data">
<thead>
  <tr>
    <th></th>
    <? foreach($apc->AccountH as $AccountID => $AccountName) { ?>
        <th><? print $AccountName ?></th>
    <? } ?>
  </tr>
</thead>
<tbody>
<? foreach($apc->PeriodH as $Period => $tmp) { 
    $i++;
    if (!($i % 3)) { $sec_color = "r0"; } else { $sec_color = "r1"; }; ?>
    <tr class="<? print $sec_color ?>">
    <td><b><? print $Period ?></b></td>
    <? foreach($apc->AccountH as $AccountID => $AccountName) { ?>
    <td><? if($apc->DataH[$AccountID][$Period]->BankVotingPeriodID) print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'Comment', 'pk' => $apc->DataH[$AccountID][$Period]->BankVotingPeriodID, 'value' => $apc->DataH[$AccountID][$Period]->Comment)) ?></td>
    <? } ?>
</tr>
<? } ?>
</tbody>
</table>
<? print $_lib['form3']->submit(array('name' => 'action_bank_commentupdate', 'value' => 'Lagre', 'accesskey' => 'S')); ?>
<? print $_lib['form3']->stop(array()); ?>
</body>
</html>