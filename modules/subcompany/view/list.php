<?
require_once "record.inc";

$query_subcompany = "SELECT * FROM $db_table";
$result_subcompany = $_lib['db']->db_query($query_subcompany);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Virksomhetliste</title>
    <meta name="cvs" content="$Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Virksomheter:</th>
    <th colspan="3"></th>
  <tr>
    <th></th>
    <th align="right" colspan="3">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="subcompany_search" action="<? print $_lib['sess']->dispatch ?>t=subcompany.edit" method="post">
        Nytt nr:
        <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'SubcompanyID', 'width'=>'10')) ?>
        <? print $_lib['form3']->submit(array('name'=>'action_subcompany_new', 'value'=>'Ny virksomhet')) ?>
      </form>
    <? } ?>
    </th>
  </tr>
  <tr>
    <td class="menu">Virksomhet</td>
    <td class="menu">Navn</td>
    <td class="menu">NO:ORGNO</td>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_subcompany)) {
  $i++;
  if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
  $link_location = $_lib['sess']->dispatch . "t=subcompany.edit&subcompany.SubcompanyID=" . $row->SubcompanyID;
?>
  <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $link_location; ?>"><? print $row->SubcompanyID; ?></a></td>
      <td><a href="<? print $link_location; ?>"><? print $row->Name; ?></a></td>
      <td><a href="<? print $link_location; ?>"><? print $row->OrgNumber; ?></a></td>
  </tr>
<? } ?>
</tbody>
</table>
</body>
</html>
