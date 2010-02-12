<?
#Copyright: Empatix AS 2007
#Donated code from empatix framework
class framework_logic_fixedwidthtoobject {
    public $formatH             = array();
    public $IdentificationStart = 0;
    public $IdentificationStop  = 0;
    /* 
    Describe the format of the lines in question
    * (star) in first line match is wildcard for every character
    Example
    private $formatH = array(
        'NY000010' => array(
            'name'              => 'startrecordforsendelse',
            'formatkode'        => array('start' => 0,  'stop' => 1, 'value' => 'NY', 'type' => 'text'),
            'tjenestekode'      => array('start' => 2,  'stop' => 3, 'value' => '00', 'type' => 'text'),
            'forsendelsestype'  => array('start' => 4,  'stop' => 5, 'value' => '00', 'type' => 'text'),
            'recordtype'        => array('start' => 6,  'stop' => 7, 'value' => '10', 'type' => 'text'),
            'dataavsender'      => array('start' => 8,  'stop' => 15, 'value' => '', 'type' => 'text'),
            'forsendelsesnummer'=> array('start' => 16, 'stop' => 22, 'value' => '', 'type' => 'text'),
            'datamottaker'      => array('start' => 23, 'stop' => 30, 'value' => '', 'type' => 'text'),
        ),
        'NY09**30' => array(
    */

    public function __construct($args) {
        $this->formatH              = $args['formatH'];
        $this->IdentificationStart  = $args['IdentificationStart'];
        $this->IdentificationStop   = $args['IdentificationStop'];
    }
    
    ################################################################################################
    #Parse the fixed width format of a line to an object
    public function line($line) {

        $identification = substr($line, $this->IdentificationStart, $this->IdentificationStop);

        #Find the correct configuration top parse the line with, based on the caracteristics of the line
        #** is wildcard
        if(!$this->formatH[$identification]) {

            #Could have been a smarter generic lookup of the types by looking at stars in the different identifications
            $identification = substr_replace($identification, '**', 4, 2);
            #print "identification: #$identification#\n";
            if(!$this->formatH[$identification]) {
                print "Unknown format identification: $identification\n";
                #$identification = '';
            }
        }

        if($this->debug) print "identification: $identification<br>\n";
        if($this->debug) print "line: $line<br>\n";
        $lineO          = new stdClass();
        $lineO->name    = $this->formatH[$identification]['name'];
        foreach($this->formatH[$identification] as $name => $configH) {

            if($this->debug) print_r($configH);
            if(is_array($configH)) {
                $length = $configH['stop'] - $configH['start'] + 1;
                $lineO->{$name} = $this->{$configH['type']}(substr($line, $configH['start'], $length)); 
                #print "substr(" . $configH['start'] . ", $length) = " . $lineO->{$name} . "\n";
            } elseif($name != 'name') {
                print "##Ingen konfigurasjon funnet for identification: $identification<br />\n";
                if($this->debug) print_r($configH);
                if($this->debug) print "<br />\n";            

            }
        }
        
        #print "$line\n";
        if($this->debug) print_r($lineO);
        return $lineO;
    }

    ################################################################################################
    #Parse the fixed width format of a line to an array of objects
    public function multilines($lines) {
 
        $linesA = array();
        foreach($lines as $line) {
            $linesA[] = $this->line($line);
        }
        
        return $linesA;
    }

    ################################################################################################
    #Automatic data converting functions
    private function date($date) {
        global $_lib;
        return $_lib['convert']->date($date);
    }

    private function int($int) {
        return (int) $int;
    }

    private function amount($amount) {
        global $_lib;
        return $_lib['convert']->amount($amount/100);
    }

    private function text($text) {
        return trim($text);
    }

    private function transactiontype($type) {
        return $this->transaksjonstypeH[$type];
    }

    private function account($account) {
        return $account;
    }
    private function kid($kid) {
        return trim($kid);
    }
    private function sign($sign) {
        return $sign;
    }
    private function day($day) {
        return $day;
    }
}
?>