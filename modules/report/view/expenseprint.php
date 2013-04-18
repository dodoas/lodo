<?
# $Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $ company_edit.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
header("Content-type: text/html; charset=utf-8");

$db_table  = "expense_section";
$db_table2 = "expense_line";
$db_table3 = "expense_heading";
$VareTellingID= $_REQUEST['VareTellingID']; 
//$CompanyID = $_REQUEST['CompanyDepartmentID'];
//assert(!is_int($VareTellingID)); #All main input should be int
function norway_format($str){
    //return number_format($str,2,',','.');
    return number_format($str,2,',',' ');
} 

require_once  "record.inc";

//print $query;
/*$query = "select * from company where CompanyID='$VareTellingID'";
$result = $_dbh[$_dsn]->db_query($query);
$row = $_dbh[$_dsn]->db_fetch_object($result);*/

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Expense</title>
    <meta name="cvs"                content="$Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $" />
    <? //includeinc('head') ?>
      <style> 
        body{margin:0px;}
        .bmatter-vlarge{ 	FONT-WEIGHT: normal; FONT-SIZE: 32px; COLOR: #000; FONT-FAMILY: "Arial", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter-large{ 	FONT-WEIGHT: normal; FONT-SIZE: 22px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter-med{ 	FONT-WEIGHT: normal; FONT-SIZE: 19px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter-small{ 	FONT-WEIGHT: normal; FONT-SIZE: 14px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }

        .bmatter{ 	FONT-WEIGHT: normal; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter-bold { 	FONT-WEIGHT: bold; FONT-SIZE: 16px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter-bold1 { 	FONT-WEIGHT: bold; FONT-SIZE: 19px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .bmatter1{ 	FONT-WEIGHT: normal; FONT-SIZE: 18px; COLOR: #000; FONT-FAMILY: "tahoma", Verdana, Arial, Helvetica, sans-serif; }
        .buttongrey {     background-image: url("../images/buttonbg1.gif");     border: 1px solid #C9C9C9;     color: #3b3b3b;     cursor: pointer;     font-family: Verdana,Arial,Helvetica,sans-serif;     font-size: 11px;     font-weight: bold; } 
        table.sample {
            border-top: 1px solid #000000;
            border-left: 1px solid #000000;
        
        }
        table.sample th {
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
        }
        table.sample td {
            border-bottom: 1px solid #000000;
            border-right: 1px solid #000000;
        }

        </style>
</head>

<body onload="javascript:window.print();">

<? //includeinc('top') ?>
<? //includeinc('left') ?>
<?

$query = "select * from expense_section";
//print $query;
$result_companies = $_dbh[$_dsn]->db_query($query);

?>
<form name="frmsection"  action="<? print $MY_SELF ?>" method="post">
<input type="hidden" name="mode" value="add" />

<table cellspacing="0" width="100%" cellpadding="0" border="0">
<tr>
    <td width="20%"><img src="img/lodo/logo.gif"></td>
    <td width="60%" align="center" class="bmatter-large"><b>Report</b></td>
    <td width="20%">&nbsp;</td>

</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<tr><td colspan="3">
    <table cellspacing="0" class="sample" width="100%" id="table1">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 0,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
    <tr class="SubHeading">
      <th class="menu"><!--Heading <?echo $headcount++?>--></th>
      <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
      <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField2[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
      <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField3[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField4[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField5[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField6[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField7[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField8[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField9[]', 'value'=>$row_heading->vField9)) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10)) ?></th>
            <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
            <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>

   
        </tr>
			<?}?>

        <?
		$i = 0;
        $query = "select * from expense_line Where eType='Table1' AND VareTellingID = '".$VareTellingID."' order by ExpenseLineId asc ";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);

        $query = "select SUM(Salg) as totsalg, SUM(Kjop) as totkjop, SUM(Kjop1) as totkjop1, SUM(Kjop2) as totkjop2, SUM(Kjop3) as totkjop3, SUM(Kjop4) as totkjop4, SUM(Kjop5) as totkjop5, SUM(Kjop6) as totkjop6, SUM(Kjop7) as totkjop7, SUM(Kjop8) as totkjop8, SUM(Kjop9) as totkjop9, SUM(Kjop10) as totkjop10, SUM(Kjop11) as totkjop11, SUM(Kjop12) as totkjop12, SUM(Kjop13) as totkjop13, SUM(Kjop14) as totkjop14, SUM(Kjop15) as totkjop15 from expense_line Where eType='Table1' AND VareTellingID = '".$VareTellingID."' ";
        //print $query;
        $result_sum_table1 = $_dbh[$_dsn]->db_query($query);
    		$sum_table1_row = $_dbh[$_dsn]->db_fetch_object($result_sum_table1);

		while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
        <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td width="2%"><? print $i; ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Title[]', 'value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Salg[]', 'value'=>norway_format($row->Salg))) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop[]', 'value'=>norway_format($row->Kjop))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop1[]', 'value'=>norway_format($row->Kjop1))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop2[]', 'value'=>norway_format($row->Kjop2))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop3[]', 'value'=>norway_format($row->Kjop3))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop4[]', 'value'=>norway_format($row->Kjop4))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop5[]', 'value'=>norway_format($row->Kjop5))) ?></td>
            <?if($row->Kjop6 != ""){?>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))); }?>
                      <?if($row->Kjop7 != ""){?>
					  <td width="8%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))); }?>
                      <?if($row->Kjop8 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))); }?>
                      <?if($row->Kjop9 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))); }?>
                      <?if($row->Kjop10 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))); }?>
                      <?if($row->Kjop11 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))); }?>
                      <?if($row->Kjop12 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))); }?>
                      <?if($row->Kjop13 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))); }?>
                      <?if($row->Kjop14 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))); }?>
                      <?if($row->Kjop15 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))); }?>

                      <td>
					  <? 
					  print norway_format(($row->Salg+$row->Kjop+$row->Kjop1+$row->Kjop2+$row->Kjop3+$row->Kjop4+$row->Kjop5+$row->Kjop6+$row->Kjop7+$row->Kjop8+$row->Kjop9+$row->Kjop10+$row->Kjop11+$row->Kjop12+$row->Kjop13+$row->Kjop14+$row->Kjop15)); ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ExpenseSectionId[]', 'value'=>$row->ExpenseSectionId)) ?></td>
                  </tr>
            <?
        }
        ?>
    </table>
    <br /> 
</td>
</tr>
<tr><td colspan="3">
    <table cellspacing="0" class="sample" width="100%" id="table2">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 2,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
	        <tr class="SubHeading">
            <th class="menu"><!--Heading <?echo $headcount++?>--></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField2[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField3[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField4[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField5[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField6[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField7[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField8[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField9[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField9))) ?></th>
            <?if ($row_heading->vField10 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10));} ?>
            <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
            <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>


   
        </tr>
			<?}?>

        <?
		$i = 0;
        $query = "select * from expense_line Where eType='Table2' AND VareTellingID = '".$VareTellingID."' order by ExpenseLineId asc ";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);
		$totkjoparr = array();
		$kjopcounter = 0;
        while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
        <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td width="2%"><? print $i; ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Title[]', 'value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Salg[]', 'value'=>norway_format($row->Salg))) ?></td>
            <td width="10%"><? if ($i == 2) { echo norway_format($sum_table1_row->totkjop); $totkjoparr[] = $sum_table1_row->totkjop;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop[]', 'value'=>norway_format($row->Kjop))) ?></td>
					  <td width="10%"><? if ($i == 4) { echo norway_format($sum_table1_row->totkjop1); $totkjoparr[] = $sum_table1_row->totkjop1;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop1[]', 'value'=>norway_format($row->Kjop1))) ?></td>
					  <td width="10%"><? if ($i == 6) { echo norway_format($sum_table1_row->totkjop2); $totkjoparr[] = $sum_table1_row->totkjop2;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop2[]', 'value'=>norway_format($row->Kjop2))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop3[]', 'value'=>norway_format($row->Kjop3))) ?></td>
					  <td width="10%"><? if ($i%2 == 0) { echo norway_format($totkjoparr[$kjopcounter]+$row->Salg-$row->Kjop3);$kjopcounter++; } ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop4[]', 'value'=>norway_format($row->Kjop4))) ?></td>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop5[]', 'value'=>norway_format($row->Kjop5))) ?></td>
                      <?if($row->Kjop6 != ""){?>
					  <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))); }?>
                      <?if($row->Kjop7 != ""){?>
					  <td width="8%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))); }?>
                      <?if($row->Kjop8 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))); }?>
                      <?if($row->Kjop9 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))); }?>
                      <?if($row->Kjop10 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))); }?>
                      <?if($row->Kjop11 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))); }?>
                      <?if($row->Kjop12 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))); }?>
                      <?if($row->Kjop13 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))); }?>
                      <?if($row->Kjop14 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))); }?>
                      <?if($row->Kjop15 != ""){?>
					  <td><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))); }?>

                      <td>&nbsp;
					 </td>
                  </tr>
            <?
        }
        ?>
    </table>
    <br /> 
</td>
</tr>
<tr><td colspan="3">
    <table cellspacing="0" class="sample" width="100%" id="table3">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 4,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
	        <tr class="SubHeading">
            <th class="menu"><!--Heading <?echo $headcount++?>--></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField2[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
            <th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField3[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField4[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField5[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField6[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField7[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField8[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField9[]', 'value'=>$row_heading->vField9)) ?></th>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10)) ?></th>
            <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
            <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->show(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>


   
        </tr>
			<?}?>

        <?
		$i = 0;
        $query = "select * from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."' order by ProjectID asc";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);

        $query = "select SUM(Salg) as totsalg, SUM(Kjop) as totkjop, SUM(Kjop1) as totkjop1, SUM(Kjop2) as totkjop2, SUM(Kjop3) as totkjop3, SUM(Kjop4) as totkjop4, SUM(Kjop5) as totkjop5, SUM(Kjop6) as totkjop6, SUM(Kjop7) as totkjop7, SUM(Kjop8) as totkjop8, SUM(Kjop9) as totkjop9, SUM(Kjop10) as totkjop10, SUM(Kjop11) as totkjop11, SUM(Kjop12) as totkjop12, SUM(Kjop13) as totkjop13, SUM(Kjop14) as totkjop14, SUM(Kjop15) as totkjop15 from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."'";
        //print $query;
        $result_sum_table3 = $_dbh[$_dsn]->db_query($query);
		$sum_table3_row = $_dbh[$_dsn]->db_fetch_object($result_sum_table3);

        while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
        <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td width="2%"><? print $i; ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Title[]', 'value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Salg[]', 'value'=>norway_format($row->Salg))) ?></td>
            <td width="10%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop[]', 'value'=>norway_format($row->Kjop))) ?></td>
					  <td width="10%"><? print norway_format($row->Salg-$row->Kjop) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop1[]', 'value'=>norway_format($row->Kjop1)));?></td>
            <td width="12%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop2[]', 'value'=>norway_format($row->Kjop2)))?></td>
            <td width="12%"><? print norway_format(($row->Salg-$row->Kjop)+$row->Kjop2) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop3[]', 'value'=>norway_format($row->Kjop3)));?></td>
					  <td width="12%"><? print $_lib['form3']->show(array('table'=>$db_table2, 'field'=>'Kjop4[]', 'value'=>norway_format($row->Kjop4))) ?></td>					  
              <td width="12%"><? print norway_format($row->Kjop4-(($row->Salg-$row->Kjop)+$row->Kjop2)) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop5[]', 'value'=>norway_format($row->Kjop5)));?></td>
					  <td width="10%"><? print norway_format((100/($row->Salg-$row->Kjop+$row->Kjop2))*$row->Kjop4) ?></td>
					  <!--<td width="4%">&nbsp;</td>
            <td align="right"></td>-->
                      
                  </tr>
            <?
        }
        ?>                      
        <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td>&nbsp;</td>
            <td>Sum</td>
            <td><? print norway_format($sum_table3_row->totsalg) ?></td>
            <td><? print norway_format($sum_table3_row->totkjop) ?></td>
					  <td><? print norway_format($sum_table3_row->totsalg-$sum_table3_row->totkjop) ?></td>
					  <td><? print norway_format($sum_table3_row->totkjop2) ?></td>
					  <td><? print norway_format(($sum_table3_row->totsalg-$sum_table3_row->totkjop)+$sum_table3_row->totkjop2) ?></td>
    			  <td><? print norway_format($sum_table3_row->totkjop4) ?></td>
					  <td><? print norway_format($sum_table3_row->totkjop4-(($sum_table3_row->totsalg-$sum_table3_row->totkjop)+$sum_table3_row->totkjop2)) ?></td>
		        <td><? print norway_format((100/($sum_table3_row->totsalg-$sum_table3_row->totkjop+$sum_table3_row->totkjop2))*$sum_table3_row->totkjop4) ?></td>
					  <td >&nbsp;</td>
				</tr>

    </table>
    <br /> 
</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>


</table>
</form>

<script type="text/javascript">
var        $counter = 0; // initialize 0 for limitting textboxes

function createsection(){
document.getElementById("newsection").style.display='';     
}
function cancelsection(){
document.getElementById("newsection").style.display='none';
}
function checkvalid(){
if(document.getElementById("Title").value == ''){
alert("Please enter Section name");
return false;
}
document.frmsection.mode.value='addsection';
}

function createline(secid){

          var table = document.getElementById("table"+secid);
 
            var rowCount = table.rows.length-1;
            var row = table.insertRow(rowCount);
 			var rowCounter = rowCount-1;

            var cell1 = row.insertCell(0);
            cell1.innerHTML = rowCounter++;
 
	        var cell2 = row.insertCell(1);
            var element2 = document.createElement("input");
            element2.type = "text";
            element2.name = "expense_line_Title[]";
            cell2.appendChild(element2);

	        var cell3 = row.insertCell(2);
            var element2 = document.createElement("input");
            element2.type = "text";
            element2.name = "expense_line_Salg[]";
            cell3.appendChild(element2);
			
	        var cell4 = row.insertCell(3);
            var element2 = document.createElement("input");
			if(secid == 2){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(4);
            var element2 = document.createElement("input");
			if(secid == 2 || secid == 3){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop1[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(5);
            var element2 = document.createElement("input");
			if(secid == 2 || secid == 3){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop2[]";
            cell4.appendChild(element2);
		
				
	        var cell4 = row.insertCell(6);
            var element2 = document.createElement("input");
            element2.type = "text";
            element2.name = "expense_line_Kjop3[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(7);
            var element2 = document.createElement("input");
			if(secid == 2){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop4[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(8);
            var element2 = document.createElement("input");
			if(secid == 3){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop5[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(9);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop6[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(10);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop7[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(11);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop8[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(12);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop9[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(13);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop10[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(14);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop11[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(15);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop12[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(16);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop13[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(17);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop14[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(18);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_Kjop15[]";
            cell4.appendChild(element2);

	        var cell4 = row.insertCell(19);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_eType[]";
            element2.value = "Table"+secid;

            cell4.appendChild(element2);

return false;
      
}

      function deleteRow(tableID) {
            try {
            var table = document.getElementById(tableID);
            var rowCount = table.rows.length;
 
            for(var i=0; i<rowCount; i++) {
                var row = table.rows[i];
                var chkbox = row.cells[0].childNodes[0];
                if(null != chkbox && true == chkbox.checked) {
                    table.deleteRow(i);
                    rowCount--;
                    i--;
                }
 
 
            }
            }catch(e) {
                alert(e);
            }
        }
</script>
</body>
</html>
