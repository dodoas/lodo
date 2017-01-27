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
$all_migrations = $migration_system->get_migration_contents();
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

  .sign_field {
    text-align: center;
    width: 10px;
  }

  .wide_field {
    width: 150px;
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
  var all_migrations = {};
    <?
      foreach ($all_migrations as $filename => $content) {
        $content = str_replace('"','\"', str_replace(array("\n", "\r"), '', nl2br($content)));
        print "all_migrations[\"$filename\"] = { content: \"".$content."\" };";
      }
    ?>

  var migration_search_cache = {};

  function searchMigrations(words) {
    if(migration_search_cache[words]) {
      return migration_search_cache[words];
    }

    var regex_string = "";
    for(var i=0; i<words.length; i++) {
      regex_string += words[i] + ".*";
    }
    var regex = new RegExp(regex_string, "i");

    var possible_migrations = [];
    for(var filename in all_migrations) {
      var migration = all_migrations[filename];
      if(migration["content"].match(regex)) {
        possible_migrations.push(filename);
      }
    }

    migration_search_cache[words] = possible_migrations;

    return possible_migrations;
  }

  function insertMigrationLines(button, migrations) {
    var row = $(button).parents("tr");
    var diff_details = row.next();
    var migration_list = diff_details.find("#migration_list");

    if(migration_list.html()) return;

    for(var i = 0; i<migrations.length; i++) {
      var filename = migrations[i];
      migration_list.append("<br><span class='dotted' onclick='toggleMigration(this)'>- " + filename + "</span><pre class='hidden migration_details'>" + all_migrations[filename]["content"] + "</pre>");
    }
  }

  function toggleDiff(button) {
    var row = $(button).parents("tr");
    var diff_details = row.next();

    if($(row).hasClass("expanded")) {
      $(button).text("Expand");
      row.removeClass("expanded");
      diff_details.addClass("hidden");
    } else {
      $(button).text("Hide");
      row.addClass("expanded");
      diff_details.removeClass("hidden");
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
  <hr>
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
          <tr class='". ($diff["Status"] == "missing" ? "red" : "green" ) ."'>
            <td class='sign_field'><b>". ($diff["Status"] == "missing" ? "-" : "+") ."</b></td>
            <td class='wide_field'>". $diff["TableName"] ."</td>
            <td class='wide_field'>". $diff["TableField"] ."</td>
            <td class='wide_field'>". $diff["FieldType"] ."</td>
            <td class='wide_field'>". ($diff["Status"] == "missing" ? "<button onclick='toggleDiff(this); insertMigrationLines(this, searchMigrations([\"". $diff["TableName"] ."\", \"". $diff["TableField"] ."\"]))'>Expand</button>" : "") ."</td>
          </tr>";

        if($diff["Status"] == "missing") {
          print "
            <tr class='hidden'>
              <td colspan='6'>
                Added to skeleton DB at: <b>". $diff["TS"] ."</b><br>
                Migrations which include '<b>". $diff["TableName"] ."</b>' and '<b>". $diff["TableField"] ."</b>':
                <div id='migration_list'></div>
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
