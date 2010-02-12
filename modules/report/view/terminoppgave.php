<?
# $Id: terminoppgave.php,v 1.22 2005/09/09 06:51:08 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$FromPeriod = $_REQUEST['report_FromPeriod'];
$ToPeriod   = $_REQUEST['report_ToPeriod'];

includelogic('terminoppgave/terminoppgave');
$termin = new logic_terminoppgave_terminoppgave($_REQUEST);

print $_lib['sess']->doctype;
?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Terminoppgave</title>
    <meta name="cvs"                content="$Id: terminoppgave.php,v 1.22 2005/09/09 06:51:08 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>
<table>
    <thead>
        <tr><th>Firma: </th><th><? print $_lib['sess']->get_companydef('CompanyName') ?></th>
        <tr><th>Adresse: </th><th><? print $_lib['sess']->get_companydef('VAddress') ?></th></tr>
        <tr><th>Post: </th><th><? print $_lib['sess']->get_companydef('VZipCode') ?> <? print $_lib['sess']->get_companydef('VCity') ?></th></tr>
        <tr><th>Orgnummer: </th><th><? print $_lib['sess']->get_companydef('OrgNumber') ?></th></tr>
        <tr><th>Periode: </th><th><? print "$termin->FromPeriod - $termin->ToPeriod" ?></th></tr>
    </thead>
</table>
<br><br>

<table>
<?
foreach($termin->SalaryLineH as $ZoneID => $Zone) {
    ?>
    <tr>
        <th colspan="3"><h2>Sone: <? print $ZoneID ?></h2></th>
    </tr>
    <?
    foreach($Zone as $KommuneID => $Kommune) {
        #print "Kommune: $KommuneID<br>\n";
        #print_r($Kommune);
        ?>
        <tr>
            <th colspan="3">Kommune: <? print $termin->KommuneIDToNumber($KommuneID) ?> - <? print $termin->KommuneIDToName($KommuneID) ?></th>
        </tr>
        <?
        foreach($Kommune as $AccountPlanID => $AccountPlan) {
            ?>
            <tr>
                <th colspan="3"><b><? print $AccountPlanID ?> - <? print $termin->AccountPlanIDToName($AccountPlanID) ?></b></th>
            </tr>
            <?
            foreach($AccountPlan as $JournalID => $Journal ) { 
                ?>
                <tr>
                    <th colspan="3" class="menu">Bilag: <? print $JournalID ?> - Periode: <? print $termin->JournalIDToName($JournalID) ?></th>
                </tr>
                <?
               foreach($Journal as $LineID => $Amount) { 
                    ?>
                    <tr>
                    <td class="number"><? print $LineID ?></td>
                    <td><? print $termin->LineIDToName($LineID) ?></td>
                    <td class="number"><? print $_lib['format']->Amount($Amount) ?></td>
                    </tr> 
            <? }
            } ?>
            <tr>
                <th colspan="3"><b>Sum <? print $AccountPlanID ?> - <? print $termin->AccountPlanIDToName($AccountPlanID) ?></b></th>
            </tr>
            <? foreach($termin->AccountPlanH[$ZoneID][$KommuneID][$AccountPlanID] as $LineID => $Amount) { ?>
            <tr>
                <td class="number"><b><? print $LineID ?></b></td>
                <td><b><? print $termin->LineIDToName($LineID) ?></b></td>
                <td class="number"><b><? print $_lib['format']->Amount($Amount) ?></b></td>
            </tr> 
        <? } ?>
        <tr>
            <td colspan="2" class="menu"><b>Arbeidsgiveravgift ansatt <? print $AccountPlanID ?> - <? print $termin->AccountPlanIDToName($AccountPlanID) ?></b></td>
            <td class="menu number"><b><? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftAccountPlanH[$ZoneID][$KommuneID][$AccountPlanID]->Amount) ?> x <? print $termin->ArbeidsgiveravgiftAccountPlanH[$ZoneID][$KommuneID][$AccountPlanID]->Percent ?>% = <? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftAccountPlanH[$ZoneID][$KommuneID][$AccountPlanID]->Avgift) ?></b></td>
        </tr>
        <? } ?>
        <tr>
            <th colspan="3">Sum kommune: <? print $termin->KommuneIDToNumber($KommuneID) ?> - <? print $termin->KommuneIDToName($KommuneID) ?></th>
        </tr>
        <? foreach($termin->KommuneH[$ZoneID][$KommuneID] as $LineID => $Amount) { ?>
        <tr>
            <td class="number"><b><? print $LineID ?></b></td>
            <td><b><? print $termin->LineIDToName($LineID) ?></b></td>
            <td class="number"><b><? print $_lib['format']->Amount($Amount) ?></b></td>
        </tr> 
        <? } ?>
        <tr>
            <td colspan="2" class="menu"><b>Arbeidsgiveravgift kommune: <? print $termin->KommuneIDToNumber($KommuneID) ?> - <? print $termin->KommuneIDToName($KommuneID) ?></b></td>
            <td class="menu number"><b><? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftKommuneH[$ZoneID][$KommuneID]->Amount) ?> x <? print $termin->ArbeidsgiveravgiftKommuneH[$ZoneID][$KommuneID]->Percent ?>% = <? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftKommuneH[$ZoneID][$KommuneID]->Avgift) ?></b></td>
        </tr>
    <? } ?>
    <tr>
        <th colspan="3">Sum sone: <? print $ZoneID ?></th>
    </tr>
    <? foreach($termin->SoneH[$ZoneID] as $LineID => $Amount) { ?>
    <tr>
        <td class="number"><b><? print $LineID ?></b></td>
        <td><b><? print $termin->LineIDToName($LineID) ?></b></td>
        <td class="number"><b><? print $_lib['format']->Amount($Amount) ?></b></td>
    </tr>
    <? } ?>
    <tr>
        <td colspan="2" class="menu"><b>Arbeidsgiveravgift sone: <? print $ZoneID ?></b></td>
        <td class="menu number"><b><? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftSoneH[$ZoneID]->Amount) ?> x <? print $termin->ArbeidsgiveravgiftSoneH[$ZoneID]->Percent ?>% = <? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftSoneH[$ZoneID]->Avgift) ?></b></td>
    </tr>
<? } ?>
<tr>
    <th colspan="3">Sum totalt:</th>
</tr>
<? foreach($termin->SumH as $LineID => $Amount) { ?>
<tr>
    <td class="number"><b><? print $LineID ?></b></td>
    <td><b><? print $termin->LineIDToName($LineID) ?></b></td>
    <td class="number"><b><? print $_lib['format']->Amount($Amount) ?></b></td>
</tr> 
<? } ?>
<tr>
    <td colspan="2" class="menu"><b>Arbeidsgiveravgift totalt: </b></td>
    <td class="menu number"><b><? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftSumO->Amount) ?> = <? print $_lib['format']->Amount($termin->ArbeidsgiveravgiftSumO->Avgift) ?></b></td>
</tr>
</table>
</body>
</html>