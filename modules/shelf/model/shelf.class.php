<?php

class lodo_shelf {
    function __construct() {}

    function listAll() {
        global $_lib;

        $query = "SELECT * FROM shelf"; 
        $res = $_lib['db']->db_query($query);

        $arr = array();
        while( ($row = $_lib['db']->db_fetch_assoc($res)) ) {
            $arr[ $row['ShelfID'] ] = array($row['Name'], $row['Active']);
        }
        
        return $arr;
    }

    function get($id) {
        global $_lib;

        $query = sprintf(
            "SELECT * FROM shelf WHERE ShelfID = %d", 
            $id
            );

        $res = $_lib['db']->db_query($query);
        $r = $_lib['db']->db_fetch_assoc($res);

        return array($r['Name'], $r['Active']);
    }

    function update($id, $name, $active = 0) {
        global $_lib;

        $query = sprintf(
            "UPDATE shelf SET Name = '%s', Active = '%d' WHERE ShelfID = %d",
            $name, 
            $active,
            $id
            );
        
        $_lib['db']->db_query($query);
    }

    function create($name) {
        global $_lib;

        $query = sprintf(
            "INSERT INTO shelf (`Name`, `Active`) VALUES ('%s', '%d');",
            $name, 
            1
            );
        
        $_lib['db']->db_query($query);

        return $_lib['db']->db_insert_id();
    }

    function delete($id) {
        global $_lib;

        $query = sprintf(
            "DELETE FROM shelf WHERE ShelfID = %d LIMIT 1",
            $id
            );

        $_lib['db']->db_query($query);
    }
}

?>