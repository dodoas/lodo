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

    function XMLenvelope($inputData)
    {
        /*
         * Trenger følgende variable:
         * $inputData["orgnr"]
         * $inputData["batchId"]
         * $inputData["systemID"]
         * $inputData["sendersRef"]
         * $inputData["parentsRef"]
         * $inputData["xml"]
         *
         */
        $xmlCode = '<?xml version="1.0" encoding="UTF-8"?>' . chr(10) . chr(13);
        $xmlCode .= '<DataBatch';
        $xmlCode .= ' schemaVersion="1.1"';
        $xmlCode .= ' batchId="' . $inputData["batchId"] . '"';
        $xmlCode .= ' enterpriseSystemId="' . $inputData["systemID"] . '"';
        $xmlCode .= ' receiptType="OnDemand"';
        $xmlCode .= ' receiptUrl="http://konsulentvikaren.no:8080/axis/AltinnReceipt.jws"';
        $xmlCode .= '>' . chr(10) . chr(13);
        $xmlCode .= '   <DataUnits>' . chr(10) . chr(13);
        $xmlCode .= '       <DataUnit';
        $xmlCode .= ' participantId="' . $inputData["orgnr"] . '"';
        $xmlCode .= ' sendersReference="' . $inputData["sendersRef"] . '"';
        $xmlCode .= ' parentReference="' . $inputData["parentsRef"] . '"';
        $xmlCode .= ' completed="0"';
        $xmlCode .= ' locked="0"';
        $xmlCode .= '>' . chr(10) . chr(13);
        $xmlCode .= $inputData["xml"]  . chr(10) . chr(13) ;
        $xmlCode .= '       </DataUnit>' . chr(10) . chr(13);
        $xmlCode .= '   </DataUnits>' . chr(10) . chr(13);
        $xmlCode .= '</DataBatch>' . chr(10) . chr(13);
        return $xmlCode;
    }

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

    $packageId = 4;
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
    $layout->PrintHead( "AltInn" );
    if ( $lodo->inLodo ) {
<<<<<<< .mine
        includeinc('top');
        includeinc('leftmenu');
=======
        includeinc('top');
        includeinc('left');
>>>>>>> .r75
    }
?>
<form name="" action="lodo.php?t=altinn.sendpacket_debug&amp;_Level1ID=&amp;_Level2ID=&amp;" method="post">
batchId: <input type="text" name="batchId" value="<?php echo($_REQUEST["batchId"]);?>" size="70"/><br>
passord: <input type="text" name="passord" value="lodotest1" size="70"/><br>
enterpriseSystemId: <input type="text" name="enterpriseSystemId" value="294" size="70"/><br>
participantId: <input type="text" name="participantId" value="<?php echo($_REQUEST["participantId"]);?>" size="70"/><br>
sendersReference: <input type="text" name="sendersReference" value="<?php echo($_REQUEST["sendersReference"]);?>" size="70"/><br>
parentReference: <input type="text" name="parentReference" value="<?php echo($_REQUEST["parentReference"]);?>" size="70"/><br>
completed: <input type="text" name="completed" value="<?php echo($_REQUEST["completed"]);?>" size="70"/><br>
locked: <input type="text" name="locked" value="<?php echo($_REQUEST["locked"]);?>" size="70"/><br>
skjema: <textarea name="skjema" rows="10" cols="80" wrap="off"><?php echo($_REQUEST["skjema"]);?></textarea><br>
<input type="submit" name="send" value="Send til altinn" />
</form>
<?php
         $inputData["orgnr"] = $_REQUEST["participantId"];
         $inputData["batchId"] = $_REQUEST["batchId"];
         $inputData["systemID"] = $_REQUEST["enterpriseSystemId"];
         $inputData["sendersRef"] = $_REQUEST["sendersReference"];
         $inputData["parentsRef"] = $_REQUEST["parentReference"];
         $inputData["xml"] = $_REQUEST["skjema"];

        $sendPost = "passord=" . $_REQUEST["passord"] . "&xml=" . urlencode(XMLenvelope($inputData));
        # $sendPost = "schemaVersion=1.0&enterpriseSystemId=" . $config->GetConfig($config->TYPE_FAGSYSTEMID ) . "&batchId=" . $config->GetConfig($config->TYPE_ORGNO) . "-" . sprintf("%02d", $config->GetConfig($config->TYPE_BATCHSUBNO)) . "-" . $packageId . "&participantId=" . $config->GetConfig( $config->TYPE_ORGNO ) . "&passord=" . $config->GetConfig( $config->TYPE_PASSWORD );




if ($_REQUEST["skjema"] != "")
{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://konsulentvikaren.no:8080/altinn/servlet/AltInnServlet");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sendPost);
        $result = curl_exec($ch);
//echo("<p>XML<hr><pre>" . $xml['body'] . "</pre>\n");
//echo("<p>SEND<hr><pre>" . $sendPost . "</pre>\n");
        curl_close($ch);
}

    $db->Disconnect();
?>
