<?

function query_full_table($table, $index_field) {
    global $_lib;
    
    $query = "SELECT * FROM $table";
    $result = $_lib['db']->db_query($query);
    
    $ret = array();
    while($row = $_lib['db']->db_fetch_assoc($result)) {
        $ret[ $row[$index_field] ] = $row;
    }
    
    ksort($ret);
    
    return $ret;
}

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
    $post['varelager_Comment'] = "Det er ikke ukurans p&aring; varelageret.
Varelager er priset til innkj&oslash;pspris.
Varelager er priset og telt av 
";
    $VareLagerID = $_lib['db']->db_new_hash($post, 'varelager');

    $post = array();
    $post['varelagerline_VareLagerID']   = $VareLagerID;

    $departments = query_full_table("companydepartment", "CompanyDepartmentID");
    $shelfs = query_full_table("shelf", "ShelfID");
    $projects = query_full_table("project", "ProjectID");

    $query = "select * from product order by ProductID asc";
    $result = $_lib['db']->db_query($query);
    while($row = $_lib['db']->db_fetch_object($result))
    {
        $post['varelagerline_ProductNr']   = $row->ProductID;
        $post['varelagerline_ProductName'] = $row->ProductName;
        $post['varelagerline_CostPrice']   = $row->UnitCostPrice;
        $post['varelagerline_Antall']      = 0;

        //if(!$row->ProjectID) $row->ProjectID = 1;
        if(!$row->ShelfID) $row->ShelfID = 1;

        $post['varelagerline_Department'] = $departments[ $row->CompanyDepartmentID ]["DepartmentName"];
        $post['varelagerline_Project'] = $projects[ $row->ProjectID ]["Heading"];
        $post['varelagerline_Shelf'] = $shelfs[ $row->ShelfID ]["Name"];

        $post['varelagerline_UnitSize'] = $row->Unitsize;
        $post['varelagerline_BulkSize'] = $row->Bulksize;
        $post['varelagerline_RealProductNumber'] = $row->ProductNumber;

        $_lib['db']->db_new_hash($post, 'varelagerline');
    }
}

elseif($_REQUEST['action_varelager_reload_products']) {
    $departments = query_full_table("companydepartment", "CompanyDepartmentID");
    $shelfs = query_full_table("shelf", "ShelfID");
    $projects = query_full_table("project", "ProjectID");
    
    $VareLagerID = $_REQUEST['VareLagerID'];
    
    $query = "select * from product order by ProductID asc";
    $result = $_lib['db']->db_query($query);
    while($row = $_lib['db']->db_fetch_object($result)) {
        $current_query = sprintf("select * from varelagerline where VareLagerID = %d and ProductNr = %d", 
                                 $VareLagerID, $row->ProductID);
        $current_result = $_lib['db']->db_query($current_query);
        $post = $_lib['db']->db_fetch_assoc($current_result);

        if(!$post) {
            $post = array();
        }

        $post['varelagerline_VareLagerID']   = $VareLagerID;
        
        $post['varelagerline_ProductNr']   = $row->ProductID;
        $post['varelagerline_ProductName'] = $row->ProductName;
        
        //if(!$row->ProjectID) $row->ProjectID = 1;
        if(!$row->ShelfID) $row->ShelfID = 1;

        $post['varelagerline_Department'] = $departments[ $row->CompanyDepartmentID ]["DepartmentName"];
        $post['varelagerline_Project'] = $projects[ $row->ProjectID ]["Heading"];
        $post['varelagerline_Shelf'] = $shelfs[ $row->ShelfID ]["Name"];
        
        $post['varelagerline_UnitSize'] = $row->Unitsize;
        $post['varelagerline_BulkSize'] = $row->Bulksize;
        $post['varelagerline_RealProductNumber'] = $row->ProductNumber;

        if(!$post) {
            // Add new varelagerline
            $post['varelagerline_CostPrice']   = $row->UnitCostPrice;
            $post['varelagerline_Antall']      = 0;
            $_lib['db']->db_new_hash($post, 'varelagerline');
        }
        else {
            // Update varelagerline
            $_lib['db']->db_update_hash($post, 'varelagerline', 
                                        array('VareLagerLineID' => $post['VareLagerLineID']));
        }
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

elseif($_REQUEST['action_varelager_lock']) {
    $query = sprintf("UPDATE varelager SET LockedBy = '%d', LockedDate = NOW() WHERE VareLagerID = %d",
                     $_SESSION["login_id"], $VareLagerID);
    $_lib['db']->db_query($query);
}

elseif($_REQUEST['action_varelager_unlock']) {
    $query = sprintf("UPDATE varelager SET LockedBy = '0', LockedDate = NOW() WHERE VareLagerID = %d",
                     $VareLagerID);
    $_lib['db']->db_query($query);
}


?>