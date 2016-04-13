<?
class salary {
    var $ValidFrom = '';
    var $ValidTo   = '';
    var $PersonID  = 0;

    function salary($args) {
        $this->ValidFrom    = $args['ValidFrom'];
        $this->ValidTo      = $args['ValidTo'];
        $this->PersonID     = $args['PersonID'];
    }

}
?>
