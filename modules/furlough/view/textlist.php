<?
require_once "record.inc";

$query_furloughtexts  = "select * from furloughtext order by FurloughTextID";
$result_furloughtexts = $_lib['db']->db_query($query_furloughtexts);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Permisjon og permittering tekst liste</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<table class="lodo_data">
<thead>
  <tr>
    <th colspan="2">Permisjon og permittering tekster:</th>
    <th></th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Tekst</td>
    <td class="menu"></td>
  </tr>
</thead>

<tbody>
<?
while($furloughtext = $_lib['db']->db_fetch_object($result_furloughtexts)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
    <td><? print $furloughtext->FurloughTextID; ?></td>
    <td><? print $furloughtext->Text; ?></td>
    <td>
      <form name="furloughtext_delete" action="<? print $_lib['sess']->dispatch ?>t=furlough.textlist" method="post">
        <input type="hidden" name="furloughtext.FurloughTextID" value="<? print $furloughtext->FurloughTextID; ?>">
        <? print $_lib['form3']->submit(array('name'=>'action_furloughtext_delete', 'value'=>'Slett')); ?>
      </form>
    </td>
  </tr>
<? } ?>
</tbody>
</table>
<form name="furloughtext_add" action="<? print $_lib['sess']->dispatch ?>t=furlough.textlist" method="post">
    <input type="text" name="furloughtext.Text" size="20">
    <? print $_lib['form3']->submit(array('name'=>'action_furloughtext_add', 'value'=>'Legg til')); ?>
</form>
</body>
</html>
