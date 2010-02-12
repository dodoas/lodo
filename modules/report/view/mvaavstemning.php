<?
# $Id: mvaavstemning.php,v 1.14 2005/10/24 11:54:33 thomasek Exp $ invoice_edit.php,v 1.7 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no

$tull1 = 12;

//$_lib['sess']->Debug($tull1);
?>
<? print $_lib['sess']->doctype ?>
<head>
        <title>Empatix - <? print $_lib['sess']->get_companydef('CompanyName') ?> : <? print $_lib['sess']->get_person('FirstName') ?> <? print $_lib['sess']->get_person('LastName') ?></title>
        <meta name="cvs"                content="$Id: mvaavstemning.php,v 1.14 2005/10/24 11:54:33 thomasek Exp $" />
        <? includeinc('head') ?>
    </head>

<body>

<? includeinc('top') ?>
<? includeinc('left') ?>

<table border="0" cellspacing="0" width="900">
    <thead>
        <tr>
            <th colspan="3">I følge bokført regnskap
        <tr>
            <td>Mnd/Termin
            <td align="center">Total omsettning
            <td align="center">Fri omsettning
            <td align="center">Grl for 24% Pl.omsettn
            <td align="center">24% Utg MVA
            <td align="center">Grl for 12% Pl.omsettn
            <td align="center">12% Utg MVA
            <td align="center">24% MVA Ing MVA
            <td align="center">12% MVA Ing MVA
            <td align="center">MVA
    <tbody>
        <?
        for($i=1; $i<=12; $i++)
        {
            ?><tr><td><?
            print $_lib['format']->MonthToText(array('value'=>$i, 'return'=>'value'));
        }
        ?>
        <tr>
            <td>SUM
        <tr>
            <td>.
        <tr height="20">
            <td>
</table>
<table border="0" cellspacing="0" width="900">
    <thead>
        <tr>
            <th colspan="3">I følge innsendte oppgaver
        <tr>
            <td>Mnd/Termin
            <td align="center">Total omsettning
            <td align="center">Fri omsettning
            <td align="center">Grl for 24% Pl.omsettn
            <td align="center">24% Utg MVA
            <td align="center">Grl for 12% Pl.omsettn
            <td align="center">12% Utg MVA
            <td align="center">24% MVA Ing MVA
            <td align="center">12% MVA Ing MVA
            <td align="center">MVA
    <tbody>
        <?
        for($i=1; $i<=12; $i++)
        {
            print "<tr><td>";
            print $_lib['format']->MonthToText(array('value'=>$i, 'return'=>'value'));
            for($j=0; $j<=8; $j++)
            {
                print "<td align=\"center\">".$_lib['form3']->text(array('table'=>'budsjett', 'field'=>"$i.$j", 'value'=>'0.00', 'width'=>'5'));
            }
        }
        ?>
        <tr>
            <td>SUM
        <tr>
            <td>.
        <tr height="20">
            <td>
    <tfoot>
        <tr>
            <td>Diff
        <tr height="5">
            <td>
        <tr>
            <td>Sum + Diff
</table>

</body>
</html>