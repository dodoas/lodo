<?php
// Based on the data recieved dispach it in LODO

// Redirects back to the page that requested the OAuth resource
function redirect() {
    global $_SETUP;
    $redirect_url = $_SESSION['oauth_tmp_redirect_back_url'];
    unset($_SESSION['oauth_tmp_redirect_back_url']);
    unset($_SESSION['oauth_action']);
    if (empty($redirect_url)) $redirect_url = "$_SETUP[OAUTH_PROTOCOL]://$_SERVER[HTTP_HOST]/lodo.php?t=lodo.main";
    header('Location: ' . $redirect_url); 
}

global $_lib;
session_start();
includelogic("oauth/oauth");
includelogic('orgnumberlookup/orgnumberlookup');
includelogic('fakturabank/invoicereconciliationreason');
includelogic('fakturabank/bankreconciliationreason');
includelogic('fakturabank/fakturabankvoting');
includelogic("accountplan/scheme");

includelogic('fakturabank/fakturabanksalary');
includelogic('fakturabank/fakturabank');

// create client
$oauth_client = new lodo_oauth();

// get previously saved resources
$params = (isset($_SESSION['oauth_resource_params'])) ? $_SESSION['oauth_resource_params'] : false;
$resource_url = $_SESSION['oauth_resource_url'];
$http_verb = $_SESSION['oauth_http_verb'];

if ($resource_url) {
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
}

// depending on the action, do stuff
switch ($_SESSION['oauth_action']) {
case 'get_balance_report': // fetch balance report from FB
  $fbvoting = new lodo_fakturabank_fakturabankvoting();
  $AccountID = $_SESSION['oauth_balance_account_id'];
  $Period    = $_SESSION['oauth_balance_period'];
  $Country   = $_SESSION['oauth_balance_country'];
  unset($_SESSION['oauth_balance_account_id']);
  unset($_SESSION['oauth_balance_period']);
  unset($_SESSION['oauth_balance_country']);
  $fbvoting->import_transactions($AccountID, $Period, $Country);
  if (strpos($_SESSION['oauth_tmp_redirect_back_url'], 'Period') === false) $_SESSION['oauth_tmp_redirect_back_url'] .= "&Period=" . $Period;
  redirect();
  break;
case 'get_car_info': // fetch car info from FB
  $CarCodes = $_SESSION['oauth_car_codes'];
  foreach ($CarCodes as $key => &$code) {
    $code = "carregistration.RegistrationNumber.".$key."=".$code; // $key value is not of importance here. We just want to have all parameters, so they have to have different keys
  }
  $car_codes_string = join("&", $CarCodes);
  $CarID = $_SESSION['oauth_car_id'];
  unset($_SESSION['oauth_car_codes']);
  unset($_SESSION['oauth_car_id']);
  $_SESSION['oauth_tmp_redirect_back_url'] = $_SETUP['DISPATCH'] . "t=car.edit&car.CarID=". $CarID ."&". $car_codes_string ."&action_car_update_from_fakturabank=1";
  redirect();
  break;
case 'send_paycheck': // sending a paycheck to FB
  $SalaryID     = $_SESSION['oauth_salary_id'];
  $SalaryConfID = $_SESSION['oauth_salary_conf_id'];
  unset($_SESSION['oauth_salary_id']);
  unset($_SESSION['oauth_salary_conf_id']);
  $fb_salary = new lodo_fakturabank_fakturabanksalary();
  $fb_salary->sendsalary($SalaryID, $SalaryConfID);
  redirect();
  break;
case 'send_invoice': // sending an invoice to FB
  $fb = new lodo_fakturabank_fakturabank();
  $fb->write($_SESSION['oauth_invoice_object']);
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
  if ($_SESSION['oauth_action'] != "test") $_SESSION['oauth_tmp_redirect_back_url'] = "";
  unset($_SESSION['oauth_action']);
  redirect();
  break;
}

?>
