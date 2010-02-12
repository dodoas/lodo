<?
/* $Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

//gj�re en sjekk om denne allerede er satt??
$WeeklySaleID       = (int) $_REQUEST['WeeklySaleID'];
$WeeklySaleConfID   = (int) $_REQUEST['WeeklySaleConfID'];

includelogic('accounting/accounting');
includelogic('weeklysale/weeklysale');
$accounting = new accounting();
require_once "record.inc";

$weeklysale = new weeklysale($WeeklySaleID, $WeeklySaleConfID);
$weeklysale->presentation();

#print_r($weeklysale->salehead['enablequantity']);

$readonly = "";
if($weeklysale->head->Period and !$accounting->is_valid_accountperiod($weeklysale->head->Period, $_lib['sess']->get_person('AccessLevel'))) {
  $_lib['message']->add(array('message' => "Perioden er lukket, du kan ikke endre data"));
  $readonly = "readonly disable";
}

$formname = "Update";
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Ukeomsetning</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body>

    <? includeinc('top') ?>
    <? includeinc('left') ?>

    <?if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

    <form name="<? print $formname ?>" action="<? print $MY_SELF ?>" method="post">
        <input type="hidden" name="WeeklySaleID" value="<? print $WeeklySaleID ?>">
        <?
        //print "$message";
        ?>
        <table class="lodo_data">
            <thead>
            <tr>
                <th colspan="13">Ukeomsetning: <? print $WeeklySaleID ?></th>
            </tr>
            <tr class="voucher">
                <th colspan="2">Bilagsnr</th>
                <th colspan="2">Bilagsdato</th>
                <th>Periode</th>
                <th colspan="2">Avdeling</th>
                <th>Mal</th>
                <th colspan="3"><? print $weeklysale->head->CompanyName ?></th>
                <th colspan="2"></th>
            </tr>
            <tr class="voucher">
                <td colspan="2"><nobr><? if ($weeklysale->head->JournalID) { ?>
                	<a href="<? print $_SETUP['DISPATCH']."t=journal.edit&amp;&voucher_VoucherType=".$weeklysale->head->VoucherType."&amp;voucher_JournalID=" . $weeklysale->head->JournalID . "&amp;action_journalid_search=1"; ?>"><? print $weeklysale->head->VoucherType ?> </a>
               	<? } ?>
               	<?
                #To let the user change vouchertype at this point would make a lot of probems with updates, series and hovedbok A posteringer.
                #$_lib['form2']->Type_menu2(array('table' => 'weeklysale', 'field' => 'VoucherType', 'type' => 'VoucherType', 'value'  => $weeklysale->head->VoucherType, 'pk' => $weeklysale->head->WeeklySaleID));
                print $_lib['form3']->text(array('table'=>'weeklysale',   'field' => 'JournalID'  , 'pk' => $weeklysale->head->WeeklySaleID, 'value' => $weeklysale->head->JournalID, 'width' => 10, 'tabindex' => 1)) ?>
                <? print $_lib['form3']->hidden(array('name'=>'VoucherPeriodOld', 'value'=>$weeklysale->head->Period)) ?>
                </td>
                <td colspan="2"><? print $_lib['form3']->text(array('table'=>'weeklysale', 'field'=>'JournalDate', 'pk'=>$weeklysale->head->WeeklySaleID, 'value'=>$weeklysale->head->JournalDate, 'width'=>'10', 'tabindex'=>'2', 'OnChange'=>"update_period(this, '".$formname."', 'weeklysale.JournalDate.".$weeklysale->head->WeeklySaleID."', 'weeklysale.Period.".$weeklysale->head->WeeklySaleID."');")) ?></td>
                <td align="center"><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'weeklysale', 'field' => 'Period', 'pk'=>$weeklysale->head->WeeklySaleID, 'value' => $weeklysale->head->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'pk' => $weeklysale->head->WeeklySaleID, 'tabindex'=>'3', 'required'=>'1')) ?></td>
                <td colspan="2">
                    <nobr>
                        <?
                        print $weeklysale->head->DepartmentName;
                        print $_lib['form3']->hidden(array('name'=>'weeklysale_DepartmentID_'.$weeklysale->head->WeeklySaleID, 'value'=>$weeklysale->head->DepartmentID));
                        ?>
                    </nobr>
                </td>
                <td><? print $weeklysale->head->Name ?></td>
                <td colspan="3"><? print $weeklysale->head->CompanyAddress ?></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td colspan="3"></td>
                <td colspan="3"></td>
                <td colspan="3"><? print $weeklysale->head->CompanyZipCode ?> <? print $weeklysale->head->CompanyCity ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="1">Uke</td>
                <td colspan="2"><input type="text" <? print $readonly ?> name="weeklysale.Week.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->Week ?>" size="3" class="number" tabindex="4">
                <td colspan="1">&Aring;r</td>
                <td colspan="1"><input type="text" <? print $readonly ?> name="weeklysale.Year.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->Year ?>" size="5" class="number" tabindex="4">
                <td colspan="1">Fast kasse</td>
                <td colspan="1"><? print $_lib['format']->Amount($weeklysale->head->PermanentCash) ?></td>
                <td colspan="3">Tlf: <? print $weeklysale->head->CompanyPhone ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td colspan="3"></td>
                <td colspan="3"></td>
                <td colspan="3">Mob: <? print $weeklysale->head->CompanyMobile ?></td>
                <td colspan="3"></td>
            </tr>
        <tbody>
            <tr>
                <td class="menu"></td>
                <td class="menu">Dag</td>
                <td class="menu">Z nr</td>
                <td class="menu">Z nr total</td>
                <? foreach($weeklysale->salehead['groups'] as $name => $id) { ?>
                    <td class="menu"><? print $name ?></td>
                <? } ?>
                <td class="menuright">Total</td>
                <td class="menu" colspan="2">Sig</td>
                <td class="menu"></td>
                <?/*<td class="menu">L&aring;s*/?>
            <?
            foreach($weeklysale->sale as $WeeklySaleDayID => $line) {
 
                #print_r($line);
 
                if(round($line->ZnrTotalAmount,2) != round($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID],2)) {
                    $classznramount     = 'number red';
                    $titleznramount     = 'Znr total stemmer ikke med summen av gruppene';
                } else {
                    $classznramount     = 'number';
                    $titleznramount     = 'Fyll inn Znr total fra kassaapparat';                
                }
                
                if($znrwrong[$line->ParentWeeklySaleDayID]) {
                    $classznr = 'number red';
                    $titleznr = 'Dette Znummeret er enten brukt fra f&oslash;r eller ikke utfylt';
                } else {
                    $classznr = 'number';
                    $titleznr = 'Fyll inn Znr fra kassaapparat';
                }

                if($line->Locked == 1 and $readonly == "")
                {
                    $readonly = "disabled";
                }

                ?>
                <tr>
                <td class="menu"><? print $line->WeekDayName ?></td>
                <td class="number"><input type="text" <? print $readonly ?> name="weeklysaleday.Day.<? print $line->WeeklySaleDayID ?>" value="<? print $line->Day ?>" size="2" class="number" tabindex="<? print (5 + $counter) ?>"></td>
                <td class="number"><nobr><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.zreport&WeeklySaleID=<? print $WeeklySaleID ?>&WeeklySaleDayID=<? print $line->WeeklySaleDayID?>">Z</a><input type="text" <? print $readonly ?> name="weeklysaleday.Znr.<? print $line->WeeklySaleDayID ?>" value="<? print $line->Znr ?>" size="4" class="<? print $classznr ?>"  title="<? print $titleznr ?>" tabindex="<? print (6 + $counter) ?>"></nobr></td>
                <td class="number"><input type="text" <? print $readonly ?> name="weeklysaleday.ZnrTotalAmount.<? print $line->WeeklySaleDayID ?>" value="<? print $_lib['format']->Amount($line->ZnrTotalAmount) ?>" size="7" class="<? print $classznramount ?>" title="<? print $titleznramount ?>" tabindex="<? print (7 + $counter) ?>">

                <? foreach($weeklysale->salehead['groups'] as $name => $i) { ?>
                <td class="number"><nobr><input type="text" title="Bel&oslash;p" <? print $readonly ?> name="weeklysaleday.Group<? print $i ?>Amount.<? print $line->WeeklySaleDayID ?>"   value="<? print $_lib['format']->Amount($line->{"Group{$i}Amount"}) ?>"  size="7" class="number" tabindex="<? print (8 + $counter) ?>">
                <? if($weeklysale->salehead['enablequantity'][$i] == 1) { ?><input type="text" title="Antall"<? print $readonly ?> name="weeklysaleday.Group<? print $i ?>Quantity.<? print $line->WeeklySaleDayID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->{"Group{$i}Quantity"}, 'return'=>'value')) ?>"  size="7" class="number"><? } ?></nobr></td>
                <? } ?>

                <td class="menuright"><nobr><? print $_lib['format']->Amount($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID]) ?></nobr></td>
                <td colspan="2"><? print $line->Person ?></td>
            	<td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $WeeklySaleID ?>&action_weeklysale_daynew=1&amp;DayID=<? print $line->DayID ?>&amp;Day=<? print $line->Day ?>">Ny linje</a></td>
                <?
                $counter += 43;

                if($line->Locked == 1 and $readonly == "disabled")
                {
                    $readonly = "";
                }
            }
            ?>
            <tr>
                <td class="menu">Sum</td>
                <td class="number"></td>
                <td class="number"></td>
                <td class="number"><nobr><? if($sumtot != $total) { print "<font color=\"red\">"; } print $_lib['format']->Amount($weeklysale->head->saleznrtotal); if($weeklysale->head->saleznrtotal != $weeklysale->head->saletotal) { print "</font>"; }?></nobr></td>
                <? foreach($weeklysale->salehead['groups'] as $name => $i) { ?>
                <td class="number"><? print $_lib['format']->Amount($weeklysale->salehead['sumgroup'][$i]); if($weeklysale->salehead['sumquantity'][$i] > 0) { print " - "; print $_lib['format']->Amount($weeklysale->salehead['sumquantity'][$i]); } ?></nobr></td>
                <? } ?>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->saletotal) ?></nobr></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="3">Inntektskonto</td>
                <td></td>
                
                <? foreach($weeklysale->salehead['account'] as $i => $name) { ?>
                <td align="left"><nobr><? print $name ?></nobr></td>
                <? } ?>
                <td></td>
                <td colspan="2"></td>
            </tr>
            <? if(is_array($weeklysale->salehead['department'])) { ?>
            <tr>
                <td colspan="3">Avdelinger</td>
                <td></td>
                
                <? foreach($weeklysale->salehead['department'] as $i => $name) { ?>
                <td align="left"><nobr><? print $name ?></nobr></td>
                <? } ?>
                
                <td></td>
                <td colspan="2"></td>
            </tr>
            <? } ?>
            <? if(is_array($weeklysale->salehead['project'])) { ?>
            <tr>
                <td colspan="3">Prosjekter</td>
                <td></td>
                
                <? foreach($weeklysale->salehead['project'] as $i => $name) { ?>
                <td align="left"><nobr><? print $name ?></nobr></td>
                <? } ?>
                
                <td></td>
                <td colspan="2"></td>
            </tr>
            <? } ?>
            <tr>
                <td colspan="12">&nbsp;</td>
            </tr>
            <tr>
                <td class="menu"></td>
                <td class="menu">Dag</td>
                <td class="menu">Znr</td>
                 <? 
                if(is_array($weeklysale->revenuehead['groups'])) {
                    foreach($weeklysale->revenuehead['groups'] as $name => $id) { ?>
                        <td class="menu"><? print $name ?></td>
                    <? } 
                } ?>
                <td class="menu">Kontant</td>
                <td class="menu">Sum</td>
                <td class="menu">Kontant inn</td>
                <td class="menu">Kontant ut</td>
                <td class="menu">Opptelling</td>
                <td class="menu">Diff</td>
                <td class="menu">Forklaring</td>
                <td class="menu">Sig</td>
                <td class="menu">L&aring;s</td>
            <?
            $sumtot = 0;
            $sum = array();
            $counter=0;
            
            if(is_array($weeklysale->revenue)) {
                foreach($weeklysale->revenue as $WeeklySaleDayID => $line) {
    
                    if($line->Locked == 1 and $readonly == "")
                    {
                        $readonly = "disabled";
                    }
    
                    ?>
                    <tr>
                        <td class="menu"><? print $line->WeekDayName ?></td>
                        <td><input type="hidden" name="weeklysaleday.Day.<? print $line->WeeklySaleDayID ?>" value="<? print $line->Day ?>" ><? print $line->Day ?></td>
                        <td class="number"><? print $line->Znr ?></td>
                        <? if(is_array($weeklysale->revenuehead['groups'])) {
				        foreach($weeklysale->revenuehead['groups'] as $name => $i) { ?>
                        <td class="number"><input <? print $readonly ?> type="text" name="weeklysaleday.Group<? print $i ?>Amount.<? print $line->WeeklySaleDayID ?>"      value="<? print $_lib['format']->Amount($line->{"Group{$i}Amount"}) ?>"  size="7" class="number" tabindex="<? print (28 + $counter) ?>">
                        <? } 
			            } ?>
    
                        <!-- Kontant-->
                        <td class="number"><input type="hidden" name="weeklysaleday.Group18Amount.<? print $line->WeeklySaleDayID ?>" value="<? print $weeklysale->head->sum18 ?>"><nobr><? print $_lib['format']->Amount($weeklysale->revenuehead['sumcash'][$line->ParentWeeklySaleDayID]) ?></nobr>
                        <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID]) ?></nobr>
                        <td class="number"><!-- kontant inn --><input type="text" <? print $readonly ?> name="weeklysaleday.Group19Amount.<? print $line->WeeklySaleDayID ?>"   value="<? $hash = $_lib['format']->Amount(array('value'=>$line->Group19Amount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print (46 + $counter) ?>">
                        <td class="number"><!-- kontant ut --><input type="text" <? print $readonly ?> name="weeklysaleday.Group20Amount.<? print $line->WeeklySaleDayID ?>"   value="<? $hash = $_lib['format']->Amount(array('value'=>$line->Group20Amount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print (47 + $counter) ?>">
                        <td class="number"><!-- opptelling --><input <? print $readonly ?> type="text" name="weeklysaleday.ActuallyCashAmount.<? print $line->WeeklySaleDayID ?>" value="<? $hash = $_lib['format']->Amount(array('value'=>$line->ActuallyCashAmount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print (45 + $counter) ?>">
                        <td class="number"><!-- diff --><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { print "<font color=\"red\">"; } ?><? print $_lib['format']->Amount($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID]) ?><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { print "</font>"; } ?></td>
                        <td><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { ?><input <? print $readonly ?> type="text" name="weeklysaleday.CashAmountExplanation.<? print $line->WeeklySaleDayID ?>" value="<? print $line->CashAmountExplanation ?>" size="20" tabindex="306"><? } ?>
                        <td><? print $line->Person ?><? //$_lib['form2']->CompanyContactMenu( array('table' => 'weeklysaleday', 'field' => 'PersonID', 'value' => $line->PersonID, 'pk' => $line->WeeklySaleDayID, 'disabled'=>$line->Locked)); ?>
                        <td><? if($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID] > 0) { print $_lib['form3']->checkbox(array('name'=>"weeklysaleday.Locked.".$line->WeeklySaleDayID, 'value'=>$line->Locked, 'disabled'=>($_lib['sess']->get_person('AccessLevel') >= 3)?'0':$line->Locked)); } ; ?>
                    </tr>
                    <?
                    $counter += 43;
    
                    if($line->Locked == 1 and $readonly == "disabled")
                    {
                        $readonly = "";
                    }
                }
            }
            ?>
            <tr>
                <td class="menu">Sum</td>
                <td class="number"></td>
                <td class="number"></td>
                
                <? if(is_array($weeklysale->revenuehead['groups'])) {
		foreach($weeklysale->revenuehead['groups'] as $name => $i) { ?>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->revenuehead['sumgroup'][$i]) ?></nobr></td>
                <? } 
		} ?>

                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->sumcash) ?></nobr></td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->saletotal) ?></nobr></td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->sumcashin) ?></nobr></td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->sumcashout) ?></nobr></td>
                <td class="number"><? print $_lib['format']->Amount($weeklysale->head->sumActuallyCashAmount) ?></td>
                <td class="number"><? if($weeklysale->head->sumcashdiff != 0) { print "<font color=\"red\">"; } ?><? print $_lib['format']->Amount($weeklysale->head->sumcashdiff) ?><? if($weeklysale->head->sumcashdiff != 0) { print "</font>"; } ?></td>
                <td><? if($weeklysale->head->sumcashdiff != 0) { ?><input <? print $readonly ?> type="text" name="weeklysale.CashAmountExplanation.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->CashAmountExplanation ?>" size="20" tabindex="306"><? } ?>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2">Likvidkonto</td>
                <td></td>
                <? if(is_array($weeklysale->revenuehead['account'])) {
                    foreach($weeklysale->revenuehead['account'] as $i => $name) { ?>
                    <td align="right"><? print $name ?></td>
                <? } 
                } ?>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td colspan="2">Avdelinger</td>
                <td></td>
                <? if(is_array($weeklysale->revenuehead['department'])) {
                    foreach($weeklysale->revenuehead['department'] as $i => $name) { ?>
                    <td align="left"><nobr><? print $name ?></nobr></td>
                <? }
                } ?>
                <td>
                <td colspan=7">
            </tr>
            <tr>
                <td colspan="2">Prosjekter</td>
                <td></td>
                <? if(is_array($weeklysale->revenuehead['project'])) {
                    foreach($weeklysale->revenuehead['project'] as $i => $name) { ?>
                    <td><? print $name ?></td>
                <? }
                } ?>
                <td colspan="8"></td>
            </tr>
            <tr>
                <td class="menu" colspan="3">Kontant inn</td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->sumcashin) ?></nobr></td>
                <td colspan="10"></td>
            </tr>
            <tr>
                <td class="menu" colspan="3">Kontant ut</td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->sumcashout) ?></nobr></td>
                <td colspan="10"></td>
            </tr>
            <tr>
                <td class="menu" colspan="3">Privat: (Innskudd - Uttak)</td>
                <td class="number"><input <? print $readonly ?> type="text" name="weeklysale.PrivateAmount.<? print $weeklysale->head->WeeklySaleID ?>" value="<? $hash = $_lib['format']->Amount(array('value'=>$weeklysale->head->PrivateAmount)); print $hash['value']; ?>" size="8" class="number" tabindex="305">
                <td>Forklaring</td>
                <td colspan="3"><input <? print $readonly ?> type="text" name="weeklysale.PrivateExplanation.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->PrivateExplanation ?>" size="20" class="number" tabindex="306">
                <td colspan="6"></td>
            </tr>
            <tr>
                <td class="menu" colspan="3">Sum</td>
                <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->head->TotalAmount) ?></nobr></td>
                <td colspan="10"></td>
            </tr>
        </table>
        <?
        if(!$weeklysale->head->Period)
        {
            ?>
            <input type="submit" name="action_weeklysale_journal"  value="Lagre (S)" accesskey="S" align="right" tabindex="307"/>
            <?
        }
        elseif($accounting->is_valid_accountperiod($weeklysale->head->Period, $_lib['sess']->get_person('AccessLevel')))
        {
            ?>
            <input type="submit" name="action_weeklysale_journal"  value="Lagre (S)" accesskey="S" align="right" />
            <?
        }
        ?>
    </form>
    <? includeinc('bottom') ?>
</body>
</html>
