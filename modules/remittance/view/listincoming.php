<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('accounting/accounting');
includelogic('remittance/remittance');

$accounting = new accounting();

$rem    = new logic_remittance_remittance($_lib['input']->request);
$rem->fill(array());

require_once "record.inc";


print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2>Betal regningene din her
<? print $_lib['message']->get() ?>
Her listes bare fakturaer som skal betales (ikke kredittnotaer). Fakturaer som er registrert betalt med kort eller kontant vil ikke bli listet i remittering - siden de ikke skal betales i banken.

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=remittance.listincoming" method="post">
<table>
<tr>
    <td>Fra:</td>
    <td><? print $_lib['form3']->date(array('name' => 'FromDate',                  'value' => $rem->FromDate)) ?></td>
</tr>
<tr>
    <td>Til:</td> 
    <td><? print $_lib['form3']->date(array('name' => 'ToDate',                    'value' => $rem->ToDate)) ?></td>
</tr>
<tr>
    <td>Status: </td>
    <td><? print $_lib['form3']->text(array('name' => 'RemittanceStatus',       'value' => $rem->RemittanceStatus)) ?></td>
</tr>
<tr>
    <td>Fakturanummer:</td> 
    <td><? print $_lib['form3']->text(array('name' => 'InvoiceNumber',   'value' => $rem->InvoiceNumber)) ?></td>
</tr>
<tr>
    <td>Navn:</td> 
    <td><? print $_lib['form3']->text(array('name' => 'IName',   'value' => $rem->IName)) ?></td>
</tr>
<tr>
    <td>Remittert:</td>
    <td><? print $_lib['form3']->checkbox(array('name' => 'Remitted',        'value' => $rem->Remitted)) ?></td>
</tr>
<tr>
    <td><? print $_lib['form3']->submit(array('name' => 'show_search',                  'value' => 'S&oslash;k (S)')) ?></td>
</tr>
</table>
</form>

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=remittance.listincoming" method="post">
<table class="lodo_data">
<thead>
<tr>
    <th align="right">Godkjenn</th>
    <th align="right">Fakturadato</th>
    <th align="right">Faktura nr</th>
    <th>Leverand&oslash;r</th>
    <th>Firmanavn</th>
    <th align="right">Forfallsdato</th>
    <th align="right">Bel&oslash;p</th>
    <th>Fra bankkonto</th>
    <th>Til bankkonto</th>
    <th align="right">Betal</th>
    <th align="right">Betaling</th>
    <th align="right">KID</th>
    <th align="right">Bilag</th>
    <th align="right">Remittering</th>
    <th align="right">Status</th>
</tr>
</thead>
<tbody>
<?
foreach($rem as $InvoiceO) {
    $TotalCustPrice += $InvoiceO->TotalCustPrice;
    ?>
    <tr class="<? print $InvoiceO->Class ?>">
      <td class="number"><? print $_lib['form3']->checkbox(array()) ?></td>
      <td class="number"><? print $InvoiceO->InvoiceDate ?></td>
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=invoicein.edit&ID=<? print $InvoiceO->ID ?>" title="Endre faktura"><? print $InvoiceO->InvoiceNumber ?></a></td>
      <td class="number"><? print $InvoiceO->SupplierAccountPlanID ?></td>
      <td><? print $InvoiceO->IName ?></td>
      <td class="number"><? print $InvoiceO->DueDate ?></td>
      <td class="number"><? print $_lib['format']->Amount($InvoiceO->TotalCustPrice) ?></td>
      <td class="number"><? print $InvoiceO->CustomerBankAccount ?></td>
      <td class="number"><? print $InvoiceO->SupplierBankAccount ?></td>
      <td class="number"><? print $_lib['form3']->input(array('name' => 'RemittanceAmount', 'value' => $_lib['format']->Amount($InvoiceO->RemittanceAmount), 'class' => 'number')); ?></td>
      <td class="number"><? print $InvoiceO->PaymentMeans ?></td>
      <td class="number"><? print $InvoiceO->KID ?></td>
      <td class="number"><? if($InvoiceO->Journaled) { ?><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$InvoiceO->VoucherType&amp;voucher_JournalID=$InvoiceO->ID"; ?>&amp;action_journalid_search=1" target="_new"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a><? } else { ?><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?><? } ?></td>
      <td class="number"><? print $InvoiceO->RemittanceStatus ?></td>
      <td class="number"><? print $InvoiceO->Status ?></td>
  </tr>
<? } ?>
</tbody>
<tr>
    <th colspan="7"></th>
    <th>SUM</th>
    <th class="number"><? print  $_lib['format']->Amount($TotalCustPrice) ?></th>
    <th colspan="6"></th>
</tr>
<tr>
    <td colspan="8"></td>
    <td colspan="3" class="number"><input type="submit" value="Lagre (S)" name="action_remittance_update" accesskey="S"></td>
</tr>
</table>
</form>

<a href="<? print $_lib['sess']->dispatch ?>t=remittance.file&amp;RemittanceStatus=<? print $rem->RemittanceStatus ?>&amp;FromDate=<? print $rem->FromDate ?>&amp;ToDate= <? print $rem->ToDate ?>&amp;InvoiceNumber=<? print $rem->InvoiceNumber ?>&amp;IName=<? print $rem->IName ?>" title="Klikk for Œ hente fila">Direkte remittering - Telepay 2.0.1 fil</a>
Denne filen skal s&aring; lastes opp i din Bedriftsnettbank, og transaksjonene kan godkjennes direkte der.

</body>
</html>