<?
# $Id: list.php,v 1.54 2005/01/30 12:35:03 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

global $_dsn, $_SETUP, $_dbh;

$db_table = "eierforhold";

require_once  "formatNumber.inc";
require_once  "record_e.inc";


if(!$CompanyID) { $CompanyID = 1; }

/* S�kestreng */
$selectCompany = "select * from company where CompanyID = '$CompanyID';";
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Borettslag leiligheter</title>
    <meta name="cvs"                content="$Id: list.php,v 1.54 2005/01/30 12:35:03 thomasek Exp $" />
    <? includeinc('head') ?>
<script language="JavaScript1.1">
var LeilTab = new Array();
function nyLeilighet(myLeilighetID, myKvadrat, myAndelTotal, myBorettInnskudd, myProdukt1, myProdukt2, myProdukt3, myProdukt4, myProdukt5, myProdukt6, myProdukt7, myProdukt8, myProdukt9, myProdukt10)
{
    this.leilighetID = myLeilighetID;
    this.Kvadrat= myKvadrat;
    this.Andelsbrev = myAndelTotal;
    this.BorettInnskudd= myBorettInnskudd;
    this.Produkt1 = myProdukt1;
    this.Produkt2 = myProdukt2;
    this.Produkt3 = myProdukt3;
    this.Produkt4 = myProdukt4;
    this.Produkt5 = myProdukt5;
    this.Produkt6 = myProdukt6;
    this.Produkt7 = myProdukt7;
    this.Produkt8 = myProdukt8;
    this.Produkt9 = myProdukt9;
    this.Produkt10 = myProdukt10;
}
function setLeilighetNr(leilighetnr)
{
    var myIdx = leilighetnr.selectedIndex;
    for (i = 0; i < LeilTab.length; i++)
    {
//      alert(leilighetnr.options[myIdx].value + ' - ' + LeilTab[i].leilighetID);
        if(leilighetnr.options[myIdx].value = LeilTab[i].leilighetID)
        {
            document.leiligheterEdit.max_Kvadrat.value = LeilTab[i].Kvadrat;
            document.leiligheterEdit.max_Andelsbrev.value = LeilTab[i].Andelsbrev;
            document.leiligheterEdit.max_BorettInnskudd.value = LeilTab[i].BorettInnskudd;
            document.leiligheterEdit.max_Produkt1.value = LeilTab[i].Produkt1;
            document.leiligheterEdit.max_Produkt2.value = LeilTab[i].Produkt2;
            document.leiligheterEdit.max_Produkt3.value = LeilTab[i].Produkt3;
            document.leiligheterEdit.max_Produkt4.value = LeilTab[i].Produkt4;
            document.leiligheterEdit.max_Produkt5.value = LeilTab[i].Produkt5;
            document.leiligheterEdit.max_Produkt6.value = LeilTab[i].Produkt6;
            document.leiligheterEdit.max_Produkt7.value = LeilTab[i].Produkt7;
            document.leiligheterEdit.max_Produkt8.value = LeilTab[i].Produkt8;
            document.leiligheterEdit.max_Produkt9.value = LeilTab[i].Produkt9;
            document.leiligheterEdit.max_Produkt10.value = LeilTab[i].Produkt10;
        }
    }
}
<?
$selectLeilighet = "select * from leilighet;";
$query_handler = $_dbh[$_dsn]->db_query($selectLeilighet);
$i = 0;
while ($row_l = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
    $i++;
    print "LeilTab[" . ($i - 1) ."] = new nyLeilighet(\"" . $row_l->LeilighetID . "\", \"" . formatNumber($row_l->Kvadrat) . "\", \"" . formatNumber($row_l->AndelTotal) . "\", \"" . formatNumber($row_l->BorettInnskudd) . "\", \"" . formatNumber($row_l->Produkt1) . "\", \"" . formatNumber($row_l->Produkt2) . "\", \"" . formatNumber($row_l->Produkt3) . "\", \"" . formatNumber($row_l->Produkt4) . "\", \"" . formatNumber($row_l->Produkt5) . "\", \"" . formatNumber($row_l->Produkt6) . "\", \"" . formatNumber($row_l->Produkt7) . "\", \"" . formatNumber($row_l->Produkt8) . "\", \"" . formatNumber($row_l->Produkt9) . "\", \"" . formatNumber($row_l->Produkt10) . "\");";

}
?>
</script>
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
$query2 = "select * from $db_table where EierforholdID='" . $_REQUEST["eierforhold_EierforholdID"] . "'";
$row_br= $_dbh[$_dsn]->get_row(array('query' => $query2));
$query2 = "select * from leilighet where LeilighetID='" . $row_br->LeilighetID . "'";
$row_le= $_dbh[$_dsn]->get_row(array('query' => $query2));

?>
<table cellspacing="0" class="lodo_data">
<thead>
  <th colspan="5"><? if($row_br->EierforholdID == "") print "Ny"; else print "Endre"; ?> eierforhold
  <form name="leiligheterEdit" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="eierforhold.EierforholdID" value="<? print $row_br->EierforholdID; ?>">
</thead>
<tbody>
  <tr>
    <td class="BGColorDark">Leilighet
    <td class="BGColorLight" colspan="3"><select name="eierforhold.LeilighetID" size="1" onChange="setLeilighetNr(this);">
    <option value="0">Ingen leilighet valgt</option>
<?php
$query = "select LeilighetID, Seksjonsnr from leilighet order by Seksjonsnr;";
print $query;
$query_handler = $_dbh[$_dsn]->db_query($query);
while ($hlRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
?>
    <option value="<?php print $hlRow->LeilighetID; ?>"<?php if ($row_br->LeilighetID == $hlRow->LeilighetID) print " SELECTED"; ?>><?php print $hlRow->Seksjonsnr; ?></option>
<?php
}
?>
  <tr>
    <td class="BGColorDark">Eier
    <td class="BGColorLight" colspan="3"><select name="eierforhold.AccountPlanID" size="1">
<?php
$query = "select ReskontroAccountPlanType from accountplan where AccountPlanID = '1500';";
$query_handler = $_dbh[$_dsn]->db_query($query);
$TempRow = $_dbh[$_dsn]->db_fetch_object($query_handler);
$query = "select AccountPlanID, AccountName from accountplan where AccountPlanType = '" . $TempRow->ReskontroAccountPlanType . "' order by AccountName;";
print $query;
$query_handler = $_dbh[$_dsn]->db_query($query);
while ($hlRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
    if ($hlRow->AccountName != "")
    {
?>
    <option value="<?php print $hlRow->AccountPlanID; ?>"<?php if ($row_br->AccountPlanID == $hlRow->AccountPlanID) print " SELECTED"; ?>><?php print $hlRow->AccountName; ?></option>
<?php
    }
}
?>
  <tr>
    <td class="BGColorDark">Kvadrat
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.Kvadrat" value="<? print formatNumber($row_br->Kvadrat); ?>" size="70">
    Hele leiligheten: <input type="text" name="max_Kvadrat" value="<? print formatNumber($row_le->Kvadrat); ?>" size="15">
  <tr>
    <td class="BGColorDark">Andelsbrev
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.Andelsbrev" value="<? print formatNumber($row_br->Andelsbrev); ?>" size="70">
    Hele leiligheten: <input type="text" name="max_Andelsbrev" value="<? print formatNumber($row_le->AndelTotal); ?>" size="15">

  <tr>
    <td class="BGColorDark">Borett Innskudd
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.BorettInnskudd" value="<? print formatNumber($row_br->BorettInnskudd); ?>" size="70">
    Hele leiligheten: <input type="text" name="max_BorettInnskudd" value="<? print formatNumber($row_le->BorettInnskudd); ?>" size="15">
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
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.Produkt<? print $i; ?>" value="<? print formatNumber($row_br->$myProdukt); ?>" size="70">
    Hele leiligheten: <input type="text" name="max_Produkt<? print $i; ?>" value="<? print formatNumber($row_le->$myProdukt); ?>" size="15">
<?
    }
}
?>
  <tr>
    <td class="BGColorDark">Fra dato
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.FraDato" value="<? print $row_br->FraDato; ?>" size="70">
  <tr>
    <td class="BGColorDark">Til dato
    <td class="BGColorLight" colspan="3"><input type="text" name="eierforhold.TilDato" value="<? print $row_br->TilDato; ?>" size="70">

</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4"><input type="submit" value="Lagre (S)" name="action_eierforhold_update" tabindex="0" accesskey="S"><br>
    <input type="submit" value="Slett (D)" name="action_eierforhold_delete" tabindex="1" accesskey="D"><br/>
    <input type="button" value="leilighetsliste" name="action_leilighet_list" tabindex="2" accesskey="B" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.leiligheter';">
</tfoot>
</table>
</form>


</body>
</html>


