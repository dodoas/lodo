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
case 'send_invoice': // sending an invoice to FB
  var_dump($_SESSION);
  var_dump($_SESSION['oauth_resource']);
  if ($_SESSION['oauth_resource']['code'] == 400) $_SESSION['oauth_invoice_error'] = "Error: opprette faktura: " . $_SESSION['oauth_resource']['result'];
  redirect();
  break;
case 'incoming_invoices': // fetching all approved invoices from FB
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
