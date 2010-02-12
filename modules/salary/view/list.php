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

$query_conf     = "select * from salaryconf as S, accountplan as A, kommune as K where S.AccountPlanID=A.AccountPlanID and A.KommuneID=K.KommuneID and S.SalaryConfID!=1 order by AccountName asc";
#print "$query_conf<br>\n";
$result_conf    = $_lib['db']->db_query($query_conf);

print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - salary list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.49 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<form name="salary_new" action="<? print $_lib['sess']->dispatch ?>t=salary.list" method="post">
    <table class="lodo_data">
    <tr>
        <th colspan="5">Standardverdier for nye lønninger</th>
    <tr>
        <th class="sub">Bilagsdato</th>
        <th class="sub">Fra dato</th>
        <th class="sub">Til dato</th>
        <th class="sub"></th>
        <th class="sub"></th>
    </tr>
    <tr>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydefvoucherdate', 'value'=>$setup['salarydefvoucherdate'], 'width'=>'10')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydeffromdate', 'value'=>$setup['salarydeffromdate'], 'width'=>'10')) ?></td>
        <td><? print $_lib['form3']->text(array('table'=>'setup.value', 'field'=>'salarydeftodate', 'value'=>$setup['salarydeftodate'], 'width'=>'10')) ?></td>
        <td>
        <? if(!$accounting->is_valid_accountperiod($setup['salarydefvoucherdate'], $_lib['sess']->get_person('AccessLevel'))) { print "Perioden er stengt"; } ?>
        </td>
        <td><? print $_lib['form3']->submit(array('name'=>'action_defconf_save', 'value'=>'Lagre verdier', 'accesskey'=>'S')) ?></td>
    </tr>
    </table>
</form>
<br><br>
<table class="lodo_data">
<tr>
    <th colspan="9">Hovedmal hele firma</th>
</tr>
<tr>
    <th class="sub">Ansatt/konto</th>
    <th class="sub">Navn/mal</th>
    <th class="sub">Ny l&oslash;nn</th>
    <th class="sub">Kommune</th>
    <th class="sub">Arb.avg.</th>
    <th class="sub">Sist endret</th>
    <th class="sub" colspan="3"></th>
</tr>
<tr>
    <td>
  <?
  if(($_lib['sess']->get_person('AccessLevel') >= 1))
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
    <td style="background-color: #FFB600"></td>
    <td></td>
    <td></td>
    <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&SalaryConfID=<? print $row_head->SalaryConfID ?>"><? print $_lib['format']->Date(array('value'=>$row_head->TS, 'return'=>'value')) ?></a></td>
    <td>L&oslash;nns og trekk oppgave for </td>
    <td colspan="2"></td>

</tr>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_conf)) {
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
      <td style="background-color: #FFB600">
      <?
      if($_lib['sess']->get_person('AccessLevel') >= 2 && $accounting->is_valid_accountperiod($setup['salarydefvoucherdate'], $_lib['sess']->get_person('AccessLevel')))
      {
        if($row->SalaryConfID != 1)
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=salary.edit&amp;SalaryConfID=<? print $row->SalaryConfID ?>&amp;action_salary_new=1" class="button"><? if($row->SalaryConfID != 1) { print $row->AccountPlanID; } ?></a><?
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
            ?><a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.edit"><? print $row->KommuneName; ?></a></td><?
        }
      ?>
      <td>
      <?
        if($row->SalaryConfID != 1)
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.edit"><? print $row->Sone; ?></a><?
        }
      ?>
      </td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=salary.template&SalaryConfID=<? print $row->SalaryConfID ?>"><? print $_lib['format']->Date(array('value'=>$row->TS, 'return'=>'value')) ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.salaryreport&amp;report.Year=<? print $year ?>&amp;report.Employee=<? print $row->AccountPlanID ?>" target="new">Vis <? print $year ?></a></td>

      <td colspan="2">	    
	<? if(($_lib['sess']->get_person('AccessLevel') >= 4) and ($row->SalaryConfID != 1)) { 
	    echo $_lib['form3']->button(array('url' => 
				 $_lib['sess']->dispatch . "t=salary.list&amp;SalaryConfID=" . $row->SalaryConfID . "&amp;action_salaryconf_delete=1"
				 , 'name' => 'Slett', 'confirm' => 'Vil du virkelig slette linjen?'));
	 } ?>
      </td>

<? } ?>
</tbody>
<tr>
  <td colspan="6"></td>
  <td colspan="2">
  </td>
  <td></td>
</tr>
</table>
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
</body>
</html>