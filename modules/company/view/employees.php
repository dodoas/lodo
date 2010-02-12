<?
# $Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table  = "company";
$db_table2 = "person";

$CompanyID = $_REQUEST['CompanyID'];
//assert(!is_int($CompanyID)); #All main input should be int

require_once  "record.inc";

$query = "select c.*, cm.MenuChoice from company as c left join confmenues as cm on (cm.MenuValue=c.ClassificationID and cm.MenuName='CompanyClassification') where (c.CompanyID=1 or c.CompanyID=2 or c.CompanyID=3 or c.CompanyID=4) group by c.CompanyID order by c.CompanyID asc";
//print $query;
$result_companies = $_dbh[$_dsn]->db_query($query);

//print $query;
/*$query = "select * from company where CompanyID='$CompanyID'";
$result = $_dbh[$_dsn]->db_query($query);
$row = $_dbh[$_dsn]->db_fetch_object($result);*/

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - customer</title>
    <meta name="cvs"                content="$Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>
<?
while($row_companies = $_dbh[$_dsn]->db_fetch_object($result_companies))
{
    ?>
    <table cellspacing="0" class="lodo_data">
        <tr>
            <th colspan="8"><a href="<? print $_SETUP['DISPATCH'] ?>t=company.edit&amp;CompanyID=<? print $row_companies->CompanyID ?>">Firma: <? print $row_companies->CompanyName ?> - <? print $row_companies->MenuChoice ?></a></th>
        </tr>
        <tr class="SubHeading">
            <th class="menu">Bruker nr</th>
            <th class="menu">Fornavn</th>
            <th class="menu">Etternavn</th>
            <th class="menu">Epost</th>
            <th class="menu">Mob</th>
            <th class="menu">Tilgang</th>
            <th class="menu">Endre</th>
            <th class="menu">Slett</th>
            <th class="menu">Lagre</th>
        </tr>
        <?
        $query = "select * from person as P, companypersonstruct as C where C.CompanyID='$row_companies->CompanyID' and C.PersonID=P.PersonID  and C.Active=1 and P.Active=1 order by FirstName, LastName desc";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);

        while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
                <tr class="<? print $sec_color ?>">
                <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>&CompanyID=<? print $row_companies->CompanyID ?>" method="post">
                    <input type="hidden" name="PersonID"    value="<? print $row->PersonID; ?>">
                    <input type="hidden" name="CompanyID"   value="<? print $row_companies->CompanyID; ?>">
                      <td><? print $row->PersonID; ?></td>
                      <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'FirstName', 'pk'=>$row->PersonID, 'value'=>$row->FirstName)) ?></td>
                      <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'LastName', 'pk'=>$row->PersonID, 'value'=>$row->LastName)) ?></td>
                      <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Email', 'pk'=>$row->PersonID, 'value'=>$row->Email)) ?></td>
                      <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'MobilePhoneNumber', 'pk'=>$row->PersonID, 'value'=>$row->MobilePhoneNumber)) ?></td>
                      <td>
                          <?
                            if($_lib['sess']->get_person('AccessLevel') == 4)
                                print $_lib['form3']->Type_menu3(array('table'=>$db_table2, 'field'=>'AccessLevel', 'pk'=>$row->PersonID, 'value'=>$row->AccessLevel, 'type'=>'typeaccesslevelmenu'));
                            else
                                print $row->AccessLevel;
                          ?>
                      </td>
                      <td><a href="<? print $_SETUP['DISPATCH'] ?>t=person.edit&PersonID=<? print $row->PersonID ?>&CompanyID=<? print $row_companies->CompanyID ?>">Detaljer</a></td>
                      <td>
                      <?
                      if($_lib['sess']->get_person('AccessLevel') >= 4)
                      {
                          ?>
                          <a href="<? print $_SETUP['DISPATCH'] ?>t=company.employees&amp;PersonID=<? print $row->PersonID ?>&amp;CompanyID=<? print $row_companies->CompanyID ?>&amp;action_person_delete=1" class="button">Slett</a></td>
                          <?
                      }
                      ?>
                      <td>
                      <?
                      if($_lib['sess']->get_person('AccessLevel') >= 3)
                      {
                          print $_lib['form3']->submit(array('name'=>'action_person_update', 'value'=>'Lagre (S)', 'accesskey'=>'S'));
                      }
                      ?>
                      </td>
                  </form>
                  </tr>
            <?
        }
        ?>
        <tr>
            <form name="new_employee" action="<? print $_SETUP['DISPATCH'] ?>t=person.edit&CompanyID=<? print $row_companies->CompanyID ?>" method="post">
                <td align="left">
                    <?
                    if($_lib['sess']->get_person('AccessLevel') >= 3 )
                    {
                        print $_lib['form3']->submit(array('name'=>'action_person_new', 'value'=>'Ny ansatt (N)', 'tabindex'=>'0', 'accesskey'=>'N'));
                    }
                    ?>
                </td>
                <td colspan="8"></td>
            </form>
        </tr>
    </table>
    <br /> <br />
    <?
}
?>
</body>
</html>
