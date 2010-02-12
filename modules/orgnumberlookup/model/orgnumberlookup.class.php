<?
#http://gullfisk:rTp3Qzy@brreg.lodo.no/rest/companies/970131450

class lodo_orgnumberlookup_orgnumberlookup {
    #private $host           = 'fakturabank.cavatina.no';
    private $host           = 'brreg.lodo.no';
    #private $protocol       = 'http';
    private $protocol       = 'http';
    private $username       = '';
    private $password       = '';
    private $path           = '/rest/companies/';   #Do not store in svn
    private $url            = '';
    private $credentials    = '';
    public  $startexectime  = '';
    public  $stopexectime   = '';
    public  $diffexectime   = '';
    public  $error          = '';
    public  $success        = false;

    function __construct() {
        global $_lib;
        
        $this->startexectime  = microtime();

        $this->username = $_lib['setup']->get_value('orgnumberlookup.username');
        $this->password = $_lib['setup']->get_value('orgnumberlookup.password');

        $this->credentials = "$this->username:$this->password";
        $this->url = "$this->protocol://$this->host$this->path";
        #print "$this->url<br>\n";
        #print "$this->credentials   <br>\n";
        #$this->url = "$this->protocol://$this->username:$this->password@$this->host$this->path";
    }

    function __destruct() {
        $this->stopexectime   = microtime();
        $this->diffexectime   = $this->stopexectime - $this->startexectime;
    }
        
    ####################################################################################################
    #READ XML    
    function getOrgNumber($OrgNumber) {
        global $_lib;

        $old_pattern    = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern    = array("", "", "");
        $OrgNumber      = strtolower(preg_replace($old_pattern, $new_pattern , $OrgNumber)); 

        if(strlen($OrgNumber) == 9) {

            $url = $this->url . $OrgNumber;

            $path = $this->path . $OrgNumber;

            $this->getOrgNumber2($path, $url);

        } else {
            $_lib['message']->add("Orgnummer m? best&aring; av 9 siffer");        
        }
    }

    function getOrgNumber2($path, $url) {
        global $_lib;
                
        $headers = array(
            "GET " . $this->path . " HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: application/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Authorization: Basic " . base64_encode($this->credentials)
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
            $this->CreditDays           = (string) utf8_decode($company->{'credit-days'});
            $this->MotkontoResultat1    = (string) utf8_decode($company->{'default-bookkeeping-account1'});
            $this->MotkontoResultat2    = (string) utf8_decode($company->{'default-bookkeeping-account2'});
            
            $this->IAdress->Address1    = (string) utf8_decode($company->{'business-address'}->address1);
            $this->IAdress->Address2    = (string) utf8_decode($company->{'business-address'}->address2);
            $this->IAdress->Address3    = (string) utf8_decode($company->{'business-address'}->address3);
            $this->IAdress->City        = (string) utf8_decode($company->{'business-address'}->city);
            $this->IAdress->ZipCode     = (string) utf8_decode($company->{'business-address'}->zip);
            $this->IAdress->Country     = (string) utf8_decode($company->{'business-address'}->country);
            #$CountryCode               = (string) utf8_decode($company->{'businessaddress'}->country-code);
            
            $this->Municipality         = (string) utf8_decode($company->{'business-address'}->municipality);
            #$this->MunicipalityNo      = (string) utf8_decode($company->{'business-address'}->municipality-no);    
        }
    }
}
?>
