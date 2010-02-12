-- MySQL dump 10.9
--
-- Host: localhost    Database: empatix
-- ------------------------------------------------------
-- Server version	4.1.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `AccountID` int(11) NOT NULL auto_increment,
  `ValidFrom` date NOT NULL default '0000-00-00',
  `ValidTo` date NOT NULL default '0000-00-00',
  `BankName` varchar(255) NOT NULL default '',
  `OwnerName` varchar(255) NOT NULL default '',
  `AccountNumber` varchar(20) NOT NULL default '',
  `AccountDescription` varchar(255) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `AccountPlanID` bigint(20) default NULL,
  `VoucherType` char(3) default NULL,
  `DefaultPeriod` varchar(7) default NULL,
  `Sort` int(11) default '10',
  `includeinsaldo` smallint(6) default NULL,
  PRIMARY KEY  (`AccountID`),
  KEY `AccountPlanID` (`AccountPlanID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `accountline`
--

CREATE TABLE `accountline` (
  `InterestDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `AccountLineID` int(11) NOT NULL auto_increment,
  `UseDate` date NOT NULL default '0000-00-00',
  `AmountIn` decimal(16,3) default NULL,
  `AmountOut` decimal(16,3) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `BookKeepingDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `KID` varchar(30) default NULL,
  `ProjectID` int(11) NOT NULL default '0',
  `ProjecActivitytID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `AccountID` int(11) NOT NULL default '0',
  `ReskontroAccountPlanID` bigint(20) default NULL,
  `Period` varchar(7) default NULL,
  `JournalID` int(11) default NULL,
  `Priority` int(11) default NULL,
  `Day` char(2) default NULL,
  `ReskontroOrgNumber` varchar(20) default NULL,
  `Description` varchar(255) default NULL,
  `DepartmentID` int(11) default NULL,
  `Vat` decimal(16,2) default NULL,
  `VatID` int(11) default NULL,
  `Comment` varchar(255) default NULL,
  `AutoResultAccount` smallint(6) default NULL,
  `Approved` smallint(6) default NULL,
  `ArchiveRef` varchar(30) default NULL,
  `ResultAccountPlanID` bigint(20) default NULL,
  `ResultQuantity` decimal(16,3) default NULL,
  PRIMARY KEY  (`AccountLineID`),
  KEY `AccountLineID` (`AccountLineID`),
  KEY `AccountID` (`AccountID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `accountperiod`
--

CREATE TABLE `accountperiod` (
  `AccountPeriodID` int(11) NOT NULL auto_increment,
  `Status` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedDate` datetime default NULL,
  `CreatedByID` int(11) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Period` char(7) default NULL,
  `Payed` smallint(6) default NULL,
  PRIMARY KEY  (`AccountPeriodID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `accountplan`
--

CREATE TABLE `accountplan` (
  `AccountPlanID` bigint(20) NOT NULL default '0',
  `AccountName` varchar(255) default NULL,
  `VatID` int(11) default NULL,
  `Report` varchar(255) default NULL,
  `Budget` double default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `InsertedDateTime` datetime default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `debittext` varchar(20) default NULL,
  `credittext` varchar(20) default NULL,
  `Currency` char(3) default NULL,
  `Description` text,
  `EnableReskontro` smallint(6) default '0',
  `EnableQuantity` smallint(6) default NULL,
  `EnableDepartment` smallint(6) default NULL,
  `EnableProject` smallint(6) default NULL,
  `EnableReport1` smallint(6) default NULL,
  `EnableReport2` smallint(6) default NULL,
  `EnableReport3` smallint(6) default NULL,
  `EnableReport4` smallint(6) default NULL,
  `EnableReport5` smallint(6) default NULL,
  `EnableReport6` smallint(6) default NULL,
  `EnableReport7` smallint(6) default NULL,
  `EnableReport8` smallint(6) default NULL,
  `EnableReport9` smallint(6) default NULL,
  `EnableReport10` smallint(6) default NULL,
  `EnableReportShort` smallint(6) default NULL,
  `EnableBudgetResult` smallint(6) default NULL,
  `EnableBudgetLikviditet` smallint(6) default NULL,
  `Report1Line` varchar(10) default '0',
  `Report2Line` varchar(10) default '0',
  `Report3Line` varchar(10) default '0',
  `Report4Line` varchar(10) default '0',
  `Report5Line` varchar(10) default '0',
  `Report6Line` varchar(10) default '0',
  `Report7Line` varchar(10) default '0',
  `Report8Line` varchar(10) default '0',
  `Report9Line` varchar(10) default '0',
  `Report10Line` varchar(10) default '0',
  `ReportShort` int(11) default NULL,
  `EnableVATOverride` smallint(6) default NULL,
  `EnablePostPost` smallint(6) default NULL,
  `ReskontroFromAccount` bigint(20) default NULL,
  `ReskontroToAccount` bigint(20) default NULL,
  `EnableZeroYearly` int(11) default NULL,
  `Reskontro` int(11) default NULL,
  `EnableVAT` smallint(6) default NULL,
  `Address` varchar(255) default NULL,
  `ZipCode` varchar(10) default NULL,
  `Email` varchar(255) default NULL,
  `DomesticBankAccount` varchar(255) default NULL,
  `AbroadBankAccount` varchar(20) default NULL,
  `EnableSalary` smallint(6) default NULL,
  `SocialSecurityNumber` int(11) default NULL,
  `Municipality` varchar(255) default NULL,
  `EmployedFromDate` date default NULL,
  `EmployedToDate` date default NULL,
  `SalaryDue` float default NULL,
  `EnableCredit` smallint(6) default NULL,
  `CreditDays` int(11) default NULL,
  `EnableAutogiro` smallint(6) default NULL,
  `EnableNettbank` smallint(6) default NULL,
  `DebitColor` varchar(10) default NULL,
  `CreditColor` varchar(10) default NULL,
  `Active` smallint(6) default '1',
  `EnableCurrency` smallint(6) default NULL,
  `CustomerNumber` varchar(10) default NULL,
  `EnableMotkontoBalanse` smallint(6) default NULL,
  `MotkontoBalanse1` bigint(20) default NULL,
  `MotkontoBalanse2` bigint(20) default NULL,
  `MotkontoBalanse3` bigint(20) default NULL,
  `MotkontoResultat1` bigint(20) default NULL,
  `MotkontoResultat2` bigint(20) default NULL,
  `MotkontoResultat3` bigint(20) default NULL,
  `EnableMotkontoResultat` smallint(6) default NULL,
  `LastUsedTime` datetime default NULL,
  `City` varchar(50) default NULL,
  `EnablePersonalUsage` smallint(6) default NULL,
  `EnableMoneyFlow` smallint(6) default NULL,
  `EnableSaldo` smallint(6) default NULL,
  `NorwegianStandardText` varchar(255) default NULL,
  `OrgNumber` varchar(20) default NULL,
  `SocietyNumber` varchar(15) default NULL,
  `KommuneID` int(11) default NULL,
  `WorkStart` date default NULL,
  `WorkStop` date default NULL,
  `EnableNorwegianStandard` smallint(6) default NULL,
  `PersonID` int(11) default NULL,
  `ProjectID` int(11) default '0',
  `DepartmentID` int(11) default '0',
  `TabellTrekk` varchar(10) default NULL,
  `ProsentTrekk` varchar(10) default NULL,
  `Phone` varchar(20) default NULL,
  `IPoBox` varchar(255) default NULL,
  `IPoBoxCity` varchar(255) default NULL,
  `Mobile` varchar(255) default NULL,
  `EnableInvoicePoBox` smallint(6) default NULL,
  `EnableInvoiceAddress` smallint(6) default NULL,
  `IPoBoxZipCode` varchar(255) default NULL,
  `BirthDate` datetime default NULL,
  `WorkPercent` decimal(16,2) default NULL,
  `EnableSalaryPayment` smallint(6) default NULL,
  `Report1LinePage` varchar(5) NOT NULL default '',
  `Report2LinePage` varchar(5) NOT NULL default '',
  `Report3LinePage` varchar(5) NOT NULL default '',
  `Report4LinePage` varchar(5) NOT NULL default '',
  `Report5LinePage` varchar(5) NOT NULL default '',
  `Report6LinePage` varchar(5) NOT NULL default '',
  `Report7LinePage` varchar(5) NOT NULL default '',
  `Report8LinePage` varchar(5) NOT NULL default '',
  `Report9LinePage` varchar(5) NOT NULL default '',
  `Report10LinePage` varchar(5) NOT NULL default '',
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `Report66Line` varchar(11) default NULL,
  `Report0002Line` varchar(11) default NULL,
  `Report1224Line` varchar(11) default NULL,
  `Report2224Line` varchar(11) default NULL,
  `Report1028Line` varchar(11) default NULL,
  `Report1125Line` varchar(11) default NULL,
  `Report1217Line` varchar(11) default NULL,
  `Report1122Line` varchar(11) default NULL,
  `Report1052Line` varchar(11) default NULL,
  `Report1223Line` varchar(11) default NULL,
  `Report1219Line` varchar(11) default NULL,
  `Report1061Line` varchar(11) default NULL,
  `Report1239Line` varchar(11) default NULL,
  `Report1215Line` varchar(11) default NULL,
  `Report1221Line` varchar(11) default NULL,
  `Report1037Line` varchar(11) default NULL,
  `Report1025Line` varchar(11) default NULL,
  `Report1231Line` varchar(11) default NULL,
  `Report1022Line` varchar(11) default NULL,
  `Report1086Line` varchar(11) default NULL,
  `EnableReport1224` smallint(6) default NULL,
  `EnableReport2224` smallint(6) default NULL,
  `InvoiceCommentCustomerPosition` varchar(10) NOT NULL default 'bottom',
  `AccountPlanType` varchar(9) default NULL,
  `UpdatedByPersonID` int(11) default NULL,
  `ReskontroAccountPlanType` varchar(9) default NULL,
  `AccountLineFreeTextMatch` varchar(255) default NULL,
  PRIMARY KEY  (`AccountPlanID`),
  KEY `Active` (`Active`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `activitycommunicationstruct`
--

CREATE TABLE `activitycommunicationstruct` (
  `ActivityCommunicationID` int(11) NOT NULL auto_increment,
  `Active` tinyint(4) default NULL,
  `DateCreated` date default NULL,
  `CreatedByID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Subject` varchar(255) default NULL,
  `Description` text,
  `ToPersons` varchar(255) default NULL,
  `MessageID` varchar(50) default NULL,
  PRIMARY KEY  (`ActivityCommunicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `activitydependencystruct`
--

CREATE TABLE `activitydependencystruct` (
  `ProjectActivityParentID` int(11) NOT NULL default '0',
  `ProjectActivityChildID` int(11) NOT NULL default '0',
  `Active` tinyint(4) default NULL,
  `DateCreated` date default NULL,
  `CreatedByID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProjectActivityParentID`,`ProjectActivityChildID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `activitydocumentstruct`
--

CREATE TABLE `activitydocumentstruct` (
  `ActivityDocumentID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `Active` tinyint(4) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `DateCreated` date default NULL,
  `CreatedByID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ActivityDocumentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `activitypersonstruct`
--

CREATE TABLE `activitypersonstruct` (
  `PersonID` int(11) NOT NULL default '0',
  `ProjectActivityID` int(11) NOT NULL default '0',
  `PersonRole` int(11) default NULL,
  `RoleDescription` varchar(100) default NULL,
  `Active` tinyint(4) default NULL,
  `Notify` int(11) default NULL,
  `DateCreated` date default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CostPrice` double default NULL,
  PRIMARY KEY  (`PersonID`,`ProjectActivityID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `altinn_config`
--

CREATE TABLE `altinn_config` (
  `config_id` int(11) NOT NULL auto_increment,
  `mvabankaccount` varchar(11) NOT NULL default '',
  `termintype` int(11) NOT NULL default '4',
  `fagsystemid` int(11) NOT NULL default '0',
  `password` varchar(100) NOT NULL default '',
  `batchsubno` int(11) NOT NULL default '0',
  PRIMARY KEY  (`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `altinn_packet`
--

CREATE TABLE `altinn_packet` (
  `packet_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL default '0',
  `status` int(11) NOT NULL default '0',
  `ts_created` int(11) NOT NULL default '0',
  `ts_modified` int(11) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `packettype` int(11) NOT NULL default '0',
  `termin` int(11) NOT NULL default '0',
  `termintype` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`packet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `altinn_schema`
--

CREATE TABLE `altinn_schema` (
  `instance_id` int(11) NOT NULL auto_increment,
  `packet_id` int(11) NOT NULL default '0',
  `schematype` int(11) NOT NULL default '0',
  `schemarevision` int(11) NOT NULL default '0',
  `data` text,
  PRIMARY KEY  (`instance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `altinn_tempxml`
--

CREATE TABLE `altinn_tempxml` (
  `Altinn_tempxmlID` int(10) NOT NULL auto_increment,
  `xmlcode` mediumtext,
  `sendersRef` varchar(250) default NULL,
  `tittel` varchar(250) default NULL,
  `TS` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`Altinn_tempxmlID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `altinnschemalist`
--

CREATE TABLE `altinnschemalist` (
  `AltinnschemalistID` int(11) NOT NULL auto_increment,
  `tvar` varchar(250) NOT NULL default '',
  `rfname` varchar(250) NOT NULL default '',
  `name` varchar(250) NOT NULL default '',
  `shortname` varchar(250) default '',
  `termintype` int(10) NOT NULL default '1',
  `fagsystemid` int(10) NOT NULL default '0',
  `revision` int(10) NOT NULL default '0',
  `active` int(10) NOT NULL default '1',
  PRIMARY KEY  (`AltinnschemalistID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `arbeidsgiveravgift`
--

CREATE TABLE `arbeidsgiveravgift` (
  `Code` int(11) NOT NULL auto_increment,
  `Percent` float default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Percent62` double default NULL,
  PRIMARY KEY  (`Code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `bankvotingline`
--

CREATE TABLE `bankvotingline` (
  `BankVotingLineID` int(11) NOT NULL auto_increment,
  `AccountID` int(11) default NULL,
  `VoucherPeriod` varchar(7) default NULL,
  `VoucherDate` date NOT NULL default '0000-00-00',
  `AmountIn` decimal(16,3) default NULL,
  `AmountOut` decimal(16,3) default NULL,
  `JournalID` int(11) default NULL,
  `VoucherType` char(3) default NULL,
  `Type` varchar(7) default NULL,
  `KID` varchar(255) default NULL,
  `Closed` smallint(6) default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `InsertedDateTime` datetime default NULL,
  `UpdatedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`BankVotingLineID`),
  KEY `AccountID` (`AccountID`,`VoucherPeriod`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `bankvotingperiod`
--

CREATE TABLE `bankvotingperiod` (
  `BankVotingPeriodID` int(11) NOT NULL auto_increment,
  `AccountID` int(11) default NULL,
  `Period` varchar(7) default NULL,
  `AmountIn` decimal(16,3) default NULL,
  `AmountOut` decimal(16,3) default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `InsertedDateTime` datetime default NULL,
  `UpdatedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Comment` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  `Locked` smallint(6) default NULL,
  PRIMARY KEY  (`BankVotingPeriodID`),
  UNIQUE KEY `AccountID_2` (`AccountID`,`Period`),
  KEY `AccountID` (`AccountID`,`Period`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `baskethead`
--

CREATE TABLE `baskethead` (
  `BasketHeadID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `SessionID` varchar(40) default NULL,
  `BasketName` varchar(50) default NULL,
  `BasketType` int(11) default NULL,
  `Agreement` int(11) default NULL,
  `PriceInTotal` float default NULL,
  `PriceOutTotal` float default NULL,
  `Active` set('on','off') default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `RequiredDate` datetime default NULL,
  `Comment` text,
  `CustomerOrderRef` varchar(50) default NULL,
  `TaxFree` smallint(6) default NULL,
  PRIMARY KEY  (`BasketHeadID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `basketline`
--

CREATE TABLE `basketline` (
  `BasketLineID` int(11) NOT NULL auto_increment,
  `BasketLineNum` int(11) default NULL,
  `BasketHeadID` int(11) default NULL,
  `ProductID` int(11) default NULL,
  `Quantity` int(11) default NULL,
  `PriceIn` float default NULL,
  `PriceOut` float default NULL,
  `MVA` float default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `TaxAmount` decimal(16,5) default NULL,
  PRIMARY KEY  (`BasketLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `borettslag`
--

CREATE TABLE `borettslag` (
  `BorettslagID` int(11) NOT NULL auto_increment,
  `CompanyID` int(11) default NULL,
  `TS` timestamp NULL default NULL,
  `Kvadrat` decimal(12,2) default NULL,
  `Andelsbrev` decimal(12,2) default NULL,
  `BorettInnskudd` decimal(12,2) default NULL,
  `Gardsnr` varchar(12) default NULL,
  `Bruksnr` varchar(12) default NULL,
  `Seksjonsnr` varchar(12) default NULL,
  `KundefordringerKonto` int(11) default NULL,
  `ProductID1` int(11) default NULL,
  `ProductID2` int(11) default NULL,
  `ProductID3` int(11) default NULL,
  `ProductID4` int(11) default NULL,
  `ProductID5` int(11) default NULL,
  `ProductID6` int(11) default NULL,
  `ProductID7` int(11) default NULL,
  `ProductID8` int(11) default NULL,
  `ProductID9` int(11) default NULL,
  `ProductID10` int(11) default NULL,
  PRIMARY KEY  (`BorettslagID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `borettslagarsoppgjor`
--

CREATE TABLE `borettslagarsoppgjor` (
  `BorettslagarsoppgjorID` int(11) NOT NULL auto_increment,
  `BorettslagID` int(11) default NULL,
  `TS` timestamp NULL default NULL,
  `Arstall` int(11) default NULL,
  `ProsentInntekt` decimal(12,2) default NULL,
  `Inntekter` decimal(12,2) default NULL,
  `Utgifter` decimal(12,2) default NULL,
  `Ligningsverdi` decimal(12,2) default NULL,
  `Formue` decimal(12,2) default NULL,
  `Gjeld` decimal(12,2) default NULL,
  `Kostpris` decimal(12,2) default NULL,
  PRIMARY KEY  (`BorettslagarsoppgjorID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `budget`
--

CREATE TABLE `budget` (
  `BudgetID` int(11) NOT NULL auto_increment,
  `Type` varchar(20) NOT NULL default '',
  `PeriodYear` varchar(8) default NULL,
  `SumIn` double default '0',
  `SumOut` double default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`BudgetID`),
  KEY `Type` (`Type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `budgetline`
--

CREATE TABLE `budgetline` (
  `BudgetLinesID` int(11) NOT NULL auto_increment,
  `BudgetID` int(11) NOT NULL default '0',
  `AccountPlanID` bigint(20) default NULL,
  `Period1In` double default '0',
  `Period1Out` double default '0',
  `Period2In` double default '0',
  `Period2Out` double default '0',
  `Period3In` double default '0',
  `Period3Out` double default '0',
  `Period4In` double default '0',
  `Period4Out` double default '0',
  `Period5In` double default '0',
  `Period5Out` double default '0',
  `Period6In` double default '0',
  `Period6Out` double default '0',
  `Period7In` double default '0',
  `Period7Out` double default '0',
  `Period8In` double default '0',
  `Period8Out` double default '0',
  `Period9In` double default '0',
  `Period9Out` double default '0',
  `Period10In` double default '0',
  `Period10Out` double default '0',
  `Period11In` double default '0',
  `Period11Out` double default '0',
  `Period12In` double default '0',
  `Period12Out` double default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `SumOut` double default '0',
  `SumIn` double default '0',
  PRIMARY KEY  (`BudgetLinesID`),
  KEY `BudgetID` (`BudgetID`),
  KEY `AccountPlanID` (`AccountPlanID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `calendarevents`
--

CREATE TABLE `calendarevents` (
  `CalendarEventID` int(11) NOT NULL auto_increment,
  `TimeStart` datetime NOT NULL default '0000-00-00 00:00:00',
  `TimeStop` datetime NOT NULL default '0000-00-00 00:00:00',
  `PersonID` int(11) NOT NULL default '0',
  `Description` varchar(255) NOT NULL default '',
  `TimeCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `Timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `TimeAlarm` datetime NOT NULL default '0000-00-00 00:00:00',
  `ReOccurence` tinyint(4) NOT NULL default '0',
  `EventType` varchar(30) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '1',
  `Subject` varchar(255) default NULL,
  PRIMARY KEY  (`CalendarEventID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `categorycategorystruc`
--

CREATE TABLE `categorycategorystruc` (
  `ProductCategoryParentID` int(11) NOT NULL default '0',
  `ProductCategoryChildID` int(11) NOT NULL default '0',
  `Active` set('on','off') default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProductCategoryParentID`,`ProductCategoryChildID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `changerequest`
--

CREATE TABLE `changerequest` (
  `ChangeRequestID` int(11) NOT NULL auto_increment,
  `ProjectHeadID` int(11) NOT NULL default '0',
  `OfferHeadID` int(11) NOT NULL default '0',
  `CustomerChangeDateCreated` varchar(10) NOT NULL default '',
  `CustomerChangePersonID` int(11) default NULL,
  `CustomerChangeDescription` varchar(50) NOT NULL default '',
  `CustomerChangeDocument` varchar(50) NOT NULL default '',
  `CustomerChangeStatus` varchar(50) NOT NULL default '',
  `FinalChangeDate` varchar(10) NOT NULL default '',
  `FinalChangePersonID` int(11) default NULL,
  `FinalChangeDescription` varchar(50) NOT NULL default '',
  `FinalChangeDocument` varchar(50) NOT NULL default '',
  `FinalChangeStatus` varchar(50) NOT NULL default '',
  `CustomerLogStatus` set('approved','postponed','rejected') default NULL,
  `CustomerLogProposal` set('clearedForImplementation','implementationPostponed','spesificationRejected') default NULL,
  `CustomerLogImplementation` set('implementationAccepted','implementationRejected') default NULL,
  `CustomerApprovedDate` varchar(10) NOT NULL default '',
  `CustomerApprovedByID` int(11) NOT NULL default '0',
  `EstimatedPreparation` int(11) NOT NULL default '0',
  `EstimatedAnalysis` int(11) NOT NULL default '0',
  `EstimatedDesign` int(11) NOT NULL default '0',
  `EstimatedCoding` int(11) NOT NULL default '0',
  `EstimatedIntegration` int(11) NOT NULL default '0',
  `EstimatedSystemTest` int(11) NOT NULL default '0',
  `EstimatedDocumentation` int(11) NOT NULL default '0',
  `EstimatedProject` int(11) NOT NULL default '0',
  `EstimatedMeetings` int(11) NOT NULL default '0',
  `EstimatedTaskSize` set('small','medium','large') default NULL,
  `ResultedChangeability` set('better','unchanged','worse') default NULL,
  `ChangeClassification` set('correctRequirementFault','correctDesignFault','correctCodingFault','implExistingUserRequirements','implNewUserRequirements','implChangedUserRequirements','improvedPerformance','preventiveRestructuring','AdaptionForReuse','AdoptionExternalLibrary','AdoptionChangedDeveloperTools') default NULL,
  `LinesOfCode` int(11) NOT NULL default '0',
  `UnexpectedProblems` text NOT NULL,
  `PreviousChangeID` int(11) NOT NULL default '0',
  `ResponsibleDeveloperID` int(11) NOT NULL default '0',
  `DelegatedDeveloperID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ChangeRequestID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `CityID` int(11) NOT NULL auto_increment,
  `MunicipalID` varchar(10) NOT NULL default '',
  `Name` varchar(255) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CityID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `CompanyID` int(11) NOT NULL auto_increment,
  `VName` varchar(100) default NULL,
  `VAddress` varchar(60) default NULL,
  `VCity` varchar(255) default NULL,
  `VZipCode` varchar(10) default NULL,
  `VCountry` varchar(15) default NULL,
  `Phone` varchar(24) default NULL,
  `Fax` varchar(24) default NULL,
  `Status` varchar(50) default NULL,
  `Information` mediumtext,
  `VAT` double(16,4) NOT NULL default '1.2400',
  `Email` varchar(50) default NULL,
  `WWW` varchar(50) default NULL,
  `CustomerResponsibleID` int(11) NOT NULL default '0',
  `SalePersonID` int(11) default NULL,
  `CompanyNumber` varchar(25) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `CreatedByID` int(11) NOT NULL default '0',
  `CreatedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `DAddress` varchar(60) default NULL,
  `DZipCode` varchar(20) default NULL,
  `DCity` varchar(255) default NULL,
  `DCountry` varchar(15) default NULL,
  `IAddress` varchar(60) default NULL,
  `IZipCode` varchar(10) default NULL,
  `ICity` varchar(255) default NULL,
  `ICountry` varchar(15) default NULL,
  `DeliveryCondition` varchar(255) NOT NULL default '',
  `PaymentCondition` varchar(255) NOT NULL default '0',
  `VATDuty` tinyint(4) NOT NULL default '0',
  `BankAccount` varchar(15) NOT NULL default '',
  `PostAccount` varchar(12) NOT NULL default '',
  `DName` varchar(100) default NULL,
  `IName` varchar(100) default NULL,
  `HourPrice` double default NULL,
  `TravelPrice` double default NULL,
  `ClassificationID` int(11) default NULL,
  `FoundedDate` date default NULL,
  `InterestRate` double default NULL,
  `InterestDate` datetime default NULL,
  `VoucherBankNumber` int(11) default '2001',
  `VoucherSaleNumber` int(11) default '10001',
  `VoucherBuyNumber` int(11) default '7001',
  `VoucherSalaryNumber` int(11) default '11',
  `VoucherCashNumber` int(11) default '101',
  `ShareValue` int(11) default NULL,
  `ShareNumber` int(11) default NULL,
  `VatInFull` double default NULL,
  `VatInHalf` double default NULL,
  `VatInNull` double default NULL,
  `VatOutNull` double default NULL,
  `VatOutHalf` double default NULL,
  `VatOutFull` double default NULL,
  `VatOutAccount` varchar(255) default NULL,
  `VatInAccount` varchar(255) default NULL,
  `VatInvestmentAccount` varchar(255) default NULL,
  `VatAccount` varchar(255) default NULL,
  `AccountSale` varchar(255) default NULL,
  `AccountInvestment` varchar(255) default NULL,
  `TagLine` varchar(255) default NULL,
  `OpenMon` varchar(5) default NULL,
  `OpenTue` varchar(5) default NULL,
  `OpenWed` varchar(5) default NULL,
  `OpenThu` varchar(5) default NULL,
  `OpenFri` varchar(5) default NULL,
  `OpenSat` varchar(5) default NULL,
  `OpenSun` varchar(5) default NULL,
  `CloseMon` varchar(5) default NULL,
  `CloseTue` varchar(5) default NULL,
  `CloseWed` varchar(5) default NULL,
  `CloseThu` varchar(5) default NULL,
  `CloseFri` varchar(5) default NULL,
  `CloseSat` varchar(5) default NULL,
  `CloseSun` varchar(5) default NULL,
  `Map` varchar(255) default NULL,
  `EnableBankNumberSequence` smallint(6) default NULL,
  `EnableSaleNumberSequence` smallint(6) default NULL,
  `EnableBuyNumberSequence` smallint(6) default NULL,
  `EnableSalaryNumberSequence` smallint(6) default NULL,
  `EnableCashNumberSequence` smallint(6) default NULL,
  `VoucherResultAccount` int(11) default '8800',
  `VoucherBalanceAccount` int(11) default '2090',
  `AccountEmployeeFrom` bigint(20) default NULL,
  `AccountEmployeeTo` bigint(20) default NULL,
  `AccountVat` int(11) default '2740',
  `AccountHovedbokBalanseFrom` bigint(20) default NULL,
  `AccountHovedbokBalanseTo` bigint(20) default NULL,
  `AccountHovedbokResultatFrom` bigint(20) default NULL,
  `AccountHovedbokResultatTo` bigint(20) default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `OrgNumber` varchar(20) default NULL,
  `Mobile` varchar(24) default NULL,
  `EnableAutoNumberSequence` smallint(6) default NULL,
  `VoucherAutoNumber` int(11) default '100001',
  `EnableVat` smallint(6) default '1',
  `AccountHovedbokReskontroFrom` bigint(20) default NULL,
  `AccountHovedbokReskontroTo` bigint(20) default NULL,
  `CostPrice` double default NULL,
  `CollectionFee` float default '0',
  `IPoBox` varchar(255) default NULL,
  `IPoBoxCity` varchar(255) default NULL,
  `CompanyMunicipality` varchar(50) default NULL,
  `CompanyMunicipalityName` varchar(100) default NULL,
  `VPoBox` varchar(255) default NULL,
  `VPoBoxCity` varchar(255) default NULL,
  `IPoBoxZipCode` varchar(255) default NULL,
  `VPoBoxZipCode` varchar(255) default NULL,
  `DPoBoxZipCode` varchar(255) default NULL,
  `ShowInvoiceAmountThisYear` smallint(6) default NULL,
  `HourAccountPlanID` bigint(20) default NULL,
  `Password` varchar(60) NOT NULL default '',
  `PasswordCleartext` varchar(60) NOT NULL default '',
  `Category` varchar(50) default '',
  `AccountPlanID` bigint(20) default NULL,
  `VatPeriod` varchar(15) NOT NULL default '',
  `DPoBox` varchar(40) NOT NULL default '',
  `DPoBoxCity` varchar(40) NOT NULL default '',
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `CompanyName` varchar(255) default NULL,
  `ParentCompanyID` int(11) NOT NULL default '0',
  `SaleCompanyID` int(11) default NULL,
  `OperationCompanyID` int(11) default NULL,
  `LogoImageID` int(11) default NULL,
  `IPoBoxCode` varchar(255) default NULL,
  `ExternalID` varchar(30) NOT NULL default '',
  `InvoiceCommentCustomerPosition` varchar(10) NOT NULL default 'bottom',
  `VPoBoxZipCodeCity` varchar(255) default NULL,
  `SWIFT` varchar(255) default NULL,
  `VoucherWeeklysaleNumber` bigint(20) default '1000',
  `EnableWeeklysaleNumberSequence` smallint(6) default NULL,
  `CurrencyID` varchar(4) default 'NOK',
  `LanguageID` char(2) default 'no',
  PRIMARY KEY  (`CompanyID`),
  KEY `ExternalID` (`ExternalID`),
  KEY `ExternalID_2` (`ExternalID`),
  KEY `ExternalID_3` (`ExternalID`),
  KEY `ExternalID_4` (`ExternalID`),
  KEY `ExternalID_5` (`ExternalID`),
  KEY `ExternalID_6` (`ExternalID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `companycategory`
--

CREATE TABLE `companycategory` (
  `CompanyCategoryID` int(11) NOT NULL auto_increment,
  `Title` varchar(10) NOT NULL default '',
  `Description` varchar(50) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CompanyCategoryID`),
  KEY `Title` (`Title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `companycategorystruct`
--

CREATE TABLE `companycategorystruct` (
  `CompanyID` int(11) NOT NULL default '0',
  `CompanyCategoryID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CompanyID`,`CompanyCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `companycontacthistory`
--

CREATE TABLE `companycontacthistory` (
  `CompanyContactHistoryID` int(11) NOT NULL default '0',
  `ContactedByID` int(11) NOT NULL default '0',
  `ContactedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `TS` datetime NOT NULL default '0000-00-00 00:00:00',
  `BodyText` mediumtext NOT NULL,
  `Active` tinyint(4) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `companydepartment`
--

CREATE TABLE `companydepartment` (
  `CompanyDepartmentID` int(11) NOT NULL default '0',
  `DepartmentName` varchar(50) NOT NULL default '',
  `TS` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` tinyint(4) NOT NULL default '0',
  `ValidFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  `ValidTo` datetime NOT NULL default '0000-00-00 00:00:00',
  `EnableZeroYearEnd` smallint(6) default NULL,
  `Address` varchar(255) default NULL,
  `ZipCode` varchar(10) default NULL,
  `Description` text,
  `km0101` int(11) default NULL,
  `km3112` int(11) default NULL,
  `City` varchar(50) default NULL,
  `DepartmentCode` varchar(10) default NULL,
  `PurchasePrice` int(11) default '0',
  `PurchaseAddCost` int(11) default '0',
  `PurchaseYear` smallint(6) NOT NULL default '0',
  `CurrentPrice` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CompanyDepartmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `companyparameter`
--

CREATE TABLE `companyparameter` (
  `CompanyParameterID` int(11) NOT NULL auto_increment,
  `CompanyID` int(11) default NULL,
  `Type` varchar(255) default NULL,
  `Heading` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CompanyParameterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `companypersonstruct`
--

CREATE TABLE `companypersonstruct` (
  `CompanyID` int(11) NOT NULL default '0',
  `PersonID` int(11) NOT NULL default '0',
  `Active` tinyint(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CompanyID`,`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `companystruct`
--

CREATE TABLE `companystruct` (
  `CompanyStructID` int(11) NOT NULL auto_increment,
  `CompanyParentID` int(11) NOT NULL default '0',
  `CompanyChildID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CompanyStructID`),
  KEY `CompanyParentID` (`CompanyParentID`),
  KEY `CompanyChildID` (`CompanyChildID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `compcatcompcatstruct`
--

CREATE TABLE `compcatcompcatstruct` (
  `ParentCompanyCategoryID` int(11) NOT NULL default '0',
  `ChildCompanyCategoryID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ParentCompanyCategoryID`,`ChildCompanyCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `confdbfieldlanguage`
--

CREATE TABLE `confdbfieldlanguage` (
  `ConfDbFieldLanguageID` int(11) NOT NULL auto_increment,
  `ConfDbFieldID` int(11) NOT NULL default '0',
  `LanguageID` char(2) NOT NULL default '',
  `Alias` varchar(255) default NULL,
  `TableName` varchar(60) default NULL,
  `TableField` varchar(40) default NULL,
  `Description` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ConfDbFieldLanguageID`),
  KEY `ConfDbFieldID` (`ConfDbFieldID`),
  KEY `Language` (`LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `confdbfields`
--

CREATE TABLE `confdbfields` (
  `ConfDBFieldID` int(11) NOT NULL auto_increment,
  `TableField` varchar(40) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `TableName` varchar(60) NOT NULL default '',
  `PrimaryKey` smallint(6) default NULL,
  `FormHeight` int(11) default NULL,
  `FormWidth` int(11) default NULL,
  `DefaultValue` varchar(20) NOT NULL default '0',
  `FormType` varchar(30) default NULL,
  `FieldType` varchar(20) NOT NULL default '',
  `FieldNull` varchar(5) NOT NULL default '',
  `FieldExtra` varchar(35) NOT NULL default '',
  `DefaultLink` varchar(255) default NULL,
  `InputValidation` varchar(30) default NULL,
  `OutputValidation` varchar(30) default NULL,
  `Required` smallint(6) default NULL,
  `FormTypeEdit` varchar(30) NOT NULL default '',
  `FieldExtraEdit` varchar(30) NOT NULL default '',
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ConfDBFieldID`),
  UNIQUE KEY `FieldName` (`TableField`,`TableName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `conflayout`
--

CREATE TABLE `conflayout` (
  `ConfLayoutID` int(11) NOT NULL auto_increment,
  `LayoutName` varchar(255) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  `ValidTo` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` set('0','1') NOT NULL default '',
  `SQL_where` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ConfLayoutID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `conflayoutfield`
--

CREATE TABLE `conflayoutfield` (
  `ConfLayoutFieldID` int(11) NOT NULL auto_increment,
  `ConfLayoutID` int(11) NOT NULL default '0',
  `ConfDBFieldID` tinyint(4) NOT NULL default '0',
  `FieldInputType` set('hidden','submit','checkbox','radiobutton','text','select') NOT NULL default '',
  `DefaultValue` varchar(30) NOT NULL default '',
  `FieldPlacementX` tinyint(4) NOT NULL default '0',
  `FieldPlacementY` tinyint(4) NOT NULL default '0',
  `FieldColspan` tinyint(4) NOT NULL default '0',
  `FieldRowspan` tinyint(4) NOT NULL default '0',
  `Editable` tinyint(4) NOT NULL default '0',
  `FieldHeight` tinyint(4) NOT NULL default '0',
  `FieldWidth` tinyint(4) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  `ValidTo` datetime NOT NULL default '0000-00-00 00:00:00',
  `FieldNamePlacement` set('top','bottom','right','left') NOT NULL default 'right',
  PRIMARY KEY  (`ConfLayoutFieldID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `confmenues`
--

CREATE TABLE `confmenues` (
  `MenuID` int(11) NOT NULL auto_increment,
  `MenuName` varchar(255) NOT NULL default '',
  `MenuValue` varchar(255) default NULL,
  `MenuChoice` varchar(255) default NULL,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `LanguageID` char(2) NOT NULL default '',
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `sort` int(11) default NULL,
  PRIMARY KEY  (`MenuID`),
  KEY `Menu_name` (`MenuName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `confsql`
--

CREATE TABLE `confsql` (
  `SqlID` int(11) NOT NULL default '0',
  `Sql` varchar(255) NOT NULL default '',
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Name` varchar(255) NOT NULL default '',
  `Module` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `conftemplates`
--

CREATE TABLE `conftemplates` (
  `TemplateID` int(11) NOT NULL default '0',
  `TemplateContent` text NOT NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` tinyint(4) NOT NULL default '1',
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `CountryID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CountryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `county`
--

CREATE TABLE `county` (
  `CountyID` varchar(10) NOT NULL default '',
  `CountryID` varchar(10) NOT NULL default '',
  `Name` varchar(255) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`CountyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `departmenttype`
--

CREATE TABLE `departmenttype` (
  `code` varchar(20) NOT NULL default '',
  `name` varchar(255) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `drivedistance`
--

CREATE TABLE `drivedistance` (
  `DriveDistanceID` int(11) NOT NULL auto_increment,
  `DriveDate` datetime default NULL,
  `DriveDistance` int(11) NOT NULL default '0',
  `DriveType` varchar(50) NOT NULL default '',
  `DriveFrom` varchar(50) NOT NULL default '',
  `DriveTo` varchar(50) NOT NULL default '',
  `TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ProjectActivityID` int(11) NOT NULL default '0',
  `DrivePersonID` int(11) NOT NULL default '0',
  `CreatedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `ProjectID` int(11) NOT NULL default '0',
  `DriveComment` varchar(255) default NULL,
  `OutInvoiceID` int(11) default NULL,
  `ToBeInvoiced` smallint(6) default '1',
  `SalaryID` int(11) NOT NULL default '0',
  `InInvoiceID` int(11) default NULL,
  `StartDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `StopDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`DriveDistanceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `eierforhold`
--

CREATE TABLE `eierforhold` (
  `EierforholdID` int(11) NOT NULL auto_increment,
  `LeilighetID` int(11) NOT NULL default '0',
  `AccountPlanID` bigint(20) default NULL,
  `TS` timestamp NULL default NULL,
  `Andelsbrev` float default NULL,
  `BorettInnskudd` float default NULL,
  `Kvadrat` float default NULL,
  `FraDato` date default '0000-00-00',
  `TilDato` date NOT NULL default '0000-00-00',
  `Produkt1` decimal(12,2) default NULL,
  `Produkt2` decimal(12,2) default NULL,
  `Produkt3` decimal(12,2) default NULL,
  `Produkt4` decimal(12,2) default NULL,
  `Produkt5` decimal(12,2) default NULL,
  `Produkt6` decimal(12,2) default NULL,
  `Produkt7` decimal(12,2) default NULL,
  `Produkt8` decimal(12,2) default NULL,
  `Produkt9` decimal(12,2) default NULL,
  `Produkt10` decimal(12,2) default NULL,
  PRIMARY KEY  (`EierforholdID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `exchange`
--

CREATE TABLE `exchange` (
  `ExchangeID` int(11) NOT NULL auto_increment,
  `TypeOfTransfer` varchar(20) default NULL,
  `Priority` varchar(20) default NULL,
  `BankCompanyID` int(11) default NULL,
  `CustomerCompanyID` int(11) default NULL,
  `ReceiverCompanyID` int(11) default NULL,
  `Amount` double default NULL,
  `Currency` char(3) default NULL,
  `City` varchar(50) default NULL,
  `Date` datetime default NULL,
  `PersonID` int(11) default NULL,
  `AccountReceiver` varchar(15) default NULL,
  `AccountToCharge` varchar(15) default NULL,
  `PaymentDetails` text,
  `ExpencesPayed` varchar(50) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `PaymentDescription` text,
  `ExchangeRate` double default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ExchangeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `expence`
--

CREATE TABLE `expence` (
  `ExpenceID` int(11) NOT NULL auto_increment,
  `ValidFrom` date NOT NULL default '2001-01-01',
  `ValidTo` date NOT NULL default '9999-12-31',
  `OwnerName` varchar(255) NOT NULL default '',
  `ExpenceDescription` varchar(255) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `InInvoiceID` int(11) default NULL,
  PRIMARY KEY  (`ExpenceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `expenceline`
--

CREATE TABLE `expenceline` (
  `ExpenceLineID` int(11) NOT NULL auto_increment,
  `ExpenceID` int(14) NOT NULL default '0',
  `ExpenceLineDescription` varchar(255) NOT NULL default '',
  `ExpenceLineDate` date NOT NULL default '0000-00-00',
  `Amount` float(10,2) NOT NULL default '0.00',
  `ExpenceCategory` varchar(20) NOT NULL default '',
  `ExpenceActivityID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ExpenceProjectID` int(11) NOT NULL default '0',
  `ExpencePersonID` int(11) default NULL,
  `ToBeInvoiced` smallint(6) default '1',
  `Invoiced` smallint(6) default '0',
  `Active` smallint(6) default '1',
  `OutInvoiceID` int(11) default NULL,
  `InInvoiceID` int(11) default NULL,
  `SalaryID` int(11) NOT NULL default '0',
  `AccountPlanID` bigint(20) default NULL,
  PRIMARY KEY  (`ExpenceLineID`),
  KEY `AccountNumber` (`ExpenceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `feriepenger`
--

CREATE TABLE `feriepenger` (
  `feriepengerID` int(11) NOT NULL auto_increment,
  `ts_created` int(14) NOT NULL default '0',
  `ts_modified` int(14) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `year` int(11) default NULL,
  `AccountPlanID` bigint(20) default NULL,
  `Grunnlag` decimal(16,2) NOT NULL default '0.00',
  `Prosentsats` decimal(16,2) NOT NULL default '0.00',
  `Utbetalt` decimal(16,2) default '0.00',
  `ArbeidsgiveravgSats` decimal(16,2) default '0.00',
  PRIMARY KEY  (`feriepengerID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `filarkiv`
--

CREATE TABLE `filarkiv` (
  `filarkivID` int(11) NOT NULL auto_increment,
  `filkategoriID` int(11) NOT NULL default '0',
  `ts_created` int(14) NOT NULL default '0',
  `ts_modified` int(14) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `navn` varchar(250) NOT NULL default 'ingen navn',
  `fildata` longblob,
  `mimetype` varchar(250) NOT NULL default '',
  `original_name` varchar(250) NOT NULL default '',
  `size` int(10) NOT NULL default '0',
  `beskrivelse` text NOT NULL,
  `tilgjengeligFra` int(14) default NULL,
  `tilgjengeligTil` int(14) default NULL,
  `year` int(11) default NULL,
  PRIMARY KEY  (`filarkivID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `filkategori`
--

CREATE TABLE `filkategori` (
  `filkategoriID` int(11) NOT NULL auto_increment,
  `ts_created` int(11) NOT NULL default '0',
  `ts_modified` int(11) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `navn` varchar(250) NOT NULL default 'ingen navn',
  `beskrivelse` text NOT NULL,
  PRIMARY KEY  (`filkategoriID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `gpscoordinates`
--

CREATE TABLE `gpscoordinates` (
  `CoordinateID` int(11) NOT NULL auto_increment,
  `Place` varchar(255) NOT NULL default '',
  `DegreesNorth` varchar(8) NOT NULL default '',
  `DegreesSouth` varchar(9) NOT NULL default '',
  `TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Country` varchar(50) NOT NULL default '',
  `City` varchar(50) NOT NULL default '',
  `CreatedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `CreatedByID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`CoordinateID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `improvementeffortstruct`
--

CREATE TABLE `improvementeffortstruct` (
  `SlaImprovementID` int(11) NOT NULL default '0',
  `SlaEffortID` int(11) NOT NULL default '0',
  `Active` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SlaImprovementID`,`SlaEffortID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `inbarbeidsgiveravgift`
--

CREATE TABLE `inbarbeidsgiveravgift` (
  `inbarbeidsgiveravgiftID` int(11) NOT NULL auto_increment,
  `ts_created` int(14) NOT NULL default '0',
  `ts_modified` int(14) NOT NULL default '0',
  `modified_by` varchar(100) NOT NULL default '',
  `year` int(11) default NULL,
  `termin` int(11) NOT NULL default '0',
  `S1grunnbelop_u62` decimal(16,2) NOT NULL default '0.00',
  `S1grunnbelop_o62` decimal(16,2) NOT NULL default '0.00',
  `S2grunnbelop_u62` decimal(16,2) default '0.00',
  `S2grunnbelop_o62` decimal(16,2) default '0.00',
  `S3grunnbelop_u62` decimal(16,2) default '0.00',
  `S3grunnbelop_o62` decimal(16,2) default '0.00',
  `S4grunnbelop_u62` decimal(16,2) default '0.00',
  `S4grunnbelop_o62` decimal(16,2) default '0.00',
  `S5grunnbelop_u62` decimal(16,2) default '0.00',
  `S5grunnbelop_o62` decimal(16,2) default '0.00',
  `forskuddstrekk_u62` decimal(16,2) default '0.00',
  `forskuddstrekk_o62` decimal(16,2) default '0.00',
  `Description` text,
  PRIMARY KEY  (`inbarbeidsgiveravgiftID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `installation`
--

CREATE TABLE `installation` (
  `InstallationID` int(11) NOT NULL auto_increment,
  `InstallName` varchar(255) default NULL,
  `DealerName` varchar(255) default NULL,
  `DealerEmail` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `VName` varchar(100) default NULL,
  `VAddress` varchar(60) default NULL,
  `VCity` varchar(15) default NULL,
  `VZipCode` varchar(10) default NULL,
  `VCountry` varchar(15) default NULL,
  `Phone` varchar(24) default NULL,
  `Fax` varchar(24) default NULL,
  `WWW` varchar(50) default NULL,
  `CompanyNumber` varchar(25) NOT NULL default '',
  `CreatedDateTime` datetime default NULL,
  `InstalledDateTime` datetime default NULL,
  `LastName` varchar(50) default NULL,
  `MiddleName` varchar(50) default NULL,
  `FirstName` varchar(50) default NULL,
  `Email` varchar(60) default NULL,
  `Password` varchar(60) default NULL,
  `MobilePhoneNumber` varchar(30) default NULL,
  `Active` smallint(6) default NULL,
  `EnableReference` smallint(6) default NULL,
  `AcceptedLicence` smallint(6) default NULL,
  `Version` varchar(255) default NULL,
  PRIMARY KEY  (`InstallationID`),
  UNIQUE KEY `InstallName` (`InstallName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `invoiceconfig`
--

CREATE TABLE `invoiceconfig` (
  `InvoiceConfigID` tinyint(4) NOT NULL auto_increment,
  `InvoiceHead` varchar(200) NOT NULL default '',
  `Name` varchar(200) NOT NULL default '',
  `Address` varchar(200) NOT NULL default '',
  `ZipCode` varchar(200) NOT NULL default '',
  `City` varchar(200) NOT NULL default '',
  `URL` varchar(200) NOT NULL default '',
  `Email` varchar(200) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `HourPrice` double NOT NULL default '0',
  `TravelPrice` double NOT NULL default '0',
  `BodyText` text NOT NULL,
  `Country` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`InvoiceConfigID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `invoiceout`
--

CREATE TABLE `invoiceout` (
  `InvoiceID` int(11) NOT NULL auto_increment,
  `CompanyID` int(11) NOT NULL default '0',
  `ContactPersonID` int(11) NOT NULL default '0',
  `TotalCustPrice` decimal(16,2) NOT NULL default '0.00',
  `DName` varchar(255) NOT NULL default '',
  `DAddress` varchar(255) NOT NULL default '',
  `DZipCode` varchar(10) NOT NULL default '',
  `DCity` varchar(255) NOT NULL default '',
  `DCountry` varchar(255) NOT NULL default '',
  `Status` varchar(10) NOT NULL default '0',
  `InvoiceDate` date NOT NULL default '0000-00-00',
  `DueDate` date NOT NULL default '0000-00-00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ProjectID` int(11) NOT NULL default '0',
  `ProjectNameInternal` varchar(255) NOT NULL default '',
  `PaymentDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` tinyint(4) NOT NULL default '0',
  `CreatedByID` int(11) NOT NULL default '0',
  `ProjectStartDate` date NOT NULL default '0000-00-00',
  `ProjectStopDate` date NOT NULL default '0000-00-00',
  `CommentCustomer` text NOT NULL,
  `CommentInternal` text NOT NULL,
  `DeliveryCondition` varchar(255) NOT NULL default '',
  `PaymentCondition` varchar(255) NOT NULL default '',
  `IAddress` varchar(255) NOT NULL default '',
  `IZipCode` varchar(10) NOT NULL default '',
  `ICity` varchar(255) NOT NULL default '',
  `ICountry` varchar(255) NOT NULL default '',
  `DeliveryDate` date NOT NULL default '0000-00-00',
  `OrderDate` date NOT NULL default '0000-00-00',
  `VATDuty` tinyint(4) NOT NULL default '0',
  `TotalVat` decimal(16,2) NOT NULL default '0.00',
  `IName` varchar(255) NOT NULL default '',
  `InvoiceType` set('order','project') default NULL,
  `FromCompanyID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `AccountPlanID` bigint(20) default NULL,
  `DEmail` varchar(50) NOT NULL default '',
  `IEmail` varchar(50) NOT NULL default '',
  `RefCustomer` varchar(255) NOT NULL default '',
  `YourRef` varchar(255) default NULL,
  `BankAccount` varchar(15) NOT NULL default '',
  `Phone` varchar(20) NOT NULL default '',
  `ProjectNameCustomer` varchar(255) NOT NULL default '',
  `IPoBox` varchar(255) NOT NULL default '',
  `IPoBoxCity` varchar(255) NOT NULL default '',
  `IPoBoxZipCode` varchar(255) NOT NULL default '',
  `IPoBoxZipCodeCity` varchar(255) NOT NULL default '',
  `DPoBoxZipCode` varchar(255) NOT NULL default '',
  `DPoBoxZipCodeCity` varchar(255) NOT NULL default '',
  `InvoiceFileID` int(11) NOT NULL default '0',
  `Discount` decimal(16,2) default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `ResponsiblePersonID` int(11) default '0',
  `RequiredDate` datetime default NULL,
  `Freight` decimal(16,2) default '0.00',
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TaxFree` tinyint(4) default '0',
  `DateShipped` date default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `TotalCostPrice` decimal(16,2) NOT NULL default '0.00',
  `IAddressID` int(11) default NULL,
  `DAddressID` int(11) default NULL,
  `AddressID` int(11) NOT NULL default '0',
  `DPoBoxCity` varchar(255) default NULL,
  `InvoiceSalePersonID` int(11) default NULL,
  `DPoBox` varchar(255) default NULL,
  `RefInternal` varchar(255) default NULL,
  `SalePersonID` int(11) NOT NULL default '0',
  `EnableTaxFree` smallint(6) NOT NULL default '0',
  `VAddressNumber` varchar(10) NOT NULL default '',
  `IAddressNumber` varchar(10) NOT NULL default '',
  `DAddressNumber` varchar(10) NOT NULL default '',
  `MailSendtDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `CustomerViewedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `CustomerViewedPersonID` int(11) NOT NULL default '0',
  `MailExpireDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `VPoBoxZipCodeCity` varchar(255) default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `InsertedDateTime` datetime default NULL,
  `DepartmentID` int(11) default NULL,
  `DepartmentCustomer` varchar(255) default NULL,
  `Period` varchar(7) default NULL,
  `KID` varchar(30) default NULL,
  `CurrencyID` varchar(4) default 'NOK',
  PRIMARY KEY  (`InvoiceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `invoiceoutline`
--

CREATE TABLE `invoiceoutline` (
  `LineID` int(11) NOT NULL auto_increment,
  `InvoiceID` int(11) NOT NULL default '0',
  `ProductNumber` varchar(255) NOT NULL default '',
  `ProductID` int(11) NOT NULL default '0',
  `UnitCostPrice` decimal(16,2) NOT NULL default '0.00',
  `UnitCustPrice` decimal(16,2) NOT NULL default '0.00',
  `QuantityOrdered` decimal(16,2) NOT NULL default '0.00',
  `QuantityDelivered` decimal(16,2) NOT NULL default '0.00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Vat` decimal(16,2) NOT NULL default '0.00',
  `Active` smallint(6) NOT NULL default '1',
  `ProductName` varchar(255) NOT NULL default '',
  `Comment` varchar(255) NOT NULL default '',
  `VatID` int(11) NOT NULL default '0',
  `TaxFree` smallint(6) NOT NULL default '1',
  `ProductVariantID` int(11) NOT NULL default '0',
  `ValidFromDate` datetime default NULL,
  `ValidToDate` datetime default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime default NULL,
  `LineNum` int(11) NOT NULL default '0',
  `TaxAmount` decimal(16,5) default NULL,
  `Discount` decimal(16,5) default NULL,
  `UnitCustPriceCurrencyID` varchar(4) default 'NOK',
  `UnitCostPriceCurrencyID` varchar(4) default 'NOK',
  PRIMARY KEY  (`LineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `invoiceoutpurring`
--

CREATE TABLE `invoiceoutpurring` (
  `InvoiceOutPurringID` int(11) NOT NULL auto_increment,
  `InvoiceID` int(11) NOT NULL default '0',
  `Number` int(11) NOT NULL default '0',
  `Date` datetime default '0000-00-00 00:00:00',
  `Gebyr` decimal(16,2) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`InvoiceOutPurringID`),
  KEY `InvoiceID` (`InvoiceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `kommune`
--

CREATE TABLE `kommune` (
  `KommuneID` int(11) NOT NULL auto_increment,
  `KommuneNumber` varchar(4) NOT NULL default '',
  `KommuneName` varchar(50) NOT NULL default '',
  `Sone` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`KommuneID`),
  KEY `KommuneNumber` (`KommuneNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `LanguageID` char(2) NOT NULL default '',
  `LanguageName` varchar(255) NOT NULL default '',
  `Region` varchar(255) NOT NULL default '',
  `Country` varchar(255) NOT NULL default '',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`LanguageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `languagestring`
--

CREATE TABLE `languagestring` (
  `LanguageStringID` int(11) NOT NULL auto_increment,
  `LanguageName` varchar(40) default NULL,
  `Interface` varchar(40) default NULL,
  `Module` varchar(40) default NULL,
  `EN` varchar(255) default NULL,
  `NO` varchar(255) default NULL,
  `NL` varchar(255) default NULL,
  `DE` varchar(255) default NULL,
  `FR` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ImportedFromID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`LanguageStringID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `leilighet`
--

CREATE TABLE `leilighet` (
  `LeilighetID` int(11) NOT NULL auto_increment,
  `BorettslagID` int(11) default NULL,
  `Seksjonsnr` varchar(12) default '0',
  `TS` timestamp NULL default NULL,
  `Kvadrat` decimal(12,2) default NULL,
  `AndelTotal` int(11) default NULL,
  `BorettInnskudd` decimal(12,2) default NULL,
  `Produkt1` decimal(12,2) default NULL,
  `Produkt2` decimal(12,2) default NULL,
  `Produkt3` decimal(12,2) default NULL,
  `Produkt4` decimal(12,2) default NULL,
  `Produkt5` decimal(12,2) default NULL,
  `Produkt6` decimal(12,2) default NULL,
  `Produkt7` decimal(12,2) default NULL,
  `Produkt8` decimal(12,2) default NULL,
  `Produkt9` decimal(12,2) default NULL,
  `Produkt10` decimal(12,2) default NULL,
  PRIMARY KEY  (`LeilighetID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `licences`
--

CREATE TABLE `licences` (
  `LicenceID` int(11) NOT NULL auto_increment,
  `CreatedByID` int(11) NOT NULL default '0',
  `CreatedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `LicenceNumber` varchar(255) NOT NULL default '',
  `Version` varchar(5) NOT NULL default '',
  `Comments` text NOT NULL,
  `Product` varchar(255) NOT NULL default '',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`LicenceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `linetextmap`
--

CREATE TABLE `linetextmap` (
  `LineTextMapID` int(11) NOT NULL auto_increment,
  `ReportID` int(11) default NULL,
  `Line` varchar(11) NOT NULL default '',
  `Text` varchar(50) NOT NULL default '',
  `LanguageID` char(2) NOT NULL default 'no',
  `InsertedByPersonID` int(11) default '0',
  `UpdatedByPersonID` int(11) default '0',
  `InsertedDateTime` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`LineTextMapID`),
  KEY `ReportID` (`ReportID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `LogID` int(11) NOT NULL auto_increment,
  `LoggedTime` datetime default NULL,
  `PersonID` int(11) default NULL,
  `TableChanged` varchar(50) default NULL,
  `FieldChanged` varchar(50) default NULL,
  `PrimaryKeyUsed` varchar(50) default NULL,
  `IPAddressUsed` varchar(50) default NULL,
  `ValueChanged` varchar(50) default NULL,
  `UserAgent` varchar(50) default NULL,
  PRIMARY KEY  (`LogID`),
  KEY `TableChanged` (`TableChanged`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logaccessdenied`
--

CREATE TABLE `logaccessdenied` (
  `LogAccessDeniedID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `Module` varchar(40) default NULL,
  `Template` varchar(40) default NULL,
  `Interface` varchar(40) default NULL,
  `Referer` varchar(255) default NULL,
  `UserAgent` varchar(255) default NULL,
  `IPAdress` varchar(16) default NULL,
  `SessionID` varchar(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Message` varchar(255) default NULL,
  PRIMARY KEY  (`LogAccessDeniedID`),
  KEY `Template` (`Template`,`Module`,`Interface`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logapplication`
--

CREATE TABLE `logapplication` (
  `LogApplicationID` int(11) NOT NULL auto_increment,
  `Type` char(2) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Description` text,
  `Trace` text,
  `PersonID` int(11) default NULL,
  PRIMARY KEY  (`LogApplicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `logclick`
--

CREATE TABLE `logclick` (
  `LogClickID` int(11) NOT NULL auto_increment,
  `Referer` varchar(255) NOT NULL default '1',
  `Url` varchar(255) NOT NULL default '1',
  `UrlAlias` varchar(50) NOT NULL default 'A',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`LogClickID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logpagenotfound`
--

CREATE TABLE `logpagenotfound` (
  `LogPageNotFoundID` int(11) NOT NULL auto_increment,
  `URL` varchar(255) default NULL,
  `PersonID` int(11) default NULL,
  `SessionID` varchar(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Referer` varchar(255) default NULL,
  PRIMARY KEY  (`LogPageNotFoundID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logsearch`
--

CREATE TABLE `logsearch` (
  `LogSearchID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `TableName` varchar(255) default NULL,
  `SearchWord` varchar(255) default NULL,
  `SessionID` varchar(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`LogSearchID`),
  KEY `TableName` (`TableName`),
  KEY `SearchWord` (`SearchWord`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logsso`
--

CREATE TABLE `logsso` (
  `LogSsoID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) NOT NULL default '0',
  `CompanyID` int(11) NOT NULL default '0',
  `SessionID` varchar(40) default NULL,
  `What` varchar(25) default NULL,
  `Access` smallint(6) default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`LogSsoID`),
  KEY `PersonID` (`PersonID`),
  KEY `CompanyID` (`CompanyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `logusage`
--

CREATE TABLE `logusage` (
  `LogUsageID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `Template` varchar(40) default NULL,
  `Module` varchar(40) default NULL,
  `Interface` varchar(40) default NULL,
  `Referer` varchar(255) default NULL,
  `UserAgent` varchar(255) default NULL,
  `IPAdress` varchar(16) default NULL,
  `SessionID` varchar(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `PkField` varchar(255) default NULL,
  `PkValue` varchar(255) default NULL,
  PRIMARY KEY  (`LogUsageID`),
  KEY `Template` (`Template`,`Module`,`Interface`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `MediaID` int(11) NOT NULL auto_increment,
  `Title` varchar(255) default NULL,
  `Description` text,
  `Type` varchar(255) default NULL,
  `Width` int(11) default '0',
  `Height` int(11) default '0',
  `LanguageID` char(2) NOT NULL default '',
  `CreatedBy` varchar(50) default NULL,
  `CreatedDate` datetime default NULL,
  `ChangedBy` varchar(50) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Publisher` varchar(255) default NULL,
  `Contributor` varchar(255) default NULL,
  `Identifier` varchar(255) default NULL,
  `Source` varchar(255) default NULL,
  `Relation` varchar(255) default NULL,
  `Coverage` varchar(255) default NULL,
  `Rights` varchar(255) default NULL,
  PRIMARY KEY  (`MediaID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediacategory`
--

CREATE TABLE `mediacategory` (
  `MediaCategoryID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ParentMediaCategoryID` int(11) default NULL,
  PRIMARY KEY  (`MediaCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediacategorystruct`
--

CREATE TABLE `mediacategorystruct` (
  `MediaCategoryStructID` int(11) NOT NULL auto_increment,
  `MediaID` int(11) NOT NULL default '0',
  `MediaCategoryID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`MediaCategoryStructID`),
  KEY `MediaID` (`MediaID`),
  KEY `MediaCategoryID` (`MediaCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediacatmediacatstruct`
--

CREATE TABLE `mediacatmediacatstruct` (
  `MediaCatMediaCatStructID` int(11) NOT NULL auto_increment,
  `ParentMediaCategoryID` int(11) NOT NULL default '0',
  `ChildMediaCategoryID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MediaCatMediaCatStructID`,`ParentMediaCategoryID`,`ChildMediaCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediakeymediakeystruct`
--

CREATE TABLE `mediakeymediakeystruct` (
  `MediaKeyMediaKeyStructID` int(11) NOT NULL auto_increment,
  `ParentMediaKeywordID` int(11) NOT NULL default '0',
  `ChildMediaKeywordID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MediaKeyMediaKeyStructID`,`ParentMediaKeywordID`,`ChildMediaKeywordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediakeyword`
--

CREATE TABLE `mediakeyword` (
  `MediaKeywordID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MediaKeywordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediakeywordstruct`
--

CREATE TABLE `mediakeywordstruct` (
  `MediaKeywordStructID` int(11) NOT NULL auto_increment,
  `MediaID` int(11) NOT NULL default '0',
  `MediaKeywordID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`MediaKeywordStructID`),
  KEY `MediaID` (`MediaID`),
  KEY `MediaKeywordID` (`MediaKeywordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `mediastorage`
--

CREATE TABLE `mediastorage` (
  `MediaStorageID` int(11) NOT NULL auto_increment,
  `MediaID` int(11) NOT NULL default '0',
  `FileName` varchar(255) default NULL,
  `Size` int(11) default NULL,
  `Height` int(11) default NULL,
  `Width` int(11) default NULL,
  `Type` varchar(10) default NULL,
  `URL` varchar(255) default NULL,
  `Align` varchar(10) default NULL,
  `Active` smallint(6) default '1',
  `Original` smallint(6) default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `HeightDefined` int(11) NOT NULL default '0',
  `WidthDefined` int(11) NOT NULL default '0',
  `TypeDefined` varchar(10) NOT NULL default '',
  `DPI` int(11) default '72',
  PRIMARY KEY  (`MediaStorageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `MenuID` int(11) NOT NULL auto_increment,
  `MenuParentID` int(11) NOT NULL default '0',
  `EN` varchar(255) default NULL,
  `Interface` varchar(40) default NULL,
  `Module` varchar(40) default NULL,
  `Template` varchar(40) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Args` varchar(255) default NULL,
  `DE` varchar(255) NOT NULL default '',
  `NL` varchar(255) NOT NULL default '',
  `FR` varchar(255) NOT NULL default '',
  `NO` varchar(255) NOT NULL default '',
  `Description` varchar(255) NOT NULL default '',
  `LanguageID` char(2) NOT NULL default 'no',
  `Version` varchar(4) NOT NULL default '0.1',
  `State` set('development','alfa','beta','production') NOT NULL default 'development',
  `Target` varchar(20) NOT NULL default '',
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` smallint(6) default '1',
  `Accesskey` char(1) NOT NULL default '',
  `Sort` int(11) default '10',
  PRIMARY KEY  (`MenuID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `municipal`
--

CREATE TABLE `municipal` (
  `MunicipalID` varchar(10) NOT NULL default '',
  `CountyID` varchar(10) NOT NULL default '',
  `Name` varchar(255) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MunicipalID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `mvaavstemming`
--

CREATE TABLE `mvaavstemming` (
  `MvaAvstemmingID` int(11) NOT NULL auto_increment,
  `PeriodYear` varchar(8) NOT NULL default '',
  `LastYearDiff` decimal(16,3) default '0.000',
  `LastYearMva` decimal(16,3) default '0.000',
  `Period1Payed` decimal(16,3) default '0.000',
  `Period2Payed` decimal(16,3) default '0.000',
  `Period3Payed` decimal(16,3) default '0.000',
  `Period4Payed` decimal(16,3) default '0.000',
  `Period5Payed` decimal(16,3) default '0.000',
  `Period6Payed` decimal(16,3) default '0.000',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `LastYearPayed` decimal(16,3) default '0.000',
  `LastYearPayedJournalID` int(11) default NULL,
  `LastYearPayedDate` date default NULL,
  `Period1PayedJournalID` int(11) default NULL,
  `Period1PayedDate` date default NULL,
  `Period2PayedJournalID` int(11) default NULL,
  `Period2PayedDate` date default NULL,
  `Period3PayedJournalID` int(11) default NULL,
  `Period3PayedDate` date default NULL,
  `Period4PayedJournalID` int(11) default NULL,
  `Period4PayedDate` date default NULL,
  `Period5PayedJournalID` int(11) default NULL,
  `Period5PayedDate` date default NULL,
  `Period6PayedJournalID` int(11) default NULL,
  `Period6PayedDate` date default NULL,
  `LastYearPayedDescription` varchar(255) default NULL,
  PRIMARY KEY  (`MvaAvstemmingID`),
  KEY `PeriodYear` (`PeriodYear`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `mvaavstemmingline`
--

CREATE TABLE `mvaavstemmingline` (
  `MvaAvstemmingLineID` int(11) NOT NULL auto_increment,
  `MvaAvstemmingID` int(11) NOT NULL default '0',
  `Period` char(8) NOT NULL default '',
  `TotalOmsettning` decimal(16,3) default '0.000',
  `FreeOmsettning` decimal(16,3) default '0.000',
  `NoVatOmsettning` decimal(16,3) default NULL,
  `Grunnlag24mva` decimal(16,3) default '0.000',
  `In24mva` decimal(16,3) default '0.000',
  `Out24mva` decimal(16,3) default '0.000',
  `Grunnlag12mva` decimal(16,3) default '0.000',
  `In12mva` decimal(16,3) default '0.000',
  `Out12mva` decimal(16,3) default '0.000',
  `Grunnlag6mva` decimal(16,3) default '0.000',
  `In6mva` decimal(16,3) default '0.000',
  `Out6mva` decimal(16,3) default '0.000',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MvaAvstemmingLineID`),
  KEY `Period` (`Period`),
  KEY `MvaAvstemmingID` (`MvaAvstemmingID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `mvaavstemminglinefield`
--

CREATE TABLE `mvaavstemminglinefield` (
  `MvaAvstemmingLineFieldID` int(11) NOT NULL auto_increment,
  `MvaAvstemmingLineID` int(11) NOT NULL default '0',
  `Name` varchar(255) default NULL,
  `Value` decimal(16,3) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`MvaAvstemmingLineFieldID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `object`
--

CREATE TABLE `object` (
  `ObjectID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` text,
  `Price` double default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Active` smallint(6) default '1',
  `Category` varchar(255) default NULL,
  `FlightTime` double default NULL,
  `TankVolume` double default NULL,
  PRIMARY KEY  (`ObjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectbattery`
--

CREATE TABLE `objectbattery` (
  `ObjectBatteryID` int(11) NOT NULL auto_increment,
  `BatteryVolt` double default NULL,
  `BatteryCapacity` double default NULL,
  `BatteryUse` varchar(50) default NULL,
  `BatteryName` varchar(50) default NULL,
  `NumberOfCells` int(11) default NULL,
  `Active` smallint(6) default NULL,
  `ValidFrom` date default NULL,
  `ObjectID` int(11) default NULL,
  `BatteryType` varchar(255) default NULL,
  PRIMARY KEY  (`ObjectBatteryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectbatteryline`
--

CREATE TABLE `objectbatteryline` (
  `ObjectBatteryLineID` int(11) NOT NULL auto_increment,
  `ObjectBatteryID` int(11) default NULL,
  `ChargedDate` date default NULL,
  `MinutesUsed` double default NULL,
  `NumberOfTrips` double default NULL,
  `DischargeCapasity` varchar(50) default NULL,
  `ChargedCapacity` varchar(50) default NULL,
  `ChargedAverageVolt` varchar(50) default NULL,
  PRIMARY KEY  (`ObjectBatteryLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectmaintenance`
--

CREATE TABLE `objectmaintenance` (
  `ObjectMaintenanceID` int(11) NOT NULL auto_increment,
  `DriveDistance` int(11) NOT NULL default '0',
  `ObjectID` varchar(20) NOT NULL default '',
  `WhatWasDone` text,
  `DateDone` datetime NOT NULL default '0000-00-00 00:00:00',
  `DoneByID` int(11) NOT NULL default '0',
  `MaintenanceCost` double NOT NULL default '0',
  `CreatedDate` date default NULL,
  `DrivePersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ObjectMaintenanceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectsub`
--

CREATE TABLE `objectsub` (
  `ObjectSubID` int(11) NOT NULL auto_increment,
  `ObjectID` int(11) default NULL,
  `Name` varchar(255) default NULL,
  `Description` text,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ObjectSubID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectsubstruct`
--

CREATE TABLE `objectsubstruct` (
  `ObjectSubStructID` int(11) NOT NULL auto_increment,
  `ObjectID` int(11) default NULL,
  `ObjectSubID` int(11) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default '9999-12-31 00:00:00',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ObjectSubStructID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `objectusage`
--

CREATE TABLE `objectusage` (
  `ObjectUsageID` int(11) NOT NULL auto_increment,
  `Time` datetime default NULL,
  `Description` text,
  `Price` double default NULL,
  `FlightNumber` varchar(20) default NULL,
  `NumberOfFlights` smallint(6) default NULL,
  `Problems` tinyint(4) default NULL,
  `Active` tinyint(4) default '1',
  `Category` varchar(40) default NULL,
  `ObjectID` int(11) default NULL,
  `BatteryUsage` double default NULL,
  `FuelUsage` double default NULL,
  `DriveDistance` double default NULL,
  PRIMARY KEY  (`ObjectUsageID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `offerordercuststruct`
--

CREATE TABLE `offerordercuststruct` (
  `OfferID` int(11) NOT NULL default '0',
  `OrderID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OfferID`,`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `offerordersubscriptionstruct`
--

CREATE TABLE `offerordersubscriptionstruct` (
  `OfferID` int(11) NOT NULL default '0',
  `OrderID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OfferID`,`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `offerprojectstruct`
--

CREATE TABLE `offerprojectstruct` (
  `OfferID` int(11) NOT NULL default '0',
  `ProjectID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`OfferID`,`ProjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ordercustinvoiceoutstruct`
--

CREATE TABLE `ordercustinvoiceoutstruct` (
  `OrderID` int(11) NOT NULL default '0',
  `InvoiceID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`InvoiceID`,`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `orderexchangestruc`
--

CREATE TABLE `orderexchangestruc` (
  `OrderExchangeStrucID` int(11) NOT NULL auto_increment,
  `ExchangeID` int(11) default NULL,
  `OrderID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default NULL,
  PRIMARY KEY  (`OrderExchangeStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `orderpurchase`
--

CREATE TABLE `orderpurchase` (
  `OrderID` int(11) NOT NULL auto_increment,
  `Status` varchar(10) default '0',
  `CompanyID` varchar(5) NOT NULL default '',
  `ContactPersonID` int(11) NOT NULL default '0',
  `ResponsiblePersonID` int(11) NOT NULL default '0',
  `OrderDate` datetime default NULL,
  `RequiredDate` datetime default NULL,
  `DeliveryDate` datetime default NULL,
  `Freight` double(16,4) NOT NULL default '0.0000',
  `DName` varchar(255) default NULL,
  `DAddress` varchar(255) default NULL,
  `DCity` varchar(255) default NULL,
  `DZipCode` varchar(255) default NULL,
  `DCountry` varchar(255) default NULL,
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `TotalCustPrice` decimal(16,2) NOT NULL default '0.00',
  `TaxFree` tinyint(4) NOT NULL default '0',
  `RefCustomer` varchar(255) default '',
  `DateShipped` date NOT NULL default '0000-00-00',
  `Discount` decimal(16,2) default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `ProjectNameInternal` varchar(255) default NULL,
  `ProjectNameCustomer` varchar(255) default NULL,
  `CommentInternal` text,
  `CommentCustomer` text,
  `RefInternal` varchar(255) default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime default NULL,
  `TotalCostPrice` decimal(16,2) NOT NULL default '0.00',
  `TotalVat` decimal(16,2) NOT NULL default '0.00',
  `IPoBox` varchar(40) NOT NULL default '',
  `DPoBox` varchar(40) NOT NULL default '',
  `DPoBoxCity` varchar(40) NOT NULL default '',
  `IPoBoxCity` varchar(40) NOT NULL default '',
  `IAddressID` int(11) default NULL,
  `DAddressID` int(11) default NULL,
  `IPoBoxZipCode` varchar(255) default NULL,
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `DPoBoxZipCode` varchar(255) default NULL,
  `DPoBoxZipCodeCity` varchar(255) default NULL,
  `IName` varchar(255) default NULL,
  `IAddress` varchar(255) default NULL,
  `ICity` varchar(255) default NULL,
  `IZipCode` varchar(255) default NULL,
  `ICountry` varchar(255) default NULL,
  `SalePersonID` int(11) NOT NULL default '0',
  `IEmail` varchar(255) default NULL,
  `DEmail` varchar(255) default NULL,
  PRIMARY KEY  (`OrderID`),
  KEY `CustomerID` (`CompanyID`),
  KEY `EmployeeID` (`ContactPersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `orderpurchaseline`
--

CREATE TABLE `orderpurchaseline` (
  `ProductNumber` varchar(10) NOT NULL default '0',
  `OrderID` int(11) default NULL,
  `ProductID` int(11) default NULL,
  `UnitCostPrice` decimal(16,2) default '0.00',
  `UnitCustPrice` decimal(16,2) default '0.00',
  `QuantityOrdered` decimal(16,2) default '0.00',
  `QuantityDelivered` decimal(16,2) default '0.00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Vat` decimal(16,2) default '0.00',
  `Active` smallint(6) default '1',
  `ProductVariantID` int(11) NOT NULL default '0',
  `LineID` int(11) NOT NULL auto_increment,
  `LineNum` int(11) NOT NULL default '0',
  `ProductName` varchar(255) NOT NULL default '',
  `VatID` int(11) default NULL,
  `TaxFree` smallint(6) default NULL,
  `Comment` varchar(255) default NULL,
  `ValidFromDate` datetime default NULL,
  `ValidToDate` datetime default NULL,
  `TaxAmount` decimal(16,5) default NULL,
  `Discount` decimal(16,5) default NULL,
  PRIMARY KEY  (`LineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `ordersubscription`
--

CREATE TABLE `ordersubscription` (
  `OrderID` int(11) NOT NULL auto_increment,
  `Status` varchar(10) default '0',
  `CompanyID` varchar(5) NOT NULL default '',
  `ContactPersonID` int(11) NOT NULL default '0',
  `ResponsiblePersonID` int(11) NOT NULL default '0',
  `OrderDate` datetime default NULL,
  `RequiredDate` datetime default NULL,
  `DeliveryDate` datetime default NULL,
  `Freight` double(16,4) NOT NULL default '0.0000',
  `DName` varchar(255) default NULL,
  `DAddress` varchar(255) default NULL,
  `DCity` varchar(255) default NULL,
  `DZipCode` varchar(255) default NULL,
  `DCountry` varchar(255) default NULL,
  `Active` tinyint(4) NOT NULL default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime NOT NULL default '9999-12-31 00:00:00',
  `TotalCustPrice` decimal(16,2) NOT NULL default '0.00',
  `TaxFree` tinyint(4) NOT NULL default '0',
  `RefCustomer` varchar(255) default '',
  `DateShipped` date NOT NULL default '0000-00-00',
  `Discount` decimal(16,2) default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `ProjectNameInternal` varchar(255) default NULL,
  `ProjectNameCustomer` varchar(255) default NULL,
  `CommentInternal` text,
  `CommentCustomer` text,
  `RefInternal` varchar(255) default '',
  `DEmail` varchar(255) default '',
  `IEmail` varchar(255) default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime default NULL,
  `TotalCostPrice` decimal(16,2) NOT NULL default '0.00',
  `TotalVat` decimal(16,2) NOT NULL default '0.00',
  `IPoBox` varchar(40) NOT NULL default '',
  `DPoBox` varchar(40) NOT NULL default '',
  `DPoBoxCity` varchar(40) NOT NULL default '',
  `IPoBoxCity` varchar(40) NOT NULL default '',
  `IAddressID` int(11) default NULL,
  `DAddressID` int(11) default NULL,
  `IPoBoxZipCode` varchar(255) default NULL,
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `DPoBoxZipCode` varchar(255) default NULL,
  `DPoBoxZipCodeCity` varchar(255) default NULL,
  `IName` varchar(255) default NULL,
  `IAddress` varchar(255) default NULL,
  `ICity` varchar(255) default NULL,
  `IZipCode` varchar(255) default NULL,
  `ICountry` varchar(255) default NULL,
  `SalePersonID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`OrderID`),
  KEY `CustomerID` (`CompanyID`),
  KEY `EmployeeID` (`ContactPersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ordersubscriptioninvoiceoutstruct`
--

CREATE TABLE `ordersubscriptioninvoiceoutstruct` (
  `OrderID` int(11) NOT NULL default '0',
  `InvoiceID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFromDate` datetime default NULL,
  `ValidToDate` datetime default NULL,
  PRIMARY KEY  (`InvoiceID`,`OrderID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `ordersubscriptionline`
--

CREATE TABLE `ordersubscriptionline` (
  `ProductNumber` varchar(10) NOT NULL default '0',
  `OrderID` int(11) default NULL,
  `ProductID` int(11) default NULL,
  `UnitCostPrice` decimal(16,2) default '0.00',
  `UnitCustPrice` decimal(16,2) default '0.00',
  `QuantityOrdered` decimal(16,2) default '0.00',
  `QuantityDelivered` decimal(16,2) default '0.00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Vat` decimal(16,2) default '0.00',
  `Active` smallint(6) default '1',
  `ProductVariantID` int(11) NOT NULL default '0',
  `LineID` int(11) NOT NULL auto_increment,
  `LineNum` int(11) NOT NULL default '0',
  `ProductName` varchar(255) NOT NULL default '',
  `VatID` int(11) default NULL,
  `TaxFree` smallint(6) default NULL,
  `Comment` varchar(255) default NULL,
  `ValidFromDate` datetime default NULL,
  `ValidToDate` datetime default NULL,
  `TaxAmount` decimal(16,5) default NULL,
  `Discount` decimal(16,5) default NULL,
  PRIMARY KEY  (`LineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `pagediscussion`
--

CREATE TABLE `pagediscussion` (
  `PageDiscussionID` int(11) NOT NULL auto_increment,
  `ChapterParent` int(11) default NULL,
  `DiscussionText` text,
  `CreatedDate` date default NULL,
  `CreatedByIP` varchar(15) default NULL,
  `FirstName` varchar(255) default NULL,
  `LastName` varchar(255) default NULL,
  `EMail` varchar(255) default NULL,
  `URL` varchar(255) default NULL,
  `Summary` varchar(255) default NULL,
  `Keyword` varchar(255) default NULL,
  `Ingress` text,
  `Teaser` text,
  `Active` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`PageDiscussionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `passwords`
--

CREATE TABLE `passwords` (
  `PasswordID` int(11) NOT NULL auto_increment,
  `Password` varchar(50) NOT NULL default '',
  `Username` varchar(50) NOT NULL default '',
  `CreatedDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `TIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Server` varchar(255) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `Active` tinyint(4) NOT NULL default '0',
  `OrganizationName` varchar(50) default NULL,
  `MembershipNumber` varchar(50) default NULL,
  `Alias` varchar(50) default NULL,
  PRIMARY KEY  (`PasswordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `PersonID` int(11) NOT NULL auto_increment,
  `LastName` varchar(50) NOT NULL default '',
  `MiddleName` varchar(50) NOT NULL default '',
  `FirstName` varchar(50) NOT NULL default '',
  `Title` varchar(255) default NULL,
  `TitleOfCourtesy` varchar(25) default NULL,
  `BirthDate` datetime default NULL,
  `Address` varchar(60) default NULL,
  `City` varchar(15) default NULL,
  `ZipCode` varchar(10) default NULL,
  `Country` varchar(15) default NULL,
  `HomePhone` varchar(24) default NULL,
  `Extension` varchar(4) default NULL,
  `Notes` mediumtext,
  `ReportsToPersonID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `DirectPhone` varchar(24) default NULL,
  `MobilePhoneNumber` varchar(15) default NULL,
  `InternalPhoneNumber` varchar(4) default NULL,
  `CompanyDepartmentID` int(11) NOT NULL default '0',
  `WhereIs` tinyint(4) NOT NULL default '0',
  `ValidFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  `ValidTo` datetime NOT NULL default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `Email` varchar(60) NOT NULL default '',
  `Password` varchar(60) NOT NULL default '',
  `AccessLevel` varchar(60) NOT NULL default '',
  `Fax` varchar(60) default NULL,
  `CompanyID` int(11) NOT NULL default '0',
  `CreatedByID` int(11) NOT NULL default '0',
  `HireDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Css` varchar(50) default NULL,
  `ListLines` int(11) default NULL,
  `ExternalID` varchar(50) NOT NULL default '',
  `CompanyDepartment` varchar(255) NOT NULL default '',
  `ClassificationID` int(11) default NULL,
  `ShareNumber` int(11) default NULL,
  `ShareValue` int(11) default NULL,
  `SocialSecurityID` varchar(255) default NULL,
  `LanguageID` char(2) NOT NULL default '',
  `BankAccount` varchar(20) default NULL,
  `Debug` smallint(6) default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `CostPrice` double default NULL,
  `IsReskontro` int(11) default '0',
  `AccountPlanID` int(11) default NULL,
  `StockAssessmentValue` float default '0',
  `StockProfit` float default '0',
  `Tax` float default '0',
  `IPoBoxCity` varchar(255) default NULL,
  `IPoBox` varchar(255) default NULL,
  `DefaultInterface` varchar(40) default '',
  `DefaultModule` varchar(40) default '',
  `DefaultTemplate` varchar(40) default '',
  `Interface` varchar(40) default NULL,
  `Module` varchar(40) default NULL,
  `Template` varchar(40) default NULL,
  `PasswordCleartext` varchar(60) NOT NULL default '',
  `VPoBox` varchar(40) default '',
  `VPoBoxCity` varchar(40) default '',
  `VPoBoxZipCode` varchar(40) default '',
  `VPoBoxZipCodeCity` varchar(100) default '',
  `DAddress` varchar(60) default '',
  `DZipCode` varchar(10) default '',
  `DCity` varchar(100) default '',
  `DCountry` varchar(15) default '',
  `DPoBox` varchar(40) default '',
  `DPoBoxCity` varchar(40) default '',
  `DPoBoxZipCode` varchar(40) default '',
  `DPoBoxZipCodeCity` varchar(100) default '',
  `IAddress` varchar(60) default '',
  `IZipCode` varchar(10) default '',
  `ICity` varchar(100) default '',
  `ICountry` varchar(15) default '',
  `IPoBoxZipCode` varchar(40) default '',
  `IPoBoxZipCodeCity` varchar(100) default '',
  `VName` varchar(255) default '',
  `IName` varchar(255) default '',
  `DName` varchar(255) default '',
  `Function` varchar(255) NOT NULL default '',
  `InvoicePercent` int(11) NOT NULL default '60',
  `PhotoImageID` int(11) default NULL,
  `PictureImageID` int(11) default NULL,
  `SalesPersonID` int(11) default NULL,
  `FakturabankUsername` varchar(255) default NULL,
  `FakturabankPassword` varchar(255) default NULL,
  PRIMARY KEY  (`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `personaddressstruct`
--

CREATE TABLE `personaddressstruct` (
  `AdressID` int(11) NOT NULL default '0',
  `PersonID` int(11) NOT NULL default '0',
  `Active` tinyint(4) default '1',
  PRIMARY KEY  (`AdressID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personcommunication`
--

CREATE TABLE `personcommunication` (
  `PersonCommunicationID` int(11) NOT NULL auto_increment,
  `FromPersonID` int(11) default NULL,
  `ToPersonID` int(11) default NULL,
  `ToCompanyID` int(11) default NULL,
  `ProjectID` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `Date` datetime default NULL,
  `CommunicationType` varchar(255) default NULL,
  `CommunicationMedium` int(11) default NULL,
  `CommunicationStatusStart` int(11) default NULL,
  `CommunicationStatusStop` int(11) default NULL,
  `HoursUsed` float default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`PersonCommunicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personcommunicationstruct`
--

CREATE TABLE `personcommunicationstruct` (
  `PersonCommunicationID` int(11) NOT NULL auto_increment,
  `ToPersonID` int(11) default NULL,
  `Type` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`PersonCommunicationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personeducation`
--

CREATE TABLE `personeducation` (
  `PersonEducationID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `Date` date default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `EducationInstitution` varchar(255) default NULL,
  `CourceType` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`PersonEducationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personhistory`
--

CREATE TABLE `personhistory` (
  `PersonHistoryID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `URLName` varchar(255) default NULL,
  `Date` date default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`PersonHistoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personknowledge`
--

CREATE TABLE `personknowledge` (
  `PersonKnowledgeID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `URLName` varchar(255) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `Competence` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`PersonKnowledgeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personparameter`
--

CREATE TABLE `personparameter` (
  `PersonParameterID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `Type` varchar(255) default NULL,
  `Heading` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CompanyID` int(11) NOT NULL default '0',
  `Value2` varchar(255) default NULL,
  `Value` varchar(255) NOT NULL default '',
  `ParameterGroup` varchar(40) NOT NULL default '',
  `Active` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`PersonParameterID`),
  KEY `PersonID` (`PersonID`),
  KEY `PersonID_2` (`PersonID`),
  KEY `PersonID_3` (`PersonID`),
  KEY `PersonID_4` (`PersonID`),
  KEY `PersonID_5` (`PersonID`),
  KEY `PersonID_6` (`PersonID`),
  KEY `PersonID_7` (`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personprojectreferences`
--

CREATE TABLE `personprojectreferences` (
  `PersonProjectReferencesID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `ValidFrom` date default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `CompanyID` varchar(255) default NULL,
  `ValidTo` date default NULL,
  PRIMARY KEY  (`PersonProjectReferencesID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `personstruct`
--

CREATE TABLE `personstruct` (
  `ParentPersonID` int(11) NOT NULL default '0',
  `ChildPersonID` int(11) NOT NULL default '0',
  `Active` int(11) default NULL,
  `Type` varchar(20) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ParentPersonID`,`ChildPersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `personworkexperience`
--

CREATE TABLE `personworkexperience` (
  `PersonWorkExperienceID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `URLName` varchar(255) default NULL,
  `DateFrom` date default NULL,
  `DateTo` date default NULL,
  `Duration` varchar(255) default NULL,
  `EmployerCompanyID` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `letterofrecomandation` text,
  PRIMARY KEY  (`PersonWorkExperienceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `pricegroup`
--

CREATE TABLE `pricegroup` (
  `PriceGroupID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` text,
  `Activ` tinyint(4) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default NULL,
  PRIMARY KEY  (`PriceGroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `pricegroupcompanystruct`
--

CREATE TABLE `pricegroupcompanystruct` (
  `PriceGroupID` int(11) NOT NULL default '0',
  `CompanyID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`PriceGroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `pricegroupproductstruct`
--

CREATE TABLE `pricegroupproductstruct` (
  `PriceGroupID` int(11) NOT NULL default '0',
  `ProductID` int(11) NOT NULL default '0',
  `Type` set('campaign','percent','amount') default 'percent',
  `Value` decimal(16,2) default '0.00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `BasePrice` set('in','out') default 'out',
  `Description` varchar(255) default NULL,
  `EnableProductWithTax` smallint(6) default NULL,
  `EnableProductWithTaxFree` smallint(6) default NULL,
  `TaxAmount` decimal(16,5) default NULL,
  `Tax` decimal(16,5) default NULL,
  KEY `PriceGroupID` (`PriceGroupID`,`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `process`
--

CREATE TABLE `process` (
  `ProcessID` int(11) NOT NULL auto_increment,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `CreatedByPersonID` int(11) default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `ChangedTime` datetime default NULL,
  `ApprovedByPersonID` varchar(255) default NULL,
  `ApprovedTime` datetime default NULL,
  `ArchiveRef` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `Chapter` varchar(6) default NULL,
  `DocumentNumber` int(11) default NULL,
  `VersionNumber` int(11) default NULL,
  `NumberOfPages` int(11) default NULL,
  `ProcessStart` varchar(255) default NULL,
  `ProcessStop` varchar(255) default NULL,
  `ProcessContains` varchar(255) default NULL,
  `ProcessOwner` varchar(255) default NULL,
  `ProcessMisc` varchar(255) default NULL,
  `Type` int(11) default NULL,
  `Module` varchar(40) default NULL,
  `Template` varchar(40) default NULL,
  `Position` set('start','stop','middle') default NULL,
  `Picture` varchar(255) default NULL,
  `URL` varchar(255) default NULL,
  PRIMARY KEY  (`ProcessID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processattachment`
--

CREATE TABLE `processattachment` (
  `ProcessAttachmentID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `FileName` varchar(255) default NULL,
  `FileSize` int(11) default NULL,
  `FileType` char(3) default NULL,
  `Heading` varchar(255) default NULL,
  PRIMARY KEY  (`ProcessAttachmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processdefinition`
--

CREATE TABLE `processdefinition` (
  `ProcessDefinitionID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Heading` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ProcessDefinitionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processdescription`
--

CREATE TABLE `processdescription` (
  `ProcessDescriptionID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Actor` varchar(255) default NULL,
  `Level` varchar(255) default NULL,
  `Work` varchar(255) default NULL,
  PRIMARY KEY  (`ProcessDescriptionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processmeasurelis`
--

CREATE TABLE `processmeasurelis` (
  `ProcessMeasureLisID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Description` text,
  PRIMARY KEY  (`ProcessMeasureLisID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processprocedure`
--

CREATE TABLE `processprocedure` (
  `ProcessProcedureID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `CreatedByPersonID` int(11) default NULL,
  `CreatedTime` datetime default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `ChangedTime` datetime default NULL,
  `MadeByPersonID` varchar(255) default NULL,
  `VersionNumber` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProcessProcedureID`),
  KEY `ProcessID` (`ProcessID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processproceduredefinition`
--

CREATE TABLE `processproceduredefinition` (
  `ProcessProcedureDefinitionID` int(11) NOT NULL auto_increment,
  `ProcessProcedureID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Heading` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ProcessProcedureDefinitionID`),
  KEY `ProcessProcedureID` (`ProcessProcedureID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processprocedurelink`
--

CREATE TABLE `processprocedurelink` (
  `ProcessProcedureLinkID` int(11) NOT NULL auto_increment,
  `ProcessProcedureID` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `URL` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProcessProcedureLinkID`),
  KEY `ProcessProcedureID` (`ProcessProcedureID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processprocedurestep`
--

CREATE TABLE `processprocedurestep` (
  `ProcessProcedureStepID` int(11) NOT NULL auto_increment,
  `ProcessProcedureID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Actor` varchar(255) default NULL,
  `Step` varchar(255) default NULL,
  `Work` varchar(255) default NULL,
  PRIMARY KEY  (`ProcessProcedureStepID`),
  KEY `ProcessProcedureID` (`ProcessProcedureID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processpurpose`
--

CREATE TABLE `processpurpose` (
  `ProcessPurposeID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Heading` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ProcessPurposeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processqualitygoal`
--

CREATE TABLE `processqualitygoal` (
  `ProcessQualityGoalID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Description` text,
  PRIMARY KEY  (`ProcessQualityGoalID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processreference`
--

CREATE TABLE `processreference` (
  `ProcessReferenceID` int(11) NOT NULL auto_increment,
  `ProcessID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `URL` varchar(255) default NULL,
  `ToProcessID` int(11) default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ProcessReferenceID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `processstruct`
--

CREATE TABLE `processstruct` (
  `FromProcessID` int(11) default NULL,
  `ToProcessID` int(11) default NULL,
  `Type` enum('paralell','down','up') default NULL,
  `Position` set('L','R','U','D','C','P') default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL auto_increment,
  `ProductNumber` varchar(255) default NULL,
  `ProductName` varchar(255) default NULL,
  `SupplierID` int(11) default '0',
  `QuantityPerUnit` int(20) default NULL,
  `UnitCostPrice` decimal(16,2) default '0.00',
  `UnitCustPrice` decimal(16,2) default '0.00',
  `Active` tinyint(4) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime NOT NULL default '0000-00-00 00:00:00',
  `ValidTo` datetime NOT NULL default '0000-00-00 00:00:00',
  `ProductHeight` decimal(16,2) default '0.00',
  `ProductWidth` decimal(16,2) default '0.00',
  `ProductLength` decimal(16,2) default '0.00',
  `ProductWeight` decimal(16,2) default '0.00',
  `MinOrdQuantity` int(11) NOT NULL default '0',
  `TaxFreeCustPrice` decimal(16,2) default '0.00',
  `TaxFreeCostPrice` decimal(16,2) default '0.00',
  `ProductUnit` varchar(10) NOT NULL default '',
  `LayersPerPallet` decimal(16,2) default NULL,
  `UnitsPerLayer` int(11) default NULL,
  `VatID` int(11) default NULL,
  `AccountPlanID` bigint(20) default NULL,
  `CompanyDepartmentID` int(11) default NULL,
  `ProjectID` int(11) default NULL,
  `ClassificationID` int(11) NOT NULL default '0',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `MediumImageID` int(11) default NULL,
  `ProductVolumeUnit` varchar(4) NOT NULL default '',
  `ProductCurrency` varchar(4) NOT NULL default '',
  `PlantID` int(11) NOT NULL default '1',
  `ProductVolume` decimal(16,2) default '0.00',
  `MaxOrdQuantity` decimal(16,2) NOT NULL default '0.00',
  `MinReOrderLevel` decimal(16,2) NOT NULL default '0.00',
  `MaxReOrderLevel` decimal(16,2) NOT NULL default '0.00',
  `EAN` varchar(255) NOT NULL default '0',
  `UNSPSC` varchar(255) NOT NULL default '0',
  `BigImageID` int(11) default NULL,
  `SmallImageID` int(11) default NULL,
  `ExpectedStockDate` datetime default NULL,
  `LocationHylle` varchar(255) default NULL,
  `LocationReol` varchar(255) default NULL,
  `LocationNumber` varchar(255) default NULL,
  `ProductFileID` int(11) default NULL,
  `SupplierCompanyID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `productcalculation`
--

CREATE TABLE `productcalculation` (
  `ProductCalculationID` int(11) NOT NULL auto_increment,
  `ProductID` int(11) NOT NULL default '0',
  `Currency` varchar(4) NOT NULL default 'no',
  `Description` varchar(255) NOT NULL default '',
  `Amount` decimal(16,2) default '0.00',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProductCalculationID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productcategory`
--

CREATE TABLE `productcategory` (
  `ProductCategoryID` int(11) NOT NULL auto_increment,
  `ProductCategoryName` varchar(100) default NULL,
  `ProductCategoryType` int(11) default NULL,
  `Sort` int(11) default NULL,
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProductCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productcategorystruc`
--

CREATE TABLE `productcategorystruc` (
  `ProductID` int(11) NOT NULL default '0',
  `ProductCategoryID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProductID`,`ProductCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productproductnustruc`
--

CREATE TABLE `productproductnustruc` (
  `ProductProductnuStrucID` int(11) NOT NULL auto_increment,
  `ProductNumber` varchar(255) default NULL,
  `ProductID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `CompanyID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ProductProductnuStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productreleasenote`
--

CREATE TABLE `productreleasenote` (
  `ProductReleasenoteID` int(11) NOT NULL auto_increment,
  `ProductID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Heading` varchar(255) default NULL,
  `Description` text,
  `Version` varchar(4) default NULL,
  `Active` smallint(6) default NULL,
  PRIMARY KEY  (`ProductReleasenoteID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productstocklocation`
--

CREATE TABLE `productstocklocation` (
  `ProductStockLocationID` int(11) NOT NULL auto_increment,
  `ProductID` int(11) NOT NULL default '0',
  `Stock` decimal(16,2) default '0.00',
  `OrderID` int(11) NOT NULL default '0',
  `OrderType` char(1) NOT NULL default 'S',
  `PlantID` char(3) NOT NULL default '1',
  `AddressID` char(3) NOT NULL default '1',
  `ReolID` char(3) NOT NULL default 'A',
  `HeightID` char(3) NOT NULL default '1',
  `PositionID` char(3) NOT NULL default 'A',
  `Active` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `CreatedDateTime` datetime default NULL,
  PRIMARY KEY  (`ProductStockLocationID`),
  KEY `PlantID` (`PlantID`,`AddressID`,`ReolID`,`HeightID`,`PositionID`),
  KEY `PlantID_2` (`PlantID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `producttext`
--

CREATE TABLE `producttext` (
  `ProductTextID` int(11) NOT NULL auto_increment,
  `ProductID` int(11) NOT NULL default '0',
  `LanguageID` char(2) NOT NULL default '',
  `ProductName` varchar(255) default NULL,
  `ProductText` text NOT NULL,
  `Active` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `CreatedDateTime` datetime default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `ImportedFromID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ProductTextID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `productvariant`
--

CREATE TABLE `productvariant` (
  `ProductVariantID` int(11) NOT NULL auto_increment,
  `ProductID` int(11) NOT NULL default '0',
  `VariantName` varchar(255) NOT NULL default '',
  `CostPrice` decimal(16,2) default NULL,
  `CustPrice` decimal(16,2) default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ChangedByPersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProductVariantID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `ProjectID` int(11) NOT NULL default '0',
  `Heading` varchar(100) NOT NULL default '',
  `Type` varchar(50) NOT NULL default '',
  `Description` mediumtext NOT NULL,
  `ValidFrom` datetime NOT NULL default '2001-01-01 00:00:00',
  `ValidTo` datetime default '9999-12-31 00:00:00',
  `Status` varchar(50) NOT NULL default 'Registrert',
  `Priority` varchar(50) default NULL,
  `CompanyID` int(11) NOT NULL default '0',
  `Active` tinyint(4) default '1',
  `ResponsiblePersonID` int(11) NOT NULL default '0',
  `FinishedDate` date default NULL,
  `RegisteredByPersonID` int(11) NOT NULL default '0',
  `SalePersonID` int(11) NOT NULL default '0',
  `Address` varchar(255) default NULL,
  `ZipCode` varchar(10) default NULL,
  `EnableZeroYearEnd` smallint(6) default NULL,
  `RequestID` int(11) default NULL,
  `City` varchar(50) default NULL,
  `ContactPersonID` int(11) NOT NULL default '0',
  `CommentInternal` text NOT NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ProjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `projectactivity`
--

CREATE TABLE `projectactivity` (
  `ProjectID` int(11) NOT NULL default '0',
  `Description` varchar(50) default NULL,
  `Type` varchar(50) default NULL,
  `EstimatedTime` double(16,4) default NULL,
  `ValidFrom` datetime default '2001-01-01 00:00:00',
  `ValidTo` datetime default '9999-12-31 00:00:00',
  `Active` tinyint(4) default '1',
  `ProjectActivityID` int(11) NOT NULL auto_increment,
  `Priority` tinyint(4) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `PercentFinished` int(11) NOT NULL default '0',
  `FinishedDate` datetime default NULL,
  `Price` float(16,2) default NULL,
  `CostPrice` double default NULL,
  `ProgressCategory` set('request','reqspec','implementation','testing','finished') default NULL,
  `Running` smallint(6) default NULL,
  `EnableSalary` smallint(6) default '1',
  `ProductID` int(11) NOT NULL default '0',
  `EnableInvoice` smallint(6) NOT NULL default '1',
  `Status` int(11) NOT NULL default '0',
  `Heading` text,
  PRIMARY KEY  (`ProjectActivityID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `projectactivityprice`
--

CREATE TABLE `projectactivityprice` (
  `ProjectActivityID` int(11) NOT NULL default '0',
  `FromDate` date NOT NULL default '0000-00-00',
  `ToDate` date default NULL,
  `Active` tinyint(4) default NULL,
  `Price` double default NULL,
  `CostPrice` double default NULL,
  PRIMARY KEY  (`ProjectActivityID`,`FromDate`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `projectinvoiceoutstruct`
--

CREATE TABLE `projectinvoiceoutstruct` (
  `InvoiceOutID` int(11) NOT NULL default '0',
  `ProjectID` int(11) NOT NULL default '0',
  `ValidFrom` date NOT NULL default '0000-00-00',
  `ValidTo` date default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`InvoiceOutID`,`ProjectID`,`ValidFrom`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `publishcatcatstruct`
--

CREATE TABLE `publishcatcatstruct` (
  `ParentPublishCategoryID` int(11) NOT NULL default '0',
  `ChildPublishCategoryID` int(11) NOT NULL default '0',
  `CategorySort` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ParentPublishCategoryID`,`ChildPublishCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `publishcatcontstruct`
--

CREATE TABLE `publishcatcontstruct` (
  `PublishCategoryID` int(11) NOT NULL default '0',
  `PublishContentID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `ContentSort` int(11) NOT NULL default '10',
  PRIMARY KEY  (`PublishCategoryID`,`PublishContentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `publishcategory`
--

CREATE TABLE `publishcategory` (
  `PublishCategoryID` int(11) NOT NULL auto_increment,
  `Heading` varchar(255) default NULL,
  `HeadingLink` varchar(255) default NULL,
  `Description` text,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `CreatedDate` datetime default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `Keywords` varchar(255) default NULL,
  `ContentType` varchar(255) NOT NULL default 'article',
  `Extranet` smallint(6) default NULL,
  `Intranet` smallint(6) default NULL,
  `Internet` smallint(6) default NULL,
  `Template` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `LanguageID` char(2) NOT NULL default '',
  `Module` varchar(40) default NULL,
  `HtmlEncode` smallint(6) default NULL,
  `Ingress` text,
  `IngressImageID` int(11) NOT NULL default '0',
  `HeadingImageID` int(11) default NULL,
  `BodyImageID` int(11) NOT NULL default '0',
  `BodyMediaID` int(11) default NULL,
  PRIMARY KEY  (`PublishCategoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `publishcontent`
--

CREATE TABLE `publishcontent` (
  `PublishContentID` int(11) NOT NULL auto_increment,
  `Heading` varchar(255) default NULL,
  `HeadingLink` varchar(255) default NULL,
  `Ingress` text,
  `Teaser` text,
  `Description` text,
  `CompanyID` int(11) default NULL,
  `Grade` varchar(255) default NULL,
  `ReviewerPersonID` int(11) default NULL,
  `Keywords` varchar(255) default NULL,
  `LanguageID` char(2) NOT NULL default '',
  `FileName` varchar(255) default NULL,
  `Template` varchar(255) default NULL,
  `ChapterSortHorisontal` int(11) default NULL,
  `ChapterSortVertical` int(11) default NULL,
  `ContentType` varchar(255) NOT NULL default 'article',
  `ContentSource` varchar(255) default NULL,
  `ContentBackground` varchar(255) default NULL,
  `CreatedDate` datetime default NULL,
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `Extranet` smallint(6) default NULL,
  `Intranet` smallint(6) default NULL,
  `Internet` smallint(6) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `PublishContentParentID` int(11) default NULL,
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `HtmlEncode` smallint(6) default NULL,
  `Publisher` varchar(255) default NULL,
  `Contributor` varchar(255) default NULL,
  `Identifier` varchar(255) default NULL,
  `Source` varchar(255) default NULL,
  `Relation` varchar(255) default NULL,
  `Coverage` varchar(255) default NULL,
  `Rights` varchar(255) default NULL,
  `HeadingImageID` int(11) default NULL,
  `IngressImageID` int(11) NOT NULL default '0',
  `TeaserMediaID` int(11) default NULL,
  `BodyImageID` int(11) NOT NULL default '0',
  `IngressMediaID` int(11) default NULL,
  `BodyMediaID` int(11) default NULL,
  PRIMARY KEY  (`PublishContentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `publishcontenturlstruct`
--

CREATE TABLE `publishcontenturlstruct` (
  `UrlID` int(11) NOT NULL default '0',
  `PublishContentID` int(11) NOT NULL default '0',
  `ParentPublishContentID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`UrlID`,`PublishContentID`),
  KEY `ParentPublishContentID` (`ParentPublishContentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referathead`
--

CREATE TABLE `referathead` (
  `ReferatHeadID` int(11) NOT NULL auto_increment,
  `ReferatSerieID` int(11) default NULL,
  `ReferatHeadNum` int(11) default NULL,
  `ReferatStartTime` datetime default NULL,
  `ReferatStopTime` datetime default NULL,
  `Subject` text,
  `Description` text,
  `ResponsiblePersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatHeadID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatheadcompanystruc`
--

CREATE TABLE `referatheadcompanystruc` (
  `ReferatHeadCompanyStrucID` int(11) NOT NULL auto_increment,
  `ReferatHeadID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default '0000-00-00 00:00:00',
  `ValidTo` datetime default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatHeadCompanyStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatheadpersonstruc`
--

CREATE TABLE `referatheadpersonstruc` (
  `ReferatHeadPersonStrucID` int(11) NOT NULL auto_increment,
  `ReferatHeadID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default '0000-00-00 00:00:00',
  `ValidTo` datetime default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatHeadPersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatline`
--

CREATE TABLE `referatline` (
  `ReferatLineID` int(11) NOT NULL auto_increment,
  `ReferatLineNum` int(11) default NULL,
  `Subject` text,
  `Description` text,
  `Status` varchar(50) default NULL,
  `CreatedDate` datetime default '9999-12-24 00:00:00',
  `ChangedDate` datetime default '9999-12-24 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DeadLineTime` datetime default '9999-12-24 00:00:00',
  `DoneTime` datetime default '9999-12-24 00:00:00',
  `AcceptedTime` datetime default '9999-12-24 00:00:00',
  `Active` smallint(6) default '1',
  `ReferatHeadNum` int(11) default NULL,
  PRIMARY KEY  (`ReferatLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatlinepersonstruc`
--

CREATE TABLE `referatlinepersonstruc` (
  `ReferatLinePersonStrucID` int(11) NOT NULL auto_increment,
  `ReferatLineID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Role` varchar(50) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `CreatedByPersonID` int(11) default NULL,
  PRIMARY KEY  (`ReferatLinePersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatlinestruc`
--

CREATE TABLE `referatlinestruc` (
  `ReferatLineStrucID` int(11) NOT NULL auto_increment,
  `ReferatHeadID` int(11) default NULL,
  `ReferatLineID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatLineStrucID`),
  UNIQUE KEY `ReferatHeadID` (`ReferatHeadID`,`ReferatLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatserie`
--

CREATE TABLE `referatserie` (
  `ReferatSerieID` int(11) NOT NULL auto_increment,
  `Subject` text,
  `Description` text,
  `ReferatNumStart` int(11) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `ResponsiblePersonID` int(11) default NULL,
  PRIMARY KEY  (`ReferatSerieID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatseriecompanystruc`
--

CREATE TABLE `referatseriecompanystruc` (
  `ReferatSerieCompanyStrucID` int(11) NOT NULL auto_increment,
  `ReferatSerieID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatSerieCompanyStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `referatseriepersonstruc`
--

CREATE TABLE `referatseriepersonstruc` (
  `ReferatSeriePersonStrucID` int(11) NOT NULL auto_increment,
  `ReferatSerieID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReferatSeriePersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqfeatureline`
--

CREATE TABLE `reqfeatureline` (
  `ReqFeatureLineID` int(11) NOT NULL auto_increment,
  `ReqFeatureLineNum` int(11) default NULL,
  `Subject` varchar(255) default '',
  `Description` varchar(255) default '',
  `Class` varchar(100) default '',
  `PlannedFinishedTime` date default '0000-00-00',
  `PlannedStartTime` date default '2003-08-25',
  `PlannedHours` varchar(100) default '',
  `PlannedSequence` varchar(100) NOT NULL default '',
  `CreatedDate` datetime default '9999-12-24 00:00:00',
  `ChangedDate` datetime default '9999-12-24 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DeadLineTime` datetime default '9999-12-24 00:00:00',
  `DoneTime` datetime default '9999-12-24 00:00:00',
  `AcceptedTime` datetime default '9999-12-24 00:00:00',
  `Active` smallint(6) default '1',
  `ReqLineNum` int(11) default NULL,
  `ReqCustID` varchar(255) default NULL,
  `ReqCustPriority1` varchar(255) default NULL,
  `ReqCustPriority2` varchar(255) default NULL,
  `ReqCustPriority3` varchar(255) default NULL,
  `ReqOurComment1` text,
  `ReqOurComment2` text,
  `ReqOurComment3` text,
  `ReqGeneralComment` text,
  `Status` varchar(255) default 'Implemented',
  PRIMARY KEY  (`ReqFeatureLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqfeatureline1`
--

CREATE TABLE `reqfeatureline1` (
  `ReqFeatureLineID` int(11) NOT NULL auto_increment,
  `ReqFeatureLineNum` int(11) default NULL,
  `Subject` varchar(255) default '',
  `Description` varchar(255) default '',
  `Class` varchar(100) default '',
  `PlannedStartTime` date default '2003-08-25',
  `PlannedFinishedTime` date default '0000-00-00',
  `PlannedHours` varchar(100) default '',
  `PlannedSequence` varchar(100) default '',
  `Status` varchar(255) default 'Implementation started',
  `CreatedDate` datetime default '9999-12-24 00:00:00',
  `ChangedDate` datetime default '9999-12-24 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DeadLineTime` datetime default '9999-12-24 00:00:00',
  `DoneTime` datetime default '9999-12-24 00:00:00',
  `AcceptedTime` datetime default '9999-12-24 00:00:00',
  `Active` smallint(6) default '1',
  `ReqLineNum` int(11) default NULL,
  `ReqCustID` varchar(255) default NULL,
  `ReqCustPriority1` varchar(255) default NULL,
  `ReqCustPriority2` varchar(255) default NULL,
  `ReqCustPriority3` varchar(255) default NULL,
  `ReqOurComment1` text,
  `ReqOurComment2` text,
  `ReqOurComment3` text,
  `ReqGeneralComment` text,
  PRIMARY KEY  (`ReqFeatureLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqhead`
--

CREATE TABLE `reqhead` (
  `ReqHeadID` int(11) NOT NULL auto_increment,
  `ReqSerieID` int(11) default NULL,
  `ReqHeadNum` int(11) default NULL,
  `ReqStartTime` datetime default NULL,
  `ReqStopTime` datetime default NULL,
  `Subject` text,
  `Description` text,
  `ResponsiblePersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqHeadID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqheadcompanystruc`
--

CREATE TABLE `reqheadcompanystruc` (
  `ReqHeadCompanyStrucID` int(11) NOT NULL auto_increment,
  `ReqHeadID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default '0000-00-00 00:00:00',
  `ValidTo` datetime default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqHeadCompanyStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqheadpersonstruc`
--

CREATE TABLE `reqheadpersonstruc` (
  `ReqHeadPersonStrucID` int(11) NOT NULL auto_increment,
  `ReqHeadID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default '0000-00-00 00:00:00',
  `ValidTo` datetime default '0000-00-00 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqHeadPersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqline`
--

CREATE TABLE `reqline` (
  `ReqLineID` int(11) NOT NULL auto_increment,
  `ReqLineNum` int(11) default NULL,
  `Subject` text,
  `Description` text,
  `Status` varchar(50) default NULL,
  `CreatedDate` datetime default '9999-12-24 00:00:00',
  `ChangedDate` datetime default '9999-12-24 00:00:00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `DeadLineTime` datetime default '9999-12-24 00:00:00',
  `DoneTime` datetime default '9999-12-24 00:00:00',
  `AcceptedTime` datetime default '9999-12-24 00:00:00',
  `Active` smallint(6) default '1',
  `ReqHeadNum` int(11) default NULL,
  `ReqCustID` varchar(255) default NULL,
  `ReqCustPriority1` varchar(255) default NULL,
  `ReqCustPriority2` varchar(255) default NULL,
  `ReqCustPriority3` varchar(255) default NULL,
  `ReqCustComment1` text,
  `ReqOurComment1` text,
  `ReqOurComment2` text,
  `ReqOurComment3` text,
  `ReqGeneralComment` text,
  `ReqMeetingFeedback1` text,
  `ReqOurImplentation1` int(11) default NULL,
  `ReqOurImplentation2` int(11) default NULL,
  `ReqOurImplentation3` int(11) default NULL,
  PRIMARY KEY  (`ReqLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqlinepersonstruc`
--

CREATE TABLE `reqlinepersonstruc` (
  `ReqLinePersonStrucID` int(11) NOT NULL auto_increment,
  `ReqLinePersonID` int(11) default NULL,
  `ReqLineID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Role` varchar(50) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` tinyint(4) default NULL,
  `CreatedByPersonID` int(11) default '0',
  PRIMARY KEY  (`ReqLinePersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqlinestruc`
--

CREATE TABLE `reqlinestruc` (
  `ReqLineStrucID` int(11) NOT NULL auto_increment,
  `ReqHeadID` int(11) default NULL,
  `ReqLineID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqLineStrucID`),
  UNIQUE KEY `ReqHeadID` (`ReqHeadID`,`ReqLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqserie`
--

CREATE TABLE `reqserie` (
  `ReqSerieID` int(11) NOT NULL auto_increment,
  `Subject` text,
  `Description` text,
  `ReqNumStart` int(11) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` smallint(6) default '1',
  `ResponsiblePersonID` int(11) default NULL,
  PRIMARY KEY  (`ReqSerieID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqseriecompanystruc`
--

CREATE TABLE `reqseriecompanystruc` (
  `ReqSerieCompanyStrucID` int(11) NOT NULL auto_increment,
  `ReqSerieID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqSerieCompanyStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `reqseriepersonstruc`
--

CREATE TABLE `reqseriepersonstruc` (
  `ReqSeriePersonStrucID` int(11) NOT NULL auto_increment,
  `ReqSerieID` int(11) default NULL,
  `PersonID` int(11) default NULL,
  `Status` varchar(15) default NULL,
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`ReqSeriePersonStrucID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `RequestID` int(11) NOT NULL auto_increment,
  `Heading` varchar(255) NOT NULL default '',
  `Type` int(11) NOT NULL default '0',
  `PersonID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `Priority` smallint(6) default NULL,
  `DateRequired` datetime default NULL,
  `DateCreated` datetime default NULL,
  `DateFinished` datetime default NULL,
  `FinishedByPersonID` int(11) default NULL,
  `HoursEstimated` double default NULL,
  `HoursEstimatedInternal` double default NULL,
  `Status` int(11) default NULL,
  `ProjectID` int(11) default NULL,
  `ProjectActivityID` int(11) default NULL,
  `DateAccepted` datetime default NULL,
  `AcceptedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `PercentFinished` smallint(6) default NULL,
  `Probability` smallint(6) default NULL,
  `DateRequiredStart` datetime default NULL,
  PRIMARY KEY  (`RequestID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `requestcomment`
--

CREATE TABLE `requestcomment` (
  `RequestCommentID` int(11) NOT NULL auto_increment,
  `RequestID` int(11) NOT NULL default '0',
  `PersonID` int(11) default NULL,
  `CompanyID` int(11) default NULL,
  `URL` varchar(255) default NULL,
  `Description` text NOT NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`RequestCommentID`),
  KEY `RequestID` (`RequestID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `requestcommentfile`
--

CREATE TABLE `requestcommentfile` (
  `RequestCommentFileID` int(11) NOT NULL auto_increment,
  `RequestCommentID` int(11) NOT NULL default '0',
  `Heading` varchar(100) NOT NULL default '',
  `File` int(11) default NULL,
  `FileSize` double default NULL,
  PRIMARY KEY  (`RequestCommentFileID`),
  KEY `RequestCommentID` (`RequestCommentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `RoleID` int(11) NOT NULL auto_increment,
  `RoleName` varchar(255) default NULL,
  `Interface` varchar(255) default NULL,
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Active` tinyint(4) default '1',
  PRIMARY KEY  (`RoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `roleactionaccess`
--

CREATE TABLE `roleactionaccess` (
  `RoleActionAccessID` int(11) NOT NULL auto_increment,
  `Module` varchar(40) NOT NULL default '',
  `Action` varchar(40) NOT NULL default '',
  `RoleID` int(11) NOT NULL default '0',
  `Access` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`RoleActionAccessID`),
  KEY `RoleID` (`RoleID`),
  KEY `Module` (`Module`,`Action`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `rolecontentaccess`
--

CREATE TABLE `rolecontentaccess` (
  `RoleContentAccessID` int(11) NOT NULL auto_increment,
  `TableName` varchar(60) NOT NULL default '',
  `FieldName` varchar(60) NOT NULL default '',
  `PrimaryKey` int(11) NOT NULL default '0',
  `Access` int(11) NOT NULL default '0',
  `RoleID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`RoleContentAccessID`),
  KEY `RoleID` (`RoleID`),
  KEY `TableName` (`TableName`,`FieldName`,`PrimaryKey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `rolefieldaccess`
--

CREATE TABLE `rolefieldaccess` (
  `RoleFieldAccessID` int(11) NOT NULL auto_increment,
  `TableName` varchar(40) NOT NULL default '',
  `FieldName` varchar(40) NOT NULL default '',
  `RoleID` int(11) NOT NULL default '0',
  `Access` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`RoleFieldAccessID`),
  KEY `RoleID` (`RoleID`),
  KEY `TableName` (`TableName`,`FieldName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `roleperson`
--

CREATE TABLE `roleperson` (
  `RoleID` int(11) NOT NULL default '0',
  `PersonID` int(11) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CompanyID` int(11) NOT NULL default '0',
  `Active` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`RoleID`,`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `roletableaccess`
--

CREATE TABLE `roletableaccess` (
  `RoleTableAccessID` int(11) NOT NULL auto_increment,
  `TableName` varchar(60) default NULL,
  `TableAccess` int(11) default NULL,
  `RoleID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`RoleTableAccessID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `roletemplate`
--

CREATE TABLE `roletemplate` (
  `Interface` varchar(40) NOT NULL default '',
  `Module` varchar(40) NOT NULL default '',
  `Template` varchar(40) NOT NULL default '',
  `Cust` smallint(6) default NULL,
  `AccessLevel` int(11) default NULL,
  `AuthType` varchar(20) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `RoleTemplateID` int(11) NOT NULL auto_increment,
  `Log` smallint(6) default '1',
  `LogReferer` smallint(6) default '1',
  `LogUserAgent` smallint(6) default '1',
  `OnlyAllowInternUser` smallint(6) default '1',
  `Active` smallint(6) default '0',
  `InterfaceExtends` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`RoleTemplateID`),
  UNIQUE KEY `Interface` (`Interface`,`Module`,`Template`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `roletemplateaccess`
--

CREATE TABLE `roletemplateaccess` (
  `RoleID` int(11) NOT NULL default '0',
  `Interface` varchar(40) NOT NULL default '',
  `Module` varchar(40) NOT NULL default '',
  `Template` varchar(40) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `AccessLevel` int(11) default NULL,
  PRIMARY KEY  (`RoleID`,`Interface`,`Module`,`Template`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `salary`
--

CREATE TABLE `salary` (
  `SalaryID` int(11) NOT NULL auto_increment,
  `SalaryConfID` int(11) default NULL,
  `AccountPlanID` bigint(20) default NULL,
  `JournalID` int(11) default NULL,
  `ValidFrom` date default NULL,
  `ValidTo` date default NULL,
  `PayDate` date default NULL,
  `Period` varchar(7) default NULL,
  `DomesticBankAccount` varchar(255) default NULL,
  `EmployeeRateID` int(11) default NULL,
  `CreatedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `JournalDate` date default NULL,
  `VacationPayment` double default NULL,
  `AmountThisPeriod` double default NULL,
  PRIMARY KEY  (`SalaryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `salaryconf`
--

CREATE TABLE `salaryconf` (
  `SalaryConfID` int(11) NOT NULL auto_increment,
  `AccountPlanID` bigint(20) default NULL,
  `CreatedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SalaryConfID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `salaryconfline`
--

CREATE TABLE `salaryconfline` (
  `SalaryConfLineID` int(11) NOT NULL auto_increment,
  `SalaryConfID` int(11) default NULL,
  `LineNumber` int(11) default NULL,
  `SalaryText` varchar(255) default NULL,
  `NumberInPeriod` float default NULL,
  `Rate` float default NULL,
  `AmountThisPeriod` double default NULL,
  `AccountPlanID` bigint(20) default NULL,
  `EnableEmployeeTax` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `SalaryCode` varchar(255) default NULL,
  `ProjectID` int(11) default NULL,
  `DepartmentID` int(11) default NULL,
  `EnableVacationPayment` smallint(6) default NULL,
  PRIMARY KEY  (`SalaryConfLineID`),
  KEY `SalaryConfID` (`SalaryConfID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `salaryline`
--

CREATE TABLE `salaryline` (
  `SalaryLineID` int(11) NOT NULL auto_increment,
  `SalaryID` int(11) default NULL,
  `LineNumber` smallint(6) default NULL,
  `SalaryText` varchar(255) default NULL,
  `NumberInPeriod` double default NULL,
  `Rate` double default NULL,
  `AmountThisPeriod` double default NULL,
  `AmountThisYear` double default NULL,
  `AccountPlanID` bigint(20) default NULL,
  `EnableEmployeeTax` smallint(6) default NULL,
  `ProjectID` int(11) default NULL,
  `DepartmentID` int(11) default NULL,
  `EmployeeTax` double default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `EnableVacationPayment` smallint(6) default NULL,
  `SalaryCode` varchar(255) default NULL,
  PRIMARY KEY  (`SalaryLineID`),
  KEY `SalaryID` (`SalaryID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `SessionID` int(11) NOT NULL default '0',
  `Auth` smallint(6) default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `CompanyID` int(11) NOT NULL default '0',
  `Css` varchar(15) NOT NULL default '',
  `LanguageID` char(2) NOT NULL default '',
  `Fingerprint` varchar(50) default NULL,
  `LastClick` datetime default NULL,
  `LoginDate` datetime default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SessionID`),
  KEY `PersonID` (`PersonID`),
  KEY `CompanyID` (`CompanyID`),
  KEY `PersonID_2` (`PersonID`),
  KEY `CompanyID_2` (`CompanyID`),
  KEY `PersonID_3` (`PersonID`),
  KEY `CompanyID_3` (`CompanyID`),
  KEY `PersonID_4` (`PersonID`),
  KEY `CompanyID_4` (`CompanyID`),
  KEY `PersonID_5` (`PersonID`),
  KEY `CompanyID_5` (`CompanyID`),
  KEY `PersonID_6` (`PersonID`),
  KEY `CompanyID_6` (`CompanyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `setup`
--

CREATE TABLE `setup` (
  `SetupID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Value` varchar(255) default NULL,
  `Module` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `CompanyID` int(11) default '0',
  `PersonID` int(11) default '0',
  PRIMARY KEY  (`SetupID`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `shortreport`
--

CREATE TABLE `shortreport` (
  `AccountReportID` int(11) NOT NULL auto_increment,
  `Period` varchar(8) NOT NULL default '',
  `DeliveredOnTime` smallint(6) default '0',
  `DeliveredOnTimeNo` smallint(6) default '0',
  `DeliveredSorted` smallint(6) default '0',
  `DeliveredSortedNo` smallint(6) default '0',
  `Elements` varchar(255) default NULL,
  `Praise` varchar(255) default NULL,
  `Improvements` varchar(255) default NULL,
  `PayExtra1` double default '0',
  `PayExtra2` double default '0',
  `PayExtra3` double default '0',
  `BalancePosts` varchar(255) default NULL,
  `ResultPosts` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `RememberLineChoice` smallint(6) default NULL,
  PRIMARY KEY  (`AccountReportID`),
  KEY `Period` (`Period`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `siteinfo`
--

CREATE TABLE `siteinfo` (
  `SiteInfoID` int(11) NOT NULL auto_increment,
  `CompanyID` int(11) default NULL,
  `WebmasterPersonID` int(11) default NULL,
  `OrderPersonID` int(11) default NULL,
  `SupportPersonID` int(11) default NULL,
  `InvoicePersonID` int(11) default NULL,
  `Description` text,
  `Name` varchar(255) default NULL,
  `ValidFromDate` date default NULL,
  `ValidToDate` date default NULL,
  `Active` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SiteInfoID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `sla`
--

CREATE TABLE `sla` (
  `SlaID` int(11) NOT NULL auto_increment,
  `CreatedDate` datetime default NULL,
  `Frequency` smallint(6) default NULL,
  `FirstDate` datetime default NULL,
  `CompanyID` varchar(255) default NULL,
  `Region` varchar(20) default NULL,
  `SalesPersonID` int(11) default NULL,
  `CreatedByPersonID` int(11) default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `SignedDate` date default NULL,
  `CompanyGroup` smallint(6) default NULL,
  `ImprovementAgenda` text,
  `StopDate` date default NULL,
  PRIMARY KEY  (`SlaID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `slaeffort`
--

CREATE TABLE `slaeffort` (
  `SlaEffortID` int(11) NOT NULL auto_increment,
  `SlaImprovementID` int(11) NOT NULL default '0',
  `Priority` smallint(6) default NULL,
  `Description` varchar(255) default NULL,
  `CustomerEffort` smallint(6) default NULL,
  `ShowCustomer` smallint(6) default NULL,
  `RegisteredDate` date default NULL,
  `RegisteredBy` int(11) default NULL,
  `CompletedDate` date default NULL,
  `Responsible` varchar(255) default NULL,
  `ScheduledDate` date default NULL,
  `CompletedBy` varchar(255) default NULL,
  `Status` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CompletedDescription` varchar(255) default NULL,
  PRIMARY KEY  (`SlaEffortID`),
  KEY `SlaImprovementID` (`SlaImprovementID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `slagoal`
--

CREATE TABLE `slagoal` (
  `SlaID` int(11) NOT NULL default '0',
  `SlaTypeID` int(11) NOT NULL default '0',
  `Goal` double default NULL,
  `GoalAchievment` double default NULL,
  `SlaGoalID` int(11) NOT NULL auto_increment,
  `Finished` smallint(6) default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SlaGoalID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `slaimprovement`
--

CREATE TABLE `slaimprovement` (
  `SlaImprovementID` int(11) NOT NULL auto_increment,
  `What` varchar(255) default NULL,
  `Who` varchar(255) default NULL,
  `Code` int(11) default NULL,
  `SlaGoalID` int(11) default NULL,
  `WhenDate` date default NULL,
  `CustomerEffort` smallint(6) default NULL,
  PRIMARY KEY  (`SlaImprovementID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `stocklocation`
--

CREATE TABLE `stocklocation` (
  `StockLocationID` int(11) NOT NULL auto_increment,
  `PlantID` char(3) NOT NULL default '1',
  `AddressID` char(3) NOT NULL default '1',
  `ReolID` char(3) NOT NULL default 'A',
  `HeightID` char(3) NOT NULL default '1',
  `PositionID` char(3) NOT NULL default 'A',
  `Active` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByPersonID` int(11) default NULL,
  `CreatedDateTime` datetime default NULL,
  `ChangedByPersonID` int(11) default NULL,
  PRIMARY KEY  (`StockLocationID`),
  UNIQUE KEY `PlantID` (`PlantID`,`AddressID`,`ReolID`,`HeightID`,`PositionID`),
  KEY `PlantID_2` (`PlantID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `survey`
--

CREATE TABLE `survey` (
  `SurveyID` int(11) NOT NULL auto_increment,
  `SurveyHead` text,
  `SurveyBody` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CompanyID` int(11) default NULL,
  `CompanyName` varchar(255) default NULL,
  `Address` varchar(255) default NULL,
  `Phone` varchar(255) default NULL,
  `Email` varchar(255) default NULL,
  `ContactPerson` varchar(255) default NULL,
  `Logo` varchar(255) default NULL,
  `Recipients` text,
  `LogoSize` int(11) default NULL,
  `LogoHeight` int(11) default NULL,
  `LogoWidth` int(11) default NULL,
  `LogoType` varchar(20) default NULL,
  `LogoHeading` varchar(255) default NULL,
  `LogoURL` varchar(255) default NULL,
  `LogoAlign` varchar(10) default NULL,
  PRIMARY KEY  (`SurveyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `sync`
--

CREATE TABLE `sync` (
  `SyncID` int(11) NOT NULL auto_increment,
  `SyncDescription` text,
  `SyncLastDate` datetime default NULL,
  `SyncHost` varchar(255) default NULL,
  `SyncDatabase` varchar(255) default NULL,
  `SyncUser` varchar(255) default NULL,
  `SyncPassword` varchar(255) default NULL,
  `SyncPath` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SyncID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `syncconfig`
--

CREATE TABLE `syncconfig` (
  `SyncConfigID` int(11) NOT NULL auto_increment,
  `SyncID` int(11) default NULL,
  `SyncDirection` set('from','to','bidirectional') default NULL,
  `SyncTable` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SyncConfigID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `synclog`
--

CREATE TABLE `synclog` (
  `SyncLogID` int(11) NOT NULL auto_increment,
  `SynchronisationID` int(11) default NULL,
  `SyncDate` datetime default NULL,
  `Status` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SyncLogID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `synctablelog`
--

CREATE TABLE `synctablelog` (
  `SyncTableLogID` int(11) NOT NULL auto_increment,
  `SyncLogID` int(11) default NULL,
  `SyncConfigID` int(11) default NULL,
  `SyncNumRecords` int(11) default NULL,
  `SyncMessage` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`SyncTableLogID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `tab`
--

CREATE TABLE `tab` (
  `TabID` int(11) NOT NULL auto_increment,
  `TableName` varchar(255) default NULL,
  `Interface` varchar(255) default NULL,
  `Module` varchar(255) default NULL,
  `Template` varchar(255) default NULL,
  `Args` varchar(255) default NULL,
  `Name` varchar(255) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Sort` smallint(6) default NULL,
  `Description` varchar(255) NOT NULL default '',
  `LanguageID` char(2) NOT NULL default 'no',
  `Version` varchar(4) NOT NULL default '0.1',
  `State` set('development','alfa','beta','production') NOT NULL default 'development',
  `Target` varchar(20) NOT NULL default '',
  `MD5Created` varchar(32) NOT NULL default '',
  `MD5Updated` varchar(32) NOT NULL default '',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` smallint(6) default '1',
  `Accesskey` char(1) NOT NULL default '',
  PRIMARY KEY  (`TabID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `templaterestrictions`
--

CREATE TABLE `templaterestrictions` (
  `TemplateRestrictionsID` int(11) NOT NULL auto_increment,
  `tr_download` tinyint(4) NOT NULL default '0',
  `tr_link` tinyint(4) NOT NULL default '0',
  `tr_file` tinyint(4) NOT NULL default '0',
  `tr_paragraph_vert` tinyint(4) NOT NULL default '1',
  `tr_paragraph_horiz` tinyint(4) NOT NULL default '1',
  `tr_heading_link` tinyint(4) NOT NULL default '0',
  `tr_paragraph_picture` tinyint(4) NOT NULL default '0',
  `tr_bg_color` tinyint(4) NOT NULL default '0',
  `tr_move_horiz` tinyint(4) NOT NULL default '0',
  `tr_move_vert` tinyint(4) NOT NULL default '0',
  `tr_type` varchar(20) NOT NULL default '',
  `tr_name` varchar(255) NOT NULL default '',
  `tr_heading` tinyint(4) NOT NULL default '0',
  `tr_ingress` tinyint(4) NOT NULL default '0',
  `tr_mtext` tinyint(4) NOT NULL default '0',
  `tr_grade` tinyint(4) NOT NULL default '0',
  `tr_company` tinyint(4) NOT NULL default '0',
  `tr_artist` tinyint(4) NOT NULL default '0',
  `tr_reviewer` tinyint(4) NOT NULL default '0',
  `tr_teaser` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`TemplateRestrictionsID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `timeliste`
--

CREATE TABLE `timeliste` (
  `TimelisteID` int(11) NOT NULL auto_increment,
  `PersonID` int(11) NOT NULL default '0',
  `Description` varchar(255) default NULL,
  `Date` date default NULL,
  `Hours` float(10,2) NOT NULL default '0.00',
  `ProjectID` int(11) NOT NULL default '0',
  `ProjectActivityID` int(11) NOT NULL default '0',
  `Progress` char(3) default NULL,
  `Active` tinyint(4) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `OutInvoiceID` int(11) default '0',
  `ToBeInvoiced` smallint(6) default '1',
  `SalaryPaid` smallint(6) default NULL,
  `InInvoiceID` int(11) default NULL,
  `SalaryID` smallint(6) default '0',
  PRIMARY KEY  (`TimelisteID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `timer_customeruser`
--

CREATE TABLE `timer_customeruser` (
  `customer_id` int(10) unsigned NOT NULL default '0',
  `ext_user_id` int(10) unsigned NOT NULL default '0',
  `unactive` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_estimateitem`
--

CREATE TABLE `timer_estimateitem` (
  `estimate_id` int(10) unsigned NOT NULL default '0',
  `projecttype_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `amount` float NOT NULL default '0',
  KEY `Index_1` (`estimate_id`),
  KEY `Index_2` (`projecttype_id`),
  KEY `Index_3` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_estimatesheet`
--

CREATE TABLE `timer_estimatesheet` (
  `estimate_id` int(10) unsigned NOT NULL auto_increment,
  `ext_client_id` int(10) unsigned NOT NULL default '0',
  `ext_user_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`estimate_id`),
  KEY `Index_2` (`ext_client_id`),
  KEY `Index_3` (`ext_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_holidays`
--

CREATE TABLE `timer_holidays` (
  `day` bigint(20) unsigned default '0',
  UNIQUE KEY `Index_1` (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_logday`
--

CREATE TABLE `timer_logday` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `ext_user_id` int(10) unsigned NOT NULL default '0',
  `ext_client_id` int(10) unsigned NOT NULL default '0',
  `time_from` int(10) unsigned NOT NULL default '0',
  `time_to` int(10) unsigned NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`log_id`),
  KEY `Index_2` (`ext_user_id`),
  KEY `Index_3` (`ext_client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_logdaycustomer`
--

CREATE TABLE `timer_logdaycustomer` (
  `log_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `time_from` int(10) unsigned NOT NULL default '0',
  `time_to` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_logproject`
--

CREATE TABLE `timer_logproject` (
  `log_id` int(10) unsigned NOT NULL default '0',
  `projecttype_id` int(10) unsigned NOT NULL default '0',
  `amount` float unsigned NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `customer_id` int(10) unsigned NOT NULL default '0',
  KEY `Index_1` (`log_id`),
  KEY `Index_2` (`projecttype_id`),
  KEY `Index_3` (`customer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_projectestimate`
--

CREATE TABLE `timer_projectestimate` (
  `estimate_id` int(11) NOT NULL default '0',
  `projecttype_id` int(11) NOT NULL default '0',
  `hourly_cost` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `timer_projecttype`
--

CREATE TABLE `timer_projecttype` (
  `projecttype_id` int(10) unsigned NOT NULL auto_increment,
  `ext_client_id` int(10) unsigned NOT NULL default '0',
  `hourly_cost` int(10) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`projecttype_id`),
  KEY `Index_2` (`ext_client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `todoes`
--

CREATE TABLE `todoes` (
  `ToDoID` tinyint(4) NOT NULL auto_increment,
  `PersonID` tinyint(4) NOT NULL default '0',
  `Heading` varchar(255) NOT NULL default '',
  `Description` text NOT NULL,
  `Timestamp` tinyint(4) NOT NULL default '0',
  `TimeCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `Type` varchar(20) NOT NULL default '',
  `Active` tinyint(4) NOT NULL default '1',
  `Priority` tinyint(4) NOT NULL default '0',
  KEY `ToDoID` (`ToDoID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `tpservice`
--

CREATE TABLE `tpservice` (
  `TPServiceID` int(11) NOT NULL auto_increment,
  `ServiceName` varchar(20) NOT NULL default '',
  `ValidEdi` smallint(6) NOT NULL default '0',
  `ValidWeb` smallint(6) NOT NULL default '0',
  `Description` varchar(255) NOT NULL default '',
  `Subscribeable` smallint(6) NOT NULL default '0',
  `RoleID` int(11) NOT NULL default '0',
  `ServiceUrl` varchar(255) NOT NULL default '',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`TPServiceID`),
  KEY `ServiceName` (`ServiceName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `url`
--

CREATE TABLE `url` (
  `UrlID` int(11) NOT NULL auto_increment,
  `Url` varchar(255) NOT NULL default '',
  `UrlName` varchar(255) NOT NULL default '',
  `UrlTitle` varchar(255) NOT NULL default '',
  `LanguageID` varchar(255) NOT NULL default '',
  `Active` smallint(6) default '1',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`UrlID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `urlalias`
--

CREATE TABLE `urlalias` (
  `Alias` varchar(20) NOT NULL default '',
  `Url` varchar(255) default NULL,
  `Internal` smallint(6) default '1',
  `Active` smallint(6) default '1',
  `VirtualHost` varchar(255) default NULL,
  PRIMARY KEY  (`Alias`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `varelager`
--

CREATE TABLE `varelager` (
  `VareLagerID` int(11) NOT NULL auto_increment,
  `CreatedDate` date NOT NULL default '0000-00-00',
  `Description` varchar(50) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`VareLagerID`),
  KEY `CreatedDate` (`CreatedDate`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `varelagerline`
--

CREATE TABLE `varelagerline` (
  `VareLagerLineID` int(11) NOT NULL auto_increment,
  `VareLagerID` int(11) NOT NULL default '0',
  `ProductNr` int(11) default NULL,
  `ProductName` varchar(50) default NULL,
  `CostPrice` double default NULL,
  `Antall` decimal(16,2) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`VareLagerLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `vat`
--

CREATE TABLE `vat` (
  `Percent` float default NULL,
  `VatID` int(11) NOT NULL default '0',
  `AccountPlanID` bigint(20) default NULL,
  `Active` smallint(6) default NULL,
  `EnableVatOverride` smallint(6) NOT NULL default '0',
  `ValidFrom` date NOT NULL default '0000-00-00',
  `ValidTo` date NOT NULL default '0000-00-00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Type` set('sale','buy') default 'sale',
  `PairID` int(11) NOT NULL default '0',
  `ID` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`ID`),
  KEY `VatID` (`VatID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `voucher`
--

CREATE TABLE `voucher` (
  `VoucherID` int(11) NOT NULL auto_increment,
  `JournalID` int(11) default NULL,
  `VoucherType` char(3) default 'K',
  `VoucherDate` date default NULL,
  `VoucherText` varchar(255) default NULL,
  `VoucherPeriod` varchar(7) default NULL,
  `AmountIn` decimal(16,2) default '0.00',
  `AmountOut` decimal(16,2) default '0.00',
  `AccountPlanID` bigint(20) default NULL,
  `Currency` char(3) default 'NOK',
  `Vat` decimal(16,2) default '0.00',
  `Quantity` decimal(16,3) default NULL,
  `DepartmentID` int(11) default NULL,
  `ProjectID` int(11) default NULL,
  `DueDate` date default NULL,
  `Reference` varchar(255) default '',
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `InsertedByPersonID` int(11) default NULL,
  `Active` smallint(6) default '0',
  `ForeignAmountIn` double default NULL,
  `ForeignAmountOut` double default NULL,
  `BalanceOk` smallint(6) default '0',
  `AutomaticFromVoucherID` int(11) default NULL,
  `AutomaticReason` varchar(30) default NULL,
  `DescriptionID` varchar(20) default NULL,
  `VatID` int(11) default NULL,
  `UpdatedByPersonID` int(11) default NULL,
  `AccountLineID` int(11) default NULL,
  `AutomaticBalanceID` int(11) default NULL,
  `EnableAutoBalance` smallint(6) default '0',
  `DisableAutoVat` smallint(6) default NULL,
  `AutoFromWeeklySale` int(11) default NULL,
  `AutomaticVatVoucherID` int(11) default NULL,
  `AutoKID` smallint(6) default NULL,
  `AddedByAutoBalance` smallint(6) default '0',
  `InsertedDateTime` datetime default NULL,
  PRIMARY KEY  (`VoucherID`),
  KEY `BalanceOk` (`BalanceOk`),
  KEY `JournalID` (`JournalID`),
  KEY `Active` (`Active`),
  KEY `AccountPlanID` (`AccountPlanID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `voucheraccountline`
--

CREATE TABLE `voucheraccountline` (
  `VoucherAccountLineID` int(11) NOT NULL auto_increment,
  `JournalID` int(11) NOT NULL default '0',
  `AccountLineID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`VoucherAccountLineID`),
  UNIQUE KEY `JournalID` (`JournalID`,`AccountLineID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `voucherstruct`
--

CREATE TABLE `voucherstruct` (
  `VoucherStructID` int(11) NOT NULL auto_increment,
  `ParentVoucherID` int(11) default NULL,
  `ChildVoucherID` int(11) default NULL,
  `Closed` smallint(6) NOT NULL default '0',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`VoucherStructID`),
  KEY `Parent` (`ParentVoucherID`),
  KEY `Child` (`ChildVoucherID`),
  KEY `Closed` (`Closed`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

--
-- Table structure for table `vouchertmp`
--

CREATE TABLE `vouchertmp` (
  `VoucherID` int(11) NOT NULL auto_increment,
  `JournalID` int(11) default NULL,
  `VoucherType` char(3) default 'K',
  `VoucherDate` date default NULL,
  `VoucherText` varchar(255) default NULL,
  `VoucherPeriod` varchar(7) default NULL,
  `AmountIn` decimal(16,2) default '0.00',
  `AmountOut` decimal(16,2) default '0.00',
  `AccountPlanID` bigint(20) default NULL,
  `Currency` char(3) default 'NOK',
  `Vat` decimal(16,2) default '0.00',
  `Quantity` int(11) default '0',
  `DepartmentID` varchar(255) default '0',
  `ProjectID` varchar(255) default '0',
  `DueDate` date default NULL,
  `Reference` varchar(255) default '',
  `Description` text,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `CreatedByID` int(11) default '0',
  `Active` smallint(6) default '0',
  `ForeignAmountIn` double default NULL,
  `ForeignAmountOut` double default NULL,
  `BalanceOk` smallint(6) default '0',
  `AutomaticFromVoucherID` int(11) default NULL,
  `AutomaticReason` varchar(40) NOT NULL default '',
  `DescriptionID` varchar(20) default NULL,
  `VatID` int(11) default NULL,
  `ChangedByPersonID` int(11) default NULL,
  `AccountLineID` int(11) default NULL,
  `AutomaticBalanceID` int(11) default NULL,
  `EnableAutoBalance` smallint(6) default '0',
  `DisableAutoVat` smallint(6) default NULL,
  `AutoFromWeeklySale` int(11) default NULL,
  `AutomaticVatVoucherID` int(11) default NULL,
  `AutoKID` smallint(6) default NULL,
  PRIMARY KEY  (`VoucherID`),
  KEY `BalanceOk` (`BalanceOk`),
  KEY `JournalID` (`JournalID`),
  KEY `Active` (`Active`),
  KEY `AccountPlanID` (`AccountPlanID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `weeklysale`
--

CREATE TABLE `weeklysale` (
  `WeeklySaleID` int(11) NOT NULL auto_increment,
  `WeeklySaleConfID` int(11) default NULL,
  `JournalID` int(11) default NULL,
  `Week` varchar(20) default NULL,
  `DepartmentID` int(11) default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `PrivateAmount` decimal(16,2) default '0.00',
  `Name` varchar(255) default NULL,
  `TotalAmount` decimal(16,2) default '0.00',
  `Period` varchar(7) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `UpdatedByPersonID` int(11) default NULL,
  `PrivateExplanation` varchar(255) default NULL,
  `InsertedDateTime` datetime default NULL,
  `JournalDate` date default NULL,
  `TotalSale` decimal(16,2) default '0.00',
  `TotalCash` decimal(16,2) default '0.00',
  `PermanentCash` decimal(16,5) default NULL,
  `Year` varchar(4) default NULL,
  `ActuallyCashAmount` decimal(16,3) default NULL,
  `CashAmountExplanation` varchar(255) default NULL,
  `VoucherType` char(3) default 'O',
  PRIMARY KEY  (`WeeklySaleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `weeklysaleconf`
--

CREATE TABLE `weeklysaleconf` (
  `WeeklySaleConfID` int(11) NOT NULL auto_increment,
  `DepartmentID` int(11) default NULL,
  `InsertedByPersonID` int(11) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Name` varchar(255) default NULL,
  `PermanentCash` decimal(16,5) default NULL,
  `VoucherType` char(3) default 'O',
  `InsertedDateTime` datetime default NULL,
  `UpdatedByPersonID` int(11) default NULL,
  PRIMARY KEY  (`WeeklySaleConfID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `weeklysaleday`
--

CREATE TABLE `weeklysaleday` (
  `WeeklySaleDayID` int(11) NOT NULL auto_increment,
  `WeeklySaleID` int(11) default NULL,
  `Type` smallint(6) default NULL,
  `DayID` smallint(6) default NULL,
  `Znr` int(11) default NULL,
  `ZnrTotalAmount` decimal(16,3) default '0.000',
  `Group1Amount` decimal(16,3) default '0.000',
  `Group2Amount` decimal(16,3) default '0.000',
  `Group3Amount` decimal(16,3) default '0.000',
  `Group4Amount` decimal(16,3) default '0.000',
  `Group5Amount` decimal(16,3) default '0.000',
  `Group6Amount` decimal(16,3) default '0.000',
  `Group7Amount` decimal(16,3) default '0.000',
  `Group8Amount` decimal(16,3) default '0.000',
  `Group9Amount` decimal(16,3) default '0.000',
  `Group10Amount` decimal(16,3) default '0.000',
  `Group11Amount` decimal(16,3) default '0.000',
  `Group12Amount` decimal(16,3) default '0.000',
  `Group13Amount` decimal(16,3) default '0.000',
  `Group14Amount` decimal(16,3) default '0.000',
  `Group15Amount` decimal(16,3) default '0.000',
  `Group16Amount` decimal(16,3) default '0.000',
  `Group17Amount` decimal(16,3) default '0.000',
  `Group18Amount` decimal(16,3) default '0.000',
  `Group19Amount` decimal(16,3) default '0.000',
  `Group20Amount` decimal(16,3) default '0.000',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `PersonID` int(11) default NULL,
  `Day` smallint(6) default NULL,
  `Datetime` datetime default NULL,
  `ActuallyCashAmount` decimal(16,3) default NULL,
  `CashAmountExplanation` varchar(255) default NULL,
  `Locked` smallint(6) default NULL,
  `Group1Quantity` decimal(16,3) default NULL,
  `Group2Quantity` decimal(16,3) default NULL,
  `Group3Quantity` decimal(16,3) default NULL,
  `Group4Quantity` decimal(16,3) default NULL,
  `Group5Quantity` decimal(16,3) default NULL,
  `Group6Quantity` decimal(16,3) default NULL,
  `Group7Quantity` decimal(16,3) default NULL,
  `Group8Quantity` decimal(16,3) default NULL,
  `Group9Quantity` decimal(16,3) default NULL,
  `Group10Quantity` decimal(16,3) default NULL,
  `Group11Quantity` decimal(16,3) default NULL,
  `Group12Quantity` decimal(16,3) default NULL,
  `Group13Quantity` decimal(16,3) default NULL,
  `Group14Quantity` decimal(16,3) default NULL,
  `Group15Quantity` decimal(16,3) default NULL,
  `Group16Quantity` decimal(16,3) default NULL,
  `Group17Quantity` decimal(16,3) default NULL,
  `Group18Quantity` decimal(16,3) default NULL,
  `Group19Quantity` decimal(16,3) default NULL,
  `Group20Quantity` decimal(16,3) default NULL,
  `ParentWeeklySaleDayID` int(11) default NULL,
  PRIMARY KEY  (`WeeklySaleDayID`),
  KEY `WeeklySaleID` (`WeeklySaleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `weeklysalegroupconf`
--

CREATE TABLE `weeklysalegroupconf` (
  `WeeklySaleGroupConfID` int(11) NOT NULL auto_increment,
  `WeeklySaleConfID` int(11) default NULL,
  `Group1Name` varchar(25) default NULL,
  `Group1Account` int(11) default NULL,
  `Group2Name` varchar(25) default NULL,
  `Group2Account` int(11) default NULL,
  `Group3Name` varchar(25) default NULL,
  `Group3Account` int(11) default NULL,
  `Group4Name` varchar(25) default NULL,
  `Group4Account` int(11) default NULL,
  `Group5Name` varchar(25) default NULL,
  `Group5Account` int(11) default NULL,
  `Group6Name` varchar(25) default NULL,
  `Group6Account` int(11) default NULL,
  `Group7Name` varchar(25) default NULL,
  `Group7Account` int(11) default NULL,
  `Group8Name` varchar(25) default NULL,
  `Group8Account` int(11) default NULL,
  `Group9Name` varchar(25) default NULL,
  `Group9Account` int(11) default NULL,
  `Group10Name` varchar(25) default NULL,
  `Group10Account` int(11) default NULL,
  `Group11Name` varchar(25) default NULL,
  `Group11Account` int(11) default NULL,
  `Group12Name` varchar(25) default NULL,
  `Group12Account` int(11) default NULL,
  `Group13Name` varchar(25) default NULL,
  `Group13Account` int(11) default NULL,
  `Group14Name` varchar(25) default NULL,
  `Group14Account` int(11) default NULL,
  `Group15Name` varchar(25) default NULL,
  `Group15Account` int(11) default NULL,
  `Group16Name` varchar(25) default NULL,
  `Group16Account` int(11) default NULL,
  `Group17Name` varchar(25) default NULL,
  `Group17Account` int(11) default NULL,
  `Group18Name` varchar(25) default NULL,
  `Group18Account` int(11) default NULL,
  `Group19Name` varchar(25) default NULL,
  `Group19Account` int(11) default NULL,
  `Group20Name` varchar(25) default NULL,
  `Group20Account` int(11) default NULL,
  `Type` smallint(6) default NULL,
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `Group1ProjectID` int(11) default NULL,
  `Group2ProjectID` int(11) default NULL,
  `Group3ProjectID` int(11) default NULL,
  `Group4ProjectID` int(11) default NULL,
  `Group5ProjectID` int(11) default NULL,
  `Group6ProjectID` int(11) default NULL,
  `Group7ProjectID` int(11) default NULL,
  `Group8ProjectID` int(11) default NULL,
  `Group9ProjectID` int(11) default NULL,
  `Group10ProjectID` int(11) default NULL,
  `Group11ProjectID` int(11) default NULL,
  `Group12ProjectID` int(11) default NULL,
  `Group13ProjectID` int(11) default NULL,
  `Group14ProjectID` int(11) default NULL,
  `Group15ProjectID` int(11) default NULL,
  `Group16ProjectID` int(11) default NULL,
  `Group17ProjectID` int(11) default NULL,
  `Group18ProjectID` int(11) default NULL,
  `Group19ProjectID` int(11) default NULL,
  `Group20ProjectID` int(11) default NULL,
  `Group1DepartmentID` int(11) default NULL,
  `Group2DepartmentID` int(11) default NULL,
  `Group3DepartmentID` int(11) default NULL,
  `Group4DepartmentID` int(11) default NULL,
  `Group5DepartmentID` int(11) default NULL,
  `Group6DepartmentID` int(11) default NULL,
  `Group7DepartmentID` int(11) default NULL,
  `Group8DepartmentID` int(11) default NULL,
  `Group9DepartmentID` int(11) default NULL,
  `Group10DepartmentID` int(11) default NULL,
  `Group11DepartmentID` int(11) default NULL,
  `Group12DepartmentID` int(11) default NULL,
  `Group13DepartmentID` int(11) default NULL,
  `Group14DepartmentID` int(11) default NULL,
  `Group15DepartmentID` int(11) default NULL,
  `Group16DepartmentID` int(11) default NULL,
  `Group17DepartmentID` int(11) default NULL,
  `Group18DepartmentID` int(11) default NULL,
  `Group19DepartmentID` int(11) default NULL,
  `Group20DepartmentID` int(11) default NULL,
  PRIMARY KEY  (`WeeklySaleGroupConfID`),
  KEY `WeeklySaleConfID` (`WeeklySaleConfID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `workout`
--

CREATE TABLE `workout` (
  `WorkOutID` int(4) NOT NULL auto_increment,
  `Date` date NOT NULL default '0000-00-00',
  `Time` time NOT NULL default '00:00:00',
  `Calories` int(11) NOT NULL default '0',
  `Weight` int(11) NOT NULL default '0',
  `LifeWidth` int(11) NOT NULL default '0',
  `WorkOutByID` int(11) NOT NULL default '0',
  `Active` smallint(6) default '1',
  PRIMARY KEY  (`WorkOutID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `zipcode`
--

CREATE TABLE `zipcode` (
  `ZipCodeID` varchar(10) NOT NULL default '',
  `DepartmentID` int(11) NOT NULL default '0',
  `City` varchar(255) NOT NULL default '',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ZipCodeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

