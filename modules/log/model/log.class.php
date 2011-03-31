<?

includelogic('invoicerecurring/recurring');

//
// extends model_invoicerecurring_recurring for database_list-function
//
class model_log_log extends model_invoicerecurring_recurring
{
    private $relative_destination = "modules/log/view/log.json";

    function iter_all_db()
    {
        global $_lib, $_SETUP;

        $dbs = $this->database_list();
        $logging = array();

        foreach($dbs as $db)
        {
            $name = $db->Database;
            $_lib['storage'] = $_lib['db'] =
                new db_mysql(array('host' => $_SETUP['DB_SERVER_DEFAULT'],
                                   'database' => $name,
                                   'username' => $_SETUP['DB_USER_DEFAULT'],
                                   'password' => $_SETUP['DB_PASSWORD_DEFAULT']));

            $_lib['cache'] = new Cache(array());
            $_lib['setup'] = new framework_lib_setup(array());
            $_lib['format'] = new format(array('_NF' => $_NF, '_DF' => $_DF, '_dbh' => $_dbh, '_dsn' => $_dsn));

            $query = "SHOW TABLES LIKE 'mvaavstemming';";
            $row = $_lib['db']->get_row(array('query' => $query));

            if (empty($row)) 
            {
                continue;
            }

            $query = "SELECT IPAdress, TS FROM logusage ORDER BY TS DESC LIMIT 10";
            $r = $_lib['db']->db_query($query);

            $logging[$name] = array();
            while($row = $_lib['db']->db_fetch_assoc($r))
            {
                $logging[$name][] = $row;
            }
        }

        $path = $_SETUP['HOME_DIR'] . "/" . $this->relative_destination;

        file_put_contents($path, json_encode($logging));
    }
}