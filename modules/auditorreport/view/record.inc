<?php
$auditor_report = null;

if($_REQUEST['action_auditorreport_update'])
{

    $tables = array();
    $tables['auditorreport'] = 'AuditorReportID';
    $tables['auditorreportline'] = 'AuditorReportLineID';
    //print_r($_POST);
    $_lib['db']->db_update_multi_table($_POST, $tables);
    #print "Lagre ferdig<br>";
}
if($_REQUEST['action_auditorreport_update'])
{

    $tables = array();
    $tables['auditorreport'] = 'AuditorReportID';
    $tables['auditorreportline'] = 'AuditorReportLineID';
    //print_r($_POST);
    $_lib['db']->db_update_multi_table($_POST, $tables);
    #print "Lagre ferdig<br>";
}
elseif($_REQUEST['action_auditorreport_new'])
{
    $auditor_report = lodo_auditorreport_auditorreport::action_auditorreport_new(array('PeriodYear'=>$_REQUEST['PeriodYear'], 'db_table'=>"auditorreport", 'db_table2'=>"auditorreportline"));
}
