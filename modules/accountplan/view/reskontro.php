<?

/* $Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

if($_lib['input']->getProperty('accountplan_AccountPlanID'))
	$AccountPlanID  		= $_lib['input']->getProperty('accountplan_AccountPlanID');
if($_lib['input']->getProperty('AccountPlanID'))
	$AccountPlanID  		= $_lib['input']->getProperty('AccountPlanID');
$AccountPlanType    	= $_lib['input']->getProperty('accountplan_AccountPlanType');
$JournalID      		= $_lib['input']->getProperty('JournalID');
$OrgNumber      		= $_lib['input']->getProperty('OrgNumber');
$DomesticBankAccount 	= $_lib['input']->getProperty('BankAccount');

$func           		= 'reskontro';

$db_table = "accountplan";
require_once "record.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where ";
if($OrgNumber && !isset($_GET['force_new']))
	$query   .= " OrgNumber = '$OrgNumber'";
elseif($DomesticBankAccount)
	$query   .= " DomesticBankAccount = '$DomesticBankAccount'";
elseif($AccountPlanID) #since this is automatically created
	$query   .= " AccountPlanID = $AccountPlanID";

#print "query: $query<br>\n";
$account = $_lib['storage']->get_row(array('query' => $query));

/*
 * Hack to get the correct information when creating new account plan
 */
if(!$account && $_lib['input']->getProperty('NewAccount'))
{
    /* fetch data from template */
    $query = sprintf("SELECT * FROM accountplantemplate WHERE AccountPlanType = '%s'", $AccountPlanType);
    $template_r = $_lib['db']->db_query($query);
    $template = $_lib['db']->db_fetch_assoc($template_r);

    if($template) {
        foreach($template as $k => $v) {
            if($k == "AccountPlanID")
                continue;

            $account->$k = $v;
        }
    }
}

if($OrgNumber && $_lib['input']->getProperty('NewAccount'))
{
    $account->OrgNumber = $OrgNumber;
    $account->EnableCredit = 1;
}


if($account->AccountPlanID){
    $AccountPlanID = $account->AccountPlanID;
}
if($account->AccountPlanType) {
    $AccountPlanType = $account->AccountPlanType;
}

$gln_q = sprintf("SELECT GLN FROM accountplangln WHERE AccountPlanID = '%d'", $AccountPlanID);
$gln_r = $_lib['db']->db_query($gln_q);
$gln_row = $_lib['db']->db_fetch_assoc($gln_r);
$gln_number = $gln_row['GLN'];

$swift_q = sprintf("SELECT * FROM accountplanswift WHERE AccountPlanID = '%d'", $AccountPlanID);
$swift_r = $_lib['db']->db_query($swift_q);
$swift_row = $_lib['db']->db_fetch_assoc($swift_r);
$swift_number = $swift_row['Swift'];
$swift_number_account = $swift_row['SwiftAccount'];

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - kontoplan - reskontro - <? print $account->AccountPlanType ?></title>
    <meta name="cvs"                content="$Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
    <style type="text/css">
      .highlighted {
        background-color: #FFF655;
      }
      .read-only-input {
        color: gray !important;
      }
    </style>
    <script type="text/javascript">
      function hightlightFirmaID() {
        $("#firma_id_fields").addClass("highlighted");
        setTimeout(function () {
          $("#firma_id_fields").removeClass("highlighted");
        }, 1000);
      }
      // this functions makes focus on the input next to the select which has selected value same as provided scheme_type
      function focusFirmaidSchemeWithSchemetype(scheme_type) {
        $("#firma_id_fields").find('select option:selected:contains("' + scheme_type + '")').parent("select").next("input").focus();
      }
    </script>
</head>
<body>

<?
includeinc('top');
includeinc('left');

if($JournalID) {
  ?><a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&JournalID=<? print "$JournalID"; ?>">Tilbake til bilag <? print $JournalID ?></a>
<? } ?>

<ul>
<? if($account->AccountPlanType == 'supplier') { ?>

    <li><a href="<? print $_lib['sess']->dispatch ?>t=reconciliation.list&report_Sort=JournalID&AccountPlanID=2400&ReskontroFromAccount=<? print $AccountPlanID ?>&ReskontroToAccount=<? print $AccountPlanID ?>">&Aring;pne poster for <? print $account->AccountName ?></a></li>
    <li><a href="<? print $_lib['sess']->dispatch ?>t=report.reskontrovoucherprint&report.Type=reskontro&report.Sort=VoucherDate&report.selectedAccount=2400&report.FromAccount=<? print $AccountPlanID ?>&report.ToAccount=<? print $AccountPlanID ?>&report.FromPeriod=<? print $_lib['sess']->get_session('PeriodStartYear') ?>&report.ToPeriod=<? print $_lib['sess']->get_session('PeriodEndYear') ?>">Bilagsutskrift innev&aelig;rende &aring;r for <? print $account->AccountName ?></a></li>

<? } else { ?>
    <li><a href="<? print $_lib['sess']->dispatch ?>t=reconciliation.list&report_Sort=JournalID&AccountPlanID=1500&ReskontroFromAccount=<? print $AccountPlanID ?>&ReskontroToAccount=<? print $AccountPlanID ?>">&Aring;pne poster for <? print $account->AccountName ?></a></li>
    <li><a href="<? print $_lib['sess']->dispatch ?>t=report.reskontrovoucherprint&report.Type=reskontro&report.Sort=VoucherDate&report.selectedAccount=1500&report.FromAccount=<? print $AccountPlanID ?>&report.ToAccount=<? print $AccountPlanID ?>&report.FromPeriod=<? print $_lib['sess']->get_session('PeriodStartYear') ?>&report.ToPeriod=<? print $_lib['sess']->get_session('PeriodEndYear') ?>">Bilagsutskrift innev&aelig;rende &aring;r for <? print $account->AccountName ?></a></li>

<? } ?>
</ul>

<? print '<h1>' . $_lib['message']->get() . '</h1>'; ?>

<? require_once 'new.inc'; ?>

<table class="lodo_data">
<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro" method="post">
<input type="hidden" name="accountplan_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="JournalID" value="<? print $JournalID ?>">
  <tr class="result">
    <th colspan="6">Reskontro - <? print $account->AccountPlanType ?> - <? print $AccountPlanID ?> (underkonto til hovedbok)</th>
  </tr>
  <tr>
    <td class="menu">Aktiv</td>
    <td></td>
    <td><? $_lib['form2']->checkbox2($db_table, "Active", $account->Active,'') ?> <? print $_lib['form3']->Type_menu3(array('table' => $db_table, 'field'=>'AccountPlanType', 'value' => $AccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1', 'disabled' => 1)) ?></td>
    <td colspan="2">Viktig! M&aring; settes riktig for at regnskapet skal fungere</td>
    <td></td>
  </tr>
  <? if ($account->ParentName) { ?>
  <tr>
    <td class="menu">Parent</td>
    <td></td>
    <td><? print $account->ParentName . " (" . $account->ParentOrgNumber . ")"  ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <? } ?>
  <tr>
    <td class="menu">Navn</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.AccountName" value="<? print $account->AccountName  ?>" size="30"><? if($account->AccountName) { ?><a href="http://w2.brreg.no/enhet/sok/treffliste.jsp?navn=<? print urlencode($account->AccountName) ?>" target="top">brreg</a><? } ?></td>
    <td style="text-align:right" colspan="2">Organisasjonsnummer (opplysninger hentes automatisk basert p&aring; orgnummer)</td>
    <td><input class="lodoreqfelt read-only-input" type="text" name="accountplan.OrgNumber" value="<? print $account->OrgNumber  ?>" size="30" readonly="readonly" onclick="this.blur(); hightlightFirmaID(); focusFirmaidSchemeWithSchemetype('NO:ORGNR');"><? if($account->OrgNumber) { ?><a href="http://w2.brreg.no/enhet/sok/detalj.jsp?orgnr=<? print $account->OrgNumber ?>" target="top">brreg</a><? } ?></td>
  </tr>
  </tr>
  <tr>
    <td class="menu">Adresse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoiceAddress", $account->EnableInvoiceAddress,'') ?></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Address" value="<? print $account->Address  ?>" size="40"></td>
    <td style="text-align:right" colspan="2">MVA-nummer</td>
    <td><input class="read-only-input" type="text" name="accountplan.VatNumber" value="<? print $account->VatNumber  ?>" size="30" readonly="readonly" onclick="this.blur(); hightlightFirmaID(); focusFirmaidSchemeWithSchemetype('NO:VAT');"></td>
  </tr>
  <tr>
    <td class="menu">Postnummer</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.ZipCode" value="<? print $account->ZipCode  ?>" size="6"></td>
    <td style="text-align:right" colspan="2">Poststed</td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.City" value="<? print $account->City  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Postboks</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoicePoBox", $account->EnableInvoicePoBox,'') ?></td>
    <td><input type="text" name="accountplan.IPoBox" value="<? print $account->IPoBox  ?>" size="4"></td>
    <td style="text-align:right" colspan="2">Postbokssted</td>
    <td><input type="text" name="accountplan.IPoBoxCity" value="<? print $account->IPoBoxCity  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Postbokspostnummer</td>
    <td></td>
    <td><input type="text" name="accountplan.IPoBoxZipCode" value="<? print $account->IPoBoxZipCode  ?>" size="6"></td>
    <td style="text-align:right" colspan="2">Postbokspostnummersted</td>
    <td><input type="text" name="accountplan.IPoBoxZipCodeCity" value="<? print $account->IPoBoxZipCodeCity  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Land</td>
    <td></td>
    <td><? print $_lib['form3']->Country_menu3(array('table'=>'accountplan', 'field'=>'CountryCode', 'value'=>$account->CountryCode, 'required'=>true, 'class'=>'lodoreqfelt')); ?></td>

    <td style="text-align:right" colspan="2">GLN</td>
    <td><input type="text" name="accountplangln.GLN" value="<? print $gln_number ?>" size="30" /></td>
  </tr>
  <tr>
    <td class="menu">Telefon</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Phone" value="<? print $account->Phone  ?>" size="40"></td>
    <td style="text-align:right" colspan="2">Mobil</td>
    <td><input type="text" name="accountplan.Mobile" value="<? print $account->Mobile  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">E-Post</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Email" value="<? print $account->Email  ?>" size="40"></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Web</td>
    <td></td>
    <td><input type="text" name="accountplan.Web" value="<? print $account->Web  ?>" size="40"></td>
    <td colspan="2"></td>
    <td></td>
  </tr>

  <tr>
    <td class="menu">Tekst informasjon</td>
    <td></td>
    <td colspan="4"><input type="text" name="accountplan.Description" value="<? print $account->Description  ?>" size="70"></td>
  </tr>
  <tr>
    <td class="menu">Plassering av fakturatekst:</td>
    <td></td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'InvoiceCommentCustomerPosition', 'value'=>$account->InvoiceCommentCustomerPosition, 'type'=>'InvoiceCommentCustomerPosition', 'required'=>'1')) ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>

  <tbody id="firma_id_fields">
  <? include("schemeid.php") ?>
  </tbody>

  <tr class="result">
    <th colspan="6">Bilagsf&oslash;ringsinformasjon</th>
  </tr>
  <tr>
    <td class="menu">Kundenummer hos mottaker</td>
    <td></td>
    <td><input type="text" name="accountplan.CustomerNumber" value="<? print $account->CustomerNumber  ?>" size="40"></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Bankkonto (norsk)</td>
    <td></td>
    <td><input type="text" name="accountplan.DomesticBankAccount" value="<? print $account->DomesticBankAccount  ?>" size="40"></td>

    <td style="text-align:right" colspan="2">IBAN (utenlandsk)</td>
    <td><input type="text" name="accountplan.IBAN" value="<? print $account->IBAN ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Valuta</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableCurrency", $account->EnableCurrency,'') ?></td>
    <td><? $_lib['form2']->currency_menu2($db_table, "Currency", $account->Currency) ?></td>

    <td style="text-align:right" colspan="2">SWIFT</td>
    <td><input type="text" name="accountplanswift.SWIFT" value="<? print $swift_number ?>" size="30"></td>
  </tr>

  <tr>
    <td class="menu"></td>
    <td></td>
    <td></td>

    <td style="text-align:right" colspan="2">SWIFT ACCOUNT</td>
    <td><input type="text" name="accountplanswift.SWIFTACCOUNT" value="<? print $swift_number_account ?>" size="30"></td>
  </tr>

  <tr>
    <td class="menu">Debit tekst</td>
    <td></td>
    <td class="<? print $account->DebitColor ?>"><input type="text" name="accountplan.debittext" value="<? print $account->debittext  ?>" size="30"></td>
    <td style="text-align:right" colspan="2">Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'DebitColor', 'value'=>$account->DebitColor, 'type'=>'DebitColor', 'required' => 1)) ?></td>
  </tr>
  <tr>
    <td class="menu">Kredit tekst</td>
    <td></td>
    <td class="<? print $account->CreditColor ?>"><input type="text" name="accountplan.credittext" value="<? print $account->credittext  ?>" size="30"></td>
    <td style="text-align:right" colspan="2">Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'CreditColor', 'value'=>$account->CreditColor, 'type'=>'CreditColor', 'required' => 1)) ?></td>
  </tr>
  <tr>
    <td class="menu">Mengde</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableQuantity", $account->EnableQuantity,'') ?></td>
    <td></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Bil</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableCar",$account->EnableCar,'') ?></td>
    <td>Standard: <? $_lib['form2']->car_menu2(array('table' => $db_table, 'field' => 'CarID', 'value' => $account->CarID, 'tabindex' => $tabindex++, 'all_cars' => true, 'unset' => true)) ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Standard: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'unset' => true)) ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Standard: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V', 'unset' => true)) ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Kreditt tid</td>
    <td><input type="hidden" name="<?= $db_table ?>.EnableCredit" value="1" /></td>

    <td><input class="lodoreqfelt" type="text" name="accountplan.CreditDays" value="<? print $account->CreditDays ?>" size="4" class="number">Dager</td>
    <td colspan="2"><? $_lib['form2']->checkbox2($db_table, "EnableAutogiro", $account->EnableAutogiro,''); ?> Autogiro</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableNettbank", $account->EnableNettbank,''); ?> Nettbank</td>
  </tr>
  <tr>
    <td class="menu">Motkontoer resultat</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableMotkontoResultat", $account->EnableMotkontoResultat,'') ?></td>
    <td colspan="4">
    <?
        $aconf = array();
        $aconf['type'][]        = 'result';
        $aconf['table']         = 'accountplan';
        $aconf['field']         = 'MotkontoResultat1';
        $aconf['value']         = $account->MotkontoResultat1;
        print $_lib['form3']->accountplan_number_menu($aconf);
        $aconf['field']         = 'MotkontoResultat2';
        $aconf['value']         = $account->MotkontoResultat2;
        print $_lib['form3']->accountplan_number_menu($aconf);
        $aconf['field']         = 'MotkontoResultat3';
        $aconf['value']         = $account->MotkontoResultat3;
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Motkontoer balanse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableMotkontoBalanse", $account->EnableMotkontoBalanse,'') ?></td>
    <td colspan="4">
    <?
        $aconf = array();
        $aconf['type'][]        = 'balance';
        $aconf['table']         = 'accountplan';
        $aconf['field']         = 'MotkontoBalanse1';
        $aconf['value']         = $account->MotkontoBalanse1;
        print $_lib['form3']->accountplan_number_menu($aconf);
        $aconf['field']         = 'MotkontoBalanse2';
        $aconf['value']         = $account->MotkontoBalanse2;
        print $_lib['form3']->accountplan_number_menu($aconf);
        $aconf['field']         = 'MotkontoBalanse3';
        $aconf['value']         = $account->MotkontoBalanse3;
        print $_lib['form3']->accountplan_number_menu($aconf);
    ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Fritekst match</td>
    <td></td>
    <td><input type="text" name="accountplan.AccountLineFreeTextMatch" value="<? print $account->AccountLineFreeTextMatch  ?>" size="30"></td>
    <td colspan="2">Brukes av bankavstemming/kontoutskrift importen for &aring; automatisk<br /> sette dette kontonummeret p&aring; en transaksjon som har en matchende tekst</td>
    <td></td>
  </tr>
  <tr class="result">
    <th colspan="6">Logg</th>
  </tr>

  <tr>
    <td class="menu">Sist brukt i bilag</td>
    <td></td>
    <td><? print $_lib['format']->Date($account->LastUsedTime) ?></td>
    <td colspan="2"></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Opprettet av </td>
    <td></td>
    <td><? print $_lib['format']->PersonIDToName($account->InsertedByPersonID)  ?></td>
    <td colspan="2"></td>
    <td><? print $_lib['format']->Date($account->InsertedDateTime)  ?></td>
  </tr>
  <tr>
    <td class="menu">Endret av </td>
    <td></td>
    <td><? print $_lib['format']->PersonIDToName($account->UpdatedByPersonID)  ?></td>
    <td colspan="2"></td>
    <td><? print $_lib['format']->Date($account->TS)  ?></td>
  </tr>
  <tr>
    <td colspan="6" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <? print $_lib['form3']->submit(array('value'=>'Oppdater fra Fakturabank(U)', 'name'=>'action_accountplan_updateautomatic', 'accesskey' => 'U', 'confirm' => 'Opplysninger som er endret kan bli overskrevet')) ?>
        <? print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_accountplan_update', 'accesskey' => 'S')) ?>
    <? } ?>
    </td>
  </tr>

  </form>

  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
  <tr>
    <td colspan="5" align="right">
        <form name="delete" action="<? print $_SETUP['DISPATCH'] ?>t=accountplan.list&accountplan_type=hovedbok" method="post">
        <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID', 'value'=>$AccountPlanID));
           print $_lib['form3']->submit(array('value'=>'Deaktiver (D)', 'name'=>'action_accountplan_deactivate', 'accesskey'=>'D'));
           if($_lib['sess']->get_person('AccessLevel') > 3) {
            print $_lib['form3']->submit(array('value'=>'Slett (D)', 'name'=>'action_accountplan_delete', 'accesskey'=>'', 'confirm' => 'Er du sikker p&aring; at du vil slette denne?'));
        } ?>
        </form>
    </td>
  </tr>
  <? } ?>

</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
