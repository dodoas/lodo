<?php

  $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
  $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

  // die(var_dump(array($type, $department, $period)));

  switch ($type) {
    case 'delete_line':
      $_lib['db']->db_query("DELETE FROM `expense_lines` WHERE id=$id");
      echo "GOOOD";
      break;
  }

?>
