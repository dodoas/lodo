<?
# $Id: htaccess.inc,v 1.15 2005/10/14 13:15:40 thomasek Exp $ authentication.inc,v 1.3 2001/11/18 15:34:22 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no

#####################################################################################

  if($_POST['DB_NAME_LOGIN']) { #Set default db if none is defined
    $_SESSION['DB_NAME'] = $_POST['DB_NAME_LOGIN'];
  }
  elseif(!$_SESSION['DB_NAME']) {
    $_SESSION['DB_NAME'] = $_SETUP['DB_NAME_DEFAULT'];
  }

#####################################################################################

#print "$_POST['username'] $password $_SESSION['DB_NAME']<br>";

#print "Jeg far: " . $_SESSION['DB_NAME'] . " vs $_SETUP[DB_NAME_DEFAULT] vs " . $_POST['DB_NAME_LOGIN']. "<br>";
#print "login_ind: " . $_SESSION['login_id'] . "<br>";

$auth = false; // Assume user is not authenticated

if (isset( $PHP_AUTH_USER ) && isset($PHP_AUTH_PW)) {

    $query = "SELECT * FROM person WHERE
            Email = '$PHP_AUTH_USER' AND
            Password=PASSWORD('$PHP_AUTH_PW')";



    // Execute the query and put results in $result

   $_row = $_lib['db']->get_row(array('query' => $query));

    if ($_row) {
     // A matching row was found - the user is authenticated.
     $auth = true;

     #Set default language for user
     $_SESSION['login_id'] = $_row->PersonID;
     #Set default language for user
     if($_REQUEST['lang']) {
       $_SESSION['lang'] = $_REQUEST['lang'];
     } elseif($_row->Language) {
       $_SESSION['lang'] = $_row->Language;
     } else {
       $_SESSION['lang'] = $_SETUP[LANGUAGE]; #Get default from configuration file
     }
     #Set default css for user
     if($_row->Css) {
       $_SESSION['css'] = $_row->Css;
     } else {
       $_SESSION['css'] = $_SETUP[CSS]; #Get default from configuration file
     }

     #Set security control data fingerprint - prevents session hijacking
     $_SESSION['fingerprint']  = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_PROTOCOL'] . session_id());

     $_SESSION['LoginFormDate'] = $_REQUEST['LoginFormDate'];
    }
}

if ( ! $auth ) {
    header ( 'WWW-Authenticate: Basic realm="Empatix"' );
    echo( 'HTTP/1.0 401 Unauthorized' );
    echo 'Authorization Required.';
    exit;
}
?>
