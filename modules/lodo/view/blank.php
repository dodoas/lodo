<?
/* $Id: blank.php,v 1.11 2005/06/24 10:39:12 thomasek Exp $ blank.php,v 1.1.1.1 2001/11/08 18:13:55 thomasek Exp $ */
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no, http://www.ekdahl.no

session_register("count", "login_username", "login_password", "db_name", "login_timeout", "login_privileges", "login_ip", "login_email", "login_surename", "login_name");

require_once  "../code/lib/setup/prefs_" . $_SESSION['DB_NAME'] . ".inc";
require_once  "$_SETUP[HOME_DIR]/code/lib/db/db_" . $_SETUP[DB_TYPE][0] . ".inc";

if(!$bgcolor){
  $bgcolor = "EFEAD6";
}
 ?>


<? print $_lib['sess']->doctype ?>
<head>
	<title>Empatix - customer</title>
	<meta name="cvs"     		    content="$Id: blank.php,v 1.11 2005/06/24 10:39:12 thomasek Exp $" />
	<? includeinc('head') ?>
</head>

<body>

</body>
</html>
