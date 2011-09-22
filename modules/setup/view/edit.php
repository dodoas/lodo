<?
/* $Id: main.php,v 1.58 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$db_table = "setup";
if($_SETUP['VERSION'] == 1) {
	require_once $_SETUP['HOME_DIR']."/interface_custom/lodo1/html/vat/record.inc";
} else {
    require_once $_SETUP['HOME_DIR']."/modules/vat/view/record.inc";
}
require_once "record.inc";

$query_setup    = "select name, value from setup";
$setup          = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?></title>
    <meta name="cvs"                content="$Id: main.php,v 1.58 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2>Bilagskonfigurasjon</h2>
<p>Her kan du velge standardkonti for de ulike bilagstypene </p>
<table class="lodo_data">
<form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
  <tr class="result" border=1>
    <th colspan="2" rowspan="2">Bilagstype</th>
    <th colspan="3">Balansekonto</th>
    <th colspan="2">Resultatkonto</th>
  </tr>
  <tr class="result">
    <th class="sub">Inn</th>
    <th class="sub">Ut</th>
    <th class="sub">Reskontro</th>
    <th class="sub">Utgift</th>
    <th class="sub">Inntekt</th>
  </tr>
  <tr>
    <td class="menu" rowspan="2">Kasse</td>
    <td>Inn</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'kasseinn', 'value' => $setup['kasseinn'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Ut</td>
    <td></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'kasseut', 'value' => $setup['kasseut'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="menu" rowspan="2">Bank</td>
    <td>Inn</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'bankinn', 'value' => $setup['bankinn'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>Ut</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'bankut', 'value' => $setup['bankut'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td rowspan="4" class="menu">Kj&oslash;p</td>
    <td class="submenu">Faktura kontant</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buycashut', 'value' => $setup['buycashut'], 'type' => array(0 => 'balance'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buycashreskontro', 'value' => $setup['buycashreskontro'], 'type' => array(0 => 'supplier'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buycashutgift', 'value' => $setup['buycashutgift'], 'type' => array(0 => 'result'))) ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="submenu">Faktura kreditt</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buycreditreskontro', 'value' => $setup['buycreditreskontro'], 'type' => array(0 => 'reskontro'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buycreditutgift', 'value' => $setup['buycreditutgift'], 'type' => array(0 => 'result'))) ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="submenu">Kreditnota kontant</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buynotacashinn', 'value' => $setup['buynotacashinn'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buynotacashreskontro', 'value' => $setup['buynotacashreskontro'], 'type' => array(0 => 'reskontro'))) ?></td>
    <td></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buynotacashutgift', 'value' => $setup['buynotacashutgift'], 'type' => array(0 => 'result'))) ?></td>
  </tr>
  <tr>
    <td class="submenu">Kreditnota kreditt</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buynotacreditreskontro', 'value' => $setup['buynotacreditreskontro'], 'type' => array(0 => 'reskontro'))) ?></td>
    <td></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'buynotacreditutgift', 'value' => $setup['buynotacreditutgift'], 'type' => array(0 => 'result'))) ?></td>
  </tr>
  <tr>
    <td rowspan="4" class="menu">Salg</td>
    <td class="submenu">Faktura kontant</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salecashut', 'value' => $setup['salecashut'], 'type' => array(0 => 'balance'))) ?></td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salecashreskontro', 'value' => $setup['salecashreskontro'], 'type' => array(0 => 'supplier'))) ?></td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salecashinntekt', 'value' => $setup['salecashinntekt'], 'type' => array(0 => 'result'))) ?></td>
  </tr>
  <tr>
    <td class="submenu">Faktura kreditt</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salecreditreskontro', 'value' => $setup['salecreditreskontro'], 'type' => array(0 => 'customer'))) ?></td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salecreditinntekt', 'value' => $setup['salecreditinntekt'], 'type' => array(0 => 'result'))) ?></td>
  </tr>
  <tr>
    <td class="submenu">Kreditnota kontant</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salenotacashut', 'value' => $setup['salenotacashut'], 'type' => array(0 => 'balance'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salenotacashreskontro', 'value' => $setup['salenotacashreskontro'], 'type' => array(0 => 'customer'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salenotacashinntekt', 'value' => $setup['salenotacashinntekt'], 'type' => array(0 => 'result'))) ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td class="submenu">Kreditnota kreditt</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salenotacreditreskontro', 'value' => $setup['salenotacreditreskontro'], 'type' => array(0 => 'customer'))) ?></td>
    <td><? print $_lib['form3']->accountplan_number_menu(array('table' => 'setup.value', 'field' => 'salenotacreditinntekt', 'value' => $setup['salenotacreditinntekt'], 'type' => array(0 => 'result'))) ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
  	<th colspan="7">Passord og brukernavn til organisasjonsnummer oppslag</th>
  </tr>
  <tr>
    <td class="submenu">Brukernavn</td>
    <td><? print $_lib['form3']->text(array('table' => 'setup.value', 'field' => 'username', 'value' => $setup['username'])) ?></td>
    <td class="submenu">Passord</td>
    <td><? print $_lib['form3']->password(array('table' => 'setup.value', 'field' => 'password', 'value' => $setup['password'])) ?></td>
  </tr>
  <tr>
    <th class="submenu" colspan="7">KID oppsett</td>
  </tr>
  <tr>
    <td colspan="7"><? print $_lib['form3']->checkbox(array('table' => 'setup.value', 'field' => 'invoiceid', 'value' => $setup['invoiceid'])) ?> Fakturanummer + sjekksum (mod10) = KID. Blir alltid unik</td>
  </tr>
  <tr>
    <td colspan="7"><? print $_lib['form3']->checkbox(array('table' => 'setup.value', 'field' => 'accountplanid', 'value' => $setup['accountplanid'])) ?> Kontoplan + sjekksum (mod10) = KID. Best for abbonementsbetalinger / Akonto</td>
  </tr>
  <tr>
    <td colspan="7"><? print $_lib['form3']->text(array('table' => 'setup.value', 'field' => 'pad', 'value' => $setup['pad'])) ?> Hvis KID skal ha en fast lengde (padding) s&aring; oppgi det her. 0/blank = dynamisk.</td>
  </tr>
  <tr>
    <th class="submenu" colspan="7">Fakturabank</td>
  </tr>
  <tr>
    <td class="submenu" colspan="2">Hent fakturaer fra fakturabank med status:</td>
    <td><? print $_lib['form3']->text(array('table' => 'setup.value', 'field' => 'status', 'value' => $setup['status'])) ?></td>
	<td colspan="3">tom: henter alle, approved: henter bare godkjente.</td>
  </tr>
  <tr>
    <td colspan="7"><? print $_lib['form3']->text(array('table' => 'setup.value', 'field' => 'outgoing', 'value' => $setup['outgoing'])) ?> [blank (bruker lodo)/fakturabank (henter fra fakturabank)/empatix (faktura er i empatix)]</td>
  </tr>
    <td class="BGColorDark">Valuta</td>
    <td class="BGColorLight">
<?php
#Retrieve all currencies

includelogic('exchange/exchange');

$currencies = exchange::getAllCurrencies();
?>
      <select name="setup.value.localcurrency">
<?
foreach ($currencies as $currency) {
?>
<option value="<? echo $currency->CurrencyISO; ?>" <?php if (!empty($setup['localcurrency']) && $setup['localcurrency'] == $currency->CurrencyISO) echo 'selected="selected"'; ?>><? echo $currency->CurrencyISO; ?></option>
<?
}
?>
      </select>

    </td>
  <tr>
    <td colspan="7" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_main_update" value="Lagre (S)" accesskey="S" />
    <? } ?>
    </td>
    </form>
  </tr>
</table>

<?
if(ini_get('register_globals')) {
   print "<b>Fatal:</b> Empatix requires that register_globals in php.ini is disabled.<br>";
}
?>
<? includeinc('bottom') ?>
</body>
</html>