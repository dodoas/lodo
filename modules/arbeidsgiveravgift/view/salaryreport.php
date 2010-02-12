<?

includemodel('salary/salaryreport');
$salaryreport = new salaryreport(array('year'=>$_REQUEST['report_Year'], 'employeeID'=>$_REQUEST['report_Employee']));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="generator" content="HTML Tidy for Mac OS X (vers 1st September 2004), see www.w3.org" />
    <title>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Lønns- og trekkoppgave for <? print $_REQUEST['report_Year'] ?></title>
    <style type="text/css">
    /*<![CDATA[*/
     body {
      color: #000000;
     }

     td { font-size: 7pt; font-family: Arial, Helvetica, SansSerif;}
    /*]]>*/
    </style>
</head>

<?
$factor = 4;
?>

<body>
    <table cellspacing="0" border="0" width="100%">
        <thead>
             <tr>
                <td><font size="4" face="Arial, Helvetica, SansSerif"><b>L&oslash;nns- og trekkoppgave for <? print $salaryreport->_year ?> </b></font><br><br><br><br><br></td>
                <td><font style="font-family: Arial, Helvetica, SansSerif; font-size: 7pt;">Regler om lønnsoppgaveplikt er gitt i ligningsloven kap. 6 med forskrifter.<br>
									Opplysningene i denne oppgaven vil bli benyttet ved forhåndsutfylling av<br>
									selvangivelsen for lønnstakere og pensjonister mv.	
                </font></td>
             </tr>
        </thead>
        <tbody>
             <tr>
                <td width="<? print $factor * 79 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 79 ?>" /><br /></td>
                <td width="<? print $factor * 80?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 80 ?>" /><br /></td>
             </tr>
    </table>
    <table cellspacing="0" border="1" width="100%">
        <tbody>
             <tr>
                <td width="<? print $factor * 20 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 20 ?>" /><br /></td>
                <td width="<? print $factor * 21 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 21 ?>" /><br /></td>
                <td width="<? print $factor * 21 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 21 ?>" /><br /></td>
                <td width="<? print $factor * 21 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 21 ?>" /><br /></td>
                <td width="<? print $factor * 21 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 21 ?>" /><br /></td>
                <td width="<? print $factor * 20 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 20 ?>" /><br /></td>
                <td width="<? print $factor * 5  ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 5  ?>" /><br /></td>
                <td width="<? print $factor * 20 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 20 ?>" /><br /></td>
                <td width="<? print $factor * 21 ?>"><img src="/img/blank.gif" height="0" width="<? print $factor * 21 ?>" /><br /></td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td height="11" align="left" colspan="4" rowspan="2" valign="top" height="100">Arbeidsgiverens (oppdragsgiverens) navn og adresse<br />
                <? print $salaryreport->_reportHash['company']['VName'] ?><br />
                <? print $salaryreport->_reportHash['company']['VAddress'] ?><br />
                <? print $salaryreport->_reportHash['company']['VZipCode']." ".$salaryreport->_reportHash['company']['VCity'] ?><br />
                <br />
                <br />

                </td>
                <td align="left" colspan="3">Organisasjonsnummer</td>
                <td align="left" colspan="2"><? print $salaryreport->_reportHash['company']['OrgNumber'] ?><br /></td>
            </tr>
            <tr>
                <td align="left" colspan="3">Kontorkommune (nummer og navn)</td>
                <td align="left" colspan="2"><? print $salaryreport->_reportHash['company']['CompanyMunicipality']." ".$salaryreport->_reportHash['company']['CompanyMunicipalityName'] ?><br /></td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td height="11" align="left" colspan="4" rowspan="4" valign="top" >Arbeidstakerens (mottakerens) navn og adresse<br />
                <? print $salaryreport->_reportHash['account']['AccountName'] ?><br />
                <? print $salaryreport->_reportHash['account']['Address'] ?><br />
                <? print $salaryreport->_reportHash['account']['ZipCode']." ".$salaryreport->_reportHash['account']['City'] ?><br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />
                <br />

                </td>
                <td align="left" colspan="3">Fødselnummer (11siffer)</td>
                <td align="left" colspan="2"><font style="font-size: 6pt;"><br /></font><? print $salaryreport->_reportHash['account']['SocietyNumber']?><font style="font-size: 6pt;"><br />&nbsp;</font></td>
            </tr>

            <tr>
                <td align="left" colspan="3">Skattekommune (nummer og navn)</td>
                <td align="left" colspan="2"><font style="font-size: 6pt;"><br /></font><? print $salaryreport->_reportHash['account']['KommuneNumber']." ".$salaryreport->_reportHash['account']['KommuneName'] ?><font style="font-size: 6pt;"><br />&nbsp;</font></td>
            </tr>
            <tr>
                <td align="left" colspan="5" valign="top">
                	<table width="100%" cellpadding="0" cellspacing="0">
			            <tr>
            			    <td align="left" valign="top" width="70%">
                Har arbeidstakeren vært ansatt gjennom hele året?<br />
                Dersom nei, oppgi tidsrom. Ved ansettelse<br />
                i atskilte perioder, oppgis antall dager<br /><br />
                <?php
function NorskDato($dato)
{
	$maaned["01"] = "januar";
	$maaned["02"] = "februar";
	$maaned["03"] = "mars";
	$maaned["04"] = "april";
	$maaned["05"] = "mai";
	$maaned["06"] = "juni";
	$maaned["07"] = "juli";
	$maaned["08"] = "august";
	$maaned["09"] = "september";
	$maaned["10"] = "oktober";
	$maaned["11"] = "november";
	$maaned["12"] = "desember";
	list($y, $m, $d) = split("-", $dato);
	return $d . ". " . $maaned[$m] . " " . $y;
}
                
 					if($salaryreport->_reportHash['account']['WorkedWholeYear'] != 1)
 					{ 
 						if ($salaryreport->_reportHash['account']['WorkPercent'] == 100)              
 						{
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? print NorskDato($salaryreport->_reportHash['account']['WorkStart']) . " - " . NorskDato($salaryreport->_reportHash['account']['WorkStop']); 
 						}
 						else
 						{
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<? print round($salaryreport->_reportHash['account']['WorkedDays']) ." dager"; 
 						}
					}
                ?>
                			</td>
	           			    <td align="right" valign="top" width="30%">
                <? if($salaryreport->_reportHash['account']['WorkedWholeYear'] == 1) { print "Ja [ X ], Nei [&nbsp;&nbsp;&nbsp;]"; } else { print "Ja [&nbsp;&nbsp;&nbsp;], Nei [ X ]"; } ?>
                			</td>
                		</tr>
                	</table>
                </td>
            </tr>

            <tr>
                <td align="left" colspan="3" valign="top" >
                Endringsoppgave. Fyll bare ut endring (økning<br />
                eller reduksjon) i forhold til tidligere oppgave &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;&nbsp;]
                </td>
                <td align="left" colspan="2" valign="top" >
                Oppgaven gjelder for sjøfolk med inntekt om<br />
                bord som gir rett til særskilt fradrag for sjøfolk  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; [&nbsp;&nbsp;&nbsp;]</td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td height="11" align="left" valign="top" colspan="2">
                <strong>111-A</strong><br />
                Lønn, honorarer mv<br />(Post 2.1.1 i selvangivelsen)<br />
                </td>
                <td align="left" valign="top" >
                <strong>112-A</strong><br />
                Trekkpliktige naturalytelser<br />(Post 2.1.1 i selvang.)
                </td>
                <td align="left" valign="top" >
                <strong>116-A</strong><br />
                Skattepliktig del av ulyk-<br />kesforsikring og yrkes-<br />
                skadeforsikring (mer-<br />premie) mv (Post 2.1.1<br />og/ev 3.2.2 i selvang.)
                </td>
                <td align="left" valign="top" >
                <strong>OOO</strong><br />
                Feriepengegrunnlag<br />
                <br />
                <br />
                <br />
                Føres ikke i selvangivelsen
                </td>
                <td align="left" valign="top" >
                <strong>313</strong><br />
                Underholdsbidrag etter pålegg fra Trygdeetatens
                Innkrevingssentral<br />
                <br />
                Føres ikke i selv- angivelsen
                </td>
                <td align="left" valign="top" colspan="2">
                <strong>316</strong><br />
                Underholdsbidrag etter pålegg fra Trygdeetatens
                Innkrevingssentral (ikke
                fradragsberettiget)<br />
                Føres ikke i selvangivelsen
                </td>
                <td align="left" valign="top" >
                <strong>950</strong><br />
                Forskuddstrekk<br />
                <br />
                <br />
                Føres ikke i selvangivelsen
                </td>
            </tr>
            <tr>
                <td height="25" align="left" colspan="2"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['111-A']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['112-A']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['116-A']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['000']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['313']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left" colspan="2"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['316']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['950']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
            </tr>
            <tr>
                <td height="10" align="left" valign="top" colspan="2"><strong>211</strong><br />Pensjon i og utenfor arbeidsforhold<br />
                og livrenter i arbeidsforhold mv<br />(Post 2.2.2 i selvangivelsen)
                </td>
                <td align="left" valign="top" ><strong>311</strong><br />Fagforeningskontigent (Post 3.2.11 i selvangivelsen)</td>
                <td align="left" valign="top" ><strong>312</strong><br />Premie til pensjons-<br>ordning<br />(Post 3.2.12 i selvang.)</td>
                <td align="left" valign="top" ><strong>314</strong><br />Premie til fond og trygd (Ev post 3.2.2 i selvangivelsen)</td>
                <td align="left" valign="top" colspan="2"><strong>401</strong><br />Utbetaling mv til næringsdrivende</td>
                <td align="left" valign="top" colspan="2"><strong>711</strong><br />Trekkfri bilgodtgjørelse<br /><br /><br />Føres ikke i selvangivelsen</td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td height="30" align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['211']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left" valign="top" >Tidsrom:</td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['311']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['312']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['314']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left" colspan="2"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['401']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left"><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['711']['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?><br /></td>
                <td align="left" valign="top" >Antall km: <br /><? print $_lib['format']->Amount(array('value'=>$salaryreport->_reportHash['head']['711']['NumberInPeriod'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1')) ?></td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td height="540" align="left" valign="top"><strong>Kode</strong><br />
                <?
                //$bottomLineHash = $salaryreport->get_bottomLineHash();
		// print_r($bottomLineHash);
		if (is_array($salaryreport->_reportHash['body']))
                foreach($salaryreport->_reportHash['body'] as $LineNumber => $lineHash)
                {
                	if ($lineHash['sumLineCode'] != 0)
                    	print $lineHash['SalaryCode']."<br />";
                }
                ?>
                </td>
                <td align="left" valign="top" ><strong>Beløp</strong> (hele kr)<br />
                <?
		if (is_array($salaryreport->_reportHash['body']))
                foreach($salaryreport->_reportHash['body'] as $LineNumber => $lineHash)
                {
                	if ($lineHash['sumLineCode'] != 0)
                    print $_lib['format']->Amount(array('value'=>$lineHash['sumLineCode'], 'return'=>'value', 'decimals'=>'0', 'roundoff'=>'down', 'nonzero'=>'1'))."<br />";
                }
                ?>
                </td>
                <td align="left" colspan="9" valign="top"><strong>Tekst</strong> *(Beløp i denne kolonnen kommer eventuelt i tillegg til beløp under faste koder ovenfor)<br />
                <?
		if (is_array($salaryreport->_reportHash['body']))
                foreach($salaryreport->_reportHash['body'] as $LineNumber => $lineHash)
                {
                	if ($lineHash['sumLineCode'] != 0)
                	if ($lineHash['SalaryCode'] == "610" || $lineHash['SalaryCode'] == "613" || $lineHash['SalaryCode'] == "616" || $lineHash['SalaryCode'] == "619" || $lineHash['SalaryCode'] == "620"  || $lineHash['SalaryCode'] == "623"  || $lineHash['SalaryCode'] == "624" || $lineHash['SalaryCode'] == "626" || $lineHash['SalaryCode'] == "627" || $lineHash['SalaryCode'] == "628")
                    	print $lineHash['SalaryText'] . ", Antall d&oslash;gn: " . $lineHash['NumberInPeriod']."<br />";
                	elseif ($lineHash['SalaryCode'] == "614")
                    	print $lineHash['SalaryText'] . ", Antall dager: " . $lineHash['NumberInPeriod']."<br />";
                    else
                    {
                    	print $lineHash['SalaryText']."<br />";
                    }
                }
                ?>
                </td>
            </tr>

            <tr>
                <td height="28" align="left" colspan="10" valign="top" >
<strong>Om overføring av beløpene til selvangivelsen, se baksiden</strong><br />
Arbeidsgivere som sender lønns- og trekkoppgaver på maskinlesbart medium, skal utferdige blanketten i to eksemplarer (arbeidstakerens og
arbeidsgiverens eksemplar). Arbeidsgivere som sender lønns- og trekkoppgaver til skatteoppkreveren, skal utferdige blanketten i tre eksemplarer
(arbeidstakerens, skatteoppkreverens og arbeidsgiverens eksemplar).<br />
Dersom arbeidsgiveren kopierer lønns- og trekkoppgaven, skal alltid <b>orginalen</b> sendes arbeidstakeren.<br />
RF 1015B Fastsatt av Skattedirektoratet</td>
            </tr>
        </tbody>
    </table><!-- ************************************************************************** -->
</body>
</html>
