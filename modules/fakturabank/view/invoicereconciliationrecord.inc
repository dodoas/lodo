<?
#print "record<br>";

error_log("modules/fakturabank/view/invoicereconciliationrecord.inc-" . __LINE__ . ":_POST:" . (is_array($_POST) || is_object($_POST) ? print_r($_POST, true) : $_POST . ". <br/>\n"));
$primarykey['FakturabankInvoiceReconciliationReasonID'] = $FakturabankInvoiceReconciliationReasonID;

if($_lib['input']->getProperty('action_fakturabankinvoicereconciliationreason_import')) {
    includelogic('fakturabank/invoicereconciliationreason');
	$fbreconcilationreason = new lodo_fakturabank_invoicereconciliationreason();
    $fbreconcilationreason->import_mappings();
} elseif($_lib['input']->getProperty('action_fakturabankinvoicereconciliationreason_update')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_update_hash($_POST, $db_table, $primarykey);
}
elseif($_lib['input']->getProperty('action_fakturabankinvoicereconciliationreason_new'))
{
	#print "record1<br>";
    $query="select FakturabankInvoiceReconciliationReasonID from $db_table where FakturabankInvoiceReconciliationReasonID='" . $_POST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'] . "'";
    $result = $_lib['db']->db_query($query);
    if($_lib['db']->db_numrows($result) == 0)
    {
    	if(!empty($_POST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'])) {
			$post = $_POST;
            $ledger_type = 'main';
            if (!empty($post['fakturabankinvoicereconciliationreason_LedgerType'])) {
                $ledger_type = $post['fakturabankinvoicereconciliationreason_LedgerType'];
            }
			$query = "insert into fakturabankinvoicereconciliationreason (FakturabankInvoiceReconciliationReasonID, AccountPlanID, FakturabankInvoiceReconciliationReasonCode, FakturabankInvoiceReconciliationReasonName, LedgerType) values ('".$post['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID']."', '".$post['fakturabankinvoicereconciliationreason_AccountPlanID'] . "', '" . $post['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonCode'] . "', '" . $post['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonName'] . "', '" . $ledger_type ."')";
			#print "$query<br>";
            error_log("modules/fakturabank/view/invoicereconciliationrecord.inc-" . __LINE__ . ":query:" . (is_array($query) || is_object($query) ? print_r($query, true) : $query . ". <br/>\n"));
			$_lib['db']->db_insert2(array('query'=> $query));
			$FakturabankInvoiceReconciliationReasonID = $_POST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'];
        } else {
            if (empty($_POST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'])) {
                $_lib['message']->add(array('message' => "Fakturabank avstemmingskode er ikke oppgitt"));
            }
        }
    }
    else
    {
    	$_lib['message']->add(array('message' => "Forbindelsen finnes allerede"));
        $FakturabankInvoiceReconciliationReasonID=$_POST['fakturabankinvoicereconciliationreason_FakturabankInvoiceReconciliationReasonID'];
    }
}
elseif($_lib['input']->getProperty('action_fakturabankinvoicereconciliationreason_delete')) {
  #Possible to extend or alter parameters here
  $_lib['db']->db_delete_hash($db_table, $primarykey);
}
?>
