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
<script language="JavaScript">
<!--//
function Calculate()
{   alert ('Anh');
	//tmp = parseInt(document.forms.mvaf.D10097.value * document.forms.mvaf.2701.value / 100);
	//document.forms.mvaf.D10098.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D10098.value) == '' ) {document.forms.mvaf.D10098.value = tmp;}
	//else if ( parseInt(document.forms.mvaf.D10098.value) != tmp) {document.forms.mvaf.D10098.style.color = "red";}
    
	//tmp = parseInt(document.forms.mvaf.D20319.value * 0.11);
	//document.forms.mvaf.D20320.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D20320.value) == '' ) {document.forms.mvaf.D20320.value = tmp;}
	//else if ( parseInt(document.forms.mvaf.D20320.value) != tmp) {document.forms.mvaf.D20320.style.color = "red";}

	//tmp = parseInt(document.forms.mvaf.D14360.value * 0.07);
	//document.forms.mvaf.D14361.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D14361.value) == '' ) {document.forms.mvaf.D14361.value = tmp;}
	//else if ( parseInt(document.forms.mvaf.D14361.value) != tmp) {document.forms.mvaf.D14361.style.color = "red";}

	//tmp = parseInt(document.forms.mvaf.D14362.value * 0.25);
	//document.forms.mvaf.D14363.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D14363.value) == '' ) {document.forms.mvaf.D14363.value = tmp;}
	//else if ( parseInt(document.forms.mvaf.D14363.value) != tmp) {document.forms.mvaf.D14363.style.color = "red";}

	//base = parseInt(document.forms.mvaf.D10096.value);
	//base += parseInt(document.forms.mvaf.D10097.value);
	//base += parseInt(document.forms.mvaf.D20319.value);
	//base += parseInt(document.forms.mvaf.D14360.value);

	//document.forms.mvaf.D8446.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D8446.value) == 0 ) {document.forms.mvaf.D8446.value = base;}
	//else if ( parseInt(document.forms.mvaf.D8446.value) != base) {document.forms.mvaf.D8446.style.color = "red";}

	//document.forms.mvaf.D10095.style.color = "black";
	//if ( parseInt(document.forms.mvaf.D10095.value) == 0 ) {document.forms.mvaf.D10095.value = base;}
	//else if ( parseInt(document.forms.mvaf.D10095.value) != base) {document.forms.mvaf.D10095.style.color = "red";}

	//tmp = parseInt(document.forms.mvaf.D10098.value);
	//tmp += parseInt(document.forms.mvaf.D20320.value);
	//tmp += parseInt(document.forms.mvaf.D14361.value);
	//tmp += parseInt(document.forms.mvaf.D14363.value);
	//tmp -= parseInt(document.forms.mvaf.D8450.value);
	//tmp -= parseInt(document.forms.mvaf.D20322.value);
	//tmp -= parseInt(document.forms.mvaf.D14364.value);

	//mtmp = parseInt(tmp);
	//if (mtmp >= 0) {
		//document.forms.mvaf.D8452.value = 0;
		//document.forms.mvaf.D8453.value = mtmp;
	//}
	//else {
		//document.forms.mvaf.D8452.value = mtmp * -1;
		//document.forms.mvaf.D8453.value = 0;
	//}
}
//-->
</script>
 

<?php  
    global $data;
    global $mvaSatsTable;

	//print ("Anh 2701:". $mvaSatsTable['2701']. "br");
 ?>
<!---?php print_r($data); ?----->

<table>
<tr>
	<td class="m">Hovedoppgave</td>
	<td><input type="radio" name="D5659" value="1"<?php if ( $data['D5659'] == "1" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Korrigert oppgave</td>
	<td><input type="radio" name="D5659" value="3"<?php if ( $data['D5659'] == "3" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
</tr>
<tr>
	<td class="m">Tilleggsoppgave</td>
	<td><input type="radio" name="D5659" value="2"<?php if ( $data['D5659'] == "2" ) {?> checked<?php }?><?php if ( $status>0 ) {?> disabled<?php }?>/></td>
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
	<td><input size=7 name="D8446" value="<?php echo($data[ 'D8446' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>2</b></td>
	<td class="m">Samlet omsetning og uttak innenfor mva-loven. Summen av post 3, 4, 5 og 6. Avgift ikke medregnet</td>

	<td><input size=7 name="D10095" value="<?php echo( $data[ 'D10095' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>3</b></td>
	<td class="m">Omsetning og uttak i post 2 som er fritatt for merverdiavgift</td>
	<td><input size=7 name="D10096" value="<?php echo( $data[ 'D10096' ] );?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>4</b></td>

	<td class="m">Omsetning og uttak i post 2 med h�y sats, og beregnet avgift 25%</td>
	<td><input size=7 name="D10097" value="<?php echo($data[ 'D10097' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2701']); ?>%</td>
	<td align="right">+ <input size=7 class="norm" name="D10098" value="<?php echo($data[ 'D10098' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00</td>
</tr>
<tr>
	<td class="m"><b>5</b></td>
	<td class="m">Omsetning og uttak i post 2 med middels sats, og beregnet avgift 11%</td>

	<td><input size=7 name="D20319" value="<?php echo($data[ 'D20319' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?> />,00 <?php echo($mvaSatsTable['2702']); ?>%</td>
	<td align="right">+ <input size=7 name="D20320" value="<?php echo($data[ 'D20320' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>6</b></td>
	<td class="m">Omsetning og uttak i post 2 med lav
sats, og beregnet avgift 7%</td>
	<td><input size=7 name="D14360" value="<?php echo($data[ 'D14360' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00 <?php echo($mvaSatsTable['2703']); ?>%</td>
	<td align="right">+ <input size=7 name="D14361" value="<?php echo($data[ 'D14361' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>7</b></td>
	<td class="m">Tjenester kj�pt fra utlandet, og
beregnet avgift 25%</td>
	<td><input size=7 name="D14362" value="<?php echo($data[ 'D14362' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
	<td align="right">+ <input size=7 name="D14363" value="<?php echo($data[ 'D14363' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>8</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift,
h�y sats</td>
	<td></td>
	<td align="right">- <input size=7 name="D8450" value="<?php echo($data[ 'D8450' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>9</b></td>
	<td class="m">Fradragsberettiget inng�ende avgift,
middels sats</td>
	<td></td>
	<td align="right">- <input size=7 name="D20322" value="<?php echo($data[ 'D20322' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>10</b></td>
	<td class="m">Fradragberettiget inng�ende avgift,
lav sats</td>
	<td></td>
	<td align="right">- <input size=7 name="D14364" value="<?php echo($data[ 'D14364' ]);?>" align="right"<?php if ( $status>0 ) {?> disabled<?php }?>/>,00</td>
</tr>
<tr>
	<td class="m"><b>11</b></td>

	<td class="m">Avgift til gode</td>
	<td></td>
	<td align="right">= <input size=7 name="D8452" value="<?php echo($data[ 'D8452' ]);?>" align="right" disabled/>,00</td>
</tr>
<tr>
	<td class="m"><b></b></td>

	<td class="m">Avgift � betale</td>
	<td></td>
	<td align="right">= <input size=7 name="D8453" value="<?php echo($data[ 'D8453' ]);?>" align="right" disabled/>,00</td>
</tr>
</table>
