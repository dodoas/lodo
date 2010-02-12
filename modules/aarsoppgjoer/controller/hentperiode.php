<?php
/*
 * Created on Sep 28, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 


 
 	$id = (int)$_REQUEST['id'];
 	
 	if (isset($_REQUEST['start'])) {
 		$start = trim($_REQUEST['start']);
 	}
 	if (empty($start)) {
 		$start = '0000-00';
 	}

 	if (isset($_REQUEST['slutt'])) {
 		$slutt = trim($_REQUEST['slutt']);
 	}
 	if (empty($slutt)) {
 		$slutt = '9999-99';
 	}
 	
 	$b = Bilag::fraBalanse($start, $slutt);
	$b->lagreTmp(1);

	$a = "input_manual";
	require($a.".php");
 
?>
