<?php
/*
 * Created on 29.aug.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */



	  

	$error = null;

	// parse input data
	$b1 = new Bilag();
	$b1->loadTmp(1, $db);
	
	$b2 = new Bilag();
	$b2->loadTmp(2, $db);
	
 	$linjer = Linje::prepare($b1, $b2);

	$title = "Tittel";
	
	$a = "review";
?>
