<?PHP
/* Copyright (c) 2005 Lodo */
    require_once '_include/inc_database_mysqli.php';
    require_once '_include/inc_lodo.php';

    $UNITTYPE_DAY = 1;
    $UNITTYPE_WEEK = 2;
    $UNITTYPE_MONTH = 3;

    $daysArray = array("Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag");
    $monthsArray = array("Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember");

    function CalcUnits ( $unitType, $timeFrom, $timeTo )
    {
        global $UNITTYPE_DAY, $UNITTYPE_WEEK, $UNITTYPE_MONTH;

        /* How many units is there in our range? */
        $units = 0;
        if ( $unitType == $UNITTYPE_DAY ) {
            $units = ($timeTo - $timeFrom) / (60 * 60 * 24); // Number of days
        }
        elseif ( $unitType == $UNITTYPE_WEEK ) {
            $units = ($timeTo - $timeFrom) / (60 * 60 * 24 * 7); // Week
        }
        else {
            /* Loop through all months and count them */
            $x = $timeFrom;
            while ($x < $timeTo)
            {
                $x = mktime(0, 0, 0, date("m",$x)+1, date("d",$x),  date("Y",$x));
                $units++;
            }
        }

        return( $units );
    }

    function DoHeadRow( $unitType, $units, $timeFrom, $timeTo )
    {
        global $db, $lodo, $UNITTYPE_DAY, $UNITTYPE_WEEK, $UNITTYPE_MONTH,$daysArray,$monthsArray;

        $oldTime = 0;
        $thisTime = $timeFrom;
        $retVal = "";
        for ($col = 0; $col < $units; $col++ )
        {
            $retVal .= "<th align=\"right\">";
            /* Are we in a new year? */
            if ( !$oldTime || (date("Y", $thisTime) != date("Y", $oldTime)) ) {
                $retVal .= date("Y", $thisTime) . "<br />";
            }
            else {
                $retVal .= "<br />";
            }
            /* Are we in a new month (Unless unit type is month)? */
            if ( $unitType != $UNITTYPE_MONTH )
            {
                if ( !$oldTime || (date("m", $thisTime) != date("m", $oldTime)) ) {
                    $retVal .= substr($monthsArray[date("n", $thisTime) - 1], 0 ,3) . "<br />";
                }
                else {
                    $retVal .= "<br />";
                }
            }

            $oldTime = $thisTime;
            if ( $unitType == $UNITTYPE_DAY )
            {
                $retVal .= date("d", $thisTime) . "<br />" . substr($daysArray[date("w", $thisTime)], 0, 2);
                $thisTime = strtotime("+1 day", $thisTime);
            }
            elseif ( $unitType == $UNITTYPE_WEEK ) {
                $retVal .= date("W", $thisTime);
                $thisTime = strtotime("+1 week", $thisTime);
            }
            else {
                $retVal .= substr($monthsArray[date("n", $thisTime) - 1], 0 ,3);
                $thisTime = mktime(0, 0, 0, date("m",$thisTime)+1, date("d",$thisTime),  date("Y",$thisTime));
            }
            $retVal .= "</td>";
        }

        return( $retVal );
    }
    $db = new DbActra();
    $db->Connect();

    /* Interface to Lodo system */
    $lodo = new Lodo();

    $timeFrom = mktime(0,0,0,$_REQUEST["time_from_month"],$_REQUEST["time_from_day"],$_REQUEST["time_from_year"]);
    $timeTo = mktime(0,0,0,$_REQUEST["time_to_month"],$_REQUEST["time_to_day"],$_REQUEST["time_to_year"]);
    $unitType = $_REQUEST["unit_type"];
    $active = $_REQUEST["active"];
    $projectTypes = $_REQUEST["projecttypes"];
    $criteriaProjectType = $_REQUEST["projecttype"];

    print $_lib['sess']->doctype;
?>

<head>
    <title>Empatix</title>
    <meta name="cvs"                content="$Id: list.php,v 1.41 2005/01/30 12:35:04 thomasek Exp $" />
    <? includeinc('head') ?>
<SCRIPT type="text/javascript">
<!--//
function PopUp( url, width, height, status, toolbar, scrollbars )
{
    if (width == null) w = 600; else w = width;
    if (height == null) h = 400; else h = height;
    if (status == null) st = 0; else st = status;
    if (toolbar == null) tb = 0; else tb = toolbar;
    if (scrollbars == null) sb = 1; else sb = scrollbars;

    open(url,"_blank","status="+st+",toolbar="+tb+",scrollbars="+sb+",resizable=1,width="+w+",height="+h+",screenX=30,screenY=30,left=30,top=30");
}
//-->
</script>
<style type="text/css">
<!--
.bgl
{
    background: #eeeeee;
}
.bgd
{
    background: #cccccc;
}
.bgld
{
    background: #cccccc;
    border-bottom: solid 1px #eeeeee;
}
.bgdd
{
    background: #999999;
    border-bottom: solid 1px #eeeeee;
}
INPUT.xs {font-size: xx-small;}
th
{
    padding: 2px 10px 2px 10px;
    BORDER-RIGHT: 0px;
    FONT-WEIGHT: bold;
    color:#000000;
    BACKGROUND-COLOR: #FFB600;
    text-align: center;
}
-->
</style>
</head>
<body>

<h2>Rapport</h2>

<p><b>I tidsrommet <?php echo(date("d.m.Y", $timeFrom));?> - <?php echo(date("d.m.Y", $timeTo));?></b></p>

<table cellspacing="0" cellpadding="0">
<tr>
    <td width="250"><img src="blank.gif" width="250" height="1" alt="" /></td>
<?PHP
    $units = CalcUnits( $unitType, $timeFrom, $timeTo );
    echo( DoHeadRow( $unitType, $units, $timeFrom, $timeTo ) );
?>
</tr>
<?PHP
    $sqlAdd = "";
    /* Include only active? */
    if ( $_REQUEST['active'] != "1" )
    {
        $sqlStrAdd .= " AND (cu.unactive IS NULL or cu.ext_user_id<>" . $lodo->currentUserId . ")";
    }
    if ( is_numeric($_REQUEST['customers']) && intval($_REQUEST['customers']) > 0) {
        $sqlStrAdd .= " AND a.AccountPlanID=" . $_REQUEST['customers'];
    }

    $sqlStr = "SELECT a.AccountPlanID, a.AccountName, cu.unactive, cu.ext_user_id FROM accountplan as r, accountplan as a LEFT JOIN timer_customeruser AS cu ON a.AccountPlanID=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE r.ReskontroAccountPlanType=a.AccountPlanType and a.Active=1 and r.AccountPlanID='1500' " . $sqlStrAdd . " ORDER BY a.AccountName";
//  $sqlStr = "SELECT c.*, cu.unactive, cu.ext_user_id FROM timer_customer AS c LEFT JOIN timer_customeruser AS cu ON c.customer_id=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE c.ext_client_id=" . $lodo->currentClientId . $sqlStrAdd . " ORDER BY c.name";
    //  $sqlStr = "SELECT c.customer_id, c.name, cu.unactive FROM timer_customer AS c LEFT JOIN timer_customeruser AS cu ON c.customer_id = cu.customer_id WHERE c.ext_client_id=" . $lodo->currentClientId . $sqlStrAdd . " ORDER BY c.name";
    //  $sqlStr = "SELECT customer_id,name FROM timer_customer WHERE ext_client_id=" . $lodo->currentClientId . $sqlAdd . " ORDER BY name";
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        while ( ($row = $db->NextRow( $rs )) )
        {
?>
<tr>
    <td class="bgld"><?php echo( $row['AccountName'] );?></td>
<?PHP
            $xAmount = 0;
            $thisTime = $oldTime = $timeFrom;
            for ($col = 0; $col < $units; $col++ )
            {
                if ( $unitType == $UNITTYPE_DAY ) {
                    $thisTime = strtotime("+1 day", $thisTime);
                }
                elseif ( $unitType == $UNITTYPE_WEEK ) {
                    $thisTime = strtotime("+1 week", $thisTime);
                }
                else {
                    $thisTime = mktime(0, 0, 0, date("m",$thisTime)+1, date("d",$thisTime),  date("Y",$thisTime));
                }

                $sqlStrAmount = "SELECT SUM(p.amount) FROM timer_logproject AS p, timer_logday AS log WHERE p.log_id=log.log_id AND log.ext_user_id=" . $lodo->currentUserId . " AND log.ext_client_id=" . $lodo->currentClientId . " AND p.customer_id=" . $row['AccountPlanID'] . " AND date BETWEEN '" . date("Y-m-d 00:00:00", $oldTime) . "' AND '" . date("Y-m-d 23:59:59", ($thisTime - 1) ) . "'";
                if ( ($rsAmount = $db->Query( $sqlStrAmount )) )
                {
                    if ( ($rowAmount = $db->NextRow( $rsAmount )) )
                    {
                        $amount = $rowAmount[0];
                        if ( !is_numeric( $amount ) ) { $amount = 0; }
                        $totalAmount[ $col ] += $amount;
                        $xAmount += $amount;
                        echo("<td class=\"bgld\" align=\"right\">" . $amount . "</td>");
                    }
                    $db->EndQuery( $rsAmount );
                }

                $oldTime = $thisTime;
            }
            echo("<td class=\"bgl\" align=\"right\">" . $xAmount . "</td>");

            /* Display projecttypes as well? */
            if ($projectTypes)
            {
            echo("</tr>");
                $sqlStrPt = "SELECT hourly_cost,projecttype_id,name " .
                    " FROM timer_projecttype " .
                    " WHERE ext_client_id=" . $lodo->currentClientId;
                if ( ($rsPt = $db->Query( $sqlStrPt )) )
                {
                    while ( ($rowPt = $db->NextRow( $rsPt )) )
                    {
            echo("<tr><td class=\"bgl\">&#160; " . $rowPt['name'] . "</td>");
            $xAmount = 0;
            $thisTime = $oldTime = $timeFrom;
            for ($col = 0; $col < $units; $col++ )
            {
                if ( $unitType == $UNITTYPE_DAY ) {
                    $thisTime += (60 * 60 * 24);
                }
                elseif ( $unitType == $UNITTYPE_WEEK ) {
                    $thisTime += (60 * 60 * 24 * 7);
                }
                else {
                    $thisTime = mktime(0, 0, 0, date("m",$thisTime)+1, date("d",$thisTime),  date("Y",$thisTime));
                }

                $sqlStrAmount = "SELECT SUM(p.amount) FROM timer_logproject AS p, timer_logday AS log WHERE p.log_id=log.log_id AND p.projecttype_id=" . $rowPt['projecttype_id'] . " AND log.ext_user_id=" . $lodo->currentUserId . " AND log.ext_client_id=" . $lodo->currentClientId . " AND p.customer_id=" . $row['AccountPlanID'] . " AND date BETWEEN '" . date("Y-m-d 00:00:00", $oldTime) . "' AND '" . date("Y-m-d 23:59:59", ($thisTime - 1) ) . "'";
                if ( ($rsAmount = $db->Query( $sqlStrAmount )) )
                {
                    if ( ($rowAmount = $db->NextRow( $rsAmount )) )
                    {
                        $amount = $rowAmount[0];
                        if ( !is_numeric( $amount ) ) { $amount = 0; }
                        $xAmount += $amount;
                        echo("<td class=\"bgl\" align=\"right\">" . $amount . "</td>");
                    }
                    $db->EndQuery( $rsAmount );
                }

                $oldTime = $thisTime;
            }
                echo("<td class=\"bgl\" align=\"right\">" . $xAmount . "</td>");

                    }
                    $db->EndQuery( $rsPt );
                }
            }
?>
</tr>
<?PHP
        }
        $db->EndQuery($rs );
    }
    /* FOOTER */
    $rowAmount = 0;
    $xAmount = 0;
    $thisTime = $oldTime = $timeFrom;
    echo("<td class=\"bgl\"><i>Sum</i></td>");
    for ($col = 0; $col < $units; $col++ )
    {
        if ( $unitType == $UNITTYPE_DAY ) {
            $thisTime = strtotime("+1 day", $thisTime);
        }
        elseif ( $unitType == $UNITTYPE_WEEK ) {
            $thisTime = strtotime("+1 week", $thisTime);
        }
        else {
            $thisTime = mktime(0, 0, 0, date("m",$thisTime)+1, date("d",$thisTime),  date("Y",$thisTime));
        }

        $xAmount += $totalAmount[ $col ];
        echo("<td class=\"bgl\" align=\"right\">" . $totalAmount[ $col ] . "</td>");
        $oldTime = $thisTime;
    }
    echo("<td class=\"bgl\" align=\"right\">" . $xAmount . "</td>");

    $db->Disconnect();
?>
</table>

<p><a href="javascript:history.go(-1);">Tilbake</a>

</body>
</html>
