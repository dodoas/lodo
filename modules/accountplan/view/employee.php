<?
/* $Id: reskontro.php,v 1.65 2005/11/03 15:33:11 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$AccountPlanID      = $_lib['input']->getProperty('accountplan_AccountPlanID');
$AccountPlanType    = $_lib['input']->getProperty('accountplan_AccountPlanType');
$JournalID          = $_lib['input']->getProperty('JournalID');
$func               = 'employee';
$form_name = 'employee_edit';

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

print '<h1>' . $_lib['message']->get() . '</h1>';

$validation_errors = validate_employee($account);
if(!empty($validation_errors)) {
  print '<div class="warning">';
  foreach ($validation_errors as $error) {
    print $error ."<br>";
  }
  print '</div>';
}

?>

<? require_once 'new.inc'; ?>

<form name="<? print $form_name ?>" action="<? print $_lib['sess']->dispatch ?>t=accountplan.employee" method="post">
<table class="lodo_data">
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
    <td><? print $_lib['form3']->Country_menu3(array('table'=>'accountplan', 'field'=>'CountryCode', 'value'=>$account->CountryCode, 'class'=> 'lodoreqfelt')); ?></td>
    <td colspan="2">&nbsp</td>
  </tr>
  <tr>
    <td class="menu">Telefon</td>
    <td></td>
    <td><input type="text" name="accountplan.Phone" value="<? print $account->Phone  ?>" size="30"></td>
    <td>Mobil</td>
    <td><input type="text" name="accountplan.Mobile" value="<? print $account->Mobile  ?>" size="20"></td>
  </tr>
  <tr>
    <td class="menu">E-Post privat</td>
    <td></td>
    <td><input class="lodoreqfelt" type="text" name="accountplan.Email" value="<? print $account->Email  ?>" size="30"></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">E-Post fakturabank</td>
    <td></td>
    <td><input type="text" name="fakturabankemail.Email" value="<? print $fakturabankemail->Email  ?>" size="30"></td>
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
    <td>Siste l&oslash;nnsendrings dato:</td>
    <td>
      <? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'CreditDaysUpdatedAt', 'form_name' => $form_name, 'value'=>$account->CreditDaysUpdatedAt, 'class'=>'lodoreqfelt')) ?>
    </td>
  </tr>
  <tr>
    <td class="menu">Prosjekt</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableProject",$account->EnableProject,'') ?></td>
    <td>Standard: <? $_lib['form2']->project_menu2(array('table' => $db_table,  'field' =>  'ProjectID',  'value' =>  $account->ProjectID, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'unset' => true)) ?>
  </tr>
  <tr>
    <td class="menu">Avdeling</td>
    <td><? $_lib['form2']->checkbox2($db_table, "EnableDepartment",$account->EnableDepartment,'') ?></td>
    <td>Standard: <? $_lib['form2']->department_menu2(array('table' => $db_table, 'field' => 'DepartmentID', 'value' => $account->DepartmentID, 'tabindex' => $tabindex++, 'accesskey' => 'V', 'unset' => true)) ?></td>
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
    <td colspan="3"><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'BirthDate', 'form_name' => $form_name, 'value'=>$account->BirthDate, 'class'=>'lodoreqfelt')) ?> (Brukes for beregning av arbeidsgiveravgift over/under 62 &aring;r)</td>
  </tr>
  <tr>
    <td class="menu">Personnr</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'SocietyNumber', 'value'=>$account->SocietyNumber, 'class'=>'lodoreqfelt')) ?></td>
    <td>Ansatt ved</td>
    <td><? print $_lib['form3']->Subcompany_menu3(array('table'=>$db_table, 'field'=>'SubcompanyID', 'value'=>$account->SubcompanyID, 'class'=> 'lodoreqfelt')); ?></td>
  </tr>
  <tr>
    <td class="menu">ID nummer</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'IDNumber', 'value'=>$account->IDNumber, 'class'=>'lodoreqfelt')) ?></td>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td class="menu">Kommune</td>
    <td></td>
    <td><? print $_lib['form3']->kommune_menu(array('table'=>$db_table, 'field'=>'KommuneID', 'value'=>$account->KommuneID, 'class'=>'lodoreqfelt'));
        if ($account->KommuneID){
            $q = sprintf("SELECT a.Percent
                FROM kommune AS k
                LEFT JOIN arbeidsgiveravgift AS a ON k.Sone = a.Code
                WHERE KommuneID = %d"
                ,$account->KommuneID);
            $res = $_lib['db']->db_fetch_object($_lib['db']->db_query($q));
            print("AGA " . $res->Percent . "%");
          }
      ?></td>
    <td></td>
    <td></td>
  </tr>
  <tr>
    <td class="menu">Feriepengeprosent</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'Feriepengeprosent', 'value'=>$account->Feriepengeprosent, 'class'=>'lodoreqfelt')) ?></td>
    <td colspan="2"></td>
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
    <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'WorkStart', 'form_name' => $form_name, 'value'=>$account->WorkStart, 'class'=>'lodoreqfelt')) ?></td>
    <td>Arbeid slutt</td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'WorkStop',  'form_name' => $form_name, 'value'=>$account->WorkStop, 'class'=>'lodoreqfelt')) ?></td>
  </tr>
  <tr>
    <td class="menu">Stillingsprosent</td>
    <td></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'WorkPercent', 'value'=>$account->WorkPercent, 'class'=>'lodoreqfelt')) ?></td>
    <td>
      Stillingsprosent oppdatert:
      <? print $_lib['form3']->date(array('table'=>$db_table, 'field'=>'WorkPercentUpdatedAt', 'form_name' => $form_name, 'value'=>$account->WorkPercentUpdatedAt, 'class'=>'lodoreqfelt')) ?>
    </td>
    <td><?

      if($_lib['db']->db_numrows($salary_conf) != 0) {
        $salary_conf_row = $_lib['db']->db_fetch_assoc($salary_conf);
        printf( "L&oslash;nnsmal %d", $salary_conf_row['SalaryConfID']);
      }

    ?></td>
  </tr>
  <tr class="result">
    <th colspan="5">Altinn felt</th>
  </tr>
  <tr>
    <td class="menu">Skifttype:</td>
    <td></td>
    <td>
      <? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['ShiftTypes'], 'width'=>100, 'table'=> 'accountplan', 'field'=>'ShiftType', 'value'=>$account->ShiftType, 'class'=> 'lodoreqfelt')); ?>
    </td>
    <td>Timer hver uke ved full stilling:</td>
    <td>
      <input class="lodoreqfelt" type="text" name="accountplan.Workmeasurement" value="<?= $account->Workmeasurement ?>" />
    </td>
  </tr>
  <tr>
    <td class='menu'>Arbeidstid:</td>
    <td></td>
    <td>
      <? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['WorkTimeSchemeTypes'], 'table'=> 'accountplan', 'field'=>'WorkTimeScheme', 'value'=>$account->WorkTimeScheme, 'class'=> 'lodoreqfelt')); ?>
    </td>

    <td>Ansettelsestype:</td>
    <td>
      <? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['TypeOfEmploymentTypes'], 'table'=> 'accountplan', 'field'=>'TypeOfEmployment', 'value'=>$account->TypeOfEmployment, 'width' => 32, 'class'=> 'lodoreqfelt')); ?>
    </td>
  </tr>
  <tr>
    <td class='menu'>Yrke</td>
    <td></td>
    <td>
      <? print $_lib['form3']->Occupation_menu3(array('table'=>$db_table, 'field'=>'OccupationID', 'value'=>$account->OccupationID, 'class'=> 'lodoreqfelt')); ?>
    </td>
    <td>Samme posisjon siden:</td>
    <td>
      <? print $_lib['form3']->date(array('table'=>'accountplan', 'field'=>'inCurrentPositionSince', 'form_name' => $form_name, 'value'=>$account->inCurrentPositionSince, 'class'=> 'lodoreqfelt')) ?>
    </td>
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
  </tr>
  <? include("schemeid.php") ?>
  </form>


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
<br/><br/>

<form name="work_relations" action="<? print $_lib['sess']->dispatch ?>t=accountplan.employee" method="post">
<table class="lodo_data">
  <tr class="result">
    <th colspan="14">Arbeidsforhold</th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Ansatt ved</td>
    <td class="menu">Arbeid start</td>
    <td class="menu">Arbeid slutt</td>
    <td class="menu">Yrke</td>
    <td class="menu">Samme posisjon siden</td>
    <td class="menu">Arbeidstid</td>
    <td class="menu">Skifttype</td>
    <td class="menu">Ansettelsestype</td>
    <td class="menu">Stillingsprosent</td>
    <td class="menu">Stillingsprosent oppdatert</td>
    <td class="menu">Timer hver uke ved full stilling</td>
    <td class="menu">Siste l&oslash;nnsendrings dato</td>
    <td class="menu"></td>
  </tr>
<?
$db_table2 = 'workrelation';
$query_work_relations = "SELECT * FROM workrelation WHERE AccountPlanID = $AccountPlanID order by WorkRelationID";
$result_work_relations = $_lib['db']->db_query($query_work_relations);
$work_relations_array = array();
while($work_relation = $_lib['db']->db_fetch_object($result_work_relations)) {
  $work_relations_array[] = $work_relation;
}

$validation_errors = work_relation::validate_work_relations($work_relations_array);
if(!empty($validation_errors)) {
  print '<div class="warning">';
  foreach ($validation_errors as $error) {
    print $error ."<br>";
  }
  print '</div><br>';
}

foreach ($work_relations_array as $work_relation) {
  $WorkRelationID = $work_relation->WorkRelationID;
?>
  <tr>
    <td><? print $work_relation->WorkRelationID; ?></td>
    <td><? print $_lib['form3']->Subcompany_menu3(array('table'=>$db_table2, 'field'=>'SubcompanyID', 'value'=>$work_relation->SubcompanyID, 'pk'=> $WorkRelationID, 'class'=> 'lodoreqfelt')); ?></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table2, 'field'=>'WorkStart', 'form_name' => 'work_relations', 'value'=>$work_relation->WorkStart, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table2, 'field'=>'WorkStop', 'form_name' => 'work_relations', 'value'=>$work_relation->WorkStop, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->Occupation_menu3(array('table'=>$db_table2, 'field'=>'OccupationID', 'value'=>$work_relation->OccupationID, 'pk'=> $WorkRelationID, 'class'=> 'lodoreqfelt')); ?></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table2, 'field'=>'InCurrentPositionSince', 'form_name' => 'work_relations', 'value'=>$work_relation->InCurrentPositionSince, 'pk'=> $WorkRelationID, 'class'=> 'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['WorkTimeSchemeTypes'], 'table'=> $db_table2, 'field'=>'WorkTimeScheme', 'value'=>$work_relation->WorkTimeScheme, 'pk'=> $WorkRelationID, 'class'=> 'lodoreqfelt')); ?></td>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['ShiftTypes'], 'width'=>100, 'table'=> $db_table2, 'field'=>'ShiftType', 'value'=>$work_relation->ShiftType, 'pk'=> $WorkRelationID, 'class'=> 'lodoreqfelt')); ?></td>
    <td><? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['TypeOfEmploymentTypes'], 'table'=> $db_table2, 'field'=>'TypeOfEmployment', 'value'=>$work_relation->TypeOfEmployment, 'pk'=> $WorkRelationID, 'width' => 32, 'class'=> 'lodoreqfelt')); ?></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'WorkPercent', 'value'=>$work_relation->WorkPercent, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table2, 'field'=>'WorkPercentUpdatedAt', 'form_name' => 'work_relations', 'value'=>$work_relation->WorkPercentUpdatedAt, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'WorkMeasurement', 'value'=>$work_relation->WorkMeasurement, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td><? print $_lib['form3']->date(array('table'=>$db_table2, 'field'=>'SalaryDateChangedAt', 'form_name' => 'work_relations', 'value'=>$work_relation->SalaryDateChangedAt, 'pk'=> $WorkRelationID, 'class'=>'lodoreqfelt')) ?></td>
    <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
      <input type="checkbox" name="work_relations_to_delete[]" value="<? print $work_relation->WorkRelationID; ?>" /></td>
      <? } ?>
    </tr>
  <?
    $db_table3 = 'workrelationfurlough';
    $query_work_relation_furloghs = "SELECT * FROM $db_table3 WHERE WorkRelationID = $work_relation->WorkRelationID ORDER BY FurloughID";
    $result_work_relation_furloughs = $_lib['db']->db_query($query_work_relation_furloghs);
    if ($_lib['db']->db_numrows($result_work_relation_furloughs)) {
  ?>
    <tr>
      <td></td>
      <td></td>
      <td class="menu">ID</td>
      <td class="menu">tekst</td>
      <td class="menu">Start dato</td>
      <td class="menu">Slutt dato</td>
      <td class="menu">Prosent</td>
      <td class="menu">Type</td>
    </tr>
    <?
      $furloughs_array = array();
      while($furlough = $_lib['db']->db_fetch_object($result_work_relation_furloughs)) {
        $furloughs_array[] = $furlough;
    ?>
      <tr>
        <td></td>
        <td></td>
        <td>
          <? echo $furlough->FurloughID ?>
        </td>
        <td>
          <? print $_lib['form3']->Generic_menu3(array(
            'query' => 'select Text from furloughtext',
            'table'=>$db_table3,
            'field'=>'Text',
            'value'=>$furlough->Text,
            'pk'=> $furlough->FurloughID,
            'class'=>'lodoreqfelt')) ?>
        </td>

        <td><? print $_lib['form3']->date(array(
          'table'=>$db_table3,
          'field'=>'Start',
          'form_name' => 'work_relations',
          'value'=>$furlough->Start,
          'pk'=> $furlough->FurloughID,
          'class'=>'lodoreqfelt')) ?></td>
        <td><? print $_lib['form3']->date(array(
          'table'=>$db_table3,
          'field'=>'Stop',
          'form_name' => 'work_relations',
          'value'=>$furlough->Stop,
          'pk'=> $furlough->FurloughID,
          'class'=>'lodoreqfelt')) ?></td>

        <td><? print $_lib['form3']->text(array(
          'table'=>$db_table3,
          'field'=>'Percent',
          'value'=>$furlough->Percent,
          'pk'=> $furlough->FurloughID,
          'class'=>'lodoreqfelt')) ?></td>

        <td>
          <? print $_lib['form3']->Generic_menu3(array(
            'data' => $_lib['form3']->_ALTINN['PermisjonsOgPermitteringsBeskrivelse'],
            'table'=>$db_table3,
            'field'=>'Description',
            'value'=>$furlough->Description,
            'pk'=> $furlough->FurloughID,
            'class'=>'lodoreqfelt')) ?>
        </td>

      </tr>

    <?
      }
      $validation_errors = work_relation::validate_furloughs($furloughs_array);
      if(!empty($validation_errors)) {
    ?>
      <tr>
        <td colspan="2"></td>
        <td colspan="6">
          <div class="warning">
    <?
        foreach ($validation_errors as $error) {
          print $error ."<br>";
        }
    ?>
          </div>
        </td>
      </tr>
  <?
      }
  ?>

  <tr>
    <td colspan="2"></td>
    <td class="menu" colspan="6"></td>
  </tr>
  <?
    }
}
if($_lib['sess']->get_person('AccessLevel') >= 2) {
?>
  <tr>
    <? print $_lib['form3']->hidden(array('name'=>'accountplan_AccountPlanID', 'value'=>$AccountPlanID)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'accountplan_AccountPlanType', 'value'=>$AccountPlanType)) ?>
    <td colspan="12" align='left'>
      <input type="submit" value="Lagre arbeidsforhold" name="action_work_relation_save">
      <input type="submit" value="Legg til arbeidsforhold" name="action_work_relation_add">
      <input type="submit" value="Legg til permisjon p&aring; markerte" name="action_work_relation_furlough_add">
    </td>
    <td colspan="2" align="right">
      <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
      <input type="submit" name="action_work_relation_delete" value="Slett markerte" onclick="return confirm('Er du sikker p&aring; at du vil slette markerte?');" />
      <? } ?>
    </td>
  </tr>
<?
}
?>
</table>
<a href="<? print $_lib['sess']->dispatch ?>t=furlough.textlist">Legg til ny permisjon og permittering tekst</a>
</form>
<? includeinc('bottom') ?>
</body>
</html>
