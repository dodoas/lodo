<?php
/**
 * Klasse for å holde orden på innrapportert arbeidsgiveravgift.
 */
includemodel("arbeidsgiveravgift/savetable");
class feriepenger_grid
{
    private $debug = false;
	/**
	 * Constructor
	 *
	 * @return feriepenger_grid
	 */
	function __construct()
	{
		
	}
	/**
	 * setData; legge inn nye data (en linje)
	 *
	 * @param unknown_type $params
	 */
	function setData($params, $midparams = "", $year = "")
	{
		$tableName  = "feriepenger";
		$myFK       = $tableName . "_" . $midparams . "feriepengerID";
		
		if($this->debug) print "FK: #$myFK#, params liste<br>\n";
		if($this->debug) print_r($params);
		
		$myTab      = new SaveTable($tableName, $params[$myFK]);
		$myFieldList = $myTab->getFields();
		foreach ($myFieldList as $field)
		{
			$fieldname = $field["name"];
			$key = $tableName . "_" . $midparams . $fieldname;
			
			if (substr($field["type"], 0,3) == "dec")
			{
				$params[$key] = $this->unformatNumber($params[$key]);
			}
			if ($fieldname != "ts_created" && $fieldname != "ts_modified" && $fieldname != "modified_by")
                
				$myTab->set($fieldname, $params[$key]);
     			if($this->debug) print "Setter " . $fieldname . " til: " . $params[$key] . " #$key#<br>\n";
		}

		$myTab->set("ts_modified", time());
		$myTab->set("modified_by", "");
		if ($params[$myFK] == "")
			$myTab->set("ts_created", time());
		return $myTab->save();
		
	}
	/**
	 * setDataMulti: Legge inn data / oppdatere data fra flere linjer
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function setDataMulti($params, $year = "")
	{
		$tableName = "feriepenger";
		$continue = true;
		$i = 0;
		while ($continue)
		{
			$mySjekkVar = $tableName . "_" . $i . "_AccountPlanID";
			if ($params[$mySjekkVar] != "")
			{
				$ret[] = $this->setData($params, $i . "_", $year);
			}
			else
				$continue = false;
			$i++;
		}
		return $ret;
	}

	function selectYear ($aar)
	{
		$this->year = $aar;
	}
	function gridPersonList ()
	{
		global $_lib;
		$query = "select AccountPlanID from accountplan where AccountPlanType='employee' order by AccountPlanID;";
		if($this->debug) print "$query<br>\n";
		$result = $_lib['db']->db_query($query);
		while($row = $_lib['db']->db_fetch_object($result))
			$returnhash[] = $row->AccountPlanID;
        return $returnhash;
	}
	function gridPerson ($person)
	{
		global $_lib;
		$this->queryYear = "select fp.*, ap.AccountPlanID, ap.AccountName from feriepenger fp, accountplan ap where fp.year='" . $this->year . "' and fp.AccountPlanID = ap.AccountPlanID and ap.AccountPlanID = '" . $person . "';";
        if($this->debug) print "$this->queryYear<br>\n";
        $this->resultYear = $_lib['db']->db_query($this->queryYear);
        $res =  $_lib['db']->db_fetch_object($this->resultYear);
        if ($res->AccountPlanID == "")
        {
        	$this->queryYear = "select AccountPlanID, AccountName from accountplan where AccountPlanID = '" . $person . "';";
            if($this->debug) print "$this->queryYear<br>\n";
        	$this->resultYear = $_lib['db']->db_query($this->queryYear);
        	$res =  $_lib['db']->db_fetch_object($this->resultYear);
        }
        if ($person == "1000")
        {
		    $this->queryYear = "select * from feriepenger where year='" . $this->year . "' and AccountPlanID = '" . $person . "';";
            if($this->debug) print "$this->queryYear<br>\n";
            $this->resultYear = $_lib['db']->db_query($this->queryYear);
        	$res =  $_lib['db']->db_fetch_object($this->resultYear);
        }
        $myVar = "feriepengerID";
        $ret[$myVar] = $res->$myVar;
        $myVar = "year";
        $ret[$myVar] = $res->$myVar;
        $myVar = "AccountPlanID";
        $ret["nr"] = $res->$myVar;
        $myVar = "Grunnlag";
//         $ret[$myVar] = $res->$myVar;
        
        $firstDate = $this->year . "-01-01";
		$lastDate = $this->year . "-12-31";
		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.JournalDate>='$firstDate' and S.JournalDate<='$lastDate' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '$person';";
		if($this->debug) print "$query<br>";
		$totalThisYear = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.JournalDate >= '$firstDate' and S.JournalDate <= '$lastDate' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '$person';";
		if($this->debug) print "$query<br>";
		$totalThisYearFradrag = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag = $totalThisYear->total - $totalThisYearFradrag->total;
        $ret[$myVar] = $fpGrunnlag;
        
        $myVar = "Prosentsats";
        if ($res->$myVar != "" || $person == 1000)
        	$ret[$myVar] = $res->$myVar;
        else
        	$ret[$myVar] = 10.2;
        $myVar = "Utbetalt";
        $ret[$myVar] = $res->$myVar;
        $myVar = "ArbeidsgiveravgSats";
        
        if ($person != 1000)
        {
        	$query = "select ag.Percent, a.BirthDate from accountplan a, kommune k, arbeidsgiveravgift ag where a.KommuneID = k.KommuneID and ag.Code = k.Sone and a.AccountPlanID = '$person';";
        	if($this->debug) print "$query<br>";
        	$agPercent = $_lib['storage']->get_row(array('query' => $query));
        	list($fDato, $tull) = split(" ", $agPercent->BirthDate);
        	list($y, $m, $d) = split("-", $fDato);
        		$ret[$myVar] = $agPercent->Percent;
        }	
		if ($res->$myVar != "")
        	$ret[$myVar] = $res->$myVar;
        $myVar = "AccountName";
        $ret["Navn"]    = $res->$myVar;
        $ret["Feriepenger"] = $ret["Grunnlag"] * $ret["Prosentsats"] / 100;
        $ret["Rest"]    = $ret["Feriepenger"] - $ret["Utbetalt"];
        $ret["ArbeidsgiveravgiftBelop"] = $ret["Rest"] * $ret["ArbeidsgiveravgSats"] / 100;
        if ($ret["Prosentsats"] == 0)
	        $ret["SkyldigFeriepengeGrunnlag"] = 0;
	    else
            $ret["SkyldigFeriepengeGrunnlag"] = $ret["Rest"] / ($ret["Prosentsats"] / 100);
        $this->sumRestFeriepenger += $ret["Rest"];
        $this->sumRestArbeidsgiveravgift += $ret["ArbeidsgiveravgiftBelop"];
        return $ret;
	}
	function restArbeidsgiveravgift ()
	{
		return $this->sumRestArbeidsgiveravgift;
	}
	function restFeriepenger ()
	{
		return $this->sumRestFeriepenger;
	}
	function unformatNumber($value)
	{
		return str_replace(",", ".", str_replace("|", ".", str_replace(" ", "", $value)));
	}
}
?>