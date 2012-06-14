<?php

$Period = $_REQUEST["Period"];
$Year = substr($Period, 0, 4);
$AccountPlanID = $_REQUEST["AccountPlanID"];

$vat_query = sprintf("SELECT 
                        *
                      FROM
                        vat
                      WHERE 
                        AccountPlanID = %d
                        AND ValidFrom <= '%d-01-01'
                        AND ValidTo >= '%d-01-01'",
                     $AccountPlanID,
                     $Year, $Year+1
    );
$vat = $_lib['storage']->get_row(array('query' => $vat_query));

$voucher_query = sprintf("SELECT
SUM(v.AmountIn - v.AmountOut) as s,
v.VoucherDate, v.VoucherType, v.JournalID

FROM 
voucher as v, 
accountplan as a 

WHERE v.Active=1 
AND v.AccountPlanID = a.AccountPlanID 
AND v.VoucherPeriod = '%s' 
AND a.AccountPlanID = %d
AND (a.AccountPlanType='balance' or a.AccountPlanType='result') 

GROUP BY v.JournalID
ORDER by v.VoucherPeriod",
                         $Period,
                         $AccountPlanID
    );
$voucher_res = $_lib['db']->db_query($voucher_query);

$diary_query = sprintf("SELECT
SUM(v.AmountIn - v.AmountOut) as s,
v.VoucherDate, v.VoucherType, v.JournalID

FROM
voucher as v,
accountplan as a

WHERE v.VatID = '%d'
and v.VoucherPeriod = '%s'
and v.AccountPlanID=a.AccountPlanID
and v.Active=1
GROUP BY v.JournalID
ORDER BY v.VoucherPeriod
",
                       $vat->VatID,
                       $Period
    
    );
$diary_res = $_lib['db']->db_query($diary_query);

$voucher_acc = 0;
$voucher_acc_map = array();

while($row = $_lib['db']->db_fetch_object($voucher_res)) {
    $voucher_acc_map[$row->VoucherType . $row->JournalID] = $row->s;
    $voucher_acc += $row->s;
}

$diary_map = array();
$diary_acc = 0;

while($row = $_lib['db']->db_fetch_object($diary_res)) {
    $diary_map[$row->VoucherType . $row->JournalID] = $row->s;
    $diary_acc += $row->s;
}

?>
<? print $_lib['sess']->doctype ?>
<head>  
  <title>MVA Avstemming, Kontodagbok - 
    <? print $_lib['sess']->get_companydef('VName') ?> - <? print $AccountPlanID ?> i <? print $Period ?>
  </title>
  <? includeinc('head') ?>
</head>
<body>
<h2>Konto <? print $AccountPlanID ?> i periode <? print $Period ?></h2>
<h3><? printf("%s %s%% Kode %d", $vat->Type, $vat->Percent, $vat->VatID) ?></h3>

<?

function merge_keys($a1, $a2) {
  $r = array_keys($a1);

  foreach($a2 as $k => $v) {
    if(array_search($k,$r) === false)
      $r[] = $k;
  }

  sort($r);

  return $r;
}

?>
<table class="lodo">
  <tr>
    <th>Voucher</th>
    <th>Bel&oslash;p</th>
    <th style='width: 20px'></th>
    <th>Diary</th>
    <th>Diary Bel&oslash;p</th>
    <th>Diary VAT</th>
    <th style='width: 20px'></th>
    <th>Diff</th>
  </tr>
  <?
    $diary_vat_acc = 0;
    $voucher_diff_acc = 0;
    $diff_acc = 0;

    foreach(merge_keys($voucher_acc_map, $diary_map) as $voucher) {
        $period = sprintf("%d-%02d", $Year, $i);

        if(isset($voucher_acc_map[$voucher])) {
            $voucher_value = $voucher_acc_map[$voucher];
        }
        else {
            $voucher_value = 0;
        }

        if(isset($diary_map[$voucher])) {
            $diary_value = $diary_map[$voucher];
            $diary_vat = ($diary_value * ($vat->Percent / 100.0)) / (1 + ($vat->Percent / 100.0));
            $diary_vat_acc += $diary_vat;
        }
        else {
            $diary_value = 0;
            $diary_vat = 0;
        }

        $diff = $voucher_value - $diary_vat;
        $diff_acc += $diff;

        printf("
<tr>
  <td>%s</td>
  <td class='number'>%s</td>
  <td></td>
  <td class='number'>%s</td>
  <td class='number'>%s</td>
  <td class='number'>%s</td>
  <td></td>
  <td class='number'>%s</td>
</tr>", 
               $voucher, 
               $_lib['format']->Amount($voucher_value),
               $voucher,
               $_lib['format']->Amount($diary_value),
               $_lib['format']->Amount($diary_vat),
               $_lib['format']->Amount($diff)
            );
    }
     ?>
  <tr><td>-</td></tr>
  <tr>
    <td><b>Sum</b></td>
    <td><b><? print $_lib['format']->Amount($voucher_acc) ?></b></td>
    <td colspan="3"></td>
    <td><b><? print $_lib['format']->Amount($diary_vat_acc) ?></b></td>
    <td></td>
    <td><b><? print $_lib['format']->Amount($diff_acc) ?></b></td>
  </tr>
</table>
