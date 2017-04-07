<?php
include('reportcodes.php');
$year = $_REQUEST['year'];
$salaryreportaccountid = $_GET['SalaryReportAccountID'];
?>
<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
     <? includeinc('head') ?>

<script type='text/javascript'>
var report_year = <?= $year ?>;
var salaries = [];
var codes = <?= json_encode($codes); ?>;

<?php

$salary_lines_query = "
  SELECT
    s.JournalID,
    s.AccountPlanID,
    sl.SalaryCode,
    sl.AmountThisPeriod,
    sl.EnableVacationPayment,
    YEAR(s.JournalDate) AS VoucherCreatedYear,
    YEAR(s.ActualPayDate) AS AlltinReportedYear
  FROM salaryline sl JOIN salary s ON s.SalaryID = sl.SalaryID
";
$salary_lines_result = $_lib['db']->db_query($salary_lines_query);
while ($salary_line = $_lib['db']->db_fetch_object($salary_lines_result)) {
  if ($salary_line->SalaryCode) {
?>
var salary_journal_id = <?= json_encode($salary_line->JournalID); ?>;
var salary_line_code = <?= json_encode($salary_line->SalaryCode); ?>;
var salary_line_amount = <?= $salary_line->AmountThisPeriod ?>;
var salary_line_vacation_money = <?= $salary_line->EnableVacationPayment ?>;
if (!salaries.hasOwnProperty(salary_journal_id)) {
  salaries[salary_journal_id] = [];
  salaries[salary_journal_id]['AccountPlanID'] = <?= $salary_line->AccountPlanID ?>;
  salaries[salary_journal_id]['VoucherCreatedYear'] = <?= (int)$salary_line->VoucherCreatedYear ?>;
  salaries[salary_journal_id]['AlltinReportedYear'] = <?= (int)$salary_line->AlltinReportedYear ?>;
}
if (!salaries[salary_journal_id].hasOwnProperty(salary_line_code)) {
  salaries[salary_journal_id][salary_line_code] = 0;
}
salaries[salary_journal_id][salary_line_code] += salary_line_amount;
if (!salaries[salary_journal_id].hasOwnProperty('000')) {
  salaries[salary_journal_id]['000'] = 0;
}
if (salary_line_vacation_money == 1) {
  salaries[salary_journal_id]['000'] += salary_line_amount;
}
<?php
  }
}
?>
function updateAmountsFromSalariesArray() {
  var salary_journal_id_element = document.getElementById('reportaccount.SalaryJournalID');
  var salary_journal_id = salary_journal_id_element.value;
  if (salaries.hasOwnProperty(salary_journal_id)) {
    // console.log("Voucher Year " + salaries[salary_journal_id]['VoucherCreatedYear']);
    // console.log("Alltin Reported Year " + salaries[salary_journal_id]['AlltinReportedYear']);
    var different_year = 
      (salaries[salary_journal_id]['VoucherCreatedYear'] != report_year)
      || (salaries[salary_journal_id]['AlltinReportedYear'] != report_year);
    var different_year_checkbox_element = document.getElementById('DifferentYear');
    if (different_year) {
      different_year_checkbox_element.checked = true;
      // console.log("Different Year");
    } else {
      different_year_checkbox_element.checked = false;
    }
    var negate_amounts = 
      (salaries[salary_journal_id]['VoucherCreatedYear'] == report_year)
      && (salaries[salary_journal_id]['AlltinReportedYear'] > report_year);
    // if (negate_amounts) console.log("Negate");
    var salary_acccountplan_id = salaries[salary_journal_id]['AccountPlanID'];
    var salary_acccountplan_element = document.getElementsByName('reportaccount.accountplanid')[0];
    var accountplan_options = salary_acccountplan_element.options;
    for(var i = 0; i < accountplan_options.length; i++) {
      var option = accountplan_options[i];
      if (option.value == salary_acccountplan_id) {
        salary_acccountplan_element.selectedIndex = i;
        break;
      }
    }
    codes.forEach(
      function(code) {
        var salary_code_amount_elements = document.getElementsByName('amounts['+code+']');
        var amount = 0;
        if (salaries[salary_journal_id].hasOwnProperty(code)) {
          amount = salaries[salary_journal_id][code];
          // console.log("amount for code " + code + " is " + salaries[salary_journal_id][code]);
        } else {
          // amount = 0;
          // console.log("code " + code + " not set for salary " + salary_journal_id);
        }
        if (negate_amounts) {
          amount = -amount;
        }
        salary_code_amount_elements[0].value = amount;
      }
    );
  }
}

</script>
</head>
<body>

     <? includeinc('top') ?>
     <? includeinc('left') ?>


<?php
printf('<form action="%st=salary.employeereport&year=%d" method="post">', $_lib['sess']->dispatch, $year);
printf('<input type="hidden" value="%d" name="SalaryReportAccountID" />', $salaryreportaccountid);

print('<table><tr><th>Konto</th><th>L&oslash;nn</th>');

foreach($codes as $c) {
    printf('<th>%s</th>', $c);
}

print('<th>Forskjellige &aring;r</th>');
print('<th>Kommentar</th>');
print('</tr><tr><td>');

print $_lib['form3']->accountplan_number_menu(array('table' => 'reportaccount', 'field' => 'accountplanid',
                                                    'value' => 10001, 'type' => array(0 => 'balance', 1 => 'result', 2 => 'employee')));
print('</td>');

print('<td>');

print $_lib['form3']->text(
  array(
    'table' => 'reportaccount',
    'field' => 'SalaryJournalID',
    'tabindex' => 0
  )
);
print('</td>');

foreach($codes as $c) {
    printf('<td><input type="input" name="amounts[%s]" value="" /></td>', $c);
}

print("<td><input type=\"checkbox\" id=\"DifferentYear\" name=\"DifferentYear\"/></td>");
print('<td><input type="text" name="comment" value="" /></td>');
print('<tr><td><input type="submit" name="add_report_account" value="Save" /></td></tr>');
print('<tr><td><input type="button" name="update_from_salary_journal_id" value="Oppdater fra l&oslash;nn automatisk" onclick="updateAmountsFromSalariesArray();" /></td></tr>');

