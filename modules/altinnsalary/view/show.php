<?
$db_table = "altinnReport1";
require_once "record.inc";
?>

<?
if (isset($_GET['AltinnReport1ID'])) {
  $query_altinn = "select * from $db_table where AltinnReport1ID = ".$_GET['AltinnReport1ID'];
  $result = $_lib['db']->db_query($query_altinn);
  $row = $_lib['db']->db_fetch_object($result);
  if (isset($row)) {
    # do nothing
  } else {
    header('Location: '.$_lib['sess']->dispatchs.'t=altinnsalary.list');
  }
} else {
  header('Location: '.$_lib['sess']->dispatchs.'t=altinnsalary.list');
}

print $_lib['sess']->doctype ?>

<head>
  <title>Empatix - Soap 1 List</title>
  <? includeinc('head') ?>

  <? includeinc('top') ?>
  <? includeinc('left') ?>
  <? print $_lib['message']->get() ?>
</head>

<body>
  <table class="lodo_data">
    <tr>
      <td class="menu">AltinnReport1ID</td>
      <td><? print $row->AltinnReport1ID; ?></td>
    </tr>
    <tr>
      <td class="menu">Folder</td>
      <td><? print $row->Folder; ?></td>
    </tr>
    <tr>
      <td class="menu">Period</td>
      <td><? print $row->Period; ?></td>
    </tr>
    <tr>
      <td class="menu">ReceiptId</td>
      <td><? print $row->ReceiptId; ?></td>
    </tr>
    <tr>
      <td class="menu">ParentReceiptId</td>
      <td><? print $row->ParentReceiptId; ?></td>
    </tr>
    <tr>
      <td class="menu">ReceiptText</td>
      <td><? print $row->ReceiptText; ?></td>
    </tr>
    <tr>
      <td class="menu">ReceiptHistory</td>
      <td><? print $row->ReceiptHistory; ?></td>
    </tr>
    <tr>
      <td class="menu">LastChanged</td>
      <td><? print $row->LastChanged; ?></td>
    </tr>
    <tr>
      <td class="menu">ReceiptTypeName</td>
      <td><? print $row->ReceiptTypeName; ?></td>
    </tr>
    <tr>
      <td class="menu">ReceiptStatusCode</td>
      <td><? print $row->ReceiptStatusCode; ?></td>
    </tr>
    <tr>
      <td class="menu">ExternalShipmentReference</td>
      <td><? print $row->ExternalShipmentReference; ?></td>
    </tr>
    <tr>
      <td class="menu">OwnerPartyReference</td>
      <td><? print $row->OwnerPartyReference; ?></td>
    </tr>
    <tr>
      <td class="menu">PartyReference</td>
      <td><? print $row->PartyReference; ?></td>
    </tr>

    <tr>
      <td class="menu">L&oslash;nnslipper</td>
      <td>
        <?
        // TODO maybe save changed to show changed one
        $query_salary   = "SELECT * FROM altinnReport1salary WHERE AltinnReport1ID = ".$row->AltinnReport1ID." ORDER BY SalaryId ASC";
        $result_salary  = $_lib['db']->db_query($query_salary);

        while($_row = $_lib['db']->db_fetch_object($result_salary)){
        ?>
          L<a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $_row->SalaryId ?>"><? print $_row->JournalID ?></a>
        <? } ?>
      </td>
    </tr>
    <tr>
      <td class="menu">XML</td>
      <td>
        <textarea rows="50" cols="130"><?
          $altinnFile = new altinn_file($row->Folder);
          $doc = new DOMDocument();
          $doc->formatOutput = true;
          $doc->loadXML($altinnFile->readFile("/A-melding.xml"));
          print $doc->saveXML();
        ?></textarea>
      </td>
    </tr>
  </table>
</body>
</html>


