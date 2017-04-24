<?

// recieves date as string 'YYYY-MM-DD' and returns bool if date is valid
function validDate($date) {
  $d = DateTime::createFromFormat('Y-m-d', $date);
  return $d && $d->format('Y-m-d') === $date;
}

$db_table    = "voucher";

/*$VoucherType        = $_REQUEST['VoucherType'];
$AccountPlanID      = $_REQUEST['AccountPlanID'];*/

includelogic('voucher/voucherinput');

$voucher_input      = new framework_logic_voucherinput($_REQUEST);
// trim spaces on InvoiceID and KID
$voucher_input->KID = trim($voucher_input->KID);
$voucher_input->InvoiceID = trim($voucher_input->InvoiceID);

if ((!empty($_REQUEST['voucher_VoucherDate']) && !validDate($_REQUEST['voucher_VoucherDate']))) {
  $voucher_input->VoucherDate = '';
  $_lib['message']->add("Ugyldig fakturadato " . $_REQUEST['voucher_VoucherDate'] . "!");
}

// if the valuta(either currency_id or exchange_rate) has changed for the voucher we are saving, then we update foreign currency for the whole journal
global $_lib;
$result = $_lib['db']->db_query('SELECT * FROM voucher WHERE JournalID = ' . $voucher_input->JournalID . ' LIMIT 1');
$voucher_current = $_lib['db']->db_fetch_assoc($result);

// Log warning (to logapplication table) if some required fields are not set
$res = var_export($_REQUEST, true);
if ((isset($_REQUEST['action_voucher_new']) || isset($_REQUEST['action_voucher_head_update']) || isset($_REQUEST['action_voucher_update'])) && (empty($_REQUEST['VoucherType']) || empty($_REQUEST['voucher_VoucherDate']))) $_lib['sess']->warning('Possible error while saving voucherline!\n $_REQUEST:\n ' . mysql_escape_string($res));

includelogic('journal/search');
$search_class   = new search_class();

// Allow unsetting a Car
if ($voucher_input->CarID === "unset") {
    $voucher_input->CarID = DB_NULL_PLACEHOLDER;
}
// Allow unsetting a Project
if ($voucher_input->ProjectID === "unset") {
    $voucher_input->ProjectID = DB_NULL_PLACEHOLDER;
}
// Allow unsetting a Department
if ($voucher_input->DepartmentID === "unset") {
    $voucher_input->DepartmentID = DB_NULL_PLACEHOLDER;
}
#################################################################################################################
#Search fasilities
if(strlen($voucher_input->CustomerNumber) > 0)
{
    $voucher_input->DefAccountPlanID    = $search_class->search_customernumber($voucher_input->CustomerNumber);
    $voucher_input->AccountPlanID       = $voucher_input->DefAccountPlanID;
}

####################################
#Check if this account is a ledger. This information should only be checked for ledger
$hovedbokto   = $_lib['sess']->get_companydef('AccountHovedbokResultatTo');
#if($accountplan->AccountPlanID > $_lib['sess']->get_companydef('AccountHovedbokResultatTo')) {
if($voucher_input->accountplan->AccountPlanType == 'supplier' && $voucher_input->accountplan->EnableCurrency && $voucher_input->ForeignCurrencyID == '') {
    if($voucher_input->action['voucher_head_update'] || $voucher_input->action['voucher_update']) {
        $old_AccountPlanID = $_lib['db']->get_row(array('query' => "SELECT AccountPlanID FROM voucher WHERE VoucherID = $voucher_input->VoucherIDOld;"))->AccountPlanID;
    }
    if($voucher_input->action['voucher_new'] || ($voucher_input->action['voucher_head_update'] || $voucher_input->action['voucher_update']) && $old_AccountPlanID != $voucher_input->AccountPlanID) {
        $voucher_input->ForeignCurrencyID = $voucher_input->accountplan->Currency;
        $voucher_input->ForeignConvRate   = exchange::getConversionRate($voucher_input->accountplan->Currency);
    }
}

if($voucher_input->action['voucher_head_update']){
    $accounting->update_voucher_head($voucher_input);
}

#Get next available or update journal id
if($voucher_input->action['voucher_new']) { #We are saving a billag for the first time, update journal id
    $voucher_input->JournalIDOrg = $voucher_input->JournalID;

    list($voucher_input->JournalID, $tmp) = $accounting->get_next_available_journalid(array('JournalID' => $voucher_input->JournalID, 'verify' => true, 'update' => true, 'type' => $voucher_input->VoucherType, 'reuse' => true, 'from' => 'voucher_new'));

    if($voucher_input->JournalIDOrg != $voucher_input->JournalID) {
        #We just search for the used journal id, no updating
        $voucher_input->JournalID                     = $voucher_input->JournalIDOrg;
        $voucher_input->action['voucher_update']      = false;
        $voucher_input->action['voucher_new']         = false;
        $voucher_input->action['voucher_head_update'] = false;
        $voucher_input->action['voucher_head_delete'] = false;
        $voucher_input->action['voucherline_new']     = false;
        $voucher_input->action['voucher_delete']      = false;
        $_lib['message']->add(array('message' => "Bilagsnummer eksisterte fra f&oslash;r. S&oslash;kt frem bilag: $voucher_input->JournalID<br>"));
    }
    $_lib['message']->add(array('message' => $tmp));

    if($voucher_input->AccountLineID > 0) {
        $accounting->update_accountline($voucher_input->AccountLineID, $voucher_input->KID, $voucher_input->InvoiceID);
    }
}

elseif($voucher_input->new && !$_lib['input']->getProperty('action_journalid_search')
       && empty($_REQUEST['action_postmotpost_save_currency'])){ #We are just asking the next available journal id
     # Something to think about: changes to make it possible to set JournalID from bank reconciliation to get nicer series of journalids
    if($voucher_input->JournalID > 0) {
        list($voucher_input->JournalID, $tmp) = $accounting->get_next_available_journalid(array('verify' => true, 'JournalID' => $voucher_input->JournalID, 'type' => $voucher_input->VoucherType));

            $_lib['message']->add(array('message' => $tmp));
            if($voucher_input->JournalIDOrg != $voucher_input->JournalID) {
                $_lib['message']->add(array('message' => "Bilagnsummeret du pr&oslash;vde og bruke er allerede i bruk: Du har f&aring;tt tildelt et ledig nummer: $voucher_input->JournalID<br>"));
        }
    } else {
        list($voucher_input->JournalID, $tmp) = $accounting->get_next_available_journalid(array('available' => true, 'type' => $voucher_input->VoucherType, 'from' => 'input->new'));

        $_lib['message']->add(array('message' => $tmp));
    }
}
elseif($voucher_input->action['voucher_head_update'])
{ #FInd out if the JournalID is valid,
    if($voucher_input->JournalIDOrg and $voucher_input->JournalIDOrg != $voucher_input->JournalID) { #Only if somebody tries to change it

        list($voucher_input->JournalID, $tmp) = $accounting->get_next_available_journalid(array('verify' => true, 'JournalID' => $voucher_input->JournalID, 'type' => $voucher_input->VoucherType));

        $_lib['message']->add(array('message' => $tmp));
        if($voucher_input->JournalIDOrg != $voucher_input->JournalID) {
            // The journal number you tried to use is already in use: You've been given the first available number
            $_lib['message']->add(array('message' => "Bilagnsummeret du pr&oslash;vde og bruke er allerede i bruk: Du har f&aring;tt tildelt et ledig nummer: $voucher_input->JournalID<br>"));
        }
    }
}

elseif($voucher_input->action['voucher_head_delete'])
{
	$accounting->delete_journal($voucher_input->JournalID, $voucher_input->VoucherType);
	$voucher_input->setNew(1, 'voucher_head_delete');
	$voucher_input->exit    = 1;
}
elseif($voucher_input->action['voucher_delete'])
{
    $accounting->delete_voucher_line_smart($voucher_input->VoucherIDOld, $voucher_input->JournalID, $voucher_input->VoucherType);

    #this is wrong, we should not set this balance not ok, we should try to make it balance
    $accounting->set_journal_balance(array('ok' => false, 'JournalID' => $voucher_input->JournalID, 'VoucherType' => $voucher_input->VoucherType)); #Balance not ok anymore

    #################################################################################################################
    #Add vouchers so that the balance is zero
    # Sums up journal and a creates counter lines.
    $accounting->correct_journal_balance($_REQUEST, $voucher_input->JournalID, $voucher_input->VoucherType);
    $voucher_input->exit = 1;
}
elseif(!empty($_REQUEST['action_postmotpost_save_currency']))
{
    // update currency for all vouchers of this journal
    exchange::updateJournalForeignCurrency();
}

#################################################################################################################
#################################################################################################################
#Start logic
#################################################################################################################
#################################################################################################################

#Always add the person modifying the data. Should probably be split into created by and CHanged by

if($voucher_input->action['voucher_new'] and !$voucher_input->exit)
{

    $VoucherKidH = array(); #Hash that will be poulated on KID Match

    #Insert new voucher
    $voucher_input->AutomaticReason         = "Manuell";
    $voucher_input->VoucherType             = $voucher_input->VoucherType;
    $voucher_input->CreatedByID             = $_lib['sess']->login_id;
    $voucher_input->DisableAutoVat          = 0;
    $voucher_input->EnableAutoBalance       = 0;
    $voucher_input->AddedByAutoBalance      = 0;
    $voucher_input->AutomaticVatVoucherID    = '';
    $voucher_input->AutomaticBalanceID       = '';

    #print "type:" . $voucher_input->type . "<br>\n";
    if($voucher_input->type == "bank_in" or $voucher_input->type == "bank_out" or $voucher_input->type == "buycash_out" or $voucher_input->type == "buynotacash_out" or $voucher_input->type == "salecash_in" or $voucher_input->type == "salecredit_in" or $voucher_input->type == "salenotacash_in" or $voucher_input->type == "salenotacredit_in") {
        if(!strlen($voucher_input->InvoiceID) > 0) {
            $voucher_input->InvoiceID = $voucher_input->VoucherType . $voucher_input->JournalID;
        }
    }

    /****************************************************************************/
    # We have to do a KID search first so we can set the correct amount on the first voucher to be able to save the line
    $debug = false;
    if($voucher_input->KID && ($voucher_input->VoucherType == 'K' || $voucher_input->VoucherType == 'B')) {

        # The KID must be supplied, we must not have found a KID match earlier and the account plan we are accounting on must have KID enabled
        #We only do if if we have a KID, and the voucher line is not automatically updated from this KID before.
        if($debug) print "KID supplied and account plan has KID enabled<br>\n";
        if($debug) print "We are using KID reference for the upcomig searches<br>";
        if($debug) print "findOpenPostKid1<br>";
        list($status, $refH) = $accounting->postmotpost->findOpenPostKid($voucher_input->KID, $voucher_input->VoucherIDOld, $voucher_input->AccountPlanID);
        if($debug) print "Status find KID: $status<br>";
        if($status) {
            #Exactly one KID ref match, update the voucher AmountIn, AmountOut and Accountplan ID only if not updated before.
            list($KIDAccountPlanID, $KIDAmountIn, $KIDAmountOut, $KIDJournalID, $KIDVoucherID, $KIDstatus) = $accounting->postmotpost->getKIDInfo($refH); # find line that has a ledger (it has to be a  customer, supplier or employee)
            if($debug) print "Status find KID Info: $KIDstatus<br>\n";
            if($KIDstatus) {

                $VoucherKidH['voucher_JournalID']           = $voucher_input->JournalID;
                $VoucherKidH['voucher_VoucherPeriod']       = $voucher_input->VoucherPeriod;
                $VoucherKidH['voucher_VoucherDate']         = $voucher_input->VoucherDate;
                $VoucherKidH['voucher_EnableAutoBalance']   = 0;
                $VoucherKidH['voucher_AddedByAutoBalance']  = 0;
                $VoucherKidH['voucher_VoucherType']         = $voucher_input->VoucherType;
                $VoucherKidH['voucher_AccountPlanID']       = $KIDAccountPlanID;
                $VoucherKidH['voucher_AmountIn']            = $KIDAmountIn;
                $VoucherKidH['voucher_AmountOut']           = $KIDAmountOut;
                $VoucherKidH['voucher_AutoKID']             = 1; #Information updated automatically from KID information
                $VoucherKidH['voucher_KID']                 = $voucher_input->KID;
                $VoucherKidH['voucher_InvoiceID']           = $voucher_input->InvoiceID;
                $VoucherKidH['voucher_Active']              = 1;
                $VoucherKidH['voucher_AutomaticReason']     = "Automatisk fra KID: " . $voucher_input->KID;

                # Update the original voucher too, if not amounts are present it will not be inserted
                if($KIDAmountOut > 0) $voucher_input->setAmountIn($KIDAmountOut);
                if($KIDAmountIn  > 0) $voucher_input->setAmountOut($KIDAmountIn);
                #$voucher_input->KID           = $voucher_input->JournalID; #Kid should only be saved in the vouchers that should generate a match.

                # Remember to close against this match. But it will be reopened again at once. This must be checked more thouroughly. KID must be oppened on line level.
            }
        }
    }
    /****************************************************************************/
    if($debug) print "<br />\nVoucher1: $voucher_input->AccountPlanID<br>\n";
    #print_r($voucher_input->request('voucher_new'));
    $voucher_input->VoucherIDOld = $voucher_input->VoucherID = $accounting->insert_voucher_line(array('post'=>$voucher_input->request('voucher_new'), 'accountplanid'=>$voucher_input->AccountPlanID, 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'F¿rste line ny postering', 'in_or_out' => $voucher_input->InOrOut));
    if($debug) print_r($voucher_input->request('debug2'));
    #exit;
    if($KIDstatus) {
        # Update KID voucher, this should balance the journal
        if($debug) print "<br \>\nVoucher2 KID: " . $VoucherKidH['voucher_AccountPlanID'] . "<br>\n";
        #print_r($VoucherKidH);
        $VoucherKidID = $accounting->insert_voucher_line(array('post'=>$VoucherKidH, 'accountplanid'=>$VoucherKidH['voucher_AccountPlanID'], 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'KID postering'));
        #exit;
        #unset($VoucherKidH['voucher_KID']); #New test TE
        $accounting->set_journal_motkonto(array('post' => $VoucherKidH));
    }
    if($debug) print "<br \><br \>\n\n";
    /****************************************************************************/
    #unset($_REQUEST['voucher_Description']); # We are copying text on the new lines as well: TBD 2006-05-16
    $voucher_input->DescriptionID   = '';
    $voucher_KID              = $voucher_input->KID;
    $voucher_InvoiceID        = $voucher_input->InvoiceID;

    if(isset($voucher_input->autovoucher['balanse']) && !$KIDstatus)
    {
        $voucher_input->AutomaticReason     = "Automatisk motpostering";
        if($voucher_input->AmountIn > 0) $voucher_input->setAmountOut($voucher_input->AmountIn);
        if($voucher_input->AmountOut > 0) $voucher_input->setAmountIn($voucher_input->AmountOut);

        #$voucher_input->VoucherID           = $accounting->insert_voucher_line(array('post'=>$voucher_input->request(), 'accountplanid' => $voucher_input->AccountPlanID, 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'Automatisk motpostering'));
    }

    # New. Change side on everything that is automatically provided. Or should it be changed only for certain types: balance, result, ledger
    # Note: Ledger and result accountplan are accounted on the opposite side of their original debit/credit
    # Balance are accounted on the same side as their original debit/credit

    foreach($voucher_input->autovoucher as $account_type => $account)
    {
        if($account > 0 and $account_type != 'balanse' and $account_type != 'resultat')
        {
            #print "autovoucher: $account_type = $account<br>";
            $requestcopy = $voucher_input->request('autovouchercopy');

            if($voucher_input->type == "buycash_out" || $voucher_input->type == "buynotacash_out" || $voucher_input->type == "salecash_in" || $voucher_input->type == "salenotacash_in") {
                if(!preg_match("/balanse/", $account_type)) {
                    # We invert amounts for ledger and result account plan with automatic voucher
                    $tmp = $requestcopy['voucher_AmountIn'];
                    $requestcopy['voucher_AmountIn']           = $requestcopy['voucher_AmountOut'];
                    $requestcopy['voucher_AmountOut']          = $tmp;

                    #print_r($requestcopy);
                }
            }

            $tmp_account_obj = $accounting->get_accountplan_object($account);

            if($tmp_account_obj->EnableProject)     $requestcopy['voucher_ProjectID']          = $tmp_account_obj->ProjectID;
            if($tmp_account_obj->EnableDepartment)  $requestcopy['voucher_DepartmentID']       = $tmp_account_obj->DepartmentID;
            $requestcopy['voucher_AccountPlanID'] = $account;

            if($account_type == 'reskontro') {
                $requestcopy['voucher_KID']         = $voucher_KID;
                $requestcopy['voucher_InvoiceID']   = $voucher_InvoiceID;
            }
            // Automatic voucher after rule set: __
            $requestcopy['voucher_AutomaticReason'] = "Automatisk postering etter regelsett: $account_type";

            $_lib['sess']->debug("######Auto voucher loop: $account : $account_type######. AmountIn: " .  $requestcopy['voucher_AmountIn'] . ' AmountOut: ' . $requestcopy['voucher_AmountOut']);
            # From auto voucher
            $accounting->insert_voucher_line(array('post'=>$requestcopy, 'accountplanid'=>$account, 'type'=>$account_type, 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'Fra auto voucher', 'in_or_out' => $voucher_input->InOrOut));
        }
    }
}
#################################################################################################################
elseif(($voucher_input->action['voucher_update'] || $voucher_input->action['voucher_head_update']) && !$voucher_input->exit){

	#print_r($voucher_input->request('voucher_update'));

    $accounting->update_voucher_line_smart($voucher_input->request('voucher_update'), $voucher_input->VoucherIDOld, 'record', $voucher_input->InOrOut);

    #Update main ledger(hovedbok) account plan for this voucher, it may have changed
}

if(($voucher_input->action['voucher_update'] or $voucher_input->action['voucher_head_update'] or $voucher_input->action['voucher_new'] or $voucher_input->action['voucher_delete'] or $voucher_input->action['voucher_head_delete']) and !$voucher_input->exit)
{
    // The following code has to be run for all actions that potentially changes data in the voucher.
    // We must correct the balance before we update counterpart account plan. That means we have to calculate the result after all vouchers are bookkeept

    // This will run more often now (also on delete) so it will probably be more correct. Or could it have zero data for period etc?
    if($accounting->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) {
        $accounting->voucher_to_hovedbok_auto($voucher_input->AccountPlanID, $voucher_input->request('voucher_to_hovedbok_auto'), $voucher_input->VoucherIDOld);

        $accounting->delete_credit_debit_zero_lines($voucher_input->JournalID, $voucher_input->VoucherType); // This becomes wrong when we add a new line
        $accounting->correct_journal_balance($voucher_input->request('correct_journal_balance'), $voucher_input->JournalID, $voucher_input->VoucherType);

        // We only do this if the user has clicked new or update
        //#################################################################################################################
        // Automatically update counterpart account plan result and balance for the period (Always on all changes)
        $post = array();
        $post['voucher_VoucherPeriod']      = $voucher_input->VoucherPeriod;
        $accounting->set_journal_motkonto(array('post' => $post));
    }
    else {
        $_lib['message']->add("Lagring i stengt periode ble hindret");
    }
}

# If there's a ledger display a list with all open posts against this ledger (this is the account plan we have chosen on a voucher)
#print "AccountPlanID: $AccountPlanID<br>\n";

if(!empty($voucher_input->AccountPlanID) &&
   $accounting->is_reskontro($voucher_input->AccountPlanID)) {
    $accountplan = $accounting->get_accountplan_object($voucher_input->AccountPlanID);
    if($accountplan->EnablePostPost) $print_postmotpost_matches_button = true;
}

# This must be done at the end so it doesn't get removed with the lines with zero in debit and credit before
if($voucher_input->action['voucherline_new'])
{
   $fields = array();
   $fields['voucher_VoucherPeriod']     = $voucher_input->VoucherPeriod;
   $fields['voucher_VoucherDate']       = $voucher_input->VoucherDate;
   $fields['voucher_DueDate']           = $voucher_input->DueDate;
   $fields['voucher_DescriptionID']     = $voucher_input->DescriptionID;
   $fields['voucher_AccountPlanID']     = $voucher_input->AccountPlanID;
   $fields['voucher_Description']       = $voucher_input->Description;
   $fields['voucher_JournalID']         = $voucher_input->JournalID;
   $fields['voucher_VoucherType']       = $voucher_input->VoucherType;
   $fields['voucher_ForeignCurrencyID'] = $voucher_input->ForeignCurrencyID;
   $fields['voucher_ForeignConvRate']   = $voucher_input->ForeignConvRate;
   $fields['voucher_ForeignAmount']     = 0;
   $fields['voucher_AutomaticReason']   = 'Ny linje';
   $fields['voucher_Active']            = 1;
   $_lib['storage']->db_new_hash($fields,'voucher');
}
?>
