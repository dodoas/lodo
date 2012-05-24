<?php

if(isset($_POST['add_report'])) {
    $query = sprintf("INSERT INTO salaryreport (`Date`, `AccountPlanID`, `ReportDate`) VALUES ('%d-01-01', '%d', '%s');", 
                     $year, $_POST['AccountPlanID'], mysql_escape_string($_POST['add_date']));

    $_lib['db']->db_query($query);
    $id = $_lib['db']->db_insert_id();
 
    foreach($_POST['add_amounts'] as $code => $amount) {
        $query = sprintf("INSERT INTO salaryreportentries (`SalaryReportID`, `Code`, `Amount`) VALUES ('%d', '%s', '%s');", 
                         $id, mysql_escape_string($code), mysql_escape_string(str_replace(array(" ", ","), array("", "."), $amount)));
        $_lib['db']->db_query($query);
    }
}
else if(isset($_POST['edit_report'])) {
    $query = sprintf("UPDATE salaryreport SET ReportDate = '%s' WHERE SalaryReportID = %d", mysql_escape_string($_POST['edit_date']), $_POST['SalaryReportID']);
    $_lib['db']->db_query($query);

    foreach($_POST['edit_amounts'] as $code => $amount) {
        $query = sprintf("UPDATE salaryreportentries SET Amount = '%s' WHERE Code = '%s' AND SalaryReportID = %d", 
                         mysql_escape_string(str_replace(array(" ", ","), array("", "."), $amount)), mysql_escape_string($code), $_POST['SalaryReportID']);
        echo $query;
        $_lib['db']->db_query($query);
    }
}
else if(isset($_GET['lock_report'])) {
    $query = sprintf("UPDATE salaryreport SET Locked = 1, LockedBy = %d WHERE SalaryReportID = %d", $_SESSION["login_id"], $_GET['SalaryReportID']);
    $_lib['db']->db_query($query);
}
else if(isset($_GET['unlock_report'])) {
    $query = sprintf("UPDATE salaryreport SET Locked = 0, LockedBy = %d WHERE SalaryReportID = %d", $_SESSION["login_id"], $_GET['SalaryReportID']);
    $_lib['db']->db_query($query);
}
else if(isset($_GET['delete_report'])) {
    $query = sprintf("DELETE FROM salaryreport WHERE SalaryReportID = %d", $_GET['SalaryReportID']);
    $_lib['db']->db_query($query);
}
else if(isset($_POST['add_report_account'])) {
    $query = sprintf("INSERT INTO salaryreportaccount (`Year`, `AccountPlanID`) VALUES ('%s', '%d');", 
                     mysql_escape_string($_GET['year']), $_POST['reportaccount_accountplanid']);
    $_lib['db']->db_query($query);
    $id = $_lib['db']->db_insert_id();

    foreach($_POST['amounts'] as $code => $amount) {
        $query = sprintf("INSERT INTO salaryreportaccountentries (`SalaryReportAccountID`, `Code`, `Amount`) VALUES ('%d', '%s', '%s');",
                         $id, mysql_escape_string($code), mysql_escape_string(str_replace(array(" ", ","), array("", "."), $amount)));
        $_lib['db']->db_query($query);
    }
    
}
else if(isset($_POST['edit_report_account'])) {
    $query = sprintf("UPDATE salaryreportaccount SET AccountPlanID = %d WHERE SalaryReportAccountID = %d", 
                     $_POST['reportaccount_accountplanid'],
                     $_POST['SalaryReportAccountID']);
    $_lib['db']->db_query($query);

    foreach($_POST['amounts'] as $code => $amount) {
        $query = sprintf("UPDATE salaryreportaccountentries SET Amount = '%s' WHERE Code = '%s' AND SalaryReportAccountID = %d",
                         mysql_escape_string(str_replace(array(" ", ","), array("", "."), $amount)), 
                         mysql_escape_string($code), $_POST['SalaryReportAccountID']);
        $_lib['db']->db_query($query);
    }
}
else if(isset($_GET['lock_account_report'])) {
    $query = sprintf("UPDATE salaryreportaccount SET Locked = 1, LockedBy = %d WHERE SalaryReportAccountID = %d", 
                     $_SESSION['login_id'], $_GET['SalaryReportAccountID']);
    $_lib['db']->db_query($query);
}
else if(isset($_GET['unlock_account_report'])) {
    $query = sprintf("UPDATE salaryreportaccount SET Locked = 0, LockedBy = %d WHERE SalaryReportAccountID = %d", 
                     $_SESSION['login_id'], $_GET['SalaryReportAccountID']);
    $_lib['db']->db_query($query);
}
else if(isset($_GET['delete_account_report'])) {
    $query = sprintf("DELETE FROM salaryreportaccount WHERE SalaryReportAccountID = %d", $_GET['SalaryReportAccountID']);
    $_lib['db']->db_query($query);
    $query = sprintf("DELETE FROM salaryreportaccountentries WHERE SalaryReportAccountID = %d", $_GET['SalaryReportAccountID']);
    $_lib['db']->db_query($query);
}

?>
