<?
/* Checks status on batches */
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package.php';
	require_once '_include/class_schema.php';
	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

	$lodo = new lodo();

	if (!$lodo->inLodo) {
		$lodo->lodoDbHost = 'localhost';
		$lodo->lodoDbUsername = 'konsulentvikaren';
		$lodo->lodoDbPassword = 'yFKVuJgh';
		$lodo->lodoDbPassword = 'konsulentvikaren0';
	}
	$layout = new Layout( $lodo );
	$db = new Db( $lodo );
//echo("$lodo->lodoDbHost''$lodo->lodoDbUsername''$lodo->lodoDbPassword''$lodo->lodoDbDatabase<br>\n");

	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}


	$mva = new Mva( $db );
	$config = new Config( $db );

	$sqlStr = "SELECT packet_id FROM altinn_packet WHERE status=1";
	if ( $rs = $db->Query( $sqlStr ) )
	{
		while ( $row = $db->NextRow( $rs ))
		{
			$packetFailed = false;
			$packetSuccess = false;

			$packageId = $row['packet_id'];
			$batchId = $config->GetConfig($config->TYPE_ORGNO) . "-" . sprintf("%02d", $config->GetConfig($config->TYPE_BATCHSUBNO)) . "-" . $packageId;

			$urlStr = 'batchId=' . urlencode($batchId) . '&enterpriseSystemId=' . urlencode($config->GetConfig($config->TYPE_FAGSYSTEMID)) . '&passord=' . urlencode($config->GetConfig($config->TYPE_PASSWORD));

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://konsulentvikaren.no:8080/altinn/servlet/AltinnBatchStatus");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $urlStr);
			$result = curl_exec($ch);

			$lines = split("\n", $result);

			$mode = 'start';
			foreach ($lines as $line)
			{
				if ( $mode == 'start' )
				{
					if (strstr($line,'BatchStatus = '))
					{
						if (!strstr($line,' OK')) {
							$packetFailed = true;
							break;
						}
						else {
							$mode = "inbatch";
						}
					}
				}
				elseif ( $mode == 'inbatch' )
				{
					if (strstr($line,'Unitstatus = '))
					{
						if (!strstr($line,' OK')) {
							$packetFailed = true;
							break;
						}
					}
					if (strstr($line,'  workflowReference='))
					{
						$packetSuccess = true;
						break;
					}
				}
			}

			/* Update status?? */
			$updateStr = "";
			$statusText = "BatchId $batchId ";
			if ( $packetFailed )
			{
				$statusText .= "set to failed.<br>\n";
				$updateStr = 'UPDATE altinn_packet SET status=3 WHERE packet_id=' . $packageId;
			}
			elseif ( $packetSuccess )
			{
				$statusText .= "set to success.<br>\n";
				$updateStr = 'UPDATE altinn_packet SET status=2 WHERE packet_id=' . $packageId;
			}
			else {
				$statusText .= "not changed.<br>\n";
			}

			/* Execute update sql str? */
			if ( $updateStr != '' )
			{
				$db->Query( $updateStr );
			}

			echo( $statusText );
		}
		$db->EndQuery( $rs );
	}
	$db->Disconnect();
?>
