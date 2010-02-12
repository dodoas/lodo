<?
#table

include  "setup.inc";
include  "$HOME_DIR/code/lodo/lib/accounting.inc";
include  "record.inc";

#################################################################################################################
#Get person data
$sql_person     = "select * from person where PersonID='$login_id'";
$result_person  = db_query($_SESSION['DB_NAME'], $sql_person);
$person         = db_fetch_object($result_person);

#################################################################################################################
#Get voucher data
$sql_voucher    = "select * from $db_table where JournalID='$JournalID'";
#print "Billag: $sql_voucher<br>";
$result_voucher = db_query($_SESSION['DB_NAME'], $sql_voucher);

#################################################################################################################
#Get date from newest voucher
$sql_date = "select VoucherDate, VoucherPeriod, DueDate from $db_table  where VoucherType='$VoucherType' order by TS desc limit 1";
#print "$sql_date<br>";
$result_date = db_query($_SESSION['DB_NAME'], $sql_date);
$date = db_fetch_object($result_date);

#################################################################################################################
$voucher    = db_fetch_object($result_voucher);
$form_i     = 1;
$i          = 1;
$form_name  = "voucher_$form_i";
$tabindex   = 1;

##############################################################
#Calculate account balance
$account_balance = "select sum(AmountIn - AmountOut) as balance from $db_table  where AccountPlanID='$voucher->AccountPlanID' group by AccountPlanID";
$voucher_balance = db_query($_SESSION['DB_NAME'], $account_balance);
$vb = db_fetch_object($voucher_balance);

if($balance >= 0) {
  $class = "debet";
} else {
  $class = "credit";
}

##############################################################
#DEFAULT VALUES
$AccountPlanID  = $voucher->AccountPlanID   ? $voucher->AccountPlanID   : $DefAccountPlanID;
$Currency       = $voucher->Currency        ? $voucher->Currency        : $accountplan->Currency;
$VATcode        = $voucher->VatCode         ? $voucher->VatCode         : $accountplan->VATAccount;
$VoucherType    = $voucher->VoucherType     ? $voucher->VoucherType     : $VoucherType;
$VoucherPeriod  = $voucher->VoucherPeriod   ? $voucher->VoucherPeriod   : $date->VoucherPeriod;
$VoucherDate    = $voucher->VoucherDate     ? $voucher->VoucherDate     : $date->VoucherDate;
$DueDate        = $voucher->DueDate         ? $voucher->DueDate         : $date->DueDate;

##############################################################
#Get accountplan info
$accountplan = accountplan($AccountPlanID);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">
<head>
    <title>Empatix - journal</title>
    <meta name="cvs"                content="$Id: edit2.php,v 1.1 2005/10/28 15:02:45 thomasek Exp $">
    <? include "$HOME_DIR/code/lib/html/head.inc"; ?>
    <script language="javascript1.1">

    function update_period(element){
       var date = element.value;
       /* var pattern = new RegExp("/(\\d+)-(\\d+)-(\\d+)/"); */
       var pattern = /(\d+)-(\d+)-(\d+)/;
       var result  = date.match(pattern)

        if(result != null) {
          var yeardate = result[1] + "-" + result[2];
          /* Find index based on this hash */
          var periodhash = new Array();
          <?
            if($sess->get_person('AccessLevel') > 2) {
              $query = "select Period from accountperiod where Status=2 or Status=3 order by Period asc";
            } else {
              $query = "select Period from accountperiod where Status=2 order by Period";
            }
            $result = db_query($_SESSION['DB_NAME'], $query);
            $row = db_fetch_object($result);
            $j = 0;
            while($row = db_fetch_object($result)) {
               $j++;
               print "periodhash['$row->Period'] = '$j';\n";
            }
          ?>

          /* Place menu */
          document.forms['voucher_1'].elements['voucher.VoucherPeriod'].selectedIndex = periodhash[yeardate];

        } else {
          alert('Dato er feilformatert, eksempel på riktig dato er: YYYY-MM-DD, 2004-04-04');
        }
    }

    function place_cursor() {
      /* document.getElementById('voucher.VoucherDate').focus(); */
      /* document.forms[document.forms.length-4].elements['voucher.VoucherDate'].focus(); */
      document.forms['voucher_1'].elements['voucher.VoucherDate'].focus();
    }

    /* Fang opp enter og submit form, funker ikke i dropdowns? */
    /* Er flytting mellom feltene med piltaster mulig? tabindex? */

    </script>



</head>
<body  onload="place_cursor();">
<? include "$HOME_DIR/code/lodo/lib/header.inc"; ?>
<? include "$HOME_DIR/code/lodo/lib/leftmenu.inc"; ?>

<h2>Bilagsregistrering</h2>
<?if($message) { ?> <div class="warning"><? print "$message"; ?></div><br><? } ?>
<form class="voucher" name="<? print "$form_name"; ?>" action="<? print "$MY_SELF"; ?>" method="post">
<input type="hidden"  name="type"               value="<? print "$type"; ?>">
<input type="hidden"  name="voucher.VoucherID"  value="<? print "$voucher->VoucherID"; ?>">
<input type="hidden"  name="voucher.JournalID"  value="<? print "$JournalID"; ?>">

<table  class="lodo_data">
  <tr class="voucher">
    <!--th><u>A</u>rt (<? print $voucher->VoucherID; ?>) -->
    <th><u>B</u>ilagsnr
    <th>Bilags<u>d</u>ato
    <th><u>P</u>eriode
    <th colspan="2">
        <?
        $aconf = array();
        $aconf['table']         = $db_table;
        $aconf['field']         = 'AccountPlanID';
        $aconf['value']         = $AccountPlanID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = 'K';
        accountplan_number_menu2($sess, $aconf);
        ?>
    <th>Saldo
    <th><? print "$accountplan->Currency"; ?>&nbsp;I<u>n</u>n
    <th><? print "$accountplan->Currency"; ?>&nbsp;U<u>t</u>
<?
  ##############################################################
  if($sess->get_person('AccessLevel') > 1) {
?>

  <tr class="voucher">
    <!--td><? Type_menu2('VoucherType', $VoucherType, 'VoucherType', $db_table, $tabindex++, 'A'); ?> -->
    <td>
      <? if(!$voucher->JournalID) { ?>
       <input class="voucher" type="text" size="3"   tabindex="<? print $tabindex++; ?>"  name="voucher.JournalID"  value="<? print $JournalID; ?>" accesskey="B">
      <? } else {
        print $JournalID;
      } ?>
    <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" name="voucher.VoucherDate" value="<? print $VoucherDate; ?>"  accesskey="D" OnChange="update_period(this);">
    <td>
        <? if($sess->get_person('AccessLevel') > 2) {
            AccountPeriod_menu2($db_table, 'VoucherPeriod', $VoucherPeriod, $person->AccessLevel, -1 ,'P','');
        } else {
            print "$VoucherPeriod";
        }
        ?>
    <td class="<? print $accountplan->DebitColor;  ?>">
      <?
      $tabindexin  = '';
      $tabindexout = '';
      if($AmountField == 'in') {
        $tabindexin  = $tabindex++;
      } else {
        $tabindexout = $tabindex++;
      } ?>
      <input class="number" type="text" size="12" tabindex="<? print $tabindexin; ?>"  accesskey="I" name="voucher.AmountIn"    value="<? print number_format($voucher->AmountIn,  $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>">
      <br><? print $accountplan->debittext; ?>
    <td class="<? print $accountplan->CreditColor; ?>">
    <input class="number" type="text" size="12" tabindex="<? print $tabindexout; ?>"  accesskey="U" name="voucher.AmountOut"    value="<? print number_format($voucher->AmountOut, $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>">
      <br><? print $accountplan->credittext; ?>
    <td class="<? print "$class"; ?>"><nobr><? print number_format($vb->balance,"2",",","."); ?>
      <?
      if($accountplan->EnableCurrency) {
          $tabindexin  = '';
          $tabindexout = '';
          if($AmountField == 'in') {
            $tabindexin  = $tabindex++;
          } else {
            $tabindexout = $tabindex++;
          }
      } ?></nobr>
    <td><? if($accountplan->EnableCurrency)   { ?><input class="number" type="text" tabindex="<? print $tabindexin; ?>" name="voucher.ForeignAmountIn"  accesskey="N" value="<? print number_format($voucher->ForeignAmountIn,  $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>" size="6"><? } ?>
    <td><? if($accountplan->EnableCurrency)   { ?><input class="number" type="text" tabindex="<? print $tabindexout; ?>" name="voucher.ForeignAmountOut" accesskey="T" value="<? print number_format($voucher->ForeignAmountOut, $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>" size="6"><? } ?>
  <tr>
    <td colspan="16"><br>
  <tr>
    <td><u>M</u>VA%
    <td>M<u>e</u>ngde
    <td>A<u>v</u>d.
    <td><u>P</u>rosjekt
    <td><u>F</u>orfallsdato
    <td><u>R</u>ef.
    <td colspan="2">Te<u>k</u>st
  <tr>
    <td><? if($accountplan->EnableVAT) {
      if($accountplan->EnableVATOverride){ ?>
        <input class="voucher" type="text" size="4"  tabindex="<? print $tabindex++; ?>" name="voucher.Vat" accesskey="M" value="<? print "$VAT"; ?>">
      <? } else { ?>
        <input type="hidden" name="voucher.Vat" value="<? print "$VAT"; ?>">
        <? #Not secure with hidden VAT code if security is very important
        print "$VAT%";
        }
      } ?>
    <td><? if($accountplan->EnableQuantity)   { ?><input class="voucher" type="text" size="5"  tabindex="<? print $tabindex++; ?>" name="voucher.Quantity" accesskey="Q" value="<? print "$voucher->Quantity"; ?>"><? } ?>
    <td><? if($accountplan->EnableDepartment) { department_menu2($db_table, 'DepartmentID', $voucher->DepartmentID, $tabindex++, 'V',''); } ?>
    <td><? if($accountplan->EnableProject)    { project_menu2($db_table,    'ProjectID',    $voucher->ProjectID, $tabindex++, 'P','');    } ?>
    <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" accesskey="F" name="voucher.DueDate"       value="<? print $DueDate; ?>">
    <td><input class="voucher" type="text" size="5" tabindex="<? print $tabindex++; ?>"  accesskey="R" name="voucher.KID"         value="<? print "$voucher->KID"; ?>">
    <td><? Type_menu2('Description', $voucher->DescriptionID, 'VoucherDescriptionID', $db_table, $tabindex++, 'E'); ?>
    <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher->Description; ?>">
    <!--td><? print "$voucher->AutomaticFromVoucherID : $voucher->AutomaticReason"; ?> -->
  <tr>
    <td colspan="16" align="right">
   <?
        if($new) { ?>
            <input type="submit" name="button_voucher_new" value="Lagre nytt bilag (S)"         tabindex="<? print $tabindex++; ?>" class="button">
        <?
        } elseif(valid_period($VoucherPeriod, $sess->get_person('AccessLevel'))) { ?>
            <input type="submit" name="button_voucher_head_update" value="Lagre postering (S)"  tabindex="<? print $tabindex++; ?>" class="button" accesskey="S" />
        <?
        }
        else { ?>
        Perioden er avsluttet
    <? } ?>
  </form>


<?
  }

if($VoucherID) {

while($voucher = db_fetch_object($result_voucher)) {
  $form_i++; $form_name = "voucher_$form_i";
  if (!($i % 2)) {  $sec_color = "BGColorLight"; } else { $sec_color = "BGColorDark"; };

  #Calculate account balance
  $account_balance = "select sum(AmountIn - AmountOut) as balance from $db_table  where AccountPlanID='$voucher->AccountPlanID' group by AccountPlanID";
  $voucher_balance = db_query($_SESSION['DB_NAME'], $account_balance);
  $vb = db_fetch_object($voucher_balance);

  ##############################################################
  #Get accountplan info
  $accountplan = accountplan($voucher->AccountPlanID);

  ##############################################################
  if($balance >= 0) {
    $class = "debet";
  } else {
    $class = "credit";
  }

  ##############################################################
  #Currency handling
  if(!$voucher->Currency) {
    $Currency = $accountplan->Currency;
  } else {
    $Currency = $voucher->Currency;
  }

  #print "VAT: kontoplan:$accountplan->VATAccount bilag:$voucher->VatCode<br>";
  #Vat default handling
  if(!$voucher->VatCode) {
    $VAT = $accountplan->VATAccount;
  } else {
    $VAT = $voucher->VatCode;
  }

  if($sess->get_person('AccessLevel') > 1) {

?>
  <form name="<? print "$form_name"; ?>" action="<? print "$MY_SELF"; ?>" method="post">
  <input type="hidden" name="type"                  value="<? print "$type"; ?>">
  <input type="hidden" name="voucher.JournalID"     value="<? print "$JournalID"; ?>">
  <input type="hidden" name="voucher.VoucherID"     value="<? print "$voucher->VoucherID"; ?>">
  <input type="hidden" name="voucher.VoucherPeriod" value="<? print "$voucher->VoucherPeriod"; ?>">
  <tr class="voucher">
    <!--th class="sub">Art (<? print $voucher->VoucherID; ?>)-->
    <td class="sub">Bilagsnr
    <td class="sub">Bilagsdato
    <td class="sub">Periode
    <td class="sub" colspan="2">
        <?
        $aconf = array();
        $aconf['table']         = $db_table;
        $aconf['field']         = 'AccountPlanID';
        $aconf['value']         = $voucher->AccountPlanID;
        $aconf['tabindex']      = '';
        $aconf['accesskey']     = 'K';
        accountplan_number_menu2($sess, $aconf);
        ?>
    <td class="sub">Saldo
    <td class="sub"><? print "$accountplan->Currency"; ?> inn
    <td class="sub"><? print "$accountplan->Currency"; ?> ut
    <td class="sub">MVA%
    <td class="sub">Mengde
    <td class="sub">Avd.
    <td class="sub">Prosjekt
    <td class="sub">Forfallsdato
    <td class="sub">Ref.
    <td class="sub" colspan="2">Tekst

  <tr class="voucher">
    <!--td><? print $voucher->VoucherType;   ?>-->
    <td><? print $voucher->JournalID;     ?>
    <td><? print $voucher->VoucherDate;   ?>
    <td><? print $voucher->VoucherPeriod; ?>
    <td class="<? print $accountplan->DebitColor;  ?>">
    <?
      $tabindexin  = '';
      $tabindexout = '';
      if($voucher->AmountIn > 0) {
        $tabindexin  = $tabindex++;
      }
      if($voucher->AmountOut > 0) {
        $tabindexout = $tabindex++;
      } ?>
        <input class="number" type="text" size="12" tabindex="<? print $tabindexin; ?>"  accesskey="I" name="voucher.AmountIn"  value="<? print number_format($voucher->AmountIn,  $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>">
        <br><? print $accountplan->debittext; ?>
    <td class="<? print $accountplan->CreditColor; ?>">
        <input class="number" type="text" size="12" tabindex="<? print $tabindexout; ?>" accesskey="U" name="voucher.AmountOut" value="<? print number_format($voucher->AmountOut, $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>">
        <br><? print $accountplan->credittext; ?>
    <td class="<? print "$class"; ?>"><? print number_format($vb->balance,"2",",","."); ?>
    <td>
      <?
      if($accountplan->EnableCurrency) {
          $tabindexin  = '';
          $tabindexout = '';
          if($voucher->ForeignAmountIn > 0) {
            $tabindexin  = $tabindex++;
          }
          if($voucher->ForeignAmountOut > 0) {
            $tabindexout = $tabindex++;
          }
      }
      if($accountplan->EnableCurrency) { ?>
        <input class="number" type="text" name="voucher.ForeignAmountIn" tabindex="<? print $tabindexin; ?>"  accesskey="N" value="<? print number_format($voucher->ForeignAmountIn,  $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>" size="6">
      <? } ?>

    <td>
        <? if($accountplan->EnableCurrency) { ?>
        <input class="number" type="text" name="voucher.ForeignAmountOut" tabindex="<? print $tabindexout; ?>" accesskey="T" value="<? print number_format($voucher->ForeignAmountOut, $nf['decimals'], $nf['dec_point'], $nf['thousands_sep']); ?>" size="6">
        <? } ?>
    <td><? if($accountplan->EnableVAT) {
      if($accountplan->EnableVATOverride){ ?>
        <input class="voucher" type="text" size="4"  tabindex="<? print $tabindex++; ?>" name="voucher.Vat" accesskey="M" value="<? print "$VAT"; ?>">
      <? } else { ?>
        <input type="hidden" name="voucher.Vat" value="<? print "$VAT"; ?>">
        <? #Not secure with hidden VAT code if security is very important
        print "$VAT%";
        }
      } ?>
    <td><? if($accountplan->EnableQuantity)   { ?><input class="voucher" type="text" size="5"  tabindex="<? print $tabindex++; ?>" accesskey="Q" name="voucher.Quantity"        value="<? print "$voucher->Quantity"; ?>"><? } ?>
    <td><? if($accountplan->EnableDepartment) { department_menu2($db_table, 'DepartmentID', $voucher->DepartmentID,  $tabindex++, 'V',''); } ?>
    <td><? if($accountplan->EnableProject)    { project_menu2($db_table,    'ProjectID',    $voucher->ProjectID,     $tabindex++, 'P',''); } ?>
    <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" name="voucher.DueDate"     accesskey="F" value="<? print $voucher->DueDate ? $voucher->DueDate : $date->DueDate ?>">
    <td><input class="voucher" type="text" size="5"  tabindex="<? print $tabindex++; ?>" name="voucher.KID"   accesskey="R" value="<? print "$voucher->KID"; ?>">
    <td><? Type_menu2('Description', $voucher->DescriptionID, 'VoucherDescriptionID', $db_table, $tabindex++, 'E'); ?>
    <td><input class="voucher" type="text" size="10" tabindex="<? print $tabindex++; ?>" accesskey="G" name="voucher.Description"       value="<? print $voucher->Description; ?>">
    <!--td><? print "$voucher->AutomaticFromVoucherID : $voucher->AutomaticReason"; ?> -->
 <?
  }
?>

  <tr>
    <td colspan="16" align="right">
    <? if(valid_period($VoucherPeriod, $sess->get_person('AccessLevel'))) { ?>
        <input type="submit" name="button_voucher_update" value="Lagre postering (S)" class="button" tabindex="<? print $tabindex++; ?>" accesskey="S" >
    <? } else { ?>
        Perioden er avsluttet
    <? } ?>
    <? if(!$voucher->Active) {
      print "Ikke lagret<br>";
    }
    ?>

  </form>
<?
  }
}
?>

</table>

<br/><br/>
<table>
<tr>
  <td>
S&oslash;k opp konto:
<td><form name="accountplan_list" action="<? print "$DISPATCH"; ?>t=accountplan.hovedbok" method="post">
<input type="text"   value="<? print $searchstring; ?>"     name="searchstring" tabindex="-1" />
<input type="hidden" value="<? print $_POST['type']; ?>"    name="type" />
<input type="hidden" value="<? print $JournalID; ?>"        name="JournalID" />
<input type="submit" value="S&oslash;k"                     name="button_accountplan_search" />
</form>
<td>
Ny hovedbokskonto:
<td><form name="accountplan_search" action="<? print "$DISPATCH"; ?>t=accountplan.hovedbok" method="post">
<input type="hidden"    value="<? print $_POST['type']; ?>" name="type" />
<input type="text"      value=""                            name="accountplan.AccountPlanID" tabindex="-1" />
<input type="hidden"    value="<? print $JournalID; ?>"     name="JournalID" />
<input type="submit"    value="Ny hovedbok"                 name="button_accountplan_new" />
</form>
<td>
Ny reskontro:
<td><form name="accountplan_search" action="<? print "$DISPATCH"; ?>t=accountplan.reskontro" method="post">
<input type="hidden"    value="<? print $_POST['type']; ?>" name="type" />
<input type="text"      value=""                            name="accountplan.AccountPlanID" tabindex="-1"/>
<input type="hidden"    value="<? print $JournalID; ?>"     name="JournalID" />
<input type="submit"    value="Ny reskontro"                name="button_accountplan_new" />
</form>
</table>
<? include "$HOME_DIR/code/lodo/lib/footer.inc"; ?>
</body>
</html>