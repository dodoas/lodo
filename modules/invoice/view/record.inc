<?
$CustomerAccountPlanID  = (int) $_POST["invoiceout_CustomerAccountPlanID_$InvoiceID"];
$VoucherType            = 'S';

#print_r($_SERVER);

includelogic('invoice/invoice');
$invoice = new invoice(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'InvoiceID' => $_lib['input']->getProperty('InvoiceID')));

if($_lib['input']->getProperty('action_invoice_update') or $_lib['input']->getProperty('action_invoiceline_new')) {
    #print "Oppdaterer faktura";
    $invoice->update($_POST);
}

elseif($_lib['input']->getProperty('action_invoice_new')) {
    $InvoiceID = $invoice->create($_POST);
}

elseif($_lib['input']->getProperty('action_invoice_outlinedelete')) {
    $invoice->linedelete($_lib['input']->getProperty('LineID'));
    $invoice->init(array());
    #$invoice->update($_POST);
}

elseif($_lib['input']->getProperty('action_invoice_delete')) {
    $invoice->delete_invoice();

    $header = "Location: http://".$_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $_SETUP['DISPATCHR'] ."t=invoice.list&message=" . urlencode($_lib['message']->get());
    #print "$header<br>";
    #It is a problem that http host includes server port after the first deletion.
    header ($header);
}
elseif($_lib['input']->getProperty('action_invoice_linenew')) {
    $invoice->update($_POST);
    #print "Du fikk en ny linje<br>\n";
    $invoice->linenew();
}
elseif($_lib['input']->getProperty('action_invoice_newonthis')) {
    $invoice->init(array());
    $InvoiceID = $invoice->copy($_POST);
}
elseif($_lib['input']->getProperty('action_invoice_fakturabanksend')) {
    $invoice->init(array());
    $invoice->fakturabank_send();
}
elseif($_lib['input']->getProperty('action_invoice_fakturabankjournal')) {
    $invoice->fakturabankjournal();
}
elseif($_lib['input']->getProperty('action_invoice_lock')) {
    $invoice->init(array());
    $invoice->lock();
}
?>