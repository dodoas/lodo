<?
includemodel('budget/budget_action');


if($_REQUEST['action_budget_update']) {
    /*  Initiating class  */
    $budgetAction = new budget_Action(array('_sess'=>$_sess, '_dsn'=>$_dsn));

    /*  Running update  */
    $budgetAction->action_budget_update(array('db_table'=>$db_table, 'db_table2'=>$db_table2, '_POST'=> $_POST));
}

elseif($_REQUEST['action_budget_new']) {
    $budgetAction = new budget_Action(array('_sess'=>$_sess, '_dsn'=>$_dsn, '_date'=>$_date));

    $budgetAction->action_budget_new(array('db_table'=>$db_table, 'db_table2'=>$db_table2, 'year' => $_POST['year']));
}

?>