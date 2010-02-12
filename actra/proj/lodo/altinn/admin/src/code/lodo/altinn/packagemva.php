<?php
	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

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
	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

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

	$year = $_REQUEST['year'];
	$terminItem = $_REQUEST['terminitem'];

	$packageId = $_REQUEST['packageid'];
	/* No packetid = create new packet */
	if ( !is_numeric( $packageId ) )
	{
		$status = 0;
		$year = $_REQUEST['year'];
		$terminItem = $_REQUEST['terminitem'];
		if ( !is_numeric($year) || !is_numeric($terminItem) )
		{
			$layout->PrintError('Det ble ikke oppgitt termin for pakken.');
			$db->Disconnect();
			die();
		}

		$packageId = $package->CreateNewPackage( $package->PACKAGETYPE_MVA, $year, $terminItem );
	}
	/* Edit old packet */
	else
	{
		if ($_REQUEST['delete']<>'')
		{
			$sqlStr = 'DELETE FROM altinn_packet WHERE status=0 AND packet_id=' . $packageId;
			if ($db->Query($sqlStr))
			{
				$sqlStr = 'DELETE FROM altinn_schema WHERE packet_id=' . $packageId;
				$db->Query($sqlStr);

				$str = 'Location: ' . $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' ) . "\n";
				$str = str_replace( '&amp;', '&', $str );
				header( $str );
				$db->EndQuery( $rs );
				$db->Disconnect();
				die();
			}
			else
			{
				$layout->PrintWarning("Kunne ikke slette pakken!");
			}
		}

		/* Get status */
		$status = -1;
		$sqlStr = 'SELECT status FROM altinn_packet WHERE packet_id=' . $packageId;
		if ( $rsStatus = $db->Query( $sqlStr ) )
		{
			if ( $rowStatus = $db->NextRow( $rsStatus ) )
			{
				$status = $rowStatus['status'];
			}
			$db->EndQuery( $rsStatus );
		}

		if ($status == -1)
		{
			$layout->PrintError("Kunne ikke finne pakken.");
			$db->Disconnect();
			die();
		}

		$schemaInstance = $_REQUEST['schemainstance'];
		if ($schemaInstance == '') {$schemaInstance = 0;}
		if ( $_REQUEST['schemainstance'] <> '' )
		{
			$currentschemainstance = $_REQUEST['currentschemainstance'];

			/* Find schema instance */
			$sqlStr = 'SELECT schematype,schemarevision,packet_id FROM altinn_schema WHERE instance_id=' . $currentschemainstance;
			if ($rs = $db->Query( $sqlStr ))
			{
				if ( $row = $db->NextRow( $rs ))
				{
					/* If this is a draft we should ask if the user wants to send the packet to AltInn.
					 * This is handeled by another page, so we redirect there. */

					if ( $status == -1 ) {
						$layout->PrintError("Kunne ikke hente status på denne pakken.");
						$db->EndQuery( $rs );
						$db->Disconnect();
						die();
					}
					$schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $year);
					$schema->ReadSchemaForm();
					$schema->SaveSchema( $currentschemainstance, $row['packet_id'] );

					/* Continue */
					if ( $status == 0 )
					{
						if ( $_REQUEST['send']<>'' )
						{
							$str = 'Location: ' . $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.sendpacket' ) . '&amp;packetid=' . $row['packet_id'] . "\n";
							$str = str_replace( '&amp;', '&', $str );
							header( $str );
							$db->EndQuery( $rs );
							$db->Disconnect();
							die();
						}
						elseif ($_REQUEST['draft']<>'')
						{
							$str = 'Location: ' . $lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.termins' ) . "\n";
							$str = str_replace( '&amp;', '&', $str );
							header( $str );
							$db->EndQuery( $rs );
							$db->Disconnect();
							die();
						}
					}
				}
				$db->EndQuery( $rs );
			}
		}

		if ($_REQUEST['schemainstance'] < 0)
		{
echo("...<br>\n");
			$schemaInstance = $_REQUEST['currentschemainstance'];
			$schema = new Schema($db, $lodo, $config, $layout, 212, 3148, $year);
			$schema->LoadSchema( $schemaInstance );
			$schema->ToXML( $year, $termin );
		}
		else
		{
			if ( !$package->LoadPackage( $packageId ) )
			{
				$layout->PrintError("En feil oppstod under lasting av pakken.");
				$db->Disconnect();
				die();
			}
			$year = $package->package['year'];
			$terminItem = $package->package['termin'];
		}
	}

	$schemaInstanceId = $_REQUEST['schemainstance'];
	if (!is_numeric($schemaInstanceId)) {$schemaInstanceId = 0;}

	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
?>
<script>
<!--//
function AskDelete( )
{
	res = confirm("Er du sikker på at du vil slette dette skjemaet?");
	if ( res == true )
	{
		return( true );
	}
	return( false );
}
//-->
</script>

<h1>AltInn skjema</h1>
<strong>Type:</strong> MVA<br/>
<strong>År:</strong> <?php echo( $year );?><br/>
<strong>Termin:</strong> <?php echo( $terminItem );?> (<?php echo($mva->GetTerminItemName( $config->GetConfig( $config->TYPE_TERMIN ), $terminItem ));?>)<br/>

<style>
<!--
.m {
	background: #eeeeee;
}
-->
</style>

<table>
<tr>
	<th>Innhold</th>
</tr>
<tr valign="top">
<?php
	$schemaNumber = 0;
	$instanceNext = 0;
	$sqlStr = 'SELECT instance_id,schematype FROM altinn_schema WHERE packet_id=' . $package->package['packet_id'];
	if ( $rs = $db->Query( $sqlStr ) )
	{
		while ( $row = $db->NextRow( $rs ))
		{
			if ( $instanceNext == -1 )
			{
				$instanceNext = $row['schematype'];
			}
			if ( $schemaInstanceId < 1 ) {
				$schemaInstanceId = $row['instance_id'];
			}
			if ( $schemaInstanceId == $row['instance_id'] )
			{
				$schemaNumber = $row['schematype'];
				$instanceNext = -1;
			}
		}
		$db->EndQuery( $rs );
	}
?>
	<td>
<?php
	if ( $schemaNumber > 0 )
	{
		$schema = new Schema($db, $lodo, $config, $layout, $schemaNumber, $package->GetSchemaRevision($schemaNumber, $terminItem, $config->GetConfig($config->TERMIN_TYPE), $year));
		$schema->LoadSchema( $schemaInstanceId );

		/* Check if there is any other packets that has been sent */
		$hasBeenSent = false;
		$sqlStr = 'SELECT status FROM altinn_packet WHERE status<>0 AND termin=' . $terminItem . ' AND termintype=' . $config->GetConfig($config->TERMIN_TYPE) . ' AND year=' . $year;
		if ( $rs = $db->Query( $sqlStr ) )
		{
			if ( $row = $db->NextRow( $rs ))
			{
				$hasBeenSent = true;
				$schema->SetData( 5659, 3 );
			}
			$db->EndQuery( $rs );
		}

?>
	<h3><?php echo($schema->GetSchemaName()); ?></h3>
<form name="mvaf" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post">
<input type="hidden" name="schemainstance" value="<?php echo($instanceNext);?>"/>
<input type="hidden" name="currentschemainstance" value="<?php echo($schemaInstanceId);?>"/>
<input type="hidden" name="packageid" value="<?php echo($packageId);?>"/>
<input type="hidden" name="year" value="<?php echo($year);?>"/>
<input type="hidden" name="terminitem" value="<?php echo($terminItem);?>"/>

<?php echo($lodo->LodoUrlSelf( '', $lodo->LODOURLTYPE_FORM ));?>

<?php $schema->DisplaySchema( $status );?>
<br/><br/>
<input type="submit" name="send" value="Send skjema"<?php if ($status > 0) {?> disabled<?php }?>>
<input type="submit" name="draft" value="Lagre kladd"<?php if ($status > 0) {?> disabled<?php }?>>
<?php if ($status < 1) {?>&#160; &#160; &#160; <input type="submit" name="delete" value="Slett" onclick="return AskDelete();"><?php }?>
</form>
<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.termins' ));?>">Tilbake til terminoversikt</a></p>
<?php
	}
	else {
		$layout->PrintError('Fant ikke det aktuelle skjemaet i pakken.');
	}
?>
	</td>
</tr>
</table>

<?php
	$db->Disconnect();
?>
