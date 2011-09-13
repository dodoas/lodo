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

<table class="lodo_data">
<thead>
  <tr>
    <th style="width: 70px;"><b>konto</b></th>
    <? foreach($apc->PeriodH as $Period => $tmp) { ?>
        <th style="width: 70px; text-align: center;"><b><? print $Period ?></b></th>
    <? } ?>
  </tr>
</thead>
<tbody>

<? foreach($apc->AccountH as $AccountID => $AccountName) { 
    //$bankname = $_lib['storage']->get_row(array('query' => "SELECT BankName FROM account WHERE AccountID = $AccountID"));
    ?>
    <tr>
    <td><? print $AccountName ?></td>
    <? foreach($apc->PeriodH as $Period => $tmp) { ?>
        <td>
    <? 
       if($apc->DataH[$AccountID][$Period]->BankVotingPeriodID) 
           print $_lib['form3']->text(array('table' => 'bankvotingperiod', 
                                            'field' => 'Comment', 
                                            'pk'    => $apc->DataH[$AccountID][$Period]->BankVotingPeriodID, 
                                            'value' => $apc->DataH[$AccountID][$Period]->Comment));
       else
           print '&aring;pn;';
    ?>        
        </td>
    <? } ?>
    </tr>
<? } ?>
<?php 
         $save_button = $_lib['form3']->submit(array('name' => 'action_bank_commentupdate', 'value' => 'Lagre', 'accesskey' => 'S'));
$iter = 0;
?>
  <tr>
    <? foreach($apc->PeriodH as $Period => $tmp) { ?>
    <td><?php if ($iter++ % 6 == 0) print $save_button ?></td>
    <? } ?>
  </tr>
</tbody>
</table>

<? print $_lib['form3']->stop(array()); ?>
</body>
</html>
