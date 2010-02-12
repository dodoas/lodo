<?
# $Id: rest.php,v 1.8 2006/09/18 09:23:01 thomasek Exp $

#/rest.php?Object=servicepartnerproximity&Version=1&Action=get&username=i@k.n&password=i&DAddress=Sandeveien

require_once("../conf/default.inc");
$_prefs_file = "../conf/prefs_" . $_SETUP['DB_NAME_DEFAULT'] . ".inc";
include($_prefs_file);
$_REQUEST['DB_NAME_LOGIN'] = $_SETUP['DB_NAME_DEFAULT'];

################################################
#Choose correct interface
$_SETUP['ACTIVE_INTERFACE'] = $_SETUP['LOGIN_INTERFACE'].$_SETUP['VERSION'];

function includelogic($class)
{
    global $_SETUP;
    require_once($_SETUP['HOME_DIR'] . "/logic/" . $class . ".class.php");
}

function includeinc($inc)
{
    global $_SETUP, $_date, $_lib, $_format, $_sess, $_dbh, $_dsn, $_dblodo;
    require_once($_SETUP['HOME_DIR'] . "/inc/" . $inc . ".inc.php");
}

function includealogic($inc)
{
    global $_SETUP;
    $fil = $_SETUP['HOME_DIR'] . "/alogic/" . $inc . ".class.php";
    if (! is_file($fil))
        $fil = $_SETUP['HOME_DIR'] . "/code/lodo/lib/" . $inc . ".class";
    require_once($fil);
}

//session_start();
require_once($_SETUP['HOME_DIR'] . "/code/lib/session/session.class.php");
$_lib['sess'] = & new SessionNew(array('database' => $_SESSION['DB_NAME'], 'company_id' => $_SETUP[COMPANY_ID], 'login_id' => $_SESSION['login_id'], 'interface' => $_SETUP['ACTIVE_INTERFACE'], 'module' => $args[0], 'template' => $args[1], 'LoginFormDate' => $_SESSION['LoginFormDate']));

require_once($_SETUP['HOME_DIR']."/code/lib/db/db_" . $_SETUP['DB_TYPE']['0'] . ".class.php");
$_dbh = array();
$_dsn = $_SETUP['DB_SERVER']['0'] . $_SESSION['DB_NAME'] . $_SETUP['DB_TYPE']['0'];

require_once($_SETUP['HOME_DIR'] . "/code/lib/convert/convert.class.php");
$_lib['convert']    = $_convert   = new convert(array('_dbh' => $_dbh, '_dsn' => $_dsn));

$_lib['storage'] = $_lib['db'] = $_dblodo = $_dbh[$_dsn] = & new db_mysql(array('host' => $_SETUP['DB_SERVER']['0'], 'database' => $_SETUP['DB_NAME']['0'], 'username' => $_SETUP['DB_USER']['0'], 'password' => $_SETUP['DB_PASSWORD']['0'], '_sess' => $_sess));

require_once($_SETUP['HOME_DIR'] . "/code/lib/query/query.class.php");        #Saved queryes, to be replaced with web interface
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements.class.php");  #Auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements2.class.php"); #No auto save
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form_elements3.class.php"); #only hash parameters
require_once($_SETUP['HOME_DIR'] . "/code/lib/form/form.class.php");        #only hash parameters
#require_once($_SETUP['HOME_DIR'] . "/code/lib/gui/list_procedures_2.3.inc");

require_once($_SETUP['HOME_DIR'] . "/code/lib/message/message.class.php");
$_lib['message'] = new message(array('dbserver'=> $_SETUP['DB_SERVER']['0'], 'dbname' => $_SESSION['DB_NAME']));
$_lib['message']->add(array('message' => $_REQUEST['message']));

require_once($_SETUP['HOME_DIR'] . "/code/lib/cache/cache.class.php");
$_lib['cache']  = new Cache(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/date/date.class.php");
$_lib['date']   = new Date($_DF, $_NF);

require_once($_SETUP['HOME_DIR'] . "/code/lib/log/log.class.php");
$_lib['log']    = new logg(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/input/input.class.php");
$_lib['input']  = new input();

require_once($_SETUP['HOME_DIR'] . "/code/lib/format/format.class.php");
$_lib['format'] = new format(array('_NF' => $_NF, '_DF' => $_DF, '_dbh' => $_dbh, '_dsn' => $_dsn));

require_once($_SETUP['HOME_DIR'] . "/code/lib/security/security.class.php");

//require_once($_SETUP['HOME_DIR'] . "/code/lib/init/init.php");
//$_lib['template']->Interface = $_SETUP['ACTIVE_INTERFACE'];

#NEW
$httprawpostdata = file_get_contents("php://input");

$_lib['log']->file($httprawpostdata);

$xml = simplexml_load_string($httprawpostdata);

$old_pattern    = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
$new_pattern    = array("_", "_", "");
$InstallName    = strtoupper(preg_replace($old_pattern, $new_pattern , (string) $xml->firma)); 

$dataH['InstallName']       = $InstallName; #regexp
$dataH['DealerName']        = 'Akademikernes Servicesenter AS';
$dataH['DealerEmail']       = 'support@konsulentvikaren.no';
$dataH['VName']             = (string) $xml->firma;
$dataH['VAddress']          = (string) $xml->adresse;
$dataH['VCity']             = (string) $xml->sted;
$dataH['VZipCode']          = (string) $xml->postnummer;
#$dataH['VCountry']         = $xml->VCountry;
$dataH['Phone']             = (string) $xml->telefon;
#$dataH['Fax']              = $xml->Fax;
$dataH['WWW']               = (string) $xml->webadresse;
$dataH['CompanyNumber']     = (string) $xml->organisasjonsnummer;
#$dataH['OrgNumber']        = $xml->organisasjonsnummer;
$dataH['CreatedDateTime']   = 'NOW()';
#$dataH['InstalledDateTime'] = $xml->InstalledDateTime;
$dataH['LastName']          = (string) $xml->etternavn;
$dataH['FirstName']         = (string) $xml->fornavn;
$dataH['Email']             = (string) $xml->email;
$dataH['Password']          = (string) $xml->passord;
$dataH['MobilePhoneNumber'] = (string) $xml->MobilePhoneNumber;
$dataH['Version']           = (string) $xml->versjon;
$dataH['Active']            = 0;
$dataH['EnableReference']   = 0;
$dataH['AcceptedLicence']   = 1;

$_lib['storage']->store_record(array('table' => 'installation', 'data' => $dataH, 'debug' => false));
exit;

function required($field)
{
    global $_lib;

    if($_lib['input']->getProperty($field))
    {
        return true;
    }
    else
    {
        print "Missing parameter $field";
        return false;
    }
}

$_REQUEST['xml'] = str_replace("&", "&amp;", $_REQUEST['xml']);

$xmlin = simplexml_load_string($_REQUEST['xml']);
$_lib['log']->file($_REQUEST['xml']);

//print_r($xmlin->Head);
if($xmlin)
{
    foreach($xmlin->Head->children() as $key => $value)
    {
        #XML input from head overrides querystring parameters
        //print "$key = $value <br>\n";
        $_lib['input']->setProperty($key, $value);
    }
}

required('Object');
required('Action');
required('Version');

require_once($_SETUP['HOME_DIR']."/code/lib/auth/web.inc"); #Should be factory pattern
//$_lib['auth'] = framework_lib_auth::getInstance(array());

require_once($_SETUP['HOME_DIR'] . "/code/lib/lang/language.class");
$_lib['lang']       = $_lang = new language(array('language' => $_SESSION['lang'], 'action' => $_action));

//$_lib['sess']->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_lib['auth']->PersonID, 'Interface' => $_lib['auth']->Interface, 'LanguageID' => $_lib['auth']->LanguageID, 'language' => $_lib['auth']->LanguageID, 'lang' => $_lib['auth']->language)); #init dbh in session object after dbh is setup and verified working
$_lib['sess']->SessionInit(array('dbh' => $_dbh, 'dsn' => $_dsn, 'login_id' => $_SESSION['login_id'],  language => $_SESSION['lang'])); #init dbh in session object after dbh is setup and verified working

################################################
#Set headers
$_lib['sess']->setHTTPHeader('Expires', 0);                 #Better. Enabled again 2005-05-24
$_lib['sess']->setHTTPHeader('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT"); #Enabled again 2005-05-24
$_lib['sess']->setHTTPHeader('Cache-Control', 'private, no-cache');   #Impossible to press  back in browser
$_lib['sess']->setHTTPHeader('Pragma', 'no-cache');         # Enabled again 2005-05-24
$_lib['sess']->setHTTPHeader('Content-type', 'application/xml;charset=iso-8859-1');

##############Retur
includelogic('simplexml/simplexml');
if(strlen($_SETUP['XML_ROOT_TAG']) > 0)
    $rootTag = $_SETUP['XML_ROOT_TAG'];
else
    $rootTag = "<Empatix></Empatix>";
$xml = new XMLElement($rootTag); #B¿r kunne hgyttes med variabel fra oppsett

$head = $xml->addChild('Head');
$head->addChild('Version'   ,   1);
$head->addChild('Object'    ,   '');
$head->addChild('Action'    ,   'select');
$head->addChild('Hint'      ,   '');
$head->addChild('LanguageID',   $_lib['auth']->LanguageID);
$head->addChild('SessionID' ,   $_lib['auth']->SessionID);
$head->addChild('Username'  ,   $_lib['input']->getProperty('username'));
//$head->addChild('Password'  ,   $_lib['input']->getProperty('password'));
$head->addChild('Message'   ,   $_lib['message']->get());
$head->addChild('ElapsedTime', '');

#REMEMBER to update this hash, so we dont get duplicate items in xml head.
$addedItemsH = array(
    'xml'=>'1',
    'Version'=>'1',
    'Object'=>'1',
    'Action'=>'1',
    'Status'=>'1',
    'Hint'=>'1',
    'LanguageID'=>'1',
    'Fingerprint'=>'1',
    'SessionID'=>'1',
    'Username'=>'1',
    'Password'=>'1',
    'username'=>'1',
    'password'=>'1',
    'Message'=>'1',
    'ElapsedTime'=>'1',
    'ErrorID'=>'1',
    'ErrorMessage'=>'1',
    'ErrorSeverity'=>'1',
    $_SETUP['SESSION']['NAME']=>'1'
);

foreach($_GET as $key => $value)
{
    #Ikke add noe som er fast fra ovenfor.
    if($addedItemsH[$key] != '1')
    {
        $head->addChild($key, $value);
    }
}



if($_SESSION['login_id'] > 1) //sjekk om vi er logget inn før vi genererer body
{
    $file = $_SETUP['HOME_DIR'].$_SETUP['SLASH']. 'code' .$_SETUP['SLASH'].$_SETUP['ACTIVE_INTERFACE'].$_SETUP['SLASH'].$_lib['input']->getProperty('Object').$_SETUP['SLASH'].'view'.$_SETUP['SLASH'].'xml'.$_SETUP['SLASH'].'version'.$_lib['input']->getProperty('Version').".php";
    require_once($file);
    $module = $_lib['input']->getProperty('Object').'_view_xml';
    $$module = new $module(array());
    $returnXML = $$module->Execute(array());

    if($_lib['message']->get()) {
        $errormessage = substr(strip_tags($_lib['message']->get()),0,200);
        #$errormessage = 'URGH';

        #print "<h1>$errormessage</h1>";

        $error = $xml->addChild('Errors');
        $head->addChild('Status', 'ERROR');
        $error->addChild('ErrorID', '1');
        $error->addChild('ErrorMessage', $errormessage);
        $error->addChild('ErrorSeverity', '1');
    } else {
        $head->addChild('Status', 'OK');
    }

    $body = $xml->addChild('Body');

    //print_r($returnXML);
    if($returnXML)
    {
        $body->addElement($returnXML, true);
    }
}
else
{
    ##Sjekk message for erros.
    $head->addChild('ErrorID', '1');
    $head->addChild('ErrorMessage', 'User does not have access to this information');
    $head->addChild('ErrorSeverity', '1');
}

##retuner xml.
//print "<br><br><br>\n\n\n";
//print_r($xml);
$_lib['sess']->printHTTPHeader();
print $xml->asXML();
#print "<hr>$file";
?>