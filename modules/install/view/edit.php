<?
/* $Id: edit.php,v 1.22 2005/11/18 07:35:46 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */
#print_r($_REQUEST); 

$inst           = $_REQUEST['inst'];
$inst_old       = $_REQUEST['inst_old'];
$inst_new       = $_REQUEST['inst_new'];
$InstallationID = $_REQUEST['InstallationID'];
$type           = $_REQUEST['type'];

includelogic('install/install');
require_once "record.inc";

function get_DealerInfo($InstallationID)
{
    global $_lib;
    $query = "select * from installation where InstallationID='" . $InstallationID . "'";
    return $_lib['storage']->get_row(array('query' => $query));
}

function get_customer_menu()
{
    global $_lib;
    $html = "";
    $query = "select * from installation where InstallationID='" . $InstallationID . "'";
    $_lib['storage']->get_row(array('query' => $query));
    return $html;
}

$row = get_DealerInfo($InstallationID);
?>

<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - prosjekter</title>
    <meta name="cvs"                content="$Id: edit.php,v 1.22 2005/11/18 07:35:46 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top'); ?>
<? includeinc('left'); ?>

<?
if($_REQUEST['installation_finished'])
{
    ?>
    <br />
    <br />
    <br />
    <br />
    <b>Du har n&aring; installert: <? print $row->InstallName ?>, og den er klar til bruk.
    <br />
    <br />
    Har du skrevet ned brukernavnet og passordet fra forrige side, kan du logge inn her og opprette nye
    brukere. <a href="/index.php">Klikk her</a></b>
    <?
}
else
{
    ?>
    <form name="<? print $form_name ?>" action="<? print $MY_SELF ?>" method="post">
    <? print $_lib['form3']->hidden(array('name' => 'InstallationID', 'value' => $InstallationID)); ?>
    <? print $_lib['form3']->hidden(array('name' => 'type', 'value' => $type)); ?>
    <table class="lodo_data">
      <? if($type == 'installation') { ?>
      <tr class="result">
        <th colspan="2">Du har valgt &aring; installere fra: <? print $_lib['form3']->select(array('name' => 'inst_copy_from', 'extra' => 'installation.menu', 'value' => 'empatix')) ?></th>
        
      </tr>
      <tr>
        <td colspan="2">
            Navn p&aring; installasjon m&aring; ikke inneholde spesialtegn eller s&aelig;rnorske tegn (&AElig; &Oslash; og &Aring;) og  m&aring; best&aring; av bare store bokstaver, (ikke inneholde bindestrek, mellomrom, semikolon, kolon, komma, punktum og m&aring; begynne med en bokstav). <br />
            <br />
            Du m&aring; ogs&aring; velge et installasjonsnavn som ikke finnes fra f&oslash;r. <br />
            Det vil v&aelig;re lurt &aring; bruke navn som er lett &aring; lese og huske (f.eks KONSULENTVIKAREN, EKDAHL, etc). Dette navnet m&aring; oppgis n&aring;r kunden skal logge inn i sitt regnskap.

      <tr>
        <td class="menu">Navn p&aring; installasjon</td>
        <td>
          <? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'InstallName', 'value' => $row->InstallName)); ?>
		</td>
	  </tr>
      <tr>
        <td colspan="2">N&aring; m&aring; du velge hvilke elementer du vil kopiere fra <? print $inst ?>. Hvis du velger &aring; ikke kopiere noe fra denne installasjonen, er det ogs&aring; ok og alle konfigurasjoner vil v&aelig;re blanke og kan konfigureres opp p&aring; nytt.</td>
	  </tr>
      <tr>
        <td class="menu">Kontoplan oppsett</td>
        <td><? print $_lib['form3']->radiobutton(array('table'=>'install', 'field'=>'accountplan', 'value'=>'1', 'choice'=>'1')) ?> Alle konto - <? print $_lib['form3']->radiobutton(array('table'=>'install', 'field'=>'accountplan', 'value'=>'2')) ?> NS4102
      </tr>
      <tr>
        <td class="menu">MVA</td>
        <td><? print $_lib['form3']->radiobutton(array('table'=>'install', 'field'=>'vat', 'value'=>'1', 'choice'=>'1')) ?> Med MVA - <? print $_lib['form3']->radiobutton(array('table'=>'install', 'field'=>'vat', 'value'=>'2')) ?> Uten MVA
      </tr>
      <tr>
        <td class="menu">Periode oppsett</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'accountperiod')); ?></td>
      </tr>
      <tr>
        <td class="menu">Arbeidsgiveravgift oppsett</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'arbeidsgiveravgift')); ?></td>
      </tr>
      <tr>
        <td class="menu">Valuta oppsett</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'exchange')); ?></td>
      </tr>
      <tr>
        <td class="menu">Prosjekt oppsett</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'project')); ?></td>
      </tr>
      <tr>
        <td class="menu">Avdelingsoppsett
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'department')); ?></td>
      </tr>
      <tr>
        <td class="menu">Firma oppsett</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'company')); ?></td>
      </tr>
      <tr>
        <td class="menu">Kopier l&oslash;nnsmal</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'install', 'field' => 'salaryconf', 'value' => 1)); ?></td>
      </tr>
      <? } ?>
      <tr>
        <th colspan="2">Du m&aring; angi forhandler til denne instalasjonen</th>
      </tr>
      <tr>
        <td class="menu">Forhandler</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'DealerName', 'value' => $row->DealerName, 'width' => '40')) ?></td>
      </tr>
      <tr>
        <td class="menu">E-Post - blir brukt til supporthenvendelser</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'DealerEmail', 'value' => $row->DealerEmail, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <th colspan="2">Du m&aring; oppgi firmaopplysninger</th>
      </tr>
      <tr>
        <td class="menu">Firmanavn</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VName', 'value' => $row->VName, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Adresse</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VAddress', 'value' => $row->VAddress, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Postnummer</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VZipCode', 'value' => $row->VZipCode, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Poststed</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'VCity', 'value' => $row->VCity, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Telefon</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Phone', 'value' => $row->Phone, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Fax</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Fax', 'value' => $row->Fax, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Webadresse</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'WWW', 'value' => $row->WWW, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Organisasjonsnummer</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'CompanyNumber', 'value' => $row->CompanyNumber, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <th colspan="2">Du m&aring; oppgi en bruker for kunden</th>
      </tr>

      <tr>
        <td class="menu">Fornavn</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'FirstName', 'value' => $row->FirstName, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Etternavn</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'LastName', 'value' => $row->LastName, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Mobil</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'MobilePhoneNumber', 'value' => $row->MobilePhoneNumber, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">E-Post - blir brukt som brukernavn</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Email', 'value' => $row->Email, 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Passord</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Password', 'value' => $row->Password, 'width' => '40')); ?></td>
      </tr>
      <? if($type == 'installation') { ?>
      <tr>
        <th colspan="2">Du m&aring; angi administrator brukeren i systemet. Du m&aring; notere deg brukernavnet og passordet. Passordet vil bli kryptert, og det vil ikke g&aring; an &aring; logge inn og administrere regnskapet uten denne brukeren.</th>
      </tr>
      <tr>
        <td class="menu">Fornavn</td>
        <td><? print $_lib['form3']->text(array('name' => 'FirstName', 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Etternavn</td>
        <td><? print $_lib['form3']->text(array('name' => 'LastName', 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Mobil</td>
        <td><? print $_lib['form3']->text(array('name' => 'MobilePhoneNumber', 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">E-Post - blir brukt som brukernavn</td>
        <td><? print $_lib['form3']->text(array('name' => 'Email', 'width' => '40')); ?></td>
      </tr>
      <tr>
        <td class="menu">Passord</td>
        <td><? print $_lib['form3']->password(array('name' => 'Password', 'width' => '40')); ?></td>
      </tr>
      <? } ?>
      <tr>
        <th colspan="2">Oppsett</th>
      </tr>
      <tr>
        <td class="menu">Aktiv</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'installation', 'field' => 'Active', 'value' => $row->Active)); ?></td>
      </tr>
      <tr>
        <td class="menu">Vis p&aring; referanse siden</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'installation', 'field' => 'EnableReference', 'value' => $row->EnableReference)); ?></td>
      </tr>
      <tr>
        <td class="menu">Har akseptert lisens</td>
        <td><? print $_lib['form3']->checkbox(array('table' => 'installation', 'field' => 'AcceptedLicence', 'value' => $row->AcceptedLicence)); ?></td>
      </tr>
      <tr>
        <td class="menu">Opprettet</td>
        <td><? print $_lib['form3']->show(array('table' => 'installation', 'field' => 'CreatedDateTime', 'value' => $row->CreatedDateTime)); ?></td>
      </tr>
      <tr>
        <td class="menu">Installert</td>
        <td><? print $_lib['form3']->show(array('table' => 'installation', 'field' => 'InstalledDateTime', 'value' => $row->InstalledDateTime)); ?></td>
      </tr>
      <tr>
        <td class="menu">Versjon</td>
        <td><? print $_lib['form3']->text(array('table' => 'installation', 'field' => 'Version', 'value' => $row->Version)); ?></td>
      </tr>
      <tr>
        <td class="menu"></td>
        <td colspan="3" align="right"><? print $_lib['form3']->submit(array('name' => 'action_install_update', 'value' => 'Lagre')); ?><? if($type == 'installation') { ?><? print $_lib['form3']->submit(array('name' => 'action_install_db', 'value' => 'Installer')); ?><? } ?></td>
      </tr>
      </form>
    </table>
    <?
}
?>
<? includeinc('bottom') ?>
</body>
</html>
