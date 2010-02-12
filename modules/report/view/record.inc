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
}

?>