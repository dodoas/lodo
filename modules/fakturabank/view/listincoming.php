<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('fakturabank/fakturabank');
includelogic('accounting/accounting');
includelogic('exchange/exchange');

$accounting = new accounting();

require_once "record.inc";
$fb         = new lodo_fakturabank_fakturabank();
$InvoicesO  = $fb->incoming();

print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>


<h2><a href="<? print $_lib['sess']->dispatch ?>t=invoicein.list">Innkommende fakturaer</a> / <a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.listincoming">Hent fakturaer fra fakturabank</a></h2>
<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>


<h3>Alle innkommende fakturaer fra Fakturabank
<? if($_lib['setup']->get_value('fakturabank.status')) { ?>
    med status <? print $_lib['setup']->get_value('fakturabank.status') ?>
<? } ?>
som ikke er lastet ned.
</h3>


<h2>Send dine innscannede innkommende pdf bilag til: <a href="mailto:<? print str_replace(" MVA","",$_lib['sess']->get_companydef('OrgNumber')) ?>@scanning.fakturabank.no"><? print str_replace(" MVA","",$_lib['sess']->get_companydef('OrgNumber')) ?>@scanning.fakturabank.no</a> s&aring; vil de bli punchet og lagt klar for automatisk bilagsf&oslash;ring i fakturabank</h2>
Merk: Du m&aring; registrere brukeren din p&aring; <a href="http://fakturabank.no">http://fakturabank.no</a> for at dette skal fungere

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.listincoming" method="post">
<input type="submit" value="Last ned fakturaer (L)" name="action_fakturabank_registerincoming" accesskey="B">
</form>

<table class="lodo_data">
<thead>
<tr>
    <th class="number">Bilag</th>
    <th class="number">Faktura nr</th>
    <th class="number">Fakturadato</th>
    <th class="number">Periode</th>
    <th class="number">Org Nummer</th>
    <th class="number">Lev. Konto</th>
    <th>Firmanavn</th>
    <th>Motkonto</th>
    <th class="number">Forfallsdato</th>
    <th class="number">Bel&oslash;p</th>
    <th>Avdeling</th>
    <th>Prosjekt</th>
    <th class="number">Bankkonto</th>
    <th class="number">KID</th>
    <th class="number">Utskrift</th>
    <th class="number">Status</th>
</tr>
</thead>
<tbody>
<?
if (!empty($InvoicesO->Invoice)) {

  foreach($InvoicesO->Invoice as $InvoiceO) {
    $TotalCustPrice += $InvoiceO->LegalMonetaryTotal->PayableAmount;
    $tmp_currency_code = $InvoiceO->DocumentCurrencyCode;

  ?>
    <tr class="<? print $InvoiceO->Class ?>">
      <td class="number"><? if($InvoiceO->Journaled) { ?><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$InvoiceO->VoucherType&amp;voucher_JournalID=$InvoiceO->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a><? } else { ?><i><a title="Foresl&aring;tt bilagsnummer - dette kan endre seg"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a></i><? } ?></td>
      <td class="number"><? print $InvoiceO->ID ?></td>
      <td class="number"><? print $InvoiceO->IssueDate ?></td>
      <td class="number"><? print $InvoiceO->Period ?></td>
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&OrgNumber=<? print $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID ?>&inline=show" target="_new"><? print $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID ?></a></td>
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&AccountPlanID=<? print $InvoiceO->AccountPlanID ?>&inline=show" target="_new"><? print $InvoiceO->AccountPlanID ?></a></td>
      <td>&nbsp;<? print substr($InvoiceO->AccountingSupplierParty->Party->PartyName->Name,0,30) ?></td>
      <td>&nbsp;<? print substr($InvoiceO->MotkontoAccountPlanID,0,30) ?></td>
      <td class="number"><b><? print $InvoiceO->PaymentMeans->PaymentDueDate ?></b></td>
      <!--<td class="number"><? print $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ?></td>-->
      <td class="number">
<? if ($tmp_currency_code == exchange::getLocalCurrency()) { ?>
        <? print $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ?>
<? } else {
        $conv = exchange::convertToLocal($tmp_currency_code, $InvoiceO->LegalMonetaryTotal->PayableAmount);
        $rate = exchange::getConversionRate($tmp_currency_code);
        if ($conv) {
            print " (". $tmp_currency_code ." ". $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ." / $rate) ";
            print $_lib['format']->Amount($conv);
        } else {
            $conv = $_lib['format']->Amount($conv);
            print "Valutaverdi for ". $tmp_currency_code ." er ikke satt";
            print " (". $tmp_currency_code ." ". $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) .") ";
       }
   }
?>
      </td>
      <td class="number"><? print $InvoiceO->Department ?></td>
      <td class="number"><? print $InvoiceO->Project ?></td>
      <td class="number"><? print $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID ?></td>
      <td class="number"><? print $InvoiceO->PaymentMeans->InstructionID ?></td>
      <td align="center"><a href="<?php echo $_SETUP['FB_URL'] ?>suppliers/<? print $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID ?>/invoices/<? print str_replace(".", "%2E", rawurlencode($InvoiceO->ID)) ?>" title="Vis faktura i fakturabank" target="_new">Vis</a>
      <td class="number"><? print $InvoiceO->Status ?></td>
  </tr>
<? 
  } 
}
?>
<tr>
    <th colspan="8"></th>
    <th>SUM</th>
    <th class="number"><? print  $_lib['format']->Amount($TotalCustPrice) ?></th>
    <th colspan="6"></th>
</tr>
</tbody>

</table>
</body>
</html>
