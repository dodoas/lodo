<?
$primarykey['Code'] = $_REQUEST['Code'];  

if($_REQUEST['action_arbeidsgiveravgift_update']) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_REQUEST['action_arbeidsgiveravgift_new']) {
  #Possible to extend or alter parameters here
  //$_POST['arbeidsgiveravgift_TS'] = "NOW()";
  //$_lib['db']->db_new_hash($_POST, $db_table);
  $query = "insert into arbeidsgiveravgift set TS=now(), Code='$Code'";
  $_lib['db']->db_insert($query);
}
elseif($_REQUEST['action_arbeidsgiveravgift_delete']) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>