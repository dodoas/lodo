<?php
#print_r(_SETUP);
/*
 * Enkel front-controller/dispatcher
 */
?>
<?php
	//for databasetilkobling
	require_once $_SETUP['HOME_DIR']."/modules/altinn/_include/class_database.php";
	require_once $_SETUP['HOME_DIR']."/modules/altinn/_include/class_lodo.php";
	includemodel('accounting/accounting');
	$accounting = new accounting();
	
	//egne modeller
	includemodel("aarsoppgjoer/konto");
	includemodel("aarsoppgjoer/linje");
	includemodel("aarsoppgjoer/beloep");
	includemodel("aarsoppgjoer/bilag");
	includemodel("aarsoppgjoer/postering");
	includemodel("aarsoppgjoer/periode");
	includemodel("aarsoppgjoer/avdeling");

	@session_start();
	
	if (isset($_REQUEST['a'])) {
		$a = $_REQUEST['a'];
		$a = preg_replace("/[^a-zA-Z0-9-_]/", "", $a);
		if (empty($a) || $a == '') {
			$a = 'input_manual';
		}
	} else {
		$a = 'input_manual';
	}

	include($_SETUP['HOME_DIR'] . "/modules/aarsoppgjoer/controller/$a.php");
?>
<html>
<head>
	<title><?php print $title; ?></title>
	
	
<?php

	if ($a != 'review' && $a != 'review_diff') {
?>
	
    <link rel="stylesheet"          	title="Default" href="/css/default_lodo.css" media="screen" type="text/css" />
    <link rel="stylesheet"            	title="Default" href="/css/default_lodo.css" media="print" type="text/css" />
    <link rel="alternate stylesheet" 	title="Test"    href="/template/css/dfds_large.css" type="text/css" title="Default" >
    
    <link rel="stylesheet"          href="/css/sla_intranett.css" media="screen" type="text/css" />
    <link rel="icon"                href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon"       href="favicon.ico" type="image/x-icon" />
    
</head>
	
<body>
<?
	print "<!-- header starts -->";
	includeinc('head');
	print "<!-- header ends -->";
	print "<!-- leftmenu starts -->";
	#includeinc('leftmenu');
	print "<!-- leftmenu ends -->";


	} else {		// spesiell header for rapporter
	?>
	    <link rel="stylesheet"          	title="Default" href="/css/default_lodo.css" media="screen" type="text/css" />
	<?php
	}

	include($_SETUP['HOME_DIR']."/modules/aarsoppgjoer/component/$a.php");
?>

</body>
</html>
