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

elseif($_lib['input']->getProperty('action_auto_save')) {
    if(!$accounting->is_valid_accountperiod($_POST['voucher_period'], $_lib['sess']->get_person('AccessLevel'))) { 
        $message = "Perioden er stengt"; 
	$voucher_period = "";
    }
    else {
        $message = "";
	$voucher_periode = $_POST['voucher_period'];
    }

    $header = "Location: " . str_replace("&amp;", "&", $_lib['sess']->dispatch) . "t=invoice.listoutgoing&message=$message";
    header($header);
    setcookie('invoice_period', $voucher_periode, time() + 60*60*5);
    setcookie('invoice_voucher_date', $_POST['voucher_date'], time() + 60*60*5);

    exit();
}

elseif($_lib['input']->getProperty('action_invoice_outlinedelete')) {
    $invoice->linedelete($_lib['input']->getProperty('LineID'));
    $invoice->init(array());
    #$invoice->update($_POST);
}

elseif($_lib['input']->getProperty('action_invoice_delete')) {
    $invoice->delete_invoice();

    if(isset($_SERVER['HTTPS']))
        $protocol = "https";
    else
        $protocol = "http";

    $header = "Location: ".$protocol."://".$_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $_SETUP['DISPATCHR'] ."t=invoice.listoutgoing&message=" . urlencode($_lib['message']->get());
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
