<?PHP
/***************************************************************************
** Copyright (c) 2004-2005 Lodo.no.
** Developed by Actra / Gunnar Skeid (gunnar@actra.no)
**
** Interface between Lodo account system and this time account system.
***************************************************************************/
require_once 'inc_database.php';
require_once 'inc_lodo.php';

class LodoTimerReport
{
	var $db;
	var $lodo;
	
	var $goUrlArray;

	/**
	 * Constructor: db object and lodo object. Also saves info entered in form for a customer.
	 *
	 * @param unknown $db
	 * @return LodoTimerReport
	 */
	function LodoTimerReport( $db, $lodo, $goUrlArray )
	{
		$this->db = $db;
		$this->lodo = $lodo;

		$this->goUrlArray = $goUrlArray;
	}

	/**
	 * Estimate header add, modify and delete estimate sheets.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayMainHeader()
	{
		$retVal = "<h2>Lag rapport</h2>";
		return( $retVal );
	}
	
	/**
	 * Displays the header above the companies.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayHeader()
	{
		$retVal = "<td style=\"border-right: 1px solid #cccccc; border-left: 1px solid #cccccc;\">Til</td>";
		return( $retVal );
	}

	/**
	 * Displays the footer below the companies.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayFooter(  )
	{
//		$retVal = "<td style=\"border-right: 1px solid #cccccc; border-left: 1px solid #cccccc;\"></td>";
		$retVal = "";
		return( $retVal );
	}

	function DisplayMainFooter(  )
	{
		$retVal = "<td style=\"border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000;\" class=\"bgl\" colspan=\"100\">&#160;</td>";
		
		return( $retVal );
	}

	/**
	 * Handles estimate for each company.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayCompany( $customerId )
	{
		$retVal = "<td style=\"border-right: 1px solid #cccccc; border-left: 1px solid #cccccc;\">&#160;</td>";
//		$retVal = "";
		return( $retVal );
	}
}
?>
