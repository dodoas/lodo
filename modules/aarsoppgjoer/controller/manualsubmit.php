<?php
/*
 * Submit handler for manuell input -skjema
 */
 
	/*
	* 1. Les form-data
	* 2. Lagre bilag i db
	* 
	*/

	$error = null;
	$a = "input_manual";
	
	
	$avdelinger = Avdeling::find_all();
	
	$avdeling = 0;
	if (!empty($_SESSION['avdeling'])) {
		$avdeling = $_SESSION['avdeling'];
	}

	
	$action = $_REQUEST['submit'];
	try {
	
		// parse input data
		$b1 = new Bilag();
		$b1->lesFraArray($_REQUEST, "bilag1");
		
		$b2 = new Bilag();
		$b2->lesFraArray($_REQUEST, "bilag2");
		
		// lagre bilag i db
		$b1->lagreTmp(1, null, $avdeling);
		$b2->lagreTmp(2, null, $avdeling);
		
	} catch (Exception $e) {
		die("En feil har oppstått: " . $e);
	}
	
	//parse data, then open review window
	$window_options = "status=1,toolbar=1,location=1,menubar=1,resizable=1,scrollbars=1";
	if ($error) {
		$a = "input_manual";
		require("$a.php");
	} else if ($_REQUEST['radiotype'] == 'direkte') {
		$_SESSION['aarsoppgjoer_type'] = 'direkte';
		
		$script = 'window.open ("?t=aarsoppgjoer.index&a=review", "Forhåndsvisning","'. $window_options .'");';
		//$a = "review";
		require("$a.php");
	} else if ($_REQUEST['radiotype'] == 'diff') {
		$_SESSION['aarsoppgjoer_type'] = 'diff';
		$script = 'window.open ("?t=aarsoppgjoer.index&a=review_diff", "Forhåndsvisning","'. $window_options .'");';
		//$a = "review_diff";
		require("$a.php");
	} else {
		$_SESSION['aarsoppgjoer_type'] = 'lagre';
		$a = "input_manual";
		require("$a.php");
	}	

?>