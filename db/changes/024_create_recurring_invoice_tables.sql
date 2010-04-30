-- phpMyAdmin SQL Dump
-- version 2.11.8.1deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2010 at 11:36 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6-2ubuntu4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `BLIX_SOLUTIONS_AS`
--

-- --------------------------------------------------------

--
-- Table structure for table `recurring`
--

CREATE TABLE IF NOT EXISTS `recurring` (
  `RecurringID` bigint(20) NOT NULL auto_increment,
  `StartDate` date NOT NULL,
  `TimeInterval` varchar(20) NOT NULL,
  `LastDate` date NOT NULL,
  PRIMARY KEY  (`RecurringID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10968 ;

-- --------------------------------------------------------

--
-- Table structure for table `recurringout`
--

CREATE TABLE IF NOT EXISTS `recurringout` (
  `RecurringID` bigint(20) NOT NULL auto_increment,
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
  `CustomerAccountPlanID` bigint(20) default NULL,
  `SupplierAccountPlanID` bigint(20) default NULL,
  `ExternalID` int(11) NOT NULL default '0',
  `JournalID` int(11) NOT NULL default '0',
  `Note` varchar(255) default NULL,
  `FakturabankID` bigint(20) default NULL,
  `VoucherType` char(3) default NULL,
  PRIMARY KEY  (`RecurringID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10968 ;

-- --------------------------------------------------------

--
-- Table structure for table `recurringoutline`
--

CREATE TABLE IF NOT EXISTS `recurringoutline` (
  `LineID` int(11) NOT NULL auto_increment,
  `RecurringID` bigint(20) default NULL,
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
  `InsertedByPersonID` bigint(20) NOT NULL default '0',
  `UpdatedByPersonID` bigint(20) NOT NULL default '0',
  `InsertedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `LineNum` int(11) NOT NULL default '0',
  `TaxAmount` decimal(16,5) default NULL,
  `Discount` decimal(16,5) default NULL,
  `UnitCustPriceCurrencyID` varchar(4) default 'NOK',
  `UnitCostPriceCurrencyID` varchar(4) default 'NOK',
  PRIMARY KEY  (`LineID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

