<?
/* $Id: template.php,v 1.38 2005/10/24 11:50:25 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$WeeklySaleConfID = $_REQUEST['WeeklySaleConfID'];

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$query_week     = "select * from weeklysaleconf where WeeklySaleConfID = '$WeeklySaleConfID'";
$head           = $_lib['storage']->get_row(array('query' => $query_week));

$query_sale     = "select * from weeklysalegroupconf where WeeklySaleConfID = '$WeeklySaleConfID' order by Type";
$result_sale    = $_lib['db']->db_query($query_sale);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: template.php,v 1.38 2005/10/24 11:50:25 svenn Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="template_update" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="WeeklySaleConfID"        value="<? print $WeeklySaleConfID ?>" size="7">
<? print $message ?>
<table class="lodo_data">
  <tr class="result">
    <th colspan="21">Ukeomsetning mal oppsett: <? print $head->Name ?></th>

  <tr>
    <th class="sub">Avdeling</th>
    <td colspan="2"><? $_lib['form2']->department_menu2(array('table'=>'weeklysaleconf', 'field'=>'DepartmentID', 'accesskey'=>'D', 'value' => $head->DepartmentID, 'pk'=>$head->WeeklySaleConfID)) ?>
    </td>
   </tr>
   <tr>
    <th class="sub">Art</th>
    <td><? print $head->VoucherType ?> <? $_lib['form2']->Type_menu2(array('table' => 'weeklysaleconf', 'field' => 'VoucherType', 'type' => 'VoucherType', 'value'  => $head->VoucherType, 'pk'=>$head->WeeklySaleConfID)) ?></td>
   </tr>
   <tr>
    <th class="sub">Navn p&aring; konfigurasjon</th>
    <td colspan="2"><input type="text" name="weeklysaleconf.Name.<? print $head->WeeklySaleConfID ?>"   value="<? print $head->Name ?>" size="30"></td>
   </tr>
   <tr>
    <th class="sub">Permanente kontanter</th>
    <td colspan="2"><input type="text" name="weeklysaleconf.PermanentCash.<? print $head->WeeklySaleConfID ?>"   value="<? print $_lib['format']->Amount($head->PermanentCash) ?>" size="30"></td>
   </tr>
   <tr>
    <th class="sub">Oppstartsdato</th>
    <td colspan="2"><input type="text" name="weeklysaleconf.StartDate.<? print $head->WeeklySaleConfID ?>"  value="<? print $head->StartDate ?>" /></td>
   </tr>
   <tr>
    <th class="sub">Sluttdato</th>
    <td colspan="2"><input type="text" name="weeklysaleconf.EndDate.<? print $head->WeeklySaleConfID ?>"  value="<? print $head->EndDate ?>" /></td>
   </tr>
<?
$aconf = array();
$aconf['table']         = 'weeklysalegroupconf';
$aconf['type'][]   		= 'hovedbok';
$aconf['num_letters']   = 10;

  $teller=0;
  while($row = $_lib['db']->db_fetch_object($result_sale))
  {
      $teller++;
      print $_lib['form3']->hidden(array('name'=>$teller, 'value'=>$row->WeeklySaleGroupConfID));
  ?>
      <tr>
        <th><? if($row->Type == 1) { ?>Inntekt<? } else { ?>Likvidkonto<? } ?></th>
      </tr>
      <tr>
        <th class="sub"></th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
        <th class="sub">Gruppe<? print $i; ?></th>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Beskrivelse</th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
          <td><input type="text" name="weeklysalegroupconf.Group<? print $i; ?>Name.<? print $row->WeeklySaleGroupConfID ?>" value="<? print $row->{"Group". $i ."Name"} ?>" size="7"></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Konto</th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
        <td><?
          $aconf['table'] = 'weeklysalegroupconf';
          $aconf['field'] = 'Group'. $i .'Account';
          $aconf['value'] = $row->{"Group". $i ."Account"};
          $aconf['pk'] = $row->WeeklySaleGroupConfID;
          print $_lib['form3']->accountplan_number_menu($aconf);
        ?></td>
        <?
        }
        ?>
      </tr>

        <th class="sub">Avdeling</th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group'. $i .'DepartmentID', 'accesskey'=>'D', 'value'=>$row->{"Group". $i ."DepartmentID"}, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Prosjekt</th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group'. $i .'ProjectID', 'value' => $row->{"Group". $i ."ProjectID"}, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Bil</th>
        <?
        for ($i = 1; $i <= 10; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableCar from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableCar == 1)
                        $_lib['form2']->car_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group'. $i .'CarID', 'value'=>$row->{"Group". $i ."CarID"}, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub"></th>
        <?
        for ($i = 11; $i <= 20; $i++) {
        ?>
          <th class="sub">Gruppe<? print $i; ?></th>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Beskrivelse</th>
        <?
        for ($i = 11; $i <= 20; $i++) {
          if ($i < 18 || ($i >= 18 && $row->Type != 2)) {
        ?>
            <td><input type="text" name="weeklysalegroupconf.Group<? print $i; ?>Name.<? print $row->WeeklySaleGroupConfID ?>" value="<? print $row->{"Group". $i ."Name"} ?>" size="7"></td>
        <?
          }
          else {
            print "<td>Kontant *</td>";
          }
        ?>
        </td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Konto</th>
        <?
        for ($i = 11; $i <= 20; $i++) {
        ?>
        <td><?
          if ($i < 19 || ($i >= 19 && $row->Type != 2)) {
            $aconf['table'] = 'weeklysalegroupconf';
            $aconf['field'] = 'Group'. $i .'Account';
            $aconf['value'] = $row->{"Group". $i ."Account"};
            $aconf['pk'] = $row->WeeklySaleGroupConfID;
            print $_lib['form3']->accountplan_number_menu($aconf);
          }
        ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Avdeling</th>
        <?
        for ($i = 11; $i <= 20; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group'. $i .'DepartmentID', 'accesskey'=>'D', 'value'=>$row->{"Group". $i ."DepartmentID"}, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Prosjekt</th>
        <?
        for ($i = 11; $i <= 20; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group'. $i .'ProjectID', 'value' => $row->{"Group". $i ."ProjectID"}, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <tr>
        <th class="sub">Bil</th>
        <?
        for ($i = 11; $i <= 20; $i++) {
        ?>
        <td>
            <?
                if($row->{"Group". $i ."Account"} > 0)
                {
                    $queryCheck="select EnableCar from accountplan where AccountPlanID=". $row->{"Group". $i ."Account"};
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableCar == 1)
                        $_lib['form2']->car_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group'. $i .'CarID', 'value'=>$row->{"Group". $i ."CarID"}, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <?
        }
        ?>
      </tr>
      <?
    }
    ?>
</table>
<?
if($_lib['sess']->get_person('AccessLevel') > 2)
{
    ?>
    <input type="submit" name="action_weeklysaleconf_update" value="Lagre uke mal (S)" accesskey="S" />
    <?
}
?>
</form>
* = P&aring;krevet
<? includeinc('bottom') ?>
</body>
</html>
