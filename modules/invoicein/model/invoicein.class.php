<?
includelogic('accounting/accounting');
includelogic('exchange/exchange');

class logic_invoicein_invoicein implements Iterator {

    private $iteratorH   = array() ;
    private $table_head  = 'invoicein';
    private $table_line  = 'invoiceinline';
    public  $allowance_charge_table      = 'invoiceallowancecharge';
    public  $line_allowance_charge_table = 'invoicelineallowancecharge';
    private $VoucherType = 'U';

    public function __construct($args) {
        global $_lib;

        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }

		#FromDate
        if (!$this->FromDate && array_key_exists('invin', $_COOKIE) && array_key_exists('fd', $_COOKIE['invin'])) {
			$this->FromDate =  $_COOKIE['invin']['fd'];
		} elseif(!$this->FromDate) {
            $this->FromDate = $_lib['sess']->get_session('DateStartYear');
		}
		setcookie("invin[fd]", $this->FromDate, time()+3600);

		#ToDate
        if (!$this->ToDate && array_key_exists('invin', $_COOKIE) && array_key_exists('td', $_COOKIE['invin'])) {
			$this->ToDate =  $_COOKIE['invin']['td'];
		} elseif(!$this->ToDate) {
            $this->ToDate = $_lib['sess']->get_session('DateEndYear');
		}
		setcookie("invin[td]", $this->ToDate, time()+3600);

		#RemittanceStatus
        if ($this->show_search) {
			setcookie("invin[rs]", $this->RemittanceStatus, time()+3600);
		} elseif (!$this->show_search && array_key_exists('invin', $_COOKIE) && array_key_exists('rs', $_COOKIE['invin'])) {
			$this->RemittanceStatus = $_COOKIE['invin']['rs'];
		}
		setcookie("invin[rs]", $this->RemittanceStatus, time()+3600);

		#InvoiceNumber
        if ($this->show_search) {
			setcookie("invin[iid]", $this->InvoiceNumber, time()+3600);
		} elseif (!$this->show_search && array_key_exists('invin', $_COOKIE) && array_key_exists('iid', $_COOKIE['invin'])) {
			$this->InvoiceNumber = $_COOKIE['invin']['iid'];
		}
		setcookie("invin[iid]", $this->InvoiceNumber, time()+3600);

		#Journaled
        if ($this->show_search && !$this->Journaled) {
			setcookie("invin[j]", '', time()+3600);
		} elseif ($this->show_search && $this->Journaled) {
			$this->Journaled =  1;
			setcookie("invin[j]", 1, time()+3600);
		} elseif (array_key_exists('invin', $_COOKIE) && array_key_exists('j', $_COOKIE['invin'])) {
			$this->Journaled =  $_COOKIE['invin']['j'];
			setcookie("invin[j]", 1, time()+3600);
		}

		#PaymentMeans
        if ($this->show_search) {
			setcookie("invin[pm]", $this->PaymentMeans, time()+3600);
		} elseif (!$this->show_search && array_key_exists('invin', $_COOKIE) && array_key_exists('pm', $_COOKIE['invin'])) {
			$this->PaymentMeans = $_COOKIE['invin']['pm'];
		}
		setcookie("invin[pm]", $this->PaymentMeans, time()+3600);

        $this->accounting  = new accounting();
    }

    #List all incoming invoices according to status
    function fill($args) {
        global $_lib;

        $query = "select i.* from invoicein as i where ";

        if($this->RemittanceStatus) {
            $query .= " i.RemittanceStatus='$this->RemittanceStatus' and ";
        }
        if($this->InvoiceNumber) {
            $query .= " i.InvoiceNumber like '%$this->InvoiceNumber%' and ";
        }
        if($this->FromDate) {
            $query .= " i.InvoiceDate >= '$this->FromDate' and ";
        }
        if($this->ToDate) {
            $query .= " i.InvoiceDate <= '$this->ToDate' and ";
        }
        if($this->PaymentMeans) {
            $query .= " i.PaymentMeans = '$this->PaymentMeans' and ";
        }
        if($this->Journaled) {
            $query .= " i.JournalID > 0 and ";
        } else {
            $query .= " (i.JournalID = 0 or i.JournalID is NULL) and ";
        }

        $query  = substr($query, 0, -4);
        $query .= " order by i.InvoiceDate asc";

        $result     = $_lib['db']->db_query($query);
        list($NextAvailableJournalID) = $this->accounting->get_next_available_journalid(array('available' => true, 'update' => false, 'type' => $this->VoucherType, 'reuse' => false, 'from' => 'Invoicein'));

        while($row  = $_lib['db']->db_fetch_object($result)) {

            $row->Journal   = true;
            $row->Journaled = false;

            # More fail checking that sum/amount is correct and that the period is open

            if($row->JournalID) {
                $row->Journal = false;
                $row->Class   = 'red';
                $row->Journaled = true;
                # Is already bookkept
                $row->Status .= "Er allerede bilagsf&oslash;rt";

            } else {
                $row->JournalID = $NextAvailableJournalID++;
                $row->Journal = true;
                $row->Class   = 'green';
            }

            $query                  = "select * from accountplan where AccountPlanID='" . $row->SupplierAccountPlanID . "' and AccountPlanType='supplier' and Active=1";
            #print "$query<br>\n";
            $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));

            #Counterpart accountplan result.
            if($account) {

                if($account->EnableMotkontoResultat && $account->MotkontoResultat1) {
                    $row->MotkontoAccountPlanID   = $account->MotkontoResultat1;
                } elseif($account->EnableMotkontoBalanse && $account->MotkontoBalanse1) {
                    $row->MotkontoAccountPlanID   = $account->MotkontoBalanse1;
                }

                // Bookkeeping account data
                $query = "select * from accountplan where AccountPlanID='" . $row->MotkontoAccountPlanID . "' and Active=1";

                $acc = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));

                $row->MotkontoAccountName = $acc->AccountName;


                $query_line  = "select * from invoiceinline where ID='" . $row->ID . "' and Active=1";
                $_lib['sess']->debug("linje: $query");
                $result_line = $_lib['db']->db_query($query_line);
                while($line  = $_lib['db']->db_fetch_object($result_line)) {

                    if(!$line->AccountPlanID) {
                        # One or more invoice lines are missing result accountplan
                        $row->Status .= "En eller flere fakturalinjer mangler resultatkonto";
                        $row->Journal = false;
                        $row->Class   = 'red';
                        break;
                    }
                }

            } else {
                # Could not find accountplan __
                $row->Status .= "Finner ikke kontoplan: " . $row->SupplierAccountPlanID;
                $row->Journal = false;
                $row->Class   = 'red';
            }

            $row->VoucherType  = $this->VoucherType;
            $this->iteratorH[] = $row;
        }
    }

    // Since it is then simpler to load for XML, and we only show it inverted in view/edit page
    function invertAllAllowanceAmounts(&$args) {
        global $_lib;

        foreach ($args as $arg_key => $arg_val) {
          if ((strpos($arg_key, 'invoiceallowancecharge_Amount_') !== false) || (strpos($arg_key, 'invoicelineallowancecharge_Amount_') !== false)) {
            $charge_indicator = $args[preg_replace('/Amount/', 'ChargeIndicator', $arg_key)];
            $amount = $_lib['convert']->Amount($args[$arg_key]);
            if ($charge_indicator == 0) $args[$arg_key] = -$amount;
          }
        }
    }

    function addVATPercentToAllowanceCharge(&$args) {
        if (!is_numeric($args['ID'])) {
            return;
        }

        global $_lib;

        $InvoiceID = $args['ID'];
        $InvoiceDate = $args['invoicein_InvoiceDate_'.$InvoiceID];
        foreach ($args as $arg_key => $arg_val) {
          if (strpos($arg_key, 'invoiceallowancecharge_VatID_') !== false) {
            $select_vat = "select * from vat where VatID = '" . $arg_val . "' and Type = 'buy' and ValidFrom <= '" . $InvoiceDate . "' and ValidTo >= '" . $InvoiceDate . "'";
            $vat = $_lib['db']->get_row(array('query' => $select_vat));
            $args[preg_replace('/VatID/', 'VatPercent', $arg_key)] = $vat->Percent;
          }
        }
    }

    function update($args) {
        global $_lib, $_SETUP, $accounting;

        $ID             = $args['ID'];
        $AccountPlanID  = $args['invoicein_SupplierAccountPlanID_' . $ID];
        $accountplan    = $accounting->get_accountplan_object($AccountPlanID);
        $invoicein      = $_lib['storage']->get_row(array('query' => "select * from invoicein where ID='$ID'"));

        self::addVATPercentToAllowanceCharge($args);
        self::invertAllAllowanceAmounts($args);
        if(!$invoicein->IZipCode) {
            if($accountplan->ZipCode) {
                $args['invoicein_IZipCode_' . $ID] = $accountplan->ZipCode;
                # ZipCode copied from supplier accountplan
                $_lib['message']->add('Postnummer kopiert fra leverand&oslash;r kontoplan');
            } else {
                # ZipCode missing on supplier accountplan
                $_lib['message']->add('Postnummer mangler p&aring; leverand&oslash;r kontoplan');
            }
        }
        if(!$invoicein->ICity) {
            if($accountplan->City) {
                $args['invoicein_ICity_' . $ID] = $accountplan->City;
                # City copied from supplier accountplan
                $_lib['message']->add('Sted kopiert fra leverand&oslash;r kontoplan');
            } else {
                # City missing on supplier accountplan
                $_lib['message']->add('Sted mangler p&aring; leverand&oslash;r kontoplan');
            }
        }
        if(!$invoicein->SupplierBankAccount) {
            if($accountplan->DomesticBankAccount) {
                $args['invoicein_SupplierBankAccount_' . $ID] = $accountplan->DomesticBankAccount;
                # Account number copied from supplier accountplan
                $_lib['message']->add('Kontonummer kopiert fra leverand&oslash;r kontoplan');
            } else {
                # Account number missing on supplier accountplan
                $_lib['message']->add('Kontonummer mangler p&aring; leverand&oslash;r kontoplan');
            }
        }

        #print_r($accountplan);
        #print_r($args);

        $tables_to_update = array(
          'invoicein'                        => 'ID',
          'invoiceinline'                    => 'LineID',
          $this->allowance_charge_table      => 'InvoiceAllowanceChargeID',
          $this->line_allowance_charge_table => 'InvoiceLineAllowanceChargeID');
        $_lib['db']->db_update_multi_table($args, $tables_to_update);

    }

    function linenew($args) {
        global $_lib;
        $invoicelineH['invoiceinline_Active']           = 1;
        $invoicelineH['invoiceinline_ID']               = $this->ID;
        $invoicelineH['invoiceinline_AccountPlanID']    = $args['AccountPlanID'];
        $invoicelineH['invoiceinline_QuantityOrdered']  = 1;
        $invoicelineH['invoiceinline_QuantityDelivered']= 1;
        $invoicelineH['invoiceinline_Vat']              = 25;

        return $_lib['db']->db_new_hash($invoicelineH, $this->table_line);
    }

    /*******************************************************************************
    * Add a new invoice allowance/charge
    * @param
    * @return
    */
    function allowance_charge_new($args)
    {
        global $_lib;
        $allowance_chargeH['invoiceallowancecharge_InvoiceType'] = 'in';
        $allowance_chargeH['invoiceallowancecharge_InvoiceID']   = $this->ID;
        return $_lib['db']->db_new_hash($allowance_chargeH, $this->allowance_charge_table);
    }

    /*******************************************************************************
    * Add a new invoice line allowance/charge
    * @param
    * @return
    */
    function line_allowance_charge_new($args)
    {
        global $_lib;
        $allowance_chargeH['invoicelineallowancecharge_InvoiceType']         = 'in';
        $allowance_chargeH['invoicelineallowancecharge_InvoiceLineID']       = $args['LineID'];
        $allowance_chargeH['invoicelineallowancecharge_AllowanceChargeType'] = 'line';
        return $_lib['db']->db_new_hash($allowance_chargeH, $this->line_allowance_charge_table);
    }

    /*******************************************************************************
    * Delete invoice line allowance/charge
    * @param
    * @return
    */
    function line_allowance_charge_delete($args)
    {
        global $_lib;
        $query = "delete from $this->line_allowance_charge_table where InvoiceLineAllowanceChargeID = " . $args['InvoiceLineAllowanceChargeID'];
        return $_lib['db']->db_delete($query);
    }

    /*******************************************************************************
    * Delete invoice allowance/charge
    * @param
    * @return
    */
    function allowance_charge_delete($args)
    {
        global $_lib;
        $query = "delete from $this->allowance_charge_table where InvoiceAllowanceChargeID = " . $args['InvoiceAllowanceChargeID'];
        return $_lib['db']->db_delete($query);
    }

    function linedelete($args) {
        global $_lib;
        // dependant destroy for allowances/charges connected to this line
        $query="delete from $this->line_allowance_charge_table where InvoiceType = 'in' and InvoiceLineID=" . $args['LineID'];
        $_lib['db']->db_delete($query);
        $invoicelineH['Active']   = 0;
        $invoicelineH['LineID']   = $args['LineID'];
        return $_lib['storage']->store_record(array('table' => 'invoiceinline', 'data' => $invoicelineH, 'debug' => false));
    }

    /***************************************************************************
     * Create new incoming invoice
     * @param None
     * @return Current iteration
     */
    public function add() {
        $dataH['CustomerBankAccount']       = $_lib['sess']->get_companydef('BankAccount');
        $old_pattern                        = array("/[^0-9]/");
        $new_pattern                        = array("");
        $dataH['CustomerAccountPlanID']     = strtolower(preg_replace($old_pattern, $new_pattern , $_lib['sess']->get_companydef('OrgNumber')));
    }

    ################################################################################################
    #Journal the invoices automatically.
    #Set the invoices as registered in fakturabank
    #update the bankaccount in accountplan.
    public function journal() {
        global $_lib, $accounting;

        if(is_array($this->iteratorH)) {
            $this->Journaled = 1; //#So that we immideately list the journaled vouchers

            foreach($this->iteratorH as $InvoiceO) {

                if($InvoiceO->Journal) {

                    $countjournaled++;

                    //#TODO: Check Payment means for codes 10, 42, 48 - and change the journaling accorging to this.
                    $VoucherH = array();
                    $VoucherH['voucher_ExternalID']         = $InvoiceO->ExternalID;

                    if ($accounting->is_valid_accountperiod($InvoiceO->Period, $_lib['sess']->get_person('AccessLevel'))) {
                        $VoucherH['voucher_VoucherPeriod']      = $InvoiceO->Period;
                    }
                    else {
                        $first_open_period = $accounting->get_first_open_accountingperiod();
                        if ($first_open_period > $InvoiceO->Period) { // since periods are 0 preceded we can use normal string comparison
                            // first open period is later then Invoice0->Period, so use it
                            $VoucherH['voucher_VoucherPeriod'] = $first_open_period;
                        } else { // use last open period instead
                            $last_open_period = $accounting->get_last_open_accountingperiod();
                            $VoucherH['voucher_VoucherPeriod'] = $last_open_period;
                        }
                    }

                    $VoucherH['voucher_VoucherDate']        = $InvoiceO->InvoiceDate;
                    $VoucherH['voucher_DueDate']            = $InvoiceO->DueDate;
                    $VoucherH['voucher_EnableAutoBalance']  = 0;
                    $VoucherH['voucher_AddedByAutoBalance'] = 0;
                    $VoucherH['voucher_VoucherType']        = $InvoiceO->VoucherType;
                    $VoucherH['voucher_AutoKID']            = 0; //#Information updated automatically from KID information

                    //$InvoiceO->DocumentCurrencyCode = 'EUR'; //DELETE

                    //#Foreign currency
                    $TotCustPrice = $InvoiceO->TotalCustPrice;
                    if ($InvoiceO->DocumentCurrencyCode != exchange::getLocalCurrency()) {
                        $TotCustPrice = exchange::convertToLocal($InvoiceO->DocumentCurrencyCode, $InvoiceO->TotalCustPrice);
                        $VoucherH['voucher_ForeignCurrencyID']  = $InvoiceO->ForeignCurrencyID; //$InvoiceO->DocumentCurrencyCode;
                        $VoucherH['voucher_ForeignAmount']      = (float)abs($InvoiceO->ForeignAmount); //abs($InvoiceO->TotalCustPrice);
                        $VoucherH['voucher_ForeignConvRate']    = (float)$InvoiceO->ForeignConvRate; //exchange::getConversionRate($InvoiceO->DocumentCurrencyCode);
                    }

                    if($InvoiceO->TotalCustPrice < 0)
                        $VoucherH['voucher_AmountIn']           = abs($InvoiceO->TotalCustPrice);
                    else
                        $VoucherH['voucher_AmountOut']          = abs($InvoiceO->TotalCustPrice);

                    $VoucherH['voucher_Active']             = 1;
                    $VoucherH['voucher_Description']        = "";
                    # From incoming invoice ID
                    $VoucherH['voucher_AutomaticReason']    = "Fra innk faktura ID: " . $InvoiceO->ID;

                    $VoucherH['voucher_KID']                = $InvoiceO->KID;
                    $VoucherH['voucher_InvoiceID']          = $InvoiceO->InvoiceNumber;
                    $VoucherH['voucher_AccountPlanID']      = $InvoiceO->SupplierAccountPlanID;

                    //#We can not guarantee that the reserved JournalIDs is held, so we have to check before really registering the voucher
                    list($InvoiceO->JournalID) = $this->accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $InvoiceO->VoucherType, 'reuse' => false, 'from' => 'Invoicein voucher'));

                    $VoucherH['voucher_JournalID']          = $InvoiceO->JournalID;
                    // if amount is 0 then the journal is not created and should be null
                    if ($InvoiceO->TotalCustPrice == 0) unset($VoucherH['voucher_JournalID']);
                    //#$VoucherH['voucher_JournalID']          = $InvoiceO->JournalID;

                    //#Update the voucherID back to the incoming invoice

                    //#print_r($VoucherH);
                    $this->accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $InvoiceO->VoucherType, 'comment' => 'Fra invoicein'));

                    $invoice_line_sum = 0;

                    // Allowances/Charges on invoice
                    $query_invoice_allowance_charge = "select iac.*, ac.DepartmentID, ac.ProjectID from invoiceallowancecharge iac left join allowancecharge ac on iac.AllowanceChargeID=ac.AllowanceChargeID where InvoiceID = '$InvoiceO->ID' and InvoiceType = 'in'";
                    $result_invoice_allowance_charge = $_lib['db']->db_query($query_invoice_allowance_charge);

                    while ($acrow = $_lib['db']->db_fetch_object($result_invoice_allowance_charge)) {
                        $query = "select a.MotkontoResultat1 as InAccountPlanID from accountplan a where a.AccountPlanID = " . $InvoiceO->SupplierAccountPlanID;
                        $invoiceallowancecharge = $_lib['storage']->get_row(array('query' => $query));

                        $VoucherH['voucher_AccountPlanID']  = $invoiceallowancecharge->InAccountPlanID;
                        $VoucherH['voucher_AmountIn']       = 0;
                        $VoucherH['voucher_AmountOut']      = 0;
                        $VoucherH['voucher_Vat']            = $acrow->VatPercent;
                        $VoucherH['voucher_VatID']          = $acrow->VatID;
                        $VoucherH['voucher_Description']    = $acrow->AllowanceChargeReason;
                        $VoucherH['voucher_DepartmentID']   = $acrow->DepartmentID;
                        $VoucherH['voucher_ProjectID']      = $acrow->ProjectID;

                        $TotalPrice = $acrow->Amount * ((100 + $acrow->VatPercent) / 100);
                        $TotalPrice = $TotalPrice * (($acrow->ChargeIndicator == 1) ? 1 : -1);

                        $invoice_line_sum += $TotalPrice;

                        if($TotalPrice > 0) {
                            $VoucherH['voucher_AmountIn']   = abs($TotalPrice);
                            $VoucherH['voucher_AmountOut']  = 0;
                        }
                        else {
                            $VoucherH['voucher_AmountOut']  = abs($TotalPrice);
                            $VoucherH['voucher_AmountIn']   = 0;
                        }

                        $this->accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $InvoiceO->VoucherType, 'comment' => 'Fra fakturabank'));
                    }

                    //####################################################################################
                    //#Each line has a different Vat - counterpart accountplan is from supplier
                    $query_invoiceline      = "select il.* from invoiceinline as il where il.ID='$InvoiceO->ID' and il.Active <> 0 order by il.LineID asc";
                    //#print "query_invoiceline" . $query_invoiceline . "<br>\n";
                    $result2                = $_lib['db']->db_query($query_invoiceline);

                    $lines = array();

                    while ($line = $_lib['db']->db_fetch_object($result2)) {
                        $lines[] = $line;
                    }

                    $num_lines = count($lines);

                    for ($i = 0; $i < $num_lines; $i++) {
                        $line = $lines[$i];

                        $last_line = ($i == $num_lines - 1) ? true : false;

                        $VoucherH['voucher_AmountIn']       = 0;
                        $VoucherH['voucher_AmountOut']      = 0;
                        $VoucherH['voucher_Vat']            = '';
                        $VoucherH['voucher_Description']    = '';
                        $VoucherH['voucher_AccountPlanID']  = 0;

                        //#Motkonto resultat.
                        $VoucherH['voucher_AccountPlanID']  = $line->AccountPlanID;
                        if($line->IsOnlyTax) {
                            $VoucherH['voucher_AccountPlanID'] = 2710;
                        }
                        $line_accountplan = accounting::get_accountplan_object($VoucherH['voucher_AccountPlanID']);

                        $query = "select sum(if(ChargeIndicator = 1, Amount, -Amount)) as sum from invoicelineallowancecharge where InvoiceType = 'in' and AllowanceChargeType = 'line' and InvoiceLineID = " . $line->LineID;
                        $result = $_lib['storage']->get_row(array('query' => $query));
                        $sum_line_allowance_charge = $result->sum;

                        $TotalPrice = round(($line->QuantityDelivered * $line->UnitCustPrice + $sum_line_allowance_charge), 2);
                        $TotalForeignPrice = round($line->ForeignAmount + $sum_line_allowance_charge, 2);

                        if($line->Vat > 0) {
                            //Add VAT to the price - since it is ex VAT

                            // $TotalPrice = round(($TotalPrice * (($line->Vat/100) +1)), 2);
                            // $TotalForeignPrice = round(($TotalForeignPrice * (($line->Vat/100) +1)), 2);
                            // We already have tax amount given to us by fakturabank.
                            // So why recalculate, and possibly make 0.01 error in recalculation?
                            // Just add them up and get the correct amount.
                            if ($VoucherH['voucher_ForeignCurrencyID'] != '') {
                              $TaxAmount = (!empty($line->TaxAmount)) ? $line->TaxAmount : $TotalForeignPrice * $line->Vat / 100.0;
                              // Using $TotalPrice since we already have that amount converted to local currency on download of this invoice
                              $TotalPrice = round(($TotalPrice + exchange::convertToLocal($VoucherH['voucher_ForeignCurrencyID'], $TaxAmount)), 2);
                            }
                            else {
                              $TaxAmount = (!empty($line->TaxAmount)) ? $line->TaxAmount : $TotalPrice * $line->Vat / 100.0;
                              $TotalPrice = round(($TotalPrice + $TaxAmount), 2);
                            }
                            $invoice_line_sum += $TotalPrice;
                        } else {
                            $invoice_line_sum += $TotalPrice;
                        }

                        if ($last_line) {
                            if ($invoice_line_sum != $InvoiceO->TotalCustPrice) {
                                if ($invoice_line_sum < $InvoiceO->TotalCustPrice) {
                                    $TotalPrice += ($InvoiceO->TotalCustPrice - $invoice_line_sum);
                                } else {
                                    $TotalPrice -= ($invoice_line_sum - $InvoiceO->TotalCustPrice);
                                }
                            }
                        }

                        if($TotalPrice > 0) {
                            $VoucherH['voucher_AmountIn']   = abs($TotalPrice);
                            $VoucherH['voucher_AmountOut']  = 0;
                        }
                        else {
                            $VoucherH['voucher_AmountOut']  = abs($TotalPrice);
                            $VoucherH['voucher_AmountIn']   = 0;
                        }

                        if ($VoucherH['voucher_ForeignCurrencyID'] != '') $VoucherH['voucher_ForeignAmount']   = abs($TotalForeignPrice);

                        $VoucherH['voucher_Vat']            = $line->Vat;
                        //#$VoucherH['voucher_VatID']         = $line->VatID; Has to be mapped properly
                        if($line->QuantityDelivered > 0) {
                            $VoucherH['voucher_Description']    .= round($line->QuantityDelivered,2) . 'x';
                        }

                        if($line->ProductNumber) {
                            $VoucherH['voucher_Description']    .= $line->ProductNumber . ':';
                        }

                        if($line->ProductName) {
                            $VoucherH['voucher_Description']    .= $line->ProductName;
                        }

                        // If value(car/department/project) sent use that, otherwise use defaults from this line's accountplan (if set and enabled)
                        if($line->CarID) {
                            $VoucherH['voucher_CarID'] = $line->CarID;
                        } elseif (!empty($line_accountplan) && $line_accountplan->EnableCar == 1 && isset($line_accountplan->CarID)) {
                            $VoucherH['voucher_CarID'] = $line_accountplan->CarID;
                        }
                        else {
                            unset($VoucherH['voucher_CarID']);
                        }
                        if($line->DepartmentID) {
                            $VoucherH['voucher_DepartmentID'] = $line->DepartmentID;
                        } elseif (!empty($line_accountplan) && $line_accountplan->EnableDepartment == 1 && isset($line_accountplan->DepartmentID)) {
                            $VoucherH['voucher_DepartmentID'] = $line_accountplan->DepartmentID;
                        }
                        else {
                            unset($VoucherH['voucher_DepartmentID']);
                        }
                        if($line->ProjectID) {
                            $VoucherH['voucher_ProjectID'] = $line->ProjectID;
                        } elseif (!empty($line_accountplan) && $line_accountplan->EnableProject == 1 && isset($line_accountplan->ProjectID)) {
                            $VoucherH['voucher_ProjectID'] = $line_accountplan->ProjectID;
                        }
                        else {
                            unset($VoucherH['voucher_ProjectID']);
                        }

                        $this->accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $InvoiceO->VoucherType, 'comment' => 'Fra fakturabank'));
                    }

                    /* here a fetch from the fakturaBank table is needed that fixes the reason lines, for example: cahs from cash register(kontant fra kasse) and similar */


                    // Creating vouchers for reconsiliation reasons
                    $fb_query = sprintf("SELECT * FROM fbdownloadedinvoicereasons WHERE LodoID = %d", $InvoiceO->ID);

                    $fb_rows = $_lib['db']->db_query($fb_query);
                    $original_accountplanid = $InvoiceO->SupplierAccountPlanID;

                    while($fb_row = $_lib['db']->db_fetch_object($fb_rows)) {
                        $reasonID = $fb_row->ClosingReasonId;
                        $reconciliation_amount = $fb_row->Amount;

                        $VoucherH['voucher_AmountIn']       = 0;
                        $VoucherH['voucher_AmountOut']      = 0;
                        $VoucherH['voucher_Vat']            = '';
                        $VoucherH['voucher_Description']    = '';
                        $VoucherH['voucher_AccountPlanID']  = 0;

                        if($reasonID) {
                            $VoucherH['voucher_Description'] = sprintf(
                                // Reconciliation from reason __
                                'Avstemt med årsak %d',
                                $reasonID
                                );

                            $reasonQuery = sprintf(
                                "SELECT r.*
                               FROM fakturabankinvoicereconciliationreason r,
                                    accountplan a
                               WHERE r.FakturabankInvoiceReconciliationReasonID = %d
                                 AND r.AccountPlanID = a.AccountPlanID",
                                $reasonID
                                );

                            $reason_row = $_lib['storage']->get_row(array('query' => $reasonQuery, 'debug' => true));
                            if(!$reason_row) {
                                # Something's wrong with reconciliation reason _
                                $_lib['message']->add(sprintf("Noe galt med reconciliationreason %d", $reasonID));
                            }
                            else {
                                $VoucherH['voucher_AccountPlanID'] = $reason_row->AccountPlanID;

                                if($reconciliation_amount > 0) {
                                    $VoucherH['voucher_AmountIn']   = abs($reconciliation_amount);
                                    $VoucherH['voucher_AmountOut']  = 0;
                                }
                                else {
                                    $VoucherH['voucher_AmountOut']  = abs($reconciliation_amount);
                                    $VoucherH['voucher_AmountIn']   = 0;
                                }

                                # From fakturabank - reconciliation
                                $this->accounting->insert_voucher_line(
                                    array(
                                        'post' => $VoucherH,
                                        'accountplanid' => $VoucherH['voucher_AccountPlanID'],
                                        'VoucherType'=> $InvoiceO->VoucherType,
                                        'comment' => 'Fra fakturabank - Reconciliation'
                                        )
                                    );

                                /* motpost */
                                $VoucherH['voucher_AccountPlanID'] = $original_accountplanid;
                                $tmp = $VoucherH['voucher_AmountIn'];
                                $VoucherH['voucher_AmountIn'] = $VoucherH['voucher_AmountOut'];
                                $VoucherH['voucher_AmountOut'] = $tmp;

                                # From fakturabank - reconciliation
                                $this->accounting->insert_voucher_line(
                                    array(
                                        'post' => $VoucherH,
                                        'accountplanid' => $VoucherH['voucher_AccountPlanID'],
                                        'VoucherType'=> $InvoiceO->VoucherType,
                                        'comment' => 'Fra fakturabank - Reconciliation'
                                        )
                                    );
                            }
                        }
                    }


                    //# If VatID missing (which it always will be here), and we have accountplanid, and
                    //# InvoiceO->InvoiceDate != "", then
                    //# get VatID from account plan. This is suboptimal since we do not know if VatID
                    //# matches Vat percentage from last invoiceinline, but that will have to be the
                    //# simplification to live with for now, because of time constraints.
                    if (!isset($VoucherH['voucher_VatID']) || $VoucherH['voucher_VatID'] === "" || is_null($VoucherH['Voucher_VatID'])) {

                        if (isset($VoucherH['voucher_AccountPlanID']) && is_numeric($VoucherH['voucher_AccountPlanID']) &&
                            $InvoiceO->InvoiceDate != "") {

                            $account_vatid_query = "SELECT VatID from accountplan WHERE AccountPlanID = '" . $VoucherH['voucher_AccountPlanID'] . "'";

                            if ($result = $_lib['storage']->db_query3(array('query' => $account_vatid_query))) {

                                $account_vatid_obj = $_lib['storage']->db_fetch_object($result);
                                if (!empty($account_vatid_obj)) {
                                    $VAT = $accounting->get_vataccount_object(array('VatID' => $account_vatid_obj->VatID, 'date' => $InvoiceO->InvoiceDate));
                                    $VoucherH['voucher_VatID'] = $VAT->VatID;
                                }

                            }

                        }

                    }

                    $this->accounting->set_journal_motkonto(array('post' => $VoucherH, 'VoucherType' => $VoucherH['voucher_VoucherType']));
                    $this->accounting->correct_journal_balance($VoucherH, $VoucherH['voucher_JournalID'], $VoucherH['voucher_VoucherType']);
                    $this->accounting->delete_credit_debit_zero_lines($VoucherH['voucher_JournalID'], $VoucherH['voucher_VoucherType']);

                    //####################################################################################
                    //#Update invoicein to journaled
                    $dataH = array();
                    $dataH['ID']        = $InvoiceO->ID;
                    $dataH['JournalID'] = $InvoiceO->JournalID;
                    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoicein', 'debug' => false));

                    //####################################################################################
                    //#Update bankaccount on accountplan (for later usage in direct remittance(direkte remittering) - could be punched to -
                    //#Could be erronus if the same accountplan has more than one bank account. We just ignore it for now.
                    if($InvoiceO->BankAccount) {
                        $dataH = array();
                        $dataH['DomesticBankAccount']   = $InvoiceO->BankAccount;
                        $dataH['AccountPlanID']         = $InvoiceO->SupplierAccountPlanID;
                        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'debug' => false));
                    }
                }
                else {
                    # Invoice is bookkept
                    print "Fakturaen er bilagsf¿rt<br>";
                }
            }

            # this weird logic to make theNewlyJournaled on top and in correct order
            $theNewlyJournaled = $this->iteratorH;
            $this->iteratorH = array();

            $this->fill(array()); //#Refresh the list after a new query - because lots of paramters get updated when journaling.
            $this->iteratorH = array_merge($this->iteratorH, $theNewlyJournaled);

            # __ invoices have been bookkept
            $_lib['message']->add("$countjournaled fakturaer er bilagsf&oslash;rt");
        }
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param None
    * @return Current iteration
    */
    function current() {
        return current($this->iteratorH);
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function next() {
       $this->valid = (FALSE !== next($this->iteratorH));
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function key() {
        return key($this->iteratorH);
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function valid() {
        return $this->valid;
    }

    /***************************************************************************
    * Iterator interface abstract
    * @param Define input parameters
    * @return Define return og function
    */
    function rewind() {
        $this->valid = (FALSE !== reset($this->iteratorH)); ;
    }

    /***************************************************************************
    * Change order on IteratorH
    */
    function changeOrder() {
        $this->iteratorH = array_reverse($this->iteratorH);
    }


    /***************************************************************************
    *
    * @return mixed array
    */
    function getAllReadyInvoices() {
		global $_lib;
		$query = "SELECT COUNT(*) AS cnt, EXTRACT(YEAR FROM i.InvoiceDate) AS Y, EXTRACT(MONTH FROM i.InvoiceDate) AS M
					FROM invoicein i
					WHERE i.TotalCustPrice != 0 and i.JournalID NOT IN (SELECT q.JournalID FROM voucher q WHERE i.JournalID = q.JournalID AND q.VoucherType = 'U' AND q.Active = 1)
					GROUP BY Y, M
					ORDER BY Y, M
					LIMIT 0,1000";

	    $result     = $_lib['db']->db_query($query);
		$lines = array();
        while($row  = $_lib['db']->db_fetch_object($result)) {
			$lines[] = array('Y'=>$row->Y, 'M'=>$row->M, 'cnt'=>$row->cnt);
		}
		return $lines;
    }


}
?>
