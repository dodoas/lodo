<?
if ($_lib['input']->getProperty('action_soap1')) {
} elseif($_lib['input']->getProperty('action_soap2')) {
} elseif($_lib['input']->getProperty('action_soap3')) {
  $sc = new SoapClient($_SETUP['ALTINN_URL'] . "/AuthenticationExternal/SystemAuthentication.svc?wsdl");
  $params = array(
    'challengeRequest' => array(
      'AuthMethod' => 'AltinnPin',
      'SystemUserName' => $_SETUP['ALTINN_SYSTEM_USER_NAME'],
      'UserPassword'   => $_SETUP['ALTINN_USER_PASSWORD'],
      'UserSSN'        => $_SETUP['ALTINN_USER_SSN']
    )
  );

  $result = $sc->__soapCall('GetAuthenticationChallenge', array('parameters' => $params));
} elseif($_lib['input']->getProperty('action_soap4')) {
} elseif($_lib['input']->getProperty('action_soap5')) {
}

?>
