<?
# $Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$SalaryID = $_REQUEST['SalaryID'];
$SalaryConfID = $_REQUEST['SalaryConfID'];
$SalaryperiodconfID = $_REQUEST['SalaryperiodconfID'];

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
$result_conf    = $_lib['db']->db_query($query_conf);

$current_period = null;
if($SalaryperiodconfID)
{
    $period_query = sprintf("SELECT Period FROM salaryperiodconf WHERE SalaryperiodconfID = %d", $SalaryperiodconfID);
    $period_result = $_lib['db']->db_query($period_query);
    $period_row = $_lib['db']->db_fetch_assoc($period_result);

    $period_open = ($accounting->is_valid_accountperiod($period_row['Period'], $_lib['sess']->get_person('AccessLevel'))) ? true : false;

    $current_period = $period_row['Period'];

    $entry_query = sprintf("SELECT * FROM salaryperiodentries WHERE SalaryperiodconfID = %d", $SalaryperiodconfID);
    $entry_result = $_lib['db']->db_query($entry_query);
    $entries = array();
  
    while($row = $_lib['db']->db_fetch_assoc($entry_result))
    {
        if(!isset($entries[ $row['AccountPlanID'] ]))
            $entries[ $row['AccountPlanID'] ] = array();

        $entries[ $row['AccountPlanID'] ][] = $row;
    }
}
else
{
    $period_open = false;
}


print $_lib['sess']->doctype;

?>

<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>

</head> 
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<?

  $period_query = "SELECT SalaryperiodconfID, Name, Year FROM salaryperiodconf ORDER BY Year, SalaryperiodconfID";
  $period_result = $_lib['db']->db_query($period_query);

?>
<div>
  <form action="<?= $_lib['sess']->dispatch ?>&t=salary.list" method="post">
  <select name="SalaryperiodconfID">
  <?
    $last = array('Year' => 0, 'Month' => 0);
    $month = date('n');
    $year  = date('Y');
    while( $row = $_lib['db']->db_fetch_assoc($period_result) )
    {
        if($row['Year'] != $last['Year'])
        {
           $last['Year'] = $row['Year'];
           $last['Month'] = 0;
        }

        $last['Month'] ++;

        if( $SalaryperiodconfID == $row['SalaryperiodconfID'] || 
           (!$SalaryperiodconfID && $last['Year'] == $year && $last['Month'] == $month) )
        { 
            $selected = "selected";
        }
        else
        {
            $selected = "";
        }
  
        printf("<option value=\"%d\" %s>%s - %s</option>", 
               $row['SalaryperiodconfID'], $selected, $row['Year'], $row['Name']);
    }
  ?>
  </select>
  <input type="submit" value="Velg periode" />
  </form>
  <a href="<?= $_lib['sess']->dispatch ?>t=salary.config">Konfigurasjon</a>
</div>

<? 

if(!$SalaryperiodconfID) 
{
    echo "Ingen periode er valgt";
}
else if(!$period_open) 
{
    echo "Perioden er stengt";
}
else 
{

/*
 * tape-function to reuse code
 */
function worker_line($row, $i) {
    global $_lib, $accounting, $setup, $entries, $SalaryperiodconfID, $current_period, $period_open;

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
            ?><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.employee&accountplan_AccountPlanID=<? print $row->AccountPlanID ?>"><? print $row->AccountName ?></a><?
        }
      ?>
      </td>

      <td style="text-align: right">
      <?
            ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row->SalaryConfID ?>&amp;action_salarysubconf_enter=1"><? print $row->SalaryConfID ?></a><?
      ?>
      </td>

      <td style="background-color: yellow"> 
      <?
        $sql = sprintf("SELECT * FROM salaryinfo WHERE SalaryConfID = %d AND SalaryperiodconfID = %d", $row->SalaryConfID, $SalaryperiodconfID);
        $salaryinfo = $_lib['storage']->get_row(array('query' => $sql));

        if($salaryinfo === null) 
        {
            $checked = false; 
        } 
        else 
        { 
            $checked = true;
        } 

        if(!$checked && isset($entries[ $row->AccountPlanID ]))
        {
            foreach($entries[ $row->AccountPlanID ] as $a) 
            {
                if($a['Processed'] == 0)
                {
                    $checked = true;
                    break;
                }
            }
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

      if(!empty($current_period) && $_lib['sess']->get_person('AccessLevel') >= 2 && $period_open)
      {
        if($row->SalaryConfID != 1 && $checked)
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&amp;SalaryConfID=<? print $row->SalaryConfID ?>&amp;action_salary_new=1&amp;SalaryperiodconfID=<? print $SalaryperiodconfID ?>" class="button"><? if($row->SalaryConfID != 1) { print /* $row->AccountPlanID */ "Lage l&oslash;nnslipp"; } ?></a><?
        }
      } else {
        print "Stengt";
      }
      ?>
      </td>

      <td>
        <?
          if(isset($entries[ $row->AccountPlanID ]))
          {
              foreach($entries[ $row->AccountPlanID ] as $a)
              {
                  if($a['Processed'] != 0)
                  {
                      printf("<a href='%s&t=salary.edit&SalaryID=%d'>L%d</a> ",
                             $_lib['sess']->dispatch, $a['SalaryID'], $a['JournalID']);
                  }
              }          
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
<form name="salary_save_info" action="<? print $_lib['sess']->dispatch ?>t=salary.list&SalaryperiodconfID=<?= $SalaryperiodconfID ?>" method="post">
<table class="lodo_data">
<tr>
    <th colspan="12">Hovedmal hele firma</th>
</tr>
<tr>
    <th class="sub">Ansatte</th>
    <th class="sub">Navn</th>
    <th class="sub">
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row_head->SalaryConfID ?>"><b>Hovedmal</b></a>
    </th>
    <th class="sub" style="background-color: yellow; color: black;">
      <input type="checkbox" disabled checked>
      <input name="salary_save_info" type="submit" value="Disse f&aring;r l&oslash;nn / Lagre opplysninger" />
      Valgt periode: <?= $period_row['Period'] ?>
    </th>
    <th class="sub" style="background-color: yellow; color: black;">L&oslash;nnslipp</th>
    <th class="sub">L&oslash;nninger</th>
    <th class="sub">Kommune</th>
    <th class="sub">Startdato</th>
    <th class="sub">Sluttdato</th>
    <th class="sub">L&T</th>
    <th class="sub"></th>
    <th class="sub"></th>
</tr>
<tr>
    <td></td>
    <td></td>

    <td>
    </td>
    <td style="background-color: yellow">
    </td>
    <td style="background-color: yellow"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3"></td>
</tr>

<tbody>
   <? 
     $i = 1;
     foreach($current_workers as $row) {
       worker_line($row, $i);
       $i++;
     }
  ?>
</tbody>
<tr>
  <td colspan="6"></td>
  <td colspan="2">
  </td>
  <td></td>
</tr>
</table>
</form>

<?

///
/// OLD WORKERS:
///

if(!isset($_GET['view_old_workers'])) {
   echo '<br><br><a href="' . $_lib['sess']->dispatch . 't=salary.list&view_old_workers&SalaryperiodconfID=' . $SalaryperiodconfID . '">Vis tidligere ansatte</a><br><br>';
}
else {
   echo '<br><br><a href="' . $_lib['sess']->dispatch . 't=salary.list&SalaryperiodconfID=' . $SalaryperiodconfID . '">Skjul tidligere ansatte</a><br><br>';

?>

<form name="salary_save_info" action="<? print $_lib['sess']->dispatch ?>t=salary.list&SalaryperiodconfID=<?= $SalaryperiodconfID ?>&view_old_workers" method="post">
<table class="lodo_data">
<tr>
    <th colspan="12">Tidligere ansatte</th>
</tr>

<tr>
    <th class="sub">Ansatte</th>
    <th class="sub">Navn</th>
    <th class="sub">
      <a href="<? print $_lib['sess']->dispatch ?>t=salary.template&amp;SalaryConfID=<? print $row_head->SalaryConfID ?>"><b>Hovedmal</b></a>
    </th>
    <th class="sub" style="background-color: yellow; color: black;">
      <input type="checkbox" disabled checked>
      <input name="salary_save_info" type="submit" value="Disse f&aring;r l&oslash;nn / Lagre opplysninger" />

    </th>
    <th class="sub" style="background-color: yellow; color: black;">L&oslash;nnslipp</th>
    <th class="sub">L&oslash;nninger</th>
    <th class="sub">Kommune</th>
    <th class="sub">Startdato</th>
    <th class="sub">Sluttdato</th>
    <th class="sub">L&T</th>
    <th class="sub"></th>
    <th class="sub"></th>
</tr>
<tr>
    <td></td>
    <td></td>

    <td>
    </td>
    <td style="background-color: yellow">
    </td>
    <td style="background-color: yellow"></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="3"></td>
</tr>

<tbody>
   <? 
     $i = 1;
     foreach($old_workers as $row) {
       worker_line($row, $i);
     }
  ?>
</tbody>
<tr>
  <td colspan="6"></td>
  <td colspan="2">
  </td>
  <td></td>
</tr>
</table>
</form>

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
