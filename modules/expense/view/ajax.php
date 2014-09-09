<?php

  $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
  $department = filter_input(INPUT_POST, 'department', FILTER_SANITIZE_NUMBER_INT);
  $period = filter_input(INPUT_POST, 'period', FILTER_SANITIZE_NUMBER_INT);

  // die(var_dump(array($type, $department, $period)));

  switch ($type) {
    case 'newline':
      // $id = $_lib['db']->db_insert("INSERT INTO `expense_lines` (department_id, expense_period_id) VALUES($department, $period)");
      header('Content-Type: application/json');
      echo json_encode(array("id" => 2));
      break;
  }

?>
