<?php
/*
 * Henter bilag fra db. Bruker tomt bilag om det ikke finnes
 * noe bilag i her, eller dette er uten posteringer.
 */

 
 	$title = "Tittel";
 	
 	//print_r($_SESSION);
 	$error = null;
 	
	$lodo = new Lodo();

	$db = new Db($lodo);
	
	$avdelinger = Avdeling::find_all();
	
	$avdeling = 0;
	if (!empty($_SESSION['avdeling'])) {
		$avdeling = $_SESSION['avdeling'];
	}
	$startnr = Bilag::nyttBilagsNummer($db);

	$b1 = new Bilag("2008-12-31", "2008-12", array(), $startnr);
	$b2 = new Bilag("2009-12-31", "2009-12", array(), $startnr + 1);
	$tmp = new Bilag();
	$tmp->loadTmp(1, $db);
	if ($tmp->getNummer() != 0) {
		$b1 = $tmp;
	}
	$tmp = new Bilag();
	$tmp->loadTmp(2, $db);
	if ($tmp->getNummer() != 0) {
		$b2 = $tmp;
	}
	
 	$linjer = Linje::prepare($b1, $b2);
	
	
 	if ($error != null) {
 		$a = "error";
 	}

?>
