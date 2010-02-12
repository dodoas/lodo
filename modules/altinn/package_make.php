<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)

Siden oppretter først en ny kladd av skjematypen $_REQUEST['packettype'] for den
valgte perioden.

Deretter får man opp et skjema for å sende inn og/eller slette (sendpacket_ge),
eller redigere (js redirect til package_list) eksisterende kladder.
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
        includeinc('top');
        includeinc('left');
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

        // print ("Anh:.".$packettype."<br>");
        $packageId = $package->CreateNewPackage( $packettype, $year, $terminItem );
    }

$pt = $package->package['packettype'];
$pd = $package->schemaDef[$pt][0]['number'];
print "<h3>" . utf8_decode($package->schemaNames[$pd]) . "</h3>";
?><br>
F&oslash;lgende skjema finnes fra f&oslash;r:
<ul>
<?php
	$conn = mysqli_connect( $_SETUP['DB_SERVER'][0], $_SETUP['DB_USER'][0], $_SETUP['DB_PASSWORD'][0], $_SETUP['DB_NAME'][0] );
	$query = "select * from altinn_tempxml";
	$buffer = mysqli_query($conn, $query);
	while ($data = mysqli_fetch_array($buffer))
	{
		print "<li>" . $data["tittel"] . "</li>\n";
	}	
?>
</ul>
<p>Hva &oslash;nsker du &aring; gj&oslash;re med denne pakken?</p>

<form name="mvaf" action="<?php print $lodo->LodoUrlGet('', $lodo->LODOURLTYPE_HREF, 'altinn.sendpacket_ge'); ?>" method="post">
<input type="hidden" name="packetid" value="<?php echo($packageId);?>"/>
<!--
Tilknyttes skjema:  <select name="parentRef" size="1">
    <option value="">-Ingen tilknytning.-</option>
<?php
$sqlStr = 'SELECT skjema.instance_id,skjema.schematype, packet.year, packet.ts_created , list.rfname, list.name, packet.ts_modified ' .
            'FROM altinn_schema AS skjema,altinn_packet AS packet , altinnschemalist as list ' .
            'WHERE skjema.packet_id=packet.packet_id AND list.fagsystemid = skjema.schematype AND (skjema.schematype=179 OR skjema.schematype=99 ) order by skjema.instance_id desc;';
$rs = $db->Query( $sqlStr );
while ( $row = $db->NextRow( $rs ) )
{
?>
    <option value="<?php print $row["ts_created"]."&".$row["schematype"]; ?>"><?php print $row["rfname"] . " " . $row["name"] . " (" . $row["year"] . ") - Opprettet: " . date("d.m.Y H:i:s", $row["ts_created"]); ?></option>
<?php } ?>
  </select>
-->
<br />
<input type="checkbox" name="sendNow" value="1"/>Send til altinn n&aring;.<br />
<input type="checkbox" name="ikketameddette" value="1"/>Send kun det som ligger i databasen. (ikke dette skjemaet <?php print utf8_decode($package->schemaNames[$pd]); ?>)<br />
<input type="checkbox" name="slettaltfrafor" value="1"/>Slett alt som ligger i databasen fra f&oslash;r.<br />
<input type="hidden" name="schemacontrol" value="N"/>
<input type="submit" name="send" value="Send til AltInn">

  <input type="button" name="edit" value="Rediger skjema" onClick="document.location='<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_list' ));?>&amp;year=<?php echo( $year );?>&amp;status=0&amp;packettype=<?php print $packettype; ?>&amp;termin=<?php print $terminItem; ?>'"/>

</form>

<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.index' ));?>">Tilbake til Altinn oversikt</a></p>

<?php
    $layout->PrintFoot(  );

    $db->Disconnect();
?>
