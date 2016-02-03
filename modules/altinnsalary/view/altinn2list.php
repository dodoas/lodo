<?
$db_table = "altinnReport2";
require_once "record.inc";

$query_altinn  = "select * from $db_table order by AltinnReport2ID";
$result = $_lib['db']->db_query($query_altinn);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 2 List</title>
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

<br />
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn2list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_soap2', 'value'=>'Test Soap2')) ?>
</form>


<table class="lodo_data">
  <thead>
    <tr>
      <th>Soap 2:</th>
      <th colspan="24"></th>
    </tr>
    <tr>
      <td class="menu">ID</td>
      <td class="menu">LastChanged</td>
      <td class="menu">ParentReceiptId</td>
      <td class="menu">ReceiptHistory</td>
      <td class="menu">ReceiptId</td>
      <td class="menu">ReceiptStatus</td>
      <td class="menu">ReceiptTemplate</td>
      <td class="menu">ReceiptText</td>
      <td class="menu">ReceiptType</td>
      <td class="menu">ExternalShipmentReference</td>
      <td class="menu">OwnerPartyReference</td>
      <td class="menu">WorkFlowReference</td>
      <td class="menu">ReceiversReference</td>
      <td class="menu">ArchiveReference</td>
      <td class="menu">PartyReferenceA</td>
      <td class="menu">PartyReferenceB</td>
      <td class="menu">SubReceiptsLastChanged</td>
      <td class="menu">SubReceiptsParentReceiptId</td>
      <td class="menu">SubReceiptsReceiptHistory</td>
      <td class="menu">SubReceiptsReceiptId</td>
      <td class="menu">SubReceiptsReceiptStatus</td>
      <td class="menu">SubReceiptsReceiptTemplate</td>
      <td class="menu">SubReceiptsReceiptText</td>
      <td class="menu">SubReceiptsReceiptType</td>
      <td class="menu">SubReceiptsSendersReference</td>
    </tr>
  </thead>


  <tbody>
  <?
  while($row = $_lib['db']->db_fetch_object($result)) {
  $i++;
  ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><?print $row->AltinnReport2ID; ?></td>
      <td><?print $row->res_LastChanged; ?></td>
      <td><?print $row->res_ParentReceiptId; ?></td>
      <td><?print $row->res_ReceiptHistory; ?></td>
      <td><?print $row->res_ReceiptId; ?></td>
      <td><?print $row->res_ReceiptStatus; ?></td>
      <td><?print $row->res_ReceiptTemplate; ?></td>
      <td><?print $row->res_ReceiptText; ?></td>
      <td><?print $row->res_ReceiptType; ?></td>
      <td><?print $row->res_ExternalShipmentReference; ?></td>
      <td><?print $row->res_OwnerPartyReference; ?></td>
      <td><?print $row->res_WorkFlowReference; ?></td>
      <td><?print $row->res_ReceiversReference; ?></td>
      <td><?print $row->res_ArchiveReference; ?></td>
      <td><?print $row->res_PartyReferenceA; ?></td>
      <td><?print $row->res_PartyReferenceB; ?></td>
      <td><?print $row->res_SubReceiptsLastChanged; ?></td>
      <td><?print $row->res_SubReceiptsParentReceiptId; ?></td>
      <td><?print $row->res_SubReceiptsReceiptHistory; ?></td>
      <td><?print $row->res_SubReceiptsReceiptId; ?></td>
      <td><?print $row->res_SubReceiptsReceiptStatus; ?></td>
      <td><?print $row->res_SubReceiptsReceiptTemplate; ?></td>
      <td><?print $row->res_SubReceiptsReceiptText; ?></td>
      <td><?print $row->res_SubReceiptsReceiptType; ?></td>
      <td><?print $row->res_SubReceiptsSendersReference; ?></td>
  <? } ?>
  </tbody>
</table>
</body>
</html>


