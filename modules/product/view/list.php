<?
# $Id: list.php,v 1.18 2005/10/28 17:59:40 thomasek Exp $ product_list.php,v 1.2 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "product";

require_once "record.inc";

$query = "select * from $db_table";
$result2 = $_lib['db']->db_query($query);
$db_total = $_lib['db']->db_numrows($result2);

$query = "select * from $db_table order by CAST(ProductNumber as SIGNED), ProductNumber asc";
$result2 = $_lib['db']->db_query($query);
$products = array();
while($row = $_lib['db']->db_fetch_object($result2)) {
  $products[] = $row;
  if (empty($row->AccountPlanID)) $_lib['message']->add("For produkt '".$row->ProductID." - ".$row->ProductName."' er ikke resultatkonto satt");
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - product list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.18 2005/10/28 17:59:40 thomasek Exp $">
    <? includeinc('head') ?>
    <script src="lib/js/sorttable.js"></script>
</head>

<body>
<?
    includeinc('top');
    includeinc('left');
    if ($messages = $_lib['message']->get()) {
?>
    <div class="warning"><? print $messages; ?></div>
    <br/>
<?
    }
?>

    <table class="lodo_data" width="800px">
        <thead>
            <tr>
                <th>Produkt listen:
            <tr>
                <th style="text-align: right;">
                  <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
                    <form name="edit" action="<? print $_lib['sess']->dispatch ?>t=product.edit" method="post">
                        <? print $_lib['form3']->Input(array('type'=>'text', 'table'=>$db_table, 'field'=>'ProductNumber')) ?>
                        <? print $_lib['form3']->Input(array('type'=>'submit', 'name'=>'action_product_new', 'value'=>'Nytt produkt (N)')) ?>
                    </form>
                  <? } ?>
               </th>
            </tr>
         </thead>
    </table>

    <table class="sortable lodo_data" width="800px">
            <tr>
              <th class="sub">Produkt ID</th>
              <th class="sub">Produktnummer</th>
              <th class="sub">Aktiv</th>
              <th class="sub">Produktnavn</th>
              <th class="sub" align="right">MVA</th>
              <th class="sub" align="right">Kostpris</th>
              <th class="sub" align="right">Pris</th>
              <th class="sub" align="right">Hovedbok</th>
              <th class="sub" align="right">Prosjekt</th>
              <th class="sub" align="right">Avdeling</th>
            </tr>
            <?
              if (!empty($products)) {
                foreach($products as $row)
                {
                    if($row->AccountPlanID) {
                        $date = $_lib['sess']->get_session('LoginFormDate');
                        $vat_query = "select v.Percent from vat as v, accountplan as a where v.Active=1 and a.AccountPlanID=$row->AccountPlanID and a.VatID=v.VatID and v.ValidFrom <= '$date' and v.ValidTo >= '$date'";
                        $vatRow = $_lib['storage']->get_row(array('query' => $vat_query));
                    } else {
                        unset($vatRow);
                    }
                    ?>
                    <tr>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=product.edit&ProductID=<? print $row->ProductID ?>"><? print $row->ProductID ?></a></td>
                        <td align="center"><a href="<? print $_lib['sess']->dispatch ?>t=product.edit&ProductID=<? print $row->ProductID ?>"><? print $row->ProductNumber ?></a></td>
                        <td> <? print $_lib['form3']->checkbox(array('table'=>'product', 'value'=>$row->Active, 'disabled'=>'1')) ?> </td>
                        <td><a href="<? print $_lib['sess']->dispatch ?>t=product.edit&ProductID=<? print $row->ProductID ?>"><? print $row->ProductName ?></a></td>
                        <td align="right"><? print $_lib['format']->Percent(array('value'=>$vatRow->Percent*1, 'return'=>'value')) ?></td>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$row->UnitCostPrice, 'return'=>'value')) ?></td>
                        <td align="right"><? print $_lib['format']->Amount(array('value'=>$row->UnitCustPrice, 'return'=>'value')) ?></td>
                        <td align="left"><? if($row->AccountPlanID) { $query="select AccountName from accountplan where AccountPlanID=$row->AccountPlanID"; $row2=$_lib['storage']->get_row(array('query' => $query)); print $row->AccountPlanID." ".$row2->AccountName; } ?></td>
                        <td align="left"><? if($row->ProjectID) { $query="select Heading from project where ProjectID=$row->ProjectID"; $row2=$_lib['storage']->get_row(array('query' => $query)); print $row->ProjectID." ".$row2->Heading; } ?></td>
                        <td align="left"><? if($row->DepartmentID) { $query="select DepartmentName from department where DepartmentID=$row->DepartmentID"; $row2=$_lib['storage']->get_row(array('query' => $query)); print $row->DepartmentID." ".$row2->DepartmentName; } ?></td>
                    </tr>
                    <?
                }
              }
            ?>
    </table>
</body>
</html>


