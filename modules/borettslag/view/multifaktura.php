<?
# $Id: misc.php,v 1.21 2005/02/22 10:29:30 thomasek Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
$CompanyID = $_REQUEST['CompanyID'];
if(!$CompanyID) { $CompanyID = 1; }

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";
require_once  "formatNumber.inc";

require_once  "record_mf.inc";


$query = "select * from company where CompanyID='$CompanyID'";
$row_c = $_dbh[$_dsn]->get_row(array('query' => $query));
$query2 = "select * from borettslag where CompanyID='$CompanyID'";
$row_b = $_dbh[$_dsn]->get_row(array('query' => $query2));
$selectFaktura = "select io.InvoiceDate from invoiceout io, invoiceoutline iol where io.InvoiceID = iol.InvoiceID and iol.ProductID = '" . $row_b->ProductID1 . "' order by io.InvoiceDate desc limit 0, 1;";
$selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
$row_f_all = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
$siste_husleiedato = $row_f_all[0];

$selectFaktura = "select sum(iol.UnitCustPrice) from invoiceout io, invoiceoutline iol where io.InvoiceID = iol.InvoiceID and iol.ProductID = '" . $row_b->ProductID1 . "' and io.InvoiceDate >= '" . date("Y") . "-01-01' and io.InvoiceDate <= '" . date("Y") . "-12-31';";
$selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
$row_f_all = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
$husleie_totalt = $row_f_all[0];
 ?>


<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: misc.php,v 1.21 2005/02/22 10:29:30 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2><? print "Multifaktura for leiligheter i " . $row_c->VName; ?></h2>
<table cellspacing="0" class="lodo_data">
<thead>
  <th colspan="5">Faktura
  <form name="leiligheterEdit" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="eierforhold.EierforholdID" value="<? print $row_br->EierforholdID; ?>">
</thead>
<tbody>
  <tr>
    <td class="BGColorDark">Siste fakturadato:
    <td class="BGColorLight" colspan="3"><? print $siste_husleiedato; ?>
  <tr>
    <td class="BGColorDark">Totalt husleie fakturert i &aring;r
    <td class="BGColorLight" colspan="3"><? print $husleie_totalt; ?>

  <tr>
    <td class="BGColorDark">M&aring;ned (tekst)
    <td class="BGColorLight" colspan="3"><input type="text" name="context" value="" size="70">
  <tr>
    <td class="BGColorDark">Fakturadato
    <td class="BGColorLight" colspan="3"><input type="text" name="fakturadato" value="" size="70">
<!-- http://lodo.ge-consulting.com/lodo.php?SID=e7fd77e4aa38ed2e73cd25a61a091bbe&_Level1ID=&_Level2ID=&t=journal.edit&voucher_JournalID=10078&type=salecash_in -->
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4"><input type="submit" value="Fakturer (F)" name="action_fakturer" tabindex="0" accesskey="F"><br>
</tfoot>
</table>
</form></form>
</body>
</html>
