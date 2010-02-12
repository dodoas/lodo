<?php
// Filnavn: schemaxml_72_3066.php
// Skjema: RF-1223    Tilleggsskjema for drosje- og lastebilnæring m.v.
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1056 gruppeid="1056">
	<Avgiver-grp-4202 gruppeid="4202">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-4202>
</GenerellInformasjon-grp-1056>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1056 gruppeid="1056">
	<Avgiver-grp-4202 gruppeid="4202">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
	</Avgiver-grp-4202>
</GenerellInformasjon-grp-1056>
<Kjoretoy-grp-3824 gruppeid="3824">
	<Kjoretoy-grp-3825 gruppeid="3825">
		<OpplysningerOmKjorelengderMv-grp-348 gruppeid="348">
			<LoyvenummerSpesifisertLoyve-datadef-1862 orid="1862">' . $data['D1862'] . '</LoyvenummerSpesifisertLoyve-datadef-1862>
			<BilRegistreringsnummerSpesifisertLoyve-datadef-7955 orid="7955">' . $data['D7955'] . '</BilRegistreringsnummerSpesifisertLoyve-datadef-7955>
			<BilRegistreringsdatoSpesifisertLoyve-datadef-7956 orid="7956">' . $data['D7956'] . '</BilRegistreringsdatoSpesifisertLoyve-datadef-7956>
			<BilInnkjopsdatoSpesifisertLoyve-datadef-1863 orid="1863">' . $data['D1863'] . '</BilInnkjopsdatoSpesifisertLoyve-datadef-1863>
			<BilDriftTidsromSpesifisertLoyve-datadef-1864 orid="1864">' . $data['D1864'] . '</BilDriftTidsromSpesifisertLoyve-datadef-1864>
			<BilMerkeTypeSpesifisertLoyve-datadef-7957 orid="7957">' . $data['D7957'] . '</BilMerkeTypeSpesifisertLoyve-datadef-7957>
			<BilKilometerstandSpesifisertLoyve-datadef-7958 orid="7958">' . $data['D7958'] . '</BilKilometerstandSpesifisertLoyve-datadef-7958>
			<BilKilometerstandFjoraretSpesifisertLoyve-datadef-7950 orid="7950">' . $data['D7950'] . '</BilKilometerstandFjoraretSpesifisertLoyve-datadef-7950>
			<BilKjorelengdeSpesifisertLoyve-datadef-7959 orid="7959">' . $data['D7959'] . '</BilKjorelengdeSpesifisertLoyve-datadef-7959>
			<BilKjorelengdeNaringSpesifisertLoyve-datadef-1865 orid="1865">' . $data['D1865'] . '</BilKjorelengdeNaringSpesifisertLoyve-datadef-1865>
			<BilDrivstoffSpesifisertLoyve-datadef-1866 orid="1866">' . $data['D1866'] . '</BilDrivstoffSpesifisertLoyve-datadef-1866>
			<LastebilVektSpesifisertLoyve-datadef-1867 orid="1867">' . $data['D1867'] . '</LastebilVektSpesifisertLoyve-datadef-1867>
		</OpplysningerOmKjorelengderMv-grp-348>
		<Bilkostnader-grp-350 gruppeid="350">
			<BilkostnaderDrivstoffSpesifisertLoyve-datadef-1868 orid="1868">' . $data['D1868'] . '</BilkostnaderDrivstoffSpesifisertLoyve-datadef-1868>
			<BilkostnaderSmoreoljeSpesifisertLoyve-datadef-1869 orid="1869">' . $data['D1869'] . '</BilkostnaderSmoreoljeSpesifisertLoyve-datadef-1869>
			<BilkostnaderVedlikeholdReparasjonerSpesifisertLoyve-datadef-1870 orid="1870">' . $data['D1870'] . '</BilkostnaderVedlikeholdReparasjonerSpesifisertLoyve-datadef-1870>
			<BilkostnaderForsikringAvgifterSpesifisertLoyve-datadef-1871 orid="1871">' . $data['D1871'] . '</BilkostnaderForsikringAvgifterSpesifisertLoyve-datadef-1871>
			<BilkostnaderLeasingleieSpesifisertLoyve-datadef-1872 orid="1872">' . $data['D1872'] . '</BilkostnaderLeasingleieSpesifisertLoyve-datadef-1872>
			<BilkostnaderSpesifisertLoyve-datadef-7960 orid="7960">' . $data['D7960'] . '</BilkostnaderSpesifisertLoyve-datadef-7960>
		</Bilkostnader-grp-350>
		<TilleggsopplysningerForDrosjeeiere-grp-351 gruppeid="351">
			<KjoreinntekterKredittSpesifisertLoyve-datadef-1873 orid="1873">' . $data['D1873'] . '</KjoreinntekterKredittSpesifisertLoyve-datadef-1873>
			<KjoreinntekterKredittVentetidSpesifisertLoyve-datadef-1874 orid="1874">' . $data['D1874'] . '</KjoreinntekterKredittVentetidSpesifisertLoyve-datadef-1874>
			<KjoreinntekterKredittSkolebarnkjoringSpesifisertLoyve-datadef-1875 orid="1875">' . $data['D1875'] . '</KjoreinntekterKredittSkolebarnkjoringSpesifisertLoyve-datadef-1875>
			<SkolebarnkjoringKilometerSpesifisertLoyve-datadef-1876 orid="1876">' . $data['D1876'] . '</SkolebarnkjoringKilometerSpesifisertLoyve-datadef-1876>
			<KjoreinntekterKontantSpesifisertLoyve-datadef-1877 orid="1877">' . $data['D1877'] . '</KjoreinntekterKontantSpesifisertLoyve-datadef-1877>
			<TaksameterTypeSpesifisertLoyve-datadef-1878 orid="1878">' . $data['D1878'] . '</TaksameterTypeSpesifisertLoyve-datadef-1878>
			<TaksameterBesatteKilometerSpesifisertLoyve-datadef-1879 orid="1879">' . $data['D1879'] . '</TaksameterBesatteKilometerSpesifisertLoyve-datadef-1879>
			<InnberetningStandardfordelArbeidsgiversBilSpesifisertLoyve-datadef-1880 orid="1880">' . $data['D1880'] . '</InnberetningStandardfordelArbeidsgiversBilSpesifisertLoyve-datadef-1880>
			<LoyveUtleidSpesifisertLoyve-datadef-1881 orid="1881">' . $data['D1881'] . '</LoyveUtleidSpesifisertLoyve-datadef-1881>
		</TilleggsopplysningerForDrosjeeiere-grp-351>
	</Kjoretoy-grp-3825>
</Kjoretoy-grp-3824>
<AndreOpplysninger-grp-3501 gruppeid="3501">
	<GarasjeleieBeregnet-datadef-1882 orid="1882">' . $data['D1882'] . '</GarasjeleieBeregnet-datadef-1882>
	<Garasjeleie-datadef-1883 orid="1883">' . $data['D1883'] . '</Garasjeleie-datadef-1883>
	<KontorleieBeregnet-datadef-1884 orid="1884">' . $data['D1884'] . '</KontorleieBeregnet-datadef-1884>
	<Kontorleie-datadef-1885 orid="1885">' . $data['D1885'] . '</Kontorleie-datadef-1885>
	<DrosjevirksomhetDriftssted-datadef-19403 orid="19403">' . $data['D19403'] . '</DrosjevirksomhetDriftssted-datadef-19403>
	<DriftsstedBoligAvstand-datadef-1887 orid="1887">' . $data['D1887'] . '</DriftsstedBoligAvstand-datadef-1887>
</AndreOpplysninger-grp-3501>
<Leietakere-grp-2570 gruppeid="2570">
	<Leietakere-grp-5105 gruppeid="5105">
		<LeietakerLoyvenummerSpesifisertLoyve-datadef-1888 orid="1888">' . $data['D1888'] . '</LeietakerLoyvenummerSpesifisertLoyve-datadef-1888>
		<LeietakerNavnSpesifisertLoyve-datadef-1889 orid="1889">' . $data['D1889'] . '</LeietakerNavnSpesifisertLoyve-datadef-1889>
		<LeietakerAdresseSpesifisertLoyve-datadef-1890 orid="1890">' . $data['D1890'] . '</LeietakerAdresseSpesifisertLoyve-datadef-1890>
		<LeietakerPostnummerSpesifisertLoyve-datadef-7951 orid="7951">' . $data['D7951'] . '</LeietakerPostnummerSpesifisertLoyve-datadef-7951>
		<LeietakerPoststedSpesifisertLoyve-datadef-7952 orid="7952">' . $data['D7952'] . '</LeietakerPoststedSpesifisertLoyve-datadef-7952>
		<LeietakerFodselsnummerSpesifisertLoyve-datadef-1891 orid="1891">' . $data['D1891'] . '</LeietakerFodselsnummerSpesifisertLoyve-datadef-1891>
	</Leietakere-grp-5105>
</Leietakere-grp-2570>
<PrivatBrukDisponeringAvBil-grp-2571 gruppeid="2571">
	<HvemHarDisponertBilenETilPrivatBruk-grp-5106 gruppeid="5106">
		<LeietakerPrivatBrukLoyvenummerSpesifisertLoyve-datadef-1892 orid="1892">' . $data['D1892'] . '</LeietakerPrivatBrukLoyvenummerSpesifisertLoyve-datadef-1892>
		<LeietakerPrivatBrukNavnSpesifisertLoyve-datadef-1893 orid="1893">' . $data['D1893'] . '</LeietakerPrivatBrukNavnSpesifisertLoyve-datadef-1893>
		<LeietakerPrivatBrukAdresseSpesifisertLoyve-datadef-1894 orid="1894">' . $data['D1894'] . '</LeietakerPrivatBrukAdresseSpesifisertLoyve-datadef-1894>
		<LeietakerPrivatBrukPostnummerSpesifisertLoyve-datadef-7953 orid="7953">' . $data['D7953'] . '</LeietakerPrivatBrukPostnummerSpesifisertLoyve-datadef-7953>
		<LeietakerPrivatBrukPoststedSpesifisertLoyve-datadef-7954 orid="7954">' . $data['D7954'] . '</LeietakerPrivatBrukPoststedSpesifisertLoyve-datadef-7954>
		<LeietakerPrivatBrukFodselsnummerSpesifisertLoyve-datadef-1895 orid="1895">' . $data['D1895'] . '</LeietakerPrivatBrukFodselsnummerSpesifisertLoyve-datadef-1895>
	</HvemHarDisponertBilenETilPrivatBruk-grp-5106>
</PrivatBrukDisponeringAvBil-grp-2571>
';
}
?>