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

	$lodo = new lodo();
	$db = new Db( $lodo );
	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	$layout = new Layout( $lodo );
	$mva = new Mva( $db );
	$config = new Config( $db );

	/* Configure range for years. Default is the current year. */
	$yearLow = 2004;
	$yearHigh = date("Y");
	$year = $_REQUEST['year'];
	if ($year == '' || !is_numeric($year)) {$year = date("Y");}

	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
	}
?>
<h1>MVA-terminer</h1>

<h2>År</h2>

<p>
<?php
	for ($yearCounter = $yearLow; $yearCounter <= $yearHigh; $yearCounter++)
	{
		if ( $yearCounter == $year ) {echo('<strong>');}
?>
<a href="<?php echo($lodo->LodoUrlSelf( "", $lodo->LODOURLTYPE_HREF ));?>&amp;year=<?php echo($yearCounter);?>"><?php echo($yearCounter);?></a>
<?php
		if ( $yearCounter == $year ) {echo('</strong>');}
	}
?>
</p>

<?php
	$terminList = $mva->GetTerminListRange( $config->GetConfig( $config->TYPE_TERMIN ), mktime(0,0,0,1,1,$year), mktime(23,59,59,12,31,$year) );
?>
<table>
<tr>
	<th>#</th>
	<th>År</th>
	<th>Termin</th>
	<th>Sendinger</th>
	<th>Kladder</th>
	<th></th>
</tr>
<?php
	foreach ( $terminList as $key => $value )
	{
?>
<tr>
	<td><?php echo( $terminList[ $key ]['terminitem'] );?></td>
	<td><?php echo( $terminList[ $key ]['year'] );?></td>
	<td><?php echo( $terminList[ $key ]['terminname'] );?></td>
<?php
	$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status>0' .
		' AND year=' . $terminList[ $key ]['year'] .
		' AND termin=' . $terminList[ $key ]['terminitem'] .
		' AND termintype=' . $config->GetConfig( $config->TYPE_TERMIN );
	if ( $rs = $db->Query( $sqlStr ) )
	{
		if ( $row = $db->NextRow( $rs ) )
		{
			$packages = $row[0];
		}
		$db->EndQuery( $rs );
	}

	if ($packages > 0)
	{
		if ($packages < 2) {
			$txtSending = "sending";
		}
		else {
			$txtSending = "sendinger";
		}
?>
	<td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packages' ));?>&amp;terminitem=<?php echo( $terminList[ $key ]['terminitem'] );?>&amp;year=<?php echo( $terminList[ $key ]['year'] );?>&amp;status=1">Vis</a>]</td>
<?php
	}
	else {
		echo('<td>&#160;</td>');
	}

	$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status<1' .
		' AND year=' . $terminList[ $key ]['year'] .
		' AND termin=' . $terminList[ $key ]['terminitem'] .
		' AND termintype=' . $config->GetConfig( $config->TYPE_TERMIN );
	if ( $rs = $db->Query( $sqlStr ) )
	{
		if ( $row = $db->NextRow( $rs ) )
		{
			$packages = $row[0];
		}
		$db->EndQuery( $rs );
	}
	if ($packages > 0)
	{
		if ($packages < 2) {
			$txtSending = "kladd";
		}
		else {
			$txtSending = "kladder";
		}
?>
	<td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packages' ));?>&amp;terminitem=<?php echo( $terminList[ $key ]['terminitem'] );?>&amp;year=<?php echo( $terminList[ $key ]['year'] );?>&amp;status=0">Editer</a>]</td>
<?php
	}
	else {
		echo('<td>&#160;</td>');
	}
?>
	<td>[<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packagemva' ));?>&amp;terminitem=<?php echo( $terminList[ $key ]['terminitem'] );?>&amp;year=<?php echo( $terminList[ $key ]['year'] );?>">Nytt skjema</a>]</td>

</tr>
<?php
	}
?>
</table>
<?php
	$layout->PrintFoot(  );

	$db->Disconnect();
?>
