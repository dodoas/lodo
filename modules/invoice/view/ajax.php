<?
$InvoiceID = $_lib['input']->getProperty('InvoiceID');
$CustomerAccountPlanID  = (int) $_POST["invoiceout_CustomerAccountPlanID_$InvoiceID"];
$VoucherType            = 'S';

includelogic('invoice/invoice');
includelogic('accounting/accounting');
// needed in some of the invoice class actions
$accounting = new accounting();
$invoice = new invoice(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'InvoiceID' => $InvoiceID));

if($_lib['input']->getProperty('action_invoice_outlinedelete')) {
    $LineID = $_lib['input']->getProperty('LineID');
    $invoice->linedelete($LineID);
    // set who updated and when, since we removed an invoice line
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
elseif($_lib['input']->getProperty('action_invoice_linenew')) {
    // set who updated and when, since we added an invoice line
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $NewLineID = $invoice->linenew();
    echo "<span id=\"line_id\">$NewLineID</span>";
}
?>
