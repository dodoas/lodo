<?php
/* View for testing oauth requests */

// Populate $_SESSION superglobal and create a new OAuth client
session_start();
includelogic("oauth/oauth");
$oauth_client = new lodo_oauth();

// Delete or make token expired only if the oauth_token is saved in session
if (isset($_SESSION['oauth_token'])) {
  if (isset($_REQUEST['expire_token'])) {
    $_SESSION['oauth_token']['expires_in'] = 0;
  }
  if (isset($_REQUEST['delete_token'])) {
    unset($_SESSION['oauth_token']);
  }
}

// If we get either a get or a post action save action,
// redirect url and parameters we get
if (isset($_REQUEST['get']) || isset($_REQUEST['post'])) {
  $_SESSION['oauth_action'] = 'test';
  $_SESSION['oauth_tmp_redirect_back_url'] = "$_SETUP[OAUTH_PROTOCOL]://$_SERVER[HTTP_HOST]?t=oauth.test_oauth&info=show";
  $params_json = json_decode($_REQUEST['params'], true);
}

// Send request
if (isset($_REQUEST['get'])) {
  $resource = $oauth_client->get_resources($_REQUEST['url'] . '?' . http_build_query($params_json));
}
elseif (isset($_REQUEST['post'])) {
  $resource = $oauth_client->post_resources($_REQUEST['url'], $params_json);
}

// Form for sending OAuth requests
// We retain the input in both url and params fields
?>
<h3>Test sending OAuth reqests</h3>
<form method="get">
  <h5>URL:</h5>
  <input type="text" name="url" size="40" value="<?php if (isset($_REQUEST['url'])) echo $_REQUEST['url']; ?>"/>
  <h5>PARAMS:</h5>
  <textarea name="params" rows="10" cols="40">
<?php
if (isset($_REQUEST['params'])) echo $_REQUEST['params'];
else echo "{\n\"param1\": \"value\",\n\"param2\": \"other value\"\n}\n";
?>
  </textarea><br/><br/>
  <input type="submit" name="post" value="POST/Create"/>
  <input type="submit" name="get" value="GET/Fetch"/><br/><br/>
  <input type="hidden" name="t" value="oauth.test_oauth"/>
  <input type="hidden" name="info" value="show"/>
</form>
<h5>REQUEST RESPONSE INFO:</h5>
<div>
<?php
// Print response
if (isset($_REQUEST['info'])) {
  foreach($_SESSION['oauth_resource'] as $key => $value) {
    echo "<h5>$key</h5><textarea rows='5' cols='40'>$value</textarea>";
  }
}
else {
  echo "No info.";
}

// Token info and actions form
if (isset($_SESSION['oauth_token'])) {
?>
  <h5>OAUTH TOKEN INFO:</h5>
  <form method="get">
    <table border="1">
      <tr><th>access_token</th><td><?php echo $_SESSION['oauth_token']['access_token']; ?></td></tr>
      <tr><th>token_type</th><td><?php echo $_SESSION['oauth_token']['token_type']; ?></td></tr>
      <tr><th>created_at</th><td><?php echo strftime("%F %T", $_SESSION['oauth_token']['created_at']); ?></td></tr>
      <tr><th>expires_at</th><td><?php echo strftime("%F %T", $_SESSION['oauth_token']['created_at']+$_SESSION['oauth_token']['expires_in']); ?></td></tr>
    </table>
    <br/>
    <input type="submit" name="expire_token" value="Expire OAuth token"/>
    <input type="submit" name="delete_token" value="Delete Oauth token"/>
    <input type="hidden" name="t" value="oauth.test_oauth"/>
    <input type="hidden" name="info" value="show"/>
  </form>
<?php
}
?>
</div>
