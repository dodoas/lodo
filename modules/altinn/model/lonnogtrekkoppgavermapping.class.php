<?php
/* copy right
 * Created on 09.jun.2005
 * by Anh Le
 * 
 * 
 */
class LonnOgTrekkOppgaverMapping
{
    var $ORID = array();
    var $Human = array();
    var $Account = array();
    
     function addMapping($o, $h, $a)
    {
        $this->Human[$o] = $h;
        $this->Account[$a][] = $o;
    }

    function setOrid($o, $v)
    {
        $this->ORID[$o] = $v;
    }

    function setAccount($a, $v)
    {
    	
       	if (is_array($this->Account[$a]))
       	{
	        if($this->Account[$a][0] == '')
    	    {
        	    $this->ORID['FEIL: '.$a] = $v;
            	return 0;
        	}
        	else
        	{
       			foreach($this->Account[$a] as $item)
           			$this->ORID[$item] = $v;
            	return 1;
        	}
       	}
       	else
       	{
	        if($this->Account[$a] == '')
    	    {
        	    $this->ORID['FEIL: '.$a] = $v;
            	return 0;
        	}
        	else
	        {
            	$item = $this->Account[$a];
            	$this->ORID[$item] = $v;
	            return 1;
    	    }
       	}
    }

    function setHuman($h, $v)
    {
        $this->ORID[$this->Human[$h]] = $v;
    }

    function getOridArray()
    {
        return $this->ORID;
    }

    function getHuman($a)
    {
        $tmpstr = $this->Human[$this->Account['D'.$a]];
        $tmp = explode('-', $tmpstr);
        return $tmp['0'];
    }
    
    function getHumanArray()
    {
        return $this->Human;
    }
    function getAccount()
    {
        return $this->Account;
    }

    /*function addValues($o, $h, $a, $v)
    {
        $ORID[$o] = $v;
        $Human[$o] = $h;
        $Account[$a] = $o;
    }*/

	function mapping()
	{	
        
	}
 }
?>
