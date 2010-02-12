<? 
if($_REQUEST[action_mail_send]) { 

$mailMottaker = "post@konsulentvikaren.no";
$mailSender = "websiden@lodo.no";
$mailSubject = "Henvendelse fra websiden";
$fieldName[] = "name";
$fieldName[] = "company";
$fieldName[] = "address";
$fieldName[] = "zipcode";
$fieldName[] = "city";
$fieldName[] = "telephone";
$fieldName[] = "email";

$headers = "From: " . $mailSender . "\n";
$headers = $headers . "Reply-To: " . $mailSender . "\n";
$headers = $headers . "X-Mailer: PHP5\n";
$headers = $headers . "X-Sender: " . $mailSender . "\n";
$message = "Det har kommet en henvendelse fra websiden.\n" .
		"Informasjonen som ble send er:\n\n";
global $_REQUEST;

for ($i = 1; $i < count($fieldName); $i++)
{
	$myFieldName = $fieldName[$i];
	$message = $message . $myFieldName . ": " . $_REQUEST[$myFieldName] . "\n\n";
}
$mailSent = mail($mailMottaker,$mailSubject,$message,$headers);

?>
   Vi takker for din interesse for regnskapspakken Lodo.<br /><br />

   Vi vil ta kontakt med deg s&aring; snart som mulig.
<? 
	if (!$mailSent)
		print "<br>Noe gikk galt, pr&oslash;v &aring; sende mailen selv til: " . $mailMottaker . ". <br>";
} else { ?>
<h2>Ja, jeg &oslash;nsker kontakt</h2>
    <form action="<? print $MY_SELF ?>" method="post">
        <table>
            <tr>
                <td>
                    Navn
                </td>
                <td>
                    <input type="text" name="name" value="" size="30" tabindex="10" />
                </td>
            </tr>
            <tr>
                <td>
                    Firma
                </td>
                <td>
                    <input type="text" name="company" value="" size="30" tabindex="11" />
                </td>
            </tr>
            <tr>
                <td>
                    Adresse
                </td>
                <td>
                    <input type="text" name="address" value="" size="30" tabindex="12" />
                </td>
            </tr>
            <tr>
                <td>
                    Postnummer
                </td>
                <td>
                    <input type="text" name="zipcode" value="" size="6" tabindex="13" />
                </td>
            </tr>
            <tr>
                <td>
                    Sted
                </td>
                <td>
                    <input type="text" name="city" value="" size="20" tabindex="14" />
                </td>
            </tr>
            <tr>
                <td>
                    Telefon
                </td>
                <td>
                    <input type="text" name="telephone" value="" size="10" tabindex="15" />
                </td>
            </tr>
            <tr>
                <td>
                    E-post
                </td>
                <td>
                    <input type="text" name="email" value="" size="20" tabindex="16" />
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <input type="submit" name="action_mail_send" accesskey="S" tabindex="17" value="Send" />
                </td>
            </tr>
        </table>
    </form>
    <? } ?>
