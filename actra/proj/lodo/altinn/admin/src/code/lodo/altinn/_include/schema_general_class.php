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
	
	function SchemaClass( $db, $lodo, $config, $mySCHEMA_NUMBER = 0)
	{
		$this->db = $db;
		$this->lodo = $lodo;
		$this->config = $config;

		$this->reported = null;

		$sqlStr = 'SELECT * FROM `altinnschemalist` WHERE fagsystemid=' . $mySCHEMA_NUMBER . ';';
		$rs = $this->db->Query( $sqlStr );
		$row = $this->db->NextRow( $rs );

		$this->SCHEMA_NUMBER = $mySCHEMA_NUMBER;
		$this->SCHEMA_REVISION = $row["revision"];
		$this->SCHEMA_NAME = $row["name"];
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
		$GeneralReport = new NaeringsOppgave2(array('fromPeriod'=>$year . '-01', 'toPeriod'=>$year . '-12', 'enableLastYear'=>'1', 'report'=>'5'));
		$this->data = $GeneralReport->getOridArray();
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
