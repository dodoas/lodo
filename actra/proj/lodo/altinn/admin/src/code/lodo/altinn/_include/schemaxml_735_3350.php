<?php
// Filnavn: schemaxml_735_3350.php
// Skjema: RF-1025    Årsoppgave for arbeidsgiveravgift. Følgeskriv til lønns- og trekkoppg.
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1064 gruppeid="1064">
	<OppgaveEndringsoppgave-datadef-21819 orid="21819">' . $data['D21819'] . '</OppgaveEndringsoppgave-datadef-21819>
	<ArbeidsOppdragsgiversNavnOgAdresse-grp-1065 gruppeid="1065">
		<EnhetNACEKode-datadef-5133 orid="5133">' . $data['D5133'] . '</EnhetNACEKode-datadef-5133>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverTelefonnummer-datadef-620 orid="620">' . $data['D620'] . '</OppgavegiverTelefonnummer-datadef-620>
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetEPost-datadef-21591 orid="21591">' . $data['D21591'] . '</EnhetEPost-datadef-21591>
	</ArbeidsOppdragsgiversNavnOgAdresse-grp-1065>
	<SkatteoppkreverKommuneNavn-datadef-22770 orid="22770">' . $data['D22770'] . '</SkatteoppkreverKommuneNavn-datadef-22770>
</GenerellInformasjon-grp-1064>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1064 gruppeid="1064">
	<OppgaveEndringsoppgave-datadef-21819 orid="21819">' . $data['D21819'] . '</OppgaveEndringsoppgave-datadef-21819>
	<ArbeidsOppdragsgiversNavnOgAdresse-grp-1065 gruppeid="1065">
		<EnhetNACEKode-datadef-5133 orid="5133">' . $data['D5133'] . '</EnhetNACEKode-datadef-5133>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverTelefonnummer-datadef-620 orid="620">' . $data['D620'] . '</OppgavegiverTelefonnummer-datadef-620>
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetEPost-datadef-21591 orid="21591">' . $data['D21591'] . '</EnhetEPost-datadef-21591>
	</ArbeidsOppdragsgiversNavnOgAdresse-grp-1065>
	<SkatteoppkreverKommuneNavn-datadef-22770 orid="22770">' . $data['D22770'] . '</SkatteoppkreverKommuneNavn-datadef-22770>
</GenerellInformasjon-grp-1064>
<Unntakstilfeller-grp-4995 gruppeid="4995">
	<EnhetArbeidsgiveravgiftsberegningUnntaktilfelleTypeNaring-datadef-22212 orid="22212">' . $data['D22212'] . '</EnhetArbeidsgiveravgiftsberegningUnntaktilfelleTypeNaring-datadef-22212>
</Unntakstilfeller-grp-4995>
<SammmendragAvLonnsOgTrekkoppgaver-grp-1069 gruppeid="1069">
	<AntallLonnsoppgaver-grp-2634 gruppeid="2634">
		<LonnsoppgaverAntallMaskinelle-datadef-6615 orid="6615">' . $data['D6615'] . '</LonnsoppgaverAntallMaskinelle-datadef-6615>
		<LonnsoppgaverAntallManuelle-datadef-8490 orid="8490">' . $data['D8490'] . '</LonnsoppgaverAntallManuelle-datadef-8490>
	</AntallLonnsoppgaver-grp-2634>
	<SamledeOppgavepliktigeYtelserIHenholdTilLonnsoppaver-grp-2635 gruppeid="2635">
		<LonnskostnaderOppgavepliktigeMaskinelle-datadef-8487 orid="8487">' . $data['D8487'] . '</LonnskostnaderOppgavepliktigeMaskinelle-datadef-8487>
		<LonnskostnaderOppgavepliktigeManuelle-datadef-8491 orid="8491">' . $data['D8491'] . '</LonnskostnaderOppgavepliktigeManuelle-datadef-8491>
		<LonnskostnaderIkkeOppgavepliktige-datadef-6617 orid="6617">' . $data['D6617'] . '</LonnskostnaderIkkeOppgavepliktige-datadef-6617>
		<YtelserLonnsoppgavepliktigeSamlede-datadef-15944 orid="15944">' . $data['D15944'] . '</YtelserLonnsoppgavepliktigeSamlede-datadef-15944>
	</SamledeOppgavepliktigeYtelserIHenholdTilLonnsoppaver-grp-2635>
	<Avgiftssoner-grp-2706 gruppeid="2706">
		<Avgiftssone1-grp-2636 gruppeid="2636">
			<LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone1-datadef-13882 orid="13882">' . $data['D13882'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone1-datadef-13882>
			<LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone1-datadef-13887 orid="13887">' . $data['D13887'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone1-datadef-13887>
			<LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone1-datadef-13892 orid="13892">' . $data['D13892'] . '</LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone1-datadef-13892>
			<PensjonsordningerTilskuddPremieAvgiftspliktigeSone1-datadef-13897 orid="13897">' . $data['D13897'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeSone1-datadef-13897>
			<LonnskostnaderArbeidsgiveravgiftspliktigeSone1-datadef-13902 orid="13902">' . $data['D13902'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSone1-datadef-13902>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone1-datadef-22213 orid="22213">' . $data['D22213'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone1-datadef-22213>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone1-datadef-22219 orid="22219">' . $data['D22219'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone1-datadef-22219>
		</Avgiftssone1-grp-2636>
		<Avgiftssone2-grp-2637 gruppeid="2637">
			<LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone2-datadef-13883 orid="13883">' . $data['D13883'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone2-datadef-13883>
			<LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone2-datadef-13888 orid="13888">' . $data['D13888'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone2-datadef-13888>
			<LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone2-datadef-13893 orid="13893">' . $data['D13893'] . '</LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone2-datadef-13893>
			<PensjonsordningerTilskuddPremieAvgiftspliktigeSone2-datadef-13898 orid="13898">' . $data['D13898'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeSone2-datadef-13898>
			<LonnskostnaderArbeidsgiveravgiftspliktigeSone2-datadef-13903 orid="13903">' . $data['D13903'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSone2-datadef-13903>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone2-datadef-22214 orid="22214">' . $data['D22214'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone2-datadef-22214>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone2-datadef-22220 orid="22220">' . $data['D22220'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone2-datadef-22220>
		</Avgiftssone2-grp-2637>
		<Avgiftssone3-grp-2638 gruppeid="2638">
			<LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone3-datadef-13884 orid="13884">' . $data['D13884'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone3-datadef-13884>
			<LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone3-datadef-13889 orid="13889">' . $data['D13889'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone3-datadef-13889>
			<LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone3-datadef-13894 orid="13894">' . $data['D13894'] . '</LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone3-datadef-13894>
			<PensjonsordningerTilskuddPremieAvgiftspliktigeSone3-datadef-13899 orid="13899">' . $data['D13899'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeSone3-datadef-13899>
			<LonnskostnaderArbeidsgiveravgiftspliktigeSone3-datadef-13904 orid="13904">' . $data['D13904'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSone3-datadef-13904>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone3-datadef-22215 orid="22215">' . $data['D22215'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone3-datadef-22215>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone3-datadef-22221 orid="22221">' . $data['D22221'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone3-datadef-22221>
		</Avgiftssone3-grp-2638>
		<Avgiftssone4-grp-2639 gruppeid="2639">
			<LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone4-datadef-13885 orid="13885">' . $data['D13885'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone4-datadef-13885>
			<LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone4-datadef-13890 orid="13890">' . $data['D13890'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone4-datadef-13890>
			<LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone4-datadef-13895 orid="13895">' . $data['D13895'] . '</LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone4-datadef-13895>
			<PensjonsordningerTilskuddPremieAvgiftspliktigeSone4-datadef-13900 orid="13900">' . $data['D13900'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeSone4-datadef-13900>
			<LonnskostnaderArbeidsgiveravgiftspliktigeSone4-datadef-13905 orid="13905">' . $data['D13905'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSone4-datadef-13905>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone4-datadef-22216 orid="22216">' . $data['D22216'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone4-datadef-22216>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone4-datadef-22222 orid="22222">' . $data['D22222'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone4-datadef-22222>
		</Avgiftssone4-grp-2639>
		<Avgiftssone5-grp-2640 gruppeid="2640">
			<LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone5-datadef-13886 orid="13886">' . $data['D13886'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeMaskinelleSone5-datadef-13886>
			<LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone5-datadef-13891 orid="13891">' . $data['D13891'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeManuelleSone5-datadef-13891>
			<LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone5-datadef-13896 orid="13896">' . $data['D13896'] . '</LonnskostnaderAvgiftpliktigeIkkeOppgavepliktigeSone5-datadef-13896>
			<PensjonsordningerTilskuddPremieAvgiftspliktigeSone5-datadef-13901 orid="13901">' . $data['D13901'] . '</PensjonsordningerTilskuddPremieAvgiftspliktigeSone5-datadef-13901>
			<LonnskostnaderArbeidsgiveravgiftspliktigeSone5-datadef-13906 orid="13906">' . $data['D13906'] . '</LonnskostnaderArbeidsgiveravgiftspliktigeSone5-datadef-13906>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone5-datadef-22217 orid="22217">' . $data['D22217'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sone5-datadef-22217>
			<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone5-datadef-22223 orid="22223">' . $data['D22223'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sone5-datadef-22223>
		</Avgiftssone5-grp-2640>
	</Avgiftssoner-grp-2706>
	<TrukketForskuddstrekk-grp-2641 gruppeid="2641">
		<ForskuddstrekkTrukketMaskinelle-datadef-8489 orid="8489">' . $data['D8489'] . '</ForskuddstrekkTrukketMaskinelle-datadef-8489>
		<ForskuddstrekkTrukketManuelle-datadef-8493 orid="8493">' . $data['D8493'] . '</ForskuddstrekkTrukketManuelle-datadef-8493>
		<ForskuddstrekkTrukketSkattemessig-datadef-8495 orid="8495">' . $data['D8495'] . '</ForskuddstrekkTrukketSkattemessig-datadef-8495>
		<LonnskostnaderArbeidsgiveravgiftspliktige-datadef-8507 orid="8507">' . $data['D8507'] . '</LonnskostnaderArbeidsgiveravgiftspliktige-datadef-8507>
		<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sum-datadef-22218 orid="22218">' . $data['D22218'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerUnder62Sum-datadef-22218>
		<LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sum-datadef-22224 orid="22224">' . $data['D22224'] . '</LonnskostnadArbeidsgiveravgiftspliktigArbeidstakerOver62Sum-datadef-22224>
	</TrukketForskuddstrekk-grp-2641>
</SammmendragAvLonnsOgTrekkoppgaver-grp-1069>
<postTreEnTilPostTreTre-grp-4996 gruppeid="4996">
	<ArbeidsgiveravgiftUtenlandskArbeidstakerUSACanadaGrunnlag-datadef-22225 orid="22225">' . $data['D22225'] . '</ArbeidsgiveravgiftUtenlandskArbeidstakerUSACanadaGrunnlag-datadef-22225>
	<ArbeidsgiveravgiftUtenlandskArbeidstakerUSACanadaBeregnet-datadef-22226 orid="22226">' . $data['D22226'] . '</ArbeidsgiveravgiftUtenlandskArbeidstakerUSACanadaBeregnet-datadef-22226>
	<AnsattUtenlandskManeder-datadef-16519 orid="16519">' . $data['D16519'] . '</AnsattUtenlandskManeder-datadef-16519>
	<ArbeidsgiveravgiftUtenlandskManedBeregnet-datadef-16520 orid="16520">' . $data['D16520'] . '</ArbeidsgiveravgiftUtenlandskManedBeregnet-datadef-16520>
	<ArbeidsgiveravgiftEkstraGrunnlag-datadef-6620 orid="6620">' . $data['D6620'] . '</ArbeidsgiveravgiftEkstraGrunnlag-datadef-6620>
	<ArbeidsgiveravgiftEkstraBeregnet-datadef-6050 orid="6050">' . $data['D6050'] . '</ArbeidsgiveravgiftEkstraBeregnet-datadef-6050>
</postTreEnTilPostTreTre-grp-4996>
<SumInnberettetPaDeEnkelteKoderPaManuelleLonnsOgTrekkoppg-grp-1070 gruppeid="1070">
	<AnsattLonn-datadef-1226 orid="1226">' . $data['D1226'] . '</AnsattLonn-datadef-1226>
	<AnsattNaturalytelserTrekkpliktig-datadef-2926 orid="2926">' . $data['D2926'] . '</AnsattNaturalytelserTrekkpliktig-datadef-2926>
	<AnsattForsikringUlykkeYrkesskadeSkattepliktig-datadef-2927 orid="2927">' . $data['D2927'] . '</AnsattForsikringUlykkeYrkesskadeSkattepliktig-datadef-2927>
	<AnsattPensjon-datadef-2929 orid="2929">' . $data['D2929'] . '</AnsattPensjon-datadef-2929>
	<AnsattFagforeningskontingent-datadef-1330 orid="1330">' . $data['D1330'] . '</AnsattFagforeningskontingent-datadef-1330>
	<AnsattPremiePensjonsordning-datadef-1331 orid="1331">' . $data['D1331'] . '</AnsattPremiePensjonsordning-datadef-1331>
	<AnsattUnderholdsbidrag-datadef-1332 orid="1332">' . $data['D1332'] . '</AnsattUnderholdsbidrag-datadef-1332>
	<AnsattPremieFondTrygd-datadef-2919 orid="2919">' . $data['D2919'] . '</AnsattPremieFondTrygd-datadef-2919>
	<AnsattUnderholdsbidragIkkeFradregsberretiget-datadef-19662 orid="19662">' . $data['D19662'] . '</AnsattUnderholdsbidragIkkeFradregsberretiget-datadef-19662>
	<NaringsdrivendeUtbetaling-datadef-2920 orid="2920">' . $data['D2920'] . '</NaringsdrivendeUtbetaling-datadef-2920>
	<AnsattBilgodtgjorelseTrekkfri-datadef-2928 orid="2928">' . $data['D2928'] . '</AnsattBilgodtgjorelseTrekkfri-datadef-2928>
	<AnsattForskuddstrekk-datadef-1333 orid="1333">' . $data['D1333'] . '</AnsattForskuddstrekk-datadef-1333>
	<AnsattUtbetalingAnnen-datadef-8498 orid="8498">' . $data['D8498'] . '</AnsattUtbetalingAnnen-datadef-8498>
	<LonnskostnaderInnberettetManuelle-datadef-8508 orid="8508">' . $data['D8508'] . '</LonnskostnaderInnberettetManuelle-datadef-8508>
</SumInnberettetPaDeEnkelteKoderPaManuelleLonnsOgTrekkoppg-grp-1070>
<Svalbard-grp-1072 gruppeid="1072">
	<OppgaverSvalbard-datadef-6621 orid="6621">' . $data['D6621'] . '</OppgaverSvalbard-datadef-6621>
	<LonnskostnaderSvalbard-datadef-6622 orid="6622">' . $data['D6622'] . '</LonnskostnaderSvalbard-datadef-6622>
	<SkattetrekkSvalbard-datadef-6623 orid="6623">' . $data['D6623'] . '</SkattetrekkSvalbard-datadef-6623>
	<TrygdeavgiftSvalbard-datadef-6616 orid="6616">' . $data['D6616'] . '</TrygdeavgiftSvalbard-datadef-6616>
</Svalbard-grp-1072>
<Merknader-grp-1078 gruppeid="1078">
	<TilleggsopplysningerArsoppgave-datadef-14878 orid="14878">' . $data['D14878'] . '</TilleggsopplysningerArsoppgave-datadef-14878>
</Merknader-grp-1078>
';
}
?>