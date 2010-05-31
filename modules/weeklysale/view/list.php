<?
# $Id: list.php,v 1.44 2005/10/28 17:59:41 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "weeklysale";

includelogic('accounting/accounting');
$accounting = new accounting();
require_once  "record.inc";

/* sortering og gruppering av data */
if (!$SORT || $SORT == "ASC") { $SORT = "DESC"; } else { $SORT = "ASC"; }
if(!$_SETUP[DB_START][0]) { $_SETUP[DB_START][0] = 0; }
if(!$CompanyID)   { $CompanyID = 1; }
if (!$order_by)   { $order_by  = "AccountNumber"; }
$db_stop = $_SETUP[DB_START][0] + $_SETUP[DB_OFFSET][0];

/* S¿kestreng */
$query_week     = "select * from $db_table order by Period desc, JournalDate desc";
$result_week    = $_lib['db']->db_query($query_week);

$query_conf     = "select * from weeklysaleconf";
$result_conf    = $_lib['db']->db_query($query_conf);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - project list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.44 2005/10/28 17:59:41 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table cellspacing="0">

<table class="lodo_data">
  <tr>
    <th>Navn</th>
    <th>Avdelingsnummer</th>
    <th>Bilagsart</th>
    <th>Opprett ny uke</th>
    <th></th>
<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_conf))
{
    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->Name; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->DepartmentID; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.template&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>"><? print $row->VoucherType; ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>&action_weeklysale_new=1" class="action">Ny ukeomsetning for avdeling <? print $row->DepartmentID; ?></a></td>
      <td>
      <? if($_lib['sess']->get_person('AccessLevel') >= 4) { ?>
        <a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list&amp;WeeklySaleConfID=<? print $row->WeeklySaleConfID ?>&amp;action_weeklysaleconf_delete=1" class="button">Slett</a>
      <? } ?>
<?
}
?>
		</td>
	</tr>
</tbody>
<? if($_lib['sess']->get_person('AccessLevel') > 2) { ?>
  <tr>
    <td>
    <td align="right" colspan="3">
      <form name="project_search" action="<? print $_lib['sess']->dispatch ?>t=weeklysale.template" method="post">
      <input type="submit" name="action_weeklysaleconf_new" value="Ny avdelings konfigurasjon (N)" accesskey="N" />
      </form>
  </tr>
<? } ?>
</table>
<br />
<table class="lodo_data">
<thead>
  <tr>
    <th>Bilagsnr</th>
    <th>Bilagsdato</th>
    <th>Periode</th>
    <th>Navn</th>
    <th>Uke</th>
    <th>Avdeling</th>
    <th>Kontant</th>
    <th>Salg</th>
    <th></th>
  </tr>
</thead>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_week))
{
    $query = "select Period from weeklysale where WeeklySaleID='$row->WeeklySaleID'";
    $week = $_lib['storage']->get_row(array('query' => $query));

    $i++;
    if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
    ?>
    <tr class="<? print "$sec_color"; ?>">
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->VoucherType ?><? print $row->JournalID ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? $hash = $_lib['format']->Date(array('value'=>$row->JournalDate)); print $hash['value']; ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Period ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Name ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><? print $row->Week ?></a>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.edit&WeeklySaleID=<? print $row->WeeklySaleID ?>"><nobr><? $query2="select DepartmentName from companydepartment where CompanyDepartmentID='$row->DepartmentID'"; $row2=$_lib['storage']->get_row(array('query' => $query2)); print $row->DepartmentID." ".$row2->DepartmentName; ?></nobr></a>
      <td><? $hash = $_lib['format']->Amount(array('value'=>$row->TotalCash)); print $hash['value']; ?>
      <td><? $hash = $_lib['format']->Amount(array('value'=>$row->TotalAmount)); print $hash['value']; ?>
      <td>
      <? if($_lib['sess']->get_person('AccessLevel') > 3) {
        if($accounting->is_valid_accountperiod($week->Period, $_lib['sess']->get_person('AccessLevel')))
        {
            ?><a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list&amp;WeeklySaleID=<? print $row->WeeklySaleID ?>&amp;action_weeklysale_delete=1" class="button">Slett</a><?
        }
      }
}
?>
</tbody>
</table>
</body>
</html>


