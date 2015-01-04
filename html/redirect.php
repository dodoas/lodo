<?php

session_start();

$SITES_LIST = array(
    'http://www.lodo.no',
    'http://www.dodo.no',
    'http://www.fakturabank.no',
    'http://vikingtyping.no',
);

$site_index = rand(0, sizeof($SITES_LIST)-1);
$_SESSION['StartTS'] = time();
//session_unset();
echo header('Location: ' . $SITES_LIST[$site_index]);
?>
