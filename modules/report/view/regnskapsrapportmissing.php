<?
$EndPeriod                = $_REQUEST['EndPeriod'];
$StartPeriod              = $_REQUEST['StartPeriod'];
$ThisYearAmount           = $_REQUEST['ThisYearAmount'];
$LastYearAmount           = $_REQUEST['LastYearAmount'];
$YearAmount               = $_REQUEST['YearAmount'];
$ThisDate                 = $_lib['sess']->get_session('LoginFormDate');
$ThisYear                 = substr($ThisDate,0,4);
$PreviousYear             = $ThisYear - 1;
$PreviousYearStart        = $PreviousYear . '-01';
$PreviousYearEnd          = $PreviousYear . '-13';
$PreviousYearStartPeriod  = $PreviousYear . '-' . substr($StartPeriod, 5, 2);
$PreviousYearEndPeriod    = $PreviousYear . '-' . substr($EndPeriod, 5, 2);

$AccountPlansQuery = "SELECT
    ap.AccountPlanID AS LineID,
    ap.AccountName AS LineText,
    SUM( IF(v.VoucherPeriod >= '$StartPeriod' AND v.VoucherPeriod <= '$EndPeriod', v.AmountIn-v.AmountOut, 0) ) AS ThisYearAmount,
    SUM( IF(v.VoucherPeriod >= '$PreviousYearStartPeriod' AND v.VoucherPeriod <= '$PreviousYearEndPeriod', v.AmountIn-v.AmountOut, 0) ) AS LastYearAmount,
    SUM( IF(v.VoucherPeriod >= '$PreviousYearStart' AND v.VoucherPeriod <= '$PreviousYearEnd', v.AmountIn-v.AmountOut, 0) ) AS Year
  FROM
    voucher v
    JOIN
    accountplan ap
    ON v.AccountPlanID = ap.AccountPlanID
  WHERE
    v.Active = 1 AND
    v.AccountPlanID IN (
      SELECT
        AccountPlanID
      FROM
        accountplan ap
      WHERE
        ap.EnableReportShort = 0 AND
        ap.AccountPlanType = 'result'
      )
  GROUP BY LineID";
$AccountPlansResult = $_lib['db']->db_query($AccountPlansQuery);
$AccountPlans = array();
while($AccountPlan = $_lib['db']->db_fetch_object($AccountPlansResult)) $AccountPlans[] = $AccountPlan;
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Kortfattet rapport (ReportID=100)</title>
    <meta name="cvs"                content="$Id: regnskapsrapport.php,v 1.15 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>
<?
if (empty($AccountPlans)) {
  print "<h1>Ingen konto funnet!</h1>";
}
else {
  $sumThisYear = 0;
  $sumLastYear = 0;
  $sumYear     = 0;
?>
<br><br>
<h1>Funnet mulige manglende kontoer:</h1>
<table border="0" cellspacing="0" class="bordered regnskapsrapport_table">
    <thead>
        <tr>
            <td class="column_konto_percent"></td>
            <th class="column_konto_name">Manglende bel&oslashpet</th>
            <td class="column_amount number"><? print $_lib['format']->Amount($ThisYearAmount) ?></td>
            <td class="column_amount number"><? print $_lib['format']->Amount($LastYearAmount) ?></td>
            <td class="column_amount number"><? print $_lib['format']->Amount($YearAmount) ?></td>
        </tr>
        <tr>
            <th class="column_konto_percent">Konto</th>
            <th class="column_konto_name">Kontonavn</th>
            <th class="column_amount">Fra <? print $StartPeriod ?> til <? print $EndPeriod ?></th>
            <th class="column_amount">Fra <? print $PreviousYearStart ?> til <? print $PreviousYearEnd ?></th>
            <th class="column_amount">&Aring;ret <? print $PreviousYear ?></th>
        </tr>
    </thead>
    <tbody>
        <?
          foreach($AccountPlans as $AccountPlan) {
            $AccountPlanEditLink = $_lib['sess']->dispatch . "t=accountplan.hovedbok&accountplan.AccountPlanID=" . $AccountPlan->LineID . "&accountplan_type=result";
            $sumThisYear += $AccountPlan->ThisYearAmount;
            $sumLastYear += $AccountPlan->LastYearAmount;
            $sumYear     += $AccountPlan->Year;
        ?>
                <tr>
                    <th class="sub"><a href="<? print $AccountPlanEditLink; ?>"><? print $AccountPlan->LineID; ?></a></th>
                    <th class="sub"><a href="<? print $AccountPlanEditLink; ?>"><? print $AccountPlan->LineText ?></a></th>
                    <td class="number"><? print $_lib['format']->Amount($AccountPlan->ThisYearAmount) ?></th>
                    <td class="number"><? print $_lib['format']->Amount($AccountPlan->LastYearAmount) ?></th>
                    <td class="number"><? print $_lib['format']->Amount($AccountPlan->Year) ?></th>
                </tr>
        <? } ?>
                <tr>
                    <th class="sub">Sum</th>
                    <th class="sub"></td>
                    <th class="number"><? print $_lib['format']->Amount($sumThisYear) ?></td>
                    <th class="number"><? print $_lib['format']->Amount($sumLastYear) ?></td>
                    <th class="number"><? print $_lib['format']->Amount($sumYear) ?></td>
                </tr>
    </tbody>
</table>
<? } ?>
</body>
</html>
