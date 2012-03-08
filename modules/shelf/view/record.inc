<?php

$new_name = "shelfname";
$new_submit = "shelfnew";

$update_name = "shelfname";
$update_active = "shelfactive";
$update_submit = "shelfupdate";

if(isset($_POST[$new_submit])) {
    $shelfs->create($_POST[$new_name]);
}
else if(isset($_POST[$update_submit])) {
    $ShelfID = (int)$_GET['ShelfID'];
    $name = $_POST[$update_name];
    $active = isset($_POST[$update_active]) ? 1 : 0;
    
    $shelfs->update($ShelfID, $name, $active);
}

?>