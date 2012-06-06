<?
includelogic('fakturabank/fakturabankvoting');

# $Id: import.php,v 1.16 2005/10/24 11:50:24 svenn Exp $ account_import.php,v 1.3 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$AccountNumber  = $_REQUEST['AccountNumber'];
$AccountID      = $_REQUEST['AccountID'];
$Bank      		= $_REQUEST['Bank'];

if($_REQUEST['Period']) {
	$Period     = $_REQUEST['Period'];
    $PeriodSelection = $Period;
} else {
	$Period     = $_lib['date']->get_prev_period(array('value' => $_lib['sess']->get_session('LoginFormDate'), 'realPeriod' => 1));
    $PeriodSelection = $Period;
}

$db_table = "accountline";

if (!is_numeric($AccountID)) {
    echo "Missing account id";
    return;
}

$query   = "select * from account where AccountID=$AccountID";
$account = $_lib['storage']->get_row(array('query' => $query));


# Import bank account files in format: Skandiabanken
# Skb:  BookKeepingDate;InterestDate;UseDate;ArchiveRef;AccountCategory;AccountDescription;AmountOut;AmountIn
# Spb1: Bet.dato;Beskrivelse;Rentedato;Ut/Inn;
# Input: AccountNumber

if (isset($_POST['Period'])) {

    $fbvoting = new lodo_fakturabank_fakturabankvoting();
    $fbvoting->import_transactions($AccountID, $Period);
}

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - fakturabank bankaccount import</title>
    <meta name="cvs"                content="$Id: import.php,v 1.16 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Bilagsf&oslash;r/Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> |
<? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> | 
<? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $AccountID . '&amp;Period=' . $Period)) ?> |
<? print $_lib['form3']->url(array('description' => 'Import fra FakturaBank',          'url' => $_lib['sess']->dispatch . 't=bank.fakturabankimport'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?>

<h2><? print $_lib['message']->get() ?></h2>

<h2><? print $account->AccountNumber ?> - 
<? print $account->AccountDescription ?>
</h2>
<form enctype="multipart/form-data" method="post" action="<? print $MY_SELF ?>" name="pages">
<input type="hidden"    name="AccountID"        value="<? print $AccountID ?>">
<input type="hidden"    name="AccountNumber"    value="<? print $account->AccountNumber ?>">
<br />
Periode: <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'pk' => $AccountID, 'value' => $PeriodSelection, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true)); ?>
<br />
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<input type="submit" name="action_bank_import"  value="Importer">
<? } ?>
</form>
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<p>
<br>
<br>
<br>
<a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationlist">Oppsett av koblinger mellom avstemmings&aring;rsaker og kontoer</a>
</p>
<? } ?>
</body>
</html>
<pre>
