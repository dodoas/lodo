<?php
$year = isset($_REQUEST["year"]) ? (int)$_REQUEST["year"] : (isset($_REQUEST["report_year"]) ? (int)$_REQUEST["report_year"] : date("Y"));

function generateSalaryLink($SalaryID, $JournalID){
  global $_lib;

  return 'L <a href="' . $_lib['sess']->dispatch . 't=salary.edit&SalaryID=' . $SalaryID . '">' . $JournalID . '</a>';
}

function print_values($codes, $line, $print_extra = true) {
    global $_lib, $year;

    printf("<td>%s</td>", $line['Date']);
    foreach($codes as $code) {
        printf("<td style='text-align: right'>%s</td>", $_lib['format']->Amount($line['amounts'][$code]));
    }

    printf("<td>%s</td>", $line['Comment']);

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
            printf("<td>L&aring;st av %s %s</td>", $result['FirstName'], $result['LastName']);

            if($_lib['sess']->get_person('AccessLevel') >= 2) {
                printf("<td><a href='%st=salary.employeereport&unlock_report&SalaryReportID=%d&year=%d'>l&aring;se opp</a></td>",
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
        printf("<td style='text-align: right'><b>%s</b></td>", $_lib['format']->Amount($sums[$code]));
    }

    if($employee_id && $_lib['sess']->get_person('AccessLevel') > 1)
        printf("<td></td><td><a href='%st=salary.addreport&AccountPlanID=%d&year=%d'>+</td>", $_lib['sess']->dispatch, $employee_id, $year);
}

?>

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
include('reportcodes.php');

printf("<h1>%s - L&oslash;nnsrapport for %d</h1>", $_lib['sess']->get_companydef('CompanyName'), $year);
printf("<p>Utvalgskriterie er p&aring; grunnlag av altinndato p&aring; hver l&oslash;nnsslipp</p>");

/*
 * Detaljetabellen
 */
print('<table class="lodo_data" border=1">
<tr>
  <th></th>
  <th></th>
  <th></th>
  <th>Innberettet</th>
');

foreach($codes as $code) {
    printf('<th>%s</th>', $code);
}
    print('<th>Kommentar</th>');

print('</tr>');

$all_reports = array();
$employees_q = "
select 
  *, A.KommuneID as NoKommune from salaryconf as S, accountplan as A, kommune as K 
where 
  S.AccountPlanID=A.AccountPlanID 
  and ( A.KommuneID=K.KommuneID or (A.KommuneID = 0 and K.KommuneID = 1) ) 
  and S.SalaryConfID !=1 
order by AccountName asc
";

$employees_r = $_lib['db']->db_query($employees_q);

while( $employee = $_lib['db']->db_fetch_assoc( $employees_r ) ) {
    $salaryreport = new salaryreport(array('year'=>$year, 'employeeID'=>$employee['AccountPlanID']));

    $query = sprintf("SELECT VoucherID FROM voucher WHERE VoucherType = 'L' AND AccountPlanID = '%d' AND JournalID IN (SELECT JournalID FROM salary WHERE ActualPayDate LIKE '%d-%%')",
                     $employee['AccountPlanID'], $year);
    if($_lib['db']->get_row(array('query' => $query)) == false) {
        continue;
    }
    
    $reports_r = $_lib['db']->db_query( 
        sprintf(
            "select * 
             from salaryreport 
             where Date >= '%d-01-01' and Date < '%d-01-01' and AccountPlanID = %d 
             order by Date", 
            $year, $year + 1, $employee['AccountPlanID']) 
        );

    $report_lines = array(); 
    while( $report = $_lib['db']->db_fetch_assoc($reports_r) ) {
        $report_line = array( 
            'Date' => $report['ReportDate'], 
            'Locked' => $report['Locked'], 
            'LockedBy' => $report['LockedBy'], 
            'ID' => $report['SalaryReportID'],
            'Comment' => $report['Comment'],
            'Employee' => $employee
            );
        $report_amounts = array();

        $amounts_r = $_lib['db']->db_query(
            sprintf("select * from salaryreportentries WHERE SalaryReportID = %d", $report['SalaryReportID']) 
            );
        while( $amount = $_lib['db']->db_fetch_assoc($amounts_r) ) {
            $report_amounts[$amount['Code']] = $amount['Amount'];
        }

        $report_line['amounts'] = $report_amounts;
        $report_lines[] = $report_line;
        $all_reports[] = $report_line;
    }

    $query = sprintf("SELECT SalaryID FROM salary WHERE ActualPayDate >= '%d-01-01' AND ActualPayDate < '%d-01-01'",
                     $year, $year + 1);
    if($_lib['db']->get_row(array('query' => $query)) == false) {
        continue;
    }

    $fields = array(
        array($employee['AccountPlanID'], '<b>'.$salaryreport->_reportHash['account']['AccountName'].'</b>', $salaryreport->_reportHash['account']['SocietyNumber']),
        array('',$salaryreport->_reportHash['account']['Address'], 'alle dager: ' . ($salaryreport->_reportHash['account']['WorkedWholeYear'] ? 'ja' : 'nei') ),
        array('',$salaryreport->_reportHash['account']['ZipCode'], $salaryreport->_reportHash['account']['WorkStart']),
        array('',$salaryreport->_reportHash['account']['City'], $salaryreport->_reportHash['account']['WorkStop']),
        array('',$salaryreport->_reportHash['account']['KommuneNumber'] . " ". $salaryreport->_reportHash['account']['KommuneName'], 
              (round($salaryreport->_reportHash['account']['WorkedDays']) != 0 && ((int)$salaryreport->_reportHash['account']['WorkPercent']) != 100 ?
               ((int)$salaryreport->_reportHash['account']['WorkPercent']) . '%: ' . round($salaryreport->_reportHash['account']['WorkedDays']) . ' dager' 
               : '100%'))
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
            printf("<td></td><td></td><td></td>");
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

/*
 * Tabell 2 - oppsummering
 */

echo '<table class="lodo_data" border=1>';

print('<tr><th></th><th></th><th></th>');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print("<th><b>Kommentar</b></th>");
print('</tr>');

foreach($all_reports as $report) {
    print('<tr>');
    printf('<td>%s</td>', $report["Employee"]["AccountPlanID"]);
    printf('<td>%s</td>', $report["Employee"]["AccountName"]);
    print_values($codes, $report, false);
    print('</tr>');
}

print('<tr>');
print('<td></td>');
print('<td></td>');
print_sums(0, $codes, $all_reports);
print('<td></td>');
print('</tr>');
print('<tr><td></td><td></td><td>diff</td>');
foreach($codes as $c) {
    print('<td>0</td>');
}
print('<td></td>');
print('</tr></table>');

/*
 * Kontosum
 */

print('
<br /><br />
<table border=1 class="lodo_data">
  <tr>
    <th>Konto</th>
');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print("<th><b>Forskjellige &aring;r</b></th>");
print("<th><b>Kommentar</b></th>");
print('</tr>');

$accountreports = array();
$accountreport_query = sprintf("SELECT sra.*, YEAR(s.JournalDate) AS JournalYear, s.SalaryID FROM salaryreportaccount sra LEFT JOIN salary s ON s.JournalID = sra.SalaryJournalID WHERE Year = %d", $year);
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

    $accountreports[] = array(
        'SalaryReportAccountID' => $row['SalaryReportAccountID'],
        'SalaryID' => $row['SalaryID'],
        'SalaryJournalID' => $row['SalaryJournalID'],
        'Account' => $account['AccountName'],
        'AccountPlanID' => $row['AccountPlanID'],
        'Locked' => $row['Locked'],
        'LockedBy' => $row['LockedBy'],
        'Comment' => $row['Comment'],
        'DifferentYear' => $row['DifferentYear'],
        'JournalYear' => $row['JournalYear'],
        'Feriepengeprosent' => $row['Feriepengeprosent'],
        'AGAprosent' => $row['AGAprosent'],
        'amounts' => $amounts
        );

}

foreach($accountreports as $accountreport) {
  if(intval($accountreport['DifferentYear']) === 1
    && (($accountreport['JournalYear'] == ($year-1))
    || ($accountreport['JournalYear'] == ($year)))
  ) continue;
    printf("
      <tr> 
        <td>%s %s</td>", $accountreport['AccountPlanID'], $accountreport['Account']);

    foreach($codes as $c) {
        printf("<td style='text-align: right'>%s</td>", $_lib['format']->Amount($accountreport['amounts'][$c]));
    }
        $different_year_checked = (intval($accountreport['DifferentYear']) === 1) ? 'checked' : '';
        print("<td><input type=\"checkbox\" name=\"DifferentYear\" $different_year_checked disabled/></td>");
        printf("<td>%s</td>", $accountreport['Comment']);

    if($accountreport['Locked'] == 0) {
        printf(
            '<td><a href="%st=salary.addeditreportaccount&SalaryReportAccountID=%d&year=%d&action=edit">rediger</a></td>
            <td><a href="%slock_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d" onclick=\'return confirm("Are you sure you want to lock?")\'>l&aring;s</a></td>
            <td><a href="%sdelete_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d"  onclick=\'return confirm("Are you sure you want to delete?")\'>slett</a></td>',
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
        );

    }
    else {
        $query = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $accountreport['LockedBy']);
        $r = $_lib['db']->db_query($query);
        $result = $_lib['db']->db_fetch_assoc($r);
        printf("<td>L&aring;st av %s %s</td>", $result['FirstName'], $result['LastName']);

        if($_lib['sess']->get_person('AccessLevel') >= 2) {
            printf("<td><a href='%st=salary.employeereport&unlock_account_report&SalaryReportAccountID=%d&year=%d'>l&aring;se opp</a></td>",
                   $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
                );
        }

    }

    print("</tr>");
}

$sum_account_prev = array();
$sum_account_next = array();
foreach($codes as $c) {
    $sum_prev = 0;
    $sum_next = 0;
    foreach($accountreports as $accountreport) {
        if(intval($accountreport['DifferentYear']) === 0) continue;
        if($accountreport['JournalYear'] == ($year-1)) {
          $sum_prev += $accountreport['amounts'][$c];
        } elseif($accountreport['JournalYear'] == $year) {
          $sum_next += $accountreport['amounts'][$c];
        }
    }
    $sum_account_prev[$c] = $sum_prev;
    $sum_account_next[$c] = $sum_next;

}
print('
<tr>
  <td><b>sum fratrukkede l&oslash;nnslipper ' . ($year-1) . ' - ' . $year . '</b></td>
');
foreach($codes as $c) {
    printf('<td style="text-align: right">%s</td>', $_lib['format']->Amount($sum_account_prev[$c]));
}
print("<td></td><td></td></tr>");

print('
<tr>
  <td><b>sum fratrukkede l&oslash;nnslipper ' . $year . ' - ' . ($year+1) . '</b></td>
');
foreach($codes as $c) {
    printf('<td style="text-align: right">%s</td>', $_lib['format']->Amount($sum_account_next[$c]));
}
print("<td></td><td></td></tr>");

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

    printf('<td style="text-align: right">%s</td>', $_lib['format']->Amount($sum));
}

if ($_lib['sess']->get_person('AccessLevel') > 1 ) {
    printf('
    <td></td><td></td><td><a href="%st=salary.addeditreportaccount&year=%s&action=add">+</a></td></tr>
    ', $_lib['sess']->dispatch, $year);
}


print('<tr><td>diff</td>');
foreach($codes as $c) {
    $sum = 0;

    foreach($all_reports as $report) {
        $sum += $report['amounts'][$c];
    }

    $d = $sum - $sum_account[$c];

    if($d < -0.01 || $d > 0.01)
        printf("<td style='text-align: right'><span style='color:red'>%s<span></td>", $_lib['format']->Amount($d));
    else
        printf("<td style='text-align: right'>%s</td>", $_lib['format']->Amount($d));
}
print("</tr></table>");

print('
<br /><br />' . ($year-1) . ' - ' . $year
. '<table border=1 class="lodo_data">
  <tr>
    <th>Konto</th>
    <th>L&oslash;nn</th>
');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print("<th>Forskjellige &aring;r</th>");
print("<th>AGA %</th>");
print("<th>Feriepenger %</th>");
print("<th>Kommentar</th>");
print('</tr>');

foreach($accountreports as $accountreport) {
  if(intval($accountreport['DifferentYear']) === 0) continue;
  if($accountreport['JournalYear'] != ($year-1)) continue;
    printf("
      <tr>
        <td>%s %s</td>", $accountreport['AccountPlanID'], $accountreport['Account']);
    if ($accountreport['SalaryJournalID']) {
      print("<td>" . generateSalaryLink($accountreport['SalaryID'], $accountreport['SalaryJournalID']) . "</td>");
    } else {
      printf("<td></td>");
    }

    foreach($codes as $c) {
        printf("<td style='text-align: right'>%s</td>", $_lib['format']->Amount($accountreport['amounts'][$c]));
    }
        print("<td><input type=\"checkbox\" name=\"DifferentYear\" checked disabled/></td>");
        printf("<td>%s</td>", $accountreport['AGAprosent']);
        printf("<td>%s</td>", $accountreport['Feriepengeprosent']);
        printf("<td>%s</td>", $accountreport['Comment']);

    if($accountreport['Locked'] == 0) {
        printf(
            '<td><a href="%st=salary.addeditreportaccount&SalaryReportAccountID=%d&year=%d&action=edit">rediger</a></td>
            <td><a href="%slock_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d" onclick=\'return confirm("Are you sure you want to lock?")\'>l&aring;s</a></td>
            <td><a href="%sdelete_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d"  onclick=\'return confirm("Are you sure you want to delete?")\'>slett</a></td>',
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
        );

    }
    else {
        $query = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $accountreport['LockedBy']);
        $r = $_lib['db']->db_query($query);
        $result = $_lib['db']->db_fetch_assoc($r);
        printf("<td>l&aring;st av %s %s</td>", $result['FirstName'], $result['LastName']);

        if($_lib['sess']->get_person('AccessLevel') >= 2) {
            printf("<td><a href='%st=salary.employeereport&unlock_account_report&SalaryReportAccountID=%d&year=%d'>l&aring;se opp</a></td>",
                   $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
                );
        }

    }

    print("</tr>");
}

print('
<tr>
  <td><b>sum innberettet</b></td>
  <td></td>
');
foreach($codes as $c) {
        printf('<td style="text-align: right">%s</td>', $_lib['format']->Amount($sum_account_prev[$c]));
}

print("</tr></table>");
print('
<br /><br />' . $year . ' - ' . ($year+1)
. '<table border=1 class="lodo_data">
  <tr>
    <th>Konto</th>
    <th>L&oslash;nn</th>
');
foreach($codes as $c) {
    printf("<th><b>%s</b></th>", $c);
}
print("<th>Forskjellige &aring;r</th>");
print("<th>AGA %</th>");
print("<th>Feriepenger %</th>");
print("<th>Kommentar</th>");
print('</tr>');

foreach($accountreports as $accountreport) {
  if(intval($accountreport['DifferentYear']) === 0) continue;
  if($accountreport['JournalYear'] != $year) continue;
    printf("
      <tr>
        <td>%s %s</td>", $accountreport['AccountPlanID'], $accountreport['Account']);
    if ($accountreport['SalaryJournalID']) {
      print("<td>" . generateSalaryLink($accountreport['SalaryID'], $accountreport['SalaryJournalID']) . "</td>");
    } else {
      printf("<td></td>");
    }

    foreach($codes as $c) {
        printf("<td style='text-align: right'>%s</td>", $_lib['format']->Amount($accountreport['amounts'][$c]));
    }
        print("<td><input type=\"checkbox\" name=\"DifferentYear\" checked disabled/></td>");
        printf("<td>%s</td>", $accountreport['AGAprosent']);
        printf("<td>%s</td>", $accountreport['Feriepengeprosent']);
        printf("<td>%s</td>", $accountreport['Comment']);

    if($accountreport['Locked'] == 0) {
        printf(
            '<td><a href="%st=salary.addeditreportaccount&SalaryReportAccountID=%d&year=%d&action=edit">rediger</a></td>
            <td><a href="%slock_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d" onclick=\'return confirm("Are you sure you want to lock?")\'>l&aring;s</a></td>
            <td><a href="%sdelete_account_report&t=salary.employeereport&SalaryReportAccountID=%d&year=%d"  onclick=\'return confirm("Are you sure you want to delete?")\'>slett</a></td>',
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year,
            $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
        );

    }
    else {
        $query = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $accountreport['LockedBy']);
        $r = $_lib['db']->db_query($query);
        $result = $_lib['db']->db_fetch_assoc($r);
        printf("<td>L&aring;st av %s %s</td>", $result['FirstName'], $result['LastName']);

        if($_lib['sess']->get_person('AccessLevel') >= 2) {
            printf("<td><a href='%st=salary.employeereport&unlock_account_report&SalaryReportAccountID=%d&year=%d'>l&aring;se opp</a></td>",
                   $_lib['sess']->dispatch, $accountreport['SalaryReportAccountID'], $year
                );
        }

    }

    print("</tr>");
}

print('
<tr>
  <td><b>sum innberettet</b></td>
  <td></td>
');
foreach($codes as $c) {
        printf('<td style="text-align: right">%s</td>', $_lib['format']->Amount($sum_account_next[$c]));
}

print("</tr></table>");

?>
