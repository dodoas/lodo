alter table accountplan add Country varchar(255);

alter table product add UnitCustPriceCurrencyID char(4) default 'NOK';
alter table session add ScreenSize char(1) default 'L'; #S = mobile, M=smartphone, L=Laptop/PC
update session set ScreenSize='L';

alter table arbeidsgiveravgift drop Percent62;

alter table accountline add UpdatedByPersonID int;
alter table accountline add InsertedByPersonID int;
alter table accountline add InsertedDateTime datetime;

alter table account add UpdatedByPersonID int;
alter table account add InsertedByPersonID int;
alter table account add InsertedDateTime datetime;

alter table person add key(Email);
alter table person add key(ExternalID);

insert into setup set Module='kid', Name='accountplanid', Value='0';
insert into setup set Module='kid', Name='invoiceid', Value='0';
insert into setup set Module='kid', Name='pad', Value='5';
insert into setup set Module='invoice', Name='outgoing', Value='lodo';

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='fakturabank', Template='listoutgoing', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='fakturabank', Template='listoutgoing', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='fakturabank', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='fakturabank', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='remittance', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='remittance', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

INSERT INTO roletemplateaccess set RoleID=5, Interface='lodo', Module='remittance', Template='listincoming', AccessLevel=3;
insert into roletemplate set Interface='lodo', Module='remittance', Template='listincoming', 	AccessLevel=1, AuthType='web', OnlyAllowInternUser=0, Cust=0;

alter table invoicein add RemittanceDaySequence int not null default '0';
alter table invoicein add RemittanceDayRecordSequence int not null default '0';
alter table invoicein add RemittanceApprovedDateTime datetime;
alter table invoicein add RemittanceApprovedPersonID int;
alter table invoicein add RemittanceSendtDateTime datetime;
alter table invoicein add RemittanceSendtPersonID int;
alter table invoicein add RemittanceStatus varchar(20) default 'recieved'; # approved; sendt; rejected;
alter table invoicein add RemittanceAmount varchar(20); # approved; sendt; rejected;
alter table invoicein add BatchID int; #The batch import number of the invoices

alter table invoicein add SupplierAccountPlanID bigint;
alter table invoicein add CustomerAccountPlanID bigint;

#We have to aim for the stars and start using bigint for all internal primary keys.
#All primary keys and all foreign keys.
alter table invoiceout modify InvoiceID bigint not null auto_increment;
alter table invoiceoutline modify InvoiceID bigint;
alter table invoicein modify ID bigint not null auto_increment;
alter table invoicein modify InvoiceID bigint not null;
alter table invoiceinline modify InvoiceID InvoiceID bigint;
alter table offer modify InvoiceID bigint not null auto_increment;
alter table offerline modify InvoiceID bigint not null;
alter table person modify PersonID bigint not null auto_increment;
alter table company modify CompanyID bigint not null auto_increment;
alter table ordercust modify OrderID bigint not null auto_increment;
alter table ordercustline modify OrderID bigint not null;
alter table orderpurchase modify OrderID bigint not null auto_increment;
alter table orderpurchaseline modify OrderID bigint;
alter table ordersubscription modify OrderID bigint not null auto_increment;
alter table ordersubscriptionline modify OrderID bigint not null;
alter table request modify RequestID bigint not null auto_increment;
alter table todo modify ToDoID bigint not null auto_increment;

alter table voucher modify VoucherID bigint not null not null auto_increment;
alter table voucher modify JournalID bigint not null;
alter table voucher modify DepartmentID bigint not null;
alter table voucher modify ProjectID bigint not null;
alter table voucher modify InsertedByPersonID bigint not null;
alter table voucher modify AutomaticFromVoucherID bigint not null;
alter table voucher modify VatID bigint not null;
alter table voucher modify UpdatedByPersonID bigint not null;

alter table voucher modify AccountLineID bigint not null;
alter table voucher modify AutomaticBalanceID bigint not null;
alter table voucher modify AutoFromWeeklySale bigint not null;
alter table voucher modify AutomaticVatVoucherID bigint not null;

alter table accountline modify AccountLineID bigint not null auto_increment;
alter table accountline modify AccountID bigint not null;
alter table account modify AccountID bigint not null auto_increment;

CREATE TABLE `invoicein` (
  'ID' bigint not null auto_increment,
  `InvoiceID` bigint(20) NOT NULL,
  `CompanyID` int(11) default '0',
  `ContactPersonID` int(11) default '0',
  `TotalCustPrice` decimal(16,2) NOT NULL default '0.00',
  `DName` varchar(255) default NULL,
  `DAddress` varchar(255) default NULL,
  `DZipCode` varchar(4) default NULL,
  `DCity` varchar(255) default NULL,
  `DCountry` varchar(255) default NULL,
  `Status` varchar(15) default NULL,
  `InvoiceDate` date NOT NULL default '0000-00-00',
  `DueDate` date NOT NULL default '0000-00-00',
  `TS` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `ProjectID` int(11) NOT NULL default '0',
  `ProjectNameInternal` varchar(255) default NULL,
  `PaymentDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `Active` tinyint(4) NOT NULL default '0',
  `CreatedByPersonID` int(11) NOT NULL default '0',
  `CreatedDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  `ProjectStartDate` date NOT NULL default '0000-00-00',
  `ProjectStopDate` tinyint(4) NOT NULL default '0',
  `CommentCustomer` text,
  `CommentInternal` text,
  `DeliveryCondition` varchar(255) NOT NULL default '',
  `PaymentCondition` varchar(255) NOT NULL default '',
  `IAddress` varchar(255) default NULL,
  `IZipCode` varchar(4) default NULL,
  `ICity` varchar(255) default NULL,
  `ICountry` varchar(255) default NULL,
  `DeliveryDate` date NOT NULL default '0000-00-00',
  `OrderDate` date NOT NULL default '0000-00-00',
  `VATDuty` tinyint(4) NOT NULL default '0',
  `TotalVat` decimal(16,2) NOT NULL default '0.00',
  `RefCustomer` varchar(255) default '',
  `IName` varchar(255) default NULL,
  `InvoiceType` set('order','project') default NULL,
  `FromCompanyID` int(11) NOT NULL default '0',
  `ProjectNameCustomer` varchar(255) NOT NULL default '',
  `IPoBox` varchar(255) NOT NULL default '',
  `IPoBoxCity` varchar(255) NOT NULL default '',
  `InvoiceFileID` int(11) default NULL,
  `RefInternal` varchar(255) default '',
  `IPoBoxZipCode` varchar(255) default NULL,
  `IPoBoxZipCodeCity` varchar(255) default NULL,
  `DPoBoxZipCode` varchar(255) default NULL,
  `DPoBoxZipCodeCity` varchar(255) default NULL,
  `SalePersonID` int(11) default '0',
  `DPoBox` varchar(255) default NULL,
  `DPoBoxCity` varchar(255) default NULL,
  `AddressID` int(11) NOT NULL default '0',
  `IAddressID` int(11) default NULL,
  `DAddressID` int(11) default NULL,
  `TotalCostPrice` decimal(16,2) NOT NULL default '0.00',
  `UpdatedByPersonID` int(11) NOT NULL default '0',
  `AccountPlanID` bigint(20) default NULL,
  `DEmail` varchar(50) default '',
  `IEmail` varchar(50) default '',
  `BankAccount` varchar(15) default '',
  `Phone` varchar(20) default '',
  `ResponsiblePersonID` int(11) default '0',
  `RequiredDate` datetime default NULL,
  `Freight` decimal(16,2) default '0.00',
  `ValidFrom` datetime default NULL,
  `ValidTo` datetime default NULL,
  `EnableTaxFree` smallint(6) default NULL,
  `DateShipped` date default NULL,
  `PersonID` int(11) NOT NULL default '0',
  `Discount` decimal(16,2) default NULL,
  `VAddressNumber` varchar(10) default NULL,
  `IAddressNumber` varchar(10) default NULL,
  `MailSendtDateTime` datetime default NULL,
  `CustomerViewedDateTime` datetime default NULL,
  `CustomerViewedPersonID` int(11) default NULL,
  `MailExpireDateTime` datetime default NULL,
  `DAddressNumber` varchar(10) default NULL,
  `IBAN` varchar(255) default NULL,
  `Period` varchar(7) default NULL,
  `SessionID` varchar(40) default NULL,
  `CommentCustomerPosition` char(10) NOT NULL default 'bottom',
  `Locked` smallint(6) default NULL,
  `FakturabankPersonID` int(11) default NULL,
  `FakturabankDateTime` datetime default NULL,
  `CurrencyID` char(4) default 'NOK',
  `KID` varchar(30) default NULL,
  `RemittanceDaySequence` int(11) NOT NULL default '0',
  `RemittanceDayRecordSequence` int(11) NOT NULL default '0',
  `RemittanceApprovedDate` datetime default NULL,
  `RemittanceApprovedPersonID` int(11) default NULL,
  `RemittanceSendtDate` datetime default NULL,
  `RemittanceSendtPersonID` int(11) default NULL,
  `RemittanceStatus` varchar(20) default 'recieved',
  `RemittanceAmount` varchar(20) default NULL,
  `RemittanceApprovedDateTime` datetime default NULL,
  `RemittanceSendtDateTime` datetime default NULL,
  `BatchID` int(11) default NULL,
  `SupplierAccountPlanID` bigint(20) default NULL,
  `CustomerAccountPlanID` bigint(20) default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE(SupplierAccountPlanID, InvoiceID)
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;
