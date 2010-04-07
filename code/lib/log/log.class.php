<?
#only allow internal users, all else exits

function accessdeniedpreinit($_sess, $_template, $args){
   global $_dbh, $_dsn, $_lib;
   $fields['logaccessdenied_Template']   = $_sess['Template'];
   $fields['logaccessdenied_Module']     = $_sess['Module'];
   $fields['logaccessdenied_Interface']  = $_SESSION['interface'];
   $fields['logaccessdenied_Referer']    = $_SERVER['HTTP_REFERER'];
   $fields['logaccessdenied_UserAgent']  = $_SERVER['HTTP_USER_AGENT'];
   $fields['logaccessdenied_IPAdress']   = $_SERVER['REMOTE_ADDR'];
   $fields['logaccessdenied_SessionID']  = session_id();
   $fields['logaccessdenied_Message']    = $args['Message'];
   #print_r($fields);
   $_lib['db']->db_new_hash($fields, 'logaccessdenied');
}

class logg {
  public $path;
  public $module;
  public $template;

    function __construct($args) {
        global $_lib;
    
        #Init
        $this->path     = $args['path'];
        $this->module   = $args['module'];
        $this->template = $args['template'];
        #Sess not used yet
        #How to access $this->sess->get session
    }

    function usage($args) {
        global $_lib;
        #print "Test<br>";
        #$sess = $args{'sess'};
      
        #if($template->Log) {
              $fields['logusage_Template']   = $_lib['sess']->get_session('Template');
              $fields['logusage_Module']     = $_lib['sess']->get_session('Module');
              $fields['logusage_Interface']  = $_lib['sess']->get_session('Interface');
              $fields['logusage_IPAdress']   = $_lib['sess']->get_session('RemoteAddr');
              $fields['logusage_SessionID']  = $_lib['sess']->get_session('SID');
              $fields['logusage_PersonID']   = $_lib['sess']->get_person('PersonID');
              $fields['logusage_PkField']    = $args{'PkField'};
              $fields['logusage_PkValue']    = $args{'PkValue'};
    
              if($template->LogReferer) {
                    $fields['logusage_Referer'] = $_sess->get_session('HttpReferer');
              }
              if($template->LogUserAgent) {
                $fields['logusage_UserAgent']  = $_sess->get_session('HttpUserAgent');
              }
    
              $_lib['db']->db_new_hash($fields, 'logusage');
      #} else {
      #  print "<!-- Logging turned off -->";
      #}
    }

  function accessdenied($_sess, $_template, $args){
    global $_lib;

    $fields['logaccessdenied_Template']   = $_sess->get_session('Template');
    $fields['logaccessdenied_Module']     = $_sess->get_session('Module');
    $fields['logaccessdenied_Interface']  = $_sess->get_session('Interface');
    $fields['logaccessdenied_Referer']    = $_sess->get_session('HttpReferer');
    $fields['logaccessdenied_UserAgent']  = $_sess->get_session('HttpUserAgent');
    $fields['logaccessdenied_IPAdress']   = $_sess->get_session('RemoteAddr');
    $fields['logaccessdenied_SessionID']  = $_sess->get_session('SID');
    $fields['logaccessdenied_PersonID']   = $_sess->get_person('PersonID');
    $fields['logaccessdenied_Message']    = $args['Message'];
    $_lib['db']->db_new_hash($fields, 'logaccessdenied');
  }

  function search($_sess, $table, $search) {
    global $_lib;

    $fields['logsearch_TableName']  = $table;
    $fields['logsearch_SearchWord'] = $search;
    $fields['logsearch_SessionID']  = $_lib['sess']->get_session('SID');
    $fields['logsearch_PersonID']   = $_lib['sess']->get_person('PersonID');
    $_lib['db']->db_new_hash($fields, 'logsearch');
  }

  function pagenotfound($_sess) {
    global $_lib;

    $fields['logpagenotfound_URL']        = $_lib['sess']->get_session('RedirectUrl');
    $fields['logpagenotfound_SessionID']  = $_lib['sess']->get_session('SID');
    $fields['logpagenotfound_PersonID']   = $_lib['sess']->get_person('PersonID');
    $fields['logpagenotfound_Referer']    = $_lib['sess']->get_session('HttpReferer');
    $_lib['db']->db_new_hash($fields, 'logpagenotfound');
  }

  function slowpage($args){
    global $_lib;
    $fields['logaccessdenied_Template']   = $_lib['sess']->get_session('Template');
    $fields['logaccessdenied_Module']     = $_lib['sess']->get_session('Module');
    $fields['logaccessdenied_Interface']  = $_lib['sess']->get_session('Interface');
    # comment in duration when it has been added as a valid field
    # $fields['logaccessdenied_Duration']   = $args['duration'];
    $fields['logaccessdenied_SessionID']  = $_lib['sess']->get_session('SID');
    $fields['logaccessdenied_PersonID']   = $_lib['sess']->get_person('PersonID');
    $_lib['db']->db_new_hash($fields, 'logaccessdenied');
  }

  function file($message) {
    global $_lib, $_SETUP;
    $filename = $_SETUP['HOME_DIR'] . "/log/empatix.txt";

    #print "$filename<br>\n";
    // Let's make sure the file exists and is writable first.
    if (is_writable($filename)) {

      // In our example we're opening $filename in append mode.
      // The file pointer is at the bottom of the file hence
      // that's where $somecontent will go when we fwrite() it.
      if (!$handle = fopen($filename, 'a')) {
            print "Cannot open file ($filename)";
            exit;
      }

      #$content = $_sess->get_microtime() . " : " . $message . "\n";
      $content = date("Y-m-d H:i:s") . "::" . $_lib['sess']->get_session('HttpUserAgent') . '::' . $message . "\n";
      // Write $somecontent to our opened file.
      if (fwrite($handle, $content) === FALSE) {
          print "Cannot write to file ($filename)";
          exit;
      }
      fclose($handle);

    } else {
      print "The file $filename is not writable";
    }
  }
}
?>
