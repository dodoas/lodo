<?

$period_ = explode('-', $_POST['report_Project']);
$period = $period_[0] . '-' . ( strlen($period_[1]) < 2 ? ("0" . $period_[1]) : $period_[1]);
$project = $_POST['report_ProjectID'];

header('Location: ' . str_replace("&amp;", "&", $_lib['sess']->dispatch) . "&t=timesheets.list&AccountPlanID=&Username=&tp=listprojectperiod&period=$period&project=$project");
exit;
