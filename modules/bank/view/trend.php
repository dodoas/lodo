<?
# $Id: trend.php,v 1.10 2005/10/14 13:15:40 thomasek Exp $ account_statistics.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "AccountLine";

if(!$ToDate) {
    $ToDate = $_lib['sess']->get_session('Date');;
}
if(!$FromDate) {
    $FromDate  = $_lib['sess']->get_session('DateFrom');
}

; ?>


<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount trend</title>
    <meta name="cvs"                content="$Id: trend.php,v 1.10 2005/10/14 13:15:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<table class="lodo_data">
  <tr>
    <form name="account" action="<? print $MY_SELF ?>" method="post">
    <th colspan="4" valign="top">Account: <? $_form->Account_menu("AccountNumber", $AccountNumber, $form_name, $where, $db_table); ?>

  <tr>
    <th valign="top">From: <input type="text" name="FromDate" value="<? print "$FromDate"; ?>" size="15">
    <th valign="top" colspan="2">To:   <input type="text" name="ToDate" value="<? print "$ToDate"; ?>" size="15">
    <th align="right"><input type="submit" name="record" value="Find">
    </form>

  <tr>
    <th>Expence category
    <th align="right">Sum inn
    <th align="right">Sum ut
    <th align="right">Balanse
<?
$sum_in  = 0;
$sum_out = 0;
$startyear = 1990;

$aout = array();
$ain  = array();

for($j = $startyear; $j <= 2005; $j++) { #Loop through all years

  for($i = 1; $i <= 12; $i++) { #Loop through all months


    $date = sprintf("%02d-%02d", $j, $i) . "-%";
    #print "$date";

    if($AccountNumber == "all") {
      $query_list  = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from accountline where UseDate like '$date' group by AccountCategory order by AccountCategory";
      $query_total = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from accountline where UseDate like '$date' group by AccountNumber";
    } elseif($AccountNumber == "Active") {
      $query_list  = "select sum(A.AmountIn) as AIn, sum(A.AmountOut) as AOut, A.AccountCategory from accountline as A, account as H where H.Active='1' and H.AccountNumber=A.AccountNumber and A.UseDate like '$date' group by A.AccountCategory order by A.AccountCategory";
      $query_total = "select sum(A.AmountIn) as AIn, sum(A.AmountOut) as AOut, A.AccountCategory from AccountLine as A, account as H where H.Active='1' and H.AccountNumber=A.AccountNumber and A.UseDate like '$date' group by H.Active";
    } elseif($AccountNumber == "closed") {
      $query_list  = "select sum(A.AmountIn) as AIn, sum(A.,AmountOut) as AOut, A.AccountCategory from accountline as A, account as H where H.Active='0' and H.AccountNumber=A.AccountNumber and A.UseDate like '$date' group by A.AccountCategory order by A.AccountCategory";
      $query_total = "select sum(A.AmountIn) as AIn, sum(A.AmountOut)  as AOut, A.AccountCategory from accountline as A, account as H where H.Active='0' and H.AccountNumber=A.AccountNumber and A.UseDate like '$date' group by H.Active";
    } else {
      $query_list  = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from accountline where AccountNumber='$AccountNumber' and UseDate like '$date' group by AccountCategory order by AccountCategory";
      $query_total = "select sum(AmountIn) as AIn, sum(AmountOut) as AOut, AccountCategory from accountline where AccountNumber='$AccountNumber' and UseDate like '$date' group by AccountNumber";
    }

    #print "$query_list<br>";
    $result     = $_lib['db']->db_query($query_list);
    $result_sum = $_lib['db']->db_query($query_total);
    $sum        = $_lib['db']->db_fetch_object($result_sum);


     while($row = $_lib['db']->db_fetch_object($result)) {
       $sum_in  += $row->AIn;
       $sum_out += $row->AOut;
       $aout[$row->AccountCategory][$j][$i] = $row->AOut;
       $ain[$row->AccountCategory][$j][$i]  = $row->AIn;
       #print "<li>$aout:$row->AccountCategory:$j:$i = $row->AOut;<br>";
       ?>
<?  }
  }
}
$count = count($aout);
foreach (array_keys($aout) as $category) {
  print "<tr><td><b>$category</td></tr>";
  for ($j = $startyear; $j <= 2005; $j++) {
     for ($k = 1; $k <= 12; $k++) {
        $balance = $ain[$category][$j][$k] - $aout[$category][$j][$k];
        if($balance != 0){
          print "<tr><td>$j-$k</td><td align=\"right\"><img src=\"/img/pixels/green-pixel.gif\" width=" . $ain[$category][$j][$k]/1000 ."\" height=\"10\" alt=\"" . $ain[$category][$j][$k] . "\"></td>";
          print "<td><img src=\"/img/pixels/black-pixel.gif\" width=" . $aout[$category][$j][$k]/1000 ."\" height=\"10\" alt=\"" . $aout[$category][$j][$k] . "\"></td>";
          print "<td align=\"right\">" . $_lib['format']->amount(array('value'=>$balance, 'return'=>'value')) . "</td></tr>\n";
        }
     }
  }
}

?>
    <tr class="Heading">
      <td>
        Total:

      <td align="right">
        <font color="#00AA00"><? print $sum_in; ?></font>

       <td align="right">
        <font color="#FF0000"><? print $sum_out; ?></font>


    </table>
    </body>
</html>
