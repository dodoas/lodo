<?

/* $Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

if($_lib['input']->getProperty('accountplantemplate_AccountPlanID'))
	$AccountPlanID  		= $_lib['input']->getProperty('accountplantemplate_AccountPlanID');
if($_lib['input']->getProperty('AccountPlanID'))
	$AccountPlanID  		= $_lib['input']->getProperty('AccountPlanID');

$AccountPlanType    	= $_lib['input']->getProperty('accountplantemplate_AccountPlanType');
$JournalID      		= $_lib['input']->getProperty('JournalID');
$OrgNumber      		= $_lib['input']->getProperty('OrgNumber');
$DomesticBankAccount 	= $_lib['input']->getProperty('BankAccount');

$func           		= 'reskontro';

$db_table = "accountplantemplate";
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
 * Hack to fetch information when creating new account plan
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

if($JournalID) { ?>
  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&JournalID=<? print "$JournalID"; ?>">Tilbake til bilag <? print $JournalID ?></a>
<? }
  print '<h1>' . $_lib['message']->get() . '</h1>';
?>

<table class="lodo_data">
<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplantemplate.reskontro" method="post">
<input type="hidden" name="accountplantemplate_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="JournalID" value="<? print $JournalID ?>">
  <tr class="result">
    <th colspan="5">Hovedmal Reskontro - <? print $account->AccountPlanType ?></th>
  </tr>
  <tr>
    <td class="menu">Aktiv</td>
    <td></td>
    <td><? $_lib['form2']->checkbox2($db_table, "Active", $account->Active,'') ?> <? print $_lib['form3']->Type_menu3(array('table' => $db_table, 'field'=>'AccountPlanType', 'value' => $AccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1', 'disabled' => 1)) ?></td>
    <td>Viktig! M&aring; settes riktig for at regnskapet skal fungere</td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Plassering av fakturatekst:</td>
    <td></td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'InvoiceCommentCustomerPosition', 'value'=>$account->InvoiceCommentCustomerPosition, 'type'=>'InvoiceCommentCustomerPosition', 'required'=>'1')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Land</td>
    <td></td>
    <td><? print $_lib['form3']->Country_menu3(array('table'=>'accountplantemplate', 'field'=>'CountryCode', 'value'=>$account->CountryCode, 'required'=>false)); ?></td>
    <td colspan="2">&nbsp</td>
  </tr>
  <tr class="result">
    <th colspan="5">Bilagsf&oslash;ringsinformasjon</th>
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
    <td class="<? print $account->DebitColor ?>"><input type="text" name="accountplantemplate.debittext" value="<? print $account->debittext  ?>" size="30"></td>
    <td>Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'DebitColor', 'value'=>$account->DebitColor, 'type'=>'DebitColor', 'required' => 1)) ?></td>
  </tr>
  <tr>
    <td class="menu">Kredit tekst</td>
    <td></td>
    <td class="<? print $account->CreditColor ?>"><input type="text" name="accountplantemplate.credittext" value="<? print $account->credittext  ?>" size="30"></td>
    <td>Farge:</td>
    <td><? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'CreditColor', 'value'=>$account->CreditColor, 'type'=>'CreditColor', 'required' => 1)) ?></td>
  </tr>
  <tr>
    <td class="menu">Mengde</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableQuantity", $account->EnableQuantity,'') ?></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Standard: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'unset' => true)) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Standard: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V', 'unset' => true)) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Kreditt tid</td>
    <td><input type="hidden" name="<?= $db_table ?>.EnableCredit" value="1" /></td>

    <td><input type="text" name="accountplantemplate.CreditDays" value="<? print $account->CreditDays ?>" size="4" class="number">Dager</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableAutogiro", $account->EnableAutogiro,''); ?> Autogiro</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableNettbank", $account->EnableNettbank,''); ?> Nettbank</td>
  </tr>
  <tr>
    <td class="menu">Motkontoer resultat</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableMotkontoResultat", $account->EnableMotkontoResultat,'') ?></td>
    <td colspan="3">
    <?
        $aconf = array();
        $aconf['type'][]        = 'result';
        $aconf['table']         = 'accountplantemplate';
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
        $aconf['type'][]        = 'balance';
        $aconf['table']         = 'accountplantemplate';
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
    <td><input type="text" name="accountplantemplate.AccountLineFreeTextMatch" value="<? print $account->AccountLineFreeTextMatch  ?>" size="30"></td>
    <td>Brukes av bankavstemming/kontoutskrift importen for &aring; automatisk<br /> sette dette kontonummeret p&aring; en transaksjon som har en matchende tekst</td>
    <td></td>
  </tr>
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
        <? print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_accountplan_update', 'accesskey' => 'S')) ?>
    <? } ?>
    </td>
    </form>
  </tr>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
