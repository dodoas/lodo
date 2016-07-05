<?

includelogic('kommune/kommune');
$kommune = new kommune();

if($_lib['input']->getProperty('action_kommune_import')) {
  $kommune_number_to_import = (string) $_lib['input']->getProperty('kommune_Import');
  if (!empty($kommune_number_to_import)) {
    $kommune->import($kommune_number_to_import);
    $kommune->save();
  }
}
?>
