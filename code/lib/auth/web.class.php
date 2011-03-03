<?
# $Id: web.inc,v 1.43 2005/11/18 07:35:46 thomasek Exp $ authentication.inc,v 1.3 2001/11/18 15:34:22 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no


#print "Jeg far: " . $_SESSION['DB_NAME'] . " vs $_SETUP[DB_NAME_DEFAULT] vs " . $_REQUEST['DB_NAME_LOGIN']. "<br>";
#print "login_ind: " . $_SESSION['login_id'] . "<br>";

#####################################################################################
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
if(strlen($_REQUEST['Username']) > 0 and strlen($username) == 0)
{
    $username = $_REQUEST['Username'];
}
if(strlen($_REQUEST['Password']) > 0 and strlen($password) == 0)
{
    $password = $_REQUEST['Password'];
}
#print "debug: $DB_SERVER[0], $DB_USER[0], $DB_PASSWORD[0]";
//print "debug: username; $username, passord: $password<br>";
//exit;

#print "etterpa: " . $_SESSION['DB_NAME'] . " vs $_SETUP[DB_NAME_DEFAULT] vs " . $_REQUEST['DB_NAME_LOGIN']. "<br>";


#####################################################################################
#User is logged in - check session credentials
if ($_SESSION['login_id'] and !$_REQUEST['DB_NAME_LOGIN'])
{
    //print "User is logged in";
  #Extra secure authentication
  #Session should also be invalidated

  #print $_SESSION['fingerprint'] . " = " . md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_PROTOCOL'] . session_id());
  #exit;

    if($_SESSION['fingerprint'] && $_SESSION['fingerprint'] != md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['SERVER_PROTOCOL'] . session_id()))
    {
        $args['Message'] = "Forged session";
        accessdeniedpreinit(array('Module'   => $args[0], 'Template' => $args[1]), $_template, $args);
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH'].$code.$_SETUP['SLASH'].$_SETUP['interface'].$_SETUP['SLASH'].$_SETUP['INTERFACE'].$_SETUP['SLASH'].$_SETUP['FIRSTPAGE'].".php";
        $message .= "Forged session&redirected=".$_REQUEST['redirected'];
    }
    else
    {
        #You can change language anytime
        if(isset($_REQUEST['lang']))
        {
          $_SESSION['lang'] = $_REQUEST['lang'];
        }
    }
}
#####################################################################################
#The user tries to login when these parameters exists
elseif ($username && $password && $_REQUEST['DB_NAME_LOGIN'])
{
    //print "The user tries to login";

    #$query = " select PersonID, FirstName, LastName, Email, Language, Css FROM person WHERE Email='$username' and Password=PASSWORD('$password') and Active=1";

    $query = " select PersonID, FirstName, LastName, Email, LanguageID, Css FROM person WHERE Email='?' and Password=PASSWORD('?') and
trim(Password) <> ''";

    $_lib['sess']->debug($query);
    #print "!-- $query<br> -->\n";

    #$_row = $_lib['db']->get_row(array('query' => $query));
    $_row = $_lib['db']->get_row2(array('query' => $query, 'values' => array($username, $password)));
#print $_row->PersonID;
   #print "<hr>";
   #print_r($_row);
   #print "<hr>";
    if (!$_row->PersonID)
    {
        $args['Message'] = "Email ($username) or password ($password) wrong: db: $_REQUEST[DB_NAME_LOGIN]<br>$query<br>";
        //print "Email ($username) or password ($password) wrong: db: $_REQUEST[DB_NAME_LOGIN]<br>$query<br>";
        accessdeniedpreinit(array('Module' => $args[0], 'Template' => $args[1]), $_template, $args);
        $include = $_SETUP['HOME_DIR'].$_SETUP['SLASH']."code".$_SETUP['SLASH'].$_SETUP['INTERFACE'].$_SETUP['SLASH'].$_SETUP['FIRSTPAGE'].".php";
        $_lib['message']->add(array('message' => 'Password or Email Invalid'));
    }
    else {
        // log login to company database table `logusage'
        $tmp_logger = new logg(array('_dsn' => $_dsn, '_SETUP' => $_SETUP, '_sess' => $_sess, 'path' => $log_path, 'module' => $args[0], 'template' => $args[1]));
        $tmp_logger->usage(array('sess'=>$_lib['sess']));
    }
    #Global information on logged in users
    $_SESSION['login_id'] = $_row->PersonID;
    $_lib['sess']->debug("Auth: $_row->PersonID");
#print "loginid: ".$_row->PersonID."<br>\n";
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

#####################################################################################
#The user is not logged inn (public browsing)
}
else
{
    //print "The user is not logged inn";
    if($_REQUEST['lang'])
    {
        $_SESSION['lang'] = $_REQUEST['lang'];
    }
    else
    {
        $_SESSION['lang'] = $_SETUP['LANGUAGE']; #Get default from configuration file
    }
    $_SESSION['css']   = $_SETUP['CSS'];  #Get default from configuration file
    $user_type   = "public"; #Parameter used to determine if user is internal or external
}

#Variables used to update database
$DB_CREATED = " CreatedByIP='".$_SERVER['REMOTE_ADDR']."', CreatedDate=NOW()";
$DB_UPDATED = " UpdatedByIP='".$_SERVER['REMOTE_ADDR']."'";
?>
