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

$get_invoice            = "select I.*, A.OrgNumber, A.VatNumber, A.Mobile, A.Phone, A.AccountName from $db_table as I, accountplan as A where InvoiceID='$InvoiceID' and A.AccountPlanID=I.CustomerAccountPlanID";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$get_invoicefrom        = "select IName as FromName, IAddress as FromAddress, Email, IZipCode as Zip, ICity as City, ICountryCode as CountryCode, Phone, BankAccount, Mobile, OrgNumber, VatNumber from company where CompanyID='$row->FromCompanyID'";
$row_from               = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

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

$params["sender"]["name"]       = $row_from->FromName;
$params["sender"]["address1"]   = $row_from->FromAddress;
$params["sender"]["zip"]        = $row_from->Zip;
$params["sender"]["city"]       = $row_from->City;
$params["sender"]["country"]    = $_lib['format']->codeToCountry($row_from->CountryCode);

$params["sender"]["orgnumber"]  = $row_from->OrgNumber;
if (!empty($row_from->VatNumber)) {
    $params["sender"]["vatnumber"]  = $row_from->VatNumber;
}
$params["sender"]["email"]      = $row_from->Email;

$params["recipient"]["name"]    = $row->AccountName;
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
if (strlen($row_from->BankAccount) == 11)
    $params["companyInfo"]["Kontonr"] = kontonr($row_from->BankAccount);
else
    $params["companyInfo"]["Kontonr"] = $row_from->BankAccount;

if  (!empty($row_from->VatNumber)) {
        $params["companyInfo"]["OrgNr"] = orgnr($row_from->OrgNumber);
        $params["companyInfo"]["MvaNr"] = orgnr($row_from->VatNumber);
} else {
    if   (strlen($row_from->OrgNumber) == 9)
    {
        if ($_lib['sess']->get_companydef('VATDuty'))
        {
            $params["companyInfo"]["OrgNr"] = orgnr($row_from->OrgNumber) . " MVA";
        }
        else
        {
            $params["companyInfo"]["OrgNr"] = orgnr($row_from->OrgNumber);
        }
    } else {
        $params["companyInfo"]["OrgNr"] = $row_from->OrgNumber;
    }
}

if ($row_from->Phone)
  $params["companyInfo"]["Telefon"]   = $row_from->Phone;
if ($row_from->Mobile)
  $params["companyInfo"]["Mobil"]     = $row_from->Mobile;
if ($row_from->Email)
  $params["companyInfo"]["Epost"]     = $row_from->Email;

$params["invoiceData"]["Fakturanr"] = $InvoiceID;
$params["invoiceData"]["Kundenr"]   = $row->CustomerAccountPlanID;
if($row->KID)  { $params["invoiceData"]["KID"]     = $row->KID; $params["kid"] = $row->KID; }
if($row->Note) $params["invoiceData"]["Merk"]      = $row->Note;

$params["invoiceData"]["Side"] = "1";
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

$sumlines = 0;
$vatlines = 0;
$rowCounter = 0;
while($row2 = $_lib['db']->db_fetch_object($result2))
{
    $LineID=$row2->LineID;
    $sumline = round($row2->QuantityDelivered * $row2->UnitCustPrice, 2);
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
    if($row_company->InvoiceLineCommentPosition == 'bottom')
        $myFakutra->addInvoiceLine ($params2);
    if ($row2->Comment != "")
        $myFakutra->addLongTextLine(array('tekst' =>$row2->Comment));
    if($row_company->InvoiceLineCommentPosition == 'top' || !$row_company->InvoiceLineCommentPosition)
        $myFakutra->addInvoiceLine ($params2);
    $rowCounter++;
    #print_r($row_company);
}

        if($_lib['sess']->get_companydef('ShowInvoiceAmountThisYear') == 1 && 1 == 0)
        {
            $myFakutra->addLongTextLine(array('tekst' =>"Hittil i " . $choosenYear ." har du handlet for: " . $_lib['format']->Amount(array('value'=>$rowTotal->Total, 'return'=>'value')) . " eks MVA (" . $_lib['format']->Amount($rowTotal->TotalWithVAT + $rowTotal->Total) . " inkl MVA)"));
        }
$params["totaltumva"] = $sumlines;
$params["totaltmva"] = $vatlines;
$params["totaltmmva"] = $vatlines + $sumlines;
// $params["kid"] = "";

if ($row->DeliveryCondition  != "")
{
    $myFakutra->addTextLine(array('tekst' => ""));
    $myFakutra->addLongTextLine(array('tekst' => "Leverings betingelse: " . $row->DeliveryCondition));
}
$myFakutra->addSumLine($params);
$myFakutra->fakturaGiro($params);
$myFakutra->printFaktura();
//print_r($myFakutra);
?>
