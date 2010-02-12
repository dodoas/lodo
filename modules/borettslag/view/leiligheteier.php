e<?
# $Id: list.php,v 1.54 2005/01/30 12:35:03 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

global $_dsn, $_SETUP, $_dbh, $_REQUEST;

$db_table = "eierforhold";

$limitSet = $_REQUEST['limit'];
$limitSet = 1;

if(!$CompanyID) { $CompanyID = 1; }
/* S�kestreng */
$selectCompany = "select * from company where CompanyID = '$CompanyID';";
    $selectLeilighet = "select * from leilighet where LeilighetID='" . $_REQUEST["eierforhold_LeilighetID"] . "';";
print $selectLeilighet;
if ($_REQUEST["eierforhold_LeilighetID"] != "")
    $selectEiere = "select a.AccountName, e.* from eierforhold e, accountplan a where e.AccountPlanID = a.AccountPlanID and LeilighetID='" . $_REQUEST["eierforhold_LeilighetID"] . "' order by FraDato desc;";
else
    $selectEiere = "select a.AccountName, e.* from eierforhold e, accountplan a where e.AccountPlanID = a.AccountPlanID order by FraDato desc;";

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
$row_l = $_dbh[$_dsn]->get_row(array('query' => $selectLeilighet));
?>
<h2><? print "Leilighet " . $row_l->Seksjonsnr . " i " . $row_c->VName; ?></h2>
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="7">Eiere</th>
  <tr>
    <th class="menu">Navn</th>
    <th class="menu">Fra dato</th>
    <th class="menu">Til dato</th>
    <th class="menu">Kvadrat</th>
    <th class="menu">Andelsbrev</th>
    <th class="menu">Borett innskudd</th>
    <th class="menu">N&aring;v&aelig;rende eier</th>
  </tr>
</thead>
<tbody>
<?
$result_e = $_dbh[$_dsn]->db_query($selectEiere);
while($row_e = $_dbh[$_dsn]->db_fetch_object($result_e))
{
?>
      <tr>
          <td><a href="<? print $_SETUP['DISPATCH'] ?>t=borettslag.leiligheteier_edit&eierforhold.EierforholdID=<? print $row_e->EierforholdID ?>"><? print $row_e->AccountName; ?></a></td>
          <td><? $hash = $_lib['format']->Date(array('value'=>$row_e->FraDato)); print $hash["value"]; ?></td>
          <td><? $hash = $_lib['format']->Date(array('value'=>$row_e->TilDato)); print $hash["value"]; ?></td>
          <td><? print $row_e->Kvadrat; ?></td>
          <td><? print $row_e->Andelsbrev; ?></td>
          <td><? print $row_e->BorettInnskudd; ?></td>
          <td><? if ($row_e->TilDato == "0000-00-00") print "*"; ?></td>
    </tr>
    <?
}
?>
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="7">
        <input type="button" value="Nytt eierforhold" name="action_eierforhold_new" tabindex="2" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.leiligheteier_edit&eierforhold.LeilighetID=<?php print $_REQUEST["eierforhold_LeilighetID"]; ?>';"><br/>
        <input type="button" value="leilighetsliste" name="action_leilighet_list" tabindex="2" accesskey="B" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=borettslag.leiligheter';">
    </td>

</tfoot>
</table>

</body>
</html>


