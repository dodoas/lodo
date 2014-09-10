<?php
  require_once "record.inc";

  $year = filter_input(INPUT_GET, 'Period', FILTER_SANITIZE_STRING);
  $cid = filter_input(INPUT_GET, 'Department', FILTER_SANITIZE_STRING);

  $departmentQuery = "SELECT * FROM companydepartment WHERE CompanyDepartmentID=$cid";
  $department = $_lib['db']->db_fetch_object($_lib['db']->db_query($departmentQuery));

  $yearQuery = "SELECT * FROM expense_periods WHERE year=$year";
  $yearObj = $_lib['db']->db_fetch_object($_lib['db']->db_query($yearQuery));

  create_defaults($yearObj->id, $cid, $_lib['db']);

  $linesQuery = "SELECT * FROM expense_lines WHERE department_id=" . $cid . " AND expense_period_id=" . $yearObj->id;
  $linesList = $_lib['db']->db_query($linesQuery);

  $groupsQuery = "SELECT * FROM expense_groups WHERE department_id=" . $cid . " AND expense_period_id=" . $yearObj->id . " ORDER BY group_id";
  $groupList = $_lib['db']->db_query($groupsQuery);

  $projectsQuery = "SELECT * FROM expense_projects WHERE department_id=" . $cid . " AND expense_period_id=" . $yearObj->id;
  $projectList = $_lib['db']->db_query($projectsQuery);

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
    <table id="add_table" class="lodo_data bordered">
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
        <tr></tr>
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

  <hr>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <table class="lodo_data bordered">
      <thead>
        <tr>
          <th></th>
          <th>Lager liter den 1 jan</th>
          <th>Kjøp liter Øl</th>
          <th>Kjøp liter Vin</th>
          <th>Kjøp liter Brennevin</th>
          <th>Lager liter den 31 des</th>
          <th>Salg Året <?= $year ?></th>
          <th>Forventet Året <?= $year ?></th>
          <th>Forventet Året <?= $year + 1 ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tabindex = 1000;
          $tabindexH[1] = $tabindex;


          while($group = $_lib['db']->db_fetch_object($groupList))
          {
            $dirtyname = 'expense_groups_dirty_' . $group->id;
            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";

            echo "<tr>";
            switch ($group->group_id) {
              case 1:
                echo "<td>Varegrupp 1<br />2,5% til 4,7% alkohol</td>";
                break;
              case 2:
                echo "<td>Varegrupp 1<br />4,7% til 22% alkohol</td>";
                break;
              case 3:
                echo "<td>Varegrupp 1<br />22% til 60% alkohol</td>";
                break;
            }
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_start_year', 'pk' => $group->id, 'value' => to_norway($group->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            switch ($group->group_id) {
              case 1:
                $sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(beer_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td class=\"number\">" . $sum->total . "</td>";
                echo "<td></td>";
                echo "<td></td>";
                break;
              case 2:
                $sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(wine_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td></td>";
                echo "<td class=\"number\">" . $sum->total . "</td>";
                echo "<td></td>";
                break;
              case 3:
                $sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(spirits_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td></td>";
                echo "<td></td>";
                echo "<td class=\"number\">" . $sum->total . "</td>";
                break;
              default:
                echo "<td></td>";
                break;
            }

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_end_year', 'pk' => $group->id, 'value' => to_norway($group->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . to_norway($group->stock_level_start_year + $sum->total - $group->stock_level_end_year) . "</td>";

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_this_year', 'pk' => $group->id, 'value' => to_norway($group->expected_stock_level_this_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_next_year', 'pk' => $group->id, 'value' => to_norway($group->expected_stock_level_next_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "</tr>";

            $tabindexH[1]++;
          }
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td><input class="new-button" type="submit" name="action_groups_save" value="Save" /></td>
        </tr>
      </tfoot>
    </table>
  </form>

  <hr>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <table class="lodo_data bordered">
      <thead>
        <tr>
          <th></th>
          <th>Varelager 1 jan <?= $year ?></th>
          <th>Varelager 31 des <?= $year ?></th>
          <th>Varelager regulering</th>
          <th>Varekjøp</th>
          <th>Vare forbruk</th>
          <th>Salg Året <?= $year ?></th>
          <th>Fortjeneste i kr</th>
          <th>Fortjeneste i %</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tabindex = 1200;
          $tabindexH[1] = $tabindex;
          $sums = array();


          while($project = $_lib['db']->db_fetch_object($projectList))
          {
            $dirtyname = 'expense_projects_dirty_' . $project->id;
            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";

            echo "<tr>";

            echo "<td>" . $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT Heading FROM project WHERE ProjectID=" . $project->project_id))->Heading . "</td>";

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_start_year', 'pk' => $project->id, 'value' => to_norway($project->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_end_year', 'pk' => $project->id, 'value' => to_norway($project->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            echo "<td class=\"number\">" . to_norway($stock_diff = $project->stock_level_start_year - $project->stock_level_end_year) . "</td>";

            $rapport = new framework_logic_regnskapsrapport(array('Period' => '$year', 'LineID' => $_REQUEST['LineID'], 'DepartmentID' => $cid, 'ProjectID'=> $project->project_id));

            echo "<td class=\"number\">" . to_norway($varekjop =  $rapport->lineSumH[400]['ThisYearAmount']) . "</td>";
            // echo "<td class=\"number\">" . to_norway($varekjop =  69951.44) . "</td>";
            echo "<td class=\"number\">" . to_norway($totals = $stock_diff + $varekjop) . "</td>";
            echo "<td class=\"number\">" . to_norway($sales = $rapport->lineSumH[300]['ThisYearAmount']) . "</td>";
            // echo "<td class=\"number\">" . to_norway($sales = 148141.20) . "</td>";
            echo "<td class=\"number\">" . to_norway($total = $sales + -$totals) . "</td>";

            if($totals * $total != 0)
              echo "<td class=\"number\">" . to_norway($percent = (100 / $totals * $total)) . "</td>";
            else
              echo "<td class=\"number\">" . to_norway($percent = (100 / 1)) . "</td>";

            echo "</tr>";

            $sums[0] += $project->stock_level_start_year;
            $sums[1] += $stock_level_end_year->stock_level_start_year;
            $sums[2] += $stock_diff;
            $sums[3] += $varekjop;
            $sums[4] += $totals;
            $sums[5] += $sales;
            $sums[6] += $total;
            $sums[7] += $percent;

            $tabindexH[1]++;
          }
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td>sum</td>
          <?php foreach ($sums as $key => $value): ?>
            <td class="number"><?= to_norway($value) ?></td>
          <?php endforeach; ?>
        </tr>
        <tr>
          <td><input class="new-button" type="submit" name="action_projects_save" value="Save" /></td>
        </tr>
      </tfoot>
    </table>
  </form>
  <script>
  // $(document).ready(function() {
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
      $('#add_table tbody tr:last').after("<tr> \
        <td><input type=\"text\" name=\"expense_lines.supplier_name[]\" id=\"expense_lines.supplier_name[]\" value=\"\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\"></td> \
        <td><input type=\"text\" name=\"expense_lines.beer_purchased[]\" id=\"expense_lines.beer_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.wine_purchased[]\" id=\"expense_lines.wine_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.spirits_purchased[]\" id=\"expense_lines.spirits_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index + "\" maxlength=\"22\" class=\"number\"></td> \
        <td>0,00</td> \
      </tr>");
      $('#tabindex').val(index);
    }
  //   window.addline = addline;
  //   window.make_dirty = make_dirty;
  // });
  </script>
  </body>
</html>
