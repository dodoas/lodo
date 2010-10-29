<?
#dl("/usr/lib/php/modules/mysqli.so");

#print "#$_SETUP[ACTIVE_INTERFACE]#<br>";
# $Id: index.inc,v 1.74 2005/11/18 07:35:46 thomasek Exp $
#print "t=$_REQUEST[t], include: $include<br>";

#From PHP mag december 2004.
#header("Expires: Wed, 24 Dec 2003 05:00:00 GMT");
#header("Expires: 0"); #Better
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
#header("Cache-Control: no-cache, must-revalidate"); Impossible to press  back in browser
header("Cache-Control: private"); #Impossible to press  back in browser
#header("Pragma: no-cache");

#Initialize assertions (this could be better)
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_BAIL, 1);
assert_options(ASSERT_WARNING, 1);
#assert_options(ASSERT_CALLBACK, "dispaly_error");

function includelogic($class) {
    includemodel($class);
}

function includemodel($file) {
    global $_SETUP;
    list($module, $class) = explode('/', $file);
    require_once($_SETUP['HOME_DIR'] . "/modules/" . $module . "/model/" . $class . ".class.php");
}

function includeinc($inc) {
    #Deprecated
    global $_SETUP, $_lib, $_dbh, $_dsn, $_dblodo;
//  if (is_file($_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php"))
    require_once($_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php");
//  else
//      print "Filen " . $_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php finnes ikke.";
}

function includealogic($inc) {
    global $_SETUP;
    $fil = $_SETUP['HOME_DIR'] . "/alogic/" . $inc . ".class.php";
    if (!is_file($fil))
        $fil = $_SETUP['HOME_DIR'] . "/modules/altinn/model/" . $inc . ".class";
    #print "$fil<br>\n";
    require_once($fil);
}

if(0)
{
    #print_r($_REQUEST);
    print "\n<br />t:".$_REQUEST['t']."<br />";
    print "\n<br />interface: ".$_SETUP['ACTIVE_INTERFACE']."<br />";
}

############################
# check if database exists before starting session.
# if not database exists, use default database name
if(strlen($_REQUEST['submit_login'])>0 and $_SETUP['ACTIVE_INTERFACE']=='lodo')
{
    $_prefs_file = "../conf/default.inc";
    include($_prefs_file);

    #Mysqli version
    $db_link = mysqli_connect($_SETUP['DB_SERVER_DEFAULT'], $_SETUP['DB_USER_DEFAULT'], $_SETUP['DB_PASSWORD_DEFAULT'], $_SETUP['DB_NAME_DEFAULT']) or die("Connection refused by <b></B> : ".mysqli_connect_error($db_link));
    $exists  = mysqli_select_db($db_link, $_REQUEST['DB_NAME_LOGIN']);

    if($exists != 1)
    {
        #MySQL
        $exists = mysqli_select_db($db_link, $_REQUEST['DB_NAME_LOGIN']);
        if($exists != 1)
        {
            unset($_REQUEST['DB_NAME_LOGIN']);
            session_write_close();
            header("location: https://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . "index.php?message=Kunden eksisterer ikke");
            exit;
        }
        else
        {
            $_REQUEST['DB_NAME_LOGIN'] = $_SETUP['DB_NAME_DEFAULT'];
        }
    }
}
#print "<!-- ".$_REQUEST['DB_NAME_LOGIN']." -->";

####################
# The sessions should be rewritten to store a persistent object like:
# This will greatly enhance the speed and be simpler to use and debug.
# Secure variables used in includes could be malicious data from the outside.
# session_set_cookie_params($_SETUP[SECURITY][SESSIONTIMEOUT], empatix, $_SERVER['HTTP_HOST']);

session_start();
if(isset($_REQUEST['LoginFormDate']))  $_SESSION['LoginFormDate'] = $_REQUEST['LoginFormDate'];
$_action = $_REQUEST['action'];

if($_REQUEST['redirected'] > 2)
{
    print "Catched a link loop<br>";
    print "referer. "   . $_SERVER['HTTP_REFERER'] . "<br>";
    print "requested: " . $_SERVER['REQUEST_URI']  . "<br>";
    $_REQUEST['t'] = "";
    $_REQUEST['message'] .= "Siden finnes ikke - link loop<br>";
    //exit;
}
else
{
    $_REQUEST['redirected'] += 1;
}

if(!$include)
{
    #If we are runnign dispatch through a require_once( these are already included
    require_once("../conf/default.inc");
    #Init the database asked for
    #hvorfor var dette avsnittet kommentert bort? $_SESSION['DB_NAME'] ble ikke satt noe sted, den må jo bli satt til valgt database?
    #---------- START -------------#
    if(isset($_REQUEST['DB_NAME_LOGIN']))
    {
        #session_regenerate_id();
        $_SESSION['DB_NAME'] = $_REQUEST['DB_NAME_LOGIN'];
    }
    elseif(!isset($_SESSION['DB_NAME']))
    {
        #Should we validate this?
        $_SESSION['DB_NAME'] = $_SETUP['DB_NAME_DEFAULT'];
    }
    #---------- STOPP -------------#

    #should we check that this exist?
    $_prefs_file = "../conf/prefs_" . $_SESSION['DB_NAME'] . ".inc";
    include($_prefs_file);

    #To free up session lock faster all session handling must be before this
    require_once($_SETUP['HOME_DIR'] . "/code/lib/session/session.class.php"); #MŒ v¾re etter auth

    #Get the page asked for
    if($_REQUEST['a'] == 'web' or $_REQUEST['a'] == 'htaccess')
    {
        $_a = $_REQUEST['a']; #Access method
    }

    if($_REQUEST['t'])
    {
        $t = $_REQUEST['t'];
    }
    else
    {
        #First page, we have to reset t
        //$_SESSION['DB_NAME'] = $_SETUP['DB_NAME_DEFAULT'];
    }

    $args = split('\.', $t);
    if(strlen($_REQUEST['submit_login']) > 0)
    {
        if(!isset($args[0]) or !strlen($args[0]) or !isset($args[1]) or !strlen($args[1]))
        {
            $_SETUP['ACTIVE_INTERFACE'] = $_SETUP['LOGIN_INTERFACE'];
            $args = split('\.', $_SETUP['LOGIN_FIRSTPAGE']);
        }
    }
    else
    {
        if(!isset($args[0]) or !strlen($args[0]))
        {
            $args[0] = $_SETUP['INTERFACE'];
        }
        if(!isset($args[1]) or !strlen($args[1]))
        {
            $args[1] = $_SETUP['FIRSTPAGE'];
        }
    }

    #Session handling
    if(!$_SESSION['lang'])
    {
        $_SESSION['lang'] = $_SETUP['LANGUAGE'];
    }

    $_lib['sess'] = new SessionNew(array('database' => $_SESSION['DB_NAME'], 'company_id' => $_SETUP[COMPANY_ID], 'login_id' => $_SESSION['login_id'], 'interface' => $_SETUP['ACTIVE_INTERFACE'], 'module' => $args[0], 'template' => $args[1], 'LoginFormDate' => $_SESSION['LoginFormDate']));
}

//print $_SETUP['ACTIVE_INTERFACE'];
#print "Her og<br>";
require_once($_SETUP['HOME_DIR']."/code/lib/db/db_" . $_SETUP['DB_TYPE']['0'] . ".class.php");
$_dbh = array();
$_dsn = $_SETUP['DB_SERVER']['0'] . $_SESSION['DB_NAME'] . $_SETUP['DB_TYPE']['0'];

require_once($_SETUP['HOME_DIR'] . "/code/lib/convert/convert.class.php");
$_lib['convert']    = new convert(array('_dbh' => $_dbh, '_dsn' => $_dsn));

$_lib['storage'] = $_lib['db'] = $_dbh[$_dsn] = new db_mysql(array('host' => $_SETUP['DB_SERVER']['0'], 'database' => $_SESSION['DB_NAME'], 'username' => $_SETUP['DB_USER']['0'], 'password' => $_SETUP['DB_PASSWORD']['0'], '_sess' => $_sess));

require_once($_SETUP['HOME_DIR'] . "/code/lib/query/query.class.php");         #Saved queryes, to be replaced with web interface
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements.class.php");  #Auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements2.class.php"); #No auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements3.class.php"); #only hash parameters
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form.class.php");         #only hash parameters
#require_once($_SETUP['HOME_DIR'] . "/code/lib/gui/list_procedures_2.3.inc");

require_once($_SETUP['HOME_DIR'] . "/code/lib/message/message.class.php");
$_lib['message']    = new message(array('dbserver'=> $_SETUP['DB_SERVER']['0'], 'dbname' => $_SESSION['DB_NAME']));
$_lib['message']->add(array('message' => $_REQUEST['message']));

require_once($_SETUP['HOME_DIR'] . "/code/lib/cache/cache.class.php");
$_lib['cache']      = new Cache(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/date/date.class.php");
$_lib['date']       = new Date($_DF, $_NF);

require_once($_SETUP['HOME_DIR'] . "/code/lib/log/log.class.php");

require_once($_SETUP['HOME_DIR'] . "/code/lib/input/input.class.php");
$_lib['input']      = new Input();

require_once($_SETUP['HOME_DIR'] . "/code/lib/format/format.class.php");
$_lib['format']     = new format(array('_NF' => $_NF, '_DF' => $_DF, '_dbh' => $_dbh, '_dsn' => $_dsn));

require_once($_SETUP['HOME_DIR'] . "/code/lib/setup/setup.class.php");   #only hash parameters
$_lib['setup']     = new framework_lib_setup(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/security/security.class.php");

#
# TODO: HVOR BÃ˜R DENNE LIGGE - martin 
#
require_once($_SETUP['HOME_DIR'] . "/modules/timesheets/model/login.php");

#Dynamic language negotiation
#if(!$lang) {
#  $pri_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2);
#  if($pri_language == 'no' || $pri_language == 'se' || $pri_language == 'dk') {
#    $lang = 'no';
#   } else { $lang = 'en';}

#If username, password and db is spesified the user tries to login
#If login_id is spesified the user has logged in (how to be sure this is not tampered with)

$query = "select * from roletemplate where Interface = '".$_SETUP['ACTIVE_INTERFACE']."' and Module = '".$args['0']."' and Template = '".$args['1']."'";
//print "$query<br>";
$_template = $_lib['db']->get_row(array('query' => $query));

$auth = $_template->AuthType;

if(!$_template->AuthType)
{
    //print "DB_NAME: $_SETUP[DB_NAME_DEFAULT], query: $query<br>";
    $auth = "web"; #Pga bad session handling
}
if($_a)
{
    $auth = $_a; # Override default athentivation mechanism
}

require_once($_SETUP['HOME_DIR']."/code/lib/auth/$auth.class.php");

#print "Her og3<br>";
#require_once( "../code/lib/gui/gui.inc"; #Problem beacuse it added headers - has to be solved - not possible to add timelist entries bacause of this
#print "lang. #" . $_SESSION['lang'] . "# loginid: # " . $_SESSION['login_id'] . "#<br>\n";
require_once($_SETUP['HOME_DIR'] . "/code/lib/lang/language.class");
$_lib['lang']       = $_lang = new language(array('language' => $_SESSION['lang'], 'action' => $_action));

#require_once( "class.php";
#Setup session object
$_lib['sess']->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_SESSION['login_id'],  language => $_SESSION['lang'])); #init dbh in session object after dbh is setup and verified working
$_lib['sess']->debug("PHP session_write_close()");
session_write_close();
#print "session_write_close()<br>";
#print "<br>SID:" . session_id() . "<br>";
#print_r($_SESSION);
#exit;

#Read query again. Ugly. Should be query object
require($_SETUP['HOME_DIR'] . "/code/lib/query/query.class.php");
$_lib['security']   = $_security  = new security(array('_sess' => $_sess, '_template' => $_template));
$_lib['log']        = $_log       = new logg(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess, 'path' => $log_path, 'module' => $args[0], 'template' => $args[1]));
$_lib['form']       = $_form      = new form(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
$_lib['form2']      = $_form2     = new form2(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess));
$_lib['form3']      = $_form3     = new form3(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess, '_QUERY' => $_QUERY));
#print $_sess->get_person("FirstName");
#print "Her og4<br>";
if($searchstring) {
  $_lib['log']->search($_sess, 'table', $searchstring);
}
#log_usage($sess, $template);
#Extra security check. Default no intrantt templates can be viewed by other than internal users, has to be turned off manually.
#if($_SESSION['interface'] == 'intranett' || $_SESSION['interface'] == 'lodo') {
if($_lib['sess']->get_person('PersonID') > 0 or ($_SETUP['ACTIVE_INTERFACE'] == 'lodo' and $args['0'] == 'lodo' and $args['1'] == 'index'))
{
}
/*if($_SETUP['ACTIVE_INTERFACE'] == 'intranett' and $_template)
{
    //print_r($_template);
    $_security->company_access($_template->OnlyAllowInternUser);
}*/
elseif(!$_template and $args[0] != 'lib')
{
    print "Security error. Unable to find security information for template: ".$_SETUP['ACTIVE_INTERFACE'].".$args[0].$args[1]<br>";
    exit;
}

#if(ini_get('magic_quotes_gpc') || ini_get('magic_quotes_sybase') || ini_get('magic_quotes_runtime')) {
#   print "<b>Warning:</b>We recommend to turn of magic quotes<br>";
#}
#Typ
#Type has to be defined for valid entrypoint. extranett/intranett/internett. Shoul be set in role login.

#$list=new ViewList("expences");
#includeÊ"class.php";
#$list=ÊnewÊViewList("expences");
#$list->NoShow("Activity");
#$list->printHead();
#$list->printBody();

######################################
#No lib includes should be done while using this framework. Authentication and lib including is done automatically.
#Parameter in example = t=invoice.edit (no file extension)

#print "int: $int<br>";
#print "interface index: ny: $int, gammel:" . $_SESSION['interface'] . "login_id: " . $_SESSION['login_id'] . "<br />";

#Cust is the flag for customized code. It is found on RoleTemplate but not retrieved yet.

# print "Inkluderer: " . $include."<br>";
if(!$include)
{
    if($args[0] == 'lib')
    {
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].'code'.$_SETUP['SLASH'].$args[0].$_SETUP['SLASH'].$args[1].".php";
        #print "Er lib: $include<br>\n";
    }
    else
    {
        #$include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].$_SETUP['ACTIVE_INTERFACE'].$_SETUP['SLASH'].$args[0].$_SETUP['SLASH'].$args[1].".php";
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].'modules'.$_SETUP['SLASH'].$args[0].$_SETUP['SLASH'].'view/'.$args[1].".php";

        #print "ikke lib: $include<br>\n";
    }
} else {
    #print "Fra for: $include<br>\n";
}

#Debug
#print "interface: #" . $_SESSION['interface'] . "#<br>";
#print "login_id:  #" . $_SESSION['login_id']  . "#<br>";

#print $_SERVER['HTTP_USER_AGENT'];

#session_id has to be kept as secret as possible
#print $_SETUP['ACTIVE_INTERFACE'];
$_SETUP['DISPATCHX'] = $_SETUP['ACTIVE_INTERFACE'].".php?t=$args[0].$args[1]";
$_SETUP['DISPATCHR'] = $_SETUP['ACTIVE_INTERFACE'].".php?SID=".$_lib['sess']->get_session('SID') . $_REQUEST['_Level2ID']."&";        # (R) For refresh only - funker ikke med &amp;
$_SETUP['DISPATCHS'] = $_SETUP['ACTIVE_INTERFACE'].".php";                                            # (S) Simple - without session, & and other special signs, for form login, etc
$_lib['sess']->dispatch  = $_SETUP['ACTIVE_INTERFACE'].".php?SID=".$_lib['sess']->get_session('SID') . "&amp;";    #Add session to all URLS cookies could be disabled

$MY_SELF  = $_SETUP['ACTIVE_INTERFACE'] . ".php?t=" . $args[0] . "." . $args[1] . "&amp;";
$_MY_SELF = $MY_SELF;
#Just run the code.
#print "$include<br>";
#Check if you are allowed to run the template

#Common headers
#print "$args[0]  $args[1] <br>";
#if($args[1] != 'index1' and $args[1] != 'index') {
$_doctype  = $_SETUP['XML']    . "\n";
$_doctype .= $_SETUP['DOCTYPE']. "\n";
$_doctype .= $_SETUP['HTML']   . "\n";
#}
#print "her<br>";
#print "$include<br>";
if(!$_SETUP['ACTIVE_INTERFACE'])
{
    $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].$_SETUP['ACTIVE_INTERFACE'].$_SETUP['SLASH'].$_SETUP['ACTIVE_INTERFACE'].$_SETUP['SLASH'].$_SETUP['FIRSTPAGE'].".php";
    $_lib['message']->add('Session timeout');
    print "Manglet aktivt grensesnitt: $include";
    //exit;
}
elseif(file_exists($include) == false)
{
    #If file does not exist something is wrong about authentication or session handling (or programmer error, reauthenticate)

    $_lib['message']->add("File does not exist: $include");
    #print "Finnes ikke: $include";
    $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].'modules/'.$_SETUP['ACTIVE_INTERFACE'].$_SETUP['SLASH'].'/view/'.$_SETUP['SLASH'].$_SETUP['FIRSTPAGE'].".php";
    #print "Finnes - reautentiser: $include";
    #exit;
}

# print "$include<br>";
$aclev = $_lib['sess']->check_roletemplate($_SETUP['ACTIVE_INTERFACE'], $args[0], $args[1]);

if($aclev >= 1 or (!$_SETUP['SECURITY']['ROLE'] and $_lib['sess']->login_id > 0))
{
    #print "access: $aclev, action: " . $_REQUEST['action_general_update'] . "t=$_REQUEST[t], include: $include<br>";
    if($_SETUP['ACTIVE_INTERFACE'] != 'lodo')
    {
        foreach($_lib['input']->get_action() as $table => $action)
        {
            #acess kontroll pŒ table
            if($table)
            {
                $action_file = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].'lib'.$_SETUP['SLASH'].'action'.$_SETUP['SLASH'].$table.$_SETUP['SLASH'].$action.".inc";
                #print "$action_file<br>\n";
                if(file_exists($action_file))
                {
                    #Run the actions specified (unordered sequence)
                    $_action = 'edit';
                    require_once($action_file);
                }
                else
                {
                    $_lib['sess']->debug("action.$table.$action not found");
                }
            }
        }
    }
    #Run condition/event check before

    #User asking for edit access? ask for login if not logged in
    if($_REQUEST['action'] == 'edit' and $_lib['sess']->login_id <= 0)
    {
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].'lib'.$_SETUP['SLASH']."login_screen.php";
    }
    #print "Her og6: $include<br>";
    #print "$include";
    require_once($include);
    #print "Her og7<br>";
    #Run condition/event check after
}
else
{
    $_lib['message']->add("User trying to acess a template without role access");
    $_lib['log']->accessdenied($_lib['sess'], $_template, $args);
    //print "Access denied to this template: " . $_SETUP['ACTIVE_INTERFACE'] . ".$args[0].$args[1]<br>";exit;
    $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].'lib'.$_SETUP['SLASH']."login_screen.php";
    //print "$include";exit;
    require_once($include);
}

#Fang opp at fil ikke finnes, mŒ st¿tte debug flagg.

#Print debug and error information
$_lib['sess']->debug("Destroy session object");
$duration = $_lib['sess']->print_debug();
if($duration > 10)
{
    #Log pages slower than 10 seconds
    $_lib['log']->slowpage(array('duration' => $duration));
}

?>
