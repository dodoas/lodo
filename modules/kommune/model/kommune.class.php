<?
/*
 * Kommune class
 *
 */

class kommune {

  private $db_table = 'kommune';
  private $all_kommunes = array();
  private $kommunes_in_db_by_id = array();
  private $kommunes_in_db_by_number = array();
  private $kommune_data_for_select = array();

  // Table columns
  public $KommuneID         = NULL;
  public $KommuneNumber     = NULL;
  public $KommuneName       = NULL;
  public $County            = NULL;
  public $Sone              = NULL;
  public $BankAccountNumber = NULL;
  public $OrgNumber         = NULL;
  public $OrgName           = NULL;
  public $OrganisationForm  = NULL;
  public $Comments          = NULL;

  // Linked to the AGA table by Sone code
  public $TaxPercent        = NULL;

  public function __construct($id = NULL) {
    self::init();
    // load the kommune if the id given
    if (!is_null($id)) self::load($id);
  }

  // Initialize, load all kommunes from csv file, all the kommunes from the database
  // and all the tax percentage per zone code
  public function init() {
    global $_SETUP, $_lib;
    $this->all_kommunes = array();
    $this->kommunes_in_db_by_id = array();
    $this->kommunes_in_db_by_number = array();

    // get all tax precentage by zone code
    $aga_result = $_lib['db']->db_query("SELECT * FROM arbeidsgiveravgift");
    while($aga_object = $_lib['db']->db_fetch_object($aga_result)) {
      $this->tax_percents[$aga_object->Code] = $aga_object->Percent;
    }

    $csv_file_url = $_SETUP['HOME_DIR'] . $_SETUP['KOMMUNE_CSV'];
    if (($kommune_cvs_file = fopen($csv_file_url, "r")) !== FALSE) {
      while (($csvdata = fgetcsv($kommune_cvs_file)) !== FALSE) {
        $kommune_object = self::kommune_object_from_csvdata($csvdata);
        $this->all_kommunes[$kommune_object->KommuneNumber] = $kommune_object;
        $this->kommune_data_for_select[$kommune_object->KommuneNumber] = implode($csvdata, ", ");
      }
      fclose($kommune_cvs_file);
    }

    $kommunes_result = $_lib['db']->db_query("SELECT * FROM $this->db_table ORDER BY KommuneNumber");
    while($kommune_object = $_lib['db']->db_fetch_object($kommunes_result)) {
      $kommune_object->TaxPercent = $this->tax_percents[$kommune_object->Sone];
      $this->kommunes_in_db_by_id[$kommune_object->KommuneID] = $kommune_object;
      $this->kommunes_in_db_by_number[$kommune_object->KommuneNumber] = $kommune_object;
    }
  }

  // unload(set to null) all properties
  public function unload() {
    $this->KommuneID         = NULL;
    $this->KommuneNumber     = NULL;
    $this->KommuneName       = NULL;
    $this->County            = NULL;
    $this->Sone              = NULL;
    $this->BankAccountNumber = NULL;
    $this->OrgNumber         = NULL;
    $this->OrgName           = NULL;
    $this->OrganisationForm  = NULL;
    $this->Comments          = NULL;

    $this->TaxPercent        = NULL;
  }

  // Returns the object with the given id from the database
  public function get_kommune_object_by_id($kommune_id) {
    return $this->kommunes_in_db_by_id[$kommune_id];
  }

  // Returns a kommune as an object from the list of all kommunes in the csv 
  // file that has the given kommune number
  public function get_kommune_object_by_number($kommune_number) {
    return $this->all_kommunes[$kommune_number];
  }

  // Returns a kommune as an object from a line from the csv file
  public function kommune_object_from_csvdata($kommune_data) {
    $kommune->KommuneNumber     = $kommune_data[0];
    $kommune->KommuneName       = $kommune_data[1];
    $kommune->County            = $kommune_data[2];
    $kommune->Sone              = $kommune_data[3];
    $kommune->BankAccountNumber = $kommune_data[4];
    $kommune->OrgNumber         = $kommune_data[5];
    $kommune->OrgName           = $kommune_data[6];
    $kommune->OrganisationForm  = $kommune_data[7];
    $kommune->Comments          = $kommune_data[8];

    $kommune->TaxPercent        = $this->tax_percents[$kommune->Sone];
    return $kommune;
  }

  // Sets the properties of this object to the columns from the database for given id
  public function load($kommune_id) {
    $kommune = self::get_kommune_object_by_id($kommune_id);
    $this->KommuneID         = $kommune->KommuneID;
    $this->KommuneNumber     = $kommune->KommuneNumber;
    $this->KommuneName       = $kommune->KommuneName;
    $this->County            = $kommune->County;
    $this->Sone              = $kommune->Sone;
    $this->BankAccountNumber = $kommune->BankAccountNumber;
    $this->OrgNumber         = $kommune->OrgNumber;
    $this->OrgName           = $kommune->OrgName;
    $this->OrganisationForm  = $kommune->OrganisationForm;
    $this->Comments          = $kommune->Comments;

    $this->TaxPercent        = $this->tax_percents[$kommune->Sone];
  }

  public function import($kommune_number) {
    $kommune = self::get_kommune_object_by_number($kommune_number);
    // if the number exists in the database use its id else id is null
    $existing_kommune_in_db = $this->kommunes_in_db_by_number[$kommune_number];
    if (empty($existing_kommune_in_db)) {
      $this->KommuneID       = NULL;
    } else {
      $this->KommuneID       = $existing_kommune_in_db->KommuneID;
    }

    $this->KommuneNumber     = $kommune->KommuneNumber;
    $this->KommuneName       = $kommune->KommuneName;
    $this->County            = $kommune->County;
    $this->Sone              = $kommune->Sone;
    $this->BankAccountNumber = $kommune->BankAccountNumber;
    $this->OrgNumber         = $kommune->OrgNumber;
    $this->OrgName           = $kommune->OrgName;
    $this->OrganisationForm  = $kommune->OrganisationForm;
    $this->Comments          = $kommune->Comments;

    $this->TaxPercent        = $this->tax_percents[$kommune->Sone];
  }

  // Update or insert the current kommune into database
  public function save() {
    global $_lib;
    $kommune_exists = !is_null($this->KommuneID);
    if ($kommune_exists) {
      $kommune_id_column_name = "KommuneID, ";
      $kommunes_id_value = "$this->KommuneID, ";
    }
    else {
      $kommune_id_column_name = "";
      $kommunes_id_value = "";
    }
    $save_query = "REPLACE INTO kommune ($kommune_id_column_name KommuneNumber, KommuneName, Sone, County, BankAccountNumber, OrgNumber, OrgName, OrganisationForm, Comments)
                   VALUES ($kommunes_id_value '$this->KommuneNumber', '$this->KommuneName', '$this->Sone', '$this->County', '$this->BankAccountNumber', '$this->OrgNumber', '$this->OrgName', '$this->OrganisationForm', '$this->Comments')";
    $_lib['db']->db_query($save_query);
    self::init();
    self::unload();
  }

  // Accessors for private properties

  public function all_kommunes_in_db() {
    return $this->kommunes_in_db_by_id;
  }

  public function kommune_data_for_select() {
    return $this->kommune_data_for_select;
  }

}
