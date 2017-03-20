<?
$primarykey['DepartmentID'] = $DepartmentID;

if($_lib['input']->getProperty('action_department_update')) {
  #Possible to extend or alter parameters here
  #print_r($_POST);
  #print_r($primarykey);
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_department_new'))
{

	if($_lib['input']->getProperty('department_DepartmentID') > 0) {

		$query  = "select DepartmentID from $db_table where DepartmentID=" . (int) $_lib['input']->getProperty('department_DepartmentID') . "";
		#print "$query<br>\n";
		$result = $_lib['db']->db_query($query);
		if($_lib['db']->db_numrows($result) == 0)
		{
			$post = $_POST;
			$post['department_ValidFrom'] = "NOW()";
			$post['department_Active']  = 1;

			$query  = "select DepartmentID from $db_table where DepartmentID=" . (int) $_lib['input']->getProperty('department_DepartmentID') . "";
			#print "$query<br>\n";
			$_lib['db']->db_insert2(array('query'=>"insert into department (DepartmentID, ValidFrom, Active) values (". (int) $post['department_DepartmentID'].", '". (int) $post['department_ValidFrom']."', '". (int) $post['department_Active']."')"));
			$DepartmentID = $_POST['department_DepartmentID'];
		}
		else
		{
			$DepartmentID = $_POST['department_DepartmentID'];
		}
	} else {
		$_lib['message']->add("Avdelingsnummer ikke oppgitt, pr&oslash;v p&aring; nytt");
	}
}
elseif($_lib['input']->getProperty('action_department_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>