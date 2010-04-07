<?php
session_start();
$_REQUEST['LoginFormDate'] = date("Y-m-d", time());
$_REQUEST['DB_NAME_LOGIN'] = "GASTRO_VIN_AS";
$_REQUEST['username'] = "faktura@gastro.no";
$_REQUEST['password'] = "veldigvanskeligpassordsomingenkanhacke";
$_SESSION['login_id'] = 5;
$_SESSION['DB_NAME'] = $_REQUEST['DB_NAME_LOGIN'];
$_SETUP['ACTIVE_INTERFACE'] = "lodo";
// doesn't look like this is getting set further in..
#$_SETUP['HOME_DIR'] = "/var/www/html/www.lodo.no";
$_SETUP['HOME_DIR'] = getcwd();

require "gastropass.php";
if ($_REQUEST['brukernavn'] != "anders" || $_REQUEST['passord'] != $gastropass)
    die("Feil brukernavn og passord.");

$log_path = "/var/opt/tg/log"; #To be moved to preferences.

function includelogic($file) {
  global $_SETUP;
  list($module, $class) = explode('/', $file);
  require_once($_SETUP['HOME_DIR'] . "/modules/" . $module . "/model/" . $class . ".class.php");
}

function includeinc($inc) {
  global $_SETUP, $_date, $_lib, $_format, $_sess, $_dbh, $_dsn, $_dblodo;
//  if (is_file($_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php"))
    require_once($_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php");
//  else
//      print "Filen " . $_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php finnes ikke.";
}

function includealogic($inc) {
    global $_SETUP;
    $fil = $_SETUP['HOME_DIR'] . "/alogic/" . $inc . ".class.php";
    if (! is_file($fil))
        $fil = $_SETUP['HOME_DIR'] . "/code/lodo/lib/" . $inc . ".class";
    require_once($fil);
}


session_start();
$_action = $_REQUEST['action'];


require_once("../conf/default.inc");

$_prefs_file = "../conf/prefs_" . $_SESSION['DB_NAME'] . ".inc";
include($_prefs_file);

#To free up session lock faster all session handling must be before this
require_once($_SETUP['HOME_DIR'] . "/code/lib/session/session.class.php"); #M� v�re etter auth

if(!$_SESSION['lang'])
{
    $_SESSION['lang'] = $_SETUP['LANGUAGE'];
}

$_lib['sess'] = $_sess = & new SessionNew(array('database' => $_SESSION['DB_NAME'], 'company_id' => $_SETUP[COMPANY_ID], 'login_id' => $_SESSION['login_id'], 'interface' => $_SETUP['ACTIVE_INTERFACE'], 'module' => $args[0], 'template' => $args[1], 'LoginFormDate' => $_SESSION['LoginFormDate']));

require_once($_SETUP['HOME_DIR']."/code/lib/db/db_" . $_SETUP['DB_TYPE']['0'] . ".class.php");
$_dbh = array();
$_dsn = $_SETUP['DB_SERVER']['0'] . $_SESSION['DB_NAME'] . $_SETUP['DB_TYPE']['0'];

require_once($_SETUP['HOME_DIR'] . "/code/lib/convert/convert.class.php");
$_lib['convert']    = $_convert   = new convert(array('_dbh' => $_dbh, '_dsn' => $_dsn));

$_lib['storage'] = $_lib['db'] = $_dblodo = $_dbh[$_dsn] = & new db_mysql(array('host' => $_SETUP['DB_SERVER']['0'], 'database' => $_SESSION['DB_NAME'], 'username' => $_SETUP['DB_USER']['0'], 'password' => $_SETUP['DB_PASSWORD']['0'], '_sess' => $_sess));

require_once($_SETUP['HOME_DIR'] . "/code/lib/query/query.class.php");        #Saved queryes, to be replaced with web interface
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements.class.php");  #Auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements2.class.php"); #No auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements3.class.php"); #only hash parameters
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form.class.php");        #only hash parameters

require_once($_SETUP['HOME_DIR'] . "/code/lib/message/message.class.php");
$_lib['message']    = $_message   = & new message(array('dbserver'=> $_SETUP['DB_SERVER']['0'], 'dbname' => $_SESSION['DB_NAME']));
$_lib['message']->add(array('message' => $_REQUEST['message']));

require_once($_SETUP['HOME_DIR'] . "/code/lib/cache/cache.class.php");
$_lib['cache']      = $_cache      = & new Cache(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/date/date.class.php");
$_lib['date']       = $_date      = & new Date($_DF, $_NF);

require_once($_SETUP['HOME_DIR'] . "/code/lib/log/log.class.php");

require_once($_SETUP['HOME_DIR'] . "/code/lib/input/input.class.php");
$_lib['input']      = $_input      = & new Input();

require_once($_SETUP['HOME_DIR'] . "/code/lib/format/format.class.php");
$_lib['format']     = $_format    = & new format(array('_NF' => $_NF, '_DF' => $_DF, '_dbh' => $_dbh, '_dsn' => $_dsn));

require_once($_SETUP['HOME_DIR'] . "/code/lib/security/security.class.php");

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];


    $query = " select PersonID, FirstName, LastName, Email, LanguageID, Css FROM person WHERE Email='?' and Password=PASSWORD('?') and
trim(Password) <> ''";
    $_row = $_lib['db']->get_row2(array('query' => $query, 'values' => array($username, $password)));

    if (!$_row->PersonID)
    {
        $args['Message'] = "Email ($username) or password ($password) wrong: db: $_REQUEST[DB_NAME_LOGIN]<br>$query<br>";
        //print "Email ($username) or password ($password) wrong: db: $_REQUEST[DB_NAME_LOGIN]<br>$query<br>";
        accessdeniedpreinit(array('Module' => $args[0], 'Template' => $args[1]), $_template, $args);
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH']."code".$_SETUP['SLASH'].$_SETUP['INTERFACE'].$_SETUP['SLASH'].$_SETUP['FIRSTPAGE'].".php";
        $_lib['message']->add(array('message' => 'Password or Email Invalid'));
    }

    #Global information on logged in users
    $_SESSION['login_id'] = $_row->PersonID;
    $_sess->debug("Auth: $_row->PersonID");

    #Set default language for user
    if($_REQUEST['lang'])
    {
        $_SESSION['lang'] = $_REQUEST['lang'];
    }
    elseif($_row->LanguageID)
    {
        $_SESSION['lang'] = $_row->LanguageID;
    }
    else
    {
        $_SESSION['lang'] = $_SETUP['LANGUAGE']; #Get default from configuration file
    }

    #Set default css for user
    if($_row->Css)
    {
        $_SESSION['css'] = $_row->Css;
    }
    else
    {
        $_SESSION['css'] = $_SETUP['CSS']; #Get default from configuration file
    }

    #Set security control data fingerprint - prevents session hijacking
    $_SESSION['fingerprint']  = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_PROTOCOL'] . session_id());

    $_SESSION['LoginFormDate'] = $_REQUEST['LoginFormDate'];


require_once($_SETUP['HOME_DIR'] . "/code/lib/lang/language.class");
$_lib['lang']       = $_lang = new language(array('language' => $_SESSION['lang'], 'action' => $_action));

#Setup session object
$_sess->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_SESSION['login_id'],  language => $_SESSION['lang'])); #init dbh in session object after dbh is setup and verified working
$_sess->debug("PHP session_write_close()");
session_write_close();

#Read query again. Ugly. Should be query object
require($_SETUP['HOME_DIR'] . "/code/lib/query/query.class.php");
$_lib['security']   = $_security  = new security(array('_sess' => $_sess, '_template' => $_template));
$_lib['log']        = $_log       = new logg(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess, 'path' => $log_path, 'module' => $args[0], 'template' => $args[1]));
$_lib['form']       = $_form      = new form(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
$_lib['form2']      = $_form2     = new form2(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
$_lib['form3']      = $_form3     = new form3(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess, '_QUERY' => $_QUERY));

if($searchstring) {
  $_log->search($_sess, 'table', $searchstring);
}

// Sjekke om inputvariablene har gyldige verdier.
$myVar == "type";
if ($_REQUEST[$myVar] != "" && ($_REQUEST[$myVar] == "ny" || $_REQUEST[$myVar] == "innbetaling"  || $_REQUEST[$myVar] == "oppdaterinnbetaling" ))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
$myVar == "kundenr";
if ($_REQUEST[$myVar] != "" && is_int($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
$myVar == "fakturanr";
if ($_REQUEST[$myVar] != "" && is_int($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
$myVar == "innbetaltbelop";
if ($_REQUEST[$myVar] != "" && is_numeric($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
$myVar == "hovedbokskonto";
if ($_REQUEST[$myVar] != "" && is_int($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
/*
$myVar == "betaltdato";
if ($_REQUEST[$myVar] != "" && is_int($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);
$myVar == "fakturadato";
if ($_REQUEST[$myVar] != "" && is_int($_REQUEST[$myVar]))
    die("Error: Ikke gjenkjent variabelen " . $myVar);

*/

$file_source =  $_SETUP['HOME_DIR'] . "/gastrotest.txt";
$file_target = $file_source;
if (is_file($file_source))
        $str = file($file_source);
$wh = fopen($file_target, 'wb');
if ($rh===false || $wh===false) {
// error reading or opening file
// Do something stupid
$ok = false;
}
else
{
    if (is_file($file_source))
    for($i = 0; $i < count($str); $i++)
    {
        if (fwrite($wh, $str[$i]) === FALSE)
        {
            break;
        }
    }
    fwrite($wh, "\n\n");
    foreach ($_REQUEST as $key => $value)
    {
        if (fwrite($wh, $key . ": " . $value . "\n") === FALSE)
        {
            break;
        }
    }
    fclose($wh);
}

includelogic("accounting/accounting");
$accounting = new accounting();
includelogic("externalinvoice/invoice");

$invoice = new extinvoice();
if ($_REQUEST["type"] == "ny")
    $bilagsnr = $invoice->updateInvoice($_REQUEST);

// Feilmelding på innbetalinger
if ($_REQUEST["type"] == "innbetaling") {
  die("ERROR: Beklager, vi tar ikke imot innbetalingsbilag.");
}

// No error
print "OK";
if ($_REQUEST["visbilagsnr"] == 1)
    print "\nBilag nr: " . $bilagsnr;

?>
