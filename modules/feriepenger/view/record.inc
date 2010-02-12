<?
if($_lib['input']->getProperty('action_feriepenger_update')) {

	#print_r($_POST);
	#Possible to extend or alter parameters here
	$fSave = new feriepenger_grid();
	$fSave->setDataMulti($_POST, $year);
}
?>