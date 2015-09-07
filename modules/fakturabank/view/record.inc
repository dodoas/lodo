<?
$fakturabank = new lodo_fakturabank_fakturabank();


if($_lib['input']->getProperty('action_fakturabank_registerincoming')) {
    #cleanup from prior developer -eirhje 23.01.10
    #print "Registrer innkommende<br>\n";
    $fakturabank->registerincoming();

} elseif($_lib['input']->getProperty('action_fakturabank_registeroutgoing')) {
    $fakturabank->registeroutgoing();

} elseif($_lib['input']->getProperty('action_fakturabank_addmissingaccountplan')) {
    // get ids from tmp table
    $new_tmp_accounts_hash = array_keys($_lib['storage']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "select AccountPlanID from accountplantemp order by AccountPlanID")));
    // update temporary table and create(copy to main account plan table) if possible for each missing account plan
    foreach ($new_tmp_accounts_hash as $tmp_acc_id) {
      $update_query = "";
      if (isset($_REQUEST['accountplantemp_' . $tmp_acc_id . '_AccountPlanID']) || isset($_REQUEST['accountplantemp_' . $tmp_acc_id . '_Motkonto'])) {
        // get new value for account plan id
        $new_id_value = $_REQUEST['accountplantemp_' . $tmp_acc_id . '_AccountPlanID'];
        // if id is empty add error and skip
        if (empty($new_id_value)) {
          $_lib['message']->add("AccountPlanID kan ikke v&aelig;re tomt");
          continue;
        }
        // if id is not a number add error and skip
        if (!is_numeric($new_id_value)) {
          $_lib['message']->add("AccountPlanID m&aring; v&aelig;re et tall");
          continue;
        }
        // get temp table data for the current id
        $new_accountplan_data = $_lib['db']->get_row(array('query' => "select * from accountplantemp where AccountPlanID = $tmp_acc_id"));
        // check if there is a conflicting id in the account plan table
        $acc_already_exists = $_lib['db']->get_row(array('query' => "select AccountPlanID from accountplan where AccountPlanID = $new_id_value"));
        // if no conflict in the main table, build query
        if (!$acc_already_exists) {
          // get new value for motkonto
          $new_motkonto_value = $_REQUEST['accountplantemp_' . $tmp_acc_id . '_Motkonto'];
          if (isset($_REQUEST['accountplantemp_' . $tmp_acc_id . '_Motkonto']) && !empty($new_motkonto_value)) {
            // get data for the newly selected motkonto
            $new_motkonto_data = $_lib['db']->get_row(array('query' => "select * from accountplan where AccountPlanID = $new_motkonto_value"));
            // build motkonto part of query depending on type
            if ($new_motkonto_data->AccountPlanType == 'result') {
              $update_query .= "EnableMotkontoResultat = 1, EnableMotkontoBalanse = 0, MotkontoBalanse1 = NULL, MotkontoResultat1 = $new_motkonto_value ";
            }
            elseif ($new_motkonto_data->AccountPlanType == 'balance') {
              $update_query .= "EnableMotkontoBalanse = 1, EnableMotkontoResultat = 0, MotkontoResultat1 = NULL, MotkontoBalanse1 = $new_motkonto_value ";
            }
          }
          // if id changed, add to query
          if (isset($_REQUEST['accountplantemp_' . $tmp_acc_id . '_AccountPlanID']) && $new_id_value != $tmp_acc_id) {
            if (!empty($update_query)) $update_query .= ", ";
            $update_query .= "AccountPlanID = $new_id_value";
          }
          $anything_changed = !empty($update_query);
          $update_query = "UPDATE accountplantemp SET " . $update_query;
          $update_query .= " WHERE AccountPlanID = $tmp_acc_id";
          // execute query to update temporary data, then copy(create) entry to main table
          if ($anything_changed) $_lib['db']->db_query($update_query);
          $_lib['db']->db_query("INSERT INTO accountplan(AccountPlanID, AccountName, InsertedDateTime, InsertedByPersonID, debittext, credittext,
                                                         Address, ZipCode, Email, DomesticBankAccount, EnableCredit, CreditDays, DebitColor, CreditColor,
                                                         Active, EnableMotkontoBalanse, MotkontoBalanse1, MotkontoResultat1, MotkontoResultat2,
                                                         EnableMotkontoResultat, City, OrgNumber, Phone, Mobile, EnableInvoiceAddress, AccountPlanType,
                                                         UpdatedByPersonID, CountryCode, Web, ParentName, ParentOrgNumber, EnablePostPost)
                                 SELECT AccountPlanID, AccountName, InsertedDateTime, InsertedByPersonID, debittext, credittext,
                                        Address, ZipCode, Email, DomesticBankAccount, EnableCredit, CreditDays, DebitColor, CreditColor,
                                        Active, EnableMotkontoBalanse, MotkontoBalanse1, MotkontoResultat1, MotkontoResultat2,
                                        EnableMotkontoResultat, City, OrgNumber, Phone, Mobile, EnableInvoiceAddress, AccountPlanType,
                                        UpdatedByPersonID, CountryCode, Web, ParentName, ParentOrgNumber, 1
                                 FROM accountplantemp WHERE AccountPlanID = $new_id_value");
          // add schemes to account plan if not NO:ORGNR
          if ($new_accountplan_data->FBSchemeType != 'NO:ORGNR') {
            $_lib['db']->db_query("INSERT INTO accountplanscheme(AccountPlanID, FakturabankSchemeID, SchemeValue)
                                   SELECT AccountPlanID, FBSchemeLodoID, FBSchemeValue
                                   FROM accountplantemp WHERE AccountPlanID = $new_id_value");
          }
        }
        else {
          // else add warning message, id already exists and skip create
          $_lib['message']->add("AccountPlanID " . $new_id_value . " er allerede i bruk!");
        }
      }
    }
} elseif($_lib['input']->getProperty('action_fakturabank_addmissingaccountplanoutgoing')) {
  $fakturabank->addmissingaccountplan();
} elseif(is_numeric($_lib['input']->getProperty('action_fakturabank_addsinglemissingaccountplan_id'))) {
    $fakturabank->addmissingaccountplan($_lib['input']->getProperty('action_fakturabank_addsinglemissingaccountplan_id'));
}

?>
