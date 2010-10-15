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
        <th class="sub">Gruppe1</th>
        <th class="sub">Gruppe2</th>
        <th class="sub">Gruppe3</th>
        <th class="sub">Gruppe4</th>
        <th class="sub">Gruppe5</th>
        <th class="sub">Gruppe6</th>
        <th class="sub">Gruppe7</th>
        <th class="sub">Gruppe8</th>
        <th class="sub">Gruppe9</th>
        <th class="sub">Gruppe10</th>
      </tr>
      <tr>
        <th class="sub">Beskrivelse</th>
        <td><input type="text" name="weeklysalegroupconf.Group1Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group1Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group2Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group2Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group3Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group3Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group4Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group4Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group5Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group5Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group6Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group6Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group7Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group7Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group8Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group8Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group9Name.<? print $row->WeeklySaleGroupConfID ?>"    value="<? print $row->Group9Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group10Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group10Name ?>" size="7"></td>
      </tr>
      <tr>
        <th class="sub">Konto</th>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group1Account';  $aconf['value'] = $row->Group1Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group2Account';  $aconf['value'] = $row->Group2Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group3Account';  $aconf['value'] = $row->Group3Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group4Account';  $aconf['value'] = $row->Group4Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group5Account';  $aconf['value'] = $row->Group5Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group6Account';  $aconf['value'] = $row->Group6Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group7Account';  $aconf['value'] = $row->Group7Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group8Account';  $aconf['value'] = $row->Group8Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group9Account';  $aconf['value'] = $row->Group9Account;  $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group10Account'; $aconf['value'] = $row->Group10Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
      </tr>

      <tr>
        <th class="sub">Avdeling</th>
        <td>
            <?
                if($row->Group1Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group1Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group1DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group1DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group2Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group2Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group2DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group2DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group3Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group3Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group3DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group3DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group4Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group4Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group4DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group4DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group5Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group5Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group5DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group5DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group6Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group6Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group6DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group6DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group7Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group7Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group7DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group7DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group8Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group8Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group8DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group8DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group9Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group9Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group9DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group9DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group10Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group10Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group10DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group10DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
      </tr>
      <tr>
        <th class="sub">Prosjekt</th>
        <td>
            <?
                if($row->Group1Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group1Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group1ProjectID', 'value' => $row->Group1ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));

                }
            ?></td>
        <td>
            <?
                if($row->Group2Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group2Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group2ProjectID', 'value' => $row->Group2ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));

                }
            ?></td>
        <td>
            <?
                if($row->Group3Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group3Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group3ProjectID', 'value' => $row->Group3ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));

                }
            ?></td>
        <td>
            <?
                if($row->Group4Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group4Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group4ProjectID', 'value' => $row->Group4ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group5Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group5Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group5ProjectID', 'value' => $row->Group5ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));

                }
            ?></td>
        <td>
            <?
                if($row->Group6Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group6Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group6ProjectID', 'value' => $row->Group6ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group7Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group7Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group7ProjectID', 'value' => $row->Group7ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group8Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group8Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group8ProjectID', 'value' => $row->Group8ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group9Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group9Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group9ProjectID', 'value' => $row->Group9ProjectID,  'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));

                }
            ?></td>
        <td>
            <?
                if($row->Group10Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group10Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group10ProjectID', 'value' => $row->Group10ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
      </tr>
      <tr>
        <th class="sub"></th>
        <th class="sub">Gruppe11</th>
        <th class="sub">Gruppe12</th>
        <th class="sub">Gruppe13</th>
        <th class="sub">Gruppe14</th>
        <th class="sub">Gruppe15</th>
        <th class="sub">Gruppe16</th>
        <th class="sub">Gruppe17</th>
        <th class="sub">Gruppe18</th>
        <th class="sub">Gruppe19</th>
        <th class="sub">Gruppe20</th>
      </tr>
      <tr>
        <th class="sub">Beskrivelse</th>
        <td><input type="text" name="weeklysalegroupconf.Group11Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group11Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group12Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group12Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group13Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group13Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group14Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group14Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group15Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group15Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group16Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group16Name ?>" size="7"></td>
        <td><input type="text" name="weeklysalegroupconf.Group17Name.<? print $row->WeeklySaleGroupConfID ?>"   value="<? print $row->Group17Name ?>" size="7"></td>
        <td>
            <?
            if($row->Type != 2)
                print $_lib['form3']->text(array('table'=>'weeklysalegroupconf', 'field'=>'Group18Name', 'pk'=>$row->WeeklySaleGroupConfID, 'value'=>$row->Group18Name, 'width'=>'7'));
            else
                print "Kontant *";
            ?>
        </td>
        <td>
            <?
            if($row->Type != 2)
                print $_lib['form3']->text(array('table'=>'weeklysalegroupconf', 'field'=>'Group19Name', 'pk'=>$row->WeeklySaleGroupConfID, 'value'=>$row->Group19Name, 'width'=>'7'));
            else
                print "Inntekt";
            ?>
        </td>
        <td>
            <?
            if($row->Type != 2)
                print $_lib['form3']->text(array('table'=>'weeklysalegroupconf', 'field'=>'Group20Name', 'pk'=>$row->WeeklySaleGroupConfID, 'value'=>$row->Group20Name, 'width'=>'7'));
            else
                print "Utgift";
            ?>
        </td>
      </tr>
      <tr>
        <th class="sub">Konto</th>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group11Account'; $aconf['value'] = $row->Group11Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group12Account'; $aconf['value'] = $row->Group12Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group13Account'; $aconf['value'] = $row->Group13Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group14Account'; $aconf['value'] = $row->Group14Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group15Account'; $aconf['value'] = $row->Group15Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group16Account'; $aconf['value'] = $row->Group16Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group17Account'; $aconf['value'] = $row->Group17Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? $aconf['table'] = 'weeklysalegroupconf';   $aconf['field'] = 'Group18Account'; $aconf['value'] = $row->Group18Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); ?></td>
        <td><? if($row->Type != 2) { $aconf['field'] = 'Group19Account'; $aconf['table'] = 'weeklysalegroupconf';   $aconf['value'] = $row->Group19Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); } ?></td>
        <td><? if($row->Type != 2) { $aconf['field'] = 'Group20Account'; $aconf['table'] = 'weeklysalegroupconf';   $aconf['value'] = $row->Group20Account; $aconf['pk'] = $row->WeeklySaleGroupConfID; print $_lib['form3']->accountplan_number_menu($aconf); } ?></td>
      </tr>
      <tr>
        <th class="sub">Avdeling</th>
        <td>
            <?
                if($row->Group11Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group11Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group11DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group11DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group12Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group12Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group12DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group12DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group13Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group13Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group13DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group13DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group14Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group14Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group14DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group14DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group15Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group15Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group15DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group15DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group16Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group16Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group16DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group16DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group17Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group17Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysaleconf', 'field'=>'Group17DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group17DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group18Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group18Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group18DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group18DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
        <td>
            <?
                if($row->Group19Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group19Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group19DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group19DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));

                }
            ?></td>
        <td>
            <?
                if($row->Group20Account > 0)
                {
                    $queryCheck="select EnableDepartment from accountplan where AccountPlanID=$row->Group20Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableDepartment == 1)
                        $_lib['form2']->department_menu2(array('table'=>'weeklysalegroupconf', 'field'=>'Group20DepartmentID', 'accesskey'=>'D', 'value'=>$row->Group20DepartmentID, 'pk'=>$row->WeeklySaleGroupConfID));
                }
            ?></td>
      </tr>
      <tr>
        <th class="sub">Prosjekt</th>
        <td>
            <?
                if($row->Group11Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group11Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group11ProjectID', 'value' => $row->Group11ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']));
                }
            ?></td>
        <td>
            <?
                if($row->Group12Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group12Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group12ProjectID', 'value' => $row->Group12ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group13Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group13Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group13ProjectID', 'value' => $row->Group13ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group14Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group14Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group14ProjectID', 'value' => $row->Group14ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group15Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group15Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group15ProjectID', 'value' => $row->Group15ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group16Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group16Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group16ProjectID', 'value' => $row->Group16ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group17Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group17Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group17ProjectID', 'value' => $row->Group17ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
            ?></td>
        <td>
            <?
                if($row->Group18Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group18Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if($rowCheck->EnableProject == 1)
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group18ProjectID', 'value' => $row->Group18ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                }
                if($teller == 2)
                    #print "*";
            ?>
        </td>
        <td>
            <?
                if($row->Group19Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group19Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if( ($row->Type != 2) and ($rowCheck->EnableProject == 1) )
                    {
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group19ProjectID', 'value' => $row->Group19ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                    }
                }
            ?></td>
        <td>
            <?
                if($row->Group20Account > 0)
                {
                    $queryCheck="select EnableProject from accountplan where AccountPlanID=$row->Group20Account";
                    $rowCheck = $_lib['storage']->get_row(array('query' => $queryCheck));
                    if( ($row->Type != 2) and ($rowCheck->EnableProject == 1) )
                    {
                        print $_lib['form3']->project_menu(array('pk' => $row->WeeklySaleGroupConfID, 'field' => 'Group20ProjectID', 'value' => $row->Group20ProjectID, 'table' => 'weeklysalegroupconf', 'num_letters' => $aconf['num_letters']  ));
                    }
                }
            ?></td>
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
