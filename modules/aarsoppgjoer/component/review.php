<?php
/*
 * Created on 24.aug.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>


<h2 class="groupheader">Meny</h2>
<ul>
	<li><a href="?t=aarsoppgjoer.index&a=input_manual">Rediger</a></li>
<?php
	if ($b2->sum() == 0 && ($b1->sum() == 0 || $b1->getNummer() == 0)) {
		print "<li><a href=\"?t=aarsoppgjoer.index&a=lagrebilag\">Lagre (Bokfør i Lodo)</a></li>";
	} else {
		print "<li>Kan ikke lagre. Bilagene må gå i null.</li>";
	}
?>
	<li><a href="?t=aarsoppgjoer.index&a=reset">Nullstill</a></li>
</ul>

<table id="review"><tr>
	<td valign="top">
	<table id="review">
		<tr>
			<th colspan="3">&nbsp;</th>
			<th colspan="2">Bilag 2</th>
			<th colspan="2">Bilag 1</th>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td colspan="2">
				Bilagsnummer: K<?php print $b2->getNummer();
					if ($b2->getNummer() == 0) {
						print " - Lagres ikke";
					}
				?><br />
				Bilagsdato: <?php print $b2->getDate(); ?><br />
				Periode: <?php print $b2->getPeriod(); ?>
			</td>
			<td colspan="2">
				Bilagsnummer: K<?php print $b1->getNummer();
					if ($b1->getNummer() == 0) {
						print " - Lagres ikke";
					}
				?><br />
				Bilagsdato: <?php print $b1->getDate(); ?><br />
				Periode: <?php print $b1->getPeriod(); ?>
			</td>
		</tr>
			
		<?php
			$kn = "";
			
			$b1_deb = 0;
			$b1_kre = 0;
			$b2_deb = 0;
			$b2_kre = 0;
			
			$h1_deb = 0;
			$h1_kre = 0;
			$h2_deb = 0;
			$h2_kre = 0;
			foreach ($linjer as $linje) {
				$bel1 = $linje->getBel1();
				$bel2 = $linje->getBel2();
				$t1 = $bel1->getKredit();
				$t2 = $bel1->getDebet();
				$t3 = $bel2->getKredit();
				$t4 = $bel2->getDebet();
				if (empty($t1) && empty($t2) && empty($t3) && empty($t4)) {
					continue;
				}
				
				$kto  = $linje->getKonto()->nummer;
				$ktmp = $linje->getKonto()->navn;
				if ($ktmp != $kn) {
					if ($kn != '') {
						//sum her
						print '<tr><th colspan="7"></th></tr>';
						?>
<tr>
	<td colspan="3" class="tdleft">Sum</td>
	<td><?php print number_format($h2_deb, 2, ',', ' '); ?></td>
	<td><?php print number_format($h2_kre, 2, ',', ' '); ?></td>
	<td><?php print number_format($h1_deb, 2, ',', ' '); ?></td>
	<td><?php print number_format($h1_kre, 2, ',', ' '); ?></td>
</tr>
<tr>
	<td colspan="3" class="tdleft">Differanse</td>
	<td></td>
	<td><?php print number_format($h2_deb - $h2_kre, 2, ',', ' '); ?></td>
	<td></td>
	<td><?php print number_format($h1_deb - $h1_kre, 2, ',', ' '); ?></td>
</tr>
						<?php
						$h1_deb = 0;
						$h1_kre = 0;
						$h2_deb = 0;
						$h2_kre = 0;
					}
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
				<td class="tdleft"><?php print $linje->getKonto()->navn; ?></td>
				
				<td><?php print $linje->getAvdeling()->navn; ?></td>
				
				<td><?php $tmp = $linje->getBel2(); $tmp = $tmp->getDebet(); $b2_deb += $tmp; $h2_deb += $tmp; print number_format($tmp, 2, ',', ' '); ?></td>
				<td><?php $tmp = $linje->getBel2(); $tmp = $tmp->getKredit(); $b2_kre += $tmp; $h2_kre += $tmp; print number_format($tmp, 2, ',', ' '); ?></td>

				<td><?php $tmp = $linje->getBel1(); $tmp = $tmp->getDebet(); $b1_deb += $tmp; $h1_deb += $tmp; print number_format($tmp, 2, ',', ' '); ?></td>
				<td><?php $tmp = $linje->getBel1(); $tmp = $tmp->getKredit(); $b1_kre += $tmp; $h1_kre += $tmp; print number_format($tmp, 2, ',', ' '); ?></td>
				
				</tr>
				<?php
			}
		?>



<?php /*    Sum for siste konto-bolk              */ ?>
<tr><th colspan="7"></th></tr>
<tr>
	<td colspan="3" class="tdleft">Sum</td>
	<td><?php print number_format($h2_deb, 2, ',', ' '); ?></td>
	<td><?php print number_format($h2_kre, 2, ',', ' '); ?></td>
	<td><?php print number_format($h1_deb, 2, ',', ' '); ?></td>
	<td><?php print number_format($h1_kre, 2, ',', ' '); ?></td>
</tr>
<tr>
	<td colspan="3" class="tdleft">Differanse</td>
	<td></td>
	<td><?php print number_format($h2_deb - $h2_kre, 2, ',', ' '); ?></td>
	<td></td>
	<td><?php print number_format($h1_deb - $h1_kre, 2, ',', ' '); ?></td>
</tr>


		
<?php /*    Sum for hele skjemaet under ett           */ ?>
			<tr>
				<th colspan="3">Total</th>
				<th colspan="2"></th>
				<th colspan="2"></th>
			</tr>
			
			<tr>
				<td colspan="3" class="tdleft">Sum</td>
	
				<td><?php print number_format($b2_deb, 2, ',', ' '); ?></td>
				<td><?php print number_format($b2_kre, 2, ',', ' '); ?></td>
	
				<td><?php print number_format($b1_deb, 2, ',', ' '); ?></td>
				<td><?php print number_format($b1_kre, 2, ',', ' '); ?></td>
			</tr>
	
			<tr>
				<td colspan="3" class="tdleft">Differanse</td>
				<?
					$diff2 = $b2_deb - $b2_kre;
					if (abs($diff2) < 0.0001) {
						$diff2 = 0;
					}
					$diff1 = $b1_deb - $b1_kre;
					if (abs($diff1) < 0.0001) {
						$diff1 = 0;
					}
				?>
	
				<td></td>
				<td><?php print number_format($diff2, 2, ',', ' '); ?></td>
	
				<td></td>
				<td><?php print number_format($diff1, 2, ',', ' '); ?></td>
			</tr>
	
	
	</table>
	</td>
	<td valign="top">
		&nbsp;
	</td>
	</tr>
</table>
