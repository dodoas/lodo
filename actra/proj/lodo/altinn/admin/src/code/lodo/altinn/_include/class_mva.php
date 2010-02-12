<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Class for handling MVA stuff.
****************************************************************************/
class Mva
{
	var $termin;
	var $terminNames; 

	var $TERMINTYPE_YEARLY;
	var $TERMINTYPE_QUARTERLY;
	var $TERMINTYPE_SECONDMONTH;
	var $TERMINTYPE_MONTHLY;
	var $TERMINTYPE_FORTHNIGHT;
	var $TERMINTYPE_WEEKLY;

	var $NEXTTERMIN_YEARLY;
	var $NEXTTERMIN_QUARTERLY;
	var $NEXTTERMIN_SECONDMONTH;
	var $NEXTTERMIN_MONTHLY;
	var $NEXTTERMIN_FORTHNIGHT;
	var $NEXTTERMIN_WEEKLY;

	var $nextTerminArray;

	var $LISTTYPE_SELECT;

	/* Get a list of all termins in a termin type.
	 * Input: type - termin type (E.g. weekly)
	 * 	listtype - e.g. select
	 * 	active - What item to make active in the list */
	function GetTerminList( $type, $listType, $active )
	{
		$retVal = '';
		
		/* Loop through all termins in this termintype (E.g. weekly) */
		for ( $i = 1; $i <= count( $this->terminNames[ $type ] ); $i++ )
		{
			/* Should we return the list in SELECT -> OPTION format? */
			if ( $listType == $this->LISTTYPE_SELECT )
			{
				/* Active means they want this selected */
				if ( $active == ($i + 1) ) { $selectText = ' selected'; }
				else { $selectText = ''; }

				$retVal .= '<option value="' . $i . '"' . $selectText . ' />' . $this->GetTerminItemName( $type, $i );
			}
		}
		
		return( $retVal );
	}

	/* List all termins between tsFrom and tsTo.
	 * type - Type of termins (= how long a termin lasts, like a week, month etc)
	 * tsFrom - Timestamp from
	 * tsTo - Tomestamp to
	 * 
	 * Returns: array[ counter ][ key ]
	 * key is:
	 * year - What year this termin is in
	 * terminitem - What termin it is in this year (number)
	 * termin - Termin number, compatible with AltInn format ddd
	 * terminname - Name of termin, e.g. "Januar - februar"
	 * */
	function GetTerminListRange( $type, $tsFrom, $tsTo )
	{
		$terminList = array();
		
		$terminItem = $this->GetTerminItemFromTime( $type, $tsFrom );
		$year = date("Y", $tsFrom);
		for (
			$tsCounter = $tsFrom, $totalCounter = 0;
			$tsCounter <= $tsTo;
			$tsCounter = strtotime($this->nextTerminArray[ $type ], $tsCounter), $terminItem++, $totalCounter++
			)
		{
			/* Check if we go into a new year: If so reset terminItem */
			$newYear = date("Y", $tsCounter);
			if ( $year != $newYear ) {
				$year = $newYear;
				$terminItem = 1;
			}
			$terminList[ $totalCounter ]['year'] = $year;
			$terminList[ $totalCounter ]['terminitem'] = $terminItem;
			$terminList[ $totalCounter ]['termin'] = $this->GetTerminItemNumber( $type, $terminItem );
			$terminList[ $totalCounter ]['terminname'] = $this->GetTerminItemName( $type, $terminItem );
		}
		
		return( $terminList );
	}

	/* Returns termin count at timestamp given.
	 * */
	function GetTerminItemFromTime( $type, $ts )
	{
		$year = date("Y", $ts);
		
		/* Loop through all termins that year to get to the one we're looking for.
		 * Begin at 1 January. */
		for (
			$tsCounter = mktime( 0,0,0,1,1,$year ), $terminItem = 1;
			$tsCounter < $ts;
			$tsCounter = strtotime($this->nextTerminArray[ $type ], $tsCounter), $terminItem++
			)
		{
			; // We're just looping, don't do anything
		}
		
		return( $terminItem );
	}

	/* Returns name of a given termin count */
	function GetTerminItemName( $type, $item )
	{
		return( $this->terminNames[ $type ][ ($item - 1) ] );
	}

	/* Returns valid AltInn termin number in ddd format */
	function GetTerminItemNumber( $type, $item )
	{
		return( sprintf("%02d%d", $item, $type ) );
	}

	/* Class constructor */
	function Mva()
	{
		$this->LISTTYPE_SELECT = 0;
		
		$this->TERMINTYPE_YEARLY = 1;
		$this->TERMINTYPE_QUARTERLY = 3;
		$this->TERMINTYPE_SECONDMONTH = 4;
		$this->TERMINTYPE_MONTHLY = 5;
		$this->TERMINTYPE_FORTHNIGHT = 6;
		$this->TERMINTYPE_WEEKLY = 7;

		/* Used with strtotime to jump to the next termin date */
		$this->NEXTTERMIN_YEARLY = "+1 year";
		$this->NEXTTERMIN_QUARTERLY = "+3 months";
		$this->NEXTTERMIN_SECONDMONTH = "+2 months";
		$this->NEXTTERMIN_MONTHLY = "+1 month";
		$this->NEXTTERMIN_FORTHNIGHT = "+2 weeks";
		$this->NEXTTERMIN_WEEKLY = "+1 week";

		/* Makes access to strtotime go next more easy */
		$this->nextTerminArray = array(
			"",
			$this->NEXTTERMIN_YEARLY,
			"",
			$this->NEXTTERMIN_QUARTERLY,
			$this->NEXTTERMIN_SECONDMONTH,
			$this->NEXTTERMIN_MONTHLY,
			$this->NEXTTERMIN_FORTHNIGHT,
			"",
			$this->NEXTTERMIN_WEEKLY
			);

		/* Do not change this values without changing customer's config in database */
		$this->termin = array(
			"&Aring;rlig" => $this->TERMINTYPE_YEARLY,
			"Kvartalsvis" => $this->TERMINTYPE_QUARTERLY,
			"Annenhver m&aring;ned" => $this->TERMINTYPE_SECONDMONTH,
			"M&aring;nedlig" => $this->TERMINTYPE_MONTHLY,
			"Hver 14. dag" => $this->TERMINTYPE_FORTHNIGHT,
			"Ukentlig" => $this->TERMINTYPE_WEEKLY
	 		);

	 	$this->terminNames = array(
	 		array(),
	 		array("&Aring;rlig"),
	 		array(),
	 		array("1.Kvartal (jan,feb,mar)",
				"2.Kvartal (apr,mai,juni)",
				"3.Kvartal (jul,aug,sept)",
				"4.Kvartal (okt,nov,des)"
				),
			array("Januar - februar",
				"Mars - april",
				"Mai - juni",
				"Juli - august",
				"September - oktober",
				"November - desember"
				),
			array("Januar",
				"Februar",
				"Mars",
				"April",
				"Mai",
				"Juni",
				"Juli",
				"August",
				"September",
				"Oktober",
				"November",
				"Desember"
				),
			array("1.Halvdel januar",
				"2.Halvdel januar",
				"1.Halvdel februar",
				"2.Halvdel februar",
				"1.Halvdel mars",
				"2.Halvdel mars",
				"1.Halvdel april",
				"2.Halvdel april",
				"1.Halvdel mai",
				"2.Halvdel mai",
				"1.Halvdel juni",
				"2.Halvdel juni",
				"1.Halvdel juli",
				"2.Halvdel juli",
				"1.Halvdel august",
				"2.Halvdel august",
				"1.Halvdel september",
				"2.Halvdel september",
				"1.Halvdel oktober",
				"2.Halvdel oktober",
				"1.Halvdel november",
				"2.Halvdel november",
				"1.Halvdel desember",
				"2.Halvdel desember"),
			array(),
			array("Uke 1",
				"Uke 2",
				"Uke 3",
				"Uke 4",
				"Uke 5",
				"Uke 6",
				"Uke 7",
				"Uke 8",
				"Uke 9",
				"Uke 10",
				"Uke 11",
				"Uke 12",
				"Uke 13",
				"Uke 14",
				"Uke 15",
				"Uke 16",
				"Uke 17",
				"Uke 18",
				"Uke 19",
				"Uke 20",
				"Uke 21",
				"Uke 22",
				"Uke 23",
				"Uke 24",
				"Uke 25",
				"Uke 26",
				"Uke 27",
				"Uke 28",
				"Uke 29",
				"Uke 30",
				"Uke 31",
				"Uke 32",
				"Uke 33",
				"Uke 34",
				"Uke 35",
				"Uke 36",
				"Uke 37",
				"Uke 38",
				"Uke 39",
				"Uke 40",
				"Uke 41",
				"Uke 42",
				"Uke 43",
				"Uke 44",
				"Uke 45",
				"Uke 46",
				"Uke 47",
				"Uke 48",
				"Uke 49",
				"Uke 50",
				"Uke 51",
				"Uke 52",
				"Uke 53"),
			);
	}
}
?>
