<?
global $_dsn, $_SETUP, $_dbh;

$db_table1 = "filkategori";
$id_ref = $db_table1 . "ID";

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

require_once $_SETUP['HOME_DIR']."/modules/filarkiv/view/lagre_mappe.php";

/* S�kestreng */
$select1 = "select * from " . $db_table1 . " where " . $db_table1 . "ID = '" . $_REQUEST[$id_ref] . "';";
print $select1;

if ($_REQUEST[$id_ref] != "")
$row= $_dbh[$_dsn]->get_row(array('query' => $select1));

?>
<h2><?php if ($_REQUEST[$id_ref] == "") print "Ny"; else print "Endre"; ?> mappe</h2>

<form name="mappe" action="<? print $MY_SELF ?>" method="post">
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="2">Mappe</th>
  </tr>
</thead>
<tbody>
    <tr class="<? print "$sec_color"; ?>">
        <input type="hidden" name="filkategori.filkategoriID" value="<?php print $row->filkategoriID; ?>"/>
        <input type="hidden" name="filkategori.ts_created" value="<?php print $row->ts_created; ?>"/>
        <td>Mappenavn:</td>
        <td><input type="text" name="filkategori.navn" value="<?php print $row->navn; ?>" size="70" maxlength="250"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Beskrivelse:</td>
        <td><input type="text" name="filkategori.beskrivelse" value="<?php print $row->beskrivelse; ?>" size="70" maxlength="250"/></td>
    </tr>
    <tr class="<? print "$sec_color"; ?>">
        <td>Sist endret av:</td>
        <td><input type="text" name="filkategori.modified_by" disabled value="<?php print $row->modified_by; ?>" size="70" maxlength="250"/></td>
    </tr>
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="2">
        <input type="submit" name="<?php if ($_REQUEST[$id_ref] == "") print "action_mappe_new"; else print "action_mappe_update"; ?>" value="  Lagre  "/>
<?php if ($row->filkategoriID != "") { ?>
        <input type="submit" name="action_fil_delete" value="  Slett  "/><br/>
<?php } ?>
        <input type="button" value="Avbryt" name="action_mappe_avbryt" tabindex="3" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index';">
        <input type="button" value="Tilbake til fil og mappeliste" name="action_mappe_avbryt" tabindex="3" onClick="document.location='<? print $_SETUP['DISPATCH'] ?>t=filarkiv.index';">
    </td>

</tfoot>
</table>
</form>
</body>
</html>


