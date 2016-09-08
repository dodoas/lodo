<?
// always send SupplierAccountPlanID, needed for new line and delete line
// actions, which are links and do not include other fields
if($_lib['input']->getProperty('action_invoicein_linenew') || $_lib['input']->getProperty('action_invoicein_linedelete')) {
  $ID = $_REQUEST['ID'];
  $supplier_accountplan = $_lib['storage']->get_row(array('query' => "SELECT SupplierAccountPlanID FROM invoicein WHERE ID = " . $ID));
  $_REQUEST['invoicein_SupplierAccountPlanID_' . $ID] = $supplier_accountplan->SupplierAccountPlanID;
}

if (isset($_POST['invoicein_InvoiceDate_'.$ID]) && !$_lib['validation']->date($_POST['invoicein_InvoiceDate_'.$ID])) {
  $_lib['message']->add('Ugyldig fakturadato '.$_POST['invoicein_InvoiceDate_'.$ID].'!');
}

if($_lib['input']->getProperty('action_invoicein_journal')) {
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

} elseif($_lib['input']->getProperty('action_invoicein_linenew')) {
    $invoicein->linenew($_REQUEST);

} elseif($_lib['input']->getProperty('action_invoicein_linedelete')) {
    $invoicein->linedelete($_REQUEST);

} elseif($_lib['input']->getProperty('action_invoicein_add')) {
    $ID = $invoicein->add();
    header("Location: " . $_lib['sess']->dispatchs . "t=invoicein.edit&ID=" . $ID . "&inline=edit");
    die();
} elseif($_lib['input']->getProperty('action_invoicein_delete')) {
    $deleted = $invoicein->delete();
    if ($deleted) {
      header("Location: " . $_lib['sess']->dispatchs . "t=invoicein.list");
      die();
    }
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
