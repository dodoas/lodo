<?php
/*
 * Created on 07.sep.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>


<form enctype="multipart/form-data" action="?t=aarsoppgjoer.index&a=csvsubmit" method="post">
	<p>Først må du eksportere excel-regnearket ditt til en kommaseparert fil. Dette gjør du slik:</p>
	<p>...</p>
	<p>
	Velg format:<br />
	<select id="format" name="format">
		<option value="semikndkdk">Semikolon-separert: Kontonummer;Kontonavn;Debet dette år;Kredit dette år;Debet foregående år;Kredit foregående år</option>
		<!--
		<option value="semikdkdk">Semikolon-separert: Kontonummer;Debet periodeslutt;Kredit periodeslutt;Debet p.start;Kredit p.start</option>
		<option value="semiknvv">Semikolon-separert: Kontonummer;Kontonavn;Periodeslutt;Periodestart</option>
		<option value="semikvv">Semikolon-separert: Kontonummer;Periodeslutt;Periodestart</option>
		-->
	</select>
		<p>NB: Overser kontonavnet.</p>
	</p>
	
	<p>
	Velg fil:<br />
	<input type="file" id="upfile" name="upfile" id="upfile" />
	</p>
	
	<p>
		<input name="submitknapp" id="submitknapp" type="submit" value="submit" />
	</p>
</form>
