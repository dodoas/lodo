<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** $Id: sendpacket_ge.php,v 1.3 2005/05/02 01:37:45 jan Exp $
**
** Developed by Gunnar Skeid (gunnar@actra.no)
** Modified by Geir Eliassen (geir@actra.no)
**
** Handles package editing.
** $Log: sendpacket_ge.php,v $
** Revision 1.3  2005/05/02 01:37:45  jan
** Added id and log headers for CVS
**
****************************************************************************/
    require_once '_include/class_lodo.php';
    require_once '_include/class_layout.php';
    require_once '_include/class_database.php';
    require_once '_include/class_mva.php';
    require_once '_include/class_config.php';
    require_once '_include/class_package_ge.php';
    require_once '_include/class_schema.php';

    $myPortal = "prod";

    $lodo = new lodo();
    $layout = new Layout( $lodo );
    $db = new Db( $lodo );

    //print ("Anh fagsystemtype:". $_REQUEST['fagsystemtype']);

    if ( !$db->Connect() ) {
        $layout->PrintError("Kunne ikke koble til databasen.");
        die();
    }

    if ( $lodo->inLodo ) {
        includeinc('top');
        includeinc('left');
    }

    $mva = new Mva( $db );
    $config = new Config( $db );

    $package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

    $packageId = $_REQUEST['packetid'];
    $schemacontrol = $_REQUEST['schemacontrol'];

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
?>
<h1>AltInn sending</h1>
<p>Hvis det kommer opplysninger om at noen variabler mangler eller er feil betyr det at du m&aring; sjekke disse n&aring;r du logger deg inn i Altinn.</p>
<?php

        /* Loop through the schemas */
        $sqlStr = 'SELECT schema.instance_id,schema.schematype,schema.schemarevision,packet.termin,packet.year, packet.ts_created ' .
                'FROM altinn_schema AS schema,altinn_packet AS packet ' .
                'WHERE schema.packet_id=packet.packet_id AND packet.packet_id=' . $packageId;
        if ( $rs = $db->Query( $sqlStr ) )
        {
            while ( $row = $db->NextRow( $rs ) )
            {
                $schema = new Schema($db, $lodo, $config, $layout, $row['schematype'], $row['schemarevision'], $row['year']);

                if ($schemacontrol=='N')
                    $schema->LoadSchema( $row['instance_id'] );
                else {
                    switch ($row['schematype']) {
                        case 212:
                            //print ("Anh 212:". "br");
                            $schema->LoadSchema( $row['instance_id'], $_REQUEST);
                            break;
                        default: break;
                    }//switch
                }
                //$schema->LoadSchema( $row['instance_id'] );

                $sendPost = $schema->ToXML( $row['year'], $row['termin'], array('packageID' => $packageId,'sendersRef' => $row['ts_created'], 'parentsRef' => $_REQUEST["parentRef"], 'useProxy' => true, 'portal' => $myPortal));
                //print ("Anh xml: ".$sendPost. "<br>");

            }
            $db->EndQuery($rs);
        }
//print $xml;
//echo("POST: $sendPost\n\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $_SERVER["HTTP_HOST"] . "/altinn/proxy.php");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sendPost);
        $result = curl_exec($ch);
// print "Sendpost: " . $sendPost;

        /* Check if it was sent ok */
        if ( !strncmp($result,'Status: OK', strlen('Status: OK')) )
        {
            /* Sending was ok, update the status of the package */
            $sqlStr = "UPDATE altinn_packet SET status=" . $package->PACKAGESTATUS_SENTOK . ",ts_modified='" . time() . "' WHERE packet_id=" . $packageId;
            if (!$db->Query( $sqlStr ))
            {
                $layout->PrintWarning("Pakken ble sendt, men fikk ikke oppdatert status p&aring; pakken i databasen. V&aring;r vennlig &aring; kontakte support og be dem sjekke status p&aring; pakkenummer " . $packageId . ".");
            }
            else if (1 == 0){
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
        $layout->PrintError("Du har ikke lov til &aring; sende en pakke som allerede har blitt fors&oslash;t sendt.");
        $db->Disconnect();
        die();
    }

    $layout->PrintHead( "AltInn" );

?>
<p><a href="<?php echo($_SERVER['SCRIPT_NAME'])?>?t=altinn.index">Tilbake til Altinn-skjemaer</a></p>

<?php
    $db->Disconnect();
?>
