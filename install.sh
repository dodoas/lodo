#!/bin/bash
set -e
database=LODO
dbuser=lodo

ln -s lodo html
echo "Warning: MySQL passwords will be passed on commandline"

trap 'stty echo; echo; exit' TERM INT
echo -n "MySQL root password: "
stty -echo
read pw
echo
echo -n "Password for new MySQL user $dbuser: "
read dbuserpw
echo
stty echo
echo -n "Email address/username (for Lodo admin user): "
read email
echo -n "First name: "
read firstname
echo -n "Last name: "
read lastname
stty -echo
echo -n "Password: "
read lodouserpw
echo
stty echo
trap - TERM INT

echo -n "Creating MySQL user '$dbuser'"...
echo "GRANT SELECT, LOCK TABLES, INSERT, UPDATE, DELETE, CREATE, ALTER
ON *.* TO '$dbuser'@'localhost' IDENTIFIED BY '$dbuserpw'; FLUSH PRIVILEGES;" | mysql -u root -p"$pw" mysql
echo "OK"

echo -n "Creating database and importing internal datastructures..."
{
# All output from this block is piped into mysql
echo "CREATE DATABASE $database; USE $database;"
cat db/sql/intern_datastructure.sql
cat db/sql/content.sql

echo "INSERT INTO person SET Email='$email', FirstName='$firstname',
LastName='$lastname', AccessLevel=4, Active=1, Password=PASSWORD('$lodouserpw');"

echo "INSERT INTO installation SET InstallName='$database',
EnableReference=1, AcceptedLicence=1, VName='$database',
CreatedDateTime=NOW(), InstalledDateTime=NOW(), Password='$lodouserpw',
Email='$email', FirstName='$firstname', LastName='$lastname', Active=1;"
year=$(date +%Y)
for month in 01 02 03 04 05 06 07 08 09 10 11 12 13
do
  echo "INSERT INTO accountperiod SET Status=2, CreatedDate=now(),
CreatedByID=1, ValidFrom='2008-01-01', ValidTo='9999-12-31', Payed=1,
Period='$year-$month';"
done
} | mysql -u $dbuser -p"$dbuserpw"
echo "OK"

echo -n "Writing code/lib/setup/default.inc and code/lib/setup/prefs_$database.inc ..."
cat <<EOF > code/lib/setup/default.inc
<?
##################################################################
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 2004, thomas@ekdahl.no, http://www.ekdahl.no/
# All variables should be upper case
# This file only controls uninloged action before a separate setup file is choosed

##################################################################
#Headers to make sure dynamic content is not cached
#header("Expires: Wed, 24 Dec 2003 05:00:00 GMT");
header("Expires: 0"); #Better
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

##################################################################
#Default database and preferences to cchoose if not explisitly given
\$_SETUP['DB_TYPE_DEFAULT']          = "mysqli";
\$_SETUP['DB_NAME_DEFAULT']        = "$database";              #Default database to use
\$_SETUP['DB_USER_DEFAULT']        = "$dbuser";         #User in database
\$_SETUP['DB_PASSWORD_DEFAULT']    = "$dbuserpw";             #Password to user in database
\$_SETUP['DB_SERVER_DEFAULT']        = "localhost";      #Server where the database is
\$_SETUP['DB_OFFSET_DEFAULT']        = 30;               #Defines how many rows to display at once
\$_SETUP['DB_LIMIT_DEFAULT']     = 50;               #Defines max number of rows to get
\$_SETUP['DB_ALLOW_DEFAULT']     = "(Active = '1' AND ValidFrom <= NOW() AND ValidTo >= NOW())"; #Define the criterias for an article beeing published

\$_SETUP['LODO_DEFAULT_INSTALL_DB'] = '$database';
\$_SETUP['LODO_DEFAULT_INSTALL_SERVER'] = 'localhost';
\$_SETUP['LODO_DEFAULT_INSTALL_USER'] = '$dbuser';
\$_SETUP['LODO_DEFAULT_INSTALL_PASSWORD'] = '$dbuserpw';

##################################################################
#Basic setup information
\$_SETUP['COMPANY_ID']       = 1;                #CompanyID
\$_SETUP['XML_VERSION']      = "";
\$_SETUP['CSS']              = "/css/default";   #Possible to have complet path
\$_SETUP['LANGUAGE']         = "en";
\$_SETUP['DISPATCH']         = "/index.php?";
\$_SETUP['INTERFACE']        = "internett";
\$_SETUP['SLASH']            = "/";

##################################################################
#Set default debug level
\$_SETUP[DEBUG]              = false;  #Debug level

##################################################################
#Number format
\$_NF['decimals']            = 2;
\$_NF['dec_point']           = ',';
\$_NF['thousands_sep']       = ' ';

##################
#Input to date function
#dd   = day 2 digit
#mm   = month 2 digit
#MM   = month text
#yyyy = year 4 digit
\$_SETUP['date_format']      = 'yyyyMMdd';
\$_SETUP['date_separator']   = ' ';

##################################################################
#Path to mysql binaries
\$_SETUP['MYSQLADMIN']       = "/usr/bin/mysqladmin";
\$_SETUP['MYSQL']        = "/usr/bin/mysql";
\$_SETUP['MYSQLDUMP']        = "/usr/bin/mysqldump";
?>
EOF

cat <<EOF > code/lib/setup/prefs_$database.inc
<?
##################################################################
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 2004, thomas@ekdahl.no, http://www.ekdahl.no/
# All variables should be upper case

##################################################################
#Database communication - this must be right for anything to work
#This is an array so it is easy to add connections to other databases
\$_SETUP['DB_TYPE'][0]        	= "mysqli";
\$_SETUP['DB_NAME'][0]        	= "$database"; 		#Default database to use
\$_SETUP['DB_USER'][0]        	= "$dbuser";			#User in database
\$_SETUP['DB_PASSWORD'][0]    	= "$dbuserpw";				#Password to user in database
\$_SETUP['DB_SERVER'][0]		= "localhost"; 		#Server where the database is
\$_SETUP['DB_START'][0]		= 0;				#Default start row
\$_SETUP['DB_OFFSET'][0]		= 30;				#Defines how many rows to display at once
\$_SETUP['DB_LIMIT'][0]		= 50;				#Defines max number of rows to get

##################################################################
#Basic setup information
\$_SETUP['COMPANY_ID']		= 1;  				#CompanyID
\$_SETUP['HOME_DIR']               = "$PWD"; #Path to installation directory for svn checkout
\$_SETUP['DOWNLOAD_DIR']		= \$_SETUP['HOME_DIR'] . "/html/download";
\$_SETUP['XML_VERSION']		= "";
\$_SETUP['CSS']			= "/css/default";	#Possible to have complet path
\$_SETUP['LANGUAGE']		= "no";
\$_SETUP['DISPATCH']		= "index.php?";
\$_SETUP['SLASH']			= "/";

##################################################################
#HTML Headers
\$_SETUP['XML']              = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
\$_SETUP['DOCTYPE']          = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\" \"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd\">";
\$_SETUP['HTML']             = "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"no\">";

##################################################################
#Frame control
\$_SETUP['INTERFACE']		= "lodo";
\$_SETUP['FIRSTPAGE']		= "index"; #THe first page the user sees

\$_SETUP['LOGIN_INTERFACE']   = "lodo"; #Interface the user gets after login in
\$_SETUP['LOGIN_FIRSTPAGE']   = "lodo.main";

##################################################################
#Set default debug level
\$_SETUP['DEBUG']				= 0;  				#Debug level

##################################################################
#Publish settings
\$_SETUP['TEMPLATE_DIR']		= 	\$_SETUP['HOME_DIR'] . "/html/template"; #Default template dir
#This should be used on all queries in all modules

##################################################################
#Max size on http uploaded images
\$_SETUP['FILE_MAX_HEIGHT'] 	= "900";			#Max height for picture
\$_SETUP['FILE_MAX_WIDTH']  	= "900";			#Max width for picture

##################################################################
#Security level
\$_SETUP['SECURITY']['ROLE']           	= true; #Turns on and off role control checks
\$_SETUP['SECURITY']['SESSION']		= true;  #Check ip number, browser, protocol to cookies - hash
\$_SETUP['SECURITY']['SESSIONTIMEOUT'] 	= 3600;	 #Login timeout value
\$_SETUP['SECURITY']['LODO']     	 	= true; 

##################################################################
#Server admin url
\$_SETUP['SERVER_ADMIN'] 	  = "http://".\$_SERVER['HTTP_HOST']."/"; #SERVER_NAME
\$_SETUP['SERVER_ADMIN_SSL'] = "https://".\$_SERVER['HTTP_HOST']."/"; #SERVER_NAME_SSL
?>
EOF
echo "OK"

echo -e "Example apache configuration:"
echo "
<VirtualHost *:80>
    ServerAdmin webmaster@$(hostname)
    DocumentRoot $PWD/html
    ServerName $(hostname)
    ErrorLog logs/lodo.no-error_log
    CustomLog logs/lodo.no-access_log common
</VirtualHost>
"

echo "After setting up apache, you should be able to log in with the following:"
echo "Før regnskap for: $database
Brukernavn (e-post): $email
Passord: ****"
