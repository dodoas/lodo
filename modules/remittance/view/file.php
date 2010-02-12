<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$_lib['sess']->debug = false;
includelogic('remittance/remittance');

$rem    = new logic_remittance_remittance($_lib['input']->request);
$rem->fill(array());

header('Content-type: text');
header("Last-Modified: " . gmdate("D, d M Y H:i:s", $row->ts_modified) . " GMT");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
#header('Content-Disposition: attachment; filename="remittering.txt"');
header('Content-Disposition: inline; filename="remittering.txt"');

print $rem->pay(array());
?>