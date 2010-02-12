<?php
/*
 * Created on 26.aug.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 

	$error = null;
	
	try {
	 
		unset($_SESSION['aarsoppgjoer_type']);
		unset($_SESSION['avdeling']);
		
		$query = "delete from vouchertmp";
		
		$lodo = new lodo();
		$db = new Db($lodo);
	
		if ( !$db->Connect() ) {
			print "Kunne ikke koble til databasen.";
			die();
		}
	
	   	$db->Query($query);
	   	
	} catch (Exception $e) {
		$error = "En feil har oppstått: " . $e;
	}
	
	if ($error == null) {
		$a = "input_manual";
		require("$a.php");
	} else {
		$a = "error";
	}

?>