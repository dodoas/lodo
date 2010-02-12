<?
global $_dsn, $_SETUP, $_dbh;

$db_table1 = "filarkiv";
$db_table2 = "filkategori";
$id_ref = $db_table1 . "ID";

$limitSet = $_REQUEST['limit'];
$limitSet = 1;

if(!$CompanyID) { $CompanyID = 1; }

?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Lodo - Filarkiv</title>
    <? includeinc('head') ?>
</head>

<body>
<?
includeinc('top');
includeinc('left');

require_once $_SETUP['HOME_DIR']."/modules/filarkiv/view/lagre_fil.php";

$select1 = "select * from " . $db_table1 . " where " . $db_table1 . "ID = '" . $_REQUEST[$id_ref] . "';";
$select2 = "select * from " . $db_table2 . ";";

if ($_REQUEST[$id_ref] != "")
$row= $_dbh[$_dsn]->get_row(array('query' => $select1));

?>
<h2><?php if ($_REQUEST[$id_ref] == "") print "Ny"; else print "Endre"; ?> fil</h2>

<form name="mappe" action="<? print $MY_SELF ?>" method="post" enctype="multipart/form-data">
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="2">Fil</th>
  </tr>
</thead>
<tbody>
        <input type="hidden" name="filarkiv.filarkivID" value="<?php print $row->filarkivID; ?>"/>
    <tr class="<? print "$sec_color"; ?>">
        <td>Mappe:</td>
        <td>
            <select name="filarkiv.filkategoriID">
<?
    $result2= $_dbh[$_dsn]->db_query($select2);
    while($row2 = $_dbh[$_dsn]->db_fetch_object($result2))
    {
?>
                <option value="<?php print $row2->filkategoriID; ?>"<?php if ($row2->filkategoriID == $row->filkategoriID) print " selected"; ?>><?php print $row2->navn; ?></option>
<?php
    }
?>
            </select>
        </td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Navn:</td>
        <td><input type="text" name="filarkiv.navn" value="<?php print $row->navn; ?>" size="70" maxlength="250"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Beskrivelse:</td>
        <td><input type="text" name="filarkiv.beskrivelse" value="<?php print $row->beskrivelse; ?>" size="70" maxlength="250"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Fil:</td>
        <td><input type="file" name="fildata" size="70"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Tilgjengelig fra (dato):</td>
        <td><input type="text" name="filarkiv.tilgjengeligFra" value="<?php if ($_REQUEST[$id_ref] != "") print date("d.m.Y H:i:s", $row->tilgjengeligFra); else print date("d.m.Y H:i:s", time()); ?>" size="70" maxlength="250"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Tilgjengelig til (dato):</td>
        <td><input type="text" name="filarkiv.tilgjengeligTil" value="<?php if ($_REQUEST[$id_ref] != "") print date("d.m.Y H:i:s", $row->tilgjengeligTil); else print date("d.m.Y H:i:s", time() + 315360000 + 172800); ?>" size="70" maxlength="250"/></td>
    </tr>
</tbody>
<tfoot>
  <tr class="BGColorDark">

    <td align="right" colspan="2">
        <input type="submit" name="<?php if ($_REQUEST[$id_ref] == "") print "action_fil_new"; else print "action_fil_update"; ?>" value="  Lagre  "/><br/>
        Max st&oslash;rrelse p&aring; filen er 2 MB.
<?php if ($row->filkategoriID != "") { ?>
        <input type="button" name="action_fil_delete" value="  Slett  "/><br/>
<?php } ?>
        <input type="button" value="Tilbake til fil og mappeliste" name="action_mappe_avbryt" tabindex="3" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index';">
        <input type="button" value="Avbryt" name="action_fil_avbryt" tabindex="3" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index';">
    </td>

</tfoot>
</table>
</form>
</body>
</html>


