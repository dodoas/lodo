<?
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

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";
$query_head     = "select S.*, A.AccountName, A.Address, A.City, A.ZipCode, A.SocietyNumber, A.TabellTrekk, A.ProsentTrekk from salary as S, accountplan as A where S.SalaryID='$SalaryID' and S.AccountPlanID=A.AccountPlanID";
$head           = $_lib['storage']->get_row(array('query' => $query_head));

$query_arb 		= "select a.Percent from kommune as k, arbeidsgiveravgift as a where a.Code=k.Sone";
$arb           	= $_lib['storage']->get_row(array('query' => $query_arb));

$ishovedmal = $head->SalaryConfID;

$query_salary   = "select * from salaryline where SalaryID = '$SalaryID' order by LineNumber asc";
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

$formname = "salaryUpdate";
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.66 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { 
    $msg = $_lib['message']->get();
$mcolor = (strstr($msg, "rror")) ? "red" : "black";
?>
    <div class="<? echo $mcolor ?> error"><? print $_lib['message']->get() ?><br/></div>
<? } ?>

<? print $message ?>

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
  </tr>
  <tr>
    <th class="sub">L <a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=$head->JournalID"; ?>&type=salary&view=1"><? print $head->JournalID ?></a>
    <th class="sub"><? print $_lib['form3']->text(array('table'=>'salary', 'field'=>'JournalDate', 'pk'=>$head->SalaryID, 'value'=>$head->JournalDate, 'OnChange'=>"update_period(this, '".$formname."', 'salary.JournalDate.".$head->SalaryID."', 'salary.Period.".$head->SalaryID."');")) ?>
    <th class="sub"><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'salary', 'field' => 'Period', 'pk'=>$head->SalaryID, 'value' => $head->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'pk' => $head->SalaryID, 'required'=>'1')); ?>
    <th class="sub"><?
        $aconf = array('table'=>'salary', 'field'=>'AccountPlanID', 'value'=>$head->AccountPlanID, 'tabindex'=>'', 'accesskey'=>'K', 'pk'=>$head->SalaryID, 'type'=> array('0' => 'employee'));
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    <th class="sub"><input type="text" name="salary.ValidFrom.<? print $head->SalaryID ?>" value="<? print $head->ValidFrom ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.ValidTo.<? print $head->SalaryID ?>" value="<? print $head->ValidTo ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.PayDate.<? print $head->SalaryID ?>" value="<? print $head->PayDate ?>" size="10" class="number">
    <th class="sub"><input type="text" name="salary.DomesticBankAccount.<? print $head->SalaryID ?>" value="<? print $head->DomesticBankAccount ?>" size="16" class="number">
  </tr>
  <tr>
    <th class="salaryhead">Tabelltrekk</th>
    <th class="salaryhead">Prosenttrekk</th>
    <th class="salaryhead">Skatteetaten</th>
  </tr>
  <tr>
    <th class="sub" colspan="1"><? print $head->TabellTrekk ?></th>
    <th class="sub"><? print $head->ProsentTrekk ?></th>
    <th class="sub"><a href="https://skort.skatteetaten.no/skd/trekk/trekk" target="_new">Vis trekktabell</a></th>
  </tr>
</table>
<br>
<table class="lodo_data">
  <tr>
    <th>Linje</th>
    <th>Tekst</th>
    <th>Antall periode</th>
    <th>Sats</th>
    <th>Bel&oslash;p periode</th>
    <th>Bel&oslash;p hittil i &aring;r</th>
    <th>Konto</th>
    <th>Avdeling</th>
    <th>Prosjekt</th>
    <th>F</th>
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
      ?>
      <tr>
        <? print $_lib['form3']->hidden(array('name'=>$counter, 'value'=>$line->SalaryLineID)) ?>
        <td>
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
            else
            {
                print $line->SalaryText;
            }
        ?>
        </td>
        <td><input type="text" name="salaryline.NumberInPeriod.<? print $line->SalaryLineID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->NumberInPeriod, 'return'=>'value')) ?>" size="5" class="number"></td>
        <td><input type="text" name="salaryline.Rate.<? print $line->SalaryLineID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->Rate, 'return'=>'value')) ?>" size="5" class="number"></td>
        <td class="number"><input type="text" name="salaryline.AmountThisPeriod.<? print $line->SalaryLineID ?>"   value="<? print $_lib['format']->Amount(array('value'=>$line->AmountThisPeriod, 'return'=>'value')) ?>" size="8" class="number"></td>
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
        <td><? if($accountplan->EnableDepartment)     { $_lib['form2']->department_menu2(array('table' => 'salaryline', 'field' => 'DepartmentID', 'value' => $line->DepartmentID, 'tabindex' => $tabindex++, 'acesskey' => 'V', 'pk' => $line->SalaryLineID)); } ?></td>
        <td><? if($accountplan->EnableProject)  { $_lib['form2']->project_menu2(array('table' => 'salaryline',  'field' =>  'ProjectID', 'value' => $line->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'pk' => $line->SalaryLineID)); } ?></td>
        <td>
          <? if($line->EnableVacationPayment) { print "ja"; }; ?>
          <? print $_lib['form3']->hidden(array('name' => 'EnableVacationPayment_' . $line->SalaryLineID, 'value' => $line->EnableVacationPayment)); ?>
        </td>
        <td><? print $line->SalaryCode ?></td>
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
  }
  ?>
<tr><td></td><td><b>Sum</b></td><td colspan="2"></td>
<td style="text-align: right;"><b><? print $_lib['format']->Amount(array('value'=>$sumThisPeriod, 'return'=>'value')) ?></b></td>
<td style="text-align: right;"><b><? print $_lib['format']->Amount(array('value'=>$sumThisYear, 'return'=>'value')) ?></b></td>
</tr>

<tr height="20">
  <td colspan="13">
</tr>
<tr>
<th colspan="13">Kommentar</th>
</tr>

<tr>
<td colspan="13">
<textarea name="salary.Comment.<? print $head->SalaryID ?>" cols="100" rows="4"><?= $head->Comment  ?></textarea>
</td>
</tr>

<tr>
<th colspan="13">Internkommentar</th>
</tr>

<tr>
<td colspan="13">
<textarea name="salary.InternComment.<? print $head->SalaryID ?>" cols="100" rows="4"><?= $head->InternComment  ?></textarea>
</td>
</tr>

<tr>
    <td colspan="<? echo ($head->FakturabankPersonID) ? '2' : '6'  ?>">
      <?
		if($_lib['sess']->get_person('FakturabankExportPaycheckAccess')) {
		    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_salary_fakturabanksend', 	'value'=>'Fakturabank (F)', 'accesskey'=>'F'));
		}

      ?>
     	<? if ($head->FakturabankPersonID) { ?>
<td colspan="4">Sendt til Fakturabank <? print $head->FakturabankDateTime ?>, av <? print $_lib['format']->PersonIDToName($head->FakturabankPersonID) ?></td>
		<? } ?>

    </td>
    <td colspan="2"></td>

    <td colspan="3">
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.list&SalaryperiodconfID=<?= $SalaryperiodconfID ?>">Tilbake til l&oslash;nnsslipp</a>
    </td>
    <td>
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.print&amp;SalaryID=<? print $SalaryID ?>" target="_new" />Vis</a>
    </td>
    <?
    if($_lib['sess']->get_person('AccessLevel') >= 2)
    {
        print $_lib['form3']->hidden(array('name'=>'fieldcount', 'value'=>$counter));
        if(!$head->Period)
        {
            ?>
            <td>
                <?php
                  if(!$head->LockedBy) echo '<input type="submit" name="action_salary_lock" value="L&aring;s (L)" accesskey="L" />';
                  else echo "L&aring;st av " . $head->LockedBy . " " . $head->LockedDate;

                  if(!$head->LockedBy || $_lib['sess']->get_person('AccessLevel') >= 4) 
                  {
                    echo '<input type="submit" name="action_salary_journal" value="Lagre (S)" accesskey="S" align="right" /><br /></td>';
                  }
        
                ?>

            </td>
            <?
        }
        elseif($accounting->is_valid_accountperiod($head->Period, $_lib['sess']->get_person('AccessLevel')))
        {
            ?>
            <td>
                <?php
                  if(!$head->LockedBy) echo '<input type="submit" name="action_salary_lock" value="L&aring;s (L)" accesskey="L" />';
                  else echo "L&aring;st av " . $head->LockedBy . " " . $head->LockedDate;

                  if(!$head->LockedBy || $_lib['sess']->get_person('AccessLevel') >= 4) 
                  {
                    echo '<input type="submit" name="action_salary_journal" value="Lagre (S)" accesskey="S" align="right" /><br /></td>';
                  }
            ?>
            </td>
            <?
        }
    }
  ?>
</tr>
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


<? includeinc('bottom') ?>
</body>
</html>
