<div>
<p style="text-align: center;"><a href="<? print $_lib['sess']->dispatch ?>page=regnskap">Hva er et regnskap?</a> | <a href="<? print $_lib['sess']->dispatch ?>page=bokholderi">Dobbelt bokholderi</a> | <a href="<? print $_lib['sess']->dispatch ?>page=balanse">Balanse og resultat</a> | Eksempel</p>
</div>

<h2>Eksempler ved bruk av Lodo</h2>

<p>En person kjøper varer for <span style="font-weight: bold; color: red;">kr 400,-</span> (rød)</p>
<p>Den samme vare blir solgt for <span style="font-weight: bold; color: blue;">kr 600,-</span>  (blå)</p>
<p>Da har personen tjent kr 200,-</p>

<table width="80%" border="0" cellpadding="9" cellspacing="0">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="borderleft">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
    <tr>
        <th>Balanse</th>
        <td>&nbsp;</td>
        <td class="borderleft">&nbsp;</td>
        <th colspan="2">Resultat</th>
    </tr>
    
    <tr>
        <td align="center">
	    		<table border="1" cellpadding="5" cellspacing="0">
        		<tr>
        		<th colspan="2">Konto 1900<br />Kassa<br /><br /></th>        		
        		</tr>
        		<tr>
        		<td class="head"><strong>Debet</strong><br />Inn</td>
        		<td class="head"><strong>Kredit</strong><br />Ut</td>
        		</tr>
        		<tr>
                <td style="text-align: right; color: blue; font-weight: bold;" class="baldebet"><br /><br />600<br /></td>
                <td style="text-align: right; color: red; font-weight: bold;" class="balkredit"><br />400<br /><br /></td>
        		</tr>
        		</table>
	    </td>	
        <td>&nbsp;</td>
        <td class="borderleft">&nbsp;</td>
		<td align="right">
				   <table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 3000<br />Salg<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head"><strong>Debet</strong><br />Utgifter</td>
        			<td class="head"><strong>Kredit</strong><br />Inntekter</td>
        			</tr>
        			<tr>
        			<td class="resdebet">&nbsp;</td>
        			<td style="text-align: right; color: blue; font-weight: bold;" class="reskredit"><br /><br />600<br /></td>
        			</tr>
        		</table>

		</td>
		<td>
			      <table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 4000<br />Varekjøp<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head"><strong>Debet</strong><br />Utgifter</td>
        			<td class="head"><strong>Kredit</strong><br />Inntekter</td>
        			</tr>
        			<tr>
                    <td style="text-align: right; color: red; font-weight: bold;" class="resdebet"><br />400<br /><br /></td>
        			<td class="reskredit">&nbsp;</td>
        			</tr>        		
        		</table>

		</td>
</tr>

<tr>
    <td align="center">Sum balanse = 200 <br />Dette er resultatet i en gitt tidsintervall.</td>
    <td>&nbsp;</td>
    <td class="borderleft">&nbsp;</td>
    <td align="center" colspan="2">Sum resultat = 200<br />Dette er resultatet i en gitt tidsintervall.</td>
</tr>
</table>

<p>Det er dette som kalles dobbelt bokholderi. Bilags transaksjonene registreres på to steder 
og differansen i balansen og resultat er det samme.</p>

<h3>Hvordan resultatene skal presenteres</h3>

<p>Når man presenterer regnskapet til myndighetene, skal resultatene foreligge på følgende måte:</p>

<table width="40%" border="1" cellpadding="7" cellspacing="1">
    <tr>
        <td class="head"><strong>Balanse</strong></td>
        <td>Kasse</td>
        <td>200</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>Overskudd</td>
        <td>&nbsp;</td>
        <td>200</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>Sum</td>
        <td>200</td>
        <td>200</td>
    </tr>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>
    <tr>
        <td class="head"><strong>Resultat</strong></td>
        <td>Salg</td>
        <td>&nbsp;</td>
        <td>600</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>Varekjøp</td>
        <td>400</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>Overskudd</td>
        <td>200</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>Sum</td>
        <td>600</td>
        <td>600</td>
    </tr>
</table>

<h3>Hvordan det gjøres med Lodo</h3>
<p>Med Lodo kan bilaget registreres på følgende måte:</p> 
<h4>Registrering av varekjøp</h4>
<p>Vi registrerer varekjøpet under kontoen "1900-Kontanter". Vi fører inn Bilagsdatoen, perioden og 
kr 400,- i feltet "Ut" (Klikk for større bilde):</p>
<a href="/lodo/registrering2.jpg"><img src="/lodo/th_registrering2.jpg" alt="Bilagsregistrering" /></a>

<h4>Registrering av salg</h4>
<p>Vi registrerer salget under konten "1900-Kontanter". Vi fører inn Bilagsdatoen, perioden og 
kr 600,- i feltet "Inntekt" (Klikk for større bilde):</p>
<a href="/lodo/registrering4.jpg"><img src="/lodo/th_registrering4.jpg" alt="Bilagsregistrering" /></a>
<p>Saldoen blir kr 200,- fordi kr 400 ut og kr 600 inn gir kr 200,- i kassa.</p>
<p style="text-align: right;">Forrige: <a href="<? print $_lib['sess']->dispatch ?>page=balanse">Balanse og resultat</a></p>
