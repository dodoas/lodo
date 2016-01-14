<?
$primarykey['OccupationID'] = $OccupationID;
$occupation_OccupationID = (int) $_lib['input']->getProperty('occupation_OccupationID');
$occupation_Import = (string) $_lib['input']->getProperty('occupation_Import');

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
elseif($_lib['input']->getProperty('action_occupation_import')) {
    $selected_value = array();
    // here we split selected value by ' ' and we pass 3 to explode function because $selected_name should contain blanks if any.
    $selected_value = explode(' ', $occupation_Import, 3);

    $selected_ynr = $selected_value[0];
    $selected_lnr = $selected_value[1];
    $selected_name = $selected_value[2];

    $max_occupation_id = "select max(OccupationID) as maxID from $db_table";
    $result = $_lib['db']->db_query($max_occupation_id);
    $maxID = (int) $_lib['db']->db_fetch_object($result)->maxID;

    $exits_query  = "select OccupationID from $db_table where name like '%" . $selected_name . "%'";
    $result = $_lib['db']->db_query($exits_query);
    if($_lib['db']->db_numrows($result) == 0) {
      $query  = "insert into occupation (OccupationID, YNr, LNr, Name,  active) values (". ($maxID+1) .", '". $selected_ynr ."', '". $selected_lnr ."', '". $selected_name ."', 1)";
      $_lib['db']->db_insert2(array('query'=>$query));
    }
}

?>
