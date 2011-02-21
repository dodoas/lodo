<?
# $Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

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
<form action="<? print $MY_SELF ?>" method="post">
<input type="submit" name="action_fakturabankbankreconciliationreason_import" value="Importer fra Fakturabank" />
</form>
</p>
<table class="lodo_data">
<thead>
  <tr>
    <th align="left" colspan="4">Koblinger mellom Fakturabank banktransaksjonsavstemmings&aring;rsaker og bokf&oslash;ringskontoer
  <tr>
    <th>
    <th colspan="4">
        <form name="fakturabankbankreconciliationreason_search" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationedit" method="post">
Ny kobling (skriv fb id):
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'FakturabankBankReconciliationReasonID', 'width'=>'10')) ?>
            <? print $_lib['form3']->submit(array('name'=>'action_fakturabankbankreconciliationreason_new', 'value'=>'Ny kobling')) ?>
        </form>
  <tr>
    <th class="menu">Fakturabank Avstemmings ID
                                                                                                              <th class="menu">FB Kode
                                                                                                              <th class="menu">FB Navn
          <th class="menu">Bokf&oslash;ringskonto 
</tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_fakturabankbankreconciliationreason)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.bankreconciliationedit&fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID=<? print $row->FakturabankBankReconciliationReasonID ?>"><? print $row->FakturabankBankReconciliationReasonID; ?></a>
                                                                                                                                                                                                                                                                                             <td><? print $row->FakturabankBankReconciliationReasonCode; ?>
                                                                                                                                                                                                                                                                                             <td><? print $row->FakturabankBankReconciliationReasonName; ?>
      <td><? print $row->AccountPlanID; ?>
<? } ?>
</tbody>
</table>
</body>
</html>