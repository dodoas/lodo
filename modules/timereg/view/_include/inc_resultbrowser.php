<?PHP
/****************************************************************************
** Copyright (c) 2004-2005 Actra.
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** A class for dividing a result set into several browseable pages.
**
** Form/link commands are as follow:
** rb_page	-	Current page - REQUIRED!!
** rb_next	-	Go to next page
** rb_prev	-	Go to previous page
** rb_first	-	Go to first page
** rb_last	-	Go to last page
****************************************************************************/
require_once 'inc_database.php';

class ResultBrowser
{
	var $db;			// A db class
	var $resultSet;		// Resultset from class Db

	var $itemsPrPage;	// Number of items pr page
	var $currentPage;	// The current page

	/* Storage */
	var $lastPageNumber;
	var $itemCounter;	// Counts items displayed to the user. Used to control when to stop

/****************************************************************************
** CONSTRUCTOR
****************************************************************************/
	function ResultBrowser( $db, $resultSet, $itemsPrPage )
	{
		$this->currentPage = 1;
		$this->db = $db;
		$this->resultSet = $resultSet;
		$this->itemsPrPage = $itemsPrPage;
		$this->itemCounter = 0;

		$this->lastPageNumber = 0;

		// Get input from the web interface
		$this->DoUserInput();
	}

/****************************************************************************
** GO FUNCTIONS
*************************************************************************
	/**
 	* @return unknown
 	* @desc Moves the database row pointer to the correct row
 	* corresponding to the current page.
	*/
	function GoCurrentPage()
	{
		$retval = true;

		/* Get what row the current page equals to */
		$row = $this->GetRowFromPage( $this->currentPage );

		// We can't allow 0, in all cases we will not have to move the result pointer
		// if we want the first row. This is a web page, not an application where the
		// state is stored between each user call.
		if ($row > 0) {
			$retval = $this->db->GoRow( $this->resultSet, $row );
		}

		return( $retval );
	}

	/**
	* @return unknown
	* @desc Go to first page.
 	*/
	function GoFirstPage( )
	{
		$retval = false;
		if ( $this->SetPage( 1 ) )
		{
			$retval = $this->GoCurrentPage();
		}

		return( $retval );
	}

	/**
	* @return unknown
	* @desc Go to last page.
 	*/
	function GoLastPage( )
	{
		$retval = false;
		if ( $this->SetPage( $this->GetLastPage() ) )
		{
			$retval = $this->GoCurrentPage();
		}

		return( $retval );
	}

	/**
	* @return unknown
	* @desc Go to next page.
 	*/
	function GoNextPage( )
	{
		$retval = false;
		if ( $this->SetPage( ($this->currentPage + 1) ) )
		{
			$retval = $this->GoCurrentPage();
		}

		return( $retval );
	}

	/**
	* @return unknown
	* @desc Go to previous page.
 	*/
	function GoPrevPage( )
	{
		$retval = false;
		if ( $this->SetPage( ($this->currentPage - 1) ) )
		{
			$retval = $this->GoCurrentPage();
		}

		return( $retval );
	}

/****************************************************************************
** GET FUNCTIONS
****************************************************************************/
	/**
	* @return unknown
	* @desc Get what page number is the last one.
 	*/
	function GetLastPage( )
	{
		/* Check if we've stored the last page number or we should look it up again. */
		if ($this->lastPageNumber == 0)
		{
			/* This is the first call, need to calculate the last page number */
			$this->lastPageNumber = intval(
				$this->db->NumRows( $this->resultSet ) / $this->itemsPrPage
				) + 1;
		}

		return( $this->lastPageNumber );
	}

	/**
	* @return unknown
	* @desc Get current page number
 	*/
	function GetCurrentPage( )
	{
		return( $this->currentPage );
	}

	/**
	* @return unknown
	* @param $page unknown
	* @desc Get row number for a specified page.
	*/
	function GetRowFromPage( $page )
	{
		return(
			($this->itemsPrPage * ($page - 1))
		);
	}

/****************************************************************************
** SET FUNCTIONS
****************************************************************************/
	function SetPage( $pageNumber )
	{
		/* Need to check if this page is valid */
		$validPage = true;

		if ($pageNumber < 1) {
			// Page given is below 1, which is invalid
			$validPage = false;
		}
		elseif ( $pageNumber > $this->GetLastPage() ) {
			// Page given is higher than the highest page and is invalid
			$validPage = false;
		}

		// If the page given was valid, set current page to this page
		if ( $validPage )
			$this->currentPage = $pageNumber;

		// Return if the current page was set to the given page
		return( $validPage );
	}

/****************************************************************************
** HIGH LEVEL FUNCTIONS
*************************************************************************
	/**
	* @return void
	* @desc Interface to the web: Checks current page and browsing commands.
	*/
	function DoUserInput()
	{
		/* Get which page we are at */
		$page = $_REQUEST['rb_page'];
		if ($page == "" || !is_numeric($page) ) {
			$page = 1;
		}
		$this->SetPage( $page );
		if ($page != 1) {
			$this->GoCurrentPage();
		}

		/* Check for next, prev etc commands */
		if ($_REQUEST['rb_next'] != "") {
			$this->GoNextPage( );
		}
		elseif ($_REQUEST['rb_prev'] != "") {
			$this->GoPrevPage( );
		}
		elseif ($_REQUEST['rb_first'] != "") {
			$this->GoFirstPage( );
		}
		elseif ($_REQUEST['rb_last'] != "") {
			$this->GoLastPage( );
		}
	}

	/**
 	* @return unknown
 	* @param $link Query to add to links that this controller creates. E.g.: a=1&b=4
 	* @desc Returns HTML-code for displaying page numbers and page navigation.
 	*/
	function Display( $link )
	{
		$numPages = $this->GetLastPage();
		$outText = "";

		/* Check if there are more than one page. If there are only one page we don't need a page controller */
		if ($numPages > 1)
		{
			$outText = "<b>";

			/* Check if link contains something. This decides how to link our query
			content with the $link query content (Link with '?' or '&') */
			if ($link == "") {
				// We insert '?' ourself below
				$linkCode = "";
			}
			else {
				$linkCode = "&";
			}

			for ($counter = 1; $counter < ($numPages + 1); $counter++)
			{
				if ($counter == $this->currentPage) {
					$outText .= " $counter ";
				}
				else {
					//                                                    Here it is ('?')
					$outText .= " <a href=\"" . $_SERVER['SCRIPT_NAME'] . "?" . $link . $linkCode . "rb_page=" . $counter . "\">" . $counter . "</a> ";
				}
			}
			$outText .= "</b>";
		}

		return( $outText );
	}

	/**
	* @return unknown
	* @desc Check if number of items displayed in the web interface has hit number of items pr page.
	* If so: tell the web interface to quit displaying more items by returning false.
 	*/
	function DoNextItem()
	{
		$nextRow = true;
		$this->itemCounter++;

		// Have we hit max number of items pr page?
		if ($this->itemCounter > $this->itemsPrPage) {
			// Yes, set nextRow to false
			$nextRow = false;
		}

		return( $nextRow );
	}
}
?>
