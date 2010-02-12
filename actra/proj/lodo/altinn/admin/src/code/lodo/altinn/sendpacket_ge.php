<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
** Modified by Geir Eliassen (geir@actra.no)
**
** Handles package editing.
****************************************************************************/
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package_ge.php';
	require_once '_include/class_schema.php';

	$lodo = new lodo();
	$layout = new Layout( $lodo );
	$db = new Db( $lodo );

	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
	
	$mva = new Mva( $db );
	$config = new Config( $db );

	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

	$packageId = $_REQUEST['packetid'];

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

		$sendPost = "schemaVersion=1.0&enterpriseSystemId=" . $config->GetConfig($config->TYPE_FAGSYSTEMID ) . "&batchId=$packageId&participantId=" . $config->GetConfig( $config->TYPE_ORGNO ) . "&passord=" . $config->GetConfig( $config->TYPE_PASSWORD );

		/* Loop through the schemas */
		$sqlStr = 'SELECT schema.instance_id,schema.schematype,schema.schemarevision,packet.termin,packet.year ' .
				'FROM altinn_schema AS schema,altinn_packet AS packet ' .
				'WHERE schema.packet_id=packet.packet_id AND packet.packet_id=' . $packageId;
		if ( $rs = $db->Query( $sqlStr ) )
		{
			while ( $row = $db->NextRow( $rs ) )
			{
				$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $row['year']);
				$schema->LoadSchema( $row['instance_id'] );
				$xml = $schema->ToXML( $row['year'], $row['termin'] );
				$sendPost .= "&sendersReference=" . $row['instance_id'] . "&parentReference=" . $row['instance_id'] . "&data=" . urlencode( $xml['body'] );
			}
			$db->EndQuery($rs);
		}

//echo("POST: $sendPost\n\n");
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, "http://konsulentvikaren.no:8080/altinn/servlet/AltInnServlet");
//		curl_setopt($ch, CURLOPT_URL, "http://localhost/AltInn/dummy.php");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sendPost);
		$result = curl_exec($ch);
//		curl_close($ch);
// print $sendPost;
//echo("<html><body>RES: $result\n</body></html>");
		/* Check if it was sent ok */
		if ( !strncmp($result,'Status: OK', strlen('Status: OK')) )
		{
			/* Sending was ok, update the status of the package */
			$sqlStr = "UPDATE altinn_packet SET status=" . $package->PACKAGESTATUS_SENTOK . ",ts_modified='" . time() . "' WHERE packet_id=" . $packageId;
			if (!$db->Query( $sqlStr ))
			{
				$layout->PrintWarning("Pakken ble sendt, men fikk ikke oppdatert status p� pakken i databasen. V�r vennlig � kontakte support og be dem sjekke status p� pakkenummer " . $packageId . ".");
			}
			else {
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
		}

		curl_close($ch);

	}

	if ( $package->package['status'] > 0 )
	{
		$layout->PrintError("Du har ikke lov til � sende en pakke som allerede har blitt fors�kt sendt.");
		$db->Disconnect();
		die();
	}

	$layout->PrintHead( "AltInn" );

?>
<h1>AltInn sending</h1>
<p><a href="<?php echo($_SERVER['SCRIPT_NAME'])?>?t=altinn.index">Tilbake til Altinn-skjemaer</a></p>

<?php
	$db->Disconnect();
?>
