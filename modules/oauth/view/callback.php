<?php
// Based on the data recieved dispach it in LODO

// Redirects back to the page that requested the OAuth resource
function redirect() {
    $redirect_url = $_SESSION['oauth_tmp_redirect_back_url'];
    unset($_SESSION['oauth_tmp_redirect_back_url']);
    header('Location: ' . $redirect_url); 
}

global $_lib;
session_start();
includelogic("oauth/oauth");
includelogic('orgnumberlookup/orgnumberlookup');
includelogic('fakturabank/invoicereconciliationreason');
includelogic('fakturabank/bankreconciliationreason');
includelogic('fakturabank/fakturabankvoting');
includelogic('fakturabank/fakturabanksalary');
includelogic("accountplan/scheme");

// create client
$oauth_client = new lodo_oauth();

// get previously saved resources
$params = (isset($_SESSION['oauth_resource_params'])) ? $_SESSION['oauth_resource_params'] : false;
$resource_url = $_SESSION['oauth_resource_url'];
$http_verb = $_SESSION['oauth_http_verb'];

if (!isset($_GET['code'])) {
  if ($http_verb == "POST") {
    $resource = $oauth_client->post_resources($resource_url, $params);
  }
  else {
    $resource = $oauth_client->get_resources($resource_url, $params);
  }
} else {
  if ($http_verb == "POST") {
    $resource = $oauth_client->post_resources($resource_url, $params, $_GET['code']);
  }
  else {
    $resource = $oauth_client->get_resources($resource_url, $params, $_GET['code']);
  }
}

// depending on the action, do stuff
switch ($_SESSION['oauth_action']) {
case 'get_balance_report': // fetch balance report from FB
  $report_xml = $_SESSION['oauth_resource']['result'];
  $_SESSION['oauth_balance_report_fetched'] = true;
  $fbvoting = new lodo_fakturabank_fakturabankvoting();
  $AccountID = $_SESSION['oauth_balance_account_id'];
  $Period    = $_SESSION['oauth_balance_period'];
  $Country   = $_SESSION['oauth_balance_country'];
  unset($_SESSION['oauth_balance_account_id']);
  unset($_SESSION['oauth_balance_period']);
  unset($_SESSION['oauth_balance_country']);
  $fbvoting->import_transactions($AccountID, $Period, $Country);
  redirect();
  break;
case 'send_paycheck': // sending a paycheck to FB
  $SalaryID     = $_SESSION['oauth_salary_id'];
  $SalaryConfID = $_SESSION['oauth_salary_conf_id'];
  unset($_SESSION['oauth_salary_id']);
  unset($_SESSION['oauth_salary_conf_id']);
  $fb_salary = new lodo_fakturabank_fakturabanksalary();
  $_SESSION['oauth_paycheck_sent'] = true;
  $fb_salary->sendsalary($SalaryID, $SalaryConfID);
  if ($_SESSION['oauth_resource']['code'] != 201) {
    $_SESSION['oauth_paycheck_messages'][] = $_SESSION['oauth_resource']['result'];
    if ($_SESSION['oauth_resource']['code'] == 403) $_SESSION['oauth_paycheck_messages'][] = "Utilstrekkelige rettigheter i fakturabank!";
  }
  else {
    $dataH = array();
    $dataH['SalaryID']              = $SalaryID;
    $dataH['FakturabankID']         = $_SESSION['oauth_fakturabank_salary_id'];
    $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
    $dataH['FakturabankDateTime']   = $_lib['sess']->get_session('Datetime');

    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'salary', 'debug' => false));

    $query = sprintf("UPDATE salary SET LockedBy = '%s %s', LockedDate = NOW() WHERE SalaryID = %d LIMIT 1", $_lib['sess']->get_person('FirstName'), $_lib['sess']->get_person('LastName'), $SalaryID);
    $_lib['db']->db_query($query);
    $_SESSION['oauth_paycheck_messages'][] = "Sendt til Fakturabank.";
  }
  redirect();
  break;
case 'send_invoice': // sending an invoice to FB
  if ($_SESSION['oauth_resource']['code'] != 302 || $_SESSION['oauth_resource']['code'] != 201) { // not created or found
    $_SESSION['oauth_invoice_error'] = "Error: opprette faktura: " . $_SESSION['oauth_resource']['result'];
    if ($_SESSION['oauth_resource']['code'] == 403) $_SESSION['oauth_invoice_error'] .= "Utilstrekkelige rettigheter i fakturabank!";
  }
  else {
    $dataH = array();
    $dataH['InvoiceID']             = $_SESSION['oauth_invoice_id'];
    $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
    $dataH['FakturabankDateTime']   = $_lib['sess']->get_session('Datetime');
    $dataH['Locked']                = 1;
    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoiceout', 'debug' => false));
  }
  redirect();
  break;
case 'set_invoice_statuses': // set status to registered for all invoices downloaded from FB
  unset($_SESSION['oauth_invoices_fetched']);
  redirect();
  break;
case 'outgoing_invoices': // fetching all approved outgoing/incoming invoices from FB, same action needed
case 'incoming_invoices':
  $_SESSION['oauth_invoices_fetched'] = true;
  redirect();
  break;
default:  // else, not recognized action
  unset($_SESSION['oauth_action']);
  break;
}

?>
