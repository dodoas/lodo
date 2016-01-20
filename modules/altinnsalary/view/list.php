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

<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list">Soap 1 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn2list">Soap 2 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn3list">Soap 3 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn4list">Soap 4 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn5list">Soap 5 LIST</a>

<br/><br/>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.salarylist">Send new report</a>

<table class="lodo_data">
  <tbody>

  <?
  $so1_query = "select * from altinnReport1 order by AltinnReport1ID";
  $so1 = $_lib['db']->db_query($so1_query);
  while($so1row = $_lib['db']->db_fetch_object($so1)) {
  ?>
    <tr>
      <th>Soap 1:</th>
      <th colspan="12"></th>
    </tr>
    <tr>
      <td class="menu">ID</td>
      <td class="menu">Period</td>
      <td class="menu">ReceiptId</td>
      <td class="menu">ReceiptText</td>
      <td class="menu">ReceiptHistory</td>
      <td class="menu">LastChanged</td>
      <td class="menu">ReceiptTypeName</td>
      <td class="menu">ReceiptStatusCode</td>
      <td class="menu">MeldingsId</td>
      <td class="menu">ErstatterMeldingsId</td>
      <td class="menu">Salaries</td>
      <td class="menu">Resend</td>
      <td class="menu">Check Status</td>
    </tr>

    <tr>
      <td>
        <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show&AltinnReport1ID=<? print $so1row->AltinnReport1ID ?>">
          <? print $so1row->AltinnReport1ID; ?>
          </a>
      </td>
      <td><?print $so1row->Period; ?></td>
      <td><?print $so1row->ReceiptId; ?></td>
      <td><?print $so1row->ReceiptText; ?></td>
      <td><?print $so1row->ReceiptHistory; ?></td>
      <td><?print $so1row->LastChanged; ?></td>
      <td><?print $so1row->ReceiptTypeName; ?></td>
      <td><?print $so1row->ReceiptStatusCode; ?></td>
      <td><?print $so1row->MeldingsId; ?></td>
      <td><?print $so1row->ErstatterMeldingsId; ?></td>
      <td>
        <?
        $query_altin_salary = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$so1row->AltinnReport1ID." ORDER BY SalaryId ASC";
        $result_altin_salary  = $_lib['db']->db_query($query_altin_salary);

        while($_row = $_lib['db']->db_fetch_object($result_altin_salary)){
          $query_salary = "SELECT * FROM salary WHERE JournalID = ".$_row->JournalID;
          $result_salary  = $_lib['db']->db_query($query_salary);
          $_salary = $_lib['db']->db_fetch_object($result_salary);
        ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>">L <? ($_salary->UpdatedAt > $_row->UpdatedAt) ? print $_row->JournalID." (endrett)" : print $_row->JournalID ?></a>
        <? } ?>
      </td>
      <td>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
        <input type="hidden" name="altinnReport1.periode" value='<?print $so1row->Period; ?>'>
        <input type="hidden" name="altinnReport1.MeldingsId" value='<?print $so1row->MeldingsId; ?>'>
        <input type="hidden" name="altinnReport1.ExternalShipmentReference" value='<?print date(DATE_RFC2822); ?>'>
        <? print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Resend')) ?>
        </form>
      </td>
      <td>
        <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
        <? print $_lib['form3']->submit(array('name'=>'action_soap2', 'value'=>'Check Status')) ?>
        </form>
      </td>
    </tr>
    <tr>
      <th>Soap 2:</th>
      <td class="menu">LastChanged</td>
      <td class="menu">ReceiptId</td>
      <td class="menu">ReceiptStatus</td>
      <td class="menu">ReceiptHistory</td>
      <td class="menu">ReceiptTemplate</td>
      <td class="menu">ReceiptText</td>
      <td class="menu">ReceiversReference</td>
      <td class="menu">ArchiveReference</td>
      <td class="menu"></td>
      <td class="menu"></td>
      <td class="menu">Get Feedback</td>
      <td class="menu">Archive</td>
    </tr>

    <?
    $so2query = "SELECT * FROM altinnReport2 WHERE res_ReceiptId = ".$so1row->ReceiptId." ORDER BY res_ReceiversReference DESC , AltinnReport2ID LIMIT 1";
    $so2 = $_lib['db']->db_query($so2query);
    $so2last = null;
    while($so2row = $_lib['db']->db_fetch_object($so2)) {
      $so2last = $so2row;
    ?>
      <tr>
        <td><?print $so2row->AltinnReport2ID; ?></td>
        <td><?print $so2row->res_LastChanged; ?></td>
        <td><?print $so2row->res_ReceiptId; ?></td>
        <td><?print $so2row->res_ReceiptStatus; ?></td>
        <td><?print $so2row->res_ReceiptHistory; ?></td>
        <td><?print $so2row->res_ReceiptTemplate; ?></td>
        <td><?print $so2row->res_ReceiptText; ?></td>
        <td><?print $so2row->res_ReceiversReference; ?></td>
        <td><?print $so2row->res_ArchiveReference; ?></td>
        <td></td>
        <td></td>
        <td>
          <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.confirm_authentication" method="post">
            <input type="hidden" name="request_type" value='feedback'>
            <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
            <? print $_lib['form3']->submit(array('name'=>'action_confirm_authentication', 'value'=>'Get Feedback')) ?>
          </form>
        </td>
        <td>
          <form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.confirm_authentication" method="post">
            <input type="hidden" name="request_type" value='archive'>
            <input type="hidden" name="request_receivers_reference" value='<?print $so2row->res_ReceiversReference; ?>'>
            <? print $_lib['form3']->submit(array('name'=>'action_confirm_authentication', 'value'=>'Archive')) ?>
          </form>
        </td>
      </tr>
    <? } ?>
    <?
    if (!$so2last) {
      print ('<tr><td style="border-top:5px solid black" colspan="30"></td></tr>');
      continue;
    }
    ?>

    <tr>
      <th>Soap 4:</th>
      <td class="menu">req_CorrespondenceID</td>
      <td class="menu">res_ArchiveReference</td>
      <td class="menu">res_ConfirmationDate</td>
      <td class="menu">res_CorrespondenceID</td>
      <td class="menu">res_DateSent</td>
      <td class="menu">res_Description</td>
      <td class="menu">res_SentBy</td>
    </tr>

    <?
    $so4_query = "SELECT * FROM altinnReport4 WHERE req_CorrespondenceID = '".$so2last->res_ReceiversReference."' ORDER BY AltinnReport4ID";
    $so4 = $_lib['db']->db_query($so4_query);
    while($so4_row = $_lib['db']->db_fetch_object($so4)) {
    ?>
      <tr>
        <td>
          <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4&AltinnReport4ID=<? print $so4_row->AltinnReport4ID ?>">
            <? print $so4_row->AltinnReport4ID; ?>
          </a>
        </td>
        <td><?print $so4_row->req_CorrespondenceID; ?></td>
        <td><?print $so4_row->res_ArchiveReference; ?></td>
        <td><?print $so4_row->res_ConfirmationDate; ?></td>
        <td><?print $so4_row->res_CorrespondenceID; ?></td>
        <td><?print $so4_row->res_DateSent; ?></td>
        <td><?print $so4_row->res_Description; ?></td>
        <td><?print $so4_row->res_SentBy; ?></td>
      </tr>
    <? } ?>




    <tr>
      <th>Soap 5:</th>
      <td class="menu">req_CorrespondenceID</td>
      <td class="menu">req_LastChanged</td>
      <td class="menu">req_ReceiptId</td>
      <td class="menu">req_ReceiptStatusCode</td>
      <td class="menu">req_ReceiptTypeName</td>
      <td class="menu">req_ExternalShipmentReference</td>
      <td class="menu">req_OwnerPartyReference</td>
    </tr>

    <?
    $so5_query = "SELECT * FROM altinnReport5 WHERE req_CorrespondenceID = '".$so2last->res_ReceiversReference."' ORDER BY AltinnReport5ID";
    $so5 = $_lib['db']->db_query($so5_query);
    while($so5_row = $_lib['db']->db_fetch_object($so5)) {
    ?>
      <tr>
        <td><?print $so5_row->AltinnReport5ID; ?></td>
        <td><?print $so5_row->req_CorrespondenceID; ?></td>
        <td><?print $so5_row->res_LastChanged; ?></td>
        <td><?print $so5_row->res_ReceiptId; ?></td>
        <td><?print $so5_row->res_ReceiptStatusCode; ?></td>
        <td><?print $so5_row->res_ReceiptTypeName; ?></td>
        <td><?print $so5_row->res_ExternalShipmentReference; ?></td>
        <td><?print $so5_row->res_OwnerPartyReference; ?></td>
      </tr>
    <? } ?>

    <tr style="border-top: 5px solid black">
      <td style="border-top: 5px solid black" colspan="200">
      </td>
    </tr>
  <? } ?>
</table>


</body>
</html>
