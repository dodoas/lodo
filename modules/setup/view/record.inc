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
else if($_lib['input']->getProperty('action_refresh_all_fakturabank_schemes')) {
  includelogic("fakturabank/scheme");
  $model_fakturabank_scheme = new model_fakturabank_scheme();
  $model_fakturabank_scheme->update_all(array('with_details' => $_POST['with_details'], 'html' => 'true'));
}
?>
