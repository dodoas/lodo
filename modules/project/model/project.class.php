<?

class lodo_project {

  public function __construct($args) {
    foreach($args as $key => $value) {
        $this->{$key} = $value;
    }
  }

  public function getProjectIDAndName() {
    global $_lib;
    $query_project = sprintf("SELECT * FROM project WHERE ProjectID = %d", $this->ProjectID);
    $project_row = $_lib['storage']->get_row(array('query' => $query_project));
    $project_id_and_name = $project_row->ProjectID . " - " . $project_row->Heading;
    return $project_id_and_name;
  }
}
?>
