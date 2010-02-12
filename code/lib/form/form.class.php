<?
class framework_lib_inline
{
  public $_confcache  = array(); #Should be global
  public $_row        = "";  #The row we are at now
  public $_result     = "";
  public $_dbh        = "";
  public $_dsn        = "";
  public $_table      = "";
  public $_maxwidth   = "";
  public $_pk         = ""; #Primary key of this table
  public $_action     = "";
  public $_record     = "multi";
  public $_form_name  = "";
  public $_type       = ""; #Default static form
  public $exist       = false; #Om det finnes en eller flere records koblet til sp¿rringen
  public $numrows     = -1; #Antall records returnert i sp¿rringen
  public $_fot        = false; #Show fields defined outside table, but they will display without formating and form handling
  public $class       = 'BGColorLight';
  public $_i          = 0;
  public $_static     = false;

  #query, table (we should omit table, and fint it out automatically from the query
  #Cache shoulkd even be more global thaan inside the object, all objects conf in the entire system

  function autoform($args)
  {
  	return $this->framework_lib_inline($args);
  }

  function framework_lib_inline($args)
  {
    global $_lib;
    #Rolecontrol check of template
    if($_lib['sess']->accesslevel > 1) {
      $this->_action   = $args['_action']; #The use can set the action themselves
      $_lib['sess']->debug("setting form action to: $this->_action");
    } else {
      #$_lib['sess']->debug("setting form action to blank - you dont have role access to edit this template");
    }
    $this->_dbh       = $args['_dbh'];
    $this->_dsn       = $args['_dsn'];
    $this->_table     = $args['table'];
    $this->_form_name = $args['form_name'];
    $this->_maxwidth  = $args['_maxwidth'];
    $this->_action    = $args['action'];
    $this->_type      = $args['type'];
    if(isset($args['fot'])){
      $this->_fot       = $args['fot'];
    }
    if(isset($args['record'])) {
      $this->_record = $args['record'];
    }
    if(isset($args['static'])) {
      $this->_static = $args['static'];
      #$this->_type   = $args['static'];
    }

    if($this->_type != 'static')
    {
        #print "form1<br>";
        #We allow initialising a form without data from the db
        #Should check if this is cached from before
        $query = "select F.TableName, F.TableField, F.OutputValidation, F.FormType, F.FormTypeEdit, F.Required, F.FormHeight, F.FormWidth, F.FieldExtra, F.FieldExtraEdit, F.DefaultValue, F.PrimaryKey, L.Alias as LanguageAlias, L.Description as LanguageDescription from confdbfields as F left join confdbfieldlanguage as L on F.TableName=L.TableName and F.TableField=L.TableField and L.LanguageID='" . $_lib['sess']->language . "' where F.TableName = '" . $args['table'] . "' and F.Active=1";
        #print "$query<br />";
        $this->_confcache[$this->_table] = $this->_dbh[$this->_dsn]->get_hashhash(array('query' => $query, 'key' => 'TableField'));
        #print "$query<br>";
        #$_cache->table(array('table' => $args['table'], 'field' => $field));


        #print_r($this->_confcache[$this->_table]);
        foreach ($this->_confcache[$this->_table] as $field => $tmp)
        {
          #print "PK: $this->_table.$field : $this->_pk # ";
          #print_r($this->_confcache[$this->_table][$field]['PrimaryKey']);
          #print "<br>";
          if($this->_confcache[$this->_table][$field]['PrimaryKey'] == 1)
          {
            $this->_pk = $field;
            #print "Found pk: $this->_table.$field: $this->_pk<br>";
            break; #Only supports one pk!!!
          }
        }
        #print "form2<br>";
        #Get data
        if(strlen($args['query']) > 0)
            $this->_result = $this->_dbh[$this->_dsn]->db_query($args['query']);
        elseif(strlen($args['query2']) > 0)
            $this->_result = $this->_dbh[$this->_dsn]->db_query2(array('query'=>$args['query2'], 'values'=>$args['values']));

        $this->_row    = $this->_dbh[$this->_dsn]->db_fetch_object($this->_result); #Init the first result imideately
        $this->numrows = $this->_dbh[$this->_dsn]->db_numrows($this->_result);
        #print "numrows: $this->numrows<br>\n";
        if($this->numrows > 0) {
          $this->exist = true;
        }
    } else {
      #print "Static form init<br>";
    }
  }

  function next_row() {
    $this->_row = $this->_dbh[$this->_dsn]->db_fetch_object($this->_result);
    $this->_i++;
    if (!($i % 2)) { $this->class = "BGColorLight"; } else { $this->class = "BGColorDark"; };

    if($this->_row){
      return  true;
    } else {
      return false;
    }
  }

  #Should also take roles into account: read (only show)
  #save input field (ans save button)
  #delete - input field (and delete button)
  #Enable inline editing if privileges and correct parameters are input (for front interfaces)

    function show($args)
    {
        global $_lib, $_SETUP;
        //print_r($args);
        if($this->numrows <= 0)
        {
            return "No rows available<br />";
        }

        if(isset($args['table']))
        {
            $table = $args['table'];
        }
        else
        {
            $table = $this->_table;
        }

        if($this->_fot and !$this->_confcache[$table][$args['field']])
        {
            #Fields outside table, return only value
            return $this->_row->{$args['field']};
        }
        elseif(!$this->_confcache[$table][$args['field']])
        {
            $_lib['sess']->warning("Field does not exist in database model: $this->_table.$args[field]");
            return  "Field does not exist in database model: $this->_table.$args[field]";
            #Return a joined value outside the scope
        }

        $setup['validation'] = $this->_confcache[$table][$args['field']]['OutputValidation'];

        if(isset($args['form']))
        {
            $formtype = $args['form'];
        }
        else
        {
            $formtype = $this->_confcache[$table][$args['field']]['FormType'];
        }

        $setup['required']  = $this->_confcache[$table][$args['field']]['Required'];

        if(isset($args['height']))
        {
            #Allow override of height. Prefered changed in confdbfields
            $setup['height'] = $args['height'];
        }
        else
        {
            $setup['height']  = $this->_confcache[$table][$args['field']]['FormHeight'];# 0 = dynamic
        }

        if($args['width'])
        {
            #Allow override of width. Prefered changed in conf db fields
            $setup['width'] = $args['width'];
        }
        else
        {
            $setup['width']   = $this->_confcache[$table][$args['field']]['FormWidth'];
        }

        if(isset($args['maxwidth']))
        {
            $setup['maxwidth'] = $args['maxwidth'];
        }
        else
        {
            $setup['maxwidth'] = $this->_maxwidth;
        }

        if(isset($args['class']))
        {
            $setup['class'] = $args['class'];
        }

        $setup['extra']         = $this->_confcache[$table][$args['field']]['FieldExtra'];
        $setup['extraEdit']     = $this->_confcache[$table][$args['field']]['FieldExtraEdit'];
        $setup['table']         = $this->_table;
        $setup['field']         = $args['field'];
        $setup['form_name']     = $this->_form_name;
        $setup['action']        = $this->_action;
        $setup['showOnlyImage'] = $args['showOnlyImage'];
        $setup['notWysiwyg']    = $args['notWysiwyg'];
        $setup['title']         = $this->_confcache[$this->_table][$args['field']]['LanguageDescription']; #Help description to form input

        if($_lib['sess']->debug)
        {
            print "felt: $setup[field]: $formtype, width: $setup[width], maxlength: $setup[maxwidth]<br>";
        }

        if($this->_record == 'multi')
        {
            #print "PK felt: $this->_pk felt: $this->_table.$setup[field], pkverdi: #" . $this->_row->{$this->_pk} . "#<br><br>";
            $setup['pk'] = $this->_row->{$this->_pk}; #PK???? how to get???
        }

        $setup['value']             = $this->_row->{$args['field']}; #???
        $setup['value_unformatted'] = $this->_row->{$args['field']};
        #print "VAL2: $setup[value]<br>";

        if(!isset($setup['value']) and isset($setup['required']))
        {
            $_lib['message']->add_field(array('table' => $this->_table, 'field' => $args['field'], 'pk' => $this->_row->{$this->_pk}, 'message' => "Required field"));
            $setup['value'] = $this->_confcache[$table][$args['field']]['DefaultValue'];
        }

        if(!isset($setup['value']))
        {
            $setup['value'] = $this->_confcache[$table][$args['field']]['DefaultValue'];
            if(!$setup['value'])
            {
                $setup['value'] = "";
            }
        }

        #added to be able to overide value from form.
        if(isset($args['value']))
        {
            $setup['value'] = $args['value'];
        }

        if($formtype == 'text' or $formtype == 'textarea' or $formtype == 'show' or $formtype == 'image' or $formtype == 'image2' or $formtype == 'file')
        {
            if($formtype == 'image2')
            {
                $answ = $_lib['media']->setMediaID(array('MediaID'=>$setup['value_unformatted']));
                if($answ['value'] == false)
                {
                    $retval = $_lib['media']->getMediaStorage(array());
                }
                elseif($answ['value'] == true)
                {
                    $retval = $_lib['media']->getMediaStorage(array('extraEdit'=>$setup['extraEdit'], 'Height'=>$args['height'], 'Width'=>$args['width'], 'Type'=>$args['type'], 'maxHeight'=>$args['maxHeight'], 'maxWidth'=>$args['maxWidth']));
                }
                $setup['mediaRow'] = $retval['value'];
                $msg = $retval['message'];
            }

            #We only need formatting in these two classes of fields - checkox, radiobutton, select does not need formating
            #print "$formtype: $args[field]: $format<br>";
            if(method_exists($_lib['format'], $setup['validation']) )
            {
                $hash           = $_lib['format']->{$setup['validation']}(array('value' => $setup['value'], 'table' => $this->_table, 'field' => $args['field'], 'row' => $this->_row, 'form_name'=>$this->_form_name, 'mediaRow'=>$setup['mediaRow']));
                $setup['value'] = $hash['value'];
                $error          = $hash['error'];

                #Format shoudl automayically add names to f.eks CompanyID and PersonID fields
                if($error)
                {
                    $_lib['error'] = "formatfeil ".$args['field']." = ".$setup['value'].": $error";
                    $_lib['message']->add_field(array('table' => $this->_table, 'field' => $args['field'],     'pk' => $this->_row->{$this->_pk}, 'message' => $_lib['error']));
                    #$_lib['sess']->warning($_lib['error']);
                    $setup['class'] = 'error';
                }
            }
            else
            {
                $_lib['sess']->warning("Method does not exist: format->".$setup['validation']."()");
            }
        }

        if(!$formtype)
        {
            $_lib['sess']->warning("OutputValidation not defined for field: ".$args['field']." in confdbfields, defaulter til text");
            $type = "text";
        }
        elseif(!method_exists($_lib['form3'], $formtype))
        {
            $_lib['sess']->warning("Metode finnes ikke: form::$formtype, defaulter til text for field: ".$args['field']);
            $formtype = "text";
        }
        else
        {
            $_lib['sess']->debug("Metode finnes: form::$formtype for field: ".$args['field']);
        }

        #print "Static: $this->_type == 'static'<br>";
        //print "Table access: $this->_table : " . $_lib['sess']->get_tableaccess($this->_table) . ";<br />";
        //print_r($_lib['sess']->tableaccess);

        ####################################################################
        #check template access
        $aclev = $_lib['sess']->check_roletemplate($_lib['sess']->get_session('Interface'), $_lib['sess']->get_session('Module'), $_lib['sess']->get_session('Template'));
        //print "aclev: $aclev";
        if(($aclev >= 1 and $_SETUP['SECURITY']['ROLE'] ) or !$_SETUP['SECURITY']['ROLE'] or $args[0] == 'lib' )
        {
            //print "ac: $table: " . $_lib['sess']->get_tableaccess($table) . "<br>\n";
            if($_lib['sess']->get_tableaccess($table) <= 0)
            {
                //print "Check access to table";
                return ""; #The user has no access to reading infor from this table
            }
            elseif(($this->_action == 'edit' and $this->_type != 'static' and $args['action'] != 'show' and !isset($args['static']) and $_lib['sess']->get_tableaccess($table) >= 2) or ($this->_action != 'edit' and $this->_type == 'static'))
            {
                #print "type: $formtype<br>";
                #The user has write/delete access
                if($formtype == 'select' && !$setup['extra'])
                {
                    $_lib['sess']->error("Missing extra (menu query) to field: " . $setup['field']);
                }

                #running the form3 method for this type
                $setup['row'] = $this->_row;
                //print "BBBBB: _form3->$formtype<br>";
                $element = $_lib['form3']->{$formtype}($setup);
                return $element;
            }
            else
            {
                //print "Ikke form<br>";
                return  $setup['value'];
            }
        }
    }

    function value($args)
    {
        return $this->_row->{$args['field']};
    }

  function format($args)
  {
    global $_lib;
    #print_r($args);
    
    $validation = $this->_confcache[$this->_table][$args['field']]['OutputValidation'];
    if ( method_exists($_lib['format'], $validation) )
    {
      $hash =  $_lib['format']->{$validation}(array('value' => $this->_row->{$args['field']}));
      #Format shoudl automayically add names to f.eks CompanyID and PersonID fields
      if($hash['error'])
      {
        $_lib['error'] = "formatfeil $args[field] = $value: $hash[error]";
        $_lib['message']->add_field(array('table' => $this->_table, 'field' => $args['field'], 'pk' => $this->_row->{$this->_pk}, 'message' => $_lib['error']));
        $_lib['sess']->warning($_lib['error']);
      }
    } else {
      #print "Format finnes ikke:  felt: " . $args['field'] . " . # validering: $validation#<br>\n";
      $hash['value'] = $this->_row->{$args['field']};
      #print_r($this->_row);
    }
    return $hash['value'];
  }

    function alias($args)
    {
        if($this->_confcache[$this->_table][$args['field']]['Required'])
        {
            $required = '<font color="#ff0000">*</font>';
        }
        if($this->_confcache[$this->_table][$args['field']]['LanguageAlias'])
        {
            $alias = $this->_confcache[$this->_table][$args['field']]['LanguageAlias'];
        }
        else
        {
            $alias =  $args['field'];
        }
        return "<label for=\".$this->_table." . $args['field']. "\" title=\"" . $this->_confcache[$this->_table][$args['field']]['LanguageDescription']  . "\">$alias $required</label>";
    }

  function show_help($args) {
    return $this->_confcache[$this->_table][$args['field']]['LanguageDescription'];
  }

  function has_error($args) {
    global $_lib;
    if($_lib['error'][$this->_table][$args['field']]) {
      return 1;
    } else {
      return 0;
    }
  }

  #returns error message for field
  function error($args) {
    global $_lib;
    return $_lib['message']->get_field(array('table' => $this->_table, 'field' => $args['field'],  'pk' => $this->_row->{$this->_pk}));
  }

    #Start form tag
    #action, name, if name not spesified, uses tablename as form_name
    function start($args)
    {
        if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static'))
        {
            if($this->_action == 'auto')
            {
                #Impossible to login and do static form if we override it
                $action = $_SERVER['REQUEST_URI']; #Override action, we find it ourselves
                $action = str_replace("action=edit", "", $action);
                $action = str_replace("noaction_", "action_", $action);
                $action = str_replace("action_",   "noaction_", $action);
            }
            else
            {
                $action = $args['action'];
            }

            //print "AA: $action<br>";
            $element = "<form action=\"$action\" ";

            if(isset($args['name']))
            {
                $this->_form_name = $args['name'];
            }
            if($this->_form_name)
            {
                $element .= "name=\"$this->_form_name\" ";
            }

            $element .= "method=\"$args[method]\" enctype=\"multipart/form-data\">\n";
            return $element;
        }
        elseif($this->_static)
        {
            //print "AAA: $args[action]<br>";
            $element .= "<form action=\"" . $args['action'] . "\" method=\"" . $args['method'] . "\" enctype=\"multipart/form-data\">\n";
            return $element;
        }
        else
        {
            return "";
        }
    }

  function stop($args) {
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return "</form>\n";
    } else {
      return "";
    }
  }

  function submit($args) {
    global $_lib;
    if($args['accesskey']) {
      $args['value'] .= " (" . $args['accesskey'] . ")";
    }
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->submit($args);
    } elseif($this->_static) {
      return $_lib['form3']->submit($args);
    } else {
      return "";
    }
  }

  #Only show this submit button in edit modus
  function submitonedit($args) {
    global $_lib;
    if($args['accesskey']) {
      $args['value'] .= " (" . $args['accesskey'] . ")";
    }
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->submit($args);
    } else {
      return "";
    }
  }

  function edit($args){
     global $_lib, $MY_SELF;
     if(2 > 1) {
     #if($_lib['sess']->accesslevel > 1) {
       $args['accesskey'] = 'E';
       $args['name']        = '';
       #print "URI: " . $_SERVER['REQUEST_URI'] . "<br>";
       if(strlen($_SERVER['REQUEST_URI']) == 1) {
         $url = $MY_SELF; #Only / will not work
       } else {
         $url = $_SERVER['REQUEST_URI'];
       }

       if($this->_action == 'edit'){
         $args['url']       = $url;
       } else {
         $args['url']       = $url . "&amp;action=edit";
       }
       return $_lib['form3']->URL($args);
     } else {
       return "";
     }
  }

    function URL($args)
    {
        global $_lib;
        if($this->_action == 'edit' and !$args['static'] and !$this->_static)
        {
            return $args['description'];
        }
        else
        {
            return $_lib['form3']->URL($args);
        }
    }

    function img($args)
    {
        global $_lib;
        if($this->_action == 'edit' and !$args['static'])
        {
            return $_lib['form3']->img($args);
        }
        else
        {
            $tmp = $_lib['form3']->img($args)."<br>\n".$_lib['form3']->file($args);
            return $tmp;
        }
    }

  function hidden($args) {
    global $_lib;
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->hidden($args);
    } elseif($this->_static) {
      return $_lib['form3']->hidden($args);
    } else {
      return "";
    }
  }

  function text($args) {
    global $_lib;
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->text($args);
    } else {
      return "";
    }
  }

  function password($args) {
    global $_lib;
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->password($args);
    } else {
      return "";
    }
  }

  function file($args)
  {
      global $_lib;
      if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static'))
      {
        return $_lib['form3']->file($args);
      }
      else
      {
        return '';
      }
  }

  function select($args)
  {
      global $_lib;
      if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static'))
      {
        return $_lib['form3']->_MakeSelect($args);
      }
      else
      {
        return '';
      }
  }

  function button($args){
    global $_lib;
    return $_lib['form3']->button($args);
  }

  function buttononedit($args){
    global $_lib;
    if(($this->_action == 'edit' and $this->_type != 'static') or ($this->_action != 'edit' and $this->_type == 'static')) {
      return $_lib['form3']->button($args);
    } else {
      return "";
    }
  }

  #Only show field in edit modus
  function show_edit($args) {

    if($this->_action == 'edit') {
      return $this->show($args);
    } else {
      return "";
    }

  }
}
?>
