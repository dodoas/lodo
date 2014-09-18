<?
//xdebug_start_trace("/home/martinaw/public_html/stack.log");
#print_r($_REQUEST);
$MatchAccountPlanID     = $_REQUEST['MatchAccountPlanID'];
$db_table         = 'voucher';

#print_r($_REQUEST);

includelogic('accounting/accounting');
$accounting = new accounting();
includelogic('postmotpost/postmotpost');
includelogic('exchange/exchange');

$showAll = isset($_REQUEST["showAll"]) ? true : false;
$showOnly = isset($_REQUEST["showOnly"]) ? $_REQUEST["showOnly"] : "1";

if($showAll) {
    $showURL = "showAll";
}
else {
    $showURL = "showOnly=" . $showOnly;
}

$postmotpost = new postmotpost(array('AccountPlanID' => $_REQUEST['AccountPlanID'], 'ReskontroFromAccount' => $_REQUEST['ReskontroFromAccount'], 'ReskontroToAccount' => $_REQUEST['ReskontroToAccount'], 'DepartmentID' => $_REQUEST['report_DepartmentID'], 'ProjectID' => $_REQUEST['report_ProjectID']));
require "record.inc";

if(!$showAll) {
    $postmotpost->getopenpost($showOnly);
}
else {
    $postmotpost->getopenpost();
}

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

<p>
<a href="<?= $_SETUP['DISPATCH'] ?>t=postmotpost.list&report_Sort=JournalID&AccountPlanID=<?= $_REQUEST['AccountPlanID'] ?>&ReskontroFromAccount=<?= $_REQUEST['ReskontroFromAccount'] ?>&ReskontroToAccount=<?= $_REQUEST['ReskontroToAccount'] ?>&report.DepartmentID=<?= $_REQUEST['report_DepartmentID'] ?>&report.ProjectID=<?= $_REQUEST['report_ProjectID'] ?>&show_report_search=Kj%F8r+rapport&showAll">Vis alle (Advarsel: Kan tar lang tid)</a>
</p>

<?
#print_r($postmotpost);
if(count($postmotpost->voucherH) > 0 || count($postmotpost->hidingAccounts) > 0)
{
?>
    <form class="voucher" name="postvspost" action="<? print $MY_SELF ?>&amp;<?= $showURL ?>" method="post">
    <? print $_lib['form3']->hidden(array('name'=>'AccountPlanID',          'value' => $postmotpost->AccountPlanID)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'ReskontroFromAccount',   'value' => $postmotpost->ReskontroFromAccount)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'ReskontroToAccount',     'value' => $postmotpost->ReskontroToAccount)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'report_DepartmentID',    'value' => $postmotpost->DepartmentID)) ?>
    <? print $_lib['form3']->hidden(array('name'=>'report_ProjectID',       'value' => $postmotpost->ProjectID)) ?>
    <table  class="lodo_data">
        <thead>
            <tr class="voucher">
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

                <th class="sub">Fakturanr</th>
                <th class="sub">KID</th>
                <th class="sub">MatchNummer</th>

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

                    $class  = 'voucher';



                    #change currency
                    if($voucher->ForeignCurrencyID != '' && $voucher->ForeignAmount > 0 && $voucher->ForeignConvRate > 0) {
                        $tmp_foreign = $voucher->ForeignCurrencyID ." ". $_lib['format']->Amount($voucher->ForeignAmount) ." / ". $voucher->ForeignConvRate;
                    } else {
                        $tmp_foreign = "Endre valuta";
                    }
                    $ch_curr = '<a href="' . $_lib['sess']->dispatch ."t=journal.edit&voucher_JournalID=" . $voucher->JournalID . '&amp;voucher_VoucherType=' . $voucher->VoucherType . '&action_journalid_search=1">' . $tmp_foreign . '</a></td>';
                    ?>
                    <tr id="row_<?= $voucher->VoucherID ?>" class="<? print $class ?>">
                        <td><? print $voucher->Name; ?></td>
                        <td><? print $voucher->VoucherType; ?> <a href="<? print $_lib['sess']->dispatch ."t=journal.edit&voucher_JournalID=" . $voucher->JournalID ?>&amp;voucher_VoucherType=<? print $voucher->VoucherType; ?>&action_journalid_search=1"><? print $voucher->JournalID; ?></a></td>
                        <td><? print $voucher->VoucherDate; ?></td>
                        <td><? print $voucher->VoucherPeriod; ?></td>
                        <td class="number"><nobr><? if($voucher->AmountIn > 0) { print $_lib['format']->Amount($voucher->AmountIn); } ?></nobr></td>
                        <td class="number"><nobr>-<? if($voucher->AmountOut > 0) { print $_lib['format']->Amount($voucher->AmountOut); } ?></nobr></td>
                        <td class="number"><nobr><? if($voucher->ForeignAmountIn > 0) { print $_lib['format']->Amount($voucher->ForeignAmountIn); } ?></nobr></td>
                        <td class="number"><nobr>-<? if($voucher->ForeignAmountOut > 0) { print $_lib['format']->Amount($voucher->ForeignAmountOut); } ?></nobr></td>
                        <td class="number"><nobr><? print $ch_curr; ?></nobr></td>
                        <td><? if($voucher->VAT > 0)          { print $voucher->VAT; } ?></td>
                        <td><? if($voucher->Quantity > 0)     { print $voucher->Quantity; } ?></td>
                        <td><? if($voucher->DepartmentID > 0) { print $voucher->DepartmentID; } ?></td>
                        <td><? if($voucher->ProjectID > 0)    { print $voucher->ProjectID; } ?></td>
                        <td><? if(isset($voucher->DueDate))   { print $voucher->DueDate; } ?></td>
                        <td><? if(isset($voucher->DescriptionID) or isset($voucher->Description)) { print substr($voucher->DescriptionID." - ".$voucher->Description, 0, 25); } ?></td>

                        <td class="<? print $class ?>">
                          <? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'InvoiceID', 'pk' => $voucher->VoucherID, 'value' => $voucher->InvoiceID, 'width' => 20, 'maxlength' => 25, 'id' => 'invoice')); ?>
                          <input type="checkbox" class="chk" id="invoice" name="chk.invoice.<?= $voucher->VoucherID ?>" value="checked" <?= $postmotpost->isMatchable($AccountPlanID, $voucher->KID, $voucher->InvoiceID, $voucher->MatchNumber, $voucher) == 1 ? 'checked' : '' ?>>
                        </td>

                        <td class="<? print $class ?>">
                          <? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'KID', 'pk' => $voucher->VoucherID, 'value' => $voucher->KID, 'width' => 20, 'maxlength' => 25, 'id' => 'kid')); ?>
                          <input type="checkbox" class="chk" id="kid" name="chk.kid.<?= $voucher->VoucherID ?>" value="checked" <?= $postmotpost->isMatchable($AccountPlanID, $voucher->KID, $voucher->InvoiceID, $voucher->MatchNumber, $voucher) == 2 ? 'checked' : '' ?>>
                        </td>

                        <td class="<? print $class ?>">
                          <? print $_lib['form3']->text(array('table'=>'vouchermatch', 'field'=>'MatchNumber', 'pk' => $voucher->VoucherMatchID, 'value' =>  $voucher->MatchNumber == "0" ? "" : $voucher->MatchNumber, 'width' => 20, 'maxlength' => 25, 'id' => 'match')); ?>
                         <input type="checkbox" class="chk" id="match" name="chk.match.<?= $voucher->VoucherMatchID ?>" value="checked" <?= $postmotpost->isMatchable($AccountPlanID, $voucher->KID, $voucher->InvoiceID, $voucher->MatchNumber, $voucher) == 3 ? 'checked' : '' ?>>

                        </td>

                        <td class="<? print $class ?>">
                          <?
                                if($postmotpost->isCloseAbleVoucher($voucher->VoucherID)) {
                                  $closeable++;
                                  print $_lib['form3']->button(array('url'=>$_SETUP['DISPATCH']."t=postmotpost.list&amp;MatchAccountPlanID=$voucher->AccountPlanID&amp;MatchKid=$voucher->KID&amp;MatchVoucherID=$voucher->VoucherID&amp;MatchInvoiceID=$voucher->InvoiceID&amp;AccountPlanID=$postmotpost->AccountPlanID&amp;action_postmotpost_close=1&amp;$showURL", 'name'=>'Lukk'));
                                } else {
                                  print $_lib['format']->Amount($postmotpost->getDiff($AccountPlanID, $voucher->KID, $voucher->InvoiceID, $voucher->MatchNumber, $voucher));
                                }
                                ?>

                                <? echo $postmotpost->voucherMessage($voucher->VoucherID); ?>
                        </td>
                        <td class="noprint"><? print $_lib['form3']->button(array('url'=> $_lib['sess']->dispatch ."t=postmotpost.list&AccountPlanID=" . $postmotpost->AccountPlanID . "&amp;MatchAccountPlanID=" . $voucher->AccountPlanID . "&amp;MatchKID=" . $voucher->KID . "&amp;$showURL", 'name'=>'Vis samme kid')) ?></td>
                    </tr>
                    <?
                }
                ?>

            <tr>
              <td colspan="18">
              <td colspan="6">
                <input type="checkbox" name="selectedClose[]" value="<? echo $AccountPlanID; ?>">
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
                <th class="sub" colspan="5">Sum for konto <? print $AccountPlanID ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->Diff); } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->Diff  < 0)  { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->Diff); } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->FAmountIn  > 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->FAmountIn) ; } ?></th>
                <th class="sub number"><? if($postmotpost->sumaccountH[$AccountPlanID]->FAmountOut > 0) { print $_lib['format']->Amount($postmotpost->sumaccountH[$AccountPlanID]->FAmountOut); } ?></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <? } ?>


           <? foreach($postmotpost->hidingAccounts as $account) { ?>
           <tr>
             <th colspan=21>
             <a href="<?= $_SETUP['DISPATCH'] ?>t=postmotpost.list&report_Sort=JournalID&AccountPlanID=<?= $_REQUEST['AccountPlanID'] ?>&ReskontroFromAccount=<?= $_REQUEST['ReskontroFromAccount'] ?>&ReskontroToAccount=<?= $_REQUEST['ReskontroToAccount'] ?>&report.DepartmentID=<?= $_REQUEST['report_DepartmentID'] ?>&report.ProjectID=<?= $_REQUEST['report_ProjectID'] ?>&show_report_search=Kj%F8r+rapport&showOnly=<?= $account['AccountPlanID'] ?>">
             +  <?= $account['AccountName'] ?>
             </a>
             </th>
           </tr>
           <tr><td></td></tr>
           <? } ?>


            <tr>
                <td colspan="19"></td>
            </tr>
            <tr class="voucher">
                <th class="sub" colspan="6">Sum &aring;pne poster</th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->total['total']->Diff);  }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->Diff  < 0)  { print $_lib['format']->Amount($postmotpost->total['total']->Diff);  }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->FDiff >= 0) { print $_lib['format']->Amount($postmotpost->total['total']->FDiff); }; ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['total']->FDiff < 0)  { print $_lib['format']->Amount($postmotpost->total['total']->FDiff); }; ?></nobr></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <tr>
                <th class="sub" colspan="6"><? print $postmotpost->total['account']->Name ?></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->Diff  >= 0) { print $_lib['format']->Amount($postmotpost->total['account']->Diff);       } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->Diff  < 0 ) { print $_lib['format']->Amount($postmotpost->total['account']->Diff);  } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->FDiff >= 0) { print $_lib['format']->Amount($postmotpost->total['account']->FDiff);      } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['account']->FDiff < 0 ) { print $_lib['format']->Amount($postmotpost->total['account']->FDiff); } ?></nobr></th>
                <th class="sub" colspan="10"></th>
            </tr>
            <tr>
                <th class="sub" colspan="6"><? print $postmotpost->total['diff']->Name ?></th>
                <th class="sub number"><nobr><? if($postmotpost->total['diff']->Diff >= 0) { print $_lib['format']->Amount($postmotpost->total['diff']->Diff);      } ?></nobr></th>
                <th class="sub number"><nobr><? if($postmotpost->total['diff']->Diff < 0 ) { print $_lib['format']->Amount($postmotpost->total['diff']->Diff); } ?></nobr></th>
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
                <? print $_lib['form3']->submit(array('name' => 'action_postmotpost_closeselected', 'value'=>'Lukk utvalgte (R)', 'accesskey' => 'R')) ?>

                </th>
            </tr>
        </tbody>
    </table>
<? } else { ?>
Ingen &aring;pne poster funnet
<? } ?>
    </form>
    <script type="text/javascript">
      $(document).ready(function() {


        // Handle clicking on voucher ids
        $('.navigate.to').click(function(e) {
          var element = $(e.target);
          var targetID = element.attr('id');

          $('#row_' + targetID)[0].scrollIntoView( false );
          hlight('#row_' + targetID);

        });

        function hlight(elementid){
          $(elementid).css('background-color','rgba(255, 182, 0, 0.6)');
          setTimeout(function() { $(elementid).css('background-color','#E1E1E1'); } , 2500);
        }

        // Handle clicking on checkboxes to match vouchers
        var tempdata = [];
        $('.chk').click(function(e) {
          var element = $(e.target);

          if(element.attr('checked')) {

            switch(e.target.id) {
              case 'invoice':
                var input = element.prev('input[type=text]');
                var kid = element.parent().next('td').children('input[type=text]');
                var match = kid.parent().next('td').children('input[type=text]');
                makeajax("invoice", e.target.name.split('.')[2]);
              break;
              case 'kid':
                var input = element.parent().prev('td').children('input[type=text]');
                var kid = element.prev('input[type=text]');
                var match = element.parent().next('td').children('input[type=text]');
                makeajax("kid", e.target.name.split('.')[2]);
              break;
              case 'match':
                var kid = element.parent().prev('td').children('input[type=text]');
                var input = kid.parent().prev('td').children('input[type=text]');
                var match = element.prev('input[type=text]');
                makeajax("match", e.target.name.split('.')[2]);
              break;
            }
          } else {
            // That or just dont allow uncheking at all?
            makeajax("none", e.target.name.split('.')[2]);
          }

          var data = { type: e.target.id, name: e.target.name, invoiceid: input.attr("id"), invoiceval: input.val(), kidid: kid.attr('id'), kidval: kid.val(), matchid: match.attr('id'), matchval: match.val() };

          updateUI(data, element);
        });

        function makeajax (type, id) {
          $.post("<?= $_SETUP['DISPATCH'] . 't=postmotpost.ajax' ?>",
          {
            type: type,
            id: id
          },
          function(data,status){
            console.log("Status: " + status);
          });
        }

        function updateUI(data, element) {
          switch(data.type) {
            case 'invoice':
              $("input[id='" + data.kidid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.kidid + "']").val('');

              $("input[id='" + data.matchid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.matchid + "']").val('');
            break;
            case 'kid':
              $("input[id='" + data.invoiceid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.invoiceid + "']").val('');

              $("input[id='" + data.matchid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.matchid + "']").val('');
            break;
            case 'match':
              $("input[id='" + data.kidid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.kidid + "']").val('');

              $("input[id='" + data.invoiceid + "']").next('.chk').attr('checked', false);
              // $("input[id='" + data.invoiceid + "']").val('');
            break;
          }
        }
      });
    </script>
</body>
</html>
