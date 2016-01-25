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
      <td class="menu">req_CorrespondenceID</td>
      <td><? print $row->req_CorrespondenceID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AllowForwarding</td>
      <td><? print $row->res_AllowForwarding; ?></td>
    </tr>
    <tr>
      <td class="menu">res_ArchiveReference</td>
      <td><? print $row->res_ArchiveReference; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AuthenticatedUser</td>
      <td><? print $row->res_AuthenticatedUser; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CaseID</td>
      <td><? print $row->res_CaseID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_ConfirmationDate</td>
      <td><? print $row->res_ConfirmationDate; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceID</td>
      <td><? print $row->res_CorrespondenceID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceName</td>
      <td><? print $row->res_CorrespondenceName; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceStatus</td>
      <td><? print $row->res_CorrespondenceStatus; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceSubject</td>
      <td><? print $row->res_CorrespondenceSubject; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceSummary</td>
      <td><? print $row->res_CorrespondenceSummary; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceTitle</td>
      <td><? print $row->res_CorrespondenceTitle; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CorrespondenceTxt</td>
      <td><? print $row->res_CorrespondenceTxt; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CustomMessageData</td>
      <td><? print $row->res_CustomMessageData; ?></td>
    </tr>
    <tr>
      <td class="menu">res_DateSent</td>
      <td><? print $row->res_DateSent; ?></td>
    </tr>
    <tr>
      <td class="menu">res_Description</td>
      <td><? print $row->res_Description; ?></td>
    </tr>
    <tr>
      <td class="menu">res_DueDate</td>
      <td><? print $row->res_DueDate; ?></td>
    </tr>
    <tr>
      <td class="menu">res_ExternalSystemReference</td>
      <td><? print $row->res_ExternalSystemReference; ?></td>
    </tr>
    <tr>
      <td class="menu">res_Header</td>
      <td><? print $row->res_Header; ?></td>
    </tr>
    <tr>
      <td class="menu">res_IsConfirmationNeeded</td>
      <td><? print $row->res_IsConfirmationNeeded; ?></td>
    </tr>
    <tr>
      <td class="menu">res_LanguageID</td>
      <td><? print $row->res_LanguageID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_Reportee</td>
      <td><? print $row->res_Reportee; ?></td>
    </tr>
    <tr>
      <td class="menu">res_SentBy</td>
      <td><? print $row->res_SentBy; ?></td>
    </tr>
    <tr>
      <td class="menu">res_SentTo</td>
      <td><? print $row->res_SentTo; ?></td>
    </tr>
    <tr>
      <td class="menu">res_UserID</td>
      <td><? print $row->res_UserID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AttachmentData</td>
      <td><? print $row->res_AttachmentData; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AttachmentFunctionTypeID</td>
      <td><? print $row->res_AttachmentFunctionTypeID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AttachmentID</td>
      <td><? print $row->res_AttachmentID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AttachmentName</td>
      <td><? print $row->res_AttachmentName; ?></td>
    </tr>
    <tr>
      <td class="menu">res_AttachmentTypeID</td>
      <td><? print $row->res_AttachmentTypeID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CreatedByUserID</td>
      <td><? print $row->res_CreatedByUserID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_CreatedDateTime</td>
      <td><? print $row->res_CreatedDateTime; ?></td>
    </tr>
    <tr>
      <td class="menu">res_DestinationType</td>
      <td><? print $row->res_DestinationType; ?></td>
    </tr>
    <tr>
      <td class="menu">res_FileName</td>
      <td><? print $row->res_FileName; ?></td>
    </tr>
    <tr>
      <td class="menu">res_IsAddedAfterFormFillin</td>
      <td><? print $row->res_IsAddedAfterFormFillin; ?></td>
    </tr>
    <tr>
      <td class="menu">res_IsAssociatedToFormSet</td>
      <td><? print $row->res_IsAssociatedToFormSet; ?></td>
    </tr>
    <tr>
      <td class="menu">res_IsEncrypted</td>
      <td><? print $row->res_IsEncrypted; ?></td>
    </tr>
    <tr>
      <td class="menu">res_ReporteeElementID</td>
      <td><? print $row->res_ReporteeElementID; ?></td>
    </tr>
    <tr>
      <td class="menu">res_SendersReference</td>
      <td><? print $row->res_SendersReference; ?></td>
    </tr>

    <tr>
      <td class="menu">XML</td>
      <td>
        <textarea rows="50" cols="130">
          <?
          $altinnFile = new altinn_file($row->Folder);
          $doc = new DOMDocument();
          $doc->formatOutput = true;
          $doc->loadXML($altinnFile->readFile("/tilbakemelding.xml"));
          print $doc->saveXML();
          ?>
        </textarea>
      </td>
    </tr>


  </table>
</body>
</html>
