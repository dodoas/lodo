<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includemodel('bank/bank');
includemodel('bank/bankaccount');
includemodel('accounting/accounting');

$bankaccount    = new model_bank_bankaccount($_lib['input']->request);
$bank           = new framework_logic_bank($_lib['input']->request);
$accounting     = new accounting();

require_once "record.inc";

$bank->init(); #Read data

$bankname = $_lib['db']->db_query("SELECT AccountName FROM accountplan WHERE AccountPlanID = " . $bank->AccountPlanID);
$bankname = $_lib['db']->db_fetch_assoc($bankname);
$bankname = $bankname['AccountName'];

$_lib['form3']->Locked = $bank->bankvotingperiod->Locked;
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - avstemming av bank</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>


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
            document.getElementById('list_form').normalize();
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
        generate_kontoliste($reskontroconf);

        $resultconf['field']         = 'ResultAccountPlanID';
        $resultconf['value']         = $row->ResultAccountPlanID;
        $resultconf['type'][]        = 'hovedbok';
        generate_kontoliste($resultconf);

        $reskontroconf = null;
        $resultconf = null;
      ?>

    </script>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? print $_lib['message']->get(); ?>

<form name="template_update" name="period_choice" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>

Neste ledige Bank (B) bilagsnummer: <? print $_lib['sess']->get_companydef('VoucherBankNumber'); ?>

<table class="lodo_data">
    <tr class="result">
        <th colspan="26">
        Velg periode 

        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_prev_period($bank->ThisPeriod), 'description' => '<<', 'title' => 'Prev')) ?>
        <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_next_period($bank->ThisPeriod), 'description' => '>>', 'title' => 'Next')) ?>
    
        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Bilagsf&oslash;r/Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> <? if($_lib['sess']->get_person('FakturabankImportBankTransactionAccess')) { ?> |
        <? print $_lib['form3']->url(array('description' => 'Import fra FakturaBank',          'url' => $_lib['sess']->dispatch . 't=bank.fakturabankimport'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> <? } ?>

        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
</form>
<form id="list_form" name="template_update"  name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',    'value' => $bank->ThisPeriod)) ?>
  <tr>
    <td colspan="4">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
        <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S" tabindex="1">
    <? } ?>
    </td>
    <td>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="text" name="numnewlines" value="0" size="3" class="number">
    <? } ?>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
        <input type="submit" name="action_bank_accountlinenew" value="Nye linjer (N)" accesskey="N" tabindex="100000">
    <? } ?>
    </td>
    <td colspan="19">
    </td>
  </tr>

<tr class="red">
    <td colspan="19">
        <? if(round($bank->bankvotingperiod->topAmountSaldo,2) != round($bank->voucher->saldo,2)) { ?>
        <b>Det er differanse mellom summen av tilbakef&oslash;rte + tilleggsf&oslash;rte bilag (<? print $bank->bankvotingperiod->topAmountSaldo ?>) og summen av transaksjoner p&aring; kto <? print $bank->AccountPlanID ?> (<? print $bank->voucher->saldo ?>) : <? print round($bank->bankvotingperiod->topAmountSaldo - $bank->voucher->saldo, 2) ?></b>
        <? } ?>
        </td>
    <td colspan="6" class="sub"><b>Hovedbokskonto: <? print $bank->AccountPlanID ?>  - <?= $bankname ?></b></td>
</tr>
  <tr>
    <td class="menu">Pri</td>
    <td class="menu">Bilagsnr</td>
    <td class="menu">Dag</td>
    <td class="menu">Ut av konto</td>
    <td class="menu">Inn p&aring; konto</td>
    <td class="menu">KID</td>
    <td class="menu">Fakturanr</td>
    <td class="menu">Tekst hovedbok</td>
    <td class="menu">Kommentarer</td>
    <td class="menu">OK</td>    
    <td class="menu">Reskontro</td>
    <td class="menu">Auto</td>
    <td class="menu">Hovedbokskonto</td>
    <td class="menu">MVA</td>
    <td class="menu">Mengde</td>
    <td class="menu">Avdeling</td>
    <td class="menu">Prosjekt</td>
    <td class="menu">KID match</td>
    <td class="menu"></td>
    <td class="menu">KID</td>
    <td class="menu">Fakturanr</td>
    <td class="menu">Bilag</td>
    <td class="menu"></td>
    <td class="menu">Inn</td>
    <td class="menu">Ut</td>
    <td class="menu">Dato</td>
  </tr>
  <tr>
    <td colspan="3">Saldo<? print $bank->ThisPeriod ?>-01</td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountOut', 'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountOut),     'class' => 'number')) ?></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountIn',  'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountIn),      'class' => 'number')) ?></td>
    <td colspan="14" class="red">Saldo fra forrige mnd (<? print $bank->PrevPeriod ?>): <? print $_lib['format']->Amount($bank->prevbankaccountcalc->AmountSaldo) ?> <? if($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo != 0) { print "Saldo differanse " . $_lib['format']->Amount($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo); } ?></td>
    <td colspan="4" class="sub"></td>
    <td class="number sub"><? print $_lib['format']->Amount($bank->voucher->saldo) ?></td>
    <td colspan="5" class="sub"></td>
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
    
        if (!($i % 3)) { $sec_color = "r0"; } else { $sec_color = "r1"; };
        ?>
      <tr class="<? print $sec_color ?>">
        <td>
            <? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Priority', 'pk' => $row->AccountLineID, 'value' => $row->Priority, 'width' => 3, 'tabindex' => $tabindexH[0])); ?>
        </td>
        <td>
            <? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'JournalID', 'pk' => $row->AccountLineID, 'value' => $row->JournalID, 'width' => 6, 'tabindex' => $tabindexH[1])); ?>
        </td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Day', 'pk' => $row->AccountLineID, 'value' => $row->Day, 'class' => 'number', 'width' => 2, 'tabindex' => $tabindexH[2])) ?></td>
        <td class="<? print $bank->CreditColor ?>">
        <? 
            if($row->AmountOut > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountOut), 'class' => $row->classAmountOut, 'tabindex' => $tabindexH[3]));
            else
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountOut', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountOut, 'tabindex' => $tabindexH[3]));
        ?>
        </td>
        <td class="<? print $bank->DebitColor ?>">
            <? 
            if($row->AmountIn > 0)
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => $_lib['format']->Amount($row->AmountIn),     'class' => $row->classAmountIn, 'tabindex' => $tabindexH[4]));
            else 
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'AmountIn', 'pk' => $row->AccountLineID, 'value' => '',     'class' => $row->classAmountIn, 'tabindex' => $tabindexH[4]));
    
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=bank&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">')) ?>
        </td>
        <td <? if(count($row->MatchSelect) >= 1) { print " colspan=\"2\""; } ?>>
            <? 
            if(count($row->MatchSelect) >= 1) {
                print $_lib['form3']->select(array('table' => 'accountline', 'field' => 'KIDandInvoiceIDandAccountPlanID', 'pk' => $row->AccountLineID, 'value' => $row->KID, 'data' => $row->MatchSelect, 'width' => 50, 'required' => false)); 
            } else {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID,     'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[5]));
            }
            ?>
        </td>
        <? if(count($row->MatchSelect) < 1) { ?>
        <td>
            <? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'InvoiceNumber', 'pk' => $row->AccountLineID, 'value' => $row->InvoiceNumber,     'class' => 'number', 'width' => 22, 'tabindex' => $tabindexH[6])) ?>
        </td>
        <? } ?>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Description',     'pk' => $row->AccountLineID, 'value' => $row->Description,      'width' => 12, 'maxlength' => 255, 'tabindex' => $tabindexH[7])) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Comment',         'pk' => $row->AccountLineID, 'value' => $row->Comment,          'width' => 12, 'maxlength' => 255, 'tabindex' => $tabindexH[8])) ?></td>
        <td class="<? print $classApproved ?>"><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'Approved',     'pk' => $row->AccountLineID, 'value' => $row->Approved)) ?></td>
        <td>
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
        <td><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'AutoResultAccount',     'pk' => $row->AccountLineID, 'value' => $row->AutoResultAccount, 'title' => 'Klikk her for &aring; velge resultatkonto automatisk fra reskontro')) ?></td>
        <td>
            <?      
            $resultconf['field']         = 'ResultAccountPlanID';
            $resultconf['value']         = $row->ResultAccountPlanID;
            $resultconf['type'][]        = 'hovedbok';
	    display_kontoliste($resultconf);

            //print $_lib['form3']->accountplan_number_menu($resultconf);    // OLD
            ?>                
        </td>
        <td>
            <? 
              if(!empty($resultaccountplan) && $resultaccountplan->EnableVAT) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Vat',        'pk' => $row->AccountLineID, 'value' => (int) $row->Vat,         'width' => 2, 'maxlength' => 3));
            }
            ?>
        </td>
        <td>
            <? 
            if(!empty($resultaccountplan) && $resultaccountplan->EnableQuantity) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'ResultQuantity',        'pk' => $row->AccountLineID, 'value' => $row->ResultQuantity,         'width' => 5, 'maxlength' => 255));
            }
            ?>
        </td>
        <td><? if(!empty($resultaccountplan) && $resultaccountplan->EnableDepartment) { ?><? $_lib['form2']->department_menu2(array('table' => 'accountline', 'field' => 'DepartmentID',  'pk' => $row->AccountLineID, 'value' => $row->DepartmentID)); } ?></td>
        <td><? if(!empty($resultaccountplan) && $resultaccountplan->EnableProject)    { ?><? $_lib['form2']->project_menu2(array(   'table' => 'accountline', 'field' => 'ProjectID',     'pk' => $row->AccountLineID, 'value' => $row->ProjectID)); } ?></td>
        <td><? 
            if($row->KID || $row->InvoiceNumber) {
                if($bank->is_closeable($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber)) {
                    print "Lukket"; 
                } else { 
                    print "Diff(" . $_lib['format']->Amount($bank->getDiff($row->ReskontroAccountPlanID, $row->KID, $row->InvoiceNumber)) . ")";
                } 
            } else { 
                $sumBalance = $row->AmountIn - $row->AmountOut;
                print " Diff(" . $sumBalance . ")"; 
            } ?>
            </td>
            <td class="horiz">
                <? print $_lib['form3']->URL(array('url' => $_lib['sess']->dispatch . "t=bank.tabbankaccount&amp;action_bank_accountlinedelete=1&amp;AccountLineID=$row->AccountLineID&amp;AccountID=$bank->AccountID&amp;Period=$bank->ThisPeriod", 'description' => '<img src="/lib/icons/trash.gif">', 'title' => 'Slett')) ?>
            </td>
        <? if($bankvoucher) { ?>
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,     'class' => 'number', 'width' => 22)) ?></td>
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'InvoiceID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->InvoiceID,     'class' => 'number', 'width' => 22)) ?></td>
        <td class="sub"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub"><? if($bank->is_closeable($row->ReskontroAccountPlanID, $bankvoucher->KID, $bankvoucher->InvoiceID)) print "Lukket"; else print "Diff (" . $_lib['format']->Amount($bank->getDiff($bankvoucher->AccountPlanID, $bankvoucher->KID, $bankvoucher->InvoiceID)) . ")"; ?></td>
        <td class="<? print $bankvoucher->classAmountIn ?> <? print $bank->DebitColor ?>">
        <? if($bankvoucher->AmountIn > 0) {
            print $_lib['format']->Amount($bankvoucher->AmountIn);
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">'));
        } ?>
        </td>
        <td class="<? print $bankvoucher->classAmountOut ?> <? print $bank->CreditColor ?>">
        <? if($bankvoucher->AmountOut > 0) {
            print $_lib['format']->Amount($bankvoucher->AmountOut);
            #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountOut&amp;searchstring=' . $row->AmountOut, 'description' => '<img src="/lib/icons/search.gif">'));
        } ?>
        </td>
        <td class="sub"><? print $bankvoucher->VoucherDate ?></td>
        <? } else { ?>
        <td colspan="6" class="sub"></td>
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
        ?>
      <tr class="<? print $sec_color ?>">
        <td colspan="19"></td>
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID',           'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,       'class' => 'number', 'class' => 'number', 'width' => 22)) ?></td>
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'InvoiceID',     'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->InvoiceID, 'class' => 'number', 'class' => 'number', 'width' => 22)) ?></td>
        <td class="sub"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub"><? if($bank->is_closeable($bankvoucher->ReskontroAccountPlanID, $bankvoucher->KID, $bankvoucher->InvoiceID)) print "Lukket"?></td>
        <td class="<? print $bank->DebitColor ?>">
        <? print $_lib['format']->Amount($bankvoucher->AmountIn) ?>
        <? #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountIn&amp;searchstring=' . $row->AmountIn, 'description' => '<img src="/lib/icons/search.gif">')); ?>
        </td>
        <td class="<? print $bank->CreditColor ?>">
        <? print $_lib['format']->Amount($bankvoucher->AmountOut) ?>
        <? #print $_lib['form3']->URL(array('url' => $bank->url . '&amp;type=voucher&amp;side=AmountOut&amp;searchstring=' . $row->AmountOut, 'description' => '<img src="/lib/icons/search.gif">')); ?>
        </td>
        <td class="sub"><? print $bankvoucher->VoucherDate ?></td>
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
    <td colspan="17"></td>
    <td>Saldo</td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumSaldo) ?></td>
    <td></td>
    <td colspan="3"></td>
</tr>
<tr>
    <td class="menu"></td>
    <td class="menu" colspan="3">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
        <input type="submit" name="action_bank_zerojournalid" value="Slett bilagsnummer" accesskey="">
        <? } ?>
    </td>
    <td class="menu" colspan="9">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2 && !$bank->bankvotingperiod->Locked) { ?>
    <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S">
    <? } ?>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) {
        print $_lib['form3']->submit(array('name' => 'action_bank_periodremove', 'value' => 'Slett hele perioden',  'accesskey' => 'D', 'confirm' => "Vil du virkelig slette kontoutskriften for perioden " . $bank->ThisPeriod));
        print $_lib['form3']->submit(array('name' => 'action_bank_periodlock',   'value' => 'L&aring;s',            'accesskey' => 'L', 'confirm' => "Vil du virkelig l&aring;se perioden " . $bank->ThisPeriod));
    } ?>
    </td>
    <td class="menu" colspan="13"></td>
</tr>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
