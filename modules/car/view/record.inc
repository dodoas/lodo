<?
$primarykey['CompanyCarID'] = $CompanyCarID;

if($_lib['input']->getProperty('action_car_update')) {
  #Possible to extend or alter parameters here
  #print_r($_POST);
  #print_r($primarykey);
  for($milage_year = $car_purchase_year; $milage_year <= $_year; $milage_year++) {
    $post_values = array("companycarmilage_km0101" => $_POST["companycarmilage_km0101_$milage_year"], "companycarmilage_km3112" => $_POST["companycarmilage_km3112_$milage_year"]);
    $_lib['db']->db_update_hash($post_values, 'companycarmilage', array("CompanyCarID" => $CompanyCarID, "MilageYear" => $milage_year));
  }
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_car_new'))
{
	
	if($_lib['input']->getProperty('companycar_CompanyCarID') > 0) {

		$query  = "select CompanyCarID from $db_table where CompanyCarID=" . (int) $_lib['input']->getProperty('companycar_CompanyCarID') . "";
		$result = $_lib['db']->db_query($query);
		if($_lib['db']->db_numrows($result) == 0)
		{
			$post = $_POST;
			$post['companycar_ValidFrom'] = "NOW()";
			$post['companycar_Active']  = 1;
	
			$query  = "select CompanyCarID from $db_table where CompanyCarID=" . (int) $_lib['input']->getProperty('companycar_CompanyCarID') . "";
			$_lib['db']->db_insert2(array('query'=>"insert into companycar (CompanyCarID, ValidFrom, Active) values (". (int) $post['companycar_CompanyCarID'].", ". $post['companycar_ValidFrom'] .", '". (int) $post['companycar_Active']."')"));
			$CompanyCarID = $_POST['companycar_CompanyCarID'];
		}
		else
		{
			$CompanyCarID = $_POST['companycar_CompanyCarID'];
		}
	} else {
		$_lib['message']->add("Bilnummer ikke oppgitt, pr&oslash;v p&aring; nytt");
	}
}
elseif($_lib['input']->getProperty('action_car_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
  # delete all milage for the car
  $query  = "DELETE FROM companycarmilage WHERE CompanyCarID=" . (int) $CompanyCarID . "";
  $result = $_lib['db']->db_query($query);
}

if (!$_lib['input']->getProperty('action_car_delete') && $CompanyCarID) {
  $companycar = $_lib['storage']->get_row(array('query' => "select * from $db_table where CompanyCarID = $CompanyCarID"));
  $car_purchase_year = date('Y', strtotime($companycar->ValidFrom));

  for($milage_year = $car_purchase_year; $milage_year <= $_year; $milage_year++) {
    $_milage_exists_query = "SELECT * FROM companycarmilage WHERE CompanyCarID = $CompanyCarID AND MilageYear = $milage_year";
    $_milage_exists_result = $_lib['storage']->db_query($_milage_exists_query);
    $exists = ($_lib['storage']->db_numrows($_milage_exists_result) > 0);
    if (!$exists) {
      $create_milage = "INSERT INTO companycarmilage(CompanyCarID, MilageYear) VALUES ($CompanyCarID, $milage_year)";
      $_lib['storage']->db_query($create_milage);
    }
  }
}

?>
