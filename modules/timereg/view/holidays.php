<?PHP
/* Copyright (c) 2005 Lodo */
	require_once '_include/inc_database_mysqli.php';

	$db = new DbActra();
	$db->Connect();
	
	$year = $_REQUEST['year'];
	if ( $_REQUEST['show'] != "" )
	{
		$days = "";
		$sqlStr = "SELECT day FROM timer_holidays WHERE day BETWEEN '" . mktime(0,0,0,1,1,$year) . "' AND '" . (mktime(0,0,0,1,1,$year+1) - 1) . "'";
		if ( ($rs = $db->Query( $sqlStr )) )
		{
			while ( ($row = $db->NextRow( $rs )) )
			{
				$days .= date("d",$row['day']) . "/" . date("m",$row['day']) . "\n";
			}
			$db->EndQuery($rs );
		}
	}
	elseif ( $_REQUEST['save'] != "" )
	{
		$days = $_REQUEST['days'];

		$sqlStr = "DELETE FROM timer_holidays WHERE day BETWEEN '" . mktime(0,0,0,1,1,$year) . "' AND '" . (mktime(0,0,0,1,1,$year+1) - 1) . "'";
		$db->Query( $sqlStr );

		/* First delete all holidays that year */
		$datesArray = explode("\n", $days);
		foreach ( $datesArray as $date )
		{
			$date = trim($date);
			$dateArray = explode("/", $date);
			if ( count($dateArray) > 1 )
			{
				$values = array( "day" => mktime(0,0,0,$dateArray[1],$dateArray[0],$year) );
				$sqlStr = "INSERT INTO timer_holidays " . $db->BuildSQLString( $db->BUILD_INSERT, $values);
				$db->Query( $sqlStr );
			}
		}
	}
?>
<html>
<head>
	<title>Timeregnskap</title>
	<link rel="stylesheet" href="timer.css" media="screen" type="text/css" />
<SCRIPT type="text/javascript">
<!--//
function PopUp( url, width, height, status, toolbar, scrollbars )
{
	if (width == null) w = 600; else w = width;
	if (height == null) h = 400; else h = height;
	if (status == null) st = 0; else st = status;
	if (toolbar == null) tb = 0; else tb = toolbar;
	if (scrollbars == null)	sb = 1; else sb = scrollbars;

  	open(url,"_blank","status="+st+",toolbar="+tb+",scrollbars="+sb+",resizable=1,width="+w+",height="+h+",screenX=30,screenY=30,left=30,top=30");
}
//-->
</script>
</head>
<body>

<h2>Helligdager</h2>

<p>Her kan du legge inn helligdager for et år. Disse vil merket av
i loggføringen og bli med i beregningen for standard månedsverk.
Lørdag og søndag regnes automatisk som helligdag. Disse helligdagene vil
gjelde for alle klienter og brukere.
</p>

<p>Formatet på datoene er slik: dag/måned. Både dag og måned er et siffer. 21. mars skal f.eks. angis
slik: 21/3. SKRIV KUN EN DATO PR LINJE!
</p>

<form action="<? print $_lib['sess']->dispatch."t=timereg.holidays"; ?>" method="post">
<select name="year">
<?php for ($y = 2005; $y < 2036; $y++) {?>
<option<?php if ( $_REQUEST['year'] == $y ) {?> selected<?php }?>><?php echo($y); ?></option>
<?php }?>
</select>

<p><input type="submit" name="show" value="Hent lagrede dager"></p>

<textarea cols="40" rows="20" name="days">
<?php echo($days);?>
</textarea>

<p><input type="submit" name="save" value="Lagre"></p>
</form>

<?PHP
	$db->Disconnect();
?>
</table>

</body>
</html>
