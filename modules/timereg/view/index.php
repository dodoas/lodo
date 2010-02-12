<?PHP
/* Copyright (c) 2005 Lodo */
    require_once '_include/inc_database_mysqli.php';
    #require_once '_include/inc_database.php';
    require_once '_include/inc_resultbrowser.php';
    require_once '_include/inc_lodo.php';
    require_once '_include/inc_lodomodule_estimate.php';
    require_once '_include/inc_lodomodule_log.php';
    require_once '_include/inc_lodomodule_report.php';

    $db = new DbActra();
    $db->Connect();

    /* Interface to Lodo system */
    $lodo = new Lodo();

    /* Decide what module we're in:
        log: Write log of what you've done
        report: Print a report
        estimate: Estimate how much time you want to use
        config: Config (Not active yet)
     */
    $goUrlArray = array(
        "t" => "timereg.index",
        "rb_page" => $_REQUEST['rb_page'],
        "srch_name" => $_REQUEST['srch_name'],
        "srch_active" => $_REQUEST['srch_active']
        );

    $module = $_REQUEST['module'];
//  $module = "log";
    if ( $module == "log" )
    {
        $goUrlArray['module'] = $module;
        $mod = new LodoTimerLog( $db, $lodo, $goUrlArray );
    }
    elseif ($module == "report")
    {
        $goUrlArray['module'] = $module;
        $mod = new LodoTimerReport( $db, $lodo, $goUrlArray );
    }
    else
    {
        $module = "estimate";   // Default module
        $goUrlArray['module'] = $module;
        $mod = new LodoTimerEstimate( $db, $lodo, $goUrlArray );
    }
print $_lib['sess']->doctype;
?>

<head>
    <title>Empatix</title>
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

function DoNewEstimate( goUrl )
{
    nameVar = prompt("Gi estimatet et navn (F.eks. 'Mai 2005'):","");
    if ( nameVar ) {
        document.nestimate.newestimate.value = nameVar;
        return( true );
    }

    return( false );
}

function DoEditEstimate( goUrl, currentName )
{
    nameVar = prompt("Editer estimatets navn (F.eks. 'Mai 2005'):",currentName);
    if ( nameVar ) {
        document.eestimate.editestimate.value = nameVar;
        return( true );
    }

    return( false );
}

function AskDelete( )
{
    res = confirm("Er du sikker på at du vil slette dette estimatet?");
    if ( res == true )
    {
        return( true );
    }
    return( false );
}
<?php
    if ( $_REQUEST['msg'] <> '' )
    {
?>
alert('<?php echo($_REQUEST['msg']);?>');
<?php
    }
?>
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
<? includeinc('top') ?>
<? includeinc('left') ?>
<h2>Timeregnskap</h2>

<?php
    /* Build SQL string for displaying customers */
    if ( $_REQUEST['srch_name'] != "" )
    {
        /* Make sure the search string doesn't mess up our SQL string */
        $tmpStr = $_REQUEST['srch_name'];
        $tmpStr = str_replace("%", "", $tmpStr);
        $tmpStr = str_replace("'", "''", $tmpStr);

        /* Search for the name that CONTAINS the search string (I.e. not exact match) */
        $sqlStrAdd .= " AND (a.AccountName LIKE '%" . $tmpStr . "%')";
    }
    if ( $_REQUEST['srch_active'] != "1" )
    {
        $sqlStrAdd .= " AND (cu.unactive IS NULL or cu.ext_user_id<>" . $lodo->currentUserId . ")";
    }

    $HourAccountPlanID = $_lib['sess']->get_companydef('HourAccountPlanID');

    $sqlStr = "SELECT a.AccountPlanID, a.AccountName, cu.unactive, cu.ext_user_id FROM accountplan as r, accountplan as a LEFT JOIN timer_customeruser AS cu ON a.AccountPlanID=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE a.AccountPlanType = r.ReskontroAccountPlanType and a.Active=1 and r.AccountPlanID='$HourAccountPlanID' " . $sqlStrAdd . " ORDER BY a.AccountName";

//  $sqlStr = "SELECT c.*, cu.unactive, cu.ext_user_id FROM timer_customer AS c LEFT JOIN timer_customeruser AS cu ON c.customer_id=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE c.ext_client_id=" . $lodo->currentClientId . $sqlStrAdd . " ORDER BY c.name";
//  $sqlStr = "SELECT c.*, cu.unactive, cu.ext_user_id FROM timer_customer AS c LEFT JOIN timer_customeruser AS cu ON c.customer_id=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE c.ext_client_id=" . $lodo->currentClientId . $sqlStrAdd . " ORDER BY c.name";
//echo($sqlStr);
//  $sqlStr = "SELECT * FROM timer_customer WHERE ext_client_id=" . $lodo->currentClientId . $sqlStrAdd . " ORDER BY name";
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        $rBrowser = new ResultBrowser( $db, $rs, 25 );
?>
<table cellspacing="0" cellpadding="0" class="bgl">
<tr bgcolor="#ffffff">
    <td width="250"><img src="blank.gif" width="250" height="1" alt="" /></td>
<?php
    $goUrl = $_SETUP['DISPATCH'] . "t=timereg.index&rb_page=" . $rBrowser->GetCurrentPage() . "&amp;srch_name=" . urlencode($_REQUEST['srch_name']) . "&amp;srch_active=" . urlencode($_REQUEST['srch_active']);
?>
    <td colspan="100">
    <table cellspacing="0" cellpadding="0" class="tab">
    <tr>
        <td><div <?php if ($module == "estimate") {?>class="active_tab"<?php } else {?>class="tab"<?php }?>><a href="<?php echo( $goUrl . "&amp;module=estimate" );?>" accesskey="E">Estimat (E)</a></div></td>
        <td><div <?php if ($module == "log") {?>class="active_tab"<?php } else {?>class="tab"<?php }?>><a href="<?php echo( $goUrl . "&amp;module=log" );?>" accesskey="A">Arbeidslogg (A)</a></div></td>
    </tr>
    </table>
    </td>
</tr>
<tr>
    <td rowspan="2" bgcolor="#ffffff" style="border-bottom: 1px solid #000000;">
        <!-- SEARCH COMPANY FORM -->
        <form action="<? print $MY_SELF ?>" style="margin: 0px; padding: 0px;" method="post">
        <input type="hidden" name="rb_page" value="<?php echo( $rBrowser->GetCurrentpage() );?>" />
        <input type="hidden" name="module" value="<?php echo( $module );?>" />
        Søk <input name="srch_name" size="10" value="<?php echo( $_REQUEST['srch_name'] );?>" /> Vis også ikke-aktive <input type="checkbox" name="srch_active" value="1"<?php if ( $_REQUEST['srch_active'] == "1" ) {?> checked<?php }?>/>
        <input type="submit" value="Søk" />
        </form>
        <!-- /SEARCH COMPANY FORM -->
    </td>
    <td colspan="100" style="border-left: 1px solid #000000; border-top: 1px solid #000000; border-right: 1px solid #000000;">
<?php
        /* All modules must implement DisplayHeader() */
        $tmpText = $mod->DisplayMainHeader();
        echo( $tmpText );
?>
    </td>
</tr>
<?php
        $first = true;  // Track first item, so we can insert header
        while ( $rBrowser->DoNextItem() && ($row = $db->NextRow($rs)) )
        {
            if ( $first )
            {
                $first = false;
?>
<tr>
<?php
        $tmpText = $mod->DisplayHeader();
        echo( $tmpText );
?>
</tr>
<?php
            }
?>
<tr>
    <td width="250" class="bgld" style="border-left: 1px solid #000000; border-bottom: solid #eeeeee 1px;">
        <form action="<?PHP echo( $_SERVER['SCRIPT_NAME'] );?>" style="margin: 0px; padding: 0px;">
        <?php /* Disable checkbox if it's not the estimate module */?>
        <input type="checkbox" name="active" value="1"<?php if ( $row['unactive'] != "1") {?> checked="checked"<?php }?><?php if ($module != "estimate") {?> disabled="disabled"<?php }?> />
        <input type="hidden" name="cmd" value="savecustomer" />
        <input type="hidden" name="customer_id" value="<?php echo($row['AccountPlanID']);?>" />
        <?php echo($lodo->GetGoUrl( 1, $goUrlArray ) );?>
        <a href="<?php echo( $lodo->GetViewCustomerUrl( $row['ext_customer_id'] ) );?>"><?php echo( $row['AccountName'] );?></a>

</td>
<?php
        $tmpText = $mod->DisplayCompany( $row['AccountPlanID'] );
        echo( $tmpText );
?>
    </form>
</tr>

<?PHP
        }
?>
<tr valign="top">
    <td bgcolor="#ffffff" style="border-top: 1px solid #000000;">&#160;</td>
<?php
        $tmpText = $mod->DisplayFooter();
        echo( $tmpText );
?>
</tr>
<tr bgcolor="#ffffff">
    <td width="250">
<?php
    $mArray = $goUrlArray;
    unset($mArray['rb_page']);
?>
    <br />Sider: <?PHP echo( $rBrowser->Display( substr($lodo->GetGoUrl( 0, $mArray ), 1 ) ) );?>
    </td>
<?php
        $tmpText = $mod->DisplayMainFooter();
        echo( $tmpText );
?>
</tr>
</table>
<?php
    }

    if ($module == "log")
    {
?>
<h2>Lag rapport</h2>
<table>
<tr>
<form action="<?PHP echo($_SETUP['DISPATCH']);?>t=timereg.report" method="post">
    <td class="bgd"><b>Fra dato</b></td>
    <td class="bgl">
<select name="time_from_day">
<?php for ($x = 1; $x < 32; $x++) {?>
<option value="<?php echo($x);?>"><?php echo($x);?>.</option>
<?php }?>
</select>
<select name="time_from_month">
<?php for ($x = 1; $x < 13; $x++) {?>
<option value="<?php echo($x);?>"><?php echo($mod->monthsArray[$x - 1]);?></option>
<?php }?>
</select>
<select name="time_from_year">
<?php for ($x = 2005; $x < 2036; $x++) {?>
<option><?php echo($x);?></option>
<?php }?>
</select>
    </td>
</tr>
<tr>
    <td class="bgd"><b>Til dato</b></td>
    <td class="bgl">
<select name="time_to_day">
<?php for ($x = 1; $x < 32; $x++) {?>
<option value="<?php echo($x);?>"><?php echo($x);?>.</option>
<?php }?>
</select>
<select name="time_to_month">
<?php for ($x = 1; $x < 13; $x++) {?>
<option value="<?php echo($x);?>"><?php echo($mod->monthsArray[$x - 1]);?></option>
<?php }?>
</select>
<select name="time_to_year">
<?php for ($x = 2005; $x < 2036; $x++) {?>
<option><?php echo($x);?></option>
<?php }?>
</select>
    </td>
</tr>
<tr>
    <td class="bgd"><b>Tidsenhet</b></td>
    <td class="bgl">
<select name="unit_type">
<option value="3">Måned</option>
<option value="2">Uke</option>
<option value="1">Dag</option>
</select>
    </td>
</tr>
<tr>
    <td class="bgd"><b>Vis prosjekter</b></td>
    <td class="bgl">
    <input type="checkbox" name="projecttypes" value="1"/>
    </td>
</tr>
<tr>
    <td class="bgd"><b>Vis kunder</b></td>
    <td class="bgl">
    <select name="customers">
<option value="0">Alle kunder</option>
<?php
    $sqlStr = "SELECT a.AccountPlanID, a.AccountName, cu.unactive, cu.ext_user_id FROM accountplan as r, accountplan as a LEFT JOIN timer_customeruser AS cu ON a.AccountPlanID=cu.customer_id AND cu.ext_user_id=" . $lodo->currentUserId . " WHERE r.ReskontroAccountPlanType=a.AccountPlanType and a.Active=1 and r.AccountPlanID='$HourAccountPlanID' ORDER BY a.AccountName";
//  $sqlStr = "SELECT customer_id,name FROM timer_customer WHERE ext_client_id=" . $lodo->currentClientId . " ORDER BY name";
    if ( ($rs = $db->Query( $sqlStr )) )
    {
        while ( $row = $db->NextRow( $rs ) )
        {
?>
<option value="<?php echo($row['AccountPlanID']);?>"><?php echo($row['AccountName']);?></option>
<?php
        }
    }
?>
    </td>
</tr>
<tr>
    <td class="bgd"><b>Vis ikke-aktive</b></td>
    <td class="bgl">
    <input type="checkbox" name="active" value="1"/>
    </td>
</tr>
<tr>
    <td class="bgd"></td>
    <td class="bgl">
<input type="submit" value="Lag rapport" />
    </td>
</tr>
</table>
</form>
<?PHP
    }
    $db->Disconnect();
?>

</body>
</html>
