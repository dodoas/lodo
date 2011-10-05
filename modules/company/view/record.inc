<?
# $Id: record.inc,v 1.16 2005/05/31 09:44:37 svenn Exp $ company_record.inc,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no

if($_lib['input']->getProperty('action_company_new'))
{
    $_POST['CustomerResponsibleID'] = $_lib['sess']->get_person('PersonID');
    $_POST['SalesmanID']            = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedByID']           = $_lib['sess']->get_person('PersonID');
    $_POST['CreatedDate']           = "NOW()";

    $CompanyID = $_dbh[$_dsn]->db_new_hash($_POST, $db_table);
}
elseif($_lib['input']->getProperty('action_company_update')) {
      $primarykey['CompanyID'] = (int) $_lib['input']->getProperty('CompanyID');
      #Possible to extend or alter parameters here
      #print_r($_POST);

      if($_POST['company_IName'] == '')
      {
          $_POST['company_IName'] = $_POST['company_CompanyName'];
      }
      if($_POST['company_IAddress'] == '')
      {
          $_POST['company_IAddress'] = $_POST['company_VAddress'];
      }
      if($_POST['company_IZipCode'] == '')
      {
          $_POST['company_IZipCode'] = $_POST['company_VZipCode'];
      }
      if($_POST['company_ICity'] == '')
      {
          $_POST['company_ICity'] = $_POST['company_VCity'];
      }
      if($_POST['company_ICountryCode'] == '')
      {
          $_POST['company_ICountryCode'] = $_POST['company_VCountryCode'];
      }

      $_dbh[$_dsn]->db_update_hash($_POST, $db_table, $primarykey);
}
elseif ($_lib['input']->getProperty('action_company_delete'))
{
    /************************************************************************/
    /* Delete info in own database                                          */
    /************************************************************************/
    $query = "DELETE FROM $db_table WHERE CompanyID='$CompanyID'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);

    /* Delete info in EmployeeTimeLine                                         */
    $query = "update $db_table2 set Active='0' WHERE CompanyID='$CompanyID'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);
}
elseif($_lib['input']->getProperty('action_person_new'))
{
    $_POST['CompanyID']             = $CompanyID;
    $_POST['DateCreated']           = "NOW()";
    $_POST['Active']                = 1;
    $_POST['CreatedByID']           = $login_id;
    $PersonID = $CompanyID = $_dbh[$_dsn]->db_new_hash($_POST, $db_table);

    $query = "INSERT INTO companypersonstruct SET PersonID='$PersonID', CompanyID='$CompanyID', Active='1'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);

    $query = "INSERT INTO roleperson SET PersonID='$PersonID', RoleID='5'";
    $result = $_dbh[$_dsn]->db_query($db_name, $query);
}
elseif($_lib['input']->getProperty('action_person_update'))
{
    $primarykey = "PersonID";
    $_dbh[$_dsn]->db_update_multi_record($_POST, $db_table2, $primarykey);
}
elseif($_lib['input']->getProperty('action_person_delete'))
{
    $PersonID = $_lib['input']->getProperty('PersonID');

    $query = "DELETE FROM $db_table2 WHERE PersonID='$PersonID'";
    //print "$query";
    $result = $_dbh[$_dsn]->db_query($query);

    $query = "update companypersonstruct set Active=0 WHERE PersonID='$PersonID' and CompanyID='$CompanyID'";
    //print "$query";
    $result = $_dbh[$_dsn]->db_query($query);
}
?>












