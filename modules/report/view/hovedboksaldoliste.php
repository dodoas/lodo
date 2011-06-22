<?
###################################################
# Henter input verdier
#
$_active            = (int) $_REQUEST['accountplan_Active'];
$EnableBudget       = (int) $_REQUEST['report_EnableBudget'];
$EnableLastYear     = (int) $_REQUEST['report_EnableLastYear'];
$_from_period       = $_REQUEST['report_FromPeriod'];
$_to_period         = $_REQUEST['report_ToPeriod'];
$_result_from       = $_REQUEST['report_ResultFromPeriod'];
$_type              = $_REQUEST['report_type'];
$_vouchertype       = $_REQUEST['report_VoucherType'];

$_from_prev_period   = $_lib['date']->get_this_period_last_year($_REQUEST['report_FromPeriod'] . "-01");
$_to_prev_period     = $_lib['date']->get_this_period_last_year($_REQUEST['report_ToPeriod'] . "-01");
$_result_prev_from   = $_lib['date']->get_this_period_last_year($_REQUEST['report_ResultFromPeriod'] . "-01");

###################################################
# Henter og setter project id hvis valgt
#
$_department    = $_REQUEST['report_DepartmentID'];
$_project       = $_REQUEST['report_ProjectID'];

if(strlen($_department) > 0)
{
    $_departmentID = "V.DepartmentID='$_department' and";
}
else
{
    $_departmentID = "";
}

if(strlen($_project) > 0)
{
    $_projectID = "V.ProjectID='$_project' and";
}
else
{
    $_projectID = "";
}

if(strlen($_vouchertype) > 0)
{
    $VoucherType = "V.VoucherType='$_vouchertype' and";
}
else
{
    $VoucherType = "";
}

###################################################
# Henter reskontro til hovedbok og event overstyrer hvis det er valgt
#
$query 			 = "select AccountName, ReskontroAccountPlanType, AccountPlanType from accountplan where AccountPlanID='".$_REQUEST['report_selectedAccount']."'";
$selectedaccount = $_lib['storage']->get_row(array('query' => $query));

$accountName = $selectedaccount->AccountName;

$_reskontroFrom = $_REQUEST['report_ReskontroFromAccount'];
$_reskontroTo 	= $_REQUEST['report_ReskontroToAccount'];

if($_REQUEST['report_selectedAccount']) {
    $url = $_lib['sess']->dispatch . 't=report.reskontrovoucherprint';
} else {
    $url = $_lib['sess']->dispatch . 't=report.hovedbokvoucherprint';
}
foreach($_POST as $key => $sumO->Amount) {
    if($key != 't' && $key != 'AccountPlanID' && $key != 'SID' && $key != 'show_report_search' && $key != 'report_ToAccount' && $key != 'report_FromAccount') {
       $url .= "&amp;$key=$sumO->Amount";
    }
}

###################################################
# Sjekker om det er valgt for hovedbok eller reskontroer.
# og sp¯r etter akutelle konto fra databasen.
#

function get_budget($AccountplanID, $FromPeriod, $ToPeriod) {
  global $_date, $_lib;
  $from_year = $_lib['date']->get_this_year($FromPeriod);
  $to_year   = $_lib['date']->get_this_year($ToPeriod);
  if($from_year == $to_year ) {
    #print "Start: Konto:$AccountplanID, FromPeriod: $FromPeriod, ToPeriod: $ToPeriod<br>";
    $query = "select bl.* from budget as b, budgetline as bl where b.BudgetID=bl.BudgetID and b.PeriodYear='$from_year' and bl.AccountPlanID=$AccountplanID and b.Type='result'";
    #print "$query<br>";
    $row = $_lib['storage']->get_row(array('query' => $query));

    while($FromPeriod <= $ToPeriod) {
      $month        = intval($_lib['date']->get_this_month($FromPeriod));
      $periodin     = "Period" . $month . "In";
      $periodout    = "Period" . $month . "Out";
      $sum          = $row->{$periodout} - $row->{$periodin};
      $total       += $sum;
      #print "Sum: $sum, FromPeriod: $FromPeriod, Month: $month, $periodout: " . $row->{$periodout} . " - $periodin: " . $row->{$periodin} . "<br>";
      $FromPeriod   = $_lib['date']->get_next_period($FromPeriod);
    }

  } else {
    print "Budsjettet må være fra samme &aring;r.<br>";
  }
  #print "Budget: $total, $query<br>";
  return $total;
}

###################################################
# Sjekker om det er valgt for hovedbok eller reskontroer.
# og sp¯r etter akutelle konto fra databasen.
#
if($_type == 'hovedbok')
{
  if($_active == 1)
  {
      #$safe_from_period = mysql_escape_string($_from_period);
      #$safe_to_period = mysql_escape_string($_to_period);
      $safe_from_period = mysql_escape_string($_from_prev_period);
      $safe_to_period = mysql_escape_string($_to_period);

      /* henter ut aktive kontoer og kontoer satt til uaktiv, men allikevel har en voucher registrert --m */
      $query_balance = "SELECT 
				A.AccountPlanID, A.AccountName, 
				A.EnableBudgetResult 
			FROM
				accountplan A,
				voucher V
			WHERE 
				( A.Active = 1 AND A.AccountPlanType = 'balance' ) 
				
				OR 
				
				( 
					-- A.Active = 0 AND 
					A.AccountPlanType = 'balance' AND
					V.AccountPlanID = A.AccountPlanID AND 
					V.VoucherPeriod >= '$safe_from_period' AND
					V.VoucherPeriod < '$safe_to_period' 
				)
			GROUP BY
				A.AccountPlanID 
			ORDER BY 
				A.AccountPlanID ASC";

      $query_result  = "SELECT 
				A.AccountPlanID, A.AccountName, A.EnableBudgetResult 
			FROM
				accountplan A,
				voucher V 
			WHERE 
				( A.Active = 1 AND A.AccountPlanType = 'result' )
		
				OR
			
				(
					-- A.Active = 0 AND 
					A.AccountPlanType = 'result' AND
					V.AccountPlanID = A.AccountPlanID AND
					V.VoucherPeriod >= '$safe_from_period' AND
					V.VoucherPeriod < '$safe_to_period'
				) 
			GROUP BY 
				A.AccountPlanID 
			ORDER BY 
				A.AccountPlanID asc";
  }
  elseif($_active == 0)
  {
      $query_balance = "select A.AccountPlanID, A.AccountName from accountplan A where A.AccountPlanType = 'balance' group by A.AccountPlanID order by A.AccountPlanID asc";
      $query_result  = "select A.AccountPlanID, A.AccountName from accountplan A where A.AccountPlanType = 'result' group by A.AccountPlanID order by A.AccountPlanID asc";
  }

  $balance_accounts = $_lib['db']->db_query($query_balance);
  $result_accounts = $_lib['db']->db_query($query_result);
}
elseif($_type == 'reskontro')
{
  if($_active == 1)
  {
      $query_balance = "select A.AccountPlanID, A.AccountName, A.EnableBudgetResult from accountplan A where A.AccountPlanType='" . $selectedaccount->ReskontroAccountPlanType . "' ";
  }
  elseif($_active == 0)
  {
      $query_balance = "select A.AccountPlanID, A.AccountName, A.EnableBudgetResult from accountplan A where A.AccountPlanType='" . $selectedaccount->ReskontroAccountPlanType . "' ";
  }
  
  	if($_reskontroFrom)
  		$query_balance .= "and A.AccountPlanID >= '$_reskontroFrom' ";
  
  	if($_reskontroTo)
  		$query_balance .= "and A.AccountplanID <= '$_reskontroTo' ";

  $query_balance .= "group by A.AccountPlanID order by A.AccountPlanID asc";

  $balance_accounts = $_lib['db']->db_query($query_balance);
}
#print "$query_balance<br>";
#print "$query_result<br>";
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

<?
if ($_type == "reskontro")
    print "<h2> Hovedbokskonto ".$_REQUEST['report_selectedAccount']." type " .  $selectedaccount->ReskontroAccountPlanType;
    if($_reskontroFrom)
    	print " fra " . $_reskontroFrom;
    if($_reskontroTo)
   		print " til " . $_reskontroTo;
   	print "</h2>\n";
?>

<h2><? print $_type ?>saldoliste: Fra periode <? print $_REQUEST['report_FromPeriod'] ?> til periode <? print $_REQUEST['report_ToPeriod'] ?>. Resultat beregnet fra <? print $_REQUEST['report_ResultFromPeriod'] ?></h2>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>">
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>">
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>">

<table  class="lodo_data">
  <tr class="voucher">
    <th colspan="9">Balanse</th>
  </tr>
  <tr>
    <th class="sub" colspan="2"></th>
    <th class="sub" colspan="3"><? print $_from_period ?> - <? print $_to_period ?></th>
    <? if($EnableLastYear) { ?>
    <th class="sub" colspan="3"><? print $_from_prev_period ?> - <? print $_to_prev_period ?></th>
    <? } ?>
    <? if($EnableBudget) { ?>
    <th class="sub"></th>
    <? } ?>
    <th class="sub"></th>
  </tr>
  <tr class="voucher">
    <th class="sub">Konto</th>
    <th class="sub">Navn</th>
    <th class="sub">Gammel saldo</th>
    <th class="sub">Perioden</th>
    <th class="sub">Ny saldo</th>
    <? if($EnableLastYear) { ?>
    <th class="sub">Gammel saldo</th>
    <th class="sub">Perioden</th>
    <th class="sub">Ny saldo</th>
    <? } ?>
    <? if($EnableBudget) { ?>
    <th class="sub">Budsjett</th>
    <? } ?>
    <th class="sub"></th>
  </tr>
<?
$sumTotal       = 0;
$sumTotal_old   = 0;
$sumTotal_new   = 0;
$sumTotal       = 0;

################################################################################
# looper over alle balanse konto
#
while($account = $_lib['db']->db_fetch_object($balance_accounts))
{
    ################################################################################
    $saldo_old      = 0;
    $query_saldo_old = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $_departmentID $_projectID $VoucherType V.VoucherPeriod < '$_from_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
	#print "$query_saldo_old<br>";
    $voucher_saldo_old = $_lib['storage']->get_row(array('query' => $query_saldo_old));
    $saldo_old      = (round($voucher_saldo_old->sumin, 2) - round($voucher_saldo_old->sumout, 2));

    $saldo_new      = 0;
    $query_saldo    = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_from_period' and V.VoucherPeriod <= '$_to_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
	#print "$query_saldo<br>";
    $voucher_saldo  = $_lib['storage']->get_row(array('query' => $query_saldo));
    $saldo_new      = (round($voucher_saldo->sumin, 2) - round($voucher_saldo->sumout, 2));

    $sumrow_new     = 0;
    $sumrow_new     = $saldo_old + $saldo_new;
    $sumTotal_old   += $saldo_old;
    $sumTotal_new   += $saldo_new;
    $sumTotal       += $sumrow_new;

    ################################################################################
    $saldo_prev_old             = 0;
    $query_prev_saldo_old       = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $_departmentID $_projectID $VoucherType V.VoucherPeriod < '$_from_prev_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    #print "$query_prev_saldo_old<br>";
    $voucher_prev_saldo_old     = $_lib['storage']->get_row(array('query' => $query_prev_saldo_old));
    $saldo_prev_old             = (round($voucher_prev_saldo_old->sumin, 2) - round($voucher_prev_saldo_old->sumout, 2));

    $saldo_prev_new         = 0;
    $query_prev_saldo_new   = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_from_prev_period' and V.VoucherPeriod <= '$_to_prev_period' and V.AccountPlanID='$account->AccountPlanID' and V.Active=1 group by V.AccountPlanID";
    #print "$query_prev_saldo_new<br>";
    $voucher_prev_saldo_new = $_lib['storage']->get_row(array('query' => $query_prev_saldo_new));
    $saldo_prev_new         = (round($voucher_prev_saldo_new->sumin, 2) - round($voucher_prev_saldo_new->sumout, 2));

    $sumrow_prev_new    = 0;
    $sumrow_prev_new    = $saldo_prev_old + $saldo_prev_new;
    $sumTotal_prev_old  += $saldo_prev_old;
    $sumTotal_prev_new  += $saldo_prev_new;
    $sumTotal_prev      += $sumrow_prev_new;

    ################################################################################
    #print_r($account);
    if($account->EnableBudgetResult > 0) {
      $budget      = get_budget($account->AccountPlanID, $_from_period, $_to_period);
      $budgetTotal+= $budget;
    } else {
      $budget = "";
    }
    
    $accountsumH[substr($account->AccountPlanID, 0,1)]->Amount += $sumrow_new;
    $accountsumH[substr($account->AccountPlanID, 0,1)]->Budget += $budget;
    
    $urltmp = $url . "&amp;report_FromAccount=$account->AccountPlanID&amp;report_ToAccount=$account->AccountPlanID";
    ?>
    <tr class="voucher">
        <td><? print $account->AccountPlanID ?></td>
        <td><? print $account->AccountName ?></td>
        <td class="number"><? print $_lib['format']->Amount($saldo_old) ?></td>
        <td class="number"><? print $_lib['format']->Amount($saldo_new) ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumrow_new) ?></td>
        <? if($EnableLastYear) { ?>
        <td class="number"><? print $_lib['format']->Amount($saldo_prev_old) ?></td>
        <td class="number"><? print $_lib['format']->Amount($saldo_prev_new) ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumrow_prev_new) ?></td>
        <? } ?>
        <? if($EnableBudget) { ?>
        <td class="number"><? print $_lib['format']->Amount($budget) ?></td>
        <? } ?>
        <td class="noprint"><? print $_lib['form3']->URL(array('description' => 'Detaljer', 'url' => $urltmp)) ?></td>
    </tr>
    <?
}
$endSum = $sumTotal;

$sumTotal_old = $_lib['format']->Amount(round($sumTotal_old, 2));
$sumTotal_new = $_lib['format']->Amount(round($sumTotal_new, 2));
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

if($sumTotal_prev != 0)
{
    $printsum_prev = "<font color=\"red\">$sumTotal_prev</font>";
    $printtext_prev = "<font color=\"red\">Sum</font>";
}
else
{
    $printsum_prev = $sumTotal_prev;
    $printtext_prev = "Sum";
}

?>
  <tr class="voucher">
      <td colspan="2"><? print $printtext ?>  (Dette er sum balanse som skal g&aring; i null)</td>
      <td class="number"><? print $sumTotal_old ?></td>
      <td class="number"><? print $sumTotal_new ?></td>
      <td class="number"><? print $printsum ?></td>
      <? if($EnableLastYear) { ?>
      <td class="number"><? print $_lib['format']->Amount($sumTotal_prev_old) ?></td>
      <td class="number"><? print $_lib['format']->Amount($sumTotal_prev_new) ?></td>
      <td class="number"><? print $_lib['format']->Amount($printsum_prev) ?></td>
      <? } ?>
      <? if($EnableBudget) { ?>
      <td class="number"><? print $_lib['format']->Amount($budgetTotal) ?></td>
      <? } ?>
      <td><? print $_lib['form3']->URL(array('description' => 'Detaljer', 'url' => 'http://regnskap.empatix.no/lodo.php?SID=ff3e9avftqiigr55qu1hd6c2m4&view_mvalines=1&view_linedetails=1&t=report.verify_consistency&report_Type=balancenotok&report_Sort=VoucherID')) ?></td>
  </tr>


    <? // Legger til ekstra summeringslinje i reskontrosaldoliste rapport
    if ($_type == "reskontro")
    {
        if($selectedaccount->AccountPlanType == 'balance')
        {
            $query = "select sum(V.AmountIn) as AIn, sum(V.AmountOut) as AOut from voucher as V where V.Active=1 and V.VoucherPeriod <= '".$_to_period."' and V.AccountPlanID=".$selectedaccount->AccountPlanID;
        }
        if($selectedaccount->AccountPlanType == 'result')
        {
            $query = "select sum(V.AmountIn) as AIn, sum(V.AmountOut) as AOut from voucher as V where V.Active=1 and  V.VoucherPeriod >='".$_from_period."' and V.VoucherPeriod <= '".$_to_period."' and V.AccountPlanID=".$selectedaccount->AccountPlanID;
        }

        $accountSumRow = $_lib['storage']->get_row(array('query' => $query));
        //print "<br><br>".$query;

        $accountSum = ($accountSumRow->AIn - $accountSumRow->AOut);
        $endSum     = ($accountSumRow->AIn - $accountSumRow->AOut)-$endSum;
        $accountSum = $_lib['format']->Amount(round($accountSum, 2));
        $endSum     = $_lib['format']->Amount(round($endSum, 2));
        ?>
        <tr class="voucher">
            <td><? print $selectedaccount->AccountPlanID ?></td>
            <td><? print $accountName ?></td>
            <td></td>
            <td></td>
            <td class="number"><? print $accountSum ?></td>
        </tr>
        <tr class="voucher">
            <td><? print "Differanse" ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="number"><? print $endSum ?></td>
        </tr>
        <?
    }
    // END Legger til ekstra summeringslinje i reskontrosaldoliste rapport
    ?>
<? if($_type == 'hovedbok') { ?>
  <tr class="voucher">
      <th colspan="9">Resultat
  </tr>
  <tr>
    <th class="sub" colspan="2"></th>
    <th class="sub" colspan="3"><? print $_from_period ?> - <? print $_to_period ?></th>
    <? if($EnableLastYear) { ?>
    <th class="sub" colspan="3"><? print $_from_prev_period ?> - <? print $_to_prev_period ?></th>
    <? } ?>
    <? if($EnableBudget) { ?>
    <th class="sub"></th>
    <? } ?>
    <th class="sub"></th>
  </tr>
  <tr class="voucher">
      <th class="sub">Konto</th>
      <th class="sub">Navn</th>
      <th class="sub">Gammel saldo</th>
      <th class="sub">Perioden</th>
      <th class="sub">Ny saldo</th>
      <? if($EnableLastYear) { ?>
      <th class="sub">Gammel saldo</th>
      <th class="sub">Perioden</th>
      <th class="sub">Ny saldo</th>
      <? } ?>
      <? if($EnableBudget) { ?>
      <th class="sub">Budsjett</th>
      <? } ?>
      <th class="sub"></th>
  </tr>

  <?
  $budgetTotal  = 0;

  $sumTotal     = 0;
  $sumTotal_old = 0;
  $sumTotal_new = 0;
  $sumTotal     = 0;

  $sumTotal_prev        = 0;
  $sumTotal_old_prev    = 0;
  $sumTotal_new_prev    = 0;
  $sumTotal_prev        = 0;

###################################################
# looper over alle resultat kontoer
#
  while($account = $_lib['db']->db_fetch_object($result_accounts))
  {
      $query_saldo_old      = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_result_from' and V.VoucherPeriod < '$_from_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
      #print "$query_saldo_old<br>\n";
      $voucher_saldo_old    = $_lib['storage']->get_row(array('query' => $query_saldo_old));
      $saldo_old            = (round($voucher_saldo_old->sumin, 2) - round($voucher_saldo_old->sumout, 2));

      $query_saldo          = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_from_period' and V.VoucherPeriod <= '$_to_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
      #print "$query_saldo<br>\n";
      $voucher_saldo        = $_lib['storage']->get_row(array('query' => $query_saldo));
      $saldo_new            = (round($voucher_saldo->sumin, 2) - round($voucher_saldo->sumout, 2));

      $query_prev_saldo_old = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_result_prev_from' and V.VoucherPeriod < '$_from_prev_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
      #print "$query_prev_saldo_old<br>\n";
      $voucher_prev_saldo_old = $_lib['storage']->get_row(array('query' => $query_prev_saldo_old));
      $saldo_prev_old       = (round($voucher_prev_saldo_old->sumin, 2) - round($voucher_prev_saldo_old->sumout, 2));

      $query_prev_saldo     = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V where  V.Active=1 and $_departmentID $_projectID $VoucherType V.VoucherPeriod >= '$_from_prev_period' and V.VoucherPeriod <= '$_to_prev_period' and V.AccountPlanID='$account->AccountPlanID' group by V.AccountPlanID";
      #print "$query_prev_saldo<br>\n";
      $voucher_prev_saldo   = $_lib['storage']->get_row(array('query' => $query_prev_saldo));
      $saldo_prev_new       = (round($voucher_prev_saldo->sumin, 2) - round($voucher_prev_saldo->sumout, 2));

      $sumrow_new           = $saldo_old + $saldo_new;
      $sumTotal_old         += $saldo_old;
      $sumTotal_new         += $saldo_new;
      $sumTotal             += $sumrow_new;

      $sumrow_prev_new      = $saldo_prev_old + $saldo_prev_new;
      $sumTotal_prev_old    += $saldo_prev_old;
      $sumTotal_prev_new    += $saldo_prev_new;
      $sumTotal_prev        += $sumrow_prev_new;

      if($account->EnableBudgetResult > 0) {
        $budget         = get_budget($account->AccountPlanID, $_from_period, $_to_period);
        $budgetTotal    += $budget;
      } else {
        $budget = "";
      }
      
      $accountsumH[substr($account->AccountPlanID, 0,1)]->Amount += $sumrow_new;
      $accountsumH[substr($account->AccountPlanID, 0,1)]->Budget += $budget;

      //print "$saldo_old + $saldo_new = $sumTotal<br>\n";
      $urltmp = $url . "&amp;report_FromAccount=$account->AccountPlanID&amp;report_ToAccount=$account->AccountPlanID";
      ?>
      <tr class="voucher">
          <td><? print $account->AccountPlanID ?></td>
          <td><? print $account->AccountName ?></td>
          <td class="number"><? print $_lib['format']->Amount($saldo_old) ?></td>
          <td class="number"><? print $_lib['format']->Amount($saldo_new) ?></td>
          <td class="number"><? print $_lib['format']->Amount($sumrow_new) ?></td>
          <? if($EnableLastYear) { ?>
          <td class="number"><? print $_lib['format']->Amount($saldo_prev_old) ?></td>
          <td class="number"><? print $_lib['format']->Amount($saldo_prev_new) ?></td>
          <td class="number"><? print $_lib['format']->Amount($sumrow_prev_new) ?></td>
          <? } ?>
          <? if($EnableBudget) { ?>
          <td class="number"><? print $_lib['format']->Amount($budget) ?></td>
          <? } ?>
          <td class="noprint"><? print $_lib['form3']->URL(array('description' => 'Detaljer', 'url' => $urltmp)) ?></td>
      </tr>
      <?
  }

  if($sumTotal != 0)
  {
      $printsum = "<font color=\"red\">" . $_lib['format']->Amount($sumTotal) . "</font>";
      $printtext = "<font color=\"red\">Sum</font>";
  }
  else
  {
      $printsum = $_lib['format']->Amount($sumTotal);
      $printtext = "Sum";
  }

  if($sumTotal_prev != 0)
  {
      $printsum_prev  = "<font color=\"red\">" . $_lib['format']->Amount($sumTotal_prev) . "</font>";
      $printtext_prev = "<font color=\"red\">Sum</font>";
  }
  else
  {
      $printsum_prev  = $_lib['format']->Amount($sumTotal_prev);
      $printtext_prev = "Sum";
  }
  ?>
    <tr class="voucher">
        <td colspan="2"><? print $printtext ?> (Dette er sum resultat som skal g&aring; i null)</td>
        <td class="number"><? print $_lib['format']->Amount(round($sumTotal_old, 2)); ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumTotal_new) ?></td>
        <td class="number"><? print $printsum ?></td>
        <? if($EnableLastYear) { ?>
        <td class="number"><? print $_lib['format']->Amount($sumTotal_prev_old) ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumTotal_prev_new) ?></td>
        <td class="number"><? print $printsum_prev ?></td>
        <? } ?>
        <? if($EnableBudget) { ?>
        <td class="number"><? print $_lib['format']->Amount($budgetTotal) ?></td>
        <? } ?>
        <td><? print $_lib['form3']->URL(array('description' => 'Detaljer', 'url' => 'http://regnskap.empatix.no/lodo.php?SID=ff3e9avftqiigr55qu1hd6c2m4&view_mvalines=1&view_linedetails=1&t=report.verify_consistency&report_Type=balancenotok&report_Sort=VoucherID')) ?></td>
    </tr>
<? } ?>

</table>
<hr>
<table class="lodo_data">
<tr>
    <th>Serie</th>
    <th>Regnskap</th>
    <th>Budsjett</th>
    <th>Diff</th>
</tr>

<? foreach($accountsumH as $key => $sumO) { 
if($key == 8) continue;
if($key == 4 || $key == 5 || $key == 6 || $key == 7) {
    $sumexpences += $sumO->Amount;
    $sumbudget   += $sumO->Budget;
}
if($key == 1) { ?>
<tr>
    <th colspan="4" class="sub">Balanse</th>
</tr>
<? }
if($key == 3) { ?>
<tr>
    <th colspan="4" class="sub">Resultat</th>
</tr>
<? } ?>

<tr>
    <td><? print $key ?>000</td>
    <td class="number"><? print $_lib['format']->Amount($sumO->Amount) ?></td>
    <td class="number"><? print $_lib['format']->Amount($sumO->Budget) ?></td>
    <td class="number"><? print $_lib['format']->Amount($sumO->Amount - $sumO->Budget) ?></td>
</tr>
<? } ?>
<tr>
    <td colspan="4"><hr></td>
</tr>
<tr>
    <td>Inntekter</td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[3]->Amount) ?></td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[3]->Budget) ?></td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[3]->Amount - $accountsumH[3]->Budget) ?></td>
</tr>
<tr>
    <td>Utgifter</td>
    <td class="number"><? print $_lib['format']->Amount($sumexpences) ?></td>
    <td class="number"><? print $_lib['format']->Amount($sumbudget) ?></td>
    <td class="number"><? print $_lib['format']->Amount($sumexpences - $sumbudget) ?></td>
</tr>
<tr>
    <th class="sub">Diff</th>
    <? 
    $diffexpences = $sumexpences + $accountsumH[3]->Amount; 
    $diffbudget   = $sumbudget   + $accountsumH[3]->Budget;
    ?>
    <th class="number sub"><? print $_lib['format']->Amount($diffexpences) ?></th>
    <th class="number sub"><? print $_lib['format']->Amount($diffbudget) ?></th>
    <th class="number sub"><? print $_lib['format']->Amount($diffexpences - $diffbudget) ?></th>
</tr>
<tr>
    <td>8000</td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[8]->Amount) ?></td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[8]->Budget) ?></td>
    <td class="number"><? print $_lib['format']->Amount($accountsumH[8]->Amount - $accountsumH[8]->Budget) ?></td>
</tr>
<tr>
    <td>Resultat</td>
    <?
    $resultexpences = $sumexpences + $accountsumH[3]->Amount + $accountsumH[8]->Amount;
    $resultbudget   = $sumbudget   + $accountsumH[3]->Budget + $accountsumH[8]->Budget;
    ?>
    <td class="number"><? print $_lib['format']->Amount($resultexpences) ?></td>
    <td class="number"><? print $_lib['format']->Amount($resultbudget) ?></td>
    <td class="number"><? print $_lib['format']->Amount($resultexpences - $resultbudget) ?></td>
</tr>
</table>
</body>
</html>
