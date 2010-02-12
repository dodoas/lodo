<?

# $Id: edit.php,v 1.12 2005/10/14 13:15:42 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$VareLagerID = $_REQUEST['VareLagerID'];

$db_table = "varelager";
$db_table2 = "varelagerline";

require_once "record.inc";

$query  = "select * from varelager where VareLagerID=$VareLagerID";
$head   = $_lib['storage']->get_row(array('query' => $query));

print $_lib['sess']->doctype ?>

<head>
    <title>Forventet flyt</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.12 2005/10/14 13:15:42 thomasek Exp $" />
    <? include $_SETUP['HOME_DIR'] . "/code/lib/html/head.inc"; ?>
</head>
<body>
<h2><? print $_lib['sess']->get_companydef('VName') ?> - <? print $head->CreatedDate ?>  - <? print $head->Description ?></h2>
    <form name="varetelling" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name'=>'VareLagerID', 'value'=>$VareLagerID)) ?>
    <table class="lodo_data">
        <thead>
            <tr>
                <th>Varetelling
                <th colspan="4">
            <tr>
                <th>Produktnr</th>
                <th>Produktnavn</th>
                <th>Kostpris</th>
                <th>Antall</th>
                <th>Sum</th>
            </tr>
        <tbody>
        <?
        $counter=0;
        $sum=0;
        $query = "select V.VareLagerID, V.CreatedDate, V.Description, VL.VareLagerLineID, VL.ProductNr, VL.ProductName, VL.Antall, VL.CostPrice from $db_table as V, $db_table2 as VL where V.VareLagerID='".$VareLagerID."' and V.VareLagerID=VL.VareLagerID order by VL.ProductNr asc";
        $result = $_lib['db']->db_query($query);
        while($row = $_lib['db']->db_fetch_object($result))
        {
            $counter++;
            $stock = $row->Antall;
            if(!$stock) { $stock = 0; }

            $sum += ($row->CostPrice * $stock)

            ?>
            <tr>
                <td><? print $row->ProductNr ?></td>
                <td><? print $row->ProductName ?></td>
                <td class="number"><? print $_lib['format']->Amount(array('value'=>$row->CostPrice, 'return'=>'value')) ?></td>
                <td><? print $_lib['form3']->text(array('table'=>'varelagerline', 'field'=>'Antall', 'pk'=>$row->VareLagerLineID, 'value'=>$row->Antall, 'width'=>'10', 'class'=>'number')) ?></td>
                <td class="number"><nobr><? print $_lib['format']->Amount(array('value'=>$row->CostPrice * $row->Antall, 'return'=>'value')) ?></nobr></td>
            </tr>
        <?
        }
            ?>
            <tr>
                <td colspan="5" align="right"><? print $_lib['format']->Amount(array('value'=>$sum, 'return'=>'value')) ?></td>
            </tr>
            <tr height="10">
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_update', 'value'=>'Lagre')) ?></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_update', 'value'=>'Slett')) ?></td>
            </tr>
            <tr>
                <td colspan="5" align="right"><input type="button" name="name" value=" Lukk "/ onClick="window.close();">
  </td>
            </tr>
    </table>

    </form>
</body>
</html>
