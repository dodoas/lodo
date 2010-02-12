<?php
require_once 'inc_database.php';
require_once 'inc_lodo.php';

class TimerReport
{
	var $timeFrom;
	var $timeTo;
	
	var $reportTimeUnit;
	var $reportRow;
	var $reportUnit;

	var $reportCriteriaCustomers;		// Array: array(0) means all
	var $reportCriteriaProjectTypes;	// Array: array(0) means all
	
	var $TIMEUNIT_DAY;
	var $TIMEUNIT_WEEK;
	var $TIMEUNIT_MONTH;

	var $ROW_CUSTOMER;
	var $ROW_PROJECTTYPE;
	
	var $UNIT_HOURS;
	var $UNIT_MONEY;
	
	var $output;
	var $db;
	var $lodo;
	
	function TimerReport( $db, $lodo, $output )
	{
		
	}
}
?>
