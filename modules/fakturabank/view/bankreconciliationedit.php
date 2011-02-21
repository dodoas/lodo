<?
/* $Id: edit.php,v 1.31 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$FakturabankBankReconciliationReasonID = (int) $_REQUEST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'];

$db_table = "fakturabankbankreconciliationreason";
require_once "bankreconciliationrecord.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where FakturabankBankReconciliationReasonID = '" . $FakturabankBankReconciliationReasonID . "'";
$fakturabankbankreconciliationreason = $_lib['storage']->get_row(array('query' => $query));
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

<? print $_lib['form3']->url(array('description' => 'Koblingsliste',          'url' => $_lib['sess']->dispatch . 'view_mvalines=&view_linedetails=&t=fakturabank.bankreconciliationlist')) ?>


<form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID" value="<? print "$fakturabankbankreconciliationreason->FakturabankBankReconciliationReasonID"; ?>">
<table class="lodo_data">
    <tr class="result">
        <th colspan="4">Koblinger
    <tr>
        <td class="menu">FB Banktransaksjon AvstemmingsID
        <td><? print $fakturabankbankreconciliationreason->FakturabankBankReconciliationReasonID  ?>
    <tr>
        <td class="menu">FB Kode
        <td><input type="text" name="fakturabankbankreconciliationreason.FakturabankBankReconciliationReasonCode" value="<? print $fakturabankbankreconciliationreason->FakturabankBankReconciliationReasonCode  ?>" size="60">
    <tr>
        <td class="menu">FB Navn
        <td><input type="text" name="fakturabankbankreconciliationreason.FakturabankBankReconciliationReasonName" value="<? print $fakturabankbankreconciliationreason->FakturabankBankReconciliationReasonName  ?>" size="60">
    <tr>
        <td class="menu">Konto
        <td><input type="text" name="fakturabankbankreconciliationreason.AccountPlanID" value="<? print $fakturabankbankreconciliationreason->AccountPlanID  ?>" size="60">
    <tr>
        <td colspan="4" align="right"><input type="submit" name="action_fakturabankbankreconciliationreason_update" value="Lagre kobling (S)" accesskey="S" />
</form>
<form name="delete" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationlist" method="post">
  <tr>
    <? print $_lib['form3']->hidden(array('name'=>'FakturabankBankReconciliationReasonID', 'value'=>$FakturabankBankReconciliationReasonID)) ?>
    <td colspan="4" align="right"><input type="submit" name="action_fakturabankbankreconciliationreason_delete" value="Slett kobling" />
</form>
</table>
<? includeinc('bottom') ?>
</body>
</html>
