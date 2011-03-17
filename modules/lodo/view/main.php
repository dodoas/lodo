<?
/* $Id: main.php,v 1.58 2005/10/28 17:59:40 thomasek Exp $ main.php,v 1.12 2001/11/20 17:55:12 thomasek Exp $ */

$db_table = "setup";
if($_SETUP['VERSION'] == 1) {
	require_once $_SETUP['HOME_DIR']."/interface_custom/lodo1/modules/vat/view/record.inc";
} else {
    require_once $_SETUP['HOME_DIR']."/modules/vat/view/record.inc";
}

$query_setup    = "select name, value from setup";
$setup          = $_lib['storage']->get_hash(array('query' => $query_setup, 'key' => 'name', 'value' => 'value'));

$query2         = "select AccountPlanID, AccountName, ReskontroAccountPlanType from accountplan where EnableReskontro=1 and Active=1 order by AccountPlanID asc";
$result2        = $_lib['db']->db_query($query2);

print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?></title>
    <meta name="cvs"                content="$Id: main.php,v 1.58 2005/10/28 17:59:40 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2 class="groupheader">Forside</h2>

<h3><a href="http://sourceforge.net/tracker2/?func=add&group_id=167033&atid=841330">Rapporter feil</a></h3>

    <table class="group">
        <tr>
            <th>Firma</th>
            <th>Kontoplan</th>
            <th>Oppsett</th>
            <th>Produkt</th>
            <th>Annet</th>
            <th>Rapporter</th>
		</tr>
        <tr valign="top">
            <td>
                <a href="<? print $_lib['sess']->dispatch ?>t=company.edit&CompanyID=<? print $_SETUP[COMPANY_ID]; ?>">Firmaopplysning</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=employee">Ansatte</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=company.employees&CompanyID=<? print $_SETUP[COMPANY_ID] ?>">Systembrukere</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=department.list">Avdeling/Bil</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=project.list">Prosjekt</a>
            </td>
            <td>
                <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=hovedbok"><b>Hovedbokskontoer</b></a>
				<a href="lodo.php?SID=m1p2l0r9mrfhhkqrkqhn1s0d82&amp;view_mvalines=&amp;view_linedetails=&amp;t=accountplan.list&accountplan_type=balance">Hovedbok balanse</a>
			   <a href="lodo.php?SID=m1p2l0r9mrfhhkqrkqhn1s0d82&amp;view_mvalines=&amp;view_linedetails=&amp;t=accountplan.list&accountplan_type=result">Hovedbok resultat</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=reskontro"><b>Reskontroer</b></a>
                <?
                while($row2 = $_lib['db']->db_fetch_object($result2))
                {
                    ?><a href="<? print $_lib['sess']->dispatch ?>t=accountplan.list&accountplan_type=<? print $row2->ReskontroAccountPlanType ?>" title="Reskontro"><? print $row2->AccountPlanID ?> - <? print $row2->AccountName ?></a><?
                }
                ?>
            </td>
            <td>
                <a href="<? print $_lib['sess']->dispatch ?>t=vat.edit">Merverdiavgift</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=kommune.edit">Kommune</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=arbeidsgiveravgift.edit">Arbeidsgiveravgift</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=exchange.edit">Valuta</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=accountperiod.edit">Periode</a>
                <a href="<? print $_lib['sess']->dispatch ?>t=linetextmap.list">Linjenr til Tekst</a>
				<?
                if($_lib['sess']->get_person('AccessLevel') > 3 || true)
                {
                ?><a href="<? print $_lib['sess']->dispatch ?>t=altinn.config">AltInn</a><?
                }
                if($_lib['sess']->get_person('AccessLevel') > 2)
                {
                    ?><a href="<? print $_lib['sess']->dispatch ?>t=timereg.holidays">Helligdager</a><?
                }
				?>
            </td>
            <td>
                <? if($_SETUP['MODE'] != 'empatix1') { ?>
                <a href="<? print $_lib['sess']->dispatch ?>t=product.list">Produkt register</a>
                <? } else { ?>
                Produktregisteret er i Empatix
                <? } ?>
                <?
                if($_lib['sess']->get_person('AccessLevel') > 3 || true)
                {
                ?><a href="<? print $_lib['sess']->dispatch ?>t=borettslag.leiligheter">Borettslag</a><?
                }
                ?>
			  <a href="<? print $_lib['sess']->dispatch ?>t=timereg.index">Timereg</a>
            </td>
            <td>
                <a href="<? print $_lib['sess']->dispatch ?>t=setup.edit">Konfigurasjon/Oppsett</a>
			  <a href="<? print $_lib['sess']->dispatch ?>t=filarkiv.index">Dokumentarkiv</a>
			  <? if($_lib['sess']->get_person('AccessLevel') >= 3) { ?>
			  <a href="<? print $_lib['sess']->dispatch ?>t=aarsoppgjoer.index" target="_new">&Aring;rsoppgj&oslash;r</a>
			  <? } ?>
				<a href="<? print $_lib['sess']->dispatch ?>t=documentation.list">Brukerveiledning</a>
            </td>
			<td>
			  <a href="<? print $_lib['sess']->dispatch ?>t=report.list">Rapporter</a>
			  <a href="<? print $_lib['sess']->dispatch ?>t=altinn.index">AltInn</a>
			  <? if($_lib['sess']->get_person('AccessLevel') > 3) { ?>
			  <a href="<? print $_lib['sess']->dispatch ?>t=install.list">Installer</a>
			  <? } ?>
			</td>
        </tr>
    </table>
<?
if(ini_get('register_globals')) {
   print "<b>Fatal:</b> Empatix requires that register_globals in php.ini is disabled.<br>";
}
?>
<? includeinc('bottom') ?>
</body>
</html>
