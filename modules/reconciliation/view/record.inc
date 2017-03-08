<?
// Trim the spaces on InvoiceID, KID and MatchNumber values
foreach ($_POST as $Key => $Value) {
  if ((strstr($Key, "voucher_InvoiceID") !== false)
    || (strstr($Key, "voucher_KID") !== false)
    || (strstr($Key, "vouchermatch_MatchNumber") !== false))
  {
    $_POST[$Key] = trim($Value);
  }
}

if (isset($_REQUEST['action_reconciliation_update'])) {
  $PrimaryKey = "VoucherID";
  $_lib['db']->db_update_multi_record($_POST, $DBTable, $PrimaryKey);
  $_lib['db']->db_update_multi_record($_POST, 'vouchermatch', 'VoucherMatchID');
} elseif (isset($_REQUEST['action_reconciliation_close'])) {
  $Reconciliation->closeVoucher($_REQUEST['MatchAccountPlanID'], $_REQUEST['MatchVoucherID']);
} elseif (isset($_REQUEST['action_reconciliation_closeall'])) {
  $Reconciliation->closeAllPosts();
} elseif (isset($_REQUEST['action_reconciliation_openall'])) {
  $Reconciliation->openAllPostsForOpenPeriods();
} elseif (isset($_REQUEST['action_reconciliation_closeselected'])
  && isset($_REQUEST['selectedClose']))
{
  foreach($_REQUEST['selectedClose'] as $Account) {
    $Reconciliation->closeAllPostsAccount($Account);
  }
} else {
  // Handle closethis and openthis actions by looping through $_REQUEST
  $CloseThisText = "action_reconciliation_closethis_";
  $CloseThisTextLength = strlen($CloseThisText);
  $OpenThisText = "action_reconciliation_openthis_";
  $OpenThisTextLength = strlen($OpenThisText);
  foreach ($_REQUEST as $Key => $Value) {
    if (substr($Key, 0, $CloseThisTextLength) == $CloseThisText) {
      $CloseID = substr($Key, $CloseThisTextLength);
      $Reconciliation->closeAllPostsAccount($CloseID);
      break;
    } elseif (substr($Key, 0, $OpenThisTextLength) == $OpenThisText) {
      $OpenID = substr($Key, $OpenThisTextLength);
      $Reconciliation->openAllPostsAccount($OpenID);
      break;
    }
  }
}

?>
