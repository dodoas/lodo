<?
$db_table = "altinnReport4";
require_once "record.inc";
?>

<?
if (isset($_GET['AltinnReport4ID'])) {
  $query_altinn = "select * from $db_table where AltinnReport4ID = ".$_GET['AltinnReport4ID'];
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
  <title>Empatix - Soap 4</title>
  <? includeinc('head') ?>
  <? includeinc('top') ?>
  <? includeinc('left') ?>
  <? print $_lib['message']->get() ?>
</head>

<body>
  <table class="lodo_data">
    <tr>
      <td class="menu">AltinnReport4ID</td>
      <td><? print $row->AltinnReport4ID; ?></td>
    </tr>
    <tr>
      <td class="menu">Folder</td>
      <td><? print $row->Folder; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceID</td>
      <td><? print $row->req_CorrespondenceID; ?></td>
    </tr>
    <tr>
      <td class="menu">AllowForwarding</td>
      <td><? print $row->res_AllowForwarding; ?></td>
    </tr>
    <tr>
      <td class="menu">ArchiveReference</td>
      <td><? print $row->res_ArchiveReference; ?></td>
    </tr>
    <tr>
      <td class="menu">AuthenticatedUser</td>
      <td><? print $row->res_AuthenticatedUser; ?></td>
    </tr>
    <tr>
      <td class="menu">CaseID</td>
      <td><? print $row->res_CaseID; ?></td>
    </tr>
    <tr>
      <td class="menu">ConfirmationDate</td>
      <td><? print $row->res_ConfirmationDate; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceID</td>
      <td><? print $row->res_CorrespondenceID; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceName</td>
      <td><? print $row->res_CorrespondenceName; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceStatus</td>
      <td><? print $row->res_CorrespondenceStatus; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceSubject</td>
      <td><? print $row->res_CorrespondenceSubject; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceSummary</td>
      <td><? print $row->res_CorrespondenceSummary; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceTitle</td>
      <td><? print $row->res_CorrespondenceTitle; ?></td>
    </tr>
    <tr>
      <td class="menu">CorrespondenceTxt</td>
      <td><? print $row->res_CorrespondenceTxt; ?></td>
    </tr>
    <tr>
      <td class="menu">CustomMessageData</td>
      <td><? print $row->res_CustomMessageData; ?></td>
    </tr>
    <tr>
      <td class="menu">DateSent</td>
      <td><? print $row->res_DateSent; ?></td>
    </tr>
    <tr>
      <td class="menu">Description</td>
      <td><? print $row->res_Description; ?></td>
    </tr>
    <tr>
      <td class="menu">DueDate</td>
      <td><? print $row->res_DueDate; ?></td>
    </tr>
    <tr>
      <td class="menu">ExternalSystemReference</td>
      <td><? print $row->res_ExternalSystemReference; ?></td>
    </tr>
    <tr>
      <td class="menu">Header</td>
      <td><? print $row->res_Header; ?></td>
    </tr>
    <tr>
      <td class="menu">IsConfirmationNeeded</td>
      <td><? print $row->res_IsConfirmationNeeded; ?></td>
    </tr>
    <tr>
      <td class="menu">LanguageID</td>
      <td><? print $row->res_LanguageID; ?></td>
    </tr>
    <tr>
      <td class="menu">Reportee</td>
      <td><? print $row->res_Reportee; ?></td>
    </tr>
    <tr>
      <td class="menu">SentBy</td>
      <td><? print $row->res_SentBy; ?></td>
    </tr>
    <tr>
      <td class="menu">SentTo</td>
      <td><? print $row->res_SentTo; ?></td>
    </tr>
    <tr>
      <td class="menu">UserID</td>
      <td><? print $row->res_UserID; ?></td>
    </tr>
    <tr>
      <td class="menu">AttachmentData</td>
      <td><? print $row->res_AttachmentData; ?></td>
    </tr>
    <tr>
      <td class="menu">AttachmentFunctionTypeID</td>
      <td><? print $row->res_AttachmentFunctionTypeID; ?></td>
    </tr>
    <tr>
      <td class="menu">AttachmentID</td>
      <td><? print $row->res_AttachmentID; ?></td>
    </tr>
    <tr>
      <td class="menu">AttachmentName</td>
      <td><? print $row->res_AttachmentName; ?></td>
    </tr>
    <tr>
      <td class="menu">AttachmentTypeID</td>
      <td><? print $row->res_AttachmentTypeID; ?></td>
    </tr>
    <tr>
      <td class="menu">CreatedByUserID</td>
      <td><? print $row->res_CreatedByUserID; ?></td>
    </tr>
    <tr>
      <td class="menu">CreatedDateTime</td>
      <td><? print $row->res_CreatedDateTime; ?></td>
    </tr>
    <tr>
      <td class="menu">DestinationType</td>
      <td><? print $row->res_DestinationType; ?></td>
    </tr>
    <tr>
      <td class="menu">FileName</td>
      <td><? print $row->res_FileName; ?></td>
    </tr>
    <tr>
      <td class="menu">IsAddedAfterFormFillin</td>
      <td><? print $row->res_IsAddedAfterFormFillin; ?></td>
    </tr>
    <tr>
      <td class="menu">IsAssociatedToFormSet</td>
      <td><? print $row->res_IsAssociatedToFormSet; ?></td>
    </tr>
    <tr>
      <td class="menu">IsEncrypted</td>
      <td><? print $row->res_IsEncrypted; ?></td>
    </tr>
    <tr>
      <td class="menu">ReporteeElementID</td>
      <td><? print $row->res_ReporteeElementID; ?></td>
    </tr>
    <tr>
      <td class="menu">SendersReference</td>
      <td><? print $row->res_SendersReference; ?></td>
    </tr>

    <tr>
      <td class="menu">XML</td>
      <td>
        <textarea rows="50" cols="130"><?
          $altinnFile = new altinn_file($row->Folder);
          $doc = new DOMDocument('1.0', 'utf-8');
          $doc->formatOutput = true;
          $fileContents = $altinnFile->readFile("tilbakemelding" . $row->AltinnReport4ID . ".xml");
          if ($fileContents) {
            $doc->loadXML($fileContents);
            $xml = $doc->saveXML();
          }
          else $xml = 'Filen kan ikke leses.';
          print $xml;
        ?></textarea>
      </td>
    </tr>


  </table>
</body>
</html>
