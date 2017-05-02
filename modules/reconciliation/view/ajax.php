<?php
// Respond to ajax request to update matched_by
$Type = $_POST['type'];
$Value = $_POST['newValue'];
$VoucherID = $_POST['id'];
if (in_array($Type, array('invoice', 'kid', 'match'))) {
  $UpdateVoucherQuery = "
    UPDATE
      voucher
    SET
      matched_by = '$Value'
    WHERE
      VoucherID = $VoucherID";
  $_lib['db']->db_update($UpdateVoucherQuery);
}

?>
