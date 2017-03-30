<?php
$db_table = 'accountsforaltinnreport';
require_once "record.inc";

$login_form_date = $_lib['sess']->get_session('LoginFormDate');
$login_form_year = $_lib['date']->get_this_year($login_form_date);
$report_year = (isset($_REQUEST['report_Year'])) ? $_REQUEST['report_Year'] : $login_form_year;

$accounts_for_altinn_report_query = "
  SELECT
    *
  FROM
    $db_table
";
$accounts_for_altinn_report_result = $_lib['db']->db_query($accounts_for_altinn_report_query);
$accounts_for_altinn_report = array();
while ($row = $_lib['db']->db_fetch_object($accounts_for_altinn_report_result)) {
  array_push($accounts_for_altinn_report, $row);
}
?>
<html>
<head>
  <title>Altinn årlig rapport oppsett</title>
  <? includeinc('head') ?>
</head>
<body>
  <? includeinc('top') ?>
  <? includeinc('left') ?>
  <h2>Altinn årlig rapport oppsett</h2>
  <form method="post">
<?php
if (!empty($accounts_for_altinn_report)) {
  $report_year_config = array(
    'name' => 'report_Year',
    'value' => $report_year,
  );
  print $_lib['form3']->hidden($report_year_config);
?>
    <table class="lodo_data">
      <tr>
        <th class="menu">Aktiv</th>
        <th class="menu">Konto</th>
        <th class="menu"></th>
      </tr>
<?php
foreach ($accounts_for_altinn_report as $account) {
?>
      <tr>
        <td>
<?php
$active_checkbox_config = array(
  'table' => $db_table,
  'field' => 'Active',
  'pk' => $account->ID,
  'value' => $account->Active
);
print $_lib['form3']->checkbox($active_checkbox_config);
?>
        </td>
        <td>
<?php
$account_select_config = array(
  'table' => $db_table,
  'field' => 'AccountPlanID',
  'pk' => $account->ID,
  'value' => $account->AccountPlanID,
  'type' => array('hovedbok')
);
print $_lib['form3']->accountplan_number_menu($account_select_config);
?>
        </td>
        <td>
          <form name="delete_account" method="post">
<?
$report_year_config = array(
  'name' => 'report_Year',
  'value' => $report_year,
);
print $_lib['form3']->hidden($report_year_config);
$account_id_config = array(
  'name' => 'ID',
  'value' => $account->ID,
);
print $_lib['form3']->hidden($account_id_config);
$remove_button_config = array(
  'type' => 'submit',
  'name' => 'action_altinn_report1_remove_account',
  'value' => 'Slett'
);
print $_lib['form3']->Input($remove_button_config);
?>
          </form>
        </td>
      </tr>
<?php
}
?>
    </table>
    <br/>
<?php
  $save_button_config = array(
    'type' => 'submit',
    'name' => 'action_altinn_report1_update',
    'value' => 'Lagre'
  );
  print $_lib['form3']->Input($save_button_config);
}
$add_account_button_config = array(
  'type' => 'submit',
  'name' => 'action_altinn_report1_add_account',
  'value' => 'Ny konto'
);
print $_lib['form3']->Input($add_account_button_config);
?>
  </form>
  <br/><br/>
  <a href="<?= $_lib['sess']->dispatch; ?>report_Year=<?= $report_year; ?>&t=report.altinn_report1">Tilbake til rapport</a>
</body>
</html>
