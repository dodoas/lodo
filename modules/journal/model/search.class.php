<?
class search_class {

    /***************************************************************************
    * Søk etter åpne poster mot en spesifisert konto
    * @param $args['AccountPlanID'], $args['EnableSingleChoose'], $args['EnableMultiChoose']
    * @return
    */
    function search_openpost_accountplan($args)
    {
        global $_lib, $_MY_SELF, $accounting;

        if($args['VoucherID'] > 0) {
            //print_r($args);
            #Find all open posts defined on this customer
            
            $accountplan = $accounting->get_accountplan_object($args['AccountPlanID']);
            #print_r($account);
            
            $query = "select v.VoucherID, v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a , voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where v.AccountPlanID = " . $args['AccountPlanID'] . " and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' or a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
            #print "$query<br>";
            $result = $_lib['db']->db_query($query);
            if($_lib['db']->db_numrows($result) > 0)
            {
                $_showresult  = "<br /><br /><fieldset><legend>&Aring;pne poster p&aring; konto: $AccountPlanID</legend><table class=\"lodo_journal\">";
                $_showresult .= "<tr><th>Type</th><th>Bilag</th><th>Dato</th><th>Konto</th><th>Kontonavn</th><th>" . $accountplan->debittext . "</th><th>" . $accountplan->credittext . "</th><th>KID</th>";
                if($args['EnableSingleChoose'])
                {
                    $_showresult .= "<th></th>";
                }
                if($args['EnableMultiChoose'])
                {
                    $_showresult .= "<th></th>";
                }
                $_showresult .= "</tr>";
    
                while($row = $_lib['db']->db_fetch_object($result))
                {
                    if($row->VoucherType != $args['VoucherType'] or $row->JournalID != $args['JournalID'])
                    {
                        $bgit++;
                        $sec_color=($bgit % 2)?"BGColorLight":"BGColorDark";

                        $_showresult .= "\n<tr class=\"$sec_color\"><td>$row->VoucherType</td><td class=\"number\">";
                        
                        $_showresult .= "<a href=\"" . $_lib['sess']->dispatch . "t=journal.edit&amp;voucher_VoucherType=$row->VoucherType&amp;voucher_JournalID=$row->JournalID&amp;action_journalid_search=1\">$row->JournalID</a>";
                        #$row->JournalID
                        
                        $_showresult .= "</td><td>$row->VoucherDate</td><td>$row->AccountPlanID</td><td>$row->AccountName</td><td class=\"" . $accountplan->CreditColor . " number\">";
                        if($row->AmountIn > 0) $_showresult .= $_lib['format']->Amount($row->AmountIn);
                        $_showresult .= "</td>\n<td class=\"" . $accountplan->DebitColor . " number\">";
                        if($row->AmountOut > 0) $_showresult .= $_lib['format']->Amount($row->AmountOut);
                        $_showresult .= "</td><td>$row->KID</td>";
                        if($args['EnableSingleChoose'])
                        {
                            //$_showresult .= "<td><a href=\"" . $_MY_SELF . '&amp;CustomerNumber=' . $CustomerNumber . '&amp;AmountOut=' . $row->AmountIn . '&amp;AmountIn=' . $row->AmountOut . '&amp;KID=' . $row->KID . '&amp;type=' . $type . "&amp;new=1\">Velg</a></td>";
                            $_showresult .= "<td><a href=\"".$_MY_SELF.'&amp;voucher_VoucherID='.$args['VoucherID'].'&amp;VoucherType='.$args['VoucherType'].'&amp;voucher_JournalID='.$args['JournalID'].'&amp;voucher_AccountPlanID='.$args['AccountPlanID'].'&amp;voucher_AmountOut='.$row->AmountIn.'&amp;voucher_AmountIn='.$row->AmountOut.'&amp;voucher_KID='.$row->KID.'&amp;type='.$args['type']."&amp;action_voucher_update=1\">Velg</a></td>";
                        }
                        if($args['EnableMultiChoose'])
                        {
                            $_showresult .= "<td>" . $_lib['form3']->checkbox(array()) . "</td></tr>";
                        }
                    }
                }
                if($args['EnableMultiChoose'])
                {
                    $_showresult .= "<tr><td colspan=\"9\"></td><td>" . $_lib['form3']->submit(array('name' => 0, 'value' => 'Velg')) . "</td></tr>";
                }
                $_showresult .= "</table></fieldset>";
            }
        }
        #$_showresult .= 'Fra: ' . $args['From'];
        return $_showresult;
    }

    /***************************************************************************
    * Søk etter konto basert på kundenummer
    * @param
    * @return
    */
    function search_customernumber($CustomerNumber) {
        global $_lib;
        $query = "select AccountPlanID from accountplan where CustomerNumber like '%$CustomerNumber%' or OrgNumber like '%$CustomerNumber%'";
        #We should possible have a dropdown to specify org or customer number + we should have a alternate GUI to show more than one hit
        print "<h2>$query</h2><br>\n";
        $result = $_lib['db']->db_query($query);

        if($row = $_lib['db']->db_fetch_object($result)) {
            $AccountPlanID = $row->AccountPlanID;
        } else {
            $AccountPlanID = 0; #No customer number match found
        }

        if($_lib['db']->db_numrows($result) > 1)
            $_lib['message']->add(array('message' => "Vi fikk mer enn ett treff p&aring: $CustomerNumber - Du m&aring; spesifisere bedre"));
        elseif($_lib['db']->db_numrows($result) == 1)
            $_lib['message']->add(array('message' => "Fant kundenummer/Organisasjonsnummer: $CustomerNumber p&aring; konto: $AccountPlanID"));

        return $AccountPlanID;
    }

    /***************************************************************************
    * Seareches for all open posts with a spesific amount
    * @param
    * @return
    */
    function search_openpost_amount($Amount) {
        global $_lib;
        #Find amount

        #$query = "select AmountIn, AmountOut, JournalID, KID, VoucherDate from voucher as v, voucherstruct as s where (v.AmountIn = '$Amount' or v.AmountOut = '$Amount') and (v.JournalID=s.Parent or v.JournalID=s.Child) and Closed=0";
        $query = "select v.AmountIn, v.AmountOut, v.JournalID, v.VoucherType, v.KID, v.InvoiceID, v.VoucherDate, a.AccountName, a.AccountPlanID from accountplan as a, voucher as v left join voucherstruct as s on (v.VoucherID=s.ParentVoucherID or v.VoucherID=s.ChildVoucherID)  where (v.AmountIn = '$Amount' or v.AmountOut = '$Amount') and (s.Closed=0 or s.Closed IS NULL) and (a.AccountPlanType='customer' or a.AccountPlanType='supplier') and a.AccountPlanID=v.AccountPlanID";
        #print "$query<br>";
        $result = $_lib['db']->db_query($query);

        if($_lib['db']->db_numrows($result) > 0) {

            $_showresult = "<table>";

            #Find all open posts defined on this customer
            while($row = $_lib['db']->db_fetch_object($result)) {
                $_showresult .= "<tr><td>$row->VoucherType</td><td>$row->JournalID</td><td>$row->VoucherDate</td><td>$row->AccountPlanID</td><td>$row->AccountName</td><td>$row->AmountIn</td><td>$row->AmountOut</td><td>$row->KID</td></tr>";
            }
            $_showresult .= "</table>";
        }
        return $_showresult;
    }
}
?>
