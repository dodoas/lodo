<?

function clean_org_number($orgnr) {
    return preg_replace('/\s/', '', $orgnr);
}

if(!$AccountPlanID && $AccountPlanType) {
    // If accountplan id is not given - we make a new one by max old + 1
    $query_accountplan    = "select max(AccountPlanID)+1 as AccountPlanID from accountplan where AccountPlanType = '$AccountPlanType'";
    $accountplanavailable = $_lib['storage']->get_row(array('query' => $query_accountplan));
    $AccountPlanID        = $accountplanavailable->AccountPlanID;
}

if($_lib['input']->getProperty('action_accountplan_update') || $_lib['input']->getProperty('action_accountplan_updateautomatic'))
{
    if (!empty($_POST['accountplan_OrgNumber'])) {
        $_POST['accountplan_OrgNumber'] = clean_org_number($_POST['accountplan_OrgNumber']);
    }
    
    // Check of this is a new and usaved account, if so - just create it
    $queryexist   = "select * from accountplantemplate where AccountPlanID = $AccountPlanID";
    $accountexist = $_lib['storage']->get_row(array('query' => $queryexist));
    
    if((!$accountexist || $_lib['input']->getProperty('action_accountplan_updateautomatic'))) {
        // Hvis kontoen ikke finnes fra før eller vi har bedt om en automatisk oppdatering - så kjører vi orgnummer lookup mot integrasjonspunktet
        includelogic('orgnumberlookup/orgnumberlookup');
        $org = new lodo_orgnumberlookup_orgnumberlookup();
        if(strlen($_POST['accountplantemplate_OrgNumber']) >= 9) 
            $org->getOrgNumber($_POST['accountplantemplate_OrgNumber']);
        elseif(strlen($AccountPlanID) >= 9)
            $org->getOrgNumber($_POST['accountplantemplate_AccountPlanID']);
        
        if($org->success) {
            $_lib['message']->add("Opplysninger er hentet automatisk basert p&aring; organisasjonsnummeret.");
	
            // Only update if the fields contains a value
            if($org->OrgNumber)   $_POST['accountplantemplate_OrgNumber']   = $org->OrgNumber;
            if($org->AccountName) $_POST['accountplantemplate_AccountName'] = $org->AccountName;
            if($org->Email)       $_POST['accountplantemplate_Email']       = $org->Email;
            if($org->Mobile)      $_POST['accountplantemplate_Mobile']      = $org->Mobile;
            if($org->Phone)       $_POST['accountplantemplate_Phone']       = $org->Phone;
            
            $_POST['accountplantemplate_EnableInvoiceAddress'] = 1;
            if($org->IAdress->Address1) $_POST['accountplantemplate_Address'] = $org->IAdress->Address1;
            if($org->IAdress->City)     $_POST['accountplantemplate_City']    = $org->IAdress->City;
            if($org->IAdress->ZipCode)  $_POST['accountplantemplate_ZipCode'] = $org->IAdress->ZipCode;
            
            if($org->IAdress->Country)  $_POST['accountplantemplate_CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);
            
            if($org->DomesticBankAccount) $_POST['accountplantemplate_DomesticBankAccount'] = $org->DomesticBankAccount;
            
            if($org->CreditDays) {
            	$_POST['accountplantemplate_EnableCredit'] = 1;
            	$_POST['accountplantemplate_CreditDays'] = $org->CreditDays;
            }
            if($org->MotkontoResultat1)	{
            	$_POST['accountplantemplate_EnableMotkontoResultat'] = 1;
            	$_POST['accountplantemplate_MotkontoResultat1'] = $org->MotkontoResultat1;
            }
            if($org->MotkontoResultat2)	{
            	$_POST['accountplantemplate_EnableMotkontoResultat'] = 1;
            	$_POST['accountplantemplate_MotkontoResultat2'] = $org->MotkontoResultat2;
            }
        }
    }
    
    if(!$accountexist) {
        
    	$newH = array();
    	$newH['AccountPlanID'] 	= $AccountPlanID;
    	$newH['Active'] 		= 1;
        
        $_lib['storage']->store_record(array('data' => array('AccountPlanID' => $AccountPlanID, 'Active' => 1), 'table' => 'accountplantemplate', 'debug' => false));
        $_POST['accountplantemplate_Active'] = 1;
        
        // Possible to extend or alter parameters here
        $query   = "select * from $db_table where AccountPlanID = $AccountPlanID";
        $account = $_lib['storage']->get_row(array('query' => $query));
	
        $_POST['accountplantemplate_debittext']  = "Inn";
        $_POST['accountplantemplate_credittext'] = "Ut";
        $_POST['accountplantemplate_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $_POST['accountplantemplate_InsertedDateTime']   = $_lib['sess']->get_session('Datetime');
        
        // If it is a hovedbok, enable report 1 to 5 always
        if($AccountPlanType == 'balance' || $AccountPlanType == 'result'){
            $_POST['accountplantemplate_EnableReport1'] = 1;
            $_POST['accountplantemplate_EnableReport2'] = 1;
            $_POST['accountplantemplate_EnableReport3'] = 1;
            $_POST['accountplantemplate_EnableReport4'] = 1;
            $_POST['accountplantemplate_EnableReport5'] = 1;
        }
    }

    $_POST['accountplantemplate_UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
    
    if($_POST['accountplantemplate_EnableReskontro']==1 and !isset($_POST['accountplantemplate_ReskontroAccountPlanType']))
    {
        $_lib['message']->add("Du m&aring; velge ett reskontro omr&aring;de n&aring;r du har haket av for reskontro");
    }
    
    if( (isset($_POST['accountplantemplate_EnableMotkontoResultat'])) and (!isset($_POST['accountplantemplate_MotkontoResultat1'])) )
    {
        $_lib['message']->add("Du m&aring; velge en resultatmotkonto n&aring;r du har haket av for resultatmotkonto");
    }
    
    if( (isset($_POST['accountplantemplate_EnableMotkontoBalanse'])) and (!isset($_POST['accountplantemplate_MotkontoBalanse1'])) )
    {
        $_lib['message']->add("Du m&aring; velge en balansemotkonto n�r du har haket av for balansemotkonto");
    }
    
    if($_POST['accountplantemplate_EnableReskontro'] == 1 && $_POST['accountplantemplate_ReskontroAccountPlanType'])
    {
        $query = "select AccountPlanID from accountplan where AccountPlanID!=$AccountPlanID and EnableReskontro=1 and ReskontroAccountPlanType = '" . $_POST['accountplan_ReskontroAccountPlanType'] . "'";

        $result = $_lib['db']->db_query($query);
        $rowCount = $_lib['db']->db_numrows($result);
        if($rowCount > 0)
        {
            $row = $_lib['db']->db_fetch_object($result);
            $_lib['message']->add(array('message' => "Reskontro omr&aring;det du valgte er i bruk av konto $row->AccountPlanID"));
        }
    }
    
    if( (strlen($_POST['accountplantemplate_Currency']) > 1) and ($_POST['accountplantemplate_EnableCurrency'] == 1) )
    {
        $_lib['message']->add("Du m&aring; velge valuta n&aring;r du har haket av for valuta");
    }
    
    if( ($_POST['accountplantemplate_VatID'] == 0) and ($_POST['accountplantemplate_EnableVAT'] == 1) )
    {
        $_lib['message']->add("Du m&aring; velge mva n&aring;r du har haket av for mva");
    }
    
    if($_POST['accountplantemplate_EnableVAT'] != 1)
    {
        $_POST['accountplantemplate_VatID'] = null;
    }
    
    if($_POST['accountplantemplate_EnableCurrency'] != 1)
    {
        $_POST['accountplantemplate_Currency'] = null;
    }
    
    if($_POST['accountplantemplate_EnableReskontro'] == 1) {
        // Update all reskontros with this information
        $query_ins = "update accountplantemplate set EnablePostPost=".$_POST['accountplan_EnablePostPost'].", EnableMoneyFlow=".
            $_POST['accountplan_EnableMoneyFlow']." where AccountPlanType = '".$_POST['accountplan_ReskontroAccountPlanType'] . "'";

        $_lib['db']->db_update($query_ins);
    }
    $primarykey['AccountPlanID'] = $AccountPlanID;

    $_lib['storage']->db_update_hash($_POST, $db_table, $primarykey);

}
elseif($_lib['input']->getProperty('action_accountplan_deactivate')) {
    $query = "update $db_table set Active=0 where AccountPlanID='" . $_POST['AccountPlanID'] . "'";
    $_lib['sess']->debug($query);
    $_lib['db']->db_update($query);
    $message = "Kontoen er deaktivert";
}
elseif($_lib['input']->getProperty('action_accountplan_delete')) {
    $query = "delete from $db_table where AccountPlanID='" . $_POST['AccountPlanID'] . "'";
    $_lib['sess']->debug($query);
    $_lib['db']->db_delete($query);
    $message = "Kontoen er deaktivert";
}

?>
