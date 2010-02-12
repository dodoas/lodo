<?
$primarykey['Code'] = $_REQUEST['Code'];  

if($_REQUEST['action_inberarbeidsgiveravgift_update']) {
  #Possible to extend or alter parameters here
  $arbAvgSave = new arbeidsgiveravgift_grid();
  $arbAvgSave->setDataMulti($_POST);
}
?>