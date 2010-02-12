<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Class for integrating with Lodo and Empatic framework.
****************************************************************************/
class Lodo
{
	var $inLodo;	/* Bool: true - we're running in the Lodo framework, false - we're not */

	var $lodoCurrentUserId;
	var $lodoCurrentClientId;
	var $lodoCurrentClientName;

	/* For schemas */
	var $lodoCompany;

	var $lodoUrlValues; /* Key / values to be included in a url */

	/* Database stuff */
	var $lodoDbHost;
	var $lodoDbUsername;
	var $lodoDbPassword;
	var $lodoDbDatabase;

	var $LODOURLTYPE_HREF;
	var $LODOURLTYPE_FORM;
	var $LODOURLTYPE_REDIRECT;

	function Lodo()
	{
		global $_sess, $_SETUP, $_SETUP;

		$this->LODOURLTYPE_HREF = 0;
		$this->LODOURLTYPE_FORM = 1;
		$this->LODOURLTYPE_REDIRECT = 2;

		/* Check if we're running in Lodo */
		if ( is_object( $_sess ) )
		{
			$this->inLodo = true;
			$this->lodoCurrentUserId = $_sess->login_id;
			$this->lodoCurrentClientName = $_sess->get_companydef('VName');
			$this->lodoCurrentClientId = 2; // Fake

			$this->lodoDbHost = $_SETUP['DB_SERVER'][0];
			$this->lodoDbUsername = $_SETUP['DB_USER'][0];
			$this->lodoDbPassword = $_SETUP['DB_PASSWORD'][0];
			$this->lodoDbDatabase = $_SETUP['DB_NAME'][0];

			$this->lodoUrlValues = array();

			/* Change this to lodo data */
			$this->lodoCompany = array(
				'name' => $_sess->get_companydef('VName'),
				'streetaddr' => $_sess->get_companydef('VAddress'),
				'zipcode' => $_sess->get_companydef('VZipCode'),
				'city' => $_sess->get_companydef('VCity')
				);
		}
		/* We're running in stand alone mode */
		else
		{
			$this->inLodo = false;
			$this->lodoCurrentUserId = 1; // Fake id for testing
			$this->lodoCurrentClientId = 2; // Fake

			$this->lodoDbHost = "localhost";
			$this->lodoDbUsername = "root";
			$this->lodoDbPassword = "";
			$this->lodoDbDatabase = "lodoaltinn";

			$this->lodoUrlValues = array();

			$this->lodoCompany = array(
				'name' => 'Konsulentvikaren',
				'streetaddr' => 'Vippen 1',
				'zipcode' => '0372',
				'city' => 'Oslo'
				);
		}
	}

	/* Make a new lodourl */
	function LodoUrlNew( )
	{
		return( $this->lodoUrlValues );
	}

	/* Add key / value pairs to the url */
	function LodoUrlAdd( $url, $values )
	{
		foreach( $values as $key => $value ) {
			$url[ $key ] = $value;
		}
	}

	/* Delete a key/value from the url */
	function LodoUrlDelete( $url, $values )
	{
		foreach( $values as $key => $value ) {
			unset( $url[ $key ] );
		}
	}

	/* Return an url string with the right key / value pairs */
	function LodoUrlGet( $url, $type, $location )
	{
		global $_SERVER;

		if ( !is_array($url) ) {$url = $this->lodoUrlValues;}

		if ( ($type == $this->LODOURLTYPE_HREF) || ($type == $this->LODOURLTYPE_REDIRECT) )
		{
			if ($this->inLodo) {$retVal = $_SERVER['SCRIPT_NAME'] . '?t=' . $location;}
			else {
				$pos = strpos( $location, '.' );
				if ($pos > 0) {$retVal = substr( $location, $pos + 1 );}
				else {$retVal = $location;}
				$retVal .= '.php?';
			}
		}
		elseif ( $type == $this->LODOURLTYPE_FORM )
		{
			$retVal = '<input type="hidden" name="t" value="' . urlencode($location) . '">';
		}

		foreach( $url as $key => $value )
		{
			if ( ($type == $this->LODOURLTYPE_HREF) || ($type == $this->LODOURLTYPE_REDIRECT) )
			{
				$retVal .= "&amp;";
//				if ($retVal != '') { }
				$retVal .= urlencode($key) . '=' . urlencode($value);
			}
			elseif ( $type == $this->LODOURLTYPE_FORM )
			{
				$retVal .= '<input type="hidden" name="' . urlencode($key) . '" value="' . urlencode($value) . '">';
			}
		}

		if ( $type == $this->LODOURLTYPE_REDIRECT ) {
			$retVal = str_replace( '&amp;', '&', $retVal );
		}
		return( $retVal );
	}

	function LodoUrlSelf( $url, $type )
	{
		if ($this->inLodo) {
			return( $this->LodoUrlGet( $url, $type, $_REQUEST['t'] ) );
		}
		else
		{
			$fileName = $_SERVER['PHP_SELF'];
			$pos = strrpos( $fileName, '/' );
			if ($pos > 0)
			{
				$fileName = substr($fileName, $pos + 1);
			}
			$pos = strrpos( $fileName, '.' );
			if ($pos > 0)
			{
				$fileName = substr($fileName, 0, $pos);
			}
			return( $this->LodoUrlGet( $url, $type, 'altinn.' . $fileName ) );
		}
	}

	/* Print / include necessary stuff for the page header */
	function PrintHeadContent()
	{
		global $_SETUP;

		if ( $this->inLodo ) {
			require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc";
		}
		else {
?>
<!-- include css -->
<?php
		}
	}

	/* Print / include necessary stuff for the page body */
	function PrintBodyContent()
	{
		global $_SETUP;

		if ( $this->inLodo ) {
		}
		else {
?>
<!-- include what you want -->
<?php
		}
	}
}
?>
