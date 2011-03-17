<?
/* $Id: hovedbok.php,v 1.59 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$AccountPlanID      = (int) $_lib['input']->getProperty('accountplan_AccountPlanID');
$AccountPlanType    = $_lib['input']->getProperty('accountplan_AccountPlanType');
$JournalID          = (int) $_lib['input']->getProperty('JournalID');
$func               = 'hovedbok';

includealogic('altinnmapping');

$map1 = new AltinnMapping(1);
$map2 = new AltinnMapping(2);
$map3 = new AltinnMapping(3);
$map4 = new AltinnMapping(4);
$map5 = new AltinnMapping(5);

$db_table = "accountplan";
require_once "record.inc";
#Input parameters should be validated - also against roles
$query   = "select * from $db_table where AccountPlanID = $AccountPlanID";
#print "$query<br>";
$account = $_lib['storage']->get_row(array('query' => $query));

if(strlen($account->AccountPlanType) > 0) {
    #print "Setter konto type: $account->AccountPlanType<br>";
    $AccountPlanType = $account->AccountPlanType;
}
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - kontoplan - hovedbok - <? print $account->AccountPlanType ?></title>
    <meta name="cvs"                content="$Id: hovedbok.php,v 1.59 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body <? if($_lib['message']->get()) { ?> onload="alert('<? print $_lib['message']->get() ?>')"<? } ?> >

<? includeinc('top'); ?>
<? includeinc('left'); ?>

<?
if(isset($JournalID))
{ ?>
   <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&JournalID=<? print $JournalID ?>">Tilbake til bilag <? print $JournalID ?></a>
<?
}

require_once 'new.inc';
print '<h1>' . $_lib['message']->get() . '</h1>';
?>

<h2><? print $AccountPlanID ?></h2>
<table class="lodo_data">
<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplan.hovedbok" method="post">
<input type="hidden" name="accountplan_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="JournalID" value="<? print $JournalID ?>">
  <tr class="result">
    <th colspan="5">Hovedbokskonto - <? print $account->AccountPlanType ?> - <? print $AccountPlanID ?></th>
  </tr>
  <tr>
    <td class="menu">Kontonummer</td>
    <td><? print $AccountPlanID ?></td>

  </tr>
  <tr>
    <td class="menu">Aktiv</td>
    <td colspan="2"><? $_lib['form2']->checkbox2($db_table, "Active", $account->Active,'') ?> <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'AccountPlanType', 'value'=>$AccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1', 'notShowKey' => 1, 'disabled' => 1)) ?> Viktig! M&aring; settes riktig for at regnskapet skal fungere</td>
  </tr>
  <tr>
    <td class="menu">Navn</td>
    <td><input type="text" name="accountplan.AccountName" value="<? print $account->AccountName ?>"   size="50"></td>

  </tr>
  <tr>
    <td class="menu">Lodo konto</td>
    <td><? print $_lib['form3']->Checkbox(array('table'=>$db_table, 'field'=>'EnableNorwegianStandard', 'value'=>$account->EnableNorwegianStandard)); print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'NorwegianStandardText', 'value'=>$account->NorwegianStandardText, 'width'=>'50')) ?>

  </tr>
  <tr>
    <td class="menu">Debit tekst</td>
    <td class="<? print $account->DebitColor ?>"><input type="text" name="accountplan.debittext" value="<? print $account->debittext ?>" size="50">
        Farge: <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'DebitColor', 'value'=>$account->DebitColor, 'type'=>'DebitColor', 'required' => 1)) ?></td>

  </tr>
  <tr>
    <td class="menu">Kredit tekst</td>
    <td class="<? print $account->CreditColor ?>"><input type="text" name="accountplan.credittext" value="<? print $account->credittext ?>" size="50">
        Farge: <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'CreditColor', 'value'=>$account->CreditColor, 'type'=>'CreditColor', 'required' => 1)) ?></td>

  </tr>
  <tr>
    <td class="menu">Valuta</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableCurrency", $account->EnableCurrency,'') ?> <? $_lib['form2']->currency_menu2($db_table, "Currency", $account->Currency) ?></td>

  </tr>
  <tr>
    <td class="menu">Tekst - informasjon</td>
    <td><input type="text" name="accountplan.Description" value="<? print $account->Description ?>" size="60"></td>
  </tr>
  <tr>
    <td class="menu">Reskontronummer</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableReskontro",$account->EnableReskontro,'') ?>
    <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'ReskontroAccountPlanType', 'value'=>$account->ReskontroAccountPlanType, 'type'=>'AccountPlanType', 'required' => 0)) ?>
  </tr>
  <tr>
    <td class="menu">KID referanse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnablePostPost",$account->EnablePostPost,'') ?></td>

  </tr>
  <tr>
    <td class="menu">Mva kode</td>
    <td> <? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'EnableVAT', 'value'=>$account->EnableVAT)) ?> Kode <? print $_lib['form3']->vat_menu3(array('table'=>$db_table, 'field'=>'VatID', 'value'=>$account->VatID, 'vatid'=>'1')) ?> overstyres: <? $_lib['form2']->checkbox2($db_table, "EnableVATOverride",$account->EnableVATOverride,'') ?></td>
  </tr>
  <tr>
    <td class="menu">Mengde</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableQuantity", $account->EnableQuantity,'') ?></td>

  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?> Default: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V')) ?></td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?> Default: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P')) ?>
  </tr>
  <tr class="result">
    <th colspan="5">Rapporter (som hovedbokskontoen skal brukes i)</th>
  </tr>
  <tr>
    <td class="menu">Pengeflyt</td>
    <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'EnableMoneyFlow', 'value'=>$account->EnableMoneyFlow)) ?> Oppstartsaldo <? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'EnableSaldo', 'value'=>$account->EnableSaldo)) ?>
  </tr>
  <tr>
    <td class="menu">Privatforbruk</td>
    <td><? print $_lib['form3']->Checkbox(array('table'=>$db_table, 'field'=>'EnablePersonalUsage', 'value'=>$account->EnablePersonalUsage)) ?>
    &nbsp; Brukes i l&oslash;nnsutbetaling: <? print $_lib['form3']->Checkbox(array('table'=>$db_table, 'field'=>'EnableSalaryPayment', 'value'=>$account->EnableSalaryPayment)) ?>
  </tr>
  <tr>
    <td class="menu">Offisielt regnskap</td>
    <td><input type="checkbox" checked disabled>
    Linjenummer: <input type="text" name="accountplan.Report1Line"  value="<? print $account->Report1Line  ?>" size="5" class="number"> <? print $map1->getHuman($account->Report1Line) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Selvangivelse for n&aelig;ringsdrivende</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport2', 'value' => $account->EnableReport2, 'disabled' => true)) ?>
    Linjenummer: <input type="text" name="accountplan.Report2Line"  value="<? print $account->Report2Line  ?>" size="5" class="number"> <? print $map2->getHuman($account->Report2Line) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">N&aelig;ringsoppgave 1</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport3', 'value' => $account->EnableReport3, 'disabled' => true)) ?>
    Linjenummer: <input type="text" name="accountplan.Report3Line"  value="<? print $account->Report3Line  ?>" size="5" class="number"> <? print $map3->getHuman($account->Report3Line) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Selvangivelse for aksjeselskap</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport4', 'value' => $account->EnableReport4, 'disabled' => true)) ?>
    Linjenummer: <input type="text" name="accountplan.Report4Line"  value="<? print $account->Report4Line  ?>" size="5" class="number"> <? print $map4->getHuman($account->Report4Line) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">N&aelig;ringsoppgave 2</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport5', 'value' => $account->EnableReport5, 'disabled' => true)) ?>
    Linjenummer: <input type="text" name="accountplan.Report5Line"  value="<? print $account->Report5Line  ?>" size="5" class="number"> <? print $map5->getHuman($account->Report5Line) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Rapport 6</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport6', 'value' => $account->EnableReport6)) ?>
    Linjenummer: <input type="text" name="accountplan.Report6Line"  value="<? print $account->Report6Line  ?>" size="5" class="number">
    </td>
  </tr>
  <tr>
    <td class="menu">Rapport 7</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport7', 'value' => $account->EnableReport7)) ?>
    Linjenummer: <input type="text" name="accountplan.Report7Line"  value="<? print $account->Report7Line  ?>" size="5" class="number">
    </td>
  </tr>
  <tr>
    <td class="menu">Rapport 8</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport8', 'value' => $account->EnableReport8)) ?>
    Linjenummer: <input type="text" name="accountplan.Report8Line"  value="<? print $account->Report8Line  ?>" size="5" class="number">
    </td>
  </tr>
  <tr>
    <td class="menu">Rapport 9</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport9', 'value' => $account->EnableReport9)) ?>
    Linjenummer: <input type="text" name="accountplan.Report9Line"  value="<? print $account->Report9Line  ?>" size="5" class="number">
    </td>
  </tr>
  <tr>
    <td class="menu">Rapport 10</td>
    <td>
    <? print $_lib['form3']->checkbox(array('table' => $db_table, 'field' => 'EnableReport10', 'value' => $account->EnableReport10)) ?>
    Linjenummer: <input type="text" name="accountplan.Report10Line" value="<? print $account->Report10Line ?>" size="5" class="number">
    </td>

  </tr>
  <tr>
    <td class="menu">Kortfattet rapport
    <td><? $_lib['form2']->checkbox2($db_table, "EnableReportShort",$account->EnableReportShort,'') ?>
    Linjenummer: <input type="text" name="accountplan.accountplan.ReportShort" value="<? print "$account->ReportShort"; ?>" size="5" class="number">
    </td>
  </tr>
  <tr>
    <td class="menu">Budsjett resultat</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableBudgetResult",$account->EnableBudgetResult,'') ?></td>

  </tr>
  <tr>
    <td class="menu">Budsjett likviditet
    <td><? $_lib['form2']->checkbox2($db_table, "EnableBudgetLikviditet",$account->EnableBudgetLikviditet,'') ?></td>
  </tr>
  <tr class="result">
    <th colspan="5">Automatiske posteringer</th>
  </tr>
  <tr>
    <td class="menu">Motkonto resultat</td>
    <td>
    <? $_lib['form2']->checkbox2($db_table, "EnableMotkontoResultat", $account->EnableMotkontoResultat,'');
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
    <td class="menu">Motkonto balanse</td>
    <td>
    <? $_lib['form2']->checkbox2($db_table, "EnableMotkontoBalanse", $account->EnableMotkontoBalanse,'');
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
    <td colspan="2"><input type="text" name="accountplan.AccountLineFreeTextMatch" value="<? print $account->AccountLineFreeTextMatch  ?>" size="30"> Brukes av bankavstemming/kontoutskrift importen for &aring; automatisk sette dette kontonummeret p&aring; en transaksjon som har en matchende tekst</td>
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
    <td colspan="2" align="right"><? if($_lib['sess']->get_person('AccessLevel') >= 2) { print $_lib['form3']->submit(array('value'=>'Lagre (S)', 'name'=>'action_accountplan_update', 'accesskey'=>'S')); } ?></td>
    </form>
  </tr>
  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
  <tr>
    <td colspan="2" align="right">
        <form name="delete" action="<? print $_SETUP['DISPATCH'] ?>t=accountplan.list&accountplan_type=hovedbok" method="post">
        <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID', 'value'=>$AccountPlanID)) ?>
        <? print $_lib['form3']->submit(array('value'=>'Deaktiver (D)', 'name'=>'action_accountplan_deactivate', 'accesskey'=>'D')) ?>
        <? if($_lib['sess']->get_person('AccessLevel') > 3) {
            print $_lib['form3']->submit(array('value'=>'Slett (D)', 'name'=>'action_accountplan_delete', 'accesskey'=>'', 'confirm' => 'Er du sikker p&aring; at du vil slette kontoen'));
        } ?>
        </form>
    </td>
  </tr>
  <? } ?>
</table>
<? includeinc('bottom') ?>
</body>
</html>
