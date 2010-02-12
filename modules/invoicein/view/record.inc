<?
if($_lib['input']->getProperty('action_invoicein_journal')) {
    print "bilagsf¿r<br>";
    $invoicein->journal();

} elseif($_lib['input']->getProperty('action_invoicein_update') or $_lib['input']->getProperty('action_invoiceline_new')) {
    $invoicein->update($_POST);

} elseif($_lib['input']->getProperty('action_invoicein_linenew')) {
    $invoicein->update($_POST);
    $invoicein->linenew($_REQUEST);

} elseif($_lib['input']->getProperty('action_invoicein_linedelete')) {
    $invoicein->update($_POST);
    $invoicein->linedelete($_REQUEST);

} elseif($_lib['input']->getProperty('action_invoicein_remittance')) {

    includelogic('remittance/remittance');

    $rem = new logic_remittance_remittance($_POST);
    print $rem->fill();
    print $rem->pay();
}
?>
