<?
#Database classes
# $Id: db_mysqli.inc,v 1.10 2005/11/18 07:41:42 thomasek Exp $ db_mysql.inc,v 1.1.1.1 2001/11/08 18:14:05 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no
#
#Database abstraction layer: MySQL
#By: Thomas Ekdahl 2000-12-01
#

define('DB_NULL_PLACEHOLDER', 'NULLNULLNULLNULLNULLerTULLTULLTULL');

class db_mysql {

  #global $DB_USER; # , $DB_PASSWORD, $DB_SERVER, $this->database;
  private $host          = "";
  private $database      = "";
  private $username      = "";
  private $password      = "";
  private $_sess         = "";
  private $link          = 0;
  private $debug         = false;
  private $_validatehash = array();

   function db_mysql($args) {
     $this->host     = $args['host'];
     $this->database = $args['database'];
     $this->username = $args['username'];
     $this->password = $args['password'];
     $this->_sess    = &$args['_sess'];

     //print "connected: $this->host:$this->database:$this->username:$this->password<br>\n";

     return $this->_db_pconnect();
   }

   #################################################################
   function _db_connect() {
       global $_lib;
       $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database) or $_lib['sess']->error("You are not authorized to login to this database");
       return $this->link;
   }

    #################################################################
    function _db_pconnect()
    {
        global $_lib;
        $this->link = mysqli_connect($this->host, $this->username, $this->password, $this->database) or $_lib['sess']->error("You are not authorized to login to this database");
        return $this->link;
    }

   #################################################################
   function db_query($db_query) {
       global $_lib;;
       # if($this->debug) print "$db_query<br>\n";
       if(empty($db_query)) 
       {
           throw new Exception( sprintf("Empty query: `%s'\n", $db_query) );
       }
       $result = mysqli_query($this->link, $db_query) or $_lib['sess']->error("Dbname: $this->database, <br>\nBad query: " . mysqli_error($this->link) . "<br />\ndb_query: $db_query");
       return $result;
   }

   #####################
   #Function top simulate bind values. Prefered query function of safety reasons. Trouble with ? in input though
   #ex:      $this->db_query2(array('query' => 'select ? from ? where field=?', 'values' => array('gert1','fort2','sakte3')));
    function db_query2($args)
    {
        global $_lib;;

        $values = $args['values'];

        $rep = '(\?)';
        foreach($values as $i)
        {
            $i = $this->db_escape($i);
            $args['query'] = preg_replace($rep, $i, $args['query'], 1);
        }

        #print $args['query']."<br />\n";
        $result = mysqli_query($this->link, $args['query']) or $_lib['sess']->error("Dbname: $this->database, <br>\nBad query: " . mysqli_error($this->link) . "<br />\ndb_query: $db_query");
        return $result;
    }

   #hasher er fine dyr
   function db_query3($args)
   {
      global $_lib;;
      $db_query = $args['query'];

      if($args['do_not_die'] == 1)
      {
        $result = mysqli_query($this->link, $db_query);
      }
      else
      {
        $result = mysqli_query($this->link, $db_query) or $_lib['sess']->error("Dbname: $this->database, <br>\nBad query: " . mysqli_error($this->link) . "<br />\ndb_query: $db_query");
      }

      return $result;
   }

   #################################################################
   function db_numrows($db_result) {
       return mysqli_num_rows($db_result);
   }

    #################################################################
    function db_NumFields($db_result)
    {
        return mysqli_num_fields($db_result);
    }

   #################################################################
   function db_fetch_object($db_result) {
       return mysqli_fetch_object($db_result);
   }

    #################################################################
    function db_fetch_array($db_result){
        return mysqli_fetch_array($db_result);
    }

    function fetch_row($result){
        return $result->fetch_row();
    }
    
   #################################################################
   function db_insert_id() {
       /* This does not exist in ODBC */
        $insert_id = mysqli_insert_id($this->link) or $_lib['sess']->error("Bad insert id: " . mysqli_error($this->link) . "<br /");
        return $insert_id;
   }

   #################################################################
   function db_close($db_link) {
       return mysqli_close($this->link) or die("Bad close: " . mysqli_error() . "<br />");
   }

   #################################################################
   function db_result($db_result, $db_row, $db_mixed) {
       return mysqli_result($db_result, $db_row, $db_mixed) or die("Bad result: " . mysqli_error($this->link) . "<br />");
   }

   #################################################################
   function db_free_result($db_result) {
       return mysqli_free_result($db_result) or die("Bad free result: " . mysqli_error($this->link) . "<br />");
   }

   #################################################################
   function db_insert($db_query) {
       global $_SETUP, $_lib;
       if($this->debug) print "$db_query<br>\n";
       $result = mysqli_query($this->link, $db_query) or $_lib['sess']->error("Bad insert query:<br />\n " . mysqli_error($this->link) . "<br />\n$db_query");
       return mysqli_insert_id($this->link); #new
       #return $result;
   }

   #################################################################
   #args[$query, insert_id=true/false, debug=true/false]
   function db_insert2($args) {
       global $_lib;;
       if($this->debug) print $args['query'] . "<br>\n";

       $result = mysqli_query($this->link, $args['query']) or $_lib['sess']->error("Bad insert query:<br />\n ".mysqli_error($this->link)."<br />\n$args[query]");
       if(isset($args['insert_id']))
       {
         return mysqli_insert_id($this->link); #new
       }
       else
       {
         return 1;
       }
       #return $result;
   }

   #################################################################
   function db_update($db_query) {
       global $_lib;
       if($this->debug) print "$db_query<br>\n";
       mysqli_query($this->link, $db_query) or /*$_lib['sess']->error*/ die("Bad update query x: " . var_dump(debug_backtrace()) . '<br>\nbacktrace: <br>\n' . mysqli_error($this->link) . "<br />$db_query");
       return mysqli_affected_rows($this->link);
   }

   #################################################################
   function db_affected_rows() {
       return mysqli_affected_rows($this->link);
   }

   #################################################################
   function db_delete($db_query) {
       global $_lib;
       if($this->debug) print "$db_query<br>\n";
       return mysqli_query($this->link, $db_query) or $_lib['sess']->error("Bad delete query: " . mysqli_error($this->link) . "<br />$db_query");
   }

   #################################################################
   function db_fetch_assoc($db_result) {
       return mysqli_fetch_assoc($db_result);
   }

   #################################################################
   function db_escape($string) {
       return mysqli_real_escape_string($this->link, $string);
   }

   #################################################################
   function db_update_hash($input, $table, $primarykey) {
     global $_error, $_lib;

    #print_r($input);

     #Shoudl also be able to find primary key data if empty
     #print "db_update_hash" . print_r($primarykey) . print "<br />";
     if($_lib['sess']->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {
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
       #print "WHERE: $where<br>";

       $fields = $this->_query_hash($input, $table);

       #print "count" . count($fields) . "hh<br>";
       if(count($fields) > 0)
       {
         $query = "update $table set ";
         foreach ($fields as $field => $value) {
           #print "felt: $field = $value,<br>\n";
           $query .= "$field = $value,";
         }
         $query = substr($query, 0, -1);
         #print "#$where#<br>\n";

         if(strlen($where) <= 0) {
           $_lib['sess']->Error("Malformed query missing where. Forgot where part?");
         } else {

           $query .=  " " . $where;

           if($input['debug'] || $this->debug) {
             print "<br>$query<br>\n";
           }
           #print "update hash: $query"."<br>\n";
           #Should we add logging here?
           return $this->db_update($query);
           }
         } else {
          $_lib['sess']->debug("Empty update query set: $table");
         }
      } else {
          $_lib['sess']->warning("Your role is not permitted to update records in: $table");
      }
    }

   #################################################################
   function db_new_hash($input, $table) {
     global $_error, $_lib;

     if($_lib['sess']->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {

       $fields = $this->_query_hash($input, $table);
       #print_r($fields);
       $query = "insert into $table set ";
       foreach ($fields as $field => $value) {
         $query .= "$field = $value, ";
       }
       $query = substr($query, 0, -2);
       #print "$query<br>";
       $this->db_insert($query);
       #print "Det gikk bra";
       return $this->db_insert_id();
     } else {
       $_lib['sess']->warning("Your role is not permitted to create records in: $table");
     }
    }


    #################################################################
    function db_delete_hash($table, $primarykey) {
      global $_lib;;
      if($_lib['sess']->get_tableaccess($table) >= 2 or !$_SETUP['SECURITY']['ROLE']) {
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
          $_lib['sess']->warning("Your role is not permitted to create records in: $table");
      }
    }

    #################################################################
    #Split and validate input fields
    #This is the prefered save function, must have safe and validated input from $_input class
    function update($input) {
      global $_lib;;

      //print_r($input);

      foreach($input as $server_name => $server_data) {
        #print_r($server_name);

        foreach($server_data as $database_name => $database_data) {

          #print_r($database_name);
          foreach($database_data as $table_name => $table_data) {
            #print_r($table_name);
            
            #Check table access
            if($_lib['sess']->get_tableaccess($table_name) >= 2 or !$_SETUP['SECURITY']['ROLE']) {

              foreach($table_data as $pk_name => $pk_data) {
                #print_r($pk_name);

                $query_set = "";
                $query_update = "update $table_name set";

                foreach($pk_data as $field => $value) {
                    #print "$field = $value<br>\n";
                  #Should we check if it is funsctions here? no.
                    if ($value == DB_NULL_PLACEHOLDER) {
                        $query_set .= "$field = NULL,";
                    } else {
                        $query_set .= "$field = '$value',";
                    }
                }
                $query_set   = substr($query_set, 0, -1);
                $pk_field    = $this->find_table_pk($table_name);
                if($pk_field && $pk_name) {
                  $query_where = " where $pk_field=$pk_name";
                  #print "\n<br>$query_update $query_set $query_where<br>\n";
                  $this->db_update("$query_update $query_set $query_where");
                } else {
                    #print "WARNING:";
                    $_lib['sess']->debug("Missing where part name or value for query: $query_update $query_set where $pk_field=$pk_name");
                }
              }
            } else {
              #print "Ikke tilgang";
              $_lib['sess']->debug("Access denied: $table_name");
            }
            #print "Loop4<br>";
          }
          #print "Loop3<br>";
        }
        #print "Loop2<br>";
      }
      #print "Loop1<br>";
    }

    #################################################################
    #Split and validate input fields
    function _query_hash($input, $table) {
      global $_lib;

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
           $_lib['sess']->warning("No fields present in: table: $table<br>");
           return 0;
         }
         #print_r($fields)."<br>\n";
         #print "FINISHED<br>\n";
         #print "new key: $key, value: $value<br>";
         #$fields2 = array();
         foreach($fields as $field => $value)
         {
           #print "foreach: " . $field." - ".$value."<br>\n";
           if($_lib['cache']->field_exist(array('table' => $table, 'field' => $field)))
           {
             if(preg_match("/\(\)$/", $value) or preg_match("/\'\)$/", $value))
             {
               #If it contains parenthesis, its a function
               #print "ikke fnutt: $field<br>";
               $fields[$field] = $value;
             }
             else
             {
               #print "_query build: $field - $value<br>\n";
               #Encode single quotas for db input
               ##################################### Verify

               $hash   = $this->verify_field(array('table' => $table, 'field' => $field, 'value' => $value));

               #print "Verify field: $table.$field = $value<br>";
               #print_r($hash);

               $value  = $hash['value'];
               $error  = $hash['error'];
               //$fields = $hash['fields']; #Trenger ikke, dette blir gjort i input nŒ.
               #####################################
               if($error)
               {
                 $_lib['message']->add_field(array('table' => $table, 'field' => $field, 'message' => $error));
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
             $_lib['sess']->warning("FIeldname does not exist: $field");
           }
         } #End foreach

         if(count($fields) < 1)
         {
           $_lib['sess']->error("No field elements present<br>");
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
        # print "Empty table in db_update_multi_record<br>";
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
    function db_update_multi_table($input, $tables)
    {
        foreach ($tables as $table => $primarykey)
        {
            #print "table: $table, primarykey = $primarykey<br>";
            foreach ($input as $key => $value)
            {
                #print "Loop2start<br>";
                #print_r($input);
                #print "field = $key, value = $value<br>";
                if(preg_match("/^$table\_/", $key))
                {
                    #print "Loop3start<br>";
                    #preg_match('{(\d+)}', $key, $m);
                    //print "<br><b>orgkey: $key = value: $value</b><br>";
                    if(preg_match('{.*_.*_(.*)$}', $key, $m)) #Find the pk value  (text or int)
                    {
                        $pk_new = $m[1];
                        #if(!isint())
                        $key = preg_replace("/\_$pk_new/i",  "", $key); #Remove primary key
                        #print "<br><b>key: $key = value: $value</b><br>";
                        $newinput = array();
                        $newinput[$key] = $value;
                        #print "Hit<br>";
                        #Now we will run an update pr field (should be optimized to record), but we will accept it for now (the commented out method would never update the last record in the form because of the foreasch exiting, so new_pk does not get a new id.
                        #Should probably be a multidimensional array with input, table and pk refs
                        #if(!$pk_old) { $pk_old = $pk_new; }
                        #print "pk_old: $pk_old = pk_new: $pk_new<br>";
                        #if($pk_new != $pk_old )
                        #  print "update<br>";
                        $pk[$primarykey] = $pk_new;
                        if($key)
                        {
                            #print "<br>";
                            #print_r($newinput);
                            #print "<br>table: $table, pk: <br>";
                            #print_r($pk);
                            $this->db_update_hash($newinput, $table, $pk);
                        }
                        #print "Mit<br>";
                        $pk_old = $pk_new;
                        unset($newinput);
                        unset($pk);
                    }
                    else
                    {
                        print "Match bommet: $key<br>";
                    }
                    #print "key: $key, pk: $m[1]<br>";
                }
                #print "Loop3stop<br>";
            }
            #print "Loop2stop<br>";
        }
        #print "Loop1<br>";
    }

    #################################################################
    #Input $args['query'], 'key' (hash field) debug=true/false
    function get_hashhash($args) {
      global $_lib;;
      #$_lib['sess']->debug($args[query]);
      #$_lib['sess']->debug("key: $args[key]");
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
      global $_lib;;
      if(!$args['key'] or !$args['value'])
      {
        $_lib['sess']->warning($args['key']." = ".$args['value'].": $error");
      }
      $hash = array();
      $result = $this->db_query($args['query']);

	  if (!$result) {
		  return $hash;
	  }

      while($_row = $this->db_fetch_assoc($result)) {
        $hash[$_row[$args['key']]] = $_row[$args['value']];
      }
      return $hash;
    }

    function get_arrayrow($args)
    {
        global $_lib;

        if(isset($args['debug']) && $args['debug'] == true) {
            #print $args[query] . "<br>\n";
            $_lib['sess']->debug($args[query]);
        }

        $result = $_lib['db']->db_query($args['query']);
        if(!$result)
        {
          $_lib['sess']->error("storage->get_row: Empty result from: " . $args[query]);
        }

        $data = array();

        while($row = $_lib['db']->db_fetch_object($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    #input: query, key
    function get_hashrow($args) {
        global $_lib;

        $array = array();
        $result = $_lib['db']->db_query($args['query']);

		if (!$result) {
			return $hash;
		}

        while($_row = $_lib['db']->db_fetch_object($result)) {
            $hash[$_row->{$args['key']}] = $_row;
        }
        return $hash;
    }
    
    #################################################################
    #Deprecated
    #args['query'], debug=true/false - returns object row directly: object->field notation for usage
    function get_row($args)
    {
        global $_lib;;
        if(isset($args['debug']) && $args['debug'] == true) { $_lib['sess']->debug($args[query]); }
        $result = $this->db_query($args['query']);
        if(!$result)
        {
       $_lib['sess']->error("Empty result: " . $args[query]);
           //print "$args[query]<br>";
        }
        return $this->db_fetch_object($result);
    }

    #Prepared statement ready
    function get_row2($args)
    {
        global $_lib;
        if(isset($args['debug']) && $args['debug'] == true) { $_lib['sess']->debug($args[query]); }
        $result = $this->db_query2($args);
        if(!$result)
        {
            $_lib['sess']->error("Empty result: " . $args[query]);
            //print $args['query']."<br>";
        }
        return $this->db_fetch_object($result);
    }

    #################################################################
    #table, field, value
    function verify_field($args)
    {
        global $_lib;

        //print_r($args);

        $field = $args['field'];
        #Kunne v¾rt enda bedre bruk av cache

        $this->_validatehash = $_lib['cache']->table(array('table' => $args['table'], 'field' => $field));
        //print_r($this->_validatehash);

        #if(isset($this->_validatehash[$args['field']]['InputValidation'])) {
        $type = $this->_validatehash[$field]['InputValidation'];
        //print $args['table']." $field : type: $type<br>\n";
        #$_lib['sess']->debug($args['field']]['InputValidation']);
        #print("valider: table: $args[table], field: $args[field] : $type: value: $args[value]<br>\n");

        if($type)
        {
            if($this->_validatehash[$field]['Required'] == 1 and strlen($args['value']) == 0) {
               #Check for required fields
               $_error .= "P&aring;krevet";
            }
            elseif (method_exists($_lib['convert'], $type))
            {

                #print "convert->{$type}($args)<br>\n";
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
                    #$_lib['sess']->warning($_error); #B¿r kunne stŒ pŒslŒtt
                }

                #print "valider: $table, $field required: " . $this->_validatehash[$field]['Required'] . "<br>";
            }
            else
            {
                $_lib['sess']->warning("Method does not exist: \$_lib[\'convert\']->$type()");
                #print "<b>Konvert</b><br>";
                #print_r($_lib['convert']);
                #exit;
            }
        }
        else
        {
            #print "field:#$args[field]#";
            #print_r($this->_validatehash[$args[field]]);
            $value = $args['value']; #JUst return what you get inside
            #$_lib['sess']->warning("Input validation setup missing for " . $args['table'] . "." . $args['field'] . ". Type: $type. Syncronize db model.");
        }
        #} else {
        #  $_error = $args['field'] . "." . $args['value'] . " does not exist in conf db fields. Syncronize db model.";
        #  $_lib['sess']->warning($_error);
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
  
    /***************************************************************************
    * Preferred new function
    * Updates or stores a single record automatically, deletes a record with optinal parameter, or could be forced to only update or insert.
    * If no parameters are given a tabe description containing a PK will be updated and records not containing a pk will be inserted and pk will be set and returned.
    * @param array('data' => $fieldHash, 'table' => 'name', action=>'auto/insert/update/delete', debug=>true/false, verify (def:true)where = array('field' => 'operator')
    * @return PrimaryKey / status - what as done
    * ToDO: Bare returnere auto_id hvis tabellen har en autoincrement (det mŒ da gŒ an Œ finne ut)
    * St¿tte tabeller med mer en en prim¾rn¿kkel (foreach loop)
    * Hvis bare prim¾rn¿kkel er tilgjengelig og komplett - slette alle data.
    * Should be possible to define another where clause than the automatically deducted (for updating and deleting purposes. where =>
    */
    function store_record($args)
    {
        global $_lib;
        $action     = $args['action'];
        $foundpk    = false;
        $success    = true;

        #print "Her<br>";
        # if($args['debug']) print_r($args);

        if(isset($args['verify']))
        {
            $this->verify = $args['verify'];
        }
        else
        {
            $this->verify = true;
        }
        if(!isset($args['table'])) $_lib['sess']->warning('Table missing to store_record');
        $where         = array();

        $pkfield = $this->find_table_pk($args['table']); #Works because all PKs are single in Empatix
        if(isset($args['data'][$pkfield]) && strlen($args['data'][$pkfield]) > 0)
        {
            #Finn ut om denne tabellen har en prim¾rn¿kkel, hvis den har det sŒ sjekk om den er satt i input nŒ.
            #We found a set primary key, so we update the record
            $args['where'] = array($pkfield => '=');
        }

        #print_r($args['data']);
        #Generate where part if specified
        if(count($args['where'])) {
            $foundpk        = true;
            foreach($args['where'] as $field => $operator) {
                if(!$operator) $operator = '=';

                $query_where  .= $field . $operator . "'" . $args['data'][$field] . "' and ";
                $where[$pkfield]   = $args['data'][$pkfield]; #Fix? Single.
            }
            $query_where = substr($query_where, 0, -4);

            #Check if record exists in the database. If exists update, if not insert
            $query_exist    = "select $pkfield from " . $args['table'] . " where " . $query_where;
            #print "$query_exist<br>\n";
            $record_exist   = $_lib['storage']->get_row(array('query' => $query_exist));
            if($record_exist) {
                foreach($args['where'] as $field => $operator) {
                    #Only remove pk data if the record existed, else it has to be updated
                    unset($args['data'][$field]); #Do not update the fields used in the where part.
                }
            }
        }

        #Avgj¿r om vi skal lagre/slette eller oppdatere informasjonen
        if($action == 'delete')
            $action = 'delete';
        elseif($action == 'insert' || ($action='auto' && (!$foundpk || !$record_exist)))
            $action = 'insert';
        elseif(($action == 'update' || $action='auto') && $foundpk && $record_exist) {
            $action = 'update';
        }
        else {
            $_lib['sess']->warning("Unable to decide what to do to this data: $table, foundpk: $foundpk, record_exist: $record_exist");
            $success = false;
        }
;


        #Sjekk om brukeren har aksess til Œ oppdatere tabellen
        #FIX FIX IMPORTEANT SECURITY HOLE 1 == 1
        if($_lib['sess']->get_tableaccess($table) >= 2 || 1 == 1)
        {
            #Sjekk at det faktisk er oppgitt minst ett felt for oppdatering
            if(count($args['data']) > 0)
            {
                #Loop igjennom alle feltene
                foreach($args['data'] as $field => $value)
                {
                    #Sjekk om feltet eksisterer i databasen
                    if($_lib['cache']->field_exist(array('table' => $args['table'], 'field' => $field))){
                        #if($args['table'] == 'mediastorage') print "$field => $value\n";
                        #If it contains parenthesis, its a function (not good enough for safety? Needs prepared statements
                        if(preg_match("/\(\)$/", $value) or preg_match("/\'\)$/", $value)) {
                            #noop
                        }
                        else {
                            #Verifiser og konverter innholdet i feltene ihenhold til confdbfields
                            if($this->verify) {
                                $hash   = $this->verify_field(array('table' => $args['table'], 'field' => $field, 'value' => $value));
                                $value  = $hash['value'];
                                $error  = $hash['error'];
                                #if($args['table'] == 'mediastorage') print "Verify => $value : $error\n";

                                if($error)
                                {
                                    $_lib['message']->add_field(array('table' => $args['table'], 'field' => $field, 'message' => $error, 'pk' => $args['field'][$pkfield]));
                                    $success = false;
                                }
                            }
                            $field = $_lib['db']->db_escape(trim($field));
                            $value = "'" . $_lib['db']->db_escape(trim($value)) . "'";
                        }
                        $query_update .= $field . "=" . $value . ", ";
                    } else {
                        # print "Field does not exist: " . $args['table'] . ".$field";
                        $_lib['sess']->warning("Field does not exist: " . $args['table'] . ".$field");
                    }
                } #end foreach
                $query_update = substr($query_update, 0, -2);
                #print "<h2>" . $args['table'] . " $query_update</h2>";

                #Sjekk at det finnes data eller en where del av sp¿rringen
                if((strlen($query_update) || strlen($query_where)) &&  strlen($args['table'])) {
                    if($action == 'update') {
                        $query = "update " . $args['table'] . " set " . $query_update . " where " . $query_where;
                    } elseif($action == 'insert') {
                        $query = "insert into " . $args['table'] . " set " . $query_update;
                    } elseif($action == 'delete') {
                        $query = "delete from " . $args['table'] . " where " . $query_where;
                    }

                    #print "<h1>pkfield: $pkfield</h1><br>\n";
                    if($args['debug'] || $this->debug) print "$query\n";
                    #$_lib['sess']->debug("store_record($query)");
                    $result     = $_lib['db']->{'db_' . $action}($query);
                    if($action == 'insert' && !isset($args['data'][$pkfield])) { #Og det er en auotincrement i tabellen. Insert_id finnes i input, da kan vi ikke be om den etterpŒ
                        $success  = $_lib['db']->db_insert_id();
                        #print "Last insert id: $success\n";
                    }
                    elseif(isset($where[$pkfield])) {
                        #print "PK fantes<br>\n";
                        $success  = $where[$pkfield];
                    }
                    #print_r($where);

                } else {
                    $_lib['sess']->warning("update or table part of query is missing" . $args['table'] . ".$field");
                    $success = false;
                }

            } else {
                $_lib['sess']->warning("No table fields given in input for table: " . $args['table'] . ".$field");
                $success = false;
            }
        } else {
            $_lib['sess']->warning("Your role is not permitted to $action records in: $field");
            $success = false;
        }

        return $success;
    }
  
} # end class
?>
