<?
# $Id: print.php,v 1.45 2005/10/20 12:58:58 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
$InvoiceID = $_REQUEST['InvoiceID'];
includelogic('auditorreport/auditorreport');

$PeriodYear = $_REQUEST['PeriodYear'];

if (!preg_match("/^[0-9]{4}$/i", $PeriodYear)) {
    die(); // short, safe and ugly, good enough given time constraints
}

require_once "record.inc";

###################################################
# Henter input verdier
#
$_from_period   = $PeriodYear . "-01";
$_to_period     = $PeriodYear . "-13";

$safe_from_period = mysql_escape_string($_from_period);
$safe_to_period = mysql_escape_string($_to_period);

$auditor_report = new lodo_auditorreport_auditorreport(array('PeriodYear'=>$PeriodYear));

$report_lines = $auditor_report->getReportLines();

###################################################
# Her starter selve siden.
#
print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - journal</title>
    <meta name="cvs"                content="$Id: hovedboksaldoliste.php,v 1.55 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

<h2>Revisorrappport <? print $PeriodYear ?></h2>

<table  class="lodo_data">
  <tr class="voucher">
    <th colspan="9">Balanse</th>
  </tr>
  <tr>
    <th class="sub" colspan="2"></th>
    <th class="sub" colspan="3"><? print $PeriodYear ?></th>
  </tr>
  <tr class="voucher">
    <th class="sub">Konto</th>
    <th class="sub">Navn</th>
    <th class="sub">Ny saldo</th>
    <th class="sub">Retting</th>
    <th class="sub"></th>
  </tr>
<?

$sumTotal       = 0;

################################################################################
# looper over alle balanse konto
#
while($report_line = $_lib['db']->db_fetch_object($report_lines))
{
    ################################################################################
    $saldo_old      = 0;
    $query_saldo_old = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod < '$safe_from_period' and V.AccountPlanID='$report_line->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
	#print "$query_saldo_old<br>";
    $voucher_saldo_old = $_lib['storage']->get_row(array('query' => $query_saldo_old));
    $saldo_old      = (round($voucher_saldo_old->sumin, 2) - round($voucher_saldo_old->sumout, 2));

    $saldo_new      = 0;
    $query_saldo    = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where V.VoucherPeriod >= '$safe_from_period' and V.VoucherPeriod <= '$safe_to_period' and V.AccountPlanID='$report_line->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
	#print "$query_saldo<br>";
    $voucher_saldo  = $_lib['storage']->get_row(array('query' => $query_saldo));
    $saldo_new      = (round($voucher_saldo->sumin, 2) - round($voucher_saldo->sumout, 2));

    $sumrow_new     = 0;
    $sumrow_new     = $saldo_old + $saldo_new;
    $sumTotal       += $sumrow_new;

    
    // $urltmp = $url . "&amp;report_FromAccount=$report_line->AccountPlanID&amp;report_ToAccount=$report_line->AccountPlanID";
    ?>
    <tr class="voucher">
        <td><? print $report_line->AccountPlanID ?></td>
        <td><? print $report_line->AccountName ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumrow_new) ?></td>
         <td class="number"><? print $_lib['format']->Amount($report_line->AuditAmount) ?></td>
        <td class="noprint"><!-- <? print $_lib['form3']->URL(array('description' => 'Detaljer', 'url' => $urltmp)) ?> --></td>
    </tr>
    <?
}
$endSum = $sumTotal;

$sumTotal = $_lib['format']->Amount(round($sumTotal, 2));

if($sumTotal != 0)
{
    $printsum = "<font color=\"red\">$sumTotal</font>";
    $printtext = "<font color=\"red\">Sum</font>";
}
else
{
    $printsum = $sumTotal;
    $printtext = "Sum";
}
?>
</table>

</body>
</html>
