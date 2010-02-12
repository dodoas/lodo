<?
# $Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$ProjectID = $_REQUEST['ProjectID'];
assert(!is_int($ProjectID)); #All main input should be int

$db_table = "project";
require_once "record.inc";

$query = "select * from $db_table order by Heading";
$result_project = $_lib['db']->db_query($query);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - project list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
    <th align="left">Prosjekter
    <th colspan="2">
  <tr>
    <th>
    <th colspan="2">
        <form name="project_search" action="<? print $_lib['sess']->dispatch ?>t=project.edit" method="post">
            Nytt nr:
            <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProjectID', 'width'=>'10')) ?>
            <? print $_lib['form3']->submit(array('name'=>'action_project_new', 'value'=>'Nytt prosjekt')) ?>
        </form>
  <tr>
    <th class="menu">Prosjekt
    <th class="menu">Beskrivelse
    <th class="menu">Aktiv
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_project)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=project.edit&project.ProjectID=<? print $row->ProjectID ?>"><? print $row->ProjectID; ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=project.edit&project.ProjectID=<? print $row->ProjectID ?>"><? print $row->Heading; ?></a>
      <td><? print $_lib['form3']->checkbox(array('table'=>'project', 'value'=>$row->Active, 'disabled'=>'1')) ?>
<? } ?>
</tbody>
</table>
</body>
</html>