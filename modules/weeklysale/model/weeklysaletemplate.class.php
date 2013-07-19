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

    public function weeklySaleIsActive($WeeklySaleID) {
        global $_lib;

        $q = sprintf("
          SELECT w.WeeklySaleID
            FROM weeklysale w, voucher v
            WHERE w.WeeklySaleID = %d AND w.JournalID = v.JournalID AND v.Active = 1",
                     $WeeklySaleID);

        $r = $_lib['db']->db_query($q);
        $n = $_lib['db']->db_numrows($r);

        if($n)
            return true;
        else
            return false;
    }

    public function weeklySaleExists($WeeklySaleID) {
        global $_lib;

        $q = sprintf("
          SELECT w.WeeklySaleID
            FROM weeklysale w
            WHERE w.WeeklySaleID = %d",
                     $WeeklySaleID);

        $r = $_lib['db']->db_query($q);
        $n = $_lib['db']->db_numrows($r);

        if($n)
            return true;
        else
            return false;
    }

    public function createEntries() {
        global $_lib;
        $entries = array();

        $q = sprintf("
          SELECT 
            t.*,
            (not (v.JournalID IS NULL)) as journalInUse
            FROM weeklysaletemplate t
              LEFT JOIN (voucher v)
                ON (v.JournalID = t.JournalID AND v.voucherType = t.voucherType AND v.Active = 1)
            WHERE t.Year = %d AND t.WeeklySaleConfID = %d 
            GROUP BY t.WeeklySaleTemplateID
            ORDER BY t.WeekNo, t.FirstDate
        ",
        $this->year, $this->config);

        $r = $_lib['db']->db_query($q);
        while($row = $_lib['db']->db_fetch_assoc($r)) 
            $entries[ $row['WeeklySaleTemplateID'] ] = $row;

        // mark lines with duplicate journalid
        $usedJournalIDs = array();

        foreach($entries as &$e) {
            if($e['JournalID']) {
                $fullJournalID = $e['VoucherType'] . $e['JournalID'];
                if(isset($usedJournalIDs[$fullJournalID])) {
                    $e['journalInUse'] = true;
                }

                $usedJournalIDs[$fullJournalID] = true;
            }
        }

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

        $years = array(date("Y"), date("Y") - 1, date("Y") + 1);
        $r = $_lib['db']->db_query("SELECT `Year` FROM weeklysaletemplate GROUP BY `Year`");
        while($row = $_lib['db']->db_fetch_assoc($r)) {
            if(!in_array($row["Year"], $years))
                $years[] = $row["Year"];
        }

        sort($years);
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

    public function deleteEntryVoucher($WeeklySaleTemplateID) {
        global $_lib;

        if(!isset($this->entries[$WeeklySaleTemplateID]))
            return false;

        $e = $this->entries[$WeeklySaleTemplateID];
        if(!$e["WeeklySaleID"])
            return false;

        $q = sprintf("DELETE FROM voucher WHERE JournalID = %d AND VoucherType = '%c'", 
                     $e["JournalID"], $e["VoucherType"]);
        $_lib['db']->db_query($q);

        $q = sprintf("DELETE FROM weeklysaleday WHERE WeeklySaleID = %d", $e['WeeklySaleID']);
        $_lib['db']->db_query($q);

        $q = sprintf("DELETE FROM weeklysale WHERE WeeklySaleID = %d", $e['WeeklySaleID']);
        $_lib['db']->db_query($q);

        $q = sprintf("UPDATE weeklysaletemplate SET WeeklySaleID = 0 WHERE WeeklySaleTemplateID = %d",
                     $WeeklySaleTemplateID);
        $_lib['db']->db_query($q);

        return true;
    }

    public function create($WeeklySaleTemplateID) {
        global $_lib;

        $entry = $this->entries[$WeeklySaleTemplateID];

        if(!$entry["JournalID"]) {
            $_lib['message']->add(array('message' => "Ingen bilagsnummer ID:$WeeklySaleTemplateID"));
            return;
        }

        if($entry['WeeklySaleConfID'] && $this->weeklySaleIsActive($entry['WeeklySaleConfID'])) {
            $_lib['message']->add(array('message' => 
                                        "Ukeomsetning finnes fra f&oslash;rst ID:$WeeklySaleTemplateID"));

            return;
        }

        $query_free_journalid = sprintf("select * from voucher where JournalID = '%d' and VoucherType = '%s' and active = 1",
                                        $entry['JournalID'], $entry['VoucherType']);
        $result_free_journalid = $_lib['db']->db_query($query_free_journalid);
        if($_lib['db']->db_numrows($result_free_journalid)) {
            $_lib['message']->add(array('message' => 
                                        sprintf("Bilagsnummer %s%d opptatt ID:$WeeklySaleTemplateID",
                                                $entry['VoucherType'], $entry['JournalID']
                                            )));

            return;
        }

        $query_free_journalid = sprintf("select * from weeklysale where JournalID = '%d' and VoucherType = '%s'",
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
        $postmain['weeklysale_JournalDate']       = $entry['LastDate'];
        $postmain['weeklysale_JournalID']         = $entry["JournalID"];
        $postmain['weeklysale_VoucherType']       = $VoucherType; //$sale_conf->VoucherType;

        $postmain['weeklysale_Week'] = $entry['WeekNo'];

        $WeeklySaleID = $_lib['storage']->db_new_hash($postmain, 'weeklysale');

        $d = strtotime($entry['FirstDate']);
        $last_d = strtotime($entry['LastDate']);

        $_lib['db']->db_update(
            sprintf("update weeklysaletemplate set WeeklySaleID = %d where WeeklySaleTemplateID = %d",
                    $WeeklySaleID, $entry['WeeklySaleTemplateID'])
            );

        // add dummy days before
        if(date('N', $d) != 1) {
            $tmp_d = strtotime("last monday", $d);
            
            do {
                $postsub['weeklysaleday_WeeklySaleID'] = $WeeklySaleID;
                $postsub['weeklysaleday_Day']          = date('j', $tmp_d);
                $postsub['weeklysaleday_DayID']        = date('N', $tmp_d);
                $postsub['weeklysaleday_Type']         = 1;
                $postsub['weeklysaleday_Locked']       = 1;
            
                $WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
                $_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");

                $postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
                $postsub['weeklysaleday_Type']                  = 2;
                $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');           

                $tmp_d += 60 * 60 * 24;
            } while($tmp_d < $d);
        }
                               
        do { 
            $postsub['weeklysaleday_WeeklySaleID'] = $WeeklySaleID;
            $postsub['weeklysaleday_Day']          = date('j', $d); 
            $postsub['weeklysaleday_DayID']        = date('N', $d);
            $postsub['weeklysaleday_Type']         = 1;
            $postsub['weeklysaleday_Locked']       = 0;
            
            $WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
            $_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");

            $postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
            $postsub['weeklysaleday_Type']                  = 2;
            $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');           

            $d += 60 * 60 * 24;
        } while($d <= $last_d);

        // add dummy days after
        while(date('N', $d) != 1) {
            $postsub['weeklysaleday_WeeklySaleID'] = $WeeklySaleID;
            $postsub['weeklysaleday_Day']          = date('j', $d);
            $postsub['weeklysaleday_DayID']        = date('N', $d);
            $postsub['weeklysaleday_Type']         = 1;
            $postsub['weeklysaleday_Locked']       = 1;
            
            $WeeklySaleDayID = $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');
            $_lib['db']->db_update("update weeklysaleday set ParentWeeklySaleDayID=$WeeklySaleDayID where WeeklySaleDayID=$WeeklySaleDayID");

            $postsub['weeklysaleday_ParentWeeklySaleDayID'] = $WeeklySaleDayID;
            $postsub['weeklysaleday_Type']                  = 2;
            $_lib['storage']->db_new_hash($postsub, 'weeklysaleday');           

            $d += 60 * 60 * 24;
        }

    }
};