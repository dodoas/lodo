<?php

$selected_year = $_GET['year'];
$selected_config = $_GET['config'];
$filename = "export$selected_year.wsarr";

includelogic('weeklysale/weeklysaletemplate');
$template = new WeeklysaleTemplate($selected_year, $selected_config);

header("Content-type: text/csv");
header("Content-Disposition: attachment;filename=$filename");
header("Content-Transfer-Encoding: binary");
header('Pragma: no-cache');
header('Expires: 0');

set_time_limit(0);
echo $template->exportSerialized();