<?
print $_lib['sess']->doctype ?>

<head>
    <title>Altinn logg</title>
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
    <th colspan="6">Altinn logg:</th>
  </tr>
  <tr>
    <td class="menu">ID</td>
    <td class="menu">Tidspunkt</td>
    <td class="menu">Type</td>
    <td class="menu">Beskrivelse</td>
    <td class="menu">Brukernavn</td>
    <td class="menu">Altinnreferanse</td>
  </tr>
</thead>

<tbody>
<?
  $log_entries_query = "select * from altinnlog order by TS desc";
  $log_entries = $_lib['db']->get_hashhash(array('query' => $log_entries_query, 'key'=>'AltinnLogID'));
  $i = 0;
  foreach ($log_entries as $log_entry) {
     if (!($i++ % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
    <td><? print $log_entry['AltinnLogID']; ?></td>
    <td><? print $log_entry['TS']; ?></td>
    <td><? print $log_entry['Type']; ?></td>
    <td><? print $log_entry['Message']; ?></td>
    <td><? print $_lib['format']->PersonIDToName($log_entry['PersonID']); ?></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.show4&AltinnReport4ID=<? print $log_entry['AltinnReference']; ?>"><? print $log_entry['AltinnReference']; ?></a></td>
  </tr>
<?
  }
?>
</tbody>
</table>
</body>
</html>
