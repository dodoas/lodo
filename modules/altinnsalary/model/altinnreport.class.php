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

/* Helper function that populates the report(melding)
 * object
 */
  function populateReportObject() {
    global $_lib;

    // leveranse = deliver
    // delivery date
    $leveranse->leveringstidspunkt = strftime('%FT%TZ', time());
    // calendar month
    $leveranse->kalendermaaned = $this->period;
    // salary system
    $leveranse->kildesystem = 'LODO';

    // message id
    // save for future use in creation of SOAP request
    $meldings_id = 'Report for ' . $_lib['sess']->get_companydef('OrgNumber') . ' at ' . strftime('%FT%TZ', time());
    $leveranse->meldingsId = $meldings_id;
    $this->meldingsId = $meldings_id;

    // opplysningspliktig = reportee
    // norskIdentifikator = norwegian identifier, personal id or company org number
    $leveranse->opplysningspliktig->norskIdentifikator = $_lib['sess']->get_companydef('OrgNumber'); 

    // select salary and the employee connected to it
    // TODO: loop through all instead of just using the first one
    $salary = $this->salaries[0];
    $employee = $this->employees[$salary->AccountPlanID];

    // get the occupation of the employee in the company
    $query_occupation = "SELECT * FROM occupation WHERE OccupationID = " . $employee->OccupationID;
    $result_occupation  = $_lib['db']->db_query($query_occupation);
    $occupation_code = $_lib['db']->db_fetch_object($result_occupation);

    // norwegian id for company the employee works for
    $virksomhet->norskIdentifikator = $_lib['sess']->get_companydef('OrgNumber');
    // norwegian id for the employee, personal id number
    $inntektsmottaker->norskIdentifikator = $employee->SocietyNumber;
    // name and birthdate
    $inntektsmottaker->identifiserendeInformasjon->navn = $employee->FirstName . ' ' . $employee->LastName;
    $inntektsmottaker->identifiserendeInformasjon->foedselsdato = strftime('%F', strtotime($employee->BirthDate));
    // employment date
    $arbeidsforhold->startdato = strftime('%F', strtotime($employee->WorkStart));
    // type of employment
    $arbeidsforhold->typeArbeidsforhold = $employee->TypeOfEmployment;
    // work measurement, ex. hours per week
    $arbeidsforhold->antallTimerPerUkeSomEnFullStillingTilsvarer = $employee->Workmeasurement;
    // work measurement type
    // TODO: checkout what values this field can take and do not hardcode
    $arbeidsforhold->avloenningstype = 'fast';
    // occupation
    $arbeidsforhold->yrke = $occupation_code->YNr . $occupation_code->LNr;
    // work time scheme, ex. no shifts
    $arbeidsforhold->arbeidstidsordning = $employee->ShiftType;
    // employment percentage
    $arbeidsforhold->stillingsprosent = (int) $employee->WorkPercent;
    // date of last change for payment date for salary
    $arbeidsforhold->sisteLoennsendringsdato = strftime('%F', strtotime($employee->CreditDaysUpdatedAt));
    // date of last change for position in company
    $arbeidsforhold->sisteDatoForStillingsprosentendring = strftime('%F', strtotime($employee->inCurrentPositionSince));
    // date of last change for work percentage
    $arbeidsforhold->loennsansiennitet = strftime('%F', strtotime($employee->WorkPercentUpdatedAt));
    // work relation
    $inntektsmottaker->arbeidsforhold = $arbeidsforhold;
    // income reciever
    $virksomhet->inntektsmottaker = $inntektsmottaker;

    // government tax
    // forskuddstrekk = tax that is taken from the employee
    // arbeidsgiveravgift = tax that the company pays
    $forskuddstrekk = 0.0;
    $arbeidsgiveravgiftbeloep = 0.0;
    // income entries/slary lines
    foreach($this->salary_lines[$salary->SalaryID] as $salary_line) {
      // if the code is 950 that is forskuddstrekk
      // if not that is a regular income entry
      if ($salary_line->SalaryCode == 950) $forskuddstrekk += (float) $salary_line->AmountThisPeriod;
      else {
        if ($salary_line->AmountThisPeriod == 0) continue;
        // fordel = determines if the income entry is positive or negative
        $inntekt->fordel = ($salary_line->LineNumber <= 69) ? 'kontantytelse' : 'utgiftsgodtgjoerelse';
        // boolean flags if the entry falls under some taxing regulation or not
        // both utloeserArbeidsgiveravgift and inngaarIGrunnlagForTrekk fields
        // first is true if the salary line code has -A in it
        // the second is true if we have a salary code for this entry
        $inntekt->utloeserArbeidsgiveravgift = (strstr($salary_line->SalaryCode, '-A')) ? true : false;
        $inntekt->inngaarIGrunnlagForTrekk = (!empty($salary_line->SalaryCode)) ? true : false;
        // start and end date for this income
        $inntekt->startdatoOpptjeningsperiode = $salary->ValidFrom;
        $inntekt->sluttdatoOpptjeningsperiode = $salary->ValidTo;
        // amount for entry
        $inntekt->beloep = $salary_line->AmountThisPeriod;
        // calculate total for arbeidsgiveravgift amount
        $arbeidsgiveravgiftbeloep += $salary_line->AmountThisPeriod;
        // description for the entry
        $inntekt->loennsinntekt->beskrivelse = self::convertNorwegianLettersToASCII($salary_line->SalaryText);
        // there can be multiple entries for one salary so we add to an array
        $virksomhet->inntektsmottaker->inntekt[] = $inntekt;
      }
    }

    // get municipality tax percentage and zone info for arbeidsgiveravgift
    $query_kommune_tax = "SELECT agag.*
                          FROM arbeidsgiveravgift agag JOIN kommune k ON k.Sone = agag.Code
                          WHERE k.KommuneNumber = " . $_lib['sess']->get_companydef('CompanyMunicipality');
    $result_kommune_tax  = $_lib['db']->db_query($query_kommune_tax);
    $kommune_tax = $_lib['db']->db_fetch_object($result_kommune_tax);

    // amount for forskuddstrekk
    $virksomhet->inntektsmottaker->forskuddstrekk->beloep = $forskuddstrekk;
    // TODO: implement field beregningskodeForArbeidsgiveravgift
    // use convertNorwegianLettersToASCII since we might have it as utf8 in the db
    // beregningskodeForArbeidsgiveravgift = calculation code for arbeidsgiveravgift
    $loennOgGodtgjoerelse->beregningskodeForArbeidsgiveravgift = self::convertNorwegianLettersToASCII('generelleNaeringer');
    // amount
    $loennOgGodtgjoerelse->avgiftsgrunnlagBeloep = $arbeidsgiveravgiftbeloep;
    // taxing zone code
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
      $this->salaries[] = $salary;
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
  function generateXML($args) {
    self::populateReportObject();
    return self::generateXMLFromObject($this->melding);
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
              $node_->appendChild($node);

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
