<?
// This global is needed in some of the actions in the
// included classes and therefore should not be removed
includelogic('accounting/accounting');
$accounting = new accounting();

$MatchAccountPlanID = $_REQUEST['MatchAccountPlanID'];
$DBTable = 'voucher';

$AccountPlanID = $_REQUEST['AccountPlanID'];
$ReskontroFromAccount = $_REQUEST['ReskontroFromAccount'];
$ReskontroToAccount = $_REQUEST['ReskontroToAccount'];
$DepartmentID = $_REQUEST['report_DepartmentID'];
$ProjectID = $_REQUEST['report_ProjectID'];

includelogic('reconciliation/reconciliation');
includelogic('exchange/exchange');

$ShowAll = isset($_REQUEST["showAll"]) ? true : false;
$ShowOnly = isset($_REQUEST["showOnly"]) ? $_REQUEST["showOnly"] : "1";

if ($ShowAll) {
  $showURL = "showAll";
} else {
  $showURL = "showOnly=" . $ShowOnly;
}

$ReconciliationConfArray = array(
  'AccountPlanID' => $AccountPlanID,
  'ReskontroFromAccount' => $ReskontroFromAccount,
  'ReskontroToAccount' => $ReskontroToAccount,
  'DepartmentID' => $DepartmentID,
  'ProjectID' => $ProjectID
);
$Reconciliation = new reconciliation($ReconciliationConfArray);
require "record.inc";

if(!$ShowAll) {
  $Reconciliation->getopenpost($ShowOnly);
} else {
  $Reconciliation->getopenpost();
}

$CompanyName = $_lib['sess']->get_companydef('CompanyName');
$FirstName = $_lib['sess']->get_person('FirstName');
$LastName = $_lib['sess']->get_person('LastName');
$VName = $_lib['sess']->get_companydef('VName');

print $_lib['sess']->doctype;
?>

<head>
    <title><? print "$CompanyName : $FirstName $LastName - Post mot Post"; ?></title>
    <? includeinc('head'); ?>
    <? includeinc('javascript'); ?>
    <script type='text/javascript'>
      // Scroll to the same position as previously saved on action click
      function goBack() {
        var element_id = popCookie('scroll_element_id')
          console.log(element_id);
        if (element_id != '') {
          var new_scroll_top = $('#'+element_id).offset().top;
          var old_scroll_top = popCookie('scroll_top');
          console.log(old_scroll_top);
          $(window).scrollTop(new_scroll_top - old_scroll_top);
        }
      }

      // Save scroll position to cookies
      function saveScrollCookies() {
        setCookie('scroll_top', $(this).parents('table').offset().top - $(window).scrollTop(), 1000);
        setCookie('scroll_element_id', $(this).parents('table').attr('id'), 1000);
      }

      // Highlight element for some time
      function highlight(elementid){
        $(elementid).css('background-color','rgba(255, 182, 0, 0.6)');
        setTimeout(
          function () {
            $(elementid).css('background-color','#E1E1E1');
          },
          2500
        );
      }

      // What to do on checkbox click
      function checkboxClick(e) {
        var element = $(e.target);

        switch (e.target.id) {
          case 'invoice':
            var input = element.prev('input[type=text]');
            var kid = element.parent().next('td').children('input[type=text]');
            var match = kid.parent().next('td').children('input[type=text]');
            if (element[0].checked) {
              makeajax("invoice", "invoice", e.target.name.split('.')[2]);
            } else {
              // Legacy to set 0 if none is checked.
              makeajax("invoice", "0", e.target.name.split('.')[2]);
            }
            break;
          case 'kid':
            var input = element.parent().prev('td').children('input[type=text]');
            var kid = element.prev('input[type=text]');
            var match = element.parent().next('td').children('input[type=text]');
            if (element[0].checked) {
              makeajax("kid", "kid", e.target.name.split('.')[2]);
            } else {
              makeajax("kid", "0", e.target.name.split('.')[2]);
            }
            break;
          case 'match':
            var kid = element.parent().prev('td').children('input[type=text]');
            var input = kid.parent().prev('td').children('input[type=text]');
            var match = element.prev('input[type=text]');
            if (element[0].checked) {
              makeajax("match", "match", e.target.name.split('.')[2]);
            } else {
              makeajax("match", "0", e.target.name.split('.')[2]);
            }
            break;
        }

        var data = {
          type: e.target.id,
          name: e.target.name,
          invoiceid: input.attr("id"),
          invoiceval: input.val(),
          kidid: kid.attr('id'),
          kidval: kid.val(),
          matchid: match.attr('id'),
          matchval: match.val()
        };
        updateUI(data, element);
      }

      // Send ajax to update matched_by field
      function makeajax (type, newValue, id) {
        $.post("<?= $_SETUP['DISPATCH'] . 't=reconciliation.ajax' ?>",
          {
            type: type,
            newValue: newValue,
            id: id
          },
          function (data, status) {
            console.log("Status: " + status);
          }
        );
      }

      // Uncheck other match types on checkbox click other than the one selected
      function updateUI(data, element) {
        switch (data.type) {
          case 'invoice':
            $("input[id='" + data.kidid + "']").next('.chk').attr('checked', false);
            $("input[id='" + data.matchid + "']").next('.chk').attr('checked', false);
            break;
          case 'kid':
            $("input[id='" + data.invoiceid + "']").next('.chk').attr('checked', false);
            $("input[id='" + data.matchid + "']").next('.chk').attr('checked', false);
            break;
          case 'match':
            $("input[id='" + data.kidid + "']").next('.chk').attr('checked', false);
            $("input[id='" + data.invoiceid + "']").next('.chk').attr('checked', false);
            break;
        }
      }

      $(document).ready(
        function () {
          // Add listener to buttons
          $('input[type="submit"], button').click(
            saveScrollCookies
          );
          // Go back closest to the last saved scroll position
          goBack();
          // Handle clicking on voucher ids, highlight the rows
          $('.navigate.to').click(
            function (e) {
              var element = $(e.target);
              var targetID = element.attr('id');

              $('#row_' + targetID)[0].scrollIntoView(false);
              highlight('#row_' + targetID);
            }
          );
          // Handle clicking on checkboxes to update matched_by field in database
          $('.chk').click(
            function (e) {
              checkboxClick(e);
            }
          );
        }
      );
    </script>
</head>
<body onload="window.focus();">
  <h2><? print $VName; ?></h2>

  <? print $_lib['message']->get() ?>

  <h2>&Aring;pne poster - dvs bilag summert p&aring; kid, hvor sum ikke g&aring;r i 0</h2>
  <b>Trykk p&aring; lagre for &aring; lagre eventuelle endringer du har gjort i KID referansene - og se om postene g&aring;r mot hverandre</b><br/>
  <b>Trykk p&aring; lukk alle for &aring; lukke alle &aring;pne poster som g&aring;r i null og som det er mulig &aring; lukke</b><br/>
  <b>Trykk p&aring; &aring;pne alle for &aring; &aring;pne alle poster for alle kunder i alle perioder</b><br/>

  <p>
<?
$ViewAllLink = $_SETUP['DISPATCH'] . "t=reconciliation.list&report_Sort=JournalID&AccountPlanID=$AccountPlanID&ReskontroFromAccount=$ReskontroFromAccount&ReskontroToAccount=$ReskontroToAccount&report.DepartmentID=$DepartmentID&report.ProjectID=$ProjectID&show_report_search=Kj%F8r+rapport&showAll";
?>
    <a href="<? print $ViewAllLink; ?>">Vis alle (Advarsel: Kan tar lang tid)</a>
  </p>

<?
if(count($Reconciliation->VoucherH) > 0
  || count($Reconciliation->HiddingAccounts) > 0)
{
?>
  <form class="voucher" name="postvspost" action="<? print $MY_SELF ?>&amp;<? print $showURL ?>" method="post">
    <? print $_lib['form3']->hidden(array('name' => 'AccountPlanID', 'value' => $Reconciliation->AccountPlanID)); ?>
    <? print $_lib['form3']->hidden(array('name' => 'ReskontroFromAccount', 'value' => $Reconciliation->ReskontroFromAccount)); ?>
    <? print $_lib['form3']->hidden(array('name' => 'ReskontroToAccount', 'value' => $Reconciliation->ReskontroToAccount)); ?>
    <? print $_lib['form3']->hidden(array('name' => 'report_DepartmentID', 'value' => $Reconciliation->DepartmentID)); ?>
    <? print $_lib['form3']->hidden(array('name' => 'report_ProjectID', 'value' => $Reconciliation->ProjectID)); ?>
    <table id="pmp_table" class="lodo_data">
      <thead>
        <tr class="voucher">
          <th class="sub"></th>
          <th class="sub">Bilagsnr</th>
          <th class="sub">Bilagsdato</th>
          <th class="sub">Periode</th>
          <th class="sub align-right">Inn</th>
          <th class="sub align-right">Ut</th>
          <th class="sub align-right">Valuta</th>
          <th class="sub align-right">Valuta</th>
          <th class="sub align-right">Kurs</th>
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
  foreach ($Reconciliation->VoucherH as $AccountPlanID => $Account) {
?>
        <tr class="voucher">
          <th colspan="12"><? print $Reconciliation->SumAccountH[$AccountPlanID]->Name; ?></th>
          <th colspan="8">
<?
    // Display motkontoresultat and -balanse from accountplan
    $ResKonto = $Reconciliation->findMotKonto($AccountPlanID);
    $Last = "";
    foreach($ResKonto as $KontoKey => $Konto) {
      if (!$Konto) {
        continue;
      }
      if ($Last != substr($KontoKey, 0, -1)) {
        $Last = substr($KontoKey, 0, -1);
        printf(" %s: ", $Last);
      } else {
        printf(", ");
      }
      printf("%d", $Konto);
    }
?>
          </th>
        </tr>

<?
    $Closeable = 0;
    foreach ($Account as $Voucher) {
      $Class = 'voucher';
      // Change currency
      if ($Voucher->ForeignCurrencyID != ''
        && $Voucher->ForeignAmount > 0
        && $Voucher->ForeignConvRate > 0)
      {
        $TmpForeign = $Voucher->ForeignCurrencyID . " " . $_lib['format']->Amount($Voucher->ForeignAmount) . " / " . $Voucher->ForeignConvRate;
      } else {
        $TmpForeign = "Endre valuta";
      }
      $ChangeCurrency = '<a href="' . $_lib['sess']->dispatch ."t=journal.edit&voucher_JournalID=" . $Voucher->JournalID . '&amp;voucher_VoucherType=' . $Voucher->VoucherType . '&action_journalid_search=1">' . $TmpForeign . '</a></td>';
?>
        <tr id="row_<?= $Voucher->VoucherID ?>" class="<? print $Class ?>">
          <td><? print $Voucher->Name; ?></td>
          <td><? print $Voucher->VoucherType; ?> <a href="<? print $_lib['sess']->dispatch . "t=journal.edit&voucher_JournalID=" . $Voucher->JournalID ?>&amp;voucher_VoucherType=<? print $Voucher->VoucherType; ?>&action_journalid_search=1"><? print $Voucher->JournalID; ?></a></td>
          <td><? print $Voucher->VoucherDate; ?></td>
          <td><? print $Voucher->VoucherPeriod; ?></td>
          <td class="number">
            <nobr>
<?
      if ($Voucher->AmountIn > 0) {
        print $_lib['format']->Amount($Voucher->AmountIn);
      }
?>
            </nobr>
          </td>
          <td class="number">
            <nobr>
<?
      if ($Voucher->AmountOut > 0) {
        print $_lib['format']->Amount(-$Voucher->AmountOut);
      }
?>
            </nobr>
          </td>
          <td class="number">
            <nobr>
<?
      if ($Voucher->ForeignAmountIn > 0) {
        print $_lib['format']->Amount($Voucher->ForeignAmountIn);
      }
?>
            </nobr>
          </td>
          <td class="number">
            <nobr>
<?
      if ($Voucher->ForeignAmountOut > 0) {
        print $_lib['format']->Amount(-$Voucher->ForeignAmountOut);
      }
?>
            </nobr>
          </td>
          <td class="number">
            <nobr><? print $ChangeCurrency; ?></nobr>
          </td>
          <td>
<?
      if ($Voucher->VAT > 0) {
        print $Voucher->VAT;
      }
?>
          </td>
          <td>
<?
      if ($Voucher->Quantity > 0) {
        print $Voucher->Quantity;
      }
?>
          </td>
          <td>
<?
      if ($Voucher->DepartmentID > 0) {
        print $Voucher->DepartmentID;
      }
?>
          </td>
          <td>
<?
      if ($Voucher->ProjectID > 0) {
        print $Voucher->ProjectID;
      }
?>
          </td>
          <td>
<?
      if (isset($Voucher->DueDate)) {
        print $Voucher->DueDate;
      }
?>
          </td>
          <td>
<?
      if (isset($Voucher->DescriptionID)
        || isset($Voucher->Description))
      {
        print substr($Voucher->DescriptionID." - ".$Voucher->Description, 0, 25);
      }
?>
          </td>
<? 
      $automatic_checked_style = "";
      if ($Voucher->matched_by == "0") {
        $automatic_checked_style = ' style="outline: 2px solid #FFB600;"'; 
      }
?>
          <td class="<? print $Class ?>">
            <? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'InvoiceID', 'pk' => $Voucher->VoucherID, 'value' => $Voucher->InvoiceID, 'width' => 20, 'maxlength' => 25, 'id' => 'invoice')); ?>
            <input type="checkbox" class="chk" id="invoice" name="chk.invoice.<?= $Voucher->VoucherID ?>" value="checked" <?= $Reconciliation->isMatchable($AccountPlanID, $Voucher->KID, $Voucher->InvoiceID, $Voucher->MatchNumber, $Voucher) == 1 ? 'checked' . $automatic_checked_style : '' ?>>
          </td>
          <td class="<? print $Class ?>">
            <? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'KID', 'pk' => $Voucher->VoucherID, 'value' => $Voucher->KID, 'width' => 20, 'maxlength' => 25, 'id' => 'kid')); ?>
            <input type="checkbox" class="chk" id="kid" name="chk.kid.<?= $Voucher->VoucherID ?>" value="checked" <?= $Reconciliation->isMatchable($AccountPlanID, $Voucher->KID, $Voucher->InvoiceID, $Voucher->MatchNumber, $Voucher) == 2 ? 'checked'.$automatic_checked_style : '' ?>>
          </td>
          <td class="<? print $Class ?>">
            <? print $_lib['form3']->text(array('table'=>'voucher', 'field'=>'MatchNumber', 'pk' => $Voucher->VoucherID, 'value' =>  $Voucher->MatchNumber == "0" ? "" : $Voucher->MatchNumber, 'width' => 20, 'maxlength' => 25, 'id' => 'match')); ?>
            <input type="checkbox" class="chk" id="match" name="chk.match.<?= $Voucher->VoucherID ?>" value="checked" <?= $Reconciliation->isMatchable($AccountPlanID, $Voucher->KID, $Voucher->InvoiceID, $Voucher->MatchNumber, $Voucher) == 3 ? 'checked'.$automatic_checked_style : '' ?>>
          </td>
          <td class="<? print $Class ?>">
<?
      if ($Reconciliation->isClosableVoucher($Voucher->VoucherID)) {
        $Closeable++;
        $ButtonConfArray = array(
          'url' => $_SETUP['DISPATCH'] . "t=reconciliation.list&amp;MatchAccountPlanID=" . $Voucher->AccountPlanID . "&amp;MatchKid=" . $Voucher->KID . "&amp;MatchVoucherID=" . $Voucher->VoucherID . "&amp;MatchInvoiceID=" . $Voucher->InvoiceID . "&amp;AccountPlanID=" . $Reconciliation->AccountPlanID . "&amp;action_reconciliation_close=1&amp;$showURL",
          'name' => 'Lukk'
        );
        print $_lib['form3']->button($ButtonConfArray);
      } else {
        print $_lib['format']->Amount($Reconciliation->getDiff($AccountPlanID, $Voucher->KID, $Voucher->InvoiceID, $Voucher->MatchNumber, $Voucher));
      }
      print " " . $Reconciliation->voucherMessage($Voucher->VoucherID);
?>
          </td>
          <td class="noprint">
<?
      $ButtonConfArray = array(
        'url' => $_lib['sess']->dispatch . "t=reconciliation.list&AccountPlanID=" . $Reconciliation->AccountPlanID . "&amp;MatchAccountPlanID=" . $Voucher->AccountPlanID . "&amp;MatchKID=" . $Voucher->KID . "&amp;$showURL",
        'name' => 'Vis samme kid'
      );
      print $_lib['form3']->button($ButtonConfArray);
?>
          </td>
        </tr>
<?
    }
?>
        <tr>
          <td colspan="18">
          <td colspan="2">
            <input type="checkbox" name="selectedClose[]" value="<? echo $AccountPlanID; ?>">
<?
    if ($Closeable) {
      $ButtonConfArray = array(
        'name' => 'action_reconciliation_closethis_' . $AccountPlanID,
        'value' => 'Lukk denne',
        'accesskey' => 'L'
      );
      print $_lib['form3']->submit($ButtonConfArray);
    }
    $ButtonConfArray = array(
      'name' => 'action_reconciliation_openthis_' . $AccountPlanID,
      'value' => '&Aring;pne denne'
    );
    print $_lib['form3']->submit($ButtonConfArray);
    $ButtonConfArray = array(
      'name' => 'action_reconciliation_update',
      'value' => 'Lagre (S)',
      'accesskey' => 'S'
    );
    print $_lib['form3']->submit($ButtonConfArray);
?>
          </td>
        </tr>
        <tr>
          <th class="sub" colspan="4">Sum for konto <? print $AccountPlanID ?></th>
          <th class="sub number align-right">
<?
    if ($Reconciliation->SumAccountH[$AccountPlanID]->Diff >= 0) {
      print $_lib['format']->Amount($Reconciliation->SumAccountH[$AccountPlanID]->Diff);
    }
?>
          </th>
          <th class="sub number align-right">
<?
    if ($Reconciliation->SumAccountH[$AccountPlanID]->Diff < 0) {
      print $_lib['format']->Amount($Reconciliation->SumAccountH[$AccountPlanID]->Diff);
    }
?>
          </th>
          <th class="sub number align-right">
<?
    if ($Reconciliation->SumAccountH[$AccountPlanID]->FDiff >= 0) {
      print $_lib['format']->Amount($Reconciliation->SumAccountH[$AccountPlanID]->FDiff);
    }
?>
          </th>
          <th class="sub number align-right">
<?
    if ($Reconciliation->SumAccountH[$AccountPlanID]->FDiff < 0) {
      print $_lib['format']->Amount($Reconciliation->SumAccountH[$AccountPlanID]->FDiff);
    }
?>
          </th>
          <th class="sub" colspan="12"></th>
        </tr>
<?
  }
?>

<?
  foreach ($Reconciliation->HiddingAccounts as $Account) {
    $ShowAccountLink = $_SETUP['DISPATCH'] . "t=reconciliation.list&report_Sort=JournalID&AccountPlanID=$AccountPlanID&ReskontroFromAccount=$ReskontroFromAccount&ReskontroToAccount=$ReskontroToAccount&report.DepartmentID=$DepartmentID&report.ProjectID=$ProjectID&show_report_search=Kj%F8r+rapport&showOnly=" . $Account['AccountPlanID']; 
?>
        <tr>
          <th colspan="20">
            <a href="<? print $ShowAccountLink; ?>">+  <?= $Account['AccountName'] ?></a>
          </th>
        </tr>
        <tr>
          <td colspan="20"></td>
        </tr>
<?
  }
?>
        <tr>
          <td colspan="20"></td>
        </tr>
        <tr class="voucher">
          <th class="sub" colspan="4">Sum &aring;pne poster</th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['total']->Diff >= 0) {
    print $_lib['format']->Amount($Reconciliation->total['total']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['total']->Diff < 0) {
    print $_lib['format']->Amount($Reconciliation->total['total']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['total']->FDiff >= 0) {
    print $_lib['format']->Amount($Reconciliation->total['total']->FDiff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['total']->FDiff < 0) {
    print $_lib['format']->Amount($Reconciliation->total['total']->FDiff);
  }
?>
            </nobr>
          </th>
          <th class="sub" colspan="12"></th>
        </tr>
        <tr>
          <th class="sub" colspan="4"><? print $Reconciliation->total['account']->Name ?></th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['account']->Diff >= 0) {
    print $_lib['format']->Amount($Reconciliation->total['account']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['account']->Diff < 0) {
    print $_lib['format']->Amount($Reconciliation->total['account']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['account']->FDiff >= 0) {
    print $_lib['format']->Amount($Reconciliation->total['account']->FDiff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['account']->FDiff < 0) {
    print $_lib['format']->Amount($Reconciliation->total['account']->FDiff);
  }
?>
            </nobr>
          </th>
          <th class="sub" colspan="12"></th>
        </tr>
        <tr>
          <th class="sub" colspan="4"><? print $Reconciliation->total['diff']->Name ?></th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['diff']->Diff >= 0) {
    print $_lib['format']->Amount($Reconciliation->total['diff']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub number align-right">
            <nobr>
<?
  if ($Reconciliation->total['diff']->Diff < 0) {
    print $_lib['format']->Amount($Reconciliation->total['diff']->Diff);
  }
?>
            </nobr>
          </th>
          <th class="sub" colspan="14"></th>
        </tr>
        <tr class="voucher">
          <th class="sub" colspan="17"></th>
          <th class="sub number" colspan="3">
<?
  $ButtonConfArray = array(
    'name' => 'action_reconciliation_update',
    'value' => 'Lagre (S)',
    'accesskey' => 'S'
  );
  print $_lib['form3']->submit($ButtonConfArray);
  $ButtonConfArray = array(
    'name' => 'action_reconciliation_closeall',
    'value' => 'Lukk alle (L)',
    'accesskey' => 'L'
  );
  print $_lib['form3']->submit($ButtonConfArray);
  $ButtonConfArray = array(
    'name' => 'action_reconciliation_closeselected',
    'value' => 'Lukk utvalgte (R)',
    'accesskey' => 'R'
  );
  print $_lib['form3']->submit($ButtonConfArray);
?>

          </th>
        </tr>
      </tbody>
    </table>
<?
} else {
?>
Ingen &aring;pne poster funnet
<?
}
?>
  </form>
</body>
</html>
