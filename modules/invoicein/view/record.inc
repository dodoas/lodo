<?
if($_lib['input']->getProperty('action_invoicein_journal')) {
    print "bilagsf¿r<br>";
    $invoicein->journal();

} elseif($_lib['input']->getProperty('action_invoicein_update') or $_lib['input']->getProperty('action_invoiceline_new')) {

        // Allow unsetting a Department
        if (($_POST['invoicein_DepartmentID_'.$invoicein->ID] === "unset")) {
            $_POST['invoicein_DepartmentID_'.$invoicein->ID] = DB_NULL_PLACEHOLDER;
        }

        // Allow unsetting a Project
        if (($_POST['invoicein_ProjectID_'.$invoicein->ID] === "unset")) {
            $_POST['invoicein_ProjectID_'.$invoicein->ID] = DB_NULL_PLACEHOLDER;
        }

    $invoice_date = $_POST['invoicein_InvoiceDate_' . $invoicein->ID];
    $Period = substr($invoice_date, 0, 7);
    if ($accounting->is_valid_accountperiod($Period, $_lib['sess']->get_person('AccessLevel'))) {
      $_POST['invoicein_Period_' . $invoicein->ID] = $Period;
    }
    $invoicein->update($_POST);

} elseif($_lib['input']->getProperty('action_invoicein_remittance')) {

    includelogic('remittance/remittance');

    $rem = new logic_remittance_remittance($_POST);
    print $rem->fill();
    print $rem->pay();
}
// Remove an allowance/charge from invoice line
if($_lib['input']->getProperty('action_invoicein_line_allowance_charge_delete')) {
    $invoicein->line_allowance_charge_delete($_REQUEST);

}
// Add an allowance/charge to invoice line
elseif($_lib['input']->getProperty('action_invoicein_line_allowance_charge_new')) {
    $invoicein->line_allowance_charge_new($_REQUEST);

}
// Remove an allowance/charge from invoice
elseif($_lib['input']->getProperty('action_invoicein_allowance_charge_delete')) {
    $invoicein->allowance_charge_delete($_REQUEST);

}
// Add an allowance/charge to invoice
elseif($_lib['input']->getProperty('action_invoicein_allowance_charge_new')) {
    $invoicein->allowance_charge_new($_REQUEST);

}
// Add new invoice line
elseif($_lib['input']->getProperty('action_invoicein_linenew')) {
    $invoicein->update($_POST);
    $invoicein->linenew($_REQUEST);

}
// Remove an invoice line
elseif($_lib['input']->getProperty('action_invoicein_linedelete')) {
    $invoicein->update($_POST);
    $invoicein->linedelete($_REQUEST);

}
?>
