<?php
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_config.php';
	require_once '_include/class_database.php';
	
	$lodo = new lodo();
	$db = new Db( $lodo );
	$layout = new Layout( $lodo );

	$layout->PrintHead( "AltInn" );

	/* Get termin type */
	$terminType = 4;

	/* We are running under Lodo/Empatix */
	if ( $lodo->inLodo )
	{
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";

		$avst = new mva_avstemming(array('_sess' => $_sess, '_dbh' => $_dbh, '_dsn' => $_dsn, '_date' => $_date, 'year' => '2005'));
		$report = $avst->reported;
	}

	foreach($report as $monthly => $tmp)
	{
		if ($monthly != 'total' && $monthly != 'percent')
		{
			foreach ($report[$monthly] as $key => $value)
			{
				echo("KEY $key<br>\n");
			}
//			echo('<br>:' . $_format->MonthToText(array('value'=>$monthly, 'return' => 'value')) . '<br>');
/*			echo(':' . $report[$monthly]['TotalOmsettning'] . '<br>');
			echo(':' . $report[$monthly]['FreeOmsettning'] . '<br>');
			echo(':' . $report[$monthly]['Grunnlag24Mva'] . '<br>');
			echo(':' . $report[$monthly]['Out24Mva'] . '<br>');
			echo(':' . $report[$monthly]['Grunnlag12Mva'] . '<br>');
			echo(':' . $report[$monthly]['Out12Mva'] . '<br>');
			echo(':' . $report[$monthly]['Grunnlag6Mva'] . '<br>');
			echo(':' . $report[$monthly]['Out6Mva'] . '<br>');
			echo(':' . $report[$monthly]['In24Mva'] . '<br>');
			echo(':' . $report[$monthly]['In12Mva'] . '<br>');
			echo(':' . $report[$monthly]['In6Mva'] . '<br>');
			echo(':' . $report[$monthly]['SumMva'] . '<br>');*/
		}
	}

	/*  */
	$layout->PrintFoot(  );
?>
