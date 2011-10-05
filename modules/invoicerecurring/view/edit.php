<?
# $Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $ edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

/**
 * Function to generate interval-selectbox
 */
function interval_menu($value, $RecurringID)
{
	$recurring = new recurring(array());
	$recurring_intervals = $recurring->get_intervals();

	printf("<select name='recurring.TimeInterval.%d'>", $RecurringID);
	foreach($recurring_intervals as $k => $v)
	{
		$v = $v[0];
		if($value == $k)
			printf('<option value="%s" selected>%s</option>', $k, $v);
		else
			printf('<option value="%s">%s</option>', $k, $v);
	}
	printf("</select>");
}

$RecurringID = (int) $_REQUEST['RecurringID'];
$inline       = $_REQUEST['inline'];
#print_r($_REQUEST);

#print_r($_POST);

$VoucherType='S';

$db_table = "recurringout";
$db_table2 = "recurringoutline";

includelogic('accounting/accounting');
$accounting = new accounting();

require_once "record.inc";

$get_invoice            = "select I.* from $db_table as I where RecurringID='$RecurringID'";
#print "Get invoice " . $get_invoice . "<br>\n";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$get_invoiceto          = "select * from accountplan where AccountPlanID=" . (int) $row->AccountPlanID;
$row_to                 = $_lib['storage']->get_row(array('query' => $get_invoiceto));

$get_recurring_row      = sprintf("select * from recurring where RecurringID=%d", $RecurringID);
$recurring_row          = $_lib['storage']->get_row(array('query' => $get_recurring_row, 'debug'=>true));

$get_invoicefrom        = "select IName as FromName, IAddress as FromAddress, Email, IZipCode as Zip, ICity as City, ICountryCode as CountryCode, Phone, BankAccount, Mobile, OrgNumber from company where CompanyID='$row->FromCompanyID'";
#print "get_invoicefrom " . $get_invoicefrom . "<br>\n";
$row_from               = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

$query_invoiceline      = "select * from $db_table2 where RecurringID='$RecurringID' and Active <> 0 order by LineID asc";
#print "query_invoiceline" . $query_invoiceline . "<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);
#print "Ferdig";
print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Repeteremde faktura <? print $RecurringID ?></title>
    <meta name="cvs"                content="$Id: edit.php,v 1.78 2005/11/03 15:57:27 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
    <script>
    function showhide(id)
    {
        el = document.getElementById(id);
        if(el == null)
            return null;
        
        if(el.style.display == 'block')
            el.style.display = 'none';
        else
            el.style.display = 'block';
    }
    </script>
<body>
<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="RecurringID" value="<? print $RecurringID ?>">
<input type="hidden" name="inline" value="edit">

<table class="lodo_data">
<thead>
    <tr>
        <td>ID</td>
        <td><? print $RecurringID ?></td>
        <td>KID:</td>
        <td><? print $row->KID ?></td>
    </tr>
    <tr>
        <td><b>Avsender</b></td>
        <td><? print $row_from->FromName ?></td>
        <td><b>Mottaker</b></td>
        <td><? print $_lib['form3']->accountplan_number_menu(array('table'=>$db_table, 'field'=>'CustomerAccountPlanID', 'pk'=>$RecurringID, 'value'=>$row->CustomerAccountPlanID,  'type' => array(0 => customer))) ?></td>
    </tr>
    <tr>
        <td>Adresse</a></td>
        <td><? print $row_from->FromAddress ?></td>
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
        <td><? print $row_from->Zip." ".$row_from->City ?></td>
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
        <td><? print $_lib['format']->codeToCountry($row_from->CountryCode) ?></td>
        <td>Land</td>
        <td><? print $_lib['format']->codeToCountry($row->ICountryCode) ?></td>
    </tr>
    <tr>
        <td>Tlf nr</td>
        <td><? print $row_from->Phone ?></td>
        <td>Tlf nr</td>
        <td><? print $row_to->Phone ?></td>
    </tr>
    </tr>
        <tr>
        <td>Mobil nr</td>
        <td><? print $row_from->Mobile ?></td>
        <td>Mobil nr</td>
        <td><? print $row_to->Mobile ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><? print $row_from->Email ?></td>
        <td>Email</td>
        <td><? print $row->IEmail ?></td>
    </tr>
    <tr>
        <td>Konto nr</td>
        <td><? print $row_from->BankAccount ?></td>
        <td>Konto nr</td>
        <td><? print $row->BankAccount ?></td>
    </tr>
    <tr>
        <td>Org nr</td>
        <td><? print $row_from->OrgNumber ?></td>
        <td>Org nr</td>
        <td><? print $row_to->OrgNumber ?></td>
    </tr>
    <tr height="5">
        <td colspan="4"></td>
    </tr>
</thead>

<tbody>
    <tr height="5">
        <td colspan="4"></td>
    </tr>

<?php

?>
    <tr>
      <td>Startdato</td>
      <td><? print $_lib['form3']->text(array('table'=>'recurring', 'field'=>'StartDate', 'pk'=>$RecurringID, 'value'=>$recurring_row->StartDate, 'width'=>'30', 'tabindex'=>$tabindex++)); ?></td>
      <td>Interval</td>
      <td><? interval_menu($recurring_row->TimeInterval, $RecurringID) ?></td>
    </tr>
    <tr>
      <td>Utskriftsdifferanse</td>
      <td><? print $_lib['form3']->text(array('table'=>'recurring', 'field'=>'PrintInterval', 'pk'=>$RecurringID, 'value'=>$recurring_row->PrintInterval, 'width'=>'30', 'tabindex'=>$tabindex++)); ?></td>
      
      <td>Sluttdato</td>
      <td><? print $_lib['form3']->text(array('table'=>'recurring', 'field'=>'EndDate', 'pk'=>$RecurringID, 'value'=>$recurring_row->EndDate, 'width'=>'30', 'tabindex'=>$tabindex++)); ?></td>
    </tr>
    <tr>
      <td>TotalCustPrice</td>
      <td><? print $row->TotalCustPrice ?></td>
      <td>TotalVat</td>
      <td><? print $row->TotalVat ?></td>
    </tr>

    <tr>
      <td>V&aring;r ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefCustomer', 'pk'=>$RecurringID, 'value'=>$row->RefCustomer, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Deres ref.</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'RefInternal', 'pk'=>$RecurringID, 'value'=>$row->RefInternal, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Avdeling</td>
      <td><? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'pk'=>$RecurringID, 'value' => $row->DepartmentID)); ?></td>
      <td>Avdeling</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DepartmentCustomer', 'pk'=>$RecurringID, 'value'=>$row->DepartmentCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Prosjekt</td>
      <td><? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID', 'pk'=>$RecurringID,  'value' =>  $row->ProjectID)) ?></td>
      <td>Prosjekt</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProjectNameCustomer', 'pk'=>$RecurringID, 'value'=>$row->ProjectNameCustomer, 'width'=>'30', 'tabindex' => $tabindex++)) ?></td>
    </tr>
    <tr>
      <td>Leverings betingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'DeliveryCondition', 'pk'=>$RecurringID, 'value'=>$row->DeliveryCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
      <td>Betalings betingelse</td>
      <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'PaymentCondition', 'pk'=>$RecurringID, 'value'=>$row->PaymentCondition, 'width'=>'30', 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
      <td valign="top">Kommentar til kunde</td>
      <td colspan="3">
        <? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentCustomer', 'pk'=>$RecurringID, 'value'=>$row->CommentCustomer, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?>
        <div id="info" style="display: none;">
          <table>
            <tr><td><b>%M</b></td><td>m&aring;nedsnavn.</td></tr>
            <tr><td><b>%W</b></td><td>uketall.</td></tr>
            <tr><td><b>%K</b></td><td>kvartalsnummer.</td></tr> 
            <tr><td><b>%H</b></td><td>halv&aring;rsnummer.</td></tr>
            <tr><td><b>%Y</b></td><td>&aring;rstall.</td></tr>
            <tr><td><b>%NY</b></td><td>neste &aring;</td></tr>
            <tr><td><b>%LY</b></td><td>forrige &aring;</td></tr>
            <tr><td><b>%NM</b></td><td>neste m&aring;ned</td></tr>
            <tr><td><b>%LM</b></td><td>forrige m&aring;ned</td></tr>
           </table>
          <br />
	  <?php
	    $eks = "Faktura for <b>%M</b> i <b>%K</b> kvartal <b>%Y</b>. Sendt uke <b>%W</b>. <br />
N&aring; er vi i <b>%H</b> halv&aring;r. Forrige m&aring;ned var <b>%LM</b>, neste er <b>%NM</b>. Forrige &aring; var <b>%LY</b>, neste er <b>%NY</b>";
            echo "Eksempel:<br />'$eks'<br /><br />ville i dag gitt: <br /><br/>";
	    echo "'".$recurring->replace_tokens($eks, date("Y-m-d"))."'<br /><br />";
          ?><br />
          <a href="javascript:showhide('info');showhide('show_info')">Skjul hjelp</a>
        </div>

        <div id="show_info" style="display: block;">
          <a href="javascript:showhide('info');showhide('show_info')">Vis hjelp</a>
        </div>
      </td>
    </tr>
    <tr>
      <td valign="top">Kommentar (intern)</td>
      <td colspan="3"><? print $_lib['form3']->TextArea(array('table'=>$db_table, 'field'=>'CommentInternal', 'pk'=>$RecurringID, 'value'=>$row->CommentInternal, 'tabindex'=>$tabindex++, 'height'=>'5', 'width'=>'80')) ?></td>
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
        <td><? print $_lib['form3']->Product_menu3(array('table'=>$db_table2, 'field'=>'ProductID', 'pk'=>$LineID, 'value'=>$row2->ProductID, 'width'=>'35', 'tabindex'=>$tabindex++, 'required' => 1)) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'ProductName', 'pk'=>$LineID, 'value'=>$row2->ProductName, 'width'=>'20', 'maxlength' => 80, 'tabindex'=>$tabindex++)) ?></td>
        <td align="center"><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'QuantityDelivered', 'pk'=>$LineID, 'value'=>$row2->QuantityDelivered, 'width'=>'8', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table2, 'field'=>'UnitCustPrice', 'pk'=>$LineID, 'value'=>$_lib['format']->Amount(array('value'=>$row2->UnitCustPrice, 'return'=>'value')), 'width'=>'15', 'tabindex'=>$tabindex++, 'class'=>'number')) ?></td>
        <td><? print $row2->Vat ?>%<? #print $_lib['form3']->vat_menu3(array('percent2'=>'1', 'table'=>$db_table2, 'field'=>'Vat', 'pk'=>$LineID, 'value'=>$row2->Vat, 'SaleMenu'=>'1', 'date' => $row->InvoiceDate)) ?></td>
        <td align="right"><? print $_lib['format']->Amount($vatline) ?></td>
        <td align="right"><? print $_lib['format']->Amount($sumline) ?></td>
        <td>
        <? 
// if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit' && $accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel'))) { 
?>
        <a href="<? print $_SETUP[DISPATCH]."t=invoicerecurring.edit&RecurringID=$RecurringID&action_invoicerecurring_outlinedelete=1&amp;LineID=$LineID&amp;inline=edit" ?>" class="button">Slett</a>
        <? 
//} 
?>
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
	print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoicerecurring_linenew', 'value'=>'Ny fakturalinje (N)', 'accesskey'=>'N'));
        ?>
        <td colspan="6" align="right">
	<?
	if($_lib['sess']->get_person('AccessLevel') >= 2 && $inline == 'edit')
	{
               	print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_invoicerecurring_delete', 
				'value'=>'Slett faktura (D)', 'accesskey'=>'D', 
				'confirm' => 'Er du sikker p&aring; at du vil slette denne malen?'));
	}
	
	print $_lib['form3']->Input(array('type'=>'submit', 
						'name'=>'action_invoicerecurring_update', 
						'value'=>'Lagre faktura (S)', 'accesskey'=>'S'));

	?>
</form>
</tfoot>
</table>
</body>
</html>
