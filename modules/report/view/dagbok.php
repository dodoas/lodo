<?
#table

$select = "select v.* from voucher as v, accountplan as a ";

if($_REQUEST['report_Type'] == 'dagbok') {
    if($_REQUEST['report_FromDate']) {
      if(!$_REQUEST['report_ToDate']) {
        $_REQUEST['report_ToDate'] =  $_REQUEST['report_FromDate'];
      }
      $where .= " v.VoucherDate >= '" . $_REQUEST['report_FromDate'] . "' and v.VoucherDate <= '" . $_REQUEST['report_ToDate'] . "' and ";
    }
    if($_REQUEST['report_VoucherType']) {
      $where .= " v.VoucherType = '" . $_REQUEST['report_VoucherType'] . "' and ";
    }
    if($_REQUEST['report_Vat']) {
      $where .= " v.Vat = '" . (int) $_REQUEST['report_Vat'] . "' and ";
    }
    if($_REQUEST['report_VatID']) {
      $where .= " v.VatID = '" . (int) $_REQUEST['report_VatID'] . "' and ";
    }   
    if($_REQUEST['report_KID']) {
      $where .= " v.KID = '" . (int) $_REQUEST['report_KID'] . "' and ";
    }   
    if($_REQUEST['report_InvoiceID']) {
      $where .= " v.InvoiceID = '" . (int) $_REQUEST['report_InvoiceID'] . "' and ";
    }   
    if($_REQUEST['report_Amount']) {
      $where .= " (v.AmountIn = '" . (int) $_REQUEST['report_Amount'] . "' or v.AmountOut = '" . (int) $_REQUEST['report_Amount'] . "') and ";
    }   

    if($_REQUEST['report_FromAccount']) {
      if(!$_REQUEST['report_ToAccount']) {
        $_REQUEST['report_ToAccount'] = (int) $_REQUEST['report_FromAccount'];
      }
      $where .= " a.AccountPlanID >= " . (int) $_REQUEST['report_FromAccount'] . " and a.AccountPlanID <= " . (int) $_REQUEST['report_ToAccount'] . " and ";
    }
    if($_REQUEST['report_FromJournal']) {
      if(!$_REQUEST['report_ToJournal']) {
        $_REQUEST['report_ToJournal'] = (int) $_REQUEST['report_FromJournal'];
      }
      $where .= " v.JournalID >= " . (int) $_REQUEST['report_FromJournal'] . " and v.JournalID <= " . (int) $_REQUEST['report_ToJournal'] . " and ";
    }
    if($_REQUEST['report_FromPeriod']) {
      if(!$_REQUEST['report_ToPeriod']) {
        $_REQUEST['report_ToPeriod'] = $_REQUEST['report_FromPeriod'];
      }
      $where .= " v.VoucherPeriod >= '" . $_REQUEST['report_FromPeriod'] . "' and v.VoucherPeriod <= '" . $_REQUEST['report_ToPeriod'] . "' and ";
    }

    if($_REQUEST['report_Search']) {
      $where .= " (v.Description like '%" . $_REQUEST['report_Search'] . "%' or v.KID like '% " . $_REQUEST['report_Search'] . "%') and ";
    }
}
elseif($_REQUEST['report_Type'] == 'hovedbok') {
    $where .= " v.VoucherPeriod >= '" . $_REQUEST['report_FromPeriod'] . "' and v.VoucherPeriod <= '" . $_REQUEST['report_ToPeriod'] . "' and ";
    $where .= " (a.AccountPlanType='balance' or a.AccountPlanType='result') and ";
}
elseif($_REQUEST['report_Type'] == 'reskontro') {
    $where .= " v.VoucherPeriod >= '" . $_REQUEST['report_FromPeriod'] . "' and v.VoucherPeriod <= '" . $_REQUEST['report_ToPeriod'] . "' and ";
    $where .= " (a.AccountPlanType='customer' or a.AccountPlanType='supplier') and ";
}
elseif($_REQUEST['report_Type'] == 'balancenotok') {
    $where = " v.BalanceOk=0 and ";
}

if($_REQUEST['report_ProjectID']) {
    $where .= " v.ProjectID = " . (int) $_REQUEST['report_ProjectID'] . " and ";
}
if($_REQUEST['report_DepartmentID']) {
    $where .= " v.DepartmentID = " . (int) $_REQUEST['report_DepartmentID'] . " and ";
}

#$where  = substr($where, 0, strlen($where) - 4);
$where .= " v.AccountPlanID=a.AccountPlanID";

##############################################################
#Calculate account balance

if($where) {
  $query_voucher  = $select . ' where ' . $where . "  and v.Active=1 order by v." . $_REQUEST['report_Sort'];
} else {
  $query_voucher  = $select . " where v.Active=1 order by v." . $_REQUEST['report_Sort'];
}
#print "$query_voucher<br>";
#$_lib['sess']->debug("$query_voucher<br>");
$result_voucher = $_lib['db']->db_query($query_voucher);
$numrows        = $_lib['db']->db_numrows($result_voucher);
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - journal</title>
    <meta name="cvs"                content="$Id: dagbok.php,v 1.38 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>), Sider: <? print $numrows/50 ?>, Linjer: <? print $numrows ?></h2>
Bilagsregistreringsrapport: <? print $_REQUEST['report_Type'] ?>
<? if(strlen($_REQUEST['report_FromPeriod']) > 0) { ?>
periode: <? print $_REQUEST['report_FromPeriod'] ?> - <? print $_REQUEST['report_ToPeriod'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_Search']) > 0) { ?>
S&oslash;k: <? print $_REQUEST['report_Search'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_FromJournal']) > 0) { ?>
Bilagnsummer: <? print $_REQUEST['report_FromJournal'] ?> - <? print $_REQUEST['report_ToJournal'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_FromAccount']) > 0) { ?>
Konto: <? print $_REQUEST['report_FromAccount'] ?> - <? print $_REQUEST['report_ToAccount'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_VoucherType']) > 0) { ?>
Bilagstype: <? print $_REQUEST['report_VoucherType'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_Vat']) > 0) { ?>
MVA%: <? print $_REQUEST['report_Vat'] ?> <br />
<? } ?>
<? if(strlen($_REQUEST['report_VatID']) > 0) { ?>
MVA Kode: <? print $_REQUEST['report_VatID'] ?><br />
<? } ?>
<? if(strlen($_REQUEST['report_FromDate']) > 0) { ?>
Dato: <? print $_REQUEST['report_FromDate'] ?> - <? print $_REQUEST['report_ToDate'] ?><br />
<? } ?>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>"/>
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>

<table  class="lodo_data" cellspacing="0">
  <tr class="voucher">
    <th class="sub">ID</th>
    <th class="sub">Bilag</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Debet</th>
    <th class="sub">Kredit</th>
    <th class="sub">Valuta</th>
    <th class="sub">MVA</th>
    <th class="sub">MVA Kode</th>
    <th class="sub">Meng</th>
    <th class="sub">Avd.</th>
    <th class="sub">Pro</th>
    <th class="sub">KID</th>
    <th class="sub">Faktura</th>
    <th class="sub">Bruker</th>
    <!-- <th class="sub" colspan="2">Tekst</th> -->
    <th class="sub">Tekst</th>
  </tr>
    <?
    while($voucher = $_lib['db']->db_fetch_object($result_voucher))
    {
    $TotalAmountIn 	+= $voucher->AmountIn;
    $TotalAmountOut += $voucher->AmountOut;
    ?>
        <tr class="voucher">
        	<td><? print $voucher->VoucherID     ?></td>
            <td><? print $voucher->VoucherType     ?><a href="<? print $_lib['sess']->dispatch."t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID" ?>&amp;action_journalid_search=1"><? print $voucher->JournalID; ?></a></td>
            <td><? print $voucher->VoucherDate     ?></td>
            <td><? print $voucher->VoucherPeriod   ?></td>
            <td><? print $voucher->AccountPlanID   ?></td>
            <td class="number"><? if($voucher->AmountIn  > 0) { print $_lib['format']->Amount(array('value'=>$voucher->AmountIn, 'return'=>'value')); }  ?></td>
            <td class="number"><? if($voucher->AmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->AmountOut, 'return'=>'value')); } ?></td>
            <td <? if($voucher->ForeignAmountOut > 0) { ?>class="number" <? } ?>><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountIn, 'return'=>'value')); }  ?><? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountOut, 'return'=>'value')); } ?></td>
            <td class="number"><? if($voucher->Vat)   { print "$voucher->Vat%"; } ?></td>
            <td class="number"><? if($voucher->VatID) { print $voucher->VatID; }   ?></td>
            <td><? if($voucher->Quantity)     { print $voucher->Quantity; }    ?></td>
            <td><? if($voucher->DepartmentID) {print $voucher->DepartmentID; } ?></td>
            <td><? if($voucher->ProjectID) { print $voucher->ProjectID; }      ?></td>
            <td><? print $voucher->KID       ?></td>
            <td><? print $voucher->InvoiceID    ?></td>
            <td><? print $voucher->InsertedByPersonID; ?></td>
            <!-- <td><? print $voucher->DescriptionID ?></td> -->
            <td><? print $voucher->Description ?></td>
        </tr>
    <?
    }
    ?>
  </tr>
  <tr>
  	<th class="sub" colspan="5"></th>
  	<th class="sub" class="number"><nobr><? print $_lib['format']->Amount($TotalAmountIn) ?></nobr></th>
  	<th class="sub" class="number"><nobr><? print $_lib['format']->Amount($TotalAmountOut) ?></nobr></th>
  	<th class="sub" colspan="9"></th>
  </tr>
  <tr>
  	<th class="sub" colspan="5"></th>
  	<th class="sub"></th>
  	<th class="sub" class="number"><nobr><? print $_lib['format']->Amount($TotalAmountIn - $TotalAmountOut) ?></nobr></th>
  	<th class="sub" colspan="9"></th>
  </tr>
</table>
</form>
</body>
</html>
