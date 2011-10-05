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

$get_invoice            = "select I.*, A.InvoiceCommentCustomerPosition, A.OrgNumber, A.VatNumber, A.Mobile, A.Phone, A.AccountName from $db_table as I, accountplan as A where InvoiceID='$InvoiceID' and A.AccountPlanID=I.CustomerAccountPlanID";
#print "$get_invoice<br>\n";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$get_invoicefrom        = "select IName as FromName, IAddress as FromAddress, Email, IZipCode as Zip, ICity as City, Phone, BankAccount, Mobile, OrgNumber, VatNumber, ICountryCode as CountryCode from company where CompanyID='$row->FromCompanyID'";
#print "$get_invoicefrom<br>\n";
$row_from               = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

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


<h2>Faktura <? print $InvoiceID ?> 
<span class="noprint">
<? print $_lib['form3']->URL(array('description' => '<<', 'title' => 'Klikk for &aring; se forrige bilag', 'url' => $_lib['sess']->dispatch . 't=invoice.print&amp;InvoiceID=' . ($InvoiceID - 1))) ?>
<? print $_lib['form3']->URL(array('description' => '>>', 'title' => 'Klikk for &aring; se neste bilag',   'url' => $_lib['sess']->dispatch . 't=invoice.print&amp;InvoiceID=' . ($InvoiceID + 1))) ?>
</span>
</h2>
<div style="margin-bottom: 10px">Valuta <? print $row->CurrencyID ?></div> 
<table class="lodo_data" id="head" frame="sides" rules="groups" summary="Faktura">
<COLGROUP align="left" span="2">
<thead>
    <tr>
        <td><label>Avsender</label></td>
        <td><nobr><? print $row_from->FromName ?></nobr></td>
        <td><label>Mottaker</label></td>
        <td><nobr><? print $row->AccountName." (".$row->CustomerAccountPlanID.")" ?></nobr></td>
    </tr>
    <tr>
        <td><label>Adresse</label></td>
        <td><? print $row_from->FromAddress ?></td>
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
        <td><? print $row_from->Zip." ".$row_from->City ?></td>
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
        <td><? print $_lib['format']->codeToCountry($row_from->CountryCode) ?></td>
        <td><label>Land</label></td>
        <td><? print $_lib['format']->codeToCountry($row_from->CountryCode) ?></td>
    </tr>
    <tr>
        <td><label>Tlf nr</label></td>
        <td><? print $row_from->Phone ?></td>
        <td><label>Tlf nr</label></td>
        <td><? print $row->Phone ?></td>
    </tr>
    <tr>
        <td><label>Mobil nr</label></td>
        <td><? print $row_from->Mobile ?></td>
        <td><label>Mobil nr</label></td>
        <td><? print $row->Mobile ?></td>
    </tr>
    <tr>
        <td><label>Email</label></td>
        <td><? print $row_from->Email ?></td>
        <td><label>Email</label></td>
        <td><? print $row->IEmail ?></td>
    </tr>
    <tr>
        <td><label>Konto nr</label></td>
        <td><? print $row_from->BankAccount ?></td>
        <td><label>Konto nr</label></td>
        <td><b><? print $row->BankAccount ?><b></td>
    </tr>
    <tr>
        <td><label>Org nr</label></td>
        <td><? print $row_from->OrgNumber ?></td>
        <td><label>Org nr</label></td>
        <td><? print $row->OrgNumber ?></td>
    </tr>
    <tr>
        <td><label><?php if (!empty($row_from->VatNumber)) echo 'Vat nr' ?></label></td>
        <td><? if (!empty($row_from->VatNumber)) print $row_from->VatNumber ?></td>
        <td><label><?php if (!empty($row->VatNumber)) echo 'Vat nr' ?></label></td>
        <td><? if (!empty($row->VatNumber)) print $row->VatNumber ?></td>
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
    <tr>
      <td>Leverings betingelse</td>
      <td><? print $row->DeliveryCondition ?></td>
      <td>Betalings betingelse</td>
      <td><? print $row->PaymentCondition ?></td>
    </tr>
    <tr>
	    <td>KID</td>
	    <td><? print $row->KID ?></td>
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
$vatlines = 0;
$rowCounter = 0;
while($row2 = $_lib['db']->db_fetch_object($result2))
{
    $LineID=$row2->LineID;
    $sumline = round($row2->QuantityDelivered * $row2->UnitCustPrice, 2);
    $vatline = round(($row2->Vat/100) * $sumline, 2);
    $sumlines += $sumline;
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
    <tr>
        <td class="number"><? print $row2->ProductID ?></td>
        <td widht="5"></td>
        <td><? print $row2->ProductName ?></td>
        <td class="number"><? print $row2->QuantityDelivered ?></td>
        <td class="number"><? print $_lib['format']->Amount($row2->UnitCustPrice) ?></td>
        <td class="number"><? print $row2->Vat ?>%</td>
        <td class="number"><? print $_lib['format']->Amount($vatline) ?></td>
        <td class="number"><? print $_lib['format']->Amount($sumline) ?></td>
    </tr>
    <?
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
    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td colspan="7" class="number">Sum linjer</td>
        <td class="number"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right">Sum MVA</td>
        <td class="number"><? print $_lib['format']->Amount($vatlines) ?></td>
    </tr>
    <tr>
        <td colspan="7" align="right"><b>Total med MVA</b></td>
        <td class="number"><h2><? print $_lib['format']->Amount($vatlines + $sumlines) ?></h2></td>
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
