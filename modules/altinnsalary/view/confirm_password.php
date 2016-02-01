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
Skriv inn passord for &aring; logge inn i altinn

<? var_dump($_REQUEST['request_type']) ?>
<? $target_page = $_REQUEST['request_type'] == 'feedback' ? 'altinnsalary.show4' : 'altinnsalary.list'?>
<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=<? print $target_page ?>" method="post">
  <input type="hidden" name="request_receivers_reference" value='<?print $_REQUEST['request_receivers_reference']; ?>'>
  <input type="hidden" name="request_type" value='<?print $_REQUEST['request_type']; ?>'>
  <? print $_lib['form3']->input(array('name'=>'user_pass_code', 'type'=>'password','value'=>'')) ?>
    <?
    if($_REQUEST['request_type'] == 'feedback'){
      print $_lib['form3']->submit(array('name'=>'action_soap4', 'value'=>'Get Feedback'));
    }elseif($_REQUEST['request_type'] == 'archive'){
      print $_lib['form3']->submit(array('name'=>'action_soap5', 'value'=>'Archive Report'));
    }?>
</form>

</body>
</html>
