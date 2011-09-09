<?
class Install
{
    private $old;
    private $new;
    private $username;
    private $password;
    private $dbpasswordp;
    private $host;
    private $tables         = array();
    private $mysql          = "";
    private $mysqladmin     = "";
    private $mysqldump      = "";
    private $homedir        = "";
    private $dsn_remote     = "";
    private $InstallationID = 0;
    private $_dbh           = array();
    private $debug          = false;

    function __construct($args)
    {
        global $_SETUP, $_sess;

        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }

        $this->homedir      = $_SETUP['HOME_DIR'];
        $this->mysql        = $_SETUP['MYSQL'];
        $this->mysqladmin   = $_SETUP['MYSQLADMIN'];
        $this->mysqldump    = $_SETUP['MYSQLDUMP'];

        if(strlen($this->dbpassword) > 1)
        {
            $this->dbpasswordp = "-p".$this->dbpassword;
        }
        else
        {
            unset($this->dbpasswordp);
            unset($this->dbpassword);
        }
    }

    #only datastructure
    function install_database()
    {
        global $_lib;
        #Dump the old database
        $command = "$this->mysqldump --skip-add-drop-table -u$this->dbuser $this->dbpasswordp -d $this->install_old > /tmp/$this->install_new";
        if($this->debug) print "$command<br>\n";
        #print "<br>\nDatabasedump: /tmp/$this->install_new<br>\n";
        $message = system($command);
        #print "Message from dump: $message<br>";
        #Create the new database
        if($_lib['db']->db_query("create database $this->install_new"))
        {
            #print "Installerer ny database<br>\n";
            #print "Database: $this->install_new created successfully<br>\n";
        }
        else
        {
            $_lib['sess']->error("Error creating database: " . mysql_error() . "<br>\n");
            print "Error creating database: " . mysql_error() . "<br>\n";
        }

        #Import the old database into the new
        $command = "$this->mysql -u$this->dbuser $this->dbpasswordp $this->install_new < /tmp/$this->install_new";
        if($this->debug) print "$command<br>\n";
        system($command);

        $this->dsn_remote = $this->dbserver . $this->install_new . $this->dbtype;
        $this->_dbh[$this->dsn_remote] = new db_mysql(array('host' => $this->dbserver, 'database' => $this->install_new, 'username' => $this->dbuser, 'password' => $this->dbpassword));
  }

    #data from tables
    function install_tables()
    {
        $i      = 0;

        foreach($this->tables as $table => $value)
        {
            $where  = '';
            $i++;

            #Dump innholdet i de ?nskede tabellene
            if($table == "accountplanNorwegian")
            {
              $where = "-w \"EnableNorwegianStandard=1\"";
              $table = "accountplan";
            }
            elseif($table == "accountplan")
            {
              $where = "-w \"AccountPlanID >= 1000 and AccountPlanID <= 9999\"";
              $table = "accountplan";
            }
            elseif($table == "companydepartment")
            {
              $where = " -w \"CompanyDepartmentID != 0\"";
            }
            elseif($table == "project")
            {
              $where = "-w \"ProjectID != 0\"";
            }
            elseif($table == "salaryconf" || $table == "salaryconfline")
            {
              $where = "-w \"SalaryConfID = 1\"";
            }


            if($i == 1)
                $operator = '>';
            else
                $operator = '>>';

            $command = "$this->mysqldump --skip-add-drop-table -u$this->dbuser $this->dbpasswordp --no-create-info $this->install_old  $table $where $operator /tmp/$this->install_new";
            if($this->debug) print "$command<br>\n";
            $retval = 0;
            $retval = system($command);
            if($this->debug) print "Resultat: $retval <br>\n";
        }

        #Importer innholdet i tabellene
        $command = "$this->mysql -u$this->dbuser $this->dbpasswordp $this->install_new < /tmp/$this->install_new";
        if($this->debug) print "<br>Importerer tabeller: /tmp/$this->install_new<br>";
        if($this->debug) print "$command<br>\n";
        system($command);
    }

    function create_periods() 
    {
        $year = date("Y");
        for($i = 1; $i <= 13; $i++) {
            $period = sprintf("%d-%02d", $year, $i);
            $query = sprintf("INSERT INTO accountperiod (`Status`, `Period`) VALUES (2, '%s');", $period);
            $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));
        }
    }


    function insert_user($args)
    {
        global $_lib, $_SETUP;

        if($_SETUP['VERSION'] == 1)
        {
            $persontablename = 'person';
        }
        else
        {
            $persontablename = 'person';
        }

        $post = array();
        $post[$persontablename.'_Email']      = $args['Email'];
        $post[$persontablename.'_Password']   = "PASSWORD('" . $args['Password'] . "')";
        $post[$persontablename.'_FirstName']  = $args['FirstName'];
        $post[$persontablename.'_LastName']   = $args['LastName'];
        $post[$persontablename.'_AccessLevel']= $args['AccessLevel'];
        $post[$persontablename.'_MobilePhoneNumber']   = $args['MobilePhoneNumber'];

        $post[$persontablename.'_Active']     = 1;
        // print_r($post);

        if($this->debug) {
            print "Oppretter bruker, _dsn_remote: $this->dsn_remote<br>";
            print_r($post);
        }
        $PersonID = $this->_dbh[$this->dsn_remote]->db_new_hash($post, $persontablename);

        $query = "insert into roleperson set RoleID='5', PersonID='$PersonID'";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        if($post[$persontablename.'_AccessLevel'] == 4) {

            #Administrative user
            $query = "insert into companypersonstruct set CompanyID=4, PersonID=$PersonID, Active=1";
            if($this->debug) print "$query<br>\n";
            $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

            $query = "insert into companypersonstruct set CompanyID=2, PersonID=$PersonID, Active=1";
            if($this->debug) print "$query<br>\n";
            $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));
        } else {

            #Normal user
            $query = "insert into companypersonstruct set CompanyID=1, PersonID=$PersonID, Active=1";
            if($this->debug) print "$query<br>\n";
            $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));
        }

        return $PersonID;
    }

    function insert_company($args) {
        global $_lib;

        #print insert role
        #print "Setter inn rolle<br>";
        $query = "insert into company set CompanyID=1, CreatedDate=NOW(), ValidFrom=NOW(), CompanyName='" . $args['installation_VName']. "', VName='" . $args['installation_VName']. "', VAddress='" . $args['installation_VAddress']. "', VZipCode='" . $args['installation_VZipCode']. "', VCity='" . $args['installation_VCity']. "', Phone='" . $args['installation_Phone']. "', Fax='" . $args['installation_Fax']. "', WWW='" . $args['installation_WWW']. "', OrgNumber='" . $args['installation_CompanyNumber']. "'";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        $query = "insert into company set CompanyName='Regnskapsf?rerFirma', VName='Regnskapsf?rerFirma', CompanyID=2, ClassificationID=2, CreatedDate=NOW(), ValidFrom=NOW()";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        $query = "insert into company set CompanyName='RevisorFirma',VName='RevisorFirma', CompanyID=3, ClassificationID=3, CreatedDate=NOW(), ValidFrom=NOW()";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        $query = "insert into company set CompanyName='Lodo', VName='Lodo', CompanyID=4, CreatedDate=NOW(), ValidFrom=NOW()";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        $query = "delete from companydepartment where CompanyDepartmentID=0";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_delete($query);

        $query = "insert into companydepartment set CompanyDepartmentID=0, DepartmentName='Diverse', Active=1";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        $query = "delete from project where ProjectID=0";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_delete($query);

        $query = "insert into project set ProjectID=0, Heading='Diverse', Active=1";
        if($this->debug) print "$query<br>\n";
        $this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));

        //$year = $_date->get_this_year($_lib['sess']->get_session('LoginFormDate'));

        //for($i=1; $i<=13; $i++)
        //{
          //$tmp = sprintf("%04d-%02d", $year, $i);
          //$query = "insert into accountperiod set CreatedDate=NOW(), CreatedByID=$_lib['sess']->login_id, Status=0, Payed=1, Period=$tmp";
          //$this->_dbh[$this->dsn_remote]->db_insert2(array('query' => $query, 'insert_id' => false));
        //}
        #print "Ferdig bruker<br>\n";
        return true;
    }

    function vat($args)
    {
        global $_lib;

        if($args['vat'] == 2) #Copy without vat
        {
            $query = "update company set EnableVAT=0, AccountVat=0";
            $this->_dbh[$this->dsn_remote]->db_query3(array('query'=>$query, 'do_not_die'=>'1'));

            $query = "update vat set Active=0, AccountPlanID=0";
            $this->_dbh[$this->dsn_remote]->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
        }
    }

    function install_prefs()
    {
        global $_lib;
        $from = $this->homedir."/conf/prefs_".$this->install_old.".inc";
        $to   = $this->homedir."/conf/prefs_".$this->install_new.".inc";

        #print "Kopierer preferanser: $from to $to<br>";
        $lines = file($from);
        #print "Les linjer<br>\n";

        if(is_writable($this->homedir."/conf/"))
        {
            #print "?pne preferanse mal<br>\n";
            $handle = fopen($to, 'w+');
            if($this->debug) print "Skriv ny preferanse<br>\n";
            foreach ($lines as $line_num => $line)
            {
              $newline = str_replace("\"".$this->install_old."\"", "\"".$this->install_new."\"", $line);
              fwrite($handle, $newline);
            }
            fclose($handle);
        }
        else
        {
            $_lib['sess']->error("Not writeable: $to<br>");
            #print "Not writeable: $to<br>";
        }

        #print "Ferdig med ? intsallere preferanser";
    }

    /* Denne funksjonen skj?nner jeg ikke helt hva skal brukes til */
    function salary() {
        global $_lib;
        $query = "select SalaryConfID from salaryconf where SalaryConfID=1";
        $row = $_lib['db']->get_row(array('query' => $query));
        if($row->SalaryConfID != 1)
        {
          $query = "insert into salaryconf (SalaryConfID, AccountPlanID, CreatedByPersonID) values (1, ".$_lib['sess']->get_companydef('AccountEmployeeFrom').", 1)";
          $this->_dbh[$this->dsn_remote]->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
          #print "Installer l?nns konfigurasjon<br>";
        }
        $query = "select AccountPlanID from accountplan where AccountPlanID='".$_lib['sess']->get_companydef('AccountEmployeeFrom')."'";
        $row = $_lib['db']->get_row(array('query' => $query));
        if($row->AccountPlanID != $_lib['sess']->get_companydef('AccountEmployeeFrom'))
        {
          $query = "insert into accountplan (AccountPlanID, CreatedByPersonID) values (".$_lib['sess']->get_companydef('AccountEmployeeFrom').", 1)";
          $this->_dbh[$this->dsn_remote]->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
          #print "Installer ansatt kontoplan<br>";
        }
    }

} # end class
?>
