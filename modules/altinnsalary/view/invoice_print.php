<?
includelogic('altinnsalary/files');
includelogic('accounting/accounting');
global $_lib;
$accounting = new accounting();

$invoice_type = $_REQUEST['type'];
$invoice_type_text = ($invoice_type == "AGA") ? "Arbeidsgiveravgift" : "Forskuddstrekk";

if (!isset($_GET['AltinnReport4ID'])) {
  print "AltinnReport4ID missing";
} else {
  $query_altinn_report4 = "select * from altinnReport4 where AltinnReport4ID = " . $_GET['AltinnReport4ID'];
  $result_altinn_report4 = $_lib['db']->db_query($query_altinn_report4);
  $altinn_report4_row = $_lib['db']->db_fetch_object($result_altinn_report4);

  $altinn_file = new altinn_file($altinn_report4_row->Folder);
  $file_contents = $altinn_file->readFile("tilbakemelding" . $altinn_report4_row->AltinnReport4ID . ".xml");
  if (!$file_contents) {
    print 'Filen kan ikke leses.';
  } else {
    $xml = simplexml_load_string($file_contents);

    # set locale for time (for month/day names) to Norwegian
    setlocale(LC_TIME, 'nb_NO');

    # faktura fields
    $name = $_lib['sess']->get_companydef('CompanyName');
    $orgnr = (isset($xml->Mottak->innsender->norskIdentifikator)) ? $xml->Mottak->innsender->norskIdentifikator : "ikke funnet";
    $month = (isset($xml->Mottak->kalendermaaned)) ? strftime("%B %Y", strtotime($xml->Mottak->kalendermaaned)) : "ikke funnet";
    $altinn_reference = $altinn_report4_row->res_ArchiveReference;
    $invoice_name = 'A-melding med Altinn-referanse ' . $altinn_reference;

    $query_altinn_report5 = "select * from altinnReport5 where req_CorrespondenceID = '" . $altinn_report4_row->req_CorrespondenceID . "' order by AltinnReport5ID";
    $result_altinn_report5 = $_lib['db']->db_query($query_altinn_report5);
    $altinn_report5_row = $_lib['db']->db_fetch_object($result_altinn_report5);
    $altinn_archive_date = (isset($altinn_report5_row->res_LastChanged)) ? strftime("%F %T", strtotime($altinn_report5_row->res_LastChanged)) : "ikke arkivert";

    $query_altinn_report1 = "select * from altinnReport1 where ReceiptId = (select req_ReceiptId from altinnReport2 where res_ReceiversReference = '" . $altinn_report4_row->req_CorrespondenceID . "' order by res_ReceiversReference desc, AltinnReport2ID limit 1)";
    $result_altinn_report1 = $_lib['db']->db_query($query_altinn_report1);
    $altinn_report1_row = $_lib['db']->db_fetch_object($result_altinn_report1);
    $message_id = $altinn_report1_row->MeldingsId;

    $recieved_messages = $xml->Mottak->mottattLeveranse;

    $bank_account_number = (isset($xml->Mottak->innbetalingsinformasjon->kontonummer)) ? $xml->Mottak->innbetalingsinformasjon->kontonummer : "ikke funnet";
    if ($invoice_type == "AGA") {
      $kid = (isset($xml->Mottak->innbetalingsinformasjon->kidForArbeidsgiveravgift)) ? $xml->Mottak->innbetalingsinformasjon->kidForArbeidsgiveravgift : "ikke funnet";
      $amount = $_lib['format']->Amount($xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumArbeidsgiveravgift);
    } else {
      $kid = (isset($xml->Mottak->innbetalingsinformasjon->kidForForskuddstrekk)) ? $xml->Mottak->innbetalingsinformasjon->kidForForskuddstrekk : "ikke funnet";
      $amount = $_lib['format']->Amount($xml->Mottak->mottattPeriode->mottattAvgiftOgTrekkTotalt->sumForskuddstrekk);
    }
    $due_date = (isset($xml->Mottak->innbetalingsinformasjon->forfallsdato)) ? $xml->Mottak->innbetalingsinformasjon->forfallsdato : "ikke funnet";

?>
<head>
    <title>Faktura <? print $invoice_name ?></title>
    <? includeinc('head') ?>
</head>

<body>

<table class="lodo_data">
  <tr>
    <th colspan="5"><h2><?= $invoice_name . " (" . $invoice_type_text . ")" ?><h2/></th>
  </tr>
  <tr>
    <td colspan="2" class="menu">Navn</td>
    <td colspan="3"><?= $name ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Orgnr/fnr/dnr</td>
    <td colspan="3"><?= $orgnr ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Kalendarm&aring;ned</td>
    <td colspan="3"><?= $month ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Altinnreferanse</td>
    <td colspan="3"><?= $altinn_reference ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Arkivdato Altinn</td>
    <td colspan="3"><?= $altinn_archive_date ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">MeldingsId</td>
    <td colspan="3"><?= $message_id ?></td>
  </tr>
  <tr>
    <th colspan="5"><br/>Oppsummering av meldinger for kalenderm&aring;ned <?= $month ?><br/><br/></th>
  </tr>
  <tr>
    <td class="menu">Arkivdato i Altinn</td>
    <td class="menu">Altinnreferanse</td>
    <td class="menu">Kommentar</td>
    <td class="menu">Antall inntektsmottakere</td>
    <td class="menu"><?= $invoice_type_text ?></td>
  </tr>
<? foreach($recieved_messages as $message) {
     $message_archive_date = (isset($message->leveringstidspunkt)) ? strftime("%F %T", strtotime($message->leveringstidspunkt)) : "";
     $message_archive_date = (empty($message_archive_date) && $message->mottakstatus == "erstattet") ?  strftime("%F %T", strtotime($message->tidsstempelFraAltinn)) : $message_archive_date;
     $message_altinn_reference = $message->altinnReferanse;
     $message_id = (string)$message->meldingsId;

     $query_altinn_report1_message = "select * from altinnReport1 where MeldingsId = '" . $message_id . "'";
     $altinn_report1_message_row = $_lib['db']->get_row(array('query' => $query_altinn_report1_message));

     $query_replacement_message = "select * from altinnReport2 where req_ReceiptId = (select ReceiptId from altinnReport1 where meldingsId = '" . $altinn_report1_message_row->ErstatterMeldingsId . "')";
     $replacement_message_row = $_lib['db']->get_row(array('query' => $query_replacement_message));
     $replacement_message = $replacement_message_row->res_ArchiveReference;

     $query_replaced_by_message = "select * from altinnReport2 where req_ReceiptId = (select ReceiptId from altinnReport1 where meldingsId = '" . $altinn_report1_message_row->ReplacedByMeldindsID . "')";
     $replaced_by_message_row = $_lib['db']->get_row(array('query' => $query_replaced_by_message));
     $replaced_by_message = $replaced_by_message_row->res_ArchiveReference;

     $message_comment = "";
     if ($replaced_by_message) $message_comment .= "Erstattet av " . $replaced_by_message;
     if ($replaced_by_message && $replacement_message) $message_comment .= "<br/>";
     if ($replacement_message) $message_comment .= "Erstatter " . $replacement_message;

     $message_income_receivers_count = $message->antallInntektsmottakere;
     $message_amount = ($invoice_type == "AGA") ? $message->mottattAvgiftOgTrekkTotalt->sumArbeidsgiveravgift : $message->mottattAvgiftOgTrekkTotalt->sumForskuddstrekk; 
     $message_amount = $_lib['format']->Amount($message_amount);
?>
  <tr>
    <td><?= $message_archive_date ?></td>
    <td><?= $message_altinn_reference ?></td>
    <td><?= $message_comment ?></td>
    <td><?= $message_income_receivers_count ?></td>
    <td><?= $message_amount ?></td>
  </tr>
<? } ?>
  <tr>
    <th colspan="5">
      <br/>Betalingsinformasjon for <?= $month ?><br/><br/>
      <?= $invoice_type_text ?> fra forrige/neste m&aring;ned med samme betalingsfrist, er ikke<br/>
      inkludert i bel&oslash;pene ovenfor.<br/><br/>
    </th>
  </tr>
  <tr>
    <td colspan="2" class="menu">Kontonummer</td>
    <td colspan="3"><?= $bank_account_number ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">KID</td>
    <td colspan="3"><?= $kid ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Forfallsdato</td>
    <td colspan="3"><?= $due_date ?></td>
  </tr>
  <tr>
    <td colspan="2" class="menu">Bel&oslash;p</td>
    <td colspan="3"><?= $amount ?></td>
  </tr>
</table>
</body>
<?
  }
}
?>
</html>
