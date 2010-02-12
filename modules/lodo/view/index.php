<?php

session_start();
session_unset();
session_write_close();

if($_REQUEST['page']) {
    $page = $_REQUEST['page'];
} else {
    $page = 'index';
}
includeinc('topp');
?>
  <div id="side-a">
    <?php
        include ('kunder.php');
    ?>
  </div>
  <div id="innhold">
  </div>
<?php
    includeinc ('bunn');
?>