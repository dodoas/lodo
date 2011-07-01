<?
# $Id: edit.php,v 1.12 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
//print_r($_POST);
$db_table = "kommune";
$KommuneID = $_REQUEST['KommuneID'];
require_once "record.inc";

/* S¿kestreng */
$query_vat  = "select * from $db_table limit 200";
$result_kommune = $_lib['db']->db_query($query_vat);

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - kommune</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.12 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<table>
 <form name="kommuner" action="<? print $_lib['sess']->dispatch ?>t=kommune.edit" method="post">
   <input type="submit" name="action_kommune_new" value="Ny Kommune" />
 </form>
  <tr class="result">
    <th>Postnummer</th>
    <th>Kommune</th>
    <th>Sone</th>
    <th></th>
  </tr>
<?
while($row = $_lib['db']->db_fetch_object($result_kommune))
{
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr>
  <form name="form_edit" action="<? print $_lib['sess']->dispatch ?>t=kommune.edit" method="post">
    <input type="hidden" name="KommuneID" value="<? print $row->KommuneID ?>">
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'KommuneNumber', 'value'=>$row->KommuneNumber, 'width'=>'5')) ?></td>
    <td><? print $_lib['form3']->text(array('table'=>$db_table, 'field'=>'KommuneName', 'value'=>$row->KommuneName)) ?></td>
    <td><? print $_lib['form3']->sone_menu(array('table'=>$db_table, 'field'=>'Sone', 'value'=>$row->Sone, 'required' => true)) ?></td>
    <td><input type="submit" name="action_kommune_update" value="Lagre" /></td>
  </form>
  </tr>
  <? } ?>
</table>
<a href="http://www.skatteetaten.no/no/Tabeller-og-satser/Arbeidsgiveravgift---soneinndeling/" target="_blank">Skattekommune oversikt</a>
<? includeinc('bottom') ?>
</body>
</html>
