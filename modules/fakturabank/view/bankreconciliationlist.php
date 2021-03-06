<?
# $Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('fakturabank/bankreconciliationreason');

$FakturabankBankReconciliationReasonID = $_REQUEST['FakturabankBankReconciliationReasonID'];
assert(!is_int($FakturabankBankReconciliationReasonID)); #All main input should be int


$db_table = "fakturabankbankreconciliationreason";
require_once "bankreconciliationrecord.inc";

$query = "select * from $db_table order by FakturabankBankReconciliationReasonID";
$result_fakturabankbankreconciliationreason = $_lib['db']->db_query($query);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - fakturabankbankreconciliationreason list</title>
    <? includeinc('head') ?>
</head>

<body>

<? 
includeinc('top') ?>
<? 
includeinc('left') ?>

<? print $_lib['form3']->url(array('description' => 'Bank',          'url' => $_lib['sess']->dispatch . 'view_mvalines=&view_linedetails=&t=bank.list')) ?>



<h2><? print $_lib['message']->get() ?></h2>
<p>
  Gr&oslash;nn = OK<br />
  Gr&aring; = Konto finnes, men ikke aktiv<br />
  R&oslash;d = Konto finnes ikke
</p>


<p>
<form action="<? print $MY_SELF ?>" method="post">
<input type="submit" name="action_fakturabankbankreconciliationreason_import" value="Importer fra Fakturabank" />
</form>
</p>
<table class="lodo_data">
<thead>
  <tr>
    <th align="left" colspan="7">Koblinger mellom Fakturabank banktransaksjonsavstemmings&aring;rsaker og bokf&oslash;ringskontoer
  <tr>
    <th>
    <th colspan="6">
        <form name="fakturabankbankreconciliationreason_search" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationedit" method="post">
Ny kobling (skriv fb id):
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'FakturabankBankReconciliationReasonID', 'width'=>'10')) ?>
            <? print $_lib['form3']->submit(array('name'=>'action_fakturabankbankreconciliationreason_new', 'value'=>'Ny kobling')) ?>
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
while($row = $_lib['db']->db_fetch_object($result_fakturabankbankreconciliationreason)) {
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

        $found = true;
        $active = $check->Active;
        $name = $check->AccountName;

        if($correct_account) {
            $found = true;
        }
        else {
            $found = false;
        }
    }
    else {
        $found = false;
        $active = false;
        $name = "";
    }

    ?>
    <tr class="<? print "$sec_color"; ?>" style="<?= ($found ? ($active ? "background-color: green" : "background-color: gray"): "background-color: red") ?>">
         <td><a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationedit&fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID=<? print $row->FakturabankBankReconciliationReasonID ?>"><? print $row->FakturabankBankReconciliationReasonID; ?></a>
         <td><? print $row->FakturabankBankReconciliationReasonCode; ?>
         <td><? print $row->FakturabankBankReconciliationReasonName; ?>
         <td><? print lodo_fakturabank_bankreconciliationreason::translate_ledger_type($row->LedgerType); ?>
         <td><? print $row->AccountPlanID; ?>
         <td><?= ($active?"ja":"nei") ?>
         <td><?= $name ?></td>
 
<? } ?>
</tbody>
</table>

</body>
</html>
