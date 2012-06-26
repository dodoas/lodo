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
                        AND ValidTo >= '%d-12-31'",
                     $AccountPlanID,
                     $Year, $Year
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
<br />
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
    
        $query = sprintf("
SELECT 
  V1.JournalID, V1.VoucherType, V1.VoucherDate
FROM
  voucher AS V1
  LEFT JOIN voucher AS V2 ON (V2.VoucherID = V1.AutomaticVatVoucherID)
  LEFT JOIN voucher AS V3 ON (V3.VoucherID = V2.AutomaticVatVoucherID)
  LEFT JOIN vat AS VAT on (VAT.VatID = V1.VatID AND VAT.ValidFrom <= V1.VoucherDate AND VAT.ValidTo >= V1.VoucherDate)
WHERE
  (V1.Active = 1
   AND V1.VoucherDate >= '%s-01' AND V1.VoucherDate <= ('%s-01' + INTERVAL 1 MONTH))
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
    (VAT.Percent != V1.Vat)
  )
", $Period, $Period);
   
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