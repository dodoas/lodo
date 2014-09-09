<?php
  require_once "record.inc";

  $year = filter_input(INPUT_GET, 'Period', FILTER_SANITIZE_STRING);
  $cid = filter_input(INPUT_GET, 'Department', FILTER_SANITIZE_STRING);


  $departmentQuery = "SELECT * FROM companydepartment WHERE CompanyDepartmentID=$cid";
  $department = $_lib['db']->db_fetch_object($_lib['db']->db_query($departmentQuery));

  $yearQuery = "SELECT * FROM expense_periods WHERE year=$year";
  $yearObj = $_lib['db']->db_fetch_object($_lib['db']->db_query($yearQuery));

  $linesQuery = "SELECT * FROM expense_lines WHERE department_id=" . $cid . " AND expense_period_id=" . $yearObj->id;
  $linesList = $_lib['db']->db_query($linesQuery);

?>

  <?php print $_lib['sess']->doctype ?>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Empatix - Expenses report</title>
    <?php includeinc('head') ?>
  </head>

  <body>
  <?php
    includeinc('top');
    includeinc('left');
  ?>

  <h2>Expenses for <?= $department->DepartmentName ?> for year <?= $year ?> - <small>Året <?= $year ?> 1 jan <?= $year ?> 31 des <?= $year ?></small></h2>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <table class="lodo_data bordered">
      <thead>
        <tr>
          <th>Supplier name</th>
          <th>Øl 2,5% til 4,7%</th>
          <th>Vin 4,7% til 21%</th>
          <th>Brennevin 22% til 60%</th>
          <th>Sum Liter</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tabindex = 100;
          $tabindexH[1] = $tabindex;


          while($line = $_lib['db']->db_fetch_object($linesList))
          {
            $dirtyname = 'expense_lines_dirty_' . $line->id;

            echo "<tr>";

            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'supplier_name', 'pk' => $line->id, 'value' => $line->supplier_name, 'class' => '', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'beer_purchased', 'pk' => $line->id, 'value' => to_norway($line->beer_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'wine_purchased', 'pk' => $line->id, 'value' => to_norway($line->wine_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'spirits_purchased', 'pk' => $line->id, 'value' => to_norway($line->spirits_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1])) . "</td>";

            echo "<td>" . to_norway($line->beer_purchased + $line->wine_purchased + $line->spirits_purchased) . "</td>";
            echo "<td>" . "<a onclick=\"return confirm('Er du sikker?');\" href=\"" . $_lib['sess']->dispatch."t=expense.expenses&action_line_delete=1&LineID=" . $line->id . "&Period=" . $year . "&Department=" . $department->CompanyDepartmentID . "\">" .'<img src="/lib/icons/trash.gif">' . "</a>" . "</td>";
            echo "</tr>";
            $tabindexH[1]++;
          }
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td><input class="new-button" type="submit" name="action_lines_save" value="Save" /></td>
          <td><input onclick="addline();" type="button" class="new-button" value="+" /></td>
        </tr>
      </tfoot>
    </table>
  </form>
  <script>
  $(document).ready(function() {
    var sdata = {
      type: 'newline',
      period: '<?= $yearObj->id ?>',
      department: '<?= $cid ?>'
    }

    function make_dirty(element) {
      $(element).val(1);
    }

    function addline() {
      var index = parseInt($('#tabindex').val());
      $('.lodo_data tbody tr:last').after("<tr> \
        <td><input type=\"text\" name=\"expense_lines.supplier_name[]\" id=\"expense_lines.supplier_name[]\" value=\"\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\"></td> \
        <td><input type=\"text\" name=\"expense_lines.beer_purchased[]\" id=\"expense_lines.beer_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.wine_purchased[]\" id=\"expense_lines.wine_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.spirits_purchased[]\" id=\"expense_lines.spirits_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index + "\" maxlength=\"22\" class=\"number\"></td> \
        <td>0,00</td> \
      </tr>");
      $('#tabindex').val(index);
    }
    window.addline = addline;
    window.make_dirty = make_dirty;
  });
  </script>
  </body>
</html>
