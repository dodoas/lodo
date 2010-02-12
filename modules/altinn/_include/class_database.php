<?PHP
/****************************************************************************
** Copyright (c) 1998-2005 Actra AS.
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** A class for connecting to MySQL database. It could be used to interface
** with other databases as well.
**
** Example of usage:
**
** require_once 'inc_database.php';
**
** $db = new Db();
** $db->host = 'localhost';
** $db->username = 'me';
** $db->password = 'secret';
** $db->database = 'mydb';
**
** if ( $db->Connect() )
** {
**		// Do some stuff
** 		$db->Disconnect();
** }
**
****************************************************************************/

require_once 'class_lodo.php';

class Db
{
	/* Default for DB connection */
	var $host;		// Db host to connect to
	var $username;	// Username to db
	var $password;	// Password to db
	var $database;	// What database to make current

	/* DB connection */
	var $conn;		// Connection object

	/* Static for BuildSQLString */
	var $BUILD_INSERT;
	var $BUILD_UPDATE;

	/**
	* @return unknown
	* @desc Constructor
	*/
	function Db( $lodo )
	{
		/* Default DB connection */
		$this->host = $lodo->lodoDbHost;
		$this->username = $lodo->lodoDbUsername;
		$this->password = $lodo->lodoDbPassword;
		$this->database = $lodo->lodoDbDatabase;

		$this->conn = null;

		/* Static for BuildSQLString */
		$this->BUILD_INSERT = 1;
		$this->BUILD_UPDATE = 0;
	}


/****************************************************************************
** CONNECT and DISCONNECT
****************************************************************************/
	/**
	* @return unknown
	* @desc Connects to database and return the connection object
	*/
	function Connect()
	{
	global $_sess;

		$retval = false;

		$this->conn = mysqli_connect( $this->host, $this->username, $this->password );

		if ( $this->conn )
		{
			mysqli_select_db( $this->conn, $this->database );
			$retval = true;
		}

		return($retval);
	}

	/**
	* @return void
	* @desc Disconnects from the database.
	*/
	function Disconnect()
	{
		// Only disconnect if the connection is open
		if (is_object($this->conn)) {
			mysqli_close( $this->conn );
			$this->conn = null;
		}
	}

/****************************************************************************
** QUERIES and handling it
****************************************************************************/
	/**
	* @return unknown
	* @param $queryString unknown
	* @desc Executes an query and returns the resultset.
 	*/
	function Query( $queryString )
	{
		return( mysqli_query ( $this->conn, $queryString ) );
	}

	/**
	* @return unknown
	* @param $rs unknown
	* @desc Returns number of rows in a result set.
 	*/
	function NumRows( $rs )
	{
		return( mysqli_num_rows( $rs ) );
	}

	/**
	* @return unknown
	* @param $rs unknown
	* @desc Go to the specified result row.
 	*/
	function GoRow( $rs, $position )
	{
		 return( mysqli_data_seek( $rs, $position ) );
	}

	/**
	* @return unknown
	* @param $rs unknown
	* @desc Frees query.
 	*/
	function EndQuery( $rs )
	{
		return( mysqli_free_result ( $rs ) );
	}

	/**
	* @return unknown
	* @param $rs unknown
	* @desc Returns the next row from the resultset.
	*/
	function NextRow( $rs )
	{
		return( mysqli_fetch_array( $rs ) );
	}

/****************************************************************************
** HIGH LEVEL FUNCTIONS
****************************************************************************/
	/**
	* @return unknown
	* @param $what unknown
	* @param $values unknown
	* @desc Takes a key/value array and returns the keyword / data part of and INSERT or UPDATE SQL statement.
 	*/
	function BuildSQLString( $what, $values )
	{
		foreach ( $values AS $key => $value )
		{
			if (substr($value, 0,1) == '#' && substr($value, strlen($value)-1, 1) == '#') {
				$values[$key] = "" . str_replace("\\'", "''", $value) . "";
			}
			else {
				$values[$key] = "'" . str_replace("\\'", "''", $value) . "'";
			}

		}

		if ($what == $this->BUILD_INSERT)
		{
			foreach ( $values AS $key => $value )
			{
				$returnValue1 .= "$key,";
				$returnValue2 .= "$value,";
			}
			$returnValue = "(" . rtrim($returnValue1,",") . ") VALUES (" . rtrim($returnValue2,",") . ")";
		}
		else
		{
			foreach ( $values AS $key => $value )
			{
				$returnValue .= "$key=$value,";
			}
			$returnValue = rtrim($returnValue,",");
		}

		return($returnValue);
	}
}
?>
