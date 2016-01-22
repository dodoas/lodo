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

<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.salarylist">Send new report</a>

<br/><br/>

<table class="lodo_data">
  <tbody>
    <tr>
      <td class="menu">ID</td>
      <td class="menu">Period</td>
      <td class="menu">Sent at</td>
      <td class="menu">Salaries</td>
      <td class="menu">Archived at</td>
      <td class="menu">Status</td>
      <td class="menu">Actions</td>
    </tr>
  <?
  $so1_query = "select * from altinnReport1 order by AltinnReport1ID";
  $so1 = $_lib['db']->db_query($so1_query);
  while($so1row = $_lib['db']->db_fetch_object($so1)) {
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
          $query_salary = "SELECT * FROM salary WHERE JournalID = ".$_row->JournalID;
          $result_salary  = $_lib['db']->db_query($query_salary);
          $_salary = $_lib['db']->db_fetch_object($result_salary);
        ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>">
            L <? print $_row->JournalID?><? print ($_row->Changed) ? "(endrett)" : "" ?>
            </a>
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
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.confirm_authentication" method="post">
          <input type="hidden" name="request_type" value='archive'>
          <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_confirm_authentication',
            'value'=>'Archive',
            'disabled' => $so2row->res_ReceiversReference ? false : true
          )) ?>
        </form>
        <?
        }
        ?>
      </td>
      <td>
        <?print $so2row->res_ReceiptStatus; ?>
        <? // print $so2row->res_ReceiversReference; ?>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <? print $_lib['form3']->submit(array('name'=>'action_soap2', 'value'=>'Check Status')) ?>
        </form>
      </td>
      <td>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
          <input type="hidden" name="altinnReport1.periode" value='<?print $so1row->Period; ?>'>
          <input type="hidden" name="altinnReport1.MeldingsId" value='<?print $so1row->MeldingsId; ?>'>
          <input type="hidden" name="altinnReport1.ExternalShipmentReference" value='<?print date(DATE_RFC2822); ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_soap1',
            'value'=>'Resend',
            'disabled' => !($so2row->res_ReceiversReference && empty($so1row->ReplacedByMeldindsID))
          )); ?>
        </form>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.confirm_authentication" method="post">
          <input type="hidden" name="request_type" value='feedback'>
          <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
          <? print $_lib['form3']->submit(array(
            'name'=>'action_confirm_authentication',
            'value'=>'Get Feedback',
            'disabled' => $so2row->res_ReceiversReference ? false : true
            )) ?>
        </form>
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
