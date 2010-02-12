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
#Fin alle Œpne periode
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
#Fin alle Œpne periode
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
#Fin alle Œpne periode
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
<legend>8. Bilag som er f&oslash;rt mot kontoer som ikke er aktive eller kontoer som er slettet</legend>
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
$query_notactive        = "select v.* from voucher as v left join  accountplan as a on a.AccountPlanID=v.AccountPlanID and a.Active=1 where a.AccountPlanID is null and v.Active=1";
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

</body>
</html>