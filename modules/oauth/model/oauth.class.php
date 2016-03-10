<?php
/*
 * OAuth 2.0:
 * OAuth client for easy use in LODO
 *
 */

// so we can have access to $_SESSION variable
session_start();

// include required oauth stuff
// code from https://github.com/adoy/PHP-OAuth2
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
  var $host             = 'fakturabank.no';
  var $authorize_url    = '/oauth/authorize';
  var $access_token_url = '/oauth/token';

  function __construct() {
    // so we can get oauth app config params
    global $_SETUP;
    
    // load redirect_url, client_id and client_secret from config
    $this->callback_url   = $_SETUP['OAUTH_REDIRECT_URL'];
    $this->client_id      = $_SETUP['OAUTH_CLIENT_ID'];
    $this->client_secret  = $_SETUP['OAUTH_CLIENT_SECRET'];
    $this->protocol       = $_SETUP['FB_SERVER_PROTOCOL'];
    $this->host           = $_SETUP['FB_SERVER'];

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
    // save url, verb and params
    $_SESSION['oauth_resource_url'] = $url;
    $_SESSION['oauth_http_verb'] = ($http_verb == OAuth2\Client::HTTP_METHOD_GET)?"GET":"POST";
    if ($params) $_SESSION['oauth_resource_params'] = $params;
    // if we have token saved in session and it has not expired yet, use it
    if (!self::isTokenExpired()) {
      $this->client->setAccessToken($_SESSION['oauth_token']['access_token']);
      $this->client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);
    }
    // if no token and no code, start authentication request
    elseif (!$code) {
      // get code
      $authorize_url = $this->client->getAuthenticationUrl($this->generate_server_url() . $this->authorize_url, $this->callback_url);
      header("Location: " . $authorize_url);
      die();
    }
    // if we have the code, get the token
    else {
      $token_params = array("code" => $code, "redirect_uri" => $this->callback_url);
      $response = $this->client->getAccessToken($this->generate_server_url() . $this->access_token_url, "authorization_code", $token_params);

      $access_token_result = $response["result"];
      $_SESSION['oauth_token'] = $access_token_result;
      $this->client->setAccessToken($access_token_result["access_token"]);
      $this->client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_BEARER);
    }
    // send the request if the access token is set
    if ($this->client->hasAccessToken()) {
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

  function isTokenExpired() {
    return !(isset($_SESSION['oauth_token']) && $_SESSION['oauth_token']['created_at']+$_SESSION['oauth_token']['expires_in'] > time());
  }
  
};

?>
