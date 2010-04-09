<?
$db_table    = "voucher";
#print_r($_REQUEST);

/*$VoucherType        = $_REQUEST['VoucherType'];
$AccountPlanID      = $_REQUEST['AccountPlanID'];*/

#print_r($_REQUEST);
#print "<br>\n";

includelogic('voucher/voucherinput');
$voucher_input      = new framework_logic_voucherinput($_REQUEST);

includelogic('journal/search');
$search_class   = new search_class();

#print_r($voucher_input);

#print_r($voucher_input->action);

#print "VoucherType6: $VoucherType<br>\n";
#################################################################################################################
#Search fasilities
if(strlen($voucher_input->CustomerNumber) > 0)
{
    //print "1";
    $voucher_input->DefAccountPlanID    = $search_class->search_customernumber($voucher_input->CustomerNumber);
    $voucher_input->AccountPlanID       = $voucher_input->DefAccountPlanID;
    $_showresult                        = $search_class->search_openpost_accountplan(array('AccountPlanID' => $voucher_input->DefAccountPlanID, 'EnableSingleChoose' => true, 'VoucherID' => $voucher_input->VoucherIDOld, 'From' => 'CustomerNumber'));
}
elseif(strlen($voucher_input->Amount) > 0)
{
    //print "2";
    $_showresult                        = $search_class->search_openpost_accountplan(array('Amount' => $voucher_input->Amount, 'From' => 'Amount'));
}

####################################
#Check if this account is a reskontro. This information should only be checked for reskontro
$hovedbokto   = $_lib['sess']->get_companydef('AccountHovedbokResultatTo');
#if($accountplan->AccountPlanID > $_lib['sess']->get_companydef('AccountHovedbokResultatTo')) {

if($voucher_input->action['voucher_head_update']){
    $accounting->update_voucher_head($voucher_input);
}

#print_r($voucher_input->action);
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

elseif($voucher_input->new && !$_lib['input']->getProperty('action_journalid_search')){ #We are just asking the next available journal id
    #print "Finn billag";
   
     #TE changes to make it possible to set JournalID from bank avstemming to get nicer series of journalids
    if($voucher_input->JournalID > 0) {
        list($voucher_input->JournalID, $tmp) = $accounting->get_next_available_journalid(array('verify' => true, 'JournalID' => $voucher_input->JournalID, 'type' => $voucher_input->VoucherType));

            $_lib['message']->add(array('message' => $tmp));
            if($voucher_input->JournalIDOrg != $voucher_input->JournalID) {
                $_lib['message']->add(array('message' => "Bilagnsummeret du pr&oslash;vde og bruke er allerede i bruk: Du har fï¿½tt tildelt et ledig nummwer: $voucher_input->JournalID<br>"));
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
            $_lib['message']->add(array('message' => "Bilagnsummeret du pr&oslash;vde og bruke er allerede i bruk: Du har fï¿½tt tildelt et ledig nummwer: $voucher_input->JournalID<br>"));
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
    #print "Sletter linje 3<br>\n";
    $accounting->delete_voucher_line_smart($voucher_input->VoucherIDOld, $voucher_input->JournalID, $voucher_input->VoucherType);

    #this is wrong, we should not set this balance not ok, we should try to make it balance
    $accounting->set_journal_balance(array('ok' => false, 'JournalID' => $voucher_input->JournalID, 'VoucherType' => $voucher_input->VoucherType)); #Balance not ok anymore

    #################################################################################################################
    #Add vouchers so that the balance is zero
    #summerer bilag og evnt oppretter motposteringer.
    $accounting->correct_journal_balance($_REQUEST, $voucher_input->JournalID, $voucher_input->VoucherType);
    $voucher_input->exit = 1;
}


#################################################################################################################
#################################################################################################################
#Start logic
#################################################################################################################
#################################################################################################################

#Always add the person modifying the data. Should probably be split into created by and CHanged by

if($voucher_input->action['voucher_new'] and !$voucher_input->exit)
{
    #print "<h1>kk1</h2>";

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
            #print "Setter: JournalID til reference<br>\n";
            $voucher_input->InvoiceID = $voucher_input->VoucherType . $voucher_input->JournalID;
        }
    }

    /****************************************************************************/
    # Vi mï¿½ kjï¿½re kid oppslag fï¿½rst for ï¿½ kunne sette belï¿½p ol. rikitg i den fï¿½rste posteringen for ï¿½ fï¿½ lov til ï¿½ lagre linjen
    $debug = false;
    if($voucher_input->KID && ($voucher_input->VoucherType == 'K' || $voucher_input->VoucherType == 'B')) {
        #print "Opprett neste linje automatisk basert pï¿½ kid referanse<br>\n";

        #Det mï¿½ vï¿½re en kid oppgitt, vi mï¿½ ikke ha funnet kid match tidligere og kontoen vi fï¿½rer pï¿½ mï¿½ ha kid stï¿½tte pï¿½skrudd
        #We only do if if we have a KID, and the voucher line is not automatically updated from this KID before.
        if($debug) print "Kid oppgitt og kontoen stï¿½tter kid<br>\n";
        if($debug) print "Vi bruker KID referansen for videre oppslag<br>";
        if($debug) print "findOpenPostKid1<br>";
        list($status, $refH) = $accounting->postmotpost->findOpenPostKid($voucher_input->KID, $voucher_input->VoucherIDOld, $voucher_input->AccountPlanID);
        if($debug) print "Status finn KID: $status<br>";
        if($status) {
            #Exactly one KID ref match, update the voucher AmountIn, AmountOut and Accountplan ID only if not updated before.
            list($KIDAccountPlanID, $KIDAmountIn, $KIDAmountOut, $KIDJournalID, $KIDVoucherID, $KIDstatus) = $accounting->postmotpost->getKIDInfo($refH); #finn linje som har en reskontro (det mï¿½ vï¿½re kunde)
            if($debug) print "Status finn KIDInfo: $KIDstatus<br>\n";
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

                #Update the original voucher to, if not amouns are presen it will not be inserted
                if($KIDAmountOut > 0) $voucher_input->setAmountIn($KIDAmountOut);
                if($KIDAmountIn  > 0)  $voucher_input->setAmountOut($KIDAmountIn);
                #$voucher_input->KID           = $voucher_input->JournalID; #Kid should only be saved in the vouchers that should generate a match.

                #Husk ï¿½ lukke mot denne matchen. Men den vil bli ï¿½pnet igjen umiddelbart. Dette mï¿½ nok sjekkes grundigere. Kid mï¿½ ï¿½pnes bare pï¿½ linjenivï¿½.
            }
        }
    }
    /****************************************************************************/
    if($debug) print "<br />\nPostering1: $voucher_input->AccountPlanID<br>\n";
    #print_r($voucher_input->request('voucher_new'));
    $voucher_input->VoucherIDOld = $voucher_input->VoucherID = $accounting->insert_voucher_line(array('post'=>$voucher_input->request('voucher_new'), 'accountplanid'=>$voucher_input->AccountPlanID, 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'F�rste line ny postering'));
    if($debug) print_r($voucher_input->request('debug2'));
    #exit;
    if($KIDstatus) {
        #Update kid postering, this shoudl balance the billag
        if($debug) print "<br \>\nPostering2 KID: " . $VoucherKidH['voucher_AccountPlanID'] . "<br>\n";
        #print_r($VoucherKidH);
        $VoucherKidID = $accounting->insert_voucher_line(array('post'=>$VoucherKidH, 'accountplanid'=>$VoucherKidH['voucher_AccountPlanID'], 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'KID postering'));
        #exit;
        #unset($VoucherKidH['voucher_KID']); #New test TE
        $accounting->set_journal_motkonto(array('post' => $VoucherKidH));
    }
    if($debug) print "<br \><br \>\n\n";
    /****************************************************************************/
    #unset($_REQUEST['voucher_Description']); #Vi kopierer med tekst p� de nye linjene ogs�: TE 2006-05-16
    $voucher_input->DescriptionID   = '';
    $voucher_KID              = $voucher_input->KID;
    $voucher_InvoiceID        = $voucher_input->InvoiceID;
    #$voucher_input->Reference       = ''; #New test TE. Removed to copy kid 2006-05-16

    if(isset($voucher_input->autovoucher['balanse']) && !$KIDstatus)
    {
        $voucher_input->AutomaticReason     = "Automatisk motpostering";
        if($voucher_input->AmountIn > 0) $voucher_input->setAmountOut($voucher_input->AmountIn);
        if($voucher_input->AmountOut > 0) $voucher_input->setAmountIn($voucher_input->AmountOut);
        #print "Bel�p endret<br>\n";
        #print_r($voucher_input);
        #$voucher_input->AddedByAutoBalance  = 1; #Kan den gjentas? Nei. TE 2006-06-01
        #print "Postering3: Automatisk motpostering: $voucher_input->AccountPlanID (kommentert ut) HUSK<br>\n";

        #$voucher_input->VoucherID           = $accounting->insert_voucher_line(array('post'=>$voucher_input->request(), 'accountplanid' => $voucher_input->AccountPlanID, 'type'=>'first', 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'Automatisk motpostering'));
    }

    #Nytt. Bytt side p� alt som blir auto postert. Eller skal det bare bli snudd for visse tuper: balanse, resultat, reskontro
    # Merk: Reskontro og resultatkontoer f�res p� motsatt side av opprinnelig debit/kredit
    # Balanse f�res p� samme side som opprinnelig debit/credit

    #################################################################################################################
    #Legg til nnye posteringer automatisk

    #print "Autocoucher<br>\n";
    #print_r($voucher_input->autovoucher);
    foreach($voucher_input->autovoucher as $account_type => $account)
    {
        if($account > 0 and $account_type != 'balanse' and $account_type != 'resultat')
        {
            #print "autovoucher: $account_type = $account<br>";
            $requestcopy = $voucher_input->request('autovouchercopy');

            if($voucher_input->type == "buycash_out" || $voucher_input->type == "buynotacash_out" || $voucher_input->type == "salecash_in" || $voucher_input->type == "salenotacash_in") {
                if(!preg_match("/balanse/", $account_type)) {
                    #Vi snur bel�pene for reskontro og resultatkonto ved automatisk f�ring
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
            $requestcopy['voucher_AutomaticReason'] = "Automatisk postering etter regelsett: $account_type";

            $_lib['sess']->debug("######Autopostering loop: $account : $account_type######. AmountIn: " .  $requestcopy['voucher_AmountIn'] . ' AmountOut: ' . $requestcopy['voucher_AmountOut']);
            #print "<br>Postering4: $account<br>\n";
            #print_r($requestcopy);
            $accounting->insert_voucher_line(array('post'=>$requestcopy, 'accountplanid'=>$account, 'type'=>$account_type, 'VoucherType'=>$voucher_input->VoucherType, 'comment' => 'Fra auto voucher'));
        }
    }
}
#################################################################################################################
elseif(($voucher_input->action['voucher_update'] || $voucher_input->action['voucher_head_update']) && !$voucher_input->exit){
    
	#print_r($voucher_input->request('voucher_update'));
	$accounting->update_voucher_line_smart($voucher_input->request('voucher_update'), $voucher_input->VoucherIDOld, 'record');

    #print "<h2>Test2</h2>";
    #Update hovedbokskonto for this postering, it may have changed
}

if(($voucher_input->action['voucher_update'] or $voucher_input->action['voucher_head_update'] or $voucher_input->action['voucher_new'] or $voucher_input->action['voucher_delete'] or $voucher_input->action['voucher_head_delete']) and !$voucher_input->exit)
{
	#The following code has to be run for all actions that potentially changes data in the voucher.
    #Vi m� korrigere balanse f�r vi oppdaterer motkonto-. Dvs vi m� beregne resultatet etter at alle posteringer er postert

	#This will run more often now (also on delete) so it will probably be more correct. Or could it have zero data for period etc?
    #FEIL HER?
    $accounting->voucher_to_hovedbok_auto($voucher_input->AccountPlanID, $voucher_input->request('voucher_to_hovedbok_auto'), $voucher_input->VoucherIDOld);

    $accounting->delete_credit_debit_zero_lines($voucher_input->JournalID, $voucher_input->VoucherType); #Dette blir feil bla n�r vi legger til ny linje
    $accounting->correct_journal_balance($voucher_input->request('correct_journal_balance'), $voucher_input->JournalID, $voucher_input->VoucherType);

    #We only do this if the user has clicked new or update
    #################################################################################################################
    # Automatically update motkonto resultat og balanse for perioden (Always on all changes)
    $post = array();
    $post['voucher_VoucherPeriod']      = $voucher_input->VoucherPeriod;
    $accounting->set_journal_motkonto(array('post' => $post));
}

//print_r($_REQUEST);
#Hvis det er en reskontro sï¿½ vis en liste med alle ï¿½pne poster mot denne reskontroen (dette er den kontoen  man har valgt pï¿½ en posteringslinje
#print "AccountPlanID: $AccountPlanID<br>\n";

if(!empty($voucher_input->AccountPlanID) && 
   $accounting->is_reskontro($voucher_input->AccountPlanID)) {
    $accountplan = $accounting->get_accountplan_object($voucher_input->AccountPlanID);
    if($accountplan->EnablePostPost)
    {
        //print "3";
        $_showresult        = $search_class->search_openpost_accountplan(array('AccountPlanID' => $voucher_input->AccountPlanID, 'VoucherID'=>$voucher_input->VoucherIDOld, 'JournalID'=>$voucher_input->JournalID, 'VoucherType'=>$voucher_input->VoucherType, 'EnableSingleChoose'=>'1', 'type'=>$voucher_input->type, 'From' => 'EnablePostMotPost'));
    }
    else
    {
        #Vi viser ikke liste over sï¿½keresultat pï¿½ andre kontoer enn de som har KID definert.
    }
}

#Dette m� gj�res til slutt for at ikke linjer med null i debit og credit skal slettes
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
   $fields['voucher_AutomaticReason']   = 'Ny linje';
   $fields['voucher_Active']            = 1;
   #print_r($fields); 
   $_lib['storage']->db_new_hash($fields,'voucher');
}
?>