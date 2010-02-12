<?php
// Filnavn: schemaxml_79_3324.php
// Skjema: RF-1239    Beregning av RISK
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1952 gruppeid="1952">
	<Selskap-grp-28 gruppeid="28">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Selskap-grp-28>
	<Regnskapsforer-grp-229 gruppeid="229">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-229>
</GenerellInformasjon-grp-1952>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1952 gruppeid="1952">
	<Selskap-grp-28 gruppeid="28">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Selskap-grp-28>
	<Regnskapsforer-grp-229 gruppeid="229">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
	</Regnskapsforer-grp-229>
</GenerellInformasjon-grp-1952>
<BeregningAvRISK-grp-227 gruppeid="227">
	<InntektSkattbarNettoRISK-datadef-16535 orid="16535">' . $data['D16535'] . '</InntektSkattbarNettoRISK-datadef-16535>
	<KonsernbidragMottattBeskattet-datadef-15561 orid="15561">' . $data['D15561'] . '</KonsernbidragMottattBeskattet-datadef-15561>
	<InntektSkattbarNettoEkslKonsernbidragRISKGrunnlag-datadef-22480 orid="22480">' . $data['D22480'] . '</InntektSkattbarNettoEkslKonsernbidragRISKGrunnlag-datadef-22480>
	<InntektSkattbarNettoEkslKonsernbidragRISKGrunnlagPositiv-datadef-22509 orid="22509">' . $data['D22509'] . '</InntektSkattbarNettoEkslKonsernbidragRISKGrunnlagPositiv-datadef-22509>
	<RISKBeregningNegativSaldoGjenstaende-datadef-7600 orid="7600">' . $data['D7600'] . '</RISKBeregningNegativSaldoGjenstaende-datadef-7600>
	<RISKBeregningNegativSaldoInntektsforing-datadef-7601 orid="7601">' . $data['D7601'] . '</RISKBeregningNegativSaldoInntektsforing-datadef-7601>
	<RISKBeregningNegativSaldoRestbelop-datadef-7602 orid="7602">' . $data['D7602'] . '</RISKBeregningNegativSaldoRestbelop-datadef-7602>
	<Korreksjonsinntekt-datadef-1098 orid="1098">' . $data['D1098'] . '</Korreksjonsinntekt-datadef-1098>
	<RISKBeregningGrunnlag-datadef-1998 orid="1998">' . $data['D1998'] . '</RISKBeregningGrunnlag-datadef-1998>
	<RISKBeregningGrunnlagSkatt-datadef-7603 orid="7603">' . $data['D7603'] . '</RISKBeregningGrunnlagSkatt-datadef-7603>
	<RISKBeregningSkattPetroleumskatteloven-datadef-7604 orid="7604">' . $data['D7604'] . '</RISKBeregningSkattPetroleumskatteloven-datadef-7604>
	<GodtgjorelseUtbytteUbenyttet-datadef-7606 orid="7606">' . $data['D7606'] . '</GodtgjorelseUtbytteUbenyttet-datadef-7606>
	<RISKBelopBeregnetPositivtBelopDelsum-datadef-7607 orid="7607">' . $data['D7607'] . '</RISKBelopBeregnetPositivtBelopDelsum-datadef-7607>
	<RISKBelopBeregnetNegativtBelopDelsum-datadef-7611 orid="7611">' . $data['D7611'] . '</RISKBelopBeregnetNegativtBelopDelsum-datadef-7611>
	<UtbytteAvsatt-datadef-235 orid="235">' . $data['D235'] . '</UtbytteAvsatt-datadef-235>
	<UtbytteDifferanseAvsattUtdelt-datadef-13949 orid="13949">' . $data['D13949'] . '</UtbytteDifferanseAvsattUtdelt-datadef-13949>
	<RealisasjonsRISK-datadef-13950 orid="13950">' . $data['D13950'] . '</RealisasjonsRISK-datadef-13950>
	<RISKBelopFusjonMorDatter-datadef-13951 orid="13951">' . $data['D13951'] . '</RISKBelopFusjonMorDatter-datadef-13951>
	<KonsernbidragIkkeSkattepliktigIkkeFradragsberettiget-datadef-13952 orid="13952">' . $data['D13952'] . '</KonsernbidragIkkeSkattepliktigIkkeFradragsberettiget-datadef-13952>
	<SkattRefusjonPersoninntekt-datadef-15575 orid="15575">' . $data['D15575'] . '</SkattRefusjonPersoninntekt-datadef-15575>
	<SkattGrunnrenteinntekt-datadef-5780 orid="5780">' . $data['D5780'] . '</SkattGrunnrenteinntekt-datadef-5780>
	<RISKBelopEgneSlettedeAksjer-datadef-22481 orid="22481">' . $data['D22481'] . '</RISKBelopEgneSlettedeAksjer-datadef-22481>
	<RISKBeregningSkattlagtKapitalTilbakeholdt-datadef-7609 orid="7609">' . $data['D7609'] . '</RISKBeregningSkattlagtKapitalTilbakeholdt-datadef-7609>
	<AksjerMvAntall-datadef-1125 orid="1125">' . $data['D1125'] . '</AksjerMvAntall-datadef-1125>
	<RISKBelopPrAksje-datadef-2001 orid="2001">' . $data['D2001'] . '</RISKBelopPrAksje-datadef-2001>
</BeregningAvRISK-grp-227>
<BeregningAvRISKForGrunnfondsbevisISparebanker-grp-228 gruppeid="228">
	<UtjevningsfondEndringSparebank-datadef-11641 orid="11641">' . $data['D11641'] . '</UtjevningsfondEndringSparebank-datadef-11641>
	<GrunnfondOverkursfondUtjevningsfondFondsemisjonEndring-datadef-20194 orid="20194">' . $data['D20194'] . '</GrunnfondOverkursfondUtjevningsfondFondsemisjonEndring-datadef-20194>
	<GrunnfondIkkeInnbetaltUtbetalingReduksjon-datadef-20195 orid="20195">' . $data['D20195'] . '</GrunnfondIkkeInnbetaltUtbetalingReduksjon-datadef-20195>
	<RISKBeregningGrunnfondsbevisGrunnlag-datadef-20196 orid="20196">' . $data['D20196'] . '</RISKBeregningGrunnfondsbevisGrunnlag-datadef-20196>
	<GrunnfondsbevisAntall-datadef-11637 orid="11637">' . $data['D11637'] . '</GrunnfondsbevisAntall-datadef-11637>
	<RISKBelopPerGrunnfondsbevis-datadef-22508 orid="22508">' . $data['D22508'] . '</RISKBelopPerGrunnfondsbevis-datadef-22508>
	<RISKBelopNegativtReverseringUtjevningsfondAndel-datadef-11639 orid="11639">' . $data['D11639'] . '</RISKBelopNegativtReverseringUtjevningsfondAndel-datadef-11639>
</BeregningAvRISKForGrunnfondsbevisISparebanker-grp-228>
';
}
?>