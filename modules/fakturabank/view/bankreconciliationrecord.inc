<?
#print "record<br>";

$primarykey['FakturabankBankReconciliationReasonID'] = $FakturabankBankReconciliationReasonID;

if($_lib['input']->getProperty('action_fakturabankbankreconciliationreason_import')) {
    includelogic('fakturabank/bankreconciliationreason');
	$fbreconcilationreason = new lodo_fakturabank_bankreconciliationreason();
    $fbreconcilationreason->import_mappings();
} elseif($_lib['input']->getProperty('action_fakturabankbankreconciliationreason_update')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_fakturabankbankreconciliationreason_new'))
{
	#print "record1<br>";
    $query="select FakturabankBankReconciliationReasonID from $db_table where FakturabankBankReconciliationReasonID='" . $_POST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'] . "'";
    $result = $_lib['db']->db_query($query);
    if($_lib['db']->db_numrows($result) == 0)
    {
    	if (!empty($_POST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'])) {
			$post = $_POST;
            $ledger_type = 'main';
            if (!empty($post['fakturabankbankreconciliationreason_LedgerType'])) {
                $ledger_type = $post['fakturabankbankreconciliationreason_LedgerType'];
            }

			$query = "insert into fakturabankbankreconciliationreason (FakturabankBankReconciliationReasonID, AccountPlanID, FakturabankBankReconciliationReasonCode, FakturabankBankReconciliationReasonName, LedgerType) values ('".$post['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID']."', '".$post['fakturabankbankreconciliationreason_AccountPlanID'] . "', '" . $post['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonCode'] . "', '" . $post['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonName'] . "', '" . $ledger_type ."')";
			#print "$query<br>";
			$_lib['db']->db_insert2(array('query'=> $query));
			$FakturabankBankReconciliationReasonID = $_POST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'];
        } else {
            if (empty($_POST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'])) {
                $_lib['message']->add(array('message' => "Fakturabank avstemmingskode er ikke oppgitt"));
            }
        }
    }
    else
    {
    	$_lib['message']->add(array('message' => "Forbindelsen finnes allerede"));
        $FakturabankBankReconciliationReasonID=$_POST['fakturabankbankreconciliationreason_FakturabankBankReconciliationReasonID'];
    }
}
elseif($_lib['input']->getProperty('action_fakturabankbankreconciliationreason_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>
