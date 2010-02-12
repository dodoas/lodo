<?php
// Filnavn: schemaxml_748_3364.php
// Skjema: RF-1231    Spesifikasjon av formue/gjeld og inntekt/fradrag tilknyttet utlandet
if ( $head == true )
{
$xml = '<GenerellInformasjon-grp-1057 gruppeid="1057">
	<Avgiver-grp-389 gruppeid="389">
		<OppgavegiverNavn-datadef-68 orid="68">' . $data['D68'] . '</OppgavegiverNavn-datadef-68>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
	</Avgiver-grp-389>
</GenerellInformasjon-grp-1057>
';
}
else
{
$xml = '<GenerellInformasjon-grp-1057 gruppeid="1057">
	<Avgiver-grp-389 gruppeid="389">
		<OppgavegiverNavn-datadef-68 orid="68">' . $data['D68'] . '</OppgavegiverNavn-datadef-68>
		<OppgavegiverFodselsnummer-datadef-26 orid="26">' . $data['D26'] . '</OppgavegiverFodselsnummer-datadef-26>
	</Avgiver-grp-389>
</GenerellInformasjon-grp-1057>
<InnskuddIUtenlandskeBankerMv-grp-390 gruppeid="390">
	<InnskuddIUtenlandskeBankerMv-grp-403 gruppeid="403">
		<BankMvUtlandNavnSpesifisertBankMv-datadef-8080 orid="8080">' . $data['D8080'] . '</BankMvUtlandNavnSpesifisertBankMv-datadef-8080>
		<KontohaverUtlandKontonummerSpesifisertBankMv-datadef-8081 orid="8081">' . $data['D8081'] . '</KontohaverUtlandKontonummerSpesifisertBankMv-datadef-8081>
		<KontohaverUtlandSpesifisertBankMv-datadef-8082 orid="8082">' . $data['D8082'] . '</KontohaverUtlandSpesifisertBankMv-datadef-8082>
		<BankinnskuddMvUtlandValutaFjoraretSpesifisertBankMv-datadef-8083 orid="8083">' . $data['D8083'] . '</BankinnskuddMvUtlandValutaFjoraretSpesifisertBankMv-datadef-8083>
		<BankinnskuddMvUtlandSpesifisertBankMv-datadef-8085 orid="8085">' . $data['D8085'] . '</BankinnskuddMvUtlandSpesifisertBankMv-datadef-8085>
		<ValutakursSpesifisertBankMv-datadef-5761 orid="5761">' . $data['D5761'] . '</ValutakursSpesifisertBankMv-datadef-5761>
		<RenteinntekterMvUtlandBankinnskuddMvSpesifisertBankMv-datadef-8087 orid="8087">' . $data['D8087'] . '</RenteinntekterMvUtlandBankinnskuddMvSpesifisertBankMv-datadef-8087>
		<ValutakursRenterOmregningSpesifisertBankMv-datadef-21990 orid="21990">' . $data['D21990'] . '</ValutakursRenterOmregningSpesifisertBankMv-datadef-21990>
		<SkattetrekkUtlandRenteavkastningSpesifisertBankMv-datadef-8089 orid="8089">' . $data['D8089'] . '</SkattetrekkUtlandRenteavkastningSpesifisertBankMv-datadef-8089>
	</InnskuddIUtenlandskeBankerMv-grp-403>
</InnskuddIUtenlandskeBankerMv-grp-390>
<BSUSparingIAnnenEOSStat-grp-4937 gruppeid="4937">
	<BankMvBSUEOSStatNavn-datadef-21991 orid="21991">' . $data['D21991'] . '</BankMvBSUEOSStatNavn-datadef-21991>
	<KontohaverBSUEOSStatKontonummer-datadef-21992 orid="21992">' . $data['D21992'] . '</KontohaverBSUEOSStatKontonummer-datadef-21992>
	<KontohaverBSUEOSStatNavn-datadef-21993 orid="21993">' . $data['D21993'] . '</KontohaverBSUEOSStatNavn-datadef-21993>
	<BankinnskuddMvBSUEOSStatValutaFjoraret-datadef-21994 orid="21994">' . $data['D21994'] . '</BankinnskuddMvBSUEOSStatValutaFjoraret-datadef-21994>
	<BankinnskuddMvBSUEOSStatBelop-datadef-21995 orid="21995">' . $data['D21995'] . '</BankinnskuddMvBSUEOSStatBelop-datadef-21995>
	<ValutakursBSUEOSStat-datadef-21996 orid="21996">' . $data['D21996'] . '</ValutakursBSUEOSStat-datadef-21996>
	<RenteinntekterMvBSUEOSStatBelop-datadef-21997 orid="21997">' . $data['D21997'] . '</RenteinntekterMvBSUEOSStatBelop-datadef-21997>
	<ValutakursRenterOmregningBSUEOSStatBelop-datadef-21998 orid="21998">' . $data['D21998'] . '</ValutakursRenterOmregningBSUEOSStatBelop-datadef-21998>
	<SkattetrekkBSUEOSStatRenteavkastningBelop-datadef-21999 orid="21999">' . $data['D21999'] . '</SkattetrekkBSUEOSStatRenteavkastningBelop-datadef-21999>
	<SparebelopInnbetaltBSUEOSStat-datadef-22000 orid="22000">' . $data['D22000'] . '</SparebelopInnbetaltBSUEOSStat-datadef-22000>
	<UttakBoligformalBSUEOSStatBelop-datadef-22001 orid="22001">' . $data['D22001'] . '</UttakBoligformalBSUEOSStatBelop-datadef-22001>
	<UttakAnnetFormalBSUEOSStatBelop-datadef-22002 orid="22002">' . $data['D22002'] . '</UttakAnnetFormalBSUEOSStatBelop-datadef-22002>
	<IIISumForAlleKontiInklusiveBSUKonto-grp-4972 gruppeid="4972">
		<BankinnskuddMvUtlandSkattemessig-datadef-8090 orid="8090">' . $data['D8090'] . '</BankinnskuddMvUtlandSkattemessig-datadef-8090>
		<RenteavkastningMvUtlandBankinnskuddMv-datadef-8091 orid="8091">' . $data['D8091'] . '</RenteavkastningMvUtlandBankinnskuddMv-datadef-8091>
		<SkattetrekkUtlandRenteavkastning-datadef-8092 orid="8092">' . $data['D8092'] . '</SkattetrekkUtlandRenteavkastning-datadef-8092>
	</IIISumForAlleKontiInklusiveBSUKonto-grp-4972>
</BSUSparingIAnnenEOSStat-grp-4937>
';
}
?>