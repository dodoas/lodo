<?
#table


preg_match('{(.*);(.*)}', $_REQUEST['report_VoucherPeriod'], $m); #Find the pk value  (text or int)

$FromPeriod = $m[1];
$ToPeriod   = $m[2];

##############################################################
#Samlet omsetning
$query_omsetning    = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Active=1  group by VoucherPeriod";
#print "$query_omsetning<br>";
$result_omsetning   = $_lib['db']->db_query($query_omsetning);
$row_omsetning1     = $_lib['db']->db_fetch_object($result_omsetning);
$row_omsetning2     = $_lib['db']->db_fetch_object($result_omsetning);
$omsetning          = $row_omsetning1->AmountIn + $row_omsetning2->AmountIn;

##############################################################
##############################################################
#Fritatt MVA
$query_nomva        = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and VatID=9 and AmountIn > 0  and Active=1 group by VoucherPeriod";
$result_nomva       = $_lib['db']->db_query($query_nomva);
$row_nomva1         = $_lib['db']->db_fetch_object($result_nomva);
$row_nomva2         = $_lib['db']->db_fetch_object($result_nomva);
$nomva              = $row_nomva1->AmountIn + $row_nomva2->AmountIn;

##############################################################
#24% inngŒende MVA
$query_in24mva      = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='24' and AmountIn > 0 and Active=1 group by VoucherPeriod";
$result_in24mva     = $_lib['db']->db_query($query_in24mva);
$row_in24mva1       = $_lib['db']->db_fetch_object($result_in24mva);
$row_in24mva2       = $_lib['db']->db_fetch_object($result_in24mva);
$in24mva            = $row_in24mva1->AmountIn + $row_in24mva2->AmountIn;

##############################################################
#12% inngŒende MVA
$query_in12mva      = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='12' and AmountIn > 0 and Active=1 group by VoucherPeriod";
$result_in12mva     = $_lib['db']->db_query($query_in12mva);
$row_in12mva1       = $_lib['db']->db_fetch_object($result_in12mva);
$row_in12mva2       = $_lib['db']->db_fetch_object($result_in12mva);
$in12mva            = $row_in12mva1->AmountIn + $row_in12mva2->AmountIn;

##############################################################
#6% inngŒende MVA
$query_in6mva       = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='6' and AmountIn > 0 and Active=1 group by VoucherPeriod";
$result_in6mva      = $_lib['db']->db_query($query_in6mva);
$row_in6mva1        = $_lib['db']->db_fetch_object($result_in6mva);
$row_in6mva2        = $_lib['db']->db_fetch_object($result_in6mva);
$in6mva             = $row_in6mva1->AmountIn + $row_in6mva2->AmountIn;

##############################################################
##############################################################
#24% utgŒende MVA
$query_out24mva     = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='24' and AmountOut > 0 and Active=1 group by VoucherPeriod";
$result_out24mva    = $_lib['db']->db_query($query_out24mva);
$row_out24mva1      = $_lib['db']->db_fetch_object($result_out24mva);
$row_out24mva2      = $_lib['db']->db_fetch_object($result_out24mva);
$out24mva           = $row_out24mva1->AmountIn + $row_out24mva2->AmountIn;

##############################################################
#12% utgŒende MVA
$query_out12mva     = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='12' and AmountOut > 0 and Active=1 group by VoucherPeriod";
$result_out12mva    = $_lib['db']->db_query($query_out12mva);
$row_out12mva1      = $_lib['db']->db_fetch_object($result_out12mva);
$row_out12mva2      = $_lib['db']->db_fetch_object($result_out12mva);
$out12mva           = $row_out12mva1->AmountIn + $row_out12mva2->AmountIn;

##############################################################
#6% utgŒende MVA
$query_out6mva      = "select sum(AmountIn) as AmountIn, sum(AmountOut) as AmountOut  from voucher where VoucherPeriod >= '$FromPeriod' and VoucherPeriod <= '$ToPeriod' and Vat='6' and AmountOut > 0 and Active=1 group by VoucherPeriod";
$result_out6mva     = $_lib['db']->db_query($query_out6mva);
$row_out6mva1       = $_lib['db']->db_fetch_object($result_out6mva);
$row_out6mva2       = $_lib['db']->db_fetch_object($result_out6mva);
$out6mva            = $row_out6mva1->AmountIn + $row_out6mva2->AmountIn;

##############################################################
#Sum post 3,4,5,6
$sum3456 = $nomva + $in24mva + $in12mva + $in6mva;

##############################################################
#Sum avgift
$avgift = $in24mva + $in12mva + $in6mva - $out24mva - $out12mva - $out6mva;

?>



<? print $_lib['sess']->doctype ?>
<head>
    <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?> - MVA</title>
    <meta name="cvs"                content="$Id: vat.php,v 1.19 2005/10/24 11:54:33 thomasek Exp $" />
    <? includeinc('head') ?>
</head>
<body  onload="place_cursor();">

<? includeinc('top') ?>
<? includeinc('left') ?>

<h2>Alminnelig omsetnignsoppgave</h2>

<table>
<tr>
    <td>
        <? print $_lib['sess']->get_companydef('CompanyName'); ?><br>
        <? print $_lib['sess']->get_companydef('VAddress'); ?><br>
        <? print $_lib['sess']->get_companydef('VZipCode'); ?><? print $_lib['sess']->get_companydef('VCity'); ?><br>
    <td>
    <td colspan="3">
        Alminnelig omsetnignsoppgave<br>
        Merverdiavgift hovedoppgave<br>
        Termin - Oppgaveperiode <? print $FromPeriod ?> til <? print $ToPeriod ?><br>
        Organisasjonsnummer: <? print $_lib['sess']->get_companydef('OrgNumber'); ?><br>
        Kontonummer: <? print $_lib['sess']->get_companydef('BankAccount'); ?><br>
<tr>
    <td>
    <td>
    <td>Grunnlag
    <td>
    <td>Beregnet avgift

<tr>
    <td>1
    <td>Samlet omsetning og uttak innenfor og utenfor merverdiavgiftsloven(mva-loven). Se veiledning.
    <td><? print $omsetning ?>
<tr>
    <td>2
    <td>Samlet omsetning og uttak innenfor mva-loven. Summen av post 3,4,5 og 6. Avgift ikke medregnet.
    <td><? print $sum ?>
<tr>
    <td>3
    <td>Omsetnign og uttak fra post 2 som er fritatt for merverdiavgift.
    <td><? print $nomva ?>
<tr>
    <td>4
    <td>Omsetning og uttak i post 2 med h&oslash;y sats og beregnet avgift 24%
    <td><? print $in24mva ?>
    <td>+
    <td><? print $in24mva ?>
<tr>
    <td>5
    <td>Omsetning og uttak i post 2 med middels sats og beregnet avgift 12%
    <td><? print $in12mva ?>
    <td>+
    <td><? print $in12mva ?>
<tr>
    <td>6
    <td>Omsetning og uttak i post 2 med lav sats og beregnet avgift 6%
    <td><? print $in6mva ?>
    <td>+
    <td><? print $in6mva ?>
<tr>
    <td>7
    <td>Beregningsgrunnlag for tjenester kj&oslash;pt i utlandet og beregnet avgift 24%
    <td>
    <td>+
<tr>
    <td>8
    <td>Fradragsberettiget inng&aring;ende avgift h&oslash;y sats
    <td>
    <td>-
    <td><? print $out24mva ?>
<tr>
    <td>9
    <td>Fradragsberettiget inng&aring;ende avgift middels sats
    <td>
    <td>-
    <td><? print $out12mva ?>
<tr>
    <td>10
    <td>Fradragsberettiget inng&aring;ende avgift lav sats
    <td>
    <td>-
    <td><? print $out6mva ?>
<tr>
    <td>11
    <td>Sum avgift. Kryss av dersom du har avgift til gode
    <td><input type="checkbox" <? if($avgift < 0) { ?>checked<? } ?>>
    <td>=
    <td><? print $avgift ?>
<tr>
    <td>
    <td>Eventuelle forklaringer til oppgaven sendt fylkesskattekontoret (se veiledningen)
    <td colspan="3">Ja <input type="checkbox"> Nei <input type="checkbox" checked>

</table>



</body>
</html>