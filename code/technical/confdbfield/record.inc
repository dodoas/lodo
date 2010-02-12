<?
# $Id: record.inc,v 1.55 2005/10/14 13:15:43 thomasek Exp $ ConfDBFields_record.php,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no/

includelogic('tablemetadata/tablemetadata');
$meta = new lodo_tablemetadata_tablemetadata();

#print_r($_REQUEST);

if($_lib['input']->getProperty('action_confdbfield_updateselected')) {
    $meta->updateselected($_REQUEST);
    #$meta->updateall();
    
}
elseif($_lib['input']->getProperty('action_confdbfield_newlang')) {
    $meta->newlang($_REQUEST);

}
elseif($_lib['input']->getProperty('action_confdbfieldlanguage_delete')) {
    $meta->delete($_REQUEST);

}
elseif($_lib['input']->getProperty('action_confdbfield_dbupdate')) {
    $args['db_name'] = $_SETUP['DB_NAME'][0];
    $meta->dbupdate($args);
}
elseif($_lib['input']->getProperty('action_confdbfield_recorddelete')) {
    $meta->recorddelete($_REQUEST);

}
elseif($_lib['input']->getProperty('action_confdbfield_fieldlanguageupdate')) {
    $meta->fieldlanguageupdate($_REQUEST);

}
elseif($_lib['input']->getProperty('action_confdbfield_fieldlanguagenew')) {
    $meta->fieldlanguagenew($_REQUEST);

}
elseif($_lib['input']->getProperty('action_confdbfield_delete')) {
    $meta->confdbfield_delete($_REQUEST);

} elseif($_lib['input']->getProperty('action_confdbfield_fieldlanguageupdate')) {
    $meta->fieldlanguageupdate($_REQUEST);

} elseif($_lib['input']->getProperty('action_confdbfield_fieldlanguagenew')) {
    $meta->fieldlanguagenew($_REQUEST);
    
}
?>