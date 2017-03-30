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

$query = sprintf("SELECT * FROM salaryreportaccount WHERE SalaryReportAccountID = %d", $salaryreportaccountid);
$query_r = $_lib['db']->db_query($query);
$accountplanid_row = $_lib['db']->db_fetch_assoc($query_r);
$accountplanid = $accountplanid_row['AccountPlanID'];
$accountComment = $accountplanid_row['Comment'];
$differentYear = intval($accountplanid_row['DifferentYear']) === 1 ? "checked" : "";

printf('<form action="%st=salary.employeereport&year=%d" method="post">', $_lib['sess']->dispatch, $year);
printf('<input type="hidden" value="%d" name="SalaryReportAccountID" />', $salaryreportaccountid);

print('<table><tr><th>konto</th>');

$query = sprintf("SELECT * FROM salaryreportaccountentries WHERE SalaryReportAccountID = %d", $salaryreportaccountid);
$query_r = $_lib['db']->db_query($query);

$data = array();
while($row = $_lib['db']->db_fetch_assoc($query_r)) {
    $c = $row['Code'];
    $a = $row['Amount'];
    $data[$c] = $a;
}

ksort($data);

foreach($data as $c => $a) {
    printf("<th>%s</th>", $c);
}

print('<th>Forskjellige &aring;r</th>');
print('<th>Kommentar</th>');
printf("</tr><tr>");

print("<td>");
print $_lib['form3']->accountplan_number_menu(array('table' => 'reportaccount', 'field' => 'accountplanid',
                                                    'value' => $accountplanid, 'type' => array(0 => 'balance', 1 => 'result', 2 => 'employee')));
print('</td>');

foreach($data as $c => $a)
    printf('<td><input style="text-align: right;" type="input" name="amounts[%s]" value="%s" /></td>', $c, $_lib['format']->Amount($a));

print("<td><input type=\"checkbox\" name=\"DifferentYear\" " . $differentYear ." /></td>");
print("<td><input type=\"text\" name=\"comment\" value=\"$accountComment\" /></td>");
print('<tr><td><input type="submit" name="edit_report_account" value="Save" /></td></tr>');

