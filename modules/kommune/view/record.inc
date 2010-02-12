<?
$primarykey['KommuneID'] = $KommuneID;

if($_REQUEST['action_kommune_update']) 
{
  #Possible to extend or alter parameters here
  //print_r($_POST);
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_REQUEST['action_kommune_new']) 
{
  #Possible to extend or alter parameters here
  //$_POST['kommune_TS'] = "NOW()";
  //$_lib['db']->db_new_hash($_POST, $db_table);
  $query = "insert into kommune set TS=now()";
  $_lib['db']->db_insert($query);
}
elseif($_REQUEST['action_kommune_delete']) 
{
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>