<?
if(isset($_REQUEST['action_postpost_update'])){
  $primarykey = "VoucherID";
  $_lib['db']->db_update_multi_record($_POST, $db_table, $primarykey);
}
elseif(isset($_REQUEST['action_postmotpost_close'])){
    #print "Lukker post mot post<br>";
    $postmotpost->closePost($_REQUEST['MatchAccountPlanID'], $_REQUEST['MatchKid'], $_REQUEST['MatchInvoiceID']);
}
elseif(isset($_REQUEST['action_postmotpost_closeall'])){
    $postmotpost->closeAllPosts();
}
elseif(isset($_REQUEST['action_postmotpost_openall'])){
    $postmotpost->openAllPosts();
}
?>