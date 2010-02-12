<?php
$db_table   = "feriepenger";
$year       = $_lib['input']->getProperty("report_Year");
includemodel('feriepenger/grid');

require_once('record.inc');
includemodel('accounting/accounting');
$accounting = new accounting();

print $_lib['sess']->doctype; ?>
<head>
        <title>Avsetning feriepenger og arbeidsgiveravgift for <? print $_lib['sess']->get_companydef('VName'); ?> - <? print $_REQUEST["year"]; ?></title>
        <? includeinc('head'); ?>
    </head>
<body>
<h2><? print $_lib['sess']->get_companydef('CompanyName'); ?> - <? print $year; ?> - Avsetning feriepenger og arbeidsgiveravgift</h2>
<table>
    <thead>
        <tr>
            <td align="center" colspan="2">L&oslash;nnsmottaker</td>
            <td colspan="5" align="center">Feriepenger</td>
            <td colspan="2" align="center">Arbeidsgiveravgift</td>
            <td align="center">&nbsp;</td>
        </tr>
        <tr>
            <td class="number">Nr</td>
            <td class="number">Navn</td>
            <td class="number">Feriepenge grunnlag</td>
            <td class="number">Prosent</td>
            <td class="number">Feriepenger</td>
            <td class="number">Utbetalt</td>
            <td class="number">Rest</td>
            <td class="number">Prosent</td>
            <td class="number">Bel&oslash;p</td>
            <td class="number">Skyldig Feriepenge grunnlag</td>
        </tr>
    </thead>
    <tbody>
        <form name="budget" action="<? print $_lib['sess']->dispatch."t=feriepenger.feriepenger&report_Year=" . $_REQUEST["report_Year"]; ?>" method="post">
<?php
// Opprett objektet.
$grid = new feriepenger_grid();
$grid->selectYear($year);
$ansatteList = $grid->gridPersonList();

for ($i = 0; $i < count($ansatteList); $i++)
{
$ansattID = $ansatteList[$i];
$result = $grid->gridPerson($ansattID);
$myIndex = "feriepengerID";
$myIndexNr = $result[$myIndex];
?>
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_" . $myIndex; ?>" value="<?php print $myIndexNr; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_year"; ?>" value="<?php print $year; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_AccountPlanID"; ?>" value="<?php print $ansattID; ?>">
        <tr>
            <td class="number"><?php print $ansattID; ?></td>
            <td class="number"><?php print $result["Navn"]; ?></td>
            <td class="number"><?php print $_lib['format']->Amount($result["Grunnlag"]) ?><input align="right" type="hidden" size="15" name="<?php print $db_table . "_" . $i . "_Grunnlag"; ?>" value="<?php print $_lib['format']->Amount($result["Grunnlag"]) ?>"></td>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $i . "_Prosentsats"; ?>" value="<?php print $_lib['format']->Amount($result["Prosentsats"]) ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($result["Feriepenger"]) ?></td>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $i . "_Utbetalt"; ?>" value="<?php print $_lib['format']->Amount($result["Utbetalt"]) ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($result["Rest"]) ?></td>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $i . "_ArbeidsgiveravgSats"; ?>" value="<?php print $_lib['format']->Amount($result["ArbeidsgiveravgSats"]) ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($result["ArbeidsgiveravgiftBelop"]) ?></td>
            <td class="number"><?php print $_lib['format']->Amount($result["SkyldigFeriepengeGrunnlag"]) ?></td>
        </tr>
<?php
}
?>
        <tr>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td>Sum</td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php print $_lib['format']->Amount($grid->restFeriepenger()) ?></td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php print $_lib['format']->Amount($grid->restArbeidsgiveravgift()) ?></td>
            <td class="number">&nbsp;</td>
        </tr>
<?php
$ansattID = 1000;
$result = $grid->gridPerson($ansattID);
$myIndexNr = $result[$myIndex];
?>
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_" . $myIndex; ?>" value="<?php print $myIndexNr; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_year"; ?>" value="<?php print $year; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $i . "_AccountPlanID"; ?>" value="<?php print $ansattID; ?>">
        <tr>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td>Avsatt</td>
            <td class="number">&nbsp;</td>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $i . "_Prosentsats"; ?>" value="<?php print $_lib['format']->Amount($result["Prosentsats"]) ?>"></td>
            <td class="number">&nbsp;</td>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $i . "_ArbeidsgiveravgSats"; ?>" value="<?php print $_lib['format']->Amount($result["ArbeidsgiveravgSats"]) ?>"></td>
            <td class="number">&nbsp;</td>
        </tr>
        <tr>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td>Reguleres</td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php print $_lib['format']->Amount($grid->restFeriepenger() - $result["Prosentsats"]) ?></td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php print $_lib['format']->Amount($grid->restArbeidsgiveravgift() - $result["ArbeidsgiveravgSats"]) ?></td>
            <td class="number">&nbsp;</td>
        </tr>
        <tr>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td class="number">&nbsp;</td>
            <td>Bokf&oslash;res: </td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php if (($grid->restFeriepenger() - $result["Prosentsats"]) > 0) print "5060 / 2940"; else if (($grid->restFeriepenger() - $result["Prosentsats"]) < 0) print "2940 / 5060"; ?></td>
            <td class="number">&nbsp;</td>
            <td class="number"><?php if (($grid->restArbeidsgiveravgift() - $result["ArbeidsgiveravgSats"]) > 0) print "5060 / 2780"; else if (($grid->restArbeidsgiveravgift() - $result["ArbeidsgiveravgSats"]) < 0) print "2780 / 5060"; ?></td>
            <td class="number">&nbsp;</td>
        </tr>
    </tbody>
</table>
<br/><br/>

<table>
<? if($accounting->is_valid_accountperiod($year . "-13", $_lib['sess']->get_person('AccessLevel')) && $_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<table border="0" cellspacing="0">
    <tr>
        <td width="500" align="right"><? print $_lib['form3']->submit(array('name'=>'action_feriepenger_update', 'value'=>'Oppdater siden (S)', 'accesskey'=>'S')) ?></td>
    </tr>
</table>
<table>
	<tr>
		<td></td>
		<td>Privat</td>
		<td>Statlig</td>
	</tr>
	<tr>
		<td>Feriepengesatser:</td>
		<td>10,2/12.0 %</td>
		<td>12,0 %</td>
	</tr>
</table>
<i>Merk at enkelte fagforeninger kan ha rett på andre satser.</i>
<br>Resurser:
<br><a href="http://www.lovdata.no/all/hl-19880429-021.html#10" target="_blank">Ferieloven § 10.</a>

<?php
}
?>
</form>
</body>
</html>
