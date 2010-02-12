<?

##################################
#
# Funksjoner som brukes i bilagsregistreringsskjermbildet journal/edit
#
##################################

class framework_logic_voucher
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
                
                $html .= '<nobr>' . $_lib['form3']->text(array('name' => 'voucher.Vat', 'value' => $VatPercent, 'class' => 'voucher', 'width' => '4', 'tabindex' => $tabindex++, 'accesskey' => 'M')) . '%</nobr>';
                $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatID', 'value' => $VatID));
            }
            else
            {
                if(isset($VatPercent))
                {
                    #Not secure with hidden VAT code if security is very important
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.Vat',      'value' => $VatPercent));
                    $html .= $_lib['form3']->hidden(array('name' => 'voucher.VatID',    'value' => $VatID));
    
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
    * Beregn postering
    * @param
    * @return
    */
    #Print accout menu on line
    function account($VoucherPeriod, $new, $db_table, $voucher, $AccountPlanID, $autosubmit) {
        global $_lib, $accounting, $tabindex;
        $html = '';
        if($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) || isset($new)) {
            $aconf = array();
            $aconf['table']         = $db_table;
            $aconf['field']         = 'AccountPlanID';
            $aconf['value']         = $AccountPlanID;
            $aconf['tabindex']      = $tabindex++;
            $aconf['accesskey']     = 'K';
            $aconf['autosubmit']    = $autosubmit;
            $aconf['type'][]        = 'employee';
            $aconf['type'][]        = 'balance';
            $aconf['type'][]        = 'result';
            $aconf['type'][]        = 'customer';
            $aconf['type'][]        = 'supplier';

            $html .=  $_lib['form3']->accountplan_number_menu($aconf);
            $html .=  $_lib['form3']->hidden(array('name'=>'OldAccountPlanID', 'value'=>$AccountPlanID));
    
        } else {
            $acctmp = $accounting->get_accountplan_object($AccountPlanID);
            $html  .= $acctmp->AccountName;
        }
        return $html;
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
        $html .= '</td>';
        $html .= '<td class="' . $accountplan->CreditColor . '">';
        $html .= $_lib['form3']->text(array('name' => 'voucher.AmountOut', 'readonly' => $readonly, 'value' => $_lib['format']->Amount($AmountOut), 'class' => 'number', 'width' => '12', 'tabindex' => $tabindexout, 'accesskey' => 'I')); 
        $html .= '<br>' . $accountplan->credittext;
        $html .= '</td>';
    
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
        if($_lib['sess']->get_person('AccessLevel') >= 2)
        {
            if($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')))
            {
                if($voucher->DisableAutoVat!=1)
                {
                    $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$voucher->VoucherPeriod&amp;voucher.VoucherDate=$voucher->VoucherDate&amp;voucher.JournalID=$JournalID&amp;voucher.VoucherID=$voucher->VoucherID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_delete=1", 'name'=>'S'));
                }
                if($voucher->DisableAutoVat!=1)
                {
                    $html .= '<input type="submit" name="action_voucher_update" value="Lagre" class="green" tabindex="' . $tabindex++ . '" accesskey="S" >';
                }
                else
                {
                    //print "Kan ikke endres";
                }
            }
            else
            {
                $html .= "Perioden er avsluttet";
            }
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
        $html .= $_lib['form3']->button(array('url' => "$MY_SELF&amp;voucher.VoucherPeriod=$VoucherPeriod&amp;voucher.VoucherDate=$voucherHead->VoucherDate&amp;voucher.JournalID=$JournalID&amp;VoucherType=$VoucherType&amp;type=$type&amp;action_voucher_head_delete=1", 'name'=>'S'));

        if($_lib['sess']->get_person('AccessLevel') >= 2) {
            if(isset($new))
            {
                $html .= '<input type="submit" name="action_voucher_new" value="Lagre" class="green" tabindex="';
                if($rowCount>1) { 
                    $html .= ''; 
                } else { 
                    $html .= $tabindex++; 
                }
                $html .= ' class="button">';
            }
            elseif($accounting->is_valid_accountperiod($VoucherPeriod, $_lib['sess']->get_person('AccessLevel')) or $VoucherPeriod == '0000-00')
            {

                $html .= '<input type="submit" name="action_voucher_head_update" value="Lagre" class="green" tabindex="';
                if($rowCount>1) {
                    $html .= ''; 
                } else { 
                    $html .= $tabindex++; 
                }
                $html .= '" class="button" accesskey="S" />';
            }
            else
            {
                $html .= 'Perioden er avsluttet';
            }
        }
    
        return $html;
    }
}
?>
