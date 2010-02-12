<?
/* $Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

includelogic('bank/bank');
includelogic('accounting/accounting');

$bank           = new framework_logic_bank($_lib['input']->request);
$accounting     = new accounting();

require_once "record.inc";

$bank->init(); #Read data
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - avstemming av bank</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.36 2005/10/24 11:50:24 svenn Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="template_update" name="period_choice" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>

Neste ledige Bank (B) bilagsnummer: <? print $_lib['sess']->get_companydef('VoucherBankNumber'); ?>

<table class="lodo_data">
    <tr class="result">
        <th colspan="24">
        Velg periode <? print $_lib['form3']->AccountPeriod_menu3(array('name' => 'Period', 'value' => $bank->ThisPeriod, 'accesskey' => 'P', 'noaccess' => true, 'autosubmit' => true)); ?>
    
        <? print $_lib['form3']->url(array('description' => 'Avstemming f&oslash;rst i m&aring;neden',      'url' => $_lib['sess']->dispatch . 't=bank.tabstatus'       . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?></li> | 
        <? print $_lib['form3']->url(array('description' => 'Kontoutskrift',    'url' => $_lib['sess']->dispatch . 't=bank.tabbankaccount'  . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?></li> | 
        <? print $_lib['form3']->url(array('description' => 'Avstemming i slutten av m&aring;neden',          'url' => $_lib['sess']->dispatch . 't=bank.tabjournal'      . '&amp;AccountID=' . $bank->AccountID . '&amp;Period=' . $bank->ThisPeriod)) ?></li>

        <h2>Kasse/bank-avstemming for periode: <? print $bank->ThisPeriod ?> med bilag av type <? print $bank->VoucherType ?> p&aring; konto <? print $bank->AccountNumber ?> <? print $bank->AccountName ?></h2>
        </th>
    </tr>
</form>
<form name="template_update"  name="bankvoting" action="<? print $MY_SELF ?>" method="post">
<? print $_lib['form3']->hidden(array('name' => 'AccountID', 'value' => $bank->AccountID)) ?>
<? print $_lib['form3']->hidden(array('name' => 'Period',    'value' => $bank->ThisPeriod)) ?>
  <tr>
    <td colspan="4">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S" tabindex="1">
    <? } ?>
    </td>
    <td>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="text" name="numnewlines" value="0" size="3" class="number">
    <? } ?>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_bank_accountlinenew" value="Nye linjer (N)" accesskey="N" tabindex="10000">
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
    <td colspan="6" class="sub"><b>Hovedbokskonto: <? print $bank->AccountPlanID ?></b></td>
</tr>
  <tr>
    <td class="menu">Pri</td>
    <td class="menu">Bilagsnr</td>
    <td class="menu">Dag</td>
    <td class="menu">Ut av konto</td>
    <td class="menu">Inn p&aring; konto</td>
    <td class="menu">KID</td>
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
    <td class="menu">Bilag</td>
    <td class="menu"></td>
    <td class="menu">Inn</td>
    <td class="menu">Ut</td>
    <td class="menu">Dato</td>
  </tr>
  <tr>
    <td colspan="3">Saldo <? print $bank->ThisPeriod ?>-01</td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountOut', 'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountOut),     'class' => 'number')) ?></td>
    <td><? print $_lib['form3']->text(array('table' => 'bankvotingperiod', 'field' => 'AmountIn',  'pk' => $bank->bankvotingperiod->BankVotingPeriodID, 'value' =>$_lib['format']->Amount($bank->bankvotingperiod->AmountIn),      'class' => 'number')) ?></td>
    <td colspan="13" class="red">Saldo fra forrige mnd (<? print $bank->PrevPeriod ?>): <? print $_lib['format']->Amount($bank->prevbankaccountcalc->AmountSaldo) ?> <? if($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo != 0) { print "Saldo differanse " . $_lib['format']->Amount($bank->bankvotingperiod->AmountSaldo - $bank->prevbankaccountcalc->AmountSaldo); } ?></td>
    <td colspan="3" class="sub"></td>
    <td class="number sub"><? print $_lib['format']->Amount($bank->voucher->saldo) ?></td>
    <td colspan="5" class="sub"></td>
  </tr>

<?
##############################################################################################################################
#Main loop
$tabindex = 100; #We start at 100 to have som space in front
$count    = count($bank->bankaccount); #This is the number of records - used for tabindex.

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

        $reskontroaccountplan   = $accounting->get_accountplan_object($row->ReskontroAccountPlanID);
        $resultaccountplan      = $accounting->get_accountplan_object($row->ResultAccountPlanID);

        $aconf = array();
        $aconf['table']         = 'accountline';
        $aconf['pk']            = $row->AccountLineID;

        $reskontroconf = $resultconf = $aconf;
        $reskontroconf['from_account']  = $_lib['sess']->get_companydef('AccountEmployeeFrom');
        $reskontroconf['to_account']    = $_lib['sess']->get_companydef('AccountHovedbokReskontroTo');
        $resultconf['from_account']     = $_lib['sess']->get_companydef('AccountHovedbokBalanseFrom');
        $resultconf['to_account']       = $_lib['sess']->get_companydef('AccountHovedbokResultatTo');
    
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
        <td>
            <? 
            if(count($row->MatchSelect) >= 1) {
                print $_lib['form3']->select(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID, 'data' => $row->MatchSelect, 'width' => 30));
            } else {
                print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'KID', 'pk' => $row->AccountLineID, 'value' => $row->KID,     'class' => 'number', 'width' => 20, 'tabindex' => $tabindexH[5]));
            }
            ?>
        </td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Description',     'pk' => $row->AccountLineID, 'value' => $row->Description,      'width' => 12, 'maxlength' => 255, 'tabindex' => $tabindexH[6])) ?></td>
        <td><? print $_lib['form3']->text(array('table' => 'accountline', 'field' => 'Comment',         'pk' => $row->AccountLineID, 'value' => $row->Comment,          'width' => 12, 'maxlength' => 255, 'tabindex' => $tabindexH[7])) ?></td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'Approved',     'pk' => $row->AccountLineID, 'value' => $row->Approved)) ?></td>
        <td>
            <?
            $reskontroconf['field']         = 'ReskontroAccountPlanID';
            $reskontroconf['value']         = $row->ReskontroAccountPlanID;
            print $_lib['form3']->accountplan_number_menu($reskontroconf);    
            print $reskontroaccountplan->OrgNumber;
            ?>
        </td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'accountline', 'field' => 'AutoResultAccount',     'pk' => $row->AccountLineID, 'value' => $row->AutoResultAccount, 'title' => 'Klikk her for &aring; velge resultatkonto automatisk fra reskontro')) ?></td>
        <td>
            <?            
            $resultconf['field']         = 'ResultAccountPlanID';
            $resultconf['value']         = $row->ResultAccountPlanID;
            print $_lib['form3']->accountplan_number_menu($resultconf);                
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
        <td><? 
            if(strlen($row->KID)) { 
                if($bank->is_closeable($row->KID)) { 
                    print "Lukket"; 
                } else { 
                    print "Diff (" . round($bank->closeablevoucheraccountline[$row->KID],2) . ")";
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
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,     'class' => 'number', 'width' => 18)) ?></td>
        <td class="sub"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub"><? if($bank->is_closeable($bankvoucher->KID)) print "Lukket"; else print "Diff (" . round($bank->closeablevoucheraccountline[$bankvoucher->KID],2) . ")"; ?></td>
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
        <td colspan="14"></td>
        <td class="sub"><? print $_lib['form3']->text(array('table' => 'voucher', 'field' => 'KID', 'pk' => $bankvoucher->VoucherID, 'value' => $bankvoucher->KID,     'class' => 'number', 'class' => 'number', 'width' => 10)) ?></td>
        <td class="sub"><? print $_lib['form3']->URL(array('url' => $bank->urlvoucher . '&amp;voucher_JournalID=' . $bankvoucher->JournalID . '&amp;voucher_VoucherType=' . $bankvoucher->VoucherType . "&amp;action_journalid_search=1", 'description' => $bankvoucher->VoucherType . $bankvoucher->JournalID)) ?></td>
        <td class="sub"><? if($bank->is_closeable($bankvoucher->KID)) print "Lukket"?></td>
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
    <td colspan="14"></td>
    <td>Sum</td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumAmountIn) ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumAmountOut) ?></td>
    <td colspan="6"></td>
</tr>
<tr>
    <td colspan="3"></td>
    <td>Saldo <? print $_lib['date']->get_last_day_in_month($bank->ThisPeriod) ?></td>
    <td class="number"><? print $_lib['format']->Amount($bank->bankaccountcalc->AmountSaldo)  ?></td>
    <td colspan="14"></td>
    <td>Saldo</td>
    <td class="number"><? print $_lib['format']->Amount($bank->voucher->sumSaldo) ?></td>
    <td></td>
    <td colspan="6"></td>
</tr>
<tr>
    <td class="menu"></td>
    <td class="menu" colspan="3">
        <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_bank_zerojournalid" value="Slett bilagsnummer" accesskey="">
        <? } ?>
    </td>
    <td class="menu" colspan="9">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
    <input type="submit" name="action_bank_update" value="Lagre (S)" accesskey="S">
    <? } ?>
    </td>
    <td class="menu" colspan="11"></td>
</tr>
</table>
</form>
<? includeinc('bottom') ?>
</body>
</html>
