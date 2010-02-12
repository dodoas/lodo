<?php

		$xml = '<Skjema xmlns:brreg="http://www.brreg.no/or" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" skjemanummer="212" spesifikasjonsnummer="3148" blankettnummer="RF-0002" tittel="Alminnelig omsetningsoppgave" gruppeid="20" etatid="974761076">
<GenerellInformasjon-grp-2581 gruppeid="2581">
	<Avgiftspliktig-grp-50 gruppeid="50">
		<EnhetBedriftNavn-datadef-21771 orid="21771">' . $this->lodo->lodoCompany['name'] . '</EnhetBedriftNavn-datadef-21771>
		<EnhetBedriftAdresse-datadef-21773 orid="21773">' . $this->lodo->lodoCompany['streetaddr'] . '</EnhetBedriftAdresse-datadef-21773>
		<EnhetBedriftPostnummer-datadef-21774 orid="21774">' . $this->lodo->lodoCompany['zipcode'] . '</EnhetBedriftPostnummer-datadef-21774>
		<EnhetBedriftPoststed-datadef-21775 orid="21775">' . $this->lodo->lodoCompany['city'] . '</EnhetBedriftPoststed-datadef-21775>
		<OppgaveType-datadef-5659 orid="5659">' . $this->data[ 'd5659' ] . '</OppgaveType-datadef-5659>
		<EnhetBedriftOrganisasjonsnummer-datadef-21772 orid="21772">' . $this->config->GetConfig($this->config->TYPE_ORGNO) . '</EnhetBedriftOrganisasjonsnummer-datadef-21772>
		<EnhetBedriftKontonummer-datadef-21776 orid="21776">' . $this->config->GetConfig($this->config->TYPE_MVABANKACCOUNT) . '</EnhetBedriftKontonummer-datadef-21776>
	</Avgiftspliktig-grp-50>

	<Termin-grp-2582 gruppeid="2582">
		<TerminType-datadef-10092 orid="10092">' . $this->config->GetConfig($this->config->TYPE_TERMIN) . '</TerminType-datadef-10092>
		<Termin-datadef-10093 orid="10093">' . $this->mva->GetTerminItemNumber( $this->config->GetConfig($this->config->TYPE_TERMIN), $termin ) . '</Termin-datadef-10093>
		<TerminAr-datadef-10094 orid="10094">' . $year . '</TerminAr-datadef-10094>
	</Termin-grp-2582>
</GenerellInformasjon-grp-2581>
<Avgiftsposter-grp-2577 gruppeid="2577">
	<Grunnlag-grp-2578 gruppeid="2578">
		<OmsetningTermin-datadef-8446 orid="8446">' . $this->data[ 'd8446' ] .'</OmsetningTermin-datadef-8446>
		<OmsetningTerminAvgiftspliktig-datadef-10095 orid="10095">' . $this->data[ 'd10095' ] . '</OmsetningTerminAvgiftspliktig-datadef-10095>
		<OmsetningTerminAvgiftsfri-datadef-10096 orid="10096">' . $this->data[ 'd10096' ] . '</OmsetningTerminAvgiftsfri-datadef-10096>
		<MerverdiavgiftUtgaendeTerminHoySatsGrunnlag-datadef-10097 orid="10097">' . $this->data[ 'd10097' ] . '</MerverdiavgiftUtgaendeTerminHoySatsGrunnlag-datadef-10097>
		<MerverdiavgiftUtgaendeTerminMiddelsSatsGrunnlag-datadef-20319 orid="20319">' . $this->data[ 'd20319' ] . '</MerverdiavgiftUtgaendeTerminMiddelsSatsGrunnlag-datadef-20319>
		<MerverdiavgiftUtgaendeTerminLavSatsGrunnlag-datadef-14360 orid="14360">' . $this->data[ 'd14360' ] . '</MerverdiavgiftUtgaendeTerminLavSatsGrunnlag-datadef-14360>
		<MerverdiavgiftUtgaendeTjenesterUtlandTerminGrunnlag-datadef-14362 orid="14362">' . $this->data[ 'd14362' ] . '</MerverdiavgiftUtgaendeTjenesterUtlandTerminGrunnlag-datadef-14362>
	</Grunnlag-grp-2578>
	<BeregnetAvgift-grp-2579 gruppeid="2579">
		<MerverdiavgiftUtgaendeTerminHoySatsBeregnet-datadef-10098 orid="10098">' . $this->data[ 'd10098' ] . '</MerverdiavgiftUtgaendeTerminHoySatsBeregnet-datadef-10098>
		<MerverdiavgiftUtgaendeTerminMiddelsSatsBeregning-datadef-20320 orid="20320">' . $this->data[ 'd20320' ] . '</MerverdiavgiftUtgaendeTerminMiddelsSatsBeregning-datadef-20320>
		<MerverdiavgiftUtgaendeTerminLavSatsBeregnet-datadef-14361 orid="14361">' . $this->data[ 'd14361' ] . '</MerverdiavgiftUtgaendeTerminLavSatsBeregnet-datadef-14361>
		<MerverdiavgiftUtgaendeTjenesterUtlandTerminBeregnet-datadef-14363 orid="14363">' . $this->data[ 'd14363' ] . '</MerverdiavgiftUtgaendeTjenesterUtlandTerminBeregnet-datadef-14363>
		<MerverdiavgiftInngaendeTerminHoySats-datadef-8450 orid="8450">' . $this->data[ 'd8450' ] . '</MerverdiavgiftInngaendeTerminHoySats-datadef-8450>
		<MerverdiavgiftInngaendeTerminMiddelsSats-datadef-20322 orid="20322">' . $this->data[ 'd20322' ] . '</MerverdiavgiftInngaendeTerminMiddelsSats-datadef-20322>
		<MerverdiavgiftInngaendeTerminLavSats-datadef-14364 orid="14364">' . $this->data[ 'd14364' ] . '</MerverdiavgiftInngaendeTerminLavSats-datadef-14364>
		';
/* IF */
if (is_numeric($this->data[ 'd8452' ]) && $this->data[ 'd8452' ] != 0) {$xml .= '<AvgiftTerminTilGode-datadef-8452 orid="8452">' . $this->data[ 'd8452' ] . '</AvgiftTerminTilGode-datadef-8452>';}
else {$xml .= '<AvgiftTerminABetale-datadef-8453 orid="8453">' . $this->data[ 'd8453' ] . '</AvgiftTerminABetale-datadef-8453>';}
/* /IF */
		$xml .= '</BeregnetAvgift-grp-2579>
</Avgiftsposter-grp-2577>
<Tilleggsopplysninger-grp-197 gruppeid="197">
	<TilleggsopplysningerForklaringSendt-datadef-8458 orid="8458">Nei</TilleggsopplysningerForklaringSendt-datadef-8458>
</Tilleggsopplysninger-grp-197>
</Skjema>';

?>
