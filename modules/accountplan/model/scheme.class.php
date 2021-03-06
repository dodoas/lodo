<?php

/*
 * fakturabankscheme:
 *  FakturabankSchemeID : table index
 *  FakturabankRemoteSchemeID : id of scheme used in fakturabank
 *  SchemeType: type of scheme, i.e. NO:ORG
 *
 *
 * accountplanscheme:
 *  AccountPlanSchemeID: table index
 *  AccountPlanID: reference to accountplan table
 *  FakturabankSchemeID: reference to fakturabankscheme table
 *  SchemeValue: lookup value of a give fakturabankscheme.
 *               i.e. for NO:ORG fakturabankscheme this could be 12345678
 *
 */

class lodo_accountplan_scheme {
    /**
     * List available scheme types. I.e. list every entry in fakturabankscheme
     */
    static function listAvailableTypes() {
        global $_lib;

        $l = array();

        $r = $_lib['db']->db_query("SELECT * FROM fakturabankscheme ORDER BY SchemeType");
        while( ($row = $_lib['db']->db_fetch_assoc($r)) ) {
            $l[(int)$row['FakturabankSchemeID']] = $row;
        }

        return $l;
    }

    /**
     * Add a new scheme type
     *
     * $remoteid is the ID used in fakturabank
     * $type is the scheme type, i.e. NO:ORG
     */
    static function addSchemeType($remoteid, $type) {
        global $_lib;

        $q = sprintf("INSERT INTO `fakturabankscheme` (`FakturabankRemoteSchemeID`, `SchemeType`) VALUES (%d, %d);",
                     $remoteid, $type);

        $_lib['db']->db_query($q);
        return $_lib['db']->db_insert_id();
    }

    /**
     * Delete a scheme
     *
     * FakturabankSchemeID is the index in table to delete
     */
    static function deleteSchemeType($FakturabankSchemeID) {
        global $_lib;

        $q = sprintf("DELETE FROM fakturabankscheme WHERE FakturabankSchemeID = %d",
                     $FakturabankSchemeID);
        $_lib['db']->db_query($q);
    }

    /**
     * Find an accountplanid associated with a scheme type and value
     * i.e. findAccountPlan("NO:ORG", "12345678")
     */
    static function findAccountPlan($schemetype, $schemevalue) {
        global $_lib;

        $schemetype = trim($schemetype);
        $schemevalue = trim($schemevalue);

        if($schemevalue == "")
            return null;

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

        if($row)
            return $row['AccountPlanID'];
        else
            return null;
    }

    /**
     * Find an accountplanid given a scheme type and value + the type of the account.
     * findAccoutnPlanType("NO:ORG", "12345678", "supplier")
     */
    static function findAccountPlanType($schemetype, $schemevalue, $accountType) {
        global $_lib;

        $schemetype = trim($schemetype);
        $schemevalue = trim($schemevalue);

        if($schemevalue == "")
            return null;

        $q = sprintf("SELECT a.AccountPlanID as AccountPlanID
                        FROM accountplanscheme a,
                             fakturabankscheme f,
                             accountplan ap
                        WHERE
                          a.SchemeValue = '%s'
                          AND a.FakturabankSchemeID = f.FakturabankSchemeID
                          AND a.AccountPlanID = ap.AccountPlanID
                          AND f.SchemeType = '%s'
                          AND ap.AccountPlanType = '%s'",
                     $_lib['db']->db_escape($schemevalue),
                     $_lib['db']->db_escape($schemetype),
                     $_lib['db']->db_escape($accountType)
            );

        $r = $_lib['db']->db_query($q);
        $row = $_lib['db']->db_fetch_assoc($r);

        if($row)
            return $row['AccountPlanID'];
        else
            return null;
    }

    static function fetchSchemesFromFakturaBank(){
      includelogic("fakturabank/fakturabank");
        $fakturabank = new lodo_fakturabank_fakturabank();

        $page = "rest/identificators.json";
        $url = $fakturabank->construct_fakturabank_url($page);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CAINFO, "/etc/ssl/fakturabank/cacert.pem");

        $result = curl_exec($ch);

        return json_decode($result);
    }

    function refreshSchemes($json) {
        global $_lib;

        foreach($json as $json_node) {
            $found = false;
            foreach($this->globalSchemes as $existing) {
                if($existing["FakturabankRemoteSchemeID"] == $json_node->identificator->id) {
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                $q = sprintf("INSERT INTO fakturabankscheme
                              (`FakturabankRemoteSchemeID`, `SchemeType`)
                              VALUES (%d, '%s');", $json_node->identificator->id, $json_node->identificator->name);
            }
            else {
                $q = sprintf("UPDATE fakturabankscheme SET SchemeType = '%s' WHERE FakturabankRemoteSchemeID = %d",
                             $json_node->identificator->name, $json_node->identificator->id);
            }


            $_lib['db']->db_query($q);
        }

        $this->globalSchemes = lodo_accountplan_scheme::listAvailableTypes();
    }

    var $AccountPlanID = 0;
    var $globalSchemes = null;

    function __construct($AccountPlanID) {
        $this->AccountPlanID = $AccountPlanID;
        $this->globalSchemes = lodo_accountplan_scheme::listAvailableTypes();
    }

    /**
     * Get a cached list of scheme types
     */
    function listTypes() {
        return $this->globalSchemes;
    }

    /**
     * Add a scheme association with the accountplan this object was constructed with
     */
    function addScheme($FakturabankSchemeID, $SchemeValue) {
        global $_lib;

        $SchemeValue = trim($SchemeValue);

        $q = sprintf("INSERT INTO `accountplanscheme` (`AccountPlanID`, `FakturabankSchemeID`, `SchemeValue`)
                        VALUES(%d, %d, '%s');",
                     $this->AccountPlanID, $FakturabankSchemeID, $_lib['db']->db_escape($SchemeValue));

        $_lib['db']->db_query($q);
    }

    /**
     * List all schemes associated with the accountplan this object was constructed with
     */
    function listSchemes() {
        global $_lib;

        $q = sprintf("SELECT * FROM accountplanscheme WHERE AccountPlanID = %d ORDER BY AccountPlanSchemeID",
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

    function findScheme($fakturabankId) {
      global $_lib;
      $q = sprintf("SELECT * FROM fakturabankscheme WHERE FakturabankSchemeID = %d LIMIT 1",
                   $fakturabankId);
      $db_obj = $_lib['db']->db_query($q);
      $row = $_lib['db']->db_fetch_assoc($db_obj);
      return $row['SchemeType'];
    }

    function getFirstFirmaID() {
      $firma_ids = $this->listSchemes();
      if (empty($firma_ids)) {
        return false;
      } else {
        $fakturabank_scheme_id = (int)($firma_ids[0]['FakturabankSchemeID']);
        $firma_id_type = $this->globalSchemes[$fakturabank_scheme_id]['SchemeType'];
        $firma_id = $firma_ids[0]['SchemeValue'];
      }
      return array('value' => $firma_id, 'type' => $firma_id_type);
    }
};
