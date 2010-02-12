<?
if($_REQUEST['action_avstemming_update'])
{
    $tables = array();
    $tables['mvaavstemming']    = 'MvaAvstemmingID';
    $tables['mvaavstemmingline'] = 'MvaAvstemmingLineID';
    $tables['mvaavstemminglinefield'] = 'MvaAvstemmingLineFieldID';
    //print_r($_POST);
    $_lib['db']->db_update_multi_table($_POST, $tables);
    #print "Lagre ferdig<br>";
}

elseif($_REQUEST['action_avstemming_new'])
{
    $avst->action_avstemming_new(array('year'=>$_REQUEST['Year'], 'db_table'=>$db_table, 'db_table2'=>$db_table2, 'db_table3'=>$db_table3));
}
?>
