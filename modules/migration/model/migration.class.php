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

  function get_databases($with_migrations = true) {
    $dbs = self::database_list();

    $all_migrations = array();

    if($with_migrations) {
      foreach ($dbs as $db) {
        $all_migrations[$db->Database] = $this->get_migrations_for_database($db->Database);
      }
    } else {
      foreach ($dbs as $db) {
        $all_migrations[$db->Database] = array();
      }
    }

    return $all_migrations;
  }

  function database_list() {
    $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
    $dbs = $model_invoicerecurring_recurring->database_list();
    return $dbs;
  }

  function run_script_on_all_db($script) {
    $dbs = self::database_list();
    $query_results = array();

    foreach ($dbs as $db) {
      $query_results[$db->Database] = $this->run_script_on_db($db->Database, $script);
    }
    return $query_results;
  }

  function run_script_on_db($db_name, $script) {
    global $_SETUP;
    // use line break html element as new line separator
    $model_tablemetadata_tablemetadata = new model_tablemetadata_tablemetadata("<br/>");
    $args = array();
    $args["commands"] = explode(';', $script);
    $args['db_server'] = $_SETUP['DB_SERVER_DEFAULT'];
    $args['db_user'] = $_SETUP['DB_USER_DEFAULT'];
    $args['db_password'] = $_SETUP['DB_PASSWORD_DEFAULT'];
    $args["db_name"] = $db_name;
    return $model_tablemetadata_tablemetadata->runscriptondb($args);
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

  static function get_database_names() {
    $dbs = self::database_list();
    foreach ($dbs as $db) {
      $ret[] = $db->Database;
    }
    return $ret;
  }

  function check_database($db_name) {
    global $_SETUP;
    global $_lib;
    $_lib['storage'] = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
                                                        'database' => $db_name,
                                                        'username' => $_SETUP['DB_USER_DEFAULT'],
                                                        'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

    $query = "SELECT *
              FROM (
                SELECT 'missing' as Status, base.*
                FROM ". self::get_good_db_name() .".confdbfields as base
                LEFT JOIN ". $db_name .".confdbfields as comp ON base.TableField = comp.TableField AND base.TableName = comp.TableName AND base.FieldType = comp.FieldType
                WHERE comp.ConfDBFieldID is null

                UNION ALL

                SELECT 'excess' as Status, comp.* 
                FROM ". self::get_good_db_name() .".confdbfields as base
                RIGHT JOIN ". $db_name .".confdbfields as comp ON base.TableField = comp.TableField AND base.TableName = comp.TableName AND base.FieldType = comp.FieldType
                WHERE base.ConfDBFieldID is null
              ) as Results
              ORDER BY
                TableName ASC,
                TableField ASC
              ";

    $differences = array();
    $rs = $_lib['db']->db_query($query);
    while($row = $_lib['db']->db_fetch_assoc($rs)) {
      $differences[] = $row;
    }

    return $differences;
  }

  function get_migration_contents() {
    global $_SETUP;
    $migrations = array();
    $migration_file_names = array_slice(scandir($_SETUP['HOME_DIR'] ."/db/changes/"), 2);
    foreach($migration_file_names as $migration_name) {
      $content = file_get_contents($_SETUP['HOME_DIR'] ."/db/changes/". $migration_name);
      $migrations["db/changes/". $migration_name] = $content;
    }
    return $migrations;
  }

  static function get_good_db_name() {
    global $_SETUP;
    return $_SETUP['DB_CHECKER_GOOD_DB'];
  }
}