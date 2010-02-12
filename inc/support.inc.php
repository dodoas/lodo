<?
if($_REQUEST[action_mail_send])
{

    $_lib['message']->send_generic($_POST);
    ?>
    Vi takker for din interesse for regnskapspakken Lodo.<br /><br />

    Vi vil ta kontakt med deg s&aring; snart som mulig.
    <?
}

else
{

    ?>
    <h2>Ja, jeg &oslash;nsker support</h2>
    <form action="<? print $MY_SELF ?>" method="post">
        <? print $_lib['form3']->hidden(array('name'=>'to', 'value'=>'post@konsulentvikaren.no')) ?>
        <? print $_lib['form3']->hidden(array('name'=>'from', 'value'=>'post@konsulentvikaren.no')) ?>
        <? print $_lib['form3']->hidden(array('name'=>'subject', 'value'=>'Support skjema fra '.$_SERVER['HTTP_HOST'])) ?>
        <table>
            <tr>
                <td valign="top">
                    Fornavn
                </td>
                <td valign="top">
                    <? print $_lib['form3']->text(array('name' => 'Fornavn', 'value' => $_REQUEST['Fornavn'])) ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Etternavn
                </td>
                <td>
                    <? print $_lib['form3']->text(array('name' => 'Etternavn', 'value' => $_REQUEST['Etternavn'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Kundenavn
                </td>
                <td>
                    <? print $_lib['form3']->text(array('name' => 'Kundenavn', 'value' => $_REQUEST['Kundenavn'])) ?>(Det du bruker for &Aring; logge inn i Lodo)
                </td>
            </tr>
            <tr>
                <td>
                    E-Post
                </td>
                <td>
                    <? print $_lib['form3']->text(array('name' => 'Epost', 'value' => $_REQUEST['Epost'])) ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Beskrivelse
                </td>
                <td>
                    <? print $_lib['form3']->textarea(array('name' => 'Beskrivelse', 'value' => $_REQUEST['Beskrivelse'])) ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <input type="submit" name="action_mail_send" accesskey="S" tabindex="17" value="Send" />
                </td>
            </tr>
        </table>
    </form>
    <?
}
?>