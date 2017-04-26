<?
class model_fakturabank_scheme {
  function update_all($args) {
    global $_SETUP, $_lib;
    $print_html = isset($args["html"]) && $args["html"] == "true";
    $with_details = isset($args["with_details"]) && $args["with_details"] == "true";

    $time_start = microtime(true);
    includelogic('invoicerecurring/recurring');
    includelogic('tablemetadata/tablemetadata');
    includelogic('accountplan/scheme');

    $backup_db_connection = $_lib['storage'] = $_lib['db'];

    // get the database list
    $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
    $dbs = $model_invoicerecurring_recurring->database_list();
    $json = lodo_accountplan_scheme::fetchSchemesFromFakturaBank();

    foreach ($dbs as $db) {
      $db_name = $db->Database;

           // connect to this database
      $_lib['storage'] = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
       'database' => $db_name,
       'username' => $_SETUP['DB_USER_DEFAULT'],
       'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

      if ($print_html) {
        print "<br>Refreshing fakturabankscheme for: <b>". $db_name ."</b>.... ";
      } else {
        print "\nRefreshing fakturabankscheme for: ". $db_name .".... ";
      }

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
        if ($print_html) {
          if($updated > 0) {
            print "<span style='color: green'><b>$updated</b> schemes updated.</span>";
          } else {
            print "<span>Schemes were up to date.</span>";
          }
        } else {
          if($updated > 0) {
            print "$updated schemes updated.";
          } else {
            print "Schemes were up to date.";
          }
        }

      }
    }

    $_lib['storage'] = $_lib['db'] = $backup_db_connection;
    $time_end = microtime(true);
    $time = $time_end - $time_start;

    if ($print_html) {
      print "<br><br><b>Done.</b> Executed in ". number_format($time, 2) ." seconds.</br><br><hr><br>";
    } else {
      print "\n\n\nDone.\nExecuted in ". number_format($time, 2) ." seconds.\n";
    }

  }
}

?>
