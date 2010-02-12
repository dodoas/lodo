<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Handles package editing.
****************************************************************************/
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package.php';
	require_once '_include/class_schema.php';

	$lodo = new lodo();
	$layout = new Layout( $lodo );
	$db = new Db( $lodo );

	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
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

	if ( $package->package['status'] > 0 )
	{
		$layout->PrintError("Du har ikke lov til å sende en pakke som allerede har blitt forsøkt sendt.");
		$db->Disconnect();
		die();
	}

		$sendPost = "schemaVersion=1.0&enterpriseSystemId=" . $config->GetConfig($config->TYPE_FAGSYSTEMID ) . "&batchId=" . $config->GetConfig($config->TYPE_ORGNO) . "-" . sprintf("%02d", $config->GetConfig($config->TYPE_BATCHSUBNO)) . "-" . $packageId . "&participantId=" . $config->GetConfig( $config->TYPE_ORGNO ) . "&passord=" . $config->GetConfig( $config->TYPE_PASSWORD );

		/* Do the schema */
		$sqlStr = 'SELECT schema.instance_id,schema.schematype,schema.schemarevision,packet.termin,packet.year ' .
				'FROM altinn_schema AS schema,altinn_packet AS packet ' .
				'WHERE schema.packet_id=packet.packet_id AND packet.packet_id=' . $packageId;
		if ( $rs = $db->Query( $sqlStr ) )
		{
			if ( $row = $db->NextRow( $rs ) )
			{
				$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $row['year']);
				$schema->LoadSchema( $row['instance_id'] );
				$xml = $schema->ToXML( $row['year'], $row['termin'] );
				$sendPost .= "&sendersReference=" . $row['instance_id'] . "&parentReference=" . $row['instance_id'] . "&data=" . urlencode( $xml['body'] );
			}
			$db->EndQuery($rs);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://konsulentvikaren.no:8080/altinn/servlet/AltInnServlet");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sendPost);
		$result = curl_exec($ch);
echo("<p>XML<hr><pre>" . $xml['body'] . "</pre>\n");
echo("<p>SEND<hr><pre>" . $sendPost . "</pre>\n");

	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
?>
<h1>AltInn sending</h1>

<strong>Type:</strong> <?php echo( $package->packageNames[ $package->package['packettype'] ] );?><br/>
<strong>År:</strong> <?php echo( $package->package['year'] );?><br/>
<strong>Termin:</strong> <?php echo( $terminItem );?> (<?php echo($mva->GetTerminItemName( $config->GetConfig( $config->TYPE_TERMIN ), $package->package['termin'] ));?>)<br/>
<strong>Opprettet:</strong> <?php echo( date("d/m Y", $package->package['ts_created']) )?><br/>
<?php
		/* Check if it was sent ok */
		$msg = "Skjemaet ble sendt til AltInn.";
		if ( !strncmp($result,'Status: OK', strlen('Status: OK')) )
		{
			/* Sending was ok, update the status of the package */
			$sqlStr = "UPDATE altinn_packet SET status=" . $package->PACKAGESTATUS_SENTOK . ",ts_modified='" . time() . "' WHERE packet_id=" . $packageId;
			if (!$db->Query( $sqlStr ))
			{
				$msg = "Pakken ble sendt, men fikk ikke oppdatert status på pakken i databasen. Vær vennlig å kontakte support og be dem sjekke status på pakkenummer " . $packageId . ".";
			}
		}
		elseif (!strncmp($result,'Status: AllreadyExists', strlen('Status: AllreadyExists')))
		{
			$msg = "Skjemaet ble ikke sent til AltInn fordi den allerede har blitt sendt dit tidligere.";
		}
		else
		{
			$msg = "Skjemaet ble ikke sent til AltInn pga en feil. Feilen som ble oppgitt var: " . $result;
		}

		curl_close($ch);
?>
<h2>Resultat</h2>
<p><?php echo($msg);?></p>
<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.termins' ));?>">Tilbake til terminoversikt</a></p>

<?php
	$db->Disconnect();
?>
