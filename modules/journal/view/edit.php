<?
$db_table       = "voucher";

#require_once  "setup.inc";
includelogic('accounting/accounting');
includelogic('voucher/vouchergui');
includelogic('exchange/exchange');

$accounting     = new accounting();
$voucher_gui    = new framework_logic_vouchergui();

require_once  "record.inc";

$db_table     = "voucher";

################################################################################

$view_mvalines      = $_lib['input']->getProperty('view_mvalines');
$view_linedetails   = $_lib['input']->getProperty('view_linedetails');
if(!$view_mvalines)     $view_mvalines = 0;
if(!$view_linedetails)  $view_linedetails = 0;

//tatt vekk pga link feil i fra rapporter
//$voucher_input->type = $_REQUEST['type'];
$view   = $_REQUEST['view'];

#################################################################################################################
#Get voucher data
$sql_voucher            = "select * from voucher where JournalID='$voucher_input->JournalID' and VoucherType='$voucher_input->VoucherType' and Active=1 order by VoucherID desc";
#print "$sql_voucher<br>\n";
$result_voucher         = $_lib['db']->db_query($sql_voucher);
$rowCount               = $_lib['db']->db_numrows($result_voucher);
if($rowCount > 1) {
   $voucher_input->setNew(0, 'bilaget finnes og er da ikke nytt');
}

$sql_journal_extern = "select ExternalID from invoicein WHERE JournalID = '$voucher_input->JournalID'";
$journal_extern_line = $_lib['db']->db_query($sql_journal_extern);
$journal_extern = $_lib['db']->db_fetch_object($journal_extern_line);

$sql_voucher            = "select * from voucher where JournalID='$voucher_input->JournalID' and VoucherType='$voucher_input->VoucherType' and Active=1 order by VoucherID asc limit 1";
#print "$sql_voucher<br>\n";
$result_voucher_head    = $_lib['db']->db_query($sql_voucher);

#################################################################################################################
#Get date from $voucher_input->newest voucher
$sql_date               = "select VoucherDate, VoucherPeriod, DueDate from $db_table  where VoucherType='$voucher_input->VoucherType' and Active=1 order by TS desc limit 1";
$date                   = $_lib['storage']->get_row(array('query' => $sql_date));

#################################################################################################################
$voucherHead    = $_lib['db']->db_fetch_object($result_voucher_head);
$voucherHead->ExternalID = $journal_extern->ExternalID;

if(!$voucherHead and $voucher_input->action['journalid_search'])
{
    $_lib['message']->add(array('message' => "Bilagsnummer: $voucher_input->JournalID, type: $voucher_input->VoucherType finnes ikke"));
    $voucher_input->new = 1;
}

$form_i     = 1;
$i          = 1;
$form_name  = "voucher_$form_i";
$form_name2 = "voucher";
$tabindex   = 1;
##############################################################
#DEFAULT VALUES
if($voucherHead->AccountPlanID > 0) {
    $voucher_input->AccountPlanID  = $voucherHead->AccountPlanID;
} elseif(!$voucher_input->AccountPlanID) {
    $voucher_input->AccountPlanID = $voucher_input->DefAccountPlanID;
} else {
    #The accountplan id is what we sendt in the querystring
}

$accountplan    = $accounting->get_accountplan_object($voucher_input->AccountPlanID); #Find account

$Currency       = $voucherHead->Currency        ? $voucherHead->Currency        : $accountplan->Currency;
$voucher_input->VoucherType    = $voucherHead->VoucherType     ? $voucherHead->VoucherType     : $voucher_input->VoucherType;

if(!$voucher_input->VoucherDate) {
    $voucher_input->VoucherDate    = $voucherHead->VoucherDate     ? $voucherHead->VoucherDate     : $date->VoucherDate;
}

$voucher_input->ProjectID      = $voucherHead->ProjectID       ? $voucherHead->ProjectID       : $accountplan->ProjectID;
$voucher_input->DepartmentID   = $voucherHead->DepartmentID    ? $voucherHead->DepartmentID    : $accountplan->DepartmentID;
$voucher_input->AmountIn       = $voucherHead->AmountIn        ? $voucherHead->AmountIn        : $voucher_input->AmountIn;
$voucher_input->AmountOut      = $voucherHead->AmountOut       ? $voucherHead->AmountOut       : $voucher_input->AmountOut;
$voucher_input->KID            = $voucherHead->KID             ? $voucherHead->KID             : $voucher_input->KID;

$voucher_input->InvoiceID      = $voucherHead->InvoiceID       ? $voucherHead->InvoiceID       : $voucher_input->InvoiceID;
$voucher_input->Description    = $voucherHead->Description     ? $voucherHead->Description     : $voucher_input->Description;
#print "VoucherDate: $voucher_input->VoucherDate - $voucherHead->VoucherDate     : $date->VoucherDate";

if(!$voucher_input->VoucherDate || $voucher_input->VoucherDate == '0000-00-00')  $voucher_input->VoucherDate = $_lib['sess']->get_session('LoginFormDate');
$voucher_input->VoucherPeriod  = $voucherHead->VoucherPeriod   ? $voucherHead->VoucherPeriod   : $_lib['date']->get_this_period($voucher_input->VoucherDate);

if($voucher_input->AccountPlanID > $_lib['sess']->get_companydef('AccountHovedbokResultatTo'))
    if($accountplan->EnableCredit == 1)
    {
        $voucher_input->DueDate = $_lib['date']->add_Days($date->VoucherDate, $accountplan->CreditDays);
    }
    else
    {
        $voucher_input->DueDate = $date->VoucherDate;
    }
else
    $voucher_input->DueDate = $date->VoucherDate;
##############################################################
#Calculate account balance
#Check if it is hovedbok or balamse konto
$fromperiod     = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'))."-01";
$toperiod       = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
if($accountplan->AccountPlanType == 'result')
{
    #This is a result konto - only to be calculated for this year to innlogged date
    $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher_input->AccountPlanID' and VoucherPeriod >= '$fromperiod' and VoucherPeriod <= '$toperiod' and Active=1 group by AccountPlanID";
}
else
{
    #Calculate from the beginning to innlogged date
    $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher_input->AccountPlanID' and VoucherPeriod <= '$toperiod' and Active=1 group by AccountPlanID";
}
$vb = $_lib['storage']->get_row(array('query' => $account_balance));
$balance = ($vb->sumin - $vb->sumout);

if($balance >= 0)
{
  $class1 = "debitblue";
}
elseif($balance < 0)
{
  $class1 = "creditred";
}

##############################################################
#Get accountplan info

if($accountplan->EnableCurrency) {
  $_lib['message']->add(array('message' => "Merk: Utenlandsk valuta er skrudd p&aring; konto " . $voucher_input->AccountPlanID .", Det er bare lov &aring; fylle ut valuta bel&oslash;p"));
}

$voucher_input->VatPercent  = '';
$voucher_input->VatID       = '';

if($accountplan->EnableVAT == 1)
{
    $VAT = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $voucher_input->VoucherDate));
    if(isset($voucherHead->VatID) and (isset($accountplan->EnableVATOverride) or $VAT->EnableVatOverride))
    {
      if(isset($accountplan->VatID))
      {

          $voucher_input->VatPercent = $voucherHead->Vat;
          $voucher_input->VatID      = $voucherHead->VatID;
      }
    }
    else
    {
      $voucher_input->VatPercent       = $VAT->Percent;
      $voucher_input->VatID            = $VAT->VatID;
      $voucher_input->oldVatPercent    = $voucherHead->Vat;
      $voucher_input->oldVatID         = $voucherHead->VatID;
    }
}
#gjøre en beregning for å sjekke om balanse og resultat kontoer går i null
$query_balanse      = "select sum(v.AmountIn) as saldo1, sum(v.AmountOut) as saldo2 from voucher as v, accountplan as a where v.AccountPlanID=a.AccountPlanID and a.AccountPlanType = 'balance' and v.Active=1";
#print "Balanse: $query_balanse<br>";
$balanceCheck       = $_lib['storage']->get_row(array('query' => $query_balanse));

$query_result       = "select sum(v.AmountIn) as saldo1, sum(v.AmountOut) as saldo2 from voucher as v, accountplan as a where v.AccountPlanID=a.AccountPlanID and a.AccountPlanType = 'result' and v.Active=1";
#print "Resultat: $query_result<br>";
$resultCheck        = $_lib['storage']->get_row(array('query' => $query_result));

$balanceAccount     = $_lib['sess']->get_companydef('VoucherBalanceAccount');
$query_balanse      = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where AccountPlanID = '$balanceAccount' and Active=1";
$balanceAccount     = $_lib['storage']->get_row(array('query' => $query_balanse));

$resultAccount      = $_lib['sess']->get_companydef('VoucherResultAccount');
$query_result       = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from voucher where AccountPlanID = '$resultAccount' and Active=1";
$resultAccount      = $_lib['storage']->get_row(array('query' => $query_result));

print $_lib['sess']->doctype ?>
<head>
<meta http-equiv="content-type" content="text/html; charset=macintosh">
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Bilag <? print $voucher_input->VoucherType ?><? print $voucher_input->JournalID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.175 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body onload="document.forms['<? print $form_name2 ?>'].elements['voucher.VoucherDate'].focus();">
<? includeinc('top') ?>
<? includeinc('left') ?>
<?
#print "Til slutt<br>\n";
#print_r($voucher_input->request('toppen av skjermbildet'));
#choose = search fopr KID
// print "Hit2,5<br>";

if (!$_REQUEST['action_journal_kidsearch'])
{

if(abs($balanceCheck->saldo1 - $balanceCheck->saldo2) != 0)
    $_lib['message']->add(array('message' => "Balanse kontoene g&aring;r ikke i 0, det er skjedd en feil. Sum balanse kontoene er: "   . $_lib['format']->Amount($balanceCheck->saldo1 - $balanceCheck->saldo2) . "<br>Ta kontakt med din kontaktperson for Lodo"));
if(abs($resultCheck->saldo1 - $resultCheck->saldo2) != 0)
    $_lib['message']->add(array('message' => "Resultat kontoene g&aring;r ikke i 0, det er skjedd en feil. Sum resultat kontoene er: " . $_lib['format']->Amount($resultCheck->saldo1 - $resultCheck->saldo2)   . "<br>Ta kontakt med din kontaktperson for Lodo"));
?>

<? if(1 == 2) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<form class="voucher" name="find_customer" action="<? print $MY_SELF ?>" method="post">
    Kunde/Org-nummer
    <? print $_lib['form3']->hidden(array('name' => 'type'              , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'VoucherType'       , 'value' => $voucher_input->VoucherType)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'     , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'new'               , 'value' => 1)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
    <input type="text"    name="CustomerNumber"     value="<? print $CustomerNumber ?>" />
    <input type="submit"  name="action_journalcustomer_search" value="S&oslash;k">
</form>

<form class="voucher" name="find_customer" action="<? print $MY_SELF ?>" method="post">
    Bilagsnummer
    <? print $_lib['form3']->hidden(array('name' => 'new'               , 'value' => $voucher_input->new)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'type'           , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
    <? print $_lib['form3']->Type_menu3(array('field' => 'VoucherType' , 'type' => 'VoucherType', 'table' => 'voucher', 'value' => $voucher_input->VoucherType, 'required'=>'1')) ?>
    <input type="text"    name="voucher.JournalID"          value="<? print $voucher_input->JournalID ?>" />
    <input type="submit"  name="action_journalid_search"    value="S&oslash;k">
</form>

<form class="voucher" name="find_kid" action="<? print $_lib['sess']->dispatch ?>" method="post" target="$voucher_input->new">
    <? print $_lib['form3']->hidden(array('name' => 't'              , 'value' => 'journal.search')) ?>
    <? print $_lib['form3']->hidden(array('name' => 'type'           , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'VoucherType'    , 'value' => $voucher_input->VoucherType)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
    <? print $_lib['form3']->Type_menu3(array('name' => 'VoucherSearchType', 'type' => 'VoucherSearchType', 'value' => $VoucherSearchType, 'required'=>'1')) ?>
    <input type="text"    name="searchstring"       value="<? print $searchstring ?>" size="8" />
    <input type="submit"  name="action_journalcustomer_search" value="S&oslash;k">
</form>

<form class="voucher" name="find_customer" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name' => 'type'                      , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'VoucherType'               , 'value' => $voucher_input->VoucherType)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'new'                       , 'value' => $voucher_input->new)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.JournalID'         , 'value' => $voucher_input->JournalID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'action_journalid_search'   , 'value' => 1)) ?>
    <? print $_lib['form3']->checkbox(array('value'=>$view_mvalines  , 'name'=>'view_mvalines',    'autosubmit'=>'1')) ?> MVA linjer
    <? print $_lib['form3']->checkbox(array('value'=>$view_linedetails, 'name'=>'view_linedetails', 'autosubmit'=>'1')) ?> Detaljer
</form>
&nbsp;
<? print $_lib['form3']->URL(array('description' => '<<', 'title' => 'Klikk for &aring; se forrige bilag', 'url' => $_lib['sess']->dispatch . 't=journal.edit&amp;voucher_VoucherType=' . $voucher_input->VoucherType . '&amp;voucher_JournalID=' . ($voucher_input->JournalID - 1) . '&amp;action_journalid_search=1')) ?>
&nbsp;
<? print $_lib['form3']->URL(array('description' => '>>', 'title' => 'Klikk for &aring; se neste bilag',   'url' => $_lib['sess']->dispatch . 't=journal.edit&amp;voucher_VoucherType=' . $voucher_input->VoucherType . '&amp;voucher_JournalID=' . ($voucher_input->JournalID + 1) . '&amp;action_journalid_search=1')) ?>

<br />
<br />
<form class="voucher" name="<? print $form_name2 ?>" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name' => 'type'              , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.VoucherID' , 'value' => $voucherHead->VoucherID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.JournalID' , 'value' => $voucher_input->JournalID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'VoucherType'       , 'value' => $voucher_input->VoucherType)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
<?
#Find number of fields showed to user
$numfields = 0;

  #if ($accountplan->EnableReskontro == 0)
  #{
    $totalAmountIn  += $voucher_input->AmountIn;
    $totalAmountOut += $voucher_input->AmountOut;
  #}


if(!$accountplan->EnableCurrency)   { $numfields += 2; };
if(!$accountplan->EnableQuantity)   { $numfields++; };
if(!$accountplan->EnableDepartment) { $numfields++; };
if(!$accountplan->EnableProject)    { $numfields++; };

$acctmp = $accounting->get_accountplan_object($voucher_input->AccountPlanID);
?>
<fieldset>
<table class="lodo_data">
  <tr class="voucher BGColorLight">
    <td colspan="14"><br />
        <nobr><b>Bilags<u>n</u>r</b>
         <? print $voucher_input->VoucherType;

            if(!$voucherHead->JournalID)
            {
                if($voucher_input->VoucherType == 'U' or $voucher_input->VoucherType == 'L' or $voucher_input->VoucherType == 'S')
                {
                    ?>
                    <input class="voucher" type="text" size="4"   tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  name="voucher.JournalID"  value="<? print $voucher_input->JournalID; ?>" accesskey="B" OnChange="update_reference(this, '<? print $form_name2 ?>', 'voucher.JournalID', 'voucher.KID');">
                    <?
                }
                else
                {
                    ?>
                    <input class="voucher" type="text" size="4"   tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  name="voucher.JournalID"  value="<? print $voucher_input->JournalID; ?>" accesskey="B">
                    <?
                }
                ?>
                <input class="voucher" type="hidden" name="JournalIDOrg"     value="<? print $voucher_input->JournalIDOrg; ?>">
            <?
            }
            else
            {
                print $voucher_input->JournalID;
            }
        ?>
        &nbsp;-&nbsp;<b>Bilags<u>d</u>ato</b>
        
        <input class="voucher" type="text" size="10" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" name="voucher.VoucherDate" id="voucher.VoucherDate" value="<? print $voucher_input->VoucherDate; ?>"  accesskey="D" OnChange="update_period(this, '<? print $form_name2 ?>', 'voucher.VoucherDate', 'voucher.VoucherPeriod');">
        &nbsp;-&nbsp;<b><u>P</u>eriode</b>
        <?
        if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) || isset($voucher_input->new)) {
            print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'VoucherPeriod', 'value' => $voucher_input->VoucherPeriod, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => ''));
        } else {
            print $voucher_input->VoucherPeriod;
        }
        ?>
	    &nbsp;&nbsp;
	    <?
	    if($voucherHead->ExternalID) {
	    	print $_lib['form3']->button(array('name' => 'Vis i fakturabank', 'url' => 'https://fakturabank.no/invoices/' . $voucherHead->ExternalID, 'target' => '_new'));
	    } ?>
        <br /><br />
    </td>
  </tr>
  <tr class="voucher">
    <th></th>
    <th>Kontoplan</th>
    <th>Debet</th>
    <th>Kredit</th>
    <th>Saldo</th>
    <th></th>
    <th>I<u>n</u>n</th>
    <th>U<u>t</u></th>
    <th><u>M</u>VA</th>
    <th>M<u>e</u>ngde</th>
    <th>A<u>v</u>d.</th>
    <th><u>P</u>rosjekt</th>
    <th><u>F</u>orfallsdato</th>
    <th><u>K</u>ID.</th>
    <th>Faktura</th>
    <th colspan="2">Te<u>k</u>st</th>
    <th>&nbsp;</th>
  </tr>
  <tr class="voucher" valign="top">
    <td><? print $voucher_gui->active_line($voucher_input->VoucherIDOld, $voucherHead->VoucherID); ?></td>
    <td><? print $voucher_gui->account($voucher_input->VoucherPeriod, $voucher_input->new, $db_table, $voucher, $voucher_input->AccountPlanID, false) ?></td>
    <? print $voucher_gui->creditdebitfield($AmountField, $accountplan, $voucher_input->AmountIn, $voucher_input->AmountOut) ?>
    <? //print $voucher_gui->currency($voucherHead, $accountplan, $vb, $class) ?>
    <? print $voucher_gui->currency2($voucherHead) ?>
    <td><? print $voucher_gui->vat($voucherHead, $accountplan, $VAT, $oldVatID, $voucher_input->VatID, $voucher_input->VatPercent) ?></td>
    <td><? if($accountplan->EnableQuantity)   { ?><input class="voucher" type="text" size="5"  tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" name="voucher.Quantity" accesskey="Q" value="<? print $_lib['format']->Amount($voucherHead->Quantity) ?>"><? } ?></td>
    <td><? if($rowCount>1) { $tmp = ''; } else { $tmp = $tabindex++; }; if($accountplan->EnableDepartment) { $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $voucher_input->DepartmentID, 'tabindex' => $tmp, 'accesskey' => 'V')); } ?></td>
    <td><? if($rowCount>1) { $tmp = ''; } else { $tmp = $tabindex++; }; if($accountplan->EnableProject)    { $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $voucher_input->ProjectID, 'tabindex' => $tmp, 'accesskey' => 'P'));    } ?></td>
    <td><input class="voucher" type="text" size="10" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" accesskey="F" name="voucher.DueDate" value="<? if ($voucherHead->DueDate != "") print $voucherHead->DueDate; ?>"></td>
    <td><input class="voucher" type="text" size="22" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  accesskey="R" name="voucher.KID" value="<? print $voucher_input->KID ?>"></td>
    <td><input class="voucher" type="text" size="22" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  accesskey="R" name="voucher.InvoiceID" value="<? print $voucher_input->InvoiceID ?>"></td>
    <td><!-- <? if($rowCount>1) { $tmp = ''; } else { $tmp = $tabindex++; }; print $_lib['form3']->Type_menu3(array('table' => $db_table, 'field' => 'DescriptionID', 'value' => $voucherHead->DescriptionID, 'type' => 'VoucherDescriptionID', 'tabindex' => $tmp, 'accesskey' => 'E')); ?> --></td>
    <td><input class="voucher" type="text" size="10" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher_input->Description; ?>"></td>
    <td align="right"><? print $voucher_gui->update_journal_button_head($voucherHead, $voucher_input->VoucherPeriod, $voucher_input->VoucherType, $voucher_input->JournalID, $voucher_input->new, $rowCount) ?></td>    
  </tr>
  <? if($view_linedetails == 1) { ?>
  <tr valign="top">
    <td colspan="10">
        <? print $voucher_gui->comment($voucherHead) ?>
    </td>
   </tr>
   <? } ?>
</form>


<?
#print "VID: $VoucherID<br>";
#if($VoucherID) { #Hvorfor denne? FJernet

$rowCount--;
$bgit=0;
$i = 0;
while($voucher = $_lib['db']->db_fetch_object($result_voucher) and $rowCount>0) {

    if (!($i % 2)) { $row_class = "r0"; } else { $row_class = "r1"; };
    $i++;

    $rowCount--;
    ##############################################################
    #Get accountplan info
    $accountplan = $accounting->get_accountplan_object($voucher->AccountPlanID);
    //print $accountplan->EnableReskontro." - ".$accountplan->AccountPlanID." - ".$voucher->AccountPlanID;
    #We sum even the lines that we do not see
    if($accountplan->EnableReskontro == 0) {
        $totalAmountIn  += $voucher->AmountIn;
        $totalAmountOut += $voucher->AmountOut;
    }
    
   #Sum foreign amount
   if ($voucher->AmountIn > 0)
      $totalForeignAmountIn  += $voucher->ForeignAmount;
   if ($voucher->AmountOut > 0)
      $totalForeignAmountOut += $voucher->ForeignAmount;
   $totalForeignCurrency = $voucher->ForeignCurrencyID;

  if($accountplan->EnableReskontro == 0 and ($view_mvalines == 1 or ($view_mvalines == 0 and $voucher->DisableAutoVat != 1)))
  {
    $bgit++;

    #Calculate placement of submit button
    $numfields = 0;
    if(!$accountplan->EnableCurrency)   { $numfields += 2; };
    if(!$accountplan->EnableQuantity)   { $numfields++; };
    if(!$accountplan->EnableDepartment) { $numfields++; };
    if(!$accountplan->EnableProject)    { $numfields++; };

    $form_i++;
    $form_name = "voucher_$form_i";
    $sec_color=($bgit % 2)?"BGColorLight":"BGColorDark";

    #Calculate account balance
    if($accountplan->AccountPlanType == 'balance') {
      #This is a result konto - only to be calculated for this year to innlogged date
      $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher->AccountPlanID' and VoucherPeriod >= '$fromperiod' and VoucherPeriod <= '$toperiod'group by AccountPlanID and Active=1";
    } else {
      #Calculate from the beginning to innlogged date
      $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher->AccountPlanID' and VoucherPeriod <= '$toperiod' group by AccountPlanID and Active=1";
    }

    $vb = $_lib['storage']->get_row(array('query' => $account_balance));

    $balance = ($vb->sumin - $vb->sumout);
    ##############################################################
    if($balance >= 0)
    {
      $class1 = "debitblue";
    }
    else
    {
      $class1 = "creditred";
    }

    ##############################################################
    #Currency handling
    if(!$voucher->Currency) {
      $Currency = $accountplan->Currency;
    } else {
      $Currency = $voucher->Currency;
    }

    #print "VAT: kontoplan:$accountplan->VATAccount bilag:$voucher->Vat<br>";
    #Vat default handling
    unset($VatPercent);
    unset($VatID);
    if($accountplan->EnableVAT == 1)
    {
        $VAT = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $voucher_input->VoucherDate));
        #print_r($VAT);
        if(isset($voucher->VatID) and (isset($accountplan->EnableVATOverride) or $VAT->EnableVatOverride))
        {
          if(isset($accountplan->VatID))
          {

              $VatPercent = $voucher->Vat;
              $VatID      = $voucher->VatID;
          }
        }
        else
        {
          $VatPercent = $VAT->Percent;
          $VatID      = $VAT->VatID;
          $oldVatPercent = $voucher->Vat;
          $oldVatID      = $voucher->VatID;
        }
    }
    //print isset($voucher->VatID)." ".$voucher->VoucherID;

    //if($_lib['sess']->get_person('AccessLevel') > 2)
    //{
   ?>
    <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
    <input type="hidden" name="type"                  value="<? print $voucher_input->type ?>">
    <input type="hidden" name="voucher.JournalID"     value="<? print $voucher_input->JournalID ?>">
    <input type="hidden" name="voucher.VoucherID"     value="<? print $voucher->VoucherID ?>">
    <input type="hidden" name="voucher.VoucherPeriod" value="<? print $voucher->VoucherPeriod ?>">
    <input type="hidden" name="VoucherType"           value="<? print $voucher_input->VoucherType ?>"  />
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <input type="hidden" name="voucher.VoucherDate"   value="<? print $voucher->VoucherDate ?>" />
    <input type="hidden" name="voucher.VoucherDateOld"   value="<? print $voucher->VoucherDate ?>" />
    <input type="hidden" name="view_mvalines"         value="<? print $view_mvalines ?>" />
    <input type="hidden" name="view_linedetails"      value="<? print $view_linedetails ?>" />

    <tr class="<? print $row_class ?> voucher">
      <td><? print $voucher_gui->active_line($voucher_input->VoucherIDOld, $voucher->VoucherID); ?></td>
      <td><? print $voucher_gui->account($voucher_input->VoucherPeriod, $voucher_input->new, $db_table, $voucher, $voucher->AccountPlanID, true) ?></td>
      <? print $voucher_gui->creditdebitfield($AmountField, $accountplan, $voucher->AmountIn, $voucher->AmountOut) ?>
      <? //print $voucher_gui->currency($voucherHead, $accountplan, $vb, $class1) ?>
      <!--<td></td><td></td><td></td><td></td>-->
      <?  print $voucher_gui->currency2($voucher) // disable currency for nonhead lines ?>
      <td><? print $voucher_gui->vat($voucher, $accountplan, $VAT, $oldVatID, $VatID, $VatPercent) ?></td>
      <td><? if($accountplan->EnableQuantity)   { ?><input class="voucher" type="text" size="5"  tabindex="<? print $tabindex++; ?>" accesskey="Q" name="voucher.Quantity"        value="<? print "$voucher->Quantity"; ?>"><? } ?></td>
      <td><? if($accountplan->EnableDepartment) { ?><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $voucher->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V')); } ?></td>
      <td><? if($accountplan->EnableProject)    { ?><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' => 'ProjectID', 'value' => $voucher->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P')); } ?></td>
      <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" name="voucher.DueDate"     accesskey="F" value="<? if ($voucherHead->DueDate != "") print $voucherHead->DueDate; else print $voucher_input->DueDate; ?>"></td>
      <td><input class="voucher" type="text" size="22"  tabindex="<? print $tabindex++; ?>" name="voucher.KID"   accesskey="R" value="<? print $voucher->KID ?>"></td>
      <td><input class="voucher" type="text" size="22"  tabindex="<? print $tabindex++; ?>" name="voucher.InvoiceID"   accesskey="R" value="<? print $voucher->InvoiceID ?>"></td>
      <td><!-- <? print $_lib['form3']->Type_menu3(array('table' => $db_table, 'field' => 'DescriptionID', 'value' => $voucher->DescriptionID, 'type' => 'VoucherDescriptionID', 'tabindex' => $tabindex++, 'accesskey' => 'E')); ?> </td>-->
      <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher->Description; ?>"></td>
      <td colspan="5" align="right"><? print $voucher_gui->update_journal_button_line($voucher, $voucher_input->VoucherPeriod, $voucher_input->JournalID, $voucher_input->VoucherType, $voucher_input->type) ?></td>
    </tr>
    <? if($view_linedetails == 1) { ?>
    <tr class="<? print $row_class ?>">
      <td colspan="16">
        <?
            print $voucher_gui->comment($voucher);
        ?>
      </td>
    </tr>
    <? } ?>
    </form>
    <?
    }
  }
?>

<tr>
    <td colspan="1"></td>
    <td></td>
    <td align="right"><? print $_lib['format']->Amount($totalAmountIn) ?></td>
    <td align="right"><? print $_lib['format']->Amount($totalAmountOut) ?></td>
    <td align="right"></td>
    <td align="right"></td>
    <td align="right"><? print $totalForeignCurrency ." ". $_lib['format']->Amount($totalForeignAmountIn) ?></td>
    <td align="right"><? print $totalForeignCurrency ." ". $_lib['format']->Amount($totalForeignAmountOut) ?></td>
  <td colspan="10" align="right">
  <? if($_lib['sess']->get_person('AccessLevel') >= 2) {
       if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) { ?>
    <form name="form_new_line" action="<? print $MY_SELF ?>" method="post">
    <input type="hidden" name="type"                    value="<? print $voucher_input->type ?>">
    <input type="hidden" name="voucher.JournalID"       value="<? print $voucher_input->JournalID ?>">
    <input type="hidden" name="voucher.VoucherID"       value="<? // kommentert ut av Geir for  at momsen ikke skal slettes ved klikk pa ny postering. print $voucherHead->VoucherID ?>">
    <input type="hidden" name="voucher.VoucherPeriod"   value="<? print $voucherHead->VoucherPeriod ?>">
    <input type="hidden" name="voucher.VoucherDate"     value="<? print $voucherHead->VoucherDate ?>">
    <input type="hidden" name="voucher.AccountPlanID"   value="<? print $voucherHead->AccountPlanID ?>">
    <input type="hidden" name="voucher.DueDate"         value="<? print $voucherHead->DueDate ?>">
    <input type="hidden" name="voucher.KID"             value="<? print $voucherHead->KID ?>">
    <input type="hidden" name="voucher.DescriptionID"   value="<? print $voucherHead->DescriptionID ?>">
    <input type="hidden" name="voucher.Description"     value="<? print $voucherHead->Description ?>">
    <input type="hidden" name="VoucherType"             value="<? print $voucher_input->VoucherType ?>" />
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'     , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
    <input type="submit" name="action_voucherline_new"  value="Ny postering til bilag <? print $voucher_input->JournalID ?> (L)" class="button" tabindex="<? print $tabindex++; ?>" accesskey="L" >
    </form>
  <? }
  } ?>
  </td>
</table>
</fieldset>

<? if($voucher_input->AccountLineID) { ?>
<h2><a href="<? print $_lib['sess']->dispatch ?>t=bank.edit&AccountLineID=<? print $voucher_input->AccountLineID; ?>">Tilbake til bankavstemming</a></h2>
<? } ?>

<?
if($_showresult) {
  #print "<br />showresult<br />";
  print $_showresult;
}
} else {

$kid = $_REQUEST['voucher_KID'];

#Find all open posts with this kid defined on this customer
$_lib['sess']->debug("I edit bildet");
#$query = "select AmountIn, AmountOut, JournalID, KID, InvoiceID, VoucherDate from voucher as v, voucherstruct as s where (v.AmountIn = '$Amount' or v.AmountOut = '$Amount') and (v.JournalID=s.Parent or v.JournalID=s.Child) and Closed=0";
$query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.JournalID=s.Parent or v.JournalID=s.Child)  where KID = '$kid' and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType== 'customer' or a.AccountPlanType== 'supplier' or a.AccountPlanType== 'employee') and a.AccountPlanID=v.AccountPlanID and Active=1";
#print "<h1>$query</h1><br>";
#$result_search = $_lib['db']->db_query($query);
?>
<body>
<h2>S&oslash;kebegrep: <? print $searchstring ?>, treff  <? print $_lib['db']->db_numrows($result_search) ?></h2>
<table class="lodo_journal">
<thead>
<tr>
  <th>Type</th>
  <th>Bilag</th>
  <th>Dato</th>
  <th>Konto</th>
  <th>Konto navn</th>
  <th>Debet</th>
  <th>Kredit</th>
  <th>KID</th>
  <th>Faktura</th>
  <th></th>
</tr>
</thead>

<tbody>
<?
if($VoucherID > 0) { #Vi har ikke dette på den forste linjen for den er lagret.
    while($row = $_lib['db']->db_fetch_object($result_search)) {
      #$accounting->sum_journal();
      #$line = $_lib['storage']->get_row(array('query' => 'select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut '));
    ?>
        <tr>
            <td><? print $row->VoucherType ?></td>
            <td><? print $row->JournalID ?></td>
            <td><? print $row->VoucherDate ?></td>
            <td><? print $row->AccountPlanID ?></td>
            <td><? print $row->AccountName ?></td>
            <td><? print $row->AmountIn ?></td>
            <td><? print $row->AmountOut ?></td>
            <td><? print $row->KID ?></td>
            <td><? print $row->InvoiceID ?></td>
            <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
            <? print $_lib['form3']->hidden(array('name' => 'type'                  , 'value' => $voucher_input->type)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'VoucherType'           , 'value' => $voucher_input->VoucherType)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.JournalID'     , 'value' => $voucher_input->JournalID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.VoucherID'     , 'value' => $VoucherID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AccountPlanID' , 'value' => $row->AccountPlanID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AmountIn'      , 'value' => $row->AmountOut)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AmountOut'     , 'value' => $row->AmountIn)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.KID'       , 'value' => $row->KID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'     , 'value' => $voucher_input->AccountLineID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
            <td><input type="submit"  name="action_voucher_update" value="Velg"></td>
            </form>
        </tr>
    <? } ?>
    </table>
            <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
            <? print $_lib['form3']->hidden(array('name' => 'type'                  , 'value' => $voucher_input->type)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'VoucherType'           , 'value' => $voucher_input->VoucherType)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.JournalID'     , 'value' => $voucher_input->JournalID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.VoucherID'     , 'value' => $VoucherID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AccountPlanID' , 'value' => $row->AccountPlanID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AmountIn'      , 'value' => $row->AmountOut)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.AmountOut'     , 'value' => $row->AmountIn)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'voucher.KID'       , 'value' => $row->KID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'     , 'value' => $voucher_input->AccountLineID)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
            <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
            <td><input type="submit"  name="action_voucher_update" value="Glem det"></td>
            </form>
    <? }
}?>
<? includeinc('bottom') ?>
