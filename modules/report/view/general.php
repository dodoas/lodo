<?
#table
$report = 1;
if($_REQUEST['show_report1']){
  $reportid = 1;
}
elseif($_REQUEST['show_report2']){
  $reportid = 2;
}
elseif($_REQUEST['show_report3']){
  $reportid = 3;
}
elseif($_REQUEST['show_report4']){
  $reportid = 4;
}
elseif($_REQUEST['show_report5']){
  $reportid = 5;
}
elseif($_REQUEST['show_report6']){
  $reportid = 6;
}
elseif($_REQUEST['show_report7']){
  $reportid = 7;
}
elseif($_REQUEST['show_report8']){
  $reportid = 8;
}
elseif($_REQUEST['show_report9']){
  $reportid = 9;
}
elseif($_REQUEST['show_report10']){
  $reportid = 10;
}

$FromPeriod         = $_REQUEST['report_FromPeriod'];
$ToPeriod           = $_REQUEST['report_ToPeriod'];
$EnableLastYear     = $_REQUEST['report_EnableLastYear'];
$EnableOnlyPartSum  = $_REQUEST['report_EnableOnlyPartSum'];

$ThisYear           = $_lib['date']->get_this_year($ToPeriod);
$PrevYear           = $_lib['date']->get_this_year($_lib['date']->get_this_period_last_year($ToPeriod));

// includealogic('altinnmapping');
includelogic('linetextmap/linetextmap');
#Oversett linjenavn
$linetext = new linetextmap(array('ReportID' => $reportid));

// $map = new AltinnMapping($reportid);

includelogic('report/general');

$report = new GeneralReport(array());
list($lineHash, $partHash, $groupHash, $totalHash, $Total) = $report->GetReport(array('fromPeriod'=> $FromPeriod, 'toPeriod'=> $ToPeriod, 'enableLastYear'=> $EnableLastYear, 'report'=> $reportid));

/*
print_r($lineHash);
print "<br>";
print_r($partHash);
print "<br>";
print_r($groupHash);
print "<br>";
print_r($totalHash);
print "<br>";
print_r($Total);
print "<br>";
*/

print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - general report</title>
    <meta name="cvs"                content="$Id: general.php,v 1.31 2005/09/08 08:51:17 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>
<h2>
Generell rapport: <? print $reportid ?><br>
For periode '<? print $_REQUEST['report_FromPeriod'] ?>' til '<? print $_REQUEST['report_ToPeriod'] ?>'
</h2>

<table  class="lodo_data" cellspacing="0">
  <tr class="voucher">
    <th class="sub" colspan="3"></th>
    <th class="sub" colspan="3"><? print $ThisYear?></th>
    <?
    if($EnableLastYear)
    {
        ?>
        <th class="sub" colspan="3"><? print $PrevYear?></th>
        <?
    }
    ?>
  </tr>
  <tr class="voucher">
    <th class="sub">Linje</th>
    <th class="sub">Kontonummer</th>
    <th class="sub">Kto navn</th>
    <th class="sub">Inn</th>
    <th class="sub">Ut</th>
    <th class="sub">Saldo</th>
    <?
    if($EnableLastYear)
    {
        ?>
        <th class="sub">Inn</th>
        <th class="sub">Ut</th>
        <th class="sub">Saldo</th>
        <?
    }
    ?>
  </tr>
    <?
    $sumInTotal  = 0;
    $sumOutTotal = 0;

    if(!$EnableOnlyPartSum)
    {
        foreach($lineHash as $lineNum => $lineData)
        {
            foreach($lineData  as $AccountPlanID => $AccountplanData )
            {
                #########################################################################
                #Show delsum
                if( (isset($lineNumOld)) and ($lineNumOld != $lineNum) )
                {
                   ?>
                   <tr class="voucher">
                       <td colspan="9"><hr></td>
                   </tr>
                   <tr class="voucher">
                       <td><b>Delsum <? print $lineNumOld ?></b></td>
                       <td colspan="2"><b><? print $linetext->getTextFromLineNr(array('Line' =>$lineNumOld, 'LanguageID' => 'no')) ?></b></td>
                       <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$ThisYear]['in']) ?></td>
                       <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$ThisYear]['out']) ?></td>
                       <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$ThisYear]['saldo']) ?></td>
                       <?
                       if($EnableLastYear)
                       {
                            ?>
                           <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$PrevYear]['in']) ?></td>
                           <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$PrevYear]['out']) ?></td>
                           <td class="number">&nbsp;<? print $_lib['format']->Amount($partHash[$lineNumOld][$PrevYear]['saldo']) ?></td>
                           <?
                       }
                       ?>
                   </tr>
                   <tr class="voucher">
                       <td colspan="9"><hr></td>
                   </tr>
                   <?
               }
               #########################################################################
               # Show line
               ?>
               <tr class="voucher">
                   <td><? print $lineNum ?></td>
                   <td><? print $AccountPlanID ?></td>
                   <td><? print $AccountplanData['name'] ?></td>
                   <td class="number"><? print $_lib['format']->Amount($AccountplanData[$ThisYear]['in']) ?></td>
                   <td class="number"><? print $_lib['format']->Amount($AccountplanData[$ThisYear]['out']) ?></td>
                   <td class="number"><? print $_lib['format']->Amount($AccountplanData[$ThisYear]['saldo']) ?></td>
                   <?
                   if($EnableLastYear)
                   {
                       ?>
                       <td class="number"><? print $_lib['format']->Amount($AccountplanData[$PrevYear]['in']) ?></td>
                       <td class="number"><? print $_lib['format']->Amount($AccountplanData[$PrevYear]['out']) ?></td>
                       <td class="number"><? print $_lib['format']->Amount($AccountplanData[$PrevYear]['saldo']) ?></td>
                       <?
                   }
                   ?>
               </tr>
               <?
               $lineNumOld = $lineNum;
            }
        }
        ?>
        <tr class="voucher">
            <td colspan="9"><hr></td>
        </tr>
        <tr class="voucher">
            <td><b>Delsum <? print $lineNumOld ?></b></td>
            <td colspan="2"><b><? print $linetext->getTextFromLineNr(array('Line' =>$lineNumOld, 'LanguageID' => 'no')) ?></b></td>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$ThisYear]['in'],         'return'=>'value')) ?></td>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$ThisYear]['out'],        'return'=>'value')) ?></td>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$ThisYear]['saldo'],      'return'=>'value')) ?></td>
            <? if($EnableLastYear) { ?>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$PrevYear]['in'],         'return'=>'value')) ?></td>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$PrevYear]['out'],        'return'=>'value')) ?></td>
            <td class="number">&nbsp;<? print $_lib['format']->Amount(array('value'=>$partHash[$lineNumOld][$PrevYear]['saldo'],      'return'=>'value')) ?></td>
            <? } ?>
        </tr>
        <tr class="voucher">
            <td colspan="9"><hr></td>
        </tr>
        <?
    }
    else
    {
        foreach($partHash as $lineNum => $lineData)
        {
            ?>
            <tr class="voucher">
               <td>Delsum <? print $lineNum ?></td>
               <td colspan="2"><? $linetext->getTextFromLineNr(array('Line' =>$lineNum, 'LanguageID' => 'no')) ?></td>
               <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$ThisYear]['in']) ?></td>
               <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$ThisYear]['out']) ?></td>
               <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$ThisYear]['saldo']) ?></td>
               <?
               if($EnableLastYear)
               {
                   ?>
                   <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$PrevYear]['in']) ?></td>
                   <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$PrevYear]['out']) ?></td>
                   <td class="number">&nbsp;<? print $_lib['format']->Amount($lineData[$PrevYear]['saldo']) ?></td>
                   <?
               }
               ?>
           </tr>
            <?
        }
    }
    ?>
    <tr class="voucher">
        <td>Sum</td>
        <td colspan="2"></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$ThisYear]['in']) ?></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$ThisYear]['out']) ?></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$ThisYear]['saldo']) ?></td>
        <? if($EnableLastYear) { ?>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$PrevYear]['in']) ?></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$PrevYear]['out']) ?></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$PrevYear]['saldo']) ?></td>
        <? } ?>
    </tr>
    <tr class="voucher">
        <td colspan="9"><hr></td>
    </tr>
    <tr class="voucher">
        <td>Sum totalt</td>
        <td colspan="4"></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$ThisYear]['saldo']) ?></td>
        <? if($EnableLastYear) { ?>
        <td colspan="2"></td>
        <td class="number"><? print $_lib['format']->Amount($totalHash[$PrevYear]['saldo']) ?></td>
        <? } ?>
    </tr>
    <tr>
      <td colspan="9"><hr></td>
    </tr>
    <tr>
      <th  class="sub" colspan="9">Beregninger</th>
    </tr>
    <?
    foreach($groupHash as $lineNum => $lineData)
    {
        ?>
        <tr class="voucher">
            <td>Linje <? print $lineNum ?></td>
            <td colspan="2"><? print $linetext->getTextFromLineNr(array('Line' =>$lineNum, 'LanguageID' => 'no')) ?></td>
            <td class="number"><? print $_lib['format']->Amount($lineData[$ThisYear]['in']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($lineData[$ThisYear]['out']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($lineData[$ThisYear]['saldo']) ?></td>
            <? if($EnableLastYear) { ?>
            <td class="number"><? print $_lib['format']->Amount($lineData[$PrevYear]['in']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($lineData[$PrevYear]['out']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($lineData[$PrevYear]['saldo']) ?></td>
            <? } ?>
        </tr>
    <? } ?>
</table>
</body>
</html>
