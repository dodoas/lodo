<?
/* $Id: edit.php,v 1.67 2005/10/24 11:54:33 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

//gjøre en sjekk om denne allerede er satt??
$WeeklySaleID       = (int) $_REQUEST['WeeklySaleID'];
$WeeklySaleConfID   = (int) $_REQUEST['WeeklySaleConfID'];

includelogic('accounting/accounting');
includelogic('weeklysale/weeklysale');
$accounting = new accounting();
require_once "record.inc";

$weeklysale = new weeklysale($WeeklySaleID, $WeeklySaleConfID);
$weeklysale->presentation();

if(isset($_POST['init_bilagsnummer'])) {
    $weeklysale->head->Week        = (int)$_POST['init_week'];	
}

$allow_changes = (($_GET["action_weeklysale_new"] == "1") || ($_lib['sess']->get_person('AccessLevel') >= 4));

$readonly = "";
if($allow_changes) {
    if($weeklysale->head->Period and !$accounting->is_valid_accountperiod($weeklysale->head->Period, $_lib['sess']->get_person('AccessLevel'))) {
        $_lib['message']->add(array('message' => "Perioden er lukket, du kan ikke endre data"));
        $readonly = "readonly disable";
    }
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

<?php

/*
 * Check if there is already registered any weekly sales on this journal id.
 * prints a message to the user if this is true
 */
$duplicates = $_lib['db']->db_query("SELECT WeeklySaleID FROM weeklysale WHERE JournalID = ".$weeklysale->head->JournalID." AND WeeklySaleID != " . $WeeklySaleID . " AND VoucherType = '" . $weeklysale->head->VoucherType . "'");
if($_lib['db']->db_numrows($duplicates) >= 1) {
    printf('Det finnes allerede en ukeomsetning med bilagsnummer %d. <a href="%s">G&aring; tilbake</a>', $weeklysale->head->JournalID, 
		$_lib['sess']->dispatch . "&view_mvalines=&view_linedetails=&t=weeklysale.list");
    exit();
}


?>

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

		if($allow_changes) {
	                print $_lib['form3']->text(array('table'=>'weeklysale',   'field' => 'JournalID'  , 'pk' => $weeklysale->head->WeeklySaleID, 'value' => $weeklysale->head->JournalID, 'width' => 10, 'tabindex' => 1)); 
		}
		else {
			print $weeklysale->head->JournalID;
	                print $_lib['form3']->hidden(array('table'=>'weeklysale',   'field' => 'JournalID'  , 'pk' => $weeklysale->head->WeeklySaleID, 'value' => $weeklysale->head->JournalID, 'width' => 10, 'tabindex' => 1)); 
		}
		?>
                <? print $_lib['form3']->hidden(array('name'=>'VoucherPeriodOld', 'value'=>$weeklysale->head->Period)) ?>
                </td>
                <td colspan="2">
		<? 
		if($allow_changes) {
			print $_lib['form3']->text(array('table'=>'weeklysale', 'field'=>'JournalDate', 'pk'=>$weeklysale->head->WeeklySaleID, 'value'=>$weeklysale->head->JournalDate, 'width'=>'10', 'tabindex'=>'2', 'OnChange'=>"update_period(this, '".$formname."', 'weeklysale.JournalDate.".$weeklysale->head->WeeklySaleID."', 'weeklysale.Period.".$weeklysale->head->WeeklySaleID."');")); 
		}
		else {
			print $_lib['form3']->hidden(array('table'=>'weeklysale', 'field'=>'JournalDate', 'pk'=>$weeklysale->head->WeeklySaleID, 'value'=>$weeklysale->head->JournalDate, 'width'=>'10', 'tabindex'=>'2', 'OnChange'=>"update_period(this, '".$formname."', 'weeklysale.JournalDate.".$weeklysale->head->WeeklySaleID."', 'weeklysale.Period.".$weeklysale->head->WeeklySaleID."');")); 
			print $weeklysale->head->JournalDate;
		}
		?>
		</td>
                <td align="center">
		<? 
		if($allow_changes) {
			print $_lib['form3']->AccountPeriod_menu3(array('table' => 'weeklysale', 'field' => 'Period', 'pk'=>$weeklysale->head->WeeklySaleID, 'value' => $weeklysale->head->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'pk' => $weeklysale->head->WeeklySaleID, 'tabindex'=>'3', 'required'=>'1'));
		}
		else {
			print $weeklysale->head->Period;
		}
		?>
		</td>
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
                <td colspan="3"><?php if($is_dup) echo "<b>Bilagsnummeret er allerede registrert!</b>"; ?></td>
                <td colspan="3"></td>
                <td colspan="3"><? print $weeklysale->head->CompanyZipCode ?> <? print $weeklysale->head->CompanyCity ?></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="1">Uke</td>
                <td colspan="2">
		<?php
		if($allow_changes) {
		?>
			<input type="text" <? print $readonly ?> name="weeklysale.Week.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->Week ?>" size="3" class="number" tabindex="4">
		<?php
		}
		else {
			print $weeklysale->head->Week;
		} 
		?>
                <td colspan="1"></td>
                <td colspan="1"><input type="hidden" <? print $readonly ?> name="weeklysale.Year.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->Year ?>" size="5" class="number" tabindex="4">
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
                <td class="menu" style="text-align: right;">Dag</td>
                <td class="menu" style="text-align: right;">Z nr</td>
                <td class="menu" style="text-align: right;">Z nr total</td>
                <? foreach($weeklysale->salehead['groups'] as $name => $id) { ?>
                    <td class="menu" style="text-align: right;"><? print $name ?></td>
                <? } ?>
                <td class="menuright">Total</td>
                <td class="menu" colspan="2">Sig</td>
                <td class="menu"></td>
                <?/*<td class="menu">L&aring;s*/?>
            <?
            /* 
             * Calculate the correct days for the given week number.  
             * If the week starts in one month and ends in another then the days in
             * the first day is omited 
             */
            if(isset($_POST['init_bilagsnummer'])) {
                $count_from = mktime(0,0,1,1,1);
                if(date("N") != 1) 
                    $count_from = strtotime("next monday", $count_from);
                $count_from = strtotime("+" . ($_POST['init_week'] - 1) . " week", $count_from);

                $month_day  = date("d", $count_from);
                $days_in_month = date("t", $count_from);

                if($month_day + 7 > $days_in_month) {
                  $start_from = $days_in_month - $month_day + 1;
                  $start_with = 1;
                }
                else {
                  $start_from = 0;
                  $start_with = $month_day;
                }
                $last_day_name = "";
            }

            $date_counter = 0;
            $tabindex = 0;
            $taboffset = 5;


            //
            // Tabindex calculation:
            // for i in range(1, y+1):
            //     print [if_(j == 1, (j - 1) * y + i, y + (x - 1) * (i - 1) + (j - 1)) for j in range(1, x+1)]
            // 
            // i.e. x = 10, y = 7
            //
            // [1, 8, 9, 10, 11, 12, 13, 14, 15, 16]
            // [2, 17, 18, 19, 20, 21, 22, 23, 24, 25]
            // [3, 26, 27, 28, 29, 30, 31, 32, 33, 34]
            // [4, 35, 36, 37, 38, 39, 40, 41, 42, 43]
            // [5, 44, 45, 46, 47, 48, 49, 50, 51, 52]
            // [6, 53, 54, 55, 56, 57, 58, 59, 60, 61]
            // [7, 62, 63, 64, 65, 66, 67, 68, 69, 70]
            // 
            // --
            // 
            // The first column is indexed downwards, while the rest are indexed to the right.
            //

            $i = 1;
            $j = 1;
            $x = 24;
            $y = count($weeklysale->sale);

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

		<?php
                  if(isset($_POST['init_bilagsnummer']) && $start_from <= $date_counter) {
                     if($last_day_name != $line->WeekDayName) {
                         if($last_day_name != "")
                             $start_with++;
                         $last_day_name = $line->WeekDayName;
                     }

                     $line->Day = (int)$start_with;

                  }
                  $date_counter++;
		?>

                <? $tabno = $taboffset + (($j == 1) ? (($j - 1) * $y + $i) : ($y + ($x - 1) * ($i - 1) + ($j - 1))); $j++; ?>
                <td class="number"><input type="text" <? print $readonly ?> name="weeklysaleday.Day.<? print $line->WeeklySaleDayID ?>" value="<? print $line->Day ?>" size="2" class="number" tabindex="<? print $tabno ?>"></td>

                <? $tabno = $taboffset + (($j == 1) ? (($j - 1) * $y + $i) : ($y + ($x - 1) * ($i - 1) + ($j - 1))); $j++; ?>
                <td class="number"><nobr><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.zreport&WeeklySaleID=<? print $WeeklySaleID ?>&WeeklySaleDayID=<? print $line->WeeklySaleDayID?>">Z</a><input type="text" <? print $readonly ?> name="weeklysaleday.Znr.<? print $line->WeeklySaleDayID ?>" value="<? print $line->Znr ?>" size="4" class="<? print $classznr ?>"  title="<? print $titleznr ?>" tabindex="<? print $tabno ?>"></nobr></td>

                <? $tabno = $taboffset + (($j == 1) ? (($j - 1) * $y + $i) : ($y + ($x - 1) * ($i - 1) + ($j - 1))); $j++; ?>
                <td class="number"><input type="text" <? print $readonly ?> name="weeklysaleday.ZnrTotalAmount.<? print $line->WeeklySaleDayID ?>" value="<? print $_lib['format']->Amount($line->ZnrTotalAmount) ?>" size="7" class="<? print $classznramount ?>" title="<? print $titleznramount ?>" tabindex="<? print $tabno ?>">

                <? foreach($weeklysale->salehead['groups'] as $name => $v) { ?>
                <? $tabno = $taboffset + (($j == 1) ? (($j - 1) * $y + $i) : ($y + ($x - 1) * ($i - 1) + ($j - 1))); $j++; ?>
                <td class="number"><nobr><input type="text" title="Bel&oslash;p" <? print $readonly ?> name="weeklysaleday.Group<? print $v ?>Amount.<? print $line->WeeklySaleDayID ?>"   value="<? print $_lib['format']->Amount($line->{"Group{$v}Amount"}) ?>"  size="7" class="number" tabindex="<? print $tabno ?>">
                <? if($weeklysale->salehead['enablequantity'][$v] == 1) { ?>
                  <? $tabno = $taboffset + (($j == 1) ? (($j - 1) * $y + $i) : ($y + ($x - 1) * ($i - 1) + ($j - 1))); $j++; ?>
                  <input type="text" style="background-color: #999; color: black;" title="Antall"<? print $readonly ?> name="weeklysaleday.Group<? print $v ?>Quantity.<? print $line->WeeklySaleDayID ?>" value="<? print $_lib['format']->Amount(array('value'=>$line->{"Group{$v}Quantity"}, 'return'=>'value')) ?>"  size="7" class="number" tabindex="<? print $tabno ?>" ><? } ?></nobr></td>
                <? } ?>

                <td class="menuright"><nobr><? print $_lib['format']->Amount($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID]) ?></nobr></td>
                <td colspan="2"><? print $line->Person ?></td>
            	<td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $WeeklySaleID ?>&action_weeklysale_daynew=1&amp;DayID=<? print $line->DayID ?>&amp;Day=<? print $line->Day ?>">Ny linje</a></td>
                <?
                $i++;
		$j = 1;

                if($line->Locked == 1 and $readonly == "disabled")
                {
                    $readonly = "";
                }
            }

	    $tabindex = $tabno + 1;
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
                <td class="menu" style="text-align: right;"></td>
                <td class="menu" style="text-align: right;">Dag</td>
                <td class="menu" style="text-align: right;">Znr</td>
                 <? 
                if(is_array($weeklysale->revenuehead['groups'])) {
                    foreach($weeklysale->revenuehead['groups'] as $name => $id) { ?>
                        <td class="menu" style="text-align: right;"><? print $name ?></td>
                    <? } 
                } ?>
                <td class="menu" style="text-align: right;">Kontant</td>
                <td class="menu" style="text-align: right;">Sum</td>
                <td class="menu" style="text-align: right;">Kontant inn</td>
                <td class="menu" style="text-align: right;">Kontant ut</td>
                <td class="menu" style="text-align: right;">Opptelling</td>
                <td class="menu" style="text-align: right;">Diff</td>
                <td class="menu" style="text-align: right;">Forklaring</td>
                <td class="menu" style="text-align: right;">Sig</td>
                <td class="menu" style="text-align: right;">L&aring;s</td>
            <?
            $sumtot = 0;
            $sum = array();
            $counter=1;
            
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
                        <td class="number"><input <? print $readonly ?> type="text" name="weeklysaleday.Group<? print $i ?>Amount.<? print $line->WeeklySaleDayID ?>"      value="<? print $_lib['format']->Amount($line->{"Group{$i}Amount"}) ?>"  size="7" class="number" tabindex="<? print ($counter * $x) ?>">
                        <? } 
			            } ?>
    
                        <!-- Kontant-->
                        <td class="number"><input type="hidden" name="weeklysaleday.Group18Amount.<? print $line->WeeklySaleDayID ?>" value="<? print $weeklysale->head->sum18 ?>"><nobr><? print $_lib['format']->Amount($weeklysale->revenuehead['sumcash'][$line->ParentWeeklySaleDayID]) ?></nobr>
                        <td class="number"><nobr><? print $_lib['format']->Amount($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID]) ?></nobr>
                        <td class="number"><!-- kontant inn --><input type="text" <? print $readonly ?> name="weeklysaleday.Group19Amount.<? print $line->WeeklySaleDayID ?>"   value="<? $hash = $_lib['format']->Amount(array('value'=>$line->Group19Amount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print ($tabindex + (2*7 + $counter)) ?>">
                        <td class="number"><!-- kontant ut --><input type="text" <? print $readonly ?> name="weeklysaleday.Group20Amount.<? print $line->WeeklySaleDayID ?>"   value="<? $hash = $_lib['format']->Amount(array('value'=>$line->Group20Amount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print ($tabindex + (3*7 + $counter)) ?>">
                        <td class="number"><!-- opptelling --><input <? print $readonly ?> type="text" name="weeklysaleday.ActuallyCashAmount.<? print $line->WeeklySaleDayID ?>" value="<? $hash = $_lib['format']->Amount(array('value'=>$line->ActuallyCashAmount)); print $hash['value']; ?>" size="7" class="number" tabindex="<? print ($tabindex + (4*7 + $counter)) ?>">
                        <td class="number"><!-- diff --><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { print "<font color=\"red\">"; } ?><? print $_lib['format']->Amount($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID]) ?><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { print "</font>"; } ?></td>
                        <td><? if($weeklysale->revenuehead['sumdiff'][$line->ParentWeeklySaleDayID] != 0) { ?><input <? print $readonly ?> type="text" name="weeklysaleday.CashAmountExplanation.<? print $line->WeeklySaleDayID ?>" value="<? print $line->CashAmountExplanation ?>" size="20" tabindex="306"><? } ?>
                        <td><? print $line->Person ?><? //$_lib['form2']->CompanyContactMenu( array('table' => 'weeklysaleday', 'field' => 'PersonID', 'value' => $line->PersonID, 'pk' => $line->WeeklySaleDayID, 'disabled'=>$line->Locked)); ?>
                        <td><? if($weeklysale->salehead['sumday'][$line->ParentWeeklySaleDayID] > 0) { print $_lib['form3']->checkbox(array('name'=>"weeklysaleday.Locked.".$line->WeeklySaleDayID, 'value'=>$line->Locked, 'disabled'=>($_lib['sess']->get_person('AccessLevel') >= 3)?'0':$line->Locked)); } ; ?>
                    </tr>
                    <?
                    $counter += 1;
    
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
	    <? foreach(range(1,3) as $bankn) { ?>
            <tr>
                <td class="menu" colspan="3">Bank <?= $bankn ?></td>
                <td class="number"><input <? print $readonly ?> type="text" name="weeklysale.Bank<?= $bankn ?>Amount.<? print $weeklysale->head->WeeklySaleID ?>" value="<? $hash = $_lib['format']->Amount(array('value'=>$weeklysale->head->{"Bank".$bankn."Amount"})); print $hash['value']; ?>" size="8" class="number" tabindex="305"></td>
		<td>Dato</td>
		<td class="date" colspan="2"><input <? print $readonly ?> type="text" name="weeklysale.Bank<?= $bankn ?>Date.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->{"Bank".$bankn."Date"}; ?>"></td>
		<td colspan="5">Negative bel&oslash;p er innskudd til bank</td>
            </tr>
            <? } ?>
            <tr>
                <td class="menu" colspan="3">Privat:</td>
                <td class="number"><input <? print $readonly ?> type="text" name="weeklysale.PrivateAmount.<? print $weeklysale->head->WeeklySaleID ?>" value="<? $hash = $_lib['format']->Amount(array('value'=>$weeklysale->head->PrivateAmount)); print $hash['value']; ?>" size="8" class="number" tabindex="305">
                <td>Forklaring</td>
                <td colspan="2"><input <? print $readonly ?> type="text" name="weeklysale.PrivateExplanation.<? print $weeklysale->head->WeeklySaleID ?>" value="<? print $weeklysale->head->PrivateExplanation ?>" size="20" class="number" tabindex="306">
                <td colspan="6">Negative bel&oslash;p er uttak fra kasse</td>
            </tr>
            <tr>
                <td class="menu" colspan="3">Sum</td>
		<?
			if($weeklysale->head->TotalAmount < 0)
				$sumcolor = "red";
			else if($weeklysale->head->TotalAmount == 0)
				$sumcolor = "green";
			else
				$sumcolor = "blue";
		?>
                <td class="number" style="color: <?= $sumcolor ?>"><nobr><? print $_lib['format']->Amount($weeklysale->head->TotalAmount) ?></nobr></td>
                <td colspan="10"></td>
            </tr>
        </table>
        <?
        if($_lib['sess']->get_person('AccessLevel') >= 2) 
        {
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
        }
        ?>
    </form>
    <? includeinc('bottom') ?>
</body>
</html>
