<?
if($_lib['input']->getProperty('action_remittance_pay')) {
    $InvoicesO = $rem->pay(array());
    #$fakturabank->journal($InvoicesO, 'incoming');

}
?>