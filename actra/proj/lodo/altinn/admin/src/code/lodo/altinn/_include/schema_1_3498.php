<?php
$lodoActive = true;
if ( $lodoActive )
{
//	include_once $_SETUP['HOME_DIR']."/code/lodo/mvaavstemming/mvaavstemming.class";
//	require_once 'class_mva.php';
}

class SchemaClass
{
	var $lodo;
	var $mva;
	var $db;
	var $config;

	var $reported;

	var $data_lodo;
	var $data;

	var $SCHEMA_NUMBER;
	var $SCHEMA_REVISION;
	var $SCHEMA_NAME;
	
	function SchemaClass( $db, $lodo, $config )
	{
		$this->db = $db;
		$this->lodo = $lodo;
		$this->config = $config;

		$this->reported = null;

		$this->SCHEMA_NUMBER = 1;
		$this->SCHEMA_REVISION = 3498;
		$this->SCHEMA_NAME = "RF-1167 N&aelig;ringsoppgave 2 for 2004";
	}

	function LoadSchemaFromLodo( $year, $termin )
	{
/*
require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave1.class";
$NaeringsOppgave1 = new NaeringsOppgave1(array('fromPeriod'=>'2004-01', 
'toPeriod'=>'2004-12'));
$Orid = $NaeringsOppgave1->getOridArray();

require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave2.class";
$NaeringsOppgave2 = new NaeringsOppgave2(array('fromPeriod'=>'2004-01', 
'toPeriod'=>'2004-12'));
$Orid = $NaeringsOppgave2->getOridArray();

 * */
		global $_SETUP;
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/naeringsoppgave2.class"; 
		require_once  $_SETUP['HOME_DIR'] . '/code/lodo/altinn/_include/class_companyinfo_mapping.php';
		$GeneralReport = new NaeringsOppgave2(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'5'));
		$this->data = $GeneralReport->getOridArray();
		$GeneralReport2 = new companyMapping( $this->db, $this->SCHEMA_NUMBER, $this->SCHEMA_REVISION);
		$this->data2 = $GeneralReport2->getMapping();
		foreach($this->data2 as $key => $value)
		{
			$this->data[$key] = $value;
		}
		print "<pre>\n";
		print_r ($this->data);
		print "</pre>\n";
		return($this->data);
	}

	function GetData()
	{
		//print_r ($this->data);
		return( $this->data );
	}

	function CreateNewPackage( $year, $termin )
	{

	}

	function ToXML( $year, $termin )
	{	
		global $_SETUP;
		$data = $this->data;
		$filepathprefix = $_SETUP['HOME_DIR'] . "code/lodo/altinn/_include/";
		$head = true;
		include $filepathprefix . 'schemaxml_' . $this->SCHEMA_NUMBER . '_' . $this->SCHEMA_REVISION . '.php';
		$headXML = $xml;

		$head = false;
		include $filepathprefix . 'schemaxml_' . $this->SCHEMA_NUMBER . '_' . $this->SCHEMA_REVISION . '.php';
		$bodyXML = $xml;

		return( array('head'=>$headXML, 'body'=>$bodyXML) );
	}

	function GetReportData( $report, $data )
	{
	}

	function DisplaySchemaCurrent( $status )
	{
		$this->DisplaySchema( $this->data, $status );
	}
	function DisplaySchema( $data, $status )
	{
		include 'schemamva_' . $this->SCHEMA_REVISION .  '.php';
	}

	function GetTerminReport( $terminType, $terminItem )
	{

	}

	function GetMonthReport( $month )
	{

	}

	function ReadSchemaForm(  )
	{
		global $_REQUEST;
		$data = array();
		foreach ($_REQUEST as $key => $val)
		{
			$data[$key] = intval($val);
		}
		$this->data = $data;
	}

	function TranslateLodoData( $lodoData )
	{
		return $lodoData; 
	}

	function AddReports( $report1, $report2 )
	{

	}

	function RetrieveYear( $year )
	{

	}
}
?>
