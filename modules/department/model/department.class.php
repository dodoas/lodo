<?

class lodo_department {

  public function __construct($args) {
    foreach($args as $key => $value) {
        $this->{$key} = $value;
    }
  }

  public function getDepartmentIDAndName() {
    global $_lib;
    $query_department = sprintf("SELECT * FROM department WHERE DepartmentID = %d", $this->DepartmentID);
    $department_row = $_lib['storage']->get_row(array('query' => $query_department));
    $department_id_and_name = $department_row->DepartmentID . " - " . $department_row->DepartmentName;
    return $department_id_and_name;
  }
}
?>
