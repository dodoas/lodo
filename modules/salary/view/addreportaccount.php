<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
     <? includeinc('head') ?>

</head>
<body>

     <? includeinc('top') ?>
     <? includeinc('left') ?>


<?php

include('reportcodes.php');

$year = $_REQUEST['year'];
$salaryreportaccountid = $_GET['SalaryReportAccountID'];

printf('<form action="%st=salary.employeereport&year=%d" method="post">', $_lib['sess']->dispatch, $year);
printf('<input type="hidden" value="%d" name="SalaryReportAccountID" />', $salaryreportaccountid);

print('<table><tr><th>konto</th>');

foreach($codes as $c) {
    printf('<th>%s</th>', $c);
}

print('<th>Kommentar</th>');
print('</tr><tr><td>');

print $_lib['form3']->accountplan_number_menu(array('table' => 'reportaccount', 'field' => 'accountplanid',
                                                    'value' => 10001, 'type' => array(0 => 'balance', 1 => 'result', 2 => 'employee')));
print('</td>');

foreach($codes as $c) {
    printf('<td><input type="input" name="amounts[%s]" value="" /></td>', $c);
}

print('<td><input type="text" name="comment" value="" /></td>');
print('<tr><td><input type="submit" name="add_report_account" value="Save" /></td></tr>');

