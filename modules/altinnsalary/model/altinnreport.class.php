<?
/* Altinn report class
 * Includes all the linked salaries and employee info
 * needed for the report.
 */

class altinn_report {
  public $salaries     = array();
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
    if (!preg_match('/^([0-9]{9})$/', $org_number)) {
      $this->errors[] = 'Company OrgNumber needs to be 9 digits long!';
    }
    $leveranse = array();
    // leveranse = deliver
    // delivery date
    $leveranse['leveringstidspunkt'] = strftime('%FT%TZ', time());
    // calendar month
    self::checkIfEmpty($this->period, 'Period for report not chosen');
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
    self::checkIfEmpty($org_number, 'Company Norwegian organisation number missing(not set)');
    $leveranse['opplysningspliktig'] = array();
    $leveranse['opplysningspliktig']['norskIdentifikator'] = $org_number;

    // used for arbeidsgiveravgift node
    $loennOgGodtgjoerelse = array();
    // beregningskodeForArbeidsgiveravgift = calculation code for arbeidsgiveravgift
    $code_for_tax_calculation = $_lib['sess']->get_companydef('CalculationCodeForTax');
    self::checkIfEmpty($code_for_tax_calculation, 'Code for tax calculation not set on company');

    $virksomhet = array();
    // select salary and the employee connected to it
    self::checkIfEmpty($this->employees, 'No employees for this period');
    self::checkIfEmpty($this->salaries, 'No salaries for this period');
    foreach($this->employees as $key_employee => $employee) {
      foreach($this->salaries[$employee->AccountPlanID] as $key_salary => $salary) {

        // norwegian id for company the employee works for
        $virksomhet['norskIdentifikator'] = $org_number; // already checked if empty
        $inntektsmottaker = array();
        $inntektsmottaker['inntektsmottaker'] = array();
        $full_name = $employee->FirstName . ' ' . $employee->LastName;
        $full_name_for_error_message = $full_name . '(' . $employee->AccountPlanID . ')';
        // forskuddstrekk = tax that is taken from the employee
        $forskuddstrekk = 0.0;
        // get the occupation of the employee in the company
        self::checkIfEmpty($salary->OccupationID, 'Occupation not set for salary L' . $salary->JournalID);
        $query_occupation = "SELECT * FROM occupation WHERE OccupationID = " . $salary->OccupationID;
        $result_occupation  = $_lib['db']->db_query($query_occupation);
        $occupation_code = $_lib['db']->db_fetch_object($result_occupation);
        self::checkIfEmpty($occupation_code, 'Occupation does not exist in the occupation list');

        // norwegian id for the employee, personal id number
        $society_number = $employee->SocietyNumber;
        self::checkIfEmpty($society_number, 'Personal ID number(society number) not set for employee ' . $full_name_for_error_message);
        $inntektsmottaker['inntektsmottaker']['norskIdentifikator'] = $society_number;
        // name and birthdate
        $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon'] = array();
        $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['navn'] = $full_name;
        $birth_date = $employee->BirthDate;
        self::checkIfEmpty($birth_date, 'Birth date not set for employee ' . $full_name_for_error_message, 'date');
        $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['foedselsdato'] = strftime('%F', strtotime($birth_date));
        $arbeidsforhold = array();
        // type of employment
        self::checkIfEmpty($salary->TypeOfEmployment, 'Employment type not set for salary L' . $salary->JournalID);
        $arbeidsforhold['typeArbeidsforhold'] = $salary->TypeOfEmployment;
        $work_start = $employee->WorkStart;
        self::checkIfEmpty($work_start, 'Employment date not set for employee ' . $full_name_for_error_message, 'date');
        // employment date
        $arbeidsforhold['startdato'] = strftime('%F', strtotime($work_start));
        // work measurement, ex. hours per week
        self::checkIfEmpty($employee->Workmeasurement, 'Work measurement not set for employee ' . $full_name_for_error_message, 'number');
        $arbeidsforhold['antallTimerPerUkeSomEnFullStillingTilsvarer'] = $employee->Workmeasurement;
        // work measurement type
        self::checkIfEmpty($salary->WorkTimeScheme, 'Work time scheme not set for salary L' . $salary->JournalID);
        $arbeidsforhold['avloenningstype'] =  $salary->WorkTimeScheme;
        // occupation, already checked above before query for occupation
        $arbeidsforhold['yrke'] = $occupation_code->YNr . $occupation_code->LNr;
        // work time scheme, ex. no shifts
        self::checkIfEmpty($salary->ShiftType, 'Shift type not set for salary L' . $salary->JournalID);
        $arbeidsforhold['arbeidstidsordning'] = $salary->ShiftType;
        // employment percentage
        self::checkIfEmpty($employee->WorkPercent, 'Work percent not set for employee ' . $full_name_for_error_message, 'number');
        $arbeidsforhold['stillingsprosent'] = (int) $employee->WorkPercent;
        // date of last change for payment date for salary
        $last_change_of_pay_date = $employee->CreditDaysUpdatedAt;
        self::checkIfEmpty($last_change_of_pay_date, 'Last change of salary pay date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold['sisteLoennsendringsdato'] = strftime('%F', strtotime($last_change_of_pay_date));
        // date of last change for position in company
        $last_change_of_position_in_company = $employee->inCurrentPositionSince;
        self::checkIfEmpty($last_change_of_position_in_company, 'Last change of position in company date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold['loennsansiennitet'] = strftime('%F', strtotime($last_change_of_position_in_company));
        // date of last change for work percentage
        $last_change_of_work_percentage = $employee->WorkPercentUpdatedAt;
        self::checkIfEmpty($last_change_of_pay_date, 'Last change of work percent date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold['sisteDatoForStillingsprosentendring'] = strftime('%F', strtotime($last_change_of_work_percentage));
        // work relation
        $inntektsmottaker['inntektsmottaker']['arbeidsforhold'] = $arbeidsforhold;

        // check if valid from and to dates are set
        self::checkIfEmpty($salary->ValidFrom, 'Valid from date not set for salary L' . $salary->JournalID, 'date');
        self::checkIfEmpty($salary->ValidTo, 'Valid to date not set for salary L' . $salary->JournalID, 'date');

        // get municipality tax percentage and zone info for arbeidsgiveravgift
        $salary_municipality = $salary->KommuneID;
        self::checkIfEmpty($salary_municipality, 'Municipality not set for salary L' . $salary->JournalID);
        $query_kommune_tax = "SELECT agag.*
                              FROM arbeidsgiveravgift agag JOIN kommune k ON k.Sone = agag.Code
                              WHERE k.KommuneID = '" . $salary_municipality . "'";
        $result_kommune_tax  = $_lib['db']->db_query($query_kommune_tax);
        $kommune_tax = $_lib['db']->db_fetch_object($result_kommune_tax);

        // taxing zone code, already checked above before the query
        self::checkIfEmpty($kommune_tax, 'Municipality selected for the salary L' . $salary->JournalID . ' does not exist in the list of municipalities or does not have a zone code set');
        // Code property covered by the above check since it is the id for arbeidsgiveravggift table
        self::checkIfEmpty($kommune_tax->Percent, 'Municipality selected for the salary L' . $salary->JournalID . ' does not have a tax percent set', 'percent');
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
            self::checkIfEmpty($salary_line->SalaryDescription, 'Salary line description for salary L' . $salary->JournalID . ' not set for line with text \'' . $salary_line->SalaryText . "'");
            $inntekt['inntekt']['loennsinntekt'] = array();
            $inntekt['inntekt']['loennsinntekt']['beskrivelse'] = self::convertNorwegianLettersToASCII($salary_line->SalaryDescription);
            // there can be multiple entries for one salary so we add to an array
            $inntekt_tmp[] = $inntekt;
          }
        }
        // check if all salary lines were empty/skipped(with 0 amount)
        $empty_salary = $all_salary_lines_empty ? '' : 'not empty';
        self::checkIfEmpty($empty_salary, 'Salary L' . $salary->JournalID . ' has only 0 amount lines');

        // amount for forskuddstrekk
        $inntektsmottaker['inntektsmottaker']['forskuddstrekk'] = array();
        $inntektsmottaker['inntektsmottaker']['forskuddstrekk']['beloep'] = -$forskuddstrekk;
        $sumForskuddstrekk += $forskuddstrekk;

        //TODO: if some problems occur assign this with a foreach loop
        $inntektsmottaker['inntektsmottaker'][] = $inntekt_tmp;

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

    $leveranse['oppgave'] = array();
    $leveranse['oppgave']['betalingsinformasjon'] = array();
    $leveranse['oppgave']['betalingsinformasjon']['sumForskuddstrekk'] = (int) round($sumForskuddstrekk);
    $leveranse['oppgave']['betalingsinformasjon']['sumArbeidsgiveravgift'] = (int) round($sumArbeidsgiveravgift);
    $leveranse['oppgave']['virksomhet'] = $virksomhet;
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

/* Helper function that populates the salaries array
 * for the selected period
 */
  function fetchSalaries($salary_ids = null) {
    global $_lib;
    // only the ones that have the altinn/actual pay date set
    $query_salaries = "SELECT s.*
                       FROM salary s
                       WHERE s.ActualPayDate LIKE  '" . $this->period . "%'";
    if ($salary_ids) {
      $query_salaries .= ' AND SalaryID IN (';
      for($i = 0; $i < count($salary_ids); ++$i) {
        if ($i == count($salary_ids)-1) $query_salaries .= (string) $salary_ids[$i] . ')';
        else $query_salaries .= (string) $salary_ids[$i] . ', ';
      }
    }
    $result_salaries  = $_lib['db']->db_query($query_salaries);
    while ($salary = $_lib['db']->db_fetch_object($result_salaries)) {
      $this->salaries[$salary->AccountPlanID][] = $salary;
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
    $query_employees = "SELECT ap.*
                       FROM salary s JOIN accountplan ap ON s.AccountPlanID = ap.AccountPlanID
                       WHERE s.ActualPayDate LIKE  '" . $this->period . "%'";
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
