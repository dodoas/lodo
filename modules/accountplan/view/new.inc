<?
if($accountplan_type == 'hovedbok')
    $selectedAccountPlanType = 'result';
elseif($accountplan_type == 'reskontro')
    $selectedAccountPlanType = 'customer';
elseif($accountplan_type)
    $selectedAccountPlanType = $accountplan_type;
else
    $selectedAccountPlanType = 'customer';
?>

<form name="accountplan_search" action="<? print $_lib['sess']->dispatch ?>t=accountplan.<? print $func ?>" method="post">
<table class="lodo_data">
<thead>
  <tr>
    <th colspan="3">Opprett ny <? print $func ?></th>
  </tr>
</thead>
<tbody>
  <tr class="menu">
    <th class="menu">Type</th>
    <th class="menu">Kontonummer/Organisasjonsnummer</th>
    <th class="menu"></th>
  </tr>
  <tr>
    <td>
        <? print $_lib['form3']->Type_menu3(array('table'=>$db_table, 'field'=>'AccountPlanType', 'value'=>$selectedAccountPlanType, 'type'=>'AccountPlanType', 'required'=>'1')) ?>
    </td>
    <td>
        <input type="text"    value=""  name="accountplan.AccountPlanID" />
        <input type="hidden"  value="<? print $JournalID; ?>"  name="JournalID" />
    </td>
    <td align="left">
      <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
        <input type="submit" name="action_accountplan_new" value="Opprett konto" />
      <? } ?>
    </td>
  </tr>
  <tr>
    <td colspan="3">Tips: S&oslash;k opp organisasjonsnummeret i <a href="http://www.brreg.no/">Br&oslash;nn&oslash;ysund registeret</a> - s&aring; oppretter vi kontoplanen automatisk med navn, adresse og telefonnummer.</td>
  </tr>
</tbody>
</table>
</form>
<br>