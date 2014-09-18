<?php

  includelogic('linetextmap/linetextmap');
  includelogic('report/regnskapsrapport');

  if($_REQUEST['action_year_new'])
  {
    $year = filter_input(INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT);
    //$periodsQuery = "INSERT INTO expense_periods ('year') VALUES ($year)";
    $periodsQuery = "INSERT INTO expense_periods (year) VALUES ($year)";
    $periodsList = $_lib['db']->db_query($periodsQuery);
  }

  if($_lib['input']->getProperty('action_lines_save')) {
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_NUMBER_INT);
    $period_id = filter_input(INPUT_POST, 'period_id', FILTER_SANITIZE_NUMBER_INT);

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
  } else if($_lib['input']->getProperty('action_groups_save')) {
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_NUMBER_INT);
    $period_id = filter_input(INPUT_POST, 'period_id', FILTER_SANITIZE_NUMBER_INT);

    $data = extract_data($_POST, 'expense_groups');

    # Save the group data
    foreach ($data as $id => $values) {
      $sql = "UPDATE expense_groups SET ";
      $dvalues = array();
      foreach ($values as $key => $value) {
        $dvalues[extract_fieldname($key, 'expense_groups')] = $value;
        if(extract_fieldname($key, 'expense_groups') == 'dirty' && $value == '1')
          $dirty = true;
      }
      if($dirty) {
        $sql .= "stock_level_start_year=" . from_norway($dvalues['stock_level_start_year']) . ", ";
        $sql .= "stock_level_end_year=" . from_norway($dvalues['stock_level_end_year']) . ", ";
        $sql .= "expected_stock_level_this_year=" . from_norway($dvalues['expected_stock_level_this_year']) . ", ";
        $sql .= "expected_stock_level_next_year=" . from_norway($dvalues['expected_stock_level_next_year']) . " ";
        $sql .= "WHERE id=" . $id;
        $_lib['db']->db_update($sql);
      }
    }
  } else if($_lib['input']->getProperty('action_projects_save')) {
    $department_id = filter_input(INPUT_POST, 'department_id', FILTER_SANITIZE_NUMBER_INT);
    $period_id = filter_input(INPUT_POST, 'period_id', FILTER_SANITIZE_NUMBER_INT);

    $data = extract_data($_POST, 'expense_projects');

    foreach ($data as $id => $values) {
      $sql = "UPDATE expense_projects SET ";
      $dvalues = array();
      foreach ($values as $key => $value) {
        $dvalues[extract_fieldname($key, 'expense_projects')] = $value;
        if(extract_fieldname($key, 'expense_projects') == 'dirty' && $value == '1')
          $dirty = true;
      }
      if($dirty) {
        $sql .= "stock_level_start_year=" . from_norway($dvalues['stock_level_start_year']) . ", ";
        $sql .= "stock_level_end_year=" . from_norway($dvalues['stock_level_end_year']) . " ";
        $sql .= "WHERE id=" . $id;
        $_lib['db']->db_update($sql);
      }
    }
  } else if($_lib['input']->getProperty('action_line_delete')) {
    $lineid = filter_input(INPUT_GET, 'LineID', FILTER_SANITIZE_NUMBER_INT);
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
        if(extract_fieldname($key, 'expense_lines', false) == 'supplier_name' && $value != '')
          $insert = true;
        $dvalues[extract_fieldname($key, 'expense_lines', false)] = from_norway($value);
      }

      if($insert) {
        $sql = "INSERT INTO expense_lines (department_id, expense_period_id, supplier_name, beer_purchased, wine_purchased, spirits_purchased) ";
        $sql .= "VALUES (" . $department_id . ", " . $period_id . ", '" . $dvalues['supplier_name'] . "', " . $dvalues['beer_purchased'] . ", " . $dvalues['wine_purchased'] . ", " . $dvalues['spirits_purchased'] . ")";

        $db->db_insert($sql);
      }
    }
  }

  # Create group and project reports if they don't exist
  # Executed on each request
  function create_defaults($period_id, $department_id, $db) {
    $groups = $db->db_numrows($db->db_query("SELECT * FROM expense_groups WHERE department_id=$department_id AND expense_period_id=$period_id"));

    if($groups != 3) {
      $db->db_insert("INSERT INTO expense_groups (group_id, department_id, expense_period_id) VALUES (1, " . $department_id . ", " . $period_id . ")");
      $db->db_insert("INSERT INTO expense_groups (group_id, department_id, expense_period_id) VALUES (2, " . $department_id . ", " . $period_id . ")");
      $db->db_insert("INSERT INTO expense_groups (group_id, department_id, expense_period_id) VALUES (3, " . $department_id . ", " . $period_id . ")");
    }

    $projects = $db->db_numrows($db->db_query("SELECT * FROM expense_projects WHERE department_id=$department_id AND expense_period_id=$period_id"));

    if($projects <= 0) {
      $projects = $db->db_query("SELECT * FROM project");
      while($project = $db->db_fetch_object($projects)) {
        $db->db_insert("INSERT INTO expense_projects (project_id, department_id, expense_period_id) VALUES (" . $project->ProjectID . ", " . $department_id . ", " . $period_id . ")");
      }
    }
  }

  function extract_data($raw, $table_name) {
    $data = array();

    foreach ($raw as $key => $value) {
      if(str_starts_with($key, $table_name)) {
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

    return $data;
  }

  function get_year_amount($rapport, $code, $project_name) {
    foreach($rapport->lineSumH as $lineH) {
      foreach ($rapport->lineH[$code] as $AccountH) {
        if($AccountH['AccountName'] == $project_name) {
          return $AccountH['ThisYearAmount'];
        }
      }
    }
  }

  function print_calculated_line($project, $group_id, $project_data, $group_data) {
    global $_lib;

    if($group_data[1]->stock_level_start_year == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($project->stock_level_start_year / $group_data[$group_id]->stock_level_start_year) . "</td>";

    if($group_data[$group_id]->stock_level_end_year == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($project->stock_level_end_year / $group_data[$group_id]->stock_level_end_year) . "</td>";

    echo "<td class=\"number\"> - </td>";

    if($group_data[$group_id]->sum == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($project_data[$project->id]['varekjop'] / $group_data[$group_id]->sum) . "</td>";

    if($group_data[$group_id]->salg == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($num1 = $project_data[$project->id]['forbruk'] / $group_data[$group_id]->salg) . "</td>";

    if($group_data[$group_id]->salg == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($num2 = $project_data[$project->id]['salg'] / $group_data[$group_id]->salg) . "</td>";

    echo "<td class=\"number\">" .   $_lib['format']->Amount($krfort = ($num2 - $num1)) . "</td>";

    if($num1 == 0 || $krfort == 0)
      echo "<td class=\"number\"> - </td>";
    else
      echo "<td class=\"number\">" .  $_lib['format']->Amount($percent = (100 / $num1 * $krfort)) . "</td>";
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
?>
