<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
includelogic('bank/bank');
$bank = new framework_logic_bank($_lib['input']->request);

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

<form name="template_update" name="period_choice" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>

<table class="lodo_data">
    <tr class="result">
        <th colspan="13">
        Velg periode <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
    
        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?>

        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
</form>

<form name="template_update"  name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID',        'value' => $bank->AccountID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',    'value' => $bank->ThisPeriod)) ?>

  <th colspan="13">Avstemming f&oslash;rst i m&aring;neden</th>
  <tr>
   <td class="menu" colspan="2">Dato</td>
    <td class="menu">Inn</td>
    <td class="menu">Ut</td>
    <td class="menu">KID</td>
    <td class="menu">Faktura</td>
    <td class="menu">Bilagsnr</td>
    <td class="menu" colspan="7"></td>
  </tr>
  <tr>
    <td colspan="2">Saldo fra <? print $bank->ThisPeriod ?>-01</td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankvotingperiod->AmountIn)  ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankvotingperiod->AmountOut) ?></td>
  </tr>
  <tr>
    <th class="menu" colspan="7">Tilbakef&oslash;re - f&oslash;rt bank ikke regnskap</th>
    <td class="menu">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_bank_votinglinenewtilbake" value="Ny linje" accesskey="M">
    <? } ?>
    </td>
  </tr>
<? 
if(is_array($bank->bankvote_tilbake)) {
    foreach($bank->bankvote_tilbake as $bankvote) { ?>
<tr>
    <td colspan="2"><? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'VoucherDate',  'pk' => $bankvote->BankVotingLineID, 'value' =>$bankvote->VoucherDate,  'class' => 'number')) ?></td>    
    <td>
    <? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'AmountIn',     'pk' => $bankvote->BankVotingLineID, 'value' =>$_lib['format']->Amount($bankvote->AmountIn),     'class' => $bankvote->classAmountIn)) ?>
    <? print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountIn&amp;searchstring=' . $bankvote->AmountIn, 'description' => 'S')) ?>
    </td>
    <td>
    <? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'AmountOut',    'pk' => $bankvote->BankVotingLineID, 'value' =>$_lib['format']->Amount($bankvote->AmountOut),    'class' => $bankvote->classAmountOut)) ?>
    <? print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountOut&amp;searchstring=' . $bankvote->AmountOut, 'description' => 'S')) ?>
    </td>    
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'KID',          'pk' => $bankvote->BankVotingLineID, 'value' =>$bankvote->KID,          'class' => 'number', 'width' => 22)) ?></td>
    <td></td>
    <td><? if($bank->is_closeable($bankvote->KID)) print "Lukket"?></td>
</tr>
<? }
} ?>
  <tr>
    <th class="menu" colspan="7">Tilleggsf&oslash;re - f&oslash;rt regnskap ikke bank</th>
    <td class="menu"><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_bank_votinglinenewtillegg" value="Ny linje" accesskey="M">
    <? } ?>
    </td>
  </tr>
<?
if(is_array($bank->bankvote_tillegg)) {
    foreach($bank->bankvote_tillegg as $bankvote) { ?>
<tr>
    <td colspan="1"><? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'VoucherDate',  'pk' => $bankvote->BankVotingLineID, 'value' =>$bankvote->VoucherDate,  'class' => 'number')) ?></td>    
    <td>
    <? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'AmountIn',     'pk' => $bankvote->BankVotingLineID, 'value' =>$_lib['format']->Amount($bankvote->AmountIn),     'class' => $bankvote->classAmountIn)) ?>
    <? print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=bank&amp;side=AmountIn&amp;searchstring=' . $bankvote->AmountIn, 'description' => 'S')) ?>
    </td>
    <td>
    <? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'AmountOut',    'pk' => $bankvote->BankVotingLineID, 'value' =>$_lib['format']->Amount($bankvote->AmountOut),    'class' => $bankvote->classAmountOut)) ?>
    <? print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=bank&amp;side=AmountOut&amp;searchstring=' . $bankvote->AmountOut, 'description' => 'S')) ?>
    </td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'KID',           'pk' => $bankvote->BankVotingLineID, 'value' =>$bankvote->KID,            'class' => 'number', 'width' => 20)) ?></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingline', 'field' => 'InvoiceNumber', 'pk' => $bankvote->BankVotingLineID, 'value' =>$bankvote->InvoiceNumber,  'class' => 'number', 'width' => 20)) ?></td>
    <td><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvote->JournalID . '&amp;voucher_VoucherType=' . $bankvote->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvote->VoucherType . $bankvote->JournalID)) ?></td>
    <td><? if($bank->is_closeable($bankvote->KID)) print "Lukket"?></td>
</tr>
<? }
} ?>
<tr>
    <td colspan="1"></td>
    <td class="number">Sum</td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankvotingperiod->topAmountOut)  ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankvotingperiod->topAmountIn)  ?></td>
    <td colspan="9"></td>
</tr>
<tr>
    <td colspan="1"></td>
    <td class="number" colspan="2">Saldo hovedbok <? print $bank->ThisPeriod ?>-01</td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankvotingperiod->topAmountSaldo)  ?></td>
    <td colspan="9"></td>
</tr>


<tr>
    <td class="menu" colspan="7"></td>
    <td class="menu" colspan="2">
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
