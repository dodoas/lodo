<?
//if(!$_SESSION['css'])
//{
  $_SESSION['css'] = "default";
//}
$DEFAULT_ACCOUNT_TYPE = 'normal';
$MIN_SESSION_LENGTH = 5; //Minimum session length is set to be 5 sec.
if ( empty($_SETUP['SESS_LENGTH']) ) {
    $_SETUP['SESS_LENGTH'] = 300;
}
$current_time = time();
$account_type = $_lib['sess']->get_companydef('account_type') or $DEFAULT_ACCOUNT_TYPE;
$session_length = ( $current_time - $_SESSION['StartTS'] < $_SETUP['SESS_LENGTH'] ) ? $_SETUP['SESS_LENGTH'] - ( $current_time - $_SESSION['StartTS'] ) : $MIN_SESSION_LENGTH;
if ( $account_type == $DEFAULT_ACCOUNT_TYPE ) {
    echo '<meta http-equiv="refresh" content="' . $session_length . ';URL=http://' . $_SERVER['SERVER_NAME'] . '/redirect.php" />';
}
?>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <meta name="author"             content="Thomas Ekdahl, thomas@ekdahl.no, http://www.ekdahl.no/" />
    <meta name="copyright"          content="Thomas Ekdahl, 1994-2004" />
    <meta name="technology"         content="Apache, PHP, MySQL, Perl" />
    <meta name="generator"          content="BBEdit 7.1"               />

    <link rel="stylesheet"              title="Default" href="/css/default_lodo.css" media="screen" type="text/css" />
    <link rel="stylesheet"              title="Default" href="/css/lodo_print.css" media="print" type="text/css" />

    <link rel="icon"                href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon"       href="favicon.ico" type="image/x-icon" />

    <script type="text/javascript"  src="/lib/tigra_calendar/calendar1.js"></script>
    <script type="text/javascript"  src='/lib/js/jquery.js'></script>
    <style  type="text/css">@import url(/lib/htmlarea3/htmlarea.css)</style>
