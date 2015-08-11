<?php
includelogic('fakturabank/fakturabank');
includelogic('orgnumberlookup/orgnumberlookup');
global $_lib;

$accountplanid = $_GET["accountplanid"];
$orgnumber = $_GET["orgnumber"];
$scheme_type  = $_GET["scheme_type"];
$scheme_value = $_GET["scheme_value"];
$not_noorgno  = $_GET["not_noorgno"];


$type = $_GET["type"];

if (!in_array($type, array('balance', 'result', 'customer', 'employee'))) {
    $type = 'supplier';    
}
// initial setup
$org = new lodo_orgnumberlookup_orgnumberlookup();
$fb = new lodo_fakturabank_fakturabank();
$TmpAccountPlanData = array();
$TmpAccountPlanData['AccountPlanType']   = 'supplier';
$TmpAccountPlanData['InsertedByPersonID']= $_lib['sess']->get_person('PersonID');
$TmpAccountPlanData['InsertedDateTime']  = $_lib['sess']->get_session('Datetime');
$TmpAccountPlanData['UpdatedByPersonID'] = $_lib['sess']->get_person('PersonID');
$TmpAccountPlanData['Active']            = 1;
$TmpAccountPlanData['debittext']         = 'Salg';
$TmpAccountPlanData['credittext']        = 'Betal';
$TmpAccountPlanData['DebitColor']        = 'debitblue';
$TmpAccountPlanData['CreditColor']       = 'creditred';

if ($not_noorgno == 1) { // scheme type is not NO:ORGNR
  $FakturabankScheme = $_lib['storage']->get_row(array('query' => "select FakturabankSchemeID from fakturabankscheme where SchemeType = '$scheme_type'"));
  $FakturabankSchemeID = $FakturabankScheme->FakturabankSchemeID;
  $org->getOrgNumberByScheme($scheme_value, $scheme_type);
  // the first available account plan id
  $starting_id = 100000001;
  if ($_lib['sess']->get_companydef('BaseAccountIDOnMotkonto')) $starting_id += $org->MotkontoResultat1 * 10000;
  $used_accounts_hash = $_lib['storage']->get_hash(array('key' => 'AccountPlanID', 'value' => 'AccountPlanID', 'query' => "select AccountPlanID from accountplan where AccountPlanType = 'supplier' and AccountPlanID >= $starting_id order by AccountPlanID"));
  for ($i = $starting_id; $i <= 999999999; $i++) {
    if (!isset($used_accounts_hash[$i])) break;
  }
  $AccountPlanID = $i;
  $TmpAccountPlanData['AccountPlanID']   = $i;
  $_lib['storage']->store_record(array('data' => $TmpAccountPlanData, 'table' => 'accountplan', 'action' => 'insert', 'debug' => false));
  $_lib['storage']->store_record(array('data' => array('AccountPlanID' => $AccountPlanID, 'FakturabankSchemeID' => $FakturabankSchemeID, 'SchemeValue' => $scheme_value), 'table' => 'accountplanscheme', 'action' => 'insert', 'debug' => false));
  $fb->update_accountplan_from_fakturabank($AccountPlanID);
?>
<html>
  <body>
    <form id="form" name="accountplan_search" action="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&view_mvalines=&view_linedetails=" method="post">
      <input type="hidden" name="accountplan.AccountPlanType" value="<?= $type ?>" />
      <input type="hidden" name="accountplan.AccountPlanID" value="<?= $AccountPlanID ?>" />
      <input type="hidden" name="JournalID" value="" />
    </form>
  </body>
  <script>
    var f = document.getElementById('form');
    f.submit();
  </script>
</html>
<?
}
else {
  $TmpAccountPlanData['AccountPlanID'] = $accountplanid;
  $TmpAccountPlanData['OrgNumber'] = $orgnumber;
  $_lib['storage']->store_record(array('data' => $TmpAccountPlanData, 'table' => 'accountplan', 'action' => 'insert', 'debug' => false));
  $fb->update_accountplan_from_fakturabank($accountplanid);
?>
<html>
  <body>
    <form id="form" name="accountplan_search" action="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&view_mvalines=&view_linedetails=" method="post">
    <input type="hidden" name="accountplan.AccountPlanType" value="<?= $type ?>" />
      <input type="hidden" name="accountplan.AccountPlanID" value="<?= $accountplanid ?>" />
      <input type="hidden" name="OrgNumber" value="<?= $orgnumber ?>" />
      <input type="hidden" name="force_new" value="1" />
      <input type="hidden" name="JournalID" value="" />
      <input type="hidden" name="NewAccount" value="1" />
      <input type="submit" name="action_accountplan_new" value="Opprett konto" />
    </form>
  </body>
  <script>
    var f = document.getElementById('form');
    f.submit();
  </script>
</html>
<? } ?>
