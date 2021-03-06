<?

// recieves date as string 'YYYY-MM-DD' and returns bool if date is valid
function validDate($date) {
  $d = DateTime::createFromFormat('Y-m-d', $date);
  return $d && $d->format('Y-m-d') === $date;
}

$CustomerAccountPlanID  = (int) $_POST["invoiceout_CustomerAccountPlanID_$InvoiceID"];
if ($CustomerAccountPlanID == 0 && $InvoiceID) {
  $CustomerAccountPlanID = $_lib['storage']->get_row(array('query' => 'SELECT CustomerAccountPlanID FROM invoiceout WHERE InvoiceID = ' . $InvoiceID))->CustomerAccountPlanID;
}
$VoucherType            = 'S';

#print_r($_SERVER);

includelogic('invoice/invoice');
$invoice = new invoice(array('CustomerAccountPlanID' => $CustomerAccountPlanID, 'VoucherType' => $VoucherType, 'InvoiceID' => $_lib['input']->getProperty('InvoiceID')));

if($_lib['input']->getProperty('action_invoice_update') or $_lib['input']->getProperty('action_invoiceline_new')) {
    #print "Oppdaterer faktura";

        // Allow unsetting a Department
        if (($_POST['invoiceout_DepartmentID_'.$InvoiceID] === "unset")) {
            $_POST['invoiceout_DepartmentID_'.$InvoiceID] = DB_NULL_PLACEHOLDER;
        }

        // Allow unsetting a Project
        if (($_POST['invoiceout_ProjectID_'.$InvoiceID] === "unset")) {
            $_POST['invoiceout_ProjectID_'.$InvoiceID] = DB_NULL_PLACEHOLDER;
        }

    $invoice_date = $_POST['invoiceout_InvoiceDate_'.$InvoiceID];
    if (!validDate($invoice_date)) {
      $_lib['message']->add('Ugyldig fakturadato '.$invoice_date.'!');
    } else {
      $updated = false;
      $Period = substr($invoice_date, 0, 7);
      if ($accounting->is_valid_accountperiod($Period, $_lib['sess']->get_person('AccessLevel'))) {
        $_POST['invoiceout_Period_' . $InvoiceID] = $Period;
      }
      $InvoiceLineIDs = array();
      for ($i = 1; $i <= $_POST['field_count']; $i++) if (isset($_POST[$i])) $InvoiceLineIDs[] = $_POST[$i];
      if ($invoice->CheckIfAnythingChanged($InvoiceID, $InvoiceLineIDs, $_POST)) {
        // set who updated and when, since something was changed
        $_POST['invoiceout_UpdatedByPersonID_' . $InvoiceID] = $_lib['sess']->get_person('PersonID');
        $_POST['invoiceout_UpdatedAt_' . $InvoiceID] = strftime("%F %T");
        $invoice->update($_POST);
        $updated = true;
      }
      // Make sure company info is updated even if nothing has been changed in the invoice data
      if (!$updated) {
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
    }
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
    if (validDate($_POST['voucher_date'])) {
      $voucher_date = $_POST['voucher_date'];
    } else {
      $voucher_date = "0000-00-00";
      $message = ((!empty($message))?$message . "<br>":"") . "Ugyldig fakturadato ".$_POST['voucher_date']."!";
    }
    setcookie('invoice_period', $voucher_periode, time() + 60*60*5);
    setcookie('invoice_voucher_date', $voucher_date, time() + 60*60*5);

    $header = "Location: " . str_replace("&amp;", "&", $_lib['sess']->dispatch) . "t=invoice.listoutgoing&message=$message";
    header($header);
    exit();
}

// TODO: maybe remove since it is moved to ajax.php
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
// TODO: maybe remove since it is moved to ajax.php
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
elseif($_lib['input']->getProperty('action_invoice_fakturabankjournal')) {
    $invoice->fakturabankjournal();
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

$ready_to_send_to_fb = false;
$error_messages = array();
if($_lib['input']->getProperty('InvoiceID')) {
  $invoice->init(array());
  list($ready_to_send_to_fb, $error_messages) = $invoice->fakturabank_send_precheck();
}

if($_lib['input']->getProperty('action_invoice_fakturabanksend') || $_lib['input']->getProperty('action_invoice_lock')) {
  foreach($error_messages as $error_message) $_lib['message']->add(array('message' => $error_message));
}

if($_lib['input']->getProperty('action_invoice_lock')) {
    $get_invoice = "select I.* from $db_table as I where InvoiceID='$InvoiceID'";
    $row = $_lib['storage']->get_row(array('query' => $get_invoice));
    if ($ready_to_send_to_fb && $row->Locked == "0") {
      $invoice->lock();
    }
}
elseif($_lib['input']->getProperty('action_invoice_fakturabanksend')) {
    if ($ready_to_send_to_fb) {
      $_SESSION['oauth_action'] = 'send_invoice';
      $invoice->fakturabank_send();
    }
}

?>
