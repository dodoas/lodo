<?
class cache
{
  var $_query       = array();
  var $_table       = array(); # [host][database][table][field]
  var $_dbserver    = "localhost";
  var $_dbname      = "";

  #############################################################################################
  #cache
  function cache($args)
  {
    global $_SETUP;
    $this->dbname   = $_SETUP['DB_NAME']['0'];
    $this->dbserver = $_SETUP['DB_SERVER']['0'];
  }

  #############################################################################################
  #input array(server, database, table*, field*)
  function table($args)
  {
    $args = $this->_default($args);
    $this->_table_cache($args);

    return $this->_table[$args['server']][$args['database']][$args['table']];
  }

  #############################################################################################
  #input array(server, database, table*, field*)
  function field_exist($args)
  {
    $args = $this->_default($args);
    $this->_table_cache($args);

    if(isset($this->_table[$args['server']][$args['database']][$args['table']][$args['field']]))
    {
      return true; #This field exists in db model
    }
    else
    {
      return false;
    }
  }

  #############################################################################################
  #Default values
  function _default($args)
  {
    if(!isset($args['server']))
    {
      $args['server'] = $this->dbserver;
    }
    if(!isset($args['database']))
    {
      $args['database'] = $this->dbname;
    }

    return $args;
  }

  #############################################################################################
  #Get new cache content if not exists
  function _table_cache($args)
  {
    global $_lib, $_sess;

    if(!isset($this->_table[$args['server']][$args['database']][$args['table']]))
    {
      #print "Read table conf cache for the first time";
      #Hash cache from query, could also be persistent between sessions for greater speed. Shoudl also have table as a part of the key
      $query = "select * from confdbfields where TableName = '$args[table]' and Active=1";
      $this->_table[$args['server']][$args['database']][$args['table']] = $_lib['db']->get_hashhash(array('query' => $query, 'key' => 'TableField'));
      //print $query;
      return false;
    }
    else
    {
      return false;
    }
  }

  function query()
  {
    #Query cache
    return "";
  }
}
?>
