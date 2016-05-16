<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>

</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<?php

include('reportcodes.php');
$year = $_GET['year'];

includemodel('salary/salaryreport');
$salaryreport = new salaryreport(array('year'=>$year, 'employeeID'=>$_GET['AccountPlanID'], 'altinn_only' => true));

?>
<h1>Innberetning</h1>
<form action="<?= $_lib['sess']->dispatch ?>t=salary.employeereport&year=<?= $year ?>" method="post">
  <table class="lodo_data">
    <tr>
      <th>Date</th>
<?php
foreach($codes as $code) {
    printf("<th>%s</th>", $code);
}

?>
    <th></th>
    </tr>
    <tr>
      <td><input type="text" name="add_date" value="<? print date("Y-m-d") ?>" />
  <?php

    foreach($codes as $code) {
        printf('
          <td><input type="text" name="add_amounts[%s]" value="%d" /></td>
        ', 
               $code,
               $salaryreport->_reportHash['head'][$code]['sumLineCode']
            );
    } 

  ?>
    <td>
      <input type="hidden" value="<?= $_GET['AccountPlanID'] ?>" name="AccountPlanID" />
      <input type="submit" value="Lagre" name="add_report" />
    </td>
    </tr>
  </table>
</form>
