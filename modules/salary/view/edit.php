<?
// needed to access session parameters for oauth
session_start();
/* $Id: edit.php,v 1.66 2005/10/28 17:59:41 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
#########################################
#This should be placed under firmaoppsett
$lineInFrom  =  10;
$lineInTo    =  69;
$lineOutFrom =  70;
$lineOutTo   = 100;
#########################################

$SalaryID       = (int) $_REQUEST['SalaryID'];
$SalaryConfID   = (int) $_REQUEST['SalaryConfID'];
$SalaryperiodconfID = (int) $_REQUEST['SalaryperiodconfID'];

$tmp_redirect_url = "$_SETUP[OAUTH_PROTOCOL]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// change only if full(with SalaryID) url
if (strpos($tmp_redirect_url, 'SalaryID') !== false) $_SESSION['oauth_tmp_redirect_back_url'] = $tmp_redirect_url;
// and if missing in url, add SalaryID
else $_SESSION['oauth_tmp_redirect_back_url'] = $tmp_redirect_url . "&SalaryID=" . $SalaryID;

includelogic('accounting/accounting');
includelogic('altinnsalary/altinnreport');
includelogic('car/car');

$accounting = new accounting();
require_once "record.inc";

// get all saved messages and remove them
if (isset($_SESSION['oauth_paycheck_messages']) && is_array($_SESSION['oauth_paycheck_messages'])) foreach ($_SESSION['oauth_paycheck_messages'] as $message) $_lib['message']->add($message);
unset($_SESSION['oauth_paycheck_messages']);

$query_head = sprintf("
select
  F.Email as FEmail,
  S.*,
  E.*,
  A.ProsentTrekk as AP_ProsentTrekk,
  E.MunicipalityPercent as Percent,
  (NOT (E.AccountName IS NULL)) as isUpdated
from
  salary as S
  left join (salaryextra as E)
    on (S.SalaryID = E.SalaryID),
  fakturabankemail F,
  accountplan A
where
  S.SalaryID = '%d'
  and F.AccountPlanID = S.AccountPlanID
  and A.AccountPlanID = S.AccountPlanID
", $SalaryID);

// Code to update old entries.
// Inserts account data into presistent table salaryextra
$head = $_lib['storage']->get_row(array('query' => $query_head));
if(!$head->isUpdated || isset($_POST['action_salary_update_extra'])) {
    $query_head     = "
     select
       F.Email as FEmail,
       S.*,
       A.AccountName,
       A.Address,
       A.City,
       A.ZipCode,
       A.IDNumber,
       A.SocietyNumber,
       A.TabellTrekk,
       A.ProsentTrekk as AP_ProsentTrekk
     from
       salary as S,
       fakturabankemail F,
       accountplan as A
     where
       S.SalaryID='$SalaryID'
       and S.AccountPlanID=A.AccountPlanID
       and F.AccountPlanID = A.AccountPlanID
    ";
    $head = $_lib['storage']->get_row(array('query' => $query_head));
    $query_arb = "select a.Percent from kommune as k, arbeidsgiveravgift as a where a.Code=k.Sone";
    $arb = $_lib['storage']->get_row(array('query' => $query_arb));

    $query_update_presistent = sprintf("
     replace
     into
       salaryextra
     (
       SalaryID,
       AccountName,
       Address,
       City,
       ZipCode,
       SocietyNumber,
       TabellTrekk,
       ProsentTrekk,
       MunicipalityPercent
     )
     VALUES (
       %d,
       '%s',
       '%s',
       '%s',
       '%s',
       '%s',
       '%s',
       '%s',
       '%s'
     );
     ", $head->SalaryID, $head->AccountName, $head->Address,
                                       $head->City, $head->ZipCode, (empty($head->SocietyNumber) ? $head->IDNumber : $head->SocietyNumber),
                                       $head->TabellTrekk, $head->ProsentTrekk, $arb->Percent);

    $_lib['db']->db_query($query_update_presistent);
    $_lib['message']->add("Updated presistent data");
}
else {
    $arb = $head;
}

$ishovedmal = $head->SalaryConfID;

$query_salary   = "select * from salaryline where SalaryID = '$SalaryID' order by LineNumber, SalaryText asc";
#print "$query_salary<br>";
$result_salary  = $_lib['db']->db_query($query_salary);

$info_query = sprintf("SELECT * FROM salaryinfo WHERE SalaryConfID = %d AND SalaryperiodconfID = %d LIMIT 1", $SalaryConfID, $SalaryperiodconfID);
$info_result = $_lib['db']->db_query($info_query);
$info_row = $_lib['db']->db_fetch_assoc($info_result);
$remove_info = sprintf("DELETE FROM salaryinfo WHERE SalaryConfID = %d AND SalaryperiodconfID = %d LIMIT 1", $SalaryConfID, $SalaryperiodconfID);
$_lib['db']->db_query($remove_info);

if($SalaryperiodconfID)
{
    $periodconf_query = sprintf("SELECT * FROM salaryperiodconf WHERE SalaryperiodconfID = %d", $SalaryperiodconfID);
    $periodconf_result = $_lib['db']->db_query($periodconf_query);
    $periodconf_row = $_lib['db']->db_fetch_assoc($periodconf_result);

    $interCommentProject = $_lib['db']->db_fetch_assoc( $_lib['db']->db_query(
                               sprintf("SELECT Heading FROM project WHERE ProjectID = %d", $info_row['project'])
                           ));

    $head->JournalDate = $periodconf_row['Voucherdate']; // name mismatch
    $head->Period      = $periodconf_row['Period'];
    $head->ValidFrom   = $periodconf_row['Fromdate'];
    $head->ValidTo     = $periodconf_row['Todate'];
    $head->InternComment = $interCommentProject['Heading'] . ': ' . $info_row['amount'];

    $entry_test_query = sprintf("SELECT * FROM salaryperiodentries WHERE JournalID = %d LIMIT 1", $head->JournalID);
    $entry_test_result = $_lib['db']->db_query($entry_test_query);
    if( !$_lib['db']->db_numrows($entry_test_result) )
    {
        $entry_query = sprintf("SELECT * FROM salaryperiodentries WHERE SalaryperiodconfID = %d AND AccountPlanID = %d AND Processed = 0",
                               $SalaryperiodconfID, $head->AccountPlanID);
        $entry_result = $_lib['db']->db_query($entry_query);
        if($_lib['db']->db_numrows($entry_result))
        {
            $entry_update_query = sprintf(
                "UPDATE salaryperiodentries SET Processed = 1, JournalID = %d, SalaryID = %d WHERE SalaryperiodconfID = %d AND AccountPlanID = %d AND Processed = 0 LIMIT 1",
                $head->JournalID, $SalaryID, $SalaryperiodconfID, $head->AccountPlanID);
        }
        else
        {
            $entry_update_query = sprintf(
                 "INSERT INTO salaryperiodentries (`SalaryperiodconfID`, `JournalID`, `SalaryID`, `AccountPlanID`, `Processed`)
                                            VALUES(%d, %d, %d, %d, 1);",
                 $SalaryperiodconfID, $head->JournalID, $SalaryID, $head->AccountPlanID);
        }

        $_lib['db']->db_query($entry_update_query);
    }
}

$SalaryperiodconfID_row = $_lib['db']->get_row( array( 'query' => sprintf("SELECT SalaryperiodconfID FROM salaryperiodconf WHERE Period = '%s'", $head->Period) ) );
$SalaryperiodconfID = $SalaryperiodconfID_row->SalaryperiodconfID;

$all_shift_types = $_lib['form3']->_ALTINN['ShiftTypes'];
$all_work_time_scheme_types = $_lib['form3']->_ALTINN['WorkTimeSchemeTypes'];
$all_type_of_employement_types = $_lib['form3']->_ALTINN['TypeOfEmploymentTypes'];
$tmp_all_occupations = $_lib['db']->get_hashhash(array('query' => $_lib['form3']->_QUERY['form']['occupationmenu'], 'key' => 'OccupationID'));
$all_occupations = array();
foreach($tmp_all_occupations as $occupation) { $all_occupations[$occupation['OccupationID']] = $occupation['YNr'] . " " . $occupation['LNr'] . " " . htmlentities($occupation['Name']); }
$tmp_all_subcompanies = $_lib['db']->get_hashhash(array('query' => $_lib['form3']->_QUERY['form'][subcompanymenu], 'key' => 'SubcompanyID'));
$all_subcompanies = array();
foreach($tmp_all_subcompanies as $subcompany) { $all_subcompanies[$subcompany['SubcompanyID']] = htmlentities($subcompany['Name']) . " " . $subcompany['OrgNumber']; }

$kommune = $_lib['db']->get_row( array( 'query' => sprintf("SELECT * FROM kommune WHERE KommuneID ='%d'", $head->KommuneID)  ) );

$formname = "salaryUpdate";
?>

<? print $_lib['sess']->doctype ?>

<?
  $work_relation_query = sprintf("SELECT
                        w.AccountPlanID as AccountPlanID,
                        w.WorkRelationID as WorkRelationID,
                        w.SubcompanyID as SubcompanyID,
                        sc.Name as SubcompanyName,
                        w.WorkTimeScheme as WorkTimeScheme,
                        w.ShiftType as ShiftType,
                        w.TypeOfEmployment as TypeOfEmployment,
                        w.OccupationID as OccupationID,
                        w.WorkStart as WorkStart,
                        w.WorkStop as WorkStop,
                        w.InCurrentPositionSince as InCurrentPositionSince,
                        w.WorkPercent as WorkPercent,
                        w.WorkPercentUpdatedAt as WorkPercentUpdatedAt,
                        w.WorkMeasurement as WorkMeasurement,
                        w.SalaryDateChangedAt as SalaryDateChangedAt
                      FROM
                        workrelation w
                        LEFT JOIN subcompany sc ON w.SubcompanyID = sc.SubcompanyID
                      GROUP BY
                        WorkRelationID
                      ");
  $work_relation_result = $_lib['db']->db_query($work_relation_query);
  $work_relations = array();
  $fetched_work_relations = array();
  while($row = mysqli_fetch_assoc($work_relation_result)) $fetched_work_relations[] = $row;
  foreach ($fetched_work_relations as $fetched_work_relation) {
    if($fetched_work_relation["OccupationID"] == 0) $fetched_work_relation["OccupationID"] = "";
    $fetched_work_relation["WorkStop"] != "0000-00-00" ? $work_stop = $fetched_work_relation["WorkStop"] : $work_stop = "";
    $fetched_work_relation["WorkRelationName"] = htmlentities($fetched_work_relation["WorkRelationID"] ." - ". $fetched_work_relation["SubcompanyName"] ." (". $fetched_work_relation["WorkStart"] ." - ". $work_stop .")");
    $work_relations[$fetched_work_relation["AccountPlanID"]][$fetched_work_relation["WorkRelationID"]] = $fetched_work_relation;
  }
?>

<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.66 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="<? print $formname ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="SalaryID" value="<? print $SalaryID ?>">
<input type="hidden" name="AccountPlanID" value="<? print $head->AccountPlanID ?>">

<table class="lodo_data">
  <tr>
    <th>Bilagsnummer
    <th>Bilagsdato
    <th>Periode
    <th>Ansatt
    <th>Fra dato
    <th>Til dato
    <th>Utbetalt dato
    <th>Konto for utbetaling
    <th>Skattekommune
    <th>Altinndato
    <th>Rapportert til Altinn
    <th>Arbeidsforhold
  </tr>
  <tr>
    <th class="sub">L <a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=$head->JournalID"; ?>&type=salary&view=1"><? print $head->JournalID ?></a>
    <th class="sub"><? print $_lib['form3']->text(array('table'=>'salary', 'field'=>'JournalDate', 'pk'=>$head->SalaryID, 'value'=>$head->JournalDate, 'OnChange'=>"update_period(this, '".$formname."', 'salary.JournalDate.".$head->SalaryID."', 'salary.Period.".$head->SalaryID."');")) ?>
    <th class="sub"><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'salary', 'field' => 'Period', 'pk'=>$head->SalaryID, 'value' => $head->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'pk' => $head->SalaryID, 'required'=>'1')); ?>
    <th class="sub"><?
        $aconf = array('table'=>'salary', 'field'=>'AccountPlanID', 'value'=>$head->AccountPlanID,
                       'tabindex'=>'', 'accesskey'=>'K', 'pk'=>$head->SalaryID, 'type'=> array('0' => 'employee'),
                       'onchange'=>"selected_employee = this.value; printWorkRelationsSelectForEmployee();");
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    <th class="sub"><input type="text" name="salary.ValidFrom.<? print $head->SalaryID ?>" value="<? print $head->ValidFrom ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.ValidTo.<? print $head->SalaryID ?>" value="<? print $head->ValidTo ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.PayDate.<? print $head->SalaryID ?>" value="<? print $head->PayDate ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.DomesticBankAccount.<? print $head->SalaryID ?>" value="<? print $head->DomesticBankAccount ?>" size="16" class="number">
    <input type="hidden" name="salary.KommuneID.<? print $head->SalaryID ?>" value="<? print $head->KommuneID ?>">
    <th class="sub"><? print $kommune->KommuneNumber . " " . htmlentities($kommune->KommuneName); ?></th>

    <?
      // if date is set just show it unless the user is admin in which case show the input
      if (is_null($head->ActualPayDate) || $head->ActualPayDate == '' || $head->ActualPayDate == '0000-00-00' || $_lib['sess']->get_person('AccessLevel') >= 4) {
        // used to enable/disable the update altinndato button
        // if the user is an admin he will always have it enabled
        $altinndato_set = false;
    ?>
    <th class="sub"><input type="text" name="salary.ActualPayDate.<? print $head->SalaryID ?>" value="<? print $head->ActualPayDate ?>" size="10" class="number"></th>
    <?
      } else {
        $altinndato_set = true;
    ?>
    <th class="sub"><? print $head->ActualPayDate ?></th>
    <?
      }
    ?>
    <th class="sub"><? print getAltinnReportedDateTime($head->SalaryID); ?>
    <th class="sub"><div id='workRelationSelect'></div>
  </tr>
  <tr>
    <th>Tabelltrekk</th>
    <th>Prosenttrekk</th>
    <th>Skatteetaten</th>
    <th colspan="2">Skifttype</th>
    <th>Arbeidstid</th>
    <th colspan="3">Ansettelsestype</th>
    <th colspan="2">Yrke</th>
    <th>Ansatt ved</th>
  </tr>
  <tr>
    <th class="sub" colspan="1"><? print $head->TabellTrekk ?></th>
    <th class="sub"><? print $head->AP_ProsentTrekk ?></th>
    <th class="sub"><a href="https://skort.skatteetaten.no/skd/trekk/trekk" target="_new">Vis trekktabell</a></th>
    <th id="ShiftType_display_value" class="sub" colspan="2"><? print $all_shift_types[$head->ShiftType] ?></th>
    <th id="WorkTimeScheme_display_value" class="sub"><? print $all_work_time_scheme_types[$head->WorkTimeScheme] ?></th>
    <th id="TypeOfEmployment_display_value"class="sub" colspan="3"><? print $all_type_of_employement_types[$head->TypeOfEmployment] ?></th>
    <th id="OccupationID_display_value" class="sub" colspan="2"><? print $all_occupations[$head->OccupationID] ?></th>
    <th id="SubcompanyID_display_value" class="sub"><? print $all_subcompanies[$head->SubcompanyID] ?></th>
  </tr>
</table>
<br>
<table class="lodo_data salary_table">
  <tr>
    <th class="line_number">Linje</th>
    <th>Tekst</th>
    <th>Fordel</th>
    <th>Antall periode</th>
    <th>Sats</th>
    <th>Bel&oslash;p periode</th>
    <th>Bel&oslash;p hittil i &aring;r</th>
    <th>Konto</th>
    <th>Bil</th>
    <th>Prosjekt</th>
    <th>Avdeling</th>
    <th>Skatt</th>
    <th>Arb. giv.</th>
    <th>Feriep.</th>
    <th>Altinn</th>
    <th>Kode</th>
    <th colspan="2"></th>
  </tr>
  <?
  $counter = 0;
  $sumThisYear = 0;
  $sumThisPeriod = 0;
  $vacationPayment = 0;
  $listetlines = array();
  while($line = $_lib['db']->db_fetch_object($result_salary))
  {
      $counter++;
      if ($line->SalaryCode != 950 && (float)$line->NumberInPeriod * (float)$line->Rate != (float)$line->AmountThisPeriod) $WrongCalculation = "style='color: red'";
      elseif ($line->SalaryCode == 950 && floor((float)$line->NumberInPeriod * (float)$line->Rate) != floor((float)$line->AmountThisPeriod)) $WrongCalculation = "style='color: red'";
      else $WrongCalculation = "";
      ?>
      <tr>
        <? print $_lib['form3']->hidden(array('name'=>$counter, 'value'=>$line->SalaryLineID)) ?>
        <td class="line_number">
        <?
            if($ishovedmal == 1)
            {
                ?><input type="text" name="salaryline.LineNumber.<? print $line->SalaryLineID ?>" value="<? print $line->LineNumber ?>" size="5" class="number"><?
            }
            else
            {
                ?><input type="hidden" name="salaryline.LineNumber.<? print $line->SalaryLineID ?>" value="<? print $line->LineNumber ?>"><?
                print $line->LineNumber;
            }
        ?>
        </td>
        <td>
        <?
            if($ishovedmal == 1)
            {
                ?><input type="text" name="salaryline.SalaryText.<? print $line->SalaryLineID ?>" value="<? print $line->SalaryText ?>" size="30" class="number"><?
            }
            else {
              print $line->SalaryText;
              if($line->SalaryDescription)
                print " (" . $_lib['form3']->_ALTINN['SalaryLineDescriptionTypes'][$line->SalaryDescription] . ")";
            }
        ?>
        </td>
        <td>
          <?
            if($ishovedmal == 1){
                ?><input type="text" name="salaryline.Fordel.<? print $line->SalaryLineID ?>" value="<? print $line->Fordel ?>" size="30" class="number"><?
            } else {
              print $_lib['form3']->_ALTINN['Fordel'][$line->Fordel];
            }
          ?>
        </td>
        <td><input <? print $WrongCalculation; ?> type="text" name="salaryline.NumberInPeriod.<? print $line->SalaryLineID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->NumberInPeriod, 'return'=>'value')) ?>" size="5" class="number"></td>
        <td><input <? print $WrongCalculation; ?> type="text" name="salaryline.Rate.<? print $line->SalaryLineID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->Rate, 'return'=>'value')) ?>" size="5" class="number"></td>
        <td class="number"><input <? print $WrongCalculation; ?> type="text" name="salaryline.AmountThisPeriod.<? print $line->SalaryLineID ?>"   value="<? print $_lib['format']->Amount(array('value'=>$line->AmountThisPeriod, 'return'=>'value')) ?>" size="8" class="number"></td>
        <td class="number">
            <nobr>
            <?

                    $firstDate = $_lib['date']->get_this_year($head->Period)."-01-01";
                    $query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.JournalDate>='$firstDate' and S.JournalDate<='$head->JournalDate' and SL.LineNumber=$line->LineNumber and S.AccountPlanID='$head->AccountPlanID'";
                    #print "$query<br>";
                    $totalThisYear = $_lib['storage']->get_row(array('query' => $query));

                    if($line->LineNumber >= $lineInFrom and $line->LineNumber <= $lineInTo)
                    {
						if($listetlines[$line->LineNumber][$line->AccountPlanID] != 1)
                    		$sumThisYear += $totalThisYear->total;
                        $sumThisPeriod += $line->AmountThisPeriod;
                    }
                    elseif($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
                    {
//                        if($listetlines[$line->LineNumber][$line->AccountPlanID] == 1)
                    		$sumThisYear -= $totalThisYear->total;
                        $sumThisPeriod -= $line->AmountThisPeriod;
                        #print "$sumThisPeriod -= $line->AmountThisPeriod<br>";
                    }
                    if($line->EnableVacationPayment == 1)
                    {
                        #print "$vacationPayment += $totalThisYear->total<br>";
                        if($line->LineNumber == $oldLine)
                    	if($line->LineNumber >= $lineInFrom and $line->LineNumber <= $lineInTo)
                    	{
                	        $vacationPayment        += $totalThisYear->total;
            	            $VacationPaymentPeriod  += $line->AmountThisPeriod;
        	                $VacationPaymentYear    += $totalThisYear->total;
    	                    #print "$vacationPayment += $totalThisYear->total<br>";
	                    }
	                    elseif($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
                    	{
                	        $vacationPayment        -= $totalThisYear->total;
            	            $VacationPaymentPeriod  -= $line->AmountThisPeriod;
        	                $VacationPaymentYear    -= $totalThisYear->total;
    	                    #print "$vacationPayment -= $totalThisYear->total<br>";
	                    }
                    	$oldLine = $line->LineNumber;
                    }

                    if($line->EnableEmployeeTax == 1)
                    {
                    	if($line->LineNumber >= $lineInFrom and $line->LineNumber <= $lineInTo)
	                        $Arbeidsgiveravgift    += $line->AmountThisPeriod;
	                    elseif($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
	                        $Arbeidsgiveravgift    -= $line->AmountThisPeriod;
                    }

                    if($listetlines[$line->LineNumber][$line->AccountPlanID] == 1)
                    {
                        print "- Sum over -";
                    } else {
                        print $_lib['format']->Amount(array('value'=>$totalThisYear->total, 'return'=>'value'));
                    }
                    $listetlines[$line->LineNumber][$line->AccountPlanID] = 1;
                    #print "listetlines[$line->LineNumber][$line->AccountPlanID] = 1";
            ?>
            </nobr>
        </td>
        <td>
        <?
            if($ishovedmal == 1)
            {
                $aconf = array('table'=>'salaryline', 'field'=>'AccountPlanID', 'pk'=>$line->SalaryLineID, 'value'=>$line->AccountPlanID, 'tabindex'=>'', 'accesskey'=>'', 'type' => array(0 => 'hovedbok'));
                print $_lib['form3']->accountplan_number_menu($aconf);
            }
            else
            {
                print $line->AccountPlanID;
            }
            $accountplan = $accounting->get_accountplan_object($line->AccountPlanID);
        ?>
        </td>
        <td><? if($accountplan->EnableCar)     { $_lib['form2']->car_menu2(array('table' => 'salaryline', 'field' => 'CarID', 'value' => $line->CarID, 'tabindex' => $tabindex++, 'pk' => $line->SalaryLineID, 'active_reference_date' => $head->JournalDate, 'unset' => true)); } ?></td>
        <td><? if($accountplan->EnableProject)  { $_lib['form2']->project_menu2(array('table' => 'salaryline',  'field' =>  'ProjectID', 'value' => $line->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'pk' => $line->SalaryLineID, 'unset' => true)); } ?></td>
        <td><? if($accountplan->EnableDepartment)     { $_lib['form2']->department_menu2(array('table' => 'salaryline', 'field' => 'DepartmentID', 'value' => $line->DepartmentID, 'tabindex' => $tabindex++, 'acesskey' => 'V', 'pk' => $line->SalaryLineID, 'unset' => true)); } ?></td>

        <td><? print $line->MandatoryTaxSubtraction ? "ja" : "nei" ?></td>
        <td><? print $line->EnableEmployeeTax ? "ja" : "nei" ?></td>
        <td>
          <? print $line->EnableVacationPayment ? "ja" : "nei" ?>
          <? print $_lib['form3']->hidden(array('name' => 'EnableVacationPayment_' . $line->SalaryLineID, 'value' => $line->EnableVacationPayment)); ?>
        </td>
        <td>
          <? print $line->SendToAltinn ? "ja" : "nei" ?>
        </td>
        <td><?
           if ($line->SalaryCode == 950) print $_lib['form3']->hidden(array('name' => 'floor_' . $line->SalaryLineID, 'value' => 1));
           print $line->SalaryCode ?></td>
        <td>
        <? if($_lib['sess']->get_person('AccessLevel') >= 2  && $accounting->is_valid_accountperiod($head->Period, $_lib['sess']->get_person('AccessLevel'))) { ?>
            <a href="<? print $MY_SELF ?>&amp;SalaryLineID=<? print $line->SalaryLineID ?>&amp;SalaryConfID=<? print $SalaryConfID ?>&amp;SalaryID=<? print $SalaryID ?>&amp;action_salaryline_delete=1" class="button">Slett</a>
        <? } ?>
        </td>
        <td>
            <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
            <nobr><? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&amp;SalaryID=<? print $SalaryID ?>&amp;SalaryLineID=<? print $line->SalaryLineID ?>&amp;action_salaryline_new=1" class="button">Ny linje nr <? print $line->LineNumber ?></a></nobr><?}?>
            <? } ?>
        </td>
    </tr>
    <?
      if ($line->SalaryDescription == 'bil') {
        // first tr, for car headers
        $line_one = array('');
        // second tr, for data, selects, inputs
        $line_two = array('');
        array_push($line_one, 'Bil');
        array_push($line_two, $_lib['form2']->car_menu2_str(array('table' => 'salaryline', 'field' => 'FreeCarID', 'value' => $line->FreeCarID, 'tabindex' => $tabindex++, 'pk' => $line->SalaryLineID, 'active_reference_date' => $head->JournalDate)));

        $car = car::get($line->FreeCarID);
        if ($car) {
          array_push($line_one, 'Listepris');
          array_push($line_two, $_lib['format']->Amount($car->OfficalPrice));

          array_push($line_one, '<a href="https://www.hertzbilpool.no/" target="_blank">BilPool</a>');
          array_push($line_two, $car->Carpool ? 'Ja' : 'Nei');
        }

        ?>
        <tr>
          <td>
          <?
            for ($i=0; $i < count($line_one); $i++) {
              print($line_one[$i]);
              print("</td><td>");
            }
          ?>
          </td>
        </tr>

        <tr>
          <td>
          <?
            for ($i=0; $i < count($line_two); $i++) {
              print($line_two[$i]);
              print("</td><td>");
            }
          ?>

          </td>
        </tr>

        <?
      }
    ?>

    <?
  }
  ?>
<tr>
  <td></td>
  <td><b>Sum</b></td>
  <td colspan="3"></td>
<td style="text-align: right;"><b><? print $_lib['format']->Amount(array('value'=>$sumThisPeriod, 'return'=>'value')) ?></b></td>
<td style="text-align: right;"><b><? print $_lib['format']->Amount(array('value'=>$sumThisYear, 'return'=>'value')) ?></b></td>
</tr>

<tr height="20">
  <td colspan="5">
  <td colspan="11">Skattetrekk trekkes bare med hele kroner
</tr>

<tr>
<th colspan="15">Kommentar</th>
</tr>

<tr>
<td colspan="15">
<textarea name="salary.Comment.<? print $head->SalaryID ?>" cols="100" rows="4"><?= $head->Comment  ?></textarea>
</td>
</tr>

<tr>
<th colspan="15">Internkommentar</th>
</tr>

<tr>
<td colspan="15">
<textarea name="salary.InternComment.<? print $head->SalaryID ?>" cols="100" rows="4"><?= $head->InternComment  ?></textarea>
</td>
</tr>

<?
    if($_lib['sess']->get_person('AccessLevel') >= 2)
    {
        print $_lib['form3']->hidden(array('name'=>'fieldcount', 'value'=>$counter));
        if(!$head->Period || $accounting->is_valid_accountperiod($head->Period, $_lib['sess']->get_person('AccessLevel')))
        {
            ?>
        <tr>
          <td colspan='14'>
          <?php
            if(!$head->LockedBy || $_lib['sess']->get_person('AccessLevel') >= 4)
            {
              echo '<input type="submit" name="action_salary_journal" value="Lagre (S)" accesskey="S" align="right" />';
              echo '<input type="submit" name="action_salary_update_altinn_fields" value="Lagre altinnfelter" align="right" />';
            }

            echo '<input type="submit" name="action_salary_internal" value="Lagre internkommentar(S)" accesskey="S" align="right" />';
            echo '<input type="submit" name="action_salary_update_extra" value="Updater kontoinformasjon" accesskey="U" align="right" />';
            echo '<input type="submit" name="action_altindato_update" value="Lagre altinndato" align="right" ' . (($altinndato_set) ? 'disabled' : '') . '/>';
          ?>

          </td>
        </tr>
        <tr>
          <td colspan='14'>
          <?php
            $is_altinn_date_set = (is_null($head->ActualPayDate) || $head->ActualPayDate == '' || $head->ActualPayDate == '0000-00-00') ? 'false' : 'true';
            $altinn_arguments = "'$head->ShiftType', '$head->WorkTimeScheme', '$head->TypeOfEmployment', $head->OccupationID, $head->SubcompanyID, $is_altinn_date_set";
            $report_for_salary = new altinn_report($head->Period, array($head->SalaryID), array($head->WorkRelationID), false, true);
            $report_for_salary->populateReportArray();
            $no_altinn_validation_errors = empty($report_for_salary->errors);
            $altinn_validations_error_array = (($no_altinn_validation_errors) ? array() : $report_for_salary->errors);
            $altinndate_missing_error_array = ($head->ActualPayDate == '0000-00-00' || empty($head->ActualPayDate)) ? array("Altinndato er ikke satt") : array();
            $errors = array_merge($altinndate_missing_error_array, $altinn_validations_error_array);
            if ($errors) echo "<div class='warning'>" . implode($errors, "<br/>") . "</div>";
            if(!$head->LockedBy) echo '<input type="submit" name="action_salary_lock" value="L&aring;s (L)" accesskey="L" onclick="return checkIfAltinnFieldsSetAndConfirm(\'Er du sikker?\', '.$altinn_arguments.');" ' . (($no_altinn_validation_errors) ? '' : 'disabled') . ' />';
          ?>
          </td>
        </tr>
        <?
        }
        else
        {
            echo "<td>Perioden er stengt</td>";
        }
    }
  ?>
</tr>

<tr>
  <td colspan = "7"></td>
  <td colspan="6">
  </td>
</tr>

<tr>
  <td colspan = "7"></td>
  <td colspan="6">
      <?
		if($_lib['sess']->get_person('FakturabankExportPaycheckAccess')) {
                    if($head->FEmail)
                        print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_salary_fakturabanksend', 	'value'=>'Fakturabank (F)', 'accesskey'=>'F', 'disabled' => !$no_altinn_validation_errors));
                    else
                        print "Mangler fakturabankepost";
		}

      ?>
  </td>
</tr>

<tr>
  <td colspan = "7">

  <?
    if($_lib['sess']->get_person('AccessLevel') > 1 && $head->UpdatedBy) echo $head->UpdatedAt . " lagret av " . $_lib['format']->PersonIDToName($head->UpdatedBy);
  ?>
  </td>
  <td colspan = "4">Fakturabankepost: <?php print $head->FEmail; ?></td>
</tr>
<tr>
  <td colspan = "7">
  <?
    if($_lib['sess']->get_person('AccessLevel') > 1 && $head->AltinnFieldsUpdatedBy) echo $head->AltinnFieldsUpdatedAt . " altinnfelter lagret av " . $_lib['format']->PersonIDToName($head->AltinnFieldsUpdatedBy);
  ?>
  </td>
  <td colspan = "4">Kommune: <? if(!$kommune) { echo "<span style='color: red'>mangler kommune</span>"; } else { echo $kommune->KommuneNumber . " " . $kommune->KommuneName; } ?></td>
</tr>
<tr>
  <td colspan = "7">
  <?
    if($_lib['sess']->get_person('AccessLevel') > 1 && $head->ActualPayDateUpdatedBy) echo $head->ActualPayDateUpdatedAt . " altinndato lagret av " . $_lib['format']->PersonIDToName($head->ActualPayDateUpdatedBy);
  ?>
  </td>
  <? $personal_number = empty($head->SocietyNumber) ? $head->IDNumber : $head->SocietyNumber; ?>
  <td colspan = "4">Personnummer: <? echo $personal_number ?></td>
</tr>
<tr>
  <td colspan = "11">
  <?
    if($_lib['sess']->get_person('AccessLevel') > 1 && $head->LockedBy) echo $head->LockedDate . " l&aring;st av " . $head->LockedBy;
  ?>
  </td>
</tr>

<tr>
  <td colspan = "11">
      <? if ($_lib['sess']->get_person('AccessLevel') > 1 && $head->FakturabankPersonID) { ?>
           <? print $head->FakturabankDateTime ?> fakturaBank <? print $_lib['format']->PersonIDToName($head->FakturabankPersonID) ?>
      <? } ?>
  </td>
</tr>

<tr>
    <td colspan="8"></td>

    <td colspan="3">
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.list&SalaryperiodconfID=<?= $SalaryperiodconfID ?>">Tilbake til l&oslash;nnsslipp</a>
    </td>
    <td>
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.print&amp;SalaryID=<? print $SalaryID ?>" target="_new" />Utskrift</a>
    </td>
</tr>
<tr>
<td colspan="4"></td>
<td colspan="7">
<?
  if ($message) $_lib['message']->add($message);
  if($_lib['message']->get()) {
    $msg = $_lib['message']->get();
    $mcolor = (strstr($msg, "rror")) ? "red" : "black";
?>
    <div class="<? echo $mcolor ?> error"><? print $_lib['message']->get() ?><br/></div>
<? } ?>

</tr>
</td>


</table>
<table>
<?php
        $firstDate = substr($head->JournalDate, 0, 4) . "-01-01";
		$lastDate = $head->JournalDate;
		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.JournalDate>='$firstDate' and S.JournalDate<='$lastDate' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		#print "$query<br>";
		$totalThisYear_da = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.JournalDate >= '$firstDate' and S.JournalDate <= '$lastDate' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		#print "$query<br>";
		$totalThisYearFradrag_da = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag_da = $totalThisYear_da->total - $totalThisYearFradrag_da->total;


		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1;";
		#print "$query<br>";
		$totalThisYear = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1;";
		#print "$query<br>";
		$totalThisYearFradrag = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag = $totalThisYear->total - $totalThisYearFradrag->total;
?>
<tr>
  <td>Feriepenge grunnlag periode</td>
  <td><nobr><? print $_lib['format']->Amount(array('value'=>$fpGrunnlag, 'return'=>'value')) ?></nobr></td>
</tr>
<tr>
  <td>Feriepenger i periode</td>
  <td><nobr><? print $_lib['format']->Amount(array('value'=>$fpGrunnlag * 0.12, 'return'=>'value')) ?></nobr></td>
</tr>
<tr>
  <td>Arbeidsgiveravgift</td>
  <td><nobr><? print $_lib['format']->Amount(array('value'=> ($Arbeidsgiveravgift * ((($arb->Percent + 100) /100)-1)) , 'return'=>'value')) ?></nobr></td>
  <? print $_lib['form3']->hidden(array('name'=>"salary_VacationPayment_$SalaryID", 'value'=>$fpGrunnlag_da)) ?>
</tr>
<tr>
  <td>Feriepenge grunnlag dette &aring;r</td>
  <td><nobr><? print $_lib['format']->Amount(array('value'=>$fpGrunnlag_da, 'return'=>'value')) ?></nobr></td>
</tr>
</table>
</form>
<b><? print "linje $lineInFrom - $lineInTo = inntekt" ?></b><br />
<b><? print "linje $lineOutFrom - $lineOutTo = utgift" ?></b><br />

<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&amp;SalaryID=<? print $SalaryID ?>&amp;SalaryConfID=<? print $head->SalaryConfID ?>&amp;action_salary_updatesalarycode=1" class="button">Hent kode/feriepenger/arbeidsgiveravgift flagg fra ansatt mal</a><?}?>


<? includeinc('bottom');
unset($_SESSION['oauth_paycheck_sent']);
?>

<script type="text/javascript">
  var all_work_relations = <? print json_encode($work_relations); ?>;
  var selected_employee = <? print $head->AccountPlanID; ?>;
  var selected_work_relation_id = <? print ($head->WorkRelationID ? $head->WorkRelationID : 0); ?>;
  var selected_work_relation = all_work_relations[selected_employee][selected_work_relation_id];

  var fields_to_change = { WorkTimeScheme: <? print json_encode($all_work_time_scheme_types); ?>,
                           ShiftType: <? print json_encode($all_shift_types); ?>,
                           TypeOfEmployment: <? print json_encode($all_type_of_employement_types); ?>,
                           OccupationID: <? print json_encode($all_occupations); ?>,
                           SubcompanyID: <? print json_encode($all_subcompanies); ?> };

  function changeDisplayValueTo(id, text) {
    document.getElementById(id + "_display_value").innerHTML = text;
  }

  function printWorkRelationsSelectForEmployee() {
    var work_relations = all_work_relations[selected_employee];
    var out = "";
    out += "<select name='salary_WorkRelationID_<? print $SalaryID; ?>' onchange='selected_work_relation = all_work_relations[selected_employee][this.value]; updateValuesWithWorkRelationInfo();'>"
    out += "  <option value=''>Ikke valgt</option>";
    for(var i in work_relations) {
      is_selected = (work_relations[i]["WorkRelationID"] == selected_work_relation_id) ? ' selected ' : '';
      out += "  <option value='" + work_relations[i]["WorkRelationID"] + "'" + is_selected + ">" + work_relations[i]["WorkRelationName"] + "</option>";
    }
    out += "</select>";
    $("#workRelationSelect").html(out);
  }

  function updateValuesWithWorkRelationInfo() {
    if(selected_work_relation) {
      for(field_name in fields_to_change) {
        field_value = selected_work_relation[field_name];
        changeDisplayValueTo(field_name, fields_to_change[field_name][field_value]);
      }
    }
  }

  $(document).ready(function() {
    printWorkRelationsSelectForEmployee();
  })

</script>

</body>
</html>
