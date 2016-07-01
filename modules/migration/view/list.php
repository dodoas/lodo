<html>
<head>
<style type='text/css'>
  * {
    font-size: 12px;
  }
</style>
</head>
<body>
<?php
if ($_lib['sess']->get_person('AccessLevel') < 4) {
  header('Location: ' . $_lib['sess']->dispatchs . "t=lodo.main");
  die();
}

includelogic("migration/migration");
global $_lib;

$migration_system = new migration_system();

$db_name_restriction = (isset($_REQUEST['db_name'])) ? $_REQUEST['db_name'] : NULL;
$show_only_failed = isset($_REQUEST['show_only_failed']);

require_once "record.inc";

if (is_null($db_name_restriction)) $all_migrations = $migration_system->get_migrations_for_all_databases();
else $all_migrations[$db_name_restriction] = $migration_system->get_migrations_for_database($db_name_restriction); 
?>
<form action="<? print $MY_SELF ?>" method="post">
  <input type="submit" name="action_update_schema" value="Update schema information on all DBs"/>
</form>
<?
foreach ($all_migrations as $database => $migrations) {
  print "<h2>". $database ."</h2>";
  ?>
  <form action="<? print $MY_SELF ?>" method="post">
    <input type="hidden" name="migration_Database" value="<? print $database; ?>">
    <input type="submit" name="action_update_schema" value="Update schema information"/>
  </form>
  <?
  print "<table>";
  foreach ($migrations as $migration) {
    $not_finished = $migration["Status"] == "STARTED";
    $finished = $migration["Status"] == "OK";
    $not_started = !$migration["Status"];

    if($not_started) $row_style = "";
    if($finished) $row_style = " style='color: green;'";
    if($not_finished) $row_style = " style='color: red;'";

    if (($show_only_failed && $not_finished) || !$show_only_failed) {
      print "<tr". $row_style .">";

      print "<td>". $migration["MigrationName"] ."</span></td>";
      print "<td>". $migration["StartedAt"] ." - </span></td>";
      print "<td>". $migration["SucceededAt"] ."</span></td>";

   ?>
        <form action="<? print $MY_SELF ?>" method="post">
          <input type="hidden" name="migration_MigrationName" value="<? print $migration['MigrationName']; ?>">
          <input type="hidden" name="migration_Database" value="<? print $database; ?>">
          <td><input type="submit" name="action_run_migration" value="Run migration"/></td>
        </form>
        <form action="<? print $MY_SELF ?>" method="post">
          <input type="hidden" name="migration_MigrationName" value="<? print $migration['MigrationName']; ?>">
          <td><input type="submit" name="action_run_migration" value="Run migration on all DBs"/></td>
        </form>
   <?


      print "</tr>";
    }
  }
  print "</table>";
}
?>
</body>
</html>
