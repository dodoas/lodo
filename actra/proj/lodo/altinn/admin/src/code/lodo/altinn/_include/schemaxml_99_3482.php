<?php
// RF-1028  Selvangivelse for aksjeselskaper, verdipapirfond, banker mv

if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-850 gruppeid="850">
	<Avgiver-grp-142 gruppeid="142">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetOrganisasjonsform-datadef-756 orid="756">' . $data['D756'] . '</EnhetOrganisasjonsform-datadef-756>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<ForhandsligningOnske-datadef-17132 orid="17132">' . $data['D17132'] . '</ForhandsligningOnske-datadef-17132>
		<ForhandsligningAr-datadef-19679 orid="19679">' . $data['D19679'] . '</ForhandsligningAr-datadef-19679>
		<Kreditfradrag-datadef-6680 orid="6680">' . $data['D6680'] . '</Kreditfradrag-datadef-6680>
		<SkattInnbetaltTilbakebetalingKontonummer-datadef-19784 orid="19784">' . $data['D19784'] . '</SkattInnbetaltTilbakebetalingKontonummer-datadef-19784>
	</Avgiver-grp-142>
	<Regnskapsforer-grp-594 gruppeid="594">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-594>
	<EnhetSignaturNavn-datadef-13943 orid="13943">' . $data['D13943'] . '</EnhetSignaturNavn-datadef-13943>
</GenerellInformasjon-grp-850>';	
}
else
{
$xml = '<GenerellInformasjon-grp-850 gruppeid="850">
	<Avgiver-grp-142 gruppeid="142">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetOrganisasjonsform-datadef-756 orid="756">' . $data['D756'] . '</EnhetOrganisasjonsform-datadef-756>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetKommune-datadef-29 orid="29">' . $data['D29'] . '</EnhetKommune-datadef-29>
		<ForhandsligningOnske-datadef-17132 orid="17132">' . $data['D17132'] . '</ForhandsligningOnske-datadef-17132>
		<ForhandsligningAr-datadef-19679 orid="19679">' . $data['D19679'] . '</ForhandsligningAr-datadef-19679>
		<Kreditfradrag-datadef-6680 orid="6680">' . $data['D6680'] . '</Kreditfradrag-datadef-6680>
		<SkattInnbetaltTilbakebetalingKontonummer-datadef-19784 orid="19784">' . $data['D19784'] . '</SkattInnbetaltTilbakebetalingKontonummer-datadef-19784>
	</Avgiver-grp-142>
	<Regnskapsforer-grp-594 gruppeid="594">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-594>
	<EnhetSignaturNavn-datadef-13943 orid="13943">' . $data['D13943'] . '</EnhetSignaturNavn-datadef-13943>
</GenerellInformasjon-grp-850>
<Selskapet-grp-144 gruppeid="144">
	<FlyttingAvHovedkontor-grp-3514 gruppeid="3514">
		<EnhetHovedkontorKommuneEndring-datadef-6849 orid="6849">' . $data['D6849'] . '</EnhetHovedkontorKommuneEndring-datadef-6849>
		<EnhetHovedkontorFlyttetDato-datadef-5909 orid="5909">' . $data['D5909'] . '</EnhetHovedkontorFlyttetDato-datadef-5909>
	</FlyttingAvHovedkontor-grp-3514>
	<EnhetBorsnotert-datadef-2210 orid="2210">' . $data['D2210'] . '</EnhetBorsnotert-datadef-2210>
	<EnhetStiftelsesdato-datadef-19540 orid="19540">' . $data['D19540'] . '</EnhetStiftelsesdato-datadef-19540>
	<EnhetOmdannelseENKANSKS-datadef-2228 orid="2228">' . $data['D2228'] . '</EnhetOmdannelseENKANSKS-datadef-2228>
	<EnhetOmdannelseSkattefri-datadef-5905 orid="5905">' . $data['D5905'] . '</EnhetOmdannelseSkattefri-datadef-5905>
	<EnhetStiftetFisjon-datadef-5910 orid="5910">' . $data['D5910'] . '</EnhetStiftetFisjon-datadef-5910>
	<Innfusjonert-grp-3508 gruppeid="3508">
		<EnhetOverdragendeInnfusjonerteOrganisasjonsnummer-datadef-1996 orid="1996">' . $data['D1996'] . '</EnhetOverdragendeInnfusjonerteOrganisasjonsnummer-datadef-1996>
		<EnhetOverdragendeInnfusjonerteNavn-datadef-1994 orid="1994">' . $data['D1994'] . '</EnhetOverdragendeInnfusjonerteNavn-datadef-1994>
	</Innfusjonert-grp-3508>
	<Utfisjonert-grp-3509 gruppeid="3509">
		<EnhetOvertakendeUtfisjonerteOrganisasjonsnummer-datadef-2009 orid="2009">' . $data['D2009'] . '</EnhetOvertakendeUtfisjonerteOrganisasjonsnummer-datadef-2009>
		<EnhetOvertakendeUtfisjonerteNavn-datadef-2008 orid="2008">' . $data['D2008'] . '</EnhetOvertakendeUtfisjonerteNavn-datadef-2008>
	</Utfisjonert-grp-3509>
	<FisjonFusjonSkattefri-datadef-5906 orid="5906">' . $data['D5906'] . '</FisjonFusjonSkattefri-datadef-5906>
	<EiendelerOverforingerSkattefrie-datadef-7674 orid="7674">' . $data['D7674'] . '</EiendelerOverforingerSkattefrie-datadef-7674>
	<KonsernspissNorskOrganisasjonsnummer-datadef-19851 orid="19851">' . $data['D19851'] . '</KonsernspissNorskOrganisasjonsnummer-datadef-19851>
	<SelskapLigningSkattelov810-datadef-22078 orid="22078">' . $data['D22078'] . '</SelskapLigningSkattelov810-datadef-22078>
</Selskapet-grp-144>
<Aksjonar-grp-145 gruppeid="145">
	<YtelserFraSelskapTilAksjonar-grp-3510 gruppeid="3510">
		<UtlanSelskap-datadef-11299 orid="11299">' . $data['D11299'] . '</UtlanSelskap-datadef-11299>
		<RenterUtlanSelskap-datadef-11300 orid="11300">' . $data['D11300'] . '</RenterUtlanSelskap-datadef-11300>
		<UtleieFormuesgjenstanderSelskap-datadef-11301 orid="11301">' . $data['D11301'] . '</UtleieFormuesgjenstanderSelskap-datadef-11301>
		<SalgUttakFormuesgjenstanderMvSelskap-datadef-11302 orid="11302">' . $data['D11302'] . '</SalgUttakFormuesgjenstanderMvSelskap-datadef-11302>
	</YtelserFraSelskapTilAksjonar-grp-3510>
	<YtelserFraAksjonarTilSelskap-grp-3511 gruppeid="3511">
		<UtlanAksjonar-datadef-11303 orid="11303">' . $data['D11303'] . '</UtlanAksjonar-datadef-11303>
		<RenterUtlanAksjonar-datadef-11304 orid="11304">' . $data['D11304'] . '</RenterUtlanAksjonar-datadef-11304>
		<UtleieFormuesgjenstandAksjonar-datadef-11305 orid="11305">' . $data['D11305'] . '</UtleieFormuesgjenstandAksjonar-datadef-11305>
		<SalgFormuesgjenstanderAksjonar-datadef-11306 orid="11306">' . $data['D11306'] . '</SalgFormuesgjenstanderAksjonar-datadef-11306>
		<UtbytteUtdeltBesluttet-datadef-13969 orid="13969">' . $data['D13969'] . '</UtbytteUtdeltBesluttet-datadef-13969>
		<AkjonarLaneopptakSikkerhetsstillelse-datadef-19852 orid="19852">' . $data['D19852'] . '</AkjonarLaneopptakSikkerhetsstillelse-datadef-19852>
		<AksjonarLaneopptakSikkerhetstillelseTilsvarendeVerdi-datadef-19853 orid="19853">' . $data['D19853'] . '</AksjonarLaneopptakSikkerhetstillelseTilsvarendeVerdi-datadef-19853>
	</YtelserFraAksjonarTilSelskap-grp-3511>
</Aksjonar-grp-145>
<ForSamvirkeforetak-grp-4988 gruppeid="4988">
	<OmsetningSamvirkeforetakMedlemmerEgetLag-datadef-22153 orid="22153">' . $data['D22153'] . '</OmsetningSamvirkeforetakMedlemmerEgetLag-datadef-22153>
	<KjopsutbytteSamvirkeforetakAvsattMedlemmerEgetLag-datadef-22154 orid="22154">' . $data['D22154'] . '</KjopsutbytteSamvirkeforetakAvsattMedlemmerEgetLag-datadef-22154>
	<OmsetningSamvirkeforetakMedlemmerAndreLag-datadef-22156 orid="22156">' . $data['D22156'] . '</OmsetningSamvirkeforetakMedlemmerAndreLag-datadef-22156>
	<KjopsutbytteSamvirkeforetakAvsattMedlemmerAndreLag-datadef-22155 orid="22155">' . $data['D22155'] . '</KjopsutbytteSamvirkeforetakAvsattMedlemmerAndreLag-datadef-22155>
	<OmsetningSamvirkeforetak-datadef-22152 orid="22152">' . $data['D22152'] . '</OmsetningSamvirkeforetak-datadef-22152>
	<OmsetningSamvirkeforetakMedlemmerEgetLagProsentandel-datadef-22157 orid="22157">' . $data['D22157'] . '</OmsetningSamvirkeforetakMedlemmerEgetLagProsentandel-datadef-22157>
</ForSamvirkeforetak-grp-4988>
<Inntekt-grp-3519 gruppeid="3519">
	<Inntekt-grp-4268 gruppeid="4268">
		<InntektNaring-datadef-1080 orid="1080">' . $data['D1080'] . '</InntektNaring-datadef-1080>
		<InntekterFastEiendomNettoSkattemessig-datadef-6854 orid="6854">' . $data['D6854'] . '</InntekterFastEiendomNettoSkattemessig-datadef-6854>
		<AksjeutbytteUtenlandskeSelskaperSkattemessig-datadef-1109 orid="1109">' . $data['D1109'] . '</AksjeutbytteUtenlandskeSelskaperSkattemessig-datadef-1109>
		<KonsernbidragMottattSkattemessig-datadef-6855 orid="6855">' . $data['D6855'] . '</KonsernbidragMottattSkattemessig-datadef-6855>
		<AksjonarbidragMottattSkattemessig-datadef-2234 orid="2234">' . $data['D2234'] . '</AksjonarbidragMottattSkattemessig-datadef-2234>
		<AndreInntekter-grp-3512 gruppeid="3512">
			<InntekterAndreSkattemessigBeskrivelseSpesifisert-datadef-13970 orid="13970">' . $data['D13970'] . '</InntekterAndreSkattemessigBeskrivelseSpesifisert-datadef-13970>
			<InntekterAndreSkattemessigSpesifisert-datadef-14214 orid="14214">' . $data['D14214'] . '</InntekterAndreSkattemessigSpesifisert-datadef-14214>
		</AndreInntekter-grp-3512>
		<OverskuddKontinentalsokkel-datadef-22079 orid="22079">' . $data['D22079'] . '</OverskuddKontinentalsokkel-datadef-22079>
		<InntektSkattbarBrutto-datadef-6856 orid="6856">' . $data['D6856'] . '</InntektSkattbarBrutto-datadef-6856>
	</Inntekt-grp-4268>
</Inntekt-grp-3519>
<FradragIInntekten-grp-3520 gruppeid="3520">
	<FradragIInntekt-grp-149 gruppeid="149">
		<UnderskuddNaring-datadef-1225 orid="1225">' . $data['D1225'] . '</UnderskuddNaring-datadef-1225>
		<KorreksjonsinntektFramfortTilFradrag-datadef-6876 orid="6876">' . $data['D6876'] . '</KorreksjonsinntektFramfortTilFradrag-datadef-6876>
		<AndreFradrag-grp-3513 gruppeid="3513">
			<NaringsinntektFradragAnnetBeskrivelse-datadef-13971 orid="13971">' . $data['D13971'] . '</NaringsinntektFradragAnnetBeskrivelse-datadef-13971>
			<FradragAndreNaringsinntekt-datadef-14825 orid="14825">' . $data['D14825'] . '</FradragAndreNaringsinntekt-datadef-14825>
		</AndreFradrag-grp-3513>
		<UnderskuddKontinentalsokkel-datadef-22080 orid="22080">' . $data['D22080'] . '</UnderskuddKontinentalsokkel-datadef-22080>
		<NaringsinntektFradragInntekt-datadef-10084 orid="10084">' . $data['D10084'] . '</NaringsinntektFradragInntekt-datadef-10084>
	</FradragIInntekt-grp-149>
</FradragIInntekten-grp-3520>
<BeregningAvInntekt-grp-3521 gruppeid="3521">
	<BeregningAvInntekt-grp-151 gruppeid="151">
		<InntektForFradragUnderskudd-datadef-6859 orid="6859">' . $data['D6859'] . '</InntektForFradragUnderskudd-datadef-6859>
		<UnderskuddFramforbartAnvendelseBegrenset-datadef-13972 orid="13972">' . $data['D13972'] . '</UnderskuddFramforbartAnvendelseBegrenset-datadef-13972>
		<AndelskapitalFelleseidSamvirkeTilleggIkkeFradragsrett-datadef-22158 orid="22158">' . $data['D22158'] . '</AndelskapitalFelleseidSamvirkeTilleggIkkeFradragsrett-datadef-22158>
		<InntektForFradragBidrag-datadef-6860 orid="6860">' . $data['D6860'] . '</InntektForFradragBidrag-datadef-6860>
		<AksjonarbidragFradragsberettiget-datadef-1151 orid="1151">' . $data['D1151'] . '</AksjonarbidragFradragsberettiget-datadef-1151>
		<KonsernbidragFradragsberettiget-datadef-1152 orid="1152">' . $data['D1152'] . '</KonsernbidragFradragsberettiget-datadef-1152>
		<InntektSkattbarNetto-datadef-6688 orid="6688">' . $data['D6688'] . '</InntektSkattbarNetto-datadef-6688>
	</BeregningAvInntekt-grp-151>
</BeregningAvInntekt-grp-3521>
<Rederibeskatning-grp-4971 gruppeid="4971">
	<FinansinntektNettoPositiv-datadef-22098 orid="22098">' . $data['D22098'] . '</FinansinntektNettoPositiv-datadef-22098>
	<UtbytteSkattepliktig-datadef-5873 orid="5873">' . $data['D5873'] . '</UtbytteSkattepliktig-datadef-5873>
	<GevinstTapskontoRederiInntektsfort-datadef-13678 orid="13678">' . $data['D13678'] . '</GevinstTapskontoRederiInntektsfort-datadef-13678>
	<InntektInntreden-datadef-12761 orid="12761">' . $data['D12761'] . '</InntektInntreden-datadef-12761>
	<InntektAlminnelig-datadef-2720 orid="2720">' . $data['D2720'] . '</InntektAlminnelig-datadef-2720>
	<Tonnasjeskatt-datadef-5857 orid="5857">' . $data['D5857'] . '</Tonnasjeskatt-datadef-5857>
</Rederibeskatning-grp-4971>
<UbenyttetFramfortKorreksjonsinntekt-grp-2132 gruppeid="2132">
	<FramfortUbenyttetKorreksjonsinntekt-grp-2135 gruppeid="2135">
		<KorreksjonsinntektFramfort1994-datadef-13973 orid="13973">' . $data['D13973'] . '</KorreksjonsinntektFramfort1994-datadef-13973>
		<KorreksjonsinntektFramfort1995-datadef-13974 orid="13974">' . $data['D13974'] . '</KorreksjonsinntektFramfort1995-datadef-13974>
		<KorreksjonsinntektFramfort1996-datadef-13975 orid="13975">' . $data['D13975'] . '</KorreksjonsinntektFramfort1996-datadef-13975>
		<KorreksjonsinntektFramfort1997-datadef-13976 orid="13976">' . $data['D13976'] . '</KorreksjonsinntektFramfort1997-datadef-13976>
		<KorreksjonsinntektFramfort1998-datadef-13977 orid="13977">' . $data['D13977'] . '</KorreksjonsinntektFramfort1998-datadef-13977>
		<KorreksjonsinntektFramfort1999-datadef-13978 orid="13978">' . $data['D13978'] . '</KorreksjonsinntektFramfort1999-datadef-13978>
		<KorreksjonsinntektFramfort2000-datadef-13979 orid="13979">' . $data['D13979'] . '</KorreksjonsinntektFramfort2000-datadef-13979>
		<KorreksjonsinntektFramfort2001-datadef-17136 orid="17136">' . $data['D17136'] . '</KorreksjonsinntektFramfort2001-datadef-17136>
		<KorreksjonsinntektFramfort2002-datadef-19854 orid="19854">' . $data['D19854'] . '</KorreksjonsinntektFramfort2002-datadef-19854>
		<KorreksjonsinntektFramfort2003-datadef-22081 orid="22081">' . $data['D22081'] . '</KorreksjonsinntektFramfort2003-datadef-22081>
		<KorreksjonsinntektFramfort-datadef-1104 orid="1104">' . $data['D1104'] . '</KorreksjonsinntektFramfort-datadef-1104>
	</FramfortUbenyttetKorreksjonsinntekt-grp-2135>
	<BeregnetTilAnvendelse-grp-2136 gruppeid="2136">
		<KorreksjonsinntektAnvendelse1994-datadef-13980 orid="13980">' . $data['D13980'] . '</KorreksjonsinntektAnvendelse1994-datadef-13980>
		<KorreksjonsinntektAnvendelse1995-datadef-13981 orid="13981">' . $data['D13981'] . '</KorreksjonsinntektAnvendelse1995-datadef-13981>
		<KorreksjonsinntektAnvendelse1996-datadef-13982 orid="13982">' . $data['D13982'] . '</KorreksjonsinntektAnvendelse1996-datadef-13982>
		<KorreksjonsinntektAnvendelse1997-datadef-13983 orid="13983">' . $data['D13983'] . '</KorreksjonsinntektAnvendelse1997-datadef-13983>
		<KorreksjonsinntektAnvendelse1998-datadef-13984 orid="13984">' . $data['D13984'] . '</KorreksjonsinntektAnvendelse1998-datadef-13984>
		<KorreksjonsinntektAnvendelse1999-datadef-13985 orid="13985">' . $data['D13985'] . '</KorreksjonsinntektAnvendelse1999-datadef-13985>
		<KorreksjonsinntektAnvendelse2000-datadef-13986 orid="13986">' . $data['D13986'] . '</KorreksjonsinntektAnvendelse2000-datadef-13986>
		<KorreksjonsinntektAnvendelse2001-datadef-17137 orid="17137">' . $data['D17137'] . '</KorreksjonsinntektAnvendelse2001-datadef-17137>
		<KorreksjonsinntektAnvendelse2002-datadef-19855 orid="19855">' . $data['D19855'] . '</KorreksjonsinntektAnvendelse2002-datadef-19855>
		<KorreksjonsinntektAnvendelse2003-datadef-22082 orid="22082">' . $data['D22082'] . '</KorreksjonsinntektAnvendelse2003-datadef-22082>
		<KorreksjonsinntektAnvendelse-datadef-7678 orid="7678">' . $data['D7678'] . '</KorreksjonsinntektAnvendelse-datadef-7678>
	</BeregnetTilAnvendelse-grp-2136>
	<RestTilFramforing-grp-2137 gruppeid="2137">
		<KorreksjonsinntektFramforbar1994-datadef-13987 orid="13987">' . $data['D13987'] . '</KorreksjonsinntektFramforbar1994-datadef-13987>
		<KorreksjonsinntektFramforbar1995-datadef-13988 orid="13988">' . $data['D13988'] . '</KorreksjonsinntektFramforbar1995-datadef-13988>
		<KorreksjonsinntektFramforbar1996-datadef-13989 orid="13989">' . $data['D13989'] . '</KorreksjonsinntektFramforbar1996-datadef-13989>
		<KorreksjonsinntektFramforbar1997-datadef-13990 orid="13990">' . $data['D13990'] . '</KorreksjonsinntektFramforbar1997-datadef-13990>
		<KorreksjonsinntektFramforbar1998-datadef-13991 orid="13991">' . $data['D13991'] . '</KorreksjonsinntektFramforbar1998-datadef-13991>
		<KorreksjonsinntektFramforbar1999-datadef-13992 orid="13992">' . $data['D13992'] . '</KorreksjonsinntektFramforbar1999-datadef-13992>
		<KorreksjonsinntektFramforbar2000-datadef-13993 orid="13993">' . $data['D13993'] . '</KorreksjonsinntektFramforbar2000-datadef-13993>
		<KorreksjonsinntektFramforbar2001-datadef-17138 orid="17138">' . $data['D17138'] . '</KorreksjonsinntektFramforbar2001-datadef-17138>
		<KorreksjonsinntektFramforbart2002-datadef-19856 orid="19856">' . $data['D19856'] . '</KorreksjonsinntektFramforbart2002-datadef-19856>
		<KorreksjonsinntektFramforbart2003-datadef-22083 orid="22083">' . $data['D22083'] . '</KorreksjonsinntektFramforbart2003-datadef-22083>
		<KorreksjonsinntektFramforbar-datadef-2220 orid="2220">' . $data['D2220'] . '</KorreksjonsinntektFramforbar-datadef-2220>
	</RestTilFramforing-grp-2137>
</UbenyttetFramfortKorreksjonsinntekt-grp-2132>
<UbenyttetFramfortSkattefradrag-grp-2138 gruppeid="2138">
	<FramfortUbenyttetSkattefradrag-grp-2139 gruppeid="2139">
		<SkattefradragFramfort1994-datadef-13996 orid="13996">' . $data['D13996'] . '</SkattefradragFramfort1994-datadef-13996>
		<SkattefradragFramfort1995-datadef-13997 orid="13997">' . $data['D13997'] . '</SkattefradragFramfort1995-datadef-13997>
		<SkattefradragFramfort1996-datadef-13998 orid="13998">' . $data['D13998'] . '</SkattefradragFramfort1996-datadef-13998>
		<SkattefradragFramfort1997-datadef-13999 orid="13999">' . $data['D13999'] . '</SkattefradragFramfort1997-datadef-13999>
		<SkattefradragFramfort1998-datadef-14000 orid="14000">' . $data['D14000'] . '</SkattefradragFramfort1998-datadef-14000>
		<SkattefradragFramfort1999-datadef-14001 orid="14001">' . $data['D14001'] . '</SkattefradragFramfort1999-datadef-14001>
		<SkattefradragFramfort2000-datadef-14002 orid="14002">' . $data['D14002'] . '</SkattefradragFramfort2000-datadef-14002>
		<SkattefradragFramfort2001-datadef-17139 orid="17139">' . $data['D17139'] . '</SkattefradragFramfort2001-datadef-17139>
		<SkattefradragFramfort2002-datadef-19857 orid="19857">' . $data['D19857'] . '</SkattefradragFramfort2002-datadef-19857>
		<SkattefradragFramfort2003-datadef-22084 orid="22084">' . $data['D22084'] . '</SkattefradragFramfort2003-datadef-22084>
		<SkattefradragFramfort-datadef-6867 orid="6867">' . $data['D6867'] . '</SkattefradragFramfort-datadef-6867>
	</FramfortUbenyttetSkattefradrag-grp-2139>
	<BeregnetTilAnvendelse-grp-2140 gruppeid="2140">
		<SkattefradragAnvendelse1994-datadef-14005 orid="14005">' . $data['D14005'] . '</SkattefradragAnvendelse1994-datadef-14005>
		<SkattefradragAnvendelse1995-datadef-14006 orid="14006">' . $data['D14006'] . '</SkattefradragAnvendelse1995-datadef-14006>
		<SkattefradragAnvendelse1996-datadef-14007 orid="14007">' . $data['D14007'] . '</SkattefradragAnvendelse1996-datadef-14007>
		<SkattefradragAnvendelse1997-datadef-14008 orid="14008">' . $data['D14008'] . '</SkattefradragAnvendelse1997-datadef-14008>
		<SkattefradragAnvendelse1998-datadef-14009 orid="14009">' . $data['D14009'] . '</SkattefradragAnvendelse1998-datadef-14009>
		<SkattefradragAnvendelse1999-datadef-14010 orid="14010">' . $data['D14010'] . '</SkattefradragAnvendelse1999-datadef-14010>
		<SkattefradragAnvendelse2000-datadef-14011 orid="14011">' . $data['D14011'] . '</SkattefradragAnvendelse2000-datadef-14011>
		<SkattefradragAnvendelse2001-datadef-17140 orid="17140">' . $data['D17140'] . '</SkattefradragAnvendelse2001-datadef-17140>
		<SkattefradragAnvendelse2002-datadef-19858 orid="19858">' . $data['D19858'] . '</SkattefradragAnvendelse2002-datadef-19858>
		<SkattefradragAnvendelse2003-datadef-22085 orid="22085">' . $data['D22085'] . '</SkattefradragAnvendelse2003-datadef-22085>
		<SkattefradragAnvendelse-datadef-7679 orid="7679">' . $data['D7679'] . '</SkattefradragAnvendelse-datadef-7679>
	</BeregnetTilAnvendelse-grp-2140>
	<RestTilFramforing-grp-2141 gruppeid="2141">
		<SkattefradragFramforbart1995-datadef-14015 orid="14015">' . $data['D14015'] . '</SkattefradragFramforbart1995-datadef-14015>
		<SkattefradragFramforbart1996-datadef-14016 orid="14016">' . $data['D14016'] . '</SkattefradragFramforbart1996-datadef-14016>
		<SkattefradragFramforbart1997-datadef-14017 orid="14017">' . $data['D14017'] . '</SkattefradragFramforbart1997-datadef-14017>
		<SkattefradragFramforbart1998-datadef-14018 orid="14018">' . $data['D14018'] . '</SkattefradragFramforbart1998-datadef-14018>
		<SkattefradragFramforbart1999-datadef-14019 orid="14019">' . $data['D14019'] . '</SkattefradragFramforbart1999-datadef-14019>
		<SkattefradragFramforbart2000-datadef-14020 orid="14020">' . $data['D14020'] . '</SkattefradragFramforbart2000-datadef-14020>
		<SkattefradragFramforbart2001-datadef-17141 orid="17141">' . $data['D17141'] . '</SkattefradragFramforbart2001-datadef-17141>
		<SkattefradragFramforbart2002-datadef-19859 orid="19859">' . $data['D19859'] . '</SkattefradragFramforbart2002-datadef-19859>
		<SkattefradragFramforbart2003-datadef-22086 orid="22086">' . $data['D22086'] . '</SkattefradragFramforbart2003-datadef-22086>
		<SkattefradragFramforbart-datadef-6868 orid="6868">' . $data['D6868'] . '</SkattefradragFramforbart-datadef-6868>
	</RestTilFramforing-grp-2141>
</UbenyttetFramfortSkattefradrag-grp-2138>
<BeregningAvKorreksjonsinntekt-grp-167 gruppeid="167">
	<AksjekapitalOverkurs-datadef-15512 orid="15512">' . $data['D15512'] . '</AksjekapitalOverkurs-datadef-15512>
	<ForskjellerPositive-datadef-1106 orid="1106">' . $data['D1106'] . '</ForskjellerPositive-datadef-1106>
	<ForskjellerNegativeTillagtKorreksjonsinntekt-datadef-10071 orid="10071">' . $data['D10071'] . '</ForskjellerNegativeTillagtKorreksjonsinntekt-datadef-10071>
	<ForskjellerPositiveNetto-datadef-6869 orid="6869">' . $data['D6869'] . '</ForskjellerPositiveNetto-datadef-6869>
	<EgenkapitalandelMidlertidigeForskjeller-datadef-2219 orid="2219">' . $data['D2219'] . '</EgenkapitalandelMidlertidigeForskjeller-datadef-2219>
	<ForskjellerPermanenteAksjer-datadef-22477 orid="22477">' . $data['D22477'] . '</ForskjellerPermanenteAksjer-datadef-22477>
	<KorreksjonsinntektEgenkapitalBeskattet-datadef-6870 orid="6870">' . $data['D6870'] . '</KorreksjonsinntektEgenkapitalBeskattet-datadef-6870>
	<EgenkapitalASMv-datadef-19456 orid="19456">' . $data['D19456'] . '</EgenkapitalASMv-datadef-19456>
	<KorreksjonsinntektEgenkapitalBeskattetNetto-datadef-6871 orid="6871">' . $data['D6871'] . '</KorreksjonsinntektEgenkapitalBeskattetNetto-datadef-6871>
	<UtbytteKonsernbidragSkattemessig-datadef-6872 orid="6872">' . $data['D6872'] . '</UtbytteKonsernbidragSkattemessig-datadef-6872>
	<KorreksjonsinntektUnderdekningOverdekning-datadef-6873 orid="6873">' . $data['D6873'] . '</KorreksjonsinntektUnderdekningOverdekning-datadef-6873>
	<KorreksjonsinntektGrunnlag-datadef-14022 orid="14022">' . $data['D14022'] . '</KorreksjonsinntektGrunnlag-datadef-14022>
	<Korreksjonsinntekt-datadef-1098 orid="1098">' . $data['D1098'] . '</Korreksjonsinntekt-datadef-1098>
</BeregningAvKorreksjonsinntekt-grp-167>
<FramfortKorreksjonsinntektFraTidligereArTilAnvendelseIAr-grp-168 gruppeid="168">
	<KorreksjonsinntektOverdekning-datadef-14023 orid="14023">' . $data['D14023'] . '</KorreksjonsinntektOverdekning-datadef-14023>
	<KorreksjonsinntektBeregnetBeskattetBrutto-datadef-6874 orid="6874">' . $data['D6874'] . '</KorreksjonsinntektBeregnetBeskattetBrutto-datadef-6874>
	<KorreksjonsinntektBeregnetBeskattetDifferanse-datadef-6875 orid="6875">' . $data['D6875'] . '</KorreksjonsinntektBeregnetBeskattetDifferanse-datadef-6875>
	<KorreksjonsinntektFramfortBegrenset-datadef-22861 orid="22861">' . $data['D22861'] . '</KorreksjonsinntektFramfortBegrenset-datadef-22861>
</FramfortKorreksjonsinntektFraTidligereArTilAnvendelseIAr-grp-168>
<UbenyttetNaturressursskatt-grp-4970 gruppeid="4970">
	<NaturressursskattUbenyttet-datadef-6667 orid="6667">' . $data['D6667'] . '</NaturressursskattUbenyttet-datadef-6667>
</UbenyttetNaturressursskatt-grp-4970>
<BeregnetPersoninntektForAksjonarer-grp-733 gruppeid="733">
	<Personinntekt-datadef-1074 orid="1074">' . $data['D1074'] . '</Personinntekt-datadef-1074>
	<LiberaltYrke-datadef-1072 orid="1072">' . $data['D1072'] . '</LiberaltYrke-datadef-1072>
</BeregnetPersoninntektForAksjonarer-grp-733>
<ReguleringAvInngangsverdienPaAksjeneISelskapetRISK-grp-734 gruppeid="734">
	<RISK-datadef-1176 orid="1176">' . $data['D1176'] . '</RISK-datadef-1176>
</ReguleringAvInngangsverdienPaAksjeneISelskapetRISK-grp-734>
<UbenyttetFramfortUnderskudd-grp-2142 gruppeid="2142">
	<FramfortUbenyttetUnderskudd-grp-2143 gruppeid="2143">
		<UnderskuddFramfort1994-datadef-14027 orid="14027">' . $data['D14027'] . '</UnderskuddFramfort1994-datadef-14027>
		<UnderskuddFramfort1995-datadef-14028 orid="14028">' . $data['D14028'] . '</UnderskuddFramfort1995-datadef-14028>
		<UnderskuddFramfort1996-datadef-14029 orid="14029">' . $data['D14029'] . '</UnderskuddFramfort1996-datadef-14029>
		<UnderskuddFramfort1997-datadef-14030 orid="14030">' . $data['D14030'] . '</UnderskuddFramfort1997-datadef-14030>
		<UnderskuddFramfort1998-datadef-14031 orid="14031">' . $data['D14031'] . '</UnderskuddFramfort1998-datadef-14031>
		<UnderskuddFramfort1999-datadef-14032 orid="14032">' . $data['D14032'] . '</UnderskuddFramfort1999-datadef-14032>
		<UnderskuddFramfort2000-datadef-14033 orid="14033">' . $data['D14033'] . '</UnderskuddFramfort2000-datadef-14033>
		<UnderskuddFramfort2001-datadef-17142 orid="17142">' . $data['D17142'] . '</UnderskuddFramfort2001-datadef-17142>
		<UnderskuddFramfort2002-datadef-19860 orid="19860">' . $data['D19860'] . '</UnderskuddFramfort2002-datadef-19860>
		<UnderskuddFramfort2003-datadef-22087 orid="22087">' . $data['D22087'] . '</UnderskuddFramfort2003-datadef-22087>
		<UnderskuddUbenyttetAksjeselskapMv-datadef-14829 orid="14829">' . $data['D14829'] . '</UnderskuddUbenyttetAksjeselskapMv-datadef-14829>
	</FramfortUbenyttetUnderskudd-grp-2143>
	<BeregnetTilAnvendelse-grp-2144 gruppeid="2144">
		<UnderskuddAnvendelse1994-datadef-14037 orid="14037">' . $data['D14037'] . '</UnderskuddAnvendelse1994-datadef-14037>
		<UnderskuddAnvendelse1995-datadef-14038 orid="14038">' . $data['D14038'] . '</UnderskuddAnvendelse1995-datadef-14038>
		<UnderskuddAnvendelse1996-datadef-14039 orid="14039">' . $data['D14039'] . '</UnderskuddAnvendelse1996-datadef-14039>
		<UnderskuddAnvendelse1997-datadef-14040 orid="14040">' . $data['D14040'] . '</UnderskuddAnvendelse1997-datadef-14040>
		<UnderskuddAnvendelse1998-datadef-14041 orid="14041">' . $data['D14041'] . '</UnderskuddAnvendelse1998-datadef-14041>
		<UnderskuddAnvendelse1999-datadef-14042 orid="14042">' . $data['D14042'] . '</UnderskuddAnvendelse1999-datadef-14042>
		<UnderskuddAnvendelse2000-datadef-14043 orid="14043">' . $data['D14043'] . '</UnderskuddAnvendelse2000-datadef-14043>
		<UnderskuddAnvendelse2001-datadef-17143 orid="17143">' . $data['D17143'] . '</UnderskuddAnvendelse2001-datadef-17143>
		<UnderskuddAnvendelse2002-datadef-19861 orid="19861">' . $data['D19861'] . '</UnderskuddAnvendelse2002-datadef-19861>
		<UnderskuddAnvendelse2003-datadef-22088 orid="22088">' . $data['D22088'] . '</UnderskuddAnvendelse2003-datadef-22088>
		<UnderskuddFremforbartAksjeselskapMv-datadef-14826 orid="14826">' . $data['D14826'] . '</UnderskuddFremforbartAksjeselskapMv-datadef-14826>
	</BeregnetTilAnvendelse-grp-2144>
	<RestTilFramforing-grp-2145 gruppeid="2145">
		<UnderskuddFramforbart1995-datadef-14047 orid="14047">' . $data['D14047'] . '</UnderskuddFramforbart1995-datadef-14047>
		<UnderskuddFramforbart1996-datadef-14048 orid="14048">' . $data['D14048'] . '</UnderskuddFramforbart1996-datadef-14048>
		<UnderskuddFramforbart1997-datadef-14049 orid="14049">' . $data['D14049'] . '</UnderskuddFramforbart1997-datadef-14049>
		<UnderskuddFramforbart1998-datadef-14050 orid="14050">' . $data['D14050'] . '</UnderskuddFramforbart1998-datadef-14050>
		<UnderskuddFramforbart1999-datadef-14051 orid="14051">' . $data['D14051'] . '</UnderskuddFramforbart1999-datadef-14051>
		<UnderskuddFramforbart2000-datadef-14052 orid="14052">' . $data['D14052'] . '</UnderskuddFramforbart2000-datadef-14052>
		<UnderskuddFramforbart2001-datadef-17144 orid="17144">' . $data['D17144'] . '</UnderskuddFramforbart2001-datadef-17144>
		<UnderskuddFramforbart2002-datadef-19862 orid="19862">' . $data['D19862'] . '</UnderskuddFramforbart2002-datadef-19862>
		<UnderskuddFramforbart2003-datadef-22089 orid="22089">' . $data['D22089'] . '</UnderskuddFramforbart2003-datadef-22089>
		<UnderskuddTidligereArTilFremforing-datadef-7680 orid="7680">' . $data['D7680'] . '</UnderskuddTidligereArTilFremforing-datadef-7680>
		<UnderskuddUbenyttetSkattemessig-datadef-6881 orid="6881">' . $data['D6881'] . '</UnderskuddUbenyttetSkattemessig-datadef-6881>
		<UnderskuddTilFramforing-datadef-1159 orid="1159">' . $data['D1159'] . '</UnderskuddTilFramforing-datadef-1159>
	</RestTilFramforing-grp-2145>
</UbenyttetFramfortUnderskudd-grp-2142>
<OversiktOverUbenyttetFramfortGodtgjorelse-grp-2149 gruppeid="2149">
	<FramfortUbenyttetGodtgjorelse-grp-2150 gruppeid="2150">
		<GodtgjorelseFramfort1994-datadef-14054 orid="14054">' . $data['D14054'] . '</GodtgjorelseFramfort1994-datadef-14054>
		<GodtgjorelseFramfort1995-datadef-14055 orid="14055">' . $data['D14055'] . '</GodtgjorelseFramfort1995-datadef-14055>
		<GodtgjorelseFramfort1996-datadef-14056 orid="14056">' . $data['D14056'] . '</GodtgjorelseFramfort1996-datadef-14056>
		<GodtgjorelseFramfort1997-datadef-14057 orid="14057">' . $data['D14057'] . '</GodtgjorelseFramfort1997-datadef-14057>
		<GodtgjorelseFramfort1998-datadef-14058 orid="14058">' . $data['D14058'] . '</GodtgjorelseFramfort1998-datadef-14058>
		<GodtgjorelseFramfort1999-datadef-14059 orid="14059">' . $data['D14059'] . '</GodtgjorelseFramfort1999-datadef-14059>
		<GodtgjorelseFramfort2000-datadef-14060 orid="14060">' . $data['D14060'] . '</GodtgjorelseFramfort2000-datadef-14060>
		<GodtgjorelseFramfort2001-datadef-17145 orid="17145">' . $data['D17145'] . '</GodtgjorelseFramfort2001-datadef-17145>
		<GodtgjorelseFramfort2002-datadef-19863 orid="19863">' . $data['D19863'] . '</GodtgjorelseFramfort2002-datadef-19863>
		<GodtgjorelseFramfort2003-datadef-22090 orid="22090">' . $data['D22090'] . '</GodtgjorelseFramfort2003-datadef-22090>
		<GodtgjorelseFramfort-datadef-6885 orid="6885">' . $data['D6885'] . '</GodtgjorelseFramfort-datadef-6885>
	</FramfortUbenyttetGodtgjorelse-grp-2150>
	<BeregnetTilAnvendelse-grp-2151 gruppeid="2151">
		<GodtgjorelseAnvendelse1994-datadef-14062 orid="14062">' . $data['D14062'] . '</GodtgjorelseAnvendelse1994-datadef-14062>
		<GodtgjorelseAnvendelse1995-datadef-14063 orid="14063">' . $data['D14063'] . '</GodtgjorelseAnvendelse1995-datadef-14063>
		<GodtgjorelseAnvendelse1996-datadef-14064 orid="14064">' . $data['D14064'] . '</GodtgjorelseAnvendelse1996-datadef-14064>
		<GodtgjorelseAnvendelse1997-datadef-14065 orid="14065">' . $data['D14065'] . '</GodtgjorelseAnvendelse1997-datadef-14065>
		<GodtgjorelseAnvendelse1998-datadef-14066 orid="14066">' . $data['D14066'] . '</GodtgjorelseAnvendelse1998-datadef-14066>
		<GodtgjorelseAnvendelse1999-datadef-14067 orid="14067">' . $data['D14067'] . '</GodtgjorelseAnvendelse1999-datadef-14067>
		<GodtgjorelseAnvendelse2000-datadef-14068 orid="14068">' . $data['D14068'] . '</GodtgjorelseAnvendelse2000-datadef-14068>
		<GodtgjorelseAnvendelse2001-datadef-17146 orid="17146">' . $data['D17146'] . '</GodtgjorelseAnvendelse2001-datadef-17146>
		<GodtgjorelseAnvendelse2002-datadef-19864 orid="19864">' . $data['D19864'] . '</GodtgjorelseAnvendelse2002-datadef-19864>
		<GodtgjorelseAnvendelse2003-datadef-22091 orid="22091">' . $data['D22091'] . '</GodtgjorelseAnvendelse2003-datadef-22091>
	</BeregnetTilAnvendelse-grp-2151>
	<RestTilFramforing-grp-2152 gruppeid="2152">
		<GodtgjorelseFramforbar1995-datadef-14071 orid="14071">' . $data['D14071'] . '</GodtgjorelseFramforbar1995-datadef-14071>
		<GodtgjorelseFramforbar1996-datadef-14072 orid="14072">' . $data['D14072'] . '</GodtgjorelseFramforbar1996-datadef-14072>
		<GodtgjorelseFramforbar1997-datadef-14073 orid="14073">' . $data['D14073'] . '</GodtgjorelseFramforbar1997-datadef-14073>
		<GodtgjorelseFramforbar1998-datadef-14074 orid="14074">' . $data['D14074'] . '</GodtgjorelseFramforbar1998-datadef-14074>
		<GodtgjorelseFramforbar1999-datadef-14075 orid="14075">' . $data['D14075'] . '</GodtgjorelseFramforbar1999-datadef-14075>
		<GodtgjorelseFramforbar2000-datadef-14076 orid="14076">' . $data['D14076'] . '</GodtgjorelseFramforbar2000-datadef-14076>
		<GodtgjorelseFramforbar2001-datadef-17147 orid="17147">' . $data['D17147'] . '</GodtgjorelseFramforbar2001-datadef-17147>
		<GodtgjorelsenFramforbart2002-datadef-19865 orid="19865">' . $data['D19865'] . '</GodtgjorelsenFramforbart2002-datadef-19865>
		<GodtgjorelseFramforbart2003-datadef-22092 orid="22092">' . $data['D22092'] . '</GodtgjorelseFramforbart2003-datadef-22092>
	</RestTilFramforing-grp-2152>
</OversiktOverUbenyttetFramfortGodtgjorelse-grp-2149>
<FormueEiendom-grp-181 gruppeid="181">
	<FormueEiendom-grp-3515 gruppeid="3515">
		<EiendomTypeSpesifisertEiendom-datadef-2018 orid="2018">' . $data['D2018'] . '</EiendomTypeSpesifisertEiendom-datadef-2018>
		<EiendomAdresseSpesifisertEiendom-datadef-6843 orid="6843">' . $data['D6843'] . '</EiendomAdresseSpesifisertEiendom-datadef-6843>
		<EiendomGardsnummerSpesifisertEiendom-datadef-11756 orid="11756">' . $data['D11756'] . '</EiendomGardsnummerSpesifisertEiendom-datadef-11756>
		<EiendomBruksnummerSpesifisertEiendom-datadef-11757 orid="11757">' . $data['D11757'] . '</EiendomBruksnummerSpesifisertEiendom-datadef-11757>
		<EiendomKommunenummerSpesifisertEiendom-datadef-17038 orid="17038">' . $data['D17038'] . '</EiendomKommunenummerSpesifisertEiendom-datadef-17038>
		<EiendomSkattemessigSpesifisertEiendom-datadef-1113 orid="1113">' . $data['D1113'] . '</EiendomSkattemessigSpesifisertEiendom-datadef-1113>
	</FormueEiendom-grp-3515>
	<EiendomLigningsverdiSumEiendommer-datadef-22093 orid="22093">' . $data['D22093'] . '</EiendomLigningsverdiSumEiendommer-datadef-22093>
	<FormueEiendomUtland-grp-4969 gruppeid="4969">
		<EiendomUtlandTypeSpesifisertEiendom-datadef-22094 orid="22094">' . $data['D22094'] . '</EiendomUtlandTypeSpesifisertEiendom-datadef-22094>
		<EiendomUtlandAdresseSpesifisertEiendom-datadef-22095 orid="22095">' . $data['D22095'] . '</EiendomUtlandAdresseSpesifisertEiendom-datadef-22095>
		<EiendomUtlandLandSpesifisertEiendom-datadef-22096 orid="22096">' . $data['D22096'] . '</EiendomUtlandLandSpesifisertEiendom-datadef-22096>
		<EiendomUtlandLigningsverdiSpesifisertEiendom-datadef-22097 orid="22097">' . $data['D22097'] . '</EiendomUtlandLigningsverdiSpesifisertEiendom-datadef-22097>
	</FormueEiendomUtland-grp-4969>
	<EiendomUtlandLigningsverdiSumEiendommer-datadef-22099 orid="22099">' . $data['D22099'] . '</EiendomUtlandLigningsverdiSumEiendommer-datadef-22099>
	<FormueAnnen-grp-2156 gruppeid="2156">
		<AvskrivbartDriftslosore-grp-4038 gruppeid="4038">
			<DriftslosoreSkattemessig-datadef-1114 orid="1114">' . $data['D1114'] . '</DriftslosoreSkattemessig-datadef-1114>
			<BuskapSkattemessig-datadef-1179 orid="1179">' . $data['D1179'] . '</BuskapSkattemessig-datadef-1179>
			<LagerbeholdningAksjeselskapMv-datadef-16530 orid="16530">' . $data['D16530'] . '</LagerbeholdningAksjeselskapMv-datadef-16530>
		</AvskrivbartDriftslosore-grp-4038>
		<FartoySkattemessig-datadef-6692 orid="6692">' . $data['D6692'] . '</FartoySkattemessig-datadef-6692>
		<DriftsmidlerIkkeAvskrivbareSkattemessig-datadef-2024 orid="2024">' . $data['D2024'] . '</DriftsmidlerIkkeAvskrivbareSkattemessig-datadef-2024>
		<FritidsbatLystyach-grp-3979 gruppeid="3979">
			<FritidsbatLystyacht-datadef-14828 orid="14828">' . $data['D14828'] . '</FritidsbatLystyacht-datadef-14828>
		</FritidsbatLystyach-grp-3979>
		<FordringerUtestaendeSkattemessig-datadef-6886 orid="6886">' . $data['D6886'] . '</FordringerUtestaendeSkattemessig-datadef-6886>
		<PantobligasjonerGjeldsbrevSkattemessig-datadef-2224 orid="2224">' . $data['D2224'] . '</PantobligasjonerGjeldsbrevSkattemessig-datadef-2224>
		<BankinnskuddPostgiroinnskudd-datadef-187 orid="187">' . $data['D187'] . '</BankinnskuddPostgiroinnskudd-datadef-187>
		<Ihendehaverobligasjoner-grp-3517 gruppeid="3517">
			<IhendehaverobligasjonerAntallSpesifisert-datadef-14077 orid="14077">' . $data['D14077'] . '</IhendehaverobligasjonerAntallSpesifisert-datadef-14077>
			<IhendehaverobligasjonerPalydendeSpesifisert-datadef-14078 orid="14078">' . $data['D14078'] . '</IhendehaverobligasjonerPalydendeSpesifisert-datadef-14078>
			<IhendehaverobligasjonerArSpesifisert-datadef-14079 orid="14079">' . $data['D14079'] . '</IhendehaverobligasjonerArSpesifisert-datadef-14079>
			<IhendehaverobligasjonerKursSpesifisert-datadef-14080 orid="14080">' . $data['D14080'] . '</IhendehaverobligasjonerKursSpesifisert-datadef-14080>
			<IhendehaverobligasjonerSkattemessigSpesifisert-datadef-6887 orid="6887">' . $data['D6887'] . '</IhendehaverobligasjonerSkattemessigSpesifisert-datadef-6887>
		</Ihendehaverobligasjoner-grp-3517>
		<IhendehaverobligasjonerSkattemessigSum-datadef-22100 orid="22100">' . $data['D22100'] . '</IhendehaverobligasjonerSkattemessigSum-datadef-22100>
		<Kontanter-datadef-84 orid="84">' . $data['D84'] . '</Kontanter-datadef-84>
		<LivsforsikringspoliserSkattemessig-datadef-1115 orid="1115">' . $data['D1115'] . '</LivsforsikringspoliserSkattemessig-datadef-1115>
		<FormueAnnenSkattemessig-datadef-1116 orid="1116">' . $data['D1116'] . '</FormueAnnenSkattemessig-datadef-1116>
		<FormueBruttoUtenAndelerMvSkattemessig-datadef-6889 orid="6889">' . $data['D6889'] . '</FormueBruttoUtenAndelerMvSkattemessig-datadef-6889>
		<AksjerMvNorskeSelskaperLigningsmessigVerdi-datadef-2226 orid="2226">' . $data['D2226'] . '</AksjerMvNorskeSelskaperLigningsmessigVerdi-datadef-2226>
		<AksjerUtenlandskeSelskaper-grp-3516 gruppeid="3516">
			<AksjerMvUtenlandskeSelskaperBeskrivelseLigningSpesifisert-datadef-14082 orid="14082">' . $data['D14082'] . '</AksjerMvUtenlandskeSelskaperBeskrivelseLigningSpesifisert-datadef-14082>
			<AksjerMvUtenlandskeSelskaperLigningsmessigVerdiSpesifisert-datadef-2227 orid="2227">' . $data['D2227'] . '</AksjerMvUtenlandskeSelskaperLigningsmessigVerdiSpesifisert-datadef-2227>
		</AksjerUtenlandskeSelskaper-grp-3516>
		<FormueBruttoUpersonligeSkattemessig-datadef-10082 orid="10082">' . $data['D10082'] . '</FormueBruttoUpersonligeSkattemessig-datadef-10082>
		<GjeldUpersonligeSkattemessig-datadef-10088 orid="10088">' . $data['D10088'] . '</GjeldUpersonligeSkattemessig-datadef-10088>
		<FormueNettoUpersonligeSkattemessig-datadef-10083 orid="10083">' . $data['D10083'] . '</FormueNettoUpersonligeSkattemessig-datadef-10083>
	</FormueAnnen-grp-2156>
</FormueEiendom-grp-181>
<VerdsettelseAvAksjer-grp-187 gruppeid="187">
	<FormueBruttoUtenAndelerMvSkattemessigIkkeBorsnotert-datadef-22995 orid="22995">' . $data['D22995'] . '</FormueBruttoUtenAndelerMvSkattemessigIkkeBorsnotert-datadef-22995>
	<AksjerMvNorskeSelskaperSkattemessig-datadef-2231 orid="2231">' . $data['D2231'] . '</AksjerMvNorskeSelskaperSkattemessig-datadef-2231>
	<AksjerMvUtenlandskeSelskaperSkattemessig-datadef-2232 orid="2232">' . $data['D2232'] . '</AksjerMvUtenlandskeSelskaperSkattemessig-datadef-2232>
	<SelskapsformueUtland-datadef-6890 orid="6890">' . $data['D6890'] . '</SelskapsformueUtland-datadef-6890>
	<FormueBruttoIkkeBorsnoterte-datadef-6891 orid="6891">' . $data['D6891'] . '</FormueBruttoIkkeBorsnoterte-datadef-6891>
	<GjeldSkattemessigIkkeBorsnotert-datadef-22996 orid="22996">' . $data['D22996'] . '</GjeldSkattemessigIkkeBorsnotert-datadef-22996>
	<FormueNettoIkkeBorsnoterte-datadef-6892 orid="6892">' . $data['D6892'] . '</FormueNettoIkkeBorsnoterte-datadef-6892>
	<AksjerMvASAntall-datadef-16534 orid="16534">' . $data['D16534'] . '</AksjerMvASAntall-datadef-16534>
	<AksjeVerdiBeregnetSkattemessig-datadef-6893 orid="6893">' . $data['D6893'] . '</AksjeVerdiBeregnetSkattemessig-datadef-6893>
	<AksjerFormuesverdiSkattemessig-datadef-6894 orid="6894">' . $data['D6894'] . '</AksjerFormuesverdiSkattemessig-datadef-6894>
</VerdsettelseAvAksjer-grp-187>';
}
?>