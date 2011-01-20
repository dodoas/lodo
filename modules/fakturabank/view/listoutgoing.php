<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('accounting/accounting');
includelogic('fakturabank/fakturabank');

$accounting = new accounting();
require_once "record.inc";

$fb         = new lodo_fakturabank_fakturabank();
$InvoicesO  = $fb->outgoing();

print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2><a href="<? print $_lib['sess']->dispatch ?>t=invoice.listoutgoing">Faktura - Liste</a> / <a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.listoutgoing">Hent utg&aring;ende fakturaer fra fakturabank</a></h2>
<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>

<h3>Alle utg&aring;ende fakturaer fra Fakturabank som ikke er registrert</h3>

<h2>Send dine innscannede utg&aring;ende pdf bilag til: <a href="mailto:<? print $_lib['sess']->get_companydef('OrgNumber') ?>@scanning.fakturabank.no"><? print $_lib['sess']->get_companydef('OrgNumber') ?>@scanning.fakturabank.no</a> s&aring; vil de bli punchet og lagt klar for automatisk bilagsf&oslash;ring i fakturabank</h2>
Merk: Du m&aring; registrere brukeren din p&aring; <a href="http://fakturabank.no">http://fakturabank.no</a> for at dette skal fungere


<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.listoutgoing" method="post">
<input type="submit" value="Registrer utg&aring;ende fakturaer (R)" name="action_fakturabank_registeroutgoing" accesskey="R">
<input type="submit" value="Opprett manglende kontoplaner (A)" name="action_fakturabank_addmissingaccountplan" accesskey="A">
</form>

<?
if(is_array($InvoicesO->Invoice))
{
 ?>
<table class="lodo_data">
<thead>
<tr>
    <th align="right">Faktura nr</th>
    <th align="right">Bilag</th>
    <th align="right">Fakturadato</th>
    <th align="right">Periode</th>
    <th align="right">Org Nummer</th>
    <th align="right">Konto</th>
    <th>Til</th>
    <th align="right">Forfallsdato</th>
    <th align="right">Bel&oslash;p</th>
    <th align="right">KID</th>
    <th align="right">Utskrift</th>
    <th align="right">Status</th>
</tr>
</thead>
<tbody>
<?
  foreach($InvoicesO->Invoice as $InvoiceO) {
    $TotalCustPrice += $InvoiceO->LegalMonetaryTotal->PayableAmount;
    $count++;

    if (!empty($InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID)) {
        $party_id = $InvoiceO->AccountingCustomerParty->Party->PartyLegalEntity->CompanyID;
    } else {
        $party_id = $InvoiceO->AccountingCustomerParty->Party->PartyIdentification->ID; //checked
    }

    ?>
      <tr class="<? print $InvoiceO->Class ?>">
        <td class="number"><? print $InvoiceO->ID ?></td>
        <td class="number"><? if($InvoiceO->Journaled) { ?><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$InvoiceO->VoucherType&amp;voucher_JournalID=$InvoiceO->ID"; ?>&amp;action_journalid_search=1" target="_new"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a><? } else { ?><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?><? } ?></td>
        <td class="number"><? print $InvoiceO->IssueDate ?></td>
        <td class="number"><? print substr($InvoiceO->IssueDate, 0, 7) ?></td>
        <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&OrgNumber=<? print $party_id ?>&inline=show" target="_new"><? print $party_id ?></a></td>
        <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&AccountPlanID=<? print $InvoiceO->AccountPlanID ?>&inline=show" target="_new"><? print $InvoiceO->AccountPlanID ?></a></td>
        <td>&nbsp;<? print substr($InvoiceO->AccountingCustomerParty->Party->PartyName->Name,0,30) ?></td>
        <td class="number"><b><? print $InvoiceO->PaymentMeans->PaymentDueDate ?></b></td>
        <td class="number"><? print $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ?></td>
        <td class="number"><? print $InvoiceO->PaymentMeans->InstructionID ?></td>
        <td><a href="<?php echo $_SETUP['FB_URL'] ?>invoices/<? str_replace(".", "%2E", rawurlencode(print $InvoiceO->FakturabankID)) ?>" title="Vis/SkrivUt faktura for produkt" target="_new">Vis</a>
        <td><? print $InvoiceO->Status ?></td>
    </tr>
<? 
  }

 ?>
<tr>
    <th colspan="7">Antall: <? print $count ?></th>
    <th>SUM</th>
    <th class="number"><? print  $_lib['format']->Amount($TotalCustPrice) ?></th>
    <th colspan="4"></th>
</tr>

</tbody>

</table>
<? 
} 
?>
</body>
</html>


