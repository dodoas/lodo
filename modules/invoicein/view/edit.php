<?
# $Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$ID         = (int) $_REQUEST['ID'];
$inline     = $_REQUEST['inline'];
#print_r($_REQUEST);

includelogic('invoicein/invoicein');
$invoicein  = new logic_invoicein_invoicein($_lib['input']->request);


$VoucherType='S';

$db_table = "invoicein";
$db_table2 = "invoiceinline";

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$get_invoice            = "select I.* from $db_table as I where ID='$ID'";
#print "Get invoice " . $get_invoice . "<br>\n";
$invoicein              = $_lib['storage']->get_row(array('query' => $get_invoice));

$get_invoicefrom        = "select * from accountplan where AccountPlanID=" . (int) $invoicein->SupplierAccountPlanID;
#print "get_invoicefrom " . $get_invoicefrom . "<br>\n";
$invoicein->from        = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

$get_invoiceto          = "select CompanyName, IAddress as FromAddress, Email, IZipCode as Zip, ICity as City, ICountryCode as CountryCode, Phone, Mobile, OrgNumber from company where CompanyID='" . $_lib['sess']->get_companydef('CompanyID') . "'";
#print "get_invoiceto " . $get_invoiceto . "<br>\n";
$invoicein->to          = $_lib['storage']->get_row(array('query' => $get_invoiceto));

$query_invoiceline      = "select * from $db_table2 where ID='$ID' and Active <> 0 order by LineID asc";
#print "query_invoiceline" . $query_invoiceline . "<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);
#print "Ferdig";
print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Faktura <? print $ID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="ID" value="<? print $ID ?>">
<input type="hidden" name="inline" value="edit">

<table class="lodo_data">
<thead>
    <tr>
        <td>Bilagsnummer</td>
        <td><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$invoicein->VoucherType&amp;voucher_JournalID=$invoicein->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $invoicein->VoucherType ?><? print $invoicein->JournalID ?></a></td>
        <td>Bilagsnummer</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'JournalID', 'pk' => $ID, 'value'=>$invoicein->JournalID, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Fakturanummer</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceNumber', 'pk' => $ID, 'value'=>$invoicein->InvoiceNumber, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
        <td>KID:</td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'KID', 'pk' => $ID, 'value'=>$invoicein->KID, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
        <td><b>Leverand&oslash;r</b></td>
        <td><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'SupplierAccountPlanID', 'pk'=>$ID, 'value'=>$invoicein->SupplierAccountPlanID,  'type' => array(0 => supplier))) ?></td>
        <td><b>Mottaker</b></td>
        <td><? print $invoicein->to->CompanyName ?></td>
    </tr>
    <tr>
        <td><b>Betal til konto</b></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'SupplierBankAccount', 'pk' => $ID, 'value' => $invoicein->SupplierBankAccount)) ?></td>
        <td><b>Betal fra konto</b></td>
        <td><? print $_lib['form3']->select(array('table'=>$db_table, 'field'=>'CustomerBankAccount', 'pk' => $ID, 'value' => $invoicein->CustomerBankAccount, 'query' => 'form.BankAccount', 'width' => 30)) ?></td>
    </tr>
    <tr>
        <td><b>Betal (bel&oslash;p)</b></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RemittanceAmount', 'pk'=>$ID, 'value' => $invoicein->RemittanceAmount)) ?></td>
        <td>Betalingsm&aring;te</td>
        <td><? print $_lib['form3']->select(array('table'=>$db_table, 'field'=>'PaymentMeans', 'pk' => $ID, 'value' => $invoicein->PaymentMeans, 'query' => 'form.PaymentMeans', 'width' => 30)) ?></td>
    </tr>
    <tr>
        <td>Org nr</td>
        <td><? print $invoicein->from->OrgNumber ?></td>
        <td>Org nr</td>
        <td><? print $invoicein->to->OrgNumber ?></td>
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
      <td>Faktura dato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'InvoiceDate', 'pk'=>$ID, 'value'=>substr($invoicein->InvoiceDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
      <td>Forfalls dato</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DueDate', 'pk'=>$ID, 'value'=>substr($invoicein->DueDate,0,10), 'width'=>'30', 'tabindex'=> $tabindex++)) ?></td>
    </tr>
    <tr>
        <td>Faktura periode</td>
        <td>        
        <?
        if($accounting->is_valid_accountperiod($invoicein->Period, $_lib['sess']->get_person('AccessLevel'))) {
            print $_lib['form3']->AccountPeriod_menu3(array('table' => $db_table, 'field' => 'Period', 'pk'=>$ID, 'value' => $invoicein->Period, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> true, 'tabindex' => ''));
        } else {
            print $invoicein->Period;
        }
        ?>
        </td>
        <td>Er med i reisegarantifondet</td>
        <td><? print $_lib['form3']->Checkbox(array('table'=>$db_table, 'field'=>'isReisegarantifond', 'pk'=>$ID, 'value'=>$invoicein->isReisegarantifond)) ?></td>
    </tr>
    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefCustomer', 'pk'=>$ID, 'value'=>$invoicein->RefCustomer, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Deres ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefInternal', 'pk'=>$ID, 'value'=>$invoicein->RefInternal, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'Department', 'pk'=>$ID, 'value' => $invoicein->Department)); ?></td>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'Project', 'pk'=>$ID,  'value' =>  $invoicein->Project)) ?></td>
    </tr>
    <tr>
      <td>Leverings betingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$ID, 'value'=>$invoicein->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Betalings betingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'PaymentCondition', 'pk'=>$ID, 'value'=>$invoicein->PaymentCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td valign="top">Kommentar (intern)</td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentInternal', 'pk'=>$ID, 'value'=>$invoicein->CommentInternal, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
    </tr>
    <tr>
      <td>Remitteringsstatus</td>
      <td><? print $invoicein->RemittanceStatus ?></td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td>Remittering godkjent</td>
      <td><? print $invoicein->RemittanceApprovedDateTime ?></td>
      <td>Remittering godkjent av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->RemittanceApprovedPersonID) ?></td>
    </tr>
    <tr>
      <td>Remittering sendt</td>
      <td><? print $invoicein->RemittanceSendtDateTime ?></td>
      <td>Remittering sendt av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->RemittanceSendtPersonID) ?></td>
    </tr>
    <tr>
      <td>RemittanceSequence</td>
      <td><? print $invoicein->RemittanceSequence ?></td>
      <td>RemittanceDaySequence</td>
      <td><? print $invoicein->RemittanceDaySequence ?></td>
    </tr>
    <tr>
      <td>Hentet fra fakturabank</td>
      <td><? print $invoicein->FakturabankDateTime ?></td>
      <td>Hentet fra fakturabank av</td>
      <td><? print $_lib['format']->PersonIDToName($invoicein->FakturabankPersonID) ?></td>
    </tr>
</tbody>

</tfoot>
</table>
<br>
<table width="775">
<thead>
  <tr>
    <td>Konto</td>
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
        <td>
        <?
        $aconf = array();
        $aconf['table']         = $db_table2;
        $aconf['field']         = 'AccountPlanID';
        $aconf['value']         = $row2->AccountPlanID;
        $aconf['pk']            = $LineID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = '';
        $aconf['width']         = '20';
        $aconf['type'][]        = 'result';
        $aconf['type'][]        = 'balance';
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
        </td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductNumber', 'pk'=>$LineID, 'value'=>$row2->ProductNumber, 'width' => 20, 'maxlength' => 20, 'tabindex'=>$tabindex++)) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName'  , 'pk'=>$LineID, 'value'=>$row2->ProductName,   'width' => 20, 'maxlength' => 80, 'tabindex'=>$tabindex++)) ?></td>
        <td align="center"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$row2->QuantityDelivered, 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'width' => 5, 'maxlength' => 5, 'tabindex'=>$tabindex++)) ?></td>
        <td align="right"><nobr><? print $_lib['format']->Amount($vatline) ?></nobr></td>
        <td align="right"><nobr><? print $_lib['format']->Amount($sumline) ?></nobr></td>
        <td>
        <? if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($invoicein->Period, $_lib['sess']->get_person('AccessLevel'))) { ?>
        <a href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&ID=$ID&action_invoicein_linedelete=1&amp;LineID=$LineID&amp;inline=edit" ?>" class="button">Slett</a>
        <? } ?>
    <tr>
        <td colspan="8"><? print $_lib['form3']->textarea(array('table'=>$db_table2, 'field'=>'Comment', 'pk'=>$LineID, 'value'=>$row2->Comment, 'tabindex'=>$tabindex++, 'min_height'=>'1', 'height'=>'1', 'width'=>'80')) ?>
    <?
    $rowCounter++;
    $AccountPlanID = $row2->AccountPlanID;
    print $_lib['form3']->Input(array('type'=>'hidden', 'name'=>$rowCounter, 'value'=>$LineID));
}
?>
</tbody>

<tfoot>
    <tr>
        <td>
        <?
        if(!$invoicein->Locked) {
			if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($_lib['date']->get_this_period($invoicein->Period), $_lib['sess']->get_person('AccessLevel')))
			{ ?>
                <a href="<? print $_SETUP[DISPATCH]."t=invoicein.edit&amp;ID=$ID&amp;action_invoicein_linenew=1&amp;AccountPlanID=$AccountPlanID&amp;inline=edit" ?>" class="button" accesskey="N">Ny linje (N)</a>
            <?
			}
        }
        ?>
        <td>
        </td>

        <td colspan="6" align="right">
        <? if($invoicein->ExternalID) { ?><a href="<?php echo $_SETUP['FB_URL'] ?>invoices/<? print $invoicein->ExternalID ?>" title="Vis i Fakturabank" target="_new">Vis i fakturabank</a><? } ?>

        <?
        if(!$invoicein->Locked) {
	
			if($_lib['sess']->get_person('AccessLevel') >= 2)
			{
				if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($invoicein->Period), $_lib['sess']->get_person('AccessLevel'))) {
					print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoicein_update', 'value'=>'Lagre faktura (S)', 'accesskey'=>'S'));
				} else {
					print "Periode stengt";
				}
			} else {
			  print "Du har ikke tilgang til &aring; lagre faktura";
			}
		} else {
			print "Faktura l&aring;st";
		}
        ?>
    </form>
    <tr>
        <td colspan="6" align="right">Sum linjer</td>
        <td align="right"><nobr><? print $_lib['format']->Amount($sumlines) ?></nobr></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Sum MVA</td>
        <td align="right"><nobr><? print $_lib['format']->Amount($vatlines) ?></nobr></td>
    </tr>
    <tr>
        <td colspan="6" align="right">Total med MVA</td>
        <td align="right"><nobr><? print $_lib['format']->Amount($vatlines + $sumlines) ?></nobr></td>
        <?
            print $_lib['form3']->Input(array('type'=>'hidden', 'table'=>'field', 'field'=>'count', 'value'=>$rowCounter));
        ?>
    </tr>
    <tr>
        <td colspan="7"><br><hr>
    </tr>

     <tr>
     	<td>L&aring;st: <? if($invoicein->Locked) { ?>Ja<? } else { ?>Nei<? } ?></td>
     </tr>
</tfoot>
</table>
</body>
</html>
