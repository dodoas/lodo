<?php
// Filnavn: schemaxml_769_3378.php
// Skjema: RF-1022    Kontrolloppstilling over registrerte og innberettede beløp
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-849 gruppeid="849">
	<Avgiver-grp-91 gruppeid="91">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-91>
	<Regnskapsforer-grp-97 gruppeid="97">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-97>
</GenerellInformasjon-grp-849>
';
}
else
{
$xml = '<GenerellInformasjon-grp-849 gruppeid="849">
	<Avgiver-grp-91 gruppeid="91">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-91>
	<Regnskapsforer-grp-97 gruppeid="97">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-97>
</GenerellInformasjon-grp-849>
<KontrolloppstillingOverRegistrerteOgInnberettedeBelop-grp-98 gruppeid="98">
	<Spesifikasjoner-grp-779 gruppeid="779">
		<LonnskostnaderKontonummerSpesifisertKonti-datadef-8499 orid="8499">' . $data['D8499'] . '</LonnskostnaderKontonummerSpesifisertKonti-datadef-8499>
		<LonnskostnaderKontonavnSpesifisertKonti-datadef-8509 orid="8509">' . $data['D8509'] . '</LonnskostnaderKontonavnSpesifisertKonti-datadef-8509>
		<LonnskostnaderOppgavepliktigeSpesifisertKonti-datadef-8500 orid="8500">' . $data['D8500'] . '</LonnskostnaderOppgavepliktigeSpesifisertKonti-datadef-8500>
		<GodtgjorelserTidligereArSpesifisertKonto-datadef-11293 orid="11293">' . $data['D11293'] . '</GodtgjorelserTidligereArSpesifisertKonto-datadef-11293>
		<FradragIkkeForfalteLonningerPaloptSpesifisertKonto-datadef-11294 orid="11294">' . $data['D11294'] . '</FradragIkkeForfalteLonningerPaloptSpesifisertKonto-datadef-11294>
		<LonnskostnaderOppgavepliktigeSpesifisertKonto-datadef-11295 orid="11295">' . $data['D11295'] . '</LonnskostnaderOppgavepliktigeSpesifisertKonto-datadef-11295>
		<LonnskostnaderArbeidsgiveravgiftspliktigeSpesifisertKonto-datadef-11296 orid="11296">' . $data['D11296'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSpesifisertKonto-datadef-11296>
	</Spesifikasjoner-grp-779>
	<Sum-grp-3001 gruppeid="3001">
		<LonnskostnaderOppgavepliktige-datadef-8501 orid="8501">' . $data['D8501'] . '</LonnskostnaderOppgavepliktige-datadef-8501>
		<GodtgjorelserTidligereAr-datadef-6630 orid="6630">' . $data['D6630'] . '</GodtgjorelserTidligereAr-datadef-6630>
		<FradragIkkeForfalteLonningerPalopt-datadef-6633 orid="6633">' . $data['D6633'] . '</FradragIkkeForfalteLonningerPalopt-datadef-6633>
		<LonnskostnaderOppgavepliktigeSamlede-datadef-14876 orid="14876">' . $data['D14876'] . '</LonnskostnaderOppgavepliktigeSamlede-datadef-14876>
		<YtelserArbeidsgiveravgiftspliktige-datadef-14888 orid="14888">' . $data['D14888'] . '</YtelserArbeidsgiveravgiftspliktige-datadef-14888>
	</Sum-grp-3001>
	<SamletKreditertBelop-grp-3055 gruppeid="3055">
		<AnsattNaturalytelserKontonummer-datadef-13878 orid="13878">' . $data['D13878'] . '</AnsattNaturalytelserKontonummer-datadef-13878>
		<AnsattNaturalytelser-datadef-6627 orid="6627">' . $data['D6627'] . '</AnsattNaturalytelser-datadef-6627>
	</SamletKreditertBelop-grp-3055>
	<OffentligTilskuddVedrorendeArbeidskraft-grp-3056 gruppeid="3056">
		<TilskuddOffentligArbeidskraftKontonummer-datadef-13879 orid="13879">' . $data['D13879'] . '</TilskuddOffentligArbeidskraftKontonummer-datadef-13879>
		<TilskuddOffentligArbeidskraft-datadef-7711 orid="7711">' . $data['D7711'] . '</TilskuddOffentligArbeidskraft-datadef-7711>
	</OffentligTilskuddVedrorendeArbeidskraft-grp-3056>
	<OffentligRefusjonVedrorendeArbeidskraft-grp-3057 gruppeid="3057">
		<RefusjonOffentligArbeidskraftKontonummer-datadef-13880 orid="13880">' . $data['D13880'] . '</RefusjonOffentligArbeidskraftKontonummer-datadef-13880>
		<RefusjonOffentligArbeidskraft-datadef-7712 orid="7712">' . $data['D7712'] . '</RefusjonOffentligArbeidskraft-datadef-7712>
	</OffentligRefusjonVedrorendeArbeidskraft-grp-3057>
	<AretsInnbetalingAvArbeidsgiveravgiftOl-grp-3058 gruppeid="3058">
		<PensjonsordningerTilskuddPremieAvgiftspliktigeKontonummer-datadef-13881 orid="13881">' . $data['D13881'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeKontonummer-datadef-13881>
		<PensjonsordningerTilskuddPremieAvgiftspliktige-datadef-11297 orid="11297">' . $data['D11297'] . '</PensjonsordningerTilskuddPremieAvgiftspliktige-datadef-11297>
	</AretsInnbetalingAvArbeidsgiveravgiftOl-grp-3058>
	<BeregnetPersoninntekt-grp-3059 gruppeid="3059">
		<PersoninntektAktiveAkjonarerBeregnet-datadef-6632 orid="6632">' . $data['D6632'] . '</PersoninntektAktiveAkjonarerBeregnet-datadef-6632>
	</BeregnetPersoninntekt-grp-3059>
	<Sum-grp-3060 gruppeid="3060">
		<LonnskostnaderLonnsoppgavepliktigeOppgavepliktige-datadef-11291 orid="11291">' . $data['D11291'] . '</LonnskostnaderLonnsoppgavepliktigeOppgavepliktige-datadef-11291>
		<LonnskostnaderLonnsoppgavepliktigeArbeidsgiveravgiftspliktige-datadef-11292 orid="11292">' . $data['D11292'] . '</LonnskostnaderLonnsoppgavepliktigeArbeidsgiveravgiftspliktige-datadef-11292>
	</Sum-grp-3060>
</KontrolloppstillingOverRegistrerteOgInnberettedeBelop-grp-98>
';
}
?>