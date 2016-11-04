<?
/* Altinn report class
 * Includes all the linked salaries and employee info
 * needed for the report.
 */

// include validation class
includecodelib('validation/validation');

class altinn_report {
  public $salaries               = array();
  public $salary_ids             = array();
  public $salary_lines           = array();
  public $employees              = array();
  public $work_relations         = array();
  public $work_relation_ids      = array();
  public $only_register_employee = false;
  public $period                 = '';
  public $melding                = null; // structured object that contains the report
  public $meldingsId             = null; // mesasge id
  public $erstatterMeldingsId    = null; // replacement message id
  public $errors                 = null; // to be populated if errors occur

  public $is_cancellation        = false;

/* Constructor accepts the accounting period(year-month)
 * salary, work relation ids, and flag if we are sending for only
 * one employee.
 * It automatically loads all the salaries and employees.
 */
  function __construct($period, $salary_ids = array(), $work_relation_ids = array(), $only_register_employee = false, $just_test_if_ready = false, $is_cancellation = false) {
    // if no period selected, exit
    if (empty($period)) return;
    else $this->period = $period;

    $this->just_test_if_ready = $just_test_if_ready;
    $this->is_cancellation = $is_cancellation;

    // TODO: Use and update initialize method and remove this below
    $this->only_register_employee = $only_register_employee;
    $this->salary_ids = $salary_ids;
    $this->work_relation_ids = $work_relation_ids;
    // fetch the salaries
    self::fetchSalaries();
    // fetch the employees
    self::fetchEmployeesAndWorkRelations();
  }

/* Helper function to initialize all the needed parameters
 * both salary_ids and work_relation_ids
 */
  function initialize($salary_ids, $work_relation_ids) {
    self::setSalaryIDs($salary_ids);
    self::setWorkRelationsIDs($work_relation_ids);
  }

/* Helper function to set which salaries to be included
 */
  function setSalaryIDs($salary_ids) {
    $this->salary_ids = $salary_ids;
  }

/* Helper function to set which work relations to be included
 */
  function setWorkRelationIDs($work_relation_ids) {
    $this->work_relation_ids = $work_relation_ids;
  }

/* Helper function to add replacement message id
 */
  function addReplacementMessageID($message_id) {
    $this->erstatterMeldingsId = $message_id;
  }

/* Helper function to check if the variable is a valid date
 */
  function isValidFurloughPercent($field) {
    return is_numeric($field) && $field > 0 && $field <= 100;
  }

/* Helper function to check if the variable is valid
 * calls a sub function for for date, percent, ...
 */
  function checkIfValid($field, $error_message, $type = '') {
    if ($type == 'date') $is_valid = validation::date($field);
    elseif ($type == 'furlough_percent') $is_valid = self::isValidFurloughPercent($field);
    else {
      $error_message = 'Unknown type ' . $type;
      $is_valid = false;
    }
    if (!$is_valid) $this->errors[] = $error_message;
    return $is_valid;
  }

/* Helper function to check if the variable is empty
 * calls a sub function for for string, date, amount/number,
 * org_number, name check based on the type
 */
  function checkIfEmpty($field, $error_message, $type = 'string') {
    global $_lib;
    if ($type == 'string') $is_empty = empty($field);
    elseif ($type == 'date') $is_empty = strstr($field, '0000-00-00') || empty($field);
    elseif ($type == 'number') $is_empty = empty($field) || ($field == 0);
    elseif ($type == 'percent') $is_empty = is_null($field);
    elseif ($type == 'org_number') $is_empty = !preg_match('/^([0-9]{9})$/', $field);
    elseif ($type == 'name') $is_empty = !preg_match(utf8_encode('/^([A-Za-zæøåäöÆØÅÄÖ\s]+)$/'), utf8_encode($field));
    elseif ($type == 'boolean') $is_empty = $field;
    elseif ($type == 'personal_number') $is_empty = !$_lib['validation']->mod11_personal($field);
    else {
      $error_message = 'Unknown type ' . $type;
      $is_empty = true;
    }
    if ($is_empty) $this->errors[] = $error_message;
    return $is_empty;
  }

/* Helper function that returns the full name for the employee.
 */
  function fullName($employee) {
    return $employee->FirstName . ' ' . $employee->LastName;
  }

/* Helper function that returns the full name and account plan id
 * for the employee. Used for error messages.
 */
  function fullNameForErrorMessage($employee) {
    return self::fullName($employee) . '(' . $employee->AccountPlanID . ')';
  }

/* Helper function that populates the report(melding) array
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
    if ($this->is_cancellation) {
      $cancelled_report_timestamp = substr($this->erstatterMeldingsId, 24);
      $meldings_id .= "_annulering_for_". $cancelled_report_timestamp;
    }

    $leveranse['meldingsId'] = $meldings_id;
    $this->meldingsId = $meldings_id;

    // opplysningspliktig = reportee
    // norskIdentifikator = norwegian identifier, personal id or company org number
    $leveranse['opplysningspliktig'] = array();
    $leveranse['opplysningspliktig']['norskIdentifikator'] = $org_number;

    // beregningskodeForArbeidsgiveravgift = calculation code for arbeidsgiveravgift
    $code_for_tax_calculation = $_lib['sess']->get_companydef('CalculationCodeForTax');
    // Error is: Code for tax calculation not set on company
    self::checkIfEmpty($code_for_tax_calculation, 'Firmaopplysning: Mangler beregningskode for arbeidsgiveravgift for firma');

    $virksomhet_array = array();
    // select salary and the employee connected to it
    if ($this->just_test_if_ready && empty($this->employees)) {
      $this->errors[] = 'Arbeidsforhold er ikke valgt';
    } else {
      // Error is: No employees for this period
      self::checkIfEmpty($this->employees, 'Det er ingen ansatte i perioden');
    }
    // Error is: No salaries for this period
    if (!$this->only_register_employee) self::checkIfEmpty($this->salaries, 'Det er ingen l&oslash;nnslipper  i perioden');
    foreach($this->employees as $key_subcompany => $employees) {
      // generate subcompany array with all the salaries for that company
      // sumForskuddstrekk and sumArbeidsgiveravgift are sent by reference and are affected outsude the function as well
      $virksomhet = self::generateVirksomhetArray($key_subcompany, $this->salaries[$key_subcompany], $this->work_relations[$key_subcompany], $code_for_tax_calculation, $sumForskuddstrekk, $sumArbeidsgiveravgift);

      if ($virksomhet) $virksomhet_array[]['virksomhet'] = $virksomhet;
    }
    $leveranse['oppgave'] = array();
    $leveranse['oppgave']['betalingsinformasjon'] = array();
    $leveranse['oppgave']['betalingsinformasjon']['sumForskuddstrekk'] = (int) floor($sumForskuddstrekk);
    $leveranse['oppgave']['betalingsinformasjon']['sumArbeidsgiveravgift'] = (int) floor($sumArbeidsgiveravgift);
    foreach($virksomhet_array as $one_virksomhet) {
      $leveranse['oppgave'][] = $one_virksomhet;
    }
    $melding['Leveranse'] = $leveranse;
    $this->melding = $melding;
  }

/* Helper function to generate a subcompany array
 * Affects $sumForskuddstrekk and $sumArbeidsgiveravgift vars.
 */
  function generateVirksomhetArray($key_subcompany, $salaries, $work_relations, $code_for_tax_calculation, &$sumForskuddstrekk, &$sumArbeidsgiveravgift) {
    // used for arbeidsgiveravgift node
    $loennOgGodtgjoerelse = array();
    $virksomhet = array();
    $use_loennOgGodtgjoerelse = false;
    foreach($this->employees[$key_subcompany] as $key_employee => $employee) {
      // if there is no salaries for current subcompany and current employee, just generate work relation
      // because we do not want to try to loop over a null value
      $work_relation = $work_relations[$key_employee];
      if (empty($salaries[$employee->AccountPlanID])) {
        $inntektsmottaker = self::generateInntektsmottakerArray($key_subcompany, NULL, $employee, $work_relation, $code_for_tax_calculation, $virksomhet, $loennOgGodtgjoerelse, $sumForskuddstrekk, true);
        // income receiver
        $inntektsmottaker ? $virksomhet[] = $inntektsmottaker : NULL;
      } else {
        if (count($salaries[$employee->AccountPlanID]) > 1) {
          // Error is: There is more than 1 salary<L 1, L 3> for <name> in this report
          $l_names = array_map(function($salary) {
            return "L ". $salary->JournalID;
          }, $salaries[$employee->AccountPlanID]);

          $msg = 'Det er mere enn 1 l&oslash;nnslipp('.implode($l_names, ', ').') for '.$this->fullNameForErrorMessage($employee).' i denne rapporten';
          self::checkIfEmpty(true, $msg, 'boolean');
        }

        foreach($salaries[$employee->AccountPlanID] as $key_salary => $salary) {
          if (!$salary->LockedBy && !$this->just_test_if_ready) {
            $this->errors[] = "L&oslash;nnslipp L" . $salary->JournalID . " er ikke l&aring;st. L&oslash;nslippen m&aring; l&aring;ses f&oslash;r du kan sende den til Altinn.";
          }
          // generate income receiver array
          // virksomhet, loennOgGodtgjoerelse and sumForskuddstrekk are affected in this function because they are sent by reference
          $inntektsmottaker = self::generateInntektsmottakerArray($key_subcompany, $salary, $employee, $work_relation, $code_for_tax_calculation, $virksomhet, $loennOgGodtgjoerelse, $sumForskuddstrekk);
          $use_loennOgGodtgjoerelse = true;

          $inntektsmottaker ? $virksomhet[] = $inntektsmottaker : NULL;
        }
      }
    }
    // loennOgGodtgjoerelse = salary and refunds
    if ($use_loennOgGodtgjoerelse) {
      $virksomhet['arbeidsgiveravgift'] = $loennOgGodtgjoerelse;
    }
    foreach($loennOgGodtgjoerelse as $zone_tax_array) {
      $zone_tax = $zone_tax_array['loennOgGodtgjoerelse'];
      $sumArbeidsgiveravgift += $zone_tax['avgiftsgrunnlagBeloep'] * $zone_tax['prosentsatsForAvgiftsberegning']/100.0;
    }
    return $virksomhet;
  }

/* Helper function to generate an incomereciever array
 * Affects $virksomhet, $loennOgGodtgjoerelse and $sumForskuddstrekk vars.
 * use_only_employee_info is set if we only want the work relation and not the incomes
 */
  function generateInntektsmottakerArray($key_subcompany, $salary, $employee, $work_relation, $code_for_tax_calculation, &$virksomhet, &$loennOgGodtgjoerelse, &$sumForskuddstrekk, $use_only_employee_info = false) {
    global $_lib;
    // subcompany is the virksomhet for which we report this salary
    if ($use_only_employee_info) {
      // Error is: No subcompany selected for employee ' . self::fullNameForErrorMessage($employee)
      $org_number_set = !self::checkIfEmpty($key_subcompany, 'Ansatt og virksomhet: Mangler virksomhet p&aring; ' . self::fullNameForErrorMessage($employee));
    } else {
      // Error is: No subcompany selected for salary L' . $salary->JournalID
      $org_number_set = !self::checkIfEmpty($key_subcompany, 'L&oslash;nnslipp og virksomhet: Mangler virksomhet p&aring; L' . $salary->JournalID);
    }

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

    if($this->is_cancellation) {
      if(!$use_only_employee_info) {
        $zone_code = null; // will be changed in the function below
        self::setLoennOgGodtgjoerelse($salary, $code_for_tax_calculation, $loennOgGodtgjoerelse, $zone_code);
      }
      return array();
    }

    // forskuddstrekk = tax that is taken from the employee
    $forskuddstrekk = 0.0;

    // norwegian id for the employee, personal id number
    $society_number = $employee->SocietyNumber; // check personal id
    $id_number = $employee->IDNumber;
    // Error is: Personal ID number(society number) not set for employee ' . self::fullNameForErrorMessage($employee)
    $society_and_id_number_are_empty = self::checkIfEmpty($society_number.$id_number, 'Ansatt: Mangler personnummer og ID nummer for ' . self::fullNameForErrorMessage($employee));
    // Error is: Personal ID must be valid by mod11
    if(!$society_and_id_number_are_empty && !empty($society_number)) self::checkIfEmpty($society_number, "Ansatt: Personnr m&aring; v&aelig;re gyldig mod11 for " . self::fullNameForErrorMessage($employee), 'personal_number');
    else if(!$society_and_id_number_are_empty && !empty($id_number)) self::checkIfEmpty($id_number, "Ansatt: ID nummer m&aring; v&aelig;re gyldig mod11 for " . self::fullNameForErrorMessage($employee), 'personal_number');

    $inntektsmottaker['inntektsmottaker']['norskIdentifikator'] = !empty($society_number) ? $society_number : $id_number;
    // name and birthdate
    $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon'] = array();
    $full_name = self::fullName($employee);
    // Error is: First and/or last name not set for employee ' . self::fullNameForErrorMessage($employee)
    self::checkIfEmpty($full_name, 'Ansatt: Mangler fornavn og/eller etternavn for ' . self::fullNameForErrorMessage($employee));
    // Error is: First and/or last name contains an unsupported character for employee ' . self::fullNameForErrorMessage($employee)
    self::checkIfEmpty($full_name, 'Ansatt: Fornavn og/eller etternavn inneholder en ust&oslash;ttet bokstav for ' . self::fullNameForErrorMessage($employee), 'name');
    $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['navn'] = $full_name;
    $birth_date = $employee->BirthDate;
    // Error is: Birth date not set for employee ' . self::fullNameForErrorMessage($employee)
    self::checkIfEmpty($birth_date, 'Ansatt: Mangler f&oslash;dselsdag for ' . self::fullNameForErrorMessage($employee), 'date');
    $inntektsmottaker['inntektsmottaker']['identifiserendeInformasjon']['foedselsdato'] = strftime('%F', strtotime($birth_date));

    // generate work relation array
    if ($use_only_employee_info) {
      $arbeidsforhold = self::generateArbeidsforholdArray($salary, $employee, $work_relation, true);
    } else {
      $arbeidsforhold = self::generateArbeidsforholdArray($salary, $employee, $work_relation);
    }

    // work relation
    $inntektsmottaker['inntektsmottaker']['arbeidsforhold'] = $arbeidsforhold;

    if (!$use_only_employee_info) {
      // check if valid from and to dates are set
      // Error is: Valid from date not set for salary L' . $salary->JournalID, 'date
      self::checkIfEmpty($salary->ValidFrom, 'L&oslash;nnslipp: Manger til dato p&aring; L' . $salary->JournalID, 'date');
      // Error is: Valid to date not set for salary L' . $salary->JournalID, 'date
      self::checkIfEmpty($salary->ValidTo, 'L&oslash;nnslipp: Manger til dato p&aring; L' . $salary->JournalID, 'date');

      // Set/initialize loennOgGodtgjoerelse array for each zone for salary
      $zone_code = null; // will be changed in the function below
      self::setLoennOgGodtgjoerelse($salary, $code_for_tax_calculation, $loennOgGodtgjoerelse, $zone_code);

      // $forskuddstrekk and $loennOgGodtgjoerelse are sent by reference and are affected outside the function as well
      $inntekt_tmp = self::generateInntektArray($this->salary_lines[$salary->SalaryID], $salary, $forskuddstrekk, $loennOgGodtgjoerelse, $zone_code);

      // amount for forskuddstrekk
      $inntektsmottaker['inntektsmottaker']['forskuddstrekk'] = array();
      $inntektsmottaker['inntektsmottaker']['forskuddstrekk']['beloep'] = -$forskuddstrekk;
      $sumForskuddstrekk += $forskuddstrekk;

      foreach($inntekt_tmp as $single_inntekt) {
        $inntektsmottaker['inntektsmottaker'][] = $single_inntekt;
      }
    }
    return $inntektsmottaker;
  }
/* Helper function that set/initializes values for tax array for each zone.
 * Returns nothing but affects $loennOgGodtgjoerelse and $zone_code vars.
 */
  function setLoennOgGodtgjoerelse($salary, $code_for_tax_calculation, &$loennOgGodtgjoerelse, &$zone_code) {
    global $_lib;
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
    self::checkIfEmpty($kommune_tax->Percent, 'L&oslash;nnslipp: Mangler prosent for komune valgt p&aring;  L' . $salary->JournalID, 'percent');
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
  }

/* Helper function that generates the array with work relation for one employee.
 * Returns the arbeidsforhold array that is to be included in the report array.
 * use_only_employee_info is set if we only want the work relation and not the incomes
 */
  function generateArbeidsforholdArray($salary, $employee, $work_relation, $use_only_employee_info = false) {
    global $_lib;

    // get the occupation of the employee in the company
    if ($use_only_employee_info) {
      // Error is: Occupation not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
      self::checkIfEmpty($work_relation->OccupationID, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler yrke p&aring; ' . self::fullNameForErrorMessage($employee));
      $occupation_id = $work_relation->OccupationID;
    } else {
      // Error is: Occupation not set for salary L' . $salary->JournalID
      self::checkIfEmpty($salary->OccupationID, 'L&oslash;nnslipp: Mangler yrke p&aring; L' . $salary->JournalID);
      $occupation_id = $salary->OccupationID;
    }
    $query_occupation = "SELECT * FROM occupation WHERE OccupationID = " . (int)$occupation_id;
    $result_occupation  = $_lib['db']->db_query($query_occupation);
    $occupation_code = $_lib['db']->db_fetch_object($result_occupation);
    if ($use_only_employee_info) {
      // Error is: Occupation does not exist in the occupation list self::fullNameForErrorMessage($employee)
      self::checkIfEmpty($occupation_code, 'Ansatt: Yrket finnes ikke i listen over yrker ' . self::fullNameForErrorMessage($employee));
    } else {
      // Error is: Occupation does not exist in the occupation list
      self::checkIfEmpty($occupation_code, 'L&oslash;nnslipp: Yrket finnes ikke i listen over yrker');
    }
    // type of employment
    if ($use_only_employee_info) {
      // Error is: Employment type not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
      self::checkIfEmpty($work_relation->TypeOfEmployment, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler ansettelsestype p&aring; ' . self::fullNameForErrorMessage($employee));
      $arbeidsforhold['typeArbeidsforhold'] = $work_relation->TypeOfEmployment;
    } else {
      // Error is: Employment type not set for salary L' . $salary->JournalID
      self::checkIfEmpty($salary->TypeOfEmployment, 'L&oslash;nnslipp: Mangler ansettelsestype p&aring; L' . $salary->JournalID);
      $arbeidsforhold['typeArbeidsforhold'] = $salary->TypeOfEmployment;
    }

    if (self::shouldWeHaveWorkPeriode($arbeidsforhold['typeArbeidsforhold'])){
      if ($use_only_employee_info) {
        $work_start = $work_relation->WorkStart;
        $work_stop = $work_relation->WorkStop;
        // Error is: Employment date not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_start, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler Ansettelsesdato for ' . self::fullNameForErrorMessage($employee), 'date');
      } else {
        $work_start = $salary->WorkStart;
        $work_stop = $salary->WorkStop;
        // Error is: Employment date not set for salary L ' . salary->JournalID
        self::checkIfEmpty($work_start, 'L&oslash;nnslipp: Mangler Ansettelsesdato p&aring; L' . $salary->JournalID, 'date');
      }
      // employment date
      $arbeidsforhold['startdato'] = strftime('%F', strtotime($work_start));
      // set end date only if the date is not 0000-00-00 otherwise set to null
      if (!strstr($work_stop, '0000-00-00')) $arbeidsforhold['sluttdato'] = strftime('%F', strtotime($work_stop));
    }

    if (self::shouldWeHaveWorkmeasurement($arbeidsforhold['typeArbeidsforhold'])){
      // work measurement, ex. hours per week
      if ($use_only_employee_info) {
        $work_measurement = $work_relation->WorkMeasurement;
        // Error is: Work measurement not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_measurement, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler arbeidsdtimer hver uke for ' . self::fullNameForErrorMessage($employee), 'number');
      } else {
        $work_measurement = $salary->WorkMeasurement;
        // Error is: Work measurement not set for salary L' . salary->JournalID
        self::checkIfEmpty($work_measurement, 'L&oslash;nnslipp: Mangler arbeidsdtimer hver uke p&aring; L' . $salary->JournalID, 'number');
      }
      $arbeidsforhold['antallTimerPerUkeSomEnFullStillingTilsvarer'] = $work_measurement;
    }

    if (self::shouldWeHaveWorkTimeScheme($arbeidsforhold['typeArbeidsforhold'])){
      // work measurement type
      if ($use_only_employee_info) {
        // Error is: Work time scheme not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_relation->WorkTimeScheme, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler arbeidstid for ' . self::fullNameForErrorMessage($employee));
        $arbeidsforhold['avloenningstype'] =  $work_relation->WorkTimeScheme;
      } else {
        // Error is: Work time scheme not set for salary L' . $salary->JournalID
        self::checkIfEmpty($salary->WorkTimeScheme, 'L&oslash;nnslipp: Mangler arbeidstid for L' . $salary->JournalID);
        $arbeidsforhold['avloenningstype'] =  $salary->WorkTimeScheme;
      }
    }

    if (self::shouldWeHaveOccupation($arbeidsforhold['typeArbeidsforhold'])){
      // occupation, already checked above before query for occupation
      $arbeidsforhold['yrke'] = $occupation_code->YNr . $occupation_code->LNr;
    }

    if (self::shouldWeHaveShift($arbeidsforhold['typeArbeidsforhold'])){
      // work time scheme, ex. no shifts
      if ($use_only_employee_info) {
        // Error is: Shift type not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_relation->ShiftType, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler skifttype ' . self::fullNameForErrorMessage($employee));
        $arbeidsforhold['arbeidstidsordning'] = $work_relation->ShiftType;
      } else {
        // Error is: Shift type not set for salary L' . $salary->JournalID
        self::checkIfEmpty($salary->ShiftType, 'L&oslash;nnslipp: Mangler skifttype L' . $salary->JournalID);
        $arbeidsforhold['arbeidstidsordning'] = $salary->ShiftType;
      }
    }

    if (self::shouldWeHaveWorkPercent($arbeidsforhold['typeArbeidsforhold'])){
      // employment percentage
      if ($use_only_employee_info) {
        $work_percent = $work_relation->WorkPercent;
        // Error is: Work percent not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_percent, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler stillingsprosent for ' . self::fullNameForErrorMessage($employee), 'number');
      } else {
        $work_percent = $salary->WorkPercent;
        // Error is: Work percent not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($work_percent, 'L&oslash;nnslipp: Mangler stillingsprosent p&aring; L' . $salary->JournalID, 'number');
      }
      $arbeidsforhold['stillingsprosent'] = (int) $work_percent;
    }

    if (self::shouldWeHaveCreditDaysUpdatedAt($arbeidsforhold['typeArbeidsforhold'])){
      // date of last change for payment date for salary
      if ($use_only_employee_info) {
        $last_change_of_pay_date = $work_relation->SalaryDateChangedAt;
        // Error is: Last change of salary pay date not set for work relation #id for employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($last_change_of_pay_date, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler siste l&oslash;nnsendrings dato for ' . self::fullNameForErrorMessage($employee), 'date');
      } else {
        $last_change_of_pay_date = $salary->SalaryDateChangedAt;
        // Error is: Last change of salary pay date not set for salary L ' . salary->JournalID
        self::checkIfEmpty($last_change_of_pay_date, 'L&oslash;nnslipp: Mangler siste l&oslash;nnsendrings dato p&aring; L' . $salary->JournalID, 'date');
      }
      $arbeidsforhold['sisteLoennsendringsdato'] = strftime('%F', strtotime($last_change_of_pay_date));
    }

    if (self::shouldWeHaveCurrentPositionSince($arbeidsforhold['typeArbeidsforhold'])){
      // date of last change for position in company
      if ($use_only_employee_info) {
        $last_change_of_position_in_company = $work_relation->InCurrentPositionSince;
        // Error is: Last change of position in company date not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($last_change_of_position_in_company, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler siste posisjonendringsdato for ' . self::fullNameForErrorMessage($employee), 'date');
      } else {
        $last_change_of_position_in_company = $salary->InCurrentPositionSince;
        // Error is: Last change of position in company date not set for salary L' . salary->JournalID
        self::checkIfEmpty($last_change_of_position_in_company, 'L&oslash;nnslipp: Mangler siste posisjonendringsdato p&aring; L' . $salary->JournalID, 'date');
      }
      $arbeidsforhold['loennsansiennitet'] = strftime('%F', strtotime($last_change_of_position_in_company));
    }

    // select all furlough active in the report period
    $query_furlough = "SELECT * FROM workrelationfurlough WHERE WorkRelationID = " . $work_relation->WorkRelationID . " AND (('" . $this->period . "-01' BETWEEN Start AND Stop) OR ('" . $this->period . "-01' > Start AND (Stop = '0000-00-00' OR Stop IS NULL)) OR ((YEAR(Start) = YEAR('" . $this->period . "-01') AND MONTH(Start) = MONTH('" . $this->period . "-01')) OR (YEAR(Stop) = YEAR('" . $this->period . "-01') AND MONTH(Stop) = MONTH('" . $this->period . "-01'))))";
    $result_furlough = $_lib['db']->db_query($query_furlough);
    while($furlough = $_lib['db']->db_fetch_object($result_furlough)) {
      $permisjon = array();

      // Error is: Furlough: Start date is missing on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
      $furlough_start_empty = self::checkIfEmpty($furlough->Start,
        'Permisjon: Mangler startdato for ' .
        self::fullNameForErrorMessage($employee) .
        ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
        ' permisjon('.$furlough->FurloughID.')', 'date');
      if (!$furlough_start_empty) {
        // Error is: Furlough: Start date is not valid on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
        self::checkIfValid($furlough->Start,
          'Permisjon: Startdato(' . $furlough->Start . ') er ikke gyldig for ' .
          self::fullNameForErrorMessage($employee) .
          ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
          ' permisjon('.$furlough->FurloughID.')', 'date');
      }
      $permisjon['startdato'] = $furlough->Start;

      // Error is: Furlough: End date is missing on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
      $furlough_end_empty = strstr($furlough->Stop, '0000-00-00') || empty($furlough->Stop);
      if (!$furlough_end_empty) {
        // Error is: Furlough: End date is not valid on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
        self::checkIfValid($furlough->Stop,
          'Permisjon: Sluttdato(' . $furlough->Stop . ') er ikke gyldig for ' .
          self::fullNameForErrorMessage($employee) .
          ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
          ' permisjon('.$furlough->FurloughID.')', 'date');
        $permisjon['sluttdato'] = $furlough->Stop;
      }

      if (!$furlough_start_empty && !$furlough_end_empty && ($furlough->Stop < $furlough->Start)) {
        $this->errors[] = 'Permisjon: Sluttdato kan ikke v&aelig;re f&oslash;r startdato for ' .
        self::fullNameForErrorMessage($employee) .
        ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
        ' permisjon('.$furlough->FurloughID.')';
      }

      // Error is: Furlough: Percent is missing on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
      $percentage_empty = self::checkIfEmpty($furlough->Percent,
        'Permisjon: Mangler prosent for ' .
        self::fullNameForErrorMessage($employee) .
        ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
        ' permisjon('.$furlough->FurloughID.')', 'percent');
      if (!$percentage_empty) {
        // Error is: Furlough: Percent is not valid on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
        self::checkIfValid($furlough->Percent,
          'Permisjon: Prosent(' . $furlough->Percent . ') er ikke gyldig for ' .
          self::fullNameForErrorMessage($employee) .
          ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
          ' permisjon('.$furlough->FurloughID.')', 'furlough_percent');
      }
      $permisjon['permisjonsprosent'] = $furlough->Percent;

      // Error is: Furlough: Text is missing on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
      self::checkIfEmpty($furlough->Text,
        'Permisjon: Mangler text for ' .
        self::fullNameForErrorMessage($employee) .
        ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
        ' permisjon('.$furlough->FurloughID.')');
      $permisjon['permisjonId'] = $furlough->Text;

      // Error is: Furlough: Description is missing on self::fullNameForErrorMessage($employee) on work relation($furlough->WorkRelationID ) furlough( $furlough->FurloughID )
      self::checkIfEmpty($furlough->Description,
        'Permisjon: Mangler beskrivelse for ' .
        self::fullNameForErrorMessage($employee) .
        ' p&aring; arbeidsforhold('.$furlough->WorkRelationID.')'.
        ' permisjon('.$furlough->FurloughID.')');
      $permisjon['beskrivelse'] = $furlough->Description;

      $arbeidsforhold[]['permisjon'] = $permisjon;
    }

    if (self::shouldWeHaveWorkPercent($arbeidsforhold['typeArbeidsforhold'])){
      // date of last change for work percentage
      if ($use_only_employee_info) {
        $last_change_of_work_percentage = $work_relation->WorkPercentUpdatedAt;
        // Error is: Last change of work percent date not set for work relation #id on employee ' . self::fullNameForErrorMessage($employee)
        self::checkIfEmpty($last_change_of_work_percentage, 'Arbeidsforhold ' . $work_relation->WorkRelationID . ': Mangler stillingsprosentendret for' . self::fullNameForErrorMessage($employee), 'date');
      } else {
        $last_change_of_work_percentage = $salary->WorkPercentUpdatedAt;
        // Error is: Last change of work percent date not set for salary L' . salary->JournalID
        self::checkIfEmpty($last_change_of_work_percentage, 'L&oslash;nnslipp: Mangler stillingsprosentendret p&aring; L' . $salary->JournalID, 'date');
      }
      $arbeidsforhold['sisteDatoForStillingsprosentendring'] = strftime('%F', strtotime($last_change_of_work_percentage));
    }

    return $arbeidsforhold;
  }

/* Helper function that generates the array of incomes from one salaries income lines.
 * Returns the inntekt array that is to be included in the report array.
 * Also affects two variables that are sent by reference: forskuddstrekk and loennOgGodtgjoerelse.
 */
  function generateInntektArray($salary_lines, $salary, &$forskuddstrekk, &$loennOgGodtgjoerelse, $zone_code) {
    // income entries/salary lines
    $inntekt_tmp = array();
    $all_salary_lines_empty = true;
    foreach($salary_lines as $salary_line) {
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
        self::checkIfEmpty($salary_line->Fordel, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . " med text '" . $salary_line->SalaryText . "' har ikke satt fordel");
        $inntekt['inntekt']['fordel'] = $salary_line->Fordel;

        // boolean flags if the entry falls under some taxing regulation or not
        $inntekt['inntekt']['utloeserArbeidsgiveravgift'] = $salary_line->EnableEmployeeTax ? 'true' : 'false';
        $inntekt['inntekt']['inngaarIGrunnlagForTrekk'] = $salary_line->MandatoryTaxSubtraction ? 'true' : 'false';
        // amount for entry
        $inntekt['inntekt']['beloep'] = $salary_line->AmountThisPeriod;
        if ($salary_line->EnableEmployeeTax) {
          // Only add this if this should have Employ tax(AGA)
          // calculate total for arbeidsgiveravgift amount
          $loennOgGodtgjoerelse[$zone_code]['loennOgGodtgjoerelse']['avgiftsgrunnlagBeloep'] += $salary_line->AmountThisPeriod;
        }
        // description for the entry
        // Error is: Salary line description for salary L' . $salary->JournalID . ' not set for line with text \'' . $salary_line->SalaryText . "'");
        self::checkIfEmpty($salary_line->SalaryDescription, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . " med text '" . $salary_line->SalaryText . "' har ikke satt altinnbeskrivelse");
        $inntekt['inntekt']['loennsinntekt'] = array();
        $inntekt['inntekt']['loennsinntekt']['beskrivelse'] = self::convertNorwegianLettersToASCII($salary_line->SalaryDescription);
        // TODO: Add other descriptions that need to have antall node in the check
        if (in_array($salary_line->SalaryDescription, array('timeloenn', 'overtidsgodtgjoerelse'))) {
          // hours/quantity for the entry
          // Error is: Salary line quantity for salary L' . $salary->JournalID . ' not set hours for line with text \'' . $salary_line->SalaryText . "'");
          self::checkIfEmpty($salary_line->NumberInPeriod, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . " med text '" . $salary_line->SalaryText . "' har ikke satt antall timer");
          $inntekt['inntekt']['loennsinntekt']['antall'] = $salary_line->NumberInPeriod;
        }
        elseif (in_array($salary_line->SalaryDescription, array('kilometergodtgjoerelseAndreFremkomstmidler', 'kilometergodtgjoerelseBil', 'kilometergodtgjoerelseElBil', 'kilometergodtgjoerelsePassasjertillegg'))) {
          // kilometers/quantity for the entry
          // Error is: Salary line quantity(in kilometers) for salary L' . $salary->JournalID . ' not set for line with text \'' . $salary_line->SalaryText . "'");
          self::checkIfEmpty($salary_line->NumberInPeriod, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . " med text '" . $salary_line->SalaryText . "' har ikke satt antall kilometer");
          $inntekt['inntekt']['loennsinntekt']['antall'] = $salary_line->NumberInPeriod;
        }
        elseif (in_array($salary_line->SalaryDescription, array('reiseKostMedOvernattingPaaHybelBrakkePrivat'))) {
          // days /quantity for the entry
          // Error is: Salary line quantity(in days) for salary L' . $salary->JournalID . ' not set for line with text \'' . $salary_line->SalaryText . "'");
          self::checkIfEmpty($salary_line->NumberInPeriod, 'L&oslash;nnslipp: L&oslash;nnslipplinje p&aring;  L' . $salary->JournalID . " med text '" . $salary_line->SalaryText . "' har ikke satt antall dager");
          $inntekt['inntekt']['loennsinntekt']['antall'] = $salary_line->NumberInPeriod;
        }
        // there can be multiple entries for one salary so we add to an array
        $inntekt_tmp[] = $inntekt;
      }
    }
    // check if all salary lines were empty/skipped(with 0 amount)
    $empty_salary = $all_salary_lines_empty ? '' : 'not empty';
    // Error is: Salary L' . $salary->JournalID . ' has only 0 amount lines
    self::checkIfEmpty($empty_salary, 'L&oslash;nnslipp: L&oslash;nnslipp L' . $salary->JournalID . ' har bare 0,00 linjer');
    return $inntekt_tmp;
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

/* Helper function to save a relation which work relations were included
 * in this report.
 */
  function saveWorkRelationReportLinks($altinn_report_id) {
    global $_lib;
    if (empty($this->work_relation_ids)) return false;
    $insert_query = 'INSERT INTO altinnReport1WorkRelation (AltinnReport1ID, WorkRelationID) VALUES ';
    foreach ($this->work_relation_ids as $work_relation_id) {
      $insert_query .= "('" . $altinn_report_id . "', '" . $work_relation_id . "'),";
    }
    $insert_query = substr($insert_query, 0, -1);
    $_lib['db']->db_query($insert_query);
    return true;
  }

/* Helper function that generates the query to get
 * the employees employed for the set period
 */
  function queryStringForCurrentlyEmployedEmployees() {
    // only the ones whose salaries have the altinn/actual pay date set and the
    // ones that are still employed
    $query_employees = "SELECT ap_merged.* FROM (
                        SELECT ap.*
                        FROM accountplan ap
                        INNER JOIN workrelation wr ON wr.AccountPlanID = ap.AccountPlanID
                        WHERE (wr.WorkStart <= '" . $this->period . "-01' OR wr.WorkStart LIKE '" . $this->period . "%') AND
                        (wr.WorkStop >= '" . $this->period . "-01' OR wr.WorkStop LIKE '0000-00-00') AND
                        AccountplanType LIKE '%employee%'
                        UNION
                        SELECT ap.*
                        FROM accountplan ap JOIN salary s ON s.AccountPlanID = ap.AccountPlanID
                        WHERE s.ActualPayDate LIKE  '" . $this->period . "%' ) AS ap_merged
                        ORDER BY ap_merged.FirstName";
    return $query_employees;
  }

/* Helper function that generates the query to get
 * the included work relations
 */
  function queryStringForIncludedWorkRelations() {
    $query_work_relations = "SELECT wr.*
                             FROM workrelation wr";
    if (!empty($this->work_relation_ids)) {
      $query_work_relations .= " WHERE wr.WorkRelationID IN (" . implode($this->work_relation_ids, ', ') . ")";
    } else {
      $query_work_relations .= " WHERE 1 = 0"; // returns an empty result set
    }
    return $query_work_relations;
  }

/* Helper function that generates the query to get
 * the included employees
 */
  function queryStringForIncludedEmployees() {
    // only the ones whose salaries have the altinn/actual pay date set and the
    // ones that are still employed
    $query_employees = "SELECT ap.*
                        FROM accountplan ap
                        WHERE AccountplanType LIKE '%employee%'";
    // add for selected employee ids also
    if (!empty($this->employee_ids)) {
      $query_employees .= ' AND ap.AccountPlanID IN (' . implode($this->employee_ids, ', ') . ')';
    } else {
      $query_employees .= " AND 1 = 0"; // returns an empty result set
    }
    return $query_employees;
  }

/* Helper function that generates the query to get
 * the included salaries
 */
  function queryStringForSelectedSalaries() {
    // only the ones that have the altinn/actual pay date set
    $query_salaries = "SELECT s.*
                       FROM salary s
                       WHERE ";
    if (!$this->just_test_if_ready) {
      $query_salaries .= "s.ActualPayDate LIKE  '" . $this->period . "%'";
    }
    else {
      $query_salaries .= "s.ActualPayDate LIKE  '%'";
    }
    // and restrict further by ids
    if (!empty($this->salary_ids)) {
      $query_salaries .= ' AND SalaryID IN (' . implode($this->salary_ids, ', ') . ')';
    } else {
      $query_salaries .= " AND 1 = 0"; // returns an empty result set
    }
    return $query_salaries;
  }

/* Helper function that populates the salaries array
 * for the selected period
 */
  function fetchSalaries() {
    global $_lib;
    $query_salaries = self::queryStringForSelectedSalaries();
    $result_salaries  = $_lib['db']->db_query($query_salaries);
    if (!$this->only_register_employee) {
      while ($salary = $_lib['db']->db_fetch_object($result_salaries)) {
        $this->salaries[(int)$salary->SubcompanyID][$salary->AccountPlanID][] = $salary;
        // array_search will return the index so if it is 0 it should still be true
        if (array_search((int) $salary->WorkRelationID, $this->work_relation_ids) === false){
          $this->work_relation_ids[] = (int) $salary->WorkRelationID;
        }
        self::fetchSalaryLines($salary);
      }
    }
  }

/* Helper function that populates the salary lines
 * for the specified salary
 */
  function fetchSalaryLines($salary) {
    global $_lib;
    $query_salary_lines = "SELECT sl.*
                           FROM salaryline sl
                           WHERE sl.SalaryID = " . $salary->SalaryID .
                           " AND sl.SendToAltinn = 1";
    // only select lines we should send to altinn

    $result_salary_lines  = $_lib['db']->db_query($query_salary_lines);
    while ($salary_line = $_lib['db']->db_fetch_object($result_salary_lines)) {
      $this->salary_lines[$salary->SalaryID][] = $salary_line;
    }
  }

/* Helper function that populates the employees and work_relations array
 */
  function fetchEmployeesAndWorkRelations() {
    global $_lib;

    $work_relations_by_employee = array();
    $query_work_relations = self::queryStringForIncludedWorkRelations();
    $result_work_relations  = $_lib['db']->db_query($query_work_relations);
    while ($work_relation = $_lib['db']->db_fetch_object($result_work_relations)) {
      $this->work_relations[$work_relation->SubcompanyID][$work_relation->AccountPlanID] = $work_relation;
      if (array_search($work_relation, $work_relations_by_employee) === false){
        $this->employee_ids[] = $work_relation->AccountPlanID;
        $work_relations_by_employee[$work_relation->AccountPlanID][] = $work_relation;
      }
    }

    // only the ones whose salaries have the altinn/actual pay date set
    $query_employees = self::queryStringForIncludedEmployees();
    $result_employees  = $_lib['db']->db_query($query_employees);
    while ($employee = $_lib['db']->db_fetch_object($result_employees)) {
      if (!empty($work_relations_by_employee[$employee->AccountPlanID])) {
        foreach ($work_relations_by_employee[$employee->AccountPlanID] as $work_relation) {
          $this->employees[$work_relation->SubcompanyID][$employee->AccountPlanID] = $employee;
        }
      }
    }
  }

/* Helper function that determine if the should have WorkTimeScheme
 * This should be used in node avloenningstype.
 * return a boolean
 */
  function shouldWeHaveWorkTimeScheme($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }

/* Helper function that determine if the should have Occupation
 * This should be used in node yrke.
 * return a boolean
 */
  function shouldWeHaveOccupation($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }

/* Helper function that determine if the should have CreditDaysUpdatedAt
 * This should be used in node sisteLoennsendringsdato.
 * return a boolean
 */
  function shouldWeHaveCreditDaysUpdatedAt($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }

/* Helper function that determine if the should have CurrentPositionSince
 * This should be used in node loennsansiennitet.
 * return a boolean
 */
  function shouldWeHaveCurrentPositionSince($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }

/* Helper function that determine if the should have WorkPeriode
 * This should be used in node startdato and sluttdato.
 * return a boolean
 */
  function shouldWeHaveWorkPeriode($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }


/* Helper function that determine if the should have Shift
 * This should be used in node arbeidstidsordning.
 * return a boolean
 */
  function shouldWeHaveShift($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }


/* Helper function that determine if the should have Workmeasurement
 * This should be used in node antallTimerPerUkeSomEnFullStillingTilsvarer.
 * return a boolean
 */
  function shouldWeHaveWorkmeasurement($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
  }

/* Helper function that determine if the should have WorkPercent
 * This should be used in node antallTimerPerUkeSomEnFullStillingTilsvarer.
 * return a boolean
 */
  function shouldWeHaveWorkPercent($type) {
    global $_lib;
    switch ($type) {
      case "pensjonOgAndreTyperYtelserUtenAnsettelsesforhold":
        return false;
        break;
    }
    return true;
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
      $doc = new DOMDocument('1.0', 'utf-8');
      $doc->formatOutput = true;
      $doc->loadXML(utf8_encode($xml));
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
