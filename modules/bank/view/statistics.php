<?
# $Id: statistics.php,v 1.12 2005/10/28 17:59:40 thomasek Exp $ account_statistics.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$FromDate 		= $_REQUEST['FromDate'];
$ToDate 		= $_REQUEST['ToDate'];
$AccountPlanID 	= $_REQUEST['AccountPlanID'];
$AccountID 		= $_REQUEST['AccountID'];

$db_table  = "accountline";

if(!$ToDate) {
	$ToDate = $_lib['sess']->get_session('Date');
}
if(!$FromDate) {
	$FromDate  = $_lib['sess']->get_session('DateFrom');
}

if($AccountNumber == "all") {
	$query_list  = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from $db_table where UseDate >= '$FromDate' and UseDate <= '$ToDate' group by AccountPlanID order by AccountPlanID";
	$query_total = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from $db_table where UseDate >= '$FromDate' and UseDate <= '$ToDate' group by AccountNumber";
} elseif($AccountNumber == "Active") {
	$query_list  = "select sum(A.AmountIn) as AIn, sum(A.AmountOut) as AOut, A.AccountCategory from $db_table as A, Account as H where H.Active='1' and H.AccountNumber=A.AccountNumber and A.UseDate >= '$FromDate' and A.UseDate <= '$ToDate' group by A.AccountPlanID order by A.AccountPlanID";
	$query_total = "select sum(A.AmountIn) as AIn, sum(A.AmountOut) as AOut, A.AccountCategory from $db_table as A, Account as H where H.Active='1' and H.AccountNumber=A.AccountNumber and A.UseDate >= '$FromDate' and A.UseDate <= '$ToDate' group by H.Active";
} elseif($AccountNumber == "closed") {
	$query_list  = "select sum(A.AmountIn) as AIn, sum(A.,AmountOut) as AOut, A.AccountCategory from $db_table as A, Account as H where H.Active='0' and H.AccountNumber=A.AccountNumber and A.UseDate >= '$FromDate' and A.UseDate <= '$ToDate' group by A.AccountPlanID order by A.AccountPlanID";
	$query_total = "select sum(A.AmountIn) as AIn, sum(A.AmountOut)  as AOut, A.AccountCategory from $db_table as A, Account as H where H.Active='0' and H.AccountNumber=A.AccountNumber and A.UseDate >= '$FromDate' and A.UseDate <= '$ToDate' group by H.Active";
} else {
	$query_list  = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from $db_table where AccountNumber='$AccountNumber' and UseDate >= '$FromDate' and UseDate <= '$ToDate' group by AccountPlanID order by AccountPlanID";
	$query_total = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from $db_table where AccountNumber='$AccountNumber' and UseDate >= '$FromDate' and UseDate <= '$ToDate' group by AccountNumber";
}

$result 	= $_lib['db']->db_query($query_list);
$result_sum = $_lib['db']->db_query($query_total);
$sum 		= $_lib['db']->db_fetch_object($result_sum);
?>

<? print $_lib['sess']->doctype ?>
<head>
	<title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount list</title>
	<meta name="cvs"     		    content="$Id: statistics.php,v 1.12 2005/10/28 17:59:40 thomasek Exp $" />
	<? includeinc('head') ?>
</head>

<body>
<table cellspacing="0">
  <tr>
    <form name="account" action="<? print $MY_SELF ?>" method="post">
    <th colspan="3" valign="top">Account: <? $_form->Account_menu("AccountNumber", $AccountNumber, $form_name, $where, $db_table); ?>

  <tr> 
    <th valign="top">From: <input type="text" name="FromDate" value="<? print "$FromDate"; ?>" size="15"></th>	
    <th valign="top">To:   <input type="text" name="ToDate" value="<? print "$ToDate"; ?>" size="15"></th>
	<th align="right"><input type="submit" name="record" value="Find"></th>
	</form>
  </tr>

  <tr> 
    <th>Expence category</th>		
    <th align="right">Sum inn</th>
	<th align="right">Sum ut</th>
	
<?
$sum_in  = 0;
$sum_out = 0;
while($row = $_lib['db']->db_fetch_object($result)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
$sum_in  += $row->AIn;
$sum_out += $row->AOut;
?>  	
    <tr class="<? print "$sec_color"; ?>"> 
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.edit&AccountID=<? print $AccountID ?>&FromDate=<? print $FromDate ?>&ToDate=<? print $ToDate ?>&AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountPlanID ?></a></td>
      <td align="right"><font color="#00AA00"><? print $row->AIn; ?></font></td>
      <td align="right"><font color="#FF0000"><? print $row->AOut; ?></font></td>
      
	
<? } ?>
    <tr class="Heading"> 
      <td> Total:</td>
      <td align="right"><font color="#00AA00"><? print $sum_in; ?></font></td>
      <td align="right"><font color="#FF0000"><? print $sum_out; ?></font></td>
    </tr>
</table>
</body>
</html>
