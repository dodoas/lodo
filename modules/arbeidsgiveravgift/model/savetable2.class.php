<?php
/************************************************************************************
***
***   API: Savetable
***
***   Functions:
***   SaveTable($tableName, $ID = ""); Create the object and fetch the data.
***   set($attrib, $value); Set attributes in the entry in the database.
***   getFields(); Returns an array of the fields in the table.
***   getNumFields(); Returns number of fields.
***   save(); Save the data back in the database.
***
***   Copyright (c) 2003 GE Consulting. All rights reserved.
***   Web: www.geconsulting.no
***   E-mail: geir@geconsulting.no
***   Author: Geir Eliassen
************************************************************************************/

	class SaveTable
	{
		var $entry_exists = false;
		function SaveTable($tableName, $ID = "", $allowID = false)
		{
			global $_SETUP;
			$this->conn = mysqli_connect( $_SETUP['DB_SERVER'][0], $_SETUP['DB_USER'][0], $_SETUP['DB_PASSWORD'][0], $_SETUP['DB_NAME'][0] );
			if ( !$this->conn )
				die ("Kan ikke koble til databasen.");
				
			$this->numFields = 0;
			$this->tableName = $tableName;
			$this->entry_exists = false;
			$query = "SHOW FIELDS FROM " . $this->tableName . ";";
			$buffer = mysqli_query($this->conn, $query);
			while ($data = mysqli_fetch_array($buffer))
			{
				if ($data["Key"] == "PRI" )
				{
					$this->indexName = $data["Field"];
					$this->indexName2 = $data["Field"] . "_";
					$this->{$this->indexName2} = $ID;
				}
				$this->myFields[$this->numFields]["type"] = $data["Type"];
				$this->myFields[$this->numFields]["name"] = $data["Field"];
				$this->numFields++;
			}
			mysqli_free_result($buffer);
			$query = "SELECT * FROM " . $this->tableName . " WHERE " . $this->indexName . " = '" . $ID . "';";
			$buffer = mysqli_query($this->conn, $query);
			$data = mysqli_fetch_array($buffer);

			for ($i = 0; $i < $this->numFields; $i++)
			{
				$myFieldName = $this->myFields[$i]["name"];
				$this->{$this->myFields[$i]["name"]} = $data[$myFieldName];
			}
			mysqli_free_result($buffer);

			if ($this->{$this->indexName} != "")
			{
				$this->entry_exists = true;
			}
		}
		function set($attrib, $value)
		{
			$this->{$attrib} = $value;
		}
		function get($attrib)
		{
			return stripslashes($this->{$attrib});
		}
		function getFields()
		{
			return $this->myFields;
		}
		function getNumFields()
		{
			return $this->numFields;
		}
		function save()
		{
			if ($this->entry_exists)
			{
				$query = "UPDATE " . $this->tableName . " SET";
				$firstUpd = false;
				for ($i = 0; $i < $this->numFields; $i++)
				{
					if ($this->indexName != $this->myFields[$i]["name"])
					{
						if ($firstUpd != false)
							$query = $query . ",";
						else
							$firstUpd = true;
						$query = $query . " ". $this->myFields[$i]["name"] . " = '" . addslashes($this->{$this->myFields[$i]["name"]}) . "'";
					}
				}
				$query = $query . " WHERE " . $this->indexName . " = '" . $this->{$this->indexName2} . "';";
				mysqli_query($this->conn,$query);
				if (mysqli_error($this->conn))
				{
					return false;
				}
			}
			else
			{
				$query = "INSERT INTO " . $this->tableName . "(";
				for ($i = 0; $i < $this->numFields; $i++)
				{
					if ($i != 0)
						$query = $query . ", ";
					$query = $query . $this->myFields[$i]["name"];
				}
				$query = $query . ") VALUES(";
				for ($i = 0; $i < $this->numFields; $i++)
				{
					if ($i != 0)
						$query = $query . ", ";
					$query = $query . "'" . addslashes($this->{$this->myFields[$i]["name"]}) . "'";
				}
				$query = $query . ");";
				mysqli_query($this->conn,$query);
				if (mysqli_error($this->conn))
				{
					return false;
				}
				$this->{$this->indexName} = mysqli_insert_id($this->conn);
			}
			return $this->{$this->indexName};
		}
		function deleteRow($id = "")
		{
			if ($id == "")
				$id = $this->{$this->indexName};
			$query = "DELETE FROM " . $this->tableName . " WHERE ID='" . $id . "';";
			return mysqli_query($this->conn,$query);
		}
	}
?>
