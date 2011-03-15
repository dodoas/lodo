<?
#Script that updates the tablemetadata for all tables in all databases on this server
#Has to be run after adding / removing or changing fields in tables.

if($argc <= 1) {
  print "\nLodo batch usage: objectname.method name1=value1 name2=value2 etc\n";
  print "example:\n";
  print "php batch.php model_tablemetadata_tablemetadata.dbupdate db_name=konsulentvikaren0\n";
  print "php batch.php model_tablemetadata_tablemetadata.updateall\n";
  print "php batch.php model_tablemetadata_tablemetadata.updateallskipsystemdbs\n";
  print "php batch.php model_tablemetadata_tablemetadata.updateallskipsystemdbs tablefilter=invoicein\n";
  print "php batch.php model_synchronizeinstallation_synchronizeinstallation.updateinstalltable db_name=konsulentvikaren0\n\n";
  print "php batch.php model_tablemetadata_tablemetadata.runscriptall scriptpath='db/changes/022_scriptname.sql'\n\n";
  print "php batch.php model_fakturabank_fakturabank.generate_invoice_xml InvoiceID=1\n";
  exit;
}

#print_r($argv[1]);

list($class_name, $method) = split('\.', $argv[1]);
#print "name: $class_name, method: $method\n";
list($interface, $module, $class) = split('_', $class_name);

include('conf/default.inc');

require_once("code/lib/db/db_" . $_SETUP['DB_TYPE_DEFAULT'] . ".class.php");
require_once("code/lib/session/session.class.php"); #MŒ v¾re etter auth
require_once("code/lib/message/message.class.php");
require_once("code/lib/convert/convert.class.php");
require_once("code/lib/date/date.class.php");

$_lib['message']    = new message(array('dbserver'=> $_SETUP['DB_SERVER']['0'], 'dbname' => $_SESSION['DB_NAME']));
$_lib['sess']       = new SessionNew(array('database' =>  $_SETUP['DB_NAME_DEFAULT'], 'company_id' => $_SETUP['COMPANY_ID'], 'interface' => $_SETUP['ACTIVE_INTERFACE']));
$_lib['storage']    = $_lib['db'] = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'], 'database' => $_SETUP['DB_NAME_DEFAULT'], 'username' => $_SETUP['DB_USER_DEFAULT'], 'password' => $_SETUP['DB_PASSWORD_DEFAULT']));
if (empty($_DF)) { $_DF = null; }
if (empty($_NF)) { $_NF = null; }
$_lib['date']       = new Date($_DF, $_NF);
if (empty($_dsn)) $_dsn = null;
if (empty($_dbh)) $_dbh = null;
$_lib['convert']    = new convert(array('_dbh' => $_dbh, '_dsn' => $_dsn));

function includemodel($file) {
    global $_SETUP;
    list($module, $class) = explode('/', $file);

    /* sometimes HOME_DIR is not specified making all 
       requires here use root / as base */
    if(!isset($_SETUP['HOME_DIR']))
        $_SETUP['HOME_DIR'] = getcwd();

    require_once($_SETUP['HOME_DIR'] . "/modules/" . $module . "/model/" . $class . ".class.php");
}

function includelogic($class) {
    includemodel($class);
}

#print "Starting: $interface.$module.$class\n";

$class_file = "modules/$module/model/$class.class.php";
#print "object file: $class_file\n";

$_SETUP['ACTIVE_INTERFACE'] = $interface;

if(file_exists($class_file)) {
	require_once($class_file);

	$class_class_name = "model_${module}_${class}";
	
	if(class_exists($class_class_name)) {
		$class_class = new ReflectionClass($class_class_name);
		$class = $class_class->newInstance();
		if(method_exists($class, $method)) {
		
			#Make hash og arguments
			unset($argv[0]);
			unset($argv[1]);
		
			$args = array();
		
			foreach($argv as $pair) {
				list($name, $value) = split('=', $pair);
				$args[$name] = $value;
			}
			
			#print $class_class_name . "->" . $method . "(" . $args . ")\n";		
			$class->{$method}($args);
		} else {
			$_lib['message']->add('The function ' . $method . ' does not exist');		
		}
		
	} else {
		$_lib['message']->add("The object " . $class_class_name . ' does not exist');
	}
}
else {
	$_lib['message']->add("object filen: $class_file not found");
}

#include "../logic/tablemetadata/tablemetadata.class.php";
#$meta = new lodo_logic_tablemetadata();
#$meta->updateall();
#$args['db_name'] = 'konsulentvikaren0';
#$meta->dbupdate($args);

#$args['db_name'] = 'BJORN_SORENSEN_AS';
#$meta->dbupdate($args);

print "############\n";
print $_lib['message']->get();

print "Finished\n";
?>
