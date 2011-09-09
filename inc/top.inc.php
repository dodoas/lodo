<?
#Patch all links to remember choises from journal GUI
$_lib['sess']->dispatch = $_lib['sess']->dispatch . 'view_mvalines=' . $_lib['input']->getProperty('view_mvalines') . '&amp;view_linedetails=' . $_lib['input']->getProperty('view_linedetails') . '&amp;';

if($_lib['sess']->get_session('LoginFormDate'))
{
  $_this_date = $_lib['sess']->get_session('LoginFormDate');
}
else
{
  $_this_date = $_lib['sess']->get_session('Date');
}

$balanceaccount = $_lib['sess']->get_companydef('VoucherBalanceAccount');
$_to_period     = $_lib['date']->get_this_period($_this_date);
$_year          = $_lib['date']->get_this_year($_this_date);
$_from_date     = $_year . '-01-01';

$sql_result     = "select sum(AmountIn) as AmIn, sum(AmountOut) as AmOut from voucher where AccountplanID = '$balanceaccount' and VoucherDate >= '$_from_date'and VoucherDate <= '$_this_date' and VoucherType ='A' and Active=1";
#$sql_result     = "select sum(AmountIn) as AmIn, sum(AmountOut) as AmOut from voucher where AccountplanID = '$balanceaccount' and VoucherDate >= '$_from_date' and VoucherType ='A' and Active=1";

#print "$sql_result<br>\n";
$_row_result        = $_lib['storage']->get_row(array('query' => $sql_result));

includelogic('bank/bankaccount');
$ba = new model_bank_bankaccount(array());
#print_r($_REQUEST);

if(isset($_REQUEST['voucher_VoucherPeriod'])) {
    $sql_control_auto_resultat  = "select count(*) as count from voucher where AccountPlanID='". $_lib['sess']->get_companydef('VoucherResultAccount') ."' and VoucherPeriod='" . $_REQUEST['voucher_VoucherPeriod'] . "' and VoucherType='A' and Active=1";
    #print $sql_control_auto_resultat;
    $row_balance_auto_resultat  = $_lib['storage']->get_row(array('query' => $sql_control_auto_resultat));
    $sum_resultat_bilag         = $row_balance_auto_resultat->count;

    $sql_control_auto_balanse   = "select count(*) as count from voucher where AccountPlanID='". $_lib['sess']->get_companydef('VoucherBalanceAccount') ."' and VoucherPeriod='" . $_REQUEST['voucher_VoucherPeriod'] . "' and VoucherType='A' and Active=1";
    $row_control_auto_balanse   = $_lib['storage']->get_row(array('query' => $sql_control_auto_balanse));
    $sum_balanse_bilag          = $row_balance_auto_resultat->count;

}
?>
<?
$sum = $_row_result->AmIn - $_row_result->AmOut;
$text .= "Regnskaps ";
if($sum > 0)
{
  $text .= "underskudd: " . $_lib['format']->Amount(abs($sum));
  $class = "debitred";
}
else
{
  $text .= "overskudd: " . $_lib['format']->Amount(abs($sum));
  $class = "debitblue";
}
$topclass = 'topclass';
?>
<div class="main">
    <div id="layout_top">
        <table>
            <tr valign="top">
                <td rowspan="3">
                <? if(preg_match("/empatix/", $_SERVER['HTTP_HOST'])) { ?>
                    <a href="<? print $_lib['sess']->dispatch ?>t=lodo.main" target="_top" class="logo"><img src="http://www.empatix.no/auto/mediastorage/430_353778_FileName.jpg" alt="Empatix regnskap" class="logo"></a>
                <? } else { ?>
                    <a href="<? print $_lib['sess']->dispatch ?>t=lodo.main" target="_top" class="logo"><img src="/img/lodo/logo.gif" alt="Regnskapspakken Lodo" width="192" height="98" class="logo"></a>
                <? } ?><br />
                <h2><? print $_lib['sess']->get_companydef('CompanyName') ?> - <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> (<? print $_lib['sess']->get_session('Date') ?>)</h2>

                </td>
              </tr>
              <tr>
                <td class="<? print $class ?>">
                   <b><? print $text ?>
                    P&aring; bankkonto: <? print $_lib['format']->Amount($ba->totalsaldo()) ?></b>
                </td>
                <td class="<? print $class ?>">Perioden <? print $_from_date ?>
                <? print $_lib['form3']->start(array()) ?>
                  <? print $_lib['form3']->text(array('name' => 'LoginFormDate'    , 'value' => $_SESSION['LoginFormDate'], 'class' => $class)) ?> 
                  <? print $_lib['form3']->submit(array('name' => 'view_journal_changedate'    , 'value' => 'Bytt dato')) ?>
                  <? print $_lib['form3']->stop(array()) ?>
                  </td>
                  <td class="<? print $class ?>">
                    <a href="<? print $_lib['sess']->dispatch ?>t=lodo.main">Forside</a>
                    <a href="<? print $_lib['sess']->dispatch ?>">Logg ut</a>
                  </td>
                </tr>
                <tr>
                <td colspan="5">
                    <div class="modulemenu">
                        <table>
                            <tr valign="top">
                                <td>
                                <fieldset>
                                <legend>Kasse - K</legend>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=cash_in&voucher_AccountPlanID=<? print $setup['kasseinn']; ?>" class="group_sale">Inn</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=cash_out&voucher_AccountPlanID=<? print $setup['kasseut']; ?>" class="group_buy">Ut</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=weeklysale.list">Ukeomsetning</a>
                                </fieldset>
                                </td>
                                <td>
                                <fieldset>
                                <legend>Bank - B</legend>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=bank_in&voucher_AccountPlanID=<? print $setup['bankinn']; ?>" class="group_sale">Inn</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=bank_out&voucher_AccountPlanID=<? print $setup['bankut']; ?>" class="group_buy">Ut</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=ocr.list">Innbetaling (OCR/KID)</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=bank.list">Bank</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=remittance.listincoming">Betaling/Remittering</a>
                                </fieldset>
                                </td>
                                <td>
                                <fieldset>
                                <legend>Kj&oslash;p - U / L&oslash;nn - L</legend>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buycredit_out&voucher_AccountPlanID=<? print $setup['buycreditreskontro']; ?>"            title="Her kan du lage inngŒende fakturaer" class="group_buy">Faktura inng&aring;ende</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buynotacredit_out&voucher_AccountPlanID=<? print $setup['buynotacreditreskontro']; ?>"    class="group_buy">Kreditnota inng&aring;ende</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=invoicein.list">Innkommende fakturaer</a>

                                  <!--<div class="group">
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buycash_out&voucher_AccountPlanID=<? print $setup['buycashut']; ?>"                       class="group_buy">Faktura inng kontant (4)</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=buynotacash_out&voucher_AccountPlanID=<? print $setup['buynotacashinn']; ?>"              class="group_buy">Kreditnota inng kontant (4)</a>

                                </div>-->
                                  <a href="<? print $_lib['sess']->dispatch ?>t=salary.list">L&oslash;nnsslipp</a>
                                </fieldset>
                                </td>
                                <td>
                                <fieldset>
                                <legend>Salg - S / Faktura</legend>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salecredit_in&voucher_AccountPlanID=<? print $setup['salecreditreskontro']; ?>"           class="group_sale">Faktura salg</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salenotacredit_in&voucher_AccountPlanID=<? print $setup['salenotacreditreskontro']; ?>"   class="group_sale">Kreditnota salg</a>
                                <!--
                                <div class="group">
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salecash_in&voucher_AccountPlanID=<? print $setup['salecashut']; ?>"                      class="group_sale">Faktura salg kontant (4)</a>
                                  <a href="<? print $_lib['sess']->dispatch ?>t=journal.edit&new=1&type=salenotacash_in&voucher_AccountPlanID=<? print $setup['salenotacashut']; ?>"              class="group_sale">Kreditnota salg kontant (4)</a>
                                </div>-->
                                  <?
                                  if($_lib['setup']->get_value('invoice.outgoing') == 'empatix') { ?>
                                      Faktura er i Empatix                                  
                                  <? } else { ?>
                                    <a href="<? print $_lib['sess']->dispatch ?>t=invoice.listoutgoing">Utg&aring;ende faktura (salg)</a>
                                  <? } ?>                              

                                  <a href="<? print $_lib['sess']->dispatch ?>t=invoicerecurring.list">Repeterende faktura</a>
                                </fieldset>
                                </td>
                                <td>
                                <fieldset>
                                <legend>Oppsett</legend>   
                                <a href="<? print $_lib['sess']->dispatch ?>t=report.list">Rapporter</a>
                                <!--a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=hovedbok">Hovedbokskontoer</a-->
                                <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=balance">Hovedbok balanse 3</a>
                                <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=result">Hovedbok resultat 3</a>
                                <?
                                $query2         = "select AccountPlanID, AccountName, ReskontroAccountPlanType from accountplan where EnableReskontro=1 and AccountPlanID != 2930 and Active=1 order by AccountPlanID asc";
                                #print "$query2<br>\n";
                                $result2        = $_lib['db']->db_query($query2);

                                while($row2 = $_lib['db']->db_fetch_object($result2))
                                {
                                    ?><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=<? print $row2->ReskontroAccountPlanType ?>" title="Reskontro"><? print $row2->AccountPlanID ?> - <? print $row2->AccountName ?></a><?
                                }
                                ?>
                                </fieldset>
                                </td>
                                
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <hr>
