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
        includeinc('top');
        includeinc('left');
    }
?>
<h1>AltInn skjemaer</h1>
<strong>&Aring;r:</strong> <?php echo( $year );?><br/>

<?php
        $sqlStr = 'SELECT * FROM altinn_schema WHERE packet_id=' . $packageid . ';';
    if ( $rs = $db->Query($sqlStr) )
    {
        $row = $db->NextRow( $rs );
        if ($row["schematype"] == "669")
        {
            require_once $_SETUP['HOME_DIR']."/code/lodo/altinn/_include/schema_669_3398.inc.php";
        }
        else
        {
?>
<table>
<tr>
    <th>ORID</th>
    <th>Verdi</th>
    <th>Gruppe</th>
    <th>Verdi</th>
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
<?php
    $key = str_replace("D", "", $key);
    if (is_numeric($key))
    {
        $sqlStr = 'SELECT * FROM altinn_orid WHERE Altinn_oridID =' . $key . ';';
        if ( $rs = $db->Query($sqlStr) )
            $row2 = $db->NextRow( $rs );
    }
?>
    <td><?php echo( $row2["Navn"] );?></td>
    <td><input type="text" name="D<?php print $key; ?>" value="<?php echo( $value );?>"></td>
</tr>
<?php
        }
        $db->EndQuery( $rs );
?>
</table>
<?php
    }echo ($_SERVER['SCRIPT_NAME']);
?>

<form name="AltinnSend" action="<?php echo($_SERVER['SCRIPT_NAME'])?>" method="post">
    <table>
    <tr>
    <td><input type="submit" name="Send" value="Send til Altinn"/></td>
    </tr>
    </table>
</form>
<?php
}
?>
<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.index' ));?>">Tilbake til Altinn oversikt</a></p>

<?php
    $layout->PrintFoot(  );
    $db->Disconnect();
?>
