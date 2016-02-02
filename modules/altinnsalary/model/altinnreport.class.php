<?
/* Altinn report class
 * Includes all the linked salaries and employee info
 * needed for the report.
 */

class altinn_report {
  public $salaries     = array();
  public $salary_ids   = array();
  public $salary_lines = array();
  public $employees    = array();
  public $period       = '';
  public $melding      = null; // structured object that contains the report
  public $meldingsId   = null; // mesasge id
  public $erstatterMeldingsId = null; // replacement message id
  public $errors       = null; // to be populated if errors occur

/* Constructor accepts the accounting period(year-month)
 * and it automatically loads all the salaries from that
 * periode and all the employees for those salaries to
 * the arrays.
 */
  function __construct($period, $salary_ids = null) {
    // if no period selected, exit
    if (empty($period)) return;
    else $this->period = $period;

    // fetch the salaries
    if (!$salary_ids) self::fetchSalaries();
    else self::fetchSalaries($salary_ids);
    // fetch the employees
    self::fetchEmployees();
  }

/* Helper function to add replacement message id
 */
  function addReplacementMessageID($message_id) {
    $this->erstatterMeldingsId = $message_id;
  }

/* Helper function to check if the variable is empty
 * calls a sub function for for string, date, amount/number check
 * based on the type
 */
  function checkIfEmpty($field, $error_message, $type = 'string') {
    if ($type == 'string') $is_empty = empty($field);
    elseif ($type == 'date') $is_empty = strstr($field, '0000-00-00');
    elseif ($type == 'number') $is_empty = empty($field) || ($field == 0);
    elseif ($type == 'percent') $is_empty = is_null($field);
    elseif ($type == 'org_number') $is_empty = !preg_match('/^([0-9]{9})$/', $field);
    else {
      $error_message = 'Unknown type ' . $type;
      $is_empty = true;
    }
    if ($is_empty) $this->errors[] = $error_message;
    return $is_empty;
  }

/* Helper function that populates the report(melding)
 * array
 */
  function populateReportArray() {
    global $_lib;

    $org_number = $_lib['sess']->get_companydef('OrgNumber');
    $org_number = preg_replace('/\s+/', '', $org_number);
    // Error is: Organisation number missing(not set);
    self::checkIfEmpty($org_number, 'Firmaopplysning: Organisasjonsnr mangler (ikke satt)');
    // Error is: OrgNumber needs to be 9 digits long!
    self::checkIfEmpty($org_number, 'Firmaopplysning: Organisasjonsnr m&aring; v&aelig;re 9 tall langt', 'org_number');
    $leveranse = array();
    // leveranse = deliver
    // delivery date
    $leveranse['leveringstidspunkt'] = strftime('%FT%TZ', time());
    // calendar month
    // Error is: Period for report not chosen
    self::checkIfEmpty($this->period, 'Periode er ikke satt');
    $leveranse['kalendermaaned'] = $this->period;
    // salary system
    $leveranse['kildesystem'] = 'LODO';

    // government tax
    // arbeidsgiveravgift = tax that the company pays
    $sumForskuddstrekk = 0.0;
    $sumArbeidsgiveravgift = 0.0;

    // message id
    // replacement if we are sending a replacement report
    if ($this->erstatterMeldingsId) $leveranse['erstatterMeldingsId'] = $this->erstatterMeldingsId;
    // save for future use in creation of SOAP request
    $meldings_id = 'report_for_' . $org_number . '_at_' . time();
    $leveranse['meldingsId'] = $meldings_id;
    $this->meldingsId = $meldings_id;

    // opplysningspliktig = reportee
    // norskIdentifikator = norwegian identifier, personal id or company org number
    $leveranse['opplysningspliktig'] = array();
    $leveranse['opplysningspliktig']['norskIdentifikator'] = $org_number;

    // used for arbeidsgiveravgift node
    $loennOgGodtgjoerelse = array();
    // beregningskodeForArbeidsgiveravgift = calculation code for arbeidsgiveravgift
    $code_for_tax_calculation = $_lib['sess']->get_companydef('CalculationCodeForTax');
    // Error is: Code for tax calculation not set on company
    self::checkIfEmpty($code_for_tax_calculation, 'Firmaopplysning: Mangler beregningskode for arbeidsgiveravgift for firma');

    $virksomhet_array = array();
    // select salary and the employee connected to it
    // Error is: No employees for this period
    self::checkIfEmpty($this->employees, 'Det er ingen ansatte i perioden');
    // Error is: No salaries for this period
    self::checkIfEmpty($this->salaries, 'Det er ingen l&oslash;nnslipper  i perioden');
    foreach($this->salaries as $key_subcompany => $salaries) {
      $virksomhet = array();
      foreach($this->employees as $key_employee => $employee) {
        // if there is no salaries for current subcompany and current employee, skip over
        // it so we do not try to loop over a null value
        if (empty($salaries[$employee->AccountPlanID])) continue;
        foreach($salaries[$employee->AccountPlanID] as $key_salary => $salary) {
          // subcompany is the virksomhet for which we report this salary
          // Error is: No subcompany selected for salary L' . $salary->JournalID
          $org_number_set = !self::checkIfEmpty($key_subcompany, 'L&oslash;nnslipp og virksomhet: Mangler virksomhet p&aring; L' . $salary->JournalID);

          // norwegian id for company the employee works for
          $query_subcompany = "SELECT sc.* FROM subcompany sc
                               WHERE sc.SubcompanyID = '" . $key_subcompany . "'";
          $result_subcompany = $_lib['db']->db_query($query_subcompany);
          $subcompany = $_lib['db']->db_fetch_object($result_subcompany);
          $subcompany_org_number = preg_replace('/\s+/', '', $subcompany->OrgNumber);
          // Error is: OrgNumber for subcompany ' . $subcompany->Name . ' needs to be 9 digits long!', 'org_number
          if ($org_number_set) self::checkIfEmpty($subcompany_org_number, 'Virksomhet: Organisasjonsnr for ' . $subcompany->Name . '(virksomhet) v&aelig;re 9 tall langt', 'org_number');
          $virksomhet['norskIdentifikator'] = $subcompany_org_number;
          $inntektsmottaker = array();
          $inntektsmottaker['inntektsmottaker'] = array();
          $full_name = $employee->FirstName . ' ' . $employee->LastName;
          $full_name_for_error_message = $full_name . '(' . $employee->AccountPlanID . ')';
          // forskuddstrekk = tax that is taken from the employee
          $forskuddstrekk = 0.0;
          // get the occupation of the employee in the company
          // Error is: Occupation not set for salary L' . $salary->JournalID
          self::checkIfEmpty($salary->OccupationID, 'L&oslash;nnslipp: Mangler yrke p&aring; L' . $salary->JournalID);
          $query_occupation = "SELECT * FROM occupation WHERE OccupationID = " . (int)$salary->OccupationID;
          $result_occupation  = $_lib['db']->db_query($query_occupation);
          $occupation_code = $_lib['db']->db_fetch_object($result_occupation);
          // Error is: Occupation does not exist in the occupation list
          self::checkIfEmpty($occupation_code, 'L&oslash;nnslipp: Yrket finnes ikke i listen over yrker');

          // norwegian id for the employee, personal id number
          $society_number = $employee->SocietyNumber;
          // Error is: Personal ID number(society number) not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($society_number, 'Ansatt: Mangler personnummer for ' . $full_name_for_error_message);
          $inntektsmottaker['inntektsmottaker']['norskIdentifikator'] = $society_number;
          // name and birthdate
          $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon'] = array();
          $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['navn'] = $full_name;
          $birth_date = $employee->BirthDate;
          // Error is: Birth date not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($birth_date, 'Ansatt: Mangler f&oslash;dselsdag for ' . $full_name_for_error_message, 'date');
          $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['foedselsdato'] = strftime('%F', strtotime($birth_date));
          $arbeidsforhold = array();
          // type of employment
          // Error is: Employment type not set for salary L' . $salary->JournalID
          self::checkIfEmpty($salary->TypeOfEmployment, 'L&oslash;nnslipp: Mangler ansettelsestype p&aring; L' . $salary->JournalID);
          $arbeidsforhold['typeArbeidsforhold'] = $salary->TypeOfEmployment;
          $work_start = $employee->WorkStart;
          // Error is: Employment date not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($work_start, 'Ansatt: Mangler Ansettelsesdato for ' . $full_name_for_error_message, 'date');
          // employment date
          $arbeidsforhold['startdato'] = strftime('%F', strtotime($work_start));
          // work measurement, ex. hours per week
          // Error is: Work measurement not set for employee ' . $full_name_for_error_message
        self::checkIfEmpty($employee->Workmeasurement, 'Ansatt: Mangler arbeidsdtimer hver uke for ' . $full_name_for_error_message, 'number');
          $arbeidsforhold['antallTimerPerUkeSomEnFullStillingTilsvarer'] = $employee->Workmeasurement;
          // work measurement type
          // Error is: Work time scheme not set for salary L' . $salary->JournalID
          self::checkIfEmpty($salary->WorkTimeScheme, 'L&oslash;nnslipp: Mangler arbeidstid for L' . $salary->JournalID);
          $arbeidsforhold['avloenningstype'] =  $salary->WorkTimeScheme;
          // occupation, already checked above before query for occupation
          $arbeidsforhold['yrke'] = $occupation_code->YNr . $occupation_code->LNr;
          // work time scheme, ex. no shifts
          // Error is: Shift type not set for salary L' . $salary->JournalI
          self::checkIfEmpty($salary->ShiftType, 'L&oslash;nnslipp: Mangler skifttype L' . $salary->JournalID);
          $arbeidsforhold['arbeidstidsordning'] = $salary->ShiftType;
          // employment percentage
          // Error is: Work percent not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($employee->WorkPercent, 'Ansatt: Mangler stillingsprosent for ' . $full_name_for_error_message, 'number');
          $arbeidsforhold['stillingsprosent'] = (int) $employee->WorkPercent;
          // date of last change for payment date for salary
          $last_change_of_pay_date = $employee->CreditDaysUpdatedAt;
          // Error is: Last change of salary pay date not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($last_change_of_pay_date, 'Ansatt: Mangler kredittid oppdatert for ' . $full_name_for_error_message, 'date');
          $arbeidsforhold['sisteLoennsendringsdato'] = strftime('%F', strtotime($last_change_of_pay_date));
          // date of last change for position in company
          $last_change_of_position_in_company = $employee->inCurrentPositionSince;
          // Error is: Last change of position in company date not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($last_change_of_position_in_company, 'Ansatt: Mangler siste posisjonendringsdato for ' . $full_name_for_error_message, 'date');
          $arbeidsforhold['loennsansiennitet'] = strftime('%F', strtotime($last_change_of_position_in_company));
          // date of last change for work percentage
          $last_change_of_work_percentage = $employee->WorkPercentUpdatedAt;
          // Error is: Last change of work percent date not set for employee ' . $full_name_for_error_message
          self::checkIfEmpty($last_change_of_work_percentage, 'Ansatt: Mangler stillingsprosentendret for' . $full_name_for_error_message, 'date');
          $arbeidsforhold['sisteDatoForStillingsprosentendring'] = strftime('%F', strtotime($last_change_of_work_percentage));
          // work relation
          $inntektsmottaker['inntektsmottaker']['arbeidsforhold'] = $arbeidsforhold;

          // check if valid from and to dates are set
          // Error is: Valid from date not set for salary L' . $salary->JournalID, 'date
          self::checkIfEmpty($salary->ValidFrom, 'L&oslash;nnslipp: Manger til dato p&aring; L' . $salary->JournalID, 'date');
          // Error is: Valid to date not set for salary L' . $salary->JournalID, 'date
          self::checkIfEmpty($salary->ValidTo, 'L&oslash;nnslipp: Manger til dato p&aring; L' . $salary->JournalID, 'date');

          // get municipality tax percentage and zone info for arbeidsgiveravgift
          $salary_municipality = $salary->KommuneID;
          // Error is: Municipality not set for salary L' . $salary->JournalI
          self::checkIfEmpty($salary_municipality, 'L&oslash;nnslipp: Mangler komune p&aring; L' . $salary->JournalID);
          $query_kommune_tax = "SELECT agag.*
                                FROM arbeidsgiveravgift agag JOIN kommune k ON k.Sone = agag.Code
                                WHERE k.KommuneID = '" . $salary_municipality . "'";
          $result_kommune_tax  = $_lib['db']->db_query($query_kommune_tax);
          $kommune_tax = $_lib['db']->db_fetch_object($result_kommune_tax);

          // taxing zone code, already checked above before the query
          // Error is: Municipality selected for the salary L' . $salary->JournalID . '
          // does not exist in the list of municipalities or does not have a zone code set
          self::checkIfEmpty($kommune_tax, 'L&oslash;nnslipp: Mangler komune p&aring; L' . $salary->JournalID . ', er ikke valgt eller komunen har ikke valgt kode');
          // Code property covered by the above check since it is the id for arbeidsgiveravggift table
          // Error is: Municipality selected for the salary L' . $salary->JournalID . ' does not have a tax percent set', 'percent
          self::checkIfEmpty($kommune_tax->Percent, 'L&oslash;nnslipp: Mangler prosent for komune valgt p$aring;  L' . $salary->JournalID, 'percent');
          $zone_code = $kommune_tax->Code;
          if (!isset($loennOgGodtgjoerelse[$zone_code])) {
            $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse'] = array();
            $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['beregningskodeForArbeidsgiveravgift'] = $code_for_tax_calculation;
            $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['sone'] = $kommune_tax->Code;
            // amount
            $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['avgiftsgrunnlagBeloep'] = 0;
            // taxing percent
            $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['prosentsatsForAvgiftsberegning'] = $kommune_tax->Percent;
          }

          // income entries/salary lines
          $inntekt_tmp = array();
          $all_salary_lines_empty = true;
          foreach($this->salary_lines[$salary->SalaryID] as $salary_line) {
            $inntekt = array();
            $inntekt['inntekt'] = array();
            if ($salary_line->AmountThisPeriod == 0) continue;
            else $all_salary_lines_empty = false;
            // if the code is 950 that is forskuddstrekk
            // if not that is a regular income entry
            if ($salary_line->SalaryCode == 950) $forskuddstrekk += (float) $salary_line->AmountThisPeriod;
            else {
              // start and end date for this income, already checked once outside the foreach loop
              $inntekt['inntekt']['startdatoOpptjeningsperiode'] = $salary->ValidFrom;
              $inntekt['inntekt']['sluttdatoOpptjeningsperiode'] = $salary->ValidTo;
              // fordel = determines if the income entry is positive or negative
              $inntekt['inntekt']['fordel'] = ($salary_line->LineNumber <= 69) ? 'kontantytelse' : 'utgiftsgodtgjoerelse';
              // boolean flags if the entry falls under some taxing regulation or not
              // both utloeserArbeidsgiveravgift and inngaarIGrunnlagForTrekk fields
              // first is true if the salary line code has -A in it
              // the second is true if we have a salary code for this entry
              $inntekt['inntekt']['utloeserArbeidsgiveravgift'] = (strstr($salary_line->SalaryCode, '-A')) ? 'true' : 'false';
              $inntekt['inntekt']['inngaarIGrunnlagForTrekk'] = (!empty($salary_line->SalaryCode)) ? 'true' : 'false';
              // amount for entry
              $inntekt['inntekt']['beloep'] = $salary_line->AmountThisPeriod;
              // calculate total for arbeidsgiveravgift amount
              $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['avgiftsgrunnlagBeloep'] += $salary_line->AmountThisPeriod;
              // description for the entry
              // Error is: Salary line description for salary L' . $salary->JournalID . ' not set for line with text \'' . $salary_linSalaryText . "'");
              self::checkIfEmpty($salary_line->SalaryDescription, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . ' har ikke satt altinnbeskrivelse med text \'' . $salary_line->SalaryText . "'");
              $inntekt['inntekt']['loennsinntekt'] = array();
              $inntekt['inntekt']['loennsinntekt']['beskrivelse'] = self::convertNorwegianLettersToASCII($salary_line->SalaryDescription);
              // there can be multiple entries for one salary so we add to an array
              $inntekt_tmp[] = $inntekt;
            }
          }
          // check if all salary lines were empty/skipped(with 0 amount)
          $empty_salary = $all_salary_lines_empty ? '' : 'not empty';
          // Error is: Salary L' . $salary->JournalID . ' has only 0 amount lines
          self::checkIfEmpty($empty_salary, 'L&oslash;nnslipp: L&oslash;nnslipp L' . $salary->JournalID . ' har bare 0,00 linjer');

          // amount for forskuddstrekk
          $inntektsmottaker['inntektsmottaker']['forskuddstrekk'] = array();
          $inntektsmottaker['inntektsmottaker']['forskuddstrekk']['beloep'] = -$forskuddstrekk;
          $sumForskuddstrekk += $forskuddstrekk;

          foreach($inntekt_tmp as $single_inntekt) {
            $inntektsmottaker['inntektsmottaker'][] = $single_inntekt;
          }

          // income reciever
          $virksomhet[] = $inntektsmottaker;
        }
      }
      foreach($loennOgGodtgjoerelse as $zone_tax_array) {
        $zone_tax = $zone_tax_array['loennOgGodtgjoerelse'];
        $sumArbeidsgiveravgift += $zone_tax['avgiftsgrunnlagBeloep'] * $zone_tax['prosentsatsForAvgiftsberegning']/100.0;
      }
      // loennOgGodtgjoerelse = salary and refunds
      $virksomhet['arbeidsgiveravgift'] = $loennOgGodtgjoerelse;
      $virksomhet_array[]['virksomhet'] = $virksomhet;
    }
    $leveranse['oppgave'] = array();
    $leveranse['oppgave']['betalingsinformasjon'] = array();
    $leveranse['oppgave']['betalingsinformasjon']['sumForskuddstrekk'] = (int) round($sumForskuddstrekk);
    $leveranse['oppgave']['betalingsinformasjon']['sumArbeidsgiveravgift'] = (int) round($sumArbeidsgiveravgift);
    $leveranse['oppgave'] = $virksomhet_array;
    $melding['Leveranse'] = $leveranse;
    $this->melding = $melding;
  }

/* Helper function that converts norwegian letters to
 * their ascii representation(both small and capital):
 * æ -> ae
 * ø -> oe
 * å -> aa
 */
  function convertNorwegianLettersToASCII($text) {
    $norwegian_letters = array('æ', 'ø', 'å', 'Æ', 'Ø', 'Å');
    $ascii_representations = array('ae', 'oe', 'aa', 'AE', 'OE', 'AA');
    return str_replace($norwegian_letters, $ascii_representations, utf8_encode($text));
  }

/* Helper function to save a relation which salaries were sent
 * in this report.
 * Returns false if no salaries were selected.
 */
  function saveSalaryReportLinks($altinn_report_id) {
    global $_lib;
    if (empty($this->salary_ids)) return false;
    $insert_query = 'INSERT INTO altinnReport1salary (AltinnReport1ID, SalaryId, JournalID) VALUES ';
    $query_salaries = self::queryStringForSelectedSalaries();
    $result_salaries  = $_lib['db']->db_query($query_salaries);
    while ($salary = $_lib['db']->db_fetch_object($result_salaries)) {
      $insert_query .= "('" . $altinn_report_id . "', '" . $salary->SalaryID . "', '" . $salary->JournalID . "'),";
    }
    $insert_query = substr($insert_query, 0, -1);
    $_lib['db']->db_query($insert_query);
    return true;
  }

/* Helper function that generates the query to get
 * the employees for the included salaries
 */
  function queryStringForIncludedEmployees() {
    // only the ones whose salaries have the altinn/actual pay date set
    $query_employees = "SELECT ap.*
                       FROM salary s JOIN accountplan ap ON s.AccountPlanID = ap.AccountPlanID
                       WHERE s.ActualPayDate LIKE  '" . $this->period . "%'";
    // and restrict further by selected salary ids
    if ($this->salary_ids) {
      $query_employees .= ' AND s.SalaryID IN (';
      foreach($this->salary_ids as $salary_id) {
        $query_employees .= (string) $salary_id . ', ';
      }
      $query_employees = substr($query_employees, 0, -2);
      $query_employees .= ')';
    }
    return $query_employees;
  }

/* Helper function that generates the query to get
 * the salaries with the ids that are chosen
 */
  function queryStringForSelectedSalaries() {
    // only the ones that have the altinn/actual pay date set
    $query_salaries = "SELECT s.*
                       FROM salary s
                       WHERE s.ActualPayDate LIKE  '" . $this->period . "%'";
    // and restrict further by ids
    if ($this->salary_ids) {
      $query_salaries .= ' AND SalaryID IN (';
      foreach($this->salary_ids as $salary_id) {
        $query_salaries .= (string) $salary_id . ', ';
      }
      $query_salaries = substr($query_salaries, 0, -2);
      $query_salaries .= ')';
    }
    return $query_salaries;
  }

/* Helper function that populates the salaries array
 * for the selected period
 */
  function fetchSalaries($salary_ids = null) {
    global $_lib;
    if (!empty($salary_ids)) $this->salary_ids = $salary_ids;
    $query_salaries = self::queryStringForSelectedSalaries();
    $result_salaries  = $_lib['db']->db_query($query_salaries);
    while ($salary = $_lib['db']->db_fetch_object($result_salaries)) {
      $this->salaries[(int)$salary->SubcompanyID][$salary->AccountPlanID][] = $salary;
      self::fetchSalaryLines($salary);
    }
  }

/* Helper function that populates the salary lines
 * for the specified salary
 */
  function fetchSalaryLines($salary) {
    global $_lib;
    $query_salary_lines = "SELECT sl.*
                           FROM salaryline sl
                           WHERE sl.SalaryID = " . $salary->SalaryID;
    $result_salary_lines  = $_lib['db']->db_query($query_salary_lines);
    while ($salary_line = $_lib['db']->db_fetch_object($result_salary_lines)) {
      $this->salary_lines[$salary->SalaryID][] = $salary_line;
    }
  }

/* Helper function that populates the employees array
 * for the selected period
 */
  function fetchEmployees() {
    global $_lib;
    // only the ones whose salaries have the altinn/actual pay date set
    $query_employees = self::queryStringForIncludedEmployees();
    $result_employees  = $_lib['db']->db_query($query_employees);
    while ($employee = $_lib['db']->db_fetch_object($result_employees)) {
      $this->employees[$employee->AccountPlanID] = $employee;
    }
  }

/* Generate XML function creates an XML used for A02
 */
  function generateXML($args = array()) {
    self::populateReportArray();
    if (empty($this->errors)) {
      $xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><melding xmlns="urn:ske:fastsetting:innsamling:a-meldingen:v2_0"></melding>');
      self::generateXMLFromArray($this->melding, $xml_data);
      $xml = $xml_data->saveXML();
      // use DOMDocument to format XML properly
      $doc = new DOMDocument();
      $doc->formatOutput = true;
      $doc->loadXML($xml);
      return $doc->saveXML();
    }
    else return 'Error!';
  }

/* Helper function that generates an XML with the same
 * structure as array that gets passed as an argument.
 */
  function generateXMLFromArray($report_message, &$xml) {
    foreach($report_message as $key => $value) {
      if(is_array($value)) {
        if(!is_numeric($key)) {
          $subnode = $xml->addChild("$key");
          self::generateXMLFromArray($value, $subnode);
        }
        else {
          self::generateXMLFromArray($value, $xml);
        }
      }
      else {
        $xml->addChild("$key","$value");
      }
    }
    return $xml;
  }

}
?>
