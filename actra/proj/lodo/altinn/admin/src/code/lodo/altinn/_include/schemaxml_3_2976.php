<?php
// Filnavn: schemaxml_3_2976.php
// Skjema: RF-1084    Avskrivningsskjema for saldo- og lineære avskrivinger
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1054 gruppeid="1054">
	<Avgiver-grp-48 gruppeid="48">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetAvskrivningKommunenummer-datadef-19895 orid="19895">' . $data['D19895'] . '</EnhetAvskrivningKommunenummer-datadef-19895>
		<EnhetAvskrivningKommunenavn-datadef-19896 orid="19896">' . $data['D19896'] . '</EnhetAvskrivningKommunenavn-datadef-19896>
	</Avgiver-grp-48>
	<Regnskapsforer-grp-51 gruppeid="51">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-51>
</GenerellInformasjon-grp-1054>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1054 gruppeid="1054">
	<Avgiver-grp-48 gruppeid="48">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetAvskrivningKommunenummer-datadef-19895 orid="19895">' . $data['D19895'] . '</EnhetAvskrivningKommunenummer-datadef-19895>
		<EnhetAvskrivningKommunenavn-datadef-19896 orid="19896">' . $data['D19896'] . '</EnhetAvskrivningKommunenavn-datadef-19896>
	</Avgiver-grp-48>
	<Regnskapsforer-grp-51 gruppeid="51">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-51>
</GenerellInformasjon-grp-1054>
<Saldoavskrivninger-grp-59 gruppeid="59">
	<SaldoavskrivningSameie-datadef-19413 orid="19413">' . $data['D19413'] . '</SaldoavskrivningSameie-datadef-19413>
	<Saldoavskrivninger-grp-2366 gruppeid="2366">
		<SaldoGruppeSpesifisert-datadef-352 orid="352">' . $data['D352'] . '</SaldoGruppeSpesifisert-datadef-352>
		<SaldoNummerSpesifisertSaldogruppe-datadef-7665 orid="7665">' . $data['D7665'] . '</SaldoNummerSpesifisertSaldogruppe-datadef-7665>
		<EnhetPrimarnaringSpesifisertSaldo-datadef-17149 orid="17149">' . $data['D17149'] . '</EnhetPrimarnaringSpesifisertSaldo-datadef-17149>
		<SaldoavskrivningAvskrivningssats-datadef-19678 orid="19678">' . $data['D19678'] . '</SaldoavskrivningAvskrivningssats-datadef-19678>
		<SaldoGrunnlagInngaendeSpesifisertSaldogruppe-datadef-6916 orid="6916">' . $data['D6916'] . '</SaldoGrunnlagInngaendeSpesifisertSaldogruppe-datadef-6916>
		<DriftsmidlerNedskrevetVerdiSpesifisertSaldogruppe-datadef-370 orid="370">' . $data['D370'] . '</DriftsmidlerNedskrevetVerdiSpesifisertSaldogruppe-datadef-370>
		<DriftsmidlerNyanskaffelserKostprisSpesifisertSaldogruppe-datadef-6913 orid="6913">' . $data['D6913'] . '</DriftsmidlerNyanskaffelserKostprisSpesifisertSaldogruppe-datadef-6913>
		<DriftsmidlerPakostningerSpesifisertSaldogruppe-datadef-6911 orid="6911">' . $data['D6911'] . '</DriftsmidlerPakostningerSpesifisertSaldogruppe-datadef-6911>
		<DriftsmidlerSalgsgevinstNedskrivingSpesifisertSaldogruppe-datadef-356 orid="356">' . $data['D356'] . '</DriftsmidlerSalgsgevinstNedskrivingSpesifisertSaldogruppe-datadef-356>
		<TilskuddOffentligeSpesifisertSaldogruppe-datadef-6912 orid="6912">' . $data['D6912'] . '</TilskuddOffentligeSpesifisertSaldogruppe-datadef-6912>
		<DriftsmidlerSaldogrunnlagForRealisasjonSpesifisertSaldogruppe-datadef-6907 orid="6907">' . $data['D6907'] . '</DriftsmidlerSaldogrunnlagForRealisasjonSpesifisertSaldogruppe-datadef-6907>
		<DriftsmidlerUttakVederlagSpesifisertSaldogruppe-datadef-6914 orid="6914">' . $data['D6914'] . '</DriftsmidlerUttakVederlagSpesifisertSaldogruppe-datadef-6914>
		<DriftsmidlerVederlagInntektsfortSpesifisertSaldogruppe-datadef-6908 orid="6908">' . $data['D6908'] . '</DriftsmidlerVederlagInntektsfortSpesifisertSaldogruppe-datadef-6908>
		<DriftsmidlerSaldoavskrivningGrunnlagSpesifisertSaldogruppe-datadef-362 orid="362">' . $data['D362'] . '</DriftsmidlerSaldoavskrivningGrunnlagSpesifisertSaldogruppe-datadef-362>
		<DriftsmidlerGevinstTapskontoOverfortSpesifisertSaldogruppe-datadef-6915 orid="6915">' . $data['D6915'] . '</DriftsmidlerGevinstTapskontoOverfortSpesifisertSaldogruppe-datadef-6915>
		<DriftsmidlerSaldoavskrivningSpesifisertSaldogruppe-datadef-6695 orid="6695">' . $data['D6695'] . '</DriftsmidlerSaldoavskrivningSpesifisertSaldogruppe-datadef-6695>
		<SaldoGrunnlagSpesifisertSaldogruppe-datadef-365 orid="365">' . $data['D365'] . '</SaldoGrunnlagSpesifisertSaldogruppe-datadef-365>
		<ForretningsbyggAnskaffetFor-grp-4043 gruppeid="4043">
			<ForretningsbyggGardsnummerSpesifisertBygg-datadef-11733 orid="11733">' . $data['D11733'] . '</ForretningsbyggGardsnummerSpesifisertBygg-datadef-11733>
			<ForretningsbyggBruksnummerSpesifisertBygg-datadef-11734 orid="11734">' . $data['D11734'] . '</ForretningsbyggBruksnummerSpesifisertBygg-datadef-11734>
			<ForretningsbyggSeksjonsnummerSpesifisertBygg-datadef-11735 orid="11735">' . $data['D11735'] . '</ForretningsbyggSeksjonsnummerSpesifisertBygg-datadef-11735>
			<ForretningsbyggAdresseSpesifisertBygg-datadef-11736 orid="11736">' . $data['D11736'] . '</ForretningsbyggAdresseSpesifisertBygg-datadef-11736>
		</ForretningsbyggAnskaffetFor-grp-4043>
		<ForretningsbyggKostprisSpesifisertBygg-datadef-367 orid="367">' . $data['D367'] . '</ForretningsbyggKostprisSpesifisertBygg-datadef-367>
		<ForretningsbyggNedskrevetVerdiSpesifisertBygg-datadef-368 orid="368">' . $data['D368'] . '</ForretningsbyggNedskrevetVerdiSpesifisertBygg-datadef-368>
		<ForretningsbyggAvskrivningNedreGrenseSpesifisertBygg-datadef-369 orid="369">' . $data['D369'] . '</ForretningsbyggAvskrivningNedreGrenseSpesifisertBygg-datadef-369>
	</Saldoavskrivninger-grp-2366>
</Saldoavskrivninger-grp-59>
<LineareAvskrivninger-grp-3505 gruppeid="3505">
	<LineareAvskrivninger-grp-88 gruppeid="88">
		<ObjektNummer-datadef-7686 orid="7686">' . $data['D7686'] . '</ObjektNummer-datadef-7686>
		<EnhetPrimarnaringSpesifisertObjekt-datadef-17150 orid="17150">' . $data['D17150'] . '</EnhetPrimarnaringSpesifisertObjekt-datadef-17150>
		<DriftsmidlerBeskrivelseSpesifisertObjekt-datadef-6909 orid="6909">' . $data['D6909'] . '</DriftsmidlerBeskrivelseSpesifisertObjekt-datadef-6909>
		<DriftsmidlerInnkjopsarSpesifisertObjekt-datadef-6917 orid="6917">' . $data['D6917'] . '</DriftsmidlerInnkjopsarSpesifisertObjekt-datadef-6917>
		<DriftsmidlerLevetidSpesifisertObjekt-datadef-6918 orid="6918">' . $data['D6918'] . '</DriftsmidlerLevetidSpesifisertObjekt-datadef-6918>
		<DriftsmidlerKostprisSpesifisertObjekt-datadef-6919 orid="6919">' . $data['D6919'] . '</DriftsmidlerKostprisSpesifisertObjekt-datadef-6919>
		<DriftsmidlerAvskrivningLinearSpesifisertObjekt-datadef-6910 orid="6910">' . $data['D6910'] . '</DriftsmidlerAvskrivningLinearSpesifisertObjekt-datadef-6910>
		<DriftsmidlerNedskrevetVerdiSpesifisertObjekt-datadef-6920 orid="6920">' . $data['D6920'] . '</DriftsmidlerNedskrevetVerdiSpesifisertObjekt-datadef-6920>
	</LineareAvskrivninger-grp-88>
</LineareAvskrivninger-grp-3505>
';
}
?>