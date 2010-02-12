<?php

	$avdeling = 0;
	if (!empty($_REQUEST['avdeling'])) {
		$avdeling = (int)$_REQUEST['avdeling'];
	}
	$_SESSION['avdeling'] = $avdeling;
	
	$a = 'input_manual';
	require("$a.php");

?>

