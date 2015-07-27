<?
// Used to redirect to the real callback page in LODO
// TODO: check if we lose any post params!
header("Location: /lodo.php?t=oauth.callback&" . http_build_query($_GET));
?>
