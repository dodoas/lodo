<?

$maindb['db']       = $_SETUP['LODO_DEFAULT_INSTALL_DB'];
$maindb['host']     = $_SETUP['LODO_DEFAULT_INSTALL_SERVER'];
$maindb['username'] = $_SETUP['LODO_DEFAULT_INSTALL_USER'];
$maindb['password'] = $_SETUP['LODO_DEFAULT_INSTALL_PASSWORD'];

$_lib['maindb']     = new db_mysql(array('host' => $maindb['host'], 'database' => $maindb['db'], 'username' => $maindb['username'], 'password' => $maindb['password'], '_sess' => $_sess));

if($_REQUEST['action_install_new'])
{
    $query          = "insert into installation set CreatedDateTime=NOW()";
    $InstallationID = $_lib['maindb']->db_insert($query);
    print "ID: $InstallationID<br>\n";
}
elseif($_REQUEST['action_install_db'])
{
    $primarykey['InstallationID'] = $InstallationID;
    $_POST['installation_CreatedDateTime']  = 'NOW()';

    #Possible to extend or alter parameters here
    $_lib['maindb']->db_update_hash($_POST, 'installation', $primarykey);
    #print_r($_POST);

    $installation = get_DealerInfo($InstallationID);

    #Tables to copy
    $tables = array();
    $tables['confmenues']               = true;
    $tables['confdbfields']             = true;
    $tables['role']                     = true;
    $tables['roletemplate']             = true;
    $tables['roletemplateaccess']       = true;
    $tables['roletableaccess']          = true;
    $tables['setup']                    = true;
    $tables['menu']                     = true;
    $tables['linetextmap']              = true;
    $tables['altinnschemalist']         = true;

    $vat  = $_REQUEST['install_vat'];
    if($_REQUEST['install_vat'] == 1) {
        $tables['vat'] = true;
    }

    #print_r($_REQUEST);

    if($_REQUEST['install_accountplan']==1){
        $tables['accountplan']          = true;
    }
    elseif($_REQUEST['install_accountplan']==2){
        $tables['accountplanNorwegian'] = true;
    }

    /*if(isset($_REQUEST['install_accountperiod']) && $_REQUEST['install_accountperiod']){
        $tables['accountperiod']        = true;
    }*/

    if(isset($_REQUEST['install_arbeidsgiveravgift']) && $_REQUEST['install_arbeidsgiveravgift']){
        $tables['arbeidsgiveravgift']   = true;
    }

    if(isset($_REQUEST['install_exchange']) && $_REQUEST['install_exchange']){
        $tables['exchange']             = true;
    }

    if(isset($_REQUEST['install_project']) && $_REQUEST['install_project']){
        $tables['project']              = true;
    }

    if(isset($_REQUEST['install_department']) && $_REQUEST['install_department']){
        $tables['companydepartment']    = true;
    }

    if(isset($_REQUEST['install_salaryconf']) && $_REQUEST['install_salaryconf']){
        $tables['salaryconf']           = true;
        $tables['salaryconfline']       = true;
    }

    if(isset($_REQUEST['install_company']) && $_REQUEST['install_company']){

        $tables['company']              = true;
        #$tables['companycompanystruct'] = true;

        #if($_REQUEST['install_persons']){
        #  $tables['person']              = true;
        #  $tables['companypersonstruct']  = true;
        #  $tables['roleperson']           = true;
        #  $tables['roletemplateaccess']   = true;
        #}
    }

    #print_r($tables);

    ##########
    includelogic("install/install");
    // include_once "Install.inc";
    $install = new Install(array('InstallationID' => (int) $_REQUEST['InstallationID'], 'dbuser' => $_SETUP['DB_USER']['0'], 'dbpassword' => $_SETUP['DB_PASSWORD']['0'], 'dbserver' => $_SETUP['DB_SERVER']['0'], 'dbtype' => $_SETUP['DB_TYPE']['0'],'install_old' => $_REQUEST['inst_copy_from'], 'install_new' => $_REQUEST['installation_InstallName'], 'tables' => $tables));
    ##########
    $install->install_database();
    #print "Installer database<br>";
    ##########
    $install->install_tables();
    #print "Installer tabeller<br>";
    ##########
    $install->vat(array('vat' => $vat));
    #print "Installer MVA<br>";
    ##########
    $install->install_prefs();
    #print "Installer preferanser<br>";
    ##########
    $install->create_periods();


    #Do not insert user information if we have chosen to copy company tables

    # Install Admin user
    $args_admin['Email']              = $_REQUEST['Email'];
    $args_admin['Password']           = $_REQUEST['Password'];
    $args_admin['FirstName']          = $_REQUEST['FirstName'];
    $args_admin['MobilePhoneNumber']  = $_REQUEST['MobilePhoneNumber'];
    $args_admin['LastName']           = $_REQUEST['LastName'];
    $args_admin['AccessLevel']        = 4;
    $install->insert_user($args_admin);

    #Install customer user
    $args_customer['Email']              = $installation->Email;
    $args_customer['Password']           = $installation->Password;
    $args_customer['FirstName']          = $installation->FirstName;
    $args_customer['MobilePhoneNumber']  = $installation->MobilePhoneNumber;
    $args_customer['LastName']           = $installation->LastName;
    $args_customer['AccessLevel']        = 2;
    $install->insert_user($args_customer);

    #print "Installer bruker<br>";

    if(!isset($tables['company'])) {
        print "Installerer etter firmaopplysningene som er oppgitt<br>\n";
        $install->insert_company($_REQUEST);
    }

    ##########
    //$args = array();

    $install->salary();

    #print "Installasjon ferdig<br>";
    $_REQUEST['installation_finished'] = true;
}
elseif($_REQUEST['action_install_drop'])
{
    $inst = $_lib['maindb']->db_escape($_REQUEST['inst']);
    $query = "drop database if exists ".$inst."";
    $_lib['maindb']->db_query3(array('query'=>$query, 'do_not_die'=>'1'));
    //mysql_drop_db($_REQUEST['inst']);  #DEPRECATED! ta vekk etterhvert
    $query = "delete from installation where InstallName='".$_REQUEST['inst']."' or InstallName is NULL";
    $_lib['maindb']->db_query($query);
    $p_file = $_SETUP['HOME_DIR'] . "/code/lib/setup/prefs_" . $row->InstallName . ".inc";
    if(is_file($p_file))
        unlink($p_file);
    $_lib['message']->add("Kunde slettet: " . $_REQUEST['inst']);
}
elseif($_REQUEST['action_install_update']) {
    $primarykey['InstallationID'] = $InstallationID;

    #Possible to extend or alter parameters here
    $_lib['maindb']->db_update_hash($_POST, 'installation', $primarykey);
}
?>
