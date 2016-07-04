<?
require_once "record.inc";

print $_lib['sess']->doctype ?>

<head>
    <title>Kommune liste</title>
    <? includeinc('head') ?>
    <? includeinc('combobox') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<table class="lodo_data">
<thead>
  <tr>
    <th>Kommune:</th>
    <th colspan="11"></th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Postnummer</td>
    <td class="menu">Kommune</td>
    <td class="menu">Fylke</td>
    <td class="menu">Sone</td>
    <td class="menu">AGA prosent</td>
    <td class="menu">Bank kontonr</td>
    <td class="menu">Orgnummer</td>
    <td class="menu">Navn</td>
    <td class="menu">Organisjasjonsform</td>
    <td class="menu">Kommentar</td>
  </tr>
</thead>

<tbody>
<?
  $kommunes = $kommune->all_kommunes_in_db();
  $i = 0;
  foreach ($kommunes as $kommune_id => $kommune_object) {
    if (!($i++ % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
    <td><? print $kommune_object->KommuneID; ?></td>
    <td><? print $kommune_object->KommuneNumber; ?></td>
    <td><? print $kommune_object->KommuneName; ?></td>
    <td><? print $kommune_object->County; ?></td>
    <td><? print $kommune_object->Sone; ?></td>
    <td><? print $kommune_object->TaxPercent . "%"; ?></td>
    <td><? print $kommune_object->BankAccountNumber; ?></td>
    <td><? print $kommune_object->OrgNumber; ?></td>
    <td><? print $kommune_object->OrgName; ?></td>
    <td><? print $kommune_object->OrganisationForm; ?></td>
    <td><? print $kommune_object->Comments; ?></td>
  </tr>
<?
  }
?>
</tbody>
</table>
<form name="kommune_import" action="<? print $_lib['sess']->dispatch ?>t=kommune.list" method="post">
<?
  $kommune_list_from_csv_args = array( 'name'=>'kommune.Import',
                                       'width'=>175,
                                       'data'=>$kommune->kommune_data_for_select(),
                                       'tabindex'=>$tabindex++,
                                       'class' => 'combobox',
                                       'required' => false,
                                       'notChoosenText' => 'Velg kommune',
                                       'show_all'=>true
                                     );
  print $_lib['form3']->Generic_menu3($kommune_list_from_csv_args);
  print $_lib['form3']->submit(array('name'=>'action_kommune_import', 'value'=>'Importer', 'style'=>'margin-left:70px'));
?>
</form>
</body>
</html>
