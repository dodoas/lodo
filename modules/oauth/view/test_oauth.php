<?php
// temporary file for testing oauth requests
includelogic("oauth/oauth");
$oauth_client = new lodo_oauth();
if (isset($_REQUEST['fetch'])) {
  $resource = $oauth_client->get_resources($_REQUEST['url']);
}
elseif (isset($_REQUEST['create'])) {
  $params = $_REQUEST; 
  $resource = $oauth_client->post_resources($_REQUEST['url'], $params);
}
?>
<form method="get">
URL:<br/>
<input type="text" name="url"/><br/>
<input type="submit" name="create" value="Create"/>
<input type="submit" name="fetch" value="Fetch"/>
<input type="hidden" name="t" value="oauth.test_oauth"/>
</form>
