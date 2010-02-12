<?php

class bilag {
	public $posteringer;
	public $journal_id;
	public $dato;
	public $period;
	public $type;
	public $desc;
	
    function __construct($dato = '2009-01-01', $period = '2009-01', $posteringer = array(), $bilagsNummer = null) {
        global $_lib;
        $_lib['sess']->debug($query);
    	$this->posteringer = $posteringer;
    	$this->journal_id = (int)$bilagsNummer;
    	$this->dato = $dato;
    	$this->period = $period;
    	$this->type = 'K';
    	$this->desc = 'Fra årsoppgjør';
    }
    
    function getPosteringer() {
    	return $this->posteringer;
    }
    
    function getNummer() {
    	return $this->journal_id;
    }
    
    function getDate() {
    	return $this->dato;
    }
    
    function getPeriod() {
    	return $this->period;
    }
    
    /*
     * @return antall linjer i bilaget
     */
    function getCount() {
    	return count($this->posteringer);
    }
    
    /*
    * Leser ut et bilag fra $_SESSION eller $_REQUEST
    * @param $arr $_SESSION, $_REQUEST eller en annen array
    * @param $prefix unikt prefiks som identifiserer bilaget i denne arrayen
    * @return en instans av denne klassen 
    */
    function lesFraArray($arr, $prefix) {
		$values = array();
		foreach($arr as $key => $element) {
			$key = trim($key);
			if (stripos($key, $prefix) === 0) {
				/*
				* Ok, vi har en match. Gjør noe fornuftig med dataene her. ;)
				*/
				$key = substr($key, strlen($prefix));
				$off = strpos($key, "-");
				$line = 0;
				if ($off > 0) {
					$line = substr($key, 0, $off);
					$key = substr($key, $off + 1);
					if (!isset($values[$line])) {
						$values[$line] = array();
					}
					$values[$line][$key] = $element;
				} else if ($off === 0) {
					if ($key == "-nr") {
						$this->journal_id = $element;
					} else if ($key == "-dato") {
						$this->dato = $element;
					} else if ($key == "-periode") {
						$this->period = $element;
					}
				}
			}
		}
		$posteringer = array();
		$avdelinger = Avdeling::find_all();
		foreach ($values as $value) {
			$deb = trim($value['deb']);
			$kred = trim($value['kred']);
			$kto = trim($value['kto']);
			$avd = trim($value['avd']);
			// har vi data?
			if ( !empty($kto) && (!empty($deb) || !empty($kred)) ) {
				$b = new Beloep();
				$b->setVal($deb, $kred);
				if (!empty($avd)) {
					if (isset($avdelinger[$avd])) {
						$a = $avdelinger[$avd];
					} else {
						$a = new Avdeling($avd, "X");
					}
				} else {
					$a = null;
				}
				$posteringer[] = new Postering($kto, $b, (int)$value['id'], $a);
			}
		}
		$this->posteringer = $posteringer;
    }
    
    function removeFromArray(&$arr, $prefix) {
    	foreach ($arr as $key => $val) {
			if (stripos($key, $prefix) === 0) {
				unset($arr[$key]);
			}
    	}
    }
    
    function skrivTilArray(&$arr, $prefix) {
    	Bilag::removeFromArray($arr, $prefix);
    	
    	//lagre bilaget
    	$i = 0;
		foreach($this->posteringer as $postering) {
			$index = $prefix . $i . "-";  
			
			$arr[$index."deb"] = $postering->getBeloep()->getDebet();
			$arr[$index."kred"] = $postering->getBeloep()->getKredit();
			$arr[$index."kto"] = $postering->getKonto();
			$arr[$index."id"] = $postering->getId();
			$arr[$index."avd"] = $postering->getAvdeling()->id;
			
			$i++;
		}
		$arr[$prefix . "-nr"] = $this->journal_id;
		$arr[$prefix . "-dato"] = $this->dato;
		$arr[$prefix . "-periode"] = $this->period;
    }
    
    function settBilagsNummer($bilagsnummer, $db) {
   		$this->journal_id = $bilagsnummer;
   		return Bilag::sjekkBilagsNummer($bilagsnummer, $db);
    }
    
    function ny_postering($postering) {
    	$this->posteringer[] = $postering;
    }
    
    static function sjekkBilagsNummer($bilagsnummer, $db) {
        global $_lib;
    	$rs = $_lib['db']->db_query("SELECT COUNT(*) AS antall FROM voucher WHERE JournalID = '$bilagsnummer' GROUP BY JournalID");
    	$count = 0;
		if ($row = $db->NextRow($rs)) {
			$count = $row['antall'];
		}
		if ($count > 0) {
			return false;
		}
    	return true;
    }
    
    static function nyttBilagsNummer(&$db = null) {
        global $_lib;
    	if ($db == null) {
			$lodo = new lodo();
			$db = new Db($lodo);
    	}

    	$rs = $_lib['db']->db_query("SELECT COALESCE(MAX(JournalID), 0) + 1 AS nytt FROM voucher WHERE VoucherType = 'K'");
    	$ret = 0;
		if ($row = $db->NextRow($rs)) {
			$ret = $row['nytt'];
		}
    	return $ret;
    }
    
    /*
     * @param fra periode som tekst. YYYY-MM
     * @param til periode som tekst. YYYY-MM
     * @return et bilag med balansen for alle kontoer i aktuelt tidsrom
	 * TODO: ta hensyn til avdelinger
     */
    static function fraBalanse($fra = '0000-00', $til = '9990-99') {
        global $_lib;
	    try {
	    	$query = "SELECT a.accountplanid AS account," .
	    			" a.accountname AS name," .
	  				" SUM(v.amountin - v.amountout) AS amount," .
					" v.DepartmentID AS departmentid" .
	  				" FROM accountplan AS a" .
					" LEFT OUTER JOIN voucher AS v ON v.accountplanid = a.accountplanid" .
					" WHERE a.active = 1" .
					" AND a.EnableResKontro = 0" .
					" AND (v.voucherperiod >= '" . $fra . "' OR v.voucherid IS NULL)" .
					" AND (v.voucherperiod < '" . $til . "' OR v.voucherid IS NULL)" .
					" GROUP BY a.accountplanid, v.DepartmentID" .
					" HAVING amount <> '0.00'" .
					" ORDER BY a.accountplanid";
				
			$lodo = new lodo();
			$db = new Db($lodo);
				
			$avdelinger = Avdeling::find_all();
	
	    	//print "<pre>query: $query</pre>";
    		$_lib['sess']->debug($query);
			$rs = $_lib['db']->db_query($query);
	    	$posteringer = array();
			while ($row = $db->NextRow($rs)) {
				$acc = $row['account'];
				$amt = "".$row['amount'];
				$avd = $row['departmentid'];
				
				$dato = "" . $til . "-01";
				$period = $til;
				
				if (isset($avdelinger[$avd])) {
					$avdeling = $avdelinger[$avd];
				} else {
					$avdeling = new Avdeling($avd, "X");
				}
				
				$p = new Postering($acc, new Beloep($amt), null, $avdeling);
				
				$posteringer[] = $p;
			}
			$b = new Bilag($dato, $period, $posteringer, null);
	    } catch (Exception $e) {
	    	print "Error: $e";
	    }
    	return $b;
	}
    
    /*
     * Summerer alle posteringer for bilaget.
     */
    function sum() {
    	$sum = 0.0;
    	foreach ($this->posteringer as $postering) {
    		$sum += $postering->getBeloep()->getVal();
    	}
    	if (abs($sum) < 0.0001) {
    		return 0;
    	} else {
    		return $sum;
    	}
    }
    
    /*
     * Henter posteringer for dette bilagsnummeret fra databasen.
     * 
     * @return antall posteringer i bilaget
     */
    function load($bilagsnummer, &$db = null) {
        global $_lib;
    	if ($db == null) {
			$lodo = new lodo();
			$db = new Db($lodo);
    	}

		if ( !$db->Connect() ) {
			print "Kunne ikke koble til databasen.";
			die();
		}

		$avdelinger = Avdeling::find_all();

		$query =
			"SELECT v.VoucherID, v.AccountPlanID, v.AmountIn, v.AmountOut, v.VoucherDate,".
			" v.VoucherPeriod, v.VoucherType, v.Description, v.DepartmentID".
			" FROM voucher AS v ".
			" INNER JOIN accountplan AS a ON v.AccountPlanID = a.AccountPlanID".
			" WHERE v.JournalID = '$bilagsnummer'".
			" AND a.EnableResKontro = 0".
			" ORDER BY AccountPlanID, VoucherID";
		 
		$_lib['sess']->debug($query);
    	$rs = $_lib['db']->db_query($query);
    	
    	$ret = 0;
    	$posteringer = array();
		while ($row = $db->NextRow($rs)) {
			$id = $row['VoucherID'];
			$acc = $row['AccountPlanID'];
			$in = "".$row['AmountIn'];
			$out = "".$row['AmountOut'];
			$dato = $row['VoucherDate'];
			$period = $row['VoucherPeriod'];
			$avdeling = $avdelinger[$row['DepartmentID']];
			$this->desc = $row['Description'];
			$this->type = $row['VoucherType'];
			
			$bel = new Beloep();
			$bel->setVal($in, $out);
			$p = new Postering($acc, $bel, $id, $avdeling);
			
			$this->dato = $dato;
			$this->period = $period;
			
			$posteringer[] = $p;
			$ret++;
		}
		$this->journal_id = $bilagsnummer;
		$this->posteringer = $posteringer;
    	return $ret;
    }
    

    /*
     * Henter posteringer for dette bilagsnummeret fra temp-tabell.
     * 
     * Bilagsnummeret fra skjema ligger i journal id, bruker quantity-feltet
     * for å identifisere bilaget her.
     * 
     * @return antall posteringer i bilaget
     */
    function loadTmp($temp_id, &$db = null) {
        global $_lib;
    	if (empty($db)) {
			$lodo = new lodo();
			$db = new Db($lodo);
    	}

		if ( !$db->Connect() ) {
			print "Kunne ikke koble til databasen.";
			die();
		}
		
		$avdelinger = Avdeling::find_all();

		$query =
			"SELECT v.JournalID, v.VoucherID, v.AccountPlanID, v.AmountIn, v.AmountOut, v.VoucherDate,".
			" v.VoucherPeriod, v.VoucherType, v.Description, v.DepartmentID".
			" FROM vouchertmp AS v " .

			// trenger ikke disse lenger, ettersom hovedbokskontoer
			// ikke lagres nå
			//" AND a.EnableResKontro = 0".
			//.INNER JOIN accountplan AS a ON v.AccountPlanID = a.AccountPlanID".
			" WHERE v.Quantity = '$temp_id'";

			//" ORDER BY AccountPlanID, VoucherID";
		 
	//print "loadTmp query: " . $query;
    	$rs = $_lib['db']->db_query($query);
    	
    	$ret = 0;
    	$posteringer = array();
		while ($row = $db->NextRow($rs)) {
			$id = $row['VoucherID'];
			$acc = $row['AccountPlanID'];
			$in = "".$row['AmountIn'];
			$out = "".$row['AmountOut'];
			$dato = $row['VoucherDate'];
			$period = $row['VoucherPeriod'];

			$avd = $row['DepartmentID'];
			if (isset($avdelinger[$avd])) {
				$avdeling = $avdelinger[$avd];
			} else {
				$avdeling = new Avdeling($avd, "X");
			}
			
			$this->desc = $row['Description'];
			$this->journal_id = $row['JournalID'];
			$this->type = $row['VoucherType'];
			
			$bel = new Beloep();
			$bel->setVal($in, $out);
			$p = new Postering($acc, $bel, $id, $avdeling);
			
			$this->dato = $dato;
			$this->period = $period;
			
			//den tomme posteringen? (som kun holder bilagsnummer)
			if ($acc != 0) {
				$posteringer[] = $p;
			}
			$ret++;
		}
		$this->posteringer = $posteringer;
    	return $ret;
    }

    /*
     * Fører resultatkontoer direkte fra b_ny, og ny - fra for balansekontoer.
     * Reskontroer føres som hovedkontoen de tilhører.
     */
    static function diff($b_fra, $b_ny) {
        global $accounting;
    	$b = new Bilag();
    	$b->journal_id = $b_ny->journal_id;
    	$b->dato = $b_ny->dato;
    	$b->period = $b_ny->period;
		
		foreach ($b_fra->getPosteringer() as $postering) {
			if (!isset($fra[$postering->getKonto()][$postering->getAvdeling()->id])) {
				$fra[$postering->getKonto()][$postering->getAvdeling()->id] = 0;
			}
			$fra[$postering->getKonto()][$postering->getAvdeling()->id] += $postering->getBeloep()->getVal();
		}
		
		foreach ($b_ny->getPosteringer() as $postering) {
			if (!isset($ny[$postering->getKonto()][$postering->getAvdeling()->id])) {
				$ny[$postering->getKonto()][$postering->getAvdeling()->id] = 0;
			}
			$ny[$postering->getKonto()][$postering->getAvdeling()->id] += $postering->getBeloep()->getVal();
		}
		
    	foreach ($fra as $kto => $tmp) {
    		$temp_kto = $kto;
    		$kname = "";
    		do {
    			$kres        = $accounting->getHovedbokToAccount($temp_kto);
    			$accountplan = $accounting->get_accountplan_object($temp_kto);
    			$kname       = $accountplan->AccountName;
    			$temp_kto    = $kres;
    		} while ($temp_kto != 0);
    		if (strripos($kname, "Hovedbok balanse") === 0) {
				foreach ($tmp as $avd => $val) {
					if (!isset($ny[$kto][$avd])) {
						$ny[$kto][$avd] = -$val;
					} else {
						$ny[$kto][$avd] = $ny[$kto][$avd] - $val;
					}
				}
    		}
    	}
		
		$avdelinger = Avdeling::find_all();
    	foreach ($ny as $kto => $tmp) {
			foreach ($tmp as $avd => $val) {
				$b->ny_postering( new Postering($kto, new Beloep($val), null, $avdelinger[$avd] ));
			}
    	}
		
    	return $b;
    }
    
    /*
     * Sletter evt. eksisterende posteringer og lagrer
     * deretter bilaget på nytt.
     * 
     * Bruker 'quantity' for $bilagsnummer (temp-id), bilagsnummeret fra skjema
     * ligger i journal_id som forventet.
	 *
	 * Legger inn en ekstra rad med ktoid 0, hvor bilagsnummer ligger
	 *
	 * @param $avdeling instans av avdeling
	 *
     */
    function lagreTmp($temp_id, $db = null) {
        global $_lib;
    	try {
	    		
	    	if (empty($db)) {
				$lodo = new lodo();
				$db = new Db($lodo);
	    	}

			if ( !$db->Connect() ) {
				die("Kunne ikke koble til databasen.");
			}
			
			if (empty($this->journal_id)) {
				$this->settBilagsNummer( Bilag::nyttBilagsNummer(), $db );
			}
	
			$db->Query("BEGIN");
	
			//fjerner gamle posteringer
			$query = "delete from vouchertmp where Quantity = '" . (int)$temp_id . "'";
			/* - Brukes kun om vi filtrerer på avdeling..
			if ($avdeling == null) {
				$avdeling = 0;
			}
			$query .= " and DepartmentID = '" . $avdeling . "'";
			*/
			
			$_lib['db']->db_delete($query);

			$posteringer = $this->posteringer;
			
			//legger til en tom postering for å garantere at bilagsnummeret
			//blir lagret
			$posteringer[] = new Postering(0, new Beloep(0), null);
			
			//lagre alle posteringer..
			foreach ($posteringer as $postering) {
	
				// hopp over tomme posteringer, unntak om kontoid = 0 - spesialpostering som kun er der for bilagsnummer
				$a = $postering->getBeloep()->getVal();
				if (empty($a) && $postering->getKonto() != 0) {
					continue;
				}
				
				//lagre enkeltbilag
				$in = $postering->getBeloep()->getDebet();
				$out = $postering->getBeloep()->getKredit();
				$kto = $postering->getKonto();
				$avd = $postering->getAvdeling();
				$avdeling = 0;
				if (!empty($avd)) {
					$avdeling = $avd->id;
				}
				
				$id = $postering->getId();
				if ($id == null || $id == "NULL" || true) {
					$query = "INSERT INTO vouchertmp(AccountPlanID, JournalID, VoucherType, VoucherDate, VoucherPeriod, AmountIn, AmountOut, Quantity, DepartmentID)" .
							" values('$kto', '$this->journal_id', '$this->type', '$this->dato', '$this->period', '$in', '$out', '$temp_id', $avdeling)";
				}
				//print "lagrer postering: " . $query . "<br />\r\n";
				$_lib['db']->db_insert($query);
			}
			
			$db->Query("COMMIT");
    	} catch (Exception $e) {
    		$error = "Bilag::lagreTmp(): $e";
    	}
    }


    /*
     * Sletter evt. eksisterende posteringer og lagrer
     * deretter bilaget på nytt.
     * 
     */
    function lagre() {
    	global $_lib, $_sess, $accounting;
    	try {
	    	if ($this->getNummer() == 0) {
	    		return;
	    	}
			
			if (count($this->posteringer) == 0) {
				return;
			}
	    	
	    	if ($this->getNummer() == -1) {
				list($JournalID, $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $this->type));
	    	} else {
	    	    $accounting->delete_journal($this->journal_id, $this->type);
	    	}
	    	
	    	// Dette gjøres automatisk av accounting biblioteket, så derfor er denne fjernet.
			//$posteringer = Bilag::calcHovedbokFromReskontro($this->posteringer);
			
			//lagre alle posteringer..
			foreach ($this->posteringer as $postering) {
	
				// hopp over tomme posteringer
				$a = $postering->getBeloep()->getVal();
				if (empty($a)) {
					continue;
				}
				
				//lagre enkeltbilag
				$data = array();
				$data['voucher_JournalID']          = $this->journal_id;
				$data['voucher_AccountPlanID']      = $postering->getKonto();
                $data['voucher_AmountIn']           = sprintf("%.2f", $postering->getBeloep()->getDebet());
                $data['voucher_AmountOut']          = sprintf("%.2f", $postering->getBeloep()->getKredit());
				$data['voucher_VoucherDate']        = $this->dato;
				$data['voucher_VoucherPeriod']      = $this->period;
				$data['voucher_VoucherType']        = $this->type;
				$data['voucher_AutomaticReason']    = 'Fra årsoppgjør x';
				$data['voucher_Description']        = '';
				$data['voucher_Active']             = 1;
				$data['voucher_DisableAutoVat']     = 1;
				$data['voucher_KID']                = '';
				$data['voucher_InvoiceID']          = '';
				
				$avd = $postering->getAvdeling();
				if (!empty($avd)) {
					$data['voucher_DepartmentID']   = $avd->id;
				} else {
					$data['voucher_DepartmentID']   = 0;
				}
				
				$data['voucher_DescriptionID']      = '';
				
        //print "<pre>";
        //print_r($data);
        //print "</pre>";
					
				$postering->id = $accounting->insert_voucher_line(array('post'=>$data, 'accountplanid'=>$postering->getKonto(), 'type'=>'first', 'VoucherType'=>$this->type, 'NoVatCalculation' => true));
			}
    	} catch (Exception $e) {
    		$error = "Bilag::lagre(): $e";
    	} 
    }
}

?>
