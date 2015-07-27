<?php
/*
 * OAuth 2.0:
 * OAuth client for easy use in LODO
 *
 */

// so we can have access to $_SESSION variable
session_start();

// include required oauth stuff
require('Client.php');
require('GrantType/IGrantType.php');
require('GrantType/AuthorizationCode.php');

class lodo_oauth {
  // client_id, client_secret and callback_url are loaded from config on create
  var $client_id      = '';
  var $client_secret  = '';
  var $callback_url   = '';

  var $client         = NULL;
  var $user_agent     = 'LODO';

  // the oauth provider target
  var $protocol         = 'http';
  var $host             = '192.168.0.42:3000';
  var $authorize_url    = '/oauth/authorize';
  var $access_token_url = '/oauth/token';

  function __construct() {
    // so we can get oauth app config params
    global $_SETUP;
    
    // load redirect_url, client_id and client_secret from config
    $this->callback_url   = $_SETUP['OAUTH_REDIRECT_URL'];
    $this->client_id      = $_SETUP['OAUTH_CLIENT_ID'];
    $this->client_secret  = $_SETUP['OAUTH_CLIENT_SECRET'];

    // create client
    $this->client = new OAuth2\Client($this->client_id, $this->client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
    $this->client->setCurlOption(CURLOPT_USERAGENT, $this->user_agent);
  }

  /**
   * Generates a url to whitch we can add routes for specific resources
   */
  function generate_server_url() {
    return $this->protocol . "://" . $this->host;
  }

  // TODO: change the url parameter to be built inside the method using 
  // generate_server_url method
  /**
   * Post resources to the server
   */
  function post_resources($url, $params = false, $code = false, $http_verb = OAuth2\Client::HTTP_METHOD_POST) {
    return $this->do_resources($url, $params, $code, $http_verb);
  }
  /**
   * Fetch resources from the server
   */
  function get_resources($url, $params = false, $code = false, $http_verb = OAuth2\Client::HTTP_METHOD_GET) {
    return $this->do_resources($url, $params, $code, $http_verb);
  }
  /**
   * Do resource action, either GET or POST
   * TODO: Maybe add delete, patch?
   */
  function do_resources($url, $params = false, $code = false, $http_verb) {
    // if no code, get the code
    if (!$code) {
      // save verb, url and params so we have them when we get back the code
      $_SESSION['oauth_resource_url'] = $url;
      $_SESSION['oauth_http_verb'] = ($http_verb == OAuth2\Client::HTTP_METHOD_GET)?"GET":"POST";
      if ($params) $_SESSION['oauth_resource_params'] = $params;
      // get code
      $authorize_url = $this->client->getAuthenticationUrl($this->generate_server_url() . $this->authorize_url, $this->callback_url);
      header("Location: " . $authorize_url);
    }
    // get token and do the request
    else {
      $token_params = array("code" => $_GET["code"], "redirect_uri" => $this->callback_url);
      $response = $this->client->getAccessToken($this->generate_server_url() . $this->access_token_url, "authorization_code", $token_params);

      $access_token_result = $response["result"];
      $this->client->setAccessToken($access_token_result["access_token"]);
      $this->client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);

      $response = $this->client->fetch($url, $params, $http_verb);
      // unset the resources we do not need anymore
      unset($_SESSION['oauth_resource_url']);
      unset($_SESSION['oauth_resource_params']);
      unset($_SESSION['oauth_http_verb']);
      // save complete response for future use
      $_SESSION['oauth_resource'] = $response;
      return $response['result'];
    }
  }
  
};

?>
