<?php

includelogic('invoicerecurring/recurring');
includelogic('tablemetadata/tablemetadata');

class model_task_task {

  function database_list() {
    $model_invoicerecurring_recurring = new model_invoicerecurring_recurring();
    $dbs = $model_invoicerecurring_recurring->database_list();
    return $dbs;
  }

  static function get_database_names() {
    $dbs = self::database_list();
    foreach ($dbs as $db) {
      $ret[] = $db->Database;
    }
    return $ret;
  }

  function postmotpost_migrate() {
    global $_SETUP, $_lib;
    $start_time = time();
    $databases = self::database_list();
    $number_of_dbs = count($databases);
    $current_db_index = 0;
    foreach($databases as $db) {
      $db_start_time = $last_status_printout = time();
      $db_name = $db->Database;
      $_lib['storage'] = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
                                                          'database' => $db_name,
                                                          'username' => $_SETUP['DB_USER_DEFAULT'],
                                                          'password' => $_SETUP['DB_PASSWORD_DEFAULT']));
      echo "DB: '$db_name'(" . ($current_db_index+1) . "/$number_of_dbs) START\n";
      $_lib['db']->db_query("      
CREATE TABLE IF NOT EXISTS test (
ID int(11) NOT NULL,
Name varchar (25) NOT NULL,
PRIMARY KEY (ID, Name)
)"
);
      $_lib['db']->db_query("TRUNCATE TABLE test");
      $_lib['db']->db_query("TRUNCATE TABLE voucherreconciliation");
      $query_vouchers = "SELECT ParentVoucherID, ChildVoucherID FROM voucherstruct";
      $result_vouchers = $_lib['db']->db_query($query_vouchers);
      $vouchers = array();
      while($row = $_lib['db']->db_fetch_assoc($result_vouchers)) {
        $vouchers[] = $row;
      }
      $groups = array();
      foreach($vouchers as $voucher) {
        if (isset($groups[$voucher['ParentVoucherID']])) {
          $group_id = $groups[$voucher['ParentVoucherID']];
        } elseif (isset($groups[$voucher['ChildVoucherID']])) {
          $group_id = $groups[$voucher['ChildVoucherID']];
        } else {
          $group_id = $voucher['ParentVoucherID'];
        }
        $groups[$voucher['ParentVoucherID']] = $group_id;
        $groups[$voucher['ChildVoucherID']] = $group_id;

        // delay to test for big DBs
        // usleep(400);
        if (time() - $last_status_printout > 1) {
          $last_status_printout = time();
          echo strftime('%H:%M:%S') . " : STILL WORKING ON DB: '$db_name'\n";
        }
      }
      if (!empty($groups)) {
        $insert_groups_query = "INSERT INTO test(ID, Name) VALUES ";
        $insert_reconciliations_query = "INSERT INTO voucherreconciliation(ID, CreatedAt, CreatedBy) VALUES ";
        $values_for_group = array();
        $values_for_reconcilation = array();
        foreach($groups as $id => $group_name) {
          $values_for_group[] = "($id, $group_name)";
          $values_for_reconcilation[$group_name] = "($group_name, NULL, NULL)";
        }
        $insert_groups_query .= implode(',', $values_for_group);
        $insert_reconciliations_query .= implode(',', $values_for_reconcilation);
        $_lib['db']->db_query($insert_groups_query);
        $_lib['db']->db_query($insert_reconciliations_query);
      }
      $update_vouchers_query = "UPDATE voucher v INNER JOIN test t ON v.VoucherID = t.ID SET v.VoucherReconciliationID = t.Name";
      $_lib['db']->db_update($update_vouchers_query);
      $update_vouchers_query = "UPDATE voucher v INNER JOIN vouchermatch vm ON v.VoucherID = vm.VoucherID SET v.MatchNumber = vm.MatchNumber";
      $_lib['db']->db_update($update_vouchers_query);
      echo "DB: '$db_name'(" . ($current_db_index+1) . "/$number_of_dbs) DONE\n";
      $current_db_index++;
      $_lib['db']->db_query("DROP TABLE test");
    }
  }
}
