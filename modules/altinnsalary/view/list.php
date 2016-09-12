<?
require_once "record.inc";
print $_lib['sess']->doctype
?>

<head>
  <title>Empatix - Altinn lønnslipper</title>
  <? includeinc('head') ?>
  <script>
    function toggleReportDetails(id) {
      $('#report_extra_info_'+id).toggle();
      $('#report_extra_info_header_'+id).toggle();
      var name = $('#report_extra_info_button_'+id).html();
      var new_name = (name == 'Vis') ? 'Skjull' : 'Vis';
      $('#report_extra_info_button_'+id).html(new_name);
    }
  </script>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.salarylist&action_show_salaries=show">Send en ny rapport</a>

<br/><br/>

<table class="lodo_data">
  <tbody>
    <tr>
      <th class='menu'>ID</th>
      <th class='menu'>Periode</th>
      <th class='menu'>Sent kl</th>
      <th class='menu'>Rapportert</th>
      <th class='menu'>Arkivert kl</th>
      <th class='menu'>Status</th>
      <th class='menu'>Handlinger</th>
    </tr>
  <?
  $so1_query = "select * from altinnReport1 order by AltinnReport1ID";
  $so1 = $_lib['db']->db_query($so1_query);
  $report_count = 0;
  while($so1row = $_lib['db']->db_fetch_object($so1)) {
    $line_color = (!($report_count % 3)) ? "BGColorDark" : "r1";
    $salary_ids = array();
    $work_relation_ids = array();
    $report_id = $so1row->AltinnReport1ID;
  ?>
    <?
    $so2query = "SELECT * FROM altinnReport2 WHERE res_ReceiptId = ".$so1row->ReceiptId." ORDER BY res_ReceiversReference DESC , AltinnReport2ID LIMIT 1";
    $so2 = $_lib['db']->db_query($so2query);
    $so2row = $_lib['db']->db_fetch_object($so2);
    ?>
    <tr class="<? print $line_color; ?>">
      <td>
        <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show&AltinnReport1ID=<? print $report_id ?>">
          <? print $report_id; ?>
          </a>
      </td>
      <td><?print $so1row->Period; ?></td>
      <td><?print $so1row->LastChanged; ?></td>
      <td>
        <?
        $query_altin_salary = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$report_id." ORDER BY SalaryId ASC";
        $result_altin_salary  = $_lib['db']->db_query($query_altin_salary);

        $included_salaries = array();
        while($_row = $_lib['db']->db_fetch_object($result_altin_salary)){
          $salary_ids[] = $_row->SalaryId;
          $journal_id = ($_row->Changed) ? $_row->JournalID . '(endrett)' : $_row->JournalID;
          $link = $_lib['sess']->dispatch . 't=salary.edit&SalaryID=' . $_row->SalaryId;
          $included_salaries[] = array('JournalID'=>$journal_id, 'link'=>$link);

        }
        print count($included_salaries) . " l&oslash;nnslipp(er) og ";

        $query_altinn_wr = "SELECT wr.*, ap.*, sc.* FROM altinnReport1WorkRelation ar1wr JOIN workrelation wr ON wr.WorkRelationID = ar1wr.WorkRelationID JOIN accountplan ap ON ap.AccountPlanID = wr.AccountPlanID JOIN subcompany sc ON sc.SubcompanyID = wr.SubcompanyID WHERE ar1wr.AltinnReport1ID = ".$report_id." ORDER BY ap.FirstName ASC";
        $result_altinn_wr  = $_lib['db']->db_query($query_altinn_wr);

        $included_work_relations = array();
        while($_row = $_lib['db']->db_fetch_object($result_altinn_wr)){
          $work_relation_ids[] = $_row->WorkRelationID;
          $work_relation_name = $_row->WorkRelationID . ' - ' . $_row->Name . ' (' . $_row->WorkStart . ' - ' . $_row->WorkStop . ') ' . $_row->FirstName . ' ' . $_row->LastName . '(' . $_row->AccountPlanID . ')';
          $link = $_lib['sess']->dispatch . 't=accountplan.employee&accountplan_AccountPlanID=' . $_row->AccountPlanID;
          $included_work_relations[] = array('AccountPlanName'=>$work_relation_name, 'link'=>$link);
        }
        print count($included_work_relations) . " arbeidsforhold ";
        ?>
          <button id="report_extra_info_button_<? print $report_id; ?>" onclick="toggleReportDetails(<? print $report_id; ?>)">Vis</button>
      </td>
      <td>
        <?
        $so5_query = "SELECT * FROM altinnReport5 WHERE req_CorrespondenceID = '".$so2row->res_ReceiversReference."' ORDER BY AltinnReport5ID";
        $so5 = $_lib['db']->db_query($so5_query);
        $so5_row = $_lib['db']->db_fetch_object($so5);
        if (!empty($so5_row->res_LastChanged)) print $so5_row->res_LastChanged;
        else {
        ?>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <input type="hidden" name="request_type" value='archive'>
          <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap5',
            'value'=>'Arkiver',
            'disabled' => $so2row->res_ReceiversReference ? false : true
          )) ?>
        </form>
        <?
        }
        ?>
      </td>
      <td>
        <?
          if ($so2row && !empty($so2row->res_ReceiversReference)) print 'Mottatt med referanse ' . $so2row->res_ReceiversReference;
          elseif (strstr($so2row->res_ReceiptStatus, 'OK')) print 'Prosseseres';
          else print $so2row->res_ReceiptStatus;
        ?>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <? print $_lib['form3']->hidden(array('name'=>'receiptId', 'value'=>$so1row->ReceiptId)) ?>
          <? print $_lib['form3']->submit(array('name'=>'action_soap2', 'value'=>'Sjekk status')) ?>
        </form>
      </td>
      <td>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <input type="hidden" name="altinnReport1.periode" value='<?print $so1row->Period; ?>'>
          <input type="hidden" name="altinnReport1.MeldingsId" value='<?print $so1row->MeldingsId; ?>'>
          <? foreach($salary_ids as $salary_id) { ?>
            <input type="hidden" name="use_salary[<? print $salary_id; ?>]" value='1'>
          <? } ?>
          <? foreach($work_relation_ids as $work_relation_id) { ?>
            <input type="hidden" name="use_work_relation[<? print $work_relation_id; ?>]" value='1'>
          <? } ?>
          <input type="hidden" name="altinnReport1.ExternalShipmentReference" value='<?print 'LODO' . time(); ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap1',
            'value'=>'Send p&aring; nytt',
            'disabled' => false
          )); ?>
        </form>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4" method="post">
          <input type="hidden" name="request_type" value='feedback'>
          <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap4',
            'value'=>'Hent tilbakemelding',
            'disabled' => $so2row->res_ReceiversReference ? false : true
            )) ?>
        </form>
        <?
          $so4query = "SELECT ar4.* FROM altinnReport1 ar1 JOIN altinnReport2 ar2 ON ar1.ReceiptId = ar2.res_ReceiptId JOIN altinnReport4 ar4 ON ar2.res_ReceiversReference = ar4.req_CorrespondenceID WHERE ar1.ReceiptId = " . $so1row->ReceiptId . " ORDER BY AltinnReport4ID DESC LIMIT 1";
          $so4 = $_lib['db']->db_query($so4query);
          $so4row = $_lib['db']->db_fetch_object($so4);
          if ($so4row) {
        ?>
        <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4&AltinnReport4ID=<? print $so4row->AltinnReport4ID ?>">Se tilbakemelding</a>
        <? } ?>
      </td>
    </tr>
    <tr id="report_extra_info_header_<? print $report_id; ?>" class="r0" style="display: none">
      <?
        $salary_detail_columns = 4;
        $employee_detail_columns = 3;
      ?>
      <td class="menu" colspan="<? print $salary_detail_columns; ?>">L&oslash;nnslipper</td>
      <td class="menu" colspan="<? print $employee_detail_columns; ?>">Arbeidsforhold</td>
    </tr>
    <tr id="report_extra_info_<? print $report_id; ?>" class="<? print $line_color; ?>" style="display: none">
      <td>
        <?
        if (!empty($included_salaries)) {
          $salary_print_count = 1;
          $new_column_every = ceil(count($included_salaries)/$salary_detail_columns);
          foreach($included_salaries as $included_salary) {
        ?>
          L <a href="<? print $included_salary['link']; ?>"><? print $included_salary['JournalID']; ?></a><br/>
        <?
            if (!($salary_print_count % $new_column_every)) {
              echo '</td><td>';
            }
            $salary_print_count++;
          }
        }
        ?>
      </td>
      <td>
        <?
        if (!empty($included_work_relations)) {
          $employee_print_count = 1;
          $new_column_every = ceil(count($included_work_relations)/$employee_detail_columns);
          foreach($included_work_relations as $included_employee) {
        ?>
          <a href="<? print $included_employee['link']; ?>"><? print $included_employee['AccountPlanName']; ?></a><br/>
        <?
            if (!($employee_print_count % $new_column_every)) {
              echo '</td><td>';
            }
            $employee_print_count++;
          }
        }
        ?>
      </td>
      <td colspan='3'></td>
    </tr>
    <?
      $report_count++;
    }
    ?>
</table>
<br/>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.log">Altinn log</a>

</body>
</html>
