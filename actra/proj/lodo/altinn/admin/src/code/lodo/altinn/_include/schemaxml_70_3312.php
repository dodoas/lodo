<?php
// Filnavn: schemaxml_70_3312.php
// Skjema: RF-1061    Oppgave over realisasjon av aksjer mv
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-827 gruppeid="827">
	<Avgiver-grp-100 gruppeid="100">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-100>
</GenerellInformasjon-grp-827>
';
}
else
{
$xml = '<GenerellInformasjon-grp-827 gruppeid="827">
	<Avgiver-grp-100 gruppeid="100">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-100>
</GenerellInformasjon-grp-827>
<RealisasjonAvAksjer-grp-3721 gruppeid="3721">
	<RealisasjonAvEnAksje-grp-4413 gruppeid="4413">
		<Aksjeinformasjon-grp-4175 gruppeid="4175">
			<EnhetNavnSpesifisertSelskap-datadef-7617 orid="7617">' . $data['D7617'] . '</EnhetNavnSpesifisertSelskap-datadef-7617>
			<EnhetOrganisasjonsnummerSpesifisertSelskap-datadef-7618 orid="7618">' . $data['D7618'] . '</EnhetOrganisasjonsnummerSpesifisertSelskap-datadef-7618>
			<AksjerMvTypeSpesifisertSelskap-datadef-19400 orid="19400">' . $data['D19400'] . '</AksjerMvTypeSpesifisertSelskap-datadef-19400>
			<AksjeBorsnotertIkkeBorsnotertSpesifisertSelskap-datadef-19401 orid="19401">' . $data['D19401'] . '</AksjeBorsnotertIkkeBorsnotertSpesifisertSelskap-datadef-19401>
			<AksjerMvErvervsdatoSpesifisertSelskap-datadef-1181 orid="1181">' . $data['D1181'] . '</AksjerMvErvervsdatoSpesifisertSelskap-datadef-1181>
			<AksjerMvErvervsmateSpesifisertSelskap-datadef-1827 orid="1827">' . $data['D1827'] . '</AksjerMvErvervsmateSpesifisertSelskap-datadef-1827>
			<AksjerMvRealisasjonDato-datadef-1821 orid="1821">' . $data['D1821'] . '</AksjerMvRealisasjonDato-datadef-1821>
			<AksjerMvPalydendeSpesifisertSelskap-datadef-7620 orid="7620">' . $data['D7620'] . '</AksjerMvPalydendeSpesifisertSelskap-datadef-7620>
			<AksjerMvRealisasjonAntallSpesifisertSelskap-datadef-7621 orid="7621">' . $data['D7621'] . '</AksjerMvRealisasjonAntallSpesifisertSelskap-datadef-7621>
		</Aksjeinformasjon-grp-4175>
		<BeregningAvGevinstTap-grp-828 gruppeid="828">
			<AksjerMvRealisasjonsvederlagSpesifisertSelskap-datadef-5827 orid="5827">' . $data['D5827'] . '</AksjerMvRealisasjonsvederlagSpesifisertSelskap-datadef-5827>
			<AksjerMvInngangsverdiSpesifisertSelskap-datadef-1822 orid="1822">' . $data['D1822'] . '</AksjerMvInngangsverdiSpesifisertSelskap-datadef-1822>
			<AksjerMvInngangsverdiTypeSpesifisertSelskap-datadef-1823 orid="1823">' . $data['D1823'] . '</AksjerMvInngangsverdiTypeSpesifisertSelskap-datadef-1823>
			<AksjerMvInngangsverdiJusteringsfaktorSpesifisertSelskap-datadef-1828 orid="1828">' . $data['D1828'] . '</AksjerMvInngangsverdiJusteringsfaktorSpesifisertSelskap-datadef-1828>
			<RISKBelopEiertidOppsummertPositivSpesifisertSelskap-datadef-7623 orid="7623">' . $data['D7623'] . '</RISKBelopEiertidOppsummertPositivSpesifisertSelskap-datadef-7623>
			<RISKBelopEiertidOppsummertNegativSpesifisertSelskap-datadef-11279 orid="11279">' . $data['D11279'] . '</RISKBelopEiertidOppsummertNegativSpesifisertSelskap-datadef-11279>
			<RealisasjonsRISKJusteringsfaktorSpesifisertSelskap-datadef-1829 orid="1829">' . $data['D1829'] . '</RealisasjonsRISKJusteringsfaktorSpesifisertSelskap-datadef-1829>
			<AksjerMvInngangsverdiKorrigeringFradragSpesifisertSelskap-datadef-1830 orid="1830">' . $data['D1830'] . '</AksjerMvInngangsverdiKorrigeringFradragSpesifisertSelskap-datadef-1830>
			<AksjerMvInngangsverdiKorrigeringIkkeFradragSpesifisertSelsk-datadef-11280 orid="11280">' . $data['D11280'] . '</AksjerMvInngangsverdiKorrigeringIkkeFradragSpesifisertSelsk-datadef-11280>
			<AksjekapitalTilbakebetaltSpesifisertSelskap-datadef-1824 orid="1824">' . $data['D1824'] . '</AksjekapitalTilbakebetaltSpesifisertSelskap-datadef-1824>
			<AksjerMvInngangsverdiNegativSaldoSpesifisertSelskap-datadef-7612 orid="7612">' . $data['D7612'] . '</AksjerMvInngangsverdiNegativSaldoSpesifisertSelskap-datadef-7612>
			<AksjerMvInngangsverdiKorrigertSpesifisertSelskap-datadef-1825 orid="1825">' . $data['D1825'] . '</AksjerMvInngangsverdiKorrigertSpesifisertSelskap-datadef-1825>
			<AksjerMvInngangsverdiKorrigertPositivSpesifisertSelskap-datadef-11281 orid="11281">' . $data['D11281'] . '</AksjerMvInngangsverdiKorrigertPositivSpesifisertSelskap-datadef-11281>
			<AksjerMvInngangsverdiKorrigertNegativSpesifisertSelskap-datadef-11282 orid="11282">' . $data['D11282'] . '</AksjerMvInngangsverdiKorrigertNegativSpesifisertSelskap-datadef-11282>
			<AksjerMvRealisasjonGevinstTapSpesifisertSelskap-datadef-14083 orid="14083">' . $data['D14083'] . '</AksjerMvRealisasjonGevinstTapSpesifisertSelskap-datadef-14083>
		</BeregningAvGevinstTap-grp-828>
		<BeregningAvMaksimaltTapsfradrag-grp-4166 gruppeid="4166">
			<SkattemessigFormuesverdiOgHistoriskKostpris-grp-4167 gruppeid="4167">
				<AksjerMvSkattemessigVerdiSpesifisertSelskap-datadef-11731 orid="11731">' . $data['D11731'] . '</AksjerMvSkattemessigVerdiSpesifisertSelskap-datadef-11731>
				<AksjerMvKostprisHistoriskSpesifisertSelskap-datadef-1178 orid="1178">' . $data['D1178'] . '</AksjerMvKostprisHistoriskSpesifisertSelskap-datadef-1178>
			</SkattemessigFormuesverdiOgHistoriskKostpris-grp-4167>
			<BeregningAvMaksimaltTapsfradrag-grp-4168 gruppeid="4168">
				<AksjerMvTapSpesifisertSelskap-datadef-7614 orid="7614">' . $data['D7614'] . '</AksjerMvTapSpesifisertSelskap-datadef-7614>
				<AksjerMvOppregulertInngangsverdiSpesifisertSelskap-datadef-22206 orid="22206">' . $data['D22206'] . '</AksjerMvOppregulertInngangsverdiSpesifisertSelskap-datadef-22206>
				<AksjerMvFormuesverdiKostprisSpesifisertSelskap-datadef-14122 orid="14122">' . $data['D14122'] . '</AksjerMvFormuesverdiKostprisSpesifisertSelskap-datadef-14122>
				<AksjerMvVerdiBeregnetSpesifisertSelskap-datadef-7672 orid="7672">' . $data['D7672'] . '</AksjerMvVerdiBeregnetSpesifisertSelskap-datadef-7672>
				<AksjerMvTapSkattSpesifisertSelskap-datadef-7673 orid="7673">' . $data['D7673'] . '</AksjerMvTapSkattSpesifisertSelskap-datadef-7673>
			</BeregningAvMaksimaltTapsfradrag-grp-4168>
		</BeregningAvMaksimaltTapsfradrag-grp-4166>
	</RealisasjonAvEnAksje-grp-4413>
</RealisasjonAvAksjer-grp-3721>
<Samleoppgave-grp-831 gruppeid="831">
	<AksjerMvGevinstSkattBrutto-datadef-7615 orid="7615">' . $data['D7615'] . '</AksjerMvGevinstSkattBrutto-datadef-7615>
	<AksjerMvTapSkattBrutto-datadef-7616 orid="7616">' . $data['D7616'] . '</AksjerMvTapSkattBrutto-datadef-7616>
	<AksjerMvOmkostninger-datadef-1826 orid="1826">' . $data['D1826'] . '</AksjerMvOmkostninger-datadef-1826>
	<AksjerMvRealisasjonGevinstTapSkatt-datadef-14084 orid="14084">' . $data['D14084'] . '</AksjerMvRealisasjonGevinstTapSkatt-datadef-14084>
</Samleoppgave-grp-831>
';
}
?>