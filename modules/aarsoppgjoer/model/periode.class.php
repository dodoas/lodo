<?php

class periode {

    function periode() {
    }
    
    
    /*
     * Henter ut alle åpne perioder som en array av strenger. eks.: '2005-03'
     */
    function getListe() {
        global $_lib;
    	if (isset($_SESSION['login_id'])) {
    	}
    	$login_id = (int)$_SESSION['login_id'];
    	
    	$query =
			"SELECT" .
			"  period" .
			" FROM accountperiod" .
			" WHERE status <= 2" .
			" ORDER BY period";
			
		// kjør spørring
		$lodo = new lodo();
		$db = new Db($lodo);

    	$ret = array();
		$rs = $_lib['db']->db_query($query);
		while ($row = $db->NextRow($rs)) {
			$ret[] = $row['period'];
		}
    	return $ret;
    }
    
}
?>