<?
# $Id: list.php,v 1.57 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$db_table = "accountplan";
require_once "record.inc";

$type               = $_lib['input']->getProperty('type');
$accountplan_type   = $_lib['input']->getProperty('accountplan_type');
$account_name       = 'Mangler kto';

$JournalID = $_lib['input']->getProperty('JournalID');
assert(!is_int($JournalID)); #All main input should be int

$searchstring = $_lib['input']->getProperty('searchstring');

$where .= " where ";
if($searchstring) {
	$where .= " (AccountPlanID like '%$searchstring%' or AccountName like '%$searchstring%' or OrgNumber like '%$searchstring%' or DomesticBankAccount like '%$searchstring%') and ";

} else {

	if($accountplan_type == 'hovedbok')
	{
		$where         .= " (AccountPlanType='balance' or AccountPlanType = 'result') and ";
		$account_name   = "Hovedbokskonto";
		$func 			= "hovedbok";
	}
	elseif($accountplan_type == 'reskontro')
	{
		$where         .= " (AccountPlanType != 'balance' && AccountPlanType != 'result' && AccountPlanType != 'employee') and ";
		$account_name   = "Reskontro";
		$func 			= "reskontro";
	}
	elseif($accountplan_type) {
		$where         .= " AccountPlanType='$accountplan_type' and ";
		$account_name   = $accountplan_type;
		$func 			= "employee";
	}
	
	if($accountplan_type == 'result' || $accountplan_type == 'balance') {
	  	$func 			= "hovedbok";
	}
	elseif($accountplan_type != 'balance' && $accountplan_type != 'result' && $accountplan_type != 'employee' && $accountplan_type != 'hovedbok') {
	  	$func 			= "reskontro";
	}
}

if($_lib['input']->getProperty('accountplan_active')) {
    $where .= " Active = 1 and";
}

$where = substr($where, 0, -4);

#print "<br>func: $func<br>";
$limitSet = $_lib['input']->getProperty('limit');
$limitSet = 1;
if(isset($limitSet))
    $limit = "";
else
    $limit = "limit 0";

$select = "select * from $db_table ";

#print "$select $where order by AccountPlanID asc $limit<br>\n";
$result_plan = $_lib['db']->db_query("$select $where order by AccountPlanID asc $limit");

?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - accountplan list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.57 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>

<body>
<?

includeinc('top');
includeinc('left');

if($JournalID) { ?>
   <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&JournalID=<? print "$JournalID"; ?>">Tilbake til bilag <? print "$JournalID"; ?></a>
<?
}
?>
<h2><? print "$account_name"; ?></h2>

<form name="accountplan_list" action="<? print $MY_SELF ?>" method="post">
<table>
<tr>
  <td>
  <table class="lodo_data">
    <thead>
      <tr>
        <th colspan="2">S&oslash;k</th>
      </tr>
    </thead>
    <tr>
      <th class="menu">S&oslash;k kun etter aktive</th>
      <td><? $_lib['form2']->checkbox2('accountplan', 'active', $_lib['input']->getProperty('accountplan_active'),'');        ?></td>
    </tr>
    <tr>
      <th class="menu">Fritekst s&oslash;k
       <td>
         <input type="text"   name="searchstring" value="<? print $searchstring ?>" />
         <input type="hidden" name="JournalID"    value="<? print $JournalID ?>"    />
         <input type="hidden" name="report.Sort"  value="AccountPlanID"  			/>
         <input type="hidden" name="limit"        value="1"  />
       </td>
    </tr>
    <tr>
    	<th class="menu">
    	Type
    	</th>
    	<td>
    	<? print $_lib['form3']->Type_menu3(array('table' => accountplan, 'field'=>'type', 'value' => $accountplan_type, 'type' => 'AccountPlanType', 'required' => 0)) ?>
    	</td>
    </tr>
    <tr>
      <td colspan="2" align="right">
        <input type="submit" value="S&oslash;k" name="action_accountplan_search" />
    </form>
  </tbody>
  </table>
  </td>
  <td valign="top">
  <? require_once 'new.inc'; ?>
  </td>
</tr>
</table>
<?
if(isset($limitSet))
{
?>
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="14">Kontoer</th>
  <tr>
    <th class="menu">Aktiv</th>
    <th class="menu">Konto</th>
    <th class="menu">Beskrivelse</th>
    <th class="menu">Type</th>
    <th class="menu">Kreditt</th>    
    <th class="menu">Lodo konto</th>
    <th class="menu">Debet tekst</th>
    <th class="menu">Kredit tekst</th>
    <th class="menu">MVA kode</th>
    <th class="menu">Avdeling</th>
    <th class="menu">Prosjekt</th>
    <th class="menu">Motkontobalanse</th>
    <th class="menu">Motkontoresultat</th>
    <th class="menu">Org nummer</th>
  </tr>
</thead>

<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_plan))
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

    if($row->AccountPlanType == 'balance' || $row->AccountPlanType == 'result')
    {
      	$func = "hovedbok";
    }
    elseif($row->AccountPlanType == 'customer' || $row->AccountPlanType == 'supplier') {
 		$func = "reskontro";
	}
    else
    {
      	$func = $row->AccountPlanType;
    }
    ?>
      <tr class="<? print "$sec_color"; ?>">
          <td><? print $_lib['form3']->checkbox(array('table'=>'accountplan', 'value'=>$row->Active, 'disabled'=>'1')) ?></td>
          <td align="right"><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.<? print "$func"; ?>&accountplan.AccountPlanID=<? print $row->AccountPlanID ?>&amp;accountplan_type=<? print $accountplan_type ?>"><? print $row->AccountPlanID; ?></a></td>
          <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.<? print $func ?>&accountplan.AccountPlanID=<? print $row->AccountPlanID ?>&amp;accountplan_type=<? print $accountplan_type ?>"><? print substr($row->AccountName,0,35) ?></a></td>
          <td><? print $row->AccountPlanType ?></td>
          <td align="right"><? if($row->EnableCredit)    { print $row->CreditDays; } ?></td>
          <td><? print $_lib['form3']->checkbox(array('table'=>'accountplan', 'value'=>$row->EnableNorwegianStandard, 'disabled'=>'1')) ?></td>
          <td><? print $row->debittext ?></td>
          <td><? print $row->credittext ?></td>
          <td align="right"><? if($row->EnableVAT)    { print $row->VatID; } ?></td>
          
          <? // forandret fra DepartmentID til EnableDepartment 6/1-2005 ?>
          <td align="right"><? if($row->EnableDepartment) { print $row->DepartmentID; } ?></td>
          
          <? // forandret fra ProjectID til EnableProject 6/1-2005 ?>
          <td align="right"><? if($row->EnableProject)    { print $row->ProjectID; } ?></td>
          <td align="right"><? if($row->EnableMotkontoBalanse)    { print $row->MotkontoBalanse1; } ?></td>
          <td align="right"><? if($row->EnableMotkontoResultat)    { print $row->MotkontoResultat1; } ?></td>
          <td><? print $row->OrgNumber ?></td>
    </tr>
    <?
}
?>
</tbody>
</table>
<?
}
?>

</body>
</html>


