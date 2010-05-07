<?
#Random id
#$unique_code = substr(md5(uniqid(rand(), 1)), 0, 6);

class SessionNew
{
  public $_mysession   = array();
  public $person       = array();
  public $company      = array();
  public $companydef   = array();
  public $setup        = array();
  public $glob         = array();
  public $args         = array();
  public $role         = array();
  public $roletemplate = array();
  public $tableaccess  = array();
  public $debug_hash   = array();
  public $w_count      = 0;
  public $d_count      = 0;
  public $e_count      = 0;
  public $dbh          = "";
  public $dsn          = "";
  public $db           = "";
  public $login_id     = "";
  public $language     = "";
  public $defcompany_id= "";
  public $company_id   = "";
  public $debugflag    = 0;
  public $accesslevel  = -1;
  public $template     = "";
  public $module       = "";
  public $interface    = "";
  public $doctype   = "";
  public $headH     = array();

  public function __construct($args) { #Konstruktor
    global $_SETUP;
    $this->interface = $args['interface'];
    $this->module    = $args['module'];
    $this->template  = $args['template'];
    #print "#$this->module, $this->template#<br>";

    #The first possible init of Empatix
     $this->debug("Init session object");
    if(isset($args['login_id'])) {
      $this->login_id      = $args['login_id'];
    }
    $this->defcompany_id = $args['company_id'];

    $this->_mysession         = $this->set_session($args['interface'], $args['module'], $args['template'], $args['LoginFormDate']);
    $this->glob               = $this->set_glob();
    $this->args               = $this->set_args();


    $this->dispatchx = "/lodo.php?t=$this->module.$this->template";
    $this->dispatchr = "/lodo.php?SID=".$this->get_session('SID') . "&amp;";        # (R) For refresh only - funker ikke med &amp;
    $this->dispatchs = "/lodo.php?";                                            # (S) Simple - without session, & and other special signs, for form login, etc
    $this->dispatch  = "/lodo.php?SID=".$this->get_session('SID') . "&";    #Add session to all URLS cookies could be disabled

    if($_SETUP['XML']) {
        $this->doctype  = $_SETUP['XML']."\n";
    }
    $this->doctype .= $_SETUP['DOCTYPE']."\n";
    $this->doctype .= $_SETUP['HTML']."\n";

    #_MY_SELF used for forms posting to the same page, simulates $_PHP_SELF
    $this->my_self  = '/'.$_SETUP['ACTIVE_INTERFACE'].'/'."index.php?SID=".$this->get_session('SID') . "&amp;t=" . $this->module . "." . $this->template . "&amp;_Level1ID=".$_REQUEST['_Level1ID']."&amp;_Level2ID=".$_REQUEST['_Level2ID']."&amp;";
  }

  public function SessionInit($args) {
    if(isset($args['login_id'])) {
      $this->login_id      = $args['login_id'];
    }
    $this->dbh                = $args['dbh'];
    $this->dsn                = $args['dsn'];
    $this->db                 = $this->dbh[$this->dsn];
    $this->language           = $args['language'];

    #Secon stage init, after db connections is up and running.
    $this->setup              = $this->set_setup();
    $this->person             = $this->set_person();
    $this->company            = $this->set_company();
    #print_r($this->company);
    #print "";
    $this->company_id         = $this->company->CompanyID;
    #print "ID: $this->company_id <br>";
    $this->companydef         = $this->set_companydef($args['interface']);

    $this->role               = $this->set_role();
    $this->roletemplateaccess = $this->set_roletemplateaccess();
    $this->tableaccess          = $this->set_tableaccess();
    $this->roletemplate       = $this->set_roletemplate();
    $this->title              = "Lodo - " . $_SESSION['DB_NAME'] . " - " . $interface . " - " . $this->get_person('FirstName') . " " . $this->get_person('LastName');
  }

  ############################################################
  function get_person($field) {
    return $this->person->{$field};
  }

  ############################################################
  function get_interface() {
    if(!$this->login_id) { print "Session: Du er ikke logget inn (get interface)"; exit;};

    $interface = Array();

    $query  = "select distinct Interface from role as R, roleperson as P where P.PersonID='$this->login_id' and P.RoleID=R.RoleID";
    #print "$query<br>";
    $result = $this->db->db_query($query);
    $i = 0;
    while($_row    = $this->db->db_fetch_object($result)) {
      $interface[$i] = $_row->Interface;
      $i++;
    }

    #$max = length($this->role);
    #for($i = 0; $i++; $i <= $max){
    #   $interface[$i] = $this->role[$i]{Interface};
    #}
    return $interface;
  }

  ############################################################
  function get_company($field) {
    return $this->company->{$field};
  }

  function get_companydef($field) {
    return $this->companydef->{$field};
  }

  ############################################################
  function get_session($field) {
    return $this->_mysession[$field];
  }

  ############################################################
  function get_setup($field) {
    return $this->setup[$field];
  }

  ############################################################
  function get_glob($field) {
    return $this->glob[$field];
  }

  ############################################################
  function get_args($field) {
    return $this->args[$field];
  }

    #################
    # get default user interface, module, template
    function get_defaultUserLogin()
    {
        global $_dbh, $_dsn, $_lib;

        if(strlen($this->get_person('PersonID')) > 0 and is_numeric($this->get_person('PersonID')))
        {
            $query_login = "select DefaultInterface, DefaultModule, DefaultTemplate from person where PersonID = ".$this->get_person('PersonID');
            //print "$query_login<br>";

            $userLogin = $_lib['db']->get_row(array('query' => $query_login));

            if((strlen($userLogin->DefaultInterface) > 0) and (strlen($userLogin->DefaultModule) > 0) and (strlen($userLogin->DefaultTemplate) > 0))
            {
                $interface = $userLogin->DefaultInterface;
                $loginPage = $userLogin->DefaultModule.".".$userLogin->DefaultTemplate;

                return array('result'=>'1', 'interface'=>$interface, 'loginPage'=>$loginPage);
            }
        }

        return array('result'=>'0');
    }

  ############################################################
  #If not spesified which template, it will check the default template inited the session
  function check_roletemplate($interface, $module, $template)
  {
    #remove
    #$this->accesslevel = 3;
    #return $this->accesslevel;
    #print $interface.", ".$module.", ".$template;

    if(!$interface)
    {
      $interface = $this->interface;
    }
    if(!$module)
    {
      $module = $this->module;
    }
    if(!$template)
    {
      $template = $this->template;
    }

    $dbh = $this->dbh;
    #print "roletemplate check<br>";
    #We cashe the current accesslevel so we dont have to specify template the next time
    #Does not work in menu buliding
    #if($this->accesslevel > -1) {
     # return $this->accesslevel;
    #}
    if($this->login_id > 0 or ($interface == 'lodo' and $module == 'lodo' and $template == 'index'))
    {
       $accesslevel = 3;
    }
    if($module == 'lib')
    { #Library functions without access control, is it smart?
       $accesslevel = 1;
    }
    else
    {
        #Tempolates with accesslevel=0 is public - everybody can see them without logging in
        #Fastest to implement role control
        if($this->login_id)
        {
          #Not a public template - check if the person has access with login_id
          $query  = "select A.Interface, A.Module, A.Template, A.AccessLevel from roletemplateaccess as A, roleperson as P where P.PersonID='$this->login_id' and P.RoleID=A.RoleID and A.Interface='$interface' and A.Module='$module' and A.Template='$template' order by A.AccessLevel desc limit 1";
          $_row   = $this->db->get_row(array('query' => $query));
          #print("!-- $query -->\n");
          if($_row)
          {
            #print "Authorized access";
            $accesslevel = $_row->AccessLevel; #You have logged inn access
          }
        }
        if($accesslevel <= 0)
        {
          $query  = "select Interface, Module, Template from roletemplate where AccessLevel=0 and Interface='$interface' and Module='$module' and Template='$template'";
          #print "!-- $query -->\n";
          $_row   = $this->db->get_row(array('query' => $query));

          if($_row) {
            #print "No access required";
            $accesslevel = 1;
          }
        }
    }
    #print "interface: #$interface#$module#$template# AC: $accesslevel <br>";
    return $accesslevel;

    # Best role control - not implemented
    #  if(($this->roletemplate[$module] && $this->roletemplate[$_template]) || ($this->roletemplateaccess[$module] && $this->roletemplateaccess[$_template])) {
    #    #This user has access to the module and template asked for or the module.template is public (AccessLevel = 0)
    #    return 1;
    #  }
    #  else {
    #    print "Access denied (session->check_roletemplate). Your role is not authorized to view this template<br>";
    #    return 0;
    #  }
  }
    #Litt voldsom med full backtrace i noen sammnhenger
    private function backtrace($backtrace) {

       #print_r($backtrace);
       for ($i=0;$i<count($backtrace);$i++) {
            // skip the first one, since it's always this func
            if ($i==0) { continue; }
            $backtrace[$i] = $this->backtraceline($backtrace[$i]);
       } // for
       return $backtrace;
    }
    
    private function backtraceline($backtraceH) {
        $MAXSTRLEN = 150;
    
        $backtraceH['time'] = $this->get_microtime();
        unset($backtraceH['object']); #her ligger faktisk hele objektet til det som blir dumpet.

        $backtraceH['file'] = basename($backtraceH['file']);
        $args               = "";
        
        if(!empty($backtraceH['args'])) {
            foreach($backtraceH['args'] as $v) {
                if (is_null($v)) $args = '';
                elseif (is_array($v)) {
                    $args  .= "Array(";
                    foreach($v as $key => $value) {
                        if(!is_object($key) && !is_object($value)) {
                            $args .= "'$key' => '$value', ";
                        }
                    }
                    $args = substr($args, 0, -2);
                    $args .= ")";
                }
                elseif (is_object($v)) $args .= 'Object:'.get_class($v);
                elseif (is_bool($v))   $args .= $v ? 'true' : 'false';
                else {
                    $v = (string) @$v;
                    $str = htmlspecialchars(substr($v,0,$MAXSTRLEN));
                    if (strlen($v) > $MAXSTRLEN) $str .= '...';
                    $args .= "\"".$str."\"";
                }
            }
        }
        $backtraceH['args']  = $args;
        $backtraceH['trace'] = $backtraceH['class'] . '->' . $backtraceH['function'] . '(' . $backtraceH['args'] . ')';
        return $backtraceH;
    }

    ############################################################
    public function debug($text) {        
        global $_SETUP;

        $this->d_count++;

        // only collect stacktrace if we are running as debug
        if(empty($_SETUP['DEBUG'])) {
            return;
        }
        
        $backtraceA = debug_backtrace();
        array_shift($backtraceA);
        $backtraceH = array_shift($backtraceA); #Element 2 is the calling function.
        $backtraceH = $this->backtraceline($backtraceH);
        $backtraceH['type'] = 'DEBUG';
        $backtraceH['text'] = $text;
        unset($backtraceH['object']); #her ligger faktisk hele objektet til det som blir dumpet.

        array_push($this->debug_hash, $backtraceH);
    }

    ############################################################
    function warning($text) {
        $this->w_count++;

        $backtraceA = debug_backtrace();
        array_shift($backtraceA);
        $backtraceH = array_shift($backtraceA); #Element 2 is the calling function.
        $backtraceH = $this->backtraceline($backtraceH);
        $backtraceH['type'] = 'WARNING';
        $backtraceH['text'] = $text;
 
        array_push($this->debug_hash, $backtraceH);

        if ( method_exists($this->db, db_insert) ){
            $query = "INSERT INTO logapplication set Type='W', PersonID='$this->login_id', Description='$text', Trace='" . $this->db->db_escape($backtraceH['trace']) . "'";
            $result = $this->db->db_insert($query);
        }
    }

    ############################################################
    function error($text) {
        global $_dbh, $_dsn;
        $this->e_count++;

        $backtraceA = debug_backtrace();
        array_shift($backtraceA);
        $backtraceH = array_shift($backtraceA); #Element 2 is the calling function.
        $backtraceH = $this->backtraceline($backtraceH);
        $backtraceH['type'] = 'ERROR';
        $backtraceH['text'] = $text;
 
        array_push($this->debug_hash, $backtraceH);

        if ( method_exists($this->db, db_insert) ){
            $text = $this->db->db_escape($text);
            $query = "INSERT INTO logapplication set Type='E', PersonID='$this->login_id', Description='$text', Trace='" . $this->db->db_escape($backtraceH['trace']) . "'";
            $result = $this->db->db_insert($query);
        }
        $this->print_debug();
        exit;
    }

    #Should be placed in date library
    function get_microtime(){
      list($usec, $sec) = explode(" ",microtime());
      $microtime = ((float)$usec + (float)$sec);
      #print "micro: $microtime<br>";
      return $microtime;
    }

    ############################################################
    function print_debug() {
        global $_SETUP;
        if($this->debug) {
        $start_time = "";

        if($this->e_count > 0 or $this->debug) {
            print "\n<br /><br /><br />\n";
            print "<div id=\"layout_debug\">\n";
            print "<table>\n";
            print "<tr><td colspan=\"5\"><div class=\"warning\"><b>System info</b></div></td>";
            print "<tr><th>Type</th><th>Duration</th><th>Trace</th><th colspan=\"2\">Description</th>\n";
            #print_r($this->debug_hash);
            foreach ($this->debug_hash as $line => $linenum) {
              if(!$start_time) {
                $start_time = $this->debug_hash[$line]['time'];
              }
              $time_since_start = $this->debug_hash[$line]['time'] - $start_time;
              $time_since_start = number_format($time_since_start,  3,',', '');
                print "<tr><td><b>" . $this->debug_hash[$line]['type'] . "</b></td><td><b>" . $time_since_start . "</b></td><td>" . $this->debug_hash[$line]['class'] . '->' . $this->debug_hash[$line]['function'].  "()</td><td>" . $this->debug_hash[$line]['file'] . ':' . $this->debug_hash[$line]['line'].  "</td></tr>\n";
                print "<td></td><td></td><td colspan=\"3\"><b>" . $this->debug_hash[$line]['text'] . "</b></td></tr>\n";
                print "<td></td><td></td><td colspan=\"3\">" . $this->debug_hash[$line]['trace'] . "</td></tr>\n";
                if($debug) {
                  print $this->debug_hash[$line]['function'];
                  #print_r($this->debug_hash[$line][backtrace]);
                 }
            }
            if($debug) {
              print "<tr><td>Debugs<td>$this->d_count</td></tr>";
            }
            print "<tr><td>Warnings<td>$this->w_count</td></tr>";
            print "<tr><td>Errors<td>$this->e_count</td></tr>";

            print "</table>";
            print "</div>";
            }
        } else {
            foreach ($this->debug_hash as $line => $linenum) {
                if(!$start_time) {
                    $start_time = $this->debug_hash[$line][time];
                }
                $time_since_start = $this->debug_hash[$line][time] - $start_time;
            }
        }
        return $time_since_start;
    }

    ############################################################
    function role_template() {
      #Check if user is allowed to view this template

    }

    ############################################################
    function set_setup() {
      #From setup table
      $dbh = $this->dbh;
      $query  = "select * from setup";
      return $this->db->get_row(array('query' => $query));
    }

    ############################################################
    function set_person() {
        #From person table
        $dbh          = $this->dbh;
        $query        = "select * from person where PersonID='$this->login_id'";
        $person       = $this->db->get_row(array('query' => $query));
  
        if($person->Debug) {
            $this->debug = true;
            ini_set('display_errors',1);
            error_reporting(E_ALL ^ E_NOTICE);
        }
  
        //print $query;
        return $person;
    }

    ############################################################
    function set_company() {
      #From Company table
      $dbh = $this->dbh;
      $query  = "select C.* from company as C, companypersonstruct as CS where CS.PersonID='$this->login_id' and CS.Active='1' and CS.CompanyID=C.CompanyID";
      #print "$query<br>";
      return $this->db->get_row(array('query' => $query));
    }

    ############################################################
    function set_companydef() {
      #From Company table
      $query  = "select * from company where CompanyID='$this->defcompany_id'";
      return $this->db->get_row(array('query' => $query));
      #return $this->companydef;
    }

    ############################################################
    function set_session($interface, $module, $_template, $formdate) {
      #From session information
      global $_SETUP, $_SERVER;
      $sid              = session_id();
      $date             = date("Y-m-d");
      $datetime         = date("Y-m-d H:m:s");
      $datefrom         = date ("Y-m-d", mktime ()-86400 );
      $dateto           = date ("Y-m-d", mktime ()+86400 );
      $datestartyear    = date ("Y", mktime ()) . '-01-01';
      $dateendyear      = date ("Y", mktime ()) . '-12-31';
      $periodstartyear  = date ("Y", mktime ()) . '-01';
      $periodendyear    = date ("Y", mktime ()) . '-12';

      if(!$formdate) {
        $formdate = $date;
      }

      $_mysession = array(
        'SID'               => $sid,
        'Date'              => $date,
        'Datetime'          => $datetime,
        'DateFrom'          => $datefrom,
        'DateTo'            => $dateto,
        'DateStartYear'     => $datestartyear,
        'DateEndYear'       => $dateendyear,
        'PeriodStartYear'   => $periodstartyear,
        'PeriodEndYear'     => $periodendyear,
        'SECURITY_IP_CHECK' => $_SETUP['SECURITY_IP_CHECK'],
        'SessionTimeout'    => $_SETUP['SECURITY_TIMEOUT'],
        'RemoteAddr'        => $_SERVER['REMOTE_ADDR'],
        'Template'          => $_template,
        'Module'            => $module,
        'Interface'         => $interface,
        'HttpReferer'       => $_SERVER['HTTP_REFERER'],
        'HttpUserAgent'     => $_SERVER['HTTP_USER_AGENT'],
        'RedirectUrl'       => $_SERVER['REDIRECT_URL'],
        'RequestURI'        => $_SERVER['REQUEST_URI'],
        'Dispatch'          => $_lib['sess']->dispatch,
        'LoginFormDate'     => $formdate,
      );

      return $_mysession;
    }

    ############################################################
    function set_glob() {
      global $_SETUP;
      #From global preferences file and other globals

      $glob = array(
        "HOME_DIR"              => $_SETUP['HOME_DIR'],
        "DOWNLOAD_DIR"          => $_SETUP['DOWNLOAD_DIR'],
        "XML_VERSION"           => $_SETUP['XML_VERSION'],
        "CSS"                   => $_SETUP['CSS'],
        "DISPATCH"              => $_lib['sess']->dispatch,
        "DEBUG"                 => $_SETUP['DEBUG'],
        "TEMPLATE_DIR"          => $_SETUP['TEMPLATE_DIR'],
        "FILE_MAX_HEIGHT"       => $_SETUP['FILE_MAX_HEIGHT'],
        "FILE_MAX_WIDTH"        => $_SETUP['FILE_MAX_WIDTH'],
        "SERVER_ADMIN"          => $_SETUP['SERVER_ADMIN'],
        "font_width"            => $font_width,
        "form_heading_vert"     => $form_heading_vert,
        "form_ingress_vert"     => $form_ingress_vert,
        "form_description_vert" => $form_description_vert,
        "form_width"            => $form_width,
        "table_width"           => $table_width,
        "frames"                => $frames
        );
      return $glob;
    }

    ############################################################
    function set_roletemplateaccess() {
      #From roletemplate tables
      $query  = "select A.Interface, A.Module, A.Template from roletemplateaccess as A, roleperson as P where P.PersonID='$this->login_id' and P.RoleID=A.RoleID";
      return $this->db->get_row(array('query' => $query));
    }

    ############################################################
    function set_tableaccess() {
      #if(strlen($this->login_id)>0)
      #{
          #How do we know that these are the best privileges? 0 privileges will not exist, but 1 in one role and 3 in another could give mixed results
          #RoleID = 0 is common rolegroup before login
          $query  = "select TableName, TableAccess from roletableaccess as a, roleperson as r where (r.PersonID='$this->login_id' and r.RoleID=a.RoleID) or r.RoleID=0";
          $result = $this->db->db_query($query);

          while($row = $this->db->db_fetch_object($result))
          {
            $table =  strtolower($row->TableName);
            if(!isset($this->tableaccess[$table]) || $this->tableaccess[$table] < $row->TableAccess)
            {
              $this->tableaccess[$table] = $row->TableAccess;
              //print "$table : $row->TableAccess<br>";
            }
          }

          #print_r($this->tableaccess);
          return $this->tableaccess;
      #}
    }

    ############################################################
    function get_tableaccess($table) {
      global $_SETUP;
      #0 = no access, 1 = read, 2=write, 3=delete
      //print "Sess: get_access: $table: " . $this->tableaccess[$table] . "<br>";
      //print_r($this->tableaccess);
      #return 3;
      if($_SETUP['SECURITY']['ROLE'] == true) {
        return $this->tableaccess[$table];
      } else {
        return 3;
      }
    }

    ############################################################
    function set_roletemplate() {
      #From roletemplate tables
      $dbh = $this->dbh;
      $query  = "select T.Interface, T.Module, T.Template from roletemplate as T where T.AccessLevel=0";
      return $this->db->get_row(array('query' => $query));
    }

    ############################################################
    function set_role() {
      #From role tables
      $role = Array();

      $query  = "select * from role as R, roleperson as P where P.PersonID='$this->login_id' and P.RoleID=R.RoleID";
      $result = $this->db->db_query($query);
      $i = 0;
      while($_row = $this->db->db_fetch_assoc($result)) {;
        $role[$i] = $_row;
        $i++;
      }
      return $role;
    }

    ############################################################
    function set_args() {
      #From URL/FORM. Safe input.
      #Not implemented yet
          foreach ($_POST as $postvarname => $rawpostcontent)
          {
            #array_push($_POST[$postvarname],
            #strip_tags(substr(trim($rawpostcontent),0,250)));
          }
    }


  function required($post, $required)
  {
    global $_lib;

    foreach($required as $field => $type)
    {
        if(!$post['$field'])
        {
            $this->error("Missing required field: $field of type: $type");
        }
        else
        {
            #Try to convert:
            if(method_exists($_convert, $type))
            {
                $hash = $_lib['convert']->{$type}(array('value'=>$post[$field]));
                $post[$field] = $hash['value'];
                $_error = $hash['error'];
                if($_error)
                { #Should we fill the global error hash like this: [args][field]
                    $_error[args][$field] = $_error;
                    $this->error($_error);
                }
            }
            else
            {
                $this->error("Convert type: $type does not exist for field: $field");
            }
        }
    }
    return $post;
  }

    /***************************************************************************
    * setHTTPHeader
    * @param $name, $value
    * @return true
    */
    public function setHTTPHeader($name, $value) {
        $this->headH[$name] = $value;
        return true;
    }

    /***************************************************************************
    * printHTTPHeader
    * @param
    * @return
    */
    public function printHTTPHeader() {

        foreach($this->headH as $name => $value) {
            header($name . ': ' . $value);
        }
        return true;
    }
}


function display_error($file, $line, $error){
?>
   <table>
    <tr>
     <th>Error
    <tr>
        <td>File
        <td><? print "$file"; ?>
    <tr>
        <td>Line
        <td><? print "$line"; ?>

    <tr>
        <td>Message
        <td><? print "$error"; ?>


   </table>
<?
}

?>
