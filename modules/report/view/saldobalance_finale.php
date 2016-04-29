<?
// Fetch input parameters
$enable_last_year = (bool) $_REQUEST['report_EnableLastYear'];
$from_period = $_REQUEST['report_FromPeriod'];
$to_period = $_REQUEST['report_ToPeriod'];
$result_from_period = $_REQUEST['report_ResultFromPeriod'];

list($report_year) = explode("-", $from_period);
$company_name = $_lib['sess']->get_companydef('CompanyName');
$last_year_text = ($enable_last_year) ? utf8_encode(' med fjoraarets') : '';
$filename = $report_year .' '. $company_name .' Saldobalanse'. $last_year_text .'.csv';

// Set header to a CSV file, and make it an attachment so it is downloaded and not displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'. $filename .'"');

// Open output stream as file output
$output = fopen('php://output', 'w');

$from_prev_period   = $_lib['date']->get_this_period_last_year($_REQUEST['report_FromPeriod'] . "-01");
$to_prev_period     = $_lib['date']->get_this_period_last_year($_REQUEST['report_ToPeriod'] . "-01");
$result_prev_from_period = $_lib['date']->get_this_period_last_year($_REQUEST['report_ResultFromPeriod'] . "-01");

$query_balance = "select A.AccountPlanID, A.AccountName, A.AccountPlanType from accountplan A where A.AccountPlanType = 'balance' OR A.AccountPlanType = 'result' group by A.AccountPlanID order by A.AccountPlanID asc";
$accounts_result = $_lib['db']->db_query($query_balance);

// Headings, left here just so we know what each column in CSV is
// $headings = array('Account number', 'Account name', 'Amount');
// if ($enable_last_year) array_push($headings, 'Amount last year');
// fputcsv($output, $headings);

// Loop over balance and result accounts
while($account = $_lib['db']->db_fetch_object($accounts_result))
{
    // get balance before the from periode
    if ($account->AccountPlanType == 'balance') {
      $query_saldo_old_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod < '%s' and V.AccountPlanID='%s' and V.Active=1 group by V.AccountPlanID";
      $voucher_saldo_old_balance = $_lib['storage']->get_row(array('query' => sprintf($query_saldo_old_balance, $from_period, $account->AccountPlanID)));
    } else {
      $query_saldo_old_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod < '%s' and V.AccountPlanID='%s' and V.Active=1 and V.VoucherPeriod >= '%s' group by V.AccountPlanID";
      $voucher_saldo_old_balance = $_lib['storage']->get_row(array('query' => sprintf($query_saldo_old_balance, $from_period, $account->AccountPlanID, $result_from_period)));
    }
    $saldo_old = (round($voucher_saldo_old_balance->sumin, 2) - round($voucher_saldo_old_balance->sumout, 2));

    // get balance from/to selected periods
    $query_saldo_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod >= '%s' and V.VoucherPeriod <= '%s' and V.AccountPlanID='%s' and V.Active=1 group by V.AccountPlanID";
    $voucher_saldo_balance = $_lib['storage']->get_row(array('query' => sprintf($query_saldo_balance, $from_period, $to_period, $account->AccountPlanID)));
    $saldo_new_sum_in = round($voucher_saldo_balance->sumin, 2);
    $saldo_new_sum_out = round($voucher_saldo_balance->sumout, 2);
    $saldo_new = $saldo_new_sum_in - $saldo_new_sum_out;

    // balance at the end of to period
    $sumrow_new = $saldo_old + $saldo_new;

    // get balance before the from periode in previous year
    if ($account->AccountPlanType == 'balance') {
      $query_prev_saldo_old_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod < '%s' and V.AccountPlanID='%s' and V.Active=1 group by V.AccountPlanID";
      $voucher_prev_saldo_old_balance = $_lib['storage']->get_row(array('query' => sprintf($query_prev_saldo_old_balance, $from_prev_period, $account->AccountPlanID)));
    } else {
      $query_prev_saldo_old_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod < '%s' and V.AccountPlanID='%s' and V.Active=1 and V.VoucherPeriod >= '%s' group by V.AccountPlanID";
      $voucher_prev_saldo_old_balance = $_lib['storage']->get_row(array('query' => sprintf($query_prev_saldo_old_balance, $from_prev_period, $account->AccountPlanID, $result_prev_from_period)));
    }
    $saldo_prev_old = (round($voucher_prev_saldo_old_balance->sumin, 2) - round($voucher_prev_saldo_old_balance->sumout, 2));

    // get balance from/to select periods in previous year
    $query_prev_saldo_new_balance = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod >= '%s' and V.VoucherPeriod <= '%s' and V.AccountPlanID='%s' and V.Active=1 group by V.AccountPlanID";
    $voucher_prev_saldo_new_balance = $_lib['storage']->get_row(array('query' => sprintf($query_prev_saldo_new_balance, $from_prev_period, $to_prev_period, $account->AccountPlanID)));
    $saldo_prev_new_sum_in = round($voucher_prev_saldo_new_balance->sumin, 2);
    $saldo_prev_new_sum_out = round($voucher_prev_saldo_new_balance->sumout, 2);
    $saldo_prev_new = $saldo_prev_new_sum_in - $saldo_prev_new_sum_out;

    // balance at the end of to period in previous year
    $sumrow_prev_new = $saldo_prev_old + $saldo_prev_new;

    // if saldo up to start period for this account is 0 and there are no vouchers with incoming/outgoing amount
    // from the from period to the to period for this or the previous year and account plan is not active then skip it
    if ($saldo_old == 0 && $saldo_new_sum_in == 0 && $saldo_new_sum_out == 0 && $sumrow_new == 0 && // this year
        $saldo_prev_old == 0 && $saldo_prev_new_sum_in == 0 && $saldo_prev_new_sum_out == 0 && $sumrow_prev_new == 0 && // previous year
        $account->Active == 0) continue;
    $row = array($account->AccountPlanID, utf8_encode($account->AccountName), $_lib['format']->Amount($sumrow_new));
    if ($enable_last_year) array_push($row, $_lib['format']->Amount($sumrow_prev_new));

    // add an account to csv
    fputs($output, implode($row, ';')."\n");
}
fclose($output);
?>
