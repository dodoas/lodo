<?
require_once "record.inc";
print $_lib['sess']->doctype
?>

<head>
  <title>Empatix - Altinn lønnslipper</title>
  <? includeinc('head') ?>
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
      <td class="menu">ID</td>
      <td class="menu">Periode</td>
      <td class="menu">Sent kl</td>
      <td class="menu">L&oslash;nnslipper</td>
      <td class="menu">Arkivert kl</td>
      <td class="menu">Status</td>
      <td class="menu">Handlinger</td>
    </tr>
  <?
  $so1_query = "select * from altinnReport1 order by AltinnReport1ID";
  $so1 = $_lib['db']->db_query($so1_query);
  while($so1row = $_lib['db']->db_fetch_object($so1)) {
    $salary_ids = array();
  ?>
    <?
    $so2query = "SELECT * FROM altinnReport2 WHERE res_ReceiptId = ".$so1row->ReceiptId." ORDER BY res_ReceiversReference DESC , AltinnReport2ID LIMIT 1";
    $so2 = $_lib['db']->db_query($so2query);
    $so2row = $_lib['db']->db_fetch_object($so2);
    ?>
    <tr>
      <td>
        <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show&AltinnReport1ID=<? print $so1row->AltinnReport1ID ?>">
          <? print $so1row->AltinnReport1ID; ?>
          </a>
      </td>
      <td><?print $so1row->Period; ?></td>
      <td><?print $so1row->LastChanged; ?></td>
      <td>
        <?
        $query_altin_salary = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$so1row->AltinnReport1ID." ORDER BY SalaryId ASC";
        $result_altin_salary  = $_lib['db']->db_query($query_altin_salary);

        while($_row = $_lib['db']->db_fetch_object($result_altin_salary)){
          $salary_ids[] = $_row->SalaryId;
        ?>
          L <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>"><? print $_row->JournalID?><? print ($_row->Changed) ? "(endrett)" : "" ?></a>
        <? } ?>
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
          <input type="hidden" name="altinnReport1.ExternalShipmentReference" value='<?print 'LODO' . time(); ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap1',
            'value'=>'Send p&aring; nytt',
            'disabled' => !($so2row->res_ReceiversReference && empty($so1row->ReplacedByMeldindsID))
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
    <? } ?>

    <tr style="border-top: 5px solid black">
      <td style="border-top: 5px solid black" colspan="200">
      </td>
    </tr>
</table>

</body>
</html>
