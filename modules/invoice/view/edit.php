<?
# $Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$InvoiceID = (int) $_REQUEST['InvoiceID'];
$inline       = $_REQUEST['inline'];
#print_r($_REQUEST);

$VoucherType='S';

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";
$db_table3 = "invoiceoutprint";

includelogic('exchange/exchange');

includelogic('accounting/accounting');


$accounting = new accounting();
require_once "record.inc";

$get_invoice            = "select I.* from $db_table as I where InvoiceID='$InvoiceID'";
#print "Get invoice " . $get_invoice . "<br>\n";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$query_invoiceline      = "select * from $db_table2 where InvoiceID='$InvoiceID' and Active <> 0 order by LineID asc";
#print "query_invoiceline" . $query_invoiceline . "<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);

$get_invoiceprint       = "select InvoicePrintDate from $db_table3 where InvoiceID='$InvoiceID'";
$row_print              = $_lib['storage']->get_row(array('query' => $get_invoiceprint));

/**
 * factoring setup
 */
/*$factoring = new invoice_factoring($InvoiceID);

if(!$factoring->isGlobalEnabled() || (!$factoring->hasFactoring() && (int)$row->CustomerAccountPlanID == 0)) {
    $willUseFactoring = false;
}*/

print $_lib['sess']->doctype;
$tabindex = 1;

?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Faktura <? print $InvoiceID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('combobox') ?>
</head>

<body>
<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="InvoiceID" value="<? print $InvoiceID ?>">
<input type="hidden" name="inline" value="edit">

<table class="lodo_data">
<thead>
    <tr>
        <td>Fakturanummer</td>
        <td><a href="<? print $_lib['sess']->dispatch."t=journal.edit&voucher_JournalID=$InvoiceID"; ?>&type=salecash_in"><? print $InvoiceID ?></a></td>
        <td>KID:</td>
        <td><? print $row->KID ?></td>
    </tr>
    <tr>
        <td><b>Avsender</b></td>
        <td><? print $row->SName ?></td>
        <td><b>Mottaker</b></td>
        <td><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'CustomerAccountPlanID', 'pk'=>$InvoiceID, 'value'=>$row->CustomerAccountPlanID,  'type' => array(0 => customer), 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Adresse</a></td>
        <td><? print $row->SAddress ?></td>
        <? if( $row->IAddress) { ?>
          <td>Adresse</td>
          <td><? print $row->IAddress ?></td>
        <? } else { ?>
          <td>Postboks</td>
          <td><? print $row->IPoBox ?> <? print $row->IPoBoxCity ?></td>
        <? } ?>
    </tr>
    <tr>
        <td>Postnr/Poststed</td>
        <td><? print $row->SZipCode." ".$row->SCity ?></td>
        <? if( $row->IAddress) { ?>
          <td>Postnr/Poststed</td>
          <td><? print $row->IZipCode." ".$row->ICity ?></td>
        <? } else { ?>
          <td>Postnr/Poststed</td>
          <td><? print $row->IPoBoxZipCode ?> <? print $row->IPoBoxZipCodeCity ?></td>
           <? } ?>
    </tr>
    <tr>
        <td>Land</td>
        <td><? print $_lib['format']->codeToCountry($row->SCountryCode) ?></td>
        <td>Land</td>
        <td><? print $_lib['format']->codeToCountry($row->ICountryCode) ?></td>
    </tr>
    <tr>
        <td>Tlf nr</td>
        <td><? print $row->SPhone ?></td>
        <td>Tlf nr</td>
        <td><? print $row->Phone ?></td>
    </tr>
    </tr>
        <tr>
        <td>Mobil nr</td>
        <td><? print $row->SMobile ?></td>
        <td>Mobil nr</td>
        <td><? print $row->IMobile ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><? print $row->SEmail ?></td>
        <td>Email</td>
        <td><? print $row->IEmail ?></td>
    </tr>

    <tr>
        <td>Web</td>
        <td><? print $row->SWeb ?></td>
        <td>Web</td>
        <td><? print $row->IWeb ?></td>
    </tr>


    <tr>
        <td>Konto nr</td>
        <td><? print $row->SBankAccount ?></td>
        <td>Konto nr</td>
        <td><? print $row->BankAccount ?></td>
    </tr>
    <tr>
        <td>Foretaksregisteret</td>
        <td><? print $row->SOrgNo ?></td>
        <td>Foretaksregisteret</td>
        <td><? print $row->IOrgNo ?></td>
    </tr>
    <tr>
        <td><?php if (!empty($row->SVatNo)) echo 'MVA reg' ?></td>
        <td><? if (!empty($row->SVatNo)) print $row->SVatNo ?></td>
        <td><?php if (!empty($row->IVatNo)) echo 'MVA reg' ?></td>
        <td><? if (!empty($row->IVatNo)) print $row->IVatNo ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td colspan="2"><b>Leveringsadresse</b></td>
    </tr>

    <tr>
        <td colspan="2"></td>
        <td>Adresse</td>
        <td><? print $_lib['form3']->text(array('field'=>'DAddress', 'value'=>$row->DAddress, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Postnummer</td>
        <td><? print $_lib['form3']->text(array('field'=>'DZipCode', 'value'=>$row->DZipCode, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>By</td>
        <td><? print $_lib['form3']->text(array('field'=>'DCity', 'value'=>$row->DCity, 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td colspan="2"></td>
        <td>Land</td>
        <td><? print $_lib['form3']->Country_menu3(array('field'=>'DCountryCode', 'value'=>(($row->DCountryCode != "") ? $row->DCountryCode : "NO"), 'required'=>false, 'tabindex' => $tabindex++)); ?></td>
    </tr>
    <tr height="5">
        <td colspan="4"></td>
    </tr>
</thead>

<tbody>
    <tr height="5">
        <td colspan="4"></td>
    </tr>
    <tr>
      <td>Valuta</td>
      <td>
<?php
#Retrieve all currencies

$currencies = exchange::getAllCurrencies();
?>
      <select name="<?php echo $db_table . '.CurrencyID.' . $InvoiceID ?>">
<?
foreach ($currencies as $currency) {
?>
<option value="<? echo $currency->CurrencyISO; ?>" <?php if ($row->CurrencyID == $currency->CurrencyISO) echo 'selected'; ?>><? echo $currency->CurrencyISO; ?></option>
<?
}
?>
      </select>
</td>
      <td></td>
      <td></td>
    </tr>
    <tr>
      <td>Fakturadato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceDate', 'pk'=>$InvoiceID, 'value'=>substr($row->InvoiceDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
      <td>Forfallsdato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DueDate', 'pk'=>$InvoiceID, 'value'=>substr($row->DueDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Fakturaperiode</td>
        <td>
        <?
        if($accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel'))) {
            print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'Period', 'pk'=>$InvoiceID, 'value' => $row->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => ''));
        } else {
            print $row->Period;
        }
        ?>
        </td>
        <? if($row_print) { ?>
        <td>Utskriftsdato</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'InvoicePrintDate', 'pk'=>$InvoiceID, 'value'=>substr($row_print->InvoicePrintDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
        <? } ?>
    </tr>
    <tr>
      <td>Merk</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Note', 'pk'=>$InvoiceID, 'value'=>$row->Note, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Total bel&oslash;p</td>
      <td><? print $_lib['format']->Amount($row->TotalCustPrice) ?></td>
      <td>MVA</td>
      <td><? print $_lib['format']->Amount($row->TotalVat) ?></td>
    </tr>

    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefCustomer', 'pk'=>$InvoiceID, 'value'=>$row->RefCustomer, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Deres ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefInternal', 'pk'=>$InvoiceID, 'value'=>$row->RefInternal, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'pk'=>$InvoiceID, 'value' => $row->DepartmentID)); ?></td>
      <td>Avdeling</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DepartmentCustomer', 'pk'=>$InvoiceID, 'value'=>$row->DepartmentCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$InvoiceID,  'value' =>  $row->ProjectID)) ?></td>
      <td>Prosjekt</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProjectNameCustomer', 'pk'=>$InvoiceID, 'value'=>$row->ProjectNameCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Leveringsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$InvoiceID, 'value'=>$row->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Betalingsbetingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'PaymentCondition', 'pk'=>$InvoiceID, 'value'=>$row->PaymentCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td valign="top">Kommentar til kunde</td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentCustomer', 'pk'=>$InvoiceID, 'value'=>$row->CommentCustomer, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
    <tr>
      <td valign="top">
        Kommentar (intern)<br />
      </td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentInternal', 'pk'=>$InvoiceID, 'value'=>$row->CommentInternal, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
</tbody>

</tfoot>
</table>
<br>
<table border="0" cellspacing="0" width="775">
<thead>
  <tr>
    <td>ProduktNr</td>
    <td>Produkt navn</td>
    <td>Antall</td>
    <td>Enhetspris</td>
    <td>MVA</td>
    <td>MVA bel&oslash;p</td>
    <td>Bel&oslash;p U/MVA</td>
    <td></td>
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
    $vatline = round(($row2->Vat/100) * $sumline,2);
    $sumlines += $sumline;
    $vatlines += $vatline;
    ?>
    <tr>
        <td><? print $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>$LineID, 'value'=>$row2->ProductID, 'width'=>'35', 'tabindex'=>$tabindex++, 'class' => 'combobox', 'required' => false, 'notChoosenText' => 'Velg produkt')) ?></td>
        <td style='<? if (empty($row2->ProductName)) echo "background-color: red;"; ?>'><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName', 'pk'=>$LineID, 'value'=>$row2->ProductName, 'width'=>'20', 'maxlength' => 80, 'tabindex'=>$tabindex++)) ?></td>
        <td align="center" style='<? if ($row2->QuantityDelivered == 0) echo "background-color: red;"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$row2->QuantityDelivered, 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td style='<? if ($row2->UnitCustPrice == 0) echo "background-color: red;"; ?>'><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $row2->Vat ?>%<? #print $_lib['form3']->vat_menu3(array('percent2'=>'1', 'table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'SaleMenu'=>'1', 'date' => $row->InvoiceDate)) ?></td>
        <td align="right"><? print $_lib['format']->Amount($vatline) ?></td>
        <td align="right"><? print $_lib['format']->Amount($sumline) ?></td>
        <td>
        <? if(
               (!$row->Locked &&
                   $_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))
               ||
                ($_lib['sess']->get_person('AccessLevel') >= 4 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))) { ?>
        <a href="<? print $_SETUP[DISPATCH]."t=invoice.edit&InvoiceID=$InvoiceID&action_invoice_outlinedelete=1&amp;LineID=$LineID&amp;inline=edit#bottomPage" ?>" class="button">Slett</a>
        <? } ?>
    <tr>
        <td colspan="8"><? print $_lib['form3']->textarea(array('table'=>$db_table2, 'field'=>'Comment', 'pk'=>$LineID, 'value'=>$row2->Comment, 'tabindex'=>$tabindex++, 'min_height'=>'1', 'height'=>'1', 'width'=>'80')) ?>
    <?
    $rowCounter++;
    print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$LineID));
}
?>
    <tr height="20">
        <td></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Sum linjer</td>
        <td align="right"><? print $_lib['format']->Amount($sumlines) ?></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Sum MVA</td>
        <td align="right"><? print $_lib['format']->Amount($vatlines) ?></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Total med MVA</td>
        <td align="right"><? print $_lib['format']->Amount($vatlines + $sumlines) ?></td>
        <?
            print $_lib['form3']->Input(array('type'=>'hidden', 'table'=>'field', 'field'=>'count', 'value'=>$rowCounter));
        ?>
    </tr>
    <tr>
        <td colspan="7"><br><hr>
    </tr>
</tbody>

<tfoot>
    <tr>
        <td>
        <?
	    if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')))
            {
                if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_linenew', 'tabindex' => $tabindex++, 'value'=>'Ny fakturalinje (N)', 'accesskey'=>'N', 'OnClick'=>"this.form.action += '#bottomPage'"));
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_update', 'tabindex' => $tabindex++, 'value'=>'Lagre faktura (S)', 'accesskey'=>'S', 'OnClick'=>"this.form.action += '#bottomPage'"));
                }
                else {
                    print "Periode stengt";
                }
	    }
            else {
                if($inline == 'edit')
                    print "Du har ikke tilgang til &aring; lagre faktura";
                else
                    print "-";
            }
        ?>
        </td>
    </tr>
    <tr>
        <td>
        <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_save_internal', 'tabindex' => $tabindex++, 'value'=>'Lagre internkommentar')) ?>

        <?
        if($_lib['sess']->get_person('AccessLevel') >= 2)
        {
            print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_newonthis', 'tabindex' => $tabindex++, 'value'=>'Ny faktura ut i fra denne', 'confirm' => 'Er du sikker p&aring; at du vil lage ny ut i fra denne?'));
        }
        ?>
        </td>
    </tr>
    <tr>
        <td>
        <?
	if(!$row->Locked) {
		print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_lock', 'tabindex' => $tabindex++, 'value'=>'L&aring;s (L)', 'accesskey'=>'L', 'confirm'=>'Er du sikker p&aring; at du vil l&aring;se fakturaen?'));
	};

        ?>
        </td>


        <td colspan="6" align="right">
        <?

        if($_lib['sess']->get_person('FakturabankExportInvoiceAccess')) {
            echo "Orgnummer: ".  $row->IOrgNo . "<br />";

            if($row->IOrgNo)
                print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_fakturabanksend', 'tabindex' => $tabindex++,'value'=>'Fakturabank (F)', 'accesskey'=>'F'));
            else
                print "Mangler orgnummer ";
        }

        if(!$row->Locked || $_lib['sess']->get_person('AccessLevel') >= 4) {
            if($_lib['sess']->get_person('AccessLevel') >= 4 && $inline == 'edit') {
                if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')))
                    print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_delete', 'tabindex' => $tabindex++, 'value'=>'Slett faktura (D)', 'accesskey'=>'D', 'confirm' => 'Er du sikker p&aring; at du vil slette denne fakturaen?'));
            }
        }
        else {
            print "Faktura l&aring;st";
        }


        ?>
</form>
    <tr>
      <td></td>
    </tr>
        <?
          if ($row->UpdatedByPersonID) echo "<tr><td>" . $row->UpdatedAt . " lagret av " . $_lib['format']->PersonIDToName($row->UpdatedByPersonID) . "</td></tr>";
          if ($row->Locked) {
            if ($row->LockedBy) echo "<tr><td>" . $row->LockedAt . " l&aring;st av " . $_lib['format']->PersonIDToName($row->LockedBy) . "</td></tr>";
            else echo "<tr><td>L&aring;st: Ja </td></tr>";
          }
          if ($row->FakturabankPersonID) echo "<tr><td>" . $row->FakturabankDateTime . " fakturaBank " . $_lib['format']->PersonIDToName($row->FakturabankPersonID) . "</td></tr>";
        ?>
        <td colspan="7" align="right">
        <form name="skriv_ut" action="<? print $_lib['sess']->dispatch ?>t=invoice.print&InvoiceID=<? print $InvoiceID ?>&amp;inline=edit" method="post" target="_new">
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift')) ?>
        </form>
        <form name="skriv_ut2" action="<? print $_lib['sess']->dispatch ?>t=invoice.print2&InvoiceID=<? print $InvoiceID ?>" method="post" target="_new">
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoice_print', 'tabindex' => $tabindex++, 'value'=>'Utskrift PDF')) ?>
        </form>
     </tr>
     <tr>
        <td colspan="7" align="right">
        <form name="send_mail" action="<? print $_lib['sess']->dispatch ?>t=invoice.sendmail&InvoiceID=<? print $InvoiceID ?>" method="post">
          <?php
            $rowcomapny = $_lib['storage']->get_row(array('query' => "SELECT * FROM `company` WHERE CompanyID=" . $row->FromCompanyID));
          ?>
            <br />
            <input type="text" value="<? print $row->IEmail; ?>" name="email_recipient" />
            <input type="hidden" value="<?=  $rowcomapny->CopyFakturaMail ?>" name="send_mail_copy_mail" />
            <input name="send_mail_copy" type="checkbox" checked /> kopi til firma
            <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_send_email2', 'tabindex' => $tabindex++, 'value'=>'Send email')) ?>
        </form>
     </tr>
   <? if(!$row->Locked) { ?>
     <tr><td>L&aring;st:  Nei</td></tr>
   <? } ?>
</tfoot>
</table>
<a name="bottomPage"></a>
</body>
</html>
