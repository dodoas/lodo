<?
$db_table = "altinnReport3";
require_once "record.inc";

$query_altinn  = "select * from $db_table order by AltinnReport3ID";
$result = $_lib['db']->db_query($query_altinn);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 3 List</title>
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
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn3list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_soap3', 'value'=>'Test Soap3')) ?>
</form>


<table class="lodo_data">
  <thead>
    <tr>
      <th>Soap 3:</th>
      <th colspan="24"></th>
    </tr>
    <tr>
      <td class="menu">AltinnReport3ID</td>
      <td class="menu">Message</td>
      <td class="menu">Status</td>
      <td class="menu">ValidFrom</td>
      <td class="menu">ValidTo</td>
      <td class="menu">KodeNr</td>
    </tr>
  </thead>

  <tbody>
  <?
  while($row = $_lib['db']->db_fetch_object($result)) {
  ?>
    <tr>
      <td><?print $row->AltinnReport3ID; ?></td>
      <td><?print $row->res_Message; ?></td>
      <td><?print $row->res_Status; ?></td>
      <td><?print $row->res_ValidFrom; ?></td>
      <td><?print $row->res_ValidTo; ?></td>
      <td><?print $row->res_KodeNr; ?></td>
  <? } ?>
  </tbody>
</table>
</body>
</html>


