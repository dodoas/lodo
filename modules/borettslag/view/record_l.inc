<?
# $Id: record.inc,v 1.15 2004/11/15 15:06:13 svenn Exp $ company_record.inc,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no

if($_REQUEST['action_leilighet_new'])
{
	//print "action_leilighet_new";
	print_r ($_POST);
    $_POST['CustomerResponsibleID'] = $_lib['sess']->get_person('PersonID');
    $_POST['SalesmanID']            = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedByID']           = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedDate']           = "NOW()";

    $BorettslagID = $_dbh[$_dsn]->db_new_hash($_POST, $db_table);
}
elseif($_REQUEST['action_leilighet_update'])
{
	//print "action_leilighet_update";
	//print_r ($_POST);
      $primarykey['LeilighetID'] = $_POST['leilighet_LeilighetID'];
      #Possible to extend or alter parameters here
	  if ($_POST['leilighet_LeilighetID'] == "")
		$BorettslagID = $_dbh[$_dsn]->db_new_hash($_POST, $db_table);
	  else
		$_dbh[$_dsn]->db_update_hash($_POST, $db_table, $primarykey);
}
elseif ($_REQUEST['action_leilighet_delete'])
{
	//print "action_leilighet_delete";
	/************************************************************************/
    /* Delete info in own database                                          */
    /************************************************************************/
    $query = "DELETE FROM $db_table WHERE LeilighetID='" . $_POST["leilighet_LeilighetID"] . "'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);

}
?>












