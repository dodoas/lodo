<?
/**
* Class containing generic accounting functionality
*
* Example usage
* includelogic('accounting/accounting');
* $accounting     = new accounting();
* list($JournalID, $message) = $accounting->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $this->VoucherType));
* $VoucherID = $accounting->insert_voucher_line(array('post'=>$data, 'accountplanid'=>$_REQUEST['voucher_AccountPlanID'], 'type'=>'first', 'VoucherType'=>$VoucherType));
*
* @package empatix_lodo
* @version  $Id:
* @author Thomas Ekdahl, Empatix AS
* @copyright http://www.empatix.com/ Empatix AS, 1994-2005, post@empatix.no
*/

includelogic('postmotpost/postmotpost');
includelogic('exchange/exchange');

class accounting {
    public  $debug               = false;
    public  $postmotpost         = array();
    private $accountplan_usednow = array(); #Hash to not upate used accounts more often than needed. Once pr page
    private $voucher_to_hovedbok = array();
    private $accountplanH        = array(); #Accountplan cache to not ask to frequently in the database

    /***************************************************************************
    * Konstruktor
    * @param
    * @return
    */
    function __construct() {
        #Empty
        $this->postmotpost = new postmotpost(array());
    }

    /***************************************************************************
    * Destruktor
    * Run clean up and caløculations only to be run once - here
    * @param
    * @return
    */
    function __destruct() {
        
        #If we had a hash with all periods we had been touching - we could loop over this hash and update all auto posteringer - just once - and never forget it.
        #$this->set_journal_motkonto(array('post'=> array('voucher_VoucherPeriod' => $voucher->VoucherPeriod)));
        
        #We could also correct the balance in all journals involved when object dies.
        #$this->correct_journal_balance();
        
    }

    /***************************************************************************
    * Finn neste tilgjengelig bilagsnummer
    * @param $args['JournalID'], $args['type'], $args['AutoFromWeeklySaleID'], $args['reuse'] (If we reuse, we dont find the next available, and we dont update, but search it) ,$args['available'], $args['update'], $args['verify'] (Verify the journalID),
    * @return
    */
    public function get_next_available_journalid($args) {
        global $_lib;
        #################################################################################################################
        if(!$args['type']) {
          $message = "Missing VoucherType to get_next_available_journalid<br>";
        }

        #Find next available journal id / Check that a journal id is not reused
        $voucersequence['B'] = $_lib['sess']->get_companydef('VoucherBankNumber');
        $voucersequence['K'] = $_lib['sess']->get_companydef('VoucherCashNumber');
        $voucersequence['U'] = $_lib['sess']->get_companydef('VoucherBuyNumber');
        $voucersequence['S'] = $_lib['sess']->get_companydef('VoucherSaleNumber');
        $voucersequence['L'] = $_lib['sess']->get_companydef('VoucherSalaryNumber');
        $voucersequence['A'] = $_lib['sess']->get_companydef('VoucherAutoNumber');
        $voucersequence['O'] = $_lib['sess']->get_companydef('VoucherWeeklysaleNumber');

        $sequencechange['B'] = $_lib['sess']->get_companydef('EnableBankNumberSequence');
        $sequencechange['K'] = $_lib['sess']->get_companydef('EnableCashNumberSequence');
        $sequencechange['U'] = $_lib['sess']->get_companydef('EnableBuyNumberSequence');
        $sequencechange['S'] = $_lib['sess']->get_companydef('EnableSaleNumberSequence');
        $sequencechange['L'] = $_lib['sess']->get_companydef('EnableSalaryNumberSequence');
        $sequencechange['A'] = $_lib['sess']->get_companydef('EnableAutoNumberSequence');
        $sequencechange['O'] = $_lib['sess']->get_companydef('EnableWeeklysaleNumberSequence');

        $sequencefield['B']  = "VoucherBankNumber";
        $sequencefield['K']  = "VoucherCashNumber";
        $sequencefield['U']  = "VoucherBuyNumber";
        $sequencefield['S']  = "VoucherSaleNumber";
        $sequencefield['L']  = "VoucherSalaryNumber";
        $sequencefield['A']  = "VoucherAutoNumber";
        $sequencefield['O']  = "VoucherWeeklysaleNumber";
        
        #print_r($voucersequence);

        if(isset($args['verify']))
        {
            if(isset($args['JournalID']))
            {
                #Check if the journal id asked for is in use, if it is in use, we use the next avaliable
                if(isset($args['AutoFromWeeklySaleID']))
                {
                    $select_journalid = "select JournalID from voucher where JournalID='".$args['JournalID']."' and VoucherType='".$args['type']."' and AutoFromWeeklySale != ".$args['AutoFromWeeklySaleID']." and Active=1 limit 1";
                }
                else
                {
                    $select_journalid = "select JournalID from voucher where JournalID='".$args['JournalID']."' and VoucherType='".$args['type']."' and Active=1 limit 1";
                }
                $_lib['sess']->debug($select_journalid);
                $row              = $_lib['storage']->get_row(array('query' => $select_journalid));
  
                if($row->JournalID)
                {
                    if($args['reuse'])
                    { #If we reuse, we dont find the next available, and we dont update, but search it
                        $args['available'] = true; #To get the next available to hget a diff
                        $args['update']    = false;
                    }
                    else
                    {
                        $args['available'] = true;
                    }
                } else {
                    $args['available'] = false;
                }
            }
            else
            {
              $message = "You cant verify without a journalid: $args[JournalID]<br>";
            }
        }

        if(isset($args['available']) && ($args['available']==true))
        {
            if($args['JournalID']) { $_lib['sess']->debug("Error: JournalID $args[JournalID] already exists"); }

            $args['JournalID'] = $voucersequence[$args['type']];

            #Check that it really is available
            $select_journalid   = "select JournalID from voucher where JournalID='" . $args['JournalID'] . "' and VoucherType='" . $args['type'] . "' and Active=1 limit 1";
            $_lib['sess']->debug($select_journalid);
            $row                = $_lib['storage']->get_row(array('query' => $select_journalid));

            if(isset($row->JournalID))
            {
              #Dette journalnummeret er tidligere brukt og firma info er ikke oppdatert
              #Vi finner neste ledige i rekken
              $select_newjournalid  = "select JournalID from voucher where VoucherType='" . $args['type'] . "' and Active=1 order by JournalID desc limit 1";
              $_lib['sess']->debug($select_newjournalid);

              $new                  = $_lib['storage']->get_row(array('query' => $select_newjournalid));
              $args['JournalID']      = $new->JournalID + 1;
              $message              = "Bilagsnummer ikke i samsvar med firmaopplysninger: automatisk tildelt nummer: $args[JournalID]<br>";
            }
        }

        if(isset($args['update']) and ($args['update']==true))
        {
          if(!isset($args['JournalID'])) { $message =  "Error: JournalID $args[JournalID] should exist<br>"; }

          #Check if sequence is ok. And save next number.
          if(($args['JournalID'] == $voucersequence[$args['type']] + 1) || ($sequencechange[$args['type']] == 1))
          {
            #Allowed to update sequence number
            $primarykeycompany['CompanyID']     = exchange::getLocalCurrency();

            $fields["company_" . $sequencefield[$args['type']]] = $args['JournalID'] + 1;
            #print "HER35<BR>";

            #print_r($fields);
            $_lib['storage']->db_update_hash($fields, 'company', $primarykeycompany);
          } else {
            #print "Sequence not ok<br>";
          }
        }

        return array($args['JournalID'], $message);
    }
    
    public function IsJournalIDInUse($JournalID, $VoucherType) {
        global $_lib;
        
        $status = false;
        $select_journalid = "select JournalID from voucher where JournalID='" . $JournalID . "' and VoucherType='" . $VoucherType . "' and Active=1 limit 1";
        $_lib['sess']->debug($select_journalid);
        $row              = $_lib['storage']->get_row(array('query' => $select_journalid));

        if($row->JournalID) {
            $status = true;
        }
        
        return $status;
    }

    /***************************************************************************
    * Beregn postering
    * @param
    * @return
    */
    public function sum_journal($JournalID, $VoucherType)
    {
        global $_lib;

        #Finn alle linjer i bilaget - ikke prøv å join i tilfelle noen avhengigheter mangler
        $select_voucher_sql = "select AmountIn, AmountOut, AccountPlanID, VoucherID from voucher where JournalID='$JournalID' and VoucherType='$VoucherType' and Active=1";
        $_lib['sess']->debug($select_voucher_sql);
        $voucherH = $_lib['storage']->get_hashhash(array('query' => $select_voucher_sql, 'key' => 'VoucherID'));

        #Finn alle hovedbokskontoer med reskontroer - disse skal vi ikke summere over - da vi ikke summerer disse i grensesnittet.
        $hovedbokmedreskontroH = $_lib['storage']->get_hash(array('query' => 'select * from accountplan where EnableReskontro=1', 'key' => 'AccountPlanID', 'value' => 'AccountPlanID'));

        #loop over og summer alle linjer som ikke har match i reskontroer
        $sum = 0;
        foreach($voucherH as $voucher) {
            if(!isset($hovedbokmedreskontroH[$voucher['AccountPlanID']])) {
                $sum += ($voucher['AmountIn'] - $voucher['AmountOut']);
                #print "num " . $voucher['VoucherID'] . "  * $sum += (" . $voucher['AmountIn'] . ' - ' . $voucher['AmountOut'] . ")<br>\n";

            }
        }

        $_lib['sess']->debug("sum: $sum $select_voucher_sql");
        #print (round(($voucher->sumin - $voucher->sumout), 2))."<br>\n";

        return round($sum, 2);
    }

    /***************************************************************************
    * Hent konto
    * @param
    * @return
    */
    public function get_accountplan_object($AccountplanID) {
      global $_lib;

      if (!is_numeric($AccountplanID)) {
          $_lib['message']->add(array('message' => 'get_accountplan_object(): Mangler AccountplanID'));
          # throw new Exception("missing accountplan");
          return false;
      }

      #Get the right color for the accountplan
      if(!isset($this->accountplanH[$AccountplanID])) {
            $query_plan                   = "select * from accountplan where Active=1 and AccountplanID='$AccountplanID'";
            $_lib['sess']->debug($query_plan);
            $this->accountplanH[$AccountplanID] = $_lib['storage']->get_row(array('query' => $query_plan));
            if(!$this->accountplanH[$AccountplanID]) {
                $_lib['message']->add(array('message' => 'Konto: ' . $AccountplanID . ' finnes ikke'));
                #print_r(debug_backtrace());
          }
      }
      return $this->accountplanH[$AccountplanID];
    }

    /***************************************************************************
    * Hent MVA konto informasjon basert pï¿½ MVA kode
    * @param [VatID, AccountPlanID]
    * @return
    */
    public function get_vataccount_object($args) {
        global $_lib;

        if(isset($args['date'])) {
          $date = $args['date'];
        } else {
          $date = $_lib['sess']->get_session('LoginFormDate');
        }
        if(!isset($args['VatID']) and !isset($args['AccountPlanID'])) {
          $args['VatID'] = 11; #Default to vatid 11 if nothing else spesified
        }
        if(isset($args['VatID'])) {
          $query_vat  = "select * from vat where Active=1 and VatID='".$args['VatID']."' and '$date' >= ValidFrom  and '$date' <= ValidTo limit 1";
        }
        else {
          $query_vat  = "select * from vat where Active=1 and AccountPlanID='".$args['AccountPlanID']."' and '$date' >= ValidFrom  and '$date' <= ValidTo limit 1";
        }
        #if($this->debug) 
        #print "$query_vat<br>\n";

        $vat = $_lib['storage']->get_row(array('query' => $query_vat));
        #Det trenger ikke v?re konto oppsett p? 30, 32, 60 og 62
        #Merk atVatID null bør sjekkes for opprinnelsen, dette bare demper feilmeldingene.
        if(!$vat->AccountPlanID && $args['VatID'] != 30 && $args['VatID'] != 32 && $args['VatID'] != 60 && $args['VatID'] != 62 && $args['VatID'] != 0) {
            $_lib['message']->add(array('message' => 'Det finnes ikke MVA oppsett for kode: ' . $args['VatID'] . ' og konto ' . $args['AccountPlanID'] . ' og dato: ' . $date));
        }

        return $vat;
    }

    /***************************************************************************
    * MVA konto hash
    * @param
    * @return
    */
    private function get_vataccount_hash($args) {
        global $_lib;
  
        if(isset($args['date'])) {
          $date = $args['date'];
        } else {
          $date = $_lib['sess']->get_session('LoginFormDate');
        }
        $date = $_lib['sess']->get_session('LoginFormDate');
        $query_vat  = "select AccountPlanID, 1 from vat where Active=1 and VatID <= 62 and '$date' >= ValidFrom  and '$date' <= ValidTo order by VatID asc";
        $_lib['sess']->debug($query_vat);
  
        #print "$query_vat<br>";
        return $_lib['storage']->get_hash(array('query' => $query_vat, 'key' => 'AccountPlanID', 'value' => 1));
    }

    /***************************************************************************
    * MVA prosent hash
    * @param
    * @return
    */
    public function get_vatpercenthash($args)
    {
        global $_lib;

        #We use the $args['accountVatID'] - too see if we are in buy or sale series vat - could have a meta tag in accountplan for this.

        $_lib['sess']->debug("get_vatpercenthash args");

        if(isset($args['date']))
        {
            $date = $args['date'];
        }
        else
        {
            $date = $_lib['sess']->get_session('LoginFormDate');
        }
        $where = "";

        if($args['accountVatID'] == 60)
            $where .= "and VatID = 60";
        elseif($args['accountVatID'] == 62)
            $where .= "and VatID = 62";
        elseif($args['accountVatID'] == 30)
            $where .= "and VatID = 30";
        elseif($args['accountVatID'] == 32)
            $where .= "and VatID = 32";
        elseif(isset($args['accountVatID']) && $args['accountVatID'] < 40)
            $where .= " and VatID < 40 and Percent!=0";
        elseif(isset($args['accountVatID']) && $args['accountVatID'] >= 40)
            $where .= " and VatID >= 40 and Percent!=0";

        if($args['sale'] == 1)
            $where .= " and VatID < 40";

        //$date = $_lib['sess']->get_session('LoginFormDate'); # tatt vekk 19/05-05 for ï¿½ respektere dato pï¿½ bilag og ikke login dato
        $query_vat  = "select Percent, VatID from vat where VatID <= 62 and '$date' >= ValidFrom  and '$date' <= ValidTo and AccountPlanID > 0 and Active=1 $where order by VatID asc";
        $_lib['sess']->debug($query_vat);

        $returnhash = $_lib['storage']->get_hash(array('query' => $query_vat, 'key' => 'Percent', 'value' => 'VatID'));

        if($args['decimal'] > 0)
        {
            foreach($returnhash as $key => $value)
            {
                $tmpreturnhash[$_lib['format']->Amount(array('value'=>$key, 'return'=>'value', 'decimals'=>$args['decimal']))] = $value;
                unset($returnhash[$key]);
            }
            $returnhash = $tmpreturnhash;
        }

        #$_lib['sess']->debug(var_dump($returnhash));
        return $returnhash;
    }

    /***************************************************************************
    * Sett inn linjer i ett bilag. MVA, Hovedboksposteringer o.l.skjer automatisk
    * $post, $account, $type, $VoucherType
    * @param Examople Array
    *  (
    *      [post] => Array
    *          (
    *              [voucher_JournalID] => 179
    *              [voucher_AccountPlanID] => 1900
    *              [voucher_VoucherDate] => 2005-06-30
    *              [voucher_VoucherPeriod] => 2005-06
    *              [voucher_AmountIn] => 0.00
    *              [voucher_AmountOut] => 5000
    *              [voucher_Vat] => 25 #Will pick default if none specified
    *              [voucher_DueDate] => 2005-06-30
    *              [voucher_KID] =>
    *              [voucher_InvoiceID] =>
    *              [voucher_Description] =>
    *              [voucher_Active] => 1
    *              [voucher_AutomaticReason] => Manuell
    *              [voucher_VoucherType] => K
    *              [voucher_InsertedByPersonID] => 70
    *              [voucher_DisableAutoVat] => 0
    *              [voucher_EnableAutoBalance] => 0
    *          )
    *
    *      [accountplanid] => 1900
    *      [type] => first
    *      [VoucherType] => K
    *  )
    * @return VoucherID
    */
    public function insert_voucher_line($args)
    {
        global $_lib;

        $_lib['sess']->debug($args['comment']);

        $db_table = "voucher";
        $post = $args['post'];
        $type = $args['type'];

        ############################################################################################
        #Validate input
        if(strlen($args['VoucherType']) != 1) {
            $_lib['sess']->warning("Missing VoucherType to insert_voucher_line()");
            return false;
        }
        if(strlen($post['voucher_VoucherPeriod']) != 7) {
            $_lib['sess']->warning("Missing VoucherPeriod to insert_voucher_line()");
            return false;
        }
        if(!isset($post['voucher_JournalID']) || $post['voucher_JournalID'] < 0) {
            $_lib['sess']->warning("Missing JournalID to insert_voucher_line()");
            return false;
        }
        if(strlen($post['voucher_VoucherDate']) != 10) {
            $_lib['sess']->warning("Missing VoucherDate to insert_voucher_line()");
            return false;
        }
        if(!isset($args['accountplanid']) || $args['accountplanid'] < 0) {
            $_lib['sess']->warning("Missing AccountPlanID to insert_voucher_line()");
            return false;
        }

        ############################################################################################
        #Bï¿½r det vï¿½re periodesjekk her?
        $accountplan = $this->get_accountplan_object($args['accountplanid']);
        if(!$accountplan) {
          $_lib['sess']->warning("Konto: $account eksisterer ikke<br>");
        }

        ############################################################################################
        if(isset($post['voucher_ProjectID'])) {
          $fields['voucher_ProjectID']        = $post['voucher_ProjectID'];
        } else {
          $fields['voucher_ProjectID']        = $accountplan->ProjectID; #Default project
        }

        if(isset($post['voucher_DepartmentID'])) {
            $fields['voucher_DepartmentID']     = $post['voucher_DepartmentID'];
        } else {
            $fields['voucher_DepartmentID']     = $accountplan->DepartmentID; #Default department
        }

        ############################################################################################
        #Deault values
        $fields['voucher_JournalID']          = $post['voucher_JournalID'];
        $fields['voucher_VoucherPeriod']      = $post['voucher_VoucherPeriod'];
        $fields['voucher_VoucherType']        = $args['VoucherType'];
        $fields['voucher_VoucherDate']        = $post['voucher_VoucherDate'];
        $fields['voucher_DueDate']            = $post['voucher_DueDate'];
        $fields['voucher_Active']             = 1;
        $fields['voucher_AmountIn']           = $post['voucher_AmountIn'];  #Will change after this
        $fields['voucher_AmountOut']          = $post['voucher_AmountOut']; #Will change after this
        $fields['voucher_Quantity']           = $post['voucher_Quantity'];
        $fields['voucher_AutomaticReason']    = $post['voucher_AutomaticReason'];
        $fields['voucher_Description']        = $post['voucher_Description'];
        $fields['voucher_DescriptionID']      = $post['voucher_DescriptionID'];
        $fields['voucher_KID']                = $post['voucher_KID'];
        $fields['voucher_InvoiceID']          = $post['voucher_InvoiceID'];
        $fields['voucher_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
        $fields['voucher_InsertedByPersonID'] = $_lib['sess']->get_session('Datetime');
        $fields['voucher_UpdatedByPersonID']  = $_lib['sess']->get_person('PersonID');
        $fields['voucher_DisableAutoVat']     = 0;
        $fields['voucher_AutoFromWeeklySale'] = $post['voucher_AutoFromWeeklySale'];
        $fields['voucher_AddedByAutoBalance'] = $post['voucher_AddedByAutoBalance'];
        $fields['voucher_AccountPlanID']      = $args['accountplanid'];

        #Foreign currency
        if (array_key_exists('voucher_ForeignCurrencyID', $post) && array_key_exists('voucher_ForeignAmount', $post) && array_key_exists('voucher_ForeignConvRate', $post)) {
            $fields['voucher_ForeignCurrencyID'] = $post['voucher_ForeignCurrencyID'];
            $fields['voucher_ForeignAmount'] = $post['voucher_ForeignAmount'];
            $fields['voucher_ForeignConvRate'] = $post['voucher_ForeignConvRate'];
        }

        ############################################################################################
        #Vat handling
        #print "Dette er en test1<br>\n";
        $VAT = $this->vat_input_control($args['accountplanid'], $post['voucher_Vat'], $fields['voucher_VoucherDate']);

        if(!$args['NoVatCalculation']) {
            if(isset($VAT->VatID)) {
                $fields['voucher_VatID'] = $VAT->VatID;
            } else {
                unset($fields['voucher_VatID']);
            }
    
            if(isset($VAT->Percent))
            {
                $fields['voucher_Vat'] = $VAT->Percent;
            } else {
                unset($fields['voucher_Vat']);
            }
        }

        #Overstyring og spesialhï¿½ndtering
        ############################################################################################
        if($type == 'reskontro')
        {
            if(!$fields['voucher_AutomaticReason']) {
                $fields['voucher_AutomaticReason'] = "Automatisk opprettet reskontro";
            }

            $fields['voucher_AmountIn']     = $post['voucher_AmountOut'];
            $fields['voucher_AmountOut']    = $post['voucher_AmountIn'];
        }
        #####################
        elseif($type == 'resultat1') {
            #Switch sides
            $fields['voucher_AmountIn']   = $post['voucher_AmountOut'];
            $fields['voucher_AmountOut']  = $post['voucher_AmountIn'];
        }
        #####################
        elseif($type == 'resultat2' || $type == 'resultat3' || $type == 'balanse2' || $type == 'balanse3') {
            #Since we used the entire amount in balanse1 or resultat1, we zero the rest - this is just proposed vouchers

            $fields['voucher_AmountIn']   = 0;
            $fields['voucher_AmountOut']  = 0;
        }
        #####################
        elseif($type == 'balanse1') {
          #Switch sides
          $fields['voucher_AmountIn']   = $post['voucher_AmountOut'];
          $fields['voucher_AmountOut']  = $post['voucher_AmountIn'];
        }

        #####################
        #"Ny auto postering: konto: $account - type: $type, tabell: $db_table<br>";
        if(!$fields['voucher_AutomaticReason'])
        {
          $fields['voucher_AutomaticReason'] = "Automatisk opprettet postering: $type";
        }

        #turns around if value < 0
        if($fields['voucher_AmountIn'] < 0)
        {
          $fields['voucher_AmountOut'] = abs($fields['voucher_AmountIn']);
          $fields['voucher_AmountIn'] = 0;
        }
        elseif($fields['voucher_AmountOut'] < 0)
        {
          $fields['voucher_AmountIn'] = abs($fields['voucher_AmountOut']);
          $fields['voucher_AmountOut'] = 0;
        }

        $run_update = 0;
        if($fields['voucher_AmountIn'] > 0 or  $fields['voucher_AmountOut'] > 0)
        {
          if($fields['voucher_AmountIn'] > 0 and  $fields['voucher_AmountOut'] > 0)
          {
            if($fields['voucher_AmountIn'] - $fields['voucher_AmountOut'] != 0)
            {
              $run_update = 1;
            }
          }
          else
          {
            $run_update = 1;
          }
        }

        #print "<h2>For opprett linje: $VoucherID , ref: " . $fields['voucher_KID'] . " Inn: " . $fields['voucher_AmountIn'] . " Ut: " . $fields['voucher_AmountOut'] . " VatID: " . $fields['voucher_VatID'] . " Vat: " . $fields['voucher_Vat'] . "%</h2>";
        if($run_update == 1)
        {

            #print "Insert line<br>\n";
            #print_r($fields);
            #print "Args<br>\n";
            #print_r($fields);
            
            
            $VoucherID = $_lib['storage']->db_new_hash($fields, $db_table);
            #print "<h2>Opprett linje: $VoucherID , ref: " . $fields['voucher_KID'] . "</h2>";
            
            unset($fields['voucher_KID']);
            unset($fields['voucher_InvoiceID']);
            $this->voucher_to_hovedbok_auto($fields['voucher_AccountPlanID'], $fields, $VoucherID); #New. We update the hovedbok whenever we insert a new postering

            $fields['VoucherType'] = $args['VoucherType'];

            //$this->voucher_to_hovedbok_auto($args['accountplanid'], $fields, $VoucherID);

            #Insert VAT
            if(($accountplan->EnableVAT) && ($_lib['sess']->get_companydef('VATDuty') == 1) && !$args['NoVatCalculation'])
            {
              if( ($type == 'balanse1' || $type == 'resultat2' || $type == 'resultat3' || $type == 'balanse2' || $type == 'balanse3' || $type == 'reskontro' || $type == 'resultat1') )
              {
                  $post['voucher_AmountIn']     = $post['voucher_AmountOut'];
                  $post['voucher_AmountOut']    = $post['voucher_AmountIn'];
              }
              #print "Beregner MVA<br>";
              $this->sub_mva_voucher(array('action'=>'new', 'AccountPlan'=>$accountplan, 'fields'=>$fields, 'VoucherID'=>$VoucherID, 'post'=>$fields));
            }

            return $VoucherID;
        }
        return 0;
    }

    /***************************************************************************
    * Oppdater konto
    * @param
    * @return
    */
    private function update_vat_smart($args)
    {
        global $_lib;
        $_lib['sess']->debug("update_vat_smart kalt fra : " . $args['comment']);

        if($args['VoucherID']) {
            #sjekke om globalt flagg er satt for autovoucher i company tabellen.
            if($_lib['sess']->get_companydef('VATDuty') == 1)
            {
                $voucher = $this->get_voucher_object(array('voucherID'=>$args['VoucherID']));
                $this->sub_AutoVat(array('voucher'=>$voucher, 'post'=>$args['post'], 'comment' => 'Fra update_account før loop'));

                #gï¿½ i lï¿½kke til !isset($voucher->AutomaticVatVoucherID);
                while(isset($voucher->AutomaticVatVoucherID))
                {
                    #hent neste autoPostering
                    $voucher = $this->get_voucher_object(array('voucherID'=>$voucher->AutomaticVatVoucherID));
                    $_lib['sess']->debug("get_voucher_object3");

                    if (!empty($voucher)) {
                        $this->sub_AutoVat(array('voucher'=>$voucher, 'post'=>$args['post'], 'comment' => 'Fra update account i loop'));
                    }
                }
            } else {
                $_lib['sess']->debug("Kunde er ikke MVA pliktig");
            }
        } else {
            print "<h1>ERRROR: VoucherID missing to update_account</h1>";
        }
        #$this->false = true;
    }

    /***************************************************************************
    * Find lines with empty credit and debit sides and delte them
    * @param JournalID, VoucherType
    * @return
    */
    public function delete_credit_debit_zero_lines($JournalID, $VoucherType) {
        global $_lib;

        $sql_voucher            = "select VoucherID from voucher where JournalID='$JournalID' and VoucherType='$VoucherType' and AmountIn=0 and AmountOut=0 and Active=1";
        $_lib['sess']->debug($sql_voucher);
        $result_voucher         = $_lib['db']->db_query($sql_voucher);
        while($voucher = $_lib['db']->db_fetch_object($result_voucher)){
            $this->delete_voucher_line_smart($voucher->VoucherID, $JournalID, $VoucherType, 'delete_credit_debit_zero_lines');
        }
    }

    private function delete_auto_vat($VoucherID, $JournalID, $VoucherType) {
        global $_lib;
        ###########################################
        # Delete posible voucherlines connected to this line.
        $query           = "select AutomaticVatVoucherID from voucher where VoucherID=" . (int) $VoucherID . " and JournalID=" . (int) $JournalID . " and VoucherType='" . $VoucherType . "' and Active=1";
        $_lib['sess']->debug("utenfor: $query");
        $voucherline     = $_lib['storage']->get_row(array('query' => $query));
        $VoucherIDtmp    = $voucherline->AutomaticVatVoucherID;

        while($VoucherIDtmp > 0)
        {
            $query = "select VoucherID, AutomaticVatVoucherID from voucher where VoucherID=" . $voucherline->AutomaticVatVoucherID . " and JournalID=" . (int) $JournalID . " and VoucherType='" . $VoucherType . "' and Active=1";
            $_lib['sess']->debug("while: $query");
            $voucherline = $_lib['storage']->get_row(array('query' => $query));

            $VoucherIDtmp = $voucherline->AutomaticVatVoucherID;
            if($voucherline->VoucherID > 0)
                $this->delete_voucher_line($voucherline->VoucherID, $JournalID, $VoucherType, 'delete_auto_vat');
        }
    }

    public function delete_auto_weeklysale($WeeklySaleID, $JournalID, $VoucherType) {
        global $_lib;
        ###########################################
        # Delete posible voucherlines connected to this line.
        $query = "select * from voucher where AutoFromWeeklySale=".$WeeklySaleID." and VoucherType='".$VoucherType."' and JournalID=" . (int) $JournalID . " and Active=1";
        $_lib['sess']->debug($query);

        while($voucherline     = $_lib['storage']->get_row(array('query' => $query)))
        {
            $this->delete_voucher_line($voucherline->VoucherID, $JournalID, $VoucherType, 'delete_auto_weeklysale');
        }
    }

    /***************************************************************************
    * Automatisk opprettet MVA
    * @param
    * @return
    */
    private function sub_AutoVat($args)
    {
        global $_lib;
        
        #print "sub_AutoVat args[voucher]\n"; print_r($args['voucher']); print "\n###############################\n";
        $voucher = $args['voucher'];

        #print "<h2>sub_AutoVat: VoucherID: $voucher->VoucherID, comment: " . $args['comment'] . "</h2><br>\n";
        #print "<h2>sub_AutoVat args[voucher]</h2>\n"; print_r($args); print "\n###############################\n";

        if($voucher->DisableAutoVat != 1) #ja, vi skal utfï¿½re AutoVat pï¿½ denne posteringen.
        {
            #$_lib['message']->add(array('message' => 'sub_AutoVat for'));
            $account = $this->get_accountplan_object($voucher->AccountPlanID);
            #$_lib['message']->add(array('message' => 'sub_AutoVat etter'));

            #if($this->debug) { print "Henter kto plan: "; print_r($account) ; print "<br>\n"; }

            #Hvis MVA er overstyrt - så skal vi godta dette og sjekke ihht til det.
            #print "<h2>sub_AutoVat voucher - sjekk etter overstyring?</h2><br>\n";
            #print "EnableVAT: $account->EnableVAT, Vat: $voucher->Vat, VatID: $voucher->VatID, AutomaticVatVoucherID: $voucher->AutomaticVatVoucherID<br>\n";
            #print_r($voucher);

            #$voucher->Vat == 0 og det er lov å overstyre.?
            if($account->EnableVAT == 1 && $voucher->Vat > 0) #sjekker om det er aktivert MVA på denne konto og at det er definert en MVA sats (Nytt)
            {
                #if($this->debug) print "det er moms pï¿½ denne kontoen<br>\n";
                if($voucher->AutomaticVatVoucherID) #sjekker om det eksisterer autoposteringer
                {
                    $_lib['sess']->debug("det er autoposteringer pï¿½ denne linjen AutomaticVatVoucherID: $voucher->AutomaticVatVoucherID");
                    #print "get_voucher_object4\n";
                    $voucherAUTO = $this->get_voucher_object(array('voucherID'=>$voucher->AutomaticVatVoucherID));

                    if($voucherAUTO->DisableAutoVat != 1) #sjekker om dette er autopostering, hvis sï¿½ er det moms posteringer
                    {
                        #innsett moms posteringer
                        #print "<h2>Ny MVA postering mot VoucherID: $voucher->VoucherID, Vat: $voucher->Vat</h2><br>\n";
                        $this->sub_mva_voucher(array('action'=>'new', 'VoucherID'=>$voucher->VoucherID, 'AccountPlan'=>$account, 'post'=>$args['post'], 'voucher'=>$voucher));
                    }
                    else
                    {
                        #oppdater moms posteringer
                        #print "<h2>Oppdater MVA postering; voucherAUTO:</h2>";
                        // print_r($voucherAUTO) . "<br>\n";
                        $this->sub_mva_voucher(array('action'=>'update', 'voucher'=>$voucher, 'voucherAV'=>$voucherAUTO, 'AccountPlan'=>$account, 'post'=>$args['post']));
                    }
                }
                else
                {
                    #ny moms postering
                    #print "<h2>Ny2 MVA postering mot: VoucherID: $voucher->VoucherID VAT: $voucher->Vat, AutomaticVatVoucherID: $voucher->AutomaticVatVoucherID</h2><br>\n";
                    $this->sub_mva_voucher(array('action'=>'new', 'VoucherID'=>$voucher->VoucherID, 'AccountPlan'=>$account, 'post'=>$args['post'], 'voucher'=>$voucher));
                    #print "<h2>Ferdig MVA postering mot: VoucherID: $voucher->VoucherID</h2><br>\n";
                }
            }
            else
            {
                #det er ikke moms pï¿½ denne kontoen
                #print "det er ikke moms på denne kontoen eller MVA manuelt overstyrt, slett posteringer<br>\n";
                if($voucher->AutomaticVatVoucherID)
                {
                    #print "get_voucher_object5\n";
                    $voucherAUTO = $this->get_voucher_object(array('voucherID'=>$voucher->AutomaticVatVoucherID));
                    if($voucherAUTO->DisableAutoVat == 1)
                    {
                        #slette moms posteringer
                        $this->sub_mva_voucher(array('action'=>'delete', 'voucher'=>$voucher, 'voucherAV'=>$voucherAUTO));
                    }
                }
            }
        }
    }

    /***************************************************************************
    * sub_mva_voucher: Lag MVA til postering
    * @param
    * @return
    */
    private function sub_mva_voucher($args)
    {
        global $_lib;
        
        unset($args['post']['voucher_Quantity']); #Quantity should bever be updated on a vat line.
        unset($args['post']['voucher_ProjectID']); #Quantity should bever be updated on a vat line.
        unset($args['post']['voucher_DepartmentID']); #Quantity should bever be updated on a vat line.

        #print "Finnes VATID fra forrige postering her<br>\n";
        #print_r($args);
        
        $_lib['sess']->debug('');

        if((!isset($args['post']['voucher_AmountIn']) || $args['post']['voucher_AmountIn'] <= 0) && (!isset($args['post']['voucher_AmountOut']) && $args['post']['voucher_AmountOut'] > 0))
            return;

        if(isset($args['VoucherID']) and strlen($args['VoucherID']) > 0)
        {
            $_lib['sess']->debug("get_voucher_object8: VoucherID:" . $args['VoucherID']);
            $args['voucher'] = $this->get_voucher_object(array('voucherID'=>$args['VoucherID']));
        }

        if($args['AccountPlan']->VatID != 30 && $args['AccountPlan']->VatID != 32 && $args['AccountPlan']->VatID != 60 && $args['AccountPlan']->VatID != 62 && $args['action'] != 'delete') #dette er avgiftftitt salg, ikke no vits og fï¿½re linjer her
        {
            ##################################
            if( ($args['action'] == 'new') || ($args['action'] == 'update') )
            {
                if($this->debug) {
                    print "New eller Update MVA postering<br>\n";
                    #print_r($args);
                }
                $fields = $args['fields'];

                # unset foreign currency fields for vat lines, as we only want them in header line
                $delete_fields = array('voucher_ForeignCurrencyID',
                                       'voucher_ForeignAmount',
                                       'voucher_ForeignConvRate');

                foreach ($delete_fields as $df) {
                    if (isset($fields[$df])) unset($fields[$df]);
                }

                $vat = $this->get_vataccount_object(array('VatID'=>$args['post']['voucher_VatID'], 'date' => $args['voucher']->VoucherDate));
                $fields['voucher_AccountPlanID']  = $vat->AccountPlanID;
                $_lib['sess']->debug("Kontoplan: $vat->AccountPlanID fra VatID" . $args['post']['voucher_VatID'] . "dato: " . $args['voucher']->VoucherDate);

                if($vat->Percent > 0)
                    $percent = $vat->Percent;
                elseif($args['post']['voucher_Vat'] > 0)
                    $percent = $args['post']['voucher_Vat'];
                elseif($fields['voucher_Vat'] > 0)
                    $percent = $fields['voucher_Vat'];

                if(isset($args['post']['voucher_AmountIn']) and $args['post']['voucher_AmountIn'] > 0)
                {
                  $fields['voucher_AmountIn'] = $_lib['convert']->Amount(($args['post']['voucher_AmountIn'] / (100 + $percent)) * $percent);
                  $fields['voucher_AmountOut'] = 0;
                }
                elseif(isset($args['post']['voucher_AmountOut']) and $args['post']['voucher_AmountOut'] > 0)
                {
                  $fields['voucher_AmountOut'] = $_lib['convert']->Amount(($args['post']['voucher_AmountOut'] / (100 + $percent))  * $percent);
                  $fields['voucher_AmountIn']  = 0;
                }

                $fields['voucher_Description']        = $args['voucher']->VatID . ':' . $percent . '%';

                unset($fields['voucher_VatID']);
                unset($fields['voucher_Vat']);
            }

            #print_r($args);

            ##################################
            if($args['action'] == 'new')
            {

                #print "New MVA postering<br>\n";
                #print_r($fields);
                if(!isset($fields['voucher_JournalID']))
                {
                    $_lib['sess']->debug("JournalID ikke satt henter fra args[voucher]");
                    $fields['voucher_JournalID']          = $args['voucher']->JournalID;
                    $fields['voucher_VoucherPeriod']      = $args['voucher']->VoucherPeriod;
                    $fields['voucher_VoucherType']        = $args['voucher']->VoucherType;
                    $fields['voucher_VoucherDate']        = $args['voucher']->VoucherDate;
                    $fields['voucher_DueDate']            = $args['voucher']->DueDate;
                    $fields['voucher_Active']             = 1;
                    $fields['voucher_DescriptionID']      = $args['voucher']->DescriptionID;
                    $fields['voucher_KID']                = $args['voucher']->KID;
                    $fields['voucher_InvoiceID']          = $args['voucher']->InvoiceID;
                    $fields['voucher_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
                    $fields['voucher_InsertedByPersonID'] = $_lib['sess']->get_session('Datetime');
                    $fields['voucher_UpdatedByPersonID']  = $_lib['sess']->get_person('PersonID');
                    $fields['voucher_AddedByAutoBalance'] = 0;
                }

                $fields['voucher_ProjectID']        = $args['voucher']->ProjectID;
                $fields['voucher_DepartmentID']     = $args['voucher']->DepartmentID;

                $fields['voucher_AutomaticReason'] = "Automatisk opprettet MVA postering $fields[voucher_Vat]";
                $fields['voucher_DisableAutoVat'] = 1; #setter disable autovat til true, fordi dette er en autovat linje

                if(!$fields['voucher_JournalID']) {
                    print "ERROR: Bilagsnummer har ikke lov til ï¿½ vï¿½re 0<br>\n";
                }

                if($fields['voucher_AmountIn'] > 0 or  $fields['voucher_AmountOut'] > 0) {
                    #print "Oppretter ny VAT<br>\n";
                    $VoucherID = $_lib['storage']->db_new_hash($fields, 'voucher');
                } else {
                    #print "MVA belï¿½p ikke funnet<br>\n";
                }

                #setter link fra posteringslinje til automoms posteringslinje
                $this->update_voucher_line(array('voucher_AutomaticVatVoucherID' => $VoucherID), $args['VoucherID'], 'sub_mva_voucher1', $args['voucher']->VoucherPeriod);

                #######################
                #Motsatt pï¿½ samme konto
                $fields['voucher_AccountPlanID'] = $args['AccountPlan']->AccountPlanID;
                $tmpIn                           = $fields['voucher_AmountIn'];
                $fields['voucher_AmountIn']      = $fields['voucher_AmountOut'];
                $fields['voucher_AmountOut']     = $tmpIn;
                $fields['voucher_AutomaticReason'] = "Automatisk opprettet MVA motpostering $fields[voucher_Vat]";

                #print "Ny MVA motsatt samme linje<br>";
                #print_r($fields);
                $VoucherID2 = $_lib['storage']->db_new_hash($fields, 'voucher');

                #setter link fra fï¿½rste mva postering til mva motpostering
                $fields2['voucher_AutomaticVatVoucherID'] = $VoucherID2;
				// use voucher period as mva period
                $this->update_voucher_line($fields2, $VoucherID, 'sub_mva_voucher2', $args['voucher']->VoucherPeriod);
            }

            ##################################
            elseif($args['action'] == 'update')
            {
                $_lib['sess']->debug("Update MVA postering");
                $fields['voucher_AutomaticReason'] = "Automatisk oppdatert MVA postering $fields[voucher_Vat]";
                $fields['voucher_DisableAutoVat'] = 1; #setter disable autovat til true, fordi dette er en autovat linje

                $fields['voucher_ProjectID']        = $args['voucher']->ProjectID;
                $fields['voucher_DepartmentID']     = $args['voucher']->DepartmentID;

                #print "Oppdater MVA postering pk VoucherID: " . $args['voucherAV']->VoucherID; print_r($fields) ; print "<br>\n";
                $this->update_voucher_line($fields, $args['voucherAV']->VoucherID, 'sub_mva_voucher3');

                #Motsatt pï¿½ samme konto
                $fields['voucher_AccountPlanID'] = $args['AccountPlan']->AccountPlanID;
                $tmpIn                           = $fields['voucher_AmountIn'];
                $fields['voucher_AmountIn']      = $fields['voucher_AmountOut'];
                $fields['voucher_AmountOut']     = $tmpIn;
                $fields['voucher_AutomaticReason'] = "Automatisk oppdatert MVA motpostering $fields[voucher_Vat]";

                #gjï¿½r ingenting med linken. lar den vï¿½re som den er
                //$fields['voucher_AutomaticVatVoucherID'] = $VoucherID;

                if($args['voucherAV']->AutomaticVatVoucherID) #hvis det eksisterer en motpostering oppdateres denne
                {
                    $fields['voucher_ProjectID']        = $args['voucher']->ProjectID;
                    $fields['voucher_DepartmentID']     = $args['voucher']->DepartmentID;

                    $_lib['sess']->debug("Oppdater MVA motpost");#print "<br>";
                    #print_r($fields);
                    $this->update_voucher_line($fields, $args['voucherAV']->AutomaticVatVoucherID, 'sub_mva_voucher4', $args['voucher']->VoucherPeriod);
                }
                else #hvis det ikke eksisterer en motpostering, lages en motpostering.
                {
                    $_lib['sess']->debug("Ny MVA motpost");
                    #generate voucher information
                    $fields['voucher_JournalID']          = $args['voucherAV']->JournalID;
                    $fields['voucher_VoucherPeriod']      = $args['voucherAV']->VoucherPeriod;
                    $fields['voucher_VoucherType']        = $args['voucherAV']->VoucherType;
                    $fields['voucher_VoucherDate']        = $args['voucherAV']->VoucherDate;
                    $fields['voucher_DueDate']            = $args['voucherAV']->DueDate;
                    $fields['voucher_Active']             = 1;
                    $fields['voucher_Description']        = $args['voucherAV']->Description;
                    $fields['voucher_DescriptionID']      = $args['voucherAV']->DescriptionID;
                    $fields['voucher_KID']                = $args['voucherAV']->KID;
                    $fields['voucher_InvoiceID']          = $args['voucherAV']->InvoiceID;
                    $fields['voucher_InsertedByPersonID'] = $_lib['sess']->get_person('PersonID');
                    $fields['voucher_InsertedDatetime']   = $_lib['sess']->get_session('Datetime');
                    $fields['voucher_UpdatedByPersonID']  = $_lib['sess']->get_person('PersonID');
                    $fields['voucher_ProjectID']          = $args['voucher']->ProjectID;
                    $fields['voucher_DepartmentID']       = $args['voucher']->DepartmentID;

                    print "Opprett ny MVA motpostering<br>";
                    #print_r($fields);
                    $voucherID = $_lib['storage']->db_new_hash($fields, 'voucher');

                    #setter link fra fï¿½rste mva postering til mva motpostering
                    $fields2['voucher_AutomaticVatVoucherID'] = $voucherID;
                    $this->update_voucher_line($fields2, $args['voucherAV']->VoucherID, 'sub_mva_voucher5');
                }
            }
        }

        ##################################
        elseif($args['action'] == 'delete')
        {
            if($args['voucherAV']) #parameteret mï¿½ vï¿½re satt for at vi skal kunne slette
            {
                $this->delete_voucher_line($args['voucherAV']->VoucherID, $args['voucher']->JournalID, $args['voucher']->VoucherType, 'sub_mva_voucher');

                $AutomaticVatVoucherID='NULL';

                while(isset($voucherAV->AutomaticVatVoucherID))
                {
                    #print "get_voucher_object6\n";
                    $voucherAV = $this->get_voucher_object(array('voucherID'=>$voucherAV->AutomaticVatVoucherID));

                    if($voucherAV->DisableAutoVat == 1)
                    {                        
                        $this->delete_voucher_line($voucherAV->VoucherID, $args['voucher']->JournalID, $args['voucher']->VoucherType, 'sub_mva_voucher2');
                    }
                }

                if($AutomaticVatVoucherID)
                {
                    #oppdatere $args['voucher']->AutomaticVatVoucherID = $AutomaticVatVoucherID;
                    $fields['voucher_AutomaticVatVoucherID'] = $AutomaticVatVoucherID; #setter link til neste auto-postering
                    $fields['voucher_Vat']      = ''; #setter vat til null pï¿½ postering
                    $fields['voucher_VatID']    = ''; #setter vat til null pï¿½ postering

                    $this->update_voucher_line($fields, $args['voucher']->VoucherID, 'sub_mva_voucher6', $args['voucher']->VoucherPeriod);
                }
            }
        }
    }

    /***************************************************************************
    * Tilgjengelig bilagsnummer
    * @param
    * @return
    */
    public function get_voucher_object($args)
    {
        global $_lib;
        if($args['voucherID']) {
            $query = "select * from voucher where VoucherID='".$args['voucherID']."' and Active=1";
            #$_lib['sess']->debug($query);
            $row = $_lib['storage']->get_row(array('query' => $query));
            if(!$row) {
                #print "get_voucher_object voucherID " . $args['voucherID'] . "does not exist\n";
            }
        } else {
            #print "get_voucher_object missing voucherID\n";
            #$_lib['message']->add(array('message' => 'get_voucher_object missing voucherID'));
        }
        return $row;
    }

    /***************************************************************************
    * Tilgjengelig bilagsnummer
    * @param JournalID, VoucherType
    * @return JournalID, VoucherType, VoucherDate, VoucherPeriod for a given journal
    */
    public function get_journal_head_data($args)
    {
        global $_lib;
        if($args['JournalID'] && $args['VoucherType']) {
            $query = "select JournalID, VoucherType, VoucherDate, VoucherPeriod from voucher where JournalID='" . $args['JournalID'] . "' and VoucherType='" . $args['VoucherType']."' and Active=1 limit 1";
            $_lib['sess']->debug($query);
            $row = $_lib['storage']->get_row(array('query' => $query));
            if(!$row && empty($args['is_delete'])) {
                $_lib['message']->add("Bilag " . $args['JournalID'] . " eksisterer ikke\n");
            }
        } else {
            $_lib['sess']->debug("get_journal_head_data missing JournalID, VoucherType");
        }
        return $row;
    }

    public function get_voucher_hash($args)
    {
        global $_lib;
        $query = "select * from voucher where VoucherID='".$args['voucherID']."' and Active=1";
        $_lib['sess']->debug($query);
        return $_lib['storage']->get_row(array('query' => $query));
    }

    /***************************************************************************
    * Tilgjengelig bilagsnummer
    * @param
    * @return
    */
    private function get_currency_object($currency) {
        global $_lib;
        if(!$currency) {
          $currency = exchange::getLocalCurrency();
        }
  
        $query_currency  = "select * from exchange where Currency='$currency'";
        $_lib['sess']->debug($query_currency);
        return $_lib['storage']->get_row(array('query' => $query_currency));
    }

    /***************************************************************************
    * Find which Accountplan is hovedbok to this accountplanid
    * @param $AccountPlanID
    * @return $Hovedbok object
    */
    function getHovedbokToAccount($AccountPlanID)
    {
        if (is_object($AccountPlanID) && !empty($AccountPlanID->AccountPlanID)) {
            $AccountPlanID = $AccountPlanID->AccountPlanID;
        }
        global $_lib;
    
        $query_plan = "select * from accountplan where AccountPlanID = '$AccountPlanID'";
        $_lib['sess']->debug($query_plan);
        $reskontroO = $_lib['storage']->get_row(array('query' => $query_plan));
    
        $query_plan = "select * from accountplan where Active=1 and EnableReskontro=1 and ReskontroAccountPlanType='" . $reskontroO->AccountPlanType . "'";
        $_lib['sess']->debug($query_plan);
        return $_lib['storage']->get_row(array('query' => $query_plan));
    }

    /***************************************************************************
    * Motkonto
    * @param
    * @return
    */
    public function voucher_to_hovedbok_auto($AccountPlanID, $field, $VoucherID) {
        global $_lib;

        if(!$VoucherID || !$field['voucher_JournalID'] || !$field['voucher_VoucherType']) {
            $_lib['sess']->warning("Mangler input til voucher to hovedbok: AccountPlanID: #$AccountPlanID#, VoucherID: #$VoucherID#, voucher_JournalID: #" . $field['voucher_JournalID'] . '# x type: #' . $field['voucher_VoucherType']);
        }

        #print "Hovedbok autoposteringer: Postering: $VoucherID, Konto: $AccountPlanID<br>\n";
        if($this->is_reskontro($AccountPlanID))
        {
          #print "Det er en reskontro: $AccountPlanID<br>\n";
          #This is a reskontro - find out which hovedbokskonto it belongs to
  
          $query_voucher    = "select vat.Percent, V.VatID from voucher as V, vat where V.VoucherID='$VoucherID' and V.VatID=vat.VatID and V.Active=1";
          $_lib['sess']->debug($query_voucher);
          $voucher          = $_lib['storage']->get_row(array('query' => $query_voucher));
  
          $hovedbok = $this->getHovedbokToAccount($AccountPlanID);
  
          unset($field['voucher_Vat']);
          unset($field['voucher_VatID']);
  
          if($hovedbok->AccountPlanID)
          {

            if(!isset($this->voucher_to_hovedbok[$VoucherID])) {
                #print "Vi fant hovedboka: $hovedbok->AccountPlanID<br>\n";
                #$query_mot = "select VoucherID from voucher where AutomaticFromVoucherID='$VoucherID' and AccountPlanID='$hovedbok->AccountPlanID' and JournalID='".$field['voucher_JournalID']."' and VoucherType='".$field['VoucherType']."'"; #Feiler hvis den har med kto. Kunne evt brukt OldAccountPlanID
                $query_mot = "select VoucherID from voucher where AutomaticFromVoucherID='$VoucherID' and JournalID='".$field['voucher_JournalID']."' and VoucherType='".$field['voucher_VoucherType']."' and Active=1";
                $_lib['sess']->debug($query_mot);

                $this->voucher_to_hovedbok[$VoucherID] = $hovedbok->AccountPlanID; #Update cache

                #print "$query_mot<br>\n";
                $mot       = $_lib['storage']->get_row(array('query' => $query_mot));

                #print "ID: $hovedbok->AccountPlanID, mot: $mot->VoucherID<br>";
                #We only continue if the reskontro has a assigned hovedbok.
                $field['voucher_AccountPlanID']             = $hovedbok->AccountPlanID;
                $field['voucher_AutomaticFromVoucherID']    = $VoucherID;
                $field['voucher_DisableAutoVat']            = 1;
                $field['voucher_AutomaticReason']           = "Automatisk oppdatert hovedboks postering fra kto: $AccountPlanID Postering: $VoucherID";
                $field['voucher_UpdatedByPersonID']         = $_lib['sess']->get_person('PersonID');

                #print "Automatisk oppdatert hovedboks postering fra kto: $AccountPlanID Postering: $VoucherID<br/>\n";

                if($mot->VoucherID > 0)
                {
                    #We update an existing hovedboks entry, should be similar to updated postering
                    $this->update_voucher_line_smart($field, $mot->VoucherID, 'voucher_to_hovedbok_auto1');
                }
                else
                {
                    #We insert a new hovedboks entry
                    #Det er en reskontro. Reskontroen har denne hovedboken. Fï¿½r postering mot denne hovedboken automatisk
                    $field['voucher_AutomaticFromVoucherID'] = $VoucherID;
                    #print "Automatisk opprettet hovedboks postering<br>\n";
                    #print_r($field);
                    $hovedboknew = $_lib['storage']->db_new_hash($field, 'voucher');

                    $this->set_accountplan_usednow($field['voucher_AccountPlanID']);

                    if(isset($voucher->VatID))
                    {
                        //print $voucher->VatID." - ".$field['voucher_AmountIn']." - ".$field['voucher_AmountOut']." - ".$hovedbok->AccountPlanID;
                        //print $voucher->Percent." - ".$field['voucher_AmountIn']." - ".$field['voucher_AmountOut'];

                        if($field['voucher_AmountIn'] > 0)
                        {
                            $field['voucher_AmountOut'] = $field['voucher_AmountIn'] / ($voucher->Percent / 100 + 1) * ($voucher->Percent / 100);
                            $field['voucher_AmountIn'] = 0;
                        }
                        elseif($field['voucher_AmountOut'] > 0)
                        {
                            $field['voucher_AmountIn'] = $field['voucher_AmountOut'] / ($voucher->Percent / 100 + 1) * ($voucher->Percent / 100);
                            $field['voucher_AmountOut'] = 0;
                        }

                        $field['voucher_AutomaticReason'] = "Automatisk opprettet hovedboks mva postering";
                        $field['voucher_AutomaticFromVoucherID'] = $hovedboknew;
                        #print "Automatisk opprettet hovedboks mva postering<br>\n";
                        #print_r($field);
                        $_lib['storage']->db_new_hash($field, 'voucher');
                    }
                }
            } else {
                #print "<h2>Denne linjen er allerede poster mot hovedbok: $VoucherID</h2>";
            }
        }
        else
        {
          #print "hovedbok->AccountPlanID not set<br>";
        }
      }
      else
      {
        #dette er ikke noen reskontro. sï¿½ vi mï¿½ sjekke om det finnes hovedboksposteringer mot denne linjen fra fï¿½r
        #lagt til: 22/10-04
        #print "Det er ikke en reskontro: $AccountPlanID<br>\n";

        $query = "select VoucherID from voucher where AutomaticFromVoucherID='$VoucherID' and Active=1";
        $_lib['sess']->debug($query);
        $old = $_lib['storage']->get_row(array('query' => $query));

        if(isset($old->VoucherID) or strlen($old->VoucherID))
        {
            $this->delete_voucher_line($old->VoucherID, $field['voucher_JournalID'], $field['voucher_VoucherType'], 'voucher_to_hovedbok_auto');
        }
      }
      #print "Ferdig hovedbok auto: $message<br>\n";
    }

    /***************************************************************************
    * Oppdater hovedboksmotkontoer for resultat og balanse. Oftest kto 2090 og 8800
    * @param post[voucher_VoucherPeriod, AccountPlanID or AccountPlanType, AccountAuto]
    * @return
    */
    public function update_motkonto($args)
    {
        global $_lib;

        $fieldsbalance = array();

        $post        = $args['post'];
        $VoucherType = 'A';

        unset($post['voucher_JournalID']);
        $periodexist = $period = $post['voucher_VoucherPeriod'];

        if(strlen($periodexist) != 7 || $periodexist == '0000-00') {
            $_lib['message']->add(array('message' => "VoucherPeriod wrong in function update_motkonto: #$periodexist#. Aborting update"));
            return;
        }

        #if(strlen($post['voucher_VoucherDate']) > 0)
        #    $fieldsbalance['voucher_VoucherDate'] = $post['voucher_VoucherDate'];
        #else
        #We always want this date to be in this period. we have seen errors when date was in another period

        ########################################################################
        #Get balanse

        if(!isset($args['Amount']) || $args['Amount'] == 0) {
            if($args['AccountPlanID'])
            {
                $sql_saldo = "select sum(AmountIn) as AmIn, sum(AmountOut) as AmOut from voucher where AccountplanID = '".$args['AccountPlanID']."' and VoucherPeriod='$period' and VoucherType != 'A' and Active=1"; // and BalanceOk = 1
            }
            elseif($args['AccountPlanType'])
            {
                $sql_saldo = "select sum(v.AmountIn) as AmIn, sum(v.AmountOut) as AmOut from voucher as v, accountplan as a where v.AccountPlanID=a.AccountPlanID and a.AccountPlanType = '" . $args['AccountPlanType'] . "' and  v.VoucherPeriod='$period' and v.VoucherType != 'A' and v.Active=1";  } //and BalanceOk = 1
            else
            {
                print "Missing AccountPlanID or AccountPlanType ";
            }

            $_lib['sess']->debug("Beregning av saldo til automatisk hovedboksmotkonto: $sql_saldo");

            $saldo = $_lib['storage']->get_row(array('query' => $sql_saldo));
            $sum_balance = round($saldo->AmIn - $saldo->AmOut, 2);
            $_lib['sess']->debug("<b>Balanse sporring: $sql_saldo<br>\nsum:$sum_balance = in:$saldo->AmIn - out:$saldo->AmOut</b>");
        } else {
            #Tallet er ferdig beregnet fra forrige runde, s? da bruker vi det.
            #Ny tilpasning. TE 2006-05-26
            $sum_balance = $args['Amount'];
        }

        ########################################################################
        #Check if balanse motkonto exists (returns changed)
        if($sum_balance >= 0)
        {
            $fieldsbalance['voucher_AmountIn'] = 0;

            $hash = $_lib['convert']->Amount(array('value'=> abs($sum_balance)) );
            $fieldsbalance['voucher_AmountOut'] = $hash['value'];
            $tmp = $hash['error'];

            $message .= $tmp;
        }
        else
        {
            $hash = $_lib['convert']->Amount(array('value'=>abs($sum_balance)));
            $fieldsbalance['voucher_AmountIn'] = $hash['value'];
            $tmp = $hash['error'];

            $fieldsbalance['voucher_AmountOut'] = 0;
            $message .= $tmp;
        }

        ########################################################################
        #Finn  prim?rn?kkel for oppdastering av motkonto.
        $primarykeybalance['AccountPlanID']   = $args['AccountAuto'];
        $primarykeybalance['VoucherType']     = $VoucherType;
        $primarykeybalance['VoucherPeriod']   = $post['voucher_VoucherPeriod']; #Should use period here

        $sql_balance_check    = "select VoucherID from voucher where AccountPlanID='".$args['AccountAuto']."' and VoucherPeriod='$periodexist' and VoucherType='$VoucherType' and Active=1";
        $_lib['sess']->debug($sql_balance_check);
        $row_balance          = $_lib['storage']->get_row(array('query' => $sql_balance_check));
        $_lib['sess']->debug("row_balance $sql_balance_check");

        $fieldsbalance['voucher_VoucherPeriod']       = $post['voucher_VoucherPeriod']; #Always send voucher period to pass period security check, even if we dont have to update it.
        ########################################################################
        if($row_balance->VoucherID)
        {
            $_lib['sess']->debug("Oppdater periodevismotkonto");
            $this->update_voucher_line($fieldsbalance, $row_balance->VoucherID, "Updated from update_motkonto ");
        }
        else
        {
            #Oppretter ny postering, da den ikke fantes fra f?r.
            #Get the fields we need, could be a more fancy hash map
            #Opprett voucher id, returner den ogsï¿½.
  
            if(isset($args['JournalID']))
            {
                $fieldsbalance['voucher_JournalID'] = $args['JournalID'];
            } else {
                list($fieldsbalance['voucher_JournalID'], $message) = $this->get_next_available_journalid(array('available' => true, 'update' => true, 'type' => $VoucherType));
            }
  
            $fieldsbalance['voucher_BalanceOk']           = 1;
            $fieldsbalance['voucher_VoucherType']         = $VoucherType;
            $fieldsbalance['voucher_VoucherDate']         = $post['voucher_VoucherPeriod'] . "-01";

            $fieldsbalance['voucher_Active']              = 1;
            $fieldsbalance['voucher_AddedByAutoBalance']  = 0;
            #$fieldsbalance['voucher_DisableAutoVat']     = 0; #mï¿½ jeg ha denne her?
  
            #This period postering does not exist, insert it
            $fieldsbalance['voucher_AutomaticReason']     = $args['Reason'];
            $fieldsbalance['voucher_AccountPlanID']       = $args['AccountAuto'];
            $_lib['sess']->debug("Setter inn periodevis motkonto");
            $this->insert_voucher_line(array('post' => $fieldsbalance, 'accountplanid' => $fieldsbalance['voucher_AccountPlanID'], 'VoucherType' => $VoucherType));
        }

        return array($fieldsbalance['voucher_JournalID'], $fieldsbalance['voucher_AmountIn'] - $fieldsbalance['voucher_AmountOut']);
    }

    /***************************************************************************
    * Update voucher head: date and period
    * @param
    * @return
    */
    public function update_voucher_head($voucher_input) {
        global $_lib;

        if($this->is_valid_accountperiod($voucher_input->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) {

            #print "Oppdaterer hodet<br>\n";
            #print_r($voucher_input);
            $query_voucher  = "select* from voucher where VoucherID='" . $voucher_input->VoucherIDOld . "'";
            $_lib['sess']->debug($query_voucher);
            $old            = $_lib['storage']->get_row(array('query' => $query_voucher));
    
            #Merk at det er viktig at dette kommer tidlig
            #Should probably update motkonto balanse and result for old and new period?
    
            $fieldshead['voucher_VoucherDate']      = $voucher_input->VoucherDate;
            $fieldshead['voucher_VoucherPeriod']    = $voucher_input->VoucherPeriod;
            $fieldshead['voucher_UpdatedByPersonID']= $_lib['sess']->get_person('PersonID');
    
            #$fieldshead['voucher_JournalID']        = $voucher_input->JournalID; #Eller skal dette v?re lov
    
            $primarykeyhead['JournalID']    = $voucher_input->JournalID;
            $primarykeyhead['VoucherType']  = $voucher_input->VoucherType;
            $_lib['db']->db_update_hash($fieldshead, 'voucher', $primarykeyhead);
            
            ########################################
            #Oppdater balanse og resultat    
            $post = array();
            $post['voucher_VoucherPeriod'] = $voucher_input->VoucherPeriod;
            $this->set_journal_motkonto(array('post' => $post));
            
            #Should probably look if accountplan / period is changed from befor - and update both the new and previous correctly
            
            #########################################
            #Update line values
            $this->update_voucher_line_smart($voucher_input->request('voucher_new'), $voucher_input->VoucherIDOld, 'update_voucher_head');
            
            ########################################
            #oppdatere motkontoer hvis vi har byttet periode
            if($old->VoucherPeriod != $voucher_input->VoucherPeriod){
                #print "Periode er endret: Oppdaterer motkonto på den gamle perioden<br>\n";
                $post = array();
                $post['voucher_VoucherPeriod']      = $old->VoucherPeriod;
                $post['voucher_VoucherDate']        = $old->VoucherDate;
                $this->set_journal_motkonto(array('post'=>$post));
        
                $post = array();
                $post['voucher_VoucherPeriod']      = $voucher_input->VoucherPeriod;
                $post['voucher_VoucherDate']        = $voucher_input->VoucherDate;
                $this->set_journal_motkonto(array('post'=>$post));
        
                $_lib['message']->add(array('message' => "Periode er byttet. Kalkuler forrige periode på nytt"));
            }        
            ########################################
            #Ferdig
        } else {
            print "Ikke lov &aring; endre data i en stengt periode i bilags hodet<br>\n";
        }
    }

    public function update_voucher_line_smart($fields, $VoucherID, $comment) {
        global $_lib;

        $_lib['sess']->debug("Oppdaterer linjen fra $comment");
        if(!$VoucherID) {
            $_lib['sess']->warning("Internal error: Missing VoucherID to update_voucher_line_smart");
            return;
        }

        unset($fields['voucher_VoucherID']); #If voucher id is set, we risk that we change it. This will prevent voucherid change it permanently.

        #Find the old values of this line. Could have checked for active.........
        $voucher = $this->get_voucher_object(array('voucherID' => $VoucherID));

        #Find the accountplan we are talking about
        $accpl = $this->get_accountplan_object($fields['voucher_AccountPlanID']);
        ########################################

        $sum = $fields['voucher_AmountIn']  + $fields['voucher_AmountOut'];

        /**************************************************************************/
        #print "Kid match: " . $_REQUEST['voucher_KID'] . 'AutoKid: ' .  $voucher_line->AutoKID . 'kto enable kid:' . $accountplan->EnablePostPost;
        if(isset($fields['voucher_KID']) && $fields['voucher_KID'] > 0 && !$voucher->AutoKID) {
            #Det ma vare en kid oppgitt, vi maa ikke ha funnet kid match tidligere og kontoen vi f maa ha kid sto skrudd
            #We only do if if we have a KID, and the voucher line is not automatically updated from this KID before.
            #print "Kid oppgitt og kontoen st√Ø¬ø¬Ωtter kid<br>\n";
            if(!$fields['voucher_AccountPlanID'] || !$sum) {
                #print "findOpenPostKid2: VoucherID: $VoucherID<br>\n";
                list($status, $refH) = $this->postmotpost->findOpenPostKid($fields['voucher_KID'], $VoucherID, $fields['voucher_AccountPlanID']);
                #print "Status finn KID: $status<br>";
                if($status) {
                    #Exactly one KID ref match, update the voucher AmountIn, AmountOut and Accountplan ID only if not updated before.
                    list($KIDAccountPlanID, $KIDAmountIn, $KIDAmountOut, $KIDJournalID, $KIDVoucherID, $KIDstatus) = $this->postmotpost->getKIDInfo($refH); #finn linje som har en reskontro (det m√Ø¬ø¬Ω v√Ø¬ø¬Ωre kunde)
                    #print "Status finn KIDInfo: $KIDstatus<br>\n";
                    if($KIDstatus) {
                        $fields['voucher_AccountPlanID']    = $KIDAccountPlanID;
                        $fields['voucher_AmountIn']         = 0;
                        $fields['voucher_AmountOut']        = 0;

                        if($KIDAmountIn > 0)  $fields['voucher_AmountIn']   = $KIDAmountIn;
                        if($KIDAmountOut > 0) $fields['voucher_AmountOut']  = $KIDAmountOut;

                        $fields['voucher_AutoKID']           = 1; #Information updated automatically from KID information
                        $fields['voucher_AutomaticReason']   = "Automatisk fra KID: " . $voucher_input->KID;
    
                        #print "Setting voucher values AccountPlanID:$KIDAccountPlanID,  AmountIn:$KIDAmountIn, AmountOut:$KIDAmountOut, KIDJournalID:$KIDJournalID, KIDVoucherID:$KIDVoucherID<br />";
                    }
                }
            }
        }

        ########################################
        #sjekke om det er moms p√Ø¬ø¬Ω denne kontoen
        #print "<b>update_voucher_line2: $VoucherID fra $comment VAT: " . $fields['voucher_Vat'] . " VatID: " . $fields['voucher_VatID'] . "</b><br>\n";

        if($accpl->EnableVAT == 1) {
            #print "running: vat_line_update<br>\n";
            list($fields['voucher_VatID'], $fields['voucher_Vat'], $vatreason) = $this->vat_line_update($accpl, $voucher, $fields);
        } else {
            $fields['voucher_VatID']   = '';
            $fields['voucher_Vat']     = '';
            $vatreason = 'Kto plan uten VAT';
        }

        if($fields['voucher_Vat'] != $voucher->Vat && !$fields['voucher_Vat']) {
            #VI sletter VAT linjer hvis VAT=0 og den er endret siden sist
            $this->delete_auto_vat($VoucherID, $fields['voucher_JournalID'], $fields['voucher_VoucherType']);
            $vatreason = 'Sletter VAT pga at den er endret til tom';
        }

        #print "<h2>update_voucher_line2: $VoucherID fra $comment, reason: #$vatreason# VAT: " . $fields['voucher_Vat'] . " VatID: " . $fields['voucher_VatID'] . "</h2>\n";

        ########################################
        #Deault values project and department. New functionality AT 2004-10-15
        #generate voucher information
        if($fields['voucher_AccountPlanID'] != $voucher->AccountPlanID)
        {
            $_lib['message']->add(array('message' => 'Default prosjekt og avdeling satt pga bytte av kontoplan'));
            $fields['voucher_ProjectID']    = $accpl->ProjectID;    #Default project
            $fields['voucher_DepartmentID'] = $accpl->DepartmentID; #Default department
    
            $this->delete_auto_vat($VoucherID, $fields['voucher_JournalID'], $fields['voucher_VoucherType']);
            $this->postmotpost->openPost($VoucherID);
        }

        ########################################
        #Åpne postene hvis beløpet endrer seg
        if($fields['voucher_AmountIn'] != $voucher->AmountIn || $fields['voucher_AmountOut'] != $voucher->AmountOut) {
            $this->postmotpost->openPost($VoucherID);
        }

        $this->update_voucher_line($fields, $VoucherID, $comment);
        
        $this->update_vat_smart(array('VoucherID'=>$voucher->VoucherID, 'post'=> $fields, 'comment' => 'Called from: update_voucher_line_smart'));
    }
    
    #This is the function that actually carries out the dirty work. Can not be called from outside this library
    #Will not check or update anything - it i the smart version that is for normal usage.
	# $check_only_period is a period which is supplied for allowing a period check to go through without
	# actually updating the voucherPeriod (i.e. without having voucherPeriod in fields array). 
    # This was added as a quick solution to avoid unintended 
	# consequences when fixing
    private function update_voucher_line($fields, $VoucherID, $comment, $check_only_period = false) {
        global $_lib;

        if ($this->is_valid_accountperiod($fields['voucher_VoucherPeriod'], $_lib['sess']->get_person('AccessLevel')) || 
			($check_only_period && $this->is_valid_accountperiod($check_only_period, $_lib['sess']->get_person('AccessLevel')))) {
    
            unset($fields['voucher_VoucherID']); #If voucher id is set, we risk that we change it. This will prevent voucherid change it permanently.
            $fields['voucher_UpdatedByPersonID']  = $_lib['sess']->get_person('PersonID');
    
            ########################################
            #Run the update in the database
            $primarykey['VoucherID'] = $VoucherID;
    
            $this->set_accountplan_usednow($fields['voucher_AccountPlanID']);
    
            #print_r($fields);
            #print "<br>\n<b>update_voucher_line - finished: $VoucherID AmountIn: " . $fields['voucher_AmountIn'] . ", AmountOut: " . $fields['voucher_AmountOut'] . ", Vat: " . $fields['voucher_Vat'] . ", VatID: " . $fields['voucher_VatID'] . "</b><br>\n";
            $_lib['storage']->db_update_hash($fields, 'voucher', $primarykey);        
        } else {
            print "Ikke lov &aring; oppdatere en bilagslinje i en stengt periode: " . $fields['voucher_VoucherPeriod'] . " - Linjenummer: $VoucherID - kommentar: $comment<br>\n";
        }
    }

    /***************************************************************************
    * sjekker om balansen til bilaget er 0
    * hvis ikke sï¿½ legger funksjonen til en ny linje hvor den foreslï¿½r motpostering
    * @param
    * @return
    */
    public function correct_journal_balance($fields, $JournalID, $VoucherType) {
        global $_lib;
        #print "<b>Korriger balanse automatisk</b><br>";

        #$fields['voucher_KID']         = '';
        $fields['voucher_AutoKID']            = '';#We empty kid
        $fields['voucher_UpdatedByPersonID']  = $_lib['sess']->get_person('PersonID');


        #Hvis bilagets balanse ikke er 0 blir det foreslï¿½tt en motpostering automatisk.
        $sum = $this->sum_journal($JournalID, $VoucherType);
        $absSum = abs($sum);

        #print "<h2>this->correct_journal_balance(: diff: $absSum)</h2>";

        if($absSum != 0)
        {
            ##################################################
            #Check if it exists unsaved journal entries
            $query_balance_voucher  = "select VoucherID, AmountIn, AmountOut from voucher where JournalID='$JournalID' and VoucherType='$VoucherType' and AddedByAutoBalance=1 and Active=1";
            $balance_voucher        = $_lib['storage']->get_row(array('query' => $query_balance_voucher));
            $VoucherID              = $balance_voucher->VoucherID;
            $sumbalance             = $balance_voucher->AmountIn - $balance_voucher->AmountOut;

            $sum = -$sum;
            $updatedsum             = $sum + $sumbalance; #Fix if this is 0 delete it.

            #print "ny sum: $updatedsum             = sumbilag:$sum + sumbalansepostering:$sumbalance<br>\n";

            $fields['voucher_VoucherType'] = $VoucherType;
            if($updatedsum < 0) {
              $fields['voucher_AmountOut'] = abs($updatedsum);
              $fields['voucher_AmountIn']  = 0;
            }
            elseif($updatedsum > 0) {
              $fields['voucher_AmountIn']  = $updatedsum;
              $fields['voucher_AmountOut'] = 0;
            }
            else {
              $fields['voucher_AmountIn']  = 0;
              $fields['voucher_AmountOut'] = 0;
              $fields['voucher_Active']    = 0;
              $this->delete_voucher_line_smart($VoucherID, $JournalID, $VoucherType, 'correct_journal_balance'); #Sjekk
              #print "Den er null, vi kan bare slette den<br>\n";
              #return;
            }

            #unset($fields['voucher_Description']);
            unset($fields['voucher_DescriptionID']);
            unset($fields['voucher_VoucherID']);

            $fields['voucher_AutomaticReason']      = "Automatisk balanse i bilaget fra: correct_journal_balance";

            if($VoucherID) {
                #Finnes, s? vi bare oppdaterer
                #print "auto: update<br>";

                $fields['voucher_EnableAutoBalance']    = 1;
                $fields['voucher_AddedByAutoBalance']   = 1;
                $fields['voucher_DisableAutoVat']       = 0;

                #print "correct_journal_balance: update<br>";
                #print_r($fields);
                $this->update_voucher_line_smart($fields, $VoucherID, 'correct_journal_balance');
            }
            else {
                #print "correct_journal_balance: new<br>";
                $fields['voucher_AddedByAutoBalance']   = 1;
                $fields['voucher_Active']               = 1;
                #print_r($fields);
                $VoucherID = $_lib['storage']->db_new_hash($fields, 'voucher');
                $this->set_accountplan_usednow($fields['voucher_AccountPlanID']);
            }

            #oppdatere mva linjer
            $this->update_vat_smart(array('VoucherID'=>$VoucherID, 'post'=>$fields, 'comment' => 'Called from: correct_journal_balance'));
            $this->voucher_to_hovedbok_auto($fields['voucher_AccountPlanID'], $fields, $VoucherID);
        } else {
            #print "Bilaget er i balanse.";
        }

        #Voucher is in balance. Set Balance Ok. (Default is not ok)
        $this->set_journal_balance(array('ok' => true, 'JournalID' => $JournalID, 'VoucherType' => $VoucherType));
        #print "<b>Ferdig korriger balanse automatisk</b><br>";
    }

    /***************************************************************************
    * Update VAT correctly for this line
    * @param
    * @return
    */
    private function vat_line_update($accpl, $old, $fields) {
        global $_lib;

        $VatID      = 0;
        $Vat        = 0;
        $vatreason  = 'Ur&oslash;rt';

        #print "MVA<br>\n";
        $vat = $this->get_vataccount_object(array('VatID' => $accpl->VatID, 'date' => $fields['voucher_VoucherDate']));

        #Det er alltid lov  bytte kontoer, og gjr man det skal MVA hentes fra oppsettet til kontoen man bytter til
        #Skal det vre mulig  bde endre kto og ikke endre VAT m vi i s fall kikke p hva VAT er satt opp til standard p denne kontoen og s endre den etter det.
        if($fields['voucher_AccountPlanID'] != $old->AccountPlanID)
        {
            $_lib['sess']->debug("Kontoplan byttet - default MVA endres automatisk");
            $Vat        = $vat->Percent;
            $VatID      = $vat->VatID;
            $vatreason  = "Bytte av kontoplan";
        }
        elseif($accpl->EnableVATOverride || $vat->EnableVatOverride)
        {
            $_lib['sess']->debug("Overstyr MVA");
            if($fields['voucher_Vat'] > 0) {
                $VatControlO = $this->vat_input_control($accpl->AccountPlanID, $fields['voucher_Vat'], $fields['voucher_VoucherDate']);
                $VatID      = $VatControlO->VatID;
                $Vat        = $VatControlO->Percent;
                $vatreason  = $VatControlO->VatReason;;
            }
            elseif(($fields['voucher_Vat'] == 0) and (strlen($fields['voucher_Vat']) > 0) )
            {
                $query = "select VoucherID, AutomaticVatVoucherID from voucher where VoucherID=" . (int) $old->VoucherID . " and JournalID=" . (int) $fields['voucher_JournalID'] . " and VoucherType='" . $fields['voucher_VoucherType'] . "' and Active=1";
                #print "vat slett loop: $query<br>\n";
                $hovedbok = $_lib['storage']->get_row(array('query' => $query));
                $this->update_voucher_line(array('voucher_AutomaticVatVoucherID' => 0), $old->VoucherID, 'slett loop nullstiller automatic vat', $fields['voucher_VoucherPeriod']);
                while($hovedbok->AutomaticVatVoucherID > 0)
                {
                    $VoucherIDtmp = $hovedbok->AutomaticVatVoucherID;

                    $query       = "select VoucherID, AutomaticVatVoucherID from voucher where VoucherID=" . (int) $VoucherIDtmp . " and JournalID=" . (int) $fields['voucher_JournalID'] . " and VoucherType='" . $fields['voucher_VoucherType'] . "' and Active=1";
                    #print "vat inne i loop $query<br>\n";
                    $hovedbok    = $_lib['storage']->get_row(array('query' => $query));
                   
                    #print "For sletting: VoucherID: $hovedbok->VoucherID, JournalID: " . $fields['voucher_JournalID'] . ", type: " . $fields['voucher_VoucherType'] . "<br>\n";
                    $this->delete_voucher_line_smart($hovedbok->VoucherID, $fields['voucher_JournalID'], $fields['voucher_VoucherType'], 'vat_line_update');
                }
                if($accpl->VatID < 40) {
                    $VatID        = 10;  #Fritt Salg, ikke MVA
                    $vatreason    = 'Fritt salg ikke MVA';
                    $Vat          = '';
                
                } else {
                    $VatID = 40;  #Fritt kjï¿½p, ikke MVA
                    $Vat   = '';
                    $vatreason = "Fritt kj&oslash;p ikke MVA";
                }
            }
            elseif($fields['voucher_Vat'] < 0) {
              $_lib['sess']->warning("MVA kan ikke v&aelig;re negativ");
            }
            else {
              #$vat = $this->get_vataccount_object(array('VatID'=>$accpl->VatID)); #Blir hentet hï¿½yere opp
              $VatID     = $vat->VatID;
              $Vat       = $vat->Percent;
              $vatreason = "Ikke noe";
            }

            #print "Final VatID: " . $_REQUEST['voucher_VatID'] . ', Vat: ' . $_REQUEST['voucher_Vat'] . ", $vatreason<br>\n";
        }
        #hvis det ikke er lov og oversyter vat i kontoplan, bruke mva satt pï¿½ kontoplan
        else
        {
            #$vat = $this->get_vataccount_object(array('VatID'=>$accpl->VatID)); #Blir hentet hï¿½yere opp
            #print "IKke overstyrt";
            $VatID      = $vat->VatID;
            $Vat        = $vat->Percent;
            $vatreason  = "Ikke lov  overstyre p denne kontoen";
        }

        $_lib['sess']->debug("MVA vat_line_update: $VatID, $Vat, $vatreason");

        return array($VatID, $Vat, $vatreason);
    }

    private function vat_input_control($AccountPlanID, $VatPercent, $VoucherDate) {
        global $_lib;
        $vat = new stdClass();
        
        $_lib['sess']->debug("vat_input_control: AccountPlanID: $AccountPlanID, VatPercent: #$VatPercent#, VoucherDate: $VoucherDate");
        $accountplan = $this->get_accountplan_object($AccountPlanID); #Get all info about this account
        
        if($accountplan->EnableVAT && $accountplan->EnableVATOverride && strlen($VatPercent)) {
            #If vat is enabled and override is ok and a percent is defined - check if the ovverride is valid
            #If vatpercent has a length - we assume it is manually assigned at will.
            
            $vathash      = $this->get_vatpercenthash(array('accountVatID' => $accountplan->VatID, 'date' => $VoucherDate, 'decimal' => 2));
            $VatPercent   = $_lib['format']->Amount($VatPercent); #trenger ikke (int) forran her hvis vi sender med decimal i linjen over
    
            $_lib['sess']->debug("VatOverride: OK, VatPercent: #" . $VatPercent . "# - finnes i hash?");

            if($vathash[$VatPercent])
            {
              #print "HASH treff<br>\n";
              $vat->VatID       = $vathash[$VatPercent]; #Find  MVAID basert pï¿½ hash med Prosentsatser
              $vat->Percent     = $VatPercent;
              $vat->VatReason   = "Overstyrt MVA sats $VatPercent% godkjent";
            }
            elseif($accountplan->VatID > 30)
            {
              #print "40 ja det tror jeg ja<br>\n";
              $vat->VatID       = 40; #Udefinert MVA ID
              $vat->Percent     = $VatPercent;
              $vat->VatReason   = "Udefinert Kj&oslash;p VatID";
            }
            elseif($accountplan->VatID <= 30)
            {
              $vat->VatID       = 10; #Udefinert MVA ID
              $vat->Percent     = $VatPercent;
              $vat->VatReason   = "Udefinert Salg VatID";
            }
        } elseif($accountplan->EnableVAT) {
            
            $vat = $this->get_vataccount_object(array('VatID' => $accountplan->VatID, 'date' => $VoucherDate));
            $vat->VatReason   = "MVA sats plukket fra kontoplan";

        } else {
            
            $vat->VatReason   = "Denne kontoen har ikke MVA definert";
        }

        $_lib['sess']->debug("vat_input_control finished: VatID: $vat->VatID, Percent: $vat->Percent, VatReason: $vat->VatReason");
        #print_r($vat);
        return $vat;
    }

    /***************************************************************************
    * If period is empty, it is set to to days period. New 2004-08-12 TE
    * @param
    * @return
    */
    function is_valid_accountperiod($period, $access)
    {
        global $_lib;

        if(!$period)
        {
          $period = $_lib['date']->get_this_period($_lib['sess']->get_session('Date'));
          #$_lib['sess']->warning("Period missing to $this->is_valid_accountperiod(: $period");
          #"Period missing to $this->is_valid_accountperiod(: $period";
        }

        $period = $_lib['date']->get_this_period($period);

        if($period == '0000-00') {
            #Da fï¿½r man alltid endret datoer som er feil
            return true;
        }

        if($access > 2)
        {
          $query = "select Period from accountperiod where Period='$period' and (Status=2 or Status=3) order by Period asc";
        } else {
          $query = "select Period from accountperiod where Period='$period' and Status=2 order by Period";
        }
        #print "$query<br>\n";
        $row = $_lib['storage']->get_row(array('query' => $query, 'debug' => false));

        if($row->Period)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /***************************************************************************
    * Hent siste periode, dette ï¿½ret
    * @param
    * @return
    */
    function get_last_accountperiod_this_year($date)
    {
        global $_lib;
        $year = $_lib['date']->get_this_year($_lib['sess']->get_session('Date'));
        $access = $_lib['sess']->get_person('AccessLevel');
        $query = "select Period from accountperiod where substring(Period,1,4)='$year' and (Status=2 or (Status=3 and '$access'>=3)) order by Period desc limit 1";
        $row = $_lib['storage']->get_row(array('query' => $query));
        return $row->Period;
    }

    /***************************************************************************
    * Tilgjengelig bilagsnummer
    * @param
    * @return hash with active periods
    */
    function get_open_accountperiod_hash(){
        // TODO this method should maybe take AcccessLevel into account
        global $_lib;
        $query = "select Period, 1 from accountperiod where (Status=2 or Status=3) order by Period asc";
        return $_lib['storage']->get_hash(array('query' => $query, 'key' => 'Period', 'value' => 'Period'));
    }

    /***************************************************************************
    * Find first open accounting period
    * @param
    * @return
    */
    function get_first_open_accountingperiod() {

        $PeriodH = $this->get_open_accountperiod_hash();
        return array_shift($PeriodH); # Get 
    }

    /***************************************************************************
    * Find last open accounting period
    * @param
    * @return
    */
    function get_last_open_accountingperiod() {

        $PeriodH = $this->get_open_accountperiod_hash();
        return array_pop($PeriodH); # Get 
    }

    /***************************************************************************
    * Tilgjengelig bilagsnummer
    * @param
    * @return
    */
    private function set_accountplan_usednow($AccountPlanID) {
        global $_lib;

        if(!$this->accountplan_usednow[$AccountPlanID]) {
            $this->accountplan_usednow[$AccountPlanID] = true;

            $fields['accountplan_LastUsedTime'] = "NOW()";
            $primarykey['AccountPlanID']        = $AccountPlanID;

            $_lib['storage']->db_update_hash($fields, 'accountplan', $primarykey);
        } else {
            #It is previously updated so we just ignore it
        }
    }

    /***************************************************************************
    * Defaults to not ok
    * @param
    * @return
    */
    function set_journal_balance($args) {
      global $_lib;
      $field = array();
      if($args['ok']) {
        $field['voucher_BalanceOk']   = 1;
      } else {
        $field['voucher_BalanceOk']   = 0;
      }
      $primarykey['JournalID']          = $args['JournalID'];
      $primarykey['VoucherType']        = $args['VoucherType'];

      $_lib['storage']->db_update_hash($field, 'voucher', $primarykey);
    }

    /***************************************************************************
    * Sett motkonto
    * @param $args['voucher_VoucherPeriod'], $args['voucher_VoucherDate'] + $args['VoucherType']
    * @return
    */
    function set_journal_motkonto($args)
    {
        global $_lib;
        #$args['post']['voucher_VoucherType'] = $args['VoucherType'];

        $accountresult  = $_lib['sess']->get_companydef('VoucherResultAccount');
        $accountbalance = $_lib['sess']->get_companydef('VoucherBalanceAccount');
        if($accountbalance && $accountresult) {
          list($JID, $amount) = $this->update_motkonto(array('post' => $args['post'], 'AccountPlanType' => 'balance', 'AccountAuto' => $_lib['sess']->get_companydef('VoucherBalanceAccount'), 'Reason' => 'Automatisk opprettet balanse motkonto for periode'));
          list($JID, $amount) = $this->update_motkonto(array('post' => $args['post'], 'AccountPlanType' => 'result',  'AccountAuto' => $_lib['sess']->get_companydef('VoucherResultAccount'), 'JournalID' => $JID, 'Reason' => "Automatisk opprettet resultat motkonto for periode sum fra linje $JID", 'Amount' => $amount));
        }
        else {
            #gi en feilmelding om at firmaoppsett ikke er satt
            $_lib['message']->add("Firmaoppsett ikke ok");
        }
    }

    /***************************************************************************
    * Brukt for ï¿½ ta inpiut en liste med posteringslinjenummer og lage nye posteringer av dem (pï¿½ motsatt side) pï¿½ ett nytt bilag som summeres opp basert pï¿½ disse verdiene.
    * @param post + VoucherType
    * @return
    */
    function makejournal_from_voucherid($VoucherIDS, $JournalID, $type) {

        foreach($VoucherIDS as $VoucherID) {

               $VoucherID = $this->insert_voucher_line(array('post'=>$_REQUEST, 'accountplanid'=>$_REQUEST['voucher_AccountPlanID'], 'type'=>'first', 'VoucherType'=>$VoucherType, 'comment' => 'Fra makejournal_from_voucher_id'));
        }
    }

    /***************************************************************************
    * Returnerer true hvis den oppgitte kontoen er en reskontro
    * @param AccountPlanID
    * @return
    */
    function is_reskontro($AccountPlanID) {
        global $_lib;

        if (empty($AccountPlanID)) {
            return false;
        }

        $account = $this->get_accountplan_object($AccountPlanID);

        if($account->AccountPlanType != 'balance' && $account->AccountPlanType != 'result') {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /***************************************************************************
    * Returnerer true hvis den oppgitte kontoen er en reskontro
    * @param AccountPlanID
    * @return
    */
    function is_hovedbok($AccountPlanID) {
        global $_lib;

        $account = $this->get_accountplan_object($AccountPlanID);
        if($account->AccountPlanType == 'balance' || $account->AccountPlanType == 'result') {
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    /***************************************************************************
    * Slett ett komplett bilag
    * @param JournalID, Type
    * @return
    */
    function delete_journal($JournalID, $VoucherType) {
        global $_lib;

        #Select for å finne perioden til spesifiserte data
        $voucher = $this->get_journal_head_data(array('JournalID' => $JournalID, 'VoucherType' => $VoucherType, 'is_delete' => true));

        #Jobb  her
        #sett inactive

        if($voucher && strlen($voucher->VoucherPeriod) == 7) {

            if($this->is_valid_accountperiod($voucher->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) {
                #print "Sletter lonns bilag<br>\n";
    
                $this->postmotpost->openPostJournal($JournalID, $VoucherType);
        
                $primarykey['JournalID']     = $JournalID;
                $primarykey['VoucherType']   = $VoucherType;

                #print_r($primarykey);
                $_lib['storage']->db_update_hash(array('voucher_Active' => 0, 'voucher_UpdatedByPersonID' => $_lib['sess']->get_person('PersonID')), 'voucher', $primarykey); #Inactivate instead

                $this->set_journal_motkonto(array('post'=> array('voucher_VoucherPeriod' => $voucher->VoucherPeriod)));

            } else {
                $_lib['message']->add(array('message' => 'Prøver å slette en periode som er stengt. Rapporter denne feilen og hvordan den oppstod'));
            }
        } else {
            #$_lib['message']->add(array('message' => 'Her var det ikke noe å slette'));        
        }
        
    }

    /***************************************************************************
    * Sletter en bilagslinje med alle automatisk opprettede posteringer
    * Function changed with more input parameters for security reasons
    * @param VoucherID
    * @return
    */
    function delete_voucher_line_smart($VoucherID, $JournalID, $VoucherType, $comment = "") {
        global $_lib;

        $_lib['sess']->debug("delete_voucher_line_smart VoucherID: $VoucherID, JournalID: $JournalID, VoucherType: $VoucherType, comment: $comment");
        $post = array();
        $post['voucher_VoucherPeriod'] = $voucher_input->VoucherPeriod;
        $this->set_journal_motkonto(array('post'=>$post));
        $this->postmotpost->openPost($voucher_input->VoucherID);

        #print "Sletter1 smart VoucherID: $VoucherID<br>\n";
        $this->delete_auto_vat($VoucherID, $JournalID, $VoucherType, "delete_voucher_line_smart: VoucherID: $VoucherID");
        #print "Sletter2 smart VoucherID: $VoucherID<br>\n";
        $this->delete_autofrom_line($VoucherID, $JournalID, $VoucherType, "delete_voucher_line_smart: VoucherID: $VoucherID");
        #print "Sletter3 smart VoucherID: $VoucherID<br>\n";
        $this->delete_voucher_line($VoucherID, $JournalID, $VoucherType, "delete_voucher_line_smart: VoucherID: $VoucherID");
        #print "Sletter4 smart VoucherID: $VoucherID<br>\n";
    }

    /***************************************************************************
    * Slettt en bilagslinje og ikke bry deg om den har opprettet andre
    * This is a function that deactivates the contents
    * Opens posts
    * New security check addes JournalID and Voucher type - and will automatically see if period is open
    * @param VoucherID
    * @return
    */
    private function delete_voucher_line($VoucherID, $JournalID, $VoucherType, $comment) {
        global $_lib;
        
        #Den må finne periode selv, og se om den er åpen for det spesifiserte bilaget
        #Select for å finne perioden til spesifiserte data        
        $voucher = $this->get_voucher_object(array('voucherID' => $VoucherID));
        
        #print_r($voucher);
        $_lib['sess']->debug("Sletter linje: Periode: #" . $voucher->VoucherPeriod . "# strlen(" . strlen($voucher->VoucherPeriod) . "), VoucherID: $VoucherID, comment: $comment");

        if($voucher && strlen($voucher->VoucherPeriod) == 7) {
            if($this->is_valid_accountperiod($voucher->VoucherPeriod, $_lib['sess']->get_person('AccessLevel'))) {
            
                $primarykey['VoucherID']    = $VoucherID;
                $primarykey['VoucherType']  = $VoucherType;
                $primarykey['JournalID']    = $JournalID;
                
                #print "Sletter VoucherID: $VoucherID<br>\n";
                #debug_print_backtrace();
                #$_lib['storage']->db_delete_hash('voucher', $primarykey);
                $this->update_voucher_line(array('voucher_Active' => 0), $VoucherID, 'delete_voucher_line', $voucher->VoucherPeriod); #Inactivate instead
            } else {
                #print "Sletter ikke2<br>\n";
                $_lib['message']->add(array('message' => 'Prøver å slette en periode som er stengt. Rapporter denne feilen og hvordan den oppstod'));
            }
        } else {
            #print "Sletter ikke1 : VoucherID: $VoucherID, Periode: $voucher->VoucherPeriod<br>\n";
            #$_lib['message']->add(array('message' => 'Her var det ikke noe å slette'));        
        }
    }

    /***************************************************************************
    * Slett en bilagslinje og ikke bry deg om den har opprettet andre
    * @param VoucherID
    * @return
    */
    private function delete_autofrom_line($VoucherID, $JournalID, $VoucherType) {
        global $_lib;

        if($VoucherID && $JournalID && $VoucherType) {
            $query = "select VoucherID from voucher where AutomaticFromVoucherID=" . (int) $VoucherID . " and JournalID=" . (int) $JournalID . " and VoucherType='" . $VoucherType . "' and Active=1";
            $_lib['sess']->debug($query);
            $hovedbok = $_lib['storage']->get_row(array('query' => $query));
    
            if(isset($hovedbok->VoucherID) and strlen($hovedbok->VoucherID)>0)
            {
                $this->delete_voucher_line_smart($hovedbok->VoucherID, $JournalID, $VoucherType, 'delete_autofrom_line');
            }
        } else {
            $_lib['sess']->debug("Missing: VoucherID: $VoucherID or JournalID: $JournalID or VoucherType: $VoucherType"); 
        }
    }

    function get_department_object($CompanyDepartmentID) {
        global $_lib;
        
        $query="select * from companydepartment where CompanyDepartmentID=" . (int) $CompanyDepartmentID;
        return $_lib['storage']->get_row(array('query' => $query));
    }

    function get_project_object($ProjectID) {
        global $_lib;
        
        $query="select * from project where ProjectID=" . (int) $ProjectID;
        return $_lib['storage']->get_row(array('query' => $query));
    }

    function update_accountline($AccountLineID, $KID, $InvoiceID) {
        global $_lib;

        $query = "select * from accountline where AccountLineID=" . (int) $AccountLineID;
        $row   = $_lib['storage']->get_row(array('query' => $query));
        
        if(strlen($row->KID) == 0) {
            #Only update if empty, not overwrite
            $dataH = array(
                'KID'           => $KID,
                'InvoiceNumber' => $InvoiceID,
                'AccountLineID' => $AccountLineID
            );
        return $_lib['storage']->store_record(array('data' => $dataH, 'table' => 'accountline', 'debug' => false));
        }
    }

    #It could be a problem that this function is not locked to a time period, if two reskontros have the same KID it can fail
    public function FindJournalWithKid($KID) {
        global $_lib;
        $query = "select v.AccountPlanID, a.OrgNumber from voucher as v, accountplan as a where v.KID='$KID' and v.KID != '' and a.EnablePostPost=1 and v.AccountPlanID=a.AccountPlanID and (a.AccountPlanType='customer' or a.AccountPlanType='supplier' or a.AccountPlanType='employee') and v.Active=1 order by v.VoucherDate desc limit 1";
        #print "$query<br>\n";
        return $_lib['storage']->get_row(array('query' => $query));
    }

    #It could be a problem that this function is not locked to a time period, if two reskontros have the same InvoiceID it can fail
    public function FindJournalWithInvoiceID($InvoiceID) {
        global $_lib;
        $query = "select v.AccountPlanID, a.OrgNumber from voucher as v, accountplan as a where v.InvoiceID='$InvoiceID' and v.InvoiceID != '' and a.EnablePostPost=1 and v.AccountPlanID=a.AccountPlanID and (a.AccountPlanType='customer' or a.AccountPlanType='supplier' or a.AccountPlanType='employee') and v.Active=1 order by v.VoucherDate desc limit 1";
        #print "$query<br>\n";
        return $_lib['storage']->get_row(array('query' => $query));
    }


    public function getAccountPlanFromOrgNumber($OrgNumber) {
        global $_lib;

        #Remove leading and trailing spaces.
        #Remove whitespace.
        #Remove letters
        $old_pattern = array("/[^0-9]/", "/_+/", "/_$/");
        $new_pattern = array("", "", "");
        $OrgNumber   = preg_replace($old_pattern, $new_pattern , $OrgNumber);

        $query = "select AccountPlanID from accountplan where OrgNumber like '%$OrgNumber%' and Active=1";
        #print "$query<br>\n";
        $accountplan = $_lib['storage']->get_row(array('query' => $query));
        if($accountplan) {
            return $this->get_accountplan_object($accountplan->AccountPlanID);
        } else {
            return false;
        }
    }

	public function invoiceIDAvailable($JournalID,
									   $AccountPlanID,
									   $InvoiceID,
									   $VoucherType) {
		if (!is_numeric($JournalID)) {
			return false;
		}
		
		if (!is_numeric($AccountPlanID)) {
			return false;
		}

		if (!is_numeric($InvoiceID)) {
			return false;
		}
		
		if ($VoucherType == "" || strlen($VoucherType) != 1) {
			return false;
		}
		

		global $_lib;
        $query = "select COUNT(*) AS cnt from voucher as v
                where v.InvoiceID='$InvoiceID' AND 
                v.InvoiceID != '' AND 
                v.VoucherType='$VoucherType' AND 
                v.JournalID != '$JournalID' AND 
                v.AccountPlanID='$AccountPlanID'";
        #print "$query<br>\n";
        $row = $_lib['storage']->get_row(array('query' => $query));
		return $row->cnt == 0;
	}

}
?>
