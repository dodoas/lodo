<?php
/**
 * Landing page that logs-out user and redirects him to another page
 *
 * @package redirect
 * @author Milan Jovanovic <milan@novarepublika.com>
 * @version 0.1
 * @copyright (C) 2014 Milan Jovanovic <milan@novarepublika.com>
 * @license MIT
 */

$SITES_LIST = array(
    'http://www.lodo.no',
    'http://www.dodo.no',
    'http://www.fakturabank.no',
    'http://vikingtyping.no',
);

$site_index = rand(0, sizeof($SITES_LIST));
session_destroy();
echo header('Location: ' . $SITES_LIST[$site_index]);
?>
