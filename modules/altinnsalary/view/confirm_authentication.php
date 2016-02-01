<?
require_once "record.inc";
print $_lib['sess']->doctype
?>

<head>
  <title>Empatix - Altinn lønnslipper</title>
  <? includeinc('head') ?>
</head>
<body>
<? print $_lib['message']->get() ?>
<? print $authentication_challenge_message ?>
<? $target_page = $_REQUEST['request_type'] == 'feedback' ? 'altinnsalary.show4' : 'altinnsalary.list'?>
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=<? print $target_page ?>" method="post">
  <input type="hidden" name="request_receivers_reference" value='<?print $_REQUEST['request_receivers_reference']; ?>'>
  <? print $_lib['form3']->input(array('name'=>'user_pin_code', 'type'=>'password', 'width'=>'10' , 'value'=>'')) ?>
    <?
    if($_REQUEST['request_type'] == 'feedback'){
        print $_lib['form3']->submit(array('name'=>'action_soap4', 'value'=>'Get Feedback'));
    }elseif($_REQUEST['request_type'] == 'archive'){
        print $_lib['form3']->submit(array('name'=>'action_soap5', 'value'=>'Archive Report'));
    }
    ?>
</form>

</body>
</html>
