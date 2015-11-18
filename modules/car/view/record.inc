<?
$primarykey['CarID'] = $CarID;
$car_CarID = $_lib['input']->getProperty('car_CarID');

// regex validation for buy/sell date
if (isset($_POST['car_ValidFrom']) && !preg_match('/^([0-9]{4})-([0-1][0-9])-([0-3][0-9])$/', $_POST['car_ValidFrom']))
  unset($_POST['car_ValidFrom']);
if (isset($_POST['car_ValidTo']) && !preg_match('/^([0-9]{4})-([0-1][0-9])-([0-3][0-9])$/', $_POST['car_ValidTo']))
  unset($_POST['car_ValidTo']);
// regex for year (4 digits)
if (isset($_POST['car_RegistrationYear']) && !preg_match('/^([0-9]{4})$/', $_POST['car_RegistrationYear']))
  unset($_POST['car_RegistrationYear']);

if($_lib['input']->getProperty('action_car_update')) {
  #Possible to extend or alter parameters here
  #print_r($_POST);
  #print_r($primarykey);
  for($milage_year = $car_purchase_year; $milage_year <= $_year; $milage_year++) {
    $post_values = array("carmilage_StartMilage" => $_POST["carmilage_StartMilage_$milage_year"], "carmilage_EndMilage" => $_POST["carmilage_EndMilage_$milage_year"]);
    $_lib['db']->db_update_hash($post_values, 'carmilage', array("CarID" => $CarID, "MilageYear" => $milage_year));
  }
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_car_new'))
{

  if($car_CarID > 0) {

    $query  = "select CarID from $db_table where CarID=" . (int) $car_CarID . "";
    $result = $_lib['db']->db_query($query);
    if($_lib['db']->db_numrows($result) == 0) {
      $post = $_POST;
      $post['car_ValidFrom'] = "NOW()";
      $post['car_Active']  = 1;

      $query  = "insert into car (CarID, ValidFrom, Active) values (". (int) $post['car_CarID'].", ". $post['car_ValidFrom'] .", '". (int) $post['car_Active']."')";
      $_lib['db']->db_insert2(array('query'=>$query));
    }
    $CarID = $_POST['car_CarID'];
  } else {
    $_lib['message']->add("Bilnummer ikke oppgitt, pr&oslash;v p&aring; nytt");
    header("Location: " . $_lib['sess']->dispatchs . "t=car.list");
  }
}
elseif($_lib['input']->getProperty('action_car_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
  # delete all milage for the car
  $query  = "DELETE FROM carmilage WHERE CarID=" . (int) $CarID . "";
  $_lib['db']->db_query($query);
}

// if the action being performed is not delete then update milage table 
// according to the purchase date(either add missing years or delete the surplus)
if (!$_lib['input']->getProperty('action_car_delete') && $CarID) {
  $car = $_lib['storage']->get_row(array('query' => "select * from $db_table where CarID = $CarID"));
  $car_purchase_year = date('Y', strtotime($car->ValidFrom));

  for($milage_year = $car_purchase_year; $milage_year <= $_year; $milage_year++) {
    $query_delete_surplus  = "DELETE FROM carmilage WHERE CarID=$CarID AND MilageYear < $car_purchase_year";
    $_lib['db']->db_query($query_delete_surplus);
    $_milage_exists_query = "SELECT * FROM carmilage WHERE CarID = $CarID AND MilageYear = $milage_year";
    $_milage_exists_result = $_lib['storage']->db_query($_milage_exists_query);
    $exists = ($_lib['storage']->db_numrows($_milage_exists_result) > 0);
    if (!$exists) {
      $create_milage = "INSERT INTO carmilage(CarID, MilageYear) VALUES ($CarID, $milage_year)";
      $_lib['storage']->db_query($create_milage);
    }
  }
}

?>
