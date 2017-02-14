 <?
includelogic("journal/search");
includelogic('accounting/accounting');

$search_class = new search_class();
$accounting   = new accounting();

if($_lib['input']->getProperty('action_postmotpost_get_matches')) {
  $result =$search_class->search_openpost_accountplan($accounting,
    array(
      'AccountPlanID'      => $_POST['AccountPlanID'],
      'VoucherID'          => $_POST['VoucherID'],
      'JournalID'          => $_POST['JournalID'],
      'VoucherType'        => $_POST['VoucherType'],
      'EnableSingleChoose' => $_POST['EnableSingleChoose'],
      'type'               => $_POST['type'],
      'From'               => $_POST['From']
      ));
  if(empty($result)){
    print "Fant ingen &aring;pne poster p&aring; leverand&oslash;ren.";
  } else {
    print $result;
  }
}
?>
