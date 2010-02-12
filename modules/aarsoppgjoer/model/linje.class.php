<?php

/*
 * sammenligner to linjer på kontonummer
 */
function compareLinjer($l1, $l2) {
	if ($l1 == null && $l2 == null) {
		return 0;
	} else if ($l1 == null) {
		return -1;
	} else if ($l2 == null) {
		return 1;
	}
	$k1 = (int)$l1->getKonto()->nu();
	$k2 = (int)$l2->getKonto()->nu();
	return $k1 - $k2;
}

class Linje {
	public $konto;		//Konto; kontonummer + navn
	public $id1;		//voucher-id, bilag1
	public $id2;		//voucher-id, bilag2
	public $bel1;		//amount, bilag1
	public $bel2;		//amount, bilag2
	public $avdeling;	//avdeling
	
	/*
	 * Sorterer og organiserer kontolisten og posteringene fra de to bilagene
	 * for bruk i input_manual -viewet.
	 * Antar at voucher-id'en ikke er i bruk; akkurat nå sletter jeg i hvert fall
	 * eksisterende bilag og lagrer på nytt, fremfor å oppdatere en postering.
	 * 
	 * @return Linje[]
	 */
	 // TODO: full rewrite
	 static function prepare($bilag1, $bilag2) {
		$kontoer = Konto::getListe();
		$avdelinger = Avdeling::find_all();
		$p1 = $bilag1->getPosteringer();
		$p2 = $bilag2->getPosteringer();
		
		if (empty($p1)) {
			$p1 = array();
		}
		if (empty($p2)) {
			$p2 = array();
		}
		
		// $linjer_by_konto[7000] = {new Linje(7000, x, x, x, x), new Linje(7000, y, y, y, y), ... }
		$linjer_by_konto = array();
		
		//ekspander kontoplan. egen linje pr. avdeling for kontoene som har avdeling..
		foreach ($kontoer as $konto) {
			if ($konto->har_avdeling) {
				foreach ($avdelinger as $avdeling) {
					$linjer_by_konto[$konto->nummer][] = new Linje($konto, null, null, $avdeling, null, null);
				}
			} else {
				$linjer_by_konto[$konto->nummer] = array( new Linje($konto, null, null, null, null, null) );
			}
		}
		
		// trykk posteringer inn i linjene..
		foreach ($p1 as $p) {
			$kto = $p->getKonto();
			$id = $p->getId();
			$bel = $p->getBeloep();
			$avd = $p->getAvdeling();
			if ( !$kontoer[$kto]->har_avdeling ) {
				if (!empty($avd) && empty($avd->id) ) {
					$avd = null;
				}
			}
			$match = false;
			foreach ($linjer_by_konto[$kto] as $linje) {
				if ( (empty($kto) && empty($linje->konto)) || ($linje->getAvdeling() == $avd) ) {
					if ($linje->bel1 == null) {
						$linje->bel1 = $bel;
						$linje->id1 = $id;
						$match = true;
						break;
					}
				}
			}
			if (!$match) {
				$linjer_by_konto[$kto][] = new Linje($kontoer[$kto], $bel, null, $avd, $id, null);
			}
			
		}
		foreach ($p2 as $p) {
			$kto = $p->getKonto();
			$id = $p->getId();
			$bel = $p->getBeloep();
			$avd = $p->getAvdeling();
			if ( !$kontoer[$kto]->har_avdeling ) {
				if (!empty($avd) && empty($avd->id) ) {
					$avd = null;
				}
			}
			$match = false;
			foreach ($linjer_by_konto[$kto] as $linje) {
				if ( (empty($kto) && empty($linje->konto)) || ($linje->getAvdeling() == $avd) ) {
					if ($linje->bel2 == null) {
						$linje->bel2 = $bel;
						$linje->id2 = $id;
						$match = true;
						break;
					}
				}
			}
			if (!$match) {
				$linjer_by_konto[$kto][] = new Linje($kontoer[$kto], null, $bel, $avd, null, $id);
			}
		}

		//trekk ut dataene til en 1d array, og bytt ut null-verdier for beløp
		$linjer = array();
		foreach ($linjer_by_konto as $linje) {
			foreach ($linje as $l) {
				if ($l->bel1 == null) {
					$l->bel1 = new Beloep(0);
				}
				if ($l->bel2 == null) {
					$l->bel2 = new Beloep(0);
				}
				$linjer[] = $l;
			}
		}
		
		return $linjer;
	}
	
    function Linje($konto, $beloep1, $beloep2, $avdeling = null, $id1 = null, $id2 = null) { 
    	$this->konto = $konto;
    	$this->bel1 = $beloep1;
    	$this->bel2 = $beloep2;
		$this->avdeling = $avdeling;
		$this->id1 = $id1;
		$this->id2 = $id2;
    }
	
	function getAvdeling() {
		return $this->avdeling;
	}
    
    function getKonto() {
    	return $this->konto;
    }
	
	function setKonto($kto) {
		$this->konto = $kto;
	}
    
    function getBel1() {
    	return $this->bel1;
    }
    
    function getBel2() {
    	return $this->bel2;
    }
	
	function getId1() {
		return $this->id1;
	}
    
	function getId2() {
		return $this->id2;
	}

}
?>
