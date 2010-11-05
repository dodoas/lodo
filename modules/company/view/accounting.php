<?
# $Id: accounting.php,v 1.39 2005/05/26 13:54:57 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "company";
$db_table2 = "person";
$db_table3 = "accountplan";
$CompanyID = $_REQUEST['CompanyID'];
assert(!is_int($CompanyID)); #All main input should be int

require_once  "record.inc";

$query  = "select * from $db_table where CompanyID='$CompanyID'";
$row    = $_lib['storage']->get_row(array('query' => $query));

$query2  = "select AccountPlanID, AccountName, ReskontroFromAccount, ReskontroToAccount, AccountPlanType, ReskontroAccountPlanType from $db_table3 where EnableReskontro=1 and Active=1";
$result2 = $_lib['db']->db_query($query2);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: accounting.php,v 1.39 2005/05/26 13:54:57 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top'); ?>
<? includeinc('left') ?>

<h2>Firmaopplysninger, <? print $row->VName; ?> (side 2 av 3)</h2>
<? print $message ?>
<table class="tab">
  <tr>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.edit&CompanyID=<? print $CompanyID ?>">Adresser og kontaktinformasjon</a></div>
  <td><div class="active_tab"><a href="#">Regnskapsinformasjon</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=company.misc&CompanyID=<? print $CompanyID ?>">Diverse</a></div>
  <td><div class="tab"><a href="<? print $_SETUP['DISPATCH'] ?>&t=borettslag.borettslag&CompanyID=<? print "$CompanyID"; 
?>">Borettslag</a></div>
</table>
<table cellspacing="0" class="lodo_data">
<thead>
  <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
  <input type="hidden" name="CompanyID" value="<? print $row->CompanyID ?>">
</thead>

<tbody>

  <tr>
    <th colspan="4" class="menu">Kontoer</th>
  </tr>
  <tr>
    <td class="BGColorDark">Bankkonto</td>
    <td class="BGColorLight" colspan="3"><input type="text" name="company.BankAccount"  value="<? print $row->BankAccount ?>" size="70"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Postgirokonto</td>
    <td class="BGColorLight" colspan="3"><input type="text" name="company.PostAccount"  value="<? print $row->PostAccount ?>" size="70"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Renter
  </tr>
  <tr>
    <td class="BGColorDark">Rentesats</td>
    <td class="BGColorLight"><input type="text" name="company.InterestRate" value="<? print $row->InterestRate ?>" size="24"></td>
    <td class="BGColorDark">Rentedato</td>
    <td class="BGColorLight"><input type="text" name="company.InterestDate" value="<? print $row->InterestDate ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Purregebyr</td>
    <td class="BGColorLight"><input type="text" name="company.CollectionFee" value="<? print $row->CollectionFee ?>" size="24"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Aksjer</th>
  </tr>
  <tr>
    <td class="BGColorDark">Aksjeverdi</td>
    <td class="BGColorLight"><input type="text" name="company.ShareValue"   value="<? print $row->ShareValue ?>" size="24"></td>
    <td class="BGColorDark">Antall aksjer</td>
    <td class="BGColorLight"><input type="text" name="company.ShareNumber"  value="<? print $row->ShareNumber ?>" size="24"></td>
  </tr>
  <tr>
    <td class="BGColorDark">Stiftelsesdato</td>
    <td class="BGColorLight"><input type="text" name="company.FoundedDate"   value="<? print $row->FoundedDate ?>" size="24"></td>
    <td class="BGColorDark"></td>
    <td class="BGColorLight"></td>
  </tr>
  <tr>
    <th colspan="4" class="menu">Regnskap (F&oslash;rste ledig nummer)</th>
  </tr>
  <tr>
    <td class="BGColorDark">K Kassa bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherCashNumber"    value="<? print "$row->VoucherCashNumber" ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableCashNumberSequence", $row->EnableCashNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">B Bank bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherBankNumber"    value="<? print $row->VoucherBankNumber ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableBankNumberSequence", $row->EnableBankNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">U Kj&oslash;p - bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherBuyNumber" value="<? print "$row->VoucherBuyNumber" ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableBuyNumberSequence", $row->EnableBuyNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">L L&oslash;nn bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherSalaryNumber"  value="<? print "$row->VoucherSalaryNumber" ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableSalaryNumberSequence", $row->EnableSalaryNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">S Salg - bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherSaleNumber"    value="<? print $row->VoucherSaleNumber ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableSaleNumberSequence", $row->EnableSaleNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">A Auto bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherAutoNumber"    value="<? print "$row->VoucherAutoNumber" ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableAutoNumberSequence", $row->EnableAutoNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">O Ukeomsetning bilagsnummer</td>
    <td class="BGColorLight"><input type="text" name="company.VoucherWeeklysaleNumber"    value="<? print "$row->VoucherWeeklysaleNumber" ?>" size="24"></td>
    <td class="BGColorDark">Justeres utenfor rekkef&oslash;lge</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "EnableWeeklysaleNumberSequence", $row->EnableWeeklysaleNumberSequence,'') ?></td>
  </tr>
  <tr>
    <td class="BGColorDark">Resultatkonto balanse</td>
    <td class="BGColorLight">
    <? print $_lib['form3']->accountplan_number_menu(array('table'=> 'company', 'field'=>'VoucherBalanceAccount', 'value'=>$row->VoucherBalanceAccount, 'type' => array(0 => 'balance'), 'required' => 1)) ?>
    </td>
    <td class="BGColorDark">Resultatkonto resultat</td>
    <td class="BGColorLight">
    <? print $_lib['form3']->accountplan_number_menu(array('table'=> 'company', 'field'=>'VoucherResultAccount', 'value'=>$row->VoucherResultAccount, 'type' => array(0 => 'result'), 'required' => 1)) ?>
    </td>
  </tr>
  <tr>
    <th colspan="4" class="menu">MVA</th>
  </tr>
  <tr>
    <td class="BGColorDark">MVAplikt (gjelder også auto MVA)</td>
    <td class="BGColorLight"><? $_lib['form2']->checkbox2($db_table, "VATDuty",$row->VATDuty,'') ?><br></td>
    <td class="BGColorDark">Oppgj&oslash;rskonto MVA</td>
    <td class="BGColorLight">
    <? print $_lib['form3']->accountplan_number_menu(array('table'=> 'company', 'field'=>'AccountVat', 'value'=>$row->AccountVat, 'type' => array(0 => 'balance'), 'required' => 1)) ?>
    </td>
  </tr>
  <tr>
    <td>MVA Periode</td>
    <td><? print $_lib['form3']->Type_menu3(array('table' => 'company', 'field'=>'VatPeriod', 'value' => $row->VatPeriod, 'type'=>'typevatperiodmenu')) ?></td>
    <td></td>
    <td></td>
  </tr>
  <?
  /*if($_lib['sess']->get_person('AccessLevel') > 2)
  {
  ?>
  </tr>
  <tr>
    <td class="BGColorDark">Automatisk MVA</td>
    <td class="BGColorDark"><? print $_lib['form3']->checkbox(array('table'=>'company', 'field'=>'EnableVat', 'value'=>$row->EnableVat)) ?></td>
    <td></td>
    <td></td>
  </tr>
  <?
  }*/
  ?>
</tbody>
<tfoot>
  <tr class="BGColorDark">
    <td align="right" colspan="4">
    <?
    if($_lib['sess']->get_person('AccessLevel') >= 2)
    {
        ?>
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
