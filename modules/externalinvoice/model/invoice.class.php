<?
/*******************************************************************************
* Lodo functionality
*
* @author Geir Eliassen
* @copyright http://www.lodo.com/
*
*/

class extinvoice {
    var $InvoiceID     = 0;
    var $AccountPlanID = 0;
    var $JournalID     = 0;
    var $VoucherType   = 'S';
    var $VoucherType2   = 'B';

    /*******************************************************************************
    * constructor
    * @param
    * @return
    */
    function __construct($args = array()) {
        foreach($args as $key => $value) {
            $this->{$key} = $value;
        }
    }
    function updateInvoice($args) {
        global $_lib, $_SETUP, $accounting;

        // Slett eventuelt eksisterende bilag forst:
        $this->delete($args);
        // Forst legge til reskontroen om den ikke finnes.
        $query_invoiceline = "select * from accountplan where AccountplanID='" . $args["kundenr"] . "';";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        if ($result2->AccountplanID != $args["kundenr"] || $args["kundenr"] == "" )
        {
            $myInfo['accountplan_debittext']  = "Inn";
            $myInfo['accountplan_credittext'] = "Ut";
            $myInfo['accountplan_VatID'] = 0;
        }
        $myInfo['accountplan_Active'] = 1;
        $myInfo['accountplan_AccountName'] = $args["kundenavn"];
        $myInfo['accountplan_AccountPlanID'] = $args["kundenr"];
        $myInfo['accountplan_type'] = "reskontro1500";

        $account = $accounting->getHovedbokToAccount($args["kundenr"]);

        $_POST['accountplan_EnablePostPost'] = $account->EnablePostPost;

        includelogic("arbeidsgiveravgift/savetable2");
        $myTable = new SaveTable("accountplan", $args["kundenr"], true);
	$myTable->set("Active", 1);
        $myTable->set("AccountName", $args["kundenavn"]);
        $myTable->set("AccountPlanID", $args["kundenr"]);
        $myTable->set("type", $args["reskontro1500"]);
        $neededCreditDays = $_lib['date']->dateDiff($args["forfall"], $args["fakturadato"]);
        if ($neededCreditDays > 0)
            $myTable->set("EnableCredit", 1);
        if ($myTable->get("CreditDays") < $neededCreditDays)
            $myTable->set("CreditDays", $neededCreditDays);
        if ($args["kundenavn"] != "" && $args["kundenr"] != "")
            $myTable->save();

        // Legg saa til posteringen.
        $fields = array();
        $fields['voucher_AmountOut'] = 0;
        $fields['voucher_AmountIn'] = 0;
        $fields['voucher_JournalID']      = $args["fakturanr"];
        $fields['voucher_KID']            = $args["fakturanr"];
        $fields['voucher_InvoiceID']      = $args["fakturanr"];
        $fields['voucher_VoucherPeriod']  = $_lib['date']->get_this_period($args["fakturadato"]);
        $fields['voucher_VoucherDate']    = $args["fakturadato"];
        $fields['voucher_VoucherType']    = $this->VoucherType;
        $fields['voucher_DueDate']        = $args["forfall"];
        $fields['voucher_Active']         = 1;
        $fields['voucher_AutomaticReason']  = "Faktura: " . $args["fakturanr"];
        $fields['voucher_Description']  = "Faktura: " . $args["fakturanr"];
        $fields['voucher_AmountOut'] = $args["belop"];

        $VoucherID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>$args["kundenr"], 'type'=>'reskontro', 'VoucherType'=> $this->VoucherType, 'invoice'=>'1'));
        $query = "select VatID, EnableVAT, EnableVATOverride from accountplan where Active=1 and AccountPlanID = '" . $args["hovedbokskonto"] . "';";
        $account = $_lib['storage']->get_row(array('query' => $query));

        $fieldsline = array();
        $fieldsline['voucher_AmountOut'] = 0;
        $fieldsline['voucher_AmountIn'] = 0;
        $vathash = $accounting->get_vatpercenthash(array('sale'=>'1', 'date' => $args["fakturadato"]));
        $fieldsline['voucher_JournalID']        = $args["fakturanr"];
        // $fieldsline['voucher_KID']        = $args["fakturanr"];
        $fieldsline['voucher_VatID']            = $vathash[($account->VAT * 100)];
        $fieldsline['voucher_Vat']              = ($account->VAT * 100);
        $fieldsline['voucher_DepartmentID']     = 0;
        $fieldsline['voucher_ProjectID']        = 0;
        $fieldsline['voucher_Description']      = "Faktura: " . $args["fakturanr"];
        $fieldsline['voucher_VoucherText']      = 0;
        $fieldsline['voucher_VoucherPeriod']    = $_lib['date']->get_this_period($args["fakturadato"]);
        $fieldsline['voucher_VoucherDate']      = $args["fakturadato"];
        $fieldsline['voucher_VoucherType']      = $this->VoucherType;
        $fieldsline['voucher_DueDate']          = $args["forfall"];
        $fieldsline['voucher_AmountOut'] = $args["belop"];
        $fieldsline['voucher_Active']           = 1;

        $VoucherID2 =  $accounting->insert_voucher_line(array('post'=>$fieldsline, 'accountplanid'=>$args["hovedbokskonto"], 'type'=>'result1', 'VoucherType'=>$this->VoucherType, 'invoice'=>'1'));
        $fields['voucher_AccountPlanID'] = $args["kundenr"];

        $AmountIn                       = $fields['voucher_AmountIn'];
        $fields['voucher_AmountIn']     = $fields['voucher_AmountOut'];
        $fields['voucher_AmountOut']    = $AmountIn;
        $accounting->set_journal_motkonto(array('post'=>$fields, 'VoucherType'=>$this->VoucherType));
        $accounting->correct_journal_balance($fields, $this->JournalID, $this->VoucherType);

        if ($VoucherID > 1 && $VoucherID2 > 1)
            return $fieldsline['voucher_VoucherType'] . $fieldsline['voucher_JournalID'];
    }
    function updatePayment($args) {
        global $_lib, $_SETUP, $accounting;
        // Slett eventuelt eksisterende bilag f�rst:
        if ($args["oppdaterinnbetaling"])
            $this->delete2($args);

        $query = "select JournalID from voucher where Active=1 and VoucherType='B' and KID ='" . $args["fakturanr"] . "';";
        $gammelPostering = $_lib['storage']->get_row(array('query' => $query));
        if ($gammelPostering->JournalID == "")
        {
            // Forst legge til reskontroen om den ikke finnes.
            $query_invoiceline = "select * from accountplan where AccountplanID='" . $args["kundenr"] . "';";
            $result2 = $_lib['db']->db_query($query_invoiceline);
            if ($result2->AccountplanID != $args["kundenr"] || $args["kundenr"] == "" )
            {
                $myInfo['accountplan_debittext']  = "Inn";
                $myInfo['accountplan_credittext'] = "Ut";
                $myInfo['accountplan_VatID'] = 0;
            }
            $myInfo['accountplan_Active'] = 1;
            $myInfo['accountplan_AccountName'] = $args["kundenavn"];
            $myInfo['accountplan_AccountPlanID'] = $args["kundenr"];
            $myInfo['accountplan_type'] = "reskontro1500";

            $account = $accounting->getHovedbokToAccount($args["kundenr"]);

            $_POST['accountplan_EnablePostPost'] = $account->EnablePostPost;

            includelogic("arbeidsgiveravgift/savetable2");
            $myTable = new SaveTable("accountplan", $args["kundenr"], true);
            $myTable->set("AccountName", $args["kundenavn"]);
            $myTable->set("AccountPlanID", $args["kundenr"]);
            $myTable->set("type", $args["reskontro1500"]);
            if ($args["kundenavn"] != "" && $args["kundenr"] != "")
            $myTable->save();


            if ($this->JournalID == "")
            list($this->JournalID, $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $this->VoucherType2));

            // Legg saa til posteringen.
            $fields = array();
            $fields['voucher_AmountOut'] = 0;
            $fields['voucher_AmountIn'] = 0;
            $fields['voucher_JournalID']      = $this->JournalID;
            $fields['voucher_KID']            = $args["fakturanr"];
            $fields['voucher_InvoiceID']      = $args["fakturanr"];
            $fields['voucher_VoucherPeriod']  = $_lib['date']->get_this_period($args["betaltdato"]);
            $fields['voucher_VoucherDate']    = $args["betaltdato"];
            $fields['voucher_VoucherType']    = $this->VoucherType2;
            $fields['voucher_DueDate']        = $args["betaltdato"];
            $fields['voucher_Active']         = 1;
            $fields['voucher_Description']  = "Faktura: " . $args["fakturanr"];
            $fields['voucher_AmountOut'] = $args["innbetaltbelop"];

            $VoucherID = $accounting->insert_voucher_line(array('post'=>$fields, 'accountplanid'=>1920, 'type'=>'balanse1', 'VoucherType'=> $this->VoucherType2));

            $fieldsline = array();
            $fieldsline['voucher_AmountOut'] = 0;
            $fieldsline['voucher_AmountIn'] = 0;
            $fieldsline['voucher_JournalID']        = $this->JournalID;
            $fieldsline['voucher_KID']              = $args["fakturanr"];
            $fieldsline['voucher_InvoiceID']        = $args["fakturanr"];
            $fieldsline['voucher_DepartmentID']     = 0;
            $fieldsline['voucher_ProjectID']        = 0;
            $fieldsline['voucher_Description']      = "Faktura: " . $args["fakturanr"];
            $fieldsline['voucher_VoucherPeriod']    = $_lib['date']->get_this_period($args["betaltdato"]);
            $fieldsline['voucher_VoucherDate']      = $args["betaltdato"];
            $fieldsline['voucher_VoucherType']      = $this->VoucherType2;
            $fieldsline['voucher_DueDate']          = $args["betaltdato"];
            $fieldsline['voucher_AmountIn'] = $args["innbetaltbelop"];
            $fieldsline['voucher_Active']           = 1;

            $VoucherID2 = $accounting->insert_voucher_line(array('post'=>$fieldsline, 'accountplanid'=>$args["kundenr"], 'type'=>'reskontro', 'VoucherType'=>$this->VoucherType2));
            $fields['voucher_AccountPlanID'] = $args["kundenr"];

            $AmountIn                       = $fields['voucher_AmountIn'];
            $fields['voucher_AmountIn']     = $fields['voucher_AmountOut'];
            $fields['voucher_AmountOut']    = $AmountIn;
            $accounting->set_journal_motkonto(array('post'=>$fields, 'VoucherType'=>$this->VoucherType2));
            $accounting->correct_journal_balance($fields, $this->JournalID, $this->VoucherType2);
            if ($VoucherID > 1 && $VoucherID2 > 1)
            return $fieldsline['voucher_VoucherType'] . $fieldsline['voucher_JournalID'];
        }
    }
    function delete($args)
    {
        global $_lib, $_SETUP, $accounting;
        $query_invoiceline = "select * from voucher where JournalID='" . $args["fakturanr"] . "' and  VoucherType = '" . $this->VoucherType . "';";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        $this->JournalID = $result2->JournalID;
        $sql_delete_voucher = "delete from voucher where JournalID='" . $args["fakturanr"] . "' and VoucherType='" . $this->VoucherType . "'";
        $_lib['db']->db_delete($sql_delete_voucher);
        $collection['voucher_VoucherPeriod']     = $result2->VoucherPeriod;
        $accounting->set_journal_motkonto(array('post'=>$collection, 'VoucherType'=>$this->VoucherType2));
    }
    function delete2($args)
    {
        global $_lib, $_SETUP, $accounting;
        $query_invoiceline = "select * from voucher where KID='" . $args["fakturanr"] . "' and  VoucherType = '" . $this->VoucherType2 . "' and Description = 'Faktura: " . $args["fakturanr"] . "';";
        $result2 = $_lib['db']->db_query($query_invoiceline);
        $this->JournalID = $result2->JournalID;
        $sql_delete_voucher = "delete from voucher where JournalID = '" . $this->JournalID . "' and VoucherType = '" . $this->VoucherType2 . "';";
        $_lib['db']->db_delete($sql_delete_voucher);
        $collection['voucher_VoucherPeriod']     = $result2->VoucherPeriod;
        $accounting->set_journal_motkonto(array('post'=>$collection, 'VoucherType'=>$this->VoucherType2));
    }
}
?>
