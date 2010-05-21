<?
if(isset($_REQUEST['action_postpost_update'])) {
  $primarykey = "VoucherID";
  $_lib['db']->db_update_multi_record($_POST, $db_table, $primarykey);
}
elseif(isset($_REQUEST['action_postmotpost_close'])) {
    #print "Lukker post mot post<br>";
    $postmotpost->closePost($_REQUEST['MatchAccountPlanID'], $_REQUEST['MatchKid'], $_REQUEST['MatchInvoiceID']);
}
elseif(isset($_REQUEST['action_postmotpost_closeall'])) {
    $postmotpost->closeAllPosts();
}
elseif(isset($_REQUEST['action_postmotpost_openall'])) {
    $postmotpost->openAllPosts();
}
elseif(isset($_REQUEST['action_postmotpost_save_currency'])) {
    exchange::updateVoucherForeignCurrency();
}
else {
    /*
     * Search and handle closethis and openthis in $_REQUEST
     */
    $search1 = "action_postmotpost_closethis_";
    $search1_len = strlen($search1);
    $search2 = "action_postmotpost_openthis_";
    $search2_len = strlen($search2);

    foreach($_REQUEST as $k => $v) {
        if(substr($k, 0, $search1_len) == $search1) {
            $close_id = substr($k, $search1_len);
            $postmotpost->closeAllPostsAccount($close_id);
            
            break;
        }
        else if(substr($k, 0, $search2_len) == $search2) {
            $open_id = substr($k, $search2_len);
            $postmotpost->openAllPostsAccount($open_id);
            
            break;
        }
    }
}
?>