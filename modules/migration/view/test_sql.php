<html>
<head>
<style type='text/css'>
  * {
    font-size: 12px;
  }
  table {
    border-collapse: collapse;
  }
  table, th, td {
    border: 1px solid black;
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
$sql_query = (isset($_REQUEST['sql_query'])) ? $_REQUEST['sql_query'] : NULL;
$query_empty = !$sql_query;

if ($query_empty) $query_results = $migration_system->get_databases();
else {
  if($db_name_restriction) $query_results[$db_name_restriction] = $migration_system->run_script_on_db($db_name_restriction, $sql_query);
  else $query_results = $migration_system->run_script_on_all_db($sql_query);
}

$current_page = $MY_SELF . ($db_name_restriction ? "db_name=". $db_name_restriction ."&" : "");
?>

<? if(!$query_empty || $db_name_restriction) { ?>
   - <a href="<? print $MY_SELF ?>">Back to list</a>
<? } ?>

<form action="<? print $current_page ?>" method="post">
<textarea name="sql_query" rows='10' cols='60' placeholder="-- SQL query goes here"><? if (!$query_empty) print $sql_query; ?></textarea><br/>
  <br/>
  <input type="submit" name="action_run_test_sql" value="Go"/>
</form>
<?
foreach ($query_results as $database => $results) {
  print "<h2><a href='". $MY_SELF ."db_name=". $database ."'>". $database ."</a></h2>";
  if(!$query_empty && $results) {
    foreach ($results as $query => $result) {
      print "<table><tr><td>QUERY:</td><td>$query</td></tr></table><br/>";
      if ($result->num_rows == 0) {
        print "Empty result<br/>";
      } else {
        print "<table>";
        print "<thead><tr>";
        foreach ($result->fetch_fields() as $column_name) {
          print "<td>$column_name->name</td>";
        }
        print "</tr></thead>";
        print "<tbody>";
        while ($row = $result->fetch_assoc()) {
          print "<tr>";
          foreach ($row as $_column_name => $value) {
            print "<td>$value</td>";
          }
          print "</tr>";
        }
        print "</tbody>";
        print "</table><br/>";
      }
    }
  }
}
?>
</body>
</html>
