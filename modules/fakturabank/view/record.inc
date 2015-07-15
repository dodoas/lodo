<?
$fakturabank = new lodo_fakturabank_fakturabank();


if($_lib['input']->getProperty('action_fakturabank_registerincoming')) {
    #cleanup from prior developer -eirhje 23.01.10
    #print "Registrer innkommende<br>\n";
    $fakturabank->registerincoming();

} elseif($_lib['input']->getProperty('action_fakturabank_registeroutgoing')) {
    $fakturabank->registeroutgoing();

} elseif($_lib['input']->getProperty('action_fakturabank_addmissingaccountplan')) {
    $fakturabank->incomingaddmissingaccountplan();
} elseif(is_numeric($_lib['input']->getProperty('action_fakturabank_addsinglemissingaccountplan_id'))) {
    $fakturabank->addmissingaccountplan($_lib['input']->getProperty('action_fakturabank_addsinglemissingaccountplan_id'));
}

?>
