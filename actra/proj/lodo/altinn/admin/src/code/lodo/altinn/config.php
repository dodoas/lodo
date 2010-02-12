<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
****************************************************************************/
	require_once '_include/class_lodo.php';
	require_once '_include/class_layout.php';
	require_once '_include/class_database.php';
	require_once '_include/class_mva.php';
	require_once '_include/class_config.php';
	require_once '_include/class_package.php';
	require_once '_include/class_schema.php';

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
	if ( $_REQUEST['save']<>'' )
	{
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
		$tmp = $_REQUEST['fagsystemid'];
		if ( $tmp != '' && !is_numeric($tmp) ) {
			$layout->PrintWarning("Du oppgav feil verdi i feltet for fagsystemid.");
			$warning = true;
		} else {
			$data['fagsystemid'] = $tmp;
		}
		$data['batchsubno'] = $_REQUEST['batchsubno'];
		$data['password'] = $_REQUEST['password'];

		if ( $warning == false )
		{
			$sqlStr = "DELETE FROM altinn_config";
			$db->Query( $sqlStr );

			$sqlStr = "INSERT INTO altinn_config " . $db->BuildSQLString( $db->BUILD_INSERT, $data );
			if (!$db->Query( $sqlStr )) {
				$layout->PrintWarning("Kunne ikke lagre konfigurasjonen i databasen.");
			}
			echo("Lagret...<br>\n");
		}
	}

	/*  */
	if (!$warning)
	{
		$config->LoadConfig();
		$data = $config->config;
	}
	$layout->PrintHead( "AltInn" );
	if ( $lodo->inLodo ) {
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/header.inc";
		require_once $_SETUP['HOME_DIR']."/code/lodo/lib/leftmenu.inc";
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
	<td>Batch løpenummer</td>
	<td><input name="batchsubno" value="<?php echo($data['batchsubno']);?>" size="1" maxlength="1" /></td>
</tr>
<tr valign="top">
	<td></td>
	<td><input type="submit" name="save" value="Lagre" /></td>
</tr>
</form>
</table>

<?php
	$db->Disconnect();
?>
