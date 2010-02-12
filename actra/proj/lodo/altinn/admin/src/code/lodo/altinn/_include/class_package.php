<?php
class Package
{
	var $db;
	var $lodo;
	var $config;
	var $layout;

	var $package;
	var $schemas;

	var $schemaDef;
	var $schemaNames;
	var $statusText;
	var $packageNames;

	var $PACKAGETYPE_MVA;

	var $PACKAGESTATUS_DRAFT;
	var $PACKAGESTATUS_SENDING;
	var $PACKAGESTATUS_SENTOK;
	var $PACKAGESTATUS_SENTERROR;

	function Package( $params )
	{
		$this->db = $params['db'];
		$this->lodo = $params['lodo'];
		$this->config = $params['config'];
		$this->layout = $params['layout'];

		$this->package = null;
		$this->schemas = array();

		$this->PACKAGESTATUS_DRAFT = 0;
		$this->PACKAGESTATUS_SENDING = 1;
		$this->PACKAGESTATUS_SENTOK = 2;
		$this->PACKAGESTATUS_SENTERROR = 3;

		$this->statusText = array('Kladd','Under sending','Sent ok','Feil');

		$this->PACKAGETYPE_MVA = 1;

		$this->packageNames = array('','MVA');

		$this->schemaDef = array( '1' => array(array('number' => 212, 'revision' => 3148)) );
		$this->schemaNames = array( '212' => 'RF0002 Alminnelig omsetningsoppgave' );
	}

	function GetSchemaName( $schemaType )
	{
		return( $this->schemaNames[ $schemaType ] );
	}

	function GetSchemaRevision( $schemaType, $termin, $termintype, $year )
	{
		$retVal = 0;
		if ( $schemaType == 212 )
		{
			$retVal = 3148;
		}

		return( $retVal );
	}

	function LoadPackage( $packetId )
	{
		$retVal = false;
		unset($this->package);
		$this->package = null;

		$sqlStr = 'SELECT * FROM altinn_packet WHERE customer_id=' . $this->lodo->lodoCurrentClientId . ' AND packet_id=' . $packetId;
		if ( $rs = $this->db->Query( $sqlStr ) )
		{
			if ( $row = $this->db->NextRow( $rs ) )
			{
				$this->package['packet_id'] = $row['packet_id'];
				$this->package['customer_id'] = $row['customer_id'];
				$this->package['status'] = $row['status'];
				$this->package['ts_created'] = $row['ts_created'];
				$this->package['ts_modified'] = $row['ts_modified'];
				$this->package['modified_by'] = $row['modified_by'];
				$this->package['packettype'] = $row['packettype'];
				$this->package['termin'] = $row['termin'];
				$this->package['termintype'] = $row['termintype'];
				$this->package['year'] = $row['year'];

				$retVal = true;
			}
			else {
				$this->layout->PrintWarning('Kunne ikke hente pakken fra databasen.');
			}
			$this->db->EndQuery( $rs );
		}
		else {
			$this->layout->PrintWarning('Kunne ikke hente pakken fra databasen.');
		}

		return( $retVal );
	}

	function SavePackage()
	{
		global $_sess;

		$retVal = false;

		/* Check the status of the package to see if the status allows us to delete it */
		if ( $this->package['status'] > $this->$PACKAGESTATUS_DRAFT ) {
			$this->layout->PrintWarning('Pakken kan ikke endres fordi den allerede er sent til AltInn.');
		}
		else
		{
			$this->package->ts_modified = time();
			$this->package->modified_by = $_sess->get_person('FirstName') . ' ' . $_sess->get_person('LastName');

			$sqlStr = 'UPDATE altinn_packet SET ' . $this->db->BuildSQLString( $this->db->BUILD_UPDATE, $this->package ) . ' WHERE packet_id=' . $this->packet['packet_id'];
			if ( !$this->db->Query( $sqlStr ) ) {
				$this->layout->PrintWarning("Kunne ikke oppdatere AltInn-pakken i databasen!");
			}
			else {
				$retVal = true;
			}
		}

		return( $retVal );
	}

	function DeletePackage()
	{
		$retVal = false;

		/* Check the status of the package to see if the status allows us to delete it */
		if ( $this->package['status'] > $this->$PACKAGESTATUS_DRAFT ) {
			$this->layout->PrintWarning('Pakken kan ikke slettes fordi den allerede er sent til AltInn.');
		}
		else
		{
			/* Delete all related schemas */
			$sqlStr = 'DELETE FROM altinn_schema WHERE packet_id=' . $this->packet['packet_id'];
			if ( !$this->db->Query( $sqlStr )) {
				$this->layout->PrintWarning('Databasefeil ved sletting av relaterte skjemaer.');
			}
			else
			{
				/* Delete this package */
				$sqlStr = 'DELETE FROM altinn_packet WHERE packet_id=' . $this->packet['packet_id'];
				if ( !$this->db->Query( $sqlStr )) {
					$this->layout->PrintWarning('Databasefeil ved sletting av pakken.');
				}
				else {
					$retVal = true;
				}

				/* Reset the package */
				unset( $this->package );
				$this->package = null;
			}

		}

		return( $retVal );
	}

	function CreateNewPackage( $type, $year, $termin )
	{
		global $_sess;

		$this->package['customer_id'] = $this->lodo->lodoCurrentClientId;
		$this->package['status'] = $this->PACKAGESTATUS_DRAFT;
		$this->package['ts_created'] = time();
		$this->package['ts_modified'] = time();
		$this->package['modified_by'] = $_sess->get_person('FirstName') . ' ' . $_sess->get_person('LastName');
		$this->package['packettype'] = $type;
		$this->package['termin'] = $termin;
		$this->package['termintype'] = $this->config->GetConfig( $this->config->TYPE_TERMIN );
		$this->package['year'] = $year;

		$sqlStr = 'INSERT INTO altinn_packet ' . $this->db->BuildSQLString( $this->db->BUILD_INSERT, $this->package );
		if ( !$this->db->Query( $sqlStr ) ) {
			$this->layout->PrintError("Kunne ikke legge til AltInn-pakken i databasen!");
			$this->db->Disconnect();
			die();
		}

		/* Find id of the new packet */
		$packetId = 0;
		$sqlStr = "SELECT MAX(packet_id) FROM altinn_packet";
		if ( $rs = $this->db->Query( $sqlStr ) )
		{
			if ( $row = $this->db->NextRow( $rs ) )
			{
				$packetId = $row[0];
				$this->package['packet_id'] = $row[0];
			}
			$this->db->EndQuery( $rs );
		}

		if ( !$packetId )
		{
			$this->layout->PrintError("Kunne ikke finne AltInn-pakken i databasen!");
			$this->db->Disconnect();
			die();
		}

		for ( $i = 0; $i < count($this->schemaDef['1']); $i++ )
		{
			$schema = new Schema( $this->db, $this->lodo, $this->config, $this->layout, $this->schemaDef[$type][$i]['number'], $this->schemaDef[$type][$i]['revision'] );
			$schema->LoadSchemaFromLodo( $year, $termin );
			$schema->CreateNewSchema( $packetId );
			array_push( $this->schemas, $schema );
		}

		return( $packetId );
	}
}
?>
