<?PHP
/***************************************************************************
** Copyright (c) 2004-2005 Lodo.no.
** Developed by Actra / Gunnar Skeid (gunnar@actra.no)
**
** Interface between Lodo account system and this time account system.
***************************************************************************/
require_once 'inc_database.php';
require_once 'inc_lodo.php';

class LodoTimerLog
{
	var $db;
	var $lodo;

	var $currentYear;	// Current year we're loging
	var $currentMonth;	// Current month we're loging
	var $currentView;	// Current view: hours / money

	/* We need this to know when to stop the loop through the days of the month */
	var $nextMonthYear;		// Year of the month after the one we're loging
	var $nextMonthMonth;	// Month of the month after the one we're loging

	/* We want to store the user's current year and month, so we use session.
	Default is current month and year. */
	var $sessionNameCurrentYear;
	var $sessionNameCurrentMonth;
	var $sessionNameCurrentView;

	var $goUrlArray;

	var $daysArray;
	var $monthsArray;

	var $holidayCache;	// Holidays during this month
	var $totalWorkDays;

	/**
	 * Constructor: db object and lodo object. Also saves info entered in form for a customer.
	 *
	 * @param unknown $db
	 * @return LodoTimerEstimate
	 */
	function LodoTimerLog( $db, $lodo, $goUrlArray )
	{
		$this->db = $db;
		$this->lodo = $lodo;

		$this->sessionNameCurrentYear = "LTR_LOG_YEAR";
		$this->sessionNameCurrentMonth = "LTR_LOG_MONTH";

		$this->goUrlArray = $goUrlArray;

		$this->holidayCache = array();
		$this->totalWorkDays = 0;

		$this->daysArray = array("Søndag","Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag");
		$this->monthsArray = array("Januar","Februar","Mars","April","Mai","Juni","Juli","August","September","Oktober","November","Desember");

		$this->currentView = $_SESSION[ $this->sessionNameCurrentView ] = 0;

		/* Should we update the current month and year? */
		if ( $_REQUEST['cmd'] == "setdate" )
		{
			/* Set current year and month in this object and in the session, if the input from the form is numeric */
			if ( is_numeric( $_REQUEST['current_year'] ) && is_numeric( $_REQUEST['current_month'] ) ) {
				$this->currentYear = $_SESSION[ $this->sessionNameCurrentYear ] = $_REQUEST['current_year'];
				$this->currentMonth = $_SESSION[ $this->sessionNameCurrentMonth ] = $_REQUEST['current_month'];
			}
		}
		else
		{
			// Use session to store current month and year
			/* YEAR */
			if ( !isset( $_SESSION[ $this->sessionNameCurrentYear ] ) ) {
				// Default is the current year
				$this->currentYear = $_SESSION[ $this->sessionNameCurrentYear ] = date("Y");
			} else {
				// Current year is stored, so we use that
				$this->currentYear = $_SESSION[ $this->sessionNameCurrentYear ];
			}
			/* MONTH */
			if ( !isset( $_SESSION[ $this->sessionNameCurrentMonth ] ) ) {
				// Default is the current month
				$this->currentMonth = $_SESSION[ $this->sessionNameCurrentMonth ] = date("m");
			} else {
				// Current year is stored, so we use that
				$this->currentMonth = $_SESSION[ $this->sessionNameCurrentMonth ];
			}
			/* VIEW */
//			if ( !isset( $_SESSION[ $this->sessionNameCurrentView ] ) ) {
				// Default is the current month
//				$this->currentView = $_SESSION[ $this->sessionNameCurrentView ] = 0;
//			} else {
				// Current year is stored, so we use that
//				$this->currentView = $_SESSION[ $this->sessionNameCurrentView ];
//			}
		}
		/* Determind what is the first day in the next month. This is where we will stop
		our loop */
		$this->nextMonthMonth = $this->currentMonth + 1;
		if ($this->nextMonthMonth > 12) {
			$this->nextMonthMonth = 1;
			$this->nextMonthYear = $this->currentYear + 1;
		}
		else {
			$this->nextMonthYear = $this->currentYear;
		}
	}

	/**
	 * Estimate header add, modify and delete estimate sheets.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayMainHeader()
	{
		$tmpStr = $this->lodo->GetGoUrl( 1, $this->goUrlArray );

		$retVal = "<div align=\"center\"><form action=\"" . $_SERVER['SCRIPT_NAME'] . "\"><input type=\"hidden\" name=\"cmd\" value=\"setdate\" />" . $tmpStr .
			 "";
		$retVal .= "<select name=\"current_month\">";
		for ($month = 1; $month < 13; $month++)
		{
			/* Do a selected if it's the current month */
			if ($this->currentMonth == $month) {
				$retVal .= "  <option selected value=\"$month\">" . $this->monthsArray[$month - 1] . "</option>";
			}
			else {
				$retVal .= "  <option value=\"$month\">" . $this->monthsArray[$month - 1]. "</option>";
			}
		}
		$retVal .= "</select> <select name=\"current_year\">";
		for ($year = 2005; $year < 2036; $year++)
		{
			/* Do a selected if it's the current year */
			if ($this->currentYear == $year) {
				$retVal .= "  <option selected>" . $year . "</option>";
			}
			else {
				$retVal .= "  <option>" . $year . "</option>";
			}
		}
		$retVal .= "</select>";
		/* Display hours or money */
/*		if ( $this->currentView == 0 ) {
			$retVal .= " <select name=\"view\"><option value=\"0\" selected>Vis timer</option><option value=\"1\">Vis kroner</option></select>";
		}
		else {
			$retVal .= " <select name=\"view\"><option value=\"0\">Vis timer</option><option value=\"1\" selected>Vis kroner</option></select>";
		}*/

		$retVal .= " <input type=\"submit\" value=\"Vis\" />";
		$retVal .= "</form></div>";
		return( $retVal );
	}

	function CheckHoliday( $timeStamp )
	{
		$retVal = false;
		$sqlStr = "SELECT day FROM timer_holidays WHERE day='" . $timeStamp . "'";
		if ( ($rs = $this->db->Query( $sqlStr )) )
		{
			if ( ($row = $this->db->NextRow( $rs )) )
			{
				$retVal = true;
			}
			$this->db->EndQuery($rs );
		}
		return($retVal);
	}

	/**
	 * Internal: Saves info entered in the form for a customer.
	 *
	 * @param
	 * @return
	 */
	function SaveForm()
	{

	}

	/**
	 * Displays the header above the companies.
	 *
	 * @param
	 * @return XHTML string with output
	 */
	function DisplayHeader()
	{
		$retVal = "";

		/* Display all days in this month */
		$toDate = mktime(0, 0, 0, $this->nextMonthMonth, 1, $this->nextMonthYear);

		$loopCounter = 1;
		$day = mktime(0, 0, 0, $this->currentMonth, 1, $this->currentYear);
		while ( $day < $toDate )
		{
			/* Is this a holiday? */
			$holiday = $this->CheckHoliday( $day );
			$this->holidayCache[ $loopCounter - 1 ] = $holiday;

			/* Put darker background on holidays */
			$dayOfWeek = date("w", $day);
			if ($holiday || $dayOfWeek == 0 || $dayOfWeek == 6) {
				$freeDay = true;
				$bgClass = "bgd";
			}
			else {
				$freeDay = false;
				$bgClass = "bgl";
				$this->totalWorkDays++;
			}

			if ( $loopCounter == 1 ) {
				$retVal .= "<td style=\"border-left: 1px solid #000000;\" class=\"$bgClass\">";
			}
			/* Cells after the first one should have nothing */
			else {
				$retVal .= "<td class=\"$bgClass\">";
			}

			/* Look up log for worked from and to hour */

			/* If there is a log entry for when the user began and ended working that day: Display it */
			if ( ($timeFrom == 0) || ($timeTo == 0) )
			{
				$retVal .= date("d", $day) . "&#160;<br />" . substr($this->daysArray[ $dayOfWeek ],0,2) . "</td>";
			}
			else
			{
				$retVal .= date("d", $day) . "&#160;<br />" . substr($this->daysArray[ $dayOfWeek ],0,2) . "</td>";
			}

			$loopCounter++;
			$day = strtotime("+1 day", $day);
		}
		$retVal .= "<td style=\"border-right: 1px solid #000000;\" class=\"bgl\">&#160;</td>";
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
		$retVal = "";

		/* Display all days in this month */
		$toDate = mktime(0, 0, 0, $this->nextMonthMonth, 1, $this->nextMonthYear);

		/* For filtering active/nonactive */
		if ($_REQUEST['srch_active'] != "1") {$sqlAdd = " AND customer.active=1";}

		$loopCounter = 1;
		$totalHours = 0;

		$day = mktime(0, 0, 0, $this->currentMonth, 1, $this->currentYear);
		while ( $day < $toDate )
		{
			/* Put darker background on holidays */
			$holiday = $this->holidayCache[ $loopCounter - 1 ];
			$dayOfWeek = date("w", $day);
			if ($holiday || $dayOfWeek == 0 || $dayOfWeek == 6) {
				$freeDay = true;
				$bgClass = "bgd";
			}
			else {
				$freeDay = false;
				$bgClass = "bgl";
			}

			if ( $loopCounter == 1 ) {
				$retVal .= "<td align=\"right\" style=\"border-left: 1px solid #000000;\" class=\"$bgClass\">";
			}
			/* Cells after the first one should have nothing */
			else {
				$retVal .= "<td align=\"right\" class=\"$bgClass\">";
			}

			$typePreChar = "";
			$typePostChar = "";

			/* Check if we should display hours or money */
			if ( $this->currentView == 1 )
			{
				$typePreChar = "kr";

				/* First look up how much each project type is worth in money  */
				$projectCost = array();
				$sqlStr = "SELECT hourly_cost,projecttype_id " .
					" FROM timer_projecttype " .
					" WHERE ext_client_id=" . $this->lodo->currentClientId;
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					while ( ($row = $this->db->NextRow( $rs )) )
					{
						$projectCost["pt_" . $row['projecttype_id']] = $row['hourly_cost'];
					}
					$this->db->EndQuery( $rs );
				}

				$todayAmount = 0;
				$logId = 0;
				$sqlStr = "SELECT SUM(log.amount),log.projecttype_id " .
					" FROM timer_logday AS day,timer_logproject AS log " .
					" WHERE day.log_id=log.log_id " .
					" AND day.ext_user_id=" . $this->lodo->currentUserId .
					" AND day.ext_client_id=" . $this->lodo->currentClientId .
					" AND day.date='" . date("Y-m-d 00:00:00", $day) . "'" .
					" GROUP BY log.projecttype_id";
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					while ( ($row = $this->db->NextRow( $rs )) )
					{
						if (is_numeric($row[0])) {$todayAmount += ($row[0] * $projectCost["pt_" . $row['projecttype_id']]);}
						else {$todayAmount += 0;}
					}
					$this->db->EndQuery($rs );
				}
				$totalHours += $todayAmount;

				$retVal .= "&#160;" . str_replace(".", ",", $todayAmount) . "";
			}
			else
			{
				$typePostChar = "t";

				/* Count hours for this day for ALL companies */
				$amount = 0;
				$sqlStr = "SELECT SUM(log.amount) " .
					" FROM timer_logday AS day,timer_logproject AS log " .
					" WHERE day.log_id=log.log_id " .
					" AND day.ext_user_id=" . $this->lodo->currentUserId .
					" AND day.ext_client_id=" . $this->lodo->currentClientId .
					" AND day.date='" . date("Y-m-d 00:00:00", $day) . "'";
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					if ( ($row = $this->db->NextRow( $rs )) )
					{
						if (is_numeric($row[0])) {$amount = $row[0];}
						else {$amount = 0;}

						$totalHours += $amount;
					}
					$this->db->EndQuery($rs );
				}
			}

			$retVal .= $amount . $typePostChar . "</td>";
			$loopCounter++;
			$day = strtotime("+1 day", $day);
		}
		$retVal .= "<td align=\"right\" style=\"border-right: 1px solid #000000;\" class=\"bgl\">" . $typePreChar . str_replace(".", ",", $totalHours) . $typePostChar . "</td>";

		return( $retVal );
	}

	function DisplayMainFooter(  )
	{
		$retVal = "<td align=\"right\" style=\"border-right: 1px solid #000000; border-bottom: 1px solid #000000; border-left: 1px solid #000000;\" class=\"bgl\" colspan=\"100\">Normal arbeidstid denne måneden er " . str_replace(".", ",", ($this->totalWorkDays*7.5)) . " timer eller " . $this->totalWorkDays . " dager</td>";

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
		global $_SETUP;

		$retVal = "";

		/* Display all days in this month */
		$toDate = mktime(0, 0, 0, $this->nextMonthMonth, 1, $this->nextMonthYear);

		$loopCounter = 1;
		$totalHours = 0;	// How many hours worked for this company this month
		$day = mktime(0, 0, 0, $this->currentMonth, 1, $this->currentYear);
		while ( $day < $toDate )
		{
			/* Put darker background on holidays */
			$holiday = $this->holidayCache[ $loopCounter - 1 ];
			$dayOfWeek = date("w", $day);
			if ($holiday || $dayOfWeek == 0 || $dayOfWeek == 6) {
				$freeDay = true;
				$bgClass = "bgdd";
			}
			else {
				$freeDay = false;
				$bgClass = "bgld";
			}

			if ( $loopCounter == 1 ) {
				$retVal .= "<td align=\"right\" class=\"$bgClass\">";
			}
			/* Cells after the first one should have nothing */
			else {
				$retVal .= "<td align=\"right\" class=\"$bgClass\">";
			}

			$typePreChar = "";
			$typePostChar = "";

			/* Check if we should display hours or money */
			if ( $this->currentView == 1 )
			{
				$typePreChar = "kr";

				/* First look up how much each project type is worth in money  */
				$projectCost = array();
				$sqlStr = "SELECT hourly_cost,projecttype_id " .
					" FROM timer_projecttype " .
					" WHERE ext_client_id=" . $this->lodo->currentClientId;
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					while ( ($row = $this->db->NextRow( $rs )) )
					{
						$projectCost["pt_" . $row['projecttype_id']] = $row['hourly_cost'];
					}
					$this->db->EndQuery( $rs );
				}

				/* Count money for this day for this company */
				$todayAmount = 0;
				$logId = 0;
				$sqlStr = "SELECT SUM(log.amount),log.projecttype_id " .
					" FROM timer_logday AS day,timer_logproject AS log " .
					" WHERE day.log_id=log.log_id " .
					" AND day.ext_user_id=" . $this->lodo->currentUserId .
					" AND day.ext_client_id=" . $this->lodo->currentClientId .
					" AND log.customer_id=" . $customerId .
					" AND day.date='" . date("Y-m-d 00:00:00", $day) . "'" .
					" GROUP BY log.projecttype_id";
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					while ( ($row = $this->db->NextRow( $rs )) )
					{
						if (is_numeric($row[0])) {$todayAmount += ($row[0] * $projectCost["pt_" . $row['projecttype_id']]);}
						else {$todayAmount += 0;}
					}
					$this->db->EndQuery($rs );
				}
				$totalHours += $todayAmount;

				$retVal .= "&#160;<a href=\"javascript:PopUp( '" . $_SETUP['DISPATCH'] . "t=timereg.logday&customer_id=$customerId&day=$day', 320, 500 );\">" . str_replace(".", ",", $todayAmount) . "</a></td>";
			}
			else
			{
				$typePostChar = "t";

				/* Count hours for this day for this company */
				$todayAmount = 0;
				$logId = 0;
				$sqlStr = "SELECT SUM(log.amount) " .
					" FROM timer_logday AS day,timer_logproject AS log " .
					" WHERE day.log_id=log.log_id " .
					" AND day.ext_user_id=" . $this->lodo->currentUserId .
					" AND day.ext_client_id=" . $this->lodo->currentClientId .
					" AND log.customer_id=" . $customerId .
					" AND day.date='" . date("Y-m-d 00:00:00", $day) . "'";
				if ( ($rs = $this->db->Query( $sqlStr )) )
				{
					if ( ($row = $this->db->NextRow( $rs )) )
					{
						if (is_numeric($row[0])) {$todayAmount = $row[0];}
						else {$todayAmount = 0;}

						$totalHours += $todayAmount;
					}
					$this->db->EndQuery($rs );
				}

				$retVal .= "<a href=\"javascript:PopUp( '" . $_SETUP['DISPATCH'] . "t=timereg.logday&customer_id=$customerId&day=$day', 320, 500 );\">" . str_replace(".", ",", $todayAmount) . "</a></td>";
			}
			$loopCounter++;
			$day = strtotime("+1 day", $day);
		}
		$retVal .= "<td align=\"right\" style=\"border-right: 1px solid #000000;\" class=\"bgl\">&#160;" . $typePreChar . str_replace(".", ",", $totalHours) . $typePostChar . "</td>";

		return( $retVal );
	}
}
?>
