<?php
/* Integration against fakturabank scheme ids. Requires that $AccountPlanID is already defined */

includelogic("accountplan/scheme");

$schemeControl = new lodo_accountplan_scheme($AccountPlanID);
if($_lib['input']->getProperty('action_refresh_sheme')) {
    $schemeControl->refreshSchemes();
}

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

<tr class="result">
  <th colspan="5">Fakturabank Firma ID</th>
</tr>

<? foreach($schemes as $scheme) { ?>
<tr>
  <td class="menu">
  </td>
  <td colspan=2>
    <select name="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>">
      <?= createSchemeOptions($scheme['FakturabankSchemeID']) ?>
    </select>
    <input type="text" value="<?= $scheme['SchemeValue'] ?>" name="accountplanscheme.SchemeValue.<?= $scheme['AccountPlanSchemeID'] ?>" />
  </td>
  <td><input type="checkbox" name="schemeid_to_delete[]" value="<?= $scheme['AccountPlanSchemeID'] ?>" /></td>
</tr>
<? } ?>
<tr>
  <td class="menu"></td>
  <td colspan="2">
    <input type="submit" name="action_save_scheme" value="Lagre firma id" />
    <input type="submit" name="action_add_scheme" value="Legg til ny" />
  </td>
  <td>
    <input type="submit" name="action_del_scheme" value="Slett markerte" onclick="return confirm('Er du sikker p&aring; at du vil slette markerte?');" />
    <input type="submit" name="action_refresh_sheme" value="Oppdater schemetyper" />
  </td>
</tr>


