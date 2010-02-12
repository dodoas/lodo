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
    $terminItem = $_REQUEST['terminitem'];
    $status = $_REQUEST['status'];

    $package = new Package( array('db'=>$db, 'lodo'=>$lodo, 'config'=>$config,'layout'=>$layout) );

    $layout->PrintHead( "AltInn" );
    if ( $lodo->inLodo ) {
        includeinc('top');
        includeinc('left');
    }
?>
<h1>AltInn skjemaer</h1>
<strong>År:</strong> <?php echo( $year );?><br/>
<strong>Termin:</strong> <?php echo( $terminItem );?> (<?php echo($mva->GetTerminItemName( $config->GetConfig( $config->TYPE_TERMIN ), $terminItem ));?>)<br/>

<?php
    if ($status == 0) {
        $sqlStr = 'SELECT * FROM altinn_packet WHERE termin=' . $terminItem . ' AND termintype=' . $config->GetConfig( $config->TYPE_TERMIN ) . ' AND year=' . $year . ' AND status=0';
    }
    else {
        $sqlStr = 'SELECT * FROM altinn_packet WHERE termin=' . $terminItem . ' AND termintype=' . $config->GetConfig( $config->TYPE_TERMIN ) . ' AND year=' . $year . ' AND status<>0';
    }
    if ( $rs = $db->Query($sqlStr) )
    {
?>
<table>
<tr>
    <th>Opprettet</th>
    <th>Endret</th>
    <th>Av</th>
    <th>Type</th>
    <th>Status</th>
    <th></th>
</tr>
<?php
        while ( $row = $db->NextRow( $rs ) )
        {
            /* If necessary, check status in AltInn */
            $packageId = $row['packet_id'];
            $status = $row['status'];
            if ( $status == 1 )
            {
                $tmp = $package->CheckAltInnStatus( $packageId );
                if ($tmp > 0) {
                    $status = $tmp;
                }
            }
?>
<tr>
    <td><?php echo( date("j/n-Y H:i", $row['ts_created']) );?></td>
    <td><?php echo( date("j/n-Y H:i", $row['ts_modified']) );?></td>
    <td><?php echo($row['modified_by']);?></td>
    <td><?php echo($package->packageNames[$row['packettype']]);?></td>
    <td><?php echo($package->statusText[$status]);?></td>
    <td>
        <?php if ($row['status'] == 0) {?>
            [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packagemva' ));?>&amp;packageid=<?php echo( $row['packet_id'] );?>">Editer</a>]<br/>
        <?php } else {?>
            [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packagemva' ));?>&amp;packageid=<?php echo( $row['packet_id'] );?>">Vis</a>]<br/>
        <?php }?>
    </td>
</tr>
<?php
        }
        $db->EndQuery( $rs );
?>
</table>
<?php
    }
?>
<p><a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF,'altinn.termins' ));?>">Tilbake til terminoversikt</a></p>

<?php
    $layout->PrintFoot(  );

    $db->Disconnect();
?>
