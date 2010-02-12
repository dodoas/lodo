<? 
#print_r($_REQUEST);
if($_REQUEST['action_company_register']) {
    $required = 0;
    $_POST['installation_CreatedDateTime']  = $_lib['sess']->get_session('Date');
    $_POST['installation_Active']           = 0;
    
    if(!$_POST['installation_VName']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'VName', 'message' => 'Firmanavn p&aring;krevet'));
    }
    
    if(!$_POST['installation_FirstName']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'FirstName', 'message' => 'Fornavn p&aring;krevet'));
    }
    
    if(!$_POST['installation_LastName']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'LastName', 'message' => 'Etternavn p&aring;krevet'));
    }

    if(!$_POST['installation_Email']) {
        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'Email', 'message' => ' E-post p&aring;krevet'));
    }
    
    if(!$_POST['installation_AcceptedLicence']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'AcceptedLicence', 'message' => 'Du m&aring; lese og godta lisensen'));
    }
    
    if(!$_POST['installation_Password']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'Password', 'message' => 'Passord er p&aring;krevet'));
    } elseif($_POST['installation_Password'] != $_POST['Password']) {

        $required = 1;
        $_lib['message']->add_field(array('table' => 'installation', 'field' => 'Password', 'message' => 'Passordene m&aring; v&aelig;re like'));    
    }
    
    if(!$required) {
        $_POST['installation_InstallName']      = strtoupper($_POST['installation_VName']);
        $_POST['installation_InstallName']      = str_replace(' ', '_', $_POST['installation_VName']);
        $_lib['db']->db_new_hash($_POST, 'installation');
        $success = 1;
    ?>
    Vi takker for din interesse for regnskapspakken Lodo.<br /><br />

    Vi vil ta kontakt med deg s&aring; snart som mulig.
    <? 
    } else {
        print "<font color=\"#FF0000\">Du m&aring; fylle ut p&aring;krevde felter i skjemaet</font>";
    }
} 

if(!$success) { ?>
<h2>Ja, jeg &oslash;nsker &aring; bruke Lodo gratis i en m&aring;ned</h2>
Du f&aring;r med en bruker og AltInn integrasjon

<h3>Lisensbetingelser</h3>

<ol>
    <li>Alle nye brukere vil f&aring; en m&aring;ned gratis. Etter dette vil prisen vil da v&aelig;re kr 200 
+ mva pr mnd. dersom antall posteringer er mindre enn 15000 pr &aring;r. Etter gratism&aring;nden blir konton 
stengt og slettet en m&aring;ned senere om ikke betaling foreligger.   
    <li>Lodo er ikke ansvarlig for tekniske feil eller regnskapsmessige feil.
    <li>Lodo serveren har typisk en oppetid p&aring; 99% eller bedre. 
    <li>Support, forbedringer og feil meldes p&aring; e-post til <a href="mailto:support@lodo.no">support@lodo.no</a>.
    <li>Lodo er ikke ansvarlig for innbrudd som m&aring;tte offentliggj&oslash;re sensitive opplysninger 
    
    <li>&Oslash;nsker du &aring; legge til flere brukere betaler du pr bruker
</ol>
<h2>Registrer deg her</h2>
    <form action="<? print $MY_SELF ?>" method="post">
        <table>
            <tr>
                <th colspan="2">Firmaopplysninger</th>
            </tr>
            <tr>
                <td colspan="2">
                    Opplysningene du oppgir her vil bli brukt for &aring; opprette deg korrekt som kunde.
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Firma*
                </td>
                <td valign="top">
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VName', 'value' => $_REQUEST['installation_VName'])) ?>
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'VName'))  ?>
                </td>
            </tr>
            <tr>
                <td>
                    Adresse
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VAddress', 'value' => $_REQUEST['installation_VAddress'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Postnummer
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VZipCode', 'value' => $_REQUEST['installation_VZipCode'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Sted
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VCity', 'value' => $_REQUEST['installation_VCity'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Telefon
                </td>
                <td>
                    <? print  $_lib['form3']->text(array('table' => 'installation', 'field' => 'Phone', 'value' => $_REQUEST['installation_Phone'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Webadresse
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'WWW', 'value' => $_REQUEST['installation_WWW'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Organisasjonsnummer
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'CompanyNumber', 'value' => $_REQUEST['installation_CompanyNumber'])) ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">Personopplysninger</th>
            </tr>
            <tr>
                <td colspan="2">
                    Opplysningene du oppgir her vil bli brukt for &aring; gi deg en personlig bruker.
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Fornavn*
                </td>
                <td valign="top">
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'FirstName', 'value' => $_REQUEST['installation_FirstName'])) ?>
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'FirstName'))  ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Etternavn*
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'LastName', 'value' => $_REQUEST['installation_LastName'])) ?>
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'LastName'))  ?>
                </td>
            </tr>
            <tr>
                <td>
                    Mobil
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'MobilePhoneNumber', 'value' => $_REQUEST['installation_MobilePhoneNumber'])) ?>
                </td>
            </tr>
            <tr>
                <td>
                    E-Post*
                </td>
                <td>
                    <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Email', 'value' => $_REQUEST['installation_Email'])) ?>
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'Email'))  ?>
                </td>
            </tr>
            <tr>
                <td valign="top">
                    Passord*
                </td>
                <td>
                    <? print $_lib['form3']->password(array('table' => 'installation', 'field' => 'Password', 'value' => $_REQUEST['installation_Password'])) ?>
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'Password'))  ?>
                </td>
            </tr>
            <tr>
                <td>
                    Gjenta passord*
                </td>
                <td>
                    <? print $_lib['form3']->password(array('name' => 'Password', 'value' => $_REQUEST['Password'])) ?>
                </td>
            </tr>
            <tr>
                <th colspan="2">Lisens</th>
            </tr>
            <tr>
                <td valign="top">
                    <? print $_lib['form3']->checkbox(array('table' => 'installation', 'field' => 'AcceptedLicence', 'value' => $_REQUEST['installation_AcceptedLicence'])) ?>
                    
                </td>
                <td valign="top">
                    Jeg godtar lisensbestemmelsene<br />
                    <? print $_lib['message']->get_field(array('table' => 'installation', 'field' => 'AcceptedLicence'))  ?>
                </td>
            </tr>            
            <tr>
                <td colspan="2" align="right">
                    <input type="submit" name="action_company_register" accesskey="S" tabindex="17" value="Bestill" />
                </td>
            </tr>
        </table>
        * = P&aring;krevet
    </form>
    <? } ?>
