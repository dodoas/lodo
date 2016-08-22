<?
$FurloughTextID = $_POST['furloughtext_FurloughTextID'];
$FurloughText = $_POST['furloughtext_Text'];
if($_lib['input']->getProperty('action_furloughtext_add') && !empty($FurloughText)) {
  $insert_query = "insert into furloughtext (Text) values ('$FurloughText')";
  $_lib['db']->db_insert2(array('query'=>$insert_query));
}
elseif($_lib['input']->getProperty('action_furloughtext_delete') && !empty($FurloughTextID)) {
  $delete_query = "delete from furloughtext where FurloughTextID = $FurloughTextID";
  $_lib['db']->db_delete($delete_query);
}
?>
