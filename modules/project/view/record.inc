<?
#print "record<br>";

$primarykey['ProjectID'] = $ProjectID;

if($_lib['input']->getProperty('action_project_update')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_project_new'))
{
	#print "record1<br>";
    $query="select ProjectID from $db_table where ProjectID='" . $_POST['project_ProjectID'] . "'";
    $result = $_lib['db']->db_query($query);
    if($_lib['db']->db_numrows($result) == 0)
    {
    	if($_POST['project_ProjectID'] > 0) {
			$post = $_POST;
			$post['project_CompanyID'] = $_lib['sess']->defcompany_id;
			$post['project_Meldt_av']  = $_lib['sess']->get_person('PersonID');
			$post['project_Active'] = 1;
			$query = "insert into project (ProjectID, CompanyID, RegisteredByPersonID, Active) values (".$post['project_ProjectID'].", ".$post['project_CompanyID'].", ".$post['project_Meldt_av'].", ".$post['project_Active'].")";
			#print "$query<br>";
			$_lib['db']->db_insert2(array('query'=> $query));
			$ProjectID = $_POST['project_ProjectID'];
        } else {
       		$_lib['message']->add(array('message' => "ProsjektID er ikke oppgitt"));
        }
    }
    else
    {
    	$_lib['message']->add(array('message' => "Prosjektet fantes fra f¿r"));
        $ProjectID=$_POST['project_ProjectID'];
    }
}
elseif($_lib['input']->getProperty('action_project_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>
