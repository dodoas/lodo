<?php
  require_once "record.inc";

  $periodsQuery = "select * from expense_periods";
  $periodsList = $_lib['db']->db_query($periodsQuery);

?>

  <?php print $_lib['sess']->doctype ?>
  <head>
      <title>Empatix - Expense period list</title>
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
        <th>Periods</th>
      </tr>
    </thead>
    <tbody>
      <?php
        while($periodRow = $_lib['db']->db_fetch_object($periodsList))
        {
          echo "<tr><td align=\"center\"><a href=\"" . $_lib['sess']->dispatch."t=expense.list_department&Period=" . $periodRow->year . "\">" . $periodRow->year . "</a></td></tr>";
        }
      ?>
    </tbody>
  </table>
  <table border="0">
    <tbody>
      <form name="budget" action="<?= $_lib['sess']->dispatch ?>t=expense.list_year" method="post">
        <tr>
          <td>
            <?= $_lib['form3']->text(array('name' => 'year', 'value' => $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate')))) ?>
          </td>
        </tr>
        <tr>
          <td>
            <?= $_lib['form3']->input(array('type'=>'submit', 'name'=>'action_year_new', 'value'=>' Nytt år (N)', 'accesskey'=>'N')) ?>
          </td>
        </tr>
      </form>
    </tbody>
  </table>

  </body>
</html>
