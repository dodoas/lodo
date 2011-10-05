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
if($OrgNumber)
	$query   .= " OrgNumber = '$OrgNumber'";
elseif($DomesticBankAccount)
	$query   .= " DomesticBankAccount = '$DomesticBankAccount'";
elseif($AccountPlanID) #since this is automatically created
	$query   .= " AccountPlanID = $AccountPlanID";

#print "query: $query<br>\n";
$account = $_lib['storage']->get_row(array('query' => $query));

/*
 * Hack for √• f√• korrekt info ved opprettelse av ny konto 
 */
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
    
print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - kontoplan - reskontro - <? print $account->AccountPlanType ?></title>
    <meta name="cvs"                content="$Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
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

    <li><a href="<? print $_lib['sess']->dispatch ?>t=postmotpost.list&report_Sort=JournalID&AccountPlanID=2400&ReskontroFromAccount=<? print $AccountPlanID ?>&ReskontroToAccount=<? print $AccountPlanID ?>">&Aring;pne poster for <? print $account->AccountName ?></a></li>
    <li><a href="<? print $_lib['sess']->dispatch ?>t=report.reskontrovoucherprint&report.Type=reskontro&report.Sort=VoucherDate&report.selectedAccount=2400&report.FromAccount=<? print $AccountPlanID ?>&report.ToAccount=<? print $AccountPlanID ?>&report.FromPeriod=<? print $_lib['sess']->get_session('PeriodStartYear') ?>&report.ToPeriod=<? print $_lib['sess']->get_session('PeriodEndYear') ?>">Bilagsutskrift innev&aelig;rende &aring;r for <? print $account->AccountName ?></a></li>

<? } else { ?> 
    <li><a href="<? print $_lib['sess']->dispatch ?>t=postmotpost.list&report_Sort=JournalID&AccountPlanID=1500&ReskontroFromAccount=<? print $AccountPlanID ?>&ReskontroToAccount=<? print $AccountPlanID ?>">&Aring;pne poster for <? print $account->AccountName ?></a></li>
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
    <th colspan="5">Reskontro - <? print $account->AccountPlanType ?> - <? print $AccountPlanID ?> (underkonto til hovedbok)</th>
  </tr>
  <tr>
    <td class="menu">Aktiv</td>
    <td></td>
    <td><? $_lib['form2']->checkbox2($db_table, "Active", $account->Active,'') ?> <? print $_lib['form3']->Type_menu3(array('table' => $db_table, 'field'=>'AccountPlanType', 'value' => $AccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1', 'disabled' => 1)) ?></td>
    <td>Viktig! M&aring; settes riktig for at regnskapet skal fungere</td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Navn</td>
    <td></td>
    <td><input type="text" name="accountplan.AccountName" value="<? print $account->AccountName  ?>" size="30"><? if($account->AccountName) { ?><a href="http://w2.brreg.no/enhet/sok/treffliste.jsp?navn=<? print urlencode($account->AccountName) ?>" target="top">brreg</a><? } ?></td>
    <td>Organisasjonsnummer (opplysninger hentes automatisk basert p&aring; orgnummer)</td>
    <td><input type="text" name="accountplan.OrgNumber" value="<? print $account->OrgNumber  ?>" size="30"><? if($account->OrgNumber) { ?><a href="http://w2.brreg.no/enhet/sok/detalj.jsp?orgnr=<? print $account->OrgNumber ?>" target="top">brreg</a><? } ?></td>
  </tr>
  </tr>
  <tr>
    <td class="menu">Adresse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoiceAddress", $account->EnableInvoiceAddress,'') ?></td>
    <td><input type="text" name="accountplan.Address" value="<? print $account->Address  ?>" size="30"></td>
    <td>MVA-nummer</td>
    <td><input type="text" name="accountplan.VatNumber" value="<? print $account->VatNumber  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Postnummer</td>
    <td></td>
    <td><input type="text" name="accountplan.ZipCode" value="<? print $account->ZipCode  ?>" size="6"></td><td>Poststed</td><td><input type="text" name="accountplan.City" value="<? print $account->City  ?>" size="30">
    </td>
  </tr>
  <tr>
    <td class="menu">Postboks</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoicePoBox", $account->EnableInvoicePoBox,'') ?></td>
    <td><input type="text" name="accountplan.IPoBox" value="<? print $account->IPoBox  ?>" size="4"></td>
    <td>Postbokssted</td>
    <td><input type="text" name="accountplan.IPoBoxCity" value="<? print $account->IPoBoxCity  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Postbokspostnummer</td>
    <td></td>
    <td><input type="text" name="accountplan.IPoBoxZipCode" value="<? print $account->IPoBoxZipCode  ?>" size="6"></td>
    <td>Postbokspostnummersted</td>
    <td><input type="text" name="accountplan.IPoBoxZipCodeCity" value="<? print $account->IPoBoxZipCodeCity  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Land</td>
    <td></td>
    <td><? print $_lib['form3']->Country_menu3(array('table'=>'accountplan', 'field'=>'CountryCode', 'value'=>$account->CountryCode, 'required'=>false)); ?></td>
    <td colspan="2">&nbsp</td>
  </tr>
  <tr>
    <td class="menu">Telefon</td>
    <td></td>
    <td><input type="text" name="accountplan.Phone" value="<? print $account->Phone  ?>" size="30"></td>
    <td>Mobil</td>
    <td><input type="text" name="accountplan.Mobile" value="<? print $account->Mobile  ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">E-Post</td>
    <td></td>
    <td><input type="text" name="accountplan.Email" value="<? print $account->Email  ?>" size="30"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Tekst informasjon</td>
    <td></td>
    <td colspan="3"><input type="text" name="accountplan.Description" value="<? print $account->Description  ?>" size="70"></td>
  </tr>
  <tr>
    <td class="menu">Plassering av fakturatekst:</td>
    <td></td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'InvoiceCommentCustomerPosition', 'value'=>$account->InvoiceCommentCustomerPosition, 'type'=>'InvoiceCommentCustomerPosition', 'required'=>'1')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr class="result">
    <th colspan="5">Bilagsf&oslash;ringsinformasjon</th>
  </tr>
  <tr>
    <td class="menu">Kundenummer hos mottaker</td>
    <td></td>
    <td><input type="text" name="accountplan.CustomerNumber" value="<? print $account->CustomerNumber  ?>" size="30"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Bankkonto (norsk)</td>
    <td></td>
    <td><input type="text" name="accountplan.DomesticBankAccount" value="<? print $account->DomesticBankAccount  ?>" size="30"></td>
    <td>IBAN (utenlandsk)</td>
    <td><input type="text" name="accountplan.IBAN" value="<? print $account->IBAN ?>" size="30"></td>
  </tr>
  <tr>
    <td class="menu">Valuta</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableCurrency", $account->EnableCurrency,'') ?></td>
    <td><? $_lib['form2']->currency_menu2($db_table, "Currency", $account->Currency) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Debit tekst</td>
    <td></td>
    <td class="<? print $account->DebitColor ?>"><input type="text" name="accountplan.debittext" value="<? print $account->debittext  ?>" size="30"></td>
    <td>Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'DebitColor', 'value'=>$account->DebitColor, 'type'=>'DebitColor', 'required' => 1)) ?></td>
  </tr>
  <tr>
    <td class="menu">Kredit tekst</td>
    <td></td>
    <td class="<? print $account->CreditColor ?>"><input type="text" name="accountplan.credittext" value="<? print $account->credittext  ?>" size="30"></td>
    <td>Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'CreditColor', 'value'=>$account->CreditColor, 'type'=>'CreditColor', 'required' => 1)) ?></td>
  </tr>
  <!--<tr>
    <td class="menu">KID referanse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnablePostPost",$account->EnablePostPost,''); ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>-->
  <!--tr>VAT should only be set on hovedbok, not on reskontro.
    <td class="menu">Mva kode</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableVAT", $account->EnableVAT,''); ?></td>
    <td><? print $_lib['form3']->vat_menu3(array('table'=>$db_table, 'field'=>'VatID', 'value'=>$account->VatID, 'vatid'=>'1', 'disabled' => true)) ?></td>
    <td>Mva kode kan overstyres:</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableVATOverride", $account->EnableVATOverride,'') ?></td>
    </td>
  </tr-->
  <tr>
    <td class="menu">Mengde</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableQuantity", $account->EnableQuantity,'') ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Default: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Default: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Kreditt tid</td>
    <td><input type="hidden" name="<?= $db_table ?>.EnableCredit" value="1" /></td>

    <td><input type="text" name="accountplan.CreditDays" value="<? print $account->CreditDays ?>" size="4" class="number">Dager</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableAutogiro", $account->EnableAutogiro,''); ?> Autogiro</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableNettbank", $account->EnableNettbank,''); ?> Nettbank</td>
  </tr>
  <tr>
    <td class="menu">Motkontoer resultat</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableMotkontoResultat", $account->EnableMotkontoResultat,'') ?></td>
    <td colspan="3">
    <?
        $aconf = array();
        $aconf['type'][]  		= 'result';
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
    <td colspan="3">
    <?
        $aconf = array();
        $aconf['type'][]  		= 'balance';
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
    <td>Brukes av bankavstemming/kontoutskrift importen for &aring; automatisk<br /> sette dette kontonummeret p&aring; en transaksjon som har en matchende tekst</td>
    <td></td>
  </tr>
  <!--tr>Pengeflyt bilr alltid overstyrt fra respektive hovedbokskonto - så ingen grunn til å kunne endre det her.
    <td class="menu">Pengeflyt</td>
    <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'EnableMoneyFlow', 'value'=>$account->EnableMoneyFlow, 'disabled' => true)) ?></td>
    <td>Oppstartsaldo <? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'EnableSaldo', 'value'=>$account->EnableSaldo)) ?></td>
  </tr-->
  <tr class="result">
    <th colspan="5">Logg</th>
  </tr>
  <tr>
    <td class="menu">Sist brukt i bilag</td>
    <td></td>
    <td><? print $_lib['format']->Date($account->LastUsedTime) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Opprettet av </td>
    <td></td>
    <td><? print $_lib['format']->PersonIDToName($account->InsertedByPersonID)  ?></td>
    <td></td>
    <td><? print $_lib['format']->Date($account->InsertedDateTime)  ?></td>
  </tr>
  <tr>
    <td class="menu">Endret av </td>
    <td></td>
    <td><? print $_lib['format']->PersonIDToName($account->UpdatedByPersonID)  ?></td>
    <td></td>
    <td><? print $_lib['format']->Date($account->TS)  ?></td>
  </tr>
  <tr>
    <td colspan="5" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <? print $_lib['form3']->submit(array('value'=>'Oppdater fra Br&oslash;nn&oslash;ysund basert p&aring; organisasjonsnummer(U)', 'name'=>'action_accountplan_updateautomatic', 'accesskey' => 'U', 'confirm' => 'Opplysninger som er endret kan bli overskrevet')) ?>
        <? print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_accountplan_update', 'accesskey' => 'S')) ?>
    <? } ?>
    </td>
    </form>
  </tr>
  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
  <tr>
    <td colspan="4" align="right">
        <form name="delete" action="<? print $_SETUP['DISPATCH'] ?>t=accountplan.list&accountplan_type=hovedbok" method="post">
        <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID', 'value'=>$AccountPlanID)) ?>
        <? print $_lib['form3']->submit(array('value'=>'Deaktiver (D)', 'name'=>'action_accountplan_deactivate', 'accesskey'=>'D')) ?>
        <? if($_lib['sess']->get_person('AccessLevel') > 3) {
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
