<?
# $Id: print.php,v 1.45 2005/10/20 12:58:58 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
$InvoiceID = $_REQUEST['InvoiceID'];

$VoucherType='S';

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";
$db_table3 = "invoiceoutprint";

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";
$get_invoice            = "select I.*, A.InvoiceCommentCustomerPosition from $db_table as I, accountplan as A where InvoiceID='$InvoiceID' and A.AccountPlanID=I.CustomerAccountPlanID";
#print "$get_invoice<br>\n";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$query_invoiceline      = "select * from $db_table2 where InvoiceID='$InvoiceID' and Active <> 0 order by LineID asc";
$result2                = $_lib['db']->db_query($query_invoiceline);

$get_invoiceprint       = "select InvoicePrintDate from $db_table3 where InvoiceID='$InvoiceID'";
$row_print              = $_lib['storage']->get_row(array('query' => $get_invoiceprint));

$query_company = "select * from company where CompanyID='".$_lib['sess']->get_companydef('CompanyID')."'";
$row_company = $_lib['storage']->get_row(array('query'=>$query_company));

# Total shopping
$choosenYear = substr($row->InvoiceDate, 0, 4);
$query_total = "select sum(IVL.UnitCustPrice * IVL.QuantityDelivered) as Total, sum(IVL.UnitCustPrice * IVL.QuantityDelivered * (IVL.Vat/100)) as TotalWithVAT from invoiceoutline IVL, invoiceout IV where substring(IV.InvoiceDate,1,4)='$choosenYear' and IVL.InvoiceID=IV.InvoiceID and IV.CompanyID='".$row->CompanyID."' and IV.InvoiceID <= '$InvoiceID' and IVL.Active=1";
$rowTotal = $_lib['storage']->get_row(array('query' => $query_total));
//class="lodo_data"
print $_lib['sess']->doctype ?>

<head>
    <title>Faktura <? print $InvoiceID ?></title>
    <meta name="cvs"                content="$Id: print.php,v 1.45 2005/10/20 12:58:58 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<h2>
<span class="noprint">
<? print $_lib['form3']->URL(array('description' => '<<', 'title' => 'Klikk for &aring; se forrige bilag', 'url' => $_lib['sess']->dispatch . 't=invoice.print&amp;InvoiceID=' . ($InvoiceID - 1))) ?>
<? print $_lib['form3']->URL(array('description' => '>>', 'title' => 'Klikk for &aring; se neste bilag',   'url' => $_lib['sess']->dispatch . 't=invoice.print&amp;InvoiceID=' . ($InvoiceID + 1))) ?>
</span>
</h2>
<table class="lodo_data" id="head" frame="sides" rules="groups" summary="Faktura">
<COLGROUP align="left" span="2">
<thead>
    <tr>
        <td><label>Avsender</label></td>
        <td><nobr><? print $row->SName ?></nobr></td>
        <td><label>Mottaker</label></td>
        <td><nobr><? print $row->IName." (".$row->CustomerAccountPlanID.")" ?></nobr></td>
    </tr>
    <tr>
        <td><label>Adresse</label></td>
        <td><? print $row->SAddress ?></td>
        <? if($row->IAddress) { ?>
          <td><label>Adresse</label></td>
          <td><? print $row->IAddress ?></td>
        <? } else { ?>
          <td><label>Postboks</label></td>
          <td><? print $row->IPoBox ?> <? print $row->IPoBoxCity ?></td>
        <? } ?>
    </tr>
    <tr>
        <td><label>Postnr/Poststed</label></td>
        <td><? print $row->SZipCode." ".$row->SCity ?></td>
        <? if($row->IAddress) { ?>
          <td><label>Postnr/Poststed</label></td>
          <td><? print $row->IZipCode." ".$row->ICity ?></td>
        <? } else { ?>
          <td><label>Postnr/Poststed</label></td>
          <td><? print $row->IPoBoxZipCode ?> <? print $row->IPoBoxZipCodeCity ?></td>
        <? } ?>
    </tr>
    <tr>
        <td><label>Land</label></td>
        <td><? print $_lib['format']->codeToCountry($row->SCountryCode) ?></td>
        <td><label>Land</label></td>
        <td><? print $_lib['format']->codeToCountry($row->ICountryCode) ?></td>
    </tr>
    <tr>
        <td><label>Tlf nr</label></td>
        <td><? print $row->SPhone ?></td>
        <td><label>Tlf nr</label></td>
        <td><? print $row->Phone ?></td>
    </tr>
    <tr>
        <td><label>Mobil nr</label></td>
        <td><? print $row->SMobile ?></td>
        <td><label>Mobil nr</label></td>
        <td><? print $row->IMobile ?></td>
    </tr>
    <tr>
        <td><label>Email</label></td>
        <td><? print $row->SEmail ?></td>
        <td><label>Email</label></td>
        <td><? print $row->IEmail ?></td>
    </tr>
    <tr>
        <td><label>Web</label></td>
        <td><? print $row->SWeb ?></td>
        <td><label>Web</label></td>
        <td><? print $row->IWeb ?></td>
    </tr>
    <tr>
        <td><label>Konto nr</label></td>
        <td><? print $row->SBankAccount ?></td>
        <td><label>Konto nr</label></td>
        <td><b><? print $row->BankAccount ?><b></td>
    </tr>

    <tr>
	    <td>KID</td>
	    <td><? print $row->KID ?></td>
            <td></td><td></td>
    </tr>


    <tr>
        <td><label>Foretaksregisteret</label></td>
        <td><? print $row->SOrgNo ?></td>
        <td><label>Foretaksregisteret</label></td>
        <td><? print $row->IOrgNo ?></td>
    </tr>
    <tr>
        <td><label><?php if (!empty($row->SVatNo)) echo 'MVA reg' ?></label></td>
        <td><? if (!empty($row->SVatNo)) print $row->SVatNo ?></td>
        <td><label><?php if (!empty($row->IVatNo)) echo 'MVA reg' ?></label></td>
        <td><? if (!empty($row->IVatNo)) print $row->IVatNo ?></td>
    </tr>
    <tr>
      <td>Avdeling</td>
      <td><? if($row->DepartmentID) print $row->DepartmentID ?></td>
      <td>Avdeling</td>
      <td><? print $row->DepartmentCustomer ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? if($row->ProjectID) print $row->ProjectID ?></td>
      <td>Prosjekt</td>
      <td><? print $row->ProjectNameCustomer ?></td>
    </tr>

    <tr height="20">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td><label><b>Leveringsadresse</b></label></td>
        <td></td>
    </tr>
    <tr>
      <td colspan="2"></td>
      <td>Adresse</td>
      <td><? print $row->DAddress ?></td>
    </tr>
    <tr>
      <td colspan="2"></td>
      <td>By</td>
      <td><? print $row->DZipCode . " " . $row->DCity ?></td>
    </tr>
    <tr>
      <td colspan="2"></td>
      <td>Land</td>
      <td><? print $_lib['format']->codeToCountry($row->DCountryCode) ?></td>
    </tr>
    <tr height="20">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

</thead>

<tbody>
    <tr>
      <td>Fakturanummer</td>
      <td><?= $InvoiceID ?></td>

      <td>Valuta</td>
      <td><?= $row->CurrencyID ?></td>
    </tr>

    <tr>
      <td>Faktura dato</td>
      <td><b><? print substr($row->InvoiceDate,0,10) ?></b></td>
      <td>Forfalls dato</td>
      <td><b><? print substr($row->DueDate,0,10) ?></b></td>
    </tr>
    <? if($row_print && $row_print->InvoicePrintDate != '0000-00-00') { ?>
    <tr>
      <td>Utskriftsdato</td>
      <td><b><? print substr($row_print->InvoicePrintDate,0,10) ?></b></td>
    </tr>
    <? } ?>

    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $row->RefCustomer ?></td>
      <td>Deres ref.</td>
      <td><? print $row->RefInternal ?></td>
    </tr>
    <tr>
      <td>Leverings betingelse</td>
      <td><? print $row->DeliveryCondition ?></td>
      <td>Betalings betingelse</td>
      <td><? print $row->PaymentCondition ?></td>
    </tr>
    <tr>
            <td></td><td></td>
	    <td>Merk</td>
	    <td><? print $row->Note ?></td>
    </tr>
    <?
    if($row_company->InvoiceCommentCustomerPosition == 'top' and strlen($row->CommentCustomer) > 0) //byttet fra $row til $row_company for lese fra firmakort
    {
        ?>
        <tr height="20">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>Kommentar:</td>
            <td colspan="3"><? print nl2br($row->CommentCustomer) ?></td>
        </tr>
        <?
    }
    ?>
</tbody>

</tfoot>
</table>
<br><br>
<table border="0" cellspacing="0">
<thead>
  <tr>
    <th class="number">Produktnr</th>
    <th widht="5"></th>
    <th><nobr>Produkt navn</nobr></th>
    <th class="number">Antall</th>
    <th class="number">Enhetspris</th>
    <th class="number">MVA %</th>
    <th class="number"><nobr>MVA beløp</nobr></th>
    <th class="number"><nobr>Beløp u/MVA</nobr></th>
  </tr>
</thead>

<tbody>
<?
$sumlines = 0;
$sumallowances = 0;
$sumcharges = 0;
$vatlines = 0;
$rowCounter = 0;
// iterate through invoice lines
while($row2 = $_lib['db']->db_fetch_object($result2))
{
    $sum_ac = 0;
    $sum_ac_vat = 0;
    $LineID=$row2->LineID;
    $sumline = round($row2->QuantityDelivered * $row2->UnitCustPrice, 2);

    // Find allowances and charges
    $line_allowancecharges = array();
    $query = "SELECT * FROM invoicelineallowancecharge WHERE InvoiceLineID = ". $LineID ." AND InvoiceType = 'out';";
    $line_allowancecharges_result = $_lib["db"]->db_query($query);
    while($line_ac = $_lib["db"]->db_fetch_object($line_allowancecharges_result)) {
        $line_ac->VatPercent = $row2->Vat;
        $multiplicator = $line_ac->ChargeIndicator == 0 ? -1 : 1;
        $line_ac->Amount = $multiplicator * $line_ac->Amount;
        $line_ac->VatAmount = $line_ac->Amount * ($row2->Vat/100);
        $line_allowancecharges[] = $line_ac;
        if($line_ac->AllowanceChargeType == "line") {
            $sum_ac += $line_ac->Amount;
            $sum_ac_vat += $line_ac->VatAmount;
        }
    }

    $vatline = round(($row2->Vat/100) * $sumline + $sum_ac_vat, 2);
    $vatline_without_ac = round(($row2->Vat/100) * $sumline, 2);
    $sumlines += $sumline + $sum_ac;
    $vatlines += $vatline;

    if($row_company->InvoiceLineCommentPosition == 'top' and strlen($row2->Comment) > 0)
    {
        ?>
        <tr>
            <td colspan="7"><? print nl2br($row2->Comment) ?></td>
        </tr>
        <?
    }
    ?>
    <tr style="background-color: #E1E1E1;">
        <td class="number"><? print $row2->ProductID ?></td>
        <td widht="5"></td>
        <td><? print $row2->ProductName ?></td>
        <td class="number"><? print $row2->QuantityDelivered ?></td>
        <td class="number"><? print $_lib['format']->Amount($row2->UnitCustPrice) ?></td>
        <td class="number"><? print $row2->Vat ?>%</td>
        <td class="number"><? print $_lib['format']->Amount($vatline_without_ac) ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumline) ?></td>
    </tr>
    <?

    foreach ($line_allowancecharges as $allowance_charge) {
      $reason = ($allowance_charge->ChargeIndicator ? "Kostnad " : "Rabatt ") . ($allowance_charge->AllowanceChargeType == "line" ? "(linje)" : "(pris)") . ' - ' . $allowance_charge->AllowanceChargeReason;
      if ($allowance_charge->AllowanceChargeType == "line") {
        $amount_line = $_lib['format']->Amount($allowance_charge->Amount);
        $amount_price = null;
        $vat_amount = $_lib['format']->Amount($allowance_charge->VatAmount);
        $vat_percent = $row2->Vat;
      }
      else {
        $amount_line = null;
        $amount_price = $_lib['format']->Amount($allowance_charge->Amount);
        $vat_amount = null;
        $vat_percent = '0.00';
      }
        ?>
        <tr>
            <td colspan="2"></td>
            <td colspan="2"><? print $reason; ?></td>
            <td class="number"><? print $amount_price; ?></td>
            <td class="number"><? print $vat_percent; ?>%</td>
            <td class="number"><? print $vat_amount; ?></td>
            <td class="number"><? print $amount_line; ?></td>
        </tr>
        <?
    }

    if($row_company->InvoiceLineCommentPosition == 'bottom' and strlen($row2->Comment) > 0)
    {
        ?>
        <tr>
            <td colspan="7"><? print nl2br($row2->Comment) ?></td>
        </tr>
        <?
    }

    $rowCounter++;
}

// Find all invoice level allowance charges
$allowancecharges = array();
$query = "SELECT * FROM invoiceallowancecharge WHERE InvoiceID = ". $InvoiceID ." and InvoiceType = 'out';";
$invoice_allowancescharges_result = $_lib["db"]->db_query($query);
while($invoice_ac = $_lib["db"]->db_fetch_object($invoice_allowancescharges_result)) {
    if($invoice_ac->ChargeIndicator == 0) {
        $multiplicator = -1;
        $sumallowances += $invoice_ac->Amount;
    } else {
        $multiplicator = 1;
        $sumcharges += $invoice_ac->Amount;
    }
    $invoice_ac->Amount *= $multiplicator;
    $vatlines += round($invoice_ac->Amount * ($invoice_ac->VatPercent/100), 2);
    $allowancecharges[] = $invoice_ac;
}

?>
    <?
    if($row_company->InvoiceCommentCustomerPosition == 'bottom' and strlen($row->CommentCustomer) > 0) //byttet fra $row til $row_company
    {
        ?>
        <tr height="20">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td>Kommentar:</td>
            <td colspan="3"><? print nl2br($row->CommentCustomer) ?></td>
        </tr>
        <?
    }
    ?>

    <tr height="20"><td colspan="8"></td></tr>

    <tr>
        <th colspan="2"></th>
        <th colspan="3"><nobr>Årsak</nobr></th>
        <th class="number">MVA %</th>
        <th class="number"><nobr>MVA beløp</nobr></th>
        <th class="number"><nobr>Beløp u/MVA</nobr></th>
    </tr>
    <?
    foreach ($allowancecharges as $allowance_charge) {
    ?>
    <tr>
        <td colspan="2"><? print $allowance_charge->ChargeIndicator == 0 ? "Rabatt" : "Kostnad" ?></td>
        <td colspan="3"><nobr><? print $allowance_charge->AllowanceChargeReason ?></nobr></td>
        <td class="number"><? print $allowance_charge->VatPercent ?>%</td>
        <td class="number"><nobr><? print $_lib['format']->Amount($allowance_charge->Amount * ($allowance_charge->VatPercent / 100)) ?></nobr></td>
        <td class="number"><nobr><? print $_lib['format']->Amount($allowance_charge->Amount) ?></nobr></td>
    </tr>
    <?
    }
    ?>

    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td colspan="7" class="number">Linje sum</td>
        <td class="number"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="7" class="number">Rabatt sum</td>
        <td class="number"><? print $_lib['format']->Amount(-$sumallowances) ?></td>
    </tr>
    <tr>
        <td colspan="7" class="number">Sum kostnad</td>
        <td class="number"><? print $_lib['format']->Amount($sumcharges) ?></td>
    </tr>
    <tr>
        <td colspan="7" class="number">Totalt U/MVA</td>
        <td class="number"><? print $_lib['format']->Amount($sumlines - $sumallowances + $sumcharges) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Total MVA</td>
        <td class="number"><? print $_lib['format']->Amount($vatlines) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right"><b>Totalt M/MVA</b></td>
        <td class="number"><h2><? print $_lib['format']->Amount($vatlines + $sumlines - $sumallowances + $sumcharges) ?></h2></td>
    </tr>
</tbody>

<? if($_lib['sess']->get_companydef('ShowInvoiceAmountThisYear') == 1) { ?>
<tfoot>
    <tr height="20">
        <td colspan="8">
            <hr>
        </td>
    </tr>
    <tr valign="top">
        <td colspan="5" align="center">
            Hittil i <? print $choosenYear ?> har du handlet for:</td>
        <td colspan="2"><? print $_lib['format']->Amount($rowTotal->Total) ?> eks MVA</td>
    </tr>
    <tr>
        <td colspan="5"></td>
        <td colspan="2"><? print $_lib['format']->Amount($rowTotal->TotalWithVAT + $rowTotal->Total) ?> inkl MVA</td>
    </tr>
</tfoot>
<? } ?>
</table>
</body>
</html>
