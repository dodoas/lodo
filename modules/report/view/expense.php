<?
# $Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
header("Content-type: text/html; charset=utf-8");



require_once  "record.inc";

//print $query;
/*$query = "select * from company where CompanyID='$CompanyID'";
$result = $_dbh[$_dsn]->db_query($query);
$row = $_dbh[$_dsn]->db_fetch_object($result);*/

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Expense</title>
    <meta name="cvs"    content="$Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<? include($_SETUP['HOME_DIR'] . "/modules/varelager/view/list.php");?>
<hr>
<? include($_SETUP['HOME_DIR'] . "/modules/report/view/varelagerlist.php");?>
<? includeinc('top') ?>
<? includeinc('left') ?>
</body>
</html>
