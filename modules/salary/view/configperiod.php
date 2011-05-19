<?php

$SalaryperiodconfID = $_lib['input']->getProperty('SalaryperiodconfID');
$confyear = $_lib['input']->getProperty('confyear');
if(!$confyear)
  $confyear = date("Y");

require_once "record_config.inc";

$period_query = "SELECT Name, Voucherdate, Period, Fromdate, Todate, Year FROM salaryperiodconf WHERE SalaryperiodconfID = " . $SalaryperiodconfID;
$line = $_lib['db']->get_row(array( 'query' => $period_query ));

$employee_query = "SELECT AccountPlanID, AccountName FROM accountplan WHERE AccountPlanType = 'employee' ORDER BY AccountPlanID";
$employee_result = $_lib['db']->db_query($employee_query);

$entries_query = "SELECT * FROM salaryperiodentries WHERE SalaryperiodconfID = " . $SalaryperiodconfID;
$entries = $_lib['db']->get_hashhash( array( 'query' => $entries_query, 'key' => 'AccountPlanID' ) );

print $_lib['sess']->doctype;
?>
<head>
    <title>Empatix - salary period configuration</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1>L&oslash;nnskonfigurasjon for <?= $line->Name ?> - <?= $line->Year ?></h1>
<p> <a href="<? print $_lib['sess']->dispatch ?>t=salary.config&confyear=<?= $line->Year ?>">Tilbake til oversikten</a> </p>

<form action="<? print $_lib['sess']->dispatch ?>t=salary.configperiod&SalaryperiodconfID=<?= $SalaryperiodconfID ?>" method="post">

<table class='lodo_data'>
  <tr>
    <td>Navn</td>
    <td>
      <?=
        $_lib['form3']->text( array(
                              'table'=>'salaryperiodconf', 
                              'field'=>'Name', 
                              'pk'=>$SalaryperiodconfID, 
                              'value'=> $line->Name, 
                              'width'=>'30', 
                            )) ?>
    </td>
  </tr>
  <tr>
    <td>Bilagsdato</td>
    <td>
      <?=
        $_lib['form3']->text( array(
                              'table'=>'salaryperiodconf', 
                              'field'=>'Voucherdate', 
                              'pk'=>$SalaryperiodconfID, 
                              'value'=> $line->Voucherdate, 
                              'width'=>'10', 
                            )) ?>
    </td>
  </tr>
  <tr>
    <td>Periode</td>
    <td>
      <?=
        $_lib['form3']->text( array(
                              'table'=>'salaryperiodconf', 
                              'field'=>'Period', 
                              'pk'=>$SalaryperiodconfID, 
                              'value'=> $line->Period, 
                              'width'=>'7', 
                            )) ?>
    </td>
  </tr>
  <tr>
    <td>Fra dato</td>
    <td>
      <?=
        $_lib['form3']->text( array(
                              'table'=>'salaryperiodconf', 
                              'field'=>'Fromdate', 
                              'pk'=>$SalaryperiodconfID, 
                              'value'=> $line->Fromdate, 
                              'width'=>'10', 
                            )) ?>
    </td>
  </tr>
  <tr>
    <td>Til dato</td>
    <td>
      <?=
        $_lib['form3']->text( array(
                              'table'=>'salaryperiodconf', 
                              'field'=>'Todate', 
                              'pk'=>$SalaryperiodconfID, 
                              'value'=> $line->Todate, 
                              'width'=>'10', 
                            )) ?>
    </td>
  </tr>

</table>

<h1>L&oslash;nnsmottakere</h1>
<table>
  <tr>
    <th></th>
    <th>Ansatt</th>
  </tr>

  <?
    while($employee = $_lib['db']->db_fetch_object($employee_result))
    {
      echo "<tr>";

      if(isset($entries[ $employee->AccountPlanID ]))
      {
        if($entries[ $employee->AccountPlanID ]['JournalID'] > 0)
        {
          printf( "<td><input type='checkbox' checked disabled /></td>" );
        }
        else
        {
          printf( "<td><input type='checkbox' name='employees[]' value='%d' checked /></td>", $employee->AccountPlanID, $selected );        
        }
      }
      else
      {
        printf( "<td><input type='checkbox' name='employees[]' value='%d' /></td>", $employee->AccountPlanID);
      }
      printf( "<td>%s</td>", $employee->AccountName );
    }
  ?>
</table>

<input type="submit" value="Lagre" name="action_update" />

</form>
