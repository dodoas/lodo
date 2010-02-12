<?
/**
* Empatix functionality
*
* @package empatix_core1_lib
* @version  $args['NodeID']: publish.php,v 1.2 2005/08/01 11:41:29 thomasek Exp
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.ekdahl.no/ Thomas Ekdahl, 1994-2005, thomas@ekdahl.no
*/
class framework_lib_setup
{
    public $error                       = false;
    public $iteratorH                   = array() ;

    function __construct($args)
    {
		global $_lib;

		$sql = "select *, concat(Module, '.',Name) as lookup from setup;";
		$this->iteratorH = $_lib['storage']->get_hashhash(array('query' => $sql, 'key' => 'lookup'));
        #print_r($this->iteratorH);
	}

	function insert($args){
		global $_lib;

        $args['CreatedDateTime']    = 'NOW()';
        $args['CreatedByPersonID']  = $_lib['sess']->get_person('PersonID');

        #print_r($args);

        $_lib['storage']->store_record(array('data' => $args, 'table' => 'setup', 'debug' => false));
	}

    function get_value($key) {
        #print "Key: $key<br>\n";
        #print_r($this->iteratorH[$key]);
        return $this->iteratorH[$key]['Value'];
    }

	function delete($SetupID){
		global $_lib;

		$sql  = "delete from setup where SetupID = " . (int) $SetupID;
		$_lib['db']->db_delete($sql);
	}
}
?>