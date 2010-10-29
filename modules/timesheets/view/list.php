<?php

includelogic('timesheets/timesheet');

$id = $_GET['AccountPlanID'];
$timesheet_username = $_GET['Username'];
$timesheet_user = new timesheet_user($timesheet_username, "", $id);

print $_lib['sess']->doctype;
?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>

<? print $_lib['message']->get(); ?>

<?
$page = new timesheet_user_page($timesheet_user);
$page->set_root("/lodo.php?SID=" . $_REQUEST['SID'] . "&t=timesheets.list&AccountPlanID=" . $id . "&Username=" . $timesheet_username);
$page->pageswitch($_GET['tp']);