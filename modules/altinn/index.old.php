<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Prints out MVA termins of the user in order to let the user do things
** with the termins, like sending a report to AltInn.
****************************************************************************/
    require_once '_include/class_lodo.php';
    require_once '_include/class_layout.php';
    require_once '_include/class_database.php';
    require_once '_include/class_mva.php';
    require_once '_include/class_config.php';

    $lodo = new lodo();
    $db = new Db( $lodo );
    if ( !$db->Connect() ) {
        $layout->PrintError("Kunne ikke koble til databasen.");
        die();
    }

    $layout = new Layout( $lodo );
    $mva = new Mva( $db );
    $config = new Config( $db );

    /* Configure range for years. Default is the current year. */
    $yearLow = 2004;
    $yearHigh = date("Y");
    $year = $_REQUEST['year'];
    if ($year == '' || !is_numeric($year)) {$year = date("Y");}

    $layout->PrintHead( "AltInn" );
    if ( $lodo->inLodo ) {
        includeinc('top');
        includeinc('left');
    }
?>
<h1>Altinnn-skjemaer</h1>

<p>

<table>
<tr>
    <th>Skjema nr</th>
    <th>Beskrivelse</th>
    <th>Kortnavn</th>
</tr>
<tr>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=altinn.termins">RF-0002</a></td>
    <td>Alminnelig omsetningsoppgave</td>
    <td>MVA Oppgave</td>
</tr>
<tr>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=altinn.neringsoppgave2">RF-1167</a></td>
    <td>N&aelig;ringsoppgave for aksjeselskap</td>
    <td>N&aelig;ringsoppgave 2</td>
</tr>
<tr>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=altinn.neringsoppgave1">RF-1175</a></td>
    <td>N&aelig;ringsoppgave for enkeltmannsforetak</td>
    <td>N&aelig;ringsoppgave 1</td>
</tr>
<tr>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=altinn.selvangivelse2">RF-1028</a></td>
    <td>Selvangivelse for aksjeselskap</td>
    <td>Selvangivelse AS</td>
</tr>
<tr>
    <td><a href="<? print $_SETUP['DISPATCH'] ?>t=altinn.selvangivelse1">RF-1027</a></td>
    <td>Selvangivelse for n&aelig;ringsdrivende</td>
    <td>Selvangivelse</td>
</tr>
</table>
<?php
    $layout->PrintFoot(  );

    $db->Disconnect();
?>
