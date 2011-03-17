<?php

//$_RESQUEST['InvoiceID']=11245;
//error_reporting(E_ALL);

function send_invoice($to, $from, $invoiceno, $html, $attachment) {
    $attachment = chunk_split(base64_encode($attachment));
    $hash = md5(time());

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: multipart/mixed; boundary="Email-mixed-'.$hash.'"' . "\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "From: $from\r\n";
    
    $message = sprintf("--Email-mixed-%s\r\n" .
                       "Content-Type: text/html; charset='iso-8859-1'\r\n" .
                       "\r\n" .
                       "%s" .
                       "\r\n" .
                       "--Email-mixed-%s\r\n" .
                       "Content-Type: application/pdf; name=invoice_%d.pdf\r\n" .
                       "Content-Transfer-Encoding: base64\r\n" .
                       "Content-Disposition: attachment\r\n" .
                       "\r\n" .
                       "%s" .
                       "--Email-mixed-%s\r\n",
                       $hash,
                       $html,
                       $hash,
                       $invoiceno,
                       $attachment,
                       $hash);

    mail($to, "Invoice " . $invoiceno, $message, $headers);
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

if(isset($_REQUEST['send_mail_copy']) && $_REQUEST['send_mail_copy']) 
{
  $recipient .= ', ' . $_REQUEST['send_mail_copy_mail'];
}

send_invoice($recipient, $row_from->Email, $_REQUEST['InvoiceID'], $data_html, $data_pdf);
echo $recipient;
echo '<h2>Email sent</h2>';
echo '<META HTTP-EQUIV="Refresh" CONTENT="1; URL='. $_lib['sess']->dispatch . 't=invoice.edit&InvoiceID=' . $InvoiceID . '">';

?>
