<?php

//
// Konfigurasjonssystemet fungerer slik:
//
// tabeller:
//  salaryperiodconf inneholder periodene og verdier som følger med de.
//  salaryperiodentries inneholder hvilke ansatte som skal ha lønn disse månedene.
//    Denne tabellen har ingen index fordi det behøves ikke.
//
// filer:
//  config.php er "oversiktsmenyen" hvor alle måneder/perioder listes opp.
//  configperiod.php er konfigurasjonen for en gitt måned/periode.
//  record_config.inc er "record.inc"-filen til konfigurasjonen. Dette ble gjort 
//     for å ha litt mer oversikt
//  
// I alle filene prøver jeg å bruke database-interface som allerede eksister i lodo.
// f.eks. $_lib['form3'] og $_lib['db']->get_hashhash, men det er ikke alltid 
// funksjoner passer helt osv.
//
// Konfigurasjonen trer i kraft i list.php
// hvor man nå må velge en periode/måned og deretter blir tvungen til å bruke
// verdiene som er satt opp på forhånd. De forhåndsvalgte ansatte
// kan ikke "unmarkes" fra listen så lenge de er i salaryperiodentries - satt
// opp gjennom konfigurasjonen. Kan merkes at SalaryperiodconfID må følge med i
// _REQUEST (ble gjort for å kunne bruke den som både post og get) i list.php.
//
// Det skjer en liten kollisjon mellom det gamle systemet som bruker
// salaryinfo, men det virker som om de komplementerer hverandre mer enn
// de lager rot akkurat nå. Mulig salaryperiodentries og salaryinfo bør merges
// i fremtiden.
//
//


//
// Henter ut aarene som har data
//
$years = $_lib['db']->get_hash( 
    array( 'query' => 'SELECT Year FROM salaryperiodconf ORDER BY Year ASC', 'key' => 'Year' )
    );
$years[ date("Y") ] = true;     // sikrer at dette aaret et med
$years[ date("Y") + 1 ] = true; // legger til et aar fremover
$confyear = $_lib['input']->getProperty('confyear');
if(!$confyear)
  $confyear = date("Y");

require_once "record_config.inc";

$period_query = "SELECT SalaryperiodconfID, Name, Voucherdate, Period, Fromdate, Todate FROM salaryperiodconf WHERE Year = $confyear ORDER BY SalaryperiodconfID ASC";
$period_result = $_lib['db']->db_query($period_query);

print $_lib['sess']->doctype;
?>
<head>
    <title>Empatix - salary period configuration</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1>L&oslash;nnskonfigurasjon
  <form action="<? print $_lib['sess']->dispatch ?>t=salary.config" method="post">
    <select name="confyear">
      <?
        foreach($years as $y => $dummy)
        {
          printf('<option value="%d" %s>%d</option>', $y, ($y == $confyear ? "selected" : ""), $y );
        }
      ?>
    </select>
    <input type="submit" value="Endre">
  </form>
</h1>

<table class="lodo_data">
  <tr>
    <th></th>
    <th>Navn</th>
    <th>Bilagsdato</th>
    <th>Periode</th>
    <th>Fra dato</th>
    <th>Til dato</th>
    <th></th>
  </tr>

  <?
    $count = 0;
    while($line = $_lib['db']->db_fetch_object($period_result)) 
    {
      $count ++;

      echo '<tr>';
      printf( '<td>%d</td>', $count );

      printf( '<td>%s</td>', $line->Name );
      printf( '<td>%s</td>', $line->Voucherdate );
      printf( '<td>%s</td>', $line->Period );
      printf( '<td>%s</td>', $line->Fromdate );
      printf( '<td>%s</td>', $line->Todate );
      printf( '<td><a href="%st=salary.configperiod&SalaryperiodconfID=%d" class="button">Endre</a></td>', 
              $_lib['sess']->dispatch, $line->SalaryperiodconfID );

      echo '</tr>';
    }

  ?>

</table>

<?
  if($count == 0)
  {
    printf('<a href="%st=salary.config&action_create_defaults=1&confyear=%d" class="button" />Sett inn standardverdier</a>',
      $_lib['sess']->dispatch,
      $confyear
      );
  }
?>  
