<?PHP
/* Copyright (c) 2005 Lodo */
    require_once '_include/inc_database_mysqli.php';
    require_once '_include/inc_lodo.php';

    $db = new DbActra();
    $db->Connect();

    /* Interface to Lodo system */
    $lodo = new Lodo();

    $logId = $_REQUEST['log_id'];
    $customerId = $_REQUEST['customer_id'];
    $day = $_REQUEST['day'];

    /* Find name of company (we need it below for displaying title) */
    $sqlStr = "SELECT AccountName FROM accountplan as a WHERE Active=1 and AccountPlanID='$customerId'";
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        if ( ($row = $db->NextRow( $rs )) )
        {
            $companyName = $row['AccountName'];
        }
        $db->EndQuery($rs );
    }

    /* Find log_id if needed */
    if ( !is_numeric($logId)  || $logId == 0 )
    {
        $sqlStr = "SELECT log_id FROM timer_logday " .
            " WHERE ext_user_id=" . $lodo->currentUserId.
            " AND ext_client_id=" . $lodo->currentClientId.
            " AND date='" . date("Y-m-d 00:00:00", $day) . "'";
        if ( ($rs = $db->Query( $sqlStr )) )
        {
            if ( ($row = $db->NextRow( $rs )) )
            {
                $logId = $row['log_id'];
            }
            $db->EndQuery( $rs );
        }
    }

    /* Save */
    if ( $_REQUEST['cmd'] == "update")
    {
        /* Prepare time from/to convert to mins since midnight */
        $timeFromHours = $_REQUEST['time_from_hour'];
        $timeFromMins = $_REQUEST['time_from_min'];
        $timeToHours = $_REQUEST['time_to_hour'];
        $timeToMins = $_REQUEST['time_to_min'];

        $timeFrom = ($timeFromHours * 60) + $timeFromMins;
        $timeTo = ($timeToHours * 60) + $timeToMins;

        /* logId 0 means that there is no timer_logday entry for this day: Create an entry */
        $dateStr = date("Y-m-d 00:00:00", $day);
        if ( !is_numeric($logId) || $logId == 0 )
        {
            /* Make a timer_logday entry for this day */
            $values = array("ext_user_id" => $lodo->currentUserId,
                "ext_client_id" => $lodo->currentClientId,
                "date" => $dateStr
                );

            $sqlStr = "INSERT INTO timer_logday " . $db->BuildSQLString( $db->BUILD_INSERT, $values );
            $db->Query( $sqlStr );

            /* Retrieve the log_id of the entry we just created */
            $sqlStr = "SELECT MAX(log_id) FROM timer_logday WHERE ext_user_id=" . $lodo->currentUserId . " AND ext_client_id=" . $lodo->currentClientId . " AND date='" . $dateStr . "'";
            if ( ($rs = $db->Query( $sqlStr )) )
            {
                if ( ($row = $db->NextRow( $rs )) )
                {
                    $logId = $row[0];
                }
                $db->EndQuery( $rs );
            }
        }
        /* If an entry for this day was not required to create: save from/to time */
        else
        {
/*          $values = array("time_from" => $timeFrom,
                "time_to" => $timeTo);

            $sqlStr = "UPDATE timer_logdaycustomer SET " . $db->BuildSQLString( $db->BUILD_UPDATE, $values ) .
                " WHERE ext_user_id=" . $lodo->currentUserId .
                " AND ext_client_id=" . $lodo->currentClientId .
                " AND date='" . $dateStr . "'";
            $db->Query( $sqlStr );*/
        }

        /* Check if this day and customer exists */
        $sqlStr = "SELECT time_from,time_to FROM timer_logdaycustomer WHERE log_id=" . $logId . " AND customer_id=" . $customerId;
        $doInsert = true;
        if ( ($rs = $db->Query( $sqlStr )) )
        {
            if ( ($row = $db->NextRow( $rs )) )
            {
                $db_time_from = $row['time_from'];
                $db_time_to = $row['time_to'];
                $doInsert = false;
            }
            $db->EndQuery($rs );
        }

        /* Should we update to/from hours? */
        if ( $doInsert || ( !$doInsert && ( ($db_time_from != $timeFrom) || ($db_time_to != $timeTo) ) ) )
        {
            $values = array("log_id" => $logId,
                "customer_id" => $customerId,
                "time_from" => $timeFrom,
                "time_to" => $timeTo
                );

            /* Update or insert? */
            if ( $doInsert ) {
                $sqlStr = "INSERT INTO timer_logdaycustomer " . $db->BuildSQLString( $db->BUILD_INSERT, $values );
            }
            else {
                $sqlStr = "UPDATE timer_logdaycustomer SET " . $db->BuildSQLString( $db->BUILD_UPDATE, $values ) . " WHERE log_id=" . $logId . " AND customer_id=" . $customerId;
            }
            $db->Query( $sqlStr );
        }

        /* Quick and VERY dirty error message handling... lol... */
        if ($logId == 0)
        {
            die("\n\nError: Couldn't find log id!\n");
        }

        /* First delete entries for this day */
        $sqlStr = "DELETE FROM timer_logproject WHERE log_id=$logId AND customer_id=$customerId";
        $db->Query( $sqlStr );

        /* Now insert the project types that isn't 0 */
        /* Loop through all $_REQUEST and pick out amount_x. x = projecttype */
        foreach ( $_REQUEST as $key => $value )
        {
            if (!strncmp($key, "amount_", 7))
            {
                $projectTypeId = substr($key, 7);
                $value = str_replace(",", ".", $value);
                if ( !is_numeric( $value ) ) {
                    $value = 0;
                }
                $description = trim($_REQUEST["description_" . $projectTypeId]);
                /* Only insert if amount is numeric and above 0. We don't log 0. */
                if ( $value > 0 || $description != "" )
                {
                    $values = array("log_id" => $logId,
                        "customer_id" => $customerId,
                        "projecttype_id" => $projectTypeId,
                        "description" => $description,
                        "amount" => $value);

                    $sqlStr = "INSERT INTO timer_logproject " . $db->BuildSQLString( $db->BUILD_INSERT, $values );
                    $db->Query( $sqlStr );
                }
            }
        }
    }

    $timeFrom = 0;
    $timeTo = 0;
    $sqlStr = "SELECT time_from,time_to FROM timer_logdaycustomer WHERE log_id=" . $logId . " AND customer_id=" . $customerId;
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        if ( ($row = $db->NextRow( $rs )) )
        {
            /* timeFrom and timeTo are minutes since midnight */
            $timeFrom = $row['time_from'];
            $timeTo = $row['time_to'];

            /* Calculate hours and minutes */
            $timeFromHours = intval($timeFrom / 60);
            $timeFromMins = $timeFrom - ($timeFromHours * 60);

            $timeToHours = intval($timeTo / 60);
            $timeToMins = $timeTo - ($timeToHours * 60);
        }
        $db->EndQuery($rs );
    }
    print $_lib['sess']->doctype;
?>

<head>
    <title>Empatix</title>
    <meta name="cvs"                content="$Id: list.php,v 1.41 2005/01/30 12:35:04 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<h2>Logg for <?php echo(date("d-m-Y", $day));?></h2>

<h3>Tid på jobb denne dagen</h3>
<table>
<form action="<?php echo( $MY_SELF );?>" method="post">
<input type="hidden" name="cmd" value="update">
<input type="hidden" name="day" value="<?php echo( $_REQUEST['day'] );?>">
<input type="hidden" name="log_id" value="<?php echo( $logId );?>">
<input type="hidden" name="customer_id" value="<?php echo( $_REQUEST['customer_id'] );?>">

<tr>
    <td class="bgd">Start klokka</td>
    <td class="bgl">
    <select name="time_from_hour">
<?php for ($hour = 0; $hour < 24; $hour++) {?>
        <option value="<?php echo($hour);?>"<?php if ( $hour == $timeFromHours ) {?> selected<?php }?>><?php echo(sprintf("%02d", $hour));?>
<?php }?>
    </select>:<select name="time_from_min">
<?php for ($min = 0; $min < 60; $min++) {?>
        <option value="<?php echo($min);?>"<?php if ( $min == $timeFromMins ) {?> selected<?php }?>><?php echo(sprintf("%02d", $min));?>
<?php }?>
    </select>
    </td>
</tr>
<tr>
    <td class="bgd">Slutt klokka</td>
    <td class="bgl">
    <select name="time_to_hour">
<?php for ($hour = 0; $hour < 24; $hour++) {?>
        <option value="<?php echo($hour);?>"<?php if ( $hour == $timeToHours ) {?> selected<?php }?>><?php echo(sprintf("%02d", $hour));?>
<?php }?>
    </select>:<select name="time_to_min">
<?php for ($min = 0; $min < 60; $min++) {?>
        <option value="<?php echo($min);?>"<?php if ( $min == $timeToMins ) {?> selected<?php }?>><?php echo(sprintf("%02d", $min));?>
<?php }?>
    </select>
    </td>
</tr>
</table>

<h3>Jobbet for <?echo($companyName);?>:</h3>

<table>
<tr>
    <th>Navn</th>
    <th>Timer</th>
    <th>Beskrivelse</th>
</tr>
<?PHP
    /* List all projectttypes */
    $sqlStr = "SELECT * " .
        " FROM timer_projecttype " .
        " WHERE ext_client_id=" . $lodo->currentClientId;
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        while ( ($row = $db->NextRow( $rs )) )
        {
            $amount = 0;
            $description = "";

            /* If logId is 0 it means that there is no log entry for this day yet */
            if ( $logId > 0 )
            {
                /* Get amount, if specified */
                $sqlStrLog = "SELECT amount,description FROM timer_logproject " .
                    " WHERE log_id=" . $logId .
                    " AND projecttype_id=" . $row['projecttype_id'] .
                    " AND customer_id=" . $customerId;
                if ( ($rsLog = $db->Query( $sqlStrLog )) )
                {
                    while ( ($rowLog = $db->NextRow( $rsLog )) )
                    {
                        $amount = $rowLog['amount'];
                        $description = $rowLog['description'];
                    }
                    $db->EndQuery( $rsLog );
                }

            }
?>
<tr>
    <td class="bgd"><?PHP echo( $row['name'] );?></td>
    <td class="bgl"><input style="text-align: right;" name="amount_<?php echo( $row['projecttype_id'] );?>" value="<?PHP echo( str_replace(".", ",", $amount) );?>" size="4" />
    <input type="hidden" name="old_amount_<?php echo( $row['projecttype_id'] );?>" value="<?PHP echo( str_replace(".", ",", $amount) );?>" />
    </td>
    <td class="bgl">
    <input name="description_<?php echo( $row['projecttype_id'] );?>" value="<?PHP echo( $description );?>" size="20" maxlength="255" />
    <input type="hidden" name="old_description_<?php echo( $row['projecttype_id'] );?>" value="<?PHP echo( $description );?>" />
    </td>
</tr>
<?PHP
        }
        $db->EndQuery($rs );
    }
?>
</table>

<input type="submit" value="Lagre" />

</form>

<p><a href="javascript:close();">Lukk vindu</a></p>

</body>
</html>
<?PHP
    $db->Disconnect();
?>
