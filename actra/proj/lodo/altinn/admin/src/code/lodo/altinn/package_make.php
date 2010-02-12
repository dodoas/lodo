<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Prints out MVA termins of the user in order to let the user do things
** with the termins, like sending a report to AltInn.
****************************************************************************/
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package_ge.php';
	require_once '_include/class_schema.php';
	global $MY_SELF; 
	$lodo = new lodo();
	$db = new Db( $lodo );
	$db->Connect();
	
	$layout = new Layout( $lodo );
	$config = new Config( $db );

	$year = $_REQUEST['year'];
	$packettype = $_REQUEST['packettype'];
	$status = $_REQUEST['status'];
	if ($_REQUEST['terminItem'] != "")
		$terminItem = $_REQUEST['terminItem'];
	else
		$terminItem = 0;

	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
?>
<h1>Lage skjemaer</h1>
<?php
	if ( !is_numeric( $packageId ) )
	{
?>
<p>F&oslash;lgende skjema har blitt laget:
<p><?php
		$status = 0;
		$year = $_REQUEST['year'];
		$terminItem = $_REQUEST['terminitem'];
		if ( !is_numeric($year) || !is_numeric($packettype) )
		{
			$layout->PrintError('Det ble ikke oppgitt termin eller pakketype for pakken.');
			$db->Disconnect();
			die();
		}
		$packageId = $package->CreateNewPackage( $packettype, $year, $terminItem );
	}

$pt = $package->package['packettype'];
$pd = $package->schemaDef[$pt][0]['number'];
print $package->schemaNames[$pd];
?><br>
<p>Hva &oslash;nsker du &aring; gj&oslash;re med denne pakken?</p>

<form name="mvaf" action="<?php print $MY_SELF; ?>" method="post">
<input type="hidden" name="packetid" value="<?php echo($packageId);?>"/>
<input type="hidden" name="t" value="altinn.sendpacket_ge"/>
<input type="submit" name="send" value="Send til AltInn">
</form>

<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.index' ));?>">Tilbake til Altinn oversikt</a></p>

<?php
	$layout->PrintFoot(  );

	$db->Disconnect();
?>
