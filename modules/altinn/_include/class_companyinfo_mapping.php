<?php

// Må rettes
includelogic("company/companyinfo");

class companyMapping
{
	var $maptable = array();
	var $db;
	
	function companyMapping( $db, $fagsystemid, $revision)
	{
		global $_sess;
		$this->db = $db;
		$this->SCHEMA_NUMBER = $fagsystemid;
		$this->SCHEMA_REVISION = $revision;
		$this->cinfo = new ComanyInfo();
		$personnummer = $_lib['sess']->Get_Person("SocialSecurityID");
		$sqlStr = 'SELECT * FROM `altinncompanymap` WHERE fagsystemid = ' . $fagsystemid . ';';
		$rs = $this->db->Query( $sqlStr );
		while ( $row = $db->NextRow( $rs ) )
		{
			$myOrid = "D" .$row["orid"];
			
			if ( $row["hardkodet"] == 0 && $row["infotype"] != "Login_Person")
			{
				if ($row["infotype"] != "")
				{
					$objektRef1 = $row["infotype"];
					$mapname = $row["mapname"];
					$Value = $this->cinfo->{$objektRef1}->{$mapname};
//					print "Test: " . $ComanyInfo->CustomerCompany["VName"] . "<br>\n";
					//$Value = $this->cinfo->{$objektRef1}[$mapname];
//					print "Mapname: " . $mapname . "<br>Infotype: " . $objektRef1 . "<br>"; 
				}
				else 
				{
					$mapname = $row["mapname"];
					$Value = $this->cinfo->{$mapname};
				}
			}
			if ( $row["hardkodet"] == 0 && $row["infotype"] == "Login_Person")
			{
				$Value = $personnummer;
			}
			if ( $row["hardkodet"] == 1)
			{
				$Value = $row["harkodetVerdi"];
			}
			//print "<br>ORID" . $myOrid . " = " . $Value . "<br>\n";;
			$this->maptable[$myOrid] = $Value;
		}
	}
	function getMapping()
	{
		return $this->maptable;
	}
}
?>
