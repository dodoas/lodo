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
		//$query = "select DepartmentID as id, DepartmentName as name from department where active = 1 order by 2";

		// workaround. ser ut til � v�re en bug i lodo som ikke lar deg gj�re alle avdelinger aktive..
		$query = "select DepartmentID as id, DepartmentName as name from department order by 2";
		#print "$query<br>\n";
		// kj�r sp�rring
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

