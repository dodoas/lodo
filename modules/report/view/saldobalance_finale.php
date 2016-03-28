<?
// Set header to a CSV file, and make it an attachment so it is downloaded and not displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Saldobalanse'.time().'.csv');

// Open output stream as file output
$output = fopen('php://output', 'w');

// Fetch input parameters
$enable_last_year = (bool) $_REQUEST['report_EnableLastYear'];
$from_period = $_REQUEST['report_FromPeriod'];
$to_period = $_REQUEST['report_ToPeriod'];
$result_from_period = $_REQUEST['report_ResultFromPeriod'];

$from_prev_period   = $_lib['date']->get_this_period_last_year($_REQUEST['report_FromPeriod'] . "-01");
$to_prev_period     = $_lib['date']->get_this_period_last_year($_REQUEST['report_ToPeriod'] . "-01");
$result_prev_from   = $_lib['date']->get_this_period_last_year($_REQUEST['report_ResultFromPeriod'] . "-01");

$query_balance = "select A.AccountPlanID, A.AccountName from accountplan A where A.AccountPlanType = 'balance' or A.AccountPlanType = 'result' group by A.AccountPlanID order by A.AccountPlanID asc";
$query_result  = str_replace("balance", "result", $query_balance);
$balance_accounts = $_lib['db']->db_query($query_balance);
$result_accounts = $_lib['db']->db_query($query_result);

// Headings
$headings = array('Account number', 'Account name', 'Amount');
if ($enable_last_year) array_push($headings, 'Amount last year');
fputcsv($output, $headings);

// Loop over balance accounts
while($account = $_lib['db']->db_fetch_object($balance_accounts))
{
    $saldo_old      = 0;
    $query_saldo_old = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $VoucherType V.VoucherPeriod < '$from_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    $voucher_saldo_old = $_lib['storage']->get_row(array('query' => $query_saldo_old));
    $saldo_old      = (round($voucher_saldo_old->sumin, 2) - round($voucher_saldo_old->sumout, 2));

    $saldo_new      = 0;
    $query_saldo    = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $VoucherType V.VoucherPeriod >= '$from_period' and V.VoucherPeriod <= '$to_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    $voucher_saldo  = $_lib['storage']->get_row(array('query' => $query_saldo));
    $saldo_new_sum_in = round($voucher_saldo->sumin, 2);
    $saldo_new_sum_out = round($voucher_saldo->sumout, 2);
    $saldo_new      = $saldo_new_sum_in - $saldo_new_sum_out;

    $sumrow_new     = 0;
    $sumrow_new     = $saldo_old + $saldo_new;

    ################################################################################
    $saldo_prev_old             = 0;
    $query_prev_saldo_old       = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $VoucherType V.VoucherPeriod < '$from_prev_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    $voucher_prev_saldo_old     = $_lib['storage']->get_row(array('query' => $query_prev_saldo_old));
    $saldo_prev_old             = (round($voucher_prev_saldo_old->sumin, 2) - round($voucher_prev_saldo_old->sumout, 2));

    $saldo_prev_new         = 0;
    $query_prev_saldo_new   = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $VoucherType V.VoucherPeriod >= '$from_prev_period' and V.VoucherPeriod <= '$to_prev_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    $voucher_prev_saldo_new = $_lib['storage']->get_row(array('query' => $query_prev_saldo_new));
    $saldo_prev_new_sum_in  = round($voucher_prev_saldo_new->sumin, 2);
    $saldo_prev_new_sum_out = round($voucher_prev_saldo_new->sumout, 2);
    $saldo_prev_new         = $saldo_prev_new_sum_in - $saldo_prev_new_sum_out;

    $sumrow_prev_new    = 0;
    $sumrow_prev_new    = $saldo_prev_old + $saldo_prev_new;

    // if all saldos are zero and account plan is not active then skip it
    if ($saldo_old == 0 && $saldo_new_sum_in == 0 && $saldo_new_sum_out == 0 && $sumrow_new == 0 && $saldo_prev_old == 0 && $saldo_prev_new_sum_in == 0 && $saldo_prev_new_sum_out == 0 && $sumrow_prev_new == 0 && $account->Active == 0) continue;
    $row = array($account->AccountPlanID, utf8_encode($account->AccountName), $_lib['format']->Amount($sumrow_new));
    if ($enable_last_year) array_push($row, $_lib['format']->Amount($sumrow_prev_new));
    // add an account to csv
    fputcsv($output, $row);
}
// Loop over result accounts
while($account = $_lib['db']->db_fetch_object($result_accounts))
{
    $query_saldo_old      = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $VoucherType V.VoucherPeriod >= '$result_from' and V.VoucherPeriod < '$from_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
    $voucher_saldo_old    = $_lib['storage']->get_row(array('query' => $query_saldo_old));
    $saldo_old            = (round($voucher_saldo_old->sumin, 2) - round($voucher_saldo_old->sumout, 2));

    $query_saldo          = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $VoucherType V.VoucherPeriod >= '$from_period' and V.VoucherPeriod <= '$to_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
    $voucher_saldo        = $_lib['storage']->get_row(array('query' => $query_saldo));
    $saldo_new_sum_in  = round($voucher_saldo->sumin, 2);
    $saldo_new_sum_out = round($voucher_saldo->sumout, 2);
    $saldo_new         = $saldo_new_sum_in - $saldo_new_sum_out;

    $query_prev_saldo_old = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $VoucherType V.VoucherPeriod >= '$result_prev_from' and V.VoucherPeriod < '$from_prev_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
    $voucher_prev_saldo_old = $_lib['storage']->get_row(array('query' => $query_prev_saldo_old));
    $saldo_prev_old       = (round($voucher_prev_saldo_old->sumin, 2) - round($voucher_prev_saldo_old->sumout, 2));

    $query_prev_saldo     = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $VoucherType V.VoucherPeriod >= '$from_prev_period' and V.VoucherPeriod <= '$to_prev_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
    $voucher_prev_saldo   = $_lib['storage']->get_row(array('query' => $query_prev_saldo));
    $saldo_prev_new_sum_in  = round($voucher_prev_saldo->sumin, 2);
    $saldo_prev_new_sum_out = round($voucher_prev_saldo->sumout, 2);
    $saldo_prev_new         = $saldo_prev_new_sum_in - $saldo_prev_new_sum_out;

    $sumrow_new           = $saldo_old + $saldo_new;

    $sumrow_prev_new      = $saldo_prev_old + $saldo_prev_new;

    // if all saldos are zero and account plan is not active then skip it
    if ($saldo_old == 0 && $saldo_new_sum_in == 0 && $saldo_new_sum_out == 0 && $sumrow_new == 0 && $saldo_prev_old == 0 && $saldo_prev_new_sum_in == 0 && $saldo_prev_new_sum_out == 0 && $sumrow_prev_new == 0 && $account->Active == 0) continue;
    $row = array($account->AccountPlanID, utf8_encode($account->AccountName), $_lib['format']->Amount($sumrow_new));
    if ($enable_last_year) array_push($row, $_lib['format']->Amount($sumrow_prev_new));
    // add an account to csv
    fputcsv($output, $row);
}
fclose($output);
?>
