<?php

class WeeklysaleTemplate {
    private $year;
    private $config;
    private $entries;
    private $defaultVoucherType = "O";

    public function __construct($year, $config) {
        $this->year = $year;
        $this->config = $config;

        $this->reload();
    }

    public function lastWeekNo() {
        $m = 0;
        foreach($this->entries as &$entry) {
            if($entry["WeekNo"] > $m) 
                $m = $entry["WeekNo"];
        }

        return $m;
    }

    public function updateFromPost() {
        global $_lib;
        $_lib['storage']->db_update_multi_table($_POST, array('weeklysaletemplate' => 'WeeklySaleTemplateID'));
        $this->entries = $this->createEntries();
    }

    public function createEntries() {
        global $_lib;
        $entries = array();

        $q = sprintf("SELECT * FROM weeklysaletemplate WHERE Year = %d AND WeeklySaleConfID = %d 
                       ORDER BY WeekNo, FirstDate", 
                     $this->year, $this->config);

        $r = $_lib['db']->db_query($q);
        while($row = $_lib['db']->db_fetch_assoc($r)) 
            $entries[ $row['WeeklySaleTemplateID'] ] = $row;

        return $entries;
    }

    public function reload() {
        $this->entries = $this->createEntries();
    }

    public function listEntries() {
        return $this->entries;
    }

    public static function listYears() {
        global $_lib;

        $years = array();
        $r = $_lib['db']->db_query("SELECT `Year` FROM weeklysaletemplate GROUP BY `Year`");
        while($row = $_lib['db']->db_fetch_assoc($r)) {
            $years[] = $row["Year"];
        }

        $curYear = date("Y");
        if(!in_array($curYear, $years))
            $years[] = $curYear;

        rsort($years);
        return $years;
    }

    public static function listConfigs() {
        global $_lib;

        $now = date('Y-m-d');

        $query_sale_conf  = "select * from weeklysaleconf 
                               where (StartDate >= $now and EndDate <= $now) or EndDate = '0000-00-00'";
        $result_sale_conf = $_lib['db']->db_query($query_sale_conf);

        $configs = array();
        while($row = $_lib['db']->db_fetch_assoc($result_sale_conf)) {
            $configs[ $row['WeeklySaleConfID'] ] = $row['Name'];
        }

        ksort($configs);
        return $configs;
    }

    public function exportSerialized() {
        return serialize($this->entries);
    }

    public function importSerialized($serialized) {
        $entries = unserialize($serialized);

        foreach($entries as $entry) {
            $this->addEntry(
                $entry['Year'],
                $entry['WeekNo'],
                $entry['FirstDate'],
                $entry['LastDate'],
                $entry['Period'],
                $entry['VoucherType']
                );
        }

        $this->reload();
    }

    public function addEntry($year, $weekno, $firstdate, $lastdate, $period, $vouchertype) {
        global $_lib;
        
        $q = sprintf("INSERT INTO weeklysaletemplate (`Year`, `WeekNo`, `FirstDate`, `LastDate`, `Period`, `VoucherType`, `WeeklySaleConfID`)
                             VALUES (%d, %d, '%s', '%s', '%s', '%s', %d);",
                     $year, $weekno, $firstdate, $lastdate, $period, $vouchertype, $this->config);
        $_lib['db']->db_query($q);
    }

    public function addBlankEntry($update = true) {
        global $_lib;

        $nextWeek = $this->lastWeekNo() + 1;

        $q = sprintf("INSERT INTO weeklysaletemplate (`Year`, `WeekNo`, `VoucherType`, `WeeklySaleConfID`) VALUE ('%d', '%d', '%s', %d)",
                     $this->year, $nextWeek, $this->defaultVoucherType, $this->config);
        $_lib['db']->db_query($q);

        if($update)
            $this->reload();
    }

    public function deleteEntry($WeeklySaleTemplateID) {
        global $_lib;

        $q = sprintf("DELETE FROM weeklysaletemplate WHERE WeeklySaleTemplateID = %d LIMIT 1",
                     $WeeklySaleTemplateID);

        $_lib['db']->db_query($q);

        foreach($this->entries as $k=>$v) {
            if($v["WeeklySaleTemplateID"] == $WeeklySaleTemplateID) {
                unset($this->entries[$k]);
                break;
            }
        }
    }

    public function create($WeeklySaleTemplateID) {
        global $_lib;

        $entry = $this->entries[$WeeklySaleTemplateID];

        if(!$entry["JournalID"]) {
            $_lib['message']->add(array('message' => "Ingen bilagsnummer ID:$WeeklySaleTemplateID"));
            return;
        }

        $query_free_journalid = sprintf("select * from voucher where JournalID = '%d' and VoucherType = '%s'",
                                        $entry['JournalID'], $entry['VoucherType']);
        $result_free_journalid = $_lib['db']->db_query($query_free_journalid);
        if($_lib['db']->db_numrows($result_free_journalid)) {
            $_lib['message']->add(array('message' => 
                                        sprintf("Bilagsnummer %s%d opptatt ID:$WeeklySaleTemplateID",
                                                $entry['VoucherType'], $entry['JournalID']
                                            )));
            return;
        }

        $VoucherType = $entry['VoucherType'];
        $JournalID = $entry['JournalID'];
        $WeeklySaleConfID = $entry['WeeklySaleConfID'];

        $query_sale_conf  = "select * from weeklysaleconf where WeeklySaleConfID = '$WeeklySaleConfID'";
        $result_sale_conf = $_lib['db']->db_query($query_sale_conf);
        $sale_conf        = $_lib['db']->db_fetch_object($result_sale_conf);

        $postmain['weeklysale_Name']              = $sale_conf->Name;
        $postmain['weeklysale_PermanentCash']     = $sale_conf->PermanentCash;
        $postmain['weeklysale_DepartmentID']      = $sale_conf->DepartmentID;
        $postmain['weeklysale_Period']            = $entry["Period"];
        $postmain['weeklysale_InsertedDateTime']  = $_lib['sess']->get_session('Date');
        $postmain['weeklysale_InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
        $postmain['weeklysale_UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $postmain['weeklysale_WeeklySaleConfID']  = $WeeklySaleConfID;
        $postmain['weeklysale_JournalDate']       = $_lib['sess']->get_session('LoginFormDate');
        $postmain['weeklysale_JournalID']         = $entry["JournalID"];
        $postmain['weeklysale_VoucherType']       = $sale_conf->VoucherType;

        $postmain['weeklysale_Week'] = $entry['WeekNo'];

        $WeeklySaleID = $_lib['storage']->db_new_hash($postmain, 'weeklysale');

        echo "Got '$WeeklySaleID'";

        $d = strtotime($entry['FirstDate']);
        $last_d = strtotime($entry['LastDate']);

        $_lib['db']->db_update(
            sprintf("update weeklysaletemplate set WeeklySaleID = %d where WeeklySaleTemplateID = %d",
                    $WeeklySaleID, $entry['WeeklySaleTemplateID'])
            );
                               

        do { 
            $postsub['weeklysaleday_WeeklySaleID'] = $WeeklySaleID;
            $postsub['weeklysaleday_Day']          = date('j', $d); 
            $postsub['weeklysaleday_DayID']        = date('N', $d);
            $postsub['weeklysaleday_Type']         = 1;
            
            $WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
            $_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");

            $postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
            $postsub['weeklysaleday_Type']                  = 2;
            $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');           

            $d += 60 * 60 * 24;
        } while($d <= $last_d);
    }
};