<?
$ExchangeID = $_POST['exchange_ExchangeID'];
assert(!is_int($ExchangeID)); #All main input should be int

if($_lib['input']->getProperty('action_exchange_update')) {
  $primarykey['ExchangeID'] = $ExchangeID;  
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
  record_inc_insert_log('update');
}
elseif($_lib['input']->getProperty('action_exchange_new')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_new_hash($_POST, $db_table);
  record_inc_insert_log('insert');
}
elseif($_lib['input']->getProperty('action_exchange_delete')) {
  $primarykey['ExchangeID'] = $ExchangeID;  
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
  record_inc_insert_log('delete');
}



function record_inc_insert_log($action) { 
  global $_lib;

  $LoggedTime = 'NOW()';
  $PersonID = $_lib['sess']->get_person('PersonID');
  $TableChanged = "'exchange'";
  $FieldChanged = "'". $action .": exchange.Currency, exchange.Amount'";
  $PrimaryKeyUsed = "'". $_POST['exchange_ExchangeID'] ."'";
  $IPAddressUsed = "'".$_SERVER['REMOTE_ADDR']."'";
  $ValueChanged = "'". $_POST['exchange_Currency'] .', '. $_POST['exchange_Amount'] ."'";
  $UserAgent = "'". $_lib['db']->db_escape($_SERVER['HTTP_USER_AGENT']) ."'";

  $query = "INSERT INTO `log`
			( `LoggedTime`,
			  `PersonID`,
			  `TableChanged`,
			  `FieldChanged`,
			  `PrimaryKeyUsed`,
			  `IPAddressUsed`,
			  `ValueChanged`,
			  `UserAgent`)
			VALUES (
			  $LoggedTime,
			  $PersonID,
			  $TableChanged,
			  $FieldChanged,
			  $PrimaryKeyUsed,
			  $IPAddressUsed,
			  $ValueChanged,
			  $UserAgent)";

  $_lib['db']->db_query($query);
}


?>