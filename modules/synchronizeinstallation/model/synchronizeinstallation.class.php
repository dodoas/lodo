<?
# $Id: record.inc,v 1.55 2005/10/14 13:15:43 thomasek Exp $ ConfDBFields_record.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no/

class lodo_synchronizeinstallation_synchronizeinstallation {

    public $new     = 0;
    public $exists  = 0;
    public $total   = 0;

    function __construct() {
    
    }

    #input: db_name (of database with installation table)
    function updateinstalltable($args) {
        global $_lib, $_SETUP;

        #connect to local database with master installation table
        if(!isset($args['db_name'])) {
            $_lib['message']->add("Missing db_name=>");
            return;
        }
        $databaseName   = $args['db_name'];
        $dsn            = $_SETUP['DB_SERVER_DEFAULT'] . $databaseName . $_SETUP['DB_TYPE_DEFAULT'];
        $dbh[$dsn]      = new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'], 'database' => $databaseName, 'username' => $_SETUP['DB_USER_DEFAULT'], 'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

        $query          = "select InstallName, InstallName from installation";
        $installedH     = $dbh[$dsn]->get_hash(array('query' => $query, 'key' => 'InstallName', 'value' => 'InstallName'));

        #Find all other databases
        $query_show = "show databases";
        $result     = $_lib['db']->db_query($query_show);

        while($row = $_lib['db']->db_fetch_object($result)) {
            #print_r($row);
            #print "$row->Database\n";
            if($installedH[$row->Database]) {
                $this->exists++;
            } else {
                $this->new++;
                $query_insert   = "insert into installation set InstallName='$row->Database', Active=1";
                print "$query_insert\n";
                $dbh[$dsn]->db_insert($query_insert);
            }

            $this->total++;
        }
        
        print "Totalt antall databaser: $this->total, eksisterende: $this->exists, nye: $this->new\n";
    }
}
?>