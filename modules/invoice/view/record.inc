<?
$CustomerAccountPlanID  = (int) $_POST["invoiceout_CustomerAccountPlanID_$InvoiceID"];
$VoucherType            = 'S';

#print_r($_SERVER);

includelogic('invoice/invoice');
$invoice = new invoice(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'InvoiceID' => $_lib['input']->getProperty('InvoiceID')));

if($_lib['input']->getProperty('action_invoice_update') or $_lib['input']->getProperty('action_invoiceline_new')) {
    #print "Oppdaterer faktura";
    $InvoiceLineIDs = array();
    for ($i = 1; $i <= $_POST['field_count']; $i++) $InvoiceLineIDs[] = $_POST[$i];
    if ($invoice->CheckIfAnythingChanged($InvoiceID, $InvoiceLineIDs, $_POST)) {
      // set who updated and when, since something was changed
      $_POST['invoiceout_UpdatedByPersonID_' . $InvoiceID] = $_lib['sess']->get_person('PersonID');
      $_POST['invoiceout_UpdatedAt_' . $InvoiceID] = strftime("%F %T");
      $invoice->update($_POST);
    }
    // Make sure company info is updated
    $get_invoice = "SELECT * FROM $db_table WHERE InvoiceID='$InvoiceID'";
    $row = $_lib['storage']->get_row(array('query' => $get_invoice));
    $invoice_info_for_update = array('InvoiceID'                                      => $InvoiceID,
                                     'invoiceout_DueDate_' . $InvoiceID               => $row->DueDate,
                                     'invoiceout_CustomerAccountPlanID_' . $InvoiceID => $row->CustomerAccountPlanID,
                                     '_DAddress'                                      => $_POST['_DAddress'],
                                     '_DZipCode'                                      => $_POST['_DZipCode'],
                                     '_DCity'                                         => $_POST['_DCity'],
                                     '_DCountryCode'                                  => $_POST['_DCountryCode']
                                    );
    $invoice->update($invoice_info_for_update);
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
    // set who updated and when, since we deleted an invoice line
    $invoice->update(array('invoiceout_UpdatedByPersonID_' . $InvoiceID => $_lib['sess']->get_person('PersonID'), 'invoiceout_UpdatedAt_' . $InvoiceID => strftime("%F %T")));
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
    // set who updated and when, since we added an invoice line
    $_POST['invoiceout_UpdatedByPersonID_' . $InvoiceID] = $_lib['sess']->get_person('PersonID');
    $_POST['invoiceout_UpdatedAt_' . $InvoiceID] = strftime("%F %T");
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
    if ($invoice->fakturabank_send_precheck()) $invoice->fakturabank_send();
}
elseif($_lib['input']->getProperty('action_invoice_fakturabankjournal')) {
    $invoice->fakturabankjournal();
}
elseif($_lib['input']->getProperty('action_invoice_lock')) {
    $invoice->init(array());
    $invoice->lock();
}
elseif($_lib['input']->getProperty('action_save_internal')) {
    $comment = "";
    $invoiceno = $_lib['input']->getProperty('InvoiceID');

    foreach($_POST as $k => $v) {
        if(strstr($k, "CommentInternal") !== false) {
            $comment = $v;
        }
    }
    
    $query = sprintf("UPDATE invoiceout SET CommentInternal = '%s' WHERE InvoiceID = %d",
                     $_lib['db']->db_escape($comment), 
                     $invoiceno);
    $_lib['db']->db_query($query);
    $header = "Location: lodo.php?t=invoice.edit&inline=show&InvoiceID=".$InvoiceID;
    header ($header);
}

?>
