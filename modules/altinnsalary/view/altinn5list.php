<?
$db_table = "altinnReport5";
require_once "record.inc";

$query  = "select * from $db_table order by AltinnReport5ID";
$result = $_lib['db']->db_query($query);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 5 List</title>
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
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn5list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_soap5', 'value'=>'Test Soap5')) ?>
</form>


<table class="lodo_data">
  <thead>
    <tr>
      <th>Soap 4:</th>
      <th colspan="18"></th>
    </tr>
    <tr>
      <td class="menu">AltinnReport5ID</td>
      <td class="menu">req_CorrespondenceID</td>
      <td class="menu">LastChanged</td>
      <td class="menu">ParentReceiptId</td>
      <td class="menu">ReceiptHistory</td>
      <td class="menu">ReceiptId</td>
      <td class="menu">ReceiptStatusCode</td>
      <td class="menu">ReceiptTemplate</td>
      <td class="menu">ReceiptText</td>
      <td class="menu">ReceiptTypeName</td>
      <td class="menu">ExternalShipmentReference</td>
      <td class="menu">OwnerPartyReference</td>
    </tr>
  </thead>
  <tbody>
  <?
  while($row = $_lib['db']->db_fetch_object($result)) {
  ?>
    <tr>
      <td><? print $row->AltinnReport5ID; ?></td>
      <td><? print $row->req_CorrespondenceID; ?></td>
      <td><? print $row->res_LastChanged; ?></td>
      <td><? print $row->res_ParentReceiptId; ?></td>
      <td><? print $row->res_ReceiptHistory; ?></td>
      <td><? print $row->res_ReceiptId; ?></td>
      <td><? print $row->res_ReceiptStatusCode; ?></td>
      <td><? print $row->res_ReceiptTemplate; ?></td>
      <td><? print $row->res_ReceiptText; ?></td>
      <td><? print $row->res_ReceiptTypeName; ?></td>
      <td><? print $row->res_ExternalShipmentReference; ?></td>
      <td><? print $row->res_OwnerPartyReference; ?></td>
  <? } ?>
  </tbody>
</table>
</body>
</html>


