<?
//includelogic('salary/salaryreport');

class lodo_fakturabank_fakturabanksalary {
    private $host           = '';
    private $protocol       = '';
    private $username       = '';
    private $password       = '';
    private $login          = false;
    private $timeout        = 30; 
    private $credentials    = '';
    private $OrgNumber      = '';

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
        global $_lib;

        $query_head     = "select S.*, F.Email AS FakturabankEmail, A.AccountName, A.Address, A.City, A.ZipCode, A.SocietyNumber, A.TabellTrekk, A.ProsentTrekk, A.Email, A.Address, A.ZipCode, A.LastName, A.FirstName, A.City, A.Phone, A.Mobile, A.DomesticBankAccount from salary as S, accountplan as A, fakturabankemail as F  where S.SalaryID='$SalaryID' and S.AccountPlanID=A.AccountPlanID and F.AccountPlanID = S.AccountPlanID and A.AccountPlanID = F.AccountPlanID";
        #print "$query_head<br>";
        $result_head    = $_lib['db']->db_query($query_head);
        $head           = $_lib['db']->db_fetch_object($result_head);

        $query_salary   = "select * from salaryline where SalaryID = '$SalaryID' order by LineNumber asc";
        $result_salary  = $_lib['db']->db_query($query_salary);

        $project_query = "select * from project";
        $project_result = $_lib['db']->db_query($project_query);
        $projects = array();
        while( $project_line = $_lib['db']->db_fetch_assoc($project_result))
            $projects[ $project_line['ProjectID'] ] = $project_line['Heading'];
        $projects[0] = "";

        $xml_prefix = "<" . "?xml version=\"1.0\" encoding=\"UTF-8\"?" . "><paycheck_messages><paycheck>\n";
        $xml_postfix = "</paycheck></paycheck_messages>\n";

        $xml_content = "";

        $xml_content .= "<from_date>" . $head->ValidFrom . "</from_date>\n";
        $xml_content .= "<to_date>" . $head->ValidTo . "</to_date>\n";
		$xml_content .= "<document_number>L" . $head->JournalID . "</document_number>\n";
		$xml_content .= "<document_date>" . $head->JournalDate . "</document_date>\n";
		$xml_content .= "<period>" . $head->Period . "</period>\n";
        $xml_content .= "<currency_code>NOK</currency_code>\n";

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
            $xml_paycheck_lines .= "<description>" . $line->SalaryText . "</description>\n";
            $xml_paycheck_lines .= "<work_amount>" . $line->NumberInPeriod . "</work_amount>\n";

            // leave work_amount_unit blank since the system does not yet specify such
            $xml_paycheck_lines .= "<work_amount_unit />\n";
            $xml_paycheck_lines .= "<work_amount_rate>" . $line->Rate . "</work_amount_rate>\n";
            $xml_paycheck_lines .= "<amount_period>" . $line->NumberInPeriod . "</amount_period>\n";

            if ($line->LineNumber == $last_line_value) {
                $amount_year = "-Som over-";
            } else {
                $amount_year = $totalThisYear->total;
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
		$xml_content .= "<name>" . $_lib['sess']->get_companydef('CompanyName') . "</name>";

		$xml_content .= "<postal_address>\n";
		$xml_content .= "<street>" . $_lib['sess']->get_companydef('VAddress') . "</street>";
		$xml_content .= "<city>" . $_lib['sess']->get_companydef('VCity') . "</city>";
		$xml_content .= "<zip_code>" . $_lib['sess']->get_companydef('VZipCode') . "</zip_code>";
		$xml_content .= "<country_code>NO</country_code>";
		$xml_content .= "</postal_address>\n";

        $xml_content .= "<email>" . $_lib['sess']->get_companydef('Email') . "</email>\n";
        $xml_content .= "<website>" . $_lib['sess']->get_companydef('WWW') . "</website>\n";
        $xml_content .= "<identifier>" . $this->OrgNumber . "</identifier>\n";
        $xml_content .= "<scheme>NO:ORGNR</scheme>\n";

		$xml_content .= "</employer>\n";

        
        /* employee */

		$xml_content .= "<employee>\n";
        $xml_content .= "<id>" . $head->AccountPlanID . "</id>";
		$xml_content .= "<first_name>" . $head->FirstName . "</first_name>";
		$xml_content .= "<last_name>" . $head->LastName . "</last_name>";
        $xml_content .= "<scheme>FAKTURABANK:EMAIL</scheme>";
        $xml_content .= "<identifier>" . $head->FakturabankEmail . "</identifier>";
        $xml_content .= "<email>" . $head->Email . "</email>";

		$xml_content .= "<postal_address>";
		$xml_content .= "<street>" . $head->Address . "</street>";
		$xml_content .= "<city>" . $head->City . "</city>";
        $xml_content .= "<zip_code>" . $head->ZipCode . "</zip_code>";
		$xml_content .= "<country_code>NO</country_code>";
		$xml_content .= "</postal_address>";

        $xml_content .= "<fixedphone>" . $head->Phone . "</fixedphone>";
        $xml_content .= "<cellphone>" . $head->Mobile . "</cellphone>";
        $xml_content .= "<bank_account_country>NO</bank_account_country>";
        $xml_content .= "<bank_account_number>" . $head->DomesticBankAccount . "</bank_account_number>";

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

        $xml_content .= "<paycheck_additional>";
        $xml_content .= "<title>Feriepenge grunnlag</title>";
        $xml_content .= "<value>" . $_lib['format']->Amount(array('value'=>$fpGrunnlag_da, 'return'=>'value')) . "</value>";
        $xml_content .= "</paycheck_additional>";



        return $xml_prefix . $xml_content . $xml_postfix;
    }

    function sendsalary($SalaryID) {
        $xml = $this->createSalaryXML($SalaryID, $SalaryConfID);
        $fakturabank_salary_id = $this->write($xml);
        // TODO save 
        //FakturabankID
        //FakturabankPersonID
        //FakturabankDateTime
    }

    ####################################################################################################
    #WRITE XML
    function write($xml) {
        echo("modules/fakturabank/model/fakturabanksalary.class.php-" . __LINE__ . ":xml:" . (is_array($xml) || is_object($xml) ? print_r($xml, true) : $xml . ". <br/>\n"));
        
        global $_lib;

        
        $page = "/import_paychecks.xml";
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
        error_log("modules/fakturabank/model/fakturabanksalary.class.php-" . __LINE__ . ":data:" . (is_array($data) || is_object($data) ? print_r($data, true) : $data . ". <br/>\n"));
        $_lib['message']->add("modules/fakturabank/model/fakturabanksalary.class.php-" . __LINE__ . ":data:" . (is_array($data) || is_object($data) ? print_r($data, true) : $data . ". <br/>\n"));
        #$_lib['message']->add("FB->write()->exec()");

        $success = false;
        if (curl_errno($ch)) {
            $_lib['message']->add("Error: opprette faktura: " . curl_error($ch));
        } else {
            // Show me the result
            $_lib['message']->add(microtime() . " Opprettet faktura: $i");
            $_lib['message']->add("<pre>$data</pre>");
            #print_r(curl_getinfo($ch));
            $success  = true;
        }
       
        curl_close($ch);
        // todo return salary id
        return $success;
    }
}
?>
