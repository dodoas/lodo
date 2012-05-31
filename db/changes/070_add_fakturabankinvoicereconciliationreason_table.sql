CREATE TABLE IF NOT EXISTS `fakturabankinvoicereconciliationreason` (
  `FakturabankInvoiceReconciliationReasonID` bigint(20) NOT NULL,
  `FakturabankInvoiceReconciliationReasonCode` varchar(10) default NULL,
  `FakturabankInvoiceReconciliationReasonName` varchar(255) default NULL,
  `AccountPlanID` bigint(20) NOT NULL,
  `LedgerType` varchar(255) NOT NULL default 'main',
  PRIMARY KEY  (`FakturabankInvoiceReconciliationReasonID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
