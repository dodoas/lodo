<?
# $Id: misc.php,v 1.21 2005/02/22 10:29:30 thomasek Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "company";
$db_table2  = "borettslagarsoppgjor";

require_once  "record_a.inc";

$CompanyID = $_REQUEST['CompanyID'];
if(!$CompanyID) { $CompanyID = 1; }

$query = "select * from company where CompanyID='$CompanyID'";
$row = $_dbh[$_dsn]->get_row(array('query' => $query));
$query2 = "select * from borettslag where CompanyID='$CompanyID'";
$row_b = $_dbh[$_dsn]->get_row(array('query' => $query2));
$query3 = "select * from borettslagarsoppgjor where BorettslagarsoppgjorID='" . $_REQUEST['borettslagarsoppgjor_BorettslagarsoppgjorID'] . "';";
$row_bra= $_dbh[$_dsn]->get_row(array('query' => $query3));
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
  <input type="hidden" name="borettslagarsoppgjor.BorettslagarsoppgjorID" value="<? print $row_bra->BorettslagarsoppgjorID; ?>">
  <input type="hidden" name="borettslagarsoppgjor.BorettslagID" value="<? print $row_b->BorettslagID; ?>">
  <tr>
    <th class="BGColorDark" colspan="4">&Aring;rsoppgj&oslash;r
</thead>
<tbody>
  <tr>
    <td class="BGColorDark">&Aring;rstall
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Arstall" value="<? print $row_bra->Arstall; ?>" size="70">

  <tr>
    <td class="BGColorDark">Prosentinntekt
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.ProsentInntekt" value="<? print formatNumber($row_bra->ProsentInntekt); ?>" size="70">

  <tr>
    <td class="BGColorDark">Inntekter
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Inntekter" value="<? print formatNumber($row_bra->Inntekter); ?>" size="70">
<!-- lodo.php?SID=d9c6eeb0fb6145e878858061afd4c8cd&amp;_Level1ID=&amp;_Level2ID=&amp;t=report.dagbok -->
  <tr>
    <td class="BGColorDark">Utgifter
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Utgifter" value="<? print formatNumber($row_bra->Utgifter); ?>" size="70">

  <tr>
    <td class="BGColorDark">Ligningsverdi
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Ligningsverdi" value="<? print formatNumber($row_bra->Ligningsverdi); ?>" size="70">

  <tr>
    <td class="BGColorDark">Formue
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Formue" value="<? print formatNumber($row_bra->Formue); ?>" size="70">

  <tr>
    <td class="BGColorDark">Gjeld
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Gjeld" value="<? print formatNumber($row_bra->Gjeld); ?>" size="70">

  <tr>
    <td class="BGColorDark">Kostpris
    <td class="BGColorLight" colspan="3"><input type="text" name="borettslagarsoppgjor.Kostpris" value="<? print formatNumber($row_bra->Kostpris); ?>" size="70">
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4"><input type="submit" value="Lagre (S)" name="action_aarsoppgjor_update" tabindex="0" accesskey="S"><br>
    <input type="button" value="Tilbake til borettslag oppsett" name="goto" tabindex="2" accesskey="N" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.borettslag&ComanyID=<? print $CompanyID; ?>';"><br>
    <!-- <input type="submit" value="Slett (D)" name="action_borettslag_delete" tabindex="0" accesskey="D"> -->

</tfoot>
</table>
</form>
</body>
</html>
