<?
require_once  $_SETUP['HOME_DIR']. "/modules/journal/view/setup.inc";
includelogic('accounting/accounting');
$accounting = new accounting();
require_once  $_SETUP['HOME_DIR']. "/modules/journal/view/record.inc";

#table
$query_first_period     = "select Period from accountperiod where Status=2 or Status=3 order by Period asc limit 1";
$first_period           = $_lib['storage']->get_row(array('query' => $query_first_period));
$FirstPeriod            = $first_period->Period;

$query_last_period      = "select Period from accountperiod where Status=2 or Status=3 order by Period desc limit 1";
$last_period            = $_lib['storage']->get_row(array('query' => $query_last_period));
$LastPeriod             = $last_period->Period;

$query_voucher          = "select concat(v.VoucherType , JournalID) as TypeJournalID, JournalID, VoucherType, sum(v.AmountIn) as sumin, sum(v.AmountOut) as sumout, v.VoucherPeriod, v.AccountPlanID, v.ProjectID, v.DepartmentID, v.Quantity, v.KID, v.InvoiceID, v.Description, v.Currency from voucher as v, accountplan as a where v.AccountPlanID=a.AccountPlanID and (a.AccountPlanType='balance' or a.AccountPlanType='result') and v.Active=1 group by TypeJournalID order by TypeJournalID";
#print "$query_voucher<br>\n";

#New logic that sums the same as what you see in the voucher - for easier understanding of problems
#Returns a hash with all JournalDs where the sum of the reskontro is different from the sum in hovedbok - this is an error
function hovedbokreskonrooppdateringdiff() {
    global $_lib;
    $query_voucher_hovedbok  = "select concat(v.VoucherType , v.JournalID,'#',AutomaticFromVoucherID) as Compare, v.VoucherType, v.JournalID, sum(v.AmountIn - v.AmountOut) as saldo, v.VoucherDate, v.VoucherPeriod, v.AccountPlanID, a.AccountPlanType, a.ReskontroAccountPlanType, v.VoucherID from voucher as v, accountplan as a where a.AccountPlanID=v.AccountPlanID and a.EnableReskontro=1 and (a.AccountPlanType='balance' or a.AccountPlanType='result') and v.Active=1 group by Compare order by Compare";
    $query_voucher_reskontro = "select concat(v.VoucherType , v.JournalID,'#',VoucherID)              as Compare, v.VoucherType, v.JournalID, sum(v.AmountIn - v.AmountOut) as saldo, v.VoucherDate, v.VoucherPeriod, v.AccountPlanID, a.AccountPlanType, a.ReskontroAccountPlanType, v.VoucherID from voucher as v, accountplan as a where a.AccountPlanID=v.AccountPlanID and a.AccountPlanType != 'balance' and a.AccountPlanType !='result' and v.Active=1 group by Compare order by Compare";

    #print "XXXX: $query_voucher_hovedbok<br>\n";
    #print "YYYY: $query_voucher_reskontro<br>\n";

    $hovedboksaldoH     = $_lib['storage']->get_hashrow(array('query' => $query_voucher_hovedbok,   'key' => 'Compare'));
    $reskontrosaldoH    = $_lib['storage']->get_hashrow(array('query' => $query_voucher_reskontro,  'key' => 'Compare'));

    if(is_array($hovedboksaldoH)) {
        foreach($hovedboksaldoH as $journal) {

            #print "BILAG H: $journal->Compare : Sum: $journal->saldo<br>\n";

            if(!isset($reskontrosaldoH[$journal->Compare])) {
                $saldodiffH[$journal->Compare]            = $journal;
                $saldodiffH[$journal->Compare]->status    = "Bilaget finnes i hovedbok $journal->AccountPlanID ($journal->AccountPlanType) med linje $journal->VoucherID og bel&oslash;p $journal->saldo, ikke i reskontro ($journal->ReskontroAccountPlanType) - bel&oslash;p 0";
                #print "reskontro $Compare: Finnes ikke eller sum differanse<br>\n";
            }
            elseif(isset($reskontrosaldoH[$journal->Compare]) && $reskontrosaldoH[$journal->Compare]->saldo != $journal->saldo) {
                $saldodiffH[$journal->Compare]            = $journal;
                $saldodiffH[$journal->Compare]->status    = 'Saldo differanse: hovedbok ' . $journal->AccountPlanID . '(' . $journal->AccountPlanType . ')  linje ' . $journal->VoucherID . ' : ' . $journal->saldo . ' != reskontro ' . $reskontrosaldoH[$journal->Compare]->AccountPlanID . '($journal->ReskontroAccountPlanType): ' . $reskontrosaldoH[$journal->Compare]->saldo;
                #print "reskontro $Compare: Finnes ikke eller sum differanse<br>\n";
            }
            elseif(isset($reskontrosaldoH[$journal->Compare]) && $reskontrosaldoH[$journal->Compare]->saldo == $journal->saldo) {
                #If saldo is equal - we have to check that the reskontro is related to the hovedbok:
                if($reskontrosaldoH[$journal->Compare]->AccountPlanType != $journal->ReskontroAccountPlanType) {
                    $saldodiffH[$journal->Compare]            = $journal;
                    $saldodiffH[$journal->Compare]->status    = 'Summene er riktig men reskontrotypen (' . $reskontrosaldoH[$journal->Compare]->AccountPlanID . '): ' . $reskontrosaldoH[$journal->Compare]->AccountPlanType . ' er oppdatert mot feil hovedbokskonto type #' . $journal->ReskontroAccountPlanType . '# (' . $journal->AccountPlanID . ') Lagre bilaget p&aring; nytt s&aring; vil dette bli rettet';
                    #print "reskontro $Compare: Finnes ikke eller sum differanse<br>\n";
                }
            }
        }
    }

    if(is_array($reskontrosaldoH)) {
        foreach($reskontrosaldoH as $journal) {

            #print "BILAG R: $journal->Compare : Sum: $journal->saldo<br>\n";
            if(!isset($hovedboksaldoH[$journal->Compare])) {
                $saldodiffH[$journal->Compare]            = $journal;
                $saldodiffH[$journal->Compare]->status    = "Bilaget finnes i reskontro $journal->AccountPlanID ($journal->AccountPlanType)  med linje $journal->VoucherID og bel&oslash;p $journal->saldo, men ikke i hovedbok - bel&oslash;p 0";
                #print "hovedbok $Compare: Finnes ikke eller sum differanse<br>\n";
            }
            elseif(isset($hovedboksaldoH[$journal->Compare]) && $hovedboksaldoH[$journal->Compare]->saldo != $journal->saldo) {
                $saldodiffH[$journal->Compare]            = $journal;
                $saldodiffH[$journal->Compare]->status    = 'Saldo differanse: reskontro ' . $journal->AccountPlanID . '(' . $journal->AccountPlanType . ') linje ' . $journal->VoucherID . ' : ' . $journal->saldo . ' != hovedbok: ' . $hovedboksaldoH[$journal->Compare]->saldo . ' konto' . $hovedboksaldoH[$journal->Compare]->AccountPlanID;
                #print "hovedbok $Compare: Finnes ikke eller sum differanse<br>\n";
            }
            elseif(isset($hovedboksaldoH[$journal->Compare]) && $hovedboksaldoH[$journal->Compare]->saldo == $journal->saldo) {
                #If saldo is equal - we have to check that the reskontro is related to the hovedbok:
                if($hovedboksaldoH[$journal->Compare]->ReskontroAccountPlanType != $journal->AccountPlanType) {
                    $saldodiffH[$journal->Compare]            = $journal;
                    $saldodiffH[$journal->Compare]->status    = 'Summene er riktig men hovedbokskontotypen  (' . $hovedboksaldoH[$journal->Compare]->AccountPlanID . '): ' . $hovedboksaldoH[$journal->Compare]->ReskontroAccountPlanType . ' er oppdatert mot feil reskontrotype #' . $journal->AccountPlanType . '# (' . $journal->AccountPlanID . ') Lagre bilaget p&aring; nytt s&aring; vil dette bli rettet';
                    #print "reskontro $Compare: Finnes ikke eller sum differanse<br>\n";
                }
            }
        }
    }
    return $saldodiffH;
}

$query_voucher_account_0    = "select *, sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where AccountPlanID=0 and Active=1 group by JournalID order by JournalID";
$query_open_period          = "select Period from accountperiod where Status=2 or Status=3";
$query_hovedbok_reskontro   = "select A.*, sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from accountplan as A, voucher as V where A.EnableReskontro=1 and V.AccountPlanID=A.AccountPlanID and V.Active=1 group by AccountPlanID order by AccountPlanID";
$query_bad_date             = "select * from voucher where (VoucherPeriod = '0000-00' or VoucherPeriod = '' or VoucherDate = '0000-00-00' or VoucherPeriod is null or VoucherDate is null or VoucherDate = ''  or VoucherPeriod = 0 or VoucherPeriod < '2001-01' or VoucherDate < '2001-01-01') and Active=1 order by JournalID";
$result_bad_date            = $_lib['db']->db_query($query_bad_date);
$result_voucher             = $_lib['db']->db_query($query_voucher);
$result_voucher_account_0   = $_lib['db']->db_query($query_voucher_account_0);
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - verifiser regnskap konsistens</title>
    <meta name="cvs"                content="$Id: verify_consistency.php,v 1.21 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">

<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

<fieldset>
<legend>1. Bilag, der sum linjer synlig er forskjellig fra kr 0,-: <? print $FirstPeriod ?> - <? print $LastPeriod ?></legend>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>"/>
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>

<table class="lodo_data">
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilag</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Debet</th>
    <th class="sub">Kredit</th>
    <th class="sub">Valuta</th>
    <th class="sub">MVA</th>
    <th class="sub">Meng</th>
    <th class="sub">Avd.</th>
    <th class="sub">Pro</th>
    <th class="sub">Ref.</th>
    <th class="sub">Tekst</th>
    <th class="sub">Diff</th>
    <th class="sub"></th>
  </tr>
    <?
    while($voucher = $_lib['db']->db_fetch_object($result_voucher))
    {
      #print_r($voucher);
      if(($voucher->sumin - $voucher->sumout) != 0) {
    ?>
        <tr class="voucher">
            <td><? print $voucher->VoucherType     ?></td>
            <td><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID"; ?>&amp;action_journalid_search=1"><? print $voucher->TypeJournalID; ?></a></td>
            <td><? print $voucher->VoucherDate     ?></td>
            <td><? print $voucher->VoucherPeriod   ?></td>
            <td><? print $voucher->AccountPlanID   ?></td>
            <td class="number"><? if($voucher->sumin  > 0) { print $_lib['format']->Amount(array('value'=>$voucher->sumin, 'return'=>'value')); }  ?></td>
            <td class="number"><? if($voucher->sumout > 0) { print $_lib['format']->Amount(array('value'=>$voucher->sumout, 'return'=>'value')); } ?></td>
            <td <? if($voucher->ForeignAmountOut > 0) { ?>class="number"<? } ?>><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountIn, 'return'=>'value')); } ?><? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountOut, 'return'=>'value')); } ?></td>
            <td><? if($voucher->VAT) { print "$voucher->VAT%"; } ?></td>
            <td><? if($voucher->Quantity)     { print $voucher->Quantity; }    ?></td>
            <td><? if($voucher->DepartmentID) { print $voucher->DepartmentID; }?></td>
            <td><? if($voucher->ProjectID)    { print $voucher->ProjectID; }   ?></td>
            <td><? print $voucher->KID       ?></td>
            <td><? print $voucher->InvoiceID       ?></td>
            <td><? print $voucher->DescriptionID; print $voucher->Description  ?></td>
            <td><font color="FF0000"><? print $_lib['format']->Amount(array('value' => $voucher->sumin - $voucher->sumout, 'return' => 'value'));  ?></font></td>
            <td>
                <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
                <a class="button" href="<? print $MY_SELF . "&amp;voucher.JournalID=$voucher->JournalID&amp;$voucher->VoucherType&amp;action_voucher_head_delete=1" ?>">Slett bilag</a>
                <? } ?>
            </td>

        </tr>
      <?
      }
    }
    ?>
</table>
</fieldset>
<br />
<br />
<fieldset>
<? $saldodiffH = hovedbokreskonrooppdateringdiff() ?>
<legend>2. Antall bilag (<? print count($saldodiffH) ?>) der oppdateringen av hovedbok fra reskontro har en differanse</legend>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>"/>
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>

<table class="lodo_data">
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilag</th>
    <th class="sub">Dato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Diff</th>
    <th class="sub">&Aring;rsak</th>
  </tr>
    <?
    if(is_array($saldodiffH)) {
        foreach($saldodiffH as $DiffO)
        {
        ?>
            <tr class="voucher">
                <td><? print $DiffO->VoucherType ?></td>
                <td><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=" . $DiffO->VoucherType . "&amp;voucher_JournalID=" . $DiffO->JournalID ?>&amp;action_journalid_search=1"><? print $DiffO->JournalID ?></a></td>
                <td><? print $DiffO->VoucherDate ?></td>
                <td><? print $DiffO->VoucherPeriod ?></td>
                <td><? print $DiffO->AccountPlanID ?></td>
                <td><? print $DiffO->saldo ?></td>
                <td><? print $DiffO->status ?></td>
            </tr>
          <?
          }
    }
    ?>
</table>
</fieldset>
<br />
<br />
<fieldset>
<legend>3. Balansekontoer, sum forskjellig fra kr 0,-: <? print $FirstPeriod ?> - <? print $LastPeriod ?></legend>
<table class="lodo_data">
<thead>
<tr>
  <th class="sub">Perioden</th>
  <th class="sub">Debet</th>
  <th class="sub">Credit</th>
  <th class="sub">Diff</th>
</tr>
</thead>
<tbody>
<?
#Find all open periods
$result_open_period  = $_lib['db']->db_query($query_open_period);

while($period = $_lib['db']->db_fetch_object($result_open_period)) {
  $query_saldo_period   = "select sum(v.AmountIn) as AmountIn, sum(v.AmountOut) as AmountOut from voucher as v, accountplan as a where v.Active=1 and v.VoucherPeriod='$period->Period' and a.AccountPlanType='balance' and a.AccountPlanID=v.AccountPlanID";
  #print "$query_saldo_period<br>";
  $check  = $_lib['storage']->get_row(array('query' => $query_saldo_period));

  if(($check->AmountIn - $check->AmountOut) != 0) {
    ?>
    <tr>
      <td><? print $period->Period ?></td>
      <td><nobr><? print $_lib['format']->Amount(array('value'=>$check->AmountIn, 'return'=>'value')) ?></nobr></td>
      <td><nobr><? print $_lib['format']->Amount(array('value'=>$check->AmountOut, 'return'=>'value')) ?></nobr></td>
      <td><nobr><? print $_lib['format']->Amount(array('value'=>round(($check->AmountIn - $check->AmountOut), 2), 'return'=>'value')) ?></nobr></td>
    </tr>
    <?
  }
}
?>
</tbody>
</table>
</fieldset>

<br />
<br />
<fieldset>
<legend>4. Resultatkontoer, sum forskjellig fra kr 0,-: <? print $FirstPeriod ?> - <? print $LastPeriod ?></legend>
<table class="lodo_data">
<thead>
<tr>
  <th class="sub">Perioden</th>
  <th class="sub">Debet</th>
  <th class="sub">Credit</th>
  <th class="sub">Diff</th>
</tr>
</thead>
<tbody>
<?
#Find all open periods
$result_open_period  = $_lib['db']->db_query($query_open_period);

while($period = $_lib['db']->db_fetch_object($result_open_period)) {
  $query_saldo_period   = "select sum(v.AmountIn) as AmountIn, sum(v.AmountOut) as AmountOut from voucher as v, accountplan as a where v.Active=1 and v.VoucherPeriod='$period->Period' and  a.AccountPlanType='result' and a.AccountplanID=v.AccountplanID";
  $check  = $_lib['storage']->get_row(array('query' => $query_saldo_period));

  if(($check->AmountIn - $check->AmountOut) != 0) {
    ?>
    <tr>
      <td><? print $period->Period ?></td>
      <td><? print $_lib['format']->Amount(array('value'=>$check->AmountIn, 'return'=>'value')) ?></td>
      <td><? print $_lib['format']->Amount(array('value'=>$check->AmountOut, 'return'=>'value')) ?></td>
      <td><? print $_lib['format']->Amount(array('value'=>round(($check->AmountIn - $check->AmountOut), 2), 'return'=>'value')) ?></td>
    </tr>
    <?
  }
}
?>
</tbody>
</table>
</fieldset>

<br />
<br />
<fieldset>
<legend>5. Hovedbokskonto samsvarer med sum reskontro: <? print $FirstPeriod ?> - <? print $LastPeriod ?></legend>
<table class="lodo_data">
<thead>
<tr>
  <th class="sub">Konto nr</th>
  <th class="sub">Hovedbok</th>
  <th class="sub">Reskontro</th>
  <th class="sub">Sum reskontro</th>
</tr>
</thead>
<tbody>
<?
#Fin alle �pne periode
$result_hovedbok_reskontro  = $_lib['db']->db_query($query_hovedbok_reskontro);

while($hovedbok = $_lib['db']->db_fetch_object($result_hovedbok_reskontro)) {
  $query_saldo   = "select sum(v.AmountIn) as AmountIn, sum(v.AmountOut) as AmountOut from voucher as v, accountplan as a where v.Active=1 and v.AccountPlanID=a.AccountPlanID and (a.AccountPlanType='customer' or a.AccountPlanType='supplier')";
  $check  = $_lib['storage']->get_row(array('query' => $query_saldo_period));

  if(($check->AmountIn - $check->AmountOut) != 0) {
    ?>
    <tr>
      <td>Hovedbok <? print $hovedbok->AccountPlanID." ".$hovedbok->AccountName ?></td>
      <td><? print $_lib['format']->Amount($hovedbok->AmountIn - $hovedbok->AmountOut) ?></td>
      <td><? print $_lib['format']->Amount($check->AmountIn - $check->AmountOut) ?></td>
    </tr>
    <?
  }
}
?>
</tbody>
</table>
</fieldset>

<br />
<br />
<fieldset>
<legend>6. Dato og periode kontroll</legend>
<table class="lodo_data">
<thead>
<tr>
  <th class="sub">Bilagsnr</th>
  <th class="sub"></th>
</tr>
</thead>
<tbody>
<?
while($voucher = $_lib['db']->db_fetch_object($result_bad_date))
{
    ?>
    <tr>
      <td><a href="<? print $_lib['sess']->dispatch."t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID"; ?>&amp;action_journalid_search=1"><? print $voucher->VoucherType." ".$voucher->JournalID; ?></a></td>
      <td>
          <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
          <a class="button" href="<? print $MY_SELF . "&amp;voucher.JournalID=$voucher->JournalID&amp;voucher_VoucherType=$voucher->VoucherType&amp;VoucherPeriod=$voucher->VoucherPeriod&amp;action_voucher_head_delete=1" ?>">Slett bilag</a>
          <? } ?>
      </td>

    </tr>
    <?
}
?>
</tbody>
</table>
</fieldset>

<br />
<br />

<fieldset>
<legend>7. Posteringer i billag hvor konto er 0</legend>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>"/>
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>


<table class="lodo_data">
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilag</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Debet</th>
    <th class="sub">Kredit</th>
    <th class="sub">Valuta</th>
    <th class="sub">MVA</th>
    <th class="sub">Meng</th>
    <th class="sub">Avd.</th>
    <th class="sub">Pro</th>
    <th class="sub">KID</th>
    <th class="sub">Faktura</th>
    <th class="sub">Tekst</th>
    <th class="sub">Diff</th>
    <th class="sub"></th>
  </tr>
    <?
    while($voucher = $_lib['db']->db_fetch_object($result_voucher_account_0))
    {
      if(($voucher->sumin - $voucher->sumout) != 0) {
    ?>
        <tr class="voucher">
            <td><? print $voucher->VoucherType     ?></td>
            <td><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID"; ?>&amp;action_journalid_search=1"><? print $voucher->JournalID; ?></a></td>
            <td><? print $voucher->VoucherDate     ?></td>
            <td><? print $voucher->VoucherPeriod   ?></td>
            <td><? print $voucher->AccountPlanID   ?></td>
            <td class="number"><? if($voucher->sumin  > 0) { print $_lib['format']->Amount(array('value'=>$voucher->sumin, 'return'=>'value')); }  ?></td>
            <td class="number"><? if($voucher->sumout > 0) { print $_lib['format']->Amount(array('value'=>$voucher->sumout, 'return'=>'value')); } ?></td>
            <td <? if($voucher->ForeignAmountOut > 0) { ?>class="number"<? } ?>><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountIn, 'return'=>'value')); } ?><? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountOut, 'return'=>'value')); } ?></td>
            <td><? if($voucher->VAT) { print "$voucher->VAT%"; } ?></td>
            <td><? if($voucher->Quantity)     { print $voucher->Quantity; }    ?></td>
            <td><? if($voucher->DepartmentID) { print $voucher->DepartmentID; }?></td>
            <td><? if($voucher->ProjectID)    { print $voucher->ProjectID; }   ?></td>
            <td><? print $voucher->KID       ?></td>
            <td><? print $voucher->InvoiceID ?></td>
            <td><? print $voucher->DescriptionID; print $voucher->Description  ?></td>
            <td><font color="FF0000"><? print $_lib['format']->Amount(array('value' => $voucher->sumin - $voucher->sumout, 'return' => 'value'));  ?></font></td>
            <td>
                <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
                <a class="button" href="<? print $MY_SELF . "&amp;voucher.JournalID=$voucher->JournalID&amp;$voucher->VoucherType&amp;VoucherPeriod=$voucher->VoucherPeriod&amp;action_voucher_head_delete=1" ?>">Slett bilag</a>
                <? } ?>
            </td>

        </tr>
      <?
      }
    }
    ?>
</table>
</fieldset>
<br />


<br />

<fieldset>
<legend>8. Bilag som er f&oslash;rt mot kontoer som er slettet</legend>
<table class="lodo_data">
<thead>
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilag</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Konto</th>
    <th class="sub">Debet</th>
    <th class="sub">Kredit</th>
    <th class="sub">Valuta</th>
    <th class="sub">MVA</th>
    <th class="sub">Meng</th>
    <th class="sub">Avd.</th>
    <th class="sub">Pro</th>
    <th class="sub">KID</th>
    <th class="sub">Faktura</th>
    <th class="sub">Tekst</th>
    <th class="sub">Diff</th>
    <th class="sub"></th>
  </tr>
</thead>
<tbody>
<?
$query_notactive        = "select v.* from voucher as v left join  accountplan as a on a.AccountPlanID=v.AccountPlanID where a.AccountPlanID is null and v.Active = 1";

$result_notactive       = $_lib['db']->db_query($query_notactive);
while($voucher          = $_lib['db']->db_fetch_object($result_notactive))
{ ?>
    <tr class="voucher">
        <td><? print $voucher->VoucherType     ?></td>
        <td><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID"; ?>&amp;action_journalid_search=1"><? print $voucher->JournalID; ?></a></td>
        <td><? print $voucher->VoucherDate     ?></td>
        <td><? print $voucher->VoucherPeriod   ?></td>
        <td><? print $voucher->AccountPlanID   ?></td>
        <td class="number"><? if($voucher->AmountIn  > 0) { print $_lib['format']->Amount(array('value'=>$voucher->AmountIn, 'return'=>'value')); }  ?></td>
        <td class="number"><? if($voucher->AmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->AmountOut, 'return'=>'value')); } ?></td>
        <td <? if($voucher->ForeignAmountOut > 0) { ?>class="number"<? } ?>><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountIn, 'return'=>'value')); } ?><? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount(array('value'=>$voucher->ForeignAmountOut, 'return'=>'value')); } ?></td>
        <td><? if($voucher->VAT) { print "$voucher->VAT%"; } ?></td>
        <td><? if($voucher->Quantity)     { print $voucher->Quantity; }    ?></td>
        <td><? if($voucher->DepartmentID) { print $voucher->DepartmentID; }?></td>
        <td><? if($voucher->ProjectID)    { print $voucher->ProjectID; }   ?></td>
        <td><? print $voucher->KID       ?></td>
        <td><? print $voucher->InvoiceID ?></td>
        <td><? print $voucher->DescriptionID; print $voucher->Description  ?></td>
        <td><font color="FF0000"><? print $_lib['format']->Amount(array('value' => $voucher->AmountIn - $voucher->AmountOut, 'return' => 'value'));  ?></font></td>
        <td>
            <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
            <a class="button" href="<? print $MY_SELF . "&amp;voucher.JournalID=$voucher->JournalID&amp;$voucher->VoucherType&amp;action_voucher_head_delete=1" ?>">Slett bilag</a>
            <? } ?>
        </td>
    </tr>
<? } ?>
</tbody>
</table>
</fieldset>

<br />
<br />

<fieldset>
<legend>9. Ukeomsetninger hvor summen av grupper er forskjellig fra oppgitt totalsum</legend>
<table class="lodo_data">
<thead>
  <tr>
    <th class="sub">Ukeomsetning nr</th>
  </tr>
</thead>
</thead>

<tbody>
<?
$query_week        = "select distinct WeeklySaleID from weeklysaleday where round(ZnrTotalAmount,2) != round(Group1Amount + Group2Amount + Group3Amount + Group4Amount + Group5Amount + Group6Amount + Group7Amount + Group8Amount + Group9Amount + Group10Amount + Group11Amount + Group12Amount + Group13Amount + Group14Amount + Group15Amount + Group16Amount + Group17Amount + Group18Amount + Group19Amount + Group20Amount,2) and Type=1";
#print "$query_week<br>";
$result_week       = $_lib['db']->db_query($query_week);
while($row = $_lib['db']->db_fetch_object($result_week))
{
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->WeeklySaleID ?></a></td>
    </tr>
<? } ?>
</table>
</fieldset>

<fieldset>
  <legend>10. Mistenkelige bilag</legend>
  <table class="lodo_data">
    <thead>
      <tr>
        <th class="sub">Bilagsnummer</th>
        <th class="sub">Dato</th>
      </tr>
    </thead>

    <tbody>
      <?

         $query = "
SELECT
  V1.JournalID, V1.VoucherType, V1.VoucherDate
FROM
  voucher AS V1
  LEFT JOIN voucher AS V2 ON (V2.VoucherID = V1.AutomaticVatVoucherID)
  LEFT JOIN voucher AS V3 ON (V3.VoucherID = V2.AutomaticVatVoucherID)
  LEFT JOIN vat AS VAT on (VAT.VatID = V1.VatID AND VAT.ValidFrom <= V1.VoucherDate AND VAT.ValidTo >= V1.VoucherDate)
WHERE
  V1.Active = 1
  AND
  ((V1.VatID != 0
  AND V1.Vat > 0.0
  AND V1.AmountIn + V1.AmountOut > 0.1
  AND (
    (V2.VoucherID IS NULL
       OR V2.Active = 0)
    OR
    (V3.VoucherID IS NULL
       OR V3.Active = 0)
    OR
    (V2.AmountIn != V3.AmountOut
      OR V2.AmountOut != V3.AmountIn)
  ))
  OR
  (VAT.Percent != V1.Vat))
";

         $res = $_lib['db']->db_query($query);
         while( ($row = $_lib['db']->db_fetch_assoc($res)) ) {
      ?>
      <tr class="BGColorLight">
        <td>
          <a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=". $row['VoucherType'] ."&amp;voucher_JournalID=". $row['JournalID']; ?>&amp;action_journalid_search=1">
            <? printf("%s%s", $row['VoucherType'], $row['JournalID']); ?>
          </a>
        </td>
        <td><? printf("%s", $row['VoucherDate']); ?></td>

      </tr>
      <?
         }
         ?>
    </tbody>
  </table>
</fieldset>

<br />
<br />

<fieldset>
<legend>11. L�nnsslipp</legend>
<table class="lodo_data">
<thead>
  <tr class="voucher">
    <th class="sub">Art</th>
    <th class="sub">Bilagsnummer</th>
    <th class="sub">Bilagsdato</th>
    <th class="sub">Periode</th>
    <th class="sub">Ansatt</th>
    <th class="sub">Fra dato</th>
    <th class="sub">Til dato</th>
    <th class="sub">Utbetalt dato</th>
    <th class="sub">Konto for utbetaling</th>
    <th class="sub">Skattekommune</th>
    <th class="sub">Problem</th>
    <th class="sub"></th>
  </tr>
</thead>
<tbody>
<?
// Select all salaries that are created but not saved in the voucher table
$query_salary  = "SELECT s.SalaryID, s.JournalDate, s.Period, s.AccountPlanID, ap.AccountName, s.ValidFrom, s.ValidTo, s.PayDate, s.DomesticBankAccount, k.KommuneNumber, k.KommuneName
                  FROM salary s, accountplan ap, kommune k
                  WHERE s.AccountPlanID = ap.AccountPlanID AND s.KommuneID = k.KommuneID AND s.JournalID NOT IN (
                                                                                                                  SELECT DISTINCT(JournalID)
                                                                                                                  FROM voucher
                                                                                                                  WHERE VoucherType = 'L' AND Active = 1
                                                                                                                )
                        AND s.Period IN ( SELECT Period FROM accountperiod WHERE Status < 4 )
                  ORDER BY s.SalaryID DESC";
$result_salary = $_lib['db']->db_query($query_salary);
// Select all salaries and their corresponding journal ids that have different lines
// A bit of an explanation of the below query:
// First we select the number of active lines for salary in voucher table for all open
// periods and compare them to number of lines in salary line table for all open periods
// based on corresponding JournalID
// if the numbers differ then something is not good

// same line limits as in salary/edit
$lineInFrom  =  10;
$lineInTo    =  69;
$lineOutFrom =  70;
$lineOutTo   = 100;

$salary_lines_subquery = "SELECT 
                            count(*) as NumberOfLinesSalary,
                            sum(sl.AmountThisPeriod) as SumOfLinesSalary,
                            sl.AccountPlanID as ForAccountPlanIDSalary,
                            s.JournalID
                          FROM (
                                -- all salary lines
                                SELECT SalaryID,
                                       AccountPlanID,
                                       round(IF(LineNumber >= $lineInFrom and LineNumber <= $lineInTo, AmountThisPeriod, IF(LineNumber >= $lineOutFrom and LineNumber <= $lineOutTo, -AmountThisPeriod, 0)), 2) as AmountThisPeriod
                                FROM salaryline
                                WHERE AmountThisPeriod != 0

                                UNION ALL
                                -- salary 'head' line, which is sum of all salary lines
                                SELECT SalaryID,
                                       (SELECT AccountPlanID FROM salary WHERE SalaryID = sldeep.SalaryID) as AccountPlanID,
                                       -sum(round(IF(LineNumber >= $lineInFrom and LineNumber <= $lineInTo, AmountThisPeriod, IF(LineNumber >= $lineOutFrom and LineNumber <= $lineOutTo, -AmountThisPeriod, 0)), 2)) as AmountThisPeriod
                                FROM salaryline sldeep
                                WHERE AmountThisPeriod != 0
                                GROUP BY SalaryID
                                HAVING sum(round(IF(LineNumber >= $lineInFrom and LineNumber <= $lineInTo, AmountThisPeriod, IF(LineNumber >= $lineOutFrom and LineNumber <= $lineOutTo, -AmountThisPeriod, 0)), 2)) != 0
                          ) sl JOIN salary s ON s.SalaryID = sl.SalaryID
                          GROUP BY s.JournalID, sl.AccountplanID";

$voucher_lines_subquery = "SELECT
                             count(*) as NumberOfLinesJournal,
                             sum(round(v.AmountIn, 2) - round(v.AmountOut,2)) as SumOfLinesJournal,
                             v.AccountPlanID as ForAccountPlanIDJournal,
                             v.JournalID
                           FROM voucher v JOIN accountplan ap ON v.AccountPlanID = ap.AccountPlanID
                           WHERE v.VoucherType = 'L' AND v.Active = 1 AND ap.EnableReskontro = 0
                           GROUP BY v.JournalID, v.AccountPlanID";

$query_diff  = "SELECT
                  SUM(ABS(IFNULL(NumberOfLinesSalary, 0) - IFNULL(NumberOfLinesJournal, 0))) as LineDifference,
                  SUM(ABS(IFNULL(SumOfLinesSalary, 0) - IFNULL(SumOfLinesJournal, 0))) as SumDifference,
                  sal.SalaryID, sal.JournalID , sal.JournalDate, sal.Period, sal.AccountPlanID, sal.ValidFrom, sal.ValidTo, sal.PayDate, ap.DomesticBankAccount, ap.AccountName, k.KommuneNumber, k.KommuneName
                FROM (
                    SELECT IF(p1.JournalID is not null, p1.JournalID, p2.JournalID) as JournalID, p2.NumberOfLinesJournal, p2.SumOfLinesJournal, p1.SumOfLinesSalary, p1.NumberOfLinesSalary
                    FROM (
                          ". $salary_lines_subquery ."
                    ) p1 LEFT JOIN (
                          ". $voucher_lines_subquery ."
                    ) p2 ON p1.JournalID = p2.JournalID AND p1.ForAccountPlanIDSalary = p2.ForAccountPlanIDJournal

                    UNION

                    SELECT IF(p1.JournalID is not null, p1.JournalID, p2.JournalID) as JournalID, p2.NumberOfLinesJournal, p2.SumOfLinesJournal, p1.SumOfLinesSalary, p1.NumberOfLinesSalary
                    FROM (
                          ". $salary_lines_subquery ."
                    ) p1 RIGHT JOIN (
                          ". $voucher_lines_subquery ."
                    ) p2 ON p1.JournalID = p2.JournalID AND p1.ForAccountPlanIDSalary = p2.ForAccountPlanIDJournal
                ) result
                JOIN salary sal ON sal.JournalID = result.JournalID LEFT JOIN accountplan ap ON sal.AccountPlanID = ap.AccountPlanID LEFT JOIN kommune k ON ap.KommuneID = k.KommuneID

                WHERE IFNULL(NumberOfLinesSalary, 0) != IFNULL(NumberOfLinesJournal, 0) || IFNULL(SumOfLinesSalary, 0) != IFNULL(SumOfLinesJournal, 0)
                GROUP BY JournalID;";

$result_diff = $_lib['db']->db_query($query_diff);

  while($salary = $_lib['db']->db_fetch_object($result_salary)) { ?>
    <tr class="voucher">
        <td><? print "L" ?></td>
        <td><a href="<? print $_SETUP[DISPATCH]."t=salary.edit&SalaryID=".$salary->SalaryID; ?>" target="_blank"><? print $salary->SalaryID; ?></a></td>
        <td><? print $salary->JournalDate; ?></td>
        <td><? print $salary->Period; ?></td>
        <td><? print $salary->AccountPlanID . " " . $salary->AccountName; ?></td>
        <td><? print $salary->ValidFrom; ?></td>
        <td><? print $salary->ValidTo; ?></td>
        <td><? print $salary->PayDate; ?></td>
        <td><? print $salary->DomesticBankAccount; ?></td>
        <td><? print $salary->KommuneNumber . " " . $salary->KommuneName; ?></td>
        <td>Ikke i bilag</td>
        <td></td>
    </tr>
<? }

  while($salary = $_lib['db']->db_fetch_object($result_diff)) { ?>
    <tr class="voucher">
        <td><? print "L" ?></td>
        <td><a href="<? print $_SETUP[DISPATCH]."t=salary.edit&SalaryID=".$salary->SalaryID; ?>" target="_blank"><? print $salary->JournalID; ?></a></td>
        <td><? print $salary->JournalDate; ?></td>
        <td><? print $salary->Period; ?></td>
        <td><? print $salary->AccountPlanID . " " . $salary->AccountName; ?></td>
        <td><? print $salary->ValidFrom; ?></td>
        <td><? print $salary->ValidTo; ?></td>
        <td><? print $salary->PayDate; ?></td>
        <td><? print $salary->DomesticBankAccount; ?></td>
        <td><? print $salary->KommuneNumber . " " . $salary->KommuneName; ?></td>
        <td>
            <? print "Det er forskjellig antall linjer <span style='color: red;'>". $salary->LineDifference ."</span style='color: red;'>. " ?>
            <? print "Det er forskjell p&aring; bel&oslash;p <span style='color: red;'>". $_lib['format']->Amount($salary->SumDifference) ."</span style='color: red;'>. " ?>
        </td>
        <td><a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_VoucherType=L&action_journalid_search=1&voucher_JournalID=".$salary->JournalID; ?>" target="_blank"><? print "L".$salary->JournalID; ?></a></td>
    </tr>
<? } ?>
</tbody>
</table>
</fieldset>
</body>
</html>
