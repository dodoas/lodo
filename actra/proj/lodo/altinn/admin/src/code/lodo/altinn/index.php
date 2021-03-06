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
function toggleDir($newSort)
{
	global $_REQUEST;
	if ($_REQUEST["dir"] == "asc" && $newSort == $_REQUEST["criteria"])
		return "desc";
	else
		return "asc";
}
?>
<h1>Altinnn-skjemaer</h1>

<p>

<table>
<tr>
	<th><a href="<?php print $_SETUP['DISPATCH']; ?>t=altinn.index&criteria=rfname&dir=<?php print toggleDir("rfname"); ?>" style="text-decoration:none;">Skjema nr</a></th>
	<th><a href="<?php print $_SETUP['DISPATCH']; ?>t=altinn.index&criteria=name&dir=<?php print toggleDir("name"); ?>" style="text-decoration:none;">Beskrivelse</a></th>
	<th><a href="<?php print $_SETUP['DISPATCH']; ?>t=altinn.index&criteria=shortname&dir=<?php print toggleDir("shortname"); ?>" style="text-decoration:none;">Kortnavn</a></th>
</tr>
<?php
if ($_REQUEST["dir"] != "" && ($_REQUEST["dir"] == "asc" || $_REQUEST["dir"] == "desc"))
	$dir = $_REQUEST["dir"];
else
	$dir = "asc";
if ($_REQUEST["criteria"] != "" && ($_REQUEST["criteria"] == "rfname" || $_REQUEST["criteria"] == "name" || $_REQUEST["criteria"] == "shortname"))
	$crit = $_REQUEST["criteria"];
else
	$crit = "rfname";
$sqlStr = 'SELECT * FROM `altinnschemalist` WHERE active=1 order by ' . $crit . ' ' . $dir . ';';
if ( $rs = $db->Query( $sqlStr ) )
{
	while ( $row = $db->NextRow( $rs ) )
	{
?>
<tr>
	<td><a href="<? print $_SETUP['DISPATCH'] ?>t=<?php print $row["tvar"]; ?>&AltinnschemalistID=<?php print $row["AltinnschemalistID"]; ?>"><?php print $row["rfname"]; ?></a></td>
	<td><?php print $row["name"]; ?></td>
	<td><?php print $row["shortname"]; ?></td>
</tr>
<?php
	}
}
?>
</table>
<?php
	$layout->PrintFoot(  );

	$db->Disconnect();
?>
