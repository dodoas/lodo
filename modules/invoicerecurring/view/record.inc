<?
$CustomerAccountPlanID  = (int) $_POST["recurringout_CustomerAccountPlanID_$RecurringID"];
$VoucherType            = 'S';

#print_r($_SERVER);

includelogic('invoicerecurring/recurring');

$recurring = new recurring(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'RecurringID' => $_lib['input']->getProperty('RecurringID')));

if($_lib['input']->getProperty('action_invoicerecurring_update') or $_lib['input']->getProperty('action_invoiceline_new')) {
    print "Oppdaterer faktura";
    $recurring->update($_POST);
    $recurring->create_recurring($_POST, true);
}

elseif($_lib['input']->getProperty('action_invoicerecurring_new')) {
    $RecurringID = $recurring->create($_POST);

    $recurring->create_recurring($_POST);
}

elseif($_lib['input']->getProperty('action_invoicerecurring_outlinedelete')) {
    $recurring->linedelete($_lib['input']->getProperty('LineID'));
    $recurring->init(array());
    #$recurring->update($_POST);
}

elseif($_lib['input']->getProperty('action_invoicerecurring_delete')) {
    $recurring->delete_invoice();

    if(isset($_SERVER['HTTPS']))
        $protocol = "https";
    else
        $protocol = "http";

    $header = "Location: ".$protocol."://".$_SERVER['HTTP_HOST'] . '/' . $_SETUP['DISPATCHR'] ."t=invoicerecurring.list&message=" . urlencode($_lib['message']->get());
    #print "$header<br>";
    #It is a problem that http host includes server port after the first deletion.
    header ($header);
}
elseif($_lib['input']->getProperty('action_invoicerecurring_linenew')) {
    $recurring->create_recurring($_POST, true);
    $recurring->update($_POST);
    #print "Du fikk en ny linje<br>\n";
    $recurring->linenew();
}
elseif($_lib['input']->getProperty('action_invoicerecurring_newonthis')) {
    $recurring->init(array());
    $RecurringID = $recurring->copy($_POST);
}

?>
