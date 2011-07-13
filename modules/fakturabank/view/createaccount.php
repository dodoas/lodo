<?php

$accountplanid = $_GET["accountplanid"];

$type = $_GET["type"];

if (!in_array($type, array('balance', 'result', 'customer', 'employee'))) {
    $type = 'supplier';    
}


?>
<html>
  <body> 
    <form id="form" name="accountplan_search" action="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro" method="post">
      <input type="hidden" name="accountplan_AccountPlanType" value="<?= $type ?>"    />
      <input type="hidden" name="accountplan_AccountPlanID" value="<?= $accountplanid ?>" />
      <input type="hidden" name="JournalID" value="" />
      <input type="hidden" name="action_accountplan_new" value="" />
      <input type="submit" value="Fortsett" name="submit" />
    </form>
  </body>
  <script>
    var f = document.getElementById('form');
//    f.submit();
  </script>
</html>
