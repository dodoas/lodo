<?
// var_dump($_POST);
// die();

//trim the spaces on InvoiceID, KID and MatchNumber
foreach($_POST as $key => $value) {
  if ((strstr($key, "voucher_InvoiceID") !== false) ||
      (strstr($key, "voucher_KID") !== false) ||
      (strstr($key, "vouchermatch_MatchNumber") !== false)) {
    $_POST[$key] = trim($value);
  }
}

if(isset($_REQUEST['action_postpost_update'])) {
  $primarykey = "VoucherID";
  $_lib['db']->db_update_multi_record($_POST, $db_table, $primarykey);
  $_lib['db']->db_update_multi_record($_POST, 'vouchermatch', 'VoucherMatchID');
}
elseif(isset($_REQUEST['action_postmotpost_close'])) {
    #print "Lukker post mot post<br>";
    //$postmotpost->closePost($_REQUEST['MatchAccountPlanID'], $_REQUEST['MatchKid'], $_REQUEST['MatchInvoiceID']);
    $postmotpost->closeVoucher($_REQUEST['MatchAccountPlanID'], $_REQUEST['MatchVoucherID']);
}
elseif(isset($_REQUEST['action_postmotpost_closeall'])) {
    $postmotpost->closeAllPosts();
}
elseif(isset($_REQUEST['action_postmotpost_openall'])) {
    $postmotpost->openAllPostsForOpenPeriods();
}
elseif(isset($_REQUEST['action_postmotpost_save_currency'])) {
    exchange::updateVoucherForeignCurrency();
}
elseif(isset($_REQUEST['action_postmotpost_closeselected']) && isset($_REQUEST['selectedClose'])) {
    foreach($_REQUEST['selectedClose'] as $acc) {
        $postmotpost->closeAllPostsAccount($acc);
    }
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
