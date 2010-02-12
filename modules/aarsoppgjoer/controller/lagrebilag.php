<?php
/*
 * Created on 05.sep.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


	$error = null;
	$a = "savesuccessful";
	
	try {
		$b1 = new Bilag();
		$b2 = new Bilag();
	
		$tmp = new Bilag();
		$tmp->loadTmp(1, $db);
		if ($tmp->getCount() > 0) {
			$b1 = $tmp;
		}
	
		$tmp = new Bilag();
		$tmp->loadTmp(2, $db);
		if ($tmp->getCount() > 0) {
			$b2 = $tmp;
		}
		
		$b1->lagre();
		$b2->lagre();
	
	 } catch (Exception $e) {
 		$error = "Det oppsto en feil under lagring. " . $e;
 		$a = "error";
 		die($error);
 	}
	
?>
