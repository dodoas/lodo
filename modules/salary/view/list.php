<?
# $Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$SalaryID = $_REQUEST['SalaryID'];
$SalaryConfID = $_REQUEST['SalaryConfID'];

includelogic('accounting/accounting');
$accounting = new accounting();
require_once "record.inc";

$query_setup    = "select name, value from setup where Name like '%salary%'";
$setup          = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC") { $SORT = "DESC"; } else { $SORT = "ASC"; }
if(!$_SETUP[DB_START][0]) { $_SETUP['DB_START']['0'] = 0; }
if(!$CompanyID)   { $CompanyID = 1; }
if (!$order_by)   { $order_by  = "AccountNumber"; }
$db_stop = $_SETUP['DB_START']['0'] + $_SETUP['DB_OFFSET']['0'];


$year = $_lib['date']->get_this_year($_lib['sess']->get_session('LoginFormDate'));

##################################
#  Check that default values exist
#
    $query = "select SalaryConfID, AccountPlanID from salaryconf where SalaryConfID=1";
    $row = $_lib['storage']->get_row(array('query' => $query));
    if($row->SalaryConfID != 1)
    {
        $query_salconf = "insert into salaryconf (SalaryConfID, CreatedByPersonID) values (1, " . $_lib['sess']->get_person('PersonID') . ")";
        $_lib['db']->db_query3(array('query'=>$query_salconf, 'do_not_die'=>'1'));
    }
#
##################################

/* S&oslash;kestreng */
$query_salary   = "select S.AmountThisPeriod, S.JournalID, S.ValidFrom as FromDate, S.ValidTo as ToDate, A.AccountPlanID, A.AccountName, S.PayDate, S.DomesticBankAccount, S.TS, S.SalaryID, S.JournalDate, S.Period from salary as S, accountplan as A where S.AccountPlanID=A.AccountPlanID order by S.JournalID desc";
$result_salary  = $_lib['db']->db_query($query_salary);

$query_conf_head= "select * from salaryconf as S where S.SalaryConfID=1";
$row_head = $_lib['storage']->get_row(array('query' => $query_conf_head));

$query_conf     = "select *, A.KommuneID as NoKommune from salaryconf as S, accountplan as A, kommune as K where S.AccountPlanID=A.AccountPlanID and (A.KommuneID=K.KommuneID or (A.KommuneID = 0 and K.KommuneID = 1) ) and S.SalaryConfID!=1 order by AccountName asc";
print "$query_conf<br>\n";
$result_conf    = $_lib['db']->db_query($query_conf);


$period_open = ($accounting->is_valid_accountperiod($setup['salarydefvoucherperiod'], $_lib['sess']->get_person('AccessLevel'))) ? true : false;


print $_lib['sess']->doctype;

?>

<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>

    <script>
function changemonth()
{
        var voucherdate = document.getElementById('setup.value.salarydefvoucherdate');
        var voucherperiod = document.getElementById('setup.value.salarydefvoucherperiod');
	var fromdate = document.getElementById('setup.value.salarydeffromdate');
	var todate   = document.getElementById('setup.value.salarydeftodate');
	var select   = document.getElementById('month');

	var selectedmonth = select.value;
	if(selectedmonth.length < 2)
		selectedmonth = "0" + selectedmonth; 

	var monthlengths = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
        var date = new Date();

	monthlength = monthlengths[ parseInt(select.value) - 1 ];
	year = date.getFullYear();

	/* leap year */
	if(parseInt(select.value) == 2)
	{
		if(year % 400 == 0)
			monthlength += 1;
		else if(year % 4 == 0 && year % 100 != 0)
			monthlength += 1;
	}

        voucherperiod.value = "" + year + "-" + selectedmonth;
        voucherdate.value = voucherperiod.value + "-" + monthlength;
	fromdate.value = voucherperiod.value + "-01";
	todate.value = voucherperiod.value + "-" + monthlength;
}
    </script>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="salary_new" action="<? print $_lib['sess']->dispatch ?>t=salary.list" method="post">
    <table class="lodo_data">
    <tr>
        <th colspan="5">Standardverdier for nye lønninger</th>
    <tr>
        <th class="sub">M&aring;ned</th>
        <th class="sub">Bilagsdato</th>
        <th class="sub">Periode</th>
        <th class="sub">Fra dato</th>
        <th class="sub">Til dato</th>
        <th class="sub"></th>
        <th class="sub"></th>
    </tr>
    <tr> 
        <td> 
        <?
            $months = array(1 => 'Januar', 2 => 'Februar', 3 => 'Mars', 4=> 'April', 5=> 'Mai', 
				6 => 'Juni', 7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 
				11 => 'November', 12 => 'Desember');

	    list($dummy, $month) = explode('-', $setup['salarydefvoucherdate']);
	    $month_selected = (int)$month;

	    printf("<select id='month' onchange='changemonth();' />");
	    foreach($months as $n => $month) {
	        if($n == $month_selected)
	            printf("<option value='%d' selected>%s</option>\n", $n, $month);
		else
	            printf("<option value='%d'>%s</option>\n", $n, $month);
	    }
	    printf("</select>");
	?>
        </td>
        
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydefvoucherdate', 'value'=>$setup['salarydefvoucherdate'], 'width'=>'10')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydefvoucherperiod', 'value'=>$setup['salarydefvoucherperiod'], 'width'=>'10')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydeffromdate', 'value'=>$setup['salarydeffromdate'], 'width'=>'10')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydeftodate', 'value'=>$setup['salarydeftodate'], 'width'=>'10')) ?></td>
        <td>
        <? if(!$period_open) { print "Perioden er stengt"; } ?>
        </td>
        <td><? print $_lib['form3']->submit(array('name'=>'action_defconf_save', 'value'=>'Lagre verdier', 'accesskey'=>'S')) ?></td>
    </tr>
    </table>
</form>

<? if($period_open) {

/*
 * tape-function to reuse code
 */
function worker_line($row, $i) {
  global $_lib, $accounting, $setup;

  $i++;
  if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
?>
  <tr class="<? print "$sec_color"; ?>">
      <td>
      <?
        if($row->SalaryConfID != 1)
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountPlanID ?></a><?
        }
      ?>
      </td>
      <td>
      <?
        if($row->SalaryConfID == 1)
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row->SalaryConfID ?>"><b>Hovedmal</b></a><?
        }
        else
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row->SalaryConfID ?>&amp;action_salarysubconf_enter=1"><? print $row->AccountName ?></a><?
        }
      ?>
      </td>

      <td style="background-color: yellow"> 
      <?
        $sql = sprintf("SELECT * FROM salaryinfo WHERE SalaryConfID = %d", $row->SalaryConfID);
        $salaryinfo = $_lib['storage']->get_row(array('query' => $sql));

        if($salaryinfo === null) 
        {
          $checked = false; 
        } 
        else 
        { 
          $checked = true;
        } 
      ?>
      <input type="checkbox" name="recieveSalary[]" value="<? echo $row->SalaryConfID ?>" <? echo $checked ? "checked" : "" ?> />
      <input type="hidden" name="existingSalaryLines[]" value="<? echo $row->SalaryConfID ?>" />
      <?
        if($checked) {
            printf('<input type="text" size="50" name="salaryinfo_amount_%d" value="%s" />', $row->SalaryConfID, 
                    $salaryinfo->amount);
            $_lib['form2']->project_menu2(array('table' => 'salaryinfo',  'field' =>  
'project', 'value' => $salaryinfo->project, 'tabindex' => $tabindex++, 'accesskey' => 'P', 'pk' => $row->SalaryConfID));
        }
      ?>
      </td>

      <td style="background-color: yellow">
      <?
      if($_lib['sess']->get_person('AccessLevel') >= 2 && $accounting->is_valid_accountperiod($setup['salarydefvoucherperiod'], $_lib['sess']->get_person('AccessLevel')))
      {
        if($row->SalaryConfID != 1 && $checked)
        {
            ?><a target="_blank" href="<? print $_lib['sess']->dispatch ?>t=salary.edit&amp;SalaryConfID=<? print $row->SalaryConfID ?>&amp;action_salary_new=1" class="button"><? if($row->SalaryConfID != 1) { print /* $row->AccountPlanID */ "Lage l&oslash;nnslipp"; } ?></a><?
        }
      } else {
        print "Stengt";
      }
      ?>
      </td>

      <td>
      <?
        if($row->SalaryConfID != 1)
        {
          if($row->NoKommune == 0) 
          {
            ?>
            <span style="color: red;">Ingen kommune valgt!</span>
            <?
          }
          else
          {
            ?>
          <a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.edit"><? print $row->KommuneName; ?></a></td>
            <?
          }
        }
      ?>

      <td><?= $row->WorkStart ?></td>
      <td><?= $row->WorkStop ?></td>

      <td><a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.salaryreport&amp;report.Year=<? print $year ?>&amp;report.Employee=<? print $row->AccountPlanID ?>" target="new">Vis <? print $year ?></a></td>


      <? if(($_lib['sess']->get_person('AccessLevel') >= 3)) { ?>
      <td colspan="1">
      <? } else { ?>	    
      <td colspan="1">
      <? } ?>
      
	<? if(($_lib['sess']->get_person('AccessLevel') >= 4) and ($row->SalaryConfID != 1)) { 
	    echo $_lib['form3']->button(array('url' => 
				 $_lib['sess']->dispatch . "t=salary.list&amp;SalaryConfID=" . $row->SalaryConfID . "&amp;action_salaryconf_delete=1"
				 , 'name' => 'Slett', 'confirm' => 'Vil du virkelig slette linjen?'));
 	   } ?>
      </td>

      <td style="text-align: right">
      <?
        echo $row->SalaryConfID;
      ?>
      </td>

      <td> 
        <a href="<? print $_lib['sess']->dispatch ?>t=timesheets.list&AccountPlanID=<?= $row->AccountPlanID ?>&Username=<?= $row->AccountName ?>">timeliste</a>
      </td>
    
<? 
}  /* function */

$current_workers = array();
$old_workers = array();

while($row = $_lib['db']->db_fetch_object($result_conf)) {
    if($row->WorkStop != '0000-00-00' && strtotime($row->WorkStop) < time()) {
        $old_workers[] = $row;
    }
    else {
        $current_workers[] = $row;
    }
}
?>

<br><br>
<table class="lodo_data">
<tr>
    <th colspan="11">Hovedmal hele firma</th>
</tr>
<tr>
    <th class="sub">Ansatte</th>
    <th class="sub">Navn</th>
    <th class="sub" style="background-color: yellow; color: black;">Merk</th>
    <th class="sub" style="background-color: yellow; color: black;">L&oslash;nnslipp</th>
    <th class="sub">Kommune</th>
    <th class="sub">Startdato</th>
    <th class="sub">Sluttdato</th>
    <th class="sub">L&T</th>
    <th class="sub"></th>
    <th class="sub">Mal</th>
    <th class="sub"></th>
</tr>
<tr>
    <td>
    </td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row_head->SalaryConfID ?>"><b>Hovedmal</b></a></td>
    <td style="background-color: yellow"></td>
    <td style="background-color: yellow"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3"></td>
</tr>

<tbody>
   <form name="salary_save_info" action="<? print $_lib['sess']->dispatch ?>t=salary.list" method="post">
   <? 
     $i = 1;
     foreach($current_workers as $row) {
       worker_line($row, $i);
       $i++;
     }
  ?>
  <tr>
    <td colspan="2">
    </td>
    <td style="background-color: yellow">
      <input type="checkbox" disabled checked>
      <input name="salary_save_info" type="submit" value="Disse f&aring;r l&oslash;nn" />
    </td>
    <td style="background-color: yellow"></td>
  </tr>
  </form>
</tbody>
<tr>
  <td colspan="6"></td>
  <td colspan="2">
  </td>
  <td></td>
</tr>
</table>

<?

///
/// OLD WORKERS:
///

if(!isset($_GET['view_old_workers'])) {
   echo '<br><br><a href="' . $_lib['sess']->dispatch . 't=salary.list&view_old_workers">Vis tidligere ansatte</a><br><br>';
}
else {
   echo '<br><br><a href="' . $_lib['sess']->dispatch . 't=salary.list">Skjul tidligere ansatte</a><br><br>';

?>
<table class="lodo_data">
<tr>
    <th colspan="11">Tidligere ansatte</th>
</tr>

<tr>
    <th class="sub">Ansatte</th>
    <th class="sub">Navn</th>
    <th class="sub" style="background-color: yellow; color: black;">Merk</th>
    <th class="sub" style="background-color: yellow; color: black;">L&oslash;nnslipp</th>
    <th class="sub">Kommune</th>
    <th class="sub">Startdato</th>
    <th class="sub">Sluttdato</th>
    <th class="sub">L&T</th>
    <th class="sub"></th>
    <th class="sub">Mal</th>
    <th class="sub"></th>
</tr>
<tr>
    <td>
  <?
  if(($_lib['sess']->get_person('AccessLevel') >= 4))
  {
    ?>
    <form name="salary_new" action="<? print $_lib['sess']->dispatch ?>t=salary.template" method="post">
      <input type="submit" name="action_salaryconf_new" value="Ny ansatt (N)" accesskey="N" />
    </form>
    <?
  }
  ?>
    </td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row_head->SalaryConfID ?>"><b>Hovedmal</b></a></td>
    <td style="background-color: yellow"></td>
    <td style="background-color: yellow"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3"></td>

</tr>

<tbody>
   <form name="salary_save_info" action="<? print $_lib['sess']->dispatch ?>t=salary.list&view_old_workers" method="post">
   <? 
     $i = 1;
     foreach($old_workers as $row) {
       worker_line($row, $i);
     }
  ?>
  <tr>
    <td colspan="2">
    </td>
    <td style="background-color: yellow">
      <input type="checkbox" disabled checked>
      <input name="salary_save_info" type="submit" value="Disse f&aring;r l&oslash;nn" />
    </td>
    <td style="background-color: yellow"></td>
  </tr>
  </form>
</tbody>
<tr>
  <td colspan="6"></td>
  <td colspan="2">
  </td>
  <td></td>
</tr>
</table>

<?php
}
?>

<br />
<table class="lodo_data">
<thead>
   <tr>
     <th colspan="12">L&oslash;nnsutbetalinger
  <tr>
    <th class="sub">Nr</th>
    <th class="sub">Dato</th>
    <th class="sub">Periode</th>
    <th class="sub">Ansatt</th>
    <th class="sub">Navn</th>
    <!-- <th class="sub">Netto</th> -->
    <th class="sub">Utbetalt dato</th>
    <th class="sub">Fra perioden</th>
    <th class="sub">Til perioden</th>
    <th class="sub">Bankkonto</th>
    <th class="sub">Utskrift</th>
    <th class="sub"></th>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_salary))
{
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
        <td>L <a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->JournalDate ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->Period ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountPlanID ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->AccountName ?></a></td>
        <!-- <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Amount(array('value'=>$row->AmountThisPeriod, 'return'=>'value')) ?></a></td> -->
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->PayDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->FromDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $_lib['format']->Date(array('value'=>$row->ToDate, 'return'=>'value')) ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&SalaryID=<? print $row->SalaryID ?>"><? print $row->DomesticBankAccount ?></a></td>
        <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.print&SalaryID=<? print $row->SalaryID ?>" target="print">Vis</a></td>
        <td>
          <? if($_lib['sess']->get_person('AccessLevel') > 3) {
            if($accounting->is_valid_accountperiod($row->Period, $_lib['sess']->get_person('AccessLevel')))
            {
/*<a href="<? print $_lib['sess']->dispatch ?>t=salary.list&SalaryID=<? print $row->SalaryID ?>&action_salary_delete=1" class="button">Slett</a>*/

		echo $_lib['form3']->button(array('url' => 
						  $_lib['sess']->dispatch . "t=salary.list&SalaryID=" . $row->SalaryID . "&action_salary_delete=1",
						  'name' => 'Slett', 'confirm' => 'Vil du virkelig slette linjen?'));
            }
            else
            {
                print "Stengt";
            }
          }
        ?>
        </td>
    </tr>
    <?
}
?>
</tbody>
</table>

<? } /* if($period_open) */ ?>

</body>
</html>
