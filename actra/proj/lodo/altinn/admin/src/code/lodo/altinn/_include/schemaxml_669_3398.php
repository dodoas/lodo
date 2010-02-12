<?php
// Filnavn: schemaxml_669_3398.php
// Skjema: RF-1037    Terminoppgave for  arbeidsgiveravgift og forskuddstrekk.
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-986 gruppeid="986">
	<Periode-grp-57 gruppeid="57">
		<OppgaveTermin-datadef-11819 orid="11819">' . $data['D11819'] . '</OppgaveTermin-datadef-11819>
		<OppgaveAr-datadef-11236 orid="11236">' . $data['D11236'] . '</OppgaveAr-datadef-11236>
	</Periode-grp-57>
	<Skatteoppkrever-grp-989 gruppeid="989">
		<SkatteoppkreverKommuneNummer-datadef-16513 orid="16513">' . $data['D16513'] . '</SkatteoppkreverKommuneNummer-datadef-16513>
		<SkatteoppkreverKommuneNavn-datadef-8486 orid="8486">' . $data['D8486'] . '</SkatteoppkreverKommuneNavn-datadef-8486>
	</Skatteoppkrever-grp-989>
	<Innsender-grp-56 gruppeid="56">
		<EnhetBedriftOrganisasjonsnummer-datadef-21772 orid="21772">' . $data['D21772'] . '</EnhetBedriftOrganisasjonsnummer-datadef-21772>
		<EnhetBedriftNavn-datadef-21771 orid="21771">' . $data['D21771'] . '</EnhetBedriftNavn-datadef-21771>
		<EnhetBedriftAdresse-datadef-21773 orid="21773">' . $data['D21773'] . '</EnhetBedriftAdresse-datadef-21773>
		<EnhetBedriftPostnummer-datadef-21774 orid="21774">' . $data['D21774'] . '</EnhetBedriftPostnummer-datadef-21774>
		<EnhetBedriftPoststed-datadef-21775 orid="21775">' . $data['D21775'] . '</EnhetBedriftPoststed-datadef-21775>
		<EnhetNACEKode-datadef-5133 orid="5133">' . $data['D5133'] . '</EnhetNACEKode-datadef-5133>
	</Innsender-grp-56>
</GenerellInformasjon-grp-986>
';
}
else
{
$xml = '<GenerellInformasjon-grp-986 gruppeid="986">
	<Periode-grp-57 gruppeid="57">
		<OppgaveTermin-datadef-11819 orid="11819">' . $data['D11819'] . '</OppgaveTermin-datadef-11819>
		<OppgaveAr-datadef-11236 orid="11236">' . $data['D11236'] . '</OppgaveAr-datadef-11236>
	</Periode-grp-57>
	<Skatteoppkrever-grp-989 gruppeid="989">
		<SkatteoppkreverKommuneNummer-datadef-16513 orid="16513">' . $data['D16513'] . '</SkatteoppkreverKommuneNummer-datadef-16513>
		<SkatteoppkreverKommuneNavn-datadef-8486 orid="8486">' . $data['D8486'] . '</SkatteoppkreverKommuneNavn-datadef-8486>
	</Skatteoppkrever-grp-989>
	<Innsender-grp-56 gruppeid="56">
		<EnhetBedriftOrganisasjonsnummer-datadef-21772 orid="21772">' . $data['D21772'] . '</EnhetBedriftOrganisasjonsnummer-datadef-21772>
		<EnhetBedriftNavn-datadef-21771 orid="21771">' . $data['D21771'] . '</EnhetBedriftNavn-datadef-21771>
		<EnhetBedriftAdresse-datadef-21773 orid="21773">' . $data['D21773'] . '</EnhetBedriftAdresse-datadef-21773>
		<EnhetBedriftPostnummer-datadef-21774 orid="21774">' . $data['D21774'] . '</EnhetBedriftPostnummer-datadef-21774>
		<EnhetBedriftPoststed-datadef-21775 orid="21775">' . $data['D21775'] . '</EnhetBedriftPoststed-datadef-21775>
		<EnhetNACEKode-datadef-5133 orid="5133">' . $data['D5133'] . '</EnhetNACEKode-datadef-5133>
	</Innsender-grp-56>
</GenerellInformasjon-grp-986>
<ArbeidsgiveravgiftsgrunnlagForskuddstrekkOgBeregningsmate-grp-4953 gruppeid="4953">
	<Beregningsmate-grp-169 gruppeid="169">
		<ArbeidsgiveravgiftBeregningType-datadef-16522 orid="16522">' . $data['D16522'] . '</ArbeidsgiveravgiftBeregningType-datadef-16522>
		<ArbeidsgiveravgiftBunnfradrag-datadef-16517 orid="16517">' . $data['D16517'] . '</ArbeidsgiveravgiftBunnfradrag-datadef-16517>
	</Beregningsmate-grp-169>
	<ForskuddstrekkOgGrunnlagArbeidsgiveravgift-grp-67 gruppeid="67">
		<KommuneNummer-datadef-5950 orid="5950">' . $data['D5950'] . '</KommuneNummer-datadef-5950>
		<KommuneNavn-datadef-5932 orid="5932">' . $data['D5932'] . '</KommuneNavn-datadef-5932>
		<ArbeidsgiveravgiftSone-datadef-3545 orid="3545">' . $data['D3545'] . '</ArbeidsgiveravgiftSone-datadef-3545>
		<ArbeidsgiveravgiftUnder62ArGrunnlagKommune-datadef-6047 orid="6047">' . $data['D6047'] . '</ArbeidsgiveravgiftUnder62ArGrunnlagKommune-datadef-6047>
		<ArbeidsgiveravgiftOver62ArGrunnlagKommune-datadef-16509 orid="16509">' . $data['D16509'] . '</ArbeidsgiveravgiftOver62ArGrunnlagKommune-datadef-16509>
		<ForskuddstrekkKommune-datadef-6046 orid="6046">' . $data['D6046'] . '</ForskuddstrekkKommune-datadef-6046>
	</ForskuddstrekkOgGrunnlagArbeidsgiveravgift-grp-67>
</ArbeidsgiveravgiftsgrunnlagForskuddstrekkOgBeregningsmate-grp-4953>
<Utlandet-grp-987 gruppeid="987">
	<UTL1-grp-69 gruppeid="69">
		<ArbeidsgiveravgiftUtenlandskGrunnlag-datadef-16518 orid="16518">' . $data['D16518'] . '</ArbeidsgiveravgiftUtenlandskGrunnlag-datadef-16518>
		<ArbeidsgiveravgiftUtenlandskArbeidstakerBergenet-datadef-6049 orid="6049">' . $data['D6049'] . '</ArbeidsgiveravgiftUtenlandskArbeidstakerBergenet-datadef-6049>
	</UTL1-grp-69>
	<UTL2-grp-71 gruppeid="71">
		<AnsattUtenlandskManeder-datadef-16519 orid="16519">' . $data['D16519'] . '</AnsattUtenlandskManeder-datadef-16519>
		<ArbeidsgiveravgiftUtenlandskManedBeregnet-datadef-16520 orid="16520">' . $data['D16520'] . '</ArbeidsgiveravgiftUtenlandskManedBeregnet-datadef-16520>
	</UTL2-grp-71>
	<Ekstra-grp-73 gruppeid="73">
		<ArbeidsgiveravgiftEkstraGrunnlag-datadef-16521 orid="16521">' . $data['D16521'] . '</ArbeidsgiveravgiftEkstraGrunnlag-datadef-16521>
		<ArbeidsgiveravgiftEkstraBeregnet-datadef-6050 orid="6050">' . $data['D6050'] . '</ArbeidsgiveravgiftEkstraBeregnet-datadef-6050>
	</Ekstra-grp-73>
</Utlandet-grp-987>
<Resultater-grp-74 gruppeid="74">
	<Kontrollsummer-grp-4909 gruppeid="4909">
		<ArbeidsgiveravgiftUnder62ArGrunnlag-datadef-6051 orid="6051">' . $data['D6051'] . '</ArbeidsgiveravgiftUnder62ArGrunnlag-datadef-6051>
		<ArbeidsgiveravgiftOver62ArGrunnlag-datadef-16510 orid="16510">' . $data['D16510'] . '</ArbeidsgiveravgiftOver62ArGrunnlag-datadef-16510>
		<ArbeidsgiveravgiftRestFribelop-datadef-21169 orid="21169">' . $data['D21169'] . '</ArbeidsgiveravgiftRestFribelop-datadef-21169>
	</Kontrollsummer-grp-4909>
	<Arbeidsgiveravgift-grp-4910 gruppeid="4910">
		<ArbeidsgiveravgiftSkyldig-datadef-223 orid="223">' . $data['D223'] . '</ArbeidsgiveravgiftSkyldig-datadef-223>
		<KIDnummerArbeidsgiveravgift-datadef-16512 orid="16512">' . $data['D16512'] . '</KIDnummerArbeidsgiveravgift-datadef-16512>
	</Arbeidsgiveravgift-grp-4910>
	<Forskuddstrekk-grp-4911 gruppeid="4911">
		<Forskuddstrekk-datadef-2903 orid="2903">' . $data['D2903'] . '</Forskuddstrekk-datadef-2903>
		<KIDnummerForskuddstrekk-datadef-16511 orid="16511">' . $data['D16511'] . '</KIDnummerForskuddstrekk-datadef-16511>
	</Forskuddstrekk-grp-4911>
</Resultater-grp-74>
';
}
?>