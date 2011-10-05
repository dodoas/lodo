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
    if(isset($_POST['timesheetpasswords_Password'])) 
    {
        $password_sql = sprintf("DELETE FROM timesheetpasswords WHERE AccountPlanID = %d", $AccountPlanID);
        $_lib['db']->db_query($password_sql);
        $password_sql = sprintf("INSERT INTO timesheetpasswords (`AccountPlanID`, `Password`)
  		VALUES ('%d', '%s');", $AccountPlanID, $_POST['timesheetpasswords_Password']);
        $_lib['db']->db_query($password_sql);
    }
    
    if(isset($_POST['fakturabankemail_Email'])) {
        $fakturabankemail_query = sprintf("DELETE FROM fakturabankemail WHERE AccountPlanID = %d", $AccountPlanID);
        $_lib['db']->db_query($fakturabankemail_query);
        
        $fakturabankemail_query = sprintf("INSERT INTO fakturabankemail (`AccountPlanID`, `Email`) 
    						VALUES('%d', '%s');",
                                          $AccountPlanID, $_POST['fakturabankemail_Email']);
        $_lib['db']->db_query($fakturabankemail_query);
    }


    if (!empty($_POST['accountplan_OrgNumber'])) {
        $_POST['accountplan_OrgNumber'] = clean_org_number($_POST['accountplan_OrgNumber']);
    }
    
    // Check of this is a new and usaved account, if so - just create it
    $queryexist   = "select * from accountplan where AccountPlanID = $AccountPlanID";
    $accountexist = $_lib['storage']->get_row(array('query' => $queryexist));
    
    if((!$accountexist || $_lib['input']->getProperty('action_accountplan_updateautomatic'))) {
        // Hvis kontoen ikke finnes fra før eller vi har bedt om en automatisk oppdatering - så kjører vi orgnummer lookup mot integrasjonspunktet
        includelogic('orgnumberlookup/orgnumberlookup');
        $org = new lodo_orgnumberlookup_orgnumberlookup();
        if(strlen($_POST['accountplan_OrgNumber']) >= 9) 
            $org->getOrgNumber($_POST['accountplan_OrgNumber']);
        elseif(strlen($AccountPlanID) >= 9)
            $org->getOrgNumber($_POST['accountplan_AccountPlanID']);
        
        if($org->success) {
            $_lib['message']->add("Opplysninger er hentet automatisk basert p&aring; organisasjonsnummeret.");
	
            // Only update if the fields contains a value
            if($org->OrgNumber)   $_POST['accountplan_OrgNumber']   = $org->OrgNumber;
            if($org->AccountName) $_POST['accountplan_AccountName'] = $org->AccountName;
            if($org->Email)       $_POST['accountplan_Email']       = $org->Email;
            if($org->Mobile)      $_POST['accountplan_Mobile']      = $org->Mobile;
            if($org->Phone)       $_POST['accountplan_Phone']       = $org->Phone;
            
            $_POST['accountplan_EnableInvoiceAddress'] = 1;
            if($org->IAdress->Address1) $_POST['accountplan_Address'] = $org->IAdress->Address1;
            if($org->IAdress->City)     $_POST['accountplan_City']    = $org->IAdress->City;
            if($org->IAdress->ZipCode)  $_POST['accountplan_ZipCode'] = $org->IAdress->ZipCode;
            
            if($org->IAdress->Country)  $_POST['accountplan_CountryCode'] = $_lib['format']->countryToCode($org->IAdress->Country);
            
            if($org->DomesticBankAccount) $_POST['accountplan_DomesticBankAccount'] = $org->DomesticBankAccount;
            
            if($org->CreditDays) {
            	$_POST['accountplan_EnableCredit'] = 1;
            	$_POST['accountplan_CreditDays'] = $org->CreditDays;
            }
            if($org->MotkontoResultat1)	{
            	$_POST['accountplan_EnableMotkontoResultat'] = 1;
            	$_POST['accountplan_MotkontoResultat1'] = $org->MotkontoResultat1;
            }
            if($org->MotkontoResultat2)	{
            	$_POST['accountplan_EnableMotkontoResultat'] = 1;
            	$_POST['accountplan_MotkontoResultat2'] = $org->MotkontoResultat2;
            }
        }
    }
    
    if(!$accountexist) {
        
    	$newH = array();
    	$newH['AccountPlanID'] 	= $AccountPlanID;
    	$newH['Active'] 		= 1;
        
        $_lib['storage']->store_record(array('data' => array('AccountPlanID' => $AccountPlanID, 'Active' => 1), 'table' => 'accountplan', 'debug' => false));
        $_POST['accountplan_Active'] = 1;
        
        // Possible to extend or alter parameters here
        $query   = "select * from $db_table where AccountPlanID = $AccountPlanID";
        $account = $_lib['storage']->get_row(array('query' => $query));
	
        $_POST['accountplan_debittext']  = "Inn";
        $_POST['accountplan_credittext'] = "Ut";
        $_POST['accountplan_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $_POST['accountplan_InsertedDateTime']   = $_lib['sess']->get_session('Datetime');
        
        if($AccountPlanType == 'employee') {
            /* Create a new salaryconfig for an employee based on the hovedmal (Id:1) */
            $query = "SELECT * FROM salaryconfline WHERE SalaryConfID = 1";
            $hovedmal = $_lib['db']->get_arrayrow(array('query' => $query));
            
            $query = "INSERT INTO salaryconf VALUES (NULL, $AccountPlanID, 0, NULL); ";
            $id = $_lib['db']->db_insert($query);
            
            foreach($hovedmal as $line) {
                $q = "INSERT INTO salaryconfline VALUES (";
                $i = 0;
                foreach($line as $k => $v) {
                    if($k == "SalaryConfLineID") {
                        $v = "NULL";
                    } 
                    else if($k == "SalaryConfID") {
                        $v = $id;
                    }
                    else if($k == "Active") {
                        $v = '0';
                    }
                    
                    if($i++ != 0) 
                        $q .= ",";
                    
                    $q .= "'" .  $_lib['db']->db_escape($v) . "'";
                }
                $q .= ")";
                
                $_lib['db']->db_query($q);
            }
        }

        // If it is a hovedbok, enable report 1 to 5 always
        if($AccountPlanType == 'balance' || $AccountPlanType == 'result'){
            $_POST['accountplan_EnableReport1'] = 1;
            $_POST['accountplan_EnableReport2'] = 1;
            $_POST['accountplan_EnableReport3'] = 1;
            $_POST['accountplan_EnableReport4'] = 1;
            $_POST['accountplan_EnableReport5'] = 1;
        }
        if($AccountPlanType == 'customer' || $AccountPlanType == 'supplier') {
            $query = "select AccountPlanID, EnablePostPost, EnableMoneyFlow from accountplan where EnableReskontro=1 and Active=1 and ReskontroAccountPlanType='" . $AccountPlanType . "'";
            $account = $_lib['storage']->get_row(array('query' => $query));

            $_POST['accountplan_EnablePostPost'] 	= $account->EnablePostPost;
            $_POST['accountplan_EnableMoneyFlow'] 	= $account->EnableMoneyFlow;
        }
    }

    $_POST['accountplan_UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
    
    if($_POST['accountplan_EnableReskontro']==1 and !isset($_POST['accountplan_ReskontroAccountPlanType']))
    {
        $_lib['message']->add("Du m&aring; velge ett reskontro omr&aring;de n&aring;r du har haket av for reskontro");
    }
    
    if( (isset($_POST['accountplan_EnableMotkontoResultat'])) and (!isset($_POST['accountplan_MotkontoResultat1'])) )
    {
        $_lib['message']->add("Du m&aring; velge en resultatmotkonto n&aring;r du har haket av for resultatmotkonto");
    }
    
    if( (isset($_POST['accountplan_EnableMotkontoBalanse'])) and (!isset($_POST['accountplan_MotkontoBalanse1'])) )
    {
        $_lib['message']->add("Du m&aring; velge en balansemotkonto n�r du har haket av for balansemotkonto");
    }
    
    if($_POST['accountplan_EnableReskontro'] == 1 && $_POST['accountplan_ReskontroAccountPlanType'])
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
    
    if( (strlen($_POST['accountplan_Currency']) > 1) and ($_POST['accountplan_EnableCurrency'] == 1) )
    {
        $_lib['message']->add("Du m&aring; velge valuta n&aring;r du har haket av for valuta");
    }
    
    if( ($_POST['accountplan_VatID'] == 0) and ($_POST['accountplan_EnableVAT'] == 1) )
    {
        $_lib['message']->add("Du m&aring; velge mva n&aring;r du har haket av for mva");
    }
    
    if($AccountPlanType == 'employee' && !$_POST['accountplan_KommuneID']) {
        $_lib['message']->add("Du m&aring; legge inn kommune");
    }
    
    if($_POST['accountplan_EnableVAT'] != 1)
    {
        $_POST['accountplan_VatID'] = null;
    }
    
    if($_POST['accountplan_EnableCurrency'] != 1)
    {
        $_POST['accountplan_Currency'] = null;
    }
    
    if($_POST['accountplan_EnableReskontro'] == 1) {
        // Update all reskontros with this information
        $query_ins = "update accountplan set EnablePostPost=".$_POST['accountplan_EnablePostPost'].", EnableMoneyFlow=".
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
