<?
if($_REQUEST['action_main_update']) {

  foreach ($_REQUEST as $key => $value) {
    $elements = explode("_", $key);
    if(count($elements) == 3) {
      $query = "update setup set value='$value' where Name='$elements[2]'";
      #print "$query<br>\n";
      $_lib['db']->db_update($query);
    }
  }
}
else if($_lib['input']->getProperty('action_refresh_all_fakturabank_schemes')) {
  $time_start = microtime(true);
  includelogic('invoicerecurring/recurring');
  includelogic('tablemetadata/tablemetadata');
  includelogic('accountplan/scheme');

  $with_details = $_POST['with_details'];
  
  $backup_db_connection = $_lib['storage'] = $_lib['db'];

  // get the database list
  $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
  $dbs = $model_invoicerecurring_recurring->database_list();
  $schemeControl = new lodo_accountplan_scheme(null);
  $json = $schemeControl->fetchSchemesFromFakturaBank();

  foreach ($dbs as $db) {
    $db_name = $db->Database;

    // connect to this database
    $_lib['storage'] = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
                                                        'database' => $db_name,
                                                        'username' => $_SETUP['DB_USER_DEFAULT'],
                                                        'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

    print "<br>Refreshing fakturabankscheme for: <b>". $db_name ."</b>.... ";

    $schemeControl = new lodo_accountplan_scheme(null);

    // get the old schemes, refresh schemes, and get schemes after update
    if($with_details) $old = $schemeControl->listAvailableTypes();
    $schemeControl->refreshSchemes($json);
    if($with_details) $new = $schemeControl->listAvailableTypes();

    if($with_details) {
      // compare schemes before and after
      $updated = 0;
      foreach ($new as $id => $scheme) {
        if ($scheme['FakturabankSchemeID'] != @$old[$id]['FakturabankSchemeID'] ||
            $scheme['FakturabankRemoteSchemeID'] != @$old[$id]['FakturabankRemoteSchemeID'] ||
            $scheme['SchemeType'] != @$old[$id]['SchemeType']) {
          $updated++;
        }
      }
      if($updated > 0) {
        print "<span style='color: green'><b>$updated</b> schemes updated.</span>";
      } else {
        print "<span>Schemes were up to date.</span>";
      }
    }
    
    
  }

  $_lib['storage'] = $_lib['db'] = $backup_db_connection;
  $time_end = microtime(true);
  $time = $time_end - $time_start;
  print "<br><br><b>Done.</b> Executed in ". number_format($time, 2) ." seconds.</br><br><hr><br>";
}
?>