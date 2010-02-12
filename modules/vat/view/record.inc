<?
//print_r($_REQUEST);

$primarykey['ID'] = $ID = (int) $_REQUEST['ID'];

if($_REQUEST['action_vat_update'])
{
    #Possible to extend or alter parameters here
    unset($_POST['action_vat_update']);
    unset($_POST['ID']);
    //print_r($_POST);
    $_lib['db']->db_update_hash($_POST, 'vat', $primarykey);

    #Update paralell pair
    #Sale is master for all updates
    #Check to see if it is supposed to be in a pair
    $row = $_lib['storage']->get_row(array('query' => "select * from vat where ID=$ID and Type='sale'"));
    //print "<br>ID: $row->ID - Pair: $row->PairID<br>";
    if($row->PairID > 0)
    {
        #check to see if pair exist
        $exist = $_lib['storage']->get_row(array('query' => "select * from vat where ID=$row->PairID"));
        //print "<br>ID: $exist->ID - Pair: $exist->PairID<br>";
        if($exist->ID > 0)
        {
            #pair exist, we do update
            $primarykey['ID'] = $exist->ID;
            unset($_POST['vat_AccountPlanID']);
            unset($_POST['ID']);
            $_POST['vat_Type'] = 'buy';
            $_POST['vat_VatID'] = (int) $_REQUEST['vat_VatID'] + 30; #Interval difference between buy and sale
            //print_r($_POST);
            $_lib['db']->db_update_hash($_POST, 'vat', $primarykey);
        }
        else
        {
            #pair does not exist, we insert
            $POST = array();
            $POST['vat_ID']     = $row->PairID;
            $POST['vat_PairID'] = $row->ID;
            $POST['vat_Percent'] = $row->Percent;
            $POST['vat_Type']   = 'buy';
            $POST['vat_VatID']  = (int) $_REQUEST['vat_VatID'] + 30; #Interval difference between buy and sale
            $POST['vat_ValidFrom'] = $row->ValidFrom;
            $POST['vat_ValidTo'] = $row->ValidTo;
            $POST['vat_Active']   = $row->Active;
            $POST['vat_EnableVatOverride']   = $row->EnableVatOverride;
            //print_r($POST);
            $PairID = $_lib['db']->db_new_hash($POST, 'vat');
        }
    }
}
elseif($_REQUEST['action_vat_new'])
{
    #Possible to extend or alter parameters here
    $_REQUEST['vat_Type']   = 'sale';
    $ID = $_lib['db']->db_new_hash($_REQUEST, 'vat');
    $_REQUEST['vat_PairID'] = $ID;
    $_REQUEST['vat_Type']   = 'buy';
    $_REQUEST['vat_VatID']  = (int) $_REQUEST['vat_VatID'] + 30; #Interval difference between buy and sale
    $PairID = $_lib['db']->db_new_hash($_REQUEST, 'vat');
    $_lib['db']->db_update("update vat set PairID=$PairID where ID=$ID");
}
elseif($_REQUEST['action_vat_delete'])
{
    #Possible to extend or alter parameters here
    $_lib['db']->db_delete_hash('vat', $primarykey);
}
elseif($_REQUEST['action_vataccount_update'])
{
    #Update VAT Accounts for open periods

    $VoucherType = 'A';
    $periodes = $accounting->get_open_accountperiod_hash();
    #print_r($periodes);

    foreach ($periodes as $period => $value2)
    {
        #print "Konto: $AccountPlanID<br>";

        $VATaccount = $accounting->get_vataccount_objecthash(array('date' => $period . '-01')); #Get the available accounts for the first day in the month
        #print_r($VATaccount);

        foreach ($VATaccount as $AccountPlanID => $value1)
        {
            if($AccountPlanID > 0)
            {
                $sql_saldo = "select sum(AmountIn) as AmIn, sum(AmountOut) as AmOut from voucher where AccountplanID = '".$AccountPlanID."' and VoucherPeriod='$period' and VoucherType!='A'";
                #print "$sql_saldo<br>\n";
                $row       = $_lib['storage']->get_row(array('query' => $sql_saldo));
                $sum       = ($row->AmIn - $row->AmOut);

                if($sum != 0)
                {
                    #Skal vi inserte 0 data?
                    if($sum > 0)
                    {
                        $post['voucher_AmountOut'] = $sum; #Det er positivt, for Œ nulle ut setter vi det pŒ motsatt side, samme konot
                        unset($post['voucher_AmountIn']);
                    }
                    else
                    {
                        $post['voucher_AmountIn']  = $sum;
                        unset($post['voucher_AmountOut']);
                    }

                    $post['voucher_AccountPlanID'] = $AccountPlanID;
                    $post['voucher_VoucherPeriod'] = $period;
                    $post['voucher_VoucherType']   = $VoucherType;
                    $post['voucher_VoucherDate']   = $_lib['date']->get_last_day_in_month($period . '-01');
                    #print_r($post);

                    #Insert / update VAT Accounts
                    $sql_vat_check    = "select JournalID, VoucherID from voucher where AccountPlanID='".$AccountPlanID."' and VoucherPeriod='$period' and VoucherType='$VoucherType'";
                    $row_vat          = $_lib['storage']->get_row(array('query' => $sql_vat_check));

                    if($row_vat->JournalID)
                    {
                        $primarykey['AccountPlanID']   = $AccountPlanID;
                        $primarykey['VoucherPeriod']   = $period;
                        $primarykey['VoucherType']     = $VoucherType;
                        $_lib['db']->db_update_hash($post, 'voucher', $primarykey);

                        if($sum <= 0)
                        {
                            $post['voucher_AmountOut']   = $sum; #Det er positivt, for Œ nulle ut setter vi det pŒ motsatt side
                            unset($post['voucher_AmountIn']);
                        }
                        else
                        {
                            $post['voucher_AmountIn']    = $sum;
                            unset($post['voucher_AmountOut']);
                        }

                        #Motsatt postering mot kto 2740
                        $primarykey['AccountPlanID']   = $_lib['sess']->get_companydef('AccountVat');
                        $post['voucher_AccountPlanID'] = $_lib['sess']->get_companydef('AccountVat');
                        $_lib['db']->db_update_hash($post, 'voucher', $primarykey);

                    }
                    else
                    {
                        list($post['voucher_JournalID'], $message) = $accounting->get_next_available_journalid($_sess, array('available' => true, 'update' => true, 'type' => $VoucherType));
                        #print_r($post);
                        $_lib['db']->db_new_hash($post, 'voucher');

                        if($sum <= 0)
                        {
                            $post['voucher_AmountOut'] = $sum; #Det er positivt, for Œ nulle ut setter vi det pŒ motsatt side
                            unset($post['voucher_AmountIn']);
                        }
                        else
                        {
                            $post['voucher_AmountIn']  = $sum;
                            unset($post['voucher_AmountOut']);
                        }

                        #Motsatt postering mot kto 2740
                        $post['voucher_AccountPlanID'] = $_lib['sess']->get_companydef('AccountVat');
                        #print_r($post);
                        $_lib['db']->db_new_hash($post, 'voucher');
                    }
                }
            } # AccountplanID > 0
        } # end foreach VATAccount
    } # end foreach periods
}
?>