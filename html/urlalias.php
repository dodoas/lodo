<?
# $Id: urlalias.php,v 1.20 2005/10/14 13:15:43 thomasek Exp $

#Parse redirect URL

#$array_path = (array) explode('/', substr($_SERVER['REDIRECT_URI'], 1));
$array_path = (array) explode('/', substr($_SERVER['REDIRECT_URL'], 1));
#Array element 1 = module / or main function like news (required
#Array element 2 = func id to find info more spesific (not required)
#Example1: http://localhost/news/
#Example2: http://localhost/news/12/

#Initialize assertions
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_WARNING, 1);
#assert_options(ASSERT_CALLBACK, "dispaly_error");

#The sessions should be rewritten to store a persistent object like: 
#This will greatly enhance the speed and be simpler to use and debug.
#Secure variables used in includes could be malicious data from the outside.
require_once "/kunder/empatix/empatix1/conf/default.inc";
require_once "/kunder/empatix/empatix1/conf/prefs_$_SETUP[DB_NAME_DEFAULT].inc";
require_once "$_SETUP[HOME_DIR]/framework/lib/db/db_" . $_SETUP[DB_TYPE][0] . ".class.php";

$_dsn = "$DB_SERVER[0]$DB_NAME[0]$DB_TYPE[0] ";
$_dbh[$_dsn] = new db_mysql(array('host' => $_SETUP[DB_SERVER][0], 'database' => $_SETUP[DB_NAME][0], 'username' => $_SETUP[DB_USER][0], 'password' => $_SETUP[DB_PASSWORD][0]));
require_once($_SETUP['HOME_DIR'] . "/framework/lib/cache/cache.class.php");
$_cache      = new Cache(array());
require_once $_SETUP['HOME_DIR'] . "/framework/lib/log/log.class.php";
require_once "$_SETUP[HOME_DIR]/framework/lib/auth/web.class.php";
require_once "$_SETUP[HOME_DIR]/framework/lib/session/session.class.php";
$_sess      = new SessionNew(array('database' => $_SESSION['DB_NAME'], 'company_id' => $_SETUP[COMPANY_ID], 'login_id' => $_SESSION['login_id'], 'interface' => $_SESSION['interface'], 'module' => $args[0], 'template' => $args[1]));

require_once "$_SETUP[HOME_DIR]/framework/lib/date/date.class.php";
$_date 		= new Date($_DF, $_NF);
require_once "$_SETUP[HOME_DIR]/framework/lib/convert/convert.class.php";
$_convert 	= new convert(array('_dbh' => $_dbh, '_dsn' => $_dsn));
require_once "$_SETUP[HOME_DIR]/framework/lib/format/format.class.php";
$_format 	= new format(array('_NF' => $_NF, '_DF' => $_DF));
require_once "$_SETUP[HOME_DIR]/framework/lib/message/message.inc";
$_message 	= new message(array('dbserver'=> $_SETUP['DB_SERVER']['0'], 'dbname' => $_SESSION['DB_NAME']));
$_sess->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_SESSION['login_id'], 	'LoginFormDate' => $_SESSION['LoginFormDate'], language => $_SESSION['lang'])); #init dbh in session object after dbh is setup and verified working
require_once "$_SETUP[HOME_DIR]/framework/lib/security/security.class.php";

#print $_SERVER['REDIRECT_URI'] . "<br>";

$_SESSION['interface']  = $_SETUP['ACTIVE_INTERFACE'];
$_sess->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_SESSION['login_id'], 	'LoginFormDate' => $_SESSION['LoginFormDate'], language => $_SESSION['lang'])); #init dbh in session object after dbh is setup and verified working
$_security  = new security(array('_sess' => $_sess, '_template' => $_template));
$_log  		= new logg(array('_dbh' => $_dbh, '_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
#$_form		= new form(array('_dbh' => $_dbh, '_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
#$_form2		= new form2(array('_dbh' => $_dbh, '_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));

#Lookup URLALIAS
$path = $array_path[0];
if($array_path[1]) {
  $path .= "/" . $array_path[1];
}
if($array_path[2]) {
  $path .= "/" . $array_path[2];
}

$query_alias    = "select * from urlalias where Alias = '$path'";
#print "$query_alias<br>\n";
#Note takes only first element in path, should use all?

$result_alias   = $_lib['db']->db_query($query_alias);
$alias 			= $_lib['db']->db_fetch_object($result_alias);

#If the URL is internal we shoudl bulid the dispatch routine and run it here.
#For now we just redirect

if($alias->Url) {
  header("Location: $alias->Url&redirected=$_REQUEST[redirected]");
  exit;
} else {
  #Best practise, see if such a module exists
  $query_template	= "select * from roletemplate where Module = '$array_path[0]' and Interface='$_SETUP[INTERFACE]'";
  $result_template	= $_lib['db']->db_query($query_template);
  $template 		= $_lib['db']->db_fetch_object($result_template);
  
  if($_template->Module) {
    header("Location: $_SETUP[SERVER_ADMIN]$_SETUP[DISPATCHR]" . "t=$template->Module.list&redirected=$_REQUEST[redirected]");
    exit;
  } else {
    $_log->pagenotfound($_sess);
    print "Error: Page not found";
  }
}
?>
