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



<table class="lodo_data">
  <tbody>

  <?
  $so1_query = "select * from altinnReport1 order by AltinnReport1ID";
  $so1 = $_lib['db']->db_query($so1_query);
  while($so1row = $_lib['db']->db_fetch_object($so1)) {
  ?>
    <tr>
      <th>Soap 1:</th>
      <th colspan="11"></th>
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
      <td class="menu">Salaries</td>
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
      <td>
        <?
        $query_salary = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$so1row->AltinnReport1ID." ORDER BY SalaryId ASC";
        $result_salary  = $_lib['db']->db_query($query_salary);

        while($_row = $_lib['db']->db_fetch_object($result_salary)){
        ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>">L <? print $_row->JournalID ?></a>
        <? } ?>
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
    </tr>

    <?
    $so2_query = "SELECT * FROM altinnReport2 WHERE res_ReceiptId = ".$so1row->ReceiptId." ORDER BY res_ReceiversReference DESC , AltinnReport2ID LIMIT 1";
    $so2 = $_lib['db']->db_query($so2_query);
    $so2_last = null;
    while($so2_row = $_lib['db']->db_fetch_object($so2)) {
      $so2_last = $so2_row;
    ?>
      <tr>
        <td><?print $so2_row->AltinnReport2ID; ?></td>
        <td><?print $so2_row->res_LastChanged; ?></td>
        <td><?print $so2_row->res_ReceiptId; ?></td>
        <td><?print $so2_row->res_ReceiptStatus; ?></td>
        <td><?print $so2_row->res_ReceiptHistory; ?></td>
        <td><?print $so2_row->res_ReceiptTemplate; ?></td>
        <td><?print $so2_row->res_ReceiptText; ?></td>
        <td><?print $so2_row->res_ReceiversReference; ?></td>
        <td><?print $so2_row->res_ArchiveReference; ?></td>
      </tr>
    <? } ?>
    <?
    if (!$so2_last) {
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
    $so4_query = "SELECT * FROM altinnReport4 WHERE req_CorrespondenceID = ".$so2_last->res_ReceiversReference." ORDER BY AltinnReport4ID";
    $so4 = $_lib['db']->db_query($so4_query);
    while($so4_row = $_lib['db']->db_fetch_object($so4)) {
    ?>
      <tr>
        <td>
          <a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show&AltinnReport1ID=<? print $so1row->AltinnReport4ID ?>">
            <? print $so1row->AltinnReport4ID; ?>
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
    $so5_query = "SELECT * FROM altinnReport5 WHERE req_CorrespondenceID = ".$so2_last->res_ReceiversReference." ORDER BY AltinnReport5ID";
    var_dump($so5_query);
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


