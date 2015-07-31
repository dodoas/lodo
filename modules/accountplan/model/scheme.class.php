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

        $r = $_lib['db']->db_query("SELECT * FROM fakturabankscheme");
        while( ($row = $_lib['db']->db_fetch_assoc($r)) ) {
            $l[] = $row;
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

    function refreshSchemes($_schemes = false) {
        global $_lib;
        includelogic("fakturabank/fakturabank");
        includelogic("oauth/oauth");
        $fakturabank = new lodo_fakturabank_fakturabank();
        $client = new lodo_oauth();

        $page = "rest/identificators.json";
        $url = $fakturabank->construct_fakturabank_url($page);
        $_SESSION['oauth_action'] = 'get_identificators';
        if (!$_schemes) $client->get_resources($url);
        else $decoded_json = $_schemes;

        foreach($decoded_json as $json_node) {
            $json_node = $json_node['identificator'];
            $found = false;
            foreach($this->globalSchemes as $existing) {
                if($existing["FakturabankRemoteSchemeID"] == $json_node['id']) {
                    $found = true;
                    break;
                }
            }

            if(!$found) {
                $q = sprintf("INSERT INTO fakturabankscheme 
                              (`FakturabankRemoteSchemeID`, `SchemeType`)
                              VALUES (%d, '%s');", $json_node['id'], $json_node['name']);
            }
            else {
                $q = sprintf("UPDATE fakturabankscheme SET SchemeType = '%s' WHERE FakturabankRemoteSchemeID = %d",
                             $json_node['name'], $json_node['id']);
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
};