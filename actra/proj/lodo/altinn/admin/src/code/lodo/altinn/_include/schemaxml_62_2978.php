<?php
// Filnavn: schemaxml_62_2978.php
// Skjema: RF-1219    Gevinst- og tapskonto
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-2857 gruppeid="2857">
	<Avgiver-grp-1575 gruppeid="1575">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetKommunenummer-datadef-17 orid="17">' . $data['D17'] . '</EnhetKommunenummer-datadef-17>
		<EnhetKommuneNavn-datadef-15506 orid="15506">' . $data['D15506'] . '</EnhetKommuneNavn-datadef-15506>
	</Avgiver-grp-1575>
	<Regnskapsforer-grp-1579 gruppeid="1579">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-1579>
	<GevinstTapskontoAntall-datadef-1740 orid="1740">' . $data['D1740'] . '</GevinstTapskontoAntall-datadef-1740>
</GenerellInformasjon-grp-2857>
';
}
else
{
$xml = '<GenerellInformasjon-grp-2857 gruppeid="2857">
	<Avgiver-grp-1575 gruppeid="1575">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetKommunenummer-datadef-17 orid="17">' . $data['D17'] . '</EnhetKommunenummer-datadef-17>
		<EnhetKommuneNavn-datadef-15506 orid="15506">' . $data['D15506'] . '</EnhetKommuneNavn-datadef-15506>
	</Avgiver-grp-1575>
	<Regnskapsforer-grp-1579 gruppeid="1579">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-1579>
	<GevinstTapskontoAntall-datadef-1740 orid="1740">' . $data['D1740'] . '</GevinstTapskontoAntall-datadef-1740>
</GenerellInformasjon-grp-2857>
<GevinstMvSomSkalTilleggesGevinstOgTapskontoForFjoraret-grp-1576 gruppeid="1576">
	<GevinstTapskontoSaldoInngaende-datadef-6896 orid="6896">' . $data['D6896'] . '</GevinstTapskontoSaldoInngaende-datadef-6896>
	<GevinstTapskontoDriftsmidlerIkkeAvskrivbareGevinst-datadef-1741 orid="1741">' . $data['D1741'] . '</GevinstTapskontoDriftsmidlerIkkeAvskrivbareGevinst-datadef-1741>
	<GevinstTapskontoDriftsmidlerRealisasjonGevinst-datadef-1742 orid="1742">' . $data['D1742'] . '</GevinstTapskontoDriftsmidlerRealisasjonGevinst-datadef-1742>
	<GevinstTapskontoForretningsverdiNegativSaldo-datadef-1743 orid="1743">' . $data['D1743'] . '</GevinstTapskontoForretningsverdiNegativSaldo-datadef-1743>
	<GevinstTapskontoBuskapRealisasjonGevinst-datadef-1744 orid="1744">' . $data['D1744'] . '</GevinstTapskontoBuskapRealisasjonGevinst-datadef-1744>
	<AvsetningerAndreBetingetSkattefrie-datadef-14844 orid="14844">' . $data['D14844'] . '</AvsetningerAndreBetingetSkattefrie-datadef-14844>
	<GevinstTapskontoGevinster-datadef-6899 orid="6899">' . $data['D6899'] . '</GevinstTapskontoGevinster-datadef-6899>
	<GevinstTapskontoSaldoForTap-datadef-6900 orid="6900">' . $data['D6900'] . '</GevinstTapskontoSaldoForTap-datadef-6900>
</GevinstMvSomSkalTilleggesGevinstOgTapskontoForFjoraret-grp-1576>
<TapMvSomSkalFradrasPaGevinstOgTapskontoForFjoraret-grp-1578 gruppeid="1578">
	<GevinstTapskontoDriftsmidlerIkkeAvskrivbareTap-datadef-1745 orid="1745">' . $data['D1745'] . '</GevinstTapskontoDriftsmidlerIkkeAvskrivbareTap-datadef-1745>
	<GevinstTapskontoDriftsmidlerRealisasjonTap-datadef-6901 orid="6901">' . $data['D6901'] . '</GevinstTapskontoDriftsmidlerRealisasjonTap-datadef-6901>
	<GevinstTapskontoRealisertEiendelVerdiDifferanse-datadef-1746 orid="1746">' . $data['D1746'] . '</GevinstTapskontoRealisertEiendelVerdiDifferanse-datadef-1746>
	<GevinstTapskontoDeltakerlignetSelskapRealisertEiendelAndel-datadef-1747 orid="1747">' . $data['D1747'] . '</GevinstTapskontoDeltakerlignetSelskapRealisertEiendelAndel-datadef-1747>
	<GevinstTapskontoTap-datadef-6902 orid="6902">' . $data['D6902'] . '</GevinstTapskontoTap-datadef-6902>
</TapMvSomSkalFradrasPaGevinstOgTapskontoForFjoraret-grp-1578>
<GrunnlagOgSaldo-grp-2862 gruppeid="2862">
	<GevinstTapskontoInntektFradragGrunnlag-datadef-6903 orid="6903">' . $data['D6903'] . '</GevinstTapskontoInntektFradragGrunnlag-datadef-6903>
	<GevinstTapskontoInntektArets-datadef-6904 orid="6904">' . $data['D6904'] . '</GevinstTapskontoInntektArets-datadef-6904>
	<GevinstTapskontoFradragArets-datadef-21982 orid="21982">' . $data['D21982'] . '</GevinstTapskontoFradragArets-datadef-21982>
	<GevinstTapskontoNaringSaldo-datadef-7963 orid="7963">' . $data['D7963'] . '</GevinstTapskontoNaringSaldo-datadef-7963>
</GrunnlagOgSaldo-grp-2862>
';
}
?>