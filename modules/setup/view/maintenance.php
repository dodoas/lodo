<?php
if ($_lib['sess']->get_person('AccessLevel') < 4) {
  header('Location: ' . $_lib['sess']->dispatchs . "t=lodo.main");
  die();
}
?>
<h3>Maintenance page:</h3>

<ul>
  <li><a href="<? print $_lib['sess']->dispatchs . "t=oauth.test_oauth"; ?>">Test OAuth</a></li>
  <li><a href="<? print $_lib['sess']->dispatchs . "t=migration.list"; ?>">Migrations</a></li>
  <li><a href="<? print $_lib['sess']->dispatchs . "t=altinnsalary.altinn1list"; ?>">Altinn XML DEBUG</a></li>
</ul>
