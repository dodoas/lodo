<?
if(!$_SESSION['css']) {
  $_SESSION['css'] = "/css/default";
}
?>
    <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
    <meta name="author"             content="Thomas Ekdahl, thomas@ekdahl.no, http://www.ekdahl.no/" />
    <meta name="copyright"          content="Thomas Ekdahl, 1994-2004" />
    <meta name="technology"         content="Apache, PHP, MySQL, Perl" />
    <meta name="generator"          content="BBEdit 7.1"               />
    
    <link rel="stylesheet"          	title="Default" href="<? print "$_SESSION[css]_".$_SETUP['ACTIVE_INTERFACE'].".css"; ?>" media="screen" type="text/css" />
    <link rel="stylesheet"            	title="Default" href="<? print "$_SESSION[css]_".$_SETUP['ACTIVE_INTERFACE'].".css"; ?>" media="print" type="text/css" />
    <link rel="alternate stylesheet" 	title="Test"    href="/template/css/dfds_large.css" type="text/css" title="Default" >
    
    <link rel="stylesheet"          href="/css/sla_intranett.css" media="screen" type="text/css" />
    <link rel="icon"                href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon"       href="favicon.ico" type="image/x-icon" />

    <script type="text/javascript"  src="/lib/tigra_calendar/calendar1.js"></script>
    <style  type="text/css">@import url(/lib/htmlarea3/htmlarea.css)</style>