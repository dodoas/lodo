SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;

alter table country add column LocalName varchar(100) after Name;

update country set LocalName = 'Andorra' WHERE Code = 'AD';
update country set LocalName = 'De forente arabiske emirater' WHERE Code = 'AE';
update country set LocalName = 'Afghanistan' WHERE Code = 'AF';
update country set LocalName = 'Antigua og Barbuda' WHERE Code = 'AG';
update country set LocalName = 'Anguilla' WHERE Code = 'AI';
update country set LocalName = 'Albania' WHERE Code = 'AL';
update country set LocalName = 'Armenia' WHERE Code = 'AM';
update country set LocalName = 'Angola' WHERE Code = 'AO';
update country set LocalName = 'Antarktis' WHERE Code = 'AQ';
update country set LocalName = 'Argentina' WHERE Code = 'AR';
update country set LocalName = 'Amerikansk Samoa' WHERE Code = 'AS';
update country set LocalName = 'Østerrike' WHERE Code = 'AS';
update country set LocalName = 'Australia' WHERE Code = 'AU';
update country set LocalName = 'Aruba' WHERE Code = 'AW';
update country set LocalName = 'Åland' WHERE Code = 'AX';
update country set LocalName = 'Aserbajdsjan' WHERE Code = 'AZ';
update country set LocalName = 'Bosnia-Hercegovina' WHERE Code = 'BA';
update country set LocalName = 'Barbados' WHERE Code = 'BB';
update country set LocalName = 'Bangladesh' WHERE Code = 'BD';
update country set LocalName = 'Belgia' WHERE Code = 'BE';
update country set LocalName = 'Burkina Faso' WHERE Code = 'BF';
update country set LocalName = 'Bulgaria' WHERE Code = 'BG';
update country set LocalName = 'Bahrain' WHERE Code = 'BH';
update country set LocalName = 'Burundi' WHERE Code = 'BI';
update country set LocalName = 'Benin' WHERE Code = 'BJ';
update country set LocalName = 'Saint-Barthélemy' WHERE Code = 'BL';
update country set LocalName = 'Bermuda' WHERE Code = 'BM';
update country set LocalName = 'Brunei' WHERE Code = 'BN';
update country set LocalName = 'Bolivia' WHERE Code = 'BO';
update country set LocalName = 'Bonaire, Sint Eustatius og Saba' WHERE Code = 'BQ';
update country set LocalName = 'Brasil' WHERE Code = 'BR';
update country set LocalName = 'Bahamas' WHERE Code = 'BS';
update country set LocalName = 'Bhutan' WHERE Code = 'BT';
update country set LocalName = 'Bouvetøya' WHERE Code = 'BV';
update country set LocalName = 'Botswana' WHERE Code = 'BW';
update country set LocalName = 'Hviterussland' WHERE Code = 'BY';
update country set LocalName = 'Belize' WHERE Code = 'BZ';
update country set LocalName = 'Canada' WHERE Code = 'CA';
update country set LocalName = 'Kokosøyene' WHERE Code = 'CC';
update country set LocalName = 'Den demokratiske republikken Kongo' WHERE Code = 'CD';
update country set LocalName = 'Den sentralafrikanske republikk' WHERE Code = 'CF';
update country set LocalName = 'Republikken Kongo' WHERE Code = 'CG';
update country set LocalName = 'Sveits' WHERE Code = 'CH';
update country set LocalName = 'Elfenbenskysten' WHERE Code = 'CI';
update country set LocalName = 'Cookøyene' WHERE Code = 'CK';
update country set LocalName = 'Chile' WHERE Code = 'CL';
update country set LocalName = 'Kamerun' WHERE Code = 'CM';
update country set LocalName = 'Kina' WHERE Code = 'CN';
update country set LocalName = 'Colombia' WHERE Code = 'CO';
update country set LocalName = 'Costa Rica' WHERE Code = 'CR';
update country set LocalName = 'Cuba' WHERE Code = 'CU';
update country set LocalName = 'Kapp Verde' WHERE Code = 'CV';
update country set LocalName = 'Curaçao' WHERE Code = 'CW';
update country set LocalName = 'Christmasøya' WHERE Code = 'CX';
update country set LocalName = 'Kypros' WHERE Code = 'CY';
update country set LocalName = 'Tsjekkia' WHERE Code = 'CZ';
update country set LocalName = 'Tyskland' WHERE Code = 'DE';
update country set LocalName = 'Djibouti' WHERE Code = 'DJ';
update country set LocalName = 'Danmark' WHERE Code = 'DK';
update country set LocalName = 'Dominica' WHERE Code = 'DM';
update country set LocalName = 'Den dominikanske republikk' WHERE Code = 'DO';
update country set LocalName = 'Algerie' WHERE Code = 'DZ';
update country set LocalName = 'Ecuador' WHERE Code = 'EC';
update country set LocalName = 'Estland' WHERE Code = 'EE';
update country set LocalName = 'Egypt' WHERE Code = 'EG';
update country set LocalName = 'Vest-Sahara' WHERE Code = 'EH';
update country set LocalName = 'Eritrea' WHERE Code = 'ER';
update country set LocalName = 'Spania' WHERE Code = 'ES';
update country set LocalName = 'Etiopia' WHERE Code = 'ET';
update country set LocalName = 'Finland' WHERE Code = 'FI';
update country set LocalName = 'Fiji' WHERE Code = 'FJ';
update country set LocalName = 'Falklandsøyene' WHERE Code = 'FK';
update country set LocalName = 'Mikronesiaføderasjonen' WHERE Code = 'FM';
update country set LocalName = 'Færøyene' WHERE Code = 'FO';
update country set LocalName = 'Frankrike' WHERE Code = 'FR';
update country set LocalName = 'Gabon' WHERE Code = 'GA';
update country set LocalName = 'Storbritannia' WHERE Code = 'GB';
update country set LocalName = 'Storbritannia' WHERE Code = 'UK';
update country set LocalName = 'Grenada' WHERE Code = 'GD';
update country set LocalName = 'Georgia' WHERE Code = 'GE';
update country set LocalName = 'Fransk Guyana' WHERE Code = 'GF';
update country set LocalName = 'Guernsey' WHERE Code = 'GG';
update country set LocalName = 'Ghana' WHERE Code = 'GH';
update country set LocalName = 'Gibraltar' WHERE Code = 'GI';
update country set LocalName = 'Grønland' WHERE Code = 'GL';
update country set LocalName = 'Gambia' WHERE Code = 'GM';
update country set LocalName = 'Guinea' WHERE Code = 'GN';
update country set LocalName = 'Guadeloupe' WHERE Code = 'GP';
update country set LocalName = 'Ekvatorial-Guinea' WHERE Code = 'GQ';
update country set LocalName = 'Hellas' WHERE Code = 'GR';
update country set LocalName = 'Sør-Georgia og Sør-Sandwichøyene' WHERE Code = 'GS';
update country set LocalName = 'Guatemala' WHERE Code = 'GT';
update country set LocalName = 'Guam' WHERE Code = 'GU';
update country set LocalName = 'Guinea-Bissau' WHERE Code = 'GW';
update country set LocalName = 'Guyana' WHERE Code = 'GY';
update country set LocalName = 'Hongkong' WHERE Code = 'HK';
update country set LocalName = 'Heard- og McDonaldøyene' WHERE Code = 'HM';
update country set LocalName = 'Honduras' WHERE Code = 'HN';
update country set LocalName = 'Kroatia' WHERE Code = 'HR';
update country set LocalName = 'Haiti' WHERE Code = 'HT';
update country set LocalName = 'Ungarn' WHERE Code = 'HU';
update country set LocalName = 'Indonesia' WHERE Code = 'ID';
update country set LocalName = 'Irland' WHERE Code = 'IE';
update country set LocalName = 'Israel' WHERE Code = 'IL';
update country set LocalName = 'Man' WHERE Code = 'IM';
update country set LocalName = 'India' WHERE Code = 'IN';
update country set LocalName = 'Det britiske territoriet i Indiahavet' WHERE Code = 'IO';
update country set LocalName = 'Irak' WHERE Code = 'IQ';
update country set LocalName = 'Iran' WHERE Code = 'IR';
update country set LocalName = 'Island' WHERE Code = 'IS';
update country set LocalName = 'Italia' WHERE Code = 'IT';
update country set LocalName = 'Jersey' WHERE Code = 'JE';
update country set LocalName = 'Jamaica' WHERE Code = 'JM';
update country set LocalName = 'Jordan' WHERE Code = 'JO';
update country set LocalName = 'Japan' WHERE Code = 'JP';
update country set LocalName = 'Kenya' WHERE Code = 'KE';
update country set LocalName = 'Kirgisistan' WHERE Code = 'KG';
update country set LocalName = 'Kambodsja' WHERE Code = 'KH';
update country set LocalName = 'Kiribati' WHERE Code = 'KI';
update country set LocalName = 'Komorene' WHERE Code = 'KM';
update country set LocalName = 'Saint Kitts og Nevis' WHERE Code = 'KN';
update country set LocalName = 'Nord-Korea' WHERE Code = 'KP';
update country set LocalName = 'Sør-Korea' WHERE Code = 'KR';
update country set LocalName = 'Kuwait' WHERE Code = 'KW';
update country set LocalName = 'Caymanøyene' WHERE Code = 'KY';
update country set LocalName = 'Kasakhstan' WHERE Code = 'KZ';
update country set LocalName = 'Laos' WHERE Code = 'LA';
update country set LocalName = 'Libanon' WHERE Code = 'LB';
update country set LocalName = 'Saint Lucia' WHERE Code = 'LC';
update country set LocalName = 'Liechtenstein' WHERE Code = 'LI';
update country set LocalName = 'Sri Lanka' WHERE Code = 'LK';
update country set LocalName = 'Liberia' WHERE Code = 'LR';
update country set LocalName = 'Lesotho' WHERE Code = 'LS';
update country set LocalName = 'Litauen' WHERE Code = 'LT';
update country set LocalName = 'Luxembourg' WHERE Code = 'LU';
update country set LocalName = 'Latvia' WHERE Code = 'LV';
update country set LocalName = 'Libya' WHERE Code = 'LY';
update country set LocalName = 'Marokko' WHERE Code = 'MA';
update country set LocalName = 'Monaco' WHERE Code = 'MC';
update country set LocalName = 'Moldova' WHERE Code = 'MD';
update country set LocalName = 'Montenegro' WHERE Code = 'ME';
update country set LocalName = 'Saint-Martin' WHERE Code = 'MF';
update country set LocalName = 'Madagaskar' WHERE Code = 'MG';
update country set LocalName = 'Marshalløyene' WHERE Code = 'MH';
update country set LocalName = 'Makedonia' WHERE Code = 'MK';
update country set LocalName = 'Mali' WHERE Code = 'ML';
update country set LocalName = 'Myanmar' WHERE Code = 'MM';
update country set LocalName = 'Macau' WHERE Code = 'MO';
update country set LocalName = 'Nord-Marianene' WHERE Code = 'MP';
update country set LocalName = 'Martinique' WHERE Code = 'MQ';
update country set LocalName = 'Mauritania' WHERE Code = 'MR';
update country set LocalName = 'Montserrat' WHERE Code = 'MS';
update country set LocalName = 'Malta' WHERE Code = 'MT';
update country set LocalName = 'Mauritius' WHERE Code = 'MU';
update country set LocalName = 'Maldivene' WHERE Code = 'MV';
update country set LocalName = 'Malawi' WHERE Code = 'MW';
update country set LocalName = 'Mexico' WHERE Code = 'MX';
update country set LocalName = 'Malaysia' WHERE Code = 'MY';
update country set LocalName = 'Mosambik' WHERE Code = 'MZ';
update country set LocalName = 'Namibia' WHERE Code = 'NA';
update country set LocalName = 'Ny-Caledonia' WHERE Code = 'NC';
update country set LocalName = 'Niger' WHERE Code = 'NE';
update country set LocalName = 'Norfolkøya' WHERE Code = 'NF';
update country set LocalName = 'Nigeria' WHERE Code = 'NG';
update country set LocalName = 'Nicaragua' WHERE Code = 'NI';
update country set LocalName = 'Nederland' WHERE Code = 'NL';
update country set LocalName = 'Norge' WHERE Code = 'NO';
update country set LocalName = 'Nepal' WHERE Code = 'NP';
update country set LocalName = 'Nauru' WHERE Code = 'NR';
update country set LocalName = 'Niue' WHERE Code = 'NU';
update country set LocalName = 'Ny-Zealand' WHERE Code = 'NZ';
update country set LocalName = 'Oman' WHERE Code = 'OM';
update country set LocalName = 'Panama' WHERE Code = 'PA';
update country set LocalName = 'Peru' WHERE Code = 'PE';
update country set LocalName = 'Fransk Polynesia' WHERE Code = 'PF';
update country set LocalName = 'Papua Ny-Guinea' WHERE Code = 'PG';
update country set LocalName = 'Filippinene' WHERE Code = 'PH';
update country set LocalName = 'Pakistan' WHERE Code = 'PK';
update country set LocalName = 'Polen' WHERE Code = 'PL';
update country set LocalName = 'Saint-Pierre og Miquelon' WHERE Code = 'PM';
update country set LocalName = 'Pitcairnøyene' WHERE Code = 'PN';
update country set LocalName = 'Puerto Rico' WHERE Code = 'PR';
update country set LocalName = 'Palestina' WHERE Code = 'PS';
update country set LocalName = 'Portugal' WHERE Code = 'PT';
update country set LocalName = 'Palau' WHERE Code = 'PW';
update country set LocalName = 'Paraguay' WHERE Code = 'PY';
update country set LocalName = 'Qatar' WHERE Code = 'QA';
update country set LocalName = 'Réunion' WHERE Code = 'RE';
update country set LocalName = 'Romania' WHERE Code = 'RO';
update country set LocalName = 'Serbia' WHERE Code = 'RS';
update country set LocalName = 'Russland' WHERE Code = 'RU';
update country set LocalName = 'Rwanda' WHERE Code = 'RW';
update country set LocalName = 'Saudi-Arabia' WHERE Code = 'SA';
update country set LocalName = 'Salomonøyene' WHERE Code = 'SB';
update country set LocalName = 'Seychellene' WHERE Code = 'SC';
update country set LocalName = 'Sudan' WHERE Code = 'SD';
update country set LocalName = 'Sverige' WHERE Code = 'SE';
update country set LocalName = 'Singapore' WHERE Code = 'SG';
update country set LocalName = 'St. Helena, Ascension og Tristan da Cunha' WHERE Code = 'SH';
update country set LocalName = 'Slovenia' WHERE Code = 'SI';
update country set LocalName = 'Svalbard og Jan Mayen' WHERE Code = 'SJ';
update country set LocalName = 'Slovakia' WHERE Code = 'SK';
update country set LocalName = 'Sierra Leone' WHERE Code = 'SL';
update country set LocalName = 'San Marino' WHERE Code = 'SM';
update country set LocalName = 'Senegal' WHERE Code = 'SN';
update country set LocalName = 'Somalia' WHERE Code = 'SO';
update country set LocalName = 'Surinam' WHERE Code = 'SR';
update country set LocalName = 'Sør-Sudan' WHERE Code = 'SS';
update country set LocalName = 'São Tomé og Príncipe' WHERE Code = 'ST';
update country set LocalName = 'El Salvador' WHERE Code = 'SV';
update country set LocalName = 'Sint Maarten' WHERE Code = 'SX';
update country set LocalName = 'Syria' WHERE Code = 'SY';
update country set LocalName = 'Swaziland' WHERE Code = 'SZ';
update country set LocalName = 'Turks- og Caicosøyene' WHERE Code = 'TC';
update country set LocalName = 'Tsjad' WHERE Code = 'TD';
update country set LocalName = 'De franske sørterritorier' WHERE Code = 'TF';
update country set LocalName = 'Togo' WHERE Code = 'TG';
update country set LocalName = 'Thailand' WHERE Code = 'TH';
update country set LocalName = 'Tadsjikistan' WHERE Code = 'TJ';
update country set LocalName = 'Tokelau' WHERE Code = 'TK';
update country set LocalName = 'Timor-Leste' WHERE Code = 'TL';
update country set LocalName = 'Turkmenistan' WHERE Code = 'TM';
update country set LocalName = 'Tunisia' WHERE Code = 'TN';
update country set LocalName = 'Tonga' WHERE Code = 'TO';
update country set LocalName = 'Tyrkia' WHERE Code = 'TR';
update country set LocalName = 'Trinidad og Tobago' WHERE Code = 'TT';
update country set LocalName = 'Tuvalu' WHERE Code = 'TV';
update country set LocalName = 'Taiwan' WHERE Code = 'TW';
update country set LocalName = 'Tanzania' WHERE Code = 'TZ';
update country set LocalName = 'Ukraina' WHERE Code = 'UA';
update country set LocalName = 'Uganda' WHERE Code = 'UG';
update country set LocalName = 'USAs ytre småøyer' WHERE Code = 'UM';
update country set LocalName = 'USA' WHERE Code = 'US'; 
update country set LocalName = 'Uruguay' WHERE Code = 'UY';
update country set LocalName = 'Usbekistan' WHERE Code = 'UZ';
update country set LocalName = 'Vatikanstaten' WHERE Code = 'VA';
update country set LocalName = 'Saint Vincent og Grenadinene' WHERE Code = 'VC';
update country set LocalName = 'Venezuela' WHERE Code = 'VE';
update country set LocalName = 'De britiske Jomfruøyene' WHERE Code = 'VG';
update country set LocalName = 'De amerikanske Jomfruøyene' WHERE Code = 'VI';
update country set LocalName = 'Vietnam' WHERE Code = 'VN';
update country set LocalName = 'Wallis og Futuna' WHERE Code = 'WF';
update country set LocalName = 'Samoa' WHERE Code = 'WS';
update country set LocalName = 'Jemen' WHERE Code = 'YE';
update country set LocalName = 'Sør-Afrika' WHERE Code = 'ZA';
update country set LocalName = 'Zambia' WHERE Code = 'ZM';

update country set LocalName = Name where LocalName is NULL;


SET character_set_client = @saved_cs_client;
