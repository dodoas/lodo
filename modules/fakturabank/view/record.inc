<?
$fakturabank = new lodo_fakturabank_fakturabank();


if($_lib['input']->getProperty('action_fakturabank_registerincoming')) {
    #cleanup from prior developer -eirhje 23.01.10
    #print "Registrer innkommende<br>\n";
    $InvoicesO = $fakturabank->registerincoming();

} elseif($_lib['input']->getProperty('action_fakturabank_registeroutgoing')) {
    $InvoicesO = $fakturabank->registeroutgoing();

} elseif($_lib['input']->getProperty('action_fakturabank_addmissingaccountplan')) {
    $InvoicesO = $fakturabank->addmissingaccountplan();
}

?>
