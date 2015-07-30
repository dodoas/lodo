<?
// Used to redirect to the real callback page in LODO
// Need to start session here or we lose redirect url and data
session_start();
header("Location: /lodo.php?t=oauth.callback&" . http_build_query($_GET));
?>
