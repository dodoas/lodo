<?php
// Filnavn: schemaxml_238_3490.php
// Skjema: RF-1215    Selskapsoppgave for ansvarlige selskap mv
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-819 gruppeid="819">
	<Selskap-grp-2161 gruppeid="2161">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetTelefonnummer-datadef-755 orid="755">' . $data['D755'] . '</EnhetTelefonnummer-datadef-755>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<EnhetSkattekommune-datadef-2888 orid="2888">' . $data['D2888'] . '</EnhetSkattekommune-datadef-2888>
		<EnhetOrganisasjonsform-datadef-756 orid="756">' . $data['D756'] . '</EnhetOrganisasjonsform-datadef-756>
		<DeltakereAntall-datadef-1022 orid="1022">' . $data['D1022'] . '</DeltakereAntall-datadef-1022>
		<KonsernspissNorskOrganisasjonsnummer-datadef-19851 orid="19851">' . $data['D19851'] . '</KonsernspissNorskOrganisasjonsnummer-datadef-19851>
		<EnhetSignaturNavn-datadef-13943 orid="13943">' . $data['D13943'] . '</EnhetSignaturNavn-datadef-13943>
		<EnhetKommunenummerDLS-datadef-18070 orid="18070">' . $data['D18070'] . '</EnhetKommunenummerDLS-datadef-18070>
	</Selskap-grp-2161>
	<Regnskapsforer-grp-66 gruppeid="66">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-66>
</GenerellInformasjon-grp-819>
';
}
else
{
$xml = '<GenerellInformasjon-grp-819 gruppeid="819">
	<Selskap-grp-2161 gruppeid="2161">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetTelefonnummer-datadef-755 orid="755">' . $data['D755'] . '</EnhetTelefonnummer-datadef-755>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<EnhetSkattekommune-datadef-2888 orid="2888">' . $data['D2888'] . '</EnhetSkattekommune-datadef-2888>
		<EnhetOrganisasjonsform-datadef-756 orid="756">' . $data['D756'] . '</EnhetOrganisasjonsform-datadef-756>
		<DeltakereAntall-datadef-1022 orid="1022">' . $data['D1022'] . '</DeltakereAntall-datadef-1022>
		<KonsernspissNorskOrganisasjonsnummer-datadef-19851 orid="19851">' . $data['D19851'] . '</KonsernspissNorskOrganisasjonsnummer-datadef-19851>
		<EnhetSignaturNavn-datadef-13943 orid="13943">' . $data['D13943'] . '</EnhetSignaturNavn-datadef-13943>
		<EnhetKommunenummerDLS-datadef-18070 orid="18070">' . $data['D18070'] . '</EnhetKommunenummerDLS-datadef-18070>
	</Selskap-grp-2161>
	<Regnskapsforer-grp-66 gruppeid="66">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-66>
</GenerellInformasjon-grp-819>
<DeltakereISelskapetSameiet-grp-3503 gruppeid="3503">
	<DeltakereISelskapetSameiet-grp-2162 gruppeid="2162">
		<DeltakerFodselsnummer-datadef-3647 orid="3647">' . $data['D3647'] . '</DeltakerFodselsnummer-datadef-3647>
		<DeltakerOrganisasjonsnummer-datadef-3648 orid="3648">' . $data['D3648'] . '</DeltakerOrganisasjonsnummer-datadef-3648>
		<DeltakerNavn-datadef-1025 orid="1025">' . $data['D1025'] . '</DeltakerNavn-datadef-1025>
		<DeltakerKommuneSpesifisertKommune-datadef-13804 orid="13804">' . $data['D13804'] . '</DeltakerKommuneSpesifisertKommune-datadef-13804>
		<DeltakerEierandelFjoraretSpesifisertDeltaker-datadef-17172 orid="17172">' . $data['D17172'] . '</DeltakerEierandelFjoraretSpesifisertDeltaker-datadef-17172>
		<DeltakerEierandelSpesifisertDeltaker-datadef-7948 orid="7948">' . $data['D7948'] . '</DeltakerEierandelSpesifisertDeltaker-datadef-7948>
		<AksjonarDeltakerLikningSkatteloven810Spesifisert-datadef-22070 orid="22070">' . $data['D22070'] . '</AksjonarDeltakerLikningSkatteloven810Spesifisert-datadef-22070>
		<Endringer-grp-3884 gruppeid="3884">
			<AndelErvervetSpesifisertDeltaker-datadef-14366 orid="14366">' . $data['D14366'] . '</AndelErvervetSpesifisertDeltaker-datadef-14366>
			<AndelRealisertSpesifisertDeltaker-datadef-14365 orid="14365">' . $data['D14365'] . '</AndelRealisertSpesifisertDeltaker-datadef-14365>
		</Endringer-grp-3884>
	</DeltakereISelskapetSameiet-grp-2162>
	<SumEierandeler-grp-4257 gruppeid="4257">
		<DetakereEierandelerFjoraretKontrollsum-datadef-19958 orid="19958">' . $data['D19958'] . '</DetakereEierandelerFjoraretKontrollsum-datadef-19958>
		<DeltakereEierandelerKontrollsum-datadef-19959 orid="19959">' . $data['D19959'] . '</DeltakereEierandelerKontrollsum-datadef-19959>
	</SumEierandeler-grp-4257>
</DeltakereISelskapetSameiet-grp-3503>
<Formuesstilling-grp-2163 gruppeid="2163">
	<SkattemessigVerdi-grp-3680 gruppeid="3680">
		<KontormaskinerMvSkattemessigVerdi-datadef-1030 orid="1030">' . $data['D1030'] . '</KontormaskinerMvSkattemessigVerdi-datadef-1030>
		<ForretningsverdiErvervetSkattemessigVerdi-datadef-1032 orid="1032">' . $data['D1032'] . '</ForretningsverdiErvervetSkattemessigVerdi-datadef-1032>
		<VogntogLastebilerVarebilerMvSkattemessigVerdi-datadef-1033 orid="1033">' . $data['D1033'] . '</VogntogLastebilerVarebilerMvSkattemessigVerdi-datadef-1033>
		<PersonbilerTraktorerMaskinerMvSkattemessigVerdi-datadef-1035 orid="1035">' . $data['D1035'] . '</PersonbilerTraktorerMaskinerMvSkattemessigVerdi-datadef-1035>
		<SkipFartoyerRiggerMvSkattemessigVerdi-datadef-1037 orid="1037">' . $data['D1037'] . '</SkipFartoyerRiggerMvSkattemessigVerdi-datadef-1037>
		<FlyHelikopterSkattemessigVerdi-datadef-1039 orid="1039">' . $data['D1039'] . '</FlyHelikopterSkattemessigVerdi-datadef-1039>
		<LagerbeholdningSkattemessig-datadef-115 orid="115">' . $data['D115'] . '</LagerbeholdningSkattemessig-datadef-115>
		<FordringerSkattemessig-datadef-287 orid="287">' . $data['D287'] . '</FordringerSkattemessig-datadef-287>
		<BankinnskuddKontantbeholdningSkattemessigVerdi-datadef-1052 orid="1052">' . $data['D1052'] . '</BankinnskuddKontantbeholdningSkattemessigVerdi-datadef-1052>
		<AksjerMvSkattemessigVerdi-datadef-1014 orid="1014">' . $data['D1014'] . '</AksjerMvSkattemessigVerdi-datadef-1014>
		<AndelerDeltakerlignedeSelskaperSkattemessigVerdi-datadef-1015 orid="1015">' . $data['D1015'] . '</AndelerDeltakerlignedeSelskaperSkattemessigVerdi-datadef-1015>
		<GevinstTapskontoNegativ-datadef-7308 orid="7308">' . $data['D7308'] . '</GevinstTapskontoNegativ-datadef-7308>
		<GevinstTapskontoPositiv-datadef-7321 orid="7321">' . $data['D7321'] . '</GevinstTapskontoPositiv-datadef-7321>
		<GevinsterAndreBetingetSkattefrie-datadef-1057 orid="1057">' . $data['D1057'] . '</GevinsterAndreBetingetSkattefrie-datadef-1057>
		<PosisjonerAndreSkattemessige-datadef-1059 orid="1059">' . $data['D1059'] . '</PosisjonerAndreSkattemessige-datadef-1059>
		<OmvurderingskontoValuta-datadef-1060 orid="1060">' . $data['D1060'] . '</OmvurderingskontoValuta-datadef-1060>
		<EiendelerSkattemessige-datadef-1061 orid="1061">' . $data['D1061'] . '</EiendelerSkattemessige-datadef-1061>
		<GjeldAnsvarligeSkattemessig-datadef-10090 orid="10090">' . $data['D10090'] . '</GjeldAnsvarligeSkattemessig-datadef-10090>
		<EgenkapitalSkattemessig-datadef-1065 orid="1065">' . $data['D1065'] . '</EgenkapitalSkattemessig-datadef-1065>
	</SkattemessigVerdi-grp-3680>
	<LikningsmessigFormuesverdi-grp-3681 gruppeid="3681">
		<KontormaskinerMvLigningsmessigFormuesverdi-datadef-1031 orid="1031">' . $data['D1031'] . '</KontormaskinerMvLigningsmessigFormuesverdi-datadef-1031>
		<VogntogLastebilerVarebilerMvLigningsmessigFormuesverdi-datadef-1034 orid="1034">' . $data['D1034'] . '</VogntogLastebilerVarebilerMvLigningsmessigFormuesverdi-datadef-1034>
		<PersonbilerTraktorerMaskinerMvLigningsmessigFormuesverdi-datadef-1036 orid="1036">' . $data['D1036'] . '</PersonbilerTraktorerMaskinerMvLigningsmessigFormuesverdi-datadef-1036>
		<SkipFartoyerRiggerMvLigningsmessigFormuesverdi-datadef-1038 orid="1038">' . $data['D1038'] . '</SkipFartoyerRiggerMvLigningsmessigFormuesverdi-datadef-1038>
		<FlyHelikopterLigningsmessigFormuesverdi-datadef-1040 orid="1040">' . $data['D1040'] . '</FlyHelikopterLigningsmessigFormuesverdi-datadef-1040>
		<LagerbeholdningLigningsmessigFormuesverdi-datadef-1050 orid="1050">' . $data['D1050'] . '</LagerbeholdningLigningsmessigFormuesverdi-datadef-1050>
		<FordringerLigningsmessigFormuesverdi-datadef-1051 orid="1051">' . $data['D1051'] . '</FordringerLigningsmessigFormuesverdi-datadef-1051>
		<BankinnskuddKontantbeholdningLigningsmessigFormuesverdi-datadef-1053 orid="1053">' . $data['D1053'] . '</BankinnskuddKontantbeholdningLigningsmessigFormuesverdi-datadef-1053>
		<AksjerMvLigningsmessigFormuesverdi-datadef-1054 orid="1054">' . $data['D1054'] . '</AksjerMvLigningsmessigFormuesverdi-datadef-1054>
		<AndelerDeltakerlignedeSelskaperLigningsmessigFormuesverdi-datadef-1016 orid="1016">' . $data['D1016'] . '</AndelerDeltakerlignedeSelskaperLigningsmessigFormuesverdi-datadef-1016>
		<FormuesverdiBruttoLigningsmessig-datadef-1062 orid="1062">' . $data['D1062'] . '</FormuesverdiBruttoLigningsmessig-datadef-1062>
		<GjeldLigningsmessig-datadef-1063 orid="1063">' . $data['D1063'] . '</GjeldLigningsmessig-datadef-1063>
		<FormuesverdiNettoLigningsmessig-datadef-1066 orid="1066">' . $data['D1066'] . '</FormuesverdiNettoLigningsmessig-datadef-1066>
	</LikningsmessigFormuesverdi-grp-3681>
	<Landbruksbygg-grp-3856 gruppeid="3856">
		<LandbruksbyggSkattemessigVerdi-datadef-13822 orid="13822">' . $data['D13822'] . '</LandbruksbyggSkattemessigVerdi-datadef-13822>
		<LandbruksbyggLigningsmessigFormuesverdi-datadef-13823 orid="13823">' . $data['D13823'] . '</LandbruksbyggLigningsmessigFormuesverdi-datadef-13823>
		<LandbruksbyggKommune-datadef-13824 orid="13824">' . $data['D13824'] . '</LandbruksbyggKommune-datadef-13824>
	</Landbruksbygg-grp-3856>
	<ByggOgAnleggHotellerLosjihusBevertningsstederMv-grp-3857 gruppeid="3857">
		<ByggAnleggSkattemessigVerdi-datadef-1041 orid="1041">' . $data['D1041'] . '</ByggAnleggSkattemessigVerdi-datadef-1041>
		<ByggAnleggLigningsmessigFormuesverdi-datadef-1042 orid="1042">' . $data['D1042'] . '</ByggAnleggLigningsmessigFormuesverdi-datadef-1042>
		<ByggAnleggKommune-datadef-1043 orid="1043">' . $data['D1043'] . '</ByggAnleggKommune-datadef-1043>
	</ByggOgAnleggHotellerLosjihusBevertningsstederMv-grp-3857>
	<Forretningsbygg-grp-3858 gruppeid="3858">
		<ForretningsbyggSkattemessigVerdi-datadef-1044 orid="1044">' . $data['D1044'] . '</ForretningsbyggSkattemessigVerdi-datadef-1044>
		<ForretningsbyggLigningsmessigFormuesverdi-datadef-1045 orid="1045">' . $data['D1045'] . '</ForretningsbyggLigningsmessigFormuesverdi-datadef-1045>
		<ForretningsbyggKommune-datadef-1046 orid="1046">' . $data['D1046'] . '</ForretningsbyggKommune-datadef-1046>
	</Forretningsbygg-grp-3858>
	<Tomter-grp-3859 gruppeid="3859">
		<TomterSkattemessigVerdi-datadef-1047 orid="1047">' . $data['D1047'] . '</TomterSkattemessigVerdi-datadef-1047>
		<TomterLigningsmessigFormuesverdi-datadef-1048 orid="1048">' . $data['D1048'] . '</TomterLigningsmessigFormuesverdi-datadef-1048>
		<TomterKommune-datadef-1049 orid="1049">' . $data['D1049'] . '</TomterKommune-datadef-1049>
	</Tomter-grp-3859>
	<AndreFormuesposter-grp-3871 gruppeid="3871">
		<FormuesposterAndreSkattemessigVerdi-datadef-1055 orid="1055">' . $data['D1055'] . '</FormuesposterAndreSkattemessigVerdi-datadef-1055>
		<FormuesposterAndreLigningsmessigFormuesverdi-datadef-1056 orid="1056">' . $data['D1056'] . '</FormuesposterAndreLigningsmessigFormuesverdi-datadef-1056>
	</AndreFormuesposter-grp-3871>
</Formuesstilling-grp-2163>
<InntektOgFormueISelskapet-grp-2169 gruppeid="2169">
	<ResultatNaring-datadef-7298 orid="7298">' . $data['D7298'] . '</ResultatNaring-datadef-7298>
	<AksjeutbytteNorskeSelskaperSkattemessigFullGodtgjorelse-datadef-11239 orid="11239">' . $data['D11239'] . '</AksjeutbytteNorskeSelskaperSkattemessigFullGodtgjorelse-datadef-11239>
	<ResultatDeltakerlignetSelskapSkattemessig-datadef-7841 orid="7841">' . $data['D7841'] . '</ResultatDeltakerlignetSelskapSkattemessig-datadef-7841>
	<AksjerMvRealisasjonGevinstTapSelskapet-datadef-22353 orid="22353">' . $data['D22353'] . '</AksjerMvRealisasjonGevinstTapSelskapet-datadef-22353>
</InntektOgFormueISelskapet-grp-2169>
<SelskaperSomLiknesEtterSktl810-grp-4961 gruppeid="4961">
	<FinansinntekterSkattemessig-datadef-11802 orid="11802">' . $data['D11802'] . '</FinansinntekterSkattemessig-datadef-11802>
	<GevinstTapskontoInntektsfort-datadef-2044 orid="2044">' . $data['D2044'] . '</GevinstTapskontoInntektsfort-datadef-2044>
	<InntektInntreden-datadef-12761 orid="12761">' . $data['D12761'] . '</InntektInntreden-datadef-12761>
	<Tonnasjeskatt-datadef-5857 orid="5857">' . $data['D5857'] . '</Tonnasjeskatt-datadef-5857>
	<InntektGevinstSkatteloven238-datadef-22332 orid="22332">' . $data['D22332'] . '</InntektGevinstSkatteloven238-datadef-22332>
</SelskaperSomLiknesEtterSktl810-grp-4961>
<SelskapMedInntektFormueTilBeskatningIFlereKommuner-grp-2170 gruppeid="2170">
	<ForslagTilFordeling-grp-3010 gruppeid="3010">
		<Totalt-grp-3066 gruppeid="3066">
			<InntektStedbundet-datadef-13867 orid="13867">' . $data['D13867'] . '</InntektStedbundet-datadef-13867>
			<Fordelingsfradrag-datadef-13806 orid="13806">' . $data['D13806'] . '</Fordelingsfradrag-datadef-13806>
		</Totalt-grp-3066>
		<Kommune-grp-3067 gruppeid="3067">
			<EiendomNaringAnnenKommuneKommunenummerSpesifisertEiendomMv-datadef-17034 orid="17034">' . $data['D17034'] . '</EiendomNaringAnnenKommuneKommunenummerSpesifisertEiendomMv-datadef-17034>
			<InntektNaringFordeltSpesifisertKommune-datadef-13808 orid="13808">' . $data['D13808'] . '</InntektNaringFordeltSpesifisertKommune-datadef-13808>
			<FordelingsfradragFordeltSpesifisertKommune-datadef-13810 orid="13810">' . $data['D13810'] . '</FordelingsfradragFordeltSpesifisertKommune-datadef-13810>
			<ResultatDeltakerlignetSelskapNettoFordeltSpesifisertKommune-datadef-13812 orid="13812">' . $data['D13812'] . '</ResultatDeltakerlignetSelskapNettoFordeltSpesifisertKommune-datadef-13812>
			<FormuesverdiBruttoFordeltSpesifisertKommune-datadef-13814 orid="13814">' . $data['D13814'] . '</FormuesverdiBruttoFordeltSpesifisertKommune-datadef-13814>
			<GjeldFordeltSpesifisertKommune-datadef-13816 orid="13816">' . $data['D13816'] . '</GjeldFordeltSpesifisertKommune-datadef-13816>
			<FormuesverdiNettoFordeltSpesifisertKommune-datadef-13818 orid="13818">' . $data['D13818'] . '</FormuesverdiNettoFordeltSpesifisertKommune-datadef-13818>
		</Kommune-grp-3067>
	</ForslagTilFordeling-grp-3010>
</SelskapMedInntektFormueTilBeskatningIFlereKommuner-grp-2170>
<FordelingAvFormueVedSkjevEgenkapital-grp-3504 gruppeid="3504">
	<FordelingAvFormueVedSkjevEgenkapital-grp-2172 gruppeid="2172">
		<DeltakerNavnFordelingFormueSpesifisertDeltaker-datadef-13864 orid="13864">' . $data['D13864'] . '</DeltakerNavnFordelingFormueSpesifisertDeltaker-datadef-13864>
		<DeltakerFodselsnummerOrganisasjonsnummerSpesifisertDeltaker-datadef-17363 orid="17363">' . $data['D17363'] . '</DeltakerFodselsnummerOrganisasjonsnummerSpesifisertDeltaker-datadef-17363>
		<FormueNettoEierandelSpesifisertDeltaker-datadef-1073 orid="1073">' . $data['D1073'] . '</FormueNettoEierandelSpesifisertDeltaker-datadef-1073>
		<EgenkapitalkontoSpesifisertDeltaker-datadef-3066 orid="3066">' . $data['D3066'] . '</EgenkapitalkontoSpesifisertDeltaker-datadef-3066>
		<EgenkapitalSpesifisertDeltaker-datadef-3067 orid="3067">' . $data['D3067'] . '</EgenkapitalSpesifisertDeltaker-datadef-3067>
		<FormueNettoAndelKorrigertSpesifisertDeltaker-datadef-3068 orid="3068">' . $data['D3068'] . '</FormueNettoAndelKorrigertSpesifisertDeltaker-datadef-3068>
	</FordelingAvFormueVedSkjevEgenkapital-grp-2172>
	<Sum-grp-2467 gruppeid="2467">
		<Egenkapitalkonti-datadef-8327 orid="8327">' . $data['D8327'] . '</Egenkapitalkonti-datadef-8327>
		<EgenkapitalSkjev-datadef-16175 orid="16175">' . $data['D16175'] . '</EgenkapitalSkjev-datadef-16175>
		<FormueNettoKorrigert-datadef-13865 orid="13865">' . $data['D13865'] . '</FormueNettoKorrigert-datadef-13865>
	</Sum-grp-2467>
</FordelingAvFormueVedSkjevEgenkapital-grp-3504>
<OvrigeOpplysninger-grp-2177 gruppeid="2177">
	<Delingsforetak-datadef-1023 orid="1023">' . $data['D1023'] . '</Delingsforetak-datadef-1023>
	<EnhetNaring-datadef-19410 orid="19410">' . $data['D19410'] . '</EnhetNaring-datadef-19410>
	<EnhetLiberaltYrke-datadef-17000 orid="17000">' . $data['D17000'] . '</EnhetLiberaltYrke-datadef-17000>
	<EnhetFormueVirksomhetFlereKommuner-datadef-17171 orid="17171">' . $data['D17171'] . '</EnhetFormueVirksomhetFlereKommuner-datadef-17171>
	<FusjonFisjon-datadef-5651 orid="5651">' . $data['D5651'] . '</FusjonFisjon-datadef-5651>
	<EnhetStiftetOmdannelse-datadef-13819 orid="13819">' . $data['D13819'] . '</EnhetStiftetOmdannelse-datadef-13819>
	<FormueInntektFordelt-datadef-13820 orid="13820">' . $data['D13820'] . '</FormueInntektFordelt-datadef-13820>
	<TransaksjonerMellomDeltakerSelskap-datadef-13821 orid="13821">' . $data['D13821'] . '</TransaksjonerMellomDeltakerSelskap-datadef-13821>
	<DeltagerNaturalytelser-datadef-17175 orid="17175">' . $data['D17175'] . '</DeltagerNaturalytelser-datadef-17175>
	<DeltakerRederiSkatteloven810-datadef-22062 orid="22062">' . $data['D22062'] . '</DeltakerRederiSkatteloven810-datadef-22062>
</OvrigeOpplysninger-grp-2177>
';
}
?>