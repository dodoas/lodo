<?
global $_dsn, $_SETUP, $_dbh;

$db_table1 = "filkategori";
$db_table2 = "filarkiv";
$myAccessLevel = 3;

$limitSet = $_REQUEST['limit'];
$limitSet = 1;

if(!$CompanyID) { $CompanyID = 1; }

/* S�kestreng */
$select1 = "select * from " . $db_table1 . ";";
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Lodo - Filarkiv</title>
    <? includeinc('head') ?>
<SCRIPT LANGUAGE="JavaScript1.1" type="text/javascript">
<!--
var maxFolders;
function toggleMenuShow(a, b)
{
    if (document.getElementById)
    {
        if (document.getElementById(a))
        {
            document.getElementById(a).style.display=(document.getElementById(a).style.display=='none')?'':'none';
            SetCookie(a, document.getElementById(a).style.display);
        }
        if (document.getElementById(b))
        {
            document.getElementById(b).src='/img/filarkiv/'+((document.getElementById(a).style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
    }
    else if (document.all)
    {
        if (document.all[a])
        {
            document.all[a].style.display=(document.all[a].style.display=='none')?'':'none';
            SetCookie(a, document.all[a].style.display);
        }
        if (document.all[b])
        {
            document.all[b].src='/img/filarkiv/'+((document.all[a].style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
    }
    else if (document.layers)
    {
        if (document.layers[a])
        {
            document.layers[a].style.display=(document.layers[a].style.display=='none')?'':'none';
            SetCookie(a, document.layers[a].style.display);
        }
        if (document.layers[b])
        {
            document.layers[b].src='/img/filarkiv/'+((document.layers[a].style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
    }
}
function restoreSettings()
{
    var name;
    for (i = 1; i < maxFolders + 1; i++)
    {
        name = 'foldersLayer' + i;
        name2 = 'mappe' + i;

        if (document.getElementById)
        {
            if (document.getElementById(name))
                document.getElementById(name).style.display = GetCookie(name);
            if (document.getElementById(name2))
                document.getElementById(name2).src='/img/filarkiv/'+((document.getElementById(name).style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
        else if (document.all)
        {
            if (document.all[name])
                document.all[name].style.display = GetCookie(name);
            if (document.all[name2])
                document.all[name2].src='/img/filarkiv/'+((document.all[name].style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
        else if (document.layers)
        {
            if (document.layers[name])
                document.layers[name].style.display = GetCookie(name);
            if (document.layers[name2])
                document.layers[name2].src='/img/filarkiv/'+((document.layers[name].style.display=='none')?'mappe':'mappeopen')+'.gif';
        }
    }
}
function Slett(navn, url)
{
    if (confirm('Slette ' + navn + '?'))
    {
        document.location = url;
    }
}
function DelCookie(sName)
{
  document.cookie = sName + "=" + escape(sValue) + "; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}
function GetCookie(sName)
{
  // cookies are separated by semicolons
  var aCookie = document.cookie.split("; ");
  for (var i=0; i < aCookie.length; i++)
  {
    // a name/value pair (a crumb) is separated by an equal sign
    var aCrumb = aCookie[i].split("=");
    if (sName == aCrumb[0])
      return unescape(aCrumb[1]);
  }

  // a cookie with the requested name does not exist
  return null;
}
function SetCookie(sName, sValue)
{
  futdate = new Date();
  var futdate = new Date();
  var expdate = futdate.getTime();
  expdate += 3600*1000 //expires in 1 hour(milliseconds)
  futdate.setTime(expdate);
  document.cookie = sName + "=" + escape(sValue) + "; expires=" + futdate.toGMTString();
}
-->
</script>
</head>

<body onLoad="restoreSettings();">
<?
includeinc('top');
includeinc('left');

// $row_c = $_dbh[$_dsn]->get_row(array('query' => $select1));
?>
<h2>Filarkiv</h2>
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="10">Arkiv</th>
  <tr>
    <th class="menu">&nbsp;</th>
    <th class="menu">Mappe navn</th>
    <th class="menu">Navn</th>
    <th class="menu">Beskrivelse</th>
    <th class="menu">St&oslash;rrelse</th>
    <th class="menu">Opprettet</th>
    <th class="menu">Sist endret</th>
    <th class="menu">Sist oppdatert av</th>
<?php
if ($_lib['sess']->get_person('AccessLevel') >= $myAccessLevel)
{
?>
    <th class="menu">&nbsp;</th>
    <th class="menu">&nbsp;</th>
<?php
}
?>
  </tr>
</thead>
<?
$myMaxFolders = 0;
$result1= $_dbh[$_dsn]->db_query($select1);
while($row1 = $_dbh[$_dsn]->db_fetch_object($result1))
{
$select2 = "select * from " . $db_table2 . " where filkategoriID = '" . $row1->filkategoriID . "'";
if ($_REQUEST["visAlleFiler"] != "1")
    $select2 .= " AND tilgjengeligFra < " . time() . " AND tilgjengeligTil > " . time();
$select2 .= ";";
// print $select2;
$myMaxFolders++;
?>
      <tr class="<? print "$sec_color"; ?>">
          <td><img src="/img/filarkiv/mappe.gif" width="24" height="22" id="mappe<? print $myMaxFolders; ?>"></td>
          <td><a href="javascript:toggleMenuShow('foldersLayer<? print $myMaxFolders; ?>', 'mappe<? print $myMaxFolders; ?>');"><? print $row1->navn; ?></a></td>
          <td>&nbsp;</td>
          <td><? print $row1->beskrivelse; ?></td>
          <td>&nbsp;</td>
          <td><? print date("d.m.Y", $row1->ts_created); ?></td>
          <td><? print date("d.m.Y", $row1->ts_modified); ?></td>
          <td><? print $row1->modified_by; ?></td>
<?php
if ($_lib['sess']->get_person('AccessLevel') >= $myAccessLevel)
{
?>
          <td><a href="<? print $_SETUP['DISPATCH'] ?>t=filarkiv.mappe&filkategoriID=<? print $row1->filkategoriID; ?>">Endre</a></td>
          <td><a href="javascript:Slett('<? print $row1->navn; ?>', '<? print $_SETUP['DISPATCH'] ?>t=filarkiv.mappe&filkategoriID=<? print $row1->filkategoriID; ?>&action_mappe_delete=1');">Slett</a></td>
<?php
}
?>
    </tr>
<tbody id="foldersLayer<? print $myMaxFolders; ?>" style="display:none;">
<?
    $result2= $_dbh[$_dsn]->db_query($select2);
    while($row2 = $_dbh[$_dsn]->db_fetch_object($result2))
    {
?>
     <tr class="<? print "$sec_color"; ?>">
          <td>&nbsp;&nbsp;&nbsp;<img src="/img/filarkiv/file_icon.gif" width="16" height="16" name="fil"></td>
          <td>&nbsp;</td>
          <td><a href="<? print $_SETUP['DISPATCH'] ?>t=filarkiv.vis_fil&filarkivID=<? print $row2->filarkivID; ?>"><? print $row2->navn; ?></a></td>
          <td><? print $row2->beskrivelse; ?></td>
          <td><? print number_format($row2->size, 0, '', ' '); ?></td>
          <td><? print date("d.m.Y", $row2->ts_created); ?></td>
          <td><? print date("d.m.Y", $row2->ts_modified); ?></td>
          <td><? print $row2->modified_by; ?></td>
<?php
if ($_lib['sess']->get_person('AccessLevel') >= $myAccessLevel)
{
?>
          <td><a href="<? print $_SETUP['DISPATCH'] ?>t=filarkiv.fil&filarkivID=<? print $row2->filarkivID; ?>">Endre</a></td>
          <td><a href="javascript:Slett('<? print $row2->navn; ?>', '<? print $_SETUP['DISPATCH'] ?>t=filarkiv.fil&filarkivID=<? print $row2->filarkivID; ?>&action_fil_delete=1');">Slett</a></td>
<?php
}
?>
    </tr>
<?
    }
?>
</tbody>
<?
}
?>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="10">
        <!-- <a href="<? print $_SETUP['DISPATCH'] ?>t=borettslag.leilighet&new=1&">Ny leilighet</a> -->
<?php
if ($_lib['sess']->get_person('AccessLevel') >= $myAccessLevel)
{
    if ($_REQUEST["visAlleFiler"] != "1")
    {
?>
        <input type="button" value="Vis skjulte filer" name="action_fil_vis_alle" tabindex="1" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index&visAlleFiler=1';">
<?php
    }
    else
    {
?>
        <input type="button" value="Skjul skjulte filer" name="action_fil_skjul_skjulte" tabindex="1" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index&visAlleFiler=0';">
<?php
    }
?>
        <input type="button" value="Ny mappe" name="action_mappe_new" tabindex="2" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.mappe';">
        <input type="button" value="Ny fil" name="action_fil_new" tabindex="3" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.fil';">
<?php
}
?>
    </td>

</tfoot>
</table>
<SCRIPT LANGUAGE="JavaScript1.1" type="text/javascript">
<!--
    maxFolders = <? print $myMaxFolders; ?>;

-->
</script>
</body>
</html>


