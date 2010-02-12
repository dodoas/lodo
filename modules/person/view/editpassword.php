<?
# $Id: editpassword.php,v 1.14 2005/10/28 17:59:40 thomasek Exp $ person_edit.php,v 1.13 2001/12/02 09:19:50 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 2001, thomas@ekdahl.no, http://www.ekdahl.no

$PersonID  = $_REQUEST['PersonID'];
assert(!is_int($PersonID)); #All main input should be int

$CompanyID = $_REQUEST['CompanyID'];

$passwordOld = $_POST['passwordOld'];
$newPassword = $_POST['newPassword'];

$db_table = "person";

if(!$PersonID){
  $PersonID = $login_id;
}

include  "record.inc";

$query = "select * from $db_table where PersonID='$PersonID'";
$result = $_lib['db']->db_query($query);
$person = $_lib['db']->db_fetch_object($result);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - Bytt passord</title>
    <meta name="cvs"                content="$Id: editpassword.php,v 1.14 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc ("head"); ?>
</head>

<body>
<? includeinc('top'); ?>

<table cellspacing="0">
<form name="person" action="<? print $_lib['sess']->dispatch ?>t=person.editpassword&PersonID=<? print $PersonID; ?>&CompanyID=<? print $CompanyID ?>" method="post">
<input type="hidden" name="PersonID" value="<? print $person->PersonID; ?>">
<thead>

</thead>
<? if($_lib['sess']->get_person('AccessLevel') == 4 or $_lib['sess']->login_id==$person->PersonID) { ?>
<tbody>
    <tr><td><br>
    <?
    if(isset($melding))
    {
        print "<tr><td><font color=\"red\">".$melding."</font><tr><td><br>";
    }
    ?>
    <tr>
      <td colspan="4">Skriv inn i alle felt <? print $person->FirstName; ?>
    <tr>
        <td  class="BGColorDark">Gammelt passord
        <td><input type="password" name="passwordOld" value="<? print $passwordOld; ?>" size="24" tabindex="28" maxlength="50">
    <tr>
        <td  class="BGColorDark">Nytt passord
        <td><input type="password" name="newPassword" value="<? print $newPassword; ?>" size="24" tabindex="28" maxlength="50">
    <tr>
        <td  class="BGColorDark">Gjenta nytt passord
        <td><input type="password" name="newPassword2" value="<? print $newPassword; ?>" size="24" tabindex="28" maxlength="50">
</tbody>

<tfoot>
    <tr>
      <td class="BGColorDark"><a href="<? print $_lib['sess']->dispatch ?>&t=person.edit&PersonID=<? print "$PersonID" ?>&CompanyID=<? print $CompanyID ?>">Tilbake</a>
      <td class="BGColorDark"><br />
      <td class="BGColorDark"><br />
      <td class="BGColorDark" align="right"><input type="submit" value="Lagre (S)" name="action_password_update" tabindex="31" accesskey="S">
</tfoot>
</table>
</form>
<? } else { ?>
  Du har ikke rettigheter til &aring; endre passord på denne brukeren.
<? } ?>
<? // includeinc( "bunn"); ?>
</body>
</html>