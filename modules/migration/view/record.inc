<?

$_lib['message']    = new message(array('dbserver'=> $_SETUP['DB_SERVER_DEFAULT'], 'dbname' => $_SETUP['DB_NAME_DEFAULT']));
$db_name = (isset($_POST['migration_Database'])) ? $_POST['migration_Database'] : null;

if($_lib['input']->getProperty('action_run_migration')) {
  $scriptpath = $_SETUP['HOME_DIR'] ."/". $_POST['migration_MigrationName'];
  print "RUNNING MIGRATION: <b>". $scriptpath ."</b><br>";
  print "ON DATABASE: <b>". (is_null($db_name) ? 'all databases' : $db_name) ."</b><br>";
  $migration_system->migrate_db($db_name, $scriptpath);
} 
elseif ($_lib['input']->getProperty('action_update_schema')) {
  $migration_system->update_db($db_name);
  print $_lib['message']->get();
}

?>