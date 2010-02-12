<?php
// Filnavn: schemaxml_754_3102.php
// Skjema: RF-1052    Avstemming av egenkapitalen mv.
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-2134 gruppeid="2134">
	<Avgiver-grp-128 gruppeid="128">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-128>
	<Regnskapsforer-grp-130 gruppeid="130">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-130>
</GenerellInformasjon-grp-2134>
';
}
else
{
$xml = '<GenerellInformasjon-grp-2134 gruppeid="2134">
	<Avgiver-grp-128 gruppeid="128">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-128>
	<Regnskapsforer-grp-130 gruppeid="130">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-130>
</GenerellInformasjon-grp-2134>
<AvstemmingAvEgenkapital-grp-131 gruppeid="131">
	<EgenkapitalASMvFjoraret-datadef-17491 orid="17491">' . $data['D17491'] . '</EgenkapitalASMvFjoraret-datadef-17491>
	<ArsresultatASMv-datadef-17492 orid="17492">' . $data['D17492'] . '</ArsresultatASMv-datadef-17492>
	<UtbytteASMvAvsatt-datadef-17493 orid="17493">' . $data['D17493'] . '</UtbytteASMvAvsatt-datadef-17493>
	<KonsernbidragASMvMottatt-datadef-17494 orid="17494">' . $data['D17494'] . '</KonsernbidragASMvMottatt-datadef-17494>
	<KonsernbidragASMvAvgitt-datadef-17495 orid="17495">' . $data['D17495'] . '</KonsernbidragASMvAvgitt-datadef-17495>
	<Kontantinnskudd-datadef-11307 orid="11307">' . $data['D11307'] . '</Kontantinnskudd-datadef-11307>
	<Tingsinnskudd-datadef-11308 orid="11308">' . $data['D11308'] . '</Tingsinnskudd-datadef-11308>
	<AksjekapitalNedsettelseKontanter-datadef-11309 orid="11309">' . $data['D11309'] . '</AksjekapitalNedsettelseKontanter-datadef-11309>
	<AksjekapitalNedsettelseEiendelerAndre-datadef-11310 orid="11310">' . $data['D11310'] . '</AksjekapitalNedsettelseEiendelerAndre-datadef-11310>
	<AksjonarbidragASMv-datadef-17496 orid="17496">' . $data['D17496'] . '</AksjonarbidragASMv-datadef-17496>
	<GjeldEttergivelse-datadef-11311 orid="11311">' . $data['D11311'] . '</GjeldEttergivelse-datadef-11311>
	<AksjerEgneKjop-datadef-11312 orid="11312">' . $data['D11312'] . '</AksjerEgneKjop-datadef-11312>
	<AksjerEgneSalg-datadef-11313 orid="11313">' . $data['D11313'] . '</AksjerEgneSalg-datadef-11313>
	<PrinsippendringerEgenkapitalen-datadef-8681 orid="8681">' . $data['D8681'] . '</PrinsippendringerEgenkapitalen-datadef-8681>
	<AksjekapitalGjeldKonvertert-datadef-17422 orid="17422">' . $data['D17422'] . '</AksjekapitalGjeldKonvertert-datadef-17422>
	<UtbytteAvsattUtdeltDifferanse-datadef-19807 orid="19807">' . $data['D19807'] . '</UtbytteAvsattUtdeltDifferanse-datadef-19807>
	<EgenkapitalEndringerAndre-datadef-11314 orid="11314">' . $data['D11314'] . '</EgenkapitalEndringerAndre-datadef-11314>
	<FondVerdiendringerAretsEndring-datadef-22248 orid="22248">' . $data['D22248'] . '</FondVerdiendringerAretsEndring-datadef-22248>
	<EgenkapitalAvstemt-datadef-13548 orid="13548">' . $data['D13548'] . '</EgenkapitalAvstemt-datadef-13548>
</AvstemmingAvEgenkapital-grp-131>
<AndreOpplysninger-grp-134 gruppeid="134">
	<AksjerMvAntall-datadef-1125 orid="1125">' . $data['D1125'] . '</AksjerMvAntall-datadef-1125>
	<AksjerMvPalydende-datadef-11315 orid="11315">' . $data['D11315'] . '</AksjerMvPalydende-datadef-11315>
	<AksjerAntallOmsatt-datadef-11316 orid="11316">' . $data['D11316'] . '</AksjerAntallOmsatt-datadef-11316>
	<AksjerFordeltSplitt-datadef-11317 orid="11317">' . $data['D11317'] . '</AksjerFordeltSplitt-datadef-11317>
	<AksjerFordeltSpleis-datadef-11318 orid="11318">' . $data['D11318'] . '</AksjerFordeltSpleis-datadef-11318>
	<InnbetaltAksjekapital-grp-790 gruppeid="790">
		<AksjekapitalInnbetaltFjoraret-datadef-19980 orid="19980">' . $data['D19980'] . '</AksjekapitalInnbetaltFjoraret-datadef-19980>
		<AksjekapitalInnbetaltOkning-datadef-11319 orid="11319">' . $data['D11319'] . '</AksjekapitalInnbetaltOkning-datadef-11319>
		<AksjekapitalInnbetaltNedgang-datadef-11320 orid="11320">' . $data['D11320'] . '</AksjekapitalInnbetaltNedgang-datadef-11320>
		<AksjekapitalInnbetaltOverkurs-datadef-19979 orid="19979">' . $data['D19979'] . '</AksjekapitalInnbetaltOverkurs-datadef-19979>
	</InnbetaltAksjekapital-grp-790>
</AndreOpplysninger-grp-134>
<EnkeltmannsforetakOgDeltakerliknendeSelskap-grp-135 gruppeid="135">
	<EgenkapitalEnkeltmannsforetakFjoraret-datadef-14125 orid="14125">' . $data['D14125'] . '</EgenkapitalEnkeltmannsforetakFjoraret-datadef-14125>
	<ArsresultatEnkeltmannsforetak-datadef-14126 orid="14126">' . $data['D14126'] . '</ArsresultatEnkeltmannsforetak-datadef-14126>
	<InnskuddKontanter-datadef-11321 orid="11321">' . $data['D11321'] . '</InnskuddKontanter-datadef-11321>
	<InnskuddEiendeler-datadef-11322 orid="11322">' . $data['D11322'] . '</InnskuddEiendeler-datadef-11322>
	<BilPrivatkjoring-datadef-7592 orid="7592">' . $data['D7592'] . '</BilPrivatkjoring-datadef-7592>
	<PrinsippendringerEgenkapitalEnkeltmannsforetak-datadef-14167 orid="14167">' . $data['D14167'] . '</PrinsippendringerEgenkapitalEnkeltmannsforetak-datadef-14167>
	<EgenkapitalKorreksjoner-datadef-14112 orid="14112">' . $data['D14112'] . '</EgenkapitalKorreksjoner-datadef-14112>
	<EgenkapitalEnkeltmannsforetakAvstemt-datadef-14127 orid="14127">' . $data['D14127'] . '</EgenkapitalEnkeltmannsforetakAvstemt-datadef-14127>
</EnkeltmannsforetakOgDeltakerliknendeSelskap-grp-135>
<SpesifikasjonAvPrivatkontoForEnkeltmannsforetakOgDeltakerliknend-grp-136 gruppeid="136">
	<PrivatkontoKontantuttak-datadef-291 orid="291">' . $data['D291'] . '</PrivatkontoKontantuttak-datadef-291>
	<PrivatkontoUttakDriftsmidler-datadef-6935 orid="6935">' . $data['D6935'] . '</PrivatkontoUttakDriftsmidler-datadef-6935>
	<PrivatkontoUttakVarerTjenester-datadef-294 orid="294">' . $data['D294'] . '</PrivatkontoUttakVarerTjenester-datadef-294>
	<PrivatkontoNaringsbyggEgenBolig-datadef-295 orid="295">' . $data['D295'] . '</PrivatkontoNaringsbyggEgenBolig-datadef-295>
	<PrivatkontoLysVarme-datadef-296 orid="296">' . $data['D296'] . '</PrivatkontoLysVarme-datadef-296>
	<PrivatkontoTelefonkostnader-datadef-297 orid="297">' . $data['D297'] . '</PrivatkontoTelefonkostnader-datadef-297>
	<PrivatkontoDiverse-datadef-298 orid="298">' . $data['D298'] . '</PrivatkontoDiverse-datadef-298>
	<PrivatkontoSkatter-datadef-292 orid="292">' . $data['D292'] . '</PrivatkontoSkatter-datadef-292>
	<PrivatkontoNaringsbil-datadef-6937 orid="6937">' . $data['D6937'] . '</PrivatkontoNaringsbil-datadef-6937>
	<PrivatkontoSykeUlykkesforsikringPremie-datadef-11323 orid="11323">' . $data['D11323'] . '</PrivatkontoSykeUlykkesforsikringPremie-datadef-11323>
	<PrivatkontoSykepengerTilleggstrygd-datadef-6936 orid="6936">' . $data['D6936'] . '</PrivatkontoSykepengerTilleggstrygd-datadef-6936>
	<Privatkonto-datadef-7354 orid="7354">' . $data['D7354'] . '</Privatkonto-datadef-7354>
</SpesifikasjonAvPrivatkontoForEnkeltmannsforetakOgDeltakerliknend-grp-136>
';
}
?>