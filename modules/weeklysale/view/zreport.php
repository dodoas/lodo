<?
/* $Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$WeeklySaleID       = $_lib['input']->getProperty('WeeklySaleID');
$WeeklySaleConfID   = $_lib['input']->getProperty('WeeklySaleConfID');
$WeeklySaleDayID    = $_lib['input']->getProperty('WeeklySaleDayID');

includelogic('accounting/accounting');
includelogic('weeklysale/weeklysale');

$weeklysale = new weeklysale($WeeklySaleID, $WeeklySaleConfID);
$weeklysale->presentation();
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body>

    <? includeinc('top') ?>
    <? includeinc('left') ?>

    <?if($message) { ?> <div class="warning"><? print $message ?></div><br><? } ?>

        <?        
        $salenameH      = array_keys($weeklysale->salehead['groups']);
        $revenuenameH   = array_keys($weeklysale->revenuehead['groups']);
 
        #print_r($weeklysale);
 
        foreach($weeklysale->sale as $tmp => $line) {

            if($line->WeeklySaleDayID != $WeeklySaleDayID) { continue; }
            ?>
           <fieldset>
           <legend>Z-oppgj&oslash;r: <? print $line->Znr ?></legend>
           <table>
            <?
            $ParentWeeklySaleDayID = $line->WeeklySaleDayID;
            $ZnrTotalAmount = $line->ZnrTotalAmount;
            ?>
           <tr><td class="menu">Navn</td><td><? print $_lib['sess']->get_companydef('VName') ?></td></tr>
           <tr><td class="menu">Org nr</td><td><? print $_lib['sess']->get_companydef('OrgNumber') ?></td></tr>
           <tr><td class="menu">Oppgj&oslash;r Z- foretatt </td><td><? print $line->Datetime ?></td></tr>
           <tr><td class="menu">Dag</td><td><? print $weeklysale->head->Period ?>-<? print $line->Day ?></td></tr>
           <tr><td class="menu">Dag</td><td><? print $line->WeekDayName ?></td></tr>
 
            <tr><th colspan="2">Zrapport</th></tr>
           <tr><td class="menu">Z nr</td><td class="number"><? print $line->Znr ?></td></tr>
           <tr><td class="menu">Z nr total</td><td class="number"><? print $_lib['format']->Amount($line->ZnrTotalAmount) ?></td></tr>
           
           <tr><th colspan="2">Salg</th></tr>
            <? foreach($weeklysale->salehead['groups'] as $name => $i) { 
                $name = array_shift($salenameH);
            ?>
            <tr>
                <td class="menu"><? print $name ?></td>
                <td class="number"><? print $_lib['format']->Amount($line->{"Group{$i}Amount"}) ?></td>
            </tr>
            <? } ?>
             <tr>
            <td class="menu">Sum</td><td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID]) ?></nobr></td>
            </tr>
        <? } ?>
        <tr><th colspan="2">Likvider</th></tr>
        <?
        foreach($weeklysale->revenue as $tmp => $line) {
            if($line->ParentWeeklySaleDayID != $ParentWeeklySaleDayID) { continue; }

            foreach($weeklysale->revenuehead['groups'] as $name => $i) { 
                $name = array_shift($revenuenameH);
                $sumrevenue += $line->{"Group{$i}Amount"};
            ?>
            <tr>
                <td class="menu"><? print $name ?></td>
                <td class="number"><? print $_lib['format']->Amount($line->{"Group{$i}Amount"}) ?></td>
            </tr>
            <? } 
            $sumrevenue += $weeklysale->revenuehead['sumcash'][$line->ParentWeeklySaleDayID];
            $diffznrtotal = $ZnrTotalAmount - $sumrevenue;
            $diffsum      = $sumrevenue - $weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID];
            ?>
            <td class="menu">Kontant</td><td class="number"><? print $_lib['format']->Amount($weeklysale->revenuehead['sumcash'][$line->ParentWeeklySaleDayID]) ?></td></tr>
            <tr><td class="menu">Sum</td><td class="number"><? print $_lib['format']->Amount($sumrevenue) ?></td></tr>
        <? } ?>            
            <tr><th colspan="2">Differanse</th></tr>
            <tr><td class="menu">Sum likvider vs Znrtotal</td><td class="number"><? print $_lib['format']->Amount($diffznrtotal) ?></td></tr>
            <tr><td class="menu">Sum likvider vs salg</td><td class="number"><? print $_lib['format']->Amount($diffsum) ?></td></tr>

    </table>
    </fieldset>
    <? includeinc('bottom') ?>
</body>
</html>
