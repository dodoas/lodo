<?php

includelogic('invoicerecurring/recurring');
includelogic('tablemetadata/tablemetadata');

class migration_system {
  private $skipping_regex = '/\/(0\d\d|1[0-2]\d|13[0-4])_/';

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

    // add all the migrations from db/changes/ folder into $all_migrations, except for the ones that should be skipped
    foreach ($migration_file_names as $m) {
      $m = 'db/changes/'.$m;
      if(!preg_match($this->skipping_regex, $m)) $all_migrations[] = array('MigrationName' => $m);
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

    // this adds the remaining migrations from the database to the array, except the ones that should be skipped
    foreach ($migrations_from_database as $migration_from_database) {
      if(!preg_match($this->skipping_regex, $migration_from_database["MigrationName"]) && !in_array($migration_from_database, $all_migrations)) {
        $all_migrations[] = $migration_from_database;
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

  function get_database_names() {
    $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
    $dbs = $model_invoicerecurring_recurring->database_list();

    $all_migrations = array();

    foreach ($dbs as $db) {
      $all_migrations[$db->Database] = array();
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

  function update_db($table_filter = "", $db_name = null) {
    $model_tablemetadata_tablemetadata = new model_tablemetadata_tablemetadata("<br/>");
    $params["tablefilter"] = $table_filter;
    if(!$db_name) {      
      $model_tablemetadata_tablemetadata->updateallskipsystemdbs($params);
    } else {
      echo '<p>Updating schema information for: <b>'. $db_name .'</b></p>';
      $params["db_name"] = $db_name;
      $model_tablemetadata_tablemetadata->update_db($params);
    }
  }
}