<?
// lagt til 05/01-2005 for � hente hovedbokskontonavn
#ResultFromPeriod
includelogic('accounting/accounting');
includelogic('postmotpost/postmotpost');
$accounting = new accounting();
$kidmatchH  = array();

if($_REQUEST['action_postmotpost_open']) {
    includelogic('postmotpost/postmotpost');
    $postmotpost = new postmotpost(array());
    $postmotpost->openPost((int) $_REQUEST['VoucherID']);
}

if($_REQUEST['report_selectedAccount'])
    $selectedAccount = $accounting->get_accountplan_object($_REQUEST['report_selectedAccount']);

if ($selectedAccount) $_type = $selectedAccount->ReskontroAccountPlanType;
else $_type = 'none';

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

if($_REQUEST['report_FromAccount'])
    $_reskontroFrom = $_REQUEST['report_FromAccount'];
if($_REQUEST['report_ToAccount'])
    $_reskontroTo   = $_REQUEST['report_ToAccount'];

$select = "select v.* from voucher as v, accountplan as a where v.Active=1 and v.AccountPlanID=a.AccountPlanID and";

$whereouter .= " v.VoucherPeriod >= '" . $_REQUEST['report_FromPeriod'] . "' and v.VoucherPeriod <= '" . $_REQUEST['report_ToPeriod'] . "' and ";
$whereouter .= " (a.AccountPlanType != 'balance' and a.AccountPlanType != 'result') and ";
if($_reskontroFrom) {
    $whereouter .= " a.AccountPlanID >= '$_reskontroFrom' and ";
}
if($_reskontroTo) {
    $whereouter .= " a.AccountPlanID <= '$_reskontroTo' and ";
}
if(strlen($_REQUEST['report_ProjectID']) > 0)
{
    $whereouter .= " v.ProjectID = " . $_REQUEST['report_ProjectID'] . " and ";
}

if(strlen($_REQUEST['report_DepartmentID']) > 0)
{
    $whereouter .= " v.DepartmentID = " . $_REQUEST['report_DepartmentID'] . " and ";
}

if ($selectedAccount->AccountPlanID) $whereouter .= " a.AccountPlanType = '". $selectedAccount->ReskontroAccountPlanType ."' and ";

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
    $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout, count(Quantity) as quantity from voucher where AccountPlanID=$account and VoucherPeriod>='$fromperiod' and VoucherPeriod<'$toperiod' and Active=1";
  } else {
    $query_voucher  = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout, count(Quantity) as quantity from voucher where AccountPlanID=$account and VoucherPeriod<'$toperiod' and Active=1";
  }
  $row = $_lib['storage']->get_row(array('query' => $query_voucher));
  $sum = $row->sumin - $row->sumout;
  #print "<h2>$query_voucher: " . $sum . "</h2>";

  return array($sum, $row->quantity);
}
#######################################

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - journal</title>
    <meta name="cvs"                content="$Id: hovedbok.php,v 1.47 2005/10/20 12:58:59 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

<?
    $postmotpost = new postmotpost(array('AccountPlanID' => $selectedAccount->AccountPlanID));
    $postmotpost->getopenpost();
    $error_count = 0;
    print "<h2> Hovedbokskonto " . $selectedAccount->AccountPlanID . " type " .  $selectedaccount->ReskontroAccountPlanType;
    if($_reskontroFrom)
    	print " fra " . $_reskontroFrom;
    if($_reskontroTo)
   		print " til " . $_reskontroTo;
   	print "</h2>\n";
?>

<h2>Bilagsutskrift: <? print $_REQUEST['report_Type']?> Fra <? print $_REQUEST['report_FromPeriod'] ?> Til <? print $_REQUEST['report_ToPeriod']; ?>
<! tatt vekk fra enden av linjen over:, Sider <? print $numrows/50 ?>, Linjer <? print $numrows ?></h2>
<div id='error_links'>
</div>

<form class="voucher" name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden"  name="type"               value="<? print $type ?>"/>
<input type="hidden"  name="voucher.VoucherID"  value="<? print $voucher->VoucherID ?>"/>
<input type="hidden"  name="voucher.JournalID"  value="<? print $JournalID ?>"/>

<? print $_lib['message']->get() ?>

<table class="lodo_data">
  <tr class="voucher">
    <th width="50">Dato</th>
    <th>Bilag</th>
    <th>Prosj</th>
    <th>Avd</th>
    <th>Mengde</th>
    <th width="50" class="align-right">Debet</th>
    <th width="50" class="align-right">Kredit</th>
    <th width="50" class="align-right">Saldo</th>
    <th width="50" class="align-right">Valuta</th>
    <th width="50" class="align-right">Valuta</th>
    <th width="50" class="align-right">Saldo</th>
    <th>Tekst</th>
    <th>Fakturanr</th>
    <th>KID</th>
    <th>Matchet med</th>
    <th class="noprint" class="align-right">Diff</th>
    <th class="noprint"></th>
  </tr>
    <?
    $i           = 0;
    $sumAccountH = array();
    // set starting previous account to starting one
    $prev_acc_id = $_reskontroFrom;
    
    while($voucher = $_lib['db']->db_fetch_object($result_voucher))
    {
        if($account != $voucher->AccountPlanID || $account == 0)
        {
            #Dette er siste linje i loopen
            // only prints after first time(when $account var is set)
            if ($account) {
            ?>
			<tr>
				<td colspan="2">Periode sum</td>
				<td class="number"><? if($quantitysum > 0) print $_lib['format']->Amount($quantitysum); ?></td>
				<td class="number"></td>
				<td class="number"></td>
				<td colspan="2"></td>
				<td class="number"><? print $_lib['format']->Amount($sumAccountH[$account]) ?></td>
				<td colspan="6"></td>
				<td class="noprint"></td>
			</tr>
            <?
            }
            // first empty line below table headers
            else echo "<tr><td></td></tr>";

            // go trough account plans that are not in vouchers for the selected period
            // but still have some leftover amount from the time before starting peroid

            // get first and last account id if they are not selected
            if (!$_reskontroFrom && !$_reskontroTo) {
              $acc_start_end  = $_lib['db']->get_row(array("query" => "SELECT MIN(AccountPlanID) AS MinAccPlanID, MAX(AccountPlanID) AS MaxAccPlanID FROM accountplan WHERE AccountPlanType = '$_type'"));
              $_reskontroFrom = $acc_start_end->MinAccPlanID;
              $_reskontroTo   = $acc_start_end->MaxAccPlanID;
            }
            $acc_result   = $_lib['db']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "SELECT AccountPlanID from accountplan WHERE AccountPlanID>=". $_reskontroFrom ." AND AccountPlanID<=". $_reskontroTo));
            foreach($acc_result as $key => $value) {
              if ($key > $prev_acc_id && $key < $voucher->AccountPlanID) {
                list($sum, $quantity) = get_saldo($key, $_REQUEST['report_FromPeriod'], $_REQUEST['report_ToPeriod']);
                if ($quantity == 0) {
                  list($sum, $quantity) = get_saldo($key, false, $_REQUEST['report_FromPeriod']);
                  if ($sum != 0) {
                    $sql_accountplan = "select * from accountplan where AccountPlanID=$key";
                    $accountplan     = $_lib['storage']->get_row(array('query' => $sql_accountplan));
                    $sumAccountH[$key] = $sum;
                    // add to sum and print table with no vouchers since they 
                    // are not in the selected period
                    // print order of each tr: accountplan id and name, period and leftover amount, 
                    // sum(leftover amount at the end of selected period, same as at the beginning)
                ?>
                <tr>
                    <th colspan="9"><? print $accountplan->AccountPlanID . " " . $accountplan->AccountName ?></th>
                    <th class="number"></th>
                    <th colspan="2"></th>
                    <th colspan="6"></th>
                </tr>
                <tr>
                    <th class="sub" colspan="4"><? print "Periode: " . $_REQUEST['report_FromPeriod']; ?></th>
                    <th class="sub number"></th>
                    <th class="sub number"><? print $accountplan->debittext ?></th>
                    <th class="sub number"><? print $accountplan->credittext ?></th>
                    <th class="sub number"><? print $_lib['format']->Amount($sum); ?></th>
                    <th class="sub number"></th>
                    <th class="sub number"></th>
                    <th class="sub number"></th>
                    <th class="sub"></th>
                    <th class="sub" colspan="2"></th>
                    <th class="sub noprint" colspan="2"></th>
                </tr>
			          <tr>
				            <td colspan="2">Periode sum</td>
                    <td class="number"></td>
                    <td class="number"></td>
                    <td class="number"></td>
                    <td class="number"><? print $_lib['format']->Amount($sum); ?></td>
                    <td colspan="6"></td>
                    <td class="noprint" colspan="2"></td>
                </tr>
                <?
                  }
                }
              }
            }
            $period = 0;
            $account = $voucher->AccountPlanID;

            $quantitysum = 0;
            $saldo       = 0;

            $foreign_saldo      = 0;
            
            #if account is reskontro, get its hovedboks konto
            $accountWork = $accounting->getHovedbokToAccount($account);

            if($accountWork->AccountPlanType == 'result') {
              #Resultat
              list($saldo, $quantity) = get_saldo($account ,$_REQUEST['report_ResultFromPeriod'] , $_REQUEST['report_FromPeriod']);
              $sumAccountH[$account]  = $saldo;
              #print "Resultat for kto: $account, saldo: $sumAccountH[$account]<br>";
            }
            elseif($accountWork->AccountPlanType == 'balance') {
              #Balanse
              list($saldo, $quantity) = get_saldo($account ,'' , $_REQUEST['report_FromPeriod']);
              $sumAccountH[$account]  = $saldo;
              #print "Balanse for kto: $account, saldo: $sumAccountH[$account]<br>";
            } 
            elseif($accountWork->AccountPlanType == 'employee') {
                $saldo                      = 0;
                $sumAccountH[$account]  	= 0; 

            } else {
                print "Denne situasjonen har vi ikke kodet for kto mangler type: " . $accountWork->AccountPlanID;
            }
            // just set to 0 so it don't get printed, the number of vouchers is superfluous info
            $quantity = 0;
            ?>

            <tr>
                <th colspan = "12"><? print "$account - ".$_lib['format']->AccountPlanIDToName($account) ?></th>
                <th></th>
                <th colspan="4"></th>
            </tr>
            <?
            $sql_accountplan = "select * from accountplan where AccountPlanID=$account";
            $accountplan     = $_lib['storage']->get_row(array('query' => $sql_accountplan));
        } #end changed accountnumber

        if( ($period != $voucher->VoucherPeriod) || (!$period) )
        {
            $period = $voucher->VoucherPeriod;
            ?>
                <tr>
                    <th class="sub" colspan="4"><? print "Periode: $period" ?></th>
                    <th class="sub number"><? if($quantity > 0) print $quantity; ?></th>
                    <th class="sub number align-right"><? print $accountplan->debittext ?></th>
                    <th class="sub number align-right"><? print $accountplan->credittext ?></th>
                    <th class="sub number align-right"><? print $_lib['format']->Amount($sumAccountH[$account]) ?></th>
                    <th class="sub number align-right">Valuta</th>
                    <th class="sub number align-right">Valuta</th>
                    <th class="sub number align-right">Saldo</th>
                <? if ($voucher->ForeignCurrencyID && $voucher->ForeignAmount && $voucher->ForeignConvRate) { ?>
                    <th class="sub">Utenlandsk valuta</th>
                <? } else { ?>
                    <th class="sub"></th>
                <? } ?>
                    <th class="sub" colspan="4"></th>
                    <th class="sub noprint"></th>
                </tr>
            <?
        }

        #print "$quantitysum<br>\n";
        $sumAccountH[$account]  += ($voucher->AmountIn - $voucher->AmountOut);
        $saldo                  += ($voucher->AmountIn - $voucher->AmountOut);
        $_lib['message']->add("$i Saldo: $voucher->VoucherType$voucher->JournalID $voucher->AccountPlanID - $voucher->VoucherPeriod : $voucher->AmountIn - $voucher->AmountOut = $saldo");
        $quantitysum += $voucher->Quantity;
        
        #Foreign currency
        $foreign_amount_in  = 0;
        $foreign_amount_out = 0;
        if ($voucher->ForeignCurrencyID && $voucher->ForeignAmount && $voucher->ForeignConvRate) {
            $foreign_currency = "(".$voucher->ForeignCurrencyID ." ". $_lib['format']->Amount($voucher->ForeignAmount). " / ". $voucher->ForeignConvRate .") ";
            $foreign_currency_id = $voucher->ForeignCurrencyID;
            if ($voucher->AmountIn > 0) {
                $foreign_amount_in  = $voucher->ForeignAmount;
            }
            if ($voucher->AmountOut > 0) {
                $foreign_amount_out = $voucher->ForeignAmount;
            }
            $foreign_saldo += ($foreign_amount_in - $foreign_amount_out);
        } else {
            $foreign_currency = '';
        }

        ?>
            <tr class="voucher">
                <td><nobr><? print $i    ?> <? print $voucher->VoucherDate    ?></nobr></td>
                <td class="number"><? print $voucher->VoucherType    ?><a href="<? print $_SETUP[DISPATCH] . "t=journal.edit&amp;voucher_VoucherType=$voucher->VoucherType&amp;voucher_JournalID=$voucher->JournalID" ?>&amp;action_journalid_search=1"><? print $voucher->JournalID ?></a></td>
                <td><? if($voucher->ProjectID > 0)    { print $voucher->ProjectID;  }  ?></td>
                <td><? if($voucher->DepartmentID > 0) { print $voucher->DepartmentID; } ?></td>
                <td><? if($voucher->Quantity > 0)     { print $voucher->Quantity; } ?></td>
                <td class="number"><nobr><? if($voucher->AmountIn > 0) { print $_lib['format']->Amount($voucher->AmountIn); } ?></nobr></td>
                <td class="number"><nobr><? if($voucher->AmountOut > 0) { print $_lib['format']->Amount(-$voucher->AmountOut); } ?></nobr></td>
                <td class="number"><nobr><? print $_lib['format']->Amount($saldo); ?></nobr></td>
                <td class="number"><? ($voucher->AmountIn > 0) ? print $voucher->ForeignCurrencyID ." ". $_lib['format']->Amount($foreign_amount_in) : print '' ?></td>
                <td class="number"><? ($voucher->AmountOut > 0) ? print $voucher->ForeignCurrencyID ." ". $_lib['format']->Amount(-$foreign_amount_out) : print '' ?></td>
                <td class="number"><? print $_lib['format']->Amount($foreign_saldo) ?></td>
                <td><? print $foreign_currency; print substr($voucher->Description,0,20); if(strlen($voucher->Description) > 20) print "..."; ?></td>
                <td><? print $voucher->InvoiceID ?></td>
                <td><? print $voucher->KID ?></td>
                <td><?
                    $is_closed = !is_null($_lib['db']->get_row(array("query" => "SELECT * FROM voucherstruct WHERE Closed = 1 AND (ParentVoucherID = " . $voucher->VoucherID . " OR ChildVoucherID = " . $voucher->VoucherID . ")"))->VoucherStructID);
                    $match_number = $_lib['db']->get_row(array("query" => "SELECT MatchNumber FROM vouchermatch WHERE VoucherID = $voucher->VoucherID"))->MatchNumber;
                    if ($is_closed) {
                      if ($voucher->matched_by == 'invoice') print 'FAK ' . $voucher->InvoiceID . " ";
                      elseif ($voucher->matched_by == 'kid') print 'KID ' . $voucher->KID . " ";
                      elseif ($voucher->matched_by == 'match') print 'MAT ' . $match_number . " ";
                      else {
                        print "<span id='error".$error_count."' style='color: red;'>mulig feil</span>";
                        $error_count++;
                      }
                    }
                    else {
                      print "-";
                    }
                    ?></td>
                <td class="noprint align-right"><nobr><?
                  $amount_to_show = $postmotpost->getDiff($voucher->AccountPlanID, $voucher->KID, $voucher->InvoiceID, $match_number, $voucher);
                  print $_lib['format']->Amount($amount_to_show)
                  ?></nobr></td>
                <td class="noprint"><? if ($is_closed) { ?><a href="<? print $_MY_SELF ?>&amp;VoucherID=<? print $voucher->VoucherID ?>&action_postmotpost_open=1" title="&Aring;pne post">&Aring;pne post</a><? } ?></td>
            </tr>
        <?
        $i++;
        // set previous accountplan id so we know where to start when searching
        // for the ones without vouchers in the selected period
        $prev_acc_id = $voucher->AccountPlanID;
    }
    $reptype = "Totalsum alle reskontro til hovedbok";
    #print_r($sumAccountH);
    $sumSaldoAll    = 0;
    foreach($sumAccountH as $account => $amount) {
        $sumSaldoAll += $amount;
    }
    ?>
    <tr>
        <th colspan="7"><? print "$reptype " ?></th>
        <th class="number"><nobr><? print $_lib['format']->Amount($sumSaldoAll) ?></nobr></th>
        <th colspan="2"></th>
        <th class="number"><? print $foreign_currency_id ." ". $_lib['format']->Amount($foreign_saldo) ?></th>
        <th colspan="6"></th>
    </tr>
        <tr>
            <th colspan="7"><? print $selectedAccount->AccountPlanID . " " . $selectedAccount->AccountName ?></th>
            <th class="number"><nobr><? list($sumhoved, $quantity) = get_saldo($selectedAccount->AccountPlanID, $_fromperiod, $_lib['date']->get_next_period($_toperiod)); print $_lib['format']->Amount($sumhoved) ?></nobr></th>
            <th colspan="2"></th>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th colspan="7"><? print "Differanse " ?></th>
            <th class="number"><nobr>
            <?
            $sumdiff = $sumhoved - $sumSaldoAll;
            print $_lib['format']->Amount($sumdiff);
            ?></nobr></th>
            <th colspan="2"></th>
            <th colspan="6"></th>
        </tr>
</table>
</form>

  <script>
    var error_count = <? print $error_count; ?>;
    var error_links_div = document.getElementById("error_links");
    if (error_count > 0) {
      error_links_div.innerHTML += "<span style='color: red;'>mulig feil</span><br>"
      for(i = 0; i < error_count; i++) {
        var current_error = document.getElementById("error"+i);
        error_links_div.innerHTML += "<a href='#error"+i+"' style='color: red;'>"+(i+1)+"</a>";
        if (i != error_count-1) {
          current_error.innerHTML = "<a href='#error"+(i+1)+"' style='color: red;'>mulig feil</a>";
          error_links_div.innerHTML += ", ";
        }
        else {
          current_error.innerHTML = "<a href='#error0' style='color: red;'>mulig feil</a>";
        }
      }
    }
  </script>
</body>
</html>
