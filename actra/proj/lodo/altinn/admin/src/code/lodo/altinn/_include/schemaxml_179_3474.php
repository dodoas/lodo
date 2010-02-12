<?php
// RF-1027  Selvangivelse for næringsdrivende mv
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-763 gruppeid="763">
	<Likningskontor-grp-584 gruppeid="584">
		<OppgavegiverLigningskontor-datadef-2725 orid="2725">' . $data['D2725'] . '</OppgavegiverLigningskontor-datadef-2725>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<ForhandsligningOnske-datadef-17132 orid="17132">' . $data['D17132'] . '</ForhandsligningOnske-datadef-17132>
		<ForhandsligningAr-datadef-19679 orid="19679">' . $data['D19679'] . '</ForhandsligningAr-datadef-19679>
	</Likningskontor-grp-584>
	<Personalia-grp-250 gruppeid="250">
		<OppgavegiverNavn-datadef-68 orid="68">' . $data['D68'] . '</OppgavegiverNavn-datadef-68>
		<OppgavegiverPostadresse-datadef-2442 orid="2442">' . $data['D2442'] . '</OppgavegiverPostadresse-datadef-2442>
		<OppgavegiverPostnummer-datadef-6676 orid="6676">' . $data['D6676'] . '</OppgavegiverPostnummer-datadef-6676>
		<OppgavegiverPoststed-datadef-6677 orid="6677">' . $data['D6677'] . '</OppgavegiverPoststed-datadef-6677>
		<OppgavegiverBoligadresseEndring-datadef-6696 orid="6696">' . $data['D6696'] . '</OppgavegiverBoligadresseEndring-datadef-6696>
		<OppgavegiverPostadresseEndring-datadef-6697 orid="6697">' . $data['D6697'] . '</OppgavegiverPostadresseEndring-datadef-6697>
		<OppgavegiverPostnummerEndring-datadef-6699 orid="6699">' . $data['D6699'] . '</OppgavegiverPostnummerEndring-datadef-6699>
		<OppgavegiverPoststedEndring-datadef-6698 orid="6698">' . $data['D6698'] . '</OppgavegiverPoststedEndring-datadef-6698>
		<OppgavegiverStillingYrke-datadef-1834 orid="1834">' . $data['D1834'] . '</OppgavegiverStillingYrke-datadef-1834>
		<OppgavegiverTelefonnummerPrivat-datadef-16533 orid="16533">' . $data['D16533'] . '</OppgavegiverTelefonnummerPrivat-datadef-16533>
		<OppgavegiverTelefonnummerArbeid-datadef-6700 orid="6700">' . $data['D6700'] . '</OppgavegiverTelefonnummerArbeid-datadef-6700>
		<OppgavegiverSivilStatus-datadef-520 orid="520">' . $data['D520'] . '</OppgavegiverSivilStatus-datadef-520>
	</Personalia-grp-250>
	<Ektefelle-grp-87 gruppeid="87">
		<EktefelleNavn-datadef-2272 orid="2272">' . $data['D2272'] . '</EktefelleNavn-datadef-2272>
		<EktefelleFodselsnummer-datadef-19372 orid="19372">' . $data['D19372'] . '</EktefelleFodselsnummer-datadef-19372>
		<EktefelleInntektFordeling-datadef-14086 orid="14086">' . $data['D14086'] . '</EktefelleInntektFordeling-datadef-14086>
		<Ekteskap-grp-3607 gruppeid="3607">
			<EkteskapDato-datadef-6702 orid="6702">' . $data['D6702'] . '</EkteskapDato-datadef-6702>
			<EktefelleNavnForEkteskap-datadef-6703 orid="6703">' . $data['D6703'] . '</EktefelleNavnForEkteskap-datadef-6703>
			<EktefelleBoligadresseForEkteskap-datadef-6704 orid="6704">' . $data['D6704'] . '</EktefelleBoligadresseForEkteskap-datadef-6704>
			<EktefellePostnummerForEkteskap-datadef-6705 orid="6705">' . $data['D6705'] . '</EktefellePostnummerForEkteskap-datadef-6705>
			<EktefellePoststedForEkteskap-datadef-6706 orid="6706">' . $data['D6706'] . '</EktefellePoststedForEkteskap-datadef-6706>
			<EktefelleSkattekommuneForEkteskap-datadef-6707 orid="6707">' . $data['D6707'] . '</EktefelleSkattekommuneForEkteskap-datadef-6707>
		</Ekteskap-grp-3607>
	</Ektefelle-grp-87>
	<SamboerMedFellesBarn-grp-252 gruppeid="252">
		<SamboerNavn-datadef-6708 orid="6708">' . $data['D6708'] . '</SamboerNavn-datadef-6708>
		<SamboerFodselsnummer-datadef-6709 orid="6709">' . $data['D6709'] . '</SamboerFodselsnummer-datadef-6709>
	</SamboerMedFellesBarn-grp-252>
</GenerellInformasjon-grp-763>';	
}
else
{
$xml = '<GenerellInformasjon-grp-763 gruppeid="763">
	<Likningskontor-grp-584 gruppeid="584">
		<OppgavegiverLigningskontor-datadef-2725 orid="2725">' . $data['D2725'] . '</OppgavegiverLigningskontor-datadef-2725>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<ForhandsligningOnske-datadef-17132 orid="17132">' . $data['D17132'] . '</ForhandsligningOnske-datadef-17132>
		<ForhandsligningAr-datadef-19679 orid="19679">' . $data['D19679'] . '</ForhandsligningAr-datadef-19679>
	</Likningskontor-grp-584>
	<Personalia-grp-250 gruppeid="250">
		<OppgavegiverNavn-datadef-68 orid="68">' . $data['D68'] . '</OppgavegiverNavn-datadef-68>
		<OppgavegiverPostadresse-datadef-2442 orid="2442">' . $data['D2442'] . '</OppgavegiverPostadresse-datadef-2442>
		<OppgavegiverPostnummer-datadef-6676 orid="6676">' . $data['D6676'] . '</OppgavegiverPostnummer-datadef-6676>
		<OppgavegiverPoststed-datadef-6677 orid="6677">' . $data['D6677'] . '</OppgavegiverPoststed-datadef-6677>
		<OppgavegiverBoligadresseEndring-datadef-6696 orid="6696">' . $data['D6696'] . '</OppgavegiverBoligadresseEndring-datadef-6696>
		<OppgavegiverPostadresseEndring-datadef-6697 orid="6697">' . $data['D6697'] . '</OppgavegiverPostadresseEndring-datadef-6697>
		<OppgavegiverPostnummerEndring-datadef-6699 orid="6699">' . $data['D6699'] . '</OppgavegiverPostnummerEndring-datadef-6699>
		<OppgavegiverPoststedEndring-datadef-6698 orid="6698">' . $data['D6698'] . '</OppgavegiverPoststedEndring-datadef-6698>
		<OppgavegiverStillingYrke-datadef-1834 orid="1834">' . $data['D1834'] . '</OppgavegiverStillingYrke-datadef-1834>
		<OppgavegiverTelefonnummerPrivat-datadef-16533 orid="16533">' . $data['D16533'] . '</OppgavegiverTelefonnummerPrivat-datadef-16533>
		<OppgavegiverTelefonnummerArbeid-datadef-6700 orid="6700">' . $data['D6700'] . '</OppgavegiverTelefonnummerArbeid-datadef-6700>
		<OppgavegiverSivilStatus-datadef-520 orid="520">' . $data['D520'] . '</OppgavegiverSivilStatus-datadef-520>
	</Personalia-grp-250>
	<Ektefelle-grp-87 gruppeid="87">
		<EktefelleNavn-datadef-2272 orid="2272">' . $data['D2272'] . '</EktefelleNavn-datadef-2272>
		<EktefelleFodselsnummer-datadef-19372 orid="19372">' . $data['D19372'] . '</EktefelleFodselsnummer-datadef-19372>
		<EktefelleInntektFordeling-datadef-14086 orid="14086">' . $data['D14086'] . '</EktefelleInntektFordeling-datadef-14086>
		<Ekteskap-grp-3607 gruppeid="3607">
			<EkteskapDato-datadef-6702 orid="6702">' . $data['D6702'] . '</EkteskapDato-datadef-6702>
			<EktefelleNavnForEkteskap-datadef-6703 orid="6703">' . $data['D6703'] . '</EktefelleNavnForEkteskap-datadef-6703>
			<EktefelleBoligadresseForEkteskap-datadef-6704 orid="6704">' . $data['D6704'] . '</EktefelleBoligadresseForEkteskap-datadef-6704>
			<EktefellePostnummerForEkteskap-datadef-6705 orid="6705">' . $data['D6705'] . '</EktefellePostnummerForEkteskap-datadef-6705>
			<EktefellePoststedForEkteskap-datadef-6706 orid="6706">' . $data['D6706'] . '</EktefellePoststedForEkteskap-datadef-6706>
			<EktefelleSkattekommuneForEkteskap-datadef-6707 orid="6707">' . $data['D6707'] . '</EktefelleSkattekommuneForEkteskap-datadef-6707>
		</Ekteskap-grp-3607>
	</Ektefelle-grp-87>
	<SamboerMedFellesBarn-grp-252 gruppeid="252">
		<SamboerNavn-datadef-6708 orid="6708">' . $data['D6708'] . '</SamboerNavn-datadef-6708>
		<SamboerFodselsnummer-datadef-6709 orid="6709">' . $data['D6709'] . '</SamboerFodselsnummer-datadef-6709>
	</SamboerMedFellesBarn-grp-252>
</GenerellInformasjon-grp-763>
<BarnSomDuForsorger-grp-256 gruppeid="256">
	<BarnAdoptivbarnPleieOgFosterbarnSomEr17ArEllerYngre-grp-587 gruppeid="587">
		<BarnNavnSpesifisertBarn-datadef-2728 orid="2728">' . $data['D2728'] . '</BarnNavnSpesifisertBarn-datadef-2728>
		<BarnFodselsdatoSpesifisertBarn-datadef-19405 orid="19405">' . $data['D19405'] . '</BarnFodselsdatoSpesifisertBarn-datadef-19405>
	</BarnAdoptivbarnPleieOgFosterbarnSomEr17ArEllerYngre-grp-587>
	<BarnAdoptivbarnPleiebarnOgFosterbarnSomEr18ArEllerEldre-grp-257 gruppeid="257">
		<BarnForsorgerfradragNavnSpesifisertBarn-datadef-6710 orid="6710">' . $data['D6710'] . '</BarnForsorgerfradragNavnSpesifisertBarn-datadef-6710>
		<BarnForsorgerfradragFodselsdatoSpesifisertBarn-datadef-19406 orid="19406">' . $data['D19406'] . '</BarnForsorgerfradragFodselsdatoSpesifisertBarn-datadef-19406>
		<BarnForsorgelseTidsromSpesifisertBarn-datadef-2729 orid="2729">' . $data['D2729'] . '</BarnForsorgelseTidsromSpesifisertBarn-datadef-2729>
	</BarnAdoptivbarnPleiebarnOgFosterbarnSomEr18ArEllerEldre-grp-257>
</BarnSomDuForsorger-grp-256>
<AndreOpplysninger-grp-260 gruppeid="260">
	<BSU-datadef-2733 orid="2733">' . $data['D2733'] . '</BSU-datadef-2733>
	<GevinstLotteriOL-datadef-6713 orid="6713">' . $data['D6713'] . '</GevinstLotteriOL-datadef-6713>
	<GevinstLotteriOL-datadef-2734 orid="2734">' . $data['D2734'] . '</GevinstLotteriOL-datadef-2734>
	<GaveArvMottatt-datadef-6714 orid="6714">' . $data['D6714'] . '</GaveArvMottatt-datadef-6714>
	<GaveArvMottatt-datadef-2735 orid="2735">' . $data['D2735'] . '</GaveArvMottatt-datadef-2735>
	<AksjeutbytteGodtgjorelsesfradragUbenyttet-datadef-2722 orid="2722">' . $data['D2722'] . '</AksjeutbytteGodtgjorelsesfradragUbenyttet-datadef-2722>
	<KostnaderForskningsUtviklingsprosjekterSkattefradrag-datadef-17033 orid="17033">' . $data['D17033'] . '</KostnaderForskningsUtviklingsprosjekterSkattefradrag-datadef-17033>
	<EiendomNaringKommune-datadef-17031 orid="17031">' . $data['D17031'] . '</EiendomNaringKommune-datadef-17031>
	<Kommune-grp-3522 gruppeid="3522">
		<EiendomNaringAnnenKommuneNavn-datadef-19785 orid="19785">' . $data['D19785'] . '</EiendomNaringAnnenKommuneNavn-datadef-19785>
		<EiendomNaringAnnenKommuneKommunenummerSpesifisertEiendomMv-datadef-17034 orid="17034">' . $data['D17034'] . '</EiendomNaringAnnenKommuneKommunenummerSpesifisertEiendomMv-datadef-17034>
	</Kommune-grp-3522>
	<FormueUtland-datadef-1117 orid="1117">' . $data['D1117'] . '</FormueUtland-datadef-1117>
	<Kreditfradrag-datadef-6680 orid="6680">' . $data['D6680'] . '</Kreditfradrag-datadef-6680>
	<LonnUtlandSkattenedsettelse-datadef-17032 orid="17032">' . $data['D17032'] . '</LonnUtlandSkattenedsettelse-datadef-17032>
	<SkattInnbetaltTilbakebetalingKontonummer-datadef-19784 orid="19784">' . $data['D19784'] . '</SkattInnbetaltTilbakebetalingKontonummer-datadef-19784>
	<NaturressursskattUbenyttet-datadef-6667 orid="6667">' . $data['D6667'] . '</NaturressursskattUbenyttet-datadef-6667>
	<Malform-datadef-2736 orid="2736">' . $data['D2736'] . '</Malform-datadef-2736>
</AndreOpplysninger-grp-260>
<PersoninntektForetak-grp-265 gruppeid="265">
	<BeregnetPersoninntektFraForetakOgSykepengerMv-grp-2119 gruppeid="2119">
		<FiskeJordOgSkogbrukPelsOgReindrift-grp-621 gruppeid="621">
			<PersoninntektEnkeltmannsforetakFiskeJordbrukSkogbruk-datadef-6716 orid="6716">' . $data['D6716'] . '</PersoninntektEnkeltmannsforetakFiskeJordbrukSkogbruk-datadef-6716>
			<PersoninntektDeltakerlignetDelingsforetakFiskeJordbrukSkogbruk-datadef-2738 orid="2738">' . $data['D2738'] . '</PersoninntektDeltakerlignetDelingsforetakFiskeJordbrukSkogbruk-datadef-2738>
			<GodtgjorelseDeltakerlignetDelingsforetakFiskeJordbrukSkog-datadef-2718 orid="2718">' . $data['D2718'] . '</GodtgjorelseDeltakerlignetDelingsforetakFiskeJordbrukSkog-datadef-2718>
			<SykepengerFiskeJordbrukSkogbruk-datadef-6724 orid="6724">' . $data['D6724'] . '</SykepengerFiskeJordbrukSkogbruk-datadef-6724>
			<AndelAksjeselskapDeltakerlignetSelskapFiskeJordbrukSkogbruk-datadef-2741 orid="2741">' . $data['D2741'] . '</AndelAksjeselskapDeltakerlignetSelskapFiskeJordbrukSkogbruk-datadef-2741>
		</FiskeJordOgSkogbrukPelsOgReindrift-grp-621>
		<LiberaltYrke-grp-622 gruppeid="622">
			<PersoninntektEnkeltmannsforetakLiberaltYrke-datadef-6717 orid="6717">' . $data['D6717'] . '</PersoninntektEnkeltmannsforetakLiberaltYrke-datadef-6717>
			<PersoninntektDeltakerlignetDelingsforetakLiberaltYrke-datadef-6719 orid="6719">' . $data['D6719'] . '</PersoninntektDeltakerlignetDelingsforetakLiberaltYrke-datadef-6719>
			<GodtgjorelseDeltakerlignetDelingsforetakLiberaltYrke-datadef-6721 orid="6721">' . $data['D6721'] . '</GodtgjorelseDeltakerlignetDelingsforetakLiberaltYrke-datadef-6721>
			<PersoninntektAksjeselskapLiberaltYrke-datadef-2739 orid="2739">' . $data['D2739'] . '</PersoninntektAksjeselskapLiberaltYrke-datadef-2739>
			<SykepengerLiberaltYrke-datadef-6725 orid="6725">' . $data['D6725'] . '</SykepengerLiberaltYrke-datadef-6725>
			<AndelAksjeselskapDeltakerlignetSelskapLiberaltYrke-datadef-6727 orid="6727">' . $data['D6727'] . '</AndelAksjeselskapDeltakerlignetSelskapLiberaltYrke-datadef-6727>
		</LiberaltYrke-grp-622>
		<AnnenNaring-grp-623 gruppeid="623">
			<PersoninntektEnkeltmannsforetakAnnenNaring-datadef-6718 orid="6718">' . $data['D6718'] . '</PersoninntektEnkeltmannsforetakAnnenNaring-datadef-6718>
			<PersoninntektDeltakerlignetDelingsforetakAnnenNaring-datadef-6720 orid="6720">' . $data['D6720'] . '</PersoninntektDeltakerlignetDelingsforetakAnnenNaring-datadef-6720>
			<GodtgjorelseDeltakerlignetDelingsforetakAnnenNaring-datadef-6722 orid="6722">' . $data['D6722'] . '</GodtgjorelseDeltakerlignetDelingsforetakAnnenNaring-datadef-6722>
			<PersoninntektAksjeselskapAnnenNaring-datadef-6723 orid="6723">' . $data['D6723'] . '</PersoninntektAksjeselskapAnnenNaring-datadef-6723>
			<SykepengerAnnenNaring-datadef-6726 orid="6726">' . $data['D6726'] . '</SykepengerAnnenNaring-datadef-6726>
			<AndelAksjeselskapDeltakerlignetSelskapAnnenNaring-datadef-6728 orid="6728">' . $data['D6728'] . '</AndelAksjeselskapDeltakerlignetSelskapAnnenNaring-datadef-6728>
		</AnnenNaring-grp-623>
	</BeregnetPersoninntektFraForetakOgSykepengerMv-grp-2119>
</PersoninntektForetak-grp-265>
<GodtgjorelseForArbeidTilDeltakerIDeltakerliknetSelskapHvorDet-grp-266 gruppeid="266">
	<GodtgjorelseDeltakerlignetSelskapFiskeJordbrukSkogbruk-datadef-7198 orid="7198">' . $data['D7198'] . '</GodtgjorelseDeltakerlignetSelskapFiskeJordbrukSkogbruk-datadef-7198>
	<GodtgjorelseDeltakerlignetSelskapAnnenNaring-datadef-6729 orid="6729">' . $data['D6729'] . '</GodtgjorelseDeltakerlignetSelskapAnnenNaring-datadef-6729>
</GodtgjorelseForArbeidTilDeltakerIDeltakerliknetSelskapHvorDet-grp-266>
<PersoninntektLonnNaturalytelserMv-grp-267 gruppeid="267">
	<LonnHonorarerOgAnnenGodtgjorelse-grp-3375 gruppeid="3375">
		<ArbeidsgiverNavnSpesifisert-datadef-6730 orid="6730">' . $data['D6730'] . '</ArbeidsgiverNavnSpesifisert-datadef-6730>
		<ArbeidTidsromSpesifisertArbeidsgiver-datadef-4186 orid="4186">' . $data['D4186'] . '</ArbeidTidsromSpesifisertArbeidsgiver-datadef-4186>
		<LonnAnnenGodtgjorelseSpesifisertUtbetaler-datadef-6731 orid="6731">' . $data['D6731'] . '</LonnAnnenGodtgjorelseSpesifisertUtbetaler-datadef-6731>
	</LonnHonorarerOgAnnenGodtgjorelse-grp-3375>
	<LonnAnnenGodtgjorelse-datadef-1123 orid="1123">' . $data['D1123'] . '</LonnAnnenGodtgjorelse-datadef-1123>
	<SarskiltFradragForSjofolk-grp-2121 gruppeid="2121">
		<ArbeidsgiverMaritimVirksomhet-datadef-7691 orid="7691">' . $data['D7691'] . '</ArbeidsgiverMaritimVirksomhet-datadef-7691>
		<InntektSjomannsfradragTidsrom-datadef-6732 orid="6732">' . $data['D6732'] . '</InntektSjomannsfradragTidsrom-datadef-6732>
		<InntekterSjomannsfradrag-datadef-2742 orid="2742">' . $data['D2742'] . '</InntekterSjomannsfradrag-datadef-2742>
	</SarskiltFradragForSjofolk-grp-2121>
	<InntekterSjomannsfradragSum-datadef-22169 orid="22169">' . $data['D22169'] . '</InntekterSjomannsfradragSum-datadef-22169>
	<Barnepass-grp-3624 gruppeid="3624">
		<ArbeidsgiverBarnepass-datadef-7692 orid="7692">' . $data['D7692'] . '</ArbeidsgiverBarnepass-datadef-7692>
		<InntektBarnepassTidsrom-datadef-6733 orid="6733">' . $data['D6733'] . '</InntektBarnepassTidsrom-datadef-6733>
		<InntekterBarnepass-datadef-2743 orid="2743">' . $data['D2743'] . '</InntekterBarnepass-datadef-2743>
	</Barnepass-grp-3624>
	<InntekterBarnepassSum-datadef-22170 orid="22170">' . $data['D22170'] . '</InntekterBarnepassSum-datadef-22170>
	<GodtgjorelseTilDekningAvTjenestekostnader-grp-3625 gruppeid="3625">
		<UtbetalerGodtgjorelserTjenesteutgifter-datadef-7693 orid="7693">' . $data['D7693'] . '</UtbetalerGodtgjorelserTjenesteutgifter-datadef-7693>
		<GodtgjorelseTjenesteutgifterOverskudd-datadef-2744 orid="2744">' . $data['D2744'] . '</GodtgjorelseTjenesteutgifterOverskudd-datadef-2744>
	</GodtgjorelseTilDekningAvTjenestekostnader-grp-3625>
	<GodtgjorelseTjenesteutgifterOverskuddSum-datadef-22171 orid="22171">' . $data['D22171'] . '</GodtgjorelseTjenesteutgifterOverskuddSum-datadef-22171>
	<AnnenArbeidsinntekt-grp-3626 gruppeid="3626">
		<ArbeidsinntektAnnenBeskrivelse-datadef-7694 orid="7694">' . $data['D7694'] . '</ArbeidsinntektAnnenBeskrivelse-datadef-7694>
		<ArbeidsinntektAnnenTidsrom-datadef-6735 orid="6735">' . $data['D6735'] . '</ArbeidsinntektAnnenTidsrom-datadef-6735>
		<ArbeidsinntektAnnen-datadef-2517 orid="2517">' . $data['D2517'] . '</ArbeidsinntektAnnen-datadef-2517>
	</AnnenArbeidsinntekt-grp-3626>
	<ArbeidsinntektAnnenSum-datadef-22172 orid="22172">' . $data['D22172'] . '</ArbeidsinntektAnnenSum-datadef-22172>
	<LonnNaturalytelserMv-datadef-6736 orid="6736">' . $data['D6736'] . '</LonnNaturalytelserMv-datadef-6736>
	<Dagpenger-grp-3628 gruppeid="3628">
		<DagpengerArbeidsledighetTidsrom-datadef-6737 orid="6737">' . $data['D6737'] . '</DagpengerArbeidsledighetTidsrom-datadef-6737>
		<DagpengerArbeidsledighet-datadef-2745 orid="2745">' . $data['D2745'] . '</DagpengerArbeidsledighet-datadef-2745>
	</Dagpenger-grp-3628>
</PersoninntektLonnNaturalytelserMv-grp-267>
<PersoninntektEgnePensjonerLivrenterIArbeidsforholdMv-grp-652 gruppeid="652">
	<PensjonFraFolketrygden-grp-3381 gruppeid="3381">
		<PensjonType-datadef-19407 orid="19407">' . $data['D19407'] . '</PensjonType-datadef-19407>
		<PensjonTidsromSpesifisertPensjon-datadef-6739 orid="6739">' . $data['D6739'] . '</PensjonTidsromSpesifisertPensjon-datadef-6739>
		<PensjonerFolketrygden-datadef-2746 orid="2746">' . $data['D2746'] . '</PensjonerFolketrygden-datadef-2746>
	</PensjonFraFolketrygden-grp-3381>
	<PensjonerFraAndre-grp-3382 gruppeid="3382">
		<UtbetalerPensjonerSpesifisertUtbetaler-datadef-6740 orid="6740">' . $data['D6740'] . '</UtbetalerPensjonerSpesifisertUtbetaler-datadef-6740>
		<Barnepensjon-datadef-6741 orid="6741">' . $data['D6741'] . '</Barnepensjon-datadef-6741>
		<PensjonerIkkeFolketrygdenTidsrom-datadef-6742 orid="6742">' . $data['D6742'] . '</PensjonerIkkeFolketrygdenTidsrom-datadef-6742>
		<PensjonerIkkeFolketrygden-datadef-2747 orid="2747">' . $data['D2747'] . '</PensjonerIkkeFolketrygden-datadef-2747>
	</PensjonerFraAndre-grp-3382>
	<Pensjoner-datadef-2518 orid="2518">' . $data['D2518'] . '</Pensjoner-datadef-2518>
	<Ektefelletillegg-grp-3388 gruppeid="3388">
		<EktefelletilleggTidsrom-datadef-6743 orid="6743">' . $data['D6743'] . '</EktefelletilleggTidsrom-datadef-6743>
		<Ektefelletillegg-datadef-2689 orid="2689">' . $data['D2689'] . '</Ektefelletillegg-datadef-2689>
	</Ektefelletillegg-grp-3388>
	<LonnPensjonerMV-datadef-7670 orid="7670">' . $data['D7670'] . '</LonnPensjonerMV-datadef-7670>
</PersoninntektEgnePensjonerLivrenterIArbeidsforholdMv-grp-652>
<PersoninntektBarnsLonnsinntekt-grp-2126 gruppeid="2126">
	<BarnsLonnsinntekt-grp-275 gruppeid="275">
		<UtbetalerLonnBarn-datadef-6749 orid="6749">' . $data['D6749'] . '</UtbetalerLonnBarn-datadef-6749>
		<BarnLonnFodselsnummer-datadef-6750 orid="6750">' . $data['D6750'] . '</BarnLonnFodselsnummer-datadef-6750>
		<BarnLonn-datadef-2693 orid="2693">' . $data['D2693'] . '</BarnLonn-datadef-2693>
	</BarnsLonnsinntekt-grp-275>
	<BarnLonnSum-datadef-22173 orid="22173">' . $data['D22173'] . '</BarnLonnSum-datadef-22173>
	<LonnPensjonerInklBarnsInntekt-datadef-14087 orid="14087">' . $data['D14087'] . '</LonnPensjonerInklBarnsInntekt-datadef-14087>
</PersoninntektBarnsLonnsinntekt-grp-2126>
<AlminneligInntektBidragLivrenterUtenforArbeidsforholdBarnepe-grp-768 gruppeid="768">
	<SkattepliktigeBidrag-grp-3399 gruppeid="3399">
		<UtbetalerBidragUtenforArbeidsforholdSpesifisertUtbetaler-datadef-6752 orid="6752">' . $data['D6752'] . '</UtbetalerBidragUtenforArbeidsforholdSpesifisertUtbetaler-datadef-6752>
		<BidragEgneUtenforArbeidsforhold-datadef-6753 orid="6753">' . $data['D6753'] . '</BidragEgneUtenforArbeidsforhold-datadef-6753>
	</SkattepliktigeBidrag-grp-3399>
	<BidragAnnet-grp-3400 gruppeid="3400">
		<UtbetalerBidragAndre-datadef-7696 orid="7696">' . $data['D7696'] . '</UtbetalerBidragAndre-datadef-7696>
		<BidragAndreUtenforArbeidsforhold-datadef-6756 orid="6756">' . $data['D6756'] . '</BidragAndreUtenforArbeidsforhold-datadef-6756>
	</BidragAnnet-grp-3400>
	<Barnepensjon-grp-3738 gruppeid="3738">
		<UtbetalerBarnepensjon-datadef-7697 orid="7697">' . $data['D7697'] . '</UtbetalerBarnepensjon-datadef-7697>
		<BarnPensjonFodselsnummer-datadef-6757 orid="6757">' . $data['D6757'] . '</BarnPensjonFodselsnummer-datadef-6757>
		<Barnepensjon-datadef-6758 orid="6758">' . $data['D6758'] . '</Barnepensjon-datadef-6758>
	</Barnepensjon-grp-3738>
	<BidragMottatt-datadef-2694 orid="2694">' . $data['D2694'] . '</BidragMottatt-datadef-2694>
</AlminneligInntektBidragLivrenterUtenforArbeidsforholdBarnepe-grp-768>
<AlminneligInntektNaringsinntekter-grp-4020 gruppeid="4020">
	<Naringsinntekter-grp-278 gruppeid="278">
		<InntekterJordbrukGartneriMv-datadef-2211 orid="2211">' . $data['D2211'] . '</InntekterJordbrukGartneriMv-datadef-2211>
		<InntekterSkogbruk-datadef-2212 orid="2212">' . $data['D2212'] . '</InntekterSkogbruk-datadef-2212>
		<InntekterFiskeriSkattemessig-datadef-6681 orid="6681">' . $data['D6681'] . '</InntekterFiskeriSkattemessig-datadef-6681>
		<NaringsinntekterAnnenNaring-datadef-22174 orid="22174">' . $data['D22174'] . '</NaringsinntekterAnnenNaring-datadef-22174>
		<InntektNaringSum-datadef-10085 orid="10085">' . $data['D10085'] . '</InntektNaringSum-datadef-10085>
	</Naringsinntekter-grp-278>
	<SykepengerNaring-datadef-11259 orid="11259">' . $data['D11259'] . '</SykepengerNaring-datadef-11259>
</AlminneligInntektNaringsinntekter-grp-4020>
<AlminneligInntektInntektAvBoligOgAnnenFastEiendom-grp-280 gruppeid="280">
	<InntektAvBoligeiendom-grp-3523 gruppeid="3523">
		<EiendomBoligKommunenummer-datadef-17040 orid="17040">' . $data['D17040'] . '</EiendomBoligKommunenummer-datadef-17040>
		<BoligeiendomLigningsverdi0451000SpesifisertVerdi-datadef-22185 orid="22185">' . $data['D22185'] . '</BoligeiendomLigningsverdi0451000SpesifisertVerdi-datadef-22185>
		<InntekterBoligeiendomProsentligningLigningsverdi0451000-datadef-22186 orid="22186">' . $data['D22186'] . '</InntekterBoligeiendomProsentligningLigningsverdi0451000-datadef-22186>
		<BoligeiendomLigningsverdiUtover451000SpesifisertVerdi-datadef-6760 orid="6760">' . $data['D6760'] . '</BoligeiendomLigningsverdiUtover451000SpesifisertVerdi-datadef-6760>
		<InntekterBoligeiendomBeregnetSpesifisertVerdi-datadef-6761 orid="6761">' . $data['D6761'] . '</InntekterBoligeiendomBeregnetSpesifisertVerdi-datadef-6761>
	</InntektAvBoligeiendom-grp-3523>
	<InntekterBoligeiendom-datadef-6762 orid="6762">' . $data['D6762'] . '</InntekterBoligeiendom-datadef-6762>
	<InntektAvFritidseiendom-grp-3524 gruppeid="3524">
		<EiendomFritidsboligKommunenummer-datadef-17039 orid="17039">' . $data['D17039'] . '</EiendomFritidsboligKommunenummer-datadef-17039>
		<FritidseiendomLigningsverdi0451000SpesifisertVerdi-datadef-22187 orid="22187">' . $data['D22187'] . '</FritidseiendomLigningsverdi0451000SpesifisertVerdi-datadef-22187>
		<InntekterFritidseiendomProsentligningLigningsverdi0451000-datadef-22188 orid="22188">' . $data['D22188'] . '</InntekterFritidseiendomProsentligningLigningsverdi0451000-datadef-22188>
		<FritidseiendomLigningsverdiUtover451000SpesifisertVerdi-datadef-6764 orid="6764">' . $data['D6764'] . '</FritidseiendomLigningsverdiUtover451000SpesifisertVerdi-datadef-6764>
		<InntekterFritidseiendomBeregnetSpesifisertVerdi-datadef-6765 orid="6765">' . $data['D6765'] . '</InntekterFritidseiendomBeregnetSpesifisertVerdi-datadef-6765>
	</InntektAvFritidseiendom-grp-3524>
	<InntekterFritidseiendom-datadef-6766 orid="6766">' . $data['D6766'] . '</InntekterFritidseiendom-datadef-6766>
	<InntekterBoligselskapBoligsameieAndel-datadef-6767 orid="6767">' . $data['D6767'] . '</InntekterBoligselskapBoligsameieAndel-datadef-6767>
	<InntektVedUtleieAvFastEiendom-grp-3525 gruppeid="3525">
		<EiendomUtenomNaringKommunenummer-datadef-17035 orid="17035">' . $data['D17035'] . '</EiendomUtenomNaringKommunenummer-datadef-17035>
		<InntekterUtleieFastEiendomNetto-datadef-6768 orid="6768">' . $data['D6768'] . '</InntekterUtleieFastEiendomNetto-datadef-6768>
	</InntektVedUtleieAvFastEiendom-grp-3525>
	<InntekterUtleieFastEiendomNettoSum-datadef-22175 orid="22175">' . $data['D22175'] . '</InntekterUtleieFastEiendomNettoSum-datadef-22175>
	<GevinstVedRealisasjon-grp-3528 gruppeid="3528">
		<EiendomRealisertKommunenummer-datadef-17036 orid="17036">' . $data['D17036'] . '</EiendomRealisertKommunenummer-datadef-17036>
		<GevinstRealisasjonFastEiendom-datadef-6771 orid="6771">' . $data['D6771'] . '</GevinstRealisasjonFastEiendom-datadef-6771>
	</GevinstVedRealisasjon-grp-3528>
	<GevinstRealisasjonFastEiendomSum-datadef-22176 orid="22176">' . $data['D22176'] . '</GevinstRealisasjonFastEiendomSum-datadef-22176>
	<InntektAvFastEiendomIUtlandet-grp-3533 gruppeid="3533">
		<FastEiendomUtlandLandSpesifisertEiendom-datadef-14105 orid="14105">' . $data['D14105'] . '</FastEiendomUtlandLandSpesifisertEiendom-datadef-14105>
		<InntekterFastEiendomUtlandSpesifisertEiendom-datadef-14211 orid="14211">' . $data['D14211'] . '</InntekterFastEiendomUtlandSpesifisertEiendom-datadef-14211>
	</InntektAvFastEiendomIUtlandet-grp-3533>
	<InntektFastEiendomUtlandSum-datadef-22203 orid="22203">' . $data['D22203'] . '</InntektFastEiendomUtlandSum-datadef-22203>
	<InntekterFastEiendomSkattemessig-datadef-6772 orid="6772">' . $data['D6772'] . '</InntekterFastEiendomSkattemessig-datadef-6772>
	<AlminneligInntektUtdrag-datadef-6773 orid="6773">' . $data['D6773'] . '</AlminneligInntektUtdrag-datadef-6773>
</AlminneligInntektInntektAvBoligOgAnnenFastEiendom-grp-280>
<AlminneligInntektKapitalinntekterOgAndreInntekter-grp-281 gruppeid="281">
	<Renteinntekter-grp-670 gruppeid="670">
		<RenteinntekterBankNavnSpesifisertBank-datadef-6774 orid="6774">' . $data['D6774'] . '</RenteinntekterBankNavnSpesifisertBank-datadef-6774>
		<RenteinntekterSpesifisertBank-datadef-6775 orid="6775">' . $data['D6775'] . '</RenteinntekterSpesifisertBank-datadef-6775>
	</Renteinntekter-grp-670>
	<RenteinntekterNaringInnenlandske-datadef-16537 orid="16537">' . $data['D16537'] . '</RenteinntekterNaringInnenlandske-datadef-16537>
	<RenteinntekterAvAnnet-grp-3543 gruppeid="3543">
		<RenteinntekterAnnetBeskrivelse-datadef-7698 orid="7698">' . $data['D7698'] . '</RenteinntekterAnnetBeskrivelse-datadef-7698>
		<RenteinntekterAnnet-datadef-6776 orid="6776">' . $data['D6776'] . '</RenteinntekterAnnet-datadef-6776>
	</RenteinntekterAvAnnet-grp-3543>
	<RenteinntekterAnnetSum-datadef-22177 orid="22177">' . $data['D22177'] . '</RenteinntekterAnnetSum-datadef-22177>
	<InntektFraAksjerOgAndreInntekter-grp-672 gruppeid="672">
		<LivsforsikringAvkastningSkattemessig-datadef-1108 orid="1108">' . $data['D1108'] . '</LivsforsikringAvkastningSkattemessig-datadef-1108>
		<AksjeutbytteNorskeSelskaper-datadef-19989 orid="19989">' . $data['D19989'] . '</AksjeutbytteNorskeSelskaper-datadef-19989>
		<AksjerMvGevinstSkattNaring-datadef-15522 orid="15522">' . $data['D15522'] . '</AksjerMvGevinstSkattNaring-datadef-15522>
		<InntekterUtland-datadef-2695 orid="2695">' . $data['D2695'] . '</InntekterUtland-datadef-2695>
		<AksjeutbytteUtenlandskeSelskaperSkattemessig-datadef-1109 orid="1109">' . $data['D1109'] . '</AksjeutbytteUtenlandskeSelskaperSkattemessig-datadef-1109>
		<AndreInntekter-grp-3552 gruppeid="3552">
			<InntekterAndreSkattemessigBeskrivelseSpesifisert-datadef-13970 orid="13970">' . $data['D13970'] . '</InntekterAndreSkattemessigBeskrivelseSpesifisert-datadef-13970>
			<InntekterAndreSkattemessigSpesifisert-datadef-14214 orid="14214">' . $data['D14214'] . '</InntekterAndreSkattemessigSpesifisert-datadef-14214>
		</AndreInntekter-grp-3552>
		<InntekterAndreSkattemessigSum-datadef-22204 orid="22204">' . $data['D22204'] . '</InntekterAndreSkattemessigSum-datadef-22204>
		<KapitalinntekterMV-datadef-1214 orid="1214">' . $data['D1214'] . '</KapitalinntekterMV-datadef-1214>
	</InntektFraAksjerOgAndreInntekter-grp-672>
	<InntektAlminneligForFradrag-datadef-6778 orid="6778">' . $data['D6778'] . '</InntektAlminneligForFradrag-datadef-6778>
</AlminneligInntektKapitalinntekterOgAndreInntekter-grp-281>
<FradragITilknytningTilArbeidsinntektMv-grp-769 gruppeid="769">
	<MinstefradragEgenInntekt-datadef-2696 orid="2696">' . $data['D2696'] . '</MinstefradragEgenInntekt-datadef-2696>
	<FradragFaktiskeUtgifter-datadef-6777 orid="6777">' . $data['D6777'] . '</FradragFaktiskeUtgifter-datadef-6777>
	<MinstefradragEgenInntektFaktiskeUtgifter-datadef-22189 orid="22189">' . $data['D22189'] . '</MinstefradragEgenInntektFaktiskeUtgifter-datadef-22189>
	<FradragEktefelletillegg-datadef-11260 orid="11260">' . $data['D11260'] . '</FradragEktefelletillegg-datadef-11260>
	<FradragBarnLonn-datadef-6780 orid="6780">' . $data['D6780'] . '</FradragBarnLonn-datadef-6780>
	<FradragBarnebidragBarnepensjon-datadef-6781 orid="6781">' . $data['D6781'] . '</FradragBarnebidragBarnepensjon-datadef-6781>
	<MinstefradragEktefelletilleggBarnLonnBarnepensjon-datadef-22190 orid="22190">' . $data['D22190'] . '</MinstefradragEktefelletilleggBarnLonnBarnepensjon-datadef-22190>
	<FradragKostLosjiMerutgifter-datadef-6782 orid="6782">' . $data['D6782'] . '</FradragKostLosjiMerutgifter-datadef-6782>
	<Reiseutgifter-grp-3555 gruppeid="3555">
		<ReiseHjemArbeid-grp-3553 gruppeid="3553">
			<ReiseHjemArbeidDager-datadef-6783 orid="6783">' . $data['D6783'] . '</ReiseHjemArbeidDager-datadef-6783>
			<ReiseHjemArbeidAvstand-datadef-6785 orid="6785">' . $data['D6785'] . '</ReiseHjemArbeidAvstand-datadef-6785>
			<ReiseutgifterHjemArbeid-datadef-2723 orid="2723">' . $data['D2723'] . '</ReiseutgifterHjemArbeid-datadef-2723>
		</ReiseHjemArbeid-grp-3553>
		<BesoksreiseTilHjemmet-grp-3554 gruppeid="3554">
			<ReiseBesokHjemDager-datadef-6784 orid="6784">' . $data['D6784'] . '</ReiseBesokHjemDager-datadef-6784>
			<ReiseHjemAvstand-datadef-7668 orid="7668">' . $data['D7668'] . '</ReiseHjemAvstand-datadef-7668>
			<ReiseutgifterBesokHjem-datadef-6786 orid="6786">' . $data['D6786'] . '</ReiseutgifterBesokHjem-datadef-6786>
		</BesoksreiseTilHjemmet-grp-3554>
		<BompengerFergeutgifter-datadef-7669 orid="7669">' . $data['D7669'] . '</BompengerFergeutgifter-datadef-7669>
		<ReiseutgifterGrunnlag-datadef-6787 orid="6787">' . $data['D6787'] . '</ReiseutgifterGrunnlag-datadef-6787>
		<ReiseutgifterBeregnet-datadef-6788 orid="6788">' . $data['D6788'] . '</ReiseutgifterBeregnet-datadef-6788>
	</Reiseutgifter-grp-3555>
	<Foreldrefradrag-grp-3556 gruppeid="3556">
		<ForeldrefradragBeskrivelse-datadef-14108 orid="14108">' . $data['D14108'] . '</ForeldrefradragBeskrivelse-datadef-14108>
		<Foreldrefradrag-datadef-2698 orid="2698">' . $data['D2698'] . '</Foreldrefradrag-datadef-2698>
	</Foreldrefradrag-grp-3556>
	<ForeldrefradragSumInnbetalt-datadef-21367 orid="21367">' . $data['D21367'] . '</ForeldrefradragSumInnbetalt-datadef-21367>
	<KontingentFagforeningGaverFradragsberettiget-datadef-2271 orid="2271">' . $data['D2271'] . '</KontingentFagforeningGaverFradragsberettiget-datadef-2271>
	<PensjonsordningPremieTilskudd-datadef-2699 orid="2699">' . $data['D2699'] . '</PensjonsordningPremieTilskudd-datadef-2699>
	<Sjomannsfradrag-datadef-2700 orid="2700">' . $data['D2700'] . '</Sjomannsfradrag-datadef-2700>
	<FradragSarskiltFiskeMv-datadef-1353 orid="1353">' . $data['D1353'] . '</FradragSarskiltFiskeMv-datadef-1353>
	<FradragSjomennFiskeJordbruk-datadef-7671 orid="7671">' . $data['D7671'] . '</FradragSjomennFiskeJordbruk-datadef-7671>
	<Jordbruksfradrag-datadef-11298 orid="11298">' . $data['D11298'] . '</Jordbruksfradrag-datadef-11298>
	<Reindriftsfradrag-datadef-19771 orid="19771">' . $data['D19771'] . '</Reindriftsfradrag-datadef-19771>
	<Skiferfradrag-datadef-19775 orid="19775">' . $data['D19775'] . '</Skiferfradrag-datadef-19775>
	<PremieTilleggstrygdSkattemessig-datadef-308 orid="308">' . $data['D308'] . '</PremieTilleggstrygdSkattemessig-datadef-308>
	<Underskudd-grp-5132 gruppeid="5132">
		<UnderskuddJordbruk-datadef-22178 orid="22178">' . $data['D22178'] . '</UnderskuddJordbruk-datadef-22178>
		<UnderskuddSkogbruk-datadef-22179 orid="22179">' . $data['D22179'] . '</UnderskuddSkogbruk-datadef-22179>
		<UnderskuddFiske-datadef-21072 orid="21072">' . $data['D21072'] . '</UnderskuddFiske-datadef-21072>
		<UnderskuddAnnenNaring-datadef-22180 orid="22180">' . $data['D22180'] . '</UnderskuddAnnenNaring-datadef-22180>
		<UnderskuddEiendomUtenforNaring-datadef-22181 orid="22181">' . $data['D22181'] . '</UnderskuddEiendomUtenforNaring-datadef-22181>
		<UnderskuddNaringFordelt-datadef-16174 orid="16174">' . $data['D16174'] . '</UnderskuddNaringFordelt-datadef-16174>
	</Underskudd-grp-5132>
</FradragITilknytningTilArbeidsinntektMv-grp-769>
<FradragKapitalkostnaderOgAndreFradrag-grp-299 gruppeid="299">
	<Renteutgifter-grp-3558 gruppeid="3558">
		<FordringshaverNavnSpesifisertFordringshaver-datadef-6789 orid="6789">' . $data['D6789'] . '</FordringshaverNavnSpesifisertFordringshaver-datadef-6789>
		<FordringshaverAdresseSpesifisertFordringshaver-datadef-6790 orid="6790">' . $data['D6790'] . '</FordringshaverAdresseSpesifisertFordringshaver-datadef-6790>
		<FordringshaverPostnummerSpesifisertFordringshaver-datadef-6791 orid="6791">' . $data['D6791'] . '</FordringshaverPostnummerSpesifisertFordringshaver-datadef-6791>
		<FordringshaverPoststedSpesifisertFordringshaver-datadef-6792 orid="6792">' . $data['D6792'] . '</FordringshaverPoststedSpesifisertFordringshaver-datadef-6792>
		<RenteutgifterNorskeFordringshavereSpesifisertFordringshaver-datadef-6793 orid="6793">' . $data['D6793'] . '</RenteutgifterNorskeFordringshavereSpesifisertFordringshaver-datadef-6793>
	</Renteutgifter-grp-3558>
	<RenteutgifterNorskeFordringshavere-datadef-2701 orid="2701">' . $data['D2701'] . '</RenteutgifterNorskeFordringshavere-datadef-2701>
	<RenteutgifterUtenlandskeFordringshavere-datadef-2702 orid="2702">' . $data['D2702'] . '</RenteutgifterUtenlandskeFordringshavere-datadef-2702>
	<Underholdsbidrag-grp-3559 gruppeid="3559">
		<BidragsmottakerNavnSpesifisertMottaker-datadef-6794 orid="6794">' . $data['D6794'] . '</BidragsmottakerNavnSpesifisertMottaker-datadef-6794>
		<BidragsmottakerAdresseSpesifisertMottaker-datadef-6795 orid="6795">' . $data['D6795'] . '</BidragsmottakerAdresseSpesifisertMottaker-datadef-6795>
		<BidragsmottakerPostnummerSpesifisertMottaker-datadef-6796 orid="6796">' . $data['D6796'] . '</BidragsmottakerPostnummerSpesifisertMottaker-datadef-6796>
		<BidragsmottakerPoststedSpesifisertMottaker-datadef-6797 orid="6797">' . $data['D6797'] . '</BidragsmottakerPoststedSpesifisertMottaker-datadef-6797>
		<BidragsmottakerKommuneSpesifisertMottaker-datadef-6798 orid="6798">' . $data['D6798'] . '</BidragsmottakerKommuneSpesifisertMottaker-datadef-6798>
		<BidragsmottakerFodselsdatoSpesifisertMottaker-datadef-6799 orid="6799">' . $data['D6799'] . '</BidragsmottakerFodselsdatoSpesifisertMottaker-datadef-6799>
		<UnderholdsbidragFoderad-datadef-2703 orid="2703">' . $data['D2703'] . '</UnderholdsbidragFoderad-datadef-2703>
	</Underholdsbidrag-grp-3559>
	<FoderadsytelserMvSum-datadef-22182 orid="22182">' . $data['D22182'] . '</FoderadsytelserMvSum-datadef-22182>
	<UtgifterBoligselskapAndel-datadef-2704 orid="2704">' . $data['D2704'] . '</UtgifterBoligselskapAndel-datadef-2704>
	<PremiePensjonsforsikring-datadef-2705 orid="2705">' . $data['D2705'] . '</PremiePensjonsforsikring-datadef-2705>
	<TapVedRealisasjon-grp-3564 gruppeid="3564">
		<FastEiendomRealisasjonTapBeskrivelse-datadef-7699 orid="7699">' . $data['D7699'] . '</FastEiendomRealisasjonTapBeskrivelse-datadef-7699>
		<FastEiendomRealisasjonTap-datadef-2706 orid="2706">' . $data['D2706'] . '</FastEiendomRealisasjonTap-datadef-2706>
	</TapVedRealisasjon-grp-3564>
	<FastEiendomRealisasjonTapSum-datadef-22183 orid="22183">' . $data['D22183'] . '</FastEiendomRealisasjonTapSum-datadef-22183>
	<AndreUtgifterEllerFradrag-grp-3569 gruppeid="3569">
		<FradragUtgifterAndreBeskrivelse-datadef-7700 orid="7700">' . $data['D7700'] . '</FradragUtgifterAndreBeskrivelse-datadef-7700>
		<FradragUtgifterAndre-datadef-2688 orid="2688">' . $data['D2688'] . '</FradragUtgifterAndre-datadef-2688>
	</AndreUtgifterEllerFradrag-grp-3569>
	<FradragUtgifterAndreSum-datadef-22184 orid="22184">' . $data['D22184'] . '</FradragUtgifterAndreSum-datadef-22184>
	<AksjerMvTapFradragNaring-datadef-15523 orid="15523">' . $data['D15523'] . '</AksjerMvTapFradragNaring-datadef-15523>
	<UnderskuddTidligereArFremfores-datadef-20278 orid="20278">' . $data['D20278'] . '</UnderskuddTidligereArFremfores-datadef-20278>
	<FradragArbeidsinntekterKapitalutgifterMV-datadef-6800 orid="6800">' . $data['D6800'] . '</FradragArbeidsinntekterKapitalutgifterMV-datadef-6800>
</FradragKapitalkostnaderOgAndreFradrag-grp-299>
<AlminneligInntektSarfradragMv-grp-732 gruppeid="732">
	<InntektAlminnelig-datadef-2720 orid="2720">' . $data['D2720'] . '</InntektAlminnelig-datadef-2720>
	<Sarfradrag-grp-301 gruppeid="301">
		<SarfradragType-datadef-6801 orid="6801">' . $data['D6801'] . '</SarfradragType-datadef-6801>
		<Sarfradrag-datadef-2707 orid="2707">' . $data['D2707'] . '</Sarfradrag-datadef-2707>
	</Sarfradrag-grp-301>
	<KommuneFellesskattGrunnlag-datadef-2721 orid="2721">' . $data['D2721'] . '</KommuneFellesskattGrunnlag-datadef-2721>
</AlminneligInntektSarfradragMv-grp-732>
<FormueBankinnskuddKontanterVerdipapirerMv-grp-302 gruppeid="302">
	<Innskudd-grp-3414 gruppeid="3414">
		<InnskuddBankNavnSpesifisertBank-datadef-14091 orid="14091">' . $data['D14091'] . '</InnskuddBankNavnSpesifisertBank-datadef-14091>
		<InnskuddKontonummerSpesifisertBank-datadef-14092 orid="14092">' . $data['D14092'] . '</InnskuddKontonummerSpesifisertBank-datadef-14092>
		<InnskuddKontohaverNavnSpesifisertBank-datadef-14093 orid="14093">' . $data['D14093'] . '</InnskuddKontohaverNavnSpesifisertBank-datadef-14093>
		<BankinnskuddPostgiroinnskuddSpesifisertBank-datadef-6803 orid="6803">' . $data['D6803'] . '</BankinnskuddPostgiroinnskuddSpesifisertBank-datadef-6803>
	</Innskudd-grp-3414>
	<BankinnskuddPostgiroinnskudd-datadef-187 orid="187">' . $data['D187'] . '</BankinnskuddPostgiroinnskudd-datadef-187>
	<KontanterVerdipapirerMv-grp-748 gruppeid="748">
		<KontanterMvNaring-datadef-15511 orid="15511">' . $data['D15511'] . '</KontanterMvNaring-datadef-15511>
		<KontanterFribelop-datadef-7701 orid="7701">' . $data['D7701'] . '</KontanterFribelop-datadef-7701>
		<KontanterMvNaringsdrivende-datadef-16536 orid="16536">' . $data['D16536'] . '</KontanterMvNaringsdrivende-datadef-16536>
	</KontanterVerdipapirerMv-grp-748>
	<AndelerINorskeVerdipapirfond-grp-3415 gruppeid="3415">
		<Aksjefond-grp-3416 gruppeid="3416">
			<AksjefondNavn-datadef-14094 orid="14094">' . $data['D14094'] . '</AksjefondNavn-datadef-14094>
			<AksjefondAndelerAntall-datadef-14095 orid="14095">' . $data['D14095'] . '</AksjefondAndelerAntall-datadef-14095>
			<AksjefondAndelerPalydende-datadef-14096 orid="14096">' . $data['D14096'] . '</AksjefondAndelerPalydende-datadef-14096>
			<AksjefondAndelerVerdi-datadef-14097 orid="14097">' . $data['D14097'] . '</AksjefondAndelerVerdi-datadef-14097>
			<AksjefondSpesifisertVerdi-datadef-6806 orid="6806">' . $data['D6806'] . '</AksjefondSpesifisertVerdi-datadef-6806>
		</Aksjefond-grp-3416>
		<AksjefondSum-datadef-22191 orid="22191">' . $data['D22191'] . '</AksjefondSum-datadef-22191>
		<ObligasjonsPengemarkedsfond-grp-3417 gruppeid="3417">
			<ObligasjonsfondNavn-datadef-14098 orid="14098">' . $data['D14098'] . '</ObligasjonsfondNavn-datadef-14098>
			<ObligasjonsfondAndelerAntall-datadef-14099 orid="14099">' . $data['D14099'] . '</ObligasjonsfondAndelerAntall-datadef-14099>
			<ObligasjonsfondAndelerPalydende-datadef-14100 orid="14100">' . $data['D14100'] . '</ObligasjonsfondAndelerPalydende-datadef-14100>
			<ObligasjonsfondAndelerVerdi-datadef-14101 orid="14101">' . $data['D14101'] . '</ObligasjonsfondAndelerVerdi-datadef-14101>
			<ObligasjonsfondPengemarkedsfondSpesifisertVerdi-datadef-6807 orid="6807">' . $data['D6807'] . '</ObligasjonsfondPengemarkedsfondSpesifisertVerdi-datadef-6807>
		</ObligasjonsPengemarkedsfond-grp-3417>
		<ObligasjonsfondPengemarkedsfondSum-datadef-22192 orid="22192">' . $data['D22192'] . '</ObligasjonsfondPengemarkedsfondSum-datadef-22192>
	</AndelerINorskeVerdipapirfond-grp-3415>
	<UtestaendeFordringer-grp-3418 gruppeid="3418">
		<FordringerObligasjonerMvBeskrivelse-datadef-7702 orid="7702">' . $data['D7702'] . '</FordringerObligasjonerMvBeskrivelse-datadef-7702>
		<FordringerObligasjonerMvSkattemessigSpesifisert-datadef-11692 orid="11692">' . $data['D11692'] . '</FordringerObligasjonerMvSkattemessigSpesifisert-datadef-11692>
	</UtestaendeFordringer-grp-3418>
	<FordringerObligasjonerMvSum-datadef-22193 orid="22193">' . $data['D22193'] . '</FordringerObligasjonerMvSum-datadef-22193>
	<AksjerGrunnfondsbevisObligasjonerOA-grp-3422 gruppeid="3422">
		<VerdipapirerVPSKontonummer-datadef-6808 orid="6808">' . $data['D6808'] . '</VerdipapirerVPSKontonummer-datadef-6808>
		<VerdipapirerVPSAntall-datadef-14102 orid="14102">' . $data['D14102'] . '</VerdipapirerVPSAntall-datadef-14102>
		<VerdipapirerVPSPalydende-datadef-14103 orid="14103">' . $data['D14103'] . '</VerdipapirerVPSPalydende-datadef-14103>
		<VerdipapirerVPSLigningsverdi-datadef-14104 orid="14104">' . $data['D14104'] . '</VerdipapirerVPSLigningsverdi-datadef-14104>
		<VerdipapirerVPS-datadef-6809 orid="6809">' . $data['D6809'] . '</VerdipapirerVPS-datadef-6809>
	</AksjerGrunnfondsbevisObligasjonerOA-grp-3422>
	<VerdipapirerVPSSum-datadef-22194 orid="22194">' . $data['D22194'] . '</VerdipapirerVPSSum-datadef-22194>
	<VerdipapirIkkeRegistrertIVPS-grp-3424 gruppeid="3424">
		<VerdipapirerAndreSelskapNavn-datadef-6810 orid="6810">' . $data['D6810'] . '</VerdipapirerAndreSelskapNavn-datadef-6810>
		<VerdipapirerAndreAntall-datadef-6811 orid="6811">' . $data['D6811'] . '</VerdipapirerAndreAntall-datadef-6811>
		<VerdipapirerAndrePalydende-datadef-6812 orid="6812">' . $data['D6812'] . '</VerdipapirerAndrePalydende-datadef-6812>
		<VerdipapirerAndreLigningsverdi-datadef-6813 orid="6813">' . $data['D6813'] . '</VerdipapirerAndreLigningsverdi-datadef-6813>
		<VerdipapirerAndreSpesifisert-datadef-1703 orid="1703">' . $data['D1703'] . '</VerdipapirerAndreSpesifisert-datadef-1703>
	</VerdipapirIkkeRegistrertIVPS-grp-3424>
	<VerdipapirerAndreSum-datadef-22195 orid="22195">' . $data['D22195'] . '</VerdipapirerAndreSum-datadef-22195>
	<BankinnskuddMvUtlandSkattemessig-datadef-8090 orid="8090">' . $data['D8090'] . '</BankinnskuddMvUtlandSkattemessig-datadef-8090>
</FormueBankinnskuddKontanterVerdipapirerMv-grp-302>
<FormueInnboOgLosore-grp-307 gruppeid="307">
	<InnboOgAnnetLosore-grp-2453 gruppeid="2453">
		<InnboLosoreForsikringssum-datadef-6814 orid="6814">' . $data['D6814'] . '</InnboLosoreForsikringssum-datadef-6814>
		<InnboLosoreSalgsverdi-datadef-6815 orid="6815">' . $data['D6815'] . '</InnboLosoreSalgsverdi-datadef-6815>
	</InnboOgAnnetLosore-grp-2453>
	<FritidsbaterLavereVerdi-grp-2454 gruppeid="2454">
		<FritidsbaterLavereVerdiType-datadef-6816 orid="6816">' . $data['D6816'] . '</FritidsbaterLavereVerdiType-datadef-6816>
		<FritidsbaterLavereVerdiSalgsverdi-datadef-6819 orid="6819">' . $data['D6819'] . '</FritidsbaterLavereVerdiSalgsverdi-datadef-6819>
	</FritidsbaterLavereVerdi-grp-2454>
	<InnboLosoreMVForFradrag-datadef-6817 orid="6817">' . $data['D6817'] . '</InnboLosoreMVForFradrag-datadef-6817>
	<InnboLosoreMVSkattemessig-datadef-6818 orid="6818">' . $data['D6818'] . '</InnboLosoreMVSkattemessig-datadef-6818>
	<FritidsbaterHoyereVerdi-grp-2455 gruppeid="2455">
		<FritidsbaterMerke-datadef-6820 orid="6820">' . $data['D6820'] . '</FritidsbaterMerke-datadef-6820>
		<FritidsbaterHoyereVerdiType-datadef-14109 orid="14109">' . $data['D14109'] . '</FritidsbaterHoyereVerdiType-datadef-14109>
		<FritidsbaterArsmodell-datadef-6821 orid="6821">' . $data['D6821'] . '</FritidsbaterArsmodell-datadef-6821>
		<FritidsbaterForsikringssum-datadef-6822 orid="6822">' . $data['D6822'] . '</FritidsbaterForsikringssum-datadef-6822>
		<FritidsbaterSalgsverdiSpesifisert-datadef-2709 orid="2709">' . $data['D2709'] . '</FritidsbaterSalgsverdiSpesifisert-datadef-2709>
	</FritidsbaterHoyereVerdi-grp-2455>
	<FritidsbaterSalgsverdiSum-datadef-22196 orid="22196">' . $data['D22196'] . '</FritidsbaterSalgsverdiSum-datadef-22196>
	<Motorkjoretoy-grp-2456 gruppeid="2456">
		<MotorkjoretoyMerke-datadef-6823 orid="6823">' . $data['D6823'] . '</MotorkjoretoyMerke-datadef-6823>
		<MotorkjoretoyRegistreringAr-datadef-6824 orid="6824">' . $data['D6824'] . '</MotorkjoretoyRegistreringAr-datadef-6824>
		<MotorkjoretoyListeprisNy-datadef-6825 orid="6825">' . $data['D6825'] . '</MotorkjoretoyListeprisNy-datadef-6825>
		<MotorkjoretoySalgsverdi-datadef-2710 orid="2710">' . $data['D2710'] . '</MotorkjoretoySalgsverdi-datadef-2710>
	</Motorkjoretoy-grp-2456>
	<MotorkjoretoyerSalgsverdiSum-datadef-22197 orid="22197">' . $data['D22197'] . '</MotorkjoretoyerSalgsverdiSum-datadef-22197>
	<Campingvogn-grp-2457 gruppeid="2457">
		<CampingvognerMerke-datadef-6826 orid="6826">' . $data['D6826'] . '</CampingvognerMerke-datadef-6826>
		<CampingvognerRegistreringsar-datadef-6827 orid="6827">' . $data['D6827'] . '</CampingvognerRegistreringsar-datadef-6827>
		<CampingvognerListeprisNy-datadef-6828 orid="6828">' . $data['D6828'] . '</CampingvognerListeprisNy-datadef-6828>
		<CampingvognerSalgsverdi-datadef-2711 orid="2711">' . $data['D2711'] . '</CampingvognerSalgsverdi-datadef-2711>
	</Campingvogn-grp-2457>
	<CampingvognerSalgsverdiSum-datadef-22198 orid="22198">' . $data['D22198'] . '</CampingvognerSalgsverdiSum-datadef-22198>
</FormueInnboOgLosore-grp-307>
<FormueFasteEiendommerMv-grp-780 gruppeid="780">
	<Boligselskap-grp-2458 gruppeid="2458">
		<BoligselskapLigningsverdiNavn-datadef-10089 orid="10089">' . $data['D10089'] . '</BoligselskapLigningsverdiNavn-datadef-10089>
		<BoligselskapOrganisasjonsnummer-datadef-6830 orid="6830">' . $data['D6830'] . '</BoligselskapOrganisasjonsnummer-datadef-6830>
		<BoligselskapKommunenummer-datadef-17037 orid="17037">' . $data['D17037'] . '</BoligselskapKommunenummer-datadef-17037>
		<BoligselskapLigningsverdiAndel-datadef-6832 orid="6832">' . $data['D6832'] . '</BoligselskapLigningsverdiAndel-datadef-6832>
	</Boligselskap-grp-2458>
	<BoligselskaperLigningsverdiAndelerSum-datadef-22199 orid="22199">' . $data['D22199'] . '</BoligselskaperLigningsverdiAndelerSum-datadef-22199>
	<SelveidBolig-grp-2459 gruppeid="2459">
		<EiendomSelveidBoligAdresse-datadef-6834 orid="6834">' . $data['D6834'] . '</EiendomSelveidBoligAdresse-datadef-6834>
		<EiendomSelveidBoligGardsnummer-datadef-11750 orid="11750">' . $data['D11750'] . '</EiendomSelveidBoligGardsnummer-datadef-11750>
		<EiendomSelveidBoligBruksnummer-datadef-11751 orid="11751">' . $data['D11751'] . '</EiendomSelveidBoligBruksnummer-datadef-11751>
		<EiendomSelveidBoligSeksjonsnummer-datadef-11752 orid="11752">' . $data['D11752'] . '</EiendomSelveidBoligSeksjonsnummer-datadef-11752>
		<EiendomBoligFormueKommunenummer-datadef-18059 orid="18059">' . $data['D18059'] . '</EiendomBoligFormueKommunenummer-datadef-18059>
		<BoligSelveid-datadef-6837 orid="6837">' . $data['D6837'] . '</BoligSelveid-datadef-6837>
	</SelveidBolig-grp-2459>
	<Fritidsbolig-grp-2460 gruppeid="2460">
		<EiendomFritidsboligAdresse-datadef-6839 orid="6839">' . $data['D6839'] . '</EiendomFritidsboligAdresse-datadef-6839>
		<EiendomFritidsboligGardsnummer-datadef-11753 orid="11753">' . $data['D11753'] . '</EiendomFritidsboligGardsnummer-datadef-11753>
		<EiendomFritidsboligBruksnummer-datadef-11754 orid="11754">' . $data['D11754'] . '</EiendomFritidsboligBruksnummer-datadef-11754>
		<EiendomFritidsboligSeksjonsnummer-datadef-11755 orid="11755">' . $data['D11755'] . '</EiendomFritidsboligSeksjonsnummer-datadef-11755>
		<EiendomFritidsboligFormueKommunenummer-datadef-18060 orid="18060">' . $data['D18060'] . '</EiendomFritidsboligFormueKommunenummer-datadef-18060>
		<Fritidsbolig-datadef-6842 orid="6842">' . $data['D6842'] . '</Fritidsbolig-datadef-6842>
	</Fritidsbolig-grp-2460>
	<FritidsboligerSum-datadef-22200 orid="22200">' . $data['D22200'] . '</FritidsboligerSum-datadef-22200>
	<AnnenFastEiendom-grp-791 gruppeid="791">
		<EiendomTypeSpesifisertEiendom-datadef-2018 orid="2018">' . $data['D2018'] . '</EiendomTypeSpesifisertEiendom-datadef-2018>
		<EiendomNavnSpesifisertEiendom-datadef-6689 orid="6689">' . $data['D6689'] . '</EiendomNavnSpesifisertEiendom-datadef-6689>
		<EiendomAdresseSpesifisertEiendom-datadef-6843 orid="6843">' . $data['D6843'] . '</EiendomAdresseSpesifisertEiendom-datadef-6843>
		<EiendomGardsnummerSpesifisertEiendom-datadef-11756 orid="11756">' . $data['D11756'] . '</EiendomGardsnummerSpesifisertEiendom-datadef-11756>
		<EiendomBruksnummerSpesifisertEiendom-datadef-11757 orid="11757">' . $data['D11757'] . '</EiendomBruksnummerSpesifisertEiendom-datadef-11757>
		<EiendomSeksjonsnummerSpesifisertEiendom-datadef-11758 orid="11758">' . $data['D11758'] . '</EiendomSeksjonsnummerSpesifisertEiendom-datadef-11758>
		<EiendomKommunenummerSpesifisertEiendom-datadef-17038 orid="17038">' . $data['D17038'] . '</EiendomKommunenummerSpesifisertEiendom-datadef-17038>
		<EiendomSkattemessigSpesifisertEiendom-datadef-1113 orid="1113">' . $data['D1113'] . '</EiendomSkattemessigSpesifisertEiendom-datadef-1113>
	</AnnenFastEiendom-grp-791>
	<EiendomSkattemessigSum-datadef-22201 orid="22201">' . $data['D22201'] . '</EiendomSkattemessigSum-datadef-22201>
</FormueFasteEiendommerMv-grp-780>
<FormueDriftslosoreAnnenFormueOgSkattepliktigFormueIUtland-grp-792 gruppeid="792">
	<DriftslosoreOgAndreEiendelerINaring-grp-813 gruppeid="813">
		<BilerMaskinerInventarMv-datadef-6844 orid="6844">' . $data['D6844'] . '</BilerMaskinerInventarMv-datadef-6844>
		<BuskapSkattemessig-datadef-1179 orid="1179">' . $data['D1179'] . '</BuskapSkattemessig-datadef-1179>
		<LagerbeholdningNaringsdrivendeMv-datadef-16531 orid="16531">' . $data['D16531'] . '</LagerbeholdningNaringsdrivendeMv-datadef-16531>
		<FartoySkattemessig-datadef-6692 orid="6692">' . $data['D6692'] . '</FartoySkattemessig-datadef-6692>
		<DriftslosoreSkattemessig-datadef-1114 orid="1114">' . $data['D1114'] . '</DriftslosoreSkattemessig-datadef-1114>
	</DriftslosoreOgAndreEiendelerINaring-grp-813>
	<AnnenFormue-grp-814 gruppeid="814">
		<PremiefondInnskudd-datadef-2712 orid="2712">' . $data['D2712'] . '</PremiefondInnskudd-datadef-2712>
		<LivsforsikringspoliserSkattemessig-datadef-1115 orid="1115">' . $data['D1115'] . '</LivsforsikringspoliserSkattemessig-datadef-1115>
		<BoligselskapAnnenFormueAndel-datadef-2713 orid="2713">' . $data['D2713'] . '</BoligselskapAnnenFormueAndel-datadef-2713>
		<AnnenSkattepliktigFormue-grp-3603 gruppeid="3603">
			<FormueAnnenBeskrivelseSpesifisert-datadef-7703 orid="7703">' . $data['D7703'] . '</FormueAnnenBeskrivelseSpesifisert-datadef-7703>
			<FormueAnnenSkattemessigSpesifisert-datadef-14215 orid="14215">' . $data['D14215'] . '</FormueAnnenSkattemessigSpesifisert-datadef-14215>
		</AnnenSkattepliktigFormue-grp-3603>
		<FormueAnnen-datadef-20933 orid="20933">' . $data['D20933'] . '</FormueAnnen-datadef-20933>
	</AnnenFormue-grp-814>
	<SkattepliktigFormueIUtlandet-grp-2160 gruppeid="2160">
		<FormueIEiendom-grp-3885 gruppeid="3885">
			<FormueFastEiendomUtlandLand-datadef-14110 orid="14110">' . $data['D14110'] . '</FormueFastEiendomUtlandLand-datadef-14110>
			<FormueFastEiendomUtland-datadef-14106 orid="14106">' . $data['D14106'] . '</FormueFastEiendomUtland-datadef-14106>
		</FormueIEiendom-grp-3885>
		<FormueFastEiendomUtlandSum-datadef-22202 orid="22202">' . $data['D22202'] . '</FormueFastEiendomUtlandSum-datadef-22202>
	</SkattepliktigFormueIUtlandet-grp-2160>
	<FormueAnnenUtland-datadef-14107 orid="14107">' . $data['D14107'] . '</FormueAnnenUtland-datadef-14107>
	<FormueBruttoSkattemessig-datadef-1118 orid="1118">' . $data['D1118'] . '</FormueBruttoSkattemessig-datadef-1118>
</FormueDriftslosoreAnnenFormueOgSkattepliktigFormueIUtland-grp-792>
<Gjeld-grp-812 gruppeid="812">
	<GjeldNorskeFordringshavere-grp-310 gruppeid="310">
		<GjeldNorskeFordringshavereNavnSpesifisertFordringshaver-datadef-22631 orid="22631">' . $data['D22631'] . '</GjeldNorskeFordringshavereNavnSpesifisertFordringshaver-datadef-22631>
		<GjeldNorskeFordringshavereAdresseSpesifisertFordringshaver-datadef-22632 orid="22632">' . $data['D22632'] . '</GjeldNorskeFordringshavereAdresseSpesifisertFordringshaver-datadef-22632>
		<GjeldNorskeFordringshaverePostnummerSpesifisertFordringshaver-datadef-22633 orid="22633">' . $data['D22633'] . '</GjeldNorskeFordringshaverePostnummerSpesifisertFordringshaver-datadef-22633>
		<GjeldNorskeFordringshaverePoststedSpesifisertFordringshaver-datadef-22634 orid="22634">' . $data['D22634'] . '</GjeldNorskeFordringshaverePoststedSpesifisertFordringshaver-datadef-22634>
		<GjeldNorskeFordringshavereSpesifisertFordringshaver-datadef-22635 orid="22635">' . $data['D22635'] . '</GjeldNorskeFordringshavereSpesifisertFordringshaver-datadef-22635>
	</GjeldNorskeFordringshavere-grp-310>
	<GjeldNorskeFordringshavere-datadef-2714 orid="2714">' . $data['D2714'] . '</GjeldNorskeFordringshavere-datadef-2714>
	<GjeldBoligselskapAndel-datadef-2715 orid="2715">' . $data['D2715'] . '</GjeldBoligselskapAndel-datadef-2715>
	<GjeldUtenlandskeFordringshavere-datadef-2716 orid="2716">' . $data['D2716'] . '</GjeldUtenlandskeFordringshavere-datadef-2716>
	<GjeldPersonligeSkattemessig-datadef-10087 orid="10087">' . $data['D10087'] . '</GjeldPersonligeSkattemessig-datadef-10087>
</Gjeld-grp-812>
<Nettoformue-grp-2159 gruppeid="2159">
	<FormueNetto-datadef-1352 orid="1352">' . $data['D1352'] . '</FormueNetto-datadef-1352>
	<Vedlegg-datadef-6846 orid="6846">' . $data['D6846'] . '</Vedlegg-datadef-6846>
</Nettoformue-grp-2159>';
}
?>
