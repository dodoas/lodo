<?
# $Id: index.php,v 1.3 2005/08/01 11:41:29 thomasek Exp $

if(is_dir("../../../interface_custom")) #module in empatix
{
    ################################################
    #Choose correct interface
    $_SETUP['ACTIVE_INTERFACE'] = "lodo1";
    $_SETUP['EMPATIX_MODE'] = 1;

    ################################################

    require_once("../../../conf/default.inc");
    require_once($_SETUP['HOME_DIR'] . "/framework/lib/index/index.class.php");
}
else #independent instalation
{
    #ini_set('register_globals', 1);

    session_start();

    #Include logic
    #$_SETUP['INTRFACE'] = "";
     session_start();
     require_once("../conf/default.inc");
     #Init the database asced for
     if(isset($_POST['DB_NAME_LOGIN']))
     {
        #session_regenerate_id();
        $_SESSION['DB_NAME'] = $_POST['DB_NAME_LOGIN'];
        #print "Ny db: " .  $_SESSION['DB_NAME'] . "<br>";
     }
     elseif(!isset($_SESSION['DB_NAME']))
     { #Should we validate this?
        $_SESSION['DB_NAME'] = $_SETUP['DB_NAME_DEFAULT'];
     }
     #print $_SETUP['ACTIVE_INTERFACE'];
     $_prefs_file = "../conf/prefs_" . $_SESSION['DB_NAME'] . ".inc";
     include($_prefs_file);

    ################################################
    #Choose correct interface
    $args = split('\.', $t);

    if(!$args[0]) {
      #print "ger<br>";
      #print_r($_SETUP);
      #print $_SETUP['INTERFACE'];
      #The user is in for the first time or first page, reset interface to default.
      $_SETUP['ACTIVE_INTERFACE']  = $_SETUP['INTERFACE'];
      $args[0] = $_SETUP['ACTIVE_INTERFACE'];
      $args[1] = $_SETUP['FIRSTPAGE'];
    }
    $args[2] =  $_SETUP['ACTIVE_INTERFACE'];

    if($_GET['interf']){
      #print "der<br>";
      #Assign requested interface
      $_SETUP['ACTIVE_INTERFACE'] = $_GET['interf'];
      #print "INT: " . $_SESSION['interface'];
      #Check if interface exists for this user
      #print "Assigned interface: " . $_SESSION['interface'] . "<br>";
    }
    elseif(!$_SETUP['ACTIVE_INTERFACE']) {
      #Give them what they have
      #print "her<br>";
      $_SETUP['ACTIVE_INTERFACE']  = $_SETUP['INTERFACE'];
    }

    require_once("../code/lib/index/index.class.php");
}
?>
