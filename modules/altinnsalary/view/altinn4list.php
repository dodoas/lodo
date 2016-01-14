<?
$db_table = "altinnReport4";
require_once "record.inc";

$query  = "select * from $db_table order by AltinnReport4ID";
$result = $_lib['db']->db_query($query);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Soap 4 List</title>
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>


<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn1list">Soap 1 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn2list">Soap 2 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn3list">Soap 3 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn4list">Soap 4 LIST</a>
<a href="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn5list">Soap 5 LIST</a>

<br />
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.altinn4list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_soap4', 'value'=>'Test Soap4')) ?>
</form>


<table class="lodo_data">
  <thead>
    <tr>
      <th>Soap 4:</th>
      <th colspan="39"></th>
    </tr>
    <tr>
      <td class="menu">AltinnReport4ID</td>
      <td class="menu">req_CorrespondenceID</td>
      <td class="menu">res_AllowForwarding</td>
      <td class="menu">res_ArchiveReference</td>
      <td class="menu">res_AuthenticatedUser</td>
      <td class="menu">res_CaseID</td>
      <td class="menu">res_ConfirmationDate</td>
      <td class="menu">res_CorrespondenceID</td>
      <td class="menu">res_CorrespondenceName</td>
      <td class="menu">res_CorrespondenceStatus</td>
      <td class="menu">res_CorrespondenceSubject</td>
      <td class="menu">res_CorrespondenceSummary</td>
      <td class="menu">res_CorrespondenceTitle</td>
      <td class="menu">res_CorrespondenceTxt</td>
      <td class="menu">res_CustomMessageData</td>
      <td class="menu">res_DateSent</td>
      <td class="menu">res_Description</td>
      <td class="menu">res_DueDate</td>
      <td class="menu">res_ExternalSystemReference</td>
      <td class="menu">res_Header</td>
      <td class="menu">res_IsConfirmationNeeded</td>
      <td class="menu">res_LanguageID</td>
      <td class="menu">res_Reportee</td>
      <td class="menu">res_SentBy</td>
      <td class="menu">res_SentTo</td>
      <td class="menu">res_UserID</td>
      <td class="menu">res_AttachmentData</td>
      <td class="menu">res_AttachmentFunctionTypeID</td>
      <td class="menu">res_AttachmentID</td>
      <td class="menu">res_AttachmentName</td>
      <td class="menu">res_AttachmentTypeID</td>
      <td class="menu">res_CreatedByUserID</td>
      <td class="menu">res_CreatedDateTime</td>
      <td class="menu">res_DestinationType</td>
      <td class="menu">res_FileName</td>
      <td class="menu">res_IsAddedAfterFormFillin</td>
      <td class="menu">res_IsAssociatedToFormSet</td>
      <td class="menu">res_IsEncrypted</td>
      <td class="menu">res_ReporteeElementID</td>
      <td class="menu">res_SendersReference</td>
    </tr>
  </thead>
  <tbody>
  <?
  while($row = $_lib['db']->db_fetch_object($result)) {
  $i++;
  ?>
    <tr>
      <td><? print $row->AltinnReport4ID; ?></td>
      <td><? print $row->req_CorrespondenceID; ?></td>
      <td><? print $row->res_AllowForwarding; ?></td>
      <td><? print $row->res_ArchiveReference; ?></td>
      <td><? print $row->res_AuthenticatedUser; ?></td>
      <td><? print $row->res_CaseID; ?></td>
      <td><? print $row->res_ConfirmationDate; ?></td>
      <td><? print $row->res_CorrespondenceID; ?></td>
      <td><? print $row->res_CorrespondenceName; ?></td>
      <td><? print $row->res_CorrespondenceStatus; ?></td>
      <td><? print $row->res_CorrespondenceSubject; ?></td>
      <td><? print $row->res_CorrespondenceSummary; ?></td>
      <td><? print $row->res_CorrespondenceTitle; ?></td>
      <td><? print $row->res_CorrespondenceTxt; ?></td>
      <td><? print $row->res_CustomMessageData; ?></td>
      <td><? print $row->res_DateSent; ?></td>
      <td><? print $row->res_Description; ?></td>
      <td><? print $row->res_DueDate; ?></td>
      <td><? print $row->res_ExternalSystemReference; ?></td>
      <td><? print $row->res_Header; ?></td>
      <td><? print $row->res_IsConfirmationNeeded; ?></td>
      <td><? print $row->res_LanguageID; ?></td>
      <td><? print $row->res_Reportee; ?></td>
      <td><? print $row->res_SentBy; ?></td>
      <td><? print $row->res_SentTo; ?></td>
      <td><? print $row->res_UserID; ?></td>
      <td><? print $row->res_AttachmentData; ?></td>
      <td><? print $row->res_AttachmentFunctionTypeID; ?></td>
      <td><? print $row->res_AttachmentID; ?></td>
      <td><? print $row->res_AttachmentName; ?></td>
      <td><? print $row->res_AttachmentTypeID; ?></td>
      <td><? print $row->res_CreatedByUserID; ?></td>
      <td><? print $row->res_CreatedDateTime; ?></td>
      <td><? print $row->res_DestinationType; ?></td>
      <td><? print $row->res_FileName; ?></td>
      <td><? print $row->res_IsAddedAfterFormFillin; ?></td>
      <td><? print $row->res_IsAssociatedToFormSet; ?></td>
      <td><? print $row->res_IsEncrypted; ?></td>
      <td><? print $row->res_ReporteeElementID; ?></td>
      <td><? print $row->res_SendersReference; ?></td>
    </tr>
  <? } ?>
  </tbody>
</table>
</body>
</html>


