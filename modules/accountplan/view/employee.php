<?
/* $Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$AccountPlanID      = $_lib['input']->getProperty('accountplan_AccountPlanID');
$AccountPlanType    = $_lib['input']->getProperty('accountplan_AccountPlanType');
$JournalID          = $_lib['input']->getProperty('JournalID');
$func               = 'employee';

$db_table = "accountplan";
require_once "record.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table where AccountPlanID = " . $AccountPlanID;
$account = $_lib['storage']->get_row(array('query' => $query));

$password_query = "SELECT * FROM timesheetpasswords WHERE AccountPlanID = " .
					$AccountPlanID;
$password = $_lib['db']->get_row(array('query' => $password_query));


if($account->AccountPlanType)
    $AccountPlanType = $account->AccountPlanType;

$fakturabankemail_query = "select * from fakturabankemail where AccountPlanID = " . $AccountPlanID;
$fakturabankemail = $_lib['storage']->get_row(array('query' => $fakturabankemail_query));

$salary_conf_query = "SELECT SalaryConfID FROM salaryconf WHERE AccountPlanID = $AccountPlanID";
$salary_conf = $_lib['db']->db_query($salary_conf_query);

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

if($JournalID)
{
  ?><a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&JournalID=<? print "$JournalID"; ?>">Tilbake til bilag <? print $JournalID ?></a><?
}

print '<h1>' . $_lib['message']->get() . '</h1>'; ?>

<? require_once 'new.inc'; ?>

<table class="lodo_data">
<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplan.employee" method="post">
<input type="hidden" name="accountplan_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="fakturabankemail_AccountPlanID" value="<? print $AccountPlanID ?>">
<input type="hidden" name="JournalID" value="<? print $JournalID ?>">
  <tr class="result">
    <th colspan="5">Ansatt <? print $AccountPlanID ?> (underkonto til hovedbok)</th>
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
    <td class="menu">Navn</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.AccountName" value="<? print $account->AccountName  ?>" size="20"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
                             <td class="menu">Fornavn (Kun for personer)</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.FirstName" value="<? print $account->FirstName  ?>" size="20"></td>
                             <td>Etternavn (Kun for personer)</td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.LastName" value="<? print $account->LastName  ?>" size="20"></td>
  </tr>
  <tr>
    <td class="menu">Adresse</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoiceAddress", $account->EnableInvoiceAddress,'') ?></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Address" value="<? print $account->Address  ?>" size="20"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Postnummer</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.ZipCode" value="<? print $account->ZipCode  ?>" size="4"></td><td>Poststed</td><td><input class="lodoreqfelt" type="text" name="accountplan.City" value="<? print $account->City  ?>" size="20">
    </td>
  </tr>
  <tr>
    <td class="menu">Postboks</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableInvoicePoBox", $account->EnableInvoicePoBox,'') ?></td>
    <td><input type="text" name="accountplan.IPoBox" value="<? print $account->IPoBox  ?>" size="4"></td>
    <td>Postbokssted</td>
    <td><input type="text" name="accountplan.IPoBoxCity" value="<? print $account->IPoBoxCity  ?>" size="20"></td>
  </tr>
  <tr>
    <td class="menu">Postbokspostnummer</td>
    <td></td>
    <td><input type="text" name="accountplan.IPoBoxZipCode" value="<? print $account->IPoBoxZipCode  ?>" size="4"></td>
    <td>Postbokspostnummersted</td>
    <td><input type="text" name="accountplan.IPoBoxZipCodeCity" value="<? print $account->IPoBoxZipCodeCity  ?>" size="20"></td>
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
    <td><input type="text" name="accountplan.Phone" value="<? print $account->Phone  ?>" size="20"></td>
    <td>Mobil</td>
    <td><input type="text" name="accountplan.Mobile" value="<? print $account->Mobile  ?>" size="20"></td>
  </tr>
  <tr>
    <td class="menu">E-Post privat</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Email" value="<? print $account->Email  ?>" size="20"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">E-Post fakturabank</td>
    <td></td>
    <td><input type="text" name="fakturabankemail.Email" value="<? print $fakturabankemail->Email  ?>" size="20"></td>
    <td></td>
    <td></td>    
  </tr>
  <tr>
    <td class="menu">Tekst informasjon</td>
    <td></td>
    <td colspan="3"><input type="text" name="accountplan.Description" value="<? print $account->Description  ?>" size="70"></td>
  </tr>
  <tr class="result">
    <th colspan="5">Bilagsf&oslash;ringsinformasjon</th>
  </tr>
  <tr>
    <td class="menu">Bank kontonummer</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.DomesticBankAccount" value="<? print $account->DomesticBankAccount  ?>" size="20"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Kreditt tid</td>
    <td>
     <input type="hidden" name="accountplan.EnableCredit" value="1"/>
<? /* $_lib['form2']->checkbox2($db_table, "EnableCredit", $account->EnableCredit,''); */ ?> 

</td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.CreditDays" value="<? print $account->CreditDays ?>" size="4" class="number">Dager</td>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Default: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V')) ?></td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Default: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P')) ?>
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

  <tr class="result">
    <th colspan="5">Ansatte</th>
  </tr>
  <tr>
    <td class="menu">F&oslash;dselsdag</td>
    <td></td>
    <td colspan="3"><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'BirthDate', 'value'=>$account->BirthDate, 'class'=>'lodoreqfelt')) ?> (Brukes for beregning av arbeidsgiveravgift over/under 62 &aring;r)</td>
  </tr>
  <tr>
    <td class="menu">Personnr</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'SocietyNumber', 'value'=>$account->SocietyNumber, 'class'=>'lodoreqfelt')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Kommune</td>
    <td></td>
    <td><? print $_lib['form3']->kommune_menu(array('table'=>$db_table, 'field'=>'KommuneID', 'value'=>$account->KommuneID, 'class'=>'lodoreqfelt')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Tabelltrekk:</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'TabellTrekk', 'value'=>$account->TabellTrekk, 'class'=>'lodoreqfelt')) ?></td>
    <td>Prosenttrekk:</td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'ProsentTrekk', 'value'=>$account->ProsentTrekk, 'class'=>'lodoreqfelt')) ?></td>
    </td>
  </tr>
  <tr>
    <td class="menu">Arbeid start</td>
    <td></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'WorkStart', 'value'=>$account->WorkStart, 'class'=>'lodoreqfelt')) ?></td>
    <td>Arbeid slutt</td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'WorkStop', 'value'=>$account->WorkStop, 'class'=>'lodoreqfelt')) ?></td>
  </tr>
  <tr>
    <td class="menu">Stillingsprosent</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'WorkPercent', 'value'=>$account->WorkPercent, 'class'=>'lodoreqfelt')) ?></td>
    <td></td>
    <td><? 
  
      if($_lib['db']->db_numrows($salary_conf) != 0) {
        $salary_conf_row = $_lib['db']->db_fetch_assoc($salary_conf);
        printf( "L&oslash;nnsmal %d", $salary_conf_row['SalaryConfID']);
      }

    ?></td>
  </tr>

  <tr class="result">
    <th colspan="5">Timeliste</th>
  </tr>
  <tr>
    <td class="menu">Passord</td> <td></td>
    <td><input type="text" name="timesheetpasswords.Password" value="<?= $password->Password ?>" /></td>
    <td>Ansatt logger inn med:</td>

  </tr>
  <tr>
    <td class="menu">Oversikt</td> <td></td>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=timesheets.list&AccountPlanID=<?= $AccountPlanID ?>&Username=<?= $account->AccountName ?>">Vis oversikt</a></td>
    <td>Database: <b><?= $_SESSION['DB_NAME'] ?></b><br />
        Brukernavn: <b><?= $AccountPlanID ?></b> eller <b><?= $account->AccountName ?></b><br />
        Passord: <b><?= $password->Password ?></b></td>

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
