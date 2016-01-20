<?
includelogic('altinnsalary/files');
includelogic('altinnsalary/altinnreport');

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
    $period = $_REQUEST['altinnReport1_periode'];
    // generate the altinn report object for the selected period
    $report = new altinn_report($period);
    // TODO: Add error checking, after it is implemented in report class
    $xml = $report->generateXML();
    $xml_generated = true;
/*
'<?xml version="1.0" encoding="UTF-8"?>
<melding xmlns="urn:ske:fastsetting:innsamling:a-meldingen:v2_0">
  <Leveranse>
    <leveringstidspunkt>2015-12-11T00:00:00Z</leveringstidspunkt>
    <kalendermaaned>2015-12</kalendermaaned>
    <kildesystem>LODO</kildesystem>
    <meldingsId>108</meldingsId>
    <opplysningspliktig>
      <norskIdentifikator>910748645</norskIdentifikator>
    </opplysningspliktig>
    <oppgave>
      <betalingsinformasjon>
        <sumForskuddstrekk>3000</sumForskuddstrekk>
        <sumArbeidsgiveravgift>1410</sumArbeidsgiveravgift>
      </betalingsinformasjon>
      <virksomhet>
        <norskIdentifikator>910748645</norskIdentifikator>
        <inntektsmottaker>
          <norskIdentifikator>24040100918</norskIdentifikator>
          <identifiserendeInformasjon>
            <navn>MARTIN EIDEM</navn>
            <foedselsdato>2001-04-24</foedselsdato>
          </identifiserendeInformasjon>
          <arbeidsforhold>
            <typeArbeidsforhold>ordinaertArbeidsforhold</typeArbeidsforhold>
            <startdato>2013-11-01</startdato>
            <antallTimerPerUkeSomEnFullStillingTilsvarer>37.5</antallTimerPerUkeSomEnFullStillingTilsvarer>
            <avloenningstype>fast</avloenningstype>
            <yrke>2130109</yrke>
            <arbeidstidsordning>ikkeSkift</arbeidstidsordning>
            <stillingsprosent>100</stillingsprosent>
            <sisteLoennsendringsdato>2013-11-01</sisteLoennsendringsdato>
            <loennsansiennitet>2013-11-01</loennsansiennitet>
            <sisteDatoForStillingsprosentendring>2013-11-01</sisteDatoForStillingsprosentendring>
          </arbeidsforhold>
          <forskuddstrekk>
            <beloep>-3000</beloep>
          </forskuddstrekk>
          <inntekt>
            <fordel>kontantytelse</fordel>
            <utloeserArbeidsgiveravgift>true</utloeserArbeidsgiveravgift>
            <inngaarIGrunnlagForTrekk>true</inngaarIGrunnlagForTrekk>
            <beloep>10000</beloep>
            <loennsinntekt>
              <beskrivelse>fastloenn</beskrivelse>
            </loennsinntekt>
          </inntekt>
        </inntektsmottaker>
        <arbeidsgiveravgift>
          <loennOgGodtgjoerelse>
            <beregningskodeForArbeidsgiveravgift>generelleNaeringer</beregningskodeForArbeidsgiveravgift>
            <sone>1</sone>
            <avgiftsgrunnlagBeloep>10000</avgiftsgrunnlagBeloep>
            <prosentsatsForAvgiftsberegning>14.1</prosentsatsForAvgiftsberegning>
          </loennOgGodtgjoerelse>
        </arbeidsgiveravgift>
      </virksomhet>
    </oppgave>
  </Leveranse>
</melding>
';
*/
    // here logic should be use old MeldingsID as new erstatterMeldingsID
    $use_erstatterMeldingsId = '';
    if (isset($_POST['altinnReport1_MeldingsId'])) {
      $use_erstatterMeldingsId = '<erstatterMeldingsID>'.$_POST['altinnReport1_MeldingsId'].'</ErstatterMeldingsID>';
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

    $result = $sc->__soapCall('SubmitFormTaskBasic', array('parameters' => $params));

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

    $values['Folder'] = (string) time();;
    $altinnFile = new altinn_file($values['Folder']);
    $altinnFile->save($xml);


    $_lib['storage']->store_record(array('table' => 'altinnReport1', 'data' => $values, 'debug' => false));

    // to fetch the AltinnReport1ID to the one I just saved
    $query = 'SELECT AltinnReport1ID FROM altinnReport1 order by AltinnReport1ID desc limit 1';
    $res = $_lib['db']->db_query($query);
    $row = $_lib['db']->db_fetch_object($res);

    $query_salary  = "SELECT SalaryID, JournalID FROM salary WHERE ActualPayDate LIKE  '" . $_REQUEST['altinnReport1_periode'] . "%'";
    $result_salary = $_lib['db']->db_query($query_salary);
    $insert_query = 'INSERT INTO altinnReport1salary (AltinnReport1ID, SalaryId, JournalID) VALUES ';
    while($_row = $_lib['db']->db_fetch_object($result_salary)){
      $insert_query .= "('".$row->AltinnReport1ID."', '".$_row->SalaryID."', '".$_row->JournalID."'),";
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
  $values["req_CorrespondenceID"] = $_rowB->res_ReceiversReference;
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

  $values['Folder'] = (string) time();;
  $zipContent = $correspondenceAttachments->AttachmentBEV2->AttachmentData;

  $altinnFile = new altinn_file($values['Folder']);
  $altinnFile->extract($zipContent);

  $_lib['storage']->store_record(array('table' => 'altinnReport4', 'data' => $values, 'debug' => false));

} elseif($_lib['input']->getProperty('action_soap5')) {
  $query = 'SELECT * FROM altinnReport2 where res_ReceiversReference IS NOT NULL order by AltinnReport2Id desc limit 1';
  $_rowB = $_lib['db']->get_row(array('query' => $query));

  $query = 'SELECT * FROM altinnReport3 order by AltinnReport3Id desc limit 1';
  $_rowC = $_lib['db']->get_row(array('query' => $query));

  $values = array();
  $values['req_CorrespondenceID'] = $_rowB->res_ReceiversReference;
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

} elseif($_lib['input']->getProperty('action_generate_xml_report')) {
  $period = $_REQUEST['altinnReport1_periode'];
  // generate the altinn report object for the selected period
  $report = new altinn_report($period);
  $xml_generated = true;
}

?>
