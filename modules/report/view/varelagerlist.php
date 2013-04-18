<?
# $Id: list.php,v 1.16 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$CreatedDate = $_REQUEST['CreatedDate'];


$db_table_var = "varetelling";
$db_table_var2 = "varelagerline";

require_once "record.inc";

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Varelager</title>
    <meta name="cvs" content="$Id: list.php,v 1.16 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table class="lodo_data">
<thead>
  <tr>
      <th align="left">Varelager 
      <th colspan="5">
  </tr>
  <tr>
      <th>Dato</th>
      <th>Beskrivelse</th>
      <th>Avdeling</th>
      <th>periode</th>
      <th><input type="button" name="name" value=" Oppdater " onClick="document.location='<?php global $MY_SELF; print $MY_SELF; ?>';"/></th>
      <th></th>
  </tr>
  <tr>
      <form name="varelager_ny" action="<? print $_lib['sess']->dispatch ?>t=report.edit" method="post" target="_new">
        <th>
            <? print $_lib['form3']->text(array('table'=>$db_table_var, 'field'=>'CreatedDate', 'width'=>'10', 'tabindex'=>'1')) ?>
        </th>
        <th>
            <? print $_lib['form3']->text(array('table'=>$db_table_var, 'field'=>'Description', 'width'=>'30', 'tabindex'=>'9')) ?>
        </th>
       <th>
        <?
            $aconf = array();
            $aconf['table']         = $db_table_var;
            $aconf['field']         = 'DepartmentID';
            $aconf['accesskey']     = 'D';
            $_lib['form2']->department_menu2($aconf);
        ?>
        </th> 
        <th>
            <?
               print $_lib['form3']->AccountPeriod_menu3(array('table' =>$db_table_var,'field' => 'Period','width'=>'30', 'tabindex'=>'4','noaccess' => false));
            ?>
        </th>
        <th>
            <? print $_lib['form3']->submit(array('name'=>'action_varetelling_new', 'value'=>'Ny varetelling')) ?>
        </th>
        <th></th>
      </form>
  </tr>
  <tr>
    <th class="menu">Dato</th>
    <th class="menu">Beskrivelse</th>
    <th class="menu">Avdeling</th>
    <th class="menu">periode</th>
    <th class="menu">Sum</th>
    <th class="menu"></th>
  </tr>
</thead>

<tbody>
<?
//$query = "select *, SUM(L.CostPrice * L.Antall) as S from $db_table_var as V, varelagerline as L WHERE L.VarelagerID = V.VarelagerID group by V.VareLagerID order by CreatedDate desc ";
$query = "select cd.DepartmentName,V.*, SUM(L.CostPrice * L.Antall) as S from $db_table_var as V LEFT JOIN companydepartment as cd ON V.DepartmentID = cd.CompanyDepartmentID LEFT JOIN varelagerline as L ON L.VarelagerID = V.VareTellingID group by V.VareTellingID order by CreatedDate desc ";
$result = $_lib['db']->db_query($query);
while($row = $_lib['db']->db_fetch_object($result))
{
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print $sec_color ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=report.edit&VareTellingID=<? print $row->VareTellingID ?>" target="_new"><? print $row->CreatedDate; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=report.edit&VareTellingID=<? print $row->VareTellingID ?>" target="_new"><? print $row->Description; ?></a></td>
      <td><? print $row->DepartmentName; ?></td>
      <td><? print $row->Period; ?></td>
      <td>
        <?= $_lib['format']->Amount(array('value'=>$row->S, 'return'=>'value')) ?>
      </td>
      <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=report.expense&action_varetelling_delete=<? print $row->VareTellingID ?>" class="button">Slett</a>
      <? } ?>
      </td>

  </tr>
<? } ?>
</tbody>
</table>
</body>
</html>
