<?
# $Id: record.inc,v 1.13 2005/10/14 13:15:41 thomasek Exp $ person_record.inc,v 1.1.1.1 2001/11/08 18:13:59 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no, http://www.ekdahl.no

$primaryKey['PersonID'] = $PersonID;

if($_lib['input']->getProperty('action_person_new'))
{
    /************************************************************************/
    /* Update info in Db database, grant access to current db, default      */
    /* whithout any privileges                                              */
    /************************************************************************/
    $query      = "INSERT INTO $db_table set HireDate=NOW(), CreatedDateTime=NOW(), CreatedByPersonID='" . $_lib['sess']->get_person('PersonID') . "',Active=1, CompanyID='$CompanyID', AccessLevel=1";
    $PersonID   = $_lib['db']->db_insert($query);

    $query      = "INSERT INTO companypersonstruct SET PersonID='$PersonID', CompanyID='$CompanyID', Active='1'";
    $_lib['db']->db_insert($query);

    $query      = "INSERT INTO roleperson SET PersonID='$PersonID', RoleID='5'"; #Default add the lodo role to all persons
    $_lib['db']->db_insert($query);
}

elseif($_lib['input']->getProperty('action_person_update'))
{
    $_lib['db']->db_update_hash($_POST, $db_table, $primaryKey);
}

elseif($_lib['input']->getProperty('action_person_delete'))
{
    $_lib['db']->db_delete_hash($db_table, $primaryKey);
}

elseif($_lib['input']->getProperty('action_password_update'))
{
    if($_POST['newPassword'] == $_POST['newPassword2'])
    {
        $query = "select Password from person where Password=password('".$_POST['passwordOld']."')";
        $result = $_lib['db']->db_query($query);
        $person = $_lib['db']->db_fetch_object($result);
        if($person || $_lib['sess']->get_person('AccessLevel') == 4)
        {
            $passwdQuery = "update person set Password = password('".$_POST['newPassword']."') where PersonID=$PersonID";
            $_lib['sess']->debug($passwdQuery);
            $_lib['db']->db_update($passwdQuery);

            $melding = "Passordet er byttet";
        }
        else
        {
            $melding = "Feil gammelt passord";
        }
    }
    else
    {
        $melding = "Du skrev ikke nytt passord likt to ganger";
    }
}
?>