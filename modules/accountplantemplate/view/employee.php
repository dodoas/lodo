<?
/* $Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */


$AccountPlanID      = $_lib['input']->getProperty('accountplantemplate_AccountPlanID');
$AccountPlanType    = $_lib['input']->getProperty('accountplantemplate_AccountPlanType');
$JournalID          = $_lib['input']->getProperty('JournalID');
$func               = 'employee';

$db_table = "accountplantemplate";
require_once "record.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where AccountPlanID = " . $AccountPlanID;
$account = $_lib['storage']->get_row(array('query' => $query));

if($account->AccountPlanType)
    $AccountPlanType = $account->AccountPlanType;

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - kontoplan - ansatte</title>
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

print '<h1>' . $_lib['message']->get() . '</h1>'; ?>

<table class="lodo_data">
<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplantemplate.employee" method="post">
<input type="hidden" name="accountplantemplate_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="JournalID" value="<? print $JournalID ?>">
  <tr class="result">
    <th colspan="5">Hovedmal ansatte</th>
  </tr>
  <tr>
    <td class="menu">Aktiv</td>
    <td>
      <? $_lib['form2']->checkbox2($db_table, "Active", $account->Active,'') ?>
    </td>
    <td>
      <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'AccountPlanType', 'value' => $AccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1', 'disabled'=>true)) ?>
    </td>
    <td>Viktig! M&aring; settes riktig for at regnskapet skal fungere</td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Land</td>
    <td></td>
    <td><? print $_lib['form3']->Country_menu3(array('table'=>'accountplantemplate', 'field'=>'CountryCode', 'value'=>$account->CountryCode, 'required'=>false)); ?></td>
    <td colspan="2">&nbsp</td>
  </tr>
  <tr>
    <td class="menu">Tekst informasjon</td>
    <td></td>
    <td colspan="3"><input type="text" name="accountplantemplate.Description" value="<? print $account->Description  ?>" size="70"></td>
  </tr>
  <tr class="result">
    <th colspan="5">Bilagsf&oslash;ringsinformasjon</th>
  </tr>
  <tr>
    <td class="menu">Kreditt tid</td>
    <td>
     <input type="hidden" name="accountplantemplate.EnableCredit" value="1"/>
<? /* $_lib['form2']->checkbox2($db_table, "EnableCredit", $account->EnableCredit,''); */ ?>

</td>
    <td><input class="lodoreqfelt" type="text" name="accountplantemplate.CreditDays" value="<? print $account->CreditDays ?>" size="4" class="number">Dager</td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Default: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P')) ?>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Default: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V')) ?></td>
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

  <tr class="result">
    <th colspan="5">Ansatte</th>
  </tr>
  <tr>
    <td class="menu">Stillingsprosent</td>
    <td></td>
    <td>
      <? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'WorkPercent', 'value'=>$account->WorkPercent, 'class'=>'lodoreqfelt')) ?>
    </td>
  </tr>
  <tr>
    <th colspan="5">Logg</th>
  </tr>
  <tr>
    <td colspan="5" align="right">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" value="Lagre (S)" name="action_accountplan_update" accesskey="S">
    <? } ?>
    </td>
    </form>
  </tr>

  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
  <tr>
    <td colspan="2" align="right">
        <form name="delete" action="<? print $_SETUP['DISPATCH'] ?>t=accountplan.list&accountplan_type=hovedbok" method="post">
        <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID', 'value'=>$AccountPlanID)) ?>
        </form>
    </td>
  </tr>

  <? } ?>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
