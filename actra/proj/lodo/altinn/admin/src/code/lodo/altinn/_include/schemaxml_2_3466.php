<?php
// RF-1175  Næringsoppgave 1 (for næringsdrivende med begrenset regnskapsplikt)
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-861 gruppeid="861">
	<Avgiver-grp-269 gruppeid="269">
		<VirksomhetNavn-datadef-15756 orid="15756">' . $data['D15756'] . '</VirksomhetNavn-datadef-15756>
		<VirksomhetAdresse-datadef-15759 orid="15759">' . $data['D15759'] . '</VirksomhetAdresse-datadef-15759>
		<VirksomhetPostnummer-datadef-15808 orid="15808">' . $data['D15808'] . '</VirksomhetPostnummer-datadef-15808>
		<VirksomhetPoststed-datadef-15809 orid="15809">' . $data['D15809'] . '</VirksomhetPoststed-datadef-15809>
		<VirksomhetOrganisasjonsnummer-datadef-15761 orid="15761">' . $data['D15761'] . '</VirksomhetOrganisasjonsnummer-datadef-15761>
		<OppgavegiverVirksomhetFodselsnummer-datadef-15762 orid="15762">' . $data['D15762'] . '</OppgavegiverVirksomhetFodselsnummer-datadef-15762>
		<VirksomhetSysselsatte-datadef-15763 orid="15763">' . $data['D15763'] . '</VirksomhetSysselsatte-datadef-15763>
		<KontaktpersonVirksomhetNavn-datadef-15757 orid="15757">' . $data['D15757'] . '</KontaktpersonVirksomhetNavn-datadef-15757>
		<KontaktpersonVirksomhetTelefonnummer-datadef-15758 orid="15758">' . $data['D15758'] . '</KontaktpersonVirksomhetTelefonnummer-datadef-15758>
		<VirksomhetAdresseEndret-datadef-22057 orid="22057">' . $data['D22057'] . '</VirksomhetAdresseEndret-datadef-22057>
	</Avgiver-grp-269>
	<Regnskapsperiode-grp-2118 gruppeid="2118">
		<RegnskapPeriodeStart-datadef-15805 orid="15805">' . $data['D15805'] . '</RegnskapPeriodeStart-datadef-15805>
		<RegnskapPeriodeSlutt-datadef-15806 orid="15806">' . $data['D15806'] . '</RegnskapPeriodeSlutt-datadef-15806>
	</Regnskapsperiode-grp-2118>
	<EksternRegnskapsforer-grp-270 gruppeid="270">
		<RegnskapsforerEksternNavn-datadef-15779 orid="15779">' . $data['D15779'] . '</RegnskapsforerEksternNavn-datadef-15779>
		<RegnskapsforerEksternLopenummer-datadef-15867 orid="15867">' . $data['D15867'] . '</RegnskapsforerEksternLopenummer-datadef-15867>
		<RegnskapsforerEksternAdresse-datadef-15780 orid="15780">' . $data['D15780'] . '</RegnskapsforerEksternAdresse-datadef-15780>
		<RegnskapsforerEksternPostnummer-datadef-15810 orid="15810">' . $data['D15810'] . '</RegnskapsforerEksternPostnummer-datadef-15810>
		<RegnskapsforerEksternPoststed-datadef-15811 orid="15811">' . $data['D15811'] . '</RegnskapsforerEksternPoststed-datadef-15811>
		<OppgaveRegnskapsforerEkstern-datadef-15814 orid="15814">' . $data['D15814'] . '</OppgaveRegnskapsforerEkstern-datadef-15814>
		<RegnskapRegnskapsforerEkstern-datadef-15856 orid="15856">' . $data['D15856'] . '</RegnskapRegnskapsforerEkstern-datadef-15856>
	</EksternRegnskapsforer-grp-270>
	<TilleggsopplysningerOgSpesifikasjoner-grp-4182 gruppeid="4182">
	</TilleggsopplysningerOgSpesifikasjoner-grp-4182>
</GenerellInformasjon-grp-861>';
}
else
{
$xml = '<GenerellInformasjon-grp-861 gruppeid="861">
	<Avgiver-grp-269 gruppeid="269">
		<VirksomhetNavn-datadef-15756 orid="15756">' . $data['D15756'] . '</VirksomhetNavn-datadef-15756>
		<VirksomhetAdresse-datadef-15759 orid="15759">' . $data['D15759'] . '</VirksomhetAdresse-datadef-15759>
		<VirksomhetPostnummer-datadef-15808 orid="15808">' . $data['D15808'] . '</VirksomhetPostnummer-datadef-15808>
		<VirksomhetPoststed-datadef-15809 orid="15809">' . $data['D15809'] . '</VirksomhetPoststed-datadef-15809>
		<VirksomhetOrganisasjonsnummer-datadef-15761 orid="15761">' . $data['D15761'] . '</VirksomhetOrganisasjonsnummer-datadef-15761>
		<OppgavegiverVirksomhetFodselsnummer-datadef-15762 orid="15762">' . $data['D15762'] . '</OppgavegiverVirksomhetFodselsnummer-datadef-15762>
		<VirksomhetSysselsatte-datadef-15763 orid="15763">' . $data['D15763'] . '</VirksomhetSysselsatte-datadef-15763>
		<KontaktpersonVirksomhetNavn-datadef-15757 orid="15757">' . $data['D15757'] . '</KontaktpersonVirksomhetNavn-datadef-15757>
		<KontaktpersonVirksomhetTelefonnummer-datadef-15758 orid="15758">' . $data['D15758'] . '</KontaktpersonVirksomhetTelefonnummer-datadef-15758>
		<VirksomhetAdresseEndret-datadef-22057 orid="22057">' . $data['D22057'] . '</VirksomhetAdresseEndret-datadef-22057>
	</Avgiver-grp-269>
	<Regnskapsperiode-grp-2118 gruppeid="2118">
		<RegnskapPeriodeStart-datadef-15805 orid="15805">' . $data['D15805'] . '</RegnskapPeriodeStart-datadef-15805>
		<RegnskapPeriodeSlutt-datadef-15806 orid="15806">' . $data['D15806'] . '</RegnskapPeriodeSlutt-datadef-15806>
	</Regnskapsperiode-grp-2118>
	<EksternRegnskapsforer-grp-270 gruppeid="270">
		<RegnskapsforerEksternNavn-datadef-15779 orid="15779">' . $data['D15779'] . '</RegnskapsforerEksternNavn-datadef-15779>
		<RegnskapsforerEksternLopenummer-datadef-15867 orid="15867">' . $data['D15867'] . '</RegnskapsforerEksternLopenummer-datadef-15867>
		<RegnskapsforerEksternAdresse-datadef-15780 orid="15780">' . $data['D15780'] . '</RegnskapsforerEksternAdresse-datadef-15780>
		<RegnskapsforerEksternPostnummer-datadef-15810 orid="15810">' . $data['D15810'] . '</RegnskapsforerEksternPostnummer-datadef-15810>
		<RegnskapsforerEksternPoststed-datadef-15811 orid="15811">' . $data['D15811'] . '</RegnskapsforerEksternPoststed-datadef-15811>
		<OppgaveRegnskapsforerEkstern-datadef-15814 orid="15814">' . $data['D15814'] . '</OppgaveRegnskapsforerEkstern-datadef-15814>
		<RegnskapRegnskapsforerEkstern-datadef-15856 orid="15856">' . $data['D15856'] . '</RegnskapRegnskapsforerEkstern-datadef-15856>
	</EksternRegnskapsforer-grp-270>
	<TilleggsopplysningerOgSpesifikasjoner-grp-4182 gruppeid="4182">
	</TilleggsopplysningerOgSpesifikasjoner-grp-4182>
</GenerellInformasjon-grp-861>
<Varelager-grp-862 gruppeid="862">
	<Varelager-grp-2185 gruppeid="2185">
		<DetteAr-grp-2179 gruppeid="2179">
			<VarelagerRavarerHalvfabrikata-datadef-15764 orid="15764">' . $data['D15764'] . '</VarelagerRavarerHalvfabrikata-datadef-15764>
			<VarelagerUnderTilvirkning-datadef-15765 orid="15765">' . $data['D15765'] . '</VarelagerUnderTilvirkning-datadef-15765>
			<VarelagerEgentilvirkede-datadef-15766 orid="15766">' . $data['D15766'] . '</VarelagerEgentilvirkede-datadef-15766>
			<VarelagerInnkjopteVideresalg-datadef-15767 orid="15767">' . $data['D15767'] . '</VarelagerInnkjopteVideresalg-datadef-15767>
			<BuskapVerdiSluttstatus-datadef-9669 orid="9669">' . $data['D9669'] . '</BuskapVerdiSluttstatus-datadef-9669>
			<LagerbeholdningJordbrukEgetBruk-datadef-17165 orid="17165">' . $data['D17165'] . '</LagerbeholdningJordbrukEgetBruk-datadef-17165>
			<VarelagerNaring-datadef-19786 orid="19786">' . $data['D19786'] . '</VarelagerNaring-datadef-19786>
		</DetteAr-grp-2179>
		<Fjoraret-grp-2180 gruppeid="2180">
			<VarelagerRavarerHalvfabrikataFjoraret-datadef-15815 orid="15815">' . $data['D15815'] . '</VarelagerRavarerHalvfabrikataFjoraret-datadef-15815>
			<VarelagerJordbrukUnderTilvirkningFjoraret-datadef-15816 orid="15816">' . $data['D15816'] . '</VarelagerJordbrukUnderTilvirkningFjoraret-datadef-15816>
			<VarelagerEgentilvirkedeFjoraret-datadef-15817 orid="15817">' . $data['D15817'] . '</VarelagerEgentilvirkedeFjoraret-datadef-15817>
			<VarelagerInnkjopteVideresalgFjoraret-datadef-15818 orid="15818">' . $data['D15818'] . '</VarelagerInnkjopteVideresalgFjoraret-datadef-15818>
			<BuskapVerdiApningsstatus-datadef-9670 orid="9670">' . $data['D9670'] . '</BuskapVerdiApningsstatus-datadef-9670>
			<LagerbeholdningJordbrukEgetBrukFjoraret-datadef-17166 orid="17166">' . $data['D17166'] . '</LagerbeholdningJordbrukEgetBrukFjoraret-datadef-17166>
			<VarelagerNaringFjoraret-datadef-19787 orid="19787">' . $data['D19787'] . '</VarelagerNaringFjoraret-datadef-19787>
		</Fjoraret-grp-2180>
	</Varelager-grp-2185>
</Varelager-grp-862>
<BruttofortjenestePaInnkjopteVarerForVideresalg-grp-273 gruppeid="273">
	<SalgsinntekterHandelsvarerAvgiftspliktigSkattemessig-datadef-7355 orid="7355">' . $data['D7355'] . '</SalgsinntekterHandelsvarerAvgiftspliktigSkattemessig-datadef-7355>
	<SalgsinntekterHandelsvarerAvgiftsfriSkattemessig-datadef-7356 orid="7356">' . $data['D7356'] . '</SalgsinntekterHandelsvarerAvgiftsfriSkattemessig-datadef-7356>
	<SalgsinntekterHandelsvarerUtenforAvgiftsomradeSkattemessig-datadef-11333 orid="11333">' . $data['D11333'] . '</SalgsinntekterHandelsvarerUtenforAvgiftsomradeSkattemessig-datadef-11333>
	<SalgsinntekterHandelsvarerSkattemessig-datadef-7357 orid="7357">' . $data['D7357'] . '</SalgsinntekterHandelsvarerSkattemessig-datadef-7357>
	<VarekostnadHandelsvarerSkattemessig-datadef-7358 orid="7358">' . $data['D7358'] . '</VarekostnadHandelsvarerSkattemessig-datadef-7358>
	<FortjenesteHandelsvarerBruttoSkattemessig-datadef-7359 orid="7359">' . $data['D7359'] . '</FortjenesteHandelsvarerBruttoSkattemessig-datadef-7359>
</BruttofortjenestePaInnkjopteVarerForVideresalg-grp-273>
<SkattemessigVerdiPaKundefordringer-grp-274 gruppeid="274">
	<Kundefordringer-grp-2182 gruppeid="2182">
		<DetteAr-grp-2183 gruppeid="2183">
			<FordringerKunderPalydende-datadef-6941 orid="6941">' . $data['D6941'] . '</FordringerKunderPalydende-datadef-6941>
			<FordringerKunderTap-datadef-6940 orid="6940">' . $data['D6940'] . '</FordringerKunderTap-datadef-6940>
			<Kundefordringer-datadef-15769 orid="15769">' . $data['D15769'] . '</Kundefordringer-datadef-15769>
			<FordringerKunderNedskrivningSkattemessig-datadef-117 orid="117">' . $data['D117'] . '</FordringerKunderNedskrivningSkattemessig-datadef-117>
			<SalgKreditt-datadef-6944 orid="6944">' . $data['D6944'] . '</SalgKreditt-datadef-6944>
			<FordringerKunderSkattemessig-datadef-18116 orid="18116">' . $data['D18116'] . '</FordringerKunderSkattemessig-datadef-18116>
		</DetteAr-grp-2183>
		<Fjoraret-grp-2184 gruppeid="2184">
			<FordringerKunderPalydendeFjoraret-datadef-6938 orid="6938">' . $data['D6938'] . '</FordringerKunderPalydendeFjoraret-datadef-6938>
			<FordringerKunderTapFjoraret-datadef-6939 orid="6939">' . $data['D6939'] . '</FordringerKunderTapFjoraret-datadef-6939>
			<FordringerKunderNedskrivningSkattemessigFjoraret-datadef-6942 orid="6942">' . $data['D6942'] . '</FordringerKunderNedskrivningSkattemessigFjoraret-datadef-6942>
			<SalgKredittFjoraret-datadef-6943 orid="6943">' . $data['D6943'] . '</SalgKredittFjoraret-datadef-6943>
			<FordringerSkattemessigFjoraret-datadef-6922 orid="6922">' . $data['D6922'] . '</FordringerSkattemessigFjoraret-datadef-6922>
		</Fjoraret-grp-2184>
		<VirksomhetNyetablering-datadef-15831 orid="15831">' . $data['D15831'] . '</VirksomhetNyetablering-datadef-15831>
	</Kundefordringer-grp-2182>
</SkattemessigVerdiPaKundefordringer-grp-274>
<SkattemessigResultatregnskapDriftsinntekt-grp-277 gruppeid="277">
	<Naringsinntekt-grp-2186 gruppeid="2186">
		<DetteAr-grp-882 gruppeid="882">
			<SalgsinntekterUttakAvgiftspliktigSkattemessig-datadef-7360 orid="7360">' . $data['D7360'] . '</SalgsinntekterUttakAvgiftspliktigSkattemessig-datadef-7360>
			<SalgsinntekterUttakAvgiftsfriSkattemessig-datadef-7362 orid="7362">' . $data['D7362'] . '</SalgsinntekterUttakAvgiftsfriSkattemessig-datadef-7362>
			<SalgsinntekterUttakUtenforAvgiftsomradeSkattemessig-datadef-7364 orid="7364">' . $data['D7364'] . '</SalgsinntekterUttakUtenforAvgiftsomradeSkattemessig-datadef-7364>
			<NaringsinntektAvgifterOffentlige-datadef-15843 orid="15843">' . $data['D15843'] . '</NaringsinntektAvgifterOffentlige-datadef-15843>
			<TilskuddOffentligeSkattemessig-datadef-7368 orid="7368">' . $data['D7368'] . '</TilskuddOffentligeSkattemessig-datadef-7368>
			<LeieinntekterFastEiendomSkattemessig-datadef-7370 orid="7370">' . $data['D7370'] . '</LeieinntekterFastEiendomSkattemessig-datadef-7370>
			<LeieinntekterRettigheterSkattemessig-datadef-17163 orid="17163">' . $data['D17163'] . '</LeieinntekterRettigheterSkattemessig-datadef-17163>
			<LeieinntekterAndreSkattemessig-datadef-7372 orid="7372">' . $data['D7372'] . '</LeieinntekterAndreSkattemessig-datadef-7372>
			<ProvisjonsinntekterSkattemessig-datadef-7374 orid="7374">' . $data['D7374'] . '</ProvisjonsinntekterSkattemessig-datadef-7374>
			<InntektsforingNegativSaldo-datadef-7266 orid="7266">' . $data['D7266'] . '</InntektsforingNegativSaldo-datadef-7266>
			<DriftsinntekterAndreSkattemessig-datadef-7376 orid="7376">' . $data['D7376'] . '</DriftsinntekterAndreSkattemessig-datadef-7376>
			<NaringsinntektBrutto-datadef-15799 orid="15799">' . $data['D15799'] . '</NaringsinntektBrutto-datadef-15799>
		</DetteAr-grp-882>
		<Fjoraret-grp-883 gruppeid="883">
			<SalgsinntekterUttakAvgiftspliktigSkattemessigFjoraret-datadef-7361 orid="7361">' . $data['D7361'] . '</SalgsinntekterUttakAvgiftspliktigSkattemessigFjoraret-datadef-7361>
			<SalgsinntekterUttakAvgiftsfriSkattemessigFjoraret-datadef-7363 orid="7363">' . $data['D7363'] . '</SalgsinntekterUttakAvgiftsfriSkattemessigFjoraret-datadef-7363>
			<SalgsinntekterUttakUtenforAvgiftsomradeSkattemessigFjoraret-datadef-7365 orid="7365">' . $data['D7365'] . '</SalgsinntekterUttakUtenforAvgiftsomradeSkattemessigFjoraret-datadef-7365>
			<AvgifterOffentligeSolgteVarerSkattemessigFjoraret-datadef-7367 orid="7367">' . $data['D7367'] . '</AvgifterOffentligeSolgteVarerSkattemessigFjoraret-datadef-7367>
			<TilskuddOffentligeSkattemessigFjoraret-datadef-7369 orid="7369">' . $data['D7369'] . '</TilskuddOffentligeSkattemessigFjoraret-datadef-7369>
			<LeieinntekterFastEiendomSkattemessigFjoraret-datadef-7371 orid="7371">' . $data['D7371'] . '</LeieinntekterFastEiendomSkattemessigFjoraret-datadef-7371>
			<LeieinntekterRettigheterSkattemessigFjoraret-datadef-17164 orid="17164">' . $data['D17164'] . '</LeieinntekterRettigheterSkattemessigFjoraret-datadef-17164>
			<LeieinntekterAndreSkattemessigFjoraret-datadef-7373 orid="7373">' . $data['D7373'] . '</LeieinntekterAndreSkattemessigFjoraret-datadef-7373>
			<ProvisjonsinntekterSkattemessigFjoraret-datadef-7375 orid="7375">' . $data['D7375'] . '</ProvisjonsinntekterSkattemessigFjoraret-datadef-7375>
			<InntektsforingNegativSaldoFjoraret-datadef-7267 orid="7267">' . $data['D7267'] . '</InntektsforingNegativSaldoFjoraret-datadef-7267>
			<DriftsinntekterAndreSkattemessigFjoraret-datadef-7377 orid="7377">' . $data['D7377'] . '</DriftsinntekterAndreSkattemessigFjoraret-datadef-7377>
			<NaringsinntektFjoraret-datadef-7268 orid="7268">' . $data['D7268'] . '</NaringsinntektFjoraret-datadef-7268>
		</Fjoraret-grp-883>
	</Naringsinntekt-grp-2186>
</SkattemessigResultatregnskapDriftsinntekt-grp-277>
<SkattemessigResultatregnskapDriftskostnad-grp-2187 gruppeid="2187">
	<Driftskostnad-grp-279 gruppeid="279">
		<DetteAr-grp-885 gruppeid="885">
			<VarekostnadSkattemessig-datadef-7378 orid="7378">' . $data['D7378'] . '</VarekostnadSkattemessig-datadef-7378>
			<BeholdningsendringerVarerEgentilvirkedeSkattemessig-datadef-7380 orid="7380">' . $data['D7380'] . '</BeholdningsendringerVarerEgentilvirkedeSkattemessig-datadef-7380>
			<FremmedytelserSkattemessig-datadef-7382 orid="7382">' . $data['D7382'] . '</FremmedytelserSkattemessig-datadef-7382>
			<BeholdningsendringerAnleggsmidlerEgentilvirkedeSkattemessig-datadef-7384 orid="7384">' . $data['D7384'] . '</BeholdningsendringerAnleggsmidlerEgentilvirkedeSkattemessig-datadef-7384>
			<NaringskostnadLonnMv-datadef-15844 orid="15844">' . $data['D15844'] . '</NaringskostnadLonnMv-datadef-15844>
			<NaringskostnadAnnen-datadef-15845 orid="15845">' . $data['D15845'] . '</NaringskostnadAnnen-datadef-15845>
			<NaringskostnadArbeidsgiveravgift-datadef-15846 orid="15846">' . $data['D15846'] . '</NaringskostnadArbeidsgiveravgift-datadef-15846>
			<PensjonskostnaderInnberetningspliktigeSkattemessig-datadef-7392 orid="7392">' . $data['D7392'] . '</PensjonskostnaderInnberetningspliktigeSkattemessig-datadef-7392>
			<GodtgjorelserDeltakerlignedeSelskaperSkattemessig-datadef-7394 orid="7394">' . $data['D7394'] . '</GodtgjorelserDeltakerlignedeSelskaperSkattemessig-datadef-7394>
			<PersonalkostnaderAndreSkattemessig-datadef-7396 orid="7396">' . $data['D7396'] . '</PersonalkostnaderAndreSkattemessig-datadef-7396>
			<NaringskostnadAvskrivning-datadef-15847 orid="15847">' . $data['D15847'] . '</NaringskostnadAvskrivning-datadef-15847>
			<FraktTransportkostnaderSalgSkattemessig-datadef-7400 orid="7400">' . $data['D7400'] . '</FraktTransportkostnaderSalgSkattemessig-datadef-7400>
			<NaringskostnadEnergiBrenselMv-datadef-15848 orid="15848">' . $data['D15848'] . '</NaringskostnadEnergiBrenselMv-datadef-15848>
			<LeiekostnaderFastEiendomSkattemessig-datadef-7404 orid="7404">' . $data['D7404'] . '</LeiekostnaderFastEiendomSkattemessig-datadef-7404>
			<LysVarmeSkattemessig-datadef-7408 orid="7408">' . $data['D7408'] . '</LysVarmeSkattemessig-datadef-7408>
			<RenovasjonRenholdMvSkattemessig-datadef-7406 orid="7406">' . $data['D7406'] . '</RenovasjonRenholdMvSkattemessig-datadef-7406>
			<NaringskostnadMaskinerMvLeie-datadef-15849 orid="15849">' . $data['D15849'] . '</NaringskostnadMaskinerMvLeie-datadef-15849>
			<NaringskostnadVerktoyMvIkkeAktivert-datadef-15850 orid="15850">' . $data['D15850'] . '</NaringskostnadVerktoyMvIkkeAktivert-datadef-15850>
			<VedlikeholdReparasjonBygningerSkattemessig-datadef-7269 orid="7269">' . $data['D7269'] . '</VedlikeholdReparasjonBygningerSkattemessig-datadef-7269>
			<ReparasjonVedlikeholdAnnet-datadef-15836 orid="15836">' . $data['D15836'] . '</ReparasjonVedlikeholdAnnet-datadef-15836>
			<FremmedeTjenesterSkattemessig-datadef-7414 orid="7414">' . $data['D7414'] . '</FremmedeTjenesterSkattemessig-datadef-7414>
			<KontorkostnadTelefonMvSkattemessig-datadef-7416 orid="7416">' . $data['D7416'] . '</KontorkostnadTelefonMvSkattemessig-datadef-7416>
			<TransportmidlerDrivstoffSkattemessig-datadef-7418 orid="7418">' . $data['D7418'] . '</TransportmidlerDrivstoffSkattemessig-datadef-7418>
			<TransportmidlerVedlikeholdMvSkattemessig-datadef-7422 orid="7422">' . $data['D7422'] . '</TransportmidlerVedlikeholdMvSkattemessig-datadef-7422>
			<NaringskostnadForsikringAvgifterTransportmidler-datadef-15851 orid="15851">' . $data['D15851'] . '</NaringskostnadForsikringAvgifterTransportmidler-datadef-15851>
			<BilkostnaderPrivatBilSkattemessig-datadef-7273 orid="7273">' . $data['D7273'] . '</BilkostnaderPrivatBilSkattemessig-datadef-7273>
			<NaringsbilBruktPrivat-datadef-15801 orid="15801">' . $data['D15801'] . '</NaringsbilBruktPrivat-datadef-15801>
			<ReiseDiettBilgodtgjorelseOppgavepliktigSkattemessig-datadef-7424 orid="7424">' . $data['D7424'] . '</ReiseDiettBilgodtgjorelseOppgavepliktigSkattemessig-datadef-7424>
			<ReiseDiettIkkeOppgavepliktigSkattemessig-datadef-7426 orid="7426">' . $data['D7426'] . '</ReiseDiettIkkeOppgavepliktigSkattemessig-datadef-7426>
			<ProvisjonskostnaderSkattemessig-datadef-7428 orid="7428">' . $data['D7428'] . '</ProvisjonskostnaderSkattemessig-datadef-7428>
			<SalgReklameSkattemessig-datadef-7275 orid="7275">' . $data['D7275'] . '</SalgReklameSkattemessig-datadef-7275>
			<RepresentasjonFradragsberettigetSkattemessig-datadef-7277 orid="7277">' . $data['D7277'] . '</RepresentasjonFradragsberettigetSkattemessig-datadef-7277>
			<KontingenterGaverFradragsberettigetSkattemessig-datadef-7279 orid="7279">' . $data['D7279'] . '</KontingenterGaverFradragsberettigetSkattemessig-datadef-7279>
			<ForsikringspremierSkattemessig-datadef-11334 orid="11334">' . $data['D11334'] . '</ForsikringspremierSkattemessig-datadef-11334>
			<GarantiServiceSkattemessig-datadef-7430 orid="7430">' . $data['D7430'] . '</GarantiServiceSkattemessig-datadef-7430>
			<PatentLisensRoyaltiesSkattemessig-datadef-7432 orid="7432">' . $data['D7432'] . '</PatentLisensRoyaltiesSkattemessig-datadef-7432>
			<NaringskostnadAndre-datadef-15837 orid="15837">' . $data['D15837'] . '</NaringskostnadAndre-datadef-15837>
			<FordringerTapSkattemessig-datadef-7434 orid="7434">' . $data['D7434'] . '</FordringerTapSkattemessig-datadef-7434>
			<FordringerKunderNedskrivningEndringSkattemessig-datadef-7283 orid="7283">' . $data['D7283'] . '</FordringerKunderNedskrivningEndringSkattemessig-datadef-7283>
			<Naringskostnad-datadef-7286 orid="7286">' . $data['D7286'] . '</Naringskostnad-datadef-7286>
			<NaringsinntektNettoForKapitalposter-datadef-6686 orid="6686">' . $data['D6686'] . '</NaringsinntektNettoForKapitalposter-datadef-6686>
		</DetteAr-grp-885>
		<Fjoraret-grp-884 gruppeid="884">
			<VarekostnadSkattemessigFjoraret-datadef-7379 orid="7379">' . $data['D7379'] . '</VarekostnadSkattemessigFjoraret-datadef-7379>
			<BeholdningsendringerVarerEgentilvirkedeSkattemessigFjoraret-datadef-7381 orid="7381">' . $data['D7381'] . '</BeholdningsendringerVarerEgentilvirkedeSkattemessigFjoraret-datadef-7381>
			<FremmedytelserSkattemessigFjoraret-datadef-7383 orid="7383">' . $data['D7383'] . '</FremmedytelserSkattemessigFjoraret-datadef-7383>
			<BeholdningsendringerAnleggsmidlerEgentilvSkattemessigFjoraret-datadef-7385 orid="7385">' . $data['D7385'] . '</BeholdningsendringerAnleggsmidlerEgentilvSkattemessigFjoraret-datadef-7385>
			<LonnskostnaderSkattemessigFjoraret-datadef-7387 orid="7387">' . $data['D7387'] . '</LonnskostnaderSkattemessigFjoraret-datadef-7387>
			<GodtgjorelserAndreOppgavepliktigSkattemessigFjoraret-datadef-7389 orid="7389">' . $data['D7389'] . '</GodtgjorelserAndreOppgavepliktigSkattemessigFjoraret-datadef-7389>
			<ArbeidsgiveravgiftSkattemessigFjoraret-datadef-7391 orid="7391">' . $data['D7391'] . '</ArbeidsgiveravgiftSkattemessigFjoraret-datadef-7391>
			<PensjonskostnaderInnberetningspliktigeSkattemessigFjoraret-datadef-7393 orid="7393">' . $data['D7393'] . '</PensjonskostnaderInnberetningspliktigeSkattemessigFjoraret-datadef-7393>
			<GodtgjorelserDeltakerlignedeSelskaperSkattemessigFjoraret-datadef-7395 orid="7395">' . $data['D7395'] . '</GodtgjorelserDeltakerlignedeSelskaperSkattemessigFjoraret-datadef-7395>
			<PersonalkostnaderAndreSkattemessigFjoraret-datadef-7397 orid="7397">' . $data['D7397'] . '</PersonalkostnaderAndreSkattemessigFjoraret-datadef-7397>
			<AvskrivningerOrdinareSkattemessigFjoraret-datadef-7399 orid="7399">' . $data['D7399'] . '</AvskrivningerOrdinareSkattemessigFjoraret-datadef-7399>
			<FraktTransportkostnaderSalgSkattemessigFjoraret-datadef-7401 orid="7401">' . $data['D7401'] . '</FraktTransportkostnaderSalgSkattemessigFjoraret-datadef-7401>
			<EnergiProduksjonSkattemessigFjoraret-datadef-7403 orid="7403">' . $data['D7403'] . '</EnergiProduksjonSkattemessigFjoraret-datadef-7403>
			<LeiekostnaderFastEiendomSkattemessigFjoraret-datadef-7405 orid="7405">' . $data['D7405'] . '</LeiekostnaderFastEiendomSkattemessigFjoraret-datadef-7405>
			<LysVarmeSkattemessigFjoraret-datadef-7409 orid="7409">' . $data['D7409'] . '</LysVarmeSkattemessigFjoraret-datadef-7409>
			<RenovasjonRenholdMvSkattemessigFjoraret-datadef-7407 orid="7407">' . $data['D7407'] . '</RenovasjonRenholdMvSkattemessigFjoraret-datadef-7407>
			<LeiekostnaderDriftsmidlerSkattemessigFjoraret-datadef-7411 orid="7411">' . $data['D7411'] . '</LeiekostnaderDriftsmidlerSkattemessigFjoraret-datadef-7411>
			<DriftsmaterialerIkkeAktivertSkattemessigFjoraret-datadef-7413 orid="7413">' . $data['D7413'] . '</DriftsmaterialerIkkeAktivertSkattemessigFjoraret-datadef-7413>
			<VedlikeholdReparasjonBygningerSkattemessigFjoraret-datadef-7270 orid="7270">' . $data['D7270'] . '</VedlikeholdReparasjonBygningerSkattemessigFjoraret-datadef-7270>
			<VedlikeholdReparasjonAnnetSkattemessigFjoraret-datadef-7272 orid="7272">' . $data['D7272'] . '</VedlikeholdReparasjonAnnetSkattemessigFjoraret-datadef-7272>
			<FremmedeTjenesterSkattemessigFjoraret-datadef-7415 orid="7415">' . $data['D7415'] . '</FremmedeTjenesterSkattemessigFjoraret-datadef-7415>
			<KontorkostnadTelefonMvSkattemessigFjoraret-datadef-7417 orid="7417">' . $data['D7417'] . '</KontorkostnadTelefonMvSkattemessigFjoraret-datadef-7417>
			<TransportmidlerDrivstoffSkattemessigFjoraret-datadef-7419 orid="7419">' . $data['D7419'] . '</TransportmidlerDrivstoffSkattemessigFjoraret-datadef-7419>
			<TransportmidlerVedlikeholdMvSkattemessigFjoraret-datadef-7423 orid="7423">' . $data['D7423'] . '</TransportmidlerVedlikeholdMvSkattemessigFjoraret-datadef-7423>
			<TransportmidlerForsikringAvgifterSkattemessigFjoraret-datadef-7421 orid="7421">' . $data['D7421'] . '</TransportmidlerForsikringAvgifterSkattemessigFjoraret-datadef-7421>
			<BilkostnaderPrivatBilSkattemessigFjoraret-datadef-7274 orid="7274">' . $data['D7274'] . '</BilkostnaderPrivatBilSkattemessigFjoraret-datadef-7274>
			<NaringsbilPrivatBrukFjoraret-datadef-7265 orid="7265">' . $data['D7265'] . '</NaringsbilPrivatBrukFjoraret-datadef-7265>
			<ReiseDiettBilgodtgjorelseOppgavepliktigSkattemessigFjoraret-datadef-7425 orid="7425">' . $data['D7425'] . '</ReiseDiettBilgodtgjorelseOppgavepliktigSkattemessigFjoraret-datadef-7425>
			<ReiseDiettIkkeOppgavepliktigSkattemessigFjoraret-datadef-7427 orid="7427">' . $data['D7427'] . '</ReiseDiettIkkeOppgavepliktigSkattemessigFjoraret-datadef-7427>
			<ProvisjonskostnaderSkattemessigFjoraret-datadef-7429 orid="7429">' . $data['D7429'] . '</ProvisjonskostnaderSkattemessigFjoraret-datadef-7429>
			<SalgReklameSkattemessigFjoraret-datadef-7276 orid="7276">' . $data['D7276'] . '</SalgReklameSkattemessigFjoraret-datadef-7276>
			<RepresentasjonFradragsberettigetSkattemessigFjoraret-datadef-7278 orid="7278">' . $data['D7278'] . '</RepresentasjonFradragsberettigetSkattemessigFjoraret-datadef-7278>
			<KontingenterGaverFradragsberettigetSkattemessigFjoraret-datadef-7280 orid="7280">' . $data['D7280'] . '</KontingenterGaverFradragsberettigetSkattemessigFjoraret-datadef-7280>
			<ForsikringspremierSkattemessigFjoraret-datadef-11335 orid="11335">' . $data['D11335'] . '</ForsikringspremierSkattemessigFjoraret-datadef-11335>
			<GarantiServiceSkattemessigFjoraret-datadef-7431 orid="7431">' . $data['D7431'] . '</GarantiServiceSkattemessigFjoraret-datadef-7431>
			<PatentLisensRoyaltiesSkattemessigFjoraret-datadef-7433 orid="7433">' . $data['D7433'] . '</PatentLisensRoyaltiesSkattemessigFjoraret-datadef-7433>
			<DriftskostnaderAndreFradragsberettigetSkattemessigFjoraret-datadef-7282 orid="7282">' . $data['D7282'] . '</DriftskostnaderAndreFradragsberettigetSkattemessigFjoraret-datadef-7282>
			<FordringerTapSkattemessigFjoraret-datadef-7435 orid="7435">' . $data['D7435'] . '</FordringerTapSkattemessigFjoraret-datadef-7435>
			<FordringerKunderNedskrivningEndringSkattemessigFjoraret-datadef-7284 orid="7284">' . $data['D7284'] . '</FordringerKunderNedskrivningEndringSkattemessigFjoraret-datadef-7284>
			<NaringskostnadFjoraret-datadef-7287 orid="7287">' . $data['D7287'] . '</NaringskostnadFjoraret-datadef-7287>
			<NaringsinntektNettoForKapitalposterFjoraret-datadef-6687 orid="6687">' . $data['D6687'] . '</NaringsinntektNettoForKapitalposterFjoraret-datadef-6687>
		</Fjoraret-grp-884>
	</Driftskostnad-grp-279>
</SkattemessigResultatregnskapDriftskostnad-grp-2187>
<SkattemessigResultatregnskapKapital-grp-2188 gruppeid="2188">
	<Kapitalinntekt-grp-284 gruppeid="284">
		<DetteAr-grp-890 gruppeid="890">
			<KapitalinntektValutagevinst-datadef-15852 orid="15852">' . $data['D15852'] . '</KapitalinntektValutagevinst-datadef-15852>
			<AksjerMvRealisasjonGevinst-datadef-15775 orid="15775">' . $data['D15775'] . '</AksjerMvRealisasjonGevinst-datadef-15775>
			<KapitalinntektAnnen-datadef-15853 orid="15853">' . $data['D15853'] . '</KapitalinntektAnnen-datadef-15853>
			<GevinstTapskontoPositivInntektsfort-datadef-13676 orid="13676">' . $data['D13676'] . '</GevinstTapskontoPositivInntektsfort-datadef-13676>
			<KapitalinntekterSkattemessig-datadef-13962 orid="13962">' . $data['D13962'] . '</KapitalinntekterSkattemessig-datadef-13962>
		</DetteAr-grp-890>
		<Fjoraret-grp-892 gruppeid="892">
			<ValutagevinstAgioSkattemessigFjoraret-datadef-7437 orid="7437">' . $data['D7437'] . '</ValutagevinstAgioSkattemessigFjoraret-datadef-7437>
			<AksjerMvGevinstSkattFjoraret-datadef-7291 orid="7291">' . $data['D7291'] . '</AksjerMvGevinstSkattFjoraret-datadef-7291>
			<KapitalinntekterAndreSkattemessigFjoraret-datadef-7292 orid="7292">' . $data['D7292'] . '</KapitalinntekterAndreSkattemessigFjoraret-datadef-7292>
			<GevinstTapskontoPositivInntektsfortFjoraret-datadef-7295 orid="7295">' . $data['D7295'] . '</GevinstTapskontoPositivInntektsfortFjoraret-datadef-7295>
			<KapitalinntekterSkattemessigFjoraret-datadef-13963 orid="13963">' . $data['D13963'] . '</KapitalinntekterSkattemessigFjoraret-datadef-13963>
		</Fjoraret-grp-892>
	</Kapitalinntekt-grp-284>
	<Kapitalkostnad-grp-285 gruppeid="285">
		<DetteAr-grp-893 gruppeid="893">
			<KapitalkostnadValutatap-datadef-15854 orid="15854">' . $data['D15854'] . '</KapitalkostnadValutatap-datadef-15854>
			<AksjerMvRealisasjonTap-datadef-15776 orid="15776">' . $data['D15776'] . '</AksjerMvRealisasjonTap-datadef-15776>
			<KapitalkostnaderAndreSkattemessig-datadef-7441 orid="7441">' . $data['D7441'] . '</KapitalkostnaderAndreSkattemessig-datadef-7441>
			<GevinstTapskontoNegativFradragsfort-datadef-1202 orid="1202">' . $data['D1202'] . '</GevinstTapskontoNegativFradragsfort-datadef-1202>
			<KjopsutbytteSamvirkeforetakAvsattSkattemessig-datadef-7442 orid="7442">' . $data['D7442'] . '</KjopsutbytteSamvirkeforetakAvsattSkattemessig-datadef-7442>
			<AndelskapitalFelleseidSamvirkeforetakAvsetningerSkattemessige-datadef-7443 orid="7443">' . $data['D7443'] . '</AndelskapitalFelleseidSamvirkeforetakAvsetningerSkattemessige-datadef-7443>
			<KapitalkostnaderSkattemessig-datadef-13964 orid="13964">' . $data['D13964'] . '</KapitalkostnaderSkattemessig-datadef-13964>
			<NaringsinntektGrunnlagPersoninntekt-datadef-6675 orid="6675">' . $data['D6675'] . '</NaringsinntektGrunnlagPersoninntekt-datadef-6675>
		</DetteAr-grp-893>
		<Fjoraret-grp-895 gruppeid="895">
			<ValutatapDisagioSkattemessigFjoraret-datadef-7440 orid="7440">' . $data['D7440'] . '</ValutatapDisagioSkattemessigFjoraret-datadef-7440>
			<AksjerMvTapSkattFjoraret-datadef-7293 orid="7293">' . $data['D7293'] . '</AksjerMvTapSkattFjoraret-datadef-7293>
			<KapitalkostnaderAndreSkattemessigFjoraret-datadef-7294 orid="7294">' . $data['D7294'] . '</KapitalkostnaderAndreSkattemessigFjoraret-datadef-7294>
			<GevinstTapskontoNegativFradragsfortFjoraret-datadef-7296 orid="7296">' . $data['D7296'] . '</GevinstTapskontoNegativFradragsfortFjoraret-datadef-7296>
			<KjopsutbytteSamvirkeforetakAvsattSkattemessigFjoraret-datadef-7297 orid="7297">' . $data['D7297'] . '</KjopsutbytteSamvirkeforetakAvsattSkattemessigFjoraret-datadef-7297>
			<AndelskapitalFelleseidSamvirkeforetakAvsetnSkattemessigFjor-datadef-7444 orid="7444">' . $data['D7444'] . '</AndelskapitalFelleseidSamvirkeforetakAvsetnSkattemessigFjor-datadef-7444>
			<KapitalkostnaderSkattemessigFjoraret-datadef-13965 orid="13965">' . $data['D13965'] . '</KapitalkostnaderSkattemessigFjoraret-datadef-13965>
			<ResultatFjoraret-datadef-19789 orid="19789">' . $data['D19789'] . '</ResultatFjoraret-datadef-19789>
		</Fjoraret-grp-895>
	</Kapitalkostnad-grp-285>
</SkattemessigResultatregnskapKapital-grp-2188>
<BalanseEiendeler-grp-2189 gruppeid="2189">
	<Eiendeler-grp-287 gruppeid="287">
		<DetteAr-grp-2190 gruppeid="2190">
			<FoUSkattemessig-datadef-7445 orid="7445">' . $data['D7445'] . '</FoUSkattemessig-datadef-7445>
			<EiendelerImmaterielle-datadef-2400 orid="2400">' . $data['D2400'] . '</EiendelerImmaterielle-datadef-2400>
			<ForretningsverdiGoodwillSkattemessig-datadef-7447 orid="7447">' . $data['D7447'] . '</ForretningsverdiGoodwillSkattemessig-datadef-7447>
			<ForretningsbyggBalanse-datadef-15796 orid="15796">' . $data['D15796'] . '</ForretningsbyggBalanse-datadef-15796>
			<ByggAnleggMvBalanse-datadef-15795 orid="15795">' . $data['D15795'] . '</ByggAnleggMvBalanse-datadef-15795>
			<AnleggMaskinerUnderUtforelseSkattemessig-datadef-7451 orid="7451">' . $data['D7451'] . '</AnleggMaskinerUnderUtforelseSkattemessig-datadef-7451>
			<JordSkogverdierSkattemessig-datadef-7453 orid="7453">' . $data['D7453'] . '</JordSkogverdierSkattemessig-datadef-7453>
			<TomterGrunnarealerSkattemessig-datadef-7454 orid="7454">' . $data['D7454'] . '</TomterGrunnarealerSkattemessig-datadef-7454>
			<BoligerBoligtomterSkattemessig-datadef-7456 orid="7456">' . $data['D7456'] . '</BoligerBoligtomterSkattemessig-datadef-7456>
			<MaskinerMvBalanse-datadef-15792 orid="15792">' . $data['D15792'] . '</MaskinerMvBalanse-datadef-15792>
			<SkipRiggerMvBalanse-datadef-15793 orid="15793">' . $data['D15793'] . '</SkipRiggerMvBalanse-datadef-15793>
			<FlyHelikopterMvBalanse-datadef-15794 orid="15794">' . $data['D15794'] . '</FlyHelikopterMvBalanse-datadef-15794>
			<VareLastebilerBusserMvBalanse-datadef-15791 orid="15791">' . $data['D15791'] . '</VareLastebilerBusserMvBalanse-datadef-15791>
			<KontormaskinerBalanse-datadef-15790 orid="15790">' . $data['D15790'] . '</KontormaskinerBalanse-datadef-15790>
			<EiendelerAvskrivbareUtenomSaldosystemetSkattemessig-datadef-7306 orid="7306">' . $data['D7306'] . '</EiendelerAvskrivbareUtenomSaldosystemetSkattemessig-datadef-7306>
			<GevinstTapskontoNegativBalanse-datadef-15838 orid="15838">' . $data['D15838'] . '</GevinstTapskontoNegativBalanse-datadef-15838>
			<Varelager-datadef-15768 orid="15768">' . $data['D15768'] . '</Varelager-datadef-15768>
			<FordringerAnsatteSkattemessig-datadef-7465 orid="7465">' . $data['D7465'] . '</FordringerAnsatteSkattemessig-datadef-7465>
			<FordringerEiereStyremedlemmerOlSkattemessig-datadef-7467 orid="7467">' . $data['D7467'] . '</FordringerEiereStyremedlemmerOlSkattemessig-datadef-7467>
			<FordringerLangsiktigUtenlandskValutaSkattemessig-datadef-7310 orid="7310">' . $data['D7310'] . '</FordringerLangsiktigUtenlandskValutaSkattemessig-datadef-7310>
			<FordringerAndreSkattemessig-datadef-7469 orid="7469">' . $data['D7469'] . '</FordringerAndreSkattemessig-datadef-7469>
			<SelskapskapitalInnbetalingKravSkattemessig-datadef-7471 orid="7471">' . $data['D7471'] . '</SelskapskapitalInnbetalingKravSkattemessig-datadef-7471>
			<AksjerMvBalanseSkattemessig-datadef-15788 orid="15788">' . $data['D15788'] . '</AksjerMvBalanseSkattemessig-datadef-15788>
			<VerdipapirerSkattemessig-datadef-7473 orid="7473">' . $data['D7473'] . '</VerdipapirerSkattemessig-datadef-7473>
			<FinansielleInstrumenterAndreSkattemessig-datadef-7474 orid="7474">' . $data['D7474'] . '</FinansielleInstrumenterAndreSkattemessig-datadef-7474>
			<AndelerDeltakerlignetSelskapBalanse-datadef-15789 orid="15789">' . $data['D15789'] . '</AndelerDeltakerlignetSelskapBalanse-datadef-15789>
			<KontanterBalanse-datadef-15812 orid="15812">' . $data['D15812'] . '</KontanterBalanse-datadef-15812>
			<BankinnskuddSkattemessig-datadef-7477 orid="7477">' . $data['D7477'] . '</BankinnskuddSkattemessig-datadef-7477>
			<BankinnskuddSkattetrekkSkattemessig-datadef-7315 orid="7315">' . $data['D7315'] . '</BankinnskuddSkattetrekkSkattemessig-datadef-7315>
			<EiendelerBalanse-datadef-15797 orid="15797">' . $data['D15797'] . '</EiendelerBalanse-datadef-15797>
		</DetteAr-grp-2190>
		<Fjoraret-grp-2191 gruppeid="2191">
			<FoUSkattemessigFjoraret-datadef-7446 orid="7446">' . $data['D7446'] . '</FoUSkattemessigFjoraret-datadef-7446>
			<EiendelerImmaterielleFjoraret-datadef-8006 orid="8006">' . $data['D8006'] . '</EiendelerImmaterielleFjoraret-datadef-8006>
			<ForretningsverdiGoodwillSkattemessigFjoraret-datadef-7448 orid="7448">' . $data['D7448'] . '</ForretningsverdiGoodwillSkattemessigFjoraret-datadef-7448>
			<ForretningsbyggSkattemessigVerdiFjoraret-datadef-7449 orid="7449">' . $data['D7449'] . '</ForretningsbyggSkattemessigVerdiFjoraret-datadef-7449>
			<ByggAnleggSkattemessigVerdiFjoraret-datadef-7450 orid="7450">' . $data['D7450'] . '</ByggAnleggSkattemessigVerdiFjoraret-datadef-7450>
			<AnleggMaskinerUnderUtforelseSkattemessigFjoraret-datadef-7452 orid="7452">' . $data['D7452'] . '</AnleggMaskinerUnderUtforelseSkattemessigFjoraret-datadef-7452>
			<JordSkogverdierSkattemessigFjoraret-datadef-7305 orid="7305">' . $data['D7305'] . '</JordSkogverdierSkattemessigFjoraret-datadef-7305>
			<TomterGrunnarealerSkattemessigFjoraret-datadef-7455 orid="7455">' . $data['D7455'] . '</TomterGrunnarealerSkattemessigFjoraret-datadef-7455>
			<BoligerBoligtomterSkattemessigFjoraret-datadef-7457 orid="7457">' . $data['D7457'] . '</BoligerBoligtomterSkattemessigFjoraret-datadef-7457>
			<PersonbilerTraktorerMaskinerMvSkattemessigFjoraret-datadef-7458 orid="7458">' . $data['D7458'] . '</PersonbilerTraktorerMaskinerMvSkattemessigFjoraret-datadef-7458>
			<SkipFartoyerRiggerMvSkattemessigVerdiFjoraret-datadef-7459 orid="7459">' . $data['D7459'] . '</SkipFartoyerRiggerMvSkattemessigVerdiFjoraret-datadef-7459>
			<FlyHelikopterSkattemessigVerdiFjoraret-datadef-7460 orid="7460">' . $data['D7460'] . '</FlyHelikopterSkattemessigVerdiFjoraret-datadef-7460>
			<VogntogLastebilerVarebilerMvSkattemessigVerdiFjoraret-datadef-7461 orid="7461">' . $data['D7461'] . '</VogntogLastebilerVarebilerMvSkattemessigVerdiFjoraret-datadef-7461>
			<KontormaskinerMvSkattemessigVerdiFjoraret-datadef-7462 orid="7462">' . $data['D7462'] . '</KontormaskinerMvSkattemessigVerdiFjoraret-datadef-7462>
			<EiendelerAvskrivbareUtenomSaldosystemetSkattemessigFjoraret-datadef-7307 orid="7307">' . $data['D7307'] . '</EiendelerAvskrivbareUtenomSaldosystemetSkattemessigFjoraret-datadef-7307>
			<GevinstTapskontoNegativFjoraret-datadef-7309 orid="7309">' . $data['D7309'] . '</GevinstTapskontoNegativFjoraret-datadef-7309>
			<VarelagerFjoraret-datadef-15819 orid="15819">' . $data['D15819'] . '</VarelagerFjoraret-datadef-15819>
			<FordringerAnsatteSkattemessigFjoraret-datadef-7466 orid="7466">' . $data['D7466'] . '</FordringerAnsatteSkattemessigFjoraret-datadef-7466>
			<FordringerEiereStyremedlemmerOlSkattemessigFjoraret-datadef-7468 orid="7468">' . $data['D7468'] . '</FordringerEiereStyremedlemmerOlSkattemessigFjoraret-datadef-7468>
			<FordringerLangsiktigUtenlandskValutaSkattemessigFjoraret-datadef-7311 orid="7311">' . $data['D7311'] . '</FordringerLangsiktigUtenlandskValutaSkattemessigFjoraret-datadef-7311>
			<FordringerAndreSkattemessigFjoraret-datadef-7470 orid="7470">' . $data['D7470'] . '</FordringerAndreSkattemessigFjoraret-datadef-7470>
			<SelskapskapitalInnbetalingKravSkattemessigFjoraret-datadef-7472 orid="7472">' . $data['D7472'] . '</SelskapskapitalInnbetalingKravSkattemessigFjoraret-datadef-7472>
			<AksjerMvBalanseSkattemessigFjoraret-datadef-15833 orid="15833">' . $data['D15833'] . '</AksjerMvBalanseSkattemessigFjoraret-datadef-15833>
			<VerdipapirerSkattemessigFjoraret-datadef-7312 orid="7312">' . $data['D7312'] . '</VerdipapirerSkattemessigFjoraret-datadef-7312>
			<FinansielleInstrumenterAndreSkattemessigFjoraret-datadef-7475 orid="7475">' . $data['D7475'] . '</FinansielleInstrumenterAndreSkattemessigFjoraret-datadef-7475>
			<AndelerDeltakerlignedeSelskapBalanseFjoraret-datadef-15834 orid="15834">' . $data['D15834'] . '</AndelerDeltakerlignedeSelskapBalanseFjoraret-datadef-15834>
			<KontanterSkattemessigFjoraret-datadef-7476 orid="7476">' . $data['D7476'] . '</KontanterSkattemessigFjoraret-datadef-7476>
			<BankinnskuddSkattemessigFjoraret-datadef-7478 orid="7478">' . $data['D7478'] . '</BankinnskuddSkattemessigFjoraret-datadef-7478>
			<BankinnskuddSkattetrekkSkattemessigFjoraret-datadef-7316 orid="7316">' . $data['D7316'] . '</BankinnskuddSkattetrekkSkattemessigFjoraret-datadef-7316>
			<EiendelerSkattemessigeFjoraret-datadef-7317 orid="7317">' . $data['D7317'] . '</EiendelerSkattemessigeFjoraret-datadef-7317>
		</Fjoraret-grp-2191>
	</Eiendeler-grp-287>
</BalanseEiendeler-grp-2189>
<BalanseEgenkapital-grp-2192 gruppeid="2192">
	<SkattemessigEgenkapital-grp-288 gruppeid="288">
		<DetteAr-grp-2193 gruppeid="2193">
			<AndelskapitalFelleseidSkattemessig-datadef-7479 orid="7479">' . $data['D7479'] . '</AndelskapitalFelleseidSkattemessig-datadef-7479>
			<EgenkapitalAnnenSkattemessig-datadef-7481 orid="7481">' . $data['D7481'] . '</EgenkapitalAnnenSkattemessig-datadef-7481>
			<TapUdekketSkattemessig-datadef-7483 orid="7483">' . $data['D7483'] . '</TapUdekketSkattemessig-datadef-7483>
			<EgenkapitalBeskattet-datadef-7318 orid="7318">' . $data['D7318'] . '</EgenkapitalBeskattet-datadef-7318>
		</DetteAr-grp-2193>
		<Fjoraret-grp-2194 gruppeid="2194">
			<AndelskapitalFelleseidSkattemessigFjoraret-datadef-7480 orid="7480">' . $data['D7480'] . '</AndelskapitalFelleseidSkattemessigFjoraret-datadef-7480>
			<EgenkapitalAnnenSkattemessigFjoraret-datadef-7482 orid="7482">' . $data['D7482'] . '</EgenkapitalAnnenSkattemessigFjoraret-datadef-7482>
			<TapUdekketSkattemessigFjoraret-datadef-7484 orid="7484">' . $data['D7484'] . '</TapUdekketSkattemessigFjoraret-datadef-7484>
			<EgenkapitalBeskattetFjoraret-datadef-7319 orid="7319">' . $data['D7319'] . '</EgenkapitalBeskattetFjoraret-datadef-7319>
		</Fjoraret-grp-2194>
	</SkattemessigEgenkapital-grp-288>
	<UbeskattetEgenkapital-grp-289 gruppeid="289">
		<DetteAr-grp-2195 gruppeid="2195">
			<SaldoNegativ-datadef-1471 orid="1471">' . $data['D1471'] . '</SaldoNegativ-datadef-1471>
			<GevinstTapskontoPositivUbeskattet-datadef-15839 orid="15839">' . $data['D15839'] . '</GevinstTapskontoPositivUbeskattet-datadef-15839>
			<GevinstBetingetAvsattUbeskattet-datadef-15803 orid="15803">' . $data['D15803'] . '</GevinstBetingetAvsattUbeskattet-datadef-15803>
			<EgenkapitalUbeskattet-datadef-7324 orid="7324">' . $data['D7324'] . '</EgenkapitalUbeskattet-datadef-7324>
		</DetteAr-grp-2195>
		<Fjoraret-grp-2196 gruppeid="2196">
			<SaldoNegativFjoraret-datadef-7320 orid="7320">' . $data['D7320'] . '</SaldoNegativFjoraret-datadef-7320>
			<GevinstTapskontoPositivFjoraret-datadef-7322 orid="7322">' . $data['D7322'] . '</GevinstTapskontoPositivFjoraret-datadef-7322>
			<GevinstBetingetAvsattUbeskattetFjoraret-datadef-15840 orid="15840">' . $data['D15840'] . '</GevinstBetingetAvsattUbeskattetFjoraret-datadef-15840>
			<EgenkapitalUbeskattetFjoraret-datadef-7325 orid="7325">' . $data['D7325'] . '</EgenkapitalUbeskattetFjoraret-datadef-7325>
		</Fjoraret-grp-2196>
	</UbeskattetEgenkapital-grp-289>
</BalanseEgenkapital-grp-2192>
<BalanseGjeld-grp-2198 gruppeid="2198">
	<Gjeld-grp-290 gruppeid="290">
		<DetteAr-grp-2199 gruppeid="2199">
			<GjeldKredittinstitusjonerSkattemessig-datadef-7485 orid="7485">' . $data['D7485'] . '</GjeldKredittinstitusjonerSkattemessig-datadef-7485>
			<LanLangsiktigUtenlandskValutaSkattemessig-datadef-7327 orid="7327">' . $data['D7327'] . '</LanLangsiktigUtenlandskValutaSkattemessig-datadef-7327>
			<StilleInteressentinnskuddAnsvarligLanekapitalSkattemessig-datadef-7487 orid="7487">' . $data['D7487'] . '</StilleInteressentinnskuddAnsvarligLanekapitalSkattemessig-datadef-7487>
			<KassekredittSkattemessig-datadef-7489 orid="7489">' . $data['D7489'] . '</KassekredittSkattemessig-datadef-7489>
			<LeverandorgjeldSkattemessig-datadef-7491 orid="7491">' . $data['D7491'] . '</LeverandorgjeldSkattemessig-datadef-7491>
			<SkattetrekkAndreTrekkSkattemessig-datadef-7493 orid="7493">' . $data['D7493'] . '</SkattetrekkAndreTrekkSkattemessig-datadef-7493>
			<MerverdiavgiftSkyldigSkattemessig-datadef-7495 orid="7495">' . $data['D7495'] . '</MerverdiavgiftSkyldigSkattemessig-datadef-7495>
			<ArbeidsgiveravgiftSkyldigSkattemessig-datadef-7497 orid="7497">' . $data['D7497'] . '</ArbeidsgiveravgiftSkyldigSkattemessig-datadef-7497>
			<AvgifterOffentligeSkyldigSkattemessig-datadef-7499 orid="7499">' . $data['D7499'] . '</AvgifterOffentligeSkyldigSkattemessig-datadef-7499>
			<ForskuddKunderSkattemessig-datadef-7501 orid="7501">' . $data['D7501'] . '</ForskuddKunderSkattemessig-datadef-7501>
			<GjeldAnsatteEiereSkattemessig-datadef-7503 orid="7503">' . $data['D7503'] . '</GjeldAnsatteEiereSkattemessig-datadef-7503>
			<LonnFeriepengerMvSkyldigSkattemessig-datadef-7505 orid="7505">' . $data['D7505'] . '</LonnFeriepengerMvSkyldigSkattemessig-datadef-7505>
			<RenterPaloptSkattemessig-datadef-7507 orid="7507">' . $data['D7507'] . '</RenterPaloptSkattemessig-datadef-7507>
			<GjeldAnnenSkattemessig-datadef-7509 orid="7509">' . $data['D7509'] . '</GjeldAnnenSkattemessig-datadef-7509>
			<GjeldNaringBalanse-datadef-15855 orid="15855">' . $data['D15855'] . '</GjeldNaringBalanse-datadef-15855>
		</DetteAr-grp-2199>
		<Fjoraret-grp-2200 gruppeid="2200">
			<GjeldKredittinstitusjonerSkattemessigFjoraret-datadef-7486 orid="7486">' . $data['D7486'] . '</GjeldKredittinstitusjonerSkattemessigFjoraret-datadef-7486>
			<LanLangsiktigUtenlandskValutaSkattemessigFjoraret-datadef-7328 orid="7328">' . $data['D7328'] . '</LanLangsiktigUtenlandskValutaSkattemessigFjoraret-datadef-7328>
			<StilleInteressentinnskuddAnsvLanekapitalSkattemessigFjoraret-datadef-7488 orid="7488">' . $data['D7488'] . '</StilleInteressentinnskuddAnsvLanekapitalSkattemessigFjoraret-datadef-7488>
			<KassekredittSkattemessigFjoraret-datadef-7490 orid="7490">' . $data['D7490'] . '</KassekredittSkattemessigFjoraret-datadef-7490>
			<LeverandorgjeldSkattemessigFjoraret-datadef-7492 orid="7492">' . $data['D7492'] . '</LeverandorgjeldSkattemessigFjoraret-datadef-7492>
			<SkattetrekkAndreTrekkSkattemessigFjoraret-datadef-7494 orid="7494">' . $data['D7494'] . '</SkattetrekkAndreTrekkSkattemessigFjoraret-datadef-7494>
			<MerverdiavgiftSkyldigSkattemessigFjoraret-datadef-7496 orid="7496">' . $data['D7496'] . '</MerverdiavgiftSkyldigSkattemessigFjoraret-datadef-7496>
			<ArbeidsgiveravgiftSkyldigSkattemessigFjoraret-datadef-7498 orid="7498">' . $data['D7498'] . '</ArbeidsgiveravgiftSkyldigSkattemessigFjoraret-datadef-7498>
			<AvgifterOffentligeSkyldigSkattemessigFjoraret-datadef-7500 orid="7500">' . $data['D7500'] . '</AvgifterOffentligeSkyldigSkattemessigFjoraret-datadef-7500>
			<ForskuddKunderSkattemessigFjoraret-datadef-7502 orid="7502">' . $data['D7502'] . '</ForskuddKunderSkattemessigFjoraret-datadef-7502>
			<GjeldAnsatteEiereSkattemessigFjoraret-datadef-7504 orid="7504">' . $data['D7504'] . '</GjeldAnsatteEiereSkattemessigFjoraret-datadef-7504>
			<LonnFeriepengerMvSkyldigSkattemessigFjoraret-datadef-7506 orid="7506">' . $data['D7506'] . '</LonnFeriepengerMvSkyldigSkattemessigFjoraret-datadef-7506>
			<RenterPaloptSkattemessigFjoraret-datadef-7508 orid="7508">' . $data['D7508'] . '</RenterPaloptSkattemessigFjoraret-datadef-7508>
			<GjeldAnnenSkattemessigFjoraret-datadef-7329 orid="7329">' . $data['D7329'] . '</GjeldAnnenSkattemessigFjoraret-datadef-7329>
			<GjeldNaringSkattemessigFjoraret-datadef-7510 orid="7510">' . $data['D7510'] . '</GjeldNaringSkattemessigFjoraret-datadef-7510>
		</Fjoraret-grp-2200>
	</Gjeld-grp-290>
	<SumEgenkapitalOgGjeld-grp-2201 gruppeid="2201">
		<GjeldEgenkapitalSkattemessig-datadef-7511 orid="7511">' . $data['D7511'] . '</GjeldEgenkapitalSkattemessig-datadef-7511>
		<GjeldEgenkapitalSkattemessigFjoraret-datadef-7512 orid="7512">' . $data['D7512'] . '</GjeldEgenkapitalSkattemessigFjoraret-datadef-7512>
	</SumEgenkapitalOgGjeld-grp-2201>
</BalanseGjeld-grp-2198>
<KorrigertEgenkapital-grp-2197 gruppeid="2197">
	<EgenkapitalNaringsoppgave2Fjoraret-datadef-15832 orid="15832">' . $data['D15832'] . '</EgenkapitalNaringsoppgave2Fjoraret-datadef-15832>
	<ForskjellerMidlertidigeFjoraret-datadef-15807 orid="15807">' . $data['D15807'] . '</ForskjellerMidlertidigeFjoraret-datadef-15807>
	<EgenkapitalSkattemessigFjoraret-datadef-7326 orid="7326">' . $data['D7326'] . '</EgenkapitalSkattemessigFjoraret-datadef-7326>
</KorrigertEgenkapital-grp-2197>
<SpesifikasjonAvBelopSomSkalOverforesTilPersoninntektsskjema-grp-866 gruppeid="866">
	<FordelingPaNaringer-grp-2204 gruppeid="2204">
		<EnhetNaringTypeSpesifisert-datadef-19811 orid="19811">' . $data['D19811'] . '</EnhetNaringTypeSpesifisert-datadef-19811>
		<EnhetNaringSpesifisertNaring-datadef-19890 orid="19890">' . $data['D19890'] . '</EnhetNaringSpesifisertNaring-datadef-19890>
		<NaringPersoninntektsskjemaSpesifisert-datadef-19790 orid="19790">' . $data['D19790'] . '</NaringPersoninntektsskjemaSpesifisert-datadef-19790>
		<ResultatFordelingSpesifisert-datadef-19791 orid="19791">' . $data['D19791'] . '</ResultatFordelingSpesifisert-datadef-19791>
		<ResultatSkogbrukReindriftKorreksjonerSpesifisert-datadef-19792 orid="19792">' . $data['D19792'] . '</ResultatSkogbrukReindriftKorreksjonerSpesifisert-datadef-19792>
		<ResultatPrimarnaringKorreksjonerAndreSpesifisert-datadef-19793 orid="19793">' . $data['D19793'] . '</ResultatPrimarnaringKorreksjonerAndreSpesifisert-datadef-19793>
		<EnhetNaringsinntektSkattepliktigSpesifisert-datadef-19794 orid="19794">' . $data['D19794'] . '</EnhetNaringsinntektSkattepliktigSpesifisert-datadef-19794>
		<EnhetNaringsinntektSkattepliktigInnehaverSpesifisert-datadef-19795 orid="19795">' . $data['D19795'] . '</EnhetNaringsinntektSkattepliktigInnehaverSpesifisert-datadef-19795>
		<EnhetNaringsinntektSkattepliktigEktefelleMvSpesifisert-datadef-19796 orid="19796">' . $data['D19796'] . '</EnhetNaringsinntektSkattepliktigEktefelleMvSpesifisert-datadef-19796>
	</FordelingPaNaringer-grp-2204>
	<SkattepliktigNaringsinntekt-grp-2444 gruppeid="2444">
		<EnhetNaringsinntektPositivSkattepliktig-datadef-19797 orid="19797">' . $data['D19797'] . '</EnhetNaringsinntektPositivSkattepliktig-datadef-19797>
		<EnhetNaringsinntektUnderskudd-datadef-19800 orid="19800">' . $data['D19800'] . '</EnhetNaringsinntektUnderskudd-datadef-19800>
	</SkattepliktigNaringsinntekt-grp-2444>
	<FordeltPaInnehaver-grp-2449 gruppeid="2449">
		<EnhetNaringsinntektPositivSkattepliktigInnehaver-datadef-19798 orid="19798">' . $data['D19798'] . '</EnhetNaringsinntektPositivSkattepliktigInnehaver-datadef-19798>
		<EnhetNaringsinntektUnderskuddInnehaver-datadef-19801 orid="19801">' . $data['D19801'] . '</EnhetNaringsinntektUnderskuddInnehaver-datadef-19801>
	</FordeltPaInnehaver-grp-2449>
	<FordeltPaEktefelleRegistrertPartner-grp-2450 gruppeid="2450">
		<EnhetNaringsinntektPositivSkattepliktigEktefelleMv-datadef-19799 orid="19799">' . $data['D19799'] . '</EnhetNaringsinntektPositivSkattepliktigEktefelleMv-datadef-19799>
		<EnhetNaringsinntektUnderskuddEktefelleMv-datadef-19802 orid="19802">' . $data['D19802'] . '</EnhetNaringsinntektUnderskuddEktefelleMv-datadef-19802>
	</FordeltPaEktefelleRegistrertPartner-grp-2450>
</SpesifikasjonAvBelopSomSkalOverforesTilPersoninntektsskjema-grp-866>
<Arsresultat-grp-4189 gruppeid="4189">
	<SkattepliktigNaringsinntekt-grp-4191 gruppeid="4191">
		<AksjeutbytteVirksomhet-datadef-15835 orid="15835">' . $data['D15835'] . '</AksjeutbytteVirksomhet-datadef-15835>
		<RenteinntekterNaring-datadef-15573 orid="15573">' . $data['D15573'] . '</RenteinntekterNaring-datadef-15573>
		<RentekostnaderNaring-datadef-15813 orid="15813">' . $data['D15813'] . '</RentekostnaderNaring-datadef-15813>
		<ArsresultatSkattemessig-datadef-7303 orid="7303">' . $data['D7303'] . '</ArsresultatSkattemessig-datadef-7303>
	</SkattepliktigNaringsinntekt-grp-4191>
</Arsresultat-grp-4189>
<SpesifikasjonAvPrivatkonto-grp-2202 gruppeid="2202">
	<DetteAr-grp-292 gruppeid="292">
		<PrivatkontoUttakKontanter-datadef-15781 orid="15781">' . $data['D15781'] . '</PrivatkontoUttakKontanter-datadef-15781>
		<PrivatkontoUttakDriftsmidler-datadef-15820 orid="15820">' . $data['D15820'] . '</PrivatkontoUttakDriftsmidler-datadef-15820>
		<PrivatkontoUttakVarerTjenester-datadef-15783 orid="15783">' . $data['D15783'] . '</PrivatkontoUttakVarerTjenester-datadef-15783>
		<PrivatkontoBoligNaringsbygg-datadef-15784 orid="15784">' . $data['D15784'] . '</PrivatkontoBoligNaringsbygg-datadef-15784>
		<PrivatkontoStrom-datadef-15785 orid="15785">' . $data['D15785'] . '</PrivatkontoStrom-datadef-15785>
		<PrivatkontoTelefonutgifter-datadef-15786 orid="15786">' . $data['D15786'] . '</PrivatkontoTelefonutgifter-datadef-15786>
		<PrivatkontoKostnaderAndre-datadef-15787 orid="15787">' . $data['D15787'] . '</PrivatkontoKostnaderAndre-datadef-15787>
		<PrivatkontoSkattekostnader-datadef-15782 orid="15782">' . $data['D15782'] . '</PrivatkontoSkattekostnader-datadef-15782>
		<PrivatkontoNaringsbilPrivatBruk-datadef-15822 orid="15822">' . $data['D15822'] . '</PrivatkontoNaringsbilPrivatBruk-datadef-15822>
		<PrivatkontoForsikring-datadef-22058 orid="22058">' . $data['D22058'] . '</PrivatkontoForsikring-datadef-22058>
		<PrivatkontoPremieTilleggstrygdSykepenger-datadef-15821 orid="15821">' . $data['D15821'] . '</PrivatkontoPremieTilleggstrygdSykepenger-datadef-15821>
		<PrivatkontoSum-datadef-15842 orid="15842">' . $data['D15842'] . '</PrivatkontoSum-datadef-15842>
	</DetteAr-grp-292>
</SpesifikasjonAvPrivatkonto-grp-2202>
<DriftskostnaderUtenFradragsrettEgenkapitalkorreksjoner-grp-2213 gruppeid="2213">
	<DriftskostFradragsrett-grp-293 gruppeid="293">
		<DetteAr-grp-3008 gruppeid="3008">
			<NaringskostnaderRepresentasjonIkkeFradragsberettiget-datadef-15772 orid="15772">' . $data['D15772'] . '</NaringskostnaderRepresentasjonIkkeFradragsberettiget-datadef-15772>
			<NaringskostnaderKontingenterGaverIkkeFradragsberettiget-datadef-15773 orid="15773">' . $data['D15773'] . '</NaringskostnaderKontingenterGaverIkkeFradragsberettiget-datadef-15773>
			<NaringskostnaderAndreIkkeFradragsberettigede-datadef-15774 orid="15774">' . $data['D15774'] . '</NaringskostnaderAndreIkkeFradragsberettigede-datadef-15774>
			<NaringskostnadIkkeFradragsberettiget-datadef-14111 orid="14111">' . $data['D14111'] . '</NaringskostnadIkkeFradragsberettiget-datadef-14111>
		</DetteAr-grp-3008>
	</DriftskostFradragsrett-grp-293>
	<DriftskostKapitalkorreksjoner-grp-294 gruppeid="294">
		<DetteAr-grp-3009 gruppeid="3009">
			<InntekterSkattefrie-datadef-7344 orid="7344">' . $data['D7344'] . '</InntekterSkattefrie-datadef-7344>
			<KontanterInnskudd-datadef-7345 orid="7345">' . $data['D7345'] . '</KontanterInnskudd-datadef-7345>
			<EiendelerInnskudd-datadef-22059 orid="22059">' . $data['D22059'] . '</EiendelerInnskudd-datadef-22059>
			<BilkostnaderPrivatBilUtenFradragsrett-datadef-15841 orid="15841">' . $data['D15841'] . '</BilkostnaderPrivatBilUtenFradragsrett-datadef-15841>
			<EgenkapitalKorreksjonerPositive-datadef-13967 orid="13967">' . $data['D13967'] . '</EgenkapitalKorreksjonerPositive-datadef-13967>
			<EgenkapitalKorreksjonerNegative-datadef-13968 orid="13968">' . $data['D13968'] . '</EgenkapitalKorreksjonerNegative-datadef-13968>
			<Egenkapitalkorreksjoner-datadef-15870 orid="15870">' . $data['D15870'] . '</Egenkapitalkorreksjoner-datadef-15870>
		</DetteAr-grp-3009>
	</DriftskostKapitalkorreksjoner-grp-294>
</DriftskostnaderUtenFradragsrettEgenkapitalkorreksjoner-grp-2213>';
}