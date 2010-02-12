<?php
/*
 * Created on Sep 28, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 
 
 	$b = $_REQUEST['bilag'];
 	$id = (int)$_REQUEST['id'];

	$bi = new Bilag();
	$bi->load($id);
 	
	if ($b == 1) {
		if ($bi->getCount() > 0) {
			$bi->lagreTmp(1);
		} else {
			$input_manual_bilag_ikke_funnet = "Beklager, bilag $id finnes ikke";
		}
	} else if ($b == 2) {
		if ($bi->getCount() > 0) {
			$bi->lagreTmp(2);
		} else {
			$input_manual_bilag_ikke_funnet = "Beklager, bilag $id finnes ikke";
		}
	}
	
	$a = "input_manual";
	require($a.".php");
 
?>
