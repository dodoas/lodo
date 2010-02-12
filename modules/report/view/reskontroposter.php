<?
#table

$query_voucher = "select * from voucher where BalanceOk=0 and Active=1 order by JournalID";

if($_REQUEST['report_FromAccount']) {
     $where .= " AccountPlanID >= " . $_REQUEST['report_FromAccount'] . " and AccountPlanID <= " . $_REQUEST['report_ToAccount'] . " and ";
    }

#print "$query_voucher<br>";
$result_voucher = $_lib['db']->db_query($query_voucher);
?>



<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Reskontroposter</title>
    <meta name="cvs"                content="$Id: reskontroposter.php,v 1.20 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body  onload="place_cursor();">

<? includeinc('top') ?>
<? includeinc('left') ?>
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

<h2>&Aring;pne poster - dvs bilag hvor balansen ikke g&aring;r i 0</h2>

<form class="voucher" name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print "$type"; ?>">
<input type="hidden"  name="voucher.VoucherID"  value="<? print "$voucher->VoucherID"; ?>">
<input type="hidden"  name="voucher.JournalID"  value="<? print "$JournalID"; ?>">

<table  class="lodo_data" cellspacing="0">
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilagsnr</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Inn</th>
    <th class="sub">Ut</th>
    <th class="sub">Valuta inn</th>
    <th class="sub">Valuta ut</th>
    <th class="sub">MVA%</th>
    <th class="sub">Mengde</th>
    <th class="sub">Avd.</th>
    <th class="sub">Prosjekt</th>
    <th class="sub">Forfall</th>
    <th class="sub">KID</th>
    <th class="sub">Faktura</th>
    <th class="sub">Tekst</th>
    <th class="sub">Balanse OK?</th>
</tr>
<? while($voucher = $_lib['db']->db_fetch_object($result_voucher)) {?>
<tr class="voucher">
<td><? print $voucher->VoucherType; ?></td>
<td><a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=$voucher->JournalID" ?>&amp;voucher_VoucherType=<? print $voucher->VoucherType ?>"><? print $voucher->JournalID; ?></a></td>
<td><? print $voucher->VoucherDate;     ?></td>
<td><? print $voucher->VoucherPeriod;   ?></td>
<td><? print $voucher->AccountPlanID;   ?></td>
<td><? print $_lib['format']->Amount(array('value'=>$voucher->AmountIn, 'return'=>'value')); ?></td>
<td><? print $_lib['format']->Amount(array('value'=>$voucher->AmountOut, 'return'=>'value')); ?></td>
<td><? print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountIn, 'return'=>'value')); ?></td>
<td><? print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountOut, 'return'=>'value')); ?></td>
<td><? print $voucher->VAT;             ?></td>
<td><? if($voucher->Quantity) {     print $voucher->Quantity; }     ?></td>
<td><? if($voucher->DepartmentID) { print $voucher->DepartmentID; } ?></td>
<td><? if($voucher->ProjectID) {    print $voucher->ProjectID; }    ?></td>
<td><? if($voucher->DueDate)   {    print $voucher->DueDate;   }    ?></td>
<td><? if($voucher->KID)       {    print $voucher->KID; }    ?></td>
<td><? if($voucher->InvoiceID) {    print $voucher->InvoiceID; }    ?></td>
<td><? if($voucher->DescriptionID or $voucher->Description) {print $voucher->DescriptionID; print $voucher->Description; } ?></td>
<td><? if($voucher->BalanceOk) { ?>Ja<? } else { ?>Nei<? } ?></td>
</tr>
<? } ?>
</body>
</html>