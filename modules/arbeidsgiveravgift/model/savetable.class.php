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
		private $entry_exists = false;
		private $debug        = false;
		
		public function __construct($tableName, $ID = "")
		{
			global $_SETUP, $_lib;
			$this->numFields    = 0;
			$this->tableName    = $tableName;
			$this->entry_exists = false;

			$query = "SHOW FIELDS FROM " . $this->tableName . ";";
			if($this->debug) print "$query<br>\n";
			$buffer = $_lib['db']->db_query($query);
			while ($data = $_lib['db']->db_fetch_array($buffer))
			{
				if ($data["Key"] == "PRI" )
				{
					$this->indexName    = $data["Field"];
					$this->indexName2   = $data["Field"] . "_";
					$this->{$this->indexName2} = $ID;
				}
				$this->myFields[$this->numFields]["type"] = $data["Type"];
				$this->myFields[$this->numFields]["name"] = $data["Field"];
				$this->numFields++;
			}
			#$_lib['db']->db_free_result($buffer);
			$query = "SELECT * FROM " . $this->tableName . " WHERE " . $this->indexName . " = '" . $ID . "'";
            #print_r($_REQUEST);
			if($this->debug) print "$query<br>\n";
			$buffer = $_lib['db']->db_query($query);
			$data = $_lib['db']->db_fetch_array($buffer);
			for ($i = 0; $i < $this->numFields; $i++)
			{
				$myFieldName = $this->myFields[$i]["name"];
				$this->{$this->myFields[$i]["name"]} = $data[$myFieldName];
			}
			#$_lib['db']->db_free_result($buffer);

			if ($this->{$this->indexName} != "")
			{
				$this->entry_exists = true;
			}
		}
		
		public function set($attrib, $value)
		{
			$this->{$attrib} = $value;
		}
		
		public function get($attrib)
		{
			return stripslashes($this->{$attrib});
		}
		
		public function getFields()
		{
			return $this->myFields;
		}
		
		public function getNumFields()
		{
			return $this->numFields;
		}
		
		public function save()
		{
    	    global $_lib;

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
				$query = $query . " WHERE " . $this->indexName . " = '" . $this->{$this->indexName2} . "'";
				if($this->debug) print "$query<br>\n";
				$_lib['db']->db_update($query);
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
					if ($this->myFields[$i]["name"] != $this->indexName)
						$query = $query . "'" . addslashes($this->{$this->myFields[$i]["name"]}) . "'";
					else
						$query = $query . "''";
				}
				$query = $query . ");";
				if($this->debug) print "$query<br>\n";
				$this->{$this->indexName} = $_lib['db']->db_insert($query);
			}
			return $this->{$this->indexName};
		}
		
		public function deleteRow($id = "")
		{
			if ($id == "")
				$id = $this->{$this->indexName};
			$query = "DELETE FROM " . $this->tableName . " WHERE ID='" . $id . "'";
            if($this->debug) print "$query<br>\n";
			return $_lib['db']->db_delete($query);
		}
	}
?>
