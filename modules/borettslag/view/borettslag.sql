CREATE TABLE `leilighet` (
  `LeilighetID` int(11) NOT NULL auto_increment,
  `Borettslag` int(11) NOT NULL,
  `LeilighetNr` varchar(10) NOT NULL default '0',
  `TS` timestamp(14) default NULL,
  `Kvadrat` float default NULL,
  `AndelTotal` int(11) NULL,
  `BorettInnskudd` int(11) NULL,
  PRIMARY KEY  (`LeilighetID`)
);

CREATE TABLE `eierforhold` (
  `EierforholdID` int(11) NOT NULL auto_increment,
  `LeilighetID` int(11) NOT NULL,
  `AccountPlanID` int(11) NOT NULL,
  `TS` timestamp(14) default NULL,
  `Andelsbrev` float default NULL,
  `BorettInnskudd` int(10) default NULL,
  `Husleie` int(10) default NULL,
  `FraDato` date default NULL,
  `TilDato` date default NULL,
  `Kvadrat` float default NULL,
  PRIMARY KEY  (`EierforholdID`)
);

CREATE TABLE `borettslag` (
  `BorettslagID` int(11) NOT NULL auto_increment,
  `Navn` varchar(255) NOT NULL default '0',
  `TS` timestamp(14) NOT NULL,
  `Kvadrat` float default NULL,
  `Andelsbrev` float default NULL,
  `BorettInnskudd` int(12) default NULL,
  `Prosentinntekt` int(12) default NULL,
  `Inntekter` int(12) default NULL,
  `Utgifter` int(12) default NULL,
  `Ligningsverdi` int(12) default NULL,
  `Formue` int(12) default NULL,
  `Gjeld` int(12) default NULL,
  `Kostpris` int(12) default NULL,
  PRIMARY KEY  (`BorettslagID`)
);

