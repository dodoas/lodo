<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>

</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<?php

include('record_salaryreport.php');
includemodel('salary/salaryreport');

/*

CREATE TABLE  `salaryreport` (
`SalaryReportID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`Date` DATE NOT NULL ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Locked` BOOL NOT NULL ,
`LockedBy` INT( 11 ) NOT NULL
) ENGINE = MYISAM


CREATE TABLE  `salaryreportentries` (
`SalaryReportEntryID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SalaryReportID` INT( 11 ) NOT NULL ,
`Code` VARCHAR( 7 ) NOT NULL ,
`Amount` FLOAT NOT NULL
) ENGINE = MYISAM

CREATE TABLE  `salaryreportaccount` (
`SalaryReportAccountID` INT( 11 ) NOT NULL AUTO_INCREMENT ,
`AccountPlanID` INT( 11 ) NOT NULL ,
`Year` YEAR NOT NULL ,
`Locked` BOOL NOT NULL ,
`LockedBy` INT( 11 ) NOT NULL ,
PRIMARY KEY (  `SalaryReportAccountID` ) 
) ENGINE = MYISAM

CREATE TABLE  `salaryreportaccountentries` (
`SalaryReportAccountEntryID` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`SalaryReportAccountID` INT( 11 ) NOT NULL ,
`Code` VARCHAR( 7 ) NOT NULL ,
`Amount` FLOAT NOT NULL
) ENGINE = MYISAM

*/

include('reportcodes.php');

$year = isset($_REQUEST["year"]) ? (int)$_REQUEST["year"] : date("Y");
$employees_q = "select *, A.KommuneID as NoKommune from salaryconf as S, accountplan as A, kommune as K where S.AccountPlanID=A.AccountPlanID and (A.KommuneID=K.KommuneID or (A.KommuneID = 0 and K.KommuneID = 1) ) and S.SalaryConfID!=1 order by AccountName asc";
$employees_r = $_lib['db']->db_query($employees_q);

printf('<form action="%st=salary.employeereport" method="post">
          <input name="year" value="%s" />
          <input type="submit" value="Change year" />
        </form><br /><br />', $_lib['sess']->dispatch, $year);

print('<table class="lodo_data" border=1">
<tr>
  <th></th>
  <th></th>
  <th>Innberettet</th>
');

foreach($codes as $code) {
    printf('<th>%s</th>', $code);
}

print('</tr>');

function print_values($codes, $line, $print_extra = true) {
    global $_lib, $year;

    printf("<td>%s</td>", $line['Date']);
    foreach($codes as $code) {
        printf("<td>%2.2f</td>", $line['amounts'][$code]);
    }

    if($print_extra) {
        if($line['Locked'] == 0) {
            printf("<td><a href='%st=salary.editreport&SalaryReportID=%d&year=%d'>edit</a></td>
                    <td><a href='%st=salary.employeereport&lock_report&SalaryReportID=%d&year=%d'  onclick='return confirm(\"Are you sure you want to lock?\")'>lock</a></td>
                    <td><a href='%st=salary.employeereport&delete_report&SalaryReportID=%d&year=%d' onclick='return confirm(\"Are you sure you want to delete?\")'>delete</a></td>",
                $_lib['sess']->dispatch, $line['ID'], $year,
                $_lib['sess']->dispatch, $line['ID'], $year,
                $_lib['sess']->dispatch, $line['ID'], $year
            );
        }
        else {
            $query = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $line['LockedBy']);
            $r = $_lib['db']->db_query($query);
            $result = $_lib['db']->db_fetch_assoc($r);
            printf("<td>Locked by %s %s</td>", $result['FirstName'], $result['LastName']);

            if($_lib['sess']->get_person('AccessLevel') >= 2) {
                printf("<td><a href='%st=salary.employeereport&unlock_report&SalaryReportID=%d&year=%d'>unlock</a></td>", 
                     $_lib['sess']->dispatch, $line['ID'], $year
                );
            }
        }
    }
}

function print_sums($employee_id, $codes, $report) {
    global $_lib, $year;
 
    $sums = array();
    foreach($codes as $code)
        $sums[$code] = 0;

    foreach($report as $line) {
        foreach($line['amounts'] as $k => $v) {
            $sums[$k] += $v;
        }
    }

    print("<td><b>sum</b></td>");
    foreach($codes as $code) {
        printf("<td><b>%2.2f</b></td>", $sums[$code]);
    }

    if($employee_id)
        printf("<td><a href='%st=salary.addreport&AccountPlanID=%d&year=%d'>+</td>", $_lib['sess']->dispatch, $employee_id, $year);
}


$all_reports = array();

while( $employee = $_lib['db']->db_fetch_assoc( $employees_r ) ) {
    $salaryreport = new salaryreport(array('year'=>$year, 'employeeID'=>$employee['AccountPlanID']));
    
    $reports_r = $_lib['db']->db_query( sprintf("select * from salaryreport WHERE Date >= '%d-01-01' AND Date < '%d-01-01' AND AccountPlanID = %d ORDER BY Date", $year, $year + 1, $employee['AccountPlanID']) );

    $report_lines = array(); 
    while( $report = $_lib['db']->db_fetch_assoc($reports_r) ) {
        $report_line = array( 'Date' => $report['Date'], 'Locked' => $report['Locked'], 'LockedBy' => $report['LockedBy'], 'ID' => $report['SalaryReportID'] );
        $report_amounts = array();

        $amounts_r = $_lib['db']->db_query( sprintf("select * from salaryreportentries WHERE SalaryReportID = %d", $report['SalaryReportID']) );
        while( $amount = $_lib['db']->db_fetch_assoc($amounts_r) ) {
            $report_amounts[$amount['Code']] = $amount['Amount'];
        }

        $report_line['amounts'] = $report_amounts;
        $report_lines[] = $report_line;
        $all_reports[] = $report_line;
    }

    /*
    $query = sprintf("SELECT DATEDIFF( DATE('%s'), DATE('%s') ) AS d", $salaryreport->_reportHash['account']['WorkStop'], $salaryreport->_reportHash['account']['WorkStart']);
    $workedDays_r = $_lib['db']->db_query($query);
    $workedDays = $_lib['db']->db_fetch_assoc($workedDays_r);
    $workedDays['d'] += 1;

    if($workedDays['d'] < 1)
    continue;*/

    $query = sprintf("SELECT SalaryID FROM salary WHERE PayDate >= '%d-01-01' AND PayDate < '%d-01-01'",
                     $year, $year + 1);
    if($_lib['db']->get_row(array('query' => $query)) == false) {
        continue;
    }
                     

    $fields = array(
        array($employee['AccountPlanID'] . ' <b>'.$salaryreport->_reportHash['account']['AccountName'].'</b>', $salaryreport->_reportHash['account']['SocietyNumber']),
        array($salaryreport->_reportHash['account']['Address'], 'alle dager: ' . ($salaryreport->_reportHash['account']['WorkedWholeYear'] ? 'ja' : 'nei') ),
        array($salaryreport->_reportHash['account']['ZipCode'], $salaryreport->_reportHash['account']['WorkStart']),
        array($salaryreport->_reportHash['account']['City'], $salaryreport->_reportHash['account']['WorkStop']),
        array('', $workedDays['d'] . ' dager' )
    );

    $no_lines = count($report_lines);
    $no_fields = count($fields);
    $m = max($no_lines + 1, $no_fields);
    $sum_printed = false;

    for($i = 0; $i < $m; $i++) {
        print("<tr>");
        if($i < $no_fields) {
            foreach($fields[$i] as $l) {
                printf("<td>%s</td>", $l);
            }
        }
        else {
            printf("<td></td><td></td>");
        }

        if($i < $no_lines) {
            print_values($codes, $report_lines[$i]);
        }
        else if(!$sum_printed) {
            $sum_printed = true;
            print_sums($employee['AccountPlanID'], $codes, $report_lines);
        }
    }
    print('</tr><tr></tr><tr></tr>');
}

echo '</table><br />';

echo '<table class="lodo_data" border=1>';

print('<tr><th></th>');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print('</tr>');

foreach($all_reports as $report) {
     //print_r($report);
     print('<tr>');
     print_values($codes, $report, false);
     print('</tr>');
}
print_sums(0, $codes, $all_reports);
print('<tr><td>diff</td>');
foreach($codes as $c) {
    print('<td>0</td>');
}
print('</tr></table>');	



print('
<br /><br />
<table border=1 class="lodo_data">
  <tr>
    <th>Konto</th>
');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print('</tr>');

$accountreports = array();
$accountreport_query = sprintf("SELECT * FROM salaryreportaccount WHERE Year = %d", $year);
$accountreport_r = $_lib['db']->db_query($accountreport_query);
while($row = $_lib['db']->db_fetch_assoc($accountreport_r)) {
    $account_query = sprintf("SELECT AccountName FROM accountplan WHERE AccountPlanID = %d", $row['AccountPlanID']);
    $account_r = $_lib['db']->db_query($account_query);
    $account = $_lib['db']->db_fetch_assoc($account_r);

    $amounts_query = sprintf("SELECT * FROM salaryreportaccountentries WHERE SalaryReportAccountID = %d", $row['SalaryReportAccountID']);
    $amounts_r = $_lib['db']->db_query($amounts_query);
    $amounts = array();
    while($amountrow = $_lib['db']->db_fetch_assoc($amounts_r)) {
        $amounts[$amountrow['Code']] = $amountrow['Amount'];
    }

    $accountreports[] = array('SalaryReportAccountID' => $row['SalaryReportAccountID'], 'Account' => $account['AccountName'], 'Locked' => $row['Locked'], 'LockedBy' => $row['LockedBy'], 'amounts' => $amounts);

}

foreach($accountreports as $accountreport) {
    printf("
      <tr> 
        <td>%s</td>", $accountreport['Account']);

    foreach($codes as $c) {
        printf("<td>%2.2f</td>", $accountreport['amounts'][$c]);
    }

    if($accountreport['Locked'] == 0) {
        printf(
            '<td><a href="%st=salary.editreportaccount&SalaryReportAccountID=%d&year=%d">edit</a></td>
            <td><a href="%slock_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d" onclick=\'return confirm("Are you sure you want to lock?")\'>lock</a></td>
            <td><a href="%sdelete_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d"  onclick=\'return confirm("Are you sure you want to delete?")\'>delete</a></td>',
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
        );

    }
    else {
        $query = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $accountreport['LockedBy']);
        $r = $_lib['db']->db_query($query);
        $result = $_lib['db']->db_fetch_assoc($r);
        printf("<td>Locked by %s %s</td>", $result['FirstName'], $result['LastName']);

        if($_lib['sess']->get_person('AccessLevel') >= 2) {
            printf("<td><a href='%st=salary.employeereport&unlock_account_report&SalaryReportAccountID=%d&year=%d'>unlock</a></td>",
                   $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
                );
        }

    }

    print("</tr>");
}

print('
<tr>
  <td><b>sum innberettet</b></td>
');

$sum_account = array();

foreach($codes as $c) {
    $sum = 0;
    foreach($accountreports as $accountreport) {
        $sum += $accountreport['amounts'][$c];
    }

    $sum_account[$c] = $sum;

    printf('<td>%2.2f</td>', $sum);
}
printf('
<td><a href="%st=salary.addreportaccount&year=%d">+</a></td></tr>
', $_lib['sess']->dispatch, $year);


print('<tr><td>diff</td>');
foreach($codes as $c) {
    $sum = 0;
    
    foreach($all_reports as $report) {
        $sum += $report['amounts'][$c];
    }

    $d = $sum - $sum_account[$c];

    if($d < -0.01 || $d > 0.01)
        printf("<td><span style='color:red'>%2.2f<span></td>", $d);
    else
        printf("<td>%2.2f</td>", $d);
}


?>
