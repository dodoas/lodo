<?php

class Avdeling {

	public $id;
	public $navn;
	
	function __construct($id, $navn) {
		$this->id = $id;
		$this->navn = $navn;
	}
	
	/**
	* @return an array of avdeling instances, sorted by id
	*/
	static function find_all() {
	    global $_lib;
		//$query = "select CompanyDepartmentID as id, DepartmentName as name from companydepartment where active = 1 order by 2";

		// workaround. ser ut til å være en bug i lodo som ikke lar deg gjøre alle avdelinger aktive..
		$query = "select CompanyDepartmentID as id, DepartmentName as name from companydepartment order by 2";
		#print "$query<br>\n";
		// kjør spørring
		$lodo = new lodo();
		$db = new Db($lodo);

		$rs = $_lib['db']->db_query($query);
		
		$depts = array();
		while ($row = $db->NextRow($rs)) {
			$depts[$row['id']] = new Avdeling($row['id'], $row['name']);
		}
		#print_r($depts);
		return $depts;
	}
	
	
}

?>

