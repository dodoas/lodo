<?
$InvoiceID = $_REQUEST['InvoiceID'];

$VoucherType='S';

$db_table = "invoiceout";
$db_table2 = "invoiceoutline";
$db_table3 = "invoiceoutprint";

includelogic('accounting/accounting');
includelogic('invoicepdf/faktura');
includelogic('kid/kid');

$accounting = new accounting();
require_once "record.inc";

$get_invoice            = "select I.*, A.InvoiceCommentCustomerPosition from $db_table as I, accountplan as A where InvoiceID='$InvoiceID' and A.AccountPlanID=I.CustomerAccountPlanID";

$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$query_invoiceline      = "select * from $db_table2 where InvoiceID='$InvoiceID' and Active <> 0 order by LineID asc";
#print "$query_invoiceline<br>\n";
$result2                = $_lib['db']->db_query($query_invoiceline);

$get_invoiceprint       = "select InvoicePrintDate from $db_table3 where InvoiceID='$InvoiceID'";
$row_print              = $_lib['storage']->get_row(array('query' => $get_invoiceprint));

$query_company = "select * from company where CompanyID='".$_lib['sess']->get_companydef('CompanyID')."'";
$row_company = $_lib['storage']->get_row(array('query'=>$query_company));

# Total shopping
$choosenYear = substr($row->InvoiceDate, 0, 4);
$query_total = "select sum(IVL.UnitCustPrice * IVL.QuantityDelivered) as Total, sum(IVL.UnitCustPrice * IVL.QuantityDelivered * (IVL.Vat/100)) as TotalWithVAT from invoiceoutline IVL, invoiceout IV where substring(IV.InvoiceDate,1,4)='$choosenYear' and IVL.InvoiceID=IV.InvoiceID and IV.CompanyID='".$row->CompanyID."' and IV.InvoiceID <= '$InvoiceID' and IVL.Active=1";
$rowTotal = $_lib['storage']->get_row(array('query' => $query_total));
//class="lodo_data"
//require_once("../code/lodo/invoicepdf/faktura.class.php");

$myFakutra = new pdfInvoice();

$params["CommentPlacement"] = $row_company->InvoiceCommentCustomerPosition;

$params["sender"]["name"]       = $row->SName;
$params["sender"]["address1"]   = $row->SAddress;
$params["sender"]["zip"]        = $row->SZipCode;
$params["sender"]["city"]       = $row->SCity;
$params["sender"]["country"]    = $_lib['format']->codeToCountry($row->SCountryCode);

$params["sender"]["orgnumber"]  = $row->SOrgNo;
if (!empty($row->SVatNo)) {
    $params["sender"]["vatnumber"]  = $row->SVatNo;
}
$params["sender"]["email"]      = $row->SEmail;

$params["recipient"]["name"]    = $row->IName;
if($row->IAddress)
    $params["recipient"]["address1"] = $row->IAddress;
else
    $params["recipient"]["address1"] = 'Postboks ' . $row->IPoBox . " " . $row->IPoBoxCity ;
// $params["recipient"]["address2"] = "Test 2";
if($row->IAddress)
{
    $params["recipient"]["zip"]  = $row->IZipCode;
    $params["recipient"]["city"] = $row->ICity;
}
else
{
    $params["recipient"]["zip"]  = $row->IPoBoxZipCode;
    $params["recipient"]["city"] = $row->IPoBoxZipCodeCity;
}
$params["recipient"]["country"]     = $_lib['format']->codeToCountry($row->ICountryCode);
$params["recipient"]["orgnumber"]   = $row->OrgNumber;
if (!empty($row->VatNumber)) {
    $params["recipient"]["vatnumber"]   = $row->VatNumber;
}
$params["recipient"]["email"]       = $row->DEmail;

if($row->DAddress) {
    //$params["delivery"]["name"]   = "";
    $params["delivery"]["address1"] = $row->DAddress;
    $params["delivery"]["zip"]      = $row->DZipCode;
    $params["delivery"]["city"]     = $row->DCity;
    $params["delivery"]["country"]  = $_lib['format']->codeToCountry($row->DCountryCode);
}

function kontonr($knr)
{
    $del1 = substr($knr, 0, 4);
    $del2 = substr($knr, 4, 2);
    $del3 = substr($knr, 6);
    return $del1 . "." .$del2 . "." .$del3;
}
function orgnr($onr)
{
    $del1 = substr($onr, 0, 3);
    $del2 = substr($onr, 3, 3);
    $del3 = substr($onr, 6);
    return $del1 . " " .$del2 . " " .$del3;

}
if (strlen($row->SBankAccount) == 11)
    $params["companyInfo"]["Kontonr"] = kontonr($row->SBankAccount);
else
    $params["companyInfo"]["Kontonr"] = $row->SBankAccount;

if  (!empty($row->SVatNo)) {
        $params["companyInfo"]["Foretaksregisteret"] = orgnr($row->SOrgNo);
        $params["companyInfo"]["MVA reg"] = orgnr($row->SVatNo);
} else {
    if   (strlen($row->SOrgNo) == 9)
    {
        if ($_lib['sess']->get_companydef('VATDuty'))
        {
            $params["companyInfo"]["Foretaksregisteret"] = orgnr($row->SOrgNo) . " MVA";
        }
        else
        {
            $params["companyInfo"]["Foretaksregisteret"] = orgnr($row->SOrgNo);
        }
    } else {
        $params["companyInfo"]["Foretaksregisteret"] = $row->SOrgNo;
    }
}

if ($row->SPhone)
  $params["companyInfo"]["Telefon"]   = $row->SPhone;
if ($row->SMobile)
  $params["companyInfo"]["Mobil"]     = $row->SMobile;
if ($row->SEmail)
  $params["companyInfo"]["Epost"]     = $row->SEmail;

$params["invoiceData"]["Fakturanr"] = $InvoiceID;
$params["invoiceData"]["Kundenr"]   = $row->CustomerAccountPlanID;
if($row->KID)  { $params["invoiceData"]["KID"]     = $row->KID; $params["kid"] = $row->KID; }
if($row->Note) $params["invoiceData"]["Merk"]      = $row->Note;

$params["invoiceData"]["Fakturadato"] = $myFakutra->norwegianDate(substr($row->InvoiceDate,0,10));
$params["invoiceData"]["Betalingsfrist"] = $myFakutra->norwegianDate(substr($row->DueDate,0,10));
if($row_print && $row_print->InvoicePrintDate != '0000-00-00') $params["invoiceData"]["Utskriftsdato"] = $myFakutra->norwegianDate(substr($row_print->InvoicePrintDate, 0, 10));
$params["invoiceData"]["Valuta"] = $row->CurrencyID;

if($row->RefInternal) $params["invoiceData"]["Deres referanse"] = $row->RefInternal;
if($row->RefCustomer) $params["invoiceData"]["Vår referanse"] = $row->RefCustomer;
if ($row->ProjectName != "")
    $params["invoiceData"]["Prosjekt"] = $row->ProjectNameInternal;
if ($row->ProjectNameCustomer != "")
    $params["invoiceData"]["Prosjekt"] = $row->ProjectNameCustomer;

$params["fakturatype"] = "Faktura";
$params["betingelser"] = $row->PaymentCondition;
$params["comment"] = $row->CommentCustomer;


// Legg til en ny faktura, lage header med sender og mottaker av fakturaen.
$myFakutra->newInvoice($params);
$myFakutra->setPrintGiro($row_company->InvoicePDFPrintGiroInfo);


$sumlines = 0;
$vatlines = 0;
$rowCounter = 0;
$myFakutraLines = array();
// Find and sum all the lines first
while($row2 = $_lib['db']->db_fetch_object($result2))
{
    $LineID=$row2->LineID;
    $sumline = round($row2->QuantityDelivered * $row2->UnitCustPrice, 2);

    $params2["allowancecharges"] = array();

    // Find allowances and charges
    $query = "SELECT * FROM invoicelineallowancecharge WHERE InvoiceLineID = ". $LineID .";";
    $rs = $_lib["db"]->db_query($query);
    while($line_ac = $_lib["db"]->db_fetch_object($rs)) {
        $line_ac_array = array();
        $line_ac_array["type"] = $line_ac->AllowanceChargeType;
        $line_ac_array["ChargeIndicator"] = $line_ac->ChargeIndicator;
        $line_ac_array["AllowanceChargeReason"] = $line_ac->AllowanceChargeReason;
        $line_ac_array["Amount"] = $line_ac->Amount;
        $params2["allowancecharges"][] = $line_ac_array;

        if($line_ac_array["type"] == "line") {
            $multiplicator = $line_ac_array["ChargeIndicator"] == 0 ? -1 : 1;
            $sumline += $multiplicator * $line_ac_array["Amount"];
        }
    }

    $vatline = round(($row2->Vat/100) * $sumline, 2);
    $sumlines += $sumline;
    $vatlines += $vatline;

    $params2["produktnr"] = $row2->ProductID;
    $params2["produktnavn"] = $row2->ProductName;
    $params2["antall"] = $row2->QuantityDelivered;
    $params2["enhetspris"] = $row2->UnitCustPrice;
    $params2["mva"] = $row2->Vat;
    $params2["mvabelop"] = $vatline;
    $params2["linjesum"] = $sumline;

    $myFakutraLines[] = array($row2->Comment, $params2);
}

$params["allowancecharges"] = array();

// Find invoice level allowances and charges
$query = "SELECT * FROM invoiceallowancecharge WHERE InvoiceID = ". $InvoiceID .";";
$rs = $_lib["db"]->db_query($query);
while($invoice_ac = $_lib["db"]->db_fetch_object($rs)) {
    $invoice_ac_array = array();
    $invoice_ac_array["ChargeIndicator"] = $invoice_ac->ChargeIndicator;
    $invoice_ac_array["AllowanceChargeReason"] = $invoice_ac->AllowanceChargeReason;
    $invoice_ac_array["Amount"] = $invoice_ac->Amount;
    $invoice_ac_array["VatPercent"] = $invoice_ac->VatPercent;
    $invoice_ac_array["VatID"] = $invoice_ac->VatID;
    $params["allowancecharges"][] = $invoice_ac_array;

    $multiplicator = $invoice_ac_array["ChargeIndicator"] == 0 ? -1 : 1;
    $sumlines += $multiplicator * $invoice_ac_array["Amount"];
    $vatlines += $multiplicator * round($invoice_ac_array["Amount"] * ($invoice_ac_array["VatPercent"]/100), 2);
}

// setting up params for SumLine
$params["totaltumva"] = $sumlines;
$params["totaltmva"] = $vatlines;
$params["totaltmmva"] = $vatlines + $sumlines;
// $params["kid"] = "";

// print giroinfo
// Need to be before printing lines since it should be on first page
$myFakutra->addSumLine($params);
$myFakutra->fakturaGiro($params);

// printing lines
foreach ($myFakutraLines as $params2) {
    if($row_company->InvoiceLineCommentPosition == 'bottom')
    $myFakutra->addInvoiceLine ($params2[1]);
    if ($params2[0] != "")
        $myFakutra->addLongTextLine(array('tekst' =>$params2[0]));
    if($row_company->InvoiceLineCommentPosition == 'top' || !$row_company->InvoiceLineCommentPosition)
        $myFakutra->addInvoiceLine ($params2[1]);
    $rowCounter++;
}

if($_lib['sess']->get_companydef('ShowInvoiceAmountThisYear') == 1 && 1 == 0)
{
    $myFakutra->addLongTextLine(array('tekst' =>"Hittil i " . $choosenYear ." har du handlet for: " . $_lib['format']->Amount(array('value'=>$rowTotal->Total, 'return'=>'value')) . " eks MVA (" . $_lib['format']->Amount($rowTotal->TotalWithVAT + $rowTotal->Total) . " inkl MVA)"));
}

if ($row->DeliveryCondition  != "")
{
    $myFakutra->addTextLine(array('tekst' => ""));
    $myFakutra->addLongTextLine(array('tekst' => "Leverings betingelse: " . $row->DeliveryCondition));
}
$myFakutra->printFaktura();
//print_r($myFakutra);
?>
