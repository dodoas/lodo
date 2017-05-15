<?php
  global $salary_virksomhet_array;
  global $workrelation_virksomhet_array;
  global $virksomhet_names;
  global $tax_zone;
  global $tax_municipality_name;
  global $tax_percent;
?>
<script type="text/javascript">
  var salary_virksomhet_array = <?= json_encode($salary_virksomhet_array); ?>;
  var work_relation_virksomhet_array = <?= json_encode($workrelation_virksomhet_array); ?>;
  var salary_selected = {};
  var work_relations_selected = {};
  var virksomhet_names = <?= json_encode($virksomhet_names); ?>;
  var virksomhet_otp_amounts = [];
  for(var virksomhet_id in virksomhet_names) {
    virksomhet_otp_amounts[virksomhet_id] = 0;
  }

  function updateSelectedArrays(target_array, element, id) {
    if (element.checked) {
      target_array[id] = true;
    } else {
      delete target_array[id];
    }
    generateOTPInputsFromSelectedSalariesAndWorkRelations();
  }

  function updateSelectedWorkRelation(work_relation_checkbox_element, work_relation_id) {
    updateSelectedArrays(work_relations_selected, work_relation_checkbox_element, work_relation_id);
  }

  function updateSelectedSalary(salary_checkbox_element, salary_id) {
    updateSelectedArrays(salary_selected, salary_checkbox_element, salary_id);
  }

  function updateVirksomhetOTPAmount(amount_element, virksomhet_id) {
    var amount = toNumber(amount_element.value);
    virksomhet_otp_amounts[virksomhet_id] = amount;
    amount_element.value = toAmountString(amount);
  }

  function generateOTPInputsFromSelectedSalariesAndWorkRelations() {
    var otp_form_part_html = "<span>placeholder_name OTP:</span><br/><span>Sone: <? print $tax_zone . " ($tax_municipality_name)"; ?></span><br/><span>Prosent: <? print $_lib['format']->Amount($tax_percent); ?>%</span><br/><span>Bel&oslash;p: </span><input type=\"text\" name=\"altinnReport1_pensionAmount[placeholder_id]\" value=\"placeholder_amount\" OnChange=\"updateVirksomhetOTPAmount(this, placeholder_id);\"><br/><br/>";

    var otp_div_element = document.getElementById('otp');
    otp_div_element.innerHTML = "";
    var used_virksomhets = {};
    for(var salary_id in salary_selected) {
      virksomhet_id = salary_virksomhet_array[salary_id];
      if (salary_selected[salary_id]) {
        used_virksomhets[virksomhet_id] = true;
      }
    }
    for(var work_relation_id in work_relations_selected) {
      virksomhet_id = work_relation_virksomhet_array[work_relation_id];
      if (work_relations_selected[work_relation_id]) {
        used_virksomhets[virksomhet_id] = true;
      }
    }
    for(var virksomhet_id in used_virksomhets) {
      if (used_virksomhets[virksomhet_id]) {
        otp_div_element.innerHTML +=
          otp_form_part_html
          .replace(/placeholder_id/g, virksomhet_id)
          .replace(/placeholder_name/g, virksomhet_names[virksomhet_id])
          .replace(/placeholder_amount/g, toAmountString(virksomhet_otp_amounts[virksomhet_id]));
      }
    }
    if (otp_div_element.innerHTML == "") {
      otp_div_element.innerHTML = "<span>Ingen l&oslash;nnsslipper/ansatte valgt.</span><br/>";
    }
  }
</script>
