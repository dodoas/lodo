<?php

require('mailsend.php');
//$_RESQUEST['InvoiceID']=11245;
//error_reporting(E_ALL);

function send_invoice($to, $from, $invoiceno, $html, $attachment, $subject) {
    global $_lib;
    $attachment = str_replace("\r", "", chunk_split(base64_encode($attachment)));
    $html = str_replace(array("=", "\r"), array("=3D", ""), $html);
    $hash = md5(time());

    $boundary = $hash;
    $firstb = $boundary;

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

    $mailer = new Mailer();
    $mailer->send("admin@lodo.no", $to, $subject, $message, $firstb);
}

ob_start();
include('print2.php');
header('Content-type: text/html;');
$data_pdf = ob_get_contents();
ob_end_clean();

$recipient = $_REQUEST['email_recipient'];

if(isset($_REQUEST['send_mail_copy']) && $_REQUEST['send_mail_copy'])
{
  $recipient .= ', ' . $_REQUEST['send_mail_copy_mail'];
}

$InvoiceID = $_REQUEST['InvoiceID'];
$db_table = "invoiceout";
$get_invoice            = "select I.*, A.InvoiceCommentCustomerPosition from $db_table as I, accountplan as A where InvoiceID='$InvoiceID' and A.AccountPlanID=I.CustomerAccountPlanID";
$row                    = $_lib['storage']->get_row(array('query' => $get_invoice));

$data_html = "Her er faktura fra " . $row->SName . " til " . $row->IName . ".<br>";
$data_html .= "Faktura med fakturanummer: " . $InvoiceID . "<br>";
$data_html .= "Se vedlegg for " . iconv("UTF-8", "ISO-8859-1", 'å') . " se hele fakturaen";

send_invoice($recipient, $row_from->Email, $_REQUEST['InvoiceID'], $data_html, $data_pdf, $row->SName . " - faktura " . $InvoiceID);

echo $recipient;
echo '<h2>Email sent</h2>';
echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL='. $_lib['sess']->dispatch . 't=invoice.edit&InvoiceID=' . $InvoiceID . '">';

?>
