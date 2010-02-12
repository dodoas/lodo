
<?php
// Lagre skjemaet først.
global $_REQUEST;
$data = array();



foreach ($_REQUEST as $key => $value)
{
	if (substr($key, 0, 1) == "D")
		if (is_null($data[$key]))
			$data[$key] = $value;
		else
			$data[$key][] = $value;
}
print_r($data);
if (is_array($data))
{
	foreach ( $data as $key => $value )
	{
		if (is_array($value))
		{
			foreach ($value as $v)
			{
				if ( $retData != '' ) {$retData .= '&';}
				$retData .= urlencode( $key ) . '=' . urlencode( $v );
			}
		}
		else
		{
			if ( $retData != '' ) {$retData .= '&';}
			$retData .= urlencode( $key ) . '=' . urlencode( $value );
		}
	}
	$db->Query("UPDATE altinn_schema set data= '" . $retData . "' where instance_id = '" . $_REQUEST["instance_id"] . "';");
}
?>

<h1>RF-1037: Terminoppgave for  arbeidsgiveravgift og forskuddstrekk.</h1>
<p>Dersom denne innberetningen gjelder endringer i tidligere innberettet termin (endringsoppgave), kan du ikke benytte Altinn. Du m&aring; sende en  ny og fullstendig innberetning p&aring; papirskjema til skatteoppkreveren.</p>
<p>Hver 15. januar, 8. mars, 8. mai, 8. juli 8. september og 8. november, skal arbeidsgiver sende terminoppgaven som viser forskuddstrekket, og  grunnlaget for arbeidsgiveravgift for de to foreg&aring;ende m&aring;nedene.  Terminoppgaven
skal sendes selv om det ikke er foretatt trekk i terminen.</p>

<form name="schema_669" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post">
<input type="hidden" name="t" value="altinn.package_edit"/>
  <input type="hidden" name="instance_id" value="<?php print $row["instance_id"] ?>" />
  <input type="hidden" name="packageid" value="<?php print $row["packet_id"] ?>" />

  
<table border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td align="left" valign="top">
  <table width="100%">
    <tr>
      <td><b>1.0</b></td><td>Termin</td><td><?php $a = "D11819"; printInput($a, $data[$a]); ?></td><td>&Aring;r</td><td><?php $a = "D11236"; printInput($a, $data[$a]); ?></td>
    </tr>
    <tr>
      <td colspan="5"><b>
  <a alt="Du skal sende blanketten til skatteoppkreveren i den kommunen hvor  arbeidsgiveren har sitt hovedkontor. Arbeidsgivere som ikke har  hovedkontor, skal levere oppgaven til skatteoppkreveren i den kommune  hvor han h&oslash;rer hjemme.">Til Skatteoppkreveren</a>
  </b></td>
    </tr>
    <tr>
      <td colspan="2">Kommune nr.:</td><td colspan="3"><?php $a = "D16513"; printInput($a, $data[$a]); ?></td>
    </tr>
    <tr>
      <td colspan="2">Kommune navn:</td><td colspan="3"><?php $a = "D8486"; printInput($a, $data[$a]); ?></td>
    </tr>
  </table>
		</td>
		<td align="left" valign="top">
		  <table width="100%">
		    <tr>
		      <td colspan="5"><b>Fra arbeidsgiver- (oppdragsgiver)</b> Nace-kode</td><td><?php $a = "D5133"; printInput($a, $data[$a]); ?></td>
		    </tr>
		    <tr>
		      <td colspan="2">Org nr/F. nr:</td><td colspan="4"><?php $a = "D21772"; printInput($a, $data[$a]); ?></td>
		    </tr>
		    <tr>
		      <td colspan="2">Navn:</td><td colspan="4"><?php $a = "D21771"; printInput($a, $data[$a]); ?></td>
		    </tr>
		    <tr>
		      <td colspan="2">Adresse:</td><td colspan="4"><?php $a = "D21773"; printInput($a, $data[$a]); ?></td>
		    </tr>
		    <tr>
		      <td colspan="2">Postnr:</td><td colspan="4"><?php $a = "D21774"; printInput($a, $data[$a]); ?></td>
		    </tr>
		    <tr>
		      <td colspan="2">Poststed:</td><td colspan="4"><?php $a = "D21775"; printInput($a, $data[$a]); ?></td>
		    </tr>
		  </table>
		</td>
	</tr>
</table>
<br />
<table border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td align="left" valign="top" colspan="2"><b>2.0 Tilh&oslash;rer foretaket et av unntakstilfellene, velg her:</b></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2">

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td align="left" valign="top" colspan="2">
<p>Velg den gruppen foretaket tilh&oslash;rer:<br />
<ul>
<li>Hovedregel: Gjelder alle foretak som ikke tilh&oslash;rer ett av unntakene nedenfor.</li>
<li>Unntak 1: Produksjon av elektrisitet ved vannkraft etc.</li>
<li>Unntak 2: Dyrking av jordbruks- og hagebruksvekster etc.</li>
<li>Unntak 3: Godstransport med over 50 &aring;rsverk.</li>
<li>Unntak 4: Andre transportforetak. Dette gjelder transportforetak med  n&aelig;ringskode: 60 Landtransport og r&oslash;rtransport, med unntak av 60.3  R&oslash;rtransport. 61 Sj&oslash;transport. 62 Lufttransport, med unntak av 62.3  Romfart.</li>
<li>Unntak 5: Foretak omfattet av helseforetaksloven.</li>
<li>Unntak 6: Fiske og fangst. Dette gjelder foretak som driver fiske og  fangst men som ikke er pliktige til &aring; betale arbeidsgiveravgift og  som kun skal innberette forskuddstrekk.</li>
</ul>
  	</td>
	</tr>
	<tr>
		<td align="left" valign="top"></td>
		<td align="left" valign="top">
		  <select name="D16522" size="1">
		    <option value="OF"<?php if ($data["D16522"] == "OF") print " selected"; ?>>Hovedregel - generelle n&aelig;ringer</option>
		    <option value="HF"<?php if ($data["D16522"] == "HF") print " selected"; ?>>Produksjon av elektrisitet ved vannkraft etc.</option>
		    <option value="DF"<?php if ($data["D16522"] == "DF") print " selected"; ?>>Dyrking av jordbruks- og hagebruksvekster etc.</option>
		    <option value="HU"<?php if ($data["D16522"] == "HU") print " selected"; ?>>Godstransport med over 50 &aring;rsverk</option>
		    <option value="OU"<?php if ($data["D16522"] == "OU") print " selected"; ?>>&oslash;vrige transportforetak</option>
		    <option value="HH"<?php if ($data["D16522"] == "HH") print " selected"; ?>>Foretak omfattet av helseforetaksloven</option>
		    <option value="FF"<?php if ($data["D16522"] == "FF") print " selected"; ?>>Fiske og fangst</option>
		  </select>
  	</td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2"></td>
	</tr>
	<tr>
		<td align="left" valign="top"><a alt="E&Oslash;S-reglene &aring;pner for at det kan gis et &aring;rlig fribel&oslash;p. For 2005  utgj&oslash;r dette kroner 270 000 per juridiske enhet. I f&oslash;rste termin oppgis hele fribel&oslash;pet. I de f&oslash;lgende terminer  overf&oslash;res rest fribel&oslash;p inntil fribel&oslash;pet er brukt opp. N&aring;r fribel&oslash;pet er brukt opp, oppgi 0.">Fribel&oslash;p/rest fribel&oslash;p fra forrige terminoppgave:</a></td>
		<td align="left" valign="top"><?php $a = "D16517"; printInput($a, $data[$a]); ?></td>
	</tr>
</table>
		</td>
	</tr>
</table>

<br />
<table border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td align="left" valign="top" colspan="2"><b>3.0 Arbeidsgiveravgift og forskuddstrekk per kommune</b></td>
	</tr>
	<tr>
		<td align="left" valign="top" colspan="2">
		
	<table border="1" cellspacing="0" cellpadding="0" width="100%">
    	<tr>
			<td>Komm nr.</td><td>Kommune navn</td><td>Sone</td><td>u/o</td><td>Avgiftsgrunnlag</td><td>&nbsp;</td><td>Sats</td><td>&nbsp;</td><td>Beregnet avgift</td><td><a alt="Oppgi samlet forskuddstrekk per kommune">Forskuddstrekk</a></td>
    	<tr>
<?php

$qry = "select * from arbeidsgiveravgift";
if ( $arb_rs = $db->Query($qry) )
{
	while ($arb_res = $db->NextRow( $arb_rs ))
	{
		$code = $arb_res["Code"];
		$arbGivAvgRefU[$code] = $arb_res["Percent"];
		$arbGivAvgRefO[$code] = $arb_res["Percent62"];
	}
}
$qry = "select * from kommune";
if ( $kom_rs = $db->Query($qry) )
{
	while ($kom_res = $db->NextRow( $kom_rs ))
	{
		$KommuneNumber = $kom_res["KommuneNumber"];
		$kommuneData[$KommuneNumber]["navn"] = $kom_res["KommuneName"];
		$kommuneData[$KommuneNumber]["sone"] = $kom_res["Sone"];
	}
}
if (is_array($data["D5950"]))
for ($i = 0; $i < count($data["D5950"]); $i++)
{
	$sone = $data["D3545"][$i];
	$sum_o = ($arbGivAvgRefO[$sone] * $data["D16509"][$i])/100;
	$sum_u = ($arbGivAvgRefU[$sone] * $data["D6047"][$i])/100;
	$kommnr = $data["D5950"][$i];
?>
	    <tr>
			<td rowspan="2"><?php $a = "D5950"; printInput($a . "[]", $data[$a][$i]); ?></td>
			<td rowspan="2"><?php $a = "D5932"; printInput($a . "[]", $data[$a][$i], $kommuneData[$kommnr]["navn"]); ?></td>
			<td rowspan="2"><?php $a = "D3545"; printInput($a . "[]", $data[$a][$i], $kommuneData[$kommnr]["sone"]); ?></td>
			<td>u</td>
			<td><?php $a = "D6047"; printInput($a . "[]", $data[$a][$i]); ?></td>
			<td>*</td>
			<td><?php print $arbGivAvgRefU[$sone]; ?> %</td>
			<td>=</td>
			<td><?php print $sum_u; ?></td>
			<td rowspan="2"><?php $a = "D6046"; printInput($a . "[]", $data[$a][$i], $sum_u + $sum_o); ?></td>
    	</tr>
	    <tr>
			<td>o</td>
			<td><?php $a = "D16509"; printInput($a . "[]", $data[$a][$i]); ?></td>
			<td>*</td>
			<td><?php print $arbGivAvgRefO[$sone]; ?></td>
			<td>=</td>
			<td><?php print $sum_o; ?></td>
	    </tr>
<?php
}
else
{
	$sone = $data["D3545"];
	$sum_o = ($arbGivAvgRefO[$sone] * $data["D16509"])/100;
	$sum_u = ($arbGivAvgRefU[$sone] * $data["D6047"])/100;
	$kommnr = $data["D5950"];
	
?>
	    <tr>
			<td rowspan="2"><?php $a = "D5950"; printInput($a, $data[$a]); ?></td>
			<td rowspan="2"><?php $a = "D5932"; printInput($a, $data[$a], $kommuneData[$kommnr]["navn"]); ?></td>
			<td rowspan="2"><?php $a = "D3545"; printInput($a, $data[$a], $kommuneData[$kommnr]["sone"]); ?></td>
			<td>u</td>
			<td><?php $a = "D6047"; printInput($a, $data[$a]); ?></td>
			<td>*</td>
			<td><?php print $arbGivAvgRefU[$sone]; ?> %</td>
			<td>=</td>
			<td><?php print $sum_u; ?></td>
			<td rowspan="2"><?php $a = "D6046"; printInput($a, $data[$a], $sum_u + $sum_o); ?></td>
    	</tr>
	    <tr>
			<td>o</td>
			<td><?php $a = "D16509"; printInput($a, $data[$a]); ?></td>
			<td>*</td>
			<td><?php print $arbGivAvgRefO[$sone]; ?></td>
			<td>=</td>
			<td><?php print $sum_o; ?></td>
	    </tr>
<?php
}
?>
	    </tr>
			<td colspan="3">Sum avgiftsgrunnlag under 62 &aring;r</td>
			<td>u</td>
			<td><?php $a = "D6051"; printInput($a, $data[$a]); ?></td>
			<td rowspan="2" colspan="4"></td>
			<td>&nbsp;</td>
    	<tr>
	    </tr>
			<td colspan="3">Sum avgiftsgrunnlag over 62 &aring;r</td>
			<td>o</td>
			<td><?php $a = "D16510"; printInput($a, $data[$a]); ?></td>
			<td>&nbsp;</td>
    	<tr>
	    </tr>
			<td colspan="3">Rest fribel&oslash;p</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="4"></td>
			<td><?php $a = "D21169"; printInput($a, $data[$a]); ?></td>
    	<tr>
	    </tr>
			<td colspan="3"Arbeidsgiveravgift</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="4"></td>
			<td><?php $a = "D223"; printInput($a, $data[$a]); ?></td>
    	<tr>
	</table>
  		</td>
	</tr>
</table>
<br/>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td align="left" valign="top" colspan="2">
		Spesielle grupper<br/>
			  <table border="1">
			    <tr>
			      <td><a alt="For arbeidstakere utsendt til Norge fra USA eller Canda, og som faller  inn under n&aelig;rmere angitte vilk&aring;r i Sosialdepartementes &aring;rlige  forskrift om avgift for slike arbeidstakere, skal det betales  arbeidsgiveravgift etter n&aelig;rmere angitt sats dersom det foreligger  avgiftsplikt etter folketrygdelovens &sect; 23-2.">Arbeidstakere utsendt til Norge fra USA og Canada</a></td>
			      <td>Avgiftsgrunnlag:</td>
			      <td><?php $a = "D16518"; printInput($a, $data[$a]); ?></td>
			      <td>* sats 7%</td>
			      <td>=</td>
			      <td><?php $a = "D6049"; printInput($a, $data[$a]); ?></td>
			    </tr>
			    <tr>
			      <td><a alt="For sj&oslash;menn som er ansatt p&aring; norsk skip i utenriksfart som er  registrert i det ordin&aelig;re norske skipsregisteret, skal arbeidsgiveren  betale avgift med et fast bel&oslash;p per m&aring;ned uten hensyn til antall  arbeidsdager i m&aring;neden, s&aring; sant sj&oslash;mannen er medlem i trygden etter  lov om folketrygd &sect; 2-6. Det er unntak for personer som er ansatt i  tjeneste hos utenlandsk arbeidsgiver som driver med n&aelig;ringsvirksomhet  om bord. Bel&oslash;pet fastsettes &aring;rlig i forskrift gitt av  Sosialdepartementet.">Visse sj&oslash;menn medlem i trygden etter folketrygdlovens &sect; 2-6</a></td>
			      <td><a alt="Antall m&aring;neder per arbeidstaker denne terminen summert:">Antall m&aring;neder</a>:</td>
			      <td><?php $a = "D16519"; printInput($a, $data[$a]); ?></td>
			      <td>* sats kr. 270</td>
			      <td>=</td>
			      <td><?php $a = "D16520"; printInput($a, $data[$a]); ?></td>
			    </tr>
			    <tr>
			      <td><a alt="Det skal betales ekstra arbeidsgiveravgift av den del av  avgiftspliktige ytelser som for den enkelte mottaker overstiger 16  ganger folketrygdens gjennomsnittlige grunnbel&oslash;p.">Ekstra arbeidsgiveravgift av ytelser over 16 x folketrygdens  grunnbel&oslash;p (G)</a></td>
			      <td>Avgiftsgrunnlag:</td>
			      <td><?php $a = "D16521"; printInput($a, $data[$a]); ?></td>
			      <td>* sats 12,5 %</td>
			      <td>=</td>
			      <td><?php $a = "D6050"; printInput($a, $data[$a]); ?></td>
			    </tr>
			    <tr>
			      <td><a alt=""></a></td>
			      <td>Avgiftsgrunnlag:</td>
			      <td colspan="3">&nbsp;</td>
			      <td><?php $a = "D2903"; printInput($a, $data[$a]); ?></td>
			    </tr>
			  </table>
		</td>
	</tr>
<?php
function printInput($a, $v, $correctValue = "null")
{
	if ($correctValue == "null")
		print "<input type=\"text\" name=\"" . $a . "\" value=\"" . $v . "\">";
	else if ($correctValue != $v)
		print "<input type=\"text\" name=\"" . $a . "\" style=\"color='red';\" value=\"" . $v . "\"> (riktig verdi er " . $correctValue . ")";
	else
		print "<input type=\"text\" name=\"" . $a . "\" value=\"" . $v . "\">";
}
?>
</table>
<input type="submit" name="Save" value="Lagre"/>
</form>