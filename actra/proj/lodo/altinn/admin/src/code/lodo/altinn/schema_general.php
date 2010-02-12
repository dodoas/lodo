<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Geir Eliassen (geir@actra.no)
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
$sqlStr = 'SELECT * FROM `altinnschemalist` WHERE AltinnschemalistID=' . $_REQUEST["AltinnschemalistID"] . ';';
$rs = $db->Query( $sqlStr );
$row = $db->NextRow( $rs );

	$packettype = $row["AltinnschemalistID"];
	$myHeading = htmlentities($row["name"]);
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
<h1><?php print $myHeading; ?></h1>


<table>
<tr>
	<th>#</th>
	<th>&Aring;r</th>
	<th>Sendinger</th>
	<th>Kladder</th>
	<th></th>
</tr>
<?php
$sqlStr = ' SELECT `Period` FROM `accountperiod` ORDER BY `Period` ASC LIMIT 0, 1';
		$rs = $db->Query( $sqlStr );
		$row = $db->NextRow( $rs );
		list($fraArr, $tull) = split("-", $row[0]);
	$myCount = 0;	
	for($i = $fraArr; $i  < date("Y", time() + mktime(1+0,0,0,1,1,1970)); $i++)
	{
		$myCount++;
?>
<tr>
	<td><?php echo( $myCount );?></td>
	<td><?php echo( $i );?></td>
<?php
	$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status>0' .
		' AND year=' . $i .
		' AND packettype=' . $packettype;
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
	<td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.gpackages' ));?>&amp;year=<?php echo( $i );?>&amp;status=1&amp;packettype=<?php print $packettype; ?>">Vis</a>]</td>
<?php
	}
	else {
		echo('<td>&#160;</td>');
	}

	$sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId . 
		' AND status<1' .
		' AND year=' . $i .
		' AND packettype=' . $packettype;
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
	<td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_list' ));?>&amp;year=<?php echo( $i );?>&amp;status=0&amp;packettype=<?php print $packettype; ?>">Editer</a>]</td>
<?php
	}
	else {
		echo('<td>&#160;</td>');
	}
?>
	<td>[<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_make' ));?>&amp;year=<?php echo( $i ); ?>&amp;packettype=<?php print $packettype; ?>">Nytt skjema</a>]</td>

</tr>
<?php
	}
?>
</table>
<?php
	$layout->PrintFoot(  );

	$db->Disconnect();
?>
