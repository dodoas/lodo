<?
# $Id: login.php,v 1.11 2005/11/18 07:35:46 thomasek Exp $ index1.php,v 1.1.1.1 2001/11/08 18:13:55 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$_SETUP['DISPATCHR'] = "lodo.php?";
$to_page = "$_SETUP[DISPATCHR]" . "t=" . $_SETUP['LOGIN_FIRSTPAGE'] . "&interf=" . $_SESSION['interface'];

if($_SERVER['SERVER_PORT'] == 443)
{
    header ("Location: ".$_SETUP['SERVER_ADMIN_SSL']."$to_page&redirected=".$_REQUEST['redirected']);
}
else
{
    header ("Location: ".$_SETUP['SERVER_ADMIN']."$to_page&redirected=".$_REQUEST['redirected']);
}
exit;
?>
