<?
/* $Id: template.php,v 1.19 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$AccountID = $_REQUEST['AccountID'];
assert(!is_int($AccountID)); #All main input should be int

includemodel('bank/bank');
includemodel('bank/bankaccount');
includemodel('accounting/accounting');

$bankaccount = new model_bank_bankaccount(array('AccountID' => $AccountID));

require_once "record.inc";

$query_bank     = "select * from account where AccountID='$AccountID'";
$result_bank    = $_lib['db']->db_query($query_bank);
$bank           = $_lib['db']->db_fetch_object($result_bank);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - template</title>
    <meta name="cvs"                content="$Id: template.php,v 1.19 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="template_update" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AccountID"       value="<? print $AccountID ?>">
<table class="lodo_data">
  <tr>
    <th colspan="21">Bankkonto oppsett: <? print $bank->AccountDescription ?>
  </tr>
  <tr>
    <th class="sub">ID</th>
    <td><? print $bank->AccountID ?></td>
  </tr>
  <tr>
    <th class="sub">Kontonavn</th>
    <td><input type="text" name="account.AccountDescription.<? print $bank->AccountID ?>"   value="<? print $bank->AccountDescription ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Bankens navn</th>
    <td><input type="text" name="account.BankName.<? print $bank->AccountID ?>"             value="<? print $bank->BankName ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Kontoinnehavers navn</th>
    <td><input type="text" name="account.OwnerName.<? print $bank->AccountID ?>"        value="<? print $bank->OwnerName ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Kontonummer</th>
    <td><input type="text" name="account.AccountNumber.<? print $bank->AccountID ?>"    value="<? print $bank->AccountNumber ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Koblet til regnskapskonto</th>
    <td>
        <?
        $aconf = array();
        $aconf['table']         = 'account';
        $aconf['field']         = 'AccountPlanID';
        $aconf['value']         = $bank->AccountPlanID;
        $aconf['pk']            = $bank->AccountID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = 'K';
        $aconf['width']         = '30';
        $aconf['type'][]        = 'balance';
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
  </tr>
  <tr>
    <th class="sub">Gyldig fra (yyyy-mm-dd)</th>
    <td><input type="text" name="account.ValidFrom.<? print $bank->AccountID ?>" value="<? print $bank->ValidFrom ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Gyldig til (yyyy-mm-dd)</th>
    <td><input type="text" name="account.ValidTo.<? print $bank->AccountID ?>"  value="<? print $bank->ValidTo ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Sortering</th>
    <td><input type="text" name="account.Sort.<? print $bank->AccountID ?>"  value="<? print $bank->Sort ?>" size="30" class="number"></td>
  </tr>
  <tr>
    <th class="sub">Default periode</th>
    <td><? print $_lib['form3']->AccountPeriod_menu3(array('table' => 'account', 'field' => 'DefaultPeriod', 'pk' => $bank->AccountID, 'value' => $bank->DefaultPeriod, 'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 'required'=> false)); ?></td>
  </tr>
  <tr>
    <th class="sub">Bilagstype</th>
    <td><? print $_lib['form3']->Type_menu3(array('table' => 'account', 'field' => 'VoucherType', 'pk' => $bank->AccountID, 'type' => 'VoucherType','value' => $bank->VoucherType, 'required'=>'1')) ?></td>
  </tr>
  <tr>
    <th class="sub">Inkluder i saldoberegning ved siden av resultat</th>
    <td><? $_lib['form2']->checkbox2('account', "includeinsaldo", $bank->includeinsaldo, $bank->AccountID); ?></td>
  </tr>
  <tr>
    <th class="sub">Aktiv</th>
    <td><? $_lib['form2']->checkbox2('account', "Active", $bank->Active, $bank->AccountID); ?></td>
  </tr>
</table>
<? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
  <input type="submit" name="action_bank_update"     value="Lagre (S)" accesskey="S" />
  <input type="submit" name="action_bank_cardadd"    value="Nytt kort (N)" accesskey="N" />
<? } ?>

<?php

/****
 * IKKE I DRIFT

<table>
<tr>
    <th>Kort type</th>
    <th>Kortnummer</th>
    <th>Eier</th>
    <th>Exp mnd</th>
    <th>Exp &aring;r</th>
    <th>Sort</th>
    <th></th>
</tr>
<? 
$bankaccount->bancaccountcard($AccountID);
foreach($bankaccount->bankaccountcardA as $tmp => $card) { ?>
<tr>
    <td><input type="text" name="bankaccountcard.CardType.<? print $card->BankAccountCardID ?>"                 value="<? print $card->CardType ?>"         size="6" class="number"></td>
    <td><input type="text" name="bankaccountcard.CardNumber.<? print $card->BankAccountCardID ?>"               value="<? print $card->CardNumber ?>"       size="16" class="number"></td>
    <td>
    <?
        $aconf = array();
        $aconf['table']         = 'bankaccountcard';
        $aconf['field']         = 'CardOwnerAccountPlanID';
        $aconf['value']         = $card->CardOwnerAccountPlanID;
        $aconf['pk']            = $card->BankAccountCardID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = '';
        $aconf['width']         = '20';
        $aconf['type'][]        = 'employee';
        print $_lib['form3']->accountplan_number_menu($aconf);
        ?>
    </td>
    <td><input type="text" name="bankaccountcard.ExpirationMonth.<? print $card->BankAccountCardID ?>"          value="<? print $card->ExpirationMonth ?>"  size="2" class="number"></td>
    <td><input type="text" name="bankaccountcard.ExpirationYear.<? print $card->BankAccountCardID ?>"           value="<? print $card->ExpirationYear ?>"   size="2" class="number"></td>
    <td><input type="text" name="bankaccountcard.Sort.<? print $card->BankAccountCardID ?>"                     value="<? print $card->Sort ?>"             size="2" class="number"></td>
    <td><input type="submit" name="action_bank_cardremove" value="Slett kort (D)" accesskey="D" /></td>
</tr>
<? } ?>
</table>
</form>


***/ ?>


<? includeinc('bottom') ?>
</body>
</html>
