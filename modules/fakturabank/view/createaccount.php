<?php

$accountplanid = $_GET["accountplanid"];
$orgnumber = $_GET["orgnumber"];


$type = $_GET["type"];

if (!in_array($type, array('balance', 'result', 'customer', 'employee'))) {
    $type = 'supplier';    
}


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
//    f.submit();
  </script>
</html>
