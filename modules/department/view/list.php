<?
# $Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

if(!$CompanyDepartmentID) {
  $CompanyDepartmentID = $_REQUEST['CompanyDepartmentID'];
}
assert(!is_int($CompanyDepartmentID)); #All main input should be int

$db_table = "companydepartment";
require_once "record.inc";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC") { $SORT = "DESC"; } else { $SORT = "ASC"; }
if(!$_SETUP[DB_START][0]) { $_SETUP[DB_START][0] = 0; }
if(!$CompanyID)   { $CompanyID = 1; }
if (!$order_by)   { $order_by  = "AccountNumber"; }
$db_stop = $_SETUP[DB_START][0] + $_SETUP[DB_OFFSET][0];

/* S¿kestreng */
$query_department = "select * from $db_table order by DepartmentName limit 200";
#print $query_department;
$result_department = $_lib['db']->db_query($query_department);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - department list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.25 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Avdelinger:
    <th colspan="3">
  <tr>
    <th>
    <th align="right" colspan="3">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="department_search" action="<? print $_lib['sess']->dispatch ?>t=department.edit" method="post">
        Nytt nr:
        <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'CompanyDepartmentID', 'width'=>'10')) ?>
        <? print $_lib['form3']->submit(array('name'=>'action_department_new', 'value'=>'Ny avdeling')) ?>
      </form>
  <? } ?>
  </tr>
  <tr>
    <td class="menu">Avdeling
    <td class="menu">Avdelingsnavn
    <td class="menu">Aktiv
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_department)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=department.edit&companydepartment.CompanyDepartmentID=<? print "$row->CompanyDepartmentID"; ?>"><? print $row->CompanyDepartmentID; ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=department.edit&companydepartment.CompanyDepartmentID=<? print "$row->CompanyDepartmentID"; ?>"><? print $row->DepartmentName; ?></a>
      <td><? print $_lib['form3']->checkbox(array('table'=>'project', 'value'=>$row->Active, 'disabled'=>'1')) ?>
<? } ?>
</tbody>
</table>
</body>
</html>


