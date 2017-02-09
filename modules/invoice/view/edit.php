<?
// needed to access oauth session parameters
session_start();
# $Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$InvoiceID = (int) $_REQUEST['InvoiceID'];
$inline       = $_REQUEST['inline'];
#print_r($_REQUEST);

$tmp_redirect_url = "$_SETUP[OAUTH_PROTOCOL]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// change only if full(with InvoiceID) url
if (strpos($tmp_redirect_url, 'InvoiceID') !== false) $_SESSION['oauth_tmp_redirect_back_url'] = $tmp_redirect_url;
// and if missing in url, add InvoiceID
else $_SESSION['oauth_tmp_redirect_back_url'] = $tmp_redirect_url . "InvoiceID=" . $InvoiceID;
$_SESSION['oauth_invoice_id'] = $InvoiceID;

$tax_categories = array();

$VoucherType='S';

$db_table  = "invoiceout";
$db_table2 = "invoiceoutline";
$db_table3 = "invoiceoutprint";
$db_table4 = "invoiceallowancecharge";
$db_table5 = "invoicelineallowancecharge";

includelogic('exchange/exchange');

includelogic('accounting/accounting');


$accounting = new accounting();
require_once "record.inc";

if (isset($_SESSION['oauth_invoice_error'])) {
  if (is_array($_SESSION['oauth_invoice_error'])) $_SESSION['oauth_invoice_error'] = implode(", ", $_SESSION['oauth_invoice_error']);
  $_lib['message']->add($_SESSION['oauth_invoice_error']);
  unset($_SESSION['oauth_invoice_error']);
}

$get_invoice            = "select I.* from $db_table as I where InvoiceID='$InvoiceID'";
#print "Get invoice " . $get_invoice . "<br>\n";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$query_invoiceline      = "select * from $db_table2 where InvoiceID='$InvoiceID' and Active <> 0 order by LineID asc";
#print "query_invoiceline" . $query_invoiceline . "<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);

$get_invoiceprint       = "select InvoicePrintDate from $db_table3 where InvoiceID='$InvoiceID'";
$row_print              = $_lib['storage']->get_row(array('query' => $get_invoiceprint));

$query_invoice_allowance_charge = "select * from $db_table4 where InvoiceID = '$InvoiceID' and InvoiceType = 'out'";
$result3                        = $_lib['db']->db_query($query_invoice_allowance_charge);

/**
 * factoring setup
 */
/*$factoring = new invoice_factoring($InvoiceID);

if(!$factoring->isGlobalEnabled() || (!$factoring->hasFactoring() && (int)$row->CustomerAccountPlanID == 0)) {
    $willUseFactoring = false;
}*/

print $_lib['sess']->doctype;
$tabindex = 1;

?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Faktura <? print $InvoiceID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
    <? includeinc('combobox') ?>

<script type="text/javascript">
// needed so we can update invoice line without reloading the page
var products = [];
var is_hidden = false; // changes if toggled or some action to add an allowance/charge is used
<?
$product_query = 'SELECT ProductID, ProductName, UnitCustPrice, AccountPlanID
                  FROM product
                  WHERE Active = 1';
$product_result = $_lib['db']->db_query($product_query);
while($product = $_lib['db']->db_fetch_assoc($product_result)) {
  if (!empty($product['AccountPlanID'])) {
    $accountplan = $accounting->get_accountplan_object($product['AccountPlanID']);
    $vat = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $row->InvoiceDate));
  }
?>
products['<? print $product['ProductID']; ?>'] = {ProductName: '<? print $product['ProductName']; ?>', UnitCustPrice: parseFloat('<? print $product['UnitCustPrice']; ?>'), VatPercent: parseFloat('<? print ($vat->Percent == '') ? 0 : $vat->Percent; ?>'), AccountPlanID: '<? print $product['AccountPlanID']; ?>'};
<?
}
?>
// indicates the if the allowances on invoice line level have been changed for the first time already
// so we can know if we should set the amount to negative if it is the first time
var first_time_array = [];
// needed so we can update invoice allowance/charge line without reloading the page
var allowances_charges = [];
<?
$allowancecharge_query = "SELECT AllowanceChargeID, ChargeIndicator, OutAccountPlanID, Reason, Amount, OutVatPercent, OutVatID
                  FROM allowancecharge
                  WHERE Active = 1";
$allowancecharge_result = $_lib['db']->db_query($allowancecharge_query);
$allowances_charges = array();
while($allowance_charge = $_lib['db']->db_fetch_assoc($allowancecharge_result)) {
  $allowances_charges[$allowance_charge["AllowanceChargeID"]] = $allowance_charge;
?>
allowances_charges['<? print $allowance_charge['AllowanceChargeID']; ?>'] = {ChargeIndicator: '<? print $allowance_charge['ChargeIndicator']; ?>', Reason: '<? print $allowance_charge['Reason']; ?>', AccountPlanID: '<? print $allowance_charge['OutAccountPlanID']; ?>', Amount: parseFloat('<? print $allowance_charge['Amount']; ?>'), VatPercent: parseFloat('<? print $allowance_charge['OutVatPercent']; ?>'), VatID: '<? print $allowance_charge['OutVatID']; ?>'};
<?
}
?>
// add a new invoice line, using ajax request
function newInvoiceLine(InvoiceID, CustomerAccountPlanID, LineNumber) {
  LineNumber = parseInt(LineNumber) + 1;
  var params = {
                InvoiceID: InvoiceID,
                action_invoice_linenew: 1
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var InvoiceLineID = $($.parseHTML(data)).filter("#line_id").text();
           var newInvoiceLineHTML='<tr id="invoiceline_fields_'+InvoiceLineID+'" class="invoiceline_fields"><td class="product_td"><? print str_replace("\n", '', $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>'placeholder_id', 'width'=>'35', 'tabindex'=> 0, 'class' => 'combobox product', 'required' => false, 'notChoosenText' => 'Velg produkt'))); ?></td> <td class="red"><input type="text" class="product" name="invoiceoutline.ProductName.'+InvoiceLineID+'" id="invoiceoutline.ProductName.'+InvoiceLineID+'" value="" size="20" tabindex="0" maxlength="80" onchange="validateBeforeSave();"> </td> <td align="center" class="red"><input type="text" name="invoiceoutline.QuantityDelivered.'+InvoiceLineID+'" id="invoiceoutline.QuantityDelivered.'+InvoiceLineID+'" value="0,00" size="8" tabindex="0" maxlength="8" class="number" onchange="updateInvoiceLineData(this, false);"> </td> <td class="red"><input type="text" name="invoiceoutline.UnitCustPrice.'+InvoiceLineID+'" id="invoiceoutline.UnitCustPrice.'+InvoiceLineID+'" value="0,00" size="15" tabindex="0" maxlength="15" class="number" onchange="updateInvoiceLineData(this, false);"> </td> <td id="invoiceoutline.VatPercent.'+InvoiceLineID+'">0,00%</td> <td align="right" id="invoiceoutline.VatAmount.'+InvoiceLineID+'">0,00</td> <td align="right" id="invoiceoutline.AmountExcludingVat.'+InvoiceLineID+'">0,00</td> <td><input type="submit" name="action_invoiceline_allowance_charge_new" id="action_invoiceline_allowance_charge_new" value="Ny linje rabatt/kostnad" size="20" tabindex="0" onclick="newInvoiceLineAllowanceCharge('+InvoiceID+', <? print ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0); ?>, '+InvoiceLineID+'); return false;" /> <input type="button" class="button" onclick="deleteInvoiceLine('+InvoiceID+', '+ (CustomerAccountPlanID ? CustomerAccountPlanID : 0) +', '+InvoiceLineID+'); return false;", value="Slett" /></td></tr><tr id="invoiceline_comment_'+InvoiceLineID+'"> <td colspan="8"><textarea name="invoiceoutline.Comment.'+InvoiceLineID+'" id="invoiceoutline.Comment.'+InvoiceLineID+'" cols="80" rows="1" tabindex="0"></textarea> <input type="hidden" name="'+LineNumber+'" id="'+LineNumber+'" value="'+InvoiceLineID+'"> </td></tr> <tr id="allowance_placeholder_'+InvoiceLineID+'"><td colspan="8"><hr/></td></tr>';
           newInvoiceLineHTML = newInvoiceLineHTML.replace(/placeholder_id/g, InvoiceLineID);
           $(newInvoiceLineHTML).insertBefore($('#placeholder'));
           $("#field_count").val(LineNumber);
           refreshComboboxOnPage();
           setComboboxOnChangeAction();
           validateBeforeSave();
           // console.log("Added invoice line "+InvoiceLineID);
           // since we always add an empty line there is no need to update
           // the total amounts here
         });
  return false;
}

// add a new invoice line allowance/charge, using ajax request
function newInvoiceLineAllowanceCharge(InvoiceID, CustomerAccountPlanID, InvoiceLineID) {
  if (is_hidden) showHideAllowanceCharge();
  var params = {
                InvoiceID: InvoiceID,
                action_invoiceline_allowance_charge_new: 1,
                InvoiceLineID: InvoiceLineID
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var InvoiceLineAllowanceChargeID = $($.parseHTML(data)).filter("#line_allowance_charge_id").text();
           var newInvoiceLineAllowanceChargeHTML='<tr class="allowance_charge line_invoice_allowancecharge_'+InvoiceLineID+'" id="invoiceline_allowancecharge_fields_'+InvoiceLineAllowanceChargeID+'"><td><? print str_replace("\n", '', $_lib['form3']->Generic_menu3(array('data' => array('1' => 'Kostnad', '0' => 'Rabatt'), 'table' => $db_table5, 'field' => 'ChargeIndicator', 'OnChange' => 'updateInvoiceLineAllowanceChargeData(this)', 'pk' => 'placeholder_id', 'value' => '0', 'tabindex' => '0'))); ?> <? print str_replace("\n", '', $_lib['form3']->Generic_menu3(array('data' => array('line' => 'Linje', 'price' => 'Pris'), 'table' => $db_table5, 'field' => 'AllowanceChargeType', 'OnChange' => 'updateInvoiceLineAllowanceChargeData(this)', 'pk' => 'placeholder_id', 'value' => 'line', 'tabindex' => '0'))); ?></td> <td><? print str_replace("\n", '', $_lib['form3']->text(array('table' => $db_table5, 'field' => 'AllowanceChargeReason', 'pk' => 'placeholder_id', 'width' => '20', 'maxlength' => '255', 'tabindex' => '0'))); ?></td> <td></td> <td id="<? print $db_table5; ?>.AmountPaddingForLineType.placeholder_id" colspan="3"></td> <td> <span style="display: none;" id="<? print "$db_table5.InvoiceLineID.placeholder_id"; ?>">'+InvoiceLineID+'</span> <? print str_replace("\n", '', $_lib['form3']->text(array('table' => $db_table5, 'field' => 'Amount', 'OnChange' => 'updateInvoiceLineAllowanceChargeData(this)', 'pk' => 'placeholder_id', 'value' => $_lib['format']->Amount(0), 'class' => 'number', 'width' => '15', 'tabindex' => '0'))); ?></td> <td id="<? print $db_table5; ?>.AmountPaddingForPriceType.placeholder_id" colspan="3" hidden></td> <td><input type="button" class="button" onclick="deleteInvoiceLineAllowanceCharge(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", "; ?>'+InvoiceLineAllowanceChargeID+', '+InvoiceLineID+'); return false;" value="Slett" /></td></tr>';
           newInvoiceLineAllowanceChargeHTML = newInvoiceLineAllowanceChargeHTML.replace(/placeholder_id/g, InvoiceLineAllowanceChargeID);
           $(newInvoiceLineAllowanceChargeHTML).insertBefore($('#allowance_placeholder_'+InvoiceLineID));
           validateBeforeSave();
           // console.log("Added line allowance/charge "+InvoiceLineAllowanceChargeID);
           // since we always add an empty allowance/charge to the line there is no need to update
           // the total amounts here
           first_time_array[InvoiceLineAllowanceChargeID] = true;
         });
  return false;
}

// add a new invoice allowance/charge, using ajax request
function newInvoiceAllowanceCharge(InvoiceID, CustomerAccountPlanID) {
  if (is_hidden) showHideAllowanceCharge();
  var params = {
                InvoiceID: InvoiceID,
                action_invoice_allowance_charge_new: 1
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var InvoiceAllowanceChargeID = $($.parseHTML(data)).filter("#allowance_charge_id").text();
           var newInvoiceAllowanceChargeHTML='<tr class="allowance_charge global_invoice_allowancecharge" id="invoice_allowancecharge_fields_'+InvoiceAllowanceChargeID+'"><td><? print str_replace("\n", '', $_lib['form3']->Generic_menu3(array('query' => "select AllowanceChargeID, CONCAT(IF(ChargeIndicator, 'Kostnad - ', 'Rabatt - '), Reason) from allowancecharge where Active = 1", 'table' => $db_table4, 'field' => 'AllowanceChargeID', 'width' => 40, 'value' => $acrow->AllowanceChargeID, 'tabindex' => '0', 'pk' => 'placeholder_id', 'OnChange' => 'updateInvoiceAllowanceChargeLineData(this, true)'))); print str_replace("\n", '', $_lib['form3']->hidden(array('table' => $db_table4, 'field' => 'ChargeIndicator', 'pk' => 'placeholder_id', 'value' => 0))); ?></td> <td> <? print str_replace("\n", '', $_lib['form3']->text(array('table' => $db_table4, 'field' => 'AllowanceChargeReason', 'pk' => 'placeholder_id', 'value' => '', 'width' => '20', 'maxlength' => '255', 'tabindex' => '0'))); ?></td> <td colspan="2"></td> <td> <? print '<span id="' . $db_table4 . '.VatPercent.' . 'placeholder_id" >' . $_lib['format']->Percent('0,00') . '</span>'; print str_replace("\n", '', $_lib['form3']->hidden(array('table' => $db_table4, 'field' => 'VatID', 'pk' => 'placeholder_id', 'value' => null))); ?></td> <td class="number"> <? print '<span id="' . $db_table4 . '.VatAmount.' . 'placeholder_id" >' . $_lib['format']->Amount(0) . '</span>'; ?></td> <td> <? print str_replace("\n", '', $_lib['form3']->text(array('table' => $db_table4, 'field' => 'Amount', 'OnChange' => 'updateInvoiceAllowanceChargeLineData(this)', 'class' => 'number', 'width' => '15', 'pk' => 'placeholder_id', 'value' => $_lib['format']->Amount(0), 'tabindex' => '0'))); ?></td> <td><input type="button" class="button" onclick="deleteInvoiceAllowanceCharge(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", "; ?>'+InvoiceAllowanceChargeID+'); return false;" value="Slett" /></td></tr>';
           newInvoiceAllowanceChargeHTML = newInvoiceAllowanceChargeHTML.replace(/placeholder_id/g, InvoiceAllowanceChargeID);
           $(newInvoiceAllowanceChargeHTML).insertBefore($('#allowance_placeholder'));
           validateBeforeSave();
           // console.log("Added allowance/charge "+InvoiceAllowanceChargeID);
           // since we always add an empty allowance/charge to invoice there is no need to update
           // the total amounts here
         });
  return false;
}

// delete an invoice allowance/charge, using ajax request
function deleteInvoiceAllowanceCharge(InvoiceID, CustomerAccountPlanID, InvoiceAllowanceChargeID) {
  if (is_hidden) showHideAllowanceCharge();
  var params = {
                InvoiceID: InvoiceID,
                InvoiceAllowanceChargeID: InvoiceAllowanceChargeID,
                action_invoice_allowance_charge_delete: 1
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var fields = $('#invoice_allowancecharge_fields_'+InvoiceAllowanceChargeID);
           fields.remove();
           // console.log("Removed invoice allowance/charge "+InvoiceAllowanceChargeID);
           // update total amounts for invoice
           updateInvoiceData();
         });
  return false;
}

// delete an invoice line allowance/charge, using ajax request
function deleteInvoiceLineAllowanceCharge(InvoiceID, CustomerAccountPlanID, InvoiceLineAllowanceChargeID, InvoiceLineID) {
  if (is_hidden) showHideAllowanceCharge();
  var params = {
                InvoiceID: InvoiceID,
                InvoiceLineAllowanceChargeID: InvoiceLineAllowanceChargeID,
                action_invoiceline_allowance_charge_delete: 1
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var fields = $('#invoiceline_allowancecharge_fields_'+InvoiceLineAllowanceChargeID);
           fields.remove();
           // console.log("Removed invoice line allowance/charge "+InvoiceLineAllowanceChargeID);
           // update line amounts
          var invoice_line_name = ['invoiceoutline', 'ProductID', InvoiceLineID];
          var invoice_line_product_id_element = document.getElementById(invoice_line_name.join('.'));
          updateInvoiceLineData(invoice_line_product_id_element, false);
         });
  return false;
}

// delete an invoice line, using ajax request
function deleteInvoiceLine(InvoiceID, CustomerAccountPlanID, LineID) {
  var params = {
                InvoiceID: InvoiceID,
                LineID: LineID,
                action_invoice_outlinedelete: 1
                };

  params['invoiceout_CustomerAccountPlanID_'+InvoiceID] = CustomerAccountPlanID;
  $.post('<? print $_lib['sess']->dispatchs; ?>t=invoice.ajax', params,
         function(data, status) {
           var fields = $('#invoiceline_fields_'+LineID);
           fields.remove();
           var comment = $('#invoiceline_comment_'+LineID);
           comment.remove();
           var allowances = $('.line_invoice_allowancecharge_'+LineID);
           allowances.remove();
           var allowances_placeholder = $('#allowance_placeholder_'+LineID);
           allowances_placeholder.remove();
           var allowances_header = $('#invoiceline_allowancecharge_header_'+LineID);
           allowances_header.remove();
           // console.log("Removed invoice line "+LineID);
           // update total amounts for invoice
           updateInvoiceData();
         });
  return false;
}

// goes through all invoice lines on page and calculates the total amounts
// and changes them on page so we have a realtime update of amounts as the
// invoice changes
function updateInvoiceData() {
  var number_of_invoice_lines = parseInt(document.getElementById('field_count').value);
  var vat_amount_sum = 0.0;
  var amount_line_extension_sum = 0.0;
  var amount_allowance_sum = 0.0;
  var amount_charge_sum = 0.0;
  var vat_amounts_per_percent = {};
  for(i = 1; i <= number_of_invoice_lines; i++) {
    var invoice_line_id = document.getElementById(i);
    if (invoice_line_id != null) {
      var invoice_line_vat_amount = toNumber(document.getElementById('<? print $db_table2 ?>.VatAmount.'+invoice_line_id.value).innerHTML);
      var percent = toNumber((document.getElementById('<? print $db_table2 ?>.VatPercent.'+invoice_line_id.value).innerHTML).replace(/%/, ''));
      vat_amounts_per_percent[percent] = vat_amounts_per_percent[percent] || 0.0;
      vat_amount_sum += invoice_line_vat_amount;
      vat_amounts_per_percent[percent] += invoice_line_vat_amount;
      amount_line_extension_sum += toNumber(document.getElementById('<? print $db_table2 ?>.AmountExcludingVat.'+invoice_line_id.value).innerHTML);
    }
  }
  var invoice_allowances_charges = document.getElementsByClassName('global_invoice_allowancecharge');

  document.getElementById('invoice_errors').innerHTML = "";
  var allowance_charge_errors = "";

  for(i = 0; i < invoice_allowances_charges.length; i++) {
    var allowance_charge_id = invoice_allowances_charges[i].id.split('_')[3];
    var register_allowance_charge_id = document.getElementById('<? print $db_table4 ?>.AllowanceChargeID.'+allowance_charge_id).value;
    var allowance_charge_vat_id = document.getElementById('<? print $db_table4 ?>.VatID.'+allowance_charge_id).value;
    var allowance_charge_vat_percent_string = document.getElementById('<? print $db_table4 ?>.VatPercent.'+allowance_charge_id).innerHTML;
    var allowance_charge_charge_indicator = document.getElementById('<? print $db_table4 ?>.ChargeIndicator.'+allowance_charge_id).value == 1;
    allowance_charge_vat_percent_string = allowance_charge_vat_percent_string.replace('%', '');
    var allowance_charge_vat_percent = toNumber(allowance_charge_vat_percent_string)/100.0;
    var allowance_charge_amount = toNumber(document.getElementById('<? print $db_table4 ?>.Amount.'+allowance_charge_id).value);
    allowance_charge_vat_amount = allowance_charge_amount * allowance_charge_vat_percent;
    vat_amount_sum += allowance_charge_vat_amount;
    vat_amounts_per_percent[allowance_charge_vat_percent*100] = vat_amounts_per_percent[allowance_charge_vat_percent*100] || 0.0;
    vat_amounts_per_percent[allowance_charge_vat_percent*100] += allowance_charge_vat_amount;
    if (allowance_charge_charge_indicator) {
      amount_charge_sum += allowance_charge_amount;
    } else {
      amount_allowance_sum += allowance_charge_amount;
    }

    if (!allowance_charge_vat_id && register_allowance_charge_id != 0) {
      allowance_charge_errors += "Feil utg&aring;ende konto valg f&oslash;r " + (allowance_charge_charge_indicator?"kostnad":"rabatt") + " <a href='<? print $_lib['sess']->dispatch . "t=allowancecharge.edit&AllowanceChargeID="; ?>" + register_allowance_charge_id + "'>" + register_allowance_charge_id + "</a><br/>";
    }

  }
  if (allowance_charge_errors !== "") {
    document.getElementById('invoice_errors').innerHTML += "<div class='warning'>" + allowance_charge_errors + "</div>";
  }
  var amount_excluding_vat_sum = amount_line_extension_sum + amount_charge_sum + amount_allowance_sum;
  var amount_including_vat_sum = amount_excluding_vat_sum + vat_amount_sum;
  var invoice_total_vat_element = document.getElementById('<? print $db_table ?>.TotalVat');
  invoice_total_vat_element.innerHTML = toAmountString(vat_amount_sum, 2);
  var charge_total_element = document.getElementById('<? print $db_table ?>.ChargeTotalAmount');
  charge_total_element.innerHTML = toAmountString(amount_charge_sum, 2);
  var allowance_total_element = document.getElementById('<? print $db_table ?>.AllowanceTotalAmount');
  allowance_total_element.innerHTML = toAmountString(amount_allowance_sum, 2);
  var invoice_total_cust_price_element = document.getElementById('<? print $db_table ?>.TotalCustPrice');
  invoice_total_cust_price_element.innerHTML = toAmountString(amount_including_vat_sum, 2);
  var invoice_amount_without_vat_sum_element = document.getElementById('<? print $db_table ?>.LineExtensionAmount');
  invoice_amount_without_vat_sum_element.innerHTML = toAmountString(amount_line_extension_sum, 2);
  var invoice_vat_amount_sum_element = document.getElementById('<? print $db_table ?>.TaxTotalTaxAmount');
  invoice_vat_amount_sum_element.innerHTML = toAmountString(vat_amount_sum, 2);
  var invoice_amount_without_vat_sum_element = document.getElementById('<? print $db_table ?>.TaxExclusiveAmount');
  invoice_amount_without_vat_sum_element.innerHTML = toAmountString(amount_excluding_vat_sum, 2);
  var invoice_amount_with_vat_sum_element = document.getElementById('<? print $db_table ?>.TaxInclusiveAmount');
  invoice_amount_with_vat_sum_element.innerHTML = toAmountString(amount_including_vat_sum, 2);

  validateBeforeSave();
  updateVatCategories(vat_amounts_per_percent);
}

// update vat categories data
function updateVatCategories(vat_amounts_per_percent) {
  // get percentages
  var keys = [];
  for(var key in vat_amounts_per_percent) {
    if (hasOwnProperty.call(vat_amounts_per_percent, key)) {
      keys.push(toNumber(key));
    }
  }

  // sort by percent
  keys.sort(function(a, b) {
    return a - b;
  });

  // remove all lines and print out new lines
  var vat_amount_sum = 0.0;
  $(".vat_amount_per_percent").remove();
  for(var i=0; i<keys.length; i++) {
    var percent = keys[i];
    var vat_category_amount = vat_amounts_per_percent[percent];
    vat_amount_sum += vat_category_amount;
    var newVatCategoryAmountLine='<tr class="vat_amount_per_percent"><td colspan="6"></td><td align="right">' + toAmountString(percent) + '%</td><td class="Amount" align="right">' + toAmountString(vat_category_amount) + '</td></tr>';
    $(newVatCategoryAmountLine).insertBefore($('#vat_category_placeholder'));
  }
  $('#vat_category_placeholder').find(".Amount")[0].innerHTML = toAmountString(vat_amount_sum);
}

// update data for the allowance/charge line if allowance/charge changes
function updateInvoiceAllowanceChargeLineData(element, update_amount) {
  update_amount = typeof update_amount !== 'undefined' ? update_amount : false;
  var id = element.id;
  var name = element.id.split('.');

  name[1] = 'AllowanceChargeID';
  var allowance_charge_id_element = document.getElementById(name.join('.'));
  var allowance_charge_id = allowance_charge_id_element.value;
  if (allowance_charge_id == '') {
    validateBeforeSave();
    return;
  }

  name[1] = 'ChargeIndicator';
  var charge_indicator_element = document.getElementById(name.join('.'));
  charge_indicator_element.value = allowances_charges[allowance_charge_id]['ChargeIndicator'];

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

  updateInvoiceData();
}

// calculate the line's allowances/charges
// should only count in the ones that have type 'line'
function calculateInvoiceLineAllowanceCharge(line_id) {
  var allowance_charge_lines_trs = document.getElementsByClassName('line_invoice_allowancecharge_'+line_id);
  var allowances_charges_sum = 0;
  for (i = 0; i < allowance_charge_lines_trs.length; i++) {
    var allowance_charge_id = allowance_charge_lines_trs[i].id.split('_')[3];

    var allowance_charge_name = ['invoicelineallowancecharge', 'AllowanceChargeType', allowance_charge_id];
    allowance_charge_type_element = document.getElementById(allowance_charge_name.join('.'));
    allowance_charge_type = allowance_charge_type_element.value;
    // console.log('type : ' + allowance_charge_type);
    if (allowance_charge_type !== 'line') {
      // console.log('not line allowance/charge, skip');
      // console.log('\n');
      continue;
    }

    allowance_charge_name[1] = 'Amount';
    allowance_charge_amount_element = document.getElementById(allowance_charge_name.join('.'));
    allowance_charge_amount = toNumber(allowance_charge_amount_element.value);

    allowances_charges_sum += allowance_charge_amount;
  }
  // console.log(allowances_charges_sum);
  return allowances_charges_sum;
}

// update data for the line's allowance/charge if anything of note changes
// should only format the amount correctly and move the amount field to
// the correct place depending if it is a line/price allowance/charge
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

  name[1] = 'AllowanceChargeType';
  allowance_charge_type_element = document.getElementById(name.join('.'));
  allowance_charge_type = allowance_charge_type_element.value;

  name[1] = 'AmountPaddingForLineType';
  var amount_padding_for_line_type_element = document.getElementById(name.join('.'));
  name[1] = 'AmountPaddingForPriceType';
  var amount_padding_for_price_type_element = document.getElementById(name.join('.'));
  if (allowance_charge_type == 'line') {
    amount_padding_for_line_type_element.hidden = false;
    amount_padding_for_price_type_element.hidden = true;
  } else {
    amount_padding_for_price_type_element.hidden = false;
    amount_padding_for_line_type_element.hidden = true;
  }

  name[1] = 'InvoiceLineID';
  var invoice_line_id_element = document.getElementById(name.join('.'));
  var invoice_line_id = parseInt(invoice_line_id_element.innerHTML);

  var invoice_line_name = ['invoiceoutline', 'ProductID', invoice_line_id];
  var invoice_line_product_id_element = document.getElementById(invoice_line_name.join('.'));
  updateInvoiceLineData(invoice_line_product_id_element, false);
}

// update data for the line if the product/quantity/price changes
// should only change the price if the product changes
function updateInvoiceLineData(element, update_amount) {
  update_amount = typeof update_amount !== 'undefined' ? update_amount : true;
  var id = element.id;
  var name = element.id.split('.');
  var line_id = name[2];
  // console.log(line_id);
  var sum_allowances_charges = calculateInvoiceLineAllowanceCharge(line_id);

  name[1] = 'ProductID';
  var product_id_element = document.getElementById(name.join('.'));
  var product_id = product_id_element.value;
  if (product_id == '') return;

  name[1] = 'ProductName';
  var product_name_element = document.getElementById(name.join('.'));
  if (update_amount) {
    product_name_element.value = products[product_id]['ProductName'];
  }

  name[1] = 'QuantityDelivered';
  var quantity_delivered_element = document.getElementById(name.join('.'));
  var quantity_delivered = toNumber(quantity_delivered_element.value);
  quantity_delivered_element.value = toAmountString(quantity_delivered, 2);

  name[1] = 'UnitCustPrice';
  var unit_cust_price_element = document.getElementById(name.join('.'));
  var unit_cust_price = 0;
  if (update_amount) {
    unit_cust_price = products[product_id]['UnitCustPrice'];
    unit_cust_price_element.value = toAmountString(unit_cust_price, 2);
  } else {
    unit_cust_price = toNumber(unit_cust_price_element.value);
    unit_cust_price_element.value = toAmountString(unit_cust_price, 2);
  }

  name[1] = 'VatPercent';
  var vat_percent_element = document.getElementById(name.join('.'));
  var vat_percent = products[product_id]['VatPercent'];
  vat_percent_element.innerHTML = toAmountString(vat_percent, 2) + "%";

  name[1] = 'VatAmount';
  var vat_amount_element = document.getElementById(name.join('.'));
  var vat_amount = (vat_percent/100.0) * (unit_cust_price * quantity_delivered + sum_allowances_charges);
  vat_amount_element.innerHTML = toAmountString(vat_amount, 2);

  name[1] = 'AmountExcludingVat';
  var amount_excluding_vat_element = document.getElementById(name.join('.'));
  var amount_excluding_vat = unit_cust_price * quantity_delivered + sum_allowances_charges;
  amount_excluding_vat_element.innerHTML = toAmountString(amount_excluding_vat, 2);
  updateInvoiceData();
}

// hide/show all allowance/charge elements (ones which belong to allowance_charge class)
function showHideAllowanceCharge() {
  is_hidden = !is_hidden;
  var allowance_charge_elements = document.getElementsByClassName("allowance_charge");
  for(var i = 0; i < allowance_charge_elements.length; i++) {
    allowance_charge_elements[i].hidden = is_hidden;
  }
}

// set/reset onchange action for all comboboxes on page
function setComboboxOnChangeAction() {
  $( "select.product" ).change(function( event ) {
    updateInvoiceLineData(this);
  });
}

// when new selects are added of class combobox
// this needs to be run in order to display them correctly
function refreshComboboxOnPage() {
  $(".combobox" ).combobox();
}

function validateBeforeSave() {
  var all_valid = true;
  var allowance_charges = $(".global_invoice_allowancecharge select");
  all_valid = markRed(allowance_charges) && all_valid;

  var invoice_lines = $(".invoiceline_fields select.product");
  all_valid = markRed(invoice_lines) && all_valid;

  var number_inputs = $("input.number");
  all_valid = markRed(number_inputs) && all_valid;

  var product_names = $("input.product");
  all_valid = markRed(product_names) && all_valid;

  var invoice_line_products = $("select.product");
  for(var i=0; i<invoice_line_products.length; i++) {
    var product = $(invoice_line_products[i]);
    var product_number = product.val();

    if (product_number) {
      var valid = products[product_number]["AccountPlanID"] != "";
      if (!valid) product.parent("td").addClass("red");
      else product.parent("td").removeClass("red");
      // all_valid = valid && all_valid; // do not disable save if no account plan selected for product
    }
  }

  var allowance_charge_errors = document.getElementById('invoice_errors').innerHTML;
  all_valid = (allowance_charge_errors.trim() == "") && all_valid;

  enableOrDisable(all_valid, 'action_invoice_update');
}

function markRed(elements) {
  var all_valid = true;
  for(var i=0; i<elements.length; i++) {
    var element = $(elements[i]);
    if(element.val() == "" || element.val() == "0" || element.val() == "0,00") {
      all_valid = false;
      element.parent("td").addClass("red");
    } else {
      element.parent("td").removeClass("red");
    }
  }
  return all_valid;
}

$(document).ready(function() {
  setComboboxOnChangeAction();
  validateBeforeSave();
});
</script>

<style type="text/css">
  .red {
    background-color: red;
  }
  .red select {
    background-color: white;
  }
  .product_td {
    width: 180px;
  }
</style>
</head>

<body>
<? includeinc('top') ?>
<? includeinc('left') ?>
<? includeinc('javascript') ?>

<?
$message = $_lib['message']->get();
if(strstr($message, "Success")) $class = 'user';
else $class = 'warning';
if($message) { print "<div class='$class'>$message</div><br>"; }
?>
<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="InvoiceID" value="<? print $InvoiceID ?>">
<input type="hidden" name="inline" value="edit">

<table class="lodo_data">
<thead>
    <tr>
        <td>Fakturanummer</td>
        <td>
          <? 
            $vouchers_exist = $_lib['db']->db_fetch_assoc($_lib['db']->db_query("SELECT count(*) as count FROM voucher WHERE JournalID = $InvoiceID AND VoucherType = 'S' AND Active = 1;"));
            $vouchers_exist = $vouchers_exist['count'];
            if($vouchers_exist) {
              print "<a href='".$_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=".$InvoiceID."&type=salecash_in'>".$InvoiceID."</a>";
            } else {
              print $InvoiceID;
            }
          ?>
        </td>
        <td>KID:</td>
        <td><? print $row->KID ?></td>
    </tr>
    <tr>
        <td><b>Avsender</b></td>
        <td><? print $row->SName ?></td>
        <td><b>Mottaker</b></td>
        <td><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'CustomerAccountPlanID', 'pk'=>$InvoiceID, 'value'=>$row->CustomerAccountPlanID, 'type' => array(0 => customer), 'tabindex' => $tabindex++, 'class' => 'combobox', 'onlyonce'=> true)) ?></td>
    </tr>
    <tr>
        <td>Adresse</a></td>
        <td><? print $row->SAddress ?></td>
        <? if( $row->IAddress) { ?>
          <td>Adresse</td>
          <td><? print $row->IAddress ?></td>
        <? } else { ?>
          <td>Postboks</td>
          <td><? print $row->IPoBox ?> <? print $row->IPoBoxCity ?></td>
        <? } ?>
    </tr>
    <tr>
        <td>Postnr/Poststed</td>
        <td><? print $row->SZipCode." ".$row->SCity ?></td>
        <? if( $row->IAddress) { ?>
          <td>Postnr/Poststed</td>
          <td><? print $row->IZipCode." ".$row->ICity ?></td>
        <? } else { ?>
          <td>Postnr/Poststed</td>
          <td><? print $row->IPoBoxZipCode ?> <? print $row->IPoBoxZipCodeCity ?></td>
           <? } ?>
    </tr>
    <tr>
        <td>Land</td>
        <td><? print $_lib['format']->codeToCountry($row->SCountryCode) ?></td>
        <td>Land</td>
        <td><? print $_lib['format']->codeToCountry($row->ICountryCode) ?></td>
    </tr>
    <tr>
        <td>Tlf nr</td>
        <td><? print $row->SPhone ?></td>
        <td>Tlf nr</td>
        <td><? print $row->Phone ?></td>
    </tr>
    </tr>
        <tr>
        <td>Mobil nr</td>
        <td><? print $row->SMobile ?></td>
        <td>Mobil nr</td>
        <td><? print $row->IMobile ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><? print $row->SEmail ?></td>
        <td>Email</td>
        <td><? print $row->IEmail ?></td>
    </tr>

    <tr>
        <td>Web</td>
        <td><? print $row->SWeb ?></td>
        <td>Web</td>
        <td><? print $row->IWeb ?></td>
    </tr>


    <tr>
        <td>Konto nr</td>
        <td><? print $row->SBankAccount ?></td>
        <td>Konto nr</td>
        <td><? print $row->BankAccount ?></td>
    </tr>
    <tr>
        <td>Foretaksregisteret</td>
        <td><? print $row->SOrgNo ?></td>
        <td>Foretaksregisteret</td>
        <?
          $firma_id_missing = false;
          if (!$row->IOrgNo) {
            includelogic("accountplan/scheme");
            $schemeControl = new lodo_accountplan_scheme($row->CustomerAccountPlanID);
            $first_firma_id = $schemeControl->getFirstFirmaID();
            if (!$first_firma_id) {
              $firma_id_missing = true;
            } else {
              $firma_id = $first_firma_id['type'] . " " . $first_firma_id['value'];
            }
          } else {
            $firma_id = $row->IOrgNo;
          }
        ?>
        <td><? print $firma_id ?></td>
    </tr>
    <tr>
        <td><?php if (!empty($row->SVatNo)) echo 'MVA reg' ?></td>
        <td><? if (!empty($row->SVatNo)) print $row->SVatNo ?></td>
        <td><?php if (!empty($row->IVatNo)) echo 'MVA reg' ?></td>
        <td><? if (!empty($row->IVatNo)) print $row->IVatNo ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="2"><b>Leveringsadresse</b></td>
    </tr>

    <tr>
        <td colspan="2"></td>
        <td>Adresse</td>
        <td><? print $_lib['form3']->text(array('field'=>'DAddress', 'value'=>$row->DAddress, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Postnummer</td>
        <td><? print $_lib['form3']->text(array('field'=>'DZipCode', 'value'=>$row->DZipCode, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Poststed</td>
        <td><? print $_lib['form3']->text(array('field'=>'DCity', 'value'=>$row->DCity, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Land</td>
        <td><? print $_lib['form3']->Country_menu3(array('field'=>'DCountryCode', 'value'=>(($row->DCountryCode != "") ? $row->DCountryCode : "NO"), 'required'=>false, 'tabindex' => $tabindex++)); ?></td>
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
<option value="<? echo $currency->CurrencyISO; ?>" <?php if ($row->CurrencyID == $currency->CurrencyISO) echo 'selected'; ?>><? echo $currency->CurrencyISO; ?></option>
<?
}
?>
      </select>
</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>Fakturadato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceDate', 'pk'=>$InvoiceID, 'value'=>substr($row->InvoiceDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++, 'OnKeyUp' => "enableOrDisable(validDate(this.value), 'action_invoice_update');")) ?></td>
      <td>Forfallsdato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DueDate', 'pk'=>$InvoiceID, 'value'=>substr($row->DueDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Fakturaperiode</td>
        <td>
        <?
        if($accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel'))) {
            print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'Period', 'pk'=>$InvoiceID, 'value' => $row->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => ''));
        } else {
            print $row->Period;
        }
        ?>
        </td>
        <? $print_date_value = ($row_print) ? $row_print->InvoicePrintDate : "0000-00-00"; ?>
        <td>Utskriftsdato</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'InvoicePrintDate', 'pk'=>$InvoiceID, 'value'=>substr($print_date_value, 0, 10), 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <? if($row->Note){ ?>
    <tr>
      <td>Merk</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Note', 'pk'=>$InvoiceID, 'value'=>$row->Note, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <? } ?>
    <tr>
      <td>Total bel&oslash;p</td>
      <td id="invoiceout.TotalCustPrice"><? print $_lib['format']->Amount($row->TotalCustPrice) ?></td>
      <td>MVA</td>
      <td id="invoiceout.TotalVat"><? print $_lib['format']->Amount($row->TotalVat) ?></td>
    </tr>

    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefCustomer', 'pk'=>$InvoiceID, 'value'=>$row->RefCustomer, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Deres ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefInternal', 'pk'=>$InvoiceID, 'value'=>$row->RefInternal, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$InvoiceID,  'value' =>  $row->ProjectID)) ?></td>
      <td>Prosjekt</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProjectNameCustomer', 'pk'=>$InvoiceID, 'value'=>$row->ProjectNameCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'pk'=>$InvoiceID, 'value' => $row->DepartmentID)); ?></td>
      <td>Avdeling</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DepartmentCustomer', 'pk'=>$InvoiceID, 'value'=>$row->DepartmentCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <? if($row->DeliveryCondition){ ?>
        <td>Leveringsbetingelse</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$InvoiceID, 'value'=>$row->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <? } else{ ?>
        <td></td>
        <td></td>
      <? } ?>
      <td>Betalingsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'PaymentCondition', 'pk'=>$InvoiceID, 'value'=>$row->PaymentCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td valign="top">Kommentar til kunde</td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentCustomer', 'pk'=>$InvoiceID, 'value'=>$row->CommentCustomer, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
    <tr>
      <td valign="top">
        Kommentar (intern)<br />
      </td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentInternal', 'pk'=>$InvoiceID, 'value'=>$row->CommentInternal, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
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

        if(is_null($allowances_charges[$acrow->AllowanceChargeID]["OutVatID"]) && $acrow->AllowanceChargeID != 0) {
          $ac_errors[] = "Feil utg&aring;ende konto valg f&oslash;r ". ($allowances_charges[$acrow->AllowanceChargeID]["charge_indicator"]?"kostnad":"rabatt") ." <a href='". $_lib['sess']->dispatch ."t=allowancecharge.edit&AllowanceChargeID=". $acrow->AllowanceChargeID ."'>". $acrow->AllowanceChargeID ."</a>";
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
<table border="0" cellspacing="0" width="875">
<thead>
  <tr>
    <td>ProduktNr</td>
    <td>Produkt navn</td>
    <td>Antall</td>
    <td>Enhetspris</td>
    <td>MVA</td>
    <td>MVA bel&oslash;p</td>
    <td style="width: 85px;">Bel&oslash;p U/MVA</td>
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

    $query_invoiceline_allowance_charge = "select * from $db_table5 where InvoiceLineID = '$LineID' and InvoiceType = 'out'";
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

    $sumline = round( $row2->QuantityDelivered * $row2->UnitCustPrice + $charges - $allowances, 2);
    $vatline = round(($row2->QuantityDelivered * $row2->UnitCustPrice + $charges - $allowances) * ($row2->Vat/100), 2);

    $sumlines += $sumline;
    $vatlines += $vatline;
    $tax_categories[$row2->Vat]->TaxableAmount += $sumline;
    $tax_categories[$row2->Vat]->TaxAmount     += $vatline;
    ?>
    <tr id='invoiceline_fields_<? print $LineID; ?>' class='invoiceline_fields'>
        <td class="product_td"><? print $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>$LineID, 'value'=>$row2->ProductID, 'width'=>'35', 'tabindex'=>$tabindex++, 'class' => 'combobox product', 'required' => false, 'notChoosenText' => 'Velg produkt')) ?></td>
        <td class='<? if (empty($row2->ProductName)) echo "red"; ?>'><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName', 'pk'=>$LineID, 'value'=>$row2->ProductName, 'width'=>'20', 'maxlength' => 80, 'tabindex'=>$tabindex++, 'class'=>"product", 'OnChange'=>"validateBeforeSave();")) ?></td>
        <td align="center" class='<? if ($row2->QuantityDelivered == 0) echo "red"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount($row2->QuantityDelivered), 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number', 'OnChange' => 'updateInvoiceLineData(this, false);')) ?></td>
        <td class='<? if ($row2->UnitCustPrice == 0) echo "red"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number', 'OnChange' => 'updateInvoiceLineData(this, false);')) ?></td>
        <td id="<? print $db_table2 . ".VatPercent." . $LineID; ?>"><? print $_lib['format']->Amount($row2->Vat) ?>%<? #print $_lib['form3']->vat_menu3(array('percent2'=>'1', 'table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'SaleMenu'=>'1', 'date' => $row->InvoiceDate)) ?></td>
        <td align="right" id="<? print $db_table2 . ".VatAmount." . $LineID; ?>"><? print $_lib['format']->Amount($vatline) ?></td>
        <td align="right" style="width: 85px;" id="<? print $db_table2 . ".AmountExcludingVat." . $LineID; ?>"><? print $_lib['format']->Amount($sumline) ?></td>
        <td>
        <? if(
               (!$row->Locked &&
                   $_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))
               ||
                ($_lib['sess']->get_person('AccessLevel') >= 4 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))) { ?>
                <input type="submit" name="action_invoiceline_allowance_charge_new" id="action_invoiceline_allowance_charge_new" value="Ny linje rabatt/kostnad" size="20" tabindex="<? print $tabindex++; ?>" onclick="newInvoiceLineAllowanceCharge(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", " . $LineID; ?>); return false;" />
        <input type="button" class="button" onclick="deleteInvoiceLine(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", " . $LineID; ?>); return false;" value="Slett" />
        <? } ?>
    <tr id='invoiceline_comment_<? print $LineID; ?>'>
        <td colspan="8"><? print $_lib['form3']->textarea(array('table'=>$db_table2, 'field'=>'Comment', 'pk'=>$LineID, 'value'=>$row2->Comment, 'tabindex'=>$tabindex++, 'min_height'=>'1', 'height'=>'1', 'width'=>'80')) ?>
    <?
    $rowCounter++;
    print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$LineID));
    if (count($line_allowancecharges) > 0) {
      foreach($line_allowancecharges as $acrow) {
    ?>
    <tr class="allowance_charge line_invoice_allowancecharge_<? print $LineID; ?>" id="invoiceline_allowancecharge_fields_<? print $acrow->InvoiceLineAllowanceChargeID; ?>">
      <td>
        <?
          print $_lib['form3']->Generic_menu3(array(
            'data'     => array('1' => 'Kostnad', '0' => 'Rabatt'),
            'table'    => $db_table5,
            'field'    => 'ChargeIndicator',
            'OnChange' => 'updateInvoiceLineAllowanceChargeData(this);',
            'value'    => $acrow->ChargeIndicator,
            'tabindex' => $tabindex++,
            'pk'       => $acrow->InvoiceLineAllowanceChargeID));
          print $_lib['form3']->Generic_menu3(array(
            'data'     => array('line' => 'Linje', 'price' => 'Pris'),
            'table'    => $db_table5,
            'OnChange' => 'updateInvoiceLineAllowanceChargeData(this);',
            'field'    => 'AllowanceChargeType',
            'value'    => $acrow->AllowanceChargeType,
            'tabindex' => $tabindex++,
            'pk'       => $acrow->InvoiceLineAllowanceChargeID));
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'     => $db_table5,
            'field'     => 'AllowanceChargeReason',
            'pk'        => $acrow->InvoiceLineAllowanceChargeID,
            'value'     => $acrow->AllowanceChargeReason,
            'width'     => '20',
            'maxlength' => '255',
            'tabindex'  => $tabindex++));
        ?>
      </td>
      <td></td>
      <td id="<? print "$db_table5.AmountPaddingForLineType.$acrow->InvoiceLineAllowanceChargeID"; ?>" colspan="3" <? if ($acrow->AllowanceChargeType == 'price') print 'hidden'; ?>></td>
      <td>
        <span style="display: none;" id="<? print "$db_table5.InvoiceLineID.$acrow->InvoiceLineAllowanceChargeID"; ?>"><? print $LineID; ?></span>
        <?
          print $_lib['form3']->text(array(
            'table'    => $db_table5,
            'field'    => 'Amount',
            'OnChange' => 'updateInvoiceLineAllowanceChargeData(this);',
            'pk'       => $acrow->InvoiceLineAllowanceChargeID,
            'class'    => 'number',
            'width'    => '15',
            'value'    => $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount),
            'tabindex' => $tabindex++));
        ?>
      </td>
      <td id="<? print "$db_table5.AmountPaddingForPriceType.$acrow->InvoiceLineAllowanceChargeID"; ?>" colspan="3" <? if ($acrow->AllowanceChargeType == 'line') print 'hidden'; ?>></td>
      <td>
        <?
          if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel'))) {
            if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
        ?>
              <input type="button" class="button" onclick="deleteInvoiceLineAllowanceCharge(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", " . $acrow->InvoiceLineAllowanceChargeID . ", " . $LineID; ?>); return false;" value="Slett" />
        <?
            }
          }
        ?>
      </td>
    </tr>
  <?
      }
    }
  ?>
    <tr id="allowance_placeholder_<? print $LineID; ?>"><td colspan="8"><hr/></td></tr>
<?
}
?>
    <tr id="placeholder"></tr>
<?
    if (!empty($invoice_allowances_charges)) {
      foreach ($invoice_allowances_charges as $acrow) {
?>
    <tr class="allowance_charge global_invoice_allowancecharge" id="invoice_allowancecharge_fields_<? print $acrow->InvoiceAllowanceChargeID; ?>">
      <td>
        <?
          print $_lib['form3']->Generic_menu3(array(
            'query'    => "select AllowanceChargeID, CONCAT(IF(ChargeIndicator, 'Kostnad - ', 'Rabatt - '), Reason) from allowancecharge where Active = 1",
            'table'    => $db_table4,
            'field'    => 'AllowanceChargeID',
            'width'    => 40,
            'value'    => $acrow->AllowanceChargeID,
            'tabindex' => $tabindex++,
            'OnChange' => 'updateInvoiceAllowanceChargeLineData(this, true)',
            'pk'       => $acrow->InvoiceAllowanceChargeID));
          print $_lib['form3']->hidden(array(
            'table'    => $db_table4,
            'field'    => 'ChargeIndicator',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $acrow->ChargeIndicator));
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'     => $db_table4,
            'field'     => 'AllowanceChargeReason',
            'pk'        => $acrow->InvoiceAllowanceChargeID,
            'value'     => $acrow->AllowanceChargeReason,
            'width'     => '20',
            'maxlength' => '255',
            'tabindex'  => $tabindex++));
        ?>
      </td>
      <td colspan="2"></td>
      <td>
        <?
          print '<span id="' . $db_table4 . '.VatPercent.' . $acrow->InvoiceAllowanceChargeID . '" >' . $_lib['format']->Percent($acrow->VatPercent) . '</span>';
          print $_lib['form3']->hidden(array(
            'table'    => $db_table4,
            'field'    => 'VatID',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $acrow->VatID));
        ?>
      </td>
      <td class="number">
        <?
          print '<span id="' . $db_table4 . '.VatAmount.' . $acrow->InvoiceAllowanceChargeID . '" >' . $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount * ($acrow->VatPercent / 100.0)) . '</span>';
        ?>
      </td>
      <td>
        <?
          print $_lib['form3']->text(array(
            'table'    => $db_table4,
            'field'    => 'Amount',
            'OnChange' => 'updateInvoiceAllowanceChargeLineData(this)',
            'class'    => 'number',
            'width'    => '15',
            'pk'       => $acrow->InvoiceAllowanceChargeID,
            'value'    => $_lib['format']->Amount(($acrow->ChargeIndicator == 1?1:-1)*$acrow->Amount),
            'tabindex' => $tabindex++));
        ?>
      </td>
      <td>
        <?
          if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel'))) {
            if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
        ?>
              <input type="button" class="button" onclick="deleteInvoiceAllowanceCharge(<? print $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . ", " . $acrow->InvoiceAllowanceChargeID; ?>); return false;" value="Slett" />
        <?
            }
          }
        ?>
      </td>
    </tr>
<?
      }
    }
?>
    <tr class="allowance_charge" id="allowance_placeholder"><td colspan="8"><hr/></td></tr>
    <tr height="20">
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Linje sum</td>
        <td id="invoiceout.LineExtensionAmount" align="right"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Rabatt sum</td>
        <td id="invoiceout.AllowanceTotalAmount" align="right"><? print $_lib['format']->Amount($sum_allowance) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Kostnad sum</td>
        <td id="invoiceout.ChargeTotalAmount" align="right"><? print $_lib['format']->Amount($sum_charge) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Totalt U/MVA</td>
        <td id="invoiceout.TaxExclusiveAmount" align="right"><? print $_lib['format']->Amount($sumlines + $sum_allowance + $sum_charge) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Total MVA</td>
        <td id="invoiceout.TaxTotalTaxAmount" align="right"><? print $_lib['format']->Amount($vatlines + $vat_allowance + $vat_charge) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Totalt M/MVA</td>
        <td id="invoiceout.TaxInclusiveAmount" align="right"><? print $_lib['format']->Amount($vatlines + $vat_allowance + $vat_charge + $sumlines + $sum_allowance + $sum_charge) ?></td>
        <?
            print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>'field_count', 'value'=>$rowCounter));
        ?>
    </tr>
    <tr>
        <td colspan="8"><br><hr></td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td class="number">Prosent</td>
        <td class="number">Bel&oslash;p</td>
    </tr>
<?
    $sum_tax = 0;
    ksort($tax_categories);
    foreach($tax_categories as $percent => $tax_category) {
      $sum_tax += $tax_category->TaxAmount;
?>
    <tr class="vat_amount_per_percent">
        <td colspan="6"></td>
        <td align="right"><? print $_lib['format']->Percent($percent); ?></td>
        <td class="Amount" align="right"><? print $_lib['format']->Amount($tax_category->TaxAmount); ?></td>
    </tr>
<?
    }
?>
    <tr id="vat_category_placeholder">
        <td colspan="6"></td>
        <td align="right">Total MVA</td>
        <td class="Amount" align="right"><? print $_lib['format']->Amount($sum_tax); ?></td>
    </tr>
    <tr>
        <td colspan="8"><br><hr>
    </tr>
</tbody>

<tfoot>
    <tr>
        <td colspan="8">
        <?
	    if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')))
            {
                if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_linenew', 'tabindex' => $tabindex++, 'value'=>'Ny fakturalinje (N)', 'accesskey'=>'N', 'OnClick'=>"newInvoiceLine(".$InvoiceID.", ". ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) .", $('#field_count').val()); return false;"));
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_update', 'tabindex' => $tabindex++, 'value'=>'Lagre faktura (S)', 'accesskey'=>'S', 'OnClick'=>"this.form.action += '#bottomPage'"));
                }
                else {
                    print "Periode stengt";
                }
	    }
            else {
                if($inline == 'edit')
                    print "Du har ikke tilgang til &aring; lagre faktura";
                else
                    print "-";
            }
        ?>
        </td>
    </tr>
    <tr>
      <td colspan="8">
        <?
        if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel'))) {
          if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
            print $_lib['form3']->Input(array(
              'type'     => 'submit',
              'name'     => 'action_invoice_allowance_charge_new',
              'tabindex' => $tabindex++,
              'value'    => 'Ny rabatt-/kostnadslinje',
              'OnClick'  => "newInvoiceAllowanceCharge(" . $InvoiceID . ", " . ($row->CustomerAccountPlanID ? $row->CustomerAccountPlanID : 0) . "); return false;"));
          }
        }
        ?>
        <?
          print $_lib['form3']->Input(array(
            'type'     => 'submit',
            'name'     => 'action_invoice_allowance_show_hide',
            'tabindex' => $tabindex++,
            'value'    => 'Vis/Skjul rabatt/kostand info',
            'OnClick'  => "showHideAllowanceCharge(); return false;"));
        ?>
      </td>
    </tr>
    <tr>
        <td colspan="8">
        <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_save_internal', 'tabindex' => $tabindex++, 'value'=>'Lagre internkommentar')) ?>

        <?
        if($_lib['sess']->get_person('AccessLevel') >= 2)
        {
            print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_newonthis', 'tabindex' => $tabindex++, 'value'=>'Ny faktura ut i fra denne', 'confirm' => 'Er du sikker p&aring; at du vil lage ny ut i fra denne?'));
        }
        ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
        <?
	if(!$row->Locked) {
		print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_lock', 'tabindex' => $tabindex++, 'value'=>'L&aring;s (L)', 'accesskey'=>'L', 'confirm'=>'Er du sikker p&aring; at du vil l&aring;se fakturaen?', 'disabled' => !$ready_to_send_to_fb));
	};

        ?>
        </td>


        <td colspan="6" align="right">
        <?

        if($_lib['sess']->get_person('FakturabankExportInvoiceAccess')) {
            echo "Firma ID: ".  $firma_id . "<br />";

            if(!$firma_id_missing)
                print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_fakturabanksend', 'tabindex' => $tabindex++,'value'=>'Fakturabank (F)', 'accesskey'=>'F', 'disabled' => !$ready_to_send_to_fb));
            else
                print "Mangler firma id ";
        }

        if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
            if($_lib['sess']->get_person('AccessLevel') >= 4 && $inline == 'edit') {
                if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')))
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_delete', 'tabindex' => $tabindex++, 'value'=>'Slett faktura (D)', 'accesskey'=>'D', 'confirm' => 'Er du sikker p&aring; at du vil slette denne fakturaen?'));
            }
        }
        else {
            print "Faktura l&aring;st";
        }

        if (!empty($error_messages)) {
        ?>
        </td>
    <tr>
      <td colspan="8">
        <div class='warning'>
          <? foreach($error_messages as $error_message) print $error_message . '<br/>'; ?>
        <div>
      </td>
    </tr>
<?
        }
?>
</form>
    <tr>
      <td colspan="8"></td>
    </tr>
        <?
          if ($_lib['sess']->get_person('AccessLevel') > 1) {
            if ($row->UpdatedByPersonID) echo "<tr><td colspan='8'>" . $row->UpdatedAt . " lagret av " . $_lib['format']->PersonIDToName($row->UpdatedByPersonID) . "</td></tr>";
            if ($row->Locked) {
              if ($row->LockedBy) echo "<tr><td colspan='8'>" . $row->LockedAt . " l&aring;st av " . $_lib['format']->PersonIDToName($row->LockedBy) . "</td></tr>";
              else echo "<tr><td>L&aring;st: Ja </td></tr>";
            }
            if ($row->FakturabankPersonID) echo "<tr><td colspan='8'>" . $row->FakturabankDateTime . " fakturaBank " . $_lib['format']->PersonIDToName($row->FakturabankPersonID) . "</td></tr>";
          }
        ?>
    <tr>
        <td colspan="8" align="right">
        <form name="skriv_ut" action="<? print $_lib['sess']->dispatch ?>t=invoice.print&InvoiceID=<? print $InvoiceID ?>&amp;inline=edit" method="post" target="_new">
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift')) ?>
        </form>
        <? print $_lib['form3']->Input(array('type'=>'button', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift PDF', 'OnClick'=>"var win = window.open('". $_lib['sess']->dispatch ."t=invoice.print2&InvoiceID=". $row->InvoiceID ."&inline=show', '_new');")) ?>
    </tr>
    <tr>
        <td colspan="8" align="right">
        <form name="send_mail" action="<? print $_lib['sess']->dispatch ?>t=invoice.sendmail&InvoiceID=<? print $InvoiceID ?>" method="post">
          <?php
            $rowcomapny = $_lib['storage']->get_row(array('query' => "SELECT * FROM `company` WHERE CompanyID=" . $row->FromCompanyID));
          ?>
            <br />
            <input type="text" value="<? print $row->IEmail; ?>" name="email_recipient" />
            <input type="hidden" value="<?=  $rowcomapny->CopyFakturaMail ?>" name="send_mail_copy_mail" />
            <?
              $send_copy_select = "SELECT 1 AS value, 'send kopi til firma' AS text UNION SELECT 0 AS value, 'ikke send kopi til firma' AS text";
              print $_lib['form3']->_MakeSelect(array('query' => $send_copy_select, 'name' => 'send_mail_copy', 'num_letters' => 25, 'value' => 1, 'required' => 1));
              if($row->Locked == "1") {
                print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_send_email2', 'tabindex' => $tabindex++, 'value'=>'Send email', 'disabled' => !$ready_to_send_to_fb));
              } else {
                print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_send_email2_lock', 'tabindex' => $tabindex++, 'value'=>'L&aring;s og send email', 'disabled' => !$ready_to_send_to_fb, 'confirm'=>'Er du sikker p&aring; at du vil l&aring;se fakturaen?'));
              }
            ?>
        </form>
    </tr>
   <? if(!$row->Locked) { ?>
     <tr><td colspan="8">L&aring;st:  Nei</td></tr>
   <? } ?>
</tfoot>
</table>
<a name="bottomPage"></a>
</body>
</html>
<? unset($_SESSION['oauth_invoice_sent']); ?>
