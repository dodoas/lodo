<?
# $Id: bedriftensstatus.php,v 1.5 2005/01/30 12:35:04 thomasek Exp $ invoice_list.php,v 1.4 2001/11/20 17:55:12 thomasek Exp $
# Based on EasyComposer technology
# Copyright Thomas Ekdahl, 1994-2005, thomas@ekdahl.no, http://www.ekdahl.no
?>


    <? print $_lib['sess']->doctype ?>
<head>
        <title>Forventet flyt</title>
        <meta name="cvs"                content="$Id: bedriftensstatus.php,v 1.5 2005/01/30 12:35:04 thomasek Exp $" />
        <? include $_SETUP[HOME_DIR] . "/code/lib/html/head.inc"; ?>
    </head>
<body>
    <? include "$_SETUP[HOME_DIR]/code/lodo/lib/header.inc"; ?>
    <? include "$_SETUP[HOME_DIR]/code/lodo/lib/leftmenu.inc"; ?>

    <table border=0 cellspacing="0" width="550">
        <thead>
            <tr>
                <th>Periode
                <th>Privatuttak
                <th>Overskudd
                <th>Skatt 35%
                <th>Netto
                <th>Resultat
        <tbody>
            <tr>
                <td>Januar
                <td>amountout
                <td>amountin
                <td>amountin * skattprosent
                <td>amountin - skatt
                <td>netto - amountout
        <tfoot>
            <tr>
                <td>sum
                <td>*
                <td>*
                <td>*
                <td>*
                <td>*
    </table>
    <br>
    <table>
        <?
        $j = "Bet skatt:";
        for($i=1; $i<=4; $i++)
        {
        ?>
        <tr>
            <td><? print $j ?>
            <td><? print $i ?>.Term
            <td>*
        <?
            $j="";
        }
        ?>
        <tr>
            <td colspan=2>Forskjell virkelig og beregnet skatt
            <td>*
    </table>
    <table>
        <tr>
            <td>
            <td>
            <td>bokført mnd
            <td>året
            <td>
        <tr>
            <td>Stipulert privatforbruk
            <td>sum privatuttak
            <td>mnd nr
            <td>?
            <td>sum privatuttak
        <tr>
            <td>Stipulert overskudd
            <td>sum overskudd
            <td>mnd nr
            <td>?
            <td>sum overskudd
        <tr>
            <td>Økning av bedriftens verdi
            <td>
            <td>
            <td>
            <td>sum overskudd - sum privatuttak
    </table>
</body>
</html>