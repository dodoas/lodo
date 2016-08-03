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
$show_all = isset($_REQUEST['show_all']);
$show_only_database_names = !$show_all && !$db_name_restriction;

require_once "record.inc";

if (is_null($db_name_restriction)) $all_migrations = $migration_system->get_databases();
else if($db_name_restriction) $all_migrations[$db_name_restriction] = $migration_system->get_migrations_for_database($db_name_restriction);
else if($show_only_database_names) $all_migrations = $migration_system->get_databases(false);

$current_page = $MY_SELF . ($show_all ? "show_all=1&" : "") . ($db_name_restriction ? "db_name=". $db_name_restriction ."&" : "") . ($show_only_failed ? "show_only_failed=1&" : "");
?>

<? if(!$show_all) { ?>
   - <a href="<? print $MY_SELF .'show_all=1&'. ($show_only_failed ? "show_only_failed=1&" : "") ?>">Show all databases</a><br>  
<? } ?>
<? if(!$show_only_database_names) { ?>
  <? if(!$show_only_failed) { ?>
     - <a href="<? print $MY_SELF . ($show_all ? "show_all=1&" : "") . ($db_name_restriction ? "db_name=".$db_name_restriction."&" : "") ?>show_only_failed=1">Show only failed migrations</a><br>  
  <? } else { ?>
     - <a href="<? print $MY_SELF . ($show_all ? "show_all=1&" : "") . ($db_name_restriction ? "db_name=".$db_name_restriction."&" : "") ?>">Show all migrations</a><br>  
  <? } ?>
   - <a href="<? print $MY_SELF ?>">Back to list</a>
<? } else { ?>
  <? if(!$show_only_failed) { ?>
     - <a href="<? print $MY_SELF . "show_all=1&show_only_failed=1"?>">Show only failed migrations for all databases</a><br>  
  <? } ?>
<? } ?>

<? if(!$show_only_database_names) { ?>
<form action="<? print $current_page ?>" method="post">
  <input type="submit" name="action_update_schema" value="Update schema information on all DBs"/>
  Table filter: <input type="text" name="migration_tablefilter">
</form>
<? } ?>
<?
foreach ($all_migrations as $database => $migrations) {
  print "<h2><a href='". $MY_SELF ."db_name=". $database."&". ($show_only_failed ? "show_only_failed=1&" : "") ."'>". $database ."</a></h2>";
  if(!$show_only_database_names) {
  ?>
    <form action="<? print $current_page ?>" method="post">
      <input type="hidden" name="migration_Database" value="<? print $database; ?>">
      <input type="submit" name="action_update_schema" value="Update schema information"/>
      Table filter: <input type="text" name="migration_tablefilter">
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
          <form action="<? print $current_page ?>" method="post">
            <input type="hidden" name="migration_MigrationName" value="<? print $migration['MigrationName']; ?>">
            <input type="hidden" name="migration_Database" value="<? print $database; ?>">
            <td><input type="submit" name="action_run_migration" value="Run migration"/></td>
          </form>
          <form action="<? print $current_page ?>" method="post">
            <input type="hidden" name="migration_MigrationName" value="<? print $migration['MigrationName']; ?>">
            <td><input type="submit" name="action_run_migration" value="Run migration on all DBs"/></td>
          </form>
        <?
        print "</tr>";
      }
    }
    print "</table>";
  }
}
?>
</body>
</html>
