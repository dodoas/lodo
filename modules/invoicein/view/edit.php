<?
# $Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$ID         = (int) $_REQUEST['ID'];
$inline     = $_REQUEST['inline'];
#print_r($_REQUEST);

includelogic('invoicein/invoicein');
$invoicein  = new logic_invoicein_invoicein($_lib['input']->request);
$invoicein_status  = new logic_invoicein_invoicein($_lib['input']->request);

$tax_categories = array();

$VoucherType='S';

$db_table  = "invoicein";
$db_table2 = "invoiceinline";
$db_table3 = "invoiceallowancecharge";
$db_table4 = "invoicelineallowancecharge";

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$tabindex = 1;

$get_invoice            = "select I.* from $db_table as I where ID='$ID'";
#print "Get invoice " . $get_invoice . "<br>\n";
$invoicein              = $_lib['storage']->get_row(array('query' => $get_invoice));

// invoice with the specified id and fill its status
$invoicein_status->fill(array());
$invoicein_status = $invoicein_status->current();
if (!empty($invoicein_status->Status)) $_lib['message']->add($invoicein_status->Status);

$get_invoicefrom        = "select * from accountplan where AccountPlanID=" . (int) $invoicein->SupplierAccountPlanID;
#print "get_invoicefrom " . $get_invoicefrom . "<br>\n";
$invoicein->from        = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

$get_invoiceto          = "select CompanyName, IAddress as FromAddress, Email, IZipCode as Zip, ICity as City, ICountryCode as CountryCode, Phone, Mobile, OrgNumber from company where CompanyID='" . $_lib['sess']->get_companydef('CompanyID') . "'";
#print "get_invoiceto " . $get_invoiceto . "<br>\n";
$invoicein->to          = $_lib['storage']->get_row(array('query' => $get_invoiceto));

$query_invoiceline      = "select * from $db_table2 where ID='$ID' and Active <> 0 order by LineID asc";
#print "query_invoiceline" . $query_invoiceline . "<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);

$query_invoice_allowance_charge = "select * from $db_table3 where InvoiceID = '$ID' and InvoiceType = 'in'";
$result3                        = $_lib['db']->db_query($query_invoice_allowance_charge);

#print "Ferdig";
print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Faktura <? print $ID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>

<script type="text/javascript">
// indicates the if the allowances on invoice line level have been changed for the first time already
// so we can know if we should set the amount to negative if it is the first time
var first_time_array = [];
// needed so we can update invoice allowance/charge line without reloading the page
var allowances_charges = [];
<?
$allowances_charges = array();
$date = $_lib['sess']->get_session('LoginFormDate');
if (!is_null($invoicein->SupplierAccountPlanID)) {
  $supplier_accountplan = $accounting->get_accountplan_object($invoicein->SupplierAccountPlanID);
  $allowancecharge_query = "SELECT ac.AllowanceChargeID, ac.ChargeIndicator, a.AccountPlanID as InAccountPlanID, ac.Reason, ac.Amount, v.Percent as InVatPercent, case when a.VatID < 40 THEN a.VatID + 30 ELSE a.VatID END as InVatID
                  FROM allowancecharge ac LEFT JOIN accountplan a ON a.AccountPlanID = " . $supplier_accountplan->MotkontoResultat1 . " LEFT JOIN vat v ON v.VatID = a.VatID AND v.Active = 1 AND v.ValidFrom <= '" . $date . "' AND v.ValidTo >= '" . $date . "'
                  WHERE ac.Active = 1";
} else {
  $allowancecharge_query = "SELECT ac.AllowanceChargeID, ac.ChargeIndicator, NULL as InAccountPlanID, ac.Reason, ac.Amount, NULL as InVatPercent, NULL as InVatID
                  FROM allowancecharge ac
                  WHERE ac.Active = 1";
}
$allowancecharge_result = $_lib['db']->db_query($allowancecharge_query);
while($allowance_charge = $_lib['db']->db_fetch_assoc($allowancecharge_result)) {
  $allowances_charges[$allowance_charge["AllowanceChargeID"]] = $allowance_charge;
?>
allowances_charges['<? print $allowance_charge['AllowanceChargeID']; ?>'] = {ChargeIndicator: '<? print $allowance_charge['ChargeIndicator']; ?>', Reason: '<? print $allowance_charge['Reason']; ?>', AccountPlanID: '<? print $allowance_charge['InAccountPlanID']; ?>', Amount: parseFloat('<? print $allowance_charge['Amount']; ?>'), VatPercent: parseFloat('<? print $allowance_charge['InVatPercent']; ?>'), VatID: '<? print $allowance_charge['InVatID']; ?>'};
<?
}
?>
var is_hidden = false; // changes if toggled or some action to add an all allowance/charge is used

// hide/show all allowance/charge elements (ones which belong to allowance_charge class)
function showHideAllowanceCharge() {
  is_hidden = !is_hidden;
  var allowance_charge_elements = document.getElementsByClassName("allowance_charge");
  for(var i = 0; i < allowance_charge_elements.length; i++) {
    allowance_charge_elements[i].hidden = is_hidden;
  }
}

// update data for the line's allowance/charge if anything of note changes
// should only format the amount correctly
function updateInvoiceLineAllowanceChargeData(element) {
  var id = element.id;
  var name = element.id.split('.');
  var allowance_charge_id = name[2];

  name[1] = 'ChargeIndicator';
  var charge_indicator_element = document.getElementById(name.join('.'));
  charge_indicator = charge_indicator_element.value;

  name[1] = 'Amount';
  var amount_element = document.getElementById(name.join('.'));
  var amount = toNumber(amount_element.value);
  if (typeof(first_time_array[allowance_charge_id]) == 'undefined') first_time_array[allowance_charge_id] = true;
  if (charge_indicator == 0 && first_time_array[allowance_charge_id] && amount > 0) {
    first_time_array[allowance_charge_id] = false;
    amount = -amount;
  }
  amount_element.value = toAmountString(amount, 2);
}

// update data for the allowance/charge line if allowance/charge changes
function updateInvoiceAllowanceChargeLineData(element, update_amount) {
  update_amount = typeof update_amount !== 'undefined' ? update_amount : false;
  var id = element.id;
  var name = element.id.split('.');

  name[1] = 'AllowanceChargeID';
  var allowance_charge_id_element = document.getElementById(name.join('.'));
  var allowance_charge_id = allowance_charge_id_element.value;
  if (allowance_charge_id == '') return;

  name[1] = 'ChargeIndicator';
  var change_indicator_element = document.getElementById(name.join('.'));
  change_indicator_element.value = allowances_charges[allowance_charge_id]['ChargeIndicator'];

  name[1] = 'AllowanceChargeReason';
  var reason_element = document.getElementById(name.join('.'));
  reason_element.value = allowances_charges[allowance_charge_id]['Reason'];

  name[1] = 'Amount';
  var amount_element = document.getElementById(name.join('.'));
  var amount = 0.0;
  if (update_amount) {
    amount = allowances_charges[allowance_charge_id]['Amount'];
  } else {
    amount = toNumber(amount_element.value);
  }
  amount_element.value = toAmountString(amount, 2);

  name[1] = 'VatID';
  var vat_id_element = document.getElementById(name.join('.'));
  var vat_id = allowances_charges[allowance_charge_id]['VatID'];
  vat_id_element.value = vat_id;

  name[1] = 'VatPercent';
  var vat_percent_element = document.getElementById(name.join('.'));
  var vat_percent = allowances_charges[allowance_charge_id]['VatPercent'];
  vat_percent_element.innerHTML = toAmountString(vat_percent, 2) + "%";

  name[1] = 'VatAmount';
  var vat_amount_element = document.getElementById(name.join('.'));
  var vat_amount = amount * (vat_percent / 100.0);
  vat_amount_element.innerHTML = toAmountString(vat_amount, 2);
}

function updateAndPerformAction(link_button) {
  var link_button_location = $(link_button).attr('href');
  var form = $(link_button).parents('form');
  $(form).attr('action', link_button_location+'&action_invoicein_update=1');
  form.submit();
}

function updatePeriodFromInvoiceDate(invoice_date_element) {
  var invoice_id = invoice_date_element.id.split('.')[2];
  var invoice_date = invoice_date_element.value;
  var invoice_period_element = document.getElementById("invoicein.Period." + invoice_id);
  var invoice_period = invoice_date.substr(0,7);
  if (validDate(invoice_date)) {
    // console.log("InvoiceDate is valid!");
    for(var i=0; i < invoice_period_element.options.length; i++) {
      if(invoice_period_element.options[i].value === invoice_period) {
        invoice_period_element.selectedIndex = i;
        invoice_period_element.disabled = true;
        break;
      }
    }
  } else {
    // console.log("InvoiceDate is NOT valid!");
  }
}
</script>
</head>

<body>
<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="ID" value="<? print $ID ?>">
<input type="hidden" name="inline" value="edit">

<table class="lodo_data">
<thead>
    <tr>
        <td>Bilagsnummer</td>
        <td>
          <? if ($invoicein->JournalID) { ?>
          <a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$invoicein->VoucherType&amp;voucher_JournalID=$invoicein->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $invoicein->VoucherType ?><? print $invoicein->JournalID ?></a></td>
        <? } ?>
        </td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td>Fakturanummer</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceNumber', 'pk' => $ID, 'value'=>$invoicein->InvoiceNumber, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
        <td>KID:</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'KID', 'pk' => $ID, 'value'=>$invoicein->KID, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td><b>Leverand&oslash;r</b></td>
        <td><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'SupplierAccountPlanID', 'pk'=>$ID, 'value'=>$invoicein->SupplierAccountPlanID, 'tabindex' => $tabindex++, 'type' => array(0 => supplier))) ?></td>
        <td><b>Mottaker</b></td>
        <td><? print $invoicein->to->CompanyName ?></td>
    </tr>
    <tr>
        <td><b>Betal til konto</b></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'SupplierBankAccount', 'pk' => $ID, 'value' => $invoicein->SupplierBankAccount, 'tabindex' => $tabindex++)) ?></td>
        <td><b>Betal fra konto</b></td>
        <td><? print $_lib['form3']->select(array('table'=>$db_table, 'field'=>'CustomerBankAccount', 'pk' => $ID, 'value' => $invoicein->CustomerBankAccount, 'query' => 'form.BankAccount', 'width' => 30, 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td><b>Betal (bel&oslash;p)</b></td>
        <td><? print $_lib['format']->Amount($invoicein->TotalCustPrice); ?></td>
        <td>Betalingsm&aring;te</td>
        <td><? print $_lib['form3']->select(array('table'=>$db_table, 'field'=>'PaymentMeans', 'pk' => $ID, 'value' => $invoicein->PaymentMeans, 'query' => 'form.PaymentMeans', 'width' => 30, 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Orgnr</td>
        <td><? print $invoicein->from->OrgNumber ?></td>
        <td>Orgnr</td>
        <td><? print $invoicein->to->OrgNumber ?></td>
    </tr>
    <tr height="5">
        <td colspan="4"></td>
    </tr>
</thead>

<tbody>
    <tr height="5">
        <td colspan="4"></td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td>Valuta</td>
      <td>
<?php
#Retrieve all currencies

$currencies = exchange::getAllCurrencies();
?>
      <select name="<?php echo $db_table . '.CurrencyID.' . $InvoiceID ?>">
<?
foreach ($currencies as $currency) {
?>
<option value="<? echo $currency->CurrencyISO; ?>" <?php if ($invoicein->CurrencyID == $currency->CurrencyISO) echo 'selected'; ?>><? echo $currency->CurrencyISO; ?></option>
<?
}
?>
      </select>
</td>
    </tr>
    <tr>
      <td>Faktura dato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceDate', 'pk'=>$ID, 'value'=>substr($invoicein->InvoiceDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++, 'OnChange' => 'updatePeriodFromInvoiceDate(this)')) ?></td>
      <td>Faktura periode</td>
      <td>
        <?
        if($accounting->is_valid_accountperiod($invoicein->Period, $_lib['sess']->get_person('AccessLevel'))) {
            print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'Period', 'pk'=>$ID, 'value' => $invoicein->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => '', 'disabled' => true));
        } else {
            print $invoicein->Period;
        }
        ?>
      </td>
    </tr>
    <tr>
        <td>Forfalls dato</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DueDate', 'pk'=>$ID, 'value'=>substr($invoicein->DueDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
<? if (!$invoicein->Imported) { ?>
        <td>Er med i reisegarantifondet</td>
        <td><? print $_lib['form3']->Checkbox(array('table'=>$db_table, 'field'=>'isReisegarantifond', 'pk'=>$ID, 'value'=>$invoicein->isReisegarantifond, 'tabindex' => $tabindex++)) ?></td>
<? } else { ?>
        <td></td>
        <td></td>
    </tr>
    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefCustomer', 'pk'=>$ID, 'value'=>$invoicein->RefCustomer, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Deres ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefInternal', 'pk'=>$ID, 'value'=>$invoicein->RefInternal, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$ID,  'value' =>  $invoicein->ProjectID, 'unset' => true)) ?></td>
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'pk'=>$ID, 'value' => $invoicein->DepartmentID, 'unset' => true)); ?></td>
    </tr>
    <tr>
      <td>Leveringsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$ID, 'value'=>$invoicein->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Betalingsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'PaymentCondition', 'pk'=>$ID, 'value'=>$invoicein->PaymentCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td valign="top">Kommentar (intern)</td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentInternal', 'pk'=>$ID, 'value'=>$invoicein->CommentInternal, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
<?
/* Commented out per Arnt's instructions
    <tr>
      <td>Remitteringsstatus</td>
      <td><? print $invoicein->RemittanceStatus ?></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td>Remittering godkjent</td>
      <td><? print $invoicein->RemittanceApprovedDateTime ?></td>
      <td>Remittering godkjent av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->RemittanceApprovedPersonID) ?></td>
    </tr>
    <tr>
      <td>Remittering sendt</td>
      <td><? print $invoicein->RemittanceSendtDateTime ?></td>
      <td>Remittering sendt av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->RemittanceSendtPersonID) ?></td>
    </tr>
    <tr>
      <td>RemittanceSequence</td>
      <td><? print $invoicein->RemittanceSequence ?></td>
      <td>RemittanceDaySequence</td>
      <td><? print $invoicein->RemittanceDaySequence ?></td>
    </tr>
*/
?>
<? if (!$invoicein->Imported) { ?>
    <tr>
      <td>Opprettet</td>
      <td><? print $invoicein->InsertedDateTime ?></td>
      <td>Opprettet av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->InsertedByPersonID) ?></td>
    </tr>
<? } else { ?>
    <tr>
      <td>Hentet fra fakturabank</td>
      <td><? print $invoicein->FakturabankDateTime ?></td>
      <td>Hentet fra fakturabank av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->FakturabankPersonID) ?></td>
    </tr>
<? } ?>
</tbody>
</table>
  <?
    if ($_lib['db']->db_numrows($result3) > 0) {
      $vat_allowance = 0;
      $vat_charge    = 0;
      $sum_allowance = 0;
      $sum_charge    = 0;
      $ac_errors = array();
      $invoice_allowances_charges = array();
      while($acrow = $_lib['db']->db_fetch_object($result3)) {
        $invoice_allowances_charges[] = $acrow;
        $vat_allowance_tax_amount     = ($acrow->ChargeIndicator) ? 0 : -$acrow->Amount * ($acrow->VatPercent/100);
        $vat_charge_tax_amount        = ($acrow->ChargeIndicator) ? $acrow->Amount * ($acrow->VatPercent/100) : 0;
        $sum_allowance_taxable_amount = ($acrow->ChargeIndicator) ? 0 : -$acrow->Amount;
        $sum_charge_taxable_amount    = ($acrow->ChargeIndicator) ? $acrow->Amount : 0;
        $vat_allowance += $vat_allowance_tax_amount;
        $vat_charge    += $vat_charge_tax_amount;
        $sum_allowance += $sum_allowance_taxable_amount;
        $sum_charge    += $sum_charge_taxable_amount;
        $tax_categories[$acrow->VatPercent]->TaxableAmount += $sum_charge_taxable_amount + $sum_allowance_taxable_amount;
        $tax_categories[$acrow->VatPercent]->TaxAmount     += $vat_charge_tax_amount + $vat_allowance_tax_amount;
        if($acrow->AllowanceChargeID && is_null($allowances_charges[$acrow->AllowanceChargeID]["InVatID"])) {
          $ac_errors[] = "Leverand&oslash;rs resultat konto har ikke valgt Mva kode";
        }
      }
    }
?>

<div id="invoice_errors" class="allowance_charge">
  <? if(!empty($ac_errors)) {
      print "<div class='warning'>". implode("<br>", $ac_errors) ."</div>";
  } ?>
</div>

<br>
<table width="875">
<thead>
  <tr>
    <td>Konto</td>
    <td>ProduktNr</td>
    <td>Produktnavn</td>
    <td>Bil</td>
    <td>Antall</td>
    <td>Enhetspris</td>
    <td>Enhetspris inkl. MVA</td>
    <td>MVA</td>
    <td>MVA bel&oslash;p</td>
    <td>Bel&oslash;p U/MVA</td>
    <td>Bel&oslash;p M/MVA</td>
    <td></td>
  </tr>
</thead>

<tbody>
<?
$sumlines = 0;
$vatlines = 0;
$rowCounter = 0;
while($row2 = $_lib['db']->db_fetch_object($result2))
{
    $LineID=$row2->LineID;

    $query_invoiceline_allowance_charge = "select * from $db_table4 where InvoiceLineID = '$LineID' and InvoiceType = 'in'";
    $result4                            = $_lib['db']->db_query($query_invoiceline_allowance_charge);

    $allowances = 0;
    $charges    = 0;
    $line_allowancecharges = array();
    while($acrow = $_lib['db']->db_fetch_object($result4)) {
      $line_allowancecharges[] = $acrow;
      if ($acrow->AllowanceChargeType == 'line') {
        $allowances += ($acrow->ChargeIndicator == 0) ? $acrow->Amount : 0;
        $charges    += ($acrow->ChargeIndicator == 1) ? $acrow->Amount : 0;
      }
    }

    $sumline = round($row2->TotalWithoutTax,2);
    $vatline = round($row2->TaxAmount, 2);
    $sumlines += $sumline;
    $vatlines += $vatline;
    $tax_categories[$row2->Vat]->TaxableAmount += $sumline;
    $tax_categories[$row2->Vat]->TaxAmount     += $vatline;
    ?>
    <tr>
        <td>
        <?
        $aconf = array();
        $aconf['table']         = $db_table2;
        $aconf['field']         = 'AccountPlanID';
        $aconf['value']         = $row2->AccountPlanID;
        $aconf['pk']            = $LineID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = '';
        $aconf['width']         = '20';
        $aconf['type'][]        = 'result';
        $aconf['type'][]        = 'balance';
        $aconf['tabindex']      = $tabindex++;
        $accountplan            = $accounting->get_accountplan_object($row2->AccountPlanID);
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
        </td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductNumber', 'pk'=>$LineID, 'value'=>$row2->ProductNumber, 'width' => 20, 'maxlength' => 20, 'tabindex'=>$tabindex++)) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName'  , 'pk'=>$LineID, 'value'=>$row2->ProductName,   'width' => 20, 'maxlength' => 80, 'tabindex'=>$tabindex++)) ?></td>
        <td>
<?
  if($accountplan->EnableCar) {
    $car_menu_conf = array(
      'table'                 => $db_table2,
      'field'                 => 'CarID',
      'pk'                    => $LineID,
      'value'                 => $row2->CarID,
      'tabindex'              => $tabindex++,
      'active_reference_date' => substr($invoicein->InvoiceDate,0,10)
    );
    $_lib['form2']->car_menu2($car_menu_conf);
  }
  elseif (isset($row2->CarID)) {
    $car_code_query = "select CarCode from car where CarID = $row2->CarID";
    $car_code_row = $_lib['storage']->get_row(array('query' => $car_code_query));
    print $row2->CarID . " " . $car_code_row->CarCode;
  }
?></td>
        <td align="center"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$row2->QuantityDelivered, 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCostPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCostPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'width' => 5, 'maxlength' => 5, 'tabindex'=>$tabindex++)) ?></td>
        <td align="right"><nobr><? print $_lib['format']->Amount($row2->TaxAmount) ?></nobr></td>
        <td align="right"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'TotalWithoutTax', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->TotalWithoutTax, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td align="right"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'TotalWithTax', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->TotalWithTax, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td>
        <? if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($invoicein->Period, $_lib['sess']->get_person('AccessLevel'))) { ?>
          <a onclick="updateAndPerformAction(this); return false;" href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_line_allowance_charge_new=1&amp;LineID=$LineID&amp;inline=edit" ?>" class="button">Ny linje rabatt/kostnad</a>
          <a onclick="updateAndPerformAction(this); return false;" href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_linedelete=1&amp;LineID=$LineID&amp;inline=edit" ?>" class="button">Slett</a>
        <? } ?>
    <tr>
        <td colspan="8"><? print $_lib['form3']->textarea(array('table'=>$db_table2, 'field'=>'Comment', 'pk'=>$LineID, 'value'=>$row2->Comment, 'tabindex'=>$tabindex++, 'min_height'=>'1', 'height'=>'1', 'width'=>'80')) ?>
    <?
    $rowCounter++;
    $AccountPlanID = $row2->AccountPlanID;
    print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$LineID));

    if (count($line_allowancecharges) > 0) {
      foreach($line_allowancecharges as $acrow) {
    ?>
    <tr class="allowance_charge line_invoice_allowancecharge_<? print $LineID; ?>" id="invoiceline_allowancecharge_fields_<? print $acrow->InvoiceLineAllowanceChargeID; ?>">
      <td>
        <? print $_lib['form3']->hidden(array('name'=>('invoicelineallowancecharge.InvoiceLineID.'.$acrow->InvoiceLineAllowanceChargeID), 'value'=>$acrow->InvoiceLineID)) ?>
        <?
          print $_lib['form3']->Generic_menu3(array(
            'data'     => array('1' => 'Kostnad', '0' => 'Rabatt'),
            'table'    => $db_table4,
            'field'    => 'ChargeIndicator',
            'value'    => $acrow->ChargeIndicator,
            'tabindex' => $tabindex++,
            'OnChange' => 'updateInvoiceLineAllowanceChargeData(this)',
            'pk'       => $acrow->InvoiceLineAllowanceChargeID));
          print $_lib['form3']->Generic_menu3(array(
            'data'     => array('line' => 'Linje', 'price' => 'Pris'),
            'table'    => $db_table4,
            'field'    => 'AllowanceChargeType',
            'value'    => $acrow->AllowanceChargeType,
            'tabindex' => $tabindex++,
            'pk'       => $acrow->InvoiceLineAllowanceChargeID));
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'     => $db_table4,
            'field'     => 'AllowanceChargeReason',
            'pk'        => $acrow->InvoiceLineAllowanceChargeID,
            'value'     => $acrow->AllowanceChargeReason,
            'width'     => '20',
            'maxlength' => '255',
            'tabindex'  => $tabindex++));
        ?>
      </td>
      <td colspan="3"></td>
      <? if ($acrow->AllowanceChargeType == 'line') print '<td colspan="3"></td>'; ?>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'    => $db_table4,
            'field'    => 'Amount',
            'pk'       => $acrow->InvoiceLineAllowanceChargeID,
            'value'    => $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount),
            'class'    => 'number',
            'OnChange' => 'updateInvoiceLineAllowanceChargeData(this)',
            'tabindex' => $tabindex++));
        ?>
      </td>
      <? if ($acrow->AllowanceChargeType == 'price') print '<td colspan="3"></td>'; ?>
      <td></td>
      <td>
        <a onclick="updateAndPerformAction(this); return false;" href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_line_allowance_charge_delete=1&amp;InvoiceLineAllowanceChargeID=$acrow->InvoiceLineAllowanceChargeID&amp;inline=edit" ?>" class="button">Slett</a>
      </td>
    </tr>
<?
      }
    }
?>
    <tr><td colspan="10"><hr/></td></tr>
<?
}
    if (!empty($invoice_allowances_charges)) {
      foreach ($invoice_allowances_charges as $acrow) {
?>
    <tr class="allowance_charge global_invoice_allowancecharge" id="invoice_allowancecharge_fields_<? print $acrow->InvoiceAllowanceChargeID; ?>">
      <td>
        <?
          print $_lib['form3']->Generic_menu3(array(
            'query'    => "select AllowanceChargeID, CONCAT(IF(ChargeIndicator, 'Kostnad - ', 'Rabatt - '), Reason) from allowancecharge where Active = 1",
            'table'    => $db_table3,
            'field'    => 'AllowanceChargeID',
            'width'    => 40,
            'value'    => $acrow->AllowanceChargeID,
            'tabindex' => $tabindex++,
            'OnChange' => 'updateInvoiceAllowanceChargeLineData(this, true)',
            'pk'       => $acrow->InvoiceAllowanceChargeID));
          print $_lib['form3']->hidden(array(
            'table'    => $db_table3,
            'field'    => 'ChargeIndicator',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $acrow->ChargeIndicator));
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'     => $db_table3,
            'field'     => 'AllowanceChargeReason',
            'pk'        => $acrow->InvoiceAllowanceChargeID,
            'value'     => $acrow->AllowanceChargeReason,
            'width'     => '20',
            'maxlength' => '255',
            'tabindex'  => $tabindex++));
        ?>
      </td>
      <td colspan="4"></td>
      <td>
        <?
          print '<span id="' . $db_table3 . '.VatPercent.' . $acrow->InvoiceAllowanceChargeID . '" >' . $_lib['format']->Percent($acrow->VatPercent) . '</span>';
          print $_lib['form3']->hidden(array(
            'table'    => $db_table3,
            'field'    => 'VatID',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $acrow->VatID));
        ?>
      </td>
      <td class="number">
        <?
          print '<span id="' . $db_table3 . '.VatAmount.' . $acrow->InvoiceAllowanceChargeID . '" >' . $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount * ($acrow->VatPercent / 100.0)) . '</span>';
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'    => $db_table3,
            'field'    => 'Amount',
            'OnChange' => 'updateInvoiceAllowanceChargeLineData(this)',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount),
            'class'    => 'number',
            'tabindex' => $tabindex++));
        ?>
      </td>
      <td>
        <a onclick="updateAndPerformAction(this); return false;" href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_allowance_charge_delete=1&amp;InvoiceAllowanceChargeID=$acrow->InvoiceAllowanceChargeID&amp;inline=edit" ?>" class="button">Slett</a>
      </td>
    </tr>
<?
      }
    }
?>
    <tr class="allowance_charge"><td colspan="10"><hr/></td></tr>
</tbody>

<tfoot>
    <tr>
        <td>
        <?
        if(!$invoicein->Locked) {
			if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($invoicein->Period), $_lib['sess']->get_person('AccessLevel')))
			{ ?>
                <a onclick="updateAndPerformAction(this); return false;" href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&amp;ID=$ID&amp;action_invoicein_linenew=1&amp;AccountPlanID=$AccountPlanID&amp;inline=edit" ?>" class="button" accesskey="N">Ny linje (N)</a>
            <?
			}
        }
        ?>
        </td>
        <td colspan="9"></td>
    </tr>
    <tr>
      <td>
        <a href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_allowance_charge_new=1&amp;inline=edit" ?>" class="button">Ny rabatt-/kostnadslinje</a>
      </td>
      <td colspan="2">
        <a href="#" onclick="showHideAllowanceCharge(); return false;" class="button">Vis/Skjul rabatt/kostnad info</a>
      </td>
      <td colspan="7"></td>
    </tr>
    <tr>
        <td colspan="10" align="right">
        <? if($invoicein->ExternalID) { ?><a href="<?php echo $_SETUP['FB_SERVER_PROTOCOL'] ."://". $_SETUP['FB_SERVER']; ?>/invoices/<? print $invoicein->ExternalID ?>" title="Vis i Fakturabank" target="_new">Vis i fakturabank</a><? } ?>

        <?
        if(!$invoicein->Locked) {
	
			if($_lib['sess']->get_person('AccessLevel') >= 2)
			{
				if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($invoicein->Period), $_lib['sess']->get_person('AccessLevel'))) {
					print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoicein_update', 'value'=>'Lagre faktura (S)', 'accesskey'=>'S', 'tabindex' => $tabindex++));
				} else {
					print "Periode stengt";
				}
			} else {
			  print "Du har ikke tilgang til &aring; lagre faktura";
			}
		} else {
			print "Faktura l&aring;st";
    }
    ?>
        </td>
    </tr>
    <tr>
      <td>
        <?
          if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($invoicein->Period), $_lib['sess']->get_person('AccessLevel')) && ($_lib['sess']->get_person('AccessLevel') >= 4) && (!$invoicein->Imported) && (!$invoicein->JournalID)) {
            print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoicein_delete', 'value'=>'Slett faktura (D)', 'accesskey'=>'D', 'tabindex' => $tabindex++));
          }
        ?>
      </td>
    </tr>
    </form>
    <tr>
        <td colspan="5" align="right">Linje sum</td>
        <td colspan="2" id="invoiceout.LineExtensionAmount" align="right"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">Rabatt sum</td>
        <td colspan="2" id="invoiceout.AllowanceTotalAmount" align="right"><? print $_lib['format']->Amount($sum_allowance) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">Kostnad sum</td>
        <td colspan="2" id="invoiceout.ChargeTotalAmount" align="right"><? print $_lib['format']->Amount($sum_charge) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">Totalt U/MVA</td>
        <td colspan="2" id="invoiceout.TaxExclusiveAmount" align="right"><? print $_lib['format']->Amount($sumlines + $sum_allowance + $sum_charge) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">Total MVA</td>
        <td colspan="2" id="invoiceout.TaxTotalTaxAmount" align="right"><? print $_lib['format']->Amount($vatlines + $vat_allowance + $vat_charge) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">Totalt M/MVA</td>
        <td colspan="2" id="invoiceout.TaxInclusiveAmount" align="right"><? print $_lib['format']->Amount($vatlines + $vat_allowance + $vat_charge + $sumlines + $sum_allowance + $sum_charge) ?></td>
        <?
            print $_lib['form3']->Input(array('type'=>'hidden', 'table'=>'field', 'field'=>'count', 'value'=>$rowCounter));
        ?>
    </tr>
    <tr>
        <td colspan="7"><br><hr></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td>Prosent</td>
        <td></td>
        <td class="number">Bel&oslash;p</td>
    </tr>

<?
    $sum_tax = 0;
    ksort($tax_categories);
    foreach($tax_categories as $percent => $tax_category) {
      $sum_tax += $tax_category->TaxAmount;
?>
    <tr>
        <td colspan="5" align="right"><? print $_lib['format']->Percent($percent); ?></td>
        <td colspan="2" align="right"><? print $_lib['format']->Amount($tax_category->TaxAmount); ?></td>
    </tr>
<?
    }
?>
    <tr>
        <td colspan="5" align="right">Total MVA</td>
        <td colspan="2" align="right"><? print $_lib['format']->Amount($sum_tax); ?></td>
    </tr>
    <tr>
        <td colspan="7"><br><hr>
    </tr>

     <tr>
     	<td>L&aring;st: <? if($invoicein->Locked) { ?>Ja<? } else { ?>Nei<? } ?></td>
     </tr>
</tfoot>
</table>
</body>
</html>
