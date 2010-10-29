<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includelogic('bank/bank');
includelogic('accounting/accounting');

$accounting     = new accounting();
$bank           = new framework_logic_bank($_lib['input']->request);
//$bank->init(); #Read data

require_once "record.inc";
$bank->init(); #Read data

$warningH = array();

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

            if (text != 'Velg konto') {
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

        $reskontroconf['field']         = 'ReskontroAccountPlanID';
        $reskontroconf['value']         = $row->ReskontroAccountPlanID;
        $reskontroconf['type'][]        = 'reskontro';
        $reskontroconf['type'][]        = 'employee';
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

<form name="template_update" name="period_choice" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>

<table class="lodo_data">
    <tr class="result">
        <th colspan="18">
        Velg periode         
        
        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_prev_period($bank->ThisPeriod), 'description' => '<<', 'title' => 'Prev')) ?>
        <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
        <? print $_lib['form3']->URL(array('url' => $MY_SELF . "&amp;AccountID=$bank->AccountID&amp;Period=" . $_lib['date']->get_next_period($bank->ThisPeriod), 'description' => '>>', 'title' => 'Next')) ?>

        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Bilagsf&oslash;r/Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> |
        <? print $_lib['form3']->url(array('description' => 'Enkel',          'url' => $_lib['sess']->dispatch . 't=bank.tabsimple'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?> | 
        <? print $_lib['form3']->url(array('description' => 'Import',          'url' => $_lib['sess']->dispatch . 't=bank.import'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?>

        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
</form>

<form name="template_update"  name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID',        'value' => $bank->AccountID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',    'value' => $bank->ThisPeriod)) ?>
  
  <tr><th colspan="18">Avstemming i slutten av m&aring;neden</th></tr>
  <tr>
    <td class="menu">Pri</td>
    <td class="menu">Bilagsnr</td>
    <td class="menu">Dag</td>
    <td class="menu menu-left-border">Ut av konto</td>
    <td class="menu menu-right-border">Inn p&aring; konto</td>
    <td class="menu">KID</td>
    <td class="menu">Faktura</td>
    <td class="menu">Beskrivelse</td>
    <td class="menu">Kommentar</td>
    <td class="menu">OK</td>
    <td class="menu">Reskontro</td>
    <td class="menu">Auto</td>
    <td class="menu">Hovedbokskonto</td>
    <td class="menu">MVA</td>
    <td class="menu">Mengde</td>
    <td class="menu">Avdeling</td>
    <td class="menu">Prosjekt</td>
    <td class="menu"></td>
  </tr>
<tr>
    <td colspan="2">Saldo bank <? print $_lib['date']->get_last_day_in_month($bank->ThisPeriod) ?></td>
    <td></td>
<?php 
                                                        if ($bank->bankaccountcalc->AmountSaldo < 0) {
?>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountSaldo)  ?></td>
    <td></td>
  <?php
                                                        } else {
?>
    <td></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountSaldo)  ?></td>
  <?php
                                                        }
?>
    <td colspan="7"></td>
</tr>
  <tr>
    <th class="menu" colspan="6">Tilbakef&oslash;re - f&oslash;rt bank ikke regnskap</th>
    <td class="menu" colspan="12"></td>
  </tr>
<?
if(is_array($bank->unvotedaccount)) {
    $aconf = array();
    $aconf['table']         = 'accountline';

    foreach($bank->unvotedaccount as $row) {

        $reskontroaccountplan   = $accounting->get_accountplan_object($row->ReskontroAccountPlanID);
        $resultaccountplan      = $accounting->get_accountplan_object($row->ResultAccountPlanID);

        $aconf['pk']            = $row->AccountLineID;

        $reskontroconf = $resultconf = $aconf;
        
        if($row->Approved) {
            $classApproved = 'creditblue';
        } else {
            $classApproved = 'creditred';
        }

        #If JournalID is used, we are not allowed to journal this entry automatic
        if(!$row->ReskontroAccountPlanID && !$row->ResultAccountPlanID) {
            $warning = "Velg konto";
            $warningH['chosseaccount']++;
            $classWarning = 'creditred';

        } elseif($accounting->IsJournalIDInUse($row->JournalID, $bank->VoucherType)) {
            $warning = "Bilagsnummeret er brukt";
            $warningH['journalidused']++;
            $classWarning = 'creditred';

        } elseif(!$row->Approved) {
            $warning = "Ikke godkjent";
            $warningH['notapproved']++;
            $classWarning = 'creditred';

        } elseif($row->Day < 1 || $row->Day > 31) {
            $warning = "Ikke lovlig dato";
            $warningH['notapproved']++;
            $classWarning = 'creditred';

        } elseif($row->JournalID < 1) {
            $warning = "Ikke lovlig bilagsnummer";
            $warningH['notapproved']++;
            $classWarning = 'creditred';
            
        } else {
            $warning = $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;VoucherType=' . $bank->VoucherType . '&amp;voucher_AmountIn=' . $row->AmountIn . '&amp;voucher_AmountOut=' . $row->AmountOut . '&amp;voucher_KID=' . $row->KID . '&amp;voucher_VoucherDate=' . $row->Period . '-' . $row->Day . '&amp;AccountLineID=' . $row->AccountLineID . '&amp;JournalID=' . $row->JournalID . '&amp;voucher_Description=' . $row->Description . '&amp;new=1', 'description' => 'F&oslash;r bilag'));
            $classWarning = 'creditblue';
        }
      ?>
      <? print $_lib['form3']->hidden(array('table' => 'accountline', 'field' => 'JournalID',   'pk' => $row->AccountLineID, 'value' => $row->JournalID)) ?>
      <? print $_lib['form3']->hidden(array('table' => 'accountline', 'field' => 'Day',         'pk' => $row->AccountLineID, 'value' => $row->Day)) ?>
      <tr class="<? print "$sec_color"; ?>">
        <td><? print $row->Priority ?></td>
        <td><? print $bank->VoucherType . $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $row->JournalID . '&amp;voucher_VoucherType=' . $row->VoucherType . "&amp;action_journalid_search=1", 'description' => $row->VoucherType . $row->JournalID)) ?></td>
        <td><? print $row->Day ?></td>
        <td class="number menu-left-border"><? if($row->AmountOut > 0) print $_lib['format']->Amount($row->AmountOut); ?></td>
        <td class="number menu-right-border"><? if($row->AmountIn > 0)  print $_lib['format']->Amount($row->AmountIn); ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'KID',             'pk' => $row->AccountLineID, 'value' => $row->KID,               'class' => 'number', 'width' => 22)) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'InvoiceNumber',   'pk' => $row->AccountLineID, 'value' => $row->InvoiceNumber,     'class' => 'number', 'width' => 23)) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Description',     'pk' => $row->AccountLineID, 'value' => $row->Description,       'width' => 22, 'maxlength' => 255, 'tabindex' => $tabindexH[6])) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Comment',         'pk' => $row->AccountLineID, 'value' => $row->Comment,           'width' => 12, 'maxlength' => 255, 'tabindex' => $tabindexH[7])) ?></td>
        <td class="<? print $classApproved ?>"><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'Approved',     'pk' => $row->AccountLineID, 'value' => $row->Approved)) ?></td>
        <td>
            <?
            $reskontroconf['field']         = 'ReskontroAccountPlanID';
            $reskontroconf['value']         = $row->ReskontroAccountPlanID;
            $reskontroconf['type'][]        = 'reskontro';
            $reskontroconf['type'][]        = 'employee';
            //print $_lib['form3']->accountplan_number_menu($reskontroconf);    
            display_kontoliste($reskontroconf);
            print $reskontroaccountplan->OrgNumber;
            ?>
        </td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'AutoResultAccount',     'pk' => $row->AccountLineID, 'value' => $row->AutoResultAccount, 'title' => 'Klikk her for &aring; velge resultatkonto automatisk fra reskontro')) ?></td>
        <td>
            <?            
            $resultconf['field']         = 'ResultAccountPlanID';
            $resultconf['value']         = $row->ResultAccountPlanID;
            $resultconf['type'][]        = 'hovedbok';
            //print $_lib['form3']->accountplan_number_menu($resultconf);                
            display_kontoliste($resultconf);
            ?>                
        </td>
        <td>
            <? 
            if($resultaccountplan->EnableVAT) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Vat',        'pk' => $row->AccountLineID, 'value' => (int) $row->Vat,         'width' => 2, 'maxlength' => 3));
            }
            ?>
        </td>
        <td>
            <? 
            if($resultaccountplan->EnableQuantity) {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'ResultQuantity',        'pk' => $row->AccountLineID, 'value' => $row->ResultQuantity,         'width' => 5, 'maxlength' => 255));
            }
            ?>
        </td>
        <td><? if($resultaccountplan->EnableDepartment) { ?><? $_lib['form2']->department_menu2(array('table' => 'accountline', 'field' => 'DepartmentID',  'pk' => $row->AccountLineID, 'value' => $row->DepartmentID)); } ?></td>
        <td><? if($resultaccountplan->EnableProject)    { ?><? $_lib['form2']->project_menu2(array(   'table' => 'accountline', 'field' => 'ProjectID',     'pk' => $row->AccountLineID, 'value' => $row->ProjectID)); } ?></td>
        <td class="<? print $classWarning ?>">
            <? print $warning ?>
        </td>
      </tr>
    <? } 
}
?>
  <tr>
    <th class="menu" colspan="6">Tilleggsf&oslash;re - f&oslash;rt regnskap ikke bank</th>
    <td class="menu" colspan="12"></td>
  </tr>
<?
if(is_array($bank->unvotedvoucher)) {
    foreach($bank->unvotedvoucher as $row) {
    ?>
      <tr class="<? print $sec_color ?>">
        <td></td>
        <td><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $row->JournalID . '&amp;voucher_VoucherType=' . $row->VoucherType . "&amp;action_journalid_search=1", 'description' => $row->VoucherType . $row->JournalID)) ?></td>
        <td><? print substr($row->VoucherDate,8,2) ?></td>
    <td class="number menu-left-border"><? if ($row->AmountOut > 0) print $_lib['format']->Amount($row->AmountOut) ?></td>
        <td class="number menu-right-border"><? if ($row->AmountIn > 0) print $_lib['format']->Amount($row->AmountIn) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID',         'pk' => $row->VoucherID, 'value' => $row->KID,     'class' => 'number', 'width' => 22)) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'InvoiceID',   'pk' => $row->VoucherID, 'value' => $row->InvoiceID,     'class' => 'number', 'width' => 22)) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'Description', 'pk' => $row->VoucherID, 'value' => $row->Description,      'width' => 12, 'maxlength' => 255)) ?></td>
        <td></td>
        <td><? print $row->AccountPlanID ?></td>
        <td></td>
        <td><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'Quantity',        'pk' => $row->VoucherID, 'value' => $row->Quantity,         'width' => 12, 'maxlength' => 255)) ?></td>
        <td></td>
      </tr>
    <? }
}?>

                                                                <?php if (false) { // supress for now since it gives wrong sums
?>
<tr>
    <td class="menu" colspan="3">Sum</td>
    <td class="number menu-left-border"><? print $_lib['format']->Amount($bank->unvotedcalc->AmountIn)  ?></td>
    <td class="number menu-right-border"><? print $_lib['format']->Amount($bank->unvotedcalc->AmountOut)  ?></td>
</tr>
     <?php } ?>
<tr>
                                                                    <td class="menu" colspan="3">Saldo avstemming <? print $bank->ThisPeriod ?>-30/31</td>
    <td class="number menu-left-border"><? print $_lib['format']->Amount($bank->unvotedcalc->AmountSaldo)  ?></td>
    <td class="menu-right-border"></td>
    <td></td>
    <td colspan="2">Sum mangler konto</td>
    <td><? print $warningH['chosseaccount'] ?></td>
</tr>
<tr>
    <td class="menu" colspan="3">Saldo hovedbok <? print $bank->ThisPeriod ?>-30/31</td>
    <td class="number menu-left-border"><? print $_lib['format']->Amount($bank->voucher->sumSaldo) ?></td>
    <td class="menu-right-border"></td>
    <td></td>
    <td colspan="2">Bilag ikke godkjent</td>
    <td><? print $warningH['notapproved'] ?></td>

</tr>
<tr>
    <td class="menu" colspan="3">Diff</td>
    <td class="number menu-left-border"><? print $_lib['format']->Amount(abs($bank->unvotedcalc->AmountSaldo - $bank->voucher->sumSaldo))  ?></td>
    <td class="menu-right-border"></td>
    <td></td>
    <td colspan="2">Bilagsnummer brukt.</td>
    <td><? print $warningH['journalidused'] ?></td>

</tr>
<tr>
    <td class="menu" colspan="7"></td>
    <td class="menu" colspan="2">
    <? if($accounting->is_valid_accountperiod($bank->ThisPeriod, $_lib['sess']->get_person('AccessLevel'))) { ?>
    <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S">
    <input type="submit" name="action_bank_journal" value="Bilagsf&oslash;r (B)" accesskey="B">
    <? } else { ?>
    Perioden er stengt
    <? } ?>
    </td>
</tr>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
