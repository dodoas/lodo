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
// Remove an allowance/charge from invoice line
elseif($_lib['input']->getProperty('action_invoiceline_allowance_charge_delete')) {
    $InvoiceLineAllowanceChargeID = $_lib['input']->getProperty('InvoiceLineAllowanceChargeID');
    $invoice->line_allowance_charge_delete($InvoiceLineAllowanceChargeID);
    // set who updated and when, since we removed an invoice line allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
// Remove an allowance/charge from invoice
elseif($_lib['input']->getProperty('action_invoice_allowance_charge_delete')) {
    $InvoiceAllowanceChargeID = $_lib['input']->getProperty('InvoiceAllowanceChargeID');
    $invoice->allowance_charge_delete($InvoiceAllowanceChargeID);
    // set who updated and when, since we removed an allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
}
// Add an allowance/charge to invoice
elseif($_lib['input']->getProperty('action_invoice_allowance_charge_new')) {
    // set who updated and when, since we added an allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $InvoiceAllowanceChargeID = $invoice->allowance_charge_new();
    echo "<span id=\"allowance_charge_id\">$InvoiceAllowanceChargeID</span>";
}
// Add an allowance/charge to invoice line
elseif($_lib['input']->getProperty('action_invoiceline_allowance_charge_new')) {
    $InvoiceLineID = $_lib['input']->getProperty('InvoiceLineID');
    // set who updated and when, since we added a line allowance/charge
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
    $InvoiceLineAllowanceChargeID = $invoice->line_allowance_charge_new($InvoiceLineID);
    echo "<span id=\"line_allowance_charge_id\">$InvoiceLineAllowanceChargeID</span>";
}
?>
