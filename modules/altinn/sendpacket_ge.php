<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** $Id: sendpacket_ge.php,v 1.3 2005/05/02 01:37:45 jan Exp $
**
** Developed by Gunnar Skeid (gunnar@actra.no)
** Modified by Geir Eliassen (geir@actra.no)
**
** Handles package editing.
** $Log: sendpacket_ge.php,v $
** Revision 1.3  2005/05/02 01:37:45  jan
** Added id and log headers for CVS
**
****************************************************************************/
    require_once '_include/class_lodo.php';
    require_once '_include/class_layout.php';
    require_once '_include/class_database.php';
    require_once '_include/class_mva.php';
    require_once '_include/class_config.php';
    require_once '_include/class_package_ge.php';
    require_once '_include/class_schema.php';

    
    
	require_once "_include_proxy/class_xmlformat.php";
	require_once "_include_proxy/class_skjemamaker.php";
	require_once "_include_proxy/class_childkeeper.php";
	require_once "_include_proxy/class_xmlskjema.php";
	
	
  $xsd = "";
	
	function onlyNumbers($num)
	{
		$len = strlen($num);
		for($i = 0; $i < $len; $i++)
		{
			$s = substr($num, $i, 1);
			print "S: " . $s . "<br>\n";
			if ($s == '0' || $s == '1' || $s == '2' || $s == '3' || $s == '4' || $s == '5' || $s == '6' || $s == '7' || $s == '8' || $s == '9')
				$ret = $ret . $s;
		}
		return $ret;
	}
	function numbersDash($num)
	{
		$len = strlen($num);
		for($i = 0; $i < $len; $i++)
		{
			$s = substr($num, $i, 1);
			if ($s == '0' || $s == '1' || $s == '2' || $s == '3' || $s == '4' || $s == '5' || $s == '6' || $s == '7' || $s == '8' || $s == '9' || $s == '-' )
				$ret = $ret . $s;
		}
		return $ret;
	}
	
    $myPortal = "prod";

    $lodo = new lodo();
    $layout = new Layout( $lodo );
    $db = new Db( $lodo );

    //print ("Anh fagsystemtype:". $_REQUEST['fagsystemtype']);

    if ( !$db->Connect() ) {
        $layout->PrintError("Kunne ikke koble til databasen.");
        die();
    }

    if ( $lodo->inLodo ) {
        includeinc('top');
        includeinc('left');
    }

    $mva = new Mva( $db );
    $config = new Config( $db );

    $package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

    $packageId = $_REQUEST['packetid'];
    $schemacontrol = $_REQUEST['schemacontrol'];

    if ( !is_numeric($packageId) ) {
        $layout->PrintError("Kunne ikke laste pakken, for det ble ikke oppgitt noe referanse til den.");
        $db->Disconnect();
        die();
    }

    if ( !$package->LoadPackage( $packageId ) )
    {
        $layout->PrintError("En feil oppstod under lasting av pakken.");
        $db->Disconnect();
        die();
    }

    /* SEND TO ALTINN */
    if ($_REQUEST['send'] <> '')
    {
// close curl resource, and free up system resources
?>
<h1>AltInn sending</h1>
<p>Hvis det kommer opplysninger om at noen variabler mangler eller er feil betyr det at du m&aring; sjekke disse n&aring;r du logger deg inn i Altinn.</p>
<?php

        /* Loop through the schemas */
        $sqlStr = 'SELECT skjema.instance_id, skjema.schematype, skjema.schemarevision, packet.termin, packet.year, packet.ts_created' .
                ' FROM altinn_schema AS skjema INNER JOIN altinn_packet AS packet ON (skjema.packet_id = packet.packet_id)' .
                ' WHERE packet.packet_id=' . $packageId;
        if ( $rs = $db->Query( $sqlStr ) )
        {
            while ( $row = $db->NextRow( $rs ) )
            {
                $schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $row['year']);
                $xsd = "melding-" . $row['schematype'] . "-" . $row['schemarevision'] . ".xsd";

                if ($schemacontrol=='N')
                    $schema->LoadSchema( $row['instance_id'] );
                else {
                    switch ($row['schematype']) {
                        case 212:
                            //print ("Anh 212:". "br");
                            $schema->LoadSchema( $row['instance_id'], $_REQUEST);
                            break;
                        default: break;
                    }//switch
                }
                //$schema->LoadSchema( $row['instance_id'] );

                $sendPost = $schema->ToXML( $row['year'], $row['termin'], array('packageID' => $packageId,'sendersRef' => $row['ts_created'], 'parentsRef' => $_REQUEST["parentRef"], 'useProxy' => true, 'portal' => $myPortal));
                //print ("Anh xml: ".$sendPost. "<br>");

            }
            $db->EndQuery($rs);
        }

//echo("POST: $sendPost\n\n");

$sendpostArr1 = split("&", $sendPost);
foreach ($sendpostArr1 as $items)
{
	list($key, $value) = split("=", $items);
	$sendpostArr[urldecode($key)] = urldecode($value);
}

global $_SETUP;
	
	/* --- Make the xml document with input data --- */
	$conn = mysqli_connect( $_SETUP['DB_SERVER'][0], $_SETUP['DB_USER'][0], $_SETUP['DB_PASSWORD'][0], $_SETUP['DB_NAME'][0] );
	if ($_REQUEST["slettaltfrafor"] == "1")
	{
		$query = "TRUNCATE TABLE `altinn_tempxml`;";
		mysqli_query($conn, $query);
	}
	$schemaMaker= new skjemaMaker(array('fagsystemid' => $sendpostArr["fagsystemID"], 'db' => $db, 'filename' => $_SETUP['HOME_DIR'] . "/code/lodo/altinn/skjemaer/" . $xsd));
	
	//print ("Anh proxy data:".$_REQUEST["data"]);
	$schemaMaker->addDataAsString($sendpostArr["data"]);
	$schemaMaker->makeSkjema();
	// $inputData["orgnr"] = $this->config->GetConfig($this->config->TYPE_ORGNO);
	$inputData["orgnr"] = $sendpostArr["orgnr"];
	$inputData["batchId"] = numbersDash($sendpostArr["batchId"]);
	$inputData["systemID"] = $sendpostArr["systemID"];
	$inputData["sendersRef"] = $sendpostArr["sendersRef"];
	$query = "select * from altinn_tempxml order by Altinn_tempxmlID ASC LIMIT 0 , 1";
	$buffer = mysqli_query($conn, $query);
	$data = mysqli_fetch_array($buffer);
	
	$inputData["parentsRef"] = $data["sendersRef"];

	
// Manuell XML kode:
$envelopeStart = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>
<DataBatch schemaVersion=\"1.1\" batchId=\"" . $inputData["batchId"] . "\" enterpriseSystemId=\"" . $inputData["systemID"] . "\" receiptType=\"OnDemand\" receiptUrl=\"http://213.236.237.168:8080/axis/AltinnReceipt.jws\"><DataUnits>";
$envelopeEnd = "</DataUnits></DataBatch>";
	
	$schemaMaker->makeDataUnit($inputData);
	// $schemaMaker->makeEnvelope($inputData);
	$myXML = $schemaMaker->getXML();
	$sp1 = strpos($myXML, "<DataUnit");
	$myXML = substr($myXML, $sp1);
	// print "XML:\n" . $myXML . "::XML";
	// print ("Geir ParticipantID:". $inputData["orgnr"]);
if ($_REQUEST["ikketameddette"] != "1")
{
	includelogic("report/arbeidsgiveravgift/savetable");
	$myTab = new SaveTable("altinn_tempxml");
	$myTab->set("xmlcode", $myXML);
	$myTab->set("sendersRef", $inputData["sendersRef"]);
	$sp1 = strpos($myXML, "blankettnummer");
	$sp1 = strpos($myXML, "\"", $sp1) + 1;
	$sp2 = strpos($myXML, "\"", $sp1 +2);
	$blankettnr = substr($myXML, $sp1, $sp2 - $sp1);
	$sp1 = strpos($myXML, "tittel");
	$sp1 = strpos($myXML, "\"", $sp1) + 1;
	$sp2 = strpos($myXML, "\"", $sp1 +2);
	$tittel = substr($myXML, $sp1, $sp2 - $sp1);
	$myTab->set("tittel", $blankettnr . " " . $tittel);
	$myTab->save();	
}	
	// print "XML:\n" . $myXML;
if ($_REQUEST["sendNow"] == "1")
{
	global $_SETUP;
	$sluttXML = $envelopeStart;
	$query = "select * from altinn_tempxml";
	$buffer = mysqli_query($conn, $query);
	while ($data = mysqli_fetch_array($buffer))
	{
		$sluttXML .= $data["xmlcode"];
	}
	$sluttXML .= $envelopeEnd;
	
	if ($_REQUEST["portal"] == "")
		$sendPost = "passord=" . $sendpostArr["passord"] . "&xml=" . urlencode( $sluttXML );
	else
		$sendPost = "passord=" . $sendpostArr["passord"] . "&portal=" . $sendpostArr["portal"] . "&xml=" . urlencode( $sluttXML );
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://213.236.237.168:8080/altinn/servlet/AltInnServlet");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $sendPost);
  die ("Data not sent! - Stopping here, we're just debugging. -- XML: " . $sendPost);
	$retur = curl_exec($ch);

	if ( strncmp($result,'Status: OK', strlen('Status: OK')) )
	print $retur;
	curl_close($ch);

	
        /* Check if it was sent ok */
        if ( !strncmp($result,'Status: OK', strlen('Status: OK')) )
        {
            /* Sending was ok, update the status of the package */
            $sqlStr = "UPDATE altinn_packet SET status=" . $package->PACKAGESTATUS_SENTOK . ",ts_modified='" . time() . "' WHERE packet_id=" . $packageId;
            if (!$db->Query( $sqlStr ))
            {
                $layout->PrintWarning("Pakken ble sendt, men fikk ikke oppdatert status p&aring; pakken i databasen. V&aring;r vennlig &aring; kontakte support og be dem sjekke status p&aring; pakkenummer " . $packageId . ".");
            }
            else if (1 == 0){
                $str = 'Location: ' . $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' ) . "\n";
                $str = str_replace( '&amp;', '&', $str );
                header( $str );
                $db->Disconnect();
                die();
            }
        }
        elseif (!strncmp($result,'Status: AllreadyExists', strlen('Status: AllreadyExists')))
        {
            $layout->PrintWarning("Skjemaet ble ikke sent til AltInn fordi den allerede har blitt sendt dit tidligere.");
        }
        else
        {
            $layout->PrintWarning("Skjemaet ble ikke sent til AltInn pga en feil. Feilen som ble oppgitt var: " . $result);
            print "XML Kode:\n" . htmlentities($sluttXML) . "\nXML Slutt!";
        }
}

    if ( $package->package['status'] > 0 )
    {
        $layout->PrintError("Du har ikke lov til &aring; sende en pakke som allerede har blitt fors&oslash;t sendt.");
        $db->Disconnect();
        die();
    }
}

?>
<p><a href="<?php echo($_SERVER['SCRIPT_NAME'])?>?t=altinn.index">Tilbake til Altinn-skjemaer</a></p>

<?php
    $layout->PrintFoot(  );

    $db->Disconnect();
?>
