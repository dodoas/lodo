<?php
class lodo_accountplan_scheme {
    static function listAvailableTypes() {
        global $_lib;

        $l = array();

        $r = $_lib['db']->db_query("SELECT * FROM fakturabankscheme");
        while( ($row = $_lib['db']->db_fetch_assoc($r)) ) {
            $l[] = $row;
        }

        return $l;
    }

    static function addSchemeType($remoteid, $type) {
        global $_lib;

        $q = sprintf("INSERT INTO `fakturabankscheme` (`FakturabankRemoteSchemeID`, `SchemeType`) VALUES (%d, %d);", 
                     $remoteid, $type);

        $_lib['db']->db_query($q);
        return $_lib['db']->db_insert_id();
    }

    static function deleteSchemeType($FakturabankSchemeID) {
        global $_lib;

        $q = sprintf("DELETE FROM fakturabankscheme WHERE FakturabankSchemeID = %d",
                     $FakturabankSchemeID);
        $_lib['db']->db_query($q);
    }

    static function findAccountPlan($schemetype, $schemevalue) {
        global $_lib;

        $q = sprintf("SELECT a.AccountPlanID as AccountPlanID
                        FROM accountplanscheme a,
                             fakturabankscheme f
                        WHERE
                          a.SchemeValue = '%s' 
                          AND a.FakturabankSchemeID = f.FakturabankSchemeID
                          AND f.SchemeType = '%s'",
                     $_lib['db']->db_escape($schemevalue),
                     $_lib['db']->db_escape($schemetype));
        $r = $_lib['db']->db_query($q);
        $row = $_lib['db']->db_fetch_assoc($r);

        return $row['AccountPlanID'];
    }

    var $AccountPlanID = 0;
    var $globalSchemes = null;

    function __construct($AccountPlanID) {
        $this->AccountPlanID = $AccountPlanID;
        $this->globalSchemes = lodo_accountplan_scheme::listAvailableTypes();
    }

    function listTypes() {
        return $this->globalSchemes;
    }

    function addScheme($FakturabankSchemeID, $SchemeValue) {
        global $_lib;

        $q = sprintf("INSERT INTO `accountplanscheme` (`AccountPlanID`, `FakturabankSchemeID`, `SchemeValue`) 
                        VALUES(%d, %d, '%s');",
                     $this->AccountPlanID, $FakturabankSchemeID, $_lib['db']->db_escape($SchemeValue));

        $_lib['db']->db_query($q);
    }

    function listSchemes() {
        global $_lib;
        
        $q = sprintf("SELECT * FROM accountplanscheme WHERE AccountPlanID = %d",
                     $this->AccountPlanID);
        $r = $_lib['db']->db_query($q);

        $l = array();

        while(($row = $_lib['db']->db_fetch_assoc($r))) {
            $l[] = $row;
        }

        return $l;
    }

    function deleteScheme($AccountPlanSchemeID) {
        $q = sprintf("DELETE FROM accountplanscheme WHERE AccountplanSchemeID = %d LIMIT 1",
                     $AccountPlanSchemeID);
        $r = $_lib['db']->db_query($q);
    }
};