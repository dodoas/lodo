CREATE TABLE IF NOT EXISTS accountplantemp (
  AccountPlanID bigint(20) NOT NULL DEFAULT '0',
  AccountName varchar(255) DEFAULT NULL,
  InsertedDateTime datetime DEFAULT NULL,
  InsertedByPersonID int(11) DEFAULT NULL,
  debittext varchar(20) DEFAULT NULL,
  credittext varchar(20) DEFAULT NULL,
  Address varchar(255) DEFAULT NULL,
  ZipCode varchar(10) DEFAULT NULL,
  Email varchar(255) DEFAULT NULL,
  DomesticBankAccount varchar(255) DEFAULT NULL,
  EnableCredit smallint(6) DEFAULT NULL,
  CreditDays int(11) DEFAULT NULL,
  DebitColor varchar(10) DEFAULT NULL,
  CreditColor varchar(10) DEFAULT NULL,
  Active smallint(6) DEFAULT '1',
  EnableMotkontoBalanse smallint(6) DEFAULT NULL,
  MotkontoBalanse1 bigint(20) DEFAULT NULL,
  MotkontoResultat1 bigint(20) DEFAULT NULL,
  MotkontoResultat2 bigint(20) DEFAULT NULL,
  EnableMotkontoResultat smallint(6) DEFAULT NULL,
  City varchar(50) DEFAULT NULL,
  OrgNumber varchar(20) DEFAULT NULL,
  Phone varchar(20) DEFAULT NULL,
  Mobile varchar(255) DEFAULT NULL,
  EnableInvoiceAddress smallint(6) DEFAULT NULL,
  AccountPlanType varchar(9) DEFAULT NULL,
  UpdatedByPersonID int(11) DEFAULT NULL,
  CountryCode varchar(2) NOT NULL DEFAULT 'NO',
  ParentName varchar(255) NULL,
  ParentOrgNumber varchar(255) NULL,
  FBSchemeType varchar(255) NULL,
  FBSchemeValue varchar(255) NULL,
  FBSchemeLodoID int(11) DEFAULT NULL,
  PRIMARY KEY (AccountPlanID),
  KEY Active (Active)
);
