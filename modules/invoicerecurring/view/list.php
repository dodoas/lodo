<?
# $Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $ invoicerecurring_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('accounting/accounting');
$accounting = new accounting();

$FromDate       = $_lib['input']->getProperty('FromDate');
$ToDate         = $_lib['input']->getProperty('ToDate');
$RecurringID      = $_lib['input']->getProperty('RecurringID');
$searchstring   = $_lib['input']->getProperty('searchstring');
require_once "record.inc";

$recurring = new recurring(array());
$recurring_intervals = $recurring->get_intervals();

if(!$FromDate) {
    $FromDate = $_lib['sess']->get_session('DateStartYear');
}

if(!$ToDate) {
    $ToDate   = $_lib['sess']->get_session('DateEndYear');
}

$db_table = "recurringout";
$db_table2 = "recurringoutline";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC")
{
    $SORT = "DESC";
}
else
{
    $SORT = "ASC";
}

if(!$_SETUP['DB_START']['0'])
{
    $_SETUP['DB_START']['0'] = 0;
}

if (!$order_by)
{
    $order_by = "RecurringID";
}

$db_stop = $_SETUP['DB_START']['0'] + $_SETUP['DB_OFFSET']['0'];

if (!$SORT || $SORT == "ASC")
{
    $SORT = "DESC";
}
else
{
    $SORT = "ASC";
}

/* Sokestreng */
$query  = "select i.* from recurringout as i where ";
 if($FromDate) {
     $query .= " i.InvoiceDate >= '$FromDate' and ";
 }
 if($ToDate) {
     $query .= " i.InvoiceDate <= '$ToDate' and ";
 }

 if($RecurringID) {
     $query .= " i.RecurringID='$RecurringID' and ";
 }

if($searchstring){
    $query .= " i.IName like '%$searchstring%' and ";
}

$query = substr($query, 0, -4);
$query  .= " order by RecurringID desc";

#print "$query<br>\n";
$result_inv = $_lib['db']->db_query($query);
$query_count = "SELECT COUNT(*) as total, sum(TotalCustPrice) as sum FROM $db_table $where";
$result_count = $_lib['db']->db_query($query_count);
$row = $_lib['db']->db_fetch_object($result_count);

$db_total = $row->total;
$db_sum   = $row->sum;
?>


    <? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Invoice List</title>
        <meta name="cvs"                content="$Id: list.php,v 1.38 2005/10/28 17:59:40 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2><a href="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.list">Repeterende Faktura - Liste</a></h2>
<? if($_lib['message']->get()) { ?>
    <div class="red error"><? print $_lib['message']->get() ?></div>
<? } ?>

<? print $_lib['message']->get(); ?>

<table>
<thead>

<tr>
    <td>
        <form name="invoicerecurring_list" action="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.list" method="post">
        S&oslash;k:   <input type="text" value="<? print $searchstring ?>" name="searchstring" size="10"/>
        Fra:    <? print $_lib['form3']->date(array('name' => 'FromDate',           'value' => $FromDate)) ?>
        Til:    <? print $_lib['form3']->date(array('name' => 'ToDate',             'value' => $ToDate)) ?>
        Fakturanummer: <? print $_lib['form3']->text(array('name' => 'RecurringID',   'value' => $RecurringID)) ?>
        <? print $_lib['form3']->submit(array('name' => 'show_search',   'value' => 'S&oslash;k (S)')) ?>
        </form>
        <form name="invoicerecurring_edit" action="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.edit" method="post">
        <input type="hidden" value="edit" name="inline">
        <input type="submit" value="Ny faktura (N)" name="action_invoicerecurring_new" accesskey="N">
        </form>
	</td>
</tr>
</table>
<table>
<tr>
    <th>
        <?
        if($_SETUP['DB_START']['0'] > 0)
            {?> <a href="<? print $MY_SELF ?>&amp;DB_START=<? print $_SETUP['DB_START']['0']-$_SETUP['DB_OFFSET']['0']; ?>&amp;searchstring=<? print "$searchstring"; ?>&amp;InvoiceStatus=<? print "$InvoiceStatus"; ?>&amp;order_by=<? print "$order_by"; ?>&amp;sort=<? print "$SORT"; ?>" title="forrige">&lt;&lt;</a> <?}
        else
            {?> &lt;&lt; <?}
        ?>
    <th colspan="1">Fant:<? print $db_total ?>, viser <? print $_SETUP['DB_START']['0']." til ".$db_stop; ?></th>
    <th colspan="10">Total salg: <? print $_lib['format']->Amount(array('value'=>$db_sum, 'return'=>'value')) ?></th>
    <th align="right">
        <?
        if($db_total > $db_stop)
            {?> <a href="<? print $MY_SELF ?>&amp;DB_START=<? print $_SETUP['DB_START']['0']+$_SETUP['DB_OFFSET']['0']; ?>&amp;searchstring=<? print "$searchstring"; ?>&amp;InvoiceStatus=<? print "$InvoiceStatus"; ?>&amp;order_by=<? print "$order_by"; ?>&amp;sort=<? print "$SORT"; ?>" title="next">&gt;&gt;</a> <?}
        else
            {?> &gt;&gt; <?}
        ?>
    </th>
</tr>
<tr>
    <th align="right">Rep. faktura</th>
    <th align="right">Interval</th>
    <!--<th>OrdreRef-->
    <th align="right">Kunde nr</th>
    <th>Firmanavn</th>
    <th>Avdeling</th>
    <th>Prosjekt</th>
    <th>Kommentar</th>
    <th align="right">Forrige gikk</th>
    <th align="right">Neste g&aring;r</th>
    <th align="right">Total</th>
    <th align="right">Endre</th>
</tr>
</thead>

<tbody>

<?
while($row = $_lib['db']->db_fetch_object($result_inv))
{
    $i++;
    if (!($i % 2))
    {
        $sec_color = "BGColorLight";
    }
    else
    {
        $sec_color = "BGColorDark";
    }

  $_lib['sess']->debug("ja");
  $TotalCustPrice += $row->TotalCustPrice;

  $interval_sql = "SELECT * FROM recurring WHERE RecurringID = " . $row->RecurringID;
  $interval_row = $_lib['db']->get_row(array('query' => $interval_sql));
  $interval = $recurring_intervals[ $interval_row->TimeInterval ][0];

  if($interval != '')
  {
    $interval_sql = "SELECT DATE_ADD(LastDate, INTERVAL " . $recurring_intervals[ $interval_row->TimeInterval ][1] . ") as NextDate, LastDate
  			FROM recurring 
  			WHERE RecurringID = " . $row->RecurringID;
    $interval_row = $_lib['db']->get_row(array('query' => $interval_sql));
    $nextDate = $interval_row->NextDate;
    $lastDate = $interval_row->LastDate;

    if($lastDate == "0000-00-00")
    {
      $nextDate = "-";
      $lastDate = "-";
    }
  }
  else
  {
    $nextDate = "N/A";
    $lastDate = "N/A";
  }
  ?>
  <form name="invoice" action="<? print $MY_SELF ?>" method="post">
    <tr class="<? print $sec_color ?>">
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.edit&RecurringID=<? print $row->RecurringID ?>&inline=show" 
title="Vis/Endre faktura informasjon"><? print $row->RecurringID ?></a></td>
<!--      <td class="number"><? print substr($row->InvoiceDate,0,10) ?></td>
      <td class="number"><? print $row->Period ?></td> -->
      <td class="center"><? echo $interval; ?></td>
      <!--<td><? print $row->OrderRef; ?>-->
      <td class="number"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&accountplan.AccountPlanID=<? print $row->CustomerAccountPlanID ?>&inline=show"><? print $row->CustomerAccountPlanID ?></a></td></td>
      <td>&nbsp;<a href="<? print $_lib['sess']->dispatch ?>t=accountplan.reskontro&accountplan.AccountPlanID=<? print $row->CustomerAccountPlanID ?>&inline=show"><? print substr($row->IName,0,30) ?></a></td>
      <td><? $department = $accounting->get_department_object($row->DepartmentID); print "$department->CompanyDepartmentID - $department->DepartmentName"; ?></td>
      <td><? $project    = $accounting->get_project_object($row->ProjectID); print "$project->ProjectID - $project->Heading"; ?></td>
      <td><? print substr($row->CommentCustomer,0,30) ?></td>
      <td class="number"><b><? echo $lastDate ?></b></td>
      <td class="number"><b><? echo $nextDate ?></b></td>
      <td class="number"><? print $_lib['format']->Amount($row->TotalCustPrice) ?></td>
      <td align="center">
      <? if($accounting->is_valid_accountperiod($_lib['date']->get_this_period($row->Period), $_lib['sess']->get_person('AccessLevel')) && $_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.edit&RecurringID=<? print $row->RecurringID ?>&inline=edit" title="Endre faktura" target="_new">Endre</a><br />
      <? } else { ?>
      <a href="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.edit&RecurringID=<? print $row->RecurringID ?>&inline=show" title="Endre faktura" target="_new">Stengt</a><br />
      <? } ?>
      </td>


  </form>
  </tr>
  <?
}
?>
<tr>
    <td colspan="9"></td>
    <td class="number"><? print $TotalCustPrice ?></td>
    <td></td>
</tr>
</tbody>

</table>
</body>
</html>


