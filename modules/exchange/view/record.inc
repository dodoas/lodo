<?
$ExchangeID = $_POST['exchange_ExchangeID'];
assert(!is_int($ExchangeID)); #All main input should be int

if($_lib['input']->getProperty('action_exchange_update')) {
  $primarykey['ExchangeID'] = $ExchangeID;  
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_exchange_new')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_new_hash($_POST, $db_table);
}
elseif($_lib['input']->getProperty('action_exchange_delete')) {
  $primarykey['ExchangeID'] = $ExchangeID;  
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>