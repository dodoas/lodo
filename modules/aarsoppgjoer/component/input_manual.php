<?php
/*
 * Created on 24.aug.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */






	if (!empty($error)) {
		print "<div class=\"warning\">$error</div>";
	}
?>


<h2 class="groupheader">Meny</h2>
<ul>
	<li><a href="?t=aarsoppgjoer.index&a=csv_import">Importer data fra Excel</a></li>
	<li><a href="?t=aarsoppgjoer.index&a=reset">Nullstill skjema</a></li>
</ul>


	<table id="aarsoppgjoer_input">
    <tr>    
        <td colspan="3"></td>
        <td colspan="2">Foregående år, hent periode:</td>
        <td colspan="2" align="right">
            <form method="post" action="?t=aarsoppgjoer.index&a=hentperiode">
            Start: 
            <input type="text" id="start" name="start" value="" size="7" maxlength="7"/>
            Slutt:  
            <input type="text" id="slutt" name="slutt" value="" size="7" maxlength="7"/>
            <input type="submit" value="Hent" />
            </form>
        </td>
    </tr>
	<tr>
	   <td colspan="3">Hent eksisterende bilag:</td>
	   <td colspan="2" align="right">
           
            <form method="post" action="?t=aarsoppgjoer.index&a=hentbilag&bilag=2">
                Dette år:
                <input type="text" id="id" name="type"  value="<? print $b2->type ?>"               size="1" maxlength="1"/>
                <input type="text" id="id" name="id"    value="<?php print $b2->getNummer(); ?>"    size="5" maxlength="7"/>
                <input type="submit" value="Hent" />
            </form>
        </td>
        <td colspan="2" align="right"> 
            <form method="post" action="?t=aarsoppgjoer.index&a=hentbilag&bilag=1">
                Foregående år:
                <input type="text" id="id" name="type"  value="<? print $b1->type ?>"         size="1" maxlength="1"/>
                <input type="text" id="id" name="id" value="<?php print $b1->getNummer(); ?>" size="5" maxlength="7"/>
                <input type="submit" value="Hent" />
            </form>
            <?
                if (!empty($input_manual_bilag_ikke_funnet)) {
                    print "<span class=\"warning\">$input_manual_bilag_ikke_funnet</span>";
                }
            ?>
	    </td>
	</tr>
	<tr>
		<th colspan="3">&nbsp;</th>
		<th colspan="2">Dette år</th>
		<th colspan="2">Foregående år</th>
	</tr>

<?php /* Brukes ikke om man ikke filtrerer pr. avdeling
	<tr>
		<td colspan="7">
			<form action="?t=aarsoppgjoer.index&a=chooseavdeling" method="post">
				Velg avdeling
				<select id="avdeling" name="avdeling">
					<?php
						foreach ($avdelinger as $avd) {
							print '<option ';
							if ($avd->id == $avdeling) {
								print 'selected="selected" ';
							}
							print 'value="' . $avd->id . '">' . $avd->navn . '</option>';
						}
					?>
				</select>
				<input type="submit" value="Velg">
			</form>
		</td>
	</tr>
*/ ?>

	<form id="skjema" name="skjema" action="?t=aarsoppgjoer.index&a=manualsubmit" method="post">
	<tr>
		<td colspan="3">&nbsp;</td>
		<td>Bilagsnummer:</td><td><? print $b2->type ?><input class="left" type="text" id="bilag2-nr" name="bilag2-nr" value="<?php print $b2->getNummer(); ?>"/></td>
		<td>Bilagsnummer:</td><td><? print $b1->type ?><input class="left" type="text" id="bilag1-nr" name="bilag1-nr" value="<?php print $b1->getNummer(); ?>"/></td>
	</tr>
	<tr>
		<td colspan="3">
			<input type="radio" name="radiotype" <?php if ( ($_SESSION['aarsoppgjoer_type'] == 'lagre') || (!isset($_SESSION['aarsoppgjoer_type'])) ) {print 'checked="checked"';}; ?> value="lagre">Mellomlagre</input>
			<input type="radio" name="radiotype" <?php if ($_SESSION['aarsoppgjoer_type'] == 'direkte') {print "checked=\"checked\"";}; ?> value="direkte">Før direkte</input>
			<input type="radio" name="radiotype" <?php if ($_SESSION['aarsoppgjoer_type'] == 'diff') {print "checked=\"checked\"";}; ?> value="diff">Differanse</input>
		<td>Bilagsdato:</td><td><input class="left" type="text" id="bilag2-dato" name="bilag2-dato" value="<?php print $b2->getDate(); ?>" /></td>
		<td>Bilagsdato:</td><td><input class="left" type="text" id="bilag1-dato" name="bilag1-dato" value="<?php print $b1->getDate(); ?>" /></td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" name="submit" id="submit" value="Forhåndsvisning" /></td>
		<td>Periode:</td><td><input class="left" type="text" id="bilag2-periode" name="bilag2-periode" value="<?php print $b2->getPeriod(); ?>" /></td>
		<td>Periode:</td><td><input class="left" type="text" id="bilag1-periode" name="bilag1-periode" value="<?php print $b1->getPeriod(); ?>" /></td>
	</tr>

	<?php
		$li = 0;

		$kn = "";
		
		foreach ($linjer as $linje) {

			$kto  = $linje->getKonto()->nummer;
			$ktmp = $linje->getKonto()->AccountPlanType;

			if ($ktmp != $kn) {
				$kn = $ktmp;
				print "<tr><th colspan=\"7\">";
				print $kn;
				print "</th></tr>";
				?>
				<tr>
					<th colspan="2">Konto</th>
					<th>Avdeling</th>
					<th>Debet</th>
					<th>Kredit</th>
					<th>Debet</th>
					<th>Kredit</th>
				</tr>
			<?
			}
			?>

			<tr>
			<td><?php print $linje->getKonto()->nummer; ?></td>
			<td><?php print $linje->getKonto()->navn; ?></td>

			<td><?php
				$kkk = $linje->avdeling;
				$locked = "";
				if (!empty($kkk)) {
					print "$kkk->id - $kkk->navn";
					$minavd = $kkk->id;
				} else {
					print "null";
					$minavd = 0;
				}
			?></td>
			
			<input type="hidden" name="bilag1<?php print $li; ?>-kto" id="bilag1<?php print $li; ?>-konto" value="<?php print $linje->getKonto()->nummer; ?>" />
			<input type="hidden" name="bilag2<?php print $li; ?>-kto" id="bilag2<?php print $li; ?>-konto" value="<?php print $linje->getKonto()->nummer; ?>" />

			<input type="hidden" name="bilag1<?php print $li; ?>-id" id="bilag1<?php print $li; ?>-id" value="<?php print $linje->getId1(); ?>" />
			<input type="hidden" name="bilag2<?php print $li; ?>-id" id="bilag2<?php print $li; ?>-id" value="<?php print $linje->getId2(); ?>" />
			
			<input type="hidden" name="bilag1<?php print $li; ?>-avd" id="bilag1<?php print $li; ?>-avd" value="<?php print $minavd; ?>" />
			<input type="hidden" name="bilag2<?php print $li; ?>-avd" id="bilag2<?php print $li; ?>-avd" value="<?php print $minavd; ?>" />
			
			<td><input type="text" <?php print $locked; ?> name="bilag2<?php print $li; ?>-deb" id="bilag2<?php print $li; ?>-deb" value="<?php $a = $linje->getBel2()->getDebet(); if (!empty($a)) {print number_format($a, 2, ',', ' ');}; ?>" /></td>
			<td><input type="text" <?php print $locked; ?>  name="bilag2<?php print $li; ?>-kred" id="bilag2<?php print $li; ?>-kred" value="<?php $a = $linje->getBel2()->getKredit(); if (!empty($a)) {print number_format($a, 2, ',', ' ');}; ?>" /></td>

			<td><input type="text" <?php print $locked; ?>  name="bilag1<?php print $li; ?>-deb" id="bilag1<?php print $li; ?>-deb" value="<?php $a = $linje->getBel1()->getDebet(); if (!empty($a)) {print number_format($a, 2, ',', ' ');}; ?>" /></td>
			<td><input type="text" <?php print $locked; ?>  name="bilag1<?php print $li; ?>-kred" id="bilag1<?php print $li; ?>-kred" value="<?php $a = $linje->getBel1()->getKredit(); if (!empty($a)) {print number_format($a, 2, ',', ' ');}; ?>" /></td>
			
			</tr>
			<?php
			$li++;
		}
	?>
	</table>
	<?php
		if (isset($script)) {
			print '<script type="text/javascript">';
			print $script;
			print "</script>";
		}
	?>
</form>
