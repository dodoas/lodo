<?
#Database classes
# $Id: db_mysql.inc,v 1.126 2005/10/14 13:31:07 thomasek Exp $ db_mysql.inc,v 1.1.1.1 2001/11/08 18:14:05 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no
#
#Database abstraction layer: MySQL
#By: Thomas Ekdahl 2000-12-01
#

class db_mysql {

  #global $DB_USER; # , $DB_PASSWORD, $DB_SERVER, $this->database;
  var $host          = "";
  var $database      = "";
  var $username      = "";
  var $password      = "";
  var $_sess         = "";
  var $_validatehash = array();

   function db_mysql($args) {
     $this->host     = $args['host'];
     $this->database = $args['database'];
     $this->username = $args['username'];
     $this->password = $args['password'];
     $this->_sess    = &$args['_sess'];

     #print "connected: $this->host:$this->database:$this->username:$this->password<br>\n";

     return $this->_db_pconnect();
   }

   #################################################################
   function _db_connect() {
       global $_sess;
       return mysql_connect($this->host, $this->username, $this->password) or $_sess->error("You are not authorized to login to this database");
   }

    #################################################################
    function _db_pconnect()
    {
        global $_sess;
        return mysql_pconnect($this->host, $this->username, $this->password) or $_sess->error("You are not authorized to login to this database");
    }

   #################################################################
   # $_lib['db']->db_query($sql);
   # Kun for Œ hente data
   function db_query($db_query) {
       global $_sess;
       #print "$db_query<br>\n";
       $result = mysql_db_query($this->database, $db_query) or $_sess->error("Dbname: $this->database, <br>\nBad query: " . mysql_error() . "<br />\ndb_query: $db_query");
       return $result;
   }

   #####################
   #Function top simulate bind values. Prefered query function of safety reasons. Trouble with ? in input though
   #ex:      $this->db_query2(array('query' => 'select ? from ? where field=?', 'values' => array('gert1','fort2','sakte3')));
    function db_query2($args)
    {
        global $_sess;

        $values = $args['values'];

        $rep = '(\?)';
        foreach($values as $i)
        {
            $i = $this->db_escape($i);
            $args['query'] = preg_replace($rep, $i, $args['query'], 1);
        }

        #print "!-- ".$args['query']."<br /> -->\n";
        $result = mysql_db_query($this->database, $args['query']) or $_sess->error("Dbname: $this->database, <br>\nBad query: " . mysql_error() . "<br />\ndb_query: $db_query");
        return $result;
    }

   #hasher er fine dyr
   function db_query3($args)
   {
      global $_sess;
      $db_query = $args['query'];

      if($args['do_not_die'] == 1)
      {
        $result = mysql_db_query($this->database, $db_query);
      }
      else
      {
        $result = mysql_db_query($this->database, $db_query) or $_sess->error("Dbname: $this->database, <br>\nBad query: " . mysql_error() . "<br />\ndb_query: $db_query");
      }

      return $result;
   }

   #################################################################
   function db_numrows($db_result) {
       global $_sess;
       return mysql_numrows($db_result);
   }

    #################################################################
    function db_NumFields($db_result)
    {
        global $_sess;
        return mysql_num_fields($db_result);
    }

   #################################################################
   function db_fetch_object($db_result) {
       global $_sess;
       $object = mysql_fetch_object($db_result);
       return $object;
   }

    #################################################################
    function db_fetch_array($db_result){
        global $_sess;
        $object = mysql_fetch_array($db_result);
        return $object;
    }

   #################################################################
   function db_insert_id() {
       global $_sess;
       /* This does not exist in ODBC */
       $insert_id = mysql_insert_id() or $_sess->error("Bad insert id: " . mysql_error() . "<br /");
       return $insert_id;
   }

   #################################################################
   function db_close($db_link) {
       return mysql_close($db_link) or die("Bad close: " . mysql_error() . "<br />");
   }

   #################################################################
   function db_result($db_result, $db_row, $db_mixed) {
       return mysql_result($db_result, $db_row, $db_mixed) or die("Bad result: " . mysql_error() . "<br />");
   }

   #################################################################
   function db_free_result($db_result) {
       return mysql_free_result($db_result) or die("Bad free result: " . mysql_error() . "<br />");
   }

   #################################################################
   # $_lib['db']->db_insert($sql);
   function db_insert($db_query) {
       global $_SETUP, $_sess;
       #print "$db_query<br>\n";
       if($_SETUP['DEBUG']) { $_sess->debug("DEBUG: $db_query<br>"); }
       $result = mysql_db_query($this->database, $db_query) or $_sess->error("Bad insert query:<br />\n " . mysql_error() . "<br />\n$db_query");
       return mysql_insert_id(); #new
       #return $result;
   }

   #################################################################
   #args[$query, insert_id=true/false, debug=true/false]
   function db_insert2($args) {
       global $_sess;
       if($args['debug']) { print "DEBUG: $db_query<br>"; }

       $result = mysql_db_query($this->database, $args['query']) or $_sess->error("Bad insert query:<br />\n ".mysql_error()."<br />\n$args[query]");
       if(isset($args['insert_id']))
       {
         return mysql_insert_id(); #new
       }
       else
       {
         return 1;
       }
       #return $result;
   }

   #################################################################
   # $_lib['db']->update($sql);
   function db_update($db_query) {
       global $_sess;
       if($_SETUP[DEBUG]) { print "DEBUG: $db_query<br>"; }
       #print "DEBUG: $db_query<br>\n";
       mysql_db_query($this->database, $db_query) or /*$_sess->error*/ die("Bad update query: " . mysql_error() . "<br />$db_query");
       return mysql_affected_rows();
   }

   #################################################################
   function db_affected_rows() {
       return mysql_affected_rows();
   }

   #################################################################
   function db_delete($db_query) {
       global $_sess;
       if($_SETUP[DEBUG]) { print "DEBUG: $db_query<br>"; }
       $result = mysql_db_query($this->database, $db_query) or $_sess->error("Bad delete query: " . mysql_error() . "<br />$db_query");
       return $result;
   }

   #################################################################
   function db_fetch_assoc($db_result) {
       return mysql_fetch_assoc($db_result);
   }

   #################################################################
   function db_escape($string) {
       return mysql_real_escape_string($string);
   }

   #################################################################
   # $primarykey['RequestID']                        = $args['RequestID'];
   #     
   # $input['request_FinishedByPersonID']  = $_lib['sess']->login_id;
   # $input['request_DateFinished']        = 'NOW()';
   # $input['request_Status']              = 5;
   # $_lib['storage']->db_update_hash($request, 'request', $pk);
   function db_update_hash($input, $table, $primarykey) {
     global $_error, $_sess, $_cache;
     #Shoudl also be able to find primary key data if empty
     if($_sess->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {
       $where = "where ";
       foreach ($primarykey as $key => $value) {
         #print "pk: $key = $value<br>";
         if($key and $value) {
           $where .= "$key = '" . $this->db_escape(trim($value)) . "' and ";
         } else {
           return -1;
         }
       }
       $where = substr($where, 0, -4);

       $fields = $this->_query_hash($input, $table);

       if(count($fields) > 0)
       {
         $query = "update $table set ";
         foreach ($fields as $field => $value) {
           #print "felt: $field = $value,";
           $query .= "$field = $value,";
         }
         $query = substr($query, 0, -1);
         #print "#$where#<br>\n";

         if(strlen($where) <= 0) {
           $_sess->Error("Malformed query missing where. Forgot where part?");
         } else {

           $query .=  " " . $where;

           if($input['debug']) {
             print "<br>$query<br>\n";
           }
           #print $query."<br>\n";
           #Should we add logging here?
           return $this->db_update($query);
           }
         } else {
          $_sess->debug("Empty update query set: $table");
         }
      } else {
          $_sess->warning("Your role is not permitted to update records in: $table");
      }
    }

   #################################################################    
   # $input['request_FinishedByPersonID']  = $_lib['sess']->login_id;
   # $input['request_DateFinished']        = 'NOW()';
   # $input['request_Status']              = 5;
   # $_lib['storage']->db_update_hash($request, 'request', $pk);
   function db_new_hash($input, $table) {
     global $_error, $_sess;

     if($_sess->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {

       $fields = $this->_query_hash($input, $table);
       #print_r($fields);
       $query = "insert into $table set ";
       foreach ($fields as $field => $value) {
         $query .= "$field = $value, ";
       }
       $query = substr($query, 0, -2);
       //print "$query<br>";
       $this->db_insert($query);
       return $this->db_insert_id();
     } else {
       $_sess->warning("Your role is not permitted to create records in: $table");
     }
    }


    #################################################################
    function db_delete_hash($table, $primarykey) {
      global $_sess;
      if($_sess->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {
       $where = "where ";
       foreach ($primarykey as $key => $value) {
         #print "pk: $key = $value<br>";
         if($key and $value) {
           $where .= "$key = '" . $this->db_escape(trim($value)) . "' and ";
         } else {
           return -1;
         }
       }
       $where = substr($where, 0, -4);

        $query = "delete from $table $where";
        #print "<br>action_$table" . "_delete, $query<br><br>";
        #Should we add logging here?
        $result = $this->db_delete($query);
        #print "Slettet $value fra $table<br>";
      } else {
          $_sess->warning("Your role is not permitted to create records in: $table");
      }
    }

    #################################################################
    #Split and validate input fields
    #This is the prefered save function, must have safe and validated input from $_input class
    function update($input) {
      global $_sess;

      #print_r($input);

      foreach($input as $server_name => $server_data) {
		#print "$server_name<br>";

        foreach($server_data as $database_name => $database_data) {
			#print "$database_name<br>";
          #print_r($input[$server][$database]);
          foreach($database_data as $table_name => $table_data) {
			#print "$table_name<br>";
			#print_r($table_data);
            #Check table access
            if($_sess->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {
				#print "access granted<br>";
              foreach($table_data as $pk_name => $pk_data) {
				#print "pk: $pk_name<br>";
				if($pk_name) {
					$query_set = "";
					$query_update = "update $table_name set";
		
					foreach($pk_data as $field => $value) {
					  #Should we check if it is funsctions here? no.
					  $query_set .= "$field = '$value',";
					}
					$query_set   = substr($query_set, 0, -1);
					$pk_field    = $this->find_table_pk($table_name);
					if($pk_field && $pk_name) {
					  $query_where = " where $pk_field=$pk_name";
					  #print "\n<br>$query_update $query_set $query_where<br>\n";
					  $this->db_update("$query_update $query_set $query_where");
					} else {
					  $_sess->warning("Missing where part name or value for query: $query_update $query_set where $pk_field=$pk_name");
					  print "Missing where part name or value for query: $query_update $query_set where $pk_field=$pk_name<br>";
					}
				  } else {
					#print "Missing pk name";
				  }
              } 
            } else {
              $_sess->warning("Access denied: $table_name");
              print "Access denied: $table_name<br>";
            }
          }
        }
      }
    }

    #################################################################
    #Split and validate input fields
    function _query_hash($input, $table) {
      global $_sess, $_cache, $_message;

      $fields = array();
      #print "Generate HASH: $table<br>\n";
       foreach ($input as $key => $value)
       {
          #print "new key: $key, value: $value<br>\n";

          #Add verification, transformation and role check to input data.
          #is it int
          #If amout, convert it to mysql float
          #What about embedded html?
          #Does the user have the role rights to save the data
          #If so we use it
          #Why is . translated to _?
          if(preg_match("/^$table\_/", $key))
          {
            $key = preg_replace("/$table\_/i", "", $key);

            if(is_array($value)) {
               $x = 0;
               while(list($key2, $value2) = each($value)) {
                   $valinput .= $value2;
                   if($x<count($value)-1) {
                       $valinput .= ",";$x++;
                   }
                }
               $fields[$key] = $valinput;
               $x         = 0;
               $valinput  = "";
            }
            else {
                $fields[$key] = $value;
            }
          }
          else
          {
             $not_used .= "$key, ";
          }
          #print "$key = $value<br>\n";
        }

         if(count($fields) < 0) {
           $_sess->warning("No fields present in: table: $table<br>");
           return 0;
         }
         //print_r($fields)."<br>\n";
         #print "FINISHED<br>\n";
         #print "new key: $key, value: $value<br>";
         #$fields2 = array();
         foreach($fields as $field => $value)
         {
           //print $field." - ".$value."<br>\n";
           if($_cache->field_exist(array('table' => $table, 'field' => $field)))
           {
             if(preg_match("/\(\)$/", $value) or preg_match("/\'\)$/", $value))
             {
               #If it contains parenthesis, its a function
               #print "ikke fnutt: $field<br>";
               $fields[$field] = $value;
             }
             else
             {
               //print "$field - $value<br>\n";
               #Encode single quotas for db input
               ##################################### Verify
               $hash   = $this->verify_field(array('table' => $table, 'field' => $field, 'value' => $value));

               #print "$table.$field = $value<br>";
               #print_r($hash);

               $value  = $hash['value'];
               $error  = $hash['error'];
               //$fields = $hash['fields']; #Trenger ikke, dette blir gjort i input nŒ.
               #####################################
               if($error)
               {
                 $_message->add_field(array('table' => $table, 'field' => $field, 'message' => $error));
               }
               //print "$field - $value<br>\n";
               //print "field: $field, val: $fields[$field]<br>\n";
               $fields[$field] = "'" . $this->db_escape($value) . "'";
               //print "field: $field, val: $fields[$field]<br>\n";
               //print_r($fields);
             }
           }
           else
           {
             if(!$table) {
               print "Tabell mangler til db_update/db_new: $table.$field<br />\n";
             }
             if(!$field) {
               print "Felt mangler til db_update/db_new: $table.$field<br />\n";
             }
             print "Finnes ikke: $table.$field<br />\n";
             unset($fields[$field]); #remove it
             $_sess->warning("FIeldname does not exist: $field");
           }
         } #End foreach

         if(count($fields) < 1)
         {
           $_sess->error("No field elements present<br>");
         }
       //print_r($fields);
       return $fields;
    }

    #################################################################
    #Multi record update function in same table: form name requirement: tablename_fieldname_pkvalue
    #$input     = hash with input (table.field)
    #$table      = hash with tables (table)
    #$databases  = hash with databases
    #primarykey = hash with primary keys. (table.primarykey)
    function db_update_multi_record($input, $table, $primarykey) {
      #print "Multi update<br>";
      if(!$table){
        print "Empty table in db_update_multi_record<br>";
      }

      foreach ($input as $key => $value) {
        #print "table: $table, $key = $value<br>";
        if(preg_match("/^$table\_/", $key)){

            #preg_match('{(\d+)}', $key, $m);
            preg_match('{.*_(.*)$}', $key, $m); #Find the pk value  (text or int)
            $pk_new = $m[1];
            $key = preg_replace("/\_$pk_new/i",  "", $key); #Remove primary key
            #print "$key = $value<br>";
            $newinput = array();
            $newinput[$key] = $value;

            #Now we will run an update pr field (should be optimized to record), but we will accept it for now (the commented out method would never update the last record in the form because of the foreasch exiting, so new_pk does not get a new id.
            #Should probably be a multidimensional array with input, table and pk refs
            #if(!$pk_old) { $pk_old = $pk_new; }
            #print "pk_old: $pk_old = pk_new: $pk_new<br>";
            #if($pk_new != $pk_old ) {
            #  print "update<br>";
            $pk[$primarykey] = $pk_new;
            if($key) {
              $this->db_update_hash($newinput, $table, $pk);
            }
            $pk_old = $pk_new;
            unset($newinput);
            #}

            #print "key: $key, pk: $m[1]<br>";
        }
      }
    }

    #################################################################
    #Multi record update function in same table: form name requirement: tablename_fieldname_pkvalue
    #$input     = hash with input (table.field)
    #$table      = hash with tables (table) as key and primary key as value $tables['tabellnavn'] = 'pk';
    #$databases  = hash with databases
    #primarykey = hash with primary keys. (table.primarykey)
    function db_update_multi_table($input, $tables) {

      foreach ($tables as $table => $primarykey) {
          #print "table: $table, primarykey = $primarykey<br>";

          foreach ($input as $key => $value) {
            #print "field = $key, value = $value<br>";
            if(preg_match("/^$table\_/", $key)){

                #preg_match('{(\d+)}', $key, $m);
                preg_match('{.*_(.*)$}', $key, $m); #Find the pk value  (text or int)
                $pk_new = $m[1];
                $key = preg_replace("/\_$pk_new/i",  "", $key); #Remove primary key
                #print "$key = $value<br>";
                $newinput = array();
                $newinput[$key] = $value;

                #Now we will run an update pr field (should be optimized to record), but we will accept it for now (the commented out method would never update the last record in the form because of the foreasch exiting, so new_pk does not get a new id.
                #Should probably be a multidimensional array with input, table and pk refs
                #if(!$pk_old) { $pk_old = $pk_new; }
                #print "pk_old: $pk_old = pk_new: $pk_new<br>";
                #if($pk_new != $pk_old ) {
                #  print "update<br>";
                $pk[$primarykey] = $pk_new;
                if($key) {
                  #print_r($newinput);
                  $this->db_update_hash($newinput, $table, $pk);
                }
                $pk_old = $pk_new;
                unset($newinput);
                unset($pk);
                #}

                #print "key: $key, pk: $m[1]<br>";
            }
          }
      }
    }

    #################################################################
    #Input $args['query'], 'key' (hash field) debug=true/false
    function get_hashhash($args) {
      global $_sess;
      #$_sess->debug($args[query]);
      #$_sess->debug("key: $args[key]");
      $hash = array();
      #print "query: $args[query]";
      $result = $this->db_query($args['query']);
      while($_row = $this->db_fetch_assoc($result)) {
        $key = $_row[$args['key']];
        #trim($key); #Mysql returns VoucherCashNumber with leading blank even if it does not exist.....
        $hash[$key] = $_row;
        #print "#$key#<br>\n";
      }
      return $hash;
    }

    #################################################################
    #Input $args['query'], 'key','value'  (hash field), debug=true/false
    #
    function get_hash($args) {
      global $_sess;
      if(!$args['key'] or !$args['value'])
      {
        $_sess->warning($args['key']." = ".$args['value'].": $error");
      }
      $_sess->debug($args['query']);
      $hash = array();
      $result = $this->db_query($args['query']);
      while($_row = $this->db_fetch_assoc($result)) {
        $hash[$_row[$args['key']]] = $_row[$args['value']];
      }
      return $hash;
    }

    #################################################################
    #Deprecated
    #args['query'], debug=true/false - returns object row directly: object->field notation for usage
    function get_row($args)
    {
        global $_sess;
        if(isset($args['debug']) && $args['debug'] == true) { $_sess->debug($args[query]); }
        $result = $this->db_query($args['query']);
        if(!$result)
        {
       $_sess->error("Empty result: " . $args[query]);
           //print "$args[query]<br>";
        }
        return $this->db_fetch_object($result);
    }

    #Prepared statement ready
    function get_row2($args)
    {
        global $_sess;
        if(isset($args['debug']) && $args['debug'] == true) { $_sess->debug($args[query]); }
	//print $args['query']."<br>";
        #print $args['query']."<br>";
        $result = $this->db_query2($args);
        if(!$result)
        {
            $_sess->error("Empty result: " . $args[query]);
            //print $args['query']."<br>";
        }
        return $this->db_fetch_object($result);
    }

    #################################################################
    #table, field, value
    function verify_field($args)
    {
        global $_sess, $_convert, $_cache, $_message, $_lib;
        //print_r($args);

        $field = $args['field'];
        #Kunne v¾rt enda bedre bruk av cache
        $this->_validatehash = $_cache->table(array('table' => $args['table'], 'field' => $field));
        //print_r($this->_validatehash);

        #if(isset($this->_validatehash[$args['field']]['InputValidation'])) {
        $type = $this->_validatehash[$field]['InputValidation'];
        //print $args['table']." $field : type: $type<br>\n";
        #$_sess->debug($args['field']]['InputValidation']);
        //print("valider: table: $args[table], field: $args[field] : $type: value: $args[value]<br>");
        if($type)
        {
            if($this->_validatehash[$field]['Required'] == 1 and strlen($args['value']) == 0) {
               #Check for required fields
               $_error .= "P&aring;krevet";
            }
            elseif (method_exists($_convert, $type))
            {
                $hash   = $_lib['convert']->{$type}($args);
                //print_r($hash);
                $value  = $hash['value'];
                //print "Value: $value<br>";
                $error  = $hash['error'];
                $fields = $hash['fields'];
                $delete = $hash['delete'];
                if($error)
                {
                    $_error .= "$args[field] = $args[value]: $error";
                    $_sess->warning($_error);
                }

                #print "valider: $table, $field required: " . $this->_validatehash[$field]['Required'] . "<br>";
            }
            else
            {
                $_sess->warning("Method does not exist: \$_lib['convert']->$type()");
            }
        }
        else
        {
            #print "field:#$args[field]#";
            #print_r($this->_validatehash[$args[field]]);
            $value = $args['value']; #JUst return what you get inside
            $_sess->warning("Input validation setup missing for " . $args['table'] . "." . $args['field'] . ". Type: $type. Syncronize db model.");
        }
        #} else {
        #  $_error = $args['field'] . "." . $args['value'] . " does not exist in conf db fields. Syncronize db model.";
        #  $_sess->warning($_error);
        #}
        return array('value' => $value, 'error' => $_error, 'fields' => $fields, 'delete'=>$delete);
    }

   #################################################################
   #Find all tables and pks with name from regular $_POST input
   function find_pk($post) {
     #Find tables
     $tables = array();
     #TODO: Should be role control on table finding here.
     foreach ($post as $name => $value) {

       if(!preg_match("/^action\_/", $name)){
         #print "findpk: $name => $value<br>";
         if(preg_match('{(.*)_(.*)_\d+$}', $name, $m)) {
           $tables[$m[1]]     = true;
         }
         elseif(preg_match('{(.*)_(.*)}', $name, $m)) {
           $tables[$m[1]]     = true;
         }
       }
     }
     #Find primary keys (Could in fact guess them from table names, but problem guessing upper camel cas
       foreach ($tables as $table => $tmp) {
         $tables[$table] = $this->find_table_pk($table);
       }
     return $tables;
   }

  #################################################################
  function find_table_pk($table) {
     $query = "select TableField from confdbfields where TableName = '$table' and PrimaryKey=1";
     $row = $this->get_row(array('query' => $query));
     return $row->TableField;
  }
} # end class
?>
