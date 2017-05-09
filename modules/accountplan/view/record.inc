<?
includelogic('fakturabank/fakturabank');
includelogic("accountplan/scheme");
includelogic('workrelation/workrelation');

function validate_employee($employee) {
    global $_lib;

    if(!$employee->AccountPlanID) return null;

    $errors = array();
    if(empty($employee->FirstName)) {
        $errors[] = "Fornavn kan ikke v&aelig;re blank.";
    }

    if(empty($employee->LastName)) {
        $errors[] = "Etternavn kan ikke v&aelig;re blank.";
    }

    if(!filter_var($employee->Email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Ugyldig personlig epost.";
    }

    if(!empty($employee->SocietyNumber) && !$_lib['validation']->personal_number_birthday_match(array("society_number"=>$employee->SocietyNumber, "birth_date"=>$employee->BirthDate))) {
        $errors[] = "Personnr m&aring; stemme med f&oslash;dselsdag.";
    }

    if(empty($employee->SocietyNumber) && empty($employee->IDNumber)) {
        $errors[] = "Enten Personnr eller ID nummer p&aring; v&aelig;re der.";
    } else {
        if(!empty($employee->SocietyNumber)) {
            if(!preg_match('/^\d{11}$/', $employee->SocietyNumber)) {
                $errors[] = "Personnr m&aring; v&aelig;re 11 siffer.";
            } else if (!$_lib['validation']->mod11_personal($employee->SocietyNumber)) {
                $errors[] = "Personnr m&aring; v&aelig;re gyldig mod11.";
            }
        }

        if(!empty($employee->IDNumber)) {
            if (!preg_match('/^\d{11}$/', $employee->IDNumber)) {
                $errors[] = "ID nummer m&aring; v&aelig;re 11 siffer.";
            } else if (!$_lib['validation']->mod11_personal($employee->IDNumber)) {
                $errors[] = "ID nummer m&aring; v&aelig;re gyldig mod11.";
            }
        }
    }

    return $errors;
}

function clean_org_number($orgnr) {
    return preg_replace('/\s/', '', $orgnr);
}

function update_accountplan_field_from_scheme($field, $SchemeType) {
    global $_lib;
    $q = "UPDATE accountplan ap
            LEFT JOIN accountplanscheme aps
              ON ap.AccountPlanID = aps.AccountPlanID AND
                 aps.FakturabankSchemeID = (SELECT FakturabankSchemeID from fakturabankscheme WHERE SchemeType = '$SchemeType')
            SET ap.$field = aps.SchemeValue
            WHERE ap.AccountPlanID = '". $_POST["accountplan_AccountPlanID"] ."';";
    $_lib['db']->db_query($q);
}

// updates OrgNumber and VatNumber from firmaids
function update_orgnr_vat_from_firmaid() {
    update_accountplan_field_from_scheme("OrgNumber", "NO:ORGNR");
    update_accountplan_field_from_scheme("VatNumber", "NO:VAT");
}

function update_phone_email_from_firmaid() {
    update_accountplan_field_from_scheme("Phone", "NO:PHONE");
    update_accountplan_field_from_scheme("Email", "NO:EMAIL");
}

// Removes all the firmaids whose accountplan is missing. This is useful in cases
// when we go to create a new customer (as a side effect we immediately save two
// firmaids in database via create_new_noorgnr_and_vat() function), but we decide
// not to create it after all and don't save it.
function remove_hanging_firmaids() {
    global $_lib;
    $query = "DELETE aps.*
              FROM accountplanscheme aps
              LEFT JOIN accountplan ap ON aps.AccountPlanID = ap.AccountPlanID
              WHERE ap.AccountPlanID is null;";
    $_lib['db']->db_query($query);
}

function create_new_noorgnr_and_vat($AccountPlanID, $OrgNumber = '') {
    global $_lib;
    $query = "INSERT INTO accountplanscheme (AccountPlanID, FakturabankSchemeID, SchemeValue, CountryCode) VALUES
              ('". $AccountPlanID ."', (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:ORGNR'), '". $OrgNumber ."', 'NO'),
              ('". $AccountPlanID ."', (SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:VAT'), '', 'NO');";
    $_lib['db']->db_query($query);
}

if(!$AccountPlanID && $AccountPlanType) {
    // If accountplan id is not given - we make a new one by max old + 1
    $query_accountplan    = "select max(AccountPlanID)+1 as AccountPlanID from accountplan where AccountPlanType = '$AccountPlanType'";
    $accountplanavailable = $_lib['storage']->get_row(array('query' => $query_accountplan));
    $AccountPlanID        = $accountplanavailable->AccountPlanID;
}

if($_lib['input']->getProperty('action_accountplan_new')) {
    if (in_array($AccountPlanType, array('supplier', 'customer'))) {
        remove_hanging_firmaids();
        create_new_noorgnr_and_vat($AccountPlanID);
    }
}

if($AccountPlanID) {
    // Check of this is a new and usaved account, if so - just create it
    $queryexist   = "select * from accountplan where AccountPlanID = $AccountPlanID";
    $accountexist = $_lib['storage']->get_row(array('query' => $queryexist));
}

if($_lib['input']->getProperty('action_accountplan_update') || $_lib['input']->getProperty('action_accountplan_updateautomatic') || $_lib['input']->getProperty('action_add_scheme') || ($_lib['input']->getProperty('action_save_scheme') && !$accountexist))
{

        // Allow unsetting a Car
        if (($_POST['accountplan_CarID']) === "unset") {
            $_POST['accountplan_CarID'] = DB_NULL_PLACEHOLDER;
        }
        // Allow unsetting a Department
        if (($_POST['accountplan_DepartmentID']) === "unset") {
            $_POST['accountplan_DepartmentID'] = DB_NULL_PLACEHOLDER;
        }
        // Allow unsetting a Project
        if (($_POST['accountplan_ProjectID']) === "unset") {
            $_POST['accountplan_ProjectID'] = DB_NULL_PLACEHOLDER;
        }

    // If new firmaID is created and account doesnt exist, accountplan should be saved.
    if(!($_lib['input']->getProperty('action_add_scheme') && $accountexist)) {
        if(isset($_POST['timesheetpasswords_Password']))
        {
            $password_sql = sprintf("DELETE FROM timesheetpasswords WHERE AccountPlanID = %d", $AccountPlanID);
            $_lib['db']->db_query($password_sql);
            $password_sql = sprintf("INSERT INTO timesheetpasswords (`AccountPlanID`, `Password`)
      		VALUES ('%d', '%s');", $AccountPlanID, mysql_escape_string($_POST['timesheetpasswords_Password']));
            $_lib['db']->db_query($password_sql);
        }

        if(isset($_POST['accountplangln_GLN'])) {
            $gln_query = sprintf("DELETE FROM accountplangln WHERE AccountPlanID = %d", $AccountPlanID);
            $_lib['db']->db_query($gln_query);

            $gln_query = sprintf("INSERT INTO accountplangln (`AccountPlanID`, `GLN`) VALUES ('%d', '%s')",
                                 $AccountPlanID, mysql_escape_string($_POST['accountplangln_GLN']));
            $_lib['db']->db_query($gln_query);
        }

        if(isset($_POST['accountplanswift_SWIFT'])) {
            $swift_query = sprintf("DELETE FROM accountplanswift WHERE AccountPlanID = %d", $AccountPlanID);
            $_lib['db']->db_query($swift_query);

            $swift_query = sprintf("INSERT INTO accountplanswift (`AccountPlanID`, `Swift`, `SwiftAccount`)
                                      VALUES ('%d', '%s', '%s');",
                                   $AccountPlanID,
                                   mysql_escape_string($_POST['accountplanswift_SWIFT']),
                                   mysql_escape_string($_POST['accountplanswift_SWIFTACCOUNT']));
            $_lib['db']->db_query($swift_query);
        }


        if (!empty($_POST['accountplan_OrgNumber'])) {
            $_POST['accountplan_OrgNumber'] = clean_org_number($_POST['accountplan_OrgNumber']);
        }

        if($_lib['input']->getProperty('action_accountplan_updateautomatic')) {
          $fb = new lodo_fakturabank_fakturabank();
          if (in_array($AccountPlanType, array('supplier', 'customer'))) $fb->update_accountplan_from_fakturabank($AccountPlanID);
        }

        if(!$accountexist) {

        	$newH = array();
        	$newH['AccountPlanID'] 	= $AccountPlanID;
        	$newH['Active'] 		= 1;

            $_lib['storage']->store_record(array('data' => array('AccountPlanID' => $AccountPlanID, 'Active' => 1), 'table' => 'accountplan', 'debug' => false));
            $_POST['accountplan_Active'] = 1;
    	$_POST['accountplan_EnablePostPost'] = 1;

            // Possible to extend or alter parameters here
            $query   = "select * from $db_table where AccountPlanID = $AccountPlanID";
            $account = $_lib['storage']->get_row(array('query' => $query));

            $_POST['accountplan_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
            $_POST['accountplan_InsertedDateTime']   = $_lib['sess']->get_session('Datetime');

            if($AccountPlanType == 'employee') {
                /* Create a new salaryconfig for an employee based on the hovedmal (Id:1) */
                $query = "SELECT * FROM salaryconfline WHERE SalaryConfID = 1";
                $hovedmal = $_lib['db']->get_arrayrow(array('query' => $query));

                $query = "SELECT * FROM salaryconfline WHERE SalaryConfID = 1";
                $hovedmal = $_lib['db']->get_arrayrow(array('query' => $query));

                $query = "SELECT * FROM salaryconf where AccountPlanID = '$AccountPlanID'";
                $salaryconf = $_lib['db']->get_arrayrow(array('query' => $query));

                if (empty($salaryconf)) {
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

                            if($i++ != 0)
                                $q .= ",";

                            $q .= "'" .  $_lib['db']->db_escape($v) . "'";
                        }
                        $q .= ")";

                        $_lib['db']->db_query($q);
                    }

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

            if($AccountPlanType == 'customer' || $AccountPlanType == 'supplier' || $AccountPlanType == 'employee') {
                $query = "select AccountPlanID, EnablePostPost, EnableMoneyFlow from accountplan where EnableReskontro=1 and Active=1 and ReskontroAccountPlanType='" . $AccountPlanType . "'";
                $account = $_lib['storage']->get_row(array('query' => $query));

                $_POST['accountplan_EnablePostPost'] 	= $account->EnablePostPost;
                $_POST['accountplan_EnableMoneyFlow'] 	= $account->EnableMoneyFlow;
            }
        }

        $_POST['accountplan_UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');

        if($_POST['accountplan_EnableReskontro']==1 and !isset($_POST['accountplan_ReskontroAccountPlanType']))
        {
            // You must choose one ledger area when you checked of for ledger
            $_lib['message']->add("Du m&aring; velge ett reskontro omr&aring;de n&aring;r du har haket av for reskontro");
        }

        if( (isset($_POST['accountplan_EnableMotkontoResultat'])) and (!isset($_POST['accountplan_MotkontoResultat1'])) )
        {
            // You must choose one ledger counterpart account plan when you checked of for ledger counterpart account plan
            $_lib['message']->add("Du m&aring; velge en resultatmotkonto n&aring;r du har haket av for resultatmotkonto");
        }

        if( (isset($_POST['accountplan_EnableMotkontoBalanse'])) and (!isset($_POST['accountplan_MotkontoBalanse1'])) )
        {
            // You must choose one Balance counterpart account plan when you checked of for Balance counterpart account plan
            $_lib['message']->add("Du m&aring; velge en balansemotkonto n&aring;r du har haket av for balansemotkonto");
        }

        if($_POST['accountplan_EnableReskontro'] == 1 && $_POST['accountplan_ReskontroAccountPlanType'])
        {
            $query = "select AccountPlanID from accountplan where AccountPlanID!=$AccountPlanID and EnableReskontro=1 and ReskontroAccountPlanType = '" . $_POST['accountplan_ReskontroAccountPlanType'] . "'";

            $result = $_lib['db']->db_query($query);
            $rowCount = $_lib['db']->db_numrows($result);
            if($rowCount > 0)
            {
                $row = $_lib['db']->db_fetch_object($result);
                // The ledger you chose is in use by account plan __
                $_lib['message']->add(array('message' => "Reskontro omr&aring;det du valgte er i bruk av konto $row->AccountPlanID"));
            }
        }

        if( (strlen($_POST['accountplan_Currency']) < 1) and ($_POST['accountplan_EnableCurrency'] == 1) )
        {
            // You must choose currency when you have checked of for currency
            $_lib['message']->add("Du m&aring; velge valuta n&aring;r du har haket av for valuta");
        }

        if( ($_POST['accountplan_VatID'] == 0) and ($_POST['accountplan_EnableVAT'] == 1) )
        {
            // You must choose VAT when you have checked of for VAT
            $_lib['message']->add("Du m&aring; velge mva n&aring;r du har haket av for mva");
        }

        if($AccountPlanType == 'employee' && !$_POST['accountplan_KommuneID']) {
            // You must add Municipality
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
    $query = "delete from accountplanscheme where AccountPlanID='" . $_POST['AccountPlanID'] . "'";
    $_lib['sess']->debug($query);
    $_lib['db']->db_delete($query);
    $message = "Kontoen er deaktivert";
}
elseif($_lib['input']->getProperty('action_work_relation_delete')) {
  $workrelation_ids = $_POST['work_relations_to_delete'];
  if (!empty($workrelation_ids)) {
    $query = sprintf("DELETE FROM workrelationfurlough WHERE WorkRelationID IN (%s)", implode($workrelation_ids, ', '));
    $_lib['db']->db_query($query);
    $query = sprintf("DELETE FROM workrelation WHERE WorkRelationID IN (%s)", implode($workrelation_ids, ', '));
    $_lib['db']->db_query($query);
  }
}
elseif($_lib['input']->getProperty('action_work_relation_add')) {
  $query = "INSERT INTO workrelation(AccountPlanID) VALUES($AccountPlanID)";
  $_lib['db']->db_update($query);
}
elseif($_lib['input']->getProperty('action_work_relation_furlough_add')) {
  # We use the same checkboxs as for delete
  $workrelation_ids = $_POST['work_relations_to_delete'];

  if (!empty($workrelation_ids)) {
    $query = sprintf("INSERT INTO workrelationfurlough(WorkRelationID) VALUES (%s)", implode($workrelation_ids, '),('));
    $_lib['db']->db_query($query);
  }
}
elseif($_lib['input']->getProperty('action_work_relation_save')) {
  $tables = array();
  $tables['workrelation'] = 'WorkRelationID';
  $tables['workrelationfurlough'] = 'FurloughID';
  $_lib['storage']->db_update_multi_table($_POST, $tables);
}

if($_lib['input']->getProperty('action_add_scheme')) {
    $select_query = sprintf("SELECT * FROM accountplan WHERE AccountPlanID = %d;", $AccountPlanID);
    $AccountPlan = $_lib['db']->db_fetch_assoc($_lib['db']->db_query($select_query));
    $CountryCode = $AccountPlan["CountryCode"];
    $query = sprintf("insert into accountplanscheme (`AccountPlanID`, `FakturabankSchemeID`, `CountryCode`)
                        VALUES (%d, 0, '%s');",
                     $AccountPlanID, $CountryCode);
    $_lib['db']->db_query($query);
}
elseif($_lib['input']->getProperty('action_del_scheme')) {
    $to_delete = $_POST['schemeid_to_delete'];

    foreach($to_delete as $id) {
        $query = sprintf("delete from accountplanscheme where AccountPlanSchemeID = %d", $id);
        $_lib['db']->db_query($query);
    }
    if ($AccountPlanType == "employee") {
        update_phone_email_from_firmaid();
    } else {
        update_orgnr_vat_from_firmaid();
    }
}
elseif($_lib['input']->getProperty('action_save_scheme')) {
    $no_sup_accnt_scheme = $_lib['db']->db_fetch_assoc($_lib['db']->db_query("SELECT FakturabankSchemeID FROM fakturabankscheme WHERE SchemeType = 'NO:SUP-ACCNT-RE';"));
    foreach($_POST as $key => $value) {
      // new issue: On saving firmaID, not all of the scheme values should be formatted like this. Phone number for example can be set as '123 45 678', and wont work if formatted '12345678'
      if (strpos($key, 'accountplanscheme_SchemeValue') !== false) $_POST[$key] = str_replace(' ', '', $_POST[$key]);

      // when saving firmaID with NO:SUP-ACCNT-RE, always set the value to AccountPlanID. This is backend safety measure
      // We also need this because SchemeValue is not send due to being disabled
      if(preg_match("/accountplanscheme_FakturabankSchemeID_(\d+)/", $key, $matches) && $value == $no_sup_accnt_scheme["FakturabankSchemeID"]) {
        $_POST['accountplanscheme_SchemeValue_'. $matches[1]] = $AccountPlanID;
      }
    }
    $_lib['storage']->db_update_multi_table($_POST, array('accountplanscheme' => 'AccountPlanSchemeID'));
    if ($AccountPlanType == "employee") {
        update_phone_email_from_firmaid();
    } else {
        update_orgnr_vat_from_firmaid();
    }
    if(!$accountexist) {
        $fb = new lodo_fakturabank_fakturabank();
        if (in_array($AccountPlanType, array('supplier', 'customer'))) $fb->update_accountplan_from_fakturabank($AccountPlanID);
    }
}
elseif($_lib['input']->getProperty('action_refresh_sheme')) {
  $schemeControl = new lodo_accountplan_scheme($AccountPlanID);
  $schemeControl->refreshSchemes(lodo_accountplan_scheme::fetchSchemesFromFakturaBank());
}

?>
