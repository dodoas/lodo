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
  public $errors       = null; // to be populated if errors occur

/* Constructor accepts the accounting period(year-month)
 * and it automatically loads all the salaries from that
 * periode and all the employees for those salaries to
 * the arrays.
 */
  function __construct($period) {
    // if no period selected, exit
    if (empty($period)) return;
    else $this->period = $period;

    // fetch the salaries
    self::fetchSalaries();
    // fetch the employees
    self::fetchEmployees();
  }

/* Helper function to check if the variable is empty
 * calls a sub function for for string, date, amount/number check
 * based on the type
 */
  function checkIfEmpty($field, $error_message, $type = 'string') {
    if ($type == 'string') $is_empty = empty($field);
    elseif ($type == 'date') $is_empty = strstr($field, '0000-00-00');
    elseif ($type == 'number') $is_empty = empty($field) || ($field == 0);
    else {
      $error_message = 'Unknown type ' . $type;
      $is_empty = true;
    }
    if ($is_empty) $this->errors[] = $error_message;
    return $is_empty;
  }

/* Helper function that populates the report(melding)
 * object
 */
  function populateReportObject() {
    global $_lib;

    $leveranse = null;
    // leveranse = deliver
    // delivery date
    $leveranse->leveringstidspunkt = strftime('%FT%TZ', time());
    // calendar month
    self::checkIfEmpty($this->period, 'Period for report not chosen');
    $leveranse->kalendermaaned = $this->period;
    // salary system
    $leveranse->kildesystem = 'LODO';

    // government tax
    // arbeidsgiveravgift = tax that the company pays
    $arbeidsgiveravgiftbeloep = 0.0;

    // message id
    // save for future use in creation of SOAP request
    $meldings_id = 'report_for_' . $_lib['sess']->get_companydef('OrgNumber') . '_at_' . strftime('%FT%TZ', time());
    $leveranse->meldingsId = $meldings_id;
    $this->meldingsId = $meldings_id;

    // opplysningspliktig = reportee
    // norskIdentifikator = norwegian identifier, personal id or company org number
    $org_number = $_lib['sess']->get_companydef('OrgNumber');
    self::checkIfEmpty($org_number, 'Company Norwegian organisation number missing(not set)');
    $leveranse->opplysningspliktig->norskIdentifikator = $org_number;

    $virksomhet = null;
    // select salary and the employee connected to it
    self::checkIfEmpty($this->employees, 'No employees for this period');
    self::checkIfEmpty($this->salaries, 'No salaries for this period');
    foreach($this->employees as $key_employee => $employee) {
      foreach($this->salaries[$employee->AccountPlanID] as $key_salary => $salary) {

        $inntektsmottaker = null;
        $full_name = $employee->FirstName . ' ' . $employee->LastName;
        $full_name_for_error_message = $full_name . '(' . $employee->AccountPlanID . ')';
        // forskuddstrekk = tax that is taken from the employee
        $forskuddstrekk = 0.0;
        // get the occupation of the employee in the company
        self::checkIfEmpty($employee->OccupationID, 'Occupation not set for employee ' . $full_name_for_error_message);
        $query_occupation = "SELECT * FROM occupation WHERE OccupationID = " . $employee->OccupationID;
        $result_occupation  = $_lib['db']->db_query($query_occupation);
        $occupation_code = $_lib['db']->db_fetch_object($result_occupation);
        self::checkIfEmpty($occupation_code, 'Occupation does not exist in the occupation list');

        // norwegian id for company the employee works for
        $virksomhet->norskIdentifikator = $org_number; // already checked if empty
        // norwegian id for the employee, personal id number
        $society_number = $employee->SocietyNumber;
        self::checkIfEmpty($society_number, 'Personal ID number(society number) not set for employee ' . $full_name_for_error_message);
        $inntektsmottaker->norskIdentifikator = $society_number;
        // name and birthdate
        $inntektsmottaker->identifiserendeInformasjon->navn = $full_name;
        $birth_date = $employee->BirthDate;
        self::checkIfEmpty($birth_date, 'Birth date not set for employee ' . $full_name_for_error_message, 'date');
        $inntektsmottaker->identifiserendeInformasjon->foedselsdato = strftime('%F', strtotime($birth_date));
        $arbeidsforhold = null;
        $work_start = $employee->WorkStart;
        self::checkIfEmpty($work_start, 'Employment date not set for employee ' . $full_name_for_error_message, 'date');
        // employment date
        $arbeidsforhold->startdato = strftime('%F', strtotime($work_start));
        // type of employment
        self::checkIfEmpty($employee->TypeOfEmployment, 'Employment type not set for employee ' . $full_name_for_error_message);
        $arbeidsforhold->typeArbeidsforhold = $employee->TypeOfEmployment;
        // work measurement, ex. hours per week
        self::checkIfEmpty($employee->Workmeasurement, 'Work measurement not set for employee ' . $full_name_for_error_message, 'number');
        $arbeidsforhold->antallTimerPerUkeSomEnFullStillingTilsvarer = $employee->Workmeasurement;
        // work measurement type
        self::checkIfEmpty($employee->WorkTimeScheme, 'Work time scheme not set for employee ' . $full_name_for_error_message);
        $arbeidsforhold->avloenningstype =  $employee->WorkTimeScheme;
        // occupation, already checked above before query for occupation
        $arbeidsforhold->yrke = $occupation_code->YNr . $occupation_code->LNr;
        // work time scheme, ex. no shifts
        self::checkIfEmpty($employee->ShiftType, 'Shift type not set for employee ' . $full_name_for_error_message);
        $arbeidsforhold->arbeidstidsordning = $employee->ShiftType;
        // employment percentage
        self::checkIfEmpty($employee->WorkPercent, 'Work percent not set for employee ' . $full_name_for_error_message, 'number');
        $arbeidsforhold->stillingsprosent = (int) $employee->WorkPercent;
        // date of last change for payment date for salary
        $last_change_of_pay_date = $employee->CreditDaysUpdatedAt;
        self::checkIfEmpty($last_change_of_pay_date, 'Last change of salary pay date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold->sisteLoennsendringsdato = strftime('%F', strtotime($last_change_of_pay_date));
        // date of last change for position in company
        $last_change_of_position_in_company = $employee->inCurrentPositionSince;
        self::checkIfEmpty($last_change_of_position_in_company, 'Last change of position in company date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold->sisteDatoForStillingsprosentendring = strftime('%F', strtotime($last_change_of_position_in_company));
        // date of last change for work percentage
        $last_change_of_work_percentage = $employee->WorkPercentUpdatedAt;
        self::checkIfEmpty($last_change_of_pay_date, 'Last change of work percent date not set for employee ' . $full_name_for_error_message, 'date');
        $arbeidsforhold->loennsansiennitet = strftime('%F', strtotime($last_change_of_work_percentage));
        // work relation
        $inntektsmottaker->arbeidsforhold = $arbeidsforhold;

        // check if valid from and to dates are set
        self::checkIfEmpty($salary->ValidFrom, 'Valid from date not set for salary L' . $salary->JournalID, 'date');
        self::checkIfEmpty($salary->ValidTo, 'Valid to date not set for salary L' . $salary->JournalID, 'date');

        // income entries/salary lines
        $inntekt = null;
        $all_salary_lines_empty = true;
        foreach($this->salary_lines[$salary->SalaryID] as $salary_line) {
          if ($salary_line->AmountThisPeriod == 0) continue;
          else $all_salary_lines_empty = false;
          // if the code is 950 that is forskuddstrekk
          // if not that is a regular income entry
          if ($salary_line->SalaryCode == 950) $forskuddstrekk += (float) $salary_line->AmountThisPeriod;
          else {
            // fordel = determines if the income entry is positive or negative
            $inntekt->fordel = ($salary_line->LineNumber <= 69) ? 'kontantytelse' : 'utgiftsgodtgjoerelse';
            // boolean flags if the entry falls under some taxing regulation or not
            // both utloeserArbeidsgiveravgift and inngaarIGrunnlagForTrekk fields
            // first is true if the salary line code has -A in it
            // the second is true if we have a salary code for this entry
            $inntekt->utloeserArbeidsgiveravgift = (strstr($salary_line->SalaryCode, '-A')) ? true : false;
            $inntekt->inngaarIGrunnlagForTrekk = (!empty($salary_line->SalaryCode)) ? true : false;
            // start and end date for this income
            // already checked once outside the foreach loop
            $inntekt->startdatoOpptjeningsperiode = $salary->ValidFrom;
            $inntekt->sluttdatoOpptjeningsperiode = $salary->ValidTo;
            // amount for entry
            $inntekt->beloep = $salary_line->AmountThisPeriod;
            // calculate total for arbeidsgiveravgift amount
            $arbeidsgiveravgiftbeloep += $salary_line->AmountThisPeriod;
            // description for the entry
            $inntekt->loennsinntekt->beskrivelse = self::convertNorwegianLettersToASCII($salary_line->SalaryText);
            // there can be multiple entries for one salary so we add to an array
            $inntektsmottaker->inntekt[] = $inntekt;
          }
        }
        // check if all salary lines were empty/skipped(with 0 amount)
        $empty_salary = $all_salary_lines_empty ? '' : 'not empty';
        self::checkIfEmpty($empty_salary, 'Salary L' . $salary->JournalID . ' has only 0 amount lines');

        // get municipality tax percentage and zone info for arbeidsgiveravgift
        $company_municipality = $_lib['sess']->get_companydef('CompanyMunicipality');
        self::checkIfEmpty($company_municipality, 'Municipality for company not set');
        $query_kommune_tax = "SELECT agag.*
                              FROM arbeidsgiveravgift agag JOIN kommune k ON k.Sone = agag.Code
                              WHERE k.KommuneNumber = " . $company_municipality;
        $result_kommune_tax  = $_lib['db']->db_query($query_kommune_tax);
        $kommune_tax = $_lib['db']->db_fetch_object($result_kommune_tax);

        // amount for forskuddstrekk
        $inntektsmottaker->forskuddstrekk->beloep = $forskuddstrekk;
        // income reciever
        $virksomhet->inntektsmottaker[] = $inntektsmottaker;
      }
    }
    $loennOgGodtgjoerelse = null;
    // beregningskodeForArbeidsgiveravgift = calculation code for arbeidsgiveravgift
    $code_for_tax_calculation = $_lib['sess']->get_companydef('CalculationCodeForTax');
    self::checkIfEmpty($code_for_tax_calculation, 'Code for tax calculation not set on company');
    $loennOgGodtgjoerelse->beregningskodeForArbeidsgiveravgift = $code_for_tax_calculation;
    // amount
    $loennOgGodtgjoerelse->avgiftsgrunnlagBeloep = $arbeidsgiveravgiftbeloep;
    // taxing zone code, already checked above before the query
    self::checkIfEmpty($kommune_tax, 'Municipality selected for the company does not exist in the list of municipalities or does not have a zone code set');
    // Code property covered by the above check since it is the id for arbeidsgiveravggift table
    self::checkIfEmpty($kommune_tax->Percent, 'Municipality selected for the company does not have a tax percent set');
    $loennOgGodtgjoerelse->sone = $kommune_tax->Code;
    // taxing percent
    $loennOgGodtgjoerelse->prosentsatsForAvgiftsberegning = $kommune_tax->Percent;
    // loennOgGodtgjoerelse = salary and refunds
    $virksomhet->arbeidsgiveravgift->loennOgGodtgjoerelse = $loennOgGodtgjoerelse;
    $leveranse->oppgave->virksomhet = $virksomhet;
    $this->melding->Leveranse = $leveranse;
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
  function fetchSalaries() {
    global $_lib;
    // only the ones that have the altinn/actual pay date set
    $query_salaries = "SELECT s.*
                       FROM salary s
                       WHERE s.ActualPayDate LIKE  '" . $this->period . "%'";
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
    self::populateReportObject();
    if (empty($this->errors)) return self::generateXMLFromObject($this->melding);
    else return 'Error!';
  }

/* Helper function that generates an XML with the same
 * structure as the object that gets passed as an
 * argument.
 */
  function generateXMLFromObject($object_or_value, $first_time = true) {
    $doc = new DOMDocument('1.0', 'UTF-8');
    $doc->formatOutput = true;
    $node = null;
    if (!is_object($object_or_value) && $first_time) {
      // var_dump('DEBUG: first_time, non object');
      return $doc->saveXML();
    }
    if ($first_time) {
      // var_dump('DEBUG: first_time, melding object');
      // var_dump($object_or_value);
      $node = $doc->createElement('melding');
      $xmlns = $doc->createAttribute('xmlns');
      $xmlns->value = 'urn:ske:fastsetting:innsamling:a-meldingen:v2_0';
      $node->appendChild($xmlns);
      // var_dump('recursive call generateXMLFromObject');
      $tmp_nodes = self::generateXMLFromObject($object_or_value, false);
      // var_dump($tmp_nodes);
      foreach ($tmp_nodes->childNodes as $tmp_node) {
        // var_dump('append child ' . $tmp_node->nodeName . ' : ' . $tmp_node->nodeValue);
        $node_t = $doc->importNode($tmp_node, true);
        $node->appendChild($node_t);
      }
      $doc->appendChild($node);
    }
    else {
      // var_dump('DEBUG: melding object');
      // var_dump($object_or_value);
      $dummy_node = $doc->createElement('dummy');
      // var_dump('foreach object');
      foreach(get_object_vars($object_or_value) as $key => $value) {
        // var_dump('key ' . $key);
        // var_dump($value);
        if (!is_object($value)) {
          // var_dump('value not object');
          if(is_array($value)) {
            // var_dump('value is array of object');
            // var_dump('foreach object in array');
            foreach($value as $object_from_array) {
              $node = $doc->createElement($key);
              // var_dump('recursive call generateXMLFromObject');
              $tmp_nodes = self::generateXMLFromObject($object_from_array, false);
              // var_dump($tmp_nodes);
              foreach ($tmp_nodes->childNodes as $tmp_node) {
                // var_dump('append child ' . $tmp_node->nodeName . ' : ' . $tmp_node->nodeValue);
                $node_t = $doc->importNode($tmp_node, true);
                $node->appendChild($node_t);
              }
              $dummy_node->appendChild($node);
            }
          }
          else {
            $node_ = $doc->createElement($key, $value);
          }
        }
        else {
          // var_dump('value IS object');
          $node_ = $doc->createElement($key);
          // var_dump('recursive call generateXMLFromObject');
          $tmp_nodes = self::generateXMLFromObject($value, false);
          foreach ($tmp_nodes->childNodes as $tmp_node) {
            // var_dump('append child ' . $tmp_node->nodeName . ' : ' . $tmp_node->nodeValue);
            $node_t = $doc->importNode($tmp_node, true);
            $node_->appendChild($node_t);
          }
        }
        // var_dump('append child to dummy node');
        $dummy_node->appendChild($node_);
        // var_dump($dummy_node);
      }
    }
    if ($first_time) {
      // var_dump('returning final xml');
      return $doc->saveXML();
      // var_dump('returning final xml');
    }
    else {
      // var_dump('returning dummy node');
      // var_dump($dummy_node);
      return $dummy_node;
    }
  }
}

?>
