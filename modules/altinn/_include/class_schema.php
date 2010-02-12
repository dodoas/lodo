<?php

require_once "schema_general_class.php";

class Schema
{
	var $SCHEMA_NUMBER;
	var $SCHEMA_REVISION;

	var $instanceId;

	var $schemaClass;
	var $db;
	var $lodo;
	var $layout;
	var $config;

	function Schema( $db, $lodo, $config, $layout, $schemaNumber, $schemaRevision )
	{
		global $_SETUP;
		$this->db = $db;
		$this->lodo = $lodo;
		$this->config = $config;
		$this->layout = $layout;

		$this->SCHEMA_NUMBER = $schemaNumber;
		$this->SCHEMA_REVISION = $schemaRevision;

		$this->PACKAGESTATUS_DRAFT = 0;
		$this->PACKAGESTATUS_SENDING = 1;
		$this->PACKAGESTATUS_SENTOK = 2;
		$this->PACKAGESTATUS_SENTERROR = 3;

		$schemaFile = $_SETUP['HOME_DIR'] . '/code/lodo/altinn/_include/schema_' . $this->SCHEMA_NUMBER .  '_' . $this->SCHEMA_REVISION . '.php';

        //print ( "Anh schemaNr:" . $this->SCHEMA_NUMBER . "<br>");
		//if (file_exists($schemaFile))
			//require_once $schemaFile;
		//else
			//require_once $_SETUP['HOME_DIR'] . '/code/lodo/altinn/_include/schema_general_class.php';
		//if (file_exists($schemaFile))
			//$this->schemaClass = new SchemaClass( $this->db, $this->lodo, $this->config );
		//else
			$this->schemaClass = new SchemaClass( $this->db, $this->lodo, $this->config , $this->SCHEMA_NUMBER);

	}

	function LoadSchemaFromLodo( $year, $termin, $terminitem=0)
	{
		return $this->schemaClass->LoadSchemaFromLodo( $year, $termin, $terminitem);
	}

	function ReadSchemaForm(  )
	{
		$this->schemaClass->ReadSchemaForm();
	}

	function SaveSchema( $instanceId, $packetId )
	{
		$retData = '';
		$data = $this->schemaClass->GetData();
		
		
		//Start the mapping of the schema list
		foreach ( $data as $key => $value )
		{
			if (is_array($value))
			{
				foreach ($value as $v)
				{
					if ( $retData != '' ) {$retData .= '&';}
					$retData .= urlencode( $key ) . '=' . urlencode( $v );
				}
			}
			else
			{
				if ( $retData != '' ) {$retData .= '&';}
				$retData .= urlencode( $key ) . '=' . urlencode( $value );
			}
		}

/*
		foreach ( $data as $key => $value )
		{
			if ( $retData != '' ) {$retData .= '&';}
			$retData .= urlencode( $key ) . '=' . urlencode( $value );
		}
*/
		$sval['data'] = $retData;
		$sqlStr = 'UPDATE altinn_schema SET ' . $this->db->BuildSQLString( $this->db->BUILD_UPDATE, $sval ) . ' WHERE instance_id=' . $instanceId;
		if ( !$this->db->Query( $sqlStr ) ) {
			$this->layout->PrintError("Kunne ikke legge til AltInn-skjema i databasen!");
			$this->db->Disconnect();
			die();
		}

		$sqlStr = "UPDATE altinn_packet SET ts_modified='" . time() . "' WHERE packet_id=" . $packetId;
		if ( !$this->db->Query( $sqlStr ) ) {
			$this->layout->PrintWarning("Kunne ikke oppdatere modifisert dato i AltInn-pakken i databasen!");
			$this->db->Disconnect();
			die();
		}
	}

	function CreateNewSchema( $packetId )
	{
		$sval['packet_id'] = $packetId;
		$sval['schematype'] = $this->SCHEMA_NUMBER;
		$sval['schemarevision'] = $this->SCHEMA_REVISION;

		$retData = '';
		
		//Get the orid array data list
		$data = $this->schemaClass->GetData();

		foreach ( $data as $key => $value )
		{
			if (is_array($value))
			{
				foreach ($value as $v)
				{
					if ( $retData != '' ) {$retData .= '&';}
					$retData .= urlencode( $key ) . '=' . urlencode( $v );
				}
			}
			else
			{
				if ( $retData != '' ) {$retData .= '&';}
				$retData .= urlencode( $key ) . '=' . urlencode( $value );
			}
		}
/*
		foreach ( $data as $key => $value )
		{
			if ( $retData != '' ) {$retData .= '&';}
			$retData .= urlencode( $key ) . '=' . urlencode( $value );
		}
*/
		$sval['data'] = $retData;

		//Create a new schema with the new packet id
		$sqlStr = 'INSERT INTO altinn_schema ' . $this->db->BuildSQLString( $this->db->BUILD_INSERT, $sval );
		if ( !$this->db->Query( $sqlStr ) ) {
			$this->layout->PrintError("Kunne ikke legge til AltInn-skjema i databasen!");
			$this->db->Disconnect();
			die();
		}
	}//CreateNewSchema

	function LoadSchema( $instanceId, $insertedData=null)
	{
		$sqlStr = "SELECT * FROM altinn_schema WHERE instance_id=" . $instanceId;

		if ( $rs = $this->db->Query( $sqlStr ) )
		{
			if ( $row = $this->db->NextRow( $rs ) )
			{
				$this->SCHEMA_NUMBER = $row['schematype'];
				$this->SCHEMA_REVISION = $row['schemarevision'];
				$this->instanceId = $row['instance_id'];
				$data = array();
				$liste = split("&", $row['data']);
                foreach($liste as $linje)
                {
                        list($key, $value) = split("=", $linje);
                        $key = urldecode($key);
                        $value = urldecode($value);
                        if(isset($data[$key]) || is_array($data[$key]))
                        //if($data[$key] != "" || is_array($data[$key]))
                        {
                                if(isset($data[$key]) &&  !is_array($data[$key]))
                                //if($data[$key] != "" &&  !is_array($data[$key]))
                                {
                                        $temp = $data[$key];
                                        unset($data[$key]);
                                        $data[$key] = array();
                                        $data[$key][] = $temp;
                                }
                                $data[$key][] = $value;
                        }
                        else
                                $data[$key] = $value;
                }

 //print_r($data);
/*
				$tmp = $row['data'];
				$dataArray = split('&', $tmp);
				for ( $counter = 0; $counter < count($dataArray); $counter++ )
				{
					$tmpArray = split('=', $dataArray[ $counter ]);
					$data[ urldecode($tmpArray[0]) ] = urldecode( $tmpArray[1] );
				}
				
					
*/
				if ($insertedData!=null){
					//print("Anh merge:".$insertedData['oppgavetype']);
					$data= array_merge($data, $insertedData);
				}

				$this->schemaClass->data = $data;
			}

			$this->db->EndQuery( $rs );
		}
	}

	function ToXML( $year, $termin, $extraParams = null)
	{
		if (is_null($extraParams))
			return( $this->schemaClass->ToXML( $year, $termin ) );
		else
			return( $this->schemaClass->ToXML( $year, $termin, $extraParams ) );
	}

	function GetSchemaName()
	{
		return( $this->schemaClass->SCHEMA_NAME );
	}
	function GetSchemaNumber()
	{
		return( $this->SCHEMA_NUMBER );
	}
	function GetSchemaRevision()
	{
		return( $this->SCHEMA_REVISION );
	}

	function SetData( $orId, $value )
	{
		$this->schemaClass->SetData( $orId, $value );
	}
	
	function GetData()
	{
		return $this->schemaClass->GetData();
	}

	function DisplaySchema( $status )
	{   
		$this->schemaClass->DisplaySchemaCurrent($status);
	}
}
?>
