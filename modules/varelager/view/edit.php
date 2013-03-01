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
$locked = ($head->LockedBy != 0);

$lockedByQuery = sprintf("SELECT FirstName, LastName FROM person WHERE PersonID = %d", $head->LockedBy);
$lockedByRes = $_lib['db']->db_query($lockedByQuery);
$lockedBy = $_lib['db']->db_fetch_assoc($lockedByRes);

print $_lib['sess']->doctype 
?>

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
                <th colspan="10" style="text-align:right;">side 1
            <tr>
                <th>Avdeling/Prosjekt/Hylle</th>
                <th>ProduktID</th>
                <th>Produktnr</th>
                <th>Produktnavn</th>
                <th style="text-align: right">Enhetsst&oslash;relse</th>
                <th style="text-align: right">Antall enheter i pris</th>
                <th style="text-align: right">Kostpris</th>
                <th style="text-align: right">Antall</th>
                <th style="text-align: right">Sum</th>
                <th style="text-align: right">Mengde</th>
            </tr>
        <tbody>
        <?
        $counter=0;
        $sum=0;
        $query = 
            "select 
               V.VareLagerID, 
               V.CreatedDate, 
               V.Description, 
               VL.*
             from 
               $db_table as V, 
               $db_table2 as VL
             where 
               V.VareLagerID='".$VareLagerID."' 
               and V.VareLagerID=VL.VareLagerID 
             order by 
               VL.Department, VL.Project, VL.Shelf, VL.ProductNr asc
        ";
        $result = $_lib['db']->db_query($query);

        $lastDepartment = "";
        $lastProject = "";
        $lastShelf = "";

        $departmentSum = -1;
        $projectSum = -1;
        $shelfSum = -1;
        $departmentAmount = 0;
        $projectAmount = 0;
        $shelfAmount = 0;

        function sumDepartment() {
            global $departmentSum, $departmentAmount, $lastDepartment, $_lib;

            if($departmentSum != -1) {
                printf("<tr style='background-color: #888;'><td colspan='2'><b>Sum %s</b></td><td></td><td></td><td></td><td></td><td></td><td></td><td style='text-align: right'><b>%s</b></td><td style='text-align: right'><b>%s</b></td></tr>", 
                       $lastDepartment, 
                       $_lib['format']->Amount(array('value'=>$departmentSum, 'return'=>'value')),
                       $departmentAmount
                    );
            }
        }

        function sumProject() {
            global $projectSum, $projectAmount, $lastProject, $_lib;

            if($projectSum != -1) {
                printf("<tr style='background-color: #aaa;'><td colspan='2'><b>Sum %s</b></td><td></td><td></td><td></td><td></td><td><td></td><td style='text-align: right'><b>%s</b></td><td style='text-align: right'><b>%s</b></td></tr>", 
                       $lastProject, 
                       $_lib['format']->Amount(array('value'=>$projectSum, 'return'=>'value')),
                       $projectAmount
                    );
            }
        }

        function sumShelf() {
            global $shelfSum, $shelfAmount, $lastShelf, $_lib;

            if($shelfSum != -1) {
                printf("<tr style='background-color: #ccc;'><td colspan='2'><b>Sum %s</b></td><td></td><td></td><td></td><td></td><td></td><td><td style='text-align: right'><b>%s</b></td><td style='text-align: right'><b>%s</b></td></tr>", 
                       $lastShelf, 
                       $_lib['format']->Amount(array('value'=>$shelfSum, 'return'=>'value')),
                       $shelfAmount
                    );
            }
        }

        while($row = $_lib['db']->db_fetch_object($result))
        {
            if($row->Department != $lastDepartment) {
                sumShelf();
                sumProject();
                sumDepartment();

                $lastDepartment = $row->Department;
                $lastProject = "";
                $lastShelf = "";

                $departmentSum = 0;
                $projectSum = -1;
                $shelfSum = -1;
                $departmentAmount = 0;
                $projectAmount = 0;
                $shelfAmount = 0;

                printf("<tr><td><b>%s</b></td></tr>\n", $lastDepartment);
            }
            
            if($row->Project != $lastProject) {
                sumShelf();
                sumProject();

                $lastProject = $row->Project;
                $lastShelf = "";

                $projectSum = 0;
                $shelfSum = -1;
                $projectAmount = 0;
                $shelfAmount = 0;
                
                printf("<tr><td><b>- %s</b></td></tr>\n", $lastProject);
            }
            
            if($row->Shelf != $lastShelf) {
                sumShelf();

                $lastShelf = $row->Shelf;

                $shelfSum = 0;
                $shelfAmount = 0;
                printf("<tr><td><b>-- %s</b></td></tr>\n", $lastShelf);
            }
 
            $counter++;
            $stock = $row->Antall;
            if(!$stock) { $stock = 0; }

            $sum += ($row->CostPrice * $stock);
            $departmentSum += ($row->CostPrice * $stock);
            $projectSum += ($row->CostPrice * $stock);
            $shelfSum += ($row->CostPrice * $stock);
            
            $departmentAmount += ($stock * $row->UnitSize * $row->BulkSize);
            $projectAmount += ($stock * $row->UnitSize * $row->BulkSize);
            $shelfAmount += ($stock * $row->UnitSize * $row->BulkSize);

            ?>
            <tr>
                <td></td>
                <td><? print $row->ProductNr; /* this is product ID */ ?></td>
                <td><? print $row->RealProductNumber ?></td>
                <td><? print $row->ProductName ?></td>
                <td class="number"><? printf("%2.2f", $row->UnitSize) ?></td>
                <td class="number"><? print $row->BulkSize ?></td>
                <td class="number"><? print ($locked?
                                              $_lib['format']->Amount(array('value'=>$row->CostPrice, 'return'=>'value'))
                                              : $_lib['form3']->text(array('table'=>'varelagerline', 'field'=>'CostPrice', 'pk'=>$row->VareLagerLineID, 'value'=>$row->CostPrice, 'width'=>'10', 'class'=>'number'))
                                              ) ?></td>
                <td class="number"><? print ($locked?$row->Antall:$_lib['form3']->text(array('table'=>'varelagerline', 'field'=>'Antall', 'pk'=>$row->VareLagerLineID, 'value'=>$row->Antall, 'width'=>'10', 'class'=>'number'))) ?></td>
                <td class="number"><nobr><? print $_lib['format']->Amount(array('value'=>$row->CostPrice * $row->Antall, 'return'=>'value')) ?></nobr></td>
                <td class="number"><?= sprintf("%2.2f", $stock * $row->UnitSize * $row->BulkSize); ?></td>
            </tr>
        <?
        }
            ?>

        <? 
          sumShelf();
          sumProject();
          sumDepartment();
        ?>
            <tr>
                <td colspan="10" align="right"><? print $_lib['format']->Amount(array('value'=>$sum, 'return'=>'value')) ?></td>
            </tr>
            <tr height="10">
                <td colspan="5"></td>
            </tr>
              <tr><td colspan="10">Vurdering av ukurans</td></tr>
              <?php
              if(!$locked) {
                  ?>
                 <tr><td colspan="10"><textarea cols="100" rows="5" name="varelager.Comment.<?= $head->VareLagerID ?>"><?= $head->Comment ?></textarea></td>
              <?php
              }
              else {
                  printf("<tr><td>%s</td></tr>", $head->Comment);
                  printf("<tr><td>L&aring;st av %s %s - %s</td></tr>", 
                         $lockedBy['FirstName'],
                         $lockedBy['LastName'],
                         $head->LockedDate);
              }
              ?>

              <?php
              if(!$locked) {
                  ?>
                 <tr>
                   <td colspan="10" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_update', 'value'=>'Lagre')) ?></td>
                 </tr>
                 <tr>
                    <td colspan="10" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_lock', 'value'=>'L&aring;s')) ?></td>
                 </tr>

                  <?
              }
              else if($_lib['sess']->get_person('AccessLevel') >= 4){
                  ?>
                                
                  <tr>
                  <td colspan="10" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_unlock', 'value'=>'L&aring;s opp')) ?></td>
                  </tr>
                  <?                  
              }
              ?>
            <tr>
              <?
                 if($_lib['sess']->get_person('AccessLevel') >= 3 && $sum == 0) { 
              ?>
              <td colspan="10" align="right"><? print $_lib['form3']->submit(array('name'=>'action_varelager_update', 'value'=>'Slett')) ?></td>
              <? 
                 }  
                 ?>
            </tr>
            <tr>
            <td>
              <? if(!$locked)
                 print $_lib['form3']->submit(array('name'=>'action_varelager_reload_products', 'value' => 'Last inn produktene p&aring;nytt'))
              ?>
            </td>
            </tr>
            <tr>
                <td colspan="10" align="right"><input type="button" name="name" value=" Lukk "/ onClick="window.close();">
  </td>
            </tr>
    </table>

    </form>
</body>
</html>
