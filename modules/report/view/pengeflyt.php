<?
# $Id: pengeflyt.php,v 1.16 2005/10/14 13:15:42 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

includelogic('moneyflow/moneyflow');
$moneyflow = new moneyflow(array('StartDate' => $_lib['input']->getProperty('StartDate')));

$_lib['sess']->debug($expected_query_accounts);
print $_lib['sess']->doctype; ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Forventet pengeflyt</title>
        <meta name="cvs"                content="$Id: pengeflyt.php,v 1.16 2005/10/14 13:15:42 thomasek Exp $" />
        <? includeinc('head'); ?>
    </head>
<body>
    <? includeinc('top'); ?>
    <? includeinc('left'); ?>
    
    <? print $_lib['form3']->start(array()) ?>
    Start dato: <? print $_lib['form3']->text(array('name' => 'StartDate'    , 'value' => $moneyflow->StartDate)) ?> <? print $_lib['form3']->submit(array('name' => 'view_pengeflyt_changedate'    , 'value' => 'Endre start dato')) ?>
    <? print $_lib['form3']->stop(array()) ?>
    <table>
        <thead>
        <tr>
            <td colspan="4"><b>Kontoer som er med i saldo beregningen:</b></td>
        </tr>
        <?
        $sum=0;
        foreach($moneyflow->saldoH as $account)
        {
            print "<tr>";
            print "<td><a href=\"$_SETUP[DISPATCH]t=accountplan.hovedbok&accountplan.AccountPlanID=$row->AccountPlanID\">$account->AccountPlanID</a></td>";
            print "<td><a href=\"$_SETUP[DISPATCH]t=accountplan.hovedbok&accountplan.AccountPlanID=$row->AccountPlanID\">$account->AccountName</a></td>";
            print "<td>" . $_lib['format']->Amount($account->AmountBalance) . "</td>";
            print "</tr>";
            $sum += ($row->sumin - $row->sumout);
        }
        $startBalance = $sum;
        ?>
      <tr>
         <td colspan="3"><b>Kontoer som er med i pengeflyt beregningen:</b></td>
      </tr>
        <?
        while($row = $_lib['db']->db_fetch_object($moneyflow->result_account))
        {
            print "<tr>";
            print "<td><a href=\"$_SETUP[DISPATCH]t=accountplan.hovedbok&accountplan.AccountPlanID=$row->AccountPlanID\">$row->AccountPlanID</a></td>";
            print "<td><a href=\"$_SETUP[DISPATCH]t=accountplan.hovedbok&accountplan.AccountPlanID=$row->AccountPlanID\">$row->AccountName</a></td>";
            print "<td></td>";
            print "</tr>";
        }
        ?>
    </table>
    <br><br>
    <table>
        <thead>
            <tr>
                <th colspan="3">Pengeflyt</th>

                <th colspan="5">
                    <? print $_lib['format']->Date($_lib['sess']->get_session('LoginFormDate')); ?>
                </th>
            </tr>
            <tr>
                <td>Forfallsdato</td>
                <td>Kto</td>
                <td>Navn</td>
                <td>KID</td>
                <td>Faktura</td>
                <td>Inn</td>
                <td>Ut</td>
                <td>Balanse</td>
            </tr>
        <tbody>
            <tr>
                <td></td>
            </tr>
            <tr>
            <?
            foreach($moneyflow->dateH as $date => $dateH)
            {
            ?>
            <tr valign="top">
                <? $tmpDate = $_lib['format']->Date($date); ?>
                <td rowspan="<? print count($moneyflow->detailH[$date]) + 1 ?>"><b><? print $tmpDate ?></b></td>
            </tr>
            <? foreach($moneyflow->detailH[$date] as $tmp => $row) { ?>
                <tr>
                    <td><? print $row->AccountPlanID ?></td>
                    <td><? print $row->AccountName ?></td>
                    <td class="number"><? print $row->KID ?></td>
                    <td class="number"><? print $row->InvoiceID ?></td>
                    <td class="number"><? if($row->AmountIn > 0)  print $_lib['format']->Amount($row->AmountIn) ?></td>
                    <td class="number red"><?   if($row->AmountOut > 0) print $_lib['format']->Amount($row->AmountOut) ?></td>
                    <td class="number <? print $row->color ?>"><b><? print $_lib['format']->Amount($row->sumBalance) ?></b></td>
                </tr>
            <? } ?>
            <tr>
                <td></td>
            </tr>
            <?
            }
            ?>
            <tr>
                <td><br></td>
            </tr>
            <tr>
                <td colspan="5" align="right">
                    <h2>Forventet resultat pr '<? print $tmpDate ?>': <? print $_lib['format']->Amount($moneyflow->sumBalance) ?> kroner</h2>
                </td>
            </tr>
    </table>
</body>
</html>