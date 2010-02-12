<?
$primarykey['CompanyDepartmentID'] = $CompanyDepartmentID;

if($_lib['input']->getProperty('action_department_update')) {
  #Possible to extend or alter parameters here
  #print_r($_POST);
  #print_r($primarykey);
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_department_new'))
{
	
	if($_lib['input']->getProperty('companydepartment_CompanyDepartmentID') > 0) {

		$query  = "select CompanyDepartmentID from $db_table where CompanyDepartmentID=" . (int) $_lib['input']->getProperty('companydepartment_CompanyDepartmentID') . "";
		#print "$query<br>\n";
		$result = $_lib['db']->db_query($query);
		if($_lib['db']->db_numrows($result) == 0)
		{
			$post = $_POST;
			$post['companydepartment_ValidFrom'] = "NOW()";
			$post['companydepartment_Active']  = 1;
	
			$query  = "select CompanyDepartmentID from $db_table where CompanyDepartmentID=" . (int) $_lib['input']->getProperty('companydepartment_CompanyDepartmentID') . "";
			#print "$query<br>\n";
			$_lib['db']->db_insert2(array('query'=>"insert into companydepartment (CompanyDepartmentID, ValidFrom, Active) values (". (int) $post['companydepartment_CompanyDepartmentID'].", '". (int) $post['companydepartment_ValidFrom']."', '". (int) $post['companydepartment_Active']."')"));
			$CompanyDepartmentID = $_POST['companydepartment_CompanyDepartmentID'];
		}
		else
		{
			$CompanyDepartmentID = $_POST['companydepartment_CompanyDepartmentID'];
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