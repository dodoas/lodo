<?
#Search for kid reference or amount
$searchstring       = $_REQUEST['searchstring'];
$JournalID          = (int) $_REQUEST['JournalID'];
$VoucherID          = (int) $_REQUEST['VoucherID'];
$VoucherSearchType  = $_REQUEST['VoucherSearchType'];
$type      		    = $_REQUEST['type'];

#################################################################################################################
#Search fasilities

if(strlen($searchstring) > 0 && 1 != 1)
{
    #Find amount
    $_showresult = "<table>";
  
    #Find all open posts defined on this customer
    #$query = "select AmountIn, AmountOut, JournalID, KID, InvoiceID, VoucherDate from voucher as v, voucherstruct as s where (v.AmountIn = '$Amount' or v.AmountOut = '$Amount') and (v.JournalID=s.Parent or v.JournalID=s.Child) and Closed=0";
    $_lib['sess']->debug("VoucherSearchType: $VoucherSearchType");
    if($VoucherSearchType == 'Amount') {
        $query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where (v.AmountIn = '$searchstring' or v.AmountOut = '$searchstring') and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' || a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
    } 
    elseif($VoucherSearchType == 'AmountIn') {
        $query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where v.AmountIn = '$searchstring and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' || a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
    }
    elseif($VoucherSearchType == 'AmountOut') {
        $query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where v.AmountOut = '$searchstring' and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' || a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
    }
    elseif($VoucherSearchType == 'KID') {
        $query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where KID = '$searchstring' and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' || a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
    } 
    else {
        print "Missing search type";
    }
    
    
    
    #print "$query<br>";
    #$result_searcha = $_lib['db']->db_query($query);
}
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - journal search</title>
    <meta name="cvs"                content="$Id: search.php,v 1.10 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<!--
<body onload="window.focus();">
<h2>S&oslash;kebegrep: <? print $searchstring ?>, treff  <? #print $_lib['db']->db_numrows($result_search) ?></h2>
-->
<body>
<h2>S&oslash;kebegrep: <? print $searchstring ?>, treff  <? print $_lib['db']->db_numrows($result_searcha) ?></h2>

<table class="lodo_data">
<thead>
<tr>
  <th>Type</th>
  <th>Bilag</th>
  <th>Dato</th>
  <th>Konto</th>
  <th>Navn</th>
  <th>Debet</th>
  <th>Kredit</th>
  <th>KID</th>
  <th></th>
</tr>
</thead>

<tbody>
<?
print "<h2>VoucherID: $VoucherID</h2><br>\n";
if($VoucherID > 0) {
    while($row = $_lib['db']->db_fetch_object($result_searcha))
    { 
      #$accounting->sum_journal();
      #$line = $_lib['storage']->get_row(array('query' => 'select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut '));  
    ?>
        <tr>
            <td><? print $row->VoucherType ?></td>
            <td><? print $row->JournalID ?></td>
            <td><? print $row->VoucherDate ?></td>
            <td><? print $row->AccountPlanID ?></td>
            <td><? print $row->AccountName ?></td>
            <td><? print $row->AmountIn ?></td>
            <td><? print $row->AmountOut ?></td>
            <td><? print $row->KID ?></td>
            <form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>&amp;t=journal.edit=" method="post">
            <input type="hidden"  name="type" 					value="<? print $type ?>">
            <input type="hidden"  name="voucher.JournalID" 		value="<? print $JournalID ?>">
            <input type="hidden"  name="voucher.VoucherID" 		value="<? print $VoucherID ?>">
            <input type="hidden"  name="voucher.AccountPlanID" 	value="<? print $row->AccountPlanID ?>">
            <input type="hidden"  name="voucher.AmountIn" 		value="<? print $row->AmountOut ?>">
            <input type="hidden"  name="voucher.AmountOut" 		value="<? print $row->AmountIn ?>">
            <input type="hidden"  name="voucher.KID" 		    value="<? print $row->KID ?>">
            <td><input type="submit"  name="action_voucher_update" value="Velg"></td>
            </form>
        </tr>
 <? }
}
?>
</table>
</body>
</html>