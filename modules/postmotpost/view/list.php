<?
#print_r($_REQUEST);
$MatchAccountPlanID     = $_REQUEST['MatchAccountPlanID'];
$db_table 				= 'voucher';

#print_r($_REQUEST);

includelogic('accounting/accounting');
$accounting = new accounting();
includelogic('postmotpost/postmotpost');
includelogic('exchange/exchange');

$postmotpost = new postmotpost(array('AccountPlanID' => $_REQUEST['AccountPlanID'], 'ReskontroFromAccount' => $_REQUEST['ReskontroFromAccount'], 'ReskontroToAccount' => $_REQUEST['ReskontroToAccount'], 'DepartmentID' => $_REQUEST['report_DepartmentID'], 'ProjectID' => $_REQUEST['report_ProjectID']));
require "record.inc";

$postmotpost->getopenpost();

if($fieldCount > 0)
{
    unset($message);

    $posts = array();

    foreach($vouchers as $voucherID => $voucher)
    {
        if(isset($posts[$voucher['KID']."-".$voucher['AccountPlanID']]))
        {
            #oppdatere post med denne referanse
            $posts[$voucher['KID']."-".$voucher['AccountPlanID']]->update(array('VoucherID'=>$voucherID, 'voucher'=>$voucher));
            $counter++;
        }
        else
        {
            #hashe objektene
            $posts[$voucher['KID']."-".$voucher['AccountPlanID']] = new postmotpost(array('VoucherID'=>$voucherID, 'voucher'=>$voucher));
            $counter++;
        }
    }
    //print_r($posts);
    if(isset($_REQUEST['action_postpost_accept']))
    {
        $_POST['action_postpost_acceptnow'] = 1;
        require "record.inc";
    }
}
else
{
    #$_lib['message']->add("Det er ingen poster som manger kontroll");
}
//print_r($posts);
print $_lib['sess']->doctype ?>

<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - Post mot Post</title>
    <meta name="cvs"                content="$Id: list.php,v 1.26 2005/11/03 15:33:11 thomasek Exp $" />
    <? includeinc('head') ?>
    <? includeinc('javascript') ?>
</head>
<body  onload="window.focus();">

<h2><? print $_lib['sess']->get_companydef('VName') ?></h2>

<? print $_lib['message']->get() ?>

<h2>&Aring;pne poster - dvs bilag summert på kid, hvor sum ikke g&aring;r i 0</h2>
<b>Trykk p&aring; lagre for &aring; lagre eventuelle endringer du har gjort i KID referansene - og se om postene g&aring;r mot hverandre</b><br />
<b>Trykk p&aring; lukk alle for &aring; lukke alle &aring;pne poster som g&aring;r i null og som det er mulig &aring; lukke</b><br />
<b>Trykk p&aring; &aring;pne alle for &aring; &aring;pne alle poster for alle kunder i alle perioder</b><br />

<?
#print_r($postmotpost);
if(count($postmotpost->voucherH) > 0)
{
?>
    <form class="voucher" name="postvspost" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID',          'value' => $postmotpost->AccountPlanID)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'ReskontroFromAccount',   'value' => $postmotpost->ReskontroFromAccount)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'ReskontroToAccount',     'value' => $postmotpost->ReskontroToAccount)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'report_DepartmentID',    'value' => $postmotpost->DepartmentID)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'report_ProjectID',       'value' => $postmotpost->ProjectID)) ?>
    <table  class="lodo_data">
        <thead>
            <tr class="voucher">
                <th class="sub"></th>
                <th class="sub"></th>
                <th class="sub">Bilagsnr</th>
                <th class="sub">Bilagsdato</th>
                <th class="sub">Periode</th>
                <th class="sub">Inn</th>
                <th class="sub">Ut</th>
                <th class="sub">Valuta inn</th>
                <th class="sub">Valuta ut</th>
                <th class="sub">Valuta/kurs</th>
                <th class="sub">MVA%</th>
                <th class="sub">Mengde</th>
                <th class="sub">Avd.</th>
                <th class="sub">Prosjekt</th>
                <th class="sub">Forfall</th>
                <th class="sub">Tekst</th>
                <th class="sub">KID</th>
                <th class="sub">Fakturanr</th>
                <th class="sub"></th>
                <th class="sub noprint"></th>
            </tr>
        </thead>
        <tbody>
            <?
            #print_r($postmotpost->voucherH);
            foreach($postmotpost->voucherH as $AccountPlanID => $account)
            {
            ?>
                <tr class="voucher">
                    <th colspan="12"><? print $postmotpost->sumaccountH[$AccountPlanID]->Name ?></th>
                    <th colspan="6">
                    <?

                    /* display motkontoresultat and -balanse from accountplan */
                    $reskonto = $postmotpost->findMotKonto($AccountPlanID);
                
                    $last = "";
                    foreach($reskonto as $kontokey => $konto) {
                        if(!$konto)
                            continue;
                        
                        if($last != substr($kontokey, 0, -1)) { 
                            $last = substr($kontokey, 0, -1);
                            
                            printf(" %s: ", $last); 
                        }
                        else {
                            printf(", ");
                        }
                        
                        printf("%d", $konto);
                    }
                    ?>
                  </th>
                </tr>

                <?
                $closeable = 0;
                foreach($account as $voucher)
                {
                    if($MatchAccountPlanID == $voucher->AccountPlanID && $voucher->KID == $MatchKID)
                    {
                        $class  = "green";
                    } else {
                        $class  = 'voucher';
                    }
                    
                    #change currency
                    if($voucher->ForeignCurrencyID != '' && $voucher->ForeignAmount > 0 && $voucher->ForeignConvRate > 0) {
                        $tmp_foreign = $voucher->ForeignCurrencyID ." ". $_lib['format']->Amount($voucher->ForeignAmount) ." / ". $voucher->ForeignConvRate;
                    } else {
                        $tmp_foreign = "Endre valuta";
                    }
                    $ch_curr = '<a href="' . $_lib['sess']->dispatch ."t=journal.edit&voucher_JournalID=" . $voucher->JournalID . '&amp;voucher_VoucherType=' . $voucher->VoucherType . '&action_journalid_search=1">' . $tmp_foreign . '</a></td>';
                    ?>
                    <tr class="<? print $class ?>">
                        <td><? print $voucher->Name; ?></td>
                        <td><? if($postmotpost->isCloseAble($AccountPlanID, $voucher->KID, $voucher->InvoiceID)) { print "*"; } ?></td>
                        <td><? print $voucher->VoucherType; ?> <a href="<? print $_lib['sess']->dispatch ."t=journal.edit&voucher_JournalID=" . $voucher->JournalID ?>&amp;voucher_VoucherType=<? print $voucher->VoucherType; ?>&action_journalid_search=1"><? print $voucher->JournalID; ?></a></td>
                        <td><? print $voucher->VoucherDate; ?></td>
                        <td><? print $voucher->VoucherPeriod; ?></td>
                        <td class="number"><nobr><? if($voucher->AmountIn > 0) { print $_lib['format']->Amount($voucher->AmountIn); } ?></nobr></td>
                        <td class="number"><nobr><? if($voucher->AmountOut > 0) { print $_lib['format']->Amount($voucher->AmountOut); } ?></nobr></td>
                    <td class="number"><nobr><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount($voucher->ForeignAmountIn); } ?></nobr></td>
                        <td class="number"><nobr><? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount($voucher->ForeignAmountOut); } ?></nobr></td>
                        <td class="number"><nobr><? print $ch_curr; ?></nobr></td>
                        <td><? if($voucher->VAT > 0)          { print $voucher->VAT; } ?></td>
                        <td><? if($voucher->Quantity > 0)     { print $voucher->Quantity; } ?></td>
                        <td><? if($voucher->DepartmentID > 0) { print $voucher->DepartmentID; } ?></td>
                        <td><? if($voucher->ProjectID > 0)    { print $voucher->ProjectID; } ?></td>
                        <td><? if(isset($voucher->DueDate))   { print $voucher->DueDate; } ?></td>
                        <td><? if(isset($voucher->DescriptionID) or isset($voucher->Description)) { print substr($voucher->DescriptionID." - ".$voucher->Description, 0, 25); } ?></td>
                        <td class="<? print $class ?>"><? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'KID', 'pk' => $voucher->VoucherID, 'value' => $voucher->KID, 'width' => 22)); ?></td>
                        <td class="<? print $class ?>"><? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'InvoiceID', 'pk' => $voucher->VoucherID, 'value' => $voucher->InvoiceID, 'width' => 22)); ?></td>
                        <td class="<? print $class ?>">
                        	<?
                        	if($postmotpost->isCloseAble($AccountPlanID, $voucher->KID, $voucher->InvoiceID)) {
                                    $closeable++;
                                    
                                    print $_lib['form3']->button(array('url'=>$_SETUP['DISPATCH']."t=postmotpost.list&amp;MatchAccountPlanID=$voucher->AccountPlanID&amp;MatchKid=$voucher->KID&amp;MatchInvoiceID=$voucher->InvoiceID&amp;AccountPlanID=$postmotpost->AccountPlanID&amp;action_postmotpost_close=1", 'name'=>'Lukk')); 
                                } else {
                                    print $_lib['format']->Amount($postmotpost->getDiff($AccountPlanID, $voucher->KID, $voucher->InvoiceID));
                                }
                                ?>
                        </td>
                        <td class="noprint"><? print $_lib['form3']->button(array('url'=> $_lib['sess']->dispatch ."t=postmotpost.list&AccountPlanID=" . $postmotpost->AccountPlanID . "&amp;MatchAccountPlanID=" . $voucher->AccountPlanID . "&amp;MatchKID=" . $voucher->KID, 'name'=>'Vis samme kid')) ?></td>
                    </tr>
                    <?
                }
                ?>
            <tr>
              <td colspan="18">
              <td colspan="6">
               <?
                 if($closeable) {
                     print $_lib['form3']->submit(
                         array('name' => 'action_postmotpost_closethis_' . $AccountPlanID, 'value'=>'Lukk denne', 'accesskey' => 'L')
                         );
                 }
                 print $_lib['form3']->submit(
                     array('name' => 'action_postmotpost_openthis_' . $AccountPlanID, 'value'=>'&Aring;pne denne', 'accesskey' => 'L')
                     );
                 print $_lib['form3']->submit(array('name'=>'action_postpost_update', 'value'=>'Lagre (S)', 'accesskey' => 'S'));
                ?>
              </td>
            </tr>

            <tr>
                <th class="sub" colspan="6">Sum for konto <? print $AccountPlanID ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->Diff); } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->Diff  < 0)  { print $_lib['format']->Amount(abs($postmotpost->sumaccountH[$AccountPlanID]->Diff)); } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->FAmountIn  > 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->FAmountIn) ; } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->FAmountOut > 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->FAmountOut); } ?></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <? } ?>
            <tr>
                <td colspan="19"></td>
            </tr>
            <tr class="voucher">
                <th class="sub" colspan="6">Sum &aring;pne poster</th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->total['total']->Diff);  }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->Diff  < 0)  { print $_lib['format']->Amount(abs($postmotpost->total['total']->Diff));  }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->FDiff >= 0) { print $_lib['format']->Amount($postmotpost->total['total']->FDiff); }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->FDiff < 0)  { print $_lib['format']->Amount(abs($postmotpost->total['total']->FDiff)); }; ?></nobr></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <tr>
                <th class="sub" colspan="6"><? print $postmotpost->total['account']->Name ?></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->total['account']->Diff);       } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->Diff  < 0 ) { print $_lib['format']->Amount(abs($postmotpost->total['account']->Diff));  } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->FDiff >= 0) { print $_lib['format']->Amount($postmotpost->total['account']->FDiff);      } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->FDiff < 0 ) { print $_lib['format']->Amount(abs($postmotpost->total['account']->FDiff)); } ?></nobr></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <tr>
                <th class="sub" colspan="6"><? print $postmotpost->total['diff']->Name ?></th>
                <th class="sub number"><nobr><? if($postmotpost->total['diff']->Diff >= 0) { print $_lib['format']->Amount($postmotpost->total['diff']->Diff);      } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['diff']->Diff < 0 ) { print $_lib['format']->Amount(abs($postmotpost->total['diff']->Diff)); } ?></nobr></th>
                <th class="sub"></th>
                <th class="sub"></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <tr class="voucher">
                <th class="sub" colspan="17"></th>
                <th class="sub number" colspan="3">
                <? print $_lib['form3']->submit(array('name'=>'action_postpost_update', 'value'=>'Lagre (S)', 'accesskey' => 'S')) ?>
                <? if($_lib['sess']->get_person('AccessLevel') >= 3) { print $_lib['form3']->submit(array('name' => 'action_postmotpost_openall', 'value'=>'&Aring;pne alle (L)', 'accesskey' => 'O')); } ?> 
                <? print $_lib['form3']->submit(array('name' => 'action_postmotpost_closeall', 'value'=>'Lukk alle (L)', 'accesskey' => 'L')) ?>

                </th>
            </tr>
        </tbody>
    </table>
<? } else { ?>
Ingen &aring;pne poster funnet
<? } ?>
    </form>
</body>
</html>
