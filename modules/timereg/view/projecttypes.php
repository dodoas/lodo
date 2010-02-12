<?PHP
/* Copyright (c) 2005 Lodo */
    require_once '_include/inc_database_mysqli.php';
    require_once '_include/inc_lodo.php';

    $db = new DbActra();
    $db->Connect();

    /* Interface to Lodo system */
    $lodo = new Lodo();

    /* Add a new project type to this client */
    if ( $_REQUEST['cmd'] == "add" )
    {
        /* Add a new project type */
        $values = array(
            "ext_client_id" => $lodo->currentClientId,
            "hourly_cost" => intval($_REQUEST['hourly_cost']),
            "name" => trim($_REQUEST['name'])
        );
        $sqlStr = "INSERT INTO timer_projecttype " . $db->BuildSQLString( $db->BUILD_INSERT, $values );
        $db->Query( $sqlStr );

        header("Location: " . $MY_SELF . "\n\n");
    }
    /* Delete this project type (THIS elseif must be above the one that checks if the projecttype should be updated) */
    elseif ( $_REQUEST['delete'] != "" )
    {
        $sqlStr = "DELETE FROM timer_projecttype WHERE projecttype_id=" . $_REQUEST['projecttype_id'];
        $db->Query( $sqlStr );

            header("Location: " . $MY_SELF . "\n\n");
    }
    elseif ( $_REQUEST['save'] != "" )
    {
        $values = array(
            "hourly_cost" => intval($_REQUEST['hourly_cost']),
            "name" => trim($_REQUEST['name'])
        );
        $sqlStr = "UPDATE timer_projecttype SET " . $db->BuildSQLString( $db->BUILD_UPDATE, $values ) . " WHERE projecttype_id=" . $_REQUEST['projecttype_id'];
        $db->Query( $sqlStr );

        header("Location: " . $MY_SELF . "\n\n");
    }
    print $_lib['sess']->doctype;
?>

<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.41 2005/01/30 12:35:04 thomasek Exp $" />
    <? includeinc('head') ?>
<script type="text/javascript">
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

function URLencode(sStr)
{
    return escape(sStr).replace(/\+/g, '%2C').replace(/\"/g,'%22').replace(/\'/g, '%27');
}

function DoNewEstimate( goUrl  )
{
    nameVar = prompt("Gi estimatet et navn (F.eks. 'Mai 2005'):","");
    if ( nameVar ) {
        document.nestimate.newestimate.value = nameVar;
        return( true );
    }

    return( false );
}

function AskDelete( )
{
    res = confirm("Er du sikker på at du vil slette denne linjen?");
    if ( res == true )
    {
        return( true );
    }
    return( false );
}
//-->
</script>

    </head>
<body>

<h2>Prosjekttyper</h2>

<table>
<tr>
    <th></th>
    <th>Navn</th>
    <th>Timepris</th>
    <th></th>
</tr>
<?PHP
    $sqlStr = "SELECT * FROM timer_projecttype WHERE ext_client_id=" . $lodo->currentClientId;
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        while ( ($row = $db->NextRow( $rs )) )
        {
?>
<tr>
<form action="<?php echo( $MY_SELF );?>" onsubmit="return AskDelete();" method="post">
<input type="hidden" name="projecttype_id" value="<?PHP echo( $row['projecttype_id'] );?>" />
    <td class="bgl"><input type="submit" name="delete" value="Slett linjen" /></td>
</form>
<form action="<?php echo( $MY_SELF );?>" method="post">
<input type="hidden" name="cmd" value="update" />
<input type="hidden" name="projecttype_id" value="<?PHP echo( $row['projecttype_id'] );?>" />
    <td class="bgl"><input name="name" value="<?PHP echo( $row['name'] );?>" size="20" /></td>
    <td class="bgl"><input name="hourly_cost" value="<?PHP echo( $row['hourly_cost'] );?>" size="4" /></td>
    <td class="bgl"><input type="submit" name="save" value="Lagre linjen" /></td>
</tr>
</form>
<?PHP
        }
        $db->EndQuery($rs );
    }
?>
<tr>
    <td class="bgl">&#160;</td>
    <td class="bgl"><form action="<?php echo( $MY_SELF );?>" method="post">
<input type="hidden" name="cmd" value="add" /><input name="name" value="<?PHP echo( $row['name'] );?>" size="20" /></td>
    <td class="bgl"><input name="hourly_cost" value="<?PHP echo( $row['hourly_cost'] );?>" size="4" /></td>
    <td class="bgl"><input type="submit" value="Legg til ny" /></form></td>
</tr>
</table>

<p><a href="javascript:close();">Lukk vindu</a></p>

</body>
</html>
<?PHP
    $db->Disconnect();
?>
