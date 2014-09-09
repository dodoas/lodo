<?php
  if($_REQUEST['action_year_new'])
  {
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_STRING);
    //$periodsQuery = "INSERT INTO expense_periods ('year') VALUES ($year)";
    $periodsQuery = "INSERT INTO expense_periods (year) VALUES ($year)";
    $periodsList = $_lib['db']->db_query($periodsQuery);
  }

  if($_lib['input']->getProperty('action_lines_save')) {
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_STRING);
    $period_id = filter_input(INPUT_POST, 'period_id', FILTER_SANITIZE_STRING);

    $data = array();
    $newdata = array();

    foreach ($_POST as $key => $value) {
      if(str_starts_with($key, 'expense_lines')) {

        # The field is not names, so we are going to insert it
        if(is_array($_POST[$key])) {

          # Loop over all items in each iteration, and add values to specific index
          for ($i = 0; $i < count($_POST[$key]); $i++) {
            if(isset($newdata[$i])) {
              # Add value to existing group
              $newdata[$i][$key] = $_POST[$key][$i];
            } else {
              # Create a group with initial value
              $newdata[$i] = array($key => $_POST[$key][$i]);
            }
          }
        }
        else {
          # Get the id of a field from field name
          $id = extract_id($key, '_');

          # Checking if the key exists, and if does, we insert the value in it
          if(isset($data[$id])) {
            $data[$id][$key] = $value;
          }
          else {
            # There is no array with that key, so we create it
            $data[$id] = array($key => $value);
          }
        }
      }
    }
    save_lines($data, $newdata, $_lib['db'], $department_id, $period_id);
  } else if($_lib['input']->getProperty('action_line_delete')) {
    $lineid = filter_input(INPUT_GET, 'LineID', FILTER_SANITIZE_STRING);
    $q = "DELETE FROM expense_lines WHERE id=$lineid";
    $_lib['db']->db_delete($q);
  }

  function save_lines($data, $newdata, $db, $department_id, $period_id) {
    # Update only dirty fields
    foreach ($data as $id => $values) {
      $sql = "UPDATE expense_lines SET ";
      $dvalues = array();
      foreach ($values as $key => $value) {
        if(extract_fieldname($key, 'expense_lines') == 'dirty' && $value == '1')
          $dirty = true;
        $dvalues[extract_fieldname($key, 'expense_lines')] = $value;
      }
      if($dirty) {
        $sql .= "supplier_name='" . $dvalues['supplier_name'] . "', ";
        $sql .= "beer_purchased=" . from_norway($dvalues['beer_purchased']) . ", ";
        $sql .= "wine_purchased=" . from_norway($dvalues['wine_purchased']) . ", ";
        $sql .= "spirits_purchased=" . from_norway($dvalues['spirits_purchased']) . " ";
        $sql .= "WHERE id=" . $id;
        $db->db_update($sql);
      }
    }

    # And insert new lines to the db
    foreach ($newdata as $id => $values) {
      $dvalues = array();
      foreach ($values as $key => $value) {
        if(extract_fieldname($key, 'expense_lines', false) == 'supplier_name' && isset($value))
          $insert = true;
        $dvalues[extract_fieldname($key, 'expense_lines')] = from_norway($value);
      }

      if($insert) {
        $sql = "INSERT INTO expense_lines (department_id, expense_period_id, supplier_name, beer_purchased, wine_purchased, spirits_purchased) ";
        $sql .= "VALUES (" . $department_id . ", " . $period_id . ", '" . $dvalues['supplier_name'] . "', " . $dvalues['beer_purchased'] . ", " . $dvalues['wine_purchased'] . ", " . $dvalues['spirits_purchased'] . ")";

        $db->db_insert($sql);
      }
    }
  }

  function extract_id($str, $delim) {
    return end(explode($delim, $str));
  }

  function extract_fieldname($str, $table, $include_id = true) {
    # Remove the table name and first _
    $draft = substr($str, strlen($table) + 1);

    # Mesure the length of id and one more _
    $idlen = strlen(extract_id($str, '_')) + 1;

    # Remove name of a field without extra junk
    if($include_id)
      return substr($draft, 0, strlen($draft) - $idlen);
    else
      return substr($draft, 0, strlen($draft));
  }

  function str_starts_with($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
  }

  function from_norway($str) {
    $num1 = str_replace(" ", "", $str);
    $num2 = str_replace(",", ".", $num1);
    return $num2;
  }

  function to_norway($str) {
    if($str != "")
      return number_format($str, 2, ',', ' ');
    else
      return "0,00";
  }
?>
