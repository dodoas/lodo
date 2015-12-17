<?
$primarykey['OccupationID'] = $OccupationID;
$occupation_OccupationID = (int) $_lib['input']->getProperty('occupation_OccupationID');

if($_lib['input']->getProperty('action_occupation_update')) {
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_occupation_new'))
{
  if($occupation_OccupationID > 0) {
    $exits_query  = "select OccupationID from $db_table where OccupationID=" . (int) $occupation_OccupationID . "";
    $result = $_lib['db']->db_query($exits_query);
    if($_lib['db']->db_numrows($result) == 0) {
      $query  = "insert into occupation (OccupationID, active) values (". $occupation_OccupationID .", 1)";
      $_lib['db']->db_insert2(array('query'=>$query));
    }
    $OccupationID = $_POST['occupation_OccupationID'];
  } else {
    $_lib['message']->add("Yrkenr ikke oppgitt, pr&oslash;v p&aring; nytt");
    header("Location: " . $_lib['sess']->dispatchs . "t=occupation.list");
  }
}
elseif($_lib['input']->getProperty('action_occupation_delete')) {
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}

?>
