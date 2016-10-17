<?php
includelogic("migration/migration");
global $_lib;
if ($_lib['sess']->get_person('AccessLevel') < 4) {
  header('Location: ' . $_lib['sess']->dispatchs . "t=lodo.main");
  die();
}
require_once "record.inc";
$migration_system = new migration_system();
if($_REQUEST["db_name"]) {  
  $databases = array($_REQUEST["db_name"]);
} else {
  $databases = $migration_system->get_database_names();
}
?>
<html>
<head>
<style type='text/css'>
  * {
    font-size: 12px;
  }

  table {
    border-collapse: collapse;
  }

  .green {
    background-color: #eaffea;
  }
  .red {
    background-color: #ffecec;
  }
  .hidden {
    display: none;
  }
  .dotted {
    border-bottom: 1px dotted black;
    margin-left: 20px;
  }

  span.expanded {
    color: darkblue;
    font-weight: bold;
  }

  .migration_details {
    border: 1px solid black;
    padding: 3px;
  }
</style>

<script type="text/javascript"  src='/lib/js/jquery.js'></script>
<script type="text/javascript">
  function toggleRow(row) {
    if($(row).hasClass("expanded")) {
      $(row).removeClass("expanded");
      $(row).parents("table").find(".for_" + row.id).addClass("hidden");  
    } else {
      $(row).addClass("expanded");
      $(row).parents("table").find(".for_" + row.id).removeClass("hidden");
    }
  }
  function toggleMigration(row) {
    $(".migration_details").addClass("hidden");
    if($(row).hasClass("expanded")) {
      $(row).removeClass("expanded");
    } else {
      $("span.dotted").removeClass("expanded");
      $(row).addClass("expanded");
      $(row).next().removeClass("hidden");
    }
  }
</script>

</head>
<body>

<?php
if($_REQUEST["db_name"]) {
  print "<a href='". $_lib["sess"]->dispatch ."t=migration.list&db_name=". $_REQUEST["db_name"] ."''>Return to ". $_REQUEST["db_name"] ."</a>";
} else {
  print "<a href='". $_lib["sess"]->dispatch ."t=migration.list'>Return to all databases</a>";
}


foreach ($databases as $db_name) {
  $differences = $migration_system->check_database($db_name);
  ?>

  <h2>Comparing '<? print $db_name; ?>' to a good database ('<? print migration_system::get_good_db_name(); ?>')</h2>
  <?
    if(count($differences) == 0 || $db_name == migration_system::get_good_db_name()) {
      print "<span class='green'>All good! (y)</span>";
      continue;
    }
  ?>

  <table id="<? print $db_name; ?>">
    <thead>
      <th></th>
      <th>Table Name</th>
      <th>Field Name</th>
      <th>Field Type</th>
      <th></th>
      <th></th>
    </thead>
    <tbody>
      <?php
      foreach ($differences as $diff) {
        print "
          <tr id='". $diff["ConfDBFieldID"] ."' class='". ($diff["Status"] == "missing" ? "red" : "green" ) ."'>
            <td style='text-align: center; width: 10px;'><b>". ($diff["Status"] == "missing" ? "-" : "+") ."</b></td>
            <td style='width: 150px;'>". $diff["TableName"] ."</td>
            <td style='width: 150px;'>". $diff["TableField"] ."</td>
            <td style='width: 150px;'>". $diff["FieldType"] ."</td>
            <td style='width: 150px;'>". ($diff["Status"] == "missing" ? "<button onclick='toggleRow($(this).parents(\"tr\")[0])'>Expand</button>" : "") ."</td>
          </tr>";

        if($diff["Status"] == "missing") {
          print "
            <tr class='hidden for_". $diff["ConfDBFieldID"] ."'>
              <td colspan='6'>
                Added to skeleton DB at: <b>". $diff["TS"] ."</b><br>
                Migrations which include '<b>". $diff["TableName"] ."</b>' and '<b>". $diff["TableField"] ."</b>':";
                $possible_migrations = $migration_system->get_migrations_including(array($diff["TableName"], $diff["TableField"]));
                foreach ($possible_migrations as $migration_name => $content) {
                  print "<br><span class='dotted' onclick='toggleMigration(this)'>- ". $migration_name ."</span>";
                  print "<pre class='hidden migration_details'>". $content ."</pre>";
                }
          print "
              </td>
            </tr>
          ";
        }
      }
      ?>
    </tbody>
  </table>
<? } ?>

</body>
</html>
