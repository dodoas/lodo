<?php

$Year = (isset($_REQUEST["Year"]) ? $_REQUEST["Year"] :$_REQUEST["report_Year"]);
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
                     $Year, $Year + 1
    );

$vat = $_lib['storage']->get_row(array('query' => $vat_query));

$voucher_query = sprintf("SELECT
SUM(v.AmountIn - v.AmountOut) as s,
v.VoucherPeriod

FROM 
voucher as v, 
accountplan as a 

WHERE v.Active=1 
AND v.AccountPlanID=a.AccountPlanID 
AND v.VoucherPeriod <= '%d-13' 
AND a.AccountPlanID = %d
AND (a.AccountPlanType='balance' or a.AccountPlanType='result') 

GROUP by v.VoucherPeriod
ORDER by v.VoucherPeriod",
                         $Year,
                         $AccountPlanID
    );
$voucher_res = $_lib['db']->db_query($voucher_query);

$diary_query = sprintf("SELECT
SUM(v.AmountIn - v.AmountOut) as s,
v.VoucherPeriod

FROM
voucher as v,
accountplan as a

WHERE v.VatID = '%d'
and v.VoucherPeriod >= '%d-01'
and v.VoucherPeriod <= '%d-13'
and v.AccountPlanID=a.AccountPlanID
and v.Active=1
GROUP BY v.VoucherPeriod
ORDER BY v.VoucherPeriod",
                       $vat->VatID,
                       $Year, $Year
    
    );
$diary_res = $_lib['db']->db_query($diary_query);

$voucher_acc = 0;
$voucher_acc_map = array();
$voucher_in_found = false;
$voucher_in_value = 0;

while($row = $_lib['db']->db_fetch_object($voucher_res)) {
    if(!$voucher_in_found && substr($row->VoucherPeriod, 0, 4) == $Year) {
        $voucher_in_found = true;
        $voucher_in_value = $voucher_acc;
    }

    $voucher_acc_map[$row->VoucherPeriod] = array($voucher_acc, $voucher_acc + $row->s);
    $voucher_acc += $row->s;
}

$diary_map = array();
$diary_acc = 0;

while($row = $_lib['db']->db_fetch_object($diary_res)) {
    $diary_map[$row->VoucherPeriod] = $row->s;
    $diary_acc += $row->s;
}

?>
<? print $_lib['sess']->doctype ?>
<head>  
  <title>MVA Avstemming, Kontodagbok - 
    <? print $_lib['sess']->get_companydef('VName') ?> - <? print $AccountPlanID ?> i <? print $Year ?>
  </title>
  <? includeinc('head') ?>
</head>
<body>
<h2>Konto <? print $AccountPlanID ?> i &aring;r <? print $Year ?></h2>
<h3><? printf("%s %s%% Kode %d", $vat->Type, $vat->Percent, $vat->VatID) ?></h3>

<?


?>
<table class="lodo">
  <tr>
    <th>Periode</th>
    <th>Voucher</th>
    <th>Voucher diff</th>
    <th style='width: 20px'></th>
    <th>Diary</th>
    <th>Diary VAT</th>
    <th style='width: 20px'></th>
    <th>Diff</th>
  </tr>
  <tr>
    <td>Inn fra <? print ($Year - 1) ?></td>
    <td class="number"><? print $_lib['format']->Amount($voucher_in_value) ?></td>
  </tr>

  <?
    $diary_vat_acc = 0;
    $voucher_diff_acc = 0;
    $diff_acc = 0;
    for($i = 1; $i <= 13; $i++) {
        $period = sprintf("%d-%02d", $Year, $i);

        if(isset($voucher_acc_map[$period])) {
            $voucher_in_value = $voucher_acc_map[$period][1];
            $voucher_diff = $voucher_acc_map[$period][1] - $voucher_acc_map[$period][0];
            $voucher_diff_acc += $voucher_diff;
        }
        else {
            $voucher_diff = 0;
        }

        if(isset($diary_map[$period])) {
            $diary_value = $diary_map[$period];
            $diary_vat = ($diary_value * ($vat->Percent / 100.0)) / (1 + ($vat->Percent / 100.0));
            $diary_vat_acc += $diary_vat;
        }
        else {
            $diary_value = 0;
            $diary_vat = 0;
        }

        $diff = $voucher_diff - $diary_vat;
        $diff_acc += $diff;

        printf("
<tr style='background-color: %s'>
  <td>%s</td>
  <td class='number'>%s</td>
  <td class='number'>%s</td>
  <td></td>
  <td class='number'>%s</td>
  <td class='number'>%s</td>
  <td></td>
  <td class='number'>%s</td>
  <td>
    <a href='%st=mvaavstemming.view_diary_period&AccountPlanID=%d&Period=%s'>rapport</a>
  </td>
</tr>", 
               ($i % 2 == 0 ? "white" : "#ccc"),
               $period, 
               $_lib['format']->Amount($voucher_in_value),
               $_lib['format']->Amount($voucher_diff),
               $_lib['format']->Amount($diary_value),
               $_lib['format']->Amount($diary_vat),
               $_lib['format']->Amount($diff),
               $_lib['sess']->dispatch, $AccountPlanID, $period

            );
    }
     ?>
  <tr><td>-</td></tr>
  <tr>
    <td><b>Sum</b></td>
    <td><b><? print $_lib['format']->Amount($voucher_acc) ?></b></td>
    <td><b><? print $_lib['format']->Amount($voucher_diff_acc) ?></b></td>
    <td colspan="2"></td>
    <td><b><? print $_lib['format']->Amount($diary_vat_acc) ?></b></td>
    <td></td>
    <td><b><? print $_lib['format']->Amount($diff_acc) ?></b></td>
  </tr>
</table>
