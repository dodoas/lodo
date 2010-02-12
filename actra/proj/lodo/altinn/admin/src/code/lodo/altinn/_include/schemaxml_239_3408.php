<?php
// Filnavn: schemaxml_239_3408.php
// Skjema: RF-1221    Deltakerens oppgave over formue og inntekt i ANS mv
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-2106 gruppeid="2106">
	<InformasjonOmDeltaker-grp-102 gruppeid="102">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetEierandelANSMv-datadef-17007 orid="17007">' . $data['D17007'] . '</EnhetEierandelANSMv-datadef-17007>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
	</InformasjonOmDeltaker-grp-102>
	<InformasjonOmSelskap-grp-103 gruppeid="103">
		<EnhetNavnANSMv-datadef-15019 orid="15019">' . $data['D15019'] . '</EnhetNavnANSMv-datadef-15019>
		<AnsvarligSelskapAnsvarsform-datadef-15951 orid="15951">' . $data['D15951'] . '</AnsvarligSelskapAnsvarsform-datadef-15951>
		<EnhetAdresseANSMv-datadef-15020 orid="15020">' . $data['D15020'] . '</EnhetAdresseANSMv-datadef-15020>
		<EnhetPostnummerANSMv-datadef-15021 orid="15021">' . $data['D15021'] . '</EnhetPostnummerANSMv-datadef-15021>
		<EnhetPoststedANSMv-datadef-15022 orid="15022">' . $data['D15022'] . '</EnhetPoststedANSMv-datadef-15022>
		<DelingsforetakANSMv-datadef-15025 orid="15025">' . $data['D15025'] . '</DelingsforetakANSMv-datadef-15025>
		<LiberaltYrkeANSMv-datadef-15026 orid="15026">' . $data['D15026'] . '</LiberaltYrkeANSMv-datadef-15026>
		<BeskatningsmateOrdinarRederiRederi811-datadef-22307 orid="22307">' . $data['D22307'] . '</BeskatningsmateOrdinarRederiRederi811-datadef-22307>
		<EnhetSkattekommuneANSMv-datadef-15028 orid="15028">' . $data['D15028'] . '</EnhetSkattekommuneANSMv-datadef-15028>
		<EnhetTelefonnummerANSMv-datadef-15024 orid="15024">' . $data['D15024'] . '</EnhetTelefonnummerANSMv-datadef-15024>
		<EnhetOrganisasjonsnummerANSMv-datadef-15023 orid="15023">' . $data['D15023'] . '</EnhetOrganisasjonsnummerANSMv-datadef-15023>
		<KonsernspissNorskOrganisasjonsnummerDeltakeroppgave-datadef-20251 orid="20251">' . $data['D20251'] . '</KonsernspissNorskOrganisasjonsnummerDeltakeroppgave-datadef-20251>
	</InformasjonOmSelskap-grp-103>
</GenerellInformasjon-grp-2106>
';
}
else
{
$xml = '<GenerellInformasjon-grp-2106 gruppeid="2106">
	<InformasjonOmDeltaker-grp-102 gruppeid="102">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetEierandelANSMv-datadef-17007 orid="17007">' . $data['D17007'] . '</EnhetEierandelANSMv-datadef-17007>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
	</InformasjonOmDeltaker-grp-102>
	<InformasjonOmSelskap-grp-103 gruppeid="103">
		<EnhetNavnANSMv-datadef-15019 orid="15019">' . $data['D15019'] . '</EnhetNavnANSMv-datadef-15019>
		<AnsvarligSelskapAnsvarsform-datadef-15951 orid="15951">' . $data['D15951'] . '</AnsvarligSelskapAnsvarsform-datadef-15951>
		<EnhetAdresseANSMv-datadef-15020 orid="15020">' . $data['D15020'] . '</EnhetAdresseANSMv-datadef-15020>
		<EnhetPostnummerANSMv-datadef-15021 orid="15021">' . $data['D15021'] . '</EnhetPostnummerANSMv-datadef-15021>
		<EnhetPoststedANSMv-datadef-15022 orid="15022">' . $data['D15022'] . '</EnhetPoststedANSMv-datadef-15022>
		<DelingsforetakANSMv-datadef-15025 orid="15025">' . $data['D15025'] . '</DelingsforetakANSMv-datadef-15025>
		<LiberaltYrkeANSMv-datadef-15026 orid="15026">' . $data['D15026'] . '</LiberaltYrkeANSMv-datadef-15026>
		<BeskatningsmateOrdinarRederiRederi811-datadef-22307 orid="22307">' . $data['D22307'] . '</BeskatningsmateOrdinarRederiRederi811-datadef-22307>
		<EnhetSkattekommuneANSMv-datadef-15028 orid="15028">' . $data['D15028'] . '</EnhetSkattekommuneANSMv-datadef-15028>
		<EnhetTelefonnummerANSMv-datadef-15024 orid="15024">' . $data['D15024'] . '</EnhetTelefonnummerANSMv-datadef-15024>
		<EnhetOrganisasjonsnummerANSMv-datadef-15023 orid="15023">' . $data['D15023'] . '</EnhetOrganisasjonsnummerANSMv-datadef-15023>
		<KonsernspissNorskOrganisasjonsnummerDeltakeroppgave-datadef-20251 orid="20251">' . $data['D20251'] . '</KonsernspissNorskOrganisasjonsnummerDeltakeroppgave-datadef-20251>
	</InformasjonOmSelskap-grp-103>
</GenerellInformasjon-grp-2106>
<DeltakersInntektOgFormue-grp-104 gruppeid="104">
	<FormuesverdiNettoANSAndel-datadef-17008 orid="17008">' . $data['D17008'] . '</FormuesverdiNettoANSAndel-datadef-17008>
	<AndelAvAlminneligInntekt-grp-2333 gruppeid="2333">
		<ResultatANSAndel-datadef-17009 orid="17009">' . $data['D17009'] . '</ResultatANSAndel-datadef-17009>
		<GodtgjorelseArbeidANSAndel-datadef-17010 orid="17010">' . $data['D17010'] . '</GodtgjorelseArbeidANSAndel-datadef-17010>
		<InntekterLottfiskeANSAndel-datadef-17011 orid="17011">' . $data['D17011'] . '</InntekterLottfiskeANSAndel-datadef-17011>
		<ResultatJordbrukANSMvAndel-datadef-22308 orid="22308">' . $data['D22308'] . '</ResultatJordbrukANSMvAndel-datadef-22308>
		<ResultatSkogbrukReindriftANSMvAndel-datadef-22309 orid="22309">' . $data['D22309'] . '</ResultatSkogbrukReindriftANSMvAndel-datadef-22309>
		<ResultatJordbrukSkogbrukReindriftANSMvAndel-datadef-22310 orid="22310">' . $data['D22310'] . '</ResultatJordbrukSkogbrukReindriftANSMvAndel-datadef-22310>
		<AvviklingsOmstillingsfondetReindriftNettouttakANSMvAndel-datadef-22311 orid="22311">' . $data['D22311'] . '</AvviklingsOmstillingsfondetReindriftNettouttakANSMvAndel-datadef-22311>
		<InntektAlminneligANSAndel-datadef-17012 orid="17012">' . $data['D17012'] . '</InntektAlminneligANSAndel-datadef-17012>
		<AksjerMvRealisasjonGevinstTapNettoANSMvAndel-datadef-22312 orid="22312">' . $data['D22312'] . '</AksjerMvRealisasjonGevinstTapNettoANSMvAndel-datadef-22312>
		<InntektAlminneligANSOverforingSelvangivelse-datadef-19960 orid="19960">' . $data['D19960'] . '</InntektAlminneligANSOverforingSelvangivelse-datadef-19960>
	</AndelAvAlminneligInntekt-grp-2333>
	<AksjeutbytteANSAndel-datadef-17013 orid="17013">' . $data['D17013'] . '</AksjeutbytteANSAndel-datadef-17013>
	<Personinntekt-grp-2337 gruppeid="2337">
		<FiskeJordOgSkogbrukPelsOgReindrift-grp-4325 gruppeid="4325">
			<ArbeidsgodtgjorelseANSFiskeJordSkogPelsRein-datadef-19961 orid="19961">' . $data['D19961'] . '</ArbeidsgodtgjorelseANSFiskeJordSkogPelsRein-datadef-19961>
			<PersoninntektANSFiskeJordSkogPelsRein-datadef-19964 orid="19964">' . $data['D19964'] . '</PersoninntektANSFiskeJordSkogPelsRein-datadef-19964>
		</FiskeJordOgSkogbrukPelsOgReindrift-grp-4325>
		<LiberalVirksomhet-grp-4326 gruppeid="4326">
			<ArbeidsgodtgjorelseANSLiberalVirksomhet-datadef-19962 orid="19962">' . $data['D19962'] . '</ArbeidsgodtgjorelseANSLiberalVirksomhet-datadef-19962>
			<PersoninntektANSLiberalVirksomhet-datadef-19965 orid="19965">' . $data['D19965'] . '</PersoninntektANSLiberalVirksomhet-datadef-19965>
		</LiberalVirksomhet-grp-4326>
		<AnnenNaring-grp-4327 gruppeid="4327">
			<ArbeidsgodtgjorelseANSAnnenNaring-datadef-19963 orid="19963">' . $data['D19963'] . '</ArbeidsgodtgjorelseANSAnnenNaring-datadef-19963>
			<PersoninntektANSAnnenNaring-datadef-19966 orid="19966">' . $data['D19966'] . '</PersoninntektANSAnnenNaring-datadef-19966>
		</AnnenNaring-grp-4327>
	</Personinntekt-grp-2337>
</DeltakersInntektOgFormue-grp-104>
<FordelingAvAndelFormueOgInntekt-grp-105 gruppeid="105">
	<Kommune-grp-3065 gruppeid="3065">
		<KommuneANSNummerSpesifisertKommune-datadef-17016 orid="17016">' . $data['D17016'] . '</KommuneANSNummerSpesifisertKommune-datadef-17016>
		<FormueANSAndelSpesifisertKommune-datadef-17017 orid="17017">' . $data['D17017'] . '</FormueANSAndelSpesifisertKommune-datadef-17017>
		<ResultatANSAndelSpesifisertKommune-datadef-17018 orid="17018">' . $data['D17018'] . '</ResultatANSAndelSpesifisertKommune-datadef-17018>
	</Kommune-grp-3065>
	<InnskuddKontantbelopANSAndel-datadef-17020 orid="17020">' . $data['D17020'] . '</InnskuddKontantbelopANSAndel-datadef-17020>
	<UttakKontantbelopANSAndel-datadef-17021 orid="17021">' . $data['D17021'] . '</UttakKontantbelopANSAndel-datadef-17021>
	<UttakFormuesgjenstanderANSDeltaker-datadef-17022 orid="17022">' . $data['D17022'] . '</UttakFormuesgjenstanderANSDeltaker-datadef-17022>
	<InnskuddFormuesgjenstanderANSDeltaker-datadef-17023 orid="17023">' . $data['D17023'] . '</InnskuddFormuesgjenstanderANSDeltaker-datadef-17023>
	<TransaksjonerMellomDeltakerSelskapDeltaker-datadef-22483 orid="22483">' . $data['D22483'] . '</TransaksjonerMellomDeltakerSelskapDeltaker-datadef-22483>
</FordelingAvAndelFormueOgInntekt-grp-105>
<SarskiltePosterForDeltakereSomLiknesEtterSktl810-grp-4936 gruppeid="4936">
	<GevinstTapskontoInntektANSMvDeltakerRederibeskatningAndel-datadef-21976 orid="21976">' . $data['D21976'] . '</GevinstTapskontoInntektANSMvDeltakerRederibeskatningAndel-datadef-21976>
	<InntektInntredenDeltakerANSMvRederibeskatning-datadef-21977 orid="21977">' . $data['D21977'] . '</InntektInntredenDeltakerANSMvRederibeskatning-datadef-21977>
	<FinansinntektDeltakerRederibeskatningAndel-datadef-21975 orid="21975">' . $data['D21975'] . '</FinansinntektDeltakerRederibeskatningAndel-datadef-21975>
	<TonnasjeskattANSMvDeltakerAndel-datadef-22313 orid="22313">' . $data['D22313'] . '</TonnasjeskattANSMvDeltakerAndel-datadef-22313>
	<FinansaktivaANSMvDeltakerRederibeskatningAndelProsent-datadef-21978 orid="21978">' . $data['D21978'] . '</FinansaktivaANSMvDeltakerRederibeskatningAndelProsent-datadef-21978>
	<InntektUtgiftSkatteloven238ANSMvDeltakerAndel-datadef-22314 orid="22314">' . $data['D22314'] . '</InntektUtgiftSkatteloven238ANSMvDeltakerAndel-datadef-22314>
	<Totalkapital-grp-5054 gruppeid="5054">
		<TotalkapitalANSMvDeltakerAndelRederibeskatningFjoraret-datadef-22315 orid="22315">' . $data['D22315'] . '</TotalkapitalANSMvDeltakerAndelRederibeskatningFjoraret-datadef-22315>
		<TotalkapitalANSMvDeltakerAndelRederibeskatning-datadef-21979 orid="21979">' . $data['D21979'] . '</TotalkapitalANSMvDeltakerAndelRederibeskatning-datadef-21979>
	</Totalkapital-grp-5054>
	<AndelGjeld-grp-5055 gruppeid="5055">
		<GjeldANSMvDeltakerAndelRederibeskatning-datadef-22316 orid="22316">' . $data['D22316'] . '</GjeldANSMvDeltakerAndelRederibeskatning-datadef-22316>
		<GjeldANSMvDeltakerAndelRederibeskatning-datadef-21980 orid="21980">' . $data['D21980'] . '</GjeldANSMvDeltakerAndelRederibeskatning-datadef-21980>
	</AndelGjeld-grp-5055>
	<BokforingMetodeANSMvRederibeskatning-datadef-21981 orid="21981">' . $data['D21981'] . '</BokforingMetodeANSMvRederibeskatning-datadef-21981>
</SarskiltePosterForDeltakereSomLiknesEtterSktl810-grp-4936>
<RealisasjonAvAndel-grp-107 gruppeid="107">
	<Erverver-grp-4328 gruppeid="4328">
		<ErververAndelANSNavn-datadef-17061 orid="17061">' . $data['D17061'] . '</ErververAndelANSNavn-datadef-17061>
		<ErververAndelANSAdresse-datadef-17062 orid="17062">' . $data['D17062'] . '</ErververAndelANSAdresse-datadef-17062>
		<ErververAndelANSPostnummer-datadef-17063 orid="17063">' . $data['D17063'] . '</ErververAndelANSPostnummer-datadef-17063>
		<ErververAndelANSPoststed-datadef-17064 orid="17064">' . $data['D17064'] . '</ErververAndelANSPoststed-datadef-17064>
		<AndelANSRealisertProsent-datadef-17065 orid="17065">' . $data['D17065'] . '</AndelANSRealisertProsent-datadef-17065>
		<AndelANSSelgerErvervtidspunkt-datadef-17066 orid="17066">' . $data['D17066'] . '</AndelANSSelgerErvervtidspunkt-datadef-17066>
		<AndelANSRealisasjonstidspunkt-datadef-17067 orid="17067">' . $data['D17067'] . '</AndelANSRealisasjonstidspunkt-datadef-17067>
	</Erverver-grp-4328>
	<AndelANSRealisasjonVederlag-datadef-17068 orid="17068">' . $data['D17068'] . '</AndelANSRealisasjonVederlag-datadef-17068>
	<AndelANSRealisasjonEgenkapitalSkattemessig-datadef-17069 orid="17069">' . $data['D17069'] . '</AndelANSRealisasjonEgenkapitalSkattemessig-datadef-17069>
	<AndelANSRealisasjonEgenkapitalSkjevKorreksjon-datadef-17070 orid="17070">' . $data['D17070'] . '</AndelANSRealisasjonEgenkapitalSkjevKorreksjon-datadef-17070>
	<AndelANSRealisasjonOverUnderpris-datadef-17071 orid="17071">' . $data['D17071'] . '</AndelANSRealisasjonOverUnderpris-datadef-17071>
	<AndelANSRealisasjonFusjonFisjonDifferanse-datadef-17072 orid="17072">' . $data['D17072'] . '</AndelANSRealisasjonFusjonFisjonDifferanse-datadef-17072>
	<AndelANSRealisasjonSalgskostnader-datadef-17073 orid="17073">' . $data['D17073'] . '</AndelANSRealisasjonSalgskostnader-datadef-17073>
	<GevinstTap-grp-4054 gruppeid="4054">
		<AndelANSRealisasjonGevinst-datadef-17076 orid="17076">' . $data['D17076'] . '</AndelANSRealisasjonGevinst-datadef-17076>
		<AndelANSRealisasjonTap-datadef-17077 orid="17077">' . $data['D17077'] . '</AndelANSRealisasjonTap-datadef-17077>
	</GevinstTap-grp-4054>
</RealisasjonAvAndel-grp-107>
<SkattemessigEKPaOverdragelsestidspunkt-grp-109 gruppeid="109">
	<AndelANSEgenkapitalSkattemessigFjoraret-datadef-17078 orid="17078">' . $data['D17078'] . '</AndelANSEgenkapitalSkattemessigFjoraret-datadef-17078>
	<AndelANSResultatAvhender-datadef-17079 orid="17079">' . $data['D17079'] . '</AndelANSResultatAvhender-datadef-17079>
	<AndelANSInntekterUtgifterSkattefriIkkeFradragsberettigetAvh-datadef-17080 orid="17080">' . $data['D17080'] . '</AndelANSInntekterUtgifterSkattefriIkkeFradragsberettigetAvh-datadef-17080>
	<AndelANSInnbetalingUtbetalingAvhenderSelskap-datadef-17081 orid="17081">' . $data['D17081'] . '</AndelANSInnbetalingUtbetalingAvhenderSelskap-datadef-17081>
	<AndelANSEgenkapitalSkattemessigOverdragelsestidspunkt-datadef-17082 orid="17082">' . $data['D17082'] . '</AndelANSEgenkapitalSkattemessigOverdragelsestidspunkt-datadef-17082>
</SkattemessigEKPaOverdragelsestidspunkt-grp-109>
<OverUnderprisVedErvervAvAndel-grp-110 gruppeid="110">
	<Avhender-grp-4329 gruppeid="4329">
		<AvhenderAndelANSNavn-datadef-17083 orid="17083">' . $data['D17083'] . '</AvhenderAndelANSNavn-datadef-17083>
		<AvhenderAndelANSAdresse-datadef-17084 orid="17084">' . $data['D17084'] . '</AvhenderAndelANSAdresse-datadef-17084>
		<AvhenderAndelANSPostnummer-datadef-17085 orid="17085">' . $data['D17085'] . '</AvhenderAndelANSPostnummer-datadef-17085>
		<AvhenderAndelANSPoststed-datadef-17086 orid="17086">' . $data['D17086'] . '</AvhenderAndelANSPoststed-datadef-17086>
		<AndelANSErvervetProsent-datadef-17087 orid="17087">' . $data['D17087'] . '</AndelANSErvervetProsent-datadef-17087>
		<AndelANSErvervtidspunkt-datadef-17088 orid="17088">' . $data['D17088'] . '</AndelANSErvervtidspunkt-datadef-17088>
	</Avhender-grp-4329>
	<AndelANSErvervVederlag-datadef-17089 orid="17089">' . $data['D17089'] . '</AndelANSErvervVederlag-datadef-17089>
	<AndelANSErvervEgenkapitalSkattemessig-datadef-17090 orid="17090">' . $data['D17090'] . '</AndelANSErvervEgenkapitalSkattemessig-datadef-17090>
	<AndelANSErvervEgenkapitalSkjevKorreksjon-datadef-17091 orid="17091">' . $data['D17091'] . '</AndelANSErvervEgenkapitalSkjevKorreksjon-datadef-17091>
	<AndelANSErvervKjopskostnader-datadef-17092 orid="17092">' . $data['D17092'] . '</AndelANSErvervKjopskostnader-datadef-17092>
	<AndelANSErvervOverUnderpris-datadef-17093 orid="17093">' . $data['D17093'] . '</AndelANSErvervOverUnderpris-datadef-17093>
</OverUnderprisVedErvervAvAndel-grp-110>
<DifferanseVedFusjonFisjon-grp-111 gruppeid="111">
	<AndelANSEgenkapitalSkattemessigForFusjonFisjon-datadef-17094 orid="17094">' . $data['D17094'] . '</AndelANSEgenkapitalSkattemessigForFusjonFisjon-datadef-17094>
	<AndelANSEgenkapitalSkattemessigEtterFusjonFisjon-datadef-17095 orid="17095">' . $data['D17095'] . '</AndelANSEgenkapitalSkattemessigEtterFusjonFisjon-datadef-17095>
	<AndelANSFusjonFisjonDifferanse-datadef-17096 orid="17096">' . $data['D17096'] . '</AndelANSFusjonFisjonDifferanse-datadef-17096>
</DifferanseVedFusjonFisjon-grp-111>
<AndreOpplysninger-grp-112 gruppeid="112">
	<AndelANSErvervTidspunktSpesifisertAndel-datadef-17097 orid="17097">' . $data['D17097'] . '</AndelANSErvervTidspunktSpesifisertAndel-datadef-17097>
	<AndelANSErvervOverUnderprisBeregnet-datadef-17098 orid="17098">' . $data['D17098'] . '</AndelANSErvervOverUnderprisBeregnet-datadef-17098>
</AndreOpplysninger-grp-112>
';
}
?>