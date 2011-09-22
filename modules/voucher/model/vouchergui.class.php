<?

includelogic('exchange/exchange');

##################################
#
# Funksjoner som brukes i bilagsregistreringsskjermbildet journal/edit
#
##################################

class framework_logic_vouchergui
{
    /***************************************************************************
    * comment
    * @param
    * @return
    */
    #comments to posteringslinjer
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
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */
    #Vat fields in posteringslinjer
    function vat($voucher, $accountplan, $VAT, $oldVatID, $VatID, $VatPercent) {
        global $_lib;
        $html = '';
     
        if( ($accountplan->EnableVAT == 1) and ($voucher->DisableAutoVat != 1) )
        {
            if($accountplan->EnableVATOverride or $VAT->EnableVatOverride)
            {
                
                $html .= $VatID . ' ' . '<nobr>' . $_lib['form3']->text(array('name' => 'voucher.Vat', 'value' => $VatPercent, 'class' => 'voucher', 'width' => '4', 'tabindex' => $tabindex++, 'accesskey' => 'M')) . '%</nobr>';
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
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */
    function currency($voucher, $accountplan, $vb, $class) {
        global $_lib, $tabindex;
    
        $html = '<td class="' . $class1 . '"><nobr>' . $_lib['format']->Amount(array('value'=>($vb->sumin - $vb->sumout), 'return'=>'value'));
        if($accountplan->EnableCurrency) {
            $tabindexin  = '';
            $tabindexout = '';
            if($AmountField == 'in')
            {
              $tabindexout = '';
              $tabindexin  = $tabindex++;
            }
            else
            {
              $tabindexin  = '';
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
    function currency2($voucher) {
        global $_lib;
    
        $html = '';
        
        #Converted to local currency from foreign currency
        $is_foreign = false;
        if ($voucher->ForeignCurrencyID && $voucher->ForeignAmount && $voucher->ForeignConvRate) {
            $tmp_foreign = $voucher->ForeignCurrencyID ." ". $_lib['format']->Amount($voucher->ForeignAmount);
            $is_foreign = true;
        } else {
            $tmp_foreign = "Sett valuta";
        }

        $html .= '<td></td>';
        
        #Show conversion rate
        $html .= '<td>';
        if($is_foreign) {
            $html .= exchange::getAnchorVoucherForeignCurrency($voucher->VoucherID, 'Rate '. $voucher->ForeignConvRate);
        } else {
            $html .= exchange::getAnchorVoucherForeignCurrency($voucher->VoucherID, 'Valuta');
        }
        $html .= exchange::getFormVoucherForeignCurrency($voucher->VoucherID, $voucher->ForeignAmount, $voucher->ForeignConvRate, $voucher->ForeignCurrencyID, '', false);
        $html .= '</td>';

        #AmountIn
        $html .= '<td>';
        if($is_foreign && $voucher->AmountIn > 0) {
            $html .= $tmp_foreign;
        };
        $html .= '</td>';


        #AmountOut
        $html .= '<td>';
        if($is_foreign && $voucher->AmountOut > 0) {
            $html .= $tmp_foreign;
        };
        $html .= '</td>';

        return $html;
    }
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */
    #Print accout menu on line
    function account($VoucherPeriod, $new, $db_table, $voucher, $AccountPlanID, $autosubmit) {
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
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */
    #Print credit/debit fields in td menu on line
    function creditdebitfield($AmountField, $accountplan, $AmountIn, $AmountOut) {
        global $_lib, $tabindex;
        $html = '<td class="' . $accountplan->DebitColor . '">';
        $tabindexin  = '';
        $tabindexout = '';
        if($accountplan->EnableCurrency) { #Not possibel to edit in and out when currency is enabled
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
    
        $html .= $_lib['form3']->text(array('name' => 'voucher.AmountIn', 'readonly' => $readonly, 'value' => $_lib['format']->Amount($AmountIn), 'class' => 'number', 'width' => '12', 'tabindex' => $tabindexin, 'accesskey' => 'I')); 
        $html .= '<br>' . $accountplan->debittext;
        $html .= "</td>\n";
        $html .= '<td class="' . $accountplan->CreditColor . '">';
        $html .= $_lib['form3']->text(array('name' => 'voucher.AmountOut', 'readonly' => $readonly, 'value' => $_lib['format']->Amount($AmountOut), 'class' => 'number', 'width' => '12', 'tabindex' => $tabindexout, 'accesskey' => 'I')); 
        $html .= '<br>' . $accountplan->credittext;
        $html .= "</td>\n";
    
        return $html;
    }
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */    
    #Buttons on line
    function update_journal_button_line($voucher, $VoucherPeriod, $JournalID, $VoucherType, $type) {
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
                    $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$voucher->VoucherPeriod&amp;voucher.VoucherDate=$voucher->VoucherDate&amp;voucher.JournalID=$JournalID&amp;voucher.VoucherID=$voucher->VoucherID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_delete=1&amp;view_mvalines=$view_mvalines&amp;view_linedetails=$view_linedetails", 'name'=>'<img src="/lib/icons/trash.gif">', 'confirm' => 'Vil du virkelig slette linjen?'));
                }
                if($voucher->DisableAutoVat !=1 )
                {
                    $html .= '<input type="submit" name="action_voucher_update" value="Lagre" class="green" tabindex="' . $tabindex++ . '" accesskey="S" >';
                }
                else
                {
                    //print "Kan ikke endres";
                }
            }
            elseif($voucher->VoucherType == 'A') {
                $html .= "Det er ikke lov &aring; endre auto bilag";
            } else {
                $html .= "Perioden er avsluttet";
            }
        }
        return $html;
    }
    
    function active_line($VoucherID1, $VoucherID2) {
        global $voucher_input;
        
        if($VoucherID1 == $VoucherID2) {
            $html = ">>";
        }
        return $html;
    }
    
    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */    
    #Buttons on head
    function update_journal_button_head($voucherHead, $VoucherPeriod, $VoucherType, $JournalID, $new, $rowCount) {
        global $_lib, $tabindex, $accounting, $MY_SELF;
    
        $html = '';

        
        if($_lib['sess']->get_person('AccessLevel') >= 2) {

            if($new)
            {
                $html .= '<input type="submit" name="action_voucher_new" value="Lagre" class="green" tabindex="';
                if($rowCount>1) { 
                    $html .= ''; 
                } else { 
                    $html .= $tabindex++; 
                }
                $html .= ' class="button">';
            }
            elseif(($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) && $voucherHead->VoucherType != 'A') || $VoucherPeriod == '0000-00')
            {
                $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$VoucherPeriod&amp;voucher.VoucherDate=$voucherHead->VoucherDate&amp;voucher.JournalID=$JournalID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_head_delete=1", 'name'=>'<img src="/lib/icons/trash.gif">', 'confirm' => 'Vil du virkelig slette bilaget?'));
                $html .= '<input type="submit" name="action_voucher_head_update" value="Lagre" class="green" tabindex="';
                if($rowCount>1) {
                    $html .= ''; 
                } else { 
                    $html .= $tabindex++; 
                }
                $html .= '" class="button" accesskey="S" />';
            }
            elseif($voucherHead->VoucherType == 'A') {
                $html .= "Det er ikke lov &aring; endre auto bilag";
            } else {
                $html .= "Perioden er avsluttet";
            }
        }
    
        return $html;
    }
}
?>
