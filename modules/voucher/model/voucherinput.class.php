<?
##################################
#
# Funksjoner som brukes i bilagsregistreringsskjermbildet journal/edit
#
##################################

includelogic('exchange/exchange');

class framework_logic_voucherinput
{
    #Voucherline variables
    public $JournalID               = 0;
    public $JournalIDOld            = 0;
    public $VoucherID               = 0;
    public $AccountPlanID           = 0;
    public $VoucherPeriod           = '';
    public $VoucherDate             = '';
    public $Currency                = '';
    public $VoucherType             = '';
    public $AmountIn                = 0;
    public $AmountOut               = 0;
    public $Amount                  = 0;
    public $AutomaticReason         = 'Manuell';
    public $InsertedByPersonID      = 0;
    public $DisableAutoVat          = 0;
    public $EnableAutoBalance       = 0;
    public $AddedByAutoBalance      = 0;
    public $AutomaticVatVoucherID   = 0;
    public $AutomaticBalanceID      = 0;
    public $ProjectID               = 0;
    public $DepartmentID            = 0;
    public $Description             = '';
    public $Quantity                = 0;
    public $Vat                     = 0;
    public $VatOld                  = 0;
    public $VatID                   = 0;
    public $VatIDOld                = 0;
    public $Active                  = 1;
    public $KID                     = '';
    public $InvoiceID               = '';
    public $ForeignCurrencyID = '';
    public $ForeignConvRate = 0;
    public $ForeignAmount = 0;

    #Other control variables

    #Hash to determine if we are talking about a purchase
    public $type_purchase           = array('buycredit_out' => true, 'buynotacredit_out' => true, 'buycash_out' => true, 'buynotacash_out' => true);
    #Hash to determine if we are talking about a sale
    public $type_sale               = array('salecredit_in' => true, 'salenotacredit_in' => true, 'salecash_in' => true, 'salenotacash_in' => true);


    public $type                = false;
    public $CustomerNumber      = false;
    public $new                 = false;
    public $searchstring        = '';
    public $exit                = 0;
    public $accountplan         = array();
    
    #For automatic processing
    public $DefAccountPlanID    = 0;
    public $AmountField         = 'out';
    public $autovoucher         = array();

    /***************************************************************************
    * comment
    * @param
    * @return
    */
    
    function __construct($args) {
        global $_lib;
    
        $this->convert($args);
                
        $this->AccountPlanID      = $args['voucher_AccountPlanID'];
        $this->VoucherPeriod      = strip_tags($args['voucher_VoucherPeriod']);
        $this->Vat                = $args['voucher_Vat'];
        $this->VatID              = $args['voucher_VatID'];
        $this->VatOld             = $args['voucher_VatOld'];
        $this->VatIDOld           = $args['voucher_VatIDOld'];
        $this->UpdatedByPersonID  = $_lib['sess']->login_id;
        $this->ProjectID          = $args['voucher_ProjectID'];
        $this->DepartmentID       = $args['voucher_DepartmentID'];
        $this->Description        = strip_tags($args['voucher_Description']);
        $this->AccountLineID      = $args['AccountLineID'];
        $this->Quantity           = $args['voucher_Quantity'];
        
        $this->Currency = exchange::getLocalCurrency();
        $this->Currency           = strip_tags($args['voucher_Currency']);

        $foreign_converted_amount = false;

        ########################################
        #Foreign currency information
        if(isset($args['voucher_ForeignCurrencyID']))
        {
            if ($args['voucher_ForeignCurrencyID'] != "") {
                if (Exchange::validateForeignCurrencyFields($args)) {
                    $this->ForeignCurrencyID = $args['voucher_ForeignCurrencyID'];
                    $this->ForeignAmount = $args['voucher_ForeignAmount'];
                    $foreign_currency_direction = isset($args['voucher_ForeignCurrencyDirection']) &&
                        $args['voucher_ForeignCurrencyDirection'] == 'in' ? 'in' : 'out';

                    $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_ForeignAmount']));
                    $this->ForeignAmount = $hash['value'];
                    $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_ForeignConvRate']));
                    $this->ForeignConvRate = $hash['value'];

                    $foreign_converted_amount = abs($this->ForeignAmount * (100 / $this->ForeignConvRate));
                    if ($foreign_converted_amount) {
                        /* if ($foreign_converted_amount > 0) { */
                        if ($foreign_currency_direction == 'in') {
                            $this->AmountIn = $foreign_converted_amount;
                            $this->AmountOut = 0;
                        } else {
                            $this->AmountIn = 0;
                            $this->AmountOut = $foreign_converted_amount;
                        }
                    }
                } else {
                    // invalid data, ignore for now
                }
            } else {
                $this->ForeignCurrencyID = "";
                $this->ForeignConvRate = 0;
                $this->ForeignAmount = 0;
            }
        }


        if(isset($_REQUEST['voucher_VoucherType'])) 
            $this->VoucherType        = strip_tags($args['voucher_VoucherType']);
        elseif(isset($_REQUEST['VoucherType'])) 
            $this->VoucherType        = strip_tags($args['VoucherType']);
    
        $this->KID                = $args['voucher_KID']; #Burde ikke trenge 2 referanser
        $this->InvoiceID          = $args['voucher_InvoiceID'];
        
        if ($foreign_converted_amount) {
            # I do not enable setting Amount yet, because it seems to be out of use
            # $this->Amount             = $foreign_converted_amount;
        } else {
            $this->Amount             = strip_tags($args['Amount']);
        }
        
        $this->CustomerNumber     = strip_tags($args['CustomerNumber']);
        if(!$this->CustomerNumber) {
          $this->CustomerNumber = "";
        }
        
        $this->setNew($args['new'], 'init'); #True if it is the first time it runs
        $this->searching = strip_tags($args['searching']); #True if linked to this page from a JournalID
        
        if(!$this->JournalID && $args['voucher_JournalID']) { #Given internally in record.inc
            $this->JournalID = (int) $args['voucher_JournalID'];
            #print "<h1>XXPsann</h1><br>";
        } elseif(!$this->JournalID && $args['JournalID']) { #Given internally in record.inc
            $this->JournalID = (int) $args['JournalID'];
            #print "<h1>XXPsann</h1><br>";
        } else {
            #print "<h1>OOPsann</h1><br>";
        }
        
        $this->JournalIDOrg = $args['JournalIDOrg']; #Old convension
        $this->JournalIDOld = $args['JournalIDOrg'];
        
        if(!$this->VoucherID) { #Given internally in record.inc
          $this->VoucherID            = $args['voucher_VoucherID'];
          #print "voucherinput1   : VoucherID: " . $this->VoucherID . "<br>";
        }
        
        $this->VoucherIDOld = $this->VoucherID;
        #print "VoucherIDOld: $this->VoucherIDOld = VoucherID:$this->VoucherID<br>\n";

        ########################################################################
        if($this->type = $args['type']) #Type overstyrer alt
            $this->type = strip_tags($args['type']);
        elseif($this->VoucherType == 'K')
            $this->type="cash_in";
        elseif($this->VoucherType == 'B')
            $this->type="bank_in";
        elseif($this->VoucherType == 'U')
            $this->type="buycash_out";
        elseif($this->VoucherType == 'L')
            $this->type="salary";
        elseif($this->VoucherType == 'S')
            $this->type="salecash_in";
            
        
        ########################################################################
        if(isset($args['action_voucher_update']))
            $this->action['voucher_update']      = true;
        if(isset($args['action_voucher_new']))
            $this->action['voucher_new']         = true;
        if(isset($args['action_voucher_head_update']))
            $this->action['voucher_head_update'] = true;
        if(isset($args['action_voucher_head_delete']))
            $this->action['voucher_head_delete'] = true;
        if(isset($args['action_voucherline_new']))
            $this->action['voucherline_new']     = true;
        if(isset($args['action_voucher_delete']))
            $this->action['voucher_delete']      = true;
        if(isset($args['action_journalid_search']))
            $this->action['journalid_search']  = true;
        if(isset($args['view_linedetails']))
            $this->action['view_linedetails']      = true;      

        if(!$this->action['voucher_new'] && !$this->action['voucher_head_update'] && $this->VoucherID)
        {
            #We default to voucgher update because post exist, probably javascript autosubmit
            #Vil dette v¾re feil i noen sammenhenger?
            $this->action['voucher_update'] = true;
        }

        $this->logic($args);
        $this->record();

        #print "Opprinnelige inn verdier\n";
        #print_r($this);
    }

    function convert($args) {
        global $_lib;
        #################################################################################################################
        #Remove number format in number input. Necesarry beacuse of calculations before saving.
        if($args['voucher_AmountIn'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_AmountIn']));
          $this->AmountIn = $hash['value'];
          $error1 = $hash['error'];
        }
        if($args['voucher_AmountOut'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_AmountOut']));
          $this->AmountOut = $hash['value'];
          $error2 = $hash['error'];
        }
        if($args['voucher_AmountInOld'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_AmountInOld']));
          $this->AmountInOld = $hash['value'];
          $error1 = $hash['error'];
        }
        if($args['voucher_AmountOutOld'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_AmountOutOld']));
          $this->AmountOutOld = $hash['value'];
          $error2 = $hash['error'];
        }
        if($args['voucher_ForeignAmountIn'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_ForeignAmountIn']));
          $this->ForeignAmountIn = $hash['value'];
          $error3 = $hash['error'];
        }
        if($args['voucher_ForeignAmountOut'])
        {
          $hash = $_lib['convert']->Amount(array('value'=>$args['voucher_ForeignAmountOut']));
          $this->ForeignAmountOut = $hash['value'];
          $error4 = $hash['error'];
        }
        if($args['voucher_VoucherDate'])
        {
          $hash = $_lib['convert']->Date(array('value'=>$args['voucher_VoucherDate']));
          $this->VoucherDate = $hash['value'];
          $error5 = $hash['error'];
        }
        if($args['voucher_DueDate'])
        {
          $hash = $_lib['convert']->Date(array('value'=>$args['voucher_DueDate']));
          $this->DueDate = $hash['value'];
          $error6 = $hash['error'];
        }
        $_lib['message']->add(array('message' => $error1 . $error2 . $error3 . $error4 . $error5 . $error6));    
    }

    ############################################################################
    function logic($args) {
        global $_lib;
        $status = true;

        $query_setup    = "select name, value from setup";
        $setup = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

        if($this->new || $this->action['voucher_update'] || $this->action['voucher_head_update'] || $this->action['voucher_new'] or $this->type) {
        
            if($this->type == "cash_in") {
              $this->DefAccountPlanID                 = $setup['kasseinn'];
              $this->autovoucher['balanse']           = true;
              $this->VoucherType                      = 'K';
              $this->AmountField                      = 'in';
            }
            elseif($this->type == "cash_out") {
              $this->DefAccountPlanID                 = $setup['kasseut'];
              $this->autovoucher['balanse']           = true;
              $this->VoucherType                      = 'K';
              $this->AmountField                      = 'out';
            }
            elseif($this->type == "bank_in") {
              $this->DefAccountPlanID                 = $setup['bankinn'];
              $this->autovoucher['balanse']           = true;
              $this->VoucherType                      = 'B';
              $this->AmountField                      = 'in';
            }
            elseif($this->type == "bank_out") {
              $this->DefAccountPlanID                 = $setup['bankut'];
              #$autovoucher['balanse1']         = $setup['bankut'];
              $this->autovoucher['balanse']           = true;
              $this->VoucherType                      = 'B';
              $this->AmountField                      = 'out';
            }
            elseif($this->type == "buycash_out") {
              $this->DefAccountPlanID                 = $setup['buycashut'];
              $this->autovoucher['balanse1']          = $setup['buycashut'];
              $this->autovoucher['balanse']           = true;
              $this->autovoucher['resultat']          = true;
              $this->autovoucher['reskontro']         = $setup['buycashreskontro'];
              $this->autovoucher['resultat1']         = $setup['buycashutgift'];
              $this->VoucherType                      = 'U';
              $this->AmountField                      = 'out';
              #Invert
              if($this->AmountIn > 0 && $this->action['voucher_new']) {
                $tmpOut = $this->AmountOut;
                $this->AmountOut = $this->AmountIn;
                $this->AmountIn  = $tmpOut;
              }
            }
            elseif($this->type == "buycredit_out") {
              $this->DefAccountPlanID                 = $setup['buycreditreskontro'];
              $this->autovoucher['resultat1']         = $setup['buycreditutgift'];
              $this->autovoucher['resultat']          = true;
              $this->VoucherType                      = 'U';
              $this->AmountField                      = 'out';
        
              #Invert
              if($this->AmountIn > 0 && $this->action['voucher_new']) {
                $tmpOut             = $this->AmountOut;
                $this->AmountOut    = $this->AmountIn;
                $this->AmountIn     = $tmpOut;
              }
            }
            elseif($this->type == "buynotacash_out") {
              $this->DefAccountPlanID                 = $setup['buynotacashinn'];
              $this->autovoucher['balanse']           = true;
              $this->autovoucher['resultat']          = true;
              $this->autovoucher['reskontro']         = $setup['buynotacashreskontro'];
              $this->autovoucher['resultat1']         = $setup['buynotacashutgift'];
              $this->VoucherType                      = 'U';
              $this->AmountField                      = 'out';
        
              #Invert
              if($this->AmountOut > 0 && $this->action['voucher_new']) {
                $tmpIn              = $this->AmountIn;
                $this->AmountIn     = $this->AmountOut;
                $this->AmountOut    = $tmpIn;
              }
            }
            elseif($this->type == "buynotacredit_out") {
              $this->DefAccountPlanID                 = $setup['buynotacreditreskontro'];
              $this->autovoucher['resultat1']         = $setup['buynotacreditutgift'];
              $this->autovoucher['resultat']          = true;
              $this->VoucherType                      = 'U';
              $this->AmountField                      = 'out';
              #Invert
              if($this->AmountOut > 0 and $this->action['voucher_new']) {
                $tmpIn = $this->AmountIn;
                $this->AmountIn      = $this->AmountOut;
                $this->AmountOut     = $tmpIn;
              }
        
            }
            elseif($this->type == "salecash_in") {
              #Salg faktura kontant
              $this->DefAccountPlanID                 = $setup['salecashut'];
              $this->autovoucher['balanse1']          = $setup['salecashut'];
              $this->autovoucher['balanse']           = true;
              $this->autovoucher['reskontro']         = $setup['salecashreskontro'];
              $this->autovoucher['resultat']          = true;
              $this->autovoucher['resultat1']         = $setup['salecashinntekt'];
              $this->VoucherType                      = 'S';
              $this->AmountField                      = 'in';
              #Invert
              if($this->AmountOut > 0 && $this->action['voucher_new']) {
                $tmpIn = $this->AmountIn;
                $this->AmountIn      = $this->AmountOut;
                $this->AmountOut     = $tmpIn;
              }
            }
            elseif($this->type == "salecredit_in") {
              #Salg faktura kontant
              $this->DefAccountPlanID                 = $setup['salecreditreskontro'];
              $this->autovoucher['resultat1']         = $setup['salecreditinntekt'];
              $this->autovoucher['resultat']          = true;
              $this->VoucherType                      = 'S';
              $this->AmountField                      = 'in';
              #Invert
              if($this->AmountOut > 0 && $this->action['voucher_new']) {
                $tmpIn = $this->AmountIn;
                $this->AmountIn      = $this->AmountOut;
                $this->AmountOut     = $tmpIn;
              }
        
            }
            elseif($this->type == "salenotacash_in") {
              $this->DefAccountPlanID                 = $setup['salenotacashut'];
              $this->autovoucher['balanse1']          = $setup['salenotacashut'];
        
              $this->autovoucher['balanse']           = true;
              $this->autovoucher['resultat']          = true;
              $this->autovoucher['reskontro']         = $setup['salenotacashreskontro'];
              $this->autovoucher['resultat1']         = $setup['salenotacashinntekt'];
              $this->VoucherType                      = 'S';
              $this->AmountField                      = 'in';
        
              #Invert
              if($this->AmountIn > 0 and $this->action['voucher_new']) {
                $tmpOut = $this->AmountOut;
                $this->AmountOut = $this->AmountIn;
                $this->AmountIn  = $tmpOut;
              }
            }
            elseif($this->type == "salenotacredit_in") {
              $this->DefAccountPlanID                 = $setup['salenotacreditreskontro'];
        
              #$autovoucher['reskontro']        = $setup['salenotacreditreskontro'];
              $this->autovoucher['resultat1']         = $setup['salenotacreditinntekt'];
              $this->autovoucher['resultat']          = true;
              $this->VoucherType                      = 'S';
              $this->AmountField                      = 'in';
        
              #Invert
              if($this->AmountIn > 0 && $this->action['voucher_new']) {
                $tmpOut          = $this->AmountOut;
                $this->AmountOut = $this->AmountIn;
                $this->AmountIn  = $tmpOut;
              }
            }
            elseif($this->type == "salary") {
              $this->VoucherType                      = 'L';
              $this->AmountField                      = 'out';
            }
            else {
              $_lib['message']->add(array('message' =>'Error: Missing type parameter'));
              $status = false;
            }
        }

        return true;
    }
    
    function record() {
        global $_lib, $accounting;
        
        if($this->AccountPlanID > 0) 
            $this->accountplan = $accounting->get_accountplan_object($this->AccountPlanID);

        ####################################
        #Calculate credit days on sale and purchase posterings         
        
        if($this->accountplan->EnableCredit && ($this->DueDate == '0000-00-00' || $this->DueDate == '')) {
            #Calculate DueDate form accountplan given
            $this->DueDate = $_lib['date']->add_Days($this->VoucherDate, $this->accountplan->CreditDays);
        }
        
        #Set duedate to voucher date if empty
        if($this->DueDate == '0000-00-00' or $this->DueDate == '') {
          $this->DueDate = $this->VoucherDate;
        }
        
        if($this->action['voucher_head_update'])
        {
          if(!$this->VoucherPeriod) {
            $_lib['message']->add(array('message' => "Du m&aring; velge en periode for posteringen din"));
            $this->exit = 1;
          }
        
          if(!$this->VoucherDate || $this->VoucherDate == '0000-00-00' || $this->VoucherDate == '0000-00-00 00:00:00')
          {
            $_lib['message']->add(array('message' => "<br>Du m&aring; velge en dato til posteringen din<br>"));
            $this->exit = 1;
          }
        
          if(!$this->JournalID) {
            $_lib['message']->add(array('message' => "Du m&aring; velge et bilagsnummer til posteringen din<br>"));
            $this->exit = 1;
          }
        
          if(!$this->VoucherType) {
            $_lib['message']->add(array('message' => "Du m&aring; velge en type til posteringen din<br>"));
            $this->exit = 1;
          }
        
        }
        elseif(!$this->new && !$this->action['view_linedetails'] && !$this->action['voucher_delete'] && !$this->action['voucher_head_delete'] && !$this->action['journalid_search'] && !$this->searchstring)
        {
            #We only validate input if we really try to save something
        
            if(!$this->AccountPlanID) {
              $_lib['message']->add(array('message' => "Du m&aring; velge en konto til posteringen din<br>"));
              $this->exit = 1;
            }
        
            if($this->DueDate == '0000-00-00 00:00:00') {
              $_lib['message']->add(array('message' => "Du m&aring; velge en forfallsdato til posteringen din<br>"));
              $this->exit = 1;
            }
        
            $hash = $_lib['convert']->Date(array('value'=>$this->VoucherDate));
            $this->VoucherDate = $hash['value'];
            $tmp = $hash['error'];
                
            if(($this->action['voucher_update'] or $this->action['voucher_head_update'] or $this->action['voucher_new']) and (!$accounting->is_valid_accountperiod($this->VoucherPeriod, $_lib['sess']->get_person('AccessLevel')))) {
              $_lib['message']->add(array('message' => "Perioden du har valgt er avsluttet<br>"));
              $this->exit = 1;
            }
        }
        
        #################################################################################################################
        #Sjekk om bilagsnummer finnes fra fÃ¯Â¿Â½r, default er neste ledige bilagsnummer
        #################################################################################################################

        #print "fjerner: voucherinput2   : VoucherID: " . $this->VoucherID . "<br>";
        $this->VoucherID = ''; #Sjekk TE. Ikke riktig Œ fjerne denne i alle sammenhenger.
        
        ####################################
        #Check the validity og Voucher date compared to period
        #
        #Changed 29/11-04
        if($this->action['voucher_head_update'] and $_lib['date']->get_this_period($this->VoucherDate) != $this->VoucherPeriod) {
          $_lib['message']->add(array('message' => "Bilag er lagret, men merk at dato ikke er gyldig i perioden du valgte<br>"));
          //$exit = 0;
        }
        
        ####################################
        #Get default posteringer for balanse (overwrite from main menu if anything exists there
        if($this->accountplan->EnableMotkontoBalanse == 1 && $this->accountplan->MotkontoBalanse1 > 0 && isset($this->autovoucher['balanse']))
        {
            #print "Overstyrer balanse regler: MotkontoBalanse1: $accountplan->MotkontoBalanse1<br>\n";    
            $this->autovoucher['balanse1']  = $this->accountplan->MotkontoBalanse1;
            $this->autovoucher['balanse2']  = $this->accountplan->MotkontoBalanse2;
            $this->autovoucher['balanse3']  = $this->accountplan->MotkontoBalanse3;
            $this->autovoucher['reskontro'] = "";
        } else {
            #print "Overstyrer ikke balanse regler<br>\n";        
        }
        
        ####################################
        #Get default posteringer for resultat (overwrite from main menu if anything exists there
        if($this->accountplan->EnableMotkontoResultat == 1 && $this->accountplan->MotkontoResultat1 > 0 && isset($this->autovoucher['resultat']))
        {
            #print "Overstyrer resultat regler<br>\n";    
            $this->autovoucher['resultat1'] = $this->accountplan->MotkontoResultat1;
            $this->autovoucher['resultat2'] = $this->accountplan->MotkontoResultat2;
            $this->autovoucher['resultat3'] = $this->accountplan->MotkontoResultat3;
            $this->autovoucher['reskontro'] = "";
        } else {
            #print "Overstyrer ikke resultat regler<br>\n";    
        }
        
        #Det blir vel riktig at denne alltid hardkodes til valget som er gjort i registreringsbildet.
        if($this->type == "buycash_out" || $this->type == "buynotacash_out" || $this->type == "salecash_in" || $this->type == "salenotacash_in") {
            $this->autovoucher['reskontro'] = $this->AccountPlanID;
        }
        #}
        
        #print_r($autovoucher);
        
        #################################################################################################################
        #Check that the period is open
        $query_period   = "select Status from accountperiod where Period='$this->VoucherPeriod'";
        $row_period     = $_lib['storage']->get_row(array('query' => $query_period));
        #print "$query_period";
        #print_r($row_period);
        
        if($this->VoucherPeriod != '' && $this->VoucherPeriod != 0){
          if($row_period->Status == 0 || $row_period->Status == 4){
            $_lib['message']->add(array('message' => "Denne perioden er permanent stengt<br>"));
            $this->exit = true;

          } elseif($row_period->Status == 3 && $_lib['sess']->get_person('AccessLevel') < 3) {
            $_lib['message']->add(array('message' => "Denne perioden er lukket og mÃ¯Â¿Â½ Ã¯Â¿Â½pnes av regnskapsfÃ¯Â¿Â½rer<br>"));
            $this->exit = true;

          } elseif($row_period->Status == 1 && $_lib['sess']->get_person('AccessLevel') < 3) { #Access lever 3=higest Administrator
            $_lib['message']->add(array('message' => "Denne perioden er lukket, men kan fÃ¯Â¿Â½res pÃ¯Â¿Â½ av regnskapsfÃ¯Â¿Â½rer med hÃ¯Â¿Â½yere autorisasjon enn deg"));
            $this->exit = true;
          }
        } else {
            #Her mangler det egentlig mye periodedata. Da er den Œpen.
        }
        
        if($this->exit) {
            #Hvis det er en brŒ slutt sŒ markerer vi den som ny
            $this->setNew(1, 'exit setter new');
        }
        
        return true;            
    }

    function setNew($new, $from) {
        $this->new     = $new;
        #print "setter new: $this->new: $from<br>\n";
    }

    
    function setAmountIn($amount) {
        #print "<h2>Setter AmountIn: $amount</h2>";
        $this->AmountIn     = $amount;
        $this->AmountOut    = 0;
    }

    function setAmountOut($amount) {
        #print "<h2>Setter AmountOut: $amount</h2>";
        $this->AmountOut    = $amount;
        $this->AmountIn     = 0;
        #print_r($this);
    }

    function  currency() {
        global $_lib;
        
        #################################################################################################################
        #Calculate currency
        if($this->accountplan->EnableCurrency) {
            $currency = $this->accounting->get_currency_object($accountplan->Currency);
            if($currency->ExchangeRate > 0) {
                if($this->ForeignAmountOut > 0){
                    $this->AmountOut = $this->ForeignAmountOut * $currency->ExchangeRate;
                }
                if($this->ForeignAmountIn > 0){
                    $this->AmountIn  = $this->ForeignAmountIn  * $currency->ExchangeRate;
                }
              } else {
                  $_lib['message']->add(array('message' => "Advarsel: Det er ikke oppgitt omregningsfaktor for valuta handel<br>"));
              }
        }

        #Remove VoucherID from all queries
        $this->VoucherID = 0;    
    }
    
    ############################################################################
    function request($calledfrom) {
        global $_lib, $accounting;
        #Builds a has similar to request for db updates and the likes
        #NŒr vi oppretter en ny linje, sŒ mŒ den fŒ v¾re 0. MŒ gj¿res til slutt da amountin/out nkan v¾re satt fra kid ref s¿k og oppslag
        if($this->AmountIn == 0 && $this->AmountOut == 0 && !$this->action['voucherline_new'] && !$this->action['voucher_delete'] && !$this->action['voucher_head_delete'] && !$this->new) {
            $_lib['message']->add(array('message' => "Det m&aring; v&aelig;re fylt ut enten credit eller debit i en postering<br>"));
            $this->exit = 1;
        }
    
        $request = array();
        #Hode informasjon, burde ikke bli oppdatert hver gang vi kj¿rer update? Hvis info ikke finnes burde den hentes fra f¿rste posteringslinje med en select for Œ sette riktige default verdier.
        #Vi kunne hentetut opplysninger pŒ JournalID
        $request['voucher_JournalID']           = $this->JournalID; #Denne burde egentlig ikke bli oppdatert her heller
        $request['voucher_VoucherType']         = $this->VoucherType;
        
        if($this->action['voucher_head_update'] || $this->action['voucher_new']) {
            #If we asked to change the head, allow these values to be changed.
            $request['voucher_VoucherDate']         = $this->VoucherDate; #Denne skal ikke oppdateres her, da den kun blir satt ved spesielle anlednginer
            $request['voucher_VoucherPeriod']       = $this->VoucherPeriod;
        } else {
            #Hent opplysningene fra den f¿rste linjen, de skal aldri endres hvis det ikke er hode. En bedre datamodell hadde v¾rt Œ hatt periode, dato og type i en egen tabell.
            $head = $accounting->get_journal_head_data(array('JournalID' => $this->JournalID, 'VoucherType' => $this->VoucherType));
            $request['voucher_VoucherDate']         = $head->VoucherDate; #Denne skal ikke oppdateres her, da den kun blir satt ved spesielle anlednginer
            $request['voucher_VoucherPeriod']       = $head->VoucherPeriod;
        }
        #Linje informasjon, kan oppdateres hver gang.
        $request['voucher_AccountPlanID']       = $this->AccountPlanID;
        $request['voucher_Currency']            = $this->Currency;
        $request['voucher_UpdatedByPersonID']   = $this->UpdatedByPersonID;
        $request['voucher_Vat']                 = $this->Vat;
        $request['voucher_VatID']               = $this->VatID;
        #Denne mŒ alltid settes - fordi den skal nulle ut flagg evd manuell hŒndtering
        $request['voucher_AddedByAutoBalance']  = $this->AddedByAutoBalance;
        $request['voucher_DisableAutoVat']      = $this->DisableAutoVat;
        $request['voucher_Active']              = $this->Active;
        $request['voucher_AutomaticReason']     = $this->AutomaticReason;
        $request['voucher_ProjectID']           = $this->ProjectID;
        $request['voucher_DepartmentID']        = $this->DepartmentID;
        $request['voucher_Description']         = $this->Description;
        $request['voucher_KID']                 = $this->KID;
        $request['voucher_InvoiceID']           = $this->InvoiceID;
        $request['voucher_DueDate']             = $this->DueDate;
        $request['voucher_Quantity']            = $this->Quantity;
        
        #Myst always be present. Both cannot contain a value
        if($this->AmountIn > 0) {
            $request['voucher_AmountIn']            = $this->AmountIn;
            $request['voucher_AmountOut']           = 0;
        } elseif($this->AmountOut > 0) {
            $request['voucher_AmountIn']            = 0;
            $request['voucher_AmountOut']           = $this->AmountOut;
        }

        if($this->VoucherID)
            $request['voucher_VoucherID']       = $this->VoucherID;

        if($this->AmountIn > 0 && $this->AmountOut > 0) {
            print "<h1>Critical error. Both credit and debit sides are set</h1>";
        }

        if($this->JournalID)
            $request['voucher_JournalID']               = $this->JournalID;
     
        if($this->InsertedByPersonID)
            $request['voucher_InsertedByPersonID']      = $this->InsertedByPersonID;
                
        if($this->EnableAutoBalance)
            $request['voucher_EnableAutoBalance']       = $this->EnableAutoBalance;
        
        if($this->AutomaticVatVoucherID)
            $request['voucher_AutomaticVatVoucherID']   = $this->AutomaticVatVoucherID;
        
        if($this->AutomaticBalanceID)
            $request['voucher_AutomaticBalanceID']      = $this->AutomaticBalanceID;

        if ($this->action['voucher_head_update'] || 
            $this->action['voucher_new'] ||
            $this->action['voucher_update'] ||
            $this->action['voucherline_new']) {

            $request['voucher_ForeignCurrencyID']      = $this->ForeignCurrencyID;
            $request['voucher_ForeignConvRate']      = $this->ForeignConvRate;
            $request['voucher_ForeignAmount']      = $this->ForeignAmount;            
        }

         
        #print "<h2>Output fra voucher objektet kalt fra: $calledfrom</h2>";
        #print_r($request);

        return $request;
    }
}
?>
