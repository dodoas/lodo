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
$year = $_GET['year'];

$query = sprintf("SELECT * FROM salaryreportentries WHERE SalaryReportID = %d", $_GET['SalaryReportID']);
$res = $_lib['db']->db_query($query);
$amounts = array();
while($row = $_lib['db']->db_fetch_assoc($res)) {
    $amounts[ $row['Code'] ] = $row['Amount'];
}
$query = sprintf("SELECT Date FROM salaryreport WHERE SalaryReportID = %d", $_GET['SalaryReportID']);
$res = $_lib['db']->db_query($query);
$row = $_lib['db']->db_fetch_assoc($res);
$report_date = $row['ReportDate'];

$query = sprintf("SELECT A.AccountName FROM accountplan A, salaryreport S WHERE A.AccountPlanID = S.AccountPlanID AND SalaryReportID = %d", $_GET["SalaryReportID"]);
$res = $_lib['db']->db_query($query);
$row = $_lib['db']->db_fetch_assoc($res);
$name = $row['AccountName'];

?>
<h1>Innberetning for <?php print $name ?></h1>
<form action="<?= $_lib['sess']->dispatch ?>t=salary.employeereport&year=<?= $year ?>" method="post">
  <table>
    <tr>
      <th>Date</td>
<?php
foreach($codes as $code) {
    printf("<th>%s</th>", $code);
}    
?>
    </tr>
    <tr>
      <td><input type="text" name="edit_date" value="<?= $report_date ?>" />
  <?php

    foreach($codes as $code) {
        printf('
          <td><input type="text" name="edit_amounts[%s]" value="%s" /></td>
        ', $code, $amounts[$code]);
    } 

  ?>
    </tr>

  </table>
  <input type="hidden" value="<?= $_GET['SalaryReportID'] ?>" name="SalaryReportID" />
  <input type="submit" value="Lagre" name="edit_report" />
</form>
