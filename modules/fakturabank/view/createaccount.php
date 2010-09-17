<?php

$orgno = $_GET["orgno"];

?>
<html>
  <body> 
    <form id="form" name="accountplan_search" action="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro" method="post">
      <input type="hidden" name="accountplan_AccountPlanType" value="supplier"    />
      <input type="hidden" name="accountplan_AccountPlanID" value="<?= $orgno ?>" />
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
