<?
# $Id: record.inc,v 1.15 2004/11/15 15:06:13 svenn Exp $ company_record.inc,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no

unformatNumbers();

if($_REQUEST['action_aarsoppgjor_new'])
{
	//print "action_borettslag_new";
    $_POST['CustomerResponsibleID'] = $_lib['sess']->get_person('PersonID');
    $_POST['SalesmanID']            = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedByID']           = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedDate']           = "NOW()";

    $_REQUEST['borettslagarsoppgjor_BorettslagarsoppgjorID'] = $_dbh[$_dsn]->db_new_hash($_POST, $db_table2);
}
elseif($_REQUEST['action_aarsoppgjor_update'])
{
	//print "action_borettslag_update";
	//print_r ($_POST);
      $primarykey['BorettslagarsoppgjorID'] = $_POST['borettslagarsoppgjor_BorettslagarsoppgjorID'];
      #Possible to extend or alter parameters here
	  if ($_POST['borettslagarsoppgjor_BorettslagarsoppgjorID'] == "")
		$_REQUEST['borettslagarsoppgjor_BorettslagarsoppgjorID'] = $_dbh[$_dsn]->db_new_hash($_POST, $db_table2);
	  else
		$_dbh[$_dsn]->db_update_hash($_POST, $db_table2, $primarykey);
}
elseif ($_REQUEST['action_aarsoppgjor_update'])
{
	//print "action_borettslag_delete";
	/************************************************************************/
    /* Delete info in own database                                          */
    /************************************************************************/
    $query = "DELETE FROM borettslagarsoppgjor WHERE BorettslagarsoppgjorID='$BorettslagarsoppgjorID'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);
}
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
			$_POST[$key] = str_replace(",", "", str_replace(" ", "", $value));
			// print "Unformaterer: " . $key . " Resultat: --" . $_POST[$key] . "--<br>";
		}	
	}
}
function formatNumber($var)
{
	return number_format($var, 2, ',', ' ');
}

?>












