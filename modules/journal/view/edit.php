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
$sql_voucher            = "select * from voucher where JournalID='$voucher_input->JournalID' and VoucherType='$voucher_input->VoucherType' and Active=1 order by VoucherID asc limit 1";
#print "$sql_voucher<br>\n";
$result_voucher_head    = $_lib['db']->db_query($sql_voucher);

#################################################################################################################
#Get date from $voucher_input->newest voucher
$sql_date               = "select VoucherDate, VoucherPeriod, DueDate from $db_table  where VoucherType='$voucher_input->VoucherType' and Active=1 order by TS desc limit 1";
$date                   = $_lib['storage']->get_row(array('query' => $sql_date));

#################################################################################################################
$voucherHead    = $_lib['db']->db_fetch_object($result_voucher_head);

$external_id_table = 'invoicein';
if ($voucherHead->VoucherType == 'S') $external_id_table = 'invoiceout';
$sql_journal_extern = "select ExternalID from ". $external_id_table ." WHERE JournalID = '$voucher_input->JournalID'";
$journal_extern_line = $_lib['db']->db_query($sql_journal_extern);
$journal_extern = $_lib['db']->db_fetch_object($journal_extern_line);

// only set if voucher type is 'S' or 'U'(outgoing or incoming invoice)
if ($voucherHead->VoucherType == 'U' || $voucherHead->VoucherType == 'S') $voucherHead->ExternalID = $journal_extern->ExternalID;

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
    #The accountplan id is what we send in the querystring
}

$accountplan    = $accounting->get_accountplan_object($voucher_input->AccountPlanID); #Find account

$Currency       = $voucherHead->Currency        ? $voucherHead->Currency        : $accountplan->Currency;
$voucher_input->VoucherType    = $voucherHead->VoucherType     ? $voucherHead->VoucherType     : $voucher_input->VoucherType;

if(!$voucher_input->VoucherDate) {
    $voucher_input->VoucherDate    = $voucherHead->VoucherDate     ? $voucherHead->VoucherDate     : $date->VoucherDate;
}

$voucher_input->CarID = $voucherHead->CarID ? $voucherHead->CarID : ($accountplan->EnableCar == 1 && isset($accountplan->CarID)) ? $accountplan->CarID : NULL;
$voucher_input->ProjectID = $voucherHead->ProjectID ? $voucherHead->ProjectID : ($accountplan->EnableProject == 1 && isset($accountplan->ProjectID)) ? $accountplan->ProjectID : NULL;
$voucher_input->DepartmentID = $voucherHead->DepartmentID ? $voucherHead->DepartmentID : ($accountplan->EnableDepartment == 1 && isset($accountplan->DepartmentID)) ? $accountplan->DepartmentID : NULL;
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
#Check if it is (main ledger)hovedbok or balance account
$fromperiod     = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'))."-01";
$toperiod       = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));
if($accountplan->AccountPlanType == 'result')
{
    #This is a result account - only to be calculated for this year to login form date
    $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher_input->AccountPlanID' and VoucherPeriod >= '$fromperiod' and VoucherPeriod <= '$toperiod' and Active=1 group by AccountPlanID";
}
else
{
    #Calculate from the beginning to form login date
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

if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) {
    $period_open = true;
    $period_disabled = "";
}
else {
    $period_open = false;
    $period_disabled = "disabled";
}

print $_lib['sess']->doctype ?>
<head>
<meta http-equiv="content-type" content="text/html; charset=macintosh">
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Bilag <? print $voucher_input->VoucherType ?><? print $voucher_input->JournalID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.175 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body onload="document.forms['<? print $form_name2 ?>'].elements['voucher.VoucherDate'].focus();">
<? includeinc('top');
   includeinc('left');

if(!$period_open) {
    ?>
    <div style="padding: 20px; border: 1px solid black;">
        <form action="<? print $MY_SELF ?>" method="post">
        Endre fra stengt bilagsdato:
        <input type="text" value="<? print date("Y-m-d"); ?>" name="voucher_VoucherDate" />
        <input type="submit" value="Endre" />

    </div>
    <?
}
?>

<? print exchange::getCurrenciesInJS(); ?>

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
<? print $_lib['form3']->URL(array('accesskey' => '<', 'description' => '<<', 'title' => 'Klikk for &aring; se forrige bilag', 'url' => $_lib['sess']->dispatch . 't=journal.edit&amp;voucher_VoucherType=' . $voucher_input->VoucherType . '&amp;voucher_JournalID=' . ($voucher_input->JournalID - 1) . '&amp;action_journalid_search=1&amp;new=1&amp;type='.$voucher_input->type)) ?>
&nbsp;
<? print $_lib['form3']->URL(array('accesskey' => '>', 'description' => '>>', 'title' => 'Klikk for &aring; se neste bilag',   'url' => $_lib['sess']->dispatch . 't=journal.edit&amp;voucher_VoucherType=' . $voucher_input->VoucherType . '&amp;voucher_JournalID=' . ($voucher_input->JournalID + 1) . '&amp;action_journalid_search=1&amp;new=1&amp;type='.$voucher_input->type)) ?>

<br />
<br />
<form id="hidden_currency_form" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name' => 'type'              , 'value' => $voucher_input->type)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.VoucherID' , 'value' => $voucherHead->VoucherID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.JournalID' , 'value' => $voucher_input->JournalID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'AccountLineID'  , 'value' => $voucher_input->AccountLineID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'VoucherType'       , 'value' => $voucher_input->VoucherType)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_mvalines'     , 'value' => $view_mvalines)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'view_linedetails'  , 'value' => $view_linedetails)) ?>
    <input type="hidden" name="action_postmotpost_save_currency" value="1" />
    <input class="hiddenForeignCurrencyID" type="hidden" name="voucher.ForeignCurrencyID" value="" />
    <input class="hiddenForeignConvRate" type="hidden" name="voucher.ForeignConvRate" value="" />
</form>
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
    $totalAmountIn  += $voucherHead->AmountIn;
    $totalAmountOut += $voucherHead->AmountOut;
  #}

    #Sum foreign amount
    if ($voucherHead->AmountIn > 0) {
        $totalForeignAmountIn += $voucherHead->ForeignAmount;
    } else if ($voucherHead->AmountOut > 0) {
        $totalForeignAmountOut += $voucherHead->ForeignAmount;
    }


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

        <input class="voucher" type="text" size="20" maxlength="25" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" name="voucher.VoucherDate" id="voucher.VoucherDate" value="<? print $voucher_input->VoucherDate; ?>"  accesskey="D" OnChange="update_period(this, '<? print $form_name2 ?>', 'voucher.VoucherDate', 'voucher.VoucherPeriod');">
        &nbsp;-&nbsp;<b><u>P</u>eriode</b>
        <?
/*if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) || isset($voucher_input->new)) */

          print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'VoucherPeriod', 'value' => $voucher_input->VoucherPeriod, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => '', 'disabled' => $period_disabled));
        ?>
	    &nbsp;&nbsp;
        <?
	    if($voucherHead->ExternalID) {
        print $_lib['form3']->button(array('name' => 'Vis i fakturabank', 'url' => $_SETUP['FB_SERVER_PROTOCOL'] ."://". $_SETUP['FB_SERVER'] . '/invoices/' . $voucherHead->ExternalID, 'target' => '_new'));
	    }
        ?>
        <?
            if(!$period_open)
                printf("Perioden %s er stengt", $voucher_input->VoucherPeriod);

        ?>
        <br /><br />

    </td>
  </tr>
  <tr class="voucher">
    <th colspan="2"></th>
    <th>Kontoplan</th>
    <th>Debet</th>
    <th>Kredit</th>
    <th>Valuta</th>
    <th>I<u>n</u>n</th>
    <th>U<u>t</u></th>
    <th>Kurs</th>
    <th><u>M</u>VA</th>
    <th>M<u>e</u>ngde</th>
    <th>Bil</th>
    <th><u>P</u>rosjekt</th>
    <th>A<u>v</u>d.</th>
    <th><u>F</u>orfallsdato</th>
    <th>Faktura</th>
    <th></th>
    <th><u>K</u>ID.</th>
    <th></th>
    <th colspan="2">Te<u>k</u>st</th>
    <th>&nbsp;</th>
  </tr>
  <tr class="voucher" valign="top">
    <td><? if($period_open) print $voucher_gui->update_journal_button_head($voucherHead, $voucher_input->VoucherPeriod, $voucher_input->VoucherType, $voucher_input->JournalID, $voucher_input->new, $rowCount, 'update'); ?></td>
    <td><? print $voucher_gui->active_line($voucher_input->VoucherIDOld, $voucherHead->VoucherID); ?></td>
    <td>
    <?
      print $voucher_gui->account($voucher_input->VoucherPeriod, $voucher_input->new, $db_table, $voucher, $voucher_input->AccountPlanID, false, !$period_open)
    ?>
    </td>
<? print $voucher_gui->creditdebitfield($AmountField, $accountplan, $voucher_input->AmountIn, $voucher_input->AmountOut, !$period_open) ?>
<? print $voucher_gui->currency2($voucherHead) ?>
<td><? print $voucher_gui->vat($voucherHead, $accountplan, $VAT, $oldVatID, $voucher_input->VatID, $voucher_input->VatPercent, !$period_open) ?></td>
    <td><? if($accountplan->EnableQuantity)   { ?><input class="voucher" type="text" size="5"  tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" name="voucher.Quantity" accesskey="Q" value="<? print $_lib['format']->Amount(array('decimals' => 3, 'value' => $voucherHead->Quantity, 'return' => 'value')) ?>"><? } ?></td>

    <td>
      <?
      $tmp = ($rowCount > 1 ? '' : $tabindex++);
      if($accountplan->EnableCar) {
        $_lib['form2']->car_menu2(array('table' => $db_table, 'field' => 'CarID', 'value' => $voucher_input->CarID, 'tabindex' => $tmp, 'disabled' => $period_disabled, 'active_reference_date' => $voucher_input->VoucherDate, 'unset' => true));
      }
      elseif (isset($voucher_input->CarID)) {
        $car_code_query = "select CarCode from car where CarID = $voucher_input->CarID";
        $car_code_row = $_lib['storage']->get_row(array('query' => $car_code_query));
        print $voucher_input->CarID . " " . $car_code_row->CarCode;
      }
      ?>
    </td>

    <td>
      <?
      $tmp = ($rowCount > 1 ? '' : $tabindex++);
      if($accountplan->EnableProject) {
        $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $voucher_input->ProjectID, 'tabindex' => $tmp, 'accesskey' => 'P', 'disabled' => $period_disabled, 'unset' => true));
      }
      elseif (isset($voucher_input->ProjectID)) {
        $project_name_query = "select Heading as ProjectName from project where ProjectID = $voucher_input->ProjectID";
        $project_row = $_lib['storage']->get_row(array('query' => $project_name_query));
        print $voucher_input->ProjectID . " " . $project_row->ProjectName;
      }
      ?>
    </td>

    <td>
      <?
      $tmp = ($rowCount > 1 ? '' : $tabindex++);
      if($accountplan->EnableDepartment) {
        $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $voucher_input->DepartmentID, 'tabindex' => $tmp, 'accesskey' => 'V', 'disabled' => $period_disabled, 'unset' => true));
      }
      elseif (isset($voucher_input->DepartmentID)) {
        $department_name_query = "select DepartmentName from department where DepartmentID = $voucher_input->DepartmentID";
        $department_row = $_lib['storage']->get_row(array('query' => $department_name_query));
        print $voucher_input->DepartmentID . " " . $department_row->DepartmentName;
      }
      ?>
    </td>

<td><input class="voucher" type="text" size="20" maxlength="25" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" accesskey="F" name="voucher.DueDate" value="<? if ($voucherHead->DueDate != "") print $voucherHead->DueDate; ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>

<td><input class="voucher" type="text" size="20" maxlength="25" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  accesskey="R" name="voucher.InvoiceID" value="<? print $voucher_input->InvoiceID ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
<td><input class="voucher match_checkbox" type="checkbox" name="voucher.matched_by" value="invoice" onclick="changeMatchBy(this);" <? if ($voucherHead->matched_by == 'invoice') print 'checked' ?> <? if(!$period_open) print "disabled='disabled'"; ?>></td>
<td><input class="voucher" type="text" size="20" maxlength="25" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>"  accesskey="R" name="voucher.KID" value="<? print $voucher_input->KID ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
<td><input class="voucher match_checkbox" type="checkbox" name="voucher.matched_by" value="kid" onclick="changeMatchBy(this);" <? if ($voucherHead->matched_by == 'kid') print 'checked' ?> <? if(!$period_open) print "disabled='disabled'"; ?>></td>
<td><input class="voucher" type="text" size="40" tabindex="<? if($rowCount>1) { print ''; } else { print $tabindex++; } ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher_input->Description; ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
    <td align="right">
    <? if($period_open && !$voucher_input->new) print $voucher_gui->update_journal_button_head($voucherHead, $voucher_input->VoucherPeriod, $voucher_input->VoucherType, $voucher_input->JournalID, $voucher_input->new, $rowCount, 'delete') ?>
    </td>
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
   if($accountplan->EnableReskontro == 0) {
      if ($voucher->AmountIn > 0) {
        $totalForeignAmountIn += $voucher->ForeignAmount;
      } else {
        $totalForeignAmountOut += $voucher->ForeignAmount;
      }
   }
   $totalForeignCurrency = $voucher->ForeignCurrencyID;

  if($accountplan->EnableReskontro == 0 and ($view_mvalines == 1 or ($view_mvalines == 0 and $voucher->DisableAutoVat != 1)))
  {
    $bgit++;

    #Calculate placement of submit button
    $numfields = 0;
    if(!$accountplan->EnableCurrency)   { $numfields += 2; };
    if(!$accountplan->EnableQuantity)   { $numfields++; };
    if(!$accountplan->EnableCar)        { $numfields++; };
    if(!$accountplan->EnableDepartment) { $numfields++; };
    if(!$accountplan->EnableProject)    { $numfields++; };

    $form_i++;
    $form_name = "voucher_$form_i";
    $sec_color=($bgit % 2)?"BGColorLight":"BGColorDark";

    #Calculate account balance
    if($accountplan->AccountPlanType == 'balance') {
      #This is a result konto - only to be calculated for this year to login form date
      $account_balance = "select sum(AmountIn) as sumin, sum(AmountOut) as sumout from $db_table  where AccountPlanID='$voucher->AccountPlanID' and VoucherPeriod >= '$fromperiod' and VoucherPeriod <= '$toperiod'group by AccountPlanID and Active=1";
    } else {
      #Calculate from the beginning to login form date
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
      <td><? if($period_open) print $voucher_gui->update_journal_button_line($voucher, $voucher_input->VoucherPeriod, $voucher_input->JournalID, $voucher_input->VoucherType, $voucher_input->type, 'update') ?></td>
      <td><? print $voucher_gui->active_line($voucher_input->VoucherIDOld, $voucher->VoucherID); ?></td>
      <td><? print $voucher_gui->account($voucher_input->VoucherPeriod,
                                         $voucher_input->new,
                                         $db_table,
                                         $voucher,
                                         $voucher->AccountPlanID,
                                         true,
                                         !($period_open && $voucher->VoucherType != 'A'));
        ?></td>
      <? print $voucher_gui->creditdebitfield($AmountField, $accountplan, $voucher->AmountIn, $voucher->AmountOut, !$period_open) ?>
      <? //print $voucher_gui->currency($voucherHead, $accountplan, $vb, $class1) ?>
      <? $currency_is_editable = $voucher->DisableAutoVat != 1; ?>
      <? print $voucher_gui->currency2($voucher, $currency_is_editable) // disable currency for nonhead lines ?>
      <td><? print $voucher_gui->vat($voucher, $accountplan, $VAT, $oldVatID, $VatID, $VatPercent, !$period_open) ?></td>
      <td><? if($accountplan->EnableQuantity) { if ($voucher->DisableAutoVat != 1) { ?><input class="voucher" type="text" size="5"  tabindex="<? print $tabindex++; ?>" accesskey="Q" name="voucher.Quantity" value="<? print $_lib['format']->Amount(array('decimals' => 3, 'value' => $voucher->Quantity, 'return' => 'value')); ?>"><? } } ?></td>
      <td>
<?
  if ($accountplan->EnableCar) {
    $car_menu_conf = array(
      'table'                 => $db_table,
      'field'                 => 'CarID',
      'value'                 => $voucher->CarID,
      'tabindex'              => $tabindex++,
      'active_reference_date' => $voucher_input->VoucherDate,
      'unset'                 => true
    );
    $_lib['form2']->car_menu2($car_menu_conf);
  }
  elseif (isset($voucher->CarID)) {
    $car_code_query = "select CarCode from car where CarID = $voucher->CarID";
    $car_code_row = $_lib['storage']->get_row(array('query' => $car_code_query));
    print $voucher->CarID . " " . $car_code_row->CarCode;
  }
?>
      </td>
      <td>
<?
  if ($accountplan->EnableProject) {
    $project_menu_conf = array(
      'table'     => $db_table,
      'field'     => 'ProjectID',
      'value'     => $voucher->ProjectID,
      'tabindex'  => $tabindex++,
      'accesskey' => 'P',
      'unset'                 => true
    );
    $_lib['form2']->project_menu2($project_menu_conf);
  }
  elseif (isset($voucher->ProjectID)) {
    $project_name_query = "select Heading as ProjectName from project where ProjectID = $voucher->ProjectID";
    $project_row = $_lib['storage']->get_row(array('query' => $project_name_query));
    print $voucher->ProjectID . " " . substr($project_row->ProjectName, 0, 20);
  }
?>
      </td>
      <td>
<?
  if ($accountplan->EnableDepartment) {
    $department_menu_conf = array(
      'table'     => $db_table,
      'field'     => 'DepartmentID',
      'value'     => $voucher->DepartmentID,
      'tabindex'  => $tabindex++,
      'accesskey' => 'V',
      'unset'                 => true
    );
    $_lib['form2']->department_menu2($department_menu_conf);
  }
  elseif (isset($voucher->DepartmentID)) {
    $department_name_query = "select DepartmentName from department where DepartmentID = $voucher->DepartmentID";
    $department_row = $_lib['storage']->get_row(array('query' => $department_name_query));
    print $voucher->DepartmentID . " " . substr($department_row->DepartmentName, 0, 20);
  }
?>
      </td>
      <td><input class="voucher" type="text" size="20" tabindex="<? print $tabindex++; ?>" name="voucher.DueDate"     accesskey="F" value="<? if ($voucherHead->DueDate != "") print $voucherHead->DueDate; else print $voucher_input->DueDate; ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>

      <td><input class="voucher" type="text" size="20" maxlength="25"  tabindex="<? print $tabindex++; ?>" name="voucher.InvoiceID"   accesskey="R" value="<? print $voucher->InvoiceID ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
      <td><input class="voucher match_checkbox" type="checkbox" name="voucher.matched_by" value="invoice" onclick="changeMatchBy(this);" <? if ($voucher->matched_by == 'invoice') print 'checked' ?> <? if(!$period_open) print "disabled='disabled'"; ?>></td>

      <td><input class="voucher" type="text" size="20" maxlength="25"  tabindex="<? print $tabindex++; ?>" name="voucher.KID"   accesskey="R" value="<? print $voucher->KID ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
      <td><input class="voucher match_checkbox" type="checkbox" name="voucher.matched_by" value="kid" onclick="changeMatchBy(this);" <? if ($voucher->matched_by == 'kid') print 'checked' ?> <? if(!$period_open) print "disabled='disabled'"; ?>></td>

      <td><input class="voucher" type="text" size="40" tabindex="<? print $tabindex++; ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher->Description; ?>" <? if(!$period_open) print "disabled='disabled'"; ?>></td>
      <td colspan="5" align="right"><? if($period_open) print $voucher_gui->update_journal_button_line($voucher, $voucher_input->VoucherPeriod, $voucher_input->JournalID, $voucher_input->VoucherType, $voucher_input->type, 'delete') ?></td>
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
    <td colspan="3"></td>
    <td align="right"><? print $_lib['format']->Amount($totalAmountIn) ?></td>
    <td align="right"><? print $_lib['format']->Amount($totalAmountOut) ?></td>
    <td colspan="2"></td>
    <td align="right"></td>
    <td align="right"></td>
  <td colspan="10" align="right">
  <? if($_lib['sess']->get_person('AccessLevel') >= 2) {
       if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) { ?>
    <form name="form_new_line" action="<? print $MY_SELF ?>" method="post">
    <input type="hidden" name="type"                    value="<? print $voucher_input->type ?>">
    <input type="hidden" name="voucher.JournalID"       value="<? print $voucher_input->JournalID ?>">

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
    <? print $_lib['form3']->hidden(array('name' => 'voucher.ForeignCurrencyID'  , 'value' => $voucher->ForeignCurrencyID)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.ForeignConvRate'  , 'value' => $voucher->ForeignConvRate)) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.ForeignAmountIn'  , 'value' => '0.0')) ?>
    <? print $_lib['form3']->hidden(array('name' => 'voucher.ForeignAmountOut'  , 'value' => '0.0')) ?>
    <? if($voucher->VoucherType != 'A') { ?>
    <input type="submit" name="action_voucherline_new"  value="Ny postering til bilag <? print $voucher_input->JournalID ?> (L)" class="button" tabindex="<? print $tabindex++; ?>" accesskey="L" >
    <? } ?>
    </form>
  <? }
  } ?>
  </td>
</table>
</fieldset>

<? if($voucher_input->AccountLineID) { ?>
<h2><a href="<? print $_lib['sess']->dispatch ?>t=bank.edit&AccountLineID=<? print $voucher_input->AccountLineID; ?>">Tilbake til bankavstemming</a></h2>
<? } ?>

<? includeinc('bottom'); ?>
</br>
</br>
<?
if($print_postmotpost_matches_button){
  $search_for = array(
    'AccountPlanID'                  => $voucher_input->AccountPlanID,
    'VoucherID'                      => $voucher_input->VoucherIDOld,
    'JournalID'                      => $voucher_input->JournalID,
    'VoucherType'                    => $voucher_input->VoucherType,
    'type'                           => $voucher_input->type,
    'EnableSingleChoose'             => '1',
    'From'                           => 'EnablePostMotPost',
    'action_postmotpost_get_matches' => 1
    );
?>
<div id="postmotpostmatches">
  <input id="postmotpostmatchesbutton" type="submit" name="action_postmotpost_get_matches" value="Hent &aring;pne poster p&aring; leverand&oslash;ren (Advarsel: kan ta lang tid)" OnClick='get_postmotpost_matches_for_jounal(<? print json_encode($search_for) ?>)') >
</div>
<? } ?>
