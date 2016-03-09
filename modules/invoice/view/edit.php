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

$VoucherType='S';

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";
$db_table3 = "invoiceoutprint";

includelogic('exchange/exchange');

includelogic('accounting/accounting');


$accounting = new accounting();
require_once "record.inc";

if (isset($_SESSION['oauth_invoice_error'])) {
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
<?
$product_query = 'SELECT ProductID, ProductName, UnitCustPrice, AccountPlanID
                  FROM product
                  WHERE Active = 1';
$product_result = $_lib['db']->db_query($product_query);
while($product = $_lib['db']->db_fetch_assoc($product_result)) {
  $accountplan = $accounting->get_accountplan_object($product['AccountPlanID']);
  $vat = $accounting->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $row->InvoiceDate));
?>
products['<? print $product['ProductID']; ?>'] = {ProductName: '<? print $product['ProductName']; ?>', UnitCustPrice: parseFloat('<? print $product['UnitCustPrice']; ?>'), VatPercent: parseFloat('<? print ($vat->Percent == '') ? 0 : $vat->Percent; ?>')};
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
           var newInvoiceLineHTML='<tr id="invoiceline_fields_'+InvoiceLineID+'"><td><? print str_replace("\n", '', $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>'placeholder_id', 'width'=>'35', 'tabindex'=> 0, 'class' => 'combobox product', 'required' => false, 'notChoosenText' => 'Velg produkt'))); ?></td> <td style="background-color: red;"><input type="text" name="invoiceoutline.ProductName.'+InvoiceLineID+'" id="invoiceoutline.ProductName.'+InvoiceLineID+'" value="" size="20" tabindex="0" maxlength="80"> </td> <td align="center" style="background-color: red;"><input type="text" name="invoiceoutline.QuantityDelivered.'+InvoiceLineID+'" id="invoiceoutline.QuantityDelivered.'+InvoiceLineID+'" value="0.00" size="8" tabindex="0" maxlength="8" class="number" onchange="updateInvoiceLineData(this, false);"> </td> <td style="background-color: red;"><input type="text" name="invoiceoutline.UnitCustPrice.'+InvoiceLineID+'" id="invoiceoutline.UnitCustPrice.'+InvoiceLineID+'" value="0,00" size="15" tabindex="0" maxlength="15" class="number" onchange="updateInvoiceLineData(this, false);"> </td> <td id="invoiceoutline.VatPercent.'+InvoiceLineID+'">0.00%</td> <td align="right" id="invoiceoutline.VatAmount.'+InvoiceLineID+'">0,00</td> <td align="right" id="invoiceoutline.AmountExcludingVat.'+InvoiceLineID+'">0,00</td> <td><input type="button" class="button" onclick="deleteInvoiceLine('+InvoiceID+', '+CustomerAccountPlanID+', '+InvoiceLineID+'); return false;", value="Slett" /></td></tr><tr id="invoiceline_comment_'+InvoiceLineID+'"> <td colspan="8"><textarea name="invoiceoutline.Comment.'+InvoiceLineID+'" id="invoiceoutline.Comment.'+InvoiceLineID+'" cols="80" rows="1" tabindex="0"></textarea> <input type="hidden" name="'+LineNumber+'" id="'+LineNumber+'" value="'+InvoiceLineID+'"> </td></tr>';
           newInvoiceLineHTML = newInvoiceLineHTML.replace(/placeholder_id/g, InvoiceLineID);
           $(newInvoiceLineHTML).insertBefore($('#placeholder'));
           $("#field_count").val(LineNumber);
           refreshComboboxOnPage();
           setComboboxOnChangeAction();
           console.log("Added invoice line "+InvoiceLineID);
           // since we always add an empty line there is no need to update
           // the total amounts here
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
  $.post('http://lodo/lodo.php?t=invoice.ajax.php', params,
         function(data, status) {
           var fields = $('#invoiceline_fields_'+LineID);
           fields.remove();
           var comment = $('#invoiceline_comment_'+LineID);
           comment.remove();
           console.log("Removed invoice line "+LineID);
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
  var amount_excluding_vat_sum = 0.0;
  for(i = 1; i <= number_of_invoice_lines; i++) {
    var invoice_line_id = document.getElementById(i);
    if (invoice_line_id != null) {
      vat_amount_sum += toNumber(document.getElementById('<? print $db_table2 ?>.VatAmount.'+invoice_line_id.value).innerHTML);
      amount_excluding_vat_sum += toNumber(document.getElementById('<? print $db_table2 ?>.AmountExcludingVat.'+invoice_line_id.value).innerHTML);
    }
  }
  var amount_including_vat_sum = amount_excluding_vat_sum + vat_amount_sum;
  var invoice_total_vat_element = document.getElementById('<? print $db_table ?>.TotalVat');
  invoice_total_vat_element.innerHTML = toAmountString(vat_amount_sum, 2);
  var invoice_total_cust_price_element = document.getElementById('<? print $db_table ?>.TotalCustPrice');
  invoice_total_cust_price_element.innerHTML = toAmountString(amount_including_vat_sum, 2);
  var invoice_amount_without_vat_sum_element = document.getElementById('<? print $db_table ?>.AmountWithoutVatSum');
  invoice_amount_without_vat_sum_element.innerHTML = toAmountString(amount_excluding_vat_sum, 2);
  var invoice_vat_amount_sum_element = document.getElementById('<? print $db_table ?>.VatAmountSum');
  invoice_vat_amount_sum_element.innerHTML = toAmountString(vat_amount_sum, 2);
  var invoice_amount_with_vat_sum_element = document.getElementById('<? print $db_table ?>.AmountWithVatSum');
  invoice_amount_with_vat_sum_element.innerHTML = toAmountString(amount_including_vat_sum, 2);
}

// update data for the line if the product/quantity/price changes
// should only change the price if the product changes
function updateInvoiceLineData(element, update_amount) {
  update_amount = typeof update_amount !== 'undefined' ? update_amount : true;
  var id = element.id;
  var name = element.id.split('.');

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
  var vat_amount = (vat_percent/100.0) * unit_cust_price * quantity_delivered;
  vat_amount_element.innerHTML = toAmountString(vat_amount, 2);

  name[1] = 'AmountExcludingVat';
  var amount_excluding_vat_element = document.getElementById(name.join('.'));
  var amount_excluding_vat = unit_cust_price * quantity_delivered;
  amount_excluding_vat_element.innerHTML = toAmountString(amount_excluding_vat, 2);
  updateInvoiceData();
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

$(document).ready(function() {
  setComboboxOnChangeAction();
});
</script>
</head>

<body>
<? includeinc('top') ?>
<? includeinc('left') ?>

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
        <td><a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=$InvoiceID"; ?>&type=salecash_in"><? print $InvoiceID ?></a></td>
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
        <td><? print $row->IOrgNo ?></td>
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
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceDate', 'pk'=>$InvoiceID, 'value'=>substr($row->InvoiceDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
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
    <tr>
      <td>Merk</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Note', 'pk'=>$InvoiceID, 'value'=>$row->Note, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
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
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'pk'=>$InvoiceID, 'value' => $row->DepartmentID)); ?></td>
      <td>Avdeling</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DepartmentCustomer', 'pk'=>$InvoiceID, 'value'=>$row->DepartmentCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$InvoiceID,  'value' =>  $row->ProjectID)) ?></td>
      <td>Prosjekt</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProjectNameCustomer', 'pk'=>$InvoiceID, 'value'=>$row->ProjectNameCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Leveringsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$InvoiceID, 'value'=>$row->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
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

</tfoot>
</table>
<br>
<table border="0" cellspacing="0" width="775">
<thead>
  <tr>
    <td>ProduktNr</td>
    <td>Produkt navn</td>
    <td>Antall</td>
    <td>Enhetspris</td>
    <td>MVA</td>
    <td>MVA bel&oslash;p</td>
    <td>Bel&oslash;p U/MVA</td>
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
    $sumline = round($row2->QuantityDelivered * $row2->UnitCustPrice, 2);
    $vatline = round(($row2->Vat/100) * $sumline,2);
    $sumlines += $sumline;
    $vatlines += $vatline;
    ?>
    <tr id='invoiceline_fields_<? print $LineID; ?>'>
        <td><? print $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>$LineID, 'value'=>$row2->ProductID, 'width'=>'35', 'tabindex'=>$tabindex++, 'class' => 'combobox product', 'required' => false, 'notChoosenText' => 'Velg produkt')) ?></td>
        <td style='<? if (empty($row2->ProductName)) echo "background-color: red;"; ?>'><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName', 'pk'=>$LineID, 'value'=>$row2->ProductName, 'width'=>'20', 'maxlength' => 80, 'tabindex'=>$tabindex++)) ?></td>
        <td align="center" style='<? if ($row2->QuantityDelivered == 0) echo "background-color: red;"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount($row2->QuantityDelivered), 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number', 'OnChange' => 'updateInvoiceLineData(this, false);')) ?></td>
        <td style='<? if ($row2->UnitCustPrice == 0) echo "background-color: red;"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number', 'OnChange' => 'updateInvoiceLineData(this, false);')) ?></td>
        <td id="<? print $db_table2 . ".VatPercent." . $LineID; ?>"><? print $_lib['format']->Amount($row2->Vat) ?>%<? #print $_lib['form3']->vat_menu3(array('percent2'=>'1', 'table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'SaleMenu'=>'1', 'date' => $row->InvoiceDate)) ?></td>
        <td align="right" id="<? print $db_table2 . ".VatAmount." . $LineID; ?>"><? print $_lib['format']->Amount($vatline) ?></td>
        <td align="right" id="<? print $db_table2 . ".AmountExcludingVat." . $LineID; ?>"><? print $_lib['format']->Amount($sumline) ?></td>
        <td>
        <? if(
               (!$row->Locked &&
                   $_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))
               ||
                ($_lib['sess']->get_person('AccessLevel') >= 4 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))) { ?>
        <input type="button" class="button" onclick="deleteInvoiceLine(<? print $InvoiceID . ", " . $row->CustomerAccountPlanID . ", " . $LineID; ?>); return false;" value="Slett" />
        <? } ?>
    <tr id='invoiceline_comment_<? print $LineID; ?>'>
        <td colspan="8"><? print $_lib['form3']->textarea(array('table'=>$db_table2, 'field'=>'Comment', 'pk'=>$LineID, 'value'=>$row2->Comment, 'tabindex'=>$tabindex++, 'min_height'=>'1', 'height'=>'1', 'width'=>'80')) ?>
    <?
    $rowCounter++;
    print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$LineID));
}
?>
    <tr id="placeholder">
    </tr>
    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Sum linjer</td>
        <td id="invoiceout.AmountWithoutVatSum" align="right"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Sum MVA</td>
        <td id="invoiceout.VatAmountSum" align="right"><? print $_lib['format']->Amount($vatlines) ?></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Total med MVA</td>
        <td id="invoiceout.AmountWithVatSum" align="right"><? print $_lib['format']->Amount($vatlines + $sumlines) ?></td>
        <?
            print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>'field_count', 'value'=>$rowCounter));
        ?>
    </tr>
    <tr>
        <td colspan="7"><br><hr>
    </tr>
</tbody>

<tfoot>
    <tr>
        <td>
        <?
	    if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')))
            {
                if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_linenew', 'tabindex' => $tabindex++, 'value'=>'Ny fakturalinje (N)', 'accesskey'=>'N', 'OnClick'=>"newInvoiceLine(".$InvoiceID.", ".$row->CustomerAccountPlanID.", $('#field_count').val()); return false;"));
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
        <td>
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
        <td>
        <?
	if(!$row->Locked) {
		print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_lock', 'tabindex' => $tabindex++, 'value'=>'L&aring;s (L)', 'accesskey'=>'L', 'confirm'=>'Er du sikker p&aring; at du vil l&aring;se fakturaen?'));
	};

        ?>
        </td>


        <td colspan="6" align="right">
        <?

        if($_lib['sess']->get_person('FakturabankExportInvoiceAccess')) {
            echo "Orgnummer: ".  $row->IOrgNo . "<br />";

            if($row->IOrgNo)
                print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_fakturabanksend', 'tabindex' => $tabindex++,'value'=>'Fakturabank (F)', 'accesskey'=>'F'));
            else
                print "Mangler orgnummer ";
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


        ?>
</form>
    <tr>
      <td></td>
    </tr>
        <?
          if ($row->UpdatedByPersonID) echo "<tr><td>" . $row->UpdatedAt . " lagret av " . $_lib['format']->PersonIDToName($row->UpdatedByPersonID) . "</td></tr>";
          if ($row->Locked) {
            if ($row->LockedBy) echo "<tr><td>" . $row->LockedAt . " l&aring;st av " . $_lib['format']->PersonIDToName($row->LockedBy) . "</td></tr>";
            else echo "<tr><td>L&aring;st: Ja </td></tr>";
          }
          if ($row->FakturabankPersonID) echo "<tr><td>" . $row->FakturabankDateTime . " fakturaBank " . $_lib['format']->PersonIDToName($row->FakturabankPersonID) . "</td></tr>";
        ?>
        <td colspan="7" align="right">
        <form name="skriv_ut" action="<? print $_lib['sess']->dispatch ?>t=invoice.print&InvoiceID=<? print $InvoiceID ?>&amp;inline=edit" method="post" target="_new">
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift')) ?>
        </form>
        <form name="skriv_ut2" action="<? print $_lib['sess']->dispatch ?>t=invoice.print2&InvoiceID=<? print $InvoiceID ?>" method="post" target="_new">
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift PDF')) ?>
        </form>
     </tr>
     <tr>
        <td colspan="7" align="right">
        <form name="send_mail" action="<? print $_lib['sess']->dispatch ?>t=invoice.sendmail&InvoiceID=<? print $InvoiceID ?>" method="post">
          <?php
            $rowcomapny = $_lib['storage']->get_row(array('query' => "SELECT * FROM `company` WHERE CompanyID=" . $row->FromCompanyID));
          ?>
            <br />
            <input type="text" value="<? print $row->IEmail; ?>" name="email_recipient" />
            <input type="hidden" value="<?=  $rowcomapny->CopyFakturaMail ?>" name="send_mail_copy_mail" />
            <input name="send_mail_copy" type="checkbox" checked /> kopi til firma
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_send_email2', 'tabindex' => $tabindex++, 'value'=>'Send email')) ?>
        </form>
     </tr>
   <? if(!$row->Locked) { ?>
     <tr><td>L&aring;st:  Nei</td></tr>
   <? } ?>
</tfoot>
</table>
<a name="bottomPage"></a>
</body>
</html>
<? unset($_SESSION['oauth_invoice_sent']); ?>
