<?php

class Beloep {
	var $val;

    function Beloep($val = 0) {
    	$this->val = $val;
    }
    
    /*
     * @param $deb
     * @param $kred
     * 
     * Parameters are unparsed text input.
     */
    function setVal($deb, $kred) {
		//print "***************deb: $deb -- kred: $kred<br />\r\n";
    	$tmp = array();
		$deb = (preg_match('/[\d|,. ]+/', $deb, $tmp) == 0) ? "" : $tmp[0];
		$deb = preg_replace('/(.*)\.00\w*/', '\1', $deb);

    	$tmp = array();
		$kred = (preg_match('/[\d|,. ]+/', $kred, $tmp) == 0) ? "" : $tmp[0];
		$kred = preg_replace('/(.*)\.00\w*/', '\1', $kred);

		$this->val = (empty($kred)) ? $deb : "-".$kred;

		//$this->val = $_lib['format']->Amount(array('value'=>$this->val, 'return'=>'value'));
		$this->val = str_replace(" ", "", $this->val);
		$this->val = str_replace("|", ".", $this->val);
		$this->val = str_replace(",", ".", $this->val);
		$this->val = preg_replace('/^(.*?\.\d\d)\d*/', '\1', $this->val);
    }
	
    /*
     * @return the value if positive or zero, otherwise an empty string
     */
    function getDebet() {
    	return ($this->val >= 0) ? $this->val : "";
    }
    
    /*
     * @return abs(value) if negative, otherwise an empty string
     */
    function getKredit() {
    	return ($this->val < 0) ? abs($this->val) : "";
    }
    
    function getVal() {
    	return $this->val;
    }
    
}
?>