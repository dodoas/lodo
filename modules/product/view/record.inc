<?
# $Id: record.inc,v 1.7 2005/10/14 13:15:41 thomasek Exp $ product_record.inc,v 1.2 2001/11/11 12:35:06 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2003, thomas@ekdahl.no, http://www.ekdahl.no
$ProductID = $_lib['input']->getProperty('ProductID');

if($_lib['input']->getProperty('action_product_new'))
{
    $query="select ProductNumber, ProductID from product where ProductNumber='$_POST[product_ProductNumber]'";
    $result = $_lib['db']->db_query($query);

    if($_lib['db']->db_numrows($result) == 0)
    {
        $_post = $_POST;
        $_post['product_Active'] = '1';
        $ProductID = $_lib['db']->db_new_hash($_post, $db_table);
    }
    else
    {
        $row = $_lib['db']->db_fetch_object($result);
        $ProductID=$row->ProductID;
    }
}
elseif($_lib['input']->getProperty('action_product_update'))
{
  $tables['product'] = 'ProductID';
  $_lib['db']->db_update_multi_table($_POST, $tables);
}
elseif ($_lib['input']->getProperty('action_product_delete'))
{
    //$query = "update product set Active='0' WHERE ProductID='$ProductID'";
    //$result = $_lib['db']->db_query($query);
    $query_delete = "delete from product where ProductID='$ProductID'";
    $result = $_lib['db']->db_delete($query_delete);
}
