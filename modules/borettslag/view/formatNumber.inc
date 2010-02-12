<?
# $Id: record.inc,v 1.15 2004/11/15 15:06:13 svenn Exp $ company_record.inc,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no

unformatNumbers();
function unformatNumbers()
{
	// Tar bare POST verdier!
	global $_POST, $_dbh, $_dsn;
	foreach ($_POST as $key => $value)
	{
		list($tablename, $attrib) = split("_", $key, 2);
		$query = "select * from confdbfields where TableName='" . $tablename . "' and TableField='" . $attrib . "'";
		$myRow= $_dbh[$_dsn]->get_row(array('query' => $query));
		if ($myRow->InputValidation == "Amount")
		{
			$_POST[$key] = str_replace(",", ".", str_replace(" ", "", $value));
			// print "Unformaterer: " . $key . " Resultat: --" . $_POST[$key] . "--<br>";
		}	
		if ($myRow->InputValidation == "Int")
		{
			$_POST[$key] = str_replace(",", ".", str_replace(" ", "", $value));
			// print "Unformaterer: " . $key . " Resultat: --" . $_POST[$key] . "--<br>";
		}	
	}
}
function formatNumber($var)
{
	return number_format($var, 2, ',', ' ');
}
