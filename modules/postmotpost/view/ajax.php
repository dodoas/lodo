<?php

switch ($_POST['type']) {
  case 'invoice':
    $q = "UPDATE voucher SET matched_by='invoice' WHERE VoucherID=" . $_POST['id'] . "";
    $result = $_lib['db']->db_query($q);
    break;
  case 'kid':
    $q = "UPDATE voucher SET matched_by='kid' WHERE VoucherID=" . $_POST['id'] . "";
    $result = $_lib['db']->db_query($q);
    break;
  case 'match':
    $q = "UPDATE voucher SET matched_by='match' WHERE VoucherID IN (SELECT VoucherID FROM vouchermatch WHERE VoucherMatchID=" . $_POST['id'] . ")";
    $result = $_lib['db']->db_query($q);
    break;
}


?>
