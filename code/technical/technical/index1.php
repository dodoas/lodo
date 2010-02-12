<?
# $Id: index1.php,v 1.4 2005/10/28 17:59:41 thomasek Exp $ index1.php,v 1.1.1.1 2001/11/08 18:13:55 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

if($_POST['interf']) {
  #Set interface if present
  $interface = $_POST['interf'];
} else {
  $interface = $_SETUP[INTERFACE];
}

#$to_page = $_lib['sess']->dispatch . "t=intranett.index2&interf=" . $_SESSION['interface'];

$interface = $_sess->get_interface();
$size = count($interface);
#exit;
#if($size > 1) {
$to_page = $_lib['sess']->dispatch . "t=lib.frame&interf=" . $_SETUP[LOGIN_INTERFACE];
#} else {
#js problems because of frame difference
#  $to_page = $_lib['sess']->dispatch . "t=$interface[0].index2&interf=" . $_SESSION['interface'];
#}
#print "Location: $_SETUP[SERVER_ADMIN]$to_page&redirected=$_REQUEST[redirected]";
#exit;
header("Location: . $_SETUP[SERVER_ADMIN]$to_page&redirected=$_REQUEST[redirected]");
exit;
?>
