<?

includelogic('altinnsalary/files');

// Get the report year, if none given, get the one from LoginFormDate
$LoginFormDate = $_lib['sess']->get_session('LoginFormDate');
$LoginFormYear = $_lib['date']->get_this_year($LoginFormDate);
$ReportYear = (isset($_REQUEST['report_Year'])) ? $_REQUEST['report_Year'] : $LoginFormYear;

$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

function generateSalaryLink($SalaryID, $JournalID){
  global $_lib;

  return 'L <a href="' . $_lib['sess']->dispatch . 't=salary.edit&SalaryID=' . $SalaryID . '">' . $JournalID . '</a>';
}

// Relevant AltinnReport1 entries, non-cancelled and not replaced
// part of the query to be used in multiple other queries
function relevant_altinn_report_1_query($Period) {
  return "
    SELECT *
    FROM
      altinnReport1
    WHERE
      (
        ReceivedStatus IS NULL OR
        ReceivedStatus = 'received'
      ) AND
      (
        CancellationStatus IS NULL OR
        CancellationStatus = 'pending' OR
        CancellationStatus = 'not_cancelled'
      ) AND
      ReplacedByMeldindsID IS NULL AND
      Period LIKE '%$Period%'
    ORDER BY AltinnReport1ID DESC";
}

// Get all reports sent to altinn(for the selected year)
$altinn_report_1_query  = relevant_altinn_report_1_query("$ReportYear-");
$altinn_report_1_result = $_lib['db']->db_query($altinn_report_1_query);

// Reported amounts for salary per period
$salary_amounts = array();
// Process the file containing the sent data for each sent report that should be counted
while ($row = $_lib['db']->db_fetch_object($altinn_report_1_result)) {
  if (!isset($salary_amounts[$row->Period])) {
    $salary_amounts[$row->Period] = 0;
  }

  // Get message sent file
  $altinn_sent_file = new altinn_file($row->Folder);
  $file_sent_contents = $altinn_sent_file->readFile("req_" . $row->AltinnReport1ID . ".xml");

  // If we can read the file get relevant data(reported amounts for salary) from it, if not add an error
  $sent_salary_amount = 0;
  if (!$file_sent_contents) {
    $altinn_row['error'] = "Filen kan ikke leses for rapport 1 id $row->AltinnReport1ID";
  } else {
    $xml_sent = simplexml_load_string($file_sent_contents);
    $sent_messages = $xml_sent->Leveranse->oppgave->virksomhet;
    if ($sent_messages) {
      foreach ($sent_messages as $sent_message) {
        if ($sent_message->arbeidsgiveravgift) {
          $sent_salary_amount += (float)$sent_message->arbeidsgiveravgift->loennOgGodtgjoerelse->avgiftsgrunnlagBeloep;
        }
      }
    }
  }
  $salary_amounts[$row->Period] += $sent_salary_amount;
}
ksort($salary_amounts);

// Get latest report in each period(for the selected year) and its response(which contains the AGA/FTR amounts)
$altinn_report_4_query  = "
  SELECT
    ar1.AltinnReport1ID,
    ar1.Period,
    ar4.*
  FROM
    (
      SELECT
        *
      FROM
        (" .
          relevant_altinn_report_1_query("$ReportYear-") .
       ") ar1_tmp
      GROUP BY Period
    ) ar1
    JOIN
    altinnReport2 ar2
    ON ar1.ReceiptId = ar2.res_ReceiptId
    JOIN
    (
      SELECT
        *
      FROM
        altinnReport4
      GROUP BY res_ArchiveReference
    ) ar4
    ON ar2.res_ReceiversReference = ar4.req_CorrespondenceID
    ORDER BY ar1.Period";
$altinn_report_4_result = $_lib['db']->db_query($altinn_report_4_query);

// Amounts and some info sent to Altinn, one for each period of the year
$altinn_rows = array();

// Process the file containing the end tally for each month 
while($row = $_lib['db']->db_fetch_object($altinn_report_4_result)) {
  // Current report
  $altinn_row = array();

  // Get response message file 
  $altinn_file = new altinn_file($row->Folder);
  $file_contents = $altinn_file->readFile("tilbakemelding" . $row->AltinnReport4ID . ".xml");

  $altinn_row['salary_amount'] = $salary_amounts[$row->Period];

  // Get sum of all OTP reported in this(current report's) period
  $pension_amount_query  = "
    SELECT
      SUM(PensionAmount) AS OTPSum
    FROM
      (" .
        relevant_altinn_report_1_query($row->Period) .
     ") ar1";
  $pension_amount_result = $_lib['db']->db_query($pension_amount_query);
  $pension_amount_row = $_lib['db']->db_fetch_object($pension_amount_result);
  $altinn_row['pension_amount'] = $pension_amount_row->OTPSum;

  // Save the Altinn reference for the current report
  $altinn_reference = $row->res_ArchiveReference;
  $altinn_row['altinn_reference'] = $altinn_reference;

  // If we can read the file get relevant data(reported amounts for AGA/FTR) from it, if not add an error
  if (!$file_contents) {
    $altinn_row['error'] = "Filen kan ikke leses for rapport 4 id $row->AltinnReport4ID";
  } else {
    $xml = simplexml_load_string($file_contents);
    $recieved_messages = $xml->Mottak->mottattLeveranse;
    $altinn_row['aga_amount'] = $xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumArbeidsgiveravgift;
    $altinn_row['ftr_amount'] = -$xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumForskuddstrekk;
  }

  // Sum up for all the months
  if (!isset($altinn_rows[$row->Period])) {
    $altinn_rows[$row->Period] = $altinn_row;
  } else {
    // If error is present we have no data to sum up
    if (isset($altinn_row['error'])) {
      $altinn_rows[$row->Period]['error'] .= '<br/>' . $altinn_row['error'];
    } else {
      $altinn_rows[$row->Period]['aga_amount'] += $altinn_row['aga_amount'];
      $altinn_rows[$row->Period]['altinn_reference'] .= ', ' . $altinn_row['altinn_reference'];
      $altinn_rows[$row->Period]['ftr_amount'] += $altinn_row['ftr_amount'];
    }
  }
}

// Add 0 amounts to the months that are missing
foreach($months as $month) {
  if (!isset($altinn_rows[$ReportYear . '-' . $month])) {
      $altinn_rows[$ReportYear . '-' . $month]['aga_amount'] = 0;
      $altinn_rows[$ReportYear . '-' . $month]['ftr_amount'] = 0;
  }
}
// Sort array by key(period)
ksort($altinn_rows);
?>

<? print $_lib['sess']->doctype ?>
<head>
  <title>Altinn <?= $ReportYear; ?> årlig rapport</title>
  <? includeinc('head') ?>
</head>
<body>
  <? includeinc('top') ?>
  <? includeinc('left') ?>
  <h2>Altinn <?= $ReportYear; ?> årlig rapport</h2>
  <table class="lodo_data">
    <tr>
      <th class="menu">Periode</th>
      <th class="menu">Referanse(r)</th>
      <th class="menu">L&oslash;nn</th>
      <th class="menu">OTP</th>
      <th class="menu">Hittil i &aring;r</th>
      <!-- <th class="menu">AGA Amount</th> -->
      <th class="menu">Forskuddstrekk</th>
      <th class="menu">Error</th>
    </tr>
<?
$amount_so_far = 0;
$salary_amount_sum = 0;
$pension_amount_sum = 0;
$ftr_amount_sum = 0;
// Print all reported to Altinn by period
foreach($altinn_rows as $period => $row) {
  $amount_so_far += $row['salary_amount'] + $row['pension_amount'];
  $salary_amount_sum += $row['salary_amount'];
  $pension_amount_sum += $row['pension_amount'];
  $ftr_amount_sum += $row['ftr_amount'];
?>
    <tr>
      <td><? print $period; ?></td>
      <td><? print $row['altinn_reference']; ?></td>
      <td class="number"><? print $_lib['format']->Amount($row['salary_amount']); ?></td>
      <td class="number"><? print $_lib['format']->Amount($row['pension_amount']); ?></td>
      <td class="number"><? print $_lib['format']->Amount($amount_so_far); ?></td>
      <!-- <td class="number"><? // print $_lib['format']->Amount($row['aga_amount']); ?></td> -->
      <td class="number"><? print $_lib['format']->Amount($row['ftr_amount']); ?></td>
      <td><? print $row['error']; ?></td>
    </tr>
<?
}
// Sum for all periods
?>
    <tr>
      <th>Sum</th>
      <th></th>
      <th class="number"><? print $_lib['format']->Amount($salary_amount_sum); ?></th>
      <th class="number"><? print $_lib['format']->Amount($pension_amount_sum); ?></th>
      <th class="number"><? print $_lib['format']->Amount($amount_so_far); ?></th>
      <!-- <th class="number"><? // print $_lib['format']->Amount($row['aga_amount']); ?></th> -->
      <th class="number"><? print $_lib['format']->Amount($ftr_amount_sum); ?></th>
      <th></th>
    </tr>
  </table>
  <br/>
  <a href="<?= $_lib['sess']->dispatch; ?>report_Year=<?= $ReportYear; ?>&t=report.altinn_report1_setup">Kontooppsett</a>
  <br/>
  <br/>

<?

// Accounts to sum and see how much it is bookkept on each in the periods
// to compare against what is sent to Altinn
$balance_accounts_to_sum = array();
$result_accounts_to_sum = array();
$accounts_for_report_query = "
  SELECT
    ap.AccountPlanID,
    ap.AccountName,
    IF(ap.AccountPlanID < 3000, 'balance', 'result') AS AccountPlanType
  FROM
    accountsforaltinnreport afar
    LEFT JOIN
    accountplan ap
    ON ap.AccountPlanID = afar.AccountPlanID
  WHERE
    afar.Active = 1 AND
    afar.AccountPlanID != 0";
$accounts_for_report_result = $_lib['db']->db_query($accounts_for_report_query);
while($account = $_lib['db']->db_fetch_object($accounts_for_report_result)) {
  if ($account->AccountPlanType == 'balance') {
    $balance_accounts_to_sum[$account->AccountPlanID] = $account->AccountName;
  } else {
    $result_accounts_to_sum[$account->AccountPlanID] = $account->AccountName;
  }
}
ksort($balance_accounts_to_sum);
ksort($result_accounts_to_sum);
if (current($balance_accounts_to_sum)) {
  $ftr_accountplan_id = current(array_keys($balance_accounts_to_sum));

  $CompanyID = $_lib['sess']->get_companydef('CompanyID');
  $company_aga_percent_query = "
    SELECT
      aga.Percent
    FROM
      company c
      LEFT JOIN
      kommune k
      ON c.CompanyMunicipalityID = k.KommuneID
      LEFT JOIN
      arbeidsgiveravgift aga
      ON k.Sone = aga.Code
    WHERE
      c.CompanyID = $CompanyID";
  $company_aga_percent_row = $_lib['db']->get_row(array('query' => $company_aga_percent_query));
  $company_aga_percent = $company_aga_percent_row->Percent;

  // Sum up
  $voucher_accounts_sum = array();
  $voucher_accounts_aga_sum = array();
  foreach (($balance_accounts_to_sum + $result_accounts_to_sum) as $accountplan_id => $accountplan_name) {
    $voucher_accounts_sum[$accountplan_id] = array();
    $voucher_accounts_aga_sum[$accountplan_id] = array();
    $account_sum_query = "
      SELECT
        v.VoucherPeriod AS Period,
        SUM( v.AmountIn - v.AmountOut ) AS Sum,
        SUM( (v.AmountIn - v.AmountOut) * IF(v.AccountPlanID IN (" . implode(', ', array_keys($balance_accounts_to_sum)) . "), aga.Percent, $company_aga_percent)/100) AS AGASum
      FROM
        voucher v
        LEFT JOIN
        salary s
        ON v.JournalID = s.JournalID
        LEFT JOIN
        kommune k
        ON s.KommuneID = k.KommuneID
        LEFT JOIN
        arbeidsgiveravgift aga
        ON k.Sone = aga.Code
      WHERE
        v.VoucherPeriod LIKE  '$ReportYear-%' AND
        v.AccountplanID = $accountplan_id AND
        v.Active = 1
      GROUP BY v.VoucherPeriod";
    $account_sum_result = $_lib['db']->db_query($account_sum_query);
    while($account_sum_row = $_lib['db']->db_fetch_object($account_sum_result)) {
      $voucher_accounts_sum[$accountplan_id][$account_sum_row->Period] = $account_sum_row->Sum;
      $voucher_accounts_aga_sum[$accountplan_id][$account_sum_row->Period] = $account_sum_row->AGASum;
    }
    // Add 0 to missing months
    foreach($months as $month) {
      if (!isset($voucher_accounts_sum[$accountplan_id][$ReportYear . '-' . $month])) {
        $voucher_accounts_sum[$accountplan_id][$ReportYear . '-' . $month] = 0;
      }
      if (!isset($voucher_accounts_aga_sum[$accountplan_id][$ReportYear . '-' . $month])) {
        $voucher_accounts_aga_sum[$accountplan_id][$ReportYear . '-' . $month] = 0;
      }
    }
    // Sort by period
    ksort($voucher_accounts_sum[$accountplan_id]);
    ksort($voucher_accounts_aga_sum[$accountplan_id]);
  }
  // Sum of all except FTR
  $sum_all_accounts_for_period = array();
  $sum_all_accounts_aga_for_period = array();
  // set 0 for extra, salaries from other years
  $sum_all_accounts_for_period['extra_prev'] = 0;
  $sum_all_accounts_aga_for_period['extra_prev'] = 0;
  $sum_all_accounts_for_period['extra_next'] = 0;
  $sum_all_accounts_aga_for_period['extra_next'] = 0;
  // Extra salaries amounts by account
  $extra_salaries_sums_by_account = array();
  $salary_ids_by_journal_id = array();
  $extra_voucher_accounts_sum = array();
  $extra_voucher_accounts_aga_sum = array();
  foreach (($balance_accounts_to_sum + $result_accounts_to_sum) as $accountplan_id => $accountplan_name) {
    $extra_voucher_accounts_sum['prev'][$accountplan_id] = 0;
    $extra_voucher_accounts_sum['next'][$accountplan_id] = 0;
    $extra_voucher_accounts_aga_sum['prev'][$accountplan_id] = 0;
    $extra_voucher_accounts_aga_sum['next'][$accountplan_id] = 0;
    $extra_salary_query = "
      SELECT
        v.JournalID,
        s.SalaryID,
        YEAR(s.ActualPayDate) AS AltinnReportedYear,
        YEAR(s.JournalDate) AS JournalYear,
        v.AmountIn,
        v.AmountOut,
        ROUND(IF(v.AccountPlanID IN (" . implode(', ', array_keys($balance_accounts_to_sum)) . "), aga.Percent, $company_aga_percent)/100, 5) AS AGAPercent
      FROM
        voucher v
        LEFT JOIN
        salary s
        ON v.JournalID = s.JournalID
        LEFT JOIN
        kommune k
        ON s.KommuneID = k.KommuneID
        LEFT JOIN
        arbeidsgiveravgift aga
        ON k.Sone = aga.Code
      WHERE
        v.JournalID IN (
          SELECT
            DISTINCT(SalaryJournalID)
          FROM
            salaryreportaccount
          WHERE
            Year = $ReportYear AND
            DifferentYear = 1 AND
            SalaryJournalID IS NOT NULL
        ) AND
        v.AccountplanID = $accountplan_id AND
        v.Active = 1
    ";
    $extra_salary_result = $_lib['db']->db_query($extra_salary_query);
    while($extra_salary = $_lib['db']->db_fetch_object($extra_salary_result)) {
      $salary_ids_by_journal_id[$extra_salary->JournalID] = $extra_salary->SalaryID;
      $extra_account_sum = $extra_salary->AmountIn - $extra_salary->AmountOut;
      $extra_account_aga_sum = round($extra_account_sum * $extra_salary->AGAPercent, 2);
      if ($ReportYear < $extra_salary->AltinnReportedYear) {
        $extra_account_sum = -$extra_account_sum;
        $extra_account_aga_sum = -$extra_account_aga_sum;
      }
      if ($extra_salary->JournalYear == ($ReportYear-1)) {
        if ($accountplan_id >= 3000) {
          $sum_all_accounts_for_period['extra_prev'] += $extra_account_sum;
          $sum_all_accounts_aga_for_period['extra_prev'] += $extra_account_aga_sum;
        }
        if (!isset($extra_voucher_accounts_sum['prev'][$accountplan_id])) {
          $extra_voucher_accounts_sum['prev'][$accountplan_id] = 0;
        }
        if (!isset($extra_voucher_accounts_aga_sum['prev'][$accountplan_id])) {
          $extra_voucher_accounts_aga_sum['prev'][$accountplan_id] = 0;
        }
        $extra_voucher_accounts_sum['prev'][$accountplan_id] += $extra_account_sum;
        $extra_voucher_accounts_aga_sum['prev'][$accountplan_id] += $extra_account_aga_sum;
      } elseif ($extra_salary->JournalYear == $ReportYear) {
        if ($accountplan_id >= 3000) {
          $sum_all_accounts_for_period['extra_next'] += $extra_account_sum;
          $sum_all_accounts_aga_for_period['extra_next'] += $extra_account_aga_sum;
        }
        if (!isset($extra_voucher_accounts_sum['next'][$accountplan_id])) {
          $extra_voucher_accounts_sum['next'][$accountplan_id] = 0;
        }
        if (!isset($extra_voucher_accounts_aga_sum['next'][$accountplan_id])) {
          $extra_voucher_accounts_aga_sum['next'][$accountplan_id] = 0;
        }
        $extra_voucher_accounts_sum['next'][$accountplan_id] += $extra_account_sum;
        $extra_voucher_accounts_aga_sum['next'][$accountplan_id] += $extra_account_aga_sum;
      }
      if (!isset($extra_salaries_sums_by_account[$extra_salary->JournalID])) {
        $extra_salaries_sums_by_account[$extra_salary->JournalID] = array();
      }
      $extra_salaries_sums_by_account[$extra_salary->JournalID][$accountplan_id] = -$extra_account_sum;
      $extra_salaries_sums_by_account[$extra_salary->JournalID]['JournalYear'] = $extra_salary->JournalYear;
    }
  }
  ?>

  <table class="lodo_data">
    <tr>
      <th class="menu">Periode</th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
      <th class="menu">Sum</th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
    </tr>

  <?
  // Sum by account for all periods
  $yearly_sum_by_account = array();
  // Initiate all sums to 0
  foreach ($months as $month) {
    $period = $ReportYear . '-' . $month;
    $sum_all_accounts_for_period[$period] = 0;
    $sum_all_accounts_aga_for_period[$period] = 0;
  ?>
    <tr>
      <td><? print $period; ?></td>
  <?
    foreach ($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
      // Get the amount which is bookkept
      $amount = $voucher_accounts_sum[$accountplan_id][$period];
      $aga_amount = $voucher_accounts_aga_sum[$accountplan_id][$period];
      // If nothing set, set to 0
      if (!isset($yearly_sum_by_account[$accountplan_id])) $yearly_sum_by_account[$accountplan_id] = 0;
      $yearly_sum_by_account[$accountplan_id] += $amount;
      $sum_all_accounts_for_period[$period] += $amount;
      $sum_all_accounts_aga_for_period[$period] += $aga_amount;

      
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
    // Sum all
    if (!isset($yearly_sum_by_account['sum'])) $yearly_sum_by_account['sum'] = 0;
    $yearly_sum_by_account['sum'] += $sum_all_accounts_for_period[$period];
  ?>
      <td class="number"><? print $_lib['format']->Amount($sum_all_accounts_for_period[$period]); ?></td>
  <?
    foreach ($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
      // Get the amount which is bookkept
      $amount = $voucher_accounts_sum[$accountplan_id][$period];
      // If nothing set, set to 0
      if (!isset($yearly_sum_by_account[$accountplan_id])) $yearly_sum_by_account[$accountplan_id] = 0;
      $yearly_sum_by_account[$accountplan_id] += $amount;
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
  ?>
    </tr>
  <?
  }
  // Sums for all bookkept amounts
  ?>
    <tr>
      <th>Sum</th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($yearly_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
      <th class="number"><? print $_lib['format']->Amount($yearly_sum_by_account['sum']); ?></th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($yearly_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
    </tr>
  </table>

  <br/><br/>

  <table class="lodo_data">
    <tr>
      <th class="menu">Periode</th>
      <th class="menu">Altinn Lodo diff</th>
      <th class="menu">Akkumulert diff</th>
      <th class="menu">AGA rapportert</th>
      <th class="menu">AGA kalkulert</th>
      <th class="menu">AGA diff</th>
      <th class="menu">FTR rapportert</th>
      <th class="menu">FTR regnskap</th>
      <th class="menu">FTR diff</th>
    </tr>
  <?
  // Differences between what has been reported and what was bookkept
  $altinn_lodo_diff_so_far = 0;
  // Start from 0
  $sums = array(
    'salary_pension_diff' => 0,
    'reported_aga' => 0,
    'bookkept_aga' => 0,
    'diff_aga' => 0,
    'reported_ftr' => 0,
    'bookkept_ftr' => 0,
    'diff_ftr' => 0
  );
  foreach($altinn_rows as $period => $row) {
    // Diff between salary and pension reported and bookkept
    $altinn_salary_and_pension_amount = $row['salary_amount'] + $row['pension_amount'];
    $lodo_salary_and_pension_amount = $sum_all_accounts_for_period[$period];
    $altinn_lodo_diff_amount = $altinn_salary_and_pension_amount - $lodo_salary_and_pension_amount;
    $sums['salary_pension_diff'] += $altinn_lodo_diff_amount;

    // Diff between AGA/FTR reported and bookkept
    $reported_aga_amount = $altinn_rows[$period]['aga_amount'];
    $sums['reported_aga'] += $reported_aga_amount;
    $bookkept_aga_amount = $sum_all_accounts_aga_for_period[$period];
    $sums['bookkept_aga'] += $bookkept_aga_amount;
    $diff_aga_amount = $reported_aga_amount - $bookkept_aga_amount;
    $sums['diff_aga'] += $diff_aga_amount;
    $reported_ftr_amount = $altinn_rows[$period]['ftr_amount'];
    $sums['reported_ftr'] += $reported_ftr_amount;
    $bookkept_ftr_amount = $voucher_accounts_sum[$ftr_accountplan_id][$period];
    $sums['bookkept_ftr'] += $bookkept_ftr_amount;
    $diff_ftr_amount = $reported_ftr_amount - $bookkept_ftr_amount;
    $sums['diff_ftr'] += $diff_ftr_amount;
  ?>
    <tr>
      <td><? print $period; ?></td>
      <td class="number"><? print $_lib['format']->Amount($altinn_lodo_diff_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_ftr_amount); ?></td>
    </tr>
  <?
  }
  // Sum columns for all periods
  ?>
    <tr>
      <th>Sum</th>
      <th class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['reported_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['bookkept_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['diff_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['reported_ftr']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['bookkept_ftr']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['diff_ftr']); ?></th>
    </tr>
  <?php
  // Extra, salaries from other years
  $period = 'extra_prev';
  // Diff between salary and pension reported and bookkept
  $altinn_salary_and_pension_amount = 0;
  $lodo_salary_and_pension_amount = $sum_all_accounts_for_period[$period];
  $altinn_lodo_diff_amount = $altinn_salary_and_pension_amount - $lodo_salary_and_pension_amount;
  $sums['salary_pension_diff'] += $altinn_lodo_diff_amount;

  // Diff between AGA/FTR reported and bookkept
  $reported_aga_amount = 0;
  $sums['reported_aga'] += $reported_aga_amount;
  $bookkept_aga_amount = $sum_all_accounts_aga_for_period[$period];
  $sums['bookkept_aga'] += $bookkept_aga_amount;
  $diff_aga_amount = $reported_aga_amount - $bookkept_aga_amount;
  $sums['diff_aga'] += $diff_aga_amount;
  $reported_ftr_amount = 0;
  $sums['reported_ftr'] += $reported_ftr_amount;
  $bookkept_ftr_amount = $extra_voucher_accounts_sum['prev'][$ftr_accountplan_id];
  $sums['bookkept_ftr'] += $bookkept_ftr_amount;
  $diff_ftr_amount = $reported_ftr_amount - $bookkept_ftr_amount;
  $sums['diff_ftr'] += $diff_ftr_amount;
  ?>
    <tr>
      <td>Sum l&oslash;nnslipper <?= ($ReportYear-1) ?> - <?= $ReportYear ?></td>
      <td class="number"><? print $_lib['format']->Amount($altinn_lodo_diff_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_ftr_amount); ?></td>
    </tr>
  <?
  // Extra, salaries from other years
  $period = 'extra_next';
  // Diff between salary and pension reported and bookkept
  $altinn_salary_and_pension_amount = 0;
  $lodo_salary_and_pension_amount = $sum_all_accounts_for_period[$period];
  $altinn_lodo_diff_amount = $altinn_salary_and_pension_amount - $lodo_salary_and_pension_amount;
  $sums['salary_pension_diff'] += $altinn_lodo_diff_amount;

  // Diff between AGA/FTR reported and bookkept
  $reported_aga_amount = 0;
  $sums['reported_aga'] += $reported_aga_amount;
  $bookkept_aga_amount = $sum_all_accounts_aga_for_period[$period];
  $sums['bookkept_aga'] += $bookkept_aga_amount;
  $diff_aga_amount = $reported_aga_amount - $bookkept_aga_amount;
  $sums['diff_aga'] += $diff_aga_amount;
  $reported_ftr_amount = 0;
  $sums['reported_ftr'] += $reported_ftr_amount;
  $bookkept_ftr_amount = $extra_voucher_accounts_sum['next'][$ftr_accountplan_id];
  $sums['bookkept_ftr'] += $bookkept_ftr_amount;
  $diff_ftr_amount = $reported_ftr_amount - $bookkept_ftr_amount;
  $sums['diff_ftr'] += $diff_ftr_amount;
  ?>
    <tr>
      <td>Sum l&oslash;nnslipper <?= $ReportYear ?> - <?= ($ReportYear+1) ?></td>
      <td class="number"><? print $_lib['format']->Amount($altinn_lodo_diff_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_aga_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($reported_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($bookkept_ftr_amount); ?></td>
      <td class="number"><? print $_lib['format']->Amount($diff_ftr_amount); ?></td>
    </tr>
  <?
  // Sum columns for all periods
  ?>
    <tr>
      <th>Sum</th>
      <th class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['salary_pension_diff']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['reported_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['bookkept_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['diff_aga']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['reported_ftr']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['bookkept_ftr']); ?></th>
      <th class="number"><? print $_lib['format']->Amount($sums['diff_ftr']); ?></th>
    </tr>
  </table>

  <br/><br/>

  <?= ($ReportYear-1) ?> - <?= $ReportYear ?>
  <table class="lodo_data">
    <tr>
      <th class="menu">L&oslash;nn</th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
      <th class="menu">Sum</th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
    </tr>

  <?
  // Sum by account for all salaries
  $salary_prev_sum_by_account = array();
  // Initiate all sums to 0
  $sum_all_accounts_for_salary = array();
  foreach ($extra_salaries_sums_by_account as $JournalID => $salary) {
    $sum_all_accounts_for_salary[$JournalID] = 0;
    $SalaryID = $salary_ids_by_journal_id[$JournalID];
    if ($salary['JournalYear'] != ($ReportYear-1)) continue;
  ?>
    <tr>
      <td><? print generateSalaryLink($SalaryID, $JournalID); ?></td>
  <?
    foreach ($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
      $amount = -$salary[$accountplan_id];
      // If nothing set, set to 0
      if (!isset($salary_prev_sum_by_account[$accountplan_id])) $salary_prev_sum_by_account[$accountplan_id] = 0;
      $salary_prev_sum_by_account[$accountplan_id] += $amount;
      $sum_all_accounts_for_salary[$JournalID] += $amount;

      
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
    // Sum all
    // If nothing set, set to 0
    if (!isset($salary_prev_sum_by_account['sum'])) $salary_prev_sum_by_account['sum'] = 0;
    $salary_prev_sum_by_account['sum'] += $sum_all_accounts_for_salary[$JournalID];
  ?>
      <td class="number"><? print $_lib['format']->Amount($sum_all_accounts_for_salary[$JournalID]); ?></td>
  <?
    foreach ($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
      $amount = -$salary[$accountplan_id];
      // If nothing set, set to 0
      if (!isset($salary_prev_sum_by_account[$accountplan_id])) $salary_prev_sum_by_account[$accountplan_id] = 0;
      $salary_prev_sum_by_account[$accountplan_id] += $amount;
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
  ?>
    </tr>
  <?
  }
  // Sums for all extra salaries
  ?>
    <tr>
      <th>Sum <?= ($ReportYear-1) ?> - <?= $ReportYear ?></th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_prev_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_prev_sum_by_account['sum']); ?></th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_prev_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
    </tr>
  </table>
  <br/><br/>

  <?= $ReportYear ?> - <?= ($ReportYear+1) ?>
  <table class="lodo_data">
    <tr>
      <th class="menu">L&oslash;nn</th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
      <th class="menu">Sum</th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="menu"><?= $accountplan_id . " " . $accountplan_name; ?></th>
  <?
  }
  ?>
    </tr>

  <?
  // Sum by account for all salaries
  $salary_next_sum_by_account = array();
  // Initiate all sums to 0
  $sum_all_accounts_for_salary = array();
  foreach ($extra_salaries_sums_by_account as $JournalID => $salary) {
    $sum_all_accounts_for_salary[$JournalID] = 0;
    $SalaryID = $salary_ids_by_journal_id[$JournalID];
    if ($salary['JournalYear'] != $ReportYear) continue;
  ?>
    <tr>
      <td><? print generateSalaryLink($SalaryID, $JournalID); ?></td>
  <?
    foreach ($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
      $amount = -$salary[$accountplan_id];
      // If nothing set, set to 0
      if (!isset($salary_next_sum_by_account[$accountplan_id])) $salary_next_sum_by_account[$accountplan_id] = 0;
      $salary_next_sum_by_account[$accountplan_id] += $amount;
      $sum_all_accounts_for_salary[$JournalID] += $amount;

      
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
    // Sum all
    // If nothing set, set to 0
    if (!isset($salary_next_sum_by_account['sum'])) $salary_next_sum_by_account['sum'] = 0;
    $salary_next_sum_by_account['sum'] += $sum_all_accounts_for_salary[$JournalID];
  ?>
      <td class="number"><? print $_lib['format']->Amount($sum_all_accounts_for_salary[$JournalID]); ?></td>
  <?
    foreach ($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
      $amount = -$salary[$accountplan_id];
      // If nothing set, set to 0
      if (!isset($salary_next_sum_by_account[$accountplan_id])) $salary_next_sum_by_account[$accountplan_id] = 0;
      $salary_next_sum_by_account[$accountplan_id] += $amount;
  ?>
      <td class="number"><? print $_lib['format']->Amount($amount); ?></td>
  <?
    }
  ?>
    </tr>
  <?
  }
  // Sums for all extra salaries
  ?>
    <tr>
      <th>Sum <?= $ReportYear ?> - <?= ($ReportYear+1) ?></th>
  <?
  foreach($result_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_next_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_next_sum_by_account['sum']); ?></th>
  <?
  foreach($balance_accounts_to_sum as $accountplan_id => $accountplan_name) {
  ?>
      <th class="number"><? print $_lib['format']->Amount($salary_next_sum_by_account[$accountplan_id]); ?></th>
  <?
  }
  ?>
    </tr>
  </table>
  <a href="<?= $_lib['sess']->dispatch ?>t=salary.employeereport&year=<?= $ReportYear ?>">L&oslash;nnsrapport for <?= $ReportYear ?></a>

  <br/><br/>
  <?php
} else {
?>
  <h2>Velg en balansekonto i kontooppsettet</h2>
<?php
}
?>

</body>
</html>
