<?php
/* Integration against fakturabank scheme ids. Requires that $AccountPlanID is already defined */

includelogic("accountplan/scheme");

$schemeControl = new lodo_accountplan_scheme($AccountPlanID);
$schemes = $schemeControl->listSchemes();

$availableSchemeTypes = $schemeControl->listTypes();
function createSchemeOptions($selected) {
    global $availableSchemeTypes;
    $schemeTypeOptions = "<option value='0'></option>";

    foreach($availableSchemeTypes as $type) {
        $schemeTypeOptions .= 
            sprintf("<option value='%d' %s>%s</option>\n",
                    $type['FakturabankSchemeID'],
                    ($type['FakturabankSchemeID'] == $selected ? "selected" : ""),
                    $type['SchemeType']
                );
    }

    return $schemeTypeOptions;
}
?>

<?

// Dette er enda under utvikling, men siden det ser ut til at det blir en stund til
// jeg skal jobbe igjen så legger jeg det bare ut.
// Denne filen blir akkurat nå bare inkludert i reskontro.php 
/*
<tr class="result">
  <th colspan="5">Fakturabank Scheme ID</th>
</tr>

<? foreach($schemes as $scheme) { ?>
<tr>
  <td class="menu"></td>
  <td><input type="checkbox" name="schemeid_to_delete[]" value="<?= $scheme['AccountPlanSchemeID'] ?>" /></td>
  <td>
    <select name="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>">
      <?= createSchemeOptions($scheme['FakturabankSchemeID']) ?>
    </select>
    <input type="text" value="<?= $scheme['SchemeValue'] ?>" name="accountplanscheme.SchemeValue.<?= $scheme['AccountPlanSchemeID'] ?>" />
  </td>
</tr>
<? } ?>
<tr>
  <td class="menu"></td>
  <td><input type="submit" name="action_del_scheme" value="-" /></td>
  <td>
    <input type="submit" name="action_add_scheme" value="+" />
    <input type="submit" name="action_save_scheme" value="Lagre scheme id" />
  </td>
</tr>
*/

?>