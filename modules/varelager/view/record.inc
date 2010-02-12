<?
if($_REQUEST['action_varelager_new'])
{
    /*$query="select CreatedDate from $db_table where CreatedDate='".$_POST[varelager_CreatedDate]."'";
    $result = $_lib['db']->db_query($query);
    if($_lib['db']->db_numrows($result) == 0)
    {
        $query = "select * from product order by ProductID asc";
        $result = $_lib['db']->db_query($query);
        while($row = $_lib['db']->db_fetch_object($result))
        {
            $_POST['varelager_ProductNr']   = $row->ProductID;
            $_POST['varelager_ProductName'] = $row->ProductName;
            $_POST['varelager_CostPrice']   = $row->UnitCostPrice;
            $_POST['varelager_Antall']      = 0;
            $_lib['db']->db_new_hash($_POST, $db_table);
        }
        $CreatedDate = $_POST[varelager_CreatedDate];
    }
    else
    {
        $CreatedDate = $_POST[varelager_CreatedDate];
    }*/
    $post = array();
    $post['varelager_CreatedDate'] = $_POST['varelager_CreatedDate'];
    $post['varelager_Description'] = $_POST['varelager_Description'];
    $VareLagerID = $_lib['db']->db_new_hash($post, 'varelager');

    $post = array();
    $post['varelagerline_VareLagerID']   = $VareLagerID;

    $query = "select * from product order by ProductID asc";
    $result = $_lib['db']->db_query($query);
    while($row = $_lib['db']->db_fetch_object($result))
    {
        $post['varelagerline_ProductNr']   = $row->ProductID;
        $post['varelagerline_ProductName'] = $row->ProductName;
        $post['varelagerline_CostPrice']   = $row->UnitCostPrice;
        $post['varelagerline_Antall']      = 0;
        $_lib['db']->db_new_hash($post, 'varelagerline');
    }
}

elseif($_REQUEST['action_varelager_update'])
{
    $VareLagerID = $_REQUEST['VareLagerID'];
    unset($_POST['VareLagerID']);

    $tables['varelager'] = 'VareLagerID';
    $tables['varelagerline'] = 'VareLagerLineID';
    $_lib['db']->db_update_multi_table($_POST, $tables);
}

elseif($_REQUEST['action_varelager_delete'])
{
    $primarykey = array();
    $primarykey['VareLagerID'] = $_REQUEST['action_varelager_delete'];
    $_lib['db']->db_delete_hash('varelager', $primarykey);

    $_lib['db']->db_delete_hash('varelagerline', $primarykey);
}

?>