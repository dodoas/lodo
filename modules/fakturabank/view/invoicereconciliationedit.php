<?
/* $Id: edit.php,v 1.31 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$FakturabankInvoiceReconciliationReasonID = (int) $_REQUEST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'];

$db_table = "fakturabankinvoicereconciliationreason";
require_once "invoicereconciliationrecord.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where FakturabankInvoiceReconciliationReasonID = '" . $FakturabankInvoiceReconciliationReasonID . "'";
$fakturabankinvoicereconciliationreason = $_lib['storage']->get_row(array('query' => $query));
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Fakturabank Banktransaksjonsavstemmingskoder</title>
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['message']->get() ?>

<? print $_lib['form3']->url(array('description' => 'Koblingsliste',          'url' => $_lib['sess']->dispatch . 'view_mvalines=&view_linedetails=&t=fakturabank.invoicereconciliationlist')) ?>


<form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID" value="<? print "$FakturabankInvoiceReconciliationReasonID"; ?>">
<table class="lodo_data">
    <tr class="result">
        <th colspan="4">Koblinger
    <tr>
        <td class="menu">FB Faktura AvstemmingsID
        <td><? print $FakturabankInvoiceReconciliationReasonID  ?>
    <tr>
        <td class="menu">FB Kode
        <td><input type="text" name="fakturabankinvoicereconciliationreason.FakturabankInvoiceReconciliationReasonCode" value="<? print $fakturabankinvoicereconciliationreason->FakturabankInvoiceReconciliationReasonCode  ?>" size="60">
    <tr>
        <td class="menu">FB Navn
        <td><input type="text" name="fakturabankinvoicereconciliationreason.FakturabankInvoiceReconciliationReasonName" value="<? print $fakturabankinvoicereconciliationreason->FakturabankInvoiceReconciliationReasonName  ?>" size="60">
    <tr>
        <td class="menu">Type
        <td><? print $_lib['form3']->AccountTypeMenu('fakturabankinvoicereconciliationreason.LedgerType', $fakturabankinvoicereconciliationreason->LedgerType) ?>
    <tr>
        <td class="menu">Konto
        <td><?
                $aconf = array();
                $aconf['table']         = 'fakturabankinvoicereconciliationreason';
                $aconf['field']         = 'AccountPlanID';
                $aconf['value']         = $fakturabankinvoicereconciliationreason->AccountPlanID;
                if(!$fakturabankinvoicereconciliationreason->AccountPlanID) $aconf['class'] = 'redbackground';
                $aconf['type'][]        = 'reskontro';
                $aconf['type'][]        = 'hovedbok';
                $aconf['type'][]        = 'employee';
                echo $_lib['form3']->accountplan_number_menu($aconf);
            ?>
    <tr>
        <? if($_lib['sess']->get_person('AccessLevel') > 1){ ?>
        <td colspan="4" align="right"><input type="submit" name="action_fakturabankinvoicereconciliationreason_update" value="Lagre kobling (S)" accesskey="S" />
        <? } ?>
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.invoicereconciliationlist" method="post">
  <tr>
    <? print $_lib['form3']->hidden(array('name'=>'FakturabankInvoiceReconciliationReasonID', 'value'=>$FakturabankInvoiceReconciliationReasonID)) ?>
    <? if($_lib['sess']->get_person('AccessLevel') > 1){ ?>
        <td colspan="4" align="right"><input type="submit" name="action_fakturabankinvoicereconciliationreason_delete" value="Slett kobling" />
    <? } ?>
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
