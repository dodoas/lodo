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
            sprintf("<option value='%d' %s %s>%s</option>\n",
                    $type['FakturabankSchemeID'],
                    ($type['FakturabankSchemeID'] == $selected ? "selected" : ""),
                    ($country == $scheme_type_country || $type['FakturabankSchemeID'] == $selected ? "" : "hidden"),
                    $type['SchemeType']
                );
    }

    return $schemeTypeOptions;
}
?>
<script>
function filterSchemesByCountryCode(country) {
  var selected_country_code = country.value;
  var ap_schemeid = country.id.split('.')[2];
  var ap_scheme_fbschemeid_select = document.getElementById('accountplanscheme.FakturabankSchemeID.'+ap_schemeid);
  options = ap_scheme_fbschemeid_select.options;
  for(i = 0; i < options.length; i++) {
    if (options[i].text.match(selected_country_code) || options[i].selected) {
      options[i].hidden = false;
    } else {
      options[i].hidden = true;
    }
  }
}
function fixNoSupAccntRe(select_element) {
  var input = $(select_element).next("input");
  if($(select_element).find("option:selected").text() == "NO:SUP-ACCNT-RE") {
    input.val(<? print $AccountPlanID; ?>);
    input.attr("disabled", "disabled");
  } else {
    input.removeAttr("disabled");
  }
}
</script>
<tr class="result">
  <th colspan="6">Fakturabank Firma ID</th>
</tr>

<? foreach($schemes as $scheme) { ?>
<tr>
  <td class="menu">
  </td>
  <td colspan=2>
    <? print $_lib['form3']->Country_menu3(array('table'=>'accountplanscheme', 'field'=>'CountryCode', 'pk' => $scheme['AccountPlanSchemeID'], 'value'=>$scheme['CountryCode'], 'OnChange' => 'filterSchemesByCountryCode(this)')); ?>
  </td>
  <td>
    <select id="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>" name="accountplanscheme.FakturabankSchemeID.<?= $scheme['AccountPlanSchemeID'] ?>" onchange="fixNoSupAccntRe(this);">
      <?= createSchemeOptions($scheme['FakturabankSchemeID'], $scheme['CountryCode']) ?>
    </select>
    <input type="text" value="<?= $scheme['SchemeValue'] ?>" name="accountplanscheme.SchemeValue.<?= $scheme['AccountPlanSchemeID'] ?>" <?= $availableSchemeTypes[$scheme["FakturabankSchemeID"]]["SchemeType"] == "NO:SUP-ACCNT-RE" ? "disabled='disabled'" : "" ?> />
  </td>
  <td></td>
  <td><input type="checkbox" name="schemeid_to_delete[]" value="<?= $scheme['AccountPlanSchemeID'] ?>" /></td>
</tr>
<? } ?>
<tr>
  <td class="menu"></td>
  <td colspan="2">
    <input type="submit" name="action_refresh_sheme" value="Hent ID fra fakturaBank" />
    <input type="submit" name="action_add_scheme" value="Ny firma ID" />
  </td>
  <td>
  </td>
  <td>
    <? if($_lib['sess']->get_person('AccessLevel') > 1) {?>
      <input type="submit" name="action_save_scheme" value="Lagre firma ID" />
    <? } ?>
  </td>
  <td>
    <? if($_lib['sess']->get_person('AccessLevel') > 1) {?>
      <input type="submit" name="action_del_scheme" value="Slett markerte" onclick="return confirm('Er du sikker p&aring; at du vil slette markerte?');" />
    <? } ?>
  </td>
</tr>


