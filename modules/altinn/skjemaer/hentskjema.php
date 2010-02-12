<?php
$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=974761076";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=942114184";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=971203420";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=974761246";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=840747972";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=981105516";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=937884117";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=971527757";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=974760673";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=970935657";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=971648198";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=960885406";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=971526920";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);

$GetURL = "http://w2.brreg.no/oppgaveregisteret/spesifikasjon_etat.jsp?aktoernr=874761532";
print "Henter: " . $GetURL . "\n";
printSchemaList($GetURL);


//set_time_limit(600);

function printSchemaList($url)
{
	$inputdata = join("", file($url));
	$mySubURL = "x";
	$oldsp = 0;
	$sp = 1;
	while ($mySubURL != "" && $sp > $oldsp)
	{
		$oldsp = $sp;
		$sp = strpos($inputdata, "<a href=\"spesifikasjon_skjema.jsp?skjemanr", $sp);
		$sp = strpos($inputdata, "\"", $sp) + 1;
		$sp2 = strpos($inputdata, "\"", $sp);
		$mySubURL = substr($inputdata, $sp, $sp2 - $sp);
		print "Henter: http://w2.brreg.no/oppgaveregisteret/" . $mySubURL . "\n";
		printForm("http://w2.brreg.no/oppgaveregisteret/" . $mySubURL);
	}
}

function printForm($murl)
{
	global $continue, $skjemalist;
	$tittel = getTitle($murl);
//	list($RfKode, $SkjemaNavn) = split(" ", html_entity_decode($tittel), 2);
//	list($RfTxt, $RfNr) = split("-", $RfKode, 2);
	$RfKode = $tittel;
	for($i = 0; $i < count($skjemalist); $i++)
		if ($skjemalist[$i] == $RfKode)
			$found = true;
//	if (!$found)
//		print "Fant ikke " . $RfKode . "<br>";
	
//	if ($RfNr > 1189)
	if (true)
	{
		$Url = getXMLLink(getXsdRef($murl));
		$xml = getXMLLink(getXMLRef($murl));
		
		// $XsdData = join("", file($Url));
		print "wget " . $Url . "\n";
		system("wget " . $Url);
		print "wget " . $xml . "\n";
		system("wget " . $xml);
		// $XMLData = join("", file($Url));
		
		$myPos = strpos($Url, "-") + 1;
		$myVal = substr($Url, $myPos);
		
		$myPos = strpos($myVal, ".");
		$myVal = substr($myVal, 0, $myPos);
		list($SkjemaNr, $Revisjon) = split("-", $myVal);
		
		
		print "Lagrer skjema nr: " .  $SkjemaNr . "\n";
	}
}
function getTitle($url)
{
	$inputdata = join("", file($url));
	$sp = strpos($inputdata, "<h3>Oversikt over spesifikasjoner for ") + strlen("<h3>Oversikt over spesifikasjoner for ");
	//$sp = strpos($inputdata, "&nbsp;", $sp) + 6;
	$sp2 = strpos($inputdata, "&nbsp;", $sp);
	// $sp2 = strpos($inputdata, "</h3", $sp);
	$myTitle = substr($inputdata, $sp, $sp2 - $sp);
	return $myTitle;
}
function getXMLLink($url)
{
	$inputdata = join("", file($url));
	$sp = strpos($inputdata, "http://ftp");
	//$sp = strpos($inputdata, "'", $sp) + 1;
	$sp2 = strpos($inputdata, "\"", $sp);
	$myURL = substr($inputdata, $sp, $sp2 - $sp);
	return $myURL;
}
function getXsdRef($url)
{
	$url_prefix = "http://w2.brreg.no/oppgaveregisteret/";
	$inputdata = join("", file($url));
	$sp = strpos($inputdata, "<a href=\"spesifikasjon_nedlasting_xml.jsp?spesifikasjonsnr=");
	$sp = strpos($inputdata, "\"", $sp) + 1;
	$sp2 = strpos($inputdata, "\"", $sp);
	$myURL = substr($inputdata, $sp, $sp2 - $sp);
	return $url_prefix . $myURL;
}
function getXMLRef($url)
{
	$url_prefix = "http://w2.brreg.no/oppgaveregisteret/";
	print "Henter: " . $url . "\n";
	$inputdata = join("", file($url));
	$sp = strpos($inputdata, "<a href=\"spesifikasjon_nedlasting_xml.jsp?spesifikasjonsnr=");
	$sp = strpos($inputdata, "<a href=\"spesifikasjon_nedlasting_xml.jsp?spesifikasjonsnr=", $sp + 5);
	$sp = strpos($inputdata, "\"", $sp) + 1;
	$sp2 = strpos($inputdata, "\"", $sp);
	$myURL = substr($inputdata, $sp, $sp2 - $sp);
	return $url_prefix . $myURL;
}
?>