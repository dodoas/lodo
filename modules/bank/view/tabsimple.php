<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includelogic('bank/bank');
includelogic('accounting/accounting');

$bank           = new framework_logic_bank($_lib['input']->request);
$accounting     = new accounting();

require_once "record.inc";

$bank->init(); #Read data
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - avstemming av bank</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1>Enklere enn dette kan det ikke gj&oslash;res!!</h1>

<form name="template_update" name="period_choice" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>

Neste ledige Bank (B) bilagsnummer: <? print $_lib['sess']->get_companydef('VoucherBankNumber'); ?>

<table class="lodo_data">
    <tr class="result">
        <td colspan="10">
        Velg periode <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
    
        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?>
    </tr>
    <tr class="result">
        <th colspan="10">
        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
</form>
<form name="template_update"  name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',    'value' => $bank->ThisPeriod)) ?>
<tr>
  <td colspan="2">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="text" name="numnewlines" value="0" size="3" class="number">
        <input type="submit" name="action_bank_accountlinenew" value="Nye linjer (N)" accesskey="N" tabindex="10000">
    <? } ?>
  </td>
  <td colspan="5"></td>
  <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S" tabindex="1">
      <? } ?>
  </td>
</tr>
<tr class="red">
    <td colspan="8">
        <? if(round($bank->bankvotingperiod->topAmountSaldo,2) != round($bank->voucher->saldo,2)) { ?>
        <b>Det er differanse mellom summen av tilbakef&oslash;rte + tilleggsf&oslash;rte bilag (<? print $bank->bankvotingperiod->topAmountSaldo ?>) og summen av transaksjoner p&aring; kto <? print $bank->AccountPlanID ?> (<? print $bank->voucher->saldo ?>) : <? print round($bank->bankvotingperiod->topAmountSaldo - $bank->voucher->saldo, 2) ?></b>
        <? } ?>
        </td>
</tr>
</tr>
  <tr>
    <td class="menu">Dag</td>
    <td class="menu">Ut av konto</td>
    <td class="menu">Inn p&aring; konto</td>
    <td class="menu">KID</td>
    <td class="menu">Tekst</td>
    <td class="menu">Orgnummer</td>
    <td class="menu">Mengde</td>
    <td class="menu">Reskontronavn</td>
  </tr>
  <tr>
    <td>Saldo</td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountOut', 'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountOut),     'class' => 'number')) ?></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountIn',  'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountIn),      'class' => 'number')) ?></td>
    <td colspan="5" class="red">Saldo fra forrige mnd (<? print $bank->PrevPeriod ?>): <? print $_lib['format']->Amount($bank->prevbankaccountcalc->AmountSaldo) ?> <? if($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo != 0) { print "Saldo differanse " . $_lib['format']->Amount($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo); } ?></td>
  </tr>
<?
##############################################################################################################################
#Main loop

if(is_array($bank->bankaccount)) {
    foreach($bank->bankaccount as $row) { 
        $i++;

        $reskontroaccountplan   = $accounting->get_accountplan_object($row->ReskontroAccountPlanID);
        if($reskontroaccountplan) {
            $reskontroclass = 'green';
        } else {
            $reskontroclass = 'warning';        
        }

        $aconf = array();
        $aconf['table']         = 'accountline';
        $aconf['pk']            = $row->AccountLineID;

        $reskontroconf = $resultconf = $aconf;
    
	if(is_array($bank->bankvoucher_this_hash))
        $bankvoucher = array_pop($bank->bankvoucher_this_hash);
    
        if (!($i % 3)) { $sec_color = "r0"; } else { $sec_color = "r1"; };
        ?>
      <tr class="<? print $sec_color ?>">
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Day', 'pk' => $row->AccountLineID, 'value' => $row->Day, 'class' => 'number', 'width' => 2)) ?></td>
        <td class="<? print $bank->CreditColor ?>">
        <? 
            if($row->AmountOut > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountOut), 'class' => $row->classAmountOut));
            else
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountOut));
        ?>
        </td>
        <td class="<? print $bank->DebitColor ?>">
            <? 
            if($row->AmountIn > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountIn),     'class' => $row->classAmountIn));
            else 
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountIn));
    
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=bank&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">')) ?>
        </td>
        <td>
            <? 
            if(count($row->MatchSelect) >= 1) {
                print $_lib['form3']->select(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID, 'data' => $row->MatchSelect, 'width' => 30));
            } else {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID,     'class' => 'number', 'width' => 22));
            }
            ?>
        </td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Description',        'pk' => $row->AccountLineID, 'value' => $row->Description,        'width' => 12, 'maxlength' => 255)) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'ReskontroOrgNumber', 'pk' => $row->AccountLineID, 'value' => $row->ReskontroOrgNumber, 'width' => 12, 'maxlength' => 255)) ?></td>
        <td>
        <?
            $reskontroconf['field']         = 'ReskontroAccountPlanID';
            $reskontroconf['value']         = $row->ReskontroAccountPlanID;
            print $_lib['form3']->accountplan_number_menu($reskontroconf);    
        ?>
        </td>
        <td>
            <? 
            #if($resultaccountplan->EnableQuantity) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'ResultQuantity',        'pk' => $row->AccountLineID, 'value' => $row->ResultQuantity,         'width' => 5, 'maxlength' => 255));
            #}
            ?>
        </td>
    
        <td class="<? print $reskontroclass ?>"><? print $reskontroaccountplan->AccountName ?></td>
      </tr>
    <?
        $sumin  += $row->AmountIn;
        $sumout += $row->AmountOut;        
    }
}
##############################################################################################################################
?>
<tr>
    <td></td>
    <td>Saldo <? print $_lib['date']->get_last_day_in_month($bank->ThisPeriod) ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountSaldo)  ?></td>
    <td colspan="5"></td>
</tr>
<tr>
    <td class="menu" colspan="7"></td>
    <td class="menu">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S">
        <? } ?>
    </td>
</tr>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>