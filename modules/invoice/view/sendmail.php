<?php

//$_RESQUEST['InvoiceID']=11245;
//error_reporting(E_ALL);

function send_invoice($to, $from, $invoiceno, $html, $attachment) {
    global $_lib;

    $invoice = $_lib['storage']->get_row(array("query" => "select I.*, A.InvoiceCommentCustomerPosition from invoiceout as I, accountplan as A where InvoiceID='$invoiceno' and A.AccountPlanID=I.CustomerAccountPlanID"));
    $attachment = str_replace("\r", "", chunk_split(base64_encode($attachment)));
    $html = str_replace(array("=", "\r"), array("=3D", ""), $html);
    $hash = md5(time());

    $boundary = $hash;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: multipart/mixed; boundary='.$boundary. "\r\n";
    $headers .= "From: $from\r\n";

    $message = sprintf("--$boundary\n" .
                       "Content-Type: text/html; charset=ISO-8859-1\n" .
                       "Content-Transfer-Encoding: quoted-printable\n" .
                       "\n" .
                       "%s" .
                       "\n" .
                       "--$boundary\n" .
                       "Content-Type: application/pdf; name=invoice_%d.pdf\n" .
                       "Content-Disposition: attachment; filename=invoice_%d.pdf\n" .
                       "Content-Transfer-Encoding: base64\r\n" .
                       "\n" .
                       "%s" .
                       "--$boundary--",
                       $html,
                       $invoiceno, $invoiceno,
                       $attachment);

    mail(
          $to
        , $invoice->SName . ", fakturanr " . $invoiceno . " kr " . $invoice->TotalCustPrice
        , $message
        , $headers
    );
}

ob_start();
include('print.php');
$data_html = ob_get_contents();
ob_end_clean();
ob_start();
include('print2.php');
header('Content-type: text/html;');
$data_pdf = ob_get_contents();
ob_end_clean();

$data_html = "<html><body>" . strstr($data_html, "<h2>");
$data_html = strip_tags($data_html, "<html><body><table><h2><tr><td><thead><tbody><tfoot><label><colgroup><br><th>");

$recipient = $_REQUEST['email_recipient'];

if(isset($_REQUEST['send_mail_copy']) && (bool)$_REQUEST['send_mail_copy'])
{
  $recipient .= ', ' . $_REQUEST['send_mail_copy_mail'];
}

$get_invoicefrom = "SELECT * FROM company WHERE CompanyID='" . $_lib['sess']->get_companydef('CompanyID') . "'";
$row_from = $_lib['storage']->get_row(array('query' => $get_invoicefrom));

$additional_params = "";
if($_lib["input"]->getProperty("action_send_email2_lock")) $additional_params = "&action_invoice_lock=1";

send_invoice($recipient, $row_from->Email, $_REQUEST['InvoiceID'], $data_html, $data_pdf);
echo $recipient;
echo '<h2>Email sent</h2>';
echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL='. $_lib['sess']->dispatch . 't=invoice.edit&InvoiceID=' . $InvoiceID . $additional_params .'">';

?>
