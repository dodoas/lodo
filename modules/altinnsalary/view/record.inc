<?
includelogic('altinnsalary/savefiles');

class LocalSoapClient extends SoapClient {
  function __doRequest($request, $location, $action, $version, $one_way = 0) {
    // error_log('===================================', 3, $_SETUP['HOME_DIR']."/error.log");
    // error_log("$request\n", 3, $_SETUP['HOME_DIR']."/error.log");

    // TODO !!why
    // formTaskShipment->FormTasks->Forms->Form->FormData
    // In soap reqest 1 we send one string that contain xml and this have
    // to be valid xml when we send it. The only way I found to do that,
    // is to replace '&lt;' with '<' and '&gt;' with '>'since it get
    // encoded somewhere between I pass the argument to __soapCall()
    // untill this moneypatch
    $request = str_replace('&lt;','<', $request);
    $request = str_replace('&gt;','>', $request);

    // error_log("Now it is replaced\n", 3, $_SETUP['HOME_DIR']."/error.log");
    // error_log("$request\n", 3, $_SETUP['HOME_DIR']."/error.log");
    return parent::__doRequest($request, $location, $action, $version, $one_way);
  }
}

// TODO Make some interface for inputting this. We should't eave store
// this in lodo. There is also a possabliety to get a SMS with pin code
// If we do we are storing information that makes it possible to send
// report to altinn if you broke into lodo.
// This ones are the one I got in the letter with all pincodes for the test user
$pins = array('','ajhhs', 'piksd', 'iuyhs', 'asdfg', 'rtefs', 'loj7s', 'mmmyp', 'juksa', 'fizck', 'qicks', '98ujs', 'mnbvs', 'qwers', 'polze', 'ztang', 'alt1n', 'zcatt', 'kjasd', '23as3', 'lkiju', '4564s', 'zxhfg', 'alsks', 'ooiks', 'likme', 'kaffe', 'arbei', '00kks', 'mjhgg', 'ziste');


if ($_lib['input']->getProperty('action_soap1')) {
  // If you didn't select a period, don't send.
  if (empty($_POST['altinnReport1_periode'])){
    $_lib['message']->add('Legg inn: periode');
  } else {
    $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/IntermediaryExternal/IntermediaryInboundBasic.svc?wsdl", array('trace' => 1, 'encoding'=>'UTF-8'));
    $params = array(
      'systemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
      'systemPassword' => 'altinnlodo0',
      'formTaskShipment' => array(
        'Reportee' => 910748645,
        'ExternalShipmentReference' => $_POST['altinnReport1_ExternalShipmentReference'],
        'FormTasks' => array(
          // this codes are for this very spesific report.
          // so maybe hard coding is ok...
          'ServiceCode' => 3357,
          'ServiceEdition' => 130429,
          'Forms' => array(
            'Form' => array(
              'Completed' => true,
              'DataFormatId' => 4166,
              'DataFormatVersion' => 35895,
              'EndUserSystemReference' => 115173,
              'FormData' => '
                <![CDATA[
                <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                <melding dataFormatVersion="35895" dataFormatId="4166" dataFormatProvider="SERES">
                  <InformasjonOmInnsender>
                    <innsender>
                      <norskIdentifikator>910748645</norskIdentifikator>
                    </innsender>
                  </InformasjonOmInnsender>
                  <InnsendtInformasjon>
                    <innsending>
                      <meldingsId>108</meldingsId>
                      <kalendermaaned>'.$_POST['altinnReport1_periode'].'</kalendermaaned>
                    </innsending>
                  </InnsendtInformasjon>
                </melding>
                ]]>
              '
            )
          )
        ),
        'Attachments' => array(
          'Attachment' => array(
            'Name' => 'A-melding',
            'FileName' => 'amelding.xml',
            'AttachementData' => 'PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPG1lbGRpbmcgeG1sbnM9InVybjpza2U6ZmFzdHNldHRpbmc6aW5uc2FtbGluZzphLW1lbGRpbmdlbjp2Ml8wIj4KICA8TGV2ZXJhbnNlPgogICAgPGxldmVyaW5nc3RpZHNwdW5rdD4yMDE1LTEyLTExVDAwOjAwOjAwWjwvbGV2ZXJpbmdzdGlkc3B1bmt0PgogICAgPGthbGVuZGVybWFhbmVkPjIwMTUtMTI8L2thbGVuZGVybWFhbmVkPgogICAgPGtpbGRlc3lzdGVtPkxPRE88L2tpbGRlc3lzdGVtPgogICAgPG1lbGRpbmdzSWQ+MTA4PC9tZWxkaW5nc0lkPgogICAgPG9wcGx5c25pbmdzcGxpa3RpZz4KICAgICAgPG5vcnNrSWRlbnRpZmlrYXRvcj45MTA3NDg2NDU8L25vcnNrSWRlbnRpZmlrYXRvcj4KICAgIDwvb3BwbHlzbmluZ3NwbGlrdGlnPgogICAgPG9wcGdhdmU+CiAgICAgIDxiZXRhbGluZ3NpbmZvcm1hc2pvbj4KICAgICAgICA8c3VtRm9yc2t1ZGRzdHJla2s+MzAwMDwvc3VtRm9yc2t1ZGRzdHJla2s+CiAgICAgICAgPHN1bUFyYmVpZHNnaXZlcmF2Z2lmdD4xNDEwPC9zdW1BcmJlaWRzZ2l2ZXJhdmdpZnQ+CiAgICAgIDwvYmV0YWxpbmdzaW5mb3JtYXNqb24+CiAgICAgIDx2aXJrc29taGV0PgogICAgICAgIDxub3Jza0lkZW50aWZpa2F0b3I+OTEwNzQ4NjQ1PC9ub3Jza0lkZW50aWZpa2F0b3I+CiAgICAgICAgPGlubnRla3RzbW90dGFrZXI+CiAgICAgICAgICA8bm9yc2tJZGVudGlmaWthdG9yPjI0MDQwMTAwOTE4PC9ub3Jza0lkZW50aWZpa2F0b3I+CiAgICAgICAgICA8aWRlbnRpZmlzZXJlbmRlSW5mb3JtYXNqb24+CiAgICAgICAgICAgIDxuYXZuPk1BUlRJTiBFSURFTTwvbmF2bj4KICAgICAgICAgICAgPGZvZWRzZWxzZGF0bz4yMDAxLTA0LTI0PC9mb2Vkc2Vsc2RhdG8+CiAgICAgICAgICA8L2lkZW50aWZpc2VyZW5kZUluZm9ybWFzam9uPgogICAgICAgICAgPGFyYmVpZHNmb3Job2xkPgogICAgICAgICAgICA8dHlwZUFyYmVpZHNmb3Job2xkPm9yZGluYWVydEFyYmVpZHNmb3Job2xkPC90eXBlQXJiZWlkc2ZvcmhvbGQ+CiAgICAgICAgICAgIDxzdGFydGRhdG8+MjAxMy0xMS0wMTwvc3RhcnRkYXRvPgogICAgICAgICAgICA8YW50YWxsVGltZXJQZXJVa2VTb21FbkZ1bGxTdGlsbGluZ1RpbHN2YXJlcj4zNy41PC9hbnRhbGxUaW1lclBlclVrZVNvbUVuRnVsbFN0aWxsaW5nVGlsc3ZhcmVyPgogICAgICAgICAgICA8YXZsb2VubmluZ3N0eXBlPmZhc3Q8L2F2bG9lbm5pbmdzdHlwZT4KICAgICAgICAgICAgPHlya2U+MjEzMDEwOTwveXJrZT4KICAgICAgICAgICAgPGFyYmVpZHN0aWRzb3JkbmluZz5pa2tlU2tpZnQ8L2FyYmVpZHN0aWRzb3JkbmluZz4KICAgICAgICAgICAgPHN0aWxsaW5nc3Byb3NlbnQ+MTAwPC9zdGlsbGluZ3Nwcm9zZW50PgogICAgICAgICAgICA8c2lzdGVMb2VubnNlbmRyaW5nc2RhdG8+MjAxMy0xMS0wMTwvc2lzdGVMb2VubnNlbmRyaW5nc2RhdG8+CiAgICAgICAgICAgIDxsb2VubnNhbnNpZW5uaXRldD4yMDEzLTExLTAxPC9sb2VubnNhbnNpZW5uaXRldD4KICAgICAgICAgICAgPHNpc3RlRGF0b0ZvclN0aWxsaW5nc3Byb3NlbnRlbmRyaW5nPjIwMTMtMTEtMDE8L3Npc3RlRGF0b0ZvclN0aWxsaW5nc3Byb3NlbnRlbmRyaW5nPgogICAgICAgICAgPC9hcmJlaWRzZm9yaG9sZD4KICAgICAgICAgIDxmb3Jza3VkZHN0cmVraz4KICAgICAgICAgICAgPGJlbG9lcD4tMzAwMDwvYmVsb2VwPgogICAgICAgICAgPC9mb3Jza3VkZHN0cmVraz4KICAgICAgICAgIDxpbm50ZWt0PgogICAgICAgICAgICA8Zm9yZGVsPmtvbnRhbnR5dGVsc2U8L2ZvcmRlbD4KICAgICAgICAgICAgPHV0bG9lc2VyQXJiZWlkc2dpdmVyYXZnaWZ0PnRydWU8L3V0bG9lc2VyQXJiZWlkc2dpdmVyYXZnaWZ0PgogICAgICAgICAgICA8aW5uZ2FhcklHcnVubmxhZ0ZvclRyZWtrPnRydWU8L2lubmdhYXJJR3J1bm5sYWdGb3JUcmVraz4KICAgICAgICAgICAgPGJlbG9lcD4xMDAwMDwvYmVsb2VwPgogICAgICAgICAgICA8bG9lbm5zaW5udGVrdD4KICAgICAgICAgICAgICA8YmVza3JpdmVsc2U+ZmFzdGxvZW5uPC9iZXNrcml2ZWxzZT4KICAgICAgICAgICAgPC9sb2VubnNpbm50ZWt0PgogICAgICAgICAgPC9pbm50ZWt0PgogICAgICAgIDwvaW5udGVrdHNtb3R0YWtlcj4KICAgICAgICA8YXJiZWlkc2dpdmVyYXZnaWZ0PgogICAgICAgICAgPGxvZW5uT2dHb2R0Z2pvZXJlbHNlPgogICAgICAgICAgICA8YmVyZWduaW5nc2tvZGVGb3JBcmJlaWRzZ2l2ZXJhdmdpZnQ+Z2VuZXJlbGxlTmFlcmluZ2VyPC9iZXJlZ25pbmdza29kZUZvckFyYmVpZHNnaXZlcmF2Z2lmdD4KICAgICAgICAgICAgPHNvbmU+MTwvc29uZT4KICAgICAgICAgICAgPGF2Z2lmdHNncnVubmxhZ0JlbG9lcD4xMDAwMDwvYXZnaWZ0c2dydW5ubGFnQmVsb2VwPgogICAgICAgICAgICA8cHJvc2VudHNhdHNGb3JBdmdpZnRzYmVyZWduaW5nPjE0LjE8L3Byb3NlbnRzYXRzRm9yQXZnaWZ0c2JlcmVnbmluZz4KICAgICAgICAgIDwvbG9lbm5PZ0dvZHRnam9lcmVsc2U+CiAgICAgICAgPC9hcmJlaWRzZ2l2ZXJhdmdpZnQ+CiAgICAgIDwvdmlya3NvbWhldD4KICAgIDwvb3BwZ2F2ZT4KICA8L0xldmVyYW5zZT4KPC9tZWxkaW5nPgo='
          )
        )
      )
    );

    $result = $sc->__soapCall('SubmitFormTaskBasic', array('parameters' => $params));

    $values = array();
    $values['ReceiptId']  = $result->SubmitFormTaskBasicResult->ReceiptId;
    $values['ReceiptText']  = $result->SubmitFormTaskBasicResult->ReceiptText;
    $values['ReceiptHistory']  = $result->SubmitFormTaskBasicResult->ReceiptHistory;
    $values['LastChanged']  = $result->SubmitFormTaskBasicResult->LastChanged;
    $values['ReceiptTypeName']  = $result->SubmitFormTaskBasicResult->ReceiptTypeName;
    $values['ReceiptStatusCode']  = $result->SubmitFormTaskBasicResult->ReceiptStatusCode;
    $values['ParentReceiptId']  = $result->SubmitFormTaskBasicResult->ParentReceiptId;

    for ($i=0; $i < count($result->SubmitFormTaskBasicResult->References->ReferenceBE); $i++) {
      $element = $result->SubmitFormTaskBasicResult->References->ReferenceBE[$i];
      $values[$element->ReferenceTypeName] = $element->ReferenceValue;
    }

    $_lib['storage']->store_record(array('table' => 'altinnReport1', 'data' => $values, 'debug' => false));

    // to fetch the AltinnReport1ID to the one I just saved
    $query = 'SELECT AltinnReport1ID FROM altinnReport1 order by AltinnReport1ID desc limit 1';
    $res = $_lib['db']->db_query($query);
    $row = $_lib['db']->db_fetch_object($res);

    $query_salary  = "SELECT SalaryID FROM salary WHERE PayDate LIKE  '" . $_REQUEST['altinnReport1_periode'] . "%'";
    $result_salary = $_lib['db']->db_query($query_salary);
    $insert_query = 'INSERT INTO altinnReport1salary (AltinnReport1ID, SalaryId) VALUES ';
    while($_row = $_lib['db']->db_fetch_object($result_salary)){
      $insert_query .= "('".$row->AltinnReport1ID."', '".$_row->SalaryID."'),";
    }
    if (isset($result_salary->num_rows)) {
      $insert_query = substr($insert_query, 0, -1);
      $_lib['db']->db_query($insert_query);
    }
  }

} elseif($_lib['input']->getProperty('action_soap2')) {
  // TODO, abstract this into a function
  $query = 'SELECT * FROM altinnReport1 order by ReceiptId desc limit 1';
  $_row = $_lib['db']->get_row(array('query' => $query));

  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/IntermediaryExternal/ReceiptExternalBasic.svc?wsdl");
  $params = array(
    'systemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
    'systemPassword' => 'altinnlodo0',
    'receipt' => array(
      'ReceiptId' => $_row->ReceiptId,
    )
  );
  $result = $sc->__soapCall('GetReceiptBasicV2', array('parameters' => $params));

  $values = array();
  $values['req_ReceiptId']       = $_row->ReceiptId;
  $values['res_LastChanged']     = $result->GetReceiptBasicV2Result->LastChanged;
  $values['res_ParentReceiptId'] = $result->GetReceiptBasicV2Result->ParentReceiptId;
  $values['res_ReceiptHistory']  = $result->GetReceiptBasicV2Result->ReceiptHistory;
  $values['res_ReceiptId']       = $result->GetReceiptBasicV2Result->ReceiptId;
  $values['res_ReceiptStatus']   = $result->GetReceiptBasicV2Result->ReceiptStatus;
  $values['res_ReceiptTemplate'] = $result->GetReceiptBasicV2Result->ReceiptTemplate;
  $values['res_ReceiptText']     = $result->GetReceiptBasicV2Result->ReceiptText;
  $values['res_ReceiptType']     = $result->GetReceiptBasicV2Result->ReceiptType;

  for ($i=0; $i < count($result->GetReceiptBasicV2Result->References->Reference); $i++) {
    $element = $result->GetReceiptBasicV2Result->References->Reference[$i];
    if ($element->ReferenceType == "PartyReference") {
      if (!isset($values[$element->ReferenceType.'A'])) {
        $values['res_'.$element->ReferenceType.'A'] = $element->ReferenceValue;
      } else {
        $values['res_'.$element->ReferenceType.'B'] = $element->ReferenceValue;
      }
    } else {
      $values['res_'.$element->ReferenceType] = $element->ReferenceValue;
    }
  }

  $values['res_SubReceiptsLastChanged']      = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->LastChanged;
  $values['res_SubReceiptsParentReceiptId']  = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ParentReceiptId;
  $values['res_SubReceiptsReceiptHistory']   = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptHistory;
  $values['res_SubReceiptsReceiptId']        = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptId;
  $values['res_SubReceiptsReceiptStatus']    = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptStatus;
  $values['res_SubReceiptsReceiptTemplate']  = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptTemplate;
  $values['res_SubReceiptsReceiptText']      = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptText;
  $values['res_SubReceiptsReceiptType']      = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->ReceiptType;
  $values['res_SubReceiptsSendersReference'] = $result->GetReceiptBasicV2Result->SubReceipts->Receipt->References->Reference->ReferenceValue;

  $_lib['storage']->store_record(array('table' => 'altinnReport2', 'data' => $values, 'debug' => false));

} elseif($_lib['input']->getProperty('action_soap3')) {
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/AuthenticationExternal/SystemAuthentication.svc?wsdl");
  $params = array(
    'challengeRequest' => array(
      'AuthMethod' => 'AltinnPin',
      'SystemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
      'UserPassword'   => $_SETUP['ALTINN_USER_PASSWORD'],
      'UserSSN'        => $_SETUP['ALTINN_USER_SSN']
    )
  );

  $result = $sc->__soapCall('GetAuthenticationChallenge', array('parameters' => $params));

  $values = array();
  $values['res_Message']   = $result->GetAuthenticationChallengeResult->Message;
  $_parseMessage           = explode(' ', $values['res_Message']);
  $values['res_KodeNr']    = $_parseMessage[2];
  $values['res_Status']    = $result->GetAuthenticationChallengeResult->Status;
  $values['res_ValidFrom'] = $result->GetAuthenticationChallengeResult->ValidFrom;
  $values['res_ValidTo']   = $result->GetAuthenticationChallengeResult->ValidTo;

  $_lib['storage']->store_record(array('table' => 'altinnReport3', 'data' => $values, 'debug' => false));

} elseif($_lib['input']->getProperty('action_soap4')) {
  $query = 'SELECT * FROM altinnReport2 where res_ReceiversReference IS NOT NULL order by AltinnReport2Id desc limit 1';
  $_rowB = $_lib['db']->get_row(array('query' => $query));

  $query = 'SELECT * FROM altinnReport3 order by AltinnReport3Id desc limit 1';
  $_rowC = $_lib['db']->get_row(array('query' => $query));


  $values = array();
  $values["req_correspondenceID"] = $_rowB->ReceiversReference;
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/ServiceEngineExternal/CorrespondenceExternalBasic.svc?wsdl", array('trace' => 1, 'encoding'=>'UTF-8'));
  $params = array(
    'systemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
    'systemPassword' => 'altinnlodo0',
    'userSSN'        => $_SETUP['ALTINN_USER_SSN'],
    'userPassword'   => $_SETUP['ALTINN_USER_PASSWORD'],

    'userPinCode'    => $pins[$_rowC->res_KodeNr],
    'authMethod'     => 'AltinnPin',
    'correspondenceID' => $_rowB->res_ReceiversReference,
    'languageID'     => '1'
  );

  $result = $sc->__soapCall('GetCorrespondenceForEndUserSystemsBasicV2', array('parameters' => $params));
  $correspondence = $result->GetCorrespondenceForEndUserSystemsBasicV2Result->Correspondence;
  $correspondenceAttachments = $result->GetCorrespondenceForEndUserSystemsBasicV2Result->CorrespondenceAttachments;
  $values['res_AllowForwarding']          = $correspondence->AllowForwarding;
  $values['res_ArchiveReference']         = $correspondence->ArchiveReference;
  $values['res_AuthenticatedUser']        = $correspondence->AuthenticatedUser;
  $values['res_CaseID']                   = $correspondence->CaseID;
  $values['res_ConfirmationDate']         = $correspondence->ConfirmationDate;
  $values['res_CorrespondenceID']         = $correspondence->CorrespondenceID;
  $values['res_CorrespondenceName']       = $correspondence->CorrespondenceName;
  $values['res_CorrespondenceStatus']     = $correspondence->CorrespondenceStatus;
  $values['res_CorrespondenceSubject']    = $correspondence->CorrespondenceSubject;
  $values['res_CorrespondenceSummary']    = $correspondence->CorrespondenceSummary;
  $values['res_CorrespondenceTitle']      = $correspondence->CorrespondenceTitle;
  $values['res_CorrespondenceTxt']        = $correspondence->CorrespondenceTxt;
  $values['res_CustomMessageData']        = $correspondence->CustomMessageData;
  $values['res_DateSent']                 = $correspondence->DateSent;
  $values['res_Description']              = $correspondence->Description;
  $values['res_DueDate']                  = $correspondence->DueDate;
  $values['res_ExternalSystemReference']  = $correspondence->ExternalSystemReference;
  $values['res_Header']                   = $correspondence->Header;
  $values['res_IsConfirmationNeeded']     = $correspondence->IsConfirmationNeeded;
  $values['res_LanguageID']               = $correspondence->LanguageID;
  $values['res_Reportee']                 = $correspondence->Reportee;
  $values['res_SentBy']                   = $correspondence->SentBy;
  $values['res_SentTo']                   = $correspondence->SentTo;
  $values['res_UserID']                   = $correspondence->UserID;
  $values['res_AttachmentData']           = $correspondenceAttachments->AttachmentBEV2->AttachmentData;
  $values['res_AttachmentFunctionTypeID'] = $correspondenceAttachments->AttachmentBEV2->AttachmentFunctionTypeID;
  $values['res_AttachmentID']             = $correspondenceAttachments->AttachmentBEV2->AttachmentID;
  $values['res_AttachmentName']           = $correspondenceAttachments->AttachmentBEV2->AttachmentName;
  $values['res_AttachmentTypeID']         = $correspondenceAttachments->AttachmentBEV2->AttachmentTypeID;
  $values['res_CreatedByUserID']          = $correspondenceAttachments->AttachmentBEV2->CreatedByUserID;
  $values['res_CreatedDateTime']          = $correspondenceAttachments->AttachmentBEV2->CreatedDateTime;
  $values['res_DestinationType']          = $correspondenceAttachments->AttachmentBEV2->DestinationType;
  $values['res_FileName']                 = $correspondenceAttachments->AttachmentBEV2->FileName;
  $values['res_IsAddedAfterFormFillin']   = $correspondenceAttachments->AttachmentBEV2->IsAddedAfterFormFillin;
  $values['res_IsAssociatedToFormSet']    = $correspondenceAttachments->AttachmentBEV2->IsAssociatedToFormSet;
  $values['res_IsEncrypted']              = $correspondenceAttachments->AttachmentBEV2->IsEncrypted;
  $values['res_ReporteeElementID']        = $correspondenceAttachments->AttachmentBEV2->ReporteeElementID;
  $values['res_SendersReference']         = $correspondenceAttachments->AttachmentBEV2->SendersReference;


  $foo = $correspondenceAttachments->AttachmentBEV2->AttachmentData;
  $bar = new altinn_file($foo);
  $bar->extract();

  $_lib['storage']->store_record(array('table' => 'altinnReport4', 'data' => $values, 'debug' => false));

} elseif($_lib['input']->getProperty('action_soap5')) {
  $query = 'SELECT * FROM altinnReport2 where res_ReceiversReference IS NOT NULL order by AltinnReport2Id desc limit 1';
  $_rowB = $_lib['db']->get_row(array('query' => $query));

  $query = 'SELECT * FROM altinnReport3 order by AltinnReport3Id desc limit 1';
  $_rowC = $_lib['db']->get_row(array('query' => $query));

  $values = array();
  $values["req_correspondenceID"] = $_rowB->ReceiversReference;
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/ServiceEngineExternal/CorrespondenceExternalBasic.svc?wsdl");
  $params = array(
    'systemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
    'systemPassword' => 'altinnlodo0',
    'userSSN'        => $_SETUP['ALTINN_USER_SSN'],
    'userPassword'   => $_SETUP['ALTINN_USER_PASSWORD'],

    'userPinCode'    => $pins[$_rowC->res_KodeNr],
    'authMethod'     => 'AltinnPin',
    'CorrespondenceID' => $_rowB->res_ReceiversReference
  );

  $result = $sc->__soapCall('ArchiveCorrespondenceForEndUserSystemBasic', array('parameters' => $params));
  $_result = $result->ArchiveCorrespondenceForEndUserSystemBasicResult;

  $values['res_LastChanged']       = $_result->LastChanged;
  $values['res_ParentReceiptId']   = $_result->ParentReceiptId;
  $values['res_ReceiptHistory']    = $_result->ReceiptHistory;
  $values['res_ReceiptId']         = $_result->ReceiptId;
  $values['res_ReceiptStatusCode'] = $_result->ReceiptStatusCode;
  $values['res_ReceiptTemplate']   = $_result->ReceiptTemplate;
  $values['res_ReceiptText']       = $_result->ReceiptText;
  $values['res_ReceiptTypeName']   = $_result->ReceiptTypeName;

  for ($i=0; $i < count($_result->References->Reference); $i++) {
    $element = $_result->GetReceiptBasicV2Result->References->Reference[$i];
    $values['res_'.$element->ReferenceType] = $element->ReferenceValue;
  }

  $_lib['storage']->store_record(array('table' => 'altinnReport5', 'data' => $values, 'debug' => false));

}

?>
