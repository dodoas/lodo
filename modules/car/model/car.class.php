<?
class car {
  // function that replaces Active field in queries.
  // $car_id can be a fixed number, or an sql statement
  // examples:
  //   1. $car_id = 4;
  //   2. $car_id = "car.CarID"; This is in examples like: $all_active_cars = "SELECT * FROM car WHERE ". car::car_active_sql("car.CarID") ."=1;";
  //   3. $car_id = "(SELECT CarID FROM voucher WHERE voucher.VoucherID = 123)"; This sends a specific CarID for which you want to see if it is active.
  public static function car_active_sql($car_id, $reference_date = false) {
    if(!$reference_date) $reference_date = date("Y-m-d");
    $sql = "(SELECT
              CASE
                WHEN ValidTo IS NOT NULL AND ValidTo != '0000-00-00' AND (ValidFrom IS NULL OR ValidFrom = '0000-00-00') AND DATE_ADD(ValidTo, INTERVAL 30 DAY) >= '$reference_date' THEN true
                WHEN ValidFrom IS NOT NULL AND ValidFrom != '0000-00-00' AND (ValidTo IS NULL OR ValidTo = '0000-00-00') AND DATE_SUB(ValidFrom, INTERVAL 30 DAY) <= '$reference_date' THEN true
                WHEN ValidTo IS NOT NULL AND ValidTo != '0000-00-00' AND ValidFrom IS NOT NULL AND ValidFrom != '0000-00-00' AND DATE_ADD(ValidTo, INTERVAL 30 DAY) >= '$reference_date' AND DATE_SUB(ValidFrom, INTERVAL 30 DAY) <= '$reference_date' THEN true
                ELSE false
              END AS active
            FROM car as __car
            WHERE __car.CarID = $car_id)";
    return $sql;
  } 

  // function that tells if car with id = $car_id is active or not.
  public static function is_active($car_id, $reference_date = false) {
    if(!$reference_date) $reference_date = date("Y-m-d");
    global $_lib;
    $query = car::car_active_sql($car_id, $reference_date);
    $result = $_lib['db']->get_row(array('query' => $query))->active == "1";
    return $result;
  }
}
?>