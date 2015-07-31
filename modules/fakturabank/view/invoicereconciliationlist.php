<?
# $Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('fakturabank/invoicereconciliationreason');
includelogic('fakturabank/bankreconciliationreason');

$tmp_redirect_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$_SESSION['oauth_tmp_redirect_back_url'] = $tmp_redirect_url;

$FakturabankInvoiceReconciliationReasonID = $_REQUEST['FakturabankInvoiceReconciliationReasonID'];
assert(!is_int($FakturabankInvoiceReconciliationReasonID)); #All main input should be int


$db_table = "fakturabankinvoicereconciliationreason";
require_once "invoicereconciliationrecord.inc";

$query = "select * from $db_table order by FakturabankInvoiceReconciliationReasonID";
$result_fakturabankinvoicereconciliationreason = $_lib['db']->db_query($query);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - fakturabankinvoicereconciliationreason list</title>
    <? includeinc('head') ?>
</head>

<body>

<? 
includeinc('top') ?>
<? 
includeinc('left') ?>

<? print $_lib['form3']->url(array('description' => 'Innkommende faktura',          'url' => $_lib['sess']->dispatch . 'view_mvalines=&view_linedetails=&t=invoicein.list')) ?>



<h2><? print $_lib['message']->get() ?></h2>
<p>
  Gr&oslash;nn = OK<br />
  Gr&aring; = Konto finnes, men ikke aktiv<br />
  R&oslash;d = Konto finnes ikke
</p>


<p>
<form action="<? print $MY_SELF ?>" method="post">
<input type="submit" name="action_fakturabankinvoicereconciliationreason_import" value="Importer fra Fakturabank" />
</form>
</p>
<table class="lodo_data">
<thead>
  <tr>
    <th align="left" colspan="6">Koblinger mellom Fakturabank banktransaksjonsavstemmings&aring;rsaker og bokf&oslash;ringskontoer
  <tr>
    <th>
    <th colspan="5">
        <form name="fakturabankinvoicereconciliationreason_search" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.invoicereconciliationedit" method="post">
Ny kobling (skriv fb id):
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'FakturabankInvoiceReconciliationReasonID', 'width'=>'10')) ?>
            <? print $_lib['form3']->submit(array('name'=>'action_fakturabankinvoicereconciliationreason_new', 'value'=>'Ny kobling')) ?>
        </form>
  <tr>
    <th class="menu">Fakturabank Avstemmings ID
    <th class="menu">FB Kode
    <th class="menu">FB Navn
    <th class="menu">Type
    <th class="menu">Bokf&oslash;ringskonto 
    <th class="menu">Aktiv</th>
    <th class="menu">LODO Navn</th>
</tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_fakturabankinvoicereconciliationreason)) {
    $i++;

    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };

    $checkQuery = sprintf("SELECT AccountName, Active, AccountPlanType FROM accountplan WHERE AccountPlanID = %d", $row->AccountPlanID);
    if( ($check = $_lib['db']->get_row(array('query' => $checkQuery))) ) {
        $correct_account = false;

        switch($row->LedgerType) {
        case 'customer':
            $correct_account = $check->AccountPlanType == 'customer'; break;
        case 'supplier':
            $correct_account = $check->AccountPlanType == 'supplier'; break;
        case 'salary':
            $correct_account = $check->AccountPlanType == 'employee'; break;
        case 'main':
            $correct_account = $check->AccountPlanType == 'balance' || $check->AccountPlanType == 'result'; break;
        }

        echo $row->LedgerType;

        if($correct_account) {
            $found = true;
            $active = $check->Active;
            $name = $check->AccountName;
        }
        else {
            $found = false;
            $name = $check->AccountName;
            $active = $check->Active;
        }
    }
    else {
        $found = false;
        $active = false;
        $name = "";
    }

    ?>
    <tr class="<? print "$sec_color"; ?>" style="<?= ($found ? ($active ? "background-color: green" : "background-color: gray"): "background-color: red") ?>">
         <td><a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.invoicereconciliationedit&fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID=<? print $row->FakturabankInvoiceReconciliationReasonID ?>"><? print $row->FakturabankInvoiceReconciliationReasonID; ?></a>
         <td><? print $row->FakturabankInvoiceReconciliationReasonCode; ?>
         <td><? print $row->FakturabankInvoiceReconciliationReasonName; ?>
         <td><? print lodo_fakturabank_bankreconciliationreason::translate_ledger_type($row->LedgerType); ?>
         <td><? print $row->AccountPlanID; ?>
         <td><?= ($active?"ja":"nei") ?>
         <td><?= $name ?></td>
 
<? } ?>
</tbody>
</table>

</body>
</html>
