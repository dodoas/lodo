<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('fakturabank/fakturabank');
includelogic('accounting/accounting');
includelogic('exchange/exchange');
includelogic('orgnumberlookup/orgnumberlookup');

$accounting = new accounting();

require_once "record.inc";
$fb         = new lodo_fakturabank_fakturabank();
$InvoicesO  = $fb->incoming();

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
  <a href="<? print $_lib['sess']->dispatch ?>t=fakturabank.listincoming">Hent faktura p&aring; nytt</a>
  <br>
  <a href="<? print $_lib['sess']->dispatch ?>t=invoicein.list">Tilbake til innkommende fakturaer</a>
</h2>
<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>


<h3>Alle innkommende fakturaer fra Fakturabank
<? if($_lib['setup']->get_value('fakturabank.status')) { ?>
    med status <? print $_lib['setup']->get_value('fakturabank.status') ?>
<? } ?>
som ikke er lastet ned.
</h3>



Merk: Du m&aring; registrere brukeren din p&aring; <a href="http://fakturabank.no">http://fakturabank.no</a> for at dette skal fungere

<form name="invoice_edit" action="<? print $_lib['sess']->dispatch ?>t=fakturabank.listincoming" method="post">
<input type="submit" value="Last ned fakturaer (L)" name="action_fakturabank_registerincoming" accesskey="B">
<input type="submit" value="Opprett manglende kontoplaner (A)" name="action_fakturabank_addmissingaccountplan" accesskey="A">


<table class="lodo_data">
<thead>
<tr>
    <th class="number">Bilag</th>
    <th class="number">Faktura nr</th>
    <th class="number">Fakturadato</th>
    <th class="number">Periode</th>
    <th class="number">Org Nummer</th>
    <th class="number">Lev. Konto</th>
    <th>Firmanavn</th>
    <th>Motkonto</th>
    <th>MotkontoNavn</th>
    <th class="number">Forfallsdato</th>
    <th class="number">Bel&oslash;p</th>
    <th>Avdeling</th>
    <th>Prosjekt</th>
    <th>&Aringrsaks informasjon</th>
    <th class="number">Bankkonto</th>
    <th class="number">KID</th>
    <th class="number">Utskrift</th>
    <th class="number">Status</th>
</tr>
</thead>
<tbody>
<?
// clean temporary account plan data table before adding anything
$sql  = "delete from accountplantemp where 1";
$_lib['db']->db_delete($sql);

if (!empty($InvoicesO->Invoice)) {

  $used_new_accountplans = array();
  $already_printed_new = array();
  $already_fetched_org = array();
  foreach($InvoicesO->Invoice as $InvoiceO) {
    $TotalCustPrice += $InvoiceO->LegalMonetaryTotal->PayableAmount;
    $tmp_currency_code = $InvoiceO->DocumentCurrencyCode;
    if ($InvoiceO->MissingAccountPlan) {
      $scheme_value = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID;
      $scheme_type  = $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID_Attr_schemeID;
      // speed up but only fetching company info for non fetched ones
      if (!isset($already_fetched_org[$scheme_value])) {
        $org = new lodo_orgnumberlookup_orgnumberlookup();
        $org->getOrgNumberByScheme($scheme_value, $scheme_type);
        $already_fetched_org[$scheme_value] = $org;
      }
      else $org = $already_fetched_org[$scheme_value];

      // initial setup
      $TmpAccountPlanData = array();
      $TmpAccountPlanData['AccountName']       = $InvoiceO->AccountingSupplierParty->Party->PartyName->Name;
      $TmpAccountPlanData['AccountPlanType']   = 'supplier';

      $TmpAccountPlanData['Address']           = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->StreetName;
      $TmpAccountPlanData['City']              = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->CityName;
      $TmpAccountPlanData['ZipCode']           = $InvoiceO->AccountingSupplierParty->Party->PostalAddress->PostalZone;

      $TmpAccountPlanData['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
      $TmpAccountPlanData['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
      $TmpAccountPlanData['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
      $TmpAccountPlanData['Active']            = 1;

      $TmpAccountPlanData['EnableCredit']      = 1;
      $TmpAccountPlanData['CreditDays']        = $_lib['date']->dateDiff($InvoiceO->PaymentMeans->PaymentDueDate, $InvoiceO->IssueDate);

      $TmpAccountPlanData['debittext']         = 'Salg';
      $TmpAccountPlanData['credittext']        = 'Betal';
      $TmpAccountPlanData['DebitColor']        = 'debitblue';
      $TmpAccountPlanData['CreditColor']       = 'creditred';

      $FakturabankScheme = $_lib['storage']->get_row(array('query' => "select FakturabankSchemeID from fakturabankscheme where SchemeType = '$scheme_type'"));
      $FakturabankSchemeID = $FakturabankScheme->FakturabankSchemeID;
      $TmpAccountPlanData['FBSchemeLodoID']    = $FakturabankSchemeID;
      $TmpAccountPlanData['FBSchemeType']      = $scheme_type;
      $TmpAccountPlanData['FBSchemeValue']     = $scheme_value;

      // info we get from FB
      if($org->OrgNumber)   $TmpAccountPlanData['OrgNumber'] = $org->OrgNumber;
      if($org->AccountName) $TmpAccountPlanData['AccountName'] = $org->AccountName;
      if($org->Email)       $TmpAccountPlanData['Email'] = $org->Email;
      if($org->Mobile)      $TmpAccountPlanData['Mobile'] = $org->Mobile;
      if($org->Phone)       $TmpAccountPlanData['Phone'] = $org->Phone;
      if($org->URL)         $TmpAccountPlanData['Web'] = $org->URL;
      if(!empty($org->ParentCompanyName))    $TmpAccountPlanData['ParentName'] = $org->ParentCompanyName;
      if(!empty($org->ParentCompanyNumber))  $TmpAccountPlanData['ParentOrgNumber'] = $org->ParentCompanyNumber;

      $TmpAccountPlanData['EnableInvoiceAddress'] = 1;
      if($org->IAdress->Address1) $TmpAccountPlanData['Address'] = $org->IAdress->Address1;
      if($org->IAdress->City)     $TmpAccountPlanData['City'] = $org->IAdress->City;
      if($org->IAdress->ZipCode)  $TmpAccountPlanData['ZipCode'] = $org->IAdress->ZipCode;

      if($org->IAdress->Country)  $TmpAccountPlanData['CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);

      if($org->DomesticBankAccount) $TmpAccountPlanData['DomesticBankAccount'] = $org->DomesticBankAccount;

      if($org->CreditDays) {
        $TmpAccountPlanData['EnableCredit'] = 1;
        $TmpAccountPlanData['CreditDays'] = $org->CreditDays;
      }
      if($org->MotkontoResultat1)	{
        $TmpAccountPlanData['EnableMotkontoResultat'] = 1;
        $TmpAccountPlanData['MotkontoResultat1'] = $org->MotkontoResultat1;
        $TmpMotkonto = $org->MotkontoResultat1;
      }
      elseif($org->MotkontoBalanse1) {
        $TmpAccountPlanData['EnableMotkontoBalanse'] = 1;
        $TmpAccountPlanData['MotkontoBalanse1'] = $org->MotkontoBalanse1;
        $TmpMotkonto = $org->MotkontoBalanse1;
      }
      else $TmpMotkonto = 0;
      $TmpAccountPlanData['Active'] = 1;
    }
  ?>

    <?  
    // new logic for getting reason information
    $ReasonsInfo = "";
    foreach($InvoiceO->ReconciliationReasons as $Reason) {
        $r_query = sprintf("SELECT * FROM fakturabankinvoicereconciliationreason WHERE FakturabankInvoiceReconciliationReasonID = %d", $Reason[0]);
        $r_row = $_lib['storage']->get_row(array('query' => $r_query, 'debug' => true));
        $ReasonsInfo = $ReasonsInfo . $r_row->FakturabankInvoiceReconciliationReasonCode . " = " . $_lib['format']->Amount($Reason[1]) . " | ";
    }
    // remove last 3 characters " | "
    $ReasonsInfo = substr($ReasonsInfo, 0, -3);
    ?>

    <tr class="<? print $InvoiceO->Class ?>">
      <td class="number"><? if($InvoiceO->Journaled) { ?><a href="<? print $_SETUP[DISPATCH]."t=journal.edit&amp;voucher_VoucherType=$InvoiceO->VoucherType&amp;voucher_JournalID=$InvoiceO->JournalID"; ?>&amp;action_journalid_search=1" target="_new"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a><? } else { ?><i><a title="Foresl&aring;tt bilagsnummer - dette kan endre seg"><? print $InvoiceO->VoucherType ?><? print $InvoiceO->JournalID ?></a></i><? } ?></td>
      <td class="number"><? print $InvoiceO->ID ?></td>
      <td class="number"><? print $InvoiceO->IssueDate ?></td>
      <td class="number"><? print $InvoiceO->Period ?></td>
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&OrgNumber=<? print $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID ?>&inline=show" target="_new"><? print $InvoiceO->AccountingSupplierParty->Party->PartyLegalEntity->CompanyID ?></a></td>
      <td class="number">
        <? if (!$InvoiceO->MissingAccountPlan) { // if account exists print link to it ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&AccountPlanID=<? print $InvoiceO->AccountPlanID ?>&inline=show" target="_new"><? print $InvoiceO->AccountPlanID ?></a>
        <? } else { // print input(or just number in case of NO:ORGNR) for account plan id and add scheme value to already printed array
              if (isset($already_printed_new[$scheme_value]) && ($scheme_type != 'NO:ORGNR')) print 'Velg ovenfor'; // only print select above for non NO:ORGNR
              elseif ($scheme_type == 'NO:ORGNR') {
                $TemporaryAccountPlanID = $scheme_value;
                print $TemporaryAccountPlanID;
        ?>
                <input type='hidden' name='<? print "accountplantemp_" . $TemporaryAccountPlanID . "_AccountPlanID"; ?>' value='<? print $TemporaryAccountPlanID; ?>' >
        <?
              }
              else {
                // find the first available account plan id
                $starting_id = 100000001;
                if ($_lib['sess']->get_companydef('BaseAccountIDOnMotkonto')) $starting_id += $org->MotkontoResultat1 * 10000;
                $used_accounts_hash = $_lib['storage']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "select AccountPlanID from accountplan where AccountPlanType = 'supplier' and AccountPlanID >= $starting_id order by AccountPlanID"));
                for ($i = $starting_id; $i <= 999999999; $i++) {
                  if (!isset($used_accounts_hash[$i]) && !isset($used_new_accountplans[$i])) break;
                }
                $TemporaryAccountPlanID = $i;
                $used_new_accountplans[$TemporaryAccountPlanID] = $TemporaryAccountPlanID;
        ?>
                <input name='<? print "accountplantemp_" . $TemporaryAccountPlanID . "_AccountPlanID"; ?>' value='<? print $TemporaryAccountPlanID; ?>' style='text-align: right' onkeypress="return event.charCode >= 48 && event.charCode <= 57">
        <?
              }
              $TmpAccountPlanData['AccountPlanID']     = $TemporaryAccountPlanID;
           }
        ?>
      </td>
      <td>&nbsp;<? print substr($InvoiceO->AccountingSupplierParty->Party->PartyName->Name,0,30) ?></td>
      <td>&nbsp;
        <? if (!$InvoiceO->MissingAccountPlan) { //if motkonto exists(account plan exists) print it
          print substr($InvoiceO->MotkontoAccountPlanID,0,30);
           } elseif (isset($already_printed_new[$scheme_value])) print 'Velg ovenfor';
             else { // motkonto balanse and result dropdown select
              $aconf = array();
              $aconf['type'][] = 'result';
              $aconf['type'][] = 'balance';
              $aconf['table']  = 'accountplantemp_' . $TemporaryAccountPlanID;
              $aconf['field']  = 'Motkonto';
              $aconf['value']  = $TmpMotkonto;
              print $_lib['form3']->accountplan_number_menu($aconf);
           } ?>
      </td>
      <td><? print $InvoiceO->MotkontoAccountName ?></td>
      <td class="number"><b><? print $InvoiceO->PaymentMeans->PaymentDueDate ?></b></td>
      <!--<td class="number"><? print $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ?></td>-->
      <td class="number">
<? if ($tmp_currency_code == exchange::getLocalCurrency()) { ?>
        <? print $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ?>
<? } else {
        $conv = exchange::convertToLocal($tmp_currency_code, $InvoiceO->LegalMonetaryTotal->PayableAmount);
        $rate = exchange::getConversionRate($tmp_currency_code);
        if ($conv) {
            print " (". $tmp_currency_code ." ". $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) ." / $rate) ";
            print $_lib['format']->Amount($conv);
        } else {
            $conv = $_lib['format']->Amount($conv);
            print "Valutaverdi for ". $tmp_currency_code ." er ikke satt";
            print " (". $tmp_currency_code ." ". $_lib['format']->Amount($InvoiceO->LegalMonetaryTotal->PayableAmount) .") ";
       }
   }
?>
      </td>
      <td class="number"><? print $InvoiceO->Department ?></td>
      <td class="number"><? print $InvoiceO->Project ?></td>
      <td class="number" title="<? print $ReasonsInfo ?>"><?
        if (strlen($ReasonsInfo) > 40){
         print substr($ReasonsInfo, 0, 37) . '...';
        }else {
          print $ReasonsInfo;
        } ?>
      </td>
      <td class="number"><? print $InvoiceO->PaymentMeans->PayeeFinancialAccount->ID ?></td>
      <td class="number"><? print $InvoiceO->PaymentMeans->InstructionID ?></td>
      <td align="center"><a href="<?php echo $_SETUP['FB_SERVER_PROTOCOL'] ."://". $_SETUP['FB_SERVER']; ?>/invoices/<? print $InvoiceO->FakturabankID; ?>" title="Vis faktura i fakturabank" target="_new">Vis</a>
      <td class="number"><? print $InvoiceO->Status ?></td>
  </tr>
<?
    // only add to temp table the first time
    if (!isset($already_printed_new[$scheme_value])) {
      $_lib['storage']->store_record(array('data' => $TmpAccountPlanData, 'table' => 'accountplantemp', 'action' => 'auto', 'debug' => false));
      $already_printed_new[$scheme_value] = true;
    }
  }
}
?>
<tr>
    <th colspan="9"></th>
    <th>SUM</th>
    <th class="number"><? print  $_lib['format']->Amount($TotalCustPrice) ?></th>
    <th colspan="7"></th>
</tr>
</tbody>

</table>
</form>
</body>
</html>
