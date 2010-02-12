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
		$this->db = db;
		$this->lodo = $lodo;
		$this->config = $config;

		$this->reported = null;
		$this->mva = new Mva( $db );

		$this->SCHEMA_NUMBER = 212;
		$this->SCHEMA_REVISION = 3148;
		$this->SCHEMA_NAME = "RF-0002 Alminnelig omsetningsoppgave";
	}

	function LoadSchemaFromLodo( $year, $termin )
	{
		$this->RetrieveYear( $year );
		$this->data_lodo = $this->GetTerminReport( $this->config->GetConfig( $this->config->TYPE_TERMIN ), $termin );
		$this->data = $this->TranslateLodoData( $this->data_lodo );
	}

	function GetData()
	{
		return( $this->data );
	}

	function SetData( $orId, $value )
	{
		$this->data[ 'd' . $orId ] = $value;
	}

	function CreateNewPackage( $year, $termin )
	{

	}

	function ToXML( $year, $termin )
	{
		$data = $this->data;

		$head = true;
		include 'schemaxml_' . $this->SCHEMA_NUMBER . '_' . $this->SCHEMA_REVISION . '.php';
		$headXML = $xml;

		$head = false;
		include 'schemaxml_' . $this->SCHEMA_NUMBER . '_' . $this->SCHEMA_REVISION . '.php';
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
		/* Get whole year */
		if ( $terminType == $this->mva->TERMINTYPE_YEARLY )
		{
			$totalReport = $this->GetMonth( 1 );
			for ( $month = 2; $month <= 12; $month++ )
			{
				$tmp = $this->GetMonthReport( $month );
				$totalReport = $this->AddReports( $totalReport, $tmp );
			}

			return( $totalReport );
		}
		elseif ( $terminType == $this->mva->TERMINTYPE_QUARTERLY )
		{
			$startMonth = (($terminItem - 1) * 3);
			$monthReport1 = $this->GetMonthReport( $startMonth );
			$monthReport2 = $this->GetMonthReport( $startMonth + 1 );
			$monthReport3 = $this->GetMonthReport( $startMonth + 2 );

			$totalReport = $this->AddReports( $monthReport1, $monthReport2 );
			$totalReport = $this->AddReports( $totalReport, $monthReport3 );

			return( $totalReport );
		}
		elseif ( $terminType == $this->mva->TERMINTYPE_SECONDMONTH )
		{
			$startMonth = (($terminItem - 1) * 2);
			$monthReport1 = $this->GetMonthReport( $startMonth );
			$monthReport2 = $this->GetMonthReport( $startMonth + 1 );

			$totalReport = $this->AddReports( $monthReport1, $monthReport2 );

			return( $totalReport );
		}
		elseif ( $terminType == $this->mva->TERMINTYPE_MONTHLY )
		{
			$startMonth = $terminItem;
			$totalReport = $this->GetMonthReport( $startMonth );

			return( $totalReport );
		}
		else {
			return( null );
		}
	}

	function GetMonthReport( $month )
	{
		if ( $this->reported == null ) { return(null); }

		return( $this->reported[ $month ] );
	}

	function ReadSchemaForm(  )
	{
		global $_REQUEST;

		$data = array();

		$data['d5659'] = intval($_REQUEST['d5659']);
		$data['d8446'] = intval($_REQUEST['d8446']);
		$data['d10095'] = intval($_REQUEST['d10095']);
		$data['d10096'] = intval($_REQUEST['d10096']);
		$data['d10097'] = intval($_REQUEST['d10097']);
		$data['d10098'] = intval($_REQUEST['d10098']);
		$data['d20319'] = intval($_REQUEST['d20319']);
		$data['d20320'] = intval($_REQUEST['d20320']);
		$data['d14360'] = intval($_REQUEST['d14360']);
		$data['d14361'] = intval($_REQUEST['d14361']);
		$data['d14362'] = intval($_REQUEST['d14362']);
		$data['d14363'] = intval($_REQUEST['d14363']);
		$data['d8450'] = intval($_REQUEST['d8450']);
		$data['d20322'] = intval($_REQUEST['d20322']);
		$data['d14364'] = intval($_REQUEST['d14364']);
		$data['d8452'] = intval($_REQUEST['d8452']);
		$data['d8453'] = intval($_REQUEST['d8453']);

		$data['d10098'] = intval($data['d10097'] * 0.25);
		$data['d20320'] = intval($data['d20319'] * 0.11);
		$data['d14361'] = intval($data['d14360'] * 0.07);
		$data['d14363'] = intval($data['d14362'] * 0.25);

		$base = $data['d10096'];
		$base += $data['d10097'];
		$base += $data['d20319'];
		$base += $data['d14360'];
		$data['d8446'] = $data['d10095'] = $base;

		$tmp = $data['d10098'];
		$tmp += $data['d20320'];
		$tmp += $data['d14361'];
		$tmp += $data['d14363'];
		$tmp -= $data['d8450'];
		$tmp -= $data['d20322'];
		$tmp -= $data['d14364'];

		$mtmp = $tmp;
		if ($mtmp >= 0) {
			$data['d8452'] = 0;
			$data['d8453'] = $mtmp;
		}
		else {
			$data['d8452'] = $mtmp * -1;
			$data['d8453'] = 0;
		}

		$this->data = $data;
	}

	function TranslateLodoData( $lodoData )
	{
		$data['d5659'] = 1; // type oppgave
		$data['d8446'] = $lodoData['TotalOmsettning'];
		$data['d10095'] = $lodoData['TotalOmsettning'];
		$data['d10096'] = $lodoData['FreeOmsettning'];
		$data['d10097'] = $lodoData['Grunnlag25Mva'];
		$data['d10098'] = $lodoData['Out25Mva'];
		$data['d20319'] = $lodoData['Grunnlag11Mva'];
		$data['d20320'] = $lodoData['Out11Mva'];
		$data['d14360'] = $lodoData['Grunnlag7Mva'];
		$data['d14361'] = $lodoData['Out7Mva'];
		$data['d14362'] = 0;
		$data['d14363'] = 0;
		$data['d8450'] = $lodoData['In25Mva'];
		$data['d20322'] = $lodoData['In11Mva'];
		$data['d14364'] = $lodoData['In7Mva'];

		$tmp = intval($lodoData['SumMva']);
		if ( $tmp >= 0 ) {
			$data['d8452'] = 0;
			$data['d8453'] = $tmp;
		}
		else {
			$data['d8452'] = $tmp;
			$data['d8453'] = 0;
		}

		return( $data );
	}

	function AddReports( $report1, $report2 )
	{
		$retVal = array(
			'TotalOmsettning' => intval($report1['TotalOmsettning'] + $report2['TotalOmsettning']),
			'FreeOmsettning' => intval($report1['FreeOmsettning'] + $report2['FreeOmsettning']),
			'Grunnlag25Mva' => intval($report1['Grunnlag25Mva'] + $report2['Grunnlag25Mva']),
			'Out25Mva' => intval($report1['Out25Mva'] + $report2['Out25Mva']),
			'Grunnlag11Mva' => intval($report1['Grunnlag11Mva'] + $report2['Grunnlag11Mva']),
			'Out11Mva' => intval($report1['Out11Mva'] + $report2['Out11Mva']),
			'Grunnlag7Mva' => intval($report1['Grunnlag7Mva'] + $report2['Grunnlag7Mva']),
			'Out7Mva' => intval($report1['Out7Mva'] + $report2['Out7Mva']),
			'In25Mva' => intval($report1['In25Mva'] + $report2['In25Mva']),
			'In11Mva' => intval($report1['In11Mva'] + $report2['In11Mva']),
			'In7Mva' => intval($report1['In7Mva'] + $report2['In7Mva']),
			'SumMva' => intval($report1['SumMva'] + $report2['SumMva'])
		);

		return( $retVal );
	}

	function RetrieveYear( $year )
	{
		global $_sess, $_dbh, $_dsn, $_date;

		if ( $this->reported != null ) {
			unset($this->reported);
			$this->reported = null;
		}

		if ( $this->lodo->inLodo )
		{
			$avst = new mva_avstemming(array('_sess' => $_sess, '_dbh' => $_dbh, '_dsn' => $_dsn, '_date' => $_date, 'year' => $year));
			$this->reported = $avst->reported;
		}
		/* We are running stand alone */
		else
		{
			$this->reported = array(
			'1' => array(
				'TotalOmsettning' => 34500,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 30000,
				'Out25Mva' => 7500,
				'Grunnlag11Mva' => 2500,
				'Out11Mva' => 275,
				'Grunnlag7Mva' => 2000,
				'Out7Mva' => 140,
				'In25Mva' => 5000,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 2950
				),
			'2' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'3' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'4' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'5' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'6' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'7' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'8' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'9' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'10' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'11' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				),
			'12' => array(
				'TotalOmsettning' => 0,
				'FreeOmsettning' => 0,
				'Grunnlag25Mva' => 0,
				'Out25Mva' => 0,
				'Grunnlag11Mva' => 0,
				'Out11Mva' => 0,
				'Grunnlag7Mva' => 0,
				'Out7Mva' => 0,
				'In25Mva' => 0,
				'In11Mva' => 0,
				'In7Mva' => 0,
				'SumMva' => 0
				)
			);
		}
	}
}
?>
