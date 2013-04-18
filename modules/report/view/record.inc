<?
if($_REQUEST['action_varetelling_new'])
{
    $post = array();/*echo "<pre>";print_r($_REQUEST);exit;*/
    $post['varetelling_CreatedDate'] = $_POST['varetelling_CreatedDate'];
    $post['varetelling_Description'] = $_POST['varetelling_Description'];
    $post['varetelling_DepartmentID'] = $_POST['varetelling_DepartmentID'];
    $post['varetelling_Period'] = $_POST['varetelling_Period'];
    /*echo "<pre>";print_r($post);exit;*/
    $VareTellingID = $_lib['db']->db_new_hash($post, 'varetelling');
    
    $post = array();
    $post['varetelling_VareTellingID']   = $VareTellingID;
    
}
elseif($_REQUEST['action_varetelling_delete'])
{
    $VareTellingID = $_REQUEST['action_varetelling_delete'];
    
    $query = "DELETE from expense_heading where VareTellingID = '".$VareTellingID."' ";
	  $_lib['db']->db_query($query);
  
    $query = "DELETE from expense_line where VareTellingID = '".$VareTellingID."' ";
	  $_lib['db']->db_query($query);
  
    $query = "DELETE from varetelling where VareTellingID = '".$VareTellingID."' ";
	  $_lib['db']->db_query($query);
  
}


if($_REQUEST['action_oppdater_privatforbruk'])
{
   $primarykey = "Name";
   $_POST['setup_Value_expectedflowTax'] = $_lib['format']->ReversePercent(array('value'=>$_POST['setup_Value_expectedflowTax'], 'return'=>'value'));
   $_lib['db']->db_update_multi_record($_POST, $db_table, $primarykey);
}

elseif($_REQUEST['action_shortreport_update'])
{
    $primarykey['Period'] = $Period;
    $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}

?>