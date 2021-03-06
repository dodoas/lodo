<?
# $Id: edit.php,v 1.39 2005/10/28 17:59:40 thomasek Exp $ person_edit.php,v 1.13 2001/12/02 09:19:50 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no, http://www.ekdahl.no

$PersonID  = $_REQUEST['PersonID'];
assert(!is_int($PersonID)); #All main input should be int

$CompanyID = $_REQUEST['CompanyID'];
assert(!is_int($CompanyID)); #All main input should be int

$db_table = "person";

if(!$PersonID){
  $PersonID = $login_id;
}

require_once  "record.inc";

$query = "select * from $db_table where PersonID='$PersonID'";
$person = $_lib['storage']->get_row(array('query' => $query));
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Ansatt</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.39 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head'); ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left'); ?>
<? $tabindex = 1; ?>

<table cellspacing="0">
<form name="person" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="PersonID" value="<? print $PersonID ?>">
<input type="hidden" name="CompanyID" value="<? print $CompanyID ?>">
<thead>
    <tr>
      <th colspan="4">Info ansattnummer <? print $person->PersonID ?>
    </tr>
</thead>

<tbody>
    <tr>
        <td class="BGColorDark">Personnummer</td>
        <td><input type="text" name="<? print $db_table ?>.SocialSecurityID" value="<? print $person->SocialSecurityID ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
        <td>Passord</td>
        <td>
        <? if($_lib['sess']->get_person('AccessLevel') == 4 or $_lib['sess']->login_id==$person->PersonID) { ?>
          <a href="<? print $_lib['sess']->dispatch ?>&t=person.editpassword&PersonID=<? print $PersonID ?>&CompanyID=<? print $CompanyID ?>">Endre</a></td>
        <? } else { ?>
          Du kan ikke endre passordet til denne brukeren.
        <? } ?>
    </tr>
    <tr>
        <td class="BGColorDark">Etternavn</td>
        <td><input type="text" name="<? print $db_table ?>.LastName" value="<? print $person->LastName ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Fornavn</td>
        <td><input type="text" name="<? print $db_table ?>.FirstName" value="<? print $person->FirstName ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
    </tr><tr>
        <td class="BGColorDark">Adresse</td>
        <td> <input type="text" name="<? print $db_table ?>.Address" value="<? print $person->Address ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Land</td>
        <td><? print $_lib['form3']->Country_menu3(array('table'=>$db_table, 'field'=>'CountryCode', 'value'=>$person->CountryCode, 'required'=>false, 'tabindex'=>$tabindex++)); ?></td>
    </tr>
    <tr>
        <td class="BGColorDark">Postnummer</td>
        <! forandret fra PostalCode til ZipCode 6/1-2005 >
        <td><input type="text" name="<? print $db_table ?>.ZipCode" value="<? print $person->ZipCode ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Poststed</td>
        <td><input type="text" name="<? print $db_table ?>.City" value="<? print $person->City ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Hjemmetelefon</td>
        <td><input type="text" name="<? print $db_table ?>.HomePhone" value="<? print $person->HomePhone ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Direktenummer</td>
        <td><input type="text" name="<? print $db_table ?>.DirectPhone" value="<? print $person->DirectPhone ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Mobil</td>
        <td><input type="text" name="<? print $db_table ?>.MobilePhoneNumber" value="<? print $person->MobilePhoneNumber ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
        <td class="BGColorDark">Internt nummer</td>
        <td><input type="text" name="<? print $db_table ?>.InternalPhoneNumber" value="<? print $person->InternalPhoneNumber ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
    </tr>
    <tr>
        <td class="BGColorDark">E-Post</td>
        <td><input type="text" name="<? print $db_table ?>.Email" value="<? print $person->Email ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Telefax</td>
        <td><input type="text" name="<? print $db_table ?>.Fax" value="<? print $person->Fax ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Bank kontonr</td>
        <td><input type="text" name="<? print $db_table ?>.BankAccount" value="<? print $person->BankAccount ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Initialer</td>
        <td><input type="text" name="<? print $db_table ?>.Extension" value="<? print $person->Extension ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Tittel</td>
        <td><input type="text" name="<? print $db_table ?>.TitleOfCourtesy" value="<? print $person->TitleOfCourtesy ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Bursdag</td>
        <td><input type="text" name="<? print $db_table ?>.BirthDate" value="<? print $person->BirthDate ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="24"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Ansattdato</td>
        <td><input type="text" name="<? print $db_table ?>.HireDate" value="<? print $person->HireDate ?>"size="24" tabindex="<? echo $tabindex++; ?>" maxlength="24"></td>
        <td  class="BGColorDark">Opprettet dato</td>
        <td><? print $person->DateCreated ?></td>
    </tr>
    <tr>
        <td class="BGColorDark">Eksternt ansattnummer</td>
        <td><input type="text" name="<? print $db_table ?>.ExternalID" value="<? print $person->ExternalID ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
        <td class="BGColorDark">Company department</td>
        <td><input type="text" name="<? print $db_table ?>.Department" value="<? print $person->Department ?>" size="24" tabindex="<? echo $tabindex++; ?>" maxlength="50"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Aktiv</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'Active', 'value'=>$person->Active, 'tabindex'=>$tabindex++)) ?></td>
        <td class="BGColorDark">Avdeling<br /></td>
        <td><? print $_lib['form3']->Avd_menu3(array('table'=>$db_table, 'field'=>'DepartmentID', 'value'=>$person->DepartmentID, 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
        <td class="BGColorDark">Tilgangsniv&aring;</td>
        <td>
        <?
        if($_lib['sess']->get_person('AccessLevel') == 4) {
          $_lib['form2']->Type_menu2(array('type'=>'AccessLevel', 'table'=>$db_table, 'field'=>'AccessLevel', 'value'=>$person->AccessLevel, 'tabindex'=>$tabindex++));
        } else {
          print "Du har ikke lov til &aring; endre tilgang p� denne brukeren";
        }
        ?></td>
        <td class="BGColorDark">Klassifikasjon</td>
        <td><? $_lib['form2']->Type_menu2(array('type'=>'ClassificationID', 'table'=>$db_table, 'field'=>'ClassificationID', value=>$person->ClassificationID, 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
        <th colspan="4" class="menu">Aksjer</th>
    </tr>
    <tr>
        <td class="BGColorDark">Antall aksjer</td>
        <td><input type="text" name="<? print $db_table ?>.ShareNumber" value="<? print $person->ShareNumber ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
        <td class="BGColorDark">Aksje verdi</td>
        <td><input type="text" name="<? print $db_table ?>.ShareValue" value="<? print $person->ShareValue ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
    </tr>
    <tr>
        <td class="BGColorDark">Likningsverdi</td>
        <td><input type="text" name="<? print $db_table ?>.StockAssessmentValue" value="<? print $person->StockAssessmentValue ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
        <td class="BGColorDark">Utbytte</td>
        <td><input type="text" name="<? print $db_table ?>.StockProfit" value="<? print $person->StockProfit ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
    </tr>
    <tr>
      <th colspan="4" class="menu">Altinn A-ordningen tilgang</th>
    </tr>
    <tr>
        <td class="BGColorDark">Personnummer</td>
        <td><input type="text" name="<? print $db_table ?>.AltinnUserSSN" value="<? print $person->AltinnUserSSN ?>" size="24" tabindex="<? echo $tabindex++; ?>"></td>
        <td class="BGColorDark">Autentifikasjon metode</td>
        <td>
          <? print $_lib['form3']->Generic_menu3(array('data' => $_lib['form3']->_ALTINN['AuthMethods'],'table'=> $db_table, 'field'=>'AltinnAuthMethod', 'value' => $person->AltinnAuthMethod, 'required' => true, 'tabindex' => $tabindex++
          )); ?>

        </td>
    <? if($_lib['sess']->get_person('AccessLevel') > 3) { ?>
    <tr>
        <td class="BGColorDark">Tilgang til Altinn L&oslash;nnslipper</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'AltinnSalaryAccess', 'value'=>$person->AltinnSalaryAccess, 'tabindex'=>$tabindex++)) ?></td>
        <td colspan='2' class="BGColorDark"></td>
    </tr>
    <? } ?>
    </tr>
    <tr>
        <th colspan="4" class="menu">Fakturabank tilgang</th>
    </tr>
        <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
    <tr>
        <td class="BGColorDark">Tilgang til import av faktura</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'FakturabankImportInvoiceAccess', 'value'=>$person->FakturabankImportInvoiceAccess, 'tabindex'=>$tabindex++)) ?></td>
        <td class="BGColorDark">Tilgang til eksport av faktura</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'FakturabankExportInvoiceAccess', 'value'=>$person->FakturabankExportInvoiceAccess, 'tabindex'=>$tabindex++)) ?></td>
    </tr>
    <tr>
        <td class="BGColorDark">Tilgang til import av banktransaksjoner</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'FakturabankImportBankTransactionAccess', 'value'=>$person->FakturabankImportBankTransactionAccess, 'tabindex'=>$tabindex++)) ?></td>
         <td class="BGColorDark">Tilgang til eksport av l&oslash;nnslipp</td>
        <td><? print $_lib['form3']->checkbox(array('table'=>$db_table, 'field'=>'FakturabankExportPaycheckAccess', 'value'=>$person->FakturabankExportPaycheckAccess, 'tabindex'=>$tabindex++)) ?></td>
    </tr>
         <? } ?>
    <tr>
        <td align="left" valign="top" class="BGColorDark">Info</td>
        <td colspan="3"><textarea name="<? print $db_table ?>.Notes" cols="80" rows="6" tabindex="<? echo $tabindex++; ?>"><? print $person->Notes ?></textarea></td>
    </tr>
</tbody>

<tfoot>
    <tr>
      <td class="BGColorDark"></td>
      <td class="BGColorDark"><br /></td>
      <td class="BGColorDark"></td>
      <td class="BGColorDark" align="right">
      <?
      if($_lib['sess']->get_person('AccessLevel') >= 3 or $_lib['sess']->login_id==$person->PersonID)
      {
          ?>
          <input type="submit" value="Lagre (S)" name="action_person_update" tabindex="<? echo $tabindex++; ?>" accesskey="S">
          <?
      }
      ?>
      </td>
    </tr>
</tfoot>
</table>
</form>
<? includeinc('bottom'); ?>
</body>
</html>
