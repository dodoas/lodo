<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('accounting/accounting');
$accounting = new accounting();

includelogic('ocr/ocr');
$ocr         = new lodo_ocr_ocr();
$dataH 		 = $ocr->preprocess();

if($_lib['input']->getProperty('action_ocr_journal')) {
    $ocr->journal();
}

print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2>Alle innkommende OCR / KID transaksjoner som ikke er bilagsf&oslash;rt - klare for automatisk bilagsf&oslash;ring</h2>

<? print $_lib['message']->get() ?>

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=ocr.list" method="post">
<input type="hidden" value="edit" name="inline">

<input type="submit" value="Hent OCR fra BBS (G)" name="action_ocr_retrieve" accesskey="B">
<input type="submit" value="Bilagsf&oslash;r automatisk (B)" name="action_ocr_journal" accesskey="B">
</form>

<?
/* Vi må sjekke hva som er bilagsført fra før. */
?>

<table class="lodo_data">
<thead>
<tr>
    <th align="right">Dato</th>
    <th align="right" colspan="2">Kundekonto</th>
    <th align="right">Bel&oslash;p</th>
    <th align="right">KID</th>
    <th align="right">Debetkonto</th>
    <th align="right">Bankkonto</th>
    <th align="right">Bilag</th>    
    <th align="right">L&oslash;penummer</th>
    <th align="right">Arkivreferanse</th>
    <th align="right">Oppdaragsdato</th>
    <th align="right">Status</th>
</tr>
</thead>
<tbody>
<? foreach($dataH as $bankaccount => $transactionsH) {
	?>
    <tr class="<? print $InvoiceO->Class ?>">
		<td colspan="5"><b>Transaksjoner p&aring; bankkonto:<? print $bankaccount ?></b></td>
	</tr>
	<? foreach($transactionsH as $tmp => $transactionO) { 
		$total += $transactionO->belop;
		?>
    <tr class="<? print $transactionO->Class ?>">
		<td><? print $transactionO->oppgjorsdato ?></td>
		<td><? print $transactionO->CustomerAccountPlanID ?></td>
		<td><? print $transactionO->CustomerAccountPlanName ?></td>
		<td class="number"><? print $_lib['format']->Amount($transactionO->belop) ?></td>
		<td><? print $transactionO->kid ?></td>
		<td><? print $transactionO->debetkonto ?></td>
		<td><? print $transactionO->BankAccountPlanID ?></td>
		<td class="number"><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$transactionO->VoucherType&amp;voucher_JournalID=$transactionO->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $transactionO->VoucherType ?><? print $transactionO->JournalID ?></a></td>
		<td class="number"><? print $transactionO->lopenummer ?></td>
		<td><? print $transactionO->arkivreferanse ?></td>
		<td><? print $transactionO->oppdragsdato ?></td>
		<td><? print $transactionO->Status ?></td>
  	</tr>
	<? } ?>
<? } ?>
<tr>
    <th colspan="2"></th>
    <th>SUM</th>
    <th class="number"><? print  $_lib['format']->Amount($total) ?></th>
    <th colspan="8"></th>
</tr>
</tbody>
</table>
<?php
/*
#Sjekke om vi har match p&aring; kontonummer	 - kontoplan<br />
#Sjekke om vi har match p&aring; KID (kun blandt &aring;pne poster) - kontoplan (Hvor langt tilbake i tid skal vi lete)<br />
#Sjekke om transaksjonen er bilagsf&oslash;rt fra f&oslash;r? Varsle slik at den ikke bilagsf&oslash;res p&aring; nytt.<br />
#Sjekke om xxxxxx<br />
#Hva skal telle mest i bilagsf&oslash;ringen - match pŒ bankkontonummer eller match p&aring; KID?
*/
?>

</body>
</html>

