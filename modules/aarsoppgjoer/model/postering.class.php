<?php

class Postering {
	public $konto;
	public $beloep;
	public $id;
	public $avdeling;

	/*
	 * @param $konto kontonummer, int
	 * @param $beloep beløp inn eller ut, Beloep.class
	 * @param $id primary key for voucheren, null om den ikke er lastet fra db
	 * @param $avdeling instans av avdeling
	 */
    function __construct($konto, $beloep, $id = null, $avdeling = null) {
    	$this->konto = (int)$konto;
    	$this->beloep = $beloep;
    	$this->id = (int)$id;
		$this->avdeling = $avdeling;
    }
    
    function getKonto() {
    	return $this->konto;
    }
    
    function getBeloep() {
    	return $this->beloep;
    }
    
    function getId() {
    	return $this->id;
    }
	
	function getAvdeling() {
		return $this->avdeling;
	}
}
?>
