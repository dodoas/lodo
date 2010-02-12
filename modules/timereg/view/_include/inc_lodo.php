<?PHP
/***************************************************************************
** Copyright (c) 2004-2005 Lodo.no.
** Developed by Actra / Gunnar Skeid (gunnar@actra.no)
**
** Interface between Lodo account system and this time account system.
***************************************************************************/
require_once 'inc_database.php';

class Lodo
{
	var $currentUserId;		// User that is logged in
	var $currentClientId;	// Client in the Lodo system

	/* Session */
	var $externalSessionClientId;
	var $externalSessionUserId;

	/* Database table and field name */
	var $externalCustomerTable;		// Name of Lodo customer table
	var $externalCustomerId;		// Name of Lodo customer ID field
	var $externalCustomerName;		// Name of Lodo customer name field
	var $externalCustomerClientId;	// Name of Lodo client ID field in customer table

	function Lodo()
	{
		global $_sess, $_lib;

		$this->externalSessionClientId = "client";
		$this->externalSessionUserId = "user";

		$this->externalCustomerTable = "customer";
		$this->externalCustomerId = "id";
		$this->externalCustomerName = "name";
//		$this->externalCustomerClientId = "id";

		session_start();
		/* Get/set client id */
		if ( !isset( $_SESSION[ $this->externalSessionClientId ] ) ) {
			$_SESSION[ $this->externalSessionClientId ] = $_lib['sess']->defcompany_id;	// Just a hack until we're setup at the Lodo server
		}
		$this->currentClientId = $_SESSION[ $this->externalSessionClientId ];

		/* Get/set user id */
		if ( is_numeric( $_REQUEST['userlogin'] ) )
		{
			$_SESSION[ $this->externalSessionUserId ] = $_REQUEST['userlogin'];
		}
		else
		{
			if ( !isset( $_SESSION[ $this->externalSessionUserId ] ) ) {
				$_SESSION[ $this->externalSessionUserId ] = $_lib['sess']->login_id;	// Just a hack until we're setup at the Lodo server
			}
		}
		if(isset($_REQUEST['PersonID'])) {
		  $this->currentUserId = $_REQUEST['PersonID'];
		} else {
		  $this->currentUserId = $_SESSION[ $this->externalSessionUserId ];
        }
	}

	/**
	 * Returns an URL (type=0) or XHTML-code(type=1) with hidden form elements that is global for this application.
	 * Should be used in all forms.
	 *
	 * @param $type $array
	 * @return XHTML-code or URL
	 */
	function GetGoUrl( $type, $array )
	{
		/* Type 1 means that we should return XHTML to be used in a form */
		if ( $type == 1 ) {
			$retVal = "";
		}
		/* Type 0 means that we should return an URL */
		else {
			$retVal = "?";
		}
		$first = true;	// Used to track if this is the first loop
		foreach ($array AS $key => $value)
		{
			if ( $type == 1 ) {
				$retVal .= "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
			}
			else
			{
				if ( $first ) {
					$first = false;
					/* This is first loop: Do not include & as prefix */
					$retVal .= "" . $key . "=" . urlencode($value);
				}
				else {
					/* This is not first loop: Include & as prefix */
					$retVal .= "&amp;" . $key . "=" . urlencode($value);
				}
			}

		}

		return($retVal);
	}

	function GetViewCustomerUrl( $ext_customer_id )
	{
		global $_SETUP;

		return($_SETUP['DISPATCH'] . "t=accountplan.reskontro&accountplan.AccountPlanID=$AccountPlanID");
	}

	function CheckNewCustomers( $db )
	{
/* TODO: Check which customers has been deleted from Lodo. They should
be deleted here too */
		/* Return new customers in an array */
		$retVal = array();

		/* Get customers in the Lodo system that isn't in our db */
		$sqlStr = "SELECT ext." . $this->externalCustomerId . ",ext." . $this->externalCustomerName . ",c.ext_customer_id" .
			" FROM " . $this->externalCustomerTable . " AS ext LEFT JOIN timer_customer AS c" .
			" ON c.ext_customer_id=ext." . $this->externalCustomerId .
			" WHERE (c.ext_client_id=" . $this->currentClientId . " AND c.user_id=" . $this->currentUserId . ")";
		if ( ($rs = $db->Query( $sqlStr )) )
		{
			$doNext = true; // Flow variable: See inside the next if

			/* If there where no new customers returned from this query it can mean
			that our timer_customer table is empty. That would mean that we must include
			all customers from Lodo */
			if (  $db->NumRows( $rs ) < 1 )
			{
				$doNext = false;	// Flow variable: Should we execute the while ( ..NextRow) below for
									// filling in the retval variable?
				$db->EndQuery($rs );

				/* Is our table empty? */
				$sqlStr2 = "SELECT COUNT(customer_id) FROM timer_customer" .
					" WHERE (ext_client_id=" . $this->currentClientId . " AND user_id=" . $this->currentUserId . ")";
				if ( ($rs2 = $db->Query( $sqlStr2 )) )
				{
					if ( ($row2 = $db->NextRow( $rs2 )) )
					{
						if ($row2[ 0 ] == 0)
						{
							/* All customers in Lodo base is new */
							$sqlStr = "SELECT ext." . $this->externalCustomerId . ",ext." . $this->externalCustomerName . "" .
								" FROM " . $this->externalCustomerTable . " AS ext" .
								" WHERE 1";

							/* Use the while loop below to fill the return array: Execute an query */
							if ( ($rs = $db->Query( $sqlStr )) ) {
								$doNext = true;
							}

						}
					}
					$db->EndQuery( $rs2 );
				}

			}

			if ( $doNext )
			{
				/* List all new */
				while ( ($row = $db->NextRow( $rs )) )
				{
					/* Null means that the row didn't exists in our customer table */
//echo("R: '" .  $row[ 1 ] . $row[ 2 ] . "'<br>\n");
					if ( is_null( $row[ 2 ] ) )
					{
						/* Store new customers in return array */
						$retVal[ count($retVal) + 1 ] = array( $row[$this->externalCustomerName], $row[$this->externalCustomerId] );
					}
				}
				$db->EndQuery($rs );
			}

		}

		return( $retVal );
	}
}
?>