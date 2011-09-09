<?
# $Id: edit.php,v 1.55 2005/10/28 17:59:40 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "mvaavstemming";
$db_table2 = "mvaavstemmingline";
$db_table3 = 'mvaavstemminglinefield';

require_once "record.inc";

includelogic('vat/mvaavstemming');
$avst = new mva_avstemming(array('_sess' => $_sess, '_dbh' => $_dbh, '_dsn' => $_dsn, '_date' => $_date, 'year' => $_REQUEST['Period']));
#print_r($avst);


#print_r($avst->registered);
?>
<? print $_lib['sess']->doctype ?>
<head>
        <title>MVA Avstemming <? print $_lib['sess']->get_companydef('VName') ?> - <? print $avst->year ?></title>
        <meta name="cvs"                content="$Id: edit.php,v 1.55 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>

<body>
<h2>MVA Avstemming <? print $_lib['sess']->get_companydef('VName') ?> - <? print $avst->year ?></h2>

<table width="100%"  class="lodo_data">
	<tr>
		<th colspan="15">MVA Avstemming i f&oslash;lge bokf&oslash;rt regnskap - p&aring; grunnlag av MVA koden</th>
	</tr>
	<tr>
		<td>Mnd/Termin</td>
		<td class="number">Salg utenfor avg. omr.</td>
		<td class="number">Total</td>
		<td class="number">Salg avg.<br /> fritt</td>
		<?
		foreach($avst->_inAccountPlanID as $Vat => $Account)
		{
			?>
			<td class="number">Grl <? print $Vat ?>%</td>
			<td class="number">Utg <? print $Vat ?>%</td>
			<?
		}

		foreach($avst->_inAccountPlanID as $Vat => $Account)
		{
			?>
			<td class="number">Ing <? print $Vat ?>%</td>
			<?
		}
		?>
		<td class="number"><? print $avst->undefinedVatAccountPlanID ?></td>
		<td class="number">MVA</td>
	</tr>
    <tbody>
        <?
		$i = 1;
        foreach($avst->registered as $monthly => $tmp)
        {

            if($monthly != 'total' && $monthly != 'percent')
            {
                if( ($i % 4) == 1 || ($i % 4) == 2) { $class = "r0"; } else { $class = "r1"; };
    	    	$i++;

                $Period = $avst->year."-".sprintf("%02d",$monthly);
                ?>
                <tr class="<? print $class ?>">
                    <td><? print $_lib['format']->MonthToText($monthly) ?></td>
                    <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['NoVatOmsettning'])?></td>

                    <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['TotalOmsettning']) ?></td>
                    <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['FreeOmsettning'])?></td>
                    <?
                    foreach($avst->_inAccountPlanID as $Vat => $Account)
                    {
                        ?>
                        <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['Grunnlag'.$Vat.'Mva']) ?></td>
                        <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['Out'.$Vat.'Mva']) ?></td>
                        <?
                    }

                    foreach($avst->_inAccountPlanID as $Vat => $Account)
                    {
                        ?>
                        <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly]['In'.$Vat.'Mva']) ?></td>
                        <?
                    }
                    ?>
                    <td class="number"><? print $_lib['format']->Amount($avst->registered[$monthly][$avst->undefinedVatAccountPlanID]) ?></td>
                    <td class="number"><b><? print $_lib['format']->Amount($avst->registered[$monthly]['SumMva']) ?></b></td>
                </tr>
                <?
            }
        }
        ?>
        <tr>
            <td class="number">SUM</td>
            <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['NoVatOmsettning'])?></td>
            <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['TotalOmsettning'])?></td>
            <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['FreeOmsettning'])?></td>
            <?
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['Grunnlag'.$Vat.'Mva']) ?></td>
                <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['Out'.$Vat.'Mva']) ?></td>
                <?
            }

            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['In'.$Vat.'Mva']) ?></td>
                <?
            }
            ?>
            <td class="number"><? print $_lib['format']->Amount($avst->registered['total'][$avst->undefinedVatAccountPlanID]) ?></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->registered['total']['SumMva']) ?></b></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <?
            $tdCounter = 0;
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                if(tdCounter > 0)
                {
                    ?><td></td><?
                }
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->registered['total']['Grunnlag'.$Vat.'Mva'] * ($Vat / 100)) ?></td>
                <td></td>
                <?
                $tdCounter++;
            }
            ?>
            <td colspan="4"></td>
        </tr>
        <tr height="20">
            <td></td>
        </tr>

        <tr>
            <th colspan="15">I f&oslash;lge innsendte oppgaver - Det som faktisk er rapportert til staten (Altinn)</th>
        </tr>
        <tr>
            <td class="number">Mnd/Termin</td>
            <td class="number">Salg utenfor avg. omr.</td>
            <td class="number">Total</td>
            <td class="number">Fri</td>
            <?
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number">Grl <? print $Vat ?>%</td>
                <td class="number">Utg <? print $Vat ?>%</td>
                <?
            }
            ?>
            <?
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number">Ing <? print $Vat ?>%</td>
                <?
            }
            ?>
            <td class="number"><? print $avst->undefinedVatAccountPlanID ?></td>
            <td class="number">MVA</td>
        </tr>
    <tbody>
        <form name="budget" action="<? print $_lib['sess']->dispatch."t=mvaavstemming.edit&amp;Period=".$avst->year ?>" method="post">
        <?
        ########################################################################
        # Innsendte oppgaver
        $i = 0;
        foreach($avst->reported as $monthly => $tmp)
        {
            if($monthly != 'total' && $monthly != 'percent')
            {
            	$i++;
                if( ($i % 4) == 1 || ($i % 4) == 2) { $class = "r0"; } else { $class = "r1"; };
                ?>
                <tr class="<? print $class ?>">
                    <td><? print $_lib['format']->MonthToText(array('value'=>$monthly, 'return' => 'value')) ?></td>

                    <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'NoVatOmsettning',  'pk'=>$avst->reported[$monthly]['LineID'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly]['NoVatOmsettning']),  'width'=>'11', 'class'=>'number')); ?></td>

                    <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'TotalOmsettning', 'pk'=>$avst->reported[$monthly]['LineID'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly]['TotalOmsettning']), 'width'=>'11', 'class'=>'number')); ?></td>

                    <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'FreeOmsettning',  'pk'=>$avst->reported[$monthly]['LineID'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly]['FreeOmsettning']),  'width'=>'11', 'class'=>'number')); ?></td>
                    <?
                    foreach($avst->_inAccountPlanID as $Vat => $Account)
                    {
                        ?>
                        <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'Value', 'pk'=>$avst->reported[$monthly]['LineFieldID']['Grunnlag'.$Vat.'Mva'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly]['Grunnlag'.$Vat.'Mva']), 'width'=>'11', 'class'=>'number')); ?></td>
                        <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'Value', 'pk'=>$avst->reported[$monthly]['LineFieldID']['Out'.$Vat.'Mva'],      'value'=>$_lib['format']->Amount($avst->reported[$monthly]['Out'.$Vat.'Mva']),      'width'=>'11', 'class'=>'number')); ?></td>
                        <?
                    }

                    foreach($avst->_inAccountPlanID as $Vat => $Account)
                    {
                        ?>
                        <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'Value', 'pk'=>$avst->reported[$monthly]['LineFieldID']['In'.$Vat.'Mva'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly]['In'.$Vat.'Mva']), 'width'=>'11', 'class'=>'number')); ?></td>
                        <?
                    }
                    ?>
                    <?
                    //print_r($avst->reported[$monthly]['SumMva']);
                    //print $avst->reported[$monthly][$avst->undefinedVatAccountPlanID];
                    ?>
                    <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'Value', 'pk'=>$avst->reported[$monthly]['LineFieldID']['In'.$avst->undefinedVatAccountPlanID.'Mva'], 'value'=>$_lib['format']->Amount($avst->reported[$monthly][$avst->undefinedVatAccountPlanID]), 'width'=>'11', 'class'=>'number', 'disabled' => 1)); ?></td>
                    <td class="number"><b><? print $_lib['format']->Amount(array('value'=>$avst->reported[$monthly]['SumMva'], 'return' => 'value')) ?></b></td>
                </tr>
                <?
            }
        }
        ?>
        <tr>
            <td>SUM</td>
            <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['NoVatOmsettning']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['TotalOmsettning']) ?></td>
            <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['FreeOmsettning']) ?></td>
            <?
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['Grunnlag'.$Vat.'Mva']) ?></td>
                <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['Out'.$Vat.'Mva']) ?></td>
                <?
            }

            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->reported['total']['In'.$Vat.'Mva']) ?></td>
                <?
            }
            ?>
            <td class="number"><? print $_lib['format']->Amount($avst->reported['total'][$avst->undefinedVatAccountPlanID]) ?></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->reported['total']['SumMva']) ?></b></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <?
            $tdCounter = 0;
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                if(tdCounter > 0)
                {
                    ?><td></td><?
                }
                ?>
                <td class="number"><? print $_lib['format']->Amount($avst->reported['percent']['Grunnlag'.$Vat.'Mva']) ?></td>
                <td></td>
                <?
                $tdCounter++;
            }
            ?>
            <td colspan="4"></td>
        </tr>
        <tr height="20">
            <td></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="number"><b>Diff</b></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->diff['NoVatOmsettning']) ?></b></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->diff['TotalOmsettning']) ?></b></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->diff['FreeOmsettning']) ?></b></td>
            <?
            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><b><? print $_lib['format']->Amount($avst->diff['Grunnlag'.$Vat.'Mva']) ?></b></td>
                <td class="number"><b><? print $_lib['format']->Amount($avst->diff['Out'.$Vat.'Mva']) ?></b></td>
                <?
            }

            foreach($avst->_inAccountPlanID as $Vat => $Account)
            {
                ?>
                <td class="number"><b><? print $_lib['format']->Amount($avst->diff['In'.$Vat.'Mva']) ?></b></td>
                <?
            }
            ?>
            <td class="number"><b><? print $_lib['format']->Amount($avst->diff[$avst->undefinedVatAccountPlanID]) ?></b></td>
            <td class="number"><b><? print $_lib['format']->Amount($avst->diff['SumMva']) ?></b></td>
       	</tr>
        <tr height="5">
            <td></td>
        </tr>
    </tfoot>
</table>
<br/><br/>
<table class="lodo_data">
<tr>
    <th colspan="8">De bel&oslash;pene som faktisk er satt av p&aring; konto</th>
</tr>
<tr>
    <td colspan="8">Eksempel: Bel&oslash;pet p&aring; utg 25% i f&oslash;lge bokf&oslash;rt regnskap skal v&aelig;re samme tall som p&aring; konto 2701 kode 11 - 25% i listen her</td>
</tr>
<tr><td><? print $row->AccountPlanID ?></td></tr>
    <tbody>
        <?
        $totalSum = 0;

        $query = "select v.*, a.AccountName from vat as v, accountplan as a where v.VatID < 40 and v.Percent>=0 and v.AccountPlanID=a.AccountPlanID group by v.AccountPlanID order by v.VatID";
        #print "$query<br />";
        $result = $_lib['db']->db_query($query);
        while($row = $_lib['db']->db_fetch_object($result))
        {
            $query = "select sum(AmountOut) as sumOut, sum(AmountIn) as sumin from voucher where Active=1 and AccountPlanID='".$row->AccountPlanID."' and substring(VoucherPeriod,1,4)='".$avst->year."'";
            $sumRow = $_lib['storage']->get_row(array('query' => $query));
            $totalSum += ($sumRow->sumin - $sumRow->sumOut);
# print "<br />\$totalSum += " . ($sumRow->sumin - $sumRow->sumOut);
            if($row->AccountPlanID)
            {
                ?>
                <tr>
                    <td><? print  $row->AccountPlanID . ' - ' . $row->AccountName . " Utg. MVA kode $row->VatID ($row->Percent%)" ?></td>
                    <td class="number"><? print $_lib['format']->Amount(($sumRow->sumin - $sumRow->sumOut)) ?></td>
                </tr>
                <?
            }
            $_lib['sess']->debug($query);
        }
        $query = "select  v.*, a.AccountName from vat as v, accountplan as a where v.VatID>=40 and v.Percent>=0 and v.AccountPlanID=a.AccountPlanID group by v.AccountPlanID order by v.VatID";
        $result = $_lib['db']->db_query($query);
        while($row = $_lib['db']->db_fetch_object($result))
        {
            $query = "select sum(AmountOut) as sumOut, sum(AmountIn) as sumin from voucher where Active=1 and AccountPlanID='".$row->AccountPlanID."' and substring(VoucherPeriod,1,4)='".$avst->year."'";
            #print "<tr><td>xx $query<br /></td></tr>";
            $sumRow = $_lib['storage']->get_row(array('query' => $query));
            $totalSum += ($sumRow->sumin - $sumRow->sumOut);
# print "<br />\$totalSum += " . ($sumRow->sumin - $sumRow->sumOut);
            if($row->AccountPlanID > 0)
            {
                ?>
                <tr>
                    <td><? print  $row->AccountPlanID . ' - ' . $row->AccountName . " inng. MVA kode $row->VatID ($row->Percent%)" ?></td>
                    <td class="number"><? print $_lib['format']->Amount(($sumRow->sumin - $sumRow->sumOut)) ?></td>
                </tr>
                <?
            }
        }
        ?>
        <tr>
            <?   ############################## SJEKK DENN SP�RRING
            $query = "select sum(AmountOut) as sumOut, sum(AmountIn) as sumin from voucher where Active=1 and AccountPlanID='".$_lib['sess']->get_companydef('AccountVat')."' and substring(VoucherPeriod,1,4) <='".$avst->year."'";
            //print $query;
            $sumRow = $_lib['storage']->get_row(array('query' => $query));
            $totalSum += ($sumRow->sumin - $sumRow->sumOut);
# print "<br />\$totalSum += " . ($sumRow->sumin - $sumRow->sumOut);
            ?>
            <td><? print $_lib['sess']->get_companydef('AccountVat') ?> Oppgj&oslash;r MVA</td>
            <td class="number"><? print $_lib['format']->Amount(($sumRow->sumin - $sumRow->sumOut)) ?></td>
        </tr>
        <tr>
            <td><b>Sum</b></td>
            <td class="number"><b><? print $_lib['format']->Amount($totalSum) ?></b></td>
        </tr>
</table>
<br/><br/>
<table class="lodo_data">
    <tbody>
        <tr>
            <th colspan="8">Avstemmer at det du har sagt du skal betale matcher med det du har betalt.</td>
        </tr>
        <tr>
            <td><? print ($avst->year - 1) ?> Differanse</td>
            <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'LastYearDiff', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->LastYearDiff), 'width'=>'12', 'class'=>'number')) ?></td>
			<td>Positiv betalt for mye</td>
        </tr>
        <tr>
            <td><? print $avst->year ?> Differanse</td>
            <td class="number"><? print $_lib['format']->Amount($avst->diff['SumMva']) ?></td>
			<td>Positiv betalt for mye</td>
        </tr>
        <tr>
            <td>Sum</td>
            <td class="number"><? $sumDiff = $avst->DiffReportedMva; print $_lib['format']->Amount($sumDiff); ?></td>
            <td></td>
            <td>Betalt</td>
            <td>Bilagnr</td>
            <td>Dato</td>
            <td>Beskrivelse</td>
        </tr>
        <tr>
            <td><? print ($avst->year - 1) ?> - Gjeld</td>
            <td class="number"><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'LastYearMva', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount(array('value'=> $avst->avstemmingRow->LastYearMva, 'return' => 'value')), 'width'=>'12', 'class'=>'number')) ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'LastYearPayed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount(array('value'=> $avst->avstemmingRow->LastYearPayed, 'return' => 'value')), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'LastYearPayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->LastYearPayedJournalID, 'width'=>'12', 'class'=>'number')) ?></td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'LastYearPayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date(array('value' => $avst->avstemmingRow->LastYearPayedDate, 'return' => 'value')), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
            <td rowspan="5"><? print $_lib['form3']->textarea(array('table'=>$db_table, 'field'=>'LastYearPayedDescription', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->LastYearPayedDescription, 'width'=> 40, 'height' => 5, 'class'=>'number')) ?></td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-01/02 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? print $_lib['format']->Amount($avst->ReportedPeriod1 + $avst->avstemmingRow->Period1Payed) ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period1Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->Period1Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period1PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period1PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period1PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period1PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-03/04 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? print $_lib['format']->Amount(array('value'=> $avst->ReportedPeriod2 + $avst->avstemmingRow->Period2Payed, 'return' => 'value')); ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period2Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->Period2Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period2PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period2PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period2PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period2PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-05/06 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? $hash = $_lib['format']->Amount(array('value'=> $avst->ReportedPeriod3 + $avst->avstemmingRow->Period3Payed)); print $hash['value']; ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period3Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->Period3Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period3PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period3PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period3PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period3PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-07/08 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? $hash = $_lib['format']->Amount(array('value'=>$avst->ReportedPeriod4 + $avst->avstemmingRow->Period4Payed)); print $hash['value']; ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period4Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$_lib['format']->Amount($avst->avstemmingRow->Period4Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period4PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period4PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period4PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period4PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-09/10 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? $hash = $_lib['format']->Amount(array('value'=> $avst->ReportedPeriod5 + $avst->avstemmingRow->Period5Payed)); print $hash['value']; ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period5Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->Period5Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period5PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period5PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period5PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period5PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><? print ($avst->year) ?>-11/12 (sum kto 2740 rapportert + betalt)</td>
            <td class="number"><? $hash = $_lib['format']->Amount(array('value'=>$avst->ReportedPeriod6 + $avst->avstemmingRow->Period6Payed)); print $hash['value']; ?></td>
            <td></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period6Payed', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Amount($avst->avstemmingRow->Period6Payed), 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Period6PayedJournalID', 'pk'=>$avst->MvaAvstemmingID, 'value'=>$avst->avstemmingRow->Period6PayedJournalID, 'width'=>'12', 'class'=>'number')) ?> </td>
            <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'Period6PayedDate', 'pk'=>$avst->MvaAvstemmingID, 'value'=> $_lib['format']->Date($avst->avstemmingRow->Period6PayedDate), 'width'=>'20', 'class'=>'number', 'form_name' => 'budget')) ?> </td>
        </tr>
        <tr>
            <td><b>Sum</b></td>
            <td class="number">
            <b><? print $_lib['format']->Amount(array('value' => $avst->between, 'return' => 'value')) ?></b>
            </td>
        </tr>
        <tr>
            <td><b>Hovedbok (sum MVA p&aring; MVA kontoer 2700 til 2740)</b></td>
            <td class="number"><b><? $hash = $_lib['format']->Amount(array('value'=>$totalSum)); print $hash['value']; ?></b></td>
        </tr>
        <tr>
            <td><b>Differanse</b></td>
            <td class="number"><b><? $hash = $_lib['format']->Amount(array('value'=> $avst->between - $totalSum)); print $hash['value']; ?></b></td>
            <td>Positiv betalt for mye</td>
        </tr>
</table>
<table>
    <tr>
        <td width="500" class="number"><? print $_lib['form3']->submit(array('name'=>'action_avstemming_update', 'value'=>'Lagre siden (S)', 'accesskey'=>'S')) ?>   <? global $_sess; if($_lib['sess']->get_person('AccessLevel') >= 3) { ?> <a href="<? print $_lib['sess']->dispatch."t=mvaavstemming.fix_mva_avst"; ?>">oppdater MVA Kode</a>   <? } ?> </td>
    </tr>
</table>
</form>
</body>
</html>
