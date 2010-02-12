<?php
// Filnavn: schemaxml_80_3182.php
// Skjema: RF-1224    Skjema for beregning av personinntekt
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-841 gruppeid="841">
	<Selskap-grp-49 gruppeid="49">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<TypeNaring-grp-4422 gruppeid="4422">
			<EnhetNaringType-datadef-19812 orid="19812">' . $data['D19812'] . '</EnhetNaringType-datadef-19812>
			<EnhetNaringSpesifisert-datadef-20284 orid="20284">' . $data['D20284'] . '</EnhetNaringSpesifisert-datadef-20284>
		</TypeNaring-grp-4422>
	</Selskap-grp-49>
	<Regnskapsforer-grp-306 gruppeid="306">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-306>
</GenerellInformasjon-grp-841>
';
}
else
{
$xml = '<GenerellInformasjon-grp-841 gruppeid="841">
	<Selskap-grp-49 gruppeid="49">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<TypeNaring-grp-4422 gruppeid="4422">
			<EnhetNaringType-datadef-19812 orid="19812">' . $data['D19812'] . '</EnhetNaringType-datadef-19812>
			<EnhetNaringSpesifisert-datadef-20284 orid="20284">' . $data['D20284'] . '</EnhetNaringSpesifisert-datadef-20284>
		</TypeNaring-grp-4422>
	</Selskap-grp-49>
	<Regnskapsforer-grp-306 gruppeid="306">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-306>
</GenerellInformasjon-grp-841>
<GrunnlagOverfortFraNaringsoppgavenr-grp-58 gruppeid="58">
	<EnhetNaringsinntektSkattepliktig-datadef-19813 orid="19813">' . $data['D19813'] . '</EnhetNaringsinntektSkattepliktig-datadef-19813>
	<RentekostnaderTillegg-datadef-19814 orid="19814">' . $data['D19814'] . '</RentekostnaderTillegg-datadef-19814>
	<GevinstTapskontoKostnadfortTillegg-datadef-19815 orid="19815">' . $data['D19815'] . '</GevinstTapskontoKostnadfortTillegg-datadef-19815>
	<UnderskuddAndelDATillegg-datadef-19816 orid="19816">' . $data['D19816'] . '</UnderskuddAndelDATillegg-datadef-19816>
	<KapitalkostnaderTapAndreTillegg-datadef-19818 orid="19818">' . $data['D19818'] . '</KapitalkostnaderTapAndreTillegg-datadef-19818>
	<RenteinntekterFradrag-datadef-19819 orid="19819">' . $data['D19819'] . '</RenteinntekterFradrag-datadef-19819>
	<GevinstTapskontoInntektsfortFradrag-datadef-19820 orid="19820">' . $data['D19820'] . '</GevinstTapskontoInntektsfortFradrag-datadef-19820>
	<OverskuddAndelDAFradrag-datadef-19821 orid="19821">' . $data['D19821'] . '</OverskuddAndelDAFradrag-datadef-19821>
	<ReduksjonsbelopEiendomLeidInnskudd-datadef-19822 orid="19822">' . $data['D19822'] . '</ReduksjonsbelopEiendomLeidInnskudd-datadef-19822>
	<PrimarnaringKorreksjonFradrag-datadef-19823 orid="19823">' . $data['D19823'] . '</PrimarnaringKorreksjonFradrag-datadef-19823>
	<KapitalinntekterGevinsterAndreFradrag-datadef-19824 orid="19824">' . $data['D19824'] . '</KapitalinntekterGevinsterAndreFradrag-datadef-19824>
	<EnhetNaringsinntektPersoninntektGrunnlag-datadef-19825 orid="19825">' . $data['D19825'] . '</EnhetNaringsinntektPersoninntektGrunnlag-datadef-19825>
</GrunnlagOverfortFraNaringsoppgavenr-grp-58>
<BeregnetKapitalavkastning-grp-83 gruppeid="83">
	<AvskrivbareDriftsmidler-grp-900 gruppeid="900">
		<Verdsettelsesmetode-grp-2123 gruppeid="2123">
			<KontormaskinerMvKapitalavkastningVerdsettelsesmetode-datadef-7513 orid="7513">' . $data['D7513'] . '</KontormaskinerMvKapitalavkastningVerdsettelsesmetode-datadef-7513>
			<ForretningsverdiErvervetKapitalavkastningVerdsettelsesmetode-datadef-7516 orid="7516">' . $data['D7516'] . '</ForretningsverdiErvervetKapitalavkastningVerdsettelsesmetode-datadef-7516>
			<VogntogLastebilerMvKapitalavkastningVerdsettelsesmetode-datadef-7519 orid="7519">' . $data['D7519'] . '</VogntogLastebilerMvKapitalavkastningVerdsettelsesmetode-datadef-7519>
			<PersonbilerTraktorerMvKapitalavkastningVerdsettelsesmetode-datadef-7522 orid="7522">' . $data['D7522'] . '</PersonbilerTraktorerMvKapitalavkastningVerdsettelsesmetode-datadef-7522>
			<SkipFartoyerRiggerMvKapitalavkasningVerdsettelsesmetode-datadef-7525 orid="7525">' . $data['D7525'] . '</SkipFartoyerRiggerMvKapitalavkasningVerdsettelsesmetode-datadef-7525>
			<FlyHelikopterKapitalavkastningVerdsettelsesmetode-datadef-7528 orid="7528">' . $data['D7528'] . '</FlyHelikopterKapitalavkastningVerdsettelsesmetode-datadef-7528>
			<AnleggKraftoverforingKapitalavkastningVerdsettelsesmetode-datadef-19404 orid="19404">' . $data['D19404'] . '</AnleggKraftoverforingKapitalavkastningVerdsettelsesmetode-datadef-19404>
			<ByggAnleggKapitalavkastningVerdsettelsesmetode-datadef-7531 orid="7531">' . $data['D7531'] . '</ByggAnleggKapitalavkastningVerdsettelsesmetode-datadef-7531>
			<ForretningsbyggKapitalavkastningVerdsettelsesmetode-datadef-7534 orid="7534">' . $data['D7534'] . '</ForretningsbyggKapitalavkastningVerdsettelsesmetode-datadef-7534>
			<DriftsmiddelAvskrivningLineartVerdsettingsmetode-datadef-11242 orid="11242">' . $data['D11242'] . '</DriftsmiddelAvskrivningLineartVerdsettingsmetode-datadef-11242>
		</Verdsettelsesmetode-grp-2123>
		<InngaendeVerdi-grp-2124 gruppeid="2124">
			<KontormaskinerMvKapitalavkastningInngaendeVerdi-datadef-7514 orid="7514">' . $data['D7514'] . '</KontormaskinerMvKapitalavkastningInngaendeVerdi-datadef-7514>
			<ForretningsverdiErvervetKapitalavkastningInngaendeVerdi-datadef-7517 orid="7517">' . $data['D7517'] . '</ForretningsverdiErvervetKapitalavkastningInngaendeVerdi-datadef-7517>
			<VogntogLastebilerMvKapitalavkastningInngaendeVerdi-datadef-7520 orid="7520">' . $data['D7520'] . '</VogntogLastebilerMvKapitalavkastningInngaendeVerdi-datadef-7520>
			<PersonbilerTraktorerMvKapitalavkastningInngaendeVerdi-datadef-7523 orid="7523">' . $data['D7523'] . '</PersonbilerTraktorerMvKapitalavkastningInngaendeVerdi-datadef-7523>
			<SkipFartoyerRiggerMvKapitalavkasningInngaendeVerdi-datadef-7526 orid="7526">' . $data['D7526'] . '</SkipFartoyerRiggerMvKapitalavkasningInngaendeVerdi-datadef-7526>
			<FlyHelikopterKapitalavkastningInngaendeVerdi-datadef-7529 orid="7529">' . $data['D7529'] . '</FlyHelikopterKapitalavkastningInngaendeVerdi-datadef-7529>
			<AnleggKraftoverforingKapitalavkastningInngaendeVerdi-datadef-17100 orid="17100">' . $data['D17100'] . '</AnleggKraftoverforingKapitalavkastningInngaendeVerdi-datadef-17100>
			<ByggAnleggKapitalavkastningInngaendeVerdi-datadef-7532 orid="7532">' . $data['D7532'] . '</ByggAnleggKapitalavkastningInngaendeVerdi-datadef-7532>
			<ForretningsbyggKapitalavkastningInngaendeVerdi-datadef-7535 orid="7535">' . $data['D7535'] . '</ForretningsbyggKapitalavkastningInngaendeVerdi-datadef-7535>
			<DriftsmidlerAvskrivningLineartInngaendeVerdi-datadef-11243 orid="11243">' . $data['D11243'] . '</DriftsmidlerAvskrivningLineartInngaendeVerdi-datadef-11243>
		</InngaendeVerdi-grp-2124>
		<UtgaendeVerdi-grp-2125 gruppeid="2125">
			<KontormaskinerMvKapitalavkastningUtgaendeVerdi-datadef-7515 orid="7515">' . $data['D7515'] . '</KontormaskinerMvKapitalavkastningUtgaendeVerdi-datadef-7515>
			<ForretningsverdiErvervetKapitalavkastningUtgaendeVerdi-datadef-7518 orid="7518">' . $data['D7518'] . '</ForretningsverdiErvervetKapitalavkastningUtgaendeVerdi-datadef-7518>
			<VogntogLastebilerMvKapitalavkastningUtgaendeVerdi-datadef-7521 orid="7521">' . $data['D7521'] . '</VogntogLastebilerMvKapitalavkastningUtgaendeVerdi-datadef-7521>
			<PersonbilerTraktorerMvKapitalavkastningUtgaendeVerdi-datadef-7524 orid="7524">' . $data['D7524'] . '</PersonbilerTraktorerMvKapitalavkastningUtgaendeVerdi-datadef-7524>
			<SkipFartoyerRiggerMvKapitalavkasningUtgaendeVerdi-datadef-7527 orid="7527">' . $data['D7527'] . '</SkipFartoyerRiggerMvKapitalavkasningUtgaendeVerdi-datadef-7527>
			<FlyHelikopterKapitalavkastningUtgaendeVerdi-datadef-7530 orid="7530">' . $data['D7530'] . '</FlyHelikopterKapitalavkastningUtgaendeVerdi-datadef-7530>
			<AnleggKraftoverforingKapitalavkastningUtgaendeVerdi-datadef-17101 orid="17101">' . $data['D17101'] . '</AnleggKraftoverforingKapitalavkastningUtgaendeVerdi-datadef-17101>
			<ByggAnleggKapitalavkastningUtgaendeVerdi-datadef-7533 orid="7533">' . $data['D7533'] . '</ByggAnleggKapitalavkastningUtgaendeVerdi-datadef-7533>
			<ForretningsbyggKapitalavkastningUtgaendeVerdi-datadef-7536 orid="7536">' . $data['D7536'] . '</ForretningsbyggKapitalavkastningUtgaendeVerdi-datadef-7536>
			<DriftsmiddelAvskrivningLineartUtgaendeVerdi-datadef-11244 orid="11244">' . $data['D11244'] . '</DriftsmiddelAvskrivningLineartUtgaendeVerdi-datadef-11244>
		</UtgaendeVerdi-grp-2125>
	</AvskrivbareDriftsmidler-grp-900>
	<AnnenKapitalavkastning-grp-2127 gruppeid="2127">
		<Verdsettelsesmetode-grp-2128 gruppeid="2128">
			<DriftsmidlerIkkeAvskrivbareKapitalavkastningVerdsettelsesmetode-datadef-7537 orid="7537">' . $data['D7537'] . '</DriftsmidlerIkkeAvskrivbareKapitalavkastningVerdsettelsesmetode-datadef-7537>
			<ImmaterielleRettigheterErvervedeKapitalavkVerdsettelsesmetode-datadef-7540 orid="7540">' . $data['D7540'] . '</ImmaterielleRettigheterErvervedeKapitalavkVerdsettelsesmetode-datadef-7540>
			<FoUKostnaderKapitalavkastningVerdsettelsesmetode-datadef-7543 orid="7543">' . $data['D7543'] . '</FoUKostnaderKapitalavkastningVerdsettelsesmetode-datadef-7543>
			<VarerKapitalavkastningVerdsettelsesmetode-datadef-7546 orid="7546">' . $data['D7546'] . '</VarerKapitalavkastningVerdsettelsesmetode-datadef-7546>
			<KundefordringerKapitalavkastningVerdsettelsesmetode-datadef-7549 orid="7549">' . $data['D7549'] . '</KundefordringerKapitalavkastningVerdsettelsesmetode-datadef-7549>
		</Verdsettelsesmetode-grp-2128>
		<InngaendeVerdi-grp-2129 gruppeid="2129">
			<DriftsmidlerIkkeAvskrivbareKapitalavkastningInngaendeVerdi-datadef-7538 orid="7538">' . $data['D7538'] . '</DriftsmidlerIkkeAvskrivbareKapitalavkastningInngaendeVerdi-datadef-7538>
			<ImmaterielleRettigheterErvervedeKapitalavkInngaendeVerdi-datadef-7541 orid="7541">' . $data['D7541'] . '</ImmaterielleRettigheterErvervedeKapitalavkInngaendeVerdi-datadef-7541>
			<FoUKostnaderKapitalavkastningInngaendeVerdi-datadef-7544 orid="7544">' . $data['D7544'] . '</FoUKostnaderKapitalavkastningInngaendeVerdi-datadef-7544>
			<VarerKapitalavkastningInngaendeVerdi-datadef-7547 orid="7547">' . $data['D7547'] . '</VarerKapitalavkastningInngaendeVerdi-datadef-7547>
			<KundefordringerKapitalavkastningInngaendeVerdi-datadef-7550 orid="7550">' . $data['D7550'] . '</KundefordringerKapitalavkastningInngaendeVerdi-datadef-7550>
			<EiendelerKapitalavkastningInngaendeVerdi-datadef-7552 orid="7552">' . $data['D7552'] . '</EiendelerKapitalavkastningInngaendeVerdi-datadef-7552>
			<LeverandorgjeldKundeforskuddKapitalavkastningInngaendeVerdi-datadef-7554 orid="7554">' . $data['D7554'] . '</LeverandorgjeldKundeforskuddKapitalavkastningInngaendeVerdi-datadef-7554>
			<KapitalavkastningsgrunnlagInngaendeVerdi-datadef-7556 orid="7556">' . $data['D7556'] . '</KapitalavkastningsgrunnlagInngaendeVerdi-datadef-7556>
		</InngaendeVerdi-grp-2129>
		<UtgaendeVerdi-grp-2130 gruppeid="2130">
			<DriftsmidlerIkkeAvskrivbareKapitalavkastningUtgaendeVerdi-datadef-7539 orid="7539">' . $data['D7539'] . '</DriftsmidlerIkkeAvskrivbareKapitalavkastningUtgaendeVerdi-datadef-7539>
			<ImmaterielleRettigheterErvervedeKapitalavkUtgaendeVerdi-datadef-7542 orid="7542">' . $data['D7542'] . '</ImmaterielleRettigheterErvervedeKapitalavkUtgaendeVerdi-datadef-7542>
			<FoUKostnaderKapitalavkastningUtgaendeVerdi-datadef-7545 orid="7545">' . $data['D7545'] . '</FoUKostnaderKapitalavkastningUtgaendeVerdi-datadef-7545>
			<VarerKapitalavkastningUtgaendeVerdi-datadef-7548 orid="7548">' . $data['D7548'] . '</VarerKapitalavkastningUtgaendeVerdi-datadef-7548>
			<KundefordringerKapitalavkastningUtgaendeVerdi-datadef-7551 orid="7551">' . $data['D7551'] . '</KundefordringerKapitalavkastningUtgaendeVerdi-datadef-7551>
			<EiendelerKapitalavkastningUtgaendeVerdi-datadef-7553 orid="7553">' . $data['D7553'] . '</EiendelerKapitalavkastningUtgaendeVerdi-datadef-7553>
			<LeverandorgjeldKundeforskuddKapitalavkastningUtgaendeVerdi-datadef-7555 orid="7555">' . $data['D7555'] . '</LeverandorgjeldKundeforskuddKapitalavkastningUtgaendeVerdi-datadef-7555>
			<KapitalavkastningsgrunnlagUtgaendeVerdi-datadef-7557 orid="7557">' . $data['D7557'] . '</KapitalavkastningsgrunnlagUtgaendeVerdi-datadef-7557>
			<KapitalavkastningGrunnlag-datadef-2028 orid="2028">' . $data['D2028'] . '</KapitalavkastningGrunnlag-datadef-2028>
		</UtgaendeVerdi-grp-2130>
	</AnnenKapitalavkastning-grp-2127>
	<BeregnetKapitalavkastning-grp-2131 gruppeid="2131">
		<Kapitalavkastingsrate-datadef-7675 orid="7675">' . $data['D7675'] . '</Kapitalavkastingsrate-datadef-7675>
		<DriftTidsrom-datadef-1818 orid="1818">' . $data['D1818'] . '</DriftTidsrom-datadef-1818>
		<KapitalavkastningBeregnet-datadef-7558 orid="7558">' . $data['D7558'] . '</KapitalavkastningBeregnet-datadef-7558>
		<PersoninntektForelopigBeregnet-datadef-2029 orid="2029">' . $data['D2029'] . '</PersoninntektForelopigBeregnet-datadef-2029>
	</BeregnetKapitalavkastning-grp-2131>
</BeregnetKapitalavkastning-grp-83>
<Selskapsforhold-grp-961 gruppeid="961">
	<AktiveTilordnesPersoninntektAntall-datadef-2030 orid="2030">' . $data['D2030'] . '</AktiveTilordnesPersoninntektAntall-datadef-2030>
	<AktiveEierandel-datadef-2031 orid="2031">' . $data['D2031'] . '</AktiveEierandel-datadef-2031>
	<AktiveOverskuddUtbytteRettTilSamlet-datadef-2032 orid="2032">' . $data['D2032'] . '</AktiveOverskuddUtbytteRettTilSamlet-datadef-2032>
	<OpplysningerOmDenAktive-grp-972 gruppeid="972">
		<AktivNavn-datadef-2033 orid="2033">' . $data['D2033'] . '</AktivNavn-datadef-2033>
		<AktivKommune-datadef-2034 orid="2034">' . $data['D2034'] . '</AktivKommune-datadef-2034>
		<AktivFodselsnummer-datadef-2035 orid="2035">' . $data['D2035'] . '</AktivFodselsnummer-datadef-2035>
		<AktivOverskuddUtbytteRettTil-datadef-2036 orid="2036">' . $data['D2036'] . '</AktivOverskuddUtbytteRettTil-datadef-2036>
	</OpplysningerOmDenAktive-grp-972>
	<OpplysningerOmPersonInnretningSelskap-grp-973 gruppeid="973">
		<AktivIdentifikasjonNavnSpesifisert-datadef-2037 orid="2037">' . $data['D2037'] . '</AktivIdentifikasjonNavnSpesifisert-datadef-2037>
		<AktivIdentifikasjonFodselsnummerSpesifisert-datadef-2038 orid="2038">' . $data['D2038'] . '</AktivIdentifikasjonFodselsnummerSpesifisert-datadef-2038>
		<AktivIdentifikasjonOrganisasjonsnummerSpesifisert-datadef-13697 orid="13697">' . $data['D13697'] . '</AktivIdentifikasjonOrganisasjonsnummerSpesifisert-datadef-13697>
		<AktivIdentifikasjonOverskuddUtbytteSpesifisert-datadef-2039 orid="2039">' . $data['D2039'] . '</AktivIdentifikasjonOverskuddUtbytteSpesifisert-datadef-2039>
	</OpplysningerOmPersonInnretningSelskap-grp-973>
	<AktivOverskuddUtbytteEgenAndel-datadef-7559 orid="7559">' . $data['D7559'] . '</AktivOverskuddUtbytteEgenAndel-datadef-7559>
	<AktivesDelAvRettTilTotaleUtbytteOverskudd-grp-988 gruppeid="988">
		<AktivOverskuddUtbytteTotalUtbytteandel-datadef-7560 orid="7560">' . $data['D7560'] . '</AktivOverskuddUtbytteTotalUtbytteandel-datadef-7560>
	</AktivesDelAvRettTilTotaleUtbytteOverskudd-grp-988>
</Selskapsforhold-grp-961>
<FordelingMellomEktefeller-grp-303 gruppeid="303">
	<AndelerEktefelle-datadef-11248 orid="11248">' . $data['D11248'] . '</AndelerEktefelle-datadef-11248>
</FordelingMellomEktefeller-grp-303>
<OpplysningerTilBrukForUtregningAvForetaketsLonnsfradrag-grp-304 gruppeid="304">
	<PersoninntektLonnsfradragBeregningsgrunnlag-datadef-7562 orid="7562">' . $data['D7562'] . '</PersoninntektLonnsfradragBeregningsgrunnlag-datadef-7562>
	<LonnsfradragForetak-datadef-7563 orid="7563">' . $data['D7563'] . '</LonnsfradragForetak-datadef-7563>
</OpplysningerTilBrukForUtregningAvForetaketsLonnsfradrag-grp-304>
<Personinntekt-grp-305 gruppeid="305">
	<OverforingAvSum-grp-992 gruppeid="992">
		<AktivAndelFellesBedriftSelskap-datadef-13076 orid="13076">' . $data['D13076'] . '</AktivAndelFellesBedriftSelskap-datadef-13076>
		<PersoninntektAktiv-datadef-7561 orid="7561">' . $data['D7561'] . '</PersoninntektAktiv-datadef-7561>
	</OverforingAvSum-grp-992>
	<PersoninntektLonnAnnenGodtgjorelseAktiv-datadef-7575 orid="7575">' . $data['D7575'] . '</PersoninntektLonnAnnenGodtgjorelseAktiv-datadef-7575>
	<PersoninntektAktivForelopigAndel-datadef-7564 orid="7564">' . $data['D7564'] . '</PersoninntektAktivForelopigAndel-datadef-7564>
	<PersoninntektLonnsfradrag-datadef-7565 orid="7565">' . $data['D7565'] . '</PersoninntektLonnsfradrag-datadef-7565>
	<PersoninntektLonnsfradragAktiv-datadef-7566 orid="7566">' . $data['D7566'] . '</PersoninntektLonnsfradragAktiv-datadef-7566>
	<PersoninntektLonnsfradragMinsteBelop-datadef-13698 orid="13698">' . $data['D13698'] . '</PersoninntektLonnsfradragMinsteBelop-datadef-13698>
	<PersoninntektBeregnetIInntektsaret-datadef-7567 orid="7567">' . $data['D7567'] . '</PersoninntektBeregnetIInntektsaret-datadef-7567>
	<GodtgjorelseDeltakerlignetSelskap-datadef-1456 orid="1456">' . $data['D1456'] . '</GodtgjorelseDeltakerlignetSelskap-datadef-1456>
	<PersoninntektFremforbarNegativTidligereAr-datadef-2041 orid="2041">' . $data['D2041'] . '</PersoninntektFremforbarNegativTidligereAr-datadef-2041>
	<PersoninntektForSamordning-datadef-7569 orid="7569">' . $data['D7569'] . '</PersoninntektForSamordning-datadef-7569>
	<PersoninntektNegativAnnenNaringFradrag-datadef-2043 orid="2043">' . $data['D2043'] . '</PersoninntektNegativAnnenNaringFradrag-datadef-2043>
	<PersoninntektNegativAnnenNaringTillegg-datadef-7568 orid="7568">' . $data['D7568'] . '</PersoninntektNegativAnnenNaringTillegg-datadef-7568>
	<PersoninntektBeregnetKorrigert-datadef-7572 orid="7572">' . $data['D7572'] . '</PersoninntektBeregnetKorrigert-datadef-7572>
	<SpesifiseringAvBeregnetPersoninntekt-grp-4194 gruppeid="4194">
		<FiskeJordOgSkogbrukPelsOgReindriftMellomsats-grp-4196 gruppeid="4196">
			<PersoninntektPrimarnaringBeregnet-datadef-19826 orid="19826">' . $data['D19826'] . '</PersoninntektPrimarnaringBeregnet-datadef-19826>
			<ArbeidsgodtgjorelsePrimarnaringBeregnet-datadef-19829 orid="19829">' . $data['D19829'] . '</ArbeidsgodtgjorelsePrimarnaringBeregnet-datadef-19829>
		</FiskeJordOgSkogbrukPelsOgReindriftMellomsats-grp-4196>
		<LiberalVirksomhet-grp-4197 gruppeid="4197">
			<PersoninntektLiberalVirksomhetBeregnet-datadef-19827 orid="19827">' . $data['D19827'] . '</PersoninntektLiberalVirksomhetBeregnet-datadef-19827>
			<ArbeidsgodtgjorelseLiberaltVirksomhetBeregnet-datadef-19830 orid="19830">' . $data['D19830'] . '</ArbeidsgodtgjorelseLiberaltVirksomhetBeregnet-datadef-19830>
		</LiberalVirksomhet-grp-4197>
		<AnnenNaring-grp-4198 gruppeid="4198">
			<PersonimmtektNaringAnnenBeregnet-datadef-19828 orid="19828">' . $data['D19828'] . '</PersonimmtektNaringAnnenBeregnet-datadef-19828>
			<ArbeidsinntektNaringAnnenBeregnet-datadef-19831 orid="19831">' . $data['D19831'] . '</ArbeidsinntektNaringAnnenBeregnet-datadef-19831>
		</AnnenNaring-grp-4198>
	</SpesifiseringAvBeregnetPersoninntekt-grp-4194>
</Personinntekt-grp-305>
';
}
?>