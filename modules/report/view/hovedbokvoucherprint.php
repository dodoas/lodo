<?
// lagt til 05/01-2005 for � hente hovedbokskontonavn
#ResultFromPeriod
includelogic('accounting/accounting');
$accounting = new accounting();
$kidmatchH  = array();

if($_REQUEST['action_postmotpost_open']) {
    includelogic('postmotpost/postmotpost');
    $postmotpost = new postmotpost(array());
    $postmotpost->openPost((int) $_REQUEST['VoucherID']);
}

if($_REQUEST['report_selectedAccount'])
    $selectedAccount = $accounting->get_accountplan_object($_REQUEST['report_selectedAccount']);

$_toperiod 		= $_REQUEST['report_ToPeriod'];
$_firstperiod 	= $_REQUEST['report_FromPeriod'];

$_fromaccount 	= $_REQUEST['report_FromAccount'];
$_toaccount 	= $_REQUEST['report_ToAccount'];

if($selectedAccount->AccountPlanType == 'balance')
{
    $_fromperiod 	= '';
    $_toperiod 		= $_REQUEST['report_ToPeriod'];
    $_firstperiod 	= $_REQUEST['report_FromPeriod'];
}
elseif($selectedAccount->AccountPlanType == 'result')
{
    $_fromperiod 	= $_REQUEST['report_FromPeriod'];
    $_toperiod 		= $_REQUEST['report_ToPeriod'];
    $_firstperiod 	= $_REQUEST['report_FromPeriod'];
}

$select = "select v.* from voucher as v, accountplan as a where v.Active=1 and v.AccountPlanID=a.AccountPlanID and";

$whereouter .= " v.VoucherPeriod >= '" . $_REQUEST['report_FromPeriod'] . "' and v.VoucherPeriod <= '" . $_REQUEST['report_ToPeriod'] . "' and ";
if( ($_fromaccount) && ($_toaccount) )
{
    $whereouter .= " a.AccountPlanID >= ".$_fromaccount." and a.AccountplanID <= ".$_toaccount." and ";
}
elseif($_fromaccount)
{
    $whereouter .= " a.AccountPlanID >= ".$_fromaccount." and ";
}
elseif($_toaccount)
{
    $whereouter .= " a.AccountplanID <= ".$_toaccount." and ";
}

$whereouter .= " (a.AccountPlanType='balance' or a.AccountPlanType='result') and ";

if(strlen($_REQUEST['report_ProjectID']) > 0)
{
    $whereouter .= " v.ProjectID = " . $_REQUEST['report_ProjectID'] . " and ";
}

if(strlen($_REQUEST['report_DepartmentID']) > 0)
{
    $whereouter .= " v.DepartmentID = " . $_REQUEST['report_DepartmentID'] . " and ";
}

if($_REQUEST['report_VoucherType'])
{
    $whereouter .= " v.VoucherType = '" . $_REQUEST['report_VoucherType'] . "' and ";
}

foreach($_REQUEST as $key => $value) {
    if($key != 'VoucherID' && $key != 'redirected' && strlen($value) > 0) {
        $_MY_SELF .= $key . '=' . $value . '&amp;';
    }
}

$whereouter = substr($whereouter, 0, strlen($whereouter) - 4);

##############################################################
#Calculate account balance
$query_voucher  = $select . $whereouter . " order by AccountPlanID asc, VoucherPeriod asc, VoucherDate asc, JournalID asc";
#print "$query_voucher<br>";
$_lib['sess']->debug("$query_voucher<br>");
$result_voucher     = $_lib['db']->db_query($query_voucher);
$result_voucher2    = $_lib['db']->db_query($query_voucher);
setdiffKID($result_voucher2);

$numrows        = $_lib['db']->db_numrows($result_voucher);

function setdiffKID($result_voucher) {
    global $_lib, $kidmatchH;
    
    while($voucher = $_lib['db']->db_fetch_object($result_voucher)) {
        $kidmatchH[$voucher->AccountPlanID][$voucher->KID] += $voucher->AmountIn - $voucher->AmountOut;
    }
}

function getdiffKID($AccountPlanID, $KID, $VoucherID) {
    global $_lib, $kidmatchH, $accounting;

    #if($kidmatchH[$AccountPlanID][$KID] != 0) {
        #print "Åpner postene: $KID<br>\n";
        #We open all VoucherIDs with a diff, since it is an error if they are accidentally closed (it happens). This depends on the period specified - so we can not open it automatically anymore.
        #$accounting->postmotpost->openPost($VoucherID);
    #}

    return $kidmatchH[$AccountPlanID][$KID];
}

#######################################
function get_saldo($account, $fromperiod, $toperiod)
{
  global $_lib;
  if($fromperiod) {
    $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout, sum(Quantity) as quantity from voucher where AccountPlanID=$account and VoucherPeriod>='$fromperiod' and VoucherPeriod<'$toperiod' and Active=1";
  } else {
    $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout, sum(Quantity) as quantity from voucher where AccountPlanID=$account and VoucherPeriod<'$toperiod' and Active=1";
  }
  $row = $_lib['storage']->get_row(array('query' => $query_voucher));
  $sum = $row->sumin - $row->sumout;
  #print "<h2>get_saldo: $query_voucher: " . $sum . " account: $account, fromperiod: $fromperiod, toperiod: $toperiod</h2>";

  return array($sum, $row->quantity);
}
#######################################

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Hovedbokbilagsutskrift <? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</title>
    <meta name="cvs"                content="$Id: hovedbok.php,v 1.47 2005/10/20 12:58:59 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

<h2>Bilagsutskrift: Hovedbok Fra <? print $_REQUEST['report_FromPeriod'] ?> Til <? print $_REQUEST['report_ToPeriod']; ?>
<! tatt vekk fra enden av linjen over:, Sider <? print $numrows/50 ?>, Linjer <? print $numrows ?></h2>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>

<table class="lodo_data">
  <tr class="voucher">
    <th width="50">Dato</th>
    <th>Bilag</th>
    <th>Prosj</th>
    <th>Avd</th>
    <th>Mengde</th>
    <th width="50">Debet</th>
    <th width="50">Kredit</th>
    <th>MVA</th>
    <th>Kode</th>
    <th width="50">Saldo</th>
    <th>Tekst</th>
    <th>KID</th>
    <th>Faktura</th>
    <th class="noprint">Diff</th>
    <th class="noprint"></th>
  </tr>
    <?
    $i           = 0;
    $sumAccountH = array();
    while($voucher = $_lib['db']->db_fetch_object($result_voucher))
    {
        if($account != $voucher->AccountPlanID || $account == 0)
        {
            ?>
			<tr>
				<td colspan="4">Periode sum</td>
				<td class="number"><? if($quantitysum > 0) print $_lib['format']->Amount($quantitysum); ?></td>
				<td class="number"></td>
				<td class="number"></td>
				<td colspan="2"></td>
				<td class="number"><? print $_lib['format']->Amount($sumAccountH[$account]) ?></td>
				<td colspan="3"></td>
				<td class="noprint" colspan="2"></td>
			</tr>
            <?
        
            $period = 0;
            $account = $voucher->AccountPlanID;
            
            $accountWork = $accounting->get_accountplan_object($account); #Hovedbokskontoer

            #print "AccountPlanType: " . $accountWork->AccountPlanType . "<br>";

            $saldo              = 0;
            $quantitysum        = 0;

            if($accountWork->AccountPlanType == 'result') {
                #Resultat
                list($saldo, $quantity) = get_saldo($account ,$_REQUEST['report_ResultFromPeriod'] , $_REQUEST['report_FromPeriod']);
                $sumAccountH[$account]  = $saldo;
                #print "Resultat for kto: $account, saldo: $saldo<br>";
            }
            elseif($accountWork->AccountPlanType == 'balance') {
              #Balanse
              list($saldo, $quantity) = get_saldo($account ,'' , $_REQUEST['report_FromPeriod']);
              $sumAccountH[$account]  = $saldo;
              #print "Balanse for kto: $account, saldo: $saldo<br>";
            } 
            elseif($accountWork->AccountPlanType == 'employee') {
                $saldo  	= 0; 

            } else {
                print "Denne situasjonen har vi ikke kodet for kto mangler type: " . $accountWork->AccountPlanID;
            }
            #This is the last line in each group.
            ?>

            <tr>
                <th colspan = "9"><? print "$account - ".$_lib['format']->AccountPlanIDToName($account) ?></th>
                <th></th>
                <th colspan="5"></th>
            </tr>
            <?
            $sql_accountplan    = "select * from accountplan where AccountPlanID=$account";
            $accountplan        = $_lib['storage']->get_row(array('query' => $sql_accountplan));
        }

        if( ($period != $voucher->VoucherPeriod) || (!$period) )
        {
            $period = $voucher->VoucherPeriod;
            #Grey - top line
            ?>
                <tr>
                    <th class="sub" colspan="4"><? print "Periode: $period" ?></th>
                    <th class="sub number"><? if($quantity > 0) print $quantity; ?></th>
                    <th class="sub number"><? print $accountplan->debittext ?></th>
                    <th class="sub number"><? print $accountplan->credittext ?></th>
                    <th class="sub" colspan="2"></th>
                    <th class="sub number"><? print $_lib['format']->Amount($saldo) ?></th>
                    <th class="sub" colspan="3"></th>
                    <th class="sub noprint" colspan="2"></th>
                </tr>
            <?
        }

        $sumAccountH[$account]  += ($voucher->AmountIn - $voucher->AmountOut);
        $saldo                  += ($voucher->AmountIn - $voucher->AmountOut);
        #print "$quantitysum<br>\n";
        ?>
            <tr class="voucher">
                <td><nobr><? print $voucher->VoucherDate    ?></nobr></td>
                <td class="number"><? print $voucher->VoucherType    ?><a href="<? print $_SETUP[DISPATCH] . "t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID" ?>&amp;action_journalid_search=1"><? print $voucher->JournalID ?></a></td>
                <td><? if($voucher->ProjectID > 0)    { print $voucher->ProjectID;  }  ?></td>
                <td><? if($voucher->DepartmentID > 0) { print $voucher->DepartmentID; } ?></td>
                <td><? if($voucher->Quantity > 0)     { print $voucher->Quantity; } ?></td>
                <td class="number"><nobr><? if($voucher->AmountIn > 0) { print $_lib['format']->Amount($voucher->AmountIn); } ?></nobr></td>
                <td class="number"><nobr><? if($voucher->AmountOut > 0) { print $_lib['format']->Amount($voucher->AmountOut); } ?></nobr></td>
                <td class="number"><? if($voucher->VatID > 0) { print "$voucher->Vat%"; } ?> </td>
                <td class="number"><? if($voucher->VatID > 0) { print $voucher->VatID; } ?></td>
                <td class="number"><nobr><? print $_lib['format']->Amount($saldo); ?></nobr></td>
                <td><? print substr($voucher->Description,0,20); if(strlen($voucher->Description) > 20) print "..."; ?></td>
                <td><? print $voucher->KID ?></td>
                <td><? print $voucher->InvoiceID ?></td>
                <td class="noprint"><nobr><? print $_lib['format']->Amount(getdiffKID($voucher->AccountPlanID, $voucher->KID, $voucher->VoucherID)) ?></nobr></td>
                <td class="noprint"><a href="<? print $_MY_SELF ?>&amp;VoucherID=<? print $voucher->VoucherID ?>&action_postmotpost_open=1" title="&Aring;pne post">&Aring;pne post</a></td>
            </tr>
        <?
        $i++;
    }
    $reptype = "Totalsum alle konto";
    #print_r($sumAccountH);
    $sumSaldoAll    = 0;
    foreach($sumAccountH as $account => $amount) {
        $sumSaldoAll += $amount;
    }
    ?>

    <tr>
        <th colspan="9"><? print "$reptype " ?></th>
        <th class="number"><nobr><? print $_lib['format']->Amount($sumSaldoAll) ?></nobr></th>
        <th colspan="5"></th>
    </tr>
    <?
    if($_REQUEST['report_Type'] == 'reskontro')
    {
        ?>
                
        <tr>
            <th colspan="9"><? print $selectedAccount->AccountPlanID . " " . $selectedAccount->AccountName ?></th>
            <th class="number"><nobr><? list($sumhoved, $quantity) = get_saldo($selectedAccount->AccountPlanID, $_fromperiod, $_lib['date']->get_next_period($_toperiod)); print $_lib['format']->Amount($sumhoved) ?></nobr></th>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="9"><? print "Differanse " ?></th>
            <th class="number"><nobr>
            <?
            $sumdiff = $sumhoved - $sumSaldoAll;
            print $_lib['format']->Amount($sumdiff);
            ?></nobr></th>
            <th colspan="5"></th>
        </tr>
        <?
    }
    ?>
</table>
</form>
</body>
</html>