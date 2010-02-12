<?php
// RF-1167  Næringsoppgave 2 (for aksjeselskaper m.v.)
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1053 gruppeid="1053">
	<Regnskapsperiode-grp-3959 gruppeid="3959">
		<RegnskapStartdato-datadef-4166 orid="4166">' . $data['D4166'] . '</RegnskapStartdato-datadef-4166>
		<RegnskapAvslutningsdato-datadef-4167 orid="4167">' . $data['D4167'] . '</RegnskapAvslutningsdato-datadef-4167>
	</Regnskapsperiode-grp-3959>
	<Avgiver-grp-138 gruppeid="138">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<Sysselsatte-datadef-30 orid="30">' . $data['D30'] . '</Sysselsatte-datadef-30>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<Regnskapsregler-datadef-6923 orid="6923">' . $data['D6923'] . '</Regnskapsregler-datadef-6923>
		<EnhetNaring-datadef-16 orid="16">' . $data['D16'] . '</EnhetNaring-datadef-16>
		<Regnskapsplikt-datadef-7194 orid="7194">' . $data['D7194'] . '</Regnskapsplikt-datadef-7194>
	</Avgiver-grp-138>
	<HenvendelseRettesTil-grp-247 gruppeid="247">
		<KontaktpersonNavn-datadef-2 orid="2">' . $data['D2'] . '</KontaktpersonNavn-datadef-2>
		<KontaktpersonTelefonnummer-datadef-3 orid="3">' . $data['D3'] . '</KontaktpersonTelefonnummer-datadef-3>
	</HenvendelseRettesTil-grp-247>
	<Regnskapsforer-grp-139 gruppeid="139">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<OppgaveUtfyllingEkstern-datadef-6924 orid="6924">' . $data['D6924'] . '</OppgaveUtfyllingEkstern-datadef-6924>
		<RegnskapsforingEkstern-datadef-11262 orid="11262">' . $data['D11262'] . '</RegnskapsforingEkstern-datadef-11262>
	</Regnskapsforer-grp-139>
	<Revisor-grp-248 gruppeid="248">
		<RevisorOrganisasjonsnummer-datadef-1938 orid="1938">' . $data['D1938'] . '</RevisorOrganisasjonsnummer-datadef-1938>
		<RevisjonsselskapNavn-datadef-13035 orid="13035">' . $data['D13035'] . '</RevisjonsselskapNavn-datadef-13035>
		<RevisorNavn-datadef-1937 orid="1937">' . $data['D1937'] . '</RevisorNavn-datadef-1937>
		<RevisorAdresse-datadef-2247 orid="2247">' . $data['D2247'] . '</RevisorAdresse-datadef-2247>
		<RevisorPostnummer-datadef-11265 orid="11265">' . $data['D11265'] . '</RevisorPostnummer-datadef-11265>
		<RevisorPoststed-datadef-11266 orid="11266">' . $data['D11266'] . '</RevisorPoststed-datadef-11266>
	</Revisor-grp-248>
</GenerellInformasjon-grp-1053>';
}
else
{
$xml = '<GenerellInformasjon-grp-1053 gruppeid="1053">
	<Regnskapsperiode-grp-3959 gruppeid="3959">
		<RegnskapStartdato-datadef-4166 orid="4166">' . $data['D4166'] . '</RegnskapStartdato-datadef-4166>
		<RegnskapAvslutningsdato-datadef-4167 orid="4167">' . $data['D4167'] . '</RegnskapAvslutningsdato-datadef-4167>
	</Regnskapsperiode-grp-3959>
	<Avgiver-grp-138 gruppeid="138">
		<EnhetNavn-datadef-1 orid="1">' . $data['D1'] . '</EnhetNavn-datadef-1>
		<EnhetAdresse-datadef-15 orid="15">' . $data['D15'] . '</EnhetAdresse-datadef-15>
		<EnhetOrganisasjonsnummer-datadef-18 orid="18">' . $data['D18'] . '</EnhetOrganisasjonsnummer-datadef-18>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
		<Sysselsatte-datadef-30 orid="30">' . $data['D30'] . '</Sysselsatte-datadef-30>
		<EnhetPostnummer-datadef-6673 orid="6673">' . $data['D6673'] . '</EnhetPostnummer-datadef-6673>
		<EnhetPoststed-datadef-6674 orid="6674">' . $data['D6674'] . '</EnhetPoststed-datadef-6674>
		<Regnskapsregler-datadef-6923 orid="6923">' . $data['D6923'] . '</Regnskapsregler-datadef-6923>
		<EnhetNaring-datadef-16 orid="16">' . $data['D16'] . '</EnhetNaring-datadef-16>
		<Regnskapsplikt-datadef-7194 orid="7194">' . $data['D7194'] . '</Regnskapsplikt-datadef-7194>
	</Avgiver-grp-138>
	<HenvendelseRettesTil-grp-247 gruppeid="247">
		<KontaktpersonNavn-datadef-2 orid="2">' . $data['D2'] . '</KontaktpersonNavn-datadef-2>
		<KontaktpersonTelefonnummer-datadef-3 orid="3">' . $data['D3'] . '</KontaktpersonTelefonnummer-datadef-3>
	</HenvendelseRettesTil-grp-247>
	<Regnskapsforer-grp-139 gruppeid="139">
		<RegnskapsforerNavn-datadef-280 orid="280">' . $data['D280'] . '</RegnskapsforerNavn-datadef-280>
		<RegnskapsforerOrganisasjonsnummer-datadef-3651 orid="3651">' . $data['D3651'] . '</RegnskapsforerOrganisasjonsnummer-datadef-3651>
		<RegnskapsforerAdresse-datadef-281 orid="281">' . $data['D281'] . '</RegnskapsforerAdresse-datadef-281>
		<RegnskapsforerPostnummer-datadef-6678 orid="6678">' . $data['D6678'] . '</RegnskapsforerPostnummer-datadef-6678>
		<RegnskapsforerPoststed-datadef-6679 orid="6679">' . $data['D6679'] . '</RegnskapsforerPoststed-datadef-6679>
		<OppgaveUtfyllingEkstern-datadef-6924 orid="6924">' . $data['D6924'] . '</OppgaveUtfyllingEkstern-datadef-6924>
		<RegnskapsforingEkstern-datadef-11262 orid="11262">' . $data['D11262'] . '</RegnskapsforingEkstern-datadef-11262>
	</Regnskapsforer-grp-139>
	<Revisor-grp-248 gruppeid="248">
		<RevisorOrganisasjonsnummer-datadef-1938 orid="1938">' . $data['D1938'] . '</RevisorOrganisasjonsnummer-datadef-1938>
		<RevisjonsselskapNavn-datadef-13035 orid="13035">' . $data['D13035'] . '</RevisjonsselskapNavn-datadef-13035>
		<RevisorNavn-datadef-1937 orid="1937">' . $data['D1937'] . '</RevisorNavn-datadef-1937>
		<RevisorAdresse-datadef-2247 orid="2247">' . $data['D2247'] . '</RevisorAdresse-datadef-2247>
		<RevisorPostnummer-datadef-11265 orid="11265">' . $data['D11265'] . '</RevisorPostnummer-datadef-11265>
		<RevisorPoststed-datadef-11266 orid="11266">' . $data['D11266'] . '</RevisorPoststed-datadef-11266>
	</Revisor-grp-248>
</GenerellInformasjon-grp-1053>
<TilleggsopplysningerOgSpesifikasjoner-grp-140 gruppeid="140">
	<Varelager-grp-1954 gruppeid="1954">
		<SkattemessigVerdiDetteAr-grp-1955 gruppeid="1955">
			<LagerbeholdningRavarerHalvfabrikataSkattemessig-datadef-111 orid="111">' . $data['D111'] . '</LagerbeholdningRavarerHalvfabrikataSkattemessig-datadef-111>
			<LagerbeholdningVarerIArbeidSkattemessig-datadef-112 orid="112">' . $data['D112'] . '</LagerbeholdningVarerIArbeidSkattemessig-datadef-112>
			<LagerbeholdningFerdigEgentilvirkedeVarerSkattemessig-datadef-113 orid="113">' . $data['D113'] . '</LagerbeholdningFerdigEgentilvirkedeVarerSkattemessig-datadef-113>
			<LagerbeholdningInnkjopteVarerVideresalgSkattemessig-datadef-114 orid="114">' . $data['D114'] . '</LagerbeholdningInnkjopteVarerVideresalgSkattemessig-datadef-114>
			<BuskapVerdiSluttstatus-datadef-9669 orid="9669">' . $data['D9669'] . '</BuskapVerdiSluttstatus-datadef-9669>
			<LagerbeholdningJordbrukEgetBruk-datadef-17165 orid="17165">' . $data['D17165'] . '</LagerbeholdningJordbrukEgetBruk-datadef-17165>
			<LagerbeholdningSkattemessig-datadef-115 orid="115">' . $data['D115'] . '</LagerbeholdningSkattemessig-datadef-115>
		</SkattemessigVerdiDetteAr-grp-1955>
		<RegnskapsmessigVerdiDetteAr-grp-1956 gruppeid="1956">
			<LagerbeholdningRavarerHalvfabrikata-datadef-283 orid="283">' . $data['D283'] . '</LagerbeholdningRavarerHalvfabrikata-datadef-283>
			<LagerbeholdningVarerIArbeid-datadef-284 orid="284">' . $data['D284'] . '</LagerbeholdningVarerIArbeid-datadef-284>
			<LagerbeholdningFerdigEgentilvirkedeVarer-datadef-285 orid="285">' . $data['D285'] . '</LagerbeholdningFerdigEgentilvirkedeVarer-datadef-285>
			<LagerbeholdningInnkjopteVarerVideresalg-datadef-286 orid="286">' . $data['D286'] . '</LagerbeholdningInnkjopteVarerVideresalg-datadef-286>
			<BuskapVerdiRegnskapsmessig-datadef-22510 orid="22510">' . $data['D22510'] . '</BuskapVerdiRegnskapsmessig-datadef-22510>
			<LagerbeholdningJordbrukEgetBrukRegnskapsmessig-datadef-22512 orid="22512">' . $data['D22512'] . '</LagerbeholdningJordbrukEgetBrukRegnskapsmessig-datadef-22512>
		</RegnskapsmessigVerdiDetteAr-grp-1956>
		<SkattemessigVerdiFjoraret-grp-1957 gruppeid="1957">
			<LagerbeholdningRavarerHalvfabrikataSkattemessigFjoraret-datadef-6926 orid="6926">' . $data['D6926'] . '</LagerbeholdningRavarerHalvfabrikataSkattemessigFjoraret-datadef-6926>
			<LagerbeholdningVarerIArbeidSkattemessigFjoraret-datadef-6928 orid="6928">' . $data['D6928'] . '</LagerbeholdningVarerIArbeidSkattemessigFjoraret-datadef-6928>
			<LagerbeholdningFerdigEgentilvirkedeVarerSkattemessigFjoraret-datadef-6930 orid="6930">' . $data['D6930'] . '</LagerbeholdningFerdigEgentilvirkedeVarerSkattemessigFjoraret-datadef-6930>
			<LagerbeholdningInnkjopteVarerVideresalgSkattemessigFjoraret-datadef-6932 orid="6932">' . $data['D6932'] . '</LagerbeholdningInnkjopteVarerVideresalgSkattemessigFjoraret-datadef-6932>
			<BuskapVerdiApningsstatus-datadef-9670 orid="9670">' . $data['D9670'] . '</BuskapVerdiApningsstatus-datadef-9670>
			<LagerbeholdningJordbrukEgetBrukFjoraret-datadef-17166 orid="17166">' . $data['D17166'] . '</LagerbeholdningJordbrukEgetBrukFjoraret-datadef-17166>
			<LagerbeholdningSkattemessigFjoraret-datadef-6934 orid="6934">' . $data['D6934'] . '</LagerbeholdningSkattemessigFjoraret-datadef-6934>
		</SkattemessigVerdiFjoraret-grp-1957>
		<RegnskapsmessigVerdiFjoraret-grp-1958 gruppeid="1958">
			<LagerbeholdningRavarerHalvfabrikataFjoraret-datadef-6927 orid="6927">' . $data['D6927'] . '</LagerbeholdningRavarerHalvfabrikataFjoraret-datadef-6927>
			<LagerbeholdningVarerIArbeidFjoraret-datadef-6929 orid="6929">' . $data['D6929'] . '</LagerbeholdningVarerIArbeidFjoraret-datadef-6929>
			<LagerbeholdningFerdigEgentilvirkedeVarerFjoraret-datadef-6931 orid="6931">' . $data['D6931'] . '</LagerbeholdningFerdigEgentilvirkedeVarerFjoraret-datadef-6931>
			<LagerbeholdningInnkjopteVarerVideresalgFjoraret-datadef-6933 orid="6933">' . $data['D6933'] . '</LagerbeholdningInnkjopteVarerVideresalgFjoraret-datadef-6933>
			<BuskapVerdiRegnskapsmessigFjoraret-datadef-22511 orid="22511">' . $data['D22511'] . '</BuskapVerdiRegnskapsmessigFjoraret-datadef-22511>
			<LagerbeholdningJordbrukEgetBrukRegnskapsmessigFjoraret-datadef-22513 orid="22513">' . $data['D22513'] . '</LagerbeholdningJordbrukEgetBrukRegnskapsmessigFjoraret-datadef-22513>
		</RegnskapsmessigVerdiFjoraret-grp-1958>
	</Varelager-grp-1954>
</TilleggsopplysningerOgSpesifikasjoner-grp-140>
<Bruttofortjeneste-grp-146 gruppeid="146">
	<SalgsinntekterHandelsvarerAvgiftspliktig-datadef-312 orid="312">' . $data['D312'] . '</SalgsinntekterHandelsvarerAvgiftspliktig-datadef-312>
	<SalgsinntekterHandelsvarerAvgiftsfri-datadef-313 orid="313">' . $data['D313'] . '</SalgsinntekterHandelsvarerAvgiftsfri-datadef-313>
	<SalgsinntekterHandelsvarer-datadef-94 orid="94">' . $data['D94'] . '</SalgsinntekterHandelsvarer-datadef-94>
	<VarekostnadHandelsvarer-datadef-102 orid="102">' . $data['D102'] . '</VarekostnadHandelsvarer-datadef-102>
	<FortjenesteHandelsvarerBrutto-datadef-110 orid="110">' . $data['D110'] . '</FortjenesteHandelsvarerBrutto-datadef-110>
</Bruttofortjeneste-grp-146>
<ForSamvirkelag-grp-4990 gruppeid="4990">
	<AndelskapitalFelleseidSamvirkeforetakAvsetningGrunnlag-datadef-22150 orid="22150">' . $data['D22150'] . '</AndelskapitalFelleseidSamvirkeforetakAvsetningGrunnlag-datadef-22150>
	<NaringsinntektSamvirkeforetakOmsetningMedlemmerEgetLag-datadef-22151 orid="22151">' . $data['D22151'] . '</NaringsinntektSamvirkeforetakOmsetningMedlemmerEgetLag-datadef-22151>
</ForSamvirkelag-grp-4990>
<SkattemessigVerdiPaFordringer-grp-148 gruppeid="148">
	<Fordringer-grp-1962 gruppeid="1962">
		<FordringerDetteAr-grp-1964 gruppeid="1964">
			<FordringerKunderPalydende-datadef-6941 orid="6941">' . $data['D6941'] . '</FordringerKunderPalydende-datadef-6941>
			<FordringerKunderTap-datadef-6940 orid="6940">' . $data['D6940'] . '</FordringerKunderTap-datadef-6940>
			<FordringerKunderNedskrivningSkattemessig-datadef-117 orid="117">' . $data['D117'] . '</FordringerKunderNedskrivningSkattemessig-datadef-117>
			<SalgKreditt-datadef-6944 orid="6944">' . $data['D6944'] . '</SalgKreditt-datadef-6944>
			<FordringerKunderSkattemessig-datadef-118 orid="118">' . $data['D118'] . '</FordringerKunderSkattemessig-datadef-118>
			<FordringerKonsernMv-datadef-7128 orid="7128">' . $data['D7128'] . '</FordringerKonsernMv-datadef-7128>
			<FordringerSkattemessig-datadef-287 orid="287">' . $data['D287'] . '</FordringerSkattemessig-datadef-287>
		</FordringerDetteAr-grp-1964>
		<FordringerFjoraret-grp-1965 gruppeid="1965">
			<FordringerKunderPalydendeFjoraret-datadef-6938 orid="6938">' . $data['D6938'] . '</FordringerKunderPalydendeFjoraret-datadef-6938>
			<FordringerKunderTapFjoraret-datadef-6939 orid="6939">' . $data['D6939'] . '</FordringerKunderTapFjoraret-datadef-6939>
			<FordringerKunderNedskrivningSkattemessigFjoraret-datadef-6942 orid="6942">' . $data['D6942'] . '</FordringerKunderNedskrivningSkattemessigFjoraret-datadef-6942>
			<SalgKredittFjoraret-datadef-6943 orid="6943">' . $data['D6943'] . '</SalgKredittFjoraret-datadef-6943>
			<FordringerKunderSkattemessigFjoraret-datadef-6945 orid="6945">' . $data['D6945'] . '</FordringerKunderSkattemessigFjoraret-datadef-6945>
			<FordringerKonsernMvFjoraret-datadef-6946 orid="6946">' . $data['D6946'] . '</FordringerKonsernMvFjoraret-datadef-6946>
			<FordringerSkattemessigFjoraret-datadef-6922 orid="6922">' . $data['D6922'] . '</FordringerSkattemessigFjoraret-datadef-6922>
		</FordringerFjoraret-grp-1965>
	</Fordringer-grp-1962>
	<EnhetNyetablering-datadef-6947 orid="6947">' . $data['D6947'] . '</EnhetNyetablering-datadef-6947>
</SkattemessigVerdiPaFordringer-grp-148>
<AretsAnskaffelserOgSalgAvDriftsmidler-grp-150 gruppeid="150">
	<Driftsmidler-grp-1989 gruppeid="1989">
		<Anskaffelser-grp-1990 gruppeid="1990">
			<EiendelerImmaterielleTilgang-datadef-2637 orid="2637">' . $data['D2637'] . '</EiendelerImmaterielleTilgang-datadef-2637>
			<SkipFartoyerRiggerMvTilgang-datadef-6948 orid="6948">' . $data['D6948'] . '</SkipFartoyerRiggerMvTilgang-datadef-6948>
			<ByggAnleggTilgang-datadef-3998 orid="3998">' . $data['D3998'] . '</ByggAnleggTilgang-datadef-3998>
			<VogntogLastebilerVarebilerMvTilgang-datadef-3991 orid="3991">' . $data['D3991'] . '</VogntogLastebilerVarebilerMvTilgang-datadef-3991>
			<TomterGrunnarealerTilgang-datadef-6950 orid="6950">' . $data['D6950'] . '</TomterGrunnarealerTilgang-datadef-6950>
			<KontormaskinerMvTilgang-datadef-6952 orid="6952">' . $data['D6952'] . '</KontormaskinerMvTilgang-datadef-6952>
			<BoligerBoligtomterTilgang-datadef-6954 orid="6954">' . $data['D6954'] . '</BoligerBoligtomterTilgang-datadef-6954>
			<PersonbilerTraktorerMaskinerMvTilgang-datadef-6956 orid="6956">' . $data['D6956'] . '</PersonbilerTraktorerMaskinerMvTilgang-datadef-6956>
		</Anskaffelser-grp-1990>
		<Salg-grp-1991 gruppeid="1991">
			<EiendelerImmaterielleAvgang-datadef-2638 orid="2638">' . $data['D2638'] . '</EiendelerImmaterielleAvgang-datadef-2638>
			<SkipFartoyerRiggerMvAvgang-datadef-6949 orid="6949">' . $data['D6949'] . '</SkipFartoyerRiggerMvAvgang-datadef-6949>
			<ByggAnleggAvgang-datadef-3999 orid="3999">' . $data['D3999'] . '</ByggAnleggAvgang-datadef-3999>
			<VogntogLastebilerVarebilerMvAvgang-datadef-3990 orid="3990">' . $data['D3990'] . '</VogntogLastebilerVarebilerMvAvgang-datadef-3990>
			<TomterGrunnarealerAvgang-datadef-6951 orid="6951">' . $data['D6951'] . '</TomterGrunnarealerAvgang-datadef-6951>
			<KontormaskinerMvAvgang-datadef-6953 orid="6953">' . $data['D6953'] . '</KontormaskinerMvAvgang-datadef-6953>
			<BoligerBoligtomterAvgang-datadef-6955 orid="6955">' . $data['D6955'] . '</BoligerBoligtomterAvgang-datadef-6955>
			<PersonbilerTraktorerMaskinerMvAvgang-datadef-6957 orid="6957">' . $data['D6957'] . '</PersonbilerTraktorerMaskinerMvAvgang-datadef-6957>
		</Salg-grp-1991>
	</Driftsmidler-grp-1989>
</AretsAnskaffelserOgSalgAvDriftsmidler-grp-150>
<ResultatregnskapDriftsinntekter-grp-1992 gruppeid="1992">
	<Driftsinntekter-grp-157 gruppeid="157">
		<DriftsinntekterDetteAr-grp-1993 gruppeid="1993">
			<SalgsinntekterUttakAvgiftspliktig-datadef-6958 orid="6958">' . $data['D6958'] . '</SalgsinntekterUttakAvgiftspliktig-datadef-6958>
			<SalgsinntekterUttakAvgiftsfri-datadef-6960 orid="6960">' . $data['D6960'] . '</SalgsinntekterUttakAvgiftsfri-datadef-6960>
			<SalgsinntekterUttakUtenforAvgiftsomrade-datadef-6962 orid="6962">' . $data['D6962'] . '</SalgsinntekterUttakUtenforAvgiftsomrade-datadef-6962>
			<AvgifterOffentligeSolgteVarer-datadef-120 orid="120">' . $data['D120'] . '</AvgifterOffentligeSolgteVarer-datadef-120>
			<TilskuddOffentlige-datadef-371 orid="371">' . $data['D371'] . '</TilskuddOffentlige-datadef-371>
			<DriftsinntekterUopptjentASMv-datadef-17368 orid="17368">' . $data['D17368'] . '</DriftsinntekterUopptjentASMv-datadef-17368>
			<LeieinntekterFastEiendom-datadef-99 orid="99">' . $data['D99'] . '</LeieinntekterFastEiendom-datadef-99>
			<LeieinntekterAndre-datadef-6967 orid="6967">' . $data['D6967'] . '</LeieinntekterAndre-datadef-6967>
			<ProvisjonsinntekterASMv-datadef-19467 orid="19467">' . $data['D19467'] . '</ProvisjonsinntekterASMv-datadef-19467>
			<AnleggsmidlerAvgangGevinst-datadef-123 orid="123">' . $data['D123'] . '</AnleggsmidlerAvgangGevinst-datadef-123>
			<DriftsinntekterAndre-datadef-73 orid="73">' . $data['D73'] . '</DriftsinntekterAndre-datadef-73>
			<Driftsinntekter-datadef-72 orid="72">' . $data['D72'] . '</Driftsinntekter-datadef-72>
		</DriftsinntekterDetteAr-grp-1993>
		<DriftsinntekterFjoraret-grp-159 gruppeid="159">
			<SalgsinntekterUttakAvgiftspliktigFjoraret-datadef-6959 orid="6959">' . $data['D6959'] . '</SalgsinntekterUttakAvgiftspliktigFjoraret-datadef-6959>
			<SalgsinntekterUttakAvgiftsfriFjoraret-datadef-6961 orid="6961">' . $data['D6961'] . '</SalgsinntekterUttakAvgiftsfriFjoraret-datadef-6961>
			<SalgsinntekterUttakUtenforAvgiftsomradeFjoraret-datadef-6963 orid="6963">' . $data['D6963'] . '</SalgsinntekterUttakUtenforAvgiftsomradeFjoraret-datadef-6963>
			<AvgifterOffentligeSolgteVarerFjoraret-datadef-6964 orid="6964">' . $data['D6964'] . '</AvgifterOffentligeSolgteVarerFjoraret-datadef-6964>
			<TilskuddOffentligeFjoraret-datadef-6965 orid="6965">' . $data['D6965'] . '</TilskuddOffentligeFjoraret-datadef-6965>
			<DriftsinntekterUopptjentASMvFjoraret-datadef-17369 orid="17369">' . $data['D17369'] . '</DriftsinntekterUopptjentASMvFjoraret-datadef-17369>
			<LeieinntekterFastEiendomFjoraret-datadef-6966 orid="6966">' . $data['D6966'] . '</LeieinntekterFastEiendomFjoraret-datadef-6966>
			<LeieinntekterAndreFjoraret-datadef-6968 orid="6968">' . $data['D6968'] . '</LeieinntekterAndreFjoraret-datadef-6968>
			<ProvisjonsinntekterFjoraret-datadef-6969 orid="6969">' . $data['D6969'] . '</ProvisjonsinntekterFjoraret-datadef-6969>
			<AnleggsmidlerAvgangGevinstFjoraret-datadef-6970 orid="6970">' . $data['D6970'] . '</AnleggsmidlerAvgangGevinstFjoraret-datadef-6970>
			<DriftsinntekterAndreFjoraret-datadef-6971 orid="6971">' . $data['D6971'] . '</DriftsinntekterAndreFjoraret-datadef-6971>
			<DriftsinntekterFjoraret-datadef-6972 orid="6972">' . $data['D6972'] . '</DriftsinntekterFjoraret-datadef-6972>
		</DriftsinntekterFjoraret-grp-159>
	</Driftsinntekter-grp-157>
</ResultatregnskapDriftsinntekter-grp-1992>
<ResultatregnskapDriftskostnader-grp-2013 gruppeid="2013">
	<Driftskostnader-grp-1995 gruppeid="1995">
		<DriftskostnaderDetteAr-grp-165 gruppeid="165">
			<Varekostnad-datadef-101 orid="101">' . $data['D101'] . '</Varekostnad-datadef-101>
			<BeholdningsendringerVarerEgentilvirkede-datadef-6973 orid="6973">' . $data['D6973'] . '</BeholdningsendringerVarerEgentilvirkede-datadef-6973>
			<Fremmedytelser-datadef-3209 orid="3209">' . $data['D3209'] . '</Fremmedytelser-datadef-3209>
			<BeholdningsendringerAnleggsmidlerEgentilvirkede-datadef-6975 orid="6975">' . $data['D6975'] . '</BeholdningsendringerAnleggsmidlerEgentilvirkede-datadef-6975>
			<Lonnskostnader-datadef-81 orid="81">' . $data['D81'] . '</Lonnskostnader-datadef-81>
			<GodtgjorelserAndreOppgavepliktig-datadef-6980 orid="6980">' . $data['D6980'] . '</GodtgjorelserAndreOppgavepliktig-datadef-6980>
			<Arbeidsgiveravgift-datadef-104 orid="104">' . $data['D104'] . '</Arbeidsgiveravgift-datadef-104>
			<PensjonskostnaderInnberetningspliktige-datadef-6983 orid="6983">' . $data['D6983'] . '</PensjonskostnaderInnberetningspliktige-datadef-6983>
			<GodtgjorelserDeltakerlignedeSelskaper-datadef-309 orid="309">' . $data['D309'] . '</GodtgjorelserDeltakerlignedeSelskaper-datadef-309>
			<PersonalkostnaderAndre-datadef-288 orid="288">' . $data['D288'] . '</PersonalkostnaderAndre-datadef-288>
			<AvskrivningerOrdinare-datadef-141 orid="141">' . $data['D141'] . '</AvskrivningerOrdinare-datadef-141>
			<AnleggsmidlerNedskrivning-datadef-142 orid="142">' . $data['D142'] . '</AnleggsmidlerNedskrivning-datadef-142>
			<FraktTransportkostnaderSalg-datadef-6989 orid="6989">' . $data['D6989'] . '</FraktTransportkostnaderSalg-datadef-6989>
			<EnergiProduksjon-datadef-103 orid="103">' . $data['D103'] . '</EnergiProduksjon-datadef-103>
			<LeiekostnaderFastEiendom-datadef-126 orid="126">' . $data['D126'] . '</LeiekostnaderFastEiendom-datadef-126>
			<LysVarme-datadef-6995 orid="6995">' . $data['D6995'] . '</LysVarme-datadef-6995>
			<RenovasjonRenholdMv-datadef-6993 orid="6993">' . $data['D6993'] . '</RenovasjonRenholdMv-datadef-6993>
			<LeiekostnaderDriftsmidler-datadef-128 orid="128">' . $data['D128'] . '</LeiekostnaderDriftsmidler-datadef-128>
			<DriftsmaterialerIkkeAktivert-datadef-129 orid="129">' . $data['D129'] . '</DriftsmaterialerIkkeAktivert-datadef-129>
			<VedlikeholdReparasjonBygninger-datadef-11324 orid="11324">' . $data['D11324'] . '</VedlikeholdReparasjonBygninger-datadef-11324>
			<VedlikeholdReparasjonAnnet-datadef-11325 orid="11325">' . $data['D11325'] . '</VedlikeholdReparasjonAnnet-datadef-11325>
			<FremmedeTjenester-datadef-106 orid="106">' . $data['D106'] . '</FremmedeTjenester-datadef-106>
			<KontorkostnadTelefonMv-datadef-7001 orid="7001">' . $data['D7001'] . '</KontorkostnadTelefonMv-datadef-7001>
			<TransportmidlerDrivstoff-datadef-7015 orid="7015">' . $data['D7015'] . '</TransportmidlerDrivstoff-datadef-7015>
			<TransportmidlerVedlikeholdMv-datadef-7003 orid="7003">' . $data['D7003'] . '</TransportmidlerVedlikeholdMv-datadef-7003>
			<TransportmidlerForsikringAvgifter-datadef-7005 orid="7005">' . $data['D7005'] . '</TransportmidlerForsikringAvgifter-datadef-7005>
			<BilkostnaderPrivatBil-datadef-7353 orid="7353">' . $data['D7353'] . '</BilkostnaderPrivatBil-datadef-7353>
			<NaringsbilPrivatBruk-datadef-11329 orid="11329">' . $data['D11329'] . '</NaringsbilPrivatBruk-datadef-11329>
			<ReiseDiettBilgodtgjorelseOppgavepliktig-datadef-7007 orid="7007">' . $data['D7007'] . '</ReiseDiettBilgodtgjorelseOppgavepliktig-datadef-7007>
			<ReiseDiettIkkeOppgavepliktig-datadef-7008 orid="7008">' . $data['D7008'] . '</ReiseDiettIkkeOppgavepliktig-datadef-7008>
			<ProvisjonskostnaderASMv-datadef-19466 orid="19466">' . $data['D19466'] . '</ProvisjonskostnaderASMv-datadef-19466>
			<SalgReklame-datadef-7011 orid="7011">' . $data['D7011'] . '</SalgReklame-datadef-7011>
			<Representasjon-datadef-7013 orid="7013">' . $data['D7013'] . '</Representasjon-datadef-7013>
			<KontingenterGaver-datadef-138 orid="138">' . $data['D138'] . '</KontingenterGaver-datadef-138>
			<Forsikringspremier-datadef-837 orid="837">' . $data['D837'] . '</Forsikringspremier-datadef-837>
			<GarantiService-datadef-7020 orid="7020">' . $data['D7020'] . '</GarantiService-datadef-7020>
			<PatentLisensRoyalties-datadef-140 orid="140">' . $data['D140'] . '</PatentLisensRoyalties-datadef-140>
			<DriftskostnaderAndre-datadef-82 orid="82">' . $data['D82'] . '</DriftskostnaderAndre-datadef-82>
			<AnleggsmidlerAvgangTap-datadef-143 orid="143">' . $data['D143'] . '</AnleggsmidlerAvgangTap-datadef-143>
			<FordringerTap-datadef-144 orid="144">' . $data['D144'] . '</FordringerTap-datadef-144>
			<Driftskostnader-datadef-83 orid="83">' . $data['D83'] . '</Driftskostnader-datadef-83>
			<Driftsresultat-datadef-146 orid="146">' . $data['D146'] . '</Driftsresultat-datadef-146>
		</DriftskostnaderDetteAr-grp-165>
		<DriftskostnaderFjoraret-grp-166 gruppeid="166">
			<VarekostnadFjoraret-datadef-6977 orid="6977">' . $data['D6977'] . '</VarekostnadFjoraret-datadef-6977>
			<BeholdningsendringerVarerEgentilvirkedeFjoraret-datadef-6974 orid="6974">' . $data['D6974'] . '</BeholdningsendringerVarerEgentilvirkedeFjoraret-datadef-6974>
			<FremmedytelserFjoraret-datadef-6978 orid="6978">' . $data['D6978'] . '</FremmedytelserFjoraret-datadef-6978>
			<BeholdningsendringerAnleggsmidlerEgentilvirkedeFjoraret-datadef-6976 orid="6976">' . $data['D6976'] . '</BeholdningsendringerAnleggsmidlerEgentilvirkedeFjoraret-datadef-6976>
			<LonnskostnaderFjoraret-datadef-6979 orid="6979">' . $data['D6979'] . '</LonnskostnaderFjoraret-datadef-6979>
			<GodtgjorelserAndreOppgavepliktigFjoraret-datadef-6981 orid="6981">' . $data['D6981'] . '</GodtgjorelserAndreOppgavepliktigFjoraret-datadef-6981>
			<ArbeidsgiveravgiftFjoraret-datadef-6982 orid="6982">' . $data['D6982'] . '</ArbeidsgiveravgiftFjoraret-datadef-6982>
			<PensjonskostnaderInnberetningspliktigeFjoraret-datadef-6984 orid="6984">' . $data['D6984'] . '</PensjonskostnaderInnberetningspliktigeFjoraret-datadef-6984>
			<GodtgjorelserDeltakerlignedeSelskaperFjoraret-datadef-6985 orid="6985">' . $data['D6985'] . '</GodtgjorelserDeltakerlignedeSelskaperFjoraret-datadef-6985>
			<PersonalkostnaderAndreFjoraret-datadef-6986 orid="6986">' . $data['D6986'] . '</PersonalkostnaderAndreFjoraret-datadef-6986>
			<AvskrivningerOrdinareFjoraret-datadef-6987 orid="6987">' . $data['D6987'] . '</AvskrivningerOrdinareFjoraret-datadef-6987>
			<AnleggsmidlerNedskrivningFjoraret-datadef-6988 orid="6988">' . $data['D6988'] . '</AnleggsmidlerNedskrivningFjoraret-datadef-6988>
			<FraktTransportkostnaderSalgFjoraret-datadef-6990 orid="6990">' . $data['D6990'] . '</FraktTransportkostnaderSalgFjoraret-datadef-6990>
			<EnergiProduksjonFjoraret-datadef-6991 orid="6991">' . $data['D6991'] . '</EnergiProduksjonFjoraret-datadef-6991>
			<LeiekostnaderFastEiendomFjoraret-datadef-6992 orid="6992">' . $data['D6992'] . '</LeiekostnaderFastEiendomFjoraret-datadef-6992>
			<LysVarmeFjoraret-datadef-6996 orid="6996">' . $data['D6996'] . '</LysVarmeFjoraret-datadef-6996>
			<RenovasjonRenholdMvFjoraret-datadef-6994 orid="6994">' . $data['D6994'] . '</RenovasjonRenholdMvFjoraret-datadef-6994>
			<LeiekostnaderDriftsmidlerFjoraret-datadef-6997 orid="6997">' . $data['D6997'] . '</LeiekostnaderDriftsmidlerFjoraret-datadef-6997>
			<DriftsmaterialerIkkeAktivertFjoraret-datadef-6998 orid="6998">' . $data['D6998'] . '</DriftsmaterialerIkkeAktivertFjoraret-datadef-6998>
			<VedlikeholdReparasjonBygningerFjoraret-datadef-11326 orid="11326">' . $data['D11326'] . '</VedlikeholdReparasjonBygningerFjoraret-datadef-11326>
			<VedlikeholdReparasjonAnnetFjoraret-datadef-11327 orid="11327">' . $data['D11327'] . '</VedlikeholdReparasjonAnnetFjoraret-datadef-11327>
			<FremmedeTjenesterFjoraret-datadef-7000 orid="7000">' . $data['D7000'] . '</FremmedeTjenesterFjoraret-datadef-7000>
			<KontorkostnadTelefonMvFjoraret-datadef-7002 orid="7002">' . $data['D7002'] . '</KontorkostnadTelefonMvFjoraret-datadef-7002>
			<TransportmidlerDrivstoffFjoraret-datadef-7016 orid="7016">' . $data['D7016'] . '</TransportmidlerDrivstoffFjoraret-datadef-7016>
			<TransportmidlerVedlikeholdMvFjoraret-datadef-7004 orid="7004">' . $data['D7004'] . '</TransportmidlerVedlikeholdMvFjoraret-datadef-7004>
			<TransportmidlerForsikringAvgifterFjoraret-datadef-7006 orid="7006">' . $data['D7006'] . '</TransportmidlerForsikringAvgifterFjoraret-datadef-7006>
			<BilkostnaderPrivatBilFjoraret-datadef-11328 orid="11328">' . $data['D11328'] . '</BilkostnaderPrivatBilFjoraret-datadef-11328>
			<NaringsbilPrivatBrukFjoraret-datadef-11330 orid="11330">' . $data['D11330'] . '</NaringsbilPrivatBrukFjoraret-datadef-11330>
			<ReiseDiettBilgodtgjorelseOppgavepliktigFjoraret-datadef-7017 orid="7017">' . $data['D7017'] . '</ReiseDiettBilgodtgjorelseOppgavepliktigFjoraret-datadef-7017>
			<ReiseDiettIkkeOppgavepliktigFjoraret-datadef-7009 orid="7009">' . $data['D7009'] . '</ReiseDiettIkkeOppgavepliktigFjoraret-datadef-7009>
			<ProvisjonskostnaderFjoraret-datadef-7010 orid="7010">' . $data['D7010'] . '</ProvisjonskostnaderFjoraret-datadef-7010>
			<SalgReklameFjoraret-datadef-7012 orid="7012">' . $data['D7012'] . '</SalgReklameFjoraret-datadef-7012>
			<RepresentasjonFjoraret-datadef-7014 orid="7014">' . $data['D7014'] . '</RepresentasjonFjoraret-datadef-7014>
			<KontingenterGaverFjoraret-datadef-7018 orid="7018">' . $data['D7018'] . '</KontingenterGaverFjoraret-datadef-7018>
			<ForsikringspremierFjoraret-datadef-7019 orid="7019">' . $data['D7019'] . '</ForsikringspremierFjoraret-datadef-7019>
			<GarantiServiceFjoraret-datadef-7021 orid="7021">' . $data['D7021'] . '</GarantiServiceFjoraret-datadef-7021>
			<PatentLisensRoyaltiesFjoraret-datadef-7022 orid="7022">' . $data['D7022'] . '</PatentLisensRoyaltiesFjoraret-datadef-7022>
			<DriftskostnaderAndreFjoraret-datadef-7023 orid="7023">' . $data['D7023'] . '</DriftskostnaderAndreFjoraret-datadef-7023>
			<AnleggsmidlerAvgangTapFjoraret-datadef-7024 orid="7024">' . $data['D7024'] . '</AnleggsmidlerAvgangTapFjoraret-datadef-7024>
			<FordringerTapFjoraret-datadef-7025 orid="7025">' . $data['D7025'] . '</FordringerTapFjoraret-datadef-7025>
			<DriftskostnaderFjoraret-datadef-7987 orid="7987">' . $data['D7987'] . '</DriftskostnaderFjoraret-datadef-7987>
			<DriftsresultatFjoraret-datadef-7026 orid="7026">' . $data['D7026'] . '</DriftsresultatFjoraret-datadef-7026>
		</DriftskostnaderFjoraret-grp-166>
	</Driftskostnader-grp-1995>
</ResultatregnskapDriftskostnader-grp-2013>
<ResultatregnskapFinans-grp-2014 gruppeid="2014">
	<Finansinntekter-grp-1996 gruppeid="1996">
		<FinansinntekterDetteAr-grp-177 gruppeid="177">
			<ResultatAndelPositiv-datadef-13956 orid="13956">' . $data['D13956'] . '</ResultatAndelPositiv-datadef-13956>
			<RenteinntekterKonsern-datadef-149 orid="149">' . $data['D149'] . '</RenteinntekterKonsern-datadef-149>
			<RenteinntekterAndre-datadef-150 orid="150">' . $data['D150'] . '</RenteinntekterAndre-datadef-150>
			<ValutagevinstAgio-datadef-151 orid="151">' . $data['D151'] . '</ValutagevinstAgio-datadef-151>
			<FinansinntekterAndre-datadef-152 orid="152">' . $data['D152'] . '</FinansinntekterAndre-datadef-152>
			<OmlopsmidlerVerdiokning-datadef-7192 orid="7192">' . $data['D7192'] . '</OmlopsmidlerVerdiokning-datadef-7192>
			<Finansinntekter-datadef-153 orid="153">' . $data['D153'] . '</Finansinntekter-datadef-153>
		</FinansinntekterDetteAr-grp-177>
		<FinansinntekterFjoraret-grp-178 gruppeid="178">
			<ResultatAndelPositivFjoraret-datadef-13957 orid="13957">' . $data['D13957'] . '</ResultatAndelPositivFjoraret-datadef-13957>
			<RenteinntekterKonsernFjoraret-datadef-7029 orid="7029">' . $data['D7029'] . '</RenteinntekterKonsernFjoraret-datadef-7029>
			<RenteinntekterAndreFjoraret-datadef-7030 orid="7030">' . $data['D7030'] . '</RenteinntekterAndreFjoraret-datadef-7030>
			<ValutagevinstAgioFjoraret-datadef-7031 orid="7031">' . $data['D7031'] . '</ValutagevinstAgioFjoraret-datadef-7031>
			<FinansinntekterAndreFjoraret-datadef-7032 orid="7032">' . $data['D7032'] . '</FinansinntekterAndreFjoraret-datadef-7032>
			<OmlopsmidlerVerdiokningFjoraret-datadef-7676 orid="7676">' . $data['D7676'] . '</OmlopsmidlerVerdiokningFjoraret-datadef-7676>
			<FinansinntekterFjoraret-datadef-7993 orid="7993">' . $data['D7993'] . '</FinansinntekterFjoraret-datadef-7993>
		</FinansinntekterFjoraret-grp-178>
	</Finansinntekter-grp-1996>
	<Finanskostnader-grp-2008 gruppeid="2008">
		<FinanskostnaderDetteAr-grp-179 gruppeid="179">
			<ResultatAndelNegativ-datadef-13958 orid="13958">' . $data['D13958'] . '</ResultatAndelNegativ-datadef-13958>
			<OmlopsmidlerVerdireduksjon-datadef-7189 orid="7189">' . $data['D7189'] . '</OmlopsmidlerVerdireduksjon-datadef-7189>
			<EiendelerFinansielleNedskrivning-datadef-7035 orid="7035">' . $data['D7035'] . '</EiendelerFinansielleNedskrivning-datadef-7035>
			<RentekostnaderKonsern-datadef-7037 orid="7037">' . $data['D7037'] . '</RentekostnaderKonsern-datadef-7037>
			<RentekostnaderAndre-datadef-2216 orid="2216">' . $data['D2216'] . '</RentekostnaderAndre-datadef-2216>
			<ValutatapDisagio-datadef-155 orid="155">' . $data['D155'] . '</ValutatapDisagio-datadef-155>
			<FinanskostnaderAndre-datadef-156 orid="156">' . $data['D156'] . '</FinanskostnaderAndre-datadef-156>
			<Finanskostnader-datadef-157 orid="157">' . $data['D157'] . '</Finanskostnader-datadef-157>
		</FinanskostnaderDetteAr-grp-179>
		<FinanskostnaderFjoraret-grp-180 gruppeid="180">
			<ResultatAndelNegativFjoraret-datadef-13959 orid="13959">' . $data['D13959'] . '</ResultatAndelNegativFjoraret-datadef-13959>
			<OmlopsmidlerVerdireduksjonFjoraret-datadef-7677 orid="7677">' . $data['D7677'] . '</OmlopsmidlerVerdireduksjonFjoraret-datadef-7677>
			<EiendelerFinansielleNedskrivningFjoraret-datadef-7036 orid="7036">' . $data['D7036'] . '</EiendelerFinansielleNedskrivningFjoraret-datadef-7036>
			<RentekostnaderKonsernFjoraret-datadef-7038 orid="7038">' . $data['D7038'] . '</RentekostnaderKonsernFjoraret-datadef-7038>
			<RentekostnaderAndreFjoraret-datadef-7039 orid="7039">' . $data['D7039'] . '</RentekostnaderAndreFjoraret-datadef-7039>
			<ValutatapDisagioFjoraret-datadef-7040 orid="7040">' . $data['D7040'] . '</ValutatapDisagioFjoraret-datadef-7040>
			<FinanskostnaderAndreFjoraret-datadef-7041 orid="7041">' . $data['D7041'] . '</FinanskostnaderAndreFjoraret-datadef-7041>
			<FinanskostnaderFjoraret-datadef-7998 orid="7998">' . $data['D7998'] . '</FinanskostnaderFjoraret-datadef-7998>
		</FinanskostnaderFjoraret-grp-180>
	</Finanskostnader-grp-2008>
	<Arsresultat-grp-2016 gruppeid="2016">
		<ArsresultatDetteAr-grp-2020 gruppeid="2020">
			<ResultatForSkattekostnad-datadef-167 orid="167">' . $data['D167'] . '</ResultatForSkattekostnad-datadef-167>
			<SkattBetalbarOrdinartResultat-datadef-7043 orid="7043">' . $data['D7043'] . '</SkattBetalbarOrdinartResultat-datadef-7043>
			<SkattRefusjonSkattelovenOrdinartResultat-datadef-6905 orid="6905">' . $data['D6905'] . '</SkattRefusjonSkattelovenOrdinartResultat-datadef-6905>
			<SkattEndringUtsattOrdinartResultat-datadef-7046 orid="7046">' . $data['D7046'] . '</SkattEndringUtsattOrdinartResultat-datadef-7046>
			<ResultatOrdinart-datadef-7048 orid="7048">' . $data['D7048'] . '</ResultatOrdinart-datadef-7048>
			<InntekterEkstraordinare-datadef-2195 orid="2195">' . $data['D2195'] . '</InntekterEkstraordinare-datadef-2195>
			<KostnaderEkstraordinare-datadef-2196 orid="2196">' . $data['D2196'] . '</KostnaderEkstraordinare-datadef-2196>
			<SkattBetalbarEkstraordinartResultat-datadef-7052 orid="7052">' . $data['D7052'] . '</SkattBetalbarEkstraordinartResultat-datadef-7052>
			<SkattEndringUtsattEkstraordinartResultat-datadef-7057 orid="7057">' . $data['D7057'] . '</SkattEndringUtsattEkstraordinartResultat-datadef-7057>
			<Arsresultat-datadef-172 orid="172">' . $data['D172'] . '</Arsresultat-datadef-172>
		</ArsresultatDetteAr-grp-2020>
		<ArsresultatFjoraret-grp-2021 gruppeid="2021">
			<ResultatForSkattekostnadFjoraret-datadef-7042 orid="7042">' . $data['D7042'] . '</ResultatForSkattekostnadFjoraret-datadef-7042>
			<SkattBetalbarOrdinartResultatFjoraret-datadef-7044 orid="7044">' . $data['D7044'] . '</SkattBetalbarOrdinartResultatFjoraret-datadef-7044>
			<SkattRefusjonSkattelovenOrdinartResultatFjoraret-datadef-7045 orid="7045">' . $data['D7045'] . '</SkattRefusjonSkattelovenOrdinartResultatFjoraret-datadef-7045>
			<SkattEndringUtsattOrdinartResultatFjoraret-datadef-7047 orid="7047">' . $data['D7047'] . '</SkattEndringUtsattOrdinartResultatFjoraret-datadef-7047>
			<ResultatOrdinartFjoraret-datadef-7049 orid="7049">' . $data['D7049'] . '</ResultatOrdinartFjoraret-datadef-7049>
			<InntekterEkstraordinareFjoraret-datadef-7050 orid="7050">' . $data['D7050'] . '</InntekterEkstraordinareFjoraret-datadef-7050>
			<KostnaderEkstraordinareFjoraret-datadef-7051 orid="7051">' . $data['D7051'] . '</KostnaderEkstraordinareFjoraret-datadef-7051>
			<SkattBetalbarEkstraordinartResultatFjoraret-datadef-7053 orid="7053">' . $data['D7053'] . '</SkattBetalbarEkstraordinartResultatFjoraret-datadef-7053>
			<SkattEndringUtsattEkstraordinartResultatFjoraret-datadef-7058 orid="7058">' . $data['D7058'] . '</SkattEndringUtsattEkstraordinartResultatFjoraret-datadef-7058>
			<ArsresultatFjoraret-datadef-7054 orid="7054">' . $data['D7054'] . '</ArsresultatFjoraret-datadef-7054>
		</ArsresultatFjoraret-grp-2021>
	</Arsresultat-grp-2016>
</ResultatregnskapFinans-grp-2014>
<BalanseAnleggsmidler-grp-190 gruppeid="190">
	<Anleggsmidler-grp-2036 gruppeid="2036">
		<AnleggsmidlerDetteAr-grp-189 gruppeid="189">
			<FoU-datadef-7073 orid="7073">' . $data['D7073'] . '</FoU-datadef-7073>
			<PatenterRettigheter-datadef-205 orid="205">' . $data['D205'] . '</PatenterRettigheter-datadef-205>
			<SkattefordelUtsatt-datadef-202 orid="202">' . $data['D202'] . '</SkattefordelUtsatt-datadef-202>
			<ForretningsverdiGoodwill-datadef-206 orid="206">' . $data['D206'] . '</ForretningsverdiGoodwill-datadef-206>
			<Forretningsbygg-datadef-1350 orid="1350">' . $data['D1350'] . '</Forretningsbygg-datadef-1350>
			<ByggAnlegg-datadef-1344 orid="1344">' . $data['D1344'] . '</ByggAnlegg-datadef-1344>
			<AnleggKraftoverforing-datadef-17029 orid="17029">' . $data['D17029'] . '</AnleggKraftoverforing-datadef-17029>
			<AnleggUnderUtforelse-datadef-212 orid="212">' . $data['D212'] . '</AnleggUnderUtforelse-datadef-212>
			<TomterGrunnarealer-datadef-214 orid="214">' . $data['D214'] . '</TomterGrunnarealer-datadef-214>
			<BoligerBoligtomter-datadef-215 orid="215">' . $data['D215'] . '</BoligerBoligtomter-datadef-215>
			<PersonbilerTraktorerMaskinerMv-datadef-1347 orid="1347">' . $data['D1347'] . '</PersonbilerTraktorerMaskinerMv-datadef-1347>
			<SkipFartoyerRiggerMv-datadef-1348 orid="1348">' . $data['D1348'] . '</SkipFartoyerRiggerMv-datadef-1348>
			<FlyHelikopter-datadef-1349 orid="1349">' . $data['D1349'] . '</FlyHelikopter-datadef-1349>
			<VogntogLastebilerVarebilerMv-datadef-1346 orid="1346">' . $data['D1346'] . '</VogntogLastebilerVarebilerMv-datadef-1346>
			<KontormaskinerMv-datadef-1345 orid="1345">' . $data['D1345'] . '</KontormaskinerMv-datadef-1345>
			<DriftsmidlerAndre-datadef-2836 orid="2836">' . $data['D2836'] . '</DriftsmidlerAndre-datadef-2836>
			<InvesteringerDatterKonsernDeltakerlignet-datadef-7089 orid="7089">' . $data['D7089'] . '</InvesteringerDatterKonsernDeltakerlignet-datadef-7089>
			<InvesteringerDatterKonsernAndre-datadef-7091 orid="7091">' . $data['D7091'] . '</InvesteringerDatterKonsernAndre-datadef-7091>
			<UtlanKonsern-datadef-6500 orid="6500">' . $data['D6500'] . '</UtlanKonsern-datadef-6500>
			<InvesteringerTilknyttetSelskapDeltakerlignet-datadef-7094 orid="7094">' . $data['D7094'] . '</InvesteringerTilknyttetSelskapDeltakerlignet-datadef-7094>
			<InvesteringerTilknyttetSelskapAndre-datadef-7096 orid="7096">' . $data['D7096'] . '</InvesteringerTilknyttetSelskapAndre-datadef-7096>
			<UtlanTilknyttetSelskapFelleskontrollertVirksomhet-datadef-7098 orid="7098">' . $data['D7098'] . '</UtlanTilknyttetSelskapFelleskontrollertVirksomhet-datadef-7098>
			<InvesteringerAksjerAndeler-datadef-7100 orid="7100">' . $data['D7100'] . '</InvesteringerAksjerAndeler-datadef-7100>
			<Obligasjoner-datadef-2363 orid="2363">' . $data['D2363'] . '</Obligasjoner-datadef-2363>
			<FordringerLangsiktigEiereStyremedlemmerOl-datadef-7103 orid="7103">' . $data['D7103'] . '</FordringerLangsiktigEiereStyremedlemmerOl-datadef-7103>
			<FordringerAnsatte-datadef-7105 orid="7105">' . $data['D7105'] . '</FordringerAnsatte-datadef-7105>
			<FordringerAndre-datadef-79 orid="79">' . $data['D79'] . '</FordringerAndre-datadef-79>
			<Anleggsmidler-datadef-217 orid="217">' . $data['D217'] . '</Anleggsmidler-datadef-217>
		</AnleggsmidlerDetteAr-grp-189>
		<AnleggsmidlerFjoraret-grp-2038 gruppeid="2038">
			<FoUFjoraret-datadef-7074 orid="7074">' . $data['D7074'] . '</FoUFjoraret-datadef-7074>
			<PatenterRettigheterFjoraret-datadef-7075 orid="7075">' . $data['D7075'] . '</PatenterRettigheterFjoraret-datadef-7075>
			<SkattefordelUtsattFjoraret-datadef-7076 orid="7076">' . $data['D7076'] . '</SkattefordelUtsattFjoraret-datadef-7076>
			<ForretningsverdiGoodwillFjoraret-datadef-7077 orid="7077">' . $data['D7077'] . '</ForretningsverdiGoodwillFjoraret-datadef-7077>
			<ForretningsbyggFjoraret-datadef-7078 orid="7078">' . $data['D7078'] . '</ForretningsbyggFjoraret-datadef-7078>
			<ByggAnleggFjoraret-datadef-7079 orid="7079">' . $data['D7079'] . '</ByggAnleggFjoraret-datadef-7079>
			<AnleggKraftoverforingFjoraret-datadef-17030 orid="17030">' . $data['D17030'] . '</AnleggKraftoverforingFjoraret-datadef-17030>
			<AnleggUnderUtforelseFjoraret-datadef-7080 orid="7080">' . $data['D7080'] . '</AnleggUnderUtforelseFjoraret-datadef-7080>
			<TomterGrunnarealerFjoraret-datadef-7081 orid="7081">' . $data['D7081'] . '</TomterGrunnarealerFjoraret-datadef-7081>
			<BoligerBoligtomterFjoraret-datadef-7082 orid="7082">' . $data['D7082'] . '</BoligerBoligtomterFjoraret-datadef-7082>
			<PersonbilerTraktorerMaskinerMvFjoraret-datadef-7083 orid="7083">' . $data['D7083'] . '</PersonbilerTraktorerMaskinerMvFjoraret-datadef-7083>
			<SkipFartoyerRiggerMvFjoraret-datadef-7084 orid="7084">' . $data['D7084'] . '</SkipFartoyerRiggerMvFjoraret-datadef-7084>
			<FlyHelikopterFjoraret-datadef-7085 orid="7085">' . $data['D7085'] . '</FlyHelikopterFjoraret-datadef-7085>
			<VogntogLastebilerVarebilerMvFjoraret-datadef-7086 orid="7086">' . $data['D7086'] . '</VogntogLastebilerVarebilerMvFjoraret-datadef-7086>
			<KontormaskinerMvFjoraret-datadef-7087 orid="7087">' . $data['D7087'] . '</KontormaskinerMvFjoraret-datadef-7087>
			<DriftsmidlerAndreFjoraret-datadef-7088 orid="7088">' . $data['D7088'] . '</DriftsmidlerAndreFjoraret-datadef-7088>
			<InvesteringerDatterKonsernDeltakerlignetFjoraret-datadef-7090 orid="7090">' . $data['D7090'] . '</InvesteringerDatterKonsernDeltakerlignetFjoraret-datadef-7090>
			<InvesteringerDatterKonsernAndreFjoraret-datadef-7092 orid="7092">' . $data['D7092'] . '</InvesteringerDatterKonsernAndreFjoraret-datadef-7092>
			<UtlanKonsernFjoraret-datadef-7093 orid="7093">' . $data['D7093'] . '</UtlanKonsernFjoraret-datadef-7093>
			<InvesteringerTilknyttetSelskapDeltakerlignetFjoraret-datadef-7095 orid="7095">' . $data['D7095'] . '</InvesteringerTilknyttetSelskapDeltakerlignetFjoraret-datadef-7095>
			<InvesteringerTilknyttetSelskapAndreFjoraret-datadef-7097 orid="7097">' . $data['D7097'] . '</InvesteringerTilknyttetSelskapAndreFjoraret-datadef-7097>
			<UtlanTilknyttetSelskapFelleskontrollertVirksomhetFjoraret-datadef-7099 orid="7099">' . $data['D7099'] . '</UtlanTilknyttetSelskapFelleskontrollertVirksomhetFjoraret-datadef-7099>
			<InvesteringerAksjerAndelerFjoraret-datadef-7101 orid="7101">' . $data['D7101'] . '</InvesteringerAksjerAndelerFjoraret-datadef-7101>
			<ObligasjonerFjoraret-datadef-7102 orid="7102">' . $data['D7102'] . '</ObligasjonerFjoraret-datadef-7102>
			<FordringerLangsiktigEiereStyremedlemmerOlFjoraret-datadef-7104 orid="7104">' . $data['D7104'] . '</FordringerLangsiktigEiereStyremedlemmerOlFjoraret-datadef-7104>
			<FordringerAnsatteFjoraret-datadef-7106 orid="7106">' . $data['D7106'] . '</FordringerAnsatteFjoraret-datadef-7106>
			<FordringerAndreFjoraret-datadef-7107 orid="7107">' . $data['D7107'] . '</FordringerAndreFjoraret-datadef-7107>
			<AnleggsmidlerFjoraret-datadef-7108 orid="7108">' . $data['D7108'] . '</AnleggsmidlerFjoraret-datadef-7108>
		</AnleggsmidlerFjoraret-grp-2038>
	</Anleggsmidler-grp-2036>
</BalanseAnleggsmidler-grp-190>
<BalanseOmlopsmidler-grp-2039 gruppeid="2039">
	<Omlopsmidler-grp-2040 gruppeid="2040">
		<OmlopsmidlerDetteAr-grp-202 gruppeid="202">
			<Lagerbeholdning-datadef-326 orid="326">' . $data['D326'] . '</Lagerbeholdning-datadef-326>
			<FordringerKunder-datadef-116 orid="116">' . $data['D116'] . '</FordringerKunder-datadef-116>
			<DriftsinntekterOpptjenteIkkeFakturerte-datadef-190 orid="190">' . $data['D190'] . '</DriftsinntekterOpptjenteIkkeFakturerte-datadef-190>
			<FordringerAndreKonsern-datadef-7110 orid="7110">' . $data['D7110'] . '</FordringerAndreKonsern-datadef-7110>
			<FordringerKortsiktigEiereStyremedlemmerOl-datadef-19685 orid="19685">' . $data['D19685'] . '</FordringerKortsiktigEiereStyremedlemmerOl-datadef-19685>
			<FordringerAndreKortsiktig-datadef-282 orid="282">' . $data['D282'] . '</FordringerAndreKortsiktig-datadef-282>
			<SelskapskapitalInnbetalingKrav-datadef-7113 orid="7113">' . $data['D7113'] . '</SelskapskapitalInnbetalingKrav-datadef-7113>
			<AksjerMvIkkeMarkedsbaserte-datadef-7115 orid="7115">' . $data['D7115'] . '</AksjerMvIkkeMarkedsbaserte-datadef-7115>
			<AksjerMvMarkedsbaserte-datadef-7117 orid="7117">' . $data['D7117'] . '</AksjerMvMarkedsbaserte-datadef-7117>
			<VerdipapirerMarkedsbaserte-datadef-7119 orid="7119">' . $data['D7119'] . '</VerdipapirerMarkedsbaserte-datadef-7119>
			<VerdipapirerIkkeMarkedsbaserte-datadef-7121 orid="7121">' . $data['D7121'] . '</VerdipapirerIkkeMarkedsbaserte-datadef-7121>
			<FinansielleInstrumenterAndre-datadef-6429 orid="6429">' . $data['D6429'] . '</FinansielleInstrumenterAndre-datadef-6429>
			<Kontanter-datadef-84 orid="84">' . $data['D84'] . '</Kontanter-datadef-84>
			<Bankinnskudd-datadef-1189 orid="1189">' . $data['D1189'] . '</Bankinnskudd-datadef-1189>
			<Omlopsmidler-datadef-194 orid="194">' . $data['D194'] . '</Omlopsmidler-datadef-194>
			<Eiendeler-datadef-219 orid="219">' . $data['D219'] . '</Eiendeler-datadef-219>
		</OmlopsmidlerDetteAr-grp-202>
		<OmlopsmidlerFjoraret-grp-203 gruppeid="203">
			<LagerbeholdningFjoraret-datadef-797 orid="797">' . $data['D797'] . '</LagerbeholdningFjoraret-datadef-797>
			<FordringerKunderFjoraret-datadef-6921 orid="6921">' . $data['D6921'] . '</FordringerKunderFjoraret-datadef-6921>
			<DriftsinntekterOpptjenteIkkeFakturerteFjoraret-datadef-7109 orid="7109">' . $data['D7109'] . '</DriftsinntekterOpptjenteIkkeFakturerteFjoraret-datadef-7109>
			<FordringerAndreKonsernFjoraret-datadef-7111 orid="7111">' . $data['D7111'] . '</FordringerAndreKonsernFjoraret-datadef-7111>
			<FordringerKortsiktigEiereStyremedlemmerOlFjoraret-datadef-19686 orid="19686">' . $data['D19686'] . '</FordringerKortsiktigEiereStyremedlemmerOlFjoraret-datadef-19686>
			<FordringerAndreKortsiktigFjoraret-datadef-7112 orid="7112">' . $data['D7112'] . '</FordringerAndreKortsiktigFjoraret-datadef-7112>
			<SelskapskapitalInnbetalingKravFjoraret-datadef-7114 orid="7114">' . $data['D7114'] . '</SelskapskapitalInnbetalingKravFjoraret-datadef-7114>
			<AksjerMvIkkeMarkedsbaserteFjoraret-datadef-7116 orid="7116">' . $data['D7116'] . '</AksjerMvIkkeMarkedsbaserteFjoraret-datadef-7116>
			<AksjerMvMarkedsbaserteFjoraret-datadef-7118 orid="7118">' . $data['D7118'] . '</AksjerMvMarkedsbaserteFjoraret-datadef-7118>
			<VerdipapirerMarkedsbaserteFjoraret-datadef-7120 orid="7120">' . $data['D7120'] . '</VerdipapirerMarkedsbaserteFjoraret-datadef-7120>
			<VerdipapirerIkkeMarkedsbaserteFjoraret-datadef-7122 orid="7122">' . $data['D7122'] . '</VerdipapirerIkkeMarkedsbaserteFjoraret-datadef-7122>
			<FinansielleInstrumenterAndreFjoraret-datadef-7123 orid="7123">' . $data['D7123'] . '</FinansielleInstrumenterAndreFjoraret-datadef-7123>
			<KontanterFjoraret-datadef-7124 orid="7124">' . $data['D7124'] . '</KontanterFjoraret-datadef-7124>
			<BankinnskuddFjoraret-datadef-7125 orid="7125">' . $data['D7125'] . '</BankinnskuddFjoraret-datadef-7125>
			<OmlopsmidlerFjoraret-datadef-7126 orid="7126">' . $data['D7126'] . '</OmlopsmidlerFjoraret-datadef-7126>
			<EiendelerFjoraret-datadef-7127 orid="7127">' . $data['D7127'] . '</EiendelerFjoraret-datadef-7127>
		</OmlopsmidlerFjoraret-grp-203>
	</Omlopsmidler-grp-2040>
</BalanseOmlopsmidler-grp-2039>
<BalanseEgenkapital-grp-2041 gruppeid="2041">
	<InnskuttEgenkapital-grp-2042 gruppeid="2042">
		<InnskuttEgenkapitalDetteAr-grp-222 gruppeid="222">
			<InnskuttEgenkapitalAksjekapitalEgenkapitalAndreForetak-datadef-19680 orid="19680">' . $data['D19680'] . '</InnskuttEgenkapitalAksjekapitalEgenkapitalAndreForetak-datadef-19680>
			<InnskuttEgenkapitalEgneAksjerFelleseidAndelskapital-datadef-19682 orid="19682">' . $data['D19682'] . '</InnskuttEgenkapitalEgneAksjerFelleseidAndelskapital-datadef-19682>
			<Overkursfond-datadef-2585 orid="2585">' . $data['D2585'] . '</Overkursfond-datadef-2585>
			<InnskuttKapitalAnnen-datadef-9703 orid="9703">' . $data['D9703'] . '</InnskuttKapitalAnnen-datadef-9703>
		</InnskuttEgenkapitalDetteAr-grp-222>
		<InnskuttEgenkapitalFjoraret-grp-223 gruppeid="223">
			<InnskuttEgenkapitalAksjekapitalEgenkapitalAndreForetakFjoraret-datadef-19681 orid="19681">' . $data['D19681'] . '</InnskuttEgenkapitalAksjekapitalEgenkapitalAndreForetakFjoraret-datadef-19681>
			<InnskuttEgenkapitalEgneAksjerFelleseidAndelskapitalFjoraret-datadef-19683 orid="19683">' . $data['D19683'] . '</InnskuttEgenkapitalEgneAksjerFelleseidAndelskapitalFjoraret-datadef-19683>
			<OverkursfondFjoraret-datadef-7135 orid="7135">' . $data['D7135'] . '</OverkursfondFjoraret-datadef-7135>
			<InnskuttKapitalAnnenFjoraret-datadef-9983 orid="9983">' . $data['D9983'] . '</InnskuttKapitalAnnenFjoraret-datadef-9983>
		</InnskuttEgenkapitalFjoraret-grp-223>
	</InnskuttEgenkapital-grp-2042>
	<OpptjentEgenkapital-grp-2043 gruppeid="2043">
		<OpptjentEgenkapitalDetteAr-grp-2044 gruppeid="2044">
			<FondVurderingsforskjellerDeltakerlignetSelskap-datadef-7136 orid="7136">' . $data['D7136'] . '</FondVurderingsforskjellerDeltakerlignetSelskap-datadef-7136>
			<FondVurderingsforskjellerAndre-datadef-7138 orid="7138">' . $data['D7138'] . '</FondVurderingsforskjellerAndre-datadef-7138>
			<FondVerdiendringer-datadef-19898 orid="19898">' . $data['D19898'] . '</FondVerdiendringer-datadef-19898>
			<EgenkapitalAnnen-datadef-3274 orid="3274">' . $data['D3274'] . '</EgenkapitalAnnen-datadef-3274>
			<TapUdekket-datadef-249 orid="249">' . $data['D249'] . '</TapUdekket-datadef-249>
			<Egenkapital-datadef-250 orid="250">' . $data['D250'] . '</Egenkapital-datadef-250>
		</OpptjentEgenkapitalDetteAr-grp-2044>
		<OpptjentEgenkapitalFjoraret-grp-2045 gruppeid="2045">
			<FondVurderingsforskjellerDeltakerlignetSelskapFjoraret-datadef-7137 orid="7137">' . $data['D7137'] . '</FondVurderingsforskjellerDeltakerlignetSelskapFjoraret-datadef-7137>
			<FondVurderingsforskjellerAndreFjoraret-datadef-7139 orid="7139">' . $data['D7139'] . '</FondVurderingsforskjellerAndreFjoraret-datadef-7139>
			<FondVerdiendringerFjoraret-datadef-19899 orid="19899">' . $data['D19899'] . '</FondVerdiendringerFjoraret-datadef-19899>
			<EgenkapitalAnnenFjoraret-datadef-7140 orid="7140">' . $data['D7140'] . '</EgenkapitalAnnenFjoraret-datadef-7140>
			<TapUdekketFjoraret-datadef-7141 orid="7141">' . $data['D7141'] . '</TapUdekketFjoraret-datadef-7141>
			<EgenkapitalFjoraret-datadef-7142 orid="7142">' . $data['D7142'] . '</EgenkapitalFjoraret-datadef-7142>
		</OpptjentEgenkapitalFjoraret-grp-2045>
	</OpptjentEgenkapital-grp-2043>
</BalanseEgenkapital-grp-2041>
<BalanseLangsiktigGjeld-grp-2046 gruppeid="2046">
	<LangsiktigGjeld-grp-2047 gruppeid="2047">
		<LangsiktigGjeldDetteAr-grp-225 gruppeid="225">
			<PensjonsforpliktelserASMv-datadef-17370 orid="17370">' . $data['D17370'] . '</PensjonsforpliktelserASMv-datadef-17370>
			<SkattUtsatt-datadef-237 orid="237">' . $data['D237'] . '</SkattUtsatt-datadef-237>
			<InntektUopptjentLangsiktig-datadef-7144 orid="7144">' . $data['D7144'] . '</InntektUopptjentLangsiktig-datadef-7144>
			<AvsetningerForpliktelserLangsiktig-datadef-7157 orid="7157">' . $data['D7157'] . '</AvsetningerForpliktelserLangsiktig-datadef-7157>
			<LanKonvertibelLangsiktig-datadef-7147 orid="7147">' . $data['D7147'] . '</LanKonvertibelLangsiktig-datadef-7147>
			<Obligasjonslan-datadef-6091 orid="6091">' . $data['D6091'] . '</Obligasjonslan-datadef-6091>
			<GjeldKredittinstitusjoner-datadef-7150 orid="7150">' . $data['D7150'] . '</GjeldKredittinstitusjoner-datadef-7150>
			<GjeldLangsiktigAnsatteEiere-datadef-19687 orid="19687">' . $data['D19687'] . '</GjeldLangsiktigAnsatteEiere-datadef-19687>
			<GjeldKonsernLangsiktig-datadef-2256 orid="2256">' . $data['D2256'] . '</GjeldKonsernLangsiktig-datadef-2256>
			<StilleInteressentinnskuddAnsvarligLanekapital-datadef-7153 orid="7153">' . $data['D7153'] . '</StilleInteressentinnskuddAnsvarligLanekapital-datadef-7153>
			<GjeldAnnenLangsiktig-datadef-242 orid="242">' . $data['D242'] . '</GjeldAnnenLangsiktig-datadef-242>
			<GjeldLangsiktig-datadef-86 orid="86">' . $data['D86'] . '</GjeldLangsiktig-datadef-86>
		</LangsiktigGjeldDetteAr-grp-225>
		<LangsiktigGjeldFjoraret-grp-226 gruppeid="226">
			<PensjonsforpliktelserASMvFjoraret-datadef-17371 orid="17371">' . $data['D17371'] . '</PensjonsforpliktelserASMvFjoraret-datadef-17371>
			<SkattUtsattFjoraret-datadef-7143 orid="7143">' . $data['D7143'] . '</SkattUtsattFjoraret-datadef-7143>
			<InntektUopptjentLangsiktigFjoraret-datadef-7145 orid="7145">' . $data['D7145'] . '</InntektUopptjentLangsiktigFjoraret-datadef-7145>
			<AvsetningerForpliktelserLangsiktigFjoraret-datadef-7146 orid="7146">' . $data['D7146'] . '</AvsetningerForpliktelserLangsiktigFjoraret-datadef-7146>
			<LanKonvertibelLangsiktigFjoraret-datadef-7148 orid="7148">' . $data['D7148'] . '</LanKonvertibelLangsiktigFjoraret-datadef-7148>
			<ObligasjonslanFjoraret-datadef-7149 orid="7149">' . $data['D7149'] . '</ObligasjonslanFjoraret-datadef-7149>
			<GjeldKredittinstitusjonerFjoraret-datadef-7151 orid="7151">' . $data['D7151'] . '</GjeldKredittinstitusjonerFjoraret-datadef-7151>
			<GjeldLangsiktigAnsatteEiereFjoraret-datadef-19688 orid="19688">' . $data['D19688'] . '</GjeldLangsiktigAnsatteEiereFjoraret-datadef-19688>
			<GjeldKonsernLangsiktigFjoraret-datadef-7152 orid="7152">' . $data['D7152'] . '</GjeldKonsernLangsiktigFjoraret-datadef-7152>
			<StilleInteressentinnskuddAnsvarligLanekapitalFjoraret-datadef-7154 orid="7154">' . $data['D7154'] . '</StilleInteressentinnskuddAnsvarligLanekapitalFjoraret-datadef-7154>
			<GjeldAnnenLangsiktigFjoraret-datadef-7155 orid="7155">' . $data['D7155'] . '</GjeldAnnenLangsiktigFjoraret-datadef-7155>
			<GjeldLangsiktigFjoraret-datadef-7156 orid="7156">' . $data['D7156'] . '</GjeldLangsiktigFjoraret-datadef-7156>
		</LangsiktigGjeldFjoraret-grp-226>
	</LangsiktigGjeld-grp-2047>
</BalanseLangsiktigGjeld-grp-2046>
<BalanseKortsiktigGjeld-grp-2048 gruppeid="2048">
	<KortsiktigGjeld-grp-2049 gruppeid="2049">
		<KortsiktigGjeldDetteAr-grp-234 gruppeid="234">
			<LanKonvertibelKortsiktig-datadef-7158 orid="7158">' . $data['D7158'] . '</LanKonvertibelKortsiktig-datadef-7158>
			<Sertifikatlan-datadef-9 orid="9">' . $data['D9'] . '</Sertifikatlan-datadef-9>
			<Kassekreditt-datadef-88 orid="88">' . $data['D88'] . '</Kassekreditt-datadef-88>
			<Leverandorgjeld-datadef-220 orid="220">' . $data['D220'] . '</Leverandorgjeld-datadef-220>
			<SkattBetalbarIkkeUtlignet-datadef-228 orid="228">' . $data['D228'] . '</SkattBetalbarIkkeUtlignet-datadef-228>
			<SkattBetalbarUtlignet-datadef-229 orid="229">' . $data['D229'] . '</SkattBetalbarUtlignet-datadef-229>
			<SkattRefusjonSkattelovenGjeld-datadef-230 orid="230">' . $data['D230'] . '</SkattRefusjonSkattelovenGjeld-datadef-230>
			<SkattetrekkAndreTrekk-datadef-7166 orid="7166">' . $data['D7166'] . '</SkattetrekkAndreTrekk-datadef-7166>
			<MerverdiavgiftSkyldig-datadef-224 orid="224">' . $data['D224'] . '</MerverdiavgiftSkyldig-datadef-224>
			<ArbeidsgiveravgiftSkyldig-datadef-223 orid="223">' . $data['D223'] . '</ArbeidsgiveravgiftSkyldig-datadef-223>
			<AvgifterOffentligeSkyldig-datadef-225 orid="225">' . $data['D225'] . '</AvgifterOffentligeSkyldig-datadef-225>
			<UtbytteAvsatt-datadef-235 orid="235">' . $data['D235'] . '</UtbytteAvsatt-datadef-235>
			<ForskuddKunder-datadef-231 orid="231">' . $data['D231'] . '</ForskuddKunder-datadef-231>
			<GjeldKortsiktigAnsatteEiere-datadef-7173 orid="7173">' . $data['D7173'] . '</GjeldKortsiktigAnsatteEiere-datadef-7173>
			<GjeldKonsernKortsiktig-datadef-2255 orid="2255">' . $data['D2255'] . '</GjeldKonsernKortsiktig-datadef-2255>
			<LonnFeriepengerMvSkyldig-datadef-226 orid="226">' . $data['D226'] . '</LonnFeriepengerMvSkyldig-datadef-226>
			<RenterPalopt-datadef-227 orid="227">' . $data['D227'] . '</RenterPalopt-datadef-227>
			<InntektUopptjentKortsiktig-datadef-7178 orid="7178">' . $data['D7178'] . '</InntektUopptjentKortsiktig-datadef-7178>
			<AvsetningerForpliktelserKortsiktig-datadef-7180 orid="7180">' . $data['D7180'] . '</AvsetningerForpliktelserKortsiktig-datadef-7180>
			<GjeldAnnenKortsiktig-datadef-236 orid="236">' . $data['D236'] . '</GjeldAnnenKortsiktig-datadef-236>
			<GjeldKortsiktig-datadef-85 orid="85">' . $data['D85'] . '</GjeldKortsiktig-datadef-85>
			<GjeldEgenkapital-datadef-251 orid="251">' . $data['D251'] . '</GjeldEgenkapital-datadef-251>
		</KortsiktigGjeldDetteAr-grp-234>
		<KortsiktigGjeldFjoraret-grp-235 gruppeid="235">
			<LanKonvertibelKortsiktigFjoraret-datadef-7159 orid="7159">' . $data['D7159'] . '</LanKonvertibelKortsiktigFjoraret-datadef-7159>
			<SertifikatlanFjoraret-datadef-7160 orid="7160">' . $data['D7160'] . '</SertifikatlanFjoraret-datadef-7160>
			<KassekredittFjoraret-datadef-7161 orid="7161">' . $data['D7161'] . '</KassekredittFjoraret-datadef-7161>
			<LeverandorgjeldFjoraret-datadef-7162 orid="7162">' . $data['D7162'] . '</LeverandorgjeldFjoraret-datadef-7162>
			<SkattBetalbarIkkeUtlignetFjoraret-datadef-7163 orid="7163">' . $data['D7163'] . '</SkattBetalbarIkkeUtlignetFjoraret-datadef-7163>
			<SkattBetalbarUtlignetFjoraret-datadef-7164 orid="7164">' . $data['D7164'] . '</SkattBetalbarUtlignetFjoraret-datadef-7164>
			<SkattRefusjonSkattelovenGjeldFjoraret-datadef-7165 orid="7165">' . $data['D7165'] . '</SkattRefusjonSkattelovenGjeldFjoraret-datadef-7165>
			<SkattetrekkAndreTrekkFjoraret-datadef-7167 orid="7167">' . $data['D7167'] . '</SkattetrekkAndreTrekkFjoraret-datadef-7167>
			<MerverdiavgiftSkyldigFjoraret-datadef-7168 orid="7168">' . $data['D7168'] . '</MerverdiavgiftSkyldigFjoraret-datadef-7168>
			<ArbeidsgiveravgiftSkyldigFjoraret-datadef-7169 orid="7169">' . $data['D7169'] . '</ArbeidsgiveravgiftSkyldigFjoraret-datadef-7169>
			<AvgifterOffentligeSkyldigFjoraret-datadef-7170 orid="7170">' . $data['D7170'] . '</AvgifterOffentligeSkyldigFjoraret-datadef-7170>
			<UtbytteAvsattFjoraret-datadef-7171 orid="7171">' . $data['D7171'] . '</UtbytteAvsattFjoraret-datadef-7171>
			<ForskuddKunderFjoraret-datadef-7172 orid="7172">' . $data['D7172'] . '</ForskuddKunderFjoraret-datadef-7172>
			<GjeldKortsiktigAnsatteEiereFjoraret-datadef-7174 orid="7174">' . $data['D7174'] . '</GjeldKortsiktigAnsatteEiereFjoraret-datadef-7174>
			<GjeldKonsernKortsiktigFjoraret-datadef-7175 orid="7175">' . $data['D7175'] . '</GjeldKonsernKortsiktigFjoraret-datadef-7175>
			<LonnFeriepengerMvSkyldigFjoraret-datadef-7176 orid="7176">' . $data['D7176'] . '</LonnFeriepengerMvSkyldigFjoraret-datadef-7176>
			<RenterPaloptFjoraret-datadef-7177 orid="7177">' . $data['D7177'] . '</RenterPaloptFjoraret-datadef-7177>
			<InntektUopptjentKortsiktigFjoraret-datadef-7179 orid="7179">' . $data['D7179'] . '</InntektUopptjentKortsiktigFjoraret-datadef-7179>
			<AvsetningerForpliktelserKortsiktigFjoraret-datadef-7181 orid="7181">' . $data['D7181'] . '</AvsetningerForpliktelserKortsiktigFjoraret-datadef-7181>
			<GjeldAnnenKortsiktigFjoraret-datadef-7182 orid="7182">' . $data['D7182'] . '</GjeldAnnenKortsiktigFjoraret-datadef-7182>
			<GjeldKortsiktigFjoraret-datadef-7183 orid="7183">' . $data['D7183'] . '</GjeldKortsiktigFjoraret-datadef-7183>
			<GjeldEgenkapitalFjoraret-datadef-7185 orid="7185">' . $data['D7185'] . '</GjeldEgenkapitalFjoraret-datadef-7185>
		</KortsiktigGjeldFjoraret-grp-235>
	</KortsiktigGjeld-grp-2049>
</BalanseKortsiktigGjeld-grp-2048>
<BeregningAvNaringsinntektTillegg-grp-238 gruppeid="238">
	<RepresentasjonskostnaderIkkeFradragsberettiget-datadef-254 orid="254">' . $data['D254'] . '</RepresentasjonskostnaderIkkeFradragsberettiget-datadef-254>
	<KontingenterGaverIkkeFradragsberettiget-datadef-255 orid="255">' . $data['D255'] . '</KontingenterGaverIkkeFradragsberettiget-datadef-255>
	<Skattekostnad-datadef-171 orid="171">' . $data['D171'] . '</Skattekostnad-datadef-171>
	<RentekostnaderSkattIkkeFradragsberettiget-datadef-256 orid="256">' . $data['D256'] . '</RentekostnaderSkattIkkeFradragsberettiget-datadef-256>
	<KostnaderAndreIkkeFradragsberettiget-datadef-258 orid="258">' . $data['D258'] . '</KostnaderAndreIkkeFradragsberettiget-datadef-258>
	<ResultatAndelKostnadTilbakeforing-datadef-7187 orid="7187">' . $data['D7187'] . '</ResultatAndelKostnadTilbakeforing-datadef-7187>
	<UtbytteEgenkapitalmetoden-datadef-7188 orid="7188">' . $data['D7188'] . '</UtbytteEgenkapitalmetoden-datadef-7188>
	<OmlopsmidlerVerdireduksjonTilbakefort-datadef-22516 orid="22516">' . $data['D22516'] . '</OmlopsmidlerVerdireduksjonTilbakefort-datadef-22516>
	<AksjerMvTap-datadef-263 orid="263">' . $data['D263'] . '</AksjerMvTap-datadef-263>
	<AksjerMvGevinstSkatt-datadef-265 orid="265">' . $data['D265'] . '</AksjerMvGevinstSkatt-datadef-265>
	<AksjerMvNedskrivning-datadef-267 orid="267">' . $data['D267'] . '</AksjerMvNedskrivning-datadef-267>
	<UnderskuddAndelDeltakerlignetSelskap-datadef-154 orid="154">' . $data['D154'] . '</UnderskuddAndelDeltakerlignetSelskap-datadef-154>
	<OverskuddAndelDeltakerlignetSelskapSkattemessig-datadef-269 orid="269">' . $data['D269'] . '</OverskuddAndelDeltakerlignetSelskapSkattemessig-datadef-269>
	<AndelDeltakerlignetSelskapTap-datadef-271 orid="271">' . $data['D271'] . '</AndelDeltakerlignetSelskapTap-datadef-271>
	<AndelDeltakerlignetSelskapGevinstSkattemessig-datadef-273 orid="273">' . $data['D273'] . '</AndelDeltakerlignetSelskapGevinstSkattemessig-datadef-273>
	<InntektAnnen-datadef-13615 orid="13615">' . $data['D13615'] . '</InntektAnnen-datadef-13615>
	<NaringsinntektTillegg-datadef-7190 orid="7190">' . $data['D7190'] . '</NaringsinntektTillegg-datadef-7190>
</BeregningAvNaringsinntektTillegg-grp-238>
<BeregningAvNaringsinntektFradrag-grp-242 gruppeid="242">
	<RenteinntekterSkatt-datadef-259 orid="259">' . $data['D259'] . '</RenteinntekterSkatt-datadef-259>
	<AksjeutbytteSkattefritt-datadef-22302 orid="22302">' . $data['D22302'] . '</AksjeutbytteSkattefritt-datadef-22302>
	<NaringsinntektFradragAnnet-datadef-261 orid="261">' . $data['D261'] . '</NaringsinntektFradragAnnet-datadef-261>
	<SykepengerNaring-datadef-11331 orid="11331">' . $data['D11331'] . '</SykepengerNaring-datadef-11331>
	<ResultatAndelInntektTilbakeforing-datadef-7191 orid="7191">' . $data['D7191'] . '</ResultatAndelInntektTilbakeforing-datadef-7191>
	<OmlopsmidlerVerdiokningTilbakefort-datadef-21017 orid="21017">' . $data['D21017'] . '</OmlopsmidlerVerdiokningTilbakefort-datadef-21017>
	<AksjerMvGevinst-datadef-264 orid="264">' . $data['D264'] . '</AksjerMvGevinst-datadef-264>
	<AksjerMvTapSkatt-datadef-266 orid="266">' . $data['D266'] . '</AksjerMvTapSkatt-datadef-266>
	<AnleggsmidlerNedskrivningReversering-datadef-5989 orid="5989">' . $data['D5989'] . '</AnleggsmidlerNedskrivningReversering-datadef-5989>
	<OverskuddAndelDeltakerlignetSelskap-datadef-148 orid="148">' . $data['D148'] . '</OverskuddAndelDeltakerlignetSelskap-datadef-148>
	<UnderskuddAndelDeltakerlignetSelskapSkattemessig-datadef-270 orid="270">' . $data['D270'] . '</UnderskuddAndelDeltakerlignetSelskapSkattemessig-datadef-270>
	<KonsernbidragResultatfort-datadef-22053 orid="22053">' . $data['D22053'] . '</KonsernbidragResultatfort-datadef-22053>
	<AndelDeltakerlignetSelskapGevinst-datadef-272 orid="272">' . $data['D272'] . '</AndelDeltakerlignetSelskapGevinst-datadef-272>
	<AndelDeltakerlignetSelskapTapSkattemessig-datadef-274 orid="274">' . $data['D274'] . '</AndelDeltakerlignetSelskapTapSkattemessig-datadef-274>
	<NaringsinntektFradrag-datadef-2218 orid="2218">' . $data['D2218'] . '</NaringsinntektFradrag-datadef-2218>
</BeregningAvNaringsinntektFradrag-grp-242>
<NaringsinntektSaregneTilleggOgFradrag-grp-244 gruppeid="244">
	<SaregneTilleggFradrag-grp-2050 gruppeid="2050">
		<EndringSkattemessigRegnskapsmessig-datadef-262 orid="262">' . $data['D262'] . '</EndringSkattemessigRegnskapsmessig-datadef-262>
		<NaringsinntektGrunnlagPersoninntekt-datadef-22056 orid="22056">' . $data['D22056'] . '</NaringsinntektGrunnlagPersoninntekt-datadef-22056>
		<ForSamvirkeforetak-grp-4955 gruppeid="4955">
			<KjopsutbytteSamvirkeforetakAvsatt-datadef-276 orid="276">' . $data['D276'] . '</KjopsutbytteSamvirkeforetakAvsatt-datadef-276>
			<AndelskapitalFelleseidSamvirkeforetakOverforinger-datadef-277 orid="277">' . $data['D277'] . '</AndelskapitalFelleseidSamvirkeforetakOverforinger-datadef-277>
		</ForSamvirkeforetak-grp-4955>
		<Rentekostnader-datadef-91 orid="91">' . $data['D91'] . '</Rentekostnader-datadef-91>
		<RenteinntekterLivsforsikring-datadef-275 orid="275">' . $data['D275'] . '</RenteinntekterLivsforsikring-datadef-275>
		<FinansposterBeregningNaringsinntektNetto-datadef-22055 orid="22055">' . $data['D22055'] . '</FinansposterBeregningNaringsinntektNetto-datadef-22055>
		<NaringsinntektGrunnlagPersoninntekt-datadef-6675 orid="6675">' . $data['D6675'] . '</NaringsinntektGrunnlagPersoninntekt-datadef-6675>
	</SaregneTilleggFradrag-grp-2050>
</NaringsinntektSaregneTilleggOgFradrag-grp-244>
<FordelingAvInntektPaNaring-grp-4956 gruppeid="4956">
	<FordelingPaNaringer-grp-4957 gruppeid="4957">
		<EnhetNaringTypeSpesifisert-datadef-19811 orid="19811">' . $data['D19811'] . '</EnhetNaringTypeSpesifisert-datadef-19811>
		<EnhetNaringSpesifisertNaring-datadef-19890 orid="19890">' . $data['D19890'] . '</EnhetNaringSpesifisertNaring-datadef-19890>
		<NaringPersoninntektsskjemaSpesifisert-datadef-19790 orid="19790">' . $data['D19790'] . '</NaringPersoninntektsskjemaSpesifisert-datadef-19790>
		<ResultatFordelingSpesifisert-datadef-19791 orid="19791">' . $data['D19791'] . '</ResultatFordelingSpesifisert-datadef-19791>
		<ResultatSkogbrukReindriftKorreksjonerSpesifisert-datadef-19792 orid="19792">' . $data['D19792'] . '</ResultatSkogbrukReindriftKorreksjonerSpesifisert-datadef-19792>
		<ResultatPrimarnaringKorreksjonerAndreSpesifisert-datadef-19793 orid="19793">' . $data['D19793'] . '</ResultatPrimarnaringKorreksjonerAndreSpesifisert-datadef-19793>
		<EnhetNaringsinntektSkattepliktigSpesifisert-datadef-19794 orid="19794">' . $data['D19794'] . '</EnhetNaringsinntektSkattepliktigSpesifisert-datadef-19794>
		<EnhetNaringsinntektSkattepliktigInnehaverSpesifisert-datadef-19795 orid="19795">' . $data['D19795'] . '</EnhetNaringsinntektSkattepliktigInnehaverSpesifisert-datadef-19795>
		<EnhetNaringsinntektSkattepliktigEktefelleMvSpesifisert-datadef-19796 orid="19796">' . $data['D19796'] . '</EnhetNaringsinntektSkattepliktigEktefelleMvSpesifisert-datadef-19796>
	</FordelingPaNaringer-grp-4957>
	<SkattepliktigNaringsinntekt-grp-4958 gruppeid="4958">
		<EnhetNaringsinntektPositivSkattepliktig-datadef-19797 orid="19797">' . $data['D19797'] . '</EnhetNaringsinntektPositivSkattepliktig-datadef-19797>
		<EnhetNaringsinntektUnderskudd-datadef-19800 orid="19800">' . $data['D19800'] . '</EnhetNaringsinntektUnderskudd-datadef-19800>
	</SkattepliktigNaringsinntekt-grp-4958>
	<FordeltPaInnehaver-grp-4959 gruppeid="4959">
		<EnhetNaringsinntektPositivSkattepliktigInnehaver-datadef-19798 orid="19798">' . $data['D19798'] . '</EnhetNaringsinntektPositivSkattepliktigInnehaver-datadef-19798>
		<EnhetNaringsinntektUnderskuddInnehaver-datadef-19801 orid="19801">' . $data['D19801'] . '</EnhetNaringsinntektUnderskuddInnehaver-datadef-19801>
	</FordeltPaInnehaver-grp-4959>
	<FordeltPaEktefelle-grp-4960 gruppeid="4960">
		<EnhetNaringsinntektPositivSkattepliktigEktefelleMv-datadef-19799 orid="19799">' . $data['D19799'] . '</EnhetNaringsinntektPositivSkattepliktigEktefelleMv-datadef-19799>
		<EnhetNaringsinntektUnderskuddEktefelleMv-datadef-19802 orid="19802">' . $data['D19802'] . '</EnhetNaringsinntektUnderskuddEktefelleMv-datadef-19802>
	</FordeltPaEktefelle-grp-4960>
</FordelingAvInntektPaNaring-grp-4956>
<Revisjonspliktig-grp-246 gruppeid="246">
	<Revisjonsplikt-datadef-310 orid="310">' . $data['D310'] . '</Revisjonsplikt-datadef-310>
	<PersoninntektAS-datadef-279 orid="279">' . $data['D279'] . '</PersoninntektAS-datadef-279>
</Revisjonspliktig-grp-246>';
}
?>
