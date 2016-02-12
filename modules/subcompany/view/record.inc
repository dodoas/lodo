<?
$db_table = "subcompany";
$SubcompanyID = (int) $_lib['input']->getProperty('subcompany_SubcompanyID');
$primarykey['SubcompanyID'] = $SubcompanyID;

if($_lib['input']->getProperty('action_subcompany_update')) {
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_subcompany_new'))
{
	if($SubcompanyID > 0) {
		$query  = "SELECT SubcompanyID FROM $db_table WHERE SubcompanyID=" . $SubcompanyID . "";
		$result = $_lib['db']->db_query($query);
		if($_lib['db']->db_numrows($result) == 0)
		{
      $query_insert = "INSERT INTO subcompany(SubcompanyID, Name, OrgNumber) VALUES
                       (" . $SubcompanyID . ", '" . $_POST['subcompany_Name'] . "', '" . $_POST['subcompany_OrgNumber'] . "')";
			$_lib['db']->db_insert2(array('query' => $query_insert));
		}
	} else {
		$message = "Virksomhet(er) ikke oppgitt";
    header("Location: " . $_lib['sess']->dispatchs . "t=subcompany.list&message=" . $message);
	}
}
elseif($_lib['input']->getProperty('action_subcompany_delete')) {
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>
