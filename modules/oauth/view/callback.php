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
case 'get_identificators': // fetch all scheme types(identificators) from FB
  $schemes = $_SESSION['oauth_resource']['result'];
  $scheme_control = new lodo_accountplan_scheme($_SESSION['oauth_account_plan_id']);
  $scheme_control->refreshSchemes($schemes);
  redirect();
  break;
case 'get_invoice_closing_reasons': // fetch all invoice closing reasons from FB
  $reasons = $_SESSION['oauth_resource']['result'];
  $fbreconcilationreason = new lodo_fakturabank_invoicereconciliationreason();
  $fbreconcilationreason->import_mappings($reasons);
  redirect();
  break;
case 'get_bank_transaction_closing_reasons': // fetch all bank transaction closing reasons from FB
  $reasons = $_SESSION['oauth_resource']['result'];
  $fbreconcilationreason = new lodo_fakturabank_bankreconciliationreason();
  $fbreconcilationreason->import_mappings($reasons);
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
  redirect();
  break;
case 'send_invoice': // sending an invoice to FB
  if ($_SESSION['oauth_resource']['code'] == 400) $_SESSION['oauth_invoice_error'] = "Error: opprette faktura: " . $_SESSION['oauth_resource']['result'];
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
case 'get_company_info': // fetching conpany info from FB
  $AccountPlanID = $_SESSION['oauth_account_plan_id'];
  unset($_SESSION['oauth_account_plan_id']);
  $org = new lodo_orgnumberlookup_orgnumberlookup($AccountPlanID);
  $org->setData($_SESSION['oauth_resource']['result']);
  if($org->success) {
    $_lib['message']->add("Opplysninger er hentet automatisk basert p&aring; organisasjonsnummeret.");
    $dataH = array();

    // Only update if the fields contains a value
    if($org->OrgNumber)   $dataH['OrgNumber'] = $org->OrgNumber;
    if($org->AccountName) $dataH['AccountName'] = $org->AccountName;
    if($org->Email)       $dataH['Email'] = $org->Email;
    if($org->Mobile)      $dataH['Mobile'] = $org->Mobile;
    if($org->Phone)       $dataH['Phone'] = $org->Phone;
    if(!empty($org->ParentCompanyName))    $dataH['ParentName'] = $org->ParentCompanyName;
    if(!empty($org->ParentCompanyNumber))  $dataH['ParentOrgNumber'] = $org->ParentCompanyNumber;

    $dataH['EnableInvoiceAddress'] = 1;
    if($org->IAdress->Address1) $dataH['Address'] = $org->IAdress->Address1;
    if($org->IAdress->City)     $dataH['City'] = $org->IAdress->City;
    if($org->IAdress->ZipCode)  $dataH['ZipCode'] = $org->IAdress->ZipCode;

    if($org->IAdress->Country)  $dataH['CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);

    if($org->DomesticBankAccount) $dataH['DomesticBankAccount'] = $org->DomesticBankAccount;

    if($org->CreditDays) {
      $dataH['EnableCredit'] = 1;
      $dataH['CreditDays'] = $org->CreditDays;
    }
    if($org->MotkontoResultat1)	{
      $dataH['EnableMotkontoResultat'] = 1;
      $dataH['MotkontoResultat1'] = $org->MotkontoResultat1;
    }
    if($org->MotkontoResultat2)	{
      $dataH['EnableMotkontoResultat'] = 1;
      $dataH['MotkontoResultat2'] = $org->MotkontoResultat2;
    }
    if($org->MotkontoBalanse1) {
      $dataH['EnableMotkontoResultat'] = 1;
      $dataH['MotkontoBalanse1'] = $org->MotkontoBalanse1;
    }
    $dataH['AccountPlanID'] = $AccountPlanID;
    $dataH['Active'] = 1;

    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'debug' => false));
    redirect();
  }
  break;
default:  // else, not recognized action
  unset($_SESSION['oauth_action']);
  break;
}

?>
