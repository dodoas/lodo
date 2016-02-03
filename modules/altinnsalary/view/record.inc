<?
session_start();
includelogic('altinnsalary/files');
includelogic('altinnsalary/altinnreport');

class LocalSoapClient extends SoapClient {
  function __doRequest($request, $location, $action, $version, $one_way = 0) {
    global $_SETUP;

    // formTaskShipment->FormTasks->Forms->Form->FormData
    // In soap reqest 1 we send one string that contain xml and this have
    // to be valid xml when we send it. The only way I found to do that,
    // is to replace '&lt;' with '<' and '&gt;' with '>'since it get
    // encoded somewhere between I pass the argument to __soapCall()
    // untill this moneypatch
    $request = str_replace('&lt;','<', $request);
    $request = str_replace('&gt;','>', $request);
    // done so we remove some unnecessary characters generated because of the
    // encoding format of the php script file
    $request = str_replace('&#13;','', $request);

    // error_log("Now it is replaced\n", 3, $_SETUP['HOME_DIR']."/error.log");
    // error_log("$request\n", 3, $_SETUP['HOME_DIR']."/error.log");
    return parent::__doRequest($request, $location, $action, $version, $one_way);
  }
}

if (isset($_POST['use_salary'])) {
  $salary_ids = array();
  foreach($_POST['use_salary'] as $salary_id => $selected) {
    if ($selected) $salary_ids[] = $salary_id;
  }
}
else $salary_ids = null;


$error_msg = "";
// Make sure user have set AltinnUserSSN
if ($_lib['input']->getProperty('action_soap3') ||
    $_lib['input']->getProperty('action_soap4') ||
    $_lib['input']->getProperty('action_soap5') ||
    $_lib['input']->getProperty('action_confirm_authentication')){
    $_tmp = $_lib['sess']->get_person('AltinnUserSSN');
    if (empty($_tmp)){
      $error_msg .= "Systembruker: Har ikke satt Personnummer. ";
    }
    unset($_tmp);
}
// Make sure user have set AltinnSystemUsername and AltinnSystemPassword
if ($_lib['input']->getProperty('action_soap1') ||
    $_lib['input']->getProperty('action_soap2') ||
    $_lib['input']->getProperty('action_soap3') ||
    $_lib['input']->getProperty('action_soap4') ||
    $_lib['input']->getProperty('action_soap5') ||
    $_lib['input']->getProperty('action_confirm_authentication')){
    $_tmp = $_lib['sess']->get_companydef('AltinnSystemUsername');
    if (empty($_tmp)){
      $error_msg .= "Firmaopplyning: Firma har ikke registrert Altinnsystemnavn. ";
    }

    $_tmp = $_lib['sess']->get_companydef('AltinnSystemPassword');
    if (empty($_tmp)){
      $error_msg .= "Firmaopplyning: Firma har ikke registrert Altinnsystempassord. ";
    }
}

if (!empty($error_msg)) {
  header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$error_msg);
  // We need to die here otherwise we may be
  // rejected later. Code continue to execute
  // after a header statement.
  die();
}



// if action is soap3 or soap4 or soap5, we need the user password
if ($_lib['input']->getProperty('action_soap3') ||
  $_lib['input']->getProperty('action_soap4') ||
  $_lib['input']->getProperty('action_soap5')) {
  // if pin already entered and not expired use it, if not unset and redirect to enter a new one
  if (!isset($_SESSION['altinn_password']) || (isset($_SESSION['altinn_password']) && (time() > strtotime($_SESSION['altinn_password']['expires_at'])))) {
    // We don't have a valid password
    unset($_SESSION['altinn_password']);
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.confirm_password&action_confirm_password=confirm&request_type=".$_REQUEST['request_type']."&request_receivers_reference=" . $_REQUEST['request_receivers_reference']);
    die();
  }
  else {
    if (!isset($_SESSION['altinn_password']['value'])) {
      // if altinn_password is set but value is not, set the entered password as the value
      $_SESSION['altinn_password']['value'] = $_POST['user_pass_code'];
    }
  }
}




if ($_lib['input']->getProperty('action_soap4') || $_lib['input']->getProperty('action_soap5')) {
  // if pin already entered and not expired use it, if not unset and redirect to enter a new one
  if (!isset($_SESSION['altinn_pin']) || (isset($_SESSION['altinn_pin']) && (time() > strtotime($_SESSION['altinn_pin']['expires_at'])))) {
    unset($_SESSION['altinn_pin']);
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.confirm_authentication&action_confirm_authentication=confirm&request_type=".$_REQUEST['request_type']."&request_receivers_reference=" . $_REQUEST['request_receivers_reference']);
    die();
  }
  else {
    if (!isset($_SESSION['altinn_pin']['value'])) {
      // if altinn_pin is set but value is not, set the entered pin as the value
      $_SESSION['altinn_pin']['value'] = $_POST['user_pin_code'];
    }
  }
}

if ($_lib['input']->getProperty('action_soap1')) {
  // redirect to salary list page if no salaries are selected for the report
  if (empty($salary_ids)) {
    $message = 'No salaries were selected.';
    if (isset($_REQUEST['altinnReport1_periode'])) $periode = '&periode=' . $_REQUEST['altinnReport1_periode'];
    else $periode = '';
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.salarylist&action_show_slaries=show&message=" . $message . $periode);
  }

  // If you didn't select a period, don't send.
  if (empty($_POST['altinnReport1_periode'])){
    $_lib['message']->add('Legg inn: periode');
  } else {
    $period = $_REQUEST['altinnReport1_periode'];
    // generate the altinn report object for the selected period
    $report = new altinn_report($period, $salary_ids);
    if (isset($_POST['altinnReport1_MeldingsId'])) {
      $report->addReplacementMessageID($_POST['altinnReport1_MeldingsId']);
    }
    $xml = $report->generateXML();
    if (!empty($report->errors)) {
      // Message is: Report not sent, there were some errors.
      $_lib['message']->add('Rapporten er ikke sendt, det er noen mangler.');
      foreach($report->errors as $error) $_lib['message']->add($error);
    }
    else {
      $xml_generated = true;

      // here logic should be use old MeldingsID as new erstatterMeldingsId
      $use_erstatterMeldingsId = '';
      if (isset($_POST['altinnReport1_MeldingsId'])) {
        $use_erstatterMeldingsId = '<erstatterMeldingsId>'.$_POST['altinnReport1_MeldingsId'].'</ErstatterMeldingsId>';
        // update the old altinnReport one

        $queryFindOld    = "select AltinnReport1ID from altinnReport1 where MeldingsId='" . $_lib['storage']->db_escape($_POST['altinnReport1_MeldingsId'])."';";
        $record_exist   = $_lib['storage']->get_row(array('query' => $queryFindOld));
        if($record_exist) {
          $_lib['storage']->db_update_hash(
            array('altinnReport1_ReplacedByMeldindsID' => $report->meldingsId),
            'altinnReport1',
            array('AltinnReport1ID' => $record_exist->AltinnReport1ID));

        }
      }
      $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/IntermediaryExternal/IntermediaryInboundBasic.svc?wsdl", array('trace' => 1, 'encoding'=>'UTF-8'));
      $params = array(
        'systemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
        'systemPassword' => $_lib['sess']->get_companydef('AltinnSystemPassword'),
        'formTaskShipment' => array(
          'Reportee' => preg_replace('/\s+/', '', $_lib['sess']->get_companydef('OrgNumber')),
          'ExternalShipmentReference' => 'LODO' . time(),
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
                        <meldingsId>'.$report->meldingsId.'</meldingsId>
                        '.$use_erstatterMeldingsId.'
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
              'AttachementData' => $xml
            )
          )
        )
      );

      try {
        $result = $sc->__soapCall('SubmitFormTaskBasic', array('parameters' => $params));
      }
      catch (SoapFault $sf) {
        header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
        die();
      }

      $values = array();
      $values['Period']  = $_POST['altinnReport1_periode'];
      $values['MeldingsId']  = $report->meldingsId;
      $values['ErstatterMeldingsId']  = $_POST['altinnReport1_MeldingsId'];
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

      $values['Folder'] = strftime('%Y%m%d', time());


      $_lib['storage']->store_record(array('table' => 'altinnReport1', 'data' => $values, 'debug' => false));

      // to fetch the AltinnReport1ID to the one I just saved
      $query = 'SELECT AltinnReport1ID FROM altinnReport1 order by AltinnReport1ID desc limit 1';
      $res = $_lib['db']->db_query($query);
      $row = $_lib['db']->db_fetch_object($res);
      $altinnReport1ID = $row->AltinnReport1ID;

      // Save which salaries have been sent in the report and notify
      if ($report->saveSalaryReportLinks($altinnReport1ID)) $_lib['message']->add('Rapport sendt');

      // Save xml we sent
      $altinnFile = new altinn_file($values['Folder']);
      $altinnFile->save($xml, $altinnReport1ID);
    }
  }
} elseif($_lib['input']->getProperty('action_soap2')) {
  if(empty($_POST['receiptId'])){
    $_lib['message']->add('Noe gikk galt, fikk ikke receiptId som vi skulle');
  } else {
    $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/IntermediaryExternal/ReceiptExternalBasic.svc?wsdl");
    $params = array(
      'systemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
      'systemPassword' => $_lib['sess']->get_companydef('AltinnSystemPassword'),
      'receipt' => array(
        'ReceiptId' => $_POST['receiptId'],
      )
    );
    try {
      $result = $sc->__soapCall('GetReceiptBasicV2', array('parameters' => $params));
    }
    catch (SoapFault $sf) {
      header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
      die();
    }

    $values = array();
    $values['req_ReceiptId']       = $_POST['receiptId'];
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
        if (!isset($values['res_'.$element->ReferenceType.'A'])) {
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
  }
} elseif($_lib['input']->getProperty('action_soap3')) {
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/AuthenticationExternal/SystemAuthentication.svc?wsdl");
  $params = array(
    'challengeRequest' => array(
      'AuthMethod' => $_lib['sess']->get_person('AltinnAuthMethod'),
      'SystemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
      'UserPassword'   => $_SESSION['altinn_password']['value'],
      'UserSSN'        => $_lib['sess']->get_person('AltinnUserSSN')
    )
  );

  try {
    $result = $sc->__soapCall('GetAuthenticationChallenge', array('parameters' => $params));
  }
  catch (SoapFault $sf) {
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
    die();
  }

  $values = array();
  $values['res_Message']   = $result->GetAuthenticationChallengeResult->Message;
  $_parseMessage           = explode(' ', $values['res_Message']);
  $values['res_KodeNr']    = $_parseMessage[2];
  $values['res_Status']    = $result->GetAuthenticationChallengeResult->Status;
  $values['res_ValidFrom'] = $result->GetAuthenticationChallengeResult->ValidFrom;
  $values['res_ValidTo']   = $result->GetAuthenticationChallengeResult->ValidTo;

  $_lib['storage']->store_record(array('table' => 'altinnReport3', 'data' => $values, 'debug' => false));
} elseif($_lib['input']->getProperty('action_delete_pin')) {
  $_lib['message']->add('Pin deleted');
  unset($_SESSION['altinn_pin']);
} elseif($_lib['input']->getProperty('action_expire_pin')) {
  $_lib['message']->add('Pin expired');
  $_SESSION['altinn_pin']['expires_at'] = strftime("%F %T", time()-1);
} elseif($_lib['input']->getProperty('action_delete_password')) {
  $_lib['message']->add('Password deleted');
  unset($_SESSION['altinn_password']);
} elseif($_lib['input']->getProperty('action_expire_password')) {
  $_lib['message']->add('Password expired');
  $_SESSION['altinn_password']['expires_at'] = strftime("%F %T", time()-1);
} elseif($_lib['input']->getProperty('action_soap4')) {
  // $query = 'SELECT * FROM altinnReport2 where res_ReceiversReference IS NOT NULL order by AltinnReport2Id desc limit 1';
  // $_rowB = $_lib['db']->get_row(array('query' => $query));

  // $query = 'SELECT * FROM altinnReport3 order by AltinnReport3Id desc limit 1';
  // $_rowC = $_lib['db']->get_row(array('query' => $query));

  $values = array();
  $values["req_CorrespondenceID"] = $_POST['request_receivers_reference'];
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/ServiceEngineExternal/CorrespondenceExternalBasic.svc?wsdl", array('trace' => 1, 'encoding'=>'UTF-8'));
  $params = array(
    'systemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
    'systemPassword' => $_lib['sess']->get_companydef('AltinnSystemPassword'),
    'userSSN'        => $_lib['sess']->get_person('AltinnUserSSN'),
    'userPassword'   => $_SESSION['altinn_password']['value'],

    'userPinCode'    => $_SESSION['altinn_pin']['value'],
    'authMethod'     => $_lib['sess']->get_person('AltinnAuthMethod'),
    'correspondenceID' => $_POST['request_receivers_reference'],
    'languageID'     => '1'
  );

  try {
    $result = $sc->__soapCall('GetCorrespondenceForEndUserSystemsBasicV2', array('parameters' => $params));
  }
  catch (SoapFault $sf) {
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
    die();
  }
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

  $values['Folder'] = strftime('%Y%m%d', time());
  $zipContent = $correspondenceAttachments->AttachmentBEV2->AttachmentData;


  // $_GET['AltinnReport4ID'] = 1;
  $_GET['AltinnReport4ID'] = $_lib['storage']->store_record(array('table' => 'altinnReport4', 'data' => $values, 'debug' => false));


  // to fetch the AltinnReport4ID to the one I just saved
  $query = 'SELECT AltinnReport4ID FROM altinnReport4 order by AltinnReport4ID desc limit 1';
  $res = $_lib['db']->db_query($query);
  $row = $_lib['db']->db_fetch_object($res);
  $altinnReport4ID = $row->AltinnReport4ID;
  $altinnFile = new altinn_file($values['Folder']);
  $altinnFile->extract($zipContent, $altinnReport4ID);
} elseif($_lib['input']->getProperty('action_soap5')) {
  // $query = 'SELECT * FROM altinnReport2 where res_ReceiversReference IS NOT NULL order by AltinnReport2Id desc limit 1';
  // $_rowB = $_lib['db']->get_row(array('query' => $query));

  // $query = 'SELECT * FROM altinnReport3 order by AltinnReport3Id desc limit 1';
  // $_rowC = $_lib['db']->get_row(array('query' => $query));

  $values = array();
  $values['req_CorrespondenceID'] = $_POST['request_receivers_reference'];
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/ServiceEngineExternal/CorrespondenceExternalBasic.svc?wsdl");
  $params = array(
    'systemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
    'systemPassword' => $_lib['sess']->get_companydef('AltinnSystemPassword'),
    'userSSN'        => $_lib['sess']->get_person('AltinnUserSSN'),
    'userPassword'   => $_SESSION['altinn_password']['value'],

    'userPinCode'    => $_SESSION['altinn_pin']['value'],
    'authMethod'     => $_lib['sess']->get_person('AltinnAuthMethod'),
    'CorrespondenceID' => $_POST['request_receivers_reference']
  );

  try {
    $result = $sc->__soapCall('ArchiveCorrespondenceForEndUserSystemBasic', array('parameters' => $params));
  }
  catch (SoapFault $sf) {
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
    die();
  }
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
    $element = $_result->References->Reference[$i];
    $values['res_'.$element->ReferenceTypeName] = $element->ReferenceValue;
  }

  $_lib['storage']->store_record(array('table' => 'altinnReport5', 'data' => $values, 'debug' => false));

  $_lib['message']->add('Report Archived');

} elseif($_lib['input']->getProperty('action_generate_xml_report')) {
  $period = $_REQUEST['altinnReport1_periode'];
  // generate the altinn report object for the selected period
  $report = new altinn_report($period, $salary_ids);
  $xml_generated = true;
} elseif($_lib['input']->getProperty('action_confirm_authentication')) {
  $sc = new LocalSoapClient($_SETUP['ALTINN_URL'] . "/AuthenticationExternal/SystemAuthentication.svc?wsdl");
  $params = array(
    'challengeRequest' => array(
      'AuthMethod' => $_lib['sess']->get_person('AltinnAuthMethod'),
      'SystemUserName' => $_lib['sess']->get_companydef('AltinnSystemUsername'),
      'UserPassword'   => $_SESSION['altinn_password']['value'],
      'UserSSN'        => $_lib['sess']->get_person('AltinnUserSSN')
    )
  );

  try {
    $result = $sc->__soapCall('GetAuthenticationChallenge', array('parameters' => $params));
  }
  catch (SoapFault $sf) {
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.list&message=".$sf->detail->AltinnFault->AltinnErrorMessage." ".$sf->detail->AltinnFault->AltinnExtendedErrorMessage);
    die();
  }
  $values = array();
  $values['res_Message']   = $result->GetAuthenticationChallengeResult->Message;
  $_parseMessage           = explode(' ', $values['res_Message']);
  $values['res_KodeNr']    = $_parseMessage[2];
  $values['res_Status']    = $result->GetAuthenticationChallengeResult->Status;
  $values['res_ValidFrom'] = preg_replace('/\+.*/', '', $result->GetAuthenticationChallengeResult->ValidFrom);
  $values['res_ValidTo']   = preg_replace('/\+.*/', '', $result->GetAuthenticationChallengeResult->ValidTo);

  $_lib['storage']->store_record(array('table' => 'altinnReport3', 'data' => $values, 'debug' => false));

  // Back to password authentication since the wrong one was entered
  if ($values['res_Status'] == 'InvalidCredentials') {
    unset($_SESSION['altinn_password']);
    $error_message = $rowC->res_Message;
    header('Location: ' . $_lib['sess']->dispatchs . "t=altinnsalary.confirm_password&action_confirm_password=confirm&message=".$error_message."&request_type=".$_REQUEST['request_type']."&request_receivers_reference=" . $_REQUEST['request_receivers_reference']);
    die();
  }

  // save all info for altinn pin in the session
  // except the value, that will get set in soap4 or soap5
  $_SESSION['altinn_pin']['type'] = $params['challengeRequest']['AuthMethod'];
  $_SESSION['altinn_pin']['created_at'] = strftime('%F %T', strtotime($values['res_ValidFrom']));
  $_SESSION['altinn_pin']['expires_at'] = strftime('%F %T', strtotime($values['res_ValidTo']));
  $authentication_challenge_message = $values['res_Message'];
} elseif($_lib['input']->getProperty('action_confirm_password')) {
  // save all info for altinn userpassord in the session
  $_SESSION['altinn_password']['expires_at'] = strftime('%F %T', time() + 60*30);
} elseif ($_lib['input']->getProperty('action_show_slaries')) {
  $query_salary   = "SELECT s.AmountThisPeriod, s.JournalID, s.ValidFrom as FromDate, s.ValidTo as ToDate, a.AccountPlanID, a.AccountName, s.PayDate, s.DomesticBankAccount, s.TS, s.SalaryID, s.JournalDate, s.Period
                     FROM salary s, accountplan a
                     WHERE s.AccountPlanID = a.AccountPlanID AND ActualPayDate LIKE  '" . $_periode . "%'
                     ORDER BY s.JournalID DESC";
  $result_salary  = $_lib['db']->db_query($query_salary);
}

?>
