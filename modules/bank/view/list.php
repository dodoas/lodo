<?
# $Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $ person_list.php,v 1.3 2001/11/20 18:04:43 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

include('record.inc');

$query_account  = "select * from account order By Active desc, Sort asc";
$result_account = $_lib['db']->db_query($query_account);

includemodel('bank/bankaccount');
$ba = new model_bank_bankaccount(array());
?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - bankaccount list</title>
    <meta name="cvs"                content="$Id: list.php,v 1.24 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
    <style>
     table.lodo_data th { padding-left: 10px; }
     table.lodo_data td { padding-left: 10px; }
    </style>
</head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h1><a href="<? print $_lib['sess']->dispatch ?>t=bank.list"><b>Kasse/bank-avstemming</b></a> <br /> <a href="<? print $_lib['sess']->dispatch ?>t=bank.accountperiodcomment">Brukte bilagsnummer</a></h1>

<table class="lodo_data">
<thead>
  <tr>
    <th>Def periode</th>
    <th>Bankkonto</th>
    <th>Banknavn</th>
    <th>Kontonavn</th>
    <th>Konto</th>
    <th>Eier</th>
    <th>Saldo</th>
    <th>Aktiv</th>
    <th>Type</th>
    <th>Gyldig fra</th>
    <th>Gyldig til</th>
    <th>Oppsett</th>
    <th>Import</th>
  </tr>
</thead>
<tbody>
<?
while($row = $_lib['db']->db_fetch_object($result_account)) {
$i++;
if (!($i % 2)) { $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };
if($row->DefaultPeriod)
    $Period = $row->DefaultPeriod;
else 
    $Period = $_lib['date']->get_prev_period(array('value' => $_lib['sess']->get_session('LoginFormDate'), 'realPeriod' => 1));
	
	list($Amount, $LastPeriod) = $ba->saldo($row->AccountID);
	$TotalAmount += $Amount;
?>
    <tr class="<? print "$sec_color"; ?>">
      <td>	
        <form method="post" action="">
        <? 
           print $_lib['form3']->AccountPeriod_menu3(
                array('table' => 'account', 'field' => 'DefaultPeriod',
                        'pk' => $row->AccountID, 'value' => $Period, 
                        'access' => $_lib['sess']->get_person('AccessLevel'), 'accesskey' => 'P', 
                        'required'=> false));
        ?>	
	<input type="submit" value="Lagre" name="action_period_update">
        </form>
      </td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.tabbankaccount&amp;AccountID=<? print $row->AccountID ?>&amp;Period=<? print $row->DefaultPeriod ?>"><? print $row->AccountNumber ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.tabbankaccount&amp;AccountID=<? print $row->AccountID ?>&amp;Period=<? print $row->DefaultPeriod ?>"><? print $row->BankName ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.tabbankaccount&amp;AccountID=<? print $row->AccountID ?>&amp;Period=<? print $row->DefaultPeriod ?>"><? print $row->AccountDescription ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.hovedbok&amp;accountplan_AccountPlanID=<? print $row->AccountPlanID ?>&amp;Period=<? print $row->DefaultPeriod ?>"><? print $row->AccountPlanID ?></a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.tabbankaccount&amp;AccountID=<? print $row->AccountID ?>&amp;Period=<? print $row->DefaultPeriod ?>"><? print $row->OwnerName ?></a></td>
      <td class="number"><? print $_lib['format']->Amount($Amount) ?></td>
      <td><? if($row->Active)    { print "Aktiv"; }; ?></td>
      <td><? print $row->VoucherType ?></td>
      <td><? print $row->ValidFrom ?></td>
      <td><? print $row->ValidTo ?></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.template&amp;AccountID=<? print $row->AccountID ?>">Endre</a></td>
      <td><a href="<? print $_lib['sess']->dispatch ?>t=bank.import&amp;AccountID=<? print $row->AccountID ?>">Import</a></td>
    </tr>
<? } ?>
  <tr>
    <td colspan="5"></td>
    <td><b>Sum</b></td>
  	<td><b><? print $_lib['format']->Amount($TotalAmount) ?><b></td>
  </tr>
  <tr>
     <td colspan="11">
     <form name="template_update" action="<? print $_lib['sess']->dispatch ?>t=bank.template" method="post">
     <td colspan="2">
     <? if($_lib['sess']->get_person('AccessLevel') >= 2) { ?>
      <input type="submit" name="action_bank_new" value="Ny konto (N)" accesskey="N" />
     <? } ?>
     </form>
   </tr>
</tbody>
</table>
</body>
</html>


