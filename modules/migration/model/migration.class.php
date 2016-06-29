<?php

includelogic('invoicerecurring/recurring');
includelogic('tablemetadata/tablemetadata');

class migration_system {
  function get_migrations_for_database($db_name) {
    global $_SETUP;
    global $_lib;
    $_lib['storage'] = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
                                                        'database' => $db_name,
                                                        'username' => $_SETUP['DB_USER_DEFAULT'],
                                                        'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

    $query = "SELECT * FROM migrations";
    $rs = $_lib['db']->db_query($query);

    $migrations_from_database = array();

    while($row = $_lib['db']->db_fetch_assoc($rs)) {
      $migrations_from_database[] = $row;
    }

    $migration_file_names = array_slice(scandir($_SETUP['HOME_DIR'] ."/db/changes/"), 2);

    $all_migrations = array();
    foreach ($migration_file_names as $m) {
      $all_migrations[] = array("MigrationName" => "db/changes/". $m);
    }

    // this replaces migrations if there are any of them in the database with the one from the database.
    foreach ($all_migrations as &$migration_from_file) {
      foreach ($migrations_from_database as $migration_from_database) {
        if($migration_from_file["MigrationName"] == $migration_from_database["MigrationName"]) {
          $migration_from_file = $migration_from_database;
          break;
        }
      }
    }

    // this adds the remaining migrations from the database to the array
    foreach ($migrations_from_database as $migration_from_database) {
      if(!in_array($migration_from_database, $all_migrations)) {
        $all_migrations[] = $migration_from_database;
      }
    }
    
    // find all the migrations you wish to skip
    $migrations_to_skip = array();

    $skipping_regex = '/\/0\d\d_|\/1[0-2]\d_|\/13[0-3]_/';
    foreach ($all_migrations as $migration) {
      if(preg_match($skipping_regex, $migration["MigrationName"])) {
        $migrations_to_skip[] = $migration;
      }
    }

    // remove all migrations that should be skipped
    $length = count($all_migrations);
    for($i=0; $i<$length; $i++) {
      if(in_array($all_migrations[$i], $migrations_to_skip)) {
        unset($all_migrations[$i]);
      }
    }

    return $all_migrations;
  }

  function get_migrations_for_all_databases() {
    $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
    $dbs = $model_invoicerecurring_recurring->database_list();

    $all_migrations = array();

    foreach ($dbs as $db) {
      $all_migrations[$db->Database] = $this->get_migrations_for_database($db->Database);
    }

    return $all_migrations;
  }

  function migrate_db($db_name, $migration_name) {
    // use line break html element as new line separator
    $model_tablemetadata_tablemetadata = new model_tablemetadata_tablemetadata("<br/>");
    $args = array();
    $args["scriptpath"] = $migration_name;
    $args["db_name"] = $db_name;
    return $model_tablemetadata_tablemetadata->runscriptall($args);
  }

  function update_db($db_name = null) {
    $model_tablemetadata_tablemetadata = new model_tablemetadata_tablemetadata("<br/>");
    if(!$db_name) {
      $model_tablemetadata_tablemetadata->updateall();
    } else {
      echo '<p>Updating schema information for: <b>'. $db_name .'</b></p>';
      $params["db_name"] = $db_name;
      $model_tablemetadata_tablemetadata->update_db($params);
    }
  }
}