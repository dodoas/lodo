<?
if($_REQUEST['action_main_update']) {

  foreach ($_REQUEST as $key => $value) {
    $elements = explode("_", $key);
    if(count($elements) == 3) {
      $query = "update setup set value='$value' where Name='$elements[2]'";
      #print "$query<br>\n";
      $_lib['db']->db_update($query);
    }
  }
}
?>