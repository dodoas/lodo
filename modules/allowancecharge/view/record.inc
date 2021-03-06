<?
includelogic('accounting/accounting');
$accounting = new accounting();

$AllowanceChargeID = $_lib['input']->getProperty('AllowanceChargeID');

if($_lib['input']->getProperty('action_charge_new') || $_lib['input']->getProperty('action_allowance_new'))
{
    $query="select AllowanceChargeID from allowancecharge where AllowanceChargeID='$_POST[allowancecharge_AllowanceChargeID]'";
    $result = $_lib['db']->db_query($query);

    if($_lib['db']->db_numrows($result) == 0)
    {
        $_post = $_POST;
        $_post['allowancecharge_ChargeIndicator'] = ($_lib['input']->getProperty('action_charge_new')) ? 1 : 0;
        $_post['allowancecharge_Active'] = '1';
        $AllowanceChargeID = $_lib['db']->db_new_hash($_post, $db_table);
    }
    else
    {
        $row = $_lib['db']->db_fetch_object($result);
        $AllowanceChargeID=$row->AllowanceChargeID;
    }
}
elseif($_lib['input']->getProperty('action_allowancecharge_update'))
{
  // Allow unsetting a Department
  if (($_POST['allowancecharge_DepartmentID_' . $AllowanceChargeID ]) === "unset") {
      $_POST['allowancecharge_DepartmentID_' . $AllowanceChargeID ] = DB_NULL_PLACEHOLDER;
  }
  // Allow unsetting a Project
  if (($_POST['allowancecharge_ProjectID_' . $AllowanceChargeID]) === "unset") {
      $_POST['allowancecharge_ProjectID_' . $AllowanceChargeID] = DB_NULL_PLACEHOLDER;
  }

  $tables['allowancecharge'] = 'AllowanceChargeID';

  $date = $_lib['sess']->get_session('LoginFormDate');
  $accountplan_query = "select * from accountplan where AccountPlanID = " . (int) $_POST['allowancecharge_OutAccountPlanID_' . $AllowanceChargeID];
  $accountplan_out = $_lib['storage']->get_row(array('query' => $accountplan_query));
  if (!is_null($accountplan_out) && (!$accountplan_out->EnableVAT || is_null($accountplan_out->VatID))) $accountplan_out->VatID = 10; // TODO: 10, 30 or 32? Fix when answered by Hildegunn
  $vat_query = "select Percent, VatID from vat where Type = 'sale' AND Active = 1 and VatID = " . (int) $accountplan_out->VatID . " and ValidFrom <= '$date' and ValidTo >= '$date'";
  $vat_out = $_lib['storage']->get_row(array('query' => $vat_query));

  if ($vat_out->VatID) {
    $_POST['allowancecharge_OutVatID_' . $AllowanceChargeID] = $vat_out->VatID;
    $_POST['allowancecharge_OutVatPercent_' . $AllowanceChargeID] = $vat_out->Percent;
  } else {
    unset($_POST['allowancecharge_OutAccountPlanID_' . $AllowanceChargeID]);
    $_lib['message']->add("Feil utg&aring;ende konto valg");
  }
  $_lib['db']->db_update_multi_table($_POST, $tables);
}
elseif ($_lib['input']->getProperty('action_allowancecharge_delete') && $AllowanceChargeID)
{
    $query_delete = "delete from allowancecharge where AllowanceChargeID='$AllowanceChargeID'";
    $result = $_lib['db']->db_delete($query_delete);
}
