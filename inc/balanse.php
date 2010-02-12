
<div>
<p style="text-align: center;"><a href="<? print $_lib['sess']->dispatch ?>page=regnskap">Hva er et regnskap?</a> | <a href="<? print $_lib['sess']->dispatch ?>page=bokholderi">Dobbelt bokholderi</a> | Balanse og resultat | <a href="<? print $_lib['sess']->dispatch ?>page=eksempel">Eksempel</a></p>
</div>
<h2>Balanse og resultat</h2>

<p>Formålet med regnskapsføring er å skape oversikt over den økonomiske stillingen på et
gitt tidspunkt og det økonomiske resultatet av virksomheten over en bestemt periode. Den 
økonomiske stillingen fremgår av <strong>Balansen</strong> og det økonomiske resultatet finner man i
resultatoppstillingen, <strong>Resultatregnskapet</strong>.</p>

<p>I balansen forteller debetsiden hvilke eiendeler virksomheten har brukt kapitalen på (eiendeler) og
kreditsiden forteller hvordan virksomheten har anskaffet seg kapitalen på (gjeld).</p>

<p>I resultatet forteller debetsiden hvilke kostnader virksomheten har hatt (utgifter) og
kreditsiden forteller hvilke inntekter virksomheten har hatt (inntekter).</p>


<p>Til venstre er balanse og til høyre resultatkonti</p>

<table width="80%" border="0" cellpadding="9" cellspacing="1">
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="borderleft">&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <th>Balanse<br />Konti fra 1000 til 2999</th>
        <td>&nbsp;</td>
        <td class="borderleft">&nbsp;</td>
        <th>Resultat<br />Konti fra 3000 til 9999</th>
    </tr>
    <tr>
<!-- Venstre celle -->
<td>
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
        <td>
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 1900<br />Kassa<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Debet</td>
        			<td class="head">Kredit</td>
        			</tr>
        			<tr>
        			<td class="baldebet"><br /><br /><br /><br /></td>
        			<td class="balkredit">&nbsp;</td>
        			</tr>
        		</table>
        </td>
        <td>
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 1920<br />Bank<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Debet</td>
        			<td class="head">Kredit</td>
        			</tr>
        			<tr>
        			<td class="baldebet"><br /><br /><br /><br /></td>
        			<td class="balkredit">&nbsp;</td>
        			</tr>        		
        		</table>
        </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Årsresultat<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Underskudd</td>
        			<td class="head">Overskudd</td>
        			</tr>
        			<tr>
        			<td><br /><br /><br /><br /></td>
        			<td>&nbsp;</td>
        			</tr>
        		</table>
        </td>
        </tr>
        </table>
        </td>
        <td>&nbsp;</td>
        <td class="borderleft">&nbsp;</td>
<!-- Høyre celle -->
        <td>
        <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
        <td>
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 3000<br />Salg<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Debet</td>
        			<td class="head">Kredit</td>
        			</tr>
        			<tr>
        			<td class="resdebet"><br /><br /><br /><br /></td>
        			<td class="reskredit">&nbsp;</td>
        			</tr>
        		</table>
        </td>
        <td>
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Konto 4000<br />Varekjøp<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Debet</td>
        			<td class="head">Kredit</td>
        			</tr>
        			<tr>
        			<td class="resdebet"><br /><br /><br /><br /></td>
        			<td class="reskredit">&nbsp;</td>
        			</tr>        		
        		</table>
        </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
        		<table border="1" cellpadding="5" cellspacing="0">
        			<tr>
        			<th colspan="2">Årsresultat<br /><br /></th>        		
        			</tr>
        			<tr>
        			<td class="head">Overskudd</td>
        			<td class="head">Underskudd</td>
        			</tr>
        			<tr>
        			<td><br /><br /><br /><br /></td>
        			<td>&nbsp;</td>
        			</tr>
        		</table>
        </td>
        </tr>
        </table>
        </td>
    </tr>
</table>

<p>Postering mellom balansekonti eller resultatkonti gir ingen resultatmessig virkning.</p>
<p>Postering mellom balansekonti og resultatkonti gir en resultatmessig virkning.</p>
<p style="text-align: right;">Forrige: <a href="<? print $_lib['sess']->dispatch ?>page=bokholderi">Dobbelt bokholderi</a> | Neste: <a href="<? print $_lib['sess']->dispatch ?>page=eksempel">Eksempel</a></p>