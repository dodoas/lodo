<?
/* $Id: edit.php,v 1.16 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
$db_table = "exchange";
require_once "record.inc";

#Input parameters should be validated - also against roles
$query   = "select * from $db_table";
$result_exchange  = $_lib['db']->db_query($query);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - hovedbok</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.16 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>
<? includeinc('top') ?>
<? includeinc('left') ?>
<table>
    <th align="right" colspan="4">
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <form name="exchange_new" action="<? print $_lib['sess']->dispatch ?>t=exchange.edit" method="post">
      <input type="text" value=""  name="exchange.Currency" />
      <input type="submit" name="action_exchange_new" value="Ny valuta" />
      </form>
    <? } ?>

  <tr class="result">
    <th>Valuta
    <th>Vekslingsrate
    <th>
    <th>
<?
while($exchange = $_lib['db']->db_fetch_object($result_exchange)) {
?>
  <tr>
<form name="<? print "$form_name"; ?>" action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="exchange_ExchangeID" value="<? print "$exchange->ExchangeID"; ?>">

    <td><input type="text" name="exchange.Currency" value="<? print $exchange->Currency  ?>" size="10" class="number">
    <td><input type="text" name="exchange.Amount"   value="<? print $_lib['format']->amount(array('value'=>$exchange->Amount, 'return'=>'value'))  ?>"    size="10" class="number">
    <td>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" value="Lagre" name="action_exchange_update">
    <? } ?>
    <td>
    <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" value="Slett" name="action_exchange_delete">
    <? } ?>
</form>
<? } ?>
</table>
<? includeinc('bottom') ?>
</body>
</html>
