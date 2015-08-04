<?

class lodo_orgnumberlookup_orgnumberlookup {
    private $host           = 'fakturabank.no';
    private $protocol       = 'https';
    private $path           = '/rest/companies/';
    private $url            = '';
    public  $startexectime  = '';
    public  $stopexectime   = '';
    public  $diffexectime   = '';
    public  $error          = '';
    public  $success        = false;

    function __construct() {
        global $_lib;

        $this->startexectime  = microtime();

        $this->host = $GLOBALS['_SETUP']['FB_SERVER'];
        $this->protocol = $GLOBALS['_SETUP']['FB_SERVER_PROTOCOL'];
        $this->url = "$this->protocol://$this->host$this->path";
    }

    function __destruct() {
        $this->stopexectime   = microtime();
        $this->diffexectime   = $this->stopexectime - $this->startexectime;
    }

    ####################################################################################################
    #READ XML
    function getOrgNumberByScheme($scheme_value, $scheme_type) {
        global $_lib;

        if ($scheme_type == "NO:ORGNR") {
          $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
          $new_pattern    = array("", "", "");
          $scheme_value   = strtolower(preg_replace($old_pattern, $new_pattern , $scheme_value));
        }

        $url = $this->url . "index.xml?value=" . $scheme_value . "&type=" . $scheme_type;
        $path = $this->path . "index.xml?value=" . $scheme_value . "&type=" . $scheme_type;
        $this->getOrgNumber2($path, $url);
    }

    function getOrgNumber2($path, $url) {
        global $_lib;

        $headers = array(
            "GET " . $this->path . " HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        #curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);

        $xml_data = curl_exec($ch);
        $xml_data = html_entity_decode($xml_data, ENT_NOQUOTES, 'UTF-8');
        $xml_data = str_replace("&", "&amp;", $xml_data);

        if (curl_errno($ch)) {
            $_lib['message']->add("Error: " . curl_error($ch));
        } else {
            $_lib['message']->add("Fant OrgNumber");
            #var_dump($data);
            #print_r(curl_getinfo($ch));

           #print_r(simplexml_load_string($data));
        }

        curl_close($ch);

        $company = simplexml_load_string($xml_data);
        $this->mapdata($company);
    }

    function mapdata($company) {

        if($company) {
            $this->success               = true;
            $this->OrgNumber            = (string) utf8_decode($company->number);
            $this->AccountName          = (string) utf8_decode($company->name);
            $this->Email                = (string) utf8_decode($company->email);
            $this->Fax                  = (string) utf8_decode($company->fax);
            $this->Mobile               = (string) utf8_decode($company->mobile);
            $this->Phone                = (string) utf8_decode($company->phone);
            $this->URL                  = (string) utf8_decode($company->website);
            $this->DomesticBankAccount  = (string) utf8_decode($company->{'bank-account-number'});
            $this->MotkontoResultat1    = (string) utf8_decode($company->{'default-result-account-number'});
            $this->MotkontoBalanse1    = (string) utf8_decode($company->{'default-balance-account-number'});
            $this->IAdress->Address1    = (string) utf8_decode($company->{'business-address'}->address1);
            $this->IAdress->Address2    = (string) utf8_decode($company->{'business-address'}->address2);
            $this->IAdress->Address3    = (string) utf8_decode($company->{'business-address'}->address3);
            $this->IAdress->City        = (string) utf8_decode($company->{'business-address'}->city);
            $this->IAdress->ZipCode     = (string) utf8_decode($company->{'business-address'}->zip);
            $this->IAdress->Country     = (string) utf8_decode($company->{'business-address'}->country);
            #$CountryCode               = (string) utf8_decode($company->{'businessaddress'}->country-code);

            $this->Municipality         = (string) utf8_decode($company->{'business-address'}->municipality);
            #$this->MunicipalityNo      = (string) utf8_decode($company->{'business-address'}->municipality-no);
            $this->ParentCompanyName    = (string) utf8_decode($company->{'parent-unit-name'});
            $this->ParentCompanyNumber  = (string) utf8_decode($company->{'parent-unit-number'});
        }
    }
}
?>
