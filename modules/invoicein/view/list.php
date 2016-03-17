<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no


includelogic('accounting/accounting');
includelogic('invoicein/invoicein');
includelogic('exchange/exchange');

$accounting = new accounting();

$invoicein    = new logic_invoicein_invoicein($_lib['input']->request);
$invoicein->fill(array());

$all_inv = $invoicein->getAllReadyInvoices();

require_once "record.inc";


print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>


<h2>
<? if($_lib['sess']->get_person('FakturabankImportInvoiceAccess')) { ?><a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.listincoming">Importer fakturaer med status regnskapbilag fra Fakturabank</a><br> <? } ?>
<a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.invoicereconciliationlist">Oppsett av koblinger mellom avstemmings&aring;rsaker og kontoer</a>
</h2>

<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>

<ul>
<li>Her kan du hente ned innkommende fakturaer fra fakturabank eller punche de selv.</li>
<li>Fakturaene m&aring; v&aelig;re registrert i denne listen for &aring; kunne brukes til direkte remittering.</li>
<li>Fakturaer som blir registrert her kan bli automatisk bilagsf&oslash;rt.</li>
<li>Grensesnittet for &aring; registrere fakturaer her er enklere enn bilagsgrensesnittet.</li>
</ul>
<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=invoicein.list" method="post">
<input type="submit" value="Ny innkommende faktura (N)" name="action_invoicein_add" accesskey="N">
</form>

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=invoicein.list" method="post">
</form>


<form name="invoice_edit1" action="<? print $_lib['sess']->dispatch ?>t=invoicein.list" method="post">
<table>
<tr>
    <td></td>
    <td></td>
    <td rowspan="6" valign="top">
        <ul>
    <?
        foreach ($all_inv as $row) {
                print '<li>'. $row['Y'] .'.'. $row['M'] .' = '. $row['cnt'] .'</li>';
        }
    ?>
        </ul>
    <td>
</tr>
<tr>
    <td>Fra:</td>
    <td><? print $_lib['form3']->date(array('name' => 'FromDate', 'field' => 'FromDate', 'form_name' => 'invoice_edit1', 'value' => $invoicein->FromDate)) ?></td>
    <td></td>
</tr>
<tr>
    <td>Til:</td>
    <td><? print $_lib['form3']->date(array('name' => 'ToDate', 'field' => 'ToDate', 'form_name' => 'invoice_edit1', 'value' => $invoicein->ToDate)) ?></td>
</tr>
<tr>
    <td>Status:</td>
    <td><? print $_lib['form3']->text(array('name' => 'RemittanceStatus',   'value' => $invoicein->RemittanceStatus)) ?></td>
    <td></td>
</tr>
<tr>
    <td>Fakturanummer:</td>
    <td><? print $_lib['form3']->text(array('name' => 'InvoiceNumber',   'value' => $invoicein->InvoiceNumber)) ?></td>
    <td></td>
</tr>
<tr>
    <td>Bilagsf&oslash;rt:</td>
    <td><? print $_lib['form3']->checkbox(array('name' => 'Journaled',   'value' => $invoicein->Journaled)) ?></td>
    <td></td>
</tr>
<tr>
    <td>Betalingsm&aring;te:</td>
    <td><? print $_lib['form3']->select(array('name'=>'PaymentMeans', 'value' => $invoicein->PaymentMeans, 'query' => 'form.PaymentMeans', 'width' => 30)) ?></td>
    <td></td>
</tr>
<tr>
    <td><? print $_lib['form3']->submit(array('name' => 'show_search',   'value' => 'S&oslash;k (S)')) ?></td>
    <td></td>
    <td><input type="submit" value="Bilagsf&oslash;r alle i listen (B)" name="action_invoicein_journal" accesskey="B"></td>
</tr>
</table>
<table class="lodo_data">
<thead>
<tr>
    <th class="number">Bilag</th>
    <th class="number">Faktura nr</th>
    <th class="number">Fakturadato</th>
    <th class="number">Periode</th>
    <th class="number">FirmaID</th>
    <th class="number">Lev. Konto</th>
    <th>Firmanavn</th>
    <th>Motkonto</th>
    <th>MotkontoNavn</th>
    <th class="number">Forfallsdato</th>
    <th class="number">Bel&oslash;p</th>
    <th>Prosjekt</th>
    <th>Avdeling</th>
    <th>&Aring;rsaksinformasjon</th>
    <th class="number">Bankkonto</th>
    <th class="number">Betaling</th>
    <th class="number">KID</th>
    <th class="number">Fakturabank</th>
    <th class="number">Remittering</th>
    <th class="number">Status</th>
</tr>
</thead>
<tbody>
<?
$invoicein->changeOrder();
foreach($invoicein as $InvoiceO) {
    // set InvoiceO->FirmaID
    $firma_id_query = "SELECT aps.SchemeValue, fbs.SchemeType FROM accountplanscheme aps JOIN fakturabankscheme fbs ON aps.FakturabankSchemeID = fbs.FakturabankSchemeID WHERE AccountPlanID = '" . $InvoiceO->SupplierAccountPlanID . "' LIMIT 1";
    $firma_id = $_lib['storage']->get_row(array('query' => $firma_id_query));
    if (!empty($firma_id->SchemeValue)) $InvoiceO->FirmaID = $firma_id->SchemeType . " " . $firma_id->SchemeValue;
    else $InvoiceO->FirmaID = "ikke funnet";

    $TotalCustPrice += $InvoiceO->TotalCustPrice;
    $TotalCustPriceForeign += $InvoiceO->ForeignAmount;
    $ForeignCurrencyID = '';
    $count++;

    $fb_query = sprintf("SELECT * FROM fakturabankinvoicein WHERE LodoID = %d", $InvoiceO->ID);
    $fb_row = $_lib['storage']->get_row(array('query' => $fb_query, 'debug' => true));

    // new logic for getting reason information
    $reasons_query = sprintf("SELECT LodoID, ClosingReasonId, Amount, FakturabankInvoiceReconciliationReasonCode FROM `fbdownloadedinvoicereasons` as fbdir join `fakturabankinvoicereconciliationreason` as fbirr on fbdir.ClosingReasonId = fbirr.FakturabankInvoiceReconciliationReasonID WHERE fbdir.LodoID= %d", $InvoiceO->ID);
    $reasons_r = $_lib['db']->db_query($reasons_query);
    $ReasonsInfo = "";
    while($row = $_lib['db']->db_fetch_object($reasons_r)) {
        $ReasonsInfo = $ReasonsInfo . $row->FakturabankInvoiceReconciliationReasonCode . " = " . $_lib['format']->Amount($row->Amount) . " | ";
    }
    // remove last 3 characters " | "
    $ReasonsInfo = substr($ReasonsInfo, 0, -3);
    ?>
    <tr class="<? print $InvoiceO->Class ?>">
      <? if ($InvoiceO->TotalCustPrice != 0) {?>
      <td class="number"><? if($InvoiceO->Journaled) { ?><a href="<? print $_SETUP['DISPATCH']."t=journal.edit&amp;voucher_VoucherType=$InvoiceO->VoucherType&amp;voucher_JournalID=$InvoiceO->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a><? } elseif ($InvoiceO->Journal) { print $InvoiceO->VoucherType . $InvoiceO->JournalID; }  ?></td>
      <?} else {?>
      <td></td>
      <? } ?>
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=invoicein.edit&ID=<? print $InvoiceO->ID ?>&amp;inline=edit" title="Endre faktura"><? print $InvoiceO->InvoiceNumber ?></a></td>
      <td class="number"><? print $InvoiceO->InvoiceDate ?></td>
      <td class="number"><? print $InvoiceO->Period ?></td>
      <td class="number"><? print $InvoiceO->FirmaID ?></td>
      <td class="number"><? print $InvoiceO->SupplierAccountPlanID ?></td>
      <td><? print substr($InvoiceO->IName,0,20) ?></td>
      <td><? print $InvoiceO->MotkontoAccountPlanID ?></td>
      <td><? print $InvoiceO->MotkontoAccountName ?></td>
      <td class="number"><? print $InvoiceO->DueDate ?></td>
      <td class="number">
        <?
           if ($InvoiceO->ForeignCurrencyID) {
                $ForeignCurrencyID = $InvoiceO->ForeignCurrencyID;
                print " (". $InvoiceO->ForeignCurrencyID ." ". $_lib['format']->Amount($InvoiceO->ForeignAmount) ." / $InvoiceO->ForeignConvRate) ";
           }
           print $_lib['format']->Amount($InvoiceO->TotalCustPrice);
        ?>
      </td>
      <?
        $query_department = sprintf("SELECT * FROM department WHERE DepartmentID = %d", $InvoiceO->Department);
        $department_row = $_lib['storage']->get_row(array('query' => $query_department, 'debug' => true));
        $department = $department_row->DepartmentID . " - " . $department_row->DepartmentName;

        $query_project = sprintf("SELECT * FROM project WHERE ProjectID = %d", $InvoiceO->Project);
        $project_row = $_lib['storage']->get_row(array('query' => $query_project, 'debug' => true));
        $project = $project_row->ProjectID . " - " . $project_row->Heading;
      ?>
      <td><? if ((string)$InvoiceO->Project != '') print $project ?></td>
      <td><? if ((string)$InvoiceO->Department != '') print $department ?></td>
      <td title="<? print $ReasonsInfo ?>"><?
        if (strlen($ReasonsInfo) > 40){
         print substr($ReasonsInfo, 0, 37) . '...';
        }else {
          print $ReasonsInfo;
        } ?></td>
      <td><? print $InvoiceO->SupplierBankAccount ?></td>
      <td class="number"><? print $InvoiceO->PaymentMeans ?></td>
      <td class="number"><? print $InvoiceO->KID ?></td>
      <td class="number"><? if($InvoiceO->ExternalID) { ?><a href="<?php echo $_SETUP['FB_SERVER_PROTOCOL'] ."://". $_SETUP['FB_SERVER']; ?>/invoices/<? print $InvoiceO->ExternalID ?>" title="Vis i Fakturabank" target="_new">Vis i fakturabank</a><? } ?></td>
      <td class="number"><? print $InvoiceO->RemittanceStatus ?></td>
      <td class="number"><? print $InvoiceO->Status ?></td>
  </tr>
<? } ?>
</tbody>
<tr>
    <th colspan="9">Antall: <? print $count ?></th>
    <th>SUM</th>
    <th class="number">
    <?
        print  $_lib['format']->Amount($TotalCustPrice);
        if ($InvoiceO->ForeignCurrencyID) {
                print " (". $ForeignCurrencyID ." ". $_lib['format']->Amount($TotalCustPriceForeign) ." / $InvoiceO->ForeignConvRate)";
        }
    ?>
    </th>
    <th colspan="10"></th>
</tr>
</table>
</form>

</body>
</html>
