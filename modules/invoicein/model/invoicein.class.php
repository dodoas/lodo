<?
includelogic('accounting/accounting');

class logic_invoicein_invoicein implements Iterator {

    private $iteratorH   = array() ;
    private $table_head  = 'invoicein';
    private $table_line  = 'invoiceinline';
    private $VoucherType = 'U';
    
    public function __construct($args) {
        global $_lib;
                
        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }       

        if(!$this->FromDate) {
            $this->FromDate = $_lib['sess']->get_session('DateStartYear');
        }

        if(!$this->ToDate) {
            $this->ToDate   = $_lib['sess']->get_session('DateEndYear');
        }
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

	#Cleaning after prior developer. -eirhje 23.01.10
        #print "$query<br>\n";
        $result     = $_lib['db']->db_query($query);
        list($NextAvailableJournalID) = $this->accounting->get_next_available_journalid(array('available' => true, 'update' => false, 'type' => $this->VoucherType, 'reuse' => false, 'from' => 'Invoicein'));
        
        while($row  = $_lib['db']->db_fetch_object($result)) {

            $row->Journal   = true;
            $row->Journaled = false;

            # Mer feilsjekking
            # At sum/bel¿p stemmer
            # At perioden er Œpen

            if($row->JournalID) {
                $row->Journal = false;
                $row->Class   = 'red';
                $row->Journaled = true;
                $row->Status .= "Er allerede bilagsf&oslash;rt";

            } else {
                $row->JournalID = $NextAvailableJournalID++;
                $row->Journal = true;
                $row->Class   = 'green';
            }
        
            $query                  = "select * from accountplan where AccountPlanID='" . $row->SupplierAccountPlanID . "' and AccountPlanType='supplier' and Active=1";
            #print "$query<br>\n";
            $account                = $_lib['storage']->get_row(array('query' => $query, 'debug' => true));
        
            #Motkonto resultat.
            if($account) {

                $query_line  = "select * from invoiceinline where ID='" . $row->ID . "' and Active=1";
                $_lib['sess']->debug("linje: $query");
                $result_line = $_lib['db']->db_query($query_line);
                while($line  = $_lib['db']->db_fetch_object($result_line)) {

                    if(!$line->AccountPlanID) {
                        $row->Status .= "En eller flere fakturalinjer mangler resultatkonto";
                        $row->Journal = false;
                        $row->Class   = 'red';
                        break;
                    }
                }

            } else {
                $row->Status .= "Finner ikke kontoplan: " . $row->SupplierAccountPlanID;
                $row->Journal = false;
                $row->Class   = 'red';
            }
            
            $row->VoucherType  = $this->VoucherType;
            $this->iteratorH[] = $row;
        }
    }

    function update($args) {
        global $_lib, $_SETUP, $accounting;

        $ID             = $args['ID'];
        $AccountPlanID  = $args['invoicein_SupplierAccountPlanID_' . $ID];
        $accountplan    = $accounting->get_accountplan_object($AccountPlanID);
        $invoicein      = $_lib['storage']->get_row(array('query' => "select * from invoicein where ID='$ID'"));

        if(!$invoicein->IZipCode) {
            if($accountplan->ZipCode) {
                $args['invoicein_IZipCode_' . $ID] = $accountplan->ZipCode;
                $_lib['message']->add('Postnummer kopiert fra leverand&oslash;r kontoplan');
            } else {
                $_lib['message']->add('Postnummer mangler p&aring; leverand&oslash;r kontoplan');            
            }
        }
        if(!$invoicein->ICity) {
            if($accountplan->City) {
                $args['invoicein_ICity_' . $ID] = $accountplan->City;
                $_lib['message']->add('Sted kopiert fra leverand&oslash;r kontoplan');
            } else {
                $_lib['message']->add('Sted mangler p&aring; leverand&oslash;r kontoplan');            
            }
        }
        if(!$invoicein->SupplierBankAccount) {
            if($accountplan->DomesticBankAccount) {
                $args['invoicein_SupplierBankAccount_' . $ID] = $accountplan->DomesticBankAccount;
                $_lib['message']->add('Kontonummer kopiert fra leverand&oslash;r kontoplan');
            } else {
                $_lib['message']->add('Kontonummer mangler p&aring; leverand&oslash;r kontoplan');            
            }
        }

        #print_r($accountplan);
        #print_r($args);
                
        $_lib['db']->db_update_multi_table($args, array('invoicein' => 'ID', 'invoiceinline' => 'LineID'));

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

    function linedelete($args) {
        global $_lib;
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
    #Journal the invoices automatic.
    #Set the invoices as registered in fakturabank
    #update the bankaccount in accountplan.
    public function journal() {
        global $_lib, $accounting;

        if(is_array($this->iteratorH)) {
            $this->Journaled = 1; #So that we immideately list the journaled vouchers
    
            foreach($this->iteratorH as $InvoiceO) {
    
                if($InvoiceO->Journal) {
                
                    #print "\n\nNeste faktura\n";
                    #print_r($InvoiceO);
                    $countjournaled++;
        
                    #ToBe Done: Check Payment means for codes 10, 42, 48 - and change the journaling accorging to this.    
                    $VoucherH = array();
                    $VoucherH['voucher_ExternalID']         = $InvoiceO->ExternalID;
                    $VoucherH['voucher_VoucherPeriod']      = $InvoiceO->Period;
                    $VoucherH['voucher_VoucherDate']        = $InvoiceO->InvoiceDate;
                    $VoucherH['voucher_DueDate']            = $InvoiceO->DueDate;
                    $VoucherH['voucher_EnableAutoBalance']  = 0;
                    $VoucherH['voucher_AddedByAutoBalance'] = 0;
                    $VoucherH['voucher_VoucherType']        = $InvoiceO->VoucherType;
                    $VoucherH['voucher_AutoKID']            = 0; #Information updated automatically from KID information
        
                    ##
                    if($InvoiceO->TotalCustPrice < 0)
                        $VoucherH['voucher_AmountIn']           = abs($InvoiceO->TotalCustPrice);
                    else
                        $VoucherH['voucher_AmountOut']          = abs($InvoiceO->TotalCustPrice);
    
                    $VoucherH['voucher_Active']             = 1;
                    $VoucherH['voucher_Description']        = "";
                    $VoucherH['voucher_AutomaticReason']    = "Fra innk faktura ID: " . $InvoiceO->ID;
    
                    $VoucherH['voucher_KID']                = $InvoiceO->KID;
                    $VoucherH['voucher_DepartmentID']       = $InvoiceO->Department;
                    $VoucherH['voucher_ProjectID']          = $InvoiceO->Project;
                    $VoucherH['voucher_InvoiceID']          = $InvoiceO->InvoiceNumber;
                    $VoucherH['voucher_AccountPlanID']      = $InvoiceO->SupplierAccountPlanID;

                    #We can not guarantee that the reserved JournalIDs is held, so we have to check before really registering the voucher
                    list($InvoiceO->JournalID) = $this->accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $InvoiceO->VoucherType, 'reuse' => false, 'from' => 'Invoicein voucher'));
                  
                    $VoucherH['voucher_JournalID']          = $InvoiceO->JournalID;
                    #$VoucherH['voucher_JournalID']          = $InvoiceO->JournalID;

                    #Update the voucherID back to the incoming invoice

                    #print_r($VoucherH);
                    $this->accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $InvoiceO->VoucherType, 'comment' => 'Fra invoicein'));
            
                    ####################################################################################
                    #Each line has a different Vat - motkonto is from supplier
                    $query_invoiceline      = "select il.* from invoiceinline as il where il.ID='$InvoiceO->ID' and il.Active <> 0 order by il.LineID asc";
                    #print "query_invoiceline" . $query_invoiceline . "<br>\n";
                    $result2                = $_lib['db']->db_query($query_invoiceline);
    
                    while($line = $_lib['db']->db_fetch_object($result2)) {
                        #print_r($vatO);
                            
                        $VoucherH['voucher_AmountIn']       = 0;
                        $VoucherH['voucher_AmountOut']      = 0;
                        $VoucherH['voucher_Vat']            = '';
                        $VoucherH['voucher_Description']    = '';
                        $VoucherH['voucher_AccountPlanID']  = 0;
                        
                        $TotalPrice = $line->QuantityDelivered * $line->UnitCustPrice;

                        if($line->Vat > 0) {
                            #Add VAT to the price - since it is ex VAT
                            #print "$line->UnitCustPrice * (($line->Vat/100) +1)";
                            $TotalPrice = $TotalPrice * (($line->Vat/100) +1);
                        }

                        if($TotalPrice > 0) {
                            $VoucherH['voucher_AmountIn']   = abs($TotalPrice);
                            $VoucherH['voucher_AmountOut']  = 0;
                        }
                        else {
                            $VoucherH['voucher_AmountOut']  = abs($TotalPrice);
                            $VoucherH['voucher_AmountIn']   = 0;
                        }
    
                        $VoucherH['voucher_Vat']            = $line->Vat;
                        #$VoucherH['voucher_VatID']         = $line->VatID; Has to be mapped properly
                        if($line->QuantityDelivered > 0) {
                            $VoucherH['voucher_Description']    .= round($line->QuantityDelivered,2) . 'x';
                        }
                        if($line->ProductNumber) {
                            $VoucherH['voucher_Description']    .= $line->ProductNumber . ':';
                        }
                        if($line->ProductName) {
                            $VoucherH['voucher_Description']    .= $line->ProductName;
                        }
    
                        #Motkonto resultat.
                        $VoucherH['voucher_AccountPlanID']  = $line->AccountPlanID;
                        #print_r($VoucherH);
                        $this->accounting->insert_voucher_line(array('post' => $VoucherH, 'accountplanid' => $VoucherH['voucher_AccountPlanID'], 'VoucherType'=> $InvoiceO->VoucherType, 'comment' => 'Fra fakturabank'));
                    }
    
                    $this->accounting->set_journal_motkonto(array('post' => $VoucherH, 'VoucherType' => $VoucherH['voucher_VoucherType']));
                    $this->accounting->correct_journal_balance($VoucherH, $VoucherH['voucher_JournalID'], $VoucherH['voucher_VoucherType']);

                    ####################################################################################
                    #Update invoicein to journaled
                    $dataH = array();
                    $dataH['ID']        = $InvoiceO->ID;
                    $dataH['JournalID'] = $InvoiceO->JournalID;
                    $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'invoicein', 'debug' => false));
    
                    ####################################################################################
                    #Update bankaccount on accountplan (for later usage in direkte remittering - could be punched to - 
                    #Could be erronus i the same accountplan has more than one bank account. We just ignore it for now.
                    if($InvoiceO->BankAccount) {
                        $dataH = array();
                        $dataH['DomesticBankAccount']   = $InvoiceO->BankAccount;
                        $dataH['AccountPlanID']         = $InvoiceO->SupplierAccountPlanID;
                        $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountplan', 'debug' => false));
                    }
                } else {
                    print "Fakturaen er bilagsf¿rt<br>";
                }
            }
            $this->fill(array()); #Refresh the list after a new query - because lots of paramters get updated when journaling.
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
}
?>
