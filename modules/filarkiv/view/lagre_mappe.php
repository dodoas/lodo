<?
// unformatNumbers();
if($_REQUEST['action_mappe_new'])
{
    $_POST['filkategori_ts_created'] = time();
    $_POST['filkategori_ts_modified'] = time();
    $_POST['filkategori_modified_by'] = $_lib['sess']->get_person('FirstName');
    if ($_lib['sess']->get_person('MiddleName') != "")
    	$_POST['filkategori_modified_by'] .= $_lib['sess']->get_person('MiddleName');
    $_POST['filkategori_modified_by'] .= $_lib['sess']->get_person('LastName');

    $$id_ref = $_dbh[$_dsn]->db_new_hash($_POST, $db_table1);
?>
<SCRIPT LANGUAGE="JavaScript1.1">
<!--
window.location="<? print str_replace("&amp;", "&", $_SETUP['DISPATCH']); ?>t=filarkiv.index";
-->
</script>
<?php
}
elseif($_REQUEST['action_mappe_update'])
{
	$$id_ref = $_POST['filkategori_filkategoriID'];
    $_POST['filkategori_ts_modified'] = time();
    $_POST['filkategori_modified_by'] = $_lib['sess']->get_person('FirstName');
    if ($_lib['sess']->get_person('MiddleName') != "")
    	$_POST['filkategori_modified_by'] .= $_lib['sess']->get_person('MiddleName');
    $_POST['filkategori_modified_by'] .= $_lib['sess']->get_person('LastName');

	$primarykey['filkategoriID'] = $_POST['filkategori_filkategoriID'];

	  if ($_POST['filkategori_filkategoriID'] == "")
		$$id_ref = $_dbh[$_dsn]->db_new_hash($_POST, $db_table1);
	  else
		$_dbh[$_dsn]->db_update_hash($_POST, $db_table1, $primarykey);
?>
<SCRIPT LANGUAGE="JavaScript1.1">
<!--
window.location="<? print str_replace("&amp;", "&", $_SETUP['DISPATCH']); ?>t=filarkiv.index";
-->
</script>
<?php
}
elseif ($_REQUEST['action_mappe_delete'])
{
	//print "action_borettslag_delete";
	/************************************************************************/
    /* Delete info in own database                                          */
    /************************************************************************/
   	if ($_REQUEST['filkategoriID'] != "")
		$query = "DELETE FROM filkategori WHERE filkategoriID = '" . $_REQUEST['filkategoriID'] . "';";
	else
		$query = "DELETE FROM filkategori WHERE filkategoriID = '" . $_REQUEST['filkategori_filkategoriID'] . "';";
    $result = $_dbh[$_dsn]->db_query($query);
   	if ($_REQUEST['filkategoriID'] != "")
		$query = "DELETE FROM filarkiv WHERE filkategoriID = '" . $_REQUEST['filkategoriID'] . "';";
	else
		$query = "DELETE FROM filarkiv WHERE filkategoriID = '" . $_REQUEST['filkategori_filkategoriID'] . "';";
    $result = $_dbh[$_dsn]->db_query($query);
    ?>
<SCRIPT LANGUAGE="JavaScript1.1">
<!--
window.location="<? print str_replace("&amp;", "&", $_SETUP['DISPATCH']); ?>t=filarkiv.index";
-->
</script>
    <?php
}

/*
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
*/
?>
