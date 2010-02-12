<?
# $Id: db_odbc.inc,v 1.5 2005/10/14 13:15:40 thomasek Exp $ db_odbc.inc,v 1.1.1.1 2001/11/08 18:14:05 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no

/* 
Database abstraction layer: MySQL
By: Thomas Ekdahl 2000-12-01
*/

function db_connect($db_host, $db_user, $db_password) {
	return odbc_connect($db_host, "$db_user", "$db_password") or die("You are not authorized to login to this database");	
}

function db_pconnect($db_host, $db_user, $db_password) {
	return odbc_pconnect($db_host, "$db_user", "$db_password") or die("You are not authorized to login to this database");	
}

function $_lib['db']->db_query($db_query) {
	global DEBUG;
	if(DEBUG) { print "DEBUG: $db_query<br>"; } 
	$db_query = odbc_prepare($db_query);
	$result = odbc_execute($_SESSION['DB_NAME'], $db_query) or die("Bad query: " . mysql_error() . "<br />$db_query");
	return $result;
}

function $_lib['db']->db_numrows($db_result) {
	return odbc_num_fields($db_result) or die("Bad numrows: " . mysql_error() . "<br />");
}

function $_lib['db']->db_fetch_object($db_result) {
	/* MŒ kanskje konverteres til objekt? */
	$object = odbc_fetch_row($db_result);
	return $object;
}

function db_insert_id($db_result) {
	/* This does not exist in ODBC */
	return mysql_insert_id() or die("Bad insert id: " . mysql_error() . "<br />");
}

function db_close($db_link) {
	return odbc_close($db_link) or die("Bad close: " . mysql_error() . "<br />");
}

function db_result($db_result, $db_row, $db_mixed) {
	return odbc_result($db_result, $db_row, $db_mixed) or die("Bad result: " . mysql_error() . "<br />");
}

function db_free_result($db_result) {
	return odbc_free_result($db_result) or die("Bad free result: " . mysql_error() . "<br />");
}
?>