<?php
$_REQUEST["Period"] = $_REQUEST["report_Year"];
$db_table = "inbarbeidsgiveravgift";

includemodel('arbeidsgiveravgift/grid');
includemodel('accounting/accounting');
$accounting     = new accounting();

require_once('record2.inc');
$query = "select * from arbeidsgiveravgift";
$result = $_lib['db']->db_query($query);
while($row = $_lib['db']->db_fetch_object($result)) {
	$sats_u = "sats_u_" . $row->Code;
	$sats_o = "sats_o_" . $row->Code;
	$$sats_u = $row->Percent;
	$$sats_o = $row->Percent62;
}

print $_lib['sess']->doctype; ?>
<head>
        <title>Innberettet arbeidsgiveravgift <? print $_lib['sess']->get_companydef('VName'); ?> - <? print $_REQUEST["Period"]; ?></title>
        <? includeinc('head'); ?>
    </head>
<body>
<h2><? print $_lib['sess']->get_companydef('VName'); ?> - <? print $_REQUEST["Period"]; ?> - Innberettet arbeidsgiveravgift</h2>

<table border="0" cellspacing="2" width="100%">
    <thead>
        <tr>
            <th colspan="4">I f&oslash;lge innsendte oppgaver</th>
        <tr>
            <td class="number">Termin/Sone</td>
<?php
/**
 * ********************************************************************************************************
 * Headingene
 * ********************************************************************************************************
 */
for ($i = 1; $i < 6; $i++)
{
?>
            <td class="number" colspan="2" align="center">Avgiftssone <?php print $i; ?></td>
<?php
}
?>
            <td class="number">Trukket</td>
        </tr>
        <tr>
            <td class="number">Termin/Sone</td>
<?php
for ($i = 1; $i < 6; $i++)
{
?>
            <td class="number">Grunnbel&oslash;p <?php print $i; ?></td>
            <td class="number">Avgift <?php print $i; ?></td>
<?php
}
?>
            <td class="number">forskuddstrekk</td>
        </tr>
    </thead>
    <tbody>
        <form name="budget" action="<? print $_lib['sess']->dispatch."t=arbeidsgiveravgift.inberarbeidsgiveravgift&Period=" . $_REQUEST["Period"]; ?>" method="post">
<input type="hidden" name="report_Year" value="<?php print $_REQUEST["report_Year"]; ?>">
<?php
// Opprett objektet.
$arbAvg = new arbeidsgiveravgift_grid();
$arbAvg->selectYear($_REQUEST["Period"]);

for ($termin = 1; $termin < 7; $termin++)
{
/**
 * ********************************************************************************************************
 * Terminene 1-6
 * ********************************************************************************************************
 */

$result = $arbAvg->gridNextTermin($termin);
$myIndex = "inbarbeidsgiveravgiftID";
$myIndexNr = $result->$myIndex;
?>
            <input type="hidden" name="<?php print $db_table . "_" . $termin . "_" . $myIndex; ?>"  value="<?php print $myIndexNr; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $termin . "_year"; ?>"         value="<?php print $_REQUEST["Period"]; ?>">
            <input type="hidden" name="<?php print $db_table . "_" . $termin . "_termin"; ?>"       value="<?php print $termin; ?>">
        <tr>
            <td class="number"><b>Termin <?php print $termin; ?></b></td>
            <td colspan="10">Kommentar: <input align="right" type="text" size="120" name="<?php print $db_table . "_" . $termin . "_Description"; ?>" value="<?php print $result->Description; ?>"></td>
        </tr>
        <tr>
            <td class="number">Under 62</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
?>
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_" . $termin . "_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $termin . "_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
        <tr>
            <td class="number">Over 62</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
?>
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_" . $termin . "_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_" . $termin . "_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
<?php
}
?>
        <tr>
            <td class="number" colspan="12"><hr></td>
        </tr>
        <tr>
            <td class="number"><b>Termin 1-6</b></td>
        </tr>
        <tr>
            <td class="number">Under 62</td>
<?php
/**
 * ********************************************************************************************************
 * Sum for terminene
 * ********************************************************************************************************
 */

$result = $arbAvg->sumTerminer();
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $sats / 100;
        #print "$avgift = $grunnlag * $sats / 100<br>\n";
?>
            <td class="number"><?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>
        <tr>
            <td class="number">Over 62</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
?>
            <td class="number"><?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>
<!--
********************************************************
Sum for terminene.
********************************************************
-->

        <tr>
            <td class="number">Termin 1-6</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag1 = $result->$myInfo;
		$avgift1 = $grunnlag1 * $$sats / 100;

		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag2 = $result->$myInfo;
		$avgift2 = $grunnlag2 * $$sats / 100;
		$grunnlag_term1_6 = "grunnlag_term1_6" . "_" . $i;
		$avgift_term1_6 = "avgift_term1_6" . "_" . $i;
		$$grunnlag_term1_6 = $grunnlag = $grunnlag1 + $grunnlag2;
		$$avgift_term1_6 = $avgift = $avgift1 + $avgift2;

?>
            <td class="number"><?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk1 = $result->$myInfo;
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk2 = $result->$myInfo;
	$forskuddstrekk_term1_6 = $forskuddstrekk = $forskuddstrekk1 + $forskuddstrekk2;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>

        
        
        
        <tr>
            <td class="number" colspan="12"><hr></td>
        </tr>
        <tr>
            <td colspan="12" align="left"><b>Utbetalt under grensen for oppgaveplikt</b> (Lønnsinntekt  mindre enn kr  eks 1 000,- og skattrekke mindre enn eller lik 0.)</td>
        </tr>
        <tr>
            <td class="number">Under 62</td>
<?php
/**
 * ********************************************************************************************************
 * Under grensen for oppgaveplikt.
 * ********************************************************************************************************
 */

$result = $arbAvg->gridNextTermin(20);
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
		$myIndex = "inbarbeidsgiveravgiftID";
		$myIndexNr = $result->$myIndex;
?>
            <input type="hidden" name="<?php print $db_table . "_20_" . $myIndex; ?>" value="<?php print $myIndexNr; ?>">
            <input type="hidden" name="<?php print $db_table . "_20_year"; ?>" value="<?php print $_REQUEST["Period"]; ?>">
            <input type="hidden" name="<?php print $db_table . "_20_termin"; ?>" value="20">
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_20_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_20_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
        <tr>
            <td class="number">Over 62</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
?>
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_20_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_20_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
<!--
********************************************************
Sum for Utbetalt under grensen for oppgaveplikt
********************************************************
-->
                
        <tr>
            <td class="number">Sum u/grens</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag1 = $result->$myInfo;
		$avgift1 = $grunnlag1 * $$sats / 100;

		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag2 = $result->$myInfo;
		$avgift2 = $grunnlag2 * $$sats / 100;
		$grunnlag_ugrens = "grunnlag_ugrens" . "_" . $i;
		$avgift_ugrens = "avgift_ugrens" . "_" . $i;
		$$grunnlag_ugrens = $grunnlag = $grunnlag1 + $grunnlag2;
		$$avgift_ugrens = $avgift = $avgift1 + $avgift2;

?>
            <td class="number">
            <?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk1 = $result->$myInfo;
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk2 = $result->$myInfo;
	$forskuddstrekk_ugrens = $forskuddstrekk = $forskuddstrekk1 + $forskuddstrekk2;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>
        <tr>
            <td class="number" colspan="12"><hr></td>
        </tr>

        
        
        
        <tr>
            <td colspan="12" align="left"><b>Utbetalt over grensen for oppgaveplikt</b> (Lønnsinntekt  større enn kr  eks 1 000,- eller skattrekke større enn 0.)</td>
        </tr>
        <tr>
            <td class="number">Under 62</td>
<?php
/**
 * ********************************************************************************************************
 * Over grensen for oppgaveplikt.
 * ********************************************************************************************************
 */

$result = $arbAvg->gridNextTermin(30);
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
		$myIndex = "inbarbeidsgiveravgiftID";
		$myIndexNr = $result->$myIndex;
?>
            <input type="hidden" name="<?php print $db_table . "_30_" . $myIndex; ?>" value="<?php print $myIndexNr; ?>">
            <input type="hidden" name="<?php print $db_table . "_30_year"; ?>" value="<?php print $_REQUEST["Period"]; ?>">
            <input type="hidden" name="<?php print $db_table . "_30_termin"; ?>" value="30">
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_30_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_30_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
        <tr>
            <td class="number">Over 62</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag = $result->$myInfo;
		$avgift = $grunnlag * $$sats / 100;
?>
            <td class="number">
            <input align="right" type="text" size="15" name="<?php print $db_table . "_30_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($grunnlag); ?>"></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk = $result->$myInfo;
?>
            <td class="number"><input align="right" type="text" size="15" name="<?php print $db_table . "_30_" . $myInfo; ?>" value="<?php print $_lib['format']->Amount($forskuddstrekk); ?>"></td>
        </tr>
<!--
********************************************************
Sum for Utbetalt over grensen for oppgaveplikt
********************************************************
-->
                
        <tr>
            <td class="number">Sum o/grens</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$sats = "sats_u_" . $i;
		$myInfo = "S" . $i . "grunnbelop_u62";
		$grunnlag1 = $result->$myInfo;
		$avgift1 = $grunnlag1 * $$sats / 100;
		$sats = "sats_o_" . $i;
		$myInfo = "S" . $i . "grunnbelop_o62";
		$grunnlag2 = $result->$myInfo;
		$avgift2 = $grunnlag2 * $$sats / 100;
		$grunnlag_ogrens = "grunnlag_ogrens" . "_" . $i;
		$avgift_ogrens = "avgift_ogrens" . "_" . $i;
		$$grunnlag_ogrens = $grunnlag = $grunnlag1 + $grunnlag2;
		$$avgift_ogrens = $avgift = $avgift1 + $avgift2;
?>
            <td class="number"><?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}
	$myInfo = "forskuddstrekk_u62";
	$forskuddstrekk1 = $result->$myInfo;
	$myInfo = "forskuddstrekk_o62";
	$forskuddstrekk2 = $result->$myInfo;
	$forskuddstrekk_ogrens = $forskuddstrekk = $forskuddstrekk1 + $forskuddstrekk2;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>
        <tr>
            <td class="number" colspan="12"><hr></td>
        </tr>
<!--
********************************************************
Differanser.
********************************************************
-->
                
        <tr>
            <td class="number">Differanser</td>
<?php
	for ($i = 1; $i < 6; $i++)
	{
		$grunnlag_ugrens = "grunnlag_ugrens" . "_" . $i;
		$avgift_ugrens = "avgift_ugrens" . "_" . $i;
		$grunnlag_ogrens = "grunnlag_ogrens" . "_" . $i;
		$avgift_ogrens = "avgift_ogrens" . "_" . $i;
		$grunnlag_term1_6 = "grunnlag_term1_6" . "_" . $i;
		$avgift_term1_6 = "avgift_term1_6" . "_" . $i;

		$grunnlag = $$grunnlag_term1_6 - $$grunnlag_ugrens - $$grunnlag_ogrens;
		$avgift = $$avgift_term1_6 - $$avgift_ugrens - $$avgift_ogrens;
?>
            <td class="number"><?php print $_lib['format']->Amount($grunnlag); ?></td>
            <td class="number"><?php print $_lib['format']->Amount($avgift); ?></td>
<?php
	}

	$forskuddstrekk = $forskuddstrekk_term1_6 - $forskuddstrekk_ugrens - $forskuddstrekk_ogrens;
?>
            <td class="number"><?php print $_lib['format']->Amount($forskuddstrekk); ?></td>
        </tr>
        <tr>
            <td class="number" colspan="12"><hr></td>
        </tr>

        
        
        

    </tbody>
</table>
<br/><br/>
<? if($accounting->is_valid_accountperiod($_REQUEST["Period"] . "-13", $_lib['sess']->get_person('AccessLevel')) && $_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<table>
    <tr>
        <td width="500" align="right"><? print $_lib['form3']->submit(array('name'=>'action_inberarbeidsgiveravgift_update', 'value'=>'Lagre (S)', 'accesskey'=>'S')) ?></td>
    </tr>
</table>
<?php
}
?>
</form>
</body>
</html>
