<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includemodel('bank/bank');
includemodel('bank/bankaccount');
includemodel('accounting/accounting');
includelogic('fakturabank/fakturabankvoting');

$bankaccount    = new model_bank_bankaccount($_lib['input']->request);
$bank           = new framework_logic_bank($_lib['input']->request);
$accounting     = new accounting();

require_once "record.inc";

$fbbank = new lodo_fakturabank_fakturabankvoting();
$bank->init(); #Read data

$bankname = $_lib['db']->db_query("SELECT AccountName FROM accountplan WHERE AccountPlanID = " . $bank->AccountPlanID);
$bankname = $_lib['db']->db_fetch_assoc($bankname);
$bankname = $bankname['AccountName'];
$bankvotingperiod_id = $bank->bankvotingperiod->BankVotingPeriodID;

$_lib['form3']->Locked = $bank->bankvotingperiod->Locked;

$host = $GLOBALS['_SETUP']['FB_SERVER'];
$protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'] . "://";
$old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
$new_pattern    = array("", "", "");
$identifier = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));
$params = "/bank_statements/get_bank_statement_for_lodo?identifier=" . $identifier . "&account_number=" . preg_replace("/\s+/","", $bank->AccountNumber) . "&period=" . $bank->ThisPeriod;

?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - avstemming av bank</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>

    <style>
      td.highlighted {
        background-color: rgba(0, 182, 0, 0.6) !important;
      }
    </style>

    <script>
      /* script for å generere de enorme konto-listene */
      /*
        $reskontroconf['field']         = 'ReskontroAccountPlanID';
        $reskontroconf['value']         = $row->ReskontroAccountPlanID;
        $reskontroconf['type'][]        = 'reskontro';
        $reskontroconf['type'][]        = 'employee';
      */

      <?php
        function generate_kontoliste($conf) {
            global $_lib;

            /* lager javascript-funksjon-toppen */
            echo "function kontoliste_";
            foreach($conf['type'] as $v)
                echo $v;
            echo "(name, selected, dest) {\n";

            /* lager JSON-array med alle kontoene. */
            printf("var data = %s", json_encode($_lib['form3']->accountplan_number_menu3($conf)));

            echo "
          color = '';
          text  = 'Velg konto';

          for(var i = 0; i < data.length; i++) {
            if(data[i][0] == selected) {
              color = data[i][1];
              text  = data[i][2];
              break;
            }
          }

          /* setter inn valgt element */
          /* 0: value, 2: color, 3: text */
          select =  document.getElementById('kontoliste_' + dest);
          select.style.width = '200px';
          select.name = name;

          // complying with current implementation where select's bacgroundcolor is same as selected option
          select.style.backgroundColor = color;

          var option = document.createElement('option');
          option.style.backgroundColor = color;
          option.innerHTML = text;
          option.value = selected;
          try {
            select.add(option, null);
          } catch(ex) {
            select.appendChild(option);
          }

          /* on mouse over lages resten av listen */
          select.onmouseover = function(e) {
            var targ;
            if (!e) var e = window.event;
            if (e.target) targ = e.target;
            else if (e.srcElement) targ = e.srcElement;


            try {
            if (targ.nodeType == 3) // defeat Safari bug
              targ = targ.parentNode;
            } catch(exp) {
               /* some error */
            }

            targ.onmouseover = null;
            if(targ.length > 1)
              return;

var selectedOptionText = targ.options[targ.selectedIndex].text;

            if (selectedOptionText != 'Velg konto') {
              var option = document.createElement('option');
              option.value = 'unset';
              option.innerHTML = 'Velg Konto';
              targ.appendChild(option);
            }

            for(i = 0; i < data.length; i++) {
              value = data[i][0];
              color = data[i][1];
              text  = data[i][2];

              if(value == selected || text == '')
                continue;

              var option = document.createElement('option');
              option.value = value;
              option.style.backgroundColor = color;
              option.innerHTML = text;
              try {
                targ.add(option, null);
              } catch(ex) {
                //targ.add(option);
                targ.appendChild(option);
              }
            }
          }

          ";

          echo "}";
        }

        $field_counter = 0;

        function display_kontoliste($conf) {
            global $field_counter;

            $field_counter++;
            printf("<select id='kontoliste_%d'></select>", $field_counter);

            echo "<script> kontoliste_";
            foreach($conf['type'] as $v)
                echo $v;
            printf("('%s.%s.%d', %d, %d);</script>", $conf['table'], $conf['field'], $conf['pk'], $conf['value'], $field_counter);
        }

        $reskontroconf['value']         = $row->ReskontroAccountPlanID;
        $reskontroconf['type'][]        = 'reskontro';
        $reskontroconf['type'][]        = 'employee';
        $reskontroconf['field']         = 'ReskontroAccountPlanID';
	//$reskontroconf['showInactive']  = true;
        generate_kontoliste($reskontroconf);

        $resultconf['field']         = 'ResultAccountPlanID';
        $resultconf['value']         = $row->ResultAccountPlanID;
        $resultconf['type'][]        = 'hovedbok';
        //$resultconf['showInactive']  = true;
        generate_kontoliste($resultconf);

        $reskontroconf = null;
        $resultconf = null;
      ?>

      function update_hidden_fields() {
        var bankvotingperiod_id = "<?= $bankvotingperiod_id ?>";
        var AmountIn  = toNumber($('#bankvotingperiod\\.AmountIn\\.' + bankvotingperiod_id).val());
        var AmountOut = toNumber($('#bankvotingperiod\\.AmountOut\\.' + bankvotingperiod_id).val());
        $('input[type=hidden][name=bankin]').val(AmountIn);
        $('input[type=hidden][name=bankout]').val(AmountOut);
      }

      function highlight(elementid){
        $(elementid).addClass("highlighted");
        setTimeout(function() {
          $(elementid).removeClass("highlighted");
        } , 5000);
      }
    $(document).ready(function() {
      $('input:submit[name=action_save_extras]').click(function () {
        update_hidden_fields();
      });
      $('.navigate.to').click(function(e) {
        var element = $(e.target);
        var targetID = element.attr('id');
        highlight('.column_' + targetID);
      });

    });
    </script>
</head>
<body>

<?
// create_span is a helper function highlighting matching lines
function create_span($value, $ID){
    $ID = preg_replace("/[\(\)]/", "_", $ID);
    return "<span class=\"navigate to\" id=$ID>$value</span>";
}

function create_diff($InvoiceID, $KID, $JournalID, $AccountPlanID, $BankVoucherAmount, $bank){
    global $_lib;
    $HighlightID = (empty($InvoiceID) && empty($KID)) ? "empty$EmptyHighlightCount" : "$JournalID-$InvoiceID-$KID";
    if($bank->is_closeable($AccountPlanID, $KID, $InvoiceID, $JournalID)){
        return create_span("Lukket", $HighlightID);
    } else {
        $DiffAmount = $_lib['format']->Amount($bank->getDiff($AccountPlanID, $KID, $InvoiceID, $JournalID, $BankVoucherAmount, (($BankVoucherAmount > 0) ? "inn" : "out"), 'voucher'));
        return create_span("Diff (" . $DiffAmount . ")", $HighlightID);
    }
}
$CheckedJournalIDsAccountline = $bank->checkJournalIDAccountline($bank->ThisPeriod);
$CheckedJournalIDsVoucher     = $bank->checkJournalIDVoucher();
?>
<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['message']->get(); ?>

<?
$relationcount = array();
if(is_array($bank->bankaccount)) {
    foreach($bank->bankaccount as $row) {
        if( substr($row->InvoiceNumber, 0, 2) == "FB" ) {
            preg_match("/FB\((\d+)\)/", $row->InvoiceNumber, $matches);
            $fakturabankID = $matches[1];

            $relations = $fbbank->get_faturabanktransactionrelations($fakturabankID);

            $c = count($relations);
            $relationcount[$fakturabankID] = $c;

            if($c == 0) {
                printf("<b>Noe er galt med denne importen fra fakturabank FB(%d).</b><br />", $fakturabankID);
            }
        }
    }
}
?>

<form name="period_choice1" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AccountID" value="<?= $bank->AccountID ?>">
<input type="hidden" name="Period" value="<?= $bank->ThisPeriod ?>">
<input type="hidden" name="BankVotingPeriodID" value="<?= $bankvotingperiod_id ?>">

Neste ledige Bank (B) bilagsnummer: <? print $_lib['sess']->get_companydef('VoucherBankNumber'); ?>

<table class="lodo_data">
    <tr class="result">
        <th colspan="26">
        Velg periode

        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_prev_period($bank->ThisPeriod), 'description' => '<<', 'title' => 'Prev')) ?>
        <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_next_period($bank->ThisPeriod), 'description' => '>>', 'title' => 'Next')) ?>

        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?>
        <? if ($_lib['sess']->get_person('AccessLevel') > 1) { ?> |
            <? print $_lib['form3']->url(array('description' => 'Bilagsf&oslash;r/Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
            <? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
            <? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod));
            if($_lib['sess']->get_person('FakturabankImportBankTransactionAccess')) { ?> |
                <? print $_lib['form3']->url(array('description' => 'Import fra FakturaBank',          'url' => $_lib['sess']->dispatch . 't=bank.fakturabankimport'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod));
            } ?>
        <? } ?>
        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
    <tr>
      <td colspan="4">
      </td>
      <td colspan="19">
      </td>
    </tr>
</table>
</form>

<form id="list_form" name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AccountID" value="<?= $bank->AccountID ?>">
<input type="hidden" name="Period" value="<?= $bank->ThisPeriod ?>">
<input type="hidden" name="BankVotingPeriodID" value="<?= $bankvotingperiod_id ?>">

<?php
   $extras_r = $_lib['db']->db_query(
       sprintf("SELECT * FROM accountextras WHERE AccountID = %d AND Period = '%s'", $bank->AccountID, $bank->ThisPeriod)
       );
   $extras = $_lib['db']->db_fetch_assoc($extras_r);
   if(!$extras) {
      $extraEntryIn = 0;
      $extraEntryOut = 0;
      $extraLastIn = 0;
      $extraLastOut = 0;
      $extraStartAtJournalID = 0;
   }
   else {
      $extraEntryIn = $extras['BankEntryIn'];
      $extraEntryOut = $extras['BankEntryOut'];
      $extraLastIn = $extras['BankLastIn'];
      $extraLastOut = $extras['BankLastOut'];
      $extraStartAtJournalID = $extras['JournalID'];
   }
    if($bank->bankvotingperiod->AmountIn - $bank->bankvotingperiod->AmountOut == 0) {
        $bankin = $extraEntryIn;
        $bankout = $extraEntryOut;
    }
    else {
        $bankin = $bank->bankvotingperiod->AmountIn;
        $bankout = $bank->bankvotingperiod->AmountOut;
    }

    if($extraStartAtJournalID === 0 || $extraStartAtJournalID === NULL ) {
      $select_maxjournalid   = "select MAX(JournalID) as JournalID from voucher where VoucherType='B' and Active=1;";
      $maxjournalid          = $_lib['storage']->get_row(array('query' => $select_maxjournalid));
      $extraStartAtJournalID = intval($maxjournalid->JournalID) + 1;
    }
?>

<table class="lodo_data">

<tr>
  <td>Bank den f&oslash;rste</td>
  <td class="<? print $bank->DebitColor ?> number" style="min-width: 100px"><? print $_lib['form3']->text(array('style' => 'text-align: right;', 'name' => 'extraEntryIn', 'value' => $_lib['format']->Amount($extraEntryIn), 'class' => 'number')); ?></td>
  <td class="<? print $bank->CreditColor ?> number" style="min-width: 100px"><? print $_lib['form3']->text(array('style' => 'text-align: right;', 'name' => 'extraEntryOut', 'value' => $_lib['format']->Amount($extraEntryOut), 'class' => 'number')); ?></td>
  <td></td>
  <? $bankvotingsum_entry = round(($bank->bankvotingperiod->AmountIn - $bank->bankvotingperiod->AmountOut) - ($extraEntryIn - $extraEntryOut), 2);
     $banksum_entry = round(($bankin - $bankout) - $bank->voucher->saldo, 2);
     $bankvotingsum_entry_color = ($bankvotingsum_entry == 0) ? "" : "red";
     $banksum_entry_color = ($banksum_entry == 0) ? "" : "red"; ?>
  <td class="<?= $bankvotingsum_entry_color ?>"><? echo "<span>Kontoutskrift-banktransaksjoner: diff " . $_lib['format']->Amount($bankvotingsum_entry) . "</span>";  ?></td>
  <td class="<?= $banksum_entry_color ?>"><? echo "<span>Banktransaksjoner-bankbilag regnskap: diff " . $_lib['format']->Amount($banksum_entry) . "</span>"; ?></td>
</tr>
<tr>
  <td>Bank den siste</td>
  <td class="<? print $bank->DebitColor ?> number" style="min-width: 100px"><? print $_lib['form3']->text(array('style' => 'text-align: right;', 'name' => 'extraLastIn', 'value' => $_lib['format']->Amount($extraLastIn), 'class' => 'number')); ?></td>
  <td class="<? print $bank->CreditColor ?> number" style="min-width: 100px"><? print $_lib['form3']->text(array('style' => 'text-align: right;', 'name' => 'extraLastOut', 'value' => $_lib['format']->Amount($extraLastOut), 'class' => 'number')); ?></td>
  <td></td>

  <? $bankvotingsum_last = round($bank->bankaccountcalc->AmountSaldo - ($extraLastIn - $extraLastOut), 2);
     $banksum_last = round($bank->bankaccountcalc->AmountSaldo - $bank->voucher->sumSaldo, 2);
     $bankvotingsum_last_color = ($bankvotingsum_last == 0) ? "" : "red";
     $banksum_last_color = ($banksum_last == 0) ? "" : "red"; ?>
    <td class="<?= $bankvotingsum_last_color ?>"><? echo "<span>Kontoutskrift-banktransaksjoner: diff " . $_lib['format']->Amount($bankvotingsum_last) . "</span>";  ?></td>
    <td class="<?= $banksum_last_color ?>"><? echo "<span>Banktransaksjoner-bankbilag regnskap: diff " . $_lib['format']->Amount($banksum_last) . "</span>"; ?></td>
</tr>

<tr>
  <td></td>
  <td></td>
  <td style="text-align: right;">
  <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
    Bilagsnr: <input type="text" size="11" name="action_bank_accountlinenew_startat" class="number"
                     value="<?= $extraStartAtJournalID ?>">
  <? } ?>
  </td>
  <input type="hidden" name="bankin" value="<?= $bankin ?>">
  <input type="hidden" name="bankout" value="<?= $bankout ?>">
  <td><? if($_lib['sess']->get_person('AccessLevel') > 1 && !$bank->bankvotingperiod->Locked) { ?><input type="submit" name="action_save_extras" value="Lagre bank" /><? } ?></td>

</tr>

<tr>
  <td><a href= "<?= $protocol . $host . $params?>"$protocol target="_new"><input type="button" value="Vis kontoutskrift i fakturaBank"></input></a></td>
  <td></td>
  <td style="text-align: right;">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
      Antall: <input type="text" name="numnewlines" value="0" size="3" class="number">
    <? } ?>
  </td>
  <td>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
        <input type="submit" name="action_bank_accountlinenew" value="Nye linjer (N)" accesskey="N" tabindex="100000">
    <? } ?>
  </td>
</tr>

</table>
</form>
<form name="period_choice" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="AccountID" value="<?= $bank->AccountID ?>">
<input type="hidden" name="Period" value="<?= $bank->ThisPeriod ?>">
<input type="hidden" name="BankVotingPeriodID" value="<?= $bankvotingperiod_id ?>">
<? // added so the default submit action is sent on Enter ?>
<input type="hidden" name="action_bank_update" value="1">

<table class="lodo_data">

<tr>
    <td colspan="14"
        class="<? $v = $bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo; if(abs($v) < 0.00001 && abs($v) > -0.00001) print 'sub'; else print 'red';?>">
      Kontoutskrift <? print $_lib['date']->get_last_day_in_month($bank->PrevPeriod) ?>:
      <? print $_lib['format']->Amount($bank->prevbankaccountcalc->AmountSaldo) ?>
      Kontoutskrift <? print $_lib['date']->get_first_day_in_month($bank->ThisPeriod) ?>:
      <? print $_lib['format']->Amount($bankin - $bankout) ?>
      <? print "diff " . $_lib['format']->Amount($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo); ?>
    </td>
</tr>

  <tr>
    <td class="menu">Pri</td>
    <td class="menu">Bilagsnr</td>
    <td class="menu">Dag</td>
    <td class="menu">Ut av konto</td>
    <td class="menu">Inn p&aring; konto</td>
    <td class="menu">Fakturanr</td>
    <td class="menu">KID</td>
    <td class="menu">Tekst hovedbok</td>
    <td class="menu">Kommentarer</td>
    <td class="menu">OK</td>
    <td class="menu">Reskontro</td>
    <td class="menu">Auto</td>
    <td class="menu">Hovedbokskonto</td>
    <td class="menu">MVA</td>
    <td class="menu">Mengde</td>
    <td class="menu">Bil</td>
    <td class="menu">Prosjekt</td>
    <td class="menu">Avdeling</td>
    <td class="menu">KID match</td>
    <td class="menu"></td>

    <td class="menu">Fakturanr</td>
    <td class="menu">KID</td>
    <td class="menu">Bilag</td>
    <td class="menu"></td>
    <td class="menu">Inn</td>
    <td class="menu">Ut</td>
    <td class="menu">Dato</td>
  </tr>
  <tr>


    <td colspan="2">Saldo<? print $bank->ThisPeriod ?>-01
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S" tabindex="1">
    <? } ?>
    </td>

    <td></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountOut',
                                            'pk' => $bank->bankvotingperiod->BankVotingPeriodID,
                                            'value' =>$_lib['format']->Amount($bankout),     'class' => 'number')) ?></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountIn',
                                            'pk' => $bank->bankvotingperiod->BankVotingPeriodID,
                                            'value' =>$_lib['format']->Amount($bankin),      'class' => 'number')) ?></td>
    <td colspan="14"></td>
    <td colspan="5" class="sub"></td>
    <td class="number sub"><? print $_lib['format']->Amount($bank->voucher->saldo) ?></td>
    <td colspan="4" class="sub"></td>
  </tr>

<?
##############################################################################################################################
#Main loop
$tabindex = 200; #We start at 100 to have som space in front
$count    = count($bank->bankaccount) + 100; #This is the number of records - used for tabindex.

$tabindexH[1] = $tabindex + ($count * 1);
$tabindexH[2] = $tabindex + ($count * 2);
$tabindexH[3] = $tabindex + ($count * 3);
$tabindexH[4] = $tabindex + ($count * 3) + 1;
$tabindexH[5] = $tabindex + ($count * 5);
$tabindexH[6] = $tabindex + ($count * 5) + 1;
$tabindexH[7] = $tabindex + ($count * 7);

$EmptyHighlightCount = 0;

if(is_array($bank->bankaccount)) {
    foreach($bank->bankaccount as $row) {

        $i++;

        $reskontroaccountplan = null;

        if (!empty($row->ReskontroAccountPlanID)) {
            $reskontroaccountplan   = $accounting->get_accountplan_object($row->ReskontroAccountPlanID);
        }
        if (!empty($row->ResultAccountPlanID)) {
            $resultaccountplan      = $accounting->get_accountplan_object($row->ResultAccountPlanID);
        }
        else $resultaccountplan = null;

        $aconf = array();
        $aconf['table']         = 'accountline';
        $aconf['pk']            = $row->AccountLineID;

        $reskontroconf = $resultconf = $aconf;

        if($row->Approved) {
            $classApproved = 'creditblue';
        } else {
            $classApproved = 'creditred';
        }
	if(is_array($bank->bankvoucher_this_hash))
        	$bankvoucher = array_pop($bank->bankvoucher_this_hash);

        // check if journalID is already in use
        //  and mark it red if it is.
        {
            if (intval($CheckedJournalIDsAccountline[$row->JournalID]['Count']) > 1) $JournalIDExists = true;
            if (isset($CheckedJournalIDsVoucher[$row->JournalID])) $JournalIDExists = true;
            $JournalIDColColor = $JournalIDExists ? "style='background-color: red;'" : "";
        }

        if(empty($row->InvoiceNumber) && empty($row->KID)){
            $EmptyHighlightCount ++;
            $BankHiglightClass ="column_empty$EmptyHighlightCount";
        } else{
            $BankHiglightClass = preg_replace("/[\(\)]/", "_", "column_$row->JournalID-$row->InvoiceNumber-$row->KID");
        }

        if($bank->is_closeable($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber, $row->JournalID)) {
            // if it has been closed then JournalID should not be red.
            $JournalIDColColor = '';
            $matchCaption = "Lukket";
        } else {
            $matchCaption = "Diff(" . $_lib['format']->Amount($bank->getDiff($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber, $row->JournalID, ($row->AmountIn - $row->AmountOut), (($row->AmountIn > 0) ? "inn" : "out"), "bank")) . ")";
        }

        if (!($i % 3)) { $sec_color = "r0"; } else { $sec_color = "r1"; };
        ?>
      <tr class="<? print $sec_color ?>">
        <td class="<?=$BankHiglightClass?>">
            <? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Priority', 'pk' => $row->AccountLineID, 'value' => $row->Priority, 'width' => 3, 'tabindex' => $tabindexH[0])); ?>
        </td>
        <td class="<?= $BankHiglightClass ?>" <?= $JournalIDColColor ?>>
            <? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'JournalID', 'pk' => $row->AccountLineID, 'value' => $row->JournalID, 'width' => 6, 'tabindex' => $tabindexH[1])); ?>
        </td>
        <td class="<?=$BankHiglightClass?>"><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Day', 'pk' => $row->AccountLineID, 'value' => $row->Day, 'class' => 'number', 'width' => 2, 'tabindex' => $tabindexH[2])) ?></td>

        <td class="<?="$BankHiglightClass $bank->CreditColor"?> number" style="min-width: 106px">
        <?
            if($row->AmountOut > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountOut), 'class' => $row->classAmountOut, 'tabindex' => $tabindexH[3]));
            else
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountOut, 'tabindex' => $tabindexH[3]));
        ?>
        </td>
        <td class="<?="$BankHiglightClass $bank->DebitColor"?> number" style="min-width: 106px">
            <?
            if($row->AmountIn > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountIn),     'class' => $row->classAmountIn, 'tabindex' => $tabindexH[4]));
            else
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountIn, 'tabindex' => $tabindexH[4]));

            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=bank&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">')) ?>
        </td>

        <? if($row->InvoiceNumber != '' || count($row->MatchSelect) < 1) { ?>
        <td class="<?=$BankHiglightClass ?>">
            <?

            print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'InvoiceNumber', 'pk' => $row->AccountLineID, 'value' => $row->InvoiceNumber,     'class' => 'number', 'width' => 20, 'maxlength' => 25, 'tabindex' => $tabindexH[5]));

            if(substr($row->InvoiceNumber, 0, 2) == "FB") {
                preg_match("/FB\((\d+)\)/", $row->InvoiceNumber, $matches);
                $fakturabankID = $matches[1];

                print $relationcount[$fakturabankID];
            }

            ?>
        </td>
        <? } ?>


        <td class="<?=$BankHiglightClass ?>" <? if($row->InvoiceNumber == '' && count($row->MatchSelect) >= 1) { print " colspan=\"2\""; } ?>>
            <?
            if($row->InvoiceNumber == '' && count($row->MatchSelect) >= 1) {
                print $_lib['form3']->select(array('table' => 'accountline', 'field' => 'KIDandInvoiceIDandAccountPlanID', 'pk' => $row->AccountLineID, 'value' => $row->KID, 'data' => $row->MatchSelect, 'width' => 50, 'required' => false));
            } else {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID,     'class' => 'number', 'width' => 22, 'maxlength' => 25, 'tabindex' => $tabindexH[6]));
            }
            ?>
        </td>



        <td class="<?=$BankHiglightClass?>"><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Description',     'pk' => $row->AccountLineID, 'value' => $row->Description,      'width' => 28, 'maxlength' => 255, 'tabindex' => $tabindexH[7])) ?></td>
        <td class="<?=$BankHiglightClass?>"><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Comment',         'pk' => $row->AccountLineID, 'value' => $row->Comment,          'width' => 28, 'maxlength' => 255, 'tabindex' => $tabindexH[8])) ?></td>
        <td class="<?="$BankHiglightClass $classApproved"?>"><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'Approved',     'pk' => $row->AccountLineID, 'value' => $row->Approved)) ?></td>
        <td class="<?=$BankHiglightClass?>">
            <?
            $reskontroconf['field']         = 'ReskontroAccountPlanID';
            $reskontroconf['value']         = $row->ReskontroAccountPlanID;
            $reskontroconf['type'][]        = 'reskontro';
            $reskontroconf['type'][]        = 'employee';

            display_kontoliste($reskontroconf);
            //print $_lib['form3']->accountplan_number_menu($reskontroconf);    // OLD
            print $_lib['form3']->URL(array('url' => $_lib['sess']->dispatch . "t=accountplan.reskontro&accountplan_AccountPlanID=$row->ReskontroAccountPlanID", 'description' => 'K', 'title' => 'Endre oppsett p&aring; denne kontoen', 'target' => '_top'));
            if (!empty($reskontroaccountplan)) {
                print $reskontroaccountplan->OrgNumber;
            }
            ?>
        </td>
        <td class="<?=$BankHiglightClass?>"><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'AutoResultAccount',     'pk' => $row->AccountLineID, 'value' => $row->AutoResultAccount, 'title' => 'Klikk her for &aring; velge resultatkonto automatisk fra reskontro')) ?></td>
        <td class="<?=$BankHiglightClass?>">
            <?
            $resultconf['field']         = 'ResultAccountPlanID';
            $resultconf['value']         = $row->ResultAccountPlanID;
            $resultconf['type'][]        = 'hovedbok';
	    display_kontoliste($resultconf);

            //print $_lib['form3']->accountplan_number_menu($resultconf);    // OLD
            ?>
        </td>
        <td class="<?=$BankHiglightClass?>">
            <?
              if(!empty($resultaccountplan) && $resultaccountplan->EnableVAT) {
                  print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Vat',        'pk' => $row->AccountLineID, 'value' => (int) $row->Vat,         'width' => 2, 'maxlength' => 3));
              }
            ?>
        </td>
        <td class="<?=$BankHiglightClass?>">
            <?
            if(!empty($resultaccountplan) && $resultaccountplan->EnableQuantity) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'ResultQuantity',        'pk' => $row->AccountLineID, 'value' => $row->ResultQuantity,         'width' => 5, 'maxlength' => 255));
            }
            ?>
        </td>
        <td class="<?=$BankHiglightClass?>">
        <? if(!empty($resultaccountplan) && $resultaccountplan->EnableCar || !empty($reskontroaccountplan) && $reskontroaccountplan->EnableCar) {
          $car_menu_conf = array(
            'table' => 'accountline',
            'field' => 'CarID',
            'pk' => $row->AccountLineID,
            'value' => $row->CarID,
            'unset' => true,
            'active_reference_date' => $bank->ThisPeriod."-".$row->Day);
         $_lib['form2']->car_menu2($car_menu_conf);
        } ?></td>
        <td class="<?=$BankHiglightClass?>">
        <? if(!empty($resultaccountplan) && $resultaccountplan->EnableProject || !empty($reskontroaccountplan) && $reskontroaccountplan->EnableProject) {
          $project_menu_conf = array(
            'table' => 'accountline',
            'field' => 'ProjectID',
            'pk' => $row->AccountLineID,
            'value' => $row->ProjectID,
            'unset' => true);
           $_lib['form2']->project_menu2($project_menu_conf);
         } ?></td>
        <td class="<?=$BankHiglightClass?>">
        <? if(!empty($resultaccountplan) && $resultaccountplan->EnableDepartment || !empty($reskontroaccountplan) && $reskontroaccountplan->EnableDepartment) {
          $department_menu_conf = array(
            'table' => 'accountline',
            'field' => 'DepartmentID',
            'pk' => $row->AccountLineID,
            'value' => $row->DepartmentID,
            'unset' => true);
            $_lib['form2']->department_menu2($department_menu_conf);
        } ?></td>
        <td class="<?=$BankHiglightClass?>">
          <?= create_span($matchCaption, ((empty($row->InvoiceNumber) && empty($row->KID)) ? "empty$EmptyHighlightCount" : "$row->JournalID-$row->InvoiceNumber-$row->KID")); ?>
        </td>
        <td class="horiz <?=$BankHiglightClass?>">
                  <?
                  if(!$_lib['form3']->Locked) {
                      print $_lib['form3']->URL(array('url' => $_lib['sess']->dispatch . "t=bank.tabbankaccount&amp;action_bank_accountlinedelete=1&amp;AccountLineID=$row->AccountLineID&amp;AccountID=$bank->AccountID&amp;Period=$bank->ThisPeriod", 'description' => '<img src="/lib/icons/trash.gif">', 'title' => 'Slett', 'confirm' => 'Er du sikker?'));
                  } ?>
            </td>
        <? if($bankvoucher) {
        if(empty($bankvoucher->InvoiceID) && empty($bankvoucher->KID)){
            $EmptyHighlightCount ++;
            $VoucherHiglightClass ="column_empty$EmptyHighlightCount";
        } else {
            $VoucherHiglightClass = preg_replace("/[\(\)]/", "_", "column_$bankvoucher->JournalID-$bankvoucher->InvoiceID-$bankvoucher->KID");
        }

        $VoucherDiff = create_diff($bankvoucher->InvoiceID, $bankvoucher->KID, $bankvoucher->JournalID, $bankvoucher->AccountPlanID, ($bankvoucher->AmountIn - $bankvoucher->AmountOut), $bank);
        ?>
        <td class="sub <?=$VoucherHiglightClass ?>"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'InvoiceID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->InvoiceID, 'class' => 'number', 'width' => 20, 'maxlength' => 25)) ?></td>
        <td class="sub <?=$VoucherHiglightClass ?>"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID',       'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,       'class' => 'number', 'width' => 20, 'maxlength' => 25)) ?></td>
        <td class="sub <?=$VoucherHiglightClass ?>"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub <?=$VoucherHiglightClass ?>"><?= $VoucherDiff ?></td>
        <td class="<?="$VoucherHiglightClass $bankvoucher->classAmountIn $bank->DebitColor" ?>">
        <? if($bankvoucher->AmountIn > 0) {
            print $_lib['format']->Amount($bankvoucher->AmountIn);
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">'));
        } ?>
        </td>
        <td class="<?="$VoucherHiglightClass $bankvoucher->classAmountOut $bank->CreditColor" ?>">
        <? if($bankvoucher->AmountOut > 0) {
            print $_lib['format']->Amount($bankvoucher->AmountOut);
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountOut&amp;searchstring=' . $row->AmountOut, 'description' => '<img src="/lib/icons/search.gif">'));
        } ?>
        </td>
        <td class="sub <?=$VoucherHiglightClass ?>"><? print $bankvoucher->VoucherDate ?></td>
        <? } else { ?>
        <td colspan="7" class="sub"></td>
        <? } ?>
      </tr>
    <?
        $sumin  += $row->AmountIn;
        $sumout += $row->AmountOut;

        $tabindexH[1]++;
        $tabindexH[2]++;
        $tabindexH[3] += 2;
        $tabindexH[4] = $tabindexH[3]+1;
        $tabindexH[5] += 2;
        $tabindexH[6] = $tabindexH[5]+1;
        $tabindexH[7]++;
    }
}

if(is_array($bank->bankvoucher_this_hash)) {
    foreach($bank->bankvoucher_this_hash as $bankvoucher) {

        if (!($i % 2)) { $sec_color = "r0"; } else { $sec_color = "r1"; };

        if(empty($bankvoucher->InvoiceID) && empty($bankvoucher->KID)){
            $EmptyHighlightCount ++;
            $VoucherHiglightClass = "column_empty$EmptyHighlightCount";
        } else{
            $VoucherHiglightClass = "column_$bankvoucher->JournalID-$bankvoucher->InvoiceID-$bankvoucher->KID";
        }

        $VoucherDiff = create_diff($bankvoucher->InvoiceID, $bankvoucher->KID, $bankvoucher->JournalID, $bankvoucher->AccountPlanID, ($bankvoucher->AmountIn - $bankvoucher->AmountOut), $bank);
        ?>
      <tr class="<? print $sec_color ?>">
        <td colspan="20"></td>
        <td class="sub <?= $VoucherHiglightClass ?>"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'InvoiceID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->InvoiceID, 'class' => 'number', 'class' => 'number', 'width' => 20, 'maxlength' => 25)) ?></td>
        <td class="sub <?= $VoucherHiglightClass ?>"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID',       'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,       'class' => 'number', 'class' => 'number', 'width' => 20, 'maxlength' => 25)) ?></td>
        <td class="sub <?= $VoucherHiglightClass ?>"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub <?= $VoucherHiglightClass ?>"><?= $VoucherDiff ?></td>
        <td class='<?= "$VoucherHiglightClass $bank->DebitColor" ?>'>
        <? print $_lib['format']->Amount($bankvoucher->AmountIn) ?>
        <? #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">')); ?>
        </td>
        <td class='<?= "$VoucherHiglightClass $bank->CreditColor" ?>'>
        <? print $_lib['format']->Amount($bankvoucher->AmountOut) ?>
        <? #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountOut&amp;searchstring=' . $row->AmountOut, 'description' => '<img src="/lib/icons/search.gif">')); ?>
        </td>
        <td class="sub <?= $VoucherHiglightClass ?>"><? print $bankvoucher->VoucherDate ?></td>
      </tr>
    <?
        $sumin  += $row->AmountIn;
        $sumout += $row->AmountOut;
    }
}
##############################################################################################################################
?>
<tr>
    <td colspan="3"></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountOut)  ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountIn)  ?></td>
    <td></td>
    <td colspan="17"></td>
    <td>Sum</td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumAmountIn) ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumAmountOut) ?></td>
    <td colspan="3"></td>
</tr>
<tr>
    <td colspan="3"></td>
    <td>Saldo <? print $_lib['date']->get_last_day_in_month($bank->ThisPeriod) ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountSaldo)  ?></td>
    <td></td>
    <td colspan="17"></td>
    <td>Saldo</td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumSaldo) ?></td>
    <td></td>
    <td colspan="3"></td>
</tr>
<?  if($bank->bankvotingperiod->Locked != 1) { ?>
<tr>
    <td class="menu"></td>
    <td class="menu" colspan="3">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_bank_zerojournalid" value="Slett bilagsnummer" accesskey="">
        <? } ?>
    </td>
    <td class="menu" colspan="2">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S">
    <? } ?>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) {
    print $_lib['form3']->submit(array('name' => 'action_bank_periodremove', 'value' => 'Slett hele perioden', 'confirm' => 'Er du sikker p&aring; at du vil slette hele perioden', 'accesskey' => 'D', 'confirm' => "Vil du virkelig slette kontoutskriften for perioden " . $bank->ThisPeriod));
      if($_lib['sess']->get_person('AccessLevel') >= 2) {
        ?><input type="submit" name="action_bank_automatching" value="Auto match (A)" accesskey="A"><?
      }
        print $_lib['form3']->submit(array('name' => 'action_bank_periodlock', 'value' => 'L&aring;s', 'accesskey' => 'L', 'confirm' => "Vil du virkelig l&aring;se perioden " . $bank->ThisPeriod, 'disabled' => ($bankvotingsum_entry != 0 || $bankvotingsum_last != 0) ? true : false));
    } ?>
    </td>
    <td style="vertical-align: top; text-align: left; background-color: #BBBBBB; color: #f44242; font-weight: bold; padding-top: 2px;" colspan="21">
    <? if($bankvotingsum_entry != 0 || $bankvotingsum_last != 0) {
           print "Du kan ikke l&aringse fordi kontoutskrift-banktransaksjon har en diff kr. " . $_lib['format']->Amount($bankvotingsum_entry) . "  " . $_lib['date']->get_first_day_in_month($bank->ThisPeriod) . " og kr. " . $_lib['format']->Amount($bankvotingsum_last) . "  " . $_lib['date']->get_last_day_in_month($bank->ThisPeriod);
       }
    ?></td>
</tr>
<? } else { ?>

<tr>
    <td class="menu"></td>
    <? if ($bank->bankvotingperiod->LockedBy) echo "<td class='menu' colspan='4'>" . $bank->bankvotingperiod->LockedAt . " l&aring;st av " . $_lib['format']->PersonIDToName($bank->bankvotingperiod->LockedBy) . "</td>"; ?>
   <? unset($_lib['form3']->Locked); ?>
    <td class="menu"><? if($_lib['sess']->get_person('AccessLevel') >= 4){ print $_lib['form3']->submit(array('name' => 'action_bank_periodunlock',   'value' => 'L&aring;s opp',  'accesskey' => 'L', 'confirm' => "Vil du virkelig l&aring;se opp perioden " . $bank->ThisPeriod)); } ?></td>
    <td class="menu"><? if($_lib['sess']->get_person('AccessLevel') >= 2){?><input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S"><? } ?></td>
   <? $_lib['form3']->Locked = $bank->bankvotingperiod->Locked; ?>
    <td class="menu" colspan="21"></td>
</tr>

<? } ?>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
