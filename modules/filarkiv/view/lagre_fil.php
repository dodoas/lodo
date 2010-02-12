<?
// unformatNumbers();
function unformatMyDate($date_time)
{
	list($date_str, $time_str) = split(" ", $date_time);
	list($date_d, $date_m, $date_y) = split("\.", $date_str);
	list($time_h, $time_m, $time_s) = split(":", $time_str);
	return mktime($time_h, $time_m, $time_s, $date_m, $date_d, $date_y);
}
if($_REQUEST['action_fil_new'])
{
    $_POST['filarkiv_tilgjengeligFra'] = unformatMyDate($_POST['filarkiv_tilgjengeligFra']);
    $_POST['filarkiv_tilgjengeligTil'] = unformatMyDate($_POST['filarkiv_tilgjengeligTil']);
    $_POST['filarkiv_ts_created'] = mktime();
    $_POST['filarkiv_ts_modified'] = mktime();
    $_POST['filarkiv_modified_by'] = $_lib['sess']->get_person('FirstName');
    if ($_lib['sess']->get_person('MiddleName') != "")
    	$_POST['filarkiv_modified_by'] .= $_lib['sess']->get_person('MiddleName');
    $_POST['filarkiv_modified_by'] .= $_lib['sess']->get_person('LastName');
	if ($_FILES["fildata"]["name"] != "")
	{
		$handle = fopen($_FILES['fildata']['tmp_name'], "rb");
		$_POST['filarkiv_fildata'] = fread($handle, filesize($_FILES['fildata']['tmp_name']));
		fclose($handle);
		$_POST['filarkiv_mimetype'] = $_FILES['fildata']['type'];
		$_POST['filarkiv_original_name'] = $_FILES['fildata']['name'];
		$_POST['filarkiv_size'] = $_FILES['fildata']['size'];
	}

    $_REQUEST[$id_ref] = $_dbh[$_dsn]->db_new_hash($_POST, $db_table1);
?>
<SCRIPT LANGUAGE="JavaScript1.1">
<!--

window.location="<? print str_replace("&amp;", "&", $_SETUP['DISPATCH']); ?>t=filarkiv.index";
-->
</script>
<?php
}
elseif($_REQUEST['action_fil_update'])
{
    $_POST['filarkiv_tilgjengeligFra'] = unformatMyDate($_POST['filarkiv_tilgjengeligFra']);
    $_POST['filarkiv_tilgjengeligTil'] = unformatMyDate($_POST['filarkiv_tilgjengeligTil']);
	$_REQUEST[$id_ref] = $_POST['filarkiv_filarkivID'];
    $_POST['filarkiv_ts_modified'] = mktime();
    $_POST['filarkiv_modified_by'] = $_lib['sess']->get_person('FirstName');
    if ($_lib['sess']->get_person('MiddleName') != "")
    	$_POST['filarkiv_modified_by'] .= $_lib['sess']->get_person('MiddleName');
    $_POST['filarkiv_modified_by'] .= $_lib['sess']->get_person('LastName');
	$primarykey['filarkivID'] = $_POST['filarkiv_filarkivID'];
	if ($_FILES["fildata"]["name"] != "")
	{
		$handle = fopen($_FILES['fildata']['tmp_name'], "rb");
		$_POST['filarkiv_fildata'] = fread($handle, filesize($_FILES['fildata']['tmp_name']));
		fclose($handle);
		$_POST['filarkiv_mimetype'] = $_FILES['fildata']['type'];
		$_POST['filarkiv_original_name'] = $_FILES['fildata']['name'];
		$_POST['filarkiv_size'] = $_FILES['fildata']['size'];
	}
	  if ($_POST['filarkiv_filarkivID'] == "")
		$_REQUEST[$id_ref] = $_dbh[$_dsn]->db_new_hash($_POST, $db_table1);
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
elseif ($_REQUEST['action_fil_delete'])
{
	/************************************************************************/
    /* Delete info in own database                                          */
    /************************************************************************/
	if ($_REQUEST["filarkivID"] != "")
 		$query = "DELETE FROM filarkiv WHERE filarkivID='" . $_REQUEST["filarkivID"] . "';";
	else
		$query = "DELETE FROM filarkiv WHERE filarkivID='" . $_REQUEST["filarkiv_filarkivID"] . "';";
	print $query;
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












