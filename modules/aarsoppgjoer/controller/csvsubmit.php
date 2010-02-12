<?php
/*
 * Created on 07.sep.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 


	$a = "input_manual";
 	$error = null;
 
 	$fname = $_FILES['upfile']['tmp_name'];
 	$size = $_FILES['upfile']['size'];
 	$error = $_FILES['upfile']['error'];
 	
 	$file = fopen($fname, "r");
 	
 	$nye_kontoer = array();
 	
	$startnr = Bilag::nyttBilagsNummer($db);
	$b1 = new Bilag("2007-12-31", "2007-12", null, $startnr);
	$b2 = new Bilag("2008-12-31", "2008-12", null, $startnr + 1);
 	while (!feof($file)) {
		$buffer = fgets($file, 4096);
		$arr = split(";", $buffer);
	 	switch ($_REQUEST['format']) {
	 		case 'semikndkdk':
	 			$kto = (int)$arr[0];
	 			if ($kto != 0 && count($arr) == 6) {
	 				$ktonavn = str_replace('"', '', $arr[1]);
	 				$bel1 = new Beloep(0);
	 				$bel2 = new Beloep(0);
	 				$bel1->setVal($arr[4], $arr[5]);
	 				$bel2->setVal($arr[2], $arr[3]);
	 				$b1->ny_postering(new Postering($kto, $bel1, 0));
	 				$b2->ny_postering(new Postering($kto, $bel2, 0));
	 				$nye_kontoer[] = new Konto($kto, $ktonavn);
	 			}
	 			break;
	 		case 'semikdkdk':
	 			break;
	 		case 'semiknvv':
	 			break;
	 		case 'semikvv':
	 			break;
	 		default:
	 			die("Beklager, feil på inputdata");
	 	}
	}
	fclose($file);


	$kontoer = Konto::getListe();
	
	$kto_by_num = array();
	foreach ($kontoer as $kto) {
		$kto_by_num[$kto->nummer] = $kto;
	}
	
	// sjekk at alle kontoer er med fra excel
	$missing = array();
	foreach ($nye_kontoer as $i => $kto) {
		if (!isset($kto_by_num[$kto->nummer])) {
			$missing[] = $kto->nummer;
		}
	}
	if (count($missing) > 0) {
		$a = "error";
		$error =  "<p>Følgende kontoer er ikke registrert i lodo:<br />\r\n";
		$i = 0;
		foreach ($missing as $mis) {
			$error .= ($i++ > 0) ? "<br />\r\n" : "";
			$error .= $mis;
		}
		$error .= "</p>";
		
		$error .= "<p>Litt om hvordan legge til konto her...</p>";
	}
	
 	if ($error != null) {
 		$a = "error";
 	} else {
		$b1->lagreTmp(1);
		$b2->lagreTmp(2);
 		require("input_manual.php");
 	}
 	
	

?>
