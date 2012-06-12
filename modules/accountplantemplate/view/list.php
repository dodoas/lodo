<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - accountplan list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.57 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<?
includeinc('top');
includeinc('left');
?>

<h1>Hovedmaler for kontoer</h1>

<table class="group">
<?

$q = "SELECT AccountPlanID, AccountPlanType FROM accountplantemplate";
$r = $_lib['db']->db_query($q);

/*
 * Content of Accountplantemplate was missing when installing new 
 * users in LODO. This code is a temp-fix for the problem and should
 * be removed when all users have got their content.
 */
$n = $_lib['db']->db_numrows($r);
if(!$n) {
    echo "Missing import, filling it";

    $q_fill = "INSERT INTO `accountplantemplate` (`AccountPlanID`, `AccountName`, `FirstName`, `LastName`, `VatID`, `Report`, `Budget`, `TS`, `InsertedDateTime`, `InsertedByPersonID`, `ValidFrom`, `ValidTo`, `debittext`, `credittext`, `Currency`, `Description`, `EnableReskontro`, `EnableQuantity`, `EnableDepartment`, `EnableProject`, `EnableReport1`, `EnableReport2`, `EnableReport3`, `EnableReport4`, `EnableReport5`, `EnableReport6`, `EnableReport7`, `EnableReport8`, `EnableReport9`, `EnableReport10`, `EnableReportShort`, `EnableBudgetResult`, `EnableBudgetLikviditet`, `Report1Line`, `Report2Line`, `Report3Line`, `Report4Line`, `Report5Line`, `Report6Line`, `Report7Line`, `Report8Line`, `Report9Line`, `Report10Line`, `ReportShort`, `EnableVATOverride`, `EnablePostPost`, `ReskontroFromAccount`, `ReskontroToAccount`, `EnableZeroYearly`, `Reskontro`, `EnableVAT`, `Address`, `ZipCode`, `Email`, `DomesticBankAccount`, `AbroadBankAccount`, `EnableSalary`, `SocialSecurityNumber`, `Municipality`, `EmployedFromDate`, `EmployedToDate`, `SalaryDue`, `EnableCredit`, `CreditDays`, `EnableAutogiro`, `EnableNettbank`, `DebitColor`, `CreditColor`, `Active`, `EnableCurrency`, `CustomerNumber`, `EnableMotkontoBalanse`, `MotkontoBalanse1`, `MotkontoBalanse2`, `MotkontoBalanse3`, `MotkontoResultat1`, `MotkontoResultat2`, `MotkontoResultat3`, `EnableMotkontoResultat`, `LastUsedTime`, `City`, `EnablePersonalUsage`, `EnableMoneyFlow`, `EnableSaldo`, `NorwegianStandardText`, `OrgNumber`, `VatNumber`, `SocietyNumber`, `KommuneID`, `WorkStart`, `WorkStop`, `EnableNorwegianStandard`, `PersonID`, `ProjectID`, `DepartmentID`, `TabellTrekk`, `ProsentTrekk`, `Phone`, `IPoBox`, `IPoBoxCity`, `Mobile`, `EnableInvoicePoBox`, `EnableInvoiceAddress`, `IPoBoxZipCode`, `BirthDate`, `WorkPercent`, `EnableSalaryPayment`, `Report1LinePage`, `Report2LinePage`, `Report3LinePage`, `Report4LinePage`, `Report5LinePage`, `Report6LinePage`, `Report7LinePage`, `Report8LinePage`, `Report9LinePage`, `Report10LinePage`, `IPoBoxZipCodeCity`, `Report66Line`, `Report0002Line`, `Report1224Line`, `Report2224Line`, `Report1028Line`, `Report1125Line`, `Report1217Line`, `Report1122Line`, `Report1052Line`, `Report1223Line`, `Report1219Line`, `Report1061Line`, `Report1239Line`, `Report1215Line`, `Report1221Line`, `Report1037Line`, `Report1025Line`, `Report1231Line`, `Report1022Line`, `Report1086Line`, `EnableReport1224`, `EnableReport2224`, `InvoiceCommentCustomerPosition`, `ReskontroAccountPlanType`, `AccountPlanType`, `UpdatedByPersonID`, `AccountLineFreeTextMatch`, `CountryCode`, `isAccountCustomer`, `isAccountSupplier`, `isAccountResult`, `isAccountBalance`, `isAccountEmployee`, `IBAN`, `Web`) VALUES
(1, '', '', '', 0, NULL, NULL, '2012-04-14 11:59:30', NULL, NULL, NULL, NULL, 'Betal', 'Lønn', '', '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL, 0, 1, NULL, NULL, NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 5, 0, 0, 'debitblue', 'creditred', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, '2008-08-10 21:22:34', '', NULL, 1, 0, NULL, '', NULL, '', 0, '0000-00-00', '0000-00-00', NULL, NULL, 0, 0, '', '', '', '', '', '', 0, 0, '', '0000-00-00 00:00:00', '0.00', NULL, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bottom', NULL, 'employee', 1, NULL, 'NO', NULL, NULL, NULL, NULL, 1, NULL, ''),
(2, '', NULL, NULL, 0, NULL, NULL, '2012-04-14 12:01:40', NULL, NULL, NULL, NULL, 'Utgift', 'Inntekt', '', '', 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 300, 0, 0, 0, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 'debitred', 'creditblue', 0, 0, NULL, 0, 0, 0, 0, 0, 0, 0, 0, '2012-03-23 13:18:54', NULL, 0, 0, 0, '', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL, NULL, NULL, '3000', '3000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 'bottom', '', 'result', 1, '', '', NULL, NULL, 1, NULL, NULL, NULL, ''),
(3, '', NULL, NULL, 0, NULL, NULL, '2012-04-27 10:00:58', NULL, NULL, NULL, NULL, 'Inn', 'Ut', '', '', 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', 0, 0, 0, 0, 0, NULL, NULL, 0, '', '0', '', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 'debitblue', 'creditred', 1, 0, '0', 0, 0, 0, 0, 0, 0, 0, 0, '2005-03-22 21:18:12', NULL, 0, 0, 0, '', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL, NULL, NULL, '1000', '1000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 'bottom', '', 'balance', 1, '', '', NULL, NULL, NULL, 1, NULL, NULL, ''),
(4, '', NULL, NULL, 0, NULL, NULL, '2012-04-14 12:00:03', NULL, NULL, NULL, NULL, 'Salg', 'Betal', '', '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL, 0, 0, NULL, NULL, NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 14, 0, 0, 'debitblue', 'creditred', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, '2012-03-23 13:18:09', '', 0, 0, 0, '', '', '', '', 0, '0000-00-00', '0000-00-00', 0, NULL, 0, 0, '', '', '', '', '', '', 0, 0, '', '0000-00-00 00:00:00', '0.00', 0, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bottom', '', 'customer', 1, '', 'NO', 1, NULL, NULL, NULL, NULL, '', ''),
(5, '', NULL, NULL, 0, NULL, NULL, '2012-04-27 10:00:14', NULL, NULL, NULL, NULL, 'Betal', 'Kjøp', '', '', 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, NULL, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL, 0, 0, NULL, NULL, NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, 1, 10, 0, 0, 'debitblue', 'creditred', 1, 0, '', 0, 0, 0, 0, 0, 0, 0, 1, NULL, '', 0, 0, 0, '', '', '', '', 0, '0000-00-00', '0000-00-00', 0, NULL, 0, 0, '', '', '', '', '', '', 0, 0, '', '0000-00-00 00:00:00', '0.00', 0, '', '', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bottom', '', 'supplier', 1, '', 'NO', NULL, 1, NULL, NULL, NULL, '', '');";
    $_lib['db']->db_query($q_fill);
    
    $r = $_lib['db']->db_query($q);
}

while( ($row = $_lib['db']->db_fetch_assoc($r)) ) {

$AccountPlanID = $row['AccountPlanID'];
$AccountPlanType = $row['AccountPlanType'];

switch($AccountPlanType) {
    case 'customer':
      $n = 'Kunde';
      $t = 'reskontro'; break;
    case 'supplier':
      $n = 'Leverand&oslash;r';
      $t = 'reskontro'; break;
    case 'employee':
      $n = 'Ansatt';
      $t = 'employee'; break;
    case 'result':
      $n = 'Resultat';
      $t = 'hovedbok'; break;
    case 'balance':
      $n = 'Balanse';
      $t = 'hovedbok'; break;
}

printf('
<tr>
  <td>
    <a href="%st=accountplantemplate.%s&accountplantemplate.AccountPlanID=%d&accountplan_type=%s">%s</a>
  </td>
</tr>
', 
$_lib['sess']->dispatch,
$t, $AccountPlanID, $AccountPlanType, $n
);

}
?>
</table>

</body>
</html>
