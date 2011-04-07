<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

require_once "record.inc";

includelogic('accounting/accounting');
$accounting = new accounting();

$FromDate       = $_lib['input']->getProperty('FromDate');
$ToDate         = $_lib['input']->getProperty('ToDate');
$InvoiceID      = $_lib['input']->getProperty('InvoiceID');
$searchstring   = $_lib['input']->getProperty('searchstring');

if(!$FromDate) {
    $FromDate = $_lib['sess']->get_session('DateStartYear');
}

if(!$ToDate) {
    $ToDate   = $_lib['sess']->get_session('DateEndYear');
}

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC")
{
    $SORT = "DESC";
}
else
{
    $SORT = "ASC";
}

if(!$_SETUP['DB_START']['0'])
{
    $_SETUP['DB_START']['0'] = 0;
}

if (!$order_by)
{
    $order_by = "InvoiceID";
}

$db_stop = $_SETUP['DB_START']['0'] + $_SETUP['DB_OFFSET']['0'];

if (!$SORT || $SORT == "ASC")
{
    $SORT = "DESC";
}
else
{
    $SORT = "ASC";
}

/* Sokestreng */
$query  = "select i.* from invoiceout as i where ";
 if($FromDate) {
     $query .= " i.InvoiceDate >= '$FromDate' and ";
 }
 if($ToDate) {
     $query .= " i.InvoiceDate <= '$ToDate' and ";
 }

 if($InvoiceID) {
     $query .= " i.InvoiceID='$InvoiceID' and ";
 }

if($searchstring){
    $query .= " i.IName like '%$searchstring%' and ";
}

$query = substr($query, 0, -4);

# Work around to avoid mysql stall query because of double space before order statement
if (substr($query, -1) == " ") {
        $query = substr($query, 0, -1);
}

$query  .= " order by InvoiceID desc";

#print "$query<br>\n";
$result_inv = $_lib['db']->db_query($query);

$query_count = "SELECT COUNT(*) as total, sum(TotalCustPrice) as sum FROM $db_table $where";
$result_count = $_lib['db']->db_query($query_count);
$row = $_lib['db']->db_fetch_object($result_count);


$db_total = $row->total;
$db_sum   = $row->sum;
?>


    <? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2><a href="<? print $_lib['sess']->dispatch ?>t=invoice.listoutgoing">Faktura - Liste</a> <? if($_lib['sess']->get_person('FakturabankImportInvoiceAccess')) { ?> / <a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.listoutgoing">Hent utg&aring;ende fakturaer fra fakturabank</a></h2> <? } ?>
<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>

<table>
<thead>
<tr>
     <form name="invoice_list" action="<? print $_lib['sess']->dispatch ?>t=invoice.listoutgoing" method="post">
     <td>
        Kundenavn:   <input type="text" value="<? print $searchstring ?>" name="searchstring" size="10"/>
        Fakturanummer: <? print $_lib['form3']->text(array('name' => 'InvoiceID',   'value' => $InvoiceID)) ?>
        Fra:    <? print $_lib['form3']->date(array('name' => 'FromDate',           'value' => $FromDate)) ?>
        Til:    <? print $_lib['form3']->date(array('name' => 'ToDate',             'value' => $ToDate)) ?>
        <? print $_lib['form3']->submit(array('name' => 'show_search',   'value' => 'S&oslash;k (S)')) ?>
        <input type="hidden" value="edit" name="inline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

     </td>
     </form>

</tr>
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
<tr>
    <form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=invoice.edit" method="post">
    <td>
	Fakturadato:
	<? 
	  $voucher_date = $_COOKIE['invoice_voucher_date']; 
	  if($voucher_date == "")
	    $voucher_date = date("Y-m-d");

          if(isset($_COOKIE['invoice_period']))
            $invoice_period = $_COOKIE['invoice_period'];
          else
            $invoice_period = date("Y-m");    
	?>
	<? print $_lib['form3']->date(array('name' => 'voucher_date',           'value' => $voucher_date)) ?>	
	Periode:
	<?
	print $_lib['form3']->AccountPeriod_menu3(array('table' => 'voucher', 'field' => 'period', 'value' => $invoice_period,
	'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => ''));
	?>

        <input type="hidden" value="edit" name="inline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="submit" value="Lagre dato" name="action_auto_save">
        <?
	if($accounting->is_valid_accountperiod($_COOKIE['invoice_period'], $_lib['sess']->get_person('AccessLevel'))) {
            list($nextJournalID, $nextMessage) = $accounting->get_next_available_journalid(array('type'=>'S', 'available' => true));

            echo ' fakturanummer: ' . $nextJournalID;
	    echo '<input type="submit" value="Ny faktura (N)" name="action_invoice_new" accesskey="N">';
        }
        else {
            echo '<i>Du m&aring; velge en &aring;pen periode for &aring; lage ny faktura</i>';
        }
        ?>

    </td>
    </form>
</tr>
<? } ?>
</table>
<table>
<tr>
    <th>
        <?
        if($_SETUP['DB_START']['0'] > 0)
            {?> <a href="<? print $MY_SELF ?>&amp;DB_START=<? print $_SETUP['DB_START']['0']-$_SETUP['DB_OFFSET']['0']; ?>&amp;searchstring=<? print "$searchstring"; ?>&amp;InvoiceStatus=<? print "$InvoiceStatus"; ?>&amp;order_by=<? print "$order_by"; ?>&amp;sort=<? print "$SORT"; ?>" title="forrige">&lt;&lt;</a> <?}
        else
            {?> &lt;&lt; <?}
        ?>
    <th colspan="2">Fant:<? print $db_total ?>, viser <? print $_SETUP['DB_START']['0']." til ".$db_stop; ?></th>
    <th colspan="10">Total salg: <? print $_lib['format']->Amount(array('value'=>$db_sum, 'return'=>'value')) ?></th>
    <th align="right">
        <?
        if($db_total > $db_stop)
            {?> <a href="<? print $MY_SELF ?>&amp;DB_START=<? print $_SETUP['DB_START']['0']+$_SETUP['DB_OFFSET']['0']; ?>&amp;searchstring=<? print "$searchstring"; ?>&amp;InvoiceStatus=<? print "$InvoiceStatus"; ?>&amp;order_by=<? print "$order_by"; ?>&amp;sort=<? print "$SORT"; ?>" title="next">&gt;&gt;</a> <?}
        else
            {?> &gt;&gt; <?}
        ?>
    </th>
</tr>
<tr>
    <th align="right">Faktura nr</th>
    <th align="right">Utskriftsdato</th>
    <th align="right">Fakturadato</th>
    <th align="right">Periode</th>
    <!--<th>OrdreRef-->
    <th align="right">Kunde nr</th>
    <th>Firmanavn</th>
    <th>Avdeling</th>
    <th>Prosjekt</th>
    <th>Kommentar</th>
    <th align="right">Forfallsdato</th>
    <th align="right">Total</th>
    <th align="right">Utskrift</th>
    <th align="right">Endre</th>
    <th align="right"></th>
    <th align="right"></th>
</tr>
</thead>

<tbody>

<?
while($row = $_lib['db']->db_fetch_object($result_inv))
{
    $print_sql = "SELECT * FROM invoiceoutprint WHERE InvoiceID = " . $row->InvoiceID;
    $printinfo = $_lib['db']->db_fetch_assoc( $_lib['db']->db_query($print_sql) );
    $printdate = $printinfo['InvoicePrintDate'];

    if($printdate == "0000-00-00")
	$printdate = "";

    $i++;
    if (!($i % 2))
    {
        $sec_color = "BGColorLight";
    }
    else
    {
        $sec_color = "BGColorDark";
    }
  $_lib['sess']->debug("ja");
  $TotalCustPrice += $row->TotalCustPrice;
  ?>
  <form name="invoice" action="<? print $MY_SELF ?>" method="post">
    <tr class="<? print $sec_color ?>">
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=invoice.edit&InvoiceID=<? print $row->InvoiceID ?>&inline=show" title="Vis/Endre faktura informasjon"><? print $row->InvoiceID ?></a></td>
      <td class="number"><? print $printdate ?></td>
      <td class="number"><? print substr($row->InvoiceDate,0,10) ?></td>
      <td class="number"><? print $row->Period ?></td>
      <!--<td><? print $row->OrderRef; ?>-->
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&accountplan.AccountPlanID=<? print $row->CustomerAccountPlanID ?>&inline=show"><? print $row->CustomerAccountPlanID ?></a></td></td>
      <td>&nbsp;<a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&accountplan.AccountPlanID=<? print $row->CustomerAccountPlanID ?>&inline=show"><? print substr($row->IName,0,30) ?></a></td>
      <td><? $department = $accounting->get_department_object($row->DepartmentID); print "$department->CompanyDepartmentID - $department->DepartmentName"; ?></td>
      <td><? $project    = $accounting->get_project_object($row->ProjectID); print "$project->ProjectID - $project->Heading"; ?></td>
      <td><? print substr($row->CommentCustomer,0,30) ?></td>
      <td class="number"><b><? print substr($row->DueDate,0,10) ?></b></td>
      <td class="number"><? print $_lib['format']->Amount($row->TotalCustPrice) ?></td>
      <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=invoice.print&InvoiceID=<? print $row->InvoiceID ?>&inline=show" title="Vis/SkrivUt faktura for produkt" target="_new">Faktura</a>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoice.print2&InvoiceID=<? print $row->InvoiceID ?>&inline=show" title="Vis/SkrivUt faktura for produkt som PDF fil">PDF</a><br /></td>
      <td align="center">
      <? if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')) && $_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoice.edit&InvoiceID=<? print $row->InvoiceID ?>&inline=edit" title="Endre faktura" target="_new">Endre</a><br />
      <? } else if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoice.edit&InvoiceID=<? print $row->InvoiceID ?>&inline=edit" title="Endre faktura" target="_new">Endre stengt</a><br />
      <? } else { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoice.edit&InvoiceID=<? print $row->InvoiceID ?>&inline=show" title="Endre faktura" target="_new">Stengt</a><br />
      <? } ?>
      </td>
      <td class="number"><? if($row->Locked) { ?>L&aring;st<? } else { ?>&Aring;pen<? } ?></td>
      <td class="number"><? if($row->ExternalID > 0) { ?><a href="https://fakturabank.no/invoices/<? print $row->ExternalID ?>" title="Vis i Fakturabank" target="_new">Vis i fakturabank</a><? } ?></td>
  </form>
  </tr>
  <?
}
?>
<tr>
    <td colspan="9"></td>
    <td class="number"><? print $TotalCustPrice ?></td>
    <td></td>
</tr>
</tbody>

</table>
</body>
</html>


