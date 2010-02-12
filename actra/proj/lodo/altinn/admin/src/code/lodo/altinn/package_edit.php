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
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package.php';
	
	$lodo = new lodo();
	$db = new Db( $lodo );
	$db->Connect();
	
	$layout = new Layout( $lodo );
	$mva = new Mva( $db );
	$config = new Config( $db );

	$year = $_REQUEST['year'];
	$packettype = $_REQUEST['packettype'];
	$packageid = $_REQUEST['packageid'];
	$status = $_REQUEST['status'];
	if ($_REQUEST['termin'] == "")
		$terminItem = "0";
	else
		$terminItem = $_REQUEST['termin'];
		 
	$package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
?>
<h1>AltInn skjemaer</h1>
<strong>&Aring;r:</strong> <?php echo( $year );?><br/>

<?php
		$sqlStr = 'SELECT * FROM altinn_schema WHERE packet_id=' . $packageid . ';';
	if ( $rs = $db->Query($sqlStr) )
	{
?>
<table>
<tr>
	<th>ORID</th>
	<th>Verdi</th>
</tr>
<?php
		$row = $db->NextRow( $rs );
		$myDataArray = split("&", $row["data"]);
		foreach($myDataArray as $lines)
		{
			list($key, $value) =  split("=", $lines);
			$value = urldecode($value);
			$key = urldecode($key);
?>
<tr>
	<td><?php echo( $key );?></td>
	<td><?php echo( $value );?></td>
</tr>
<?php
		}
		$db->EndQuery( $rs );
?>
</table>
<?php
	}
?>
<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.index' ));?>">Tilbake til Altinn oversikt</a></p>

<?php
	$layout->PrintFoot(  );

	$db->Disconnect();
?>
