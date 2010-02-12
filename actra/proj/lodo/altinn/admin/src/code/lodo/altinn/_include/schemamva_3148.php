<?php
/****************************************************************************
** Copyright (c) 2005 Actra AS.
** All rights reserved!
**
** Developed by Gunnar Skeid (gunnar@actra.no)
**
** Handles package editing.
****************************************************************************/

?>
<script>
<!--//
function DoForm()
{
	document.forms.mvaf.d8446.value = document.forms.mvaf.d10095.value;
	document.forms.mvaf.d10098.value = parseInt(document.forms.mvaf.d10097.value * 0.25);
	document.forms.mvaf.d20320.value = parseInt(document.forms.mvaf.d20319.value * 0.11);
	document.forms.mvaf.d14361.value = parseInt(document.forms.mvaf.d14360.value * 0.07);
	document.forms.mvaf.d14363.value = parseInt(document.forms.mvaf.d14362.value * 0.25);

	base = parseInt(document.forms.mvaf.d10096.value);
	base += parseInt(document.forms.mvaf.d10097.value);
	base += parseInt(document.forms.mvaf.d20319.value);
	base += parseInt(document.forms.mvaf.d14360.value);
	document.forms.mvaf.d8446.value = document.forms.mvaf.d10095.value = parseInt(base);

	tmp = parseInt(document.forms.mvaf.d10098.value);
	tmp += parseInt(document.forms.mvaf.d20320.value);
	tmp += parseInt(document.forms.mvaf.d14361.value);
	tmp += parseInt(document.forms.mvaf.d14363.value);
	tmp -= parseInt(document.forms.mvaf.d8450.value);
	tmp -= parseInt(document.forms.mvaf.d20322.value);
	tmp -= parseInt(document.forms.mvaf.d14364.value);

	mtmp = parseInt(tmp);
	if (mtmp >= 0) {
		document.forms.mvaf.d8452.value = 0;
		document.forms.mvaf.d8453.value = mtmp;
	}
	else {
		document.forms.mvaf.d8452.value = mtmp * -1;
		document.forms.mvaf.d8453.value = 0;
	}
}
//-->
</script>
<table>
<tr>
	<td class="m">Hovedoppgave</td>
	<td><input type="radio" name="d5659" value="1"<?php if ( $data['d5659'] == "1" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Korrigert oppgave</td>
	<td><input type="radio" name="d5659" value="3"<?php if ( $data['d5659'] == "3" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
</table>

<table>
<tr>
	<td></td>
	<td></td>
	<th>Grunnlag</th>
	<th>Beregnet avgift</th>
</tr>
<tr>
	<td class="m"><b>1</b></td>
	<td class="m">Samlet omsetning og uttak innenfor og utenfor merverdiavgiftsloven (mva-loven)</td>
	<td><input size=7 name="d8446" value="<?php echo($data[ 'd8446' ]);?>" align="right" onchange="DoForm();" disabled />,00</td>
</tr>
<tr>
	<td class="m"><b>2</b></td>
	<td class="m">Samlet omsetning og uttak innenfor mva-loven. Summen av post 3, 4, 5 og 6. Avgift ikke medregnet</td>

	<td><input size=7 name="d10095" value="<?php echo( $data[ 'd10095' ] );?>" align="right" onchange="DoForm();" disabled />,00</td>
</tr>
<tr>
	<td class="m"><b>3</b></td>
	<td class="m">Omsetning og uttak i post 2 som er fritatt for merverdiavgift</td>
	<td><input size=7 name="d10096" value="<?php echo( $data[ 'd10096' ] );?>" value="0" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>4</b></td>

	<td class="m">Omsetning og uttak i post 2 med høy sats, og beregnet avgift 25%</td>
	<td><input size=7 name="d10097" value="<?php echo($data[ 'd10097' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
	<td align="right">+ <input size=7 name="d10098" value="<?php echo($data[ 'd10098' ]);?>" align="right" disabled />,00</td>
</tr>
<tr>
	<td class="m"><b>5</b></td>
	<td class="m">Omsetning og uttak i post 2 med middels sats, og beregnet avgift 11%</td>

	<td><input size=7 name="d20319" value="<?php echo($data[ 'd20319' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
	<td align="right">+ <input size=7 name="d20320" value="<?php echo($data[ 'd20320' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b>6</b></td>
	<td class="m">Omsetning og uttak i post 2 med lav
sats, og beregnet avgift 7%</td>
	<td><input size=7 name="d14360" value="<?php echo($data[ 'd14360' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
	<td align="right">+ <input size=7 name="d14361" value="<?php echo($data[ 'd14361' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b>7</b></td>
	<td class="m">Tjenester kjøpt fra utlandet, og
beregnet avgift 25%</td>
	<td><input size=7 name="d14362" value="<?php echo($data[ 'd14362' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
	<td align="right">+ <input size=7 name="d14363" value="<?php echo($data[ 'd14363' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b>8</b></td>
	<td class="m">Fradragsberettiget inngående avgift,
høy sats</td>
	<td></td>
	<td align="right">- <input size=7 name="d8450" value="<?php echo($data[ 'd8450' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>9</b></td>
	<td class="m">Fradragsberettiget inngående avgift,
middels sats</td>
	<td></td>
	<td align="right">- <input size=7 name="d20322" value="<?php echo($data[ 'd20322' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>10</b></td>
	<td class="m">Fradragberettiget inngående avgift,
lav sats</td>
	<td></td>
	<td align="right">- <input size=7 name="d14364" value="<?php echo($data[ 'd14364' ]);?>" align="right" onchange="DoForm();"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>11</b></td>

	<td class="m">Avgift til gode</td>
	<td></td>
	<td align="right">= <input size=7 name="d8452" value="<?php echo($data[ 'd8452' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b></b></td>

	<td class="m">Avgift å betale</td>
	<td></td>
	<td align="right">= <input size=7 name="d8453" value="<?php echo($data[ 'd8453' ]);?>" align="right" disabled/>,00</td>
</tr>
</table>
