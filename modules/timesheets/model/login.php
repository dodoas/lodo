<?php

includelogic('timesheets/timesheet');

/**
 * using a function to not temper with the globals
 */
function handle_timesheet_logins()
{
    $timesheet = false;

    try 
    {
        if( isset($_POST['DB_NAME_LOGIN']) && isset($_POST['username']) && $_POST['password'] != '' ) 
        {
            $timesheet = new timesheet_user($_POST['username'], $_POST['password']);
        }
        else
        {
            $timesheet = new timesheet_user();
        }
    }
    catch(Exception $e)
    {
	//        print $e->getMessage();

        return false;
    }

    if(isset($_GET['logout']))
    {
        $timesheet->logout();
        
        header('Location: /');
        exit;
    }

    $page = new timesheet_user_page($timesheet);
    $page->pageswitch($_REQUEST['tp']);

    return true;
}

if( ( isset($_SESSION['timesheet_use']) && $_SESSION['timesheet_use'] === true )
    || ( isset($_POST['DB_NAME_LOGIN']) && isset($_POST['username']) ) )
{
    if(handle_timesheet_logins())
    {
        $_SESSION['timesheet_use'] = true;
        exit;
    }
    else
    {
        $_SESSION['timesheet_use'] = false;
    }
}

?>
