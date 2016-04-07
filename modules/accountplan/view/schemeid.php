<?php
/* Integration against fakturabank scheme ids. Requires that $AccountPlanID is already defined */

includelogic("accountplan/scheme");

$schemeControl = new lodo_accountplan_scheme($AccountPlanID);

$schemes = $schemeControl->listSchemes();


$availableSchemeTypes = $schemeControl->listTypes();
function createSchemeOptions($selected, $country) {
    global $availableSchemeTypes;
    $schemeTypeOptions = "<option value='0'></option>";

    foreach($availableSchemeTypes as $type) {
        $tmp_str = explode(':', $type['SchemeType']);
        $scheme_type_country = $tmp_str[0];
        $schemeTypeOptions .= 
            sprintf("<option value='%d' class='%s' %s>%s</option>\n",
                    $type['FakturabankSchemeID'],
                    ($country == $scheme_type_country || $scheme_type_country == 'FAKTURABANK' ? "active" : "inactive"),
                    ($type['FakturabankSchemeID'] == $selected ? "selected" : ""),
                    $type['SchemeType']
                );
    }

    return $schemeTypeOptions;
}
?>
<style>
option.inactive {
  display: none;
}
</style>
<script>
function filterSchemesByCountryCode(country) {
  var selected_country_code = country.value;
  var ap_schemeid = country.id.split('.')[2];
  var ap_scheme_fbschemeid_select = document.getElementById('accountplanscheme.FakturabankSchemeID.'+ap_schemeid);
  options = ap_scheme_fbschemeid_select.options;
  for(i = 0; i < options.length; i++) {
    if (options[i].text.match(selected_country_code) || options[i].text.match('FAKTURABANK')) {
      options[i].setAttribute("class", "active");
    } else {
      options[i].setAttribute("class", "inactive");
    }
  }
  console.log(country.value);
}
</script>
<tr class="result">
  <th colspan="5">Fakturabank Firma ID</th>
</tr>

<? foreach($schemes as $scheme) { ?>
<tr>
  <td class="menu">
  </td>
  <td colspan=2>
    <? print $_lib['form3']->Country_menu3(array('table'=>'accountplanscheme', 'field'=>'CountryCode', 'pk' => $scheme['AccountPlanSchemeID'], 'value'=>$scheme['CountryCode'], 'OnChange' => 'filterSchemesByCountryCode(this)')); ?>
  </td>
  <td>
    <select id="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>" name="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>">
      <?= createSchemeOptions($scheme['FakturabankSchemeID'], $scheme['CountryCode']) ?>
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


