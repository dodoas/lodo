<?

$_lib['message']    = new message(array('dbserver'=> $_SETUP['DB_SERVER_DEFAULT'], 'dbname' => $_SETUP['DB_NAME_DEFAULT']));

if($_lib['input']->getProperty('action_run_migration')) {
  $scriptpath = $_SETUP['HOME_DIR'] ."/". $_POST['migration_MigrationName'];
  $db_name = $_POST['migration_Database'];
  print "RUNNING MIGRATION: <b>". $scriptpath ."</b><br>ON DATABASE: <b>". $db_name ."</b><br>";
  $migration_system->migrate_db($db_name, $scriptpath);
} 
elseif ($_lib['input']->getProperty('action_update_schema')) {
  $db_name = $_POST['migration_Database'];
  $migration_system->update_db($db_name);
  print $_lib['message']->get();
}

?>