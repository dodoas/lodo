<?
includelogic('zreport/zreportimport');

class zreport_view_xml
{
    function __construct($args)
    {
        global $_lib;
    }

    function Execute($args)
    {
        global $_lib;

        $zreport = new lodo_logic_zreportimport($_REQUEST);

        $zreportXML = $zreport->Execute(array());

        return $zreportXML;
    }
}

?>