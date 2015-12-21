<?
require_once "record.inc";
print $_lib['sess']->doctype
?>

<head>
  <title>Empatix - Altinn lønnslipper</title>
  <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<? print $_lib['message']->get() ?>

<form name="altinnsalary_search" action="<? print $_lib['sess']->dispatch ?>t=altinnsalary.list" method="post">
  <? print $_lib['form3']->submit(array('name'=>'action_soap1', 'value'=>'Test Soap1')) ?>
  <? print $_lib['form3']->submit(array('name'=>'action_soap2', 'value'=>'Test Soap2')) ?>
  <? print $_lib['form3']->submit(array('name'=>'action_soap3', 'value'=>'Test Soap3')) ?>
  <? print $_lib['form3']->submit(array('name'=>'action_soap4', 'value'=>'Test Soap4')) ?>
  <? print $_lib['form3']->submit(array('name'=>'action_soap5', 'value'=>'Test Soap5')) ?>
</form>

<pre>
  <? var_dump($result) ?>
</pre>

</body>
</html>


