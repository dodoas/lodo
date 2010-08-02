<?
class input
{
    public $_dataH   = array();
    public $_actionH = array();
    public $_hiddenH = array();
    public $_tables  = array();
    public $_pk      = array();
    public $request  = array();

    /***************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function
    */
    function getProperty($key) {
        if(isset($this->request[$key]))
            return strip_tags($this->request[$key]);
        else
            return false;
    }

    /***************************************************************************
    * One line description of function
    * @param Define input parameters
    * @return Define return og function
    */
    function setProperty($key, $value) {
        $_REQUEST[$key] = $value;
    }

    function __construct()
    {
        global $_REQUEST, $_SETUP, $_lib;

        #Add file hash to normal input hash to make input handling easier
        foreach($_FILES as $fieldname => $value)
        {
            $_REQUEST[$fieldname] = strip_tags($_FILES[$fieldname]['name']);
        }

        $table_pk = $_lib['db']->find_pk($_REQUEST);
        #print "Tabell pk<br>";
        #print_r($_REQUEST);

        foreach ($_REQUEST as $key => $value)
        {
            $key    = strip_tags($key);

            if (is_array($value)) {
                $arrval = array();

                foreach ($value as $kkey => $vval) {
                    $arrval[strip_tags($kkey)] = strip_tags($vval);
                }

                $value = $arrval;
            } else {
                $value  = strip_tags($value);
            }

            $this->request[$key] = $value;

			if($key == '__utma' || $key == '__utmc' || $key == '__utmz') {
				next;
			}

			#print "#$key#<br>";

            if(is_array($value))
            {
                $x = 0;
                while(list($key2, $value2) = each($value))
                {
                    $valinput .= $value2;
                    if($x<count($value)-1)
                    {
                        $valinput .= ",";$x++;
                    }
                }
                $key;
                $value  = $valinput;
                $x         = 0;
                $valinput  = "";
            }

            #Split key into: table, field, pk
            $server   = strip_tags($_SETUP['DB_SERVER'][0]);
            $database = strip_tags($_SESSION['DB_NAME']);

            #$elements = split("_", $key);
            $elements = explode("_", $key);
            #print_r($elements);

            $count    = count($elements);
            #print "$key: Antall: $count<br>\n";
            #print_r($elements);
            #print "PK: " . $elements[$count-1] . "<br>\n";
            if(is_numeric($elements[$count-1]))
            {
                #The last element is int
                #We know it is a primary key, and hance an update
                $pk_exists = true;
                #print "pk er der<br>\n";
            }

            ###############################
            if($elements[0] == 'action')
            {
                $this->_actionH[$elements[1]] = strip_tags($elements[2]);
            }
            elseif($elements[0] == 'noaction')
            {
                #Just ignore
                next;
            }
            elseif($count == 1)
            {
                #print "Hidden: $t.$key: $value<br>\n";
                #print "#$key# = $value<br>\n";
                $this->_hiddenH[$key] = $value;
                if($table_pk[$_REQUEST['table']] == $key)
                {
                    $pk = $value;
                    #print "Fant prim¾rn¿kkelen: $pk<br>\n";
                }
            }
            elseif($count >= 1 or $count <= 4)
            {
                #print "Data: $key<br>\n";
                #This is probably only hidden fields
                #$_sess->error("Not correct name format for fieldname: $key");

                if($count == 1 and $pk_exists)
                {
                    $field      = $elements[0];
                    $pk         = $elements[1];
                }
                elseif($count == 1)
                {
                    #Most common multi save

                    $table      = $elements[0];
                    $field      = $elements[1];
                    $pk         = 0; #The same  as new
                }
                elseif($count == 2 and $pk_exists)
                {
                    $table    = $elements[0];
                    $field    = $elements[1];
                    $pk       = $elements[2];
                }
                elseif($count == 2)
                {
                    #Most common multi save
                    $table       = $elements[0];
                    $field       = $elements[1];
                }
                elseif($count == 3)
                {
                    $table    = $elements[0];
                    $field    = $elements[1];
                    $pk       = $elements[2];
                }
                elseif($count == 4 and $pk_exists)
                {
                    $server   = $elements[0];
                    $database = $elements[1];
                    $table    = $elements[2];
                    $field    = $elements[3];
                    $pk       = $elements[4];
                }
                elseif($count == 4)
                {
                    $server   = $elements[0];
                    $database = $elements[1];
                    $table    = $elements[2];
                    $field    = $elements[3];
                }

                ##################################### Verify
                #$_sess->Debug("input.class: $table.$field.$pk, form_name: $key $value<br>");
                $hash = $_lib['db']->verify_field(array('table' => $table, 'field' => $field, 'value' => $value, 'name' => $key, 'pk' => $pk));
                //print_r($hash);
                $value  = $hash['value'];
                $error  = $hash['error'];
                $efields = $hash['fields'];
                $delete = $hash['delete'];
                #print("input.class: $table.$field, form_name: $key $value, delete: $delete, error: $error<br>\n");

                #####################################
                if($error)
                {
                    $_lib['message']->add_field(array('table' => $table, 'field' => $field, 'message' => $error, 'pk' => $pk));
                }
                elseif(!$delete)
                {
                    #$table_pk[$table]; = pk field name
                    #print "_dataH[$server][$database][$table][$pk][$field]  = $value<br>\n";
                    $this->_tables[$table]   = true;
                    $this->_pk[$table]       = $pk;
                    $this->_dataH[$server][$database][$table][$pk][$field]  = $value;

                    if(count($efields) > 0)
                    {
                        foreach($efields as $efield => $evalue)
                        {
                            if($_cache->field_exist(array('table' => $table, 'field' => $field)))
                            {
                                #Add extra fields generated by convert to this field
                                $_sess->Debug("Extra field: [$server][$database][$table][$pk][$efield]  = $evalue<br>");
                                $this->_dataH[$server][$database][$table][$pk][$efield]  = $evalue;
                            }
                            else
                            {
                                $_sess->warning("Field does not exists. Syncronize db model. $table.$field");
                            }
                        }
                    }
                }
            }
        }
        #print_r($_FILES);
        #$_sess->warning("Auto input finished");
        #print_r($this->_dataH);
        return true;
    }

    function get_data()
    {
        return $this->_dataH;
    }

    function get_hidden()
    {
        return $this->_hiddenH;
    }

    function get_action()
    {
        return $this->_actionH;
    }

    #Return tables used in input
    function get_tables()
    {
        return $this->_tables;
    }

    #Warning only fot single record, multi table
    function get_pk($args)
    {
        return $this->_pk[$args['table']];
    }
}
