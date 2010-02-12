<?
# $Id: index2.php,v 1.9 2005/10/28 17:59:41 thomasek Exp $ index2.php,v 1.8 2001/12/02 09:19:50 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

#sleep(2);

$interf = $_REQUEST['interf'];
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_SESSION['DB_NAME'] ?> - <? print "$interface"; ?> - <? print $_lib['sess']->get_person('UserName') ?></title>
    <meta name="cvs"                content="$Id: index2.php,v 1.9 2005/10/28 17:59:41 thomasek Exp $" />
    <? require_once $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>
<body>
Teknisk grensesnitt

<ol>

<li><a href="<? print $_lib['sess']->dispatch ?>t=confdbfield.list">Confdbfield</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=urlalias.list">URLAlias</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=language.interface">Language</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=conflayout.list">Conflayout</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=sync.list">Sync</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=menu.list">Menu</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=tab.list">Tab</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=database.list">Database management</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=integration.list">Integrations</a>
<li>Ikke IMPLEMENTERT:
<li><a href="<? print $_lib['sess']->dispatch ?>t=fileupload.list">Fileupload</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=log.list">Log</a>

<li><a href="<? print $_lib['sess']->dispatch ?>t=misc.list">Misc</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=rules.list">Rules</a>
<li><a href="<? print $_lib['sess']->dispatch ?>t=signatures.list">Signatures</a>

<li><a href="<? print $_lib['sess']->dispatch ?>t=templaterestrictioons">Templaterestrictions</a>

<li><a href="<? print $_lib['sess']->dispatch ?>t=conditions.index">Conditions</a>
</ol>
</body>
</html>
