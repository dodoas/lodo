<?
#FUnctions to get data correctly into the database

class message
{
  public $_message = "";
  public $_hash    = array();

  function __construct($args) {
     $this->dbserver = $args['dbserver'];
     $this->dbname   = $args['dbname'];
  }

  #array('message')
  function add($args) {
    if(!is_array($args)) { $args = array('message' => $args); }
  
  	if ($args['message'] != "")
    	$this->_message .= $args['message'] . "<br />\n";
  }

  #array('server', 'database', 'table', 'pk', 'field', 'message')
  function add_field($args) {
    $args = $this->_default($args);
    #print "set<br>";
    #print_r($agrs);
    $this->_hash[$args['server']][$args['database']][$args['table']][$args['pk']][$args['field']] .= $args['message'];
  }

  #array('server', 'database', 'table', 'pk', 'field',
  function get_field($args) {
    $args = $this->_default($args);
    #print "get<br>";
    #print_r($this->_hash[$args['server']][$args['database']][$args['table']]);
    return "<font color=\"#FF0000\">" . $this->_hash[$args['server']][$args['database']][$args['table']][$args['pk']][$args['field']] . "</font>";
  }

  #nothing
  function get() {
    return $this->_message;
  }

  function _default($args) {

    if(!isset($args['server'])) {
      $args['server'] = $this->dbserver;
    }
    if(!isset($args['database'])) {
      $args['database'] = $this->dbname;
    }
    return $args;
  }

    #This function should go out to a communications module
    function send_user($args)
    {
        if(!$args['from'])
        {
            $args['from'] = "drift@empatix.no";
        }
        //print_r($args);
        $args[headers] = "From: ".$args['from']."\r\nX-Mailer: Empatix\r\n".$args['headers'];

        mail($args['to'], $args['subject'], $args['message'], $args['headers']);
    }

    function send_generic($args)
    {
        $new = array();
        $not = array(
            'to'        => true,
            'from'      => true,
            'subject'   => true,
            'headers'   => true
        );

        foreach($args as $key => $value)
        {
            if($not[$key])
            {
                $new[$key] = $value;
            }
            else
            {
                $new['message'] .= $key . "\t=\t". $value . "\n";
            }
        }

        //print_r($new);

        $this->send_user($new);
    }
}
?>
