<?
#table
require_once  "formatNumber.inc";
$CompanyID = $_REQUEST['CompanyID'];
if(!$CompanyID) { $CompanyID = 1; }

$query = "select * from company where CompanyID='$CompanyID'";
$row_c = $_dbh[$_dsn]->get_row(array('query' => $query));
$query2 = "select * from borettslag where CompanyID='$CompanyID'";
$row_b = $_dbh[$_dsn]->get_row(array('query' => $query2));
$query3 = "select * from borettslagarsoppgjor where BorettslagID='" . $row_b->BorettslagID . "' and Arstall = '" . $_REQUEST['arstall'] . "';";
$row_ba= $_dbh[$_dsn]->get_row(array('query' => $query3));



?>
<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - raport</title>
    <meta name="cvs"                content="$Id: dagbok.php,v 1.32 2005/02/24 08:46:16 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body onload="window.focus();">
<h2><? print $_lib['sess']->get_companydef('VName') ?></h2>
<p>Noteopplysning <? print $row_ba->Arstall; ?><br/>
Poster i selvangivelsen</p>
  <table>
    <tr>
      <th>Sek-<br/>sjoner</th>
      <th>Navn</th>
      <th>Kvadrat</th>
      <th>Andel-<br/>brev</th>
      <th>Borett-<br/>innskudd</th>
      <th>Mnd<br/>bo</th>
      <th>Husleie</th>
      <th>Prosent-<br>inntekt<br/>po 2.8.1</th>
      <th>Andel-<br>inntekt<br/>po 2.8.3</th>
      <th>Andel-<br>utgifter<br/>po 3.3.4</th>
      <th>Andel <br>ligningv<br/>po 4.3.1</th>
      <th>Andel<br>formue<br/>po 4.5.3</th>
      <th>Andel<br>gjeld<br/>po 4.8.2</th>
      <th>Kost-<br>pris</th>
    </tr>
<?
//$selectFaktura = "select sum(iol.UnitCustPrice) from invoiceout io, invoiceoutline iol where io.InvoiceOutID = iol.InvoiceOutID and io.InvoiceCompanyID = " . $row_e->AccountPlanID . " and iol.ProductID = '" . $row_b->ProductID1 . "' and io.InvoiceDate >= '" . $row_ba->Arstall . "-01-01' and io.InvoiceDate <= '" . $row_ba->Arstall . "-12-31';";
$selectFaktura = "select sum(iol.UnitCustPrice) from invoiceout io, invoiceoutline iol where io.InvoiceOutID = iol.InvoiceOutID and iol.ProductID = '" . $row_b->ProductID1 . "' and io.InvoiceDate >= '" . $row_ba->Arstall . "-01-01' and io.InvoiceDate <= '" . $row_ba->Arstall . "-12-31';";
$selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
$row_f_all = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
$husleie_totalt = $row_f_all[0];

$query = "select * from leilighet where BorettslagID = " . $row_b->BorettslagID . " order by Seksjonsnr;";
$query_handler = $_dbh[$_dsn]->db_query($query);
while ($aRow = $_dbh[$_dsn]->db_fetch_object($query_handler))
{
    $selectEier = "select a.AccountName, e.* from eierforhold e, accountplan a where e.AccountPlanID = a.AccountPlanID and e.LeilighetID = '" . $aRow->LeilighetID . "' order by FraDato desc;";
    $result_e = $_dbh[$_dsn]->db_query($selectEier);
    while($row_e = $_dbh[$_dsn]->db_fetch_object($result_e))
    {
        list($Far, $Fmaned, $Fdag) = split("-", $row_e->FraDato);
        list($Tar, $Tmaned, $Tdag) = split("-", $row_e->TilDato);
        if ($Far <= $row_ba->Arstall && ($Tar >= $row_ba->Arstall || $Tar == "0000") && $Far != "0000")
        {
        if ($Far < $row_ba->Arstall){ $Fmaned = 01; $Fdag = 01;}
        if ($Tar == "0000") {$Tmaned = 12; $Tdag = 31;}
        $Fts = strtotime ($row_ba->Arstall . "-" . $Fmaned . "-" .$Fdag);
        $Tts = strtotime ($row_ba->Arstall . "-" . $Tmaned . "-" .$Tdag) + (60*60*24);
        $MaxTs = strtotime ($row_ba->Arstall . "-12-31") + (60*60*24);
        $diff = $Tts - $Fts;
        $boMander = round($diff / (60*60*24*30.416), 0);

        $selectFaktura = "select sum(iol.UnitCustPrice) from invoiceout io, invoiceoutline iol where io.InvoiceOutID = iol.InvoiceOutID and io.InvoiceCompanyID = " . $row_e->AccountPlanID . " and iol.ProductID = '" . $row_b->ProductID1 . "' and io.InvoiceDate >= '" . $row_ba->Arstall . "-01-01' and io.InvoiceDate <= '" . $row_ba->Arstall . "-12-31';";
        $selectFaktura_handle = $_dbh[$_dsn]->db_query($selectFaktura);
        $row_f = $_dbh[$_dsn]->db_fetch_array($selectFaktura_handle);
        $husleie_eier = $row_f[0];
?>
    <tr>
      <td><? print $aRow->Seksjonsnr; ?></td><!-- Seksjoner -->
      <td><? print $row_e->AccountName; ?></td><!-- Navn -->
      <td><? print formatNumber($row_e->Kvadrat); ?></td><!-- Kvadrat -->
      <td><? if ($row_e->TilDato == "0000-00-00") print formatNumber($row_e->Andelsbrev); else print "0"; ?></td><!-- Andel brev -->
      <td><? if ($row_e->TilDato == "0000-00-00") print formatNumber($row_e->BorettInnskudd); else print "0"; ?></td><!-- Borett innskudd -->
      <td><? print $boMander; ?></td><!-- Mnd<br/>bo -->
      <td><? print formatNumber($husleie_eier); ?></td><!-- Husleie -->
      <!-- <td><? print $row_e->Produkt1 * 12; ?></td> Husleie -->
      <td><? print formatNumber(($row_ba->ProsentInntekt / $row_b->Kvadrat) * $row_e->Kvadrat); ?></td><!-- Prosent inntekt --> <!-- $row_b->Kvadrat skulle vært $row_ba->Kvadrat -->
      <td><? print formatNumber($row_ba->Inntekter / $husleie_totalt * $husleie_eier); ?></td><!-- Andel inntekt -->
      <td><? print formatNumber($row_ba->Utgifter / $husleie_totalt * $husleie_eier); ?></td><!-- Andel utgifter -->
      <td><? if ($MaxTs == $Tts) print formatNumber(($row_ba->ProsentInntekt / $row_b->Kvadrat) * $row_e->Kvadrat); else print formatNumber(0); ?></td><!-- Andel ligningv -->
      <td><? if ($MaxTs == $Tts) print formatNumber(($row_ba->Formue / $row_b->Kvadrat) * $row_e->Kvadrat); else print formatNumber(0); ?></td><!-- Andel formue -->
      <td><? if ($MaxTs == $Tts) print formatNumber(($row_ba->Gjeld / $row_b->Kvadrat) * $row_e->Kvadrat); else print formatNumber(0); ?></td><!-- Andel gjeld -->
      <td><? if ($MaxTs == $Tts) print formatNumber(($row_ba->Kostpris / $row_b->Kvadrat) * $row_e->Kvadrat); else print formatNumber(0); ?></td><!-- Kostpris -->
    </tr>
<?
        }
    }
}
?>
    <tr>
      <td colspan="14">&nbsp;</td>
    </tr>
    <tr>
      <td>Sum</td><!-- Seksjoner -->
      <td>&nbsp;</td><!-- Navn -->
      <td><? print formatNumber($row_b->Kvadrat); ?></td><!-- Kvadrat -->
      <td><? print formatNumber($row_b->Andelsbrev); ?></td><!-- Andel brev -->
      <td><? print formatNumber($row_b->BorettInnskudd); ?></td><!-- Borett innskudd -->
      <td>&nbsp;</td><!-- Mnd<br/>bo -->
      <td><? print $husleie_totalt; ?></td><!-- Husleie -->
      <td><? print formatNumber($row_ba->ProsentInntekt); ?></td><!-- Prosent inntekt --> <!-- $row_b->Kvadrat skulle vært $row_ba->Kvadrat -->
      <td><? print formatNumber($row_ba->Inntekter); ?></td><!-- Andel inntekt -->
      <td><? print formatNumber($row_ba->Utgifter); ?></td><!-- Andel utgifter -->
      <td><? print formatNumber($row_ba->ProsentInntekt); ?></td><!-- Andel ligningv -->
      <td><? print formatNumber($row_ba->Formue); ?></td><!-- Andel formue -->
      <td><? print formatNumber($row_ba->Gjeld); ?></td><!-- Andel gjeld -->
      <td><? print formatNumber($row_ba->Kostpris); ?></td><!-- Kostpris -->
    </tr>
  </table>


</body>
</html>
