<?php
// Filnavn: schemaxml_98_3394.php
// Skjema: RF-1217    Spesifikasjon av forskjeller ml regnskapsmessige og skattem. verdier
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-824 gruppeid="824">
	<Avgiver-grp-116 gruppeid="116">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-116>
	<Regnskapsforer-grp-114 gruppeid="114">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-114>
</GenerellInformasjon-grp-824>
';
}
else
{
$xml = '<GenerellInformasjon-grp-824 gruppeid="824">
	<Avgiver-grp-116 gruppeid="116">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-116>
	<Regnskapsforer-grp-114 gruppeid="114">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
	</Regnskapsforer-grp-114>
</GenerellInformasjon-grp-824>
<DriftsmidlerLangsiktigeFordringerMv-grp-825 gruppeid="825">
	<DriftsmidlerInklForretningsverdi-grp-2108 gruppeid="2108">
		<Fjoraret-grp-3695 gruppeid="3695">
			<DriftsmidlerFjoraret-datadef-7202 orid="7202">' . $data['D7202'] . '</DriftsmidlerFjoraret-datadef-7202>
			<DriftsmidlerSkattemessigFjoraret-datadef-7203 orid="7203">' . $data['D7203'] . '</DriftsmidlerSkattemessigFjoraret-datadef-7203>
			<ForskjellerDriftsmidlerFjoraret-datadef-7204 orid="7204">' . $data['D7204'] . '</ForskjellerDriftsmidlerFjoraret-datadef-7204>
		</Fjoraret-grp-3695>
		<DetteAr-grp-3696 gruppeid="3696">
			<Driftsmidler-datadef-1100 orid="1100">' . $data['D1100'] . '</Driftsmidler-datadef-1100>
			<DriftsmidlerSkattemessig-datadef-2203 orid="2203">' . $data['D2203'] . '</DriftsmidlerSkattemessig-datadef-2203>
			<ForskjellerDriftsmidler-datadef-7205 orid="7205">' . $data['D7205'] . '</ForskjellerDriftsmidler-datadef-7205>
		</DetteAr-grp-3696>
		<EndringIForskjeller-grp-3697 gruppeid="3697">
			<ForskjellerEndringDriftsmidler-datadef-7206 orid="7206">' . $data['D7206'] . '</ForskjellerEndringDriftsmidler-datadef-7206>
		</EndringIForskjeller-grp-3697>
	</DriftsmidlerInklForretningsverdi-grp-2108>
	<LangsiktigeFordringerOgGjeldIUtenlandskValuta-grp-3698 gruppeid="3698">
		<Fjoraret-grp-3699 gruppeid="3699">
			<FordringerGjeldLangsiktigUtenlandskValutaFjoraret-datadef-7207 orid="7207">' . $data['D7207'] . '</FordringerGjeldLangsiktigUtenlandskValutaFjoraret-datadef-7207>
			<FordringerGjeldLangsiktigValutaSkattemessigFjoraret-datadef-7208 orid="7208">' . $data['D7208'] . '</FordringerGjeldLangsiktigValutaSkattemessigFjoraret-datadef-7208>
			<ForskjellerFordringerGjeldLangsiktigFjoraret-datadef-7209 orid="7209">' . $data['D7209'] . '</ForskjellerFordringerGjeldLangsiktigFjoraret-datadef-7209>
		</Fjoraret-grp-3699>
		<DetteAr-grp-3700 gruppeid="3700">
			<FordringerGjeldLangsiktigUtenlandskValuta-datadef-2201 orid="2201">' . $data['D2201'] . '</FordringerGjeldLangsiktigUtenlandskValuta-datadef-2201>
			<FordringerGjeldLangsiktigUtenlandskValutaSkattemessig-datadef-2202 orid="2202">' . $data['D2202'] . '</FordringerGjeldLangsiktigUtenlandskValutaSkattemessig-datadef-2202>
			<ForskjellerFordringerGjeldLangsiktig-datadef-7210 orid="7210">' . $data['D7210'] . '</ForskjellerFordringerGjeldLangsiktig-datadef-7210>
		</DetteAr-grp-3700>
		<EndringIForskjeller-grp-3701 gruppeid="3701">
			<ForskjellerEndringFordringerGjeldLangsiktig-datadef-7211 orid="7211">' . $data['D7211'] . '</ForskjellerEndringFordringerGjeldLangsiktig-datadef-7211>
		</EndringIForskjeller-grp-3701>
	</LangsiktigeFordringerOgGjeldIUtenlandskValuta-grp-3698>
	<TilvirkningskontraktSomIkkeErFullfortVedArsslutt-grp-3702 gruppeid="3702">
		<Fjoraret-grp-3703 gruppeid="3703">
			<TilvirkningskontraktOpptjentInntektFjoraret-datadef-7212 orid="7212">' . $data['D7212'] . '</TilvirkningskontraktOpptjentInntektFjoraret-datadef-7212>
			<TilvirkningskontraktOpptjentInntektSkattemessigFjoraret-datadef-7213 orid="7213">' . $data['D7213'] . '</TilvirkningskontraktOpptjentInntektSkattemessigFjoraret-datadef-7213>
			<ForskjellerTilvirkningskontraktOpptjentInntektFjoraret-datadef-7214 orid="7214">' . $data['D7214'] . '</ForskjellerTilvirkningskontraktOpptjentInntektFjoraret-datadef-7214>
		</Fjoraret-grp-3703>
		<DetteAr-grp-3704 gruppeid="3704">
			<TilvirkningskontraktOpptjentInntekt-datadef-2199 orid="2199">' . $data['D2199'] . '</TilvirkningskontraktOpptjentInntekt-datadef-2199>
			<TilvirkningskontraktOpptjentInntektSkattemessig-datadef-2200 orid="2200">' . $data['D2200'] . '</TilvirkningskontraktOpptjentInntektSkattemessig-datadef-2200>
			<ForskjellerTilvirkningskontraktOpptjentInntekt-datadef-7215 orid="7215">' . $data['D7215'] . '</ForskjellerTilvirkningskontraktOpptjentInntekt-datadef-7215>
		</DetteAr-grp-3704>
		<EndringIForskjeller-grp-3705 gruppeid="3705">
			<ForskjellerEndringTilvirkningskontraktOpptjentInntekt-datadef-7216 orid="7216">' . $data['D7216'] . '</ForskjellerEndringTilvirkningskontraktOpptjentInntekt-datadef-7216>
		</EndringIForskjeller-grp-3705>
	</TilvirkningskontraktSomIkkeErFullfortVedArsslutt-grp-3702>
</DriftsmidlerLangsiktigeFordringerMv-grp-825>
<VarebeholdningOgUtestaendeFordringer-grp-3706 gruppeid="3706">
	<Varebeholdning-grp-3707 gruppeid="3707">
		<Fjoraret-grp-3708 gruppeid="3708">
			<LagerbeholdningRegnskapsmessigForskjellerFjoraret-datadef-22160 orid="22160">' . $data['D22160'] . '</LagerbeholdningRegnskapsmessigForskjellerFjoraret-datadef-22160>
			<LagerbeholdningSkattemessigForskjellerFjoraret-datadef-22161 orid="22161">' . $data['D22161'] . '</LagerbeholdningSkattemessigForskjellerFjoraret-datadef-22161>
			<ForskjellerLagerbeholdningFjoraret-datadef-7218 orid="7218">' . $data['D7218'] . '</ForskjellerLagerbeholdningFjoraret-datadef-7218>
		</Fjoraret-grp-3708>
		<DetteAr-grp-3709 gruppeid="3709">
			<LagerbeholdningRegnskapsmessigForskjeller-datadef-16542 orid="16542">' . $data['D16542'] . '</LagerbeholdningRegnskapsmessigForskjeller-datadef-16542>
			<LagerbeholdningSkattemessigForskjeller-datadef-16541 orid="16541">' . $data['D16541'] . '</LagerbeholdningSkattemessigForskjeller-datadef-16541>
			<ForskjellerLagerbeholdning-datadef-7219 orid="7219">' . $data['D7219'] . '</ForskjellerLagerbeholdning-datadef-7219>
		</DetteAr-grp-3709>
		<EndringIForskjeller-grp-3710 gruppeid="3710">
			<ForskjellerEndringLagerbeholdning-datadef-7220 orid="7220">' . $data['D7220'] . '</ForskjellerEndringLagerbeholdning-datadef-7220>
		</EndringIForskjeller-grp-3710>
	</Varebeholdning-grp-3707>
	<UtestaendeFordringer-grp-3711 gruppeid="3711">
		<Fjoraret-grp-3712 gruppeid="3712">
			<FordringerKunderRegnskapsmessigFjoraret-datadef-16538 orid="16538">' . $data['D16538'] . '</FordringerKunderRegnskapsmessigFjoraret-datadef-16538>
			<FordringerAndreFjoraret-datadef-7221 orid="7221">' . $data['D7221'] . '</FordringerAndreFjoraret-datadef-7221>
			<FordringerKunderSkattemessigFjoraret-datadef-16539 orid="16539">' . $data['D16539'] . '</FordringerKunderSkattemessigFjoraret-datadef-16539>
			<ForskjellerFordringerFjoraret-datadef-7222 orid="7222">' . $data['D7222'] . '</ForskjellerFordringerFjoraret-datadef-7222>
		</Fjoraret-grp-3712>
		<DetteAr-grp-3713 gruppeid="3713">
			<FordringerKunderForskjeller-datadef-16051 orid="16051">' . $data['D16051'] . '</FordringerKunderForskjeller-datadef-16051>
			<FordringerForskjeller-datadef-16532 orid="16532">' . $data['D16532'] . '</FordringerForskjeller-datadef-16532>
			<FordringerAndre-datadef-11094 orid="11094">' . $data['D11094'] . '</FordringerAndre-datadef-11094>
			<ForskjellerFordringer-datadef-7223 orid="7223">' . $data['D7223'] . '</ForskjellerFordringer-datadef-7223>
		</DetteAr-grp-3713>
		<EndringIForskjeller-grp-3714 gruppeid="3714">
			<ForskjellerEndringFordringer-datadef-7224 orid="7224">' . $data['D7224'] . '</ForskjellerEndringFordringer-datadef-7224>
		</EndringIForskjeller-grp-3714>
	</UtestaendeFordringer-grp-3711>
</VarebeholdningOgUtestaendeFordringer-grp-3706>
<BalanseforteLeieavtalerIRegnskapet-grp-4404 gruppeid="4404">
	<Fjoraret-grp-4405 gruppeid="4405">
		<LeasingobjektRegnskapsmessigFjoraret-datadef-20244 orid="20244">' . $data['D20244'] . '</LeasingobjektRegnskapsmessigFjoraret-datadef-20244>
		<LeasinggjeldRegnskapsmessigFjoraret-datadef-20246 orid="20246">' . $data['D20246'] . '</LeasinggjeldRegnskapsmessigFjoraret-datadef-20246>
		<ForskjellerLeasingobjektGjeldFjoraret-datadef-20248 orid="20248">' . $data['D20248'] . '</ForskjellerLeasingobjektGjeldFjoraret-datadef-20248>
	</Fjoraret-grp-4405>
	<DetteAr-grp-4406 gruppeid="4406">
		<LeasingobjektRegnskapsmessig-datadef-20245 orid="20245">' . $data['D20245'] . '</LeasingobjektRegnskapsmessig-datadef-20245>
		<LeasinggjeldRegnskapsmessig-datadef-20247 orid="20247">' . $data['D20247'] . '</LeasinggjeldRegnskapsmessig-datadef-20247>
		<ForskjellerLeasingobjektGjeld-datadef-20249 orid="20249">' . $data['D20249'] . '</ForskjellerLeasingobjektGjeld-datadef-20249>
	</DetteAr-grp-4406>
	<EndringIForskjeller-grp-4407 gruppeid="4407">
		<ForskjellerEndringerLeasingobjektGjeld-datadef-20250 orid="20250">' . $data['D20250'] . '</ForskjellerEndringerLeasingobjektGjeld-datadef-20250>
	</EndringIForskjeller-grp-4407>
</BalanseforteLeieavtalerIRegnskapet-grp-4404>
<AndreOpplysninger-grp-3715 gruppeid="3715">
	<Fjoraret-grp-3716 gruppeid="3716">
		<GevinstTapskontoSaldoFjoraret-datadef-7225 orid="7225">' . $data['D7225'] . '</GevinstTapskontoSaldoFjoraret-datadef-7225>
		<AvsetningerBetingetSkattefrieFjoraret-datadef-7227 orid="7227">' . $data['D7227'] . '</AvsetningerBetingetSkattefrieFjoraret-datadef-7227>
		<DriftsinntekterUopptjentFjoraret-datadef-6684 orid="6684">' . $data['D6684'] . '</DriftsinntekterUopptjentFjoraret-datadef-6684>
		<AvsetningerForpliktelserFjoraret-datadef-7230 orid="7230">' . $data['D7230'] . '</AvsetningerForpliktelserFjoraret-datadef-7230>
		<AvsetningerTapKontrakterMvFjoraret-datadef-7233 orid="7233">' . $data['D7233'] . '</AvsetningerTapKontrakterMvFjoraret-datadef-7233>
		<UtbytteAvsattInntektsfortFjoraret-datadef-7235 orid="7235">' . $data['D7235'] . '</UtbytteAvsattInntektsfortFjoraret-datadef-7235>
		<PensjonsforpliktelseNettoFjoraret-datadef-20144 orid="20144">' . $data['D20144'] . '</PensjonsforpliktelseNettoFjoraret-datadef-20144>
		<PensjonsmidlerInnskuddFondNettoFjoraret-datadef-20147 orid="20147">' . $data['D20147'] . '</PensjonsmidlerInnskuddFondNettoFjoraret-datadef-20147>
		<AvsetningerPensjonspremiefondSkattemessigFjoraret-datadef-7240 orid="7240">' . $data['D7240'] . '</AvsetningerPensjonspremiefondSkattemessigFjoraret-datadef-7240>
		<ForpliktelserOvertatteFjoraret-datadef-22044 orid="22044">' . $data['D22044'] . '</ForpliktelserOvertatteFjoraret-datadef-22044>
	</Fjoraret-grp-3716>
	<DetteAr-grp-3717 gruppeid="3717">
		<GevinstTapskontoSaldo-datadef-1019 orid="1019">' . $data['D1019'] . '</GevinstTapskontoSaldo-datadef-1019>
		<AvsetningerBetingetSkattefrie-datadef-1020 orid="1020">' . $data['D1020'] . '</AvsetningerBetingetSkattefrie-datadef-1020>
		<DriftsinntekterUopptjent-datadef-6683 orid="6683">' . $data['D6683'] . '</DriftsinntekterUopptjent-datadef-6683>
		<AvsetningerForpliktelser-datadef-7231 orid="7231">' . $data['D7231'] . '</AvsetningerForpliktelser-datadef-7231>
		<AvsetningerTapKontrakterMv-datadef-2204 orid="2204">' . $data['D2204'] . '</AvsetningerTapKontrakterMv-datadef-2204>
		<UtbytteAvsattInntektsfort-datadef-7236 orid="7236">' . $data['D7236'] . '</UtbytteAvsattInntektsfort-datadef-7236>
		<PensjonsforpliktelseNetto-datadef-20145 orid="20145">' . $data['D20145'] . '</PensjonsforpliktelseNetto-datadef-20145>
		<PensjonsmidlerInnskuddFondNetto-datadef-20148 orid="20148">' . $data['D20148'] . '</PensjonsmidlerInnskuddFondNetto-datadef-20148>
		<AvsetningerPremiePremiefondSkattemessig-datadef-2207 orid="2207">' . $data['D2207'] . '</AvsetningerPremiePremiefondSkattemessig-datadef-2207>
		<ForpliktelserOvertatte-datadef-22045 orid="22045">' . $data['D22045'] . '</ForpliktelserOvertatte-datadef-22045>
	</DetteAr-grp-3717>
	<EndringIForskjeller-grp-2122 gruppeid="2122">
		<GevinstTapskontoSaldoEndring-datadef-7226 orid="7226">' . $data['D7226'] . '</GevinstTapskontoSaldoEndring-datadef-7226>
		<AvsetningerBetingetSkattefrieEndring-datadef-7228 orid="7228">' . $data['D7228'] . '</AvsetningerBetingetSkattefrieEndring-datadef-7228>
		<DriftsinntekterUopptjentEndring-datadef-7229 orid="7229">' . $data['D7229'] . '</DriftsinntekterUopptjentEndring-datadef-7229>
		<AvsetningerForpliktelserEndring-datadef-7232 orid="7232">' . $data['D7232'] . '</AvsetningerForpliktelserEndring-datadef-7232>
		<AvsetningerTapKontrakterMvEndring-datadef-7234 orid="7234">' . $data['D7234'] . '</AvsetningerTapKontrakterMvEndring-datadef-7234>
		<UtbytteAvsattInntektsfortEndring-datadef-7237 orid="7237">' . $data['D7237'] . '</UtbytteAvsattInntektsfortEndring-datadef-7237>
		<PensjonsforpliktelseNettoEndring-datadef-20146 orid="20146">' . $data['D20146'] . '</PensjonsforpliktelseNettoEndring-datadef-20146>
		<PensjonsmidlerInnskuddFondNettoEndring-datadef-20149 orid="20149">' . $data['D20149'] . '</PensjonsmidlerInnskuddFondNettoEndring-datadef-20149>
		<AvsetningerPensjonspremiefondSkattemessigEndring-datadef-7241 orid="7241">' . $data['D7241'] . '</AvsetningerPensjonspremiefondSkattemessigEndring-datadef-7241>
		<EiendelBalansefortVirkeligDifferanse-datadef-7248 orid="7248">' . $data['D7248'] . '</EiendelBalansefortVirkeligDifferanse-datadef-7248>
		<EiendelOverdragelseSkattefriGevinst-datadef-7249 orid="7249">' . $data['D7249'] . '</EiendelOverdragelseSkattefriGevinst-datadef-7249>
		<EiendelOverforingInngangsverdiVederlagDifferanse-datadef-7250 orid="7250">' . $data['D7250'] . '</EiendelOverforingInngangsverdiVederlagDifferanse-datadef-7250>
		<EndringSkattemessigRegnskapsmessig-datadef-262 orid="262">' . $data['D262'] . '</EndringSkattemessigRegnskapsmessig-datadef-262>
		<ForpliktelserOvertatteEndring-datadef-22046 orid="22046">' . $data['D22046'] . '</ForpliktelserOvertatteEndring-datadef-22046>
	</EndringIForskjeller-grp-2122>
	<AndreForskjeller-grp-3636 gruppeid="3636">
		<ForskjellerAndreSpesifisertBeskrivelse-datadef-22040 orid="22040">' . $data['D22040'] . '</ForskjellerAndreSpesifisertBeskrivelse-datadef-22040>
		<ForskjellerAndreSpesifisertFjoraret-datadef-22041 orid="22041">' . $data['D22041'] . '</ForskjellerAndreSpesifisertFjoraret-datadef-22041>
		<ForskjellerAndreSpesifisert-datadef-22042 orid="22042">' . $data['D22042'] . '</ForskjellerAndreSpesifisert-datadef-22042>
		<ForskjellerAndreSpesifisertEndring-datadef-22043 orid="22043">' . $data['D22043'] . '</ForskjellerAndreSpesifisertEndring-datadef-22043>
	</AndreForskjeller-grp-3636>
</AndreOpplysninger-grp-3715>
<ForskjellerSomIkkeErBehandletTidligere-grp-2111 gruppeid="2111">
	<AksjerOgAndreVerdipapirer-grp-2112 gruppeid="2112">
		<Fjoraret-grp-2113 gruppeid="2113">
			<AksjerMvFjoraret-datadef-6693 orid="6693">' . $data['D6693'] . '</AksjerMvFjoraret-datadef-6693>
			<AksjerMvSkattemessigVerdiFjoraret-datadef-7195 orid="7195">' . $data['D7195'] . '</AksjerMvSkattemessigVerdiFjoraret-datadef-7195>
			<ForskjellerAksjerMvFjoraret-datadef-7251 orid="7251">' . $data['D7251'] . '</ForskjellerAksjerMvFjoraret-datadef-7251>
		</Fjoraret-grp-2113>
		<DetteAr-grp-2114 gruppeid="2114">
			<AksjerMvRegnskapsmessigVerdi-datadef-22769 orid="22769">' . $data['D22769'] . '</AksjerMvRegnskapsmessigVerdi-datadef-22769>
			<AksjerMvSkattemessigVerdiForskjeller-datadef-19469 orid="19469">' . $data['D19469'] . '</AksjerMvSkattemessigVerdiForskjeller-datadef-19469>
			<ForskjellerAksjerMv-datadef-7252 orid="7252">' . $data['D7252'] . '</ForskjellerAksjerMv-datadef-7252>
		</DetteAr-grp-2114>
	</AksjerOgAndreVerdipapirer-grp-2112>
	<AndelerIDeltakerliknedeSelskaper-grp-4048 gruppeid="4048">
		<Fjoraret-grp-4049 gruppeid="4049">
			<AndelerDeltakerlignedeSelskaperFjoraret-datadef-6694 orid="6694">' . $data['D6694'] . '</AndelerDeltakerlignedeSelskaperFjoraret-datadef-6694>
			<AndelerDeltakerlignedeSelskaperSkattemessigFjoraret-datadef-7196 orid="7196">' . $data['D7196'] . '</AndelerDeltakerlignedeSelskaperSkattemessigFjoraret-datadef-7196>
			<ForskjellerAndelerDeltakerlignedeSelskaperFjoraret-datadef-7253 orid="7253">' . $data['D7253'] . '</ForskjellerAndelerDeltakerlignedeSelskaperFjoraret-datadef-7253>
		</Fjoraret-grp-4049>
		<DetteAr-grp-4050 gruppeid="4050">
			<AndelerDeltakerlignedeSelskaper-datadef-195 orid="195">' . $data['D195'] . '</AndelerDeltakerlignedeSelskaper-datadef-195>
			<AndelerDeltakerlignedeSelskaperSkattemessigVerdi-datadef-1015 orid="1015">' . $data['D1015'] . '</AndelerDeltakerlignedeSelskaperSkattemessigVerdi-datadef-1015>
			<ForskjellerAndelerDeltakerlignedeSelskaper-datadef-7254 orid="7254">' . $data['D7254'] . '</ForskjellerAndelerDeltakerlignedeSelskaper-datadef-7254>
		</DetteAr-grp-4050>
	</AndelerIDeltakerliknedeSelskaper-grp-4048>
	<AkkumulertFramforbartSkattemessigUnderskudd-grp-4051 gruppeid="4051">
		<Fjoraret-grp-4052 gruppeid="4052">
			<UnderskuddFramforbartSkattemessigFjoraret-datadef-7255 orid="7255">' . $data['D7255'] . '</UnderskuddFramforbartSkattemessigFjoraret-datadef-7255>
			<EiendelerRealisasjonGevinstLatentSkattemessigFjoraret-datadef-7256 orid="7256">' . $data['D7256'] . '</EiendelerRealisasjonGevinstLatentSkattemessigFjoraret-datadef-7256>
			<GodtgjorelseOmregnetUbenyttetFjoraret-datadef-7258 orid="7258">' . $data['D7258'] . '</GodtgjorelseOmregnetUbenyttetFjoraret-datadef-7258>
			<KorreksjonsinntektAkkumulertFremfortFjoraret-datadef-7259 orid="7259">' . $data['D7259'] . '</KorreksjonsinntektAkkumulertFremfortFjoraret-datadef-7259>
			<ForskjellerAndreUtsattSkattSkattefordelFjoraret-datadef-14278 orid="14278">' . $data['D14278'] . '</ForskjellerAndreUtsattSkattSkattefordelFjoraret-datadef-14278>
		</Fjoraret-grp-4052>
		<DetteAr-grp-4053 gruppeid="4053">
			<UnderskuddFramforbartAkkumulert-datadef-14859 orid="14859">' . $data['D14859'] . '</UnderskuddFramforbartAkkumulert-datadef-14859>
			<EiendelerRealisasjonGevinstLatentSkattemessig-datadef-7257 orid="7257">' . $data['D7257'] . '</EiendelerRealisasjonGevinstLatentSkattemessig-datadef-7257>
			<GodtgjorelseOmregnetUbenyttet-datadef-7199 orid="7199">' . $data['D7199'] . '</GodtgjorelseOmregnetUbenyttet-datadef-7199>
			<ForskjellerNegative-datadef-1105 orid="1105">' . $data['D1105'] . '</ForskjellerNegative-datadef-1105>
			<KorreksjonsinntektFramforbar-datadef-2220 orid="2220">' . $data['D2220'] . '</KorreksjonsinntektFramforbar-datadef-2220>
			<Korreksjonsinntekt-datadef-1098 orid="1098">' . $data['D1098'] . '</Korreksjonsinntekt-datadef-1098>
			<ForskjellerAndreUtsattSkattSkattefordel-datadef-11700 orid="11700">' . $data['D11700'] . '</ForskjellerAndreUtsattSkattSkattefordel-datadef-11700>
		</DetteAr-grp-4053>
	</AkkumulertFramforbartSkattemessigUnderskudd-grp-4051>
</ForskjellerSomIkkeErBehandletTidligere-grp-2111>
<SumForskjeller-grp-133 gruppeid="133">
	<SumForskjellerFjoraret-grp-2116 gruppeid="2116">
		<ForskjellerPositiveFjoraret-datadef-7263 orid="7263">' . $data['D7263'] . '</ForskjellerPositiveFjoraret-datadef-7263>
		<ForskjellerNegativeInkludertKorreksjonsinntektFjoraret-datadef-7260 orid="7260">' . $data['D7260'] . '</ForskjellerNegativeInkludertKorreksjonsinntektFjoraret-datadef-7260>
		<ForskjellerNegativAndelUtlignbareFjoraret-datadef-7261 orid="7261">' . $data['D7261'] . '</ForskjellerNegativAndelUtlignbareFjoraret-datadef-7261>
		<GrunnlagUtsattSkattSkattefordelFjoraret-datadef-11256 orid="11256">' . $data['D11256'] . '</GrunnlagUtsattSkattSkattefordelFjoraret-datadef-11256>
	</SumForskjellerFjoraret-grp-2116>
	<SumForskjellerDetteAr-grp-2117 gruppeid="2117">
		<ForskjellerPositive-datadef-1106 orid="1106">' . $data['D1106'] . '</ForskjellerPositive-datadef-1106>
		<ForskjellerNegativInkludertKorreksjonsinntekt-datadef-7264 orid="7264">' . $data['D7264'] . '</ForskjellerNegativInkludertKorreksjonsinntekt-datadef-7264>
		<ForskjellerNegativAndelUtlignbare-datadef-7262 orid="7262">' . $data['D7262'] . '</ForskjellerNegativAndelUtlignbare-datadef-7262>
		<InntektsforingOvergangsregelC-datadef-22859 orid="22859">' . $data['D22859'] . '</InntektsforingOvergangsregelC-datadef-22859>
		<GrunnlagUtsattSkattSkattefordel-datadef-11257 orid="11257">' . $data['D11257'] . '</GrunnlagUtsattSkattSkattefordel-datadef-11257>
	</SumForskjellerDetteAr-grp-2117>
</SumForskjeller-grp-133>
';
}
?>