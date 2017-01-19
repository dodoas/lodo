<?
require_once "record.inc";
print $_lib['sess']->doctype
?>

<head>
  <title>Empatix - Altinn lønnslipper</title>
  <? includeinc('head') ?>
  <style>
    tr.highlighted td {
      background-color: rgba(255, 182, 0, 0.6) !important;
    }
  </style>
  <script>
    function toggleReportDetails(id) {
      $('#report_extra_info_'+id).toggle();
      $('#report_extra_info_header_'+id).toggle();
      var name = $('#report_extra_info_button_'+id).html();
      var new_name = (name == 'Vis') ? 'Skjul' : 'Vis';
      $('#report_extra_info_button_'+id).html(new_name);
    }

    function submitSoapForm(submitButton) {
      form = submitButton.parentElement.parentElement.getElementsByClassName("soap_form")[0];
      $(submitButton.cloneNode(true)).insertAfter($(submitButton));
      form.appendChild(submitButton);
      // form submits automatically upon appending this input
    }

    $(document).ready(function() {
      $('.navigate.to').click(function(e) {
        var element = $(e.target);
        var targetID = element.attr('id');
        highlight('#row_' + targetID);
      });

      function highlight(elementid){
        $(elementid).addClass("highlighted");
        setTimeout(function() { 
          $(elementid).removeClass("highlighted");
        } , 2500);
      }
    });
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
      <th class='menu align-right'>OTP Bel&oslash;p</th>
      <th class='menu'>Arkivert kl</th>
      <th class='menu'>Status</th>
      <th class='menu'>Handlinger</th>
      <th class='menu'></th>
      <th class='menu'></th>
      <th class='menu'></th>
      <th class='menu'></th>
      <th class='menu'></th>
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
    <tr id="row_<? print $report_id ?>" class="<? print $line_color; ?>">
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
      <td class="number"><? print $_lib['format']->Amount($so1row->PensionAmount); ?></td>
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
        <form class="soap_form" style="display: none;" name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <input type="hidden" name="altinnReport1.periode" value='<?print $so1row->Period; ?>'>
          <input type="hidden" name="altinnReport1.MeldingsId" value='<?print $so1row->MeldingsId; ?>'>
          <? foreach($salary_ids as $salary_id) { ?>
            <input type="hidden" name="use_salary[<? print $salary_id; ?>]" value='1'>
          <? } ?>
          <? foreach($work_relation_ids as $work_relation_id) { ?>
            <input type="hidden" name="use_work_relation[<? print $work_relation_id; ?>]" value='1'>
          <? } ?>
          <input type="hidden" name="altinnReport1.ExternalShipmentReference" value='<?print 'LODO' . time(); ?>'>
          <input type="hidden" name="altinnReport1.pensionAmount" value='<?print $so1row->PensionAmount; ?>'>
        </form>

        <? 
          $resend_enabled = ($so1row->ReceivedStatus == "received" || ($so2row->res_ReceiversReference && empty($so1row->ReplacedByMeldindsID) && empty($so1row->ReceivedStatus))) && ($so1row->CancellationStatus != "is_cancellation" && $so1row->CancellationStatus != "cancelled" && $so1row->CancellationStatus != "pending");
          print $_lib['form3']->submit(array(
            'name'=>'action_soap1',
            'value'=>'Send p&aring; nytt',
            'disabled' => !$resend_enabled,
            'OnClick' => "submitSoapForm(this);"
          )); ?>
        <?
          $so4query = "SELECT ar4.* FROM altinnReport1 ar1 JOIN altinnReport2 ar2 ON ar1.ReceiptId = ar2.res_ReceiptId JOIN altinnReport4 ar4 ON ar2.res_ReceiversReference = ar4.req_CorrespondenceID WHERE ar1.ReceiptId = " . $so1row->ReceiptId . " ORDER BY AltinnReport4ID DESC LIMIT 1";
          $so4 = $_lib['db']->db_query($so4query);
          $so4row = $_lib['db']->db_fetch_object($so4);
          if (!$so4row) {
        ?>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4" method="post">
          <input type="hidden" name="request_type" value='feedback'>
          <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap4',
            'value'=>'Hent tilbakemelding',
            'disabled' => $so2row->res_ReceiversReference ? false : true
            )) ?>
        </form>
        <? } else { ?>
        <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4&AltinnReport4ID=<? print $so4row->AltinnReport4ID ?>">Se tilbakemelding</a>
        <? } ?>
      </td>
      <td>
        <?
          if($so1row->CancellationStatus == "cancelled") print "<b style='color: red;'>kansellert</b>";
          else {
            if($so1row->ReceivedStatus == "sent") print "sendt";
            if($so1row->ReceivedStatus == "received") print "<b style='color: green;'>mottatt</b>";
            if($so1row->ReceivedStatus == "replaced") print "<span style='color: orange;'>erstattet</span>";
            if($so1row->ReceivedStatus == "rejected") print "<b style='color: red;'>avvist</b>";
          }
        ?>
      </td>
      <td>
        <?
          if($so1row->ErstatterMeldingsId && $so1row->CancellationStatus != "is_cancellation") {
            $replaced_so1row = $_lib['db']->get_row(array("query" => "SELECT * FROM altinnReport1 WHERE MeldingsId = '". $so1row->ErstatterMeldingsId ."';"));
            print "Erstatter <span class=\"navigate to\" id=\"". $replaced_so1row->AltinnReport1ID ."\">". $replaced_so1row->AltinnReport1ID ."</span>";
          }
        ?>
      </td>
      <td> 
        <?
          if($so1row->ReplacedByMeldindsID && $so1row->ReceivedStatus == "replaced") {
            $replaced_by_so1row = $_lib['db']->get_row(array("query" => "SELECT * FROM altinnReport1 WHERE MeldingsId = '". $so1row->ReplacedByMeldindsID ."';"));
            if($replaced_by_so1row->ReceivedStatus != "rejected") {
              print "Erstattet med <span class=\"navigate to\" id=\"". $replaced_by_so1row->AltinnReport1ID ."\">". $replaced_by_so1row->AltinnReport1ID ."</span>";  
            }            
          }
        ?>
      </td>
      <td> 
        <?
          if($so1row->CancellationStatus == "is_cancellation") {
            $cancelled_so1row = $_lib['db']->get_row(array("query" => "SELECT * FROM altinnReport1 WHERE MeldingsId = '". $so1row->ErstatterMeldingsId ."';"));
            print "Kansellerer <span class=\"navigate to\" id=\"". $cancelled_so1row->AltinnReport1ID ."\">". $cancelled_so1row->AltinnReport1ID ."</span>";
          }
        ?>
      </td>
      <td>
        <?
          $cancel_enabled = $so1row->ReceivedStatus == "received" && $so1row->CancellationStatus != "cancelled" && $so1row->CancellationStatus != "is_cancellation" && $so1row->CancellationStatus != "pending";
          print $_lib['form3']->submit(array(
            'name'=>'action_soap1_cancel',
            'value'=>'Kansellere',
            'disabled' => !$cancel_enabled,
            'OnClick' => "submitSoapForm(this);"
          )); ?>
      </td>
    </tr>
    <tr>
      <td colspan="15" style="padding: 0;">
        <table class="lodo_data" style="width: 100%;">
          <tr id="report_extra_info_header_<? print $report_id; ?>" class="r0" style="display: none">
            <?
              $salary_detail_columns = 4;
              $employee_detail_columns = 2;
            ?>
            <td class="menu" colspan="<? print $salary_detail_columns; ?>">L&oslash;nnslipper</td>
            <td class="menu" colspan="<? print $employee_detail_columns; ?>">Arbeidsforhold</td>
          </tr>
          <tr id="report_extra_info_<? print $report_id; ?>" class="<? print $line_color; ?>" style="display: none">
            <td>
              <?
              $columns_printed = 1;
              if (!empty($included_salaries)) {
                $salary_print_count = 1;
                $new_column_every = ceil(count($included_salaries)/$salary_detail_columns);
                foreach($included_salaries as $included_salary) {
              ?>
                L <a href="<? print $included_salary['link']; ?>"><? print $included_salary['JournalID']; ?></a><br/>
              <?
                  if (!($salary_print_count % $new_column_every || $columns_printed == $salary_detail_columns)) {
                    echo '</td><td>';
                    $columns_printed++;
                  }
                  $salary_print_count++;
                }
              }
              ?>
            </td>
            <?
                while($columns_printed++ < $salary_detail_columns) print "<td></td>";
            ?>
            <td>
              <?
              $columns_printed = 1;
              if (!empty($included_work_relations)) {
                $employee_print_count = 1;
                $new_column_every = ceil(count($included_work_relations)/$employee_detail_columns);
                foreach($included_work_relations as $included_employee) {
              ?>
                <a href="<? print $included_employee['link']; ?>"><? print $included_employee['AccountPlanName']; ?></a><br/>
              <?
                  if (!($employee_print_count % $new_column_every || $columns_printed == $employee_detail_columns)) {
                    echo '</td><td>';
                    $columns_printed++;
                  }
                  $employee_print_count++;
                }
              }
              ?>
            </td>
            <?
                while($columns_printed++ < $employee_detail_columns) print "<td></td>";
            ?>
          </tr>
        </table>
      </td>
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
