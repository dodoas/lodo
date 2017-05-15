<?

includelogic('exchange/exchange');

##################################
#
# Functions used in the bookkeeping register view journal/edit
#
##################################

class framework_logic_vouchergui
{
    #comments to voucher lines
    function comment($voucher) {
        $commentstring =  "Postering: ".$voucher->VoucherID;
        if($voucher->AutomaticReason)
            $commentstring .= " - Kilde: ".$voucher->AutomaticReason;
        if($voucher->AutomaticVatVoucherID)
            $commentstring .= " - Opprettet MVA: ".$voucher->AutomaticVatVoucherID;
        if($voucher->AutomaticFromVoucherID)
            $commentstring .= " - Opprettet automatisk fra linje: ".$voucher->AutomaticFromVoucherID;
        if($voucher->DisableAutoVat)
            $commentstring .= " - Auto MVA: ".$voucher->DisableAutoVat;
        if($voucher->AutoKID)
            $commentstring .= " - Auto KID: ".$voucher->AutoKID;
        if($voucher->Active)
            $commentstring .= " - Aktiv";
        else
            $commentstring .= " - Inaktiv";

        return $commentstring;
    }

    # Vat fields in voucher lines
    function vat($voucher, $accountplan, $VAT, $oldVatID, $VatID, $VatPercent, $closed = false) {
        global $_lib;
        $html = '';

        if( ($accountplan->EnableVAT == 1) and ($voucher->DisableAutoVat != 1) )
        {
            if($accountplan->EnableVATOverride or $VAT->EnableVatOverride)
            {
                $html .= $VatID . ' ' . '<nobr>' . $_lib['form3']->text(array('name' => 'voucher.Vat', 'value' => $VatPercent, 'class' => 'voucher', 'width' => '4', 'tabindex' => $tabindex++, 'accesskey' => 'M', 'readonly' => $closed)) . '%</nobr>';
                $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatID', 'value' => $VatID));
                $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatOld',   'value' => $VatPercent));
                $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatIDOld', 'value' => $VatID));
            }
            else
            {
                if(isset($VatPercent))
                {
                    #Not secure with hidden VAT code if security is very important
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.Vat',      'value' => $VatPercent));
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatID',    'value' => $VatID));
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatOld',   'value' => $VatPercent));
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatIDOld', 'value' => $VatID));

                    if($oldVatID != $VatID)
                        $html .= $VatID . ' ' . '<font color="red">'.$VatPercent.'%</font>';
                    else
                        $html .= $VatID . ' ' . $VatPercent."%";
                }
            }
        } else {
          if($voucher->Vat > 0) {
            $html .= $voucher->VatID . ' ' . $voucher->Vat . "%";
          }
        }

        return $html;
    }

    # Currency fields in voucher lines
    function currency($voucher, $accountplan, $vb, $class) {
        global $_lib, $tabindex;

        $html = '<td class="' . $class1 . '"><nobr>' . $_lib['format']->Amount(array('value'=>($vb->sumin - $vb->sumout), 'return'=>'value'));
        if($accountplan->EnableCurrency) {
            $tabindexin  = '';
            $tabindexout = '';
            if($AmountField == 'in')
            {
              $tabindexin  = $tabindex++;
            }
            else
            {
              $tabindexout = $tabindex++;
            }
        }
        $html .= '</nobr>';

        $html .= '<td>';
        if($accountplan->EnableCurrency) {
            $html .= $_lib['form3']->text(array('name' => 'voucher.ForeignAmountIn', 'value' => $_lib['format']->Amount($voucher->ForeignAmountIn), 'class' => 'number', 'size' => '6', 'tabindex' => $tabindexin, 'accesskey' => 'N')) . $accountplan->Currency;
        };
        $html .= '</td>';

        $html .= '<td>';
        if($accountplan->EnableCurrency) {
            $html .= $_lib['form3']->text(array('name' => 'voucher.ForeignAmountOut', 'value' => $_lib['format']->Amount($voucher->ForeignAmountOut), 'class' => 'number', 'size' => '6', 'tabindex' => $tabindexout, 'accesskey' => 'T')) . $accountplan->Currency;
        }
        $html .= '</td>';

        return $html;
    }


    /***************************************************************************
    * Show currency imported from Fakturabank
    * @param
    * @return
    */
    function currency2($voucher, $editable = true) {
        global $_lib;

        $html = '';

        #Converted to local currency from foreign currency
        $is_foreign = $voucher->ForeignCurrencyID && $voucher->ForeignAmount && $voucher->ForeignConvRate;

        $currencies = exchange::getActiveCurrencies();

        if (!empty($currencies)) {
            $in_or_out = ($voucher->AmountIn > 0 ? 'in' : ($voucher->AmountOut > 0 ? 'out' : ''));

            $html = '';

            $html .= '<td>';

            $voucher_foreign_currency = $voucher->ForeignCurrencyID;
            if($editable) {
                $select_options = '<option value="">'. exchange::getLocalCurrency() .'</option>';
                foreach ($currencies as $currency) {
                    if ($voucher_foreign_currency && $currency->CurrencyISO == $voucher_foreign_currency)
                        $select_options .= '<option value="'. $currency->CurrencyISO .'" selected="selected">'. $currency->CurrencyISO .'</option>';
                    else
                        $select_options .= '<option value="'. $currency->CurrencyISO .'">'. $currency->CurrencyISO .'</option>';
                }
                $html .= '<select name="voucher.ForeignCurrencyID" onchange="onCurrencyChange(this)">'. $select_options .'"</select>';
            } else {
                $html .= $voucher_foreign_currency;
            }
            $html .= '</td>';

            $foreign_amount_in =  ($in_or_out == 'in'  ? $voucher->ForeignAmount : 0);
            $foreign_amount_out = ($in_or_out == 'out' ? $voucher->ForeignAmount : 0);

            #AmountIn
            $html .= '<td>';
            if($editable) {
                $html .=  $_lib['form3']->text(array('name' => 'voucher.ForeignAmountIn', 'value' => $_lib['format']->Amount($foreign_amount_in), 'class' => 'number currency_field', 'OnChange' => "this.value = toAmountString(toNumber(this.value)); allowOnlyCreditOrDebit(this, 'credit')", 'width' => '12', 'style' => ($is_foreign ? 'text-align: right;' : 'display: none;')));
            } else {
                $html .= '<div style="width: 80px !important;">'. $_lib['format']->Amount($foreign_amount_in) .'</div>';
            }
            $html .= '</td>';

            #AmountOut
            $html .= '<td>';
            if($editable) {
                $html .=  $_lib['form3']->text(array('name' => 'voucher.ForeignAmountOut', 'value' => $_lib['format']->Amount($foreign_amount_out), 'class' => 'number currency_field', 'OnChange' => "this.value = toAmountString(toNumber(this.value)); allowOnlyCreditOrDebit(this, 'debit')", 'width' => '12', 'style' => ($is_foreign ? 'text-align: right;' : 'display: none;')));
            } else {
                $html .= '<div style="width: 80px !important;">'. $_lib['format']->Amount($foreign_amount_out) .'</div>';
            }
            $html .= '</td>';

            if($editable) {
                $html .= '<td>'.'<div class="currency_field" style="'. ($is_foreign ? '' : 'display: none;') .'">'.'<input class="number currency_rate" type="text" name="voucher.ForeignConvRate" size="10" value="'. $_lib['format']->Amount($voucher->ForeignConvRate) .'" onchange="this.value = toAmountString(toNumber(this.value))"> = 100'. exchange::getLocalCurrency() .'</div></td>';
            } else {
                $html .= '<td style="text-align: right;">'. ($is_foreign ? $_lib['format']->Amount($voucher->ForeignConvRate) .' = 100NOK' : '') .'</td>';
            }
        } else {
          $html .= '<td colspan="4"></td>';
        }

        return $html;
    }

    # Print accout menu on line
    function account($VoucherPeriod, $new, $db_table, $voucher, $AccountPlanID, $autosubmit, $disabled = false) {
        global $_lib, $accounting, $tabindex;
        $html = '';

        if(!$voucher->DisableAutoVat) {
            if($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) || isset($new)) {
                $aconf = array();
                $aconf['table']         = $db_table;
                $aconf['field']         = 'AccountPlanID';
                $aconf['value']         = $AccountPlanID;
                $aconf['tabindex']      = $tabindex++;
                $aconf['accesskey']     = 'K';
                if(!$AccountPlanID) $aconf['class'] = 'redbackground';
                $aconf['autosubmit']    = $autosubmit;
                $aconf['type'][]        = 'reskontro';
                $aconf['type'][]        = 'hovedbok';
                $aconf['type'][]        = 'employee';
                $aconf['disabled']      = $disabled;
                $html .=  $_lib['form3']->accountplan_number_menu($aconf);
            } else {
                $acctmp = $accounting->get_accountplan_object($AccountPlanID);
                $html  .= $acctmp->AccountName;
            }
        } else {
            $account = $accounting->get_accountplan_object($AccountPlanID);
            $html    = $AccountPlanID . '-' . $account->AccountName;
        }
        return $html . "\n";
    }

    # Print credit/debit fields in td on line
    function creditdebitfield($AmountField, $accountplan, $AmountIn, $AmountOut, $closed = false) {
        global $_lib, $tabindex;

        $tabindexin  = '';
        $tabindexout = '';
        if($closed) {
          $readonly = "readonly disable";
        } else {
          if($AmountField == 'in')
          {
              $tabindexin = $tabindex++;
          }
          else
          {
              $tabindexout = $tabindex++;
          }
        }

        $html = '<td class="' . $accountplan->DebitColor . '" style="text-align: right;">';
        $html .= $_lib['form3']->text(array('name' => 'voucher.AmountIn', 'readonly' => $readonly, 'value' => $_lib['format']->Amount($AmountIn), 'class' => 'number', 'width' => '12', 'tabindex' => $tabindexin, 'accesskey' => 'I', 'OnChange' => 'return allowOnlyCreditOrDebit(this, \'credit\')'));
        $html .= '<br>' . $accountplan->debittext;
        $html .= "</td>\n";
        $html .= '<td class="' . $accountplan->CreditColor . '" style="text-align: right;">';
        $html .= $_lib['form3']->text(array('name' => 'voucher.AmountOut', 'readonly' => $readonly, 'value' => $_lib['format']->Amount($AmountOut), 'class' => 'number', 'width' => '12', 'tabindex' => $tabindexout, 'accesskey' => 'I', 'OnChange' => 'return allowOnlyCreditOrDebit(this, \'debit\')'));
        $html .= '<br>' . $accountplan->credittext;
        $html .= "</td>\n";

        return $html;
    }

    #Buttons on line
    function update_journal_button_line($voucher, $VoucherPeriod, $JournalID, $VoucherType, $type, $button) {
        global $_lib, $accounting, $tabindex, $MY_SELF;
        $html = '';

        $view_mvalines      = $_lib['input']->getProperty('view_mvalines');
        $view_linedetails   = $_lib['input']->getProperty('view_linedetails');

        if($_lib['sess']->get_person('AccessLevel') >= 2)
        {
            if($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) && $voucher->VoucherType != 'A')
            {
                if($voucher->DisableAutoVat !=1 )
                {
                    if($button == 'delete') {
                        $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$voucher->VoucherPeriod&amp;voucher.VoucherDate=$voucher->VoucherDate&amp;voucher.JournalID=$JournalID&amp;voucher.VoucherID=$voucher->VoucherID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_delete=1&amp;view_mvalines=$view_mvalines&amp;view_linedetails=$view_linedetails", 'name'=>'<img src="/lib/icons/trash.gif">', 'confirm' => 'Vil du virkelig slette linjen?'));
                    }
                    if($button == 'update') {
                        $html .= '<input type="submit"  id="save_button_' . $voucher->VoucherID . '" name="action_voucher_update" value="Lagre" class="green" tabindex="' . $tabindex++ . '" accesskey="S" >';
                    }
                }
            }
            elseif($voucher->VoucherType == 'A') {
                if($button != 'update') {
                    $html .= "Det er ikke lov &aring; endre auto bilag";
                }
            } else {
                if($button != 'update') {
                    $html .= "Perioden er avsluttet";
                }
            }
        }
        return $html;
    }

    # Marks the active line with >>
    function active_line($VoucherID1, $VoucherID2) {
        global $voucher_input;

        if($VoucherID1 == $VoucherID2) {
            $html = ">>";
        }
        return $html;
    }

    #Buttons on head
    function update_journal_button_head($voucherHead, $VoucherPeriod, $VoucherType, $JournalID, $new, $rowCount, $button) {
        global $_lib, $tabindex, $accounting, $MY_SELF;

        $html = '';

        if($_lib['sess']->get_person('AccessLevel') >= 2) {

            if($new)
            {
                if($button = 'update') {
                    $html .= '<input type="submit" id="save_button_' . $voucherHead->VoucherID . '" name="action_voucher_new" value="Lagre" class="green" tabindex="';
                    if($rowCount>1) {
                        $html .= '';
                    } else {
                        $html .= $tabindex++;
                    }
                    $html .= ' class="button">';
                }
            }
            elseif(($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) && $voucherHead->VoucherType != 'A') || $VoucherPeriod == '0000-00')
            {
                if($button == 'delete') {
                    $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$VoucherPeriod&amp;voucher.VoucherDate=$voucherHead->VoucherDate&amp;voucher.JournalID=$JournalID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_head_delete=1", 'name'=>'<img src="/lib/icons/trash.gif">', 'confirm' => 'Vil du virkelig slette bilaget?'));
                }
                if($button == 'update') {
                    $html .= '<input type="submit" id="save_button_' . $voucherHead->VoucherID . '" name="action_voucher_head_update" value="Lagre" class="green" tabindex="';
                    if($rowCount>1) {
                        $html .= '';
                    } else {
                        $html .= $tabindex++;
                    }
                    $html .= '" class="button" accesskey="S" />';
                }
            }
            elseif($voucherHead->VoucherType == 'A') {
                if($button != 'update') $html .= "Det er ikke lov &aring; endre auto bilag";
            } else {
                if($button != 'update') $html .= "Perioden er avsluttet";
            }
        }

        return $html;
    }
}
?>
