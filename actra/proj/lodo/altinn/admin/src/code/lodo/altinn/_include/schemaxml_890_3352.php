<?php
// Filnavn: schemaxml_890_3352.php
// Skjema: RF-1086    Aksjonærregisteroppgaven
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-2587 gruppeid="2587">
	<Selskap-grp-2588 gruppeid="2588">
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetISINNummer-datadef-17513 orid="17513">' . $data['D17513'] . '</EnhetISINNummer-datadef-17513>
		<AksjeType-datadef-17659 orid="17659">' . $data['D17659'] . '</AksjeType-datadef-17659>
		<Inntektsar-datadef-692 orid="692">' . $data['D692'] . '</Inntektsar-datadef-692>
	</Selskap-grp-2588>
	<Regnskapsforer-grp-3442 gruppeid="3442">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-3442>
</GenerellInformasjon-grp-2587>
';
}
else
{
$xml = '<GenerellInformasjon-grp-2587 gruppeid="2587">
	<Selskap-grp-2588 gruppeid="2588">
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<EnhetISINNummer-datadef-17513 orid="17513">' . $data['D17513'] . '</EnhetISINNummer-datadef-17513>
		<AksjeType-datadef-17659 orid="17659">' . $data['D17659'] . '</AksjeType-datadef-17659>
		<Inntektsar-datadef-692 orid="692">' . $data['D692'] . '</Inntektsar-datadef-692>
	</Selskap-grp-2588>
	<Regnskapsforer-grp-3442 gruppeid="3442">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-3442>
</GenerellInformasjon-grp-2587>
<Selskapsopplysninger-grp-2589 gruppeid="2589">
	<AksjekapitalForHeleSelskapet-grp-3443 gruppeid="3443">
		<AksjekapitalFjoraret-datadef-7129 orid="7129">' . $data['D7129'] . '</AksjekapitalFjoraret-datadef-7129>
		<Aksjekapital-datadef-87 orid="87">' . $data['D87'] . '</Aksjekapital-datadef-87>
	</AksjekapitalForHeleSelskapet-grp-3443>
	<AksjekapitalPrISINAksjeklasse-grp-3444 gruppeid="3444">
		<AksjekapitalISINAksjetypeFjoraret-datadef-17663 orid="17663">' . $data['D17663'] . '</AksjekapitalISINAksjetypeFjoraret-datadef-17663>
		<AksjekapitalISINAksjetype-datadef-17664 orid="17664">' . $data['D17664'] . '</AksjekapitalISINAksjetype-datadef-17664>
	</AksjekapitalPrISINAksjeklasse-grp-3444>
	<PalydendePrAksjeLignVerdiPrAksje-grp-3447 gruppeid="3447">
		<AksjeMvPalydendeFjoraret-datadef-17660 orid="17660">' . $data['D17660'] . '</AksjeMvPalydendeFjoraret-datadef-17660>
		<AksjeMvPalydende-datadef-1126 orid="1126">' . $data['D1126'] . '</AksjeMvPalydende-datadef-1126>
		<AksjeMvLigningsverdi-datadef-2276 orid="2276">' . $data['D2276'] . '</AksjeMvLigningsverdi-datadef-2276>
	</PalydendePrAksjeLignVerdiPrAksje-grp-3447>
	<AntallAksjer-grp-3445 gruppeid="3445">
		<AksjerMvAntallFjoraret-datadef-7666 orid="7666">' . $data['D7666'] . '</AksjerMvAntallFjoraret-datadef-7666>
		<AksjerMvAntall-datadef-1125 orid="1125">' . $data['D1125'] . '</AksjerMvAntall-datadef-1125>
	</AntallAksjer-grp-3445>
	<InnbetaltAksjekapital-grp-3446 gruppeid="3446">
		<AksjekapitalInnbetaltFjoraret-datadef-8020 orid="8020">' . $data['D8020'] . '</AksjekapitalInnbetaltFjoraret-datadef-8020>
		<AksjekapitalInnbetalt-datadef-5867 orid="5867">' . $data['D5867'] . '</AksjekapitalInnbetalt-datadef-5867>
	</InnbetaltAksjekapital-grp-3446>
	<InnbetaltOverkursPrISINAksjeklasse-grp-3448 gruppeid="3448">
		<AksjeOverkursISINAksjetypeFjoraret-datadef-17662 orid="17662">' . $data['D17662'] . '</AksjeOverkursISINAksjetypeFjoraret-datadef-17662>
		<AksjeOverkursISINAksjetype-datadef-17661 orid="17661">' . $data['D17661'] . '</AksjeOverkursISINAksjetype-datadef-17661>
	</InnbetaltOverkursPrISINAksjeklasse-grp-3448>
</Selskapsopplysninger-grp-2589>
<Utbytte-grp-3449 gruppeid="3449">
	<AvsattUtbytteHensyntattVedRISKBeregningen-grp-3450 gruppeid="3450">
		<UtbytteAvsattFjoraret-datadef-7171 orid="7171">' . $data['D7171'] . '</UtbytteAvsattFjoraret-datadef-7171>
	</AvsattUtbytteHensyntattVedRISKBeregningen-grp-3450>
	<UtdeltSkatterettsligUtbytteILopetAvInntektsaret-grp-3451 gruppeid="3451">
		<AksjeUtbytteISINAksjetype-datadef-17665 orid="17665">' . $data['D17665'] . '</AksjeUtbytteISINAksjetype-datadef-17665>
		<AksjeUtbyttePrAksje-datadef-17666 orid="17666">' . $data['D17666'] . '</AksjeUtbyttePrAksje-datadef-17666>
		<AksjeUtbytteTidspunkt-datadef-17667 orid="17667">' . $data['D17667'] . '</AksjeUtbytteTidspunkt-datadef-17667>
	</UtdeltSkatterettsligUtbytteILopetAvInntektsaret-grp-3451>
</Utbytte-grp-3449>
<UtstedelseAvAksjerIfmStiftelseNyemisjonMv-grp-3452 gruppeid="3452">
	<AntallNyutstedteAksjer-grp-3453 gruppeid="3453">
		<AksjerNyutstedteStiftelseMvAntall-datadef-17668 orid="17668">' . $data['D17668'] . '</AksjerNyutstedteStiftelseMvAntall-datadef-17668>
		<AksjerStiftelseMvAntall-datadef-17669 orid="17669">' . $data['D17669'] . '</AksjerStiftelseMvAntall-datadef-17669>
		<AksjerNyutstedteStiftelseMvType-datadef-17670 orid="17670">' . $data['D17670'] . '</AksjerNyutstedteStiftelseMvType-datadef-17670>
		<AksjerNyutstedteStiftelseMvTidspunkt-datadef-17671 orid="17671">' . $data['D17671'] . '</AksjerNyutstedteStiftelseMvTidspunkt-datadef-17671>
		<AksjerNyutstedteStiftelseMvPalydende-datadef-17672 orid="17672">' . $data['D17672'] . '</AksjerNyutstedteStiftelseMvPalydende-datadef-17672>
		<AksjerNyutstedteStiftelseMvOverkurs-datadef-17673 orid="17673">' . $data['D17673'] . '</AksjerNyutstedteStiftelseMvOverkurs-datadef-17673>
		<AksjerNyutstedteStiftelseMvOverfortEgneAntall-datadef-17674 orid="17674">' . $data['D17674'] . '</AksjerNyutstedteStiftelseMvOverfortEgneAntall-datadef-17674>
		<EnhetOverdragendeStiftelseMvOrganisasjonsnummer-datadef-17675 orid="17675">' . $data['D17675'] . '</EnhetOverdragendeStiftelseMvOrganisasjonsnummer-datadef-17675>
		<EnhetOvertakendeStiftelseMvOrganisasjonsnummer-datadef-17676 orid="17676">' . $data['D17676'] . '</EnhetOvertakendeStiftelseMvOrganisasjonsnummer-datadef-17676>
	</AntallNyutstedteAksjer-grp-3453>
</UtstedelseAvAksjerIfmStiftelseNyemisjonMv-grp-3452>
<UtstedelseAvAksjerIfmFondsemisjonSplittMv-grp-3454 gruppeid="3454">
	<AntallNyutstedteAksjerOmfordeling-grp-3455 gruppeid="3455">
		<AksjerNyutstedteFondsemisjonMvAntall-datadef-17677 orid="17677">' . $data['D17677'] . '</AksjerNyutstedteFondsemisjonMvAntall-datadef-17677>
		<AksjerNyutstedteFondsemisjonMvAntallEtter-datadef-17678 orid="17678">' . $data['D17678'] . '</AksjerNyutstedteFondsemisjonMvAntallEtter-datadef-17678>
		<AksjerNyutstedteFondsemisjonMvType-datadef-17679 orid="17679">' . $data['D17679'] . '</AksjerNyutstedteFondsemisjonMvType-datadef-17679>
		<AksjerNyutstedteFondsemisjonMvTidspunkt-datadef-17680 orid="17680">' . $data['D17680'] . '</AksjerNyutstedteFondsemisjonMvTidspunkt-datadef-17680>
		<AksjerNyutstedteFondsemisjonMvPalydende-datadef-17681 orid="17681">' . $data['D17681'] . '</AksjerNyutstedteFondsemisjonMvPalydende-datadef-17681>
		<AksjerNyutstedteFondsemisjonMvEgneOverfortAntall-datadef-17682 orid="17682">' . $data['D17682'] . '</AksjerNyutstedteFondsemisjonMvEgneOverfortAntall-datadef-17682>
		<EnhetOverdragendeFondsemisjonMvOrganisasjonsnummer-datadef-17683 orid="17683">' . $data['D17683'] . '</EnhetOverdragendeFondsemisjonMvOrganisasjonsnummer-datadef-17683>
		<AksjerNyutstedteFondsemisjonMvISIN-datadef-17684 orid="17684">' . $data['D17684'] . '</AksjerNyutstedteFondsemisjonMvISIN-datadef-17684>
		<AksjerNyutstedteFondsemisjonMvAksjetype-datadef-19905 orid="19905">' . $data['D19905'] . '</AksjerNyutstedteFondsemisjonMvAksjetype-datadef-19905>
		<AksjerNyutstedteFondsemisjonMvInnlosteAntall-datadef-17685 orid="17685">' . $data['D17685'] . '</AksjerNyutstedteFondsemisjonMvInnlosteAntall-datadef-17685>
		<AksjerNyutstedteFondsemisjonMvInnlostPalydende-datadef-17686 orid="17686">' . $data['D17686'] . '</AksjerNyutstedteFondsemisjonMvInnlostPalydende-datadef-17686>
		<EnhetOvertakendeKonsernfusjonKonsernfisjonOrganisasjonnummer-datadef-17687 orid="17687">' . $data['D17687'] . '</EnhetOvertakendeKonsernfusjonKonsernfisjonOrganisasjonnummer-datadef-17687>
	</AntallNyutstedteAksjerOmfordeling-grp-3455>
</UtstedelseAvAksjerIfmFondsemisjonSplittMv-grp-3454>
<SlettingAvAksjerIfmLikvidasjonPartiellLikvidasjonMv-grp-3456 gruppeid="3456">
	<AntallSlettedeAksjerAvgang-grp-3457 gruppeid="3457">
		<AksjerSlettedeLikvidasjonMvAntall-datadef-17688 orid="17688">' . $data['D17688'] . '</AksjerSlettedeLikvidasjonMvAntall-datadef-17688>
		<AksjerLividasjonMvAntall-datadef-17689 orid="17689">' . $data['D17689'] . '</AksjerLividasjonMvAntall-datadef-17689>
		<AksjerSlettedeLikvidasjonMvPalydende-datadef-17690 orid="17690">' . $data['D17690'] . '</AksjerSlettedeLikvidasjonMvPalydende-datadef-17690>
		<AksjerSlettedeLikvidasjonMvType-datadef-17691 orid="17691">' . $data['D17691'] . '</AksjerSlettedeLikvidasjonMvType-datadef-17691>
		<AksjerSlettedeLividasjonMvTidspunkt-datadef-17692 orid="17692">' . $data['D17692'] . '</AksjerSlettedeLividasjonMvTidspunkt-datadef-17692>
		<AksjerSlettedeLikvidasjonMvVederlag-datadef-17770 orid="17770">' . $data['D17770'] . '</AksjerSlettedeLikvidasjonMvVederlag-datadef-17770>
	</AntallSlettedeAksjerAvgang-grp-3457>
</SlettingAvAksjerIfmLikvidasjonPartiellLikvidasjonMv-grp-3456>
<SlettingAvAksjerIfmSpleisSkattefriFusjonFisjon-grp-3458 gruppeid="3458">
	<AntallSlettedeAksjerOmfordeling-grp-3459 gruppeid="3459">
		<AksjerSlettedeSpleisMvAntall-datadef-17693 orid="17693">' . $data['D17693'] . '</AksjerSlettedeSpleisMvAntall-datadef-17693>
		<AksjerSpleisAntall-datadef-17694 orid="17694">' . $data['D17694'] . '</AksjerSpleisAntall-datadef-17694>
		<AksjerSlettedeSpleisMvType-datadef-17695 orid="17695">' . $data['D17695'] . '</AksjerSlettedeSpleisMvType-datadef-17695>
		<AksjerSlettedeSpleisMvTidspunkt-datadef-17696 orid="17696">' . $data['D17696'] . '</AksjerSlettedeSpleisMvTidspunkt-datadef-17696>
		<AksjerSlettedeFisjonPalydende-datadef-17697 orid="17697">' . $data['D17697'] . '</AksjerSlettedeFisjonPalydende-datadef-17697>
		<AksjerSlettedeSpleisPalydende-datadef-17698 orid="17698">' . $data['D17698'] . '</AksjerSlettedeSpleisPalydende-datadef-17698>
		<EnhetSlettedeSpleisMvDatterselskaovertakendeOrganisasjonsnumm-datadef-20373 orid="20373">' . $data['D20373'] . '</EnhetSlettedeSpleisMvDatterselskaovertakendeOrganisasjonsnumm-datadef-20373>
		<AksjerSlettedeSpleisMvDatterselskapOvertakendeISINType-datadef-20374 orid="20374">' . $data['D20374'] . '</AksjerSlettedeSpleisMvDatterselskapOvertakendeISINType-datadef-20374>
		<AksjerSlettedeSpleisMvDatterselskapOvertakendeAksjetype-datadef-20375 orid="20375">' . $data['D20375'] . '</AksjerSlettedeSpleisMvDatterselskapOvertakendeAksjetype-datadef-20375>
		<AksjerSlettedeSpleisMvOvertakendeAntall-datadef-17701 orid="17701">' . $data['D17701'] . '</AksjerSlettedeSpleisMvOvertakendeAntall-datadef-17701>
		<AksjerSlettedeSpleisMvOvertakendePalydende-datadef-17702 orid="17702">' . $data['D17702'] . '</AksjerSlettedeSpleisMvOvertakendePalydende-datadef-17702>
		<EnhetSlettedeSpleisMvMorselskapOvertakendeOrganisasjonsnumm-datadef-17703 orid="17703">' . $data['D17703'] . '</EnhetSlettedeSpleisMvMorselskapOvertakendeOrganisasjonsnumm-datadef-17703>
		<AksjerSlettedeSpleisMvMorselskapOvertakendeISINType-datadef-17704 orid="17704">' . $data['D17704'] . '</AksjerSlettedeSpleisMvMorselskapOvertakendeISINType-datadef-17704>
		<AksjerSlettedeSpleisMvMorselskapOvertakendeAksjetype-datadef-19907 orid="19907">' . $data['D19907'] . '</AksjerSlettedeSpleisMvMorselskapOvertakendeAksjetype-datadef-19907>
		<AksjerSlettedeSpleisMvMorselskapOvertakendeAntall-datadef-17705 orid="17705">' . $data['D17705'] . '</AksjerSlettedeSpleisMvMorselskapOvertakendeAntall-datadef-17705>
		<AksjerSlettedeSpleisMvMorselskapOvertakendePalydende-datadef-17706 orid="17706">' . $data['D17706'] . '</AksjerSlettedeSpleisMvMorselskapOvertakendePalydende-datadef-17706>
	</AntallSlettedeAksjerOmfordeling-grp-3459>
</SlettingAvAksjerIfmSpleisSkattefriFusjonFisjon-grp-3458>
<EndringerIAksjekapitalOgOverkurs-grp-3460 gruppeid="3460">
	<NedsettelseAvInnbetaltOverkursMedTilbakebetalingTilAksjonarene-grp-3461 gruppeid="3461">
		<AksjerOverkursNedsettelse-datadef-17707 orid="17707">' . $data['D17707'] . '</AksjerOverkursNedsettelse-datadef-17707>
		<AksjerOverkursNedsettelseTidspunkt-datadef-17708 orid="17708">' . $data['D17708'] . '</AksjerOverkursNedsettelseTidspunkt-datadef-17708>
	</NedsettelseAvInnbetaltOverkursMedTilbakebetalingTilAksjonarene-grp-3461>
	<ForhoyelseAvAKVedOkningAvPalydnende-grp-3462 gruppeid="3462">
		<AksjekapitalForhoyelseFondsemisjon-datadef-17709 orid="17709">' . $data['D17709'] . '</AksjekapitalForhoyelseFondsemisjon-datadef-17709>
		<AksjeFondsemisjonPalydendeForhoyelse-datadef-17710 orid="17710">' . $data['D17710'] . '</AksjeFondsemisjonPalydendeForhoyelse-datadef-17710>
		<AksjePalydendeEtterFondsemisjon-datadef-17711 orid="17711">' . $data['D17711'] . '</AksjePalydendeEtterFondsemisjon-datadef-17711>
		<AksjeFondsemisjonTidspunkt-datadef-17712 orid="17712">' . $data['D17712'] . '</AksjeFondsemisjonTidspunkt-datadef-17712>
	</ForhoyelseAvAKVedOkningAvPalydnende-grp-3462>
	<ForhoyelseAvAKVedOkningAvPalydnede-grp-3463 gruppeid="3463">
		<AksjekapitalNyemisjonForhoyelse-datadef-17713 orid="17713">' . $data['D17713'] . '</AksjekapitalNyemisjonForhoyelse-datadef-17713>
		<AksjeNyemisjonPalydendeForhoyelse-datadef-17714 orid="17714">' . $data['D17714'] . '</AksjeNyemisjonPalydendeForhoyelse-datadef-17714>
		<AksjePalydendeEtterNyemisjon-datadef-17715 orid="17715">' . $data['D17715'] . '</AksjePalydendeEtterNyemisjon-datadef-17715>
		<AksjeNyemisjonTidspunkt-datadef-17716 orid="17716">' . $data['D17716'] . '</AksjeNyemisjonTidspunkt-datadef-17716>
		<AksjeOverkursForhoyelse-datadef-22071 orid="22071">' . $data['D22071'] . '</AksjeOverkursForhoyelse-datadef-22071>
	</ForhoyelseAvAKVedOkningAvPalydnede-grp-3463>
	<NedsettelseAvInnbetaltOgFondsemitertAK-grp-3464 gruppeid="3464">
		<AksjekapitalInnbetaltNedsettelse-datadef-17717 orid="17717">' . $data['D17717'] . '</AksjekapitalInnbetaltNedsettelse-datadef-17717>
		<AksjePalydendeNedsettelse-datadef-17718 orid="17718">' . $data['D17718'] . '</AksjePalydendeNedsettelse-datadef-17718>
		<AksjePalydendeEtterNedsettelse-datadef-17719 orid="17719">' . $data['D17719'] . '</AksjePalydendeEtterNedsettelse-datadef-17719>
		<AksjeNedsettelseTidspunkt-datadef-17720 orid="17720">' . $data['D17720'] . '</AksjeNedsettelseTidspunkt-datadef-17720>
		<AksjekapitalFondsemittertNedsettelse-datadef-17721 orid="17721">' . $data['D17721'] . '</AksjekapitalFondsemittertNedsettelse-datadef-17721>
	</NedsettelseAvInnbetaltOgFondsemitertAK-grp-3464>
	<NedsettelseAKVedReduksjonAvPalydende-grp-3465 gruppeid="3465">
		<AksjekapitalUtbetalingNedsettelse-datadef-17722 orid="17722">' . $data['D17722'] . '</AksjekapitalUtbetalingNedsettelse-datadef-17722>
		<AksjePalydendeNedsettelse-datadef-17723 orid="17723">' . $data['D17723'] . '</AksjePalydendeNedsettelse-datadef-17723>
		<AksjePalydendeEtterNedsettelse-datadef-17724 orid="17724">' . $data['D17724'] . '</AksjePalydendeEtterNedsettelse-datadef-17724>
		<AksjeNedsettelseTidspunkt-datadef-17725 orid="17725">' . $data['D17725'] . '</AksjeNedsettelseTidspunkt-datadef-17725>
	</NedsettelseAKVedReduksjonAvPalydende-grp-3465>
	<NedsettelseAvAKVedReduksjonUtfisjonering-grp-3466 gruppeid="3466">
		<AksjekapitalUtfisjoneringNedsettelse-datadef-17726 orid="17726">' . $data['D17726'] . '</AksjekapitalUtfisjoneringNedsettelse-datadef-17726>
		<AksjePalydendeNedsettelse-datadef-17727 orid="17727">' . $data['D17727'] . '</AksjePalydendeNedsettelse-datadef-17727>
		<AksjePalydendeEtterNedsettelse-datadef-17728 orid="17728">' . $data['D17728'] . '</AksjePalydendeEtterNedsettelse-datadef-17728>
		<AksjeNedsettelseUtfisjoneringTidspunkt-datadef-17729 orid="17729">' . $data['D17729'] . '</AksjeNedsettelseUtfisjoneringTidspunkt-datadef-17729>
		<EnhetOvertakendeOrganisasjonsnummer-datadef-17730 orid="17730">' . $data['D17730'] . '</EnhetOvertakendeOrganisasjonsnummer-datadef-17730>
		<EnhetOvertakendeISIN-datadef-17731 orid="17731">' . $data['D17731'] . '</EnhetOvertakendeISIN-datadef-17731>
		<EnhetOvertakendeAksjetype-datadef-19903 orid="19903">' . $data['D19903'] . '</EnhetOvertakendeAksjetype-datadef-19903>
		<AksjerOvertakendeVederlagAntall-datadef-17732 orid="17732">' . $data['D17732'] . '</AksjerOvertakendeVederlagAntall-datadef-17732>
		<AkjerOvertakendeVederlagPalydende-datadef-17733 orid="17733">' . $data['D17733'] . '</AkjerOvertakendeVederlagPalydende-datadef-17733>
		<EnhetMorselskapOvertakendeOrganisasjonsnummer-datadef-17734 orid="17734">' . $data['D17734'] . '</EnhetMorselskapOvertakendeOrganisasjonsnummer-datadef-17734>
		<EnhetISINOvertakendeMorselskap-datadef-17735 orid="17735">' . $data['D17735'] . '</EnhetISINOvertakendeMorselskap-datadef-17735>
		<EnhetAksjetypeOvertakendeMorsselskap-datadef-19904 orid="19904">' . $data['D19904'] . '</EnhetAksjetypeOvertakendeMorsselskap-datadef-19904>
		<AksjerMorselskapOvertakendeVederlagAntall-datadef-17736 orid="17736">' . $data['D17736'] . '</AksjerMorselskapOvertakendeVederlagAntall-datadef-17736>
		<AksjerMorselskapOvertakendeVederlagPalydende-datadef-17737 orid="17737">' . $data['D17737'] . '</AksjerMorselskapOvertakendeVederlagPalydende-datadef-17737>
	</NedsettelseAvAKVedReduksjonUtfisjonering-grp-3466>
</EndringerIAksjekapitalOgOverkurs-grp-3460>
';
}
?>