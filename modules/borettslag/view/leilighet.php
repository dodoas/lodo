<?
# $Id: list.php,v 1.54 2005/01/30 12:35:03 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

global $_dsn, $_SETUP, $_dbh;

$db_table = "leilighet";

require_once  "formatNumber.inc";
require_once  "record_l.inc";

if(!$CompanyID) { $CompanyID = 1; }

/* S�kestreng */
$selectCompany = "select * from company where CompanyID = '$CompanyID';";
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Borettslag leiligheter</title>
    <meta name="cvs"                content="$Id: list.php,v 1.54 2005/01/30 12:35:03 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<?
$row_c = $_dbh[$_dsn]->get_row(array('query' => $selectCompany));
$selectBorettslag = "select * from borettslag where CompanyID = '$CompanyID';";
$row_b = $_dbh[$_dsn]->get_row(array('query' => $selectBorettslag));

?>
<h2><? print "Leiligheter i " . $row_c->VName; ?></h2>
<?
$query2 = "select * from $db_table where LeilighetID='" . $_REQUEST["leilighet_LeilighetID"] . "'";
$row_br= $_dbh[$_dsn]->get_row(array('query' => $query2));

?>
<table cellspacing="0" class="lodo_data">
<thead>
  <th colspan="5"><? if($row_br->LeilighetID == "") print "Ny"; else print "Endre"; ?> leilighet
  <form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="leilighet.LeilighetID" value="<? print $row_br->LeilighetID; ?>">
  <input type="hidden" name="leilighet.BorettslagID" value="<? print $row_b->BorettslagID; ?>"></td>
</thead>
<tbody>
  <tr>
    <td class="BGColorDark">Seksjons nr
    <td class="BGColorLight" colspan="3"><input type="text" name="leilighet.Seksjonsnr" value="<? print $row_br->Seksjonsnr; ?>" size="70">

  <tr>
    <td class="BGColorDark">Kvadrat
    <td class="BGColorLight" colspan="3"><input type="text" name="leilighet.Kvadrat" value="<? print formatNumber($row_br->Kvadrat); ?>" size="70">

  <tr>
    <td class="BGColorDark">AndelTotal
    <td class="BGColorLight" colspan="3"><input type="text" name="leilighet.AndelTotal" value="<? print formatNumber($row_br->AndelTotal); ?>" size="70">

  <tr>
    <td class="BGColorDark">BorettInnskudd
    <td class="BGColorLight" colspan="3"><input type="text" name="leilighet.BorettInnskudd" value="<? print formatNumber($row_br->BorettInnskudd); ?>" size="70">
<!-- Produkter -->
<?
for ($i=1; $i < 5; $i++)
{
// $row_b->
$myProdID = "ProductID" . $i;
$myProdukt = "Produkt" . $i;
if ($row_b->$myProdID != 0)
    {
    $query_l = "select ProductName, ProductID from product where ProductID = " . $row_b->$myProdID . ";";
    $row_p= $_dbh[$_dsn]->get_row(array('query' => $query_l));
?>
  <tr>
    <td class="BGColorDark"><? print $row_p->ProductName; ?>:
    <td class="BGColorLight" colspan="3"><input type="text" name="leilighet.Produkt<? print $i; ?>" value="<? print formatNumber($row_br->$myProdukt); ?>" size="70">
<?
    }
}
?>
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4"><input type="submit" value="Lagre (S)" name="action_leilighet_update" tabindex="0" accesskey="S"><br>
    <input type="submit" value="Slett (D)" name="action_leilighet_delete" tabindex="1" accesskey="D"><br/>
    <input type="button" value="leilighetsliste" name="action_leilighet_list" tabindex="2" accesskey="B" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.leiligheter';">
</tfoot>
</table>
</form>


</body>
</html>


