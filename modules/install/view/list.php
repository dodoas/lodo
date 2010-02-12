<?
# $Id: list.php,v 1.22 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$inst     = $_REQUEST['inst'];
$inst_old = $_REQUEST['inst_old'];
$inst_new = $_REQUEST['inst_new'];

includelogic('install/install');
require_once "record.inc";

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - project list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.22 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<br />

<? print $_lib['message']->get(); ?>

<table class="lodo_data">
<thead>
  <tr>
    <th colspan="11">Velkommen til vedlikehold av regnskapskunder.</th>
  </tr>
  <tr>
    <td colspan="11"><a href="<? print $_lib['sess']->dispatch ?>action_install_new=1&amp;t=install.edit&amp;InstallationID=<? print $row->InstallationID ?>&type=installation">installer ny kunde</a></td>
  </tr>
  <tr>
    <td colspan="11">
        F&oslash;rst m&aring; du velge hvilken regnskapspakke du vil bruke som grunnlag for installasjonen, s&aring; f&aring;r du sp&oslash;rsm&aring;l om hvilke tabeller du vil kopiere<br>
        (kontoplan, momsoppsett, arbeidsgiveravgift, firma, brukere, valuta, periode, prosjekt, avdeling).
     </td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <th>Aktiv</th>
    <th>Database</th>
    <th>Kundenavn</th>
    <th>Versjon</th>
    <th>By</th>
    <th>Kontaktperson</th>
    <th>Forhandler</th>
    <th>Forhandler email</th>
    <th>Opprettet</th>
    <th>Innstallert</th>

    <th>Slett alt</th>
  </tr>
</thead>

<tbody>
<?
//$query = "select * from installation";
//$result = $_lib['dbmain']->db_query($query);

$maindb['db']       = $_SETUP['LODO_DEFAULT_INSTALL_DB'];
$maindb['host']     = $_SETUP['LODO_DEFAULT_INSTALL_SERVER'];
$maindb['username'] = $_SETUP['LODO_DEFAULT_INSTALL_USER'];
$maindb['password'] = $_SETUP['LODO_DEFAULT_INSTALL_PASSWORD'];

$_lib['maindb']     = new db_mysql(array('host' => $maindb['host'], 'database' => $maindb['db'], 'username' => $maindb['username'], 'password' => $maindb['password'], '_sess' => $_sess));

$query_show = "select * from installation";
$result1    = $_lib['maindb']->db_query($query_show);
$i = 0;
while ($row = $_lib['maindb']->db_fetch_object($result1))
{

  ?>
  <tr <? if($row->Active ==0) { print "class=\"red\""; } ?>>
   <td><? print $row->Active?></td>
   <td><a href="<? print $_lib['sess']->dispatch ?>t=install.edit&amp;InstallationID=<? print $row->InstallationID ?><? global $_SETUP; $p_file = $_SETUP['HOME_DIR'] . "/code/lib/setup/prefs_" . $row->InstallName . ".inc"; if (!is_file($p_file)) print "&type=installation"; ?>"><? print $row->InstallName ?></a></td>
   <td><a href="<? print $_lib['sess']->dispatch ?>t=install.edit&amp;InstallationID=<? print $row->InstallationID ?><? global $_SETUP; $p_file = $_SETUP['HOME_DIR'] . "/code/lib/setup/prefs_" . $row->InstallName . ".inc"; if (!is_file($p_file)) print "&type=installation"; ?>"><? print $row->VName?></a></td>
   <td><? print $row->Version ?></td>
   <td><? print $row->VCity ?></td>
   <td><? print $row->FirstName ?> <? print $row->LastName ?></td>
   <td><? print $row->DealerName ?></td>
   <td><? print $row->DealerEmail ?></td>
   <td><? print $row->CreatedDateTime ?></td>
   <td><? global $_SETUP; $p_file = $_SETUP['HOME_DIR'] . "/conf/prefs_" . $row->InstallName . ".inc"; if (is_file($p_file)) print "Installert"; else print "Prefs ikke installert"; ?></td>
   <td><a href="<? print $MY_SELF ?>&amp;action_install_drop=1&amp;inst=<? print $row->InstallName ?>">Slett kunde</a></td>
  <?
    $i++;
}
?>
<tfoot>
    <tr height="20">
        <td colspan="5">
        </td>
    <tr>
        <td colspan="5">
            Antall installasjoner: <? print $i ?>
        </td>
    </tr>
</tbody>
</table>
</body>
</html>