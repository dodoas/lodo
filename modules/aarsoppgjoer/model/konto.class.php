<?php

class Konto {
	public $nummer;
	public $navn;
	public $er_reskontro;
	public $har_avdeling;
	public $AccountPlanType;

    function Konto($nummer, $navn, $har_avdeling = false, $er_reskontro = false, $AccountPlanType) {
    	$this->nummer           = (int)$nummer;
    	$this->navn             = (string)$navn;
		$this->har_avdeling     = $har_avdeling;
		$this->er_reskontro     = $er_reskontro;
		$this->AccountPlanType  = $AccountPlanType;
		#print_r($this);
    }
    
    function nu() {
    	return $this->nummer;
    }
	
    /*
     * Henter ut alle aktive kontoer (hvor navn ikke er null) 
     */
    static function getListe() {
        global $_lib;
    	$query =
			"SELECT" .
			" a.AccountPlanID" .
			", a.AccountName" .
			", a.EnableDepartment" .
			", a.EnableReskontro" .
			", a.AccountPlanType" .
			" FROM accountplan AS a" .
			" WHERE a.active = 1" .
			" AND a.accountname IS NOT NULL" .
			" ORDER BY a.AccountPlanType, a.accountplanid";

		$lodo = new lodo();
		$db = new Db($lodo);
		
    	$ret = array();
		$rs = $_lib['db']->db_query($query);
		while ($row = $db->NextRow($rs)) {
			$ret[$row['AccountPlanID']] = new Konto($row['AccountPlanID'], $row['AccountName'], $row['EnableDepartment'], $row['EnableReskontro'], $row['AccountPlanType']);
		}
    	return $ret;
    }
    
}
?>