<?php
  require_once "record.inc";

  $login_period = $_lib['date']->get_this_period($_lib['sess']->get_session('LoginFormDate'));

  $start_period    = $_REQUEST['start_period'] ? $_REQUEST['start_period'] : $_lib['date']->get_first_period_in_year($login_period);
  $end_period    = $_REQUEST['end_period'] ? $_REQUEST['end_period'] : $login_period;
  $cid = $_REQUEST['department_id'] ? $_REQUEST['department_id'] : 0;
  // All logic around this report was made to depend on year and department.
  // Since request is changed to have from - to period (important just for one part of report),
  // we will keep rest of report to have set year dependent on from date.
  $year = $_lib['date']->get_this_year($start_period);

  $departmentQuery = "SELECT * FROM companydepartment WHERE CompanyDepartmentID=$cid";
  $department = $_lib['db']->db_fetch_object($_lib['db']->db_query($departmentQuery));

  create_year($year, $_lib['db']);

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

<form name="velg" action="<?= $MY_SELF ?>" method="post">
    <table border="0" cellspacing="0">
        <thead>
            <tr>
                <th>Fra Periode</th>
                <th>Til Periode</th>
                <th>Avdeling</th>
                <th>Til m&aring;ned</th>
            </tr>
            <tr>
                <th><?= $_lib['form3']->AccountPeriod_menu3(array('name' => 'start_period', 'value' => $start_period, 'noaccess' => '1')) ?></th>
                <th><?= $_lib['form3']->AccountPeriod_menu3(array('name' => 'end_period', 'value' => $end_period, 'noaccess' => '1')) ?></th>
                <th><?
                    $aconf = array();
                    $aconf['field']         = 'department_id';
                    $aconf['accesskey']     = 'D';
                    $aconf['value']         = $cid;
                    $_lib['form2']->department_menu2($aconf);
                    ?>
                </th>
                <th><input type="submit" value="Velg periode (V)" name="velg_periode" accesskey="V"></th>
            </tr>
    </table>
</form>

<br><br>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <input type="hidden" name="start_period" value="<?= $start_period ?>">
    <input type="hidden" name="end_period" value="<?= $end_period ?>">
    <table id="add_table" class="lodo_data bordered">
      <thead>
        <tr>
          <th></th>
          <th class="number">Supplier name</th>
          <th class="number">&Oslash;l 2,5% til 4,7%</th>
          <th class="number">Vin 4,7% til 21%</th>
          <th class="number">Brennevin 22% til 60%</th>
          <th class="number">Sum Liter</th>
          <th class="number"></th>
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

            echo "<tr id=" . $idname . "><td></td>";

            echo "<input type=\"hidden\" name=\"" . $dirtyname . "\" id=\"" . $dirtyname . "\" value=\"0\">";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'supplier_name', 'pk' => $line->id, 'value' => $line->supplier_name, 'class' => '', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'beer_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->beer_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'wine_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->wine_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_lines', 'field' => 'spirits_purchased', 'pk' => $line->id, 'value' => $_lib['format']->Amount($line->spirits_purchased), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1])) . "</td>";

            echo "<td class=\"number\">" . $_lib['format']->Amount($line->beer_purchased + $line->wine_purchased + $line->spirits_purchased) . "</td>";
            echo "<td class=\"number\">" . "<a onclick=\"return delete_line('#" . $idname . "', " . $line->id . ");\" href=\"" . $_lib['sess']->dispatch."t=expense.expenses&action_line_delete=1&LineID=" . $line->id . "&Period=" . $year . "&Department=" . $department->CompanyDepartmentID . "\">" .'<img src="/lib/icons/trash.gif">' . "</a>" . "</td>";
            echo "</tr>";
            $tabindexH[1]++;
          }
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td><input class="new-button" type="submit" name="action_lines_save" value="Lagre <? echo $year ?>" onclick="disable_multiple_submit(this, 'action_lines_save')"/></td>
          <td><input onclick="addline();" type="button" class="new-button" value="+" /></td>
        </tr>
      </tfoot>
    </table>
  </form>

  <hr>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <input type="hidden" name="start_period" value="<?= $start_period ?>">
    <input type="hidden" name="end_period" value="<?= $end_period ?>">
    <table class="lodo_data bordered">
      <thead>
        <tr>
          <th class="number"></th>
          <th class="number">Lager liter den 1 jan</th>
          <th class="number">Kj&oslash;p liter &Oslash;l</th>
          <th class="number">Kj&oslash;p liter Vin</th>
          <th class="number">Kj&oslash;p liter Brennevin</th>
          <th class="number">Lager liter den 31 des</th>
          <th class="number">Salg &Aring;ret <?= $year ?></th>
          <th class="number">Forventet &Aring;ret <?= $year ?></th>
          <th class="number">Forventet &Aring;ret <?= $year + 1 ?></th>
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
                echo "<td class=\"number\">Varegrupp 1<br />2,5% til 4,7% alkohol</td>";
                break;
              case 2:
                echo "<td class=\"number\">Varegrupp 1<br />4,7% til 22% alkohol</td>";
                break;
              case 3:
                echo "<td class=\"number\">Varegrupp 1<br />22% til 60% alkohol</td>";
                break;
            }
            echo "<td>" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_start_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            switch ($group->group_id) {
              case 1:
                $sum = $beer_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(beer_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                echo "<td class=\"number\"></td>";
                echo "<td class=\"number\"></td>";
                break;
              case 2:
                $sum = $wine_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(wine_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td class=\"number\"></td>";
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                echo "<td class=\"number\"></td>";
                break;
              case 3:
                $sum = $spirits_sum = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT SUM(spirits_purchased) as total FROM `expense_lines` WHERE department_id=$cid AND expense_period_id=" . $yearObj->id));
                echo "<td class=\"number\"></td>";
                echo "<td class=\"number\"></td>";
                echo "<td class=\"number\">" . $_lib['format']->Amount($sum->total) . "</td>";
                break;
              default:
                echo "<td></td>";
                break;
            }

            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'stock_level_end_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($salg = ($group->stock_level_start_year + $sum->total - $group->stock_level_end_year)) . "</td>";

            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_this_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->expected_stock_level_this_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_groups', 'field' => 'expected_stock_level_next_year', 'pk' => $group->id, 'value' => $_lib['format']->Amount($group->expected_stock_level_next_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
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
          <td><input class="new-button" type="submit" name="action_groups_save" value="Lagre <? echo $year ?>" onclick="disable_multiple_submit(this, 'action_groups_save')"/></td>
        </tr>
      </tfoot>
    </table>
  </form>

  <hr>

  <form action="" method="POST">
    <input type="hidden" name="period_id" value="<?= $yearObj->id ?>">
    <input type="hidden" name="department_id" value="<?= $cid ?>">
    <input type="hidden" name="start_period" value="<?= $start_period ?>">
    <input type="hidden" name="end_period" value="<?= $end_period ?>">
    <table class="lodo_data bordered">
      <thead>
        <tr>
          <th class="number"></th>
          <th class="number">Varelager 1 jan <?= $year ?></th>
          <th class="number">Varelager 31 des <?= $year ?></th>
          <th class="number">Varelager regulering</th>
          <th class="number">Varekj&oslash;p fra <?= $start_period ?> til <?= $end_period ?></th>
          <th class="number">Vare forbruk</th>
          <th class="number">Salg &Aring;ret fra <?= $start_period ?> til <?= $end_period ?></th>
          <th class="number">Fortjeneste i kr</th>
          <th class="number">Fortjeneste i %</th>
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

            echo "<tr>";

            echo "<td class=\"number\">" .  $project_name . "</td>";

            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_start_year', 'pk' => $project->id, 'value' => $_lib['format']->Amount($project->stock_level_start_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";
            echo "<td class=\"number\">" . $_lib['form3']->text(array('OnKeyUp' => 'make_dirty(\'#' . $dirtyname . '\')', 'table' => 'expense_projects', 'field' => 'stock_level_end_year', 'pk' => $project->id, 'value' => $_lib['format']->Amount($project->stock_level_end_year), 'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[1]++)) . "</td>";

            echo "<td class=\"number\">" . $_lib['format']->Amount($stock_diff = $project->stock_level_start_year - $project->stock_level_end_year) . "</td>";

            $rapport = new framework_logic_regnskapsrapport(array('StartPeriod' => $start_period, 'Period' => $end_period, 'LineID' => $_REQUEST['LineID'], 'DepartmentID' => $cid, 'ProjectID'=> $project->project_id));

            echo "<td class=\"number\">" . $_lib['format']->Amount($varekjop = get_year_amount($rapport, 400, $project_id)) . "</td>";

            // echo "<td class=\"number\">" . $_lib['format']->Amount($varekjop =  69951.44) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($forbruk = $stock_diff + $varekjop) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($salg = abs(get_year_amount($rapport, 300, $project_id))) . "</td>";
            // echo "<td class=\"number\">" . $_lib['format']->Amount($salg = 3746054.80) . "</td>";
            echo "<td class=\"number\">" . $_lib['format']->Amount($fortjeneste = -$forbruk + $salg) . "</td>";

            if(($forbruk * $fortjeneste) != 0)
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

            $project_data[$project->id]['stock_diff'] = $stock_diff;
            $project_data[$project->id]['varekjop'] = $varekjop;
            $project_data[$project->id]['forbruk'] = $forbruk;
            $project_data[$project->id]['salg'] = $salg;
            $project_data[$project->id]['fortjeneste'] = $fortjeneste;
            $project_data[$project->id]['percent'] = $percent;

            $tabindexH[1]++;
          }
          $sums['percent'] = 100 / $sums['forbruk'] * $sums['fortjeneste'];
          echo "<input type=\"hidden\" value=\"" . $tabindexH[1] . "\" id=\"tabindex\">";
        ?>
      </tbody>
      <tfoot>
        <tr>
          <td class="number">sum</td>
          <?php foreach ($sums as $key => $value): ?>
            <td class="number"><?= $_lib['format']->Amount($value) ?></td>
          <?php endforeach; ?>
        </tr>
        <tr>
          <td><input class="new-button" type="submit" name="action_projects_save" value="Lagre <? echo $year ?>" onclick="disable_multiple_submit(this, 'action_projects_save')"/></td>
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
          <th class="number">Priser pr. liter u/mva</th>
          <?php for($i = 0; $i < 6; $i++): ?>
            <th class="number"></th>
          <?php endfor; ?>
          <th class="number">kr fortjeneste</th>
          <th class="number">% fortjeneste</th>
        </tr>
      </thead>
      <tbody>

      <?php
        while($project = $_lib['db']->db_fetch_object($projectList))
        {
          $project_name = $_lib['db']->db_fetch_object($_lib['db']->db_query("SELECT Heading FROM project WHERE ProjectID=" . $project->project_id))->Heading;
          if($project_name == iconv("UTF-8", "ISO-8859-1", 'Ø') . 'l' || $project_name == 'Vin' || $project_name == 'Brennevin') {
            echo "<tr>";
            echo "<td class=\"number\">" .  $project_name . "</td>";

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

    // Ugly hack for disabling multiple submit
    // Creation of input element is needed since record.inc depends on button name
    // which is not collected if we submit trough JS
    function disable_multiple_submit(el, name) {
      el.disabled = true;
      var html = document.createElement("input");
      html.name = name;
      html.type = "hidden";
      html.value = 1;
      el.form.appendChild(html);
      el.form.submit();
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
        <td></td> \
        <td class=\"number\"><input type=\"text\" name=\"expense_lines.supplier_name[]\" id=\"expense_lines.supplier_name[]\" value=\"\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\"></td> \
        <td class=\"number\"><input type=\"text\" name=\"expense_lines.beer_purchased[]\" id=\"expense_lines.beer_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td class=\"number\"><input type=\"text\" name=\"expense_lines.wine_purchased[]\" id=\"expense_lines.wine_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index++ + "\" maxlength=\"22\" class=\"number\"></td> \
        <td class=\"number\"><input type=\"text\" name=\"expense_lines.spirits_purchased[]\" id=\"expense_lines.spirits_purchased[]\" value=\"0,00\" size=\"22\" tabindex=\"" + index + "\" maxlength=\"22\" class=\"number\"></td> \
        <td class=\"number\">0,00</td> \
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
