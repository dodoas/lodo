<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Geir Eliassen (geir@actra.no)

Virker som om siden viser en liste med terminer for det valgte skjemaet,
og gir deg muligheten til å lage eller redigere kladder for terminene.
****************************************************************************/
    require_once '_include/class_lodo.php';
    require_once '_include/class_layout.php';
    require_once '_include/class_database.php';
    require_once '_include/class_mva.php';
    require_once '_include/class_config.php';
    
    includelogic("company/companyinfo");

    $lodo = new lodo();
    $db = new Db( $lodo );
    if ( !$db->Connect() ) {
        $layout->PrintError("Kunne ikke koble til databasen.");
        die();
    }

    //Get the inforamtion of the company
    //$comanyInfo     = new ComanyInfo(array());
    //print ("Anh VatPeriod:". $comanyInfo->CustomerCompany->VatPeriod);


     //Get the inforamtion of the company
    //$_comanyInfo = new ComanyInfo(array());

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
        includeinc('top');
        includeinc('left');
    }
?>
<h1><?php print utf8_decode($myHeading); ?></h1>


<table>
<tr>
<?php
//print ("Anh termintype:". $row["termintype"]."<br>");

$MOMSOPPGAVE=1;
$TERMINOPPGAVE=22;

$termintype = $row["termintype"];
if ($termintype > 0 || $packettype==$MOMSOPPGAVE)
{

?>
    <th>Termin</th>
<?php
}
?>
    <th>&Aring;r</th>
    <th>Sendinger</th>
    <th>Kladder</th>
    <th></th>
</tr>
<?php
$sqlStr = ' SELECT `Period` FROM `accountperiod` ORDER BY `Period` ASC LIMIT 0, 1';
        $rs = $db->Query( $sqlStr );
        $row = $db->NextRow( $rs );
        list($fraArr, $till) = split("-", $row[0]);
$sqlStr = ' SELECT `Period` FROM `accountperiod` ORDER BY `Period` DESC LIMIT 0, 1';
        $rs = $db->Query( $sqlStr );
        $row = $db->NextRow( $rs );
        list($tilArr, $till) = split("-", $row[0]);
    $myCount = 0;
    for($i = $fraArr; $i  < $tilArr+1; $i++)
    {
        $myCount++;
        //if ($termintype != 4)
        if ($packettype!=$MOMSOPPGAVE && $termintype != 4)
        {

?>
<tr>
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
        else {

            //if ($packettype==$MOMSOPPGAVE)
                //$termintype=$comanyInfo->CustomerCompany->VatPeriod;

            $terminStr="";
            $termin_length=4;
            if ($packettype==$MOMSOPPGAVE || $termintype > 0) {
                switch ($termintype) {
                    //2 M?nedvis
                    case 4:$termin_length=4;
                        break;
                    case 3:$termin_length=3;
                        break;
                    case 1:$termin_length=1;
                        break;
                    case 2:$termin_length=2;
                        break;
                    case 6:$termin_length=6;
                        break;
                    case 12:$termin_length=12;
                        break;
                    default:break;
                }//switch
            }
                 //2 M?nedvis

                 for ($j=1; $j <= $termin_length; $j++) {
                     switch ($termintype){
                        //2 M?nedvis
                        case 6: switch ($j) {
                                    case 1 : $terminStr=$j . " - Jan, Feb -";
                                    break;
                                    case 2: $terminStr=$j . " - Mars, April -";
                                    break;
                                    case 3: $terminStr=$j . " - Mai, Juni -";
                                    break;
                                    case 4: $terminStr=$j . " - Juli, Aug -";
                                    break;
                                    case 5: $terminStr=$j . " - Sept, Okt -";
                                    break;
                                    case 6: $terminStr=$j . " - Nov, Des -";
                                    break;
                                    default: break;
                                }
                                break;
                        //Kvartalvis
                        case 4: switch ($j){
                             case 1: $terminStr=$j . " - Jan, Feb, Mars -";
                                break;
                             case 2: $terminStr=$j . " - April, Mai, Juni -";
                                break;
                             case 3: $terminStr=$j . " - Jul, Aug, Sept -";
                                break;
                             case 4: $terminStr=$j . " - Okt, Nov, Des -";
                                break;
                             default: break;
                            }//switch
                               break;
                        //M?nedlig
                        case 12: switch ($j){
                             case 1: $terminStr=$j . " - Jan -";
                             break;
                             case 2: $terminStr=$j . " - Feb -";
                             break;
                             case 3: $terminStr=$j . " - Mars -";
                             break;
                             case 4: $terminStr=$j . " - April -";
                             break;
                             case 5: $terminStr=$j . " - Mai -";
                             break;
                             case 6: $terminStr=$j . " - Juni -";
                             break;
                             case 7: $terminStr=$j . " - Juli -";
                             break;
                             case 8: $terminStr=$j . " - Aust -";
                             break;
                             case 9: $terminStr=$j . " - Sept -";
                             break;
                             case 10: $terminStr=$j . " - Okt -";
                             break;
                             case 11: $terminStr=$j . " - Nov -";
                             break;
                             case 12: $terminStr=$j . " - Des -";
                             break;
                             default: break;
                        }//switch
                            break;
                        //?rlig
                        case 1: switch ($j){
                             case 1: $terminStr=$j . " - Jan..Des -";
                             break;
                             default: break;
                            }//switch
                           break;
                       // Halv ?rlig
                        case 2: switch ($j){
                             case 1: $terminStr=$j . " - Jan..Juni -";
                             break;
                             case 2: $terminStr=$j . " - Juni..Des -";
                             break;
                             default: break;
                            }//switch
                           break;
                       case 3: switch ($j){
                             case 1: $terminStr=$j . " - Jan.....April -";
                             break;
                             case 2: $terminStr=$j . " - April..August -";
                             break;
                             case 3: $terminStr=$j . " - August....Des -";
                             break;
                             default: break;
                            }//switch
                           break;
                       default: break;

                    }//switch



?>
<tr>
    <td><?php echo( $terminStr );?></td>
    <td><?php echo( $i );?></td>
<?php
    $sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId .
        ' AND status>0' .
        ' AND year=' . $i .
        ' AND termin=' . $j .
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
    <td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.gpackages' ));?>&amp;year=<?php echo( $i );?>&amp;status=1&amp;packettype=<?php print $packettype; ?>&amp;termin=<?php print $j; ?>">Vis</a>]</td>
<?php
    }
    else {
        echo('<td>&#160;</td>');
    }

    $sqlStr = 'SELECT COUNT(*) FROM altinn_packet WHERE customer_id=' . $lodo->lodoCurrentClientId .
        ' AND status<1' .
        ' AND year=' . $i .
        ' AND termin=' . $j .
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
    <td><?php echo($packages);?> <?php echo($txtSending);?> [<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_list' ));?>&amp;year=<?php echo( $i );?>&amp;status=0&amp;packettype=<?php print $packettype; ?>&amp;termin=<?php print $j; ?>">Editer</a>]</td>
<?php
    }
    else {
        echo('<td>&#160;</td>');
    }

    if ($packettype==$MOMSOPPGAVE) {
    ?>
    <td>[<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.packagemva' ));?>&amp;year=<?php echo( $i ); ?>&amp;packettype=<?php print $packettype; ?>&amp;terminitem=<?php print $j; ?>&amp;termin=<?php print $termintype; ?>&amp;terminstr=<?php print $terminStr; ?>">Nytt skjema</a>]</td>
    <?php } elseif ($packettype==$TERMINOPPGAVE) { ?>
        <td>[<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_termin' ));?>&amp;year=<?php echo( $i ); ?>&amp;packettype=<?php print $packettype; ?>&amp;terminitem=<?php print $j; ?>&amp;termin=<?php print $termintype; ?>&amp;terminstr=<?php print $terminStr; ?>">Nytt skjema</a>]</td>
    <?php }
    else { ?>
    <td>[<a href="<?php echo($lodo->LodoUrlGet( '', $lodo->LODOURLTYPE_HREF, 'altinn.package_make' ));?>&amp;year=<?php echo( $i ); ?>&amp;packettype=<?php print $packettype; ?>&amp;terminitem=<?php print $j; ?>">Nytt skjema</a>]</td>
    <?php }
?>

</tr>
<?php
        }

    }//for
    }//else
?>
</table>
<?php
    $layout->PrintFoot(  );

    $db->Disconnect();
?>
