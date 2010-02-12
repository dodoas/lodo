<?php
// Filnavn: schemaxml_245_3050.php
// Skjema: RF-1125    Opplysninger om bruk av bil
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1124 gruppeid="1124">
	<Avgiver-grp-1125 gruppeid="1125">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<Inntektsar-datadef-692 orid="692">' . $data['D692'] . '</Inntektsar-datadef-692>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-1125>
	<Regnskapsforer-grp-2633 gruppeid="2633">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-2633>
</GenerellInformasjon-grp-1124>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1124 gruppeid="1124">
	<Avgiver-grp-1125 gruppeid="1125">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<Inntektsar-datadef-692 orid="692">' . $data['D692'] . '</Inntektsar-datadef-692>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-1125>
	<Regnskapsforer-grp-2633 gruppeid="2633">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-2633>
</GenerellInformasjon-grp-1124>
<InformasjonOmBil-grp-3497 gruppeid="3497">
	<InformasjonOmBilOgSpesifikasjonAvDriftskostnader-grp-2771 gruppeid="2771">
		<BilRegistreringsnummerSpesifisertBil-datadef-7579 orid="7579">' . $data['D7579'] . '</BilRegistreringsnummerSpesifisertBil-datadef-7579>
		<BilkategoriSpesifisertBil-datadef-7576 orid="7576">' . $data['D7576'] . '</BilkategoriSpesifisertBil-datadef-7576>
		<BilMerkeTypeSpesifisertBil-datadef-7580 orid="7580">' . $data['D7580'] . '</BilMerkeTypeSpesifisertBil-datadef-7580>
		<BilArsmodellSpesifisertBil-datadef-7577 orid="7577">' . $data['D7577'] . '</BilArsmodellSpesifisertBil-datadef-7577>
		<BilListeprisSpesifisertBil-datadef-3114 orid="3114">' . $data['D3114'] . '</BilListeprisSpesifisertBil-datadef-3114>
		<DriftskostnaderSpesifisertBil-datadef-7578 orid="7578">' . $data['D7578'] . '</DriftskostnaderSpesifisertBil-datadef-7578>
		<BilKilometerstandSpesifisertBil-datadef-7581 orid="7581">' . $data['D7581'] . '</BilKilometerstandSpesifisertBil-datadef-7581>
		<BilKilometerstandFjoraretSpesifisertBil-datadef-7582 orid="7582">' . $data['D7582'] . '</BilKilometerstandFjoraretSpesifisertBil-datadef-7582>
		<BilKjorelengdePrivatSpesifisertBil-datadef-3116 orid="3116">' . $data['D3116'] . '</BilKjorelengdePrivatSpesifisertBil-datadef-3116>
		<BilKjorelengdeHjemArbeidSpesifisertBil-datadef-7583 orid="7583">' . $data['D7583'] . '</BilKjorelengdeHjemArbeidSpesifisertBil-datadef-7583>
		<BilBruksomradeSpesifisertBil-datadef-7584 orid="7584">' . $data['D7584'] . '</BilBruksomradeSpesifisertBil-datadef-7584>
		<BilParkeringAdresseSpesifisertBil-datadef-19411 orid="19411">' . $data['D19411'] . '</BilParkeringAdresseSpesifisertBil-datadef-19411>
		<BilParkeringAnnetStedSpesifisertBil-datadef-7667 orid="7667">' . $data['D7667'] . '</BilParkeringAnnetStedSpesifisertBil-datadef-7667>
		<BilKjorebokSpesifisertBil-datadef-3118 orid="3118">' . $data['D3118'] . '</BilKjorebokSpesifisertBil-datadef-3118>
	</InformasjonOmBilOgSpesifikasjonAvDriftskostnader-grp-2771>
	<HvemBrukerBilenUtenomArbeidstiden-grp-1139 gruppeid="1139">
		<BilBrukerNavnSpesifisertBil-datadef-7585 orid="7585">' . $data['D7585'] . '</BilBrukerNavnSpesifisertBil-datadef-7585>
		<BilBrukerFodselsnummerSpesifisertBil-datadef-7586 orid="7586">' . $data['D7586'] . '</BilBrukerFodselsnummerSpesifisertBil-datadef-7586>
		<BilBrukerAdresseSpesifisertBil-datadef-7587 orid="7587">' . $data['D7587'] . '</BilBrukerAdresseSpesifisertBil-datadef-7587>
		<BilBrukerPostnummerSpesifisertBil-datadef-7588 orid="7588">' . $data['D7588'] . '</BilBrukerPostnummerSpesifisertBil-datadef-7588>
		<BilBrukerPoststedSpesifisertBil-datadef-7589 orid="7589">' . $data['D7589'] . '</BilBrukerPoststedSpesifisertBil-datadef-7589>
	</HvemBrukerBilenUtenomArbeidstiden-grp-1139>
	<SpesifikasjonAvDriftskostnaderForPrivatBrukAvYrkesbil-grp-1150 gruppeid="1150">
		<KolonneI-grp-3822 gruppeid="3822">
			<DriftskostnaderDrivstoffSpesifisertBil-datadef-7596 orid="7596">' . $data['D7596'] . '</DriftskostnaderDrivstoffSpesifisertBil-datadef-7596>
			<DriftskostnaderVedlikeholdSpesifisertBil-datadef-11251 orid="11251">' . $data['D11251'] . '</DriftskostnaderVedlikeholdSpesifisertBil-datadef-11251>
			<DriftskostnaderForsikringAvgifterSpesifisertBil-datadef-7590 orid="7590">' . $data['D7590'] . '</DriftskostnaderForsikringAvgifterSpesifisertBil-datadef-7590>
			<DriftskostnaderLeasingleieSpesifisertBil-datadef-11252 orid="11252">' . $data['D11252'] . '</DriftskostnaderLeasingleieSpesifisertBil-datadef-11252>
			<BilkostnaderVerdiforringelseLinearSpesifisertBil-datadef-11249 orid="11249">' . $data['D11249'] . '</BilkostnaderVerdiforringelseLinearSpesifisertBil-datadef-11249>
			<BilkostnaderTilbakeforingsgrunnlagSamletKostnadSpesifisertBil-datadef-11250 orid="11250">' . $data['D11250'] . '</BilkostnaderTilbakeforingsgrunnlagSamletKostnadSpesifisertBil-datadef-11250>
		</KolonneI-grp-3822>
		<KolonneII-grp-3823 gruppeid="3823">
			<SaldoavskrivningSpesifisertBil-datadef-11253 orid="11253">' . $data['D11253'] . '</SaldoavskrivningSpesifisertBil-datadef-11253>
			<BilkostnaderPrivatTilbakefortSpesifisertBil-datadef-7591 orid="7591">' . $data['D7591'] . '</BilkostnaderPrivatTilbakefortSpesifisertBil-datadef-7591>
			<DriftskostnaderYrketSpesifisertBil-datadef-11254 orid="11254">' . $data['D11254'] . '</DriftskostnaderYrketSpesifisertBil-datadef-11254>
			<BilgodtgjorelseMottattSpesifisertBil-datadef-3115 orid="3115">' . $data['D3115'] . '</BilgodtgjorelseMottattSpesifisertBil-datadef-3115>
			<DriftskostnaderOverskuddSpesifisertBil-datadef-7594 orid="7594">' . $data['D7594'] . '</DriftskostnaderOverskuddSpesifisertBil-datadef-7594>
			<DriftskostnaderUnderskuddSpesifisertBil-datadef-7595 orid="7595">' . $data['D7595'] . '</DriftskostnaderUnderskuddSpesifisertBil-datadef-7595>
		</KolonneII-grp-3823>
	</SpesifikasjonAvDriftskostnaderForPrivatBrukAvYrkesbil-grp-1150>
</InformasjonOmBil-grp-3497>
';
}
?>