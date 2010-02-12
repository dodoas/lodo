<?php
/**
 * Klasse for å holde orden på innrapportert arbeidsgiveravgift.
 */
includemodel("arbeidsgiveravgift/savetable");
class arbeidsgiveravgift_grid
{
	/**
	 * Constructor
	 *
	 * @return arbeidsgiveravgift_grid
	 */
	function arbeidsgiveravgift_grid ()
	{
		$this->termin = 1;
	}
	/**
	 * setData; legge inn nye data (en linje)
	 *
	 * @param unknown_type $params
	 */
	function setData($params, $midparams = "")
	{
	    global $_lib;
		$tableName = "inbarbeidsgiveravgift";
		$myFK = $tableName . "_" . $midparams . "inbarbeidsgiveravgiftID";
		
		$myTab = new SaveTable($tableName, $params[$myFK]);
		$myFieldList = $myTab->getFields();
		foreach ($myFieldList as $field)
		{
			$fieldname = $field["name"];
			if (substr($field["type"], 0,3) == "dec")
			{
				$params[$tableName . "_" . $midparams . $fieldname] = $_lib['convert']->Amount($params[$tableName . "_" . $midparams . $fieldname]);
			}
				$myTab->set($fieldname, $params[$tableName . "_" . $midparams . $fieldname]);
 				//print "Setter " . $fieldname . " til: " . $params[$tableName . "_" . $midparams . $fieldname] . "<br>\n";
		}
		return $myTab->save();
		
	}
	/**
	 * setDataMulti: Legge inn data / oppdatere data fra flere linjer
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function setDataMulti($params)
	{
		$tableName = "inbarbeidsgiveravgift";
		for($i = 1; $i < 7; $i++)
		{
			$mySjekkVar = $tableName . "_" . $i . "_year";
			if ($params[$mySjekkVar] != "")
			{
				$ret[] = $this->setData($params, $i . "_");
			}
		}
		$mySjekkVar = $tableName . "_20_year";
		if ($params[$mySjekkVar] != "")
			$ret[] = $this->setData($params, "20_");
		$mySjekkVar = $tableName . "_30_year";
		if ($params[$mySjekkVar] != "")
			$ret[] = $this->setData($params, "30_");
		return $ret;
	}
	
	function selectYear ($aar)
	{
		$this->year = $aar;
	}

	function gridNextTermin ($termin)
	{
		global $_lib;
		$this->queryYear = "select * from inbarbeidsgiveravgift where year='" . $this->year . "' and termin='" . $termin . "';";
        #print "$this->queryYear<br>\n";
        $this->resultYear = $_lib['db']->db_query($this->queryYear);
        return $_lib['db']->db_fetch_object($this->resultYear);
	}
	function sumTerminer ()
	{
		global $_lib;
		$this->queryYear = "select ";
		for ($i = 1; $i < 6; $i++)
			$this->queryYear .= "SUM(S" . $i . "grunnbelop_u62) as S" . $i . "grunnbelop_u62, SUM(S" . $i . "grunnbelop_o62) as S" . $i . "grunnbelop_o62, ";
		$this->queryYear .= "SUM(forskuddstrekk_u62) as forskuddstrekk_u62, SUM(forskuddstrekk_o62) as forskuddstrekk_o62 ";
		$this->queryYear .= "from inbarbeidsgiveravgift where year='" . $this->year . "' and termin < 10;";
        $this->resultYear = $_lib['db']->db_query($this->queryYear);
//         $this->Result = $_lib['db']->db_fetch_object($this->resultYear);
        return $_lib['db']->db_fetch_object($this->resultYear);
	}
}
?>