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

  <?= $_lib['sess']->doctype ?>
  <head>
    <!-- <meta charset="utf-8"> -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Empatix - Expenses report</title>
    <?php includeinc('head') ?>
  </head>

  <body>
  <?php
    includeinc('top');
    includeinc('left');
  ?>

  <h2>Expenses for <?= $department->DepartmentName ?> for year <?= $year ?> - &Aring;ret <?= $year ?> 1 jan <?= $year ?> 31 des <?= $year ?></small></h2>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <table id="add_table" class="lodo_data bordered">
      <thead>
        <tr>
          <th>Supplier name</th>
          <th>&Oslash;l 2,5% til 4,7%</th>
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
            $idname = 'expense_lines_id_' . $line->id;

            echo "<tr id=" . $idname . ">";

            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'supplier_name', 'pk' => $line->id, 'value' => $line->supplier_name, 'class' => '', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'beer_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->beer_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'wine_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->wine_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'spirits_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->spirits_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1])) . "</td>";

            echo "<td>" . $_lib['format']->Amount($line->beer_purchased + $line->wine_purchased + $line->spirits_purchased) . "</td>";
            echo "<td>" . "<a onclick=\"return delete_line('#" . $idname . "', " . $line->id . ");\" href=\"" . $_lib['sess']->dispatch."t=expense.expenses&action_line_delete=1&LineID=" . $line->id . "&Period=" . $year . "&Department=" . $department->CompanyDepartmentID . "\">" .'<img src="/lib/icons/trash.gif">' . "</a>" . "</td>";
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
          <th>Kj&oslash;p liter &Oslash;l</th>
          <th>Kj&oslash;p liter Vin</th>
          <th>Kj&oslash;p liter Brennevin</th>
          <th>Lager liter den 31 des</th>
          <th>Salg &Aring;ret <?= $year ?></th>
          <th>Forventet &Aring;ret <?= $year ?></th>
          <th>Forventet &Aring;ret <?= $year + 1 ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tabindex = 1000;
          $tabindexH[1] = $tabindex;
          $group_data = array();


          while($group = $_lib['db']->db_fetch_object($groupList))
          {
            $dirtyname = 'expense_groups_dirty_' . $group->id;
            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";


            $group_data[$group->group_id] = $group;

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
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_start_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            switch ($group->group_id) {
              case 1:
                $sum = $beer_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(beer_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                echo "<td></td>";
                echo "<td></td>";
                break;
              case 2:
                $sum = $wine_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(wine_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td></td>";
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                echo "<td></td>";
                break;
              case 3:
                $sum = $spirits_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(spirits_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td></td>";
                echo "<td></td>";
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                break;
              default:
                echo "<td></td>";
                break;
            }

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_end_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($salg = ($group->stock_level_start_year + $sum->total - $group->stock_level_end_year)) . "</td>";

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_this_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->expected_stock_level_this_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_next_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->expected_stock_level_next_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "</tr>";

            $group_data[$group->group_id]->sum = $sum->total;
            $group_data[$group->group_id]->salg = $salg;

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
          <th>Varekj&oslash;p</th>
          <th>Vare forbruk</th>
          <th>Salg &Aring;ret <?= $year ?></th>
          <th>Fortjeneste i kr</th>
          <th>Fortjeneste i %</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $tabindex = 1200;
          $tabindexH[1] = $tabindex;
          $sums = array();
          $project_data = array();

          while($project = $_lib['db']->db_fetch_object($projectList))
          {
            $dirtyname = 'expense_projects_dirty_' . $project->id;
            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";

            $projectP = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT Heading, ProjectID FROM project WHERE ProjectID=" . $project->project_id));
            $project_name =  $projectP->Heading;
            $project_id = $projectP->ProjectID;
            //var_dump($project_name);

            echo "<tr>";

            echo "<td>" .  $project_name . "</td>";

            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_start_year', 'pk' => $project->id, 'value' => $_lib['format']->Amount($project->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_end_year', 'pk' => $project->id, 'value' => $_lib['format']->Amount($project->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            echo "<td class=\"number\">" . $_lib['format']->Amount($stock_diff = $project->stock_level_start_year - $project->stock_level_end_year) . "</td>";

            $rapport = new framework_logic_regnskapsrapport(array('Period' => $year . '-13', 'LineID' => $_REQUEST['LineID'], 'DepartmentID' => $cid, 'ProjectID'=> $project->project_id));

            echo "<td class=\"number\">" . $_lib['format']->Amount($varekjop = get_year_amount($rapport, 400, $project_id)) . "</td>";

            // echo "<td class=\"number\">" . $_lib['format']->Amount($varekjop =  69951.44) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($forbruk = $stock_diff + $varekjop) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($salg = get_year_amount($rapport, 300, $project_id)) . "</td>";
            // echo "<td class=\"number\">" . $_lib['format']->Amount($salg = 3746054.80) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($fortjeneste = $forbruk + $salg) . "</td>";

            if($forbruk * $fortjeneste != 0)
              echo "<td class=\"number\">" . $_lib['format']->Amount($percent = (100 / $forbruk * $fortjeneste)) . "</td>";
            else
              echo "<td class=\"number\">" . $_lib['format']->Amount($percent = (0)) . "</td>";

            echo "</tr>";

            $sums['stock_level_start_year'] += $project->stock_level_start_year;
            $sums['stock_level_end_year'] += $project->stock_level_end_year;
            $sums['stock_diff'] += $stock_diff;
            $sums['varekjop'] += $varekjop;
            $sums['forbruk'] += $forbruk;
            $sums['salg'] += $salg;
            $sums['fortjeneste'] += $fortjeneste;
            $sums['percent'] += $percent;

            $project_data[$project->id]['stock_diff'] = $stock_diff;
            $project_data[$project->id]['varekjop'] = $varekjop;
            $project_data[$project->id]['forbruk'] = $forbruk;
            $project_data[$project->id]['salg'] = $salg;
            $project_data[$project->id]['fortjeneste'] = $fortjeneste;
            $project_data[$project->id]['percent'] = $percent;

            $tabindexH[1]++;
          }
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td>sum</td>
          <?php foreach ($sums as $key => $value): ?>
            <td class="number"><?= $_lib['format']->Amount($value) ?></td>
          <?php endforeach; ?>
        </tr>
        <tr>
          <td><input class="new-button" type="submit" name="action_projects_save" value="Save" /></td>
        </tr>
      </tfoot>
    </table>
  </form>

  <hr>

  <?php
    $_lib['db']->db_seek($projectList, 0);
  ?>

  <table class="lodo_data bordered">
      <thead>
        <tr>
          <th>Priser pr. liter u/mva</th>
          <?php for($i = 0; $i < 6; $i++): ?>
            <th></th>
          <?php endfor; ?>
          <th>kr fortjeneste</th>
          <th>% fortjeneste</th>
        </tr>
      </thead>
      <tbody>

      <?php
        while($project = $_lib['db']->db_fetch_object($projectList))
        {
          $project_name = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT Heading FROM project WHERE ProjectID=" . $project->project_id))->Heading;
          if($project_name == iconv("UTF-8", "ISO-8859-1", 'Ø') . 'l' || $project_name == 'Vin' || $project_name == 'Brennevin') {
            echo "<tr>";
            echo "<td>" .  $project_name . "</td>";

            switch ($project_name) {
              case iconv("UTF-8", "ISO-8859-1", 'Ø') . 'l':
                print_calculated_line($project, 1, $project_data, $group_data);
                break;
              case 'Vin':
                print_calculated_line($project, 2, $project_data, $group_data);
                break;
              case 'Brennevin':
                print_calculated_line($project, 3, $project_data, $group_data);
                break;
            }

            echo "</tr>";
          }
        }
      ?>

      </tbody>
    </table>

  <script>

    function make_dirty(element) {
      $(element).val(1);
    }

    function delete_line (element, id) {
      if(confirm('Er du sikker?')) {
        $.post("<?= $_SETUP['DISPATCH'] . 't=expense.ajax' ?>",
        {
          type: 'delete_line',
          id: id
        },
        function(data,status){
          $(element).remove();
          return false;
        });
      }

      return false;
    }

    $('#add_table tbody tr:last input:last').keyup(function(e) {
      var code = e.keyCode || e.which;
      if (code == '9') {
        addline();
      }
    });

    function addline() {
      var index = parseInt($('#tabindex').val());
      $('#add_table tbody tr:last input:last').unbind();
      $('#add_table tbody tr:last').after("<tr> \
        <td><input type=\"text\" name=\"expense_lines.supplier_name[]\" id=\"expense_lines.supplier_name[]\" value=\"\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\"></td> \
        <td><input type=\"text\" name=\"expense_lines.beer_purchased[]\" id=\"expense_lines.beer_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.wine_purchased[]\" id=\"expense_lines.wine_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td><input type=\"text\" name=\"expense_lines.spirits_purchased[]\" id=\"expense_lines.spirits_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index + "\" maxlength=\"22\" class=\"number\"></td> \
        <td>0,00</td> \
      </tr>");
      $('#tabindex').val(index);
      $('#add_table tbody tr:last input:last').keyup(function(e) {
        var code = e.keyCode || e.which;
        if (code == '9') {
          addline();
        }
      });
    }
  </script>
  </body>
</html>
