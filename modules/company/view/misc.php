<?
# $Id: misc.php,v 1.24 2005/05/31 09:44:37 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "company";
$db_table2 = "person";

require_once  "record.inc";

$CompanyID = $_REQUEST['CompanyID'];
assert(!is_int($CompanyID)); #All main input should be int

$query = "select * from $db_table where CompanyID='$CompanyID'";
$row = $_lib['storage']->get_row(array('query' => $query));
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: misc.php,v 1.24 2005/05/31 09:44:37 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top'); ?>
<? includeinc('left') ?>

<h2>Firmaopplysninger, <? print $row->VName; ?> (side 3 av 3)</h2>
<table class="tab">
  <tr>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.edit&CompanyID=<? print "$CompanyID" ?>">Adresser og kontaktinformasjon</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.accounting&CompanyID=<? print "$CompanyID" ?>">Regnskapsinformasjon</a></div>
  <td><div class="active_tab">Diverse</div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=borettslag.borettslag&CompanyID=<? print "$CompanyID"; 
?>">Borettslag</a></div>
</table>
<table cellspacing="0" class="lodo_data">
<thead>
  <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="CompanyID" value="<? print $CompanyID; ?>">
</thead>

<tbody>
  <tr>
    <th colspan="4" class="menu">Diverse

  <tr>
    <td  class="BGColorDark">Status
    <td class="BGColorLight" colspan="3"><input type="text" name="company.Status"   value="<? print $row->Status ?>" size="24">

  <tr>
    <td valign="top" class="BGColorDark">Informasjon
    <td colspan="3" class="BGColorLight"><textarea name="company.Information" cols="70" rows="8"><? print $row->Information; ?></textarea>

  <tr>
    <td class="BGColorDark">Leveringsbetingelser
    <td class="BGColorLight" colspan="3"><input type="text" name="company.DeliveryCondition"    value="<? print $row->DeliveryCondition; ?>" size="70">

  <tr>
    <td class="BGColorDark">Betalingsbetingelser
    <td class="BGColorLight" colspan="3"><input type="text" name="company.PaymentCondition" value="<? print $row->PaymentCondition; ?>" size="70">

  <tr>
    <td class="BGColorDark">Klassifisering
    <td class="BGColorLight"><? $_lib['form2']->Type_menu2(array('field' => 'ClassificationID', 'value' => $row->ClassificationID, 'type' => 'CompanyClassification', 'table' => $db_table)); ?>
    <td class="BGColorDark">Stiftelsesdato
    <td class="BGColorLight" colspan="3"><input type="text" name="company.FoundedDate"  value="<? print $row->FoundedDate; ?>" size="24">

  <tr>
    <td class="BGColorDark">Motto
    <td class="BGColorLight" colspan="3"><input type="text" name="company.TagLine"  value="<? print $row->TagLine; ?>" size="70">

  <tr>
    <td class="BGColorDark">Bransje
    <td class="BGColorLight"><input type="text" name="company.Category"    value="<? print $row->Category; ?>" size="24">
    <td class="BGColorDark">
    <td class="BGColorLight">


  <tr>
    <td class="BGColorDark">Timepris
    <td class="BGColorLight"><input type="text" name="company.HourPrice"    value="<? print $row->HourPrice; ?>" size="24">
    <td class="BGColorDark">Reisepris
    <td class="BGColorLight"><input type="text" name="company.TravelPrice"  value="<? print $row->TravelPrice; ?>" size="24">


  <tr>
    <td class="BGColorDark">Kundeansvarlig
    <td class="BGColorLight"><? $_lib['form2']->CompanyContactMenu($db_table, "CustomerResponsibleID", $row->CustomerResponsibleID, $_SETUP[COMPANY_ID]); ?>
    <td class="BGColorDark">Selger
    <td class="BGColorLight"><? $_lib['form2']->CompanyContactMenu($db_table, "SalesmanID", $row->SalesmanID, $_SETUP[COMPANY_ID]); ?>

  <tr>
    <td class="BGColorDark">Aktiv
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "Active",$row->Active,''); ?>
    <td class="BGColorDark">Organisasjonsnummer
    <td class="BGColorLight">
      <input type="text" name="company.CompanyNumber"   value="<? print $row->CompanyNumber; ?>" size="24">
      <input type="text" name="company.OrgNumber"   value="<? print $row->OrgNumber; ?>" size="24">

</tbody>

<tfoot>
    <tr class="BGColorDark">
        <td align="right" colspan="4">
            <?
            if($_lib['sess']->get_person('AccessLevel') >= 2)
            {
                ?>
                <input type="submit" value="Lagre (S)" name="action_company_update" tabindex="0" accesskey="S">
                <?
            }
            ?>
        </td>
    </tr>
</tfoot>
</table>
</form>
</body>
</html>
