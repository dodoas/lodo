<?

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
} elseif($_REQUEST['action_altinn_report1_add_account']) {
  $new_account = array(
    'accountsforaltinnreport_AccountPlanID' => 0
  );
  $_lib['db']->db_new_hash($new_account, $db_table);
} elseif($_REQUEST['action_altinn_report1_update']) {
  $tables = array(
    $db_table => 'ID'
  );
  $_lib['db']->db_update_multi_table($_POST, $tables);
} elseif($_REQUEST['action_altinn_report1_remove_account']) {
  $id = $_REQUEST['ID'];
  if ($id) {
    $primary_key = array(
      'ID' => $id
    );
    $_lib['db']->db_delete_hash($db_table, $primary_key);
  }
}

?>
