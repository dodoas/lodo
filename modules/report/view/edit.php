<?

# $Id: edit.php,v 1.12 2005/10/14 13:15:42 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
header("Content-type: text/html; charset=utf-8");

$VareTellingID = $_REQUEST['VareTellingID']; 
$db_table  = "expense_section";
$db_table2 = "expense_line";
$db_table3 = "expense_heading";

require_once "record.inc";

$query  = "select * from varetelling where VareTellingID=$VareTellingID";
$head   = $_lib['storage']->get_row(array('query' => $query));//echo "<pre>";print_r($head);
$locked = ($head->LockedBy != 0);
$start = substr($head->Period,0,4)."-01";
$end  = $head->Period;

$curr_year = "Aret"." ".substr($head->Period,0,4);
        
$query_department  = "select * from companydepartment where CompanyDepartmentID=$head->DepartmentID";
$department   = $_lib['storage']->get_row(array('query' => $query_department));

// Add Heading from table expense_heading  for new Vare Telling
if($VareTellingID != ""){
$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."'";
$heading = $_dbh[$_dsn]->db_query($query);
$totheading=$heading->num_rows;
   if($totheading == 0){
       //echo $totheading;
       $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','','Øl','Vin','Brennevin','','','','Sum','Sum','','','','','','')";
			 $_lib['db']->db_query($query);
			 
			 $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','','2,5% til 4,7%','4,7% til 21%','22% til 60%','','','','Liter','kr','','','','','','')";
			 $_lib['db']->db_query($query);
			 
			 $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','Beholdning','Kjøp','Kjøp','Kjøp','Beholdning','Salg','Forventet','Salg','','','','','','','')";
			 $_lib['db']->db_query($query);
			 
			 $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','Den 1 jan','Øl','Vin','Brennevin','Den 31 des','".$curr_year."','Året 2011','Året 2010','','','','','','','')";
			 $_lib['db']->db_query($query);
			 
			 $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','Varelager','Varelager','Varelager','Varekjøp','Vare','Salg','Fortjeneste','Fortjeneste','','','','','','','')";
			 $_lib['db']->db_query($query);
			 
			 $query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','','1 jan 2011','31 des 2011','regulering','','forbruk','".$curr_year."','i kr','i %','','','','','','','')";
			 $_lib['db']->db_query($query);
			 
   }
}
// Add Heading from table expense_heading  for new Vare Telling ends

//checking for projects for Table3
if($VareTellingID != ""){
$query = "select * from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."' ";
$project = $_dbh[$_dsn]->db_query($query);
$total_project = $project->num_rows; 
//checking for projects for etype='Table3' in expense_line table  
if($total_project == 0){
    $query = "Select ProjectID,Heading from project order by ProjectId desc";
    $project1 = $_dbh[$_dsn]->db_query($query);//echo "<pre>";print_r($project1);exit;
    $total_project1 = $project1->num_rows; //echo $total_project1;
    while($project_rec = $_dbh[$_dsn]->db_fetch_object($project1))
		{           
        $where = ' V.DepartmentID=' . $head->DepartmentID . ' and ';
        $query = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='400' and V.ProjectID = '".$project_rec->ProjectID."' group by A.ReportShort";
        $compareThisYear = $_lib['storage']->get_row(array('query' => $query));
        $ThisYearSumin = $compareThisYear->sumin - $compareThisYear->sumout;
    
        $sum_in = 0;
        $sum_out = 0;
        $query1 = "select sum(V.AmountIn) as totalin, sum(V.AmountOut) as totalout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort IN ('300','310','360') and V.ProjectID = '".$project_rec->ProjectID."' group by A.ReportShort";
        $compareThisYearout = $_lib['db']->db_query($query1);
        while($new_sum = $_dbh[$_dsn]->db_fetch_object($compareThisYearout))
		    {
		         $sum_in += $new_sum->totalin; 
		         $sum_out += $new_sum->totalout; 
		    } 
		    $ThisYearSumout = $sum_in - $sum_out;
        //$compareThisYearout = $_lib['storage']->get_row(array('query' => $query1));
        //$ThisYearSumout = $compareThisYearout->totalin - $compareThisYearout->totalout;
        
        $query = "insert into expense_line (VareTellingID,ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','".$project_rec->ProjectID."','Table3','".$project_rec->Heading."','','','','".$ThisYearSumin."','','".abs($ThisYearSumout)."','','','','','','','','','','','')";
			  $_lib['db']->db_query($query);
    } 
    
}
else{    
    while($project_exist_expense = $_dbh[$_dsn]->db_fetch_object($project))
		{           
        $project_exist_arr[] = $project_exist_expense->ProjectID; 
    } //echo "<pre>";print_r($project_exist_arr);
    //$project_exist_arr = implode(",",$project_exist_arr);//echo $project_exist_arr;  
    
    $query = "Select ProjectID,Heading from project";
    $project_new = $_dbh[$_dsn]->db_query($query);//echo "<pre>";print_r($project_new);exit;    
    while($projectadd_new = $_dbh[$_dsn]->db_fetch_object($project_new))
		{           
        if(!in_array($projectadd_new->ProjectID, $project_exist_arr))
        {
                $where = ' V.DepartmentID=' . $head->DepartmentID . ' and ';
                $query = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='400' and V.ProjectID = '".$projectadd_new->ProjectID."' group by A.ReportShort";
                $compareThisYear = $_lib['storage']->get_row(array('query' => $query));
                $ThisYearSumin = $compareThisYear->sumin - $compareThisYear->sumout;
            
                $sum_in = 0;
                $sum_out = 0;
                $query1 = "select sum(V.AmountIn) as totalin, sum(V.AmountOut) as totalout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort IN ('300','310','360') and V.ProjectID = '".$projectadd_new->ProjectID."' group by A.ReportShort";
                $compareThisYearout = $_lib['db']->db_query($query1);
                while($new_sum = $_dbh[$_dsn]->db_fetch_object($compareThisYearout))
        		    {
        		         $sum_in += $new_sum->totalin; 
        		         $sum_out += $new_sum->totalout; 
        		    } 
        		    $ThisYearSumout = $sum_in - $sum_out;
        		    
                $query = "insert into expense_line (VareTellingID,ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','".$projectadd_new->ProjectID."','Table3','".$projectadd_new->Heading."','','','','".$ThisYearSumin."','','".abs($ThisYearSumout)."','','','','','','','','','','','')";
			          $_lib['db']->db_query($query);
			          //echo $projectadd_new->Heading;
        }
        else
        {       // code for runtime update sum of in and out in Kjop2 and Kjop4
                $where = ' V.DepartmentID=' . $head->DepartmentID . ' and ';
                $query = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='400' and V.ProjectID = '".$projectadd_new->ProjectID."' group by A.ReportShort";
                $compareThisYear = $_lib['storage']->get_row(array('query' => $query));
                $ThisYearSumin = $compareThisYear->sumin - $compareThisYear->sumout;
                
                $sum_in = 0;
                $sum_out = 0;
                $query1 = "select sum(V.AmountIn) as totalin, sum(V.AmountOut) as totalout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort IN ('300','310','360') and V.ProjectID = '".$projectadd_new->ProjectID."' group by A.ReportShort";
                $compareThisYearout = $_lib['db']->db_query($query1);
                while($new_sum = $_dbh[$_dsn]->db_fetch_object($compareThisYearout))
        		    {
        		         $sum_in += $new_sum->totalin; 
        		         $sum_out += $new_sum->totalout; 
        		    } 
        		    $ThisYearSumout = $sum_in - $sum_out;
        		    
        		    $query = "update expense_line set Kjop2='".$ThisYearSumin."',Kjop4='".abs($ThisYearSumout)."' where VareTellingID='".$VareTellingID."' AND ProjectID = '".$projectadd_new->ProjectID."' AND eType='Table3'";
                $_lib['db']->db_update($query);
                
        } 
    }  //echo "<pre>";print_r($project_arr); 
} 
$query = "select * from expense_line Where eType='Table2' AND VareTellingID = '".$VareTellingID."' ";
$type2 = $_dbh[$_dsn]->db_query($query);
$total_type2 = $type2->num_rows;                                                                                                                                                                                
if($total_type2 == 0){
    $query = "insert into expense_line (VareTellingID,ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','','Table2','Øl','','','','','','','','','','','','','','','','','')";
		$_lib['db']->db_query($query);
		
		$query = "insert into expense_line (VareTellingID,ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','','Table2','Vin','','','','','','','','','','','','','','','','','')";
		$_lib['db']->db_query($query);
		
		$query = "insert into expense_line (VareTellingID,ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','','Table2','Brennevin','','','','','','','','','','','','','','','','','')";
		$_lib['db']->db_query($query);
} 
   
}   
//checking for projects for etype='Table3' in expense_line table ends 
function replace($str){
    //$num1 = str_replace(".","",$str);
    $num1 = str_replace(" ","",$str);
    $num2 = str_replace(",",".",$num1);
    return $num2;
    //$num3 = str_replace(" ","",$num2);
    //return $num3;  
}

function norway_format($str){
    if($str != ""){
    //return number_format($str,2,',','.');
    return number_format($str,2,',',' ');
    }else{
    return "";
    }
}

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - Expense</title>
    <meta name="cvs"    content="$Id: employees.php,v 1.34 2005/05/31 09:44:37 svenn Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<? //includeinc('top') ?>
<? //includeinc('left') ?>
<?
if($_POST['mode'] == "add"){
	
	//$query = "TRUNCATE TABLE expense_heading";
	$query = "DELETE from expense_heading where VareTellingID = '".$VareTellingID."' ";
	$_lib['db']->db_query($query);
       
	for($i=0;$i<count($_POST['expense_heading_vField1']);$i++){
    	$query = "insert into expense_heading (VareTellingID,vField1,vField2,vField3,vField4,vField5,vField6,vField7,vField8,vField9,vField10,vField11,vField12,vField13,vField14,vField15,vField16) values ('".$VareTellingID."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField1'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField2'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField3'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField4'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField5'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField6'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField7'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField8'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField9'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField10'][$i])."','".iconv("utf-8","iso-8859-1",$_POST['expense_heading_vField11'][$i])."','".$_POST['expense_heading_vField12'][$i]."','".$_POST['expense_heading_vField13'][$i]."','".$_POST['expense_heading_vField14'][$i]."','".$_POST['expense_heading_vField15'][$i]."','".$_POST['expense_heading_vField16'][$i]."')";
			$_lib['db']->db_query($query);
	}
   
	//$query = "TRUNCATE TABLE expense_line";
	$query = "DELETE from expense_line where VareTellingID = '".$VareTellingID."'";
	$_lib['db']->db_query($query);
    
	for($i=0;$i<count($_POST['expense_line_Title']);$i++){
    		$query = "insert into expense_line (VareTellingID, ProjectID, eType, Title, Salg, Kjop, Kjop1, Kjop2, Kjop3, Kjop4, Kjop5, Kjop6, Kjop7, Kjop8, Kjop9, Kjop10, Kjop11, Kjop12, Kjop13, Kjop14, Kjop15) values ('".$VareTellingID."','".$_POST['expense_line_ProjectID'][$i]."','".$_POST['expense_line_eType'][$i]."','".iconv("utf-8","iso-8859-1",$_POST['expense_line_Title'][$i])."','".replace($_POST['expense_line_Salg'][$i])."','".replace($_POST['expense_line_Kjop'][$i])."','".replace($_POST['expense_line_Kjop1'][$i])."','".replace($_POST['expense_line_Kjop2'][$i])."','".replace($_POST['expense_line_Kjop3'][$i])."','".replace($_POST['expense_line_Kjop4'][$i])."','".replace($_POST['expense_line_Kjop5'][$i])."','".replace($_POST['expense_line_Kjop6'][$i])."','".replace($_POST['expense_line_Kjop7'][$i])."','".replace($_POST['expense_line_Kjop8'][$i])."','".replace($_POST['expense_line_Kjop9'][$i])."','".replace($_POST['expense_line_Kjop10'][$i])."','".replace($_POST['expense_line_Kjop11'][$i])."','".replace($_POST['expense_line_Kjop12'][$i])."','".replace($_POST['expense_line_Kjop13'][$i])."','".replace($_POST['expense_line_Kjop14'][$i])."','".replace($_POST['expense_line_Kjop15'][$i])."')";
			  $_lib['db']->db_query($query);
	}
//echo "<script>window.open('lodo.php?t=report.expenseprint','Test section Print','width=700,height=600,resizable=yes,scrollbars=yes');</script>"; 
  //echo "<script>window.location='lodo.php?t=report.expense&VareTellingID=$VareTellingID'</script>";
  //header("location:https://".$_SERVER['SERVER_NAME']."/loko/html/lodo.php?SID=7ut2q4mscdoncpa83ag7b8ljp6&view_mvalines=&view_linedetails=&t=report.expense&VareTellingID=".$VareTellingID);
  //exit;
}
if($_POST['mode'] == "addsection"){
		$query = "insert into expense_section (Title) values ('".$_POST['Title']."')";
		//$_lib['db']->db_query($query); 
		//header("Location:".$MY_SELF);
		//exit;
}

//$query = "select * from expense_section";
//print $query;
//$result_companies = $_dbh[$_dsn]->db_query($query);
//include($_SETUP['HOME_DIR'] . "/modules/varelager/view/list.php");
echo "<br />"; 
?>
<h2><? print $_lib['sess']->get_companydef('VName') ?> - <? print $head->CreatedDate ?>  - <? print $head->Description ?> - <? print $head->Period ?> - <? print $department->DepartmentName ?> </h2><hr />
<form name="frmsection"  action="lodo.php?t=report.edit&VareTellingID=<?php echo $VareTellingID;?>" method="post">
<input type="hidden" name="mode" value="add" />

<table cellspacing="0" cellpadding="0" border="0">
<tr><td><br />
    <table cellspacing="0" class="lodo_data" style="width:600px;" id="table1">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 0,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
    <tr class="SubHeading">
      <th class="menu">Heading <?echo $headcount++?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField2[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField3[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField4[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField5[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField6[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField7[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField8[]','class'=>'rightclass' ,'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField9[]','class'=>'rightclass' ,'value'=>$row_heading->vField9)) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField10[]','class'=>'rightclass' ,'value'=>$row_heading->vField10)) ?></th>
      <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			<? }else{ ?>
			<? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
      <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>

   
    </tr>
			<?}?>

        <?
		    $i = 0;
        $query = "select * from expense_line Where eType='Table1' AND VareTellingID = '".$VareTellingID."' order by ExpenseLineId asc";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);

        $query = "select SUM(Salg) as totsalg, SUM(Kjop) as totkjop, SUM(Kjop1) as totkjop1, SUM(Kjop2) as totkjop2, SUM(Kjop3) as totkjop3, SUM(Kjop4) as totkjop4, SUM(Kjop5) as totkjop5, SUM(Kjop6) as totkjop6, SUM(Kjop7) as totkjop7, SUM(Kjop8) as totkjop8, SUM(Kjop9) as totkjop9, SUM(Kjop10) as totkjop10, SUM(Kjop11) as totkjop11, SUM(Kjop12) as totkjop12, SUM(Kjop13) as totkjop13, SUM(Kjop14) as totkjop14, SUM(Kjop15) as totkjop15 from expense_line Where eType='Table1' AND VareTellingID = '".$VareTellingID."'";
        //print $query;
        $result_sum_table1 = $_dbh[$_dsn]->db_query($query);
		    $sum_table1_row = $_dbh[$_dsn]->db_fetch_object($result_sum_table1);

		    while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
            <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td><? print $i; ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Title[]','value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ProjectID[]', 'value'=>0)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Salg[]','class'=>'rightclass','value'=>norway_format($row->Salg))) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop1[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop1))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop2[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop2))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop3[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop3))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop4[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop4))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop5[]','class'=>'rightclass' ,'value'=>norway_format($row->Kjop5))) ?></td>
                      <?if($row->Kjop6 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop6[]', 'value'=>norway_format($row->Kjop6))); }?>
                      <?if($row->Kjop7 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))); }?>
                      <?if($row->Kjop8 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))); }?>
                      <?if($row->Kjop9 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))); }?>
                      <?if($row->Kjop10 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))); }?>
                      <?if($row->Kjop11 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))); }?>
                      <?if($row->Kjop12 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))); }?>
                      <?if($row->Kjop13 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))); }?>
                      <?if($row->Kjop14 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))); }?>
                      <?if($row->Kjop15 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))); }?>

            <td align="right"> 
					  <? 					  
            print norway_format(($row->Salg+$row->Kjop+$row->Kjop1+$row->Kjop2+$row->Kjop3+$row->Kjop4+$row->Kjop5+$row->Kjop6+$row->Kjop7+$row->Kjop8+$row->Kjop9+$row->Kjop10+$row->Kjop11+$row->Kjop12+$row->Kjop13+$row->Kjop14+$row->Kjop15)); ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ExpenseSectionId[]', 'value'=>$row->ExpenseSectionId)) ?>
            </td> 
            </tr>
            <?
        }
        ?>
        <tr>
             <td align="right" colspan="9">
                    <?
                   // if($_lib['sess']->get_person('AccessLevel') >= 3 )
                   // {
							print $_lib['form3']->submit(array('name'=>'action_expense_new'.$row_companies->ExpenseSectionId, 'value'=>'Legg til ny linje', 'tabindex'=>'0', 'accesskey'=>'N', 'OnClick'=>'return createline(1)'));
                   // }
                    ?>
             </td>
             <td colspan="8"></td>
        </tr>
    </table>
    <br /> 
</td>
</tr>
<tr><td>
    <table cellspacing="0" class="lodo_data" style="width:600px;" id="table2">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 2,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
	   <tr class="SubHeading">
      <th class="menu">Heading <?echo $headcount++?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField2[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField3[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField4[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField5[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField6[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField7[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField8[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField9[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField9))) ?></th>
            <?if ($row_heading->vField10 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10));} ?>
            <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
            <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>


   
        </tr>
			<?}?>

        <?
		$i = 0;
        $query = "select * from expense_line Where eType='Table2' AND VareTellingID = '".$VareTellingID."' order by ExpenseLineId asc";
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
            <td><? print $i; ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Title[]', 'value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ProjectID[]', 'value'=>0)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Salg[]','class'=>'rightclass', 'value'=>norway_format($row->Salg))) ?></td>
            <td align="right"><? if ($i == 1) { echo norway_format($sum_table1_row->totkjop); $totkjoparr[] = $sum_table1_row->totkjop;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop[]', 'value'=>norway_format($row->Kjop))) ?></td>
					  <td align="right"><? if ($i == 2) { echo norway_format($sum_table1_row->totkjop1); $totkjoparr[] = $sum_table1_row->totkjop1;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop1[]', 'value'=>norway_format($row->Kjop1))) ?></td>
					  <td align="right"><? if ($i == 3) { echo norway_format($sum_table1_row->totkjop2); $totkjoparr[] = $sum_table1_row->totkjop2;} ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop2[]', 'value'=>norway_format($row->Kjop2))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop3[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop3))) ?></td>
					  <td align="right"><? echo norway_format($totkjoparr[$kjopcounter]+$row->Salg-$row->Kjop3);$kjopcounter++;  ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop4[]', 'value'=>norway_format($row->Kjop4))) ?></td>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop5[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop5))) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop6[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop6))) ?></td>
                      <?if($row->Kjop7 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop7[]', 'value'=>norway_format($row->Kjop7))); }?>
                      <?if($row->Kjop8 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop8[]', 'value'=>norway_format($row->Kjop8))); }?>
                      <?if($row->Kjop9 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop9[]', 'value'=>norway_format($row->Kjop9))); }?>
                      <?if($row->Kjop10 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop10[]', 'value'=>norway_format($row->Kjop10))); }?>
                      <?if($row->Kjop11 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop11[]', 'value'=>norway_format($row->Kjop11))); }?>
                      <?if($row->Kjop12 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop12[]', 'value'=>norway_format($row->Kjop12))); }?>
                      <?if($row->Kjop13 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop13[]', 'value'=>norway_format($row->Kjop13))); }?>
                      <?if($row->Kjop14 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop14[]', 'value'=>norway_format($row->Kjop14))); }?>
                      <?if($row->Kjop15 != ""){?>
					  <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))) ?></td>
					  <? }else{ ?>
					  <? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop15[]', 'value'=>norway_format($row->Kjop15))); }?>

            <td>&nbsp;
					  </td>
            </tr>
            <?
        }
        ?>
        <tr>
                <td align="right" colspan="9">
                    <?
                   // if($_lib['sess']->get_person('AccessLevel') >= 3 )
                   // {
							print $_lib['form3']->submit(array('name'=>'action_expense_new'.$row_companies->ExpenseSectionId, 'value'=>'Legg til ny linje', 'tabindex'=>'0', 'accesskey'=>'N', 'OnClick'=>'return createline(2)'));
                   // }
                    ?>
                </td>
                <td colspan="8"></td>
        </tr>
    </table>
    <br /> 
</td>
</tr>
<tr><td>
    <table cellspacing="0" class="lodo_data" style="width:600px;" id="table3">
			<?	
			$query = "select * from expense_heading where VareTellingID = '".$VareTellingID."' order by iExpHeadingId asc limit 4,2";
			//print $query;
			$result_heading = $_dbh[$_dsn]->db_query($query);
			$headcount = 1;
			while($row_heading = $_dbh[$_dsn]->db_fetch_object($result_heading))
			{
			?>
    <tr class="SubHeading">
      <th class="menu">Heading <?echo $headcount++?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField1[]', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField1))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField2[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField2))) ?></th>
      <th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField3[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField3))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField4[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField4))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField5[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField5))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField6[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField6))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField7[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField7))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField8[]','class'=>'rightclass', 'value'=>iconv("iso-8859-1","utf-8",$row_heading->vField8))) ?></th>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField9[]','class'=>'rightclass', 'value'=>$row_heading->vField9)) ?></th>
            <?if ($row_heading->vField10 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField10[]', 'value'=>$row_heading->vField10));} ?>
            <?if ($row_heading->vField11 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField11[]', 'value'=>$row_heading->vField11));} ?>
            <?if ($row_heading->vField12 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField12[]', 'value'=>$row_heading->vField12));} ?>
            <?if ($row_heading->vField13 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField13[]', 'value'=>$row_heading->vField13));} ?>
            <?if ($row_heading->vField14 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField14[]', 'value'=>$row_heading->vField14));} ?>
            <?if ($row_heading->vField15 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField15[]', 'value'=>$row_heading->vField15));} ?>
            <?if ($row_heading->vField16 != ""){?>
			<th class="menu"><? print $_lib['form3']->text(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16)) ?></th>
			  <? }else{ ?>
			  <? print $_lib['form3']->hidden(array('table'=>$db_table3, 'field'=>'vField16[]', 'value'=>$row_heading->vField16));} ?>


   
        </tr>
			<?}?>

        <?
		    $i = 0;
        $query = "select * from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."' order by ProjectID asc ";
        //print $query;
        $result = $_dbh[$_dsn]->db_query($query);

        $query = "select SUM(Salg) as totsalg, SUM(Kjop) as totkjop, SUM(Kjop1) as totkjop1, SUM(Kjop2) as totkjop2, SUM(Kjop3) as totkjop3, SUM(Kjop4) as totkjop4, SUM(Kjop5) as totkjop5, SUM(Kjop6) as totkjop6, SUM(Kjop7) as totkjop7, SUM(Kjop8) as totkjop8, SUM(Kjop9) as totkjop9, SUM(Kjop10) as totkjop10, SUM(Kjop11) as totkjop11, SUM(Kjop12) as totkjop12, SUM(Kjop13) as totkjop13, SUM(Kjop14) as totkjop14, SUM(Kjop15) as totkjop15 from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."'";
        //print $query;
        $result_sum_table3 = $_dbh[$_dsn]->db_query($query);
		    $sum_table3_row = $_dbh[$_dsn]->db_fetch_object($result_sum_table3);
        //$start = substr($head->Period,0,4)."-01";
        //$end  = $head->Period;
        while($row = $_dbh[$_dsn]->db_fetch_object($result))
        {
           /*$where = ' V.DepartmentID=' . $head->DepartmentID . ' and ';
           $query = "select sum(V.AmountIn) as sumin, sum(V.AmountOut) as sumout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort='400' and V.ProjectID = '".$row->ProjectID."' group by A.ReportShort";
           $compareThisYear = $_lib['storage']->get_row(array('query' => $query));
           $ThisYearSumin = $compareThisYear->sumin - $compareThisYear->sumout;
    
           $query4 = "UPDATE expense_line SET Kjop2 = '".$ThisYearSumin."' where VareTellingID = '".$VareTellingID."' and eType='Table3' and ProjectID = '".$row->ProjectID."' ";
           $update_kjop2 = $_lib['db']->db_query($query4);
    
           $query1 = "select sum(V.AmountIn) as totalin, sum(V.AmountOut) as totalout from voucher V, accountplan as A where V.AccountPlanID=A.AccountPlanID and $where  V.VoucherPeriod<='".$end."' and V.VoucherPeriod>='".$start."' and A.EnableReportShort=1 and A.Active=1 and V.Active=1 and A.ReportShort IN ('300','310','360') and V.ProjectID = '".$row->ProjectID."' group by A.ReportShort";
           $compareThisYearout = $_lib['storage']->get_row(array('query' => $query1));
           $ThisYearSumout = $compareThisYearout->totalin - $compareThisYearout->totalout;
    
           $query4 = "UPDATE expense_line SET Kjop4 = '".$ThisYearSumout."' where VareTellingID = '".$VareTellingID."' and eType='Table3' and ProjectID = '".$row->ProjectID."' ";
           $update_kjop4 = $_lib['db']->db_query($query4);*/
                          
           /*$sql_amtin = "Select SUM(AmountIn) as amin,SUM(AmountOut) as amout from voucher v,accountplan as A where v.VoucherPeriod >= '".$head->Period."'  and A.ReportShort = '400' AND v.ProjectID = '".$row->ProjectID."'";
           $amtin = $_lib['storage']->get_row(array('query' => $sql_amtin));
           $sum_amtin = round($amtin->amin, 2);
           $sum_amtout = round($amtin->amout, 2);*/ 
        
        
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
         <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td><? print $i;?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>$row->eType)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Title[]', 'value'=>iconv("iso-8859-1","utf-8",$row->Title))) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ProjectID[]', 'value'=>$row->ProjectID)) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Salg[]','class'=>'rightclass', 'value'=>norway_format($row->Salg))) ?></td>
            <td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop))) ?></td>
					  <td align="right"><? print norway_format($row->Salg-$row->Kjop) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop1[]', 'value'=>norway_format($row->Kjop1)));?></td>
					  <!--<td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop2[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop2))) ?></td>-->
					  <td align="right"><? print norway_format($row->Kjop2)?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop2[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop2))) ?></td>
            <td align="right"><? print norway_format(($row->Salg-$row->Kjop)+$row->Kjop2) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop3[]', 'value'=>norway_format($row->Kjop3)));?></td>				            
					  <!--<td><? print $_lib['form3']->text(array('table'=>$db_table2, 'field'=>'Kjop4[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop4))) ?></td>-->
					  <td align="right"><? print norway_format($row->Kjop4)?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop4[]','class'=>'rightclass', 'value'=>norway_format($row->Kjop4))) ?></td>
            <td align="right"><? print norway_format($row->Kjop4-(($row->Salg-$row->Kjop)+$row->Kjop2)) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'Kjop5[]', 'value'=>norway_format($row->Kjop5)));?></td>					  
            <td align="right"><? print norway_format((100/($row->Salg-$row->Kjop+$row->Kjop2))*$row->Kjop4) ?></td>
            <!--<td>&nbsp;</td>-->
         </tr>
            <?        
        }
        ?>                        
        <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td>&nbsp;</td>
            <td>Sum</td>
            <td align="right"><? print norway_format($sum_table3_row->totsalg) ?></td>
            <td align="right"><? print norway_format($sum_table3_row->totkjop) ?></td>
					  <td align="right"><? print norway_format($sum_table3_row->totsalg-$sum_table3_row->totkjop) ?></td>
					  <td align="right"><? print norway_format($sum_table3_row->totkjop2) ?></td>
					  <td align="right"><? print norway_format(($sum_table3_row->totsalg-$sum_table3_row->totkjop)+$sum_table3_row->totkjop2) ?></td>
					  <td align="right"><? print norway_format($sum_table3_row->totkjop4) ?></td>
					  <td align="right"><? print norway_format($sum_table3_row->totkjop4-(($sum_table3_row->totsalg-$sum_table3_row->totkjop)+$sum_table3_row->totkjop2)) ?></td>					  
            <td align="right"><? print norway_format((100/($sum_table3_row->totsalg-$sum_table3_row->totkjop+$sum_table3_row->totkjop2))*$sum_table3_row->totkjop4) ?></td>					  
            <!--<td align="right"><? print norway_format((100/($sum_table3_row->totsalg-$sum_table3_row->totkjop+$sum_table3_row->totkjop2))*($sum_table3_row->totkjop4-($sum_table3_row->totsalg-$sum_table3_row->totkjop+$sum_table3_row->totkjop2))) ?></td>-->
        </tr>
        <tr>
                <td align="right" colspan="9">
                    <?
                   // if($_lib['sess']->get_person('AccessLevel') >= 3 )
                   // {
 						//	print $_lib['form3']->submit(array('name'=>'action_expense_new'.$row_companies->ExpenseSectionId, 'value'=>'Legg til ny linje', 'tabindex'=>'0', 'accesskey'=>'N', 'OnClick'=>'return createline(3)'));
                   // }
                    ?>
                </td>
                <td colspan="8"></td>
        </tr>
    </table>
    <br /> 
</td>
</tr>
<tr><td>
    <table cellspacing="0" class="lodo_data" style="width:1000px;" id="table4">
    <tr class="SubHeading">
      <th class="menu" width="1%">Heading1</th>
      <th class="menu" width="12%">Pris pr liter</th>
      <th class="menu" width="13%"><? print $row_heading->vField2 ?></th>
      <th class="menu" width="12%"><? print $row_heading->vField3 ?></th>
			<th class="menu" width="14%"><? print $row_heading->vField4 ?></th>
			<th class="menu" width="12%"><? print $row_heading->vField5 ?></th>
			<th class="menu" width="12%"><? print $row_heading->vField6 ?></th>
			<th class="menu" width="12%"><? print $row_heading->vField7 ?></th>
			<th class="menu" width="12%"><? print $row_heading->vField8 ?></th>
			<th class="menu" width="12%"><? print $row_heading->vField9 ?></th>
      <th class="menu" width="10%"><? print $row_heading->vField10 ?></th>
		</tr>
    <tr class="SubHeading">
      <th class="menu">Heading1</th>
      <th class="menu"></th>
      <th class="menu" align="right">01-Jan</th>
      <th class="menu" align="right">31-Dec</th>
			<th class="menu"><? print $row_heading->vField4 ?></th>
			<th class="menu"><? print $row_heading->vField5 ?></th>
			<th class="menu"><? print $row_heading->vField6 ?></th>
			<th class="menu"><? print $row_heading->vField7 ?></th>
			<th class="menu"><? print $row_heading->vField8 ?></th>
			<th class="menu"><? print $row_heading->vField9 ?></th>
      <th class="menu"><? print $row_heading->vField10 ?></th>
		</tr>


			     <?
		    $i = 0;
        $query = "select Title from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."' order by ProjectID asc ";
        //print $query;
        $result_t3 = $_dbh[$_dsn]->db_query($query);

        $query = "select Salg,Title,Kjop,Kjop3,Kjop2,Kjop4 from expense_line Where eType='Table2' AND VareTellingID = '".$VareTellingID."' order by ExpenseLineId asc ";
        //print $query;
        $result2 = $_dbh[$_dsn]->db_query($query);
        
         //$start = substr($head->Period,0,4)."-01";
        //$end  = $head->Period;
        while($row = $_dbh[$_dsn]->db_fetch_object($result2))
        {
         
            $query = "select Salg,Title,Kjop,Kjop3,Kjop2,Kjop4 from expense_line Where eType='Table3' AND VareTellingID = '".$VareTellingID."' AND Title = '".$row->Title."'";
            //print $query;
            $result3 = $_dbh[$_dsn]->db_query($query);

            while($row1 = $_dbh[$_dsn]->db_fetch_object($result3))
           {
            $i++; $form_name = "person_$i";
            if (!($i % 2)) { $sec_color = "BGColorlight"; } else { $sec_color = "BGColorDark"; };
            ?>
         <tr class="<? print $sec_color ?>" id="exprow<? print $i.$row_companies->ExpenseSectionId ?>">
            <td><? print $i;?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'eType[]', 'value'=>'Table4')) ?></td>
            <td><? print iconv("iso-8859-1","utf-8",$row->Title) ?><? print $_lib['form3']->hidden(array('table'=>$db_table2, 'field'=>'ProjectID[]', 'value'=>$row->ProjectID)) ?></td>
            <td><? print norway_format($row1->Salg/$row->Salg) ?></td>
            <td><? print norway_format($row1->Kjop/$row->Kjop3) ?></td>
					  <td></td>
					  <td align="right"><? if($row1->Title == "Brennevin"){ echo norway_format($row1->Kjop2/$sum_table1_row->totkjop2); }else if($row1->Title == "Øl"){ echo norway_format($row1->Kjop2/$sum_table1_row->totkjop); }else if($row1->Title == "Vin"){ echo norway_format($row1->Kjop2/$sum_table1_row->totkjop1); }  ?></td>
					  <td align="right"><? if($row1->Title == "Brennevin"){ echo norway_format(($row1->Salg-$row1->Kjop+$row1->Kjop2)/$sum_table1_row->totkjop2);}else if($row1->Title == "Øl"){ echo norway_format(($row1->Salg-$row1->Kjop+$row1->Kjop2)/$sum_table1_row->totkjop);}else if($row1->Title == "Vin"){ echo norway_format(($row1->Salg-$row1->Kjop+$row1->Kjop2)/$sum_table1_row->totkjop1);}?></td>				            
					  <td align="right"><? if($row1->Title == "Brennevin"){ echo norway_format($row1->Kjop4/($row->Salg+$sum_table1_row->totkjop2-$row->Kjop3));}else if($row1->Title == "Øl"){ echo norway_format($row1->Kjop4/($row->Salg+$sum_table1_row->totkjop-$row->Kjop3));}else if($row1->Title == "Vin"){ echo norway_format($row1->Kjop4/($row->Salg+$sum_table1_row->totkjop1-$row->Kjop3));} ?></td>
					  <td align="right"></td>					  
            <td align="right"></td>
            <!--<td>&nbsp;</td>-->
            <td></td>
         </tr>
            <?        
        } }
        ?>                        
       

     </table>
</td>
</tr>	  
<tr><td align="center"><? print $_lib['form3']->submit(array('name'=>'action_submit_new', 'value'=>'Update', 'tabindex'=>'0', 'accesskey'=>'N'));?>&nbsp;&nbsp;&nbsp;<? print $_lib['form3']->submit(array('name'=>'action_print_new', 'value'=>'Skriv ut', 'tabindex'=>'0', 'accesskey'=>'N','OnClick'=>"window.open('lodo.php?t=report.expenseprint&VareTellingID=$VareTellingID','Report Print','width=900,height=600,resizable=yes,scrollbars=yes');return false;"));?></td></tr>
<tr><td>&nbsp;</td></tr>


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

          var table = document.getElementById("table"+secid);//alert(table);return false;
 
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
			if(secid == 2){
            	element2.type = "hidden";
			}else{
            	element2.type = "text";
			}
            element2.name = "expense_line_Kjop2[]";
            cell4.appendChild(element2);
		
				
	          var cell4 = row.insertCell(6);
            var element2 = document.createElement("input");
      if(secid == 3){
            element2.type = "hidden";
			}else{      
            element2.type = "text";
      }      
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
      if(secid == 2){
            element2.type = "text";
      }else{
            element2.type = "hidden";
      }      
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

            var cell4 = row.insertCell(20);
            var element2 = document.createElement("input");
            element2.type = "hidden";
            element2.name = "expense_line_ProjectID[]";
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



