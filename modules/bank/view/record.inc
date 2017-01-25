<?
$AccountID  = $_lib['input']->getProperty('AccountID');
$Period     = $_lib['input']->getProperty('Period');

includelogic('fakturabank/fakturabankvoting');
includelogic('fakturabank/bankreconciliationreason');

#$bank2           = new framework_logic_bank($_lib['input']->request);
#$bank2->init();
if( isset($_GET['sort_direction'])){
  $_SESSION['TabJournalSort'] = intval($_GET['sort_direction']);
  $_lib['sess']->set_mysession('TabJournalSort', $_SESSION['TabJournalSort']);
}

if($_lib['input']->getProperty('action_bank_periodremove')) {
    $bankaccount->periodremove();
}
elseif($_lib['input']->getProperty('action_bank_import_from_fakturabank')) {
    $fbvoting = new lodo_fakturabank_fakturabankvoting();
    $_SESSION['oauth_balance_account_id'] = $AccountID;
    $_SESSION['oauth_balance_period'] = $Period;
    $_SESSION['oauth_balance_country'] = $account->CountryCode;
    $fbvoting->import_transactions($AccountID, $Period, $account->CountryCode);
}
elseif($_lib['input']->getProperty('action_bank_zerojournalid')) {
    $bank->ZeroAccountLineJournalID();
}
elseif($_lib['input']->getProperty('action_bank_new')) {
  $postmain['account_ValidFrom']   = $_lib['sess']->get_session('Date');
  $postmain['account_Active']      = 1;
  $postmain['account_VoucherType'] = 'B';
  $AccountID = $_lib['db']->db_new_hash($postmain, 'account');
}
elseif($_lib['input']->getProperty('action_period_update')) {
    $_lib['storage']->update($_lib['input']->get_data());
}
elseif($_lib['input']->getProperty('action_bank_automatching')) {
    do_action('auto_matching', $AccountID, $Period);
}
elseif($_lib['input']->getProperty('action_bank_update')) {
    do_action('do_bank_update', $AccountID, $Period);
}
elseif($_lib['input']->getProperty('action_bank_votinglinenewtillegg')) {
  $Period = $_lib['input']->getProperty('Period');
  $postmain['bankvotingline_AccountID']            = $AccountID;
  $postmain['bankvotingline_VoucherDate']          = "$Period-01";
  $postmain['bankvotingline_VoucherPeriod']        = $Period;
  $postmain['bankvotingline_InsertedDateTime']     = 'NOW()';
  $postmain['bankvotingline_Type']                 = 'tillegg';
  $postmain['bankvotingline_InsertedByPersonID']   = $_lib['sess']->get_person('PersonID');
  #print_r($postmain);
  $postmain['bankvotingline_AccountLineID']        = $_lib['db']->db_new_hash($postmain, 'bankvotingline');
}
elseif($_lib['input']->getProperty('action_bank_votinglinenewtilbake')) {
  $Period = $_lib['input']->getProperty('Period');
  $postmain['bankvotingline_AccountID']            = $AccountID;
  $postmain['bankvotingline_VoucherDate']          = "$Period-01";
  $postmain['bankvotingline_VoucherPeriod']        = $Period;
  $postmain['bankvotingline_InsertedDateTime']     = 'NOW()';
  $postmain['bankvotingline_Type']                 = 'tilbake';
  $postmain['bankvotingline_InsertedByPersonID']   = $_lib['sess']->get_person('PersonID');
  #print_r($postmain);
  $postmain['bankvotingline_AccountLineID']        = $_lib['db']->db_new_hash($postmain, 'bankvotingline');
}
elseif($_lib['input']->getProperty('action_bank_accountlinedelete')) {

    $sql_delete  = "update accountline set Active=0 where AccountLineID=" . (int) $_lib['input']->getProperty('AccountLineID');
    # print "$sql_delete<br>\n";
    $_lib['db']->db_delete($sql_delete);
}
elseif($_lib['input']->getProperty('action_bank_accountlinenew')) {
    $startat = $_lib['input']->getProperty('action_bank_accountlinenew_startat');
    $bank->AddAccountLinesWithJournalID($_lib['input']->getProperty('numnewlines'), $startat);
}
elseif($_lib['input']->getProperty('action_bank_journal')) {
    $bank->init();
    $bank->journal();
}
elseif($_lib['input']->getProperty('action_bank_cardadd')) {
    $bankaccount->cardadd($_lib['input']->getProperty('AccountID'));
}
elseif($_lib['input']->getProperty('action_bank_cardremove')) {
    $bankaccount->cardremove($_lib['input']->getProperty('BankAccountCardID'));
}
elseif($_lib['input']->getProperty('action_bank_commentupdate')) {
	#print_r($_POST);
	$tables['bankvotingperiod']    = 'BankVotingPeriodID';
	$_lib['storage']->db_update_multi_table($_POST, $tables);
}
elseif($_lib['input']->getProperty('action_save_extras')) {
    $period = $_lib['input']->getProperty('Period');

    $_lib['db']->db_query(
        sprintf("DELETE FROM accountextras WHERE AccountID = %d AND Period = '%s'",
                $bank->AccountID, $bank->ThisPeriod)
        );

    $q = sprintf("
INSERT INTO accountextras
  (`AccountID`, `Period`, `BankEntryIn`, `BankEntryOut`, `BankLastIn`, `BankLastOut`, `JournalID`)
VALUES
  ('%d', '%s', '%s', '%s', '%s', '%s', '%d');
",
                 $bank->AccountID,
                 $bank->ThisPeriod,
                 $_lib['convert']->Amount($_POST['extraEntryIn']),
                 $_lib['convert']->Amount($_POST['extraEntryOut']),
                 $_lib['convert']->Amount($_POST['extraLastIn']),
                 $_lib['convert']->Amount($_POST['extraLastOut']),
                 $_POST['action_bank_accountlinenew_startat']
        );

    $_lib['db']->db_query($q);
}

$BankVotingPeriodID = $_lib['input']->getProperty('BankVotingPeriodID');
# Moved outside if elseif statement because we want to save before we unlock
if($_lib['input']->getProperty('action_bank_periodunlock')) {
    $bankaccount->periodunlock($BankVotingPeriodID);
}

# Moved outside if elseif statement because we want to save before we lock
if($_lib['input']->getProperty('action_bank_periodlock')) {
    $bankaccount->periodlock($BankVotingPeriodID);
}

function do_action($ActionName, $AccounID, $Period){
    global $_lib;

    //#Automatically update and increment JournalIDs thats missing
    $DataH = $_lib['input']->get_data();

    foreach($DataH as $server_name => $server_data) {
        foreach($server_data as $database_name => $database_data) {
            foreach($database_data as $table_name => $table_data) {
                foreach($table_data as $pk_name => $pk_data) {

                    $accountline = &$DataH[$server_name][$database_name][$table_name][$pk_name];
                    if($ActionName == 'do_bank_update'){
                        do_bank_update($table_name, $accountline, $Period);
                    } elseif ($ActionName == 'auto_matching') {
                        auto_matching($accountline, $Period, $AccountID, $pk_name);
                    } else {
                      return;
                      // We know nothing about this action
                    }
                    unset($accountline['KIDandInvoiceIDandAccountPlanID']); // Does not exist in db.
                }
            }
        }
    }
    $_lib['storage']->update($DataH);
}

function do_bank_update($table_name, &$accountline, $Period) {
    global $_lib, $accounting, $PrevJournalID;
    $debugline = 99999999999999999;

    foreach($accountline as $field => $value) {
        if($field == 'JournalID') {
            if($value > 0) {
                if($value > $PrevJournalID) {
                    $PrevJournalID = $value;
                }
            } elseif($PrevJournalID > 0) {
                $accountline[$field] = $PrevJournalID + 1;
                $PrevJournalID++;
            }
        }
    }

    $accountplan = null;
    // Speedier to have it here? Could we throw away the inner loop?
    if($table_name == 'accountline') { // We are just working on accountline?
        $CarID = $accountline['CarID'];
        $car_active_ref_date = $Period."-".$accountline['Day'];
        includelogic("car/car");
        if ($CarID != "" && !car::is_active($CarID, $car_active_ref_date)) {
            print "<div class='warning'>Linje ".$accountline['Priority'].": Bil $CarID ikke er aktiv i $car_active_ref_date, s&aring; det ble fjernet.</div>";
            $accountline['CarID'] = 0;
        }

        // Allow unsetting a reskontro
        if (!isset($accountline['ReskontroAccountPlanID'])) {
            // set value to "" to not trigger errors like:
            // get_accountplan_object(): Mangler AccountplanID
            $accountline['ReskontroAccountPlanID'] = DB_NULL_PLACEHOLDER;
        }

        // Allow unsetting a resultaccount
        if (!isset($accountline['ResultAccountPlanID'])) {
            // set value to "" to not trigger errors like:
            // get_accountplan_object(): Mangler AccountplanID
            $accountline['ResultAccountPlanID'] = DB_NULL_PLACEHOLDER;
        }

        if($accountline['Priority'] == $debugline) print_r($accountline);

        // These fields are generated from a drop down with alternatives
        if($accountline['KIDandInvoiceIDandAccountPlanID']) {
            if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "KIDandAccountPlanID<br>";
            list($KID, $InvoiceNumber, $ReskontroAccountPlanID) = explode('#', $accountline['KIDandInvoiceIDandAccountPlanID']);
            if($accountline['Priority'] == $debugline) print "Resultat fra meny: KIDandInvoiceIDandAccountPlanID<br>\n";
            #print_r($accountline['KIDandInvoiceIDandAccountPlanID']);


            if(!$accountline['ReskontroAccountPlanID']) {
                $accountline['ReskontroAccountPlanID'] = $ReskontroAccountPlanID;
            }
            if(!$accountline['KID']) {
                $accountline['KID'] = $KID;
            }
            if(!$accountline['InvoiceNumber']) {
                $accountline['InvoiceNumber']          = $InvoiceNumber;
            }
            #print_r($accountline);
        }

        // Add a zero of the day is only one char.
        if($accountline['Day'])
            $accountline['Day']  = str_pad($accountline['Day'], 2, '0', STR_PAD_LEFT);;

        if($accountline['Day'] < 1 || $accountline['Day'] > 31) {
            $accountline['Approved'] = 0;
            $_lib['message']->add('Dag ikke mellom 1 og 31 - fjerner ok flagg');
        }
        if($accountline['JournalID'] < 1) {
            $accountline['Approved'] = 0;
            $_lib['message']->add('Bilagsnummer mindre enn 0 - fjerner ok flagg');
        }

        if($accountline['Priority'] == $debugline) print_r($accountline);

        if($accountline['AutoResultAccount'] && $accountline['ReskontroAccountPlanID']) {
            if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "AutoResultAccount<br>\n";
            // Reset to the
            $reskontroaccountplan   = $accounting->get_accountplan_object($accountline['ReskontroAccountPlanID']);
            if($reskontroaccountplan->MotkontoResultat1) {
                $accountline['ResultAccountPlanID'] = $reskontroaccountplan->MotkontoResultat1;
            } else {
                $accountline['ResultAccountPlanID'] = $reskontroaccountplan->MotkontoBalanse1;
            }
        }

        if($accountline['Approved'] && !$accountline['ReskontroAccountPlanID'] && !$accountline['ResultAccountPlanID']) {
            // If no reskontro and no result accountplan - set Approved to 0
            if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "Remove Approved<br>";
            // sprint "Remove approved<br>\n";
            $accountline['Approved'] = 0;
        }

        // Used for simple bankavstemming
        // print "ReskontroAccountPlanID: " . $accountline['ReskontroAccountPlanID'] . " && ReskontroOrgNumber: " . !$accountline['ReskontroOrgNumber'] . "<br>\n";
        // Hvis Reskontrorgnummer ikke er satt og rekontrokonto er satt- så setter vi det nå (uansett)
        if($accountline['ReskontroAccountPlanID'] && !$accountline['ReskontroOrgNumber']) {
            if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "ReskontroAccountPlanID og ikke ReskontroOrgNummer<br>\n";

            $accountplan = $accounting->get_accountplan_object($accountline['ReskontroAccountPlanID']);
            $accountline['ReskontroOrgNumber']       = $accountplan->OrgNumber; #$match[0]->OrgNumber;

        } elseif($accountline['ReskontroOrgNumber'] && !$accountline['ReskontroAccountPlanID']) {
            if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "ReskontroOrgNummer og ikke ReskontroAccountPlanID<br>\n";

            // Hvis reskontro er satt og ikke reskontrokontoorgnummer er satt - så setter vi den nå
            $accountplan = $accounting->getAccountPlanFromOrgNumber($accountline['ReskontroOrgNumber']);
            $accountline['ReskontroAccountPlanID']   = $accountplan->AccountPlanID;
        } else {
            // We know nothing about this reskontro.
        }
    }
}

function auto_matching(&$accountline, $Period, $AccountID, $pk_name){
  global $_lib, $accounting;

    $PrevPeriod     = $_lib['date']->get_prev_period(array('value' => $Period . '-01', 'realPeriod' => 1));
    $PrevPrevPeriod = $_lib['date']->get_prev_period(array('value' => $PrevPeriod . '-01', 'realPeriod' => 1));
    $moneyflow  = new moneyflow(array('StartDate' =>  $PrevPrevPeriod . '-01'));

    // get fakturabank
    $fbvoting         = new lodo_fakturabank_fakturabankvoting();
    $fbreconcilationreason = new lodo_fakturabank_bankreconciliationreason();

    /* if we are missing account then look for matches in bank transaction relations */
    if(
        ($accountline['AmountIn'] > 0 || $accountline['AmountOut'] > 0) &&
        (!$accountline['ReskontroAccountPlanID'] && !$accountline['ResultAccountPlanID']))
    {
        // KID, invoicenumber or reskontro missing. Find match from fakturabank data or moneyflow

        if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "Ikke KID ikke ReskontroAccountPlanID<br>";

        if($accountline['AmountIn'] > 0) {
            $incoming = 0;
            $field = 'AmountIn';
            $value = $accountline['AmountIn'];
        } else {
            $incoming = 1;
            $field = 'AmountOut';
            $value = $accountline['AmountOut'];
        }

        // find fakturabank data
        // look for paycheck
        if ($match = $fbvoting->findpaycheckmatch(array('Amount' => $value, 'VoucherDate' => $Period . '-' . $accountline['Day'], 'BankAccountID' => $AccountID, 'Incoming' => $incoming, 'id' => $pk_name, 'accountline' => $accountline, 'period' => $Period))) {
            if(!$accountline['ReskontroAccountPlanID']) {
                $accountline['ReskontroAccountPlanID'] = $match['AccountPlanID'];
            }
        }
        elseif (
            (
                $match = $fbvoting->findinvoicematch(
                    array('Amount'        => $value,
                          'VoucherDate'   => $Period . '-' . $accountline['Day'],
                          'BankAccountID' => $AccountID,
                          'Incoming'      => $incoming,
                          'id'            => $pk_name,
                          'accountline'   => $accountline,
                          'period'        => $Period))
            )
            && !empty($match['AccountPlanID']) // only invoice matches (i.e. with orgnr)
            && !empty($match['AccountPlanOrgNumber'])
        )
        {

            if(!$accountline['ReskontroAccountPlanID']) {
                $accountline['ReskontroAccountPlanID'] = $match['AccountPlanID'];
            }
            if(!$accountline['ReskontroOrgNumber']) {
                $accountline['ReskontroOrgNumber'] = $match['AccountPlanOrgNumber'];
            }
            if(!$accountline['KID'] && !empty($match['KID'])) {
                $accountline['KID'] = $match['KID'];
            }

            if(!$accountline['InvoiceNumber'] && !empty($match['InvoiceNumber'])) {
                $accountline['InvoiceNumber'] = $match['InvoiceNumber'];
            }
        }
        else
        {
            // find possible matches on non-invoice reconcilation reasons
            $match = $fbvoting->findnoninvoicematch(array('Amount' => $value, 'VoucherDate' => $Period . '-' . $accountline['Day'], 'BankAccountID' => $AccountID, 'Incoming' => $incoming, 'id' => $pk_name, 'accountline' => $accountline, 'period' => $Period));
            $reconciliation_accountplan_id = null;
            $reconciliation_accountplan = null;
            if ($match && !empty($match['FakturabankBankTransactionAccountID'])) {
                $reconciliation_accountplan = $fbreconcilationreason->bank_reconciliation_reason_to_accountplan($match['FakturabankBankTransactionAccountID']);
            }

            if (!empty($reconciliation_accountplan)) {
                if(!$accountline['ResultAccountPlanID']) {
                    $accountline['ResultAccountPlanID'] = $reconciliation_accountplan['AccountPlanID'];
                }
            } else {
                // If no KID and no Reskontro and in or out amount - try an automatic kid match to update data
                // Always look up reskontro on kid - no we only do it if kid is missing - and on the first run.
                // print "Ikke kjør denne nå<br>\n";

                $match = $moneyflow->findmatch(array($field => $value));
                if(count($match) == 1) {
                    // Fant eksakt en match på beløp.
                    // print_r($match[0]);
                    if(!$accountline['ReskontroAccountPlanID']) {
                        $accountline['ReskontroAccountPlanID'] = $match[0]->AccountPlanID;
                    }
                    if(!$accountline['ReskontroOrgNumber']) {
                        $accountline['ReskontroOrgNumber']     = $match[0]->OrgNumber;
                    }
                    if(!$accountline['KID']) {
                        $accountline['KID']                    = $match[0]->KID;
                    }
                    if(!$accountline['InvoiceNumber']) {
                        $accountline['InvoiceNumber']          = $match[0]->InvoiceID;
                    }
                }
            }
        }
    }
    elseif($accountline['KID'] > 0 && !$accountline['ReskontroAccountPlanID']) {
        // Finn kontoplan til bankavstemminglinje basert på KID
        if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "Finn kontoplan til bankavstemminglinje basert på KID<br>\n";

        // print "Finn kid: " . $accountline['KID'] . "<br />";
        $voucher = $accounting->FindJournalWithKid($accountline['KID']);

        if(!$accountline['ReskontroAccountPlanID']) {
            $accountline['ReskontroAccountPlanID'] = $voucher->AccountPlanID;
        }
        if(!$accountline['ReskontroOrgNumber']) {
            $accountline['ReskontroOrgNumber']     = $voucher->OrgNumber;
        }
    }
    elseif($accountline['InvoiceNumber'] > 0 && !$accountline['ReskontroAccountPlanID']) {
        // print "Finn kontoplan til bankavstemminglinje basert p&aring; Fakturanummer: " . $accountline['InvoiceNumber'] . "<br>\n";

        if($accountline['Priority'] == $debugline) print "P: " . $accountline['Priority'] . "Finn kontoplan til bankavstemminglinje basert på Fakturanummer<br>\n";
        if($accountline['Priority'] == $debugline) print "Finn fakturanummer: " . $accountline['InvoiceNumber'] . "<br />";

        $voucher = $accounting->FindJournalWithInvoiceID($accountline['InvoiceNumber']);
        #print_r($voucher);
        if(!$accountline['ReskontroAccountPlanID']) {
            $accountline['ReskontroAccountPlanID'] = $voucher->AccountPlanID;
        }
        if(!$accountline['ReskontroOrgNumber']) {
            $accountline['ReskontroOrgNumber']     = $voucher->OrgNumber;
        }
    }
}
?>
