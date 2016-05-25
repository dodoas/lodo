<?php

includelogic("migration/migration");
global $_lib;

$migration_system = new migration_system();

require_once "record.inc";

$all_migrations = $migration_system->get_migrations_for_all_databases();

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

    print "<tr". $row_style .">";

    print "<td>". $migration["MigrationName"] ."</span></td>";
    print "<td>". $migration["StartedAt"] ." - </span></td>";
    print "<td>". $migration["SucceededAt"] ."</span></td>";
    
    if($not_started || $not_finished) { ?>
      <form action="<? print $MY_SELF ?>" method="post">
        <input type="hidden" name="migration_MigrationName" value="<? print $migration['MigrationName']; ?>">
        <input type="hidden" name="migration_Database" value="<? print $database; ?>">

        <td><input type="submit" name="action_run_migration" value="Run migration"/></td>
      </form>
    <? }


    print "</tr>";
  }
  print "</table>";
}