<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Used to set config information in the table altinn_config.
****************************************************************************/
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package.php';
	require_once '_include/class_schema.php';

	/* Init stuff */
	$lodo = new lodo();
	$layout = new Layout( $lodo );
	$db = new Db( $lodo );
	if ( !$db->Connect() ) {
		$layout->PrintError("Kunne ikke koble til databasen.");
		die();
	}

	$config = new Config( $db );
	$mva = new Mva( $db );

	$warning = false;
	/* Save the configuration */
	if ( $_REQUEST['save']<>'' )
	{
		/* Validate bank account info */
		$tmp = $_REQUEST['mvabankaccount'];
		if ($tmp == 0) {
			$tmp = '';
		}
		if ( $tmp != '' && (!is_numeric($tmp) || strlen($tmp) != 11  ) ) {
			$layout->PrintWarning("Du oppgav feil verdi i feltet for bankkonto.");
			$warning = true;
		} else {
			$data['mvabankaccount'] = $tmp;
		}
		/* Validate fag system id */
		$tmp = $_REQUEST['fagsystemid'];
		if ( $tmp != '' && !is_numeric($tmp) ) {
			$layout->PrintWarning("Du oppgav feil verdi i feltet for fagsystemid.");
			$warning = true;
		} else {
			$data['fagsystemid'] = $tmp;
		}
		/* Get the rest of the data */
		$data['batchsubno'] = $_REQUEST['batchsubno'];
		$data['password'] = $_REQUEST['password'];
		$data['termintype'] = $_REQUEST['termintype'];

		/* If everything has validated we can save it */
		if ( $warning == false )
		{
			/* Since there is only one config row we do a quick hack and just delete the previous config.
			   Then we doesn't need to check if there already exists a config */
			$sqlStr = "DELETE FROM altinn_config";
			$db->Query( $sqlStr );

			/* Insert the config */
			$sqlStr = "INSERT INTO altinn_config " . $db->BuildSQLString( $db->BUILD_INSERT, $data );
			if (!$db->Query( $sqlStr )) {
				$layout->PrintWarning("Kunne ikke lagre konfigurasjonen i databasen.");
			}
		}
	}

	/* Load the config unless there was displayed a warning. If a warning was displayed we will not
	   display the config schema. */
	if (!$warning)
	{
		$config->LoadConfig();
		$data = $config->config;
	}
	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {		
	includeinc('top');
	includeinc('left');
    }
?>
<h1>AltInn konfigurasjon</h1>
<table>
<form action="<?php echo($_SERVER['SCRIPT_NAME']);?>" method="post">
<?php echo($lodo->LodoUrlSelf( '', $lodo->LODOURLTYPE_FORM ));?>
<input type="hidden" name="orgno" value="<?php echo($data['orgno']);?>" size="9" maxlength="9" />
<tr valign="top">
	<td>Fagsystem ID<br/>(Oppgitt av AltInn)</td>
	<td><input name="fagsystemid" value="<?php echo($data['fagsystemid']);?>" size="5" maxlength="11" /></td>
</tr>
<tr valign="top">
	<td>Fagsystem passord<br/>(Oppgitt av AltInn)</td>
	<td><input name="password" value="<?php echo($data['password']);?>" size="11" maxlength="30" /></td>
</tr>
<tr valign="top">
	<td>Bankkonto<br/>(11 siffer)</td>
	<td><input name="mvabankaccount" value="<?php echo($data['mvabankaccount']);?>" size="11" maxlength="11" /></td>
</tr>
<tr valign="top">
	<td>Batch l&oslash;penummer</td>
	<td><input name="batchsubno" value="<?php echo($data['batchsubno']);?>" size="1" maxlength="1" /></td>
</tr>
<input type="hidden" name="termintype" value="<?php echo($data['termintype']);?>" />
<?php /*<tr valign="top">
	<td>Termintype</td>
	<td><select name="termintype">
	<?php foreach( $mva->termin as $key => $value ) {
		if ( $value == $data['termintype'] ) {
			echo('<option value=' . $value . ' selected>' . $key);
		}
		else {
			echo('<option value=' . $value . '>' . $key);
		}
	}?>
	</select></td>
</tr> */?>
<tr valign="top">
	<td></td>
	<td><input type="submit" name="save" value="Lagre" /></td>
</tr>
</form>
</table>

<?php
	$db->Disconnect();
?>

