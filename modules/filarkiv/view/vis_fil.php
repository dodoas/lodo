<?php

global $_dsn, $_SETUP, $_dbh;

$db_table1 = "filarkiv";
$id_ref = $db_table1 . "ID";

/* S�kestreng */
$select1 = "select * from " . $db_table1 . " where " . $id_ref . " = " . $_REQUEST[$id_ref] . ";";
$row= $_dbh[$_dsn]->get_row(array('query' => $select1));

header('Content-type: ' . $row->mimetype);
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $row->ts_modified) . " GMT");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header('Content-Disposition: attachment; filename="' . $row->original_name . '"');

print $row->fildata;


?>