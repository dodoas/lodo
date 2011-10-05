<?
//includelogic('salary/salaryreport');
includelogic('exchange/exchange');

class lodo_fakturabank_fakturabanksalary {
    private $host           = '';
    private $protocol       = '';
    private $username       = '';
    private $password       = '';
    private $login          = false;
    private $timeout        = 30; 
    private $credentials    = '';
    private $OrgNumber      = '';
    private $ArrayTag       = array(
                                    'import-paychecks-result' => true,
                                 'paycheck-results'          => true,
                                 'paycheck'  => true,
                                );


    function __construct() {
        global $_lib;

        $this->username         = $_lib['sess']->get_person('FakturabankUsername');
        $this->password         = $_lib['sess']->get_person('FakturabankPassword');

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];

        if(is_array($args)) {
            foreach($args as $key => $value) {
                $this->{$key} = $value;
            }
        }

        if(!$this->username || !$this->username) {
            $_lib['message']->add("Fakturabank brukernavn og passord er ikke definert p&aring; brukeren din");
        } else {
            $this->login = true;
        }

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $this->OrgNumber = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber'))); 

        $this->credentials = "$this->username:$this->password";
    }
    
    
    
    ################################################################################################
    public function createSalaryXML($SalaryID, $SalaryConfID) {
        #########################################
        #This should be placed under firmaoppsett
        $lineInFrom  =  10;
        $lineInTo    =  69;
        $lineOutFrom =  70;
        $lineOutTo   = 100;


        global $_lib;

        $query_head     = "select S.*, F.Email AS FakturabankEmail, A.AccountName, A.Address, A.City, A.ZipCode, A.SocietyNumber, A.TabellTrekk, A.ProsentTrekk, A.Email, A.Address, A.ZipCode, A.LastName, A.FirstName, A.City, A.CountryCode, A.Phone, A.Mobile, A.DomesticBankAccount from salary as S, accountplan as A, fakturabankemail as F  where S.SalaryID='$SalaryID' and S.AccountPlanID=A.AccountPlanID and F.AccountPlanID = S.AccountPlanID and A.AccountPlanID = F.AccountPlanID";
        #print "$query_head<br>";
        $result_head    = $_lib['db']->db_query($query_head);
        $head           = $_lib['db']->db_fetch_object($result_head);

        $accountplan_edit_url = "/lodo.php?view_mvalines=&view_linedetails=&t=accountplan.employee&accountplan_AccountPlanID=" . $head->AccountPlanID;

        if (empty($head))  {
            $_lib['message']->add("Du maring; sette den ansattes person.fakturabank.no-adresse <a href=\"$accountplan_edit_url\">her</a> for &aring; kunne laste opp l&oslash;nnslipper.");
            echo "Du m&aring; sette den ansattes person.fakturabank.no-adresse <a href=\"$accountplan_edit_url\">her</a> for &aring; kunne laste opp l&oslash;nnslipper.<br>";
        
            return false;
        }

        if (empty($head->FirstName) || empty($head->LastName)) {
            $_lib['message']->add("Du maring; sette den ansattes fornavn og etternavn <a href=\"$accountplan_edit_url\">her</a> for &aring; kunne laste opp l&oslash;nnslipper.");
            echo "Du maring; sette den ansattes fornavn og etternavn <a href=\"$accountplan_edit_url\">her</a> for &aring; kunne laste opp l&oslash;nnslipper.<br>";

            return false;
        }

        //http://locallodo.no/lodo.php?view_mvalines=&view_linedetails=&t=accountplan.employee&accountplan_AccountPlanID=10000

        $query_salary   = "select * from salaryline where SalaryID = '$SalaryID' order by LineNumber asc";
        $result_salary  = $_lib['db']->db_query($query_salary);

        $project_query = "select * from project";
        $project_result = $_lib['db']->db_query($project_query);
        $projects = array();
        while( $project_line = $_lib['db']->db_fetch_assoc($project_result))
            $projects[ $project_line['ProjectID'] ] = $project_line['Heading'];
        $projects[0] = "";

        $xml_prefix = "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . ">\n<paycheck_messages>\n<paycheck>\n";
        $xml_postfix = "</paycheck>\n</paycheck_messages>\n";

        $xml_content = "";

        $xml_content .= "<from_date>" . $head->ValidFrom . "</from_date>\n";
        $xml_content .= "<to_date>" . $head->ValidTo . "</to_date>\n";
		$xml_content .= "<document_number>L" . $head->JournalID . "</document_number>\n";
		$xml_content .= "<document_date>" . $head->JournalDate . "</document_date>\n";
		$xml_content .= "<period>" . $head->Period . "</period>\n";
        $xml_content .= "<currency_code>" . exchange::getLocalCurrency() . "</currency_code>\n";

        // process paycheck lines

        $xml_paycheck_lines = "";

        $sumTotal = 0;
        $sumTotalYear = 0;
        $sumVacation = 0; // this variable and it's calculation seems to be deprecated, another calculation is done below
        $last_line_value = "sdafsadfsadfsadfsdafasdsda"; // dummy non matching value
        while($line = $_lib['db']->db_fetch_object($result_salary))
        {
            $xml_paycheck_lines .= "<paycheck_line>\n";
            
            $firstPeriod = $_lib['date']->get_this_year($head->Period)."-01";
            $query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.AccountPlanID=$head->AccountPlanID and S.Period>='$firstPeriod' and S.Period<='$head->Period' and SL.LineNumber=$line->LineNumber and SL.AccountPlanID=$line->AccountPlanID";
            $totalThisYear = $_lib['storage']->get_row(array('query' => $query));
                
            if($line->LineNumber >= $lineInFrom and $line->LineNumber <= $lineInTo)
            {
                $sumTotal += $line->AmountThisPeriod;
                if ($line->LineNumber != $forige_linje)
                    $sumTotalYear += $totalThisYear->total;
            }
            elseif($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
            {
                $sumTotal -= $line->AmountThisPeriod;
                $sumTotalYear -= $totalThisYear->total;
            }
            if($line->EnableVacationPayment == 1)
            {
                $sumVacation += ($line->VacationPayment / 100) * $line->AmountThisPeriod;
            }



            $xml_paycheck_lines .= "<code>" . $line->LineNumber . "</code>\n";
            $xml_paycheck_lines .= "<description>" . htmlentities($line->SalaryText) . "</description>\n";
            $xml_paycheck_lines .= "<work_amount>" . $line->NumberInPeriod . "</work_amount>\n";

            // leave work_amount_unit blank since the system does not yet specify such
            $xml_paycheck_lines .= "<work_amount_unit />\n";
            $xml_paycheck_lines .= "<work_amount_unit_rate>" . $line->Rate . "</work_amount_unit_rate>\n";
            if($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
            {
                $adjustedLineAmount = -$line->AmountThisPeriod;
            } else {
                $adjustedLineAmount = $line->AmountThisPeriod;
            }
            $xml_paycheck_lines .= "<amount_period>" . $adjustedLineAmount . "</amount_period>\n";

            if ($line->LineNumber == $last_line_value) {
                $amount_year = "-Som over-";
            } else {
                if($line->LineNumber >= $lineOutFrom and $line->LineNumber <= $lineOutTo)
                {
                    $amount_year = -$totalThisYear->total;
                } else {
                    $amount_year = $totalThisYear->total;
                }
            }
            $last_line_value = $line->LineNumber;

            $xml_paycheck_lines .= "<amount_year>" . $amount_year . "</amount_year>\n";
            $xml_paycheck_lines .= "<department>" . $departments[$line->DepartmentID] . "</department>\n";
            $xml_paycheck_lines .= "<project>" . $projects[$line->ProjectID] . "</project>\n";
            $xml_paycheck_lines .= "<ledger_account_number>" . $line->AccountPlanID . "</ledger_account_number>\n";


            $xml_paycheck_lines .= "</paycheck_line>\n";
        }


		$xml_content .= "<paid_amount>" . $sumTotal . "</paid_amount>\n";
		$xml_content .= "<paid_amount_year>" . $sumTotalYear . "</paid_amount_year>\n";

        // Find taxing method
        if (!empty($head->TabellTrekk)) {
            $taxing_method = "Tabelltrekk";
            $taxing_method_value = $head->TabellTrekk;
        } else if (!empty($head->ProsentTrekk)) {
            $taxing_method = "Prosenttrekk - $head->ProsentTrekk";
            $taxing_method_value = $head->ProsentTrekk;
        } else { //non found, send empty string
            $taxing_method = "";
            $taxing_method_value = "";
        }
        
		$xml_content .= "<taxing_method>" . $taxing_method . "</taxing_method>\n";
		$xml_content .= "<taxing_method_value>" . $taxing_method_value . "</taxing_method_value>\n";

        // send empty for now
		$xml_content .= "<project></project>\n";
		$xml_content .= "<department></department>\n";

        /* employer */


		$xml_content .= "<employer>\n";
		$xml_content .= "<name>" . $_lib['sess']->get_companydef('CompanyName') . "</name>\n";

		$xml_content .= "<postal_address>\n";
		$xml_content .= "<street>" . $_lib['sess']->get_companydef('VAddress') . "</street>\n";
		$xml_content .= "<city>" . $_lib['sess']->get_companydef('VCity') . "</city>\n";
		$xml_content .= "<zip_code>" . $_lib['sess']->get_companydef('VZipCode') . "</zip_code>\n";
		$xml_content .= "<country_code>" . ($_lib['sess']->get_companydef('VCountryCode') == '' ? 'NO' : $_lib['sess']->get_companydef('VCountryCode'))  . "</country_code>\n"; // hardcoded to Norway for now
		$xml_content .= "</postal_address>\n";

        $xml_content .= "<email>" . $_lib['sess']->get_companydef('Email') . "</email>\n";
        $xml_content .= "<website>" . $_lib['sess']->get_companydef('WWW') . "</website>\n";
        $xml_content .= "<identifier>" . $this->OrgNumber . "</identifier>\n";
        $xml_content .= "<position />";
        $xml_content .= "<scheme>NO:ORGNR</scheme>\n";

		$xml_content .= "</employer>\n";

        
        /* employee */

		$xml_content .= "<employee>\n";
        $xml_content .= "<employee_number>" . $head->AccountPlanID . "</employee_number>\n";
		$xml_content .= "<first_name>" . $head->FirstName . "</first_name>\n";
		$xml_content .= "<last_name>" . $head->LastName . "</last_name>\n";
        $xml_content .= "<scheme>FAKTURABANK:EMAIL</scheme>\n";
        $xml_content .= "<identifier>" . $head->FakturabankEmail . "</identifier>\n";
        $xml_content .= "<email>" . $head->Email . "</email>\n";

		$xml_content .= "<postal_address>\n";
		$xml_content .= "<street>" . $head->Address . "</street>\n";
		$xml_content .= "<city>" . $head->City . "</city>\n";
        $xml_content .= "<zip_code>" . $head->ZipCode . "</zip_code>\n";
        $xml_content .= "<country_code>" . (empty($head->CountryCode) ? 'NO' : $head->CountryCode) . "</country_code>\n"; // hardcoded to Norway for now
		$xml_content .= "</postal_address>\n";

        $xml_content .= "<fixedphone>" . $head->Phone . "</fixedphone>\n";
        $xml_content .= "<cellphone>" . $head->Mobile . "</cellphone>\n";
        $xml_content .= "<bank_account_country_code>" . (empty($head->CountryCode) ? 'NO' : $head->CountryCode) . "</bank_account_country_code>\n"; // hardcoded to Norway for now
        $xml_content .= "<bank_account_number>" . $head->DomesticBankAccount . "</bank_account_number>\n";

		$xml_content .= "</employee>\n";

        $xml_content .= $xml_paycheck_lines;

        /* find vacation pay base the correct way */

        $firstDate = substr($head->JournalDate, 0, 4) . "-01-01";
		$lastDate = $head->JournalDate;
		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.JournalDate>='$firstDate' and S.JournalDate<='$lastDate' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		# print "$query<br>";
		$totalThisYear_da = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.JournalDate >= '$firstDate' and S.JournalDate <= '$lastDate' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1 and S.AccountPlanID = '" . $head->AccountPlanID . "';";
		#print "$query<br>";
		$totalThisYearFradrag_da = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag_da = $totalThisYear_da->total - $totalThisYearFradrag_da->total;


		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID=SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber < 70 and SL.EnableVacationPayment = 1;";
		# print "$query<br>";
		$totalThisYear = $_lib['storage']->get_row(array('query' => $query));

		$query = "select sum(SL.AmountThisPeriod) as total from salary S, salaryline SL where S.SalaryID = SL.SalaryID and S.SalaryID = '" . $head->SalaryID . "' and SL.LineNumber > 69 and SL.EnableVacationPayment = 1;";
		#print "$query<br>";
		$totalThisYearFradrag = $_lib['storage']->get_row(array('query' => $query));

		$fpGrunnlag = $totalThisYear->total - $totalThisYearFradrag->total;

        $xml_content .= "<paycheck_additional>\n";
        $xml_content .= "<title>Feriepenge grunnlag</title>\n";
        $xml_content .= "<value>" . $_lib['format']->Amount(array('value'=>$fpGrunnlag_da, 'return'=>'value')) . "</value>\n";
        $xml_content .= "</paycheck_additional>\n";



        return utf8_encode($xml_prefix . $xml_content . $xml_postfix);
    }

    function sendsalary($SalaryID, $SalaryConfID) {
        global $_lib;
        $xml = $this->createSalaryXML($SalaryID, $SalaryConfID);
        if ($xml === false) {
            return false;
        }

        $fakturabank_salary_id = $this->write($xml);

        if (!$fakturabank_salary_id) {
            return false;
        }


        $dataH = array();
        $dataH['SalaryID']             = $SalaryID;
        $dataH['FakturabankID']   = $fakturabank_salary_id;
        $dataH['FakturabankPersonID']   = $_lib['sess']->get_person('PersonID');
        $dataH['FakturabankDateTime']   = $_lib['sess']->get_session('Datetime');
        
        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'salary', 'debug' => false));
        return true;
    }

    ####################################################################################################
    #WRITE XML
    function write($xml) {
        global $_lib;

        $page = "/import_paychecks";
        $url  = "$this->protocol://$this->host$page";
        
        $headers = array(
            "POST ".$page." HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: \"run\"",
            "Content-length: ".strlen($xml),
            "Authorization: Basic " . base64_encode($this->credentials)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, $defined_vars['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Apply the XML to our curl call
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); 

        $data = curl_exec($ch); 

        $import_paycheck_result = $this->parseResult(substr($data, strpos($data, "<?xml version")));

        $ret = false;
        if (curl_errno($ch)) {
            $_lib['message']->add("Error: lastet opp lønnslipp: " . curl_error($ch) . " " . $import_paycheck_result['message']);
            if (!empty($import_paycheck_result['paycheck-results'])) {
                $_lib['message']->add("Error info: " . $import_paycheck_result['paycheck-results'][0]['error-message']);
            }
            $ret = false;
        } else {

            // Show me the result
            if ($import_paycheck_result['omitted-paychecks'] == 1) {
                $_lib['message']->add("L&oslash;nnslipp finnes allerede");
                $ret = false;
            } else if ($import_paycheck_result['failed-paychecks'] == 1) { // we might get errors even if errno is 0
                $_lib['message']->add("Feil under opplasting: " . $import_paycheck_result['message'] . " Info: " . $import_paycheck_result['exception']);
                $ret = false;
            } else if ($import_paycheck_result['created-paychecks'] == 0) {
                $_lib['message']->add("Feil tilbakemeldingsinfo fra server opplasting. " . $import_paycheck_result['message'] . " Info: " . $import_paycheck_result['exception']);
                $ret = false;
            } else {
                $_lib['message']->add("L&oslash;nnslippen ble opprettet riktig");
                $ret  = $import_paycheck_result['paycheck-results'][0]['paycheck-result']['id'];
            }
        }
       
        curl_close($ch);
        // todo return salary id
        return $ret;
    }

    function parseResult($xml_data) {
        global $_lib;

        $size = strlen($xml_data);

        if($size) {
            includelogic('xmldomtoobject/xmldomtoobject');
            $domtoobject = new empatix_framework_logic_xmldomtoobject(array('arrayTags' => $this->ArrayTag));
            #print "\n<hr>$xml_data\n<hr>";
            $import_paychecks_result    = $domtoobject->convert($xml_data);
        } else {
                $_lib['message']->add("XML Dokument tomt - pr&oslash;v igjen: $url");            
                return false;
        }
        // ugly convert from stdClass to array, and slight restructuring
        $ret_arr = (array) $import_paychecks_result;
        if (!empty($ret_arr['paycheck-results'])) {
            $ret_arr['paycheck-results'] = (array) $ret_arr['paycheck-results'];

            foreach ($ret_arr['paycheck-results'] as &$res) {
                $res = (array) $res;
                $res['paycheck-result'] = (array) $res['paycheck-result'];
            }
        }

        return $ret_arr;
    }
}
?>
