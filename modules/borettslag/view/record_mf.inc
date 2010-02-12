<?
# $Id: record.inc,v 1.15 2004/11/15 15:06:13 svenn Exp $ company_record.inc,v 1.1.1.1 2001/11/08 18:13:57 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1995-2004, thomas@ekdahl.no

unformatNumbers();
includelogic('accounting/accounting');
$accounting = new accounting();

if($_REQUEST['action_fakturer'])
{
    $context = $_POST["context"];
    $fakturadato =  $_POST["fakturadato"];

    $query = "select * from company where CompanyID='$CompanyID'";
    $row_c = $_dbh[$_dsn]->get_row(array('query' => $query));
    $query2 = "select * from borettslag where CompanyID='$CompanyID'";
    $row_b = $_dbh[$_dsn]->get_row(array('query' => $query2));
    $selectFaktura = "select io.InvoiceDate from invoiceout io, invoiceoutline iol where io.InvoiceID = iol.InvoiceID and iol.ProductID = '" . $row_b->ProductID1 . "' order by io.InvoiceDate desc limit 0, 1;";
    $selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
    $row_f_all = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
    $siste_husleiedato = $row_f_all[0];

    $selectFaktura = "select sum(iol.UnitCustPrice) from invoiceout io, invoiceoutline iol where io.InvoiceID = iol.InvoiceID and iol.ProductID = '" . $row_b->ProductID1 . "' and io.InvoiceDate >= '" . date("Y") . "-01-01' and io.InvoiceDate <= '" . date("Y") . "-12-31';";
    $selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
    $row_f_all = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
    $husleie_totalt = $row_f_all[0];

    $query = "select * from leilighet where BorettslagID = " . $row_b->BorettslagID . " order by Seksjonsnr;";
    $query_handler = $_dbh[$_dsn]->db_query($query);
    while ($aRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
    {
        $selectEier = "select a.AccountName, a.EnableCredit, a.CreditDays, e.* from eierforhold e, accountplan a where e.AccountPlanID = a.AccountPlanID and e.LeilighetID = '" . $aRow->LeilighetID . "' and TilDato = '0000-00-00' order by e.EierforholdID;";
        $result_e = $_dbh[$_dsn]->db_query($selectEier);

        while($row_e = $_dbh[$_dsn]->db_fetch_object($result_e))
        {
            $InvoiceID = 0;
            $InvoiceID = '';
            $_post['invoiceout_InvoiceID']          = '';
            $_post['invoiceout_OrderDate']          = $_lib['sess']->get_session('Date');
            $_post['invoiceout_Status']             = "progress";
            $_post['invoiceout_Active']             = 1;
            $_post['invoiceout_FromCompanyID']      = $_lib['sess']->get_companydef('CompanyID');
            $_post['invoiceout_CreatedDateTime']    = $_lib['sess']->get_session('Date');
            $_post['invoiceout_InvoiceDate']        = $_lib['sess']->get_session('LoginFormDate');
            $_post['invoiceout_PaymentDate']        = $_lib['sess']->get_session('DateTo');
            $_post['invoiceout_DeliveryDate']       = $_lib['sess']->get_session('DateTo');
            $InvoiceID = $_dbh[$_dsn]->db_new_hash($_post, $db_table);
            unset($fakturalinjer);
            $_REQUEST['action_invoiceline_new'] = 1;
            for($i = 1; $i < 11; $i++)
            {
                if ($row_b->{"ProductID" . $i} != 0 && $row_b->{"ProductID" . $i} != "")
                {
                    $_post2['invoiceoutline_Active'] = 1;
                    $_post2['invoiceoutline_InvoiceID'] = $InvoiceID;
                    $InvoiceOutLineId = $_dbh[$_dsn]->db_new_hash($_post2, $db_table2);
                    $fakturalinjer[] = $InvoiceOutLineId;
                }
            }
            unset($_REQUEST['action_invoiceline_new']);
            $_REQUEST['action_invoice_update'] = 1;
            $_POST["invoiceout_AccountPlanID_" . $InvoiceID] = $row_e->AccountPlanID;
            $_POST["invoiceout_InvoiceDate_" . $InvoiceID] = $fakturadato;
            if ($row_e->EnableCredit == 1)
            {
                $dateInfo = split("-", $fakturadato);
                $_POST["invoiceout_PaymentDate_" . $InvoiceID] = date( "Y-m-d", mktime( 0, 0, 0, $dateInfo[1], $dateInfo[2], $dateInfo[0]) + $row_e->CreditDays );
            }
            else
            {
                $_POST["invoiceout_PaymentDate_" . $InvoiceID] = $fakturadato;
            }
            $n = 0;
            $m = 0;
            $mySum = 0;
            for($i = 0; $i < 10; $i++)
            {
                $n++;
                if ($row_b->{"ProductID" . $n} != 0 && $row_b->{"ProductID" . $n} != "")
                {
                    if ($row_e->{"Produkt" . $n} != 0 && $row_e->{"Produkt" . $n} != "")
                    {
                        $m++;
                        $queryP = "select ProductName, AccountPlanID from product where ProductID='" . $row_b->{"ProductID" . $n} . "';";
                        $row_p = $_dbh[$_dsn]->get_row(array('query' => $queryP));
                        $accountplan = get_accountplan($row_p->AccountPlanID);
                        $queryV = "select * from vat where VatID='$accountplan->VatID'";
                        $vat = $_dbh[$_dsn]->get_row(array('query' => $queryV));
                        $invoiceline_id = $fakturalinjer[$i];
                        $_POST["invoiceoutline_ProductID_" . $invoiceline_id] = $row_b->{"ProductID" . $n};
                        $_POST["invoiceoutline_ProductName_" . $invoiceline_id] = $row_p->ProductName . " for " . $context;
                        $_POST["invoiceoutline_QuantityDelivered_" . $invoiceline_id] = "1";
                        $_POST["invoiceoutline_UnitCustPrice_" . $invoiceline_id] = $row_e->{"Produkt" . $n};
                        // print $row_e->{"Produkt" . $n};
                        $_POST["invoiceoutline_Vat_" . $invoiceline_id] = $vat->Percent / 100;
                        $_POST["invoiceoutline_Comment_" . $invoiceline_id] = "";
                        //print_r($_POST);
                        $_POST["$m"] = $invoiceline_id;
                        $mySum += (($_POST["invoiceoutline_UnitCustPrice_" . $invoiceline_id] + $_POST["invoiceoutline_Vat_" . $invoiceline_id]) * $_POST["invoiceoutline_QuantityDelivered_" . $invoiceline_id]) + ($_POST["invoiceoutline_UnitCustPrice_" . $invoiceline_id] + $_POST["invoiceoutline_Vat_" . $invoiceline_id]);

                    }

                }
            }
            $_POST["invoiceout_TotalCustPrice_" . $InvoiceID] = $mySum;
            $_POST["InvoiceID"] = $InvoiceID;
            $_REQUEST['action_invoice_update'] = 1;
            include $_SETUP['HOME_DIR']."/code/lodo/invoice/record.inc";
            unset($_REQUEST['action_invoice_update']);
            unset($_POST["InvoiceID"]);
        }
    }
    $saved = true;
}
?>
