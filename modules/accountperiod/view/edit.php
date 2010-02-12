<?
#require_once  "accountperiod_record.inc";

#table
$db_table = "accountperiod";
require_once "record.inc";

#Get period data
$sql_period    = "select * from $db_table order by Period desc";
$result_period = $_lib['db']->db_query($sql_period);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - accountperiod</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.34 2005/10/24 11:54:33 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">

<table class="lodo_data">
  <tr>
    <td><h2>Periode</h2></td>
    <td colspan="5" align="right">
        <?
        if($_lib['sess']->get_person('AccessLevel') > 1)
        {
            print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_accountperiod_newAfter', 'value'=>"Legg til perioden: $nextperiod"));
            print "<br>";
            print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_accountperiod_newBefore', 'value'=>"Legg til perioden: $prevperiod"));
        }
        ?>
    </td>
  </tr>
    <tr>
      <td colspan="6" align="right"><? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_accountperiod_update', 'value'=>'Lagre periode (S)', 'accesskey' => 'S')) ?></td>
    </tr>
  <tr>
    <th>Periode</th>
    <th>Lukket</th>
    <th>Åpen</th>
    <th>Ferdig</th>
    <th>Stengt</th>
  </tr>
    <?
    while($row = $_lib['db']->db_fetch_object($result_period)) {
      $where = "AccountPeriodID=$row->AccountPeriodID";
      $i++; $form_name = "accountperiod_$i";
      if (!($i % 2)) {  $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; }; ?>
      <tr>
        <td><? print $row->Period ?>
        <td><? if($row->Status <= 1 and $_lib['sess']->get_person('AccessLevel') > 1) { print $_lib['form3']->radiobutton(array('table'=>$db_table, 'field'=>'Status', 'pk'=>$row->AccountPeriodID, 'choice'=>$row->Status, 'value'=>'1')); } elseif($row->Status == 1) { print "<input type=\"checkbox\" checked disabled>"; } ?></td>
        <td><? if($row->Status <= 2 and $_lib['sess']->get_person('AccessLevel') > 1) { print $_lib['form3']->radiobutton(array('table'=>$db_table, 'field'=>'Status', 'pk'=>$row->AccountPeriodID, 'choice'=>$row->Status, 'value'=>'2')); } elseif($row->Status == 2) { print "<input type=\"checkbox\" checked disabled>"; } ?></td>
        <td><? if($row->Status <= 3 and $_lib['sess']->get_person('AccessLevel') > 1) { print $_lib['form3']->radiobutton(array('table'=>$db_table, 'field'=>'Status', 'pk'=>$row->AccountPeriodID, 'choice'=>$row->Status, 'value'=>'3')); } elseif($row->Status == 3) { print "<input type=\"checkbox\" checked disabled>"; } ?></td>
        <td><? if($row->Status <= 4 and $_lib['sess']->get_person('AccessLevel') > 1) { print $_lib['form3']->radiobutton(array('table'=>$db_table, 'field'=>'Status', 'pk'=>$row->AccountPeriodID, 'choice'=>$row->Status, 'value'=>'4')); } elseif($row->Status == 4) { print "<input type=\"checkbox\" checked disabled>"; } ?></td>
      </tr>
    <? }
    if($_lib['sess']->get_person('AccessLevel') > 1)
    {
    ?>
    <tr>
      <td colspan="6" align="right"><? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_accountperiod_update', 'value'=>'Lagre periode (S)', 'accesskey' => 'S')) ?></td>
    </tr>
    <?
    }
    ?>
</table>

</form>

<? includeinc('bottom') ?>
</body>
</html>
