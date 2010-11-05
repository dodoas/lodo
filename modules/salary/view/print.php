<?
/* $Id: print.php,v 1.29 2005/10/14 13:15:42 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

#########################################
#This should be placed under firmaoppsett
$lineInFrom  =  10;
$lineInTo    =  69;
$lineOutFrom =  70;
$lineOutTo   = 100;
#########################################

$SalaryID = $_REQUEST['SalaryID'];
includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$query_head     = "select S.*, A.AccountName, A.Address, A.City, A.ZipCode, A.SocietyNumber, A.TabellTrekk, A.ProsentTrekk from salary as S, accountplan as A where S.SalaryID='$SalaryID' and S.AccountPlanID=A.AccountPlanID";
#print "$query_head<br>";
$result_head    = $_lib['db']->db_query($query_head);
$head           = $_lib['db']->db_fetch_object($result_head);

$query_salary   = "select * from salaryline where SalaryID = '$SalaryID' order by LineNumber asc";
$result_salary  = $_lib['db']->db_query($query_salary);

$project_query = "select * from project";
$project_result = $_lib['db']->db_query($project_query);
$projects = array();
while( $project_line = $_lib['db']->db_fetch_assoc($project_result))
	$projects[ $project_line['ProjectID'] ] = $project_line['Heading'];
$projects[0] = "";

$department_query = "select * from companydepartment";
$department_result = $_lib['db']->db_query($department_query);
$departments = array();
while( $department_line = $_lib['db']->db_fetch_assoc($department_result))
	$departments[ $department_line['CompanyDepartmentID'] ] = $department_line['DepartmentName'];
$departments[0] = "";
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: print.php,v 1.29 2005/10/14 13:15:42 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<table class="lodo_data" id="head">
  <tr>
    <td><label>L&oslash;nning til:</td>
    <td></td>
    <td class="empty" width="10"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td><label>Ansatt nr</label></td>
    <td><? print $head->AccountPlanID ?></td>
    <td class="empty" width="10"></td>
    <td><label>Firma</label></td>
    <td><? print $_lib['sess']->get_companydef('CompanyName') ?></td>
  </tr>
  <tr>
    <td><label>Navn</label></td>
    <td><? print $head->AccountName ?></td>
    <td class="empty" width="10"></td>
    <td><label>Adresse</label></td>
    <td><? print $_lib['sess']->get_companydef('VAddress') ?></td>
  </tr>
  <tr>
    <td><label>Adresse</label></td>
    <td><? print $head->Address ?></td>
    <td class="empty" width="10"></td>
    <td><label>Postnr/Sted</label></td>
    <td><? print $_lib['sess']->get_companydef('VZipCode')." ".$_lib['sess']->get_companydef('VCity') ?></td>
  </tr>
  <tr>
    <td><label>Postnr/Sted</label></td>
    <td><? print $head->ZipCode." ".$head->City ?></td>
    <td class="empty" width="10"></td>
    <td><label>Orgnr</label></td>
    <td><? print $_lib['sess']->get_companydef('OrgNumber') ?></td>
  </tr>
  <tr>
    <td><label>Personnr</label></td>
    <td><? print $head->SocietyNumber ?></td>
    <td class="empty"></td>
    <td><label>Fra dato</label></td>
    <td><? print $head->ValidFrom ?></td>
  </tr>
  <tr>
    <td><label>Tabelltrekk</label></td>
    <td><? print $head->TabellTrekk ?></td>
    <td class="empty"></td>
    <td><label>Til dato</label></td>
    <td><? print $head->ValidTo ?></td>
  </tr>
  <tr>
    <td><label>Prosenttrekk</label></td>
    <td><? print $head->ProsentTrekk ?></td>
    <td class="empty" width="10"></td>
    <td><label>Bilagsnummer</label></td>
    <td>L <? print $head->JournalID ?></td>
  </tr>
  <tr>
    <td><label>Konto for utbetaling</label></td>
    <td><? print $head->DomesticBankAccount ?></td>
    <td class="empty" width="10"></td>
    <td><label>Bilagsdato</label></td>
    <td><? print $head->JournalDate ?></td>
  </tr>
  <tr>
    <td><label>Utbetalt dato</label></td>
    <td><? print $head->PayDate ?></td>
    <td class="empty" width="10"></td>
    <td><label>Periode</label></td>
    <td><? print $head->Period ?></td>
  </tr>
  <tr>
    <td><label>Kommentar:</label></td>
  </tr>
  <tr>
    <td colspan="4" style="font-family: arial"><?= nl2br($head->Comment) ?></td>
  </tr>
</table>
<br>
<table class="lodo_data">
  <tr>
    <th>Linje</th>
    <th>Tekst</th>
    <th>Antall</th>
    <th>Sats</th>
    <th>Sum periode</th>
    <th>Sum hittil i &aring;r</th>
    <th>Konto</th>
    <th>Avd</th>
    <th>Prosj</th>
  </tr>

  <?
  $sumTotal = 0;
  $sumTotalYear = 0;
  $sumVacation = 0;
  while($line = $_lib['db']->db_fetch_object($result_salary))
  {
      $firstPeriod = $_lib['date']->get_this_year($head->Period)."-01";
      $query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.AccountPlanID=$head->AccountPlanID and S.Period>='$firstPeriod' and S.Period<='$head->Period' and SL.LineNumber=$line->LineNumber and SL.AccountPlanID=$line->AccountPlanID";
      $totalThisYear = $_lib['storage']->get_row(array('query' => $query));

      if($line->LineNumber >= $lineInFrom and $line->LineNumber <= $lineInTo)
      {
        $sumTotal += $line->AmountThisPeriod;
        if ($line->LineNumber != $forige_linje)
	        $sumTotalYear += $totalThisYear->total;
      }
      elseif($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
      {
        $sumTotal -= $line->AmountThisPeriod;
        $sumTotalYear -= $totalThisYear->total;
      }
      if($line->EnableVacationPayment == 1)
      {
        $sumVacation += ($line->VacationPayment / 100) * $line->AmountThisPeriod;
      }
      ?>
      <tr>
        <td><? print $line->LineNumber ?></td>
        <td><? print $line->SalaryText ?></td>
        <td class="number"><nobr><? print $line->NumberInPeriod ?></nobr></td>
        <td class="number"><nobr><? print $_lib['format']->Amount(array('value'=>$line->Rate, 'return'=>'value')) ?></nobr></td>
        <td class="number"><nobr><? print $_lib['format']->Amount(array('value'=>$line->AmountThisPeriod, 'return'=>'value')) ?></nobr></td>
        <td class="number"><nobr><? 
        if ($line->LineNumber == $forige_linje)
        	print "-Som over-";
        else
        	print $_lib['format']->Amount(array('value'=>$totalThisYear->total, 'return'=>'value'));
        $forige_linje = $line->LineNumber;
?></nobr></td>
        <td><? print $line->AccountPlanID ?></td>
        <td><?= $departments[$line->DepartmentID] ?></td>
        <td><?= $projects[$line->ProjectID] ?></td>
      </tr>
      <?
  }
  ?>
    <tr>
      <td colspan="4">SUM Utbetalt</td>
      <td class="number"><? print $_lib['format']->Amount(array('value'=>$sumTotal, 'return'=>'value')) ?></td>
      <td class="number"><? print $_lib['format']->Amount(array('value'=>$sumTotalYear, 'return'=>'value')) ?></td>
      <td class="number"><? // Fjernet av Geir. print $_lib['format']->Amount(array('value'=>$sumVacation, 'return'=>'value')) ?></td>
      <td colspan="3"></td>
    </tr>
<?php
        $firstDate = substr($head->JournalDate, 0, 4) . "-01-01";
		$lastDate = $head->JournalDate;
		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.JournalDate>='$firstDate' and S.JournalDate<='$lastDate' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		# print "$query<br>";
		$totalThisYear_da = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.JournalDate >= '$firstDate' and S.JournalDate <= '$lastDate' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		#print "$query<br>";
		$totalThisYearFradrag_da = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag_da = $totalThisYear_da->total - $totalThisYearFradrag_da->total;

		

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1;";
		# print "$query<br>";
		$totalThisYear = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1;";
		#print "$query<br>";
		$totalThisYearFradrag = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag = $totalThisYear->total - $totalThisYearFradrag->total;
?>
    <tr>
        <td colspan="5">Feriepenge grunnlag</td>
        <td class="number"><nobr><? print $_lib['format']->Amount(array('value'=>$fpGrunnlag_da, 'return'=>'value')) ?></nobr></td>
        <td colspan="4"></td>
    </tr>
</table>
</form>
</body>
</html>
