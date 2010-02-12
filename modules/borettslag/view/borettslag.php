<?
# $Id: misc.php,v 1.21 2005/02/22 10:29:30 thomasek Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "company";
$db_table2  = "borettslag";

require_once  "record_b.inc";

$CompanyID = $_REQUEST['CompanyID'];
if(!$CompanyID) { $CompanyID = 1; }

$query = "select * from $db_table where CompanyID='$CompanyID'";
$row = $_dbh[$_dsn]->get_row(array('query' => $query));
$query2 = "select * from $db_table2 where CompanyID='$CompanyID'";
$row_br= $_dbh[$_dsn]->get_row(array('query' => $query2));
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

<h2>Firmaopplysninger, <? print $row->VName; ?> (side 4 av 4)</h2>
<table class="tab">
  <tr>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.edit&CompanyID=<? print "$CompanyID" ?>">Adresser og kontaktinformasjon</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.accounting&CompanyID=<? print "$CompanyID" ?>">Regnskapsinformasjon</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.misc&CompanyID=<? print "$CompanyID" ?>">Diverse</a></div>
  <td><div class="active_tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=borettslag.borettslag&CompanyID=<? print "$CompanyID" ?>">Borettslag</a></div>
</table>
<table cellspacing="0" class="lodo_data">
<thead>
  <form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="t" value="<? print $_REQUEST['t']; ?>">
  <input type="hidden" name="CompanyID" value="<? print $CompanyID; ?>">
  <input type="hidden" name="borettslag.CompanyID" value="<? print $CompanyID; ?>">
  <input type="hidden" name="borettslag.BorettslagID" value="<? print $row_br->BorettslagID; ?>">
</thead>
<tbody>
  <tr>
    <td class="BGColorDark">Kvadrat totalt
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslag.Kvadrat" value="<? print formatNumber($row_br->Kvadrat); ?>" size="70">

  <tr>
    <td class="BGColorDark">Andelsbrev totalt
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslag.Andelsbrev" value="<? print formatNumber($row_br->Andelsbrev); ?>" size="70">

  <tr>
    <td class="BGColorDark">Boretts innskudd
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslag.BorettInnskudd" value="<? print formatNumber($row_br->BorettInnskudd); ?>" size="70">

  <tr>
    <td class="BGColorDark">G&aring;rdsnr:
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslag.Gardsnr" value="<? print $row_br->Gardsnr; ?>" size="70">

  <tr>
    <td class="BGColorDark">Bruksnr:
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslag.Bruksnr" value="<? print $row_br->Bruksnr; ?>" size="70">

  <tr>
    <td class="BGColorDark">Kundefordringer konto:
    <td class="BGColorLight" colspan="3"><select name="borettslag.KundefordringerKonto" size="1">
    <option value="0">Ingen valgt</option>
<?php
if ($row_br->KundefordringerKonto == "") $row_br->KundefordringerKonto = 1500;
$query = "select AccountPlanID, AccountName from accountplan order by AccountPlanID;";
$query_handler = $_dbh[$_dsn]->db_query($query);
while ($hlRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
?>
    <option value="<?php print $hlRow->AccountPlanID; ?>"<?php if ($row_br->KundefordringerKonto == $hlRow->AccountPlanID) print " SELECTED"; ?>><?php print $hlRow->AccountPlanID; ?>: <?php print $hlRow->AccountName; ?></option>
<?php
}
?>
</select>
<?php
for ($i = 1; $i < 11; $i++)
{
?>
  <tr>
    <td class="BGColorDark">produkter:
    <td class="BGColorLight" colspan="3"><select name="borettslag.ProductID<?php print $i; ?>" size="1">
    <option value="0">Ingen valgt</option>
<?php
$query = "select ProductID, ProductNumber, ProductName from product order by ProductNumber;";
$query_handler = $_dbh[$_dsn]->db_query($query);
while ($hlRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
?>
    <option value="<?php print $hlRow->ProductID; ?>"<?php if ($row_br->{"ProductID" . $i } == $hlRow->ProductID) print " SELECTED"; ?>><?php print $hlRow->ProductID; ?>: <?php print $hlRow->ProductName; ?></option>
<?php
}
?>
</select>
<?php
}
?>
<br/><br/>
  <tr>
    <td class="BGColorDark" colspan="4">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="BGColorDark">&Aring;rstall</td>
        <td class="BGColorDark">ProsentInntekt</td>
        <td class="BGColorDark">Inntekter</td>
        <td class="BGColorDark">Utgifter</td>
        <td class="BGColorDark">Ligningsverdi</td>
        <td class="BGColorDark">Formue</td>
        <td class="BGColorDark">Gjeld</td>
        <td class="BGColorDark">Kostpris</td>
      </tr>
    <?
    if ($row_br->BorettslagID != "")
    {
        $query = "select * from borettslagarsoppgjor where BorettslagID = " . $row_br->BorettslagID . " order by Arstall;";
        $query_handler = $_dbh[$_dsn]->db_query($query);
        while ($aRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
        {
      ?>
      <tr>
        <td class="BGColorDark"><a href="<? print $_SETUP['DISPATCH'] ?>t=borettslag.aarsoppgjor&borettslagarsoppgjor.BorettslagarsoppgjorID=<? print $aRow->BorettslagarsoppgjorID ?>"><? print $aRow->Arstall; ?></a></td>
        <td class="BGColorDark"><? print $aRow->ProsentInntekt; ?> </td>
        <td class="BGColorDark"><? print $aRow->Inntekter; ?> </td>
        <td class="BGColorDark"><? print $aRow->Utgifter; ?> </td>
        <td class="BGColorDark"><? print $aRow->Ligningsverdi; ?> </td>
        <td class="BGColorDark"><? print $aRow->Formue; ?> </td>
        <td class="BGColorDark"><? print $aRow->Gjeld; ?> </td>
        <td class="BGColorDark"><? print $aRow->Kostpris; ?> </td>
      </tr>
<?
        }
    }
?>

    </table>
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4"><br/><input type="submit" value="Lagre (S)" name="action_borettslag_update" tabindex="0" accesskey="S"><br>
    <input type="button" value="Ny &aring;rsbalanse" name="action_borettslag_arsbalanse" tabindex="2" accesskey="N" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.aarsoppgjor&CompanyID=<? print $CompanyID; ?>';"><br>
    <!-- <input type="submit" value="Slett (D)" name="action_borettslag_delete" tabindex="0" accesskey="D"> -->

</tfoot>
</table>
</form>
</body>
</html>
