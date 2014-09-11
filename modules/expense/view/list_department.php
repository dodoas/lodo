<?php
  require_once "record.inc";

  $departmentsQuery = "select * from companydepartment";
  $departmentsList = $_lib['db']->db_query($departmentsQuery);
  $year = filter_input(INPUT_GET, 'Period', FILTER_SANITIZE_STRING);

?>

  <?php print $_lib['sess']->doctype ?>
  <head>
      <title>Empatix - Expense department list</title>
      <?php includeinc('head') ?>
  </head>

  <body>
  <?php
    includeinc('top');
    includeinc('left');
  ?>

  <table class="lodo_data">
    <thead>
      <tr>
        <th>Departments</th>
      </tr>
    </thead>
    <tbody>
      <?php
        while($departmentRow = $_lib['db']->db_fetch_object($departmentsList))
        {
          echo "<tr><td><a href=\"" . $_lib['sess']->dispatch."t=expense.expenses&Period=" . $year . "&Department=" . $departmentRow->CompanyDepartmentID . "\">" . $departmentRow->DepartmentName . "</a></td></tr>";
        }
      ?>
    </tbody>
  </table>
  </body>
</html>
