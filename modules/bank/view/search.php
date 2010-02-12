<?
# $Id: search.php,v 1.16 2005/10/14 13:15:40 thomasek Exp $ account.php,v 1.5 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$AccountID 			= $_REQUEST['AccountID'];
$AccountDescription = $_REQUEST['AccountDescription'];
$Amount 			= $_REQUEST['Amount'];
$FromDate 			= $_REQUEST['FromDate'];
$ToDate 			= $_REQUEST['ToDate'];

$db_table   = "account";
$db_table2  = "accountline";

require_once  "record.inc";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "DESC") {$SORT = "ASC";} else { $SORT = "DESC";}
if(!$_SETUP[DB_START][0]) { $_SETUP[DB_START][0] = 0;}
$db_stop = $_SETUP[DB_START][0] + $_SETUP[DB_OFFSET][0] - 1;
if (!$order_by) { $order_by = "al.UseDate"; }


$select = "select al.*, a.AccountNumber from $db_table2 as al, account as a";
if($Amount){
  $where  = " where a.AccountID=al.AccountID and al.AccountID like '$AccountID' and (al.AmountIn='$Amount' or al.AmountOut='$Amount') order by $order_by $SORT";
} elseif($AccountDescription) {
  $where  = " where a.AccountID=al.AccountID and al.AccountID like '$AccountID' and al.AccountDescription like '%$AccountDescription%' order by $order_by $SORT";
} elseif($FromDate) {
  $where  			= " where a.AccountID=al.AccountID and al.AccountID like '$AccountID' and al.UseDate >= '$FromDate' and al.UseDate <= '$ToDate' order by $order_by $SORT";
  $query_total 		= "select sum(al.AmountIn) as AmountIn, sum(al.AmountOut) as AmountOut FROM $db_table2 as al where al.AccountID like '$AccountID' and al.UseDate < '$FromDate'";
  $row_total 		= $_lib['storage']->get_row(array('query' => $query_total));
  #print_r($row_total);
  $year_total		= $row_total->AmountIn - $row_total->AmountOut;

} else {
  $where  = " where UseDate=NOW() order by $order_by $SORT";
}
#print "$select $where<br>";
$result = $_lib['db']->db_query("$select $where");

?>
<? print $_lib['sess']->doctype ?>
<head>
	<title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount list</title>
	<meta name="cvs"     		    content="$Id: search.php,v 1.16 2005/10/14 13:15:40 thomasek Exp $" />
	<? includeinc('head') ?>
</head>

<body>

<table class="lodo_data">
<thead>
<tr>
<th colspan="2">
Search Account
    <form name="menu" action="<? print $MY_SELF ?>" method="post">
    <th>Account: 	<? $_form->Account_menu("AccountID", $AccountID, $form_name, $where, $db_table); ?></th>
    <th>FromDate: 	<input type="text" size="12" name="FromDate" 			value="<? print $FromDate ?>"></th>
	<th>ToDate: 	<input type="text" size="12" name="ToDate" 				value="<? print $ToDate ?>"></th>
	<th>Amount: 	<input type="text" size="5" name="Amount" 				value="<? print $Amount ?>"></th>
	<th>Text: 		<input type="text" size="5" name="AccountDescription" 	value="<? print $AccountDescription ?>"></th>
    <th colspan="2">
    <input type="submit" value="Search" name="<? print $MY_SELF ?>" tabindex="1" accesskey="N">
	</form>
	</th>
</tr>

<tr class="Heading">
    <td>AccountID</td>
    <td width="100">UseDate</td>
    <td>Description</td>
	<td>Out</td>
    <td>In</td>
    <td>Category</td>
    <td>P</td>
    <td>A</td>
    <td>D</td>
</tr>
</thead>
<tbody>
<tr>
<td colspan="4">
<?
$saldo = $year_total;

while($row = $_lib['db']->db_fetch_object($result)) {
$i++; $form_name = "account_$i";
$period = $_lib['date']->get_this_period($row->UseDate);
if(!$period or $period != $period_old) {
  $period_old = $period; ?>
  <tr><th colspan="3">Saldo pr <? print $_lib['date']->get_prev_period(array('value' => $period, 'realPeriod' => 1)) ?></th><th colspan="2"><? print $_lib['format']->Amount(array('value' => $saldo, 'return' => 'value')) ?></th></tr>
  <tr>
  <th colspan="3">Saldo i periode <? print $_lib['date']->get_prev_period(array('value' => $period, 'realPeriod' => 1)) ?></th>
  <th align="right" colspan="2"><nobr><? print $_lib['format']->Amount(array('value'=>$saldo_period, 'return'=>'value')) ?></nobr></th>
</tr>
  <?
   $saldo_period = 0;
}

if (!($i % 2)) {	$sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
$where = "AccountLineID=$row->AccountLineID";
?>  
<tr class="<? print "$sec_color"; ?>">
  <td><? print $row->AccountNumber ?></td>
  <td><nobr><? print $row->UseDate ?></nobr></td>
  <td><? print $row->AccountDescription ?>
  <td class="number"><? if($row->AmountOut != 0) { print $_lib['format']->Amount(array('value'=>$row->AmountOut, 'return'=>'value')); }; ?></td>
  <td class="number"><? if($row->AmountIn  != 0) { print $_lib['format']->Amount(array('value'=>$row->AmountIn, 'return'=>'value')) ; }; ?></td>
  <td><? print $row->AccountCategory ?></td>
  <td><? print $row->ProjectID ?></td>
  <td><? print $row->ProjectActivityID ?></td>
  <td></td>
  </tr>
  <? 
  $sumIn  			+= $row->AmountIn;
  $sumOut 			+= $row->AmountOut;
  $saldo  			+= ($row->AmountIn - $row->AmountOut);
  $saldo_period 		+= ($row->AmountIn - $row->AmountOut);
  $saldo_sum_period 	+= ($row->AmountIn - $row->AmountOut);
  #print "$period: p: $saldo_period x $saldo_sum_period<br>";
} ?>
<tr>
<td>
<td>
<td>Total
<td><nobr><? print $_lib['format']->Amount(array('value' => $sumOut, 'return' => 'value')) ?></nobr></td>
<td><nobr><? print $_lib['format']->Amount(array('value' => $sumIn,  'return' => 'value')) ?></nobr></td>
<td>
<td></td>
</tr>

<tr>
  <th colspan="3">Saldo pr <? print $period ?></th>
  <th align="right" colspan="2"><nobr><? print $_lib['format']->Amount(array('value'=>$saldo, 'return'=>'value')) ?></nobr></th>
</tr>
<tr>
<tr>
  <th colspan="3">Saldo i periode <? print $period ?></th>
  <th align="right" colspan="2"><nobr><? print $_lib['format']->Amount(array('value'=>$saldo_period, 'return'=>'value')) ?></nobr></th>
</tr>
<tr>
	<td><br /><br /></td>
</tr>
<tr>
  <th colspan="3">Saldo for periode <? print $FromDate ?> - <? print $ToDate ?></th>
  <th align="right" colspan="2"><nobr><? print $_lib['format']->Amount(array('value'=>$saldo_sum_period, 'return'=>'value')) ?></nobr></th>
</tr>

</tbody>	
</table>
</body>
</html>
