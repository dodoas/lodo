<?php
// Filnavn: schemaxml_74_3488.php
// Skjema: RF-1122    Tilleggsskj for overnatt/serv.sted som serverer øl/vin/brennevin
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-979 gruppeid="979">
	<Avgiver-grp-329 gruppeid="329">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<ServeringsstedAdresse-datadef-23040 orid="23040">' . $data['D23040'] . '</ServeringsstedAdresse-datadef-23040>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-329>
</GenerellInformasjon-grp-979>
';
}
else
{
$xml = '<GenerellInformasjon-grp-979 gruppeid="979">
	<Avgiver-grp-329 gruppeid="329">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<ServeringsstedAdresse-datadef-23040 orid="23040">' . $data['D23040'] . '</ServeringsstedAdresse-datadef-23040>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-329>
</GenerellInformasjon-grp-979>
<Varer-grp-330 gruppeid="330">
	<Vareart-grp-2282 gruppeid="2282">
		<Matvarer-grp-2474 gruppeid="2474">
			<LagerbeholdningMatvarerFjoraret-datadef-8160 orid="8160">' . $data['D8160'] . '</LagerbeholdningMatvarerFjoraret-datadef-8160>
			<VarekjopMatvarer-datadef-8161 orid="8161">' . $data['D8161'] . '</VarekjopMatvarer-datadef-8161>
			<LagerbeholdningMatvarer-datadef-8162 orid="8162">' . $data['D8162'] . '</LagerbeholdningMatvarer-datadef-8162>
			<InntakskostMatvarerSolgte-datadef-8163 orid="8163">' . $data['D8163'] . '</InntakskostMatvarerSolgte-datadef-8163>
			<SalgMatvarer-datadef-8164 orid="8164">' . $data['D8164'] . '</SalgMatvarer-datadef-8164>
			<FortjenesteMatvarerBrutto-datadef-1913 orid="1913">' . $data['D1913'] . '</FortjenesteMatvarerBrutto-datadef-1913>
		</Matvarer-grp-2474>
		<TobakkSigaretterMv-grp-2475 gruppeid="2475">
			<LagerbeholdningTobakkSigaretterMvFjoraret-datadef-8165 orid="8165">' . $data['D8165'] . '</LagerbeholdningTobakkSigaretterMvFjoraret-datadef-8165>
			<VarekjopTobakkSigaretterMv-datadef-8166 orid="8166">' . $data['D8166'] . '</VarekjopTobakkSigaretterMv-datadef-8166>
			<LagerbeholdningTobakkSigaretterMv-datadef-8167 orid="8167">' . $data['D8167'] . '</LagerbeholdningTobakkSigaretterMv-datadef-8167>
			<InntakskostTobakkSigaretterMvSolgte-datadef-8168 orid="8168">' . $data['D8168'] . '</InntakskostTobakkSigaretterMvSolgte-datadef-8168>
			<SalgTobakkSigaretterMv-datadef-8169 orid="8169">' . $data['D8169'] . '</SalgTobakkSigaretterMv-datadef-8169>
			<FortjenesteTobakkSigaretterMvBrutto-datadef-8170 orid="8170">' . $data['D8170'] . '</FortjenesteTobakkSigaretterMvBrutto-datadef-8170>
		</TobakkSigaretterMv-grp-2475>
		<KaffeTe-grp-2476 gruppeid="2476">
			<LagerbeholdningKaffeTeFjoraret-datadef-8171 orid="8171">' . $data['D8171'] . '</LagerbeholdningKaffeTeFjoraret-datadef-8171>
			<VarekjopKaffeTe-datadef-8172 orid="8172">' . $data['D8172'] . '</VarekjopKaffeTe-datadef-8172>
			<LagerbeholdningKaffeTe-datadef-8173 orid="8173">' . $data['D8173'] . '</LagerbeholdningKaffeTe-datadef-8173>
			<InntakskostKaffeTeSolgte-datadef-8174 orid="8174">' . $data['D8174'] . '</InntakskostKaffeTeSolgte-datadef-8174>
			<SalgKaffeTe-datadef-8175 orid="8175">' . $data['D8175'] . '</SalgKaffeTe-datadef-8175>
			<FortjenesteKaffeTeBrutto-datadef-8176 orid="8176">' . $data['D8176'] . '</FortjenesteKaffeTeBrutto-datadef-8176>
		</KaffeTe-grp-2476>
		<Mineralvann-grp-2477 gruppeid="2477">
			<LagerbeholdningMineralvannFjoraret-datadef-8177 orid="8177">' . $data['D8177'] . '</LagerbeholdningMineralvannFjoraret-datadef-8177>
			<VarekjopMineralvann-datadef-8178 orid="8178">' . $data['D8178'] . '</VarekjopMineralvann-datadef-8178>
			<LagerbeholdningMineralvann-datadef-8179 orid="8179">' . $data['D8179'] . '</LagerbeholdningMineralvann-datadef-8179>
			<InntakskostMineralvannSolgte-datadef-8180 orid="8180">' . $data['D8180'] . '</InntakskostMineralvannSolgte-datadef-8180>
			<SalgMineralvann-datadef-8181 orid="8181">' . $data['D8181'] . '</SalgMineralvann-datadef-8181>
			<FortjenesteMineralvannBrutto-datadef-8182 orid="8182">' . $data['D8182'] . '</FortjenesteMineralvannBrutto-datadef-8182>
		</Mineralvann-grp-2477>
		<RusbrusCider-grp-4171 gruppeid="4171">
			<LagerbeholdningRusbrusCiderFjoraret-datadef-19672 orid="19672">' . $data['D19672'] . '</LagerbeholdningRusbrusCiderFjoraret-datadef-19672>
			<VarekjopRusbrusCider-datadef-19673 orid="19673">' . $data['D19673'] . '</VarekjopRusbrusCider-datadef-19673>
			<LagerbeholdningRusbrusCider-datadef-19674 orid="19674">' . $data['D19674'] . '</LagerbeholdningRusbrusCider-datadef-19674>
			<InntakskostRusbrusCiderSolgte-datadef-19675 orid="19675">' . $data['D19675'] . '</InntakskostRusbrusCiderSolgte-datadef-19675>
			<SalgRusbrusCider-datadef-19676 orid="19676">' . $data['D19676'] . '</SalgRusbrusCider-datadef-19676>
			<FortjenesteRusbrusCiderBrutto-datadef-19677 orid="19677">' . $data['D19677'] . '</FortjenesteRusbrusCiderBrutto-datadef-19677>
		</RusbrusCider-grp-4171>
		<Ol-grp-2478 gruppeid="2478">
			<LagerbeholdningOlFjoraret-datadef-8183 orid="8183">' . $data['D8183'] . '</LagerbeholdningOlFjoraret-datadef-8183>
			<VarekjopOl-datadef-8184 orid="8184">' . $data['D8184'] . '</VarekjopOl-datadef-8184>
			<LagerbeholdningOl-datadef-8185 orid="8185">' . $data['D8185'] . '</LagerbeholdningOl-datadef-8185>
			<InntakskostOlSolgte-datadef-8186 orid="8186">' . $data['D8186'] . '</InntakskostOlSolgte-datadef-8186>
			<SalgOl-datadef-8187 orid="8187">' . $data['D8187'] . '</SalgOl-datadef-8187>
			<FortjenesteOlBrutto-datadef-8188 orid="8188">' . $data['D8188'] . '</FortjenesteOlBrutto-datadef-8188>
		</Ol-grp-2478>
		<Vin-grp-2479 gruppeid="2479">
			<LagerbeholdningVinFjoraret-datadef-8189 orid="8189">' . $data['D8189'] . '</LagerbeholdningVinFjoraret-datadef-8189>
			<VarekjopVin-datadef-8190 orid="8190">' . $data['D8190'] . '</VarekjopVin-datadef-8190>
			<LagerbeholdningVin-datadef-8191 orid="8191">' . $data['D8191'] . '</LagerbeholdningVin-datadef-8191>
			<InntakskostVinSolgte-datadef-8192 orid="8192">' . $data['D8192'] . '</InntakskostVinSolgte-datadef-8192>
			<SalgVin-datadef-8193 orid="8193">' . $data['D8193'] . '</SalgVin-datadef-8193>
			<FortjenesteVinBrutto-datadef-8194 orid="8194">' . $data['D8194'] . '</FortjenesteVinBrutto-datadef-8194>
		</Vin-grp-2479>
		<Brennevin-grp-2480 gruppeid="2480">
			<LagerbeholdningBrennevinFjoraret-datadef-8195 orid="8195">' . $data['D8195'] . '</LagerbeholdningBrennevinFjoraret-datadef-8195>
			<VarekjopBrennevin-datadef-8196 orid="8196">' . $data['D8196'] . '</VarekjopBrennevin-datadef-8196>
			<LagerbeholdningBrennevin-datadef-8197 orid="8197">' . $data['D8197'] . '</LagerbeholdningBrennevin-datadef-8197>
			<InntakskostBrennevinSolgte-datadef-8198 orid="8198">' . $data['D8198'] . '</InntakskostBrennevinSolgte-datadef-8198>
			<SalgBrennevin-datadef-8199 orid="8199">' . $data['D8199'] . '</SalgBrennevin-datadef-8199>
			<FortjenesteBrennevinBrutto-datadef-8200 orid="8200">' . $data['D8200'] . '</FortjenesteBrennevinBrutto-datadef-8200>
		</Brennevin-grp-2480>
		<AndreVarer-grp-2481 gruppeid="2481">
			<LagerbeholdningVarerAndreFjoraret-datadef-8201 orid="8201">' . $data['D8201'] . '</LagerbeholdningVarerAndreFjoraret-datadef-8201>
			<VarekjopVarerAndre-datadef-8202 orid="8202">' . $data['D8202'] . '</VarekjopVarerAndre-datadef-8202>
			<LagerbeholdningVarerAndre-datadef-8203 orid="8203">' . $data['D8203'] . '</LagerbeholdningVarerAndre-datadef-8203>
			<InntakskostVarerAndreSolgte-datadef-8204 orid="8204">' . $data['D8204'] . '</InntakskostVarerAndreSolgte-datadef-8204>
			<SalgVarerAndre-datadef-8205 orid="8205">' . $data['D8205'] . '</SalgVarerAndre-datadef-8205>
			<FortjenesteVarerAndreBrutto-datadef-8206 orid="8206">' . $data['D8206'] . '</FortjenesteVarerAndreBrutto-datadef-8206>
		</AndreVarer-grp-2481>
		<Sum-grp-2482 gruppeid="2482">
			<LagerbeholdningFjoraretSkattemessig-datadef-8207 orid="8207">' . $data['D8207'] . '</LagerbeholdningFjoraretSkattemessig-datadef-8207>
			<Varekjop-datadef-8208 orid="8208">' . $data['D8208'] . '</Varekjop-datadef-8208>
			<LagerbeholdningSkattemessig-datadef-1909 orid="1909">' . $data['D1909'] . '</LagerbeholdningSkattemessig-datadef-1909>
			<InntakskostVarerSolgte-datadef-1910 orid="1910">' . $data['D1910'] . '</InntakskostVarerSolgte-datadef-1910>
			<SalgVarer-datadef-1911 orid="1911">' . $data['D1911'] . '</SalgVarer-datadef-1911>
			<FortjenesteBrutto-datadef-1912 orid="1912">' . $data['D1912'] . '</FortjenesteBrutto-datadef-1912>
		</Sum-grp-2482>
	</Vareart-grp-2282>
</Varer-grp-330>
<AndreInntekter-grp-2324 gruppeid="2324">
	<Inntektstype-grp-2484 gruppeid="2484">
		<OmsetningLosji-datadef-764 orid="764">' . $data['D764'] . '</OmsetningLosji-datadef-764>
		<Garderobeinntekter-datadef-1933 orid="1933">' . $data['D1933'] . '</Garderobeinntekter-datadef-1933>
		<CoverCharge-grp-2485 gruppeid="2485">
			<CoverChargeAvgiftspliktig-datadef-1935 orid="1935">' . $data['D1935'] . '</CoverChargeAvgiftspliktig-datadef-1935>
			<CoverChargeAvgiftsfri-datadef-8221 orid="8221">' . $data['D8221'] . '</CoverChargeAvgiftsfri-datadef-8221>
		</CoverCharge-grp-2485>
	</Inntektstype-grp-2484>
</AndreInntekter-grp-2324>
<SpilleinntekterFraAutomaterMv-grp-340 gruppeid="340">
	<Spilleautomater-datadef-1928 orid="1928">' . $data['D1928'] . '</Spilleautomater-datadef-1928>
	<Eier-grp-4172 gruppeid="4172">
		<SpilleautomaterEierNavn-datadef-1929 orid="1929">' . $data['D1929'] . '</SpilleautomaterEierNavn-datadef-1929>
		<SpilleautomaterEierAdresse-datadef-1930 orid="1930">' . $data['D1930'] . '</SpilleautomaterEierAdresse-datadef-1930>
		<SpilleautomaterEierPostnummer-datadef-8209 orid="8209">' . $data['D8209'] . '</SpilleautomaterEierPostnummer-datadef-8209>
		<SpilleautomaterEierPoststed-datadef-8210 orid="8210">' . $data['D8210'] . '</SpilleautomaterEierPoststed-datadef-8210>
	</Eier-grp-4172>
	<AndelAvSpilleinntekterVirksomhetens-grp-4173 gruppeid="4173">
		<SpilleautomaterInntektProsent-datadef-1931 orid="1931">' . $data['D1931'] . '</SpilleautomaterInntektProsent-datadef-1931>
		<OmsetningSpilleautomater-datadef-1932 orid="1932">' . $data['D1932'] . '</OmsetningSpilleautomater-datadef-1932>
	</AndelAvSpilleinntekterVirksomhetens-grp-4173>
</SpilleinntekterFraAutomaterMv-grp-340>
<KostTilPersonalet-grp-336 gruppeid="336">
	<FriKostAnsatte-datadef-1914 orid="1914">' . $data['D1914'] . '</FriKostAnsatte-datadef-1914>
	<FriKostAntall-datadef-1915 orid="1915">' . $data['D1915'] . '</FriKostAntall-datadef-1915>
	<FriKostPersonalkontoUttaksverdi-datadef-1916 orid="1916">' . $data['D1916'] . '</FriKostPersonalkontoUttaksverdi-datadef-1916>
	<FriKostSkattedirektoratetsTakseringsregler-datadef-1917 orid="1917">' . $data['D1917'] . '</FriKostSkattedirektoratetsTakseringsregler-datadef-1917>
	<FriKostPaslag-datadef-1918 orid="1918">' . $data['D1918'] . '</FriKostPaslag-datadef-1918>
</KostTilPersonalet-grp-336>
<PrivatUttakAvVarer-grp-337 gruppeid="337">
	<UttakVarerOmsetningsverdi-datadef-1919 orid="1919">' . $data['D1919'] . '</UttakVarerOmsetningsverdi-datadef-1919>
	<UttakVarerPaslag-datadef-1920 orid="1920">' . $data['D1920'] . '</UttakVarerPaslag-datadef-1920>
	<UttakVarerInntektsfort-datadef-1921 orid="1921">' . $data['D1921'] . '</UttakVarerInntektsfort-datadef-1921>
	<UttakVarerRegistrert-datadef-1922 orid="1922">' . $data['D1922'] . '</UttakVarerRegistrert-datadef-1922>
	<UttakVarer-datadef-1923 orid="1923">' . $data['D1923'] . '</UttakVarer-datadef-1923>
</PrivatUttakAvVarer-grp-337>
<Representasjon-grp-339 gruppeid="339">
	<UttakRepresentasjonOmsetningsverdi-datadef-1924 orid="1924">' . $data['D1924'] . '</UttakRepresentasjonOmsetningsverdi-datadef-1924>
	<UttakRepresentasjonPaslag-datadef-1925 orid="1925">' . $data['D1925'] . '</UttakRepresentasjonPaslag-datadef-1925>
	<UttakRepresentasjon-datadef-1926 orid="1926">' . $data['D1926'] . '</UttakRepresentasjon-datadef-1926>
</Representasjon-grp-339>
';
}
?>