<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
includelogic('accounting/accounting');
$accounting = new accounting();
$SearchInvoiceID = $_POST["SearchInvoiceID"];
require_once "record.inc";

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
$query  = "select #[select] from invoiceout as i where ";
 if($FromDate) {
     $query .= " i.InvoiceDate >= '$FromDate' and ";
 }
 if($ToDate) {
     $query .= " i.InvoiceDate <= '$ToDate' and ";
 }

 if($SearchInvoiceID) {
     $query .= " i.InvoiceID='$SearchInvoiceID' and ";
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

$query_for_ids = str_replace("#[select]", "i.InvoiceID", $query);
$query = str_replace("#[select]", "i.*", $query);

// print "$query<br>\n";
// print "$query_for_ids<br>\n";
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
        Fakturanummer: <? print $_lib['form3']->text(array('name' => 'SearchInvoiceID',   'value' => $SearchInvoiceID)) ?>
        Fra:    <? print $_lib['form3']->date(array('name' => 'FromDate', 'field' => 'FromDate', 'form_name' => 'invoice_list', 'value' => $FromDate)) ?>
        Til:    <? print $_lib['form3']->date(array('name' => 'ToDate', 'field' => 'ToDate', 'form_name' => 'invoice_list', 'value' => $ToDate)) ?>
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
  <? print $_lib['form3']->date(array('name' => 'voucher_date', 'field' => 'voucher_date', 'form_name' => 'invoice_edit', 'value' => $voucher_date)) ?>
  Periode:
  <?
  print $_lib['form3']->AccountPeriod_menu3(array('table' => 'voucher', 'field' => 'period', 'value' => $invoice_period,
  'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true));
  ?>

        <input type="hidden" value="edit" name="inline">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <? print $_lib['form3']->submit(array('name' => 'action_auto_save','value' => "Lagre dato")) ?>
        <?
  $valid_accountperiod = $accounting->is_valid_accountperiod($invoice_period, $_lib['sess']->get_person('AccessLevel'));
  $valid_date = validDate($voucher_date);
  if ($valid_accountperiod && $valid_date) {
    list($nextJournalID, $nextMessage) = $accounting->get_next_available_journalid(array('type'=>'S', 'available' => true));
    echo ' fakturanummer: ' . $nextJournalID;
    print $_lib['form3']->submit(array('name' => 'action_invoice_new', 'value' => "Ny faktura (N)", 'accesskey'=>"N"));
  }
  if (!$valid_accountperiod && !$valid_date) {
    echo '<i>Du m&aring; velge en &aring;pen periode og skrive en gyldig fakturadato for &aring; lage ny faktura</i>';
  }
  elseif (!$valid_accountperiod) {
    echo '<i>Du m&aring; velge en &aring;pen periode for &aring; lage ny faktura</i>';
  }
  elseif (!$valid_date) {
    echo '<i>Du m&aring; skrive en gyldig fakturadato for &aring; lage ny faktura</i>';
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
    <th colspan="13">Total salg: <? print $_lib['format']->Amount(array('value'=>$db_sum, 'return'=>'value')) ?></th>
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
    <th align="right">Linjekontroll</th>
    <th align="right"></th>
</tr>
</thead>

<tbody>

<?
// The query below creates the voucher lines that should exist based on the invoice lines.
// Then we do the same thing from the voucher lines and concatenate the two results.
// Group them and count the duplicates.
// If the count is an odd number then we either have some extra lines or some of the lines are missing.
// That is why we restrict the result to the lines having an odd count(or
// other than 2 for the total line), so we get left with the ones that are wrong.
// If everything is correct, this query should return an empty result, if not - we have an error.


$query_line_control = "
SELECT DISTINCT(JournalID) FROM (
  SELECT *, COUNT(*) AS count
     FROM (
       SELECT *
       FROM (
         -- Create voucher lines for invoice lines in invoiceoutline table
         SELECT 'Regular' AS Type, il.LineID, il.Vat AS tmpVat,
         -- Calculate TotalAmount for each line of the invoice, and take in account if it is a credit note to switch the amounts
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, 0, ROUND(il.QuantityDelivered * il.UnitCustPrice, 2) * (1 + (il.Vat/100)) * -1), 2) AS AmountIn,
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, ROUND(il.QuantityDelivered * il.UnitCustPrice, 2) * (1 + (il.Vat/100)), 0), 2) AS AmountOut,
         il.InvoiceID AS JournalID
         FROM invoiceoutline il
         WHERE il.Active = 1 AND il.QuantityDelivered <> 0 AND il.UnitCustPrice <> 0 AND il.InvoiceID in ($query_for_ids)

         UNION

         -- Create Vat voucher lines for invoice lines from invoiceoutline table once for the vat line and once more for the counterpart line
         SELECT 'VAT' AS Type, il.LineID, 0 AS tmpVat,
         -- Calculate TaxAmount since it is not available for all entries in the invoiceoutline table, also taking in account for credit note
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, 0, il.QuantityDelivered * il.UnitCustPrice * il.Vat / 100 * -1), 2) AS AmountIn,
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, il.QuantityDelivered * il.UnitCustPrice * il.Vat / 100, 0), 2) AS AmountOut,
         il.InvoiceID AS JournalID
         FROM invoiceoutline il
         WHERE il.Active = 1 AND il.Vat <> 0 AND il.QuantityDelivered <> 0 AND il.UnitCustPrice <> 0 AND il.InvoiceID in ($query_for_ids)

         UNION

         SELECT 'VAT' AS Type, il.LineID, 0 AS tmpVat,
         -- Calculate TaxAmount since it is not available for all entries in the invoiceoutline table, also taking in account for credit note
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, il.QuantityDelivered * il.UnitCustPrice * il.Vat / 100, 0), 2) AS AmountIn,
         ROUND(IF(il.QuantityDelivered * il.UnitCustPrice > 0, 0, il.QuantityDelivered * il.UnitCustPrice * il.Vat / 100 * -1), 2) AS AmountOut,
         il.InvoiceID AS JournalID
         FROM invoiceoutline il
         WHERE il.Active = 1 AND il.Vat <> 0 AND il.QuantityDelivered <> 0 AND il.UnitCustPrice <> 0 AND il.InvoiceID in ($query_for_ids)

         UNION

         -- Total amount line for invoice
         SELECT 'Total' AS Type, 0 AS LineID, 0 AS tmpVat,
         -- Take in account amount for credit note
         IF(i.TotalCustPrice > 0, i.TotalCustPrice, 0) AS AmountIn,
         IF(i.TotalCustPrice > 0, 0, i.TotalCustPrice * -1) AS AmountOut,
         i.InvoiceID AS JournalID

         FROM invoiceout i
         WHERE i.TotalCustPrice <> 0 AND i.InvoiceID in ($query_for_ids)
       ) li

       UNION ALL

       -- Create the same as above only from lines in the voucher table
       SELECT
       -- Determine type from the voucher line data
       CASE
         WHEN ta.AutomaticReason LIKE 'Automatisk % MVA%' THEN 'VAT'
         WHEN ta.AccountPlanType = 'customer' THEN 'Total'
         WHEN ta.AutomaticReason LIKE 'Faktura%' AND ta.AccountPlanID >= 3000 && ta.AccountPlanID <= 3999 THEN 'Regular'
         ELSE 'SOMETHING_IS_WRONG'
       END AS Type,
       '' AS LineID,
       ta.vat AS tmpVat,
       ta.AmountIn AS AmountIn,
       ta.AmountOut AS AmountOut,
       ta.JournalID AS JournalID
       FROM (
         SELECT v.VoucherID, v.JournalID, v.VoucherType, v.AmountIn, v.AmountOut, v.AccountPlanID, v.Vat, v.Description, v.Active, v.AutomaticFromVoucherID, v.AutomaticReason, v.AutomaticVatVoucherID, v.InvoiceID, ap.AccountPlanType
         FROM voucher v
         JOIN accountplan ap ON v.AccountPlanID = ap.AccountPlanID
         WHERE
         -- Exclude the hovedbok lines
         ap.EnableReskontro = 0 AND v.VoucherType = 'S' AND v.Active = 1 AND v.JournalID in ($query_for_ids)
       ) ta
     ) taa
     -- Group the same so we can count the duplicates
     GROUP BY Type, tmpVat, AmountIn, AmountOut, JournalID
     -- Leave only the ones that were oddly paired, and the total line that has other than count of 2
     HAVING ((count % 2) = 1) OR (count <> 2 AND Type = 'Total')
     ORDER BY JournalID
  ) as ids";

$invoices_with_line_control = array();
$result_line_control = $_lib['db']->db_query($query_line_control);
while($row = $_lib['db']->db_fetch_object($result_line_control)){
  $invoices_with_line_control[$row->JournalID] = true;
}

$query_printinfo = "SELECT InvoicePrintDate, InvoiceID FROM invoiceoutprint WHERE InvoiceID in ($query_for_ids)";
$printinfo = array();
$result_printinfo = $_lib['db']->db_query($query_printinfo);
while($row = $_lib['db']->db_fetch_object($result_printinfo)){
  $printinfo[$row->InvoiceID] = $row->InvoicePrintDate;
}


while($row = $_lib['db']->db_fetch_object($result_inv))
{
    $printdate = $printinfo[$row->InvoiceID];

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
      <td class="number"><? if($row->ExternalID > 0) { ?><a href="<? print $_SETUP['FB_SERVER_PROTOCOL'] ."://". $_SETUP['FB_SERVER']; ?>/invoices/<? print $row->ExternalID ?>" title="Vis i Fakturabank" target="_new">Vis i fakturabank</a><? } ?></td>
      <td><? if ($invoices_with_line_control[$row->InvoiceID]) print "Linjekontroll"; ?></td>
      <td class="number"><? if(!strstr($row->InvoiceDate, $row->Period)) { ?><span style="color: red">feil periode</span><? } ?></td>
  </form>
  </tr>
  <?
}
?>
<tr>
    <td colspan="10"></td>
    <td class="number"><? print $_lib['format']->Amount($TotalCustPrice) ?></td>
    <td></td>
</tr>
</tbody>

</table>
</body>
</html>


