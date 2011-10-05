<?
# $Id: edit.php,v 1.46 2005/05/26 13:54:57 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "company";
$db_table2 = "person";

$CompanyID = $_REQUEST['CompanyID'];
assert(!is_int($CompanyID)); #All main input should be int

require_once  "record.inc";



/* check if company exist, create one if not */
$query_comp = "select * from company where CompanyID=$CompanyID";
$row_comp = $_lib['storage']->get_row(array('query' => $query_comp));

if(!isset($row_comp->CompanyID))
{
    #create empty company
    $query_comp_new = "insert into company (CompanyID, CreatedDate) values (1, now())";
    $comanyID = $_dbh[$_dsn]->db_insert($query_comp_new);
}
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.46 2005/05/26 13:54:57 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?> <div class="warning"><? print $_lib['message']->get() ?></div><br><? } ?>

<h2>Firmaopplysninger, <? print $row_comp->VName; ?> (side 1 av 3)</h2>
<table class="tab">
  <tr>
  <td><div class="active_tab">Adresser og kontaktinformasjon</div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.accounting&CompanyID=<? print "$CompanyID" ?>">Regnskapsinformasjon</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.misc&CompanyID=<? print "$CompanyID" ?>">Diverse</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=borettslag.borettslag&CompanyID=<? print "$CompanyID";
?>">Borettslag</a></div>
</table>
<table cellspacing="0" class="lodo_data">
<thead>
  <form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="CompanyID" value="<? print $CompanyID; ?>">
</thead>

<tbody>

  <tr>
  <th colspan="4" class="menu">Bes&oslash;ksadresse</th>

  <tr>
    <td class="BGColorDark">Navn</td>
    <td class="BGColorLight" colspan="3"><input type="text" name="company.CompanyName"    value="<? print $row_comp->CompanyName; ?>" size="70"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Adresse</td>
    <td class="BGColorLight"><input type="text" name="company.VAddress" value="<? print $row_comp->VAddress; ?>" size="24"></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VZipCode" value="<? print $row_comp->VZipCode; ?>" size="24"></td>
    <td class="BGColorDark">Sted</td>
    <td class="BGColorLight"><input type="text" name="company.VCity"    value="<? print $row_comp->VCity; ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postboks</td>
    <td class="BGColorLight"><input type="text" name="company.VPoBox" value="<? print $row_comp->VPoBox; ?>" size="24"></td>
    <td class="BGColorDark">Postbokssted</td>
    <td class="BGColorLight"><input type="text" name="company.VPoBoxCity" value="<? print $row_comp->VPoBoxCity; ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postbokspostnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VPoBoxZipCode" value="<? print $row_comp->VPoBoxZipCode; ?>" size="24"></td>
    <td class="BGColorDark">Postbokspostnummersted</td>
    <td class="BGColorLight"><input type="text" name="company.VPoBoxZipCodeCity" value="<? print $row_comp->VPoBoxZipCodeCity; ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Land</td>
    <td class="BGColorLight"><? print $_lib['form3']->Country_menu3(array('table'=>'company', 'field'=>'VCountryCode', 'value'=>$row_comp->VCountryCode, 'required'=>false)); ?></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Organisasjons nr</td>
    <td class="BGColorLight"><input type="text" name="company.OrgNumber" value="<? print $row_comp->OrgNumber ?>" size="24"></td>
    <td class="BGColorDark">MVA nr</td>
    <td class="BGColorLight"><input type="text" name="company.VatNumber" value="<? print $row_comp->VatNumber ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Kontorkommune nr</td>
    <td class="BGColorLight"><input type="text" name="company.CompanyMunicipality" value="<? print $row_comp->CompanyMunicipality ?>" size="24"></td>
    <td class="BGColorDark">Kontorkommune navn</td>
    <td class="BGColorLight"><input type="text" name="company.CompanyMunicipalityName" value="<? print $row_comp->CompanyMunicipalityName ?>" size="24"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Fakturaadresse</th>
  </tr>
  <tr>
    <td class="BGColorDark">Navn</td>
    <td class="BGColorLight" colspan="3"><input type="text" name="company.IName"    value="<? print $row_comp->IName ?>" size="70"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Adresse</td>
    <td class="BGColorLight"><input type="text" name="company.IAddress" value="<? print $row_comp->IAddress ?>" size="24"></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postnummer/Postboks</td>
    <td class="BGColorLight"><input type="text" name="company.IZipCode" value="<? print $row_comp->IZipCode ?>" size="24"></td>
    <td class="BGColorDark">Sted/Poststed</td>
    <td class="BGColorLight"><input type="text" name="company.ICity"    value="<? print $row_comp->ICity ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Land</td>
    <td class="BGColorLight"><? print $_lib['form3']->Country_menu3(array('table'=>'company', 'field'=>'ICountryCode', 'value'=>$row_comp->ICountryCode, 'required'=>'1')); ?></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Vis omsetning dette &aring;r p&aring; faktura</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2('company', 'ShowInvoiceAmountThisYear', $row_comp->ShowInvoiceAmountThisYear,'') ?></td>
    <td class="BGColorDark">Plassering av fakturakommentar</td>
    <td class="BGColorLight"><? print $_lib['form3']->Type_menu3(array('table'=>'company', 'field'=>'InvoiceCommentCustomerPosition', 'value'=>$row_comp->InvoiceCommentCustomerPosition, 'type'=>'InvoiceCommentCustomerPosition', 'required'=>'1')) ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">Plassering av linjekommentar</td>
    <td class="BGColorLight"><? print $_lib['form3']->Type_menu3(array('table'=>'company', 'field'=>'InvoiceLineCommentPosition', 'value'=>$row_comp->InvoiceLineCommentPosition, 'type'=>'InvoiceCommentCustomerPosition', 'required'=>'1')) ?></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Leveringsadresse</th>
  </tr>
  <tr>
    <td class="BGColorDark">Navn</td>
    <td class="BGColorLight" colspan="3"><input type="text" name="company.DName"    value="<? print "$row_comp->DName"; ?>" size="70</td>">
  </tr>
  <tr>
    <td class="BGColorDark">Adresse</td>
    <td class="BGColorLight"><input type="text" name="company.DAddress" value="<? print "$row_comp->DAddress"; ?>" size="24"></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postnummer</td>
    <td class="BGColorLight"><input type="text" name="company.DZipCode" value="<? print "$row_comp->DZipCode"; ?>" size="24"></td>
    <td class="BGColorDark">Sted</td>
    <td class="BGColorLight"><input type="text" name="company.DCity"    value="<? print "$row_comp->DCity"; ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Land</td>
    <td class="BGColorLight"><? print $_lib['form3']->Country_menu3(array('table'=>'company', 'field'=>'DCountryCode', 'value'=>$row_comp->DCountryCode, 'required'=>false)); ?></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Kontaktinformasjon</th>
  </tr>
  <tr>
    <td class="BGColorDark">Telefon</td>
    <td class="BGColorLight"><input type="text" name="company.Phone"    value="<? print $row_comp->Phone; ?>" size="24"></td>
    <td class="BGColorDark">Fax</td>
    <td class="BGColorLight"><input type="text" name="company.Fax"  value="<? print $row_comp->Fax; ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Mobil</td>
    <td class="BGColorLight"><input type="text" name="company.Mobile"    value="<? print $row_comp->Mobile; ?>" size="24"></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <td class="BGColorDark">E-Post</td>
    <td class="BGColorLight"><input type="text" name="company.Email"    value="<? print $row_comp->Email; ?>" size="24"></td>
    <td class="BGColorDark">WWW</td>
    <td class="BGColorLight"><input type="text" name="company.WWW"  value="<? print $row_comp->WWW; ?>" size="24"></td>
  </tr>
</tbody>

<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4">
    <?
    if($_lib['sess']->get_person('AccessLevel') >= 2)
    {
        ?>
        <!--input type="submit" value="Slett firma(D)" name="action_company_delete" tabindex="0" accesskey="D"-->
        <input type="submit" value="Lagre (S)" name="action_company_update" tabindex="0" accesskey="S">
        <?
    }
    ?>
    </td>
  </tr>
</tfoot>
</table>
</form>

    </body>

</html>
