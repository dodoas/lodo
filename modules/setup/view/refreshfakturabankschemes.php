<?
  require_once "record.inc";
?>

<form action="<? print $_lib['sess']->dispatch ?>t=setup.refreshfakturabankschemes" method="post">
  Refresh FB schemes:<br>
  <input type="checkbox" name="with_details" value="1" <? print $_POST['with_details'] != null ? "checked" : ""; ?>> With details<br>
  <input type="submit" name="action_refresh_all_fakturabank_schemes" value="All databases">
</form>
